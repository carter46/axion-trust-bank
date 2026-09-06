<?php
/**
 * Admin API — reset a non-admin user's password (no email).
 */
error_reporting(E_ALL);
ini_set('display_errors', '0');
ob_start();

try {
    require_once __DIR__ . '/../config/config.php';
    require_once __DIR__ . '/../includes/functions.php';
    require_once __DIR__ . '/../includes/security.php';
    require_once __DIR__ . '/../models/User.php';

    if (ob_get_length() !== false) {
        ob_end_clean();
    }
    ob_start();

    header('Content-Type: application/json; charset=UTF-8');

    if (!isLoggedIn() || ($_SESSION['user_role'] ?? '') !== 'admin') {
        ob_end_clean();
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'Unauthorized',
            'api' => 'admin-reset-user-password',
            'v' => 2,
        ]);
        exit;
    }

    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput ?: '', true);
    if (!is_array($input)) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid JSON input', 'api' => 'admin-reset-user-password', 'v' => 2]);
        exit;
    }

    $userId = (int)($input['user_id'] ?? 0);
    $newPassword = (string)($input['new_password'] ?? '');

    if ($userId <= 0) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'User ID required']);
        exit;
    }

    if ($newPassword === '') {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Password is required']);
        exit;
    }

    if (strlen($newPassword) < 8) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Password must be 8+ characters']);
        exit;
    }

    // Demo users: super admin only (function lives in functions.php — must load first)
    enforceDemoUserAdminAccessForUserId($userId);

    $userModel = new User();
    $user = $userModel->findById($userId);

    if (!$user) {
        ob_end_clean();
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }

    if (($user['role'] ?? '') === 'admin') {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Cannot reset admin passwords from this endpoint']);
        exit;
    }

    $result = $userModel->updatePassword($userId, $newPassword);
    if (!$result) {
        ob_end_clean();
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update password']);
        exit;
    }

    logActivity(
        (int)$_SESSION['user_id'],
        'ADMIN_USER_PASSWORD_RESET',
        "Admin reset password for user: {$user['email']} (ID: {$userId})"
    );

    ob_end_clean();
    echo json_encode([
        'success' => true,
        'message' => 'Password reset successfully',
        'api' => 'admin-reset-user-password',
        'v' => 2,
    ]);
    exit;
} catch (Throwable $e) {
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    error_log('Admin User Password Reset Error: ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(500);
    }
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage(),
        'api' => 'admin-reset-user-password',
        'v' => 2,
        'file' => basename($e->getFile()),
        'line' => $e->getLine(),
    ]);
    exit;
}
