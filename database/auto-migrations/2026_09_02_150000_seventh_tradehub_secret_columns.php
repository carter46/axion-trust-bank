<?php
/**
 * Ensure Hub secret columns exist as TEXT (fixes older partial installs).
 */

return [
    'id' => '2026_09_02_seventh_tradehub_secret_columns',
    'description' => 'Ensure seventh_tradehub_integrations secret columns are TEXT',
    'up' => function ($db) {
        $conn = $db->getConnection();

        $tableCheck = $conn->query("SHOW TABLES LIKE 'seventh_tradehub_integrations'");
        if (!$tableCheck || !$tableCheck->fetch()) {
            // Base migration will create the full table
            return;
        }

        $columns = [];
        $cols = $conn->query('SHOW COLUMNS FROM seventh_tradehub_integrations');
        if ($cols) {
            while ($row = $cols->fetch(PDO::FETCH_ASSOC)) {
                $columns[strtolower($row['Field'])] = $row;
            }
        }

        if (!isset($columns['client_secret_enc'])) {
            $conn->exec('ALTER TABLE seventh_tradehub_integrations ADD COLUMN client_secret_enc TEXT NULL');
        } else {
            $conn->exec('ALTER TABLE seventh_tradehub_integrations MODIFY COLUMN client_secret_enc TEXT NULL');
        }

        if (!isset($columns['webhook_secret_enc'])) {
            $conn->exec('ALTER TABLE seventh_tradehub_integrations ADD COLUMN webhook_secret_enc TEXT NULL');
        } else {
            $conn->exec('ALTER TABLE seventh_tradehub_integrations MODIFY COLUMN webhook_secret_enc TEXT NULL');
        }

        // Clear unreadable legacy blobs so UI forces a fresh paste
        try {
            $db->query(
                "UPDATE seventh_tradehub_integrations
                 SET client_secret_enc = NULL
                 WHERE client_secret_enc IS NOT NULL
                   AND client_secret_enc NOT LIKE 'sth0:%'
                   AND client_secret_enc NOT LIKE 'sth0.%'"
            );
        } catch (Throwable $e) {
            // non-fatal
        }
    },
];
