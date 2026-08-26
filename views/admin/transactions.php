<?php 
$pageTitle = 'Transactions - Admin - SecureBank';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAdmin();

// Include head
include __DIR__ . '/../../includes/head.php';

// Include admin sidebar
include __DIR__ . '/../../includes/admin-sidebar.php';

// Include admin modals for edit functionality
include __DIR__ . '/../../includes/admin-modals.php';
?>

<!-- ===== ADMIN TRANSACTIONS PAGE CONTENT ===== -->

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

/* Mobile Transaction Cards */
.mobile-transactions {
    display: none;
}

.transaction-item {
    background: white;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border: 1px solid #e8e8e8;
}

.transaction-item-header {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    margin-bottom: 12px;
    padding-bottom: 12px;
    border-bottom: 1px solid #f0f0f0;
}

.transaction-item-amount {
    font-weight: 700;
    font-size: 22px;
    white-space: nowrap;
    text-align: left;
    width: 100%;
    margin-bottom: 8px;
}

.transaction-item-left {
    width: 100%;
}

.transaction-item-title {
    font-weight: 600;
    font-size: 14px;
    color: #2d3748;
    margin-bottom: 4px;
    line-height: 1.3;
}

.transaction-item-ref {
    font-family: monospace;
    font-size: 11px;
    color: #9ca3af;
    margin-bottom: 0;
}

.transaction-item-user {
    font-size: 13px;
    color: #6b7280;
    margin-top: 4px;
}

.transaction-item-amount.amount-credit {
    color: #059669;
}

.transaction-item-amount.amount-debit {
    color: #dc2626;
}

.transaction-item-body {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    margin-bottom: 12px;
    margin-top: 12px;
    padding-top: 0;
}

.transaction-item-field {
    display: flex;
    flex-direction: column;
}

.transaction-item-label {
    font-size: 11px;
    color: #6b7280;
    text-transform: uppercase;
    font-weight: 600;
    margin-bottom: 6px;
    letter-spacing: 0.5px;
}

.transaction-item-value {
    font-size: 14px;
    color: #2d3748;
    font-weight: 500;
    line-height: 1.4;
}

.transaction-item-footer {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    padding-top: 12px;
    border-top: 1px solid #f0f0f0;
    gap: 8px;
    flex-wrap: wrap;
}

.transaction-item-footer .action-btn {
    padding: 8px 16px;
    font-size: 13px;
    border-radius: 6px;
    font-weight: 500;
    border: 1px solid;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.transaction-item-footer .btn-edit {
    background: #e0e7ff;
    color: #3730a3;
    border-color: #c7d2fe;
}

.transaction-item-footer .btn-edit:hover {
    background: #c7d2fe;
}

.transaction-item-footer .btn-reverse {
    background: #fef3c7;
    color: #92400e;
    border-color: #fde68a;
}

.transaction-item-footer .btn-reverse:hover {
    background: #fde68a;
}

.transaction-item-footer .btn-delete {
    background: #fee2e2;
    color: #991b1b;
    border-color: #fecaca;
}

.transaction-item-footer .btn-delete:hover {
    background: #fecaca;
}

.status-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}

.status-completed {
    background: #d1fae5;
    color: #065f46;
}

.status-successful {
    background: #d1fae5;
    color: #065f46;
}

.status-pending {
    background: #fef3c7;
    color: #78350f;
}

.status-failed {
    background: #fee2e2;
    color: #991b1b;
}

.status-cancelled {
    background: #e5e7eb;
    color: #374151;
}

.transaction-type {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}

.type-credit {
    background: #d1fae5;
    color: #065f46;
}

.type-debit {
    background: #fee2e2;
    color: #991b1b;
}

.filters-section {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 25px;
}

.filter-input:focus {
    outline: none;
    border-color: #1e3a8a;
    box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
}

.filter-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

