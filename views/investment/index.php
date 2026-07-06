<?php 
$pageTitle = 'Investments - ' . getSiteName();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../models/InvestmentProduct.php';
require_once __DIR__ . '/../../models/Account.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/CryptoWallet.php';

requireLogin();

// Include head and sidebar
include __DIR__ . '/../../includes/head.php';
include __DIR__ . '/../../includes/sidebar.php';

include __DIR__ . '/../../includes/restricted-banner.php';

// Get data from controller
$productModel = new InvestmentProduct();

// Get filters
$filters = [
    'type' => $_GET['type'] ?? null,
    'search' => $_GET['search'] ?? null,
    'sort' => $_GET['sort'] ?? 'newest',
    'status' => 'active'
];

$products = $productModel->getAll($filters);

// Group by type
$groupedProducts = [
    'stocks' => [],
    'forex' => [],
    'crypto' => []
];

foreach ($products as $product) {
    if (isset($groupedProducts[$product['type']])) {
        $groupedProducts[$product['type']][] = $product;
    }
}

// Check KYC status
$userModel = new User();
$user = $userModel->findById($_SESSION['user_id']);
$kycVerified = ($user['kyc_status'] ?? '') === 'verified';
$userCurrency = getUserDisplayCurrency($user);

// Get active crypto wallets configured by admin (not investment products)
// These are the wallets users can send crypto to for funding their investment balance
$cryptoWalletModel = new CryptoWallet();
$activeCryptoWallets = $cryptoWalletModel->getAll(true); // Get only active wallets
?>

<style>
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
    border-radius: 24px;
    padding: 30px;
    max-width: 600px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 1px solid #e5e7eb;
}

.modal-header h2 {
    font-size: 24px;
    color: #032B44;
}

.close-modal {
    background: none;
    border: none;
    font-size: 28px;
    color: #6b7280;
    cursor: pointer;
    padding: 0;
    width: 32px;
    height: 32px;
    line-height: 1;
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

.btn-invest {
    background: linear-gradient(135deg, #032B44 0%, #024a6b 100%);
    color: white;
    padding: 14px 24px;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.btn-invest:hover {
    background: linear-gradient(135deg, #024a6b 0%, #032B44 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(3, 43, 68, 0.3);
}

.btn-invest:disabled {
    background: #9ca3af;
    cursor: not-allowed;
    transform: none;
    opacity: 0.6;
}

.page-header {
    margin-bottom: 30px;
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
}

.filters-section {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 30px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.filter-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.filter-tab {
    padding: 10px 20px;
    border-radius: 8px;
    background: #f3f4f6;
    color: #374151;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
    border: 2px solid transparent;
}

.filter-tab:hover {
    background: #e5e7eb;
}

.filter-tab.active {
    background: linear-gradient(135deg, #032B44 0%, #024a6b 100%);
    color: white;
}

.search-sort-row {
    display: flex;
    gap: 15px;
    align-items: center;
    flex-wrap: wrap;
}

.search-box {
    flex: 1;
    min-width: 200px;
}

.search-box input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 16px;
}

.sort-select {
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 16px;
    background: white;
}

.investments-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

@media (max-width: 1024px) {
    .investments-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }
}

@media (max-width: 768px) {
    .investments-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
}

.investment-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 24px;
    padding: 0;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s;
    overflow: hidden;
}

.investment-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 24px 48px rgba(0,0,0,0.15);
}

.investment-image-wrapper {
    width: 100%;
    height: 140px;
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    overflow: hidden;
    padding-left: 18px;
}

.investment-image {
    width: 72px;
    height: 72px;
    max-width: 72px;
    max-height: 72px;
    object-fit: contain;
    object-position: left center;
    padding: 0;
}

.investment-image-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #032B44 0%, #024a6b 100%);
    color: white;
    font-size: 48px;
    font-weight: 700;
    text-transform: uppercase;
}

.investment-card-content {
    padding: 24px;
}

.investment-type-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 12px;
    text-transform: uppercase;
}

.type-stocks {
    background: #dbeafe;
    color: #1e40af;
}

.type-forex {
    background: #fef3c7;
    color: #92400e;
}

