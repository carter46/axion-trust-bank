<?php
$pageTitle = 'System Settings - Admin';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/currency.php';

require_once __DIR__ . '/../../includes/kyc-config.php';

requireAdmin();

// Get supported currencies for dropdown
$currencyHelper = new Currency();
$supportedCurrencies = $currencyHelper->getSupportedCurrencies();

// Handle settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_settings'])) {
    $systemSettings = SystemSettings::getInstance();
    $db = Database::getInstance();
    $userId = $_SESSION['user_id'];
    
    $errors = [];
    $updatedCount = 0;

    // Validate KYC custom fields JSON before general settings loop
    if (isset($_POST['setting_kyc_custom_fields'])) {
        $rawJson = trim((string)$_POST['setting_kyc_custom_fields']);
        if ($rawJson === '') {
            $_POST['setting_kyc_custom_fields'] = '[]';
        } else {
            $decoded = json_decode($rawJson, true);
            if (!is_array($decoded)) {
                $errors[] = 'KYC custom fields must be valid JSON array';
            } else {
                $_POST['setting_kyc_custom_fields'] = json_encode(parseKycCustomFields($decoded));
            }
        }
    }
    
    foreach ($_POST as $key => $value) {
        if ($key !== 'update_settings' && strpos($key, 'setting_') === 0) {
            $settingKey = substr($key, 8); // Remove 'setting_' prefix
            
            // Get setting metadata to validate
            $settingSql = "SELECT setting_type, description FROM system_settings WHERE setting_key = ? LIMIT 1";
            $settingStmt = $db->query($settingSql, [$settingKey]);
            $settingMeta = $settingStmt ? $settingStmt->fetch() : null;
            
            // Validate based on type
            if ($settingMeta && isset($settingMeta['setting_type'])) {
                $settingType = $settingMeta['setting_type'];
                
                // Validate number type
                if ($settingType === 'number') {
                    if (!is_numeric($value) && !empty($value)) {
                        $errors[] = "Setting '{$settingKey}' must be a number";
                        continue;
                    }
                    // Ensure non-negative for amounts/fees
                    if (strpos($settingKey, 'fee') !== false || strpos($settingKey, 'limit') !== false || strpos($settingKey, 'amount') !== false) {
                        if (floatval($value) < 0) {
                            $errors[] = "Setting '{$settingKey}' cannot be negative";
                            continue;
                        }
                    }
                }
                
                // Validate boolean type
                if ($settingType === 'boolean') {
                    // For checkboxes: value will be '1' if checked, otherwise it won't be in POST at all
                    // Since we're in the loop, the key exists, so check if value is '1'
                    $value = ($value === '1') ? '1' : '0';
                }
            }
            
            // Validate currency setting
            if ($settingKey === 'default_currency') {
                $supportedCurrencies = array_keys($currencyHelper->getSupportedCurrencies());
                if (!in_array($value, $supportedCurrencies)) {
                    $errors[] = "Invalid currency code: {$value}";
                    continue;
                }
            }

            // Keep default currency aligned when operating country changes
            if ($settingKey === 'bank_operating_country') {
                if ($systemSettings->update($settingKey, $value, $userId)) {
                    $updatedCount++;
                    $expectedCurrency = getCurrencyForOperatingCountry($value);
                    $supportedCurrencies = array_keys($currencyHelper->getSupportedCurrencies());
                    if ($expectedCurrency && in_array($expectedCurrency, $supportedCurrencies, true)) {
                        if ($systemSettings->update('default_currency', $expectedCurrency, $userId)) {
                            $updatedCount++;
                            $_SESSION['settings_sync_note'] = "Default currency was updated to {$expectedCurrency} to match the operating country.";
                        }
                    }
                } else {
                    $errors[] = "Failed to update setting: {$settingKey}";
                }
                continue;
            }
            
            // Update setting
            if ($systemSettings->update($settingKey, $value, $userId)) {
                $updatedCount++;
            } else {
                $errors[] = "Failed to update setting: {$settingKey}";
            }
        }
    }
    
    // Handle boolean settings that weren't submitted (unchecked checkboxes)
    // Get all boolean settings and check if they're missing from POST
    $booleanSql = "SELECT setting_key FROM system_settings WHERE setting_type = 'boolean'";
    $booleanStmt = $db->query($booleanSql);
    $booleanSettings = $booleanStmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($booleanSettings as $boolKey) {
        $postKey = 'setting_' . $boolKey;
        if (!isset($_POST[$postKey])) {
            // Checkbox was unchecked, set to '0'
            if ($systemSettings->update($boolKey, '0', $userId)) {
                $updatedCount++;
            }
        }
    }
    
    if (!empty($errors)) {
        $_SESSION['error_message'] = 'Some settings could not be updated: ' . implode(', ', $errors);
    } else {
        $successMsg = "Successfully updated {$updatedCount} setting(s)!";
        if (!empty($_SESSION['settings_sync_note'])) {
            $successMsg .= ' ' . $_SESSION['settings_sync_note'];
            unset($_SESSION['settings_sync_note']);
        }
        $_SESSION['success_message'] = $successMsg;
    }
    
    // Reload settings cache
    $systemSettings->reload();
    
    header('Location: ' . SITE_URL . '/admin/system-settings');
    exit;
}

