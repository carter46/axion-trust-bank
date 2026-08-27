<?php
/**
 * LIVE CHAT WIDGET LOADER (site-neutral)
 *
 * This file never contains a chat provider key or embed.
 * Each website configures its own script in:
 *   Admin → System Settings → Other → Live Chat Script
 *
 * If that setting is empty, no widget is shown.
 *
 * Allowed pages: home, contact, help center, customer dashboard / transfer.
 */

// Simple page detection
$currentRoute = isset($_GET['route']) ? trim($_GET['route'], '/') : '';
$requestUri = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');

$allowedPages = ['', 'home', 'contact', 'help-center', 'help'];

$showChat = false;

if (in_array($currentRoute, $allowedPages, true)) {
    $showChat = true;
}

// Allow customer dashboard / transfer only (never admin)
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

// Direct file access (contact.php, help-center.php, etc.)
if (!$showChat) {
    $fileName = str_replace('.php', '', basename($requestUri));
    if (in_array($fileName, $allowedPages, true) || $requestUri === '') {
        $showChat = true;
    }
}

if (!$showChat) {
    return;
}

// Load script from system settings only (per-site DB value)
$chatScript = '';
try {
    if (!class_exists('SystemSettings') && is_file(__DIR__ . '/system-settings.php')) {
        require_once __DIR__ . '/system-settings.php';
    }
    if (class_exists('SystemSettings')) {
        $chatScript = trim((string)SystemSettings::getInstance()->get('live_chat_script', ''));
    }
} catch (Throwable $e) {
    error_log('livechat.php: failed to load live_chat_script — ' . $e->getMessage());
    $chatScript = '';
}

if ($chatScript === '') {
    return;
}

// Layout helpers only — do not embed any provider script here
?>
<style>
  #smartsupp-widget-container,
  .smartsupp-widget-container,
  iframe[src*="smartsupp"],
  iframe[src*="tawk"],
  iframe[src*="crisp"],
  #tawkchat-container,
  .crisp-client {
    z-index: 2147483647 !important;
  }

  @media (max-width: 768px) {
    #smartsupp-widget-container,
    .smartsupp-widget-container,
    #tawkchat-container,
    .crisp-client {
      bottom: 98px !important;
      right: 12px !important;
    }
  }
</style>
<?php
// Unescaped on purpose — admin-pasted provider embed for THIS site only
echo $chatScript;
