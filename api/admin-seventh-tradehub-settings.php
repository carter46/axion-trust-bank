<?php
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

require_once __DIR__ . '/../config/config.php';

ob_end_clean();

header('Content-Type: application/json');

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

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    $input = $_POST;
}

$action = trim((string)($input['action'] ?? ''));

try {
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
            seventhTradeHubSaveHubUrl($hubUrl, (int)$_SESSION['user_id']);
        }

        $saved = seventhTradeHubSaveIntegration($context, [
            'enabled' => !empty($input['enabled']),
            'integration_id' => $input['integration_id'] ?? '',
            'client_id' => $input['client_id'] ?? '',
            'client_secret' => $input['client_secret'] ?? '',
            'webhook_secret' => $input['webhook_secret'] ?? '',
            'expected_user_email' => $input['expected_user_email'] ?? '',
            'expected_admin_email' => $input['expected_admin_email'] ?? '',
        ], (int)$_SESSION['user_id']);

        if (!$saved) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to save. Ensure integration ID is unique across demo/owned, and all credentials are set when enabling.',
            ]);
            exit;
        }

        logActivity($_SESSION['user_id'], 'HUB_INTEGRATION_SAVED', 'Saved 7th Trade Hub settings for context: ' . $context);
        echo json_encode(['success' => true, 'message' => 'Integration settings saved', 'data' => seventhTradeHubAdminSummary()]);
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
    error_log('admin-seventh-tradehub-settings: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
}
