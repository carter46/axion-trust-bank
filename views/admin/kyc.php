<?php 
$pageTitle = 'KYC Approvals - Admin - ' . getSiteName();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../models/Kyc.php';

// Include head
include __DIR__ . '/../../includes/head.php';

// Include admin sidebar
include __DIR__ . '/../../includes/admin-sidebar.php';

// Get KYC verifications
$kycModel = new Kyc();
$status = $_GET['status'] ?? 'pending';
$kycList = $kycModel->getAll(['status' => $status]);
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

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    color: white;
    padding: 24px;
    border-radius: 16px;
    text-align: center;
}

.stat-card h3 {
    font-size: 36px;
    margin: 0;
    font-weight: 700;
}

.stat-card p {
    margin: 8px 0 0 0;
    opacity: 0.9;
}

.tab-navigation {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    border-bottom: 2px solid #e0e0e0;
}

.tab-button {
    padding: 12px 24px;
    background: transparent;
    border: none;
    font-size: 16px;
    cursor: pointer;
    color: #666;
    border-bottom: 3px solid transparent;
    transition: all 0.3s;
}

.tab-button.active {
    color: #1e3a8a;
    border-bottom-color: #1e3a8a;
    font-weight: 600;
}

.card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 24px;
    padding: 30px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.kyc-item {
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 16px;
    transition: all 0.3s;
}

.kyc-item:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.status-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
}

.status-verified {
    background: #d1fae5;
    color: #065f46;
}

.status-rejected {
    background: #fee2e2;
    color: #991b1b;
}

.view-btn {
    background: #1e3a8a;
    color: white;
    padding: 8px 16px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 14px;
    transition: all 0.3s;
}

.view-btn:hover {
    background: #3b82f6;
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .page-header h1 {
        font-size: 24px;
    }
    
    .card {
        padding: 20px;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
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
    margin-bottom: 20px;
}

.back-button:hover {
    background: #e5e7eb;
    transform: translateX(-4px);
}
</style>

<a href="<?php echo SITE_URL; ?>/admin" class="back-button">
    <i class="fas fa-arrow-left"></i> Back to Admin
</a>

<div class="page-header">
    <h1>KYC Verification Management</h1>
    <p style="color: #666;">Review and verify user KYC submissions</p>
</div>

<div class="stats-grid">
    <?php
    $pending = count($kycModel->getAll(['status' => 'pending']));
    $verified = count($kycModel->getAll(['status' => 'verified']));
    $rejected = count($kycModel->getAll(['status' => 'rejected']));
    ?>
    <div class="stat-card">
        <h3><?php echo $pending; ?></h3>
        <p>Pending</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $verified; ?></h3>
        <p>Verified</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $rejected; ?></h3>
        <p>Rejected</p>
    </div>
</div>

<div class="card">
    <div class="tab-navigation">
        <a href="?status=pending" class="tab-button <?php echo $status == 'pending' ? 'active' : ''; ?>">
            Pending
        </a>
        <a href="?status=verified" class="tab-button <?php echo $status == 'verified' ? 'active' : ''; ?>">
            Verified
        </a>
        <a href="?status=rejected" class="tab-button <?php echo $status == 'rejected' ? 'active' : ''; ?>">
            Rejected
        </a>
    </div>
    
    <?php if (empty($kycList)): ?>
        <div style="text-align: center; color: #666; padding: 40px;">
            No KYC submissions found
        </div>
    <?php else: ?>
        <div>
            <?php foreach ($kycList as $kyc): ?>
            <div class="kyc-item">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div>
                        <h4 style="margin: 0 0 8px 0; color: #032B44;">
                            <?php echo htmlspecialchars($kyc['full_name'] ?? 'Unknown User'); ?>
                        </h4>
                        <p style="margin: 4px 0; color: #666;">
                            <strong>Account Type:</strong> <?php echo ucfirst($kyc['account_type'] ?? 'individual'); ?>
                        </p>
                        <p style="margin: 4px 0; color: #666;">
                            <strong>Email:</strong> <?php echo htmlspecialchars($kyc['email'] ?? 'N/A'); ?>
                        </p>
                        <p style="margin: 4px 0; color: #666;">
                            <strong>Submitted:</strong> <?php echo date('M d, Y', strtotime($kyc['submitted_at'])); ?>
                        </p>
                        <p style="margin: 4px 0; color: #666;">
                            <strong>ID Type:</strong> <?php echo ucfirst(str_replace('_', ' ', $kyc['id_type'] ?? 'N/A')); ?>
                        </p>
                    </div>
                    <div style="text-align: right;">
                        <span class="status-badge status-<?php echo $kyc['status']; ?>">
                            <?php echo ucfirst($kyc['status']); ?>
                        </span>
                        <br><br>
                        <a href="<?php echo SITE_URL; ?>/admin/kyc-view/<?php echo $kyc['id']; ?>" class="view-btn">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
