        </div>
    </div>
</div>

<!-- Mobile Bottom Navigation (Your Preferred Design) - HIDDEN ON DESKTOP AND ADMIN PAGES -->
<?php 
// Only show mobile nav for regular users, not admins
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin'): 
?>
<nav class="mobile-bottom-nav" role="navigation" aria-label="Primary">
    <!-- 1. Home button -->
    <a href="<?php echo SITE_URL; ?>/dashboard" class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false) ? 'active' : ''; ?>" aria-label="Home">
        <i class="fas fa-home"></i>
    </a>

    <!-- 2. Transfer button -->
    <button class="fab nav-item" aria-label="Transfer" onclick="location.href='<?php echo SITE_URL; ?>/transfer'">
        <i class="fas fa-exchange-alt"></i>
    </button>

    <!-- 3. Menu/Toggle button (Last) -->
    <a href="#" class="nav-item" aria-label="Menu" onclick="event.preventDefault(); toggleMobileSidebar();">
        <i class="fas fa-bars"></i>
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

    // Set active state for mobile nav items
    document.querySelectorAll('.nav-item').forEach(item => {
        item.addEventListener('click', function() {
            document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
            this.classList.add('active');
        });
    });
</script>

<?php include __DIR__ . '/session-monitor.php'; ?>

</body>
</html>
