<?php
/**
 * Delete Version Package API
 * Deletes a version record and its associated package file
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Check if POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get input
$input = json_decode(file_get_contents('php://input'), true);
$versionId = $input['version_id'] ?? null;
$version = $input['version'] ?? null;

if (!$versionId && !$version) {
    echo json_encode(['success' => false, 'message' => 'Version ID or version number required']);
    exit;
}

try {
    $db = Database::getInstance();
    
    // Get version info
    if ($versionId) {
        $sql = "SELECT * FROM system_versions WHERE id = ?";
        $stmt = $db->query($sql, [$versionId]);
    } else {
        $sql = "SELECT * FROM system_versions WHERE version = ?";
        $stmt = $db->query($sql, [$version]);
    }
    
    $versionData = $stmt->fetch();
    
    if (!$versionData) {
        echo json_encode(['success' => false, 'message' => 'Version not found']);
        exit;
    }
    
    $versionNumber = $versionData['version'];
    $packageDir = BASE_PATH . '/packages';
    
    // Find and delete package file(s)
    $packageFiles = glob($packageDir . '/update-v' . $versionNumber . '-*.zip');
    $deletedFiles = [];
    $errors = [];
    
    foreach ($packageFiles as $packageFile) {
        if (file_exists($packageFile)) {
            if (@unlink($packageFile)) {
                $deletedFiles[] = basename($packageFile);
            } else {
                $errors[] = 'Failed to delete file: ' . basename($packageFile);
            }
        }
    }
    
    // Delete version record from database
    $sql = "DELETE FROM system_versions WHERE id = ?";
    $result = $db->query($sql, [$versionData['id']]);
    
    if (!$result) {
        $errors[] = 'Failed to delete version record from database';
    }
    
    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'message' => 'Some errors occurred: ' . implode(', ', $errors)
        ]);
        exit;
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Version deleted successfully',
        'version' => $versionNumber,
        'deleted_files' => $deletedFiles
    ]);
    
} catch (Exception $e) {
    error_log('Delete version error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to delete version: ' . $e->getMessage()
    ]);
}

