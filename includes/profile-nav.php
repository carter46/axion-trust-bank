<?php
/**
 * Profile Navigation Tabs
 * Shared across all profile pages
 */

$currentPage = $_SERVER['REQUEST_URI'];
$isOverview = (strpos($currentPage, '/profile') !== false && strpos($currentPage, '/profile/') === false);
$isSecurity = (strpos($currentPage, '/profile/security') !== false || strpos($currentPage, '/profile/change-password') !== false || strpos($currentPage, '/profile/two-factor') !== false);
$isKYC = (strpos($currentPage, '/profile/kyc') !== false);
$isUserInfo = (strpos($currentPage, '/profile/edit') !== false || strpos($currentPage, '/profile/info') !== false);
?>

<style>
    /* Profile Page Styles (Same as Dashboard/Account Pattern) */
    .main-content-area .content-area {
        background: #f5f7fa !important;
        padding: 15px !important;
        overflow-x: hidden !important;
    }
    
    .profile-page-container {
        max-width: 100%;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 25px;
    }
    
    /* Page Header (Same as Dashboard) */
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
    
    /* Tab Navigation */
    .profile-tabs {
        display: flex;
        gap: 8px;
        background: white;
        padding: 8px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .profile-tab {
        padding: 12px 24px;
        border-radius: 8px;
        text-decoration: none;
        color: #666;
        font-weight: 500;
        font-size: 14px;
        white-space: nowrap;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .profile-tab:hover {
        background: #f5f7fa;
        color: #1e3a8a;
    }
    
    .profile-tab.active {
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
    }
    
    /* Content Cards */
    .profile-card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    
    .profile-card h3 {
        font-size: 20px;
        color: #1e3a8a;
        font-weight: 600;
        margin: 0 0 20px 0;
    }
    
    @media (max-width: 768px) {
        .header h1 {
            font-size: 24px;
            padding-top: 15px;
        }
        
        .header p {
            font-size: 14px;
            padding-bottom: 15px;
        }
        
        .profile-tabs {
            gap: 4px;
            padding: 6px;
        }
        
        .profile-tab {
            padding: 10px 16px;
            font-size: 13px;
        }
    }
</style>

<div class="profile-page-container">
    <!-- Page Header -->
    <div class="header">
        <h1>Profile Settings</h1>
        <p>Manage your account information and preferences</p>
    </div>
    
    <!-- Tab Navigation -->
    <div class="profile-tabs">
        <a href="<?php echo SITE_URL; ?>/profile" class="profile-tab <?php echo $isOverview ? 'active' : ''; ?>">
            <i class="fas fa-user"></i>
            <span>Overview</span>
        </a>
        <a href="<?php echo SITE_URL; ?>/profile/security" class="profile-tab <?php echo $isSecurity ? 'active' : ''; ?>">
            <i class="fas fa-shield-alt"></i>
            <span>Security</span>
        </a>
        <a href="<?php echo SITE_URL; ?>/profile/kyc" class="profile-tab <?php echo $isKYC ? 'active' : ''; ?>">
            <i class="fas fa-id-card"></i>
            <span>KYC Verification</span>
        </a>
        <a href="<?php echo SITE_URL; ?>/profile/edit" class="profile-tab <?php echo $isUserInfo ? 'active' : ''; ?>">
            <i class="fas fa-user-edit"></i>
            <span>User Information</span>
        </a>
    </div>

