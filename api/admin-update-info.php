<?php
// Update admin email and name
error_reporting(0);
ini_set('display_errors', 0);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');
ob_start();

try {
    // Check authentication
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Unauthorized - Not logged in as admin']);
        exit;
    }
    
    // Get input
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);
    
    if (!$input) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
        exit;
    }
    
    $adminId = intval($input['admin_id'] ?? 0);
    $newEmail = trim($input['email'] ?? '');
    $newName = trim($input['full_name'] ?? '');
    
    // Validate
    if (!$adminId) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Admin ID required']);
        exit;
    }
    
    if (empty($newEmail) || !filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Valid email address required']);
        exit;
    }
    
    if (empty($newName)) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Full name required']);
        exit;
    }
    
    // Load required files
    require_once __DIR__ . '/../config/config.php';
    require_once __DIR__ . '/../models/User.php';
    require_once __DIR__ . '/../includes/functions.php';
    
    // Get admin info
    $userModel = new User();
    $admin = $userModel->findById($adminId);
    
    if (!$admin) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Admin not found']);
        exit;
    }
    
    if ($admin['role'] !== 'admin') {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'User is not an admin']);
        exit;
    }
    
    // Allow all admins to update their own info or other admin info
    // Check if email is already in use by another user
    $db = Database::getInstance();
    $checkSql = "SELECT id FROM users WHERE email = ? AND id != ?";
    $checkStmt = $db->query($checkSql, [$newEmail, $adminId]);
    $existing = $checkStmt->fetch();
    
    if ($existing) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Email address is already in use']);
        exit;
    }
    
    // Update admin info
    $updateSql = "UPDATE users SET email = ?, full_name = ?, updated_at = NOW() WHERE id = ?";
    $db->query($updateSql, [$newEmail, $newName, $adminId]);
    
    // Log activity
    logActivity($_SESSION['user_id'], 'ADMIN_INFO_UPDATED', "Updated info for {$admin['email']} to {$newEmail}");
    
    ob_end_clean();
    echo json_encode(['success' => true, 'message' => 'Administrator information updated successfully']);
    exit;
    
} catch (Exception $e) {
    ob_end_clean();
    error_log('Admin Info Update Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Server error occurred',
        'error' => $e->getMessage()
    ]);
    exit;
}

