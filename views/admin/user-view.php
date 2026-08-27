<?php 
$pageTitle = 'User Management - Admin - SecureBank';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/currency.php';

// Ensure admin access
requireLogin();
requireAdmin();

// Get user ID from URL
$userId = $GLOBALS['id'] ?? null;

if (!$userId) {
    $_SESSION['error'] = 'User ID not provided';
    redirect('/admin/users');
}

// Get user data
$userModel = new User();
$user = $userModel->findById($userId);

if (!$user) {
    $_SESSION['error'] = 'User not found';
    redirect('/admin/users');
}

// Get user accounts
$accountModel = new Account();
$accounts = $accountModel->getUserAccounts($userId);
$totalUserBalance = $accountModel->getTotalBalance($userId);
$userCurrency = getUserDisplayCurrency($user);
$currencyHelper = new Currency();
$supportedCurrencies = $currencyHelper->getSupportedCurrencies();

// Check if user is a joint account user
require_once __DIR__ . '/../../models/JointAccount.php';
$jointAccount = new JointAccount();
$isJointAccountUser = $jointAccount->isJointAccountUser($userId);
$jointRelationship = null;
$primaryOwnerInfo = null;

if ($isJointAccountUser) {
    $jointRelationship = $jointAccount->getJointAccountRelationship($userId);
    
    // If relationship data is available, use it
    if ($jointRelationship && isset($jointRelationship['primary_owner'])) {
        $primaryOwnerInfo = $jointRelationship['primary_owner'];
    } else {
        // Fallback: Get primary owner info directly from database
        $db = Database::getInstance();
        $primaryOwnerSql = "SELECT DISTINCT u.id, u.full_name, u.email
                           FROM account_owners ao
                           INNER JOIN accounts a ON ao.account_id = a.id
                           INNER JOIN users u ON a.user_id = u.id
                           WHERE ao.user_id = ? 
                           AND ao.status = 'active'
                           AND ao.is_primary = 0
                           AND a.user_id != ?
                           LIMIT 1";
        $primaryOwnerStmt = $db->query($primaryOwnerSql, [$userId, $userId]);
        $primaryOwnerInfo = $primaryOwnerStmt ? $primaryOwnerStmt->fetch() : null;
    }
}

// Get recent transactions
$transactionModel = new Transaction();
$recentTransactions = $transactionModel->getUserTransactions($userId, ['limit' => 5]);

// Get user loans
$loanModel = new Loan();
$loans = $loanModel->getUserLoans($userId);

// Include head
include __DIR__ . '/../../includes/head.php';

// Include sidebar and main structure
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

/* No card / no full-width empty header — just a compact top row */
.user-page-top {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 14px 18px;
    margin: 0 0 18px;
    padding: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
    border: none !important;
    border-radius: 0 !important;
    min-height: 0 !important;
    width: auto;
    max-width: 100%;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    flex: 0 0 auto;
    width: auto;
    max-width: max-content;
    padding: 8px 12px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    color: #334155;
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    line-height: 1;
    white-space: nowrap;
    box-shadow: none;
}

.back-link:hover {
    color: #1e3a8a;
    border-color: #cbd5e1;
    background: #f8fafc;
}

.user-identity {
    display: flex;
    align-items: center;
    gap: 12px;
    flex: 1 1 auto;
    min-width: 0;
}

