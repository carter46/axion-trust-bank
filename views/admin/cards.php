<?php 
$pageTitle = 'Card Applications - Admin - SecureBank';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

// Include head
include __DIR__ . '/../../includes/head.php';

// Include admin sidebar
include __DIR__ . '/../../includes/admin-sidebar.php';
include __DIR__ . '/../../includes/admin-modals.php';

// Get all cards
$db = Database::getInstance();
$sql = "SELECT c.*, u.full_name, u.email, a.account_number, a.account_type 
        FROM cards c
        JOIN users u ON c.user_id = u.id
        JOIN accounts a ON c.account_id = a.id
        WHERE u.role = 'user'
        ORDER BY 
            CASE c.status 
                WHEN 'pending' THEN 1 
                WHEN 'active' THEN 2 
                WHEN 'frozen' THEN 3
                WHEN 'cancelled' THEN 4
                WHEN 'blocked' THEN 5
                WHEN 'expired' THEN 6
            END,
            c.created_at DESC";
$stmt = $db->query($sql);
$applications = $stmt->fetchAll();
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
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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

.badge-active {
    background: #d1fae5;
    color: #065f46;
}

.badge-frozen {
    background: #dbeafe;
    color: #1e40af;
}

.badge-cancelled {
    background: #fee2e2;
    color: #991b1b;
}

.badge-blocked {
    background: #f3f4f6;
    color: #374151;
}

.badge-expired {
    background: #fef3c7;
    color: #92400e;
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

.btn-view {
    background: #3b82f6;
    color: white;
}

.btn-view:hover {
    background: #2563eb;
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
    <h1>User Cards</h1>
    <p style="color: #666;">Review and manage user cards</p>
</div>

<!-- Stats -->
<div class="stats-grid">
    <?php
    $pending = count(array_filter($applications, fn($a) => $a['status'] === 'pending'));
    $active = count(array_filter($applications, fn($a) => $a['status'] === 'active'));
    $cancelled = count(array_filter($applications, fn($a) => $a['status'] === 'cancelled'));
    ?>
    <div class="stat-card">
        <div class="stat-label">Pending</div>
        <div class="stat-value" style="color: #f59e0b;"><?php echo $pending; ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Active</div>
        <div class="stat-value" style="color: #10b981;"><?php echo $active; ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Cancelled</div>
        <div class="stat-value" style="color: #ef4444;"><?php echo $cancelled; ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total</div>
        <div class="stat-value"><?php echo count($applications); ?></div>
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
                    <th>Account</th>
                    <th>Card Type</th>
                    <th>Card Name</th>
                            <th>Created Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($applications)): ?>
                    <?php foreach ($applications as $app): ?>
                        <tr>
                            <td><?php echo $app['id']; ?></td>
                            <td>
                                <div style="font-weight: 600;"><?php echo htmlspecialchars($app['full_name']); ?></div>
                                <div style="font-size: 12px; color: #666;"><?php echo htmlspecialchars($app['email']); ?></div>
                            </td>
                            <td>
                                <div><?php echo htmlspecialchars($app['account_number']); ?></div>
                                <div style="font-size: 12px; color: #666;"><?php echo ucfirst($app['account_type']); ?></div>
                            </td>
                            <td style="text-transform: capitalize;"><?php echo $app['card_type']; ?></td>
                            <td><?php echo htmlspecialchars($app['card_name']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($app['created_at'])); ?></td>
                            <td>
                                <span class="badge badge-<?php echo $app['status']; ?>">
                                    <?php echo $app['status']; ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <?php if ($app['status'] === 'pending'): ?>
                                        <button class="btn btn-approve" onclick="showApproveModal(<?php echo $app['id']; ?>, '<?php echo htmlspecialchars($app['full_name']); ?>')">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                        <button class="btn btn-reject" onclick="showRejectModal(<?php echo $app['id']; ?>, '<?php echo htmlspecialchars($app['full_name']); ?>')">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    <?php endif; ?>
                                    
                                    <!-- Admin Delete Button (always visible) -->
                                    <button class="btn btn-danger" onclick="adminDeleteCard(<?php echo $app['id']; ?>, '<?php echo htmlspecialchars($app['full_name']); ?>')" style="grid-column: 1 / -1; margin-top: 10px; width: 100%;">
                                        <i class="fas fa-trash"></i> Delete Card
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center; color: #666; padding: 40px;">
                            No user cards found
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
        <div class="modal-header">Approve Card Application</div>
        <div class="modal-body">
            <p>Are you sure you want to approve this card application for <strong id="approveName"></strong>?</p>
            <form id="approveForm" method="POST">
                <input type="hidden" name="card_id" id="approveId">
                <div class="form-group">
                    <label class="form-label">Admin Notes (Optional)</label>
                    <textarea name="admin_notes" class="form-control" rows="3" placeholder="Add any notes..."></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-cancel" onclick="closeModal('approveModal')">Cancel</button>
            <button class="btn btn-approve" onclick="submitApprove()">
                <i class="fas fa-check"></i> Approve
            </button>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">Reject Card Application</div>
        <div class="modal-body">
            <p>Are you sure you want to reject this card application for <strong id="rejectName"></strong>?</p>
            <form id="rejectForm" method="POST">
                <input type="hidden" name="card_id" id="rejectId">
                <div class="form-group">
                    <label class="form-label">Rejection Reason *</label>
                    <textarea name="rejection_reason" class="form-control" rows="3" placeholder="Please provide a reason for rejection..." required></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-cancel" onclick="closeModal('rejectModal')">Cancel</button>
            <button class="btn btn-reject" onclick="submitReject()">
                <i class="fas fa-times"></i> Reject
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize admin cards page
});

function showApproveModal(id, name) {
    document.getElementById('approveId').value = id;
    document.getElementById('approveName').textContent = name;
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
    const cardId = document.getElementById('approveId').value;
    const adminNotes = form.querySelector('textarea[name="admin_notes"]').value;
    
    try {
        const response = await fetch('<?php echo SITE_URL; ?>/api/admin-approve-card.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                card_id: cardId,
                admin_notes: adminNotes
            })
        });
        const data = await response.json();
        
        if (data.success) {
            showToast('Card application approved successfully!', 'success');
            location.reload();
        } else {
            showToast(data.message || 'Failed to approve application', 'error');
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
    
    const cardId = document.getElementById('rejectId').value;
    const rejectionReason = form.querySelector('textarea[name="rejection_reason"]').value;
    
    try {
        const response = await fetch('<?php echo SITE_URL; ?>/api/admin-reject-card.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                card_id: cardId,
                rejection_reason: rejectionReason
            })
        });
        const data = await response.json();
        
        if (data.success) {
            showToast('Card application rejected.', 'success');
            location.reload();
        } else {
            showToast(data.message || 'Failed to reject application', 'error');
        }
    } catch (error) {
        showToast('An error occurred. Please try again.', 'error');
    }
}

function adminDeleteCard(cardId, userName) {
    if (!confirm(`Are you sure you want to delete the card for ${userName}? This action cannot be undone.`)) {
        return;
    }
    
    if (!confirm('This will permanently delete the card and all associated data. Are you absolutely sure?')) {
        return;
    }
    
    fetch('<?php echo SITE_URL; ?>/api/admin-delete-card.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            card_id: cardId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Card deleted successfully', 'success');
            location.reload();
        } else {
            showToast(data.message || 'Failed to delete card', 'error');
        }
    })
    .catch(error => {
        console.error('Error deleting card:', error);
        showToast('An error occurred while deleting the card', 'error');
    });
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}
</script>


