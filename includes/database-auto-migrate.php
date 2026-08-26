<?php
/**
 * Idempotent database auto-migrator.
 * Runs pending PHP migrations from database/auto-migrations/ when an admin loads any admin page.
 *
 * Add a new file under database/auto-migrations/ named like:
 *   2026_08_26_120000_short_name.php
 * Returning:
 *   ['id' => '...', 'description' => '...', 'up' => function(Database $db) { ... }]
 */

class DatabaseAutoMigrate {
    private static $ranThisRequest = false;
    private $db;
    private $dir;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'auto-migrations';
    }

    /**
     * Run once per request. Returns summary array.
     */
    public function run($appliedBy = null) {
        if (self::$ranThisRequest) {
            return $_SESSION['auto_migration_last_result'] ?? [
                'ran' => false,
                'applied' => [],
                'failed' => [],
                'skipped' => 0,
                'errors' => [],
            ];
        }
        self::$ranThisRequest = true;

        $result = [
            'ran' => true,
            'applied' => [],
            'failed' => [],
            'skipped' => 0,
            'errors' => [],
        ];

        try {
            $this->ensureTrackingTable();
            $migrations = $this->discoverMigrations();
            $appliedIds = $this->getSuccessfullyAppliedIds();

            foreach ($migrations as $migration) {
                $id = (string)$migration['id'];
                if (isset($appliedIds[$id])) {
                    $result['skipped']++;
                    continue;
                }

                try {
                    $up = $migration['up'];
                    if (!is_callable($up)) {
                        throw new Exception('Migration up() is not callable');
                    }
                    $up($this->db);
                    $this->recordSuccess($id, $migration['description'] ?? $id, $appliedBy);
                    $result['applied'][] = [
                        'id' => $id,
                        'description' => $migration['description'] ?? $id,
                    ];
                } catch (Throwable $e) {
                    $msg = $e->getMessage();
                    $this->recordFailure($id, $migration['description'] ?? $id, $msg, $appliedBy);
                    $result['failed'][] = [
                        'id' => $id,
                        'description' => $migration['description'] ?? $id,
                        'error' => $msg,
                    ];
                    $result['errors'][] = "{$id}: {$msg}";
                    error_log("Auto-migration failed [{$id}]: {$msg}");
                }
            }
        } catch (Throwable $e) {
            $result['errors'][] = 'Migrator bootstrap failed: ' . $e->getMessage();
            error_log('DatabaseAutoMigrate bootstrap error: ' . $e->getMessage());
        }

        $_SESSION['auto_migration_last_result'] = $result;

        if (!empty($result['failed']) || !empty($result['errors'])) {
            $_SESSION['auto_migration_errors'] = $result['errors'];
        } else {
            unset($_SESSION['auto_migration_errors']);
        }

        if (!empty($result['applied'])) {
            $_SESSION['auto_migration_success'] = array_map(static function ($row) {
                return ($row['description'] ?? $row['id']) . ' (' . $row['id'] . ')';
            }, $result['applied']);
        }

        return $result;
    }

    private function ensureTrackingTable() {
        $result = $this->db->query("CREATE TABLE IF NOT EXISTS `auto_migrations` (
            `id` varchar(191) NOT NULL,
            `description` varchar(255) DEFAULT NULL,
            `status` enum('success','failed') NOT NULL DEFAULT 'success',
            `error_message` text DEFAULT NULL,
            `applied_by` int(11) DEFAULT NULL,
            `applied_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_status` (`status`),
            KEY `idx_applied_at` (`applied_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        if ($result === false) {
            throw new Exception('Failed creating auto_migrations tracking table');
        }
    }

    private function getSuccessfullyAppliedIds() {
        $ids = [];
        $stmt = $this->db->query("SELECT id FROM auto_migrations WHERE status = 'success'");
        if ($stmt === false) {
            throw new Exception('Failed reading auto_migrations');
        }
        foreach ($stmt->fetchAll() ?: [] as $row) {
            $ids[$row['id']] = true;
        }
        return $ids;
    }

    private function discoverMigrations() {
        if (!is_dir($this->dir)) {
            @mkdir($this->dir, 0755, true);
        }

        $files = glob($this->dir . DIRECTORY_SEPARATOR . '*.php') ?: [];
        sort($files, SORT_STRING);

        $migrations = [];
        foreach ($files as $file) {
            if (basename($file) === 'index.php') {
                continue;
            }
            $data = include $file;
            if (!is_array($data) || empty($data['id']) || !isset($data['up'])) {
                throw new Exception('Invalid migration file: ' . basename($file));
            }
            $migrations[] = $data;
        }
        return $migrations;
    }

    private function recordSuccess($id, $description, $appliedBy) {
        $result = $this->db->query(
            "INSERT INTO auto_migrations (id, description, status, error_message, applied_by, applied_at)
             VALUES (?, ?, 'success', NULL, ?, NOW())
             ON DUPLICATE KEY UPDATE
                description = VALUES(description),
                status = 'success',
                error_message = NULL,
                applied_by = VALUES(applied_by),
                applied_at = NOW()",
            [$id, $description, $appliedBy]
        );
        if ($result === false) {
            throw new Exception("Failed recording success for migration {$id}");
        }
    }

    private function recordFailure($id, $description, $error, $appliedBy) {
        $this->db->query(
            "INSERT INTO auto_migrations (id, description, status, error_message, applied_by, applied_at)
             VALUES (?, ?, 'failed', ?, ?, NOW())
             ON DUPLICATE KEY UPDATE
                description = VALUES(description),
                status = 'failed',
                error_message = VALUES(error_message),
                applied_by = VALUES(applied_by),
                updated_at = NOW()",
            [$id, $description, $error, $appliedBy]
        );
    }

    /** Add a column if missing. Returns true if added. */
    public static function ensureColumn($db, $table, $column, $definitionSql) {
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
        $column = preg_replace('/[^a-zA-Z0-9_]/', '', $column);

        // Use information_schema — SHOW COLUMNS ... LIKE ? often fails with PDO prepared statements
        $stmt = $db->query(
            "SELECT COUNT(*) AS cnt
             FROM information_schema.columns
             WHERE table_schema = DATABASE()
               AND table_name = ?
               AND column_name = ?",
            [$table, $column]
        );
        if ($stmt === false) {
            $pdoErr = method_exists($db, 'errorInfo') ? ($db->errorInfo()[2] ?? '') : '';
            throw new Exception("Failed checking column {$table}.{$column}" . ($pdoErr ? ": {$pdoErr}" : ''));
        }
        $row = $stmt->fetch();
        if (!empty($row['cnt'])) {
            return false;
        }
        $result = $db->query("ALTER TABLE `{$table}` ADD COLUMN {$definitionSql}");
        if ($result === false) {
            $pdoErr = method_exists($db, 'errorInfo') ? ($db->errorInfo()[2] ?? '') : '';
            throw new Exception("Failed adding column {$table}.{$column}" . ($pdoErr ? ": {$pdoErr}" : ''));
        }
        return true;
    }

    /** Ensure a system_settings row exists. Returns true if inserted. */
    public static function ensureSetting($db, $key, $value, $type, $description) {
        $stmt = $db->query("SELECT id FROM system_settings WHERE setting_key = ? LIMIT 1", [$key]);
        if ($stmt === false) {
            throw new Exception("Failed checking system_settings for {$key}");
        }
        if ($stmt->fetch()) {
            return false;
        }
        $result = $db->query(
            "INSERT INTO system_settings (setting_key, setting_value, setting_type, description, created_at, updated_at)
             VALUES (?, ?, ?, ?, NOW(), NOW())",
            [$key, $value, $type, $description]
        );
        if ($result === false) {
            throw new Exception("Failed inserting system_settings.{$key}");
        }
        return true;
    }

    /** Run a query and throw if it fails (Database::query returns false on error). */
    public static function execOrFail($db, $sql, $params = [], $label = null) {
        $result = $db->query($sql, $params);
        if ($result === false) {
            throw new Exception($label ?: ('Query failed: ' . substr($sql, 0, 120)));
        }
        return $result;
    }
}

/**
 * Run auto-migrations for the current admin session.
 */
function runAdminDatabaseAutoMigrations($adminUserId = null) {
    if (!class_exists('Database')) {
        return null;
    }
    $adminUserId = $adminUserId ?? ($_SESSION['user_id'] ?? null);
    $migrator = new DatabaseAutoMigrate();
    return $migrator->run($adminUserId ? (int)$adminUserId : null);
}
