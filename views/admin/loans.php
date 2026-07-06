<?php 
$pageTitle = 'Loan Applications - Admin - SecureBank';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

// Include head
include __DIR__ . '/../../includes/head.php';

// Include admin sidebar
include __DIR__ . '/../../includes/admin-sidebar.php';
include __DIR__ . '/../../includes/admin-modals.php';

// Get all loan applications
$db = Database::getInstance();
$sql = "SELECT l.*, u.full_name, u.email, a.account_number, a.account_type 
        FROM loans l
        JOIN users u ON l.user_id = u.id
        LEFT JOIN accounts a ON l.account_id = a.id
        WHERE u.role = 'user'
        ORDER BY 
            CASE l.status 
                WHEN 'pending' THEN 1 
                WHEN 'approved' THEN 2
                WHEN 'active' THEN 3
                WHEN 'rejected' THEN 4
                WHEN 'completed' THEN 5
            END,
            l.application_date DESC";
$stmt = $db->query($sql);
$loans = $stmt->fetchAll();
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
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.stat-label {
    font-size: 14px;
    color: #666;
    margin-bottom: 8px;
}

.stat-value {
    font-size: 28px;
    font-weight: 700;
    color: #032B44;
}

.card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 24px;
    padding: 30px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.table-responsive {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

table th {
    padding: 12px;
    text-align: left;
    border-bottom: 2px solid #e0e0e0;
    color: #032B44;
    font-weight: 600;
}

table td {
    padding: 12px;
    border-bottom: 1px solid #f0f0f0;
}

.badge {
    padding: 6px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    text-transform: capitalize;
}

.badge-pending {
    background: #fef3c7;
    color: #92400e;
}

.badge-approved,
.badge-active {
    background: #d1fae5;
    color: #065f46;
}

.badge-rejected {
    background: #fee2e2;
    color: #991b1b;
}

.badge-completed {
    background: #e0e7ff;
    color: #3730a3;
}

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-block;
}

.btn-approve {
    background: #10b981;
    color: white;
}

.btn-approve:hover {
    background: #059669;
}

.btn-reject {
    background: #ef4444;
    color: white;
}

.btn-reject:hover {
    background: #dc2626;
}

.action-buttons {
    display: flex;
    gap: 8px;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: white;
    margin: 10% auto;
    padding: 30px;
    border-radius: 16px;
    max-width: 500px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.2);
}

.modal-header {
    font-size: 20px;
    font-weight: 700;
    color: #032B44;
    margin-bottom: 20px;
}

.modal-body {
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 15px;
}

.form-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 6px;
}

.form-control {
    width: 100%;
    padding: 10px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
}

