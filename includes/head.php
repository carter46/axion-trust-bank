<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'SecureBank'; ?></title>
    
    <!-- Dynamic Favicon - Supports all formats -->
    <?php 
    try {
        $faviconUrl = getSetting('site_favicon_url', SITE_URL . '/favicon.ico');
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
    
    <!-- Font Awesome Icons (Local - CSP Compliant) -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/fontawesome/css/all.min.css">
    
    <style>
        /* ===== BASE STYLES ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            background-attachment: fixed;
            color: #1e293b;
            line-height: 1.6;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ===== DESKTOP LAYOUT ===== */
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* ===== COLLAPSIBLE SIDEBAR ===== */
        .sidebar {
            width: 70px; /* Collapsed by default on desktop */
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.95) 100%);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(30, 58, 138, 0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: fixed;
            height: 100vh;
            overflow-y: auto; /* Make entire sidebar scrollable */
            overflow-x: hidden;
            z-index: 10000;
            box-shadow: 0 4px 20px rgba(30, 58, 138, 0.08);
            display: flex;
            flex-direction: column;
            /* Custom scrollbar styling */
            scrollbar-width: thin;
            scrollbar-color: rgba(30, 58, 138, 0.3) transparent;
        }
        
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(30, 58, 138, 0.3);
            border-radius: 3px;
        }
        
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(30, 58, 138, 0.5);
        }

        .sidebar.expanded {
            width: 280px; /* Expanded state */
        }

        /* Mobile sidebar - always shows full version when open */
        @media (max-width: 768px) {
            .sidebar {
                width: 280px; /* Full width on mobile */
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar.mobile-open {
                transform: translateX(0);
            }
            
            /* Force full layout on mobile when open */
            .sidebar.mobile-open .sidebar-logo,
            .sidebar.mobile-open .menu-item span,
            .sidebar.mobile-open .user-info,
            .sidebar.mobile-open .user-action span {
                opacity: 1 !important;
                width: auto !important;
                display: block !important;
                visibility: visible !important;
            }
            
            .sidebar.mobile-open .menu-item {
                justify-content: flex-start !important;
                padding: 12px 20px !important;
            }
            
            .sidebar.mobile-open .menu-item i {
                margin-right: 12px !important;
            }
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 80px;
            flex-shrink: 0;
        }

        .sidebar-logo {
            font-size: 24px;
            font-weight: 700;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            white-space: nowrap;
            transition: opacity 0.3s ease;
            opacity: 0;
            width: 0;
            overflow: hidden;
        }

        .sidebar.expanded .sidebar-logo {
            opacity: 1;
            width: auto;
        }

        .sidebar-toggle {
            background: none;
            border: none;
            font-size: 18px;
            color: #032B44;
            cursor: pointer;
            padding: 8px;
            border-radius: 4px;
            transition: all 0.2s;
        }

        .sidebar-toggle:hover {
            background: #032B44;
            color: white;
        }

        .sidebar-menu {
            padding: 20px 0;
            flex-grow: 1;
            /* Remove individual scrolling - let parent sidebar handle it */
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #475569;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 3px solid transparent;
            position: relative;
            margin: 4px 8px;
            border-radius: 10px;
        }

        .menu-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            opacity: 0;
            border-radius: 10px;
            transition: opacity 0.3s ease;
            z-index: -1;
        }

        .menu-item:hover {
            color: white;
            transform: translateX(5px);
        }

        .menu-item:hover::before {
            opacity: 1;
        }

        .menu-item.active {
            color: white;
            border-left-color: transparent;
        }

        .menu-item.active::before {
            opacity: 1;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        }

        .menu-item i {
            width: 20px;
            font-size: 18px;
            text-align: center;
            margin-right: 12px;
            flex-shrink: 0;
            color: inherit;
        }

        .menu-item span {
            transition: opacity 0.3s ease;
            white-space: nowrap;
            color: inherit;
            opacity: 0;
            width: 0;
            overflow: hidden;
        }

        .sidebar.expanded .menu-item span {
            opacity: 1;
            width: auto;
        }

        /* Ensure icons are always visible in collapsed state */
        .sidebar:not(.expanded) .menu-item {
            justify-content: center;
            padding: 12px 0;
        }

        .sidebar:not(.expanded) .menu-item i {
            margin-right: 0;
        }

        /* ===== USER SECTION ===== */
        .user-section {
            padding: 20px;
            border-top: 1px solid #e0e0e0;
            margin-top: auto;
            flex-shrink: 0;
            padding-bottom: 20px;
        }

        @media (max-width: 768px) {
            .user-section {
                padding-bottom: 160px; /* Extra padding for mobile bottom nav */
            }
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding: 8px 0;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            min-width: 40px;
            min-height: 40px;
            max-width: 40px;
            max-height: 40px;
            border-radius: 50%;
            background: #032B44;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            flex-shrink: 0;
            flex-grow: 0;
            box-sizing: border-box;
            aspect-ratio: 1 / 1;
        }
        
        .user-avatar-img {
            width: 40px;
            height: 40px;
            min-width: 40px;
            min-height: 40px;
            max-width: 40px;
            max-height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e5e7eb;
            flex-shrink: 0;
            flex-grow: 0;
            box-sizing: border-box;
            aspect-ratio: 1 / 1;
        }

        .user-info {
            white-space: nowrap;
            overflow: hidden;
            transition: opacity 0.3s ease;
            opacity: 0;
            width: 0;
        }

        .sidebar.expanded .user-info {
            opacity: 1;
            width: auto;
        }

        .user-name {
            font-weight: 600;
            font-size: 15px;
            color: #032B44;
        }

        .user-email {
            font-size: 13px;
            color: #666;
        }

        .user-actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .user-action {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            color: #032B44;
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
        }

        .user-action:hover {
            color: #032B44;
            background: rgba(3, 43, 68, 0.1);
            border-radius: 6px;
            padding-left: 8px;
        }

        .user-action i {
            width: 20px;
            font-size: 16px;
            text-align: center;
            flex-shrink: 0;
        }

        .user-action span {
            transition: opacity 0.3s ease;
            white-space: nowrap;
            opacity: 0;
            width: 0;
            overflow: hidden;
        }

        .sidebar.expanded .user-action span {
            opacity: 1;
            width: auto;
        }

        /* ===== MAIN CONTENT AREA ===== */
        .main-content-area {
            flex: 1;
            margin-left: 70px; /* Account for collapsed sidebar by default */
            transition: margin-left 0.3s ease;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            width: calc(100% - 70px);
            max-width: calc(100% - 70px);
            box-sizing: border-box;
        }

        .sidebar.expanded + .main-content-area {
            margin-left: 280px;
            width: calc(100% - 280px);
            max-width: calc(100% - 280px);
        }

        /* ===== TOP HEADER ===== */
        .top-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 20px 30px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            height: 80px;
            flex-shrink: 0;
            gap: 20px;
        }
        
        .header-logo {
            display: flex;
            align-items: center;
            flex-shrink: 0;
            order: 1; /* Ensure logo is first/left */
            margin-right: auto; /* Push everything else to the right */
        }
        
        .site-logo-img {
            max-height: 50px;
            max-width: 200px;
            width: auto;
            height: auto;
            object-fit: contain;
        }
        
        .site-logo-text {
            font-size: 24px;
            font-weight: 700;
            color: #032B44;
            white-space: nowrap;
        }

        .header-left {
            display: flex;
            align-items: center;
        }

        .mobile-menu-toggle {
            display: none; /* Hidden on desktop */
            background: transparent;
            border: none;
            color: #032B44;
            font-size: 24px;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 8px;
            transition: all 0.3s;
            z-index: 1000;
            position: relative;
        }

        .mobile-menu-toggle:hover {
            background: rgba(30, 58, 138, 0.1);
        }
        
        .mobile-menu-toggle:active {
            background: rgba(30, 58, 138, 0.2);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 20px;
            order: 2; /* Ensure actions are on the right */
            margin-left: auto;
        }

        .header-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(248, 249, 250, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid rgba(240, 240, 240, 0.5);
        }

        .header-icon:hover {
            background: white;
            border-color: #032B44;
            color: #032B44;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(3, 43, 68, 0.15);
        }

        .user-profile-header {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
        }

        .user-avatar-header {
            width: 40px;
            height: 40px;
            min-width: 40px;
            min-height: 40px;
            max-width: 40px;
            max-height: 40px;
            border-radius: 50%;
            background: #032B44;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            flex-shrink: 0;
            flex-grow: 0;
            box-sizing: border-box;
            aspect-ratio: 1 / 1;
        }
        
        .user-avatar-img-header {
            width: 40px;
            height: 40px;
            min-width: 40px;
            min-height: 40px;
            max-width: 40px;
            max-height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e5e7eb;
            flex-shrink: 0;
            flex-grow: 0;
            box-sizing: border-box;
            aspect-ratio: 1 / 1;
        }

        /* ===== CONTENT AREA ===== */
        .content-area {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
        }

        /* ===== MOBILE LAYOUT ===== */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
            }
            
            .sidebar.mobile-open {
                transform: translateX(0);
            }
            
            .main-content-area {
                margin-left: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                padding-bottom: 140px; /* Space for bottom nav */
                box-sizing: border-box;
            }
            
            .sidebar.expanded + .main-content-area {
                margin-left: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }
            
            .content-area {
                padding: 15px !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box;
            }
            
            /* Ensure all containers and content are full width on mobile */
            .content-area .container,
            .content-area [class*="container"]:not(.notification-container):not(.user-profile-container),
            .content-area [class*="wrapper"] {
                width: 100% !important;
                max-width: 100% !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
                box-sizing: border-box;
            }
            
            /* Ensure direct children of content-area are full width */
            .content-area > div:not(.notification-dropdown):not(.user-dropdown),
            .content-area > section,
            .content-area > main,
            .content-area > article {
                width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box;
            }
            
            /* Override any page-specific max-width constraints BUT keep internal padding */
            .content-area [class*="octobank"],
            .content-area [class*="dashboard"],
            .content-area [class*="account"],
            .content-area [class*="profile"],
            .content-area [class*="transaction"],
            .content-area [class*="transfer"] {
                max-width: 100% !important;
                width: 100% !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
                box-sizing: border-box;
                overflow-x: hidden; /* Prevent horizontal overflow */
            }
            
            /* Ensure cards and boxes maintain their boundaries */
            .content-area [class*="card"],
            .content-area [class*="box"],
            .content-area [class*="Card"],
            .content-area [class*="Box"],
            .content-area .summary-card,
            .content-area .profile-card,
            .content-area .dashboard-card,
            .content-area .account-card {
                width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box;
                overflow-x: hidden;
                word-wrap: break-word;
                overflow-wrap: break-word;
            }
            
            /* Prevent overflow for specific content types inside cards */
            .content-area [class*="card"] table,
            .content-area [class*="box"] table,
            .content-area [class*="card"] img,
            .content-area [class*="box"] img,
            .content-area [class*="card"] video,
            .content-area [class*="box"] video,
            .content-area [class*="card"] iframe,
            .content-area [class*="box"] iframe {
                max-width: 100% !important;
                height: auto !important;
            }
            
            /* Fix text overflow in cards - only for text elements */
            .content-area [class*="card"] p,
            .content-area [class*="box"] p,
            .content-area [class*="card"] h1,
            .content-area [class*="card"] h2,
            .content-area [class*="card"] h3,
            .content-area [class*="card"] h4,
            .content-area [class*="box"] h1,
            .content-area [class*="box"] h2,
            .content-area [class*="box"] h3,
            .content-area [class*="box"] h4,
            .content-area [class*="card"] span:not([class*="flex"]):not([class*="grid"]),
            .content-area [class*="box"] span:not([class*="flex"]):not([class*="grid"]) {
                word-wrap: break-word;
                overflow-wrap: break-word;
            }
            
            /* Fix tables and wide content */
            .content-area table {
                width: 100% !important;
                max-width: 100% !important;
                display: block;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            /* Fix images and media - ensure they don't overflow */
            .content-area img,
            .content-area video,
            .content-area iframe {
                max-width: 100% !important;
                height: auto !important;
            }
            
            /* Ensure inputs and form elements respect container width */
            .content-area input,
            .content-area textarea,
            .content-area select,
            .content-area button {
                max-width: 100%;
                box-sizing: border-box;
            }
            
            /* Fix flex containers to wrap properly */
            .content-area [class*="flex"] {
                min-width: 0; /* Allow flex items to shrink */
            }
            
            /* Fix grid containers */
            .content-area [class*="grid"] {
                min-width: 0; /* Allow grid items to shrink */
            }
            
            /* Fix transaction avatar specifically - prevent stretching on mobile */
            .content-area .transaction-avatar {
                width: 40px !important;
                height: 40px !important;
                min-width: 40px !important;
                min-height: 40px !important;
                max-width: 40px !important;
                max-height: 40px !important;
                flex-shrink: 0 !important;
                flex-grow: 0 !important;
                aspect-ratio: 1 / 1 !important;
                border-radius: 50% !important;
                box-sizing: border-box !important;
                overflow: hidden !important;
            }
            
            /* Fix profile photos - prevent stretching on mobile */
            .content-area .profile-photo,
            .content-area .profile-photo-placeholder {
                flex-shrink: 0 !important;
                flex-grow: 0 !important;
                aspect-ratio: 1 / 1 !important;
                box-sizing: border-box !important;
                object-fit: cover !important;
                border-radius: 50% !important;
                overflow: hidden !important;
                display: block !important;
            }
            
            .content-area .profile-photo {
                width: 80px !important;
                height: 80px !important;
                min-width: 80px !important;
                min-height: 80px !important;
                max-width: 80px !important;
                max-height: 80px !important;
            }
            
            .content-area .profile-photo-placeholder {
                width: 80px !important;
                height: 80px !important;
                min-width: 80px !important;
                min-height: 80px !important;
                max-width: 80px !important;
                max-height: 80px !important;
            }
            
            /* Fix user avatars - prevent stretching */
            .content-area .user-avatar,
            .content-area .user-avatar-img,
            .content-area .user-avatar-header,
            .content-area .user-avatar-img-header {
                flex-shrink: 0 !important;
                flex-grow: 0 !important;
                aspect-ratio: 1 / 1 !important;
                box-sizing: border-box !important;
                object-fit: cover !important;
            }
            
            /* Fix menu item icons and account icons */
            .content-area .menu-item-icon,
            .content-area .account-icon {
                flex-shrink: 0 !important;
                flex-grow: 0 !important;
                box-sizing: border-box !important;
            }
            
            /* Ensure transaction items don't stretch their children */
            .content-area .transaction-item {
                align-items: center !important;
            }
            
            /* Global fix: ensure box-sizing and prevent overflow for specific elements */
            .content-area * {
                box-sizing: border-box;
            }
            
            /* Constrain only elements that commonly cause overflow */
            .content-area img,
            .content-area video,
            .content-area iframe,
            .content-area embed,
            .content-area object,
            .content-area svg,
            .content-area canvas,
            .content-area table,
            .content-area pre,
            .content-area code {
                max-width: 100% !important;
            }
            
            /* Fix common layout elements */
            .content-area [class*="header"],
            .content-area [class*="footer"],
            .content-area [class*="section"],
            .content-area [class*="container"] {
                width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box;
            }
            
            .top-header {
                padding: 15px 20px;
                justify-content: space-between;
                height: 70px;
                width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box;
                gap: 15px;
            }
            
            .header-logo {
                flex-shrink: 0;
                order: 1;
                margin-right: auto;
                margin-left: 0 !important;
                padding-left: 0 !important;
            }
            
            .site-logo-img {
                max-height: 35px !important;
                max-width: 120px !important;
                height: auto !important;
                width: auto !important;
            }
            
            .site-logo-text {
                font-size: 18px !important;
            }
            
            .header-actions {
                order: 2;
                margin-left: auto;
            }
            
            .mobile-menu-toggle {
                display: block; /* Show on mobile */
            }
            
            .header-actions {
                gap: 15px;
            }
            
            .header-icon {
                width: 44px;
                height: 44px;
                font-size: 18px;
            }
            
            .user-profile-header {
                gap: 8px;
            }
            
            .user-avatar-header {
                width: 44px;
                height: 44px;
                font-size: 18px;
            }
            
            .user-name-header {
                font-size: 14px;
                font-weight: 600;
            }
        }

        /* ===== MOBILE BOTTOM NAVIGATION (YOUR PREFERRED DESIGN) ===== */
        :root {
            --bg: #e9dfd6;
            --bar: #fbf7f5;
            --inactive: #475569; /* Darker gray for better visibility */
            --active-bg: #032B44; /* Navy blue background for active */
            --active-text: #ffffff; /* White text/icons for active */
            --shadow: rgba(11,11,11,0.12);
            --nav-height: 64px;
            --fab-size: 64px;
        }

        .mobile-bottom-nav {
            /* HIDDEN ON DESKTOP BY DEFAULT */
            display: none;
            position: fixed;
            left: 50%;
            transform: translateX(-50%);
            bottom: 18px;
            height: var(--nav-height);
            background: var(--bar);
            border-radius: 18px;
            box-shadow: 0 10px 30px var(--shadow);
            align-items: center;
            justify-content: center;
            gap: 34px;
            padding: 0 16px;
            width: min(340px, calc(100% - 120px));
            z-index: 9999;
        }

        /* SHOW ONLY ON MOBILE */
        @media (max-width: 768px) {
            .mobile-bottom-nav {
                display: flex;
            }
        }

        /* Ensure it never shows on desktop even if other CSS leaks */
        @media (min-width: 769px) {
            .mobile-bottom-nav {
                display: none !important;
            }
        }

        .nav-item {
            display: flex;
            justify-content: center;
            align-items: center;
            color: var(--inactive);
            text-decoration: none;
            line-height: 1;
            -webkit-tap-highlight-color: transparent;
            padding: 8px;
            border-radius: 10px;
            transition: all 0.3s ease;
            width: 42px;
            height: 42px;
            min-height: auto;
        }

        .nav-item.active {
            background: transparent;
            color: var(--inactive);
        }

        .nav-item i {
            font-size: 18px;
            transition: color 0.3s ease;
        }

        .nav-item.active i {
            color: var(--inactive);
        }

        .nav-spacer {
            display: none;
        }

        .fab {
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff !important; /* Keep white color always */
            text-decoration: none;
            line-height: 1;
            -webkit-tap-highlight-color: transparent;
            padding: 8px;
            border-radius: 10px;
            transition: all 0.3s ease;
            background: #000 !important; /* Keep black background always */
            border: none;
            cursor: pointer;
            width: 42px;
            height: 42px;
            font-size: 18px;
            box-shadow: none;
            min-height: auto;
        }

        .fab i {
            font-size: 18px;
            transition: color 0.3s ease;
            color: #fff !important; /* Keep white icon always */
        }

        /* Ensure transfer button doesn't get active state styling */
        .fab.active,
        .fab.nav-item.active {
            background: #000 !important;
            color: #fff !important;
        }

        .fab.active i,
        .fab.nav-item.active i {
            color: #fff !important;
        }

        .fab:active {
            transform: scale(0.96);
        }

        /* Overlay for mobile */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9998;
            display: none;
        }

        .overlay.active {
            display: block;
        }

        /* Ensure all modals are above mobile nav on mobile */
        @media (max-width: 768px) {
            .modal,
            [class*="modal"],
            [id*="modal"],
            [id*="Modal"] {
                z-index: 10000 !important; /* Higher than mobile nav (9999) */
            }
        }
    </style>
    
    <!-- Password Toggle Styles -->
    <style>
        .password-wrapper {
            position: relative;
            display: inline-block;
            width: 100%;
        }
        
        .password-toggle-icon {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6b7280;
            font-size: 18px;
            z-index: 10;
            transition: color 0.2s;
            user-select: none;
        }
        
        .password-toggle-icon:hover {
            color: #3b82f6;
        }
        
        .password-wrapper input[type="password"],
        .password-wrapper input[type="text"] {
            padding-right: 45px !important;
        }
    </style>
    
    <!-- Native Select Styling - White BG, Black Text, Rounded -->
    <style>
        /* Style all select elements */
        select,
        select.form-control,
        select.form-select,
        select.form-input {
            width: 100%;
            padding: 12px 16px;
            padding-right: 40px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 400;
            color: #000000;
            background-color: #ffffff;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 16 16'%3E%3Cpath fill='%23000000' d='M8 11L3 6h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }
        
        /* Hover state */
        select:hover {
            border-color: #d1d5db;
            background-color: #ffffff;
        }
        
        /* Focus state */
        select:focus {
            outline: none;
            border-color: #032B44;
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(3, 43, 68, 0.1);
        }
        
        /* Active/Open state */
        select:active {
            background-color: #ffffff;
        }
        
        /* Disabled state */
        select:disabled {
            background-color: #f3f4f6;
            color: #6b7280;
            cursor: not-allowed;
            opacity: 0.6;
        }
        
        /* Style dropdown options - White background, black text, rounded */
        select option {
            background-color: #ffffff;
            color: #000000;
            padding: 12px 16px;
            font-size: 16px;
            border-radius: 8px;
            margin: 2px 0;
        }
        
        /* Hover state for options (limited browser support) */
        select option:hover,
        select option:checked,
        select option:focus {
            background-color: #f3f4f6;
            color: #000000;
        }
        
        /* Selected option styling */
        select option:checked {
            background-color: #e0f2fe;
            color: #000000;
            font-weight: 500;
        }
        
        /* Mobile optimizations */
        @media (max-width: 768px) {
            select,
            select.form-control,
            select.form-select,
            select.form-input {
                padding: 14px 16px;
                padding-right: 44px;
                font-size: 16px;
                border-radius: 12px;
                -webkit-tap-highlight-color: transparent;
            }
            
            select option {
                padding: 14px 16px;
                font-size: 16px;
            }
        }
        
        /* Firefox specific fixes */
        @-moz-document url-prefix() {
            select {
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 16 16'%3E%3Cpath fill='%23000000' d='M8 11L3 6h10z'/%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: right 12px center;
            }
        }
        
        /* Safari specific fixes */
        @supports (-webkit-appearance: none) {
            select {
                -webkit-appearance: none;
                appearance: none;
            }
        }
    </style>
    
    <!-- Universal Password Toggle Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Find all password inputs
        const passwordInputs = document.querySelectorAll('input[type="password"]');
        
        passwordInputs.forEach(function(input) {
            // Skip if input or parent doesn't exist
            if (!input || !input.parentNode) {
                return;
            }
            
            // Skip if already wrapped
            if (input.parentElement && input.parentElement.classList.contains('password-wrapper')) {
                return;
            }
            
            // Create wrapper
            const wrapper = document.createElement('div');
            wrapper.className = 'password-wrapper';
            
            // Insert wrapper before input
            if (input.parentNode) {
                input.parentNode.insertBefore(wrapper, input);
                
                // Move input into wrapper
                if (wrapper && input) {
                    wrapper.appendChild(input);
                }
            }
            
            // Create toggle icon
            const toggleIcon = document.createElement('i');
            toggleIcon.className = 'fas fa-eye password-toggle-icon';
            toggleIcon.setAttribute('aria-label', 'Toggle password visibility');
            toggleIcon.setAttribute('role', 'button');
            toggleIcon.setAttribute('tabindex', '0');
            
            // Add icon to wrapper (only if wrapper exists)
            if (wrapper) {
                wrapper.appendChild(toggleIcon);
            }
            
            // Toggle functionality
            toggleIcon.addEventListener('click', function() {
                togglePasswordVisibility(input, toggleIcon);
            });
            
            // Keyboard support
            toggleIcon.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    togglePasswordVisibility(input, toggleIcon);
                }
            });
        });
        
        function togglePasswordVisibility(input, icon) {
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
                icon.setAttribute('aria-label', 'Hide password');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
                icon.setAttribute('aria-label', 'Show password');
            }
        }
    });
    </script>
    
    <!-- Page Loading Animation - Exact copy from grand.burlingmail.click reference -->
    <style>
        .page-loading {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100%;
            transition: all .4s .2s ease-in-out;
            background-color: #ffffff;
            visibility: hidden;
            z-index: 99999; /* Lower than transfer overlay which is 10001 */
        }
        .page-loading.active {
            opacity: 1;
            visibility: visible;
        }
        .page-loading-inner {
            position: absolute;
            top: 50%;
            left: 0;
            width: 100%;
            text-align: center;
            transform: translateY(-50%);
            transition: opacity .2s ease-in-out;
            opacity: 0;
        }
        .page-loading.active>.page-loading-inner {
            opacity: 1;
        }
        
        .loading-container {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        
        .loading-animation {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 80px;
            margin-bottom: 1rem;
            position: relative;
        }
        
        .loading-animation .circle {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 4px solid transparent;
            mix-blend-mode: overlay;
            animation: rotateCircle 1.5s linear infinite;
        }
        
        .loading-animation .circle:nth-child(1) {
            border-top-color: #073049;
            animation-delay: 0s;
        }
        
        .loading-animation .circle:nth-child(2) {
            border-right-color: #073049;
            animation-delay: 0.2s;
        }
        
        .loading-animation .circle:nth-child(3) {
            border-bottom-color: #041826;
            animation-delay: 0.4s;
        }
        
        .loading-animation .circle:nth-child(4) {
            border-left-color: #073049;
            animation-delay: 0.6s;
        }
        
        .loading-animation .core {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: linear-gradient(45deg, #073049, #041826);
            box-shadow: 0 0 15px rgba(7, 48, 73, 0.5);
            animation: pulse 1s ease-in-out infinite alternate;
        }
        
        .page-loading .text {
            color: #041826;
            font-weight: 500;
            letter-spacing: 0.05em;
            margin-top: 0.5rem;
            font-size: 0.875rem;
            background: linear-gradient(90deg, #041826, #073049, #041826);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradient 2s linear infinite;
        }
        
        @keyframes rotateCircle {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @keyframes pulse {
            from { transform: scale(0.8); opacity: 0.8; }
            to { transform: scale(1.2); opacity: 1; }
        }
        
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
    </style>
    
    <!-- Page Loading Script - Only show on home page and login -->
    <script>
    (function() {
        // Get site name for display
        const siteName = '<?php echo htmlspecialchars(getSiteName()); ?>';
        
        // Check if we should show loading screen
        function shouldShowLoader() {
            const currentPath = window.location.pathname;
            const currentUrl = window.location.href.toLowerCase();
            const urlParams = new URLSearchParams(window.location.search);
            
            // Show on home page (check both path and route parameter)
            const isHomePage = currentPath === '/' || 
                               currentPath === '/home' || 
                               currentPath === '/index.php' ||
                               currentPath.match(/^\/$|^\/home\/?$/) ||
                               urlParams.get('route') === 'home' ||
                               (currentPath === '/' && !urlParams.get('route'));
            
            // Show on login page
            const isLoginPage = currentPath.includes('/auth/login') || 
                               currentPath.includes('/login') ||
                               currentUrl.includes('/auth/login') ||
                               currentUrl.includes('/login') ||
                               urlParams.get('route') === 'auth/login';
            
            // Show after login redirect (check for login success indicator)
            const isAfterLogin = sessionStorage.getItem('justLoggedIn') === 'true' ||
                                 urlParams.has('login') ||
                                 urlParams.has('logged_in');
            
            return isHomePage || isLoginPage || isAfterLogin;
        }
        
        // Store pageLoading in outer scope so all functions can access it
        let pageLoading = null;
        
        // Create page loading element - wait for DOM to be ready
        function initPageLoading() {
            // Only proceed if body exists and pageLoading not already created
            if (!document.body || pageLoading) {
                return;
            }
            
            // Only create if we should show loader
            if (!shouldShowLoader()) {
                return;
            }
            
            pageLoading = document.createElement('div');
            pageLoading.className = 'page-loading';
            pageLoading.innerHTML = `
                <div class="page-loading-inner">
                    <div class="loading-container">
                        <div class="loading-animation">
                            <div class="circle"></div>
                            <div class="circle"></div>
                            <div class="circle"></div>
                            <div class="circle"></div>
                            <div class="core"></div>
                        </div>
                        <div class="text">${siteName}</div>
                    </div>
                </div>
            `;
            
            // Only append if body exists and pageLoading was created
            if (document.body && pageLoading) {
                document.body.appendChild(pageLoading);
            }
        }
        
        // Show loader immediately when page starts loading
        function showPageLoading() {
            // Only show if we should show loader
            if (!shouldShowLoader()) {
                return;
            }
            
            if (pageLoading) {
                pageLoading.classList.add('active');
            }
        }
        
        // Hide and remove loader
        function hidePageLoading() {
            if (pageLoading) {
                pageLoading.classList.remove('active');
                setTimeout(function() {
                    if (pageLoading) {
                        pageLoading.remove();
                        pageLoading = null;
                    }
                }, 500);
            }
        }
        
        // Only initialize if we should show loader
        if (shouldShowLoader()) {
            // Try immediately if body is ready, otherwise wait for DOMContentLoaded
            if (document.body) {
                initPageLoading();
            } else {
                document.addEventListener('DOMContentLoaded', initPageLoading);
            }
            
            // Show loader on page load
            if (document.readyState === 'loading') {
                // Wait for DOM to be ready, then init and show
                document.addEventListener('DOMContentLoaded', function() {
                    initPageLoading();
                    showPageLoading();
                });
            } else {
                // If DOM is already loaded, initialize and show briefly
                initPageLoading();
                showPageLoading();
            }
            
            // Hide loader when page is fully loaded
            window.addEventListener('load', function() {
                // Add a slight delay to make loading animation more noticeable
                if (pageLoading && shouldShowLoader()) {
                    setTimeout(function() {
                        hidePageLoading();
                        // Clear login flag after showing
                        sessionStorage.removeItem('justLoggedIn');
                        // Clean up URL parameter if present (without page reload)
                        const url = new URL(window.location.href);
                        if (url.searchParams.has('logged_in') || url.searchParams.has('login')) {
                            url.searchParams.delete('logged_in');
                            url.searchParams.delete('login');
                            window.history.replaceState({}, '', url.toString());
                        }
                    }, 800);
                }
            });
        }
        
        // No longer intercept link clicks or browser navigation for loading screen
        // (Only show on home page and login page)
    })();
    </script>
    <?php
    // Enable translation on all non-admin pages that use this shared head.
    $isAdminRoute = isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/admin') !== false;
    if (!$isAdminRoute) {
        include __DIR__ . '/translation.php';
    }
    ?>
    
</head>
<body>
