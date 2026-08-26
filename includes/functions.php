<?php
/**
 * Helper Functions
 */

function redirect($url) {
    // Explicitly save session data before redirecting
    // This ensures error/success messages persist across redirects
    if (session_status() === PHP_SESSION_ACTIVE) {
        // Force session write - this saves all $_SESSION data
        session_write_close();
    }
    header("Location: " . SITE_URL . '/' . ltrim($url, '/'));
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function restrictedAccountMessage() {
    return 'Your account has been suspended or deactivated, Please contact the support for more details';
}

function isRestrictedStatus($status) {
    $status = strtolower(trim((string)$status));
    return in_array($status, ['suspended', 'blocked', 'hold', 'deactivated', 'inactive', 'restricted', 'closed'], true);
}

/**
 * Why a transfer is blocked (user or source account status). Empty string = OK to proceed.
 */
function getTransferBlockedReason($userStatus, $accountStatus) {
    if (isRestrictedStatus($userStatus ?? '')) {
        return restrictedAccountMessage();
    }
    $acct = strtolower(trim((string)$accountStatus));
    if ($acct === 'frozen') {
        return 'Your account is frozen and cannot send transfers. Please contact support for assistance.';
    }
    if ($acct === 'closed') {
        return 'This account is closed and cannot be used for transfers.';
    }
    if ($acct !== '' && $acct !== 'active') {
        return 'This account is not available for transfers. Please contact support.';
    }
    return '';
}

function isTransferBlocked($userStatus, $accountStatus) {
    return getTransferBlockedReason($userStatus, $accountStatus) !== '';
}

/**
 * Dashboard KYC prompt: show only when user must start or re-submit (not while under review).
 */
function shouldShowKycDashboardPrompt($userId) {
    if (!$userId) {
        return false;
    }
    try {
        $db = Database::getInstance();
        $stmt = $db->query(
            "SELECT kyc_status, kyc_prompt_dismissed FROM users WHERE id = ?",
            [$userId]
        );
        $user = $stmt->fetch();
        if (!$user) {
            return false;
        }
        if (($user['kyc_status'] ?? '') === 'verified') {
            return false;
        }
        if (!empty($user['kyc_prompt_dismissed'])) {
            return false;
        }
        $kycStmt = $db->query(
            "SELECT status FROM kyc_verifications WHERE user_id = ? ORDER BY id DESC LIMIT 1",
            [$userId]
        );
        $submission = $kycStmt->fetch();
        if ($submission) {
            $subStatus = $submission['status'] ?? '';
            if (in_array($subStatus, ['pending', 'under_review'], true)) {
                return false;
            }
            if (in_array($subStatus, ['rejected', 'requires_action'], true)) {
                return true;
            }
        }
        if (($user['kyc_status'] ?? '') === 'rejected') {
            return true;
        }
        if (!$submission) {
            return true;
        }
        return false;
    } catch (Exception $e) {
        error_log('shouldShowKycDashboardPrompt error: ' . $e->getMessage());
        return false;
    }
}

function isForceSecuritySetupEnabled() {
    try {
        require_once __DIR__ . '/system-settings.php';
        return SystemSettings::getInstance()->get('force_security_setup', '1') === '1';
    } catch (Exception $e) {
        return true;
    }
}

function establishUserSession($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_name'] = $user['full_name'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['session_domain'] = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
    $profilePicture = $user['profile_picture'] ?? null;
    if ($profilePicture && defined('BASE_PATH') && file_exists(BASE_PATH . $profilePicture)) {
        $_SESSION['user_photo'] = $profilePicture;
    } else {
        $_SESSION['user_photo'] = null;
    }
    if (class_exists('User')) {
        $userModel = new User();
        $userModel->updateLastLogin($user['id']);
    }
    logActivity($user['id'], 'LOGIN', 'User session established');
}

/**
 * Format a person's name for UI: first letter of each word capitalized (title case).
 */
function formatDisplayName($name) {
    $name = trim((string)$name);
    if ($name === '') {
        return '';
    }
    if (function_exists('mb_convert_case')) {
        return mb_convert_case($name, MB_CASE_TITLE, 'UTF-8');
    }
    return ucwords(strtolower($name));
}

function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        redirect('/auth/login');
    }
    
    // CRITICAL SECURITY: Verify user account still exists
    // Prevents deleted/suspended users from accessing the system
    try {
        if (!class_exists('User')) {
            require_once __DIR__ . '/../models/User.php';
        }
        $userModel = new User();
        $user = $userModel->findById($_SESSION['user_id']);
        
        // If user doesn't exist, destroy session and redirect
        if (!$user) {
            // Destroy session completely
            $_SESSION = [];
            if (isset($_COOKIE[session_name()])) {
                setcookie(session_name(), '', time() - 3600, '/');
            }
            session_destroy();
            
            // Redirect to login with account deleted message
            $_SESSION = []; // Reset session array
            session_start(); // Start fresh session for the message
            $_SESSION['error'] = 'Your account has been deleted or is no longer active. Please contact support if you believe this is an error.';
            redirect('/auth/login');
        }
        
        // If user is restricted (suspended/blocked/etc.) they may still log in,
        // but financial actions will be blocked by dedicated guards.
        $status = $user['status'] ?? 'active';
        if (isRestrictedStatus($status)) {
            $_SESSION['restricted_status'] = $status;
        } else {
            unset($_SESSION['restricted_status']);
        }
        
        // Check if security setup is incomplete - redirect to security page
        // Skip this check for staff (admin/support/super-admin) and when already on security/profile pages
        $currentPath = $_SERVER['REQUEST_URI'] ?? '';
        $role = strtolower(trim((string)($user['role'] ?? 'user')));
        $isStaff = ($role === 'admin') || ($role === 'support') || !empty($user['is_super_admin'] ?? 0);
        
        // Check if we're on the security page or any profile page to avoid redirect loops
        // Check both URL path and route parameter
        $isSecurityPage = (strpos($currentPath, '/profile/security') !== false) || 
                         (strpos($currentPath, 'route=profile/security') !== false) ||
                         (isset($_GET['route']) && $_GET['route'] === 'profile/security');
        
        // Only check and redirect if not staff, not restricted, not on security page, and security is incomplete
        if (!$isStaff && !isRestrictedStatus($status ?? '') && !$isSecurityPage) {
            try {
                // Only check if function exists (defensive programming)
                if (function_exists('isSecuritySetupIncomplete')) {
                    if (isSecuritySetupIncomplete($user['id'])) {
                        $_SESSION['security_setup_required'] = true; // Flag that security setup is required
                        redirect('/profile/security');
                    }
                }
            } catch (Exception $e) {
                // If check fails, log error but don't block access
                error_log("Security setup check error in requireLogin: " . $e->getMessage());
            } catch (Error $e) {
                // Catch fatal errors too
                error_log("Security setup check fatal error in requireLogin: " . $e->getMessage());
            }
        }
        
    } catch (Exception $e) {
        // If database error occurs, log it but don't expose to user
        error_log("Session validation error: " . $e->getMessage());
        // In case of DB error, still allow access but log the issue
        // This prevents DB issues from locking out all users
    }
}

function requireNotRestrictedForFinancialActions() {
    // Call requireLogin() first in controllers before using this guard.
    $status = $_SESSION['restricted_status'] ?? '';
    if ($status && isRestrictedStatus($status)) {
        $_SESSION['warning'] = restrictedAccountMessage();
        redirect('/dashboard?restricted=1');
    }
}

