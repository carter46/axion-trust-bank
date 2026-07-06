<?php
// Prevent any output before JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);
ob_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/security.php';

// Clear any accidental output
ob_end_clean();

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Block restricted users from money-moving actions
if (function_exists('isRestrictedStatus') && isRestrictedStatus($_SESSION['restricted_status'] ?? '')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => restrictedAccountMessage()]);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit;
}

$accountId = intval($input['account_id'] ?? 0);
$destinationAccountId = intval($input['destination_account_id'] ?? null);

if (!$accountId) {
    echo json_encode(['success' => false, 'message' => 'Account ID required']);
    exit;
}

try {
    $db = Database::getInstance();
    $userId = $_SESSION['user_id'];
    
    // Start transaction
    $db->beginTransaction();
    
    // Get account to close
    $sql = "SELECT * FROM accounts WHERE id = ? AND user_id = ? AND status = 'active'";
    $stmt = $db->query($sql, [$accountId, $userId]);
    $account = $stmt->fetch();
    
    if (!$account) {
        $db->rollback();
        echo json_encode(['success' => false, 'message' => 'Account not found or already closed']);
        exit;
    }
    
    $balance = floatval($account['balance']);
    
    // Check how many active accounts the user has
    $sql = "SELECT COUNT(*) as count FROM accounts WHERE user_id = ? AND status = 'active'";
    $stmt = $db->query($sql, [$userId]);
    $result = $stmt->fetch();
    $activeAccountsCount = intval($result['count']);
    
    // Cannot close if only one account
    if ($activeAccountsCount <= 1) {
        $db->rollback();
        echo json_encode(['success' => false, 'message' => 'Cannot close your last active account']);
        exit;
    }
    
    // If account has balance, transfer funds
    if ($balance > 0) {
        if (!$destinationAccountId) {
            $db->rollback();
            echo json_encode(['success' => false, 'message' => 'Destination account required when account has balance']);
            exit;
        }
        
        // Verify destination account
        $sql = "SELECT * FROM accounts WHERE id = ? AND user_id = ? AND status = 'active' AND id != ?";
        $stmt = $db->query($sql, [$destinationAccountId, $userId, $accountId]);
        $destinationAccount = $stmt->fetch();
        
        if (!$destinationAccount) {
            $db->rollback();
            echo json_encode(['success' => false, 'message' => 'Invalid destination account']);
            exit;
        }
        
        // Get balances before transfer
        $fromBalanceBefore = $balance;
        $toBalanceBefore = floatval($destinationAccount['balance']);
        $toBalanceAfter = $toBalanceBefore + $balance;
        
        // Update destination account balance
        $sql = "UPDATE accounts SET balance = ?, available_balance = ?, updated_at = NOW() WHERE id = ?";
        $db->query($sql, [$toBalanceAfter, $toBalanceAfter, $destinationAccountId]);
        
        // Create transaction references
        $transactionRef = 'CLS' . date('YmdHis') . rand(100, 999);
        $fromTransactionRef = $transactionRef . '-D';
        $toTransactionRef = $transactionRef . '-C';
        $fullDateTime = date('Y-m-d H:i:s');
        
        // Create debit transaction (from account being closed)
        $sql = "INSERT INTO transactions (
                    transaction_ref, user_id, account_id, transaction_type, category, expense_category,
                    amount, currency, balance_before, balance_after, description, 
                    recipient_account, recipient_name, recipient_bank,
                    status, fee, metadata, ip_address, created_at, completed_at
                ) VALUES (?, ?, ?, 'debit', 'transfer', 'transfer', ?, ?, ?, 0, ?, ?, ?, ?, 'completed', 0, ?, ?, ?, ?)";
        
        $metadata = json_encode([
            'account_closed' => true,
            'transfer_to_account_id' => $destinationAccountId,
            'transfer_to_account_number' => $destinationAccount['account_number'],
            'user_action' => true
        ]);
        
        $description = 'Account closure - Funds transferred to ' . $destinationAccount['account_type'] . ' account ' . $destinationAccount['account_number'];
        
        $db->query($sql, [
            $fromTransactionRef, $userId, $accountId, 
            $balance, $account['currency'], $fromBalanceBefore,
            $description,
            $destinationAccount['account_number'], $destinationAccount['account_type'], 'Account Transfer',
            $metadata, $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0', $fullDateTime, $fullDateTime
        ]);
        
        // Create credit transaction (to destination account)
        $sql = "INSERT INTO transactions (
                    transaction_ref, user_id, account_id, transaction_type, category, expense_category,
                    amount, currency, balance_before, balance_after, description, 
                    recipient_account, recipient_name, recipient_bank,
                    status, fee, metadata, ip_address, created_at, completed_at
                ) VALUES (?, ?, ?, 'credit', 'transfer', 'transfer', ?, ?, ?, ?, ?, ?, ?, ?, 'completed', 0, ?, ?, ?, ?)";
        
        $metadata = json_encode([
            'account_closed' => true,
            'transfer_from_account_id' => $accountId,
            'transfer_from_account_number' => $account['account_number'],
            'user_action' => true
        ]);
        
        $description = 'Account closure transfer - Funds received from ' . $account['account_type'] . ' account ' . $account['account_number'];
        
        $db->query($sql, [
            $toTransactionRef, $userId, $destinationAccountId,
            $balance, $destinationAccount['currency'], $toBalanceBefore, $toBalanceAfter,
            $description,
            $account['account_number'], $account['account_type'], 'Account Transfer',
            $metadata, $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0', $fullDateTime, $fullDateTime
        ]);
        
        // Set account balance to 0 before closing
        $sql = "UPDATE accounts SET balance = 0, available_balance = 0, updated_at = NOW() WHERE id = ?";
        $db->query($sql, [$accountId]);
    }
    
    // Close the account
    $sql = "UPDATE accounts SET status = 'closed', closed_at = NOW(), updated_at = NOW() WHERE id = ?";
    $result = $db->query($sql, [$accountId]);
    
    if (!$result) {
        $db->rollback();
        echo json_encode(['success' => false, 'message' => 'Failed to close account']);
        exit;
    }
    
    // Log activity
    logActivity($userId, 'ACCOUNT_CLOSED', "Account {$account['account_number']} closed" . ($balance > 0 ? " - Funds transferred to account {$destinationAccount['account_number']}" : ''));
    
    // Commit transaction
    if (!$db->commit()) {
        throw new Exception('Failed to commit transaction');
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Account closed successfully' . ($balance > 0 ? ' - Funds transferred' : ''),
        'transaction_ref' => isset($transactionRef) ? $transactionRef : null
    ]);
    
} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollback();
    }
    error_log('Close Account Error: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
    
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while closing account: ' . $e->getMessage()
    ]);
}
