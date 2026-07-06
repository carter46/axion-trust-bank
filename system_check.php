<?php
// Comprehensive System Check for SecureBank Online Banking
echo "<h1>🔍 SecureBank System Check</h1>";
echo "<style>
body { font-family: Arial, sans-serif; margin: 20px; }
.success { color: green; }
.error { color: red; }
.warning { color: orange; }
.info { color: blue; }
.section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
</style>";

// 1. PHP Environment Check
echo "<div class='section'>";
echo "<h2>1. PHP Environment</h2>";
echo "<p class='success'>✓ PHP Version: " . PHP_VERSION . "</p>";
echo "<p class='info'>✓ Server: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p class='info'>✓ Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p class='info'>✓ Current Directory: " . __DIR__ . "</p>";

// Check required PHP extensions
$required_extensions = ['pdo', 'pdo_mysql', 'openssl', 'mbstring', 'curl'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<p class='success'>✓ Extension '$ext' loaded</p>";
    } else {
        echo "<p class='error'>✗ Extension '$ext' NOT loaded</p>";
    }
}
echo "</div>";

// 2. File Structure Check
echo "<div class='section'>";
echo "<h2>2. File Structure</h2>";
$required_files = [
    'index.php',
    'config/config.php',
    'config/database.php',
    'includes/functions.php',
    'includes/security.php',
    '.htaccess',
    'views/layouts/header.php',
    'views/layouts/footer.php',
    'views/home/index.php',
    'controllers/HomeController.php',
    'controllers/AuthController.php',
    'models/User.php'
];

foreach ($required_files as $file) {
    if (file_exists($file)) {
        echo "<p class='success'>✓ $file exists</p>";
    } else {
        echo "<p class='error'>✗ $file MISSING</p>";
    }
}

// Check directories
$required_dirs = ['config', 'controllers', 'models', 'views', 'assets', 'includes'];
foreach ($required_dirs as $dir) {
    if (is_dir($dir)) {
        echo "<p class='success'>✓ Directory '$dir' exists</p>";
    } else {
        echo "<p class='error'>✗ Directory '$dir' MISSING</p>";
    }
}
echo "</div>";

// 3. Configuration Check
echo "<div class='section'>";
echo "<h2>3. Configuration</h2>";
try {
    require_once 'config/config.php';
    echo "<p class='success'>✓ config.php loaded successfully</p>";
    
    // Check constants
    $constants = ['SITE_URL', 'DB_HOST', 'DB_NAME', 'DB_USER', 'SITE_NAME'];
    foreach ($constants as $const) {
        if (defined($const)) {
            $value = constant($const);
            if ($const === 'DB_PASS') {
                echo "<p class='success'>✓ $const is set (hidden for security)</p>";
            } else {
                echo "<p class='success'>✓ $const = " . htmlspecialchars($value) . "</p>";
            }
        } else {
            echo "<p class='error'>✗ $const NOT defined</p>";
        }
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Error loading config: " . $e->getMessage() . "</p>";
}
echo "</div>";

// 4. Database Connection Check
echo "<div class='section'>";
echo "<h2>4. Database Connection</h2>";
try {
    require_once 'config/database.php';
    $db = Database::getInstance();
    $conn = $db->getConnection();
    echo "<p class='success'>✓ Database connection successful</p>";
    
    // Test query
    $stmt = $conn->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "<p class='success'>✓ Database query successful - Users count: " . $result['count'] . "</p>";
    
    // Check if demo users exist
    $stmt = $conn->query("SELECT email, role FROM users WHERE email IN ('user@demo.com', 'admin@demo.com')");
    $users = $stmt->fetchAll();
    foreach ($users as $user) {
        echo "<p class='success'>✓ Demo user found: " . $user['email'] . " (" . $user['role'] . ")</p>";
    }
    
    // Check system settings
    $stmt = $conn->query("SELECT setting_key, setting_value FROM system_settings WHERE setting_key = 'site_url'");
    $setting = $stmt->fetch();
    if ($setting) {
        echo "<p class='success'>✓ Site URL setting: " . $setting['setting_value'] . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>✗ Database error: " . $e->getMessage() . "</p>";
}
echo "</div>";

// 5. Security Functions Check
echo "<div class='section'>";
echo "<h2>5. Security Functions</h2>";
try {
    require_once 'includes/security.php';
    echo "<p class='success'>✓ Security class loaded</p>";
    
    // Test CSRF token generation
    $token = Security::generateCSRFToken();
    if ($token) {
        echo "<p class='success'>✓ CSRF token generation working</p>";
    }
    
    // Test password hashing
    $hashed = Security::hashPassword('test123');
    if ($hashed && Security::verifyPassword('test123', $hashed)) {
        echo "<p class='success'>✓ Password hashing/verification working</p>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>✗ Security error: " . $e->getMessage() . "</p>";
}
echo "</div>";

// 6. Routing Check
echo "<div class='section'>";
echo "<h2>6. Routing & Controllers</h2>";
try {
    require_once 'index.php';
    // This won't execute fully due to routing, but we can check if files exist
    $controllers = ['HomeController', 'AuthController', 'DashboardController', 'AdminController'];
    foreach ($controllers as $controller) {
        $file = "controllers/$controller.php";
        if (file_exists($file)) {
            echo "<p class='success'>✓ $controller exists</p>";
        } else {
            echo "<p class='error'>✗ $controller MISSING</p>";
        }
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Routing error: " . $e->getMessage() . "</p>";
}
echo "</div>";

// 7. Demo Credentials
echo "<div class='section'>";
echo "<h2>7. Demo Credentials</h2>";
echo "<p class='info'><strong>User Account:</strong></p>";
echo "<p class='info'>Email: user@demo.com</p>";
echo "<p class='info'>Password: password</p>";
echo "<p class='info'>Role: user</p>";
echo "<br>";
echo "<p class='info'><strong>Admin Account:</strong></p>";
echo "<p class='info'>Email: admin@demo.com</p>";
echo "<p class='info'>Password: password</p>";
echo "<p class='info'>Role: admin</p>";
echo "</div>";

// 8. URLs to Test
echo "<div class='section'>";
echo "<h2>8. Test URLs</h2>";
echo "<p class='info'>Homepage (Login/Register): <a href='" . SITE_URL . "/'>" . SITE_URL . "/</a></p>";
echo "<p class='info'>User Login: <a href='" . SITE_URL . "/auth/login'>" . SITE_URL . "/auth/login</a></p>";
echo "<p class='info'>Admin Dashboard: <a href='" . SITE_URL . "/admin'>" . SITE_URL . "/admin</a></p>";
echo "<p class='info'>Dashboard: <a href='" . SITE_URL . "/dashboard'>" . SITE_URL . "/dashboard</a></p>";
echo "</div>";

echo "<div class='section'>";
echo "<h2>🎯 System Status</h2>";
echo "<p class='success'><strong>If all checks above show green, your banking platform is ready to use!</strong></p>";
echo "<p class='info'>Upload this file to your server and run it to verify everything is working correctly.</p>";
echo "</div>";
?>
