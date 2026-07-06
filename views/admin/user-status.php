<?php
$pageTitle = 'User Status Control - Admin - SecureBank';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAdmin();

// Get user ID from URL
$userId = isset($GLOBALS['id']) ? intval($GLOBALS['id']) : 0;

if (!$userId) {
    $_SESSION['error'] = 'Invalid user ID';
    redirect('/admin/users');
}

// Fetch user data
$db = Database::getInstance();
$sql = "SELECT * FROM users WHERE id = ? AND role != 'admin'";
$stmt = $db->query($sql, [$userId]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['error'] = 'User not found';
    redirect('/admin/users');
}

include __DIR__ . '/../../includes/head.php';
include __DIR__ . '/../../includes/admin-sidebar.php';
include __DIR__ . '/../../includes/admin-modals.php';
?>

<style>
.status-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.header {
    margin-bottom: 30px;
}

.back-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 14px;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    color: #0f172a;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: all 0.2s;
}

.back-btn:hover {
    background: #f8fafc;
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
}

.user-header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-top: 16px;
}

.avatar {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 20px;
}

.user-info h1 {
    font-size: 28px;
    font-weight: 600;
    color: #202124;
    margin: 0;
}

.user-info .email {
    color: #666;
    font-size: 15px;
    margin-top: 4px;
}

.status-grid {
    display: grid;
    gap: 24px;
}

.status-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.status-card h3 {
    font-size: 20px;
    font-weight: 600;
    color: #1e3a8a;
    margin: -8px -8px 20px -8px;
    padding: 16px;
    border-bottom: 2px solid #e8f0ff;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.status-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: #1e3a8a;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
    font-size: 14px;
}

.status-select {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s;
    background: white;
    cursor: pointer;
}

.status-select:focus {
    outline: none;
    border-color: #1e3a8a;
    box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
}

.status-select option {
    padding: 8px;
}

