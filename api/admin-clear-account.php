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
if (!is_array($input)) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit;
}

$userId = (int)($input['user_id'] ?? 0);
$accountId = isset($input['account_id']) ? (int)$input['account_id'] : 0;
$reason = trim($input['reason'] ?? '');

if ($userId <= 0) {
    echo json_encode(['success' => false, 'message' => 'User ID is required']);
    exit;
}

enforceDemoUserAdminAccessForUserId($userId);

if ($accountId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Select an account to clear']);
    exit;
}

if ($reason === '') {
    echo json_encode(['success' => false, 'message' => 'Reason is required']);
    exit;
}

try {
    $result = adminClearUserAccountHistory(
        Database::getInstance(),
        $userId,
        $accountId,
        $reason
    );

    echo json_encode([
        'success' => true,
        'message' => 'Account #' . ($result['account_number'] ?? $accountId) . ' cleared for this user. '
            . ($result['deleted_count'] ?? 0) . ' transaction(s) removed, balance set to $0.00.',
        'deleted_count' => (int)($result['deleted_count'] ?? 0),
        'accounts_zeroed' => 1,
        'scope' => 'account',
    ]);
} catch (InvalidArgumentException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} catch (Throwable $e) {
    error_log('Admin Clear Account Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage() ?: 'An error occurred while clearing the account',
    ]);
}
