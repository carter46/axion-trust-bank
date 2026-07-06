<?php

require_once __DIR__ . '/merchant-selector.php';

function getGeneratorPersonas(): array
{
    static $personas = null;
    if ($personas === null) {
        $personas = require __DIR__ . '/personas.php';
    }
    return $personas;
}

function getGeneratorPersonaById(?string $id): ?array
{
    if (!$id) {
        return null;
    }
    foreach (getGeneratorPersonas() as $persona) {
        if ($persona['id'] === $id) {
            return $persona;
        }
    }
    return null;
}

function getGeneratorPresets(): array
{
    return [
        ['id' => 'active_personal', 'label' => 'Active Personal Account', 'persona_id' => '', 'account_style' => 'personal', 'financial_behaviour' => 'active_spender', 'volume' => 'high'],
        ['id' => 'salary_earner', 'label' => 'Salary Earner', 'persona_id' => 'salaried_uae', 'account_style' => 'personal', 'financial_behaviour' => 'average', 'volume' => 'medium'],
        ['id' => 'small_business', 'label' => 'Small Business', 'persona_id' => 'business_ng', 'account_style' => 'business', 'financial_behaviour' => 'average', 'volume' => 'medium'],
        ['id' => 'intl_client', 'label' => 'International Client', 'persona_id' => 'investor_uk', 'account_style' => 'investor', 'financial_behaviour' => 'intl_traveller', 'volume' => 'high'],
        ['id' => 'wealth_client', 'label' => 'Wealth Management Client', 'persona_id' => 'luxury_personal_ae', 'account_style' => 'investor', 'financial_behaviour' => 'luxury', 'volume' => 'high'],
        ['id' => 'dormant', 'label' => 'Dormant Account', 'persona_id' => 'dormant_personal', 'account_style' => 'personal', 'financial_behaviour' => 'conservative', 'volume' => 'low'],
        ['id' => 'premium', 'label' => 'Premium Customer', 'persona_id' => 'luxury_personal_ae', 'account_style' => 'personal', 'financial_behaviour' => 'luxury', 'volume' => 'high'],
    ];
}

function getGeneratorSeasonality(): array
{
    static $data = null;
    if ($data === null) {
        $data = require __DIR__ . '/seasonality.php';
    }
    return $data;
}

function getSeasonalTagBoosts(int $month): array
{
    $seasonality = getGeneratorSeasonality();
    return $seasonality[$month] ?? ['boost' => [], 'reduce' => []];
}

/** Base monthly salary range by account style (US-normalized reference). */
function getGeneratorStyleSalaryBase(string $style): array
{
    $map = [
        'personal' => [2500, 6500],
        'business' => [8000, 25000],
        'investor' => [5000, 18000],
        'student' => [600, 1500],
    ];
    return $map[$style] ?? $map['personal'];
}

/** Scale salary amounts for the bank's operating country. */
function getGeneratorCountrySalaryMultiplier(string $operatingCountry): float
{
    $iso = generatorCountryIsoFromOperating($operatingCountry);
    $map = [
        'US' => 1.0,
        'GB' => 0.92,
        'DE' => 0.95,
        'AE' => 1.3,
        'NG' => 0.4,
    ];
    return $map[$iso] ?? 1.0;
}

/** Persona salary band multiplier relative to style baseline. */
function getGeneratorSalaryBandMultiplier(?string $band): float
{
    if ($band === null || $band === '') {
        return 1.0;
    }
    $map = [
        'minimal' => 0.45,
        'entry' => 0.75,
        'standard' => 1.0,
        'executive' => 1.55,
        'premium' => 2.1,
    ];
    return $map[$band] ?? 1.0;
}

/**
 * Salary range scaled to operating country + optional persona band.
 */
function resolvePersonaSalaryRange(?array $persona, string $style, string $operatingCountry): array
{
    $base = getGeneratorStyleSalaryBase($style);
    $countryMult = getGeneratorCountrySalaryMultiplier($operatingCountry);
    $bandMult = getGeneratorSalaryBandMultiplier($persona['salary_band'] ?? null);

    return [
        round($base[0] * $countryMult * $bandMult, 2),
        round($base[1] * $countryMult * $bandMult, 2),
    ];
}
