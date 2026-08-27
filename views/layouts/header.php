<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes, viewport-fit=cover, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://www.google.com https://www.gstatic.com https://translate.google.com https://translate.googleapis.com https://*.googleapis.com https://*.smartsuppchat.com https://*.smartsuppcdn.com https://cdn.gtranslate.net; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://www.gstatic.com https://cdnjs.cloudflare.com https://*.smartsuppcdn.com; font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com https://*.smartsuppcdn.com data:; img-src 'self' data: https: blob: https://*.smartsuppcdn.com; media-src 'self' https://assets.mixkit.co https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://*.smartsuppcdn.com blob:; connect-src 'self' https://api.exchangerate-api.com https://cdn.jsdelivr.net https://*.smartsupp.com https://*.smartsuppchat.com https://*.smartsuppcdn.com https://cdn77.org wss://*.smartsupp.com wss://*.smartsuppchat.com https://translate.google.com https://translate.googleapis.com https://*.googleapis.com; frame-src 'self' https://*.smartsupp.com https://*.smartsuppchat.com https://translate.google.com; base-uri 'self'; form-action 'self'; upgrade-insecure-requests;">
    
    <!-- Prevent caching of HTML pages during development -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    <?php
    // Get dynamic branding with error handling and debugging
    try {
        // Verify functions exist
        if (!function_exists('getSiteName')) {
            error_log("[Header Debug] getSiteName() function not found");
            $siteName = 'Cosmopolitan Trust Bank';
        } else {
            $siteName = getSiteName();
        }
        
        if (!function_exists('getSiteLogo')) {
            error_log("[Header Debug] getSiteLogo() function not found");
            $siteLogo = SITE_URL . '/assets/images/logo.svg';
        } else {
            $siteLogo = getSiteLogo();
        }
        
        if (!function_exists('getSiteInitials')) {
            error_log("[Header Debug] getSiteInitials() function not found");
            $siteInitials = 'CTB';
        } else {
            $siteInitials = getSiteInitials();
        }
        
        if (!function_exists('getSetting')) {
            error_log("[Header Debug] getSetting() function not found");
            $siteTagline = 'Bank Globally. Invest Confidently.';
        } else {
            $siteTagline = getSetting('site_tagline', 'Bank Globally. Invest Confidently.');
        }
        
        // Ensure values are not empty
        $siteName = !empty($siteName) ? $siteName : 'Cosmopolitan Trust Bank';
        $siteInitials = !empty($siteInitials) ? $siteInitials : 'CTB';
        $siteTagline = !empty($siteTagline) ? $siteTagline : 'Bank Globally. Invest Confidently.';
        
        // Debug logging (only in development - comment out in production)
        // error_log("[Header Debug] Site Name: " . $siteName . " | Initials: " . $siteInitials . " | Logo: " . substr($siteLogo, 0, 50));
    } catch (Exception $e) {
        error_log("[Header Debug] Critical error in branding: " . $e->getMessage());
        // Fallback values
        $siteName = 'Cosmopolitan Trust Bank';
        $siteLogo = SITE_URL . '/assets/images/logo.svg';
        $siteInitials = 'CTB';
        $siteTagline = 'Bank Globally. Invest Confidently.';
    }
    ?>
    <title><?php echo $pageTitle ?? $siteName; ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($siteName . ' – ' . $siteTagline); ?>">
    
    <!-- Dynamic Favicon - Supports all formats -->
    <?php 
    try {
    $faviconUrl = function_exists('getSiteFavicon') ? getSiteFavicon() : getSetting('site_favicon_url', SITE_URL . '/favicon.svg');
        if ($faviconUrl && !empty(trim($faviconUrl))) {
        echo '<link rel="icon" type="image/x-icon" href="' . htmlspecialchars($faviconUrl) . '">';
        echo '<link rel="shortcut icon" type="image/x-icon" href="' . htmlspecialchars($faviconUrl) . '">';
        // Also add apple-touch-icon for better mobile support
        echo '<link rel="apple-touch-icon" href="' . htmlspecialchars($faviconUrl) . '">';
    } else {
        // Fallback to default favicon
        echo '<link rel="icon" type="image/x-icon" href="' . SITE_URL . '/favicon.ico">';
        echo '<link rel="shortcut icon" type="image/x-icon" href="' . SITE_URL . '/favicon.ico">';
            echo '<link rel="apple-touch-icon" href="' . SITE_URL . '/favicon.ico">';
        }
    } catch (Exception $e) {
        // Fallback if getSetting fails
        echo '<link rel="icon" type="image/x-icon" href="' . SITE_URL . '/favicon.ico">';
        echo '<link rel="shortcut icon" type="image/x-icon" href="' . SITE_URL . '/favicon.ico">';
        echo '<link rel="apple-touch-icon" href="' . SITE_URL . '/favicon.ico">';
    }
    ?>
    
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css?v=<?php echo defined('ASSET_VERSION') ? ASSET_VERSION : time(); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Header Design - Exact Template Implementation */
        :root {
            --navy: #1a2b5f;
            --navy-light: #2a3b7f;
            --white: #ffffff;
            --text: #333333;
            --text-light: #666666;
            --transition: all 0.3s ease;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .header {
            /* Glass effect header background (matches PulseFit header feel) */
            background-color: rgba(255, 255, 255, 0.55) !important;
            backdrop-filter: blur(14px) saturate(130%);
            -webkit-backdrop-filter: blur(14px) saturate(130%);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06) !important;
            position: sticky !important;
            top: 0 !important;
            z-index: 1000 !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .nav-container {
            max-width: 1200px !important;
            margin: 0 auto !important;
            padding: 0 2rem !important; /* px-8 */
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            height: 80px !important; /* keep consistent with existing layout */
        }

        @media (min-width: 1024px) {
            .nav-container {
                padding: 0 4rem !important; /* px-16 */
            }
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* PulseFit-style header uses text logo; hide the icon so it matches. */
        .logo-icon {
            display: none;
        }

        .logo-text {
            font-size: 24px;
            font-weight: 700;
            color: #1a1a1a;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 32px; /* gap-8 */
            margin: 0;
            padding: 0;
        }

        .nav-menu li {
            margin: 0 !important;
            padding: 0 !important;
            list-style: none !important;
            position: relative;
        }

        .nav-link {
            text-decoration: none !important;
            color: #4a5568 !important;
            font-weight: 400 !important;
            padding: 0 !important;
            border-radius: 6px !important;
            transition: opacity 0.2s ease !important;
            position: relative !important;
            display: block !important;
            background: transparent !important;
        }

        .nav-link:hover {
            background-color: transparent !important;
            color: #4a5568 !important;
            opacity: 0.7 !important;
        }

        /* Dropdown Styles */
        .nav-item-dropdown {
            position: relative;
        }

        .nav-link.has-dropdown {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .nav-link.has-dropdown::after {
            content: '▼';
            font-size: 0.7rem;
            transition: var(--transition);
            opacity: 0.7;
        }

        .nav-link.has-dropdown:hover::after {
            opacity: 1;
            transform: rotate(180deg);
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            background-color: var(--white);
            min-width: 200px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border-radius: 6px;
            padding: 10px;
            margin-top: 8px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            z-index: 1000;
            list-style: none;
        }

        .nav-item-dropdown:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-menu li {
            margin: 0;
            padding: 0;
        }

        .dropdown-menu li + li {
            margin-top: 4px;
        }

        .dropdown-menu .nav-link {
            padding: 10px 14px;
            border-radius: 10px;
            color: var(--text);
            display: block;
            width: 100%;
            box-sizing: border-box;
        }

        .dropdown-menu .nav-link:hover {
            background-color: var(--navy);
            color: var(--white);
        }

        /* Public mobile sidebar — matches user/admin slide-in pattern */
        .public-mobile-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 10001;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .public-mobile-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .public-mobile-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 280px;
            max-width: 85vw;
            height: 100vh;
            height: 100dvh;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.95) 100%);
            border-right: 1px solid rgba(30, 58, 138, 0.08);
            box-shadow: 4px 0 24px rgba(30, 58, 138, 0.12);
            z-index: 10002;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .public-mobile-sidebar.open {
            transform: translateX(0);
        }

        .public-sidebar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px;
            border-bottom: 1px solid #e5e7eb;
            min-height: 80px;
            flex-shrink: 0;
        }

        .public-sidebar-logo {
            font-size: 18px;
            font-weight: 700;
            color: #1e3a8a;
            line-height: 1.3;
            padding-right: 12px;
        }

        .public-sidebar-close {
            background: none;
            border: none;
            color: #475569;
            font-size: 22px;
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            line-height: 1;
            flex-shrink: 0;
        }

        .public-sidebar-close:hover {
            background: rgba(30, 58, 138, 0.08);
            color: #1e3a8a;
        }

        .public-sidebar-menu {
            flex: 1;
            padding: 12px 0 24px;
            list-style: none;
            margin: 0;
        }

        .public-menu-group {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .public-menu-row {
            display: flex;
            align-items: stretch;
            margin: 4px 8px;
            border-radius: 10px;
            overflow: hidden;
        }

        .public-menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
            min-width: 0;
            padding: 12px 16px;
            color: #475569;
            text-decoration: none;
            font-weight: 500;
            font-size: 15px;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
            border-radius: 10px 0 0 10px;
        }

        .public-menu-row.has-submenu .public-menu-item {
            border-radius: 10px 0 0 10px;
        }

        .public-menu-item i {
            width: 20px;
            text-align: center;
            font-size: 17px;
            flex-shrink: 0;
        }

        .public-menu-item:hover,
        .public-menu-item.active {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: #fff;
        }

        .public-submenu-toggle {
            flex-shrink: 0;
            width: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            background: transparent;
            color: #64748b;
            cursor: pointer;
            border-radius: 0 10px 10px 0;
            transition: background 0.2s ease, color 0.2s ease, transform 0.2s ease;
        }

        .public-submenu-toggle:hover {
            background: rgba(30, 58, 138, 0.08);
            color: #1e3a8a;
        }

        .public-submenu-toggle[aria-expanded="true"] i {
            transform: rotate(180deg);
        }

        .public-submenu-toggle i {
            transition: transform 0.2s ease;
            font-size: 14px;
        }

        .public-submenu {
            list-style: none;
            margin: 0;
            padding: 0;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .public-submenu.open {
            max-height: 480px;
        }

        .public-submenu .public-menu-item {
            padding-left: 52px;
            font-size: 14px;
            border-radius: 10px;
            margin: 2px 8px;
        }

        .public-sidebar-footer {
            padding: 20px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            gap: 10px;
            flex-shrink: 0;
        }

        .public-sidebar-footer .public-menu-item {
            border-radius: 10px;
        }

        .public-sidebar-cta {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: linear-gradient(135deg, var(--navy), var(--navy-light));
            color: #fff;
            border: none;
            padding: 14px 20px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 15px;
            text-decoration: none;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .public-sidebar-cta:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(26, 43, 95, 0.25);
            color: #fff;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .login-btn {
            color: #4a5568;
            font-weight: 400;
            text-decoration: none;
            padding: 0;
            border-radius: 9999px;
            transition: opacity 0.2s ease;
        }

        .login-btn:hover {
            background-color: transparent;
            opacity: 0.7;
        }

        .get-started-btn {
            background: #27365f;
            color: #ffffff;
            border: 1px solid rgba(39, 54, 95, 0.35);
            padding: 0.9rem 1.6rem;
            border-radius: 9999px;
            font-weight: 500;
            cursor: pointer;
            transition: transform .15s ease, box-shadow .15s ease, opacity .2s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-family: inherit;
            font-size: inherit;
        }

        .get-started-btn:hover {
            transform: scale(1.03);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.10);
        }

        .hamburger {
            display: none !important;
            flex-direction: column;
            cursor: pointer;
            width: 30px;
            height: 24px;
            justify-content: space-between;
            position: relative;
            z-index: 1001;
        }

        .hamburger span {
            height: 3px;
            width: 100%;
            background-color: var(--navy);
            border-radius: 2px;
            transition: var(--transition);
        }


        /* Media query for screens smaller than 1024px (approx 12 inches) */
        @media (max-width: 1024px) {
            .nav-menu, .nav-actions {
                display: none !important;
            }

            .hamburger {
                display: flex !important;
            }
        }
        
        .public-mobile-sidebar .gtranslate_wrapper {
            display: block !important;
            visibility: visible !important;
            width: calc(100% - 40px) !important;
            margin: 0 20px 16px !important;
        }
    </style>