function requireAdmin() {
    requireLogin();
    
    // Check if admin is impersonating - if so, check original admin credentials
    $isImpersonating = isset($_SESSION['admin_impersonating']) && $_SESSION['admin_impersonating'];
    $userIdToCheck = $isImpersonating ? ($_SESSION['admin_original_id'] ?? null) : $_SESSION['user_id'];
    
    // Verify admin role still exists in database
    try {
        if (!class_exists('User')) {
            require_once __DIR__ . '/../models/User.php';
        }
        $userModel = new User();
        $user = $userModel->findById($userIdToCheck);
        
        if (!$user || ($user['role'] ?? '') !== 'admin') {
            $_SESSION['error'] = 'Admin access required';
            redirect('/dashboard');
        }

        // Apply pending DB auto-migrations on every admin page load (idempotent)
        try {
            require_once __DIR__ . '/database-auto-migrate.php';
            runAdminDatabaseAutoMigrations((int)$userIdToCheck);
        } catch (Throwable $migrateError) {
            $_SESSION['auto_migration_errors'] = [
                'Auto-migration runner failed: ' . $migrateError->getMessage()
            ];
            error_log('requireAdmin auto-migrate error: ' . $migrateError->getMessage());
        }
    } catch (Exception $e) {
        error_log("Admin validation error: " . $e->getMessage());
        $_SESSION['error'] = 'Unable to verify admin access';
        redirect('/dashboard');
    }
}

/**
 * Check if user's security setup is incomplete
 * Returns true if user needs Login PIN / Transfer PIN (2FA is optional and never blocks access)
 */
function isSecuritySetupIncomplete($userId = null) {
    if (!$userId && isLoggedIn()) {
        $userId = $_SESSION['user_id'];
    }
    
    if (!$userId) {
        return false;
    }
    
    try {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT transfer_pin, login_pin, role FROM users WHERE id = ?", [$userId]);
        $user = $stmt->fetch();
        
        if (!$user) {
            return false;
        }
        
        $hasTransferPin = !empty($user['transfer_pin'] ?? '');
        $hasLoginPin = !empty($user['login_pin'] ?? '');
        
        // 2FA is optional — incompleteness is Login PIN / Transfer PIN only
        $incomplete = !$hasTransferPin || !$hasLoginPin;
        if (!$incomplete) {
            return false;
        }
        if (!isForceSecuritySetupEnabled()) {
            return false;
        }
        return true;
    } catch (Exception $e) {
        error_log("Security setup check error: " . $e->getMessage());
        return false;
    } catch (Error $e) {
        // Catch fatal errors too
        error_log("Security setup check fatal error: " . $e->getMessage());
        return false;
    }
}

/**
 * Get transaction limit for account type
 * Returns daily or monthly limit from system settings based on account type
 */
function getAccountLimit($accountType, $limitType = 'daily') {
    require_once __DIR__ . '/system-settings.php';
    $systemSettings = SystemSettings::getInstance();
    
    // Normalize account type
    $accountType = strtolower($accountType);
    if ($accountType === 'saving') {
        $accountType = 'savings';
    }
    
    // Build setting key
    $settingKey = $limitType . '_limit_' . $accountType;
    
    // Get default values
    $defaults = [
        'daily' => [
            'checking' => 5000.00,
            'savings' => 3000.00,
            'business' => 50000.00
        ],
        'monthly' => [
            'checking' => 100000.00,
            'savings' => 50000.00,
            'business' => 1000000.00
        ]
    ];
    
    $default = $defaults[$limitType][$accountType] ?? ($limitType === 'daily' ? 5000.00 : 100000.00);
    
    return floatval($systemSettings->get($settingKey, $default));
}

/**
 * Admin-assigned (or otherwise set) display currency for a user.
 * Prefers users.currency whenever present; falls back to site default.
 */
function getUserDisplayCurrency($user = null) {
    if ($user === null && isLoggedIn()) {
        if (!class_exists('User')) {
            require_once __DIR__ . '/../models/User.php';
        }
        $user = (new User())->findById($_SESSION['user_id']);
    }

    if (is_array($user)) {
        $code = strtoupper(trim((string)($user['currency'] ?? '')));
        if (preg_match('/^[A-Z]{3}$/', $code)) {
            return $code;
        }
    }

    return getSiteDefaultCurrency();
}

/**
 * Primary country name for a currency code (for flag / location sync when admin sets currency).
 */
function currencyToPrimaryCountry($currencyCode) {
    $code = strtoupper(trim((string)$currencyCode));
    $map = [
        'USD' => 'United States',
        'CAD' => 'Canada',
        'MXN' => 'Mexico',
        'GBP' => 'United Kingdom',
        'EUR' => 'Germany',
        'CHF' => 'Switzerland',
        'SEK' => 'Sweden',
        'NOK' => 'Norway',
        'DKK' => 'Denmark',
        'PLN' => 'Poland',
        'CZK' => 'Czech Republic',
        'HUF' => 'Hungary',
        'RON' => 'Romania',
        'BGN' => 'Bulgaria',
        'AUD' => 'Australia',
        'NZD' => 'New Zealand',
        'JPY' => 'Japan',
        'CNY' => 'China',
        'HKD' => 'Hong Kong',
        'TWD' => 'Taiwan',
        'KRW' => 'South Korea',
        'SGD' => 'Singapore',
        'MYR' => 'Malaysia',
        'THB' => 'Thailand',
        'IDR' => 'Indonesia',
        'PHP' => 'Philippines',
        'VND' => 'Vietnam',
        'INR' => 'India',
        'PKR' => 'Pakistan',
        'BDT' => 'Bangladesh',
        'LKR' => 'Sri Lanka',
        'NGN' => 'Nigeria',
        'GHS' => 'Ghana',
        'KES' => 'Kenya',
        'ZAR' => 'South Africa',
        'EGP' => 'Egypt',
        'MAD' => 'Morocco',
        'TND' => 'Tunisia',
        'DZD' => 'Algeria',
        'AED' => 'United Arab Emirates',
        'SAR' => 'Saudi Arabia',
        'QAR' => 'Qatar',
        'KWD' => 'Kuwait',
        'BRL' => 'Brazil',
        'ARS' => 'Argentina',
        'CLP' => 'Chile',
        'COP' => 'Colombia',
        'PEN' => 'Peru',
        'TRY' => 'Turkey',
        'ILS' => 'Israel',
        'RUB' => 'Russia',
        'XOF' => 'Senegal',
        'ZMW' => 'Zambia',
        'DOP' => 'Dominican Republic',
        'JMD' => 'Jamaica',
        'BBD' => 'Barbados',
        'BZD' => 'Belize',
        'BND' => 'Brunei',
        'FJD' => 'Fiji',
        'GYD' => 'Guyana',
        'LRD' => 'Liberia',
        'SBD' => 'Solomon Islands',
        'SRD' => 'Suriname',
        'TTD' => 'Trinidad and Tobago',
        'XCD' => 'Antigua and Barbuda',
        'AWG' => 'Aruba',
        'BMD' => 'Bermuda',
        'BSD' => 'Bahamas',
        'KYD' => 'Cayman Islands',
        'ANG' => 'Curaçao',
    ];
    return $map[$code] ?? 'United States';
}

/**
 * Site ledger / default currency from system_settings (via DEFAULT_CURRENCY constant).
 */
function getSiteDefaultCurrency() {
    if (defined('DEFAULT_CURRENCY')) {
        return strtoupper(trim(DEFAULT_CURRENCY));
    }
    return strtoupper(trim(getSystemSetting('default_currency', 'USD')));
}

/**
 * Expected ISO currency code for a bank operating country name/code.
 */
