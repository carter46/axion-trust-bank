<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/User.php';
require_once __DIR__ . '/Account.php';
require_once __DIR__ . '/Transaction.php';

class InvestmentWithdrawal {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // Create withdrawal request
    public function create($data) {
        // Validate user has sufficient investment balance
        $userModel = new User();
        $user = $userModel->findById($data['user_id']);
        $investmentBalance = (float)($user['investment_balance'] ?? 0);
        
        if ($investmentBalance < (float)$data['amount']) {
            return ['success' => false, 'message' => 'Insufficient investment balance'];
        }
        
        $recipientInfo = json_encode($data['recipient_info'] ?? []);
        
        $sql = "INSERT INTO investment_withdrawals (
                    user_id, amount, withdrawal_method, recipient_type,
                    recipient_info, status, notes
                ) VALUES (?, ?, ?, ?, ?, 'pending', ?)";
        
        $result = $this->db->query($sql, [
            $data['user_id'],
            $data['amount'],
            $data['withdrawal_method'],
            $data['recipient_type'] ?? null,
            $recipientInfo,
            $data['notes'] ?? null
        ]);
        
        if ($result) {
            $withdrawalId = $this->db->lastInsertId();
            
            // If withdrawal to bank balance, process immediately
            if ($data['withdrawal_method'] === 'bank_balance' && !empty($data['account_id'])) {
                return $this->processBankWithdrawal($withdrawalId, $data['account_id']);
            }
            
            return ['success' => true, 'withdrawal_id' => $withdrawalId, 'requires_processing' => true];
        }
        
        return ['success' => false, 'message' => 'Failed to create withdrawal request'];
    }
    
    // Process bank balance withdrawal (instant)
    private function processBankWithdrawal($withdrawalId, $accountId) {
        $withdrawal = $this->findById($withdrawalId);
        if (!$withdrawal) {
            return ['success' => false, 'message' => 'Withdrawal request not found'];
        }
        
        $userModel = new User();
        $user = $userModel->findById($withdrawal['user_id']);
        $investmentBalance = (float)($user['investment_balance'] ?? 0);
        
        if ($investmentBalance < (float)$withdrawal['amount']) {
            $this->updateStatus($withdrawalId, 'failed', null, 'Insufficient investment balance');
            return ['success' => false, 'message' => 'Insufficient investment balance'];
        }
        
        $this->db->beginTransaction();
        
        try {
            // Deduct from investment balance
            $newBalance = $investmentBalance - (float)$withdrawal['amount'];
            $sql = "UPDATE users SET investment_balance = ? WHERE id = ?";
            $this->db->query($sql, [$newBalance, $withdrawal['user_id']]);
            
            // Credit account
            $accountModel = new Account();
            $accountModel->updateBalance($accountId, (float)$withdrawal['amount'], 'credit');
            
            // Update withdrawal status
            $transactionRef = 'INV-WD-' . date('Ymd') . strtoupper(substr(uniqid(), -8));
            $this->updateStatus($withdrawalId, 'completed', null, null, $transactionRef);
            
            // Create transaction record
            $transactionModel = new Transaction();
            $transactionModel->create([
                'user_id' => $withdrawal['user_id'],
                'account_id' => $accountId,
                'transaction_type' => 'credit',
                'category' => 'investment_withdrawal',
                'amount' => $withdrawal['amount'],
                'description' => "Investment account withdrawal - #{$withdrawalId}",
                'status' => 'completed'
            ]);
            
            $this->db->commit();
            return ['success' => true, 'withdrawal_id' => $withdrawalId, 'message' => 'Withdrawal completed'];
            
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Investment Withdrawal Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to process withdrawal'];
        }
    }
    
    // Find by ID
    public function findById($id) {
        $sql = "SELECT * FROM investment_withdrawals WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt ? $stmt->fetch() : null;
    }
    
    // Update status
    public function updateStatus($id, $status, $processedBy = null, $rejectionReason = null, $transactionRef = null) {
        $sql = "UPDATE investment_withdrawals SET 
                status = ?, 
                processed_at = NOW(),
                processed_by = ?,
                rejection_reason = ?,
                transaction_ref = ?
                WHERE id = ?";
        return $this->db->query($sql, [$status, $processedBy, $rejectionReason, $transactionRef, $id]);
    }
    
    // Get user withdrawal history
    public function getUserWithdrawals($userId, $limit = 50) {
        $sql = "SELECT * FROM investment_withdrawals 
                WHERE user_id = ? 
                ORDER BY created_at DESC 
                LIMIT ?";
        $stmt = $this->db->query($sql, [$userId, $limit]);
        return $stmt ? $stmt->fetchAll() : [];
    }
}

