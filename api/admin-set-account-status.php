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

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit;
}

$userId = intval($input['user_id'] ?? 0);
$status = trim($input['status'] ?? '');
$reason = trim($input['reason'] ?? '');

if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'User ID required']);
    exit;
}

enforceDemoUserAdminAccessForUserId($userId);

if (!in_array($status, ['active', 'pending', 'suspended', 'blocked', 'hold'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid status. Must be: active, pending, suspended, blocked, or hold']);
    exit;
}

try {
    $db = Database::getInstance();
    
    // Check if user exists and is not an admin
    $sql = "SELECT id, email, full_name, status FROM users WHERE id = ? AND role != 'admin'";
    $stmt = $db->query($sql, [$userId]);
    $user = $stmt->fetch();
    
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }
    
    // Update user status
    $sql = "UPDATE users SET status = ?, updated_at = NOW() WHERE id = ?";
    $result = $db->query($sql, [$status, $userId]);
    
    if ($result) {
        // Log admin action
        $adminId = $_SESSION['user_id'];
        $logDescription = "Changed user status from '{$user['status']}' to '{$status}'" . ($reason ? " - Reason: $reason" : "");
        
        $sql = "INSERT INTO admin_logs (admin_id, user_id, action, description, created_at) VALUES (?, ?, 'status_change', ?, NOW())";
        $db->query($sql, [$adminId, $userId, $logDescription]);
        
        echo json_encode([
            'success' => true,
            'message' => 'User status updated successfully',
            'new_status' => $status
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update user status']);
    }
    
} catch (Exception $e) {
    error_log('Admin Set Account Status Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while updating user status']);
}