<?php 
$pageTitle = 'Transfer Funds - Octobank';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/countries.php';
require_once __DIR__ . '/../../includes/transaction-categories.php';
require_once __DIR__ . '/../../includes/transfer-rails.php';

// Get dynamic site name for branding
$siteName = getSiteName() ?? 'SecureBank';

// Get user data and accounts
requireLogin();
$userId = $_SESSION['user_id'];

// Get database instance
$db = Database::getInstance();

// Get user accounts
$accountModel = new Account();
$userAccounts = $accountModel->getUserAccounts($userId);

// Determine default daily limit for initial display
$defaultDailyLimit = getDailyLimitForAccountType($userAccounts[0]['account_type'] ?? 'checking');

// Get user info
$userModel = new User();
$userInfo = $userModel->findById($userId);
$userCurrency = getUserDisplayCurrency($userInfo);
$userStatus = $userInfo['status'] ?? 'active';
$userHasTransferPin = !empty($userInfo['transfer_pin'] ?? '');
require_once __DIR__ . '/../../includes/system-settings.php';
$requireTransferPinSetting = SystemSettings::getInstance()->get('require_transfer_pin', '1') === '1';
$clientMustCollectTransferPin = $requireTransferPinSetting && $userHasTransferPin;

// Get system settings for bank charges
$sql = "SELECT * FROM system_settings WHERE setting_key LIKE 'transfer_%'";
$stmt = $db->query($sql);
$settings = $stmt->fetchAll();
$chargeSettings = [];
foreach ($settings as $setting) {
    $chargeSettings[$setting['setting_key']] = $setting['setting_value'];
}

// Default charges if not set
if (empty($chargeSettings)) {
    $chargeSettings = [
        'transfer_internal_fee' => '0',
        'transfer_domestic_fee' => '0.5',
        'transfer_international_fee' => '2.5'
    ];
}

// Get system operating country for domestic transfers
$sql = "SELECT setting_value FROM system_settings WHERE setting_key = 'bank_operating_country'";
$stmt = $db->query($sql);
$operatingCountry = $stmt->fetch();
$bankCountry = $operatingCountry['setting_value'] ?? 'United States';
$expenseCategoryOptionsHtml = renderExpenseCategorySelectOptions();
$domesticRails = getDomesticRailFields($bankCountry);
$siteDefaultCurrency = getSiteDefaultCurrency();

// Get all active banks for international transfers
$sql = "SELECT id, name as bank_name, region, country, swift_code FROM banks WHERE is_active = 1 ORDER BY country ASC, name ASC";
$stmt = $db->query($sql);
$allBanks = $stmt->fetchAll();

// Get banks from operating country for domestic transfers (only active banks)
$bankCountryIso = countryToIso2($bankCountry) ?? $bankCountry;
$domesticAccountRules = getDomesticAccountNumberRules($bankCountryIso);
$domesticBanks = array_filter($allBanks, function($bank) use ($bankCountry, $bankCountryIso) {
    $bankC = trim((string)($bank['country'] ?? ''));
    if ($bankC === '') {
        return false;
    }
    $bankIso = countryToIso2($bankC) ?? $bankC;
    return strcasecmp($bankIso, $bankCountryIso) === 0
        || strcasecmp($bankC, trim((string)$bankCountry)) === 0;
});

// Canonical countries grouped by region for international transfers
$countriesByRegion = getCountriesByRegion();

// Countries that have active banks in the database (for autocomplete)
$bankCountryNames = [];
foreach ($allBanks as $bank) {
    $name = trim((string)($bank['country'] ?? ''));
    if ($name !== '') {
        $bankCountryNames[$name] = true;
    }
}

$countriesWithBanksByCode = [];
$countryCodeToName = [];
foreach (getAllCountriesFlat() as $country) {
    $countryCodeToName[$country['code']] = $country['name'];
    if (isset($bankCountryNames[$country['name']])) {
        $countriesWithBanksByCode[$country['code']] = true;
    }
}

$regionNames = [
    'north-america' => 'North America',
    'south-america' => 'South America',
    'europe' => 'Europe',
    'asia' => 'Asia',
    'africa' => 'Africa',
    'oceania' => 'Oceania',
    'middle-east' => 'Middle East',
];

$internationalRailsMap = [];
foreach (getAllCountriesFlat() as $country) {
    $internationalRailsMap[$country['code']] = getInternationalRailFields($country['code']);
}

// Include head
include __DIR__ . '/../../includes/head.php';

// Include sidebar and main structure
include __DIR__ . '/../../includes/sidebar.php';
?>

<!-- ===== TRANSFER PAGE CONTENT ===== -->

<?php include __DIR__ . '/../../includes/restricted-banner.php'; ?>

<style>
        /* Override parent content-area styles */
        .main-content-area .content-area {
            background: #f5f5f5 !important;
            padding: 20px !important;
        }

        .transfer-wrapper {
            max-width: 900px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
            overflow: hidden;
        }

        /* ===== PAGE HEADER STANDARD (Same as Dashboard) ===== */
        .header {
            padding: 20px;
            border-bottom: 1px solid #dadce0;
        }

        .header h1 {
            font-size: 24px;
            color: #202124;
            margin: 0;
            font-weight: 600;
            text-align: left;
        }

        .step-indicator {
            display: flex;
            margin-top: 16px;
        }

        .step {
            display: flex;
            align-items: center;
            margin-right: 20px;
        }

        .step-number {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background-color: #1a73e8;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            margin-right: 8px;
        }

        .step.active .step-number {
            background-color: #1a73e8;
        }

        .step.inactive .step-number {
            background-color: #5f6368;
        }

        .step-label {
            font-size: 14px;
            font-weight: 500;
        }

        .form-container {
            padding: 20px;
        }

        .form-section {
            display: none;
        }

        .form-section.active {
            display: block;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 16px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-select, .form-input {
            width: 100%;
            padding: 12px;
            border: 1px solid #dadce0;
            border-radius: 4px;
            font-size: 16px;
            background-color: #ffffff;
        }

        .form-select:focus, .form-input:focus {
            outline: none;
            border-color: #1a73e8;
        }

        .fee-info {
            display: inline-block;
            background-color: #e8f0fe;
            color: #1a73e8;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 13px;
            margin-top: 4px;
        }

        .action-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dadce0;
        }

        .btn-cancel {
            background: none;
            border: none;
            color: #1a73e8;
            font-size: 16px;
            cursor: pointer;
            padding: 10px 0;
        }

        .btn-proceed {
            background-color: #1a73e8;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            padding: 10px 24px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
        }

        .btn-proceed:hover {
            background-color: #0d62d3;
        }

        /* Searchable dropdown styles */
        .bank-search-container {
            position: relative;
        }

        .bank-suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #dadce0;
            border-top: none;
            border-radius: 0 0 4px 4px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 100;
            display: none;
        }

        .bank-suggestion-item {
            padding: 10px 12px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
        }

        .bank-suggestion-item:hover {
            background-color: #f8f9fa;
        }

        .bank-suggestion-item.active {
            background-color: #e8f0fe;
        }

        /* Preview Section */
        .preview-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        @media (min-width: 768px) {
            .preview-container {
                grid-template-columns: 2fr 1fr;
            }
        }

        .preview-details {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
        }

        .preview-sidebar {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
            height: fit-content;
        }

        .preview-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 16px;
        }

        .preview-item {
            margin-bottom: 16px;
            padding-bottom: 16px;
            border-bottom: 1px solid #dadce0;
        }

        .preview-label {
            font-weight: 500;
            margin-bottom: 4px;
        }

        .preview-value {
            color: #5f6368;
        }

        .balance-section {
            margin-bottom: 20px;
        }

        .balance-amount {
            font-size: 24px;
            font-weight: 600;
            margin: 8px 0;
        }

        .transfer-amount {
            font-size: 20px;
            font-weight: 600;
            margin: 8px 0;
            color: #1a73e8;
        }

        .btn-pay {
            background-color: #34a853;
            color: #ffffff;
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 500;
            margin-top: 16px;
            cursor: pointer;
        }

        .btn-pay:hover {
            background-color: #2e8b47;
        }

        .btn-back {
            background-color: transparent;
            border: 1px solid #dadce0;
            color: #202124;
            width: 100%;
            padding: 12px;
            border-radius: 4px;
            font-size: 16px;
            margin-top: 16px;
            cursor: pointer;
        }

        .btn-back:hover {
            background-color: #f8f9fa;
        }

        /* Loading Overlay */
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            backdrop-filter: blur(5px);
        }

        .loading-popup {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
            width: 90%;
            max-width: 400px;
        }

        .loading-text {
            font-size: 18px;
            font-weight: 500;
            margin-bottom: 20px;
        }

        .loading-bar {
            height: 4px;
            background-color: #dadce0;
            border-radius: 2px;
            overflow: hidden;
        }

        .loading-progress {
            height: 100%;
            background-color: #1a73e8;
            width: 0%;
            animation: loading 2s infinite;
        }

        @keyframes loading {
            0% { width: 0%; }
            50% { width: 70%; }
            100% { width: 100%; }
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .main-content-area .content-area {
                padding: 10px !important;
            }

            .transfer-wrapper {
                border-radius: 4px;
            }

            .header {
                padding: 15px;
            }

            .form-container {
                padding: 15px;
            }

            .step-indicator {
                flex-direction: column;
                gap: 10px;
            }

            .step {
                margin-right: 0;
            }
        }

        @media (max-width: 480px) {
            .main-content-area .content-area {
                padding: 5px !important;
            }

            .header h1 {
                font-size: 20px;
            }
        }
</style>