.status-active { color: #059669; font-weight: 600; }
.status-pending { color: #d97706; font-weight: 600; }
.status-suspended { color: #dc2626; font-weight: 600; }
.status-blocked { color: #6b7280; font-weight: 600; }
.status-hold { color: #7c3aed; font-weight: 600; }

.status-info {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 20px;
}

.status-info h4 {
    margin: 0 0 8px 0;
    color: #374151;
    font-size: 14px;
    font-weight: 600;
}

.status-info p {
    margin: 0;
    color: #6b7280;
    font-size: 13px;
    line-height: 1.5;
}

.quick-actions {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.quick-actions h3 {
    font-size: 18px;
    font-weight: 600;
    color: #0f172a;
    margin: 0 0 20px 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 14px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-primary {
    background: #3b82f6;
    color: white;
}

.btn-primary:hover {
    background: #2563eb;
    transform: translateY(-1px);
}

.btn-success {
    background: #10b981;
    color: white;
}

.btn-success:hover {
    background: #059669;
    transform: translateY(-1px);
}

.btn-warning {
    background: #f59e0b;
    color: white;
}

.btn-warning:hover {
    background: #d97706;
    transform: translateY(-1px);
}

.btn-danger {
    background: #ef4444;
    color: white;
}

.btn-danger:hover {
    background: #dc2626;
    transform: translateY(-1px);
}

/* Toggle Switch (for transfer security controls) */
.toggle-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 12px 14px;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    background: #f8fafc;
}
.toggle-row + .toggle-row {
    margin-top: 10px;
}
.toggle-meta {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.toggle-meta .title {
    font-weight: 700;
    color: #0f172a;
    font-size: 14px;
}
.toggle-meta .desc {
    font-size: 12px;
    color: #64748b;
    line-height: 1.4;
}
.switch {
    position: relative;
    display: inline-block;
    width: 52px;
    height: 30px;
    flex: 0 0 auto;
}
.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}
.slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #cbd5e1;
    transition: 0.2s;
    border-radius: 999px;
}
.slider:before {
    position: absolute;
    content: "";
    height: 22px;
    width: 22px;
    left: 4px;
    top: 4px;
    background-color: white;
    transition: 0.2s;
    border-radius: 999px;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.18);
}
.switch input:checked + .slider {
    background-color: #10b981;
}
.switch input:checked + .slider:before {
    transform: translateX(22px);
}
.code-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 12px 14px;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    background: #ffffff;
}
.code-value {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    font-weight: 700;
    color: #0f172a;
    letter-spacing: 0.08em;
    font-size: 14px;
}
.code-actions {
    display: inline-flex;
    gap: 8px;
    flex-wrap: wrap;
    justify-content: flex-end;
}
.btn-small {
    padding: 10px 12px;
    border-radius: 10px;
    font-size: 13px;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 10000; /* Higher than mobile nav (9999) */
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
}

@media (max-width: 768px) {
    .modal {
        z-index: 10000; /* Ensure modals are above mobile nav on mobile */
        align-items: center;
        justify-content: center;
    }
}

.modal-content {
    background: white;
    border-radius: 12px;
    width: 90%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.modal-header {
    padding: 20px 24px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #0f172a;
}

.modal-close {
    font-size: 24px;
    font-weight: bold;
    cursor: pointer;
    color: #6b7280;
    line-height: 1;
}

.modal-close:hover {
    color: #374151;
}

.modal-body {
    padding: 24px;
}

.modal-footer {
    padding: 20px 24px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    gap: 12px;
    justify-content: flex-end;
}

.form-input {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s;
}

.form-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

@media (max-width: 768px) {
    .status-container {
        padding: 16px;
    }
    
    .header {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
    
    .user-header {
        width: 100%;
    }
    
    .modal-content {
        width: 95%;
        margin: 20px;
    }
    
    .modal-footer {
        flex-direction: column;
    }
}
</style>

<div class="status-container">
    <!-- Header -->
    <div class="header">
        <a href="/admin/user/<?php echo $userId; ?>" class="back-btn">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                <path d="M15 6l-6 6 6 6" stroke="currentColor" stroke-width="2"/>
            </svg>
            Back to User
        </a>
        
        <div class="user-header">
            <div class="avatar"><?php echo strtoupper(substr($user['full_name'], 0, 2)); ?></div>
            <div class="user-info">
                <h1>Account Status Control</h1>
                <div class="email"><?php echo htmlspecialchars($user['email']); ?></div>
            </div>
        </div>
    </div>

    <div class="status-grid">
        <!-- Account Status -->
        <div class="status-card">
            <h3>
                <div class="status-icon">👤</div>
                Account Status
            </h3>
            
            <div class="status-info">
                <h4>Current Status</h4>
                <p>Control the user's account access level. Changes take effect immediately.</p>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="accountStatus">Account Status</label>
                <select id="accountStatus" class="status-select" onchange="updateAccountStatus()">
                    <option value="active" class="status-active" <?php echo $user['status'] === 'active' ? 'selected' : ''; ?>>🟢 Active - Full access to all features</option>
                    <option value="pending" class="status-pending" <?php echo $user['status'] === 'pending' ? 'selected' : ''; ?>>🟡 Pending - Limited access, awaiting verification</option>
                    <option value="suspended" class="status-suspended" <?php echo $user['status'] === 'suspended' ? 'selected' : ''; ?>>🔴 Suspended - Account temporarily disabled</option>
                    <option value="blocked" class="status-blocked" <?php echo $user['status'] === 'blocked' ? 'selected' : ''; ?>>⚫ Blocked - Account permanently disabled</option>
                    <option value="hold" class="status-hold" <?php echo $user['status'] === 'hold' ? 'selected' : ''; ?>>🟣 Hold - Account on hold, limited functionality</option>
                </select>
            </div>
        </div>

        <!-- KYC Status -->
        <div class="status-card">
            <h3>
                <div class="status-icon">🪪</div>
                KYC Status
            </h3>
            
            <div class="status-info">
                <h4>Manual KYC Control</h4>
                <p>Set KYC status even if the user did not submit documents.</p>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="kycStatus">KYC Status</label>
                <select id="kycStatus" class="status-select" onchange="onKycStatusChange()">
                    <option value="pending" <?php echo ($user['kyc_status'] ?? 'pending') === 'pending' ? 'selected' : ''; ?>>⏳ Pending</option>
                    <option value="verified" <?php echo ($user['kyc_status'] ?? '') === 'verified' ? 'selected' : ''; ?>>✅ Verified</option>
                    <option value="rejected" <?php echo ($user['kyc_status'] ?? '') === 'rejected' ? 'selected' : ''; ?>>❌ Rejected</option>
                </select>
            </div>
            
            <div class="form-group" id="kycRejectReasonWrap" style="display:none;">
                <label class="form-label" for="kycRejectReason">Rejection Reason (required)</label>
                <input type="text" id="kycRejectReason" class="form-input" placeholder="Reason for rejection">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="kycNotes">Admin Notes (optional)</label>
                <input type="text" id="kycNotes" class="form-input" placeholder="Internal notes">
            </div>
            
            <div class="form-group">
                <label style="display:flex; gap:10px; align-items:center;">
                    <input type="checkbox" id="activateOnVerify">
                    Activate account when setting Verified
                </label>
            </div>
            
            <button type="button" class="btn btn-success" onclick="updateKycStatus()">
                Save KYC Status
            </button>
        </div>

        <!-- Email Verification Status -->
        <div class="status-card">
            <h3>
                <div class="status-icon">📧</div>
                Email Verification Status
            </h3>
            
            <div class="status-info">
                <h4>Manual Email Verification</h4>
                <p>Manually verify user's email address. This allows the user to bypass email verification.</p>
            </div>
            
            <div class="form-group">
                <label class="form-label">Current Status</label>
                <div style="padding: 12px; background: <?php echo $user['email_verified'] ? '#d1fae5' : '#fee2e2'; ?>; border-radius: 8px; color: <?php echo $user['email_verified'] ? '#065f46' : '#991b1b'; ?>; font-weight: 600;">
                    <?php echo $user['email_verified'] ? '✅ Email Verified' : '❌ Email Not Verified'; ?>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">User Email</label>
                <div style="padding: 12px; background: #f8fafc; border-radius: 8px; color: #374151;">
                    <?php echo htmlspecialchars($user['email']); ?>
                </div>
            </div>
            
            <button type="button" class="btn btn-success" onclick="verifyUserEmail()" <?php echo $user['email_verified'] ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : ''; ?>>
                <?php echo $user['email_verified'] ? '✓ Email Already Verified' : 'Verify Email Address'; ?>
            </button>
        </div>

        <!-- Transaction Override -->
        <div class="status-card">
            <h3>
                <div class="status-icon">⚙️</div>
                Transaction Processing Override
            </h3>
            
            <div class="status-info">
                <h4>Transaction Override</h4>
                <p>Override how this user's transactions are processed. Useful for testing or disciplinary actions.</p>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="transactionOverride">Transaction Override</label>
                <select id="transactionOverride" class="status-select" onchange="updateTransactionOverride()">
                    <option value="normal" <?php echo ($user['transaction_override'] ?? 'normal') === 'normal' ? 'selected' : ''; ?>>🔄 Normal - Standard transaction processing</option>
                    <option value="force_success" <?php echo ($user['transaction_override'] ?? 'normal') === 'force_success' ? 'selected' : ''; ?>>✅ Force Success - All transactions succeed</option>
                    <option value="force_pending" <?php echo ($user['transaction_override'] ?? 'normal') === 'force_pending' ? 'selected' : ''; ?>>⏳ Force Pending - All transactions stay pending</option>
                    <option value="force_failed" <?php echo ($user['transaction_override'] ?? 'normal') === 'force_failed' ? 'selected' : ''; ?>>❌ Force Failed - All transactions fail</option>
                </select>
            </div>
        </div>

        <!-- Transfer Security Controls -->
        <div class="status-card">
            <h3>
                <div class="status-icon">🛡️</div>
                Transfer Security Controls
            </h3>
            
            <div class="status-info">
                <h4>Per-user Transfer Security</h4>
                <p>Toggle extra security steps during transfers for this specific user, and manage admin-only codes.</p>
            </div>

            <!-- Transfer OTP -->
            <div class="toggle-row">
                <div class="toggle-meta">
                    <div class="title">Transfer OTP (Email)</div>
                    <div class="desc">If enabled, user must enter an OTP sent to their email during transfers.</div>
                </div>
                <label class="switch" title="Toggle transfer OTP requirement">
                    <input id="transferOtpToggle" type="checkbox" <?php echo ((int)($user['transfer_otp_required'] ?? 1) === 1) ? 'checked' : ''; ?> onchange="updateTransferOtpRequired()">
                    <span class="slider"></span>
                </label>
            </div>

            <!-- IMF -->
            <div style="margin-top: 14px;">
                <div class="toggle-row">
                    <div class="toggle-meta">
                        <div class="title">IMF Code Requirement</div>
                        <div class="desc">If enabled, user must provide the IMF code (provided by support via live chat).</div>
                    </div>
                    <label class="switch" title="Toggle IMF requirement">
                        <input id="imfRequiredToggle" type="checkbox" <?php echo ((int)($user['imf_required'] ?? 0) === 1) ? 'checked' : ''; ?> onchange="updateImfRequired()">
                        <span class="slider"></span>
                    </label>
                </div>
                <div class="code-row" style="margin-top: 10px;">
                    <?php $imfCode = (string)($user['imf_code'] ?? ''); ?>
                    <?php $imfMasked = $imfCode ? (str_repeat('•', max(0, strlen($imfCode) - 4)) . substr($imfCode, -4)) : 'Not set'; ?>
                    <div>
                        <div style="font-weight: 700; color: #0f172a; margin-bottom: 6px;">IMF Code</div>
                        <div class="code-value" id="imfCodeValue" data-code="<?php echo htmlspecialchars($imfCode); ?>" data-masked="<?php echo htmlspecialchars($imfMasked); ?>"><?php echo htmlspecialchars($imfMasked); ?></div>
                    </div>
                    <div class="code-actions">
                        <button type="button" class="btn btn-warning btn-small" onclick="toggleCodeVisibility('imfCodeValue')">Show</button>
                        <button type="button" class="btn btn-primary btn-small" onclick="copyCode('imfCodeValue')">Copy</button>
                        <button type="button" class="btn btn-success btn-small" onclick="regenerateImfCode()">Regenerate</button>
                    </div>
                </div>
            </div>

            <!-- Federal SWIFT -->
            <div style="margin-top: 14px;">
                <div class="toggle-row">
                    <div class="toggle-meta">
                        <div class="title">Federal SWIFT Code Requirement</div>
                        <div class="desc">If enabled, user must provide the Federal SWIFT code (provided by support via live chat).</div>
                    </div>
                    <label class="switch" title="Toggle Federal SWIFT requirement">
                        <input id="federalSwiftRequiredToggle" type="checkbox" <?php echo ((int)($user['federal_swift_required'] ?? 0) === 1) ? 'checked' : ''; ?> onchange="updateFederalSwiftRequired()">
                        <span class="slider"></span>
                    </label>
                </div>
                <div class="code-row" style="margin-top: 10px;">
                    <?php $swiftCode = (string)($user['federal_swift_code'] ?? ''); ?>
                    <?php $swiftMasked = $swiftCode ? (str_repeat('•', max(0, strlen($swiftCode) - 4)) . substr($swiftCode, -4)) : 'Not set'; ?>
                    <div>
                        <div style="font-weight: 700; color: #0f172a; margin-bottom: 6px;">Federal SWIFT Code</div>
                        <div class="code-value" id="federalSwiftCodeValue" data-code="<?php echo htmlspecialchars($swiftCode); ?>" data-masked="<?php echo htmlspecialchars($swiftMasked); ?>"><?php echo htmlspecialchars($swiftMasked); ?></div>
                    </div>
                    <div class="code-actions">
                        <button type="button" class="btn btn-warning btn-small" onclick="toggleCodeVisibility('federalSwiftCodeValue')">Show</button>
                        <button type="button" class="btn btn-primary btn-small" onclick="copyCode('federalSwiftCodeValue')">Copy</button>
                        <button type="button" class="btn btn-success btn-small" onclick="regenerateFederalSwiftCode()">Regenerate</button>
                    </div>
                </div>
            </div>

            <!-- VAT -->
            <div style="margin-top: 14px;">
                <div class="toggle-row">
                    <div class="toggle-meta">
                        <div class="title">VAT Code Requirement</div>
                        <div class="desc">If enabled, user must provide the VAT (Value Added Tax) code (provided by support via live chat).</div>
                    </div>
                    <label class="switch" title="Toggle VAT requirement">
                        <input id="vatRequiredToggle" type="checkbox" <?php echo ((int)($user['vat_required'] ?? 0) === 1) ? 'checked' : ''; ?> onchange="updateVatRequired()">
                        <span class="slider"></span>
                    </label>
                </div>
                <div class="code-row" style="margin-top: 10px;">
                    <?php $vatCode = (string)($user['vat_code'] ?? ''); ?>
                    <?php $vatMasked = $vatCode ? (str_repeat('•', max(0, strlen($vatCode) - 4)) . substr($vatCode, -4)) : 'Not set'; ?>
                    <div>
                        <div style="font-weight: 700; color: #0f172a; margin-bottom: 6px;">VAT Code</div>
                        <div class="code-value" id="vatCodeValue" data-code="<?php echo htmlspecialchars($vatCode); ?>" data-masked="<?php echo htmlspecialchars($vatMasked); ?>"><?php echo htmlspecialchars($vatMasked); ?></div>
                    </div>
                    <div class="code-actions">
                        <button type="button" class="btn btn-warning btn-small" onclick="toggleCodeVisibility('vatCodeValue')">Show</button>
                        <button type="button" class="btn btn-primary btn-small" onclick="copyCode('vatCodeValue')">Copy</button>
                        <button type="button" class="btn btn-success btn-small" onclick="regenerateVatCode()">Regenerate</button>
                    </div>
                </div>
            </div>

            <!-- TAC -->
            <div style="margin-top: 14px;">
                <div class="toggle-row">
                    <div class="toggle-meta">
                        <div class="title">TAC Requirement</div>
                        <div class="desc">If enabled, user must provide the TAC — Transaction Authorization Code (provided by support via live chat).</div>
                    </div>
                    <label class="switch" title="Toggle TAC requirement">
                        <input id="tacRequiredToggle" type="checkbox" <?php echo ((int)($user['tac_required'] ?? 0) === 1) ? 'checked' : ''; ?> onchange="updateTacRequired()">
                        <span class="slider"></span>
                    </label>
                </div>
                <div class="code-row" style="margin-top: 10px;">
                    <?php $tacCode = (string)($user['tac_code'] ?? ''); ?>
                    <?php $tacMasked = $tacCode ? (str_repeat('•', max(0, strlen($tacCode) - 4)) . substr($tacCode, -4)) : 'Not set'; ?>
                    <div>
                        <div style="font-weight: 700; color: #0f172a; margin-bottom: 6px;">TAC</div>
                        <div class="code-value" id="tacCodeValue" data-code="<?php echo htmlspecialchars($tacCode); ?>" data-masked="<?php echo htmlspecialchars($tacMasked); ?>"><?php echo htmlspecialchars($tacMasked); ?></div>
                    </div>
                    <div class="code-actions">
                        <button type="button" class="btn btn-warning btn-small" onclick="toggleCodeVisibility('tacCodeValue')">Show</button>
                        <button type="button" class="btn btn-primary btn-small" onclick="copyCode('tacCodeValue')">Copy</button>
                        <button type="button" class="btn btn-success btn-small" onclick="regenerateTacCode()">Regenerate</button>
                    </div>
                </div>
            </div>

            <!-- TIN -->
            <div style="margin-top: 14px;">
                <div class="toggle-row">
                    <div class="toggle-meta">
                        <div class="title">TIN Requirement</div>
                        <div class="desc">If enabled, user must provide the TIN — Tax Identification Number (provided by support via live chat).</div>
                    </div>
                    <label class="switch" title="Toggle TIN requirement">
                        <input id="tinRequiredToggle" type="checkbox" <?php echo ((int)($user['tin_required'] ?? 0) === 1) ? 'checked' : ''; ?> onchange="updateTinRequired()">
                        <span class="slider"></span>
                    </label>
                </div>
                <div class="code-row" style="margin-top: 10px;">
                    <?php $tinCode = (string)($user['tin_code'] ?? ''); ?>
                    <?php $tinMasked = $tinCode ? (str_repeat('•', max(0, strlen($tinCode) - 4)) . substr($tinCode, -4)) : 'Not set'; ?>
                    <div>
                        <div style="font-weight: 700; color: #0f172a; margin-bottom: 6px;">TIN</div>
                        <div class="code-value" id="tinCodeValue" data-code="<?php echo htmlspecialchars($tinCode); ?>" data-masked="<?php echo htmlspecialchars($tinMasked); ?>"><?php echo htmlspecialchars($tinMasked); ?></div>
                    </div>
                    <div class="code-actions">
                        <button type="button" class="btn btn-warning btn-small" onclick="toggleCodeVisibility('tinCodeValue')">Show</button>
                        <button type="button" class="btn btn-primary btn-small" onclick="copyCode('tinCodeValue')">Copy</button>
                        <button type="button" class="btn btn-success btn-small" onclick="regenerateTinCode()">Regenerate</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <h3>
                <div class="status-icon">💰</div>
                Quick Actions
            </h3>
            
            <div class="status-info">
                <h4>User Balance Management</h4>
                <p>Create custom transactions for this user with full control over amount, type, and status.</p>
            </div>
            
            <button type="button" class="btn btn-primary" onclick="openBalanceModal()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke="currentColor" stroke-width="2"/>
                </svg>
                Update User Balance
            </button>
        </div>
    </div>
</div>

<!-- Balance Update Modal -->
<div id="balanceModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Update User Balance</h3>
            <span class="modal-close" onclick="closeBalanceModal()">&times;</span>
        </div>
        
        <div class="modal-body">
            <form id="balanceForm">
                <div class="form-group">
                    <label class="form-label" for="targetAccountStatus">User Account</label>
                    <select id="targetAccountStatus" class="status-select" required>
                        <option value="">Select user account</option>
                        <!-- Accounts will be loaded dynamically -->
                    </select>
                    <small style="color: #6b7280; font-size: 12px; display: block; margin-top: 4px;">Select which account to update</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="balanceAmount">Amount</label>
                    <input type="number" id="balanceAmount" class="form-input" step="0.01" min="0.01" required placeholder="Enter amount">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="transactionType">Transaction Type</label>
                    <select id="transactionType" class="status-select" required onchange="updateTransactionFields()">
                        <option value="">Select transaction type</option>
                        <option value="transfer">Transfer</option>
                        <option value="deposit">Deposit</option>
                        <option value="withdrawal">Withdrawal</option>
                        <option value="fee">Fee</option>
                        <option value="interest">Interest</option>
                        <option value="refund">Refund</option>
                        <option value="bonus">Bonus</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="transactionDirection">Direction</label>
                    <select id="transactionDirection" class="status-select" required>
                        <option value="credit">Credit (Add money)</option>
                        <option value="debit">Debit (Subtract money)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="transactionMethod">Transaction Method</label>
                    <select id="transactionMethod" class="status-select" required onchange="updateMethodFields()">
                        <option value="">Select method</option>
                        <option value="internal">Internal Transfer</option>
                        <option value="card">Card Transaction</option>
                        <option value="domestic">Domestic Transfer</option>
                    </select>
                </div>
                
                <div id="methodSpecificFields" style="display: none;">
                    <!-- Fields will be populated based on method selection -->
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="transactionCategory">Category</label>
                    <select id="transactionCategory" class="status-select" required>
                        <option value="">Select category</option>
                        <option value="General">General</option>
                        <option value="Food & Dining">Food & Dining</option>
                        <option value="Shopping">Shopping</option>
                        <option value="Transportation">Transportation</option>
                        <option value="Entertainment">Entertainment</option>
                        <option value="Bills & Utilities">Bills & Utilities</option>
                        <option value="Healthcare">Healthcare</option>
                        <option value="Travel">Travel</option>
                        <option value="Education">Education</option>
                        <option value="Business">Business</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="transactionDate">Transaction Date & Time</label>
                    <input type="datetime-local" id="transactionDate" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="transactionStatus">Transaction Status</label>
                    <select id="transactionStatus" class="status-select" required>
                        <option value="completed">✅ Completed</option>
                        <option value="pending">⏳ Pending</option>
                        <option value="failed">❌ Failed</option>
                        <option value="on_hold">🟣 On Hold</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="transactionDescription">Description</label>
                    <textarea id="transactionDescription" class="form-input" rows="3" placeholder="Enter transaction description"></textarea>
                </div>
            </form>
        </div>
        
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeBalanceModal()">Cancel</button>
            <button type="button" class="btn btn-primary" onclick="submitBalanceUpdate()">Update Balance</button>
        </div>
    </div>
</div>

<script>
// Set current datetime as default
document.getElementById('transactionDate').value = new Date().toISOString().slice(0, 16);

// Safe toast helper (falls back to alert if showToast is not available)
function _toast(message, type) {
    try {
        if (typeof showToast === 'function') {
            showToast(message, type);
        } else {
            alert(message);
        }
    } catch (e) {
        alert(message);
    }
}

// ===== KYC Status Controls =====
let lastKycStatus = '<?php echo $user['kyc_status'] ?? 'pending'; ?>';

function onKycStatusChange() {
    const v = document.getElementById('kycStatus') ? document.getElementById('kycStatus').value : 'pending';
    const wrap = document.getElementById('kycRejectReasonWrap');
    if (wrap) wrap.style.display = (v === 'rejected') ? 'block' : 'none';
}

function updateKycStatus() {
    const userId = <?php echo $userId; ?>;
    const kycStatusEl = document.getElementById('kycStatus');
    if (!kycStatusEl) return;

    const kycStatus = kycStatusEl.value;
    const reasonEl = document.getElementById('kycRejectReason');
    const notesEl = document.getElementById('kycNotes');
    const activateEl = document.getElementById('activateOnVerify');

    const reason = (kycStatus === 'rejected') ? ((reasonEl && reasonEl.value) ? reasonEl.value : '') : '';
    const notes = (notesEl && notesEl.value) ? notesEl.value : '';
    const activate = !!(activateEl && activateEl.checked);

    if (kycStatus === 'rejected' && !reason.trim()) {
        alert('Rejection reason is required');
        if (reasonEl) reasonEl.focus();
        return;
    }

    _toast('Updating KYC status...', 'info');

    fetch('/api/admin-set-kyc-status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            user_id: userId,
            kyc_status: kycStatus,
            reason: reason,
            notes: notes,
            activate_account: activate
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            lastKycStatus = kycStatus;
            _toast('KYC status updated successfully', 'success');
        } else {
            _toast('Error: ' + (data.message || 'Failed to update KYC status'), 'error');
            kycStatusEl.value = lastKycStatus;
            onKycStatusChange();
        }
    })
    .catch(err => {
        console.error(err);
        _toast('An error occurred while updating KYC status', 'error');
        kycStatusEl.value = lastKycStatus;
        onKycStatusChange();
    });
}

function verifyUserEmail() {
    const userId = <?php echo $userId; ?>;
    
    if (!confirm('Are you sure you want to manually verify this user\'s email address?')) {
        return;
    }
    
    _toast('Verifying email...', 'info');
    
    fetch('/api/admin-verify-email.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            user_id: userId
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            _toast('Email verified successfully', 'success');
            // Reload page after 1 second to show updated status
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            _toast('Error: ' + (data.message || 'Failed to verify email'), 'error');
        }
    })
    .catch(err => {
        console.error(err);
        _toast('An error occurred while verifying email', 'error');
    });
}

function updateAccountStatus() {
    const status = document.getElementById('accountStatus').value;
    const userId = <?php echo $userId; ?>;
    
    _toast('Updating account status...', 'info');
    
    fetch('/api/admin-set-account-status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            user_id: userId,
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            _toast('Account status updated successfully', 'success');
        } else {
            _toast('Error: ' + data.message, 'error');
            // Revert selection
            document.getElementById('accountStatus').value = '<?php echo $user['status']; ?>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        _toast('An error occurred while updating account status', 'error');
        // Revert selection
        document.getElementById('accountStatus').value = '<?php echo $user['status']; ?>';
    });
}

