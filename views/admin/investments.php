<?php 
$pageTitle = 'Investment Products - Admin - ' . getSiteName();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

// Include head and admin sidebar
include __DIR__ . '/../../includes/head.php';
include __DIR__ . '/../../includes/admin-sidebar.php';

if (empty($products)) {
    $products = [];
}
if (empty($stats)) {
    $stats = [];
}
?>

<style>
.page-header {
    margin-bottom: 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

.page-header h1 {
    font-size: 32px;
    font-weight: 700;
    color: #032B44;
}

.btn-create {
    padding: 12px 24px;
    background: linear-gradient(135deg, #032B44 0%, #024a6b 100%);
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-create:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(3, 43, 68, 0.3);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.stat-label {
    font-size: 14px;
    color: #666;
    margin-bottom: 8px;
}

.stat-value {
    font-size: 28px;
    font-weight: 700;
    color: #032B44;
}

.products-table {
    width: 100%;
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.products-table table {
    width: 100%;
    border-collapse: collapse;
}

.products-table th {
    background: #f9fafb;
    padding: 16px;
    text-align: left;
    font-weight: 600;
    color: #374151;
    border-bottom: 2px solid #e5e7eb;
}

.products-table td {
    padding: 16px;
    border-bottom: 1px solid #e5e7eb;
}

.products-table tr:hover {
    background: #f9fafb;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    text-transform: capitalize;
}

.status-active {
    background: #d1fae5;
    color: #065f46;
}

.status-draft {
    background: #f3f4f6;
    color: #374151;
}

.status-paused {
    background: #fef3c7;
    color: #92400e;
}

.status-closed {
    background: #fee2e2;
    color: #991b1b;
}

.btn-action {
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    margin-right: 8px;
    display: inline-block;
}

.btn-edit {
    background: #dbeafe;
    color: #1e40af;
}

.btn-investors {
    background: #f3e8ff;
    color: #6b21a8;
}

.btn-delete {
    background: #fee2e2;
    color: #991b1b;
}

/* Mobile Investment Cards */
.mobile-investment-cards {
    display: none;
}

.investment-card-mobile {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.investment-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.investment-info-mobile {
    flex: 1;
}

.investment-title-mobile {
    font-weight: 600;
    color: #1f2937;
    font-size: 16px;
    margin-bottom: 4px;
}

.investment-type-mobile {
    color: #6b7280;
    font-size: 14px;
}

.expand-btn {
    background: #f3f4f6;
    border: none;
    width: 36px;
    height: 36px;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #374151;
    font-size: 16px;
    transition: all 0.3s;
}

.expand-btn:hover {
    background: #e5e7eb;
}

.expand-btn.active {
    background: #3b82f6;
    color: white;
    transform: rotate(180deg);
}

.investment-details-mobile {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
}

.investment-details-mobile.expanded {
    max-height: 400px;
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid #e5e7eb;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    font-size: 14px;
}

.detail-label {
    color: #6b7280;
    font-weight: 500;
}

.detail-value {
    color: #1f2937;
    font-weight: 600;
}

.mobile-actions {
    display: flex;
    gap: 8px;
    margin-top: 12px;
}

.mobile-actions a {
    flex: 1;
    padding: 10px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: all 0.3s;
}

.btn-edit-mobile {
    background: #dbeafe;
    color: #1e40af;
}

.btn-investors-mobile {
    background: #f3e8ff;
    color: #6b21a8;
}

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .page-header h1 {
        font-size: 24px;
    }
    
    .products-table {
        display: none;
    }
    
    .mobile-investment-cards {
        display: block;
    }
}
</style>

<div class="page-header">
    <h1>Investment Products</h1>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="<?php echo SITE_URL; ?>/admin/investment-funding" class="btn-create" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <i class="fas fa-money-bill-wave"></i> Crypto Funding
        </a>
        <a href="<?php echo SITE_URL; ?>/admin/investment-create" class="btn-create">
            <i class="fas fa-plus"></i> Create Product
        </a>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
<div style="background: #d1fae5; border: 2px solid #10b981; border-radius: 12px; padding: 16px; margin-bottom: 24px; color: #065f46;">
    <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
</div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
<div style="background: #fee2e2; border: 2px solid #ef4444; border-radius: 12px; padding: 16px; margin-bottom: 24px; color: #991b1b;">
    <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
</div>
<?php endif; ?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Total Products</div>
        <div class="stat-value"><?php echo count($products); ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Investors</div>
        <div class="stat-value"><?php echo number_format($stats['total_investors'] ?? 0); ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Invested</div>
        <div class="stat-value"><?php echo formatCurrency($stats['total_invested'] ?? 0); ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total ROI Accrued</div>
        <div class="stat-value"><?php echo formatCurrency($stats['total_accrued'] ?? 0); ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total ROI Paid</div>
        <div class="stat-value"><?php echo formatCurrency($stats['total_paid'] ?? 0); ?></div>
    </div>
</div>

<div class="products-table">
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Type</th>
                <th>Status</th>
                <th>Min Amount</th>
                <th>Duration</th>
                <th>ROI</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($products)): ?>
            <tr>
                <td colspan="7" style="text-align: center; padding: 40px; color: #6b7280;">
                    No investment products found. <a href="<?php echo SITE_URL; ?>/admin/investment-create">Create your first product</a>
                </td>
            </tr>
            <?php else: ?>
            <?php 
            $productModel = new InvestmentProduct();
            foreach ($products as $product): 
                // Calculate ROI
                $roiConfig = json_decode($product['roi_config'] ?? '{}', true);
                if (($roiConfig['mode'] ?? '') === 'annual') {
                    $annualROI = $roiConfig['annual_percent'] ?? 0;
                    $dailyROI = $annualROI / 365;
                } else {
                    $dailyROI = $roiConfig['daily_percent'] ?? 0;
                    $annualROI = $dailyROI * 365;
                }
            ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($product['title']); ?></strong></td>
                <td><?php echo ucfirst($product['type']); ?></td>
                <td>
                    <span class="status-badge status-<?php echo $product['status']; ?>">
                        <?php echo ucfirst($product['status']); ?>
                    </span>
                </td>
                <td><?php echo formatCurrency($product['min_amount']); ?></td>
                <td><?php echo $product['min_duration_days']; ?>-<?php echo $product['max_duration_days'] ?? '∞'; ?> days</td>
                <td>
                    <?php if ($annualROI > 0): ?>
                        <?php echo number_format($annualROI, 2); ?>% annual
                    <?php else: ?>
                        <?php echo number_format($dailyROI, 4); ?>% daily
                    <?php endif; ?>
                </td>
                <td>
                    <a href="<?php echo SITE_URL; ?>/admin/investment-edit/<?php echo $product['id']; ?>" class="btn-action btn-edit">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="<?php echo SITE_URL; ?>/admin/investment-investors/<?php echo $product['id']; ?>" class="btn-action btn-investors">
                        <i class="fas fa-users"></i> Investors
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Mobile View -->
<div class="mobile-investment-cards">
    <?php if (empty($products)): ?>
        <div style="text-align: center; color: #666; padding: 40px;">
            No investment products found. <a href="<?php echo SITE_URL; ?>/admin/investment-create">Create your first product</a>
        </div>
    <?php else: ?>
        <?php 
        $productModel = new InvestmentProduct();
        foreach ($products as $product): 
            // Calculate ROI
            $roiConfig = json_decode($product['roi_config'] ?? '{}', true);
            if (($roiConfig['mode'] ?? '') === 'annual') {
                $annualROI = $roiConfig['annual_percent'] ?? 0;
                $dailyROI = $annualROI / 365;
            } else {
                $dailyROI = $roiConfig['daily_percent'] ?? 0;
                $annualROI = $dailyROI * 365;
            }
        ?>
        <div class="investment-card-mobile">
            <div class="investment-card-header">
                <div class="investment-info-mobile">
                    <div class="investment-title-mobile"><?php echo htmlspecialchars($product['title']); ?></div>
                    <div class="investment-type-mobile"><?php echo ucfirst($product['type']); ?></div>
                </div>
                <button class="expand-btn" onclick="toggleInvestmentDetails(this)">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
            <div class="investment-details-mobile">
                <div class="detail-row">
                    <span class="detail-label">Status</span>
                    <span class="status-badge status-<?php echo $product['status']; ?>">
                        <?php echo ucfirst($product['status']); ?>
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Min Amount</span>
                    <span class="detail-value"><?php echo formatCurrency($product['min_amount']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Duration</span>
                    <span class="detail-value"><?php echo $product['min_duration_days']; ?>-<?php echo $product['max_duration_days'] ?? '∞'; ?> days</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">ROI</span>
                    <span class="detail-value">
                        <?php if ($annualROI > 0): ?>
                            <?php echo number_format($annualROI, 2); ?>% annual
                        <?php else: ?>
                            <?php echo number_format($dailyROI, 4); ?>% daily
                        <?php endif; ?>
                    </span>
                </div>
                <div class="mobile-actions">
                    <a href="<?php echo SITE_URL; ?>/admin/investment-edit/<?php echo $product['id']; ?>" class="btn-edit-mobile">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="<?php echo SITE_URL; ?>/admin/investment-investors/<?php echo $product['id']; ?>" class="btn-investors-mobile">
                        <i class="fas fa-users"></i> Investors
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<form method="POST" action="<?php echo SITE_URL; ?>/admin/runAccrual" style="margin-top: 30px; padding: 20px; background: #f9fafb; border-radius: 12px;">
    <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
    <h3 style="margin-bottom: 16px;">Manual Accrual Run</h3>
    <p style="color: #6b7280; margin-bottom: 16px;">Run ROI accrual for a specific date (defaults to today)</p>
    <div style="display: flex; gap: 12px; align-items: center;">
        <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>" style="padding: 8px 12px; border: 2px solid #e5e7eb; border-radius: 6px;">
        <button type="submit" style="padding: 8px 20px; background: #032B44; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
            Run Accrual
        </button>
    </div>
</form>

<script>
function toggleInvestmentDetails(button) {
    const card = button.closest('.investment-card-mobile');
    const details = card.querySelector('.investment-details-mobile');
    const isExpanded = details.classList.contains('expanded');
    
    if (isExpanded) {
        details.classList.remove('expanded');
        button.classList.remove('active');
    } else {
        details.classList.add('expanded');
        button.classList.add('active');
    }
}
</script>

