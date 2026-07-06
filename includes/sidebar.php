<div class="dashboard-container">
    <!-- Collapsible Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo"><?php echo getSiteName(); ?></div>
            <button class="sidebar-toggle" onclick="toggleSidebar()">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
        
        <div class="sidebar-menu">
            <a href="<?php echo SITE_URL; ?>/dashboard" class="menu-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="<?php echo SITE_URL; ?>/account" class="menu-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/account') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-wallet"></i>
                <span>Accounts</span>
            </a>
            <a href="<?php echo SITE_URL; ?>/transfer" class="menu-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/transfer') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-exchange-alt"></i>
                <span>Transfer</span>
            </a>
            <a href="<?php echo SITE_URL; ?>/transaction" class="menu-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/transaction') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-history"></i>
                <span>Transactions</span>
            </a>
            <a href="<?php echo SITE_URL; ?>/card" class="menu-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/card') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-credit-card"></i>
                <span>Cards</span>
            </a>
            <a href="<?php echo SITE_URL; ?>/loan" class="menu-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/loan') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-hand-holding-usd"></i>
                <span>Loans</span>
            </a>
            <a href="<?php echo SITE_URL; ?>/investment" class="menu-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/investment') !== false && strpos($_SERVER['REQUEST_URI'], '/investments') === false) ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i>
                <span>Investments</span>
            </a>
            <a href="<?php echo SITE_URL; ?>/profile" class="menu-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/profile') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-user"></i>
                <span>Profile</span>
            </a>
            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
            <a href="<?php echo SITE_URL; ?>/admin" class="menu-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-cog"></i>
                <span>Admin</span>
            </a>
            <?php endif; ?>
        </div>

        <!-- User Section at Bottom -->
        <div class="user-section">
            <div class="user-profile">
                <?php if (isset($_SESSION['user_photo']) && !empty($_SESSION['user_photo']) && file_exists(BASE_PATH . $_SESSION['user_photo'])): ?>
                    <img src="<?php echo htmlspecialchars($_SESSION['user_photo']); ?>" alt="Profile" class="user-avatar user-avatar-img">
                <?php else: ?>
                    <div class="user-avatar"><?php echo isset($_SESSION['user_name']) ? strtoupper(substr($_SESSION['user_name'], 0, 2)) : 'JD'; ?></div>
                <?php endif; ?>
                <div class="user-info">
                    <div class="user-name"><?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'John Doe'; ?></div>
                    <div class="user-email"><?php echo isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : 'john.doe@example.com'; ?></div>
                </div>
            </div>
            
            <div class="user-actions">
                <a href="<?php echo SITE_URL; ?>/help-center" class="user-action">
                    <i class="fas fa-question-circle"></i>
                    <span>Help Center</span>
                </a>
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
            <!-- Language button (replaces logo) -->
            <div class="header-logo">
                <a class="lang-settings-btn" href="<?php echo SITE_URL; ?>/profile/settings#language" aria-label="Language">
                    <i class="fas fa-globe"></i>
                    <span>Language</span>
                </a>
            </div>
            
            <div class="header-actions">
                <!-- Notification Bell -->
                <div class="notification-container">
                    <div class="notification-bell" onclick="toggleNotificationDropdown()">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge" id="notificationBadge">0</span>
                    </div>
                    
                    <!-- Notification Dropdown -->
                    <div class="notification-dropdown" id="notificationDropdown">
                        <div class="notification-header">
                            <h4>Notifications</h4>
                            <a href="<?php echo SITE_URL; ?>/profile/notifications" class="view-all-link">View All</a>
                        </div>
                        <div class="notification-list" id="notificationList">
                            <div class="notification-loading">Loading notifications...</div>
                        </div>
                    </div>
                </div>
                
                <!-- User Profile Dropdown -->
                <div class="user-profile-container">
                    <div class="user-profile-header" onclick="toggleUserDropdown()">
                        <?php if (isset($_SESSION['user_photo']) && !empty($_SESSION['user_photo']) && file_exists(BASE_PATH . $_SESSION['user_photo'])): ?>
                            <img src="<?php echo htmlspecialchars($_SESSION['user_photo']); ?>" alt="Profile" class="user-avatar-header user-avatar-img-header">
                        <?php else: ?>
                            <div class="user-avatar-header">
                                <?php echo isset($_SESSION['user_name']) ? strtoupper(substr($_SESSION['user_name'], 0, 1)) : 'U'; ?>
                            </div>
                        <?php endif; ?>
                        <span class="user-name-header"><?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'User Name'; ?></span>
                        <i class="fas fa-chevron-down dropdown-arrow"></i>
                    </div>
                    
                    <!-- User Dropdown Menu -->
                    <div class="user-dropdown" id="userDropdown">
                        <a href="<?php echo SITE_URL; ?>/profile" class="dropdown-item">
                            <i class="fas fa-user"></i>
                            <span>Profile</span>
                        </a>
                        <a href="<?php echo SITE_URL; ?>/profile/notifications" class="dropdown-item">
                            <i class="fas fa-bell"></i>
                            <span>Notifications</span>
                        </a>
                        <a href="<?php echo SITE_URL; ?>/profile/security" class="dropdown-item">
                            <i class="fas fa-shield-alt"></i>
                            <span>Security</span>
                        </a>
                        <a href="<?php echo SITE_URL; ?>/profile/settings" class="dropdown-item">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span>
                        </a>
                        <hr class="dropdown-divider">
                        <a href="<?php echo SITE_URL; ?>/auth/logout" class="dropdown-item logout-item">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">

