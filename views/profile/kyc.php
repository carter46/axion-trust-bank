<?php 
$pageTitle = 'KYC Verification - ' . getSiteName();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../models/Kyc.php';

require_once __DIR__ . '/../../includes/kyc-config.php';

// Include head
include __DIR__ . '/../../includes/head.php';

// Include sidebar and main structure
include __DIR__ . '/../../includes/sidebar.php';

requireLogin();

// Get user data for auto-fill
$userModel = new User();
$user = $userModel->findById($_SESSION['user_id']);

$kycModel = new Kyc();
$existingKyc = $kycModel->findByUserId($_SESSION['user_id']);

// Ensure $existingKyc is an array
if (!is_array($existingKyc)) {
    $existingKyc = [];
}

// Display success/error messages
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-error">' . htmlspecialchars($_SESSION['error']) . '</div>';
    unset($_SESSION['error']);
}

// Helper functions
function safeFormValue($data, $key, $default = '') {
    if (!is_array($data) || !isset($data[$key]) || $data[$key] === null) {
        return $default;
    }
    return htmlspecialchars((string)$data[$key], ENT_QUOTES, 'UTF-8');
}

$kycConfig = getKycFieldsForUser($_SESSION['user_id']);
$kycProfile = $kycConfig['profile'];
$kycLabels = getKycFieldLabels($_SESSION['user_id']);
$kycExtraFields = getKycExtraFieldsFromRecord($existingKyc);
$showGovIdStep = $kycConfig['show_government_id_step'];
$totalWizardSteps = $showGovIdStep ? 5 : 4;
$stepLabels = $showGovIdStep
    ? ['Personal Info', 'Government ID', 'Documents', 'Compliance', 'Review']
    : ['Personal Info', 'Documents', 'Compliance', 'Review'];

$kycReviewSections = [
    ['title' => 'Personal Information', 'fields' => ['full_legal_name', 'date_of_birth']],
];
if (!$kycConfig['use_custom']) {
    $kycReviewSections[0]['fields'][] = 'ssn';
    $kycReviewSections[0]['fields'] = array_merge($kycReviewSections[0]['fields'], [
        'residential_address', 'residential_city', 'residential_state', 'residential_zip', 'residential_country',
    ]);
} else {
    foreach ($kycConfig['custom_fields'] as $field) {
        if (($field['step'] ?? 'personal') === 'personal' && ($field['type'] ?? 'text') !== 'file') {
            $kycReviewSections[0]['fields'][] = $field['key'];
        }
    }
}
if ($showGovIdStep) {
    $kycReviewSections[] = [
        'title' => 'Government ID',
        'fields' => ['id_type', 'id_number', 'id_issued_date', 'id_expiry_date', 'id_issued_state', 'id_issued_country'],
    ];
}
$kycReviewSections[] = [
    'title' => 'Compliance',
    'fields' => ['source_of_funds', 'account_purpose'],
];
if ($kycConfig['use_custom']) {
    foreach ($kycConfig['custom_fields'] as $field) {
        if (($field['step'] ?? 'personal') === 'compliance' && ($field['type'] ?? 'text') !== 'file') {
            $kycReviewSections[count($kycReviewSections) - 1]['fields'][] = $field['key'];
        }
    }
}

function kycFormValue($data, $extra, $key, $default = '') {
    $val = getKycFieldValue(is_array($data) ? $data : [], $key, is_array($extra) ? $extra : []);
    if ($val === '' || $val === null) {
        return htmlspecialchars((string)$default, ENT_QUOTES, 'UTF-8');
    }
    if ($key === 'ssn' && $val === '[On file]') {
        return '';
    }
    return htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8');
}

function safeFormDate($data, $key, $default = '') {
    if (!is_array($data) || !isset($data[$key]) || $data[$key] === null || empty($data[$key])) {
        return $default;
    }
    $dateValue = (string)$data[$key];
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateValue)) {
        return htmlspecialchars($dateValue, ENT_QUOTES, 'UTF-8');
    }
    $timestamp = strtotime($dateValue);
    if ($timestamp !== false) {
        return htmlspecialchars(date('Y-m-d', $timestamp), ENT_QUOTES, 'UTF-8');
    }
    return $default;
}
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

.status-card {
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    color: white;
    padding: 24px;
    border-radius: 16px;
    margin-bottom: 30px;
}

.status-badge {
    display: inline-block;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 600;
    margin-top: 12px;
}