// Get all settings grouped by category
$systemSettings = SystemSettings::getInstance();
$db = Database::getInstance();

// Ensure critical settings exist in database
$criticalSettings = [
    'allow_new_registrations' => [
        'value' => '1',
        'type' => 'boolean',
        'description' => 'Allow new user registrations. When disabled, users cannot create new accounts.'
    ],
    'maintenance_mode' => [
        'value' => '0',
        'type' => 'boolean',
        'description' => 'Enable maintenance mode. When enabled, only admins can access the site.'
    ],
    'require_kyc' => [
        'value' => '1',
        'type' => 'boolean',
        'description' => 'Require KYC verification for all users. When enabled, users must complete KYC to access certain features.'
    ],
    'two_factor_required' => [
        'value' => '0',
        'type' => 'boolean',
        'description' => 'Suggest 2FA for users (informational). Does not lock users out of the app when 2FA is disabled.'
    ],
    'disable_2fa_entirely' => [
        'value' => '0',
        'type' => 'boolean',
        'description' => 'Disable 2FA entirely for all users. When enabled, users cannot enable 2FA and existing 2FA will be disabled. This overrides the "Force 2FA" setting.'
    ],
    'force_security_setup' => [
        'value' => '1',
        'type' => 'boolean',
        'description' => 'Require Login PIN and Transfer PIN setup before dashboard access. When disabled, users can skip onboarding (demo/sales mode).'
    ],
    'require_transfer_pin' => [
        'value' => '1',
        'type' => 'boolean',
        'description' => 'Require Transfer PIN for transactions. When enabled, users must enter their Transfer PIN for all transfers.'
    ],
    'max_login_attempts' => [
        'value' => '3',
        'type' => 'number',
        'description' => 'Maximum failed login attempts before account lockout.'
    ],
    'login_lockout_duration' => [
        'value' => '15',
        'type' => 'number',
        'description' => 'Lockout duration in minutes after exceeding max login attempts.'
    ],
    'email_on_login' => [
        'value' => '0',
        'type' => 'boolean',
        'description' => 'Send email notification on login. When enabled, users receive an email each time they log in.'
    ],
    'enable_currency_conversion' => [
        'value' => '1',
        'type' => 'boolean',
        'description' => 'Enable currency conversion. When enabled, users can view balances and amounts in their preferred currency. When disabled, all amounts are displayed in the site default currency.'
    ],
    'exchange_rate_api_key' => [
        'value' => '',
        'type' => 'string',
        'description' => 'ExchangeRate-API v6 API key for live FX rates. Leave empty to use cached or built-in offline fallback rates.'
    ],
    'bank_operating_country' => [
        'value' => 'United States',
        'type' => 'string',
        'description' => 'Country where the bank operates'
    ],
    'bank_operating_region' => [
        'value' => 'north-america',
        'type' => 'string',
        'description' => 'Bank operating region used for transfer rails and domestic bank lists'
    ],
    'transfer_internal_fee' => [
        'value' => '0',
        'type' => 'number',
        'description' => 'Internal transfer fee percentage charged within the same bank'
    ],
    'transfer_domestic_fee' => [
        'value' => '0.5',
        'type' => 'number',
        'description' => 'Domestic transfer fee percentage charged within the operating country'
    ],
    'transfer_international_fee' => [
        'value' => '2.5',
        'type' => 'number',
        'description' => 'International wire transfer fee percentage'
    ],
    'kyc_use_custom_fields' => [
        'value' => '0',
        'type' => 'boolean',
        'description' => 'KYC: Use custom admin-defined fields instead of country profile defaults'
    ],
    'kyc_custom_fields' => [
        'value' => '[]',
        'type' => 'json',
        'description' => 'KYC: JSON array of custom field definitions (key, label, type, required, pattern, step)'
    ]
];

