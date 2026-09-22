<?php 
$pageTitle = 'Manage Users - Admin - SecureBank';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

// Include head
include __DIR__ . '/../../includes/head.php';

// Include admin sidebar
include __DIR__ . '/../../includes/admin-sidebar.php';
include __DIR__ . '/../../includes/admin-modals.php';

$users = $users ?? [];
$currentPage = (int)($currentPage ?? 1);
$totalPages = (int)($totalPages ?? 1);
$totalUsers = (int)($totalUsers ?? count($users));
$perPage = (int)($perPage ?? 100);
$pageFrom = $totalUsers > 0 ? (($currentPage - 1) * $perPage) + 1 : 0;
$pageTo = min($totalUsers, $currentPage * $perPage);
$paginationBase = SITE_URL . '/admin/users';
?>

<!-- ===== ADMIN USERS PAGE CONTENT ===== -->

<style>
.page-header {
    margin-bottom: 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.page-header h1 {
    font-size: 32px;
    font-weight: 700;
    color: #032B44;
    margin-bottom: 8px;
}

.add-user-btn {
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    color: white;
    padding: 12px 24px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
    box-shadow: 0 2px 8px rgba(30, 58, 138, 0.2);
    white-space: nowrap;
    flex-shrink: 0;
}

.add-user-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
}

.card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 24px;
    padding: 30px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.table-responsive {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

table th {
    padding: 12px;
    text-align: left;
    border-bottom: 2px solid #e0e0e0;
    color: #032B44;
    font-weight: 600;
}

table td {
    padding: 12px;
    border-bottom: 1px solid #f0f0f0;
}

/* Mobile User Cards */
.mobile-user-cards {
    display: none;
}

.bulk-actions-bar {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    padding: 14px 16px;
    margin-bottom: 16px;
    background: #f8f9fa;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
}

.bulk-actions-bar label {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    color: #374151;
    cursor: pointer;
    margin: 0;
}

.bulk-actions-bar input[type="checkbox"],
.user-select-cell input[type="checkbox"],
.user-card-select input[type="checkbox"] {
    width: 16px;
    height: 16px;
    cursor: pointer;
}

#selectedCount {
    color: #6b7280;
    font-size: 14px;
}

.bulk-delete-btn {
    padding: 8px 16px;
    border: none;
    border-radius: 8px;
    background: #ef4444;
    color: white;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
}

.bulk-delete-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.bulk-delete-btn:not(:disabled):hover {
    background: #dc2626;
}

.user-select-cell {
    width: 40px;
    text-align: center;
}

.user-card-select {
    margin-right: 12px;
    display: flex;
    align-items: center;
}

.pagination-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 24px;
    padding-top: 20px;
    border-top: 1px solid #e5e7eb;
}

.pagination-info {
    color: #6b7280;
    font-size: 14px;
}

.pagination-links {
    display: flex;
    align-items: center;
    gap: 8px;
}

.pagination-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 8px;
    border: 1px solid #d1d5db;
    background: white;
    color: #374151;
    font-weight: 600;
    font-size: 14px;
    text-decoration: none;
    transition: all 0.2s;
}

.pagination-btn:hover {
    background: #f3f4f6;
    border-color: #9ca3af;
}

.pagination-btn.disabled {
    opacity: 0.45;
    pointer-events: none;
}

.pagination-current {
    padding: 8px 12px;
    font-size: 14px;
    color: #374151;
    font-weight: 600;
}

