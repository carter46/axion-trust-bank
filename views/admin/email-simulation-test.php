<?php
// This file is included as a sub-page, so we don't need full HTML structure
// Check if we're being included or accessed directly
if (!defined('EMAIL_SUBPAGE') || EMAIL_SUBPAGE !== true) {
    $pageTitle = 'Simulation Flash Test - Admin';
    require_once __DIR__ . '/../../config/config.php';
    require_once __DIR__ . '/../../includes/functions.php';
    
    requireAdmin();
    
    include __DIR__ . '/../../includes/head.php';
    include __DIR__ . '/../../includes/admin-sidebar.php';
    define('EMAIL_SUBPAGE', true);
}

// Get SMTP configuration
$systemSettings = SystemSettings::getInstance();

// Get alert captions and templates for simulation
$db = Database::getInstance();
$alertCaptions = [];
$simulationTemplates = [];

try {
    $captionsSql = "SELECT * FROM email_simulation_alert_captions WHERE is_active = 1 ORDER BY caption_text ASC";
    $captionsStmt = $db->query($captionsSql);
    $alertCaptions = $captionsStmt ? $captionsStmt->fetchAll() : [];
} catch (Exception $e) {
    error_log('Error loading alert captions: ' . $e->getMessage());
    $alertCaptions = [];
}

try {
    $templatesSql = "SELECT * FROM email_simulation_templates WHERE is_active = 1 ORDER BY template_name ASC";
    $templatesStmt = $db->query($templatesSql);
    $simulationTemplates = $templatesStmt ? $templatesStmt->fetchAll() : [];
} catch (Exception $e) {
    error_log('Error loading templates: ' . $e->getMessage());
    $simulationTemplates = [];
}

// Get supported currencies
require_once __DIR__ . '/../../includes/currency.php';
$currencyHelper = new Currency();
$supportedCurrencies = $currencyHelper->getSupportedCurrencies();
$defaultCurrency = $systemSettings->get('default_currency', 'USD');
?>

<style>
    .email-simulation-test-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .page-header {
        margin-bottom: 30px;
    }
    
    .page-header h1 {
        font-size: 32px;
        font-weight: 700;
        color: #032B44;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .page-header p {
        color: #666;
        font-size: 15px;
        margin: 0;
    }
    
    .test-card {
        background: white;
        border-radius: 16px;
        padding: 32px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        margin-bottom: 30px;
    }
    
    .template-info-box {
        border-left: 4px solid #1e3a8a;
        background: #f8f9fa;
        padding: 16px 20px;
        border-radius: 12px;
        margin-bottom: 24px;
    }
    
    .template-type-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        margin-top: 10px;
    }
    
    .badge-simple {
        background: #e0f2fe;
        color: #0369a1;
    }
    
    .badge-advanced {
        background: #ede9fe;
        color: #5b21b6;
    }
    
    .form-divider {
        font-weight: 600;
        color: #032B44;
        margin: 32px 0 12px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .form-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #e5e7eb;
    }
    
    .card-title {
        font-size: 24px;
        font-weight: 600;
        color: #032B44;
        margin: 0 0 20px 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .card-title i {
        color: #1e3a8a;
    }
    
    .form-group {
        margin-bottom: 24px;
    }
    
    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #202124;
        margin-bottom: 8px;
    }
    
    .form-input,
    .form-select,
    .form-textarea {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #dadce0;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s;
        font-family: inherit;
    }
    
    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        outline: none;
        border-color: #1e3a8a;
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
    }
    
    .form-textarea {
        resize: vertical;
        min-height: 100px;
    }
    
    .help-text {
        font-size: 13px;
        color: #666;
        margin-top: 6px;
    }
    
    .help-text a {
        color: #1e3a8a;
        text-decoration: none;
    }
    
    .help-text a:hover {
        text-decoration: underline;
    }
    
    .searchable-select {
        position: relative;
    }
    
    .select2-container {
        width: 100% !important;
    }
    
    .select2-container--default .select2-selection--single {
        height: 44px;
        border: 1px solid #dadce0;
        border-radius: 8px;
        padding: 8px 12px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px;
        padding-left: 0;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 42px;
        right: 10px;
    }
    
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #1e3a8a;
    }
    
    .select2-dropdown {
        border: 1px solid #dadce0;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .select2-search--dropdown .select2-search__field {
        border: 1px solid #dadce0;
        border-radius: 6px;
        padding: 8px 12px;
    }
    
    .select2-results__option {
        padding: 10px 12px;
    }
    
    .select2-results__option--highlighted {
        background-color: #1e3a8a;
    }
    
    .select2-results__group {
        padding: 8px 12px;
        font-weight: 600;
        color: #666;
        background-color: #f8f9fa;
    }
    
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 14px 28px;
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(30, 58, 138, 0.3);
    }
    
    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    }
    
    .spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-top-color: white;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    #messageContainer {
        margin-top: 20px;
    }
    
    .alert {
        padding: 16px 20px;
        border-radius: 8px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 20px;
    }
    
    .alert-success {
        background-color: #f0fdf4;
        border-left: 4px solid #10b981;
        color: #065f46;
    }
    
    .alert-error {
        background-color: #fef2f2;
        border-left: 4px solid #ef4444;
        color: #991b1b;
    }
    
    .status-icon {
        font-size: 20px;
        flex-shrink: 0;
    }
    
    .form-divider {
        font-size: 16px;
        font-weight: 600;
        color: #032B44;
        margin: 24px 0 16px 0;
        padding-bottom: 8px;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: #f3f4f6;
        color: #374151;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s;
        margin-bottom: 20px;
    }
    
    .back-button:hover {
        background: #e5e7eb;
        transform: translateX(-4px);
    }
    
    @media (max-width: 768px) {
        .email-simulation-test-container {
            padding: 15px;
        }
        
        .test-card {
            padding: 20px;
        }
        
        .page-header h1 {
            font-size: 24px;
        }
    }
