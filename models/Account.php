<?php
class Account {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($userId, $type, $name = null) {
        $accountNumber = generateAccountNumber();
        $accountName = $name ?: ucfirst($type) . ' Account';
        
        // Get limits from system settings based on account type
        require_once __DIR__ . '/../includes/functions.php';
        $dailyLimit = getDailyLimitForAccountType($type);

        // New accounts always use the site default ledger currency (admin setting).
        $currency = DEFAULT_CURRENCY;
        
        $sql = "INSERT INTO accounts (user_id, account_number, account_type, account_name, balance, available_balance, currency, daily_limit) 
                VALUES (?, ?, ?, ?, 0.00, 0.00, ?, ?)";
        
        $result = $this->db->query($sql, [
            $userId,
            $accountNumber,
            $type,
            $accountName,
            $currency,
            $dailyLimit
        ]);
        
        if ($result) {
            $accountId = $this->db->lastInsertId();
            logActivity($userId, 'ACCOUNT_CREATED', "Created {$type} account: {$accountNumber}");
            return $accountId;
        }
        
        return false;
    }
    
    public function findById($id) {
        $sql = "SELECT * FROM accounts WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }
    
    public function findByAccountNumber($accountNumber) {
        $sql = "SELECT * FROM accounts WHERE account_number = ?";
        $stmt = $this->db->query($sql, [$accountNumber]);
        return $stmt->fetch();
    }
    
    public function getUserAccounts($userId) {
        // Check if JointAccount class exists and use it for joint accounts
        if (class_exists('JointAccount')) {
            $jointAccount = new JointAccount();
            return $jointAccount->getUserAccessibleAccounts($userId);
        }
        
        // Fallback to original query
        $sql = "SELECT * FROM accounts WHERE user_id = ? AND status != 'closed' ORDER BY created_at ASC";
        $stmt = $this->db->query($sql, [$userId]);
        return $stmt->fetchAll();
    }
    
    public function getBalance($accountId) {
        $account = $this->findById($accountId);
        return $account ? $account['balance'] : 0;
    }
    
    public function updateBalance($accountId, $amount, $type = 'credit') {
        $account = $this->findById($accountId);
        
        if (!$account) {
            return false;
        }
        
        $newBalance = $type === 'credit' 
            ? $account['balance'] + $amount 
            : $account['balance'] - $amount;
        
        if ($newBalance < 0 && abs($newBalance) > $account['overdraft_limit']) {
            return false; // Insufficient funds
        }
        
        $sql = "UPDATE accounts SET balance = ?, available_balance = ?, updated_at = NOW() WHERE id = ?";
        return $this->db->query($sql, [$newBalance, $newBalance, $accountId]);
    }
    
    public function freeze($accountId) {
        $sql = "UPDATE accounts SET status = 'frozen' WHERE id = ?";
        $result = $this->db->query($sql, [$accountId]);
        
        if ($result) {
            $account = $this->findById($accountId);
            logActivity($account['user_id'], 'ACCOUNT_FROZEN', "Account {$account['account_number']} frozen");
        }
        
        return $result;
    }
    
    public function unfreeze($accountId) {
        $sql = "UPDATE accounts SET status = 'active' WHERE id = ?";
        $result = $this->db->query($sql, [$accountId]);
        
        if ($result) {
            $account = $this->findById($accountId);
            logActivity($account['user_id'], 'ACCOUNT_UNFROZEN', "Account {$account['account_number']} unfrozen");
        }
        
        return $result;
    }
    
    public function close($accountId) {
        $account = $this->findById($accountId);
        
        if ($account['balance'] != 0) {
            return ['success' => false, 'message' => 'Cannot close account with non-zero balance'];
        }
        
        $sql = "UPDATE accounts SET status = 'closed', closed_at = NOW() WHERE id = ?";
        $result = $this->db->query($sql, [$accountId]);
        
        if ($result) {
            logActivity($account['user_id'], 'ACCOUNT_CLOSED', "Account {$account['account_number']} closed");
            return ['success' => true];
        }
        
        return ['success' => false, 'message' => 'Failed to close account'];
    }
    
    public function update($accountId, $data) {
        $fields = [];
        $values = [];
        
        $allowedFields = ['account_name', 'overdraft_limit'];
        
        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $fields[] = "$key = ?";
                $values[] = $value;
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $accountId;
        
        $sql = "UPDATE accounts SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = ?";
        return $this->db->query($sql, $values);
    }
    
    public function getTotalBalance($userId) {
        // Check if JointAccount class exists and use it for joint accounts
        if (class_exists('JointAccount')) {
            $jointAccount = new JointAccount();
            $accounts = $jointAccount->getUserAccessibleAccounts($userId);
            $total = 0;
            foreach ($accounts as $account) {
                if ($account['status'] === 'active') {
                    $total += (float)($account['balance'] ?? 0);
                }
            }
            return $total;
        }
        
        // Fallback to original query
        $sql = "SELECT SUM(balance) as total FROM accounts WHERE user_id = ? AND status = 'active'";
        $stmt = $this->db->query($sql, [$userId]);
        $result = $stmt->fetch();
        return $result['total'] ?: 0;
    }
    
    public function getAccountSummary($userId) {
        $accounts = $this->getUserAccounts($userId);
        $totalBalance = 0;
        $accountCount = 0;
        
        foreach ($accounts as $account) {
            if ($account['status'] === 'active') {
                $totalBalance += $account['balance'];
                $accountCount++;
            }
        }
        
        return [
            'accounts' => $accounts,
            'total_balance' => $totalBalance,
            'account_count' => $accountCount
        ];
    }
}
