<?php 
$pageTitle = 'System Settings - Admin - Octobank';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

$regionCountryNames = [];
foreach (getCountriesByRegion() as $region => $countries) {
    $regionCountryNames[$region] = array_column($countries, 'name');
}

// Ensure admin access
requireLogin();
requireAdmin();

$db = Database::getInstance();
$userId = $_SESSION['user_id'];

// Get current settings
$sql = "SELECT * FROM system_settings WHERE setting_key LIKE 'transfer_%'";
$stmt = $db->query($sql);
$settings = $stmt->fetchAll();
$currentSettings = [];
foreach ($settings as $setting) {
    $currentSettings[$setting['setting_key']] = $setting['setting_value'];
}

// Default values if not set
if (empty($currentSettings)) {
    $currentSettings = [
        'transfer_internal_fee' => '0',
        'transfer_domestic_fee' => '0.5',
        'transfer_international_fee' => '2.5'
    ];
}

// Get bank operating country
$sql = "SELECT setting_value FROM system_settings WHERE setting_key = 'bank_operating_country'";
$stmt = $db->query($sql);
$result = $stmt->fetch();
$bankOperatingCountry = $result['setting_value'] ?? 'United States';

// Get bank operating region
$sql = "SELECT setting_value FROM system_settings WHERE setting_key = 'bank_operating_region'";
$stmt = $db->query($sql);
$result = $stmt->fetch();
$bankOperatingRegion = $result['setting_value'] ?? 'north-america';

// Note: default_currency is managed in the main System Settings page, not here
// Note: Login security settings (max_login_attempts, login_lockout_duration) are managed in System Settings page under Security & Compliance

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_settings'])) {
    $internalFee = floatval($_POST['internal_fee'] ?? 0);
    $domesticFee = floatval($_POST['domestic_fee'] ?? 0.5);
    $internationalFee = floatval($_POST['international_fee'] ?? 2.5);
    $operatingCountry = Security::sanitize($_POST['operating_country'] ?? 'United States');
    $operatingRegion = Security::sanitize($_POST['operating_region'] ?? 'north-america');
    
    try {
        $db->beginTransaction();
        
        // Update or insert settings (default_currency is managed in System Settings page)
        $settingsToUpdate = [
            'transfer_internal_fee' => $internalFee,
            'transfer_domestic_fee' => $domesticFee,
            'transfer_international_fee' => $internationalFee,
            'bank_operating_country' => $operatingCountry,
            'bank_operating_region' => $operatingRegion
        ];
        
        foreach ($settingsToUpdate as $key => $value) {
            $settingType = (strpos($key, 'fee') !== false) ? 'number' : 'string';
            $sql = "INSERT INTO system_settings (setting_key, setting_value, setting_type, updated_by) 
                    VALUES (?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE setting_value = ?, updated_by = ?, updated_at = NOW()";
            $db->query($sql, [$key, $value, $settingType, $userId, $value, $userId]);
        }

        if ($operatingCountry !== $bankOperatingCountry) {
            require_once __DIR__ . '/../../includes/currency.php';
            $expectedCurrency = getCurrencyForOperatingCountry($operatingCountry);
            $supportedCodes = array_keys((new Currency())->getSupportedCurrencies());
            if ($expectedCurrency && in_array($expectedCurrency, $supportedCodes, true)) {
                $sql = "INSERT INTO system_settings (setting_key, setting_value, setting_type, updated_by)
                        VALUES ('default_currency', ?, 'string', ?)
                        ON DUPLICATE KEY UPDATE setting_value = ?, updated_by = ?, updated_at = NOW()";
                $db->query($sql, [$expectedCurrency, $userId, $expectedCurrency, $userId]);
                $successMessage = 'Settings updated successfully! Default currency was set to ' . $expectedCurrency . ' to match the operating country.';
            }
        }
        
        logActivity($userId, 'settings_updated', 'Updated system settings');
        
        $db->commit();
        
        $successMessage = 'Settings updated successfully!';
        
        // Update current settings
        $currentSettings = [
            'transfer_internal_fee' => $internalFee,
            'transfer_domestic_fee' => $domesticFee,
            'transfer_international_fee' => $internationalFee
        ];
        $bankOperatingCountry = $operatingCountry;
        $bankOperatingRegion = $operatingRegion;
        $defaultCurrency = $systemCurrency;
        
    } catch (Exception $e) {
        $db->rollback();
        $errorMessage = 'Failed to update settings: ' . $e->getMessage();
    }
}

