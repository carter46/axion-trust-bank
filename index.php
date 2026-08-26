<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/security.php';
require_once 'includes/system-settings.php';

// Check maintenance mode (allow admin and auth pages to bypass)
$maintenanceMode = false;
try {
    $systemSettings = SystemSettings::getInstance();
    $maintenanceMode = $systemSettings->isMaintenanceMode();
    
    // Allow access to admin panel, auth pages, and API endpoints during maintenance
    $allowedRoutes = ['auth', 'admin', 'api'];
    $route = isset($_GET['route']) ? $_GET['route'] : 'home';
    $routeParts = explode('/', $route);
    $isAllowedRoute = in_array($routeParts[0] ?? '', $allowedRoutes);
    $isAdminUser = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    
    if ($maintenanceMode && !$isAllowedRoute && !$isAdminUser) {
        $maintenanceMessage = $systemSettings->get('maintenance_message', 'System maintenance in progress. Please check back soon.');
        http_response_code(503);
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Maintenance Mode</title>
            <style>
                body {
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    min-height: 100vh;
                    margin: 0;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: #333;
                }
                .maintenance-container {
                    text-align: center;
                    background: white;
                    padding: 60px 40px;
                    border-radius: 12px;
                    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
                    max-width: 500px;
                }
                .maintenance-container h1 {
                    font-size: 36px;
                    margin-bottom: 20px;
                    color: #667eea;
                }
                .maintenance-container p {
                    font-size: 18px;
                    color: #666;
                    line-height: 1.6;
                }
                .icon {
                    font-size: 64px;
                    margin-bottom: 20px;
                }
            </style>
        </head>
        <body>
            <div class="maintenance-container">
                <div class="icon">🔧</div>
                <h1>Maintenance Mode</h1>
                <p><?php echo htmlspecialchars($maintenanceMessage); ?></p>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
} catch (Exception $e) {
    // If settings can't be loaded, continue normally (fail open)
    error_log("Maintenance mode check failed: " . $e->getMessage());
}

// Autoload classes
spl_autoload_register(function ($class_name) {
    $paths = [
        'classes/',
        'controllers/',
        'models/'
    ];
    
    foreach ($paths as $path) {
        $file = BASE_PATH . '/' . $path . $class_name . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Security initialized from config.php for all requests
$route = isset($_GET['route']) ? $_GET['route'] : 'home';
$route = Security::sanitize($route);

// Parse route
$parts = explode('/', $route);
$controller = !empty($parts[0]) ? $parts[0] : 'home';
$action = isset($parts[1]) ? $parts[1] : 'index';
$param = isset($parts[2]) ? $parts[2] : null;
$param2 = isset($parts[3]) ? $parts[3] : null;

// Convert kebab-case to camelCase for action names
// e.g., "admin-settings" becomes "adminSettings"
if (strpos($action, '-') !== false) {
    $action = lcfirst(str_replace('-', '', ucwords($action, '-')));
}

// Map routes to controllers
$controllerMap = [
    'home' => 'HomeController',
    'auth' => 'AuthController',
    'dashboard' => 'DashboardController',
    'account' => 'AccountController',
    'accounts' => 'HomeController',
    'transfer' => 'TransferController',
    'transaction' => 'TransactionController',
    'card' => 'CardController',
    'cards' => 'HomeController',
    'loan' => 'LoanController',
    'loans' => 'HomeController',
    'profile' => 'ProfileController',
    'admin' => 'AdminController',
    'investment' => 'InvestmentController',
    'investments' => 'HomeController',
    'partnership' => 'HomeController',
    'about' => 'HomeController',
    'services' => 'HomeController',
    'contact' => 'HomeController',
    'charity' => 'HomeController',
    'security' => 'HomeController',
    'faqs' => 'HomeController',
    'terms' => 'HomeController',
    'help-center' => 'HomeController',
    'investor-portal' => 'HomeController'
];

try {
    // Check if controller exists
    if (!isset($controllerMap[$controller])) {
        throw new Exception('Page not found', 404);
    }
    
    $controllerClass = $controllerMap[$controller];
    
    // Special handling: If action is 'index' and controller maps to HomeController,
    // and the controller name matches a method in HomeController, use the controller name as action
    // This handles routes like /partnership, /about, /help-center, etc.
    if ($action === 'index' && $controllerClass === 'HomeController') {
        // Map of route names to method names (for kebab-case routes)
        $homeControllerMethodMap = [
            'partnership' => 'partnership',
            'about' => 'about',
            'contact' => 'contact',
            'charity' => 'charity',
            'services' => 'services',
            'accounts' => 'accounts',
            'cards' => 'cards',
            'loans' => 'loans',
            'investments' => 'investments',
            'security' => 'security',
            'faqs' => 'faqs',
            'terms' => 'terms',
            'help-center' => 'helpCenter',
            'investor-portal' => 'investorPortal'
        ];
        
        // Handle kebab-case to camelCase for method names
        $methodName = isset($homeControllerMethodMap[$controller]) 
            ? $homeControllerMethodMap[$controller] 
            : $controller;
        
        // Check if method exists before overriding action
        if (class_exists($controllerClass)) {
            $tempController = new $controllerClass();
            if (method_exists($tempController, $methodName)) {
                $action = $methodName;
            }
        }
    }
    
    // Check if controller file exists
    if (!class_exists($controllerClass)) {
        throw new Exception('Controller not found', 404);
    }
    
    // Instantiate controller
    $controllerObj = new $controllerClass();
    
    // Check if method exists
    if (!method_exists($controllerObj, $action)) {
        throw new Exception('Action not found', 404);
    }
    
    // Call method - handle methods with 2 parameters (e.g., fundCrypto)
    if ($param !== null && $param2 !== null) {
        $controllerObj->$action($param, $param2);
    } elseif ($param !== null) {
        $controllerObj->$action($param);
    } else {
        $controllerObj->$action();
    }
    
} catch (Exception $e) {
    // Error handling
    $errorCode = $e->getCode() ?: 500;
    http_response_code($errorCode);
    
    if ($errorCode == 404) {
        include 'views/errors/404.php';
    } else {
        include 'views/errors/500.php';
    }
}
