<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/exchange-rates.php';

header('Content-Type: application/json');

$fromCurrency = $_GET['from'] ?? DEFAULT_CURRENCY;
$toCurrency = $_GET['to'] ?? DEFAULT_CURRENCY;

// Validate currencies
if (!preg_match('/^[A-Z]{3}$/', $fromCurrency) || !preg_match('/^[A-Z]{3}$/', $toCurrency)) {
    jsonResponse(['success' => false, 'message' => 'Invalid currency codes'], 400);
}

try {
    $rate = ExchangeRates::getInstance()->getRate($fromCurrency, $toCurrency);
    
    jsonResponse([
        'success' => true,
        'from' => $fromCurrency,
        'to' => $toCurrency,
        'rate' => $rate
    ]);
} catch (Exception $e) {
    error_log("Exchange Rate API Error: " . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Failed to fetch exchange rate',
        'rate' => 1.0 // Fallback to 1.0
    ], 500);
}
