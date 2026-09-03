<?php
/**
 * Outbox for owned.admin_credentials.updated retries (re-sign on drain; stable event_id).
 */

return [
    'id' => '2026_09_03_seventh_tradehub_credential_sync_outbox',
    'description' => 'Create seventh_tradehub_credential_sync_outbox for Hub admin credential sync retries',
    'up' => function ($db) {
        DatabaseAutoMigrate::execOrFail(
            $db,
            "CREATE TABLE IF NOT EXISTS `seventh_tradehub_credential_sync_outbox` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `event_id` varchar(64) NOT NULL,
                `integration_id` varchar(36) NOT NULL,
                `email` varchar(255) DEFAULT NULL,
                `password_enc` text DEFAULT NULL,
                `attempts` int NOT NULL DEFAULT 0,
                `next_attempt_at` datetime NOT NULL,
                `last_error` varchar(255) DEFAULT NULL,
                `created_at` datetime NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_event_id` (`event_id`),
                KEY `idx_next_attempt` (`next_attempt_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            [],
            'Create seventh_tradehub_credential_sync_outbox'
        );
    },
];