function getCurrencyForOperatingCountry($country) {
    $country = trim((string)$country);
    if ($country === '') {
        return DEFAULT_CURRENCY;
    }

    if (!function_exists('countryToIso2')) {
        require_once __DIR__ . '/countries.php';
    }
    if (!function_exists('normalizeCountryCode')) {
        require_once __DIR__ . '/transfer-rails.php';
    }

    $countryCode = countryToIso2($country) ?: normalizeCountryCode($country);

    if (!class_exists('CurrencyConverter')) {
        require_once __DIR__ . '/currency-converter.php';
    }

    return strtoupper((new CurrencyConverter())->getCurrencyFromCountry($countryCode));
}

/**
 * Currency users enter transfer amounts in — their admin-assigned display currency.
 */
function getBankTransferEntryCurrency($user = null) {
    return getUserDisplayCurrency($user);
}

/**
 * Currency amounts were stored in when the account was created.
 */
function getAccountStoredCurrency(array $account) {
    return strtoupper(trim($account['currency'] ?? getSiteDefaultCurrency()));
}

/**
 * Currency user-level balances (investment, etc.) were stored in.
 */
function getUserStoredCurrency($user) {
    return getSiteDefaultCurrency();
}

function convertCurrencyAmount($amount, $fromCurrency, $toCurrency) {
    $fromCurrency = strtoupper(trim($fromCurrency ?: DEFAULT_CURRENCY));
    $toCurrency = strtoupper(trim($toCurrency ?: DEFAULT_CURRENCY));
    $amount = (float)$amount;

    if ($fromCurrency === $toCurrency) {
        return $amount;
    }

    $systemSettings = SystemSettings::getInstance();
    if ($systemSettings->get('enable_currency_conversion', '1') !== '1') {
        return $amount;
    }

    if (!class_exists('ExchangeRates')) {
        require_once __DIR__ . '/exchange-rates.php';
    }

    return ExchangeRates::getInstance()->convert($amount, $fromCurrency, $toCurrency);
}

function sumAccountBalancesForDisplay(array $accounts, $displayCurrency = null) {
    $displayCurrency = strtoupper(trim($displayCurrency ?: getUserDisplayCurrency()));
    $total = 0.0;

    foreach ($accounts as $account) {
        if (($account['status'] ?? 'active') !== 'active') {
            continue;
        }
        $balance = (float)($account['balance'] ?? 0);
        $total += convertCurrencyAmount($balance, getAccountStoredCurrency($account), $displayCurrency);
    }

    return $total;
}

function formatAccountBalance($amount, array $account, $displayCurrency = null) {
    $displayCurrency = $displayCurrency ?? getUserDisplayCurrency();
    return formatCurrency(
        $amount,
        $displayCurrency,
        getAccountStoredCurrency($account)
    );
}

/**
 * Convert an amount entered in the user's display currency to an account's ledger currency.
 */
function convertDisplayAmountToAccountLedger(float $displayAmount, $user, array $account): float
{
    return round(convertCurrencyAmount(
        $displayAmount,
        getUserDisplayCurrency($user),
        getAccountStoredCurrency($account)
    ), 2);
}

/**
 * Resolve admin adjustment amount: display currency (default) vs raw ledger units.
 */
function adminResolveLedgerAdjustmentAmount(float $amount, $user, array $account, string $amountCurrency = 'display'): float
{
    if ($amountCurrency === 'ledger') {
        return round($amount, 2);
    }
    return convertDisplayAmountToAccountLedger($amount, $user, $account);
}

/**
 * Total balance across a user's active accounts in their display currency.
 */
function getUserTotalBalanceForDisplay($user, array $accounts) {
    return sumAccountBalancesForDisplay($accounts, getUserDisplayCurrency($user));
}

/**
 * Format a user's total balance using the same rules as their dashboard.
 */
function formatUserTotalBalance($user, array $accounts) {
    $displayCurrency = getUserDisplayCurrency($user);
    $total = getUserTotalBalanceForDisplay($user, $accounts);
    return formatCurrency($total, $displayCurrency, $displayCurrency);
}

/**
 * Format any amount in the user's chosen display currency.
 */
function formatAmountForUser($amount, $user, $fromCurrency = null) {
    $displayCurrency = getUserDisplayCurrency($user);
    $sourceCurrency = $fromCurrency ?? DEFAULT_CURRENCY;
    return formatCurrency($amount, $displayCurrency, $sourceCurrency);
}

/**
 * Format an amount already expressed in display currency (no conversion).
 */
function formatDisplayCurrencyAmount($amount, $displayCurrency = null) {
    $currency = strtoupper(trim($displayCurrency ?: getUserDisplayCurrency()));
    return formatCurrency((float)$amount, $currency, $currency);
}

function getLoanStoredCurrency(array $loan = []) {
    return strtoupper(trim($loan['currency'] ?? getSiteDefaultCurrency()));
}

function formatLoanAmountForUser($amount, $user, array $loan = []) {
    return formatAmountForUser($amount, $user, getLoanStoredCurrency($loan));
}

function getCardStoredCurrency(array $card, $account = null) {
    if (is_array($account) && !empty($account['currency'])) {
        return getAccountStoredCurrency($account);
    }
    if (!empty($card['account_currency'])) {
        return strtoupper(trim($card['account_currency']));
    }
    return getSiteDefaultCurrency();
}

function formatCardAmountForUser($amount, $user, array $card, $account = null) {
    return formatAmountForUser($amount, $user, getCardStoredCurrency($card, $account));
}

function formatInvestmentAmountForUser($amount, $user) {
    return formatAmountForUser($amount, $user, DEFAULT_CURRENCY);
}

function formatUserInvestmentBalanceForUser($amount, $user) {
    return formatAmountForUser($amount, $user, getUserStoredCurrency($user));
}

/**
 * Decode transaction metadata JSON to an array.
 */
function parseTransactionMetadata($transaction) {
    if (!is_array($transaction)) {
        return [];
    }
    $raw = $transaction['metadata'] ?? null;
    if (is_array($raw)) {
        return $raw;
    }
    if (!is_string($raw) || trim($raw) === '') {
        return [];
    }
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : [];
}

/**
 * Ledger currency the transaction amount was recorded in.
 */
function getTransactionStoredCurrency($transaction) {
    if (!is_array($transaction)) {
        return DEFAULT_CURRENCY;
    }
    return strtoupper(trim($transaction['currency'] ?? DEFAULT_CURRENCY));
}

/**
 * Currency the user entered on transfer (when stored in metadata).
 */
function getTransactionEntryCurrency($transaction) {
    $meta = parseTransactionMetadata($transaction);
    if (!empty($meta['entry_currency'])) {
        return strtoupper(trim($meta['entry_currency']));
    }
    return getTransactionStoredCurrency($transaction);
}

/**
 * Show amount in native ledger currency (no conversion) — for admin ledgers.
 */
function formatTransactionNative($transaction, $field = 'amount') {
    $currency = getTransactionStoredCurrency($transaction);
    $amount = (float)(is_array($transaction) ? ($transaction[$field] ?? 0) : 0);
    return formatCurrency($amount, $currency, $currency);
}

/**
 * Format a transaction monetary field for the user (amount/fee) using stored currency.
 */
function formatTransactionAmountForUser($transaction, $user, $field = 'amount') {
    $amount = (float)(is_array($transaction) ? ($transaction[$field] ?? 0) : 0);
    return formatAmountForUser($amount, $user, getTransactionStoredCurrency($transaction));
}

/**
 * Format account balance snapshots on a transaction (balance_before/after).
 */
