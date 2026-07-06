<?php
// Prevent output before JSON
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

require_once '../config/config.php';
require_once '../includes/functions.php';

ob_end_clean();

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get input
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($input['email_credit_alert']) || !isset($input['email_debit_alert']) || 
    !isset($input['email_login_alert']) || !isset($input['sms_enabled'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

try {
    $db = Database::getInstance();
    $userId = $_SESSION['user_id'];
    
    // Prepare preferences array
    $preferences = [
        'email_credit_alert' => (bool) $input['email_credit_alert'],
        'email_debit_alert' => (bool) $input['email_debit_alert'],
        'email_login_alert' => (bool) $input['email_login_alert'],
        'sms_enabled' => (bool) $input['sms_enabled']
    ];
    
    // Convert to JSON
    $preferencesJson = json_encode($preferences);
    
    // Update database
    $db->query(
        "UPDATE users SET notification_preferences = ?, updated_at = NOW() WHERE id = ?",
        [$preferencesJson, $userId]
    );
    
    // Log activity
    logActivity($userId, 'NOTIFICATION_PREFERENCES_UPDATED', 'User updated notification preferences');
    
    echo json_encode([
        'success' => true,
        'message' => 'Notification preferences updated successfully'
    ]);
    exit;
    
} catch (Exception $e) {
    error_log("Notification preferences update error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred', 'error' => $e->getMessage()]);
    exit;
}
