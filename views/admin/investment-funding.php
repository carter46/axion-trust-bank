<?php 
$pageTitle = 'Investment Funding Management - Admin';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAdmin();

// Include head and sidebar
include __DIR__ . '/../../includes/head.php';
include __DIR__ . '/../../includes/admin-sidebar.php';

// Display messages
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success" style="background: #d1fae5; border: 2px solid #10b981; color: #065f46; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
            <i class="fas fa-check-circle"></i> ' . htmlspecialchars($_SESSION['success']) . '
          </div>';
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-error" style="background: #fee2e2; border: 2px solid #ef4444; color: #991b1b; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
            <i class="fas fa-exclamation-circle"></i> ' . htmlspecialchars($_SESSION['error']) . '
          </div>';
    unset($_SESSION['error']);
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
}

.tab-navigation {
    display: flex;
    gap: 10px;
    margin-bottom: 30px;
    border-bottom: 2px solid #e5e7eb;
}

.tab-button {
    padding: 12px 24px;
    background: transparent;
    border: none;
    font-size: 16px;
    cursor: pointer;
    color: #666;
    border-bottom: 3px solid transparent;
    transition: all 0.3s;
    font-weight: 600;
}

.tab-button.active {
    color: #032B44;
    border-bottom-color: #032B44;
}

.funding-table {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 24px;
    padding: 30px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

thead {
    background: #f9fafb;
}

th {
    padding: 16px;
    text-align: left;
    font-weight: 600;
    color: #374151;
    border-bottom: 2px solid #e5e7eb;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

td {
    padding: 16px;
    border-bottom: 1px solid #e5e7eb;
    color: #374151;
}

tr:hover {
    background: #f9fafb;
}

.status-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
}

.status-completed {
    background: #d1fae5;
    color: #065f46;
}

.status-failed {
    background: #fee2e2;
    color: #991b1b;
}

.btn-approve {
    background: #10b981;
    color: white;
    padding: 8px 16px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-approve:hover {
    background: #059669;
}

.btn-reject {
    background: #ef4444;
    color: white;
    padding: 8px 16px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s;
    margin-left: 8px;
}

.btn-reject:hover {
    background: #dc2626;
}

.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.7);
    z-index: 10000; /* Higher than mobile nav (9999) */
    align-items: center;
    justify-content: center;
    padding: 20px;
}

@media (max-width: 768px) {
    .modal {
        z-index: 10000; /* Ensure modals are above mobile nav on mobile */
    }
}

.modal.active {
    display: flex;
}

.modal-content {
    background: white;
    border-radius: 16px;
    padding: 30px;
    max-width: 500px;
    width: 100%;
}

.modal-header {
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 1px solid #e5e7eb;
}

.modal-header h2 {
    margin: 0;
    color: #032B44;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #374151;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 16px;
}

.form-control:focus {
    outline: none;
    border-color: #032B44;
}

