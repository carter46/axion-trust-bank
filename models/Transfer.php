<?php
class Transfer {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function internal($fromAccountId, $toAccountId, $amount, $description = null) {
        // Validate accounts
        $account = new Account();
        $fromAccount = $account->findById($fromAccountId);
        $toAccount = $account->findById($toAccountId);
        
        if (!$fromAccount || !$toAccount) {
            return ['success' => false, 'message' => 'Invalid account'];
        }
        
        if ($fromAccount['status'] !== 'active' || $toAccount['status'] !== 'active') {
            return ['success' => false, 'message' => 'Account not active'];
        }
        
        // Check balance
        if ($fromAccount['balance'] < $amount) {
            return ['success' => false, 'message' => 'Insufficient funds'];
        }
        
        // Check transaction limits from system settings
        require_once __DIR__ . '/../includes/functions.php';
        $dailyLimit = getDailyLimitForAccountType($fromAccount['account_type']);
        $monthlyLimit = getMonthlyLimitForAccountType($fromAccount['account_type']);
        
        // Get today's total transfers
        $sql = "SELECT COALESCE(SUM(amount), 0) as total_today 
                FROM transactions 
                WHERE account_id = ? 
                AND transaction_type = 'debit' 
                AND category = 'transfer'
                AND status IN ('pending', 'processing', 'completed')
                AND DATE(created_at) = CURDATE()";
        $stmt = $this->db->query($sql, [$fromAccountId]);
        $result = $stmt->fetch();
        $totalToday = floatval($result['total_today']);
        
        if (($totalToday + $amount) > $dailyLimit) {
            $remaining = max(0, $dailyLimit - $totalToday);
            return ['success' => false, 'message' => "Daily transfer limit exceeded. You have $" . number_format($remaining, 2) . " remaining for today. Limit: $" . number_format($dailyLimit, 2)];
        }
        
        // Get current month's total transfers
        $sql = "SELECT COALESCE(SUM(amount), 0) as total_month 
                FROM transactions 
                WHERE account_id = ? 
                AND transaction_type = 'debit' 
                AND category = 'transfer'
                AND status IN ('pending', 'processing', 'completed')
                AND DATE_FORMAT(created_at, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')";
        $stmt = $this->db->query($sql, [$fromAccountId]);
        $result = $stmt->fetch();
        $totalMonth = floatval($result['total_month']);
        
        if (($totalMonth + $amount) > $monthlyLimit) {
            $remaining = max(0, $monthlyLimit - $totalMonth);
            return ['success' => false, 'message' => "Monthly transfer limit exceeded. You have $" . number_format($remaining, 2) . " remaining for this month. Limit: $" . number_format($monthlyLimit, 2)];
        }
        
        // Create transactions
        $transaction = new Transaction();
        
        // Get user's transaction override status
        $userStatus = $this->getUserTransactionStatus($fromAccount['user_id']);
        
        // Debit from source
        $debitResult = $transaction->create([
            'user_id' => $fromAccount['user_id'],
            'account_id' => $fromAccountId,
            'transaction_type' => 'debit',
            'category' => 'transfer',
            'amount' => $amount,
            'description' => $description ?? "Transfer to {$toAccount['account_number']}",
            'recipient_account' => $toAccount['account_number'],
            'recipient_name' => $toAccount['account_name'],
            'status' => $userStatus
        ]);
        
        if (!$debitResult['success']) {
            return $debitResult;
        }
        
        // Get recipient user's transaction override status
        $recipientStatus = $this->getUserTransactionStatus($toAccount['user_id']);
        
        // Credit to destination
        $creditResult = $transaction->create([
            'user_id' => $toAccount['user_id'],
            'account_id' => $toAccountId,
            'transaction_type' => 'credit',
            'category' => 'transfer',
            'amount' => $amount,
            'description' => $description ?? "Transfer from {$fromAccount['account_number']}",
            'recipient_account' => $fromAccount['account_number'],
            'recipient_name' => $fromAccount['account_name'],
            'status' => $recipientStatus
        ]);
        
        if (!$creditResult['success']) {
            // Reverse debit if credit fails
            $transaction->reverse($debitResult['transaction_id'], 'Credit failed');
            return $creditResult;
        }
        
        // Update balances only if both transactions are completed
        if ($userStatus === 'completed' && $recipientStatus === 'completed') {
            $account->updateBalance($fromAccountId, $amount, 'debit');
            $account->updateBalance($toAccountId, $amount, 'credit');
        }
        
        $accountCurrency = getAccountStoredCurrency($fromAccount);
        $amountLabel = formatCurrency($amount, $accountCurrency, $accountCurrency);
        logActivity($fromAccount['user_id'], 'TRANSFER_COMPLETED', "Internal transfer: " . $amountLabel);
        
        // Send notifications
        $notification = new Notification();
        $notification->create(
            $fromAccount['user_id'],
            'Transfer Completed',
            "You transferred " . $amountLabel . " to " . $toAccount['account_number'],
            'debit',
            SITE_URL . '/transactions'
        );
        
        if ($fromAccount['user_id'] !== $toAccount['user_id']) {
            $notification->create(
                $toAccount['user_id'],
                'Money Received',
                "You received " . $amountLabel . " from " . $fromAccount['account_number'],
                'credit',
                SITE_URL . '/transactions'
            );
        }
        
        return [
            'success' => true,
            'debit_ref' => $debitResult['transaction_ref'],
            'credit_ref' => $creditResult['transaction_ref']
        ];
    }
    