.modal-footer {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

.btn-cancel {
    background: #e5e7eb;
    color: #374151;
}

.btn-cancel:hover {
    background: #d1d5db;
}

@media (max-width: 768px) {
    .page-header h1 {
        font-size: 24px;
    }
    
    .card {
        padding: 20px;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .modal-content {
        margin: 20% 15px;
        padding: 20px;
    }
}
</style>

<div class="page-header">
    <h1>Loan Applications</h1>
    <p style="color: #666;">Review and manage loan applications</p>
</div>

<!-- Stats -->
<div class="stats-grid">
    <?php
    $pending = count(array_filter($loans, fn($l) => $l['status'] === 'pending'));
    $approved = count(array_filter($loans, fn($l) => $l['status'] === 'approved' || $l['status'] === 'active'));
    $rejected = count(array_filter($loans, fn($l) => $l['status'] === 'rejected'));
    $totalAmount = array_sum(array_map(fn($l) => $l['status'] === 'pending' ? $l['loan_amount'] : 0, $loans));
    ?>
    <div class="stat-card">
        <div class="stat-label">Pending</div>
        <div class="stat-value" style="color: #f59e0b;"><?php echo $pending; ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Approved</div>
        <div class="stat-value" style="color: #10b981;"><?php echo $approved; ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Rejected</div>
        <div class="stat-value" style="color: #ef4444;"><?php echo $rejected; ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Pending Amount</div>
        <div class="stat-value" style="font-size: 22px;"><?php echo formatCurrency($totalAmount, DEFAULT_CURRENCY); ?></div>
    </div>
</div>

<!-- Applications Table -->
<div class="card">
    <h3 style="color: #032B44; margin-bottom: 20px;">All Applications</h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Loan Type</th>
                    <th>Amount</th>
                    <th>Term</th>
                    <th>Interest Rate</th>
                    <th>Applied Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($loans)): ?>
                    <?php foreach ($loans as $loan): ?>
                        <tr>
                            <td><?php echo $loan['id']; ?></td>
                            <td>
                                <div style="font-weight: 600;"><?php echo htmlspecialchars($loan['full_name']); ?></div>
                                <div style="font-size: 12px; color: #666;"><?php echo htmlspecialchars($loan['email']); ?></div>
                            </td>
                            <td style="text-transform: capitalize;"><?php echo $loan['loan_type']; ?></td>
                            <td style="font-weight: 600;"><?php echo formatCurrency($loan['loan_amount'], DEFAULT_CURRENCY, DEFAULT_CURRENCY); ?></td>
                            <td><?php echo $loan['term_months']; ?> months</td>
                            <td><?php echo $loan['interest_rate']; ?>%</td>
                            <td><?php echo date('M d, Y', strtotime($loan['application_date'])); ?></td>
                            <td>
                                <span class="badge badge-<?php echo $loan['status']; ?>">
                                    <?php echo $loan['status']; ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <?php if ($loan['status'] === 'pending'): ?>
                                        <button class="btn btn-approve" onclick="showApproveModal(<?php echo $loan['id']; ?>, '<?php echo htmlspecialchars($loan['full_name']); ?>', <?php echo $loan['loan_amount']; ?>)">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                        <button class="btn btn-reject" onclick="showRejectModal(<?php echo $loan['id']; ?>, '<?php echo htmlspecialchars($loan['full_name']); ?>')">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    <?php else: ?>
                                        <span style="color: #999; font-size: 13px;">
                                            <?php echo ucfirst($loan['status']); ?>
                                            <?php if ($loan['approval_date']): ?>
                                                on <?php echo date('M d', strtotime($loan['approval_date'])); ?>
                                            <?php endif; ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" style="text-align: center; color: #666; padding: 40px;">
                            No loan applications found
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Approve Modal -->
<div id="approveModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">Approve Loan Application</div>
        <div class="modal-body">
            <p>Approve loan application for <strong id="approveName"></strong></p>
            <form id="approveForm" method="POST">
                <input type="hidden" name="loan_id" id="approveId">
                <div class="form-group">
                    <label class="form-label">Approved Amount *</label>
                    <input type="number" name="approved_amount" id="approvedAmount" class="form-control" step="0.01" required>
                    <small style="color: #666;">You can adjust the approved amount</small>
                </div>
                <div class="form-group">
                    <label class="form-label">Admin Notes (Optional)</label>
                    <textarea name="admin_notes" class="form-control" rows="3" placeholder="Add any notes..."></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-cancel" onclick="closeModal('approveModal')">Cancel</button>
            <button class="btn btn-approve" onclick="submitApprove()">
                <i class="fas fa-check"></i> Approve Loan
            </button>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">Reject Loan Application</div>
        <div class="modal-body">
            <p>Reject loan application for <strong id="rejectName"></strong>?</p>
            <form id="rejectForm" method="POST">
                <input type="hidden" name="loan_id" id="rejectId">
                <div class="form-group">
                    <label class="form-label">Rejection Reason *</label>
                    <textarea name="rejection_reason" class="form-control" rows="3" placeholder="Please provide a reason for rejection..." required></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-cancel" onclick="closeModal('rejectModal')">Cancel</button>
            <button class="btn btn-reject" onclick="submitReject()">
                <i class="fas fa-times"></i> Reject Loan
            </button>
        </div>
    </div>
</div>

<script>
function showApproveModal(id, name, amount) {
    document.getElementById('approveId').value = id;
    document.getElementById('approveName').textContent = name;
    document.getElementById('approvedAmount').value = amount;
    document.getElementById('approveModal').style.display = 'block';
}

function showRejectModal(id, name) {
    document.getElementById('rejectId').value = id;
    document.getElementById('rejectName').textContent = name;
    document.getElementById('rejectModal').style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

async function submitApprove() {
    const form = document.getElementById('approveForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const formData = new FormData(form);
    
    try {
        const response = await fetch('<?php echo SITE_URL; ?>/api/admin-approve-loan.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        
        if (data.success) {
            showToast('Loan application approved successfully!', 'success');
            location.reload();
        } else {
            showToast(data.message || 'Failed to approve loan', 'error');
        }
    } catch (error) {
        showToast('An error occurred. Please try again.', 'error');
    }
}

async function submitReject() {
    const form = document.getElementById('rejectForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const formData = new FormData(form);
    
    try {
        const response = await fetch('<?php echo SITE_URL; ?>/api/admin-reject-loan.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        
        if (data.success) {
            showToast('Loan application rejected.', 'success');
            location.reload();
        } else {
            showToast(data.message || 'Failed to reject loan', 'error');
        }
    } catch (error) {
        showToast('An error occurred. Please try again.', 'error');
    }
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}
</script>