.btn-primary {
    background: linear-gradient(135deg, #032B44 0%, #024a6b 100%);
    color: white;
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(3, 43, 68, 0.3);
}

.tx-hash {
    font-family: monospace;
    font-size: 12px;
    background: #f9fafb;
    padding: 8px;
    border-radius: 4px;
    word-break: break-all;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-state i {
    font-size: 64px;
    color: #d1d5db;
    margin-bottom: 16px;
}

.empty-state h3 {
    font-size: 24px;
    color: #374151;
    margin-bottom: 8px;
}

.empty-state p {
    color: #6b7280;
    font-size: 16px;
}
</style>

<div class="page-header">
    <h1>Investment Funding Management</h1>
    <p style="color: #666;">Approve or reject pending crypto funding requests</p>
</div>

<div class="tab-navigation">
    <button class="tab-button active" onclick="showTab('pending')">Pending (<span id="pendingCount"><?php echo isset($pendingFunding) ? count($pendingFunding) : 0; ?></span>)</button>
    <button class="tab-button" onclick="showTab('all')">All History (<span id="allCount"><?php echo isset($allFunding) ? count($allFunding) : 0; ?></span>)</button>
</div>

<!-- Pending Funding Tab -->
<div id="pendingTab" class="tab-content">
    <div class="funding-table">
        <?php if (empty($pendingFunding ?? [])): ?>
            <div class="empty-state">
                <i class="fas fa-check-circle"></i>
                <h3>No Pending Crypto Funding</h3>
                <p>All crypto funding requests have been processed.</p>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>User</th>
                        <th>Amount</th>
                        <th>Crypto</th>
                        <th>Transaction Hash</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendingFunding as $funding): ?>
                    <tr>
                        <td><?php echo date('M d, Y H:i', strtotime($funding['created_at'])); ?></td>
                        <td>
                            <div>
                                <strong><?php echo htmlspecialchars($funding['full_name'] ?? 'Unknown'); ?></strong><br>
                                <small style="color: #6b7280;"><?php echo htmlspecialchars($funding['email'] ?? ''); ?></small>
                            </div>
                        </td>
                        <td><strong><?php echo formatCurrency($funding['amount']); ?></strong></td>
                        <td><?php echo strtoupper($funding['crypto_currency'] ?? ''); ?></td>
                        <td>
                            <div class="tx-hash"><?php echo htmlspecialchars($funding['crypto_tx_hash'] ?? 'N/A'); ?></div>
                        </td>
                        <td>
                            <span class="status-badge status-<?php echo $funding['status']; ?>">
                                <?php echo ucfirst($funding['status']); ?>
                            </span>
                        </td>
                        <td>
                            <button class="btn-approve" onclick="approveFunding(<?php echo $funding['id']; ?>)">
                                <i class="fas fa-check"></i> Approve
                            </button>
                            <button class="btn-reject" onclick="rejectFunding(<?php echo $funding['id']; ?>)">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<!-- All History Tab -->
<div id="allTab" class="tab-content" style="display: none;">
    <div class="funding-table">
        <?php if (empty($allFunding ?? [])): ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>No Crypto Funding History</h3>
                <p>No crypto funding requests found.</p>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>User</th>
                        <th>Amount</th>
                        <th>Crypto</th>
                        <th>Transaction Hash</th>
                        <th>Status</th>
                        <th>Processed By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allFunding as $funding): ?>
                    <tr>
                        <td><?php echo date('M d, Y H:i', strtotime($funding['created_at'])); ?></td>
                        <td>
                            <div>
                                <strong><?php echo htmlspecialchars($funding['full_name'] ?? 'Unknown'); ?></strong><br>
                                <small style="color: #6b7280;"><?php echo htmlspecialchars($funding['email'] ?? ''); ?></small>
                            </div>
                        </td>
                        <td><strong><?php echo formatCurrency($funding['amount']); ?></strong></td>
                        <td><?php echo strtoupper($funding['crypto_currency'] ?? ''); ?></td>
                        <td>
                            <div class="tx-hash"><?php echo htmlspecialchars($funding['crypto_tx_hash'] ?? 'N/A'); ?></div>
                        </td>
                        <td>
                            <span class="status-badge status-<?php echo $funding['status']; ?>">
                                <?php echo ucfirst($funding['status']); ?>
                            </span>
                        </td>
                        <td>
                            <?php 
                            if ($funding['processed_by']) {
                                $db = Database::getInstance();
                                $adminSql = "SELECT full_name FROM users WHERE id = ?";
                                $adminStmt = $db->query($adminSql, [$funding['processed_by']]);
                                $admin = $adminStmt ? $adminStmt->fetch() : null;
                                echo htmlspecialchars($admin['full_name'] ?? 'Admin');
                            } else {
                                echo '<span style="color: #9ca3af;">—</span>';
                            }
                            ?>
                        </td>
                        <td>
                            <?php if ($funding['status'] === 'pending' && !empty($funding['crypto_tx_hash'])): ?>
                                <button class="btn-approve" onclick="approveFunding(<?php echo $funding['id']; ?>)">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                                <button class="btn-reject" onclick="rejectFunding(<?php echo $funding['id']; ?>)">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            <?php else: ?>
                                <span style="color: #9ca3af; font-size: 12px;">
                                    <?php 
                                    if (empty($funding['crypto_tx_hash'])) {
                                        echo 'Awaiting TX Hash';
                                    } else {
                                        echo 'Processed';
                                    }
                                    ?>
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Reject Crypto Funding</h2>
            <button onclick="closeRejectModal()" style="float: right; background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
        </div>
        <form method="POST" action="<?php echo SITE_URL; ?>/admin/investment-funding">
            <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
            <input type="hidden" name="action" value="reject">
            <input type="hidden" name="funding_id" id="rejectFundingId">
            
            <div class="form-group">
                <label class="form-label">Rejection Reason *</label>
                <textarea name="reason" class="form-control" rows="4" required placeholder="Explain why this funding request is being rejected..."></textarea>
            </div>
            
            <div style="display: flex; gap: 10px; margin-top: 24px;">
                <button type="submit" class="btn-primary" style="flex: 1;">
                    <i class="fas fa-times"></i> Reject Funding
                </button>
                <button type="button" onclick="closeRejectModal()" style="padding: 12px 24px; background: #e5e7eb; color: #374151; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showTab(tab) {
    if (tab === 'pending') {
        document.getElementById('pendingTab').style.display = 'block';
        document.getElementById('allTab').style.display = 'none';
        document.querySelectorAll('.tab-button')[0].classList.add('active');
        document.querySelectorAll('.tab-button')[1].classList.remove('active');
    } else {
        document.getElementById('pendingTab').style.display = 'none';
        document.getElementById('allTab').style.display = 'block';
        document.querySelectorAll('.tab-button')[0].classList.remove('active');
        document.querySelectorAll('.tab-button')[1].classList.add('active');
    }
}

function approveFunding(fundingId) {
    if (confirm('Are you sure you want to approve this crypto funding request? The user\'s investment balance will be credited.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo SITE_URL; ?>/admin/investment-funding';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = 'csrf_token';
        csrfToken.value = '<?php echo Security::generateCSRFToken(); ?>';
        form.appendChild(csrfToken);
        
        const action = document.createElement('input');
        action.type = 'hidden';
        action.name = 'action';
        action.value = 'approve';
        form.appendChild(action);
        
        const id = document.createElement('input');
        id.type = 'hidden';
        id.name = 'funding_id';
        id.value = fundingId;
        form.appendChild(id);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function rejectFunding(fundingId) {
    const rejectId = document.getElementById('rejectFundingId');
    const rejectModal = document.getElementById('rejectModal');
    if (rejectId && rejectModal) {
        rejectId.value = fundingId;
        rejectModal.classList.add('active');
    }
}

function closeRejectModal() {
    const rejectModal = document.getElementById('rejectModal');
    const rejectId = document.getElementById('rejectFundingId');
    const textarea = document.querySelector('#rejectModal textarea');
    if (rejectModal) {
        rejectModal.classList.remove('active');
    }
    if (rejectId) {
        rejectId.value = '';
    }
    if (textarea) {
        textarea.value = '';
    }
}

// Close modal on outside click
const rejectModal = document.getElementById('rejectModal');
if (rejectModal) {
    rejectModal.addEventListener('click', function(e) {
        if (e.target === this) {
            closeRejectModal();
        }
    });
}
</script>


