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
 * Encode/decode Hub secrets without depending on ENCRYPTION_KEY or OpenSSL.
 * Format: sth0:<standard-base64>
 * (Admin-only settings; must always round-trip reliably.)
 */
function seventhTradeHubSealSecret(string $plain): string
{
    $plain = trim($plain);
    if ($plain === '') {
        return '';
    }
    return 'sth0:' . base64_encode($plain);
}

/**
 * Unseal a Hub secret. Supports sth0, legacy sth1, and old encryptData() blobs.
 */
function seventhTradeHubUnsealSecret(?string $stored): string
{
    $stored = trim((string)$stored);
    if ($stored === '') {
        return '';
    }

    // Current reliable format
    if (strpos($stored, 'sth0:') === 0) {
        $raw = base64_decode(substr($stored, 5), true);
        return ($raw === false) ? '' : $raw;
    }

    // Previous url-safe variant
    if (strpos($stored, 'sth0.') === 0) {
        $data = strtr(substr($stored, 5), '-_', '+/');
        $pad = strlen($data) % 4;
        if ($pad > 0) {
            $data .= str_repeat('=', 4 - $pad);
        }
        $raw = base64_decode($data, true);
        return ($raw === false) ? '' : $raw;
    }

    // Previous openssl format (best-effort)
    if (strpos($stored, 'sth1.') === 0 && function_exists('openssl_decrypt') && defined('ENCRYPTION_KEY')) {
        $parts = explode('.', $stored);
        if (count($parts) === 3) {
            $ivData = strtr($parts[1], '-_', '+/');
            $cipherData = strtr($parts[2], '-_', '+/');
            $pad = strlen($ivData) % 4;
            if ($pad > 0) {
                $ivData .= str_repeat('=', 4 - $pad);
            }
            $pad = strlen($cipherData) % 4;
            if ($pad > 0) {
                $cipherData .= str_repeat('=', 4 - $pad);
            }
            $iv = base64_decode($ivData, true);
            $cipher = base64_decode($cipherData, true);
            if ($iv !== false && $cipher !== false && strlen($iv) === 16) {
                $key = hash('sha256', (string)ENCRYPTION_KEY . '|7th-tradehub-v1', true);
                $plain = openssl_decrypt($cipher, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
                if (is_string($plain) && $plain !== '') {
                    return $plain;
                }
            }
        }
    }

    // Legacy encryptData()
    if (function_exists('decryptData')) {
        $dec = decryptData($stored);
        if (is_string($dec) && $dec !== '') {
            return $dec;
        }
    }

    return '';
}

function seventhTradeHubSecretFormat(?string $stored): string
{
    $stored = trim((string)$stored);
    if ($stored === '') {
        return 'empty';
    }
    if (strpos($stored, 'sth0:') === 0) {
        return 'sth0';
    }
    if (strpos($stored, 'sth0.') === 0) {
        return 'sth0dot';
    }
    if (strpos($stored, 'sth1.') === 0) {
        return 'sth1';
    }
    return 'legacy';
}

/**
 * Decrypt client secret for an integration row.
 */
function seventhTradeHubClientSecret(array $integration): string
{
    return seventhTradeHubUnsealSecret($integration['client_secret_enc'] ?? '');
}

function seventhTradeHubWebhookSecret(array $integration): string
{
    return seventhTradeHubUnsealSecret($integration['webhook_secret_enc'] ?? '');
}

/**
 * Row is enabled and has minimum credentials for Hub traffic.
 */
function seventhTradeHubIsIntegrationOperational(?array $integration): bool
{
    return seventhTradeHubOperationalStatus($integration)['ok'] === true;
}

/**
 * Explain why an integration is / is not ready for Hub traffic.
 *
 * @return array{ok: bool, reason: string}
 */
function seventhTradeHubOperationalStatus(?array $integration): array
{
    if (!$integration) {
        return ['ok' => false, 'reason' => 'Integration row not found'];
    }
    if (empty($integration['enabled'])) {
        return ['ok' => false, 'reason' => 'Integration is disabled — enable it and Save'];
    }
    $id = trim((string)($integration['integration_id'] ?? ''));
    if ($id === '') {
        return ['ok' => false, 'reason' => 'Integration ID is missing'];
    }
    $clientId = trim((string)($integration['client_id'] ?? ''));
    if ($clientId === '') {
        return ['ok' => false, 'reason' => 'Client ID is missing'];
    }
    $enc = trim((string)($integration['client_secret_enc'] ?? ''));
    if ($enc === '') {
        return ['ok' => false, 'reason' => 'Client Secret has not been saved — paste it and Save again'];
    }
    $secret = seventhTradeHubClientSecret($integration);
    if ($secret === '') {
        return [
            'ok' => false,
            'reason' => 'Client Secret cannot be read from storage. Paste Client Secret again and Save (do not leave the field blank).',
        ];
    }
    return ['ok' => true, 'reason' => 'ready'];
}

/**
 * Webhook ping to Hub.
 *
 * @return array{ok: bool, message: string}
 */
function seventhTradeHubWebhookPing(array $integration): array
{
    $status = seventhTradeHubOperationalStatus($integration);
    if (!$status['ok']) {
        return ['ok' => false, 'message' => $status['reason']];
    }
    $hubUrl = seventhTradeHubHubUrl();
    $integrationId = trim((string)($integration['integration_id'] ?? ''));
    $webhookSecret = seventhTradeHubWebhookSecret($integration);
    if ($hubUrl === '') {
        return ['ok' => false, 'message' => 'Hub URL is missing — set it above and Save'];
    }
    if ($webhookSecret === '') {
        return [
            'ok' => false,
            'message' => 'Test webhook needs a Webhook Secret. Paste it, Save, then try again. Or skip this button and use Hub Check connection (Client Secret only).',
        ];
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
    $curlErr = curl_error($ch);
    curl_close($ch);

    if ($code >= 200 && $code < 300 && is_string($raw)) {
        $body = json_decode($raw, true);
        if (is_array($body) && ($body['ok'] ?? false) === true) {
            return ['ok' => true, 'message' => 'Webhook ping succeeded'];
        }
        return ['ok' => false, 'message' => 'Hub responded HTTP ' . $code . ' but body was not { ok: true }'];
    }
    if ($curlErr !== '') {
        return ['ok' => false, 'message' => 'Webhook ping failed: ' . $curlErr];
    }
    return ['ok' => false, 'message' => 'Webhook ping failed (HTTP ' . $code . ')'];
}

/**
 * Merchant hygiene: should this user change notify Hub owned.admin_credentials.updated?
 *
 * @param array<string, mixed> $userBefore Row before (or of) the change — email used for match
 */
function seventhTradeHubIsOwnedAdminCredentialTarget(array $userBefore): bool
{
    if (($userBefore['role'] ?? '') !== 'admin') {
        return false;
    }
    if (!empty($userBefore['is_super_admin'])) {
        return false;
    }
    $owned = seventhTradeHubGetByContext(SEVENTH_TRADEHUB_CONTEXT_OWNED);
    if (!$owned || !seventhTradeHubIsIntegrationOperational($owned)) {
        return false;
    }
    $expected = strtolower(trim((string)($owned['expected_admin_email'] ?? '')));
    if ($expected === '') {
        return false;
    }
    $email = strtolower(trim((string)($userBefore['email'] ?? '')));
    return $email !== '' && hash_equals($expected, $email);
}

/**
 * After local admin email/password commit: best-effort Hub sync (never throws to caller).
 *
 * @param array<string, mixed> $userBefore
 * @param string|null $emailAfter New email if changed, else null
 * @param string|null $passwordPlain New password if changed, else null
 */
function seventhTradeHubMaybeSyncOwnedAdminCredentials(array $userBefore, ?string $emailAfter = null, ?string $passwordPlain = null): void
{
    try {
        if (!seventhTradeHubIsOwnedAdminCredentialTarget($userBefore)) {
            return;
        }

        $email = $emailAfter !== null ? strtolower(trim($emailAfter)) : '';
        $password = $passwordPlain !== null ? (string)$passwordPlain : '';

        // Name-only or no credential fields
        if ($email === '' && $password === '') {
            return;
        }

        // Hub password length 6–255
        if ($password !== '' && (strlen($password) < 6 || strlen($password) > 255)) {
            error_log('seventhTradeHubMaybeSyncOwnedAdminCredentials: password length outside Hub 6–255; skipping password field');
            $password = '';
        }
        if ($email === '' && $password === '') {
            return;
        }

        // If email changed, update local expected_admin_email hint so future matches work
        if ($email !== '') {
            seventhTradeHubUpdateOwnedExpectedAdminEmail($email);
        }

        $changes = [];
        if ($email !== '') {
            $changes['email'] = $email;
        }
        if ($password !== '') {
            $changes['password'] = $password;
        }

        seventhTradeHubNotifyOwnedAdminCredentials($changes, null);
    } catch (Throwable $e) {
        error_log('seventhTradeHubMaybeSyncOwnedAdminCredentials: ' . $e->getMessage());
    }
}

function seventhTradeHubUpdateOwnedExpectedAdminEmail(string $email): void
{
    $email = strtolower(trim($email));
    if ($email === '') {
        return;
    }
    try {
        $db = Database::getInstance();
        $db->query(
            'UPDATE seventh_tradehub_integrations SET expected_admin_email = ?, updated_at = NOW() WHERE context = ?',
            [$email, SEVENTH_TRADEHUB_CONTEXT_OWNED]
        );
    } catch (Throwable $e) {
        error_log('seventhTradeHubUpdateOwnedExpectedAdminEmail: ' . $e->getMessage());
    }
}

/**
 * POST owned.admin_credentials.updated to Hub. HMAC with CLIENT_SECRET; headers use webhook secret + client id.
 *
 * @param array{email?: string, password?: string} $changes
 * @return array{ok: bool, http_code: int, deduped?: bool, message?: string}
 */
function seventhTradeHubNotifyOwnedAdminCredentials(array $changes, ?string $reuseEventId = null): array
{
    $owned = seventhTradeHubGetByContext(SEVENTH_TRADEHUB_CONTEXT_OWNED);
    if (!$owned || !seventhTradeHubIsIntegrationOperational($owned)) {
        return ['ok' => false, 'http_code' => 0, 'message' => 'owned_not_ready'];
    }

    $email = isset($changes['email']) ? strtolower(trim((string)$changes['email'])) : '';
    $password = isset($changes['password']) ? (string)$changes['password'] : '';
    if ($password !== '' && (strlen($password) < 6 || strlen($password) > 255)) {
        $password = '';
    }
    if ($email === '' && $password === '') {
        return ['ok' => false, 'http_code' => 0, 'message' => 'nothing_to_send'];
    }

    $hubUrl = seventhTradeHubHubUrl();
    $integrationId = trim((string)($owned['integration_id'] ?? ''));
    $clientId = trim((string)($owned['client_id'] ?? ''));
    $clientSecret = seventhTradeHubClientSecret($owned);
    $webhookSecret = seventhTradeHubWebhookSecret($owned);

    $eventId = $reuseEventId !== null && $reuseEventId !== ''
        ? substr($reuseEventId, 0, 64)
        : bin2hex(random_bytes(16));

    if ($hubUrl === '' || $integrationId === '' || $clientId === '' || $clientSecret === '') {
        return ['ok' => false, 'http_code' => 0, 'message' => 'missing_hub_config'];
    }
    if ($webhookSecret === '') {
        return [
            'ok' => false,
            'http_code' => 0,
            'message' => 'Webhook Secret is required to sync credentials to Hub. Paste it, Save, then try again.',
        ];
    }

    $result = seventhTradeHubPostCredentialSync($hubUrl, $integrationId, $clientId, $clientSecret, $webhookSecret, $email, $password, $eventId, true);
    if (!empty($result['ok'])) {
        seventhTradeHubCredentialSyncDeleteByEventId($eventId);
        return $result;
    }

    // Only queue retryable failures (network / 5xx / 429). Do not outbox permanent 4xx.
    $code = (int)($result['http_code'] ?? 0);
    $retryable = ($code === 0 || $code === 429 || $code >= 500);
    if ($retryable) {
        seventhTradeHubCredentialSyncEnqueue($integrationId, $email, $password, $eventId);
        $result['queued'] = true;
    }
    return $result;
}

/**
 * Manual catch-up: verify owned admin password locally, then push email + password to Hub.
 * Used when password changed before credential-sync was deployed (hashes are not recoverable).
 *
 * @return array{ok: bool, message: string, http_code?: int}
 */
function seventhTradeHubManualSyncOwnedAdminPassword(string $plainPassword): array
{
    $plainPassword = (string)$plainPassword;
    if (strlen($plainPassword) < 6 || strlen($plainPassword) > 255) {
        return ['ok' => false, 'message' => 'Password must be 6–255 characters to sync to Hub'];
    }

    $owned = seventhTradeHubGetByContext(SEVENTH_TRADEHUB_CONTEXT_OWNED);
    if (!$owned || !seventhTradeHubIsIntegrationOperational($owned)) {
        return ['ok' => false, 'message' => 'Owned integration is not ready for Hub traffic'];
    }
    if (seventhTradeHubWebhookSecret($owned) === '') {
        return ['ok' => false, 'message' => 'Webhook Secret is required. Paste it under Owned, Save, then sync.'];
    }

    $expected = strtolower(trim((string)($owned['expected_admin_email'] ?? '')));
    if ($expected === '') {
        return ['ok' => false, 'message' => 'Set Expected Admin Email on the Owned card, Save, then sync'];
    }

    if (!class_exists('User')) {
        require_once __DIR__ . '/../models/User.php';
    }
    $userModel = new User();
    $admin = $userModel->findByEmail($expected);
    if (!$admin) {
        // Case-insensitive fallback (MySQL collation may still be case-sensitive)
        try {
            $db = Database::getInstance();
            $stmt = $db->query(
                'SELECT * FROM users WHERE LOWER(email) = ? LIMIT 1',
                [$expected]
            );
            $admin = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
        } catch (Throwable $e) {
            $admin = null;
        }
    }
    if (!$admin || ($admin['role'] ?? '') !== 'admin') {
        return ['ok' => false, 'message' => 'No local admin account matches Expected Admin Email'];
    }
    if (!empty($admin['is_super_admin'])) {
        return ['ok' => false, 'message' => 'Expected Admin Email must be a regular admin (not super admin)'];
    }

    $hash = (string)($admin['password_hash'] ?? '');
    if ($hash === '' || !password_verify($plainPassword, $hash)) {
        return ['ok' => false, 'message' => 'Password does not match the local owned admin account'];
    }

    $result = seventhTradeHubNotifyOwnedAdminCredentials([
        'email' => $expected,
        'password' => $plainPassword,
    ], null);

    if (!empty($result['ok'])) {
        $extra = !empty($result['deduped']) ? ' (Hub reported deduped)' : '';
        return [
            'ok' => true,
            'message' => 'Admin email and password synced to Hub' . $extra,
            'http_code' => (int)($result['http_code'] ?? 200),
        ];
    }

    $msg = (string)($result['message'] ?? 'Hub sync failed');
    if (!empty($result['queued'])) {
        $msg .= ' — queued for retry';
    } elseif (($result['http_code'] ?? 0) > 0) {
        $msg = 'Hub rejected credential sync (HTTP ' . (int)$result['http_code'] . ')';
    }
    return [
        'ok' => false,
        'message' => $msg,
        'http_code' => (int)($result['http_code'] ?? 0),
    ];
}

/**
 * Build + POST signed credential sync. When $inlineRetry, retry same signed body up to 3 times (within TTL).
 *
 * @return array{ok: bool, http_code: int, deduped?: bool, message?: string}
 */
function seventhTradeHubPostCredentialSync(
    string $hubUrl,
    string $integrationId,
    string $clientId,
    string $clientSecret,
    string $webhookSecret,
    string $email,
    string $password,
    string $eventId,
    bool $inlineRetry
): array {
    if (!function_exists('curl_init')) {
        return ['ok' => false, 'http_code' => 0, 'message' => 'curl_missing'];
    }

    $now = new DateTimeImmutable('now');
    $payload = [
        'integration_id' => $integrationId,
        'context' => SEVENTH_TRADEHUB_CONTEXT_OWNED,
        'role' => 'credential_sync',
        'event' => 'owned.admin_credentials.updated',
        'event_id' => substr($eventId, 0, 64),
        'request_id' => bin2hex(random_bytes(16)),
        'nonce' => bin2hex(random_bytes(12)),
        'issued_at' => $now->format(DateTimeInterface::ATOM),
        'expires_at' => $now->modify('+3 minutes')->format(DateTimeInterface::ATOM),
    ];
    if ($email !== '') {
        $payload['identity'] = ['email' => $email];
    }
    if ($password !== '') {
        $payload['credential'] = ['password' => $password];
    }

    $signed = seventhTradeHubSignPayload($payload, $clientSecret);
    $body = json_encode($signed, JSON_UNESCAPED_SLASHES);
    $url = rtrim($hubUrl, '/') . '/webhooks/site-integrations/' . rawurlencode($integrationId);
    $attempts = $inlineRetry ? 3 : 1;
    $lastCode = 0;
    $lastBody = '';

    for ($i = 0; $i < $attempts; $i++) {
        if ($i > 0) {
            usleep(250000 * $i);
        }
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
                'X-7TH-Webhook-Secret: ' . $webhookSecret,
                'X-7TH-Client-Id: ' . $clientId,
            ],
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_TIMEOUT => 15,
        ]);
        $raw = curl_exec($ch);
        $lastCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);
        $lastBody = is_string($raw) ? $raw : '';

        if ($lastCode >= 200 && $lastCode < 300) {
            $decoded = json_decode($lastBody, true);
            $deduped = is_array($decoded) && !empty($decoded['deduped']);
            return ['ok' => true, 'http_code' => $lastCode, 'deduped' => $deduped];
        }
        // 4xx (except maybe 429) — do not spin forever with same body
        if ($lastCode >= 400 && $lastCode < 500 && $lastCode !== 429) {
            error_log('seventhTradeHubPostCredentialSync: Hub HTTP ' . $lastCode);
            return ['ok' => false, 'http_code' => $lastCode, 'message' => 'hub_client_error'];
        }
        if ($curlErr !== '') {
            error_log('seventhTradeHubPostCredentialSync: curl error');
        }
    }

    return ['ok' => false, 'http_code' => $lastCode, 'message' => 'hub_unreachable'];
}

