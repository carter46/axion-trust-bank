<?php 
$pageTitle = 'Security Settings - ' . getSiteName();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

requireLogin();

// Fetch user security data
$db = Database::getInstance();
$userId = $_SESSION['user_id'];

$stmt = $db->query("SELECT two_factor_enabled, transfer_pin, role FROM users WHERE id = ?", [$userId]);
$user = $stmt->fetch();

// SECURITY: Verify user exists (should be caught by requireLogin, but double-check)
if (!$user) {
    // User account was deleted during session - requireLogin should have caught this
    // But if we get here, destroy session and redirect
    session_destroy();
    $_SESSION['error'] = 'Your account is no longer active.';
    redirect('/auth/login');
}

$has2FA = isset($user['two_factor_enabled']) && $user['two_factor_enabled'] == 1;
$hasTransferPin = !empty($user['transfer_pin'] ?? '');
$isAdmin = isset($user['role']) && $user['role'] === 'admin';

// Check if this is first login and user needs to setup security
// For admins, 2FA is optional (not required)
$isFirstLogin = isset($_GET['logged_in']) && $_GET['logged_in'] == '1';

// Check if user was redirected here due to incomplete security setup
// This shows a persistent alert even when redirected from other pages
$wasRedirectedForSecurity = isset($_SESSION['security_setup_required']) && $_SESSION['security_setup_required'];

// Check if 2FA is required system-wide or disabled entirely
$twoFactorRequired = false;
$twoFactorDisabled = false;
if (!$isAdmin) {
    try {
        require_once __DIR__ . '/../../includes/system-settings.php';
        $systemSettings = SystemSettings::getInstance();
        $twoFactorRequired = $systemSettings->is2FARequired();
        $twoFactorDisabled = $systemSettings->is2FADisabled();
    } catch (Exception $e) {
        error_log("SystemSettings error in security page: " . $e->getMessage());
        $twoFactorRequired = false;
        $twoFactorDisabled = false;
    }
}

// Setup gate is Transfer PIN only — Login PIN removed; 2FA is optional
$needsSetup = !$hasTransferPin;

// Clear the flag if setup is now complete
if (!$needsSetup) {
    unset($_SESSION['security_setup_required']);
    unset($_SESSION['security_onboarding']);
}

$forceSecuritySetup = isForceSecuritySetupEnabled();
$isOnboardingWizard = (
    !empty($_SESSION['security_onboarding'])
    || !empty($_SESSION['security_setup_required'])
    || (isset($_GET['verified']) && $_GET['verified'] === '1' && isset($_GET['setup']) && $_GET['setup'] === '1')
    || $isFirstLogin
) && $needsSetup && !$isAdmin;

if ($isOnboardingWizard) {
    $_SESSION['security_onboarding'] = true;
}

$needs2FAStep = false;
$showOnboardingWizard = $isOnboardingWizard;

// Show security setup notice on first login OR when redirected (skip during guided wizard)
$showSecuritySetupNotice = !$showOnboardingWizard && ($wasRedirectedForSecurity || ($isFirstLogin && $needsSetup)) && $needsSetup;

// Include head
include __DIR__ . '/../../includes/head.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<style>
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
    transition: all 0.3s;
    margin-bottom: 20px;
}

.back-button:hover {
    background: #e5e7eb;
    transform: translateX(-4px);
}

/* Security Page Styles */
.security-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 0;
}

.security-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: all 0.3s;
}

.security-card:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
    transform: translateY(-2px);
}

.security-card h4 {
    font-size: 18px;
    color: #1e3a8a;
    margin: 0 0 8px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.security-card p {
    color: #666;
    font-size: 14px;
    margin: 0 0 16px 0;
    line-height: 1.5;
}

.security-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 16px;
}

.status-active {
    background: #d1fae5;
    color: #065f46;
}

.status-inactive {
    background: #fee2e2;
    color: #991b1b;
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: currentColor;
}

