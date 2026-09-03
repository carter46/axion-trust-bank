<?php
// ABSOLUTE minimal version - no includes that could cause issues
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
        echo json_encode(['success' => false, 'message' => 'Unauthorized - Not logged in as admin']);
        exit;
    }
    
    // Get input
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);
    
    if (!$input) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Invalid JSON input', 'raw' => $rawInput]);
        exit;
    }
    
    $adminId = intval($input['admin_id'] ?? 0);
    $newPassword = $input['new_password'] ?? '';
    
    // Validate
    if (!$adminId) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Admin ID required']);
        exit;
    }
    
    if (strlen($newPassword) < 8) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Password must be 8+ characters']);
        exit;
    }
    
    // NOW load the heavy files
    require_once __DIR__ . '/../config/config.php';
    require_once __DIR__ . '/../includes/security.php';
    require_once __DIR__ . '/../models/User.php';
    require_once __DIR__ . '/../includes/functions.php';
    
    // Update password
    $userModel = new User();
    $admin = $userModel->findById($adminId);
    
    if (!$admin) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Account not found']);
        exit;
    }

    if (!canManageManagedAccount($admin, $_SESSION['user_id'])) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'You do not have permission to edit this account']);
        exit;
    }

    $isDemo = isDemoUserRecord($admin);
    if (!$isDemo && ($admin['role'] ?? '') !== 'admin') {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'User is not a managed admin account']);
        exit;
    }
    $userModel->updatePassword($adminId, $newPassword);

    seventhTradeHubMaybeSyncOwnedAdminCredentials($admin, null, $newPassword);
    
    // Log
    logActivity($_SESSION['user_id'], 'ADMIN_PASSWORD_UPDATED', "Updated password for {$admin['email']}");
    
    // Clear any accidental output
    ob_end_clean();
    
    // Success
    echo json_encode(['success' => true, 'message' => 'Password updated successfully']);
    exit;
    
} catch (Exception $e) {
    // Clear output buffer
    ob_end_clean();
    
    // Log to file
    error_log('Admin Password Update Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
    
    // Return error
    echo json_encode([
        'success' => false,
        'message' => 'Server error occurred',
        'error' => $e->getMessage(),
        'file' => basename($e->getFile()),
        'line' => $e->getLine()
    ]);
    exit;
} catch (Error $e) {
    // Catch PHP 7+ errors
    ob_end_clean();
    error_log('Admin Password Update Fatal Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Fatal error occurred',
        'error' => $e->getMessage()
    ]);
    exit;
}
