<?php
/**
 * Apply Update Package API
 * Handles uploading and applying update packages with database migrations
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/version-control-config.php';

header('Content-Type: application/json');

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

/**
 * Validate migration file is safe (additive only)
 * CRITICAL: Only allows adding new tables/columns - NEVER overwrites or deletes existing data
 */
function validateMigration($migrationFile) {
    $sql = file_get_contents($migrationFile);
    
    // Block ALL dangerous operations that could delete or modify existing data
    $dangerousPatterns = [
        '/DROP\s+(TABLE|DATABASE|COLUMN|INDEX|KEY|CONSTRAINT)/i', // Blocks: DROP TABLE, DROP COLUMN, etc.
        '/TRUNCATE\s+TABLE/i', // Blocks: TRUNCATE TABLE (deletes all data)
        '/DELETE\s+FROM/i', // Blocks: DELETE FROM (deletes data)
        '/UPDATE\s+.*\s+SET/i', // Blocks: UPDATE (modifies existing data)
        '/ALTER\s+TABLE.*DROP/i', // Blocks: ALTER TABLE ... DROP COLUMN
        '/ALTER\s+TABLE.*MODIFY/i', // Blocks: ALTER TABLE ... MODIFY (changes existing columns)
        '/ALTER\s+TABLE.*CHANGE/i', // Blocks: ALTER TABLE ... CHANGE (renames/modifies columns)
        '/RENAME\s+TABLE/i', // Blocks: RENAME TABLE
        '/REPLACE\s+INTO/i', // Blocks: REPLACE INTO (could overwrite data)
    ];
    
    foreach ($dangerousPatterns as $pattern) {
        if (preg_match($pattern, $sql)) {
            error_log("Version Control: ❌ BLOCKED dangerous operation in migration: " . $pattern);
            return false;
        }
    }
    
    // Only allow SAFE additive operations (that won't affect existing data)
    $allowedPatterns = [
        '/CREATE\s+(TABLE|INDEX)\s+IF\s+NOT\s+EXISTS/i', // Only CREATE IF NOT EXISTS (won't overwrite)
        '/ALTER\s+TABLE.*ADD\s+(COLUMN|INDEX|KEY|CONSTRAINT)/i', // Only ADD (won't modify existing)
        '/CREATE\s+INDEX\s+IF\s+NOT\s+EXISTS/i', // Only CREATE INDEX IF NOT EXISTS
        '/INSERT\s+IGNORE\s+INTO/i', // INSERT IGNORE is safe (won't overwrite if exists)
        '/PREPARE.*ALTER\s+TABLE.*ADD/i', // Dynamic SQL that checks before adding (safe)
    ];
    
    // Migration should contain at least one safe operation
    $hasSafeOperation = false;
    foreach ($allowedPatterns as $pattern) {
        if (preg_match($pattern, $sql)) {
            $hasSafeOperation = true;
            error_log("Version Control: ✅ Found safe operation: " . $pattern);
            break;
        }
    }
    
    if (!$hasSafeOperation) {
        error_log("Version Control: ⚠️ No safe additive operations found in migration file");
    }
    
    return $hasSafeOperation;
}

/**
 * Execute migration SQL safely
 */
function executeMigration($migrationFile, $db) {
    $sql = file_get_contents($migrationFile);
    
    // Split by semicolon (handle multiple statements)
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) {
            return !empty($stmt) && !preg_match('/^--/', $stmt);
        }
    );
    
    // Execute each statement in a transaction
    $db->beginTransaction();
    
    try {
        $statementCount = 0;
        foreach ($statements as $statement) {
            // Skip empty or comment-only statements
            $trimmedStatement = trim($statement);
            if (empty($trimmedStatement)) continue;
            
            $statementCount++;
            
            // Log first few characters of statement for debugging
            $statementPreview = substr($trimmedStatement, 0, 50);
            error_log("Version Control: Executing migration statement {$statementCount}: {$statementPreview}...");
            
            // Execute statement
            try {
                $db->query($trimmedStatement);
            } catch (Exception $stmtError) {
                // Enhanced error logging
                error_log("Version Control: ❌ Migration statement failed at statement #{$statementCount}");
                error_log("Version Control: Failed statement preview: {$statementPreview}...");
                error_log("Version Control: Error: " . $stmtError->getMessage());
                throw new Exception("Migration failed at statement #{$statementCount}: " . $stmtError->getMessage());
            }
        }
        
        if ($statementCount === 0) {
            error_log("Version Control: ⚠️ No executable statements found in migration file");
        } else {
            error_log("Version Control: ✅ Successfully executed {$statementCount} statement(s)");
        }
        
        $db->commit();
        return true;
        
    } catch (Exception $e) {
        $db->rollBack();
        error_log("Version Control: ❌ Migration transaction rolled back due to error: " . $e->getMessage());
        throw $e;
    }
}

// Check if POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

/**
 * Initialize version control tables if they don't exist
 */
