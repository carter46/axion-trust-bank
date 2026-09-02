<?php
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

ob_end_clean();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$accountId = intval($input['admin_id'] ?? 0);

if (!$accountId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Account ID is required']);
    exit;
}

if ($accountId == $_SESSION['user_id']) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'You cannot delete your own account']);
    exit;
}

try {
    $db = Database::getInstance();
    $userModel = new User();
    $target = $userModel->findById($accountId);

    if (!$target) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Account not found']);
        exit;
    }

    if (!canManageManagedAccount($target, $_SESSION['user_id'])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'You do not have permission to delete this account']);
        exit;
    }

    if (!empty($target['is_super_admin'])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Cannot delete Super Administrator']);
        exit;
    }

    if (isDemoUserRecord($target)) {
        $deleteSql = "DELETE FROM users WHERE id = ? AND COALESCE(is_demo_user, 0) = 1";
        $result = $db->query($deleteSql, [$accountId]);
        $logAction = 'DEMO_USER_DELETED';
        $logMessage = "Deleted demo user: {$target['full_name']} ({$target['email']})";
        $successMessage = 'Demo user deleted successfully';
    } else {
        if (($target['role'] ?? '') !== 'admin') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Account is not an administrator']);
            exit;
        }

        $stmtCount = $db->query("SELECT COUNT(*) AS count FROM users WHERE role = 'admin' AND COALESCE(is_super_admin, 0) = 0");
        $countResult = $stmtCount ? $stmtCount->fetch() : ['count' => 0];
        if ((int)($countResult['count'] ?? 0) <= 1 && empty($target['is_super_admin'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Cannot delete the last administrator. Add another admin first.']);
            exit;
        }

        $deleteSql = "DELETE FROM users WHERE id = ? AND role = 'admin' AND COALESCE(is_demo_user, 0) = 0";
        $result = $db->query($deleteSql, [$accountId]);
        $logAction = 'ADMIN_DELETED';
        $logMessage = "Deleted administrator: {$target['full_name']} ({$target['email']})";
        $successMessage = 'Administrator deleted successfully';
    }

    if ($result) {
        logActivity($_SESSION['user_id'], $logAction, $logMessage);
        echo json_encode(['success' => true, 'message' => $successMessage]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete account']);
    }
} catch (Exception $e) {
    error_log('Delete Managed Account Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred while deleting the account']);
}