.user-card-mobile {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.user-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.user-info-mobile {
    flex: 1;
}

.user-name-mobile {
    font-weight: 600;
    color: #1f2937;
    font-size: 16px;
    margin-bottom: 4px;
}

.user-email-mobile {
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

.user-details-mobile {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
}

.user-details-mobile.expanded {
    max-height: 300px;
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

.mobile-actions a, .mobile-actions button {
    flex: 1;
    min-width: 0;
    padding: 10px 6px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    font-size: 11px;
    line-height: 1.2;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 6px;
    border: none;
    cursor: pointer;
    transition: all 0.3s;
    text-align: center;
    white-space: nowrap;
    overflow: hidden;
}

.mobile-actions a i,
.mobile-actions button i {
    font-size: 16px;
    line-height: 1;
    margin: 0;
}

.btn-view-mobile {
    background: #eff6ff;
    color: #1d4ed8;
}

.btn-login-mobile {
    background: #d1fae5;
    color: #065f46;
}

.btn-delete-mobile {
    background: #fee2e2;
    color: #dc2626;
}

/* Desktop actions dropdown — fixed so it isn't clipped by table/card overflow */
.user-actions-menu {
    position: relative;
    display: inline-flex;
    justify-content: flex-end;
}

.user-actions-toggle {
    width: 36px;
    height: 36px;
    border: 1px solid #e5e7eb;
    background: #fff;
    border-radius: 8px;
    color: #374151;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s, border-color 0.2s, color 0.2s;
}

.user-actions-toggle:hover,
.user-actions-menu.open .user-actions-toggle {
    background: #f3f4f6;
    border-color: #d1d5db;
    color: #111827;
}

.user-actions-dropdown {
    position: fixed;
    min-width: 168px;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.18);
    padding: 6px;
    z-index: 10050;
    display: none;
    margin: 0;
}

.user-actions-dropdown.is-open {
    display: block;
}

.user-actions-dropdown a,
.user-actions-dropdown button {
    width: 100%;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    border: none;
    background: transparent;
    border-radius: 8px;
    color: #374151;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    text-align: left;
}

.user-actions-dropdown a:hover,
.user-actions-dropdown button:hover {
    background: #f3f4f6;
}

.user-actions-dropdown .action-login {
    color: #059669;
}

.user-actions-dropdown .action-delete {
    color: #dc2626;
}

.user-actions-dropdown i {
    width: 16px;
    text-align: center;
}

@media (max-width: 768px) {
    .page-header {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 15px !important;
    }
    
    .page-header > div {
        width: 100%;
    }
    
    .add-user-btn {
        width: 100%;
        justify-content: center;
        text-align: center;
    }
    
    .page-header h1 {
        font-size: 24px;
    }
    
    .card {
        padding: 20px;
    }
    
    .table-responsive {
        display: none;
    }
    
    .mobile-user-cards {
        display: block;
    }

    .user-details-mobile.expanded {
        max-height: 420px;
    }
}
</style>

<div class="page-header">
    <div>
        <h1>Manage Users</h1>
        <p style="color: #666; margin: 0;">View and manage registered users — <?php echo (int)$totalUsers; ?> total, 100 per page</p>
    </div>
    <a href="<?php echo SITE_URL; ?>/admin/user-create" class="add-user-btn">
        <i class="fas fa-user-plus"></i>
        <span>Add New User</span>
    </a>
</div>

<div class="card">
    <h3 style="color: #032B44; margin-bottom: 20px;">All Users</h3>
    <?php if (!empty($users)): ?>
    <div class="bulk-actions-bar">
        <label>
            <input type="checkbox" id="selectAllUsers" aria-label="Select all on this page">
            Select all on this page
        </label>
        <span id="selectedCount">0 selected</span>
        <button type="button" id="bulkDeleteBtn" class="bulk-delete-btn" disabled>
            <i class="fas fa-trash"></i> Delete selected
        </button>
    </div>
    <?php endif; ?>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th class="user-select-cell">
                        <input type="checkbox" id="selectAllUsersHeader" aria-label="Select all" title="Select all">
                    </th>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="user-select-cell">
                                <input type="checkbox" class="user-select" value="<?php echo (int)$user['id']; ?>" aria-label="Select user <?php echo htmlspecialchars($user['full_name']); ?>">
                            </td>
                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><span style="text-transform: capitalize;"><?php echo htmlspecialchars($user['role']); ?></span></td>
                            <td>
                                <span style="padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; 
                                    <?php 
                                    if ($user['status'] === 'active') echo 'background: #d1fae5; color: #065f46;';
                                    elseif ($user['status'] === 'suspended' || $user['status'] === 'blocked') echo 'background: #fee2e2; color: #991b1b;';
                                    elseif ($user['status'] === 'pending') echo 'background: #fef3c7; color: #92400e;';
                                    else echo 'background: #e5e7eb; color: #1f2937;';
                                    ?>">
                                    <?php echo ucfirst($user['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="user-actions-menu">
                                    <button type="button" class="user-actions-toggle" aria-label="Actions" aria-haspopup="true" aria-expanded="false" onclick="toggleUserActionsMenu(this)">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="user-actions-dropdown" role="menu">
                                        <a href="<?php echo SITE_URL; ?>/admin/user/<?php echo $user['id']; ?>" role="menuitem">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a class="action-login" href="<?php echo SITE_URL; ?>/admin/login-as/<?php echo $user['id']; ?>"
                                           onclick="return confirm('Login as <?php echo htmlspecialchars($user['full_name'], ENT_QUOTES); ?>? You will be redirected to their dashboard.');"
                                           role="menuitem">
                                            <i class="fas fa-sign-in-alt"></i> Login
                                        </a>
                                        <button type="button" class="action-delete" role="menuitem"
                                                onclick="deleteUser(<?php echo (int)$user['id']; ?>, '<?php echo htmlspecialchars($user['full_name'], ENT_QUOTES); ?>')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: #666; padding: 40px;">No users found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Mobile View -->
    <div class="mobile-user-cards">
        <?php if (!empty($users)): ?>
            <?php foreach ($users as $user): ?>
                <div class="user-card-mobile">
                    <div class="user-card-header">
                        <label class="user-card-select">
                            <input type="checkbox" class="user-select" value="<?php echo (int)$user['id']; ?>" aria-label="Select user <?php echo htmlspecialchars($user['full_name']); ?>">
                        </label>
                        <div class="user-info-mobile">
                            <div class="user-name-mobile"><?php echo htmlspecialchars($user['full_name']); ?></div>
                            <div class="user-email-mobile"><?php echo htmlspecialchars($user['email']); ?></div>
                        </div>
                        <button class="expand-btn" onclick="toggleUserDetails(this)">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                    <div class="user-details-mobile">
                        <div class="detail-row">
                            <span class="detail-label">ID</span>
                            <span class="detail-value">#<?php echo $user['id']; ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Role</span>
                            <span class="detail-value" style="text-transform: capitalize;"><?php echo $user['role']; ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Status</span>
                            <span style="padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; 
                                <?php 
                                if ($user['status'] === 'active') echo 'background: #d1fae5; color: #065f46;';
                                elseif ($user['status'] === 'suspended' || $user['status'] === 'blocked') echo 'background: #fee2e2; color: #991b1b;';
                                elseif ($user['status'] === 'pending') echo 'background: #fef3c7; color: #92400e;';
                                else echo 'background: #e5e7eb; color: #1f2937;';
                                ?>">
                                <?php echo ucfirst($user['status']); ?>
                            </span>
                        </div>
                        <div class="mobile-actions">
                            <a href="<?php echo SITE_URL; ?>/admin/user/<?php echo $user['id']; ?>" class="btn-view-mobile">
                                <i class="fas fa-eye"></i>
                                <span>View</span>
                            </a>
                            <a href="<?php echo SITE_URL; ?>/admin/login-as/<?php echo $user['id']; ?>"
                               onclick="return confirm('Login as <?php echo htmlspecialchars($user['full_name'], ENT_QUOTES); ?>?');"
                               class="btn-login-mobile">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>Login</span>
                            </a>
                            <button type="button" onclick="deleteUser(<?php echo (int)$user['id']; ?>, '<?php echo htmlspecialchars($user['full_name'], ENT_QUOTES); ?>')" class="btn-delete-mobile">
                                <i class="fas fa-trash"></i>
                                <span>Delete</span>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="text-align: center; color: #666; padding: 40px;">No users found</div>
        <?php endif; ?>
    </div>

    <?php if ($totalPages > 1 || $totalUsers > 0): ?>
    <div class="pagination-bar">
        <div class="pagination-info">
            Showing <?php echo (int)$pageFrom; ?>–<?php echo (int)$pageTo; ?> of <?php echo (int)$totalUsers; ?> users
            (<?php echo (int)$perPage; ?> per page)
        </div>
        <div class="pagination-links">
            <?php if ($currentPage > 1): ?>
                <a class="pagination-btn" href="<?php echo $paginationBase . '?page=' . ($currentPage - 1); ?>">
                    <i class="fas fa-chevron-left"></i> Previous
                </a>
            <?php else: ?>
                <span class="pagination-btn disabled"><i class="fas fa-chevron-left"></i> Previous</span>
            <?php endif; ?>

            <span class="pagination-current"><?php echo (int)$currentPage; ?> / <?php echo (int)$totalPages; ?></span>

            <?php if ($currentPage < $totalPages): ?>
                <a class="pagination-btn" href="<?php echo $paginationBase . '?page=' . ($currentPage + 1); ?>">
                    Next <i class="fas fa-chevron-right"></i>
                </a>
            <?php else: ?>
                <span class="pagination-btn disabled">Next <i class="fas fa-chevron-right"></i></span>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function getSelectedUserIds() {
    const ids = Array.from(document.querySelectorAll('.user-select:checked'))
        .map(cb => parseInt(cb.value, 10))
        .filter(id => id > 0);
    return [...new Set(ids)];
}

function getAllUserIdsOnPage() {
    const ids = Array.from(document.querySelectorAll('.user-select'))
        .map(cb => parseInt(cb.value, 10))
        .filter(id => id > 0);
    return [...new Set(ids)];
}

function setAllUserCheckboxes(checked) {
    document.querySelectorAll('.user-select').forEach(cb => {
        cb.checked = checked;
    });
    updateBulkSelectionUi();
}

function updateBulkSelectionUi() {
    const checkedIds = getSelectedUserIds();
    const allIds = getAllUserIdsOnPage();
    const count = checkedIds.length;
    const total = allIds.length;

    const countEl = document.getElementById('selectedCount');
    const bulkBtn = document.getElementById('bulkDeleteBtn');
    const selectAll = document.getElementById('selectAllUsers');
    const selectAllHeader = document.getElementById('selectAllUsersHeader');

    if (countEl) countEl.textContent = count + ' selected';
    if (bulkBtn) bulkBtn.disabled = count === 0;

    const allChecked = total > 0 && count === total;
    const someChecked = count > 0 && count < total;

    if (selectAll) {
        selectAll.checked = allChecked;
        selectAll.indeterminate = someChecked;
    }
    if (selectAllHeader) {
        selectAllHeader.checked = allChecked;
        selectAllHeader.indeterminate = someChecked;
    }
}

function bulkDeleteUsers() {
    const ids = getSelectedUserIds();
    if (!ids.length) {
        showToast('Select at least one user on this page', 'error');
        return;
    }
    if (ids.length > 100) {
        showToast('Maximum 100 users per bulk delete', 'error');
        return;
    }

    showModal(
        'Delete Selected Users',
        'Delete ' + ids.length + ' selected user' + (ids.length === 1 ? '' : 's') + ' on this page?\n\nThis cannot be undone. Accounts, transactions, cards, and related data will be removed.',
        'danger',
        function() {
            fetch('/api/admin-bulk-delete-users.php', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ user_ids: ids })
            })
            .then(response => response.text().then(text => {
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    throw new Error(text || 'Invalid response');
                }
                if (!response.ok && !data.message) {
                    throw new Error('HTTP error ' + response.status);
                }
                return data;
            }))
            .then(data => {
                if (data.success) {
                    showToast(data.message || 'Users deleted', 'success');
                    setTimeout(() => location.reload(), 500);
                } else {
                    showToast('Error: ' + (data.message || 'Failed to delete users'), 'error');
                }
            })
            .catch(error => {
                showToast('An error occurred while deleting users: ' + error.message, 'error');
            });
        }
    );
}