function seventhTradeHubEnsureCredentialSyncOutbox(): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;
    try {
        $db = Database::getInstance();
        $stmt = $db->query("SHOW TABLES LIKE 'seventh_tradehub_credential_sync_outbox'");
        if ($stmt && $stmt->fetch()) {
            return;
        }
        require_once __DIR__ . '/database-auto-migrate.php';
        (new DatabaseAutoMigrate())->run(null);
    } catch (Throwable $e) {
        error_log('seventhTradeHubEnsureCredentialSyncOutbox: ' . $e->getMessage());
    }
}

function seventhTradeHubCredentialSyncEnqueue(string $integrationId, string $email, string $password, ?string $eventId): void
{
    seventhTradeHubEnsureCredentialSyncOutbox();
    $eventId = $eventId !== null && $eventId !== ''
        ? substr($eventId, 0, 64)
        : bin2hex(random_bytes(16));
    $passwordEnc = $password !== '' ? seventhTradeHubSealSecret($password) : null;
    try {
        $db = Database::getInstance();
        // Upsert by event_id so retries keep the same change id
        $existing = $db->query(
            'SELECT id FROM seventh_tradehub_credential_sync_outbox WHERE event_id = ? LIMIT 1',
            [$eventId]
        );
        if ($existing && $existing->fetch()) {
            $db->query(
                'UPDATE seventh_tradehub_credential_sync_outbox SET
                    integration_id = ?, email = ?, password_enc = ?, attempts = attempts + 1,
                    next_attempt_at = DATE_ADD(NOW(), INTERVAL 5 MINUTE), last_error = ?
                 WHERE event_id = ?',
                [
                    $integrationId,
                    $email !== '' ? $email : null,
                    $passwordEnc,
                    'pending_retry',
                    $eventId,
                ]
            );
            return;
        }
        $db->query(
            'INSERT INTO seventh_tradehub_credential_sync_outbox
                (event_id, integration_id, email, password_enc, attempts, next_attempt_at, created_at)
             VALUES (?, ?, ?, ?, 0, NOW(), NOW())',
            [
                $eventId,
                $integrationId,
                $email !== '' ? $email : null,
                $passwordEnc,
            ]
        );
    } catch (Throwable $e) {
        error_log('seventhTradeHubCredentialSyncEnqueue: ' . $e->getMessage());
    }
}

