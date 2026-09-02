<?php
/**
 * 7th Trade Hub Protocol v1 — integration registry, verify, SSO, shutdown.
 */

if (!defined('SEVENTH_TRADEHUB_CONTEXT_DEMO')) {
    define('SEVENTH_TRADEHUB_CONTEXT_DEMO', 'demo');
    define('SEVENTH_TRADEHUB_CONTEXT_OWNED', 'owned_tool');
}

/**
 * Ensure Hub tables exist (e.g. Hub health check before any admin page load).
 */
function seventhTradeHubEnsureSchema(): void
{
    static $checked = false;
    if ($checked) {
        return;
    }
    $checked = true;
    try {
        if (!class_exists('Database')) {
            return;
        }
        $db = Database::getInstance();
        $stmt = $db->query("SHOW TABLES LIKE 'seventh_tradehub_integrations'");
        if (!$stmt || !$stmt->fetch()) {
            require_once __DIR__ . '/database-auto-migrate.php';
            (new DatabaseAutoMigrate())->run(null);
        }
        // Always ensure both context slots exist (migration may have created table but skipped seeds)
        seventhTradeHubEnsureContextRow(SEVENTH_TRADEHUB_CONTEXT_DEMO);
        seventhTradeHubEnsureContextRow(SEVENTH_TRADEHUB_CONTEXT_OWNED);
    } catch (Throwable $e) {
        error_log('seventhTradeHubEnsureSchema: ' . $e->getMessage());
    }
}

function seventhTradeHubIsCliRequest(): bool
{
    return PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg';
}

/**
 * Shared Hub base URL (env override, then system_settings).
 */
function seventhTradeHubHubUrl(): string
{
    $env = trim((string)($_ENV['SEVENTH_TRADEHUB_HUB_URL'] ?? getenv('SEVENTH_TRADEHUB_HUB_URL') ?: ''));
    if ($env !== '') {
        return rtrim($env, '/');
    }
    try {
        if (class_exists('SystemSettings')) {
            $url = trim((string)SystemSettings::getInstance()->get('seventh_tradehub_hub_url', ''));
            if ($url !== '') {
                return rtrim($url, '/');
            }
        }
    } catch (Throwable $e) {
        error_log('seventhTradeHubHubUrl: ' . $e->getMessage());
    }
    return '';
}

function seventhTradeHubSaveHubUrl(string $url, ?int $userId = null): bool
{
    $url = rtrim(trim($url), '/');
    try {
        return SystemSettings::getInstance()->update('seventh_tradehub_hub_url', $url, $userId);
    } catch (Throwable $e) {
        error_log('seventhTradeHubSaveHubUrl: ' . $e->getMessage());
        return false;
    }
}

/**
 * @return array<string, mixed>|null
 */
function seventhTradeHubGetByContext(string $context): ?array
{
    if (!in_array($context, [SEVENTH_TRADEHUB_CONTEXT_DEMO, SEVENTH_TRADEHUB_CONTEXT_OWNED], true)) {
        return null;
    }
    seventhTradeHubEnsureSchema();
    try {
        $db = Database::getInstance();
        $stmt = $db->query(
            'SELECT * FROM seventh_tradehub_integrations WHERE context = ? LIMIT 1',
            [$context]
        );
        $row = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
        return $row ?: null;
    } catch (Throwable $e) {
        error_log('seventhTradeHubGetByContext: ' . $e->getMessage());
        return null;
    }
}

/**
 * @return array<string, mixed>|null
 */
function seventhTradeHubGetByIntegrationId(string $integrationId): ?array
{
    $integrationId = trim($integrationId);
    if ($integrationId === '') {
        return null;
    }
    seventhTradeHubEnsureSchema();
    try {
        $db = Database::getInstance();
        $stmt = $db->query(
            'SELECT * FROM seventh_tradehub_integrations WHERE integration_id = ? LIMIT 1',
            [$integrationId]
        );
        $row = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
        return $row ?: null;
    } catch (Throwable $e) {
        error_log('seventhTradeHubGetByIntegrationId: ' . $e->getMessage());
        return null;
    }
}

/**
 * Decrypt client secret for an integration row.
 */
function seventhTradeHubClientSecret(array $integration): string
{
    $enc = trim((string)($integration['client_secret_enc'] ?? ''));
    if ($enc === '') {
        return '';
    }
    if (function_exists('decryptData')) {
        $dec = decryptData($enc);
        return is_string($dec) ? $dec : '';
    }
    return $enc;
}

function seventhTradeHubWebhookSecret(array $integration): string
{
    $enc = trim((string)($integration['webhook_secret_enc'] ?? ''));
    if ($enc === '') {
        return '';
    }
    if (function_exists('decryptData')) {
        $dec = decryptData($enc);
        return is_string($dec) ? $dec : '';
    }
    return $enc;
}