.btn {
    display: inline-block;
    padding: 10px 20px;
    font-size: 14px;
    font-weight: 600;
    text-align: center;
    text-decoration: none;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s;
    width: 100%;
}

.btn-primary {
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    color: white;
}

.btn-primary:hover:not(:disabled) {
    background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
}

.btn-primary:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    pointer-events: none;
}

.btn-loader {
    display: inline-flex;
    align-items: center;
}

.btn-loader i {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.btn-secondary {
    background: #f3f4f6;
    color: #374151;
}

.btn-secondary:hover {
    background: #e5e7eb;
}

.btn-danger {
    background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
    color: white;
}

.btn-danger:hover {
    background: linear-gradient(135deg, #b91c1c 0%, #dc2626 100%);
}

/* PIN Modal Styles */
.modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(4px);
    z-index: 10000;
    animation: fadeIn 0.3s;
}

.modal-overlay.active {
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal {
    background: white;
    border-radius: 16px;
    padding: 32px;
    max-width: 400px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    animation: slideUp 0.3s;
}

.modal h3 {
    font-size: 24px;
    color: #1e3a8a;
    margin: 0 0 8px 0;
}

.modal p {
    color: #666;
    font-size: 14px;
    margin: 0 0 24px 0;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
}

.form-control {
    width: 100%;
    padding: 12px;
    font-size: 16px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    transition: all 0.3s;
    box-sizing: border-box;
}

.form-control:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.pin-input-container {
    display: flex;
    gap: 12px;
    justify-content: center;
    margin: 20px 0;
}

.pin-digit {
    width: 50px;
    height: 60px;
    font-size: 24px;
    font-weight: 700;
    text-align: center;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    transition: all 0.3s;
}

.pin-digit:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.modal-actions {
    display: flex;
    gap: 12px;
    margin-top: 24px;
}

.alert {
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: none;
}

.alert.show {
    display: block;
}

.alert-success {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #6ee7b7;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fca5a5;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 768px) {
    .security-grid {
        grid-template-columns: 1fr;
    }
    
    .modal {
        padding: 24px;
    }
    
    .pin-digit {
        width: 40px;
        height: 50px;
        font-size: 20px;
    }
}
</style>

<a href="<?php echo SITE_URL; ?>/profile" class="back-button">
    <i class="fas fa-arrow-left"></i> Back to Profile
</a>

<div class="page-header">
    <h1>
        <i class="fas fa-shield-alt"></i>
        Security Settings
    </h1>
    <p>Manage your account security and authentication methods</p>
</div>

<!-- Security Settings Grid -->
<div class="security-grid">
    
    <!-- Transfer PIN Card -->
    <div class="security-card">
        <h4>
            <i class="fas fa-lock"></i>
            Transfer PIN
        </h4>
        <p>Required 4-digit PIN to authorize all money transfers</p>
        
        <?php if ($hasTransferPin): ?>
            <div class="security-status status-active">
                <span class="status-dot"></span>
                PIN Active
            </div>
        <?php else: ?>
            <div class="security-status status-inactive">
                <span class="status-dot"></span>
                Not Set
            </div>
        <?php endif; ?>
        
        <button onclick="openTransferPINModal()" class="btn btn-primary">
            <i class="fas <?php echo $hasTransferPin ? 'fa-edit' : 'fa-plus'; ?>"></i>
            <?php echo $hasTransferPin ? 'Update Transfer PIN' : 'Setup Transfer PIN'; ?>
        </button>
    </div>
    
    <!-- Password Card -->
    <div class="security-card">
        <h4>
            <i class="fas fa-shield-alt"></i>
            Password
        </h4>
        <p>Update your account password for enhanced security</p>
        
        <div class="security-status status-active">
            <span class="status-dot"></span>
            Password Set
        </div>
        
        <button onclick="openPasswordModal()" class="btn btn-primary">
            <i class="fas fa-edit"></i>
            Change Password
        </button>
    </div>
    
    <!-- 2FA Card -->
    <div class="security-card">
        <h4>
            <i class="fas fa-mobile-alt"></i>
            Two-Factor Authentication
        </h4>
        <p>Add an extra layer of security with OTP verification</p>
        
        <?php if ($has2FA): ?>
            <div class="security-status status-active">
                <span class="status-dot"></span>
                Enabled
            </div>
        <?php else: ?>
            <div class="security-status status-inactive">
                <span class="status-dot"></span>
                Disabled
            </div>
        <?php endif; ?>
        
        <button onclick="toggle2FA()" class="btn btn-<?php echo $has2FA ? 'danger' : 'primary'; ?>" id="twoFABtn">
            <i class="fas fa-<?php echo $has2FA ? 'times' : 'check'; ?>"></i>
            <?php echo $has2FA ? 'Disable 2FA' : 'Enable 2FA'; ?>
        </button>
    </div>
    
</div>

<!-- Security Setup Notice Modal (shows on first login OR when redirected) -->
<?php if ($showSecuritySetupNotice): ?>
<div class="modal-overlay" id="securitySetupNotice" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.7); z-index: 10000; align-items: center; justify-content: center; animation: fadeIn 0.3s;">
    <div class="modal" style="max-width: 500px; margin: 20px; position: relative;">
        <button id="closeSecurityNotice" style="position: absolute; top: 16px; right: 16px; background: none; border: none; font-size: 24px; color: #666; cursor: pointer; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 50%; transition: all 0.3s;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='none'">
            &times;
        </button>
        
        <div style="text-align: center; margin-bottom: 24px;">
            <div style="width: 64px; height: 64px; background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                <i class="fas fa-shield-alt" style="font-size: 32px; color: white;"></i>
            </div>
            <h3 style="margin: 0 0 8px 0; color: #1e3a8a;">Secure Your Account</h3>
            <p style="color: #666; margin: 0;">
                <?php if ($wasRedirectedForSecurity): ?>
                    You cannot access other pages until you complete your security setup.
                <?php else: ?>
                    Welcome! Let's set up your security settings
                <?php endif; ?>
            </p>
        </div>
        
        <div style="background: #f0f4ff; padding: 16px; border-radius: 8px; margin-bottom: 24px;">
            <p style="margin: 0 0 12px 0; color: #1e3a8a; font-weight: 600;">Please set up the following:</p>
            <ul style="margin: 0; padding-left: 20px; color: #374151;">
                <?php if (!$hasTransferPin): ?>
                <li style="margin-bottom: 8px;"><strong>Transfer PIN</strong> - Required for all money transfers</li>
                <?php endif; ?>
            </ul>
        </div>
        
        <div style="text-align: center; padding-top: 16px; border-top: 1px solid #e5e7eb;">
            <p style="margin: 0 0 12px 0; color: #666; font-size: 14px;">
                This notice will close automatically in <span id="noticeCountdown" style="font-weight: 700; color: #1e3a8a;">10</span> seconds
            </p>
            <button id="closeSecurityNoticeBtn" class="btn btn-primary" style="width: auto; padding: 10px 24px;">
                Got it!
            </button>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Transfer PIN Modal -->
<div class="modal-overlay" id="transferPINModal">
    <div class="modal">
        <h3>Setup Transfer PIN</h3>
        <p>Create a 4-digit PIN to authorize transfers</p>
        
        <div class="alert alert-error" id="transferPINError"></div>
        <div class="alert alert-success" id="transferPINSuccess"></div>
        
        <form id="transferPINForm">
            <div class="form-group transfer-pin-password-group"<?php echo !$hasTransferPin ? ' style="display:none;"' : ''; ?>>
                <label>Current Password</label>
                <input type="password" class="form-control" id="transferPINCurrentPassword"<?php echo !$hasTransferPin ? '' : ' required'; ?>>
            </div>
            
            <div class="form-group">
                <label>New 4-Digit PIN</label>
                <div class="pin-input-container">
                    <input type="text" maxlength="1" class="pin-digit" data-transfer-index="0" pattern="[0-9]" inputmode="numeric">
                    <input type="text" maxlength="1" class="pin-digit" data-transfer-index="1" pattern="[0-9]" inputmode="numeric">
                    <input type="text" maxlength="1" class="pin-digit" data-transfer-index="2" pattern="[0-9]" inputmode="numeric">
                    <input type="text" maxlength="1" class="pin-digit" data-transfer-index="3" pattern="[0-9]" inputmode="numeric">
                </div>
            </div>
            
            <div class="form-group">
                <label>Confirm 4-Digit PIN</label>
                <div class="pin-input-container">
                    <input type="text" maxlength="1" class="pin-digit" data-transfer-confirm-index="0" pattern="[0-9]" inputmode="numeric">
                    <input type="text" maxlength="1" class="pin-digit" data-transfer-confirm-index="1" pattern="[0-9]" inputmode="numeric">
                    <input type="text" maxlength="1" class="pin-digit" data-transfer-confirm-index="2" pattern="[0-9]" inputmode="numeric">
                    <input type="text" maxlength="1" class="pin-digit" data-transfer-confirm-index="3" pattern="[0-9]" inputmode="numeric">
                </div>
            </div>
            
            <div class="modal-actions">
                <?php if ($showOnboardingWizard && !$forceSecuritySetup): ?>
                <button type="button" onclick="skipSecurityOnboarding()" class="btn btn-secondary" style="flex: 1;">Skip for Now</button>
                <?php else: ?>
                <button type="button" onclick="closeTransferPINModal()" class="btn btn-secondary" style="flex: 1;">Cancel</button>
                <?php endif; ?>
                <button type="submit" class="btn btn-primary" id="transferPINSaveBtn" style="flex: 2;">
                    <span class="btn-text">Save PIN</span>
                    <span class="btn-loader" style="display: none;">
                        <i class="fas fa-spinner fa-spin" style="margin-right: 8px;"></i>
                        Saving...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Onboarding Complete -->
<div class="modal-overlay" id="onboardingCompleteModal">
    <div class="modal" style="text-align:center;">
        <div style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#10b981,#059669);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
            <i class="fas fa-shield-alt" style="font-size:32px;color:white;"></i>
        </div>
        <h3>Your Account Is Now Protected</h3>
        <p>Security setup is complete. Redirecting you to the dashboard…</p>
        <a href="<?php echo SITE_URL; ?>/dashboard" class="btn btn-primary" style="display:inline-block;width:auto;padding:12px 28px;margin-top:8px;" onclick="clearSecurityOnboarding()">
            Go to Dashboard
        </a>
    </div>
</div>

<!-- Password Change Modal -->
<div class="modal-overlay" id="passwordModal">
    <div class="modal">
        <h3>Change Password</h3>
        <p>Update your account password</p>
        
        <div class="alert alert-error" id="passwordError"></div>
        <div class="alert alert-success" id="passwordSuccess"></div>
        
        <form id="passwordForm">
            <div class="form-group">
                <label>Current Password</label>
                <input type="password" class="form-control" id="currentPassword" required>
            </div>
            
            <div class="form-group">
                <label>New Password</label>
                <input type="password" class="form-control" id="newPassword" required minlength="8">
            </div>
            
            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" class="form-control" id="confirmPassword" required>
            </div>
            
            <div class="modal-actions">
                <button type="button" onclick="closePasswordModal()" class="btn btn-secondary" style="flex: 1;">Cancel</button>
                <button type="submit" class="btn btn-primary" id="passwordSaveBtn" style="flex: 2;">
                    <span class="btn-text">Update Password</span>
                    <span class="btn-loader" style="display: none;">
                        <i class="fas fa-spinner fa-spin" style="margin-right: 8px;"></i>
                        Updating...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const isOnboardingWizard = <?php echo $showOnboardingWizard ? 'true' : 'false'; ?>;
let wizardHasTransferPin = <?php echo $hasTransferPin ? 'true' : 'false'; ?>;
const dashboardUrl = <?php echo json_encode(SITE_URL . '/dashboard'); ?>;

function finishSecuritySetupAndGoToDashboard() {
    clearSecurityOnboarding();
    window.location.href = dashboardUrl;
}

function advanceSecurityWizard() {
    if (!isOnboardingWizard) {
        location.reload();
        return;
    }
    if (!wizardHasTransferPin) {
        openTransferPINModal();
        return;
    }
    document.getElementById('onboardingCompleteModal').classList.add('active');
    setTimeout(finishSecuritySetupAndGoToDashboard, 900);
}

async function skipSecurityOnboarding() {
    try {
        const response = await fetch('<?php echo SITE_URL; ?>/api/skip-security-onboarding.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
        });
        const data = await response.json();
        if (data.success && data.redirect) {
            window.location.href = data.redirect;
        } else {
            alert(data.message || 'Security setup cannot be skipped.');
        }
    } catch (e) {
        alert('An error occurred. Please try again.');
    }
}

