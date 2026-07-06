<?php
// Prevent any output before JSON
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Clear any accidental output
ob_end_clean();

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $db = Database::getInstance();
    $userId = $_SESSION['user_id'];
    
    // Get recent notifications (last 5)
    $sql = "SELECT id, title, message, type, is_read, link, created_at 
            FROM notifications 
            WHERE user_id = ? 
            ORDER BY created_at DESC 
            LIMIT 5";
    
    $stmt = $db->query($sql, [$userId]);
    $notifications = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'notifications' => $notifications
    ]);
    
} catch (Exception $e) {
    error_log('Get Notifications Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to load notifications'
    ]);
}