/**
 * Row is enabled and has minimum credentials for Hub traffic.
 */
function seventhTradeHubIsIntegrationOperational(?array $integration): bool
{
    if (!$integration || empty($integration['enabled'])) {
        return false;
    }
    $id = trim((string)($integration['integration_id'] ?? ''));
    $clientId = trim((string)($integration['client_id'] ?? ''));
    $secret = seventhTradeHubClientSecret($integration);
    return $id !== '' && $clientId !== '' && $secret !== '';
}

function seventhTradeHubCanonicalize($value): string
{
    if (is_array($value)) {
        if ($value === [] || array_keys($value) === range(0, count($value) - 1)) {
            return '[' . implode(',', array_map('seventhTradeHubCanonicalize', $value)) . ']';
        }
        ksort($value);
        $parts = [];
        foreach ($value as $key => $item) {
            $parts[] = seventhTradeHubCanonicalize((string)$key) . ':' . seventhTradeHubCanonicalize($item);
        }
        return '{' . implode(',', $parts) . '}';
    }
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    if ($value === null) {
        return 'null';
    }
    if (is_int($value) || is_float($value)) {
        return (string)$value;
    }
    if (!is_string($value)) {
        throw new InvalidArgumentException('Unsupported type for canonicalization.');
    }
    return '"' . addcslashes($value, "\\\"\n\r\t") . '"';
}

function seventhTradeHubVerifyPayload(array $payload, string $clientSecret): bool
{
    $signature = $payload['signature'] ?? null;
    if (!is_string($signature) || $signature === '') {
        return false;
    }
    if (($payload['protocol'] ?? null) !== '7th-tradehub') {
        return false;
    }
    if ((int)($payload['version'] ?? 0) !== 1) {
        return false;
    }
    $copy = $payload;
    unset($copy['signature']);
    ksort($copy);
    $expected = hash_hmac('sha256', seventhTradeHubCanonicalize($copy), $clientSecret);
    return hash_equals($expected, $signature);
}

function seventhTradeHubAssertionExpired(array $payload): bool
{
    $expiresAt = trim((string)($payload['expires_at'] ?? ''));
    if ($expiresAt === '') {
        return true;
    }
    try {
        $exp = new DateTimeImmutable($expiresAt);
        return $exp < new DateTimeImmutable('now', $exp->getTimezone());
    } catch (Throwable $e) {
        return true;
    }
}

function seventhTradeHubHeader(string $name): string
{
    $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
    return trim((string)($_SERVER[$key] ?? ''));
}

/**
 * Emit JSON error and exit (merchant endpoints).
 */
function seventhTradeHubHubError(string $code, int $httpStatus = 401): void
{
    http_response_code($httpStatus);
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => $code], JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * Verify inbound health/sync request. Returns integration row or exits with error JSON.
 *
 * @return array<string, mixed>
 */
function seventhTradeHubVerifyInboundRequest(array $payload, array $integration): array
{
    if (!seventhTradeHubIsIntegrationOperational($integration)) {
        seventhTradeHubHubError('integration_disabled', 401);
    }

    $payloadIntegrationId = trim((string)($payload['integration_id'] ?? ''));
    $rowIntegrationId = trim((string)($integration['integration_id'] ?? ''));
    if ($payloadIntegrationId === '' || !hash_equals($rowIntegrationId, $payloadIntegrationId)) {
        seventhTradeHubHubError('unknown_integration', 404);
    }

    $headerIntegrationId = seventhTradeHubHeader('X-7TH-Integration-Id');
    if ($headerIntegrationId === '' || !hash_equals($rowIntegrationId, $headerIntegrationId)) {
        seventhTradeHubHubError('integration_id_mismatch', 401);
    }

    $headerClientId = seventhTradeHubHeader('X-7TH-Client-Id');
    $rowClientId = trim((string)($integration['client_id'] ?? ''));
    if ($headerClientId === '' || !hash_equals($rowClientId, $headerClientId)) {
        seventhTradeHubHubError('client_id_mismatch', 401);
    }

    $payloadContext = trim((string)($payload['context'] ?? ''));
    $rowContext = trim((string)($integration['context'] ?? ''));
    if ($payloadContext === '' || !hash_equals($rowContext, $payloadContext)) {
        seventhTradeHubHubError('context_mismatch', 401);
    }

    if (seventhTradeHubAssertionExpired($payload)) {
        seventhTradeHubHubError('expired_assertion', 401);
    }

    $secret = seventhTradeHubClientSecret($integration);
    if (!seventhTradeHubVerifyPayload($payload, $secret)) {
        seventhTradeHubHubError('invalid_signature', 401);
    }

    seventhTradeHubRecordNonce($integration, $payload);

    return $integration;
}