// Include head
include __DIR__ . '/../../includes/head.php';

// Include sidebar and main structure
include __DIR__ . '/../../includes/admin-sidebar.php';
?>

<!-- ===== ADMIN SETTINGS PAGE CONTENT ===== -->

<style>
/* Admin Settings Specific Styles */
.content-area {
    background: #f5f7fa;
    min-height: 100vh;
    padding: 20px;
}

.settings-container {
    max-width: 1000px;
    margin: 0 auto;
}

.settings-header {
    background: white;
    border-radius: 16px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.settings-title {
    font-size: 32px;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 8px;
}

.settings-subtitle {
    color: #6c757d;
    font-size: 16px;
}

.settings-card {
    background: white;
    border-radius: 16px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.card-title {
    font-size: 24px;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #e2e8f0;
}

.form-group {
    margin-bottom: 25px;
}

.form-label {
    display: block;
    font-weight: 600;
    color: #4a5568;
    margin-bottom: 8px;
    font-size: 15px;
}

.form-input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 16px;
    transition: all 0.3s ease;
}

.form-input:focus {
    outline: none;
    border-color: #1a73e8;
    box-shadow: 0 0 0 3px rgba(26, 115, 232, 0.1);
}

.input-group {
    position: relative;
}

.input-addon {
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #718096;
    font-weight: 500;
}

.form-help {
    font-size: 13px;
    color: #718096;
    margin-top: 6px;
}

.info-box {
    background: #e8f0fe;
    border-left: 4px solid #1a73e8;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 25px;
}

.info-box-title {
    font-weight: 600;
    color: #1a73e8;
    margin-bottom: 8px;
}

.info-box-text {
    font-size: 14px;
    color: #4a5568;
    line-height: 1.6;
}

.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: 500;
}

.alert-success {
    background: #d1fae5;
    color: #065f46;
    border-left: 4px solid #059669;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    border-left: 4px solid #dc2626;
}

