<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Start output buffering to prevent HTML errors from breaking JSON
ob_start();

try {
    require_once __DIR__ . '/../config/config.php';
    require_once __DIR__ . '/../includes/functions.php';
    require_once __DIR__ . '/../models/Card.php';
} catch (Exception $e) {
    // Clean any output before sending JSON
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Configuration error: ' . $e->getMessage()]);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Clean any output before sending JSON
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$cardId = intval($input['card_id'] ?? 0);

if (!$cardId) {
    // Clean any output before sending JSON
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Card ID is required']);
    exit;
}

try {
    $cardModel = new Card();
    $card = $cardModel->findById($cardId);
    
    // Verify card belongs to user
    if (!$card || $card['user_id'] != $userId) {
        // Clean any output before sending JSON
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Card not found']);
        exit;
    }
    
    // Check if card is frozen
    if ($card['status'] !== 'frozen') {
        // Clean any output before sending JSON
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Card is not frozen']);
        exit;
    }
    
    // Unfreeze the card
    if ($cardModel->unfreeze($cardId)) {
        // Clean any output before sending JSON
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Card unfrozen successfully'
        ]);
        exit;
    } else {
        // Clean any output before sending JSON
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Failed to unfreeze card']);
        exit;
    }
    
} catch (Exception $e) {
    error_log('Card Unfreeze Error: ' . $e->getMessage());
    
    // Clean any output before sending JSON
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'An error occurred', 'error' => $e->getMessage()]);
    exit;
}
