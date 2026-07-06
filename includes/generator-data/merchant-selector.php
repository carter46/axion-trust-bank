<?php

function generatorCountryIsoFromOperating(string $operatingCountry): string
{
    if (function_exists('countryToIso2')) {
        $iso = countryToIso2($operatingCountry);
        if ($iso) {
            return strtoupper($iso);
        }
    }
    $map = [
        'Germany' => 'DE', 'United Arab Emirates' => 'AE', 'Nigeria' => 'NG',
        'United Kingdom' => 'GB', 'United States' => 'US', 'USA' => 'US',
    ];
    return $map[$operatingCountry] ?? strtoupper(substr($operatingCountry, 0, 2));
}

function loadMerchantsForCountry(string $iso): array
{
    static $cache = [];
    $iso = strtoupper($iso);
    if (isset($cache[$iso])) {
        return $cache[$iso];
    }
    $path = __DIR__ . '/merchants/' . $iso . '.php';
    $local = file_exists($path) ? require $path : [];
    $global = require __DIR__ . '/merchants/global.php';
    $cache[$iso] = array_merge($local, $global);
    return $cache[$iso];
}

function selectGeneratorMerchant(string $operatingCountry, array $preferredTags, ?string $channel, callable $rng, float $localRatio = 0.9): ?array
{
    $iso = generatorCountryIsoFromOperating($operatingCountry);
    $merchants = loadMerchantsForCountry($iso);
    if (empty($merchants)) {
        return null;
    }

    $useLocal = $rng() < $localRatio;
    $candidates = array_filter($merchants, function ($m) use ($useLocal, $channel, $preferredTags) {
        $isGlobal = !empty($m['global']);
        if ($useLocal && $isGlobal) {
            return false;
        }
        if (!$useLocal && !$isGlobal) {
            return false;
        }
        if ($channel && ($m['channel'] ?? '') !== $channel && ($m['channel'] ?? '') !== 'both') {
            if ($channel === 'online' && empty($m['global'])) {
                return false;
            }
            if ($channel === 'pos' && !empty($m['global'])) {
                return false;
            }
        }
        if (!empty($preferredTags)) {
            $tags = $m['tags'] ?? [];
            foreach ($preferredTags as $tag) {
                if (in_array($tag, $tags, true)) {
                    return true;
                }
            }
            return empty($preferredTags);
        }
        return true;
    });

    if (empty($candidates)) {
        $candidates = $merchants;
    }

    $candidates = array_values($candidates);
    $idx = (int) floor($rng() * count($candidates));
    return $candidates[$idx];
}

function merchantAmount(array $merchant, callable $rng, float $behaviourMultiplier = 1.0): float
{
    $min = (float)($merchant['amount_min'] ?? 10);
    $max = (float)($merchant['amount_max'] ?? 50);
    $amount = $min + ($rng() * ($max - $min));
    return round($amount * $behaviourMultiplier, 2);
}