function deleteUser(userId, userName) {
    showModal(
        'Delete User Account',
        `Are you sure you want to delete user "${userName}"?\n\nThis action cannot be undone and will:\n- Delete the user account\n- Remove all associated data\n- Cannot be reversed`,
        'danger',
        function() {
            fetch('/api/admin-delete-user.php', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ user_id: userId })
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error('HTTP error! status: ' + response.status + ', body: ' + text);
                    });
                }
                return response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        throw new Error('Invalid JSON response: ' + text);
                    }
                });
            })
            .then(data => {
                if (data.success) {
                    showToast('User deleted successfully', 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 500);
                } else {
                    showToast('Error: ' + (data.message || 'Failed to delete user'), 'error');
                }
            })
            .catch(error => {
                showToast('An error occurred while deleting the user: ' + error.message, 'error');
            });
        }
    );
}

let activeUserActionsMenu = null;

function closeAllUserActionsMenus() {
    document.querySelectorAll('.user-actions-menu.open').forEach(menu => {
        menu.classList.remove('open');
        const toggle = menu.querySelector('.user-actions-toggle');
        if (toggle) toggle.setAttribute('aria-expanded', 'false');
    });

    document.querySelectorAll('.user-actions-dropdown.is-open').forEach(dropdown => {
        dropdown.classList.remove('is-open');
        dropdown.style.top = '';
        dropdown.style.left = '';
        dropdown.style.right = '';
        dropdown.style.bottom = '';
        dropdown.style.display = '';
        dropdown.style.visibility = '';
        const home = dropdown._menuHome;
        if (home && dropdown.parentNode !== home) {
            home.appendChild(dropdown);
        }
        delete dropdown._menuHome;
    });

    activeUserActionsMenu = null;
}