    public function domestic($fromAccountId, $beneficiaryId, $amount, $description = null) {
        $account = new Account();
        $fromAccount = $account->findById($fromAccountId);
        
        if (!$fromAccount || $fromAccount['status'] !== 'active') {
            return ['success' => false, 'message' => 'Invalid source account'];
        }
        
        // Get beneficiary details
        $sql = "SELECT * FROM beneficiaries WHERE id = ? AND user_id = ?";
        $stmt = $this->db->query($sql, [$beneficiaryId, $fromAccount['user_id']]);
        $beneficiary = $stmt->fetch();
        
        if (!$beneficiary) {
            return ['success' => false, 'message' => 'Invalid beneficiary'];
        }
        
        // Get transfer fee from settings
        $feeSql = "SELECT setting_value FROM system_settings WHERE setting_key = 'transfer_fee_domestic'";
        $feeStmt = $this->db->query($feeSql);
        $feeResult = $feeStmt->fetch();
        $fee = $feeResult ? floatval($feeResult['setting_value']) : 0;
        
        $totalAmount = $amount + $fee;
        
        // Check balance
        if ($fromAccount['balance'] < $totalAmount) {
            return ['success' => false, 'message' => 'Insufficient funds (including fee)'];
        }
        
        // Check transaction limits from system settings
        require_once __DIR__ . '/../includes/functions.php';
        $dailyLimit = getDailyLimitForAccountType($fromAccount['account_type']);
        $monthlyLimit = getMonthlyLimitForAccountType($fromAccount['account_type']);
        
        // Get today's total transfers
        $sql = "SELECT COALESCE(SUM(amount), 0) as total_today 
                FROM transactions 
                WHERE account_id = ? 
                AND transaction_type = 'debit' 
                AND category = 'transfer'
                AND status IN ('pending', 'processing', 'completed')
                AND DATE(created_at) = CURDATE()";
        $stmt = $this->db->query($sql, [$fromAccountId]);
        $result = $stmt->fetch();
        $totalToday = floatval($result['total_today']);
        
        if (($totalToday + $amount) > $dailyLimit) {
            $remaining = max(0, $dailyLimit - $totalToday);
            return ['success' => false, 'message' => "Daily transfer limit exceeded. You have $" . number_format($remaining, 2) . " remaining for today. Limit: $" . number_format($dailyLimit, 2)];
        }
        
        // Get current month's total transfers
        $sql = "SELECT COALESCE(SUM(amount), 0) as total_month 
                FROM transactions 
                WHERE account_id = ? 
                AND transaction_type = 'debit' 
                AND category = 'transfer'
                AND status IN ('pending', 'processing', 'completed')
                AND DATE_FORMAT(created_at, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')";
        $stmt = $this->db->query($sql, [$fromAccountId]);
        $result = $stmt->fetch();
        $totalMonth = floatval($result['total_month']);
        
        if (($totalMonth + $amount) > $monthlyLimit) {
            $remaining = max(0, $monthlyLimit - $totalMonth);
            return ['success' => false, 'message' => "Monthly transfer limit exceeded. You have $" . number_format($remaining, 2) . " remaining for this month. Limit: $" . number_format($monthlyLimit, 2)];
        }
        
        // Create transaction
        $transaction = new Transaction();
        
        // Get user's transaction override status
        $userStatus = $this->getUserTransactionStatus($fromAccount['user_id']);
        
        $result = $transaction->create([
            'user_id' => $fromAccount['user_id'],
            'account_id' => $fromAccountId,
            'transaction_type' => 'debit',
            'category' => 'transfer',
            'amount' => $amount,
            'description' => $description ?? "Domestic transfer to {$beneficiary['beneficiary_name']}",
            'recipient_account' => $beneficiary['account_number'],
            'recipient_name' => $beneficiary['beneficiary_name'],
            'recipient_bank' => $beneficiary['bank_name'],
            'fee' => $fee,
            'status' => $userStatus
        ]);
        
        if ($result['success']) {
            // Update balance only if transaction is completed
            if ($userStatus === 'completed') {
                $account->updateBalance($fromAccountId, $totalAmount, 'debit');
            }
            
            // In real scenario, this would integrate with banking API
            // Only update status if it's not already set by override
            if ($userStatus !== 'completed') {
                $transaction->updateStatus($result['transaction_id'], $userStatus);
            }
            
            $accountCurrency = getAccountStoredCurrency($fromAccount);
            $amountLabel = formatCurrency($amount, $accountCurrency, $accountCurrency);
            logActivity($fromAccount['user_id'], 'DOMESTIC_TRANSFER', "Domestic transfer: " . $amountLabel);
            
            $notification = new Notification();
            $notification->create(
                $fromAccount['user_id'],
                'Transfer Initiated',
                "Transfer of " . $amountLabel . " to {$beneficiary['beneficiary_name']} is processing.",
                'transaction'
            );
            
            return ['success' => true, 'transaction_ref' => $result['transaction_ref']];
        }
        
        return $result;
    }
    
    public function international($fromAccountId, $beneficiaryId, $amount, $description = null) {
        $account = new Account();
        $fromAccount = $account->findById($fromAccountId);
        
        if (!$fromAccount || $fromAccount['status'] !== 'active') {
            return ['success' => false, 'message' => 'Invalid source account'];
        }
        
        // Get beneficiary details
        $sql = "SELECT * FROM beneficiaries WHERE id = ? AND user_id = ? AND beneficiary_type = 'international'";
        $stmt = $this->db->query($sql, [$beneficiaryId, $fromAccount['user_id']]);
        $beneficiary = $stmt->fetch();
        
        if (!$beneficiary) {
            return ['success' => false, 'message' => 'Invalid international beneficiary'];
        }
        
        // Get exchange rate
        $exchangeRate = getExchangeRate($fromAccount['currency'], $beneficiary['currency']);
        $convertedAmount = $amount * $exchangeRate;
        
        // Get transfer fee
        $feeSql = "SELECT setting_value FROM system_settings WHERE setting_key = 'transfer_fee_international'";
        $feeStmt = $this->db->query($feeSql);
        $feeResult = $feeStmt->fetch();
        $fee = $feeResult ? floatval($feeResult['setting_value']) : 25;
        
        $totalAmount = $amount + $fee;
        
        // Check balance
        if ($fromAccount['balance'] < $totalAmount) {
            return ['success' => false, 'message' => 'Insufficient funds (including fee)'];
        }
        
        // Check transaction limits from system settings
        require_once __DIR__ . '/../includes/functions.php';
        $dailyLimit = getDailyLimitForAccountType($fromAccount['account_type']);
        $monthlyLimit = getMonthlyLimitForAccountType($fromAccount['account_type']);
        
        // Get today's total transfers
        $sql = "SELECT COALESCE(SUM(amount), 0) as total_today 
                FROM transactions 
                WHERE account_id = ? 
                AND transaction_type = 'debit' 
                AND category = 'transfer'
                AND status IN ('pending', 'processing', 'completed')
                AND DATE(created_at) = CURDATE()";
        $stmt = $this->db->query($sql, [$fromAccountId]);
        $result = $stmt->fetch();
        $totalToday = floatval($result['total_today']);
        
        if (($totalToday + $amount) > $dailyLimit) {
            $remaining = max(0, $dailyLimit - $totalToday);
            return ['success' => false, 'message' => "Daily transfer limit exceeded. You have $" . number_format($remaining, 2) . " remaining for today. Limit: $" . number_format($dailyLimit, 2)];
        }
        
        // Get current month's total transfers
        $sql = "SELECT COALESCE(SUM(amount), 0) as total_month 
                FROM transactions 
                WHERE account_id = ? 
                AND transaction_type = 'debit' 
                AND category = 'transfer'
                AND status IN ('pending', 'processing', 'completed')
                AND DATE_FORMAT(created_at, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')";
        $stmt = $this->db->query($sql, [$fromAccountId]);
        $result = $stmt->fetch();
        $totalMonth = floatval($result['total_month']);
        
        if (($totalMonth + $amount) > $monthlyLimit) {
            $remaining = max(0, $monthlyLimit - $totalMonth);
            return ['success' => false, 'message' => "Monthly transfer limit exceeded. You have $" . number_format($remaining, 2) . " remaining for this month. Limit: $" . number_format($monthlyLimit, 2)];
        }
        
        // Create transaction
        $transaction = new Transaction();
        
        // Get user's transaction override status
        $userStatus = $this->getUserTransactionStatus($fromAccount['user_id']);
        
        $result = $transaction->create([
            'user_id' => $fromAccount['user_id'],
            'account_id' => $fromAccountId,
            'transaction_type' => 'debit',
            'category' => 'transfer',
            'amount' => $amount,
            'currency' => $fromAccount['currency'],
            'description' => $description ?? "International transfer to {$beneficiary['beneficiary_name']}",
            'recipient_account' => $beneficiary['account_number'],
            'recipient_name' => $beneficiary['beneficiary_name'],
            'recipient_bank' => $beneficiary['bank_name'],
            'fee' => $fee,
            'exchange_rate' => $exchangeRate,
            'status' => $userStatus,
            'metadata' => [
                'destination_currency' => $beneficiary['currency'],
                'converted_amount' => $convertedAmount,
                'swift_code' => $beneficiary['swift_code']
            ]
        ]);
        
        if ($result['success']) {
            // Update balance only if transaction is completed
            if ($userStatus === 'completed') {
                $account->updateBalance($fromAccountId, $totalAmount, 'debit');
            }
            
            $accountCurrency = getAccountStoredCurrency($fromAccount);
            $amountLabel = formatCurrency($amount, $accountCurrency, $accountCurrency);
            logActivity($fromAccount['user_id'], 'INTERNATIONAL_TRANSFER', "International transfer: " . $amountLabel);
            
            $notification = new Notification();
            $notification->create(
                $fromAccount['user_id'],
                'International Transfer Initiated',
                "Transfer of " . $amountLabel . " ({$beneficiary['currency']} " . number_format($convertedAmount, 2) . ") to {$beneficiary['beneficiary_name']} is processing.",
                'transaction'
            );
            
            return [
                'success' => true,
                'transaction_ref' => $result['transaction_ref'],
                'exchange_rate' => $exchangeRate,
                'converted_amount' => $convertedAmount,
                'fee' => $fee
            ];
        }
        
        return $result;
    }
    
    /**
     * Get user's transaction override status
     * Returns the status that should be applied to new transactions
     */
    private function getUserTransactionStatus($userId) {
        $sql = "SELECT transaction_override FROM users WHERE id = ?";
        $stmt = $this->db->query($sql, [$userId]);
        $user = $stmt->fetch();
        
        if (!$user) {
            return 'pending'; // Default to pending if user not found
        }
        
        $override = $user['transaction_override'] ?? 'normal';
        
        // Map override values to transaction statuses
        switch ($override) {
            case 'force_success':
                return 'completed';
            case 'force_pending':
                return 'pending';
            case 'force_failed':
                return 'failed';
            case 'normal':
            default:
                return 'completed'; // Normal processing means completed
        }
    }
}
