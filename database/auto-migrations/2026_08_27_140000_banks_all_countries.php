<?php
/**
 * Ensure every catalog country has banks (5+, US up to 15) and remove duplicate bank rows.
 * Idempotent — safe to re-run tracking will skip after success; logic itself also skips existing names.
 */

return [
    'id' => '2026_08_27_140000_banks_all_countries',
    'description' => 'Seed banks for all countries (min 5 each, US max 15) and deactivate duplicates',
    'up' => function ($db) {
        if (!function_exists('getCountriesData')) {
            require_once dirname(__DIR__, 2) . '/includes/countries.php';
        }

        $bankNameTemplates = [
            '%s National Bank',
            '%s Commercial Bank',
            '%s Development Bank',
            'First Bank of %s',
            '%s People\'s Bank',
            '%s Cooperative Bank',
            '%s Investment Bank',
            '%s Savings Bank',
            '%s Trust Bank',
            '%s Agricultural Bank',
            '%s Merchant Bank',
            '%s Central Credit Bank',
            '%s Community Bank',
            '%s Federal Bank',
            '%s United Bank',
        ];

        // Prefer real US names when topping up / capping US list
        $usPreferred = [
            'JPMorgan Chase Bank',
            'Bank of America',
            'Wells Fargo Bank',
            'Citibank',
            'U.S. Bank',
            'PNC Bank',
            'TD Bank',
            'Capital One Bank',
            'Goldman Sachs Bank',
            'Morgan Stanley Bank',
            'Truist Bank',
            'Charles Schwab Bank',
            'Ally Bank',
            'Fifth Third Bank',
            'KeyBank',
        ];

        // 1) Deactivate exact duplicate names within the same country (keep lowest id)
        $dupStmt = $db->query(
            "SELECT country, name, MIN(id) AS keep_id, COUNT(*) AS cnt
             FROM banks
             GROUP BY country, name
             HAVING cnt > 1"
        );
        if ($dupStmt) {
            while ($row = $dupStmt->fetch()) {
                $db->query(
                    "UPDATE banks SET is_active = 0
                     WHERE country = ? AND name = ? AND id <> ?",
                    [$row['country'], $row['name'], $row['keep_id']]
                );
            }
        }

        $countries = getCountriesData();
        $seenCountryCodes = [];

        foreach ($countries as $country) {
            $code = strtoupper((string)$country['code']);
            $name = (string)$country['name'];
            $region = (string)$country['region'];

            // No duplicate countries in this pass
            if (isset($seenCountryCodes[$code])) {
                continue;
            }
            $seenCountryCodes[$code] = true;

            $target = ($code === 'US') ? 15 : 5;

            // Count active banks for this country (match by country name)
            $countStmt = $db->query(
                "SELECT id, name FROM banks WHERE country = ? AND is_active = 1 ORDER BY id ASC",
                [$name]
            );
            $existing = $countStmt ? $countStmt->fetchAll() : [];
            $existingNames = [];
            foreach ($existing as $bank) {
                $existingNames[strtolower(trim($bank['name']))] = (int)$bank['id'];
            }
            $activeCount = count($existingNames);

            // Cap US (and any over-target) by deactivating extras beyond target
            if ($activeCount > $target) {
                $ids = array_values($existingNames);
                sort($ids, SORT_NUMERIC);
                $keep = array_slice($ids, 0, $target);
                $drop = array_slice($ids, $target);
                if (!empty($drop)) {
                    $placeholders = implode(',', array_fill(0, count($drop), '?'));
                    $db->query(
                        "UPDATE banks SET is_active = 0 WHERE id IN ($placeholders)",
                        $drop
                    );
                }
                $activeCount = count($keep);
                // Refresh name map for kept only
                $existingNames = [];
                foreach ($existing as $bank) {
                    if (in_array((int)$bank['id'], $keep, true)) {
                        $existingNames[strtolower(trim($bank['name']))] = (int)$bank['id'];
                    }
                }
            }

            $needed = $target - $activeCount;
            if ($needed <= 0) {
                continue;
            }

            $candidates = [];
            if ($code === 'US') {
                foreach ($usPreferred as $preferredName) {
                    $candidates[] = $preferredName;
                }
            }
            foreach ($bankNameTemplates as $tpl) {
                $candidates[] = sprintf($tpl, $name);
            }

            $inserted = 0;
            $seq = 0;
            foreach ($candidates as $bankName) {
                if ($inserted >= $needed) {
                    break;
                }
                $key = strtolower(trim($bankName));
                if (isset($existingNames[$key])) {
                    continue;
                }

                $seq++;
                $bankCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $code . $seq . 'BNK'), 0, 8));
                $swift = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $name), 0, 4));
                if (strlen($swift) < 4) {
                    $swift = str_pad($swift, 4, 'X');
                }
                $swift .= $code . 'XX';

                $ok = $db->query(
                    "INSERT INTO banks (name, code, region, country, swift_code, is_active, created_by, created_at, updated_at)
                     VALUES (?, ?, ?, ?, ?, 1, NULL, NOW(), NOW())",
                    [$bankName, $bankCode, $region, $name, $swift]
                );
                if ($ok === false) {
                    // Retry without code column if schema differs
                    $ok = $db->query(
                        "INSERT INTO banks (name, region, country, swift_code, is_active, created_by)
                         VALUES (?, ?, ?, ?, 1, NULL)",
                        [$bankName, $region, $name, $swift]
                    );
                }
                if ($ok !== false) {
                    $existingNames[$key] = 1;
                    $inserted++;
                }
            }
        }
    },
];
