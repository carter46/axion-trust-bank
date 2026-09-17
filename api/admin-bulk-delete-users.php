<?php
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

$deleteCore = __DIR__ . '/../includes/admin-user-delete.php';
if (!is_file($deleteCore)) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Delete helper is missing. Upload includes/admin-user-delete.php']);
    exit;
}
require_once $deleteCore;

ob_end_clean();
header('Content-Type: application/json');

$sessionUserId = (int)($_SESSION['user_id'] ?? 0);
$sessionRole = (string)($_SESSION['user_role'] ?? '');
$actingAdminId = function_exists('resolveActingAdminId') ? resolveActingAdminId() : 0;
if ($actingAdminId <= 0 && function_exists('getActingAdminId')) {
    $actingAdminId = getActingAdminId();
}
if ($actingAdminId <= 0 && $sessionUserId > 0 && $sessionRole === 'admin') {
    $actingAdminId = $sessionUserId;
}
if ($actingAdminId <= 0 && !empty($_SESSION['admin_original_id'])) {
    $actingAdminId = (int)$_SESSION['admin_original_id'];
}

if ($actingAdminId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit;
}

$userIds = $input['user_ids'] ?? [];
if (!is_array($userIds) || empty($userIds)) {
    echo json_encode(['success' => false, 'message' => 'Select at least one user to delete']);
    exit;
}

$userIds = array_values(array_unique(array_filter(array_map('intval', $userIds))));
if (empty($userIds)) {
    echo json_encode(['success' => false, 'message' => 'No valid user IDs provided']);
    exit;
}

if (count($userIds) > 100) {
    echo json_encode(['success' => false, 'message' => 'Maximum 100 users per bulk delete']);
    exit;
}

$deleted = [];
$failed = [];

foreach ($userIds as $userId) {
    $result = adminPermanentlyDeleteRegularUser($userId, $actingAdminId);
    if (!empty($result['success'])) {
        $deleted[] = $userId;
    } else {
        $failed[] = [
            'id' => $userId,
            'message' => $result['message'] ?? 'Failed to delete user',
        ];
    }
}

$deletedCount = count($deleted);
$failedCount = count($failed);

if ($deletedCount === 0) {
    echo json_encode([
        'success' => false,
        'message' => $failed[0]['message'] ?? 'Failed to delete users',
        'deleted' => 0,
        'failed' => $failedCount,
        'errors' => $failed,
    ]);
    exit;
}

$message = $deletedCount . ' user' . ($deletedCount === 1 ? '' : 's') . ' deleted';
if ($failedCount > 0) {
    $message .= '; ' . $failedCount . ' could not be deleted';
}

if (function_exists('logActivity')) {
    logActivity($actingAdminId, 'USERS_BULK_DELETED', $message . ' (IDs: ' . implode(',', $deleted) . ')');
}

echo json_encode([
    'success' => true,
    'message' => $message,
    'deleted' => $deletedCount,
    'failed' => $failedCount,
    'errors' => $failed,
]);
