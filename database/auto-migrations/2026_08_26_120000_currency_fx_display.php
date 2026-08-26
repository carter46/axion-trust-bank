<?php
/**
 * Currency / FX display ownership support.
 * - exchange_rate_api_key system setting
 * - users.currency_selection_shown
 * - exchange_rates cache table + unique pair index
 */

return [
    'id' => '2026_08_26_currency_fx_display',
    'description' => 'Currency FX settings, display-currency column, and exchange_rates table',
    'up' => function ($db) {
        // 1) API key setting for live FX (optional; static fallback works without it)
        DatabaseAutoMigrate::ensureSetting(
            $db,
            'exchange_rate_api_key',
            '',
            'string',
            'ExchangeRate-API v6 API key for live FX rates. Leave empty to use cached or built-in offline fallback rates.'
        );

        // Soften 2FA required description if row exists (non-fatal if update fails)
        try {
            $db->query(
                "UPDATE system_settings
                 SET description = ?, updated_at = NOW()
                 WHERE setting_key = 'two_factor_required'",
                ['Suggest 2FA for users (informational). Does not lock users out of the app when 2FA is disabled.']
            );
        } catch (Throwable $e) {
            // ignore
        }

        // 2) Ensure users.currency_selection_shown exists (admin-assigned display currency marker)
        DatabaseAutoMigrate::ensureColumn(
            $db,
            'users',
            'currency_selection_shown',
            '`currency_selection_shown` tinyint(1) DEFAULT 0'
        );

        // 3) Ensure exchange_rates table exists
        DatabaseAutoMigrate::execOrFail(
            $db,
            "CREATE TABLE IF NOT EXISTS `exchange_rates` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `from_currency` varchar(3) NOT NULL,
                `to_currency` varchar(3) NOT NULL,
                `rate` decimal(18,8) NOT NULL,
                `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `unique_pair` (`from_currency`,`to_currency`),
                KEY `idx_from_to` (`from_currency`,`to_currency`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            [],
            'Create exchange_rates table'
        );

        // 4) Ensure unique_pair index if table already existed without it
        $idxStmt = $db->query(
            "SELECT COUNT(*) AS cnt
             FROM information_schema.statistics
             WHERE table_schema = DATABASE()
               AND table_name = 'exchange_rates'
               AND index_name = 'unique_pair'"
        );
        if ($idxStmt === false) {
            throw new Exception('Failed checking exchange_rates.unique_pair index');
        }
        $idxRow = $idxStmt->fetch();
        if (empty($idxRow['cnt'])) {
            $addIdx = $db->query("ALTER TABLE `exchange_rates` ADD UNIQUE KEY `unique_pair` (`from_currency`,`to_currency`)");
            if ($addIdx === false) {
                // Duplicate key name / already exists race — ignore only if index now present
                $recheck = $db->query(
                    "SELECT COUNT(*) AS cnt
                     FROM information_schema.statistics
                     WHERE table_schema = DATABASE()
                       AND table_name = 'exchange_rates'
                       AND index_name = 'unique_pair'"
                );
                $recheckRow = $recheck ? $recheck->fetch() : null;
                if (empty($recheckRow['cnt'])) {
                    throw new Exception('Failed adding exchange_rates.unique_pair index');
                }
            }
        }
    },
];
