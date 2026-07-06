<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Account.php';
require_once __DIR__ . '/User.php';
require_once __DIR__ . '/Transaction.php';

class InvestmentFunding {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // Create funding request
    public function create($data) {
        $sql = "INSERT INTO investment_funding (
                    user_id, amount, funding_method, crypto_currency, 
                    crypto_address, account_id, status, notes
                ) VALUES (?, ?, ?, ?, ?, ?, 'pending', ?)";
        
        $result = $this->db->query($sql, [
            $data['user_id'],
            $data['amount'],
            $data['funding_method'],
            $data['crypto_currency'] ?? null,
            $data['crypto_address'] ?? null,
            $data['account_id'] ?? null,
            $data['notes'] ?? null
        ]);
        
        if ($result) {
            $fundingId = $this->db->getConnection()->lastInsertId();
            
            // If funding from bank balance, process immediately
            if ($data['funding_method'] === 'bank_balance' && !empty($data['account_id'])) {
                return $this->processBankFunding($fundingId);
            }
            
            return ['success' => true, 'funding_id' => $fundingId, 'requires_crypto_payment' => strpos($data['funding_method'], 'crypto_') !== false];
        }
        
        return ['success' => false, 'message' => 'Failed to create funding request'];
    }
    
    // Process bank balance funding (instant)
    private function processBankFunding($fundingId) {
        $funding = $this->findById($fundingId);
        if (!$funding) {
            return ['success' => false, 'message' => 'Funding request not found'];
        }
        
        $accountModel = new Account();
        $account = $accountModel->findById($funding['account_id']);
        
        if (!$account || (float)$account['balance'] < (float)$funding['amount']) {
            $this->updateStatus($fundingId, 'failed', null, 'Insufficient account balance');
            return ['success' => false, 'message' => 'Insufficient account balance'];
        }
        
        $this->db->beginTransaction();
        
        try {
            // Deduct from account
            $accountModel->updateBalance($funding['account_id'], (float)$funding['amount'], 'debit');
            
            // Credit investment balance
            $userModel = new User();
            $user = $userModel->findById($funding['user_id']);
            $newBalance = (float)($user['investment_balance'] ?? 0) + (float)$funding['amount'];
            $sql = "UPDATE users SET investment_balance = ? WHERE id = ?";
            $this->db->query($sql, [$newBalance, $funding['user_id']]);
            
            // Update funding status
            $this->updateStatus($fundingId, 'completed', null, 'Bank funding completed successfully');
            
            // Create transaction record
            $transactionModel = new Transaction();
            $transactionModel->create([
                'user_id' => $funding['user_id'],
                'account_id' => $funding['account_id'],
                'transaction_type' => 'debit',
                'category' => 'investment_funding',
                'amount' => $funding['amount'],
                'description' => "Investment account funding - #{$fundingId}",
                'status' => 'completed'
            ]);
            
            $this->db->commit();
            return ['success' => true, 'funding_id' => $fundingId, 'message' => 'Funding completed'];
            
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Investment Funding Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to process funding'];
        }
    }
    
    // Find by ID
    public function findById($id) {
        $sql = "SELECT * FROM investment_funding WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt ? $stmt->fetch() : null;
    }
    
    // Update status
    public function updateStatus($id, $status, $processedBy = null, $notes = null) {
        $sql = "UPDATE investment_funding SET 
                status = ?, 
                processed_at = NOW(),
                processed_by = ?,
                notes = ?
                WHERE id = ?";
        return $this->db->query($sql, [$status, $processedBy, $notes, $id]);
    }
    
    // Get user funding history
    public function getUserFunding($userId, $limit = 50) {
        $sql = "SELECT * FROM investment_funding 
                WHERE user_id = ? 
                ORDER BY created_at DESC 
                LIMIT ?";
        $stmt = $this->db->query($sql, [$userId, $limit]);
        return $stmt ? $stmt->fetchAll() : [];
    }
    
    // Get user funding history - filters out pending crypto without hash
    // All crypto funding must have a hash before appearing in user history
    public function getUserFundingWithHashFilter($userId, $limit = 50) {
        $sql = "SELECT * FROM investment_funding 
                WHERE user_id = ?
                AND (
                    -- Bank funding: show all
                    funding_method = 'bank_balance'
                    OR
                    -- Crypto funding: only show if NOT pending OR (pending AND has hash)
                    (
                        (
                            funding_method LIKE 'crypto_%'
                            OR funding_method = 'crypto_other'
                            OR (crypto_currency IS NOT NULL AND crypto_currency != '')
                        )
                        AND (
                            status != 'pending' 
                            OR (
                                crypto_tx_hash IS NOT NULL 
                                AND crypto_tx_hash != '' 
                                AND LENGTH(TRIM(crypto_tx_hash)) > 0
                            )
                        )
                    )
                )
                ORDER BY created_at DESC 
                LIMIT ?";
        $stmt = $this->db->query($sql, [$userId, $limit]);
        return $stmt ? $stmt->fetchAll() : [];
    }
    
    // Get all pending crypto funding (for admin) - only those with transaction hash submitted
    public function getPendingCryptoFunding($limit = 100) {
        $sql = "SELECT inf.*, u.full_name, u.email, u.phone 
                FROM investment_funding inf
                LEFT JOIN users u ON inf.user_id = u.id
                WHERE inf.status = 'pending' 
                AND (
                    inf.funding_method LIKE 'crypto_%' 
                    OR inf.funding_method = 'crypto_other'
                    OR (inf.crypto_currency IS NOT NULL AND inf.crypto_currency != '')
                )
                AND inf.crypto_tx_hash IS NOT NULL 
                AND inf.crypto_tx_hash != ''
                AND LENGTH(TRIM(inf.crypto_tx_hash)) > 0
                ORDER BY inf.created_at DESC 
                LIMIT ?";
        $stmt = $this->db->query($sql, [$limit]);
        $result = $stmt ? $stmt->fetchAll() : [];
        
        // Debug logging
        error_log("InvestmentFunding::getPendingCryptoFunding - Found " . count($result) . " records");
        if (count($result) > 0) {
            error_log("Sample record: " . json_encode($result[0]));
        }
        
        return $result;
    }
    
    // Get ALL crypto funding records (for admin history tab)
    // Shows all crypto funding regardless of status or hash
    public function getAllCryptoFunding($limit = 200) {
        $sql = "SELECT inf.*, u.full_name, u.email, u.phone 
                FROM investment_funding inf
                LEFT JOIN users u ON inf.user_id = u.id
                WHERE (
                    inf.funding_method LIKE 'crypto_%'
                    OR inf.funding_method = 'crypto_other'
                    OR (inf.crypto_currency IS NOT NULL AND inf.crypto_currency != '')
                )
                ORDER BY inf.created_at DESC 
                LIMIT ?";
        $stmt = $this->db->query($sql, [$limit]);
        $result = $stmt ? $stmt->fetchAll() : [];
        
        // Debug logging
        error_log("InvestmentFunding::getAllCryptoFunding - Found " . count($result) . " records");
        if (count($result) > 0) {
            error_log("Sample record: " . json_encode($result[0]));
        }
        
        return $result;
    }
    
    // Approve crypto funding (credit investment balance)
    public function approveCryptoFunding($fundingId, $processedBy) {
        $funding = $this->findById($fundingId);
        if (!$funding) {
            return ['success' => false, 'message' => 'Funding request not found'];
        }
        
        if ($funding['status'] !== 'pending') {
            return ['success' => false, 'message' => 'Funding request is not pending'];
        }
        
        if (empty($funding['crypto_tx_hash'])) {
            return ['success' => false, 'message' => 'Transaction hash not provided'];
        }
        
        $this->db->beginTransaction();
        
        try {
            // Credit investment balance
            $userModel = new User();
            $user = $userModel->findById($funding['user_id']);
            $newBalance = (float)($user['investment_balance'] ?? 0) + (float)$funding['amount'];
            $sql = "UPDATE users SET investment_balance = ? WHERE id = ?";
            $this->db->query($sql, [$newBalance, $funding['user_id']]);
            
            // Update funding status
            $this->updateStatus($fundingId, 'completed', $processedBy, 'Crypto payment verified and approved');
            
            // Update transaction status to completed (within same transaction)
            $updateTx = "UPDATE transactions SET status = 'completed' WHERE transaction_ref = ? AND category = 'investment_funding'";
            $this->db->query($updateTx, ['CRYPTO-' . $fundingId]);
            
            $this->db->commit();
            
            // Send notification
            require_once __DIR__ . '/Notification.php';
            require_once __DIR__ . '/../includes/functions.php';
            $notification = new Notification();
            $notification->create(
                $funding['user_id'],
                'Crypto Payment Approved',
                'Your crypto payment of ' . formatInvestmentAmountForUser($funding['amount'], ['currency' => DEFAULT_CURRENCY]) . ' has been verified and your investment account has been credited.',
                'success',
                '/investment'
            );
            
            return ['success' => true, 'message' => 'Funding approved successfully'];
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Approve Crypto Funding Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to approve funding'];
        }
    }
    
    // Reject crypto funding
    public function rejectCryptoFunding($fundingId, $processedBy, $reason) {
        $funding = $this->findById($fundingId);
        if (!$funding) {
            return ['success' => false, 'message' => 'Funding request not found'];
        }
        
        if ($funding['status'] !== 'pending') {
            return ['success' => false, 'message' => 'Funding request is not pending'];
        }
        
        try {
            // Update funding status
            $this->updateStatus($fundingId, 'failed', $processedBy, 'Rejected: ' . $reason);
            
            // Update transaction status to failed
            $updateTx = "UPDATE transactions SET status = 'failed' WHERE transaction_ref = ? AND category = 'investment_funding'";
            $this->db->query($updateTx, ['CRYPTO-' . $fundingId]);
            
            // Send notification
            require_once __DIR__ . '/Notification.php';
            require_once __DIR__ . '/../includes/functions.php';
            $notification = new Notification();
            $notification->create(
                $funding['user_id'],
                'Crypto Payment Rejected',
                'Your crypto payment of ' . formatInvestmentAmountForUser($funding['amount'], ['currency' => DEFAULT_CURRENCY]) . ' was rejected. Reason: ' . $reason,
                'error',
                '/investment'
            );
            
            return ['success' => true, 'message' => 'Funding rejected successfully'];
        } catch (Exception $e) {
            error_log("Reject Crypto Funding Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to reject funding'];
        }
    }
}

