<?php 
$pageTitle = 'My Investments - ' . getSiteName();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../models/UserInvestment.php';

requireLogin();

// Get user currency
$userModel = new User();
$user = $userModel->findById($_SESSION['user_id']);
$userCurrency = getUserDisplayCurrency($user);

// Include head and sidebar
include __DIR__ . '/../../includes/head.php';
include __DIR__ . '/../../includes/sidebar.php';

if (!isset($investments)) {
    $investments = [];
}
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

.investments-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.investment-item {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s;
}

.investment-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
}

.investment-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 15px;
}

.investment-title {
    font-size: 20px;
    font-weight: 700;
    color: #032B44;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: capitalize;
}

.status-active {
    background: #d1fae5;
    color: #065f46;
}

.status-matured {
    background: #dbeafe;
    color: #1e40af;
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
}

.status-closed {
    background: #e5e7eb;
    color: #374151;
}

.investment-details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}

.detail-box {
    padding: 12px;
    background: #f9fafb;
    border-radius: 8px;
}

.detail-label {
    font-size: 12px;
    color: #6b7280;
    margin-bottom: 4px;
}

.detail-value {
    font-size: 16px;
    font-weight: 600;
    color: #032B44;
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

@media (max-width: 768px) {
    .investment-header {
        flex-direction: column;
    }
    
    .investment-details-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px;">
    <div>
        <h1>My Investments</h1>
        <p>View and manage your active investments</p>
    </div>
    <div>
        <a href="<?php echo SITE_URL; ?>/investment/transactions" class="btn-invest" style="width: auto; white-space: nowrap; display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: linear-gradient(135deg, #032B44 0%, #024a6b 100%); color: white; border-radius: 12px; text-decoration: none; font-weight: 600; transition: all 0.3s; box-shadow: 0 4px 12px rgba(3, 43, 68, 0.3);">
            <i class="fas fa-history"></i> Transaction History
        </a>
    </div>
</div>

<?php if (empty($investments)): ?>
<div class="empty-state">
    <i class="fas fa-chart-line"></i>
    <h3>No Investments Yet</h3>
    <p>You haven't made any investments. <a href="<?php echo SITE_URL; ?>/investment" style="color: #032B44; font-weight: 600;">Browse Available Investments →</a></p>
</div>
<?php else: ?>
<div class="investments-list">
    <?php foreach ($investments as $investment): 
        $currentValue = $investment['current_value'] ?? 0;
        $principal = (float)$investment['amount_principal'];
        $accrued = (float)($investment['current_accrued'] ?? 0);
        $profit = $accrued;
    ?>
    <div class="investment-item">
        <div class="investment-header">
            <div style="flex: 1;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                    <div class="investment-title"><?php echo htmlspecialchars($investment['product_title'] ?? 'Investment Product'); ?></div>
                    <span class="status-badge status-<?php echo $investment['status']; ?>" style="margin-left: auto;">
                        <?php echo ucfirst(str_replace('_', ' ', $investment['status'])); ?>
                    </span>
                </div>
                <div style="display: flex; gap: 16px; flex-wrap: wrap; font-size: 14px; color: #6b7280;">
                    <span>
                        <i class="fas fa-tag"></i> <?php echo ucfirst($investment['product_type'] ?? 'Unknown'); ?>
                    </span>
                    <span>
                        <i class="fas fa-hashtag"></i> Investment #<?php echo $investment['id']; ?>
                    </span>
                    <?php if (!empty($investment['product_slug'])): ?>
                    <span>
                        <i class="fas fa-link"></i> <?php echo htmlspecialchars($investment['product_slug']); ?>
                    </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="investment-details-grid" style="margin-top: 20px;">
            <div class="detail-box" style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);">
                <div class="detail-label">Amount Invested</div>
                <div class="detail-value" style="font-size: 20px; color: #032B44;"><?php echo formatInvestmentAmountForUser($principal, $user); ?></div>
            </div>
            
            <div class="detail-box" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);">
                <div class="detail-label">ROI Earned</div>
                <div class="detail-value" style="font-size: 20px; color: #10b981;"><?php echo formatInvestmentAmountForUser($accrued, $user); ?></div>
            </div>
            
            <div class="detail-box" style="background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);">
                <div class="detail-label">Current Value</div>
                <div class="detail-value" style="font-size: 20px; color: #7c3aed;"><?php echo formatInvestmentAmountForUser($currentValue, $user); ?></div>
            </div>
            
            <div class="detail-box">
                <div class="detail-label">Daily ROI Rate</div>
                <div class="detail-value"><?php echo number_format((float)($investment['daily_percent_effective'] ?? 0), 4); ?>%</div>
            </div>
            
            <div class="detail-box">
                <div class="detail-label">Investment Start Date</div>
                <div class="detail-value"><?php echo date('F j, Y', strtotime($investment['start_date'])); ?></div>
            </div>
            
            <div class="detail-box">
                <div class="detail-label">Maturity Date</div>
                <div class="detail-value"><?php echo date('F j, Y', strtotime($investment['maturity_date'])); ?></div>
            </div>
            
            <?php 
            $daysRemaining = (strtotime($investment['maturity_date']) - time()) / 86400;
            if ($daysRemaining > 0 && $investment['status'] === 'active'):
            ?>
            <div class="detail-box">
                <div class="detail-label">Days Remaining</div>
                <div class="detail-value" style="color: #f59e0b;"><?php echo ceil($daysRemaining); ?> days</div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($investment['payout_type'])): ?>
            <div class="detail-box">
                <div class="detail-label">Payout Type</div>
                <div class="detail-value"><?php echo ucfirst(str_replace('_', ' ', $investment['payout_type'])); ?></div>
            </div>
            <?php endif; ?>
        </div>
        
        <div style="margin-top: 20px; padding-top: 16px; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <a href="<?php echo SITE_URL; ?>/investment/view/<?php echo $investment['product_id']; ?>" 
               style="color: #032B44; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: #f9fafb; border-radius: 8px; transition: all 0.3s;"
               onmouseover="this.style.background='#e5e7eb'" 
               onmouseout="this.style.background='#f9fafb'">
                <i class="fas fa-info-circle"></i> View Product Details
            </a>
            <?php if ($investment['status'] === 'active'): ?>
            <div style="display: flex; align-items: center; gap: 8px; color: #10b981; font-size: 14px;">
                <i class="fas fa-circle" style="font-size: 8px;"></i> Active Investment
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../../includes/mobile-nav.php'; ?>

