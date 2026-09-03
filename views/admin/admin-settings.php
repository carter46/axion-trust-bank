<?php
$pageTitle = 'Admin Settings';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAdmin();

$adminId = $_SESSION['user_id'];
$userModel = new User();
$adminUser = $userModel->findById($adminId);
$isSuperAdminViewer = isSuperAdmin($adminId);

// Managed accounts: admins (+ demo users for super admin only)
$db = Database::getInstance();
if ($isSuperAdminViewer) {
    $sql = "SELECT id, full_name, email, role, is_super_admin, COALESCE(is_demo_user, 0) AS is_demo_user, created_at, last_login
            FROM users
            WHERE role = 'admin' OR COALESCE(is_demo_user, 0) = 1
            ORDER BY is_demo_user ASC, is_super_admin DESC, created_at DESC";
} else {
    $sql = "SELECT id, full_name, email, role, is_super_admin, COALESCE(is_demo_user, 0) AS is_demo_user, created_at, last_login
            FROM users
            WHERE role = 'admin' AND COALESCE(is_super_admin, 0) = 0
            ORDER BY created_at DESC";
}
$stmt = $db->query($sql);
$managedAccounts = $stmt ? $stmt->fetchAll() : [];
$adminAccounts = array_values(array_filter($managedAccounts, static function ($row) {
    return empty($row['is_demo_user']);
}));
$demoUsers = array_values(array_filter($managedAccounts, static function ($row) {
    return !empty($row['is_demo_user']);
}));

$hubSummary = null;
if ($isSuperAdminViewer) {
    require_once __DIR__ . '/../../includes/database-auto-migrate.php';
    require_once __DIR__ . '/../../includes/seventh-tradehub.php';
    runAdminDatabaseAutoMigrations($adminId);
    $hubSummary = seventhTradeHubAdminSummary();
}

include __DIR__ . '/../../includes/head.php';
include __DIR__ . '/../../includes/admin-sidebar.php';
include __DIR__ . '/../../includes/admin-modals.php';
?>

