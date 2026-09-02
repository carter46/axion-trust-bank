<?php 
$pageTitle = 'KYC Details - Admin - ' . getSiteName();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/kyc-config.php';
require_once __DIR__ . '/../../models/Kyc.php';

$kycConfig = getKycFieldsForUser($kyc['user_id']);
$kycProfile = $kycConfig['profile'];
$kycExtraFields = getKycExtraFieldsFromRecord($kyc);
$canAdminEditKyc = in_array($kyc['status'], ['verified', 'pending', 'under_review', 'rejected', 'requires_action'], true);

// Include head
include __DIR__ . '/../../includes/head.php';

// Include admin sidebar
include __DIR__ . '/../../includes/admin-sidebar.php';

// Helper to decrypt and display sensitive data (if needed)
function formatSSN($encrypted) {
    if (empty($encrypted)) return 'N/A';
    // Try to decrypt, if it fails, show masked version
    if (function_exists('decryptData')) {
        try {
            $decrypted = decryptData($encrypted);
            // Show first 3 and last 4 digits only for security
            if (strlen($decrypted) >= 9) {
                return substr($decrypted, 0, 3) . '-**-****';
            }
        } catch (Exception $e) {
            // If decryption fails, show masked
        }
    }
    // Show masked version if decryption unavailable or failed
    return '***-**-****';
}
?>

<style>
.page-header {
    margin-bottom: 30px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 16px;
}

.page-header-actions {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.page-header h1 {
    font-size: 32px;
    font-weight: 700;
    color: #032B44;
    margin-bottom: 8px;
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

.card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 24px;
    padding: 30px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    margin-bottom: 24px;
}

.section-title {
    font-size: 20px;
    font-weight: 600;
    color: #032B44;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 2px solid #e0e0e0;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.info-item {
    margin-bottom: 16px;
}

.info-label {
    font-weight: 600;
    color: #666;
    font-size: 14px;
    margin-bottom: 4px;
}

.info-value {
    color: #032B44;
    font-size: 16px;
}

.status-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
}

.status-pending { background: #fef3c7; color: #92400e; }
.status-verified { background: #d1fae5; color: #065f46; }
.status-rejected { background: #fee2e2; color: #991b1b; }
.status-under_review { background: #dbeafe; color: #1e40af; }

.action-buttons {
    display: flex;
    gap: 12px;
    margin-top: 30px;
    padding-top: 30px;
    border-top: 2px solid #e0e0e0;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-approve {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.btn-approve:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
}

.btn-reject {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
}

.btn-reject:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(239, 68, 68, 0.3);
}

.btn-edit {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
}

.btn-edit:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);
}

.btn-secondary {
    background: #f3f4f6;
    color: #374151;
}

.btn-secondary:hover {
    background: #e5e7eb;
}

.edit-form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.form-group {
    margin-bottom: 16px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #374151;
    font-size: 14px;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 12px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 15px;
    box-sizing: border-box;
}

.form-group textarea {
    min-height: 100px;
    resize: vertical;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #3b82f6;
}

.form-hint {
    font-size: 12px;
    color: #6b7280;
    margin-top: 4px;
}

.edit-panel {
    display: none;
}

.edit-panel.is-visible {
    display: block;
}

.view-panel.is-hidden {
    display: none;
}

.document-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: #f3f4f6;
    color: #032B44;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s;
}

.document-link:hover {
    background: #e5e7eb;
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: stretch;
    }

    .page-header-actions {
        flex-direction: column;
        align-items: stretch;
        width: 100%;
    }

    .page-header-actions .btn {
        width: 100%;
        justify-content: center;
        text-align: center;
    }

    .page-header-actions .status-badge {
        align-self: flex-start;
    }

    .page-header h1 {
        font-size: 24px;
    }

    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<a href="<?php echo SITE_URL; ?>/admin/kyc" class="back-button">
    <i class="fas fa-arrow-left"></i> Back to KYC List
</a>

