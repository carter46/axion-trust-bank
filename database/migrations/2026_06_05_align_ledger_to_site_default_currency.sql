/*
  Align ledger data to site default_currency (USD by default).

  - Reads default from system_settings.default_currency (never hardcodes CAD/AED).
  - Converts legacy account/transaction balances using exchange_rates.
  - Idempotent: skips when ledger_aligned_to_site_default = 1.
  - Users who explicitly chose a display currency (currency_selection_shown = 1) are unchanged.

  BACK UP YOUR DATABASE BEFORE RUNNING.
*/

SET @db := DATABASE();

/* Normalize legacy default_currency from old installs */
UPDATE `system_settings`
SET `setting_value` = 'USD', `updated_at` = NOW()
WHERE `setting_key` = 'default_currency'
  AND UPPER(TRIM(`setting_value`)) IN ('CAD', 'GBP', '');

SET @site_default := (
  SELECT UPPER(TRIM(`setting_value`))
  FROM `system_settings`
  WHERE `setting_key` = 'default_currency'
  LIMIT 1
);
SET @site_default := IF(@site_default IS NULL OR @site_default = '', 'USD', @site_default);

SET @ledger_aligned := (
  SELECT COUNT(*) FROM `system_settings`
  WHERE `setting_key` = 'ledger_aligned_to_site_default' AND `setting_value` = '1'
);

SET @legacy_ledger := (
  SELECT UPPER(a.`currency`)
  FROM `accounts` a
  WHERE UPPER(IFNULL(a.`currency`, '')) <> @site_default
    AND IFNULL(a.`currency`, '') <> ''
  LIMIT 1
);
SET @loan_rate := (
  SELECT er.`rate` FROM `exchange_rates` er
  WHERE UPPER(er.`from_currency`) = @legacy_ledger
    AND UPPER(er.`to_currency`) = @site_default
  LIMIT 1
);
SET @loan_rate := IFNULL(@loan_rate, (
  SELECT 1 / er.`rate` FROM `exchange_rates` er
  WHERE UPPER(er.`from_currency`) = @site_default
    AND UPPER(er.`to_currency`) = @legacy_ledger
  LIMIT 1
));
SET @loan_rate := IF(@legacy_ledger IS NULL OR @legacy_ledger = '', 1, IFNULL(@loan_rate, 1));