function seventhTradeHubRecordNonce(array $integration, array $payload): void
{
    $requestId = trim((string)($payload['request_id'] ?? ''));
    $nonce = trim((string)($payload['nonce'] ?? ''));
    $integrationId = trim((string)($integration['integration_id'] ?? ''));
    if ($requestId === '' || $nonce === '' || $integrationId === '') {
        return;
    }
    try {
        $db = Database::getInstance();
        $stmt = $db->query(
            'SELECT id FROM seventh_tradehub_nonces WHERE integration_id = ? AND request_id = ? LIMIT 1',
            [$integrationId, $requestId]
        );
        if ($stmt && $stmt->fetch()) {
            seventhTradeHubHubError('replay_detected', 401);
        }
        $db->query(
            'INSERT INTO seventh_tradehub_nonces (integration_id, request_id, nonce, seen_at) VALUES (?, ?, ?, NOW())',
            [$integrationId, $requestId, $nonce]
        );
    } catch (Throwable $e) {
        error_log('seventhTradeHubRecordNonce: ' . $e->getMessage());
    }
}

function seventhTradeHubCapabilitiesForContext(string $context): array
{
    if ($context === SEVENTH_TRADEHUB_CONTEXT_DEMO) {
        return ['health', 'demo_user_login', 'demo_admin_login'];
    }
    if ($context === SEVENTH_TRADEHUB_CONTEXT_OWNED) {
        return ['health', 'subscription_sync', 'shutdown_on_expiry', 'owned_admin_login'];
    }
    return ['health'];
}

/**
 * @return array{valid: bool, data: ?array, http_code: int, error: ?string}
 */
function seventhTradeHubValidateToken(string $token, array $integration): array
{
    $token = trim($token);
    if ($token === '' || !seventhTradeHubIsIntegrationOperational($integration)) {
        return ['valid' => false, 'data' => null, 'http_code' => 403, 'error' => 'integration_disabled'];
    }

    $hubUrl = seventhTradeHubHubUrl();
    if ($hubUrl === '') {
        return ['valid' => false, 'data' => null, 'http_code' => 503, 'error' => 'hub_url_missing'];
    }

    if (!function_exists('curl_init')) {
        return ['valid' => false, 'data' => null, 'http_code' => 503, 'error' => 'curl_missing'];
    }

    $clientId = trim((string)($integration['client_id'] ?? ''));
    $clientSecret = seventhTradeHubClientSecret($integration);

    $ch = curl_init($hubUrl . '/api/site-integrations/v1/demo/tokens/validate');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json',
            'X-7TH-Client-Id: ' . $clientId,
            'X-7TH-Client-Secret: ' . $clientSecret,
        ],
        CURLOPT_POSTFIELDS => json_encode(['token' => $token]),
        CURLOPT_TIMEOUT => 15,
    ]);
    $raw = curl_exec($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if (!is_string($raw)) {
        return ['valid' => false, 'data' => null, 'http_code' => $code ?: 502, 'error' => 'hub_unreachable'];
    }

    $body = json_decode($raw, true);
    if ($code === 422) {
        return ['valid' => false, 'data' => is_array($body) ? $body : null, 'http_code' => 422, 'error' => 'invalid_token'];
    }
    if ($code === 401) {
        return ['valid' => false, 'data' => is_array($body) ? $body : null, 'http_code' => 401, 'error' => 'invalid_credentials'];
    }
    if ($code !== 200 || !is_array($body) || ($body['valid'] ?? false) !== true) {
        return ['valid' => false, 'data' => is_array($body) ? $body : null, 'http_code' => $code, 'error' => 'validate_failed'];
    }

    return ['valid' => true, 'data' => $body, 'http_code' => 200, 'error' => null];
}

/**
 * Case-insensitive local user lookup for Hub SSO / readiness.
 *
 * @return array<string, mixed>|null
 */
function seventhTradeHubFindUserByEmail(string $email): ?array
{
    $email = trim($email);
    if ($email === '') {
        return null;
    }
    if (!class_exists('User')) {
        require_once __DIR__ . '/../models/User.php';
    }
    $userModel = new User();
    $user = $userModel->findByEmail($email);
    if ($user) {
        return $user;
    }
    try {
        $db = Database::getInstance();
        $stmt = $db->query('SELECT * FROM users WHERE LOWER(email) = LOWER(?) LIMIT 1', [$email]);
        $row = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
        return $row ?: null;
    } catch (Throwable $e) {
        return null;
    }
}

