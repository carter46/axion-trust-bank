<?php
/**
 * Admin API — update password for managed admin / demo accounts (Admin Settings).
 */
error_reporting(E_ALL);
ini_set('display_errors', '0');
ob_start();

try {
    require_once __DIR__ . '/../config/config.php';
    require_once __DIR__ . '/../includes/functions.php';
    require_once __DIR__ . '/../includes/security.php';
    require_once __DIR__ . '/../models/User.php';

    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    header('Content-Type: application/json; charset=UTF-8');

    if (!isLoggedIn() || ($_SESSION['user_role'] ?? '') !== 'admin') {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'Unauthorized',
            'api' => 'admin-update-password',
            'v' => 2,
        ]);
        exit;
    }

    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput ?: '', true);
    if (!is_array($input)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid JSON input', 'api' => 'admin-update-password', 'v' => 2]);
        exit;
    }

    $adminId = (int)($input['admin_id'] ?? 0);
    $newPassword = (string)($input['new_password'] ?? '');

    if ($adminId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Admin ID required', 'api' => 'admin-update-password', 'v' => 2]);
        exit;
    }

    if (strlen($newPassword) < 8) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Password must be 8+ characters', 'api' => 'admin-update-password', 'v' => 2]);
        exit;
    }

    $userModel = new User();
    $admin = $userModel->findById($adminId);

    if (!$admin) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Account not found', 'api' => 'admin-update-password', 'v' => 2]);
        exit;
    }

    if (!canManageManagedAccount($admin, (int)$_SESSION['user_id'])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'You do not have permission to edit this account', 'api' => 'admin-update-password', 'v' => 2]);
        exit;
    }

    $isDemo = isDemoUserRecord($admin);
    if (!$isDemo && ($admin['role'] ?? '') !== 'admin') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'User is not a managed admin account', 'api' => 'admin-update-password', 'v' => 2]);
        exit;
    }

    $ok = $userModel->updatePassword($adminId, $newPassword);
    if (!$ok) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update password', 'api' => 'admin-update-password', 'v' => 2]);
        exit;
    }

    if (function_exists('seventhTradeHubMaybeSyncOwnedAdminCredentials')) {
        seventhTradeHubMaybeSyncOwnedAdminCredentials($admin, null, $newPassword);
    }

    logActivity((int)$_SESSION['user_id'], 'ADMIN_PASSWORD_UPDATED', "Updated password for {$admin['email']}");

    echo json_encode([
        'success' => true,
        'message' => 'Password updated successfully',
        'api' => 'admin-update-password',
        'v' => 2,
    ]);
    exit;
} catch (Throwable $e) {
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    error_log('Admin Password Update Error: ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code(500);
    }
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage(),
        'api' => 'admin-update-password',
        'v' => 2,
        'file' => basename($e->getFile()),
        'line' => $e->getLine(),
    ]);
    exit;
}
