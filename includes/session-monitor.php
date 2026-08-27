<?php
/**
 * Client-side idle session monitor for logged-in app pages.
 * Keeps idle logout without patching fetch/XHR (safe for dashboard charts and admin tools).
 */
if (!function_exists('isLoggedIn') || !isLoggedIn()) {
    return;
}
?>
<script>
(function () {
    var SESSION_MS = <?php echo (int)SESSION_LIFETIME * 1000; ?>;
    var LOGIN_URL = <?php echo json_encode(SITE_URL . '/auth/login?timeout=1'); ?>;
    var lastActivity = Date.now();
    var expired = false;

    function redirectToLogin() {
        if (expired) {
            return;
        }
        expired = true;
        window.location.replace(LOGIN_URL);
    }

    function bumpActivity() {
        lastActivity = Date.now();
    }

    ['mousedown', 'keydown', 'scroll', 'touchstart', 'click'].forEach(function (evt) {
        document.addEventListener(evt, bumpActivity, { passive: true });
    });

    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'visible' && (Date.now() - lastActivity) >= SESSION_MS) {
            redirectToLogin();
        }
    });

    setInterval(function () {
        if ((Date.now() - lastActivity) >= SESSION_MS) {
            redirectToLogin();
        }
    }, 15000);

    try {
        var url = new URL(window.location.href);
        if (url.searchParams.has('logged_in') || url.searchParams.has('login')) {
            url.searchParams.delete('logged_in');
            url.searchParams.delete('login');
            window.history.replaceState({}, '', url.toString());
        }
        sessionStorage.removeItem('justLoggedIn');
    } catch (e) {}
})();
</script>
