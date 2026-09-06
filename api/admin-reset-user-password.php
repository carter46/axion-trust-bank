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

    while (ob_get_level() > 1) {
        ob_end_clean();
    }

    header('Content-Type: application/json; charset=UTF-8');

    $actingAdminId = function_exists('getActingAdminId') ? getActingAdminId() : 0;
    $sessionRole = (string)($_SESSION['user_role'] ?? '');
    $loggedIn = function_exists('isLoggedIn') && isLoggedIn();

    if ($actingAdminId <= 0) {
        $reason = !$loggedIn
            ? 'No login session on this request (cookie not sent, or SITE_URL pointed at another domain)'
            : ('Session role is "' . ($sessionRole !== '' ? $sessionRole : 'empty') . '" — not an admin. If you used Login As, switch back or retry after this deploy.');
        if (function_exists('runtimeLog')) {
            runtimeLog('admin-reset-user-password', 'Unauthorized: ' . $reason, [
                'session_user_id' => $_SESSION['user_id'] ?? null,
                'session_role' => $sessionRole,
                'impersonating' => !empty($_SESSION['admin_impersonating']),
                'original_admin_id' => $_SESSION['admin_original_id'] ?? null,
            ]);
        }
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => $reason,
            'api' => 'admin-reset-user-password',
            'v' => 3,
        ]);
        exit;
    }

    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput ?: '', true);
    if (!is_array($input)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid JSON input', 'api' => 'admin-reset-user-password', 'v' => 3]);
        exit;
    }

    $userId = (int)($input['user_id'] ?? 0);
    $newPassword = (string)($input['new_password'] ?? '');

    if ($userId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'User ID required', 'v' => 3]);
        exit;
    }

    if ($newPassword === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Password is required', 'v' => 3]);
        exit;
    }

    if (strlen($newPassword) < 8) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Password must be 8+ characters', 'v' => 3]);
        exit;
    }

    enforceDemoUserAdminAccessForUserId($userId);

    $userModel = new User();
    $user = $userModel->findById($userId);

    if (!$user) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'User not found', 'v' => 3]);
        exit;
    }

    if (($user['role'] ?? '') === 'admin') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Cannot reset admin passwords from this endpoint', 'v' => 3]);
        exit;
    }

    $result = $userModel->updatePassword($userId, $newPassword);
    if (!$result) {
        if (function_exists('runtimeLog')) {
            runtimeLog('admin-reset-user-password', 'DB updatePassword returned false', ['user_id' => $userId]);
        }
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update password', 'v' => 3]);
        exit;
    }

    logActivity(
        $actingAdminId,
        'ADMIN_USER_PASSWORD_RESET',
        "Admin reset password for user: {$user['email']} (ID: {$userId})"
    );

    if (function_exists('runtimeLog')) {
        runtimeLog('admin-reset-user-password', 'Password reset OK for user ' . $userId, [
            'acting_admin_id' => $actingAdminId,
        ]);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Password reset successfully',
        'api' => 'admin-reset-user-password',
        'v' => 3,
    ]);
    exit;
} catch (Throwable $e) {
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    if (function_exists('runtimeLog')) {
        runtimeLog('admin-reset-user-password', $e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);
    }
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(500);
    }
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage(),
        'api' => 'admin-reset-user-password',
        'v' => 3,
        'file' => basename($e->getFile()),
        'line' => $e->getLine(),
    ]);
    exit;
}
