<?php
// Prevent any output before JSON - Critical: Must be first!
@ini_set('display_errors', 0);
@error_reporting(0);
ob_start();

try {
    require_once __DIR__ . '/../config/config.php';
    require_once __DIR__ . '/../includes/functions.php';
    require_once __DIR__ . '/../includes/security.php';
    
    // Clear any accidental output before headers
    $output = ob_get_clean();
    if (!empty($output)) {
        error_log("admin-adjust-balance.php: Unexpected output before headers: " . substr($output, 0, 200));
    }
    
    // Set JSON header
    header('Content-Type: application/json; charset=UTF-8');
    header('X-Content-Type-Options: nosniff');
} catch (Exception $e) {
    ob_end_clean();
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(['success' => false, 'message' => 'Setup error: ' . $e->getMessage()]);
    exit;
} catch (Error $e) {
    ob_end_clean();
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(['success' => false, 'message' => 'Fatal setup error: ' . $e->getMessage()]);
    exit;
} catch (Throwable $e) {
    ob_end_clean();
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(['success' => false, 'message' => 'Unexpected error: ' . $e->getMessage()]);
    exit;
}

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

/**
 * Handle internal account-to-account transfer (between user's own accounts)
 * Creates two transactions: debit from source account, credit to destination account
 */
function handleInternalAdjustment($input) {
    try {
        $db = Database::getInstance();
        if (!$db) {
            throw new Exception('Database connection failed');
        }
        
        $userId = intval($input['user_id'] ?? 0);
        $fromAccountId = intval($input['from_account_id'] ?? 0);
        $toAccountId = intval($input['to_account_id'] ?? 0);
        $amount = floatval($input['amount'] ?? 0);
        $expenseCategory = Security::sanitize($input['expense_category'] ?? 'transfer');
        $transactionDate = Security::sanitize($input['transaction_date'] ?? date('Y-m-d'));
        $transactionTime = Security::sanitize($input['transaction_time'] ?? date('H:i:s'));
        $description = Security::sanitize($input['description'] ?? 'Internal transfer between accounts');
        
        // Validation
        if (!$userId || !$fromAccountId || !$toAccountId || !$amount) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields: user_id, from_account_id, to_account_id, amount']);
            exit;
        }

        $demoAccessDenied = denyDemoUserAdminAccessJson($userId);
        if ($demoAccessDenied) {
            echo json_encode($demoAccessDenied);
            exit;
        }
        
        if ($amount <= 0) {
            echo json_encode(['success' => false, 'message' => 'Amount must be greater than 0']);
            exit;
        }
        
        if ($fromAccountId === $toAccountId) {
            echo json_encode(['success' => false, 'message' => 'From Account and To Account must be different']);
            exit;
        }
        
        // Combine date and time
        if ($transactionDate && $transactionTime) {
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $transactionDate)) {
                echo json_encode(['success' => false, 'message' => 'Invalid date format. Use YYYY-MM-DD']);
                exit;
            }
            if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $transactionTime)) {
                echo json_encode(['success' => false, 'message' => 'Invalid time format. Use HH:MM or HH:MM:SS']);
                exit;
            }
            $fullDateTime = $transactionDate . ' ' . $transactionTime;
            $datetimeObj = DateTime::createFromFormat('Y-m-d H:i:s', $fullDateTime);
            if (!$datetimeObj || $datetimeObj->format('Y-m-d H:i:s') !== $fullDateTime) {
                $datetimeObj = DateTime::createFromFormat('Y-m-d H:i', $fullDateTime);
                if (!$datetimeObj) {
                    echo json_encode(['success' => false, 'message' => 'Invalid date/time combination']);
                    exit;
                }
                $fullDateTime = $datetimeObj->format('Y-m-d H:i:s');
            }
        } else {
            $fullDateTime = date('Y-m-d H:i:s');
        }
        
        // Verify user exists
        $sql = "SELECT id, email, full_name, currency, currency_selection_shown FROM users WHERE id = ? AND role != 'admin'";
        $stmt = $db->query($sql, [$userId]);
        $user = $stmt->fetch();
        
        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'User not found']);
            exit;
        }
        
        // Verify both accounts belong to the user and are active
        $sql = "SELECT id, user_id, account_number, account_type, balance, currency FROM accounts WHERE id IN (?, ?) AND user_id = ? AND status = 'active'";
        $stmt = $db->query($sql, [$fromAccountId, $toAccountId, $userId]);
        $accounts = [];
        while ($row = $stmt->fetch()) {
            $accounts[$row['id']] = $row;
        }
        
        if (!isset($accounts[$fromAccountId])) {
            echo json_encode(['success' => false, 'message' => 'From Account not found or does not belong to user']);
            exit;
        }
        
        if (!isset($accounts[$toAccountId])) {
            echo json_encode(['success' => false, 'message' => 'To Account not found or does not belong to user']);
            exit;
        }
        
        $fromAccount = $accounts[$fromAccountId];
        $toAccount = $accounts[$toAccountId];

        $amountCurrency = Security::sanitize($input['amount_currency'] ?? 'display');
        $ledgerAmount = adminResolveLedgerAdjustmentAmount($amount, $user, $fromAccount, $amountCurrency);
        if ($ledgerAmount <= 0) {
            echo json_encode(['success' => false, 'message' => 'Amount must be greater than 0']);
            exit;
        }

        // Check if from account has sufficient balance
        if (floatval($fromAccount['balance']) < $ledgerAmount) {
            echo json_encode(['success' => false, 'message' => 'Insufficient balance in source account']);
            exit;
        }
        
        // Start transaction
        $db->beginTransaction();
        
        try {
            // Calculate new balances
            $fromBalanceBefore = floatval($fromAccount['balance']);
            $toBalanceBefore = floatval($toAccount['balance']);
            $fromBalanceAfter = $fromBalanceBefore - $ledgerAmount;
            $toBalanceAfter = $toBalanceBefore + $ledgerAmount;
            
            // Update account balances
            $sql = "UPDATE accounts SET balance = ?, available_balance = ?, updated_at = NOW() WHERE id = ?";
            $db->query($sql, [$fromBalanceAfter, $fromBalanceAfter, $fromAccountId]);
            $db->query($sql, [$toBalanceAfter, $toBalanceAfter, $toAccountId]);
            
            // Create transaction references
            $transactionRef = 'ADM' . date('YmdHis') . rand(100, 999);
            $fromTransactionRef = $transactionRef . '-D';
            $toTransactionRef = $transactionRef . '-C';
            
            // Create debit transaction (from source account)
            $sql = "INSERT INTO transactions (
                        transaction_ref, user_id, account_id, transaction_type, category, expense_category,
                        amount, currency, balance_before, balance_after, description, 
                        recipient_account, recipient_name, recipient_bank,
                        status, fee, metadata, ip_address, created_at, completed_at
                    ) VALUES (?, ?, ?, 'debit', 'transfer', ?, ?, ?, ?, ?, ?, ?, ?, ?, 'completed', 0, ?, ?, ?, ?)";
            
            $metadata = json_encode([
                'admin_id' => $_SESSION['user_id'],
                'internal_transfer' => true,
                'to_account_id' => $toAccountId,
                'to_account_number' => $toAccount['account_number'],
                'admin_action' => true
            ]);
            
            $db->query($sql, [
                $fromTransactionRef,   // 1. transaction_ref
                $userId,               // 2. user_id
                $fromAccountId,        // 3. account_id
                // 4. transaction_type = 'debit' (hardcoded)
                // 5. category = 'transfer' (hardcoded)
                $expenseCategory,      // 6. expense_category
                $ledgerAmount,               // 7. amount
                $fromAccount['currency'], // 8. currency
                $fromBalanceBefore,    // 9. balance_before
                $fromBalanceAfter,     // 10. balance_after
                $description . ' (to ' . $toAccount['account_type'] . ' ' . $toAccount['account_number'] . ')', // 11. description
                $toAccount['account_number'], // 12. recipient_account
                $toAccount['account_type'],   // 13. recipient_name
                'Internal Transfer',   // 14. recipient_bank
                // 15. status = 'completed' (hardcoded)
                // 16. fee = 0 (hardcoded)
                $metadata,             // 17. metadata
                $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0', // 18. ip_address
                $fullDateTime,         // 19. created_at
                $fullDateTime          // 20. completed_at
            ]);
            
            // Create credit transaction (to destination account)
            $sql = "INSERT INTO transactions (
                        transaction_ref, user_id, account_id, transaction_type, category, expense_category,
                        amount, currency, balance_before, balance_after, description, 
                        recipient_account, recipient_name, recipient_bank,
                        status, fee, metadata, ip_address, created_at, completed_at
                    ) VALUES (?, ?, ?, 'credit', 'transfer', ?, ?, ?, ?, ?, ?, ?, ?, ?, 'completed', 0, ?, ?, ?, ?)";
            
            $metadata = json_encode([
                'admin_id' => $_SESSION['user_id'],
                'internal_transfer' => true,
                'from_account_id' => $fromAccountId,
                'from_account_number' => $fromAccount['account_number'],
                'admin_action' => true
            ]);
            
            $db->query($sql, [
                $toTransactionRef,     // 1. transaction_ref
                $userId,               // 2. user_id
                $toAccountId,          // 3. account_id
                // 4. transaction_type = 'credit' (hardcoded)
                // 5. category = 'transfer' (hardcoded)
                $expenseCategory,      // 6. expense_category
                $ledgerAmount,               // 7. amount
                $toAccount['currency'], // 8. currency
                $toBalanceBefore,      // 9. balance_before
                $toBalanceAfter,       // 10. balance_after
                $description . ' (from ' . $fromAccount['account_type'] . ' ' . $fromAccount['account_number'] . ')', // 11. description
                $fromAccount['account_number'], // 12. recipient_account
                $fromAccount['account_type'],   // 13. recipient_name
                'Internal Transfer',   // 14. recipient_bank
                // 15. status = 'completed' (hardcoded)
                // 16. fee = 0 (hardcoded)
                $metadata,             // 17. metadata
                $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0', // 18. ip_address
                $fullDateTime,         // 19. created_at
                $fullDateTime          // 20. completed_at
            ]);
            
            // Log admin action
            $logDescription = "Internal transfer of {$amount} from account {$fromAccount['account_number']} to {$toAccount['account_number']} for user {$user['email']} (ID: {$userId})";
            $sql = "INSERT INTO admin_logs (admin_id, user_id, action, description, created_at) VALUES (?, ?, 'internal_transfer', ?, NOW())";
            $db->query($sql, [$_SESSION['user_id'], $userId, $logDescription]);
            
            // Commit transaction
            if (!$db->commit()) {
                throw new Exception('Failed to commit transaction');
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Internal transfer completed successfully',
                'transaction_ref' => $transactionRef,
                'from_balance' => $fromBalanceAfter,
                'to_balance' => $toBalanceAfter
            ]);
            
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
        
    } catch (Exception $e) {
        error_log('Internal Adjustment Error: ' . $e->getMessage());
        error_log('Stack trace: ' . $e->getTraceAsString());
        
        if (ob_get_level() > 0) {
            ob_clean();
        }
        
        echo json_encode([
            'success' => false,
            'message' => 'An error occurred while processing internal transfer: ' . $e->getMessage()
        ]);
    }
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit;
}

