<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/security.php';

requireLogin();

// Check if currency conversion is enabled
$systemSettings = SystemSettings::getInstance();
$conversionEnabled = $systemSettings->get('enable_currency_conversion', '1') === '1';

if (!$conversionEnabled) {
    echo json_encode([
        'success' => false,
        'message' => 'Currency conversion is currently disabled. All amounts are displayed in the default currency.'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$currency = strtoupper(trim($input['currency'] ?? ''));
$markAsShown = isset($input['markAsShown']) && $input['markAsShown'] === true;

// Validate currency code
$validCurrencies = [
    'USD', 'EUR', 'GBP', 'JPY', 'CNY', 'INR', 'CAD', 'AUD',
    'NGN', 'ZAR', 'AED', 'SAR', 'QAR', 'KWD', 'KES', 'GHS',
    'PKR', 'BDT', 'LKR', 'SGD', 'MYR', 'THB', 'IDR', 'PHP',
    'VND', 'KRW', 'BRL', 'MXN', 'ARS', 'CLP', 'COP', 'TRY',
    'ILS', 'NZD', 'HKD', 'TWD', 'CHF', 'SEK', 'NOK', 'DKK',
    'EGP', 'MAD', 'TND', 'DZD'
];

if (empty($currency) || !in_array($currency, $validCurrencies)) {
    echo json_encode(['success' => false, 'message' => 'Invalid currency code']);
    exit;
}

try {
    $db = Database::getInstance();
    $userId = $_SESSION['user_id'];
    
    // Update user currency and mark popup as shown if requested
    if ($markAsShown) {
        // Check if currency_selection_shown column exists, if not add it
        $checkColumn = $db->query("SHOW COLUMNS FROM users LIKE 'currency_selection_shown'");
        if ($checkColumn->rowCount() == 0) {
            // Add column if it doesn't exist
            $db->query("ALTER TABLE users ADD COLUMN currency_selection_shown TINYINT(1) DEFAULT 0");
        }
        
        $sql = "UPDATE users SET currency = ?, currency_selection_shown = 1 WHERE id = ?";
        $result = $db->query($sql, [$currency, $userId]);
    } else {
        $sql = "UPDATE users SET currency = ?, currency_selection_shown = 1 WHERE id = ?";
        $result = $db->query($sql, [$currency, $userId]);
    }
    
    if ($result) {
        // Log activity
        logActivity($userId, 'currency_changed', "Changed currency preference to $currency" . ($markAsShown ? " (from first visit popup)" : ""));
        
        echo json_encode([
            'success' => true,
            'message' => 'Currency preference updated successfully',
            'currency' => $currency
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update currency']);
    }
} catch (Exception $e) {
    error_log("Currency selection error: " . $e->getMessage());
    
    // If column doesn't exist error, try to add it and retry
    if (strpos($e->getMessage(), "currency_selection_shown") !== false) {
        try {
            $db->query("ALTER TABLE users ADD COLUMN currency_selection_shown TINYINT(1) DEFAULT 0");
            // Retry the update
            $sql = "UPDATE users SET currency = ?, currency_selection_shown = 1 WHERE id = ?";
            $params = [$currency, $userId];
            $result = $db->query($sql, $params);
            
            if ($result) {
                logActivity($userId, 'currency_changed', "Changed currency preference to $currency");
                echo json_encode([
                    'success' => true,
                    'message' => 'Currency preference updated successfully',
                    'currency' => $currency
                ]);
                exit;
            }
        } catch (Exception $e2) {
            error_log("Retry currency update error: " . $e2->getMessage());
        }
    }
    
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}
