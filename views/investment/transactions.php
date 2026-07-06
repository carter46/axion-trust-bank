<?php 
$pageTitle = 'Investment Transaction History - ' . getSiteName();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

requireLogin();

// Get user currency
require_once __DIR__ . '/../../models/User.php';
$userModel = new User();
$user = $userModel->findById($_SESSION['user_id']);
$userCurrency = getUserDisplayCurrency($user);

// Include head and sidebar
include __DIR__ . '/../../includes/head.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<?php include __DIR__ . '/../../includes/restricted-banner.php'; ?>

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

.transactions-table {
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

.amount {
    font-weight: 600;
    color: #032B44;
}

.amount.credit {
    color: #10b981;
}

.amount.debit {
    color: #ef4444;
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

.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: #e5e7eb;
    color: #374151;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    margin-bottom: 20px;
    transition: all 0.3s;
}

.btn-back:hover {
    background: #d1d5db;
    transform: translateX(-4px);
}

@media (max-width: 768px) {
    .transactions-table {
        padding: 20px;
    }
    
    table {
        font-size: 14px;
    }
    
    th, td {
        padding: 12px 8px;
    }
}
</style>

<a href="<?php echo SITE_URL; ?>/investment" class="btn-back">
    <i class="fas fa-arrow-left"></i> Back to Investments
</a>

<div class="page-header">
    <h1>Investment Transaction History</h1>
    <p style="color: #666;">View all your investment funding, withdrawals, and transactions</p>
</div>

<div class="transactions-table">
    <?php if (empty($allTransactions)): ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h3>No Transactions Yet</h3>
            <p>Your investment transactions will appear here once you start funding or investing.</p>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th>Reference</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allTransactions as $txn): ?>
                <tr>
                    <td><?php echo date('M d, Y H:i', strtotime($txn['date'])); ?></td>
                    <td>
                        <?php 
                        $typeLabel = 'Funding';
                        if ($txn['type'] === 'bank_transaction') {
                            $typeLabel = $txn['transaction_type'] === 'credit' ? 'Credit' : 'Debit';
                        }
                        echo $typeLabel;
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($txn['description']); ?></td>
                    <td class="amount <?php echo (isset($txn['transaction_type']) && $txn['transaction_type'] === 'credit') || $txn['type'] === 'funding' ? 'credit' : 'debit'; ?>">
                        <?php 
                        $sign = ((isset($txn['transaction_type']) && $txn['transaction_type'] === 'credit') || $txn['type'] === 'funding') ? '+' : '-';
                        echo $sign . formatTransactionAmountForUser($txn, $user, 'amount'); 
                        ?>
                    </td>
                    <td>
                        <?php 
                        if ($txn['type'] === 'funding') {
                            if ($txn['method'] === 'bank_balance') {
                                echo 'Bank Transfer';
                            } else {
                                echo strtoupper($txn['crypto_currency'] ?? 'Crypto');
                            }
                        } else {
                            echo 'Bank Account';
                        }
                        ?>
                    </td>
                    <td>
                        <span class="status-badge status-<?php echo $txn['status']; ?>">
                            <?php echo ucfirst($txn['status']); ?>
                        </span>
                    </td>
                    <td>
                        <?php 
                        if (!empty($txn['crypto_tx_hash'])) {
                            echo '<small style="font-family: monospace; color: #6b7280;">' . substr($txn['crypto_tx_hash'], 0, 16) . '...</small>';
                        } elseif (!empty($txn['reference'])) {
                            echo '<small style="color: #6b7280;">' . htmlspecialchars($txn['reference']) . '</small>';
                        } else {
                            echo '<small style="color: #9ca3af;">—</small>';
                        }
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/mobile-nav.php'; ?>

