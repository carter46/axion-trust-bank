<?php
// Start output buffering immediately to catch any errors
ob_start();

try {
    // Log the request
    error_log('🔴 ADMIN REJECT CARD API DEBUG: Request received at ' . date('Y-m-d H:i:s'));

    require_once __DIR__ . '/../config/config.php';
    require_once __DIR__ . '/../includes/functions.php';
    require_once __DIR__ . '/../models/Card.php';
    require_once __DIR__ . '/../models/Admin.php';

    // Prevent output before JSON - AFTER config includes
    error_reporting(0);
    ini_set('display_errors', 0);

    // Start session
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Check if user is admin
    $userId = $_SESSION['user_id'] ?? null;
    $userRole = $_SESSION['user_role'] ?? null;

    error_log('🔴 ADMIN REJECT CARD API DEBUG: User ID found: ' . ($userId ?? 'not set'));
    error_log('🔴 ADMIN REJECT CARD API DEBUG: User role found: ' . ($userRole ?? 'not set'));

    if (!$userId || $userRole !== 'admin') {
        error_log('🔴 ADMIN REJECT CARD API DEBUG: User not admin or not logged in');
        error_log('🔴 ADMIN REJECT CARD API DEBUG: Available session keys: ' . implode(', ', array_keys($_SESSION)));
        
        // Clean any output before sending JSON
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Unauthorized - Admin access required']);
        exit;
    }

    $adminId = $userId; // Corrected assignment

    // Get card ID and rejection reason from JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    $cardId = $input['card_id'] ?? null;
    $rejectionReason = $input['rejection_reason'] ?? '';
    error_log('🔴 ADMIN REJECT CARD API DEBUG: Card ID received: ' . ($cardId ?? 'not set'));
    error_log('🔴 ADMIN REJECT CARD API DEBUG: Rejection reason: ' . ($rejectionReason ?: 'none'));

    if (!$cardId) {
        error_log('🔴 ADMIN REJECT CARD API DEBUG: No card ID provided');
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Card ID is required']);
        exit;
    }

    // Validate card ID
    if (!is_numeric($cardId)) {
        error_log('🔴 ADMIN REJECT CARD API DEBUG: Invalid card ID format');
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid card ID']);
        exit;
    }

    try {
        // Initialize models
        $cardModel = new Card();
        $adminModel = new Admin();

        error_log('🔴 ADMIN REJECT CARD API DEBUG: Models initialized successfully');

        // Get card details
        $card = $cardModel->findById($cardId);
        error_log('🔴 ADMIN REJECT CARD API DEBUG: Card lookup result: ' . ($card ? 'found' : 'not found'));

        if (!$card) {
            error_log('🔴 ADMIN REJECT CARD API DEBUG: Card not found in database');
            ob_end_clean();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Card not found']);
            exit;
        }

        error_log('🔴 ADMIN REJECT CARD API DEBUG: Card found - ID: ' . $card['id'] . ', Status: ' . $card['status'] . ', User ID: ' . $card['user_id']);

        // Check if card is already rejected
        if ($card['status'] === 'rejected') {
            error_log('🔴 ADMIN REJECT CARD API DEBUG: Card is already rejected');
            ob_end_clean();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Card is already rejected']);
            exit;
        }

        // Check if card is not pending
        if ($card['status'] !== 'pending') {
            error_log('🔴 ADMIN REJECT CARD API DEBUG: Card is not pending - status: ' . $card['status']);
            ob_end_clean();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Only pending cards can be rejected']);
            exit;
        }

        // Reject the card (pass rejection reason)
        $rejectResult = $cardModel->reject($cardId, $rejectionReason);
        error_log('🔴 ADMIN REJECT CARD API DEBUG: Reject result: ' . ($rejectResult ? 'success' : 'failed'));

        if (!$rejectResult) {
            error_log('🔴 ADMIN REJECT CARD API DEBUG: Card rejection failed');
            ob_end_clean();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to reject card']);
            exit;
        }

        // Log admin action
        error_log("Admin $adminId rejected card application #$cardId");

        error_log('🔴 ADMIN REJECT CARD API DEBUG: Card rejection completed successfully');
        
        // Clean any output before sending JSON
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Card rejected successfully']);

    } catch (Exception $e) {
        error_log('🔴 ADMIN REJECT CARD API DEBUG: Exception in card rejection: ' . $e->getMessage());
        error_log('🔴 ADMIN REJECT CARD API DEBUG: Stack trace: ' . $e->getTraceAsString());
        
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'An error occurred while rejecting the card']);
    }

} catch (Exception $e) {
    error_log('🔴 ADMIN REJECT CARD API DEBUG: Fatal error: ' . $e->getMessage());
    error_log('🔴 ADMIN REJECT CARD API DEBUG: Stack trace: ' . $e->getTraceAsString());
    
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Internal server error']);
}
?>