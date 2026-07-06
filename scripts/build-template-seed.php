<?php
/**
 * Build Section 8 template seed snippet from Andy seed file.
 * Output is a standalone snippet — copy into 2026_03_19_safe_schema_upgrade.sql Section 8 when updating.
 */
$source = __DIR__ . '/../database/migrations/2026_03_19_seed_andy_transactions_from_online.sql';
$content = file_get_contents($source);

if (!preg_match('/FROM \(\s*(SELECT[\s\S]+?)\)\s*s\s*\)\s*;/', $content, $blockMatch)) {
    fwrite(STDERR, "Could not locate source transaction block\n");
    exit(1);
}

$block = $blockMatch[1];
$chunks = preg_split('/\n\s*UNION ALL SELECT\s+/i', $block);
$rows = [];

foreach ($chunks as $i => $chunk) {
    $chunk = preg_replace('/^SELECT\s+/i', '', trim($chunk));
    if (!preg_match('/^(\d+)\s*,/s', $chunk)) {
        continue;
    }
    if (!preg_match('/^(\d+)\s*,\s*\'([^\']+)\'\s*,\s*\'(credit|debit)\'\s*,\s*\'([^\']+)\'\s*,\s*(?:\'([^\']*)\'|NULL)\s*,\s*([0-9.]+)\s*,\s*\'USD\'\s*,\s*\n\s*\'((?:[^\']|\'\')*)\'\s*,\s*(?:\'([^\']*)\'|NULL)\s*,\s*(?:\'([^\']*)\'|NULL)\s*,\s*(?:\'([^\']*)\'|NULL)\s*,\s*\n\s*\'(completed|failed)\'\s*,\s*(?:NULL|[\'"][^\'"]*[\'"])\s*,\s*([0-9.]+)/s', $chunk, $m)) {
        fwrite(STDERR, "Failed to parse chunk starting with seq in: " . substr($chunk, 0, 80) . "...\n");
        continue;
    }
    $rows[] = [
        'seq' => (int)$m[1],
        'type' => $m[3],
        'cat' => $m[4],
        'exp' => $m[5] !== '' ? $m[5] : null,
        'amt' => $m[6],
        'desc' => str_replace("''", "'", $m[7]),
        'racct' => $m[8] !== '' ? $m[8] : null,
        'rname' => $m[9] !== '' ? $m[9] : null,
        'rbank' => $m[10] !== '' ? $m[10] : null,
        'status' => $m[11],
        'fee' => $m[12],
    ];
}

usort($rows, fn($a, $b) => $a['seq'] <=> $b['seq']);

$esc = static function ($v) {
    if ($v === null) {
        return 'NULL';
    }
    return "'" . str_replace("'", "''", $v) . "'";
};

$out = <<<'HDR'
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
HDR;

$rowParts = [];
foreach ($rows as $r) {
    $rowParts[] = sprintf(
        "  SELECT %d AS sort_order, '%s' AS transaction_type, '%s' AS category, %s AS expense_category, %s AS base_amount, %s AS description, %s AS recipient_account, %s AS recipient_name, %s AS recipient_bank, '%s' AS status, %s AS fee, 1 AS weight",
        $r['seq'],
        $r['type'],
        $r['cat'],
        $esc($r['exp']),
        $r['amt'],
        $esc($r['desc']),
        $esc($r['racct']),
        $esc($r['rname']),
        $esc($r['rbank']),
        $r['status'],
        $r['fee']
    );
}

$out .= "\n" . implode("\n  UNION ALL\n", $rowParts);
$out .= "\n) src\nWHERE @tpl_exists = 0 AND @template_id IS NOT NULL;\n";

$dest = __DIR__ . '/../database/migrations/_snippet_section_8_template_seed.sql';
file_put_contents($dest, $out);
echo 'Wrote ' . count($rows) . " items to $dest\n";
