<?php

function pickGeneratorName(string $type, callable $rng): string
{
    static $pools = null;
    if ($pools === null) {
        $loaded = require __DIR__ . '/personal-names-data.php';
        $pools = is_array($loaded) ? $loaded : ['personal' => [], 'business' => []];
    }
    $pool = $pools[$type === 'business' ? 'business' : 'personal'] ?? $pools['personal'];
    if (empty($pool)) {
        return $type === 'business' ? 'Acme Supplies Ltd' : 'John Smith';
    }
    return $pool[(int) floor($rng() * count($pool))];
}

function generateDomesticAccountNumber(string $countryIso, callable $rng): string
{
    if (!function_exists('getDomesticAccountNumberRules')) {
        require_once dirname(__DIR__) . '/transfer-rails.php';
    }
    $rules = getDomesticAccountNumberRules($countryIso);
    $min = (int)($rules['min'] ?? 8);
    $max = (int)($rules['max'] ?? 12);
    $len = $min + (int) floor($rng() * max(1, $max - $min + 1));
    $num = '';
    for ($i = 0; $i < $len; $i++) {
        $num .= (string) random_int(0, 9);
    }
    return $num;
}
