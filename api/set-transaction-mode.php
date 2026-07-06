<?php
// Prevent any output before JSON
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Clear any accidental output
ob_end_clean();

header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit;
}

$userId = intval($input['user_id'] ?? 0);
$mode = trim($input['mode'] ?? '');

// Validate
if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'User ID required']);
    exit;
}

if (!in_array($mode, ['normal', 'force_success', 'force_pending', 'force_failed'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid mode. Use "normal", "force_success", "force_pending", or "force_failed"']);
    exit;
}

try {
    $db = Database::getInstance();
    
    // Check if user exists and is not an admin
    $sql = "SELECT id, email, full_name FROM users WHERE id = ? AND role != 'admin'";
    $stmt = $db->query($sql, [$userId]);
    $user = $stmt->fetch();
    
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }
    
    // Store transaction processing override in user metadata
    // This is for testing/debugging purposes only
    $sql = "UPDATE users SET transaction_override = ?, updated_at = NOW() WHERE id = ?";
    $result = $db->query($sql, [$mode, $userId]);
    
    if ($result) {
        // Log activity
        logActivity($_SESSION['user_id'], 'ADMIN_SET_TRANSACTION_MODE', 
            "Set transaction mode to '{$mode}' for user {$user['email']} (ID: {$userId})");
        
        $modeNames = [
            'normal' => 'Normal Processing',
            'force_success' => 'Force All Success',
            'force_pending' => 'Force All Pending',
            'force_failed' => 'Force All Failed'
        ];
        
        echo json_encode([
            'success' => true,
            'message' => "Transaction mode set to: {$modeNames[$mode]}"
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to set transaction mode'
        ]);
    }
    
} catch (Exception $e) {
    error_log('Set Transaction Mode Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while setting transaction mode'
    ]);
}