function initializeVersionControlTables($db) {
    try {
        // Check if tables exist
        $checkTables = $db->query("SHOW TABLES LIKE 'system_version_info'");
        $tablesExist = $checkTables && $checkTables->rowCount() > 0;
        
        if ($tablesExist) {
            return; // Tables already exist
        }
        
        // Create version control tables
        $db->beginTransaction();
        
        // Create system_versions table
        $db->query("CREATE TABLE IF NOT EXISTS `system_versions` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `version` varchar(20) NOT NULL,
            `release_date` datetime NOT NULL,
            `notes` text DEFAULT NULL,
            `created_by` int(11) DEFAULT NULL,
            `package_size` bigint(20) DEFAULT 0,
            `file_count` int(11) DEFAULT 0,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`),
            UNIQUE KEY `version` (`version`),
            KEY `idx_version` (`version`),
            KEY `idx_release_date` (`release_date`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // Create schema_migrations table
        $db->query("CREATE TABLE IF NOT EXISTS `schema_migrations` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `version` varchar(20) NOT NULL,
            `migration_name` varchar(255) NOT NULL,
            `migration_file` varchar(255) NOT NULL,
            `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `applied_by` int(11) DEFAULT NULL,
            `status` enum('success','failed','skipped') NOT NULL DEFAULT 'success',
            `error_message` text DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `unique_migration` (`version`,`migration_name`),
            KEY `idx_version` (`version`),
            KEY `idx_applied_at` (`applied_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // Create update_logs table
        $db->query("CREATE TABLE IF NOT EXISTS `update_logs` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `version` varchar(20) NOT NULL,
            `applied_date` datetime NOT NULL,
            `applied_by` int(11) DEFAULT NULL,
            `status` enum('success','failed','partial') NOT NULL DEFAULT 'success',
            `log_details` text DEFAULT NULL,
            `files_updated` int(11) DEFAULT 0,
            `migrations_applied` int(11) DEFAULT 0,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`),
            KEY `idx_version` (`version`),
            KEY `idx_applied_date` (`applied_date`),
            KEY `idx_status` (`status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // Create system_version_info table
        $db->query("CREATE TABLE IF NOT EXISTS `system_version_info` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `current_version` varchar(20) NOT NULL,
            `database_version` varchar(20) NOT NULL,
            `last_updated` datetime NOT NULL,
            `updated_by` int(11) DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `unique_info` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // Insert initial version info if table was just created
        $checkData = $db->query("SELECT COUNT(*) as count FROM system_version_info");
        $result = $checkData->fetch();
        $dataExists = $result && $result['count'] > 0;
        
        if (!$dataExists) {
            $db->query("INSERT INTO `system_version_info` (`current_version`, `database_version`, `last_updated`, `updated_by`) 
                       VALUES ('1.0.0', '1.0.0', NOW(), NULL)");
        }
        
        $db->commit();
        
    } catch (Exception $e) {
        $db->rollBack();
        error_log("Failed to initialize version control tables: " . $e->getMessage());
        throw new Exception("Failed to initialize version control tables: " . $e->getMessage());
    }
}

try {
    $db = Database::getInstance();
    $userId = $_SESSION['user_id'];
    
    // Initialize version control tables if they don't exist
    initializeVersionControlTables($db);
    
    // Check PHP upload settings
    $uploadMaxSize = ini_get('upload_max_filesize');
    $postMaxSize = ini_get('post_max_size');
    
    // Check if file was uploaded
    if (!isset($_FILES['update_package'])) {
        $errorMsg = 'No file uploaded. ';
        
        // Check if POST data was received
        if (empty($_POST) && empty($_FILES)) {
            $errorMsg .= 'No POST data received. ';
            $errorMsg .= 'Check: upload_max_filesize=' . $uploadMaxSize . ', post_max_size=' . $postMaxSize . '. ';
        } else if (!empty($_POST) && empty($_FILES)) {
            $errorMsg .= 'POST data received but no file. ';
            $errorMsg .= 'File may be too large. Max upload: ' . $uploadMaxSize . ', Max POST: ' . $postMaxSize . '. ';
        }
        
        if (isset($_SERVER['CONTENT_TYPE'])) {
            $errorMsg .= 'Content-Type: ' . $_SERVER['CONTENT_TYPE'] . '. ';
        }
        
        if (isset($_SERVER['CONTENT_LENGTH'])) {
            $errorMsg .= 'Content-Length: ' . $_SERVER['CONTENT_LENGTH'] . ' bytes. ';
        }
        
        echo json_encode(['success' => false, 'message' => $errorMsg]);
        exit;
    }
    
    // Check for upload errors
    $uploadError = $_FILES['update_package']['error'];
    if ($uploadError !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize directive in php.ini',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive in HTML form',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by PHP extension'
        ];
        $errorMsg = isset($errorMessages[$uploadError]) ? $errorMessages[$uploadError] : 'Unknown upload error (' . $uploadError . ')';
        echo json_encode(['success' => false, 'message' => 'Upload error: ' . $errorMsg]);
        exit;
    }
    
    $uploadedFile = $_FILES['update_package'];
    
    // Validate file type
    $fileInfo = pathinfo($uploadedFile['name']);
    if (strtolower($fileInfo['extension']) !== 'zip') {
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Only ZIP files are allowed']);
        exit;
    }
    
    // Create temp directory for extraction
    $tempDir = sys_get_temp_dir() . '/update_extract_' . uniqid();
    if (!mkdir($tempDir, 0755, true)) {
        throw new Exception('Failed to create temp directory');
    }
    
    // Extract ZIP file
    $zip = new ZipArchive();
    if ($zip->open($uploadedFile['tmp_name']) !== TRUE) {
        throw new Exception('Failed to open ZIP file');
    }
    
    $zip->extractTo($tempDir);
    $zip->close();
    
    // Read manifest
    $manifestPath = $tempDir . '/migrations/manifest.json';
    if (!file_exists($manifestPath)) {
        throw new Exception('Manifest file not found in package');
    }
    
    $manifest = json_decode(file_get_contents($manifestPath), true);
    if (!$manifest || !isset($manifest['version'])) {
        throw new Exception('Invalid manifest file');
    }
    
    $newVersion = $manifest['version'];
    
    // Get current version (tables should exist now after initialization)
    $sql = "SELECT * FROM system_version_info LIMIT 1";
    $stmt = $db->query($sql);
    $currentVersionInfo = $stmt ? $stmt->fetch() : null;
    $currentVersion = $currentVersionInfo['current_version'] ?? '1.0.0';
    
    // Prevent version downgrade (safety check)
    if (version_compare($newVersion, $currentVersion, '<')) {
        throw new Exception("Cannot downgrade from version {$currentVersion} to {$newVersion}. Downgrades are not supported for safety reasons. Please restore from backup if needed.");
    }
    
    // Warn if same version (but allow it - useful for re-applying)
    if (version_compare($newVersion, $currentVersion, '==')) {
        error_log("Version Control: ⚠️ WARNING - Applying same version ({$newVersion}). This will re-apply migrations and update files.");
    }
    
    // Create backup directory
    $backupDir = BASE_PATH . '/backups/backup_' . date('Y-m-d_His');
    if (!is_dir(dirname($backupDir))) {
        if (!mkdir(dirname($backupDir), 0755, true)) {
            throw new Exception('Failed to create backups directory. Check file permissions.');
        }
    }
    if (!mkdir($backupDir, 0755, true)) {
        throw new Exception('Failed to create backup directory. Check file permissions.');
    }
    
    // Log backup location for reference
    error_log("Version Control: Creating backup at {$backupDir}");
    
    $updateLog = [
        'files_updated' => 0,
        'migrations_applied' => 0,
        'errors' => []
    ];
    
    // Function to backup file
    function backupFile($filePath, $backupDir, $basePath) {
        if (!file_exists($filePath)) return;
        
        $relativePath = str_replace($basePath . DIRECTORY_SEPARATOR, '', $filePath);
        $relativePath = str_replace('\\', '/', $relativePath);
        $backupPath = $backupDir . '/' . $relativePath;
        $backupDirPath = dirname($backupPath);
        
        if (!is_dir($backupDirPath)) {
            mkdir($backupDirPath, 0755, true);
        }
        
        copy($filePath, $backupPath);
    }
    
    /**
     * Smart merge config.php - SIMPLE LOGIC:
     * 1. Extract DB, SMTP, IMAP values from old config
     * 2. Use new config as base (has updated structure + ALL new features/constants)
     * 3. Replace old values into new structure
     * 4. Result: Old site gets new structure + new features + preserved credentials
     * 
     * IMPORTANT: Since we use new config as base, ALL new features/constants are automatically
     * added to old configs (IMAP, new API keys, new settings, etc.)
     */
    function smartMergeConfigFile($newConfigPath, $existingConfigPath) {
        if (!file_exists($newConfigPath)) {
            error_log("Version Control ERROR: New config.php not found at: " . $newConfigPath);
            return false;
        }
        
        if (!file_exists($existingConfigPath)) {
            error_log("Version Control ERROR: Existing config.php not found at: " . $existingConfigPath);
            return false;
        }
        
        if (!is_readable($existingConfigPath)) {
            error_log("Version Control ERROR: Existing config.php is not readable: " . $existingConfigPath);
            return false;
        }
        
        $newConfig = file_get_contents($newConfigPath);
        $existingConfig = file_get_contents($existingConfigPath);
        
        if (empty($existingConfig) || strlen(trim($existingConfig)) < 100) {
            error_log("Version Control ERROR: Existing config.php is empty or too short (" . strlen($existingConfig) . " bytes). Skipping merge.");
            return false;
        }
        
        error_log("Version Control: Starting smart merge - preserving DB/SMTP/IMAP from old config");
        error_log("Version Control: Existing config size: " . strlen($existingConfig) . " bytes");
        error_log("Version Control: New config size: " . strlen($newConfig) . " bytes");
        
        // Helper: Extract string value from any define format - VERY FLEXIBLE
        function extractStringValue($config, $key) {
            // Try multiple patterns in order of specificity
            
            // Pattern 1: define('KEY', $_ENV['KEY'] ?? 'value') - with $_ENV fallback
            // This is the most common format in new configs
            // Example: define('DB_NAME', $_ENV['DB_NAME'] ?? 'u502532383_blutech');
            $pattern1 = "/define\s*\(\s*['\"]{$key}['\"]\s*,\s*(?:\$_ENV\[['\"]{$key}['\"]\]\s*\?\?\s*)?['\"]([^'\"]+)['\"]/";
            if (preg_match($pattern1, $config, $m)) {
                $extracted = $m[1];
                error_log("Version Control: ✅ Extracted {$key} using pattern 1 (with \$_ENV fallback): " . substr($extracted, 0, 30) . "...");
                return $extracted;
            }
            
            // Pattern 1b: Same as pattern 1 but with double quotes
            $pattern1b = '/define\s*\(\s*["\']' . $key . '["\']\s*,\s*(?:\$_ENV\[["\']' . $key . '["\']\]\s*\?\?\s*)?["\']([^"\']+)["\']/';
            if (preg_match($pattern1b, $config, $m)) {
                $extracted = $m[1];
                error_log("Version Control: ✅ Extracted {$key} using pattern 1b (with \$_ENV fallback, double quotes): " . substr($extracted, 0, 30) . "...");
                return $extracted;
            }
            
            // Pattern 2: define('KEY', 'value') - simple define with single quotes (OLD FORMAT)
            // This handles the old config format where SITE_URL might be hardcoded
            if (preg_match("/define\s*\(\s*['\"]{$key}['\"]\s*,\s*['\"]([^'\"]+)['\"]/", $config, $m)) {
                $value = trim($m[1]);
                if (!empty($value) && strlen($value) > 0) {
                    error_log("Version Control: ✅ Extracted {$key} using pattern 2 (simple define): " . substr($value, 0, 30) . "...");
                    return $value;
                }
            }
            
            // Pattern 3: define("KEY", "value") - double quotes
            if (preg_match("/define\s*\(\s*[\"']{$key}[\"']\s*,\s*[\"']([^\"']+)[\"']/", $config, $m)) {
                $value = trim($m[1]);
                if (!empty($value) && strlen($value) > 0) {
                    error_log("Version Control: ✅ Extracted {$key} using pattern 3 (double quotes): " . substr($value, 0, 30) . "...");
                    return $value;
                }
            }
            
            // Pattern 4: More flexible - any quotes, any spacing
            if (preg_match("/define\s*\(\s*[\"']?{$key}[\"']?\s*,\s*[\"']([^\"';]+)[\"']/i", $config, $m)) {
                $value = trim($m[1]);
                if (!empty($value)) {
                    error_log("Version Control: ✅ Extracted {$key} using pattern 4 (flexible): " . substr($value, 0, 30) . "...");
                    return $value;
                }
            }
            
            // Pattern 5: Handle cases with extra whitespace or comments
            if (preg_match("/define\s*\(\s*[\"']{$key}[\"']\s*,\s*[\"']([^\"']*?)[\"']/s", $config, $m)) {
                $value = trim($m[1]);
                if (!empty($value)) {
                    error_log("Version Control: ✅ Extracted {$key} using pattern 5 (with whitespace): " . substr($value, 0, 30) . "...");
                    return $value;
                }
            }
            
            // Pattern 6: Very permissive - find anything that looks like the key followed by a value
            if (preg_match("/{$key}\s*[=,]\s*[\"']([^\"';]+)[\"']/i", $config, $m)) {
                $value = trim($m[1]);
                if (!empty($value) && strlen($value) > 0) {
                    error_log("Version Control: ✅ Extracted {$key} using pattern 6 (very permissive): " . substr($value, 0, 30) . "...");
                    return $value;
                }
            }
            
            // Pattern 7: Extract from any line containing the key (last resort)
            $lines = explode("\n", $config);
            foreach ($lines as $line) {
                if (stripos($line, $key) !== false && (stripos($line, 'define') !== false || stripos($line, '=') !== false)) {
                    // Try to extract value from this line
                    if (preg_match("/[\"']([^\"']{3,})[\"']/", $line, $lineMatch)) {
                        $value = trim($lineMatch[1]);
                        // Basic validation - should not be empty and should look like a real value
                        if (!empty($value) && strlen($value) > 2 && !in_array(strtolower($value), ['null', 'false', 'true'])) {
                            error_log("Version Control: ✅ Extracted {$key} using pattern 7 (line-by-line): " . substr($value, 0, 30) . "...");
                            return $value;
                        }
                    }
                }
            }
            
            // Debug: Show what we found around this key
            if (preg_match("/(.{0,50}define[^;]{0,100}{$key}[^;]{0,100})/is", $config, $debugMatch)) {
                $debugLine = trim($debugMatch[1]);
                error_log("Version Control: ❌ DEBUG - Found define for {$key} but couldn't extract value:");
                error_log("Version Control: ❌ DEBUG - Line content: " . substr($debugLine, 0, 200));
                // Try to find the actual value manually - be very permissive
                if (preg_match("/[\"']([^\"']{3,})[\"']/", $debugLine, $manualMatch)) {
                    $manualValue = trim($manualMatch[1]);
                    if (!empty($manualValue) && strlen($manualValue) > 2) {
                        error_log("Version Control: ⚠️ DEBUG - Manual extraction attempt: " . substr($manualValue, 0, 30) . "...");
                        return $manualValue;
                    }
                }
            } else {
                error_log("Version Control: ❌ DEBUG - No define found for {$key} in old config");
            }
            return null;
        }
        
        // Helper: Extract numeric value - FLEXIBLE
        function extractNumericValue($config, $key) {
            // Try: define('KEY', $_ENV['KEY'] ?? 123)
            if (preg_match("/define\s*\(\s*['\"]{$key}['\"]\s*,\s*(?:\$_ENV\[['\"]{$key}['\"]\]\s*\?\?\s*)?(\d+)/", $config, $m)) {
                error_log("Version Control: ✅ Extracted {$key} (numeric) using pattern 1: " . $m[1]);
                return $m[1];
            }
            // Try: define('KEY', 123)
            if (preg_match("/define\s*\(\s*['\"]{$key}['\"]\s*,\s*(\d+)/", $config, $m)) {
                error_log("Version Control: ✅ Extracted {$key} (numeric) using pattern 2: " . $m[1]);
                return $m[1];
            }
            // Try: More flexible pattern
            if (preg_match("/define\s*\(\s*[\"']?{$key}[\"']?\s*,\s*(\d+)/i", $config, $m)) {
                error_log("Version Control: ✅ Extracted {$key} (numeric) using pattern 3: " . $m[1]);
                return $m[1];
            }
            error_log("Version Control: ❌ DEBUG - Could not extract numeric value for {$key}");
            return null;
        }
        
        // STEP 1: Extract values from OLD config
        $oldValues = [];
        
        // Test extraction with a simple example first - verify pattern works
        error_log("Version Control: Testing extraction pattern on actual config...");
        $testPattern = "/define\s*\(\s*['\"]DB_NAME['\"]\s*,\s*(?:\$_ENV\[['\"]DB_NAME['\"]\]\s*\?\?\s*)?['\"]([^'\"]+)['\"]/";
        if (preg_match($testPattern, $existingConfig, $testMatch)) {
            error_log("Version Control: ✅ Test extraction successful! Found DB_NAME value: " . substr($testMatch[1], 0, 50));
        } else {
            error_log("Version Control: ❌ Test extraction FAILED! Pattern didn't match.");
            // Show what we're actually looking for
            if (preg_match("/(define\s*\(\s*['\"]DB_NAME['\"]\s*,[^;]+)/", $existingConfig, $debugMatch)) {
                $foundLine = trim($debugMatch[1]);
                error_log("Version Control: DEBUG - Actual line found: " . $foundLine);
                // Try a simpler pattern
                if (preg_match("/['\"]([^'\"]+)['\"]\s*\);?\s*$/", $foundLine, $simpleMatch)) {
                    error_log("Version Control: DEBUG - Simple extraction from line: " . substr($simpleMatch[1], 0, 50));
                }
            } else {
                error_log("Version Control: DEBUG - Could not find any DB_NAME define in config");
                // Check if file contains DB_NAME at all
                if (strpos($existingConfig, 'DB_NAME') !== false) {
                    error_log("Version Control: DEBUG - DB_NAME exists in file but pattern didn't match");
                    // Find the line with DB_NAME
                    $lines = explode("\n", $existingConfig);
                    foreach ($lines as $lineNum => $line) {
                        if (strpos($line, 'DB_NAME') !== false) {
                            error_log("Version Control: DEBUG - Line " . ($lineNum + 1) . ": " . trim($line));
                        }
                    }
                }
            }
        }
        
        // Database - MUST EXIST
        error_log("Version Control: Extracting DB_HOST...");
        $oldValues['DB_HOST'] = extractStringValue($existingConfig, 'DB_HOST');
        error_log("Version Control: Extracting DB_NAME...");
        $oldValues['DB_NAME'] = extractStringValue($existingConfig, 'DB_NAME');
        error_log("Version Control: Extracting DB_USER...");
        $oldValues['DB_USER'] = extractStringValue($existingConfig, 'DB_USER');
        error_log("Version Control: Extracting DB_PASS...");
        $oldValues['DB_PASS'] = extractStringValue($existingConfig, 'DB_PASS');
        
        // SMTP - OPTIONAL (preserve if exists)
        $oldValues['SMTP_HOST'] = extractStringValue($existingConfig, 'SMTP_HOST');
        $oldValues['SMTP_PORT'] = extractNumericValue($existingConfig, 'SMTP_PORT');
        $oldValues['SMTP_USER'] = extractStringValue($existingConfig, 'SMTP_USER');
        $oldValues['SMTP_PASS'] = extractStringValue($existingConfig, 'SMTP_PASS');
        $oldValues['SMTP_FROM'] = extractStringValue($existingConfig, 'SMTP_FROM');
        $oldValues['SMTP_FROM_NAME'] = extractStringValue($existingConfig, 'SMTP_FROM_NAME');
        
        // IMAP - OPTIONAL (preserve if exists, otherwise use new defaults)
        $oldValues['IMAP_HOST'] = extractStringValue($existingConfig, 'IMAP_HOST');
        $oldValues['IMAP_PORT'] = extractNumericValue($existingConfig, 'IMAP_PORT');
        $oldValues['IMAP_USER'] = extractStringValue($existingConfig, 'IMAP_USER');
        $oldValues['IMAP_PASS'] = extractStringValue($existingConfig, 'IMAP_PASS');
        
        // Encryption key - don't extract (handled separately as a block)
        // This is complex because it's a variable assignment with if/else logic
        // We'll preserve the entire block from old config
        
        // Validate: Must have DB credentials
        $missingDB = [];
        if (empty($oldValues['DB_HOST'])) $missingDB[] = 'DB_HOST';
        if (empty($oldValues['DB_NAME'])) $missingDB[] = 'DB_NAME';
        if (empty($oldValues['DB_USER'])) $missingDB[] = 'DB_USER';
        if (empty($oldValues['DB_PASS'])) $missingDB[] = 'DB_PASS';
        
        if (!empty($missingDB)) {
            $errorDetails = "Cannot extract database credentials from old config. Missing: " . implode(', ', $missingDB);
            error_log("Version Control ERROR: " . $errorDetails);
            error_log("Version Control: This usually means the old config.php has a different format than expected.");
            
            // Debug: Show actual content around DB defines
            error_log("Version Control: DEBUG - Searching for DB defines in old config...");
            $debugInfo = [];
            foreach (['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS'] as $dbKey) {
                if (preg_match("/(.{0,100}define[^;]{0,200}{$dbKey}[^;]{0,200})/is", $existingConfig, $dbMatch)) {
                    $foundContent = substr(trim($dbMatch[1]), 0, 150);
                    error_log("Version Control: DEBUG - Found content around {$dbKey}: " . $foundContent);
                    $debugInfo[] = "Found {$dbKey} but couldn't extract value. Line: " . $foundContent;
                } else {
                    error_log("Version Control: DEBUG - No content found for {$dbKey}");
                    $debugInfo[] = "No define found for {$dbKey}";
                }
            }
            
            // Check if config file even contains "define" at all
            if (stripos($existingConfig, 'define') === false) {
                error_log("Version Control: DEBUG - Config file doesn't contain 'define' keyword - might be using different format");
                $errorDetails .= " (Config doesn't use 'define' statements - check server logs for format)";
            }
            
            // Show first 500 chars of old config for debugging
            $configSample = substr($existingConfig, 0, 500);
            error_log("Version Control: DEBUG - First 500 chars of old config:");
            error_log($configSample);
            
            // Store debug info for potential return (but don't expose sensitive data)
            error_log("Version Control: DEBUG Summary - " . implode(" | ", $debugInfo));
            
            return false;
        }
        
        error_log("Version Control: Extracted from old config:");
        error_log("  DB: " . ($oldValues['DB_HOST'] ? 'OK' : 'MISSING') . " / " . ($oldValues['DB_NAME'] ? 'OK' : 'MISSING'));
        error_log("  SMTP: " . ($oldValues['SMTP_USER'] ? 'EXISTS' : 'NOT FOUND'));
        error_log("  IMAP: " . ($oldValues['IMAP_HOST'] ? 'EXISTS' : 'NOT FOUND (will use new defaults)'));
        
        // STEP 2: Use new config as base (has updated structure)
        $mergedConfig = $newConfig;
        
        // STEP 3: Replace values in new config with old values
        // Helper: Replace a define value - REPLACE ONLY ONCE per define statement
        // This preserves the structure (including $_ENV fallback) but updates the default value
        function replaceDefineValue($config, $key, $oldValue, $isNumeric = false) {
            $escapedValue = $isNumeric ? $oldValue : addslashes($oldValue);
            $quotedValue = $isNumeric ? $escapedValue : "'{$escapedValue}'";
            
            // Strategy: Find the define line, then replace the default value part
            // Pattern 1: define('KEY', $_ENV['KEY'] ?? 'default_value')
            // Match the entire define and replace the default_value part
            $pattern1 = "/(define\s*\(\s*['\"]{$key}['\"]\s*,\s*\$_ENV\[['\"]{$key}['\"]\]\s*\?\?\s*)(['\"][^'\"]+['\"]|\d+)/";
            
            if (preg_match($pattern1, $config, $matches)) {
                $replacement = $matches[1] . $quotedValue;
                $config = str_replace($matches[0], $replacement, $config);
                error_log("Version Control: ✅ Replaced {$key} with old value (preserved \$_ENV structure)");
                return $config;
            }
            
            // Pattern 2: define('KEY', 'simple_value') - simple format
            // Replace entire value but add $_ENV structure for consistency
            $pattern2 = "/(define\s*\(\s*['\"]{$key}['\"]\s*,\s*)(['\"][^'\"]+['\"]|\d+)/";
            
            if (preg_match($pattern2, $config, $matches2)) {
                // Check if this is already in the new config format (with $_ENV)
                // If new config has $_ENV, we need to add it. Otherwise, just replace value
                $replacement = $matches2[1] . ($isNumeric ? "\$_ENV['{$key}'] ?? {$quotedValue}" : "\$_ENV['{$key}'] ?? {$quotedValue}");
                $config = str_replace($matches2[0], $replacement, $config);
                error_log("Version Control: ✅ Replaced {$key} with old value (added \$_ENV structure)");
                return $config;
            }
            
            // Pattern 3: Line-by-line approach - find the line and replace intelligently
            $lines = explode("\n", $config);
            foreach ($lines as $lineIdx => $line) {
                if (preg_match("/define\s*\(\s*['\"]{$key}['\"]/", $line)) {
                    // Found the line, now replace the value
                    // Try to preserve existing structure
                    if (preg_match("/(define\s*\(\s*['\"]{$key}['\"]\s*,\s*\$_ENV\[['\"]{$key}['\"]\]\s*\?\?\s*)(['\"][^'\"]+['\"]|\d+)/", $line, $lineMatches)) {
                        $newLine = str_replace($lineMatches[2], $quotedValue, $line);
                    } elseif (preg_match("/(define\s*\(\s*['\"]{$key}['\"]\s*,\s*)(['\"][^'\"]+['\"]|\d+)/", $line, $lineMatches)) {
                        $newLine = str_replace($lineMatches[2], "\$_ENV['{$key}'] ?? {$quotedValue}", $line);
                    } else {
                        continue; // Couldn't match, try next line
                    }
                    $lines[$lineIdx] = $newLine;
                    $config = implode("\n", $lines);
                    error_log("Version Control: ✅ Replaced {$key} with old value (line-by-line method)");
                    return $config;
                }
            }
            
            error_log("Version Control: ⚠️ Could not find define statement for {$key} to replace");
            return $config;
        }
        
        // Replace Database values (MUST HAVE)
        foreach (['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS'] as $key) {
            if (!empty($oldValues[$key])) {
                $mergedConfig = replaceDefineValue($mergedConfig, $key, $oldValues[$key]);
                error_log("Version Control: ✅ Replaced {$key} with old value");
            }
        }
        
        // Replace Encryption Key Block - preserve entire encryption key logic from old config
        // This is complex with if/else blocks and variable assignments, so we replace the entire section
        // Use string position-based extraction for maximum reliability
        
        // Find encryption key block in OLD config using precise boundaries
        $oldBlockStart = strpos($existingConfig, "// Security Settings");
        if ($oldBlockStart !== false) {
            // Find the end: look for define('ENCRYPTION_KEY', ...); followed by blank lines or next section
            $oldAfterStart = substr($existingConfig, $oldBlockStart);
            
            // Find where the define statement ends - look for "define('ENCRYPTION_KEY'" then find the semicolon
            $encKeyDefineStart = strpos($oldAfterStart, "define('ENCRYPTION_KEY'");
            if ($encKeyDefineStart === false) {
                $encKeyDefineStart = strpos($oldAfterStart, 'define("ENCRYPTION_KEY"');
            }
            
            if ($encKeyDefineStart !== false) {
                // Find the semicolon after the define statement
                $defineEnd = strpos($oldAfterStart, ';', $encKeyDefineStart);
                if ($defineEnd !== false) {
                    // Extract the entire block including the define statement
                    $oldBlock = substr($existingConfig, $oldBlockStart, $defineEnd + 1 - $oldBlockStart);
                    
                    // Now find and replace in MERGED config
                    $newBlockStart = strpos($mergedConfig, "// Security Settings");
                    if ($newBlockStart !== false) {
                        $newAfterStart = substr($mergedConfig, $newBlockStart);
                        $newEncKeyDefineStart = strpos($newAfterStart, "define('ENCRYPTION_KEY'");
                        if ($newEncKeyDefineStart === false) {
                            $newEncKeyDefineStart = strpos($newAfterStart, 'define("ENCRYPTION_KEY"');
                        }
                        
                        if ($newEncKeyDefineStart !== false) {
                            $newDefineEnd = strpos($newAfterStart, ';', $newEncKeyDefineStart);
                            if ($newDefineEnd !== false) {
                                $newBlock = substr($mergedConfig, $newBlockStart, $newDefineEnd + 1 - $newBlockStart);
                                
                                // Replace the block
                                $mergedConfig = str_replace($newBlock, $oldBlock, $mergedConfig);
                                
                                // Validate: Check that the replacement worked and define statement is valid
                                $validationCheck = strpos($mergedConfig, "define('ENCRYPTION_KEY'");
                                if ($validationCheck === false) {
                                    $validationCheck = strpos($mergedConfig, 'define("ENCRYPTION_KEY"');
                                }
                                
                                if ($validationCheck !== false) {
                                    // Check that define statement ends with );
                                    $defineLine = substr($mergedConfig, $validationCheck, 100);
                                    if (preg_match("/define\s*\(\s*['\"]ENCRYPTION_KEY['\"][^;]+\)\s*;/", $defineLine)) {
                                        error_log("Version Control: ✅ Replaced entire encryption key block from old config (preserved structure and logic)");
                                    } else {
                                        error_log("Version Control: ⚠️ WARNING - ENCRYPTION_KEY define statement may be malformed after merge");
                                    }
                                } else {
                                    error_log("Version Control: ⚠️ WARNING - Could not validate ENCRYPTION_KEY define after replacement");
                                }
                            }
                        }
                    }
                }
            }
        }
        
        // Final safety check: ensure the define statement is valid (has closing parenthesis and semicolon)
        if (preg_match("/define\s*\(\s*['\"]ENCRYPTION_KEY['\"][^;]*\)\s*;/", $mergedConfig) === 0) {
            error_log("Version Control: ❌ ERROR - ENCRYPTION_KEY define statement is malformed after merge - merge may have failed");
            // Don't return false here, but log the error - we still want to proceed if possible
        }
        
        // Replace SMTP values (if they existed in old config)
        if (!empty($oldValues['SMTP_HOST'])) {
            $mergedConfig = replaceDefineValue($mergedConfig, 'SMTP_HOST', $oldValues['SMTP_HOST']);
            error_log("Version Control: ✅ Replaced SMTP_HOST");
        }
        if (!empty($oldValues['SMTP_PORT'])) {
            $mergedConfig = replaceDefineValue($mergedConfig, 'SMTP_PORT', $oldValues['SMTP_PORT'], true);
            error_log("Version Control: ✅ Replaced SMTP_PORT");
        }
        if (!empty($oldValues['SMTP_USER'])) {
            $mergedConfig = replaceDefineValue($mergedConfig, 'SMTP_USER', $oldValues['SMTP_USER']);
            error_log("Version Control: ✅ Replaced SMTP_USER");
        }
        if (!empty($oldValues['SMTP_PASS'])) {
            $mergedConfig = replaceDefineValue($mergedConfig, 'SMTP_PASS', $oldValues['SMTP_PASS']);
            error_log("Version Control: ✅ Replaced SMTP_PASS");
        }
        if (!empty($oldValues['SMTP_FROM'])) {
            $mergedConfig = replaceDefineValue($mergedConfig, 'SMTP_FROM', $oldValues['SMTP_FROM']);
            error_log("Version Control: ✅ Replaced SMTP_FROM");
        }
        if (!empty($oldValues['SMTP_FROM_NAME'])) {
            $mergedConfig = replaceDefineValue($mergedConfig, 'SMTP_FROM_NAME', $oldValues['SMTP_FROM_NAME']);
            error_log("Version Control: ✅ Replaced SMTP_FROM_NAME");
        }
        
        // Replace IMAP values (if they existed in old config)
        // If they didn't exist, use SMTP credentials from old config (common pattern)
        // This ensures IMAP works with old site's email credentials
        if (!empty($oldValues['IMAP_HOST'])) {
            $mergedConfig = replaceDefineValue($mergedConfig, 'IMAP_HOST', $oldValues['IMAP_HOST']);
            error_log("Version Control: ✅ Replaced IMAP_HOST");
        } else {
            error_log("Version Control: ℹ️ IMAP_HOST not in old config - keeping new structure with new defaults");
        }
        if (!empty($oldValues['IMAP_PORT'])) {
            $mergedConfig = replaceDefineValue($mergedConfig, 'IMAP_PORT', $oldValues['IMAP_PORT'], true);
            error_log("Version Control: ✅ Replaced IMAP_PORT");
        }
        if (!empty($oldValues['IMAP_USER'])) {
            $mergedConfig = replaceDefineValue($mergedConfig, 'IMAP_USER', $oldValues['IMAP_USER']);
            error_log("Version Control: ✅ Replaced IMAP_USER");
        } elseif (!empty($oldValues['SMTP_USER'])) {
            // If IMAP_USER not in old config but SMTP_USER exists, use SMTP_USER for IMAP
            // This matches the new config pattern: IMAP_USER defaults to SMTP_USER
            $mergedConfig = replaceDefineValue($mergedConfig, 'IMAP_USER', $oldValues['SMTP_USER']);
            error_log("Version Control: ✅ Set IMAP_USER to SMTP_USER from old config (common pattern)");
        }
        if (!empty($oldValues['IMAP_PASS'])) {
            $mergedConfig = replaceDefineValue($mergedConfig, 'IMAP_PASS', $oldValues['IMAP_PASS']);
            error_log("Version Control: ✅ Replaced IMAP_PASS");
        } elseif (!empty($oldValues['SMTP_PASS'])) {
            // If IMAP_PASS not in old config but SMTP_PASS exists, use SMTP_PASS for IMAP
            $mergedConfig = replaceDefineValue($mergedConfig, 'IMAP_PASS', $oldValues['SMTP_PASS']);
            error_log("Version Control: ✅ Set IMAP_PASS to SMTP_PASS from old config (common pattern)");
        }
        
        // IMPORTANT: Keep the new SITE_URL auto-detect logic (don't replace it)
        // The new structure is better - it auto-detects the domain dynamically
        // We intentionally don't replace SITE_URL to keep the improved auto-detect feature
        error_log("Version Control: ℹ️ SITE_URL auto-detect logic preserved (improved feature from new config)");
        
        // Final validation: Check that DB credentials are actually in merged config
        // Check that the actual values appear in the merged config (not just the define statements)
        $validationErrors = [];
        
        if (strpos($mergedConfig, $oldValues['DB_HOST']) === false) {
            $validationErrors[] = "DB_HOST value not found in merged config";
        }
        if (strpos($mergedConfig, $oldValues['DB_NAME']) === false) {
            $validationErrors[] = "DB_NAME value not found in merged config";
        }
        if (strpos($mergedConfig, $oldValues['DB_USER']) === false) {
            $validationErrors[] = "DB_USER value not found in merged config";
        }
        if (strpos($mergedConfig, $oldValues['DB_PASS']) === false) {
            $validationErrors[] = "DB_PASS value not found in merged config";
        }
        
        if (!empty($validationErrors)) {
            error_log("Version Control ERROR: Merge validation failed:");
            foreach ($validationErrors as $err) {
                error_log("Version Control ERROR: - " . $err);
            }
            return false;
        }
        
        // Verify config has required structure elements
        if (strpos($mergedConfig, "define('DB_HOST'") === false && strpos($mergedConfig, 'define("DB_HOST"') === false) {
            error_log("Version Control ERROR: DB_HOST define statement not found in merged config");
            return false;
        }
        if (strpos($mergedConfig, "define('DB_NAME'") === false && strpos($mergedConfig, 'define("DB_NAME"') === false) {
            error_log("Version Control ERROR: DB_NAME define statement not found in merged config");
            return false;
        }
        
        // CRITICAL: Validate encryption key block is properly formatted
        // Check that define statement has proper syntax (closing parenthesis and semicolon)
        $encKeyDefinePos = strpos($mergedConfig, "define('ENCRYPTION_KEY'");
        if ($encKeyDefinePos === false) {
            $encKeyDefinePos = strpos($mergedConfig, 'define("ENCRYPTION_KEY"');
        }
        
        if ($encKeyDefinePos !== false) {
            // Get the define line (first 150 chars after the define)
            $defineLine = substr($mergedConfig, $encKeyDefinePos, 150);
            
            // Check that it has a closing parenthesis before semicolon
            if (!preg_match("/define\s*\(\s*['\"]ENCRYPTION_KEY['\"][^)]+\)\s*;/", $defineLine)) {
                // Check for common errors
                if (strpos($defineLine, "define('ENCRYPTION_KEY', 'ENCRYPTION_KEY'") !== false) {
                    error_log("Version Control ERROR: Encryption key block is corrupted - value is literal string 'ENCRYPTION_KEY' instead of variable");
                    return false;
                }
                if (strpos($defineLine, "define('ENCRYPTION_KEY'") !== false && strpos($defineLine, ')') === false) {
                    error_log("Version Control ERROR: Encryption key define statement is missing closing parenthesis");
                    return false;
                }
                error_log("Version Control ERROR: Encryption key define statement is malformed: " . substr($defineLine, 0, 80));
                return false;
            }
            
            // Additional check: ensure $encryptionKey variable assignment exists and is correct
            if (strpos($mergedConfig, '$encryptionKey = $_ENV[\'ENCRYPTION_KEY\']') === false && 
                strpos($mergedConfig, '$encryptionKey = $_ENV["ENCRYPTION_KEY"]') === false) {
                // Check if it has wrong assignment
                if (strpos($mergedConfig, '$encryptionKey = \'ENCRYPTION_KEY\'') !== false) {
                    error_log("Version Control ERROR: Encryption key variable assignment is corrupted - value is literal 'ENCRYPTION_KEY'");
                    return false;
                }
            }
        } else {
            error_log("Version Control ERROR: Encryption key define statement not found in merged config");
            return false;
        }
        
        error_log("Version Control: ✅ Smart merge completed successfully");
        error_log("Version Control: - Database credentials: Preserved from old config");
        error_log("Version Control: - SMTP credentials: " . (!empty($oldValues['SMTP_USER']) ? 'Preserved from old config' : 'Using new defaults'));
        error_log("Version Control: - IMAP configuration: " . (!empty($oldValues['IMAP_HOST']) ? 'Preserved from old config' : 'Added from new config structure'));
        error_log("Version Control: - SITE_URL: Using new auto-detect logic (improved feature)");
        error_log("Version Control: - Encryption key: " . (!empty($oldValues['ENCRYPTION_KEY']) ? 'Preserved from old config' : 'Using new defaults'));
        error_log("Version Control: - All new features/constants: Automatically included from new config structure");
        
        return $mergedConfig;
    }
    
    // Function to check if file should be excluded
    function shouldExcludeFile($filePath, $excludePatterns) {
        foreach ($excludePatterns as $pattern) {
            if (preg_match($pattern, $filePath)) {
                return true;
            }
        }
        return false;
    }
    
    // Apply file updates (excluding protected files)
    $config = require __DIR__ . '/../includes/version-control-config.php';
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($tempDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $sourcePath = $file->getRealPath();
            $relativePath = str_replace($tempDir . DIRECTORY_SEPARATOR, '', $sourcePath);
            $relativePath = str_replace('\\', '/', $relativePath);
            
            // Skip manifest and migrations folder (handled separately)
            if (strpos($relativePath, 'migrations/') === 0) {
                continue;
            }
            
            // CRITICAL: Skip config.php completely - it's excluded from packages
            // Each site must keep its own database and SMTP configuration
            if ($relativePath === 'config/config.php') {
                error_log("Version Control: ⚠️ config.php found in package but should be excluded. Skipping to protect site-specific configuration.");
                $updateLog['errors'][] = "config.php skipped - file should not be in update package (protected file)";
                continue; // Skip completely - never overwrite
            }
            
            // Special handling for files with API keys - preserve existing keys
            // Note: These files are also excluded from packages, but this provides extra protection
            $protectedApiKeyFiles = [
                'includes/livechat.php',
                'includes/translation.php',
                'includes/currency-converter.php'
            ];
            
            if (in_array($relativePath, $protectedApiKeyFiles)) {
                $targetPath = BASE_PATH . '/' . $relativePath;
                $targetDirPath = dirname($targetPath);
                
                // Backup existing file
                if (file_exists($targetPath)) {
                    backupFile($targetPath, $backupDir, BASE_PATH);
                }
                
                // If target file exists, preserve it (don't overwrite with new version)
                if (file_exists($targetPath)) {
                    error_log("Version Control: Preserving existing {$relativePath} to protect API keys and site-specific settings.");
                    $updateLog['errors'][] = "{$relativePath} preserved to protect API keys";
                    continue;
                }
                
                // Only copy if target doesn't exist (new installation)
                // Create target directory if needed
                if (!is_dir($targetDirPath)) {
                    mkdir($targetDirPath, 0755, true);
                }
                
                copy($sourcePath, $targetPath);
                $updateLog['files_updated']++;
                error_log("Version Control: {$relativePath} created (new installation)");
                continue;
            }
            
            // Skip if should be excluded
            if (shouldExcludeFile($relativePath, $config['exclude_patterns'])) {
                continue;
            }
            
            $targetPath = BASE_PATH . '/' . $relativePath;
            $targetDirPath = dirname($targetPath);
            
            // Backup existing file
            if (file_exists($targetPath)) {
                backupFile($targetPath, $backupDir, BASE_PATH);
            }
            
            // Create target directory if needed
            if (!is_dir($targetDirPath)) {
                mkdir($targetDirPath, 0755, true);
            }
            
            // Copy file
            copy($sourcePath, $targetPath);
            $updateLog['files_updated']++;
        }
    }
    
    // Apply database migrations
    error_log("Version Control: Checking for migrations in package...");
    if (!isset($manifest['migrations'])) {
        error_log("Version Control: ⚠️ No 'migrations' key found in manifest");
    } elseif (!is_array($manifest['migrations'])) {
        error_log("Version Control: ⚠️ 'migrations' is not an array in manifest. Type: " . gettype($manifest['migrations']));
    } elseif (empty($manifest['migrations'])) {
        error_log("Version Control: ℹ️ Migrations array is empty - no migration files were included in this package");
    } else {
        error_log("Version Control: ✅ Found " . count($manifest['migrations']) . " migration file(s) in package");
    }
    
    if (isset($manifest['migrations']) && is_array($manifest['migrations']) && !empty($manifest['migrations'])) {
        foreach ($manifest['migrations'] as $migration) {
            error_log("Version Control: Processing migration: {$migration['name']} (file: {$migration['file']})");
            try {
                // Check if migration already applied (by filename - unique identifier)
                // This prevents re-applying same migration even if it appears in different versions
                $sql = "SELECT id FROM schema_migrations WHERE migration_file = ?";
                $stmt = $db->query($sql, [$migration['file']]);
                if ($stmt->fetch()) {
                    error_log("Version Control: ℹ️ Migration file '{$migration['file']}' already applied, skipping");
                    $updateLog['errors'][] = "Migration {$migration['name']} ({$migration['file']}) already applied, skipping";
                    continue;
                }
                
                // Validate migration file
                $migrationFile = $tempDir . '/migrations/' . $migration['file'];
                if (!file_exists($migrationFile)) {
                    $updateLog['errors'][] = "Migration file not found: {$migration['file']}";
                    continue;
                }
                
                // Validate migration is safe (additive only)
                if (!validateMigration($migrationFile)) {
                    $updateLog['errors'][] = "Migration {$migration['name']} contains unsafe operations, skipping";
                    continue;
                }
                
                // Execute migration
                executeMigration($migrationFile, $db);
                
                // Record migration as applied
                $sql = "INSERT INTO schema_migrations (version, migration_name, migration_file, applied_by, status) 
                        VALUES (?, ?, ?, ?, 'success')";
                $db->query($sql, [$newVersion, $migration['name'], $migration['file'], $userId]);
                
                $updateLog['migrations_applied']++;
                
            } catch (Exception $e) {
                // Record failed migration
                $sql = "INSERT INTO schema_migrations (version, migration_name, migration_file, applied_by, status, error_message) 
                        VALUES (?, ?, ?, ?, 'failed', ?)";
                $db->query($sql, [$newVersion, $migration['name'], $migration['file'], $userId, $e->getMessage()]);
                
                $updateLog['errors'][] = "Migration {$migration['name']} failed: " . $e->getMessage();
            }
        }
    }
    
    // Update system version
    $sql = "UPDATE system_version_info SET current_version = ?, database_version = ?, last_updated = NOW(), updated_by = ?";
    $db->query($sql, [$newVersion, $newVersion, $userId]);
    
    // Record update log
    $status = (count($updateLog['errors']) === 0) ? 'success' : (($updateLog['files_updated'] > 0 || $updateLog['migrations_applied'] > 0) ? 'partial' : 'failed');
    $sql = "INSERT INTO update_logs (version, applied_date, applied_by, status, log_details, files_updated, migrations_applied) 
            VALUES (?, NOW(), ?, ?, ?, ?, ?)";
    $db->query($sql, [
        $newVersion,
        $userId,
        $status,
        json_encode($updateLog),
        $updateLog['files_updated'],
        $updateLog['migrations_applied']
    ]);
    
    // Clean up temp directory
    function deleteDirectory($dir) {
        if (!is_dir($dir)) return;
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }
    deleteDirectory($tempDir);
    
    echo json_encode([
        'success' => true,
        'message' => 'Update applied successfully',
        'version' => $newVersion,
        'files_updated' => $updateLog['files_updated'],
        'migrations_applied' => $updateLog['migrations_applied'],
        'backup_location' => $backupDir,
        'errors' => $updateLog['errors']
    ]);
    
} catch (Exception $e) {
    error_log('Apply update error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to apply update: ' . $e->getMessage()
    ]);
}

