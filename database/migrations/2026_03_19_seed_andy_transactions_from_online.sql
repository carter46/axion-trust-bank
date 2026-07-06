/*
  Seed transactions for andyjoycemaris@gmail.com
  Source reference: u502532383_online.sql user Phartman076@outlook.com

  IMPORTANT:
  - Inserts transactions only (does NOT update accounts.balance / accounts.available_balance).
  - Amounts are intentionally MODIFIED (scaled) from source.
  - balance_before/balance_after are generated so the LAST transaction ends at the CURRENT account balance.
  - Failed transactions do NOT change the running balance.
  - Idempotent: uses a unique transaction_ref prefix and skips if already seeded.
*/

SET @andy_email := 'andyjoycemaris@gmail.com';
/* Reference label only (not a user on this DB). */
SET @source_label := 'u502532383_online:Phartman076@outlook.com';
SET @ref_prefix := 'MIG-ANDY-ONLINE-';

SET @andy_user_id := (
  SELECT id FROM users WHERE email = @andy_email LIMIT 1
);

SET @andy_account_id := (
  SELECT id FROM accounts WHERE user_id = @andy_user_id ORDER BY id ASC LIMIT 1
);

SET @target_balance := (
  SELECT balance FROM accounts WHERE id = @andy_account_id
);
SET @target_balance := IFNULL(@target_balance, 0.00);

/* If we already seeded, do nothing. */
SET @already_seeded := (
  SELECT COUNT(*) FROM transactions WHERE transaction_ref LIKE CONCAT(@ref_prefix, '%')
);
SET @should_seed := IF(@andy_user_id IS NULL OR @andy_account_id IS NULL OR @already_seeded > 0, 0, 1);

/* Always modify amounts (scale < 1). */
SET @scale := 0.73;