@media (max-width: 768px) {
    .page-header h1 {
        font-size: 24px;
    }
    
    .card {
        padding: 20px;
    }
    
    .filters-section {
        padding: 15px;
    }
    
    .filters-section form {
        grid-template-columns: 1fr !important;
        gap: 15px !important;
    }
    
    .filter-group {
        width: 100%;
    }
    
    .filter-group:last-child {
        display: flex;
        gap: 10px;
    }
    
    .filter-btn {
        flex: 1;
    }
    
    /* Hide table on mobile */
    .transactions-table {
        display: none !important;
    }
    
    /* Show mobile cards on mobile */
    .mobile-transactions {
        display: block;
    }
    
    .transaction-item {
        padding: 14px;
    }
    
    .transaction-item-header {
        margin-bottom: 8px;
        padding-bottom: 10px;
    }
    
    .transaction-item-amount {
        font-size: 20px;
        margin-bottom: 6px;
    }
    
    .transaction-item-body {
        grid-template-columns: 1fr;
        gap: 12px;
        margin-bottom: 12px;
        margin-top: 10px;
        padding-top: 0;
    }
    
    .transaction-item-footer {
        padding-top: 10px;
        flex-wrap: wrap;
        margin-top: 0;
    }
    
    .transaction-item-footer .action-btn {
        flex: 1;
        max-width: 120px;
        padding: 6px 12px;
        font-size: 12px;
    }
}
</style>

<div class="page-header">
    <h1>All Transactions</h1>
    <p style="color: #666;">View and monitor all system transactions</p>
</div>

