<?php
// Prevent any output before JSON
error_reporting(0);
ini_set('display_errors', 0);

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set JSON header FIRST
header('Content-Type: application/json');

// Catch ALL output
ob_start();

try {
    // Check authentication
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
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
    
    $userId = intval($input['user_id'] ?? 0);
    $newPassword = $input['new_password'] ?? '';
    
    // Validate
    if (!$userId) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'User ID required']);
        exit;
    }
    
    if (empty($newPassword)) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Password is required']);
        exit;
    }
    
    if (strlen($newPassword) < 8) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Password must be 8+ characters']);
        exit;
    }
    
    // Load required files
    require_once __DIR__ . '/../config/config.php';
    require_once __DIR__ . '/../includes/security.php';
    require_once __DIR__ . '/../models/User.php';
    require_once __DIR__ . '/../includes/functions.php';
    
    // Get user model
    $userModel = new User();
    $user = $userModel->findById($userId);
    
    if (!$user) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }
    
    if ($user['role'] === 'admin') {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Cannot reset admin passwords from this endpoint']);
        exit;
    }
    
    // Update the password (NO EMAIL SENT - admin action)
    $result = $userModel->updatePassword($userId, $newPassword);
    
    if (!$result) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Failed to update password']);
        exit;
    }
    
    // Log activity
    logActivity($_SESSION['user_id'], 'ADMIN_USER_PASSWORD_RESET', "Admin reset password for user: {$user['email']} (ID: {$userId})");
    
    // Clear any accidental output
    ob_end_clean();
    
    // Success (no email notification for admin actions)
    echo json_encode(['success' => true, 'message' => 'Password reset successfully']);
    exit;
    
} catch (Exception $e) {
    ob_end_clean();
    error_log('Admin User Password Reset Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
    echo json_encode([
        'success' => false,
        'message' => 'Server error occurred',
        'error' => $e->getMessage()
    ]);
    exit;
} catch (Error $e) {
    ob_end_clean();
    error_log('Admin User Password Reset Fatal Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Fatal error occurred',
        'error' => $e->getMessage()
    ]);
    exit;
}

