<?php 
$pageTitle = 'User Profile - Admin';
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
    redirect(getAdminUserListBackUrl());
}

requireDemoUserAdminAccess($user);

// Fetch user accounts
$accountModel = new Account();
$accounts = $accountModel->getUserAccounts($userId);

// Fetch user transactions
$transactionModel = new Transaction();
$transactions = $transactionModel->getUserTransactions($userId, ['limit' => 50]);

// Fetch user loans
$loanModel = new Loan();
$loans = $loanModel->getUserLoans($userId);

// Fetch user cards
$cardModel = new Card();
$cards = $cardModel->getUserCards($userId);

// Get user currency
$userCurrency = getUserDisplayCurrency($user);

include __DIR__ . '/../../includes/head.php';
include __DIR__ . '/../../includes/admin-sidebar.php';
?>

<style>
.profile-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.page-header {
    background: white;
    border-radius: 16px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.page-header h1 {
    font-size: 28px;
    font-weight: 700;
    color: #032B44;
    margin-bottom: 8px;
}

.page-header p {
    color: #6c757d;
    font-size: 16px;
}

.back-button {
    background: #f8f9fa;
    color: #6c757d;
    padding: 12px 24px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s;
    border: 1px solid #e9ecef;
}

.back-button:hover {
    background: #e9ecef;
    color: #495057;
    text-decoration: none;
}

.profile-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-bottom: 30px;
}

.profile-card {
    background: white;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.card-title {
    font-size: 20px;
    font-weight: 700;
    color: #032B44;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.info-item {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #f1f3f4;
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 600;
    color: #374151;
}

.info-value {
    color: #6b7280;
    text-align: right;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-active { background: #d1fae5; color: #065f46; }
.status-pending { background: #fef3c7; color: #92400e; }
.status-suspended { background: #fee2e2; color: #991b1b; }
.status-verified { background: #dbeafe; color: #1e40af; }

.transaction-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid #f1f3f4;
}

.transaction-item:last-child {
    border-bottom: none;
}

.transaction-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.transaction-type {
    font-weight: 600;
    color: #374151;
}

.transaction-date {
    font-size: 14px;
    color: #6b7280;
}

.transaction-amount {
    font-weight: 700;
    font-size: 16px;
}

.amount-credit { color: #059669; }
.amount-debit { color: #dc2626; }

@media (max-width: 768px) {
    .profile-grid {
        grid-template-columns: 1fr;
    }
    
    .page-header {
        flex-direction: column;
        gap: 20px;
        text-align: center;
    }
}
</style>

<div class="profile-container">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1>User Profile Details</h1>
            <p>Comprehensive view of user information and activity</p>
        </div>
        <a href="<?php echo SITE_URL; ?>/admin/user/<?php echo $user['id']; ?>" class="back-button">
            <i class="fas fa-arrow-left"></i> Back to User Management
        </a>
    </div>

    <!-- Profile Grid -->
    <div class="profile-grid">
        
        <!-- Personal Information -->
        <div class="profile-card">
            <h3 class="card-title">
                <i class="fas fa-user" style="color: #3b82f6;"></i>
                Personal Information
            </h3>
            
            <div class="info-item">
                <span class="info-label">Full Name</span>
                <span class="info-value"><?php echo htmlspecialchars($user['full_name']); ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Email Address</span>
                <span class="info-value"><?php echo htmlspecialchars($user['email']); ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Phone Number</span>
                <span class="info-value"><?php echo htmlspecialchars($user['phone'] ?? 'Not provided'); ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Date of Birth</span>
                <span class="info-value"><?php echo $user['date_of_birth'] ? date('M d, Y', strtotime($user['date_of_birth'])) : 'Not provided'; ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Address</span>
                <span class="info-value"><?php echo htmlspecialchars($user['address'] ?? 'Not provided'); ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">City</span>
                <span class="info-value"><?php echo htmlspecialchars($user['city'] ?? 'Not provided'); ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Country</span>
                <span class="info-value"><?php echo htmlspecialchars($user['country'] ?? 'Not provided'); ?></span>
            </div>
        </div>

        <!-- Account Status -->
        <div class="profile-card">
            <h3 class="card-title">
                <i class="fas fa-shield-alt" style="color: #059669;"></i>
                Account Status
            </h3>
            
            <div class="info-item">
                <span class="info-label">Account Status</span>
                <span class="status-badge status-<?php echo $user['status']; ?>">
                    <?php echo ucfirst($user['status']); ?>
                </span>
            </div>
            
            <div class="info-item">
                <span class="info-label">KYC Status</span>
                <span class="status-badge status-<?php echo $user['kyc_status']; ?>">
                    <?php echo ucfirst($user['kyc_status']); ?>
                </span>
            </div>
            
            <div class="info-item">
                <span class="info-label">2FA Enabled</span>
                <span class="info-value"><?php echo $user['two_factor_enabled'] ? 'Yes' : 'No'; ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Email Verified</span>
                <span class="info-value"><?php echo $user['email_verified'] ? 'Yes' : 'No'; ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Registration Date</span>
                <span class="info-value"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Last Login</span>
                <span class="info-value"><?php echo $user['last_login'] ? date('M d, Y H:i', strtotime($user['last_login'])) : 'Never'; ?></span>
            </div>
        </div>

        <!-- Financial Summary -->
        <div class="profile-card">
            <h3 class="card-title">
                <i class="fas fa-wallet" style="color: #7c3aed;"></i>
                Financial Summary
            </h3>
            
            <div class="info-item">
                <span class="info-label">Display Currency</span>
                <span class="info-value"><?php echo htmlspecialchars($userCurrency); ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Total Accounts</span>
                <span class="info-value"><?php echo count($accounts); ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Total Balance</span>
                <span class="info-value">
                    <?php echo formatUserTotalBalance($user, $accounts); ?>
                </span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Active Loans</span>
                <span class="info-value"><?php echo count(array_filter($loans, fn($l) => $l['status'] === 'active')); ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Active Cards</span>
                <span class="info-value"><?php echo count(array_filter($cards, fn($c) => $c['status'] === 'active')); ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Total Transactions</span>
                <span class="info-value"><?php echo count($transactions); ?></span>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="profile-card">
            <h3 class="card-title">
                <i class="fas fa-clock" style="color: #ea580c;"></i>
                Recent Activity
            </h3>
            
            <?php if (!empty($transactions)): ?>
                <?php foreach (array_slice($transactions, 0, 5) as $transaction): ?>
                    <div class="transaction-item">
                        <div class="transaction-info">
                            <div class="transaction-type"><?php echo ucfirst($transaction['transaction_type']); ?></div>
                            <div class="transaction-date"><?php echo date('M d, Y H:i', strtotime($transaction['created_at'])); ?></div>
                        </div>
                        <div class="transaction-amount amount-<?php echo $transaction['transaction_type']; ?>">
                            <?php echo $transaction['transaction_type'] === 'credit' ? '+' : '-'; ?>
                            <?php echo formatTransactionAmountForUser($transaction, $user, 'amount'); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: #6b7280; text-align: center; padding: 20px;">No recent transactions</p>
            <?php endif; ?>
        </div>

    </div>
</div>