// Check if this is an internal adjustment (between user's own accounts)
$adjustmentType = Security::sanitize($input['adjustment_type'] ?? 'external');
if ($adjustmentType === 'internal') {
    // Handle internal account-to-account transfer
    handleInternalAdjustment($input);
    exit;
}

// Continue with external adjustment (existing logic)
$userId = intval($input['user_id'] ?? 0);
$accountId = intval($input['account_id'] ?? 0);
$amount = floatval($input['amount'] ?? 0);

// Additional validation for edge cases
if ($accountId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid account ID']);
    exit;
}
$direction = Security::sanitize($input['transaction_type'] ?? 'credit'); // This is the adjust type (credit/debit)
$method = Security::sanitize($input['transaction_method'] ?? 'internal'); // This is the transaction method
// Set category based on adjustment type for proper analytics
// For internal transfers, use 'transfer' category; otherwise use deposit/withdrawal
$category = ($method === 'internal') ? 'transfer' : (($direction === 'credit') ? 'deposit' : 'withdrawal');
$expenseCategory = Security::sanitize($input['expense_category'] ?? null);
$status = Security::sanitize($input['status'] ?? 'completed');
// Description will be generated later based on method and direction
$description = '';
$transactionDate = Security::sanitize($input['transaction_date'] ?? date('Y-m-d H:i:s'));
$transactionTime = Security::sanitize($input['transaction_time'] ?? date('H:i:s'));
$reason = Security::sanitize($input['reason'] ?? 'Administrative adjustment');