function seventhTradeHubValidateResponseIsFresh(array $validated): bool
{
    if (($validated['protocol'] ?? null) !== '7th-tradehub') {
        return false;
    }
    if ((int)($validated['version'] ?? 0) !== 1) {
        return false;
    }
    $expiresAt = trim((string)($validated['expires_at'] ?? ''));
    if ($expiresAt === '') {
        return true;
    }
    try {
        $exp = new DateTimeImmutable($expiresAt);
        return $exp >= new DateTimeImmutable('now', $exp->getTimezone());
    } catch (Throwable $e) {
        return false;
    }
}

/**
 * @return array{user: ?array, error: ?string}
 */
function seventhTradeHubResolveLocalUser(string $email, string $hubRole, string $context): array
{
    $email = trim($email);
    $hubRole = strtolower(trim($hubRole));
    if ($email === '') {
        return ['user' => null, 'error' => 'missing_email'];
    }

    $user = seventhTradeHubFindUserByEmail($email);
    if (!$user) {
        return ['user' => null, 'error' => 'user_not_found'];
    }

    if (($user['status'] ?? '') === 'deleted') {
        return ['user' => null, 'error' => 'user_inactive'];
    }

    if ($hubRole === 'admin') {
        if (($user['role'] ?? '') !== 'admin') {
            return ['user' => null, 'error' => 'role_mismatch'];
        }
        if (!empty($user['is_super_admin'])) {
            return ['user' => null, 'error' => 'super_admin_not_allowed'];
        }
    } elseif ($hubRole === 'user') {
        if ($context === SEVENTH_TRADEHUB_CONTEXT_DEMO) {
            if (!isDemoUserRecord($user)) {
                return ['user' => null, 'error' => 'not_demo_user'];
            }
        } else {
            return ['user' => null, 'error' => 'owned_user_sso_not_supported'];
        }
    } else {
        return ['user' => null, 'error' => 'unknown_role'];
    }

    return ['user' => $user, 'error' => null];
}

function establishHubSsoSession(array $user, string $context): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }
    establishUserSession($user);
    unset($_SESSION['security_setup_required'], $_SESSION['security_onboarding'], $_SESSION['temp_user_id']);
    $_SESSION['hub_sso_login'] = 1;
    $_SESSION['hub_sso_context'] = $context;
    logActivity($user['id'], 'HUB_SSO_LOGIN', 'Hub SSO session established (context: ' . $context . ')');
}

function seventhTradeHubRenderConsumeError(string $message = ''): void
{
    $hubConsumeMessage = $message !== '' ? $message : 'This login link has expired or is invalid. Return to 7th Trade Hub and try again.';
    http_response_code(403);
    $view = BASE_PATH . '/views/errors/hub-consume-error.php';
    if (is_file($view)) {
        include $view;
    } else {
        echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Login unavailable</title></head>';
        echo '<body style="margin:0;background:#fff;font-family:sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;">';
        echo '<p style="color:#333;max-width:420px;text-align:center;padding:24px;">' . htmlspecialchars($hubConsumeMessage) . '</p></body></html>';
    }
    exit;
}

/**
 * Readiness hints for admin UI (Hub is authoritative for SSO).
 *
 * @return array<string, mixed>
 */
function seventhTradeHubIdentityReadiness(string $context): array
{
    $integration = seventhTradeHubGetByContext($context);
    if (!$integration) {
        return ['configured' => false, 'checks' => []];
    }

    $checks = [];
    if ($context === SEVENTH_TRADEHUB_CONTEXT_DEMO) {
        $checks[] = seventhTradeHubReadinessCheck(
            'demo_user',
            trim((string)($integration['expected_user_email'] ?? '')),
            'user',
            true
        );
    }
    $checks[] = seventhTradeHubReadinessCheck(
        'admin',
        trim((string)($integration['expected_admin_email'] ?? '')),
        'admin',
        false
    );

    return [
        'configured' => seventhTradeHubIsIntegrationOperational($integration),
        'enabled' => !empty($integration['enabled']),
        'integration_id' => trim((string)($integration['integration_id'] ?? '')),
        'checks' => $checks,
    ];
}

