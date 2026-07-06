<?php
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

ob_end_clean();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit;
}

$userId = (int)($input['user_id'] ?? 0);
if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'User ID required']);
    exit;
}

try {
    $db = Database::getInstance();
    $stmt = $db->query("SELECT id, email FROM users WHERE id = ? AND role != 'admin'", [$userId]);
    $user = $stmt ? $stmt->fetch() : null;
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }

    $code = generateOTP(10);
    $db->query("UPDATE users SET federal_swift_code = ?, updated_at = NOW() WHERE id = ?", [$code, $userId]);
    logActivity($_SESSION['user_id'], 'ADMIN_REGENERATE_FEDERAL_SWIFT', "Regenerated Federal SWIFT code for user {$user['email']} (ID: {$userId})");

    echo json_encode(['success' => true, 'code' => $code]);
} catch (Exception $e) {
    error_log('admin-regenerate-federal-swift error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to regenerate Federal SWIFT code (run migration if needed).']);
}