<div class="transfer-wrapper">
    <div class="header">
        <h1>Transfer Funds</h1>
        <div class="step-indicator">
            <div class="step active" id="step1">
                <div class="step-number">1</div>
                <div class="step-label">Transfer Details</div>
            </div>
            <div class="step inactive" id="step2">
                <div class="step-number">2</div>
                <div class="step-label">Preview & Pay</div>
            </div>
        </div>
    </div>
    
    <div class="form-container">
        <!-- Step 1: Transfer Details -->
        <div class="form-section active" id="step1Form">
            <h2 class="section-title">Transfer Details</h2>
            
            <div class="form-group">
                <label class="form-label" for="accountType">From Account</label>
                <select class="form-select" id="accountType">
                    <option value="">Select account</option>
                    <?php if (!empty($userAccounts)): ?>
                        <?php foreach ($userAccounts as $account): ?>
                            <?php $accountDailyLimit = getDailyLimitForAccountType($account['account_type']); ?>
                            <option value="<?php echo $account['id']; ?>" 
                                    data-balance="<?php echo $account['balance']; ?>"
                                    data-currency="<?php echo htmlspecialchars(getAccountStoredCurrency($account)); ?>"
                                    data-daily-limit="<?php echo $accountDailyLimit; ?>"
                                    data-type="<?php echo $account['account_type']; ?>"
                                    data-account-status="<?php echo htmlspecialchars($account['status'] ?? 'active'); ?>">
                                <?php echo ucfirst($account['account_type']); ?> Account - 
                                <?php echo htmlspecialchars($account['account_number']); ?> 
                                (Balance: <?php echo formatAccountBalance($account['balance'], $account, $userCurrency); ?>)
                            </option>
                        <?php endforeach; ?>
                        <!-- Debug removed -->
                    <?php else: ?>
                        <option value="" disabled>No accounts available</option>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="transferType">Transfer Type</label>
                <select class="form-select" id="transferType">
                    <option value="">Select transfer type</option>
                    <option value="internal" data-fee="<?php echo $chargeSettings['transfer_internal_fee']; ?>">
                        Internal Transfer (Fee: <?php echo $chargeSettings['transfer_internal_fee']; ?>%)
                    </option>
                    <option value="domestic" data-fee="<?php echo $chargeSettings['transfer_domestic_fee']; ?>">
                        Domestic Transfer (Fee: <?php echo $chargeSettings['transfer_domestic_fee']; ?>%)
                    </option>
                    <option value="international" data-fee="<?php echo $chargeSettings['transfer_international_fee']; ?>">
                        International Wire Transfer (Fee: <?php echo $chargeSettings['transfer_international_fee']; ?>%)
                    </option>
                </select>
            </div>
            
            <!-- Dynamic Fields Based on Transfer Type -->
            <div id="dynamicFields">
                <!-- Internal Transfer Fields (Interbank - User to User) -->
                <div class="transfer-type-fields" id="internalFields" style="display: none;">
                    <div class="form-group">
                        <label class="form-label" for="internalRecipientEmail">Recipient Email Address *</label>
                        <input type="email" id="internalRecipientEmail" class="form-input" placeholder="Enter recipient's email" autocomplete="off">
                        <div class="form-help">Enter the email of the user you want to transfer to</div>
                        <div id="emailLookupStatus" style="display: none; margin-top: 8px; font-size: 13px;"></div>
                    </div>
                    
                    <!-- Auto-populated fields -->
                    <div id="recipientInfoBox" style="display: none; padding: 12px; background: #e8f5e9; border-radius: 8px; margin-bottom: 16px;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                            <i class="fas fa-check-circle" style="color: #4caf50;"></i>
                            <strong style="color: #2e7d32;">Recipient Found</strong>
                        </div>
                        <div style="display: grid; grid-template-columns: auto 1fr; gap: 8px 16px; font-size: 14px;">
                            <span style="color: #666;">Account Name:</span>
                            <strong id="recipientNameDisplay">-</strong>
                            <span style="color: #666;">Account Number:</span>
                            <strong id="recipientAccountDisplay">-</strong>
                        </div>
                    </div>
                    
                    <!-- Hidden fields for form submission -->
                    <input type="hidden" id="internalAccountName" value="">
                    <input type="hidden" id="internalAccountNumber" value="">
                    <input type="hidden" id="internalBankName" value="<?php echo htmlspecialchars($siteName); ?>">
                    
                    <div class="form-group">
                        <label class="form-label" for="internalExpenseCategory">Transaction Category *</label>
                        <select id="internalExpenseCategory" class="form-input" required>
                            <?php echo $expenseCategoryOptionsHtml; ?>
                        </select>
                        <div class="form-help">This helps track your spending on the dashboard</div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="internalAmount">Amount (<span class="amount-currency-code"><?php echo strtoupper($siteDefaultCurrency); ?></span>)</label>
                        <input type="number" id="internalAmount" class="form-input" placeholder="Enter amount" step="0.01" min="0">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="internalReference">Reference (Optional)</label>
                        <input type="text" id="internalReference" class="form-input" placeholder="Enter reference">
                    </div>
                </div>
                
                <!-- Domestic Transfer Fields -->
                <div class="transfer-type-fields" id="domesticFields" style="display: none;">
                    <div class="form-group">
                        <label class="form-label" for="domesticBankName">Bank Name (<?php echo htmlspecialchars($bankCountry); ?>)</label>
                        <div class="bank-search-container">
                            <input type="text" id="domesticBankName" class="form-input" placeholder="Type to search for banks..." autocomplete="off">
                            <div class="bank-suggestions" id="domesticBankSuggestions"></div>
                        </div>
                    </div>
                    
                    <?php if (count($domesticRails['methods']) > 1): ?>
                    <div class="form-group">
                        <label class="form-label" for="domesticTransferMethod">Transfer Method</label>
                        <select class="form-select" id="domesticTransferMethod">
                            <?php foreach ($domesticRails['methods'] as $methodKey => $method): ?>
                            <option value="<?php echo htmlspecialchars($methodKey); ?>"<?php echo ($methodKey === $domesticRails['default_method']) ? ' selected' : ''; ?>>
                                <?php echo htmlspecialchars($method['label']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php else: ?>
                    <input type="hidden" id="domesticTransferMethod" value="<?php echo htmlspecialchars($domesticRails['default_method']); ?>">
                    <?php endif; ?>

                    <div id="domesticRailFieldsContainer"></div>
                    
                    <div class="form-group">
                        <label class="form-label" for="domesticAccountNumber">Account Number<?php if (!empty($domesticAccountRules['hint'])): ?> (<?php echo htmlspecialchars($domesticAccountRules['hint']); ?>)<?php endif; ?></label>
                        <input type="text" id="domesticAccountNumber" class="form-input" placeholder="Enter account number" inputmode="numeric"<?php if (!empty($domesticAccountRules['min'])): ?> minlength="<?php echo (int)$domesticAccountRules['min']; ?>"<?php endif; ?><?php if (!empty($domesticAccountRules['max'])): ?> maxlength="<?php echo (int)$domesticAccountRules['max']; ?>"<?php endif; ?>>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="domesticAccountName">Account Name</label>
                        <input type="text" id="domesticAccountName" class="form-input" placeholder="Enter account holder name">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="domesticExpenseCategory">Transaction Category *</label>
                        <select id="domesticExpenseCategory" class="form-input" required>
                            <?php echo $expenseCategoryOptionsHtml; ?>
                        </select>
                        <div class="form-help">This helps track your spending on the dashboard</div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="domesticAmount">Amount (<span class="amount-currency-code"><?php echo strtoupper($siteDefaultCurrency); ?></span>)</label>
                        <input type="number" id="domesticAmount" class="form-input" placeholder="Enter amount" step="0.01" min="0">
                    </div>
                </div>
                
                <!-- International Transfer Fields -->
                <div class="transfer-type-fields" id="internationalFields" style="display: none;">
                    <div class="form-group">
                        <label class="form-label" for="region">Region</label>
                        <select class="form-select" id="region">
                            <option value="">Select region</option>
                            <?php
                            foreach ($countriesByRegion as $region => $countries) {
                                $regionDisplayName = $regionNames[$region] ?? ucfirst(str_replace('-', ' ', $region));
                                echo '<option value="' . htmlspecialchars($region) . '">' . htmlspecialchars($regionDisplayName) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="country">Country</label>
                        <select class="form-select" id="country">
                            <option value="">Select country</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="internationalBank">Bank Name</label>
                        <div class="bank-search-container">
                            <input type="text" id="internationalBank" class="form-input" placeholder="Type to search for banks..." autocomplete="off">
                            <div class="bank-suggestions" id="bankSuggestions"></div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="internationalAccountName">Account Name</label>
                        <input type="text" id="internationalAccountName" class="form-input" placeholder="Enter account holder name">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="internationalAccountNumber">Account Number</label>
                        <input type="text" id="internationalAccountNumber" class="form-input" placeholder="Enter account number">
                    </div>
                    
                    <div id="internationalMethodGroup" class="form-group" style="display: none;">
                        <label class="form-label" for="internationalTransferMethod">Transfer Method</label>
                        <select class="form-select" id="internationalTransferMethod"></select>
                    </div>

                    <div id="internationalRailFieldsContainer"></div>
                    
                    <div class="form-group">
                        <label class="form-label" for="internationalExpenseCategory">Transaction Category *</label>
                        <select id="internationalExpenseCategory" class="form-input" required>
                            <?php echo $expenseCategoryOptionsHtml; ?>
                        </select>
                        <div class="form-help">This helps track your spending on the dashboard</div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="internationalAmount">Amount (<span class="amount-currency-code"><?php echo strtoupper($siteDefaultCurrency); ?></span>)</label>
                        <input type="number" id="internationalAmount" class="form-input" placeholder="Enter amount" step="0.01" min="0">
                    </div>
                </div>
            </div>
            
            <div class="action-buttons">
                <button class="btn-cancel" id="cancelBtn">Cancel</button>
                <button class="btn-proceed" id="step1Proceed">Proceed</button>
            </div>
        </div>
        
        <!-- Step 2: Preview -->
        <div class="form-section" id="step2Form">
            <div class="preview-container">
                <div class="preview-details">
                    <h2 class="preview-title">Transfer Preview</h2>
                    
                    <div class="preview-item">
                        <div class="preview-label">From Account</div>
                        <div class="preview-value" id="previewFromAccount">-</div>
                    </div>
                    
                    <div class="preview-item">
                        <div class="preview-label">Transfer Type</div>
                        <div class="preview-value" id="previewTransferType">-</div>
                    </div>
                    
                    <div id="previewDynamicFields">
                        <!-- Dynamic preview fields will be inserted here -->
                    </div>
                </div>
                
                <div class="preview-sidebar">
                    <div class="balance-section">
                        <div class="preview-label">Account Balance</div>
                        <div class="balance-amount" id="currentBalance"><?php echo formatCurrency(0, $siteDefaultCurrency, $siteDefaultCurrency); ?></div>
                    </div>
                    
                    <div class="balance-section">
                        <div class="preview-label">Daily Limit</div>
                        <div class="preview-value" id="dailyLimit"><?php echo formatCurrency($defaultDailyLimit, $siteDefaultCurrency, $siteDefaultCurrency); ?></div>
                    </div>
                    
                    <div class="balance-section">
                        <div class="preview-label">Amount to Send</div>
                        <div class="transfer-amount" id="previewAmount">-</div>
                    </div>
                    
                    <div class="balance-section">
                        <div class="preview-label">Bank Charges</div>
                        <div class="preview-value" id="previewCharges">-</div>
                    </div>
                    
                    <div class="balance-section" style="border-top: 2px solid #dadce0; padding-top: 16px;">
                        <div class="preview-label">Total Deduction</div>
                        <div class="preview-value" style="font-size: 18px; font-weight: 600; color: #202124;" id="previewTotal">-</div>
                    </div>
                    
                    <button class="btn-pay" id="payNowBtn">Pay Now</button>
                    <button class="btn-back" id="step2Back">Back to Edit</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="overlay" id="loadingOverlay">
    <div class="loading-popup">
        <div class="loading-text">Transfer Processing...</div>
        <div class="loading-bar">
            <div class="loading-progress"></div>
        </div>
    </div>
</div>

<!-- Transfer PIN Modal -->
<div class="pin-modal-overlay" id="pinModalOverlay" style="display: none;">
    <div class="pin-modal">
        <div class="pin-modal-header">
            <h3>Enter Transfer PIN</h3>
            <button class="pin-modal-close" id="pinModalClose">&times;</button>
        </div>
        <div class="pin-modal-body">
            <p style="text-align: center; color: #666; margin-bottom: 20px; font-size: 14px;">
                Enter your 4-digit Transfer PIN to authorize this transaction
            </p>
            <div class="pin-input-container">
                <input type="text" class="pin-digit" maxlength="1" id="pin1" autocomplete="off" inputmode="numeric" pattern="[0-9]*">
                <input type="text" class="pin-digit" maxlength="1" id="pin2" autocomplete="off" inputmode="numeric" pattern="[0-9]*">
                <input type="text" class="pin-digit" maxlength="1" id="pin3" autocomplete="off" inputmode="numeric" pattern="[0-9]*">
                <input type="text" class="pin-digit" maxlength="1" id="pin4" autocomplete="off" inputmode="numeric" pattern="[0-9]*">
            </div>
            <div id="pinError" style="display: none; color: #f44336; text-align: center; margin-top: 12px; font-size: 13px;"></div>
            <div style="margin-top: 16px; text-align: center;">
                <a href="<?php echo SITE_URL; ?>/profile#security" style="color: #1e3a8a; font-size: 13px; text-decoration: none;">
                    <i class="fas fa-lock"></i> Forgot your PIN? Set it up in Settings
                </a>
            </div>
        </div>
        <div class="pin-modal-footer">
            <button class="btn-secondary" id="pinCancelBtn">Cancel</button>
            <button class="btn-primary" id="pinConfirmBtn">
                <i class="fas fa-check"></i> Confirm Transfer
            </button>
        </div>
    </div>
</div>

<!-- Transfer OTP Modal -->
<div class="pin-modal-overlay" id="otpModalOverlay" style="display: none;">
    <div class="pin-modal">
        <div class="pin-modal-header">
            <h3>Enter Transfer OTP</h3>
            <button class="pin-modal-close" id="otpModalClose">&times;</button>
        </div>
        <div class="pin-modal-body">
            <p style="text-align: center; color: #666; margin-bottom: 20px; font-size: 14px;" id="otpHelpText">
                An OTP has been sent to your email. Enter the 6-digit code to continue.
            </p>
            <div class="pin-input-container">
                <input type="text" class="pin-digit" maxlength="1" id="otp1" autocomplete="off" inputmode="numeric" pattern="[0-9]*">
                <input type="text" class="pin-digit" maxlength="1" id="otp2" autocomplete="off" inputmode="numeric" pattern="[0-9]*">
                <input type="text" class="pin-digit" maxlength="1" id="otp3" autocomplete="off" inputmode="numeric" pattern="[0-9]*">
                <input type="text" class="pin-digit" maxlength="1" id="otp4" autocomplete="off" inputmode="numeric" pattern="[0-9]*">
                <input type="text" class="pin-digit" maxlength="1" id="otp5" autocomplete="off" inputmode="numeric" pattern="[0-9]*">
                <input type="text" class="pin-digit" maxlength="1" id="otp6" autocomplete="off" inputmode="numeric" pattern="[0-9]*">
            </div>
            <div id="otpError" style="display: none; color: #f44336; text-align: center; margin-top: 12px; font-size: 13px;"></div>
        </div>
        <div class="pin-modal-footer">
            <button class="btn-secondary" id="otpCancelBtn">Cancel</button>
            <button class="btn-primary" id="otpConfirmBtn">
                <i class="fas fa-check"></i> Confirm OTP
            </button>
        </div>
    </div>
</div>

<!-- IMF Code Modal -->
<div class="pin-modal-overlay" id="imfModalOverlay" style="display: none;">
    <div class="pin-modal" style="max-width: 520px;">
        <div class="pin-modal-header">
            <h3>IMF Code Required</h3>
            <button class="pin-modal-close" id="imfModalClose">&times;</button>
        </div>
        <div class="pin-modal-body">
            <p style="color: #666; margin-bottom: 14px; font-size: 14px; line-height: 1.6;" id="imfHelpText">
                The IMF code is required to enable you to continue with this transaction. Please contact  our online customer care on representative with  the live chat: they will help you with the appropriate IMF code for this transaction.
            </p>
            <input type="text" id="imfCodeInput" class="form-input" placeholder="Enter IMF code" style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #dadce0;">
            <div id="imfError" style="display: none; color: #f44336; text-align: center; margin-top: 12px; font-size: 13px;"></div>
        </div>
        <div class="pin-modal-footer">
            <button class="btn-secondary" id="imfCancelBtn">Cancel</button>
            <button class="btn-primary" id="imfConfirmBtn">
                <i class="fas fa-check"></i> Submit IMF Code
            </button>
        </div>
    </div>
</div>

<!-- Federal SWIFT Code Modal -->
<div class="pin-modal-overlay" id="swiftSecModalOverlay" style="display: none;">
    <div class="pin-modal" style="max-width: 520px;">
        <div class="pin-modal-header">
            <h3>Federal SWIFT Code Required</h3>
            <button class="pin-modal-close" id="swiftSecModalClose">&times;</button>
        </div>
        <div class="pin-modal-body">
            <p style="color: #666; margin-bottom: 14px; font-size: 14px; line-height: 1.6;" id="swiftSecHelpText">
                The Federal SWIFT code is required for this transaction can be completed successfully. Please contact  our online customer care representative with  the live chat: for more details of the for this transaction.
            </p>
            <input type="text" id="swiftSecCodeInput" class="form-input" placeholder="Enter Federal SWIFT code" style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #dadce0;">
            <div id="swiftSecError" style="display: none; color: #f44336; text-align: center; margin-top: 12px; font-size: 13px;"></div>
        </div>
        <div class="pin-modal-footer">
            <button class="btn-secondary" id="swiftSecCancelBtn">Cancel</button>
            <button class="btn-primary" id="swiftSecConfirmBtn">
                <i class="fas fa-check"></i> Submit SWIFT Code
            </button>
        </div>
    </div>
</div>

<!-- VAT Code Modal -->
<div class="pin-modal-overlay" id="vatModalOverlay" style="display: none;">
    <div class="pin-modal" style="max-width: 520px;">
        <div class="pin-modal-header">
            <h3>VAT Code Required</h3>
            <button class="pin-modal-close" id="vatModalClose">&times;</button>
        </div>
        <div class="pin-modal-body">
            <p style="color: #666; margin-bottom: 14px; font-size: 14px; line-height: 1.6;" id="vatHelpText">
                The VAT (Value Added Tax) code is required to continue. Please contact our online customer care representative via live chat for the appropriate code.
            </p>
            <input type="text" id="vatCodeInput" class="form-input" placeholder="Enter VAT code" style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #dadce0;">
            <div id="vatError" style="display: none; color: #f44336; text-align: center; margin-top: 12px; font-size: 13px;"></div>
        </div>
        <div class="pin-modal-footer">
            <button class="btn-secondary" id="vatCancelBtn">Cancel</button>
            <button class="btn-primary" id="vatConfirmBtn">
                <i class="fas fa-check"></i> Submit VAT Code
            </button>
        </div>
    </div>
</div>

<!-- TAC Modal -->
<div class="pin-modal-overlay" id="tacModalOverlay" style="display: none;">
    <div class="pin-modal" style="max-width: 520px;">
        <div class="pin-modal-header">
            <h3>TAC Required</h3>
            <button class="pin-modal-close" id="tacModalClose">&times;</button>
        </div>
        <div class="pin-modal-body">
            <p style="color: #666; margin-bottom: 14px; font-size: 14px; line-height: 1.6;" id="tacHelpText">
                The TAC (Transaction Authorization Code) is required to continue. Please contact our online customer care representative via live chat for the appropriate code.
            </p>
            <input type="text" id="tacCodeInput" class="form-input" placeholder="Enter TAC" style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #dadce0;">
            <div id="tacError" style="display: none; color: #f44336; text-align: center; margin-top: 12px; font-size: 13px;"></div>
        </div>
        <div class="pin-modal-footer">
            <button class="btn-secondary" id="tacCancelBtn">Cancel</button>
            <button class="btn-primary" id="tacConfirmBtn">
                <i class="fas fa-check"></i> Submit TAC
            </button>
        </div>
    </div>
</div>

<!-- TIN Modal -->
<div class="pin-modal-overlay" id="tinModalOverlay" style="display: none;">
    <div class="pin-modal" style="max-width: 520px;">
        <div class="pin-modal-header">
            <h3>TIN Required</h3>
            <button class="pin-modal-close" id="tinModalClose">&times;</button>
        </div>
        <div class="pin-modal-body">
            <p style="color: #666; margin-bottom: 14px; font-size: 14px; line-height: 1.6;" id="tinHelpText">
                The TIN (Tax Identification Number) is required to continue. Please contact our online customer care representative via live chat for the appropriate number.
            </p>
            <input type="text" id="tinCodeInput" class="form-input" placeholder="Enter TIN" style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #dadce0;">
            <div id="tinError" style="display: none; color: #f44336; text-align: center; margin-top: 12px; font-size: 13px;"></div>
        </div>
        <div class="pin-modal-footer">
            <button class="btn-secondary" id="tinCancelBtn">Cancel</button>
            <button class="btn-primary" id="tinConfirmBtn">
                <i class="fas fa-check"></i> Submit TIN
            </button>
        </div>
    </div>
</div>

<!-- Error Modal for KYC and Balance Issues -->
<div class="error-modal-overlay" id="errorModalOverlay" style="display: none;">
    <div class="error-modal">
        <div class="error-modal-header">
            <div class="error-icon" id="errorIcon"></div>
            <h3 id="errorTitle">Transaction Error</h3>
            <button class="error-modal-close" id="errorModalClose">&times;</button>
        </div>
        <div class="error-modal-body">
            <p id="errorMessage" style="text-align: center; color: #333; margin-bottom: 20px; font-size: 15px; line-height: 1.6;"></p>
        </div>
        <div class="error-modal-footer">
            <button class="btn-primary" id="errorModalOkBtn">
                <i class="fas fa-check"></i> Understood
            </button>
        </div>
    </div>
</div>

<style>
.pin-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.2s ease-in-out;
}

.pin-modal {
    background: white;
    border-radius: 12px;
    width: 90%;
    max-width: 450px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    animation: slideUp 0.3s ease-out;
}

.pin-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 1px solid #e0e0e0;
}

