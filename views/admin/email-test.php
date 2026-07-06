<?php
// This file is included as a sub-page, so we don't need full HTML structure
// Check if we're being included or accessed directly
// EMAIL_SUBPAGE is set to true when included by controller (head/sidebar already included)
if (!defined('EMAIL_SUBPAGE') || EMAIL_SUBPAGE !== true) {
    // If accessed directly, include full structure for backward compatibility
    $pageTitle = 'Email Testing - Admin';
    require_once __DIR__ . '/../../config/config.php';
    require_once __DIR__ . '/../../includes/functions.php';
    
    requireAdmin();
    
    include __DIR__ . '/../../includes/head.php';
    include __DIR__ . '/../../includes/admin-sidebar.php';
}

// Get SMTP configuration
$systemSettings = SystemSettings::getInstance();
$siteName = $systemSettings->get('site_name', 'SecureBank Online');
$siteEmail = $systemSettings->get('site_email', 'support@securebank.com');

?>

<style>
    .email-test-container {
        max-width: 1200px;
        margin: 0 auto;
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
        font-size: 16px;
        margin: 0;
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
    
    .test-card {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        margin-bottom: 24px;
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
        font-size: 16px;
        margin: 0;
    }
    
    .card-title {
        font-size: 20px;
        color: #1e3a8a;
        font-weight: 600;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .config-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }
    
    .config-item {
        background: #f8f9fa;
        padding: 16px;
        border-radius: 8px;
        border-left: 4px solid #1e3a8a;
    }
    
    .config-label {
        font-size: 12px;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }
    
    .config-value {
        font-size: 15px;
        color: #202124;
        font-weight: 600;
        word-break: break-all;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-label {
        display: block;
        font-weight: 600;
        color: #202124;
        margin-bottom: 8px;
        font-size: 14px;
    }
    
    .form-input, .form-select {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #dadce0;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s;
    }
    
    .form-input:focus, .form-select:focus {
        outline: none;
        border-color: #1e3a8a;
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
    }
    
    .btn {
        padding: 14px 28px;
        border: none;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        color: white;
        width: 100%;
        justify-content: center;
    }
    
    .btn-primary:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(30, 58, 138, 0.3);
    }
    
    .btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    .alert {
        padding: 16px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        animation: slideDown 0.3s ease-out;
    }
    
    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border-left: 4px solid #10b981;
    }
    
    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border-left: 4px solid #ef4444;
    }
    
    .alert-info {
        background: #dbeafe;
        color: #1e40af;
        border-left: 4px solid #3b82f6;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .status-icon {
        font-size: 20px;
        flex-shrink: 0;
    }
    
    .spinner {
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top: 3px solid white;
        width: 18px;
        height: 18px;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .template-preview {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        border: 2px dashed #dadce0;
        margin-top: 16px;
        text-align: center;
        color: #666;
    }
    
    .help-text {
        font-size: 13px;
        color: #666;
        margin-top: 6px;
    }
    
    .form-textarea {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #dadce0;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s;
        font-family: inherit;
        resize: vertical;
        min-height: 100px;
    }
    
    .form-textarea:focus {
        outline: none;
        border-color: #1e3a8a;
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
    }
    
    .help-text a {
        color: #1e3a8a;
        text-decoration: none;
    }
    
    .help-text a:hover {
        text-decoration: underline;
    }
    
    @media (max-width: 768px) {
        .email-test-container {
            padding: 15px;
        }
        
        .config-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="email-test-container">
    <a href="<?php echo SITE_URL; ?>/admin/email" class="back-button">
        <i class="fas fa-arrow-left"></i> Back to Email Management
    </a>
    
    <div class="page-header">
        <h1>
            <i class="fas fa-vial"></i>
            Test Email
        </h1>
        <p>Test your SMTP configuration and send test emails</p>
    </div>
    
    <div id="messageContainer"></div>
    
    <!-- SMTP Configuration Display -->
    <div class="test-card">
        <h2 class="card-title">
            <i class="fas fa-server"></i>
            Current SMTP Configuration
        </h2>
        
        <div class="config-grid">
            <div class="config-item">
                <div class="config-label">SMTP Host</div>
                <div class="config-value"><?php echo defined('SMTP_HOST') ? SMTP_HOST : 'Not configured'; ?></div>
            </div>
            
            <div class="config-item">
                <div class="config-label">SMTP Port</div>
                <div class="config-value"><?php echo defined('SMTP_PORT') ? SMTP_PORT : 'Not configured'; ?></div>
            </div>
            
            <div class="config-item">
                <div class="config-label">SMTP User</div>
                <div class="config-value"><?php echo defined('SMTP_USER') ? SMTP_USER : 'Not configured'; ?></div>
            </div>
            
            <div class="config-item">
                <div class="config-label">From Email</div>
                <div class="config-value"><?php echo defined('SMTP_FROM') ? SMTP_FROM : 'Not configured'; ?></div>
            </div>
            
            <div class="config-item">
                <div class="config-label">From Name</div>
                <div class="config-value"><?php echo defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : 'Not configured'; ?></div>
            </div>
            
            <div class="config-item">
                <div class="config-label">Site Name</div>
                <div class="config-value"><?php echo htmlspecialchars($siteName); ?></div>
            </div>
        </div>
        
        <div class="alert alert-info">
            <i class="fas fa-info-circle status-icon"></i>
            <div>
                <strong>Configuration Location:</strong><br>
                SMTP settings are defined in <code>/config/config.php</code>. Update them there if you need to change your email provider settings.
            </div>
        </div>
    </div>
    
    <!-- Send Test Email -->
    <div class="test-card">
        <h2 class="card-title">
            <i class="fas fa-paper-plane"></i>
            Send Test Email
        </h2>
        
        <form id="testEmailForm">
            <div class="form-group">
                <label class="form-label" for="test_email">Recipient Email Address *</label>
                <input type="email" class="form-input" id="test_email" name="test_email" 
                       value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>" required>
                <p class="help-text">
                    <i class="fas fa-info-circle"></i>
                    Enter your email address to receive the test email
                </p>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="template_type">Email Template to Test *</label>
                <select class="form-select" id="template_type" name="template_type" required>
                    <option value="test">Simple Test Email</option>
                    <option value="welcome">Welcome Email Template</option>
                    <option value="transaction_debit">Transaction Alert - Debit</option>
                    <option value="transaction_credit">Transaction Alert - Credit</option>
                    <option value="password_reset">Password Reset Template</option>
                    <option value="kyc_approved">KYC Approved Template</option>
                    <option value="card_approved">Card Approved Template</option>
                    <option value="loan_approved">Loan Approved Template</option>
                    <option value="2fa">2FA/OTP Template</option>
                </select>
                <p class="help-text">
                    <i class="fas fa-palette"></i>
                    All templates include your bank logo and branding
                </p>
            </div>
            
            <button type="submit" class="btn btn-primary" id="sendButton">
                <i class="fas fa-paper-plane"></i>
                <span>Send Test Email</span>
            </button>
        </form>
        
        <div class="template-preview" id="templateInfo">
            <i class="fas fa-envelope" style="font-size: 48px; margin-bottom: 12px;"></i>
            <p>Select a template above to preview what will be sent</p>
        </div>
    </div>
</div>

<script>
const form = document.getElementById('testEmailForm');
const sendButton = document.getElementById('sendButton');
const messageContainer = document.getElementById('messageContainer');
const templateSelect = document.getElementById('template_type');
const templateInfo = document.getElementById('templateInfo');

// Template descriptions
const templates = {
    'test': {
        name: 'Simple Test Email',
        desc: 'Basic email to verify SMTP configuration works'
    },
    'welcome': {
        name: 'Welcome Email',
        desc: 'Sent to new users after registration'
    },
    'transaction_debit': {
        name: 'Debit Alert',
        desc: 'Sent when user sends money'
    },
    'transaction_credit': {
        name: 'Credit Alert',
        desc: 'Sent when user receives money'
    },
    'password_reset': {
        name: 'Password Reset',
        desc: 'Sent when user requests password reset'
    },
    'kyc_approved': {
        name: 'KYC Approved',
        desc: 'Sent when admin approves KYC verification'
    },
    'card_approved': {
        name: 'Card Approved',
        desc: 'Sent when admin approves card application'
    },
    'loan_approved': {
        name: 'Loan Approved',
        desc: 'Sent when admin approves loan application'
    },
    '2fa': {
        name: '2FA/OTP Code',
        desc: 'Sent when user logs in with 2FA enabled'
    }
};

// Update template info on selection
templateSelect.addEventListener('change', function() {
    const selected = templates[this.value];
    if (selected) {
        templateInfo.innerHTML = `
            <i class="fas fa-envelope" style="font-size: 48px; margin-bottom: 12px; color: #1e3a8a;"></i>
            <h3 style="margin: 0 0 8px 0; color: #202124;">${selected.name}</h3>
            <p style="margin: 0; color: #666;">${selected.desc}</p>
        `;
    }
});

// Trigger on load
templateSelect.dispatchEvent(new Event('change'));

form.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const email = document.getElementById('test_email').value;
    const template = document.getElementById('template_type').value;
    
    // Disable button and show loading
    sendButton.disabled = true;
    sendButton.innerHTML = '<div class="spinner"></div><span>Sending...</span>';
    
    // Send test email
    fetch('<?php echo SITE_URL; ?>/api/test-email.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            email: email,
            template: template
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('success', `<strong>Email Sent Successfully!</strong><br>Check ${email} for the test email. It may take a few moments to arrive.`);
        } else {
            showMessage('error', `<strong>Email Send Failed!</strong><br>${data.message || 'Please check your SMTP configuration.'}`);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('error', '<strong>Error!</strong><br>Failed to send test email. Check console for details.');
    })
    .finally(() => {
        // Re-enable button
        sendButton.disabled = false;
        sendButton.innerHTML = '<i class="fas fa-paper-plane"></i><span>Send Test Email</span>';
    });
});

function showMessage(type, message) {
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
</script>

<?php
// Only close HTML if accessed directly (not included by controller)
if (!defined('EMAIL_SUBPAGE') || EMAIL_SUBPAGE !== true) {
    echo '</div></div></div>'; // Close content-area, main-content-area, dashboard-container
    echo '</body></html>';
}
?>