<style>
/* Header Dropdown Styles */
.top-header {
    background: white;
    border-bottom: 1px solid #e5e7eb;
    padding: 16px 24px;
    display: flex;
    justify-content: flex-end;
    align-items: center;
    position: relative;
    z-index: 1000;
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 20px;
}

/* Language button (match app button style) */
.lang-settings-btn {
    background: #032B44;
    color: white;
    border: none;
    border-radius: 12px;
    padding: 12px 18px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    text-decoration: none;
    white-space: nowrap;
    line-height: 1;
}

.lang-settings-btn:hover {
    background: #024a6b;
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .lang-settings-btn span { display: none; }
    .lang-settings-btn { padding: 12px 14px; }
}

/* Notification Container */
.notification-container {
    position: relative;
}

.notification-bell {
    position: relative;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
}

.notification-bell:hover {
    background: #f1f5f9;
    border-color: #3b82f6;
}

.notification-bell i {
    font-size: 16px;
    color: #6b7280;
}

.notification-badge {
    position: absolute;
    top: -2px;
    right: -2px;
    background: #ef4444;
    color: white;
    font-size: 10px;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 10px;
    min-width: 16px;
    text-align: center;
    border: 2px solid white;
}

.notification-dropdown {
    position: absolute;
    top: 50px;
    right: 0;
    width: 350px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    z-index: 1001;
    display: none;
    max-height: 400px;
    overflow: hidden;
}

.notification-dropdown.show {
    display: block;
}

.notification-header {
    padding: 16px 20px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.notification-header h4 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: #1f2937;
}

.view-all-link {
    font-size: 14px;
    color: #3b82f6;
    text-decoration: none;
    font-weight: 500;
}

.view-all-link:hover {
    color: #2563eb;
}

.notification-list {
    max-height: 300px;
    overflow-y: auto;
}

.notification-item {
    padding: 16px 20px;
    border-bottom: 1px solid #f3f4f6;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    transition: background 0.2s;
}

.notification-item:hover {
    background: #f8fafc;
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    color: white;
    flex-shrink: 0;
}