<div class="page-header">
    <div>
        <h1>KYC Verification Details</h1>
        <p style="color: #666;">Review submission for <?php echo htmlspecialchars($kyc['full_name'] ?? $kyc['email']); ?></p>
    </div>
    <div class="page-header-actions">
        <span class="status-badge status-<?php echo $kyc['status']; ?>">
            <?php echo ucfirst(str_replace('_', ' ', $kyc['status'])); ?>
        </span>
        <?php if ($canAdminEditKyc): ?>
        <button type="button" class="btn btn-edit" id="toggleEditBtn" onclick="toggleEditMode()">
            <i class="fas fa-pen"></i> Edit KYC Details
        </button>
        <?php endif; ?>
    </div>
</div>

<div id="viewPanel" class="view-panel">
<div class="card">
    <h3 class="section-title">User Information</h3>
    <div class="info-grid">
        <div class="info-item">
            <div class="info-label">Full Name</div>
            <div class="info-value"><?php echo htmlspecialchars($kyc['full_name'] ?? 'N/A'); ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Email</div>
            <div class="info-value"><?php echo htmlspecialchars($kyc['email'] ?? 'N/A'); ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Phone</div>
            <div class="info-value"><?php echo htmlspecialchars($kyc['phone'] ?? 'N/A'); ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Account Type</div>
            <div class="info-value"><?php echo ucfirst($kyc['account_type'] ?? 'individual'); ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Submitted</div>
            <div class="info-value"><?php echo date('F j, Y g:i A', strtotime($kyc['submitted_at'])); ?></div>
        </div>
        <?php if (!empty($kyc['verified_at'])): ?>
        <div class="info-item">
            <div class="info-label">Verified At</div>
            <div class="info-value"><?php echo date('F j, Y g:i A', strtotime($kyc['verified_at'])); ?></div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Personal Information -->
<div class="card">
    <h3 class="section-title">Personal Information</h3>
    <div class="info-grid">
        <div class="info-item">
            <div class="info-label">Full Legal Name</div>
            <div class="info-value"><?php echo htmlspecialchars($kyc['full_legal_name'] ?? 'N/A'); ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Date of Birth</div>
            <div class="info-value"><?php echo $kyc['date_of_birth'] ? date('F j, Y', strtotime($kyc['date_of_birth'])) : 'N/A'; ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">SSN/ITIN</div>
            <div class="info-value"><?php echo formatSSN($kyc['ssn']); ?></div>
        </div>
    </div>
</div>

<!-- Residential Address -->
<div class="card">
    <h3 class="section-title">Residential Address</h3>
    <div class="info-grid">
        <div class="info-item">
            <div class="info-label">Street Address</div>
            <div class="info-value"><?php echo htmlspecialchars($kyc['residential_address'] ?? 'N/A'); ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">City</div>
            <div class="info-value"><?php echo htmlspecialchars($kyc['residential_city'] ?? 'N/A'); ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">State</div>
            <div class="info-value"><?php echo htmlspecialchars($kyc['residential_state'] ?? 'N/A'); ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">ZIP Code</div>
            <div class="info-value"><?php echo htmlspecialchars($kyc['residential_zip'] ?? 'N/A'); ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Country</div>
            <div class="info-value"><?php echo htmlspecialchars($kyc['residential_country'] ?? 'N/A'); ?></div>
        </div>
    </div>
</div>

<!-- Government ID -->
<div class="card">
    <h3 class="section-title">Government-Issued Photo ID</h3>
    <div class="info-grid">
        <div class="info-item">
            <div class="info-label">ID Type</div>
            <div class="info-value"><?php echo ucfirst(str_replace('_', ' ', $kyc['id_type'] ?? 'N/A')); ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">ID Number</div>
            <div class="info-value"><?php echo htmlspecialchars($kyc['id_number'] ?? 'N/A'); ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Issued Date</div>
            <div class="info-value"><?php echo $kyc['id_issued_date'] ? date('F j, Y', strtotime($kyc['id_issued_date'])) : 'N/A'; ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Expiry Date</div>
            <div class="info-value"><?php echo $kyc['id_expiry_date'] ? date('F j, Y', strtotime($kyc['id_expiry_date'])) : 'N/A'; ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Issued State</div>
            <div class="info-value"><?php echo htmlspecialchars($kyc['id_issued_state'] ?? 'N/A'); ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Issued Country</div>
            <div class="info-value"><?php echo htmlspecialchars($kyc['id_issued_country'] ?? 'N/A'); ?></div>
        </div>
    </div>
