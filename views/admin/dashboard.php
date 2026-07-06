<?php 
$pageTitle = 'Admin Dashboard - SecureBank';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

// Include head
include __DIR__ . '/../../includes/head.php';

// Include admin sidebar
include __DIR__ . '/../../includes/admin-sidebar.php';
?>

<!-- ===== ADMIN CMS DASHBOARD PAGE CONTENT ===== -->

<style>
/* Admin Dashboard Specific Styles */
.content-area {
    background: #f5f7fa;
    min-height: 100vh;
    padding: 20px;
}

.admin-container {
    max-width: 1400px;
    margin: 0 auto;
}

.admin-header {
    background: white;
    border-radius: 16px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.admin-title {
    font-size: 32px;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 8px;
}

.admin-subtitle {
    color: #6c757d;
    font-size: 16px;
}

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

.stat-change.negative {
    color: #ef4444;
}

/* Quick Actions */
.quick-actions {
    background: white;
    border-radius: 16px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.action-btn {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
    background: #f8f9fa;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    color: #2d3748;
    min-width: 0; /* Prevents flex items from overflowing */
    width: 100%;
    box-sizing: border-box;
}

.action-btn > .action-icon {
    flex: 0 0 40px !important;
    width: 40px !important;
    height: 40px !important;
}

.action-btn:hover {
    background: #e9ecef;
    transform: translateY(-2px);
}

.action-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    color: white;
    flex: 0 0 40px !important;
    flex-shrink: 0 !important;
    flex-grow: 0 !important;
    min-width: 40px !important;
    max-width: 40px !important;
    min-height: 40px !important;
    max-height: 40px !important;
    box-sizing: border-box;
    position: relative;
    aspect-ratio: 1 / 1 !important;
    overflow: hidden;
}

.action-icon i {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 1em;
    height: 1em;
    font-size: 16px;
    line-height: 1;
    flex-shrink: 0;
    max-width: 16px;
    max-height: 16px;
}

.action-text {
    flex: 1;
}

.action-title {
    font-weight: 600;
    margin-bottom: 4px;
}

.action-desc {
    font-size: 13px;
    color: #6c757d;
}

/* Recent Activity */
.recent-activity {
    background: white;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.activity-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    border-radius: 12px;
    transition: all 0.3s ease;
    min-width: 0; /* Prevents flex items from overflowing */
    width: 100%;
    box-sizing: border-box;
}

.activity-item:hover {
    background: #f8f9fa;
}

.activity-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    color: white;
    font-size: 14px;
}

.activity-details {
    flex: 1;
    min-width: 0; /* Allows text to wrap */
    overflow: hidden; /* Prevents overflow */
}

.activity-title {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 4px;
    word-wrap: break-word;
    overflow-wrap: break-word;
    hyphens: auto;
}

.activity-time {
    font-size: 13px;
    color: #6c757d;
}

.activity-status {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
    flex-shrink: 0; /* Prevents status from shrinking */
    white-space: nowrap; /* Prevents text wrapping in status */
}

.status-success {
    background: #dcfce7;
    color: #16a34a;
}

.status-warning {
    background: #fef3c7;
    color: #d97706;
}

