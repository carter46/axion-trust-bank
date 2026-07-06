<?php 
$pageTitle = 'Account Details - ' . getSiteName();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

requireLogin();

// Get account ID from URL
$accountId = $GLOBALS['id'] ?? null;

if (!$accountId) {
    $_SESSION['error'] = 'Account not found';
    redirect('/account');
}

// Get database instance
$db = Database::getInstance();

// Get account details
$accountModel = new Account();
$account = $accountModel->findById($accountId);

if (!$account || $account['user_id'] != $_SESSION['user_id']) {
    $_SESSION['error'] = 'Account not found';
    redirect('/account');
}

// Get recent transactions for this account
$transactionModel = new Transaction();
$transactions = $transactionModel->getAccountTransactions($accountId, 10);

// Get user currency
$userModel = new User();
$user = $userModel->findById($_SESSION['user_id']);
$userCurrency = getUserDisplayCurrency($user);

// Include head
include __DIR__ . '/../../includes/head.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<style>
    /* Override parent content-area styles */
    .main-content-area .content-area {
        background: #f5f5f5 !important;
        padding: 20px !important;
    }

    .page-header {
        margin-bottom: 30px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
    }

    .page-header-content {
        flex: 1;
    }

    .page-header h1 {
        font-size: 32px;
        font-weight: 700;
        color: #032B44;
        margin-bottom: 8px;
    }

    .page-header p {
        color: #666;
        font-size: 16px;
        margin: 0;
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
    }

    .back-button:hover {
        background: #e5e7eb;
        transform: translateX(-4px);
    }

    .account-details-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 30px;
    }

    .account-card {
        background: white;
        border-radius: 16px;
        padding: 32px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .account-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
        padding-bottom: 20px;
        border-bottom: 2px solid #f0f0f0;
    }

    .account-info h2 {
        font-size: 24px;
        color: #032B44;
        margin: 0 0 8px 0;
        font-weight: 600;
    }

    .account-number {
        color: #666;
        font-size: 16px;
        font-family: monospace;
        letter-spacing: 1px;
    }

    .account-balance {
        text-align: right;
    }

    .balance-amount {
        font-size: 32px;
        font-weight: 700;
        color: #059669;
        margin: 0;
    }

    .balance-label {
        color: #666;
        font-size: 14px;
        margin: 0;
    }

    .account-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 20px;
    }

    .stat-item {
        text-align: center;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 12px;
    }

    .stat-value {
        font-size: 24px;
        font-weight: 600;
        color: #032B44;
        margin: 0 0 8px 0;
    }

    .stat-label {
        color: #666;
        font-size: 14px;
        margin: 0;
    }

    .transactions-card {
        background: white;
        border-radius: 16px;
        padding: 32px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .transactions-header {
        display: flex;
        justify-content: between;
        align-items: center;
        margin-bottom: 24px;
        padding-bottom: 20px;
        border-bottom: 2px solid #f0f0f0;
    }

    .transactions-header h3 {
        font-size: 20px;
        color: #032B44;
        margin: 0;
        font-weight: 600;
    }

    .transaction-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .transaction-item:last-child {
        border-bottom: none;
    }

    .transaction-info {
        flex: 1;
    }

    .transaction-description {
        font-weight: 600;
        color: #032B44;
        margin: 0 0 4px 0;
    }

    .transaction-date {
        color: #666;
        font-size: 14px;
        margin: 0;
    }

    .transaction-amount {
        font-weight: 600;
        font-size: 16px;
    }

    .transaction-amount.credit {
        color: #059669;
    }

    .transaction-amount.debit {
        color: #dc2626;
    }

    .no-transactions {
        text-align: center;
        padding: 40px;
        color: #666;
    }

    @media (max-width: 768px) {
        .account-details-grid {
            grid-template-columns: 1fr;
        }
        
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }
        
        .page-header h1 {
            font-size: 28px;
        }
        
        .account-stats {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<div class="main-content-area">
    <div class="content-area">
        <!-- Page Header with Back Button -->
        <div class="page-header">
            <div class="page-header-content">
                <h1>Account Details</h1>
                <p>View your account information and recent transactions</p>
            </div>
            <a href="/account" class="back-button">
                <i class="fas fa-arrow-left"></i>
                Back to My Accounts
            </a>
        </div>

        <!-- Account Details Grid -->
        <div class="account-details-grid">
            <!-- Account Overview -->
            <div class="account-card">
                <div class="account-header">
                    <div class="account-info">
                        <h2><?php echo ucfirst($account['account_type']); ?> Account</h2>
                        <div class="account-number"><?php echo htmlspecialchars($account['account_number']); ?></div>
                    </div>
                    <div class="account-balance">
                        <div class="balance-amount"><?php echo formatAccountBalance($account['balance'], $account, $userCurrency); ?></div>
                        <div class="balance-label">Available Balance</div>
                    </div>
                </div>

                <div class="account-stats">
                    <div class="stat-item">
                        <div class="stat-value"><?php echo ucfirst($account['account_type']); ?></div>
                        <div class="stat-label">Account Type</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?php echo strtoupper($account['currency']); ?></div>
                        <div class="stat-label">Currency</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?php echo ucfirst($account['status']); ?></div>
                        <div class="stat-label">Status</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?php echo date('M j, Y', strtotime($account['created_at'])); ?></div>
                        <div class="stat-label">Opened</div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="account-card">
                <h3 style="margin: 0 0 24px 0; color: #032B44; font-weight: 600;">Quick Actions</h3>
                
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <a href="/transfer" class="btn-primary" style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 24px; background: #1e3a8a; color: white; text-decoration: none; border-radius: 8px; font-weight: 600; transition: background-color 0.3s;">
                        <i class="fas fa-exchange-alt"></i>
                        Transfer Money
                    </a>
                    
                    <a href="/account/statement/<?php echo $account['id']; ?>" class="btn-secondary" style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 24px; background: #f3f4f6; color: #374151; text-decoration: none; border-radius: 8px; font-weight: 600; transition: background-color 0.3s;">
                        <i class="fas fa-file-alt"></i>
                        View Statement
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="transactions-card">
            <div class="transactions-header">
                <h3>Recent Transactions</h3>
            </div>

            <?php if (empty($transactions)): ?>
                <div class="no-transactions">
                    <i class="fas fa-receipt" style="font-size: 48px; color: #d1d5db; margin-bottom: 16px;"></i>
                    <p>No transactions yet</p>
                </div>
            <?php else: ?>
                <?php foreach ($transactions as $transaction): ?>
                    <div class="transaction-item">
                        <div class="transaction-info">
                            <div class="transaction-description">
                                <?php echo htmlspecialchars($transaction['description']); ?>
                            </div>
                            <div class="transaction-date">
                                <?php echo date('M j, Y \a\t g:i A', strtotime($transaction['created_at'])); ?>
                            </div>
                        </div>
                        <div class="transaction-amount <?php echo $transaction['transaction_type']; ?>">
                            <?php echo $transaction['transaction_type'] === 'credit' ? '+' : '-'; ?><?php echo formatTransactionAmountForUser($transaction, $user, 'amount'); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/auth-foot.php'; ?>
