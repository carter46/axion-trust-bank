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
            padding: 15px;
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
    <!-- Demo Users (super admin only) -->
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
</script>

</body>
</html>