.btn-primary {
    background: linear-gradient(135deg, #1a73e8 0%, #0d62d3 100%);
    color: white;
    padding: 14px 32px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(26, 115, 232, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(26, 115, 232, 0.4);
}

.btn-secondary {
    background: #e2e8f0;
    color: #4a5568;
    padding: 14px 32px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-left: 12px;
}

.btn-secondary:hover {
    background: #cbd5e0;
}

.fee-preview {
    background: #f7fafc;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px;
    margin-top: 30px;
}

.preview-title {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 15px;
    font-size: 18px;
}

.preview-item {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #e2e8f0;
}

.preview-item:last-child {
    border-bottom: none;
}

.preview-label {
    color: #4a5568;
    font-weight: 500;
}

.preview-value {
    color: #2d3748;
    font-weight: 600;
}

@media (max-width: 768px) {
    .settings-container {
        padding: 10px;
    }
    
    .settings-header, .settings-card {
        padding: 20px;
    }
    
    .settings-title {
        font-size: 24px;
    }
    
    .btn-primary, .btn-secondary {
        width: 100%;
        margin-left: 0;
        margin-top: 10px;
    }
}
</style>

<div class="settings-container">
    <div class="settings-header">
        <h1 class="settings-title">System Settings</h1>
        <p class="settings-subtitle">Configure transfer fees and system parameters</p>
    </div>
    
    <?php if (isset($successMessage)): ?>
        <div class="alert alert-success">
            ✓ <?php echo htmlspecialchars($successMessage); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($errorMessage)): ?>
        <div class="alert alert-error">
            ✗ <?php echo htmlspecialchars($errorMessage); ?>
        </div>
    <?php endif; ?>
    
    <div class="settings-card">
        <h2 class="card-title">Bank Location & Currency</h2>
        
        <div class="info-box">
            <div class="info-box-title">Important: Bank Operating Country</div>
            <div class="info-box-text">
                Set which country your bank operates in. This determines which banks appear for domestic transfers and sets the default currency.
            </div>
        </div>
        
        <form method="POST" action="">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="operating_region">Bank Operating Region *</label>
                    <select class="form-input" id="operating_region" name="operating_region" required onchange="updateRegionCountries()">
                        <option value="">Select region</option>
                        <option value="north-america" <?php echo ($bankOperatingRegion === 'north-america') ? 'selected' : ''; ?>>North America</option>
                        <option value="south-america" <?php echo ($bankOperatingRegion === 'south-america') ? 'selected' : ''; ?>>South America</option>
                        <option value="europe" <?php echo ($bankOperatingRegion === 'europe') ? 'selected' : ''; ?>>Europe</option>
                        <option value="asia" <?php echo ($bankOperatingRegion === 'asia') ? 'selected' : ''; ?>>Asia</option>
                        <option value="africa" <?php echo ($bankOperatingRegion === 'africa') ? 'selected' : ''; ?>>Africa</option>
                        <option value="oceania" <?php echo ($bankOperatingRegion === 'oceania') ? 'selected' : ''; ?>>Oceania</option>
                        <option value="middle-east" <?php echo ($bankOperatingRegion === 'middle-east') ? 'selected' : ''; ?>>Middle East</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="operating_country">Bank Operating Country *</label>
                    <select class="form-input" id="operating_country" name="operating_country" required>
                        <option value="<?php echo htmlspecialchars($bankOperatingCountry); ?>"><?php echo htmlspecialchars($bankOperatingCountry); ?></option>
                    </select>
                    <div class="form-help">Domestic transfers will only show banks from this country</div>
                    <div class="form-help" style="margin-top: 8px; color: #666; font-style: italic;">
                        <i class="fas fa-info-circle"></i> Note: Default Currency is managed in the main <a href="<?php echo SITE_URL; ?>/admin/system-settings" style="color: #1e3a8a; text-decoration: underline;">System Settings</a> page under "Bank Operations"
                    </div>
                </div>

    <!-- Note: Login Security Settings Moved -->
    <div class="card">
        <div class="card-body">
            <h2 class="card-title">Login Security Settings</h2>
            <div class="info-box" style="background: #f8fafc; padding: 20px; border-radius: 8px; border-left: 4px solid #1e3a8a; margin-bottom: 20px;">
                <div class="info-box-title" style="color: #1e3a8a; font-weight: 600; margin-bottom: 8px;">
                    <i class="fas fa-info-circle"></i> Settings Moved
                </div>
                <div class="info-box-text" style="color: #666; line-height: 1.6;">
                    Login security settings (Max Login Attempts, Lockout Duration) have been moved to the 
                    <a href="<?php echo SITE_URL; ?>/admin/system-settings" style="color: #1e3a8a; text-decoration: underline; font-weight: 600;">System Settings</a> 
                    page under the "Security & Compliance" section, as they are not transfer-related settings.
                </div>
            </div>
        </div>
    </div>
</div>
            
            <hr style="margin: 30px 0; border: none; border-top: 2px solid #e2e8f0;">
            
            <h2 class="card-title">Transfer Fee Configuration</h2>
            
            <div class="info-box">
                <div class="info-box-title">About Transfer Fees</div>
                <div class="info-box-text">
                    Configure percentage-based fees for different transfer types. These fees are calculated as a percentage of the transfer amount and automatically added to each transaction.
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="internal_fee">Internal Transfer Fee (%)</label>
                <div class="input-group">
                    <input type="number" 
                           id="internal_fee" 
                           name="internal_fee" 
                           class="form-input" 
                           value="<?php echo htmlspecialchars($currentSettings['transfer_internal_fee']); ?>" 
                           step="0.01" 
                           min="0" 
                           max="100"
                           required>
                    <span class="input-addon">%</span>
                </div>
                <div class="form-help">
                    Fee charged for transfers within the same bank. Typically 0% to encourage internal transfers.
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="domestic_fee">Domestic Transfer Fee (%)</label>
                <div class="input-group">
                    <input type="number" 
                           id="domestic_fee" 
                           name="domestic_fee" 
                           class="form-input" 
                           value="<?php echo htmlspecialchars($currentSettings['transfer_domestic_fee']); ?>" 
                           step="0.01" 
                           min="0" 
                           max="100"
                           required>
                    <span class="input-addon">%</span>
                </div>
                <div class="form-help">
                    Fee charged for transfers within the same country. Standard range: 0.5% - 1.0%
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="international_fee">International Wire Transfer Fee (%)</label>
                <div class="input-group">
                    <input type="number" 
                           id="international_fee" 
                           name="international_fee" 
                           class="form-input" 
                           value="<?php echo htmlspecialchars($currentSettings['transfer_international_fee']); ?>" 
                           step="0.01" 
                           min="0" 
                           max="100"
                           required>
                    <span class="input-addon">%</span>
                </div>
                <div class="form-help">
                    Fee charged for international wire transfers. Standard range: 2.0% - 3.5%
                </div>
            </div>
            
            <div class="fee-preview">
                <div class="preview-title">Fee Preview (for $1,000 transfer)</div>
                <div class="preview-item">
                    <span class="preview-label">Internal Transfer Fee:</span>
                    <span class="preview-value" id="preview-internal">$0.00</span>
                </div>
                <div class="preview-item">
                    <span class="preview-label">Domestic Transfer Fee:</span>
                    <span class="preview-value" id="preview-domestic">$5.00</span>
                </div>
                <div class="preview-item">
                    <span class="preview-label">International Transfer Fee:</span>
                    <span class="preview-value" id="preview-international">$25.00</span>
                </div>
            </div>
            
            <div style="margin-top: 30px;">
                <button type="submit" name="update_settings" class="btn-primary">
                    Save Settings
                </button>
                <button type="button" class="btn-secondary" onclick="window.location.href='<?php echo SITE_URL; ?>/admin'">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Live preview calculation
    const sampleAmount = 1000;
    
    function updatePreview() {
        const internalFee = parseFloat(document.getElementById('internal_fee').value) || 0;
        const domesticFee = parseFloat(document.getElementById('domestic_fee').value) || 0;
        const internationalFee = parseFloat(document.getElementById('international_fee').value) || 0;
        
        const internalAmount = (sampleAmount * internalFee) / 100;
        const domesticAmount = (sampleAmount * domesticFee) / 100;
        const internationalAmount = (sampleAmount * internationalFee) / 100;
        
        document.getElementById('preview-internal').textContent = '$' + internalAmount.toFixed(2);
        document.getElementById('preview-domestic').textContent = '$' + domesticAmount.toFixed(2);
        document.getElementById('preview-international').textContent = '$' + internationalAmount.toFixed(2);
    }
    
    // Add event listeners
    document.getElementById('internal_fee').addEventListener('input', updatePreview);
    document.getElementById('domestic_fee').addEventListener('input', updatePreview);
    document.getElementById('international_fee').addEventListener('input', updatePreview);
    
    // Initialize preview
    updatePreview();
    
    // Region-country mapping from canonical country list
    const regionCountries = <?php echo json_encode($regionCountryNames); ?>;
    
    function updateRegionCountries() {
        const region = document.getElementById('operating_region').value;
        const countrySelect = document.getElementById('operating_country');
        
        countrySelect.innerHTML = '<option value="">Select country</option>';
        
        if (region && regionCountries[region]) {
            regionCountries[region].forEach(country => {
                const option = document.createElement('option');
                option.value = country;
                option.textContent = country;
                countrySelect.appendChild(option);
            });
        }
    }
    
    // Initialize countries on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateRegionCountries();
        // Set selected country
        const currentCountry = '<?php echo addslashes($bankOperatingCountry); ?>';
        document.getElementById('operating_country').value = currentCountry;
    });
</script>


