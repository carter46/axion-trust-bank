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

// Check if file was uploaded
if (!isset($_FILES['profile_photo']) || $_FILES['profile_photo']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded']);
    exit;
}

$file = $_FILES['profile_photo'];

// Validate file type
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
if (!in_array($file['type'], $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed']);
    exit;
}

// Validate file size (2MB max)
if ($file['size'] > 2 * 1024 * 1024) {
    echo json_encode(['success' => false, 'message' => 'File size must be less than 2MB']);
    exit;
}

try {
    $userId = $_SESSION['user_id'];
    
    // Create upload directory if it doesn't exist
    $uploadDir = BASE_PATH . '/uploads/profile-photos/';
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            error_log("Failed to create upload directory: " . $uploadDir);
            echo json_encode(['success' => false, 'message' => 'Failed to create upload directory']);
            exit;
        }
    }
    
    // Check if directory is writable
    if (!is_writable($uploadDir)) {
        error_log("Upload directory is not writable: " . $uploadDir);
        echo json_encode(['success' => false, 'message' => 'Upload directory is not writable']);
        exit;
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    if (empty($extension)) {
        $extension = 'jpg'; // Default to jpg if no extension
    }
    $filename = 'user_' . $userId . '_' . time() . '.' . strtolower($extension);
    $filepath = $uploadDir . $filename;
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        $error = error_get_last();
        error_log("Failed to move uploaded file: " . print_r($error, true));
        echo json_encode(['success' => false, 'message' => 'Failed to save file to server']);
        exit;
    }
    
    // Update database
    $photoUrl = SITE_URL . '/uploads/profile-photos/' . $filename;
    $db = Database::getInstance();
    $result = $db->query("UPDATE users SET profile_photo = ?, updated_at = NOW() WHERE id = ?", [$photoUrl, $userId]);
    
    if (!$result) {
        error_log("Failed to update database with photo URL for user: " . $userId);
        echo json_encode(['success' => false, 'message' => 'Failed to update database']);
        exit;
    }
    
    // Update session
    $_SESSION['user_photo'] = $photoUrl;
    
    // Log activity
    logActivity($userId, 'PROFILE_PHOTO_UPDATED', 'User updated their profile photo');
    
    echo json_encode([
        'success' => true,
        'message' => 'Profile photo updated successfully',
        'photo_url' => $photoUrl
    ]);
    
} catch (Exception $e) {
    error_log("Profile photo upload error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}

