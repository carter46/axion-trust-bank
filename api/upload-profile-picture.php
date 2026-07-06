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

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Check if it's an admin uploading for another user or user uploading for themselves
$targetUserId = isset($_POST['user_id']) ? intval($_POST['user_id']) : $_SESSION['user_id'];
$isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';

// If admin is uploading for another user, allow it
// If user is uploading for themselves, allow it
// If user is trying to upload for someone else, deny it
if (!$isAdmin && $targetUserId !== $_SESSION['user_id']) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    // Check if file was uploaded
    if (!isset($_FILES['profile_picture']) || $_FILES['profile_picture']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
        exit;
    }

    $file = $_FILES['profile_picture'];
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $fileType = mime_content_type($file['tmp_name']);
    
    if (!in_array($fileType, $allowedTypes)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPEG, PNG, GIF, and WebP images are allowed.']);
        exit;
    }
    
    // Validate file size (max 5MB)
    $maxSize = 5 * 1024 * 1024; // 5MB
    if ($file['size'] > $maxSize) {
        echo json_encode(['success' => false, 'message' => 'File size too large. Maximum size is 5MB.']);
        exit;
    }
    
    // Create uploads directory if it doesn't exist
    $uploadDir = BASE_PATH . '/uploads/profile-pictures/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Generate unique filename
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = 'user_' . $targetUserId . '_' . time() . '.' . $fileExtension;
    $filePath = $uploadDir . $fileName;
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        echo json_encode(['success' => false, 'message' => 'Failed to save file']);
        exit;
    }
    
    // Get user data for logging
    $db = Database::getInstance();
    $sql = "SELECT email, full_name FROM users WHERE id = ?";
    $stmt = $db->query($sql, [$targetUserId]);
    $user = $stmt->fetch();
    
    if (!$user) {
        unlink($filePath); // Delete the uploaded file
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }
    
    // Delete old profile picture if exists
    $oldPictureSql = "SELECT profile_picture FROM users WHERE id = ?";
    $oldPictureStmt = $db->query($oldPictureSql, [$targetUserId]);
    $oldPicture = $oldPictureStmt->fetch();
    
    if ($oldPicture && $oldPicture['profile_picture'] && file_exists(BASE_PATH . $oldPicture['profile_picture'])) {
        unlink(BASE_PATH . $oldPicture['profile_picture']);
    }
    
    // Update database with new profile picture path
    $relativePath = '/uploads/profile-pictures/' . $fileName;
    $updateSql = "UPDATE users SET profile_picture = ?, updated_at = NOW() WHERE id = ?";
    $updateResult = $db->query($updateSql, [$relativePath, $targetUserId]);
    
    if (!$updateResult) {
        unlink($filePath); // Delete the uploaded file
        echo json_encode(['success' => false, 'message' => 'Failed to update database']);
        exit;
    }
    
    // Update session if user uploaded their own picture
    if ($targetUserId === $_SESSION['user_id']) {
        $_SESSION['user_photo'] = $relativePath;
    }
    
    // Log activity
    if ($isAdmin && $targetUserId !== $_SESSION['user_id']) {
        logActivity($_SESSION['user_id'], 'ADMIN_UPLOAD_PROFILE_PICTURE', 
            "Uploaded profile picture for user {$user['email']} (ID: {$targetUserId})");
    } else {
        logActivity($_SESSION['user_id'], 'USER_UPLOAD_PROFILE_PICTURE', 
            "Updated own profile picture");
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Profile picture updated successfully',
        'profile_picture_url' => $relativePath
    ]);
    
} catch (Exception $e) {
    error_log('Profile Picture Upload Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while uploading profile picture'
    ]);
}
