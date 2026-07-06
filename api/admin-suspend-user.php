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

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit;
}

$userId = intval($input['user_id'] ?? 0);
$action = trim($input['action'] ?? '');

// Validate
if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'User ID required']);
    exit;
}

if (!in_array($action, ['suspend', 'activate'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid action. Use "suspend" or "activate"']);
    exit;
}

try {
    $db = Database::getInstance();
    
    // Check if user exists and is not an admin
    $sql = "SELECT id, email, status FROM users WHERE id = ? AND role != 'admin'";
    $stmt = $db->query($sql, [$userId]);
    $user = $stmt->fetch();
    
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }
    
    // Determine new status
    $newStatus = ($action === 'suspend') ? 'suspended' : 'active';
    
    // Update user status
    $sql = "UPDATE users SET status = ?, updated_at = NOW() WHERE id = ?";
    $result = $db->query($sql, [$newStatus, $userId]);
    
    if ($result) {
        // Log activity
        logActivity($_SESSION['user_id'], 'USER_STATUS_CHANGED', 
            "Changed user {$user['email']} status to {$newStatus}");
        
        echo json_encode([
            'success' => true,
            'message' => 'User ' . $action . 'd successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update user status'
        ]);
    }
    
} catch (Exception $e) {
    error_log('Suspend User Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while updating user status'
    ]);
}
