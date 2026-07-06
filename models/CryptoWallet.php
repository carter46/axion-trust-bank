<?php
require_once __DIR__ . '/../config/database.php';

class CryptoWallet {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // Create wallet
    public function create($data) {
        $sql = "INSERT INTO crypto_wallets (
                    crypto_type, wallet_address, network, label, is_active, created_by
                ) VALUES (?, ?, ?, ?, ?, ?)";
        
        $result = $this->db->query($sql, [
            $data['crypto_type'],
            $data['wallet_address'],
            $data['network'] ?? null,
            $data['label'] ?? null,
            $data['is_active'] ?? 1,
            $data['created_by'] ?? null
        ]);
        
        return $result ? $this->db->lastInsertId() : false;
    }
    
    // Update wallet
    public function update($id, $data) {
        $sql = "UPDATE crypto_wallets SET 
                crypto_type = ?,
                wallet_address = ?,
                network = ?,
                label = ?,
                is_active = ?
                WHERE id = ?";
        
        return $this->db->query($sql, [
            $data['crypto_type'],
            $data['wallet_address'],
            $data['network'] ?? null,
            $data['label'] ?? null,
            $data['is_active'] ?? 1,
            $id
        ]);
    }
    
    // Find by ID
    public function findById($id) {
        $sql = "SELECT * FROM crypto_wallets WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt ? $stmt->fetch() : null;
    }
    
    // Get active wallet by type
    public function getActiveWallet($cryptoType) {
        $sql = "SELECT * FROM crypto_wallets 
                WHERE crypto_type = ? AND is_active = 1 
                ORDER BY created_at DESC 
                LIMIT 1";
        $stmt = $this->db->query($sql, [$cryptoType]);
        return $stmt ? $stmt->fetch() : null;
    }
    
    // Get all wallets
    public function getAll($activeOnly = false) {
        $sql = "SELECT * FROM crypto_wallets";
        if ($activeOnly) {
            $sql .= " WHERE is_active = 1";
        }
        $sql .= " ORDER BY crypto_type, created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt ? $stmt->fetchAll() : [];
    }
    
    // Delete wallet
    public function delete($id) {
        $sql = "DELETE FROM crypto_wallets WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }
}