foreach ($criticalSettings as $key => $config) {
    $checkSql = "SELECT id FROM system_settings WHERE setting_key = ? LIMIT 1";
    $checkStmt = $db->query($checkSql, [$key]);
    if (!$checkStmt->fetch()) {
        // Setting doesn't exist, create it
        $insertSql = "INSERT INTO system_settings (setting_key, setting_value, setting_type, description, created_at, updated_at) 
                      VALUES (?, ?, ?, ?, NOW(), NOW())";
        $db->query($insertSql, [$key, $config['value'], $config['type'], $config['description']]);
    }
}

$sql = "SELECT * FROM system_settings ORDER BY id ASC";
$stmt = $db->query($sql);
$allSettings = $stmt->fetchAll();

// Group settings by category
$settingsGroups = [
    'Site Branding & Identity' => [],
    'Bank Operations' => [],
    'Transfer Limits & Fees' => [],
    'Account Settings' => [],
    'Security & Compliance' => [],
    'Notifications' => [],
    'Other' => []
];

foreach ($allSettings as $setting) {
    $desc = $setting['description'];
    $key = $setting['setting_key'];
    
    // Exclude settings that are managed on dedicated sub-UIs only
    $excludedSettings = [
        'kyc_custom_fields',
        'kyc_use_custom_fields',
    ];
    
    if (in_array($key, $excludedSettings)) {
        continue;
    }
    
    if (strpos($desc, 'Website') !== false || strpos($desc, 'Site') !== false || strpos($desc, 'contact') !== false || strpos($desc, 'support') !== false || strpos($desc, 'logo') !== false || strpos($desc, 'tagline') !== false || strpos($desc, 'address') !== false || strpos($desc, 'phone') !== false) {
        $settingsGroups['Site Branding & Identity'][] = $setting;
    } elseif (strpos($desc, 'bank') !== false && (strpos($desc, 'operates') !== false || strpos($desc, 'operating') !== false)
        || $key === 'bank_operating_country' || $key === 'bank_operating_region') {
        $settingsGroups['Bank Operations'][] = $setting;
    } elseif (strpos($desc, 'currency') !== false || $key === 'enable_currency_conversion' || $key === 'default_currency' || $key === 'exchange_rate_api_key') {
        $settingsGroups['Bank Operations'][] = $setting;
    } elseif (strpos($desc, 'transfer') !== false || strpos($key, 'transfer_') === 0 || (strpos($desc, 'limit') !== false && strpos($desc, 'account') === false)) {
        $settingsGroups['Transfer Limits & Fees'][] = $setting;
    } elseif (strpos($desc, 'account') !== false || strpos($desc, 'interest') !== false || strpos($desc, 'overdraft') !== false || strpos($desc, 'maintenance fee') !== false || strpos($desc, 'daily limit') !== false || strpos($desc, 'monthly limit') !== false) {
        $settingsGroups['Account Settings'][] = $setting;
    } elseif (strpos($desc, 'KYC') !== false || strpos($desc, '2FA') !== false || strpos($desc, 'security') !== false || strpos($desc, 'maintenance mode') !== false || strpos($desc, 'registration') !== false || strpos($desc, 'login') !== false || strpos($desc, 'PIN') !== false || strpos($desc, 'session') !== false) {
        $settingsGroups['Security & Compliance'][] = $setting;
    } elseif (strpos($key, 'kyc_') === 0) {
        $settingsGroups['Security & Compliance'][] = $setting;
    } elseif (strpos($desc, 'notification') !== false || strpos($desc, 'email') !== false || strpos($desc, 'SMS') !== false) {
        $settingsGroups['Notifications'][] = $setting;
    } else {
        $settingsGroups['Other'][] = $setting;
    }
}