</style>

<div class="email-simulation-test-container">
    <a href="<?php echo SITE_URL; ?>/admin/email" class="back-button">
        <i class="fas fa-arrow-left"></i> Back to Email Management
    </a>
    
    <div class="page-header">
        <h1>
            <i class="fas fa-bolt"></i>
            Simulation Flash Test
        </h1>
        <p>Test financial transaction emails with customizable scenarios to verify SMTP configuration and deliverability</p>
    </div>
    
    <div id="messageContainer"></div>
    
    <form id="simulationEmailForm">
        <!-- Card 1: Template and Recipient/Sender Information -->
        <div class="test-card">
            <div class="form-group">
                <label class="form-label" for="sim_template">Email Template *</label>
                <select class="form-select" id="sim_template" name="template_id" required>
                    <option value="">Select a template...</option>
                    <?php foreach ($simulationTemplates as $template): ?>
                        <option value="<?php echo $template['id']; ?>"
                                data-template-name="<?php echo htmlspecialchars($template['template_name']); ?>"
                                data-template-type="<?php echo htmlspecialchars($template['template_type'] ?? 'simple'); ?>">
                            <?php echo htmlspecialchars($template['template_name']); ?> (<?php echo ucfirst($template['template_type'] ?? 'simple'); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="help-text">
                    <i class="fas fa-info-circle"></i>
                    Choose email template with custom colors and logo 
                    <a href="<?php echo SITE_URL; ?>/admin/email/simulation-settings" target="_blank">(manage in Simulation Settings)</a>
                </p>
            </div>
            
            <div class="template-info-box" id="templateInfoBox" style="display: none;">
                <p id="templateInfoText" style="margin: 0; font-weight: 600; color: #032B44;"></p>
                <span class="template-type-badge badge-simple" id="templateTypeBadge">Simple Template</span>
                <p class="help-text" style="margin-top: 8px;">
                    Template name will be used as the bank name in the simulation email.
                </p>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="sim_recipient_name">Recipient Name *</label>
                <input type="text" class="form-input" id="sim_recipient_name" name="recipient_name" 
                       placeholder="John Doe" value="<?php echo htmlspecialchars($_SESSION['full_name'] ?? ''); ?>" required>
                <p class="help-text">
                    <i class="fas fa-info-circle"></i>
                    Enter the recipient's full name
                </p>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="sim_recipient_email">Recipient Email Address *</label>
                <input type="email" class="form-input" id="sim_recipient_email" name="recipient_email" 
                       placeholder="user@example.com" value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>" required>
                <p class="help-text">
                    <i class="fas fa-info-circle"></i>
                    Enter the email address where you want to receive the test notification
                </p>
            </div>
            
            <div class="form-group" id="accountNumberGroup" style="display: none;">
                <label class="form-label" for="sim_account_number">Account Number *</label>
                <input type="text" class="form-input" id="sim_account_number" name="account_number" 
                       placeholder="1234567890">
                <p class="help-text">
                    <i class="fas fa-info-circle"></i>
                    Required for advanced templates. Used inside the transaction details table.
                </p>
            </div>
            
            <div class="form-group" id="swiftCodeGroup" style="display: none;">
                <label class="form-label" for="sim_swift_code">SWIFT Code *</label>
                <input type="text" class="form-input" id="sim_swift_code" name="swift_code" 
                       placeholder="ABCDUS33" maxlength="11">
                <p class="help-text">
                    <i class="fas fa-info-circle"></i>
                    Required for advanced templates (8-11 characters).
                </p>
            </div>
            
            <div class="form-group" id="recipientBankGroup" style="display: none;">
                <label class="form-label" for="sim_recipient_bank">Recipient Bank Name *</label>
                <input type="text" class="form-input" id="sim_recipient_bank" name="recipient_bank" 
                       placeholder="Bank Name">
                <p class="help-text">
                    <i class="fas fa-info-circle"></i>
                    Required for advanced templates. Bank name of the recipient.
                </p>
            </div>
            
            <div id="simpleFields" style="display: none;">
                <div class="form-divider" style="margin: 30px 0 20px 0;">
                    Sender Information
                </div>
                <div class="form-group" id="senderGroup">
                    <label class="form-label" for="sim_sender">Sender (Payment From) *</label>
                    <input type="text" class="form-input" id="sim_sender" name="sender" 
                           placeholder="John Smith or Company Name">
                    <p class="help-text">
                        <i class="fas fa-info-circle"></i>
                        Enter the name or email of who sent the payment. This will appear in the "From:" field of the email.
                    </p>
                </div>
            </div>
            
            <div id="advancedFields" style="display: none;">
                <div class="form-divider" style="margin: 30px 0 20px 0;">
                    Sender Information
                </div>
                <p class="help-text" style="margin-top: 0; margin-bottom: 10px;">
                    <i class="fas fa-info-circle"></i>
                    The following sender information will appear on the receipt PDF but NOT in the email.
                </p>
                
                <div class="form-group" id="senderNameGroup">
                    <label class="form-label" for="sim_sender_name">Sender Name</label>
                    <input type="text" class="form-input" id="sim_sender_name" name="sender_name" 
                           placeholder="John Doe">
                    <p class="help-text">
                        <i class="fas fa-info-circle"></i>
                        Name of the person/entity sending the payment
                    </p>
                </div>
                
                <div class="form-group" id="senderAccountGroup">
                    <label class="form-label" for="sim_sender_account">Sender Account Number</label>
                    <input type="text" class="form-input" id="sim_sender_account" name="sender_account" 
                           placeholder="9876543210">
                    <p class="help-text">
                        <i class="fas fa-info-circle"></i>
                        Account number of the sender
                    </p>
                </div>
                
                <div class="form-group" id="senderBankGroup">
                    <label class="form-label" for="sim_sender_bank">Sender Bank Name</label>
                    <input type="text" class="form-input" id="sim_sender_bank" name="sender_bank" 
                           placeholder="Sender Bank Name">
                    <p class="help-text">
                        <i class="fas fa-info-circle"></i>
                        Bank name of the sender
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Card 2: Footer Contact and Transaction Details -->
        <div class="test-card" style="margin-top: 20px;">
            <div class="form-group">
                <label class="form-label" for="sim_footer_contact_method">Footer Contact Method *</label>
                <select class="form-select" id="sim_footer_contact_method" name="footer_contact_method" required>
                    <option value="email" selected>Email</option>
                    <option value="whatsapp">WhatsApp</option>
                </select>
                <p class="help-text">
                    <i class="fas fa-info-circle"></i>
                    Select the contact method to display in the email footer
                </p>
            </div>
            
            <div class="form-group" id="footer_email_group">
                <label class="form-label" for="sim_footer_email">Footer Email Address *</label>
                <input type="email" class="form-input" id="sim_footer_email" name="footer_email" 
                       placeholder="support@bank.com" value="<?php echo htmlspecialchars($systemSettings->get('site_email', 'support@bank.com')); ?>" required>
                <p class="help-text">
                    <i class="fas fa-info-circle"></i>
                    This email will appear in the email footer contact section
                </p>
            </div>
            
            <div class="form-group" id="footer_whatsapp_group" style="display: none;">
                <label class="form-label" for="sim_footer_whatsapp">Footer WhatsApp Number *</label>
                <input type="tel" class="form-input" id="sim_footer_whatsapp" name="footer_whatsapp" 
                       placeholder="+1234567890" pattern="^\+[1-9]\d{1,14}$">
                <p class="help-text">
                    <i class="fas fa-info-circle"></i>
                    This WhatsApp number will appear in the email footer contact section (international format: +1234567890)
                </p>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="sim_amount">Transaction Amount *</label>
                <input type="number" class="form-input" id="sim_amount" name="amount" 
                       placeholder="0.00" min="0.01" step="0.01" required>
                <p class="help-text">
                    <i class="fas fa-info-circle"></i>
                    Enter the transaction amount to display in the alert
                </p>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="sim_currency">Currency *</label>
                <select class="form-select searchable-select" id="sim_currency" name="currency" required>
                    <?php
                    // Popular currencies first
                    $popularCurrencies = ['USD', 'EUR', 'GBP', 'JPY', 'CNY', 'INR', 'CAD', 'AUD', 'CHF', 'SGD', 'HKD', 'NZD', 'SEK', 'NOK', 'DKK', 'ZAR', 'BRL', 'MXN', 'KRW', 'THB'];
                    
                    // Get all currencies and limit to 70
                    $allCurrencies = $currencyHelper->getSupportedCurrencies();
                    $limitedCurrencies = array_slice($allCurrencies, 0, 70, true);
                    
                    // Separate popular and others
                    $popular = [];
                    $others = [];
                    
                    foreach ($limitedCurrencies as $code => $name) {
                        if (in_array($code, $popularCurrencies)) {
                            $popular[$code] = $name;
                        } else {
                            $others[$code] = $name;
                        }
                    }
                    
                    // Sort popular currencies by the order in $popularCurrencies array
                    $sortedPopular = [];
                    foreach ($popularCurrencies as $code) {
                        if (isset($popular[$code])) {
                            $sortedPopular[$code] = $popular[$code];
                        }
                    }
                    
                    // Output popular first
                    foreach ($sortedPopular as $code => $name): ?>
                        <option value="<?php echo htmlspecialchars($code); ?>" 
                                <?php echo ($code === $defaultCurrency) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($code); ?> - <?php echo htmlspecialchars($name); ?>
                        </option>
                    <?php endforeach; ?>
                    
                    <?php if (!empty($others)): ?>
                        <optgroup label="Other Currencies">
                            <?php foreach ($others as $code => $name): ?>
                                <option value="<?php echo htmlspecialchars($code); ?>" 
                                        <?php echo ($code === $defaultCurrency) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($code); ?> - <?php echo htmlspecialchars($name); ?>
                                </option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endif; ?>
                </select>
                <p class="help-text">
                    <i class="fas fa-info-circle"></i>
                    Select the currency for this transaction (type to search)
                </p>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="sim_status">Transaction Status *</label>
                <select class="form-select" id="sim_status" name="transaction_status" required>
                    <option value="successful" selected>Successful</option>
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="failed">Failed</option>
                </select>
                <p class="help-text">
                    <i class="fas fa-info-circle"></i>
                    Simulate different transaction states
                </p>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="sim_alert_caption">Alert Caption *</label>
                <select class="form-select" id="sim_alert_caption" name="alert_caption" required>
                    <option value="">Select an alert caption...</option>
                    <?php foreach ($alertCaptions as $caption): ?>
                        <option value="<?php echo htmlspecialchars($caption['caption_text']); ?>">
                            <?php echo htmlspecialchars($caption['caption_text']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="help-text">
                    <i class="fas fa-info-circle"></i>
                    Choose from pre-configured alert captions 
                    <a href="<?php echo SITE_URL; ?>/admin/email/simulation-settings" target="_blank">(manage in Simulation Settings)</a>
                </p>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="sim_description">Transaction Description *</label>
                <textarea class="form-textarea" id="sim_description" name="description" 
                          placeholder="Enter transaction description or details..." rows="4" required></textarea>
                <p class="help-text">
                    <i class="fas fa-info-circle"></i>
                    This description will appear in the email body
                </p>
            </div>
        </div>
        
        <!-- Send Button (Outside Cards) -->
        <div style="margin-top: 30px; text-align: center;">
            <button type="submit" class="btn btn-primary" id="sendSimulationButton">
                <i class="fas fa-paper-plane"></i>
                <span>Send Simulation Email</span>
            </button>
        </div>
    </form>
</div>

<script>
function showMessage(type, message) {
    const messageContainer = document.getElementById('messageContainer');
    const className = type === 'success' ? 'alert-success' : 'alert-error';
    const icon = type === 'success' ? 'check-circle' : 'exclamation-circle';
    
    messageContainer.innerHTML = `
        <div class="alert ${className}">
            <i class="fas fa-${icon} status-icon"></i>
            <div>${message}</div>
        </div>
    `;
    
    // Scroll to message
    messageContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
    
    // Auto-hide success messages after 10 seconds
    if (type === 'success') {
        setTimeout(() => {
            messageContainer.innerHTML = '';
        }, 10000);
    }
}

// Initialize Select2 for searchable currency dropdown
function initSelect2() {
    if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
        jQuery('#sim_currency').select2({
            placeholder: 'Select a currency...',
            allowClear: false,
            width: '100%',
            minimumResultsForSearch: 0
        });
    } else if (typeof $ !== 'undefined' && $.fn.select2) {
        $('#sim_currency').select2({
            placeholder: 'Select a currency...',
            allowClear: false,
            width: '100%',
            minimumResultsForSearch: 0
        });
    } else {
        // Load jQuery and Select2 from CDN
        const jqueryScript = document.createElement('script');
        jqueryScript.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
        jqueryScript.onload = function() {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css';
            document.head.appendChild(link);
            
            const select2Script = document.createElement('script');
            select2Script.src = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js';
            select2Script.onload = function() {
                jQuery('#sim_currency').select2({
                    placeholder: 'Select a currency...',
                    allowClear: false,
                    width: '100%',
                    minimumResultsForSearch: 0
                });
            };
            document.head.appendChild(select2Script);
        };
        document.head.appendChild(jqueryScript);
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSelect2);
} else {
    initSelect2();
}

// ===== SIMULATION FLASH TEST FORM =====
const simulationForm = document.getElementById('simulationEmailForm');
const sendSimulationButton = document.getElementById('sendSimulationButton');
const footerContactMethodSelect = document.getElementById('sim_footer_contact_method');
const footerEmailGroup = document.getElementById('footer_email_group');
const footerWhatsappGroup = document.getElementById('footer_whatsapp_group');
const footerEmailInput = document.getElementById('sim_footer_email');
const footerWhatsappInput = document.getElementById('sim_footer_whatsapp');
const templateSelect = document.getElementById('sim_template');
const templateInfoBox = document.getElementById('templateInfoBox');
const templateInfoText = document.getElementById('templateInfoText');
const templateTypeBadge = document.getElementById('templateTypeBadge');
const advancedFieldsSection = document.getElementById('advancedFields');
const simpleFieldsSection = document.getElementById('simpleFields');
const accountNumberInput = document.getElementById('sim_account_number');
const swiftCodeInput = document.getElementById('sim_swift_code');
const recipientBankInput = document.getElementById('sim_recipient_bank');
const senderInput = document.getElementById('sim_sender');
const senderNameInput = document.getElementById('sim_sender_name');
const senderAccountInput = document.getElementById('sim_sender_account');
const senderBankInput = document.getElementById('sim_sender_bank');
let currentTemplateType = null;

function updateTemplateState() {
    if (!templateSelect) return;
    
    const selectedOption = templateSelect.options[templateSelect.selectedIndex];
    if (selectedOption && selectedOption.value) {
        const templateName = selectedOption.dataset.templateName || selectedOption.textContent.trim();
        const templateType = selectedOption.dataset.templateType || 'simple';
        currentTemplateType = templateType;
        
        if (templateInfoBox && templateInfoText && templateTypeBadge) {
            templateInfoBox.style.display = 'block';
            templateInfoText.textContent = templateName;
            templateTypeBadge.textContent = templateType === 'advanced' ? 'Advanced Template' : 'Simple Template';
            templateTypeBadge.classList.remove('badge-simple', 'badge-advanced');
            templateTypeBadge.classList.add(templateType === 'advanced' ? 'badge-advanced' : 'badge-simple');
        }
        
        const requiresAdvanced = templateType === 'advanced';
        const isSimple = templateType === 'simple';
        
        // Show/hide Account Number, SWIFT Code, and Recipient Bank directly (they're now separate, not in a section)
        const accountNumberGroup = document.getElementById('accountNumberGroup');
        const swiftCodeGroup = document.getElementById('swiftCodeGroup');
        const recipientBankGroup = document.getElementById('recipientBankGroup');
        if (accountNumberGroup) {
            accountNumberGroup.style.display = requiresAdvanced ? 'block' : 'none';
        }
        if (swiftCodeGroup) {
            swiftCodeGroup.style.display = requiresAdvanced ? 'block' : 'none';
        }
        if (recipientBankGroup) {
            recipientBankGroup.style.display = requiresAdvanced ? 'block' : 'none';
        }
        
        if (advancedFieldsSection) {
            advancedFieldsSection.style.display = requiresAdvanced ? 'block' : 'none';
        }
        if (simpleFieldsSection) {
            simpleFieldsSection.style.display = isSimple ? 'block' : 'none';
        }
        if (accountNumberInput) {
            accountNumberInput.required = requiresAdvanced;
            if (!requiresAdvanced) {
                accountNumberInput.value = '';
            }
        }
        if (swiftCodeInput) {
            swiftCodeInput.required = requiresAdvanced;
            if (!requiresAdvanced) {
                swiftCodeInput.value = '';
            }
        }
        if (recipientBankInput) {
            recipientBankInput.required = requiresAdvanced;
            if (!requiresAdvanced) {
                recipientBankInput.value = '';
            }
        }
        if (senderInput) {
            senderInput.required = isSimple;
            if (!isSimple) {
                senderInput.value = '';
            }
        }
    } else {
        currentTemplateType = null;
        if (templateInfoBox) {
            templateInfoBox.style.display = 'none';
        }
        const accountNumberGroup = document.getElementById('accountNumberGroup');
        const swiftCodeGroup = document.getElementById('swiftCodeGroup');
        const recipientBankGroup = document.getElementById('recipientBankGroup');
        if (accountNumberGroup) {
            accountNumberGroup.style.display = 'none';
        }
        if (swiftCodeGroup) {
            swiftCodeGroup.style.display = 'none';
        }
        if (recipientBankGroup) {
            recipientBankGroup.style.display = 'none';
        }
        if (advancedFieldsSection) {
            advancedFieldsSection.style.display = 'none';
        }
        if (simpleFieldsSection) {
            simpleFieldsSection.style.display = 'none';
        }
        if (accountNumberInput) {
            accountNumberInput.required = false;
        }
        if (swiftCodeInput) {
            swiftCodeInput.required = false;
        }
        if (recipientBankInput) {
            recipientBankInput.required = false;
        }
        if (senderInput) {
            senderInput.required = false;
        }
    }
}

if (templateSelect) {
    templateSelect.addEventListener('change', updateTemplateState);
    updateTemplateState();
}

// Footer contact method toggle
footerContactMethodSelect.addEventListener('change', function() {
    if (this.value === 'email') {
        footerEmailGroup.style.display = 'block';
        footerWhatsappGroup.style.display = 'none';
        footerEmailInput.required = true;
        footerWhatsappInput.required = false;
        footerWhatsappInput.value = '';
    } else {
        footerEmailGroup.style.display = 'none';
        footerWhatsappGroup.style.display = 'block';
        footerEmailInput.required = false;
        footerWhatsappInput.required = true;
        footerEmailInput.value = '';
    }
});

// Simulation form submission
simulationForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validate recipient email
    const recipientEmail = document.getElementById('sim_recipient_email').value;
    if (!recipientEmail || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(recipientEmail)) {
        showMessage('error', '<strong>Validation Error!</strong><br>Please enter a valid recipient email address.');
        return;
    }
    
    // Validate footer contact
    const footerContactMethod = footerContactMethodSelect.value;
    const footerContactValue = footerContactMethod === 'email' ? footerEmailInput.value : footerWhatsappInput.value;
    
    if (!footerContactValue) {
        showMessage('error', '<strong>Validation Error!</strong><br>Please enter a footer contact value.');
        return;
    }
    
    if (footerContactMethod === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(footerContactValue)) {
        showMessage('error', '<strong>Validation Error!</strong><br>Please enter a valid footer email address.');
        return;
    }
    
    if (footerContactMethod === 'whatsapp' && !/^\+[1-9]\d{1,14}$/.test(footerContactValue)) {
        showMessage('error', '<strong>Validation Error!</strong><br>Please enter a valid WhatsApp number in international format (e.g., +1234567890).');
        return;
    }
    
    // Collect form data
    const formData = {
        recipient_name: document.getElementById('sim_recipient_name').value,
        recipient_email: recipientEmail,
        recipient_bank: recipientBankInput ? recipientBankInput.value : '',
        sender: senderInput ? senderInput.value : '',
        account_number: accountNumberInput ? accountNumberInput.value : '',
        swift_code: swiftCodeInput ? swiftCodeInput.value : '',
        sender_name: senderNameInput ? senderNameInput.value : '',
        sender_account: senderAccountInput ? senderAccountInput.value : '',
        sender_bank: senderBankInput ? senderBankInput.value : '',
        footer_contact_method: footerContactMethod,
        footer_contact_value: footerContactValue,
        amount: document.getElementById('sim_amount').value,
        currency: document.getElementById('sim_currency').value,
        transaction_status: document.getElementById('sim_status').value,
        alert_caption: document.getElementById('sim_alert_caption').value,
        description: document.getElementById('sim_description').value,
        template_id: templateSelect ? templateSelect.value : ''
    };
    
    const selectedOption = templateSelect ? templateSelect.options[templateSelect.selectedIndex] : null;
    const selectedTemplateType = currentTemplateType || (selectedOption ? (selectedOption.dataset.templateType || 'simple') : 'simple');
    const requiresAdvancedFields = selectedTemplateType === 'advanced';
    
    // Validate new fields
    if (!formData.recipient_name || formData.recipient_name.trim().length === 0) {
        showMessage('error', '<strong>Validation Error!</strong><br>Please enter recipient name.');
        return;
    }
    
    if (requiresAdvancedFields) {
        if (!formData.account_number || formData.account_number.trim().length === 0) {
            showMessage('error', '<strong>Validation Error!</strong><br>Please enter account number for advanced templates.');
            return;
        }
        
        if (!formData.swift_code || formData.swift_code.trim().length < 8 || formData.swift_code.trim().length > 11) {
            showMessage('error', '<strong>Validation Error!</strong><br>SWIFT code must be between 8 and 11 characters for advanced templates.');
            return;
        }
    } else {
        formData.account_number = '';
        formData.swift_code = '';
    }
    
    // Validate required fields
    if (!formData.amount || parseFloat(formData.amount) <= 0) {
        showMessage('error', '<strong>Validation Error!</strong><br>Please enter a valid amount greater than 0.');
        return;
    }
    
    if (!formData.alert_caption) {
        showMessage('error', '<strong>Validation Error!</strong><br>Please select an alert caption.');
        return;
    }
    
    if (!formData.template_id) {
        showMessage('error', '<strong>Validation Error!</strong><br>Please select a template.');
        return;
    }
    
    // Disable button and show loading
    sendSimulationButton.disabled = true;
    sendSimulationButton.innerHTML = '<div class="spinner"></div><span>Sending...</span>';
    
    // Send simulation email
    fetch('<?php echo SITE_URL; ?>/api/send-simulation-email.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message first
            showMessage('success', `<strong>Simulation Email Sent Successfully!</strong><br>Sent to ${recipientEmail}. ${data.transaction_ref ? 'Transaction Ref: ' + data.transaction_ref : ''}<br>Redirecting to receipt...`);
            
            // Redirect to receipt display page after a short delay
            if (data.receipt_url) {
                setTimeout(() => {
                    window.location.href = data.receipt_url + '&admin_view=1';
                }, 2000);
            }
        } else {
            showMessage('error', `<strong>Simulation Email Failed!</strong><br>${data.message || 'Please check your inputs and try again.'}`);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('error', '<strong>Error!</strong><br>Failed to send simulation email. Check console for details.');
    })
    .finally(() => {
        // Re-enable button
        sendSimulationButton.disabled = false;
        sendSimulationButton.innerHTML = '<i class="fas fa-paper-plane"></i><span>Send Simulation Email</span>';
    });
});
</script>

<?php
if (!defined('EMAIL_SUBPAGE') || EMAIL_SUBPAGE !== true) {
    echo '</div></div></div>';
    echo '</body></html>';
}
?>