.avatar {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: linear-gradient(135deg, #1a2d5a 0%, #0d1b3a 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 14px;
    flex-shrink: 0;
}

.user-info {
    min-width: 0;
}

.user-info h1 {
    font-size: 18px;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 2px 0;
    line-height: 1.25;
}

.user-info .email {
    color: #64748b;
    font-size: 13px;
    margin: 0;
}

.user-meta-row {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    margin-top: 4px;
}

.status {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.status.active { background: #dcfce7; color: #16a34a; }
.status.pending { background: #fef3c7; color: #d97706; }
.status.suspended { background: #fecaca; color: #dc2626; }
.status.blocked { background: #e5e7eb; color: #6b7280; }
.status.hold { background: #fef3c7; color: #d97706; }

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

.icon-users { background: #4f46e5; }
.icon-money { background: #10b981; }
.icon-transactions { background: #f59e0b; }
.icon-security { background: #ef4444; }
.icon-settings { background: #8b5cf6; }
.icon-reports { background: #06b6d4; }
.icon-kyc { background: #f59e0b; }
.icon-2fa { background: #3b82f6; }

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

    /* Action Cards */
    .action-grid {
      display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
    }

    .action-card {
    background: white;
    border-radius: 16px;
    padding: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
      cursor: pointer;
    transition: all 0.3s ease;
      border: 1px solid transparent;
    }

    .action-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    border-color: #4f46e5;
    }

    .action-header {
        display: flex;
        align-items: center;
      gap: 12px;
      margin-bottom: 12px;
    }

    .action-icon {
      width: 40px;
      height: 40px;
    border-radius: 10px;
    background: #f8f9fa;
    color: #2d3748;
      display: flex;
      align-items: center;
      justify-content: center;
    font-size: 18px;
    }

    .action-title {
      font-weight: 600;
    color: #2d3748;
      font-size: 16px;
    }

    .action-desc {
        font-size: 14px;
    color: #6c757d;
      margin-bottom: 16px;
    line-height: 1.5;
    }

    .action-btn {
      width: 100%;
    padding: 12px;
    background: #4f46e5;
      color: white;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
    transition: all 0.3s ease;
    }

    .action-btn:hover {
    background: #4338ca;
    transform: translateY(-2px);
    }

/* Quick Actions */
    .quick-actions {
    background: white;
    border-radius: 16px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }

    .quick-actions h3 {
    color: #2d3748;
    margin-bottom: 20px;
    font-size: 20px;
    font-weight: 600;
    }

    .quick-actions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 12px;
    }

    .quick-action-item {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 12px;
        padding: 14px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    }

    .quick-action-title {
        font-weight: 700;
        color: #0f172a;
        font-size: 14px;
        margin-bottom: 6px;
    }

    .quick-action-desc {
        color: #6c757d;
        font-size: 13px;
        line-height: 1.4;
        margin-bottom: 12px;
    }

    .quick-btn {
        width: 100%;
    padding: 12px 20px;
      border: none;
      background: linear-gradient(135deg, #1a2d5a 0%, #0d1b3a 100%);
        border-radius: 8px;
      cursor: pointer;
      font-weight: 600;
    transition: all 0.3s ease;
    color: #ffffff;
    text-align: center;
    box-shadow: 0 6px 18px rgba(13, 27, 58, 0.18);
    }

    .quick-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 24px rgba(13, 27, 58, 0.28);
    }

    .quick-btn.danger {
      background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
      color: #ffffff;
    }

    .quick-btn.danger:hover {
      box-shadow: 0 10px 24px rgba(220, 38, 38, 0.28);
    }

    /* Match dashboard-style gradient for card buttons too */
    .action-btn {
      background: linear-gradient(135deg, #1a2d5a 0%, #0d1b3a 100%);
      box-shadow: 0 6px 18px rgba(13, 27, 58, 0.18);
    }

    .action-btn:hover {
      background: linear-gradient(135deg, #223a74 0%, #0b1730 100%);
      box-shadow: 0 10px 24px rgba(13, 27, 58, 0.28);
      transform: translateY(-2px);
    }

    /* Modal Styles */
    .modal {
      display: none;
      position: fixed;
      z-index: 10040;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.5);
    align-items: center;
    justify-content: center;
    }

    .modal-content {
      background: white;
      margin: 5% auto;
      padding: 0;
      border-radius: 16px;
      width: 90%;
      max-width: 500px;
      box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
    z-index: 1;
    }

    .balance-modal-alert {
      display: none;
      margin: 0 0 16px;
      padding: 12px 14px;
      border-radius: 10px;
      font-size: 14px;
      line-height: 1.45;
      font-weight: 500;
    }

    .balance-modal-alert.is-visible {
      display: block;
    }

    .balance-modal-alert.is-error {
      background: #fef2f2;
      color: #991b1b;
      border: 1px solid #fecaca;
    }

    .balance-modal-alert.is-success {
      background: #ecfdf5;
      color: #065f46;
      border: 1px solid #a7f3d0;
    }

    .modal-header {
      padding: 24px 24px 0;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .modal-header h3 {
      font-size: 20px;
      font-weight: 600;
    color: #2d3748;
    }

    .close {
      font-size: 24px;
      font-weight: bold;
      cursor: pointer;
    color: #6c757d;
    transition: color 0.3s;
}

.close:hover {
    color: #2d3748;
    }

    .modal-body {
      padding: 24px;
    }

    .modal-footer {
      padding: 0 24px 24px;
      display: flex;
      justify-content: flex-end;
      gap: 12px;
    }

    .btn-primary, .btn-secondary {
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
        font-weight: 600;
      cursor: pointer;
    transition: all 0.3s ease;
    }

    .btn-primary {
    background: #4f46e5;
      color: white;
    }

.btn-primary:hover {
    background: #4338ca;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
}

    .btn-secondary {
    background: #f8f9fa;
    color: #2d3748;
      border: 1px solid #e2e8f0;
    }

.btn-secondary:hover {
    background: #e9ecef;
    transform: translateY(-2px);
}

/* Form Styling */
.modal .form-group {
    margin-bottom: 20px;
}

.modal .form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #2d3748;
    font-size: 14px;
}

.modal .form-input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 16px;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    box-sizing: border-box;
    background: white;
}

.modal .form-input:focus {
    outline: none;
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .content-area {
        padding: 10px;
    }
    
    .admin-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .action-grid {
        grid-template-columns: 1fr;
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
    .card-icon.icon-users,
    .card-icon.icon-transactions,
    .card-icon.icon-kyc,
    .card-icon.icon-2fa {
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
</style>

<div class="admin-container">
    <div class="user-page-top">
      <a href="<?php echo SITE_URL; ?>/admin/users" class="back-link">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M15 6l-6 6 6 6" stroke="currentColor" stroke-width="2"/>
        </svg>
        Users
      </a>
      <div class="user-identity">
        <div class="avatar"><?php echo strtoupper(substr($user['full_name'], 0, 2)); ?></div>
        <div class="user-info">
          <h1><?php echo htmlspecialchars($user['full_name']); ?></h1>
          <div class="email"><?php echo htmlspecialchars($user['email']); ?></div>
          <div class="user-meta-row">
            <span class="status <?php echo strtolower($user['status']); ?>"><?php echo strtoupper($user['status']); ?></span>
            <?php if ($isJointAccountUser): ?>
              <span style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; padding: 4px 10px; border-radius: 16px; font-size: 12px; font-weight: 600;">
                Joint (Secondary)
              </span>
              <?php if ($primaryOwnerInfo):
                $primaryOwnerName = $primaryOwnerInfo['primary_owner_name'] ?? $primaryOwnerInfo['full_name'] ?? 'Unknown';
              ?>
                <span style="color: #64748b; font-size: 13px;">Primary: <?php echo htmlspecialchars($primaryOwnerName); ?></span>
              <?php endif; ?>
            <?php elseif (!$isJointAccountUser && $accounts):
              $hasJointUsers = false;
              foreach ($accounts as $account) {
                $owners = $jointAccount->getAccountOwners($account['id']);
                if (count($owners) > 1) {
                  $hasJointUsers = true;
                  break;
                }
              }
              if ($hasJointUsers): ?>
                <span style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 4px 10px; border-radius: 16px; font-size: 12px; font-weight: 600;">
                  Primary (has joint users)
                </span>
              <?php endif; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions (moved to top) -->
    <div class="quick-actions">
        <h3>Quick Actions</h3>
        <div class="quick-actions-grid">
            <div class="quick-action-item">
                <div class="quick-action-title">Adjust Balance</div>
                <div class="quick-action-desc">Credit or debit the user by creating a controlled transaction entry.</div>
                <button class="quick-btn" onclick="adjustBalance()">Adjust Balance</button>
            </div>
            <div class="quick-action-item">
                <div class="quick-action-title">Reset Password</div>
                <div class="quick-action-desc">Generate a new password for the user and force re-login.</div>
                <button class="quick-btn" onclick="resetPassword()">Reset Password</button>
            </div>
            <div class="quick-action-item">
                <div class="quick-action-title">User Currency</div>
                <div class="quick-action-desc">Set this user’s display currency. Balances and amounts convert via FX.</div>
                <button class="quick-btn" onclick="openUserCurrencyModal()">Change Currency</button>
            </div>
            <div class="quick-action-item">
                <div class="quick-action-title">Delete User Account</div>
                <div class="quick-action-desc">Permanently remove the user and their related records.</div>
                <button class="quick-btn danger" onclick="confirmDelete()">Delete User Account</button>
            </div>
        </div>
    </div>

      <!-- Action Cards (moved directly after Quick Actions) -->
      <div class="action-grid">
        <div class="action-card" onclick="location.href='/admin/user/<?php echo $userId; ?>?action=edit'">
          <div class="action-header">
            <div class="action-icon">👤</div>
            <div class="action-title">User Information</div>
            </div>
          <div class="action-desc">
            Manage personal details, contact information, and profile settings
                </div>
          <button class="action-btn">Manage Information</button>
                </div>

        <div class="action-card" onclick="location.href='/admin/user/<?php echo $userId; ?>?action=security'">
          <div class="action-header">
            <div class="action-icon">🔒</div>
            <div class="action-title">Security Settings</div>
                </div>
          <div class="action-desc">
            Reset passwords, manage 2FA, and control transaction PINs
                </div>
          <button class="action-btn">Security Controls</button>
                </div>

        <div class="action-card" onclick="location.href='/admin/user/<?php echo $userId; ?>?action=transactions'">
          <div class="action-header">
            <div class="action-icon">💳</div>
            <div class="action-title">Transaction Management</div>
                </div>
          <div class="action-desc">
            View, edit, and manage user transactions and processing
            </div>
          <button class="action-btn">View Transactions</button>
        </div>

        <div class="action-card" onclick="location.href='/admin/user/<?php echo $userId; ?>?action=status'">
          <div class="action-header">
            <div class="action-icon">⚙️</div>
            <div class="action-title">User Status Control</div>
            </div>
          <div class="action-desc">
            Override transaction processing behavior for testing
                        </div>
          <button class="action-btn">Status Controls</button>
                        </div>

        <div class="action-card" onclick="openUserCurrencyModal()">
          <div class="action-header">
            <div class="action-icon">💱</div>
            <div class="action-title">User Currency</div>
          </div>
          <div class="action-desc">
            Current: <?php echo htmlspecialchars($userCurrency); ?>. Change the display currency for balances and transfers.
          </div>
          <button class="action-btn" type="button">Change Currency</button>
        </div>
                        </div>

    <!-- Statistics Grid -->
    <div class="admin-grid">
        <div class="admin-card">
            <div class="card-header">
                <h3 class="card-title">Total Balance</h3>
                <div class="card-icon icon-money">
                    <i class="fas fa-coins"></i>
      </div>
      </div>
            <div class="stat-number"><?php echo formatUserTotalBalance($user, $accounts); ?></div>
            <div class="stat-label">Across all active accounts</div>
            <div class="stat-change positive">
                <i class="fas fa-wallet"></i>
                <span><?php echo count($accounts); ?> accounts</span>
      </div>
        </div>

        <div class="admin-card">
            <div class="card-header">
                <h3 class="card-title">Active Accounts</h3>
                <div class="card-icon icon-users">
                    <i class="fas fa-credit-card"></i>
                </div>
            </div>
            <div class="stat-number"><?php echo count($accounts); ?></div>
            <div class="stat-label">User bank accounts</div>
            <div class="stat-change positive">
                <i class="fas fa-check-circle"></i>
                <span>All active</span>
      </div>
    </div>

        <div class="admin-card">
            <div class="card-header">
                <h3 class="card-title">Recent Transactions</h3>
                <div class="card-icon icon-transactions">
                    <i class="fas fa-exchange-alt"></i>
                </div>
            </div>
            <div class="stat-number"><?php echo count($recentTransactions); ?></div>
            <div class="stat-label">Last 5 transactions</div>
            <div class="stat-change positive">
                <i class="fas fa-list"></i>
                <span><a href="/admin/user/<?php echo $userId; ?>?action=transactions" style="color: inherit; text-decoration: none;">View all</a></span>
            </div>
        </div>

        <div class="admin-card">
            <div class="card-header">
                <h3 class="card-title">KYC Status</h3>
                <div class="card-icon icon-kyc">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
            <div class="stat-number"><?php echo ucfirst($user['kyc_status']); ?></div>
            <div class="stat-label">Verification status</div>
            <div class="stat-change <?php echo $user['kyc_status'] === 'verified' ? 'positive' : 'negative'; ?>">
                <i class="fas fa-<?php echo $user['kyc_status'] === 'verified' ? 'check-circle' : 'clock'; ?>"></i>
                <span><?php echo $user['kyc_status'] === 'verified' ? 'Verified' : 'Pending'; ?></span>
            </div>
        </div>

        <div class="admin-card" style="cursor:pointer;" onclick="location.href='/admin/user/<?php echo (int)$userId; ?>?action=security'" title="Manage 2FA on Security page">
            <div class="card-header">
                <h3 class="card-title">2FA Status</h3>
                <div class="card-icon icon-2fa">
                    <i class="fas fa-shield-alt"></i>
                </div>
            </div>
            <div class="stat-number"><?php echo $user['two_factor_enabled'] ? 'Enabled' : 'Disabled'; ?></div>
            <div class="stat-label">Two-factor authentication</div>
            <div class="stat-change <?php echo $user['two_factor_enabled'] ? 'positive' : 'negative'; ?>">
                <i class="fas fa-<?php echo $user['two_factor_enabled'] ? 'check-circle' : 'times-circle'; ?>"></i>
                <span><?php echo $user['two_factor_enabled'] ? 'Active' : 'Inactive'; ?> — manage on Security</span>
            </div>
        </div>

        <div class="admin-card">
            <div class="card-header">
                <h3 class="card-title">Active Loans</h3>
                <div class="card-icon icon-money">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
            </div>
            <div class="stat-number"><?php echo count($loans); ?></div>
            <div class="stat-label">Loan accounts</div>
            <div class="stat-change positive">
                <i class="fas fa-file-contract"></i>
                <span>View details</span>
            </div>
        </div>
    </div>

      <!-- Action Cards -->
      <!-- Action Cards moved above -->

  <!-- Transaction Mode Modal -->
  <div id="transactionModeModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Set Transaction Processing Mode</h3>
        <span class="close" onclick="closeTransactionModeModal()">&times;</span>
      </div>
      <div class="modal-body">
        <p>This setting allows you to override how transactions are processed for this user:</p>
            <div class="mode-options" style="margin-top: 16px;">
                <label class="mode-option" style="display: flex; align-items: center; gap: 12px; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 8px; cursor: pointer; transition: all 0.2s;">
            <input type="radio" name="transaction_mode" value="normal" <?php echo ($user['transaction_override'] ?? 'normal') === 'normal' ? 'checked' : ''; ?>>
            <span class="mode-info">
                        <strong style="display: block; color: #2d3748; margin-bottom: 2px;">Normal Processing</strong>
                        <small style="color: #6c757d;">Default transaction processing</small>
            </span>
          </label>
                <label class="mode-option" style="display: flex; align-items: center; gap: 12px; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 8px; cursor: pointer; transition: all 0.2s;">
            <input type="radio" name="transaction_mode" value="force_success" <?php echo ($user['transaction_override'] ?? 'normal') === 'force_success' ? 'checked' : ''; ?>>
            <span class="mode-info">
                        <strong style="display: block; color: #2d3748; margin-bottom: 2px;">Force All Success</strong>
                        <small style="color: #6c757d;">All transactions automatically succeed</small>
            </span>
          </label>
                <label class="mode-option" style="display: flex; align-items: center; gap: 12px; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 8px; cursor: pointer; transition: all 0.2s;">
            <input type="radio" name="transaction_mode" value="force_pending" <?php echo ($user['transaction_override'] ?? 'normal') === 'force_pending' ? 'checked' : ''; ?>>
            <span class="mode-info">
                        <strong style="display: block; color: #2d3748; margin-bottom: 2px;">Force All Pending</strong>
                        <small style="color: #6c757d;">All transactions show as pending</small>
            </span>
          </label>
                <label class="mode-option" style="display: flex; align-items: center; gap: 12px; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 8px; cursor: pointer; transition: all 0.2s;">
            <input type="radio" name="transaction_mode" value="force_failed" <?php echo ($user['transaction_override'] ?? 'normal') === 'force_failed' ? 'checked' : ''; ?>>
            <span class="mode-info">
                        <strong style="display: block; color: #2d3748; margin-bottom: 2px;">Force All Failed</strong>
                        <small style="color: #6c757d;">All transactions automatically fail</small>
            </span>
          </label>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn-secondary" onclick="closeTransactionModeModal()">Cancel</button>
        <button class="btn-primary" onclick="saveTransactionMode()">Save Changes</button>
      </div>
    </div>
        </div>
        
  <script>
    // Transaction Mode Modal
    function showTransactionModeModal() {
    document.getElementById('transactionModeModal').style.display = 'flex';
    }

    function closeTransactionModeModal() {
      document.getElementById('transactionModeModal').style.display = 'none';
    }

    function saveTransactionMode() {
      const selectedMode = document.querySelector('input[name="transaction_mode"]:checked');
      if (!selectedMode) {
        showToast('Please select a transaction mode', 'warning');
        return;
      }
      
      const userId = <?php echo $userId; ?>;
      const mode = selectedMode.value;
      
      fetch('/api/set-transaction-mode.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          user_id: userId,
          mode: mode
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showToast('Transaction mode updated successfully', 'success');
          closeTransactionModeModal();
          location.reload();
        } else {
          showToast('Error: ' + data.message, 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred while updating transaction mode', 'error');
      });
    }

    // Quick Actions
    function adjustBalance() {
      showBalanceAdjustmentModal();
    }
    
    function showBalanceAdjustmentModal() {
      const modal = document.createElement('div');
      modal.id = 'balanceModal';
      modal.className = 'modal';
      modal.innerHTML = `
        <div class="modal-content">
          <div class="modal-header">
            <h3>Adjust User Balance</h3>
            <span class="close" onclick="closeBalanceModal()">&times;</span>
          </div>
          <div class="modal-body">
            <div id="balanceModalAlert" class="balance-modal-alert" role="alert"></div>
            <div class="balance-adjustment-form">
                    <!-- NEW: Adjustment Type Selection (External vs Internal) -->
                    <div class="form-group">
                        <label for="adjustmentMode">Adjustment Type</label>
                        <select id="adjustmentMode" class="form-input" onchange="toggleAdjustmentMode()" required>
                            <option value="">Select adjustment type</option>
                            <option value="external">External Adjustment</option>
                            <option value="internal">Internal Adjustment (Between User's Accounts)</option>
                        </select>
                    </div>
                    
                    <!-- External Adjustment Fields -->
                    <div id="externalAdjustmentFields" style="display: none;">
                <div class="form-group">
            <label for="adjustAmount">Amount</label>
            <input type="number" id="adjustAmount" class="form-input" placeholder="Enter amount" step="0.01" required>
          </div>
          <div class="form-group">
                            <label for="adjustType">Transaction Direction</label>
                    <select id="adjustType" class="form-input" onchange="toggleStatusField()" required>
                                <option value="">Select direction</option>
                        <option value="credit">Credit (Add Money)</option>
                        <option value="debit">Debit (Remove Money)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="targetAccount">User Account</label>
            <select id="targetAccount" class="form-input" required>
                        <option value="">Select user account</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="transactionType">Transfer Type</label>
                <select id="transactionType" class="form-input" onchange="toggleTransactionFields()" required>
                        <option value="">Select transfer type</option>
                  <option value="domestic">Domestic Transfer</option>
                  <option value="international">International Wire Transfer</option>
                    </select>
              </div>
              <div id="dynamicFields" style="display: none;">
                <div id="internalFields" class="transaction-type-fields" style="display: none;">
                  <div class="form-group">
                    <label for="recipientAccount" id="internalAccountLabel">Recipient Account</label>
                    <input type="text" id="recipientAccount" class="form-input" placeholder="Enter account number">
                  </div>
                  <div class="form-group">
                    <label for="recipientName" id="internalNameLabel">Recipient Name</label>
                    <input type="text" id="recipientName" class="form-input" placeholder="Enter full name">
                </div>
            </div>
                <div id="domesticFields" class="transaction-type-fields" style="display: none;">
                <div class="form-group">
                    <label for="domesticAccount" id="domesticAccountLabel">Recipient Account</label>
                    <input type="text" id="domesticAccount" class="form-input" placeholder="Enter account number">
                  </div>
                  <div class="form-group">
                    <label for="domesticName" id="domesticNameLabel">Recipient Name</label>
                    <input type="text" id="domesticName" class="form-input" placeholder="Enter full name">
                  </div>
                  <div class="form-group">
                    <label for="domesticBank" id="domesticBankLabel">Recipient Bank</label>
                    <input type="text" id="domesticBank" class="form-input" placeholder="Enter bank name">
                  </div>
                </div>
                <div id="internationalFields" class="transaction-type-fields" style="display: none;">
                <div class="form-group">
                    <label for="intAccount" id="intAccountLabel">Recipient Account</label>
                    <input type="text" id="intAccount" class="form-input" placeholder="Enter account number">
                  </div>
                  <div class="form-group">
                    <label for="intName" id="intNameLabel">Recipient Name</label>
                    <input type="text" id="intName" class="form-input" placeholder="Enter full name">
                  </div>
                  <div class="form-group">
                    <label for="intBank" id="intBankLabel">Recipient Bank</label>
                    <input type="text" id="intBank" class="form-input" placeholder="Enter bank name">
                </div>
                <div class="form-group">
                            <label for="swift">SWIFT Code</label>
                            <input type="text" id="swift" class="form-input" placeholder="Enter SWIFT code">
                  </div>
                  <div class="form-group">
                            <label for="iban">IBAN (Optional)</label>
                            <input type="text" id="iban" class="form-input" placeholder="Enter IBAN">
                  </div>
                </div>
            </div>
              <div class="form-group">
                    <label for="expenseCategory">Transaction Category</label>
                    <select id="expenseCategory" class="form-input" required>
                        <option value="">Select Category</option>
                        <option value="salary">💰 Salary</option>
                        <option value="bonus">🎁 Bonus</option>
                        <option value="transfer">🔄 Transfer</option>
                        <option value="deposit">📥 Deposit</option>
                        <option value="withdrawal">📤 Withdrawal</option>
                        <option value="payment">💳 Payment</option>
                        <option value="refund">↩️ Refund</option>
                        <option value="fee">💸 Fee</option>
                        <option value="interest">📈 Interest</option>
                        <option value="investment">💼 Investment</option>
                        <option value="loan">🏦 Loan</option>
                        <option value="insurance">🛡️ Insurance</option>
                        <option value="utility">💡 Utility Bill</option>
                        <option value="shopping">🛍️ Shopping</option>
                        <option value="entertainment">🎬 Entertainment</option>
                        <option value="food">🍽️ Food & Dining</option>
                        <option value="transportation">🚗 Transportation</option>
                        <option value="healthcare">🏥 Healthcare</option>
                        <option value="education">📚 Education</option>
                        <option value="travel">✈️ Travel</option>
                        <option value="gift">🎁 Gift</option>
                        <option value="other">🔧 Other</option>
                </select>
    </div>
              <div class="form-group">
                <label for="transactionDate">Transaction Date</label>
                <input type="date" id="transactionDate" class="form-input" value="${new Date().toISOString().split('T')[0]}" required>
        </div>
              <div class="form-group">
                <label for="transactionTime">Transaction Time</label>
                <input type="time" id="transactionTime" class="form-input" value="${new Date().toTimeString().slice(0,5)}" required>
            </div>
                <div class="form-group" id="statusFieldGroup" style="display: none;">
                <label for="transactionStatus">Transaction Status</label>
                    <select id="transactionStatus" class="form-input">
                  <option value="successful">Successful</option>
                  <option value="completed">Completed</option>
                  <option value="pending">Pending</option>
                  <option value="failed">Failed</option>
                  <option value="pending">Pending</option>
                  <option value="on_hold">On Hold</option>
                        <option value="failed">Failed</option>
                </select>
    </div>
              <div class="form-group">
                <label for="transactionDescription">Description</label>
                <textarea id="transactionDescription" class="form-input" placeholder="Enter transaction description" rows="3"></textarea>
                        </div>
                    </div>
                    
                    <!-- Internal Adjustment Fields (Between User's Own Accounts) -->
                    <div id="internalAdjustmentFields" style="display: none;">
                        <div class="form-group">
                            <label for="fromAccount">From Account</label>
                            <select id="fromAccount" class="form-input" onchange="updateToAccountOptions()" required>
                                <option value="">Select source account</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="toAccount">To Account</label>
                            <select id="toAccount" class="form-input" required>
                                <option value="">Select destination account</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="internalAmount">Amount</label>
                            <input type="number" id="internalAmount" class="form-input" placeholder="Enter amount" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="internalCategory">Transaction Category</label>
                            <select id="internalCategory" class="form-input" required>
                                <option value="">Select Category</option>
                                <option value="salary">💰 Salary</option>
                                <option value="bonus">🎁 Bonus</option>
                                <option value="transfer">🔄 Transfer</option>
                                <option value="deposit">📥 Deposit</option>
                                <option value="withdrawal">📤 Withdrawal</option>
                                <option value="payment">💳 Payment</option>
                                <option value="refund">↩️ Refund</option>
                                <option value="fee">💸 Fee</option>
                                <option value="interest">📈 Interest</option>
                                <option value="investment">💼 Investment</option>
                                <option value="loan">🏦 Loan</option>
                                <option value="insurance">🛡️ Insurance</option>
                                <option value="utility">💡 Utility Bill</option>
                                <option value="shopping">🛍️ Shopping</option>
                                <option value="entertainment">🎬 Entertainment</option>
                                <option value="food">🍽️ Food & Dining</option>
                                <option value="transportation">🚗 Transportation</option>
                                <option value="healthcare">🏥 Healthcare</option>
                                <option value="education">📚 Education</option>
                                <option value="travel">✈️ Travel</option>
                                <option value="gift">🎁 Gift</option>
                                <option value="other">🔧 Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="internalTransactionDate">Transaction Date</label>
                            <input type="date" id="internalTransactionDate" class="form-input" value="${new Date().toISOString().split('T')[0]}" required>
                        </div>
                        <div class="form-group">
                            <label for="internalTransactionTime">Transaction Time</label>
                            <input type="time" id="internalTransactionTime" class="form-input" value="${new Date().toTimeString().slice(0,5)}" required>
                        </div>
                        <div class="form-group">
                            <label for="internalDescription">Description</label>
                            <textarea id="internalDescription" class="form-input" placeholder="Enter transaction description" rows="3"></textarea>
                        </div>
        </div>
        </div>
    </div>
          <div class="modal-footer">
                <button class="btn-secondary" onclick="closeBalanceModal()">Cancel</button>
                <button class="btn-primary" onclick="submitBalanceAdjustment()">Adjust Balance</button>
</div>
        </div>
      `;
      
      document.body.appendChild(modal);
      modal.style.display = 'flex';
      document.body.style.overflow = 'hidden';
      
      setTimeout(() => {
        loadUserAccounts();
        // Load accounts for both external and internal adjustment dropdowns
        loadUserAccountsForInternal();
        // Add event listener for From Account change
        const fromAccountSelect = document.getElementById('fromAccount');
        if (fromAccountSelect) {
            fromAccountSelect.addEventListener('change', updateToAccountOptions);
        }
      }, 100);
}

function toggleAdjustmentMode() {
    const adjustmentMode = document.getElementById('adjustmentMode').value;
    const externalFields = document.getElementById('externalAdjustmentFields');
    const internalFields = document.getElementById('internalAdjustmentFields');
    
    if (adjustmentMode === 'external') {
        externalFields.style.display = 'block';
        internalFields.style.display = 'none';
        // Clear internal fields
        document.getElementById('fromAccount').value = '';
        document.getElementById('toAccount').value = '';
        document.getElementById('internalAmount').value = '';
    } else if (adjustmentMode === 'internal') {
        externalFields.style.display = 'none';
        internalFields.style.display = 'block';
        // Clear external fields
        document.getElementById('adjustAmount').value = '';
        document.getElementById('adjustType').value = '';
        document.getElementById('targetAccount').value = '';
        document.getElementById('transactionType').value = '';
        document.getElementById('dynamicFields').style.display = 'none';
        // Hide all transaction type fields
        document.querySelectorAll('.transaction-type-fields').forEach(field => {
            field.style.display = 'none';
        });
    } else {
        externalFields.style.display = 'none';
        internalFields.style.display = 'none';
    }
}

// Store all accounts data for filtering
let allUserAccounts = [];

function loadUserAccountsForInternal() {
    const userId = <?php echo $userId; ?>;
    
    fetch('<?php echo SITE_URL; ?>/api/get-user-accounts.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ user_id: userId })
    })
    .then(response => response.text())
    .then(text => {
        try {
            const data = JSON.parse(text);
            const fromAccountSelect = document.getElementById('fromAccount');
            const toAccountSelect = document.getElementById('toAccount');
            
            if (!fromAccountSelect || !toAccountSelect) return;
            
            // Store all accounts for later filtering
            allUserAccounts = data.success && data.accounts && Array.isArray(data.accounts) ? data.accounts : [];
            
            // Helper function to populate a select with accounts
            const populateSelect = (select, excludeAccountId = null) => {
                select.innerHTML = '<option value="">Select account</option>';
                
                if (allUserAccounts.length === 0) {
                    select.innerHTML = '<option value="">No accounts found</option>';
                    return;
                }
                
                allUserAccounts.forEach(account => {
                    // Skip the excluded account (for To Account, exclude the selected From Account)
                    if (excludeAccountId && parseInt(account.id) === parseInt(excludeAccountId)) {
                        return;
                    }
                    
                    const option = document.createElement('option');
                    option.value = account.id;
                    const balance = parseFloat(account.display_balance != null ? account.display_balance : (account.balance || 0));
                    const balanceFormatted = account.balance_formatted || new Intl.NumberFormat('en-US', {
                        style: 'currency',
                        currency: <?php echo json_encode($userCurrency); ?>,
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(balance);
                    option.textContent = `${account.account_type || 'Account'} - ${account.account_number || 'N/A'} (${balanceFormatted})`;
                    select.appendChild(option);
                });
            };
            
            // Populate both selects initially (no exclusion yet)
            populateSelect(fromAccountSelect);
            populateSelect(toAccountSelect);
        } catch (e) {
            console.error('JSON parse error:', e);
            const fromAccountSelect = document.getElementById('fromAccount');
            const toAccountSelect = document.getElementById('toAccount');
            if (fromAccountSelect) fromAccountSelect.innerHTML = '<option value="">Error loading accounts</option>';
            if (toAccountSelect) toAccountSelect.innerHTML = '<option value="">Error loading accounts</option>';
        }
    })
    .catch(error => {
        console.error('Error loading accounts:', error);
        const fromAccountSelect = document.getElementById('fromAccount');
        const toAccountSelect = document.getElementById('toAccount');
        if (fromAccountSelect) fromAccountSelect.innerHTML = '<option value="">Error loading accounts</option>';
        if (toAccountSelect) toAccountSelect.innerHTML = '<option value="">Error loading accounts</option>';
    });
}

function updateToAccountOptions() {
    const fromAccountSelect = document.getElementById('fromAccount');
    const toAccountSelect = document.getElementById('toAccount');
    
    if (!fromAccountSelect || !toAccountSelect || allUserAccounts.length === 0) return;
    
    const selectedFromAccountId = fromAccountSelect.value;
    const currentToAccountId = toAccountSelect.value;
    
    // Clear and repopulate To Account dropdown, excluding the selected From Account
    toAccountSelect.innerHTML = '<option value="">Select destination account</option>';
    
    allUserAccounts.forEach(account => {
        // Skip the selected From Account
        if (selectedFromAccountId && parseInt(account.id) === parseInt(selectedFromAccountId)) {
            return;
        }
        
        const option = document.createElement('option');
        option.value = account.id;
        const balance = parseFloat(account.display_balance != null ? account.display_balance : (account.balance || 0));
        const balanceFormatted = account.balance_formatted || new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: <?php echo json_encode($userCurrency); ?>,
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(balance);
        option.textContent = `${account.account_type || 'Account'} - ${account.account_number || 'N/A'} (${balanceFormatted})`;
        
        // If this was the previously selected To Account and it's still valid, reselect it
        if (currentToAccountId && parseInt(account.id) === parseInt(currentToAccountId)) {
            option.selected = true;
        }
        
        toAccountSelect.appendChild(option);
    });
    
    // If the previously selected To Account was the same as From Account, clear it
    if (currentToAccountId === selectedFromAccountId) {
        toAccountSelect.value = '';
    }
    }
    
    function closeBalanceModal() {
      const modal = document.getElementById('balanceModal');
      if (modal) {
        modal.remove();
        document.body.style.overflow = '';
      }
    }

    function showBalanceModalAlert(message, type = 'error') {
      const alertEl = document.getElementById('balanceModalAlert');
      if (!alertEl) {
        if (typeof showToast === 'function') {
          showToast(message, type);
        }
        return;
      }
      alertEl.textContent = message || 'Something went wrong';
      alertEl.className = 'balance-modal-alert is-visible ' + (type === 'success' ? 'is-success' : 'is-error');
      try {
        alertEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      } catch (e) {}
      if (typeof showToast === 'function') {
        showToast(message, type);
      }
    }

    function clearBalanceModalAlert() {
      const alertEl = document.getElementById('balanceModalAlert');
      if (!alertEl) return;
      alertEl.textContent = '';
      alertEl.className = 'balance-modal-alert';
    }
    
    function toggleStatusField() {
      const adjustType = document.getElementById('adjustType').value;
      const statusFieldGroup = document.getElementById('statusFieldGroup');
      
      if (adjustType === 'credit') {
        statusFieldGroup.style.display = 'none';
        updateFieldLabels('sender');
      } else if (adjustType === 'debit') {
        statusFieldGroup.style.display = 'block';
        updateFieldLabels('recipient');
      } else {
        statusFieldGroup.style.display = 'none';
        updateFieldLabels('recipient');
      }
    }
    
    function updateFieldLabels(type) {
    const labels = {
        internalAccountLabel: type === 'sender' ? 'Sender Account' : 'Recipient Account',
        internalNameLabel: type === 'sender' ? 'Sender Name' : 'Recipient Name',
        domesticAccountLabel: type === 'sender' ? 'Sender Account' : 'Recipient Account',
        domesticNameLabel: type === 'sender' ? 'Sender Name' : 'Recipient Name',
        domesticBankLabel: type === 'sender' ? 'Sender Bank' : 'Recipient Bank',
        intAccountLabel: type === 'sender' ? 'Sender Account' : 'Recipient Account',
        intNameLabel: type === 'sender' ? 'Sender Name' : 'Recipient Name',
        intBankLabel: type === 'sender' ? 'Sender Bank' : 'Recipient Bank'
    };
    
    Object.keys(labels).forEach(id => {
        const el = document.getElementById(id);
        if (el) el.textContent = labels[id];
    });
    }
    
    function generateTransactionDescription(adjustType, transactionType, transactionData) {
      const description = document.getElementById('transactionDescription').value;
    if (description) return description;
      
      if (adjustType === 'credit') {
        switch(transactionType) {
          case 'domestic':
                return `Transfer from ${transactionData.recipient_name || 'Unknown Sender'} at ${transactionData.recipient_bank || 'Unknown Bank'}`;
          case 'international':
                return `Transfer from ${transactionData.recipient_name || 'Unknown Sender'} at ${transactionData.recipient_bank || 'Unknown Bank'}`;
          default:
            return `Admin Credit Adjustment - ${transactionData.expense_category}`;
        }
      } else {
        switch(transactionType) {
          case 'domestic':
                return `Domestic Transfer to ${transactionData.recipient_name || 'Unknown Recipient'} at ${transactionData.recipient_bank || 'Unknown Bank'}`;
          case 'international':
                return `International Transfer to ${transactionData.recipient_name || 'Unknown Recipient'} at ${transactionData.recipient_bank || 'Unknown Bank'}`;
          default:
            return `Admin Debit Adjustment - ${transactionData.expense_category}`;
        }
      }
    }
    
    function loadUserAccounts() {
      const userId = <?php echo $userId; ?>;
      
      fetch('<?php echo SITE_URL; ?>/api/get-user-accounts.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ user_id: userId })
    })
    .then(response => response.text())
      .then(text => {
        try {
          const data = JSON.parse(text);
          const accountSelect = document.getElementById('targetAccount');
            if (!accountSelect) return;
          
          accountSelect.innerHTML = '<option value="">Select account</option>';
          
          if (data.success && data.accounts && Array.isArray(data.accounts)) {
            if (data.accounts.length === 0) {
                    accountSelect.innerHTML = '<option value="">No accounts found</option>';
            } else {
              data.accounts.forEach(account => {
                const option = document.createElement('option');
                option.value = account.id;
                const balance = parseFloat(account.display_balance != null ? account.display_balance : (account.balance || 0));
                const balanceFormatted = account.balance_formatted || new Intl.NumberFormat('en-US', {
                  style: 'currency',
                  currency: <?php echo json_encode($userCurrency); ?>,
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
                }).format(balance);
                option.textContent = `${account.account_type || 'Account'} - ${account.account_number || 'N/A'} (${balanceFormatted})`;
                accountSelect.appendChild(option);
              });
            }
          } else {
                accountSelect.innerHTML = `<option value="">${data.message || 'No accounts found'}</option>`;
          }
        } catch (e) {
          console.error('JSON parse error:', e);
          const accountSelect = document.getElementById('targetAccount');
            if (accountSelect) accountSelect.innerHTML = '<option value="">Error loading accounts</option>';
        }
      })
      .catch(error => {
        console.error('Error loading accounts:', error);
        const accountSelect = document.getElementById('targetAccount');
        if (accountSelect) accountSelect.innerHTML = '<option value="">Error loading accounts</option>';
      });
    }
    
    function submitBalanceAdjustment() {
    clearBalanceModalAlert();
    const adjustmentMode = document.getElementById('adjustmentMode').value;
    
    if (!adjustmentMode) {
        showBalanceModalAlert('Please select adjustment type', 'error');
        return;
    }
    
    // Handle Internal Adjustment (between user's own accounts)
    if (adjustmentMode === 'internal') {
        handleInternalAdjustment();
        return;
    }
    
    // Handle External Adjustment (existing functionality)
    handleExternalAdjustment();
}

function handleInternalAdjustment() {
    const fromAccount = document.getElementById('fromAccount').value;
    const toAccount = document.getElementById('toAccount').value;
    const amount = document.getElementById('internalAmount').value;
    const category = document.getElementById('internalCategory').value;
    const transactionDate = document.getElementById('internalTransactionDate').value;
    const transactionTime = document.getElementById('internalTransactionTime').value;
    const description = document.getElementById('internalDescription').value;
    
    // Validation
    if (!fromAccount || !toAccount || !amount || !category || !transactionDate || !transactionTime) {
        showBalanceModalAlert('Please fill in all required fields', 'error');
        return;
    }
    
    if (fromAccount === toAccount) {
        showBalanceModalAlert('From Account and To Account must be different', 'error');
        return;
    }
    
    const amountNum = parseFloat(amount);
    if (isNaN(amountNum) || amountNum <= 0) {
        showBalanceModalAlert('Please enter a valid amount greater than 0', 'error');
        return;
    }
    
    const transactionData = {
        user_id: <?php echo $userId; ?>,
        adjustment_type: 'internal',
        from_account_id: parseInt(fromAccount),
        to_account_id: parseInt(toAccount),
        amount: amountNum,
        amount_currency: 'display',
        category: 'transfer',
        expense_category: category,
        transaction_date: transactionDate,
        transaction_time: transactionTime,
        description: description || `Internal transfer between accounts`
    };
    
    fetch('<?php echo SITE_URL; ?>/api/admin-adjust-balance.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(transactionData)
    })
    .then(response => response.text())
    .then(text => {
        try {
            const data = JSON.parse(text);
            if (data.success) {
                showToast(data.message || 'Internal transfer completed successfully', 'success');
                closeBalanceModal();
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showBalanceModalAlert(data.message || 'Failed to process internal transfer', 'error');
            }
        } catch (e) {
            console.error('JSON parse error:', e);
            showBalanceModalAlert('Error parsing server response', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showBalanceModalAlert('An error occurred while processing internal transfer', 'error');
    });
}

function handleExternalAdjustment() {
      const amount = document.getElementById('adjustAmount').value;
      const targetAccount = document.getElementById('targetAccount').value;
      const transactionType = document.getElementById('transactionType').value;
      const expenseCategory = document.getElementById('expenseCategory').value;
      const adjustType = document.getElementById('adjustType').value;
      const transactionDate = document.getElementById('transactionDate').value;
      const transactionTime = document.getElementById('transactionTime').value;
      const transactionStatus = document.getElementById('transactionStatus').value;
      const description = document.getElementById('transactionDescription').value;
      
      if (!amount || !targetAccount || !transactionType || !expenseCategory || !adjustType) {
        showBalanceModalAlert('Please fill in all required fields', 'error');
        return;
      }
      
      const amountNum = parseFloat(amount);
      if (isNaN(amountNum) || amountNum <= 0) {
        showBalanceModalAlert('Please enter a valid amount greater than 0', 'error');
        return;
      }
      
      const accountIdNum = parseInt(targetAccount);
      if (isNaN(accountIdNum) || accountIdNum <= 0) {
        showBalanceModalAlert('Please select a valid account', 'error');
        return;
      }
      
      if (adjustType === 'debit' && !transactionStatus) {
        showBalanceModalAlert('Please select transaction status for debit transactions', 'error');
        return;
      }
      
      let recipientAccount = '';
      let recipientName = '';
      let recipientBank = '';
      let swift = '';
      let iban = '';
      
      switch(transactionType) {
        case 'domestic':
          recipientAccount = document.getElementById('domesticAccount').value;
          recipientName = document.getElementById('domesticName').value;
          recipientBank = document.getElementById('domesticBank').value;
          if (!recipientAccount || !recipientName || !recipientBank) {
            showBalanceModalAlert('Please fill in all required fields for domestic transfer', 'error');
            return;
          }
          break;
        case 'international':
          recipientAccount = document.getElementById('intAccount').value;
          recipientName = document.getElementById('intName').value;
          recipientBank = document.getElementById('intBank').value;
          swift = document.getElementById('swift').value;
          iban = document.getElementById('iban').value;
          if (!recipientAccount || !recipientName || !recipientBank) {
            showBalanceModalAlert('Please fill in all required fields for international transfer', 'error');
            return;
          }
          break;
      }
      
      let transactionData = {
        user_id: <?php echo $userId; ?>,
        account_id: parseInt(targetAccount),
        transaction_type: adjustType,
        amount: parseFloat(amount),
        amount_currency: 'display',
        category: 'other',
        expense_category: expenseCategory,
        transaction_method: transactionType,
        status: adjustType === 'credit' ? 'completed' : (transactionStatus || 'pending'),
        transaction_date: transactionDate,
        transaction_time: transactionTime,
        recipient_account: recipientAccount,
        recipient_name: recipientName,
        recipient_bank: recipientBank,
        swift: swift,
        iban: iban,
        description: generateTransactionDescription(adjustType, transactionType, {
          recipient_account: recipientAccount,
          recipient_name: recipientName,
          recipient_bank: recipientBank,
          expense_category: expenseCategory
        })
      };
      
      fetch('<?php echo SITE_URL; ?>/api/admin-adjust-balance.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(transactionData)
    })
    .then(response => response.text())
    .then(text => {
        try {
          const data = JSON.parse(text);
          if (data.success) {
            showToast(data.message, 'success');
            closeBalanceModal();
                setTimeout(() => window.location.reload(), 1500);
          } else {
            showBalanceModalAlert(data.message || 'Failed to adjust balance', 'error');
          }
        } catch (e) {
          console.error('JSON parse error:', e);
          showBalanceModalAlert('Error parsing server response', 'error');
        }
      })
    .catch(error => {
        console.error('Error:', error);
        showBalanceModalAlert('An error occurred while adjusting balance', 'error');
      });
    }

    function toggleTransactionFields() {
      const transactionType = document.getElementById('transactionType').value;
      const dynamicFields = document.getElementById('dynamicFields');
      const allTypeFields = document.querySelectorAll('.transaction-type-fields');
      
      allTypeFields.forEach(field => field.style.display = 'none');
      
      if (transactionType) {
        dynamicFields.style.display = 'block';
        switch(transactionType) {
          case 'domestic':
            document.getElementById('domesticFields').style.display = 'block';
            break;
          case 'international':
            document.getElementById('internationalFields').style.display = 'block';
            break;
        }
      } else {
        dynamicFields.style.display = 'none';
      }
    }

    function resetPassword() {
      showPasswordResetModal();
    }
    
    function showPasswordResetModal() {
      const modal = document.createElement('div');
      modal.id = 'passwordModal';
      modal.className = 'modal';
      modal.innerHTML = `
        <div class="modal-content">
          <div class="modal-header">
            <h3>Reset User Password</h3>
            <span class="close" onclick="closePasswordModal()">&times;</span>
          </div>
          <div class="modal-body">
            <div class="password-reset-form">
              <form id="passwordResetForm">
                <div class="form-group">
                  <label for="newPassword">New Password</label>
                  <input type="password" id="newPassword" class="form-input" placeholder="Enter new password" required>
                </div>
                <div class="form-group">
                  <label for="confirmPassword">Confirm Password</label>
                  <input type="password" id="confirmPassword" class="form-input" placeholder="Confirm new password" required>
                </div>
              </form>
                    <div class="password-requirements" style="margin-top: 8px;">
                        <small style="color: #718096; font-size: 12px;">Password must be at least 8 characters long</small>
              </div>
            </div>
          </div>
          <div class="modal-footer">
                <button class="btn-secondary" onclick="closePasswordModal()">Cancel</button>
                <button class="btn-primary" onclick="submitPasswordReset()">Update Password</button>
          </div>
        </div>
      `;
      
      document.body.appendChild(modal);
      modal.style.display = 'flex';
      document.body.style.overflow = 'hidden';
      
    setTimeout(() => document.getElementById('newPassword').focus(), 100);
    }
    
    function closePasswordModal() {
      const modal = document.getElementById('passwordModal');
      if (modal) {
        modal.remove();
        document.body.style.overflow = '';
      }
    }
    
    function submitPasswordReset() {
      const newPassword = document.getElementById('newPassword').value;
      const confirmPassword = document.getElementById('confirmPassword').value;
      
    if (!newPassword || newPassword.length < 8) {
        showToast('Password must be at least 8 characters long', 'error');
        return;
    }
    
      if (newPassword !== confirmPassword) {
        showToast('Passwords do not match', 'error');
        return;
      }
      
      const userId = <?php echo $userId; ?>;
      
      fetch('/api/admin-reset-user-password.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ user_id: userId, new_password: newPassword })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
          showToast('Password updated successfully', 'success');
          closePasswordModal();
        } else {
          showToast('Error: ' + data.message, 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred while updating password', 'error');
      });
    }

    function openUserCurrencyModal() {
      const existing = document.getElementById('currencyModal');
      if (existing) existing.remove();

      const currencies = <?php echo json_encode($supportedCurrencies); ?>;
      const current = <?php echo json_encode($userCurrency); ?>;
      let optionsHtml = '';
      Object.keys(currencies).forEach(function(code) {
        const selected = code === current ? ' selected' : '';
        optionsHtml += '<option value="' + code + '"' + selected + '>' + code + ' — ' + currencies[code] + '</option>';
      });

      const modal = document.createElement('div');
      modal.id = 'currencyModal';
      modal.className = 'modal';
      modal.innerHTML = `
        <div class="modal-content">
          <div class="modal-header">
            <h3>User Currency</h3>
            <span class="close" onclick="closeUserCurrencyModal()">&times;</span>
          </div>
          <div class="modal-body">
            <p style="margin:0 0 14px; color:#64748b; font-size:14px;">
              Set the display currency for this user. Ledger balances stay in the bank default; amounts are converted for display and transfers.
            </p>
            <div class="form-group">
              <label for="adminUserCurrency">Display currency</label>
              <select id="adminUserCurrency" class="form-input">${optionsHtml}</select>
            </div>
            <div id="currencySaveStatus" style="font-size:12px; color:#64748b; min-height:18px;"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeUserCurrencyModal()">Cancel</button>
            <button type="button" class="btn-primary" id="saveUserCurrencyBtn" onclick="saveUserCurrency()">Save Currency</button>
          </div>
        </div>
      `;
      document.body.appendChild(modal);
      modal.style.display = 'flex';
      document.body.style.overflow = 'hidden';
    }

    function closeUserCurrencyModal() {
      const modal = document.getElementById('currencyModal');
      if (modal) {
        modal.remove();
        document.body.style.overflow = '';
      }
    }

    function saveUserCurrency() {
      const select = document.getElementById('adminUserCurrency');
      const statusEl = document.getElementById('currencySaveStatus');
      const btn = document.getElementById('saveUserCurrencyBtn');
      if (!select) return;
      const currency = select.value;
      if (statusEl) statusEl.textContent = 'Saving...';
      if (btn) btn.disabled = true;

      fetch('<?php echo SITE_URL; ?>/api/admin-set-user-currency.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ user_id: <?php echo (int)$userId; ?>, currency: currency })
      })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          if (statusEl) statusEl.textContent = 'Saved — reloading...';
          showToast('Currency updated to ' + currency, 'success');
          setTimeout(() => location.reload(), 700);
        } else {
          if (statusEl) statusEl.textContent = '';
          if (btn) btn.disabled = false;
          showToast(data.message || 'Failed to update currency', 'error');
        }
      })
      .catch(err => {
        console.error(err);
        if (statusEl) statusEl.textContent = '';
        if (btn) btn.disabled = false;
        showToast('Failed to update currency', 'error');
      });
    }

    function confirmDelete() {
      showModal(
        'Delete User Account',
        'Are you sure you want to delete this user account? This action cannot be undone and will permanently remove all user data.',
        'danger',
        function() {
          const userId = <?php echo $userId; ?>;
          
          fetch('/api/admin-delete-user.php', {
            method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ user_id: userId })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              showToast('User deleted successfully', 'success');
                    setTimeout(() => window.location.href = '/admin/users', 1500);
            } else {
              showToast('Error: ' + data.message, 'error');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred while deleting user', 'error');
          });
        }
      );
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
      if (event.target == modal) {
        modal.style.display = 'none';
      }
    });
}
</script>
