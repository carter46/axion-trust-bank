<?php
/**
 * User Lookup API for Internal Transfers
 * Looks up user by email and returns account information
 */

// Prevent output before JSON
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

ob_end_clean();

header('Content-Type: application/json');

// Ensure session is started (API endpoints may be called directly)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get request data
$input = json_decode(file_get_contents('php://input'), true);
$email = trim($input['email'] ?? '');

// Validate email
if (empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Email is required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

// Prevent self-transfer
$currentUserEmail = $_SESSION['user_email'] ?? '';
if (strtolower($email) === strtolower($currentUserEmail)) {
    echo json_encode(['success' => false, 'message' => 'You cannot transfer to yourself']);
    exit;
}

try {
    $db = Database::getInstance();
    
    // Look up user by email and return ANY active account (not only checking).
    // Deterministic selection:
    // - Prefer an active checking account if present
    // - Otherwise pick the oldest active account
    $sql = "SELECT 
                u.id, u.full_name, u.email, u.status,
                a.account_number, a.account_type, a.currency
            FROM users u
            LEFT JOIN accounts a ON a.id = (
                SELECT a2.id
                FROM accounts a2
                WHERE a2.user_id = u.id
                  AND a2.status = 'active'
                ORDER BY 
                    CASE 
                        WHEN a2.account_type = 'checking' THEN 1
                        WHEN a2.account_type = 'savings' THEN 2
                        WHEN a2.account_type = 'business' THEN 3
                        ELSE 4
                    END,
                    a2.created_at ASC,
                    a2.id ASC
                LIMIT 1
            )
            WHERE LOWER(u.email) = LOWER(?)
            LIMIT 1";
    
    $stmt = $db->query($sql, [$email]);
    $user = $stmt->fetch();
    
    if (!$user) {
        echo json_encode([
            'success' => false, 
            'message' => 'No user found with this email address'
        ]);
        exit;
    }
    
    // Check if user account is active (allow pending accounts for transfers)
    if (!in_array($user['status'], ['active', 'pending'])) {
        echo json_encode([
            'success' => false, 
            'message' => 'This user account is not active'
        ]);
        exit;
    }
    
    // Check if user has an active account
    if (empty($user['account_number'])) {
        echo json_encode([
            'success' => false, 
            'message' => 'This user does not have an active account'
        ]);
        exit;
    }
    
    // Return user information
    echo json_encode([
        'success' => true,
        'message' => 'Recipient found successfully',
        'data' => [
            'full_name' => $user['full_name'],
            'account_number' => $user['account_number'],
            'account_type' => ucfirst($user['account_type']),
            'currency' => $user['currency']
        ]
    ]);
    exit;
    
} catch (Exception $e) {
    error_log("User Lookup Error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'An error occurred while looking up the user',
        'error' => $e->getMessage()
    ]);
    exit;
}