<div class="card">
    <!-- Filters Section -->
    <div class="filters-section" style="background: #f8f9fa; padding: 20px; border-radius: 12px; margin-bottom: 25px;">
        <form method="GET" action="<?php echo SITE_URL; ?>/admin/transactions" id="filterForm" style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 15px; align-items: end;">
            <div class="filter-group">
                <label style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px; font-size: 14px;">User</label>
                <select name="user_id" id="userFilter" class="filter-input" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: white;">
                    <option value="">All Users</option>
                    <?php foreach ($allUsers as $user): ?>
                        <option value="<?php echo $user['id']; ?>" <?php echo (isset($_GET['user_id']) && $_GET['user_id'] == $user['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($user['full_name'] . ' (' . $user['email'] . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-group">
                <label style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px; font-size: 14px;">From Date</label>
                <input type="date" name="from_date" id="fromDate" class="filter-input" value="<?php echo htmlspecialchars($_GET['from_date'] ?? ''); ?>" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: white;">
            </div>
            
            <div class="filter-group">
                <label style="display: block; font-weight: 600; color: #374151; margin-bottom: 8px; font-size: 14px;">To Date</label>
                <input type="date" name="to_date" id="toDate" class="filter-input" value="<?php echo htmlspecialchars($_GET['to_date'] ?? ''); ?>" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: white;">
            </div>
            
            <div class="filter-group" style="display: flex; gap: 10px;">
                <button type="submit" class="filter-btn" style="padding: 10px 20px; background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; white-space: nowrap; transition: all 0.3s;">
                    <i class="fas fa-filter"></i> Apply
                </button>
                <a href="<?php echo SITE_URL; ?>/admin/transactions" class="filter-btn" style="padding: 10px 20px; background: #e5e7eb; color: #374151; border: none; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; text-decoration: none; white-space: nowrap; display: inline-flex; align-items: center; gap: 6px; transition: all 0.3s;">
                    <i class="fas fa-times"></i> Clear
                </a>
            </div>
        </form>
        
        <?php if (!empty($_GET['user_id']) || !empty($_GET['from_date']) || !empty($_GET['to_date'])): ?>
            <div style="margin-top: 15px; padding: 12px; background: #dbeafe; border-radius: 8px; color: #1e40af; font-size: 14px;">
                <i class="fas fa-info-circle"></i> 
                <strong>Filters Active:</strong>
                <?php 
                $activeFilters = [];
                if (!empty($_GET['user_id'])) {
                    $selectedUser = array_filter($allUsers, function($u) { return $u['id'] == $_GET['user_id']; });
                    if (!empty($selectedUser)) {
                        $user = reset($selectedUser);
                        $activeFilters[] = 'User: ' . htmlspecialchars($user['full_name']);
                    }
                }
                if (!empty($_GET['from_date'])) {
                    $activeFilters[] = 'From: ' . htmlspecialchars($_GET['from_date']);
                }
                if (!empty($_GET['to_date'])) {
                    $activeFilters[] = 'To: ' . htmlspecialchars($_GET['to_date']);
                }
                echo implode(' | ', $activeFilters);
                ?>
                <span style="margin-left: 10px; color: #6b7280;">(<?php echo count($transactions); ?> transactions found)</span>
            </div>
        <?php endif; ?>
    </div>
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        <h3 style="color: #032B44; margin: 0;">All User Transactions</h3>
        <div style="color: #6b7280; font-size: 14px;">
            <i class="fas fa-list"></i> 
            <strong><?php echo count($transactions); ?></strong> transaction<?php echo count($transactions) !== 1 ? 's' : ''; ?> found
        </div>
    </div>
    <div class="table-responsive">
        <?php if (!empty($transactions)): ?>
        <table class="transactions-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Date & Time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $transaction): ?>
                <tr>
                    <td>
                        <div>
                            <div style="font-weight: 600;"><?php echo htmlspecialchars($transaction['full_name'] ?? 'N/A'); ?></div>
                            <small style="color: #6b7280; font-size: 12px;"><?php echo htmlspecialchars($transaction['email'] ?? ''); ?></small>
                        </div>
                    </td>
                    <td>
                        <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; 
                            background: <?php echo $transaction['transaction_type'] === 'credit' ? '#d1fae5' : '#fee2e2'; ?>; 
                            color: <?php echo $transaction['transaction_type'] === 'credit' ? '#065f46' : '#991b1b'; ?>;">
                            <?php echo strtoupper($transaction['transaction_type'] ?? 'debit'); ?>
                        </span>
                        <br>
                        <small style="color: #6b7280;"><?php echo htmlspecialchars($transaction['category'] ?? 'N/A'); ?></small>
                    </td>
                    <td>
                        <span style="font-weight: 600; color: <?php echo $transaction['transaction_type'] === 'credit' ? '#10b981' : '#ef4444'; ?>;">
                            <?php echo $transaction['transaction_type'] === 'credit' ? '+' : '-'; ?>
                            <?php echo formatTransactionNative($transaction, 'amount'); ?>
                        </span>
                    </td>
                    <td>
                        <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo htmlspecialchars($transaction['description'] ?? 'N/A'); ?>">
                            <?php echo htmlspecialchars($transaction['description'] ?? 'N/A'); ?>
                        </div>
                    </td>
                    <td>
                        <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;
                            background: <?php 
                                echo isSuccessfulTransactionStatus($transaction['status'] ?? '') ? '#d1fae5' : 
                                    ($transaction['status'] === 'pending' ? '#fef3c7' : 
                                    ($transaction['status'] === 'failed' ? '#fee2e2' : '#e5e7eb')); 
                            ?>;
                            color: <?php 
                                echo isSuccessfulTransactionStatus($transaction['status'] ?? '') ? '#065f46' : 
                                    ($transaction['status'] === 'pending' ? '#78350f' : 
                                    ($transaction['status'] === 'failed' ? '#991b1b' : '#374151')); 
                            ?>;">
                            <?php echo htmlspecialchars(formatTransactionStatusLabel($transaction['status'] ?? 'unknown')); ?>
                        </span>
                    </td>
                    <td>
                        <div>
                            <div style="font-weight: 600;"><?php echo date('M d, Y', strtotime($transaction['created_at'])); ?></div>
                            <div style="font-size: 12px; color: #6b7280;"><?php echo date('H:i A', strtotime($transaction['created_at'])); ?></div>
                        </div>
                    </td>
                    <td>
                        <div style="display: flex; gap: 8px;">
                            <button onclick="editTransaction(<?php echo $transaction['id']; ?>)" style="padding: 6px 12px; background: #3b82f6; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 12px;" title="Edit Transaction">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <?php if (isSuccessfulTransactionStatus($transaction['status'] ?? '')): ?>
                            <button onclick="reverseTransaction(<?php echo $transaction['id']; ?>)" style="padding: 6px 12px; background: #f59e0b; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 12px;" title="Reverse Transaction">
                                <i class="fas fa-undo"></i>
                            </button>
                            <?php endif; ?>
                            <button onclick="deleteTransaction(<?php echo $transaction['id']; ?>)" style="padding: 6px 12px; background: #ef4444; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 12px;" title="Delete Transaction">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <!-- Mobile Transaction Cards -->
        <div class="mobile-transactions">
            <?php foreach ($transactions as $transaction): ?>
                <div class="transaction-item">
                    <div class="transaction-item-header">
                        <div class="transaction-item-amount amount-<?php echo $transaction['transaction_type']; ?>">
                            <?php echo $transaction['transaction_type'] === 'credit' ? '+' : '-'; ?>
                            <?php echo formatTransactionNative($transaction, 'amount'); ?>
                        </div>
                        <div class="transaction-item-left">
                            <div class="transaction-item-title"><?php echo htmlspecialchars($transaction['description'] ?? 'Transaction'); ?></div>
                            <div class="transaction-item-ref"><?php echo htmlspecialchars($transaction['transaction_ref'] ?? 'N/A'); ?></div>
                            <?php if (!empty($transaction['full_name'])): ?>
                                <div class="transaction-item-user">
                                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($transaction['full_name']); ?>
                                    <?php if (!empty($transaction['email'])): ?>
                                        <div style="font-size: 11px; color: #9ca3af; margin-top: 2px;"><?php echo htmlspecialchars($transaction['email']); ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="transaction-item-body">
                        <div class="transaction-item-field">
                            <div class="transaction-item-label">Type</div>
                            <div class="transaction-item-value">
                                <span class="transaction-type type-<?php echo $transaction['transaction_type']; ?>">
                                    <?php echo strtoupper($transaction['transaction_type'] ?? 'debit'); ?>
                                </span>
                                <?php if (!empty($transaction['category'])): ?>
                                    <div style="font-size: 11px; color: #6b7280; margin-top: 2px;"><?php echo htmlspecialchars($transaction['category']); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="transaction-item-field">
                            <div class="transaction-item-label">Status</div>
                            <div class="transaction-item-value">
                                <span class="status-badge status-<?php echo $transaction['status']; ?>">
                                    <?php echo strtoupper($transaction['status'] ?? 'unknown'); ?>
                                </span>
                            </div>
                        </div>
                        <div class="transaction-item-field">
                            <div class="transaction-item-label">Account</div>
                            <div class="transaction-item-value">
                                <?php if (!empty($transaction['account_number'])): ?>
                                    <?php echo htmlspecialchars($transaction['account_number']); ?>
                                    <?php if (!empty($transaction['account_type'])): ?>
                                        <div style="font-size: 11px; color: #6b7280; margin-top: 2px;"><?php echo ucfirst($transaction['account_type']); ?></div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span style="color: #6b7280;">N/A</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="transaction-item-field">
                            <div class="transaction-item-label">Date</div>
                            <div class="transaction-item-value">
                                <?php echo date('M d, Y', strtotime($transaction['created_at'])); ?>
                                <div style="font-size: 11px; color: #6b7280; margin-top: 2px;"><?php echo date('H:i A', strtotime($transaction['created_at'])); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="transaction-item-footer">
                        <button onclick="editTransaction(<?php echo $transaction['id']; ?>)" class="action-btn btn-edit" title="Edit Transaction">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <?php if (isSuccessfulTransactionStatus($transaction['status'] ?? '')): ?>
                            <button onclick="reverseTransaction(<?php echo $transaction['id']; ?>)" class="action-btn btn-reverse" title="Reverse Transaction">
                                <i class="fas fa-undo"></i> Reverse
                            </button>
                        <?php endif; ?>
                        <button onclick="deleteTransaction(<?php echo $transaction['id']; ?>)" class="action-btn btn-delete" title="Delete Transaction">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div style="text-align: center; padding: 60px 20px; color: #6b7280;">
            <i class="fas fa-receipt" style="font-size: 48px; margin-bottom: 20px; opacity: 0.5;"></i>
            <h3>No Transactions Found</h3>
            <p>No user transactions have been recorded yet.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function editTransaction(transactionId) {
    // Fetch transaction details first
    fetch('<?php echo SITE_URL; ?>/api/admin-get-transaction.php?id=' + transactionId)
        .then(response => response.json())
        .then(data => {
            if (!data.success || !data.transaction) {
                showToast('Error loading transaction details', 'error');
                return;
            }
            
            const txn = data.transaction;
            // Parse database timestamp (assumes UTC or local time)
            const createdDate = new Date(txn.created_at);
            // Extract date in YYYY-MM-DD format (use UTC to avoid timezone issues)
            const dateStr = createdDate.toISOString().split('T')[0];
            // Extract time in HH:mm format (use UTC to match database)
            const hours = String(createdDate.getUTCHours()).padStart(2, '0');
            const minutes = String(createdDate.getUTCMinutes()).padStart(2, '0');
            const timeStr = hours + ':' + minutes;
            
            // Create custom modal with all fields
            showEditTransactionModal(txn, dateStr, timeStr);
        })
        .catch(error => {
            showToast('Error loading transaction: ' + error, 'error');
        });
}

function showEditTransactionModal(transaction, dateValue, timeValue) {
    // Escape HTML to prevent XSS
    const escapeHtml = (text) => {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    };
    
    const safeAmount = parseFloat(transaction.amount) || 0;
    const safeDate = escapeHtml(dateValue || '');
    const safeTime = escapeHtml(timeValue || '');
    const safeDescription = escapeHtml((transaction.description || '').replace(/"/g, '&quot;'));
    const safeStatus = escapeHtml(transaction.status || 'completed');
    const safeId = parseInt(transaction.id) || 0;
    
    const modal = document.createElement('div');
    modal.id = 'editTransactionModal';
    modal.className = 'modal-overlay';
    modal.style.display = 'flex';
    modal.innerHTML = `
        <div class="modal-container" style="max-width: 600px;">
            <div class="modal-header">
                <h3>Edit Transaction</h3>
                <button class="modal-close" onclick="closeEditTransactionModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="editTransactionForm">
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 600;">Amount *</label>
                        <input type="number" id="editAmount" value="${safeAmount}" step="0.01" required 
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 600;">Status *</label>
                        <select id="editStatus" required 
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                            <option value="successful" ${safeStatus === 'successful' ? 'selected' : ''}>✅ Successful</option>
                            <option value="completed" ${safeStatus === 'completed' ? 'selected' : ''}>☑️ Completed</option>
                            <option value="pending" ${safeStatus === 'pending' ? 'selected' : ''}>⏳ Pending</option>
                            <option value="failed" ${safeStatus === 'failed' ? 'selected' : ''}>❌ Failed</option>
                            <option value="on_hold" ${safeStatus === 'on_hold' ? 'selected' : ''}>🟣 On Hold</option>
                            <option value="processing" ${safeStatus === 'processing' ? 'selected' : ''}>🔄 Processing</option>
                        </select>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 600;">Date *</label>
                        <input type="date" id="editDate" value="${safeDate}" required 
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 600;">Time *</label>
                        <input type="time" id="editTime" value="${safeTime}" required 
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 600;">Description *</label>
                        <textarea id="editDescription" required 
                                  style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; min-height: 80px; resize: vertical;">${safeDescription}</textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeEditTransactionModal()">Cancel</button>
                <button class="btn btn-primary" onclick="saveTransactionEdit(${safeId})">Save Changes</button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    document.body.style.overflow = 'hidden';
}

function closeEditTransactionModal() {
    const modal = document.getElementById('editTransactionModal');
    if (modal) {
        modal.remove();
        document.body.style.overflow = '';
    }
}

function saveTransactionEdit(transactionId) {
    const amount = parseFloat(document.getElementById('editAmount').value);
    const status = document.getElementById('editStatus').value;
    const date = document.getElementById('editDate').value;
    const time = document.getElementById('editTime').value;
    const description = document.getElementById('editDescription').value.trim();
    
    // Validate transaction ID
    if (!transactionId || isNaN(transactionId) || transactionId <= 0) {
        showToast('Invalid transaction ID', 'error');
        return;
    }
    
    if (!amount || amount <= 0 || isNaN(amount)) {
        showToast('Please enter a valid amount', 'error');
        return;
    }
    
    if (!date || !time) {
        showToast('Please enter both date and time', 'error');
        return;
    }
    
    if (!description) {
        showToast('Please enter a description', 'error');
        return;
    }
    
    // Validate date format (YYYY-MM-DD)
    const dateRegex = /^\d{4}-\d{2}-\d{2}$/;
    if (!dateRegex.test(date)) {
        showToast('Invalid date format', 'error');
        return;
    }
    
    // Validate time format (HH:mm)
    const timeRegex = /^\d{2}:\d{2}$/;
    if (!timeRegex.test(time)) {
        showToast('Invalid time format', 'error');
        return;
    }
    
    // Combine date and time (format: YYYY-MM-DD HH:mm:ss)
    const datetime = date + ' ' + time + ':00';
    
    console.log('Saving transaction:', {
        transaction_id: transactionId,
        amount: amount,
        status: status,
        description: description,
        date: datetime
    });
    
    fetch('<?php echo SITE_URL; ?>/api/admin-edit-transaction.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            transaction_id: transactionId,
            amount: amount,
            status: status,
            description: description,
            date: datetime
        })
    })
    .then(response => {
        console.log('Response status:', response.status);
        
        if (!response.ok) {
            return response.text().then(text => {
                console.error('HTTP Error Response:', text);
                throw new Error('HTTP error! status: ' + response.status + ', body: ' + text);
            });
        }
        
        return response.text().then(text => {
            console.log('Raw API Response:', text);
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Failed to parse JSON:', text);
                throw new Error('Invalid JSON response: ' + text);
            }
        });
    })
    .then(data => {
        console.log('Parsed API Response:', data);
        if (data.success) {
            console.log('Transaction update successful!');
            showToast('Transaction updated successfully!', 'success');
            closeEditTransactionModal();
            
            // Force hard reload to clear cache and show updated data
            // Use location.reload(true) for cache bypass, or construct URL with timestamp
            setTimeout(() => {
                // Get current URL and add/update timestamp parameter for cache busting
                const url = new URL(window.location.href);
                url.searchParams.set('t', Date.now());
                // Force reload with cache bypass
                window.location.replace(url.toString());
            }, 500);
        } else {
            const errorMsg = data.message || 'Unknown error';
            console.error('API Error Response:', JSON.stringify(data, null, 2));
            if (data.error_details) {
                console.error('Error Details:', JSON.stringify(data.error_details, null, 2));
            }
            const displayMsg = errorMsg || (data.error_details ? data.error_details.message : 'Unknown error');
            showToast('Error: ' + displayMsg, 'error');
        }
    })
    .catch(error => {
        console.error('Network/Fetch Error:', error);
        console.error('Error stack:', error.stack);
        showToast('Error updating transaction: ' + error.message, 'error');
    });
}

function reverseTransaction(transactionId) {
    showModal(
        'Reverse Transaction',
        'Are you sure you want to reverse this transaction? This will create an opposite transaction to balance the account.',
        'warning',
        function() {
            fetch('<?php echo SITE_URL; ?>/api/admin-reverse-transaction.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ transaction_id: transactionId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Transaction reversed successfully!', 'success');
                    location.reload();
                } else {
                    showToast('Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                showToast('Error reversing transaction: ' + error, 'error');
            });
        }
    );
}

function deleteTransaction(transactionId) {
    showModal(
        'Delete Transaction',
        'Are you sure you want to delete this transaction? This action cannot be undone and requires a reason.',
        'danger',
        function(reason) {
            if (!reason) {
                showToast('Please provide a reason for deletion', 'error');
                return;
            }
            
            fetch('<?php echo SITE_URL; ?>/api/admin-delete-transaction.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    transaction_id: transactionId,
                    reason: reason
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Transaction deleted successfully!', 'success');
                    location.reload();
                } else {
                    showToast('Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                showToast('Error deleting transaction: ' + error, 'error');
            });
        },
        {
            textarea: {
                placeholder: 'Enter reason for deletion...'
            }
        }
    );
}
</script>

