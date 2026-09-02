<?php
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../models/Account.php';
require_once __DIR__ . '/../models/User.php';

ob_end_clean();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit;
}

$accountId = (int)($input['account_id'] ?? 0);
$freeze = !empty($input['freeze']);
$reason = trim($input['reason'] ?? 'Administrative account freeze');

if ($accountId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Account ID is required']);
    exit;
}

enforceDemoUserAdminAccessForAccountId($accountId);

try {
    $accountModel = new Account();
    $account = $accountModel->findById($accountId);

    if (!$account) {
        echo json_encode(['success' => false, 'message' => 'Account not found']);
        exit;
    }

    $owner = (new User())->findById((int)$account['user_id']);
    if (!$owner || ($owner['role'] ?? '') === 'admin') {
        echo json_encode(['success' => false, 'message' => 'Cannot modify administrator accounts']);
        exit;
    }

    $adminModel = new Admin();
    if ($freeze) {
        $ok = $adminModel->freezeAccount($accountId, $reason);
        $message = $ok ? 'Account frozen successfully' : 'Failed to freeze account';
    } else {
        $ok = $adminModel->unfreezeAccount($accountId);
        $message = $ok ? 'Account unfrozen successfully' : 'Failed to unfreeze account';
    }

    if ($ok) {
        logActivity(
            (int)$_SESSION['user_id'],
            $freeze ? 'ADMIN_ACCOUNT_FROZEN' : 'ADMIN_ACCOUNT_UNFROZEN',
            ($freeze ? 'Froze' : 'Unfroze') . " account {$account['account_number']} for user {$owner['email']} (ID: {$owner['id']})"
        );
    }

    echo json_encode(['success' => (bool)$ok, 'message' => $message]);
} catch (Throwable $e) {
    error_log('admin-freeze-account: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Unable to update account freeze status']);
}