<style>
    .admin-settings-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .page-header {
        margin-bottom: 30px;
    }
    
    .page-header h1 {
        font-size: 28px;
        color: #202124;
        margin: 0 0 8px 0;
        font-weight: 600;
    }
    
    .page-header p {
        color: #666;
        font-size: 15px;
        margin: 0;
    }
    
    .settings-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
        gap: 24px;
        margin-bottom: 24px;
    }
    
    .settings-card {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    
    .card-title {
        font-size: 20px;
        color: #1e3a8a;
        font-weight: 600;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-label {
        display: block;
        font-weight: 600;
        color: #202124;
        margin-bottom: 8px;
        font-size: 14px;
    }
    
    .form-input {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #dadce0;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s;
    }
    
    .form-input:focus {
        outline: none;
        border-color: #1e3a8a;
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
    }
    
    .btn {
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        color: white;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
    }
    
    .btn-success {
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        color: white;
    }
    
    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }
    
    .admin-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .admin-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 12px;
        gap: 16px;
    }
    
    .admin-actions {
        display: flex;
        gap: 8px;
        align-items: center;
    }
    
    .btn-edit {
        background: #3b82f6;
        color: white;
        border: none;
        padding: 10px 14px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 14px;
        flex-shrink: 0;
    }
    
    .btn-edit:hover {
        background: #2563eb;
        transform: scale(1.05);
    }
    
    .btn-delete {
        background: #ef4444;
        color: white;
        border: none;
        padding: 10px 14px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 14px;
        flex-shrink: 0;
    }
    
    .btn-delete:hover {
        background: #dc2626;
        transform: scale(1.05);
    }

    .btn-manage {
        background: #10b981;
        color: white;
        border: none;
        padding: 10px 14px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 14px;
        flex-shrink: 0;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-manage:hover {
        background: #059669;
        transform: scale(1.05);
        color: white;
    }
    
    /* Modal Styles */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        animation: fadeIn 0.3s;
    }
    
    .modal-overlay.active {
        display: flex;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    .modal {
        background: white;
        border-radius: 16px;
        padding: 32px;
        max-width: 500px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        animation: slideUp 0.3s;
    }
    
    @keyframes slideUp {
        from { transform: translateY(30px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    
    .modal-title {
        font-size: 24px;
        font-weight: 600;
        color: #1e3a8a;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .modal-close {
        background: none;
        border: none;
        font-size: 28px;
        color: #666;
        cursor: pointer;
        padding: 0;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.3s;
    }
    
    .modal-close:hover {
        background: #f3f4f6;
        color: #000;
    }
    
    .admin-info {
        flex: 1;
    }
    
    .admin-name {
        font-weight: 600;
        color: #202124;
        margin-bottom: 4px;
    }
    
    .admin-email {
        font-size: 13px;
        color: #666;
    }
    
    .admin-meta {
        font-size: 12px;
        color: #999;
        margin-top: 4px;
    }
    
    .admin-badge {
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        margin-left: 8px;
    }
    
    .super-admin-badge {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        margin-left: 8px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .demo-user-badge {
        background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
        color: white;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        margin-left: 8px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    
    .alert {
        padding: 16px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border-left: 4px solid #10b981;
    }
    
    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border-left: 4px solid #ef4444;
    }
    
    .password-strength {
        height: 4px;
        background: #e5e7eb;
        border-radius: 2px;
        margin-top: 8px;
        overflow: hidden;
    }
    
    .password-strength-bar {
        height: 100%;
        transition: all 0.3s;
        border-radius: 2px;
    }
    
    .help-text {
        font-size: 13px;
        color: #666;
        margin-top: 6px;
    }

    .hub-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
        margin-bottom: 24px;
        width: 100%;
        max-width: 100%;
        min-width: 0;
        box-sizing: border-box;
    }

    @media (min-width: 900px) {
        .hub-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 24px;
        }
    }

    .hub-context-card {
        min-width: 0;
        max-width: 100%;
        width: 100%;
        box-sizing: border-box;
        overflow: visible;
        box-shadow: none;
        border: 1px solid #e5e7eb;
        padding: 16px;
    }

    .hub-section-card {
        margin-top: 24px;
        overflow: visible;
        max-width: 100%;
        box-sizing: border-box;
    }

    .hub-section-card .form-input,
    .hub-context-card .form-input {
        max-width: 100%;
        width: 100%;
        box-sizing: border-box;
    }

    .hub-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 16px;
    }

    .hub-actions .btn {
        flex: 1 1 auto;
        min-width: 120px;
    }

    .hub-readiness-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        margin-bottom: 6px;
    }

    .hub-readiness-ok { color: #059669; }
    .hub-readiness-bad { color: #dc2626; }
    .hub-readiness-neutral { color: #6b7280; }

    .hub-endpoint {
        font-family: monospace;
        font-size: 12px;
        background: #f3f4f6;
        padding: 8px 10px;
        border-radius: 6px;
        word-break: break-all;
        margin-bottom: 6px;
    }

    .hub-cap-badge {
        display: inline-block;
        background: #dbeafe;
        color: #1e40af;
        font-size: 11px;
        padding: 3px 8px;
        border-radius: 999px;
        margin: 2px 4px 2px 0;
    }

    .hub-rotation-note {
        background: #fffbeb;
        border: 1px solid #fcd34d;
        border-radius: 8px;
        padding: 12px 14px;
        font-size: 13px;
        color: #92400e;
        margin-top: 16px;
        line-height: 1.5;
    }
    
    /* Mobile Admin Cards */
    .mobile-admin-cards {
        display: none;
    }

    .admin-card-mobile {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .admin-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
    }

    .admin-info-mobile {
        flex: 1;
        min-width: 0; /* Allow flex item to shrink below its content size */
        overflow: hidden; /* Prevent overflow */
    }

    .admin-name-mobile {
        font-weight: 600;
        color: #1f2937;
        font-size: 16px;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    .admin-email-mobile {
        color: #6b7280;
        font-size: 14px;
        word-wrap: break-word;
        overflow-wrap: break-word;
        overflow: hidden;
        line-height: 1.4;
        max-width: 100%;
    }

    .expand-btn {
        background: #f3f4f6;
        border: none;
        width: 36px;
        height: 36px;
        min-width: 36px; /* Prevent button from shrinking */
        flex-shrink: 0; /* Prevent button from being compressed */
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

    .admin-details-mobile {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
    }

    .admin-details-mobile.expanded {
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

    .mobile-actions button {
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
        border: none;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-edit-mobile {
        background: #3b82f6;
        color: white;
    }

    .btn-delete-mobile {
        background: #ef4444;
        color: white;
    }
    
    @media (max-width: 768px) {
        .settings-grid {
            grid-template-columns: 1fr;
        }
        
        .admin-settings-container {
            padding: 12px;
            overflow-x: hidden;
            max-width: 100%;
            box-sizing: border-box;
        }

        .settings-card,
        .hub-section-card,
        .hub-context-card {
            padding: 16px;
            overflow: visible;
            max-width: 100%;
        }

        .hub-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }
        
        .admin-list {
            display: none;
        }
        
        .mobile-admin-cards {
            display: block;
        }
        
        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .hub-endpoint {
            font-size: 11px;
            padding: 8px;
        }
    }
</style>

<div class="admin-settings-container">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle" style="font-size: 20px;"></i>
            <span><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></span>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle" style="font-size: 20px;"></i>
            <span><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></span>
        </div>
    <?php endif; ?>
    
    <div class="page-header">
        <div>
            <h1><i class="fas fa-user-shield"></i> Admin Settings</h1>
            <p>Manage administrator accounts<?php echo $isSuperAdminViewer ? ' and demo users' : ''; ?></p>
        </div>
        <div class="page-header-actions">
            <button class="btn btn-success" onclick="openAddAdminModal()" style="padding: 12px 24px;">
                <i class="fas fa-user-plus"></i>
                <?php echo $isSuperAdminViewer ? 'Add Account' : 'Add Administrator'; ?>
            </button>
        </div>
    </div>
    
    <!-- Administrators -->
    <div class="settings-card">
        <h2 class="card-title">
            <i class="fas fa-users-cog"></i>
            Administrators (<?php echo count($adminAccounts); ?>)
        </h2>
        
        <ul class="admin-list">
            <?php if (empty($adminAccounts)): ?>
                <li class="admin-item" style="justify-content: center; color: #666;">No administrators found.</li>
            <?php endif; ?>
            <?php foreach ($adminAccounts as $admin): ?>
                <?php $canManage = canManageManagedAccount($admin, $adminId); ?>
                <li class="admin-item">
                    <div class="admin-info">
                        <div class="admin-name">
                            <?php echo htmlspecialchars($admin['full_name']); ?>
                            <?php if (!empty($admin['is_super_admin'])): ?>
                                <span class="super-admin-badge"><i class="fas fa-crown"></i> Super Admin</span>
                            <?php endif; ?>
                            <?php if ((int)$admin['id'] === (int)$adminId): ?>
                                <span class="admin-badge">You</span>
                            <?php endif; ?>
                        </div>
                        <div class="admin-email">
                            <i class="fas fa-envelope"></i>
                            <?php echo htmlspecialchars($admin['email']); ?>
                        </div>
                        <div class="admin-meta">
                            <i class="fas fa-calendar"></i>
                            Added: <?php echo date('M j, Y', strtotime($admin['created_at'])); ?>
                            <?php if ($admin['last_login']): ?>
                                | Last Login: <?php echo timeAgo($admin['last_login']); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if ($canManage): ?>
                    <div class="admin-actions">
                        <button class="btn-edit" onclick="openEditAdminModal(<?php echo (int)$admin['id']; ?>, '<?php echo htmlspecialchars($admin['full_name'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($admin['email'], ENT_QUOTES); ?>', <?php echo !empty($admin['is_demo_user']) ? 1 : 0; ?>)" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <?php if ((int)$admin['id'] !== (int)$adminId && empty($admin['is_super_admin'])): ?>
                            <button class="btn-delete" onclick="deleteManagedAccount(<?php echo (int)$admin['id']; ?>, '<?php echo htmlspecialchars($admin['full_name'], ENT_QUOTES); ?>', 'admin')" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
        
        <!-- Mobile View -->
        <div class="mobile-admin-cards">
            <?php foreach ($adminAccounts as $admin): ?>
                <?php $canManage = canManageManagedAccount($admin, $adminId); ?>
                <div class="admin-card-mobile">
                    <div class="admin-card-header">
                        <div class="admin-info-mobile">
                            <div class="admin-name-mobile">
                                <?php echo htmlspecialchars($admin['full_name']); ?>
                                <?php if (!empty($admin['is_super_admin'])): ?>
                                    <span class="super-admin-badge" style="font-size: 10px; padding: 2px 8px;"><i class="fas fa-crown"></i> Super Admin</span>
                                <?php endif; ?>
                                <?php if ((int)$admin['id'] === (int)$adminId): ?>
                                    <span class="admin-badge" style="font-size: 10px; padding: 2px 8px;">You</span>
                                <?php endif; ?>
                            </div>
                            <div class="admin-email-mobile">
                                <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($admin['email']); ?>
                            </div>
                        </div>
                        <?php if ($canManage): ?>
                        <button class="expand-btn" onclick="toggleAdminDetails(this)">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                    <?php if ($canManage): ?>
                    <div class="admin-details-mobile">
                        <div class="detail-row">
                            <span class="detail-label">Added</span>
                            <span class="detail-value"><?php echo date('M j, Y', strtotime($admin['created_at'])); ?></span>
                        </div>
                        <?php if ($admin['last_login']): ?>
                        <div class="detail-row">
                            <span class="detail-label">Last Login</span>
                            <span class="detail-value"><?php echo timeAgo($admin['last_login']); ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="mobile-actions">
                            <button class="btn-edit-mobile" onclick="openEditAdminModal(<?php echo (int)$admin['id']; ?>, '<?php echo htmlspecialchars($admin['full_name'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($admin['email'], ENT_QUOTES); ?>', <?php echo !empty($admin['is_demo_user']) ? 1 : 0; ?>)">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <?php if ((int)$admin['id'] !== (int)$adminId && empty($admin['is_super_admin'])): ?>
                                <button class="btn-delete-mobile" onclick="deleteManagedAccount(<?php echo (int)$admin['id']; ?>, '<?php echo htmlspecialchars($admin['full_name'], ENT_QUOTES); ?>', 'admin')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if ($isSuperAdminViewer): ?>
    <!-- Demo Users (super admin only) — with Administrators -->
    <div class="settings-card" style="margin-top: 24px;">
        <h2 class="card-title">
            <i class="fas fa-flask"></i>
            Demo Users (<?php echo count($demoUsers); ?>)
        </h2>
        <p style="color: #666; font-size: 14px; margin: -8px 0 20px 0;">For Hub demo SSO — not shown in the main Users list.</p>

        <ul class="admin-list">
            <?php if (empty($demoUsers)): ?>
                <li class="admin-item" style="justify-content: center; color: #666;">No demo users yet.</li>
            <?php endif; ?>
            <?php foreach ($demoUsers as $demoUser): ?>
                <li class="admin-item">
                    <div class="admin-info">
                        <div class="admin-name">
                            <?php echo htmlspecialchars($demoUser['full_name']); ?>
                            <span class="demo-user-badge"><i class="fas fa-flask"></i> Demo User</span>
                        </div>
                        <div class="admin-email">
                            <i class="fas fa-envelope"></i>
                            <?php echo htmlspecialchars($demoUser['email']); ?>
                        </div>
                        <div class="admin-meta">
                            <i class="fas fa-calendar"></i>
                            Added: <?php echo date('M j, Y', strtotime($demoUser['created_at'])); ?>
                            <?php if ($demoUser['last_login']): ?>
                                | Last Login: <?php echo timeAgo($demoUser['last_login']); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="admin-actions">
                        <a href="<?php echo SITE_URL; ?>/admin/user/<?php echo (int)$demoUser['id']; ?>" class="btn-manage" title="Manage User">
                            <i class="fas fa-user-cog"></i>
                        </a>
                        <button class="btn-edit" onclick="openEditAdminModal(<?php echo (int)$demoUser['id']; ?>, '<?php echo htmlspecialchars($demoUser['full_name'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($demoUser['email'], ENT_QUOTES); ?>', 1)" title="Edit Demo User">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-delete" onclick="deleteManagedAccount(<?php echo (int)$demoUser['id']; ?>, '<?php echo htmlspecialchars($demoUser['full_name'], ENT_QUOTES); ?>', 'demo_user')" title="Delete Demo User">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <?php if ($isSuperAdminViewer && $hubSummary): ?>
    <?php
    $hubShutdown = $hubSummary['shutdown'] ?? ['active' => false, 'reason' => ''];
    $hubShutdownActive = !empty($hubShutdown['active']);
    ?>
    <!-- 7th Trade Hub Integration (super admin only) -->
    <div class="settings-card hub-section-card" style="margin-top: 24px;">
        <h2 class="card-title">
            <i class="fas fa-plug"></i>
            7th Trade Hub Integration
        </h2>
        <p style="color: #666; font-size: 14px; margin: -8px 0 20px 0;">
            Demo and Owned Tool are separate — enable only the context you are using. Owned does <strong>not</strong> need to be enabled for Demo to work.
            Identity readiness only checks local emails; Hub Check connection / SSO use Client Secret. <em>Test webhook</em> also needs Webhook Secret.
            Hub <strong>Shutdown Site</strong> only works when <strong>Owned</strong> is enabled with the My Tools Integration ID (not Demo).
        </p>

        <?php if (!$hubSummary['curl_available']): ?>
        <div class="alert alert-error" style="margin-bottom: 16px;">
            <i class="fas fa-exclamation-triangle"></i>
            PHP curl extension is not available — Hub token validation and webhooks will fail until enabled.
        </div>
        <?php endif; ?>

        <div class="form-group" style="margin-bottom: 24px;">
            <label class="form-label" for="hub_url">Hub URL (shared)</label>
            <input type="url" class="form-input" id="hub_url" value="<?php echo htmlspecialchars($hubSummary['hub_url']); ?>" placeholder="https://7th-tradehub.online">
        </div>

        <div style="margin-bottom: 20px;">
            <strong style="font-size: 13px; color: #374151;">Protocol v1 endpoints (on this site)</strong>
            <div class="hub-endpoint">POST <?php echo htmlspecialchars($hubSummary['endpoints']['health']); ?></div>
            <div class="hub-endpoint">GET <?php echo htmlspecialchars($hubSummary['endpoints']['consume']); ?></div>
            <div class="hub-endpoint">POST <?php echo htmlspecialchars($hubSummary['endpoints']['subscription_sync']); ?></div>
        </div>

        <div class="hub-grid">
            <?php
            foreach (['demo' => 'Demo Integration', 'owned' => 'Owned Tool Integration'] as $ctxKey => $ctxLabel):
                $ctx = $hubSummary[$ctxKey];
                $readiness = $ctx['readiness'] ?? ['checks' => []];
            ?>
            <div class="settings-card hub-context-card">
                <h3 style="margin: 0 0 12px 0; font-size: 17px; color: #1e3a8a;">
                    <i class="fas fa-<?php echo $ctxKey === 'demo' ? 'flask' : 'store'; ?>"></i>
                    <?php echo htmlspecialchars($ctxLabel); ?>
                </h3>
                <p style="font-size: 12px; color: #6b7280; margin: 0 0 16px 0;">
                    Context: <code><?php echo htmlspecialchars($ctx['context']); ?></code>
                    <?php if (!empty($ctx['shutdown_active'])): ?>
                        <span style="color:#dc2626;font-weight:600;"> — Subscription expired (shutdown active)</span>
                    <?php endif; ?>
                </p>

                <form class="hub-integration-form" data-context="<?php echo htmlspecialchars($ctxKey === 'demo' ? 'demo' : 'owned_tool'); ?>">
                    <div class="form-group">
                        <label class="form-label">
                            <input type="checkbox" name="enabled" value="1" <?php echo !empty($ctx['enabled']) ? 'checked' : ''; ?>>
                            Enable this integration
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Integration ID</label>
                        <input type="text" class="form-input" name="integration_id" value="<?php echo htmlspecialchars($ctx['integration_id'] ?? ''); ?>" placeholder="UUID from Hub">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Client ID</label>
                        <input type="text" class="form-input" name="client_id" value="<?php echo htmlspecialchars($ctx['client_id'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            Client Secret
                            <span class="hub-secret-badge hub-client-secret-badge" style="margin-left:8px;font-size:11px;font-weight:600;<?php echo !empty($ctx['has_client_secret']) ? 'color:#059669;' : 'color:#dc2626;'; ?>">
                                <?php echo !empty($ctx['has_client_secret']) ? '● Saved on server' : '○ Not saved yet'; ?>
                            </span>
                        </label>
                        <input type="password" class="form-input" name="client_secret" autocomplete="new-password" placeholder="<?php echo !empty($ctx['has_client_secret']) ? 'Leave blank to keep saved secret — paste only to replace' : 'Paste Client Secret from Hub, then Save'; ?>">
                        <p class="help-text">The field stays empty after save on purpose (password fields never show the stored value). Use the green “Saved on server” badge to confirm it stuck.</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            Webhook Secret
                            <span class="hub-secret-badge hub-webhook-secret-badge" style="margin-left:8px;font-size:11px;font-weight:600;<?php echo !empty($ctx['has_webhook_secret']) ? 'color:#059669;' : 'color:#6b7280;'; ?>">
                                <?php echo !empty($ctx['has_webhook_secret']) ? '● Saved on server' : '○ Not saved'; ?>
                            </span>
                        </label>
                        <input type="password" class="form-input" name="webhook_secret" autocomplete="new-password" placeholder="<?php echo !empty($ctx['has_webhook_secret']) ? 'Leave blank to keep saved secret' : 'Paste Webhook Secret to use Test webhook'; ?>">
                        <p class="help-text">Not required for Hub <em>Check connection</em> or SSO login (those use Client Secret). Only needed if you want to use the <strong>Test webhook</strong> button.</p>
                    </div>
                    <?php if ($ctxKey === 'demo'): ?>
                    <div class="form-group">
                        <label class="form-label">Expected demo user email <span style="font-weight:normal;color:#888;">(readiness hint)</span></label>
                        <input type="email" class="form-input" name="expected_user_email" value="<?php echo htmlspecialchars($ctx['expected_user_email'] ?? ''); ?>">
                    </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label class="form-label">Expected admin email <span style="font-weight:normal;color:#888;">(readiness hint)</span></label>
                        <input type="email" class="form-input" name="expected_admin_email" value="<?php echo htmlspecialchars($ctx['expected_admin_email'] ?? ''); ?>">
                    </div>

                    <div style="margin: 12px 0;">
                        <strong style="font-size: 12px;">Capabilities</strong><br>
                        <?php foreach ($ctx['capabilities'] ?? [] as $cap): ?>
                            <span class="hub-cap-badge"><?php echo htmlspecialchars($cap); ?></span>
                        <?php endforeach; ?>
                    </div>

                    <div style="margin: 12px 0;" class="hub-connection-wrap">
                        <strong style="font-size: 12px;">Connection status</strong>
                        <?php
                        $op = $ctx['operational'] ?? ['ok' => false, 'reason' => 'Unknown'];
                        $opOk = !empty($op['ok']);
                        ?>
                        <div class="hub-connection-status hub-readiness-item <?php echo $opOk ? 'hub-readiness-ok' : 'hub-readiness-bad'; ?>">
                            <i class="fas fa-<?php echo $opOk ? 'check-circle' : 'times-circle'; ?>"></i>
                            <span class="hub-connection-text"><?php echo $opOk ? 'Ready for Hub traffic' : htmlspecialchars($op['reason'] ?? 'Not ready'); ?></span>
                        </div>
                    </div>

                    <div style="margin: 12px 0;" class="hub-readiness-wrap">
                        <strong style="font-size: 12px;">Identity readiness</strong>
                        <div class="hub-readiness-list">
                        <?php foreach ($readiness['checks'] ?? [] as $check): ?>
                            <?php
                            $cls = 'hub-readiness-neutral';
                            if ($check['ok'] === true) $cls = 'hub-readiness-ok';
                            if ($check['ok'] === false) $cls = 'hub-readiness-bad';
                            ?>
                            <div class="hub-readiness-item <?php echo $cls; ?>">
                                <i class="fas fa-<?php echo $check['ok'] === true ? 'check-circle' : ($check['ok'] === false ? 'times-circle' : 'minus-circle'); ?>"></i>
                                <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $check['label']))); ?>:
                                <?php echo htmlspecialchars($check['message']); ?>
                            </div>
                        <?php endforeach; ?>
                        </div>
                    </div>

                    <?php if ($ctxKey === 'owned'): ?>
                    <div class="hub-subscription-wrap" style="font-size: 13px; margin: 12px 0; padding: 10px; background: #f9fafb; border-radius: 6px;<?php echo empty($ctx['subscription']) ? ' display:none;' : ''; ?>">
                        <?php if (!empty($ctx['subscription'])): ?>
                        <strong>Subscription:</strong>
                        <span class="hub-subscription-text">
                            <?php echo htmlspecialchars($ctx['subscription']['status'] ?? 'unknown'); ?>
                            <?php if (!empty($ctx['subscription']['expires_at'])): ?>
                                · Expires <?php echo htmlspecialchars($ctx['subscription']['expires_at']); ?>
                            <?php endif; ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    <?php if (empty($ctx['subscription'])): ?>
                    <p class="hub-owned-hint" style="font-size: 13px; color: #6b7280;">Configure after customer Setup completes on Hub My Tools.</p>
                    <?php endif; ?>
                    <?php endif; ?>

                    <div class="hub-actions">
                        <button type="submit" class="btn btn-success hub-save-btn">
                            <i class="fas fa-save"></i> <span class="hub-btn-label">Save</span>
                        </button>
                        <button type="button" class="btn hub-ping-btn" style="background:#6366f1;color:#fff;" title="Requires Webhook Secret to be saved first">
                            <i class="fas fa-satellite-dish"></i> <span class="hub-btn-label">Test webhook</span>
                        </button>
                        <?php if ($ctxKey === 'owned'): ?>
                        <button type="button" class="btn hub-poll-btn" style="background:#b45309;color:#fff;" title="Fetch current subscription status from Hub (fixes missed Shutdown Site push)">
                            <i class="fas fa-sync"></i> <span class="hub-btn-label">Pull subscription from Hub</span>
                        </button>
                        <?php endif; ?>
                    </div>
                    <p class="help-text" style="margin-top:8px;">
                        <strong>Test webhook</strong> needs a saved Webhook Secret. For normal go-live, use Hub’s <strong>Check connection</strong> instead — that only needs Client Secret + Enable.
                    </p>
                    <p class="hub-inline-status" style="display:none;margin:10px 0 0;font-size:13px;" aria-live="polite"></p>
                </form>

                <div class="hub-rotation-note">
                    <strong>Credential rotation:</strong> When Hub rotates keys for this context only, update Client Secret / Webhook Secret here, save, then have the operator run <em>Check connection</em> for this integration ID. Confirm SSO still works.
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="settings-card hub-section-card" style="margin-top: 24px;" id="hub-sync-password-section">
        <h2 class="card-title">
            <i class="fas fa-cloud-upload-alt"></i>
            Sync password to Hub
        </h2>
        <p style="color: #666; font-size: 14px; margin: -8px 0 16px 0;">
            Owned tools only. Use this when the connected regular admin password changed
            <em>before</em> automatic credential sync was deployed, or to manually re-push credentials to Hub My Tools.
            Password hashes cannot be recovered — re-enter the admin’s <strong>current</strong> password.
        </p>
        <?php
        $ownedCtx = $hubSummary['owned'] ?? [];
        $ownedExpectedAdmin = trim((string)($ownedCtx['expected_admin_email'] ?? ''));
        $ownedReady = !empty($ownedCtx['operational']['ok']);
        $ownedHasWebhook = !empty($ownedCtx['has_webhook_secret']);
        ?>
        <div style="font-size: 13px; margin-bottom: 14px; padding: 10px 12px; background: #f9fafb; border-radius: 8px;">
            <div><strong>Expected admin email:</strong>
                <?php echo $ownedExpectedAdmin !== '' ? htmlspecialchars($ownedExpectedAdmin) : '<span style="color:#dc2626;">Not set — save it on Owned Tool Integration first</span>'; ?>
            </div>
            <div style="margin-top:6px;"><strong>Owned ready:</strong>
                <?php echo $ownedReady ? '<span style="color:#059669;">Yes</span>' : '<span style="color:#dc2626;">No — finish Owned setup first</span>'; ?>
            </div>
            <div style="margin-top:6px;"><strong>Webhook Secret:</strong>
                <?php echo $ownedHasWebhook ? '<span style="color:#059669;">Saved</span>' : '<span style="color:#dc2626;">Missing — required for credential sync</span>'; ?>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label" for="hub_sync_password_input">Owned admin current password</label>
            <input type="password" class="form-input" id="hub_sync_password_input" autocomplete="new-password" placeholder="Type current password to push to Hub">
        </div>
        <div class="hub-actions">
            <button type="button" class="btn hub-sync-password-btn" id="hub_sync_password_btn" style="background:#0f766e;color:#fff;">
                <i class="fas fa-cloud-upload-alt"></i> <span class="hub-btn-label">Sync password to Hub</span>
            </button>
        </div>
        <p class="hub-sync-inline-status" style="display:none;margin:12px 0 0;font-size:13px;" aria-live="polite"></p>
    </div>

    <div class="settings-card hub-section-card" style="margin-top: 24px;" id="hub-connection-logs-card">
        <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:12px;">
            <h2 class="card-title" style="margin:0;">
                <i class="fas fa-list"></i> Connection logs
            </h2>
            <button type="button" class="btn" id="hub_refresh_logs_btn" style="background:#374151;color:#fff;">
                <i class="fas fa-sync"></i> <span class="hub-btn-label">Refresh logs</span>
            </button>
        </div>
        <p style="font-size:13px; color:#6b7280; margin:0 0 12px 0;">
            This domain’s own database — health checks, webhook pings, subscription/shutdown sync, and SSO.
            If Hub shows Shutdown Site success but you see no <code>shutdown_sync</code> / <code>SHUTDOWN</code> row here, this site never received the push.
        </p>
        <div id="hub-connection-logs-list" style="max-height:360px; overflow:auto; border:1px solid #e5e7eb; border-radius:8px;">
            <?php
            $hubLogs = $hubSummary['connection_logs'] ?? [];
            if (empty($hubLogs)):
            ?>
            <div class="hub-log-empty" style="padding:16px; color:#6b7280; font-size:13px;">No Hub connection events yet on this domain. Run Hub Check connection or Test webhook, then Refresh.</div>
            <?php else: ?>
            <?php foreach ($hubLogs as $logRow):
                $logOk = !empty($logRow['ok']);
                $logEvent = (string)($logRow['event'] ?? '');
                $logMsg = (string)($logRow['message'] ?? '');
                $logWhen = (string)($logRow['created_at'] ?? '');
                $logHost = (string)($logRow['host'] ?? '');
                $logHttp = $logRow['http_status'] ?? null;
                $logDir = (string)($logRow['direction'] ?? '');
            ?>
            <div class="hub-log-row" style="padding:12px 14px; border-bottom:1px solid #f3f4f6; font-size:13px;">
                <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:baseline;">
                    <span style="font-weight:700; color:<?php echo $logOk ? '#059669' : '#dc2626'; ?>;">
                        <?php echo $logOk ? 'OK' : 'FAIL'; ?>
                    </span>
                    <span style="color:#6b7280;"><?php echo htmlspecialchars($logWhen); ?></span>
                    <span style="background:#f3f4f6; padding:2px 6px; border-radius:4px; font-family:monospace; font-size:12px;">
                        <?php echo htmlspecialchars($logEvent); ?>
                    </span>
                    <?php if ($logDir !== ''): ?>
                    <span style="color:#9ca3af; font-size:12px;"><?php echo htmlspecialchars($logDir); ?></span>
                    <?php endif; ?>
                    <?php if ($logHttp !== null && $logHttp !== ''): ?>
                    <span style="color:#6b7280;">HTTP <?php echo (int)$logHttp; ?></span>
                    <?php endif; ?>
                </div>
                <div style="margin-top:4px; color:#111827;"><?php echo htmlspecialchars($logMsg); ?></div>
                <?php if ($logHost !== ''): ?>
                <div style="margin-top:2px; color:#6b7280; font-size:12px;">Host: <?php echo htmlspecialchars($logHost); ?></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <p style="font-size:12px; color:#9ca3af; margin:8px 0 0;">Also written to <code>logs/seventh-tradehub-connection.log</code> on this server.</p>
    </div>

    <div id="hub-shutdown-banner" class="settings-card hub-section-card alert <?php echo $hubShutdownActive ? 'alert-error' : ''; ?>" style="margin-top: 24px; padding: 14px 16px; border-radius: 8px; background: <?php echo $hubShutdownActive ? '#fef2f2' : '#f9fafb'; ?>; border: 1px solid <?php echo $hubShutdownActive ? '#fecaca' : '#e5e7eb'; ?>;">
        <strong style="color: <?php echo $hubShutdownActive ? '#dc2626' : '#374151'; ?>;">
            <i class="fas fa-<?php echo $hubShutdownActive ? 'ban' : 'info-circle'; ?>"></i>
            Owned shutdown:
            <span id="hub-shutdown-state"><?php echo $hubShutdownActive ? 'ACTIVE' : 'not active'; ?></span>
        </strong>
        <div id="hub-shutdown-reason" style="margin-top:6px;font-size:13px;color:#4b5563;">
            <?php echo htmlspecialchars((string)($hubShutdown['reason'] ?? '')); ?>
        </div>
        <p style="margin:10px 0 0;font-size:12px;color:#6b7280;">
            If Hub already clicked Shutdown Site but this still says not active, Hub’s push likely failed (Hub may show a warning). Use <strong>Pull subscription from Hub</strong> on the Owned card above after Owned is enabled and credentials match My Tools.
        </p>
    </div>
    <?php endif; ?>
</div>

<!-- Add Admin Modal -->
<div class="modal-overlay" id="addAdminModal">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title">
                <i class="fas fa-user-plus"></i>
                <?php echo $isSuperAdminViewer ? 'Add Account' : 'Add New Administrator'; ?>
            </h2>
            <button class="modal-close" onclick="closeAddAdminModal()">&times;</button>
        </div>
        
        <form id="addAdminModalForm">
            <?php if ($isSuperAdminViewer): ?>
            <div class="form-group">
                <label class="form-label" for="add_account_type">Account Type *</label>
                <select class="form-input" id="add_account_type" name="account_type" required>
                    <option value="admin">Administrator</option>
                    <option value="demo_user">Demo User</option>
                </select>
                <p class="help-text">
                    <i class="fas fa-info-circle"></i>
                    Demo users are for Hub demo login only and are hidden from the Users list.
                </p>
            </div>
            <?php else: ?>
            <input type="hidden" name="account_type" value="admin">
            <?php endif; ?>

            <div class="form-group">
                <label class="form-label" for="add_admin_name">Full Name *</label>
                <input type="text" class="form-input" id="add_admin_name" name="full_name" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="add_admin_email">Email Address *</label>
                <input type="email" class="form-input" id="add_admin_email" name="email" required>
            </div>
            
                    <div class="form-group">
                        <label class="form-label" for="add_admin_password">Password *</label>
                        <input type="password" class="form-input" id="add_admin_password" name="password" autocomplete="new-password" required>
                <p class="help-text">
                    <i class="fas fa-info-circle"></i>
                    Admin will be able to change this after first login
                </p>
            </div>
            
            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <button type="submit" class="btn btn-success" style="flex: 1;" id="addAccountSubmitBtn">
                    <i class="fas fa-check"></i>
                    <span id="addAccountSubmitLabel"><?php echo $isSuperAdminViewer ? 'Add Account' : 'Add Administrator'; ?></span>
                </button>
                <button type="button" class="btn" onclick="closeAddAdminModal()" style="flex: 1; background: #6b7280;">
                    <i class="fas fa-times"></i>
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Admin Modal -->
<div class="modal-overlay" id="editAdminModal">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title">
                <i class="fas fa-edit"></i>
                Edit Administrator
            </h2>
            <button class="modal-close" onclick="closeEditAdminModal()">&times;</button>
        </div>
        
        <form id="editAdminModalForm">
            <input type="hidden" id="edit_admin_id">
            <input type="hidden" id="edit_is_demo_user" value="0">
            
            <div class="form-group" id="edit_account_type_display" style="display: none;">
                <label class="form-label">Account Type</label>
                <input type="text" class="form-input" id="edit_account_type_label" readonly>
            </div>

            <div class="form-group">
                <label class="form-label">Full Name</label>
                <input type="text" class="form-input" id="edit_admin_name" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" class="form-input" id="edit_admin_email" required>
            </div>
            
                    <div class="form-group">
                        <label class="form-label" for="edit_admin_password">New Password</label>
                        <input type="password" class="form-input" id="edit_admin_password" name="new_password" autocomplete="new-password" placeholder="Leave blank to keep current password">
                <div class="password-strength" style="margin-top: 8px;">
                    <div class="password-strength-bar" id="editStrengthBar"></div>
                </div>
                <p class="help-text">
                    <i class="fas fa-info-circle"></i>
                    Enter a new password only if you want to change it (min. 8 characters)
                </p>
            </div>
            
            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    <i class="fas fa-save"></i>
                    Save Changes
                </button>
                <button type="button" class="btn" onclick="closeEditAdminModal()" style="flex: 1; background: #6b7280;">
                    <i class="fas fa-times"></i>
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Modal functions
function openAddAdminModal() {
    document.getElementById('addAdminModal').classList.add('active');
    document.getElementById('add_admin_name').focus();
}

function closeAddAdminModal() {
    document.getElementById('addAdminModal').classList.remove('active');
    document.getElementById('addAdminModalForm').reset();
}

function openEditAdminModal(id, name, email, isDemoUser) {
    document.getElementById('edit_admin_id').value = id;
    document.getElementById('edit_admin_name').value = name;
    document.getElementById('edit_admin_email').value = email;
    document.getElementById('edit_is_demo_user').value = isDemoUser ? '1' : '0';

    const typeDisplay = document.getElementById('edit_account_type_display');
    const typeLabel = document.getElementById('edit_account_type_label');
    const modalTitle = document.querySelector('#editAdminModal .modal-title');
    if (isDemoUser) {
        typeDisplay.style.display = 'block';
        typeLabel.value = 'Demo User';
        modalTitle.innerHTML = '<i class="fas fa-edit"></i> Edit Demo User';
    } else {
        typeDisplay.style.display = 'none';
        typeLabel.value = '';
        modalTitle.innerHTML = '<i class="fas fa-edit"></i> Edit Administrator';
    }
    
    document.getElementById('edit_admin_name').setAttribute('data-original', name);
    document.getElementById('edit_admin_email').setAttribute('data-original', email);
    
    document.getElementById('editAdminModal').classList.add('active');
}

function closeEditAdminModal() {
    document.getElementById('editAdminModal').classList.remove('active');
    document.getElementById('editAdminModalForm').reset();
    document.getElementById('editStrengthBar').style.width = '0%';
}

// Close modals on overlay click
document.getElementById('addAdminModal').addEventListener('click', function(e) {
    if (e.target === this) closeAddAdminModal();
});

document.getElementById('editAdminModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditAdminModal();
});

// Add admin form submission
const accountTypeEl = document.getElementById('add_account_type');
if (accountTypeEl) {
    accountTypeEl.addEventListener('change', function() {
        const label = document.getElementById('addAccountSubmitLabel');
        if (!label) return;
        label.textContent = this.value === 'demo_user' ? 'Add Demo User' : 'Add Administrator';
    });
}

document.getElementById('addAdminModalForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('action', 'add_admin');
    formData.append('full_name', document.getElementById('add_admin_name').value);
    formData.append('email', document.getElementById('add_admin_email').value);
    formData.append('password', document.getElementById('add_admin_password').value);
    const accountTypeEl = document.getElementById('add_account_type');
    formData.append('account_type', accountTypeEl ? accountTypeEl.value : 'admin');
    
    // Submit via traditional form
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?php echo SITE_URL; ?>/admin/admin-settings';
    
    for (let pair of formData.entries()) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = pair[0];
        input.value = pair[1];
        form.appendChild(input);
    }
    
    document.body.appendChild(form);
    form.submit();
});

// Edit admin form submission
document.getElementById('editAdminModalForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const adminId = document.getElementById('edit_admin_id').value;
    const newEmail = document.getElementById('edit_admin_email').value;
    const newName = document.getElementById('edit_admin_name').value;
    const newPassword = document.getElementById('edit_admin_password').value;
    
    // Validate email and name
    if (!newEmail || !newName) {
        showToast('Email and name are required', 'error');
        return;
    }
    
    // Check what needs to be updated
    const originalEmail = document.getElementById('edit_admin_email').getAttribute('data-original') || '';
    const originalName = document.getElementById('edit_admin_name').getAttribute('data-original') || '';
    const isPasswordChange = newPassword && newPassword.trim() !== '';
    const isEmailChange = newEmail !== originalEmail;
    const isNameChange = newName !== originalName;
    const isInfoChange = isEmailChange || isNameChange;
    
    // If nothing to update, show message and return
    if (!isPasswordChange && !isInfoChange) {
        showToast('No changes to save', 'warning');
        return;
    }
    
    // Validate password if being changed
    if (isPasswordChange) {
        if (newPassword.length < 8) {
            showToast('Password must be at least 8 characters long', 'error');
            return;
        }
    }
    
    // Sequential updates: Password first, then email/name
    // This ensures we know what succeeded/failed
    const updatePassword = () => {
        if (!isPasswordChange) {
            return Promise.resolve({ success: true, skipped: true });
        }
        
        return fetch('<?php echo SITE_URL; ?>/api/admin-update-password.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                admin_id: adminId,
                new_password: newPassword
            })
        })
        .then(response => {
            return response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Response is not JSON:', text.substring(0, 500));
                    throw new Error('Server returned HTML instead of JSON.');
                }
            });
        });
    };
    
    const updateInfo = () => {
        if (!isInfoChange) {
            return Promise.resolve({ success: true, skipped: true });
        }
        
        return fetch('<?php echo SITE_URL; ?>/api/admin-update-info.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                admin_id: adminId,
                email: newEmail,
                full_name: newName
            })
        })
        .then(response => response.json());
    };
    
    // Execute updates sequentially
    updatePassword()
        .then(passwordResult => {
            if (!passwordResult.success && !passwordResult.skipped) {
                throw new Error('Password update failed: ' + (passwordResult.message || 'Unknown error'));
            }
            
            // If only password was updated and it succeeded, reload
            if (passwordResult.success && !isInfoChange) {
                showToast('Password updated successfully!', 'success');
                closeEditAdminModal();
                location.reload();
                return;
            }
            
            // Continue to email/name update if needed
            return updateInfo();
        })
        .then(infoResult => {
            if (!infoResult.success && !infoResult.skipped) {
                throw new Error('Information update failed: ' + (infoResult.message || 'Unknown error'));
            }
            
            // Build success message
            let successMsg = '';
            if (isPasswordChange && isInfoChange) {
                successMsg = 'Administrator information and password updated successfully!';
            } else if (isPasswordChange) {
                successMsg = 'Password updated successfully!';
            } else {
                successMsg = 'Administrator information updated successfully!';
            }
            
            showToast(successMsg, 'success');
            closeEditAdminModal();
            location.reload();
        })
        .catch(error => {
            console.error('Update Error:', error);
            showToast('Error: ' + error.message, 'error');
        });
});

