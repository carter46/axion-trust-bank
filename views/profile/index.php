<?php 
$pageTitle = 'My Profile - ' . getSiteName();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

requireLogin();

// Fetch user data
$db = Database::getInstance();
$userId = $_SESSION['user_id'];

$stmt = $db->query("SELECT * FROM users WHERE id = ?", [$userId]);
$user = $stmt ? $stmt->fetch() : null;

// Get joint account relationship information
require_once __DIR__ . '/../../models/JointAccount.php';
$jointAccount = new JointAccount();
$jointRelationship = $jointAccount->getJointAccountRelationship($userId);

// SECURITY: Verify user exists (should be caught by requireLogin, but double-check)
if (!$user) {
    // User account was deleted during session - destroy session and redirect to login
    session_destroy();
    $_SESSION = [];
    session_start();
    $_SESSION['error'] = 'Your account is no longer active.';
    header('Location: ' . SITE_URL . '/auth/login');
    exit;
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

.profile-card {
    background: white;
    border-radius: 16px;
    padding: 32px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    margin-bottom: 30px;
}

.profile-header {
    display: flex;
    align-items: center;
    gap: 24px;
    padding-bottom: 30px;
    border-bottom: 2px solid #f0f0f0;
    margin-bottom: 30px;
}

.profile-photo {
    width: 100px;
    height: 100px;
    min-width: 100px;
    min-height: 100px;
    max-width: 100px;
    max-height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #e5e7eb;
    flex-shrink: 0;
    flex-grow: 0;
    box-sizing: border-box;
    aspect-ratio: 1 / 1;
}

.profile-photo-placeholder {
    width: 100px;
    height: 100px;
    min-width: 100px;
    min-height: 100px;
    max-width: 100px;
    max-height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 40px;
    color: white;
    font-weight: 700;
    border: 4px solid #e5e7eb;
    flex-shrink: 0;
    flex-grow: 0;
    box-sizing: border-box;
    aspect-ratio: 1 / 1;
}

.profile-info {
    flex: 1;
}

.profile-info h2 {
    font-size: 28px;
    color: #032B44;
    margin: 0 0 8px 0;
    font-weight: 600;
}

.profile-info p {
    color: #666;
    font-size: 16px;
    margin: 0;
}

.profile-menu {
    display: flex;
    flex-direction: column;
    gap: 0;
}

.profile-menu-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 24px;
    text-decoration: none;
    border-bottom: 1px solid #f0f0f0;
    transition: all 0.3s;
    cursor: pointer;
}

.profile-menu-item:last-child {
    border-bottom: none;
}

.profile-menu-item:hover {
    background: #f8f9fa;
    padding-left: 32px;
}

.menu-item-left {
    display: flex;
    align-items: center;
    gap: 16px;
}

.menu-item-icon {
    width: 48px;
    height: 48px;
    min-width: 48px;
    min-height: 48px;
    max-width: 48px;
    max-height: 48px;
    border-radius: 12px;
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
    flex-shrink: 0;
    flex-grow: 0;
    box-sizing: border-box;
}

.menu-item-content h4 {
    font-size: 18px;
    color: #032B44;
    margin: 0 0 4px 0;
    font-weight: 600;
}

.menu-item-content p {
    font-size: 14px;
    color: #666;
    margin: 0;
}

.menu-item-arrow {
    font-size: 20px;
    color: #666;
    transition: all 0.3s;
}

.profile-menu-item:hover .menu-item-arrow {
    color: #1e3a8a;
    transform: translateX(4px);
}

@media (max-width: 768px) {
    .page-header h1 {
        font-size: 24px;
    }
    
    .profile-card {
        padding: 20px;
    }
    
    .profile-header {
        flex-direction: column;
        text-align: center;
        gap: 16px;
    }
    
    .profile-photo, .profile-photo-placeholder {
        width: 80px !important;
        height: 80px !important;
        min-width: 80px !important;
        min-height: 80px !important;
        max-width: 80px !important;
        max-height: 80px !important;
        font-size: 32px;
        flex-shrink: 0 !important;
        flex-grow: 0 !important;
        border-radius: 50% !important; /* Ensure circular */
        object-fit: cover !important;
        aspect-ratio: 1 / 1 !important;
        box-sizing: border-box !important;
        overflow: hidden !important;
        display: block !important;
    }
    
    .profile-photo {
        border: 3px solid #e5e7eb !important;
    }
    
    .profile-photo-placeholder {
        border: 3px solid #e5e7eb !important;
    }
    
    .profile-info h2 {
        font-size: 22px;
    }
    
    .profile-menu-item {
        padding: 16px 20px;
    }
    
    .menu-item-icon {
        width: 40px !important;
        height: 40px !important;
        min-width: 40px !important;
        min-height: 40px !important;
        max-width: 40px !important;
        max-height: 40px !important;
        font-size: 18px;
        flex-shrink: 0 !important;
        flex-grow: 0 !important;
        box-sizing: border-box !important;
    }
    
    .menu-item-content h4 {
        font-size: 16px;
    }
    
    .menu-item-content p {
        font-size: 13px;
    }
}
</style>

