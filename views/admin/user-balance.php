<?php
$pageTitle = 'User Balance Management - Admin - SecureBank';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

// Get user data
$userId = $GLOBALS['id'];
$userModel = new User();
$user = $userModel->findById($userId);

if (!$user) {
    $_SESSION['error'] = 'User not found';
    redirect('/admin/users');
}

// Get user currency
$userCurrency = getUserDisplayCurrency($user);

// Get user accounts
$accountModel = new Account();
$accounts = $accountModel->getUserAccounts($userId);

include __DIR__ . '/../../includes/head.php';
include __DIR__ . '/../../includes/admin-sidebar.php';
include __DIR__ . '/../../includes/admin-modals.php';
?>

<style>
.balance-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 24px;
}

.header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 32px;
}

.back-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    color: #0f172a;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
    transition: all 0.2s;
}

.back-btn:hover {
    background: #f8fafc;
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.12);
}

.user-header {
    display: flex;
    align-items: center;
    gap: 16px;
}

.avatar {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 20px;
}

.user-info h1 {
    font-size: 24px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}

.user-info .email {
    color: #64748b;
    font-size: 14px;
    margin-top: 4px;
}

.balance-grid {
    display: grid;
    gap: 24px;
}

.balance-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.balance-card h3 {
    font-size: 18px;
    font-weight: 600;
    color: #0f172a;
    margin: 0 0 16px 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.balance-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: #1e293b;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}

.account-list {
    display: grid;
    gap: 16px;
}

.account-item {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.account-info h4 {
    margin: 0 0 4px 0;
    color: #1e293b;
    font-size: 16px;
    font-weight: 600;
}

.account-info p {
    margin: 0;
    color: #64748b;
    font-size: 14px;
}

.account-balance {
    text-align: right;
}

.balance-amount {
    font-size: 20px;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
}

.balance-type {
    font-size: 12px;
    color: #64748b;
    margin: 0;
    text-transform: uppercase;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 14px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-primary {
    background: #3b82f6;
    color: white;
}

.btn-primary:hover {
    background: #2563eb;
    transform: translateY(-1px);
}

.btn-success {
    background: #10b981;
    color: white;
}

.btn-success:hover {
    background: #059669;
    transform: translateY(-1px);
}

.btn-danger {
    background: #ef4444;
    color: white;
}

.btn-danger:hover {
    background: #dc2626;
    transform: translateY(-1px);
}

.btn-warning {
    background: #f59e0b;
    color: white;
}

.btn-warning:hover {
    background: #d97706;
    transform: translateY(-1px);
}

.balance-summary {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    color: white;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 24px;
}

.balance-summary h3 {
    margin: 0 0 16px 0;
    color: white;
    font-size: 18px;
    font-weight: 600;
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}

.summary-item {
    text-align: center;
}

.summary-value {
    font-size: 24px;
    font-weight: 700;
    margin: 0;
}

.summary-label {
    font-size: 12px;
    opacity: 0.8;
    margin: 4px 0 0 0;
    text-transform: uppercase;
}

.no-accounts {
    text-align: center;
    padding: 40px;
    color: #64748b;
}

.no-accounts h4 {
    margin: 0 0 8px 0;
    color: #374151;
}

.no-accounts p {
    margin: 0;
    font-size: 14px;
}

@media (max-width: 768px) {
    .balance-container {
        padding: 16px;
    }
    
    .header {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
    
    .user-header {
        width: 100%;
    }
    
    .account-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
    
    .account-balance {
        text-align: left;
        width: 100%;
    }
    
    .summary-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="balance-container">
    <!-- Header -->
    <div class="header">
        <a href="/admin/user/<?php echo $userId; ?>" class="back-btn">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                <path d="M15 6l-6 6 6 6" stroke="currentColor" stroke-width="2"/>
            </svg>
            Back to User
        </a>
        
        <div class="user-header">
            <div class="avatar"><?php echo strtoupper(substr($user['full_name'], 0, 2)); ?></div>
            <div class="user-info">
                <h1><?php echo htmlspecialchars($user['full_name']); ?></h1>
                <div class="email"><?php echo htmlspecialchars($user['email']); ?></div>
            </div>
        </div>
    </div>

    <!-- Balance Summary -->
    <div class="balance-summary">
        <h3>Total Account Balances</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <p class="summary-value"><?php echo count($accounts); ?></p>
                <p class="summary-label">Active Accounts</p>
            </div>
            <div class="summary-item">
                <p class="summary-value">$<?php 
                    $totalBalance = 0;
                    foreach ($accounts as $account) {
                        $totalBalance += floatval($account['balance']);
                    }
                    echo number_format($totalBalance, 2);
                ?></p>
                <p class="summary-label">Total Balance</p>
            </div>
            <div class="summary-item">
                <p class="summary-value"><?php 
                    $checkingCount = 0;
                    $savingsCount = 0;
                    foreach ($accounts as $account) {
                        if ($account['account_type'] === 'checking') $checkingCount++;
                        if ($account['account_type'] === 'savings') $savingsCount++;
                    }
                    echo $checkingCount;
                ?></p>
                <p class="summary-label">Checking Accounts</p>
            </div>
            <div class="summary-item">
                <p class="summary-value"><?php echo $savingsCount; ?></p>
                <p class="summary-label">Savings Accounts</p>
            </div>
        </div>
    </div>

    <div class="balance-grid">
        <!-- Account List -->
        <div class="balance-card">
            <h3>
                <div class="balance-icon">💳</div>
                User Accounts
            </h3>
            
            <?php if (!empty($accounts)): ?>
                <div class="account-list">
                    <?php foreach ($accounts as $account): ?>
                    <div class="account-item">
                        <div class="account-info">
                            <h4><?php echo ucfirst($account['account_type']); ?> Account</h4>
                            <p>Account #<?php echo htmlspecialchars($account['account_number']); ?></p>
                        </div>
                        <div class="account-balance">
                            <p class="balance-amount"><?php echo formatAccountBalance(floatval($account['balance']), $account, $userCurrency); ?></p>
                            <p class="balance-type"><?php echo ucfirst($account['account_type']); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-accounts">
                    <h4>No Accounts Found</h4>
                    <p>This user doesn't have any accounts yet.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Balance Adjustment -->
        <div class="balance-card">
            <h3>
                <div class="balance-icon">⚖️</div>
                Balance Adjustments
            </h3>
            
            <?php if (!empty($accounts)): ?>
                <div style="margin-bottom: 20px;">
                    <p style="color: #6b7280; font-size: 14px; margin: 0;">
                        Adjust the balance for any of this user's accounts. Use positive amounts to credit and negative amounts to debit.
                    </p>
                </div>

                <div style="display: flex; flex-wrap: wrap; gap: 12px;">
                    <?php foreach ($accounts as $account): ?>
                    <button class="btn btn-primary" onclick="adjustAccountBalance(<?php echo $account['id']; ?>, '<?php echo htmlspecialchars($account['account_number']); ?>', '<?php echo $account['account_type']; ?>')">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <path d="M12 6v6m0 0v6m0-6h6m-6 0H6" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        Adjust <?php echo ucfirst($account['account_type']); ?> #<?php echo htmlspecialchars($account['account_number']); ?>
                    </button>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-accounts">
                    <h4>Cannot Adjust Balances</h4>
                    <p>User must have accounts before balance adjustments can be made.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Quick Actions -->
        <div class="balance-card">
            <h3>
                <div class="balance-icon">🚀</div>
                Quick Actions
            </h3>
            
            <div style="display: flex; flex-wrap: wrap; gap: 12px;">
                <button class="btn btn-success" onclick="creditAllAccounts()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M12 6v6m0 0v6m0-6h6m-6 0H6" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    Credit All Accounts
                </button>
                
                <button class="btn btn-warning" onclick="freezeAllAccounts()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M12 2v20M2 12h20" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    Freeze All Accounts
                </button>
                
                <button class="btn btn-primary" onclick="unfreezeAllAccounts()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    Unfreeze All Accounts
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function adjustAccountBalance(accountId, accountNumber, accountType) {
    showModal(
        'Adjust Account Balance',
        `Adjust the balance for ${accountType} account #${accountNumber}. Use positive amounts to add money, negative amounts to subtract.`,
        'warning',
        function(amount) {
            if (!amount || isNaN(amount)) {
                showToast('Please enter a valid amount', 'error');
                return;
            }
            
            const userId = <?php echo $userId; ?>;
            
            fetch('/api/admin-adjust-balance.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    user_id: userId,
                    account_id: accountId,
                    amount: parseFloat(amount),
                    description: `Admin balance adjustment for ${accountType} account #${accountNumber}`
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Balance adjusted successfully', 'success');
                    location.reload();
                } else {
                    showToast('Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('An error occurred while adjusting balance', 'error');
            });
        },
        {
            input: {
                placeholder: 'Enter amount (e.g., 100 or -50)',
                type: 'number'
            }
        }
    );
}

function creditAllAccounts() {
    showModal(
        'Credit All Accounts',
        'Add the same amount to all of this user\'s accounts. Enter a positive amount.',
        'warning',
        function(amount) {
            if (!amount || isNaN(amount) || parseFloat(amount) <= 0) {
                showToast('Please enter a valid positive amount', 'error');
                return;
            }
            
            const userId = <?php echo $userId; ?>;
            const accounts = <?php echo json_encode($accounts); ?>;
            
            // Process each account
            let completed = 0;
            let errors = 0;
            
            accounts.forEach(account => {
                fetch('/api/admin-adjust-balance.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        user_id: userId,
                        account_id: account.id,
                        amount: parseFloat(amount),
                        description: `Bulk credit to ${account.account_type} account #${account.account_number}`
                    })
                })
                .then(response => response.json())
                .then(data => {
                    completed++;
                    if (!data.success) errors++;
                    
                    if (completed === accounts.length) {
                        if (errors === 0) {
                            showToast(`Successfully credited all ${accounts.length} accounts`, 'success');
                            location.reload();
                        } else {
                            showToast(`Completed with ${errors} errors`, 'warning');
                            location.reload();
                        }
                    }
                })
                .catch(error => {
                    completed++;
                    errors++;
                    console.error('Error:', error);
                    
                    if (completed === accounts.length) {
                        showToast(`Completed with ${errors} errors`, 'warning');
                        location.reload();
                    }
                });
            });
        },
        {
            input: {
                placeholder: 'Enter amount to credit',
                type: 'number'
            }
        }
    );
}

function freezeAllAccounts() {
    showModal(
        'Freeze All Accounts',
        'Are you sure you want to freeze all accounts for this user? They will not be able to perform transactions until unfrozen.',
        'danger',
        function() {
            const userId = <?php echo $userId; ?>;
            const accounts = <?php echo json_encode($accounts); ?>;
            
            // Process each account
            let completed = 0;
            let errors = 0;
            
            accounts.forEach(account => {
                fetch('/api/admin-freeze-account.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        account_id: account.id,
                        freeze: true
                    })
                })
                .then(response => response.json())
                .then(data => {
                    completed++;
                    if (!data.success) errors++;
                    
                    if (completed === accounts.length) {
                        if (errors === 0) {
                            showToast(`Successfully froze all ${accounts.length} accounts`, 'success');
                            location.reload();
                        } else {
                            showToast(`Completed with ${errors} errors`, 'warning');
                            location.reload();
                        }
                    }
                })
                .catch(error => {
                    completed++;
                    errors++;
                    console.error('Error:', error);
                    
                    if (completed === accounts.length) {
                        showToast(`Completed with ${errors} errors`, 'warning');
                        location.reload();
                    }
                });
            });
        }
    );
}

function unfreezeAllAccounts() {
    showModal(
        'Unfreeze All Accounts',
        'Are you sure you want to unfreeze all accounts for this user? They will be able to perform transactions again.',
        'warning',
        function() {
            const userId = <?php echo $userId; ?>;
            const accounts = <?php echo json_encode($accounts); ?>;
            
            // Process each account
            let completed = 0;
            let errors = 0;
            
            accounts.forEach(account => {
                fetch('/api/admin-freeze-account.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        account_id: account.id,
                        freeze: false
                    })
                })
                .then(response => response.json())
                .then(data => {
                    completed++;
                    if (!data.success) errors++;
                    
                    if (completed === accounts.length) {
                        if (errors === 0) {
                            showToast(`Successfully unfroze all ${accounts.length} accounts`, 'success');
                            location.reload();
                        } else {
                            showToast(`Completed with ${errors} errors`, 'warning');
                            location.reload();
                        }
                    }
                })
                .catch(error => {
                    completed++;
                    errors++;
                    console.error('Error:', error);
                    
                    if (completed === accounts.length) {
                        showToast(`Completed with ${errors} errors`, 'warning');
                        location.reload();
                    }
                });
            });
        }
    );
}
</script>