// Password strength indicator for edit form
const editPasswordInput = document.getElementById('edit_admin_password');
const editStrengthBar = document.getElementById('editStrengthBar');

if (editPasswordInput) {
    editPasswordInput.addEventListener('input', function() {
        const password = this.value;
        let strength = 0;
        
        if (password.length >= 8) strength += 25;
        if (password.match(/[a-z]/)) strength += 25;
        if (password.match(/[A-Z]/)) strength += 25;
        if (password.match(/[0-9]/)) strength += 15;
        if (password.match(/[^a-zA-Z0-9]/)) strength += 10;
        
        editStrengthBar.style.width = strength + '%';
        
        if (strength < 50) {
            editStrengthBar.style.background = '#ef4444';
        } else if (strength < 75) {
            editStrengthBar.style.background = '#f59e0b';
        } else {
            editStrengthBar.style.background = '#10b981';
        }
    });
}

// Delete managed account (admin or demo user)
function deleteManagedAccount(accountId, accountName, accountKind) {
    const title = accountKind === 'demo_user' ? 'Delete Demo User' : 'Delete Administrator';
    const label = accountKind === 'demo_user' ? 'demo user' : 'administrator';
    showModal(
        title,
        `Are you sure you want to delete ${label} "${accountName}"?\n\nThis action cannot be undone.`,
        'danger',
        function() {
            fetch('<?php echo SITE_URL; ?>/api/delete-admin.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    admin_id: accountId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message || 'Account deleted successfully!', 'success');
                    window.location.reload();
                } else {
                    showToast('Error: ' + (data.message || 'Failed to delete account'), 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('An error occurred while deleting the account', 'error');
            });
        }
    );
}

