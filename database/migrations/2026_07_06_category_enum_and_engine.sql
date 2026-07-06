-- Extend expense_category enum for transfer UI parity + generator
-- Safe to re-run: checks column type before ALTER

SET @db := DATABASE();

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
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col2 := (
  SELECT COLUMN_TYPE FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'transaction_template_items' AND COLUMN_NAME = 'expense_category'
  LIMIT 1
);

SET @sql2 := IF(@col2 IS NOT NULL AND @col2 NOT LIKE '%bonus%',
  'ALTER TABLE `transaction_template_items` MODIFY COLUMN `expense_category` enum(
    ''shopping'',''food'',''transport'',''bills'',''entertainment'',''healthcare'',''travel'',
    ''education'',''salary'',''investment'',''rent'',''insurance'',''gift'',''personal'',''other'',
    ''bonus'',''refund'',''utility''
  ) DEFAULT NULL',
  'SELECT 1'
);
PREPARE stmt2 FROM @sql2; EXECUTE stmt2; DEALLOCATE PREPARE stmt2;

-- Optional engine params on batches (plan audit)
SET @exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'transaction_generation_batches' AND COLUMN_NAME = 'engine_params'
);
SET @sql3 := IF(@exists = 0,
  'ALTER TABLE `transaction_generation_batches`
     ADD COLUMN `engine_params` JSON DEFAULT NULL AFTER `template_id`,
     ADD COLUMN `plan_summary` JSON DEFAULT NULL AFTER `engine_params`',
  'SELECT 1'
);
PREPARE stmt3 FROM @sql3; EXECUTE stmt3; DEALLOCATE PREPARE stmt3;
