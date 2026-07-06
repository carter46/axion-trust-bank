<?php
// Prevent output before JSON
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

require_once '../config/config.php';
require_once '../includes/functions.php';
require_once '../models/User.php';

ob_end_clean();

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get input
$input = json_decode(file_get_contents('php://input'), true);
$currentPassword = $input['currentPassword'] ?? '';
$newPassword = $input['newPassword'] ?? '';

// Validate input
if (empty($currentPassword) || empty($newPassword)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

// Validate new password strength
if (strlen($newPassword) < 8) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters']);
    exit;
}

try {
    $db = Database::getInstance();
    $userId = $_SESSION['user_id'];
    
    // Verify current password
    $stmt = $db->query("SELECT password_hash FROM users WHERE id = ?", [$userId]);
    $user = $stmt->fetch();
    
    if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
        echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
        exit;
    }
    
    // Hash and save the new password
    $newPasswordHash = password_hash($newPassword, PASSWORD_BCRYPT);
    
    $db->query("UPDATE users SET password_hash = ?, updated_at = NOW() WHERE id = ?", [$newPasswordHash, $userId]);
    
    // Log activity
    logActivity($userId, 'PASSWORD_CHANGED', 'User changed their password');
    
    // Send password changed notification email (user self-service action)
    try {
        $userModel = new User();
        $user = $userModel->findById($userId);
        if ($user) {
            require_once '../includes/email-template.php';
            require_once '../includes/system-settings.php';
            $emailTemplate = new EmailTemplate();
            $changedEmail = $emailTemplate->passwordChangedEmail($user['full_name']);
            sendEmail($user['email'], 'Password Changed - ' . getSiteName(), $changedEmail);
        }
    } catch (Exception $e) {
        error_log("Password change email error: " . $e->getMessage());
    }
    
    echo json_encode(['success' => true, 'message' => 'Password changed successfully']);
    
} catch (Exception $e) {
    error_log("Password change error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred', 'error' => $e->getMessage()]);
}
