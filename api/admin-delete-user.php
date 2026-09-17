<?php
error_reporting(E_ALL);
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

$userIdToDelete = intval($input['user_id'] ?? 0);
$result = adminPermanentlyDeleteRegularUser($userIdToDelete, $actingAdminId);
echo json_encode($result);