function formatTransactionBalanceForUser($transaction, $user, $field) {
    $amount = (float)(is_array($transaction) ? ($transaction[$field] ?? 0) : 0);
    return formatAmountForUser($amount, $user, getTransactionStoredCurrency($transaction));
}

/**
 * Receipts / transfer status: prefer amount user entered at transfer time, not converted ledger value.
 */
function formatTransactionReceiptAmount($transaction, $user, $field = 'amount') {
    $meta = parseTransactionMetadata($transaction);
    $displayCurrency = getUserDisplayCurrency($user);

    if ($field === 'amount' && isset($meta['entry_amount'], $meta['entry_currency'])) {
        return formatCurrency(
            (float)$meta['entry_amount'],
            $displayCurrency,
            strtoupper(trim($meta['entry_currency']))
        );
    }

    if ($field === 'fee' && isset($meta['entry_fee'], $meta['entry_currency'])) {
        return formatCurrency(
            (float)$meta['entry_fee'],
            $displayCurrency,
            strtoupper(trim($meta['entry_currency']))
        );
    }

    return formatTransactionAmountForUser($transaction, $user, $field);
}

/**
 * Net transfer amount for receipts (entry amount minus entry fee when available).
 */
function formatTransactionReceiptNet($transaction, $user) {
    $meta = parseTransactionMetadata($transaction);
    if (isset($meta['entry_amount'], $meta['entry_currency'])) {
        $fee = (float)($meta['entry_fee'] ?? 0);
        $net = (float)$meta['entry_amount'] - $fee;
        return formatCurrency(
            $net,
            getUserDisplayCurrency($user),
            strtoupper(trim($meta['entry_currency']))
        );
    }

    $net = (float)($transaction['amount'] ?? 0) - (float)($transaction['fee'] ?? 0);
    return formatTransactionAmountForUser(
        array_merge($transaction, ['amount' => $net]),
        $user,
        'amount'
    );
}

/**
 * Total debited on receipts (ledger total, shown in user display currency).
 */
function formatTransactionReceiptTotal($transaction, $user) {
    $meta = parseTransactionMetadata($transaction);
    if (isset($meta['entry_total'], $meta['entry_currency'])) {
        return formatCurrency(
            (float)$meta['entry_total'],
            getUserDisplayCurrency($user),
            strtoupper(trim($meta['entry_currency']))
        );
    }
    return formatTransactionAmountForUser($transaction, $user, 'amount');
}

function formatCurrency($amount, $currency = null, $fromCurrency = null) {
    // Check if currency conversion is enabled
    $systemSettings = SystemSettings::getInstance();
    $conversionEnabled = $systemSettings->get('enable_currency_conversion', '1') === '1';
    
    // Get user's display currency if not specified and conversion is enabled
    if (!$currency && isLoggedIn() && $conversionEnabled) {
        $currency = getUserDisplayCurrency();
    } else {
        $currency = $currency ?? DEFAULT_CURRENCY;
    }
    
    // If conversion is disabled, always use default currency
    if (!$conversionEnabled) {
        $currency = DEFAULT_CURRENCY;
    }
    
    // Source currency: account/user currency for balances; DEFAULT_CURRENCY for system limits when omitted
    $sourceCurrency = $fromCurrency ?? DEFAULT_CURRENCY;
    $amount = convertCurrencyAmount($amount, $sourceCurrency, $currency);
    
    // Get currency symbol mapping (same as Currency class format method)
    $currencySymbols = [
        'USD' => '$', 'EUR' => '€', 'GBP' => '£', 'JPY' => '¥',
        'CNY' => '¥', 'INR' => '₹', 'CAD' => 'CA$', 'AUD' => 'A$',
        'NGN' => '₦', 'ZAR' => 'R', 'AED' => 'د.إ', 'SAR' => 'ر.س',
        'QAR' => 'ر.ق', 'KWD' => 'د.ك', 'KES' => 'KSh', 'GHS' => '₵',
        'PKR' => '₨', 'BDT' => '৳', 'LKR' => 'Rs', 'SGD' => 'S$',
        'MYR' => 'RM', 'THB' => '฿', 'IDR' => 'Rp', 'PHP' => '₱',
        'VND' => '₫', 'KRW' => '₩', 'BRL' => 'R$', 'MXN' => '$',
        'ARS' => '$', 'CLP' => '$', 'COP' => '$', 'TRY' => '₺',
        'ILS' => '₪', 'NZD' => 'NZ$', 'HKD' => 'HK$', 'TWD' => 'NT$',
        'CHF' => 'Fr', 'SEK' => 'kr', 'NOK' => 'kr', 'DKK' => 'kr',
        'EGP' => 'E£', 'MAD' => 'د.م.', 'TND' => 'د.ت', 'DZD' => 'د.ج',
        'PLN' => 'zł', 'RUB' => '₽', 'CZK' => 'Kč', 'HUF' => 'Ft',
        'RON' => 'lei', 'BGN' => 'лв', 'PEN' => 'S/', 'XOF' => 'CFA',
        'ZMW' => 'ZK'
    ];
    
    $symbol = $currencySymbols[$currency] ?? ($currency . ' ');
    
    // Format based on currency (some currencies don't use decimals)
    if (in_array($currency, ['JPY', 'KRW', 'VND', 'CLP'])) {
        return $symbol . number_format($amount, 0);
    }
    
    return $symbol . number_format($amount, 2);
}

function normalizeCountryInput($country) {
    $country = trim((string)$country);
    if ($country === '') return '';
    $country = preg_replace('/\s+/', ' ', $country);
    return $country;
}

function countryToIso2($country) {
    $country = trim((string)$country);
    if ($country === '') {
        return null;
    }

    if (preg_match('/^[A-Za-z]{2}$/', $country)) {
        $byCode = getCountryByCode($country);
        if ($byCode) {
            return strtoupper($country);
        }
    }

    $byName = getCountryByName($country);
    if ($byName) {
        return $byName['code'];
    }

    $legacy = strtoupper(normalizeCountryInput($country));
    $map = [
        'UNITED STATES' => 'US',
        'UNITED STATES OF AMERICA' => 'US',
        'USA' => 'US',
        'U.S.A' => 'US',
        'US' => 'US',
        'AMERICA' => 'US',

        'UNITED KINGDOM' => 'GB',
        'GREAT BRITAIN' => 'GB',
        'BRITAIN' => 'GB',
        'UK' => 'GB',
        'U.K' => 'GB',

        'AUSTRALIA' => 'AU',
        'CANADA' => 'CA',
        'IRELAND' => 'IE',
        'NEW ZEALAND' => 'NZ',

        'NIGERIA' => 'NG',
        'GHANA' => 'GH',
        'KENYA' => 'KE',
        'SOUTH AFRICA' => 'ZA',

        'FRANCE' => 'FR',
        'GERMANY' => 'DE',
        'ITALY' => 'IT',
        'SPAIN' => 'ES',
        'NETHERLANDS' => 'NL',
        'SWITZERLAND' => 'CH',

        'UNITED ARAB EMIRATES' => 'AE',
        'UAE' => 'AE',
        'SAUDI ARABIA' => 'SA',
        'QATAR' => 'QA',
        'KUWAIT' => 'KW',

        'DOMINICAN REPUBLIC' => 'DO',
        'JAMAICA' => 'JM',
        'BAHAMAS' => 'BS',
        'BARBADOS' => 'BB',
        'TRINIDAD AND TOBAGO' => 'TT',
    ];

    return $map[$legacy] ?? null;
}