$operatingCountrySetting = $systemSettings->get('bank_operating_country', 'United States');
$defaultCurrencySetting = strtoupper(trim($systemSettings->get('default_currency', DEFAULT_CURRENCY)));
$expectedOperatingCurrency = getCurrencyForOperatingCountry($operatingCountrySetting);
$currencyCountryMismatch = ($expectedOperatingCurrency && $defaultCurrencySetting !== strtoupper($expectedOperatingCurrency));

include __DIR__ . '/../../includes/head.php';
include __DIR__ . '/../../includes/admin-sidebar.php';
?>

<style>
    .settings-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .settings-header {
        margin-bottom: 30px;
    }
    
    .settings-header h1 {
        font-size: 28px;
        color: #202124;
        margin: 0 0 8px 0;
        font-weight: 600;
    }
    
    .settings-header p {
        color: #666;
        font-size: 15px;
        margin: 0;
    }
    
    .settings-group {
        background: white;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    
    .settings-group-title {
        font-size: 20px;
        color: #1e3a8a;
        font-weight: 600;
        margin-bottom: 20px;
        padding: 16px;
        border-bottom: 2px solid #e8f0ff;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        user-select: none;
        transition: all 0.3s ease;
        border-radius: 8px;
        margin-left: -8px;
        margin-right: -8px;
        margin-top: -8px;
        margin-bottom: 20px;
        padding-bottom: 16px;
    }
    
    .settings-group-title:hover {
        background: #f8fafc;
    }
    
    .settings-group-title i.fa-chevron-down {
        font-size: 16px;
        transition: transform 0.3s ease;
        color: #666;
        margin-left: 12px;
    }
    
    .settings-group.collapsed .settings-group-title i.fa-chevron-down {
        transform: rotate(-90deg);
    }
    
    .settings-group-content {
        max-height: 5000px;
        overflow: hidden;
        transition: max-height 0.4s ease, opacity 0.3s ease;
        opacity: 1;
    }
    
    .settings-group.collapsed .settings-group-content {
        max-height: 0;
        opacity: 0;
        overflow: hidden;
    }
    
    .settings-group-title-text {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
    }
    
    .setting-item {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        align-items: start;
        padding: 16px 0;
        border-bottom: 1px solid #f5f5f5;
    }
    
    .setting-item:last-child {
        border-bottom: none;
    }
    
    .setting-label {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    
    .setting-label strong {
        color: #202124;
        font-size: 15px;
        font-weight: 500;
    }
    
    .setting-label small {
        color: #666;
        font-size: 13px;
        line-height: 1.4;
    }
    
    .setting-key {
        display: inline-block;
        background: #f5f5f5;
        padding: 2px 8px;
        border-radius: 4px;
        font-family: monospace;
        font-size: 12px;
        color: #666;
        margin-top: 4px;
    }
    
    .setting-input input,
    .setting-input select {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #dadce0;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.2s;
    }
    
    .setting-input input:focus,
    .setting-input select:focus {
        outline: none;
        border-color: #1e3a8a;
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
    }
    
    .setting-input input[type="checkbox"] {
        width: auto;
        height: 20px;
        width: 20px;
        cursor: pointer;
    }

    .kyc-json-editor {
        width: 100%;
        min-height: 220px;
        padding: 12px 14px;
        border: 1px solid #dadce0;
        border-radius: 8px;
        font-family: monospace;
        font-size: 13px;
        line-height: 1.5;
        resize: vertical;
    }

    .kyc-json-hint {
        margin-top: 8px;
        font-size: 12px;
        color: #666;
        line-height: 1.5;
    }

    .kyc-json-hint code {
        background: #f5f5f5;
        padding: 2px 6px;
        border-radius: 4px;
    }
    
    .save-button-container {
        position: sticky;
        bottom: 20px;
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.15);
        text-align: right;
        margin-top: 30px;
    }
    
    .btn-save {
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        color: white;
        padding: 14px 32px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
    }
    
    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(30, 58, 138, 0.4);
    }
    
    .success-message {
        background: #4caf50;
        color: white;
        padding: 16px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        animation: slideDown 0.3s ease-out;
    }
    
    .error-message {
        background: #f44336;
        color: white;
        padding: 16px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        animation: slideDown 0.3s ease-out;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @media (max-width: 768px) {
        .setting-item {
            grid-template-columns: 1fr;
            gap: 12px;
        }
        
        .settings-container {
            padding: 15px;
        }
        
        .settings-group {
            padding: 16px;
        }
    }
</style>

<div class="settings-container">
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="success-message">
            <i class="fas fa-check-circle" style="font-size: 20px;"></i>
            <span><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></span>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="error-message">
            <i class="fas fa-exclamation-circle" style="font-size: 20px;"></i>
            <span><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></span>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($currencyCountryMismatch)): ?>
        <div class="error-message" style="background: #fff3cd; color: #856404; border: 1px solid #ffc107;">
            <i class="fas fa-exclamation-triangle" style="font-size: 20px;"></i>
            <span>
                Bank operating country is <strong><?php echo htmlspecialchars($operatingCountrySetting); ?></strong>
                but default currency is <strong><?php echo htmlspecialchars($defaultCurrencySetting); ?></strong>.
                Expected currency: <strong><?php echo htmlspecialchars($expectedOperatingCurrency); ?></strong>.
                This mismatch can cause wrong transfer amounts and balance display. Update default currency to match.
            </span>
        </div>
    <?php endif; ?>
    
    <div class="settings-header">
        <h1><i class="fas fa-cog"></i> System Settings</h1>
        <p>Manage all system-wide settings and configurations. Changes take effect immediately.</p>
    </div>
    
    <form method="POST" action="">
        <?php foreach ($settingsGroups as $groupName => $settings): ?>
            <?php if (!empty($settings)): ?>
                <div class="settings-group">
                    <div class="settings-group-title">
                        <div class="settings-group-title-text">
                            <?php 
                            $icons = [
                                'Site Branding & Identity' => 'fa-building',
                                'Bank Operations' => 'fa-university',
                                'Transfer Limits & Fees' => 'fa-exchange-alt',
                                'Account Settings' => 'fa-user-circle',
                                'Security & Compliance' => 'fa-shield-alt',
                                'Notifications' => 'fa-bell',
                                'Other' => 'fa-cogs'
                            ];
                            $icon = $icons[$groupName] ?? 'fa-cog';
                            ?>
                            <i class="fas <?php echo $icon; ?>"></i> 
                            <span><?php echo $groupName; ?></span>
                            <?php if ($groupName === 'Bank Operations'): ?>
                                <span style="font-size: 12px; color: #666; font-weight: normal; margin-left: 8px;">
                                    (Operating country, currency, FX API key, and related bank settings.)
                                </span>
                            <?php elseif ($groupName === 'Transfer Limits & Fees'): ?>
                                <span style="font-size: 12px; color: #666; font-weight: normal; margin-left: 8px;">
                                    (Internal, domestic, and international transfer fees.)
                                </span>
                            <?php endif; ?>
                        </div>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    
                    <div class="settings-group-content">
                        <?php foreach ($settings as $setting): ?>
                            <div class="setting-item">
                                <div class="setting-label">
                                    <strong><?php echo ucwords(str_replace('_', ' ', $setting['setting_key'])); ?></strong>
                                    <small><?php echo htmlspecialchars($setting['description']); ?></small>
                                    <span class="setting-key"><?php echo htmlspecialchars($setting['setting_key']); ?></span>
                                </div>
                                <div class="setting-input">
                                    <?php if ($setting['setting_key'] === 'default_currency'): ?>
                                        <!-- Special dropdown for currency selection -->
                                        <select name="setting_<?php echo $setting['setting_key']; ?>" required>
                                            <?php foreach ($supportedCurrencies as $code => $name): ?>
                                                <option value="<?php echo htmlspecialchars($code); ?>" 
                                                        <?php echo ($setting['setting_value'] === $code) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($code); ?> - <?php echo htmlspecialchars($name); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php elseif ($setting['setting_key'] === 'bank_operating_region'): ?>
                                        <?php
                                        $regionOptions = [
                                            'north-america' => 'North America',
                                            'south-america' => 'South America',
                                            'europe' => 'Europe',
                                            'asia' => 'Asia',
                                            'africa' => 'Africa',
                                            'oceania' => 'Oceania',
                                            'middle-east' => 'Middle East',
                                        ];
                                        $currentRegion = trim((string)$setting['setting_value']);
                                        ?>
                                        <select name="setting_<?php echo $setting['setting_key']; ?>" required>
                                            <?php foreach ($regionOptions as $regionValue => $regionLabel): ?>
                                                <option value="<?php echo htmlspecialchars($regionValue); ?>"
                                                    <?php echo ($currentRegion === $regionValue) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($regionLabel); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php elseif ($setting['setting_key'] === 'bank_operating_country'): ?>
                                        <?php $countries = getAllCountriesFlat(); ?>
                                        <select name="setting_<?php echo $setting['setting_key']; ?>" required>
                                            <?php foreach ($countries as $country): ?>
                                                <option value="<?php echo htmlspecialchars($country['name']); ?>"
                                                        <?php
                                                        $currentValue = trim((string)$setting['setting_value']);
                                                        $selected = ($currentValue === $country['name'])
                                                            || ($currentValue === $country['code'])
                                                            || (countryToIso2($currentValue) === $country['code']);
                                                        echo $selected ? 'selected' : '';
                                                        ?>>
                                                    <?php echo htmlspecialchars($country['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php elseif ($setting['setting_type'] === 'boolean'): ?>
                                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; user-select: none;">
                                            <input type="checkbox" 
                                                   name="setting_<?php echo $setting['setting_key']; ?>" 
                                                   value="1"
                                                   <?php echo ($setting['setting_value'] === '1' || $setting['setting_value'] === 'true') ? 'checked' : ''; ?>
                                                   style="width: 20px; height: 20px; cursor: pointer;">
                                            <span style="font-weight: 500; color: #202124;">
                                                <?php echo ($setting['setting_value'] === '1' || $setting['setting_value'] === 'true') ? '✓ Enabled' : '✗ Disabled'; ?>
                                            </span>
                                        </label>
                                    <?php elseif ($setting['setting_type'] === 'number'): ?>
                                        <input type="number" 
                                               name="setting_<?php echo $setting['setting_key']; ?>" 
                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                               step="0.01"
                                               min="0">
                                    <?php elseif ($setting['setting_key'] === 'exchange_rate_api_key'): ?>
                                        <input type="password" 
                                               name="setting_<?php echo $setting['setting_key']; ?>" 
                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                               placeholder="ExchangeRate-API key"
                                               autocomplete="new-password">
                                    <?php else: ?>
                                        <input type="text" 
                                               name="setting_<?php echo $setting['setting_key']; ?>" 
                                               value="<?php echo htmlspecialchars($setting['setting_value']); ?>">
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <?php
        $kycUseCustom = getSystemSetting('kyc_use_custom_fields', '0');
        $kycCustomJson = getSystemSetting('kyc_custom_fields', '[]');
        $kycActiveProfile = getKycActiveProfile();
        ?>
        <div class="settings-group">
            <div class="settings-group-title">
                <div class="settings-group-title-text">
                    <i class="fas fa-id-card"></i>
                    <span>KYC Custom Fields Override</span>
                    <span style="font-size: 12px; color: #666; font-weight: normal; margin-left: 8px;">
                        (Active country profile: <?php echo htmlspecialchars($kycActiveProfile['name']); ?>)
                    </span>
                </div>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="settings-group-content">
                <div class="setting-item">
                    <div class="setting-label">
                        <strong>Use Custom KYC Fields</strong>
                        <small>When enabled, users see admin-defined fields instead of the country profile defaults. Document uploads and compliance questions remain required.</small>
                        <span class="setting-key">kyc_use_custom_fields</span>
                    </div>
                    <div class="setting-input">
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; user-select: none;">
                            <input type="checkbox" name="setting_kyc_use_custom_fields" value="1"
                                   <?php echo ($kycUseCustom === '1') ? 'checked' : ''; ?>
                                   style="width: 20px; height: 20px; cursor: pointer;">
                            <span style="font-weight: 500; color: #202124;">
                                <?php echo ($kycUseCustom === '1') ? '✓ Custom fields enabled' : '✗ Using country profile'; ?>
                            </span>
                        </label>
                    </div>
                </div>
                <div class="setting-item" style="grid-template-columns: 1fr;">
                    <div class="setting-label">
                        <strong>Custom Field Definitions (JSON)</strong>
                        <small>Array of field objects. Each field: key, label, type (text|textarea|date|select|file), required (true/false), optional pattern, placeholder, step (personal|compliance), options (for select).</small>
                        <span class="setting-key">kyc_custom_fields</span>
                        <div class="kyc-json-hint">
                            Example: <code>[{"key":"bvn","label":"BVN","type":"text","required":true,"pattern":"^\\d{11}$","step":"personal"}]</code>
                        </div>
                    </div>
                    <div class="setting-input">
                        <textarea name="setting_kyc_custom_fields" class="kyc-json-editor" spellcheck="false"><?php echo htmlspecialchars($kycCustomJson ?: '[]'); ?></textarea>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="save-button-container">
            <button type="submit" name="update_settings" class="btn-save">
                <i class="fas fa-save"></i> Save All Settings
            </button>
        </div>
    </form>
</div>

<script>
// Always collapse all sections on page load
document.addEventListener('DOMContentLoaded', function() {
    // Always collapse all sections on every page load
    const allGroups = document.querySelectorAll('.settings-group');
    if (allGroups.length > 0) {
    allGroups.forEach(group => {
            if (group) {
        group.classList.add('collapsed');
            }
    });
    }
    
    // Handle toggle when admin clicks to open/close sections
    const titles = document.querySelectorAll('.settings-group-title');
    if (titles.length > 0) {
        titles.forEach(title => {
            if (title) {
                title.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
            const group = this.closest('.settings-group');
                    if (group) {
            group.classList.toggle('collapsed');
                    }
        });
            }
    });
    }
});
</script>

</body>
</html>