SET @has_currency_selection_shown := (
  SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema = @db AND table_name = 'users' AND column_name = 'currency_selection_shown'
);
SET @sql := IF(@ledger_aligned = 0 AND @has_currency_selection_shown > 0,
  CONCAT(
    'UPDATE `users` SET `currency` = ''', @site_default, ''' ',
    'WHERE `role` != ''admin'' ',
    'AND (`currency_selection_shown` IS NULL OR `currency_selection_shown` = 0)'
  ),
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

UPDATE `accounts` a
INNER JOIN `exchange_rates` er
  ON UPPER(er.`from_currency`) = UPPER(a.`currency`)
 AND UPPER(er.`to_currency`) = @site_default
SET
  a.`balance` = ROUND(a.`balance` * er.`rate`, 2),
  a.`available_balance` = ROUND(IFNULL(a.`available_balance`, a.`balance`) * er.`rate`, 2),
  a.`currency` = @site_default,
  a.`updated_at` = NOW()
WHERE @ledger_aligned = 0
  AND UPPER(IFNULL(a.`currency`, '')) <> @site_default
  AND IFNULL(a.`currency`, '') <> '';

UPDATE `accounts` a
INNER JOIN `exchange_rates` er
  ON UPPER(er.`from_currency`) = @site_default
 AND UPPER(er.`to_currency`) = UPPER(a.`currency`)
SET
  a.`balance` = ROUND(a.`balance` / NULLIF(er.`rate`, 0), 2),
  a.`available_balance` = ROUND(IFNULL(a.`available_balance`, a.`balance`) / NULLIF(er.`rate`, 0), 2),
  a.`currency` = @site_default,
  a.`updated_at` = NOW()
WHERE @ledger_aligned = 0
  AND UPPER(IFNULL(a.`currency`, '')) <> @site_default
  AND IFNULL(a.`currency`, '') <> ''
  AND NOT EXISTS (
    SELECT 1 FROM `exchange_rates` er2
    WHERE UPPER(er2.`from_currency`) = UPPER(a.`currency`)
      AND UPPER(er2.`to_currency`) = @site_default
  );

UPDATE `transactions` t
INNER JOIN `exchange_rates` er
  ON UPPER(er.`from_currency`) = UPPER(t.`currency`)
 AND UPPER(er.`to_currency`) = @site_default
SET
  t.`amount` = ROUND(t.`amount` * er.`rate`, 2),
  t.`balance_before` = ROUND(t.`balance_before` * er.`rate`, 2),
  t.`balance_after` = ROUND(t.`balance_after` * er.`rate`, 2),
  t.`fee` = ROUND(IFNULL(t.`fee`, 0) * er.`rate`, 2),
  t.`currency` = @site_default
WHERE @ledger_aligned = 0
  AND UPPER(IFNULL(t.`currency`, '')) <> @site_default
  AND IFNULL(t.`currency`, '') <> '';

UPDATE `transactions` t
INNER JOIN `exchange_rates` er
  ON UPPER(er.`from_currency`) = @site_default
 AND UPPER(er.`to_currency`) = UPPER(t.`currency`)
SET
  t.`amount` = ROUND(t.`amount` / NULLIF(er.`rate`, 0), 2),
  t.`balance_before` = ROUND(t.`balance_before` / NULLIF(er.`rate`, 0), 2),
  t.`balance_after` = ROUND(t.`balance_after` / NULLIF(er.`rate`, 0), 2),
  t.`fee` = ROUND(IFNULL(t.`fee`, 0) / NULLIF(er.`rate`, 0), 2),
  t.`currency` = @site_default
WHERE @ledger_aligned = 0
  AND UPPER(IFNULL(t.`currency`, '')) <> @site_default
  AND IFNULL(t.`currency`, '') <> ''
  AND NOT EXISTS (
    SELECT 1 FROM `exchange_rates` er2
    WHERE UPPER(er2.`from_currency`) = UPPER(t.`currency`)
      AND UPPER(er2.`to_currency`) = @site_default
  );

SET @has_loans := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'loans'
);
SET @sql := IF(@ledger_aligned = 0 AND @has_loans > 0 AND @loan_rate <> 1,
  CONCAT(
    'UPDATE `loans` SET ',
    '`loan_amount` = ROUND(`loan_amount` * ', @loan_rate, ', 2), ',
    '`approved_amount` = ROUND(IFNULL(`approved_amount`, `loan_amount`) * ', @loan_rate, ', 2), ',
    '`outstanding_balance` = ROUND(IFNULL(`outstanding_balance`, 0) * ', @loan_rate, ', 2), ',
    '`monthly_payment` = ROUND(IFNULL(`monthly_payment`, 0) * ', @loan_rate, ', 2)'
  ),
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_loan_payments := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'loan_payments'
);
SET @sql := IF(@ledger_aligned = 0 AND @has_loan_payments > 0 AND @loan_rate <> 1,
  CONCAT(
    'UPDATE `loan_payments` SET ',
    '`principal_amount` = ROUND(`principal_amount` * ', @loan_rate, ', 2), ',
    '`interest_amount` = ROUND(`interest_amount` * ', @loan_rate, ', 2), ',
    '`payment_amount` = ROUND(`payment_amount` * ', @loan_rate, ', 2), ',
    '`penalty_amount` = ROUND(IFNULL(`penalty_amount`, 0) * ', @loan_rate, ', 2)'
  ),
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_cards := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'cards'
);
SET @sql := IF(@ledger_aligned = 0 AND @has_cards > 0 AND @loan_rate <> 1,
  CONCAT(
    'UPDATE `cards` SET ',
    '`balance` = ROUND(IFNULL(`balance`, 0) * ', @loan_rate, ', 2), ',
    '`credit_limit` = ROUND(IFNULL(`credit_limit`, 0) * ', @loan_rate, ', 2), ',
    '`available_credit` = ROUND(IFNULL(`available_credit`, 0) * ', @loan_rate, ', 2), ',
    '`daily_limit` = ROUND(IFNULL(`daily_limit`, 0) * ', @loan_rate, ', 2), ',
    '`monthly_limit` = ROUND(IFNULL(`monthly_limit`, 0) * ', @loan_rate, ', 2)'
  ),
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(@ledger_aligned = 0 AND @loan_rate <> 1,
  CONCAT(
    'UPDATE `users` SET `investment_balance` = ROUND(IFNULL(`investment_balance`, 0) * ', @loan_rate, ', 2) ',
    'WHERE `investment_balance` IS NOT NULL AND `investment_balance` > 0'
  ),
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_user_investments := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'user_investments'
);
SET @sql := IF(@ledger_aligned = 0 AND @has_user_investments > 0 AND @loan_rate <> 1,
  CONCAT(
    'UPDATE `user_investments` SET ',
    '`amount_principal` = ROUND(`amount_principal` * ', @loan_rate, ', 2), ',
    '`current_accrued` = ROUND(IFNULL(`current_accrued`, 0) * ', @loan_rate, ', 2), ',
    '`total_roi_paid` = ROUND(IFNULL(`total_roi_paid`, 0) * ', @loan_rate, ', 2)'
  ),
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`, `created_at`, `updated_at`)
SELECT 'ledger_aligned_to_site_default', '1', 'boolean',
       'Ledger balances converted to site default_currency using exchange_rates',
       NOW(), NOW()
WHERE @ledger_aligned = 0
  AND NOT EXISTS (
    SELECT 1 FROM `system_settings` WHERE `setting_key` = 'ledger_aligned_to_site_default'
  );
