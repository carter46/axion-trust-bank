<?php
/**
 * LIVE CHAT WIDGET
 * 
 * Shows on: Homepage, Contact page, and Help Center page only
 * 
 * To change the live chat script:
 * 1. Open this file: includes/livechat.php
 * 2. Replace the script below with your new live chat code
 * 3. Upload this file to your server
 * 
 * Supported widgets: Smartsupp, Tawk.to, Crisp, Intercom, LiveChat, etc.
 */

// Simple page detection - show only on homepage/help pages, plus customer dashboard
$currentRoute = isset($_GET['route']) ? trim($_GET['route'], '/') : '';
$requestUri = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');

// Allowed pages
$allowedPages = ['', 'home', 'help-center', 'help'];

// Check if current page is allowed
$showChat = false;

// Check route parameter (for index.php?route=contact)
if (in_array($currentRoute, $allowedPages)) {
    $showChat = true;
}

// Allow customer dashboard only (never admin)
if (!$showChat) {
    $isDashboard = ($currentRoute === 'dashboard' || $requestUri === 'dashboard');
    $isTransfer = ($currentRoute === 'transfer' || $requestUri === 'transfer' || strpos($requestUri, 'transfer/') === 0);
    $isLoggedIn = function_exists('isLoggedIn')
        ? isLoggedIn()
        : (isset($_SESSION['user_id']) && !empty($_SESSION['user_id']));
    $role = $_SESSION['user_role'] ?? null;
    $isStaff = ($role === 'admin' || $role === 'support' || !empty($_SESSION['is_super_admin'] ?? 0));

    if (($isDashboard || $isTransfer) && $isLoggedIn && !$isStaff) {
        $showChat = true;
    }
}

// Check direct file access (for contact.php, help-center.php, etc.)
if (!$showChat) {
    $fileName = str_replace('.php', '', basename($requestUri));
    if (in_array($fileName, $allowedPages) || $requestUri === '') {
        $showChat = true;
    }
}

// Only show chat widget on allowed pages
if (!$showChat) {
    return;
}

// Smartsupp Live Chat script
// Replace the key below with your actual Smartsupp key
?>
<style>
  /* Keep chat visible above transfer modals/overlays */
  #smartsupp-widget-container,
  .smartsupp-widget-container,
  iframe[src*="smartsupp"] {
    z-index: 2147483647 !important;
  }

  /* On mobile, keep widget above bottom nav */
  @media (max-width: 768px) {
    #smartsupp-widget-container,
    .smartsupp-widget-container {
      bottom: 98px !important;
      right: 12px !important;
    }
  }
</style>
<script type="text/javascript">
var _smartsupp = _smartsupp || {};
_smartsupp.key = '491024ecf02a08dfbbb271f4791209e3ecb591c1';
window.smartsupp||(function(d) {
  var s,c,o=smartsupp=function(){ o._.push(arguments)};o._=[];
  s=d.getElementsByTagName('script')[0];c=d.createElement('script');
  c.type='text/javascript';c.charset='utf-8';c.async=true;
  c.src='https://www.smartsuppchat.com/loader.js?';s.parentNode.insertBefore(c,s);
})(document);
</script>
<noscript> Powered by <a href="https://www.smartsupp.com" target="_blank">Smartsupp</a></noscript>
