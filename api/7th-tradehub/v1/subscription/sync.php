<?php
/**
 * POST /api/7th-tradehub/v1/subscription/sync — owned tool subscription push.
 *
 * Always require the Hub module here (do not rely only on config.php).
 */
error_reporting(E_ALL);
ini_set('display_errors', '0');
ob_start();

try {
    require_once __DIR__ . '/../../../../config/config.php';
    require_once __DIR__ . '/../../../../includes/seventh-tradehub.php';

    if (ob_get_length() !== false) {
        ob_end_clean();
    }

    header('Content-Type: application/json');
    header('Accept: application/json');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        seventhTradeHubHubError('method_not_allowed', 405, [
            'event' => 'subscription_sync',
            'message' => 'Subscription sync rejected: method not allowed',
        ]);
    }

    $raw = file_get_contents('php://input');
    $payload = json_decode($raw ?: '', true);
    if (!is_array($payload)) {
        seventhTradeHubHubError('invalid_payload', 400, [
            'event' => 'subscription_sync',
            'message' => 'Subscription sync rejected: invalid JSON payload',
        ]);
    }

    $integrationId = trim((string)($payload['integration_id'] ?? ''));
    if ($integrationId === '') {
        seventhTradeHubHubError('unknown_integration', 404, [
            'event' => 'subscription_sync',
            'message' => 'Subscription sync rejected: missing integration_id',
        ]);
    }

    $integration = seventhTradeHubGetByIntegrationId($integrationId);
    if (!$integration) {
        seventhTradeHubHubError('unknown_integration', 404, [
            'event' => 'subscription_sync',
            'integration_id' => $integrationId,
            'message' => 'Subscription sync rejected: integration_id not found on this site',
        ]);
    }

    if (($integration['context'] ?? '') !== SEVENTH_TRADEHUB_CONTEXT_OWNED) {
        seventhTradeHubHubError('context_mismatch', 401, [
            'event' => 'subscription_sync',
            'integration_id' => $integrationId,
            'context' => (string)($integration['context'] ?? ''),
            'message' => 'Subscription sync rejected: credentials are Demo, not Owned (Shutdown Site only targets Owned)',
        ]);
    }

    seventhTradeHubVerifyInboundRequest($payload, $integration);

    $subscription = $payload['subscription'] ?? null;
    if (!is_array($subscription)) {
        seventhTradeHubHubError('invalid_subscription', 400, [
            'event' => 'subscription_sync',
            'integration_id' => $integrationId,
            'context' => SEVENTH_TRADEHUB_CONTEXT_OWNED,
            'message' => 'Subscription sync rejected: missing subscription object',
        ]);
    }

    $apply = seventhTradeHubApplySubscription($integrationId, $subscription);
    $diag = seventhTradeHubShutdownDiagnostic();
    $status = trim((string)($subscription['status'] ?? ''));
    $expiresAt = trim((string)($subscription['expires_at'] ?? ''));
    $shutdownActive = !empty($diag['active']);

    $msg = 'Subscription sync received; status=' . ($status !== '' ? $status : 'unknown');
    if ($expiresAt !== '') {
        $msg .= '; expires_at=' . $expiresAt;
    }
    if (!empty($apply['skipped'])) {
        $msg .= '; apply_skipped=' . ($apply['reason'] ?? 'skipped');
    } elseif (empty($apply['applied'])) {
        $msg .= '; apply_failed=' . ($apply['reason'] ?? 'failed');
    }
            if ($shutdownActive) {
                $msg = 'SHUTDOWN sync applied — site gate ACTIVE for non–super-admin (' . $msg . ')';
            } elseif (strtolower($status) === 'expired') {
                $msg .= ' — Hub sent expired but local shutdown gate is NOT active: ' . ($diag['reason'] ?? '');
            }

    seventhTradeHubConnectionLog([
        'direction' => 'inbound',
        'event' => $shutdownActive ? 'shutdown_sync' : 'subscription_sync',
        'ok' => !empty($apply['applied']) || !empty($apply['skipped']),
        'http_status' => 200,
        'integration_id' => $integrationId,
        'context' => SEVENTH_TRADEHUB_CONTEXT_OWNED,
        'message' => $msg,
        'detail' => [
            'subscription_status' => $status,
            'expires_at' => $expiresAt,
            'updated_at' => $subscription['updated_at'] ?? null,
            'apply' => $apply,
            'shutdown' => $diag,
        ],
    ]);

    http_response_code(200);
    echo json_encode([
        'ok' => true,
        'shutdown_active' => $shutdownActive,
    ], JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    error_log('7th-tradehub sync: ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    if (function_exists('seventhTradeHubConnectionLog')) {
        seventhTradeHubConnectionLog([
            'direction' => 'inbound',
            'event' => 'subscription_sync',
            'ok' => false,
            'http_status' => 500,
            'error_code' => 'server_error',
            'message' => 'Subscription sync server error: ' . $e->getMessage(),
        ]);
    }
    if (!headers_sent()) {
        header('Content-Type: application/json');
    }
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'server_error'], JSON_UNESCAPED_SLASHES);
    exit;
}