// Combine date and time if both are provided
if ($transactionDate && $transactionTime) {
    // Validate date format (YYYY-MM-DD)
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $transactionDate)) {
        echo json_encode(['success' => false, 'message' => 'Invalid date format. Use YYYY-MM-DD']);
        exit;
    }
    // Validate time format (HH:MM or HH:MM:SS)
    if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $transactionTime)) {
        echo json_encode(['success' => false, 'message' => 'Invalid time format. Use HH:MM or HH:MM:SS']);
        exit;
    }
    $fullDateTime = $transactionDate . ' ' . $transactionTime;
    // Validate the combined datetime
    $datetimeObj = DateTime::createFromFormat('Y-m-d H:i:s', $fullDateTime);
    if (!$datetimeObj || $datetimeObj->format('Y-m-d H:i:s') !== $fullDateTime) {
        $datetimeObj = DateTime::createFromFormat('Y-m-d H:i', $fullDateTime);
        if (!$datetimeObj) {
            echo json_encode(['success' => false, 'message' => 'Invalid date/time combination']);
            exit;
        }
        $fullDateTime = $datetimeObj->format('Y-m-d H:i:s');
    }
} else {
    $fullDateTime = date('Y-m-d H:i:s');
}

// Validate required fields
if (!$userId || !$accountId || !$amount) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields: user_id, account_id, amount']);
    exit;
}