<div class="page-header">
    <h1>My Profile</h1>
    <p>Manage your account settings and preferences</p>
</div>

<div class="profile-card">
    <!-- Profile Header with Photo and Name -->
    <div class="profile-header">
        <?php if (!empty($user['profile_picture'])): ?>
            <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Photo" class="profile-photo">
        <?php else: 
            $initials = '';
            $nameParts = explode(' ', $user['full_name']);
            foreach ($nameParts as $part) {
                $initials .= strtoupper(substr($part, 0, 1));
            }
            $initials = substr($initials, 0, 2);
        ?>
            <div class="profile-photo-placeholder">
                <?php echo htmlspecialchars($initials); ?>
            </div>
        <?php endif; ?>
        
        <div class="profile-info">
            <h2><?php echo htmlspecialchars($user['full_name']); ?></h2>
            <p><?php echo htmlspecialchars($user['email']); ?></p>
            <p style="color: #1e3a8a; font-weight: 600; margin-top: 4px;">
                <?php echo ucfirst($user['role']); ?> Account
            </p>
        </div>
    </div>
    
    <!-- Profile Menu List -->
    <div class="profile-menu">
        
        <!-- User Information -->
        <a href="<?php echo SITE_URL; ?>/profile/edit" class="profile-menu-item">
            <div class="menu-item-left">
                <div class="menu-item-icon">
                    <i class="fas fa-user-edit"></i>
                </div>
                <div class="menu-item-content">
                    <h4>User Information</h4>
                    <p>Personal details, address, phone, and profile photo</p>
                </div>
            </div>
            <div class="menu-item-arrow">
                <i class="fas fa-chevron-right"></i>
            </div>
        </a>
        
        <!-- Security Settings -->
        <a href="<?php echo SITE_URL; ?>/profile/security" class="profile-menu-item">
            <div class="menu-item-left">
                <div class="menu-item-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="menu-item-content">
                    <h4>Security Settings</h4>
                    <p>Password, Login PIN, Transfer PIN, and 2FA</p>
                </div>
            </div>
            <div class="menu-item-arrow">
                <i class="fas fa-chevron-right"></i>
            </div>
        </a>
        
        <!-- KYC Verification -->
        <a href="<?php echo SITE_URL; ?>/profile/kyc" class="profile-menu-item">
            <div class="menu-item-left">
                <div class="menu-item-icon">
                    <i class="fas fa-id-card"></i>
                </div>
                <div class="menu-item-content">
                    <h4>KYC Verification</h4>
                    <p>View, submit, and update KYC documents</p>
                </div>
            </div>
            <div class="menu-item-arrow">
                <i class="fas fa-chevron-right"></i>
            </div>
        </a>
        
        <!-- Notifications -->
        <a href="<?php echo SITE_URL; ?>/profile/notifications" class="profile-menu-item">
            <div class="menu-item-left">
                <div class="menu-item-icon">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="menu-item-content">
                    <h4>Notifications</h4>
                    <p>Manage email and SMS notification preferences</p>
                </div>
            </div>
            <div class="menu-item-arrow">
                <i class="fas fa-chevron-right"></i>
            </div>
        </a>
        
        <!-- Settings -->
        <a href="<?php echo SITE_URL; ?>/profile/settings" class="profile-menu-item">
            <div class="menu-item-left">
                <div class="menu-item-icon">
                    <i class="fas fa-cog"></i>
                </div>
                <div class="menu-item-content">
                    <h4>Settings</h4>
                    <p>Language, currency, timezone, and display preferences</p>
                </div>
            </div>
            <div class="menu-item-arrow">
                <i class="fas fa-chevron-right"></i>
            </div>
        </a>
        
        <!-- Joint Account Requests -->
        <?php
        // Check if user has any accounts that could receive joint requests
        $hasAccounts = !empty($userAccounts);
        if ($hasAccounts):
            // Check if user has pending requests
            require_once __DIR__ . '/../../models/JointAccount.php';
            $jointAccount = new JointAccount();
            $pendingRequests = [];
            foreach ($userAccounts as $account) {
                $requests = $jointAccount->getPendingRequests($userId);
                if (!empty($requests)) {
                    $pendingRequests = array_merge($pendingRequests, $requests);
                }
            }
            $requestCount = count($pendingRequests);
        ?>
        <a href="<?php echo SITE_URL; ?>/account/joint-requests" class="profile-menu-item">
            <div class="menu-item-left">
                <div class="menu-item-icon" style="background: linear-gradient(135deg, #10b981 0%, #34d399 100%);">
                    <i class="fas fa-users"></i>
                </div>
                <div class="menu-item-content">
                    <h4>Joint Account Requests <?php if ($requestCount > 0): ?><span style="background: #ef4444; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px; margin-left: 8px;"><?php echo $requestCount; ?></span><?php endif; ?></h4>
                    <p>Review and manage requests to join your accounts</p>
                </div>
            </div>
            <div class="menu-item-arrow">
                <i class="fas fa-chevron-right"></i>
            </div>
        </a>
        <?php endif; ?>
        
    </div>
