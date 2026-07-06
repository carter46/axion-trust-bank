/* 
  Safe schema upgrade (idempotent) — SINGLE live-server migration file
  Goal:
  - Bring very old databases up to date for current app features.
  - Create missing tables, add missing columns, add critical indexes/uniques, seed required system_settings keys.
  - NEVER overwrite existing business data (no destructive updates).
  - Safe to re-run.

  Includes (all prior migrations merged here):
  - Core schema, banking UX (KYC fields), dynamic KYC config, admin audit logs
  - Site default currency + ledger alignment (exchange_rates conversion, runs once)
  - Transaction history generator (templates, batches, engine audit columns)
  - expense_category enum extension (bonus, refund, utility)
  - Default checking transaction template seed (Andy reference pack, skipped if slug exists)

  How to run:
  - phpMyAdmin/Adminer: select DB, paste + run this whole file
  - CLI: mysql -u USER -p DB_NAME < 2026_03_19_safe_schema_upgrade.sql

  Do NOT run separate files in this folder — this is the only migration needed for live upgrades.
  BACK UP YOUR DATABASE BEFORE RUNNING (ledger alignment converts balances once).
*/

SET @db := DATABASE();

/* -------------------------------------------------------------------------- */
/* 1) Core tables (create if missing)                                         */
/* -------------------------------------------------------------------------- */