function updateTransactionOverride() {
    const override = document.getElementById('transactionOverride').value;
    const userId = <?php echo $userId; ?>;
    
    _toast('Updating transaction override...', 'info');
    
    fetch('/api/set-transaction-mode.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            user_id: userId,
            mode: override
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            _toast('Transaction override updated successfully', 'success');
        } else {
            _toast('Error: ' + data.message, 'error');
            // Revert selection
            document.getElementById('transactionOverride').value = '<?php echo $user['transaction_override'] ?? 'normal'; ?>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        _toast('An error occurred while updating transaction override', 'error');
        // Revert selection
        document.getElementById('transactionOverride').value = '<?php echo $user['transaction_override'] ?? 'normal'; ?>';
    });
}

function toggleCodeVisibility(elementId) {
    const el = document.getElementById(elementId);
    if (!el) return;
    const code = el.getAttribute('data-code') || '';
    const masked = el.getAttribute('data-masked') || '';
    const isShowing = (el.textContent || '').trim() === code;
    el.textContent = isShowing ? masked : (code || masked);
}

async function copyCode(elementId) {
    const el = document.getElementById(elementId);
    if (!el) return;
    const code = el.getAttribute('data-code') || '';
    if (!code) {
        _toast('Code not set yet. Use Regenerate.', 'warning');
        return;
    }
    try {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            await navigator.clipboard.writeText(code);
        } else {
            // Fallback for older browsers
            const tmp = document.createElement('textarea');
            tmp.value = code;
            document.body.appendChild(tmp);
            tmp.select();
            document.execCommand('copy');
            document.body.removeChild(tmp);
        }
        _toast('Copied to clipboard', 'success');
    } catch (e) {
        _toast('Failed to copy code', 'error');
    }
}

