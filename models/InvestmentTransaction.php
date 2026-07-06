<?php
class InvestmentTransaction {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // Create transaction record
    public function create($data) {
        try {
            $sql = "INSERT INTO investment_transactions (
                user_investment_id, user_id, type, amount,
                balance_before, balance_after, reference, description
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $this->db->query($sql, [
                $data['user_investment_id'] ?? null,
                $data['user_id'],
                $data['type'],
                $data['amount'],
                $data['balance_before'] ?? null,
                $data['balance_after'] ?? null,
                $data['reference'] ?? null,
                $data['description'] ?? null
            ]);
            
            return $this->db->lastInsertId();
        } catch (Exception $e) {
            error_log("InvestmentTransaction Create Error: " . $e->getMessage());
            return false;
        }
    }
    
    // Get transactions for an investment
    public function getByInvestment($investmentId) {
        $sql = "SELECT * FROM investment_transactions 
                WHERE user_investment_id = ? 
                ORDER BY created_at DESC";
        $stmt = $this->db->query($sql, [$investmentId]);
        return $stmt ? $stmt->fetchAll() : [];
    }
    
    // Get transactions for a user
    public function getByUser($userId, $limit = 50) {
        $sql = "SELECT it.*, ui.product_id, ip.title as product_title
                FROM investment_transactions it
                LEFT JOIN user_investments ui ON it.user_investment_id = ui.id
                LEFT JOIN investment_products ip ON ui.product_id = ip.id
                WHERE it.user_id = ? 
                ORDER BY it.created_at DESC
                LIMIT ?";
        $stmt = $this->db->query($sql, [$userId, $limit]);
        return $stmt ? $stmt->fetchAll() : [];
    }
}

