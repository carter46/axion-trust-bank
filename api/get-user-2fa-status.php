<?php
// Prevent all output before JSON
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Start output buffering and clean any previous output
while (ob_get_level()) {
    ob_end_clean();
}
ob_start();

// Suppress any warnings/notices
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    return true; // Suppress all errors
}, E_ALL);

require_once '../config/config.php';
require_once '../includes/functions.php';

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clean any output that might have been generated
ob_end_clean();

// Set headers - must be before any output
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');
header('X-Content-Type-Options: nosniff');

// Check if user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    while (ob_get_level()) {
        ob_end_clean();
    }
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized - Please log in']);
    exit;
}

// Get user ID from query parameter
$targetUserId = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($targetUserId <= 0) {
    while (ob_get_level()) {
        ob_end_clean();
    }
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
    exit;
}

try {
    $db = Database::getInstance();
    $adminUserId = $_SESSION['user_id'];
    
    // Check if admin is viewing another user's 2FA status
    if ($targetUserId !== $adminUserId) {
        // Admin is viewing another user's 2FA status - check admin status
        $adminCheck = $db->query("SELECT role FROM users WHERE id = ?", [$adminUserId]);
        $adminUser = $adminCheck->fetch();
        if (!$adminUser || $adminUser['role'] !== 'admin') {
            // Clean output before JSON
            while (ob_get_level()) {
                ob_end_clean();
            }
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Admin access required']);
            exit;
        }
    }
    
    // Get current 2FA status
    $stmt = $db->query("SELECT two_factor_enabled FROM users WHERE id = ?", [$targetUserId]);
    $user = $stmt->fetch();
    
    if (!$user) {
        // Clean output before JSON
        while (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }
    
    // Ensure no output before JSON
    while (ob_get_level()) {
        ob_end_clean();
    }
    header('Content-Type: application/json');
    
    echo json_encode([
        'success' => true,
        'enabled' => $user['two_factor_enabled'] == 1
    ]);
    exit;
    
} catch (Exception $e) {
    // Make sure no output before this
    if (ob_get_level() > 0) {
        ob_clean();
    }
    
    error_log("Get 2FA status error: " . $e->getMessage());
    
    // Ensure we output valid JSON
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
    exit;
}