function updateTransferOtpRequired() {
    const userId = <?php echo $userId; ?>;
    const enabled = document.getElementById('transferOtpToggle').checked ? 1 : 0;
    _toast('Updating transfer OTP requirement...', 'info');
    fetch('/api/admin-set-transfer-otp.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ user_id: userId, enabled })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            _toast(data.message || 'Updated', 'success');
        } else {
            _toast('Error: ' + (data.message || 'Failed'), 'error');
            document.getElementById('transferOtpToggle').checked = <?php echo ((int)($user['transfer_otp_required'] ?? 1) === 1) ? 'true' : 'false'; ?>;
        }
    })
    .catch(err => {
        console.error(err);
        _toast('An error occurred while updating transfer OTP', 'error');
        document.getElementById('transferOtpToggle').checked = <?php echo ((int)($user['transfer_otp_required'] ?? 1) === 1) ? 'true' : 'false'; ?>;
    });
}

function updateImfRequired() {
    const userId = <?php echo $userId; ?>;
    const required = document.getElementById('imfRequiredToggle').checked ? 1 : 0;
    _toast('Updating IMF requirement...', 'info');
    fetch('/api/admin-toggle-imf.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ user_id: userId, required })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            _toast(data.message || 'Updated', 'success');
        } else {
            _toast('Error: ' + (data.message || 'Failed'), 'error');
            document.getElementById('imfRequiredToggle').checked = <?php echo ((int)($user['imf_required'] ?? 0) === 1) ? 'true' : 'false'; ?>;
        }
    })
    .catch(err => {
        console.error(err);
        _toast('An error occurred while updating IMF requirement', 'error');
        document.getElementById('imfRequiredToggle').checked = <?php echo ((int)($user['imf_required'] ?? 0) === 1) ? 'true' : 'false'; ?>;
    });
}

