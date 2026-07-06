<?php 
$pageTitle = 'Manage Users - Admin - SecureBank';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

// Include head
include __DIR__ . '/../../includes/head.php';

// Include admin sidebar
include __DIR__ . '/../../includes/admin-sidebar.php';
include __DIR__ . '/../../includes/admin-modals.php';
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

.btn-view-mobile {
    background: #eff6ff;
    color: #1d4ed8;
}

.btn-delete-mobile {
    background: #fee2e2;
    color: #dc2626;
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
}
</style>

<div class="page-header">
    <div>
        <h1>Manage Users</h1>
        <p style="color: #666; margin: 0;">View and manage all registered users</p>
    </div>
    <a href="<?php echo SITE_URL; ?>/admin/user-create" class="add-user-btn">
        <i class="fas fa-user-plus"></i>
        <span>Add New User</span>
    </a>
</div>

<div class="card">
    <h3 style="color: #032B44; margin-bottom: 20px;">All Users</h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
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
                                <a href="<?php echo SITE_URL; ?>/admin/user/<?php echo $user['id']; ?>" 
                                   style="color: #4f46e5; text-decoration: none; font-weight: 500; margin-right: 15px;">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="<?php echo SITE_URL; ?>/admin/login-as/<?php echo $user['id']; ?>" 
                                   onclick="return confirm('Are you sure you want to login as <?php echo htmlspecialchars($user['full_name']); ?>? You will be redirected to their dashboard.');"
                                   style="color: #10b981; text-decoration: none; font-weight: 500; margin-right: 15px;">
                                    <i class="fas fa-sign-in-alt"></i> Login as
                                </a>
                                <a href="#" onclick="deleteUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['full_name']); ?>'); return false;"
                                   style="color: #ef4444; text-decoration: none; font-weight: 500;">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: #666; padding: 40px;">No users found</td>
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
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="<?php echo SITE_URL; ?>/admin/login-as/<?php echo $user['id']; ?>" 
                               onclick="return confirm('Are you sure you want to login as <?php echo htmlspecialchars($user['full_name']); ?>? You will be redirected to their dashboard.');"
                               class="btn-view-mobile" style="background: #d1fae5; color: #065f46;">
                                <i class="fas fa-sign-in-alt"></i> Login as
                            </a>
                            <button onclick="deleteUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['full_name']); ?>')" class="btn-delete-mobile">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="text-align: center; color: #666; padding: 40px;">No users found</div>
        <?php endif; ?>
    </div>
</div>

<script>
function deleteUser(userId, userName) {
    showModal(
        'Delete User Account',
        `Are you sure you want to delete user "${userName}"?\n\nThis action cannot be undone and will:\n- Delete the user account\n- Remove all associated data\n- Cannot be reversed`,
        'danger',
        function() {
            console.log('Deleting user:', userId);
            
            fetch('<?php echo SITE_URL; ?>/api/admin-delete-user.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ user_id: userId })
            })
            .then(response => {
                console.log('Response status:', response.status);
                
                if (!response.ok) {
                    return response.text().then(text => {
                        console.error('HTTP Error Response:', text);
                        throw new Error('HTTP error! status: ' + response.status + ', body: ' + text);
                    });
                }
                
                return response.text().then(text => {
                    console.log('Raw API Response:', text);
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Failed to parse JSON:', text);
                        throw new Error('Invalid JSON response: ' + text);
                    }
                });
            })
            .then(data => {
                console.log('Parsed API Response:', data);
                if (data.success) {
                    console.log('User deletion successful!');
                    showToast('User deleted successfully', 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 500);
                } else {
                    const errorMsg = data.message || 'Failed to delete user';
                    console.error('API Error Response:', JSON.stringify(data, null, 2));
                    if (data.error_details) {
                        console.error('Error Details:', JSON.stringify(data.error_details, null, 2));
                    }
                    showToast('Error: ' + errorMsg, 'error');
                }
            })
            .catch(error => {
                console.error('Network/Fetch Error:', error);
                console.error('Error stack:', error.stack);
                showToast('An error occurred while deleting the user: ' + error.message, 'error');
            });
        }
    );
}

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
</script>