function clearSecurityOnboarding() {
    fetch('<?php echo SITE_URL; ?>/api/complete-security-onboarding.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }
    }).catch(function() {});
}

if (isOnboardingWizard) {
    window.addEventListener('DOMContentLoaded', function() {
        advanceSecurityWizard();
    });
    if (document.readyState !== 'loading') {
        advanceSecurityWizard();
    }
}

// PIN Input Auto-Advance
document.querySelectorAll('.pin-digit').forEach((input, index, inputs) => {
    input.addEventListener('input', function(e) {
        const value = this.value;
        
        // Only allow digits
        if (!/^\d$/.test(value)) {
            this.value = '';
            return;
        }
        
        // Auto-advance to next input
        const nextIndex = Array.from(inputs).indexOf(this) + 1;
        if (value && nextIndex < inputs.length && inputs[nextIndex].closest('.pin-input-container') === this.closest('.pin-input-container')) {
            inputs[nextIndex].focus();
        }
    });
    
    input.addEventListener('keydown', function(e) {
        // Handle backspace
        if (e.key === 'Backspace' && !this.value) {
            const prevIndex = Array.from(inputs).indexOf(this) - 1;
            if (prevIndex >= 0 && inputs[prevIndex].closest('.pin-input-container') === this.closest('.pin-input-container')) {
                inputs[prevIndex].focus();
            }
        }
    });
    
    // Handle paste
    input.addEventListener('paste', function(e) {
        e.preventDefault();
        const paste = (e.clipboardData || window.clipboardData).getData('text');
        const digits = paste.replace(/\D/g, '');
        
        const container = this.closest('.pin-input-container');
        const containerInputs = container.querySelectorAll('.pin-digit');
        const startIndex = Array.from(containerInputs).indexOf(this);
        
        for (let i = 0; i < digits.length && (startIndex + i) < containerInputs.length; i++) {
            containerInputs[startIndex + i].value = digits[i];
        }
        
        // Focus last filled input
        const lastIndex = Math.min(startIndex + digits.length - 1, containerInputs.length - 1);
        containerInputs[lastIndex].focus();
    });
});

