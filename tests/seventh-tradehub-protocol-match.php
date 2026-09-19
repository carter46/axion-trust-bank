<?php
/**
 * CLI: php tests/seventh-tradehub-protocol-match.php
 * Proves Hub protocol skip matches only exact health/sync/consume paths.
 */
require_once __DIR__ . '/../includes/seventh-tradehub.php';

function hubAssertMatch(array $server, array $get, bool $expect, string $label): void
{
    $got = seventhTradeHubHubProtocolMatch($server, $get, false);
    $matched = !empty($got['matched']);
    if ($matched !== $expect) {
        fwrite(STDERR, "FAIL {$label}: expected " . ($expect ? 'skip' : 'no-skip')
            . ' got ' . json_encode($got) . PHP_EOL);
        exit(1);
    }
    echo "OK {$label}\n";
}

$mustSkip = [
    ['REQUEST_URI' => '/api/7th-tradehub/v1/health', 'SCRIPT_NAME' => '/index.php'],
    ['REQUEST_URI' => '/api/7th-tradehub/v1/health/', 'SCRIPT_NAME' => '/index.php'],
    ['REQUEST_URI' => '/api/7th-tradehub/v1/subscription/sync', 'SCRIPT_NAME' => '/index.php'],
    ['REQUEST_URI' => '/auth/7th-tradehub/demo/consume?token=x', 'SCRIPT_NAME' => '/auth/7th-tradehub/demo/consume.php'],
    ['REQUEST_URI' => '/foo', 'SCRIPT_NAME' => '/api/7th-tradehub/v1/health.php'],
];
foreach ($mustSkip as $i => $server) {
    hubAssertMatch($server, [], true, 'skip-' . $i . ' ' . $server['REQUEST_URI']);
}

$mustNot = [
    '/',
    '/dashboard',
    '/auth/login',
    '/admin',
    '/anything-containing-health.php-in-text',
    '/help',
];
foreach ($mustNot as $path) {
    hubAssertMatch(
        ['REQUEST_URI' => $path, 'SCRIPT_NAME' => '/index.php', 'PHP_SELF' => '/index.php', 'REDIRECT_URL' => $path],
        ['route' => ltrim($path, '/')],
        false,
        'open ' . $path
    );
}

$skipDefine = seventhTradeHubHubProtocolMatch(['REQUEST_URI' => '/'], [], true);
if (empty($skipDefine['matched']) || ($skipDefine['via'] ?? '') !== 'skip_define') {
    fwrite(STDERR, "FAIL skip_define\n");
    exit(1);
}
echo "OK skip_define\n";
echo "All protocol-match checks passed.\n";
exit(0);
