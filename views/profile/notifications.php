<?php 
$pageTitle = 'Notifications - ' . getSiteName();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

requireLogin();

// Get user notifications
$notificationModel = new Notification();
$notifications = $notificationModel->getUserNotifications($_SESSION['user_id'], 50);

// Ensure notifications is an array
if (!is_array($notifications)) {
    $notifications = [];
}

// Include head
include __DIR__ . '/../../includes/head.php';
include __DIR__ . '/../../includes/sidebar.php';
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

.page-header p {
    color: #666;
    font-size: 16px;
}

.page-actions {
    display: flex;
    gap: 12px;
    margin-bottom: 24px;
}

.notifications-container {
    background: white;
    border-radius: 16px;
    padding: 32px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-icon {
    font-size: 64px;
    color: #d1d5db;
    margin-bottom: 20px;
}

.empty-state h3 {
    margin: 0 0 8px 0;
    font-size: 20px;
    font-weight: 600;
    color: #374151;
}

.empty-state p {
    margin: 0;
    color: #6b7280;
}

.notification-item {
    padding: 20px;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    align-items: flex-start;
    gap: 16px;
    transition: all 0.3s;
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-item.unread {
    background: #f8f9fa;
    border-left: 4px solid #1e3a8a;
}

.notification-item:hover {
    background: #f8f9fa;
}

.notification-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
    flex-shrink: 0;
}

.notification-icon.credit,
.notification-icon.success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.notification-icon.debit,
.notification-icon.danger {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}

.notification-icon.security,
.notification-icon.warning {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.notification-icon.kyc,
.notification-icon.info {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}

.notification-icon.loan {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
}

.notification-icon.card {
    background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
}

.notification-content {
    flex: 1;
}

.notification-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 8px;
}

.notification-title {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: #032B44;
}

.notification-time {
    font-size: 14px;
    color: #9ca3af;
    white-space: nowrap;
}

.notification-message {
    margin: 0 0 12px 0;
    color: #4b5563;
    line-height: 1.5;
    font-size: 14px;
}

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-block;
}

.btn-outline {
    background: white;
    color: #1e3a8a;
    border: 2px solid #1e3a8a;
}

.btn-outline:hover {
    background: #1e3a8a;
    color: white;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 12px;
}

@media (max-width: 768px) {
    .page-header h1 {
        font-size: 24px;
    }
    
    .notifications-container {
        padding: 20px;
    }
    
    .notification-item {
        padding: 16px;
    }
    
    .notification-header {
        flex-direction: column;
        gap: 4px;
    }
    
    .notification-icon {
        width: 40px;
        height: 40px;
        font-size: 18px;
    }
}
</style>

<div class="page-header">
    <h1>Notifications</h1>
    <p>Stay updated with your account activities</p>
</div>

<?php if (!empty($notifications)): ?>
    <div class="page-actions">
        <button class="btn btn-outline" onclick="markAllAsRead()">
            <i class="fas fa-check-double"></i>
            Mark All as Read
        </button>
    </div>
<?php endif; ?>

<div class="notifications-container">
    <?php if (empty($notifications)): ?>
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-bell-slash"></i>
            </div>
            <h3>No Notifications</h3>
            <p>You don't have any notifications yet.</p>
        </div>
    <?php else: ?>
        <?php foreach ($notifications as $notification): ?>
            <div class="notification-item <?php echo $notification['is_read'] ? 'read' : 'unread'; ?>">
                <div class="notification-icon <?php echo $notification['type']; ?>">
                    <?php
                    $icons = [
                        'credit' => '<i class="fas fa-arrow-up"></i>',
                        'debit' => '<i class="fas fa-arrow-down"></i>',
                        'security' => '<i class="fas fa-shield-alt"></i>',
                        'kyc' => '<i class="fas fa-id-card"></i>',
                        'loan' => '<i class="fas fa-hand-holding-usd"></i>',
                        'card' => '<i class="fas fa-credit-card"></i>',
                        'success' => '<i class="fas fa-check-circle"></i>',
                        'warning' => '<i class="fas fa-exclamation-triangle"></i>',
                        'info' => '<i class="fas fa-info-circle"></i>',
                        'general' => '<i class="fas fa-info-circle"></i>'
                    ];
                    echo $icons[$notification['type']] ?? $icons['general'];
                    ?>
                </div>
                <div class="notification-content">
                    <div class="notification-header">
                        <h4 class="notification-title"><?php echo htmlspecialchars($notification['title']); ?></h4>
                        <span class="notification-time"><?php echo timeAgo($notification['created_at']); ?></span>
                    </div>
                    <p class="notification-message"><?php echo htmlspecialchars($notification['message']); ?></p>
                    <?php if (!$notification['is_read']): ?>
                        <button class="btn btn-sm btn-outline" onclick="markAsRead(<?php echo $notification['id']; ?>)">
                            Mark as Read
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
function markAsRead(notificationId) {
    fetch('<?php echo SITE_URL; ?>/api/mark-notification-read.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ notification_id: notificationId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            showToast('Error marking notification as read', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred', 'error');
    });
}

function markAllAsRead() {
    fetch('<?php echo SITE_URL; ?>/api/mark-all-notifications-read.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('All notifications marked as read', 'success');
            location.reload();
        } else {
            showToast('Error marking notifications as read', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred', 'error');
    });
}
</script>

<?php
// Include mobile navigation and closing tags
include __DIR__ . '/../../includes/mobile-nav.php';
?>
