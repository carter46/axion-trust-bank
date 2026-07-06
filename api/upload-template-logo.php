<?php
/**
 * Upload Template Logo
 * Admin can upload logo for email simulation templates
 */

error_reporting(0);
ini_set('display_errors', 0);
ob_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

ob_end_clean();

header('Content-Type: application/json');

// Check if user is admin
requireAdmin();

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Check if file was uploaded
if (!isset($_FILES['logo']) || $_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    exit;
}

$file = $_FILES['logo'];
$fileName = $file['name'];
$fileTmpName = $file['tmp_name'];
$fileSize = $file['size'];

// Get file extension
$fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

// Allowed extensions
$allowed = ['jpg', 'jpeg', 'png', 'svg', 'webp'];
$maxSize = 2 * 1024 * 1024; // 2MB
$targetDir = BASE_PATH . '/assets/images/template-logos/';

// Validate extension
if (!in_array($fileExt, $allowed)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Allowed: ' . implode(', ', $allowed)]);
    exit;
}

// Validate size
if ($fileSize > $maxSize) {
    echo json_encode(['success' => false, 'message' => 'File too large. Max: 2MB']);
    exit;
}

// Create directory if it doesn't exist
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
}

// Generate unique filename
$newFileName = 'template-logo-' . time() . '-' . rand(1000, 9999) . '.' . $fileExt;
$targetPath = $targetDir . $newFileName;

// Move uploaded file
if (move_uploaded_file($fileTmpName, $targetPath)) {
    // Generate URL
    $logoUrl = SITE_URL . '/assets/images/template-logos/' . $newFileName;
    
    echo json_encode([
        'success' => true,
        'message' => 'Logo uploaded successfully',
        'logo_url' => $logoUrl
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to upload file']);
}


