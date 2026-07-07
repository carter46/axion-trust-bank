<?php
$pageTitle = '500 - Server Error - SecureBank';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

// Get dynamic site name for branding
$siteName = getSiteName() ?? 'SecureBank';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    
    <!-- Dynamic Favicon - Supports all formats -->
    <?php 
    $faviconUrl = function_exists('getSiteFavicon') ? getSiteFavicon() : (SITE_URL . '/favicon.svg');
    if ($faviconUrl) {
        echo '<link rel="icon" type="image/x-icon" href="' . htmlspecialchars($faviconUrl) . '">';
        echo '<link rel="shortcut icon" type="image/x-icon" href="' . htmlspecialchars($faviconUrl) . '">';
        echo '<link rel="apple-touch-icon" href="' . htmlspecialchars($faviconUrl) . '">';
    } else {
        echo '<link rel="icon" type="image/x-icon" href="' . SITE_URL . '/favicon.svg">';
        echo '<link rel="shortcut icon" type="image/x-icon" href="' . SITE_URL . '/favicon.svg">';
    }
    ?>
    
    <!-- Font Awesome Icons (Local - CSP Compliant) -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/fontawesome/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            text-align: center;
        }
        
        .error-container {
            max-width: 600px;
            padding: 40px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
        
        .logo {
            font-size: 32px;
            font-weight: 700;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 40px;
            justify-content: center;
        }
        
        .logo i {
            font-size: 36px;
        }
        
        h1 {
            font-size: 120px;
            margin: 0;
            opacity: 0.8;
            font-weight: 300;
            line-height: 1;
        }
        
        h2 {
            font-size: 32px;
            margin: 20px 0;
            font-weight: 600;
        }
        
        p {
            font-size: 18px;
            margin: 20px 0 40px;
            opacity: 0.9;
            line-height: 1.6;
        }
        
        .btn {
            display: inline-block;
            padding: 16px 32px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s;
            border: 1px solid rgba(255, 255, 255, 0.3);
            margin: 0 10px;
        }
        
        .btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }
        
        .btn-primary {
            background: rgba(255, 255, 255, 0.9);
            color: #dc3545;
        }
        
        .btn-primary:hover {
            background: white;
        }
        
        @media (max-width: 768px) {
            .error-container {
                margin: 20px;
                padding: 30px 20px;
            }
            
            h1 {
                font-size: 80px;
            }
            
            h2 {
                font-size: 24px;
            }
            
            p {
                font-size: 16px;
            }
            
            .btn {
                display: block;
                margin: 10px 0;
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <a href="<?php echo SITE_URL; ?>/" class="logo">
            <i class="fas fa-university"></i>
            <?php echo htmlspecialchars($siteName); ?>
        </a>
        
        <h1>500</h1>
        <h2>Server Error</h2>
        <p>We're experiencing technical difficulties. Our team has been notified and is working to fix the issue. Please try again later.</p>
        
        <div>
            <a href="<?php echo SITE_URL; ?>/" class="btn btn-primary">
                <i class="fas fa-home"></i> Go Home
            </a>
            <a href="javascript:history.back()" class="btn">
                <i class="fas fa-arrow-left"></i> Go Back
            </a>
        </div>
    </div>
</body>
</html>