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
$password = $input['password'] ?? '';
$pin = $input['pin'] ?? '';
$onboarding = !empty($input['onboarding']) || !empty($_SESSION['security_onboarding']);

// Validate input
if (empty($pin)) {
    echo json_encode(['success' => false, 'message' => 'PIN is required']);
    exit;
}

// Validate PIN format (4 digits)
if (!preg_match('/^\d{4}$/', $pin)) {
    echo json_encode(['success' => false, 'message' => 'PIN must be exactly 4 digits']);
    exit;
}

try {
    $db = Database::getInstance();
    $userId = $_SESSION['user_id'];
    
    // Verify current password (skip on first-time Transfer PIN setup — no existing PIN yet)
    $stmt = $db->query("SELECT password_hash, transfer_pin FROM users WHERE id = ?", [$userId]);
    $user = $stmt ? $stmt->fetch() : null;

    $isFirstTimeSetup = empty($user['transfer_pin'] ?? '');
    $skipPassword = $isFirstTimeSetup || ($onboarding && $isFirstTimeSetup);
    if (!$skipPassword) {
        if (empty($password) || !$user || !password_verify($password, $user['password_hash'])) {
            echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
            exit;
        }
    }
    
    // Hash and save the new PIN
    $pinHash = password_hash($pin, PASSWORD_BCRYPT);
    
    $db->query("UPDATE users SET transfer_pin = ?, updated_at = NOW() WHERE id = ?", [$pinHash, $userId]);
    
    // Log activity
    logActivity($userId, 'TRANSFER_PIN_UPDATED', 'User updated their transfer PIN');
    
    // Send PIN changed notification email (user self-service action)
    try {
        $userModel = new User();
        $user = $userModel->findById($userId);
        if ($user) {
            require_once '../includes/email-template.php';
            require_once '../includes/system-settings.php';
            $emailTemplate = new EmailTemplate();
            $pinEmail = $emailTemplate->pinChangedEmail($user['full_name'], 'transfer');
            sendEmail($user['email'], 'Transfer PIN Updated - ' . getSiteName(), $pinEmail);
        }
    } catch (Exception $e) {
        error_log("PIN change email error: " . $e->getMessage());
    }
    
    echo json_encode(['success' => true, 'message' => 'Transfer PIN updated successfully']);
    
} catch (Exception $e) {
    error_log("Transfer PIN update error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}

