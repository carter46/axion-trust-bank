<?php
/**
 * POST /api/7th-tradehub/v1/health — Hub connection check (Protocol v1).
 *
 * Always require the Hub module here (do not rely only on config.php).
 */
error_reporting(E_ALL);
ini_set('display_errors', '0');
ob_start();

try {
    require_once __DIR__ . '/../../../config/config.php';
    require_once __DIR__ . '/../../../includes/seventh-tradehub.php';

    if (ob_get_length() !== false) {
        ob_end_clean();
    }

    header('Content-Type: application/json');
    header('Accept: application/json');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        seventhTradeHubHubError('method_not_allowed', 405, [
            'event' => 'health',
            'message' => 'Health rejected: method not allowed',
        ]);
    }

    $raw = file_get_contents('php://input');
    $payload = json_decode($raw ?: '', true);
    if (!is_array($payload)) {
        seventhTradeHubHubError('invalid_payload', 400, [
            'event' => 'health',
            'message' => 'Health rejected: invalid JSON payload',
        ]);
    }

    $integrationId = trim((string)($payload['integration_id'] ?? ''));
    if ($integrationId === '') {
        seventhTradeHubHubError('unknown_integration', 404, [
            'event' => 'health',
            'message' => 'Health rejected: missing integration_id',
        ]);
    }

    $integration = seventhTradeHubGetByIntegrationId($integrationId);
    if (!$integration) {
        seventhTradeHubHubError('unknown_integration', 404, [
            'event' => 'health',
            'integration_id' => $integrationId,
            'message' => 'Health rejected: integration_id not found on this site (wrong domain DB or Demo/Owned mismatch)',
        ]);
    }

    seventhTradeHubVerifyInboundRequest($payload, $integration);

    $context = trim((string)($integration['context'] ?? ''));
    $caps = seventhTradeHubCapabilitiesForContext($context);

    seventhTradeHubConnectionLog([
        'direction' => 'inbound',
        'event' => 'health',
        'ok' => true,
        'http_status' => 200,
        'integration_id' => $integrationId,
        'context' => $context,
        'message' => 'Connection successful (health check)',
        'detail' => ['capabilities' => $caps],
    ]);

    http_response_code(200);
    echo json_encode([
        'ok' => true,
        'capabilities' => $caps,
    ], JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    error_log('7th-tradehub health: ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    if (function_exists('seventhTradeHubConnectionLog')) {
        seventhTradeHubConnectionLog([
            'direction' => 'inbound',
            'event' => 'health',
            'ok' => false,
            'http_status' => 500,
            'error_code' => 'server_error',
            'message' => 'Health server error: ' . $e->getMessage(),
        ]);
    }
    if (!headers_sent()) {
        header('Content-Type: application/json');
    }
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'server_error'], JSON_UNESCAPED_SLASHES);
    exit;
}
