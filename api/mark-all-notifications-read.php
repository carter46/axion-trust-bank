<?php
// Prevent any output before JSON
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

$userId = $_SESSION['user_id'];

try {
    $db = Database::getInstance();
    
    // Mark all notifications as read for the current user
    $sql = "UPDATE notifications 
            SET is_read = 1, read_at = NOW() 
            WHERE user_id = ? AND is_read = 0";
    
    $result = $db->query($sql, [$userId]);
    
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to mark notifications as read']);
    }
    
} catch (Exception $e) {
    error_log('Mark All Notifications Read Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to mark all notifications as read'
    ]);
}
?>

