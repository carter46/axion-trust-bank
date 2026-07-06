<?php
/**
 * Upload Branding Assets (Logo, Favicon)
 * Admin can upload logo and favicon
 */

// Prevent output before JSON
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

ob_end_clean();

header('Content-Type: application/json');

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$uploadType = $_POST['type'] ?? ''; // 'logo' or 'favicon'

if (!in_array($uploadType, ['logo', 'favicon'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid upload type']);
    exit;
}

// Check if file was uploaded
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    exit;
}

$file = $_FILES['file'];
$fileName = $file['name'];
$fileTmpName = $file['tmp_name'];
$fileSize = $file['size'];
$fileError = $file['error'];

// Get file extension
$fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

// Allowed extensions
if ($uploadType === 'logo') {
    $allowed = ['jpg', 'jpeg', 'png', 'svg', 'webp'];
    $maxSize = 2 * 1024 * 1024; // 2MB
    $targetDir = BASE_PATH . '/assets/images/';
    $newFileName = 'bank-logo.' . $fileExt;
} else {
    $allowed = ['ico', 'png', 'jpg', 'jpeg', 'webp'];
    $maxSize = 500 * 1024; // 500KB
    $targetDir = BASE_PATH . '/';
    $newFileName = 'favicon.' . $fileExt;
}

// Validate extension
if (!in_array($fileExt, $allowed)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Allowed: ' . implode(', ', $allowed)]);
    exit;
}

// Validate size
if ($fileSize > $maxSize) {
    echo json_encode(['success' => false, 'message' => 'File too large. Max: ' . ($maxSize / 1024 / 1024) . 'MB']);
    exit;
}

// Create directory if it doesn't exist
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
}

// Move uploaded file
$targetPath = $targetDir . $newFileName;

// Delete old file if exists
if (file_exists($targetPath)) {
    unlink($targetPath);
}

if (move_uploaded_file($fileTmpName, $targetPath)) {
    // Update database
    try {
        $systemSettings = SystemSettings::getInstance();
        
        if ($uploadType === 'logo') {
            $url = SITE_URL . '/assets/images/' . $newFileName . '?v=' . time();
            $systemSettings->update('site_logo_url', $url, $_SESSION['user_id']);
        } else {
            $url = SITE_URL . '/' . $newFileName . '?v=' . time();
            $systemSettings->update('site_favicon_url', $url, $_SESSION['user_id']);
        }
        
        echo json_encode([
            'success' => true,
            'message' => ucfirst($uploadType) . ' uploaded successfully',
            'url' => $url
        ]);
    } catch (Exception $e) {
        error_log("Branding Upload Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error updating database']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Error moving uploaded file']);
}
?>

