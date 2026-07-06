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

// Get JSON input (allow empty for self-toggle)
$rawInput = file_get_contents('php://input');
$input = [];

if (!empty($rawInput)) {
    $decodedInput = json_decode($rawInput, true);
    
    // Only error if there's input but it's invalid JSON
    if ($decodedInput === null && json_last_error() !== JSON_ERROR_NONE) {
        while (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid JSON input: ' . json_last_error_msg()]);
        exit;
    }
    
    // If we have valid JSON, use it
    if ($decodedInput !== null) {
        $input = $decodedInput;
    }
}

// For empty input, allow self-toggle (default behavior)
$targetUserId = isset($input['user_id']) ? intval($input['user_id']) : 0;
$enabled = isset($input['enabled']) ? (bool)$input['enabled'] : null;

try {
    $db = Database::getInstance();
    $adminUserId = $_SESSION['user_id'];
    
    // Check if 2FA is disabled system-wide
    require_once __DIR__ . '/../includes/system-settings.php';
    $systemSettings = SystemSettings::getInstance();
    $twoFactorDisabled = $systemSettings->is2FADisabled();
    
    // If trying to enable 2FA but it's disabled system-wide, prevent it
    if ($twoFactorDisabled && ($enabled === true || $enabled === null)) {
        // Check current status - if already disabled, just return success
        $currentUser = $db->query("SELECT two_factor_enabled FROM users WHERE id = ?", [$targetUserId ?: $adminUserId]);
        $currentStatus = $currentUser ? $currentUser->fetch() : null;
        
        if ($currentStatus && $currentStatus['two_factor_enabled']) {
            // User has 2FA enabled but system disabled it - disable it
            $targetId = $targetUserId ?: $adminUserId;
            $db->query("UPDATE users SET two_factor_enabled = 0 WHERE id = ?", [$targetId]);
            echo json_encode(['success' => true, 'message' => '2FA has been disabled system-wide', 'enabled' => false]);
            exit;
        } else {
            // Already disabled, just return
            echo json_encode(['success' => false, 'message' => '2FA is disabled system-wide and cannot be enabled', 'enabled' => false]);
            exit;
        }
    }
    
    // Check if admin is trying to toggle another user's 2FA
    if ($targetUserId && $targetUserId !== $adminUserId) {
        // Admin is toggling another user's 2FA - check admin status
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
        
        $userId = $targetUserId;
    } else {
        // User is toggling their own 2FA
        $userId = $adminUserId;
    }
    
    // Get current 2FA status
    $stmt = $db->query("SELECT two_factor_enabled, email FROM users WHERE id = ?", [$userId]);
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
    
    // Determine new status
    if ($enabled !== null) {
        // Explicit status provided (from admin)
        $newStatus = $enabled ? 1 : 0;
    } else {
        // Toggle current status (for user self-toggle)
        $newStatus = $user['two_factor_enabled'] == 1 ? 0 : 1;
    }
    
    // Update 2FA status
    $updateResult = $db->query("UPDATE users SET two_factor_enabled = ?, updated_at = NOW() WHERE id = ?", [$newStatus, $userId]);
    
    if ($updateResult === false) {
        // Clean output before JSON
        while (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Failed to update 2FA status']);
        exit;
    }
    
    // Log activity (only if function exists)
    $action = $newStatus == 1 ? 'enabled' : 'disabled';
    $logMessage = $targetUserId && $targetUserId !== $adminUserId 
        ? "Admin " . ($_SESSION['user_email'] ?? 'unknown') . " $action two-factor authentication for user {$user['email']}"
        : "User $action two-factor authentication";
    
    $logAction = $targetUserId && $targetUserId !== $adminUserId 
        ? 'ADMIN_TOGGLE_2FA' 
        : 'TWO_FACTOR_' . strtoupper($action);
    
    if (function_exists('logActivity')) {
        logActivity($adminUserId, $logAction, $logMessage);
    }
    
    // Ensure no output before JSON
    while (ob_get_level()) {
        ob_end_clean();
    }
    header('Content-Type: application/json');
    
    echo json_encode([
        'success' => true, 
        'message' => "2FA " . ($newStatus == 1 ? 'enabled' : 'disabled') . " successfully",
        'enabled' => $newStatus == 1
    ]);
    exit;
    
} catch (Exception $e) {
    // Make sure no output before this
    if (ob_get_level() > 0) {
        ob_clean();
    }
    
    error_log("2FA toggle error: " . $e->getMessage());
    
    // Ensure we output valid JSON
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
    exit;
}