function positionUserActionsDropdown(toggle, dropdown) {
    dropdown.style.visibility = 'hidden';
    dropdown.style.display = 'block';
    dropdown.classList.add('is-open');

    const rect = toggle.getBoundingClientRect();
    const menuWidth = Math.max(dropdown.offsetWidth || 168, 168);
    const menuHeight = dropdown.offsetHeight || 140;
    const gap = 6;
    const pad = 8;

    // Prefer below the button; flip above only when there isn't enough room below
    let top = rect.bottom + gap;
    if (top + menuHeight > window.innerHeight - pad && rect.top - gap - menuHeight >= pad) {
        top = rect.top - menuHeight - gap;
    }

    let left = rect.right - menuWidth;
    if (left < pad) left = pad;
    if (left + menuWidth > window.innerWidth - pad) {
        left = Math.max(pad, window.innerWidth - menuWidth - pad);
    }

    // Keep fully on-screen without drifting far from the button
    top = Math.min(Math.max(top, pad), Math.max(pad, window.innerHeight - menuHeight - pad));

    dropdown.style.top = Math.round(top) + 'px';
    dropdown.style.left = Math.round(left) + 'px';
    dropdown.style.right = 'auto';
    dropdown.style.bottom = 'auto';
    dropdown.style.visibility = '';
}

