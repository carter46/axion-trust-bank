<?php
class Notification {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($userId, $title, $message, $type = 'info', $link = null, $metadata = null) {
        $sql = "INSERT INTO notifications (user_id, title, message, type, link, metadata) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        return $this->db->query($sql, [
            $userId,
            $title,
            $message,
            $type,
            $link,
            $metadata ? json_encode($metadata) : null
        ]);
    }
    
    public function getUserNotifications($userId, $limit = 50, $unreadOnly = false) {
        $sql = "SELECT * FROM notifications WHERE user_id = ?";
        $params = [$userId];
        
        if ($unreadOnly) {
            $sql .= " AND is_read = 0";
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT ?";
        $params[] = $limit;
        
        $stmt = $this->db->query($sql, $params);
        return $stmt ? $stmt->fetchAll() : [];
    }
    
    public function markAsRead($notificationId) {
        $sql = "UPDATE notifications SET is_read = 1 WHERE id = ?";
        return $this->db->query($sql, [$notificationId]);
    }
    
    public function markAllAsRead($userId) {
        $sql = "UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0";
        return $this->db->query($sql, [$userId]);
    }
    
    public function getUnreadCount($userId) {
        $sql = "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0";
        $stmt = $this->db->query($sql, [$userId]);
        $result = $stmt->fetch();
        return $result['count'];
    }
    
    public function delete($notificationId) {
        $sql = "DELETE FROM notifications WHERE id = ?";
        return $this->db->query($sql, [$notificationId]);
    }
}
