<?php
/**
 * Admin API — 7th Trade Hub integration settings (super admin only).
 */
error_reporting(E_ALL);
ini_set('display_errors', '0');
ob_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/seventh-tradehub.php';

if (ob_get_length() !== false) {
    ob_end_clean();
}

header('Content-Type: application/json; charset=UTF-8');

if (!isLoggedIn() || ($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if (!isSuperAdmin()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Super administrator access required']);
    exit;
}

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput ?: '', true);
if (!is_array($input)) {
    $input = $_POST;
}

$action = trim((string)($input['action'] ?? ''));

try {
    seventhTradeHubEnsureSchema();

    if ($action === 'get') {
        echo json_encode(['success' => true, 'data' => seventhTradeHubAdminSummary()]);
        exit;
    }

    if ($action === 'save') {
        $context = trim((string)($input['context'] ?? ''));
        if (!in_array($context, [SEVENTH_TRADEHUB_CONTEXT_DEMO, SEVENTH_TRADEHUB_CONTEXT_OWNED], true)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid context']);
            exit;
        }

        $hubUrl = trim((string)($input['hub_url'] ?? ''));
        if ($hubUrl !== '') {
            if (!seventhTradeHubSaveHubUrl($hubUrl, (int)$_SESSION['user_id'])) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to save Hub URL']);
                exit;
            }
        }

        $result = seventhTradeHubSaveIntegration($context, [
            'enabled' => !empty($input['enabled']),
            'integration_id' => $input['integration_id'] ?? '',
            'client_id' => $input['client_id'] ?? '',
            'client_secret' => $input['client_secret'] ?? '',
            'webhook_secret' => $input['webhook_secret'] ?? '',
            'expected_user_email' => $input['expected_user_email'] ?? '',
            'expected_admin_email' => $input['expected_admin_email'] ?? '',
        ], (int)$_SESSION['user_id']);

        if (empty($result['ok'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $result['error'] ?? 'Failed to save integration settings',
            ]);
            exit;
        }

        logActivity($_SESSION['user_id'], 'HUB_INTEGRATION_SAVED', 'Saved 7th Trade Hub settings for context: ' . $context);
        $summary = seventhTradeHubAdminSummary();
        $ctxKey = $context === SEVENTH_TRADEHUB_CONTEXT_DEMO ? 'demo' : 'owned';
        $op = $summary[$ctxKey]['operational'] ?? null;
        $msg = 'Integration settings saved';
        if (is_array($op) && empty($op['ok'])) {
            $msg .= ' — warning: ' . ($op['reason'] ?? 'not ready for Hub traffic');
        } elseif (is_array($op) && !empty($op['ok'])) {
            $msg .= ' — ready for Hub traffic';
        }
        echo json_encode([
            'success' => true,
            'message' => $msg,
            'data' => $summary,
        ]);
        exit;
    }

    if ($action === 'webhook_ping') {
        $context = trim((string)($input['context'] ?? ''));
        $integration = seventhTradeHubGetByContext($context);
        if (!$integration) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Integration not found']);
            exit;
        }
        $ping = seventhTradeHubWebhookPing($integration);
        echo json_encode(['success' => $ping['ok'], 'message' => $ping['message']]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
} catch (Throwable $e) {
    error_log('admin-seventh-tradehub-settings: ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    http_response_code(500);
    // Super-admin endpoint — surface a concise reason so the UI is actionable
    $detail = trim($e->getMessage());
    if (strlen($detail) > 180) {
        $detail = substr($detail, 0, 177) . '...';
    }
    echo json_encode([
        'success' => false,
        'message' => $detail !== ''
            ? ('Server error: ' . $detail)
            : 'Server error while saving Hub settings',
    ]);
}