$demoAccessDenied = denyDemoUserAdminAccessJson($userId);
if ($demoAccessDenied) {
    echo json_encode($demoAccessDenied);
    exit;
}

// Method-specific fields
$methodFields = [];
if ($method === 'card') {
    $methodFields['card_number'] = Security::sanitize($input['card_number'] ?? '');
    $methodFields['merchant'] = Security::sanitize($input['merchant'] ?? '');
} elseif ($method === 'domestic') {
    $methodFields['recipient_bank'] = Security::sanitize($input['recipient_bank'] ?? '');
    $methodFields['recipient_account'] = Security::sanitize($input['recipient_account'] ?? '');
    $methodFields['recipient_name'] = Security::sanitize($input['recipient_name'] ?? '');
} elseif ($method === 'international') {
    $methodFields['recipient_bank'] = Security::sanitize($input['recipient_bank'] ?? '');
    $methodFields['recipient_account'] = Security::sanitize($input['recipient_account'] ?? '');
    $methodFields['recipient_name'] = Security::sanitize($input['recipient_name'] ?? '');
} elseif ($method === 'internal') {
    $methodFields['recipient_account'] = Security::sanitize($input['recipient_account'] ?? '');
    $methodFields['recipient_name'] = Security::sanitize($input['recipient_name'] ?? '');
    // For internal transfers, set bank name to site name
    try {
        require_once __DIR__ . '/../includes/system-settings.php';
        $systemSettings = SystemSettings::getInstance();
        $methodFields['recipient_bank'] = $systemSettings->get('site_name', 'SecureBank Online');
    } catch (Exception $e) {
        error_log("Error loading system settings for bank name: " . $e->getMessage());
        // Fallback to default bank name
        $methodFields['recipient_bank'] = 'SecureBank Online';
    } catch (Error $e) {
        error_log("Fatal error loading system settings: " . $e->getMessage());
        $methodFields['recipient_bank'] = 'SecureBank Online';
    }
}

