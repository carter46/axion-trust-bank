<?php
// Prevent output before JSON
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

ob_end_clean();

header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$adminIdToDelete = intval($input['admin_id'] ?? 0);

if (!$adminIdToDelete) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Admin ID is required']);
    exit;
}

// Prevent deleting yourself
if ($adminIdToDelete == $_SESSION['user_id']) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'You cannot delete your own admin account']);
    exit;
}

try {
    $db = Database::getInstance();
    
    // Verify the user to delete is actually an admin
    $sql = "SELECT id, full_name, email, role, is_super_admin FROM users WHERE id = ?";
    $stmt = $db->query($sql, [$adminIdToDelete]);
    $adminToDelete = $stmt->fetch();
    
    if (!$adminToDelete) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Administrator not found']);
        exit;
    }
    
    if ($adminToDelete['role'] !== 'admin') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'User is not an administrator']);
        exit;
    }
    
    // Prevent deleting super admin
    if ($adminToDelete['is_super_admin'] == 1) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Cannot delete Super Administrator. This account can only be removed from the database.']);
        exit;
    }
    
    // Check if this is the last admin
    $sqlCount = "SELECT COUNT(*) as count FROM users WHERE role = 'admin'";
    $stmtCount = $db->query($sqlCount);
    $countResult = $stmtCount->fetch();
    
    if ($countResult['count'] <= 1) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Cannot delete the last administrator. Add another admin first.']);
        exit;
    }
    
    // Delete the admin user
    $deleteSql = "DELETE FROM users WHERE id = ? AND role = 'admin'";
    $result = $db->query($deleteSql, [$adminIdToDelete]);
    
    if ($result) {
        // Log the action
        logActivity($_SESSION['user_id'], 'ADMIN_DELETED', "Deleted administrator: {$adminToDelete['full_name']} ({$adminToDelete['email']})");
        
        echo json_encode([
            'success' => true,
            'message' => 'Administrator deleted successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to delete administrator'
        ]);
    }
    
} catch (Exception $e) {
    error_log('Delete Admin Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while deleting the administrator'
    ]);
}
?>

