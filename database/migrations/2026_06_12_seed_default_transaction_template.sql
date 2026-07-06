/*
  Seed default transaction template (Andy reference pack).
  Idempotent: skips if slug default_checking already exists.
*/
SET @db := DATABASE();

SET @tpl_exists := (
  SELECT COUNT(*) FROM transaction_templates WHERE slug = 'default_checking'
);

INSERT INTO transaction_templates (slug, name, account_type, description, is_active)
SELECT 'default_checking', 'Default Checking History', 'checking',
       'Realistic mixed credit/debit history derived from Andy seed reference pack.',
       1
WHERE @tpl_exists = 0;

SET @template_id := (
  SELECT id FROM transaction_templates WHERE slug = 'default_checking' LIMIT 1
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
WHERE @tpl_exists = 0 AND @template_id IS NOT NULL;