/* Build a “source-like” transaction list (70 rows). */
SET @net_scaled := (
  SELECT SUM(
    CASE
      WHEN s.status = 'completed' THEN
        CASE
          WHEN s.transaction_type = 'credit' THEN ROUND(s.base_amount * @scale, 2)
          ELSE -ROUND(s.base_amount * @scale, 2)
        END
      ELSE 0
    END
  )
  FROM (
    SELECT 1 AS seq, 'PH-ADM20251205184835280' AS source_ref, 'credit' AS transaction_type, 'deposit' AS category, 'salary' AS expense_category, 2350000.00 AS base_amount, 'USD' AS currency,
           'Transfer from Salary Payment – ACADEMI PMC at wells Fargo' AS description, '44182937' AS recipient_account, 'Salary Payment – ACADEMI PMC' AS recipient_name, 'wells Fargo' AS recipient_bank,
           'completed' AS status, NULL AS payment_method, 0.00 AS fee, NULL AS exchange_rate, '129.205.124.218' AS ip_address, '2023-11-27 15:35:00' AS created_at, '2023-11-27 15:35:00' AS completed_at
    UNION ALL SELECT 2, 'PH-ADM20251205185037752', 'debit', 'withdrawal', NULL, 7000000.00, 'USD',
           'Domestic Transfer to pascal paul at citi bank', '22353563', 'pascal paul', 'citi bank',
           'completed', NULL, 0.00, NULL, '129.205.124.218', '2023-06-14 08:51:00', '2023-06-14 08:51:00'
    UNION ALL SELECT 3, 'PH-ADM20251205185401462', 'debit', 'withdrawal', NULL, 185000.00, 'USD',
           'International Transfer to James Thornton at HSBC UK', 'GB72HBUK40127612345678', 'James Thornton', 'HSBC UK',
           'completed', NULL, 0.00, NULL, '129.205.124.218', '2023-12-05 06:53:00', '2023-12-05 06:53:00'
    UNION ALL SELECT 4, 'PH-ADM20251205190029145', 'debit', 'withdrawal', NULL, 27500.00, 'USD',
           'Domestic Transfer to Michael Rodriguez at Chase Bank', '463817492', 'Michael Rodriguez', 'Chase Bank',
           'failed', NULL, 0.00, NULL, '129.205.124.218', '2024-01-19 12:00:00', NULL
    UNION ALL SELECT 5, 'PH-ADM20251205190029312', 'debit', 'withdrawal', NULL, 27500.00, 'USD',
           'Domestic Transfer to Michael Rodriguez at Chase Bank', '463817492', 'Michael Rodriguez', 'Chase Bank',
           'completed', NULL, 0.00, NULL, '129.205.124.218', '2024-01-19 12:01:00', '2024-01-19 12:01:00'
    UNION ALL SELECT 6, 'PH-ADM20251205190448485', 'credit', 'deposit', NULL, 9842.00, 'USD',
           'IRS Tax Refund Adjustment', '009283514', 'Internal Revenue Service', 'U.S. Treasury Department',
           'completed', NULL, 0.00, NULL, '129.205.124.218', '2024-03-02 20:03:00', '2024-03-02 20:03:00'
    UNION ALL SELECT 7, 'PH-ADM20251205190701787', 'debit', 'withdrawal', NULL, 62900.00, 'USD',
           'International Transfer to Cobus Van Der West at Standard Bank South Africa', '128476395', 'Cobus Van Der West', 'Standard Bank South Africa',
           'completed', NULL, 0.00, NULL, '129.205.124.218', '2025-11-07 22:06:00', '2025-12-08 13:11:34'
    UNION ALL SELECT 8, 'PH-ADM20251205190936451', 'debit', 'withdrawal', NULL, 3120.00, 'USD',
           'Domestic Transfer to Amazon Web Services at JPMorgan Payments', '875341209', 'Amazon Web Services', 'JPMorgan Payments',
           'completed', NULL, 0.00, NULL, '129.205.124.218', '2024-12-17 04:16:00', '2025-12-06 22:32:15'
    UNION ALL SELECT 9, 'PH-ADM20251205191104697', 'credit', 'deposit', NULL, 2350000.00, 'USD',
           'Transfer from ACADEMI PMC at wells Fargo', '4418293723', 'ACADEMI PMC', 'wells Fargo',
           'completed', NULL, 0.00, NULL, '129.205.124.218', '2024-11-25 21:11:00', '2024-11-25 21:11:00'
    UNION ALL SELECT 10, 'PH-ADM20251205191222495', 'credit', 'deposit', NULL, 2350000.00, 'USD',
           'Transfer from ACADEMI PMC at wells Fargo', '4418293723', 'ACADEMI PMC', 'wells Fargo',
           'completed', NULL, 0.00, NULL, '129.205.124.218', '2024-12-05 07:11:00', '2025-12-07 21:14:11'
    UNION ALL SELECT 11, 'PH-TXN6933CB00306C2', 'debit', 'transfer', NULL, 8492.25, 'USD',
           'Domestic Transfer to Matts Anderson at Wells Fargo Bank', '6272883838', 'Matts Anderson', 'Wells Fargo Bank',
           'completed', NULL, 42.25, NULL, '102.89.69.33', '2025-11-26 14:19:00', '2025-12-07 21:16:05'
    UNION ALL SELECT 12, 'PH-ADM20251207161826394', 'debit', 'withdrawal', NULL, 35700.00, 'USD',
           'Domestic Transfer to James Thornton at HSBC UK', '3647687970809', 'James Thornton', 'HSBC UK',
           'completed', NULL, 0.00, NULL, '129.205.124.245', '2025-12-02 14:51:00', '2026-01-11 20:38:54'
    UNION ALL SELECT 13, 'PH-TXN6963971BF1568', 'debit', 'transfer', NULL, 4600.00, 'USD',
           'Domestic Transfer to Leave@academi at JPMorgan Chase Bank', '26273741639', 'Leave@academi', 'JPMorgan Chase Bank',
           'failed', NULL, 22.96, NULL, '216.227.38.36', '2026-01-12 20:27:00', NULL
    UNION ALL SELECT 14, 'PH-ADM20260201100400235', 'credit', 'deposit', 'insurance', 7097129.00, 'USD',
           'Transfer from Titan Core Assets Group LLC at wells Fargo', '4418293723', 'Titan Core Assets Group LLC', 'wells Fargo',
           'completed', NULL, 0.00, NULL, '149.88.103.34', '2022-02-03 07:03:00', '2022-02-03 07:03:00'
    UNION ALL SELECT 15, 'PH-ADM20260201100622103', 'debit', 'withdrawal', NULL, 9100.00, 'USD',
           'Domestic Transfer to Academi@Admin at JPMorgan Chase Bank', '868746356795', 'Academi@Admin', 'JPMorgan Chase Bank',
           'failed', NULL, 0.00, NULL, '149.88.103.34', '2026-02-03 12:09:00', NULL
    UNION ALL SELECT 16, 'PH-ADM20260201100934265', 'debit', 'withdrawal', NULL, 2300.00, 'USD',
           'Card payment to Academi@Clinic', '868746356795', 'Academi@Clinic', 'JPMorgan Chase Bank',
           'completed', NULL, 0.00, NULL, '149.88.103.34', '2026-01-30 09:07:00', '2026-01-30 09:07:00'
    UNION ALL SELECT 17, 'PH-ADM20260201101103882', 'debit', 'withdrawal', NULL, 49500.00, 'USD',
           'Domestic Transfer to Wright Caleb at wells Fargo', 'US-CH-77451092', 'Wright Caleb', 'wells Fargo',
           'completed', NULL, 0.00, NULL, '149.88.103.34', '2026-01-24 07:09:00', '2026-01-24 07:09:00'

    /* Seed series */
    UNION ALL SELECT 18, 'PH-SEED-HKR-20230830-01', 'credit', 'transfer', 'other', 27150.00, 'USD',
           'BKK Gesund – health allowance Q3 2023', NULL, 'BKK Gesund', 'DZ Bank Ndl. Frankfurt',
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2023-08-30 12:00:00', '2023-08-30 12:00:00'
    UNION ALL SELECT 19, 'PH-SEED-HKR-20231002-02', 'debit', 'payment', 'bills', 17.30, 'USD',
           'Telekom Deutschland – Oct 2023 invoice', NULL, 'Telekom Deutschland GmbH', NULL,
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2023-10-02 12:00:00', '2023-10-02 12:00:00'
    UNION ALL SELECT 20, 'PH-SEED-HKR-20231005-03', 'debit', 'payment', 'shopping', 550.00, 'USD',
           'Nike.com e-gift card order', NULL, 'Nike E-Commerce', NULL,
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2023-10-05 12:00:00', '2023-10-05 12:00:00'
    UNION ALL SELECT 21, 'PH-SEED-HKR-20231018-04', 'debit', 'payment', 'shopping', 182.00, 'USD',
           'Shopify store – online purchase', NULL, 'Shopify Payments', NULL,
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2023-10-18 12:00:00', '2023-10-18 12:00:00'
    UNION ALL SELECT 22, 'PH-SEED-HKR-20231102-05', 'debit', 'payment', 'bills', 17.67, 'USD',
           'Vodafone GmbH – mobile & landline Nov', NULL, 'Vodafone GmbH', NULL,
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2023-11-02 12:00:00', '2023-11-02 12:00:00'
    UNION ALL SELECT 23, 'PH-SEED-HKR-20231122-06', 'credit', 'transfer', 'other', 55955.00, 'USD',
           'Verpflegungspauschale Nov 2023', NULL, 'Muster GmbH HR', 'Landesbank Hessen-Thüringen',
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2023-11-22 12:00:00', '2023-11-22 12:00:00'
    UNION ALL SELECT 24, 'PH-SEED-HKR-20231202-07', 'debit', 'payment', 'bills', 17.45, 'USD',
           'O2 Rechnung – December 2023', NULL, 'O2 Germany', NULL,
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2023-12-02 12:00:00', '2023-12-02 12:00:00'
    UNION ALL SELECT 25, 'PH-SEED-HKR-20231224-08', 'debit', 'payment', 'shopping', 3280.00, 'USD',
           'Wilma wunder – Wiesbaden store', NULL, 'Wilma wunder Einzelhandel', NULL,
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2023-12-24 12:00:00', '2023-12-24 12:00:00'
    UNION ALL SELECT 26, 'PH-SEED-HKR-20231231-09', 'debit', 'payment', 'other', 2800.00, 'USD',
           'Heiliggeist Apotheke – prescription & OTC', NULL, 'Heiliggeist Apotheke', NULL,
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2023-12-31 12:00:00', '2023-12-31 12:00:00'
    UNION ALL SELECT 27, 'PH-SEED-HKR-20240102-10', 'debit', 'payment', 'bills', 17.67, 'USD',
           '1&1 Versatel – Jan 2024 broadband', NULL, '1&1 Versatel GmbH', NULL,
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2024-01-02 12:00:00', '2024-01-02 12:00:00'
    UNION ALL SELECT 28, 'PH-SEED-HKR-20240105-11', 'credit', 'transfer', 'salary', 1450000.00, 'USD',
           'Gehalt Nov 2023 – Muster GmbH', NULL, 'Muster GmbH Payroll', 'Commerzbank AG',
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2024-01-05 12:00:00', '2024-01-05 12:00:00'
    UNION ALL SELECT 29, 'PH-SEED-HKR-20240202-12', 'debit', 'payment', 'bills', 17.85, 'USD',
           'Congstar – Feb 2024 mobile', NULL, 'Congstar GmbH', NULL,
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2024-02-02 12:00:00', '2024-02-02 12:00:00'
    UNION ALL SELECT 30, 'PH-SEED-HKR-20240302-13', 'debit', 'payment', 'bills', 17.20, 'USD',
           'E.ON Strom – March 2024', NULL, 'E.ON Energie Deutschland', NULL,
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2024-03-02 12:00:00', '2024-03-02 12:00:00'
    UNION ALL SELECT 31, 'PH-SEED-HKR-20240315-14', 'debit', 'payment', 'shopping', 1625.00, 'USD',
           'Fitshop Wiesbaden – sports gear', NULL, 'Fitshop Wiesbaden', NULL,
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2024-03-15 12:00:00', '2024-03-15 12:00:00'
    UNION ALL SELECT 32, 'PH-SEED-HKR-20240402-15', 'debit', 'payment', 'bills', 17.67, 'USD',
           'Stadtwerke Wiesbaden – April utilities', NULL, 'Stadtwerke Wiesbaden', NULL,
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2024-04-02 12:00:00', '2024-04-02 12:00:00'
    UNION ALL SELECT 33, 'PH-SEED-HKR-20240502-16', 'debit', 'payment', 'bills', 17.60, 'USD',
           'Vodafone – May 2024 mobile', NULL, 'Vodafone GmbH', NULL,
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2024-05-02 12:00:00', '2024-05-02 12:00:00'
    UNION ALL SELECT 34, 'PH-SEED-HKR-20240602-17', 'debit', 'payment', 'bills', 17.70, 'USD',
           'O2 Rechnung – June 2024', NULL, 'O2 Germany', NULL,
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2024-06-02 12:00:00', '2024-06-02 12:00:00'
    UNION ALL SELECT 35, 'PH-SEED-HKR-20240609-18', 'debit', 'transfer', 'other', 5000.00, 'USD',
           'Wire to Paul Hartman – Ref WH-60924', NULL, 'Paul Hartman', 'Deutsche Bank AG',
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2024-06-09 12:00:00', '2024-06-09 12:00:00'
    UNION ALL SELECT 36, 'PH-SEED-HKR-20240702-19', 'debit', 'payment', 'bills', 17.67, 'USD',
           '1&1 – July 2024 broadband', NULL, '1&1 Versatel GmbH', NULL,
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2024-07-02 12:00:00', '2024-07-02 12:00:00'
    UNION ALL SELECT 37, 'PH-SEED-HKR-20240802-20', 'debit', 'payment', 'bills', 17.47, 'USD',
           'Congstar – Aug 2024', NULL, 'Congstar GmbH', NULL,
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2024-08-02 12:00:00', '2024-08-02 12:00:00'
    UNION ALL SELECT 38, 'PH-SEED-HKR-20240816-21', 'debit', 'payment', 'shopping', 1320.00, 'USD',
           'Amazon.de – treadmill order', NULL, 'Amazon EU S.à r.l.', NULL,
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2024-08-16 12:00:00', '2024-08-16 12:00:00'
    UNION ALL SELECT 39, 'PH-SEED-HKR-20240829-22', 'credit', 'transfer', 'other', 32250.00, 'USD',
           'DAK Zuschuss – health allowance Aug 2024', NULL, 'DAK-Gesundheit', 'Sparkasse KölnBonn',
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2024-08-29 12:00:00', '2024-08-29 12:00:00'
    UNION ALL SELECT 40, 'PH-SEED-HKR-20240902-23', 'debit', 'payment', 'bills', 17.85, 'USD',
           'E.ON Strom – September 2024', NULL, 'E.ON Energie Deutschland', NULL,
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2024-09-02 12:00:00', '2024-09-02 12:00:00'
    UNION ALL SELECT 41, 'PH-SEED-HKR-20241002-24', 'debit', 'payment', 'bills', 17.65, 'USD',
           'Telekom Deutschland – Oct 2024', NULL, 'Telekom Deutschland GmbH', NULL,
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2024-10-02 12:00:00', '2024-10-02 12:00:00'
    UNION ALL SELECT 42, 'PH-SEED-HKR-20241102-25', 'debit', 'payment', 'bills', 17.25, 'USD',
           'Vodafone – Nov 2024', NULL, 'Vodafone GmbH', NULL,
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2024-11-02 12:00:00', '2024-11-02 12:00:00'
    UNION ALL SELECT 43, 'PH-SEED-HKR-20241118-26', 'credit', 'transfer', 'other', 59700.00, 'USD',
           'Verpflegungspauschale Nov 2024 – Muster GmbH', NULL, 'Muster GmbH HR', 'ING-DiBa AG',
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2024-11-18 12:00:00', '2024-11-18 12:00:00'
    UNION ALL SELECT 44, 'PH-SEED-HKR-20241202-27', 'debit', 'payment', 'bills', 17.46, 'USD',
           'O2 Rechnung – Dec 2024', NULL, 'O2 Germany', NULL,
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2024-12-02 12:00:00', '2024-12-02 12:00:00'
    UNION ALL SELECT 45, 'PH-SEED-HKR-20241228-28', 'debit', 'payment', 'shopping', 18270.00, 'USD',
           'Amazon.de – year-end order', NULL, 'Amazon EU S.à r.l.', NULL,
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2024-12-28 12:00:00', '2024-12-28 12:00:00'
    UNION ALL SELECT 46, 'PH-SEED-HKR-20250102-29', 'debit', 'payment', 'bills', 17.38, 'USD',
           '1&1 – Jan 2025', NULL, '1&1 Versatel GmbH', NULL,
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2025-01-02 12:00:00', '2025-01-02 12:00:00'
    UNION ALL SELECT 47, 'PH-SEED-HKR-20250107-30', 'credit', 'transfer', 'salary', 1330000.00, 'USD',
           'Gehalt Dez 2024 – Muster GmbH', NULL, 'Muster GmbH Payroll', 'Commerzbank AG',
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2025-01-07 12:00:00', '2025-01-07 12:00:00'
    UNION ALL SELECT 48, 'PH-SEED-HKR-20250202-31', 'debit', 'payment', 'bills', 17.67, 'USD',
           'Congstar – Feb 2025', NULL, 'Congstar GmbH', NULL,
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2025-02-02 12:00:00', '2025-02-02 12:00:00'
    UNION ALL SELECT 49, 'PH-SEED-HKR-20250302-32', 'debit', 'payment', 'bills', 17.86, 'USD',
           'E.ON Strom – March 2025', NULL, 'E.ON Energie Deutschland', NULL,
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2025-03-02 12:00:00', '2025-03-02 12:00:00'
    UNION ALL SELECT 50, 'PH-SEED-HKR-20250303-33', 'debit', 'payment', 'gift', 7130.00, 'USD',
           'Galeria Kaufhof – gift & collection', NULL, 'Galeria Kaufhof', NULL,
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2025-03-03 12:00:00', '2025-03-03 12:00:00'
    UNION ALL SELECT 51, 'PH-SEED-HKR-20250402-34', 'debit', 'payment', 'bills', 17.34, 'USD',
           'Stadtwerke – April 2025', NULL, 'Stadtwerke Wiesbaden', NULL,
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2025-04-02 12:00:00', '2025-04-02 12:00:00'
    UNION ALL SELECT 52, 'PH-SEED-HKR-20250502-35', 'debit', 'payment', 'bills', 17.75, 'USD',
           'Vodafone – May 2025', NULL, 'Vodafone GmbH', NULL,
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2025-05-02 12:00:00', '2025-05-02 12:00:00'
    UNION ALL SELECT 53, 'PH-SEED-HKR-20250602-36', 'debit', 'payment', 'bills', 17.55, 'USD',
           'O2 Rechnung – June 2025', NULL, 'O2 Germany', NULL,
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2025-06-02 12:00:00', '2025-06-02 12:00:00'
    UNION ALL SELECT 54, 'PH-SEED-HKR-20250627-37', 'debit', 'transfer', 'other', 17000.00, 'USD',
           'Wire to Kendra Nielsen – Ref WN-62725', NULL, 'Kendra Nielsen', 'Erste Bank Wien',
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2025-06-27 12:00:00', '2025-06-27 12:00:00'
    UNION ALL SELECT 55, 'PH-SEED-HKR-20250702-38', 'debit', 'payment', 'bills', 17.82, 'USD',
           'Telekom Deutschland – July 2025', NULL, 'Telekom Deutschland GmbH', NULL,
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2025-07-02 12:00:00', '2025-07-02 12:00:00'
    UNION ALL SELECT 56, 'PH-SEED-HKR-20250802-39', 'debit', 'payment', 'bills', 17.22, 'USD',
           '1&1 – August 2025', NULL, '1&1 Versatel GmbH', NULL,
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2025-08-02 12:00:00', '2025-08-02 12:00:00'
    UNION ALL SELECT 57, 'PH-SEED-HKR-20250829-40', 'credit', 'transfer', 'other', 37925.00, 'USD',
           'AOK Zuschuss – health Aug 2025', NULL, 'AOK Rheinland/Hamburg', 'Postbank Ndl. Bonn',
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2025-08-29 12:00:00', '2025-08-29 12:00:00'
    UNION ALL SELECT 58, 'PH-SEED-HKR-20250902-41', 'debit', 'payment', 'bills', 17.27, 'USD',
           'Congstar – Sept 2025', NULL, 'Congstar GmbH', NULL,
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2025-09-02 12:00:00', '2025-09-02 12:00:00'
    UNION ALL SELECT 59, 'PH-SEED-HKR-20250919-42', 'debit', 'payment', 'shopping', 5500.00, 'USD',
           'Parfümerie Hussong oHG – Wiesbaden', NULL, 'Parfümerie Hussong oHG', NULL,
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2025-09-19 12:00:00', '2025-09-19 12:00:00'
    UNION ALL SELECT 60, 'PH-SEED-HKR-20251002-43', 'debit', 'payment', 'bills', 17.66, 'USD',
           'E.ON Strom – Oct 2025', NULL, 'E.ON Energie Deutschland', NULL,
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2025-10-02 12:00:00', '2025-10-02 12:00:00'
    UNION ALL SELECT 61, 'PH-SEED-HKR-20251102-44', 'debit', 'payment', 'bills', 17.52, 'USD',
           'Vodafone – Nov 2025', NULL, 'Vodafone GmbH', NULL,
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2025-11-02 12:00:00', '2025-11-02 12:00:00'
    UNION ALL SELECT 62, 'PH-SEED-HKR-20251126-45', 'debit', 'payment', 'shopping', 6750.00, 'USD',
           'E-Bike Center Mainz – electric bike', NULL, 'E-Bike Center Mainz', NULL,
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2025-11-26 12:00:00', '2025-11-26 12:00:00'
    UNION ALL SELECT 63, 'PH-SEED-HKR-20251202-46', 'debit', 'payment', 'bills', 17.79, 'USD',
           'O2 Rechnung – Dec 2025', NULL, 'O2 Germany', NULL,
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2025-12-02 12:00:00', '2025-12-02 12:00:00'
    UNION ALL SELECT 64, 'PH-SEED-HKR-20251211-47', 'debit', 'payment', 'shopping', 6400.00, 'USD',
           'SportScheck – gym equipment', NULL, 'SportScheck GmbH', NULL,
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2025-12-11 12:00:00', '2025-12-11 12:00:00'
    UNION ALL SELECT 65, 'PH-SEED-HKR-20260102-48', 'debit', 'payment', 'bills', 17.85, 'USD',
           'Telekom Deutschland – Jan 2026', NULL, 'Telekom Deutschland GmbH', NULL,
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2026-01-02 12:00:00', '2026-01-02 12:00:00'
    UNION ALL SELECT 66, 'PH-SEED-HKR-20260102-49', 'credit', 'transfer', 'salary', 1680000.00, 'USD',
           'Gehalt Jan 2026 – Muster GmbH', NULL, 'Muster GmbH Payroll', 'Targobank AG',
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2026-01-02 12:00:00', '2026-01-02 12:00:00'
    UNION ALL SELECT 67, 'PH-SEED-HKR-20260107-50', 'debit', 'payment', 'shopping', 28340.00, 'USD',
           'Ford Händler Mainz – accessories', NULL, 'Ford Autohaus Mainz', NULL,
           'completed', NULL, 0.00, NULL, '127.0.0.1', '2026-01-07 12:00:00', '2026-01-07 12:00:00'
    UNION ALL SELECT 68, 'PH-SEED-HKR-20260119-51', 'debit', 'payment', 'shopping', 3920.00, 'USD',
           'Shopify store – kiddies order (declined)', NULL, 'Shopify Payments', NULL,
           'failed', NULL, 0.00, NULL, '127.0.0.1', '2026-01-19 12:00:00', NULL
    UNION ALL SELECT 69, 'PH-SEED-HKR-20260202-52', 'debit', 'payment', 'bills', 17.67, 'USD',
           'Congstar – Feb 2026 (declined)', NULL, 'Congstar GmbH', NULL,
           'failed', NULL, 0.00, NULL, '127.0.0.1', '2026-02-02 12:00:00', NULL
    UNION ALL SELECT 70, 'PH-SEED-HKR-20260227-53', 'debit', 'transfer', 'other', 25000.00, 'USD',
           'Wire to Paul Hartman – Ref WH-22726 (declined)', NULL, 'Paul Hartman', 'UBS Switzerland',
           'failed', NULL, 0.00, NULL, '127.0.0.1', '2026-02-27 12:00:00', NULL
  ) s
);