function deleteAdmin(adminId, adminName) {
    deleteManagedAccount(adminId, adminName, 'admin');
}

function toggleAdminDetails(button) {
    const card = button.closest('.admin-card-mobile');
    const details = card.querySelector('.admin-details-mobile');
    const isExpanded = details.classList.contains('expanded');
    
    if (isExpanded) {
        details.classList.remove('expanded');
        button.classList.remove('active');
    } else {
        details.classList.add('expanded');
        button.classList.add('active');
    }
}

<?php if ($isSuperAdminViewer && $hubSummary): ?>
(function() {
    var hubApiUrl = '<?php echo SITE_URL; ?>/api/admin-seventh-tradehub-settings.php';

    function setHubButtonLoading(btn, loading, loadingText) {
        if (!btn) return;
        var icon = btn.querySelector('i');
        var label = btn.querySelector('.hub-btn-label');
        if (loading) {
            btn.disabled = true;
            btn.dataset.busy = '1';
            btn.style.opacity = '0.85';
            btn.style.cursor = 'wait';
            if (icon) icon.className = 'fas fa-spinner fa-spin';
            if (label) label.textContent = loadingText || 'Working…';
        } else {
            btn.disabled = false;
            btn.dataset.busy = '0';
            btn.style.opacity = '';
            btn.style.cursor = '';
            if (btn.classList.contains('hub-save-btn')) {
                if (icon) icon.className = 'fas fa-save';
                if (label) label.textContent = 'Save';
            } else {
                if (icon) icon.className = 'fas fa-satellite-dish';
                if (label) label.textContent = 'Test webhook';
            }
        }
    }

    function setInlineStatus(form, message, ok) {
        var el = form.querySelector('.hub-inline-status');
        if (!el) return;
        el.style.display = message ? 'block' : 'none';
        el.style.color = ok === true ? '#059669' : (ok === false ? '#dc2626' : '#6b7280');
        el.textContent = message || '';
    }

    function escapeHtml(str) {
        return String(str == null ? '' : str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function updateConnectionStatus(form, operational) {
        var row = form.querySelector('.hub-connection-status');
        var text = form.querySelector('.hub-connection-text');
        var icon = row ? row.querySelector('i') : null;
        if (!row || !operational) return;
        var ok = !!operational.ok;
        row.classList.remove('hub-readiness-ok', 'hub-readiness-bad', 'hub-readiness-neutral');
        row.classList.add(ok ? 'hub-readiness-ok' : 'hub-readiness-bad');
        if (icon) icon.className = 'fas fa-' + (ok ? 'check-circle' : 'times-circle');
        if (text) text.textContent = ok ? 'Ready for Hub traffic' : (operational.reason || 'Not ready');
    }

    function updateReadiness(form, readiness) {
        var list = form.querySelector('.hub-readiness-list');
        if (!list || !readiness || !Array.isArray(readiness.checks)) return;
        if (!readiness.checks.length) {
            list.innerHTML = '<div class="hub-readiness-item hub-readiness-neutral"><i class="fas fa-minus-circle"></i> No readiness hints configured</div>';
            return;
        }
        list.innerHTML = readiness.checks.map(function(check) {
            var cls = 'hub-readiness-neutral';
            var icon = 'minus-circle';
            if (check.ok === true) { cls = 'hub-readiness-ok'; icon = 'check-circle'; }
            if (check.ok === false) { cls = 'hub-readiness-bad'; icon = 'times-circle'; }
            var label = String(check.label || '').replace(/_/g, ' ');
            label = label.charAt(0).toUpperCase() + label.slice(1);
            return '<div class="hub-readiness-item ' + cls + '">' +
                '<i class="fas fa-' + icon + '"></i> ' +
                escapeHtml(label) + ': ' + escapeHtml(check.message || '') +
                '</div>';
        }).join('');
    }

    function updateSecretPlaceholders(form, ctx) {
        var clientSecret = form.querySelector('[name="client_secret"]');
        var webhookSecret = form.querySelector('[name="webhook_secret"]');
        var clientBadge = form.querySelector('.hub-client-secret-badge');
        var webhookBadge = form.querySelector('.hub-webhook-secret-badge');

        if (clientSecret) {
            clientSecret.value = '';
            clientSecret.placeholder = ctx && ctx.has_client_secret
                ? 'Leave blank to keep saved secret — paste only to replace'
                : 'Paste Client Secret from Hub, then Save';
        }
        if (clientBadge) {
            if (ctx && ctx.has_client_secret) {
                clientBadge.textContent = '● Saved on server';
                clientBadge.style.color = '#059669';
            } else {
                clientBadge.textContent = '○ Not saved yet';
                clientBadge.style.color = '#dc2626';
            }
        }
        if (webhookSecret) {
            webhookSecret.value = '';
            webhookSecret.placeholder = ctx && ctx.has_webhook_secret
                ? 'Leave blank to keep saved secret'
                : 'Paste Webhook Secret to use Test webhook';
        }
        if (webhookBadge) {
            if (ctx && ctx.has_webhook_secret) {
                webhookBadge.textContent = '● Saved on server';
                webhookBadge.style.color = '#059669';
            } else {
                webhookBadge.textContent = '○ Not saved';
                webhookBadge.style.color = '#6b7280';
            }
        }
    }

    function applyHubContextToForm(form, ctx) {
        if (!ctx) return;
        var enabled = form.querySelector('[name="enabled"]');
        if (enabled) enabled.checked = !!ctx.enabled;
        var integrationId = form.querySelector('[name="integration_id"]');
        if (integrationId) integrationId.value = ctx.integration_id || '';
        var clientId = form.querySelector('[name="client_id"]');
        if (clientId) clientId.value = ctx.client_id || '';
        var expectedUser = form.querySelector('[name="expected_user_email"]');
        if (expectedUser) expectedUser.value = ctx.expected_user_email || '';
        var expectedAdmin = form.querySelector('[name="expected_admin_email"]');
        if (expectedAdmin) expectedAdmin.value = ctx.expected_admin_email || '';
        updateSecretPlaceholders(form, ctx);
        updateConnectionStatus(form, ctx.operational);
        updateReadiness(form, ctx.readiness);

        var subWrap = form.querySelector('.hub-subscription-wrap');
        var subText = form.querySelector('.hub-subscription-text');
        var ownedHint = form.closest('.hub-context-card') ? form.closest('.hub-context-card').querySelector('.hub-owned-hint') : null;
        if (subWrap) {
            if (ctx.subscription) {
                subWrap.style.display = '';
                if (subText) {
                    var line = (ctx.subscription.status || 'unknown');
                    if (ctx.subscription.expires_at) line += ' · Expires ' + ctx.subscription.expires_at;
                    subText.textContent = line;
                }
                if (ownedHint) ownedHint.style.display = 'none';
            } else {
                subWrap.style.display = 'none';
                if (ownedHint) ownedHint.style.display = '';
            }
        }
    }

    function postHub(payload) {
        return fetch(hubApiUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        }).then(async function(r) {
            var data = null;
            try {
                data = await r.json();
            } catch (e) {
                throw new Error('Server returned HTTP ' + r.status + ' (invalid JSON)');
            }
            if (!r.ok && data && data.message) {
                throw new Error(data.message);
            }
            return data;
        });
    }

    document.querySelectorAll('.hub-integration-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var saveBtn = form.querySelector('.hub-save-btn');
            var pingBtn = form.querySelector('.hub-ping-btn');
            if (saveBtn && saveBtn.dataset.busy === '1') return;

            var context = form.getAttribute('data-context');
            var payload = {
                action: 'save',
                context: context,
                hub_url: (document.getElementById('hub_url') || {}).value || '',
                enabled: !!(form.querySelector('[name="enabled"]') || {}).checked,
                integration_id: (form.querySelector('[name="integration_id"]') || {}).value || '',
                client_id: (form.querySelector('[name="client_id"]') || {}).value || '',
                client_secret: (form.querySelector('[name="client_secret"]') || {}).value || '',
                hub_client_secret: (form.querySelector('[name="client_secret"]') || {}).value || '',
                webhook_secret: (form.querySelector('[name="webhook_secret"]') || {}).value || '',
                hub_webhook_secret: (form.querySelector('[name="webhook_secret"]') || {}).value || '',
                expected_admin_email: (form.querySelector('[name="expected_admin_email"]') || {}).value || ''
            };
            var userEmail = form.querySelector('[name="expected_user_email"]');
            if (userEmail) payload.expected_user_email = userEmail.value;

            setHubButtonLoading(saveBtn, true, 'Saving…');
            if (pingBtn) pingBtn.disabled = true;
            setInlineStatus(form, 'Saving settings…', null);

            postHub(payload)
                .then(function(data) {
                    if (!data || !data.success) {
                        throw new Error((data && data.message) || 'Save failed');
                    }
                    var ctxKey = context === 'demo' ? 'demo' : 'owned';
                    var ctx = data.data && data.data[ctxKey] ? data.data[ctxKey] : null;
                    if (data.data && typeof data.data.hub_url === 'string' && document.getElementById('hub_url')) {
                        document.getElementById('hub_url').value = data.data.hub_url;
                    }
                    applyHubContextToForm(form, ctx);
                    var ok = !!(ctx && ctx.operational && ctx.operational.ok);
                    setInlineStatus(form, data.message || 'Saved', ok);
                    if (typeof showToast === 'function') {
                        showToast(data.message || 'Saved', ok ? 'success' : 'info');
                    }
                })
                .catch(function(err) {
                    setInlineStatus(form, err.message || 'Save failed', false);
                    if (typeof showToast === 'function') {
                        showToast(err.message || 'Save failed', 'error');
                    }
                })
                .finally(function() {
                    setHubButtonLoading(saveBtn, false);
                    if (pingBtn) pingBtn.disabled = false;
                });
        });
    });

    function renderHubConnectionLogs(logs) {
        var list = document.getElementById('hub-connection-logs-list');
        if (!list) return;
        if (!logs || !logs.length) {
            list.innerHTML = '<div class="hub-log-empty" style="padding:16px; color:#6b7280; font-size:13px;">No Hub connection events yet on this domain. Run Hub Check connection or Test webhook, then Refresh.</div>';
            return;
        }
        var html = '';
        logs.forEach(function(row) {
            var ok = !!row.ok;
            var when = row.created_at || '';
            var event = row.event || '';
            var msg = row.message || '';
            var host = row.host || '';
            var http = row.http_status != null ? String(row.http_status) : '';
            var dir = row.direction || '';
            html += '<div class="hub-log-row" style="padding:12px 14px; border-bottom:1px solid #f3f4f6; font-size:13px;">';
            html += '<div style="display:flex; gap:10px; flex-wrap:wrap; align-items:baseline;">';
            html += '<span style="font-weight:700; color:' + (ok ? '#059669' : '#dc2626') + ';">' + (ok ? 'OK' : 'FAIL') + '</span>';
            html += '<span style="color:#6b7280;">' + escapeHtml(when) + '</span>';
            html += '<span style="background:#f3f4f6; padding:2px 6px; border-radius:4px; font-family:monospace; font-size:12px;">' + escapeHtml(event) + '</span>';
            if (dir) html += '<span style="color:#9ca3af; font-size:12px;">' + escapeHtml(dir) + '</span>';
            if (http) html += '<span style="color:#6b7280;">HTTP ' + escapeHtml(http) + '</span>';
            html += '</div>';
            html += '<div style="margin-top:4px; color:#111827;">' + escapeHtml(msg) + '</div>';
            if (host) html += '<div style="margin-top:2px; color:#6b7280; font-size:12px;">Host: ' + escapeHtml(host) + '</div>';
            html += '</div>';
        });
        list.innerHTML = html;
    }

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    var hubRefreshLogsBtn = document.getElementById('hub_refresh_logs_btn');
    if (hubRefreshLogsBtn) {
        hubRefreshLogsBtn.addEventListener('click', function() {
            if (hubRefreshLogsBtn.dataset.busy === '1') return;
            setHubButtonLoading(hubRefreshLogsBtn, true, 'Refreshing…');
            postHub({ action: 'get_connection_logs', limit: 50 })
                .then(function(data) {
                    if (!data || !data.success) {
                        throw new Error((data && data.message) || 'Failed to load logs');
                    }
                    var payload = data.data || {};
                    renderHubConnectionLogs(payload.connection_logs || []);
                    if (payload.shutdown) {
                        applyHubShutdownBanner(payload.shutdown);
                    }
                    if (typeof showToast === 'function') {
                        showToast('Connection logs refreshed', 'success');
                    }
                })
                .catch(function(err) {
                    if (typeof showToast === 'function') {
                        showToast(err.message || 'Failed to load logs', 'error');
                    }
                })
                .finally(function() {
                    setHubButtonLoading(hubRefreshLogsBtn, false);
                });
        });
    }

    document.querySelectorAll('.hub-ping-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var form = btn.closest('.hub-integration-form');
            if (!form || btn.dataset.busy === '1') return;
            var saveBtn = form.querySelector('.hub-save-btn');
            var context = form.getAttribute('data-context');

            setHubButtonLoading(btn, true, 'Testing…');
            if (saveBtn) saveBtn.disabled = true;
            setInlineStatus(form, 'Sending webhook ping…', null);

            postHub({ action: 'webhook_ping', context: context })
                .then(function(data) {
                    var ok = !!(data && data.success);
                    var msg = (data && data.message) || (ok ? 'Ping OK' : 'Ping failed');
                    setInlineStatus(form, msg, ok);
                    if (typeof showToast === 'function') {
                        showToast(msg, ok ? 'success' : 'error');
                    }
                    // Refresh connection logs so ping appears like Hub’s Connection logs
                    if (hubRefreshLogsBtn && !hubRefreshLogsBtn.dataset.busy) {
                        hubRefreshLogsBtn.click();
                    }
                })
                .catch(function(err) {
                    setInlineStatus(form, err.message || 'Ping failed', false);
                    if (typeof showToast === 'function') {
                        showToast(err.message || 'Ping failed', 'error');
                    }
                })
                .finally(function() {
                    setHubButtonLoading(btn, false);
                    if (saveBtn) saveBtn.disabled = false;
                });
        });
    });

    function applyHubShutdownBanner(shutdown) {
        var banner = document.getElementById('hub-shutdown-banner');
        var stateEl = document.getElementById('hub-shutdown-state');
        var reasonEl = document.getElementById('hub-shutdown-reason');
        if (!shutdown || !stateEl || !reasonEl) return;
        var active = !!shutdown.active;
        stateEl.textContent = active ? 'ACTIVE' : 'not active';
        reasonEl.textContent = shutdown.reason || '';
        if (banner) {
            banner.style.background = active ? '#fef2f2' : '#f9fafb';
            banner.style.borderColor = active ? '#fecaca' : '#e5e7eb';
            var strong = banner.querySelector('strong');
            if (strong) strong.style.color = active ? '#dc2626' : '#374151';
            var icon = banner.querySelector('strong i');
            if (icon) {
                icon.className = active ? 'fas fa-ban' : 'fas fa-info-circle';
            }
        }
    }

    document.querySelectorAll('.hub-poll-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var form = btn.closest('.hub-integration-form');
            if (!form || btn.dataset.busy === '1') return;
            var saveBtn = form.querySelector('.hub-save-btn');
            var pingBtn = form.querySelector('.hub-ping-btn');

            setHubButtonLoading(btn, true, 'Pulling…');
            if (saveBtn) saveBtn.disabled = true;
            if (pingBtn) pingBtn.disabled = true;
            setInlineStatus(form, 'Pulling subscription from Hub…', null);

            postHub({ action: 'poll_owned_subscription' })
                .then(function(data) {
                    var ok = !!(data && data.success);
                    var msg = (data && data.message) || (ok ? 'Pulled' : 'Pull failed');
                    if (data && data.data && data.data.owned) {
                        applyHubContextToForm(form, data.data.owned);
                    }
                    if (data && data.shutdown) {
                        applyHubShutdownBanner(data.shutdown);
                    } else if (data && data.data && data.data.shutdown) {
                        applyHubShutdownBanner(data.data.shutdown);
                    }
                    if (data && data.data && data.data.connection_logs) {
                        renderHubConnectionLogs(data.data.connection_logs);
                    } else if (hubRefreshLogsBtn && !hubRefreshLogsBtn.dataset.busy) {
                        hubRefreshLogsBtn.click();
                    }
                    setInlineStatus(form, msg, ok);
                    if (typeof showToast === 'function') {
                        showToast(msg, ok ? 'success' : 'error');
                    }
                })
                .catch(function(err) {
                    setInlineStatus(form, err.message || 'Pull failed', false);
                    if (typeof showToast === 'function') {
                        showToast(err.message || 'Pull failed', 'error');
                    }
                })
                .finally(function() {
                    setHubButtonLoading(btn, false);
                    if (saveBtn) saveBtn.disabled = false;
                    if (pingBtn) pingBtn.disabled = false;
                });
        });
    });

    var hubSyncStatusEl = document.querySelector('.hub-sync-inline-status');
    function setHubSyncStatus(msg, ok) {
        if (!hubSyncStatusEl) return;
        hubSyncStatusEl.style.display = 'block';
        hubSyncStatusEl.textContent = msg || '';
        hubSyncStatusEl.style.color = ok === true ? '#059669' : (ok === false ? '#dc2626' : '#6b7280');
    }

    var hubSyncBtn = document.getElementById('hub_sync_password_btn');
    if (hubSyncBtn) {
        hubSyncBtn.addEventListener('click', function() {
            if (hubSyncBtn.dataset.busy === '1') return;
            var input = document.getElementById('hub_sync_password_input');
            var password = input ? String(input.value || '') : '';
            if (!password) {
                setHubSyncStatus('Enter the owned admin current password first', false);
                return;
            }

            setHubButtonLoading(hubSyncBtn, true, 'Syncing…');
            setHubSyncStatus('Verifying password and syncing to Hub…', null);

            postHub({ action: 'sync_owned_admin_password', password: password })
                .then(function(data) {
                    var ok = !!(data && data.success);
                    var msg = (data && data.message) || (ok ? 'Synced' : 'Sync failed');
                    setHubSyncStatus(msg, ok);
                    if (ok && input) input.value = '';
                    if (typeof showToast === 'function') {
                        showToast(msg, ok ? 'success' : 'error');
                    }
                })
                .catch(function(err) {
                    setHubSyncStatus(err.message || 'Sync failed', false);
                    if (typeof showToast === 'function') {
                        showToast(err.message || 'Sync failed', 'error');
                    }
                })
                .finally(function() {
                    setHubButtonLoading(hubSyncBtn, false);
                });
        });
    }
})();
<?php endif; ?>
</script>

</body>
</html>

