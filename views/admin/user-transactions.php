<?php 
$pageTitle = 'User Transactions - Admin';
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

// Transactions loaded by AdminController with pagination
$transactions = $transactions ?? [];
$totalTransactions = $totalTransactions ?? count($transactions);
$currentPage = $currentPage ?? 1;
$totalPages = $totalPages ?? 1;
$perPage = $perPage ?? 100;

// Get user accounts and total balance (converted like user dashboard)
$accountModel = new Account();
$accounts = $accountModel->getUserAccounts($userId);
$totalUserBalance = getUserTotalBalanceForDisplay($user, $accounts);

// Get user currency
$userCurrency = getUserDisplayCurrency($user);

$expenseCategoryOptions = getExpenseCategoryOptions();
$structuralCategories = getValidStructuralCategories();

include __DIR__ . '/../../includes/head.php';
include __DIR__ . '/../../includes/admin-sidebar.php';
include __DIR__ . '/../../includes/admin-modals.php';
?>

<style>
/* Admin Dashboard Design Pattern Styles */
.content-area {
    background: #f5f7fa;
    min-height: 100vh;
    padding: 20px;
}

.admin-container {
    max-width: 1400px;
    margin: 0 auto;
}

.admin-header {
    background: white;
    border-radius: 16px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 20px;
}

.admin-title {
    font-size: 32px;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 8px;
}

.admin-subtitle {
    color: #6c757d;
    font-size: 16px;
}

.back-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: #f8f9fa;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    color: #2d3748;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
}

.back-btn:hover {
    background: #e9ecef;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.admin-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
    margin-bottom: 30px;
}

.admin-card {
    background: white;
    border-radius: 16px;
    padding: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.admin-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .card-header {
        display: flex !important;
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 10px;
        margin-bottom: 15px;
        width: 100%;
    }
    
    .card-title {
        margin: 0;
        width: 100%;
    }
    
    .card-header .card-icon,
    .admin-card .card-header .card-icon,
    .card-icon.icon-money,
    .card-icon.icon-transactions {
        display: inline-flex !important;
        width: 40px !important;
        height: 40px !important;
        flex: 0 0 40px !important;
        flex-shrink: 0 !important;
        flex-grow: 0 !important;
        max-width: 40px !important;
        min-width: 40px !important;
        align-self: flex-start !important;
        margin: 0 !important;
        padding: 0 !important;
        box-sizing: border-box !important;
    }
}

.card-title {
    font-size: 18px;
    font-weight: 600;
    color: #2d3748;
    margin: 0;
}

.card-icon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    color: white;
    flex-shrink: 0;
    flex-grow: 0;
    max-width: 40px;
    min-width: 40px;
}

