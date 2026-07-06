<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in response

// Set error handler to catch any PHP errors
set_error_handler(function($severity, $message, $file, $line) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'PHP Error: ' . $message . ' in ' . $file . ' on line ' . $line]);
    exit;
});

try {
    require_once __DIR__ . '/../config/config.php';
    require_once __DIR__ . '/../includes/functions.php';
    require_once __DIR__ . '/../models/Account.php';
    require_once __DIR__ . '/../models/Card.php';
    require_once __DIR__ . '/../models/Transaction.php';
    require_once __DIR__ . '/../models/CardTransaction.php';
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Configuration error: ' . $e->getMessage()]);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Block restricted users from money-moving actions
if (function_exists('isRestrictedStatus') && isRestrictedStatus($_SESSION['restricted_status'] ?? '')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => restrictedAccountMessage()]);
    exit;
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid JSON input');
    }
    
    // Validate required fields
    $cardId = $input['card_id'] ?? null;
    $sourceAccountId = $input['source_account_id'] ?? null;
    $amount = $input['amount'] ?? null;
    $description = $input['description'] ?? 'Card funding';
    
    // Get user ID first
    $userId = $_SESSION['user_id'];
    
    $cardModel = new Card();
    
    if (!$cardId || !$sourceAccountId || !$amount) {
        throw new Exception('Missing required fields: card_id=' . ($cardId ?? 'null') . ', source_account_id=' . ($sourceAccountId ?? 'null') . ', amount=' . ($amount ?? 'null'));
    }
    
    $amount = floatval($amount);
    if ($amount <= 0) {
        throw new Exception('Amount must be greater than 0');
    }
    
    // Start database transaction
    $db = Database::getInstance();
    
    $testQuery = $db->query("SELECT 1 as test");
    if (!$testQuery) {
        throw new Exception('Database connection failed');
    }
    
    $cardTransactionModel = new CardTransaction();
    $accountModel = new Account();
    $transactionModel = new Transaction();
    
    $db->beginTransaction();
    
    try {
        // Verify card belongs to user
        $card = $cardModel->findById($cardId);
        
        if ($card === false) {
            throw new Exception('Database error while looking up card');
        }
        
        if (!$card) {
            throw new Exception('Card not found. Card ID: ' . $cardId);
        }
        
        if ($card['user_id'] != $userId) {
            throw new Exception('Card access denied. Card belongs to user: ' . $card['user_id'] . ', requesting user: ' . $userId);
        }
        
        // Verify source account belongs to user and has sufficient balance
        $accountModel = new Account();
        $sourceAccount = $accountModel->findById($sourceAccountId);
        
        if (!$sourceAccount || $sourceAccount['user_id'] != $userId) {
            throw new Exception('Source account not found or access denied');
        }
        
        if ($sourceAccount['balance'] < $amount) {
            throw new Exception('Insufficient balance in source account');
        }
        
        // Get current balances before making changes
        $sourceBalanceBefore = $sourceAccount['balance'];
        $cardBalanceBefore = $card['balance'] ?? 0;
        
        // Calculate new balances
        $sourceBalanceAfter = $sourceBalanceBefore - $amount;
        $cardBalanceAfter = $cardBalanceBefore + $amount;
        
        // Deduct from source account
        $balanceUpdateResult = $accountModel->updateBalance($sourceAccountId, $amount, 'debit');
        
        // Note: Card balance will be updated by CardTransaction model
        
        // Create transaction record for source account (debit)
        $transactionModel = new Transaction();
        $sourceTransaction = $transactionModel->create([
            'user_id' => $userId,
            'account_id' => $sourceAccountId,
            'transaction_type' => 'debit',
            'amount' => $amount,
            'category' => 'transfer',
            'expense_category' => 'card_funding',
            'description' => $description . ' - Card funding',
            'status' => 'completed',
            'metadata' => [
                'card_id' => $cardId,
                'funding_type' => 'card_funding'
            ]
        ]);
        
        // Create card transaction record (credit) - this will show in card transactions
        $cardTransactionModel = new CardTransaction();
        $cardTransaction = $cardTransactionModel->create([
            'card_id' => $cardId,
            'user_id' => $userId,
            'transaction_type' => 'credit',
            'amount' => $amount,
            'category' => 'deposit',
            'description' => $description . ' - Card funding received',
            'status' => 'completed',
            'metadata' => [
                'source_account_id' => $sourceAccountId,
                'funding_type' => 'card_funding',
                'source_account_number' => $sourceAccount['account_number']
            ]
        ]);
        
        // Log activity
        if (function_exists('logActivity')) {
            logActivity($userId, 'CARD_FUNDING', "Added $amount to card from account {$sourceAccount['account_number']}");
        }
        
        // Commit transaction
        $db->commit();
        
        // Get updated account balance
        $updatedAccount = $accountModel->findById($sourceAccountId);
        
        echo json_encode([
            'success' => true,
            'message' => 'Funds added successfully',
            'data' => [
                'card_id' => $cardId,
                'amount' => $amount,
                'new_card_balance' => $cardTransaction['balance_after'] ?? $cardBalanceAfter,
                'new_source_balance' => $sourceBalanceAfter,
                'transaction_ref' => $sourceTransaction['transaction_ref'] ?? null,
                'card_transaction_ref' => $cardTransaction['transaction_ref'] ?? null
            ]
        ]);
        
    } catch (Exception $e) {
        $db->rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    error_log("Card funding error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
} catch (Error $e) {
    error_log("Card funding PHP error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'A system error occurred.'
    ]);
}
?>
