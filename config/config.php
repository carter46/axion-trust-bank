 <?php
// Core Configuration File
// Note: SITE_NAME will be loaded dynamically from database after database connection

// Auto-detect SITE_URL from current request (supports multiple domains)
// Falls back to environment variable or hardcoded default if needed
if (!defined('SITE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
                (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) || 
                (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') 
                ? 'https' : 'http';
    
    $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
    
    // Remove port if it's the default port (80 for http, 443 for https)
    $port = $_SERVER['SERVER_PORT'] ?? null;
    if ($port && (($protocol === 'http' && $port != 80) || ($protocol === 'https' && $port != 443))) {
        $host .= ':' . $port;
    }
    
    $siteUrl = $protocol . '://' . $host;
    
    // Allow override via environment variable
    if (isset($_ENV['SITE_URL']) && !empty($_ENV['SITE_URL'])) {
        $siteUrl = $_ENV['SITE_URL'];
    }
    
    define('SITE_URL', $siteUrl);
}

define('BASE_PATH', dirname(__DIR__));

// Development mode flag (set to false in production)
// MUST be defined before encryption key check
define('DEVELOPMENT_MODE', ($_ENV['DEVELOPMENT_MODE'] ?? 'false') === 'true');

// Database Configuration
// ⚠️ SECURITY WARNING: Credentials should be moved to .env file in production
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'u502532383_webaxion');
define('DB_USER', $_ENV['DB_USER'] ?? 'u502532383_webaxion');
define('DB_PASS', $_ENV['DB_PASS'] ?? 'Secretpass0721//');

// Security Settings
// ⚠️ CRITICAL: Encryption key MUST be set in environment variables in production
// Never use default encryption key in production!
$encryptionKey = $_ENV['ENCRYPTION_KEY'] ?? null;
if (!$encryptionKey) {
    // Only fail loudly in production if encryption key is not set
    if (!DEVELOPMENT_MODE) {
        // Log the error but don't die immediately - allow the system to handle user sessions first
        error_log('CRITICAL SECURITY WARNING: ENCRYPTION_KEY is not set in environment variables!');
        // Use a default fallback but log it as a critical security issue
        // In production, this should be caught and fixed immediately
        $encryptionKey = 'CHANGE_THIS_IN_PRODUCTION_' . md5(__FILE__ . time());
    } else {
        // Only allow default in development
        $encryptionKey = 'CosmopolitanTrustBank2024SecureKey!@#';
        error_log('DEVELOPMENT MODE: Using default encryption key. Change this in production!');
    }
}
define('ENCRYPTION_KEY', $encryptionKey);

define('SESSION_LIFETIME', 1800); // 30 minutes
define('MAX_LOGIN_ATTEMPTS', 3);
define('LOCKOUT_TIME', 900); // 15 minutes

// Email Configuration (for notifications)
// ⚠️ SECURITY WARNING: Credentials should be moved to .env file in production
define('SMTP_HOST', $_ENV['SMTP_HOST'] ?? 'smtp.hostinger.com');
define('SMTP_PORT', $_ENV['SMTP_PORT'] ?? 465);
define('SMTP_USER', $_ENV['SMTP_USER'] ?? 'web@axiontrustbank.com');
define('SMTP_PASS', $_ENV['SMTP_PASS'] ?? 'Secretpass0721//');
define('SMTP_FROM', $_ENV['SMTP_FROM'] ?? 'web@axiontrustbank.com');
define('SMTP_FROM_NAME', $_ENV['SMTP_FROM_NAME'] ?? 'Axion Trust Bank');

// IMAP Configuration (for receiving emails)
define('IMAP_HOST', $_ENV['IMAP_HOST'] ?? 'imap.hostinger.com');
define('IMAP_PORT', $_ENV['IMAP_PORT'] ?? 993);
define('IMAP_USER', $_ENV['IMAP_USER'] ?? SMTP_USER); // Use SMTP_USER if IMAP_USER not set
define('IMAP_PASS', $_ENV['IMAP_PASS'] ?? SMTP_PASS); // Use SMTP_PASS if IMAP_PASS not set

// SMS Configuration (Twilio example)
define('TWILIO_SID', $_ENV['TWILIO_SID'] ?? 'your-twilio-sid');
define('TWILIO_TOKEN', $_ENV['TWILIO_TOKEN'] ?? 'your-twilio-token');
define('TWILIO_FROM', $_ENV['TWILIO_FROM'] ?? '+1234567890');

// ExchangeRate-API v6 — used by includes/exchange-rates.php for all conversions
define('EXCHANGE_RATE_API_KEY', $_ENV['EXCHANGE_RATE_API_KEY'] ?? 'your-api-key');

// File Upload Settings
define('MAX_FILE_SIZE', 5242880); // 5MB
define('UPLOAD_PATH', BASE_PATH . '/uploads/');
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'pdf']);

// Timezone
date_default_timezone_set('America/New_York');

// Cache Busting Version - Change this number whenever you update CSS/JS/assets
// This forces browsers to fetch new versions instead of using cached files
define('ASSET_VERSION', '1.0.1'); // Change this to a new value (e.g., '1.0.2') whenever you update assets

// Error Reporting - CRITICAL: Disable in production!
if (DEVELOPMENT_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    // Production: Log errors but don't display them
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', BASE_PATH . '/logs/php-errors.log');
}

// Session Configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 1 : 0); // HTTPS only if available
// Use 'Lax' instead of 'Strict' to allow cookies to work after domain migrations and redirects
// This still provides good security while being more flexible
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.use_strict_mode', 1); // Prevent session fixation
// Don't set cookie domain - let browser handle it (works better with domain migrations)

session_start();

// After domain migration: Clear session if it's from old domain
// Only clear if session_domain is set and doesn't match (prevents clearing new sessions)
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    $currentDomain = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
    
    // If session_domain is set and doesn't match current domain, it's an old session
    if (isset($_SESSION['session_domain']) && $_SESSION['session_domain'] !== $currentDomain) {
        // Session is from different domain - clear it and force re-login
        session_unset();
        session_destroy();
        session_start();
        $_SESSION['error'] = 'Session expired after domain migration. Please log in again.';
        // Redirect to login if not already there
        if (strpos($_SERVER['REQUEST_URI'], '/auth/login') === false) {
            header("Location: " . SITE_URL . "/auth/login");
            exit();
        }
    } elseif (!isset($_SESSION['session_domain'])) {
        // Old session format (before domain tracking) - mark it but don't clear
        // This handles the migration period gracefully
        $_SESSION['session_domain'] = $currentDomain;
    }
}

// Include database and helper classes
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/includes/system-settings.php';

// Currency must be defined before loading helpers that rely on it
if (!defined('DEFAULT_CURRENCY')) {
    try {
        $systemSettings = SystemSettings::getInstance();
        $defaultCurrency = strtoupper(trim($systemSettings->getDefaultCurrency() ?: 'USD'));
        define('DEFAULT_CURRENCY', $defaultCurrency);
    } catch (Exception $e) {
        define('DEFAULT_CURRENCY', 'USD');
    } catch (Error $e) {
        define('DEFAULT_CURRENCY', 'USD');
    }
}

require_once BASE_PATH . '/includes/countries.php';
require_once BASE_PATH . '/includes/functions.php';
require_once BASE_PATH . '/includes/email-template.php';
require_once BASE_PATH . '/includes/security-headers.php'; // Security headers

// Load dynamic site name from database
$dynamicSiteName = getSystemSetting('site_name', 'Axion Trust Bank');
if (!defined('SITE_NAME')) {
    define('SITE_NAME', $dynamicSiteName);
}