.icon-money { background: #10b981; }
.icon-transactions { background: #f59e0b; }

.stat-number {
    font-size: 32px;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 8px;
}

.stat-label {
    color: #6c757d;
    font-size: 14px;
    margin-bottom: 15px;
}

.stat-change {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    font-weight: 500;
}

.stat-change.positive {
    color: #10b981;
}

.transactions-table-container {
    background: white;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    overflow-x: auto;
}

.transactions-table-container h3 {
    color: #2d3748;
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 20px;
}

.transactions-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.transactions-table th {
    background: #f8f9fa;
    padding: 15px;
    text-align: left;
    font-weight: 600;
    color: #374151;
    border-bottom: 2px solid #e5e7eb;
}

.transactions-table td {
    padding: 15px;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: middle;
}

.transaction-ref {
    font-family: monospace;
    font-size: 11px;
    color: #9ca3af;
}

.category-primary {
    font-weight: 600;
    color: #2d3748;
    font-size: 14px;
}

.category-structural {
    display: inline-block;
    font-size: 11px;
    color: #6b7280;
    background: #f3f4f6;
    padding: 2px 8px;
    border-radius: 6px;
    margin-top: 4px;
    text-transform: capitalize;
}

.transaction-type {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.type-credit {
    background: #d1fae5;
    color: #065f46;
}

.type-debit {
    background: #fee2e2;
    color: #991b1b;
}

.transaction-amount {
    font-weight: 700;
    font-size: 16px;
}

.amount-credit {
    color: #059669;
}

.amount-debit {
    color: #dc2626;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-completed {
    background: #d1fae5;
    color: #065f46;
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
}

.status-failed {
    background: #fee2e2;
    color: #991b1b;
}

.action-buttons {
    display: flex;
    gap: 8px;
}

.action-btn {
    padding: 6px 12px;
    border: none;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-edit {
    background: #e0e7ff;
    color: #3730a3;
}

.btn-edit:hover {
    background: #c7d2fe;
}

.btn-delete {
    background: #fee2e2;
    color: #991b1b;
}

.btn-delete:hover {
    background: #fecaca;
}

.bulk-actions-bar {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    padding: 14px 16px;
    margin-bottom: 16px;
    background: #f8f9fa;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
}

.bulk-actions-bar label {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    color: #374151;
    cursor: pointer;
    margin: 0;
}

.bulk-actions-bar input[type="checkbox"] {
    width: 16px;
    height: 16px;
    cursor: pointer;
}

#selectedCount {
    color: #6b7280;
    font-size: 14px;
}

.bulk-delete-btn {
    padding: 8px 16px;
    border: none;
    border-radius: 8px;
    background: #ef4444;
    color: white;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
}

.bulk-delete-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.bulk-delete-btn:not(:disabled):hover {
    background: #dc2626;
}

.tx-select-cell {
    width: 40px;
    text-align: center;
}

.mobile-select-row {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
    padding-bottom: 10px;
    border-bottom: 1px solid #f0f0f0;
}

.mobile-select-row label {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: #6b7280;
    cursor: pointer;
    margin: 0;
}

.pagination-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 24px;
    padding-top: 20px;
    border-top: 1px solid #e5e7eb;
}

.pagination-info {
    color: #6b7280;
    font-size: 14px;
}

.pagination-links {
    display: flex;
    align-items: center;
    gap: 8px;
}

.pagination-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 8px;
    border: 1px solid #d1d5db;
    background: white;
    color: #374151;
    font-weight: 600;
    font-size: 14px;
    text-decoration: none;
    transition: all 0.2s;
}

.pagination-btn:hover {
    background: #f3f4f6;
    border-color: #9ca3af;
}

.pagination-btn.disabled {
    opacity: 0.45;
    pointer-events: none;
}

.pagination-current {
    padding: 8px 12px;
    font-size: 14px;
    color: #374151;
    font-weight: 600;
}

.btn-reverse {
    background: #fef3c7;
    color: #92400e;
}

.btn-reverse:hover {
    background: #fde68a;
}

.transactions-table-container {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    width: 100%;
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
    color: #6b7280;
    margin-bottom: 4px;
    line-height: 1.3;
}

.transaction-item-ref {
    font-family: monospace;
    font-size: 11px;
    color: #9ca3af;
    margin-bottom: 0;
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
}

.transaction-item-footer .action-btn {
    padding: 8px 16px;
    font-size: 13px;
    border-radius: 6px;
    font-weight: 500;
    border: 1px solid;
    cursor: pointer;
    transition: all 0.2s;
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

@media (max-width: 768px) {
    .content-area {
        padding: 10px;
    }
    
    .admin-header {
        padding: 20px;
        flex-direction: column;
        align-items: flex-start;
    }
    
    .admin-title {
        font-size: 24px;
    }
    
    .admin-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .admin-card {
        padding: 20px;
    }
    
    .card-header {
        display: flex !important;
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 10px;
        margin-bottom: 15px;
        width: 100%;
    }
    
    .card-title {
        margin: 0;
        width: 100%;
    }
    
    .card-header .card-icon,
    .admin-card .card-header .card-icon,
    .card-icon.icon-money,
    .card-icon.icon-transactions {
        display: inline-flex !important;
        width: 40px !important;
        height: 40px !important;
        flex: 0 0 40px !important;
        flex-shrink: 0 !important;
        flex-grow: 0 !important;
        max-width: 40px !important;
        min-width: 40px !important;
        align-self: flex-start !important;
        margin: 0 !important;
        padding: 0 !important;
        box-sizing: border-box !important;
    }
    
    /* Hide table on mobile */
    .transactions-table {
        display: none !important;
    }
    
    /* Show mobile cards on mobile */
    .mobile-transactions {
        display: block;
    }
    
    .transactions-table-container {
        padding: 20px;
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
    
    .modal-container {
        max-width: 95% !important;
        margin: 10px;
    }
}
</style>

<div class="admin-container">
    <!-- Admin Header -->
    <div class="admin-header">
        <div class="header-left">
            <a href="<?php echo SITE_URL; ?>/admin/user/<?php echo $user['id']; ?>" class="back-btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                    <path d="M15 6l-6 6 6 6" stroke="currentColor" stroke-width="2"/>
                </svg>
                Back to User
            </a>
            <div>
                <h1 class="admin-title">User Transactions</h1>
                <p class="admin-subtitle">Transaction history for <?php echo htmlspecialchars($user['full_name']); ?></p>
            </div>
        </div>
    </div>

    <!-- Statistics Grid -->
    <div class="admin-grid">
        <div class="admin-card">
            <div class="card-header">
                <h3 class="card-title">Total Balance</h3>
                <div class="card-icon icon-money">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
            <div class="stat-number"><?php echo formatUserTotalBalance($user, $accounts); ?></div>
            <div class="stat-label">Across all active accounts</div>
            <div class="stat-change positive">
                <i class="fas fa-wallet"></i>
                <span>Current balance</span>
            </div>
        </div>

        <div class="admin-card">
            <div class="card-header">
                <h3 class="card-title">Total Transactions</h3>
                <div class="card-icon icon-transactions">
                    <i class="fas fa-exchange-alt"></i>
                </div>
            </div>
            <div class="stat-number"><?php echo number_format($totalTransactions); ?></div>
            <div class="stat-label">All time transactions</div>
            <div class="stat-change positive">
                <i class="fas fa-list"></i>
                <span>Transaction history</span>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="transactions-table-container">
        <h3>All Transactions</h3>
        <?php if ($totalTransactions > 0): ?>
            <p style="color:#6b7280;font-size:14px;margin:-8px 0 16px;">
                Page <?php echo (int)$currentPage; ?> of <?php echo (int)$totalPages; ?>
                — showing <?php echo count($transactions); ?> of <?php echo number_format($totalTransactions); ?> transactions
            </p>
        <?php endif; ?>

        <?php if (!empty($transactions)): ?>
            <div class="bulk-actions-bar">
                <label>
                    <input type="checkbox" id="selectAllTransactions" aria-label="Select all on this page">
                    Select all on this page
                </label>
                <span id="selectedCount">0 selected</span>
                <button type="button" id="bulkDeleteBtn" class="bulk-delete-btn" disabled>
                    <i class="fas fa-trash"></i> Delete selected
                </button>
            </div>

            <table class="transactions-table">
                <thead>
                    <tr>
                        <th class="tx-select-cell">
                            <input type="checkbox" id="selectAllTransactionsHeader" aria-label="Select all" title="Select all">
                        </th>
                        <th>Category</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Account</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $transaction): ?>
                        <tr>
                            <td class="tx-select-cell">
                                <input type="checkbox" class="tx-select" value="<?php echo (int)$transaction['id']; ?>" aria-label="Select transaction">
                            </td>
                            <td>
                                <div class="category-primary" title="Ref: <?php echo htmlspecialchars($transaction['transaction_ref']); ?>">
                                    <?php echo htmlspecialchars(formatExpenseCategoryLabel($transaction['expense_category'] ?? null)); ?>
                                </div>
                                <span class="category-structural"><?php echo htmlspecialchars(formatStructuralCategoryLabel($transaction['category'] ?? null)); ?></span>
                                <div class="transaction-ref"><?php echo htmlspecialchars($transaction['transaction_ref']); ?></div>
                            </td>
                            <td>
                                <span class="transaction-type type-<?php echo $transaction['transaction_type']; ?>">
                                    <?php echo ucfirst($transaction['transaction_type']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="transaction-amount amount-<?php echo $transaction['transaction_type']; ?>">
                                    <?php echo $transaction['transaction_type'] === 'credit' ? '+' : '-'; ?>
                                    <?php echo formatTransactionAmountForUser($transaction, $user, 'amount'); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($transaction['account_number']): ?>
                                    <div>
                                        <div style="font-weight: 600;"><?php echo htmlspecialchars($transaction['account_number']); ?></div>
                                        <div style="font-size: 12px; color: #6b7280;"><?php echo ucfirst($transaction['account_type']); ?></div>
                                    </div>
                                <?php else: ?>
                                    <span style="color: #6b7280;">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="max-width: 200px; word-wrap: break-word;">
                                    <?php echo htmlspecialchars($transaction['description'] ?? 'N/A'); ?>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $transaction['status']; ?>">
                                    <?php echo ucfirst($transaction['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div>
                                    <div style="font-weight: 600;"><?php echo date('M d, Y', strtotime($transaction['created_at'])); ?></div>
                                    <div style="font-size: 12px; color: #6b7280;"><?php echo date('H:i A', strtotime($transaction['created_at'])); ?></div>
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button onclick="editTransaction(<?php echo $transaction['id']; ?>)" class="action-btn btn-edit" title="Edit Transaction">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <?php if ($transaction['status'] === 'completed'): ?>
                                        <button onclick="reverseTransaction(<?php echo $transaction['id']; ?>)" class="action-btn btn-reverse" title="Reverse Transaction">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    <?php endif; ?>
                                    <button onclick="deleteTransaction(<?php echo $transaction['id']; ?>)" class="action-btn btn-delete" title="Delete Transaction">
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
                    <div class="transaction-item" data-transaction-id="<?php echo (int)$transaction['id']; ?>">
                        <div class="mobile-select-row">
                            <label>
                                <input type="checkbox" class="tx-select" value="<?php echo (int)$transaction['id']; ?>">
                                Select
                            </label>
                        </div>
                        <div class="transaction-item-header">
                            <div class="transaction-item-amount amount-<?php echo $transaction['transaction_type']; ?>">
                                <?php echo $transaction['transaction_type'] === 'credit' ? '+' : '-'; ?>
                                <?php echo formatTransactionAmountForUser($transaction, $user, 'amount'); ?>
                            </div>
                            <div class="transaction-item-left">
                                <div class="transaction-item-title"><?php echo htmlspecialchars($transaction['description'] ?? 'Transaction'); ?></div>
                                <div class="category-primary" style="font-size:13px;margin-top:4px;">
                                    <?php echo htmlspecialchars(formatExpenseCategoryLabel($transaction['expense_category'] ?? null)); ?>
                                    <span class="category-structural"><?php echo htmlspecialchars(formatStructuralCategoryLabel($transaction['category'] ?? null)); ?></span>
                                </div>
                                <div class="transaction-item-ref"><?php echo htmlspecialchars($transaction['transaction_ref']); ?></div>
                            </div>
                        </div>
                        <div class="transaction-item-body">
                            <div class="transaction-item-field">
                                <div class="transaction-item-label">Type</div>
                                <div class="transaction-item-value">
                                    <span class="transaction-type type-<?php echo $transaction['transaction_type']; ?>">
                                        <?php echo ucfirst($transaction['transaction_type']); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="transaction-item-field">
                                <div class="transaction-item-label">Status</div>
                                <div class="transaction-item-value">
                                    <span class="status-badge status-<?php echo $transaction['status']; ?>">
                                        <?php echo ucfirst($transaction['status']); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="transaction-item-field">
                                <div class="transaction-item-label">Account</div>
                                <div class="transaction-item-value">
                                    <?php if ($transaction['account_number']): ?>
                                        <?php echo htmlspecialchars($transaction['account_number']); ?>
                                        <div style="font-size: 11px; color: #6b7280; margin-top: 2px;"><?php echo ucfirst($transaction['account_type']); ?></div>
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
                            <?php if ($transaction['status'] === 'completed'): ?>
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

            <?php
            $paginationBase = SITE_URL . '/admin/user/' . (int)$userId . '?action=transactions';
            ?>
            <div class="pagination-bar">
                <div class="pagination-info">
                    Page <?php echo (int)$currentPage; ?> of <?php echo (int)$totalPages; ?>
                    (<?php echo number_format($totalTransactions); ?> total)
                </div>
                <div class="pagination-links">
                    <?php if ($currentPage > 1): ?>
                        <a class="pagination-btn" href="<?php echo $paginationBase . '&page=' . ($currentPage - 1); ?>">
                            <i class="fas fa-chevron-left"></i> Previous
                        </a>
                    <?php else: ?>
                        <span class="pagination-btn disabled"><i class="fas fa-chevron-left"></i> Previous</span>
                    <?php endif; ?>

                    <span class="pagination-current"><?php echo (int)$currentPage; ?> / <?php echo (int)$totalPages; ?></span>

                    <?php if ($currentPage < $totalPages): ?>
                        <a class="pagination-btn" href="<?php echo $paginationBase . '&page=' . ($currentPage + 1); ?>">
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php else: ?>
                        <span class="pagination-btn disabled">Next <i class="fas fa-chevron-right"></i></span>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 60px 20px; color: #6b7280;">
                <i class="fas fa-receipt" style="font-size: 48px; margin-bottom: 20px; opacity: 0.5;"></i>
                <h3>No Transactions Found</h3>
                <p>This user hasn't made any transactions yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
const ADMIN_USER_ID = <?php echo (int)$userId; ?>;
const EXPENSE_CATEGORY_OPTIONS = <?php echo json_encode($expenseCategoryOptions, JSON_UNESCAPED_UNICODE); ?>;
const STRUCTURAL_CATEGORIES = <?php echo json_encode($structuralCategories); ?>;

function getSelectedTransactionIds() {
    const ids = Array.from(document.querySelectorAll('.tx-select:checked'))
        .map(cb => parseInt(cb.value, 10))
        .filter(id => id > 0);
    return [...new Set(ids)];
}

function getAllTransactionIds() {
    const ids = Array.from(document.querySelectorAll('.tx-select'))
        .map(cb => parseInt(cb.value, 10))
        .filter(id => id > 0);
    return [...new Set(ids)];
}

function updateBulkSelectionUi() {
    const checkedIds = getSelectedTransactionIds();
    const allIds = getAllTransactionIds();
    const count = checkedIds.length;
    const total = allIds.length;

    const countEl = document.getElementById('selectedCount');
    const bulkBtn = document.getElementById('bulkDeleteBtn');
    const selectAll = document.getElementById('selectAllTransactions');
    const selectAllHeader = document.getElementById('selectAllTransactionsHeader');

    if (countEl) countEl.textContent = count + ' selected';
    if (bulkBtn) bulkBtn.disabled = count === 0;

    const allChecked = total > 0 && count === total;
    const someChecked = count > 0 && count < total;

    if (selectAll) {
        selectAll.checked = allChecked;
        selectAll.indeterminate = someChecked;
    }
    if (selectAllHeader) {
        selectAllHeader.checked = allChecked;
        selectAllHeader.indeterminate = someChecked;
    }
}

function setAllTransactionCheckboxes(checked) {
    getAllTransactionIds().forEach(id => {
        document.querySelectorAll('.tx-select[value="' + id + '"]').forEach(cb => { cb.checked = checked; });
    });
    updateBulkSelectionUi();
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.tx-select').forEach(cb => {
        cb.addEventListener('change', function() {
            document.querySelectorAll('.tx-select[value="' + this.value + '"]').forEach(other => {
                other.checked = this.checked;
            });
            updateBulkSelectionUi();
        });
    });

    const selectAll = document.getElementById('selectAllTransactions');
    const selectAllHeader = document.getElementById('selectAllTransactionsHeader');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            setAllTransactionCheckboxes(selectAll.checked);
        });
    }
    if (selectAllHeader) {
        selectAllHeader.addEventListener('change', function() {
            setAllTransactionCheckboxes(selectAllHeader.checked);
        });
    }

    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', bulkDeleteTransactions);
    }

    updateBulkSelectionUi();
});

