<?php
@ini_set('display_errors', 0);
@error_reporting(0);
ob_start();

try {
    require_once __DIR__ . '/../config/config.php';
    require_once __DIR__ . '/../includes/functions.php';
    require_once __DIR__ . '/../includes/security.php';
    require_once __DIR__ . '/../includes/transaction-history-generator.php';
    ob_get_clean();
    header('Content-Type: application/json; charset=UTF-8');
    header('X-Content-Type-Options: nosniff');
} catch (Throwable $e) {
    ob_end_clean();
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(['success' => false, 'message' => 'Setup error: ' . $e->getMessage()]);
    exit;
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit;
}

try {
    $batchId = Security::sanitize($input['batch_id'] ?? '');
    if ($batchId === '') {
        throw new InvalidArgumentException('batch_id is required.');
    }

    $confirmWithActivity = !empty($input['confirm_with_activity']);
    $generator = new TransactionHistoryGenerator();
    $result = $generator->undoBatch($batchId, $confirmWithActivity);

    if (!empty($result['blocked'])) {
        http_response_code((int)($result['http_status'] ?? 409));
    }

    echo json_encode($result);
} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} catch (Throwable $e) {
    error_log('admin-undo-transaction-batch: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
