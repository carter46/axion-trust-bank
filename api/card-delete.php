<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Log the request
error_log('🔴 CARD DELETE API DEBUG: Request received at ' . date('Y-m-d H:i:s'));

// Start output buffering to prevent HTML errors from breaking JSON
ob_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../models/Card.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    error_log('🔴 CARD DELETE API DEBUG: User not logged in');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];
error_log('🔴 CARD DELETE API DEBUG: User ID: ' . $userId);

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
error_log('🔴 CARD DELETE API DEBUG: Raw input: ' . file_get_contents('php://input'));
error_log('🔴 CARD DELETE API DEBUG: Parsed input: ' . json_encode($input));

$cardId = intval($input['card_id'] ?? 0);
error_log('🔴 CARD DELETE API DEBUG: Card ID: ' . $cardId);

if (!$cardId) {
    error_log('🔴 CARD DELETE API DEBUG: No card ID provided');
    echo json_encode(['success' => false, 'message' => 'Card ID is required']);
    exit;
}

try {
    error_log('🔴 CARD DELETE API DEBUG: Starting card deletion process...');
    
    $cardModel = new Card();
    $card = $cardModel->findById($cardId);
    
    error_log('🔴 CARD DELETE API DEBUG: Card found: ' . json_encode($card));
    
    // Verify card belongs to user
    if (!$card || $card['user_id'] != $userId) {
        error_log('🔴 CARD DELETE API DEBUG: Card not found or does not belong to user');
        error_log('🔴 CARD DELETE API DEBUG: Card user_id: ' . ($card['user_id'] ?? 'null') . ', Session user_id: ' . $userId);
        
        // Clean any output before sending JSON
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Card not found']);
        exit;
    }
    
    // Check if card can be deleted
    if ($card['status'] === 'cancelled') {
        error_log('🔴 CARD DELETE API DEBUG: Card is already cancelled');
        
        // Clean any output before sending JSON
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Card is already deleted']);
        exit;
    }
    
    error_log('🔴 CARD DELETE API DEBUG: Proceeding with card deletion...');
    
    // Delete the card
    $result = $cardModel->delete($cardId);
    
    error_log('🔴 CARD DELETE API DEBUG: Delete result: ' . json_encode($result));
    
    if ($result['success']) {
        error_log('🔴 CARD DELETE API DEBUG: Card deleted successfully');
        
        // Clean any output before sending JSON
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Card deleted successfully'
        ]);
        exit;
    } else {
        error_log('🔴 CARD DELETE API DEBUG: Card deletion failed: ' . $result['message']);
        
        // Clean any output before sending JSON
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $result['message']]);
        exit;
    }
    
} catch (Exception $e) {
    error_log('🔴 CARD DELETE API DEBUG: Exception occurred: ' . $e->getMessage());
    error_log('🔴 CARD DELETE API DEBUG: Exception trace: ' . $e->getTraceAsString());
    
    // Clean any output before sending JSON
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'An error occurred', 'error' => $e->getMessage()]);
    exit;
}
