<?php
/**
 * Complete Onboarding API
 * Marks user onboarding as completed
 */

// Prevent output before JSON
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

ob_end_clean();

header('Content-Type: application/json');

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

try {
    $userId = $_SESSION['user_id'];
    $db = Database::getInstance();
    
    // Update user onboarding status
    $sql = "UPDATE users SET onboarding_completed = 1, updated_at = NOW() WHERE id = ?";
    $db->query($sql, [$userId]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Onboarding completed successfully'
    ]);
    exit;
    
} catch (Exception $e) {
    error_log("Complete Onboarding Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while completing onboarding',
        'error' => $e->getMessage()
    ]);
    exit;
}
