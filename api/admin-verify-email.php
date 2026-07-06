<?php
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

ob_end_clean();

header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
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
$adminId = $_SESSION['user_id'];

if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'User ID required']);
    exit;
}

try {
    $db = Database::getInstance();
    
    // Check if user exists and is not an admin
    $stmt = $db->query(
        "SELECT id, email, full_name, email_verified FROM users WHERE id = ? AND role != 'admin'",
        [$userId]
    );
    $user = $stmt ? $stmt->fetch() : null;
    
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }
    
    if ($user['email_verified']) {
        echo json_encode(['success' => false, 'message' => 'Email is already verified']);
        exit;
    }
    
    // Update email_verified status
    $db->query("UPDATE users SET email_verified = 1, updated_at = NOW() WHERE id = ?", [$userId]);
    
    // Log admin action
    $desc = "Manually verified email address for user {$user['email']}";
    $db->query(
        "INSERT INTO admin_logs (admin_id, user_id, action, description, created_at) VALUES (?, ?, 'email_verified', ?, NOW())",
        [$adminId, $userId, $desc]
    );
    
    // Send notification to user
    try {
        require_once __DIR__ . '/../models/Notification.php';
        $notification = new Notification();
        $notification->create(
            $userId,
            'Email Verified',
            'Your email address has been verified by an administrator.',
            'success'
        );
    } catch (Exception $e) {
        error_log("Notification error: " . $e->getMessage());
    }
    
    echo json_encode(['success' => true, 'message' => 'Email verified successfully']);
} catch (Exception $e) {
    error_log('Admin Verify Email Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while verifying email']);
}
