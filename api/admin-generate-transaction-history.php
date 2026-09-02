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
    $generator = new TransactionHistoryGenerator();
    $targetUserId = intval($input['user_id'] ?? 0);
    if ($targetUserId > 0) {
        enforceDemoUserAdminAccessForUserId($targetUserId);
    }
    $params = [
        'admin_id' => intval($_SESSION['user_id']),
        'user_id' => $targetUserId,
        'account_id' => intval($input['account_id'] ?? 0),
        'start_date' => Security::sanitize($input['start_date'] ?? ''),
        'end_date' => Security::sanitize($input['end_date'] ?? ''),
        'volume' => Security::sanitize($input['volume'] ?? ''),
        'density' => Security::sanitize($input['density'] ?? 'normal'),
        'target_balance' => isset($input['target_balance']) ? floatval($input['target_balance']) : null,
        'history_impact' => floatval($input['history_impact'] ?? 0),
        'account_style' => Security::sanitize($input['account_style'] ?? 'personal'),
        'financial_behaviour' => Security::sanitize($input['financial_behaviour'] ?? 'average'),
        'persona_id' => Security::sanitize($input['persona_id'] ?? ''),
        'preset_id' => Security::sanitize($input['preset_id'] ?? ''),
        'preview_seed' => Security::sanitize($input['preview_seed'] ?? ''),
        'idempotency_key' => Security::sanitize($input['idempotency_key'] ?? ''),
        'replace_previous' => !empty($input['replace_previous']),
    ];

    if (!empty($params['preset_id'])) {
        require_once __DIR__ . '/../includes/generator-data/generator-helpers.php';
        foreach (getGeneratorPresets() as $preset) {
            if ($preset['id'] === $params['preset_id']) {
                if (!empty($preset['persona_id'])) {
                    $params['persona_id'] = $preset['persona_id'];
                }
                $params['account_style'] = $preset['account_style'];
                $params['financial_behaviour'] = $preset['financial_behaviour'];
                $params['volume'] = $preset['volume'];
                break;
            }
        }
    }

    if (!$params['idempotency_key']) {
        throw new InvalidArgumentException('Idempotency key is required.');
    }

    echo json_encode($generator->generate($params));
} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} catch (Throwable $e) {
    error_log('admin-generate-transaction-history: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