function toggleUserActionsMenu(button) {
    const menu = button.closest('.user-actions-menu');
    if (!menu) return;
    const dropdown = menu.querySelector('.user-actions-dropdown');
    if (!dropdown) return;

    const willOpen = !menu.classList.contains('open');
    closeAllUserActionsMenus();

    if (!willOpen) return;

    menu.classList.add('open');
    button.setAttribute('aria-expanded', 'true');
    dropdown._menuHome = menu;
    document.body.appendChild(dropdown);
    activeUserActionsMenu = menu;
    positionUserActionsDropdown(button, dropdown);
}

document.addEventListener('click', function(e) {
    if (e.target.closest('.user-actions-menu') || e.target.closest('.user-actions-dropdown')) return;
    closeAllUserActionsMenus();
});

document.addEventListener('keydown', function(e) {
    if (e.key !== 'Escape') return;
    closeAllUserActionsMenus();
});

window.addEventListener('resize', closeAllUserActionsMenus);
window.addEventListener('scroll', function() {
    if (activeUserActionsMenu) closeAllUserActionsMenus();
}, true);

function toggleUserDetails(button) {
    const card = button.closest('.user-card-mobile');
    const details = card.querySelector('.user-details-mobile');
    const isExpanded = details.classList.contains('expanded');
    
    if (isExpanded) {
        details.classList.remove('expanded');
        button.classList.remove('active');
    } else {
        details.classList.add('expanded');
        button.classList.add('active');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAllUsers');
    const selectAllHeader = document.getElementById('selectAllUsersHeader');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            setAllUserCheckboxes(selectAll.checked);
        });
    }
    if (selectAllHeader) {
        selectAllHeader.addEventListener('change', function() {
            setAllUserCheckboxes(selectAllHeader.checked);
        });
    }

    document.querySelectorAll('.user-select').forEach(cb => {
        cb.addEventListener('change', function() {
            const id = this.value;
            document.querySelectorAll('.user-select').forEach(other => {
                if (other !== this && other.value === id) {
                    other.checked = this.checked;
                }
            });
            updateBulkSelectionUi();
        });
    });

    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', bulkDeleteUsers);
    }

    updateBulkSelectionUi();
});
</script>
