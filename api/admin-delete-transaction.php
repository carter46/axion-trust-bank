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
$reason = trim($input['reason'] ?? '');

if (!$transactionId) {
    echo json_encode(['success' => false, 'message' => 'Transaction ID required']);
    exit;
}

if (empty($reason)) {
    echo json_encode(['success' => false, 'message' => 'Reason is required for deletion']);
    exit;
}

try {
    $db = Database::getInstance();
    
    // Check if transaction exists
    $sql = "SELECT t.*, u.email as user_email FROM transactions t 
            JOIN users u ON t.user_id = u.id 
            WHERE t.id = ? AND u.role != 'admin'";
    $stmt = $db->query($sql, [$transactionId]);
    $transaction = $stmt->fetch();
    
    if (!$transaction) {
        echo json_encode(['success' => false, 'message' => 'Transaction not found']);
        exit;
    }
    
    // Admin can delete any transaction regardless of status
    // No status restriction for admin deletion
    
    // Start transaction
    $db->beginTransaction();
    
    try {
        $balanceDeltas = adminComputeDeletionBalanceDeltas($db, [$transaction]);
        $balanceChange = $balanceDeltas[(int)$transaction['account_id']] ?? 0.0;
        $batchId = adminParseGeneratorBatchId($transaction);

        if (abs($balanceChange) >= 0.01) {
            $sql = "SELECT balance FROM accounts WHERE id = ? FOR UPDATE";
            $stmt = $db->query($sql, [$transaction['account_id']]);
            $account = $stmt->fetch();

            if ($account) {
                $newBalance = round((float)$account['balance'] + $balanceChange, 2);
                if ($newBalance < 0) {
                    throw new Exception('Cannot delete transaction: reversal would result in negative balance');
                }
            }

            $sql = "UPDATE accounts SET 
                    balance = balance + ?, 
                    available_balance = available_balance + ?,
                    updated_at = NOW()
                    WHERE id = ?";
            $db->query($sql, [$balanceChange, $balanceChange, $transaction['account_id']]);
        }

        // Delete the transaction
        $sql = "DELETE FROM transactions WHERE id = ?";
        $db->query($sql, [$transactionId]);

        if ($batchId) {
            adminMarkEmptyGeneratorBatchesUndone($db, [$batchId]);
        }

        // Commit transaction
        $db->commit();

        // Log activity
        logActivity($_SESSION['user_id'], 'ADMIN_DELETE_TRANSACTION', 
            "Deleted transaction {$transaction['transaction_ref']} for user {$transaction['user_email']}. Reason: {$reason}");

        echo json_encode([
            'success' => true,
            'message' => 'Transaction deleted successfully',
            'balance_adjusted' => round($balanceChange, 2),
        ]);
        
    } catch (Exception $e) {
        $db->rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    error_log('Admin Delete Transaction Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while deleting the transaction'
    ]);
}