/* Start balance so that the final computed balance matches current account balance exactly. */
SET @bal := @target_balance - IFNULL(@net_scaled, 0.00);

INSERT INTO transactions (
  transaction_ref, user_id, account_id, transaction_type, category, expense_category,
  amount, currency, balance_before, balance_after, description,
  recipient_account, recipient_name, recipient_bank, status, payment_method,
  fee, exchange_rate, metadata, ip_address, created_at, completed_at
)
SELECT
  CONCAT(@ref_prefix, s.source_ref) AS transaction_ref,
  @andy_user_id AS user_id,
  @andy_account_id AS account_id,
  s.transaction_type,
  s.category,
  s.expense_category,
  ROUND(s.base_amount * @scale, 2) AS amount,
  s.currency,
  @bal AS balance_before,
  (@bal := @bal + CASE
     WHEN s.status = 'completed' THEN
       CASE
         WHEN s.transaction_type = 'credit' THEN ROUND(s.base_amount * @scale, 2)
         ELSE -ROUND(s.base_amount * @scale, 2)
       END
     ELSE 0
   END) AS balance_after,
  s.description,
  s.recipient_account,
  s.recipient_name,
  s.recipient_bank,
  s.status,
  s.payment_method,
  ROUND(IFNULL(s.fee, 0.00) * @scale, 2) AS fee,
  s.exchange_rate,
  CONCAT(
    '{"migration":"seed_andy_transactions_from_online","source_db":"u502532383_online","source_ref":"',
    s.source_ref,
    '","source_label":"',
    @source_label,
    '"}'
  ) AS metadata,
  s.ip_address,
  s.created_at,
  s.completed_at
