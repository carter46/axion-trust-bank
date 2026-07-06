<?php
class CardTransaction {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        $transactionRef = 'CARD_' . date('Ymd') . strtoupper(substr(uniqid(), -8));
        
        // Get current card balance
        $card = new Card();
        $cardData = $card->findById($data['card_id']);
        $balanceBefore = $cardData['balance'] ?? 0;
        
        $sql = "INSERT INTO card_transactions (
                    card_id, user_id, transaction_type, amount, category, description,
                    balance_before, balance_after, status, reference, payment_method,
                    merchant_name, merchant_category, location, metadata, ip_address
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $balanceAfter = $data['transaction_type'] === 'credit' 
            ? $balanceBefore + $data['amount']
            : $balanceBefore - $data['amount'];
        
        $result = $this->db->query($sql, [
            $data['card_id'],
            $data['user_id'],
            $data['transaction_type'],
            $data['amount'],
            $data['category'] ?? null,
            $data['description'] ?? null,
            $balanceBefore,
            $balanceAfter,
            $data['status'] ?? 'completed',
            $transactionRef,
            $data['payment_method'] ?? null,
            $data['merchant_name'] ?? null,
            $data['merchant_category'] ?? null,
            $data['location'] ?? null,
            isset($data['metadata']) ? json_encode($data['metadata']) : null,
            $_SERVER['REMOTE_ADDR']
        ]);
        
        if ($result) {
            $transactionId = $this->db->lastInsertId();
            
            // Update card balance
            $updateCardSql = "UPDATE cards SET balance = ? WHERE id = ?";
            $this->db->query($updateCardSql, [$balanceAfter, $data['card_id']]);
            
            // Log activity
            logActivity($data['user_id'], 'CARD_TRANSACTION_CREATED', "Card transaction {$transactionRef} created");
            
            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'transaction_ref' => $transactionRef,
                'balance_after' => $balanceAfter
            ];
        }
        
        return ['success' => false, 'message' => 'Failed to create card transaction'];
    }
    
    public function getCardTransactions($cardId, $limit = 50) {
        $sql = "SELECT * FROM card_transactions 
                WHERE card_id = ? 
                ORDER BY created_at DESC 
                LIMIT ?";
        
        $stmt = $this->db->query($sql, [$cardId, $limit]);
        return $stmt->fetchAll();
    }
    
    public function getUserCardTransactions($userId, $limit = 50) {
        $sql = "SELECT ct.*, c.card_number, c.card_name 
                FROM card_transactions ct
                JOIN cards c ON ct.card_id = c.id
                WHERE ct.user_id = ? 
                ORDER BY ct.created_at DESC 
                LIMIT ?";
        
        $stmt = $this->db->query($sql, [$userId, $limit]);
        return $stmt->fetchAll();
    }
    
    public function findById($id) {
        $sql = "SELECT * FROM card_transactions WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }
    
    public function findByReference($ref) {
        $sql = "SELECT * FROM card_transactions WHERE reference = ?";
        $stmt = $this->db->query($sql, [$ref]);
        return $stmt->fetch();
    }
    
    public function getCardBalance($cardId) {
        $sql = "SELECT balance FROM cards WHERE id = ?";
        $stmt = $this->db->query($sql, [$cardId]);
        $result = $stmt->fetch();
        return $result ? $result['balance'] : 0;
    }
}
?>