.type-crypto {
    background: #f3e8ff;
    color: #6b21a8;
}

.investment-card h3 {
    font-size: 20px;
    font-weight: 700;
    color: #032B44;
    margin-bottom: 8px;
}

.investment-card p {
    color: #666;
    font-size: 14px;
    margin-bottom: 16px;
    line-height: 1.5;
}

@media (max-width: 768px) {
    .investment-image-wrapper {
        height: 110px;
        padding-left: 14px;
    }
    
    .investment-image {
        width: 56px;
        height: 56px;
        max-width: 56px;
        max-height: 56px;
    }

    .investment-card h3 {
        font-size: 16px;
    }
    
    .investment-card p {
        font-size: 12px;
        margin-bottom: 12px;
    }
}

.investment-details {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 16px;
    padding: 16px;
    background: #f9fafb;
    border-radius: 12px;
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
    font-size: 16px;
    color: #032B44;
    font-weight: 600;
}

@media (max-width: 768px) {
    .detail-label {
        font-size: 10px;
    }
    
    .detail-value {
        font-size: 13px;
    }
}

.roi-badge {
    display: inline-block;
    padding: 8px 16px;
    border-radius: 8px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    font-weight: 700;
    font-size: 18px;
    margin-bottom: 16px;
}

@media (max-width: 768px) {
    .roi-badge {
        padding: 6px 12px;
        font-size: 14px;
        margin-bottom: 12px;
    }
}

