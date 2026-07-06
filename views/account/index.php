<?php 
$pageTitle = 'My Accounts - Octobank';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

// Get user data and accounts
requireLogin();
$userId = $_SESSION['user_id'];

// Get database instance
$db = Database::getInstance();

// Get user accounts (all accessible accounts - own + joint)
$accountModel = new Account();
$userAccounts = $accountModel->getUserAccounts($userId);

// Get owned accounts only (for count display - only count accounts user owns, not joint access)
require_once __DIR__ . '/../../models/JointAccount.php';
$jointAccount = new JointAccount();
$ownedAccounts = $jointAccount->getUserOwnedAccounts($userId);
$isJointAccountUser = $jointAccount->isJointAccountUser($userId);

// If user is a joint account user, sync their access to ensure they have all accounts
// This fixes cases where approval happened before the "all accounts" fix
if ($isJointAccountUser) {
    // Find the primary owner by checking which accounts they have access to
    $accessibleAccounts = $jointAccount->getUserAccessibleAccounts($userId);
    if (!empty($accessibleAccounts)) {
        // Get the primary owner ID from the first account
        $firstAccount = $accessibleAccounts[0];
        $primaryOwnerId = $firstAccount['user_id'];
        
        // Only sync if this is not their own account
        if ($primaryOwnerId != $userId) {
            $jointAccount->syncJointAccountAccess($userId, $primaryOwnerId);
            
            // Refresh accounts after sync
            $userAccounts = $accountModel->getUserAccounts($userId);
        }
    }
}

// Get user info
$userModel = new User();
$userInfo = $userModel->findById($userId);
$userCurrency = getUserDisplayCurrency($userInfo);

// Calculate summary totals in the user's display currency
$totalBalance = sumAccountBalancesForDisplay($userAccounts, $userCurrency);
$totalCreditLimit = 0;
foreach ($userAccounts as $account) {
    if (($account['status'] ?? 'active') !== 'active') {
        continue;
    }
    $convertedBalance = convertCurrencyAmount(
        (float)($account['balance'] ?? 0),
        getAccountStoredCurrency($account),
        $userCurrency
    );
    $totalCreditLimit += $convertedBalance * 2;
}

// Include head
include __DIR__ . '/../../includes/head.php';

// Include sidebar and main structure
include __DIR__ . '/../../includes/sidebar.php';
?>

<!-- ===== ACCOUNTS PAGE CONTENT ===== -->

