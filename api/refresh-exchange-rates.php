<?php
/**
 * API Endpoint to refresh exchange rates
 * Can be called via cron job every 60 seconds
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/exchange-rates.php';

header('Content-Type: application/json');

try {
    $base = defined('DEFAULT_CURRENCY') ? DEFAULT_CURRENCY : 'USD';
    $success = ExchangeRates::getInstance()->refreshRates($base);
    
    if ($success) {
        echo json_encode([
            'success' => true,
            'message' => 'Exchange rates refreshed successfully',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to refresh exchange rates',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