function bulkDeleteTransactions() {
    const ids = getSelectedTransactionIds();
    if (!ids.length) {
        showToast('Select at least one transaction to delete', 'error');
        return;
    }

    showModal(
        'Delete ' + ids.length + ' transaction(s)',
        'Delete the selected transactions? Account balances will be updated (debits credited back, credits removed). This cannot be undone.',
        'danger',
        function(reason) {
            if (!reason) {
                showToast('Please provide a reason for deletion', 'error');
                return;
            }

            fetch('<?php echo SITE_URL; ?>/api/admin-bulk-delete-transactions.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    transaction_ids: ids,
                    user_id: ADMIN_USER_ID,
                    reason: reason
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message || 'Transactions deleted successfully!', 'success');
                    location.reload();
                } else {
                    showToast('Error: ' + (data.message || 'Delete failed'), 'error');
                }
            })
            .catch(error => {
                showToast('Error deleting transactions: ' + error, 'error');
            });
        },
        {
            textarea: {
                placeholder: 'Enter reason for deletion...'
            }
        }
    );
}

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
    const escapeHtml = (text) => {
        const div = document.createElement('div');
        div.textContent = text ?? '';
        return div.innerHTML;
    };

    let meta = {};
    try {
        meta = typeof transaction.metadata === 'string'
            ? JSON.parse(transaction.metadata || '{}')
            : (transaction.metadata || {});
    } catch (e) {
        meta = {};
    }

    const safeAmount = parseFloat(transaction.amount) || 0;
    const safeFee = parseFloat(transaction.fee) || 0;
    const safeDate = escapeHtml(dateValue || '');
    const safeTime = escapeHtml(timeValue || '');
    const safeDescription = escapeHtml(transaction.description || '');
    const safeStatus = escapeHtml(transaction.status || 'completed');
    const safeId = parseInt(transaction.id) || 0;
    const safeCategory = escapeHtml(transaction.category || 'other');
    const safeExpenseCategory = escapeHtml(transaction.expense_category || '');
    const safeTxnType = escapeHtml(transaction.transaction_type || 'debit');
    const safeRecipientName = escapeHtml(transaction.recipient_name || '');
    const safeRecipientAccount = escapeHtml(transaction.recipient_account || '');
    const safeRecipientBank = escapeHtml(transaction.recipient_bank || '');
    const transferScope = escapeHtml(meta.transfer_scope || meta.transfer_type || 'domestic');
    const transferCountry = escapeHtml(meta.country || meta.recipient_country || '');
    const showTransferFields = transaction.category === 'transfer'
        || (transaction.category === 'deposit' && !!meta.transfer_scope)
        || ['domestic', 'international', 'internal'].includes(meta.transfer_scope || meta.transfer_type || '');

    const expenseOptionsHtml = EXPENSE_CATEGORY_OPTIONS.map(opt => {
        const selected = safeExpenseCategory === opt.value ? ' selected' : '';
        return `<option value="${escapeHtml(opt.value)}"${selected}>${opt.icon} ${escapeHtml(opt.label)}</option>`;
    }).join('');

    const structuralOptionsHtml = STRUCTURAL_CATEGORIES.map(cat => {
        const selected = safeCategory === cat ? ' selected' : '';
        return `<option value="${escapeHtml(cat)}"${selected}>${escapeHtml(cat.charAt(0).toUpperCase() + cat.slice(1))}</option>`;
    }).join('');

    const transferSectionStyle = showTransferFields ? '' : 'display:none;';

    const modal = document.createElement('div');
    modal.id = 'editTransactionModal';
    modal.className = 'modal-overlay';
    modal.style.display = 'flex';
    modal.innerHTML = `
        <div class="modal-container" style="max-width: 640px; max-height: 90vh; overflow-y: auto;">
            <div class="modal-header">
                <h3>Edit Transaction</h3>
                <button class="modal-close" onclick="closeEditTransactionModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="editTransactionForm">
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 600;">Transaction Type</label>
                        <input type="text" value="${safeTxnType}" readonly
                               style="width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 5px; background: #f9fafb; text-transform: capitalize;">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 15px;">
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">Structural Category</label>
                            <select id="editCategory" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                                ${structuralOptionsHtml}
                            </select>
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">Transaction Category</label>
                            <select id="editExpenseCategory" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                                <option value="">General</option>
                                ${expenseOptionsHtml}
                            </select>
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 15px;">
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">Amount *</label>
                            <input type="number" id="editAmount" value="${safeAmount}" step="0.01" required
                                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">Fee</label>
                            <input type="number" id="editFee" value="${safeFee}" step="0.01" min="0"
                                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 600;">Status *</label>
                        <select id="editStatus" required
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                            <option value="completed" ${safeStatus === 'completed' ? 'selected' : ''}>✅ Completed</option>
                            <option value="pending" ${safeStatus === 'pending' ? 'selected' : ''}>⏳ Pending</option>
                            <option value="failed" ${safeStatus === 'failed' ? 'selected' : ''}>❌ Failed</option>
                            <option value="on_hold" ${safeStatus === 'on_hold' ? 'selected' : ''}>🟣 On Hold</option>
                            <option value="processing" ${safeStatus === 'processing' ? 'selected' : ''}>🔄 Processing</option>
                        </select>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 15px;">
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">Date *</label>
                            <input type="date" id="editDate" value="${safeDate}" required
                                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">Time *</label>
                            <input type="time" id="editTime" value="${safeTime}" required
                                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 600;">Description *</label>
                        <textarea id="editDescription" required
                                  style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; min-height: 80px; resize: vertical;">${safeDescription}</textarea>
                    </div>
                    <div id="editTransferSection" style="${transferSectionStyle} border-top: 1px solid #e5e7eb; padding-top: 15px; margin-top: 5px;">
                        <h4 style="margin: 0 0 12px; font-size: 15px;">Transfer Details</h4>
                        <div style="margin-bottom: 12px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">Transfer Scope</label>
                            <select id="editTransferScope" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                                <option value="domestic" ${transferScope === 'domestic' ? 'selected' : ''}>Domestic</option>
                                <option value="international" ${transferScope === 'international' ? 'selected' : ''}>International</option>
                                <option value="internal" ${transferScope === 'internal' ? 'selected' : ''}>Internal</option>
                            </select>
                        </div>
                        <div style="margin-bottom: 12px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">Recipient Name</label>
                            <input type="text" id="editRecipientName" value="${safeRecipientName}"
                                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                        <div style="margin-bottom: 12px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">Recipient Account</label>
                            <input type="text" id="editRecipientAccount" value="${safeRecipientAccount}"
                                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                        <div style="margin-bottom: 12px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">Recipient Bank</label>
                            <input type="text" id="editRecipientBank" value="${safeRecipientBank}"
                                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                        <div id="editIntlFields" style="${transferScope === 'international' ? '' : 'display:none;'}">
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">Country (ISO)</label>
                            <input type="text" id="editTransferCountry" value="${transferCountry}" maxlength="3"
                                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
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

    const categoryEl = document.getElementById('editCategory');
    const transferSection = document.getElementById('editTransferSection');
    const scopeEl = document.getElementById('editTransferScope');
    const intlFields = document.getElementById('editIntlFields');

    function toggleTransferSection() {
        const cat = categoryEl.value;
        const show = cat === 'transfer' || cat === 'deposit';
        transferSection.style.display = show ? '' : 'none';
    }
    categoryEl.addEventListener('change', toggleTransferSection);
    scopeEl.addEventListener('change', function() {
        intlFields.style.display = scopeEl.value === 'international' ? '' : 'none';
    });
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
    const fee = parseFloat(document.getElementById('editFee').value) || 0;
    const status = document.getElementById('editStatus').value;
    const date = document.getElementById('editDate').value;
    const time = document.getElementById('editTime').value;
    const description = document.getElementById('editDescription').value.trim();
    const category = document.getElementById('editCategory').value;
    const expenseCategory = document.getElementById('editExpenseCategory').value;
    const transferSection = document.getElementById('editTransferSection');
    const showTransfer = transferSection && transferSection.style.display !== 'none';
    
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
    
    const datetime = date + ' ' + time + ':00';

    const payload = {
        transaction_id: transactionId,
        amount: amount,
        fee: fee,
        status: status,
        description: description,
        date: datetime,
        category: category,
        expense_category: expenseCategory || null
    };

    if (showTransfer) {
        payload.recipient_name = document.getElementById('editRecipientName').value.trim();
        payload.recipient_account = document.getElementById('editRecipientAccount').value.trim();
        payload.recipient_bank = document.getElementById('editRecipientBank').value.trim();
        payload.transfer_scope = document.getElementById('editTransferScope').value;
        const country = document.getElementById('editTransferCountry');
        if (country && country.value.trim()) {
            payload.metadata = { country: country.value.trim().toUpperCase() };
        }
    }

    fetch('<?php echo SITE_URL; ?>/api/admin-edit-transaction.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
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
