<?php
class TransactionController {
    
    public function index() {
        requireLogin();
        
        // Check if this is a detail view (has id parameter)
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $this->viewReceipt();
            return;
        }
        
        $userId = $_SESSION['user_id'];
        $db = Database::getInstance();
        
        // Get filter parameters - Only apply date filters if explicitly provided
        // Default to showing ALL transactions to ensure nothing is missed
        $fromDate = $_GET['from_date'] ?? '';
        $toDate = $_GET['to_date'] ?? '';
        $status = $_GET['status'] ?? '';
        $type = $_GET['type'] ?? '';
        $search = $_GET['search'] ?? '';
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        
        // Ensure valid limit values
        if (!in_array($limit, [20, 50, 100])) {
            $limit = 20;
        }
        
        // Use Transaction model method like the dashboard does - this ensures consistency
        require_once __DIR__ . '/../models/Transaction.php';
        $transactionModel = new Transaction();
        
        // Build filters array for getUserTransactions
        // Only include date filters if they're explicitly provided in the URL
        // This ensures all transactions are shown by default, including the most recent ones
        $filters = [];
        if (!empty($fromDate)) {
            $filters['date_from'] = $fromDate;
        }
        if (!empty($toDate)) {
            $filters['date_to'] = $toDate;
        }
        // If neither date is provided, show ALL transactions (no date filter)
        
        if (!empty($status)) {
            $filters['status'] = $status;
        }
        
        if (!empty($type)) {
            $filters['category'] = $type;
        }
        
        // Get ALL transactions matching filters first (for counting and pagination)
        $allTransactions = $transactionModel->getUserTransactions($userId, $filters);
        
        // Apply search filter if provided (Transaction model doesn't handle search, so filter in PHP)
        if (!empty($search)) {
            $searchLower = strtolower($search);
            $allTransactions = array_filter($allTransactions, function($txn) use ($searchLower) {
                return (
                    stripos($txn['description'] ?? '', $searchLower) !== false ||
                    stripos($txn['transaction_ref'] ?? '', $searchLower) !== false ||
                    stripos($txn['recipient_name'] ?? '', $searchLower) !== false
                );
            });
            // Re-index array after filtering
            $allTransactions = array_values($allTransactions);
        }
        
        $totalRecords = count($allTransactions);
        $totalPages = ceil($totalRecords / $limit);
        
        // Apply pagination
        $offset = ($page - 1) * $limit;
        $transactions = array_slice($allTransactions, $offset, $limit);
        
        // Get transaction statistics for charts (include joint accounts)
        // Get accessible account IDs
        $accountIds = [];
        if (class_exists('JointAccount')) {
            require_once __DIR__ . '/../models/JointAccount.php';
            $jointAccount = new JointAccount();
            $accessibleAccounts = $jointAccount->getUserAccessibleAccounts($userId);
            $accountIds = array_column($accessibleAccounts, 'id');
        } else {
            require_once __DIR__ . '/../models/Account.php';
            $accountModel = new Account();
            $userAccounts = $accountModel->getUserAccounts($userId);
            $accountIds = array_column($userAccounts, 'id');
        }
        
        if (empty($accountIds)) {
            $monthlyStats = [];
        } else {
            $placeholders = implode(',', array_fill(0, count($accountIds), '?'));
            $sqlStats = "SELECT 
                            DATE_FORMAT(created_at, '%Y-%m') as month,
                            SUM(CASE WHEN transaction_type = 'credit' THEN amount ELSE 0 END) as total_credit,
                            SUM(CASE WHEN transaction_type = 'debit' THEN amount ELSE 0 END) as total_debit,
                            COUNT(*) as transaction_count
                         FROM transactions 
                         WHERE account_id IN ($placeholders)
                         AND created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                         GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                         ORDER BY month DESC
                         LIMIT 6";
            $stmtStats = $db->query($sqlStats, $accountIds);
            $monthlyStats = ($stmtStats !== false) ? ($stmtStats->fetchAll() ?: []) : [];
        }
        
        // Get category breakdown
        $sqlCategory = "SELECT category, COUNT(*) as count, SUM(amount) as total
                        FROM transactions 
                        WHERE user_id = ? 
                        AND transaction_type = 'debit'
                        AND created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                        GROUP BY category";
        $stmtCategory = $db->query($sqlCategory, [$userId]);
        $categoryStats = ($stmtCategory !== false) ? ($stmtCategory->fetchAll() ?: []) : [];
        