function seventhTradeHubReadinessCheck(string $label, string $email, string $expectedRole, bool $requireDemo): array
{
    if ($email === '') {
        return ['label' => $label, 'email' => '', 'ok' => null, 'message' => 'Not configured (optional hint)'];
    }
    if (!class_exists('User')) {
        require_once __DIR__ . '/../models/User.php';
    }
    $user = seventhTradeHubFindUserByEmail($email);
    if (!$user) {
        return ['label' => $label, 'email' => $email, 'ok' => false, 'message' => 'No local user with this email'];
    }
    if ($expectedRole === 'admin') {
        $ok = ($user['role'] ?? '') === 'admin' && empty($user['is_super_admin']);
        return ['label' => $label, 'email' => $email, 'ok' => $ok, 'message' => $ok ? 'Ready' : 'Must be admin (not super admin)'];
    }
    $ok = isDemoUserRecord($user) && ($user['role'] ?? '') === 'user';
    return ['label' => $label, 'email' => $email, 'ok' => $ok, 'message' => $ok ? 'Ready' : 'Must be demo user'];
}

/**
 * @return array<string, mixed>|null
 */
function seventhTradeHubGetSubscription(string $integrationId): ?array
{
    $integrationId = trim($integrationId);
    if ($integrationId === '') {
        return null;
    }
    try {
        $db = Database::getInstance();
        $stmt = $db->query(
            'SELECT * FROM seventh_tradehub_subscriptions WHERE integration_id = ? LIMIT 1',
            [$integrationId]
        );
        $row = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
        return $row ?: null;
    } catch (Throwable $e) {
        return null;
    }
}

function seventhTradeHubSubscriptionIsExpired(?array $subscription): bool
{
    if (!$subscription) {
        return false;
    }
    $status = strtolower(trim((string)($subscription['status'] ?? '')));
    if ($status === 'expired') {
        return true;
    }
    $expiresAt = trim((string)($subscription['expires_at'] ?? ''));
    if ($expiresAt === '') {
        return false;
    }
    try {
        $exp = new DateTimeImmutable($expiresAt);
        return $exp < new DateTimeImmutable('now', $exp->getTimezone());
    } catch (Throwable $e) {
        return false;
    }
}

function seventhTradeHubIsOwnedSiteShutdown(): bool
{
    $owned = seventhTradeHubGetByContext(SEVENTH_TRADEHUB_CONTEXT_OWNED);
    if (!$owned || empty($owned['enabled'])) {
        return false;
    }
    $integrationId = trim((string)($owned['integration_id'] ?? ''));
    if ($integrationId === '') {
        return false;
    }
    $sub = seventhTradeHubGetSubscription($integrationId);
    return seventhTradeHubSubscriptionIsExpired($sub);
}

function seventhTradeHubIsHubProtocolRequest(): bool
{
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $patterns = [
        '/api/7th-tradehub/v1/health',
        '/api/7th-tradehub/v1/subscription/sync',
        '/auth/7th-tradehub/demo/consume',
    ];
    foreach ($patterns as $pattern) {
        if (stripos($uri, $pattern) !== false || stripos($script, '7th-tradehub') !== false) {
            return true;
        }
    }
    return false;
}

function seventhTradeHubRenderShutdownPage(): void
{
    http_response_code(403);
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
    echo '<title>Session expired</title></head>';
    echo '<body style="margin:0;padding:0;background:#ffffff;min-height:100vh;display:flex;align-items:center;justify-content:center;">';
    echo '<p style="margin:0;font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,sans-serif;font-size:18px;color:#cc0000;">Session expired</p>';
    echo '</body></html>';
    exit;
}

function seventhTradeHubMaybeEnforceShutdown(): void
{
    if (seventhTradeHubIsCliRequest() || seventhTradeHubIsHubProtocolRequest()) {
        return;
    }
    if (seventhTradeHubIsOwnedSiteShutdown()) {
        seventhTradeHubRenderShutdownPage();
    }
}

/**
 * Apply monotonic subscription update from Hub sync/poll.
 */
