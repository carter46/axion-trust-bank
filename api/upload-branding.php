<?php
/**
 * Upload Branding Assets (Logo, Favicon)
 * Files are stored under uploads/branding/ so git deploys do not overwrite them.
 */

@ini_set('display_errors', 0);
@error_reporting(0);
ob_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

ob_end_clean();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$uploadType = $_POST['type'] ?? '';

if (!in_array($uploadType, ['logo', 'favicon'], true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid upload type']);
    exit;
}

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    exit;
}

$file = $_FILES['file'];
$fileName = $file['name'];
$fileTmpName = $file['tmp_name'];
$fileSize = $file['size'];
$fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

if ($uploadType === 'logo') {
    $allowed = ['jpg', 'jpeg', 'png', 'svg', 'webp'];
    $maxSize = 2 * 1024 * 1024;
    $baseName = 'site-logo';
} else {
    $allowed = ['ico', 'png', 'jpg', 'jpeg', 'webp', 'svg'];
    $maxSize = 500 * 1024;
    $baseName = 'favicon';
}

if (!in_array($fileExt, $allowed, true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Allowed: ' . implode(', ', $allowed)]);
    exit;
}

if ($fileSize > $maxSize) {
    echo json_encode(['success' => false, 'message' => 'File too large. Max: ' . round($maxSize / 1024 / 1024, 2) . 'MB']);
    exit;
}

$targetDir = BASE_PATH . '/uploads/branding/';
if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true)) {
    echo json_encode(['success' => false, 'message' => 'Could not create branding upload directory']);
    exit;
}

$newFileName = $baseName . '.' . $fileExt;
$targetPath = $targetDir . $newFileName;

foreach (glob($targetDir . $baseName . '.*') ?: [] as $oldFile) {
    if (is_file($oldFile) && realpath($oldFile) !== realpath($targetPath)) {
        @unlink($oldFile);
    }
}

if (!move_uploaded_file($fileTmpName, $targetPath)) {
    echo json_encode(['success' => false, 'message' => 'Error moving uploaded file']);
    exit;
}

$url = SITE_URL . '/uploads/branding/' . $newFileName . '?v=' . filemtime($targetPath);

try {
    $systemSettings = SystemSettings::getInstance();

    if ($uploadType === 'logo') {
        $systemSettings->update('site_logo_url', $url, $_SESSION['user_id']);
        foreach (glob(BASE_PATH . '/assets/images/bank-logo.*') ?: [] as $legacyLogo) {
            @unlink($legacyLogo);
        }
    } else {
        $systemSettings->update('site_favicon_url', $url, $_SESSION['user_id']);
        foreach (['ico', 'png', 'jpg', 'jpeg', 'webp', 'svg'] as $ext) {
            $legacyFavicon = BASE_PATH . '/favicon.' . $ext;
            if (is_file($legacyFavicon)) {
                @unlink($legacyFavicon);
            }
        }
    }

    echo json_encode([
        'success' => true,
        'message' => ucfirst($uploadType) . ' uploaded successfully',
        'url' => $url,
    ]);
} catch (Exception $e) {
    error_log('Branding Upload Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error updating database']);
}
