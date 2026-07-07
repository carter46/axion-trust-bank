<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'SecureBank'; ?></title>
    
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
            echo '<link rel="icon" type="image/x-icon" href="' . SITE_URL . '/favicon.svg">';
            echo '<link rel="shortcut icon" type="image/x-icon" href="' . SITE_URL . '/favicon.svg">';
            echo '<link rel="apple-touch-icon" href="' . SITE_URL . '/favicon.svg">';
        }
    } catch (Exception $e) {
        // Fallback if getSetting fails
        echo '<link rel="icon" type="image/x-icon" href="' . SITE_URL . '/favicon.svg">';
        echo '<link rel="shortcut icon" type="image/x-icon" href="' . SITE_URL . '/favicon.svg">';
        echo '<link rel="apple-touch-icon" href="' . SITE_URL . '/favicon.svg">';
    }
    ?>
    
    <!-- Font Awesome Icons (Local - CSP Compliant) -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/fontawesome/css/all.min.css">
    
    <style>
        /* ===== AUTH PAGE STYLES ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #032B44 0%, #024a6b 100%);
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-container {
            width: 100%;
            max-width: 500px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
        }

        .auth-header {
            background: linear-gradient(135deg, #032B44 0%, #024a6b 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .auth-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .auth-header p {
            opacity: 0.9;
            font-size: 16px;
        }

        .auth-body {
            padding: 40px 30px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #032B44;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s;
            background: rgba(248, 249, 250, 0.8);
        }

        .form-control:focus {
            outline: none;
            border-color: #032B44;
            background: white;
            box-shadow: 0 0 0 4px rgba(3, 43, 68, 0.1);
        }

        .btn {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #032B44 0%, #024a6b 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(3, 43, 68, 0.3);
        }

        .btn-secondary {
            background: rgba(248, 249, 250, 0.8);
            color: #032B44;
            border: 2px solid #e0e0e0;
        }

        .btn-secondary:hover {
            background: white;
            border-color: #032B44;
        }

        .alert {
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-error {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.2);
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
            border: 1px solid rgba(40, 167, 69, 0.2);
        }

        .alert-close {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            margin-left: auto;
            color: inherit;
        }

        .auth-links {
            text-align: center;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #e0e0e0;
        }

        .auth-links a {
            color: #032B44;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }

        .auth-links a:hover {
            color: #024a6b;
        }

        .logo {
            font-size: 24px;
            font-weight: 700;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            justify-content: center;
        }

        .logo i {
            font-size: 28px;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            
            .auth-container {
                max-width: 100%;
            }
            
            .auth-header {
                padding: 30px 20px;
            }
            
            .auth-body {
                padding: 30px 20px;
            }
            
            .auth-header h1 {
                font-size: 24px;
            }
        }

        /* Row and Column Layout */
        .row {
            display: flex;
            gap: 16px;
        }

        .col-6 {
            flex: 1;
        }

        .col-12 {
            width: 100%;
        }

        /* Checkbox and Radio Styles */
        .form-check {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 16px;
        }

        .form-check input[type="checkbox"],
        .form-check input[type="radio"] {
            width: 18px;
            height: 18px;
            accent-color: #032B44;
        }

        .form-check-label {
            font-size: 14px;
            color: #666;
        }

        /* Text Helpers */
        .text-center {
            text-align: center;
        }

        .text-muted {
            color: #666;
            font-size: 14px;
        }

        .mb-3 {
            margin-bottom: 16px;
        }

        .mt-3 {
            margin-top: 16px;
        }

        /* Password Strength Indicator */
        .password-strength {
            margin-top: 8px;
            height: 4px;
            border-radius: 2px;
            background: #e0e0e0;
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            transition: all 0.3s;
            border-radius: 2px;
        }

        .strength-weak { background: #dc3545; width: 25%; }
        .strength-fair { background: #ffc107; width: 50%; }
        .strength-good { background: #17a2b8; width: 75%; }
        .strength-strong { background: #28a745; width: 100%; }
        
        /* Password Toggle Styles */
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
    
    <!-- Universal Password Toggle Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Find all password inputs
        const passwordInputs = document.querySelectorAll('input[type="password"]');
        
        passwordInputs.forEach(function(input) {
            // Skip if already wrapped
            if (input.parentElement.classList.contains('password-wrapper')) {
                return;
            }
            
            // Create wrapper
            const wrapper = document.createElement('div');
            wrapper.className = 'password-wrapper';
            
            // Insert wrapper before input
            input.parentNode.insertBefore(wrapper, input);
            
            // Move input into wrapper
            wrapper.appendChild(input);
            
            // Create toggle icon
            const toggleIcon = document.createElement('i');
            toggleIcon.className = 'fas fa-eye password-toggle-icon';
            toggleIcon.setAttribute('aria-label', 'Toggle password visibility');
            toggleIcon.setAttribute('role', 'button');
            toggleIcon.setAttribute('tabindex', '0');
            
            // Add icon to wrapper
            wrapper.appendChild(toggleIcon);
            
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
    <?php include __DIR__ . '/translation.php'; ?>
</head>
<body>
