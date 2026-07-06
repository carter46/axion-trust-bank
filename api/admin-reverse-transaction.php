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

// Get input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit;
}

$transactionId = intval($input['transaction_id'] ?? 0);

if (!$transactionId) {
    echo json_encode(['success' => false, 'message' => 'Transaction ID required']);
    exit;
}

try {
    $db = Database::getInstance();
    
    // Check if transaction exists and is completed
    $sql = "SELECT t.*, u.email as user_email FROM transactions t 
            JOIN users u ON t.user_id = u.id 
            WHERE t.id = ? AND t.status = 'completed' AND u.role != 'admin'";
    $stmt = $db->query($sql, [$transactionId]);
    $transaction = $stmt->fetch();
    
    if (!$transaction) {
        echo json_encode(['success' => false, 'message' => 'Transaction not found or cannot be reversed']);
        exit;
    }
    
    // Start transaction
    $db->beginTransaction();
    
    try {
        // Calculate reverse amount
        $reverseAmount = $transaction['amount'];
        $reverseType = ($transaction['transaction_type'] === 'credit') ? 'debit' : 'credit';
        
        // Update account balance
        if ($transaction['transaction_type'] === 'credit') {
            $balanceChange = -$reverseAmount; // Subtract the original credit
        } else {
            $balanceChange = $reverseAmount; // Add back the original debit
        }
        
        // Update account balance
        $sql = "UPDATE accounts SET 
                balance = balance + ?, 
                available_balance = available_balance + ?,
                updated_at = NOW()
                WHERE id = ?";
        $db->query($sql, [$balanceChange, $balanceChange, $transaction['account_id']]);
        
        // Get new balance
        $sql = "SELECT balance FROM accounts WHERE id = ?";
        $stmt = $db->query($sql, [$transaction['account_id']]);
        $newBalance = $stmt->fetch()['balance'];
        
        // Create reversal transaction
        $reversalRef = 'REV' . $transaction['transaction_ref'];
        $sql = "INSERT INTO transactions (
                    transaction_ref, user_id, account_id, transaction_type, category, 
                    amount, currency, balance_before, balance_after, description, 
                    status, fee, metadata, ip_address, created_at, completed_at
                ) VALUES (?, ?, ?, ?, 'reversal', ?, ?, ?, ?, ?, 'completed', 0, ?, ?, NOW(), NOW())";
        
        $metadata = json_encode([
            'admin_action' => true,
            'admin_id' => $_SESSION['user_id'],
            'original_transaction_id' => $transactionId,
            'reversal_reason' => 'Admin reversal'
        ]);
        
        $db->query($sql, [
            $reversalRef, $transaction['user_id'], $transaction['account_id'], $reverseType,
            $reverseAmount, $transaction['currency'], $transaction['balance_after'], $newBalance,
            'Reversal of transaction ' . $transaction['transaction_ref'],
            $metadata, $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ]);
        
        // Mark original transaction as reversed
        $sql = "UPDATE transactions SET 
                status = 'reversed', 
                updated_at = NOW()
                WHERE id = ?";
        $db->query($sql, [$transactionId]);
        
        // Commit transaction
        $db->commit();
        
        // Log activity
        logActivity($_SESSION['user_id'], 'ADMIN_REVERSE_TRANSACTION', 
            "Reversed transaction {$transaction['transaction_ref']} for user {$transaction['user_email']}");
        
        echo json_encode([
            'success' => true,
            'message' => 'Transaction reversed successfully'
        ]);
        
    } catch (Exception $e) {
        $db->rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    error_log('Admin Reverse Transaction Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while reversing the transaction'
    ]);
}
