<?php
/**
 * System Settings Helper
 * Provides easy access to system settings from database
 */

/**
 * Resolve logo/favicon URL to an existing file (uploads/branding first, then legacy paths).
 * Uses filemtime for cache busting so deploys do not require DB updates.
 */
function resolveBrandingAssetUrl(?string $storedUrl, string $defaultUrl, string $basename): string
{
    $candidates = [];

    if ($storedUrl !== null && trim($storedUrl) !== '') {
        $candidates[] = $storedUrl;
    }

    $brandingDir = BASE_PATH . '/uploads/branding';
    if (is_dir($brandingDir)) {
        foreach (glob($brandingDir . '/' . $basename . '.*') ?: [] as $file) {
            if (is_file($file)) {
                $candidates[] = SITE_URL . '/uploads/branding/' . basename($file);
            }
        }
    }

  foreach (['webp', 'png', 'jpg', 'jpeg', 'svg', 'ico'] as $ext) {
        $candidates[] = SITE_URL . '/assets/images/bank-logo.' . $ext;
        $candidates[] = SITE_URL . '/assets/images/' . $basename . '.' . $ext;
        if ($basename === 'favicon') {
            $candidates[] = SITE_URL . '/favicon.' . $ext;
        }
    }

    $seen = [];
    foreach ($candidates as $url) {
        $clean = strtok((string)$url, '?');
        if ($clean === false || $clean === '' || isset($seen[$clean])) {
            continue;
        }
        $seen[$clean] = true;

        $path = str_replace(SITE_URL, BASE_PATH, $clean);
        if ($path && is_file($path)) {
            if (strpos($clean, '/uploads/branding/') === false) {
                $migrated = migrateBrandingAssetToUploads($path, $basename);
                if ($migrated !== null) {
                    return $migrated;
                }
            }
            return $clean . '?v=' . filemtime($path);
        }
    }

    return $defaultUrl;
}

/**
 * Copy a legacy branding file into uploads/branding/ so it survives git deploys.
 */
function migrateBrandingAssetToUploads(string $sourcePath, string $basename): ?string
{
    $ext = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));
    if ($ext === '') {
        return null;
    }

    $destDir = BASE_PATH . '/uploads/branding';
    if (!is_dir($destDir) && !mkdir($destDir, 0755, true)) {
        return null;
    }

    $destPath = $destDir . '/' . $basename . '.' . $ext;
    if (!is_file($destPath) && is_readable($sourcePath)) {
        @copy($sourcePath, $destPath);
    }

    if (!is_file($destPath)) {
        return null;
    }

    return SITE_URL . '/uploads/branding/' . $basename . '.' . $ext . '?v=' . filemtime($destPath);
}

class SystemSettings {
    private static $instance = null;
    private $settings = [];
    private $loaded = false;
    