function regenerateImfCode() {
    const userId = <?php echo $userId; ?>;
    _toast('Regenerating IMF code...', 'info');
    fetch('/api/admin-regenerate-imf.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ user_id: userId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const el = document.getElementById('imfCodeValue');
            if (el && data.code) {
                const code = String(data.code);
                const masked = code.length > 4 ? ('•'.repeat(code.length - 4) + code.slice(-4)) : code;
                el.setAttribute('data-code', code);
                el.setAttribute('data-masked', masked);
                el.textContent = masked;
            }
            _toast('IMF code regenerated', 'success');
        } else {
            _toast('Error: ' + (data.message || 'Failed'), 'error');
        }
    })
    .catch(err => {
        console.error(err);
        _toast('An error occurred while regenerating IMF code', 'error');
    });
}

function updateFederalSwiftRequired() {
    const userId = <?php echo $userId; ?>;
    const required = document.getElementById('federalSwiftRequiredToggle').checked ? 1 : 0;
    _toast('Updating Federal SWIFT requirement...', 'info');
    fetch('/api/admin-toggle-federal-swift.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ user_id: userId, required })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            _toast(data.message || 'Updated', 'success');
        } else {
            _toast('Error: ' + (data.message || 'Failed'), 'error');
            document.getElementById('federalSwiftRequiredToggle').checked = <?php echo ((int)($user['federal_swift_required'] ?? 0) === 1) ? 'true' : 'false'; ?>;
        }
    })
    .catch(err => {
        console.error(err);
        _toast('An error occurred while updating Federal SWIFT requirement', 'error');
        document.getElementById('federalSwiftRequiredToggle').checked = <?php echo ((int)($user['federal_swift_required'] ?? 0) === 1) ? 'true' : 'false'; ?>;
    });
}

