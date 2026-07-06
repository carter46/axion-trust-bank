<?php
// Start output buffering immediately to catch any errors
ob_start();

try {
    require_once __DIR__ . '/../config/config.php';
    require_once __DIR__ . '/../includes/functions.php';
    require_once __DIR__ . '/../models/Card.php';

    // Prevent output before JSON
    error_reporting(0);
    ini_set('display_errors', 0);

    // Start session
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Check if user is logged in
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    // Get card ID from request
    $cardId = $_GET['card_id'] ?? null;
    if (!$cardId || !is_numeric($cardId)) {
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid card ID']);
        exit;
    }

    // Initialize card model
    $cardModel = new Card();
    
    // Get fresh card details from database
    $card = $cardModel->findById($cardId);
    
    // Verify user owns this card
    if (!$card || $card['user_id'] != $userId) {
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Card not found']);
        exit;
    }

    // Decrypt card number and CVV for display
    if (!empty($card['card_number'])) {
        $card['card_number'] = decryptData($card['card_number']);
        // Remove spaces and format: XXXX XXXX XXXX XXXX
        $cardNumber = str_replace(' ', '', $card['card_number']);
        $card['card_number'] = chunk_split($cardNumber, 4, ' ');
        $card['card_number'] = trim($card['card_number']);
    }
    if (!empty($card['cvv'])) {
        $card['cvv'] = decryptData($card['cvv']);
    }

    // Clean any output before sending JSON
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'card' => $card
    ]);

} catch (Exception $e) {
    error_log("Error in get-card-details.php: " . $e->getMessage());
    
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Internal server error']);
}
?>
