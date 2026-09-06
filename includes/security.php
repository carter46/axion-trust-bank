<?php
/**
 * Security Class
 */
class Security {
    
    public static function initialize() {
        // Regenerate session ID periodically
        if (!isset($_SESSION['last_regeneration'])) {
            $_SESSION['last_regeneration'] = time();
        } elseif (time() - $_SESSION['last_regeneration'] > 300) {
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        }

        // Login POST must not be blocked by a stale authenticated cookie session
        if (self::isAuthLoginPostRequest()) {
            foreach (['user_id', 'user_email', 'user_name', 'user_role', 'user_photo', 'last_activity', 'session_started_at', 'session_domain', 'restricted_status', 'admin_impersonating', 'admin_original_id', 'admin_original_email', 'admin_original_name', 'admin_original_role', 'admin_original_photo', 'admin_original_is_super_admin', 'is_super_admin', 'is_demo_user'] as $authKey) {
                unset($_SESSION[$authKey]);
            }
            return;
        }
        
        // Only check session timeout for logged-in users
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            return;
        }

        // Idle timeout — same rules for admin and user sessions
        if (!isset($_SESSION['last_activity'])) {
            $_SESSION['last_activity'] = time();
        } elseif ((time() - (int)$_SESSION['last_activity']) > SESSION_LIFETIME) {
            destroyAuthSession('Your session expired due to inactivity. Please log in again.');
        }

        // CRITICAL SECURITY: Verify user account still exists on every request
        try {
            $db = Database::getInstance();
            $sql = "SELECT id, status FROM users WHERE id = ? LIMIT 1";
            $stmt = $db->query($sql, [$_SESSION['user_id']]);
            $user = $stmt ? $stmt->fetch() : null;
            
            if (!$user) {
                destroyAuthSession('Your account has been deleted or is no longer active.');
            }
        } catch (Exception $e) {
            error_log("Security::initialize() - User validation error: " . $e->getMessage());
        }