        $pageTitle = 'Transaction History';
        // Get user currency
        $sqlUser = "SELECT currency FROM users WHERE id = ?";
        $stmtUser = $db->query($sqlUser, [$userId]);
        $userData = ($stmtUser !== false) ? ($stmtUser->fetch() ?: ['currency' => 'USD']) : ['currency' => 'USD'];
        
        // Debug: Log what we're about to pass to the view
        error_log("Transaction Controller - Preparing data for view:");
        error_log("  - All transactions found (before pagination): " . count($allTransactions));
        error_log("  - Transactions after pagination: " . count($transactions));
        error_log("  - Total records: " . $totalRecords);
        error_log("  - User ID: " . $userId);
        error_log("  - Date range: $fromDate to $toDate");
        if (count($transactions) > 0) {
            error_log("  - First transaction ID: " . ($transactions[0]['id'] ?? 'N/A'));
            error_log("  - First transaction category: " . ($transactions[0]['category'] ?? 'N/A'));
            error_log("  - First transaction date: " . ($transactions[0]['created_at'] ?? 'N/A'));
        }
        
        $data = [
            'transactions' => $transactions,
            'monthly_stats' => $monthlyStats,
            'category_stats' => $categoryStats,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_records' => $totalRecords,
            'limit' => $limit,
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'status' => $status,
            'type' => $type,
            'search' => $search,
            'user_currency' => $userData['currency'] ?? 'USD'
        ];
        
        include 'views/transaction/index.php';
    }
    
    private function viewReceipt() {
        $transactionId = $_GET['id'] ?? null;
        $isShared = isset($_GET['share']) && $_GET['share'] === '1';
        
        if (!$transactionId) {
            redirect('/transaction');
            return;
        }
        
        // For shared receipts, don't require login
        if (!$isShared) {
            requireLogin();
        }
        
        $db = Database::getInstance();
        $userId = $_SESSION['user_id'] ?? null;
        
        // Get transaction details - check if transactionId is a ref or numeric ID
        $isNumericId = is_numeric($transactionId);
        $whereClause = $isNumericId ? "t.id = ?" : "t.transaction_ref = ?";
        
        if ($isShared) {
            // For shared receipts, don't filter by user_id
            $sql = "SELECT t.*, a.account_number, a.account_type, u.full_name as user_name, u.email as user_email
                    FROM transactions t
                    LEFT JOIN accounts a ON t.account_id = a.id
                    LEFT JOIN users u ON t.user_id = u.id
                    WHERE $whereClause";
            $stmt = $db->query($sql, [$transactionId]);
        } else {
            // For regular receipts, check if user has access (own transaction OR joint account access)
            require_once __DIR__ . '/../models/JointAccount.php';
            $jointAccount = new JointAccount();
            
            $sql = "SELECT t.*, a.account_number, a.account_type, u.full_name as user_name, u.email as user_email
                    FROM transactions t
                    LEFT JOIN accounts a ON t.account_id = a.id
                    LEFT JOIN users u ON t.user_id = u.id
                    WHERE $whereClause";
            $stmt = $db->query($sql, [$transactionId]);
            $transaction = $stmt->fetch();
            
            // Check access: user owns the transaction OR has access to the account via joint ownership
            if ($transaction) {
                $hasAccess = ($transaction['user_id'] == $userId) || 
                            ($transaction['account_id'] && $jointAccount->userHasAccess($userId, $transaction['account_id']));
                
                if (!$hasAccess) {
                    $transaction = null; // Deny access
                }
            }
        }
        
        if (!$transaction) {
            $_SESSION['error'] = 'Transaction not found';
            redirect('/transaction');
            return;
        }
        
        // Determine transfer type from metadata
        $metadata = json_decode($transaction['metadata'] ?? '{}', true) ?: [];
        require_once __DIR__ . '/../includes/transfer-rails.php';
        $transferType = inferTransferSubType($metadata, $transaction['category'] ?? '');
        $receiptFields = getReceiptFields($transaction);
        
        $pageTitle = 'Transaction Receipt';
        $data = [
            'transaction' => $transaction,
            'transfer_type' => $transferType,
            'receipt_title' => getTransferReceiptTitle($transferType, $metadata),
            'metadata' => $metadata,
            'receipt_fields' => $receiptFields,
            'is_shared' => $isShared
        ];
        
        include 'views/transaction/receipt.php';
    }
}

