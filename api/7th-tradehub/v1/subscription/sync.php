<?php
/**
 * POST /api/7th-tradehub/v1/subscription/sync — owned tool subscription push.
 */
require_once __DIR__ . '/../../../../config/config.php';

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

if (($integration['context'] ?? '') !== SEVENTH_TRADEHUB_CONTEXT_OWNED) {
    seventhTradeHubHubError('context_mismatch', 401);
}

seventhTradeHubVerifyInboundRequest($payload, $integration);

$subscription = $payload['subscription'] ?? null;
if (!is_array($subscription)) {
    seventhTradeHubHubError('invalid_subscription', 400);
}

seventhTradeHubApplySubscription($integrationId, $subscription);

http_response_code(200);
echo json_encode(['ok' => true], JSON_UNESCAPED_SLASHES);