    private function __construct() {
        $this->loadSettings();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Load all settings from database
     */
    private function loadSettings() {
        if ($this->loaded) {
            return;
        }
        
        try {
            // Check if Database class exists
            if (!class_exists('Database')) {
                error_log("[SystemSettings Debug] Database class not found - cannot load settings");
                $this->loaded = false;
                return;
            }
            
            $db = Database::getInstance();
            
            // Check if table exists
            $sql = "SELECT setting_key, setting_value FROM system_settings";
            $stmt = $db->query($sql);
            $results = $stmt->fetchAll();
            
            if (empty($results)) {
                error_log("[SystemSettings Debug] No settings found in database - using defaults");
            }
            
            foreach ($results as $row) {
                $this->settings[$row['setting_key']] = $row['setting_value'];
            }
            
            $this->loaded = true;
            
            // Debug: Log loaded settings count
            // error_log("[SystemSettings Debug] Loaded " . count($this->settings) . " settings");
        } catch (Exception $e) {
            error_log("[SystemSettings Debug] Load Error: " . $e->getMessage());
            error_log("[SystemSettings Debug] Stack trace: " . $e->getTraceAsString());
            $this->loaded = false;
        } catch (Error $e) {
            error_log("[SystemSettings Debug] Fatal Error: " . $e->getMessage());
            $this->loaded = false;
        }
    }
    
    /**
     * Get a setting value
     * @param string $key Setting key
     * @param mixed $default Default value if setting not found
     * @return mixed Setting value or default
     */
    public function get($key, $default = null) {
        if (!$this->loaded) {
            $this->loadSettings();
        }
        
        // If still not loaded after attempt, return default
        if (!$this->loaded && !empty($default)) {
            error_log("[SystemSettings Debug] Settings not loaded, returning default for key: " . $key);
            return $default;
        }
        
        $value = isset($this->settings[$key]) ? $this->settings[$key] : $default;
        
        // Debug logging for empty values
        if (empty($value) && !empty($default)) {
            error_log("[SystemSettings Debug] Setting '{$key}' is empty, using default");
        }
        
        return $value;
    }
    
    /**
     * Get all settings
     * @return array All settings
     */
    public function getAll() {
        if (!$this->loaded) {
            $this->loadSettings();
        }
        
        return $this->settings;
    }
    
    /**
     * Update a setting (creates if doesn't exist)
     * @param string $key Setting key
     * @param mixed $value New value
     * @param int $userId User making the change
     * @return bool Success status
     */
    public function update($key, $value, $userId = null) {
        try {
            $db = Database::getInstance();
            
            // First check if setting exists to get its type and description
            $checkSql = "SELECT setting_type, description FROM system_settings WHERE setting_key = ? LIMIT 1";
            $checkStmt = $db->query($checkSql, [$key]);
            $existing = $checkStmt ? $checkStmt->fetch() : null;
            
            // Determine setting type if not exists
            $settingType = 'string';
            if ($existing && isset($existing['setting_type'])) {
                $settingType = $existing['setting_type'];
            } else {
                // Auto-detect type
                if (is_numeric($value) && (strpos($value, '.') !== false || strpos($value, ',') !== false)) {
                    $settingType = 'number';
                } elseif (in_array(strtolower($value), ['0', '1', 'true', 'false', 'yes', 'no', 'on', 'off'])) {
                    $settingType = 'boolean';
                }
            }
            
            // Use INSERT ... ON DUPLICATE KEY UPDATE to handle both insert and update
            $sql = "INSERT INTO system_settings (setting_key, setting_value, setting_type, updated_by, updated_at) 
                    VALUES (?, ?, ?, ?, NOW())
                    ON DUPLICATE KEY UPDATE 
                        setting_value = VALUES(setting_value),
                        updated_by = VALUES(updated_by),
                        updated_at = NOW()";
            
            $db->query($sql, [$key, $value, $settingType, $userId]);
            
            // Update local cache
            $this->settings[$key] = $value;
            
            return true;
        } catch (Exception $e) {
            error_log("System Settings Update Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Reload settings from database
     */
    public function reload() {
        $this->loaded = false;
        $this->settings = [];
        $this->loadSettings();
    }
    
    /**
     * Helper method - Get site name
     */
    public function getSiteName() {
        return $this->get('site_name', 'SecureBank Online');
    }
    
    /**
     * Helper method - Get site email
     */
    public function getSiteEmail() {
        return $this->get('site_email', 'support@securebank.com');
    }
    
    /**
     * Helper method - Get site logo URL
     */
    public function getSiteLogo() {
        return resolveBrandingAssetUrl(
            $this->get('site_logo_url', ''),
            SITE_URL . '/assets/images/logo.svg',
            'site-logo'
        );
    }

    /**
     * Helper method - Get site favicon URL
     */
    public function getSiteFavicon() {
        return resolveBrandingAssetUrl(
            $this->get('site_favicon_url', ''),
            SITE_URL . '/favicon.svg',
            'favicon'
        );
    }
    
    /**
     * Helper method - Get default currency
     */
    public function getDefaultCurrency() {
        return $this->get('default_currency', 'USD');
    }
    
    /**
     * Helper method - Check if maintenance mode is enabled
     */
    public function isMaintenanceMode() {
        return $this->get('maintenance_mode', '0') === '1';
    }
    
    /**
     * Helper method - Check if KYC is required
     */
    public function isKYCRequired() {
        return $this->get('require_kyc', '1') === '1';
    }
    
    /**
     * Helper method - Check if 2FA is required
     */
    public function is2FARequired() {
        return $this->get('two_factor_required', '0') === '1';
    }
    
    /**
     * Check if 2FA is disabled entirely
     */
    public function is2FADisabled() {
        return $this->get('disable_2fa_entirely', '0') === '1';
    }
    
    /**
     * Helper method - Check if registrations are allowed
     */
    public function allowNewRegistrations() {
        return $this->get('allow_new_registrations', '1') === '1';
    }
    
    /**
     * Helper method - Get transfer fee by type
     */
    public function getTransferFee($type) {
        $key = 'transfer_' . $type . '_fee';
        return floatval($this->get($key, '0'));
    }
}

/**
 * Global helper function to access system settings
 */
function getSetting($key, $default = null) {
    return SystemSettings::getInstance()->get($key, $default);
}

/**
 * Global helper function to get site name
 */
function getSiteName() {
    try {
        $instance = SystemSettings::getInstance();
        $name = $instance->getSiteName();
        
        // Debug logging (only if name is empty or default)
        if (empty($name) || $name === 'SecureBank Online') {
            error_log("[Branding Debug] getSiteName() returned: " . ($name ?: 'EMPTY') . " (checking database)");
        }
        
        return $name ?: 'Cosmopolitan Trust Bank'; // Fallback to default
    } catch (Exception $e) {
        error_log("[Branding Debug] Error in getSiteName(): " . $e->getMessage());
        return 'Cosmopolitan Trust Bank'; // Fallback
    }
}

function getSiteLogo() {
    try {
        $instance = SystemSettings::getInstance();
        return $instance->getSiteLogo();
    } catch (Exception $e) {
        error_log("[Branding Debug] Error in getSiteLogo(): " . $e->getMessage());
        return SITE_URL . '/assets/images/logo.svg';
    }
}

function getSiteFavicon() {
    try {
        $instance = SystemSettings::getInstance();
        return $instance->getSiteFavicon();
    } catch (Exception $e) {
        error_log("[Branding Debug] Error in getSiteFavicon(): " . $e->getMessage());
        return SITE_URL . '/favicon.svg';
    }
}

/**
 * Global helper function to get the admin-configured site contact email
 */
function getSiteEmail() {
    try {
        $email = SystemSettings::getInstance()->get('site_email', '');
        if (!empty($email)) {
            return $email;
        }
    } catch (Exception $e) {
        error_log("[Branding Debug] Error in getSiteEmail(): " . $e->getMessage());
    }

    return defined('SMTP_FROM') ? SMTP_FROM : 'support@securebank.com';
}

/**
 * Global helper function to get site initials from site name
 * Extracts first letter of each word (e.g., "Cosmopolitan Trust Bank" -> "CTB")
 */
function getSiteInitials() {
    try {
        if (!function_exists('getSiteName')) {
            error_log("[Branding Debug] getSiteName() function not found, using fallback");
            return 'CTB'; // Fallback
        }
        
        $siteName = getSiteName();
        
        if (empty($siteName)) {
            error_log("[Branding Debug] Site name is empty, using fallback");
            return 'CTB'; // Fallback
        }
        
        $words = explode(' ', trim($siteName));
        $initials = '';
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper(substr($word, 0, 1));
            }
        }
        
        // If no initials generated, use fallback
        if (empty($initials)) {
            error_log("[Branding Debug] Failed to generate initials from: " . $siteName);
            return 'CTB';
        }
        
        // Limit to first 5 characters for display
        return substr($initials, 0, 5);
    } catch (Exception $e) {
        error_log("[Branding Debug] Error in getSiteInitials(): " . $e->getMessage());
        return 'CTB'; // Fallback
    }
}

/**
 * Global helper function - alias for getSetting (for backward compatibility)
 * Only declare if not already defined (prevents redeclaration errors)
 */
if (!function_exists('getSystemSetting')) {
    function getSystemSetting($key, $default = null) {
        return SystemSettings::getInstance()->get($key, $default);
    }
}