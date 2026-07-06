<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Log the request
error_log('❄️ CARD FREEZE API DEBUG: Request received at ' . date('Y-m-d H:i:s'));

// Start output buffering to prevent HTML errors from breaking JSON
ob_start();

try {
    require_once __DIR__ . '/../config/config.php';
    require_once __DIR__ . '/../includes/functions.php';
    require_once __DIR__ . '/../models/Card.php';
    
    // Verify session is working
    if (!isset($_SESSION['user_id'])) {
        error_log('❄️ CARD FREEZE API DEBUG: No session user_id found');
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Session expired. Please login again.']);
        exit;
    }
    
} catch (Exception $e) {
    error_log('❄️ CARD FREEZE API DEBUG: Configuration error: ' . $e->getMessage());
    error_log('❄️ CARD FREEZE API DEBUG: Error trace: ' . $e->getTraceAsString());
    
    // Clean any output before sending JSON
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Configuration error: ' . $e->getMessage()]);
    exit;
}

// Session check already done above

$userId = $_SESSION['user_id'];
error_log('❄️ CARD FREEZE API DEBUG: User ID: ' . $userId);

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
error_log('❄️ CARD FREEZE API DEBUG: Raw input: ' . file_get_contents('php://input'));
error_log('❄️ CARD FREEZE API DEBUG: Parsed input: ' . json_encode($input));

$cardId = intval($input['card_id'] ?? 0);
error_log('❄️ CARD FREEZE API DEBUG: Card ID: ' . $cardId);

if (!$cardId) {
    error_log('❄️ CARD FREEZE API DEBUG: No card ID provided');
    
    // Clean any output before sending JSON
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Card ID is required']);
    exit;
}

try {
    error_log('❄️ CARD FREEZE API DEBUG: Starting card freeze process...');
    
    $cardModel = new Card();
    $card = $cardModel->findById($cardId);
    
    error_log('❄️ CARD FREEZE API DEBUG: Card found: ' . json_encode($card));
    
    // Verify card belongs to user
    if (!$card || $card['user_id'] != $userId) {
        error_log('❄️ CARD FREEZE API DEBUG: Card not found or does not belong to user');
        error_log('❄️ CARD FREEZE API DEBUG: Card user_id: ' . ($card['user_id'] ?? 'null') . ', Session user_id: ' . $userId);
        
        // Clean any output before sending JSON
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Card not found']);
        exit;
    }
    
    // Check if card is already frozen
    if ($card['status'] === 'frozen') {
        error_log('❄️ CARD FREEZE API DEBUG: Card is already frozen');
        
        // Clean any output before sending JSON
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Card is already frozen']);
        exit;
    }
    
    // Check if card can be frozen
    if ($card['status'] === 'blocked' || $card['status'] === 'cancelled') {
        error_log('❄️ CARD FREEZE API DEBUG: Card cannot be frozen, status: ' . $card['status']);
        
        // Clean any output before sending JSON
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Cannot freeze a blocked or cancelled card']);
        exit;
    }
    
    error_log('❄️ CARD FREEZE API DEBUG: Proceeding with card freeze...');
    
    // Freeze the card
    if ($cardModel->freeze($cardId)) {
        error_log('❄️ CARD FREEZE API DEBUG: Card frozen successfully');
        
        // Clean any output before sending JSON
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Card frozen successfully'
        ]);
        exit;
    } else {
        error_log('❄️ CARD FREEZE API DEBUG: Card freeze failed');
        
        // Clean any output before sending JSON
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Failed to freeze card']);
        exit;
    }
    
} catch (Exception $e) {
    error_log('❄️ CARD FREEZE API DEBUG: Exception occurred: ' . $e->getMessage());
    error_log('❄️ CARD FREEZE API DEBUG: Exception trace: ' . $e->getTraceAsString());
    
    // Clean any output before sending JSON
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'An error occurred', 'error' => $e->getMessage()]);
    exit;
}
