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
$enabled = (int)($input['enabled'] ?? 0) ? 1 : 0;

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

    $db->query("UPDATE users SET transfer_otp_required = ?, updated_at = NOW() WHERE id = ?", [$enabled, $userId]);

    logActivity($_SESSION['user_id'], 'ADMIN_SET_TRANSFER_OTP', "Set transfer_otp_required={$enabled} for user {$user['email']} (ID: {$userId})");

    echo json_encode([
        'success' => true,
        'message' => $enabled ? 'Transfer OTP enabled for this user' : 'Transfer OTP disabled for this user'
    ]);
} catch (Exception $e) {
    error_log('admin-set-transfer-otp error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to update transfer OTP setting (run migration if needed).']);
}