// Transfer PIN Modal
function openTransferPINModal() {
    document.getElementById('transferPINModal').classList.add('active');
}

function closeTransferPINModal() {
    document.getElementById('transferPINModal').classList.remove('active');
    document.getElementById('transferPINForm').reset();
    document.getElementById('transferPINError').classList.remove('show');
    document.getElementById('transferPINSuccess').classList.remove('show');
}

// Password Modal
function openPasswordModal() {
    document.getElementById('passwordModal').classList.add('active');
}

function closePasswordModal() {
    document.getElementById('passwordModal').classList.remove('active');
    document.getElementById('passwordForm').reset();
    document.getElementById('passwordError').classList.remove('show');
    document.getElementById('passwordSuccess').classList.remove('show');
}

// Close modals on overlay click
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
        }
    });
});

// Transfer PIN Form Submit
document.getElementById('transferPINForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const errorDiv = document.getElementById('transferPINError');
    const successDiv = document.getElementById('transferPINSuccess');
    const saveBtn = document.getElementById('transferPINSaveBtn');
    const btnText = saveBtn.querySelector('.btn-text');
    const btnLoader = saveBtn.querySelector('.btn-loader');
    
    errorDiv.classList.remove('show');
    successDiv.classList.remove('show');
    
    // Get PIN values
    const pinInputs = document.querySelectorAll('#transferPINModal [data-transfer-index]');
    const confirmInputs = document.querySelectorAll('#transferPINModal [data-transfer-confirm-index]');
    
    const pin = Array.from(pinInputs).map(input => input.value).join('');
    const confirmPin = Array.from(confirmInputs).map(input => input.value).join('');
    
    // Validation
    if (pin.length !== 4) {
        errorDiv.textContent = 'Please enter a 4-digit PIN';
        errorDiv.classList.add('show');
        return;
    }
    
    if (pin !== confirmPin) {
        errorDiv.textContent = 'PINs do not match';
        errorDiv.classList.add('show');
        return;
    }
    
    const passwordEl = document.getElementById('transferPINCurrentPassword');
    const password = passwordEl ? passwordEl.value : '';
    const onboarding = !wizardHasTransferPin;
    
    // Show loading state
    saveBtn.disabled = true;
    btnText.style.display = 'none';
    btnLoader.style.display = 'inline-block';
    
    try {
        const response = await fetch('<?php echo SITE_URL; ?>/api/update-transfer-pin.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ password, pin, onboarding })
        });
        
        const data = await response.json();
        
        if (data.success) {
            wizardHasTransferPin = true;
            saveBtn.disabled = false;
            btnText.style.display = 'inline';
            btnLoader.style.display = 'none';
            closeTransferPINModal();
            advanceSecurityWizard();
        } else {
            // Hide loading state on error
            saveBtn.disabled = false;
            btnText.style.display = 'inline';
            btnLoader.style.display = 'none';
            
            errorDiv.textContent = data.message || 'Failed to update Transfer PIN';
            errorDiv.classList.add('show');
        }
    } catch (error) {
        // Hide loading state on error
        saveBtn.disabled = false;
        btnText.style.display = 'inline';
        btnLoader.style.display = 'none';
        
        errorDiv.textContent = 'An error occurred. Please try again.';
        errorDiv.classList.add('show');
    }
});

