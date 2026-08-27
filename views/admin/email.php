<?php
$pageTitle = 'Email Management - Admin';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAdmin();

include __DIR__ . '/../../includes/head.php';
include __DIR__ . '/../../includes/admin-sidebar.php';
?>

<style>
    .email-container {
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
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .page-header p {
        color: #666;
        font-size: 15px;
        margin: 0;
    }
    
    .sub-nav {
        display: flex;
        flex-direction: column;
        gap: 0;
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        margin-bottom: 30px;
    }
    
    .sub-nav-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 24px;
        text-decoration: none;
        border-bottom: 1px solid #f0f0f0;
        transition: all 0.3s;
        cursor: pointer;
    }
    
    .sub-nav-item:last-child {
        border-bottom: none;
    }
    
    .sub-nav-item:hover {
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
    
    .sub-nav-item:hover .menu-item-arrow {
        color: #1e3a8a;
        transform: translateX(4px);
    }
    
    @media (max-width: 768px) {
        .sub-nav-item {
            padding: 16px 20px;
        }
        
        .menu-item-icon {
            width: 40px;
            height: 40px;
            min-width: 40px;
            min-height: 40px;
            max-width: 40px;
            max-height: 40px;
            font-size: 18px;
        }
        
        .menu-item-content h4 {
            font-size: 16px;
        }
        
        .menu-item-content p {
            font-size: 13px;
        }
        
        .email-container {
            padding: 15px;
        }
    }
</style>

<div class="email-container">
    <div class="page-header">
        <h1>
            <i class="fas fa-envelope"></i>
            Email Management
        </h1>
        <p>Send emails to users, manage email communications, and test email templates</p>
    </div>
    
    <!-- Sub-page Navigation -->
    <div class="sub-nav">
        <a href="<?php echo SITE_URL; ?>/admin/email/send" class="sub-nav-item">
            <div class="menu-item-left">
                <div class="menu-item-icon">
                    <i class="fas fa-paper-plane"></i>
                </div>
                <div class="menu-item-content">
                    <h4>Send & Receive Email</h4>
                    <p>Send emails to users, all users, or external email addresses</p>
                </div>
            </div>
            <div class="menu-item-arrow">
                <i class="fas fa-chevron-right"></i>
            </div>
        </a>
        
        <a href="<?php echo SITE_URL; ?>/admin/email/test" class="sub-nav-item">
            <div class="menu-item-left">
                <div class="menu-item-icon">
                    <i class="fas fa-vial"></i>
                </div>
                <div class="menu-item-content">
                    <h4>Test Email</h4>
                    <p>Test your SMTP configuration and email templates</p>
                </div>
            </div>
            <div class="menu-item-arrow">
                <i class="fas fa-chevron-right"></i>
            </div>
        </a>
        
        <?php
        // Hidden for now — set to true to show Simulation Flash Test / Simulation Settings again
        $showEmailSimulationTools = false;
        if ($showEmailSimulationTools):
        ?>
        <a href="<?php echo SITE_URL; ?>/admin/email/simulation-test" class="sub-nav-item">
            <div class="menu-item-left">
                <div class="menu-item-icon">
                    <i class="fas fa-bolt"></i>
                </div>
                <div class="menu-item-content">
                    <h4>Simulation Flash Test</h4>
                    <p>Test financial transaction emails with customizable scenarios</p>
                </div>
            </div>
            <div class="menu-item-arrow">
                <i class="fas fa-chevron-right"></i>
            </div>
        </a>
        
        <a href="<?php echo SITE_URL; ?>/admin/email/simulation-settings" class="sub-nav-item">
            <div class="menu-item-left">
                <div class="menu-item-icon">
                    <i class="fas fa-cog"></i>
                </div>
                <div class="menu-item-content">
                    <h4>Simulation Settings</h4>
                    <p>Manage alert captions and email templates for testing</p>
                </div>
            </div>
            <div class="menu-item-arrow">
                <i class="fas fa-chevron-right"></i>
            </div>
        </a>
        <?php endif; ?>
    </div>
</div>

<?php
echo '</div></div></div>'; // Close content-area, main-content-area, dashboard-container
?>
</body>
</html>