</div>

<!-- Documents -->
<div class="card">
    <h3 class="section-title">Documents</h3>
    <div class="info-grid">
        <?php if (!empty($kyc['id_document_front'])): ?>
        <div class="info-item">
            <div class="info-label">ID Front</div>
            <a href="<?php echo SITE_URL; ?>/uploads/<?php echo htmlspecialchars($kyc['id_document_front']); ?>" target="_blank" class="document-link">
                <i class="fas fa-file"></i> View Document
            </a>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($kyc['id_document_back'])): ?>
        <div class="info-item">
            <div class="info-label">ID Back</div>
            <a href="<?php echo SITE_URL; ?>/uploads/<?php echo htmlspecialchars($kyc['id_document_back']); ?>" target="_blank" class="document-link">
                <i class="fas fa-file"></i> View Document
            </a>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($kyc['proof_of_address'])): ?>
        <div class="info-item">
            <div class="info-label">Proof of Address</div>
            <a href="<?php echo SITE_URL; ?>/uploads/<?php echo htmlspecialchars($kyc['proof_of_address']); ?>" target="_blank" class="document-link">
                <i class="fas fa-file"></i> View Document
            </a>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($kyc['signature_image'])): ?>
        <div class="info-item">
            <div class="info-label">Signature</div>
            <a href="<?php echo SITE_URL; ?>/uploads/<?php echo htmlspecialchars($kyc['signature_image']); ?>" target="_blank" class="document-link">
                <i class="fas fa-file"></i> View Document
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Compliance Information -->
<div class="card">
    <h3 class="section-title">Compliance Information</h3>
    <div class="info-item">
        <div class="info-label">Source of Funds</div>
        <div class="info-value" style="margin-top: 8px; white-space: pre-wrap;"><?php echo htmlspecialchars($kyc['source_of_funds'] ?? 'N/A'); ?></div>
    </div>
    <div class="info-item" style="margin-top: 20px;">
        <div class="info-label">Account Purpose</div>
        <div class="info-value" style="margin-top: 8px; white-space: pre-wrap;"><?php echo htmlspecialchars($kyc['account_purpose'] ?? 'N/A'); ?></div>
    </div>
</div>

<?php if ($kyc['account_type'] === 'business'): ?>
<!-- Business Information -->
<div class="card">
    <h3 class="section-title">Business Information</h3>
    <div class="info-grid">
        <div class="info-item">
            <div class="info-label">Business Name</div>
            <div class="info-value"><?php echo htmlspecialchars($kyc['business_name'] ?? 'N/A'); ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Business Address</div>
            <div class="info-value"><?php echo htmlspecialchars($kyc['business_address'] ?? 'N/A'); ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Business City</div>
            <div class="info-value"><?php echo htmlspecialchars($kyc['business_city'] ?? 'N/A'); ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Business State</div>
            <div class="info-value"><?php echo htmlspecialchars($kyc['business_state'] ?? 'N/A'); ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Business ZIP</div>
            <div class="info-value"><?php echo htmlspecialchars($kyc['business_zip'] ?? 'N/A'); ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Business Country</div>
            <div class="info-value"><?php echo htmlspecialchars($kyc['business_country'] ?? 'N/A'); ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">EIN</div>
            <div class="info-value"><?php echo formatSSN($kyc['ein']); ?></div>
        </div>
        <?php if (!empty($kyc['business_formation_doc'])): ?>
        <div class="info-item">
            <div class="info-label">Business Formation Document</div>
            <a href="<?php echo SITE_URL; ?>/uploads/<?php echo htmlspecialchars($kyc['business_formation_doc']); ?>" target="_blank" class="document-link">
                <i class="fas fa-file"></i> View Document
            </a>
        </div>
        <?php endif; ?>
    </div>
    
    <?php if (!empty($beneficialOwners)): ?>
    <div style="margin-top: 30px;">
        <h4 style="color: #032B44; margin-bottom: 16px;">Beneficial Owners</h4>
        <?php foreach ($beneficialOwners as $owner): ?>
        <div style="background: #f9fafb; padding: 16px; border-radius: 12px; margin-bottom: 12px;">
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Name</div>
                    <div class="info-value"><?php echo htmlspecialchars($owner['first_name'] . ' ' . $owner['last_name']); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Ownership</div>
                    <div class="info-value"><?php echo number_format($owner['ownership_percentage'], 2); ?>%</div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php if (!empty($kyc['admin_notes'])): ?>
