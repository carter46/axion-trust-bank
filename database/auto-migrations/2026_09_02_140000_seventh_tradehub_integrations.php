<?php
/**
 * 7th Trade Hub Protocol v1 — integration registry, subscriptions, optional nonces.
 */

return [
    'id' => '2026_09_02_seventh_tradehub_integrations',
    'description' => '7th Trade Hub integration tables and hub URL setting',
    'up' => function ($db) {
        DatabaseAutoMigrate::execOrFail(
            $db,
            "CREATE TABLE IF NOT EXISTS `seventh_tradehub_integrations` (
                `context` varchar(32) NOT NULL,
                `enabled` tinyint(1) NOT NULL DEFAULT 0,
                `integration_id` varchar(36) DEFAULT NULL,
                `client_id` varchar(255) DEFAULT NULL,
                `client_secret_enc` text DEFAULT NULL,
                `webhook_secret_enc` text DEFAULT NULL,
                `expected_user_email` varchar(255) DEFAULT NULL,
                `expected_admin_email` varchar(255) DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                `updated_by` int(11) DEFAULT NULL,
                PRIMARY KEY (`context`),
                UNIQUE KEY `uk_integration_id` (`integration_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            [],
            'Create seventh_tradehub_integrations'
        );

        foreach (['demo', 'owned_tool'] as $context) {
            $stmt = $db->query(
                'SELECT context FROM seventh_tradehub_integrations WHERE context = ? LIMIT 1',
                [$context]
            );
            if ($stmt && !$stmt->fetch()) {
                $db->query(
                    'INSERT INTO seventh_tradehub_integrations (context, enabled, updated_at) VALUES (?, 0, NOW())',
                    [$context]
                );
            }
        }

        DatabaseAutoMigrate::execOrFail(
            $db,
            "CREATE TABLE IF NOT EXISTS `seventh_tradehub_subscriptions` (
                `integration_id` varchar(36) NOT NULL,
                `tool_id` int(11) DEFAULT NULL,
                `public_id` varchar(64) DEFAULT NULL,
                `status` varchar(32) NOT NULL DEFAULT 'pending_setup',
                `expires_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                `last_sync_at` datetime DEFAULT NULL,
                PRIMARY KEY (`integration_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            [],
            'Create seventh_tradehub_subscriptions'
        );

        DatabaseAutoMigrate::execOrFail(
            $db,
            "CREATE TABLE IF NOT EXISTS `seventh_tradehub_nonces` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `integration_id` varchar(36) NOT NULL,
                `request_id` varchar(128) NOT NULL,
                `nonce` varchar(128) NOT NULL,
                `seen_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_integration_request` (`integration_id`, `request_id`),
                KEY `idx_seen_at` (`seen_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            [],
            'Create seventh_tradehub_nonces'
        );

        DatabaseAutoMigrate::ensureSetting(
            $db,
            'seventh_tradehub_hub_url',
            '',
            'string',
            '7th Trade Hub base URL (https://, no trailing slash). Shared by demo and owned integrations.'
        );
    },
];
