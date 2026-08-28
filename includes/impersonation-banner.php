<?php
/**
 * Admin impersonation top bar — fixed above the header/sidebar (not floating over them).
 */
if (empty($_SESSION['admin_impersonating'])) {
    return;
}

$userName = htmlspecialchars((string)($_SESSION['user_name'] ?? 'User'), ENT_QUOTES, 'UTF-8');
$adminName = htmlspecialchars((string)($_SESSION['admin_original_name'] ?? 'Admin'), ENT_QUOTES, 'UTF-8');
$switchBackUrl = htmlspecialchars(SITE_URL . '/admin/stop-impersonating', ENT_QUOTES, 'UTF-8');
?>
<div id="adminImpersonationBanner" class="admin-impersonation-banner" role="status" aria-live="polite">
    <div class="admin-impersonation-banner__inner">
        <div class="admin-impersonation-banner__text">
            <i class="fas fa-user-shield" aria-hidden="true"></i>
            <span>You are logged in as <strong><?php echo $userName; ?></strong> (Admin: <?php echo $adminName; ?>)</span>
        </div>
        <a class="admin-impersonation-banner__btn" href="<?php echo $switchBackUrl; ?>">
            <i class="fas fa-arrow-left" aria-hidden="true"></i>
            Switch Back to Admin
        </a>
    </div>
</div>
<style>
:root {
    --admin-impersonation-banner-h: 0px;
}
.admin-impersonation-banner {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 30000;
    width: 100%;
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: #fff;
    padding: 10px 16px;
    box-sizing: border-box;
    box-shadow: none;
}
.admin-impersonation-banner__inner {
    max-width: 1400px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px 16px;
    flex-wrap: wrap;
}
.admin-impersonation-banner__text {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
    font-size: 14px;
    line-height: 1.4;
}
.admin-impersonation-banner__btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255, 255, 255, 0.2);
    color: #fff !important;
    padding: 8px 14px;
    border-radius: 8px;
    text-decoration: none !important;
    font-weight: 700;
    font-size: 13px;
    border: 1px solid rgba(255, 255, 255, 0.35);
    white-space: nowrap;
}
.admin-impersonation-banner__btn:hover {
    background: rgba(255, 255, 255, 0.32);
}
/* Push page chrome below the top bar so it never covers the header/sidebar */
body.has-admin-impersonation-banner {
    padding-top: var(--admin-impersonation-banner-h) !important;
}
body.has-admin-impersonation-banner .sidebar {
    top: var(--admin-impersonation-banner-h) !important;
    height: calc(100vh - var(--admin-impersonation-banner-h)) !important;
}
body.has-admin-impersonation-banner .header {
    top: var(--admin-impersonation-banner-h) !important;
}
@media (max-width: 768px) {
    .admin-impersonation-banner__text {
        font-size: 13px;
    }
    .admin-impersonation-banner__btn {
        width: 100%;
        justify-content: center;
    }
}
</style>
<script>
(function () {
    function syncImpersonationBannerOffset() {
        var banner = document.getElementById('adminImpersonationBanner');
        if (!banner) return;
        document.body.classList.add('has-admin-impersonation-banner');
        var height = Math.ceil(banner.getBoundingClientRect().height);
        document.documentElement.style.setProperty('--admin-impersonation-banner-h', height + 'px');
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', syncImpersonationBannerOffset);
    } else {
        syncImpersonationBannerOffset();
    }
    window.addEventListener('resize', syncImpersonationBannerOffset);
})();
</script>
