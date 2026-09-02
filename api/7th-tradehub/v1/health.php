<?php
/**
 * POST /api/7th-tradehub/v1/health — Hub connection check (Protocol v1).
 */
require_once __DIR__ . '/../../../config/config.php';

header('Content-Type: application/json');
header('Accept: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    seventhTradeHubHubError('method_not_allowed', 405);
}

$raw = file_get_contents('php://input');
$payload = json_decode($raw ?: '', true);
if (!is_array($payload)) {
    seventhTradeHubHubError('invalid_payload', 400);
}

$integrationId = trim((string)($payload['integration_id'] ?? ''));
if ($integrationId === '') {
    seventhTradeHubHubError('unknown_integration', 404);
}

$integration = seventhTradeHubGetByIntegrationId($integrationId);
if (!$integration) {
    seventhTradeHubHubError('unknown_integration', 404);
}

seventhTradeHubVerifyInboundRequest($payload, $integration);

http_response_code(200);
echo json_encode([
    'ok' => true,
    'capabilities' => seventhTradeHubCapabilitiesForContext((string)$integration['context']),
], JSON_UNESCAPED_SLASHES);
