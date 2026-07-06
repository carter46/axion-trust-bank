<?php 
$pageTitle = 'Card Applications - SecureBank';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

// Include head
include __DIR__ . '/../../includes/head.php';

// Include sidebar
include __DIR__ . '/../../includes/sidebar.php';
?>

<!-- ===== CARD APPLICATIONS PAGE CONTENT ===== -->

<?php include __DIR__ . '/../../includes/restricted-banner.php'; ?>

<style>
    /* RESET: Remove all inherited constraints from parent layout */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', system-ui, sans-serif;
    }

    body {
        background: #f5f7fa;
        overflow-x: hidden !important;
    }

    /* Override parent content-area styles */
    .main-content-area .content-area {
        background: #f5f7fa !important;
        padding: 15px !important;
    }

    .cards-container {
        max-width: 100%;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 25px;
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
        align-items: center;
        margin-bottom: 10px;
        gap: 20px;
    }
    
    .header-left {
        flex: 1;
    }
    
    .header-right {
        flex-shrink: 0;
    }

    /* ===== DASHBOARD CARDS (Same as Dashboard) ===== */
    .dashboard-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 25px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }

    .card-header {
        padding: 20px 25px 15px 25px;
        border-bottom: 1px solid #f1f5f9;
        background: #fafbfc;
    }

    .card-title {
        font-size: 18px;
        font-weight: 600;
        color: #2d3748;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-body {
        padding: 25px;
    }

    /* ===== APPLICATION CARDS ===== */
    .application-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .application-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease;
        position: relative;
    }

    .application-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .application-card.pending {
        border-left: 4px solid #f59e0b;
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
    }

    .application-card.rejected {
        border-left: 4px solid #ef4444;
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    }

    .application-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
    }

    .application-title {
        font-size: 16px;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
    }

    .application-date {
        font-size: 13px;
        color: #6b7280;
        margin: 4px 0 0 0;
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-pending {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #f59e0b;
    }

    .status-rejected {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #ef4444;
    }

    .application-info {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-bottom: 20px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
    }

    .info-label {
        font-size: 12px;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }

    .info-value {
        font-size: 14px;
        font-weight: 600;
        color: #1f2937;
    }

    .application-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn {
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
    }

    .btn-primary {
        background: #3b82f6;
        color: white;
    }

    .btn-primary:hover {
        background: #2563eb;
        color: white;
        text-decoration: none;
    }

    .btn-outline-primary {
        background: transparent;
        color: #3b82f6;
        border: 1px solid #3b82f6;
    }

    .btn-outline-primary:hover {
        background: #3b82f6;
        color: white;
        text-decoration: none;
    }

    .btn-warning {
        background: #f59e0b;
        color: white;
    }

    .btn-warning:hover {
        background: #d97706;
        color: white;
        text-decoration: none;
    }

    .btn-outline-warning {
        background: transparent;
        color: #f59e0b;
        border: 1px solid #f59e0b;
    }

    .btn-outline-warning:hover {
        background: #f59e0b;
        color: white;
        text-decoration: none;
    }

    .btn-outline-secondary {
        background: transparent;
        color: #6b7280;
        border: 1px solid #d1d5db;
    }

    .btn-outline-secondary:hover {
        background: #6b7280;
        color: white;
        text-decoration: none;
    }

    /* ===== EMPTY STATE ===== */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-icon {
        font-size: 64px;
        color: #d1d5db;
        margin-bottom: 20px;
    }

    .empty-state h4 {
        color: #6b7280;
        margin-bottom: 10px;
        font-size: 18px;
    }

    .empty-state p {
        color: #9ca3af;
        margin-bottom: 30px;
        font-size: 14px;
    }

    /* ===== QUICK ACTIONS ===== */
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 20px;
    }

    .quick-action-btn {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        text-decoration: none;
        color: #374151;
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }

    .quick-action-btn:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
        text-decoration: none;
        color: #374151;
    }

    .quick-action-icon {
        font-size: 24px;
        color: #3b82f6;
    }

    .quick-action-text {
        font-weight: 500;
        font-size: 14px;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
        .main-content-area .content-area {
            padding: 10px !important;
        }
        
        .header h1 {
            font-size: 22px;
            padding-top: 15px;
            margin-bottom: 6px;
            text-align: left;
        }
        
        .header p {
            font-size: 14px;
            padding-bottom: 18px;
        }
        
        .application-grid {
            grid-template-columns: 1fr;
        }
        
        .application-info {
            grid-template-columns: 1fr;
        }
        
        .application-actions {
            flex-direction: column;
        }
        
        .btn {
            justify-content: center;
        }
        
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }
        
        .quick-actions {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="cards-container">
            <!-- Page Header -->
            <div class="header">
                <h1>Card Applications</h1>
                <p>Track your card application status and manage your applications</p>
            </div>

            <!-- Pending Applications -->
            <?php if (!empty($pendingCards)): ?>
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock" style="color: #f59e0b;"></i>
                        Pending Applications (<?php echo count($pendingCards); ?>)
                    </h3>
                </div>
                <div class="card-body">
                    <div class="application-grid">
                        <?php foreach ($pendingCards as $card): ?>
                        <div class="application-card pending">
                            <div class="application-header">
                                <div>
                                    <h5 class="application-title">Card Application #<?php echo $card['id']; ?></h5>
                                    <p class="application-date">Applied: <?php echo date('M j, Y', strtotime($card['created_at'])); ?></p>
                                </div>
                                <span class="status-badge status-pending">Pending</span>
                            </div>
                            
                            <div class="application-info">
                                <div class="info-item">
                                    <span class="info-label">Card Type</span>
                                    <span class="info-value"><?php echo ucfirst($card['card_type']); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Account</span>
                                    <span class="info-value"><?php echo $card['account_number']; ?></span>
                                </div>
                            </div>
                            
                            <div class="application-actions">
                                <a href="<?php echo SITE_URL; ?>/card/view/<?php echo $card['id']; ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                    View Details
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Rejected Applications -->
            <?php if (!empty($rejectedCards)): ?>
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-times-circle" style="color: #ef4444;"></i>
                        Rejected Applications (<?php echo count($rejectedCards); ?>)
                    </h3>
                </div>
                <div class="card-body">
                    <div class="application-grid">
                        <?php foreach ($rejectedCards as $card): ?>
                        <div class="application-card rejected">
                            <div class="application-header">
                                <div>
                                    <h5 class="application-title">Card Application #<?php echo $card['id']; ?></h5>
                                    <p class="application-date">Applied: <?php echo date('M j, Y', strtotime($card['created_at'])); ?></p>
                                </div>
                                <span class="status-badge status-rejected">Rejected</span>
                            </div>
                            
                            <div class="application-info">
                                <div class="info-item">
                                    <span class="info-label">Card Type</span>
                                    <span class="info-value"><?php echo ucfirst($card['card_type']); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Account</span>
                                    <span class="info-value"><?php echo $card['account_number']; ?></span>
                                </div>
                            </div>
                            
                            <div class="application-actions">
                                <a href="<?php echo SITE_URL; ?>/card/view/<?php echo $card['id']; ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                    View Details
                                </a>
                                <a href="<?php echo SITE_URL; ?>/card/create" class="btn btn-warning">
                                    <i class="fas fa-plus"></i>
                                    Apply Again
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- No Applications -->
            <?php if (empty($pendingCards) && empty($rejectedCards)): ?>
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title">Card Applications</h3>
                </div>
                <div class="card-body">
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <h4>No Applications Found</h4>
                        <p>You haven't applied for any cards yet.</p>
                        <a href="<?php echo SITE_URL; ?>/card/create" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Apply for New Card
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Quick Actions -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    <div class="quick-actions">
                        <a href="<?php echo SITE_URL; ?>/card" class="quick-action-btn">
                            <i class="fas fa-credit-card quick-action-icon"></i>
                            <span class="quick-action-text">My Active Cards</span>
                        </a>
                        <a href="<?php echo SITE_URL; ?>/card/create" class="quick-action-btn">
                            <i class="fas fa-plus quick-action-icon"></i>
                            <span class="quick-action-text">Apply for New Card</span>
                        </a>
                        <a href="<?php echo SITE_URL; ?>/dashboard" class="quick-action-btn">
                            <i class="fas fa-tachometer-alt quick-action-icon"></i>
                            <span class="quick-action-text">Dashboard</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

<?php
include __DIR__ . '/../../includes/mobile-nav.php';
?>