<?php
class Transaction {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        $transactionRef = 'TXN' . date('Ymd') . strtoupper(substr(uniqid(), -8));
        
        // Handle null account_id (for investment balance transactions)
        $balanceBefore = 0;
        $balanceAfter = 0;
        $accountData = null;
        
        if (!empty($data['account_id'])) {
            $account = new Account();
            $accountData = $account->findById($data['account_id']);
            
            // Handle case where account doesn't exist
            if (!$accountData) {
                error_log("Transaction create error: Account not found for account_id: " . $data['account_id']);
                return ['success' => false, 'message' => 'Account not found'];
            }
            
            $balanceBefore = $accountData['balance'];
            // Calculate balance after (only if account exists)
            $balanceAfter = $data['transaction_type'] === 'credit' 
                ? $balanceBefore + $data['amount']
                : $balanceBefore - $data['amount'];
        } else {
            // For investment balance transactions, use provided balances or 0
            $balanceBefore = $data['balance_before'] ?? 0;
            $balanceAfter = $data['balance_after'] ?? 0;
        }

        // Default transaction currency should match the account currency when possible
        $currency = $data['currency'] ?? null;
        if (!$currency && $accountData && !empty($accountData['currency'])) {
            $currency = $accountData['currency'];
        }
        $currency = strtoupper(trim($currency ?: DEFAULT_CURRENCY));
        
        $sql = "INSERT INTO transactions (
                    transaction_ref, user_id, account_id, transaction_type, category, expense_category, amount, currency,
                    balance_before, balance_after, description, recipient_account, recipient_name,
                    recipient_bank, status, payment_method, fee, exchange_rate, metadata, ip_address
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $result = $this->db->query($sql, [
            $transactionRef,
            $data['user_id'],
            $data['account_id'],
            $data['transaction_type'],
            $data['category'],
            $data['expense_category'] ?? $data['category'], // Use expense_category if provided, otherwise use category
            $data['amount'],
            $currency,
            $balanceBefore,
            $balanceAfter,
            $data['description'] ?? null,
            $data['recipient_account'] ?? null,
            $data['recipient_name'] ?? null,
            $data['recipient_bank'] ?? null,
            $data['status'] ?? 'pending',
            $data['payment_method'] ?? null,
            $data['fee'] ?? 0,
            $data['exchange_rate'] ?? null,
            isset($data['metadata']) ? json_encode($data['metadata']) : null,
            $_SERVER['REMOTE_ADDR']
        ]);
        