.btn-view {
    width: 100%;
    padding: 12px 24px;
    background: linear-gradient(135deg, #032B44 0%, #024a6b 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
    display: block;
    text-align: center;
}

@media (max-width: 768px) {
    .btn-view {
        padding: 8px 16px;
        font-size: 14px;
    }
}

.btn-view:hover {
    background: linear-gradient(135deg, #024a6b 0%, #032B44 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(3, 43, 68, 0.3);
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 24px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
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

.kyc-warning {
    background: #fef3c7;
    border: 2px solid #fbbf24;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.kyc-warning i {
    font-size: 24px;
    color: #f59e0b;
}

.kyc-warning-text {
    flex: 1;
    color: #92400e;
}

.kyc-warning a {
    color: #032B44;
    font-weight: 600;
    text-decoration: underline;
}

@media (max-width: 768px) {
    .investments-grid {
        grid-template-columns: 1fr;
    }
    
    .filter-tabs {
        justify-content: center;
    }
    
    .search-sort-row {
        flex-direction: column;
    }
    
    .search-box, .sort-select {
        width: 100%;
    }
}
</style>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px;">
    <div>
        <h1>Investments — Stocks, Forex & Crypto</h1>
        <p>Choose an investment plan, pick the amount and duration, see projected returns, and start earning — safely and transparently.</p>
    </div>
    <div>
        <a href="<?php echo SITE_URL; ?>/investment/my-investments" class="btn-invest" style="width: auto; white-space: nowrap;">
            <i class="fas fa-chart-line"></i> My Investments
        </a>
    </div>
</div>

<!-- Investment Balance & Stats Section -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
    <div style="background: linear-gradient(135deg, #032B44 0%, #024a6b 100%); border-radius: 16px; padding: 24px; color: white; box-shadow: 0 4px 12px rgba(3, 43, 68, 0.3);">
        <div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">Investment Balance</div>
        <div style="font-size: 32px; font-weight: 700; margin-bottom: 12px;"><?php echo formatUserInvestmentBalanceForUser($investmentStats['investment_balance'] ?? 0, $user); ?></div>
        <div style="display: flex; gap: 12px;">
            <button onclick="openFundModal()" style="flex: 1; padding: 10px; background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); border-radius: 8px; color: white; font-weight: 600; cursor: pointer; transition: all 0.3s;">
                <i class="fas fa-plus-circle"></i> Fund
            </button>
            <button onclick="openWithdrawModal()" style="flex: 1; padding: 10px; background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); border-radius: 8px; color: white; font-weight: 600; cursor: pointer; transition: all 0.3s;">
                <i class="fas fa-arrow-circle-down"></i> Withdraw
            </button>
        </div>
    </div>
    
    <div style="background: rgba(255, 255, 255, 0.95); border-radius: 16px; padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
        <div style="font-size: 14px; color: #6b7280; margin-bottom: 8px;">Total Invested</div>
        <div style="font-size: 28px; font-weight: 700; color: #032B44;"><?php echo formatInvestmentAmountForUser($investmentStats['total_invested'] ?? 0, $user); ?></div>
        <div style="font-size: 12px; color: #10b981; margin-top: 8px;">
            <i class="fas fa-chart-line"></i> <?php echo $investmentStats['active_investments'] ?? 0; ?> Active
        </div>
    </div>
    
    <div style="background: rgba(255, 255, 255, 0.95); border-radius: 16px; padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
        <div style="font-size: 14px; color: #6b7280; margin-bottom: 8px;">Total ROI Earned</div>
        <div style="font-size: 28px; font-weight: 700; color: #10b981;"><?php echo formatInvestmentAmountForUser($investmentStats['total_roi_earned'] ?? 0, $user); ?></div>
        <div style="font-size: 12px; color: #6b7280; margin-top: 8px;">
            <i class="fas fa-coins"></i> Total Returns
        </div>
    </div>
    
    <div style="background: rgba(255, 255, 255, 0.95); border-radius: 16px; padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
        <div style="font-size: 14px; color: #6b7280; margin-bottom: 8px;">Current Value</div>
        <div style="font-size: 28px; font-weight: 700; color: #032B44;"><?php echo formatInvestmentAmountForUser($investmentStats['total_current_value'] ?? 0, $user); ?></div>
        <div style="font-size: 12px; color: #6b7280; margin-top: 8px;">
            Principal + ROI
        </div>
    </div>
</div>

<?php if (!$kycVerified): ?>
<div class="kyc-warning">
    <i class="fas fa-exclamation-triangle"></i>
    <div class="kyc-warning-text">
        <strong>KYC Verification Required:</strong> Please complete your KYC verification before investing. 
        <a href="<?php echo SITE_URL; ?>/profile/kyc">Complete KYC Now →</a>
    </div>
</div>
<?php endif; ?>

<div class="filters-section">
    <div class="filter-tabs">
        <a href="<?php echo SITE_URL; ?>/investment" class="filter-tab <?php echo empty($filters['type']) ? 'active' : ''; ?>">All</a>
        <a href="<?php echo SITE_URL; ?>/investment?type=stocks" class="filter-tab <?php echo $filters['type'] === 'stocks' ? 'active' : ''; ?>">Stocks</a>
        <a href="<?php echo SITE_URL; ?>/investment?type=forex" class="filter-tab <?php echo $filters['type'] === 'forex' ? 'active' : ''; ?>">Forex</a>
        <a href="<?php echo SITE_URL; ?>/investment?type=crypto" class="filter-tab <?php echo $filters['type'] === 'crypto' ? 'active' : ''; ?>">Crypto</a>
    </div>
    
    <form method="GET" action="<?php echo SITE_URL; ?>/investment" class="search-sort-row">
        <?php if (!empty($filters['type'])): ?>
        <input type="hidden" name="type" value="<?php echo htmlspecialchars($filters['type']); ?>">
        <?php endif; ?>
        
        <div class="search-box">
            <input type="text" name="search" placeholder="Search investments..." value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>">
        </div>
        
        <select name="sort" class="sort-select" onchange="this.form.submit()">
            <option value="newest" <?php echo $filters['sort'] === 'newest' ? 'selected' : ''; ?>>Newest First</option>
            <option value="highest_roi" <?php echo $filters['sort'] === 'highest_roi' ? 'selected' : ''; ?>>Highest ROI</option>
            <option value="lowest_min" <?php echo $filters['sort'] === 'lowest_min' ? 'selected' : ''; ?>>Lowest Min Amount</option>
        </select>
        
        <button type="submit" style="display: none;"></button>
    </form>
</div>

<?php if (empty($products)): ?>
<div class="empty-state">
    <i class="fas fa-chart-line"></i>
    <h3>No Investments Available</h3>
    <p>There are currently no active investment products. Please check back later.</p>
</div>
<?php else: ?>
<div class="investments-grid">
    <?php foreach ($products as $product): 
        // Calculate ROI metrics
        $roiConfig = json_decode($product['roi_config'] ?? '{}', true);
        if (($roiConfig['mode'] ?? '') === 'annual') {
            $annualROI = $roiConfig['annual_percent'] ?? 0;
            $dailyROI = $annualROI / 365;
        } else {
            $dailyROI = $roiConfig['daily_percent'] ?? 0;
            $annualROI = $dailyROI * 365;
        }
        
        // Get remaining capacity
        $remainingCapacity = $productModel->getRemainingCapacity($product['id']);
    ?>
    <div class="investment-card">
        <div class="investment-image-wrapper">
            <?php if (!empty($product['image_url'])): ?>
            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>" class="investment-image" onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 200 200%27%3E%3Crect fill=%27%23f9fafb%27 width=%27200%27 height=%27200%27/%3E%3Ctext x=%2750%25%27 y=%2750%25%27 text-anchor=%27middle%27 fill=%27%239ca3af%27 font-size=%2720%27 dy=%27.3em%27%3E<?php echo htmlspecialchars(mb_substr($product['title'], 0, 10)); ?>%3C/text%3E%3C/svg%3E';">
            <?php else: ?>
            <div class="investment-image-placeholder">
                <span><?php echo htmlspecialchars(mb_substr($product['title'], 0, 3)); ?></span>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="investment-card-content">
            <span class="investment-type-badge type-<?php echo $product['type']; ?>"><?php echo ucfirst($product['type']); ?></span>
            
            <h3><?php echo htmlspecialchars($product['title']); ?></h3>
            <p><?php echo htmlspecialchars($product['short_description'] ?? 'Investment opportunity'); ?></p>
            
            <div class="investment-details">
                <div class="detail-item">
                    <span class="detail-label">Min Amount</span>
                    <span class="detail-value"><?php echo formatInvestmentAmountForUser($product['min_amount'], $user); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Max Amount</span>
                    <span class="detail-value"><?php echo $product['max_amount'] ? formatInvestmentAmountForUser($product['max_amount'], $user) : 'No Limit'; ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Min Duration</span>
                    <span class="detail-value"><?php echo $product['min_duration_days']; ?> days</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Max Duration</span>
                    <span class="detail-value"><?php echo $product['max_duration_days'] ? $product['max_duration_days'] . ' days' : 'No Limit'; ?></span>
                </div>
            </div>
            
            <div class="roi-badge">
                <?php if ($annualROI > 0): ?>
                    <?php echo number_format($annualROI, 2); ?>% Annual
                <?php else: ?>
                    <?php echo number_format($dailyROI, 4); ?>% Daily
                <?php endif; ?>
            </div>
            
            <a href="<?php echo SITE_URL; ?>/investment/view/<?php echo $product['id']; ?>" class="btn-view">View Details</a>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Fund Modal -->
<div id="fundModal" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h2>Fund Investment Account</h2>
            <button class="close-modal" onclick="closeFundModal()">&times;</button>
        </div>
        
        <form id="fundForm" method="POST" action="<?php echo SITE_URL; ?>/investment/fund">
            <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label class="form-label">Amount</label>
                <input type="number" name="amount" class="form-control" step="0.01" min="1" required>
            </div>
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label class="form-label">Funding Method</label>
                <select name="funding_method" id="fundingMethod" class="form-control" required onchange="toggleFundingMethod()">
                    <option value="bank_balance">From Bank Balance</option>
                    <?php 
                    // Only show crypto options for wallets that admin has configured
                    if (!empty($activeCryptoWallets)):
                        $cryptoLabels = [
                            'btc' => 'Bitcoin',
                            'eth' => 'Ethereum',
                            'usdt' => 'USDT (Tether)',
                            'usdc' => 'USDC',
                            'ltc' => 'Litecoin',
                            'bnb' => 'Binance Coin',
                            'ada' => 'Cardano',
                            'sol' => 'Solana',
                            'xrp' => 'Ripple',
                            'doge' => 'Dogecoin',
                            'matic' => 'Polygon',
                            'trx' => 'Tron'
                        ];
                        
                        foreach ($activeCryptoWallets as $wallet):
                            $cryptoType = strtolower($wallet['crypto_type']);
                            $cryptoLabel = $cryptoLabels[$cryptoType] ?? ucfirst($cryptoType);
                            $displayLabel = $wallet['label'] ? htmlspecialchars($wallet['label']) : $cryptoLabel;
                    ?>
                    <option value="crypto_<?php echo htmlspecialchars($cryptoType); ?>">
                        <?php echo $displayLabel; ?> (<?php echo strtoupper($cryptoType); ?>)
                    </option>
                    <?php 
                        endforeach;
                    endif;
                    ?>
                </select>
                <?php if (empty($activeCryptoWallets)): ?>
                <small style="color: #6b7280; display: block; margin-top: 8px;">
                    No crypto wallets configured by admin. Only bank balance funding is available.
                </small>
                <?php endif; ?>
            </div>
            
            <div id="bankAccountGroup" class="form-group" style="margin-bottom: 20px;">
                <label class="form-label">Select Account</label>
                <select name="account_id" class="form-control">
                    <?php 
                    if (!isset($userAccounts)) {
                        $accountModel = new Account();
                        $userAccounts = $accountModel->getUserAccounts($_SESSION['user_id']);
                    }
                    foreach ($userAccounts as $acc): 
                    ?>
                    <option value="<?php echo $acc['id']; ?>">
                        <?php echo htmlspecialchars($acc['account_name'] ?? $acc['account_type']); ?> - 
                        <?php echo formatAccountBalance($acc['balance'], $acc, $userCurrency); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div id="cryptoGroup" class="form-group" style="margin-bottom: 20px; display: none;">
                <div style="padding: 16px; background: #f9fafb; border-radius: 8px; margin-bottom: 12px;">
                    <p style="font-size: 14px; color: #6b7280;">You will be redirected to make crypto payment after submitting this form.</p>
                </div>
                <input type="hidden" name="crypto_currency" id="cryptoCurrency">
            </div>
            
            <button type="submit" class="btn-invest" style="width: 100%;">
                <i class="fas fa-arrow-circle-up"></i> Continue
            </button>
        </form>
    </div>
</div>

<!-- Withdraw Modal -->
<div id="withdrawModal" class="modal">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h2>Withdraw from Investment Account</h2>
            <button class="close-modal" onclick="closeWithdrawModal()">&times;</button>
        </div>
        
        <form id="withdrawForm" method="POST" action="<?php echo SITE_URL; ?>/investment/withdraw">
            <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label class="form-label">Available Balance</label>
                <div style="font-size: 24px; font-weight: 700; color: #032B44;">
                    <?php echo formatUserInvestmentBalanceForUser($investmentStats['investment_balance'] ?? 0, $user); ?>
                </div>
            </div>
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label class="form-label">Withdrawal Amount</label>
                <input type="number" name="amount" class="form-control" step="0.01" min="1" 
                       max="<?php echo $investmentStats['investment_balance'] ?? 0; ?>" required>
            </div>
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label class="form-label">Withdrawal Method</label>
                <select name="withdrawal_method" id="withdrawalMethod" class="form-control" required onchange="toggleWithdrawalMethod()">
                    <option value="bank_balance">To Bank Balance</option>
                    <option value="external_account">External Bank Account</option>
                    <option value="paypal">PayPal</option>
                    <option value="venmo">Venmo</option>
                    <option value="crypto_btc">Bitcoin (BTC)</option>
                    <option value="crypto_eth">Ethereum (ETH)</option>
                    <option value="crypto_usdt">USDT</option>
                    <option value="crypto_ltc">Litecoin (LTC)</option>
                </select>
            </div>
            
            <div id="bankAccountWithdrawGroup" class="form-group" style="margin-bottom: 20px;">
                <label class="form-label">Select Account</label>
                <select name="account_id" class="form-control">
                    <option value="">Choose account...</option>
                    <?php 
                    if (!isset($userAccounts)) {
                        $accountModel = new Account();
                        $userAccounts = $accountModel->getUserAccounts($_SESSION['user_id']);
                    }
                    foreach ($userAccounts as $acc): ?>
                    <option value="<?php echo $acc['id']; ?>">
                        <?php echo htmlspecialchars($acc['account_name'] ?? $acc['account_type']); ?> - 
                        <?php echo formatAccountBalance($acc['balance'], $acc, $userCurrency); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" name="recipient_type" value="bank_account">
            </div>
            
            <div id="externalAccountGroup" style="display: none;">
                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label">Account Number</label>
                    <input type="text" name="account_number" class="form-control">
                </div>
                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label">Routing Number</label>
                    <input type="text" name="routing_number" class="form-control">
                </div>
                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label">Account Holder Name</label>
                    <input type="text" name="account_name" class="form-control">
                </div>
                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label">Bank Name</label>
                    <input type="text" name="bank_name" class="form-control">
                </div>
                <input type="hidden" name="recipient_type" value="bank_account">
            </div>
            
            <div id="paypalGroup" style="display: none;">
                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label">PayPal Email</label>
                    <input type="email" name="paypal_email" class="form-control">
                </div>
                <input type="hidden" name="recipient_type" value="paypal_email">
            </div>
            
            <div id="venmoGroup" style="display: none;">
                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label">Venmo Phone Number</label>
                    <input type="tel" name="venmo_phone" class="form-control" placeholder="+1234567890">
                </div>
                <input type="hidden" name="recipient_type" value="venmo_phone">
            </div>
            
            <div id="cryptoWithdrawGroup" style="display: none;">
                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label">Crypto Wallet Address</label>
                    <input type="text" name="crypto_address" class="form-control" placeholder="Enter wallet address">
                </div>
                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label">Network (if applicable)</label>
                    <input type="text" name="crypto_network" class="form-control" placeholder="e.g., ERC20, TRC20, mainnet">
                </div>
                <input type="hidden" name="recipient_type" value="crypto_address">
            </div>
            
            <button type="submit" class="btn-invest" style="width: 100%;">
                <i class="fas fa-check-circle"></i> Submit Withdrawal Request
            </button>
        </form>
    </div>
</div>

<script>
function openFundModal() {
    document.getElementById('fundModal').classList.add('active');
}

function closeFundModal() {
    document.getElementById('fundModal').classList.remove('active');
}

function openWithdrawModal() {
    document.getElementById('withdrawModal').classList.add('active');
}

function closeWithdrawModal() {
    document.getElementById('withdrawModal').classList.remove('active');
}

function toggleFundingMethod() {
    const methodSelect = document.getElementById('fundingMethod');
    if (!methodSelect) return;
    
    const method = methodSelect.value;
    const bankGroup = document.getElementById('bankAccountGroup');
    const cryptoGroup = document.getElementById('cryptoGroup');
    const cryptoCurrency = document.getElementById('cryptoCurrency');
    
    if (!bankGroup || !cryptoGroup || !cryptoCurrency) return;
    
    if (method === 'bank_balance') {
        bankGroup.style.display = 'block';
        cryptoGroup.style.display = 'none';
    } else {
        bankGroup.style.display = 'none';
        cryptoGroup.style.display = 'block';
        cryptoCurrency.value = method.replace('crypto_', '').toUpperCase();
    }
}

function toggleWithdrawalMethod() {
    const method = document.getElementById('withdrawalMethod').value;
    document.getElementById('bankAccountWithdrawGroup').style.display = method === 'bank_balance' ? 'block' : 'none';
    document.getElementById('externalAccountGroup').style.display = method === 'external_account' ? 'block' : 'none';
    document.getElementById('paypalGroup').style.display = method === 'paypal' ? 'block' : 'none';
    document.getElementById('venmoGroup').style.display = method === 'venmo' ? 'block' : 'none';
    document.getElementById('cryptoWithdrawGroup').style.display = method.startsWith('crypto_') ? 'block' : 'none';
}

// Close modals on outside click
const fundModal = document.getElementById('fundModal');
if (fundModal) {
    fundModal.addEventListener('click', function(e) {
        if (e.target === this) closeFundModal();
    });
}

const withdrawModal = document.getElementById('withdrawModal');
if (withdrawModal) {
    withdrawModal.addEventListener('click', function(e) {
        if (e.target === this) closeWithdrawModal();
    });
}
</script>

<?php include __DIR__ . '/../../includes/mobile-nav.php'; ?>

