<?php
/**
 * Runtime error log table + optional users.kyc_prompt_dismissed (dashboard must not 500 if missing).
 */

return [
    'id' => '2026_09_06_runtime_errors_and_kyc_prompt',
    'description' => 'Create runtime_errors and add users.kyc_prompt_dismissed if missing',
    'up' => function ($db) {
        DatabaseAutoMigrate::execOrFail(
            $db,
            "CREATE TABLE IF NOT EXISTS `runtime_errors` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `source` varchar(64) NOT NULL,
                `message` varchar(512) NOT NULL,
                `host` varchar(255) DEFAULT NULL,
                `uri` varchar(512) DEFAULT NULL,
                `user_id` int DEFAULT NULL,
                `detail` text DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_created_at` (`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            [],
            'Create runtime_errors'
        );

        $col = $db->query("SHOW COLUMNS FROM users LIKE 'kyc_prompt_dismissed'");
        $exists = $col && $col->fetch();
        if (!$exists) {
            DatabaseAutoMigrate::execOrFail(
                $db,
                'ALTER TABLE users ADD COLUMN kyc_prompt_dismissed tinyint(1) NOT NULL DEFAULT 0',
                [],
                'Add users.kyc_prompt_dismissed'
            );
        }
    },
];
