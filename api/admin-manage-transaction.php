<?php
// Prevent output before JSON
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

ob_end_clean();

header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get input
$input = json_decode(file_get_contents('php://input'), true);

$accountId = intval($input['account_id'] ?? 0);
$transactionType = $input['transaction_type'] ?? ''; // 'credit' or 'debit'
$amount = floatval($input['amount'] ?? 0);
$category = $input['category'] ?? 'other';
$description = trim($input['description'] ?? '');
$transactionDate = $input['transaction_date'] ?? date('Y-m-d');
$transactionTime = $input['transaction_time'] ?? date('H:i:s');

// Validation
if (!$accountId) {
    echo json_encode(['success' => false, 'message' => 'Account ID is required']);
    exit;
}

if (!in_array($transactionType, ['credit', 'debit'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid transaction type']);
    exit;
}

if ($amount <= 0) {
    echo json_encode(['success' => false, 'message' => 'Amount must be greater than zero']);
    exit;
}

if (empty($description)) {
    echo json_encode(['success' => false, 'message' => 'Description is required']);
    exit;
}

try {
    $db = Database::getInstance();
    
    // Get account details
    $sql = "SELECT a.*, u.full_name, u.email FROM accounts a 
            JOIN users u ON a.user_id = u.id 
            WHERE a.id = ?";
    $stmt = $db->query($sql, [$accountId]);
    $account = $stmt->fetch();
    
    if (!$account) {
        echo json_encode(['success' => false, 'message' => 'Account not found']);
        exit;
    }
    
    // Check if debit amount exceeds balance
    if ($transactionType === 'debit' && $amount > $account['balance']) {
        echo json_encode(['success' => false, 'message' => 'Insufficient balance for debit transaction']);
        exit;
    }
    
    // Update account balance
    if ($transactionType === 'credit') {
        $newBalance = $account['balance'] + $amount;
        $sql = "UPDATE accounts SET balance = balance + ?, updated_at = NOW() WHERE id = ?";
    } else {
        $newBalance = $account['balance'] - $amount;
        $sql = "UPDATE accounts SET balance = balance - ?, updated_at = NOW() WHERE id = ?";
    }
    $db->query($sql, [$amount, $accountId]);
    
    // Create transaction record
    $transactionReference = 'ADMIN-' . strtoupper(substr(md5(uniqid()), 0, 12));
    $combinedDateTime = $transactionDate . ' ' . $transactionTime;
    
    $sql = "INSERT INTO transactions (
        user_id, account_id, type, amount, currency, status, 
        reference_number, description, expense_category, 
        created_at, updated_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $db->query($sql, [
        $account['user_id'],
        $accountId,
        $transactionType,
        $amount,
        $account['currency'],
        'successful',
        $transactionReference,
        $description,
        $category,
        $combinedDateTime
    ]);
    
    // Log activity
    $actionType = strtoupper($transactionType);
    logActivity(
        $_SESSION['user_id'], 
        "ADMIN_{$actionType}", 
        "Admin {$transactionType}ed {$account['currency']} {$amount} to account {$account['account_number']}"
    );
    
    echo json_encode([
        'success' => true,
        'message' => ucfirst($transactionType) . ' transaction completed successfully',
        'new_balance' => number_format($newBalance, 2),
        'reference' => $transactionReference
    ]);
    exit;
    
} catch (Exception $e) {
    error_log('Admin Transaction Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while processing the transaction',
        'error' => $e->getMessage()
    ]);
    exit;
}

