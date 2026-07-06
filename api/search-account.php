<?php
header('Content-Type: application/json');

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/security.php';

// Check if user is logged in (for registration, we might not be logged in yet)
// But we'll allow this for registration flow
$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);

if (!$input || empty($input['account_number'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Account number is required']);
    exit;
}

$accountNumber = Security::sanitize($input['account_number']);

try {
    require_once __DIR__ . '/../models/JointAccount.php';
    $jointAccount = new JointAccount();
    $accountInfo = $jointAccount->searchAccount($accountNumber);
    
    if ($accountInfo) {
        echo json_encode([
            'success' => true,
            'account_id' => $accountInfo['account_id'],
            'account_number' => $accountInfo['account_number'],
            'account_type' => $accountInfo['account_type'],
            'owner_name' => $accountInfo['owner_name'],
            'owner_email' => $accountInfo['owner_email']
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Account not found or cannot be joined']);
    }
} catch (Exception $e) {
    error_log("Search account error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error searching for account']);
}

