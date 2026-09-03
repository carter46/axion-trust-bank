<?php
/**
 * GET /auth/7th-tradehub/demo/consume — Hub SSO browser entry (demo + owned).
 *
 * Always require the Hub module here (do not rely only on config.php).
 */
ob_start();

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../includes/seventh-tradehub.php';

if (ob_get_length() !== false) {
    ob_end_clean();
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    seventhTradeHubRenderConsumeError('Invalid request method.');
}

$token = trim((string)($_GET['token'] ?? ''));
$queryIntegrationId = trim((string)($_GET['integration_id'] ?? ''));

if ($token === '' || $queryIntegrationId === '') {
    seventhTradeHubRenderConsumeError();
}

$integration = seventhTradeHubGetByIntegrationId($queryIntegrationId);
if (!$integration || !seventhTradeHubIsIntegrationOperational($integration)) {
    seventhTradeHubRenderConsumeError('This integration is not available.');
}

$context = trim((string)($integration['context'] ?? ''));

if ($context === SEVENTH_TRADEHUB_CONTEXT_OWNED && seventhTradeHubIsOwnedSiteShutdown()) {
    seventhTradeHubConnectionLog([
        'direction' => 'inbound',
        'event' => 'sso_consume',
        'ok' => false,
        'http_status' => 403,
        'error_code' => 'shutdown_active',
        'integration_id' => $queryIntegrationId,
        'context' => $context,
        'message' => 'SSO refused — owned shutdown is ACTIVE',
    ]);
    seventhTradeHubRenderShutdownPage();
}

$result = seventhTradeHubValidateToken($token, $integration);
if (!$result['valid'] || !is_array($result['data'])) {
    seventhTradeHubConnectionLog([
        'direction' => 'inbound',
        'event' => 'sso_consume',
        'ok' => false,
        'http_status' => 401,
        'error_code' => 'invalid_token',
        'integration_id' => $queryIntegrationId,
        'context' => $context,
        'message' => 'SSO refused — token validation failed',
    ]);
    seventhTradeHubRenderConsumeError();
}

$validated = $result['data'];
$responseIntegrationId = trim((string)($validated['integration_id'] ?? ''));
$responseContext = trim((string)($validated['context'] ?? ''));

if ($responseIntegrationId === '' || !hash_equals($queryIntegrationId, $responseIntegrationId)) {
    seventhTradeHubRenderConsumeError();
}
if ($responseContext === '' || !hash_equals($context, $responseContext)) {
    seventhTradeHubRenderConsumeError();
}

if (!seventhTradeHubValidateResponseIsFresh($validated)) {
    seventhTradeHubRenderConsumeError();
}

$hubRole = strtolower(trim((string)($validated['role'] ?? 'user')));
$email = trim((string)($validated['identity']['email'] ?? ''));

if ($context === SEVENTH_TRADEHUB_CONTEXT_OWNED && $hubRole !== 'admin') {
    seventhTradeHubRenderConsumeError('Admin login only for owned tools.');
}

$resolved = seventhTradeHubResolveLocalUser($email, $hubRole, $context);
if (!$resolved['user']) {
    error_log('Hub SSO user resolve failed: ' . ($resolved['error'] ?? 'unknown') . ' email=' . $email);
    seventhTradeHubRenderConsumeError('No matching account exists on this site. Contact your administrator.');
}

establishHubSsoSession($resolved['user'], $context);

seventhTradeHubConnectionLog([
    'direction' => 'inbound',
    'event' => 'sso_consume',
    'ok' => true,
    'http_status' => 302,
    'integration_id' => $queryIntegrationId,
    'context' => $context,
    'message' => 'SSO login OK as ' . $hubRole . ' (' . $email . ')',
]);

if ($hubRole === 'admin') {
    header('Location: ' . SITE_URL . '/admin?hub_sso=1');
} else {
    header('Location: ' . SITE_URL . '/dashboard?hub_sso=1');
}
exit;