function countryToAccountDescriptor($country) {
    $raw = normalizeCountryInput($country);
    if ($raw === '') return '';

    $upper = strtoupper($raw);
    $map = [
        'UNITED STATES' => 'USA',
        'UNITED STATES OF AMERICA' => 'USA',
        'USA' => 'USA',
        'US' => 'USA',

        'UNITED KINGDOM' => 'British',
        'GREAT BRITAIN' => 'British',
        'BRITAIN' => 'British',
        'UK' => 'British',
        'GB' => 'British',

        'AUSTRALIA' => 'Australian',
        'CANADA' => 'Canadian',
        'CA' => 'Canadian',
        'IRELAND' => 'Irish',
        'IE' => 'Irish',
        'NEW ZEALAND' => 'New Zealand',
        'NZ' => 'New Zealand',
        'AU' => 'Australian',
        'DE' => 'German',
        'FR' => 'French',
        'DOMINICAN REPUBLIC' => 'Dominican',
        'DO' => 'Dominican',
    ];

    if (isset($map[$upper])) {
        return $map[$upper];
    }

    $byCode = getCountryByCode($upper);
    if ($byCode) {
        return $byCode['name'];
    }

    $byName = getCountryByName($raw);
    if ($byName) {
        return $byName['name'];
    }

    return $raw;
}

function countryFlagCdnUrl($country) {
    $iso2 = countryToIso2($country);
    if (!$iso2) return null;
    $iso2 = strtolower($iso2);
    return "https://flagcdn.com/w80/{$iso2}.png";
}

/**
 * Unicode flag emoji for a country name/code (e.g. United States → 🇺🇸).
 * Prefer this over CDN images — no network/CSP dependency.
 */
function countryFlagEmoji($country) {
    $iso2 = countryToIso2($country);
    if (!$iso2 || !preg_match('/^[A-Z]{2}$/', $iso2)) {
        return null;
    }

    if (!function_exists('mb_chr')) {
        return null;
    }

    $flag = '';
    for ($i = 0; $i < 2; $i++) {
        $flag .= mb_chr(127397 + ord($iso2[$i]), 'UTF-8');
    }
    return $flag !== '' ? $flag : null;
}

/**
 * Get daily transaction limit for account type from system settings
 */
function getDailyLimitForAccountType($accountType) {
    return getAccountLimit($accountType, 'daily');
}

/**
 * Get monthly transaction limit for account type from system settings
 */
function getMonthlyLimitForAccountType($accountType) {
    return getAccountLimit($accountType, 'monthly');
}

function formatDate($date, $format = 'M d, Y') {
    return date($format, strtotime($date));
}

function formatDateTime($datetime) {
    return date('M d, Y h:i A', strtotime($datetime));
}

function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

function generateOTP($length = 6) {
    return str_pad(random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
}

function encryptData($data) {
    $key = ENCRYPTION_KEY;
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
    return base64_encode($encrypted . '::' . $iv);
}

function decryptData($data) {
    if (empty($data) || $data === null) {
        return null;
    }
    
    $key = ENCRYPTION_KEY;
    $decoded = base64_decode($data);
    
    if ($decoded === false || strpos($decoded, '::') === false) {
        return null;
    }
    
    list($encrypted_data, $iv) = explode('::', $decoded, 2);
    return openssl_decrypt($encrypted_data, 'aes-256-cbc', $key, 0, $iv);
}

function sendEmail($to, $subject, $message, $isHTML = true, $replyTo = null, $plainTextOverride = null, $fromName = null) {
    try {
        // Load PHPMailer classes
        require_once __DIR__ . '/../PHPMailer/PHPMailer.php';
        require_once __DIR__ . '/../PHPMailer/SMTP.php';
        require_once __DIR__ . '/../PHPMailer/Exception.php';
        
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        
        // Server settings - Use SMTP instead of mail()
        $mail->isSMTP();

        // ===== SMTP DEBUG (TEMP) =====
        // To enable: set environment variable SMTP_DEBUG=1 on your server (recommended),
        // or add `define('SMTP_DEBUG', true);` somewhere before calling sendEmail().
        // Output goes to the PHP error log.
        $smtpDebugEnabled = false;
        if (defined('SMTP_DEBUG') && constant('SMTP_DEBUG')) {
            $smtpDebugEnabled = true;
        } elseif (!empty($_ENV['SMTP_DEBUG']) && $_ENV['SMTP_DEBUG'] == '1') {
            $smtpDebugEnabled = true;
        } elseif (!empty($_SERVER['SMTP_DEBUG']) && $_SERVER['SMTP_DEBUG'] == '1') {
            $smtpDebugEnabled = true;
        }

        if ($smtpDebugEnabled) {
            $mail->SMTPDebug = 2;              // 1=client, 2=client+server
            $mail->Debugoutput = 'error_log';  // log to PHP error log
        }
        // ==============================

        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;

        // Prevent SMTP hangs from blocking requests
        $mail->Timeout = 10;
        
        // Determine encryption based on port
        // PHPMailer 6.0.5 uses string values: 'ssl' for port 465, 'tls' for port 587
        if (SMTP_PORT == 465) {
            $mail->SMTPSecure = 'ssl'; // SSL encryption
        } elseif (SMTP_PORT == 587) {
            $mail->SMTPSecure = 'tls'; // TLS encryption
        }
        
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';
        
        // Enable verbose debug output (disable in production)
        // Uncomment the line below for debugging if emails aren't sending
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        
        // Recipients
        $fromNameToUse = $fromName ?? SMTP_FROM_NAME;
        $mail->setFrom(SMTP_FROM, $fromNameToUse);
        $mail->addAddress($to);
        
        // Reply-To
        if ($replyTo) {
            $mail->addReplyTo($replyTo);
        } else {
            $mail->addReplyTo(SMTP_FROM);
        }
        
        // Content
        $mail->isHTML($isHTML);
        $mail->Subject = $subject;
        
        if ($isHTML) {
            $mail->Body = $message;
            $plainText = $plainTextOverride;
            if ($plainText === null) {
                $plainText = trim(
                    preg_replace(
                        "/[ \t]+/",
                        ' ',
                        html_entity_decode(strip_tags($message), ENT_QUOTES, 'UTF-8')
                    )
                );
            }
            if ($plainText === '') {
                $plainText = 'This email contains HTML content. Please view it in an HTML-capable email client.';
            }
            $mail->AltBody = $plainText;
        } else {
            $mail->Body = $message;
        }
        
        // Additional headers for deliverability
        // IMPORTANT: Do NOT add a custom Message-ID header.
        // PHPMailer generates Message-ID automatically; adding another causes Gmail to reject
        // the email as "not RFC 5322 compliant" (multiple Message-ID headers).
        $mail->addCustomHeader('List-Unsubscribe', '<' . SITE_URL . '/unsubscribe>, <mailto:' . SMTP_FROM . '?subject=Unsubscribe>');
        $mail->addCustomHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
        $mail->addCustomHeader('Precedence', 'bulk');
        $mail->addCustomHeader('X-Priority', '3');
        $mail->addCustomHeader('Importance', 'Normal');
        $mail->addCustomHeader('Auto-Submitted', 'auto-generated');
        
        // Send email using SMTP (bypasses mail() limits)
        $result = $mail->send();
        
        if (!$result) {
            error_log("Email error: Failed to send email to $to - " . $mail->ErrorInfo);
            return false;
        }
        
        return true;
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        error_log("Email error: " . $e->getMessage());
        return false;
    } catch (\Exception $e) {
        error_log("Email error: " . $e->getMessage());
        return false;
    }
}

function sendSMS($to, $message) {
    // Twilio integration
    $sid = TWILIO_SID;
    $token = TWILIO_TOKEN;
    $from = TWILIO_FROM;
    
    $url = "https://api.twilio.com/2010-04-01/Accounts/$sid/Messages.json";
    
    $data = [
        'From' => $from,
        'To' => $to,
        'Body' => $message
    ];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, "$sid:$token");
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return $httpCode == 201;
}