// Password Form Submit
document.getElementById('passwordForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const errorDiv = document.getElementById('passwordError');
    const successDiv = document.getElementById('passwordSuccess');
    const saveBtn = document.getElementById('passwordSaveBtn');
    const btnText = saveBtn.querySelector('.btn-text');
    const btnLoader = saveBtn.querySelector('.btn-loader');
    
    errorDiv.classList.remove('show');
    successDiv.classList.remove('show');
    
    const currentPassword = document.getElementById('currentPassword').value;
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    // Validation
    if (newPassword.length < 8) {
        errorDiv.textContent = 'Password must be at least 8 characters';
        errorDiv.classList.add('show');
        return;
    }
    
    if (newPassword !== confirmPassword) {
        errorDiv.textContent = 'Passwords do not match';
        errorDiv.classList.add('show');
        return;
    }
    
    // Show loading state
    saveBtn.disabled = true;
    btnText.style.display = 'none';
    btnLoader.style.display = 'inline-block';
    
    try {
        const response = await fetch('<?php echo SITE_URL; ?>/api/change-password.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ currentPassword, newPassword })
        });
        
        const data = await response.json();
        
        if (data.success) {
            successDiv.textContent = 'Password changed successfully!';
            successDiv.classList.add('show');
            
            setTimeout(() => {
                closePasswordModal();
            }, 1500);
        } else {
            // Hide loading state on error
            saveBtn.disabled = false;
            btnText.style.display = 'inline';
            btnLoader.style.display = 'none';
            
            errorDiv.textContent = data.message || 'Failed to change password';
            errorDiv.classList.add('show');
        }
    } catch (error) {
        // Hide loading state on error
        saveBtn.disabled = false;
        btnText.style.display = 'inline';
        btnLoader.style.display = 'none';
        
        errorDiv.textContent = 'An error occurred. Please try again.';
        errorDiv.classList.add('show');
    }
});