        if ($result) {
            $transactionId = $this->db->lastInsertId();
            
            // Update account balance if status is completed
            if (($data['status'] ?? 'pending') === 'completed') {
                $account->updateBalance($data['account_id'], $data['amount'], $data['transaction_type']);
            }
            
            // Log activity
            logActivity($data['user_id'], 'TRANSACTION_CREATED', "Transaction {$transactionRef} created");
            
            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'transaction_ref' => $transactionRef
            ];
        }
        
        return ['success' => false, 'message' => 'Failed to create transaction'];
    }
    
    public function findById($id) {
        $sql = "SELECT * FROM transactions WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }
    
    public function findByReference($ref) {
        $sql = "SELECT * FROM transactions WHERE transaction_ref = ?";
        $stmt = $this->db->query($sql, [$ref]);
        return $stmt->fetch();
    }
    
    public function getUserTransactions($userId, $filters = []) {
        // Get account IDs user has access to (own + joint)
        $accountIds = [];
        if (class_exists('JointAccount')) {
            $jointAccount = new JointAccount();
            $accessibleAccounts = $jointAccount->getUserAccessibleAccounts($userId);
            $accountIds = array_column($accessibleAccounts, 'id');
        } else {
            // Fallback: get user's own accounts
            $accountModel = new Account();
            $userAccounts = $accountModel->getUserAccounts($userId);
            $accountIds = array_column($userAccounts, 'id');
        }
        
        if (empty($accountIds)) {
            return [];
        }
        
        $placeholders = implode(',', array_fill(0, count($accountIds), '?'));
        $sql = "SELECT t.*, a.account_number, a.account_type 
                FROM transactions t 
                LEFT JOIN accounts a ON t.account_id = a.id
                WHERE t.account_id IN ($placeholders)";
        
        $params = $accountIds;
        
        if (isset($filters['account_id'])) {
            $sql .= " AND t.account_id = ?";
            $params[] = $filters['account_id'];
        }
        
        if (isset($filters['type'])) {
            $sql .= " AND t.transaction_type = ?";
            $params[] = $filters['type'];
        }
        
        if (isset($filters['category'])) {
            $sql .= " AND t.category = ?";
            $params[] = $filters['category'];
        }
        
        if (isset($filters['status'])) {
            $sql .= " AND t.status = ?";
            $params[] = $filters['status'];
        }
        
        if (isset($filters['date_from'])) {
            $sql .= " AND DATE(t.created_at) >= ?";
            $params[] = $filters['date_from'];
        }
        
        if (isset($filters['date_to'])) {
            // Include the entire day - use <= but ensure we get all transactions from that day
            $sql .= " AND DATE(t.created_at) <= ?";
            $params[] = $filters['date_to'];
        }
        
        if (isset($filters['min_amount'])) {
            $sql .= " AND t.amount >= ?";
            $params[] = $filters['min_amount'];
        }
        
        if (isset($filters['max_amount'])) {
            $sql .= " AND t.amount <= ?";
            $params[] = $filters['max_amount'];
        }
        
        $sql .= " ORDER BY t.created_at DESC";
        
        if (isset($filters['limit'])) {
            $sql .= " LIMIT " . intval($filters['limit']);
        }
        
        $stmt = $this->db->query($sql, $params);
        return ($stmt && $stmt !== false) ? $stmt->fetchAll() : [];
    }
    
    public function getAccountTransactions($accountId, $limit = 50, $userId = null) {
        // If userId provided, verify access (for joint accounts)
        if ($userId && class_exists('JointAccount')) {
            $jointAccount = new JointAccount();
            if (!$jointAccount->userHasAccess($userId, $accountId)) {
                return []; // User doesn't have access
            }
        }
        
        $sql = "SELECT * FROM transactions WHERE account_id = ? ORDER BY created_at DESC LIMIT ?";
        $stmt = $this->db->query($sql, [$accountId, $limit]);
        return ($stmt && $stmt !== false) ? $stmt->fetchAll() : [];
    }
    
    public function updateStatus($transactionId, $status) {
        $sql = "UPDATE transactions SET status = ?, completed_at = NOW() WHERE id = ?";
        $result = $this->db->query($sql, [$status, $transactionId]);
        
        if ($result) {
            $transaction = $this->findById($transactionId);
            
            // Update account balance if completed
            if ($status === 'completed') {
                $account = new Account();
                $account->updateBalance(
                    $transaction['account_id'],
                    $transaction['amount'],
                    $transaction['transaction_type']
                );
                
                // Create notification
                $notification = new Notification();
                $notification->create(
                    $transaction['user_id'],
                    'Transaction Completed',
                    "Your transaction of " . formatTransactionNative($transaction, 'amount') . " has been completed.",
                    'transaction',
                    "/account/transaction/{$transaction['id']}"
                );
            }
            
            logActivity($transaction['user_id'], 'TRANSACTION_STATUS_UPDATED', "Transaction {$transaction['transaction_ref']} status changed to {$status}");
        }
        
        return $result;
    }
    
    public function reverse($transactionId, $reason = null) {
        $transaction = $this->findById($transactionId);
        
        if (!$transaction || $transaction['status'] !== 'completed') {
            return ['success' => false, 'message' => 'Cannot reverse this transaction'];
        }
        
        // Create reverse transaction
        $reverseType = $transaction['transaction_type'] === 'credit' ? 'debit' : 'credit';
        
        $reverseData = [
            'user_id' => $transaction['user_id'],
            'account_id' => $transaction['account_id'],
            'transaction_type' => $reverseType,
            'category' => $transaction['category'],
            'amount' => $transaction['amount'],
            'description' => 'Reversal: ' . $transaction['description'] . ($reason ? " - Reason: $reason" : ''),
            'status' => 'completed',
            'metadata' => ['original_transaction' => $transaction['transaction_ref'], 'reason' => $reason]
        ];
        
        $result = $this->create($reverseData);
        
        if ($result['success']) {
            // Update original transaction
            $updateSql = "UPDATE transactions SET status = 'reversed' WHERE id = ?";
            $this->db->query($updateSql, [$transactionId]);
            
            // Update account balance
            $account = new Account();
            $account->updateBalance($transaction['account_id'], $transaction['amount'], $reverseType);
            
            logActivity($transaction['user_id'], 'TRANSACTION_REVERSED', "Transaction {$transaction['transaction_ref']} reversed");
        }
        
        return $result;
    }
    
    public function getMonthlyStats($userId, $year, $month) {
        $sql = "SELECT 
                    COUNT(*) as transaction_count,
                    SUM(CASE WHEN transaction_type = 'credit' THEN amount ELSE 0 END) as total_income,
                    SUM(CASE WHEN transaction_type = 'debit' THEN amount ELSE 0 END) as total_expenses,
                    expense_category as category,
                    COUNT(*) as category_count
                FROM transactions 
                WHERE user_id = ? 
                AND YEAR(created_at) = ? 
                AND MONTH(created_at) = ?
                AND status = 'completed'
                AND expense_category IS NOT NULL
                GROUP BY expense_category";
        
        $stmt = $this->db->query($sql, [$userId, $year, $month]);
        return ($stmt && $stmt !== false) ? $stmt->fetchAll() : [];
    }
    
    public function getSpendingByCategory($userId, $days = 30) {
        $sql = "SELECT 
                    expense_category as category,
                    SUM(amount) as total,
                    COUNT(*) as count
                FROM transactions 
                WHERE user_id = ? 
                AND transaction_type = 'debit'
                AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                AND status = 'completed'
                AND expense_category IS NOT NULL
                GROUP BY expense_category
                ORDER BY total DESC";
        
        $stmt = $this->db->query($sql, [$userId, $days]);
        return ($stmt && $stmt !== false) ? $stmt->fetchAll() : [];
    }
}
