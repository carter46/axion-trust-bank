<?php 
$pageTitle = 'KYC Details - Admin - ' . getSiteName();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../models/Kyc.php';

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
    align-items: center;
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
    <div>
        <span class="status-badge status-<?php echo $kyc['status']; ?>">
            <?php echo ucfirst(str_replace('_', ' ', $kyc['status'])); ?>
        </span>
    </div>
</div>

<!-- User Information -->
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

<!-- Modals -->
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

