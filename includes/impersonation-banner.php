<?php
/**
 * Admin impersonation banner — shown on app pages and public pages while
 * an admin is logged in as a customer.
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
.admin-impersonation-banner {
    position: sticky;
    top: 0;
    z-index: 20060;
    width: 100%;
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: #fff;
    padding: 12px 16px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.18);
    box-sizing: border-box;
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
body.has-admin-impersonation-banner .main-content-area,
body.has-admin-impersonation-banner .dashboard-container {
    /* Banner is sticky at top of flow; no extra offset needed */
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
document.addEventListener('DOMContentLoaded', function () {
    document.body.classList.add('has-admin-impersonation-banner');
});
</script>
