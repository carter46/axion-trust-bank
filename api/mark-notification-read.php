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

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit;
}

$notificationId = intval($input['notification_id'] ?? 0);
$userId = $_SESSION['user_id'];

if (!$notificationId) {
    echo json_encode(['success' => false, 'message' => 'Notification ID required']);
    exit;
}

try {
    $db = Database::getInstance();
    
    // Mark notification as read (only if it belongs to the current user)
    $sql = "UPDATE notifications 
            SET is_read = 1, read_at = NOW() 
            WHERE id = ? AND user_id = ?";
    
    $result = $db->query($sql, [$notificationId, $userId]);
    
    if ($result) {
        // Check if any rows were affected using the PDOStatement's rowCount method
        if ($result->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Notification not found']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to mark notification as read']);
    }
    
} catch (Exception $e) {
    error_log('Mark Notification Read Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to mark notification as read'
    ]);
}