function regenerateFederalSwiftCode() {
    const userId = <?php echo $userId; ?>;
    _toast('Regenerating Federal SWIFT code...', 'info');
    fetch('/api/admin-regenerate-federal-swift.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ user_id: userId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const el = document.getElementById('federalSwiftCodeValue');
            if (el && data.code) {
                const code = String(data.code);
                const masked = code.length > 4 ? ('•'.repeat(code.length - 4) + code.slice(-4)) : code;
                el.setAttribute('data-code', code);
                el.setAttribute('data-masked', masked);
                el.textContent = masked;
            }
            _toast('Federal SWIFT code regenerated', 'success');
        } else {
            _toast('Error: ' + (data.message || 'Failed'), 'error');
        }
    })
    .catch(err => {
        console.error(err);
        _toast('An error occurred while regenerating Federal SWIFT code', 'error');
    });
}

function updateVatRequired() {
    const userId = <?php echo $userId; ?>;
    const required = document.getElementById('vatRequiredToggle').checked ? 1 : 0;
    _toast('Updating VAT requirement...', 'info');
    fetch('/api/admin-toggle-vat.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ user_id: userId, required })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            _toast(data.message || 'Updated', 'success');
        } else {
            _toast('Error: ' + (data.message || 'Failed'), 'error');
            document.getElementById('vatRequiredToggle').checked = <?php echo ((int)($user['vat_required'] ?? 0) === 1) ? 'true' : 'false'; ?>;
        }
    })
    .catch(err => {
        console.error(err);
        _toast('An error occurred while updating VAT requirement', 'error');
        document.getElementById('vatRequiredToggle').checked = <?php echo ((int)($user['vat_required'] ?? 0) === 1) ? 'true' : 'false'; ?>;
    });
}