<div class="card">
    <h3 class="section-title">Admin Notes</h3>
    <div class="info-value" style="white-space: pre-wrap;"><?php echo htmlspecialchars($kyc['admin_notes']); ?></div>
</div>
<?php endif; ?>

<?php if ($kyc['status'] === 'pending' || $kyc['status'] === 'under_review'): ?>
<!-- Action Buttons -->
<div class="card">
    <div class="action-buttons">
        <button type="button" class="btn btn-approve" onclick="approveKYC(<?php echo $kyc['id']; ?>)">
            <i class="fas fa-check"></i> Approve Verification
        </button>
        <button type="button" class="btn btn-reject" onclick="rejectKYC(<?php echo $kyc['id']; ?>)">
            <i class="fas fa-times"></i> Reject Verification
        </button>
    </div>
</div>
<?php endif; ?>
</div>

<?php if ($canAdminEditKyc): ?>
<div id="editPanel" class="edit-panel">
    <div class="card">
        <h3 class="section-title">Edit KYC Details</h3>
        <p style="color: #666; margin-bottom: 24px;">Update submitted KYC information. Verification status will remain <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $kyc['status']))); ?>.</p>

        <form id="adminKycEditForm" enctype="multipart/form-data" onsubmit="submitKycEdit(event); return false;">
            <input type="hidden" name="kyc_id" value="<?php echo (int)$kyc['id']; ?>">
            <input type="hidden" name="account_type" value="<?php echo htmlspecialchars($kyc['account_type'] ?? 'individual'); ?>">

            <h4 style="color: #032B44; margin-bottom: 16px;">Personal Information</h4>
            <div class="edit-form-grid">
                <div class="form-group">
                    <label for="edit_full_legal_name">Full Legal Name *</label>
                    <input type="text" id="edit_full_legal_name" name="full_legal_name" required value="<?php echo htmlspecialchars($kyc['full_legal_name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="edit_date_of_birth">Date of Birth *</label>
                    <input type="date" id="edit_date_of_birth" name="date_of_birth" required value="<?php echo htmlspecialchars($kyc['date_of_birth'] ?? ''); ?>">
                </div>
                <?php if (!$kycConfig['use_custom']): ?>
                <div class="form-group">
                    <label for="edit_ssn"><?php echo htmlspecialchars($kycProfile['identity_label']); ?></label>
                    <input type="text" id="edit_ssn" name="ssn" placeholder="Leave blank to keep current value" maxlength="<?php echo (int)($kycProfile['identity_maxlength'] ?? 20); ?>">
                    <div class="form-hint">Current value on file: <?php echo formatSSN($kyc['ssn']); ?></div>
                </div>
                <?php endif; ?>
            </div>

            <?php if (!$kycConfig['use_custom']): ?>
            <h4 style="color: #032B44; margin: 24px 0 16px;">Residential Address</h4>
            <div class="edit-form-grid">
                <div class="form-group">
                    <label for="edit_residential_address">Street Address *</label>
                    <input type="text" id="edit_residential_address" name="residential_address" required value="<?php echo htmlspecialchars($kyc['residential_address'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="edit_residential_city">City *</label>
                    <input type="text" id="edit_residential_city" name="residential_city" required value="<?php echo htmlspecialchars($kyc['residential_city'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="edit_residential_state"><?php echo htmlspecialchars($kycProfile['state_label']); ?> *</label>
                    <input type="text" id="edit_residential_state" name="residential_state" required value="<?php echo htmlspecialchars($kyc['residential_state'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="edit_residential_zip"><?php echo htmlspecialchars($kycProfile['zip_label']); ?> *</label>
                    <input type="text" id="edit_residential_zip" name="residential_zip" required value="<?php echo htmlspecialchars($kyc['residential_zip'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="edit_residential_country">Country *</label>
                    <input type="text" id="edit_residential_country" name="residential_country" required value="<?php echo htmlspecialchars($kyc['residential_country'] ?? $kycProfile['default_country']); ?>">
                </div>
            </div>

            <h4 style="color: #032B44; margin: 24px 0 16px;">Government-Issued Photo ID</h4>
            <div class="edit-form-grid">
                <div class="form-group">
                    <label for="edit_id_type">ID Type *</label>
                    <select id="edit_id_type" name="id_type" required>
                        <?php foreach ($kycProfile['id_types'] as $typeKey => $typeLabel): ?>
                        <option value="<?php echo htmlspecialchars($typeKey); ?>" <?php echo ($kyc['id_type'] ?? '') === $typeKey ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($typeLabel); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_id_number">ID Number *</label>
                    <input type="text" id="edit_id_number" name="id_number" required value="<?php echo htmlspecialchars($kyc['id_number'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="edit_id_issued_date">Issued Date *</label>
                    <input type="date" id="edit_id_issued_date" name="id_issued_date" required value="<?php echo htmlspecialchars($kyc['id_issued_date'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="edit_id_expiry_date">Expiry Date *</label>
                    <input type="date" id="edit_id_expiry_date" name="id_expiry_date" required value="<?php echo htmlspecialchars($kyc['id_expiry_date'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="edit_id_issued_state"><?php echo htmlspecialchars($kycProfile['id_issued_state_label']); ?> *</label>
                    <input type="text" id="edit_id_issued_state" name="id_issued_state" required value="<?php echo htmlspecialchars($kyc['id_issued_state'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="edit_id_issued_country">Issued Country *</label>
                    <input type="text" id="edit_id_issued_country" name="id_issued_country" required value="<?php echo htmlspecialchars($kyc['id_issued_country'] ?? $kycProfile['id_issued_country_default']); ?>">
                </div>
            </div>
            <?php endif; ?>

            <?php if ($kycConfig['use_custom']): ?>
            <h4 style="color: #032B44; margin: 24px 0 16px;">Custom Fields</h4>
            <div class="edit-form-grid">
                <?php foreach ($kycConfig['custom_fields'] as $field): ?>
                <div class="form-group">
                    <label for="edit_<?php echo htmlspecialchars($field['key']); ?>"><?php echo htmlspecialchars($field['label']); ?><?php echo !empty($field['required']) ? ' *' : ''; ?></label>
                    <?php if ($field['type'] === 'textarea'): ?>
                    <textarea id="edit_<?php echo htmlspecialchars($field['key']); ?>" name="<?php echo htmlspecialchars($field['key']); ?>" <?php echo !empty($field['required']) ? 'required' : ''; ?>><?php echo htmlspecialchars($kycExtraFields[$field['key']] ?? ''); ?></textarea>
                    <?php elseif ($field['type'] === 'select'): ?>
                    <select id="edit_<?php echo htmlspecialchars($field['key']); ?>" name="<?php echo htmlspecialchars($field['key']); ?>" <?php echo !empty($field['required']) ? 'required' : ''; ?>>
                        <option value="">Select...</option>
                        <?php foreach (($field['options'] ?? []) as $option): ?>
                        <option value="<?php echo htmlspecialchars($option); ?>" <?php echo ($kycExtraFields[$field['key']] ?? '') === $option ? 'selected' : ''; ?>><?php echo htmlspecialchars($option); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php elseif ($field['type'] === 'file'): ?>
                    <input type="file" id="edit_<?php echo htmlspecialchars($field['key']); ?>" name="<?php echo htmlspecialchars($field['key']); ?>" accept="image/*,.pdf">
                    <?php if (!empty($kycExtraFields[$field['key']])): ?>
                    <div class="form-hint">Current file on record.</div>
                    <?php endif; ?>
                    <?php elseif ($field['type'] === 'date'): ?>
                    <input type="date" id="edit_<?php echo htmlspecialchars($field['key']); ?>" name="<?php echo htmlspecialchars($field['key']); ?>" value="<?php echo htmlspecialchars($kycExtraFields[$field['key']] ?? ''); ?>" <?php echo !empty($field['required']) ? 'required' : ''; ?>>
                    <?php else: ?>
                    <input type="text" id="edit_<?php echo htmlspecialchars($field['key']); ?>" name="<?php echo htmlspecialchars($field['key']); ?>" value="<?php echo htmlspecialchars($kycExtraFields[$field['key']] ?? ''); ?>" <?php echo !empty($field['required']) ? 'required' : ''; ?>>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <h4 style="color: #032B44; margin: 24px 0 16px;">Documents</h4>
            <div class="edit-form-grid">
                <?php foreach ($kycConfig['document_fields'] as $docField): ?>
                <div class="form-group">
                    <label for="edit_<?php echo htmlspecialchars($docField['key']); ?>"><?php echo htmlspecialchars($docField['label']); ?><?php echo !empty($docField['required']) ? ' *' : ''; ?></label>
                    <input type="file" id="edit_<?php echo htmlspecialchars($docField['key']); ?>" name="<?php echo htmlspecialchars($docField['key']); ?>" accept="image/*,.pdf">
                    <?php if (!empty($kyc[$docField['key']])): ?>
                    <div class="form-hint">Current document on file. Upload only to replace it.</div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>

            <h4 style="color: #032B44; margin: 24px 0 16px;">Compliance Information</h4>
            <div class="form-group">
                <label for="edit_source_of_funds">Source of Funds *</label>
                <textarea id="edit_source_of_funds" name="source_of_funds" required><?php echo htmlspecialchars($kyc['source_of_funds'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label for="edit_account_purpose">Account Purpose *</label>
                <textarea id="edit_account_purpose" name="account_purpose" required><?php echo htmlspecialchars($kyc['account_purpose'] ?? ''); ?></textarea>
            </div>

            <?php if (($kyc['account_type'] ?? 'individual') === 'business'): ?>
            <h4 style="color: #032B44; margin: 24px 0 16px;">Business Information</h4>
            <div class="edit-form-grid">
                <div class="form-group">
                    <label for="edit_business_name">Business Name</label>
                    <input type="text" id="edit_business_name" name="business_name" value="<?php echo htmlspecialchars($kyc['business_name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="edit_business_address">Business Address</label>
                    <input type="text" id="edit_business_address" name="business_address" value="<?php echo htmlspecialchars($kyc['business_address'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="edit_business_city">Business City</label>
                    <input type="text" id="edit_business_city" name="business_city" value="<?php echo htmlspecialchars($kyc['business_city'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="edit_business_state">Business State</label>
                    <input type="text" id="edit_business_state" name="business_state" value="<?php echo htmlspecialchars($kyc['business_state'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="edit_business_zip">Business ZIP</label>
                    <input type="text" id="edit_business_zip" name="business_zip" value="<?php echo htmlspecialchars($kyc['business_zip'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="edit_business_country">Business Country</label>
                    <input type="text" id="edit_business_country" name="business_country" value="<?php echo htmlspecialchars($kyc['business_country'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="edit_ein">EIN</label>
                    <input type="text" id="edit_ein" name="ein" placeholder="Leave blank to keep current value">
                    <div class="form-hint">Current value on file: <?php echo formatSSN($kyc['ein']); ?></div>
                </div>
                <div class="form-group">
                    <label for="edit_business_formation_doc">Business Formation Document</label>
                    <input type="file" id="edit_business_formation_doc" name="business_formation_doc" accept="image/*,.pdf">
                </div>
            </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="edit_admin_notes">Admin Notes</label>
                <textarea id="edit_admin_notes" name="admin_notes"><?php echo htmlspecialchars($kyc['admin_notes'] ?? ''); ?></textarea>
            </div>

            <div class="action-buttons" style="border-top: none; padding-top: 0; margin-top: 24px;">
                <button type="submit" class="btn btn-edit" id="saveKycBtn">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <button type="button" class="btn btn-secondary" onclick="toggleEditMode()">Cancel</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<div id="approveModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center;">
    <div style="background: white; padding: 30px; border-radius: 16px; max-width: 500px; width: 90%;">
        <h3 style="margin-bottom: 20px; color: #032B44;">Approve KYC Verification</h3>
        <form id="approveForm" onsubmit="submitApprove(event); return false;">
            <input type="hidden" name="kyc_id" id="approve_id">
            <div class="form-group">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Admin Notes (Optional)</label>
                <textarea name="notes" id="approve_notes" rows="3" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px;"></textarea>
            </div>
            <div style="display: flex; gap: 12px; margin-top: 20px;">
                <button type="submit" class="btn btn-approve" style="flex: 1;">Approve</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('approveModal')">Cancel</button>
            </div>
        </form>
    </div>
</div>

<div id="rejectModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center;">
    <div style="background: white; padding: 30px; border-radius: 16px; max-width: 500px; width: 90%;">
        <h3 style="margin-bottom: 20px; color: #032B44;">Reject KYC Verification</h3>
        <form id="rejectForm" onsubmit="submitReject(event); return false;">
            <input type="hidden" name="kyc_id" id="reject_id">
            <div class="form-group">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Rejection Reason *</label>
                <textarea name="reason" id="reject_reason" rows="3" required style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px;"></textarea>
            </div>
            <div class="form-group" style="margin-top: 16px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Admin Notes (Optional)</label>
                <textarea name="notes" id="reject_notes" rows="3" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px;"></textarea>
            </div>
            <div style="display: flex; gap: 12px; margin-top: 20px;">
                <button type="submit" class="btn btn-reject" style="flex: 1;">Reject</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('rejectModal')">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
let editModeActive = false;

function toggleEditMode() {
    editModeActive = !editModeActive;
    const viewPanel = document.getElementById('viewPanel');
    const editPanel = document.getElementById('editPanel');
    const toggleBtn = document.getElementById('toggleEditBtn');

    if (!viewPanel || !editPanel || !toggleBtn) {
        return;
    }

    if (editModeActive) {
        viewPanel.classList.add('is-hidden');
        editPanel.classList.add('is-visible');
        toggleBtn.innerHTML = '<i class="fas fa-eye"></i> View KYC Details';
    } else {
        viewPanel.classList.remove('is-hidden');
        editPanel.classList.remove('is-visible');
        toggleBtn.innerHTML = '<i class="fas fa-pen"></i> Edit KYC Details';
    }
}

function submitKycEdit(event) {
    event.preventDefault();

    const form = document.getElementById('adminKycEditForm');
    const saveBtn = document.getElementById('saveKycBtn');
    const formData = new FormData(form);

    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

    fetch('<?php echo SITE_URL; ?>/api/admin-update-kyc.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('KYC details updated successfully!');
            window.location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to update KYC details'));
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="fas fa-save"></i> Save Changes';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
        saveBtn.disabled = false;
        saveBtn.innerHTML = '<i class="fas fa-save"></i> Save Changes';
    });
}

function approveKYC(id) {
    document.getElementById('approve_id').value = id;
    document.getElementById('approveModal').style.display = 'flex';
}

function rejectKYC(id) {
    document.getElementById('reject_id').value = id;
    document.getElementById('rejectModal').style.display = 'flex';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function submitApprove(event) {
    event.preventDefault();
    const formData = new FormData();
    formData.append('kyc_id', document.getElementById('approve_id').value);
    formData.append('notes', document.getElementById('approve_notes').value);
    
    fetch('<?php echo SITE_URL; ?>/api/admin-approve-kyc.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('KYC approved successfully!');
            window.location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to approve KYC'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}

function submitReject(event) {
    event.preventDefault();
    const reason = document.getElementById('reject_reason').value.trim();
    if (!reason) {
        alert('Rejection reason is required');
        return;
    }
    
    const formData = new FormData();
    formData.append('kyc_id', document.getElementById('reject_id').value);
    formData.append('reason', reason);
    formData.append('notes', document.getElementById('reject_notes').value);
    
    fetch('<?php echo SITE_URL; ?>/api/admin-reject-kyc.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('KYC rejected successfully!');
            window.location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to reject KYC'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}
</script>

<?php
?>

