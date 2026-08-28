<?php
class DashboardController {
    
    public function index() {
        requireLogin();
        
        $userId = $_SESSION['user_id'];
        $db = Database::getInstance();
        
        // Get user info
        $userModel = new User();
        $user = $userModel->findById($userId);
        
        // Check if 2FA is required system-wide but user hasn't enabled it
        // Skip this check for admin users (they can access dashboard via "view as user" feature)
        // Skip for restricted users (they can log in for support, but actions are blocked anyway)
        $isAdmin = isset($user['role']) && $user['role'] === 'admin';
        $isRestricted = function_exists('isRestrictedStatus') ? isRestrictedStatus($user['status'] ?? '') : false;
        
        if (!$isAdmin && !$isRestricted) {
            // 2FA is optional — do not force-redirect when disabled.
            // Check if security setup is incomplete (Transfer PIN only)
            if (isSecuritySetupIncomplete($userId)) {
                $_SESSION['security_setup_required'] = true;
                $_SESSION['security_onboarding'] = true;
                redirect('/profile/security');
            }
        }
        
        // Currency is admin-owned — never show the user currency selection popup
        $showCurrencyPopup = false;
        $detectedCurrency = null;
        
        // Get account summary
        $accountModel = new Account();
        $summary = $accountModel->getAccountSummary($userId);
        $userAccounts = $accountModel->getUserAccounts($userId);
        
        // Get primary/default account
        $primaryAccount = !empty($userAccounts) ? $userAccounts[0] : null;
        $primaryAccountId = $primaryAccount ? $primaryAccount['id'] : null;
        
        // Get current month income (credit transactions - for primary account)
        // For joint accounts, show all transactions, not just user's own
        $currentMonth = date('Y-m');
        $incomeSql = "SELECT COALESCE(SUM(amount), 0) as total
                      FROM transactions 
                      WHERE transaction_type = 'credit'
                      AND status IN ('successful', 'completed')
                      AND DATE_FORMAT(created_at, '%Y-%m') = ?";
        $incomeParams = [$currentMonth];
        
        // If primary account exists, filter by account_id (includes joint account transactions)
        if ($primaryAccountId) {
            $incomeSql .= " AND account_id = ?";
            $incomeParams[] = $primaryAccountId;
        } else {
            // Fallback: filter by user_id if no primary account
            $incomeSql .= " AND user_id = ?";
            $incomeParams[] = $userId;
        }
        
        $stmt = $db->query($incomeSql, $incomeParams);
        $incomeResult = $stmt ? $stmt->fetch() : ['total' => 0];
        $monthlyIncome = (float)$incomeResult['total'];
        
        // Get current month outgoing (debit transactions - for primary account)
        // For joint accounts, show all transactions, not just user's own
        $outgoingSql = "SELECT COALESCE(SUM(amount), 0) as total
                        FROM transactions 
                        WHERE transaction_type = 'debit'
                        AND status IN ('successful', 'completed')
                        AND DATE_FORMAT(created_at, '%Y-%m') = ?";
        $outgoingParams = [$currentMonth];
        
        // If primary account exists, filter by account_id (includes joint account transactions)
        if ($primaryAccountId) {
            $outgoingSql .= " AND account_id = ?";
            $outgoingParams[] = $primaryAccountId;
        } else {
            // Fallback: filter by user_id if no primary account
            $outgoingSql .= " AND user_id = ?";
            $outgoingParams[] = $userId;
        }
        
        $stmt = $db->query($outgoingSql, $outgoingParams);
        $outgoingResult = $stmt ? $stmt->fetch() : ['total' => 0];
        $monthlyOutgoing = (float)$outgoingResult['total'];
        
        // Get investment balance (user level, not account level)
        $investmentBalance = (float)($user['investment_balance'] ?? 0);
        
        // Get transaction limit from system settings based on primary account type
        require_once __DIR__ . '/../includes/functions.php';
        if ($primaryAccountId) {
            $primaryAccount = $accountModel->findById($primaryAccountId);
            $transactionLimit = getDailyLimitForAccountType($primaryAccount['account_type'] ?? 'checking');
        } else {
            $transactionLimit = getDailyLimitForAccountType('checking');
        }
        
        // Get pending transactions total amount (for primary account)
        // For joint accounts, show all transactions, not just user's own
        $pendingSql = "SELECT COALESCE(SUM(amount), 0) as total
                       FROM transactions 
                       WHERE status = 'pending'";
        $pendingParams = [];
        
        // If primary account exists, filter by account_id (includes joint account transactions)
        if ($primaryAccountId) {
            $pendingSql .= " AND account_id = ?";
            $pendingParams[] = $primaryAccountId;
        } else {
            // Fallback: filter by user_id if no primary account
            $pendingSql .= " AND user_id = ?";
            $pendingParams[] = $userId;
        }
        
        $stmt = $db->query($pendingSql, $pendingParams);
        $pendingResult = $stmt ? $stmt->fetch() : ['total' => 0];
        $pendingTransactions = (float)$pendingResult['total'];
        
        // Get transaction volume (total amount of all completed transactions for primary account)
        // For joint accounts, show all transactions, not just user's own
        $volumeSql = "SELECT COALESCE(SUM(amount), 0) as total
                      FROM transactions 
                      WHERE status IN ('successful', 'completed')";
        $volumeParams = [];
        
        // If primary account exists, filter by account_id (includes joint account transactions)
        if ($primaryAccountId) {
            $volumeSql .= " AND account_id = ?";
            $volumeParams[] = $primaryAccountId;
        } else {
            // Fallback: filter by user_id if no primary account
            $volumeSql .= " AND user_id = ?";
            $volumeParams[] = $userId;
        }
        
        $stmt = $db->query($volumeSql, $volumeParams);
        $volumeResult = $stmt ? $stmt->fetch() : ['total' => 0];
        $transactionVolume = (float)$volumeResult['total'];
        
        // Get recent transactions (last 3)
        $transactionModel = new Transaction();
        $recentTransactions = $transactionModel->getUserTransactions($userId, ['limit' => 3]);
        
        // Get expense by category data for chart (include joint accounts)
        // For joint accounts, show all transactions, not just user's own
        $expenseSql = "SELECT 
                        COALESCE(expense_category, category, 'other') as expense_category,
                        SUM(amount) as total,
                        COUNT(*) as transaction_count
                      FROM transactions 
                      WHERE transaction_type = 'debit'
                      AND status IN ('successful', 'completed')
                      AND MONTH(created_at) = MONTH(CURRENT_DATE())
                      AND YEAR(created_at) = YEAR(CURRENT_DATE())";
        
        // If primary account exists, filter by account_id (includes joint account transactions)
        if ($primaryAccountId) {
            $expenseSql .= " AND account_id = ?";
            $expenseParams = [$primaryAccountId];
        } else {
            // Fallback: filter by user_id if no primary account
            $expenseSql .= " AND user_id = ?";
            $expenseParams = [$userId];
        }
        
        $expenseSql .= " GROUP BY expense_category, category
                      ORDER BY total DESC";
        $stmt = $db->query($expenseSql, $expenseParams);
        $expenseCategories = ($stmt && $stmt !== false) ? $stmt->fetchAll() : [];
        
        // Calculate total expenses for chart
        $totalExpenses = array_sum(array_column($expenseCategories, 'total'));
        if ($totalExpenses == 0) {
            $totalExpenses = 1; // Avoid division by zero
        }
        
        // Variables are available directly in view
        include __DIR__ . '/../views/dashboard/index.php';
    }
}