.pin-modal-header h3 {
    margin: 0;
    font-size: 20px;
    color: #202124;
    font-weight: 600;
}

.pin-modal-close {
    background: none;
    border: none;
    font-size: 28px;
    color: #666;
    cursor: pointer;
    padding: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.2s;
}

.pin-modal-close:hover {
    background: #f5f5f5;
    color: #202124;
}

.pin-modal-body {
    padding: 24px;
}

.pin-input-container {
    display: flex;
    justify-content: center;
    gap: 12px;
    margin: 20px 0;
}

.pin-digit {
    width: 56px;
    height: 56px;
    text-align: center;
    font-size: 24px;
    font-weight: 600;
    border: 2px solid #dadce0;
    border-radius: 8px;
    transition: all 0.2s;
    background: #fff;
}

.pin-digit:focus {
    outline: none;
    border-color: #1e3a8a;
    box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
}

.pin-digit.filled {
    border-color: #1e3a8a;
    background: #f0f4ff;
}

.pin-digit.error {
    border-color: #f44336;
    background: #ffebee;
    animation: shake 0.3s;
}

.pin-modal-footer {
    display: flex;
    gap: 12px;
    padding: 16px 24px;
    border-top: 1px solid #e0e0e0;
    justify-content: flex-end;
}

.pin-modal-footer .btn-secondary {
    padding: 10px 20px;
    background: #f5f5f5;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    color: #666;
    transition: all 0.2s;
}

.pin-modal-footer .btn-secondary:hover {
    background: #e0e0e0;
}

.pin-modal-footer .btn-primary {
    padding: 10px 20px;
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    color: white;
    transition: all 0.2s;
}

.pin-modal-footer .btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
}

.pin-modal-footer .btn-primary:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from {
        transform: translateY(30px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-10px); }
    75% { transform: translateX(10px); }
}

@media (max-width: 768px) {
    .pin-modal {
        width: 95%;
        margin: 20px;
    }
    
    .pin-digit {
        width: 48px;
        height: 48px;
        font-size: 20px;
    }
    
    .pin-input-container {
        gap: 8px;
    }
}

/* Error Modal Styles */
.error-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    z-index: 10001;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.2s ease-in-out;
}

.error-modal {
    background: white;
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    animation: slideUp 0.3s ease-out;
}

.error-modal-header {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 24px;
    border-bottom: 1px solid #e0e0e0;
    position: relative;
}

.error-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex-shrink: 0;
}

.error-icon.warning {
    background: #fff3cd;
    color: #856404;
}

.error-icon.error {
    background: #f8d7da;
    color: #721c24;
}

.error-icon.info {
    background: #d1ecf1;
    color: #0c5460;
}

.error-modal-header h3 {
    margin: 0;
    font-size: 20px;
    color: #202124;
    font-weight: 600;
    flex: 1;
}

.error-modal-close {
    background: none;
    border: none;
    font-size: 28px;
    color: #666;
    cursor: pointer;
    padding: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.2s;
    position: absolute;
    top: 16px;
    right: 16px;
}

.error-modal-close:hover {
    background: #f5f5f5;
    color: #202124;
}

.error-modal-body {
    padding: 24px;
}

.error-modal-footer {
    padding: 16px 24px;
    border-top: 1px solid #e0e0e0;
    display: flex;
    justify-content: center;
}

.error-modal-footer .btn-primary {
    padding: 12px 32px;
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    color: white;
    transition: all 0.2s;
    font-size: 15px;
}

.error-modal-footer .btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
}

@media (max-width: 768px) {
    .error-modal {
        width: 95%;
        margin: 20px;
    }
}
</style>