/**
 * Map UI/admin transaction status to values allowed in the transactions.status enum.
 * The UI uses on_hold; MySQL enum uses pending (see admin-adjust-balance.php).
 */
function adminMapTransactionStatusForDb(string $status): string
{
    $status = strtolower(trim($status));
    if ($status === 'on_hold') {
        return 'pending';
    }
    return $status;
}

/**
 * Statuses that mean the transfer/payment succeeded (funds moved).
 */
function isSuccessfulTransactionStatus($status): bool
{
    return in_array(strtolower(trim((string)$status)), ['successful', 'completed'], true);
}

/**
 * Statuses that count toward transfer limits / posted activity.
 */
function isPostedTransactionStatus($status): bool
{
    return in_array(
        strtolower(trim((string)$status)),
        ['successful', 'completed', 'pending', 'processing'],
        true
    );
}

/**
 * User-facing status label for receipts and UI.
 */
function formatTransactionStatusLabel($status): string
{
    $status = strtolower(trim((string)$status));
    $labels = [
        'successful' => 'SUCCESSFUL',
        'completed' => 'COMPLETED',
        'pending' => 'PENDING',
        'processing' => 'PROCESSING',
        'failed' => 'FAILED',
        'reversed' => 'REVERSED',
        'cancelled' => 'CANCELLED',
        'on_hold' => 'ON HOLD',
    ];
    return $labels[$status] ?? strtoupper(str_replace('_', ' ', $status));
}

/**
 * Whether deleting this transaction should adjust the account balance.
 */
function adminShouldReverseBalanceOnDelete(array $transaction): bool
{
    return in_array($transaction['status'] ?? '', ['successful', 'completed', 'pending', 'processing', 'on_hold'], true);
}

/**
 * Balance adjustment when admin deletes a transaction.
 * Credit removed → subtract amount. Debit removed → credit back amount + fee.
 */
function adminBalanceChangeOnDelete(array $transaction): float
{
    if (!adminShouldReverseBalanceOnDelete($transaction)) {
        return 0.0;
    }

    $amount = (float)($transaction['amount'] ?? 0);
    $fee = (float)($transaction['fee'] ?? 0);

    if (($transaction['transaction_type'] ?? '') === 'credit') {
        return -$amount;
    }

    return $amount + $fee;
}

/**
 * Extract generator batch id from transaction metadata JSON.
 */
function adminParseGeneratorBatchId(array $transaction): ?string
{
    $meta = $transaction['metadata'] ?? null;
    if ($meta === null || $meta === '') {
        return null;
    }
    $decoded = is_array($meta) ? $meta : json_decode((string)$meta, true);
    if (!is_array($decoded)) {
        return null;
    }
    $batchId = $decoded['generator_batch_id'] ?? null;
    return $batchId ? (string)$batchId : null;
}

/**
 * Compute per-account balance deltas when admin deletes transactions.
 * Generator (GEN) batches use history_impact from the batch record, not per-row sums,
 * because the account balance was adjusted once at generation time.
 */
function adminComputeDeletionBalanceDeltas(Database $db, array $transactions): array
{
    $byAccount = [];
    $batchGroups = [];

    foreach ($transactions as $row) {
        $batchId = adminParseGeneratorBatchId($row);
        if ($batchId) {
            $batchGroups[$batchId][] = $row;
        } else {
            $accountId = (int)$row['account_id'];
            $byAccount[$accountId] = ($byAccount[$accountId] ?? 0) + adminBalanceChangeOnDelete($row);
        }
    }

    foreach ($batchGroups as $batchId => $rows) {
        $batchStmt = $db->query(
            "SELECT batch_id, account_id, history_impact, transaction_count, status
             FROM transaction_generation_batches
             WHERE batch_id = ?
             LIMIT 1",
            [$batchId]
        );
        $batch = $batchStmt ? $batchStmt->fetch() : false;
        $accountId = (int)($batch['account_id'] ?? $rows[0]['account_id'] ?? 0);
        if (!$accountId) {
            continue;
        }

        if ($batch && ($batch['status'] ?? '') === 'completed') {
            $totalCount = max(1, (int)$batch['transaction_count']);
            $historyImpact = (float)$batch['history_impact'];
            $fraction = count($rows) / $totalCount;
            $byAccount[$accountId] = ($byAccount[$accountId] ?? 0) - round($historyImpact * $fraction, 2);
        } else {
            foreach ($rows as $row) {
                $byAccount[$accountId] = ($byAccount[$accountId] ?? 0) + adminBalanceChangeOnDelete($row);
            }
        }
    }

    return $byAccount;
}

/**
 * Mark generator batches as undone when no transactions remain for that batch.
 */
function adminMarkEmptyGeneratorBatchesUndone(Database $db, array $batchIds): void
{
    foreach (array_unique(array_filter($batchIds)) as $batchId) {
        $batchStmt = $db->query(
            "SELECT account_id FROM transaction_generation_batches
             WHERE batch_id = ? AND status = 'completed' LIMIT 1",
            [$batchId]
        );
        $batch = $batchStmt ? $batchStmt->fetch() : false;
        if (!$batch) {
            continue;
        }

        $cntStmt = $db->query(
            "SELECT COUNT(*) AS cnt FROM transactions
             WHERE account_id = ?
               AND JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.generator_batch_id')) = ?",
            [(int)$batch['account_id'], $batchId]
        );
        $remaining = (int)(($cntStmt ? $cntStmt->fetch() : [])['cnt'] ?? 0);
        if ($remaining === 0) {
            $db->query(
                "UPDATE transaction_generation_batches SET status = 'undone', updated_at = NOW() WHERE batch_id = ?",
                [$batchId]
            );
        }
    }
}

/**
 * Net balance effect of a transaction row when it counts against the account.
 */
function adminTransactionBalanceEffect(array $transaction): float
{
    if (!adminShouldReverseBalanceOnDelete($transaction)) {
        return 0.0;
    }
    $amount = (float)($transaction['amount'] ?? 0);
    $fee = (float)($transaction['fee'] ?? 0);
    if (($transaction['transaction_type'] ?? '') === 'credit') {
        return $amount;
    }
    return -($amount + $fee);
}

/**
 * Delta to apply when admin edits a transaction (amount, fee, status).
 */
function adminComputeEditBalanceDelta(array $oldTxn, array $newTxn): float
{
    $oldEffect = adminTransactionBalanceEffect($oldTxn);
    $newEffect = adminTransactionBalanceEffect($newTxn);
    return round($newEffect - $oldEffect, 2);
}

/**
 * Find paired internal transfer credit for a sender debit.
 */
