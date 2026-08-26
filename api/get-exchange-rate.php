<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/exchange-rates.php';

header('Content-Type: application/json');

$fromCurrency = strtoupper(trim($_GET['from'] ?? DEFAULT_CURRENCY));
$toCurrency = strtoupper(trim($_GET['to'] ?? DEFAULT_CURRENCY));

if (!preg_match('/^[A-Z]{3}$/', $fromCurrency) || !preg_match('/^[A-Z]{3}$/', $toCurrency)) {
    jsonResponse(['success' => false, 'message' => 'Invalid currency codes'], 400);
}

try {
    $rate = ExchangeRates::getInstance()->getRate($fromCurrency, $toCurrency);

    if ($rate === null || $rate <= 0) {
        jsonResponse([
            'success' => false,
            'message' => 'Exchange rate unavailable for ' . $fromCurrency . ' → ' . $toCurrency,
            'from' => $fromCurrency,
            'to' => $toCurrency,
        ], 503);
    }

    jsonResponse([
        'success' => true,
        'from' => $fromCurrency,
        'to' => $toCurrency,
        'rate' => $rate,
    ]);
} catch (Exception $e) {
    error_log('Exchange Rate API Error: ' . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Failed to fetch exchange rate',
    ], 500);
}
