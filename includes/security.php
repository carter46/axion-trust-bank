<?php
/**
 * Security Class
 */
class Security {
    
    public static function initialize() {
        // Regenerate session ID periodically
        if (!isset($_SESSION['last_regeneration'])) {
            $_SESSION['last_regeneration'] = time();
        } else if (time() - $_SESSION['last_regeneration'] > 300) {
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        }
        
        // Only check session timeout for logged-in users
        // Public pages should not require authentication
        if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
            // CRITICAL SECURITY: Verify user account still exists on every request
            // This catches account deletions/suspensions immediately
            try {
                $db = Database::getInstance();
                $sql = "SELECT id, status FROM users WHERE id = ? LIMIT 1";
                $stmt = $db->query($sql, [$_SESSION['user_id']]);
                $user = $stmt->fetch();
                
                // If user doesn't exist, destroy session immediately
                if (!$user) {
                    session_unset();
                    session_destroy();
                    // Start new session for error message
                    session_start();
                    $_SESSION['error'] = 'Your account has been deleted or is no longer active.';
                    if (!headers_sent()) {
                        header("Location: " . SITE_URL . "/auth/login");
                        exit;
                    }
                    return;
                }
                
                // Suspended/blocked users are allowed to stay logged in (restricted mode).
                // Financial actions are blocked elsewhere; do not destroy sessions here.
            } catch (Exception $e) {
                // Log error but don't break the site if DB has temporary issues
                error_log("Security::initialize() - User validation error: " . $e->getMessage());
                // Continue with normal flow if DB error occurs
            }
            
            // Check session timeout only for authenticated users
            if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_LIFETIME)) {
                session_unset();
                session_destroy();
                redirect('/auth/login?timeout=1');
            }
            $_SESSION['last_activity'] = time();
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
        // Minimum 8 characters, at least one uppercase, one lowercase, one number
        // Special characters are optional but allowed
        if (strlen($password) < 8) {
            return false;
        }
        
        // Check for uppercase, lowercase, and number
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
        
        // Get max login attempts from settings
        $settingsSql = "SELECT setting_value FROM system_settings WHERE setting_key = 'max_login_attempts'";
        $settingsStmt = $db->query($settingsSql);
        $settings = $settingsStmt->fetch();
        $maxAttempts = $settings ? intval($settings['setting_value']) : MAX_LOGIN_ATTEMPTS; // Fallback to constant if not set
        
        // Get lockout duration from settings
        $settingsSql = "SELECT setting_value FROM system_settings WHERE setting_key = 'login_lockout_duration'";
        $settingsStmt = $db->query($settingsSql);
        $settings = $settingsStmt->fetch();
        $lockoutMinutes = $settings ? intval($settings['setting_value']) : 15;
        
        $sql = "SELECT COUNT(*) as attempts FROM login_attempts 
                WHERE email = ? AND attempted_at > DATE_SUB(NOW(), INTERVAL ? MINUTE)";
        
        $stmt = $db->query($sql, [$email, $lockoutMinutes]);
        $result = $stmt->fetch();
        
        return $result['attempts'] >= $maxAttempts;
    }
    
    public static function recordLoginAttempt($email, $success = false) {
        $db = Database::getInstance();
        
        if ($success) {
            // Clear failed attempts
            $sql = "DELETE FROM login_attempts WHERE email = ?";
            $db->query($sql, [$email]);
        } else {
            // Record failed attempt
            $sql = "INSERT INTO login_attempts (email, ip_address, attempted_at) VALUES (?, ?, NOW())";
            $db->query($sql, [$email, $_SERVER['REMOTE_ADDR']]);
        }
    }
    
    public static function detectSuspiciousActivity($userId, $amount) {
        // Simple fraud detection based on transaction patterns
        $db = Database::getInstance();
        
        // Check for multiple large transactions in short time
        $sql = "SELECT COUNT(*) as count, SUM(amount) as total 
                FROM transactions 
                WHERE user_id = ? 
                AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
                AND amount > 1000";
        
        $stmt = $db->query($sql, [$userId]);
        $result = $stmt->fetch();
        
        if ($result['count'] > 3 || $result['total'] > 10000) {
            // Log suspicious activity
            logActivity($userId, 'SUSPICIOUS_ACTIVITY', "Multiple large transactions detected");
            return true;
        }
        
        return false;
    }
    
    public static function validate2FA($userId, $code, $purpose = 'login') {
        $db = Database::getInstance();
        $result = null;

        // Newer schema: purpose-aware
        try {
            $sql = "SELECT * FROM two_factor_codes 
                    WHERE user_id = ? 
                    AND code = ? 
                    AND purpose = ?
                    AND expires_at > NOW() 
                    AND used = 0 
                    ORDER BY created_at DESC 
                    LIMIT 1";
            $stmt = $db->query($sql, [$userId, $code, $purpose]);
            $result = $stmt ? $stmt->fetch() : null;
        } catch (Exception $e) {
            // Older schema: no purpose column
            $sql = "SELECT * FROM two_factor_codes 
                    WHERE user_id = ? 
                    AND code = ? 
                    AND expires_at > NOW() 
                    AND used = 0 
                    ORDER BY created_at DESC 
                    LIMIT 1";
            $stmt = $db->query($sql, [$userId, $code]);
            $result = $stmt ? $stmt->fetch() : null;
        }
        
        if ($result) {
            // Mark code as used
            $updateSql = "UPDATE two_factor_codes SET used = 1 WHERE id = ?";
            $db->query($updateSql, [$result['id']]);
            return true;
        }
        
        return false;
    }
    
    public static function generate2FACode($userId, $method = 'email', $purpose = 'login') {
        $code = generateOTP(6);
        $db = Database::getInstance();
        
        // Store code in database
        try {
            $sql = "INSERT INTO two_factor_codes (user_id, code, method, purpose, expires_at, created_at) 
                    VALUES (?, ?, ?, ?, DATE_ADD(NOW(), INTERVAL 10 MINUTE), NOW())";
            $db->query($sql, [$userId, $code, $method, $purpose]);
        } catch (Exception $e) {
            // Older schema: no purpose column
            $sql = "INSERT INTO two_factor_codes (user_id, code, method, expires_at, created_at) 
                    VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL 10 MINUTE), NOW())";
            $db->query($sql, [$userId, $code, $method]);
        }
        
        return $code;
    }
    
    public static function preventXSS($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
    
    public static function preventSQLInjection($string) {
        // PDO with prepared statements already handles this, but for extra safety
        return addslashes($string);
    }
}
