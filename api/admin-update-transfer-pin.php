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
$newPin = trim($input['new_pin'] ?? '');

if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'User ID required']);
    exit;
}

if (!preg_match('/^\d{4}$/', $newPin)) {
    echo json_encode(['success' => false, 'message' => 'Transfer PIN must be exactly 4 digits']);
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
    
    // Hash the PIN
    $hashedPin = Security::hashPassword($newPin);
    
    // Update transfer PIN
    $sql = "UPDATE users SET 
            transfer_pin = ?,
            updated_at = NOW()
            WHERE id = ?";
    
    $result = $db->query($sql, [$hashedPin, $userId]);
    
    if ($result) {
        // Log activity
        logActivity($_SESSION['user_id'], 'ADMIN_UPDATE_TRANSFER_PIN', 
            "Updated transfer PIN for user {$user['email']} (ID: {$userId})");
        
        echo json_encode([
            'success' => true,
            'message' => 'Transfer PIN updated successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update transfer PIN'
        ]);
    }
    
} catch (Exception $e) {
    error_log('Admin Update Transfer PIN Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while updating transfer PIN'
    ]);
}