/* system_settings */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'system_settings'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `system_settings` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `setting_key` varchar(100) NOT NULL,
     `setting_value` text DEFAULT NULL,
     `setting_type` enum(''string'',''number'',''boolean'',''json'') DEFAULT ''string'',
     `description` text DEFAULT NULL,
     `updated_by` int(11) DEFAULT NULL,
     `created_at` timestamp NULL DEFAULT current_timestamp(),
     `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
     PRIMARY KEY (`id`),
     KEY `idx_setting_key` (`setting_key`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* -------------------------------------------------------------------------- */
/* OPTIONAL: Currency code normalization (DATA CHANGE)                         */
/* -------------------------------------------------------------------------- */
/* This section is intentionally commented out.
   It updates currency CODES only (does NOT convert numeric balances/amounts).
   If you want to run it, uncomment line-by-line and execute after you confirm
   you do NOT need multi-currency accounts.
*/
--
-- START TRANSACTION;
--
-- /* Ensure default currency setting exists (fallback USD) */
-- INSERT INTO system_settings (setting_key, setting_value, setting_type, description, created_at, updated_at)
-- SELECT 'default_currency', 'USD', 'string', 'Default system currency', NOW(), NOW()
-- WHERE NOT EXISTS (
--   SELECT 1 FROM system_settings WHERE setting_key = 'default_currency'
-- );
--
-- /* Read default currency from settings */
-- SET @default_currency := (
--   SELECT UPPER(TRIM(setting_value))
--   FROM system_settings
--   WHERE setting_key = 'default_currency'
--   LIMIT 1
-- );
--
-- /* Fallback if somehow empty */
-- SET @default_currency := IF(@default_currency IS NULL OR @default_currency = '', 'USD', @default_currency);
--
-- /* Update non-admin users who have NOT chosen a currency yet (if tracking column exists) */
-- SET @has_currency_selection_shown := (
--   SELECT COUNT(*)
--   FROM information_schema.columns
--   WHERE table_schema = DATABASE()
--     AND table_name = 'users'
--     AND column_name = 'currency_selection_shown'
-- );
--
-- SET @sql := IF(@has_currency_selection_shown > 0,
--   CONCAT(
--     'UPDATE users ',
--     'SET currency = ''', @default_currency, ''' ',
--     'WHERE role != ''admin'' ',
--     'AND (currency_selection_shown IS NULL OR currency_selection_shown = 0)'
--   ),
--   CONCAT(
--     'UPDATE users ',
--     'SET currency = ''', @default_currency, ''' ',
--     'WHERE role != ''admin'' ',
--     'AND (currency IS NULL OR currency = '''')'
--   )
-- );
-- PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
--
-- /* Update all accounts */
-- UPDATE accounts
-- SET currency = @default_currency;
--
-- COMMIT;

/* users */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'users'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `users` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `email` varchar(255) NOT NULL,
     `password_hash` varchar(255) NOT NULL,
     `full_name` varchar(255) NOT NULL,
     `phone` varchar(20) DEFAULT NULL,
     `date_of_birth` date DEFAULT NULL,
     `gender` enum(''male'',''female'',''other'') DEFAULT NULL,
     `address` text DEFAULT NULL,
     `city` varchar(100) DEFAULT NULL,
     `state` varchar(100) DEFAULT NULL,
     `country` varchar(100) DEFAULT NULL,
     `postal_code` varchar(20) DEFAULT NULL,
     `profile_picture` varchar(255) DEFAULT NULL,
     `role` enum(''user'',''business'',''admin'',''support'') DEFAULT ''user'',
     `is_super_admin` tinyint(1) DEFAULT 0,
     `status` enum(''active'',''suspended'',''blocked'',''pending'',''restricted'',''closed'',''hold'') DEFAULT ''pending'',
     `kyc_status` enum(''pending'',''verified'',''rejected'') DEFAULT ''pending'',
     `kyc_prompt_dismissed` tinyint(1) NOT NULL DEFAULT 0,
     `kyc_document_path` varchar(255) DEFAULT NULL,
     `kyc_submitted_at` datetime DEFAULT NULL,
     `two_factor_enabled` tinyint(1) DEFAULT 0,
     `two_factor_method` enum(''sms'',''email'',''app'') DEFAULT ''email'',
     `security_question_1` varchar(255) DEFAULT NULL,
     `security_answer_1` varchar(255) DEFAULT NULL,
     `security_question_2` varchar(255) DEFAULT NULL,
     `security_answer_2` varchar(255) DEFAULT NULL,
     `last_login` datetime DEFAULT NULL,
     `email_verified` tinyint(1) DEFAULT 0,
     `phone_verified` tinyint(1) DEFAULT 0,
     `notification_preferences` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
     `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
     `language` varchar(10) DEFAULT ''en'',
     `currency` varchar(10) DEFAULT ''USD'',
     `investment_balance` decimal(15,2) DEFAULT 0.00,
     `transfer_pin` varchar(255) DEFAULT NULL,
     `security_pin` varchar(255) DEFAULT NULL,
     `login_pin` varchar(255) DEFAULT NULL,
     `onboarding_completed` tinyint(1) DEFAULT 0,
     `transaction_override` varchar(20) DEFAULT ''normal'',
     `transfer_otp_required` tinyint(1) DEFAULT 1,
     `imf_code` varchar(20) DEFAULT NULL,
     `imf_required` tinyint(1) DEFAULT 0,
     `federal_swift_code` varchar(20) DEFAULT NULL,
     `federal_swift_required` tinyint(1) DEFAULT 0,
     `vat_code` varchar(20) DEFAULT NULL,
     `vat_required` tinyint(1) DEFAULT 0,
     `tac_code` varchar(20) DEFAULT NULL,
     `tac_required` tinyint(1) DEFAULT 0,
     `tin_code` varchar(20) DEFAULT NULL,
     `tin_required` tinyint(1) DEFAULT 0,
     `created_at` timestamp NULL DEFAULT current_timestamp(),
     `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
     `currency_selection_shown` tinyint(1) DEFAULT 0,
     PRIMARY KEY (`id`),
     UNIQUE KEY `email` (`email`),
     KEY `idx_email` (`email`),
     KEY `idx_status` (`status`),
     KEY `idx_role` (`role`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* accounts */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'accounts'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `accounts` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `user_id` int(11) NOT NULL,
     `account_number` varchar(20) NOT NULL,
     `account_type` enum(''checking'',''savings'',''business'',''investment'',''retirement'',''joint'',''join_existing'') NOT NULL,
     `account_name` varchar(255) DEFAULT NULL,
     `balance` decimal(15,2) DEFAULT 0.00,
     `available_balance` decimal(15,2) DEFAULT 0.00,
     `currency` varchar(10) DEFAULT ''USD'',
     `interest_rate` decimal(5,2) DEFAULT 0.00,
     `overdraft_limit` decimal(15,2) DEFAULT 0.00,
     `daily_limit` decimal(15,2) DEFAULT 5000.00,
     `status` enum(''active'',''frozen'',''closed'') DEFAULT ''active'',
     `opened_at` timestamp NULL DEFAULT current_timestamp(),
     `closed_at` datetime DEFAULT NULL,
     `created_at` timestamp NULL DEFAULT current_timestamp(),
     `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
     PRIMARY KEY (`id`),
     UNIQUE KEY `account_number` (`account_number`),
     KEY `idx_user_id` (`user_id`),
     KEY `idx_status` (`status`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* transactions */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'transactions'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `transactions` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `transaction_ref` varchar(50) NOT NULL,
     `user_id` int(11) NOT NULL,
     `account_id` int(11) NOT NULL,
     `transaction_type` enum(''debit'',''credit'') NOT NULL,
     `category` enum(''transfer'',''payment'',''deposit'',''withdrawal'',''fee'',''interest'',''loan'',''card'',''other'') NOT NULL,
     `expense_category` enum(''shopping'',''food'',''transport'',''bills'',''entertainment'',''healthcare'',''travel'',''education'',''salary'',''investment'',''rent'',''insurance'',''gift'',''personal'',''other'',''bonus'',''refund'',''utility'') DEFAULT NULL,
     `amount` decimal(15,2) NOT NULL,
     `currency` varchar(10) DEFAULT ''USD'',
     `balance_before` decimal(15,2) DEFAULT NULL,
     `balance_after` decimal(15,2) DEFAULT NULL,
     `description` text DEFAULT NULL,
     `recipient_account` varchar(255) DEFAULT NULL,
     `recipient_name` varchar(255) DEFAULT NULL,
     `recipient_bank` varchar(255) DEFAULT NULL,
     `status` enum(''pending'',''processing'',''completed'',''failed'',''reversed'') DEFAULT ''pending'',
     `payment_method` varchar(50) DEFAULT NULL,
     `fee` decimal(10,2) DEFAULT 0.00,
     `exchange_rate` decimal(10,4) DEFAULT NULL,
     `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
     `ip_address` varchar(45) DEFAULT NULL,
     `created_at` timestamp NULL DEFAULT current_timestamp(),
     `completed_at` datetime DEFAULT NULL,
     PRIMARY KEY (`id`),
     UNIQUE KEY `transaction_ref` (`transaction_ref`),
     KEY `idx_user_id` (`user_id`),
     KEY `idx_account_id` (`account_id`),
     KEY `idx_status` (`status`),
     KEY `idx_created_at` (`created_at`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* banks */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'banks'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `banks` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `name` varchar(255) NOT NULL,
     `code` varchar(10) DEFAULT NULL,
     `region` varchar(50) NOT NULL,
     `country` varchar(100) NOT NULL,
     `swift_code` varchar(20) DEFAULT NULL,
     `is_active` tinyint(1) DEFAULT 1,
     `created_by` int(11) DEFAULT NULL,
     `created_at` timestamp NULL DEFAULT current_timestamp(),
     `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
     PRIMARY KEY (`id`),
     KEY `idx_country` (`country`),
     KEY `idx_region` (`region`),
     KEY `idx_is_active` (`is_active`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* -------------------------------------------------------------------------- */
/* 2) Feature tables (create if missing)                                      */
/* -------------------------------------------------------------------------- */

/* exchange_rates (currency conversion cache) */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'exchange_rates'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `exchange_rates` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `from_currency` varchar(10) NOT NULL,
     `to_currency` varchar(10) NOT NULL,
     `rate` decimal(10,4) NOT NULL,
     `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
     PRIMARY KEY (`id`),
     UNIQUE KEY `unique_pair` (`from_currency`,`to_currency`),
     KEY `idx_updated_at` (`updated_at`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* currencies */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'currencies'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `currencies` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `code` varchar(10) NOT NULL,
     `name` varchar(100) NOT NULL,
     `symbol` varchar(10) NOT NULL,
     `exchange_rate` decimal(15,6) DEFAULT 1.000000,
     `is_active` tinyint(1) DEFAULT 1,
     `created_at` timestamp NULL DEFAULT current_timestamp(),
     `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
     PRIMARY KEY (`id`),
     UNIQUE KEY `code` (`code`),
     KEY `idx_code` (`code`),
     KEY `idx_is_active` (`is_active`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* activity_logs */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'activity_logs'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `activity_logs` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `user_id` int(11) DEFAULT NULL,
     `action` varchar(255) NOT NULL,
     `details` text DEFAULT NULL,
     `ip_address` varchar(45) DEFAULT NULL,
     `user_agent` text DEFAULT NULL,
     `created_at` timestamp NULL DEFAULT current_timestamp(),
     PRIMARY KEY (`id`),
     KEY `idx_user_id` (`user_id`),
     KEY `idx_action` (`action`),
     KEY `idx_created_at` (`created_at`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* two_factor_codes */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'two_factor_codes'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `two_factor_codes` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `user_id` int(11) NOT NULL,
     `code` varchar(10) NOT NULL,
     `method` enum(''sms'',''email'',''app'') NOT NULL,
     `purpose` varchar(20) NOT NULL DEFAULT ''login'',
     `used` tinyint(1) DEFAULT 0,
     `expires_at` datetime NOT NULL,
     `created_at` timestamp NULL DEFAULT current_timestamp(),
     PRIMARY KEY (`id`),
     KEY `idx_user_id` (`user_id`),
     KEY `idx_code` (`code`),
     KEY `idx_purpose` (`purpose`),
     KEY `idx_expires_at` (`expires_at`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* password_reset_tokens */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'password_reset_tokens'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `password_reset_tokens` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `user_id` int(11) NOT NULL,
     `token` varchar(255) NOT NULL,
     `used` tinyint(1) DEFAULT 0,
     `expires_at` datetime NOT NULL,
     `created_at` timestamp NULL DEFAULT current_timestamp(),
     PRIMARY KEY (`id`),
     KEY `idx_user_id` (`user_id`),
     KEY `idx_token` (`token`),
     KEY `idx_expires_at` (`expires_at`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* email_verification_tokens */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'email_verification_tokens'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `email_verification_tokens` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `user_id` int(11) NOT NULL,
     `token` varchar(255) NOT NULL,
     `used` tinyint(1) DEFAULT 0,
     `expires_at` datetime NOT NULL,
     `created_at` timestamp NULL DEFAULT current_timestamp(),
     PRIMARY KEY (`id`),
     KEY `idx_user_id` (`user_id`),
     KEY `idx_token` (`token`),
     KEY `idx_expires_at` (`expires_at`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* notifications */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'notifications'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `notifications` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `user_id` int(11) NOT NULL,
     `type` varchar(50) NOT NULL,
     `title` varchar(255) NOT NULL,
     `message` text NOT NULL,
     `is_read` tinyint(1) DEFAULT 0,
     `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
     `created_at` timestamp NULL DEFAULT current_timestamp(),
     PRIMARY KEY (`id`),
     KEY `idx_user_id` (`user_id`),
     KEY `idx_is_read` (`is_read`),
     KEY `idx_created_at` (`created_at`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* loans + loan_payments */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'loans'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `loans` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `user_id` int(11) NOT NULL,
     `account_id` int(11) DEFAULT NULL,
     `loan_type` enum(''personal'',''auto'',''mortgage'',''business'',''education'') NOT NULL,
     `loan_amount` decimal(15,2) NOT NULL,
     `approved_amount` decimal(15,2) DEFAULT NULL,
     `outstanding_balance` decimal(15,2) DEFAULT NULL,
     `interest_rate` decimal(5,2) NOT NULL,
     `term_months` int(11) NOT NULL,
     `monthly_payment` decimal(15,2) DEFAULT NULL,
     `purpose` text DEFAULT NULL,
     `status` enum(''pending'',''approved'',''rejected'',''active'',''completed'',''defaulted'') DEFAULT ''pending'',
     `application_date` timestamp NULL DEFAULT current_timestamp(),
     `approval_date` datetime DEFAULT NULL,
     `first_payment_date` date DEFAULT NULL,
     `last_payment_date` date DEFAULT NULL,
     `next_payment_date` date DEFAULT NULL,
     `documents` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
     `notes` text DEFAULT NULL,
     `created_at` timestamp NULL DEFAULT current_timestamp(),
     `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
     PRIMARY KEY (`id`),
     KEY `idx_user_id` (`user_id`),
     KEY `idx_status` (`status`),
     KEY `idx_application_date` (`application_date`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'loan_payments'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `loan_payments` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `loan_id` int(11) NOT NULL,
     `payment_amount` decimal(15,2) NOT NULL,
     `principal_amount` decimal(15,2) NOT NULL,
     `interest_amount` decimal(15,2) NOT NULL,
     `penalty_amount` decimal(15,2) DEFAULT 0.00,
     `payment_date` date NOT NULL,
     `due_date` date NOT NULL,
     `status` enum(''scheduled'',''paid'',''overdue'',''waived'') DEFAULT ''scheduled'',
     `payment_method` varchar(50) DEFAULT NULL,
     `transaction_ref` varchar(50) DEFAULT NULL,
     `created_at` timestamp NULL DEFAULT current_timestamp(),
     PRIMARY KEY (`id`),
     KEY `idx_loan_id` (`loan_id`),
     KEY `idx_status` (`status`),
     KEY `idx_payment_date` (`payment_date`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* cards + card_applications + card_transactions */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'cards'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `cards` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `user_id` int(11) NOT NULL,
     `account_id` int(11) NOT NULL,
     `card_number` varchar(255) NOT NULL,
     `card_type` enum(''debit'',''credit'',''prepaid'',''virtual'') NOT NULL,
     `card_name` varchar(100) DEFAULT NULL,
     `cvv` varchar(255) DEFAULT NULL,
     `expiry_date` date NOT NULL,
     `credit_limit` decimal(15,2) DEFAULT NULL,
     `available_credit` decimal(15,2) DEFAULT NULL,
     `balance` decimal(15,2) DEFAULT 0.00,
     `billing_cycle` int(11) DEFAULT 1,
     `is_virtual` tinyint(1) DEFAULT 0,
     `is_single_use` tinyint(1) DEFAULT 0,
     `status` enum(''pending'',''active'',''frozen'',''blocked'',''expired'',''cancelled'',''rejected'') DEFAULT ''pending'',
     `daily_limit` decimal(15,2) DEFAULT 5000.00,
     `monthly_limit` decimal(15,2) DEFAULT 50000.00,
     `pin_hash` varchar(255) DEFAULT NULL,
     `last_used` datetime DEFAULT NULL,
     `created_at` timestamp NULL DEFAULT current_timestamp(),
     `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
     `expires_at` datetime DEFAULT NULL,
     `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
     PRIMARY KEY (`id`),
     KEY `idx_user_id` (`user_id`),
     KEY `idx_account_id` (`account_id`),
     KEY `idx_status` (`status`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'card_applications'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `card_applications` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `user_id` int(11) NOT NULL,
     `account_id` int(11) NOT NULL,
     `card_type` enum(''debit'',''credit'',''prepaid'',''virtual'') NOT NULL,
     `card_name` varchar(100) DEFAULT NULL,
     `requested_credit_limit` decimal(15,2) DEFAULT NULL,
     `is_virtual` tinyint(1) DEFAULT 0,
     `purpose` text DEFAULT NULL,
     `employment_status` varchar(100) DEFAULT NULL,
     `annual_income` decimal(15,2) DEFAULT NULL,
     `status` enum(''pending'',''approved'',''rejected'') DEFAULT ''pending'',
     `applied_date` timestamp NULL DEFAULT current_timestamp(),
     `reviewed_by` int(11) DEFAULT NULL,
     `reviewed_date` datetime DEFAULT NULL,
     `admin_notes` text DEFAULT NULL,
     `rejection_reason` text DEFAULT NULL,
     `created_at` timestamp NULL DEFAULT current_timestamp(),
     `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
     PRIMARY KEY (`id`),
     KEY `idx_user_id` (`user_id`),
     KEY `idx_status` (`status`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'card_transactions'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `card_transactions` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `card_id` int(11) NOT NULL,
     `user_id` int(11) NOT NULL,
     `transaction_type` enum(''credit'',''debit'') NOT NULL,
     `amount` decimal(15,2) NOT NULL,
     `category` varchar(50) DEFAULT NULL,
     `description` text DEFAULT NULL,
     `balance_before` decimal(15,2) NOT NULL,
     `balance_after` decimal(15,2) NOT NULL,
     `status` enum(''pending'',''completed'',''failed'',''cancelled'') DEFAULT ''completed'',
     `reference` varchar(100) DEFAULT NULL,
     `payment_method` varchar(50) DEFAULT NULL,
     `merchant_name` varchar(255) DEFAULT NULL,
     `merchant_category` varchar(100) DEFAULT NULL,
     `location` varchar(255) DEFAULT NULL,
     `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
     `ip_address` varchar(45) DEFAULT NULL,
     `created_at` timestamp NULL DEFAULT current_timestamp(),
     `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
     PRIMARY KEY (`id`),
     KEY `idx_card_id` (`card_id`),
     KEY `idx_user_id` (`user_id`),
     KEY `idx_status` (`status`),
     KEY `idx_created_at` (`created_at`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* crypto_wallets */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'crypto_wallets'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `crypto_wallets` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `crypto_type` enum(''btc'',''eth'',''usdt'',''ltc'',''bch'',''doge'',''other'') NOT NULL,
     `wallet_address` varchar(255) NOT NULL,
     `network` varchar(50) DEFAULT NULL,
     `label` varchar(255) DEFAULT NULL,
     `is_active` tinyint(1) DEFAULT 1,
     `created_by` int(11) DEFAULT NULL,
     `created_at` timestamp NULL DEFAULT current_timestamp(),
     `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
     PRIMARY KEY (`id`),
     KEY `idx_crypto_type` (`crypto_type`),
     KEY `idx_is_active` (`is_active`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* joint accounts */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'account_owners'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `account_owners` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `account_id` int(11) NOT NULL,
     `user_id` int(11) NOT NULL,
     `is_primary` tinyint(1) DEFAULT 0,
     `status` enum(''active'',''pending'',''rejected'',''removed'') DEFAULT ''active'',
     `joined_at` timestamp NULL DEFAULT current_timestamp(),
     `created_at` timestamp NULL DEFAULT current_timestamp(),
     `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
     PRIMARY KEY (`id`),
     UNIQUE KEY `unique_account_user` (`account_id`,`user_id`),
     KEY `idx_account_id` (`account_id`),
     KEY `idx_user_id` (`user_id`),
     KEY `idx_status` (`status`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'joint_account_requests'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `joint_account_requests` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `account_id` int(11) NOT NULL,
     `primary_owner_id` int(11) NOT NULL,
     `requesting_user_id` int(11) NOT NULL,
     `status` enum(''pending'',''approved'',''rejected'',''expired'') DEFAULT ''pending'',
     `requested_at` timestamp NULL DEFAULT current_timestamp(),
     `responded_at` datetime DEFAULT NULL,
     `expires_at` datetime DEFAULT NULL,
     `created_at` timestamp NULL DEFAULT current_timestamp(),
     `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
     PRIMARY KEY (`id`),
     KEY `idx_account_id` (`account_id`),
     KEY `idx_primary_owner_id` (`primary_owner_id`),
     KEY `idx_requesting_user_id` (`requesting_user_id`),
     KEY `idx_status` (`status`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* KYC */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'kyc_verifications'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `kyc_verifications` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `user_id` int(11) NOT NULL,
     `account_type` enum(''individual'',''business'') NOT NULL DEFAULT ''individual'',
     `full_legal_name` varchar(255) DEFAULT NULL,
     `date_of_birth` date DEFAULT NULL,
     `ssn` varchar(255) DEFAULT NULL,
     `residential_address` text DEFAULT NULL,
     `residential_city` varchar(100) DEFAULT NULL,
     `residential_state` varchar(100) DEFAULT NULL,
     `residential_country` varchar(100) DEFAULT NULL,
     `residential_zip` varchar(20) DEFAULT NULL,
     `id_type` varchar(50) DEFAULT NULL,
     `id_number` varchar(255) DEFAULT NULL,
     `id_issued_date` date DEFAULT NULL,
     `id_expiry_date` date DEFAULT NULL,
     `id_issued_state` varchar(100) DEFAULT NULL,
     `id_issued_country` varchar(100) DEFAULT NULL,
     `id_document_front` varchar(255) DEFAULT NULL,
     `id_document_back` varchar(255) DEFAULT NULL,
     `proof_of_address` varchar(255) DEFAULT NULL,
     `signature_image` varchar(255) DEFAULT NULL,
     `business_name` varchar(255) DEFAULT NULL,
     `business_address` text DEFAULT NULL,
     `business_city` varchar(100) DEFAULT NULL,
     `business_state` varchar(100) DEFAULT NULL,
     `business_country` varchar(100) DEFAULT NULL,
     `business_zip` varchar(20) DEFAULT NULL,
     `ein` varchar(255) DEFAULT NULL,
     `business_formation_doc` varchar(255) DEFAULT NULL,
     `source_of_funds` text DEFAULT NULL,
     `account_purpose` text DEFAULT NULL,
     `extra_fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
     `status` enum(''pending'',''under_review'',''verified'',''rejected'',''requires_action'') DEFAULT ''pending'',
     `verified_by` int(11) DEFAULT NULL,
     `verified_at` datetime DEFAULT NULL,
     `rejection_reason` text DEFAULT NULL,
     `admin_notes` text DEFAULT NULL,
     `submitted_at` timestamp NULL DEFAULT current_timestamp(),
     `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
     PRIMARY KEY (`id`),
     KEY `idx_user_id` (`user_id`),
     KEY `idx_status` (`status`),
     KEY `idx_verified_by` (`verified_by`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* admin_audit_logs */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'admin_audit_logs'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `admin_audit_logs` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `admin_id` int(11) NOT NULL,
     `action` varchar(100) NOT NULL,
     `description` text DEFAULT NULL,
     `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
     `ip_address` varchar(45) DEFAULT NULL,
     `created_at` timestamp NULL DEFAULT current_timestamp(),
     PRIMARY KEY (`id`),
     KEY `idx_admin_id` (`admin_id`),
     KEY `idx_created_at` (`created_at`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'kyc_beneficial_owners'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `kyc_beneficial_owners` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `kyc_verification_id` int(11) NOT NULL,
     `first_name` varchar(100) NOT NULL,
     `last_name` varchar(100) NOT NULL,
     `date_of_birth` date DEFAULT NULL,
     `ownership_percentage` decimal(5,2) NOT NULL,
     `id_type` enum(''drivers_license'',''state_id'',''passport'',''military_id'') DEFAULT NULL,
     `id_number` varchar(255) DEFAULT NULL,
     `id_document_front` varchar(255) DEFAULT NULL,
     `id_document_back` varchar(255) DEFAULT NULL,
     `address` text DEFAULT NULL,
     `city` varchar(100) DEFAULT NULL,
     `state` varchar(100) DEFAULT NULL,
     `country` varchar(100) DEFAULT NULL,
     `zip` varchar(20) DEFAULT NULL,
     `created_at` timestamp NULL DEFAULT current_timestamp(),
     `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
     PRIMARY KEY (`id`),
     KEY `idx_kyc_verification_id` (`kyc_verification_id`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* Investment */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'investment_products'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `investment_products` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `title` varchar(255) NOT NULL,
     `slug` varchar(255) NOT NULL,
     `type` enum(''crypto'',''forex'',''stock'',''real_estate'',''commodity'',''other'') DEFAULT ''other'',
     `image_url` text DEFAULT NULL,
     `short_description` text DEFAULT NULL,
     `full_description` longtext DEFAULT NULL,
     `status` enum(''draft'',''active'',''inactive'') DEFAULT ''draft'',
     `min_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
     `max_amount` decimal(15,2) DEFAULT NULL,
     `min_duration_days` int(11) NOT NULL DEFAULT 0,
     `max_duration_days` int(11) DEFAULT NULL,
     `roi_config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
     `payout_type` enum(''compound_daily'',''simple_daily'',''payout_at_maturity'') DEFAULT ''compound_daily'',
     `start_date` date DEFAULT NULL,
     `end_date` date DEFAULT NULL,
     `capacity_total` decimal(15,2) DEFAULT NULL,
     `per_user_max` decimal(15,2) DEFAULT NULL,
     `risk_level` enum(''low'',''medium'',''high'') DEFAULT ''medium'',
     `display_order` int(11) DEFAULT 0,
     `created_by_admin_id` int(11) DEFAULT NULL,
     `created_at` timestamp NULL DEFAULT current_timestamp(),
     `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
     PRIMARY KEY (`id`),
     UNIQUE KEY `slug` (`slug`),
     KEY `idx_status` (`status`),
     KEY `idx_type` (`type`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'user_investments'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `user_investments` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `user_id` int(11) NOT NULL,
     `product_id` int(11) NOT NULL,
     `amount_principal` decimal(15,2) NOT NULL,
     `duration_days` int(11) NOT NULL,
     `start_date` date NOT NULL,
     `maturity_date` date NOT NULL,
     `status` enum(''pending'',''active'',''matured'',''closed'',''cancelled'') DEFAULT ''pending'',
     `daily_percent_effective` decimal(10,6) DEFAULT NULL,
     `current_accrued` decimal(15,2) DEFAULT 0.00,
     `last_accrual_date` date DEFAULT NULL,
     `total_roi_paid` decimal(15,2) DEFAULT 0.00,
     `account_used_id` int(11) NOT NULL,
     `created_at` timestamp NULL DEFAULT current_timestamp(),
     `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
     PRIMARY KEY (`id`),
     KEY `idx_user_id` (`user_id`),
     KEY `idx_product_id` (`product_id`),
     KEY `idx_status` (`status`),
     KEY `idx_maturity_date` (`maturity_date`),
     KEY `idx_last_accrual_date` (`last_accrual_date`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'investment_funding'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `investment_funding` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `user_id` int(11) NOT NULL,
     `amount` decimal(15,2) NOT NULL,
     `status` enum(''pending'',''approved'',''rejected'') DEFAULT ''pending'',
     `funding_method` varchar(50) NOT NULL,
     `crypto_currency` varchar(20) DEFAULT NULL,
     `wallet_id` int(11) DEFAULT NULL,
     `tx_hash` varchar(255) DEFAULT NULL,
     `submitted_at` datetime DEFAULT NULL,
     `reviewed_by` int(11) DEFAULT NULL,
     `reviewed_at` datetime DEFAULT NULL,
     `admin_notes` text DEFAULT NULL,
     `rejection_reason` text DEFAULT NULL,
     `created_at` timestamp NULL DEFAULT current_timestamp(),
     `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
     PRIMARY KEY (`id`),
     KEY `idx_user_id` (`user_id`),
     KEY `idx_status` (`status`),
     KEY `idx_created_at` (`created_at`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* Admin logs (audit) */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'admin_logs'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `admin_logs` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `admin_id` int(11) NOT NULL,
     `user_id` int(11) DEFAULT NULL,
     `action` varchar(100) NOT NULL,
     `description` text DEFAULT NULL,
     `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
     `ip_address` varchar(45) DEFAULT NULL,
     `created_at` timestamp NULL DEFAULT current_timestamp(),
     PRIMARY KEY (`id`),
     KEY `idx_admin_id` (`admin_id`),
     KEY `idx_user_id` (`user_id`),
     KEY `idx_action` (`action`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* system_alerts */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'system_alerts'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `system_alerts` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `alert_type` varchar(50) NOT NULL,
     `title` varchar(255) NOT NULL,
     `message` text NOT NULL,
     `severity` enum(''low'',''medium'',''high'',''critical'') DEFAULT ''low'',
     `related_user_id` int(11) DEFAULT NULL,
     `related_transaction_id` int(11) DEFAULT NULL,
     `is_read` tinyint(1) DEFAULT 0,
     `is_resolved` tinyint(1) DEFAULT 0,
     `resolved_by` int(11) DEFAULT NULL,
     `resolved_at` datetime DEFAULT NULL,
     `created_at` timestamp NULL DEFAULT current_timestamp(),
     PRIMARY KEY (`id`),
     KEY `idx_alert_type` (`alert_type`),
     KEY `idx_severity` (`severity`),
     KEY `idx_is_read` (`is_read`),
     KEY `idx_created_at` (`created_at`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* Version control / migrations tracking */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'schema_migrations'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `schema_migrations` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `version` varchar(20) NOT NULL,
     `migration_name` varchar(255) NOT NULL,
     `migration_file` varchar(255) NOT NULL,
     `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
     `applied_by` int(11) DEFAULT NULL,
     `status` enum(''success'',''failed'',''skipped'') NOT NULL DEFAULT ''success'',
     `error_message` text DEFAULT NULL,
     PRIMARY KEY (`id`),
     UNIQUE KEY `unique_migration` (`version`,`migration_name`),
     KEY `idx_version` (`version`),
     KEY `idx_applied_at` (`applied_at`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'system_versions'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `system_versions` (
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
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* -------------------------------------------------------------------------- */
/* 2b) Additional feature tables (create if missing)                           */
/* -------------------------------------------------------------------------- */

/* admin_sessions */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'admin_sessions'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `admin_sessions` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `admin_id` int(11) NOT NULL,
     `session_token` varchar(255) NOT NULL,
     `ip_address` varchar(45) DEFAULT NULL,
     `user_agent` text DEFAULT NULL,
     `last_activity` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
     `expires_at` datetime NOT NULL,
     `created_at` timestamp NULL DEFAULT current_timestamp(),
     PRIMARY KEY (`id`),
     KEY `idx_admin_id` (`admin_id`),
     KEY `idx_expires_at` (`expires_at`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* login_attempts */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'login_attempts'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `login_attempts` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `email` varchar(255) NOT NULL,
     `ip_address` varchar(45) DEFAULT NULL,
     `attempted_at` timestamp NULL DEFAULT current_timestamp(),
     PRIMARY KEY (`id`),
     KEY `idx_email` (`email`),
     KEY `idx_attempted_at` (`attempted_at`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* beneficiaries */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'beneficiaries'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `beneficiaries` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `user_id` int(11) NOT NULL,
     `beneficiary_name` varchar(255) NOT NULL,
     `account_number` varchar(50) NOT NULL,
     `bank_name` varchar(255) DEFAULT NULL,
     `bank_code` varchar(50) DEFAULT NULL,
     `swift_code` varchar(20) DEFAULT NULL,
     `country` varchar(100) DEFAULT NULL,
     `currency` varchar(10) DEFAULT ''USD'',
     `nickname` varchar(100) DEFAULT NULL,
     `beneficiary_type` enum(''domestic'',''international'') DEFAULT ''domestic'',
     `is_verified` tinyint(1) DEFAULT 0,
     `created_at` timestamp NULL DEFAULT current_timestamp(),
     `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
     PRIMARY KEY (`id`),
     KEY `idx_user_id` (`user_id`),
     KEY `idx_created_at` (`created_at`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* bill_payments */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'bill_payments'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `bill_payments` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `user_id` int(11) NOT NULL,
     `account_id` int(11) NOT NULL,
     `biller_name` varchar(255) NOT NULL,
     `biller_category` enum(''utilities'',''phone'',''internet'',''insurance'',''credit_card'',''other'') NOT NULL,
     `account_number` varchar(100) DEFAULT NULL,
     `amount` decimal(15,2) NOT NULL,
     `currency` varchar(10) DEFAULT ''USD'',
     `payment_date` date NOT NULL,
     `is_recurring` tinyint(1) DEFAULT 0,
     `recurring_frequency` enum(''weekly'',''monthly'',''quarterly'',''yearly'') DEFAULT NULL,
     `next_payment_date` date DEFAULT NULL,
     `status` enum(''scheduled'',''processing'',''paid'',''failed'',''cancelled'') DEFAULT ''scheduled'',
     `transaction_ref` varchar(50) DEFAULT NULL,
     `created_at` timestamp NULL DEFAULT current_timestamp(),
     `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
     PRIMARY KEY (`id`),
     KEY `idx_user_id` (`user_id`),
     KEY `idx_account_id` (`account_id`),
     KEY `idx_status` (`status`),
     KEY `idx_payment_date` (`payment_date`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* investment_transactions */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'investment_transactions'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `investment_transactions` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `user_investment_id` int(11) DEFAULT NULL,
     `user_id` int(11) NOT NULL,
     `type` enum(''deposit'',''debit'',''payout'',''accrual'',''admin_adjustment'',''refund'') NOT NULL,
     `amount` decimal(15,2) NOT NULL,
     `balance_before` decimal(15,2) DEFAULT NULL,
     `balance_after` decimal(15,2) DEFAULT NULL,
     `reference` varchar(255) DEFAULT NULL,
     `description` text DEFAULT NULL,
     `created_at` timestamp NULL DEFAULT current_timestamp(),
     PRIMARY KEY (`id`),
     KEY `idx_user_id` (`user_id`),
     KEY `idx_user_investment_id` (`user_investment_id`),
     KEY `idx_created_at` (`created_at`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* investment_withdrawals */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'investment_withdrawals'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `investment_withdrawals` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `user_id` int(11) NOT NULL,
     `amount` decimal(15,2) NOT NULL,
     `withdrawal_method` enum(''bank_balance'',''external_account'',''paypal'',''venmo'',''crypto_btc'',''crypto_eth'',''crypto_usdt'',''crypto_ltc'',''crypto_other'') NOT NULL,
     `recipient_type` enum(''bank_account'',''paypal_email'',''venmo_phone'',''crypto_address'') DEFAULT NULL,
     `recipient_info` text DEFAULT NULL,
     `status` enum(''pending'',''processing'',''completed'',''failed'',''cancelled'',''rejected'') DEFAULT ''pending'',
     `processed_at` datetime DEFAULT NULL,
     `processed_by` int(11) DEFAULT NULL,
     `rejection_reason` text DEFAULT NULL,
     `transaction_ref` varchar(50) DEFAULT NULL,
     `notes` text DEFAULT NULL,
     `created_at` timestamp NULL DEFAULT current_timestamp(),
     `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
     PRIMARY KEY (`id`),
     KEY `idx_user_id` (`user_id`),
     KEY `idx_status` (`status`),
     KEY `idx_created_at` (`created_at`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* ip_access_control */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'ip_access_control'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `ip_access_control` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `ip_address` varchar(45) NOT NULL,
     `type` enum(''whitelist'',''blacklist'') NOT NULL,
     `reason` text DEFAULT NULL,
     `created_by` int(11) DEFAULT NULL,
     `created_at` timestamp NULL DEFAULT current_timestamp(),
     `expires_at` datetime DEFAULT NULL,
     PRIMARY KEY (`id`),
     KEY `idx_ip_address` (`ip_address`),
     KEY `idx_type` (`type`),
     KEY `idx_expires_at` (`expires_at`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* email_simulation_alert_captions */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'email_simulation_alert_captions'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `email_simulation_alert_captions` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `caption_text` varchar(255) NOT NULL,
     `is_active` tinyint(1) DEFAULT 1,
     `created_at` timestamp NULL DEFAULT current_timestamp(),
     `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
     PRIMARY KEY (`id`),
     KEY `idx_is_active` (`is_active`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* email_simulation_templates */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'email_simulation_templates'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `email_simulation_templates` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `template_name` varchar(100) NOT NULL,
     `template_type` enum(''simple'',''advanced'') DEFAULT ''simple'',
     `primary_color` varchar(7) DEFAULT ''#359eb4'',
     `secondary_color` varchar(7) DEFAULT ''#2a7e90'',
     `accent_color` varchar(7) DEFAULT ''#10b981'',
     `logo_url` varchar(500) DEFAULT NULL,
     `logo_alt_text` varchar(255) DEFAULT ''Bank Logo'',
     `address` varchar(500) DEFAULT NULL,
     `is_active` tinyint(1) DEFAULT 1,
     `created_at` timestamp NULL DEFAULT current_timestamp(),
     `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
     PRIMARY KEY (`id`),
     KEY `idx_is_active` (`is_active`),
     KEY `idx_template_type` (`template_type`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* support_tickets */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'support_tickets'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `support_tickets` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `user_id` int(11) NOT NULL,
     `ticket_number` varchar(50) NOT NULL,
     `subject` varchar(255) NOT NULL,
     `category` enum(''account'',''transaction'',''card'',''loan'',''technical'',''other'') NOT NULL,
     `priority` enum(''low'',''medium'',''high'',''urgent'') DEFAULT ''medium'',
     `status` enum(''open'',''pending'',''resolved'',''closed'') DEFAULT ''open'',
     `assigned_to` int(11) DEFAULT NULL,
     `created_at` timestamp NULL DEFAULT current_timestamp(),
     `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
     `resolved_at` datetime DEFAULT NULL,
     PRIMARY KEY (`id`),
     UNIQUE KEY `ticket_number` (`ticket_number`),
     KEY `idx_user_id` (`user_id`),
     KEY `idx_status` (`status`),
     KEY `idx_created_at` (`created_at`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* support_messages */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'support_messages'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `support_messages` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `ticket_id` int(11) NOT NULL,
     `user_id` int(11) NOT NULL,
     `message` text NOT NULL,
     `is_staff_reply` tinyint(1) DEFAULT 0,
     `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
     `created_at` timestamp NULL DEFAULT current_timestamp(),
     PRIMARY KEY (`id`),
     KEY `idx_ticket_id` (`ticket_id`),
     KEY `idx_user_id` (`user_id`),
     KEY `idx_created_at` (`created_at`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* user_notes */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'user_notes'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `user_notes` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `user_id` int(11) NOT NULL,
     `admin_id` int(11) NOT NULL,
     `note` text NOT NULL,
     `created_at` timestamp NULL DEFAULT current_timestamp(),
     PRIMARY KEY (`id`),
     KEY `idx_user_id` (`user_id`),
     KEY `idx_admin_id` (`admin_id`),
     KEY `idx_created_at` (`created_at`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'system_version_info'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `system_version_info` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `current_version` varchar(20) NOT NULL,
     `database_version` varchar(20) NOT NULL,
     `last_updated` datetime NOT NULL,
     `updated_by` int(11) DEFAULT NULL,
     PRIMARY KEY (`id`),
     UNIQUE KEY `unique_info` (`id`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'update_logs'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `update_logs` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `version` varchar(20) NOT NULL,
     `applied_date` datetime NOT NULL,
     `applied_by` int(11) DEFAULT NULL,
     `status` enum(''success'',''failed'',''partial'') NOT NULL DEFAULT ''success'',
     `log_details` text DEFAULT NULL,
     `files_updated` int(11) DEFAULT 0,
     `migrations_applied` int(11) DEFAULT 0,
     `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
     PRIMARY KEY (`id`),
     KEY `idx_version` (`version`),
     KEY `idx_applied_date` (`applied_date`),
     KEY `idx_status` (`status`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* -------------------------------------------------------------------------- */
/* 3) Add missing columns (safe)                                              */
/* -------------------------------------------------------------------------- */

/* users.currency_selection_shown */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema = @db AND table_name = 'users' AND column_name = 'currency_selection_shown'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `users` ADD COLUMN `currency_selection_shown` tinyint(1) DEFAULT 0;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* users.transaction_override */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema = @db AND table_name = 'users' AND column_name = 'transaction_override'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `users` ADD COLUMN `transaction_override` varchar(20) DEFAULT ''normal'' COMMENT ''Admin override for transaction processing: normal, force_success, force_pending, force_failed'';',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* users.currency */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema = @db AND table_name = 'users' AND column_name = 'currency'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `users` ADD COLUMN `currency` varchar(10) DEFAULT ''USD'';',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* users.kyc_prompt_dismissed */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema = @db AND table_name = 'users' AND column_name = 'kyc_prompt_dismissed'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `users` ADD COLUMN `kyc_prompt_dismissed` tinyint(1) NOT NULL DEFAULT 0 AFTER `kyc_status`;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* users.kyc_submitted_at */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema = @db AND table_name = 'users' AND column_name = 'kyc_submitted_at'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `users` ADD COLUMN `kyc_submitted_at` datetime DEFAULT NULL COMMENT ''Timestamp when user submitted KYC verification'';',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* users.transfer_otp_required */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema = @db AND table_name = 'users' AND column_name = 'transfer_otp_required'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `users` ADD COLUMN `transfer_otp_required` tinyint(1) DEFAULT 1;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* users.imf_code */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema = @db AND table_name = 'users' AND column_name = 'imf_code'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `users` ADD COLUMN `imf_code` varchar(20) DEFAULT NULL;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* users.imf_required */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema = @db AND table_name = 'users' AND column_name = 'imf_required'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `users` ADD COLUMN `imf_required` tinyint(1) DEFAULT 0;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* users.federal_swift_code */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema = @db AND table_name = 'users' AND column_name = 'federal_swift_code'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `users` ADD COLUMN `federal_swift_code` varchar(20) DEFAULT NULL;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* users.federal_swift_required */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema = @db AND table_name = 'users' AND column_name = 'federal_swift_required'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `users` ADD COLUMN `federal_swift_required` tinyint(1) DEFAULT 0;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* users.vat_code */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema = @db AND table_name = 'users' AND column_name = 'vat_code'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `users` ADD COLUMN `vat_code` varchar(20) DEFAULT NULL;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* users.vat_required */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema = @db AND table_name = 'users' AND column_name = 'vat_required'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `users` ADD COLUMN `vat_required` tinyint(1) DEFAULT 0;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* users.tac_code */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema = @db AND table_name = 'users' AND column_name = 'tac_code'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `users` ADD COLUMN `tac_code` varchar(20) DEFAULT NULL;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* users.tac_required */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema = @db AND table_name = 'users' AND column_name = 'tac_required'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `users` ADD COLUMN `tac_required` tinyint(1) DEFAULT 0;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* users.tin_code */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema = @db AND table_name = 'users' AND column_name = 'tin_code'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `users` ADD COLUMN `tin_code` varchar(20) DEFAULT NULL;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* users.tin_required */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema = @db AND table_name = 'users' AND column_name = 'tin_required'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `users` ADD COLUMN `tin_required` tinyint(1) DEFAULT 0;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* accounts.currency */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema = @db AND table_name = 'accounts' AND column_name = 'currency'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `accounts` ADD COLUMN `currency` varchar(10) DEFAULT ''USD'';',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* transactions.currency */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema = @db AND table_name = 'transactions' AND column_name = 'currency'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `transactions` ADD COLUMN `currency` varchar(10) DEFAULT ''USD'';',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* transactions.payment_method */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema = @db AND table_name = 'transactions' AND column_name = 'payment_method'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `transactions` ADD COLUMN `payment_method` varchar(50) DEFAULT NULL AFTER `status`;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* transactions.fee */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema = @db AND table_name = 'transactions' AND column_name = 'fee'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `transactions` ADD COLUMN `fee` decimal(10,2) DEFAULT 0.00 AFTER `payment_method`;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* transactions.exchange_rate */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema = @db AND table_name = 'transactions' AND column_name = 'exchange_rate'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `transactions` ADD COLUMN `exchange_rate` decimal(10,4) DEFAULT NULL AFTER `fee`;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* transactions.metadata */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema = @db AND table_name = 'transactions' AND column_name = 'metadata'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `transactions` ADD COLUMN `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL AFTER `exchange_rate`;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* kyc_verifications.extra_fields */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema = @db AND table_name = 'kyc_verifications' AND column_name = 'extra_fields'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `kyc_verifications` ADD COLUMN `extra_fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL AFTER `account_purpose`;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* kyc_verifications.id_type — expand US-only enum to varchar for country profiles */
SET @colType := (
  SELECT COLUMN_TYPE FROM information_schema.columns
  WHERE table_schema = @db AND table_name = 'kyc_verifications' AND column_name = 'id_type'
  LIMIT 1
);
SET @sql := IF(@colType LIKE 'enum%',
  'ALTER TABLE `kyc_verifications` MODIFY COLUMN `id_type` varchar(50) DEFAULT NULL;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* two_factor_codes.purpose */
SET @exists := (
  SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema = @db AND table_name = 'two_factor_codes' AND column_name = 'purpose'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE `two_factor_codes` ADD COLUMN `purpose` varchar(20) NOT NULL DEFAULT ''login'';',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* -------------------------------------------------------------------------- */
/* 4) Critical uniques/indexes (only if safe to apply)                         */
/* -------------------------------------------------------------------------- */

/* system_settings.setting_key unique (skip if duplicates exist) */
SET @has_unique := (
  SELECT COUNT(*) FROM information_schema.statistics
  WHERE table_schema = @db AND table_name = 'system_settings' AND index_name = 'setting_key'
);
SET @dupes := (
  SELECT COUNT(*) FROM (
    SELECT setting_key FROM system_settings GROUP BY setting_key HAVING COUNT(*) > 1
  ) d
);
SET @sql := IF(@has_unique = 0 AND @dupes = 0,
  'ALTER TABLE `system_settings` ADD UNIQUE KEY `setting_key` (`setting_key`);',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* transactions.transaction_ref unique (skip if duplicates exist) */
SET @has_unique := (
  SELECT COUNT(*) FROM information_schema.statistics
  WHERE table_schema = @db AND table_name = 'transactions' AND index_name = 'transaction_ref'
);
SET @dupes := (
  SELECT COUNT(*) FROM (
    SELECT transaction_ref FROM transactions GROUP BY transaction_ref HAVING COUNT(*) > 1
  ) d
);
SET @sql := IF(@has_unique = 0 AND @dupes = 0,
  'ALTER TABLE `transactions` ADD UNIQUE KEY `transaction_ref` (`transaction_ref`);',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* users.email unique (skip if duplicates exist) */
SET @has_unique := (
  SELECT COUNT(*) FROM information_schema.statistics
  WHERE table_schema = @db AND table_name = 'users' AND index_name = 'email'
);
SET @dupes := (
  SELECT COUNT(*) FROM (
    SELECT email FROM users GROUP BY email HAVING COUNT(*) > 1
  ) d
);
SET @sql := IF(@has_unique = 0 AND @dupes = 0,
  'ALTER TABLE `users` ADD UNIQUE KEY `email` (`email`);',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* -------------------------------------------------------------------------- */
/* 5) Seed required system_settings keys (no overwrite)                         */
/* -------------------------------------------------------------------------- */

INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`, `created_at`, `updated_at`)
SELECT 'default_currency', 'USD', 'string', 'Default system currency', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `system_settings` WHERE `setting_key` = 'default_currency');

INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`, `created_at`, `updated_at`)
SELECT 'enable_currency_conversion', '1', 'boolean',
       'Enable currency conversion. When enabled, users can view balances and amounts in their preferred currency. When disabled, all amounts are displayed in the site default currency.',
       NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `system_settings` WHERE `setting_key` = 'enable_currency_conversion');

INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`, `created_at`, `updated_at`)
SELECT 'bank_operating_country', 'United States', 'string', 'Country where the bank operates', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `system_settings` WHERE `setting_key` = 'bank_operating_country');

INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`, `created_at`, `updated_at`)
SELECT 'bank_operating_region', 'north-america', 'string', 'Region where the bank operates', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `system_settings` WHERE `setting_key` = 'bank_operating_region');

INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`, `created_at`, `updated_at`)
SELECT 'transfer_internal_fee', '0', 'number', 'Internal transfer fee percentage', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `system_settings` WHERE `setting_key` = 'transfer_internal_fee');

INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`, `created_at`, `updated_at`)
SELECT 'transfer_domestic_fee', '0.5', 'number', 'Domestic transfer fee percentage', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `system_settings` WHERE `setting_key` = 'transfer_domestic_fee');

INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`, `created_at`, `updated_at`)
SELECT 'transfer_international_fee', '2.5', 'number', 'International transfer fee percentage', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `system_settings` WHERE `setting_key` = 'transfer_international_fee');

INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`, `created_at`, `updated_at`)
SELECT 'allow_new_registrations', '1', 'boolean', 'Allow new user registrations. When disabled, users cannot create new accounts.', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `system_settings` WHERE `setting_key` = 'allow_new_registrations');

INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`, `created_at`, `updated_at`)
SELECT 'maintenance_mode', '0', 'boolean', 'Enable maintenance mode. When enabled, only admins can access the site.', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `system_settings` WHERE `setting_key` = 'maintenance_mode');

INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`, `created_at`, `updated_at`)
SELECT 'require_kyc', '1', 'boolean', 'Require KYC verification for all users.', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `system_settings` WHERE `setting_key` = 'require_kyc');

INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`, `created_at`, `updated_at`)
SELECT 'two_factor_required', '0', 'boolean', 'Force 2FA for all users.', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `system_settings` WHERE `setting_key` = 'two_factor_required');

INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`, `created_at`, `updated_at`)
SELECT 'disable_2fa_entirely', '0', 'boolean', 'Disable 2FA entirely for all users.', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `system_settings` WHERE `setting_key` = 'disable_2fa_entirely');

INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`, `created_at`, `updated_at`)
SELECT 'require_transfer_pin', '1', 'boolean', 'Require Transfer PIN for transactions.', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `system_settings` WHERE `setting_key` = 'require_transfer_pin');

INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`, `created_at`, `updated_at`)
SELECT 'max_login_attempts', '3', 'number', 'Maximum failed login attempts before lockout.', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `system_settings` WHERE `setting_key` = 'max_login_attempts');

INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`, `created_at`, `updated_at`)
SELECT 'login_lockout_duration', '15', 'number', 'Lockout duration in minutes after exceeding max login attempts.', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `system_settings` WHERE `setting_key` = 'login_lockout_duration');

INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`, `created_at`, `updated_at`)
SELECT 'email_on_login', '0', 'boolean', 'Send email notification on login.', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `system_settings` WHERE `setting_key` = 'email_on_login');

INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`, `created_at`, `updated_at`)
SELECT 'force_security_setup', '1', 'boolean', 'When enabled, users must complete Login PIN and Transfer PIN (+ 2FA if required) before accessing the dashboard', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `system_settings` WHERE `setting_key` = 'force_security_setup');

INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`, `created_at`, `updated_at`)
SELECT 'kyc_use_custom_fields', '0', 'boolean', 'Use custom admin-defined KYC fields instead of country profile defaults', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `system_settings` WHERE `setting_key` = 'kyc_use_custom_fields');

INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`, `created_at`, `updated_at`)
SELECT 'kyc_custom_fields', '[]', 'json', 'JSON array of custom KYC field definitions when kyc_use_custom_fields is enabled', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `system_settings` WHERE `setting_key` = 'kyc_custom_fields');

/* -------------------------------------------------------------------------- */
/* 6) Site default currency + ledger alignment (idempotent, uses settings)    */
/* -------------------------------------------------------------------------- */

/* Normalize legacy default_currency values from old migrations (CAD/GBP → USD) */
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

/* Detect legacy ledger currency before any balance conversion */
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

/* Users who never chose a display currency → site default */
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

/* Accounts: convert balances to site default using exchange_rates (direct pair) */
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

/* Accounts: inverse rate when only site_default → legacy exists */
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

/* Transactions */
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

/* Loans (no currency column — scale by legacy → site_default rate) */
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

/* Cards (limits stored in legacy ledger units) */
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

/* Investment balance on users */
SET @sql := IF(@ledger_aligned = 0 AND @loan_rate <> 1,
  CONCAT(
    'UPDATE `users` SET `investment_balance` = ROUND(IFNULL(`investment_balance`, 0) * ', @loan_rate, ', 2) ',
    'WHERE `investment_balance` IS NOT NULL AND `investment_balance` > 0'
  ),
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* user_investments */
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

/* exchange_rates: ensure unique (from_currency, to_currency) for ON DUPLICATE KEY UPDATE */
SET @has_unique_pair := (
  SELECT COUNT(*) FROM information_schema.statistics
  WHERE table_schema = @db AND table_name = 'exchange_rates' AND index_name = 'unique_pair'
);
SET @sql := IF(@has_unique_pair = 0,
  'ALTER TABLE `exchange_rates` ADD UNIQUE KEY `unique_pair` (`from_currency`,`to_currency`);',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* -------------------------------------------------------------------------- */
/* 7) Transaction history generator (templates + batches)                     */
/* -------------------------------------------------------------------------- */

SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'transaction_templates'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `transaction_templates` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `slug` varchar(100) NOT NULL,
     `name` varchar(255) NOT NULL,
     `account_type` varchar(50) DEFAULT ''checking'',
     `description` text DEFAULT NULL,
     `is_active` tinyint(1) NOT NULL DEFAULT 1,
     `created_at` timestamp NULL DEFAULT current_timestamp(),
     `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
     PRIMARY KEY (`id`),
     UNIQUE KEY `slug` (`slug`),
     KEY `idx_account_type` (`account_type`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'transaction_template_items'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `transaction_template_items` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `template_id` int(11) NOT NULL,
     `sort_order` int(11) NOT NULL DEFAULT 0,
     `transaction_type` enum(''debit'',''credit'') NOT NULL,
     `category` enum(''transfer'',''payment'',''deposit'',''withdrawal'',''fee'',''interest'',''loan'',''card'',''other'') NOT NULL,
     `expense_category` enum(''shopping'',''food'',''transport'',''bills'',''entertainment'',''healthcare'',''travel'',''education'',''salary'',''investment'',''rent'',''insurance'',''gift'',''personal'',''other'',''bonus'',''refund'',''utility'') DEFAULT NULL,
     `base_amount` decimal(15,2) NOT NULL,
     `description` text DEFAULT NULL,
     `recipient_account` varchar(255) DEFAULT NULL,
     `recipient_name` varchar(255) DEFAULT NULL,
     `recipient_bank` varchar(255) DEFAULT NULL,
     `status` enum(''pending'',''processing'',''completed'',''failed'',''reversed'') NOT NULL DEFAULT ''completed'',
     `fee` decimal(10,2) NOT NULL DEFAULT 0.00,
     `weight` int(11) NOT NULL DEFAULT 1,
     PRIMARY KEY (`id`),
     KEY `idx_template_sort` (`template_id`,`sort_order`),
     CONSTRAINT `fk_template_items_template` FOREIGN KEY (`template_id`) REFERENCES `transaction_templates` (`id`) ON DELETE CASCADE
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'transaction_generation_batches'
);
SET @sql := IF(@exists = 0,
  'CREATE TABLE `transaction_generation_batches` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `batch_id` varchar(64) NOT NULL,
     `idempotency_key` varchar(128) NOT NULL,
     `params_hash` char(64) NOT NULL,
     `admin_id` int(11) NOT NULL,
     `user_id` int(11) NOT NULL,
     `account_id` int(11) NOT NULL,
     `template_id` int(11) NOT NULL,
     `engine_params` json DEFAULT NULL,
     `plan_summary` json DEFAULT NULL,
     `density` enum(''light'',''normal'',''heavy'') NOT NULL DEFAULT ''normal'',
     `start_date` date NOT NULL,
     `end_date` date NOT NULL,
     `previous_balance` decimal(15,2) NOT NULL,
     `history_impact` decimal(15,2) NOT NULL,
     `target_final_balance` decimal(15,2) NOT NULL,
     `opening_balance` decimal(15,2) NOT NULL,
     `transaction_count` int(11) NOT NULL DEFAULT 0,
     `replaced_previous` tinyint(1) NOT NULL DEFAULT 0,
     `status` enum(''completed'',''undone'') NOT NULL DEFAULT ''completed'',
     `created_at` timestamp NULL DEFAULT current_timestamp(),
     `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
     PRIMARY KEY (`id`),
     UNIQUE KEY `batch_id` (`batch_id`),
     UNIQUE KEY `idempotency_key` (`idempotency_key`),
     KEY `idx_params_hash` (`params_hash`),
     KEY `idx_account_status` (`account_id`,`status`),
     KEY `idx_user_account` (`user_id`,`account_id`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* -------------------------------------------------------------------------- */
/* 7b) expense_category enum — transfer UI + generator parity (idempotent)    */
/* -------------------------------------------------------------------------- */

SET @col := (
  SELECT COLUMN_TYPE FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'transactions' AND COLUMN_NAME = 'expense_category'
  LIMIT 1
);
SET @sql := IF(@col IS NOT NULL AND @col NOT LIKE '%bonus%',
  'ALTER TABLE `transactions` MODIFY COLUMN `expense_category` enum(
    ''shopping'',''food'',''transport'',''bills'',''entertainment'',''healthcare'',''travel'',
    ''education'',''salary'',''investment'',''rent'',''insurance'',''gift'',''personal'',''other'',
    ''bonus'',''refund'',''utility''
  ) DEFAULT NULL',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col2 := (
  SELECT COLUMN_TYPE FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'transaction_template_items' AND COLUMN_NAME = 'expense_category'
  LIMIT 1
);
SET @sql := IF(@col2 IS NOT NULL AND @col2 NOT LIKE '%bonus%',
  'ALTER TABLE `transaction_template_items` MODIFY COLUMN `expense_category` enum(
    ''shopping'',''food'',''transport'',''bills'',''entertainment'',''healthcare'',''travel'',
    ''education'',''salary'',''investment'',''rent'',''insurance'',''gift'',''personal'',''other'',
    ''bonus'',''refund'',''utility''
  ) DEFAULT NULL',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* -------------------------------------------------------------------------- */
/* 7c) Generation batch audit columns (engine plan snapshot)                */
/* -------------------------------------------------------------------------- */

SET @has_batches := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'transaction_generation_batches'
);
SET @has_engine_params := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'transaction_generation_batches' AND COLUMN_NAME = 'engine_params'
);
SET @sql := IF(@has_batches > 0 AND @has_engine_params = 0,
  'ALTER TABLE `transaction_generation_batches` ADD COLUMN `engine_params` JSON DEFAULT NULL AFTER `template_id`',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @has_plan_summary := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'transaction_generation_batches' AND COLUMN_NAME = 'plan_summary'
);
SET @sql := IF(@has_batches > 0 AND @has_plan_summary = 0,
  'ALTER TABLE `transaction_generation_batches` ADD COLUMN `plan_summary` JSON DEFAULT NULL AFTER `engine_params`',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

/* -------------------------------------------------------------------------- */
/* 8) Seed default transaction template (Andy reference pack, idempotent)   */
/* -------------------------------------------------------------------------- */

SET @has_tpl_table := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'transaction_templates'
);
SET @tpl_exists := IF(@has_tpl_table > 0,
  (SELECT COUNT(*) FROM transaction_templates WHERE slug = 'default_checking'),
  0
);

INSERT INTO transaction_templates (slug, name, account_type, description, is_active)
SELECT 'default_checking', 'Default Checking History', 'checking',
       'Realistic mixed credit/debit history derived from Andy seed reference pack.',
       1
WHERE @has_tpl_table > 0 AND @tpl_exists = 0;

SET @template_id := IF(@has_tpl_table > 0,
  (SELECT id FROM transaction_templates WHERE slug = 'default_checking' LIMIT 1),
  NULL
);

INSERT INTO transaction_template_items (
  template_id, sort_order, transaction_type, category, expense_category,
  base_amount, description, recipient_account, recipient_name, recipient_bank,
  status, fee, weight
)
SELECT @template_id, sort_order, transaction_type, category, expense_category,
       base_amount, description, recipient_account, recipient_name, recipient_bank,
       status, fee, weight
FROM (
  SELECT 1 AS sort_order, 'credit' AS transaction_type, 'deposit' AS category, 'salary' AS expense_category, 2350000.00 AS base_amount, 'Transfer from Salary Payment – ACADEMI PMC at wells Fargo' AS description, '44182937' AS recipient_account, 'Salary Payment – ACADEMI PMC' AS recipient_name, 'wells Fargo' AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 2 AS sort_order, 'debit' AS transaction_type, 'withdrawal' AS category, NULL AS expense_category, 7000000.00 AS base_amount, 'Domestic Transfer to pascal paul at citi bank' AS description, '22353563' AS recipient_account, 'pascal paul' AS recipient_name, 'citi bank' AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 3 AS sort_order, 'debit' AS transaction_type, 'withdrawal' AS category, NULL AS expense_category, 185000.00 AS base_amount, 'International Transfer to James Thornton at HSBC UK' AS description, 'GB72HBUK40127612345678' AS recipient_account, 'James Thornton' AS recipient_name, 'HSBC UK' AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 4 AS sort_order, 'debit' AS transaction_type, 'withdrawal' AS category, NULL AS expense_category, 27500.00 AS base_amount, 'Domestic Transfer to Michael Rodriguez at Chase Bank' AS description, '463817492' AS recipient_account, 'Michael Rodriguez' AS recipient_name, 'Chase Bank' AS recipient_bank, 'failed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 5 AS sort_order, 'debit' AS transaction_type, 'withdrawal' AS category, NULL AS expense_category, 27500.00 AS base_amount, 'Domestic Transfer to Michael Rodriguez at Chase Bank' AS description, '463817492' AS recipient_account, 'Michael Rodriguez' AS recipient_name, 'Chase Bank' AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 6 AS sort_order, 'credit' AS transaction_type, 'deposit' AS category, NULL AS expense_category, 9842.00 AS base_amount, 'IRS Tax Refund Adjustment' AS description, '009283514' AS recipient_account, 'Internal Revenue Service' AS recipient_name, 'U.S. Treasury Department' AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 7 AS sort_order, 'debit' AS transaction_type, 'withdrawal' AS category, NULL AS expense_category, 62900.00 AS base_amount, 'International Transfer to Cobus Van Der West at Standard Bank South Africa' AS description, '128476395' AS recipient_account, 'Cobus Van Der West' AS recipient_name, 'Standard Bank South Africa' AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 8 AS sort_order, 'debit' AS transaction_type, 'withdrawal' AS category, NULL AS expense_category, 3120.00 AS base_amount, 'Domestic Transfer to Amazon Web Services at JPMorgan Payments' AS description, '875341209' AS recipient_account, 'Amazon Web Services' AS recipient_name, 'JPMorgan Payments' AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 9 AS sort_order, 'credit' AS transaction_type, 'deposit' AS category, NULL AS expense_category, 2350000.00 AS base_amount, 'Transfer from ACADEMI PMC at wells Fargo' AS description, '4418293723' AS recipient_account, 'ACADEMI PMC' AS recipient_name, 'wells Fargo' AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 10 AS sort_order, 'credit' AS transaction_type, 'deposit' AS category, NULL AS expense_category, 2350000.00 AS base_amount, 'Transfer from ACADEMI PMC at wells Fargo' AS description, '4418293723' AS recipient_account, 'ACADEMI PMC' AS recipient_name, 'wells Fargo' AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 11 AS sort_order, 'debit' AS transaction_type, 'transfer' AS category, NULL AS expense_category, 8492.25 AS base_amount, 'Domestic Transfer to Matts Anderson at Wells Fargo Bank' AS description, '6272883838' AS recipient_account, 'Matts Anderson' AS recipient_name, 'Wells Fargo Bank' AS recipient_bank, 'completed' AS status, 42.25 AS fee, 1 AS weight
  UNION ALL
  SELECT 12 AS sort_order, 'debit' AS transaction_type, 'withdrawal' AS category, NULL AS expense_category, 35700.00 AS base_amount, 'Domestic Transfer to James Thornton at HSBC UK' AS description, '3647687970809' AS recipient_account, 'James Thornton' AS recipient_name, 'HSBC UK' AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 13 AS sort_order, 'debit' AS transaction_type, 'transfer' AS category, NULL AS expense_category, 4600.00 AS base_amount, 'Domestic Transfer to Leave@academi at JPMorgan Chase Bank' AS description, '26273741639' AS recipient_account, 'Leave@academi' AS recipient_name, 'JPMorgan Chase Bank' AS recipient_bank, 'failed' AS status, 22.96 AS fee, 1 AS weight
  UNION ALL
  SELECT 14 AS sort_order, 'credit' AS transaction_type, 'deposit' AS category, 'insurance' AS expense_category, 7097129.00 AS base_amount, 'Transfer from Titan Core Assets Group LLC at wells Fargo' AS description, '4418293723' AS recipient_account, 'Titan Core Assets Group LLC' AS recipient_name, 'wells Fargo' AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 15 AS sort_order, 'debit' AS transaction_type, 'withdrawal' AS category, NULL AS expense_category, 9100.00 AS base_amount, 'Domestic Transfer to Academi@Admin at JPMorgan Chase Bank' AS description, '868746356795' AS recipient_account, 'Academi@Admin' AS recipient_name, 'JPMorgan Chase Bank' AS recipient_bank, 'failed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 16 AS sort_order, 'debit' AS transaction_type, 'withdrawal' AS category, NULL AS expense_category, 2300.00 AS base_amount, 'Card payment to Academi@Clinic' AS description, '868746356795' AS recipient_account, 'Academi@Clinic' AS recipient_name, 'JPMorgan Chase Bank' AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 17 AS sort_order, 'debit' AS transaction_type, 'withdrawal' AS category, NULL AS expense_category, 49500.00 AS base_amount, 'Domestic Transfer to Wright Caleb at wells Fargo' AS description, 'US-CH-77451092' AS recipient_account, 'Wright Caleb' AS recipient_name, 'wells Fargo' AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 18 AS sort_order, 'credit' AS transaction_type, 'transfer' AS category, 'other' AS expense_category, 27150.00 AS base_amount, 'BKK Gesund – health allowance Q3 2023' AS description, NULL AS recipient_account, 'BKK Gesund' AS recipient_name, 'DZ Bank Ndl. Frankfurt' AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 19 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'bills' AS expense_category, 17.30 AS base_amount, 'Telekom Deutschland – Oct 2023 invoice' AS description, NULL AS recipient_account, 'Telekom Deutschland GmbH' AS recipient_name, NULL AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 20 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'shopping' AS expense_category, 550.00 AS base_amount, 'Nike.com e-gift card order' AS description, NULL AS recipient_account, 'Nike E-Commerce' AS recipient_name, NULL AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 21 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'shopping' AS expense_category, 182.00 AS base_amount, 'Shopify store – online purchase' AS description, NULL AS recipient_account, 'Shopify Payments' AS recipient_name, NULL AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 22 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'bills' AS expense_category, 17.67 AS base_amount, 'Vodafone GmbH – mobile & landline Nov' AS description, NULL AS recipient_account, 'Vodafone GmbH' AS recipient_name, NULL AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 23 AS sort_order, 'credit' AS transaction_type, 'transfer' AS category, 'other' AS expense_category, 55955.00 AS base_amount, 'Verpflegungspauschale Nov 2023' AS description, NULL AS recipient_account, 'Muster GmbH HR' AS recipient_name, 'Landesbank Hessen-Thüringen' AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 24 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'bills' AS expense_category, 17.45 AS base_amount, 'O2 Rechnung – December 2023' AS description, NULL AS recipient_account, 'O2 Germany' AS recipient_name, NULL AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 25 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'shopping' AS expense_category, 3280.00 AS base_amount, 'Wilma wunder – Wiesbaden store' AS description, NULL AS recipient_account, 'Wilma wunder Einzelhandel' AS recipient_name, NULL AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 26 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'other' AS expense_category, 2800.00 AS base_amount, 'Heiliggeist Apotheke – prescription & OTC' AS description, NULL AS recipient_account, 'Heiliggeist Apotheke' AS recipient_name, NULL AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 27 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'bills' AS expense_category, 17.67 AS base_amount, '1&1 Versatel – Jan 2024 broadband' AS description, NULL AS recipient_account, '1&1 Versatel GmbH' AS recipient_name, NULL AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 28 AS sort_order, 'credit' AS transaction_type, 'transfer' AS category, 'salary' AS expense_category, 1450000.00 AS base_amount, 'Gehalt Nov 2023 – Muster GmbH' AS description, NULL AS recipient_account, 'Muster GmbH Payroll' AS recipient_name, 'Commerzbank AG' AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 29 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'bills' AS expense_category, 17.85 AS base_amount, 'Congstar – Feb 2024 mobile' AS description, NULL AS recipient_account, 'Congstar GmbH' AS recipient_name, NULL AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 30 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'bills' AS expense_category, 17.20 AS base_amount, 'E.ON Strom – March 2024' AS description, NULL AS recipient_account, 'E.ON Energie Deutschland' AS recipient_name, NULL AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 31 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'shopping' AS expense_category, 1625.00 AS base_amount, 'Fitshop Wiesbaden – sports gear' AS description, NULL AS recipient_account, 'Fitshop Wiesbaden' AS recipient_name, NULL AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 32 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'bills' AS expense_category, 17.67 AS base_amount, 'Stadtwerke Wiesbaden – April utilities' AS description, NULL AS recipient_account, 'Stadtwerke Wiesbaden' AS recipient_name, NULL AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 33 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'bills' AS expense_category, 17.60 AS base_amount, 'Vodafone – May 2024 mobile' AS description, NULL AS recipient_account, 'Vodafone GmbH' AS recipient_name, NULL AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 34 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'bills' AS expense_category, 17.70 AS base_amount, 'O2 Rechnung – June 2024' AS description, NULL AS recipient_account, 'O2 Germany' AS recipient_name, NULL AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 35 AS sort_order, 'debit' AS transaction_type, 'transfer' AS category, 'other' AS expense_category, 5000.00 AS base_amount, 'Wire to Paul Hartman – Ref WH-60924' AS description, NULL AS recipient_account, 'Paul Hartman' AS recipient_name, 'Deutsche Bank AG' AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 36 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'bills' AS expense_category, 17.67 AS base_amount, '1&1 – July 2024 broadband' AS description, NULL AS recipient_account, '1&1 Versatel GmbH' AS recipient_name, NULL AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 37 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'bills' AS expense_category, 17.47 AS base_amount, 'Congstar – Aug 2024' AS description, NULL AS recipient_account, 'Congstar GmbH' AS recipient_name, NULL AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 38 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'shopping' AS expense_category, 1320.00 AS base_amount, 'Amazon.de – treadmill order' AS description, NULL AS recipient_account, 'Amazon EU S.à r.l.' AS recipient_name, NULL AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 39 AS sort_order, 'credit' AS transaction_type, 'transfer' AS category, 'other' AS expense_category, 32250.00 AS base_amount, 'DAK Zuschuss – health allowance Aug 2024' AS description, NULL AS recipient_account, 'DAK-Gesundheit' AS recipient_name, 'Sparkasse KölnBonn' AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 40 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'bills' AS expense_category, 17.85 AS base_amount, 'E.ON Strom – September 2024' AS description, NULL AS recipient_account, 'E.ON Energie Deutschland' AS recipient_name, NULL AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 41 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'bills' AS expense_category, 17.65 AS base_amount, 'Telekom Deutschland – Oct 2024' AS description, NULL AS recipient_account, 'Telekom Deutschland GmbH' AS recipient_name, NULL AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 42 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'bills' AS expense_category, 17.25 AS base_amount, 'Vodafone – Nov 2024' AS description, NULL AS recipient_account, 'Vodafone GmbH' AS recipient_name, NULL AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 43 AS sort_order, 'credit' AS transaction_type, 'transfer' AS category, 'other' AS expense_category, 59700.00 AS base_amount, 'Verpflegungspauschale Nov 2024 – Muster GmbH' AS description, NULL AS recipient_account, 'Muster GmbH HR' AS recipient_name, 'ING-DiBa AG' AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 44 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'bills' AS expense_category, 17.46 AS base_amount, 'O2 Rechnung – Dec 2024' AS description, NULL AS recipient_account, 'O2 Germany' AS recipient_name, NULL AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 45 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'shopping' AS expense_category, 18270.00 AS base_amount, 'Amazon.de – year-end order' AS description, NULL AS recipient_account, 'Amazon EU S.à r.l.' AS recipient_name, NULL AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 46 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'bills' AS expense_category, 17.38 AS base_amount, '1&1 – Jan 2025' AS description, NULL AS recipient_account, '1&1 Versatel GmbH' AS recipient_name, NULL AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 47 AS sort_order, 'credit' AS transaction_type, 'transfer' AS category, 'salary' AS expense_category, 1330000.00 AS base_amount, 'Gehalt Dez 2024 – Muster GmbH' AS description, NULL AS recipient_account, 'Muster GmbH Payroll' AS recipient_name, 'Commerzbank AG' AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 48 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'bills' AS expense_category, 17.67 AS base_amount, 'Congstar – Feb 2025' AS description, NULL AS recipient_account, 'Congstar GmbH' AS recipient_name, NULL AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 49 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'bills' AS expense_category, 17.86 AS base_amount, 'E.ON Strom – March 2025' AS description, NULL AS recipient_account, 'E.ON Energie Deutschland' AS recipient_name, NULL AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 50 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'gift' AS expense_category, 7130.00 AS base_amount, 'Galeria Kaufhof – gift & collection' AS description, NULL AS recipient_account, 'Galeria Kaufhof' AS recipient_name, NULL AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 51 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'bills' AS expense_category, 17.34 AS base_amount, 'Stadtwerke – April 2025' AS description, NULL AS recipient_account, 'Stadtwerke Wiesbaden' AS recipient_name, NULL AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 52 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'bills' AS expense_category, 17.75 AS base_amount, 'Vodafone – May 2025' AS description, NULL AS recipient_account, 'Vodafone GmbH' AS recipient_name, NULL AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 53 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'bills' AS expense_category, 17.55 AS base_amount, 'O2 Rechnung – June 2025' AS description, NULL AS recipient_account, 'O2 Germany' AS recipient_name, NULL AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 54 AS sort_order, 'debit' AS transaction_type, 'transfer' AS category, 'other' AS expense_category, 17000.00 AS base_amount, 'Wire to Kendra Nielsen – Ref WN-62725' AS description, NULL AS recipient_account, 'Kendra Nielsen' AS recipient_name, 'Erste Bank Wien' AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 55 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'bills' AS expense_category, 17.82 AS base_amount, 'Telekom Deutschland – July 2025' AS description, NULL AS recipient_account, 'Telekom Deutschland GmbH' AS recipient_name, NULL AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 56 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'bills' AS expense_category, 17.22 AS base_amount, '1&1 – August 2025' AS description, NULL AS recipient_account, '1&1 Versatel GmbH' AS recipient_name, NULL AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 57 AS sort_order, 'credit' AS transaction_type, 'transfer' AS category, 'other' AS expense_category, 37925.00 AS base_amount, 'AOK Zuschuss – health Aug 2025' AS description, NULL AS recipient_account, 'AOK Rheinland/Hamburg' AS recipient_name, 'Postbank Ndl. Bonn' AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 58 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'bills' AS expense_category, 17.27 AS base_amount, 'Congstar – Sept 2025' AS description, NULL AS recipient_account, 'Congstar GmbH' AS recipient_name, NULL AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 59 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'shopping' AS expense_category, 5500.00 AS base_amount, 'Parfümerie Hussong oHG – Wiesbaden' AS description, NULL AS recipient_account, 'Parfümerie Hussong oHG' AS recipient_name, NULL AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 60 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'bills' AS expense_category, 17.66 AS base_amount, 'E.ON Strom – Oct 2025' AS description, NULL AS recipient_account, 'E.ON Energie Deutschland' AS recipient_name, NULL AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 61 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'bills' AS expense_category, 17.52 AS base_amount, 'Vodafone – Nov 2025' AS description, NULL AS recipient_account, 'Vodafone GmbH' AS recipient_name, NULL AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 62 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'shopping' AS expense_category, 6750.00 AS base_amount, 'E-Bike Center Mainz – electric bike' AS description, NULL AS recipient_account, 'E-Bike Center Mainz' AS recipient_name, NULL AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 63 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'bills' AS expense_category, 17.79 AS base_amount, 'O2 Rechnung – Dec 2025' AS description, NULL AS recipient_account, 'O2 Germany' AS recipient_name, NULL AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 64 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'shopping' AS expense_category, 6400.00 AS base_amount, 'SportScheck – gym equipment' AS description, NULL AS recipient_account, 'SportScheck GmbH' AS recipient_name, NULL AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 65 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'bills' AS expense_category, 17.85 AS base_amount, 'Telekom Deutschland – Jan 2026' AS description, NULL AS recipient_account, 'Telekom Deutschland GmbH' AS recipient_name, NULL AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 66 AS sort_order, 'credit' AS transaction_type, 'transfer' AS category, 'salary' AS expense_category, 1680000.00 AS base_amount, 'Gehalt Jan 2026 – Muster GmbH' AS description, NULL AS recipient_account, 'Muster GmbH Payroll' AS recipient_name, 'Targobank AG' AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 67 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'shopping' AS expense_category, 28340.00 AS base_amount, 'Ford Händler Mainz – accessories' AS description, NULL AS recipient_account, 'Ford Autohaus Mainz' AS recipient_name, NULL AS recipient_bank, 'completed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 68 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'shopping' AS expense_category, 3920.00 AS base_amount, 'Shopify store – kiddies order (declined)' AS description, NULL AS recipient_account, 'Shopify Payments' AS recipient_name, NULL AS recipient_bank, 'failed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 69 AS sort_order, 'debit' AS transaction_type, 'payment' AS category, 'bills' AS expense_category, 17.67 AS base_amount, 'Congstar – Feb 2026 (declined)' AS description, NULL AS recipient_account, 'Congstar GmbH' AS recipient_name, NULL AS recipient_bank, 'failed' AS status, 0.00 AS fee, 1 AS weight
  UNION ALL
  SELECT 70 AS sort_order, 'debit' AS transaction_type, 'transfer' AS category, 'other' AS expense_category, 25000.00 AS base_amount, 'Wire to Paul Hartman – Ref WH-22726 (declined)' AS description, NULL AS recipient_account, 'Paul Hartman' AS recipient_name, 'UBS Switzerland' AS recipient_bank, 'failed' AS status, 0.00 AS fee, 1 AS weight
) src
WHERE @has_tpl_table > 0 AND @tpl_exists = 0 AND @template_id IS NOT NULL;

/* Mark this upgrade in schema_migrations (if table exists) */
SET @has_schema_migrations := (
  SELECT COUNT(*) FROM information_schema.tables
  WHERE table_schema = @db AND table_name = 'schema_migrations'
);
SET @sql := IF(@has_schema_migrations > 0,
  'INSERT INTO schema_migrations (version, migration_name, migration_file, status)
   SELECT ''2026.07.07'', ''safe_schema_upgrade'', ''2026_03_19_safe_schema_upgrade.sql'', ''success''
   WHERE NOT EXISTS (
     SELECT 1 FROM schema_migrations WHERE version = ''2026.07.07'' AND migration_name = ''safe_schema_upgrade''
   );',
  'SELECT 1;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