// Toggle 2FA
async function toggle2FA() {
    const btn = document.getElementById('twoFABtn');
    const is2FAEnabled = <?php echo $has2FA ? 'true' : 'false'; ?>;
    
    if (confirm(is2FAEnabled ? 'Are you sure you want to disable 2FA?' : 'Are you sure you want to enable 2FA?')) {
        // Show loading state
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right: 8px;"></i>Processing...';
        
        try {
            const response = await fetch('<?php echo SITE_URL; ?>/api/toggle-2fa.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' }
            });
            
            const data = await response.json();
            
            if (data.success) {
                location.reload();
            } else {
                // Restore button on error
                btn.disabled = false;
                btn.innerHTML = originalText;
                alert(data.message || 'Failed to toggle 2FA');
            }
        } catch (error) {
            // Restore button on error
            btn.disabled = false;
            btn.innerHTML = originalText;
            alert('An error occurred. Please try again.');
        }
    }
}

// First-time login security setup notice
<?php if ($showSecuritySetupNotice): ?>
(function() {
    const noticeModal = document.getElementById('securitySetupNotice');
    if (!noticeModal) return;
    
    let countdown = 10;
    let countdownInterval;
    
    function closeNotice() {
        if (countdownInterval) {
            clearInterval(countdownInterval);
            countdownInterval = null;
        }
        noticeModal.style.display = 'none';
        // Remove logged_in parameter from URL
        const url = new URL(window.location.href);
        url.searchParams.delete('logged_in');
        window.history.replaceState({}, '', url);
    }
    
    function updateCountdown() {
        const countdownEl = document.getElementById('noticeCountdown');
        if (countdownEl && countdown > 0) {
            countdownEl.textContent = countdown;
        }
        
        if (countdown <= 0) {
            closeNotice();
            return;
        }
        countdown--;
    }
    
    // Show modal after page load
    window.addEventListener('DOMContentLoaded', function() {
        if (noticeModal) {
            noticeModal.style.display = 'flex';
            // Start countdown immediately
            updateCountdown();
            countdownInterval = setInterval(updateCountdown, 1000);
        }
    });
    
    // If DOM is already loaded, show immediately
    if (document.readyState === 'loading') {
        // DOM is still loading, wait for DOMContentLoaded
    } else {
        // DOM already loaded
        noticeModal.style.display = 'flex';
        updateCountdown();
        countdownInterval = setInterval(updateCountdown, 1000);
    }
    
    // Close button handlers (both X button and Got it button)
    const closeBtn = document.getElementById('closeSecurityNotice');
    const closeBtn2 = document.getElementById('closeSecurityNoticeBtn');
    
    if (closeBtn) {
        closeBtn.addEventListener('click', closeNotice);
    }
    if (closeBtn2) {
        closeBtn2.addEventListener('click', closeNotice);
    }
    
    // Also close on backdrop click
    noticeModal.addEventListener('click', function(e) {
        if (e.target === noticeModal) {
            closeNotice();
        }
    });
})();
<?php endif; ?>
</script>

<?php
// Include mobile navigation and closing tags
include __DIR__ . '/../../includes/mobile-nav.php';
?>