.notification-icon.credit { background: #10b981; }
.notification-icon.debit { background: #ef4444; }
.notification-icon.security { background: #f59e0b; }
.notification-icon.kyc { background: #3b82f6; }
.notification-icon.loan { background: #8b5cf6; }
.notification-icon.card { background: #06b6d4; }
.notification-icon.general { background: #6b7280; }

.notification-content {
    flex: 1;
}

.notification-title {
    font-size: 14px;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 4px 0;
}

.notification-message {
    font-size: 13px;
    color: #6b7280;
    margin: 0 0 4px 0;
    line-height: 1.4;
}

.notification-time {
    font-size: 12px;
    color: #9ca3af;
}

.notification-loading {
    padding: 20px;
    text-align: center;
    color: #6b7280;
    font-size: 14px;
}

/* User Profile Container */
.user-profile-container {
    position: relative;
}

.user-profile-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px 12px;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.2s;
}

.user-profile-header:hover {
    background: #f8fafc;
}

.user-avatar-header {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
}

.user-avatar-img-header {
    object-fit: cover;
    background: none;
}

.user-name-header {
    font-size: 14px;
    font-weight: 600;
    color: #1f2937;
}

.dropdown-arrow {
    font-size: 12px;
    color: #6b7280;
    transition: transform 0.2s;
}

.user-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    width: 200px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    z-index: 1001;
    display: none;
    overflow: hidden;
    margin-top: 8px;
}

.user-dropdown.show {
    display: block;
}

.dropdown-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    color: #374151;
    text-decoration: none;
    font-size: 14px;
    transition: background 0.2s;
}

.dropdown-item:hover {
    background: #f8fafc;
    color: #1f2937;
}

.dropdown-item i {
    width: 16px;
    text-align: center;
    color: #6b7280;
}

.dropdown-divider {
    margin: 8px 0;
    border: none;
    border-top: 1px solid #e5e7eb;
}

.logout-item {
    color: #ef4444;
}

.logout-item:hover {
    background: #fef2f2;
    color: #dc2626;
}

.logout-item i {
    color: #ef4444;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .top-header {
        padding: 12px 16px;
    }
    
    .header-actions {
        gap: 16px;
    }
    
    .notification-dropdown {
        width: 300px;
        right: -50px;
    }
    
    .user-dropdown {
        width: 180px;
    }
    
    .user-name-header {
        display: none;
    }
    
    .dropdown-arrow {
        display: none;
    }
}
</style>

<script>
// Dropdown functionality
function toggleUserDropdown() {
    const dropdown = document.getElementById('userDropdown');
    const notificationDropdown = document.getElementById('notificationDropdown');
    
    // Close notification dropdown if open
    notificationDropdown.classList.remove('show');
    
    // Toggle user dropdown
    dropdown.classList.toggle('show');
}

function toggleNotificationDropdown() {
    const dropdown = document.getElementById('notificationDropdown');
    const userDropdown = document.getElementById('userDropdown');
    
    // Close user dropdown if open
    userDropdown.classList.remove('show');
    
    // Toggle notification dropdown
    dropdown.classList.toggle('show');
    
    // Load notifications if dropdown is opening
    if (dropdown.classList.contains('show')) {
        loadNotifications();
    }
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    const userContainer = document.querySelector('.user-profile-container');
    const notificationContainer = document.querySelector('.notification-container');
    
    if (!userContainer.contains(event.target)) {
        document.getElementById('userDropdown').classList.remove('show');
    }
    
    if (!notificationContainer.contains(event.target)) {
        document.getElementById('notificationDropdown').classList.remove('show');
    }
});

// Load notifications
function loadNotifications() {
    const notificationList = document.getElementById('notificationList');
    const notificationBadge = document.getElementById('notificationBadge');
    
    fetch('/api/get-notifications.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const notifications = data.notifications;
                const unreadCount = notifications.filter(n => !n.is_read).length;
                
                // Update badge
                notificationBadge.textContent = unreadCount;
                notificationBadge.style.display = unreadCount > 0 ? 'block' : 'none';
                
                // Update notification list
                if (notifications.length === 0) {
                    notificationList.innerHTML = '<div class="notification-loading">No notifications</div>';
                } else {
                    notificationList.innerHTML = notifications.map(notification => `
                        <div class="notification-item" onclick="handleNotificationClick(${notification.id}, '${notification.type}', '${notification.link || ''}')">
                            <div class="notification-icon ${notification.type}">
                                ${getNotificationIcon(notification.type)}
                            </div>
                            <div class="notification-content">
                                <div class="notification-title">${notification.title}</div>
                                <div class="notification-message">${notification.message}</div>
                                <div class="notification-time">${formatTime(notification.created_at)}</div>
                            </div>
                        </div>
                    `).join('');
                }
            } else {
                notificationList.innerHTML = '<div class="notification-loading">Error loading notifications</div>';
            }
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
            notificationList.innerHTML = '<div class="notification-loading">Error loading notifications</div>';
        });
}