</div>

<!-- Account Owners Section -->
<?php if ($jointRelationship): ?>
<div class="profile-card">
    <h2 style="margin: 0 0 24px 0; color: #032B44; font-size: 24px;">Account Owners</h2>
    
    <?php 
    $primaryOwner = $jointRelationship['primary_owner'];
    $secondaryOwners = isset($jointRelationship['secondary_owner']) ? [$jointRelationship['secondary_owner']] : ($jointRelationship['secondary_owners'] ?? []);
    $jointAccountCreatedAt = $jointRelationship['joint_account_created_at'];
    ?>
    
    <div style="margin-bottom: 24px; padding: 16px; background: #f0f9ff; border-radius: 12px; border-left: 4px solid #3b82f6;">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
            <i class="fas fa-calendar-check" style="color: #3b82f6;"></i>
            <strong style="color: #032B44;">Joint Account Created:</strong>
            <span style="color: #666;">
                <?php 
                if ($jointAccountCreatedAt) {
                    echo date('F j, Y', strtotime($jointAccountCreatedAt));
                } else {
                    echo 'N/A';
                }
                ?>
                </span>
        </div>
    </div>
            
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
        <!-- Primary Owner -->
        <div style="background: #f9fafb; padding: 24px; border-radius: 12px; border-left: 4px solid #10b981;">
            <div style="display: flex; align-items: flex-start; gap: 16px; margin-bottom: 16px;">
                <?php if (!empty($primaryOwner['primary_owner_picture'] ?? $primaryOwner['profile_picture'])): ?>
                    <img src="<?php echo htmlspecialchars($primaryOwner['primary_owner_picture'] ?? $primaryOwner['profile_picture']); ?>" 
                         alt="<?php echo htmlspecialchars($primaryOwner['primary_owner_name'] ?? $primaryOwner['full_name']); ?>" 
                         style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 3px solid #10b981;">
                <?php else: ?>
                    <div style="width: 60px; height: 60px; border-radius: 50%; background: #10b981; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 20px; flex-shrink: 0;">
                        <?php 
                        $name = $primaryOwner['primary_owner_name'] ?? $primaryOwner['full_name'];
                        $nameParts = explode(' ', $name);
                        $initials = '';
                        foreach ($nameParts as $part) {
                            $initials .= strtoupper(substr($part, 0, 1));
                        }
                        echo substr($initials, 0, 2);
                        ?>
                    </div>
                <?php endif; ?>
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                        <div style="font-weight: 600; color: #032B44; font-size: 18px;">
                            <?php echo htmlspecialchars($primaryOwner['primary_owner_name'] ?? $primaryOwner['full_name']); ?>
                        </div>
                        <span style="background: #10b981; color: white; padding: 4px 12px; border-radius: 12px; font-size: 11px; font-weight: 600;">Primary Owner</span>
                    </div>
                    <div style="font-size: 14px; color: #666; margin-bottom: 4px;">
                        <i class="fas fa-envelope" style="margin-right: 6px; width: 14px;"></i>
                        <?php echo htmlspecialchars($primaryOwner['primary_owner_email'] ?? $primaryOwner['email']); ?>
                    </div>
                    <?php if (!empty($primaryOwner['primary_owner_phone'] ?? $primaryOwner['phone'])): ?>
                    <div style="font-size: 14px; color: #666;">
                        <i class="fas fa-phone" style="margin-right: 6px; width: 14px;"></i>
                        <?php echo htmlspecialchars($primaryOwner['primary_owner_phone'] ?? $primaryOwner['phone']); ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #e5e7eb;">
                <div style="font-size: 13px;">
                    <div style="color: #999; margin-bottom: 4px;">Last Login</div>
                    <div style="color: #032B44; font-weight: 500;">
                        <?php 
                        $lastLogin = $primaryOwner['primary_owner_last_login'] ?? $primaryOwner['last_login'] ?? null;
                        if ($lastLogin) {
                            echo date('M j, Y g:i A', strtotime($lastLogin));
                        } else {
                            echo 'Never';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Secondary Owner(s) -->
        <?php foreach ($secondaryOwners as $secondaryOwner): ?>
        <div style="background: #f9fafb; padding: 24px; border-radius: 12px; border-left: 4px solid #3b82f6;">
            <div style="display: flex; align-items: flex-start; gap: 16px; margin-bottom: 16px;">
                <?php if (!empty($secondaryOwner['secondary_owner_picture'] ?? $secondaryOwner['profile_picture'])): ?>
                    <img src="<?php echo htmlspecialchars($secondaryOwner['secondary_owner_picture'] ?? $secondaryOwner['profile_picture']); ?>" 
                         alt="<?php echo htmlspecialchars($secondaryOwner['secondary_owner_name'] ?? $secondaryOwner['full_name']); ?>" 
                         style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 3px solid #3b82f6;">
                <?php else: ?>
                    <div style="width: 60px; height: 60px; border-radius: 50%; background: #3b82f6; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 20px; flex-shrink: 0;">
                                <?php 
                        $name = $secondaryOwner['secondary_owner_name'] ?? $secondaryOwner['full_name'];
                        $nameParts = explode(' ', $name);
                                $initials = '';
                                foreach ($nameParts as $part) {
                                    $initials .= strtoupper(substr($part, 0, 1));
                                }
                                echo substr($initials, 0, 2);
                                ?>
                            </div>
                <?php endif; ?>
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                        <div style="font-weight: 600; color: #032B44; font-size: 18px;">
                            <?php echo htmlspecialchars($secondaryOwner['secondary_owner_name'] ?? $secondaryOwner['full_name']); ?>
                        </div>
                        <span style="background: #3b82f6; color: white; padding: 4px 12px; border-radius: 12px; font-size: 11px; font-weight: 600;">Secondary Owner</span>
                    </div>
                    <div style="font-size: 14px; color: #666; margin-bottom: 4px;">
                        <i class="fas fa-envelope" style="margin-right: 6px; width: 14px;"></i>
                        <?php echo htmlspecialchars($secondaryOwner['secondary_owner_email'] ?? $secondaryOwner['email']); ?>
                    </div>
                    <?php if (!empty($secondaryOwner['secondary_owner_phone'] ?? $secondaryOwner['phone'])): ?>
                    <div style="font-size: 14px; color: #666;">
                        <i class="fas fa-phone" style="margin-right: 6px; width: 14px;"></i>
                        <?php echo htmlspecialchars($secondaryOwner['secondary_owner_phone'] ?? $secondaryOwner['phone']); ?>
                    </div>
                                    <?php endif; ?>
                                </div>
            </div>
            
            <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #e5e7eb;">
                <div style="font-size: 13px;">
                    <div style="color: #999; margin-bottom: 4px;">Last Login</div>
                    <div style="color: #032B44; font-weight: 500;">
                        <?php 
                        $lastLogin = $secondaryOwner['secondary_owner_last_login'] ?? $secondaryOwner['last_login'] ?? null;
                        if ($lastLogin) {
                            echo date('M j, Y g:i A', strtotime($lastLogin));
                        } else {
                            echo 'Never';
                        }
                        ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
</div>
<?php endif; ?>

<?php
// Include mobile navigation and closing tags
include __DIR__ . '/../../includes/mobile-nav.php';
?>
