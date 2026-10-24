        </div>
    </div>
</div>

<!-- Mobile Bottom Navigation - HIDDEN ON DESKTOP AND ADMIN PAGES -->
<?php 
// Only show mobile nav for regular users, not admins
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin'):
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    $isHomeActive = strpos($requestUri, '/dashboard') !== false;
    $isCardActive = strpos($requestUri, '/card') !== false;
    $isTransferActive = strpos($requestUri, '/transfer') !== false;
    $isProfileActive = strpos($requestUri, '/profile') !== false;
?>
<nav class="mobile-bottom-nav" role="navigation" aria-label="Primary">
    <a href="<?php echo SITE_URL; ?>/dashboard" class="nav-item <?php echo $isHomeActive ? 'active' : ''; ?>" aria-label="Home">
        <i class="fas fa-home"></i>
        <span class="nav-label">Home</span>
    </a>

    <a href="<?php echo SITE_URL; ?>/card" class="nav-item <?php echo $isCardActive ? 'active' : ''; ?>" aria-label="Cards">
        <i class="fas fa-credit-card"></i>
        <span class="nav-label">Card</span>
    </a>

    <a href="<?php echo SITE_URL; ?>/transfer" class="nav-item fab nav-transfer <?php echo $isTransferActive ? 'active' : ''; ?>" aria-label="Transfer">
        <span class="fab-circle"><i class="fas fa-exchange-alt"></i></span>
        <span class="nav-label">Transfer</span>
    </a>

    <a href="<?php echo SITE_URL; ?>/profile" class="nav-item <?php echo $isProfileActive ? 'active' : ''; ?>" aria-label="Profile">
        <i class="fas fa-user"></i>
        <span class="nav-label">Profile</span>
    </a>

    <a href="#" class="nav-item" aria-label="Menu" onclick="event.preventDefault(); toggleMobileSidebar();">
        <i class="fas fa-bars"></i>
        <span class="nav-label">Menu</span>
    </a>
</nav>
<?php endif; ?>

<script>
    // Toggle sidebar on desktop
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const toggleIcon = sidebar.querySelector('.sidebar-toggle i');
        
        sidebar.classList.toggle('expanded');
        
        if (sidebar.classList.contains('expanded')) {
            toggleIcon.classList.remove('fa-chevron-right');
            toggleIcon.classList.add('fa-chevron-left');
        } else {
            toggleIcon.classList.remove('fa-chevron-left');
            toggleIcon.classList.add('fa-chevron-right');
        }
    }

    // Toggle mobile sidebar - opens full sidebar directly
    function toggleMobileSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        
        sidebar.classList.toggle('mobile-open');
        overlay.classList.toggle('active');
        
        // Prevent body scrolling when sidebar is open
        document.body.style.overflow = sidebar.classList.contains('mobile-open') ? 'hidden' : '';
        
        // Force a reflow to ensure proper rendering
        void sidebar.offsetWidth;
    }

    // Close mobile sidebar when clicking overlay
    document.getElementById('overlay').addEventListener('click', function() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    });

    // Close mobile sidebar when clicking a menu item or user action
    document.querySelectorAll('.menu-item, .user-action').forEach(item => {
        item.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('overlay');
                
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    });

    // Handle window resize
    window.addEventListener('resize', function() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        
        if (window.innerWidth > 768) {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    });
</script>

<?php include __DIR__ . '/session-monitor.php'; ?>

</body>
</html>
