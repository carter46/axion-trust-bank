<?php
// Prevent any output before JSON - Critical: Must be first!
@ini_set('display_errors', 0);
@error_reporting(0);
ob_start();

try {
    require_once __DIR__ . '/../config/config.php';
    require_once __DIR__ . '/../includes/functions.php';
    
    // Set up autoloader for models (API is called directly, not through router)
    if (!class_exists('Account')) {
        spl_autoload_register(function ($class_name) {
            $paths = [
                'models/',
                'controllers/',
                'classes/'
            ];
            
            foreach ($paths as $path) {
                $file = BASE_PATH . '/' . $path . $class_name . '.php';
                if (file_exists($file)) {
                    require_once $file;
                    return;
                }
            }
        });
    }
    
    // Clear any accidental output before headers
    $output = ob_get_clean();
    if (!empty($output)) {
        error_log("get-account-data.php: Unexpected output before headers: " . substr($output, 0, 200));
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

requireLogin();
$userId = $_SESSION['user_id'];
$accountId = isset($_GET['account_id']) ? (int)$_GET['account_id'] : null;

if (!$accountId) {
    echo json_encode(['success' => false, 'message' => 'Account ID required']);
    exit;
}

try {
    $db = Database::getInstance();
    
    if (!$db) {
        throw new Exception('Database connection failed');
    }

    // Get account details
    $accountModel = new Account();
    $account = $accountModel->findById($accountId);

    if (!$account || !is_array($account)) {
        echo json_encode(['success' => false, 'message' => 'Account not found']);
        exit;
    }
    
    // Check if user has access to this account (own account OR joint access)
    $hasAccess = false;
    if (isset($account['user_id']) && $account['user_id'] == $userId) {
        // User owns the account
        $hasAccess = true;
    } else {
        // Check joint account access
        if (class_exists('JointAccount')) {
            require_once __DIR__ . '/../models/JointAccount.php';
            $jointAccount = new JointAccount();
            $hasAccess = $jointAccount->userHasAccess($userId, $accountId);
        }
    }
    
    if (!$hasAccess) {
        echo json_encode(['success' => false, 'message' => 'Account not found or access denied']);
        exit;
    }

    // Get user currency
    $userModel = new User();
    $user = $userModel->findById($userId);
    if (!$user || !is_array($user)) {
        throw new Exception('User not found');
    }
    $userCurrency = getUserDisplayCurrency($user);
    $accountCurrency = getAccountStoredCurrency($account);
    $userStoredCurrency = getUserStoredCurrency($user);

    // Get current month income for this account
    // For joint accounts, show transactions for the account (not filtered by user_id)
    $currentMonth = date('Y-m');
    $incomeSql = "SELECT COALESCE(SUM(amount), 0) as total
                  FROM transactions 
                  WHERE account_id = ?
                  AND transaction_type = 'credit'
                  AND status = 'completed'
                  AND DATE_FORMAT(created_at, '%Y-%m') = ?";
    $stmt = $db->query($incomeSql, [$accountId, $currentMonth]);
    if ($stmt === false) {
        $monthlyIncome = 0;
    } else {
        $incomeResult = $stmt->fetch();
        $monthlyIncome = (float)($incomeResult['total'] ?? 0);
    }

    // Get current month outgoing for this account
    // For joint accounts, show transactions for the account (not filtered by user_id)
    $outgoingSql = "SELECT COALESCE(SUM(amount), 0) as total
                      FROM transactions 
                      WHERE account_id = ?
                      AND transaction_type = 'debit'
                      AND status = 'completed'
                      AND DATE_FORMAT(created_at, '%Y-%m') = ?";
    $stmt = $db->query($outgoingSql, [$accountId, $currentMonth]);
    if ($stmt === false) {
        $monthlyOutgoing = 0;
    } else {
        $outgoingResult = $stmt->fetch();
        $monthlyOutgoing = (float)($outgoingResult['total'] ?? 0);
    }

    // Get investment balance (user level, not account level)
    $investmentBalance = (float)($user['investment_balance'] ?? 0);

    // Get total balance across all accounts (converted to display currency)
    $allAccounts = $accountModel->getUserAccounts($userId);
    $totalBalance = sumAccountBalancesForDisplay(is_array($allAccounts) ? $allAccounts : [], $userCurrency);

    // Get transaction limit from system settings based on account type
    require_once __DIR__ . '/../includes/functions.php';
    $transactionLimit = getDailyLimitForAccountType($account['account_type'] ?? 'checking');

    // Get pending transactions total amount for this account
    // For joint accounts, show transactions for the account (not filtered by user_id)
    $pendingSql = "SELECT COALESCE(SUM(amount), 0) as total
                   FROM transactions 
                   WHERE account_id = ?
                   AND status = 'pending'";
    $stmt = $db->query($pendingSql, [$accountId]);
    if ($stmt === false) {
        $pendingTransactions = 0;
    } else {
        $pendingResult = $stmt->fetch();
        $pendingTransactions = (float)($pendingResult['total'] ?? 0);
    }

    // Get transaction volume (total amount of all completed transactions for this account)
    // For joint accounts, show transactions for the account (not filtered by user_id)
    $volumeSql = "SELECT COALESCE(SUM(amount), 0) as total
                  FROM transactions 
                  WHERE account_id = ?
                  AND status = 'completed'";
    $stmt = $db->query($volumeSql, [$accountId]);
    if ($stmt === false) {
        $transactionVolume = 0;
    } else {
        $volumeResult = $stmt->fetch();
        $transactionVolume = (float)($volumeResult['total'] ?? 0);
    }

    // formatCurrency is already available from functions.php

    // Ensure clean JSON output
    if (ob_get_level() > 0) {
        ob_clean();
    }

    // Ensure all values are valid before formatting
    $accountBalance = (float)($account['balance'] ?? 0);
    $accountNumber = $account['account_number'] ?? 'N/A';
    $accountType = $account['account_type'] ?? 'checking';
    
    $result = [
        'success' => true,
        'data' => [
            'account_number' => $accountNumber,
            'balance' => formatAccountBalance($accountBalance, $account, $userCurrency),
            'balance_raw' => $accountBalance,
            'account_type' => ucfirst($accountType),
            'current_balance' => formatAccountBalance($accountBalance, $account, $userCurrency),
            'total_balance' => formatDisplayCurrencyAmount($totalBalance, $userCurrency),
            'monthly_income' => formatCurrency($monthlyIncome, $userCurrency, $accountCurrency),
            'monthly_income_raw' => $monthlyIncome,
            'monthly_outgoing' => formatCurrency($monthlyOutgoing, $userCurrency, $accountCurrency),
            'monthly_outgoing_raw' => $monthlyOutgoing,
            'investment_balance' => formatCurrency($investmentBalance, $userCurrency, $userStoredCurrency),
            'transaction_limit' => formatCurrency($transactionLimit, $userCurrency, DEFAULT_CURRENCY),
            'pending_transactions' => formatCurrency($pendingTransactions, $userCurrency, $accountCurrency),
            'transaction_volume' => formatCurrency($transactionVolume, $userCurrency, $accountCurrency),
            'currency' => $userCurrency
        ]
    ];

    echo json_encode($result);
    
} catch (Exception $e) {
    // Ensure clean JSON output on error
    if (ob_get_level() > 0) {
        ob_clean();
    }
    
    error_log('get-account-data.php error: ' . $e->getMessage());
    error_log('Error file: ' . $e->getFile());
    error_log('Error line: ' . $e->getLine());
    error_log('Stack trace: ' . $e->getTraceAsString());
    
    echo json_encode([
        'success' => false,
        'message' => 'Error loading account data: ' . $e->getMessage()
    ]);
} catch (Throwable $e) {
    // Handle any other throwable
    if (ob_get_level() > 0) {
        ob_clean();
    }
    
    error_log('get-account-data.php fatal error: ' . $e->getMessage());
    error_log('Error file: ' . $e->getFile());
    error_log('Error line: ' . $e->getLine());
    error_log('Stack trace: ' . $e->getTraceAsString());
    
    // Return the actual error message for debugging (remove in production if needed)
    echo json_encode([
        'success' => false,
        'message' => 'Unexpected error: ' . $e->getMessage() . ' (Check server logs for details)'
    ]);
}