.status-danger {
    background: #fecaca;
    color: #dc2626;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .content-area {
        padding: 10px;
    }
    
    .admin-header {
        padding: 20px;
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
    .card-icon.icon-users,
    .card-icon.icon-money,
    .card-icon.icon-transactions,
    .card-icon.icon-security,
    .card-icon.icon-reports {
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
    
    /* Quick Actions Tablet Fixes */
    .quick-actions {
        padding: 25px;
    }
    
    .action-icon {
        width: 40px !important;
        height: 40px !important;
        min-width: 40px !important;
        max-width: 40px !important;
        min-height: 40px !important;
        max-height: 40px !important;
        flex-shrink: 0 !important;
        flex-grow: 0 !important;
        aspect-ratio: 1 / 1 !important;
    }
    
    .action-icon i {
        font-size: 16px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 100% !important;
        height: 100% !important;
        line-height: 1 !important;
    }
    
    /* Recent Activity Mobile Fixes */
    .recent-activity {
        padding: 20px;
        overflow: hidden;
        width: 100%;
        box-sizing: border-box;
    }
    
    .activity-item {
        padding: 12px;
        gap: 10px;
        flex-wrap: nowrap;
        align-items: center;
        min-width: 0;
        width: 100%;
    }
    
    .activity-avatar {
        width: 35px;
        height: 35px;
        font-size: 12px;
        flex-shrink: 0;
    }
    
    .activity-details {
        flex: 1;
        min-width: 0;
        max-width: calc(100% - 120px);
        overflow: hidden;
    }
    
    .activity-title {
        font-size: 14px;
        line-height: 1.4;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }
    
    .activity-time {
        font-size: 12px;
    }
    
    .activity-status {
        padding: 4px 8px;
        font-size: 11px;
        flex-shrink: 0;
        white-space: nowrap;
    }
}

@media (max-width: 480px) {
    .content-area {
        padding: 5px;
    }
    
    .admin-title {
        font-size: 24px;
    }
    
    .admin-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .actions-grid {
        grid-template-columns: 1fr;
    }
    
    .admin-card {
        padding: 20px;
    }
    
    /* Quick Actions Mobile Fixes */
    .quick-actions {
        padding: 20px;
    }
    
    .action-btn {
        padding: 15px;
        gap: 12px;
    }
    
    .action-icon {
        width: 40px !important;
        height: 40px !important;
        min-width: 40px !important;
        max-width: 40px !important;
        min-height: 40px !important;
        max-height: 40px !important;
        flex-shrink: 0 !important;
        flex-grow: 0 !important;
        aspect-ratio: 1 / 1 !important;
    }
    
    .action-icon i {
        font-size: 16px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 100% !important;
        height: 100% !important;
        line-height: 1 !important;
    }
    
    /* Recent Activity Extra Small Mobile */
    .recent-activity {
        padding: 15px;
    }
    
    .activity-item {
        padding: 10px;
        gap: 8px;
    }
    
    .activity-avatar {
        width: 30px;
        height: 30px;
        font-size: 11px;
    }
    
    .activity-details {
        max-width: calc(100% - 100px);
    }
    
    .activity-title {
        font-size: 13px;
    }
    
    .activity-status {
        padding: 3px 6px;
        font-size: 10px;
    }
}
</style>

<div class="admin-container">
    <!-- Admin Header -->
    <div class="admin-header">
        <h1 class="admin-title">Bank Management Dashboard</h1>
        <p class="admin-subtitle">Monitor and control all banking operations</p>
    </div>

    <!-- Statistics Grid -->
    <div class="admin-grid">
        <div class="admin-card">
            <div class="card-header">
                <h3 class="card-title">Total Users</h3>
                <div class="card-icon icon-users">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="stat-number"><?php echo number_format($stats['users']['total'] ?? 0); ?></div>
            <div class="stat-label">Active banking customers</div>
            <div class="stat-change positive">
                <i class="fas fa-user-check"></i>
                <span><?php echo number_format($stats['users']['active'] ?? 0); ?> active</span>
            </div>
        </div>

        <div class="admin-card">
            <div class="card-header">
                <h3 class="card-title">Total Assets</h3>
                <div class="card-icon icon-money">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
            <div class="stat-number"><?php echo formatCurrency($stats['accounts']['total_balance'] ?? 0, DEFAULT_CURRENCY); ?></div>
            <div class="stat-label">Under management</div>
            <div class="stat-change positive">
                <i class="fas fa-wallet"></i>
                <span><?php echo number_format($stats['accounts']['total'] ?? 0); ?> accounts</span>
            </div>
        </div>

        <div class="admin-card">
            <div class="card-header">
                <h3 class="card-title">Transactions</h3>
                <div class="card-icon icon-transactions">
                    <i class="fas fa-exchange-alt"></i>
                </div>
            </div>
            <div class="stat-number"><?php echo number_format($stats['transactions_today']['count'] ?? 0); ?></div>
            <div class="stat-label">Processed today</div>
            <div class="stat-change positive">
                <i class="fas fa-money-bill-wave"></i>
                <span><?php echo formatCurrency($stats['transactions_today']['total'] ?? 0, DEFAULT_CURRENCY); ?> total</span>
            </div>
        </div>

        <div class="admin-card">
            <div class="card-header">
                <h3 class="card-title">Security Alerts</h3>
                <div class="card-icon icon-security">
                    <i class="fas fa-shield-alt"></i>
                </div>
            </div>
            <div class="stat-number"><?php echo count($alerts ?? []); ?></div>
            <div class="stat-label">Active alerts</div>
            <div class="stat-change <?php echo count($alerts ?? []) > 0 ? 'negative' : 'positive'; ?>">
                <i class="fas fa-<?php echo count($alerts ?? []) > 0 ? 'exclamation-triangle' : 'check-circle'; ?>"></i>
                <span><?php echo count($alerts ?? []) > 0 ? 'Requires attention' : 'All clear'; ?></span>
            </div>
        </div>

        <div class="admin-card">
            <div class="card-header">
                <h3 class="card-title">Cards</h3>
                <div class="card-icon icon-reports">
                    <i class="fas fa-credit-card"></i>
                </div>
            </div>
            <div class="stat-number"><?php echo number_format($stats['cards']['total'] ?? 0); ?></div>
            <div class="stat-label">Total cards issued</div>
            <div class="stat-change positive">
                <i class="fas fa-credit-card"></i>
                <span><?php echo number_format($stats['cards']['active'] ?? 0); ?> active</span>
            </div>
        </div>

        <div class="admin-card">
            <div class="card-header">
                <h3 class="card-title">Pending KYC</h3>
                <div class="card-icon icon-reports">
                    <i class="fas fa-file-alt"></i>
                </div>
            </div>
            <div class="stat-number"><?php echo number_format($stats['users']['pending_kyc'] ?? 0); ?></div>
            <div class="stat-label">Awaiting verification</div>
            <div class="stat-change <?php echo ($stats['users']['pending_kyc'] ?? 0) > 0 ? 'negative' : 'positive'; ?>">
                <i class="fas fa-<?php echo ($stats['users']['pending_kyc'] ?? 0) > 0 ? 'clock' : 'check'; ?>"></i>
                <span><?php echo ($stats['users']['pending_kyc'] ?? 0) > 0 ? 'Review required' : 'All verified'; ?></span>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <h3 style="color: #2d3748; margin-bottom: 25px; font-size: 20px; font-weight: 600;">Quick Actions</h3>
        <div class="actions-grid">
            <a href="<?php echo SITE_URL; ?>/admin/users" class="action-btn">
                <div class="action-icon icon-users">
                    <i class="fas fa-users"></i>
                </div>
                <div class="action-text">
                    <div class="action-title">Manage Users</div>
                    <div class="action-desc">View and edit customer accounts</div>
                </div>
            </a>

            <a href="<?php echo SITE_URL; ?>/admin/transactions" class="action-btn">
                <div class="action-icon icon-transactions">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <div class="action-text">
                    <div class="action-title">View Transactions</div>
                    <div class="action-desc">Monitor all banking transactions</div>
                </div>
            </a>

            <a href="<?php echo SITE_URL; ?>/admin/kyc" class="action-btn">
                <div class="action-icon icon-reports">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="action-text">
                    <div class="action-title">KYC Approvals</div>
                    <div class="action-desc">Review verification documents</div>
                </div>
            </a>

            <a href="<?php echo SITE_URL; ?>/admin/admin-settings" class="action-btn">
                <div class="action-icon icon-security">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="action-text">
                    <div class="action-title">Admin Settings</div>
                    <div class="action-desc">Manage admin accounts</div>
                </div>
            </a>

            <a href="<?php echo SITE_URL; ?>/admin/email" class="action-btn">
                <div class="action-icon icon-money">
                    <i class="fas fa-envelope-open-text"></i>
                </div>
                <div class="action-text">
                    <div class="action-title">Email Testing</div>
                    <div class="action-desc">Test SMTP configuration</div>
                </div>
            </a>

            <a href="<?php echo SITE_URL; ?>/admin/system-settings" class="action-btn">
                <div class="action-icon icon-settings">
                    <i class="fas fa-cog"></i>
                </div>
                <div class="action-text">
                    <div class="action-title">System Settings</div>
                    <div class="action-desc">Configure bank settings</div>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="recent-activity">
        <h3 style="color: #2d3748; margin-bottom: 25px; font-size: 20px; font-weight: 600;">Recent Activity</h3>
        <div class="activity-list">
            <?php if (!empty($audit_logs)): ?>
                <?php foreach ($audit_logs as $log): ?>
                    <?php
                    // Get initials for avatar
                    $nameParts = explode(' ', $log['admin_name'] ?? 'User');
                    $initials = '';
                    if (count($nameParts) >= 2) {
                        $initials = strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1));
                    } else {
                        $initials = strtoupper(substr($nameParts[0], 0, 2));
                    }
                    
                    // Color based on action
                    $colors = [
                        'create' => '#10b981',
                        'update' => '#3b82f6',
                        'delete' => '#ef4444',
                        'login' => '#4f46e5',
                        'transfer' => '#f59e0b'
                    ];
                    $bgColor = $colors[$log['action'] ?? 'update'] ?? '#6b7280';
                    
                    // Time ago
                    $timestamp = strtotime($log['created_at']);
                    $timeAgo = timeAgo($timestamp);
                    ?>
                    <div class="activity-item">
                        <div class="activity-avatar" style="background: <?php echo $bgColor; ?>;"><?php echo $initials; ?></div>
                        <div class="activity-details">
                            <div class="activity-title"><?php echo htmlspecialchars($log['description'] ?? 'No description'); ?></div>
                            <div class="activity-time"><?php echo $timeAgo; ?></div>
                        </div>
                        <div class="activity-status status-success"><?php echo ucfirst($log['action']); ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="activity-item" style="justify-content: center; text-align: center;">
                    <div class="activity-details">
                        <div class="activity-title" style="color: #9ca3af;">No recent activity</div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