.status-pending { background: #fef3c7; color: #92400e; }
.status-verified { background: #d1fae5; color: #065f46; }
.status-rejected { background: #fee2e2; color: #991b1b; }

/* Multi-step wizard */
.kyc-wizard {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 24px;
    padding: 40px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.steps-indicator {
    display: flex;
    justify-content: space-between;
    margin-bottom: 40px;
    position: relative;
    padding: 0 20px;
}

.steps-indicator::before {
    content: '';
    position: absolute;
    top: 20px;
    left: 40px;
    right: 40px;
    height: 3px;
    background: #e0e0e0;
    z-index: 0;
}

.step {
    position: relative;
    z-index: 1;
    text-align: center;
    flex: 1;
}

.step-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e0e0e0;
    color: #666;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    margin: 0 auto 8px;
    transition: all 0.3s;
}

.step.active .step-circle {
    background: #1e3a8a;
    color: white;
    transform: scale(1.1);
}

.step.completed .step-circle {
    background: #10b981;
    color: white;
}

.step-label {
    font-size: 12px;
    font-weight: 600;
    color: #666;
}

.step.active .step-label {
    color: #1e3a8a;
}

.step-content {
    display: none;
    animation: fadeIn 0.3s;
}

.step-content.active {
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.form-group {
    margin-bottom: 24px;
}

.form-label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    color: #032B44;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    font-size: 16px;
    transition: all 0.3s;
}

.form-control:focus {
    outline: none;
    border-color: #1e3a8a;
    box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.wizard-actions {
    display: flex;
    justify-content: space-between;
    margin-top: 40px;
    padding-top: 30px;
    border-top: 2px solid #e0e0e0;
}

.btn {
    padding: 12px 32px;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-primary {
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(30, 58, 138, 0.3);
}

.btn-secondary {
    background: #f3f4f6;
    color: #374151;
}

.btn-secondary:hover {
    background: #e5e7eb;
}

.btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
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

.auto-filled-badge {
    display: inline-block;
    padding: 4px 12px;
    background: #d1fae5;
    color: #065f46;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    margin-left: 8px;
}

.file-preview {
    margin-top: 12px;
    display: none;
}

.file-preview.active {
    display: block;
}

.file-preview img {
    max-width: 200px;
    max-height: 140px;
    border-radius: 8px;
    border: 2px solid #e0e0e0;
    object-fit: cover;
}

.file-preview .pdf-label {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 14px;
    background: #f3f4f6;
    border-radius: 8px;
    color: #374151;
    font-size: 14px;
}

.review-summary {
    background: #f8fafc;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
}

.review-summary h4 {
    color: #032B44;
    margin: 0 0 12px 0;
    font-size: 16px;
}

.review-row {
    display: flex;
    justify-content: space-between;
    gap: 16px;
    padding: 8px 0;
    border-bottom: 1px solid #e5e7eb;
    font-size: 14px;
}

.review-row:last-child {
    border-bottom: none;
}

.review-label {
    color: #666;
    font-weight: 600;
    min-width: 140px;
}

.review-value {
    color: #032B44;
    text-align: right;
    word-break: break-word;
}

.review-docs {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 8px;
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 12px 24px;
}

.summary-item {
    margin: 0;
    font-size: 14px;
    line-height: 1.6;
}

@media (max-width: 768px) {
    .kyc-wizard {
        padding: 20px;
    }
    
    .steps-indicator {
        flex-direction: column;
        gap: 20px;
    }
    
    .steps-indicator::before {
        display: none;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<a href="<?php echo SITE_URL; ?>/profile" class="back-button">
    <i class="fas fa-arrow-left"></i> Back to Profile
</a>

<div class="page-header">
    <h1>
        <i class="fas fa-id-card"></i>
        KYC Verification
    </h1>
    <p style="color: #666;"><?php echo htmlspecialchars($kycConfig['subtitle']); ?></p>
</div>

<?php 
// Determine if KYC exists and can be edited
$hasKyc = is_array($existingKyc) && !empty($existingKyc);
$kycStatus = $hasKyc ? ($existingKyc['status'] ?? '') : '';

// Only show form if:
// 1. No KYC exists (new submission), OR
// 2. KYC status is 'rejected' (can resubmit)
// Do NOT show form if status is 'pending', 'under_review', or 'requires_action' (already submitted, waiting for review)
$canEdit = !$hasKyc || $kycStatus === 'rejected';
$isVerified = $kycStatus === 'verified';
$isPending = in_array($kycStatus, ['pending', 'under_review', 'requires_action']);
?>

<?php if ($hasKyc): ?>
<div class="status-card">
    <h3 style="margin: 0 0 8px 0; color: white;">Verification Status</h3>
    <p style="margin: 0; opacity: 0.9;">
        <?php if ($isPending): ?>
            Your KYC verification has been submitted and is under review. Please wait for admin approval.
        <?php elseif ($isVerified): ?>
            Your KYC verification has been approved.
        <?php elseif ($kycStatus === 'rejected'): ?>
            Your KYC verification was rejected. Please review the reason below and resubmit.
        <?php else: ?>
            Your KYC verification has been submitted. Current status:
        <?php endif; ?>
    </p>
    <span class="status-badge status-<?php echo $kycStatus; ?>">
        <?php echo ucfirst(str_replace('_', ' ', $kycStatus)); ?>
    </span>
    <?php if (isset($existingKyc['rejection_reason']) && $existingKyc['rejection_reason'] !== null && $existingKyc['rejection_reason'] !== ''): ?>
        <p style="margin: 12px 0 0 0; opacity: 0.9;"><strong>Reason:</strong> <?php echo htmlspecialchars((string)$existingKyc['rejection_reason'], ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>
    
    <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.2);">
        <h4 style="margin: 0 0 12px 0; color: white; font-size: 16px;">Submission Summary</h4>
        <div style="background: rgba(255,255,255,0.1); padding: 16px; border-radius: 12px; font-size: 14px; line-height: 1.8;">
            <div class="summary-grid">
                <p class="summary-item"><strong>Name:</strong> <?php echo htmlspecialchars($existingKyc['full_legal_name'] ?? 'N/A'); ?></p>
                <?php if (!empty($existingKyc['date_of_birth'])): ?>
                <p class="summary-item"><strong>Date of Birth:</strong> <?php echo date('F j, Y', strtotime($existingKyc['date_of_birth'])); ?></p>
                <?php endif; ?>
                <?php if (!$kycConfig['use_custom'] && !empty($existingKyc['residential_address'])): ?>
                <p class="summary-item"><strong>Address:</strong> <?php echo htmlspecialchars(trim(($existingKyc['residential_address'] ?? '') . ', ' . ($existingKyc['residential_city'] ?? '') . ', ' . ($existingKyc['residential_state'] ?? '') . ' ' . ($existingKyc['residential_zip'] ?? ''), ', ')); ?></p>
                <?php endif; ?>
                <?php if (!$kycConfig['use_custom'] && !empty($existingKyc['id_type'])): ?>
                <p class="summary-item"><strong>ID Type:</strong> <?php echo htmlspecialchars(formatKycIdTypeLabel($existingKyc['id_type'], $_SESSION['user_id'])); ?></p>
                <?php endif; ?>
                <?php if (!empty($existingKyc['ssn'])): ?>
                <p class="summary-item"><strong><?php echo htmlspecialchars($kycProfile['identity_label']); ?>:</strong> On file</p>
                <?php endif; ?>
                <?php foreach ($kycExtraFields as $efKey => $efVal): ?>
                    <?php if (!empty($efVal) && !preg_match('/\.(jpg|jpeg|png|gif|webp|pdf)$/i', (string)$efVal)): ?>
                    <p class="summary-item"><strong><?php echo htmlspecialchars($kycLabels[$efKey] ?? ucwords(str_replace('_', ' ', $efKey))); ?>:</strong> <?php echo htmlspecialchars((string)$efVal); ?></p>
                    <?php endif; ?>
                <?php endforeach; ?>
                <?php if (!empty($existingKyc['source_of_funds'])): ?>
                <p class="summary-item"><strong>Source of Funds:</strong> <?php echo htmlspecialchars(mb_strimwidth($existingKyc['source_of_funds'], 0, 80, '...')); ?></p>
                <?php endif; ?>
                <p class="summary-item"><strong>Submitted:</strong> <?php echo date('F j, Y g:i A', strtotime($existingKyc['submitted_at'] ?? 'now')); ?></p>
                <?php if (!empty($existingKyc['verified_at'])): ?>
                <p class="summary-item"><strong>Verified:</strong> <?php echo date('F j, Y g:i A', strtotime($existingKyc['verified_at'])); ?></p>
                <?php endif; ?>
            </div>
            <?php
            $docFields = ['id_document_front', 'id_document_back', 'proof_of_address', 'signature_image'];
            $hasDocs = false;
            foreach ($docFields as $docKey) {
                if (!empty($existingKyc[$docKey])) { $hasDocs = true; break; }
            }
            foreach ($kycExtraFields as $efVal) {
                if (preg_match('/\.(jpg|jpeg|png|gif|webp|pdf)$/i', (string)$efVal)) { $hasDocs = true; break; }
            }
            ?>
            <?php if ($hasDocs): ?>
            <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid rgba(255,255,255,0.15);">
                <strong>Documents on file:</strong>
                <div style="margin-top: 8px; display: flex; flex-wrap: wrap; gap: 8px;">
                    <?php foreach ($docFields as $docKey): ?>
                        <?php if (!empty($existingKyc[$docKey])): ?>
                        <span style="background: rgba(255,255,255,0.15); padding: 4px 10px; border-radius: 8px; font-size: 12px;"><?php echo htmlspecialchars($kycLabels[$docKey] ?? $docKey); ?></span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php foreach ($kycExtraFields as $efKey => $efVal): ?>
                        <?php if (preg_match('/\.(jpg|jpeg|png|gif|webp|pdf)$/i', (string)$efVal)): ?>
                        <span style="background: rgba(255,255,255,0.15); padding: 4px 10px; border-radius: 8px; font-size: 12px;"><?php echo htmlspecialchars($kycLabels[$efKey] ?? $efKey); ?></span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($canEdit): ?>
<form method="POST" enctype="multipart/form-data" id="kycForm">
    <input type="hidden" name="account_type" value="individual" id="account_type_field">
    
    <div class="kyc-wizard">
        <!-- Step Indicator -->
        <div class="steps-indicator">
            <?php for ($i = 1; $i <= $totalWizardSteps; $i++): ?>
            <div class="step <?php echo $i === 1 ? 'active' : ''; ?>" data-step="<?php echo $i; ?>">
                <div class="step-circle"><?php echo $i; ?></div>
                <div class="step-label"><?php echo htmlspecialchars($stepLabels[$i - 1]); ?></div>
            </div>
            <?php endfor; ?>
        </div>

        <!-- Step 1: Personal Information -->
        <div class="step-content active" id="step1">
            <h3 style="color: #032B44; margin-bottom: 24px;">Personal Information</h3>
            
            <div class="form-group">
                <label class="form-label">
                    Full Legal Name *
                    <span class="auto-filled-badge">Auto-filled</span>
                </label>
                <input type="text" class="form-control" name="full_legal_name" 
                       value="<?php echo safeFormValue($existingKyc, 'full_legal_name', htmlspecialchars($user['full_name'] ?? '')); ?>" 
                       required readonly>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">
                        Date of Birth *
                        <span class="auto-filled-badge">Auto-filled</span>
                    </label>
                    <input type="date" class="form-control" name="date_of_birth" 
                           value="<?php echo safeFormDate($existingKyc, 'date_of_birth', safeFormDate(['date_of_birth' => $user['date_of_birth'] ?? ''], 'date_of_birth')); ?>" 
                           required readonly>
                </div>
                <?php if (!$kycConfig['use_custom']): ?>
                <div class="form-group">
                    <label class="form-label"><?php echo htmlspecialchars($kycProfile['identity_label']); ?> *</label>
                    <input type="text" class="form-control kyc-identity-input" name="ssn" 
                           value="<?php echo kycFormValue($existingKyc, $kycExtraFields, 'ssn'); ?>" 
                           <?php echo (is_array($existingKyc) && !empty($existingKyc['ssn'])) ? '' : 'required'; ?>
                           placeholder="<?php echo htmlspecialchars($kycProfile['identity_placeholder']); ?>" 
                           maxlength="<?php echo (int)$kycProfile['identity_maxlength']; ?>"
                           data-profile="<?php echo htmlspecialchars($kycProfile['code']); ?>">
                    <?php if (is_array($existingKyc) && !empty($existingKyc['ssn'])): ?>
                    <small style="color: #666;">Leave blank to keep existing value on file.</small>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <?php if ($kycConfig['use_custom']): ?>
                <?php foreach ($kycConfig['custom_fields'] as $field): ?>
                    <?php if (($field['step'] ?? 'personal') !== 'personal') continue; ?>
                    <div class="form-group">
                        <label class="form-label"><?php echo htmlspecialchars($field['label']); ?><?php echo $field['required'] ? ' *' : ''; ?></label>
                        <?php if ($field['type'] === 'textarea'): ?>
                        <textarea class="form-control" name="<?php echo htmlspecialchars($field['key']); ?>" rows="3"
                                  <?php echo $field['required'] ? 'required' : ''; ?>
                                  placeholder="<?php echo htmlspecialchars($field['placeholder']); ?>"><?php echo kycFormValue($existingKyc, $kycExtraFields, $field['key']); ?></textarea>
                        <?php elseif ($field['type'] === 'select'): ?>
                        <select class="form-control" name="<?php echo htmlspecialchars($field['key']); ?>" <?php echo $field['required'] ? 'required' : ''; ?>>
                            <option value="">Select...</option>
                            <?php foreach ($field['options'] as $opt): ?>
                            <option value="<?php echo htmlspecialchars($opt); ?>" <?php echo kycFormValue($existingKyc, $kycExtraFields, $field['key']) === $opt ? 'selected' : ''; ?>><?php echo htmlspecialchars($opt); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php elseif ($field['type'] === 'file'): ?>
                        <input type="file" class="form-control kyc-file-input" name="<?php echo htmlspecialchars($field['key']); ?>" 
                               accept="image/*,application/pdf" data-preview="<?php echo htmlspecialchars($field['key']); ?>_preview"
                               <?php echo $field['required'] && empty(kycFormValue($existingKyc, $kycExtraFields, $field['key'])) ? 'required' : ''; ?>>
                        <div class="file-preview" id="<?php echo htmlspecialchars($field['key']); ?>_preview"></div>
                        <?php if (!empty(kycFormValue($existingKyc, $kycExtraFields, $field['key']))): ?>
                        <small style="color: #666;">Current file on record.</small>
                        <?php endif; ?>
                        <?php elseif ($field['type'] === 'date'): ?>
                        <input type="date" class="form-control" name="<?php echo htmlspecialchars($field['key']); ?>"
                               value="<?php echo kycFormValue($existingKyc, $kycExtraFields, $field['key']); ?>"
                               <?php echo $field['required'] ? 'required' : ''; ?>>
                        <?php else: ?>
                        <input type="text" class="form-control" name="<?php echo htmlspecialchars($field['key']); ?>"
                               value="<?php echo kycFormValue($existingKyc, $kycExtraFields, $field['key']); ?>"
                               placeholder="<?php echo htmlspecialchars($field['placeholder']); ?>"
                               <?php echo $field['required'] ? 'required' : ''; ?>>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
            
            <h4 style="color: #032B44; margin: 30px 0 20px 0;">Residential Address</h4>
            <p style="color: #666; margin-bottom: 20px; font-size: 14px;">
                <span class="auto-filled-badge">Auto-filled from registration</span>
            </p>
            
            <div class="form-group">
                <label class="form-label">Street Address *</label>
                <input type="text" class="form-control" name="residential_address" 
                       value="<?php echo safeFormValue($existingKyc, 'residential_address', htmlspecialchars($user['address'] ?? '')); ?>" 
                       required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">City *</label>
                    <input type="text" class="form-control" name="residential_city" 
                           value="<?php echo safeFormValue($existingKyc, 'residential_city', htmlspecialchars($user['city'] ?? '')); ?>" 
                           required>
                </div>
                <div class="form-group">
                    <label class="form-label"><?php echo htmlspecialchars($kycProfile['state_label']); ?> *</label>
                    <input type="text" class="form-control" name="residential_state" 
                           value="<?php echo safeFormValue($existingKyc, 'residential_state', htmlspecialchars($user['state'] ?? '')); ?>" 
                           required>
                </div>
                <div class="form-group">
                    <label class="form-label"><?php echo htmlspecialchars($kycProfile['zip_label']); ?> *</label>
                    <input type="text" class="form-control" name="residential_zip" 
                           value="<?php echo safeFormValue($existingKyc, 'residential_zip', htmlspecialchars($user['postal_code'] ?? '')); ?>" 
                           required>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Country *</label>
                <input type="text" class="form-control" name="residential_country" 
                       value="<?php echo safeFormValue($existingKyc, 'residential_country', htmlspecialchars($user['country'] ?? $kycProfile['default_country'])); ?>" 
                       required>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if ($showGovIdStep): ?>
        <!-- Step 2: Government ID -->
        <div class="step-content" id="step2">
            <h3 style="color: #032B44; margin-bottom: 24px;">Government-Issued Photo ID</h3>
            
            <div class="form-group">
                <label class="form-label">ID Type *</label>
                <select class="form-control" name="id_type" required>
                    <option value="">Select ID Type</option>
                    <?php foreach ($kycProfile['id_types'] as $typeKey => $typeLabel): ?>
                    <option value="<?php echo htmlspecialchars($typeKey); ?>" <?php echo (is_array($existingKyc) && isset($existingKyc['id_type']) && $existingKyc['id_type'] == $typeKey) ? 'selected' : ''; ?>><?php echo htmlspecialchars($typeLabel); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">ID Number *</label>
                <input type="text" class="form-control" name="id_number" 
                       value="<?php echo safeFormValue($existingKyc, 'id_number'); ?>" 
                       required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">ID Issued Date *</label>
                    <input type="date" class="form-control" name="id_issued_date" 
                           value="<?php echo safeFormDate($existingKyc, 'id_issued_date'); ?>" 
                           required>
                </div>
                <div class="form-group">
                    <label class="form-label">ID Expiry Date *</label>
                    <input type="date" class="form-control" name="id_expiry_date" 
                           value="<?php echo safeFormDate($existingKyc, 'id_expiry_date'); ?>" 
                           required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label"><?php echo htmlspecialchars($kycProfile['id_issued_state_label']); ?> *</label>
                    <input type="text" class="form-control" name="id_issued_state" 
                           value="<?php echo safeFormValue($existingKyc, 'id_issued_state'); ?>" 
                           required>
                </div>
                <div class="form-group">
                    <label class="form-label">ID Issued Country *</label>
                    <input type="text" class="form-control" name="id_issued_country" 
                           value="<?php echo safeFormValue($existingKyc, 'id_issued_country', $kycProfile['id_issued_country_default']); ?>" 
                           required>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php
        $docStepNum = $showGovIdStep ? 3 : 2;
        $complianceStepNum = $showGovIdStep ? 4 : 3;
        $reviewStepNum = $totalWizardSteps;
        ?>
        <!-- Documents -->
        <div class="step-content" id="step<?php echo $docStepNum; ?>">
            <h3 style="color: #032B44; margin-bottom: 24px;">Required Documents</h3>
            
            <div class="form-row">
                <?php foreach ($kycConfig['document_fields'] as $docField): ?>
                <div class="form-group">
                    <label class="form-label"><?php echo htmlspecialchars($docField['label']); ?><?php echo !empty($docField['required']) ? ' *' : ''; ?></label>
                    <input type="file" class="form-control kyc-file-input" name="<?php echo htmlspecialchars($docField['key']); ?>" 
                           accept="image/*,application/pdf" data-preview="<?php echo htmlspecialchars($docField['key']); ?>_preview"
                           <?php echo !empty($docField['required']) && empty($existingKyc[$docField['key']] ?? '') ? 'required' : ''; ?>>
                    <?php if (!empty($docField['hint'])): ?>
                    <small style="color: #666;"><?php echo htmlspecialchars($docField['hint']); ?></small>
                    <?php endif; ?>
                    <div class="file-preview" id="<?php echo htmlspecialchars($docField['key']); ?>_preview"></div>
                    <?php if (is_array($existingKyc) && !empty($existingKyc[$docField['key']])): ?>
                        <small style="color: #666; display: block; margin-top: 8px;">Current: <?php echo basename($existingKyc[$docField['key']]); ?></small>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Compliance -->
        <div class="step-content" id="step<?php echo $complianceStepNum; ?>">
            <h3 style="color: #032B44; margin-bottom: 24px;">Compliance Information</h3>
            
            <?php foreach ($kycConfig['compliance_fields'] as $field): ?>
            <div class="form-group">
                <label class="form-label"><?php echo htmlspecialchars($field['label']); ?> *</label>
                <textarea class="form-control" name="<?php echo htmlspecialchars($field['key']); ?>" rows="4" required 
                          placeholder="<?php echo htmlspecialchars($field['placeholder']); ?>"><?php echo safeFormValue($existingKyc, $field['key']); ?></textarea>
            </div>
            <?php endforeach; ?>

            <?php if ($kycConfig['use_custom']): ?>
                <?php foreach ($kycConfig['custom_fields'] as $field): ?>
                    <?php if (($field['step'] ?? 'personal') !== 'compliance') continue; ?>
                    <div class="form-group">
                        <label class="form-label"><?php echo htmlspecialchars($field['label']); ?><?php echo $field['required'] ? ' *' : ''; ?></label>
                        <?php if ($field['type'] === 'textarea'): ?>
                        <textarea class="form-control" name="<?php echo htmlspecialchars($field['key']); ?>" rows="4"
                                  <?php echo $field['required'] ? 'required' : ''; ?>><?php echo kycFormValue($existingKyc, $kycExtraFields, $field['key']); ?></textarea>
                        <?php else: ?>
                        <input type="text" class="form-control" name="<?php echo htmlspecialchars($field['key']); ?>"
                               value="<?php echo kycFormValue($existingKyc, $kycExtraFields, $field['key']); ?>"
                               <?php echo $field['required'] ? 'required' : ''; ?>>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Review -->
        <div class="step-content" id="step<?php echo $reviewStepNum; ?>">
            <h3 style="color: #032B44; margin-bottom: 24px;">Review Your Submission</h3>
            <p style="color: #666; margin-bottom: 24px;">Please review all information before submitting. You can go back to make changes.</p>
            <div id="reviewContent" class="review-summary"></div>
        </div>
        
        <!-- Wizard Navigation -->
        <div class="wizard-actions">
            <button type="button" class="btn btn-secondary" id="prevBtn" onclick="changeStep(-1)" style="display: none;">
                <i class="fas fa-arrow-left"></i> Previous
            </button>
            <div style="margin-left: auto;">
                <button type="button" class="btn btn-primary" id="nextBtn" onclick="changeStep(1)">
                    Next <i class="fas fa-arrow-right"></i>
                </button>
                <button type="submit" class="btn btn-primary" id="submitBtn" style="display: none;">
                    <i class="fas fa-paper-plane"></i> <?php echo (is_array($existingKyc) && !empty($existingKyc)) ? 'Update' : 'Submit'; ?> Verification
        </button>
            </div>
        </div>
    </div>
    </form>

<script>
let currentStep = 1;
const totalSteps = <?php echo (int)$totalWizardSteps; ?>;
const showGovIdStep = <?php echo $showGovIdStep ? 'true' : 'false'; ?>;
const fieldLabels = <?php echo json_encode($kycLabels, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
const idTypeLabels = <?php echo json_encode($kycProfile['id_types'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
const kycReviewSections = <?php echo json_encode($kycReviewSections, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
const kycFormEl = document.getElementById('kycForm');

function getKycField(name) {
    if (!kycFormEl || !name) return null;
    return kycFormEl.querySelector('[name="' + name.replace(/"/g, '\\"') + '"]');
}

function changeStep(direction) {
    if (direction > 0 && currentStep >= totalSteps) return;
    if (direction < 0 && currentStep <= 1) return;

    const currentStepEl = document.getElementById('step' + currentStep);
    if (!currentStepEl) return;

    const inputs = currentStepEl.querySelectorAll('input[required], select[required], textarea[required]');
    
    if (direction > 0) {
        let isValid = true;
        inputs.forEach(input => {
            if (input.type === 'file') {
                if (!input.files || input.files.length === 0) {
                    isValid = false;
                    input.style.borderColor = '#ef4444';
                } else {
                    input.style.borderColor = '#e0e0e0';
                }
            } else {
                const val = input.value != null ? String(input.value).trim() : '';
                if (!val) {
                    isValid = false;
                    input.style.borderColor = '#ef4444';
                } else {
                    input.style.borderColor = '#e0e0e0';
                }
            }
        });
        
        if (!isValid) {
            alert('Please fill in all required fields before continuing.');
            return;
        }
    }
    
    currentStepEl.classList.remove('active');
    const currentIndicator = document.querySelector('.step[data-step="' + currentStep + '"]');
    if (currentIndicator) currentIndicator.classList.remove('active');
    
    currentStep += direction;

    const nextStepEl = document.getElementById('step' + currentStep);
    const nextIndicator = document.querySelector('.step[data-step="' + currentStep + '"]');
    if (!nextStepEl || !nextIndicator) {
        currentStep -= direction;
        currentStepEl.classList.add('active');
        if (currentIndicator) currentIndicator.classList.add('active');
        return;
    }

    nextStepEl.classList.add('active');
    nextIndicator.classList.add('active');
    
    if (direction > 0) {
        const prevIndicator = document.querySelector('.step[data-step="' + (currentStep - 1) + '"]');
        if (prevIndicator) prevIndicator.classList.add('completed');
        if (currentStep === totalSteps) {
            buildReviewSummary();
        }
    } else {
        const forwardIndicator = document.querySelector('.step[data-step="' + (currentStep + 1) + '"]');
        if (forwardIndicator) forwardIndicator.classList.remove('completed');
    }
    
    document.getElementById('prevBtn').style.display = currentStep > 1 ? 'inline-flex' : 'none';
    document.getElementById('nextBtn').style.display = currentStep < totalSteps ? 'inline-flex' : 'none';
    document.getElementById('submitBtn').style.display = currentStep === totalSteps ? 'inline-flex' : 'none';
}

function getFieldLabel(name) {
    return fieldLabels[name] || name.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
}

function getInputValue(name) {
    const el = getKycField(name);
    if (!el) return '';
    if (el.type === 'file') {
        return el.files && el.files.length ? el.files[0].name : '';
    }
    if (el.tagName === 'SELECT') {
        const opt = el.options[el.selectedIndex];
        return opt ? (opt.text || opt.value || '') : (el.value || '');
    }
    const raw = el.value;
    return raw == null ? '' : String(raw).trim();
}

function buildReviewSummary() {
    const container = document.getElementById('reviewContent');
    if (!container || !kycFormEl) return;
    
    let html = '';
    kycReviewSections.forEach(section => {
        let rows = '';
        (section.fields || []).forEach(field => {
            if (!field) return;
            let val = getInputValue(field);
            if (field === 'id_type' && val) {
                const select = getKycField('id_type');
                val = select && select.value ? (idTypeLabels[select.value] || val) : val;
            }
            if (field === 'ssn' && !val) val = '(unchanged if on file)';
            if (!val && field !== 'ssn') return;
            rows += '<div class="review-row"><span class="review-label">' + escapeHtml(getFieldLabel(field)) + '</span><span class="review-value">' + escapeHtml(val) + '</span></div>';
        });
        if (rows) {
            html += '<div class="review-summary"><h4>' + escapeHtml(section.title) + '</h4>' + rows + '</div>';
        }
    });

    const knownFields = new Set();
    kycReviewSections.forEach(section => {
        (section.fields || []).forEach(f => knownFields.add(f));
    });
    knownFields.add('account_type');

    kycFormEl.querySelectorAll('input[name], select[name], textarea[name]').forEach(el => {
        const name = el.name;
        if (!name || knownFields.has(name) || el.type === 'file' || el.type === 'hidden') return;
        const val = getInputValue(name);
        if (val) {
            html += '<div class="review-row"><span class="review-label">' + escapeHtml(getFieldLabel(name)) + '</span><span class="review-value">' + escapeHtml(val) + '</span></div>';
        }
    });
    
    let docsHtml = '';
    kycFormEl.querySelectorAll('.kyc-file-input').forEach(input => {
        if (input.files && input.files.length) {
            const file = input.files[0];
            docsHtml += '<div style="text-align:center;"><small>' + escapeHtml(getFieldLabel(input.name)) + '</small>';
            if (file.type.startsWith('image/')) {
                docsHtml += '<img src="' + escapeHtml(getFilePreviewUrl(input)) + '" alt="' + escapeHtml(file.name) + '" style="max-width:100%;max-height:120px;border-radius:8px;">';
            } else {
                docsHtml += '<div class="pdf-label"><i class="fas fa-file"></i> ' + escapeHtml(file.name) + '</div>';
            }
            docsHtml += '</div>';
        }
    });
    if (docsHtml) {
        html += '<div class="review-summary"><h4>Documents</h4><div class="review-docs">' + docsHtml + '</div></div>';
    }
    
    container.innerHTML = html || '<p>No data to review.</p>';
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

const kycFilePreviewUrls = new Map();

function revokeFilePreviewUrl(input) {
    const existing = kycFilePreviewUrls.get(input);
    if (existing) {
        URL.revokeObjectURL(existing);
        kycFilePreviewUrls.delete(input);
    }
}

function getFilePreviewUrl(input) {
    const file = input.files && input.files[0];
    if (!file) return '';
    if (!file.type.startsWith('image/')) return '';

    const cached = kycFilePreviewUrls.get(input);
    if (cached) return cached;

    const url = URL.createObjectURL(file);
    kycFilePreviewUrls.set(input, url);
    return url;
}

function setupFilePreviews() {
    document.querySelectorAll('.kyc-file-input').forEach(input => {
        input.addEventListener('change', function(e) {
            const previewId = e.target.dataset.preview;
            const previewEl = previewId ? document.getElementById(previewId) : null;
            if (!previewEl) return;
            revokeFilePreviewUrl(e.target);
            previewEl.innerHTML = '';
            previewEl.classList.remove('active');
            const file = e.target.files && e.target.files[0];
            if (!file) return;
            previewEl.classList.add('active');
            if (file.type.startsWith('image/')) {
                const img = document.createElement('img');
                img.src = getFilePreviewUrl(e.target);
                img.alt = file.name;
                previewEl.appendChild(img);
            } else {
                previewEl.innerHTML = `<div class="pdf-label"><i class="fas fa-file-pdf"></i> ${escapeHtml(file.name)}</div>`;
            }
        });
    });

    const kycForm = document.getElementById('kycForm');
    if (kycForm) {
        kycForm.addEventListener('submit', function() {
            kycFilePreviewUrls.forEach((url) => URL.revokeObjectURL(url));
            kycFilePreviewUrls.clear();
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    setupFilePreviews();
    
    const ssnInput = document.querySelector('.kyc-identity-input');
    if (ssnInput) {
        const profile = ssnInput.dataset.profile || 'US';
        ssnInput.addEventListener('input', function(e) {
            let value = e.target.value;
            if (profile === 'US') {
                value = value.replace(/\D/g, '');
                if (value.length > 3) value = value.slice(0, 3) + '-' + value.slice(3);
                if (value.length > 6) value = value.slice(0, 6) + '-' + value.slice(6);
                e.target.value = value.slice(0, 11);
            } else if (profile === 'CA') {
                value = value.replace(/\D/g, '');
                if (value.length > 3) value = value.slice(0, 3) + '-' + value.slice(3);
                if (value.length > 6) value = value.slice(0, 7) + '-' + value.slice(7);
                e.target.value = value.slice(0, 11);
            } else if (profile === 'GB') {
                e.target.value = value.toUpperCase();
            }
        });
    }
});
</script>
<?php endif; // Close $canEdit form ?>

<?php if ($isVerified): ?>
<!-- Show message if KYC is verified and cannot be edited -->
<div style="text-align: center; padding: 40px; background: #f9fafb; border-radius: 16px; margin-top: 20px;">
    <i class="fas fa-check-circle" style="font-size: 48px; color: #10b981; margin-bottom: 16px;"></i>
    <h3 style="color: #032B44; margin-bottom: 8px;">KYC Verification Complete</h3>
    <p style="color: #666;">Your KYC verification has been verified. No further action is required.</p>
</div>
<?php endif; ?>

<?php
include __DIR__ . '/../../includes/mobile-nav.php';
?>
