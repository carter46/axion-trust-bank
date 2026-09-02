<?php
// Prevent any output before JSON
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Clear any accidental output
ob_end_clean();

header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$transactionId = intval($_GET['id'] ?? 0);

if (!$transactionId) {
    echo json_encode(['success' => false, 'message' => 'Transaction ID required']);
    exit;
}

try {
    $db = Database::getInstance();
    
    // Get transaction details
    $sql = "SELECT t.*, u.email as user_email, u.full_name as user_name 
            FROM transactions t 
            JOIN users u ON t.user_id = u.id 
            WHERE t.id = ? AND u.role != 'admin'";
    $stmt = $db->query($sql, [$transactionId]);
    $transaction = $stmt->fetch();
    
    if (!$transaction) {
        echo json_encode(['success' => false, 'message' => 'Transaction not found']);
        exit;
    }

    enforceDemoUserAdminAccessForUserId((int)$transaction['user_id']);
    
    echo json_encode([
        'success' => true,
        'transaction' => $transaction
    ]);
    
} catch (Exception $e) {
    error_log('Admin Get Transaction Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while fetching the transaction'
    ]);
}

