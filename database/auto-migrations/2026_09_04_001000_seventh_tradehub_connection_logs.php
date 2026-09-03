<?php
/**
 * Merchant-side Connection logs for 7th Trade Hub protocol traffic.
 * Mirrors Hub “Connection logs” so each domain/DB can prove health / sync / shutdown receipts.
 */

return [
    'id' => '2026_09_04_seventh_tradehub_connection_logs',
    'description' => 'Create seventh_tradehub_connection_logs for Hub health, sync, ping, shutdown receipts',
    'up' => function ($db) {
        DatabaseAutoMigrate::execOrFail(
            $db,
            "CREATE TABLE IF NOT EXISTS `seventh_tradehub_connection_logs` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `direction` varchar(16) NOT NULL DEFAULT 'inbound',
                `event` varchar(64) NOT NULL,
                `ok` tinyint(1) NOT NULL DEFAULT 0,
                `http_status` int DEFAULT NULL,
                `error_code` varchar(64) DEFAULT NULL,
                `integration_id` varchar(36) DEFAULT NULL,
                `context` varchar(32) DEFAULT NULL,
                `host` varchar(255) DEFAULT NULL,
                `message` varchar(512) NOT NULL,
                `detail` text DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_created_at` (`created_at`),
                KEY `idx_event` (`event`),
                KEY `idx_integration` (`integration_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            [],
            'Create seventh_tradehub_connection_logs'
        );
    },
];