        $_SESSION['last_activity'] = time();
        self::refreshSessionCookie();
    }

    private static function isAuthLoginPostRequest() {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            return false;
        }
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
        if (strpos($path, '/auth/login') !== false) {
            return true;
        }
        return isset($_GET['route']) && $_GET['route'] === 'auth/login';
    }

    /**
     * Keep the session cookie alive while the user is active (sliding idle window).
     */
    private static function refreshSessionCookie() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return;
        }

        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (!empty($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
            || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

        if (PHP_VERSION_ID >= 70300) {
            setcookie(session_name(), session_id(), [
                'expires' => time() + SESSION_LIFETIME,
                'path' => '/',
                'secure' => $secure,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        } else {
            setcookie(session_name(), session_id(), time() + SESSION_LIFETIME, '/; samesite=Lax', '', $secure, true);
        }
    }
    
    public static function sanitize($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = self::sanitize($value);
            }
        } else {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        }
        return $data;
    }
    
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    
    public static function validatePassword($password) {
        if (strlen($password) < 8) {
            return false;
        }
        
        $hasUppercase = preg_match('/[A-Z]/', $password);
        $hasLowercase = preg_match('/[a-z]/', $password);
        $hasNumber = preg_match('/[0-9]/', $password);
        
        return $hasUppercase && $hasLowercase && $hasNumber;
    }
    
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }
    
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    public static function validateCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    public static function checkLoginAttempts($email) {
        $db = Database::getInstance();
        
        $settingsSql = "SELECT setting_value FROM system_settings WHERE setting_key = 'max_login_attempts'";
        $settingsStmt = $db->query($settingsSql);
        $settings = $settingsStmt ? $settingsStmt->fetch() : null;
        $maxAttempts = $settings ? intval($settings['setting_value']) : MAX_LOGIN_ATTEMPTS;
        
        $settingsSql = "SELECT setting_value FROM system_settings WHERE setting_key = 'login_lockout_duration'";
        $settingsStmt = $db->query($settingsSql);
        $settings = $settingsStmt ? $settingsStmt->fetch() : null;
        $lockoutDuration = $settings ? intval($settings['setting_value']) * 60 : LOCKOUT_TIME;
        
        $sql = "SELECT COUNT(*) as attempts FROM login_attempts 
                WHERE email = ? AND success = 0 AND attempted_at > DATE_SUB(NOW(), INTERVAL ? SECOND)";
        $stmt = $db->query($sql, [$email, $lockoutDuration]);
        if (!$stmt) {
            return false;
        }
        $result = $stmt->fetch();
        
        return ($result['attempts'] ?? 0) >= $maxAttempts;
    }
    
    public static function recordLoginAttempt($email, $success) {
        $db = Database::getInstance();
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
        $sql = "INSERT INTO login_attempts (email, ip_address, success, attempted_at) VALUES (?, ?, ?, NOW())";
        $db->query($sql, [$email, $ip, $success ? 1 : 0]);
    }
    
    public static function clearLoginAttempts($email) {
        $db = Database::getInstance();
        $sql = "DELETE FROM login_attempts WHERE email = ?";
        $db->query($sql, [$email]);
    }

    public static function generate2FACode($userId, $method = 'email', $purpose = 'login') {
        $db = Database::getInstance();
        $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $purpose = preg_replace('/[^a-z0-9_\-]/i', '', (string)$purpose) ?: 'login';
        $method = strtolower(trim((string)$method));
        if (!in_array($method, ['email', 'sms', 'app'], true)) {
            $method = 'email';
        }

        // Invalidate prior unused codes for this purpose
        $invalidate = $db->query(
            "UPDATE two_factor_codes SET used = 1 WHERE user_id = ? AND purpose = ? AND used = 0",
            [$userId, $purpose]
        );
        if ($invalidate === false) {
            // Older schemas without purpose column
            $db->query(
                "UPDATE two_factor_codes SET used = 1 WHERE user_id = ? AND used = 0",
                [$userId]
            );
        }

        // Expiry must use MySQL NOW() so PHP/MySQL timezone skew cannot invalidate fresh codes
        $inserted = $db->query(
            "INSERT INTO two_factor_codes (user_id, code, method, used, expires_at, purpose)
             VALUES (?, ?, ?, 0, DATE_ADD(NOW(), INTERVAL 10 MINUTE), ?)",
            [$userId, $code, $method, $purpose]
        );

        if ($inserted === false) {
            $inserted = $db->query(
                "INSERT INTO two_factor_codes (user_id, code, method, used, expires_at)
                 VALUES (?, ?, ?, 0, DATE_ADD(NOW(), INTERVAL 10 MINUTE))",
                [$userId, $code, $method]
            );
        }

        if ($inserted === false) {
            error_log("generate2FACode failed to store code for user_id={$userId} purpose={$purpose}");
            return false;
        }

        return $code;
    }

    public static function validate2FA($userId, $code, $purpose = 'login') {
        $db = Database::getInstance();
        // Digits only — ignore spaces/dashes pasted from email clients
        $code = preg_replace('/\D+/', '', (string)$code);
        $purpose = preg_replace('/[^a-z0-9_\-]/i', '', (string)$purpose) ?: 'login';
        if ($code === '' || strlen($code) < 4) {
            return false;
        }

        $sql = "SELECT id FROM two_factor_codes
                WHERE user_id = ? AND code = ? AND purpose = ? AND used = 0 AND expires_at > NOW()
                ORDER BY id DESC LIMIT 1";
        $stmt = $db->query($sql, [$userId, $code, $purpose]);
        $row = $stmt ? $stmt->fetch() : null;

        // Fallback for installs without purpose column
        if (!$row) {
            $stmt = $db->query(
                "SELECT id FROM two_factor_codes
                 WHERE user_id = ? AND code = ? AND used = 0 AND expires_at > NOW()
                 ORDER BY id DESC LIMIT 1",
                [$userId, $code]
            );
            $row = $stmt ? $stmt->fetch() : null;
        }

        if (!$row) {
            return false;
        }

        $db->query("UPDATE two_factor_codes SET used = 1 WHERE id = ?", [$row['id']]);
        return true;
    }
}
