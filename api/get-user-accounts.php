<?php
// Prevent any output before JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);
ob_start();

try {
    require_once __DIR__ . '/../config/config.php';
    require_once __DIR__ . '/../includes/functions.php';
    
    // Clear any accidental output
    ob_end_clean();
    header('Content-Type: application/json');
} catch (Exception $e) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Setup error: ' . $e->getMessage()]);
    exit;
} catch (Error $e) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Fatal setup error: ' . $e->getMessage()]);
    exit;
}

// Check if user is logged in (admin or regular user)
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// If user is not admin, they can only access their own accounts
$isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit;
}

$requestedUserId = intval($input['user_id'] ?? 0);
$loggedInUserId = intval($_SESSION['user_id']);

// If not admin, user can only access their own accounts
if (!$isAdmin && $requestedUserId !== $loggedInUserId) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized: You can only access your own accounts']);
    exit;
}

// Use requested user ID if admin, otherwise use logged-in user's ID
$userId = $isAdmin ? $requestedUserId : $loggedInUserId;

if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'User ID is required']);
    exit;
}

try {
    $db = Database::getInstance();
    
    if (!$db) {
        throw new Exception('Database connection failed');
    }
    
    // Get user accounts (ensure user_id is valid)
    if ($userId <= 0) {
        throw new Exception('Invalid user ID');
    }
    
    $sql = "SELECT id, account_number, account_type, balance, status 
            FROM accounts 
            WHERE user_id = ? AND status = 'active'
            ORDER BY account_type, created_at ASC";
    
    $stmt = $db->query($sql, [$userId]);
    
    if ($stmt === false) {
        throw new Exception('Database query failed');
    }
    
    $accounts = $stmt->fetchAll();
    
    // Ensure clean JSON output
    if (ob_get_level() > 0) {
        ob_clean();
    }
    
    echo json_encode([
        'success' => true,
        'accounts' => $accounts
    ]);
    
} catch (Exception $e) {
    // Ensure clean JSON output on error
    if (ob_get_level() > 0) {
        ob_clean();
    }
    
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Throwable $e) {
    // Handle any other throwable
    if (ob_get_level() > 0) {
        ob_clean();
    }
    
    echo json_encode([
        'success' => false,
        'message' => 'Unexpected error: ' . $e->getMessage()
    ]);
}