<style>
        * {
            box-sizing: border-box;
        }

        /* Override parent content-area styles */
        .main-content-area .content-area {
            background: #f5f7fa !important;
            padding: 15px !important;
        }

        .octobank-accounts {
            max-width: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 25px;
            width: 100%;
            box-sizing: border-box;
        }
        
        @media (max-width: 768px) {
            .octobank-accounts {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            
            /* Ensure cards maintain internal padding and don't overflow */
            .summary-card,
            .account-card {
                width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box;
                overflow-x: hidden;
            }
            
            /* Fix grid layouts on mobile */
            .summary-cards {
                grid-template-columns: 1fr !important;
                gap: 15px;
            }
            
            .account-list {
                grid-template-columns: 1fr !important;
                gap: 15px;
            }
        }

        /* ===== PAGE HEADER STANDARD (Same as Dashboard) ===== */
        .header {
            margin-top: 0;
            margin-bottom: 0;
            padding: 0;
        }

        .header h1 {
            font-size: 28px;
            color: #2d3748;
            padding-top: 20px;
            margin: 0 0 8px 0;
            font-weight: 600;
            text-align: left;
        }
        
        .header p {
            font-size: 15px;
            color: #6c757d;
            margin: 0;
            padding-bottom: 20px;
            text-align: left;
        }

        /* Header with button */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
            gap: 20px;
        }
        
        .header-left {
            flex: 1;
            min-width: 0;
        }

        .new-account-btn {
            background: #032B44;
            color: white;
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            text-decoration: none;
            gap: 8px;
            white-space: nowrap;
            flex-shrink: 0;
            margin-top: 20px;
        }

        .new-account-btn:hover {
            background: #024a6b;
            transform: translateY(-2px);
        }

        .new-account-btn:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            transform: none;
        }

        /* Summary Cards */
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 10px;
        }

        .summary-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .summary-card.primary {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
        }

        .summary-label {
            font-size: 14px;
            opacity: 0.8;
            margin-bottom: 8px;
        }

        .summary-value {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .summary-subtext {
            font-size: 12px;
            opacity: 0.8;
        }

        /* Accounts Section */
        .accounts-section {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .section-header {
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 24px;
            font-weight: 600;
            color: #2d3748;
        }

        .accounts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }

        /* Account Card */
        .account-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .account-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .account-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
        }

        .account-checking::before { background: linear-gradient(135deg, #0a2a43, #114678); }
        .account-savings::before { background: linear-gradient(135deg, #dc2626, #f59e0b); }
        .account-business::before { background: linear-gradient(135deg, #059669, #0d9488); }

        .account-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            gap: 10px;
        }

        .account-type {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
            flex: 1;
        }

        .account-icon {
            width: 40px;
            height: 40px;
            min-width: 40px;
            min-height: 40px;
            max-width: 40px;
            max-height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: white;
            flex-shrink: 0;
            flex-grow: 0;
            box-sizing: border-box;
        }

        .icon-checking { background: #114678; }
        .icon-savings { background: #dc2626; }
        .icon-business { background: #059669; }

        .account-name {
            font-size: 18px;
            font-weight: 600;
            color: #2d3748;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .account-number {
            font-size: 14px;
            color: #6b7280;
            margin-top: 2px;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        
        @media (max-width: 480px) {
            .account-name {
                font-size: 16px;
            }
            
            .account-number {
                font-size: 13px;
            }
            
            .account-header {
                flex-wrap: wrap;
            }
            
            .account-status {
                width: 100%;
                text-align: center;
                margin-top: 8px;
            }
        }

        .account-status {
            background: #10b981;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .account-balance {
            margin-bottom: 20px;
        }

        .balance-label {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 5px;
        }

        .balance-amount {
            font-size: 28px;
            font-weight: 700;
            color: #2d3748;
        }

        .account-limits {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .limit-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .limit-item:last-child {
            margin-bottom: 0;
        }

        .limit-label {
            font-size: 12px;
            color: #6b7280;
        }

        .limit-value {
            font-size: 13px;
            font-weight: 600;
            color: #2d3748;
        }

        .account-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .detail-label {
            font-size: 12px;
            color: #6b7280;
            font-weight: 500;
        }

        .detail-value {
            font-size: 14px;
            font-weight: 600;
            color: #2d3748;
        }
        
        .account-actions {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        
        .close-account-btn {
            width: 100%;
            background: #dc2626;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 12px 20px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .close-account-btn:hover {
            background: #b91c1c;
            transform: translateY(-2px);
        }
        
        .close-account-btn:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            transform: none;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
            grid-column: 1 / -1;
        }

        .empty-icon {
            font-size: 64px;
            color: #d1d5db;
            margin-bottom: 20px;
        }

        .empty-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #374151;
        }

        .empty-description {
            font-size: 14px;
            margin-bottom: 25px;
        }

        /* Mobile - maintain left alignment for consistency */
        @media (max-width: 768px) {
            .main-content-area .content-area {
                padding: 10px !important;
            }

            .header h1 {
                font-size: 22px;
                padding-top: 15px;
                margin-bottom: 6px;
            }
            
            .header p {
                font-size: 14px;
                padding-bottom: 18px;
            }

            .page-header {
                flex-direction: column;
                gap: 10px;
                align-items: stretch;
            }
            
            .header-left {
                width: 100%;
            }

            .new-account-btn {
                width: 100%;
                justify-content: center;
                margin-top: 0;
            }

            .summary-cards {
                grid-template-columns: 1fr;
            }

            .accounts-grid {
                grid-template-columns: 1fr;
            }

            .account-details {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 480px) {
            .main-content-area .content-area {
                padding: 5px !important;
            }

            .header h1 {
                font-size: 20px;
                padding-top: 12px;
                margin-bottom: 5px;
            }
            
            .header p {
                font-size: 13px;
                padding-bottom: 15px;
            }

            .accounts-section {
                padding: 15px;
            }
            
            .account-card {
                padding: 15px;
                min-width: 0;
                overflow: hidden;
            }
            
            .account-limits {
                padding: 12px;
            }
            
            .limit-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
            }

            .balance-amount {
                font-size: 24px;
            }
        }
</style>

<div class="octobank-accounts">
    <!-- Header -->
    <div class="page-header">
        <div class="header-left">
            <div class="header">
                <h1>My Accounts</h1>
                <p>Manage your bank accounts and view balances</p>
            </div>
        </div>
        <div style="display: flex; gap: 12px; align-items: center;">
            <?php
            // Check if user has pending joint account requests
            require_once __DIR__ . '/../../models/JointAccount.php';
            $jointAccount = new JointAccount();
            $pendingRequests = $jointAccount->getPendingRequests($userId);
            $requestCount = count($pendingRequests);
            ?>
            <a href="<?php echo SITE_URL; ?>/account/joint-requests" class="new-account-btn" style="background: #10b981; position: relative;">
                <i class="fas fa-users"></i>
                Joint Requests
                <?php if ($requestCount > 0): ?>
                <span style="position: absolute; top: -8px; right: -8px; background: #ef4444; color: white; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600;">
                    <?php echo $requestCount; ?>
                </span>
                <?php endif; ?>
            </a>
            <?php if (!$isJointAccountUser): ?>
        <button class="new-account-btn" onclick="window.location.href='<?php echo SITE_URL; ?>/account/create'">
            <i class="fas fa-plus"></i>
            Open New Account
        </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card primary">
            <div class="summary-label">Total Balance</div>
            <div class="summary-value"><?php echo formatCurrency($totalBalance, $userCurrency, $userCurrency); ?></div>
            <div class="summary-subtext">Across all your accounts</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">Available Accounts</div>
            <div class="summary-value">
                <?php 
                // For joint account users, show count of accessible accounts (shared accounts)
                // For primary owners, show count of owned accounts
                $accountCount = $isJointAccountUser ? count($userAccounts) : count($ownedAccounts);
                echo $accountCount; 
                ?>/3
            </div>
            <div class="summary-subtext">Checking, Savings & Business</div>
        </div>

        <div class="summary-card">
            <div class="summary-label">Total Credit Limit</div>
            <div class="summary-value"><?php echo formatCurrency($totalCreditLimit, $userCurrency, $userCurrency); ?></div>
            <div class="summary-subtext">Combined account limits</div>
        </div>
    </div>

    <!-- Accounts Section -->
    <div class="accounts-section">
        <div class="section-header">
            <h2 class="section-title">Your Accounts</h2>
        </div>

        <div class="accounts-grid">
            <?php if (empty($userAccounts)): ?>
                <!-- Empty State -->
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <h3 class="empty-title">No accounts yet</h3>
                    <p class="empty-description">Open your first account to get started with Octobank</p>
                </div>
            <?php else: ?>
                <!-- Account Cards -->
                <?php foreach ($userAccounts as $account): 
                    // Handle legacy 'joint' account_type - map to actual account type
                    // If account_type is 'joint', we need to determine the actual type
                    // For now, default to 'checking' if it's 'joint' (legacy data)
                    $accountType = $account['account_type'];
                    if ($accountType === 'joint') {
                        // Try to get the original account type from account_name or default to checking
                        $accountNameFromDB = $account['account_name'] ?? '';
                        if (stripos($accountNameFromDB, 'savings') !== false) {
                            $accountType = 'savings';
                        } elseif (stripos($accountNameFromDB, 'business') !== false) {
                            $accountType = 'business';
                        } else {
                            $accountType = 'checking'; // Default fallback
                        }
                    }
                    
                    $cardClass = 'account-' . $accountType;
                    $iconClass = 'icon-' . $accountType;
                    
                    // Set icon based on account type
                    $icon = 'fa-money-bill-wave';
                    if ($accountType === 'savings') {
                        $icon = 'fa-piggy-bank';
                    } elseif ($accountType === 'business') {
                        $icon = 'fa-briefcase';
                    }
                    
                    // Get limits from system settings based on account type
                    require_once __DIR__ . '/../../includes/functions.php';
                    $dailyLimit = getDailyLimitForAccountType($accountType);
                    $monthlyLimit = getMonthlyLimitForAccountType($accountType);
                    
                    $accountName = ucfirst($accountType) . ' Account';
                    $accountNumber = $account['account_number'];
                    $balance = $account['balance'];
                ?>
                <div class="account-card <?php echo $cardClass; ?>">
                    <div class="account-header">
                        <div class="account-type">
                            <div class="account-icon <?php echo $iconClass; ?>">
                                <i class="fas <?php echo $icon; ?>"></i>
                            </div>
                            <div>
                                <div class="account-name"><?php echo htmlspecialchars($accountName); ?></div>
                                <div class="account-number"><?php echo htmlspecialchars($accountNumber); ?></div>
                            </div>
                        </div>
                        <div class="account-status"><?php echo ucfirst($account['status']); ?></div>
                    </div>

                    <div class="account-balance">
                        <div class="balance-label">Current Balance</div>
                        <div class="balance-amount"><?php echo formatAccountBalance($balance, $account, $userCurrency); ?></div>
                    </div>

                    <div class="account-limits">
                        <div class="limit-item">
                            <span class="limit-label">Daily Transaction Limit</span>
                            <span class="limit-value"><?php echo formatCurrency($dailyLimit, $userCurrency, DEFAULT_CURRENCY); ?></span>
                        </div>
                        <div class="limit-item">
                            <span class="limit-label">Monthly Limit</span>
                            <span class="limit-value"><?php echo formatCurrency($monthlyLimit, $userCurrency, DEFAULT_CURRENCY); ?></span>
                        </div>
                    </div>

                    <div class="account-details">
                        <div class="detail-item">
                            <div class="detail-label">Account Type</div>
                            <div class="detail-value"><?php echo ucfirst($accountType); ?></div>
                        </div>
                    </div>
                    
                    <?php 
                    // Only show Close Account button if:
                    // 1. User owns the account (not joint access)
                    // 2. User has more than 1 active account
                    $accountIsOwned = ($account['user_id'] == $userId);
                    $activeAccountsCount = count(array_filter($userAccounts, function($acc) {
                        return $acc['status'] === 'active';
                    }));
                    ?>
                    <?php if ($accountIsOwned && $activeAccountsCount > 1 && $account['status'] === 'active'): ?>
                        <div class="account-actions">
                            <button class="close-account-btn" onclick="closeAccount(<?php echo $account['id']; ?>, '<?php echo htmlspecialchars($accountNumber); ?>', '<?php echo htmlspecialchars($accountName); ?>', <?php echo $balance; ?>)">
                                <i class="fas fa-times-circle"></i>
                                Close Account
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Global variable to store account to close
let accountToClose = null;

function closeAccount(accountId, accountNumber, accountName, balance) {
    accountToClose = {
        id: accountId,
        number: accountNumber,
        name: accountName,
        balance: parseFloat(balance)
    };
    
    // If account has balance, show modal to select destination account
    if (balance > 0) {
        showCloseAccountModal();
    } else {
        // No balance, proceed with closing directly
        confirmCloseAccount(null);
    }
}

function showCloseAccountModal() {
    const modal = document.createElement('div');
    modal.id = 'closeAccountModal';
    modal.className = 'modal';
    modal.innerHTML = `
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h3>Close Account</h3>
                <span class="close" onclick="closeCloseAccountModal()">&times;</span>
            </div>
            <div class="modal-body">
                <p style="margin-bottom: 20px;">
                    Account <strong>${accountToClose.number}</strong> has a balance of 
                    <strong><?php echo $userCurrency; ?> ${accountToClose.balance.toFixed(2)}</strong>.
                    Please select a destination account to transfer the funds.
                </p>
                <div class="form-group">
                    <label for="destinationAccount">Transfer funds to:</label>
                    <select id="destinationAccount" class="form-input" required>
                        <option value="">Select destination account</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeCloseAccountModal()">Cancel</button>
                <button class="btn-primary" onclick="confirmCloseAccount()">Close Account</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    
    // Load active accounts (excluding the account being closed)
    loadActiveAccountsForClose();
}

function loadActiveAccountsForClose() {
    const select = document.getElementById('destinationAccount');
    if (!select) {
        console.error('Destination account select not found');
        return;
    }
    
    select.innerHTML = '<option value="">Loading accounts...</option>';
    
    fetch('<?php echo SITE_URL; ?>/api/get-user-accounts.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ user_id: <?php echo $userId; ?> })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text();
    })
    .then(text => {
        console.log('Raw response:', text);
        try {
            const data = JSON.parse(text);
            console.log('Parsed data:', data);
            
            select.innerHTML = '<option value="">Select destination account</option>';
            
            if (!data.success) {
                select.innerHTML = `<option value="">Error: ${data.message || 'Failed to load accounts'}</option>`;
                console.error('API error:', data.message);
                return;
            }
            
            if (data.accounts && Array.isArray(data.accounts)) {
                let hasOptions = false;
                
                data.accounts.forEach(account => {
                    // Exclude the account being closed
                    if (parseInt(account.id) === parseInt(accountToClose.id)) {
                        return;
                    }
                    
                    // Only show active accounts (though API already filters for active)
                    if (account.status !== 'active') {
                        return;
                    }
                    
                    const option = document.createElement('option');
                    option.value = account.id;
                    const balance = parseFloat(account.balance || 0);
                    const balanceFormatted = new Intl.NumberFormat('en-US', {
                        style: 'currency',
                        currency: '<?php echo $userCurrency; ?>',
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(balance);
                    option.textContent = `${account.account_type || 'Account'} - ${account.account_number || 'N/A'} (${balanceFormatted})`;
                    select.appendChild(option);
                    hasOptions = true;
                });
                
                if (!hasOptions) {
                    select.innerHTML = '<option value="">No active accounts available</option>';
                }
            } else {
                select.innerHTML = '<option value="">No accounts found</option>';
            }
        } catch (e) {
            console.error('Error parsing response:', e);
            console.error('Response text:', text);
            select.innerHTML = '<option value="">Error loading accounts</option>';
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        const select = document.getElementById('destinationAccount');
        if (select) {
            select.innerHTML = '<option value="">Error loading accounts</option>';
        }
    });
}

function confirmCloseAccount(destinationAccountId) {
    // If modal is shown, get destination account from select
    if (!destinationAccountId) {
        const select = document.getElementById('destinationAccount');
        if (select) {
            destinationAccountId = select.value;
            // If balance > 0 but no destination selected, show error
            if (accountToClose.balance > 0 && !destinationAccountId) {
                alert('Please select a destination account to transfer funds.');
                return;
            }
        }
    }
    
    if (accountToClose.balance > 0 && !destinationAccountId) {
        alert('Please select a destination account to transfer funds.');
        return;
    }
    
    // Confirm action
    const confirmMessage = accountToClose.balance > 0
        ? `Close account ${accountToClose.number} and transfer ${accountToClose.balance.toFixed(2)} to selected account?`
        : `Are you sure you want to close account ${accountToClose.number}?`;
    
    if (!confirm(confirmMessage)) {
        return;
    }
    
    // Disable button
    const btn = event.target;
    btn.disabled = true;
    btn.textContent = 'Processing...';
    
    // Send request to close account
    fetch('<?php echo SITE_URL; ?>/api/close-account.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            account_id: accountToClose.id,
            destination_account_id: destinationAccountId || null
        })
    })
    .then(response => response.text())
    .then(text => {
        try {
            const data = JSON.parse(text);
            if (data.success) {
                alert('Account closed successfully!');
                window.location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to close account'));
                btn.disabled = false;
                btn.textContent = 'Close Account';
            }
        } catch (e) {
            console.error('JSON parse error:', e);
            alert('Error parsing server response');
            btn.disabled = false;
            btn.textContent = 'Close Account';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while closing account');
        btn.disabled = false;
        btn.textContent = 'Close Account';
    });
}

function closeCloseAccountModal() {
    const modal = document.getElementById('closeAccountModal');
    if (modal) {
        modal.remove();
        document.body.style.overflow = '';
    }
    accountToClose = null;
}
</script>

<style>
/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    align-items: center;
    justify-content: center;
}

.modal-content {
    background-color: white;
    border-radius: 16px;
    padding: 0;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    max-width: 600px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    padding: 20px 25px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    font-size: 20px;
    font-weight: 600;
    color: #2d3748;
}

.modal-header .close {
    color: #9ca3af;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    line-height: 1;
}

.modal-header .close:hover {
    color: #6b7280;
}

.modal-body {
    padding: 25px;
}

.modal-footer {
    padding: 20px 25px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.btn-secondary, .btn-primary {
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    transition: all 0.3s;
}

.btn-secondary {
    background: #f3f4f6;
    color: #374151;
}

.btn-secondary:hover {
    background: #e5e7eb;
}

.btn-primary {
    background: #dc2626;
    color: white;
}

.btn-primary:hover {
    background: #b91c1c;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #374151;
    font-size: 14px;
}

.form-input {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.3s;
}

.form-input:focus {
    outline: none;
    border-color: #3b82f6;
}
</style>

<?php
// Include mobile navigation and closing tags
include __DIR__ . '/../../includes/mobile-nav.php';
?>