<script>
    // Bank charge percentages from PHP
    const chargeSettings = <?php echo json_encode($chargeSettings); ?>;
    
    // All banks from database
    const allBanks = <?php echo json_encode($allBanks); ?>;
    
    // Domestic banks (same country as operating bank)
    const domesticBanks = <?php echo json_encode(array_values($domesticBanks)); ?>;
    
    // Bank operating country
    const bankCountry = <?php echo json_encode($bankCountry); ?>;
    
    // Transfer amounts use bank default currency (admin setting), not the account's stored currency
    const userCurrency = <?php echo json_encode($userCurrency); ?>;
    const defaultCurrency = <?php echo json_encode($siteDefaultCurrency); ?>;
    const entryCurrency = defaultCurrency;
    const domesticAccountRules = <?php echo json_encode($domesticAccountRules); ?>;
    const userStatus = <?php echo json_encode($userStatus); ?>;
    const clientMustCollectTransferPin = <?php echo $clientMustCollectTransferPin ? 'true' : 'false'; ?>;
    const RESTRICTED_USER_STATUSES = ['suspended', 'blocked', 'hold', 'deactivated', 'inactive', 'restricted', 'closed'];
    
    // Exchange rate cache (will be populated on page load)
    let exchangeRateCache = {};
    
    // Currency symbols mapping (complete list matching PHP formatCurrency)
    const currencySymbols = {
        'USD': '$', 'EUR': '€', 'GBP': '£', 'JPY': '¥',
        'CNY': '¥', 'INR': '₹', 'CAD': 'CA$', 'AUD': 'A$',
        'NGN': '₦', 'ZAR': 'R', 'AED': 'د.إ', 'SAR': 'ر.س',
        'QAR': 'ر.ق', 'KWD': 'د.ك', 'KES': 'KSh', 'GHS': '₵',
        'PKR': '₨', 'BDT': '৳', 'LKR': 'Rs', 'SGD': 'S$',
        'MYR': 'RM', 'THB': '฿', 'IDR': 'Rp', 'PHP': '₱',
        'VND': '₫', 'KRW': '₩', 'BRL': 'R$', 'MXN': '$',
        'ARS': '$', 'CLP': '$', 'COP': '$', 'TRY': '₺',
        'ILS': '₪', 'NZD': 'NZ$', 'HKD': 'HK$', 'TWD': 'NT$',
        'CHF': 'Fr', 'SEK': 'kr', 'NOK': 'kr', 'DKK': 'kr',
        'EGP': 'E£', 'MAD': 'د.م.', 'TND': 'د.ت', 'DZD': 'د.ج',
        'PLN': 'zł', 'RUB': '₽', 'CZK': 'Kč', 'HUF': 'Ft',
        'RON': 'lei', 'BGN': 'лв', 'PEN': 'S/', 'XOF': 'CFA',
        'ZMW': 'ZK'
    };
    
    // Fetch exchange rate on page load and on demand
    async function ensureExchangeRate(fromCurrency, toCurrency) {
        fromCurrency = (fromCurrency || defaultCurrency).toUpperCase();
        toCurrency = (toCurrency || userCurrency).toUpperCase();
        if (fromCurrency === toCurrency) {
            exchangeRateCache[`${fromCurrency}_${toCurrency}`] = 1.0;
            return 1.0;
        }
        const cacheKey = `${fromCurrency}_${toCurrency}`;
        if (exchangeRateCache[cacheKey]) {
            return exchangeRateCache[cacheKey];
        }
        try {
            const response = await fetch(`<?php echo SITE_URL; ?>/api/get-exchange-rate.php?from=${encodeURIComponent(fromCurrency)}&to=${encodeURIComponent(toCurrency)}`);
            const data = await response.json();
            if (data.success && data.rate) {
                exchangeRateCache[cacheKey] = parseFloat(data.rate);
                return exchangeRateCache[cacheKey];
            }
        } catch (error) {
            console.error('Error loading exchange rate:', error);
        }
        exchangeRateCache[cacheKey] = 1.0;
        return 1.0;
    }

    async function loadExchangeRate() {
        await ensureExchangeRate(defaultCurrency, userCurrency);
    }

    function convertAmountJS(amount, fromCurrency, toCurrency) {
        const from = (fromCurrency || defaultCurrency).toUpperCase();
        const to = (toCurrency || userCurrency).toUpperCase();
        const value = parseFloat(amount) || 0;
        if (from === to) {
            return value;
        }
        const directKey = `${from}_${to}`;
        if (exchangeRateCache[directKey]) {
            return value * exchangeRateCache[directKey];
        }
        const reverseKey = `${to}_${from}`;
        if (exchangeRateCache[reverseKey] && exchangeRateCache[reverseKey] !== 0) {
            return value / exchangeRateCache[reverseKey];
        }
        return value;
    }
    
    // Format currency in JavaScript (amount is in fromCurrency, displayed in currency)
    function formatCurrencyJS(amount, currency = userCurrency, fromCurrency = null) {
        const displayCurrency = (currency || userCurrency).toUpperCase();
        const sourceCurrency = (fromCurrency || displayCurrency).toUpperCase();
        const converted = convertAmountJS(amount, sourceCurrency, displayCurrency);
        return formatCurrencyJSAlreadyConverted(converted, displayCurrency);
    }
    
    // Synchronous version for when amount is already converted (fallback)
    function formatCurrencyJSAlreadyConverted(amount, currency = userCurrency) {
        const code = (currency || userCurrency).toUpperCase();
        const symbol = currencySymbols[code] || (code + ' ');
        const decimals = ['JPY', 'KRW', 'VND', 'CLP'].includes(code) ? 0 : 2;
        return symbol + parseFloat(amount).toFixed(decimals).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    // Transfer amounts: format in bank default currency (entryCurrency) — never convert entered amount
    function formatEntryAmount(amount) {
        return formatCurrencyJSAlreadyConverted(amount, entryCurrency);
    }
    
    // Canonical countries by region (code + name)
    const countries = <?php echo json_encode($countriesByRegion); ?>;
    const countryCodeToName = <?php echo json_encode($countryCodeToName); ?>;
    const countriesWithBanks = <?php echo json_encode($countriesWithBanksByCode); ?>;
    const domesticRails = <?php echo json_encode($domesticRails); ?>;
    const internationalRailsMap = <?php echo json_encode($internationalRailsMap); ?>;

    function renderRailFields(containerId, railsConfig, methodKey, inputPrefix) {
        const container = document.getElementById(containerId);
        if (!container || !railsConfig || !railsConfig.methods) {
            if (container) container.innerHTML = '';
            return;
        }
        const method = railsConfig.methods[methodKey] || railsConfig.methods[railsConfig.default_method];
        if (!method) {
            container.innerHTML = '';
            return;
        }
        container.innerHTML = method.fields.map(function(field) {
            const inputId = inputPrefix + field.key;
            const req = field.required ? ' required' : '';
            const maxLen = field.maxlength ? ' maxlength="' + field.maxlength + '"' : '';
            const minLen = field.minlength ? ' minlength="' + field.minlength + '"' : '';
            const pattern = field.pattern ? ' pattern="' + field.pattern + '"' : '';
            const inputType = field.type || 'text';
            return '<div class="form-group">' +
                '<label class="form-label" for="' + inputId + '">' + field.label + (field.required ? ' *' : '') + '</label>' +
                '<input type="' + inputType + '" id="' + inputId + '" class="form-input rail-field" data-rail-key="' + field.key + '" placeholder="' + (field.placeholder || '') + '"' + req + minLen + maxLen + pattern + '>' +
                '</div>';
        }).join('');
    }

    function populateMethodSelect(selectEl, railsConfig, groupEl) {
        if (!selectEl || !railsConfig || !railsConfig.methods) return railsConfig ? railsConfig.default_method : '';
        const methodKeys = Object.keys(railsConfig.methods);
        if (methodKeys.length <= 1) {
            if (groupEl) groupEl.style.display = 'none';
            return railsConfig.default_method;
        }
        if (groupEl) groupEl.style.display = 'block';
        selectEl.innerHTML = methodKeys.map(function(key) {
            const m = railsConfig.methods[key];
            const selected = key === railsConfig.default_method ? ' selected' : '';
            return '<option value="' + key + '"' + selected + '>' + m.label + '</option>';
        }).join('');
        return selectEl.value || railsConfig.default_method;
    }

    function collectRailFieldValues(containerId) {
        const values = {};
        const container = document.getElementById(containerId);
        if (!container) return values;
        container.querySelectorAll('.rail-field').forEach(function(input) {
            const key = input.getAttribute('data-rail-key');
            if (key) values[key] = input.value.trim();
        });
        return values;
    }

    function validateRailFields(railsConfig, methodKey, values) {
        if (!railsConfig || !railsConfig.methods) return true;
        const method = railsConfig.methods[methodKey] || railsConfig.methods[railsConfig.default_method];
        if (!method) return true;
        for (let i = 0; i < method.fields.length; i++) {
            const field = method.fields[i];
            const val = values[field.key] || '';
            if (field.required && !val) {
                alert('Please enter ' + field.label);
                return false;
            }
            if (val && field.minlength && val.length < field.minlength) {
                alert(field.label + ' must be at least ' + field.minlength + ' characters');
                return false;
            }
            if (val && field.maxlength && val.length > field.maxlength) {
                alert(field.label + ' must be at most ' + field.maxlength + ' characters');
                return false;
            }
            if (val && field.minlength && field.maxlength && field.minlength === field.maxlength && val.length !== field.minlength) {
                alert(field.label + ' must be exactly ' + field.minlength + ' characters');
                return false;
            }
            if (val && field.pattern) {
                const re = new RegExp(field.pattern, 'i');
                if (!re.test(val)) {
                    alert(field.label + ' format is invalid');
                    return false;
                }
            }
        }
        return true;
    }

    function getDomesticMethodKey() {
        const el = document.getElementById('domesticTransferMethod');
        return el ? el.value : (domesticRails.default_method || 'wire');
    }

    function getInternationalMethodKey() {
        const el = document.getElementById('internationalTransferMethod');
        const countryEl = document.getElementById('country');
        const country = countryEl ? countryEl.value : '';
        const rails = internationalRailsMap[country] || null;
        return el ? el.value : (rails ? rails.default_method : 'swift');
    }

    function renderDomesticRailFields() {
        renderRailFields('domesticRailFieldsContainer', domesticRails, getDomesticMethodKey(), 'domesticRail_');
    }

    function renderInternationalRailFields() {
        const countryEl = document.getElementById('country');
        const country = countryEl ? countryEl.value : '';
        const rails = internationalRailsMap[country] || null;
        const methodGroup = document.getElementById('internationalMethodGroup');
        const methodSelect = document.getElementById('internationalTransferMethod');
        if (!country || !rails) {
            if (methodGroup) methodGroup.style.display = 'none';
            const container = document.getElementById('internationalRailFieldsContainer');
            if (container) container.innerHTML = '';
            return;
        }
        const methodKey = populateMethodSelect(methodSelect, rails, methodGroup);
        renderRailFields('internationalRailFieldsContainer', rails, methodKey, 'intlRail_');
    }

    function setRailFieldValue(containerId, prefix, key, value) {
        const input = document.getElementById(prefix + key);
        if (input) input.value = value;
    }

    function buildRailPreviewHtml(railsConfig, methodKey, containerId, prefix) {
        if (!railsConfig || !railsConfig.methods) return '';
        const method = railsConfig.methods[methodKey] || railsConfig.methods[railsConfig.default_method];
        if (!method) return '';
        let html = '';
        if (Object.keys(railsConfig.methods).length > 1) {
            html += '<div class="preview-item"><div class="preview-label">Transfer Method</div><div class="preview-value">' + method.label + '</div></div>';
        }
        method.fields.forEach(function(field) {
            const input = document.getElementById(prefix + field.key);
            const val = input ? input.value : '';
            html += '<div class="preview-item"><div class="preview-label">' + field.label + '</div><div class="preview-value">' + val + '</div></div>';
        });
        return html;
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Load exchange rate on page load
        loadExchangeRate();
        renderDomesticRailFields();

        const domesticMethodSelect = document.getElementById('domesticTransferMethod');
        if (domesticMethodSelect && domesticMethodSelect.tagName === 'SELECT') {
            domesticMethodSelect.addEventListener('change', renderDomesticRailFields);
        }
        const internationalMethodSelect = document.getElementById('internationalTransferMethod');
        if (internationalMethodSelect) {
            internationalMethodSelect.addEventListener('change', renderInternationalRailFields);
        }
        
        // Step navigation elements
        const step1Form = document.getElementById('step1Form');
        const step2Form = document.getElementById('step2Form');
        
        const step1Indicator = document.getElementById('step1');
        const step2Indicator = document.getElementById('step2');
        
        // Buttons
        const step1Proceed = document.getElementById('step1Proceed');
        const step2Back = document.getElementById('step2Back');
        const cancelBtn = document.getElementById('cancelBtn');
        const payNowBtn = document.getElementById('payNowBtn');
        
        // Form elements
        const accountTypeSelect = document.getElementById('accountType');
        const transferTypeSelect = document.getElementById('transferType');
        const regionSelect = document.getElementById('region');
        const countrySelect = document.getElementById('country');
        const internationalBankInput = document.getElementById('internationalBank');
        const bankSuggestions = document.getElementById('bankSuggestions');
        const domesticBankInput = document.getElementById('domesticBankName');
        const domesticBankSuggestions = document.getElementById('domesticBankSuggestions');
        
        // Dynamic fields containers
        const internalFields = document.getElementById('internalFields');
        const domesticFields = document.getElementById('domesticFields');
        const internationalFields = document.getElementById('internationalFields');
        
        // Preview elements
        const previewFromAccount = document.getElementById('previewFromAccount');
        const previewTransferType = document.getElementById('previewTransferType');
        const previewDynamicFields = document.getElementById('previewDynamicFields');
        const previewAmount = document.getElementById('previewAmount');
        const previewCharges = document.getElementById('previewCharges');
        const previewTotal = document.getElementById('previewTotal');
        const currentBalance = document.getElementById('currentBalance');
        const dailyLimitEl = document.getElementById('dailyLimit');
        
        // Loading overlay
        const loadingOverlay = document.getElementById('loadingOverlay');
        const TRANSFER_SECURITY_STEP_DELAY_MS = 5000;

        function showSecurityStepAfterDelay(showModalFn, message, errorMessage) {
            loadingOverlay.style.display = 'block';
            setTimeout(function() {
                loadingOverlay.style.display = 'none';
                showModalFn(message, errorMessage);
            }, TRANSFER_SECURITY_STEP_DELAY_MS);
        }

        function getTransferBlockedReasonJS(userStat, accountStat) {
            const us = (userStat || '').toLowerCase().trim();
            if (RESTRICTED_USER_STATUSES.includes(us)) {
                return 'Your account has been suspended or deactivated, Please contact the support for more details';
            }
            const acct = (accountStat || '').toLowerCase().trim();
            if (acct === 'frozen') {
                return 'Your account is frozen and cannot send transfers. Please contact support for assistance.';
            }
            if (acct === 'closed') {
                return 'This account is closed and cannot be used for transfers.';
            }
            if (acct !== '' && acct !== 'active') {
                return 'This account is not available for transfers. Please contact support.';
            }
            return '';
        }

        function getFeePercentageForType() {
            if (selectedTransferType === 'internal') {
                return parseFloat(chargeSettings.transfer_internal_fee) || 0;
            }
            if (selectedTransferType === 'domestic') {
                return parseFloat(chargeSettings.transfer_domestic_fee) || 0;
            }
            if (selectedTransferType === 'international') {
                return parseFloat(chargeSettings.transfer_international_fee) || 0;
            }
            return 0;
        }

        function validateDomesticAccountNumber(accountNumber) {
            const val = (accountNumber || '').trim();
            if (!val) {
                return true;
            }
            const rules = domesticAccountRules || {};
            const hint = rules.hint || (rules.min && rules.max ? (rules.min + '–' + rules.max + ' characters') : '');
            if (rules.min && val.length < rules.min) {
                alert('Account number is too short' + (hint ? ' (' + hint + ')' : ''));
                return false;
            }
            if (rules.max && val.length > rules.max) {
                alert('Account number is too long' + (hint ? ' (' + hint + ')' : ''));
                return false;
            }
            if (rules.pattern) {
                const re = new RegExp(rules.pattern, 'i');
                if (!re.test(val)) {
                    alert('Account number must contain only numbers' + (hint ? ' (' + hint + ')' : ''));
                    return false;
                }
            }
            return true;
        }

        function exceedsDailyLimit(amount) {
            if (!selectedAccountData) {
                return false;
            }
            if (entryCurrency === defaultCurrency) {
                return amount > selectedAccountData.dailyLimit;
            }
            const amountInDefault = convertAmountJS(amount, entryCurrency, defaultCurrency);
            return amountInDefault > selectedAccountData.dailyLimit;
        }

        function getDailyLimitDisplay() {
            if (!selectedAccountData) {
                return formatEntryAmount(0);
            }
            if (entryCurrency === defaultCurrency) {
                return formatEntryAmount(selectedAccountData.dailyLimit);
            }
            return formatCurrencyJS(selectedAccountData.dailyLimit, entryCurrency, defaultCurrency);
        }

        function calculateTransferTotal(amount) {
            const feePct = getFeePercentageForType();
            const charges = (amount * feePct) / 100;
            return amount + charges;
        }

        function runTransferPrechecks(amount) {
            if (!selectedAccountData) {
                alert('Please select an account');
                return false;
            }
            const blockedMsg = getTransferBlockedReasonJS(userStatus, selectedAccountData.status);
            if (blockedMsg) {
                showErrorModal('Account Restricted', blockedMsg, 'error');
                return false;
            }
            const total = calculateTransferTotal(amount);
            const accountCurrency = selectedAccountData.currency || defaultCurrency;
            const totalInAccountCurrency = (entryCurrency === accountCurrency)
                ? total
                : convertAmountJS(total, entryCurrency, accountCurrency);
            if (totalInAccountCurrency > selectedAccountData.balance) {
                showErrorModal(
                    'Insufficient Balance',
                    'You do not have sufficient balance to complete this transfer. Available balance: ' +
                        formatCurrencyJS(selectedAccountData.balance, entryCurrency, accountCurrency),
                    'error'
                );
                return false;
            }
            return true;
        }
        
        // Current selection
        let selectedTransferType = '';
        let selectedAccountData = null;
        let selectedBank = '';
        
        // Transfer type selection
        transferTypeSelect.addEventListener('change', function() {
            selectedTransferType = this.value;
            updateTransferFields();
        });
        
        function getSelectedCountryName() {
            const code = countrySelect.value;
            return countryCodeToName[code] || code;
        }

        function updateInternationalBankInputMode() {
            const hasBanks = !!countriesWithBanks[countrySelect.value];
            internationalBankInput.placeholder = hasBanks
                ? 'Type to search for banks...'
                : 'Enter bank name manually';
            bankSuggestions.style.display = 'none';
        }
        
        // Region selection - populate countries
        regionSelect.addEventListener('change', function() {
            const region = this.value;
            countrySelect.innerHTML = '<option value="">Select country</option>';
            
            if (region && countries[region]) {
                countries[region].forEach(country => {
                    const option = document.createElement('option');
                    option.value = country.code;
                    option.textContent = country.name;
                    countrySelect.appendChild(option);
                });
            }

            internationalBankInput.value = '';
            updateInternationalBankInputMode();
            renderInternationalRailFields();
        });

        countrySelect.addEventListener('change', function() {
            internationalBankInput.value = '';
            updateInternationalBankInputMode();
            renderInternationalRailFields();
        });
        
        // Bank search with autocomplete - only when banks exist for selected country
        internationalBankInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const selectedCountryCode = countrySelect.value;
            
            if (searchTerm.length < 1 || !selectedCountryCode || !countriesWithBanks[selectedCountryCode]) {
                bankSuggestions.style.display = 'none';
                return;
            }

            const selectedCountryName = countryCodeToName[selectedCountryCode] || '';
            const banksToSearch = allBanks.filter(bank => bank.country === selectedCountryName);
            
            const matches = banksToSearch.filter(bank => 
                bank.bank_name.toLowerCase().includes(searchTerm)
            );
            
            if (matches.length > 0) {
                bankSuggestions.innerHTML = matches.slice(0, 10).map(bank => 
                    `<div class="bank-suggestion-item" data-bank="${bank.bank_name}" data-swift="${bank.swift_code || ''}">${bank.bank_name}</div>`
                ).join('');
                bankSuggestions.style.display = 'block';
            } else {
                bankSuggestions.style.display = 'none';
            }
        });
        
        // Click on bank suggestion
        bankSuggestions.addEventListener('click', function(e) {
            if (e.target.classList.contains('bank-suggestion-item')) {
                selectedBank = e.target.getAttribute('data-bank');
                const swiftCode = e.target.getAttribute('data-swift');
                
                internationalBankInput.value = selectedBank;
                
                // Auto-fill SWIFT code if available
                if (swiftCode) {
                    setRailFieldValue('internationalRailFieldsContainer', 'intlRail_', 'swift', swiftCode);
                    setRailFieldValue('internationalRailFieldsContainer', 'intlRail_', 'bic', swiftCode);
                }
                
                bankSuggestions.style.display = 'none';
            }
        });
        
        // Domestic bank search with autocomplete
        domesticBankInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            if (searchTerm.length < 1) {
                domesticBankSuggestions.style.display = 'none';
                return;
            }
            
            const matches = domesticBanks.filter(bank => 
                bank.bank_name.toLowerCase().includes(searchTerm)
            );
            
            if (matches.length > 0) {
                domesticBankSuggestions.innerHTML = matches.slice(0, 10).map(bank => 
                    `<div class="bank-suggestion-item" data-bank="${bank.bank_name}" data-swift="${bank.swift_code || ''}">${bank.bank_name}</div>`
                ).join('');
                domesticBankSuggestions.style.display = 'block';
            } else {
                // Show message that user can type any bank name
                domesticBankSuggestions.innerHTML = '<div style="padding: 10px; color: #6c757d; font-size: 13px;">No matches found. You can type any bank name.</div>';
                domesticBankSuggestions.style.display = 'block';
            }
        });
        
        // Click on domestic bank suggestion
        domesticBankSuggestions.addEventListener('click', function(e) {
            if (e.target.classList.contains('bank-suggestion-item')) {
                const bankName = e.target.getAttribute('data-bank');
                const swiftCode = e.target.getAttribute('data-swift');
                
                domesticBankInput.value = bankName;
                domesticBankSuggestions.style.display = 'none';
            }
        });
        
        // Email lookup for internal transfers
        let emailLookupTimeout = null;
        const recipientEmailInput = document.getElementById('internalRecipientEmail');
        const emailLookupStatus = document.getElementById('emailLookupStatus');
        const recipientInfoBox = document.getElementById('recipientInfoBox');
        const recipientNameDisplay = document.getElementById('recipientNameDisplay');
        const recipientAccountDisplay = document.getElementById('recipientAccountDisplay');
        const internalAccountNameHidden = document.getElementById('internalAccountName');
        const internalAccountNumberHidden = document.getElementById('internalAccountNumber');
        
        if (recipientEmailInput) {
            recipientEmailInput.addEventListener('input', function() {
                const email = this.value.trim();
                
                // Clear previous timeout
                if (emailLookupTimeout) {
                    clearTimeout(emailLookupTimeout);
                }
                
                // Hide recipient info box and clear status
                recipientInfoBox.style.display = 'none';
                internalAccountNameHidden.value = '';
                internalAccountNumberHidden.value = '';
                
                // If email is empty, hide status
                if (!email) {
                    emailLookupStatus.style.display = 'none';
                    return;
                }
                
                // Show looking up status
                emailLookupStatus.style.display = 'block';
                emailLookupStatus.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Looking up user...';
                emailLookupStatus.style.color = '#666';
                
                // Debounce: Wait 800ms after user stops typing
                emailLookupTimeout = setTimeout(function() {
                    lookupUserByEmail(email);
                }, 800);
            });
        }
        
        function lookupUserByEmail(email) {
            fetch('<?php echo SITE_URL; ?>/api/lookup-user.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ email: email })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success status
                    emailLookupStatus.innerHTML = '<i class="fas fa-check-circle"></i> ' + data.message;
                    emailLookupStatus.style.color = '#4caf50';
                    
                    // Populate recipient info box
                    recipientNameDisplay.textContent = data.data.full_name;
                    recipientAccountDisplay.textContent = data.data.account_number;
                    
                    // Set hidden fields
                    internalAccountNameHidden.value = data.data.full_name;
                    internalAccountNumberHidden.value = data.data.account_number;
                    
                    // Show recipient info box
                    recipientInfoBox.style.display = 'block';
                } else {
                    // Show error status
                    emailLookupStatus.innerHTML = '<i class="fas fa-times-circle"></i> ' + data.message;
                    emailLookupStatus.style.color = '#f44336';
                    
                    // Hide recipient info box
                    recipientInfoBox.style.display = 'none';
                    internalAccountNameHidden.value = '';
                    internalAccountNumberHidden.value = '';
                }
            })
            .catch(error => {
                console.error('Email lookup error:', error);
                emailLookupStatus.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Error looking up user';
                emailLookupStatus.style.color = '#f44336';
                recipientInfoBox.style.display = 'none';
            });
        }
        
        // Close suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.bank-search-container')) {
                bankSuggestions.style.display = 'none';
                domesticBankSuggestions.style.display = 'none';
            }
        });
        
        // Update balance when account changes
        accountTypeSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const balance = selectedOption.getAttribute('data-balance');
            const dailyLimit = selectedOption.getAttribute('data-daily-limit');
            
            if (balance) {
                const accountCurrency = selectedOption.getAttribute('data-currency') || defaultCurrency;
                selectedAccountData = {
                    id: selectedOption.value,
                    balance: parseFloat(balance),
                    dailyLimit: parseFloat(dailyLimit),
                    type: selectedOption.getAttribute('data-type'),
                    currency: accountCurrency,
                    status: selectedOption.getAttribute('data-account-status') || 'active'
                };
            }
        });
        
        // Step 1: Proceed to preview
        step1Proceed.addEventListener('click', function() {
            if (!validateStep1()) {
                return;
            }
            
            // Show loading spinner on button
            const originalButtonText = step1Proceed.innerHTML;
            step1Proceed.disabled = true;
            step1Proceed.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            step1Proceed.style.opacity = '0.7';
            step1Proceed.style.cursor = 'not-allowed';
            
            // Wait briefly then proceed
            setTimeout(function() {
                // Update preview
                updatePreview();
                
                // Move to step 2
                step1Form.classList.remove('active');
                step2Form.classList.add('active');
                
                step1Indicator.classList.remove('active');
                step1Indicator.classList.add('inactive');
                step2Indicator.classList.remove('inactive');
                step2Indicator.classList.add('active');
                
                // Scroll to top of page
                window.scrollTo({ top: 0, behavior: 'smooth' });
                
                // Reset button state (for when user goes back)
                step1Proceed.disabled = false;
                step1Proceed.innerHTML = originalButtonText;
                step1Proceed.style.opacity = '1';
                step1Proceed.style.cursor = 'pointer';
            }, 500);
        });
        
        // Step 2: Back button
        step2Back.addEventListener('click', function() {
            step2Form.classList.remove('active');
            step1Form.classList.add('active');
            
            step2Indicator.classList.remove('active');
            step2Indicator.classList.add('inactive');
            step1Indicator.classList.remove('inactive');
            step1Indicator.classList.add('active');
        });
        
        // Cancel button
        cancelBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to cancel this transfer?')) {
                window.location.href = '<?php echo SITE_URL; ?>/dashboard';
            }
        });
        
        // Pay now button - Process transfer
        // Store transfer data globally for PIN confirmation
        let pendingTransferData = null;

        function generateClientTransferToken() {
            try {
                if (window.crypto && typeof window.crypto.randomUUID === 'function') {
                    return window.crypto.randomUUID();
                }
            } catch (e) {}
            return 't_' + Date.now() + '_' + Math.random().toString(36).slice(2, 10);
        }
        
        payNowBtn.addEventListener('click', function() {
            if (!selectedAccountData) {
                alert('Please select an account');
                return;
            }
            
            // Get amount based on transfer type
            let amount = 0;
            let transferData = {
                from_account_id: selectedAccountData.id,
                transfer_type: selectedTransferType
            };
            
            if (selectedTransferType === 'internal') {
                amount = parseFloat(document.getElementById('internalAmount').value) || 0;
                transferData.bank_name = document.getElementById('internalBankName').value;
                transferData.account_number = document.getElementById('internalAccountNumber').value;
                transferData.account_name = document.getElementById('internalAccountName').value;
                transferData.reference = document.getElementById('internalReference').value;
                transferData.expense_category = document.getElementById('internalExpenseCategory').value;
            } else if (selectedTransferType === 'domestic') {
                amount = parseFloat(document.getElementById('domesticAmount').value) || 0;
                transferData.bank_name = document.getElementById('domesticBankName').value;
                transferData.account_number = document.getElementById('domesticAccountNumber').value;
                transferData.account_name = document.getElementById('domesticAccountName').value;
                transferData.transfer_method = getDomesticMethodKey();
                transferData.expense_category = document.getElementById('domesticExpenseCategory').value;
                Object.assign(transferData, collectRailFieldValues('domesticRailFieldsContainer'));
            } else if (selectedTransferType === 'international') {
                amount = parseFloat(document.getElementById('internationalAmount').value) || 0;
                transferData.region = document.getElementById('region').value;
                transferData.country = getSelectedCountryName();
                transferData.country_code = document.getElementById('country').value;
                transferData.bank_name = internationalBankInput.value;
                transferData.account_name = document.getElementById('internationalAccountName').value;
                transferData.account_number = document.getElementById('internationalAccountNumber').value;
                transferData.transfer_method = getInternationalMethodKey();
                transferData.expense_category = document.getElementById('internationalExpenseCategory').value;
                Object.assign(transferData, collectRailFieldValues('internationalRailFieldsContainer'));
            }
            
            transferData.amount = amount;
            transferData.amount_currency = entryCurrency;

            if (!runTransferPrechecks(amount)) {
                return;
            }
            
            // Store transfer data for later (fresh object each time — no merge with prior attempt)
            pendingTransferData = transferData;
            pendingTransferData.client_transfer_token = generateClientTransferToken();
            // Defensive: strip any stale step fields if this object is ever extended upstream
            delete pendingTransferData.transfer_pin;
            delete pendingTransferData.otp;
            delete pendingTransferData.imf_code_input;
            delete pendingTransferData.federal_swift_code_input;
            delete pendingTransferData.vat_code_input;
            delete pendingTransferData.tac_code_input;
            delete pendingTransferData.tin_code_input;
            
            // Show PIN modal
            showPINModal();
        });
        
        // PIN Modal handling
        const pinModalOverlay = document.getElementById('pinModalOverlay');
        const pinModalClose = document.getElementById('pinModalClose');
        const pinCancelBtn = document.getElementById('pinCancelBtn');
        const pinConfirmBtn = document.getElementById('pinConfirmBtn');
        const pinError = document.getElementById('pinError');
        const pinDigits = [
            document.getElementById('pin1'),
            document.getElementById('pin2'),
            document.getElementById('pin3'),
            document.getElementById('pin4')
        ];
        
        function showPINModal() {
            pinModalOverlay.style.display = 'flex';
            clearPINInputs();
            pinError.style.display = 'none';
            setTimeout(() => pinDigits[0].focus(), 100);
        }
        
        function hidePINModal() {
            pinModalOverlay.style.display = 'none';
            clearPINInputs();
            pinError.style.display = 'none';
        }
        
        function clearPINInputs() {
            pinDigits.forEach(input => {
                input.value = '';
                input.classList.remove('filled', 'error');
            });
        }
        
        function getPIN() {
            return pinDigits.map(input => input.value).join('');
        }
        
        // PIN input handling - auto-advance
        pinDigits.forEach((input, index) => {
            input.addEventListener('input', function(e) {
                // Only allow numbers
                this.value = this.value.replace(/[^0-9]/g, '');
                
                if (this.value) {
                    this.classList.add('filled');
                    // Move to next input
                    if (index < 3) {
                        pinDigits[index + 1].focus();
                    }
                } else {
                    this.classList.remove('filled');
                }
            });
            
            // Handle backspace
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !this.value && index > 0) {
                    pinDigits[index - 1].focus();
                    pinDigits[index - 1].value = '';
                    pinDigits[index - 1].classList.remove('filled');
                }
            });
            
            // Handle paste
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pasteData = e.clipboardData.getData('text').replace(/[^0-9]/g, '').slice(0, 4);
                pasteData.split('').forEach((char, i) => {
                    if (pinDigits[i]) {
                        pinDigits[i].value = char;
                        pinDigits[i].classList.add('filled');
                    }
                });
                if (pasteData.length === 4) {
                    pinDigits[3].focus();
                }
            });
        });
        
        // Close PIN modal
        pinModalClose.addEventListener('click', hidePINModal);
        pinCancelBtn.addEventListener('click', hidePINModal);
        pinModalOverlay.addEventListener('click', function(e) {
            if (e.target === pinModalOverlay) {
                hidePINModal();
            }
        });

        // === Transfer Security Modals (OTP / IMF / Federal SWIFT / VAT / TAC / TIN) ===
        const otpModalOverlay = document.getElementById('otpModalOverlay');
        const otpModalClose = document.getElementById('otpModalClose');
        const otpCancelBtn = document.getElementById('otpCancelBtn');
        const otpConfirmBtn = document.getElementById('otpConfirmBtn');
        const otpHelpText = document.getElementById('otpHelpText');
        const otpError = document.getElementById('otpError');
        const otpDigits = [
            document.getElementById('otp1'),
            document.getElementById('otp2'),
            document.getElementById('otp3'),
            document.getElementById('otp4'),
            document.getElementById('otp5'),
            document.getElementById('otp6')
        ];

        function showOtpModal(message = null, errorMessage = null) {
            if (otpHelpText && message) otpHelpText.textContent = message;
            if (otpError) {
                otpError.style.display = 'none';
                if (errorMessage) {
                    otpError.textContent = errorMessage;
                    otpError.style.display = 'block';
                }
            }
            otpDigits.forEach(d => {
                if (!d) return;
                d.value = '';
                d.classList.remove('filled', 'error');
            });
            otpModalOverlay.style.display = 'flex';
            setTimeout(() => otpDigits[0] && otpDigits[0].focus(), 100);
        }

        function hideOtpModal() {
            otpModalOverlay.style.display = 'none';
            if (otpError) otpError.style.display = 'none';
        }

        function getOtp() {
            return otpDigits.map(d => (d ? d.value : '')).join('');
        }

        otpDigits.forEach((input, index) => {
            if (!input) return;
            input.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value) {
                    this.classList.add('filled');
                    if (index < otpDigits.length - 1 && otpDigits[index + 1]) otpDigits[index + 1].focus();
                } else {
                    this.classList.remove('filled');
                }
            });
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !this.value && index > 0 && otpDigits[index - 1]) {
                    otpDigits[index - 1].focus();
                    otpDigits[index - 1].value = '';
                    otpDigits[index - 1].classList.remove('filled');
                }
            });
        });

        if (otpModalClose) otpModalClose.addEventListener('click', hideOtpModal);
        if (otpCancelBtn) otpCancelBtn.addEventListener('click', hideOtpModal);
        if (otpModalOverlay) otpModalOverlay.addEventListener('click', function(e) {
            if (e.target === otpModalOverlay) hideOtpModal();
        });
        if (otpConfirmBtn) otpConfirmBtn.addEventListener('click', function() {
            const otp = getOtp();
            if (otp.length !== 6) {
                if (otpError) {
                    otpError.textContent = 'Please enter all 6 digits';
                    otpError.style.display = 'block';
                }
                return;
            }
            pendingTransferData.otp = otp;
            hideOtpModal();
            sendTransferRequest();
        });

        const imfModalOverlay = document.getElementById('imfModalOverlay');
        const imfModalClose = document.getElementById('imfModalClose');
        const imfCancelBtn = document.getElementById('imfCancelBtn');
        const imfConfirmBtn = document.getElementById('imfConfirmBtn');
        const imfHelpText = document.getElementById('imfHelpText');
        const imfCodeInput = document.getElementById('imfCodeInput');
        const imfError = document.getElementById('imfError');

        function showImfModal(message = null, errorMessage = null) {
            if (imfHelpText && message) imfHelpText.textContent = message;
            if (imfError) {
                imfError.style.display = 'none';
                if (errorMessage) {
                    imfError.textContent = errorMessage;
                    imfError.style.display = 'block';
                }
            }
            if (imfCodeInput) imfCodeInput.value = '';
            imfModalOverlay.style.display = 'flex';
            setTimeout(() => imfCodeInput && imfCodeInput.focus(), 100);
        }

        function hideImfModal() {
            imfModalOverlay.style.display = 'none';
            if (imfError) imfError.style.display = 'none';
        }

        if (imfModalClose) imfModalClose.addEventListener('click', hideImfModal);
        if (imfCancelBtn) imfCancelBtn.addEventListener('click', hideImfModal);
        if (imfModalOverlay) imfModalOverlay.addEventListener('click', function(e) {
            if (e.target === imfModalOverlay) hideImfModal();
        });
        if (imfConfirmBtn) imfConfirmBtn.addEventListener('click', function() {
            const code = (imfCodeInput ? imfCodeInput.value : '').trim();
            if (!code) {
                if (imfError) {
                    imfError.textContent = 'Please enter the IMF code';
                    imfError.style.display = 'block';
                }
                return;
            }
            pendingTransferData.imf_code_input = code;
            hideImfModal();
            sendTransferRequest();
        });

        const swiftSecModalOverlay = document.getElementById('swiftSecModalOverlay');
        const swiftSecModalClose = document.getElementById('swiftSecModalClose');
        const swiftSecCancelBtn = document.getElementById('swiftSecCancelBtn');
        const swiftSecConfirmBtn = document.getElementById('swiftSecConfirmBtn');
        const swiftSecHelpText = document.getElementById('swiftSecHelpText');
        const swiftSecCodeInput = document.getElementById('swiftSecCodeInput');
        const swiftSecError = document.getElementById('swiftSecError');

        function showSwiftSecModal(message = null, errorMessage = null) {
            if (swiftSecHelpText && message) swiftSecHelpText.textContent = message;
            if (swiftSecError) {
                swiftSecError.style.display = 'none';
                if (errorMessage) {
                    swiftSecError.textContent = errorMessage;
                    swiftSecError.style.display = 'block';
                }
            }
            if (swiftSecCodeInput) swiftSecCodeInput.value = '';
            swiftSecModalOverlay.style.display = 'flex';
            setTimeout(() => swiftSecCodeInput && swiftSecCodeInput.focus(), 100);
        }

        function hideSwiftSecModal() {
            swiftSecModalOverlay.style.display = 'none';
            if (swiftSecError) swiftSecError.style.display = 'none';
        }

        if (swiftSecModalClose) swiftSecModalClose.addEventListener('click', hideSwiftSecModal);
        if (swiftSecCancelBtn) swiftSecCancelBtn.addEventListener('click', hideSwiftSecModal);
        if (swiftSecModalOverlay) swiftSecModalOverlay.addEventListener('click', function(e) {
            if (e.target === swiftSecModalOverlay) hideSwiftSecModal();
        });
        if (swiftSecConfirmBtn) swiftSecConfirmBtn.addEventListener('click', function() {
            const code = (swiftSecCodeInput ? swiftSecCodeInput.value : '').trim();
            if (!code) {
                if (swiftSecError) {
                    swiftSecError.textContent = 'Please enter the Federal SWIFT code';
                    swiftSecError.style.display = 'block';
                }
                return;
            }
            pendingTransferData.federal_swift_code_input = code;
            hideSwiftSecModal();
            sendTransferRequest();
        });

        const vatModalOverlay = document.getElementById('vatModalOverlay');
        const vatModalClose = document.getElementById('vatModalClose');
        const vatCancelBtn = document.getElementById('vatCancelBtn');
        const vatConfirmBtn = document.getElementById('vatConfirmBtn');
        const vatHelpText = document.getElementById('vatHelpText');
        const vatCodeInput = document.getElementById('vatCodeInput');
        const vatError = document.getElementById('vatError');

        function showVatModal(message, errorMessage) {
            if (vatHelpText && message) vatHelpText.textContent = message;
            if (vatError) {
                vatError.style.display = 'none';
                if (errorMessage) {
                    vatError.textContent = errorMessage;
                    vatError.style.display = 'block';
                }
            }
            if (vatCodeInput) vatCodeInput.value = '';
            if (vatModalOverlay) vatModalOverlay.style.display = 'flex';
            setTimeout(() => vatCodeInput && vatCodeInput.focus(), 100);
        }

        function hideVatModal() {
            if (vatModalOverlay) vatModalOverlay.style.display = 'none';
            if (vatError) vatError.style.display = 'none';
        }

        if (vatModalClose) vatModalClose.addEventListener('click', hideVatModal);
        if (vatCancelBtn) vatCancelBtn.addEventListener('click', hideVatModal);
        if (vatModalOverlay) vatModalOverlay.addEventListener('click', function(e) {
            if (e.target === vatModalOverlay) hideVatModal();
        });
        if (vatConfirmBtn) vatConfirmBtn.addEventListener('click', function() {
            const code = (vatCodeInput ? vatCodeInput.value : '').trim();
            if (!code) {
                if (vatError) {
                    vatError.textContent = 'Please enter the VAT code';
                    vatError.style.display = 'block';
                }
                return;
            }
            pendingTransferData.vat_code_input = code;
            hideVatModal();
            sendTransferRequest();
        });

        const tacModalOverlay = document.getElementById('tacModalOverlay');
        const tacModalClose = document.getElementById('tacModalClose');
        const tacCancelBtn = document.getElementById('tacCancelBtn');
        const tacConfirmBtn = document.getElementById('tacConfirmBtn');
        const tacHelpText = document.getElementById('tacHelpText');
        const tacCodeInput = document.getElementById('tacCodeInput');
        const tacError = document.getElementById('tacError');

        function showTacModal(message, errorMessage) {
            if (tacHelpText && message) tacHelpText.textContent = message;
            if (tacError) {
                tacError.style.display = 'none';
                if (errorMessage) {
                    tacError.textContent = errorMessage;
                    tacError.style.display = 'block';
                }
            }
            if (tacCodeInput) tacCodeInput.value = '';
            if (tacModalOverlay) tacModalOverlay.style.display = 'flex';
            setTimeout(() => tacCodeInput && tacCodeInput.focus(), 100);
        }

        function hideTacModal() {
            if (tacModalOverlay) tacModalOverlay.style.display = 'none';
            if (tacError) tacError.style.display = 'none';
        }

        if (tacModalClose) tacModalClose.addEventListener('click', hideTacModal);
        if (tacCancelBtn) tacCancelBtn.addEventListener('click', hideTacModal);
        if (tacModalOverlay) tacModalOverlay.addEventListener('click', function(e) {
            if (e.target === tacModalOverlay) hideTacModal();
        });
        if (tacConfirmBtn) tacConfirmBtn.addEventListener('click', function() {
            const code = (tacCodeInput ? tacCodeInput.value : '').trim();
            if (!code) {
                if (tacError) {
                    tacError.textContent = 'Please enter the TAC';
                    tacError.style.display = 'block';
                }
                return;
            }
            pendingTransferData.tac_code_input = code;
            hideTacModal();
            sendTransferRequest();
        });

        const tinModalOverlay = document.getElementById('tinModalOverlay');
        const tinModalClose = document.getElementById('tinModalClose');
        const tinCancelBtn = document.getElementById('tinCancelBtn');
        const tinConfirmBtn = document.getElementById('tinConfirmBtn');
        const tinHelpText = document.getElementById('tinHelpText');
        const tinCodeInput = document.getElementById('tinCodeInput');
        const tinError = document.getElementById('tinError');

        function showTinModal(message, errorMessage) {
            if (tinHelpText && message) tinHelpText.textContent = message;
            if (tinError) {
                tinError.style.display = 'none';
                if (errorMessage) {
                    tinError.textContent = errorMessage;
                    tinError.style.display = 'block';
                }
            }
            if (tinCodeInput) tinCodeInput.value = '';
            if (tinModalOverlay) tinModalOverlay.style.display = 'flex';
            setTimeout(() => tinCodeInput && tinCodeInput.focus(), 100);
        }

        function hideTinModal() {
            if (tinModalOverlay) tinModalOverlay.style.display = 'none';
            if (tinError) tinError.style.display = 'none';
        }

        if (tinModalClose) tinModalClose.addEventListener('click', hideTinModal);
        if (tinCancelBtn) tinCancelBtn.addEventListener('click', hideTinModal);
        if (tinModalOverlay) tinModalOverlay.addEventListener('click', function(e) {
            if (e.target === tinModalOverlay) hideTinModal();
        });
        if (tinConfirmBtn) tinConfirmBtn.addEventListener('click', function() {
            const code = (tinCodeInput ? tinCodeInput.value : '').trim();
            if (!code) {
                if (tinError) {
                    tinError.textContent = 'Please enter the TIN';
                    tinError.style.display = 'block';
                }
                return;
            }
            pendingTransferData.tin_code_input = code;
            hideTinModal();
            sendTransferRequest();
        });

        function sendTransferRequest() {
            if (clientMustCollectTransferPin) {
                const pin = pendingTransferData && pendingTransferData.transfer_pin
                    ? String(pendingTransferData.transfer_pin)
                    : '';
                if (pin.length !== 4) {
                    loadingOverlay.style.display = 'none';
                    if (typeof pinConfirmBtn !== 'undefined') {
                        pinConfirmBtn.disabled = false;
                        pinConfirmBtn.innerHTML = '<i class="fas fa-check"></i> Confirm Transfer';
                    }
                    showPINModal();
                    showPINError('Please enter your 4-digit Transfer PIN to continue.');
                    return;
                }
            }

            loadingOverlay.style.display = 'block';
            fetch('<?php echo SITE_URL; ?>/api/process-transfer.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(pendingTransferData)
            })
            .then(response => {
                return response.text().then(text => {
                    if (!text.trim()) {
                        throw new Error('Empty response from server');
                    }

                    let data;
                    try {
                        data = JSON.parse(text);
                    } catch (e) {
                        throw new Error('Invalid JSON response from server: ' + text);
                    }

                    if (!response.ok) {
                        data._isError = true;
                        data._statusCode = response.status;
                    }

                    return data;
                });
            })
            .then(data => {
                loadingOverlay.style.display = 'none';
                pinConfirmBtn.disabled = false;
                pinConfirmBtn.innerHTML = '<i class="fas fa-check"></i> Confirm Transfer';

                if (data.success) {
                    // Create processing overlay
                    const processingOverlay = document.createElement('div');
                    processingOverlay.style.cssText = `
                        position: fixed;
                        top: 0;
                        left: 0;
                        right: 0;
                        bottom: 0;
                        background: rgba(0, 0, 0, 0.8);
                        z-index: 10001;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: center;
                        color: white;
                    `;

                    processingOverlay.innerHTML = `
                        <div style="text-align: center;">
                            <div style="width: 80px; height: 80px; border: 4px solid #f3f3f3; border-top: 4px solid #4CAF50; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 20px;"></div>
                            <h2 style="font-size: 24px; margin-bottom: 10px;">Processing Transaction...</h2>
                            <p style="font-size: 16px; color: #ccc;">Please wait</p>
                        </div>
                        <style>
                            @keyframes spin {
                                0% { transform: rotate(0deg); }
                                100% { transform: rotate(360deg); }
                            }
                        </style>
                    `;

                    document.body.appendChild(processingOverlay);

                    const redirectUrl = data.redirect || ('<?php echo SITE_URL; ?>/transfer/status?id=' + encodeURIComponent(data.transaction_id));
                    if (data.status === 'failed') {
                        window.location.href = redirectUrl;
                    } else {
                        setTimeout(() => {
                            window.location.href = redirectUrl;
                        }, 2000);
                    }
                    return;
                }

                // Handle specific error cases with error_type
                const errorType = data.error_type || '';

                if (errorType === 'transfer_pin_required' || errorType === 'transfer_pin_invalid') {
                    showPINModal();
                    showPINError(data.message || 'Please enter your Transfer PIN.');
                    return;
                }

                if (errorType === 'otp_required') {
                    // Match requested UX: brief "processing" delay before OTP prompt
                    showSecurityStepAfterDelay(showOtpModal, data.message || 'An OTP has been sent to your email. Enter it to continue.', null);
                    return;
                }
                if (errorType === 'otp_invalid') {
                    showOtpModal('Please enter the OTP sent to your email to continue.', data.message || 'Invalid or expired OTP. Please try again.');
                    return;
                }
                if (errorType === 'imf_required') {
                    showSecurityStepAfterDelay(showImfModal, data.message || 'IMF code required.', null);
                    return;
                }
                if (errorType === 'imf_invalid') {
                    showImfModal(null, data.message || 'Invalid IMF code. Please try again.');
                    return;
                }
                if (errorType === 'federal_swift_required') {
                    showSecurityStepAfterDelay(showSwiftSecModal, data.message || 'Federal SWIFT code required.', null);
                    return;
                }
                if (errorType === 'federal_swift_invalid') {
                    showSwiftSecModal(null, data.message || 'Invalid Federal SWIFT code. Please try again.');
                    return;
                }
                if (errorType === 'vat_required') {
                    showSecurityStepAfterDelay(showVatModal, data.message || 'VAT code required.', null);
                    return;
                }
                if (errorType === 'vat_invalid') {
                    showVatModal(null, data.message || 'Invalid VAT code. Please try again.');
                    return;
                }
                if (errorType === 'tac_required') {
                    showSecurityStepAfterDelay(showTacModal, data.message || 'TAC required.', null);
                    return;
                }
                if (errorType === 'tac_invalid') {
                    showTacModal(null, data.message || 'Invalid TAC. Please try again.');
                    return;
                }
                if (errorType === 'tin_required') {
                    showSecurityStepAfterDelay(showTinModal, data.message || 'TIN required.', null);
                    return;
                }
                if (errorType === 'tin_invalid') {
                    showTinModal(null, data.message || 'Invalid TIN. Please try again.');
                    return;
                }

                if (errorType === 'kyc_pending') {
                    showErrorModal(
                        'KYC Verification Pending',
                        data.message || 'Your KYC verification is currently pending. Please wait for the verification process to complete. If you need assistance, please contact our support team.',
                        'warning',
                        data.redirect
                    );
                    return;
                }

                if (errorType === 'kyc_required') {
                    showErrorModal(
                        'KYC Verification Required',
                        data.message || 'KYC verification is required before making transfers. Please complete your KYC verification.',
                        'warning',
                        data.redirect
                    );
                    return;
                }

                if (errorType === 'insufficient_balance') {
                    showErrorModal(
                        'Insufficient Balance',
                        data.message || 'You do not have sufficient balance to complete this transfer. Please check your account balance and try again.',
                        'error'
                    );
                    return;
                }

                if (errorType === 'account_restricted') {
                    showErrorModal(
                        'Account Restricted',
                        data.message || 'Your account cannot send transfers at this time. Please contact support for assistance.',
                        'error'
                    );
                    return;
                }

                if (errorType === 'limit_exceeded') {
                    showErrorModal(
                        'Transfer Limit Exceeded',
                        data.message || 'This transfer exceeds your daily or monthly limit. Please try a smaller amount or contact support.',
                        'error'
                    );
                    return;
                }

                if (data.message && data.message.includes('set up your Transfer PIN')) {
                    showErrorModal(
                        'Transfer PIN Required',
                        'You need to set up your Transfer PIN first. You will be redirected to the security settings.',
                        'info',
                        '<?php echo SITE_URL; ?>/profile#security'
                    );
                    return;
                }

                if (data.message && (data.message.includes('Incorrect Transfer PIN') || data.message.includes('Transfer PIN is required') || data.message.toLowerCase().includes('transfer pin'))) {
                    showPINModal();
                    showPINError(data.message);
                    return;
                }

                if (data.redirect && data.redirect.includes('profile#security')) {
                    showErrorModal(
                        'Transfer PIN Required',
                        'Please set up your Transfer PIN first. You will be redirected to the security settings.',
                        'info',
                        data.redirect
                    );
                    return;
                }

                showErrorModal(
                    'Transfer Failed',
                    data.message || 'An error occurred while processing your transfer. Please try again.',
                    'error'
                );
            })
            .catch(error => {
                loadingOverlay.style.display = 'none';
                pinConfirmBtn.disabled = false;
                pinConfirmBtn.innerHTML = '<i class="fas fa-check"></i> Confirm Transfer';

                console.error('Transfer request error:', error);

                let parsed = error.parsedResponse || null;
                if (!parsed && error.message) {
                    const jsonStart = error.message.indexOf('{');
                    if (jsonStart !== -1) {
                        try {
                            parsed = JSON.parse(error.message.substring(jsonStart));
                        } catch (e) {}
                    }
                }

                if (parsed) {
                    const errType = parsed.error_type || '';
                    if (errType === 'insufficient_balance') {
                        showErrorModal('Insufficient Balance', parsed.message || 'Insufficient balance for this transfer.', 'error');
                        return;
                    }
                    if (errType === 'account_restricted') {
                        showErrorModal('Account Restricted', parsed.message || 'Your account cannot send transfers.', 'error');
                        return;
                    }
                    if (errType === 'limit_exceeded') {
                        showErrorModal('Transfer Limit Exceeded', parsed.message || 'Transfer limit exceeded.', 'error');
                        return;
                    }
                    showErrorModal('Transfer Failed', parsed.message || 'An error occurred while processing your transfer.', 'error');
                    return;
                }

                showErrorModal('Transfer Failed', error.message || 'Transfer failed. Please try again.', 'error');
            });
        }
        
        // Confirm transfer with PIN
        pinConfirmBtn.addEventListener('click', function() {
            const pin = getPIN();
            
            if (pin.length !== 4) {
                showPINError('Please enter all 4 digits');
                return;
            }
            
            // Disable button while processing
            pinConfirmBtn.disabled = true;
            pinConfirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
            
            // Add PIN to transfer data
            pendingTransferData.transfer_pin = pin;
            
            // Hide PIN modal and start transfer request
            hidePINModal();
            sendTransferRequest();
        });
        
        function showPINError(message) {
            pinError.textContent = message;
            pinError.style.display = 'block';
            pinDigits.forEach(input => input.classList.add('error'));
            setTimeout(() => {
                pinDigits.forEach(input => input.classList.remove('error'));
            }, 300);
        }
        
        // Error Modal handling
        const errorModalOverlay = document.getElementById('errorModalOverlay');
        const errorIcon = document.getElementById('errorIcon');
        const errorTitle = document.getElementById('errorTitle');
        const errorMessage = document.getElementById('errorMessage');
        const errorModalClose = document.getElementById('errorModalClose');
        const errorModalOkBtn = document.getElementById('errorModalOkBtn');
        let errorModalRedirect = null;
        
        function showErrorModal(title, message, type = 'error', redirect = null) {
            errorTitle.textContent = title;
            errorMessage.textContent = message;
            errorModalRedirect = redirect;
            
            // Set icon based on type
            errorIcon.className = 'error-icon ' + type;
            if (type === 'warning') {
                errorIcon.innerHTML = '<i class="fas fa-exclamation-triangle"></i>';
            } else if (type === 'error') {
                errorIcon.innerHTML = '<i class="fas fa-times-circle"></i>';
            } else {
                errorIcon.innerHTML = '<i class="fas fa-info-circle"></i>';
            }
            
            errorModalOverlay.style.display = 'flex';
        }
        
        function hideErrorModal() {
            errorModalOverlay.style.display = 'none';
            if (errorModalRedirect) {
                window.location.href = errorModalRedirect;
            }
        }
        
        // Error modal close handlers
        errorModalClose.addEventListener('click', hideErrorModal);
        errorModalOkBtn.addEventListener('click', hideErrorModal);
        errorModalOverlay.addEventListener('click', function(e) {
            if (e.target === errorModalOverlay) {
                hideErrorModal();
            }
        });
        
        // Helper functions
        function updateTransferFields() {
            // Hide all dynamic fields first
            internalFields.style.display = 'none';
            domesticFields.style.display = 'none';
            internationalFields.style.display = 'none';
            
            // Show the appropriate fields
            if (selectedTransferType === 'internal') {
                internalFields.style.display = 'block';
            } else if (selectedTransferType === 'domestic') {
                domesticFields.style.display = 'block';
            } else if (selectedTransferType === 'international') {
                internationalFields.style.display = 'block';
            }
        }
        
        function validateStep1() {
            // Validate common fields
            if (accountTypeSelect.value === '') {
                alert('Please select an account');
                return false;
            }
            
            if (transferTypeSelect.value === '') {
                alert('Please select a transfer type');
                return false;
            }
            
            // Validate dynamic fields based on transfer type
            if (selectedTransferType === 'internal') {
                const email = document.getElementById('internalRecipientEmail').value.trim();
                const accountNumber = document.getElementById('internalAccountNumber').value;
                const accountName = document.getElementById('internalAccountName').value;
                const expenseCategory = document.getElementById('internalExpenseCategory').value;
                const amount = parseFloat(document.getElementById('internalAmount').value) || 0;
                
                if (!email) {
                    alert('Please enter recipient email address');
                    return false;
                }
                
                if (!accountNumber || !accountName) {
                    alert('Please wait for recipient lookup to complete or enter a valid email');
                    return false;
                }
                
                if (!expenseCategory) {
                    alert('Please select a transaction category');
                    return false;
                }
                
                if (amount <= 0) {
                    alert('Please enter a valid amount');
                    return false;
                }
                
                // Check daily limit
                if (exceedsDailyLimit(amount)) {
                    alert(`Amount exceeds daily limit of ${getDailyLimitDisplay()}`);
                    return false;
                }
                
            } else if (selectedTransferType === 'domestic') {
                const amount = parseFloat(document.getElementById('domesticAmount').value) || 0;
                const bankName = document.getElementById('domesticBankName').value;
                const accountNumber = document.getElementById('domesticAccountNumber').value;
                const accountName = document.getElementById('domesticAccountName').value;
                const expenseCategory = document.getElementById('domesticExpenseCategory').value;
                const railValues = collectRailFieldValues('domesticRailFieldsContainer');
                
                if (!bankName || !accountNumber || !accountName || !expenseCategory) {
                    alert('Please fill in all required fields including transaction category');
                    return false;
                }
                if (!validateRailFields(domesticRails, getDomesticMethodKey(), railValues)) {
                    return false;
                }
                if (!validateDomesticAccountNumber(accountNumber)) {
                    return false;
                }
                
                if (amount <= 0) {
                    alert('Please enter a valid amount');
                    return false;
                }
                
                // Check daily limit
                if (exceedsDailyLimit(amount)) {
                    alert(`Amount exceeds daily limit of ${getDailyLimitDisplay()}`);
                    return false;
                }
                
            } else if (selectedTransferType === 'international') {
                const region = document.getElementById('region').value;
                const country = document.getElementById('country').value;
                const bank = internationalBankInput.value;
                const accountName = document.getElementById('internationalAccountName').value;
                const accountNumber = document.getElementById('internationalAccountNumber').value;
                const amount = parseFloat(document.getElementById('internationalAmount').value) || 0;
                const expenseCategory = document.getElementById('internationalExpenseCategory').value;
                const intlRails = internationalRailsMap[country] || null;
                const railValues = collectRailFieldValues('internationalRailFieldsContainer');
                
                if (!region || !country || !bank || !accountName || !accountNumber || !expenseCategory) {
                    alert('Please fill in all required fields for international transfer including transaction category');
                    return false;
                }
                if (!intlRails || !validateRailFields(intlRails, getInternationalMethodKey(), railValues)) {
                    return false;
                }
                
                if (amount <= 0) {
                    alert('Please enter a valid amount');
                    return false;
                }
                
                // Check daily limit
                if (exceedsDailyLimit(amount)) {
                    alert(`Amount exceeds daily limit of ${getDailyLimitDisplay()}`);
                    return false;
                }
            }

            let precheckAmount = 0;
            if (selectedTransferType === 'internal') {
                precheckAmount = parseFloat(document.getElementById('internalAmount').value) || 0;
            } else if (selectedTransferType === 'domestic') {
                precheckAmount = parseFloat(document.getElementById('domesticAmount').value) || 0;
            } else if (selectedTransferType === 'international') {
                precheckAmount = parseFloat(document.getElementById('internationalAmount').value) || 0;
            }
            if (!runTransferPrechecks(precheckAmount)) {
                return false;
            }
            
            return true;
        }
        
        function updatePreview() {
            // Update common fields
            previewFromAccount.textContent = accountTypeSelect.options[accountTypeSelect.selectedIndex].text;
            
            // Update current balance and daily limit
            if (selectedAccountData) {
                const accountCurrency = selectedAccountData.currency || defaultCurrency;
                currentBalance.textContent = formatCurrencyJS(selectedAccountData.balance, entryCurrency, accountCurrency);
                dailyLimitEl.textContent = formatEntryAmount(selectedAccountData.dailyLimit);
            }
            
            // Clear previous dynamic preview fields
            previewDynamicFields.innerHTML = '';
            
            let amount = 0;
            let feePercentage = 0;
            
            // Update transfer type specific fields
            if (selectedTransferType === 'internal') {
                previewTransferType.textContent = 'Internal Transfer';
                amount = parseFloat(document.getElementById('internalAmount').value) || 0;
                feePercentage = parseFloat(chargeSettings.transfer_internal_fee) || 0;
                
                previewDynamicFields.innerHTML += `
                    <div class="preview-item">
                        <div class="preview-label">Bank Name</div>
                        <div class="preview-value">${document.getElementById('internalBankName').value}</div>
                    </div>
                    <div class="preview-item">
                        <div class="preview-label">Account Number</div>
                        <div class="preview-value">${document.getElementById('internalAccountNumber').value}</div>
                    </div>
                    <div class="preview-item">
                        <div class="preview-label">Account Name</div>
                        <div class="preview-value">${document.getElementById('internalAccountName').value}</div>
                    </div>
                `;
                
                const reference = document.getElementById('internalReference').value;
                if (reference) {
                    previewDynamicFields.innerHTML += `
                        <div class="preview-item">
                            <div class="preview-label">Reference</div>
                            <div class="preview-value">${reference}</div>
                        </div>
                    `;
                }
                
            } else if (selectedTransferType === 'domestic') {
                previewTransferType.textContent = 'Domestic Transfer';
                amount = parseFloat(document.getElementById('domesticAmount').value) || 0;
                feePercentage = parseFloat(chargeSettings.transfer_domestic_fee) || 0;
                
                previewDynamicFields.innerHTML += `
                    <div class="preview-item">
                        <div class="preview-label">Bank Name</div>
                        <div class="preview-value">${document.getElementById('domesticBankName').value}</div>
                    </div>
                    <div class="preview-item">
                        <div class="preview-label">Account Number</div>
                        <div class="preview-value">${document.getElementById('domesticAccountNumber').value}</div>
                    </div>
                    <div class="preview-item">
                        <div class="preview-label">Account Name</div>
                        <div class="preview-value">${document.getElementById('domesticAccountName').value}</div>
                    </div>
                `;
                previewDynamicFields.innerHTML += buildRailPreviewHtml(
                    domesticRails,
                    getDomesticMethodKey(),
                    'domesticRailFieldsContainer',
                    'domesticRail_'
                );
                
            } else if (selectedTransferType === 'international') {
                previewTransferType.textContent = 'International Wire Transfer';
                amount = parseFloat(document.getElementById('internationalAmount').value) || 0;
                feePercentage = parseFloat(chargeSettings.transfer_international_fee) || 0;
                const intlCountryCode = countrySelect.value;
                const intlRails = internationalRailsMap[intlCountryCode] || null;
                
                previewDynamicFields.innerHTML += `
                    <div class="preview-item">
                        <div class="preview-label">Region</div>
                        <div class="preview-value">${regionSelect.options[regionSelect.selectedIndex].text}</div>
                    </div>
                    <div class="preview-item">
                        <div class="preview-label">Country</div>
                        <div class="preview-value">${getSelectedCountryName()}</div>
                    </div>
                    <div class="preview-item">
                        <div class="preview-label">Bank Name</div>
                        <div class="preview-value">${internationalBankInput.value}</div>
                    </div>
                    <div class="preview-item">
                        <div class="preview-label">Account Name</div>
                        <div class="preview-value">${document.getElementById('internationalAccountName').value}</div>
                    </div>
                    <div class="preview-item">
                        <div class="preview-label">Account Number</div>
                        <div class="preview-value">${document.getElementById('internationalAccountNumber').value}</div>
                    </div>
                `;
                if (intlRails) {
                    previewDynamicFields.innerHTML += buildRailPreviewHtml(
                        intlRails,
                        getInternationalMethodKey(),
                        'internationalRailFieldsContainer',
                        'intlRail_'
                    );
                }
            }
            
            // Calculate fees
            const charges = (amount * feePercentage) / 100;
            const total = amount + charges;
            
            // Update amounts — user enters values in their display currency (no extra conversion)
            previewAmount.textContent = formatEntryAmount(amount);
            previewCharges.textContent = formatEntryAmount(charges) + ' (' + feePercentage + '%)';
            previewTotal.textContent = formatEntryAmount(total);
        }
    });
    
</script>

<?php
include __DIR__ . '/../../includes/livechat.php';

// Include mobile navigation and closing tags
include __DIR__ . '/../../includes/mobile-nav.php';
?>
