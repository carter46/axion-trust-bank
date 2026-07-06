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

// Validate required fields
$requiredFields = ['full_name', 'email', 'phone', 'date_of_birth', 'address', 'city', 'state', 'country', 'postal_code'];
foreach ($requiredFields as $field) {
    if (empty($input[$field])) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }
}

// Validate email format
if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

try {
    $db = Database::getInstance();
    $userId = $_SESSION['user_id'];
    
    // Check if email is already taken by another user
    $stmt = $db->query("SELECT id FROM users WHERE email = ? AND id != ?", [$input['email'], $userId]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email already in use']);
        exit;
    }
    
    // Update user information
    $sql = "UPDATE users SET 
            full_name = ?,
            email = ?,
            phone = ?,
            date_of_birth = ?,
            address = ?,
            city = ?,
            state = ?,
            country = ?,
            postal_code = ?,
            updated_at = NOW()
            WHERE id = ?";
    
    $db->query($sql, [
        $input['full_name'],
        $input['email'],
        $input['phone'],
        $input['date_of_birth'],
        $input['address'],
        $input['city'],
        $input['state'],
        $input['country'],
        $input['postal_code'],
        $userId
    ]);
    
    // Update session
    $_SESSION['user_name'] = $input['full_name'];
    $_SESSION['user_email'] = $input['email'];
    
    // Log activity
    logActivity($userId, 'PROFILE_UPDATED', 'User updated their profile information');
    
    echo json_encode(['success' => true, 'message' => 'User information updated successfully']);
    exit;
    
} catch (Exception $e) {
    error_log("User info update error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred', 'error' => $e->getMessage()]);
    exit;
}