</head>
<body class="<?php echo !isLoggedIn() ? 'public-page' : ''; ?>">
    <header class="header">
        <nav class="nav-container">
            <a href="<?php echo SITE_URL; ?>/" class="logo">
                <?php
                // Use logo image if available, otherwise show text
                $showLogoImage = false;
                
                if (!empty($siteLogo) && strpos($siteLogo, 'http') !== false) {
                    // Check if it's not the default logo.svg
                    if (strpos($siteLogo, 'logo.svg') === false) {
                        // Logo is a custom uploaded image - check if file exists
                        try {
                            $cleanLogoUrl = strtok($siteLogo, '?');
                            
                            // Verify BASE_PATH is defined
                            if (!defined('BASE_PATH')) {
                                error_log("[Header Debug] BASE_PATH not defined");
                            } else {
                                $logoPath = str_replace(SITE_URL, BASE_PATH, $cleanLogoUrl);
                                
                                // Debug logging
                                // error_log("[Header Debug] Logo URL: " . $cleanLogoUrl);
                                // error_log("[Header Debug] Logo Path: " . $logoPath);
                                // error_log("[Header Debug] File exists: " . (file_exists($logoPath) ? 'YES' : 'NO'));
                                
                                if (file_exists($logoPath)) {
                                    $showLogoImage = true;
                                } else {
                                    error_log("[Header Debug] Logo file not found at: " . $logoPath);
                                }
                            }
                        } catch (Exception $e) {
                            error_log("[Header Debug] Error checking logo file: " . $e->getMessage());
                        }
                    }
                }
                
                if ($showLogoImage): ?>
                    <img src="<?php echo htmlspecialchars($siteLogo); ?>" alt="<?php echo htmlspecialchars($siteName); ?>" style="max-height: 40px; max-width: 180px; object-fit: contain;">
                <?php else: ?>
                    <div class="logo-icon"><?php echo htmlspecialchars($siteInitials); ?></div>
                    <div class="logo-text"><?php echo htmlspecialchars($siteName); ?></div>
                <?php endif; ?>
            </a>
            
            <?php if (isLoggedIn()): ?>
            <ul class="nav-menu">
                    <li><a href="<?php echo SITE_URL; ?>/dashboard" class="nav-link">Dashboard</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/account" class="nav-link">Accounts</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/transfer" class="nav-link">Transfer</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/card" class="nav-link">Cards</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/loan" class="nav-link">Loans</a></li>
                    <?php if ($_SESSION['user_role'] === 'admin'): ?>
                    <li><a href="<?php echo SITE_URL; ?>/admin" class="nav-link">Admin</a></li>
                    <?php endif; ?>
                </ul>
            
            <div class="nav-actions">
                <a href="<?php echo SITE_URL; ?>/profile" class="login-btn"><?php echo htmlspecialchars($_SESSION['user_name']); ?></a>
                <a href="<?php echo SITE_URL; ?>/auth/logout" class="get-started-btn">Logout</a>
            </div>
            <?php else: ?>
            <ul class="nav-menu">
                <li><a href="<?php echo SITE_URL; ?>/" class="nav-link">Home</a></li>
                <li class="nav-item-dropdown">
                    <a href="<?php echo SITE_URL; ?>/about" class="nav-link has-dropdown">About Us</a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo SITE_URL; ?>/partnership" class="nav-link">Partnership</a></li>
                    </ul>
                </li>
                <li class="nav-item-dropdown">
                    <a href="<?php echo SITE_URL; ?>/services" class="nav-link has-dropdown">Services</a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo SITE_URL; ?>/accounts" class="nav-link">Accounts</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/cards" class="nav-link">Cards</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/loans" class="nav-link">Loans</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/investments" class="nav-link">Investments</a></li>
                    </ul>
                </li>
                <li><a href="<?php echo SITE_URL; ?>/charity" class="nav-link">Charity</a></li>
                <li class="nav-item-dropdown">
                    <a href="<?php echo SITE_URL; ?>/help-center" class="nav-link has-dropdown">Help Center</a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo SITE_URL; ?>/security" class="nav-link">Security</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/faqs" class="nav-link">FAQs</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/terms" class="nav-link">Terms</a></li>
                    </ul>
                </li>
                </ul>
            
            <div class="nav-actions">
                <a href="<?php echo SITE_URL; ?>/auth/login" class="login-btn">Login</a>
                <a href="<?php echo SITE_URL; ?>/auth/login" class="get-started-btn">My Account</a>
            </div>
            <?php endif; ?>
            
            <div class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>
    </header>

    <div class="public-mobile-overlay" id="publicMobileOverlay" aria-hidden="true"></div>
    <aside class="public-mobile-sidebar" id="publicMobileSidebar" aria-hidden="true" aria-label="Mobile navigation">
        <div class="public-sidebar-header">
            <div class="public-sidebar-logo"><?php echo htmlspecialchars($siteName); ?></div>
            <button type="button" class="public-sidebar-close" id="publicSidebarClose" aria-label="Close menu">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <?php if (isLoggedIn()): ?>
        <ul class="public-sidebar-menu">
            <li><a href="<?php echo SITE_URL; ?>/dashboard" class="public-menu-item">
                <i class="fas fa-home"></i><span>Dashboard</span>
            </a></li>
            <li><a href="<?php echo SITE_URL; ?>/account" class="public-menu-item">
                <i class="fas fa-wallet"></i><span>Accounts</span>
            </a></li>
            <li><a href="<?php echo SITE_URL; ?>/transfer" class="public-menu-item">
                <i class="fas fa-exchange-alt"></i><span>Transfer</span>
            </a></li>
            <li><a href="<?php echo SITE_URL; ?>/card" class="public-menu-item">
                <i class="fas fa-credit-card"></i><span>Cards</span>
            </a></li>
            <li><a href="<?php echo SITE_URL; ?>/loan" class="public-menu-item">
                <i class="fas fa-hand-holding-usd"></i><span>Loans</span>
            </a></li>
            <?php if ($_SESSION['user_role'] === 'admin'): ?>
            <li><a href="<?php echo SITE_URL; ?>/admin" class="public-menu-item">
                <i class="fas fa-cog"></i><span>Admin</span>
            </a></li>
            <?php endif; ?>
            <li><a href="<?php echo SITE_URL; ?>/profile" class="public-menu-item">
                <i class="fas fa-user"></i><span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            </a></li>
        </ul>
        <div class="public-sidebar-footer">
            <a href="<?php echo SITE_URL; ?>/auth/logout" class="public-menu-item">
                <i class="fas fa-sign-out-alt"></i><span>Logout</span>
            </a>
        </div>
        <?php else: ?>
        <ul class="public-sidebar-menu">
            <li><a href="<?php echo SITE_URL; ?>/" class="public-menu-item">
                <i class="fas fa-home"></i><span>Home</span>
            </a></li>

            <li class="public-menu-group">
                <div class="public-menu-row has-submenu">
                    <a href="<?php echo SITE_URL; ?>/about" class="public-menu-item">
                        <i class="fas fa-info-circle"></i><span>About Us</span>
                    </a>
                    <button type="button" class="public-submenu-toggle" aria-expanded="false" aria-controls="publicSubmenuAbout" aria-label="Toggle About Us submenu">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
                <ul class="public-submenu" id="publicSubmenuAbout">
                    <li><a href="<?php echo SITE_URL; ?>/partnership" class="public-menu-item">
                        <i class="fas fa-handshake"></i><span>Partnership</span>
                    </a></li>
                </ul>
            </li>

            <li class="public-menu-group">
                <div class="public-menu-row has-submenu">
                    <a href="<?php echo SITE_URL; ?>/services" class="public-menu-item">
                        <i class="fas fa-concierge-bell"></i><span>Services</span>
                    </a>
                    <button type="button" class="public-submenu-toggle" aria-expanded="false" aria-controls="publicSubmenuServices" aria-label="Toggle Services submenu">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
                <ul class="public-submenu" id="publicSubmenuServices">
                    <li><a href="<?php echo SITE_URL; ?>/accounts" class="public-menu-item">
                        <i class="fas fa-wallet"></i><span>Accounts</span>
                    </a></li>
                    <li><a href="<?php echo SITE_URL; ?>/cards" class="public-menu-item">
                        <i class="fas fa-credit-card"></i><span>Cards</span>
                    </a></li>
                    <li><a href="<?php echo SITE_URL; ?>/loans" class="public-menu-item">
                        <i class="fas fa-hand-holding-usd"></i><span>Loans</span>
                    </a></li>
                    <li><a href="<?php echo SITE_URL; ?>/investments" class="public-menu-item">
                        <i class="fas fa-chart-line"></i><span>Investments</span>
                    </a></li>
                </ul>
            </li>

            <li><a href="<?php echo SITE_URL; ?>/charity" class="public-menu-item">
                <i class="fas fa-hand-holding-heart"></i><span>Charity</span>
            </a></li>

            <li class="public-menu-group">
                <div class="public-menu-row has-submenu">
                    <a href="<?php echo SITE_URL; ?>/help-center" class="public-menu-item">
                        <i class="fas fa-life-ring"></i><span>Help Center</span>
                    </a>
                    <button type="button" class="public-submenu-toggle" aria-expanded="false" aria-controls="publicSubmenuHelp" aria-label="Toggle Help Center submenu">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
                <ul class="public-submenu" id="publicSubmenuHelp">
                    <li><a href="<?php echo SITE_URL; ?>/security" class="public-menu-item">
                        <i class="fas fa-shield-alt"></i><span>Security</span>
                    </a></li>
                    <li><a href="<?php echo SITE_URL; ?>/faqs" class="public-menu-item">
                        <i class="fas fa-question-circle"></i><span>FAQs</span>
                    </a></li>
                    <li><a href="<?php echo SITE_URL; ?>/terms" class="public-menu-item">
                        <i class="fas fa-file-contract"></i><span>Terms</span>
                    </a></li>
                </ul>
            </li>
        </ul>
        <div class="public-sidebar-footer">
            <a href="<?php echo SITE_URL; ?>/auth/login" class="public-menu-item">
                <i class="fas fa-sign-in-alt"></i><span>Login</span>
            </a>
            <a href="<?php echo SITE_URL; ?>/auth/login" class="public-sidebar-cta">
                <i class="fas fa-user"></i><span>My Account</span>
            </a>
        </div>
        <?php endif; ?>
    </aside>

    <!-- Public mobile sidebar -->
    <script>
        (function() {
            if (window.publicMobileNavInitialized) return;
            window.publicMobileNavInitialized = true;

            function setHamburgerState(hamburger, isOpen) {
                if (!hamburger) return;
                const spans = hamburger.querySelectorAll('span');
                hamburger.classList.toggle('active', isOpen);
                if (spans[0]) spans[0].style.transform = isOpen ? 'rotate(45deg) translate(5px, 5px)' : 'none';
                if (spans[1]) spans[1].style.opacity = isOpen ? '0' : '1';
                if (spans[2]) spans[2].style.transform = isOpen ? 'rotate(-45deg) translate(7px, -6px)' : 'none';
            }

            function closePublicMobileNav() {
                const sidebar = document.getElementById('publicMobileSidebar');
                const overlay = document.getElementById('publicMobileOverlay');
                const hamburger = document.getElementById('hamburger');
                if (!sidebar || !overlay) return;

                sidebar.classList.remove('open');
                overlay.classList.remove('active');
                sidebar.setAttribute('aria-hidden', 'true');
                overlay.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
                setHamburgerState(hamburger, false);
            }

            function openPublicMobileNav() {
                const sidebar = document.getElementById('publicMobileSidebar');
                const overlay = document.getElementById('publicMobileOverlay');
                const hamburger = document.getElementById('hamburger');
                if (!sidebar || !overlay) return;

                sidebar.classList.add('open');
                overlay.classList.add('active');
                sidebar.setAttribute('aria-hidden', 'false');
                overlay.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
                setHamburgerState(hamburger, true);
            }

            function togglePublicMobileNav() {
                const sidebar = document.getElementById('publicMobileSidebar');
                if (!sidebar) return;
                if (sidebar.classList.contains('open')) {
                    closePublicMobileNav();
                } else {
                    openPublicMobileNav();
                }
            }

            function initPublicMobileNav() {
                const hamburger = document.getElementById('hamburger');
                const overlay = document.getElementById('publicMobileOverlay');
                const closeBtn = document.getElementById('publicSidebarClose');
                const sidebar = document.getElementById('publicMobileSidebar');
                if (!hamburger || !sidebar) return;

                hamburger.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    togglePublicMobileNav();
                });

                if (overlay) {
                    overlay.addEventListener('click', closePublicMobileNav);
                }

                if (closeBtn) {
                    closeBtn.addEventListener('click', closePublicMobileNav);
                }

                sidebar.querySelectorAll('.public-submenu-toggle').forEach(function(toggle) {
                    toggle.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        const submenuId = toggle.getAttribute('aria-controls');
                        const submenu = submenuId ? document.getElementById(submenuId) : toggle.closest('.public-menu-group')?.querySelector('.public-submenu');
                        if (!submenu) return;

                        const isOpen = submenu.classList.toggle('open');
                        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                    });
                });

                sidebar.querySelectorAll('.public-menu-item, .public-sidebar-cta').forEach(function(link) {
                    link.addEventListener('click', function() {
                        if (window.innerWidth <= 1024) {
                            closePublicMobileNav();
                        }
                    });
                });

                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        closePublicMobileNav();
                    }
                });

                window.addEventListener('resize', function() {
                    if (window.innerWidth > 1024) {
                        closePublicMobileNav();
                    }
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initPublicMobileNav);
            } else {
                initPublicMobileNav();
            }
        })();
    </script>

    <?php include __DIR__ . '/../../includes/impersonation-banner.php'; ?>
    
    <main class="main-content"><?php