function regenerateVatCode() {
    const userId = <?php echo $userId; ?>;
    _toast('Regenerating VAT code...', 'info');
    fetch('/api/admin-regenerate-vat.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ user_id: userId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const el = document.getElementById('vatCodeValue');
            if (el && data.code) {
                const code = String(data.code);
                const masked = code.length > 4 ? ('•'.repeat(code.length - 4) + code.slice(-4)) : code;
                el.setAttribute('data-code', code);
                el.setAttribute('data-masked', masked);
                el.textContent = masked;
            }
            _toast('VAT code regenerated', 'success');
        } else {
            _toast('Error: ' + (data.message || 'Failed'), 'error');
        }
    })
    .catch(err => {
        console.error(err);
        _toast('An error occurred while regenerating VAT code', 'error');
    });
}

function updateTacRequired() {
    const userId = <?php echo $userId; ?>;
    const required = document.getElementById('tacRequiredToggle').checked ? 1 : 0;
    _toast('Updating TAC requirement...', 'info');
    fetch('/api/admin-toggle-tac.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ user_id: userId, required })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            _toast(data.message || 'Updated', 'success');
        } else {
            _toast('Error: ' + (data.message || 'Failed'), 'error');
            document.getElementById('tacRequiredToggle').checked = <?php echo ((int)($user['tac_required'] ?? 0) === 1) ? 'true' : 'false'; ?>;
        }
    })
    .catch(err => {
        console.error(err);
        _toast('An error occurred while updating TAC requirement', 'error');
        document.getElementById('tacRequiredToggle').checked = <?php echo ((int)($user['tac_required'] ?? 0) === 1) ? 'true' : 'false'; ?>;
    });
}

function regenerateTacCode() {
    const userId = <?php echo $userId; ?>;
    _toast('Regenerating TAC...', 'info');
    fetch('/api/admin-regenerate-tac.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ user_id: userId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const el = document.getElementById('tacCodeValue');
            if (el && data.code) {
                const code = String(data.code);
                const masked = code.length > 4 ? ('•'.repeat(code.length - 4) + code.slice(-4)) : code;
                el.setAttribute('data-code', code);
                el.setAttribute('data-masked', masked);
                el.textContent = masked;
            }
            _toast('TAC regenerated', 'success');
        } else {
            _toast('Error: ' + (data.message || 'Failed'), 'error');
        }
    })
    .catch(err => {
        console.error(err);
        _toast('An error occurred while regenerating TAC', 'error');
    });
}

function updateTinRequired() {
    const userId = <?php echo $userId; ?>;
    const required = document.getElementById('tinRequiredToggle').checked ? 1 : 0;
    _toast('Updating TIN requirement...', 'info');
    fetch('/api/admin-toggle-tin.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ user_id: userId, required })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            _toast(data.message || 'Updated', 'success');
        } else {
            _toast('Error: ' + (data.message || 'Failed'), 'error');
            document.getElementById('tinRequiredToggle').checked = <?php echo ((int)($user['tin_required'] ?? 0) === 1) ? 'true' : 'false'; ?>;
        }
    })
    .catch(err => {
        console.error(err);
        _toast('An error occurred while updating TIN requirement', 'error');
        document.getElementById('tinRequiredToggle').checked = <?php echo ((int)($user['tin_required'] ?? 0) === 1) ? 'true' : 'false'; ?>;
    });
}

function regenerateTinCode() {
    const userId = <?php echo $userId; ?>;
    _toast('Regenerating TIN...', 'info');
    fetch('/api/admin-regenerate-tin.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ user_id: userId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const el = document.getElementById('tinCodeValue');
            if (el && data.code) {
                const code = String(data.code);
                const masked = code.length > 4 ? ('•'.repeat(code.length - 4) + code.slice(-4)) : code;
                el.setAttribute('data-code', code);
                el.setAttribute('data-masked', masked);
                el.textContent = masked;
            }
            _toast('TIN regenerated', 'success');
        } else {
            _toast('Error: ' + (data.message || 'Failed'), 'error');
        }
    })
    .catch(err => {
        console.error(err);
        _toast('An error occurred while regenerating TIN', 'error');
    });
}

function openBalanceModal() {
    document.getElementById('balanceModal').style.display = 'flex';
    // Load user accounts when modal opens (small delay to ensure DOM is ready)
    setTimeout(() => {
        loadUserAccountsForStatus();
    }, 100);
}

function loadUserAccountsForStatus() {
    const userId = <?php echo $userId; ?>;
    
    // Verify the select element exists
    const accountSelect = document.getElementById('targetAccountStatus');
    if (!accountSelect) {
        console.error('targetAccountStatus select element not found in DOM');
        return;
    }
    
    // Show loading state
    accountSelect.innerHTML = '<option value="">Loading accounts...</option>';
    accountSelect.disabled = true;
    
    fetch('<?php echo SITE_URL; ?>/api/get-user-accounts.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            user_id: userId
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text();
    })
    .then(text => {
        try {
            const data = JSON.parse(text);
            
            if (!accountSelect) {
                console.error('targetAccountStatus select element not found after fetch');
                return;
            }
            
            accountSelect.disabled = false;
            accountSelect.innerHTML = '<option value="">Select user account</option>';
            
            if (data.success && data.accounts && Array.isArray(data.accounts)) {
                if (data.accounts.length === 0) {
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'No active accounts found';
                    accountSelect.appendChild(option);
                    console.warn('No active accounts found for user:', userId);
                } else {
                    // Loop through ALL accounts and add each one
                    data.accounts.forEach((account, index) => {
                        const option = document.createElement('option');
                        option.value = account.id;
                        const balance = parseFloat(account.balance || 0);
                        const balanceFormatted = new Intl.NumberFormat('en-US', {
                            style: 'currency',
                            currency: 'USD',
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }).format(balance);
                        option.textContent = `${account.account_type || 'Account'} - ${account.account_number || 'N/A'} (${balanceFormatted})`;
                        accountSelect.appendChild(option);
                    });
                }
            } else {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = data.message || 'No accounts found';
                accountSelect.appendChild(option);
                console.error('API returned unsuccessful response:', data);
            }
        } catch (e) {
            console.error('JSON parse error:', e);
            console.error('Response text:', text);
            if (accountSelect) {
                accountSelect.disabled = false;
                accountSelect.innerHTML = '<option value="">Error loading accounts</option>';
            }
        }
    })
    .catch(error => {
        console.error('Error loading accounts:', error);
        if (accountSelect) {
            accountSelect.disabled = false;
            accountSelect.innerHTML = '<option value="">Error loading accounts</option>';
        }
    });
}