// Validate
if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'User ID required']);
    exit;
}

if (!in_array($direction, ['credit', 'debit'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid direction. Use "credit" or "debit"']);
    exit;
}

if ($amount <= 0) {
    echo json_encode(['success' => false, 'message' => 'Amount must be greater than 0']);
    exit;
}

// Database enum: 'pending','processing','completed','failed','reversed'
// Note: 'on_hold' is not in database enum, so we'll map it to 'pending'
if (!in_array($status, ['completed', 'pending', 'failed', 'on_hold', 'processing'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid status. Use "completed", "pending", "failed", "processing", or "on_hold"']);
    exit;
}

// Map 'on_hold' to 'pending' since database doesn't support 'on_hold'
if ($status === 'on_hold') {
    $status = 'pending';
}

// Validate category
$validCategories = ['transfer', 'payment', 'deposit', 'withdrawal', 'fee', 'interest', 'loan', 'card', 'other'];
if (!in_array($category, $validCategories)) {
    echo json_encode(['success' => false, 'message' => 'Invalid category. Use: ' . implode(', ', $validCategories)]);
    exit;
}

try {
    $db = Database::getInstance();
    if (!$db) {
        throw new Exception('Database connection failed');
    }
    $db->beginTransaction();
    
    // Check if user exists and is not an admin
    $sql = "SELECT id, email, full_name, currency, currency_selection_shown FROM users WHERE id = ? AND role != 'admin'";
    $stmt = $db->query($sql, [$userId]);
    $user = $stmt->fetch();
    
    if (!$user) {
        $db->rollback();
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }
    
    // Verify the specified account belongs to the user
    $sql = "SELECT id, user_id, account_number, balance, currency FROM accounts WHERE id = ? AND user_id = ? AND status = 'active'";
    $stmt = $db->query($sql, [$accountId, $userId]);
    $account = $stmt->fetch();
    
    if (!$account) {
        $db->rollback();
        echo json_encode(['success' => false, 'message' => 'Account not found or does not belong to user']);
        exit;
    }
    $balanceBefore = floatval($account['balance']);

    $amountCurrency = Security::sanitize($input['amount_currency'] ?? 'display');
    $ledgerAmount = adminResolveLedgerAdjustmentAmount($amount, $user, $account, $amountCurrency);
    if ($ledgerAmount <= 0) {
        $db->rollback();
        echo json_encode(['success' => false, 'message' => 'Amount must be greater than 0']);
        exit;
    }
    
    // Calculate new balance
    if ($direction === 'credit') {
        $newBalance = $balanceBefore + $ledgerAmount;
        $balanceChange = $ledgerAmount;
    } else {
        $newBalance = $balanceBefore - $ledgerAmount;
        $balanceChange = -$ledgerAmount;
    }
    
    // Check if debit would result in negative balance
    if ($newBalance < 0) {
        $db->rollback();
        echo json_encode(['success' => false, 'message' => 'Insufficient balance. Cannot debit more than available balance.']);
        exit;
    }
    
    // Update account balance based on status
    // For pending/on_hold: don't update balance yet
    // For completed: update balance immediately
    // For failed: don't update balance
    if ($status === 'completed') {
        $sql = "UPDATE accounts SET balance = ?, available_balance = ?, updated_at = NOW() WHERE id = ?";
        $db->query($sql, [$newBalance, $newBalance, $account['id']]);
    } elseif ($status === 'pending' || $status === 'on_hold') {
        // For pending/on_hold, we could optionally reserve the balance
        // For now, we'll just record the transaction without updating balance
        // Admin can manually complete it later through Transaction Processing Override
    }
    // For failed status, no balance update
    
    // Create transaction record
    $transactionRef = 'ADM' . date('YmdHis') . rand(100, 999);
    
    // Use the description from input if provided (frontend generates it properly)
    // Only generate description if not provided
    $description = Security::sanitize($input['description'] ?? '');
    
    if (empty($description)) {
        // Generate description only if not provided by frontend
        if ($method === 'internal' && $direction === 'credit') {
            // For credit (income) from internal transfer, format: "Internal Transfer from [Sender Name]"
            $senderName = $methodFields['recipient_name'] ?? '';
            // Remove placeholder text if present
            if (empty($senderName) || strtolower(trim($senderName)) === 'internal transfer') {
                $senderName = 'Unknown Sender';
            }
            $description = "Internal Transfer from " . $senderName;
        } elseif ($method === 'internal' && $direction === 'debit') {
            // For debit (outgoing) internal transfer, format: "Internal Transfer to [Recipient Name]"
            // Use recipient_name instead of account number for better clarity
            $recipientName = $methodFields['recipient_name'] ?? '';
            if (empty($recipientName)) {
                // Fallback to account number if name not provided
                $recipientAccount = $methodFields['recipient_account'] ?? 'Unknown Account';
                $description = "Internal Transfer to " . $recipientAccount;
            } else {
                $description = "Internal Transfer to " . $recipientName;
            }
        } elseif ($method === 'domestic' && $direction === 'credit') {
            // For credit domestic transfers, use "Transfer from" instead of "Income from"
            $senderName = $methodFields['recipient_name'] ?? 'Unknown Sender';
            $senderBank = $methodFields['recipient_bank'] ?? 'Unknown Bank';
            $description = "Transfer from " . $senderName . " at " . $senderBank;
        } elseif ($method === 'international' && $direction === 'credit') {
            // For credit international transfers, use "Transfer from" instead of "Income from"
            $senderName = $methodFields['recipient_name'] ?? 'Unknown Sender';
            $senderBank = $methodFields['recipient_bank'] ?? 'Unknown Bank';
            $description = "Transfer from " . $senderName . " at " . $senderBank;
        } else {
            // Default fallback
            $description = 'Admin balance adjustment';
        }
    }
    
    $metadata = json_encode([
        'admin_id' => $_SESSION['user_id'],
        'reason' => $reason,
        'method' => $method,
        'method_fields' => $methodFields,
        'admin_action' => true,
        'display_amount' => $amount,
        'display_currency' => getUserDisplayCurrency($user),
        'ledger_amount' => $ledgerAmount,
        'ledger_currency' => getAccountStoredCurrency($account),
    ]);
    
    // Debug: Log the data being inserted
    error_log("Creating transaction with data: " . json_encode([
        'transaction_ref' => $transactionRef,
        'user_id' => $userId,
        'account_id' => $account['id'],
        'account_user_id' => $account['user_id'] ?? 'unknown', // Check if account belongs to correct user
        'transaction_type' => $direction,
        'category' => $category,
        'expense_category' => $expenseCategory,
        'amount' => $amount,
        'description' => $description,
        'recipient_name' => $methodFields['recipient_name'] ?? null,
        'recipient_account' => $methodFields['recipient_account'] ?? null,
        'recipient_bank' => $methodFields['recipient_bank'] ?? null
    ]));
    
    $sql = "INSERT INTO transactions (
                transaction_ref, user_id, account_id, transaction_type, category, expense_category,
                amount, currency, balance_before, balance_after, description, 
                recipient_account, recipient_name, recipient_bank,
                status, fee, metadata, ip_address, created_at, completed_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $completedAt = ($status === 'completed') ? $fullDateTime : null;
    
    // Debug: Log the SQL query and parameters
    error_log("SQL Query: " . $sql);
    error_log("Parameters: " . json_encode([
        $transactionRef, $userId, $account['id'], $direction, $category, $expenseCategory,
        $amount, $account['currency'], $balanceBefore, ($status === 'completed' ? $newBalance : $balanceBefore), $description,
        $methodFields['recipient_account'] ?? null, $methodFields['recipient_name'] ?? null, $methodFields['recipient_bank'] ?? null,
        $status, 0, $metadata, $_SERVER['REMOTE_ADDR'], $fullDateTime, $completedAt
    ]));
    
    // Ensure account_id is an integer (critical for JOIN to work)
    $accountIdForInsert = intval($account['id']);
    error_log("Inserting transaction with account_id: {$accountIdForInsert} (type: " . gettype($accountIdForInsert) . ")");
    
    $result = $db->query($sql, [
        $transactionRef, $userId, $accountIdForInsert, $direction, $category, $expenseCategory, // expense_category from form
        $ledgerAmount, $account['currency'], $balanceBefore, ($status === 'completed' ? $newBalance : $balanceBefore), $description,
        $methodFields['recipient_account'] ?? null, $methodFields['recipient_name'] ?? null, $methodFields['recipient_bank'] ?? null,
        $status, 0, $metadata, $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0', $fullDateTime, $completedAt
    ]);
    
    if ($result === false) {
        $db->rollback();
        $errorInfo = $db->errorInfo();
        error_log("Failed to create transaction record - Query failed. Error: " . json_encode($errorInfo));
        echo json_encode(['success' => false, 'message' => 'Failed to create transaction record - Database error: ' . ($errorInfo[2] ?? 'Unknown error')]);
        exit;
    }
    
    // Get the inserted transaction ID - CRITICAL: Verify the INSERT actually created a row
    $insertedTransactionId = $db->lastInsertId();
    if (!$insertedTransactionId || $insertedTransactionId == 0) {
        $db->rollback();
        error_log("CRITICAL ERROR: Transaction INSERT returned success but lastInsertId() is empty/zero!");
        error_log("  This indicates the INSERT didn't actually insert a row.");
        error_log("  SQL: " . $sql);
        error_log("  Parameters: " . json_encode([
            $transactionRef, $userId, $accountIdForInsert, $direction, $category, $expenseCategory,
            $amount, $account['currency'], $balanceBefore, ($status === 'completed' ? $newBalance : $balanceBefore), $description,
            $methodFields['recipient_account'] ?? null, $methodFields['recipient_name'] ?? null, $methodFields['recipient_bank'] ?? null,
            $status, 0, $metadata, $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0', $fullDateTime, $completedAt
        ]));
        echo json_encode(['success' => false, 'message' => 'Failed to create transaction record - No transaction ID returned. Please check server logs.']);
        exit;
    }
    
    error_log("Transaction inserted successfully with ID: {$insertedTransactionId}");
    
    // Log admin action
    $logDescription = "Created {$direction} transaction of {$account['currency']} {$ledgerAmount} (display {$amount} " . getUserDisplayCurrency($user) . ") for user {$user['email']} (ID: {$userId}) - Status: {$status}";
    $sql = "INSERT INTO admin_logs (admin_id, user_id, action, description, created_at) VALUES (?, ?, 'balance_adjustment', ?, NOW())";
    $logResult = $db->query($sql, [$_SESSION['user_id'], $userId, $logDescription]);
    
    if (!$logResult) {
        error_log("Failed to create admin log");
        // Don't exit here, just log the error - admin log is not critical
    }
    
    // Commit the transaction
    if (!$db->commit()) {
        error_log("CRITICAL ERROR: Failed to commit transaction! Transaction ID: {$insertedTransactionId}");
        echo json_encode(['success' => false, 'message' => 'Failed to commit transaction. Please check server logs.']);
        exit;
    }
    
    error_log("Transaction committed successfully. Transaction ID: {$insertedTransactionId}, Reference: {$transactionRef}");
    
    // Verify transaction exists using the EXACT same queries used by the views
    // This ensures transactions will be visible in all transaction pages
    
    // 1. Verify transaction exists directly (admin transactions page uses similar query)
    $directCheckSql = "SELECT t.*, u.full_name, u.email, a.account_number 
                       FROM transactions t 
                       JOIN users u ON t.user_id = u.id 
                       LEFT JOIN accounts a ON t.account_id = a.id 
                       WHERE t.transaction_ref = ? AND u.role = 'user'";
    $directCheckStmt = $db->query($directCheckSql, [$transactionRef]);
    $directCheck = $directCheckStmt->fetch();
    
    if (!$directCheck) {
        error_log("CRITICAL: Transaction was NOT found using admin transactions query! Reference: {$transactionRef}");
        error_log("  This means the transaction will NOT appear on the admin transactions page!");
    } else {
        error_log("✓ Transaction EXISTS and will appear in admin transactions page - ID: {$directCheck['id']}");
    }
    
    // 2. Verify transaction will appear in getUserTransactions (user transaction table and admin user management)
    // This uses the EXACT same query as Transaction::getUserTransactions()
    $getUserTransactionsSql = "SELECT t.*, a.account_number, a.account_type 
                               FROM transactions t 
                               LEFT JOIN accounts a ON t.account_id = a.id AND a.user_id = t.user_id
                               WHERE t.user_id = ? AND t.id = ?";
    $getUserTransactionsStmt = $db->query($getUserTransactionsSql, [$userId, $insertedTransactionId]);
    $getUserTransactionsResult = $getUserTransactionsStmt->fetch();
    
    if (!$getUserTransactionsResult) {
        error_log("CRITICAL ERROR: Transaction will NOT appear in getUserTransactions query!");
        error_log("  Transaction ID: {$insertedTransactionId}, User ID: {$userId}, Account ID: {$accountIdForInsert}");
        
        // Check if account exists and matches user
        $accountCheckSql = "SELECT id, user_id, account_number, status FROM accounts WHERE id = ?";
        $accountCheckStmt = $db->query($accountCheckSql, [$accountIdForInsert]);
        $accountCheck = $accountCheckStmt->fetch();
        
        if ($accountCheck) {
            error_log("  Account EXISTS - id: {$accountCheck['id']}, user_id: {$accountCheck['user_id']}, number: {$accountCheck['account_number']}, status: {$accountCheck['status']}");
            if ($accountCheck['user_id'] != $userId) {
                error_log("  ERROR: Account belongs to user {$accountCheck['user_id']} but transaction is for user {$userId}!");
            }
        } else {
            error_log("  Account DOES NOT EXIST with id: {$accountIdForInsert}");
        }
        
        // Verify transaction exists at all
        $directTxnSql = "SELECT * FROM transactions WHERE id = ? AND user_id = ?";
        $directTxnStmt = $db->query($directTxnSql, [$insertedTransactionId, $userId]);
        $directTxn = $directTxnStmt->fetch();
        
        if ($directTxn) {
            error_log("  Transaction EXISTS in database (direct query works)");
            error_log("  Transaction account_id in DB: {$directTxn['account_id']} (type: " . gettype($directTxn['account_id']) . ")");
        } else {
            error_log("  Transaction NOT FOUND even with direct query!");
        }
    } else {
        error_log("✓ Transaction WILL appear in getUserTransactions query - verified successfully");
        error_log("  Transaction ID: {$insertedTransactionId}, Account Number: " . ($getUserTransactionsResult['account_number'] ?? 'NULL'));
    }
    
    // 3. Get the created transaction for response
    $createdTransaction = $directCheck ?: null;
    if (!$createdTransaction) {
        // Fallback: try to get transaction directly
        $fallbackSql = "SELECT * FROM transactions WHERE id = ?";
        $fallbackStmt = $db->query($fallbackSql, [$insertedTransactionId]);
        $createdTransaction = $fallbackStmt->fetch();
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Transaction created successfully',
        'transaction_ref' => $transactionRef,
        'transaction_id' => $createdTransaction['id'] ?? null,
        'new_balance' => $newBalance,
        'balance_change' => $balanceChange
    ]);
    
} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollback();
    }
    error_log('Admin Adjust Balance Error: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
    
    // Make sure no output before JSON
    if (ob_get_level() > 0) {
        ob_clean();
    }
    
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while creating transaction. Please check the logs for details.'
    ]);
}