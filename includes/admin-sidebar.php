<div class="dashboard-container">
    <!-- Admin Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo"><?php echo getSiteName(); ?> Admin</div>
            <button class="sidebar-toggle" onclick="toggleSidebar()">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
        
        <div class="sidebar-menu">
            <a href="<?php echo SITE_URL; ?>/admin" class="menu-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin') !== false && strpos($_SERVER['REQUEST_URI'], '/admin/') === false) ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="<?php echo SITE_URL; ?>/admin/users" class="menu-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/users') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-users"></i>
                <span>Users</span>
            </a>
            <a href="<?php echo SITE_URL; ?>/admin/transactions" class="menu-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/transactions') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-exchange-alt"></i>
                <span>Transactions</span>
            </a>
            <a href="<?php echo SITE_URL; ?>/admin/transaction-generator" class="menu-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/transaction-generator') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-history"></i>
                <span>Transaction Generator</span>
            </a>
            <a href="<?php echo SITE_URL; ?>/admin/kyc" class="menu-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/kyc') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-id-card"></i>
                <span>KYC Verification</span>
            </a>
            <a href="<?php echo SITE_URL; ?>/admin/cards" class="menu-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/cards') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-credit-card"></i>
                <span>Card Applications</span>
            </a>
            <a href="<?php echo SITE_URL; ?>/admin/loans" class="menu-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/loans') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-hand-holding-usd"></i>
                <span>Loan Applications</span>
            </a>
            <a href="<?php echo SITE_URL; ?>/admin/investments" class="menu-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/investments') !== false || (strpos($_SERVER['REQUEST_URI'], '/admin/investment') !== false && strpos($_SERVER['REQUEST_URI'], '/admin/investment-funding') === false)) ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i>
                <span>Investments</span>
            </a>
            <a href="<?php echo SITE_URL; ?>/admin/investment-funding" class="menu-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/investment-funding') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-money-bill-wave"></i>
                <span>Crypto Funding</span>
            </a>
            <a href="<?php echo SITE_URL; ?>/admin/branding" class="menu-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/branding') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-palette"></i>
                <span>Branding</span>
            </a>
            <a href="<?php echo SITE_URL; ?>/admin/system-settings" class="menu-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/system-settings') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-sliders-h"></i>
                <span>System Settings</span>
            </a>
            <a href="<?php echo SITE_URL; ?>/admin/settings" class="menu-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/settings') !== false && strpos($_SERVER['REQUEST_URI'], '/admin/system-settings') === false) ? 'active' : ''; ?>">
                <i class="fas fa-cog"></i>
                <span>Transfer Settings</span>
            </a>
            <a href="<?php echo SITE_URL; ?>/admin/admin-settings" class="menu-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/admin-settings') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-user-shield"></i>
                <span>Admin Settings</span>
            </a>
            <a href="<?php echo SITE_URL; ?>/admin/email" class="menu-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/email') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-envelope"></i>
                <span>Email</span>
            </a>
            <a href="<?php echo SITE_URL; ?>/admin/banks" class="menu-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/banks') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-university"></i>
                <span>Banks</span>
            </a>
            <a href="<?php echo SITE_URL; ?>/admin/currencies" class="menu-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/currencies') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-dollar-sign"></i>
                <span>Currencies</span>
            </a>
            <a href="<?php echo SITE_URL; ?>/admin/crypto-wallets" class="menu-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/crypto-wallets') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-coins"></i>
                <span>Crypto Wallets</span>
            </a>
            <a href="<?php echo SITE_URL; ?>/admin/version-control" class="menu-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/version-control') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-code-branch"></i>
                <span>Version Control</span>
            </a>
        </div>

        <!-- User Section at Bottom -->
        <div class="user-section">
            <div class="user-profile">
                <div class="user-avatar"><?php echo isset($_SESSION['user_name']) ? strtoupper(substr($_SESSION['user_name'], 0, 2)) : 'AD'; ?></div>
                <div class="user-info">
                    <div class="user-name"><?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Administrator'; ?></div>
                    <div class="user-email"><?php echo isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : 'admin@example.com'; ?></div>
                </div>
            </div>
            
            <div class="user-actions">
                <a href="<?php echo SITE_URL; ?>/auth/logout" class="user-action">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Overlay for mobile -->
    <div class="overlay" id="overlay"></div>

    <!-- Main Content Area -->
    <div class="main-content-area">
        <!-- Top Header -->
        <div class="top-header">
            <div class="header-left">
                <button class="mobile-menu-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <div class="header-actions">
                <div class="header-icon">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="user-profile-header">
                    <div class="user-avatar-header">
                        <?php echo isset($_SESSION['user_name']) ? strtoupper(substr($_SESSION['user_name'], 0, 1)) : 'A'; ?>
                    </div>
                    <span class="user-name-header"><?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Administrator'; ?></span>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">

<script>
// Optimized toggle sidebar function for admin - MUST be in global scope
window.toggleSidebar = function() {
    const sidebar = document.getElementById('sidebar');
    if (!sidebar) {
        console.error('Sidebar not found');
        return;
    }
    
    const toggleIcon = sidebar.querySelector('.sidebar-toggle i');
    const isMobile = window.innerWidth <= 768;
    const overlay = document.getElementById('overlay');
    
    // Use requestAnimationFrame for smoother animation
    requestAnimationFrame(() => {
        sidebar.classList.toggle('expanded');
        
        // Update icon with optimized class manipulation
        if (toggleIcon) {
            toggleIcon.className = sidebar.classList.contains('expanded') 
                ? 'fas fa-chevron-left' 
                : 'fas fa-chevron-right';
        }
        
        // Handle mobile overlay with optimized checks
        if (isMobile) {
            sidebar.classList.toggle('mobile-open');
            if (overlay) {
                overlay.classList.toggle('active');
            }
            // Prevent body scrolling when sidebar is open
            document.body.style.overflow = sidebar.classList.contains('mobile-open') ? 'hidden' : '';
        }
    });
};

// Optimized event listeners with debouncing
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const mainContentArea = document.querySelector('.main-content-area');
    const overlay = document.getElementById('overlay');
    
    // Close sidebar when clicking outside on desktop (not mobile)
    document.addEventListener('click', function(event) {
        const isMobile = window.innerWidth <= 768;
        
        // Only handle desktop clicks (not mobile)
        if (!isMobile && sidebar && mainContentArea) {
            const sidebarToggle = sidebar.querySelector('.sidebar-toggle');
            const isClickInsideSidebar = sidebar.contains(event.target);
            const isClickOnToggle = sidebarToggle && sidebarToggle.contains(event.target);
            const isSidebarExpanded = sidebar.classList.contains('expanded');
            
            // If sidebar is expanded and click is outside sidebar (and not on toggle button), close it
            if (isSidebarExpanded && !isClickInsideSidebar && !isClickOnToggle) {
                sidebar.classList.remove('expanded');
                const toggleIcon = sidebar.querySelector('.sidebar-toggle i');
                if (toggleIcon) {
                    toggleIcon.className = 'fas fa-chevron-right';
                }
            }
        }
    });
    
    // Close sidebar when clicking overlay on mobile
    if (overlay) {
        overlay.addEventListener('click', function() {
            const isMobile = window.innerWidth <= 768;
            if (isMobile && sidebar) {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    }
    
    // Debounced resize handler for better performance
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            if (sidebar && overlay) {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('mobile-open');
                    if (overlay) {
                        overlay.classList.remove('active');
                    }
                    document.body.style.overflow = '';
                }
            }
        }, 100); // Debounce resize events
    });
});
</script>