FROM (
  SELECT 1 AS seq, 'PH-ADM20251205184835280' AS source_ref, 'credit' AS transaction_type, 'deposit' AS category, 'salary' AS expense_category, 2350000.00 AS base_amount, 'USD' AS currency,
         'Transfer from Salary Payment – ACADEMI PMC at wells Fargo' AS description, '44182937' AS recipient_account, 'Salary Payment – ACADEMI PMC' AS recipient_name, 'wells Fargo' AS recipient_bank,
         'completed' AS status, NULL AS payment_method, 0.00 AS fee, NULL AS exchange_rate, '129.205.124.218' AS ip_address, '2023-11-27 15:35:00' AS created_at, '2023-11-27 15:35:00' AS completed_at
  UNION ALL SELECT 2, 'PH-ADM20251205185037752', 'debit', 'withdrawal', NULL, 7000000.00, 'USD',
         'Domestic Transfer to pascal paul at citi bank', '22353563', 'pascal paul', 'citi bank',
         'completed', NULL, 0.00, NULL, '129.205.124.218', '2023-06-14 08:51:00', '2023-06-14 08:51:00'
  UNION ALL SELECT 3, 'PH-ADM20251205185401462', 'debit', 'withdrawal', NULL, 185000.00, 'USD',
         'International Transfer to James Thornton at HSBC UK', 'GB72HBUK40127612345678', 'James Thornton', 'HSBC UK',
         'completed', NULL, 0.00, NULL, '129.205.124.218', '2023-12-05 06:53:00', '2023-12-05 06:53:00'
  UNION ALL SELECT 4, 'PH-ADM20251205190029145', 'debit', 'withdrawal', NULL, 27500.00, 'USD',
         'Domestic Transfer to Michael Rodriguez at Chase Bank', '463817492', 'Michael Rodriguez', 'Chase Bank',
         'failed', NULL, 0.00, NULL, '129.205.124.218', '2024-01-19 12:00:00', NULL
  UNION ALL SELECT 5, 'PH-ADM20251205190029312', 'debit', 'withdrawal', NULL, 27500.00, 'USD',
         'Domestic Transfer to Michael Rodriguez at Chase Bank', '463817492', 'Michael Rodriguez', 'Chase Bank',
         'completed', NULL, 0.00, NULL, '129.205.124.218', '2024-01-19 12:01:00', '2024-01-19 12:01:00'
  UNION ALL SELECT 6, 'PH-ADM20251205190448485', 'credit', 'deposit', NULL, 9842.00, 'USD',
         'IRS Tax Refund Adjustment', '009283514', 'Internal Revenue Service', 'U.S. Treasury Department',
         'completed', NULL, 0.00, NULL, '129.205.124.218', '2024-03-02 20:03:00', '2024-03-02 20:03:00'
  UNION ALL SELECT 7, 'PH-ADM20251205190701787', 'debit', 'withdrawal', NULL, 62900.00, 'USD',
         'International Transfer to Cobus Van Der West at Standard Bank South Africa', '128476395', 'Cobus Van Der West', 'Standard Bank South Africa',
         'completed', NULL, 0.00, NULL, '129.205.124.218', '2025-11-07 22:06:00', '2025-12-08 13:11:34'
  UNION ALL SELECT 8, 'PH-ADM20251205190936451', 'debit', 'withdrawal', NULL, 3120.00, 'USD',
         'Domestic Transfer to Amazon Web Services at JPMorgan Payments', '875341209', 'Amazon Web Services', 'JPMorgan Payments',
         'completed', NULL, 0.00, NULL, '129.205.124.218', '2024-12-17 04:16:00', '2025-12-06 22:32:15'
  UNION ALL SELECT 9, 'PH-ADM20251205191104697', 'credit', 'deposit', NULL, 2350000.00, 'USD',
         'Transfer from ACADEMI PMC at wells Fargo', '4418293723', 'ACADEMI PMC', 'wells Fargo',
         'completed', NULL, 0.00, NULL, '129.205.124.218', '2024-11-25 21:11:00', '2024-11-25 21:11:00'
  UNION ALL SELECT 10, 'PH-ADM20251205191222495', 'credit', 'deposit', NULL, 2350000.00, 'USD',
         'Transfer from ACADEMI PMC at wells Fargo', '4418293723', 'ACADEMI PMC', 'wells Fargo',
         'completed', NULL, 0.00, NULL, '129.205.124.218', '2024-12-05 07:11:00', '2025-12-07 21:14:11'
  UNION ALL SELECT 11, 'PH-TXN6933CB00306C2', 'debit', 'transfer', NULL, 8492.25, 'USD',
         'Domestic Transfer to Matts Anderson at Wells Fargo Bank', '6272883838', 'Matts Anderson', 'Wells Fargo Bank',
         'completed', NULL, 42.25, NULL, '102.89.69.33', '2025-11-26 14:19:00', '2025-12-07 21:16:05'
  UNION ALL SELECT 12, 'PH-ADM20251207161826394', 'debit', 'withdrawal', NULL, 35700.00, 'USD',
         'Domestic Transfer to James Thornton at HSBC UK', '3647687970809', 'James Thornton', 'HSBC UK',
         'completed', NULL, 0.00, NULL, '129.205.124.245', '2025-12-02 14:51:00', '2026-01-11 20:38:54'
  UNION ALL SELECT 13, 'PH-TXN6963971BF1568', 'debit', 'transfer', NULL, 4600.00, 'USD',
         'Domestic Transfer to Leave@academi at JPMorgan Chase Bank', '26273741639', 'Leave@academi', 'JPMorgan Chase Bank',
         'failed', NULL, 22.96, NULL, '216.227.38.36', '2026-01-12 20:27:00', NULL
  UNION ALL SELECT 14, 'PH-ADM20260201100400235', 'credit', 'deposit', 'insurance', 7097129.00, 'USD',
         'Transfer from Titan Core Assets Group LLC at wells Fargo', '4418293723', 'Titan Core Assets Group LLC', 'wells Fargo',
         'completed', NULL, 0.00, NULL, '149.88.103.34', '2022-02-03 07:03:00', '2022-02-03 07:03:00'
  UNION ALL SELECT 15, 'PH-ADM20260201100622103', 'debit', 'withdrawal', NULL, 9100.00, 'USD',
         'Domestic Transfer to Academi@Admin at JPMorgan Chase Bank', '868746356795', 'Academi@Admin', 'JPMorgan Chase Bank',
         'failed', NULL, 0.00, NULL, '149.88.103.34', '2026-02-03 12:09:00', NULL
  UNION ALL SELECT 16, 'PH-ADM20260201100934265', 'debit', 'withdrawal', NULL, 2300.00, 'USD',
         'Card payment to Academi@Clinic', '868746356795', 'Academi@Clinic', 'JPMorgan Chase Bank',
         'completed', NULL, 0.00, NULL, '149.88.103.34', '2026-01-30 09:07:00', '2026-01-30 09:07:00'
  UNION ALL SELECT 17, 'PH-ADM20260201101103882', 'debit', 'withdrawal', NULL, 49500.00, 'USD',
         'Domestic Transfer to Wright Caleb at wells Fargo', 'US-CH-77451092', 'Wright Caleb', 'wells Fargo',
         'completed', NULL, 0.00, NULL, '149.88.103.34', '2026-01-24 07:09:00', '2026-01-24 07:09:00'
  UNION ALL SELECT 18, 'PH-SEED-HKR-20230830-01', 'credit', 'transfer', 'other', 27150.00, 'USD',
         'BKK Gesund – health allowance Q3 2023', NULL, 'BKK Gesund', 'DZ Bank Ndl. Frankfurt',
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2023-08-30 12:00:00', '2023-08-30 12:00:00'
  UNION ALL SELECT 19, 'PH-SEED-HKR-20231002-02', 'debit', 'payment', 'bills', 17.30, 'USD',
         'Telekom Deutschland – Oct 2023 invoice', NULL, 'Telekom Deutschland GmbH', NULL,
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2023-10-02 12:00:00', '2023-10-02 12:00:00'
  UNION ALL SELECT 20, 'PH-SEED-HKR-20231005-03', 'debit', 'payment', 'shopping', 550.00, 'USD',
         'Nike.com e-gift card order', NULL, 'Nike E-Commerce', NULL,
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2023-10-05 12:00:00', '2023-10-05 12:00:00'
  UNION ALL SELECT 21, 'PH-SEED-HKR-20231018-04', 'debit', 'payment', 'shopping', 182.00, 'USD',
         'Shopify store – online purchase', NULL, 'Shopify Payments', NULL,
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2023-10-18 12:00:00', '2023-10-18 12:00:00'
  UNION ALL SELECT 22, 'PH-SEED-HKR-20231102-05', 'debit', 'payment', 'bills', 17.67, 'USD',
         'Vodafone GmbH – mobile & landline Nov', NULL, 'Vodafone GmbH', NULL,
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2023-11-02 12:00:00', '2023-11-02 12:00:00'
  UNION ALL SELECT 23, 'PH-SEED-HKR-20231122-06', 'credit', 'transfer', 'other', 55955.00, 'USD',
         'Verpflegungspauschale Nov 2023', NULL, 'Muster GmbH HR', 'Landesbank Hessen-Thüringen',
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2023-11-22 12:00:00', '2023-11-22 12:00:00'
  UNION ALL SELECT 24, 'PH-SEED-HKR-20231202-07', 'debit', 'payment', 'bills', 17.45, 'USD',
         'O2 Rechnung – December 2023', NULL, 'O2 Germany', NULL,
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2023-12-02 12:00:00', '2023-12-02 12:00:00'
  UNION ALL SELECT 25, 'PH-SEED-HKR-20231224-08', 'debit', 'payment', 'shopping', 3280.00, 'USD',
         'Wilma wunder – Wiesbaden store', NULL, 'Wilma wunder Einzelhandel', NULL,
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2023-12-24 12:00:00', '2023-12-24 12:00:00'
  UNION ALL SELECT 26, 'PH-SEED-HKR-20231231-09', 'debit', 'payment', 'other', 2800.00, 'USD',
         'Heiliggeist Apotheke – prescription & OTC', NULL, 'Heiliggeist Apotheke', NULL,
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2023-12-31 12:00:00', '2023-12-31 12:00:00'
  UNION ALL SELECT 27, 'PH-SEED-HKR-20240102-10', 'debit', 'payment', 'bills', 17.67, 'USD',
         '1&1 Versatel – Jan 2024 broadband', NULL, '1&1 Versatel GmbH', NULL,
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2024-01-02 12:00:00', '2024-01-02 12:00:00'
  UNION ALL SELECT 28, 'PH-SEED-HKR-20240105-11', 'credit', 'transfer', 'salary', 1450000.00, 'USD',
         'Gehalt Nov 2023 – Muster GmbH', NULL, 'Muster GmbH Payroll', 'Commerzbank AG',
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2024-01-05 12:00:00', '2024-01-05 12:00:00'
  UNION ALL SELECT 29, 'PH-SEED-HKR-20240202-12', 'debit', 'payment', 'bills', 17.85, 'USD',
         'Congstar – Feb 2024 mobile', NULL, 'Congstar GmbH', NULL,
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2024-02-02 12:00:00', '2024-02-02 12:00:00'
  UNION ALL SELECT 30, 'PH-SEED-HKR-20240302-13', 'debit', 'payment', 'bills', 17.20, 'USD',
         'E.ON Strom – March 2024', NULL, 'E.ON Energie Deutschland', NULL,
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2024-03-02 12:00:00', '2024-03-02 12:00:00'
  UNION ALL SELECT 31, 'PH-SEED-HKR-20240315-14', 'debit', 'payment', 'shopping', 1625.00, 'USD',
         'Fitshop Wiesbaden – sports gear', NULL, 'Fitshop Wiesbaden', NULL,
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2024-03-15 12:00:00', '2024-03-15 12:00:00'
  UNION ALL SELECT 32, 'PH-SEED-HKR-20240402-15', 'debit', 'payment', 'bills', 17.67, 'USD',
         'Stadtwerke Wiesbaden – April utilities', NULL, 'Stadtwerke Wiesbaden', NULL,
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2024-04-02 12:00:00', '2024-04-02 12:00:00'
  UNION ALL SELECT 33, 'PH-SEED-HKR-20240502-16', 'debit', 'payment', 'bills', 17.60, 'USD',
         'Vodafone – May 2024 mobile', NULL, 'Vodafone GmbH', NULL,
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2024-05-02 12:00:00', '2024-05-02 12:00:00'
  UNION ALL SELECT 34, 'PH-SEED-HKR-20240602-17', 'debit', 'payment', 'bills', 17.70, 'USD',
         'O2 Rechnung – June 2024', NULL, 'O2 Germany', NULL,
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2024-06-02 12:00:00', '2024-06-02 12:00:00'
  UNION ALL SELECT 35, 'PH-SEED-HKR-20240609-18', 'debit', 'transfer', 'other', 5000.00, 'USD',
         'Wire to Paul Hartman – Ref WH-60924', NULL, 'Paul Hartman', 'Deutsche Bank AG',
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2024-06-09 12:00:00', '2024-06-09 12:00:00'
  UNION ALL SELECT 36, 'PH-SEED-HKR-20240702-19', 'debit', 'payment', 'bills', 17.67, 'USD',
         '1&1 – July 2024 broadband', NULL, '1&1 Versatel GmbH', NULL,
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2024-07-02 12:00:00', '2024-07-02 12:00:00'
  UNION ALL SELECT 37, 'PH-SEED-HKR-20240802-20', 'debit', 'payment', 'bills', 17.47, 'USD',
         'Congstar – Aug 2024', NULL, 'Congstar GmbH', NULL,
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2024-08-02 12:00:00', '2024-08-02 12:00:00'
  UNION ALL SELECT 38, 'PH-SEED-HKR-20240816-21', 'debit', 'payment', 'shopping', 1320.00, 'USD',
         'Amazon.de – treadmill order', NULL, 'Amazon EU S.à r.l.', NULL,
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2024-08-16 12:00:00', '2024-08-16 12:00:00'
  UNION ALL SELECT 39, 'PH-SEED-HKR-20240829-22', 'credit', 'transfer', 'other', 32250.00, 'USD',
         'DAK Zuschuss – health allowance Aug 2024', NULL, 'DAK-Gesundheit', 'Sparkasse KölnBonn',
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2024-08-29 12:00:00', '2024-08-29 12:00:00'
  UNION ALL SELECT 40, 'PH-SEED-HKR-20240902-23', 'debit', 'payment', 'bills', 17.85, 'USD',
         'E.ON Strom – September 2024', NULL, 'E.ON Energie Deutschland', NULL,
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2024-09-02 12:00:00', '2024-09-02 12:00:00'
  UNION ALL SELECT 41, 'PH-SEED-HKR-20241002-24', 'debit', 'payment', 'bills', 17.65, 'USD',
         'Telekom Deutschland – Oct 2024', NULL, 'Telekom Deutschland GmbH', NULL,
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2024-10-02 12:00:00', '2024-10-02 12:00:00'
  UNION ALL SELECT 42, 'PH-SEED-HKR-20241102-25', 'debit', 'payment', 'bills', 17.25, 'USD',
         'Vodafone – Nov 2024', NULL, 'Vodafone GmbH', NULL,
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2024-11-02 12:00:00', '2024-11-02 12:00:00'
  UNION ALL SELECT 43, 'PH-SEED-HKR-20241118-26', 'credit', 'transfer', 'other', 59700.00, 'USD',
         'Verpflegungspauschale Nov 2024 – Muster GmbH', NULL, 'Muster GmbH HR', 'ING-DiBa AG',
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2024-11-18 12:00:00', '2024-11-18 12:00:00'
  UNION ALL SELECT 44, 'PH-SEED-HKR-20241202-27', 'debit', 'payment', 'bills', 17.46, 'USD',
         'O2 Rechnung – Dec 2024', NULL, 'O2 Germany', NULL,
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2024-12-02 12:00:00', '2024-12-02 12:00:00'
  UNION ALL SELECT 45, 'PH-SEED-HKR-20241228-28', 'debit', 'payment', 'shopping', 18270.00, 'USD',
         'Amazon.de – year-end order', NULL, 'Amazon EU S.à r.l.', NULL,
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2024-12-28 12:00:00', '2024-12-28 12:00:00'
  UNION ALL SELECT 46, 'PH-SEED-HKR-20250102-29', 'debit', 'payment', 'bills', 17.38, 'USD',
         '1&1 – Jan 2025', NULL, '1&1 Versatel GmbH', NULL,
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2025-01-02 12:00:00', '2025-01-02 12:00:00'
  UNION ALL SELECT 47, 'PH-SEED-HKR-20250107-30', 'credit', 'transfer', 'salary', 1330000.00, 'USD',
         'Gehalt Dez 2024 – Muster GmbH', NULL, 'Muster GmbH Payroll', 'Commerzbank AG',
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2025-01-07 12:00:00', '2025-01-07 12:00:00'
  UNION ALL SELECT 48, 'PH-SEED-HKR-20250202-31', 'debit', 'payment', 'bills', 17.67, 'USD',
         'Congstar – Feb 2025', NULL, 'Congstar GmbH', NULL,
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2025-02-02 12:00:00', '2025-02-02 12:00:00'
  UNION ALL SELECT 49, 'PH-SEED-HKR-20250302-32', 'debit', 'payment', 'bills', 17.86, 'USD',
         'E.ON Strom – March 2025', NULL, 'E.ON Energie Deutschland', NULL,
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2025-03-02 12:00:00', '2025-03-02 12:00:00'
  UNION ALL SELECT 50, 'PH-SEED-HKR-20250303-33', 'debit', 'payment', 'gift', 7130.00, 'USD',
         'Galeria Kaufhof – gift & collection', NULL, 'Galeria Kaufhof', NULL,
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2025-03-03 12:00:00', '2025-03-03 12:00:00'
  UNION ALL SELECT 51, 'PH-SEED-HKR-20250402-34', 'debit', 'payment', 'bills', 17.34, 'USD',
         'Stadtwerke – April 2025', NULL, 'Stadtwerke Wiesbaden', NULL,
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2025-04-02 12:00:00', '2025-04-02 12:00:00'
  UNION ALL SELECT 52, 'PH-SEED-HKR-20250502-35', 'debit', 'payment', 'bills', 17.75, 'USD',
         'Vodafone – May 2025', NULL, 'Vodafone GmbH', NULL,
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2025-05-02 12:00:00', '2025-05-02 12:00:00'
  UNION ALL SELECT 53, 'PH-SEED-HKR-20250602-36', 'debit', 'payment', 'bills', 17.55, 'USD',
         'O2 Rechnung – June 2025', NULL, 'O2 Germany', NULL,
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2025-06-02 12:00:00', '2025-06-02 12:00:00'
  UNION ALL SELECT 54, 'PH-SEED-HKR-20250627-37', 'debit', 'transfer', 'other', 17000.00, 'USD',
         'Wire to Kendra Nielsen – Ref WN-62725', NULL, 'Kendra Nielsen', 'Erste Bank Wien',
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2025-06-27 12:00:00', '2025-06-27 12:00:00'
  UNION ALL SELECT 55, 'PH-SEED-HKR-20250702-38', 'debit', 'payment', 'bills', 17.82, 'USD',
         'Telekom Deutschland – July 2025', NULL, 'Telekom Deutschland GmbH', NULL,
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2025-07-02 12:00:00', '2025-07-02 12:00:00'
  UNION ALL SELECT 56, 'PH-SEED-HKR-20250802-39', 'debit', 'payment', 'bills', 17.22, 'USD',
         '1&1 – August 2025', NULL, '1&1 Versatel GmbH', NULL,
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2025-08-02 12:00:00', '2025-08-02 12:00:00'
  UNION ALL SELECT 57, 'PH-SEED-HKR-20250829-40', 'credit', 'transfer', 'other', 37925.00, 'USD',
         'AOK Zuschuss – health Aug 2025', NULL, 'AOK Rheinland/Hamburg', 'Postbank Ndl. Bonn',
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2025-08-29 12:00:00', '2025-08-29 12:00:00'
  UNION ALL SELECT 58, 'PH-SEED-HKR-20250902-41', 'debit', 'payment', 'bills', 17.27, 'USD',
         'Congstar – Sept 2025', NULL, 'Congstar GmbH', NULL,
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2025-09-02 12:00:00', '2025-09-02 12:00:00'
  UNION ALL SELECT 59, 'PH-SEED-HKR-20250919-42', 'debit', 'payment', 'shopping', 5500.00, 'USD',
         'Parfümerie Hussong oHG – Wiesbaden', NULL, 'Parfümerie Hussong oHG', NULL,
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2025-09-19 12:00:00', '2025-09-19 12:00:00'
  UNION ALL SELECT 60, 'PH-SEED-HKR-20251002-43', 'debit', 'payment', 'bills', 17.66, 'USD',
         'E.ON Strom – Oct 2025', NULL, 'E.ON Energie Deutschland', NULL,
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2025-10-02 12:00:00', '2025-10-02 12:00:00'
  UNION ALL SELECT 61, 'PH-SEED-HKR-20251102-44', 'debit', 'payment', 'bills', 17.52, 'USD',
         'Vodafone – Nov 2025', NULL, 'Vodafone GmbH', NULL,
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2025-11-02 12:00:00', '2025-11-02 12:00:00'
  UNION ALL SELECT 62, 'PH-SEED-HKR-20251126-45', 'debit', 'payment', 'shopping', 6750.00, 'USD',
         'E-Bike Center Mainz – electric bike', NULL, 'E-Bike Center Mainz', NULL,
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2025-11-26 12:00:00', '2025-11-26 12:00:00'
  UNION ALL SELECT 63, 'PH-SEED-HKR-20251202-46', 'debit', 'payment', 'bills', 17.79, 'USD',
         'O2 Rechnung – Dec 2025', NULL, 'O2 Germany', NULL,
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2025-12-02 12:00:00', '2025-12-02 12:00:00'
  UNION ALL SELECT 64, 'PH-SEED-HKR-20251211-47', 'debit', 'payment', 'shopping', 6400.00, 'USD',
         'SportScheck – gym equipment', NULL, 'SportScheck GmbH', NULL,
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2025-12-11 12:00:00', '2025-12-11 12:00:00'
  UNION ALL SELECT 65, 'PH-SEED-HKR-20260102-48', 'debit', 'payment', 'bills', 17.85, 'USD',
         'Telekom Deutschland – Jan 2026', NULL, 'Telekom Deutschland GmbH', NULL,
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2026-01-02 12:00:00', '2026-01-02 12:00:00'
  UNION ALL SELECT 66, 'PH-SEED-HKR-20260102-49', 'credit', 'transfer', 'salary', 1680000.00, 'USD',
         'Gehalt Jan 2026 – Muster GmbH', NULL, 'Muster GmbH Payroll', 'Targobank AG',
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2026-01-02 12:00:00', '2026-01-02 12:00:00'
  UNION ALL SELECT 67, 'PH-SEED-HKR-20260107-50', 'debit', 'payment', 'shopping', 28340.00, 'USD',
         'Ford Händler Mainz – accessories', NULL, 'Ford Autohaus Mainz', NULL,
         'completed', NULL, 0.00, NULL, '127.0.0.1', '2026-01-07 12:00:00', '2026-01-07 12:00:00'
  UNION ALL SELECT 68, 'PH-SEED-HKR-20260119-51', 'debit', 'payment', 'shopping', 3920.00, 'USD',
         'Shopify store – kiddies order (declined)', NULL, 'Shopify Payments', NULL,
         'failed', NULL, 0.00, NULL, '127.0.0.1', '2026-01-19 12:00:00', NULL
  UNION ALL SELECT 69, 'PH-SEED-HKR-20260202-52', 'debit', 'payment', 'bills', 17.67, 'USD',
         'Congstar – Feb 2026 (declined)', NULL, 'Congstar GmbH', NULL,
         'failed', NULL, 0.00, NULL, '127.0.0.1', '2026-02-02 12:00:00', NULL
  UNION ALL SELECT 70, 'PH-SEED-HKR-20260227-53', 'debit', 'transfer', 'other', 25000.00, 'USD',
         'Wire to Paul Hartman – Ref WH-22726 (declined)', NULL, 'Paul Hartman', 'UBS Switzerland',
         'failed', NULL, 0.00, NULL, '127.0.0.1', '2026-02-27 12:00:00', NULL
) s
WHERE @should_seed = 1
  AND NOT EXISTS (
    SELECT 1 FROM transactions t WHERE t.transaction_ref = CONCAT(@ref_prefix, s.source_ref)
  )
ORDER BY s.created_at ASC, s.seq ASC;