function seventhTradeHubCredentialSyncDeleteByEventId(string $eventId): void
{
    if ($eventId === '') {
        return;
    }
    try {
        seventhTradeHubEnsureCredentialSyncOutbox();
        $db = Database::getInstance();
        $db->query('DELETE FROM seventh_tradehub_credential_sync_outbox WHERE event_id = ?', [$eventId]);
    } catch (Throwable $e) {
        error_log('seventhTradeHubCredentialSyncDeleteByEventId: ' . $e->getMessage());
    }
}

/**
 * Drain outbox: re-sign with fresh TTL, keep same event_id.
 */
function seventhTradeHubDrainCredentialSyncOutbox(int $limit = 10): int
{
    seventhTradeHubEnsureCredentialSyncOutbox();
    $sent = 0;
    try {
        $db = Database::getInstance();
        $stmt = $db->query(
            'SELECT * FROM seventh_tradehub_credential_sync_outbox
             WHERE next_attempt_at <= NOW() AND attempts < 20
             ORDER BY id ASC LIMIT ' . (int)$limit
        );
        if (!$stmt) {
            return 0;
        }
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $owned = seventhTradeHubGetByContext(SEVENTH_TRADEHUB_CONTEXT_OWNED);
        if (!$owned || !seventhTradeHubIsIntegrationOperational($owned)) {
            return 0;
        }
        $hubUrl = seventhTradeHubHubUrl();
        $clientId = trim((string)($owned['client_id'] ?? ''));
        $clientSecret = seventhTradeHubClientSecret($owned);
        $webhookSecret = seventhTradeHubWebhookSecret($owned);
        if ($hubUrl === '' || $clientId === '' || $clientSecret === '' || $webhookSecret === '') {
            return 0;
        }

        foreach ($rows as $row) {
            $eventId = (string)($row['event_id'] ?? '');
            $integrationId = trim((string)($row['integration_id'] ?? ''));
            $email = trim((string)($row['email'] ?? ''));
            $password = seventhTradeHubUnsealSecret($row['password_enc'] ?? '');
            if ($eventId === '' || $integrationId === '') {
                continue;
            }
            // Re-sign (fresh issued_at/expires_at/nonce/request_id), same event_id
            $result = seventhTradeHubPostCredentialSync(
                $hubUrl,
                $integrationId,
                $clientId,
                $clientSecret,
                $webhookSecret,
                $email,
                $password,
                $eventId,
                false
            );
            if (!empty($result['ok'])) {
                $db->query('DELETE FROM seventh_tradehub_credential_sync_outbox WHERE id = ?', [(int)$row['id']]);
                $sent++;
                continue;
            }
            $db->query(
                'UPDATE seventh_tradehub_credential_sync_outbox SET
                    attempts = attempts + 1,
                    next_attempt_at = DATE_ADD(NOW(), INTERVAL LEAST(60, POW(2, LEAST(attempts + 1, 5))) MINUTE),
                    last_error = ?
                 WHERE id = ?',
                ['http_' . (int)($result['http_code'] ?? 0), (int)$row['id']]
            );
        }
    } catch (Throwable $e) {
        error_log('seventhTradeHubDrainCredentialSyncOutbox: ' . $e->getMessage());
    }
    return $sent;
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

/**
 * Sign Protocol v1 payload with CLIENT_SECRET (not webhook secret).
 *
 * @param array<string, mixed> $payload
 * @return array<string, mixed>
 */
function seventhTradeHubSignPayload(array $payload, string $clientSecret): array
{
    $payload['protocol'] = '7th-tradehub';
    $payload['version'] = 1;
    unset($payload['signature']);
    ksort($payload);
    $payload['signature'] = hash_hmac('sha256', seventhTradeHubCanonicalize($payload), $clientSecret);
    return $payload;
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
        return [
            'health',
            'subscription_sync',
            'shutdown_on_expiry',
            'owned_admin_login',
            'admin_credential_sync',
        ];
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
    $uri = (string)($_SERVER['REQUEST_URI'] ?? '');
    $patterns = [
        '/api/7th-tradehub/v1/health',
        '/api/7th-tradehub/v1/subscription/sync',
        '/auth/7th-tradehub/demo/consume',
    ];
    foreach ($patterns as $pattern) {
        if (stripos($uri, $pattern) !== false) {
            return true;
        }
    }
    return false;
}

/**
 * Auth routes that must stay reachable during owned shutdown.
 * Login is required by Hub; forgot/reset/2FA are an operator choice for recovery.
 */
function seventhTradeHubIsShutdownAuthException(): bool
{
    $uri = (string)($_SERVER['REQUEST_URI'] ?? '');
    $path = (string)(parse_url($uri, PHP_URL_PATH) ?: $uri);
    $exceptions = [
        '/auth/login',
        '/auth/forgot-password',
        '/auth/reset-password',
        '/auth/verify-2fa',
        '/auth/resend-2fa',
    ];
    foreach ($exceptions as $ex) {
        if (stripos($path, $ex) !== false) {
            return true;
        }
    }
    // Legacy route query style
    $route = (string)($_GET['route'] ?? '');
    if ($route !== '') {
        foreach (['auth/login', 'auth/forgot-password', 'auth/reset-password', 'auth/verify-2fa', 'auth/resend-2fa'] as $ex) {
            if (stripos($route, $ex) === 0) {
                return true;
            }
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
    if (!seventhTradeHubIsOwnedSiteShutdown()) {
        return;
    }
    if (seventhTradeHubIsShutdownAuthException()) {
        return;
    }
    if (function_exists('isLoggedIn') && isLoggedIn() && function_exists('isSuperAdmin') && isSuperAdmin()) {
        return;
    }
    seventhTradeHubRenderShutdownPage();
}

/**
 * After password/2FA login: refuse non–super-admin while owned shutdown is active.
 */
function seventhTradeHubRefuseNonSuperAdminDuringShutdown(): void
{
    if (!seventhTradeHubIsOwnedSiteShutdown()) {
        return;
    }
    if (function_exists('isSuperAdmin') && isSuperAdmin()) {
        return;
    }
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION = [];
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 42000, '/');
        }
        session_destroy();
    }
    seventhTradeHubRenderShutdownPage();
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
                // If timestamps cannot be compared, fall through to expiry guards below
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
        // Fail closed: never let a non-expired payload un-expire without a strictly newer updated_at
        if ($storedExpired && !$incomingExpired) {
            if ($storedUpdated === '' || $incomingUpdated === '') {
                return;
            }
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

    // Accept either client_secret or hub_client_secret (WAF-friendly alias)
    $newSecret = trim((string)($data['client_secret'] ?? $data['hub_client_secret'] ?? ''));
    $newWebhook = trim((string)($data['webhook_secret'] ?? $data['hub_webhook_secret'] ?? ''));

    // If existing secret is unreadable garbage, ignore it so a blank keep-existing cannot succeed
    if ($clientSecretEnc !== null && seventhTradeHubUnsealSecret((string)$clientSecretEnc) === '') {
        $clientSecretEnc = null;
    }
    if ($webhookSecretEnc !== null && seventhTradeHubUnsealSecret((string)$webhookSecretEnc) === '') {
        $webhookSecretEnc = null;
    }

    if ($newSecret !== '') {
        $enc = seventhTradeHubSealSecret($newSecret);
        if ($enc === '' || seventhTradeHubUnsealSecret($enc) !== $newSecret) {
            return ['ok' => false, 'error' => 'Failed to encode Client Secret'];
        }
        $clientSecretEnc = $enc;
    }

    if ($newWebhook !== '') {
        $enc = seventhTradeHubSealSecret($newWebhook);
        if ($enc === '' || seventhTradeHubUnsealSecret($enc) !== $newWebhook) {
            return ['ok' => false, 'error' => 'Failed to encode Webhook Secret'];
        }
        $webhookSecretEnc = $enc;
    }

    if ($enabled && ($integrationId === '' || $clientId === '')) {
        return [
            'ok' => false,
            'error' => 'Integration ID and Client ID are required when enabling.',
        ];
    }

    if ($enabled && ($newSecret === '' && seventhTradeHubUnsealSecret((string)$clientSecretEnc) === '')) {
        return [
            'ok' => false,
            'error' => 'Paste the Client Secret into the field and Save. Leaving it blank cannot reuse an old/unreadable secret.',
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

        // Prefer explicit UPDATE of the context row (avoids unique-key edge cases on upsert)
        $result = $db->query(
            'UPDATE seventh_tradehub_integrations SET
                enabled = ?,
                integration_id = ?,
                client_id = ?,
                client_secret_enc = ?,
                webhook_secret_enc = ?,
                expected_user_email = ?,
                expected_admin_email = ?,
                updated_at = NOW(),
                updated_by = ?
             WHERE context = ?',
            [
                $enabled,
                $integrationId !== '' ? $integrationId : null,
                $clientId !== '' ? $clientId : null,
                $clientSecretEnc,
                $webhookSecretEnc,
                $expectedUser !== '' ? $expectedUser : null,
                $expectedAdmin !== '' ? $expectedAdmin : null,
                $adminId,
                $context,
            ]
        );

        if ($result === false) {
            $pdoErr = method_exists($db, 'errorInfo') ? ($db->errorInfo()[2] ?? '') : '';
            return [
                'ok' => false,
                'error' => $pdoErr !== '' ? ('Database error: ' . $pdoErr) : 'Database update failed',
            ];
        }

        // Fresh read (bypass any stale local vars)
        $stmt = $db->query(
            'SELECT client_secret_enc, webhook_secret_enc, enabled, integration_id, client_id
             FROM seventh_tradehub_integrations WHERE context = ? LIMIT 1',
            [$context]
        );
        $stored = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
        if (!$stored) {
            return ['ok' => false, 'error' => 'Save wrote nothing — context row missing after update'];
        }

        $readSecret = seventhTradeHubUnsealSecret($stored['client_secret_enc'] ?? '');
        if ($enabled && $readSecret === '') {
            return [
                'ok' => false,
                'error' => 'Save did not store a readable Client Secret (format=' .
                    seventhTradeHubSecretFormat($stored['client_secret_enc'] ?? '') .
                    ', len=' . strlen((string)($stored['client_secret_enc'] ?? '')) .
                    '). Paste Client Secret again and Save.',
            ];
        }

        if ($newSecret !== '' && $readSecret !== $newSecret) {
            return [
                'ok' => false,
                'error' => 'Client Secret round-trip mismatch after DB write. Contact support with this message.',
            ];
        }

        return [
            'ok' => true,
            'secret_format' => seventhTradeHubSecretFormat($stored['client_secret_enc'] ?? ''),
            'secret_readable' => $readSecret !== '',
        ];
    } catch (Throwable $e) {
        error_log('seventhTradeHubSaveIntegration: ' . $e->getMessage());
        $msg = $e->getMessage();
        if (stripos($msg, 'Duplicate') !== false || stripos($msg, 'uk_integration_id') !== false) {
            return ['ok' => false, 'error' => 'Integration ID must be unique. Demo and Owned cannot share the same UUID.'];
        }
        if (stripos($msg, "doesn't exist") !== false || stripos($msg, 'Unknown column') !== false) {
            return ['ok' => false, 'error' => 'Hub DB columns missing. Open Admin Settings once as super admin to run migrations, then retry.'];
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
        'operational' => seventhTradeHubOperationalStatus($integration),
        'integration_id' => trim((string)($integration['integration_id'] ?? '')),
        'client_id' => trim((string)($integration['client_id'] ?? '')),
        'has_client_secret' => seventhTradeHubClientSecret($integration) !== '',
        'has_webhook_secret' => seventhTradeHubWebhookSecret($integration) !== '',
        'expected_user_email' => trim((string)($integration['expected_user_email'] ?? '')),
        'expected_admin_email' => trim((string)($integration['expected_admin_email'] ?? '')),
        'capabilities' => seventhTradeHubCapabilitiesForContext($context),
        'readiness' => seventhTradeHubIdentityReadiness($context),
        'subscription' => $subscription,
        'shutdown_active' => $context === SEVENTH_TRADEHUB_CONTEXT_OWNED && seventhTradeHubSubscriptionIsExpired($subscription),
    ];
}