function seventhTradeHubApplySubscription(string $integrationId, array $subscription): void
{
    $integrationId = trim($integrationId);
    if ($integrationId === '') {
        return;
    }

    $incomingUpdated = trim((string)($subscription['updated_at'] ?? ''));
    $incomingExpires = trim((string)($subscription['expires_at'] ?? ''));
    $status = trim((string)($subscription['status'] ?? 'pending_setup'));
    $toolId = isset($subscription['tool_id']) ? (int)$subscription['tool_id'] : null;
    $publicId = trim((string)($subscription['public_id'] ?? ''));

    $existing = seventhTradeHubGetSubscription($integrationId);
    if ($existing) {
        $storedUpdated = trim((string)($existing['updated_at'] ?? ''));
        if ($storedUpdated !== '' && $incomingUpdated !== '') {
            try {
                $storedDt = new DateTimeImmutable($storedUpdated);
                $incomingDt = new DateTimeImmutable($incomingUpdated);
                if ($incomingDt < $storedDt) {
                    return;
                }
            } catch (Throwable $e) {
                // proceed with update
            }
        }
        $storedExpired = seventhTradeHubSubscriptionIsExpired($existing);
        $incomingExpired = strtolower($status) === 'expired';
        if ($incomingExpires !== '') {
            try {
                $incomingExpDt = new DateTimeImmutable($incomingExpires);
                if ($incomingExpDt < new DateTimeImmutable('now', $incomingExpDt->getTimezone())) {
                    $incomingExpired = true;
                }
            } catch (Throwable $e) {
            }
        }
        if ($storedExpired && !$incomingExpired && $storedUpdated !== '' && $incomingUpdated !== '') {
            try {
                if (new DateTimeImmutable($incomingUpdated) <= new DateTimeImmutable($storedUpdated)) {
                    return;
                }
            } catch (Throwable $e) {
                return;
            }
        }
    }

    try {
        $db = Database::getInstance();
        $db->query(
            'INSERT INTO seventh_tradehub_subscriptions
                (integration_id, tool_id, public_id, status, expires_at, updated_at, last_sync_at)
             VALUES (?, ?, ?, ?, ?, ?, NOW())
             ON DUPLICATE KEY UPDATE
                tool_id = VALUES(tool_id),
                public_id = VALUES(public_id),
                status = VALUES(status),
                expires_at = VALUES(expires_at),
                updated_at = VALUES(updated_at),
                last_sync_at = NOW()',
            [
                $integrationId,
                $toolId,
                $publicId !== '' ? $publicId : null,
                $status,
                $incomingExpires !== '' ? $incomingExpires : null,
                $incomingUpdated !== '' ? $incomingUpdated : null,
            ]
        );
    } catch (Throwable $e) {
        error_log('seventhTradeHubApplySubscription: ' . $e->getMessage());
    }
}

/**
 * @return array<string, mixed>|null
 */
function seventhTradeHubPollSubscription(array $integration): ?array
{
    if (!seventhTradeHubIsIntegrationOperational($integration)) {
        return null;
    }
    $hubUrl = seventhTradeHubHubUrl();
    if ($hubUrl === '' || !function_exists('curl_init')) {
        return null;
    }

    $integrationId = trim((string)($integration['integration_id'] ?? ''));
    $ch = curl_init($hubUrl . '/api/site-integrations/v1/subscription');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Accept: application/json',
            'X-7TH-Client-Id: ' . trim((string)($integration['client_id'] ?? '')),
            'X-7TH-Client-Secret: ' . seventhTradeHubClientSecret($integration),
            'X-7TH-Integration-Id: ' . $integrationId,
        ],
        CURLOPT_TIMEOUT => 15,
    ]);
    $raw = curl_exec($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($code !== 200 || !is_string($raw)) {
        return null;
    }
    $body = json_decode($raw, true);
    if (!is_array($body)) {
        return null;
    }

    seventhTradeHubApplySubscription($integrationId, $body);
    return $body;
}

/**
 * Save integration row fields (super admin).
 * Upserts the context row so save works even if seed migration has not run yet.
 *
 * @param array<string, mixed> $data
 * @return array{ok: bool, error?: string}
 */