function closeBalanceModal() {
    document.getElementById('balanceModal').style.display = 'none';
    document.getElementById('balanceForm').reset();
    document.getElementById('methodSpecificFields').style.display = 'none';
    document.getElementById('transactionDate').value = new Date().toISOString().slice(0, 16);
    // Reset account selector
    const accountSelect = document.getElementById('targetAccountStatus');
    if (accountSelect) {
        accountSelect.innerHTML = '<option value="">Select user account</option>';
        accountSelect.disabled = false;
    }
}

function updateTransactionFields() {
    const type = document.getElementById('transactionType').value;
    // Update category options based on transaction type
    updateMethodFields();
}

function updateMethodFields() {
    const method = document.getElementById('transactionMethod').value;
    const methodFields = document.getElementById('methodSpecificFields');
    
    if (method === 'internal') {
        methodFields.innerHTML = `
            <div class="form-group">
                <label class="form-label" for="recipientAccount">Recipient Account</label>
                <input type="text" id="recipientAccount" class="form-input" placeholder="Enter recipient account number">
            </div>
        `;
        methodFields.style.display = 'block';
    } else if (method === 'card') {
        methodFields.innerHTML = `
            <div class="form-group">
                <label class="form-label" for="cardNumber">Card Number</label>
                <input type="text" id="cardNumber" class="form-input" placeholder="Enter card number (last 4 digits)">
            </div>
            <div class="form-group">
                <label class="form-label" for="merchant">Merchant</label>
                <input type="text" id="merchant" class="form-input" placeholder="Enter merchant name">
            </div>
        `;
        methodFields.style.display = 'block';
    } else if (method === 'domestic') {
        methodFields.innerHTML = `
            <div class="form-group">
                <label class="form-label" for="bankName">Bank Name</label>
                <input type="text" id="bankName" class="form-input" placeholder="Enter bank name">
            </div>
            <div class="form-group">
                <label class="form-label" for="accountNumber">Account Number</label>
                <input type="text" id="accountNumber" class="form-input" placeholder="Enter account number">
            </div>
            <div class="form-group">
                <label class="form-label" for="routingNumber">Routing Number</label>
                <input type="text" id="routingNumber" class="form-input" placeholder="Enter routing number">
            </div>
        `;
        methodFields.style.display = 'block';
    } else {
        methodFields.style.display = 'none';
    }
}

function submitBalanceUpdate() {
    const form = document.getElementById('balanceForm');
    const formData = new FormData(form);
    
    // Get account ID
    const targetAccount = document.getElementById('targetAccountStatus').value;
    if (!targetAccount || targetAccount === '') {
        showToast('Please select a user account', 'error');
        return;
    }
    
    // Validate amount
    const amount = parseFloat(document.getElementById('balanceAmount').value);
    if (isNaN(amount) || amount <= 0) {
        showToast('Please enter a valid amount greater than 0', 'error');
        return;
    }
    
    // Parse date and time from datetime-local input
    const dateTimeValue = document.getElementById('transactionDate').value;
    let transactionDate = '';
    let transactionTime = '';
    if (dateTimeValue) {
        const dateTime = new Date(dateTimeValue);
        transactionDate = dateTime.toISOString().split('T')[0];
        transactionTime = dateTime.toTimeString().slice(0, 8);
    }
    
    const data = {
        user_id: <?php echo $userId; ?>,
        account_id: parseInt(targetAccount),
        amount: amount,
        transaction_type: document.getElementById('transactionDirection').value, // credit or debit
        transaction_method: document.getElementById('transactionMethod').value,
        expense_category: document.getElementById('transactionCategory').value,
        transaction_date: transactionDate || new Date().toISOString().split('T')[0],
        transaction_time: transactionTime || new Date().toTimeString().slice(0, 8),
        status: document.getElementById('transactionStatus').value,
        description: document.getElementById('transactionDescription').value || 'Admin balance update'
    };
    
    // Add method-specific fields
    const method = document.getElementById('transactionMethod').value;
    if (method === 'internal') {
        data.recipient_account = document.getElementById('recipientAccount')?.value || '';
        data.recipient_name = 'Internal Transfer'; // Required for internal transfers
    } else if (method === 'card') {
        data.card_number = document.getElementById('cardNumber')?.value || '';
        data.merchant = document.getElementById('merchant')?.value || '';
    } else if (method === 'domestic') {
        data.recipient_bank = document.getElementById('bankName')?.value || '';
        data.recipient_account = document.getElementById('accountNumber')?.value || '';
        data.recipient_name = 'Domestic Transfer'; // Required for domestic transfers
    }
    
    showToast('Processing balance update...', 'info');
    
    fetch('<?php echo SITE_URL; ?>/api/admin-adjust-balance.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text();
    })
    .then(text => {
        try {
            const data = JSON.parse(text);
            if (data.success) {
                showToast('Balance updated successfully', 'success');
                closeBalanceModal();
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showToast('Error: ' + data.message, 'error');
            }
        } catch (e) {
            console.error('JSON parse error:', e);
            console.error('Response text:', text);
            showToast('Error parsing server response', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred while updating balance', 'error');
    });
}

// Initialize KYC reject reason visibility on load
onKycStatusChange();

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('balanceModal');
    if (event.target === modal) {
        closeBalanceModal();
    }
}
</script>