function adminFindInternalTransferPair(PDO $conn, array $senderTxn): ?array
{
    if (($senderTxn['transaction_type'] ?? '') !== 'debit' || ($senderTxn['category'] ?? '') !== 'transfer') {
        return null;
    }
    $meta = json_decode($senderTxn['metadata'] ?? '{}', true);
    if (!is_array($meta)) {
        $meta = [];
    }
    if (($meta['transfer_scope'] ?? $meta['transfer_type'] ?? '') !== 'internal') {
        return null;
    }
    $recipientAccountNumber = $senderTxn['recipient_account'] ?? null;
    if (!$recipientAccountNumber) {
        return null;
    }
    $stmt = $conn->prepare("SELECT account_number FROM accounts WHERE id = ?");
    $stmt->execute([(int)$senderTxn['account_id']]);
    $senderAccount = $stmt->fetch(PDO::FETCH_ASSOC);
    $senderAccountNumber = $senderAccount['account_number'] ?? null;
    if (!$senderAccountNumber) {
        return null;
    }
    $createdAt = $senderTxn['created_at'];
    $sql = "SELECT t.* FROM transactions t
            JOIN accounts a ON t.account_id = a.id
            WHERE a.account_number = ?
              AND t.transaction_type = 'credit'
              AND t.category = 'transfer'
              AND t.status IN ('completed', 'pending', 'processing', 'on_hold')
              AND t.recipient_account = ?
              AND t.created_at BETWEEN DATE_SUB(?, INTERVAL 5 MINUTE) AND DATE_ADD(?, INTERVAL 5 MINUTE)
              AND ABS(t.amount - ?) < 0.01
            ORDER BY t.created_at DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        $recipientAccountNumber,
        $senderAccountNumber,
        $createdAt,
        $createdAt,
        (float)$senderTxn['amount'],
    ]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

/**
 * Reverse recipient side of internal transfer when sender is deleted or failed.
 */
function adminReverseInternalTransferPair(PDO $conn, array $senderTxn): void
{
    $recipientTxn = adminFindInternalTransferPair($conn, $senderTxn);
    if (!$recipientTxn) {
        return;
    }
    $recipientAccountId = (int)$recipientTxn['account_id'];
    $change = -((float)$recipientTxn['amount']);
    $conn->prepare(
        "UPDATE accounts SET balance = balance + ?, available_balance = available_balance + ?, updated_at = NOW() WHERE id = ?"
    )->execute([$change, $change, $recipientAccountId]);
    $conn->prepare("UPDATE transactions SET status = 'failed', completed_at = NULL WHERE id = ?")
        ->execute([(int)$recipientTxn['id']]);
}

/**
 * When deleting an internal transfer sender debit, remove paired recipient credit and return balance deltas.
 */
function adminResolveInternalTransferPairOnDelete(Database $db, array $senderTxn): array
{
    $conn = $db->getConnection();
    $pair = adminFindInternalTransferPair($conn, $senderTxn);
    if (!$pair) {
        return [];
    }
    $accountId = (int)$pair['account_id'];
    $delta = adminBalanceChangeOnDelete($pair);
    $db->query("DELETE FROM transactions WHERE id = ?", [(int)$pair['id']]);
    return [$accountId => $delta];
}

/**
 * Find sender debit for an internal transfer credit (inverse of adminFindInternalTransferPair).
 */
function adminFindInternalTransferSenderDebit(PDO $conn, array $creditTxn): ?array
{
    if (($creditTxn['transaction_type'] ?? '') !== 'credit' || ($creditTxn['category'] ?? '') !== 'transfer') {
        return null;
    }
    $meta = json_decode($creditTxn['metadata'] ?? '{}', true);
    if (!is_array($meta)) {
        $meta = [];
    }
    $scope = $meta['transfer_scope'] ?? $meta['transfer_type'] ?? '';
    if ($scope !== 'internal') {
        return null;
    }
    $senderAccountNumber = $creditTxn['recipient_account'] ?? null;
    if (!$senderAccountNumber) {
        return null;
    }
    $stmt = $conn->prepare('SELECT account_number FROM accounts WHERE id = ?');
    $stmt->execute([(int)$creditTxn['account_id']]);
    $recipientAccount = $stmt->fetch(PDO::FETCH_ASSOC);
    $recipientAccountNumber = $recipientAccount['account_number'] ?? null;
    if (!$recipientAccountNumber) {
        return null;
    }
    $createdAt = $creditTxn['created_at'];
    $sql = "SELECT t.* FROM transactions t
            JOIN accounts a ON t.account_id = a.id
            WHERE a.account_number = ?
              AND t.transaction_type = 'debit'
              AND t.category = 'transfer'
              AND t.status IN ('completed', 'pending', 'processing', 'on_hold')
              AND t.recipient_account = ?
              AND t.created_at BETWEEN DATE_SUB(?, INTERVAL 5 MINUTE) AND DATE_ADD(?, INTERVAL 5 MINUTE)
              AND ABS(t.amount - ?) < 0.01
            ORDER BY t.created_at DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        $senderAccountNumber,
        $recipientAccountNumber,
        $createdAt,
        $createdAt,
        (float)$creditTxn['amount'],
    ]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

/**
 * Wipe transaction history and zero balance for one account or all of a user's accounts.
 */
function adminClearUserAccountHistory(Database $db, int $userId, int $accountId, string $reason): array
{
    if ($accountId <= 0) {
        throw new InvalidArgumentException('Account ID is required');
    }

    $userStmt = $db->query(
        "SELECT id, email, role FROM users WHERE id = ? LIMIT 1",
        [$userId]
    );
    $user = $userStmt ? $userStmt->fetch() : false;
    if (!$user || ($user['role'] ?? '') === 'admin') {
        throw new InvalidArgumentException('User not found or cannot be cleared');
    }

    $conn = $db->getConnection();
    $db->beginTransaction();

    try {
        $acctStmt = $db->query(
            "SELECT id, account_number, balance FROM accounts WHERE id = ? AND user_id = ? FOR UPDATE",
            [$accountId, $userId]
        );
        $account = $acctStmt ? $acctStmt->fetch() : false;
        if (!$account) {
            throw new InvalidArgumentException('Account not found for this user');
        }

        $txStmt = $db->query(
            "SELECT * FROM transactions WHERE account_id = ?",
            [$accountId]
        );
        $rows = $txStmt ? $txStmt->fetchAll() : [];
        $deletedCount = count($rows);

        $pairIds = [];
        $pairDeltas = [];
        foreach ($rows as $row) {
            $pair = null;
            if (($row['transaction_type'] ?? '') === 'debit') {
                $pair = adminFindInternalTransferPair($conn, $row);
            } elseif (($row['transaction_type'] ?? '') === 'credit') {
                $pair = adminFindInternalTransferSenderDebit($conn, $row);
            }
            if (!$pair || (int)$pair['account_id'] === $accountId) {
                continue;
            }
            $pid = (int)$pair['id'];
            if (isset($pairIds[$pid])) {
                continue;
            }
            $pairIds[$pid] = true;
            $otherAccountId = (int)$pair['account_id'];
            $pairDeltas[$otherAccountId] = ($pairDeltas[$otherAccountId] ?? 0) + adminBalanceChangeOnDelete($pair);
        }

        $db->query('DELETE FROM transactions WHERE account_id = ?', [$accountId]);
        $deletedCount += count($pairIds);

        if (!empty($pairIds)) {
            $placeholders = implode(',', array_fill(0, count($pairIds), '?'));
            $db->query("DELETE FROM transactions WHERE id IN ($placeholders)", array_keys($pairIds));
        }

        foreach ($pairDeltas as $otherAccountId => $delta) {
            if (abs($delta) < 0.00001) {
                continue;
            }
            $balStmt = $db->query(
                'SELECT balance FROM accounts WHERE id = ? FOR UPDATE',
                [$otherAccountId]
            );
            $other = $balStmt ? $balStmt->fetch() : false;
            if (!$other) {
                continue;
            }
            $newBalance = round((float)$other['balance'] + $delta, 2);
            if ($newBalance < 0) {
                throw new RuntimeException('Clear would cause negative balance on linked account #' . $otherAccountId);
            }
            $db->query(
                'UPDATE accounts SET balance = ?, available_balance = ?, updated_at = NOW() WHERE id = ?',
                [$newBalance, $newBalance, $otherAccountId]
            );
        }

        $db->query(
            'UPDATE accounts SET balance = 0, available_balance = 0, updated_at = NOW() WHERE id = ?',
            [$accountId]
        );

        $db->query(
            "UPDATE transaction_generation_batches SET status = 'undone', updated_at = NOW()
             WHERE account_id = ? AND status = 'completed'",
            [$accountId]
        );

        $db->commit();

        logActivity(
            $_SESSION['user_id'] ?? 0,
            'ADMIN_CLEAR_ACCOUNT',
            sprintf(
                'Cleared account #%s (id %d) for user %s. Deleted %d transaction(s). Reason: %s',
                $account['account_number'],
                $accountId,
                $user['email'],
                $deletedCount,
                $reason
            )
        );

        return [
            'scope' => 'account',
            'account_id' => $accountId,
            'account_number' => $account['account_number'],
            'deleted_count' => $deletedCount,
            'accounts_zeroed' => 1,
        ];
    } catch (Throwable $e) {
        if ($db->inTransaction()) {
            $db->rollback();
        }
        throw $e;
    }
}