function seventhTradeHubSaveIntegration(string $context, array $data, int $adminId): array
{
    if (!isSuperAdmin($adminId)) {
        return ['ok' => false, 'error' => 'Super administrator access required'];
    }
    if (!in_array($context, [SEVENTH_TRADEHUB_CONTEXT_DEMO, SEVENTH_TRADEHUB_CONTEXT_OWNED], true)) {
        return ['ok' => false, 'error' => 'Invalid context'];
    }

    seventhTradeHubEnsureSchema();
    seventhTradeHubEnsureContextRow($context);

    $enabled = !empty($data['enabled']) ? 1 : 0;
    $integrationId = trim((string)($data['integration_id'] ?? ''));
    $clientId = trim((string)($data['client_id'] ?? ''));
    $expectedUser = trim((string)($data['expected_user_email'] ?? ''));
    $expectedAdmin = trim((string)($data['expected_admin_email'] ?? ''));

    $existing = seventhTradeHubGetByContext($context) ?: [];
    $clientSecretEnc = $existing['client_secret_enc'] ?? null;
    $webhookSecretEnc = $existing['webhook_secret_enc'] ?? null;

    $newSecret = trim((string)($data['client_secret'] ?? ''));
    if ($newSecret !== '') {
        if (!function_exists('encryptData')) {
            return ['ok' => false, 'error' => 'Encryption helper unavailable'];
        }
        $enc = encryptData($newSecret);
        if ($enc === false || $enc === null || $enc === '') {
            return ['ok' => false, 'error' => 'Failed to encrypt client secret'];
        }
        $clientSecretEnc = $enc;
    }

    $newWebhook = trim((string)($data['webhook_secret'] ?? ''));
    if ($newWebhook !== '') {
        if (!function_exists('encryptData')) {
            return ['ok' => false, 'error' => 'Encryption helper unavailable'];
        }
        $enc = encryptData($newWebhook);
        if ($enc === false || $enc === null || $enc === '') {
            return ['ok' => false, 'error' => 'Failed to encrypt webhook secret'];
        }
        $webhookSecretEnc = $enc;
    }

    if ($enabled && ($integrationId === '' || $clientId === '' || trim((string)$clientSecretEnc) === '')) {
        return [
            'ok' => false,
            'error' => 'To enable this integration, provide Integration ID, Client ID, and Client Secret (secret required on first enable).',
        ];
    }

    if ($integrationId !== '') {
        $otherContext = $context === SEVENTH_TRADEHUB_CONTEXT_DEMO
            ? SEVENTH_TRADEHUB_CONTEXT_OWNED
            : SEVENTH_TRADEHUB_CONTEXT_DEMO;
        $other = seventhTradeHubGetByContext($otherContext);
        $otherId = trim((string)($other['integration_id'] ?? ''));
        if ($otherId !== '' && hash_equals($otherId, $integrationId)) {
            return [
                'ok' => false,
                'error' => 'Integration ID is already used by the other context. Demo and Owned must use different UUIDs.',
            ];
        }
    }

    try {
        $db = Database::getInstance();
        $result = $db->query(
            'INSERT INTO seventh_tradehub_integrations
                (context, enabled, integration_id, client_id, client_secret_enc, webhook_secret_enc,
                 expected_user_email, expected_admin_email, updated_at, updated_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)
             ON DUPLICATE KEY UPDATE
                enabled = VALUES(enabled),
                integration_id = VALUES(integration_id),
                client_id = VALUES(client_id),
                client_secret_enc = VALUES(client_secret_enc),
                webhook_secret_enc = VALUES(webhook_secret_enc),
                expected_user_email = VALUES(expected_user_email),
                expected_admin_email = VALUES(expected_admin_email),
                updated_at = NOW(),
                updated_by = VALUES(updated_by)',
            [
                $context,
                $enabled,
                $integrationId !== '' ? $integrationId : null,
                $clientId !== '' ? $clientId : null,
                $clientSecretEnc,
                $webhookSecretEnc,
                $expectedUser !== '' ? $expectedUser : null,
                $expectedAdmin !== '' ? $expectedAdmin : null,
                $adminId,
            ]
        );

        if ($result === false) {
            $pdoErr = method_exists($db, 'errorInfo') ? ($db->errorInfo()[2] ?? '') : '';
            error_log('seventhTradeHubSaveIntegration query failed: ' . $pdoErr);
            return [
                'ok' => false,
                'error' => $pdoErr !== ''
                    ? ('Database error: ' . $pdoErr)
                    : 'Database update failed. Confirm the Hub integration tables exist (open Admin Settings as super admin once to run migrations).',
            ];
        }

        return ['ok' => true];
    } catch (Throwable $e) {
        error_log('seventhTradeHubSaveIntegration: ' . $e->getMessage());
        $msg = $e->getMessage();
        if (stripos($msg, 'Duplicate') !== false || stripos($msg, 'uk_integration_id') !== false) {
            return ['ok' => false, 'error' => 'Integration ID must be unique. Demo and Owned cannot share the same UUID.'];
        }
        if (stripos($msg, "doesn't exist") !== false || stripos($msg, 'Base table') !== false) {
            return ['ok' => false, 'error' => 'Hub tables missing. Open Admin Settings as super admin to run auto-migrations, then try again.'];
        }
        return ['ok' => false, 'error' => 'Save failed: ' . $msg];
    }
}

/**
 * Ensure a context row exists so UPDATE/upsert always has a target.
 */
function seventhTradeHubEnsureContextRow(string $context): void
{
    if (!in_array($context, [SEVENTH_TRADEHUB_CONTEXT_DEMO, SEVENTH_TRADEHUB_CONTEXT_OWNED], true)) {
        return;
    }
    try {
        $db = Database::getInstance();
        $stmt = $db->query(
            'SELECT context FROM seventh_tradehub_integrations WHERE context = ? LIMIT 1',
            [$context]
        );
        if ($stmt && $stmt->fetch()) {
            return;
        }
        $db->query(
            'INSERT INTO seventh_tradehub_integrations (context, enabled, updated_at) VALUES (?, 0, NOW())',
            [$context]
        );
    } catch (Throwable $e) {
        error_log('seventhTradeHubEnsureContextRow: ' . $e->getMessage());
    }
}