// Get notification icon based on type
function getNotificationIcon(type) {
    const icons = {
        'credit': '<i class="fas fa-arrow-up"></i>',
        'debit': '<i class="fas fa-arrow-down"></i>',
        'security': '<i class="fas fa-shield-alt"></i>',
        'kyc': '<i class="fas fa-id-card"></i>',
        'loan': '<i class="fas fa-hand-holding-usd"></i>',
        'card': '<i class="fas fa-credit-card"></i>',
        'general': '<i class="fas fa-info-circle"></i>'
    };
    return icons[type] || icons.general;
}

// Format time
function formatTime(timestamp) {
    const now = new Date();
    const time = new Date(timestamp);
    const diff = now - time;
    
    if (diff < 60000) return 'Just now';
    if (diff < 3600000) return Math.floor(diff / 60000) + 'm ago';
    if (diff < 86400000) return Math.floor(diff / 3600000) + 'h ago';
    return Math.floor(diff / 86400000) + 'd ago';
}

// Handle notification click
function handleNotificationClick(notificationId, type, link) {
    // Mark as read first
    markAsRead(notificationId);
    
    // Close the dropdown
    document.getElementById('notificationDropdown').classList.remove('show');
    
    // Navigate based on notification type or link
    let redirectUrl = '';
    
    if (link) {
        redirectUrl = link;
    } else {
        switch(type) {
            case 'credit':
            case 'debit':
                redirectUrl = '<?php echo SITE_URL; ?>/transactions';
                break;
            case 'security':
                redirectUrl = '<?php echo SITE_URL; ?>/profile/security';
                break;
            case 'kyc':
                redirectUrl = '<?php echo SITE_URL; ?>/profile';
                break;
            case 'loan':
                redirectUrl = '<?php echo SITE_URL; ?>/loans';
                break;
            case 'card':
                redirectUrl = '<?php echo SITE_URL; ?>/cards';
                break;
            default:
                redirectUrl = '<?php echo SITE_URL; ?>/profile';
                break;
        }
    }
    
    // Navigate to the appropriate page
    if (redirectUrl) {
        window.location.href = redirectUrl;
    }
}

// Mark notification as read
function markAsRead(notificationId) {
    fetch('/api/mark-notification-read.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ notification_id: notificationId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload notifications to update badge count
            loadNotifications();
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
    });
}

// Load notifications on page load
document.addEventListener('DOMContentLoaded', function() {
    loadNotifications();
});

// Toggle sidebar function for user dashboard
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const toggleIcon = sidebar.querySelector('.sidebar-toggle i');
    
    // Use requestAnimationFrame for smoother animation
    requestAnimationFrame(() => {
        sidebar.classList.toggle('expanded');
        
        // Update icon
        if (toggleIcon) {
            toggleIcon.className = sidebar.classList.contains('expanded') 
                ? 'fas fa-chevron-left' 
                : 'fas fa-chevron-right';
        }
        
        // Handle mobile overlay
        const isMobile = window.innerWidth <= 768;
        const overlay = document.getElementById('overlay');
        
        if (isMobile) {
            sidebar.classList.toggle('mobile-open');
            if (overlay) {
                overlay.classList.toggle('active');
            }
            // Prevent body scrolling when sidebar is open
            document.body.style.overflow = sidebar.classList.contains('mobile-open') ? 'hidden' : '';
        }
    });
}

// Close sidebar when clicking outside on desktop (not mobile)
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const mainContentArea = document.querySelector('.main-content-area');
    
    // Close sidebar when clicking outside on desktop
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
    const overlay = document.getElementById('overlay');
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
    
    // Close sidebar when clicking menu items on mobile
    const menuItems = document.querySelectorAll('.menu-item, .user-action');
    menuItems.forEach(item => {
        item.addEventListener('click', function() {
            const isMobile = window.innerWidth <= 768;
            if (isMobile && sidebar && overlay) {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    });
    
    // Handle window resize
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            if (sidebar && overlay) {
                if (window.innerWidth > 768) {
                    // On desktop, remove mobile-open but keep expanded state
                    sidebar.classList.remove('mobile-open');
                    overlay.classList.remove('active');
                    document.body.style.overflow = '';
                }
            }
        }, 100);
    });
});
</script>