function logActivity($userId, $action, $details = null) {
    $db = Database::getInstance();
    $sql = "INSERT INTO activity_logs (user_id, action, details, ip_address, user_agent, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())";
    
    $db->query($sql, [
        $userId,
        $action,
        $details,
        $_SERVER['REMOTE_ADDR'],
        $_SERVER['HTTP_USER_AGENT']
    ]);
}

function getExchangeRate($from, $to) {
    if (!class_exists('ExchangeRates')) {
        require_once __DIR__ . '/exchange-rates.php';
    }
    return ExchangeRates::getInstance()->getRate($from, $to);
}

function generateAccountNumber() {
    return date('Y') . random_int(10000000, 99999999);
}

function generateCardNumber() {
    // Generate a valid-looking card number (not real)
    $prefix = '4532'; // Visa prefix
    $number = $prefix;
    
    for ($i = 0; $i < 12; $i++) {
        $number .= random_int(0, 9);
    }
    
    return chunk_split($number, 4, ' ');
}

function generateCVV() {
    return str_pad(random_int(0, 999), 3, '0', STR_PAD_LEFT);
}

function maskCardNumber($cardNumber) {
    $cardNumber = str_replace(' ', '', $cardNumber);
    return '**** **** **** ' . substr($cardNumber, -4);
}

function maskEmail($email) {
    $parts = explode('@', $email);
    $name = $parts[0];
    $domain = $parts[1];
    
    $maskedName = substr($name, 0, 2) . str_repeat('*', strlen($name) - 2);
    return $maskedName . '@' . $domain;
}

function uploadFile($file, $folder = 'documents') {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'No file uploaded or upload error'];
    }
    
    // Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'File size exceeds limit'];
    }
    
    // Check if file is empty
    if ($file['size'] === 0) {
        return ['success' => false, 'message' => 'File is empty'];
    }
    
    // Check extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_EXTENSIONS)) {
        return ['success' => false, 'message' => 'File type not allowed'];
    }
    
    // MIME type validation map
    $allowedMimeTypes = [
        'jpg' => ['image/jpeg', 'image/jpg'],
        'jpeg' => ['image/jpeg', 'image/jpg'],
        'png' => ['image/png'],
        'pdf' => ['application/pdf']
    ];
    
    // Get actual MIME type from file
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    // Validate MIME type matches extension
    if (!isset($allowedMimeTypes[$extension]) || !in_array($mimeType, $allowedMimeTypes[$extension])) {
        error_log("Security: MIME type mismatch for uploaded file. Extension: $extension, MIME: $mimeType, Filename: " . $file['name']);
        return ['success' => false, 'message' => 'File type validation failed'];
    }
    
    // Additional security: Check file content for image files
    if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
        $imageInfo = @getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            return ['success' => false, 'message' => 'Invalid image file'];
        }
        
        // Verify image dimensions are reasonable (prevent decompression bombs)
        $maxDimension = 10000; // 10,000 pixels
        if ($imageInfo[0] > $maxDimension || $imageInfo[1] > $maxDimension) {
            return ['success' => false, 'message' => 'Image dimensions too large'];
        }
    }
    
    // Sanitize filename
    $originalName = preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($file['name'], PATHINFO_FILENAME));
    
    // Generate unique filename
    $filename = uniqid() . '_' . time() . '_' . $originalName . '.' . $extension;
    $uploadPath = UPLOAD_PATH . $folder . '/';
    
    // Create directory if it doesn't exist
    if (!file_exists($uploadPath)) {
        mkdir($uploadPath, 0755, true);
    }
    
    // Add .htaccess to prevent direct execution in upload directories
    $htaccessPath = $uploadPath . '.htaccess';
    if (!file_exists($htaccessPath)) {
        $htaccessContent = "Options -ExecCGI\n";
        $htaccessContent .= "AddHandler cgi-script .php .pl .py .jsp .asp .sh .cgi\n";
        $htaccessContent .= "<FilesMatch \"\\.(php|pl|py|jsp|asp|sh|cgi)$\">\n";
        $htaccessContent .= "    Deny from all\n";
        $htaccessContent .= "</FilesMatch>\n";
        file_put_contents($htaccessPath, $htaccessContent);
    }
    
    $destination = $uploadPath . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        // Verify file was actually moved and is not executable
        chmod($destination, 0644); // Read/write for owner, read for others
        return ['success' => true, 'filename' => $filename, 'path' => $folder . '/' . $filename];
    }
    
    return ['success' => false, 'message' => 'Failed to move uploaded file'];
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

function timeAgo($timestamp) {
    // Convert to timestamp if it's a date string
    if (!is_numeric($timestamp)) {
        $timestamp = strtotime($timestamp);
    }
    
    $now = time();
    $diff = $now - $timestamp;
    
    if ($diff < 60) {
        return 'Just now';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ($mins == 1 ? ' minute ago' : ' minutes ago');
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ($hours == 1 ? ' hour ago' : ' hours ago');
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ($days == 1 ? ' day ago' : ' days ago');
    } elseif ($diff < 2592000) {
        $weeks = floor($diff / 604800);
        return $weeks . ($weeks == 1 ? ' week ago' : ' weeks ago');
    } elseif ($diff < 31536000) {
        $months = floor($diff / 2592000);
        return $months . ($months == 1 ? ' month ago' : ' months ago');
    } else {
        $years = floor($diff / 31536000);
        return $years . ($years == 1 ? ' year ago' : ' years ago');
    }
}

// getSystemSetting() is now defined in includes/system-settings.php
// Keeping this check here to prevent redeclaration if functions.php is loaded first
if (!function_exists('getSystemSetting')) {
    function getSystemSetting($key, $default = null) {
        // Use SystemSettings class if available, otherwise fallback to direct DB query
        if (class_exists('SystemSettings')) {
            return SystemSettings::getInstance()->get($key, $default);
        }
        
        try {
            $db = Database::getInstance();
            $sql = "SELECT setting_value FROM system_settings WHERE setting_key = ?";
            $stmt = $db->query($sql, [$key]);
            $result = $stmt->fetch();
            
            return $result ? $result['setting_value'] : $default;
        } catch (Exception $e) {
            error_log("Error getting system setting '$key': " . $e->getMessage());
            return $default;
        }
    }
}

// Shared helpers used across views, controllers, and APIs (load once with functions.php).
require_once __DIR__ . '/transaction-categories.php';
require_once __DIR__ . '/countries.php';