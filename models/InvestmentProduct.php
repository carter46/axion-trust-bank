<?php
class InvestmentProduct {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // Get all active products with filters
    public function getAll($filters = []) {
        $sql = "SELECT * FROM investment_products WHERE 1=1";
        $params = [];
        
        if (!empty($filters['type'])) {
            $sql .= " AND type = ?";
            $params[] = $filters['type'];
        }
        
        if (isset($filters['status'])) {
            if ($filters['status'] !== null && $filters['status'] !== '') {
                $sql .= " AND status = ?";
                $params[] = $filters['status'];
            }
            // If status is null, don't filter (show all) - for admin
        } else {
            // Default to active products only for public view
            $sql .= " AND status = 'active'";
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (title LIKE ? OR short_description LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        // Order by
        $orderBy = $filters['sort'] ?? 'display_order ASC, created_at DESC';
        switch ($orderBy) {
            case 'newest':
                $sql .= " ORDER BY created_at DESC";
                break;
            case 'highest_roi':
                // Would need to parse ROI config, simplified here
                $sql .= " ORDER BY display_order ASC, created_at DESC";
                break;
            case 'lowest_min':
                $sql .= " ORDER BY min_amount ASC";
                break;
            default:
                $sql .= " ORDER BY display_order ASC, created_at DESC";
        }
        
        // Pagination
        if (!empty($filters['limit'])) {
            $sql .= " LIMIT " . intval($filters['limit']);
            if (!empty($filters['offset'])) {
                $sql .= " OFFSET " . intval($filters['offset']);
            }
        }
        
        $stmt = $this->db->query($sql, $params);
        return $stmt ? $stmt->fetchAll() : [];
    }
    
    // Get product by ID
    public function findById($id) {
        $sql = "SELECT * FROM investment_products WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt ? $stmt->fetch() : null;
    }
    
    // Get product by slug
    public function findBySlug($slug) {
        $sql = "SELECT * FROM investment_products WHERE slug = ?";
        $stmt = $this->db->query($sql, [$slug]);
        return $stmt ? $stmt->fetch() : null;
    }
    
    // Create product
    public function create($data) {
        try {
            $sql = "INSERT INTO investment_products (
                title, slug, type, image_url, short_description, full_description,
                status, min_amount, max_amount, min_duration_days, max_duration_days,
                roi_config, payout_type, start_date, end_date, capacity_total,
                per_user_max, risk_level, display_order, created_by_admin_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $roiConfig = is_array($data['roi_config']) ? json_encode($data['roi_config']) : ($data['roi_config'] ?? null);
            
            $this->db->query($sql, [
                $data['title'],
                $data['slug'],
                $data['type'],
                $data['image_url'] ?? null,
                $data['short_description'] ?? null,
                $data['full_description'] ?? null,
                $data['status'] ?? 'draft',
                $data['min_amount'],
                $data['max_amount'] ?? null,
                $data['min_duration_days'],
                $data['max_duration_days'] ?? null,
                $roiConfig,
                $data['payout_type'] ?? 'compound_daily',
                $data['start_date'] ?? null,
                $data['end_date'] ?? null,
                $data['capacity_total'] ?? null,
                $data['per_user_max'] ?? null,
                $data['risk_level'] ?? 'medium',
                $data['display_order'] ?? 0,
                $data['created_by_admin_id'] ?? null
            ]);
            
            return $this->db->lastInsertId();
        } catch (Exception $e) {
            error_log("InvestmentProduct Create Error: " . $e->getMessage());
            return false;
        }
    }
    
    // Update product
    public function update($id, $data) {
        try {
            $sql = "UPDATE investment_products SET 
                title = ?, slug = ?, type = ?, image_url = ?, short_description = ?,
                full_description = ?, status = ?, min_amount = ?, max_amount = ?,
                min_duration_days = ?, max_duration_days = ?, roi_config = ?,
                payout_type = ?, start_date = ?, end_date = ?, capacity_total = ?,
                per_user_max = ?, risk_level = ?, display_order = ?, updated_at = NOW()
                WHERE id = ?";
            
            $roiConfig = is_array($data['roi_config']) ? json_encode($data['roi_config']) : ($data['roi_config'] ?? null);
            
            $this->db->query($sql, [
                $data['title'],
                $data['slug'],
                $data['type'],
                $data['image_url'] ?? null,
                $data['short_description'] ?? null,
                $data['full_description'] ?? null,
                $data['status'] ?? 'draft',
                $data['min_amount'],
                $data['max_amount'] ?? null,
                $data['min_duration_days'],
                $data['max_duration_days'] ?? null,
                $roiConfig,
                $data['payout_type'] ?? 'compound_daily',
                $data['start_date'] ?? null,
                $data['end_date'] ?? null,
                $data['capacity_total'] ?? null,
                $data['per_user_max'] ?? null,
                $data['risk_level'] ?? 'medium',
                $data['display_order'] ?? 0,
                $id
            ]);
            
            return true;
        } catch (Exception $e) {
            error_log("InvestmentProduct Update Error: " . $e->getMessage());
            return false;
        }
    }
    
    // Delete product
    public function delete($id) {
        try {
            $sql = "DELETE FROM investment_products WHERE id = ?";
            $this->db->query($sql, [$id]);
            return true;
        } catch (Exception $e) {
            error_log("InvestmentProduct Delete Error: " . $e->getMessage());
            return false;
        }
    }
    
    // Get total invested amount for a product
    public function getTotalInvested($productId) {
        $sql = "SELECT COALESCE(SUM(amount_principal), 0) as total 
                FROM user_investments 
                WHERE product_id = ? AND status IN ('active', 'matured')";
        $stmt = $this->db->query($sql, [$productId]);
        $result = $stmt ? $stmt->fetch() : null;
        return $result ? (float)$result['total'] : 0;
    }
    
    // Get remaining capacity
    public function getRemainingCapacity($productId) {
        $product = $this->findById($productId);
        if (!$product || !$product['capacity_total']) {
            return null; // No limit
        }
        
        $invested = $this->getTotalInvested($productId);
        return max(0, (float)$product['capacity_total'] - $invested);
    }
    
    // Parse ROI config to get daily percentage
    public function getDailyROI($product) {
        if (empty($product['roi_config'])) {
            return 0;
        }
        
        $config = json_decode($product['roi_config'], true);
        if (!$config) {
            return 0;
        }
        
        if ($config['mode'] === 'fixed_daily') {
            return (float)($config['daily_percent'] ?? 0);
        } elseif ($config['mode'] === 'tiered') {
            // Return first tier's rate (simplified)
            return isset($config['tiers'][0]['daily_percent']) ? (float)$config['tiers'][0]['daily_percent'] : 0;
        } elseif ($config['mode'] === 'annual') {
            // Convert annual to daily
            $annualRate = (float)($config['annual_percent'] ?? 0);
            return $annualRate / 365;
        }
        
        return 0;
    }
    
    // Get annual ROI from config
    public function getAnnualROI($product) {
        $daily = $this->getDailyROI($product);
        return $daily * 365;
    }
}

