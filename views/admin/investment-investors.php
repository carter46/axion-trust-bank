<?php 
$pageTitle = 'Investors - ' . htmlspecialchars($product['title'] ?? 'Product') . ' - Admin - ' . getSiteName();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../models/UserInvestment.php';

// Include head and admin sidebar
include __DIR__ . '/../../includes/head.php';
include __DIR__ . '/../../includes/admin-sidebar.php';

if (!$product) {
    redirect('/admin/investments');
    exit;
}

if (empty($investments)) {
    $investments = [];
}

$userInvestmentModel = new UserInvestment();
?>

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

.investors-table {
    width: 100%;
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.investors-table table {
    width: 100%;
    border-collapse: collapse;
}

.investors-table th {
    background: #f9fafb;
    padding: 16px;
    text-align: left;
    font-weight: 600;
    color: #374151;
    border-bottom: 2px solid #e5e7eb;
}

.investors-table td {
    padding: 16px;
    border-bottom: 1px solid #e5e7eb;
}

.investors-table tr:hover {
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

.status-matured {
    background: #dbeafe;
    color: #1e40af;
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
}
</style>

<div class="page-header">
    <a href="<?php echo SITE_URL; ?>/admin/investments" style="color: #032B44; text-decoration: none; margin-bottom: 12px; display: inline-block;">
        <i class="fas fa-arrow-left"></i> Back to Investments
    </a>
    <h1>Investors: <?php echo htmlspecialchars($product['title']); ?></h1>
    <p style="color: #666;">View all investors for this product</p>
</div>

<div class="investors-table">
    <table>
        <thead>
            <tr>
                <th>User</th>
                <th>Amount</th>
                <th>Duration</th>
                <th>Start Date</th>
                <th>Maturity Date</th>
                <th>ROI Earned</th>
                <th>Current Value</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($investments)): ?>
            <tr>
                <td colspan="8" style="text-align: center; padding: 40px; color: #6b7280;">
                    No investors yet for this product.
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($investments as $inv): 
                $currentValue = $userInvestmentModel->getCurrentValue($inv);
            ?>
            <tr>
                <td>
                    <div><strong><?php echo htmlspecialchars($inv['full_name']); ?></strong></div>
                    <div style="font-size: 12px; color: #6b7280;"><?php echo htmlspecialchars($inv['email']); ?></div>
                    <div style="font-size: 12px; color: #6b7280;">Account: <?php echo htmlspecialchars($inv['account_number']); ?></div>
                </td>
                <td><?php echo formatCurrency($inv['amount_principal'], DEFAULT_CURRENCY); ?></td>
                <td><?php echo $inv['duration_days']; ?> days</td>
                <td><?php echo date('M j, Y', strtotime($inv['start_date'])); ?></td>
                <td><?php echo date('M j, Y', strtotime($inv['maturity_date'])); ?></td>
                <td style="color: #10b981; font-weight: 600;"><?php echo formatCurrency($inv['current_accrued'] ?? 0, DEFAULT_CURRENCY); ?></td>
                <td style="font-weight: 600;"><?php echo formatCurrency($currentValue, DEFAULT_CURRENCY); ?></td>
                <td>
                    <span class="status-badge status-<?php echo $inv['status']; ?>">
                        <?php echo ucfirst(str_replace('_', ' ', $inv['status'])); ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>