/**
 * Mask secret for admin display.
 */
function seventhTradeHubMaskSecret(?string $enc): string
{
    if ($enc === null || trim($enc) === '') {
        return '';
    }
    return '••••••••';
}

/**
 * Webhook ping to Hub.
 *
 * @return array{ok: bool, message: string}
 */
function seventhTradeHubWebhookPing(array $integration): array
{
    if (!seventhTradeHubIsIntegrationOperational($integration)) {
        return ['ok' => false, 'message' => 'Integration not configured or disabled'];
    }
    $hubUrl = seventhTradeHubHubUrl();
    $integrationId = trim((string)($integration['integration_id'] ?? ''));
    $webhookSecret = seventhTradeHubWebhookSecret($integration);
    if ($hubUrl === '' || $integrationId === '' || $webhookSecret === '') {
        return ['ok' => false, 'message' => 'Hub URL, integration ID, and webhook secret required'];
    }
    if (!function_exists('curl_init')) {
        return ['ok' => false, 'message' => 'PHP curl extension is not available'];
    }

    $ch = curl_init($hubUrl . '/webhooks/site-integrations/' . rawurlencode($integrationId));
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json',
            'X-7TH-Webhook-Secret: ' . $webhookSecret,
        ],
        CURLOPT_POSTFIELDS => json_encode(['event' => 'ping']),
        CURLOPT_TIMEOUT => 15,
    ]);
    $raw = curl_exec($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($code >= 200 && $code < 300 && is_string($raw)) {
        $body = json_decode($raw, true);
        if (is_array($body) && ($body['ok'] ?? false) === true) {
            return ['ok' => true, 'message' => 'Webhook ping succeeded'];
        }
    }
    return ['ok' => false, 'message' => 'Webhook ping failed (HTTP ' . $code . ')'];
}

/**
 * Public integration summary for admin UI (no secrets).
 *
 * @return array<string, mixed>
 */
function seventhTradeHubAdminSummary(): array
{
    $hubUrl = seventhTradeHubHubUrl();
    $demo = seventhTradeHubGetByContext(SEVENTH_TRADEHUB_CONTEXT_DEMO);
    $owned = seventhTradeHubGetByContext(SEVENTH_TRADEHUB_CONTEXT_OWNED);

    $ownedSub = null;
    if ($owned && !empty($owned['integration_id'])) {
        $ownedSub = seventhTradeHubGetSubscription((string)$owned['integration_id']);
    }

    return [
        'hub_url' => $hubUrl,
        'endpoints' => [
            'health' => rtrim(SITE_URL, '/') . '/api/7th-tradehub/v1/health',
            'consume' => rtrim(SITE_URL, '/') . '/auth/7th-tradehub/demo/consume',
            'subscription_sync' => rtrim(SITE_URL, '/') . '/api/7th-tradehub/v1/subscription/sync',
        ],
        'demo' => seventhTradeHubFormatIntegrationForAdmin($demo, SEVENTH_TRADEHUB_CONTEXT_DEMO),
        'owned' => seventhTradeHubFormatIntegrationForAdmin($owned, SEVENTH_TRADEHUB_CONTEXT_OWNED, $ownedSub),
        'curl_available' => function_exists('curl_init'),
    ];
}

/**
 * @param array<string, mixed>|null $integration
 * @param array<string, mixed>|null $subscription
 * @return array<string, mixed>
 */
function seventhTradeHubFormatIntegrationForAdmin(?array $integration, string $context, ?array $subscription = null): array
{
    if (!$integration) {
        return ['context' => $context, 'enabled' => false, 'configured' => false];
    }
    return [
        'context' => $context,
        'enabled' => !empty($integration['enabled']),
        'configured' => seventhTradeHubIsIntegrationOperational($integration),
        'integration_id' => trim((string)($integration['integration_id'] ?? '')),
        'client_id' => trim((string)($integration['client_id'] ?? '')),
        'has_client_secret' => trim((string)($integration['client_secret_enc'] ?? '')) !== '',
        'has_webhook_secret' => trim((string)($integration['webhook_secret_enc'] ?? '')) !== '',
        'expected_user_email' => trim((string)($integration['expected_user_email'] ?? '')),
        'expected_admin_email' => trim((string)($integration['expected_admin_email'] ?? '')),
        'capabilities' => seventhTradeHubCapabilitiesForContext($context),
        'readiness' => seventhTradeHubIdentityReadiness($context),
        'subscription' => $subscription,
        'shutdown_active' => $context === SEVENTH_TRADEHUB_CONTEXT_OWNED && seventhTradeHubSubscriptionIsExpired($subscription),
    ];
}
