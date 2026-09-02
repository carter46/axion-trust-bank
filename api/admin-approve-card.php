<?php
// Start output buffering immediately to catch any errors
ob_start();

try {
    // Log the request
    error_log('🟢 ADMIN APPROVE CARD API DEBUG: Request received at ' . date('Y-m-d H:i:s'));

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

    error_log('🟢 ADMIN APPROVE CARD API DEBUG: User ID found: ' . ($userId ?? 'not set'));
    error_log('🟢 ADMIN APPROVE CARD API DEBUG: User role found: ' . ($userRole ?? 'not set'));

    if (!$userId || $userRole !== 'admin') {
        error_log('🟢 ADMIN APPROVE CARD API DEBUG: User not admin or not logged in');
        error_log('🟢 ADMIN APPROVE CARD API DEBUG: Available session keys: ' . implode(', ', array_keys($_SESSION)));
        
        // Clean any output before sending JSON
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Unauthorized - Admin access required']);
        exit;
    }

    $adminId = $userId; // Corrected assignment

    // Get card ID and admin notes from JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    $cardId = $input['card_id'] ?? null;
    $adminNotes = $input['admin_notes'] ?? '';
    error_log('🟢 ADMIN APPROVE CARD API DEBUG: Card ID received: ' . ($cardId ?? 'not set'));
    error_log('🟢 ADMIN APPROVE CARD API DEBUG: Admin notes: ' . ($adminNotes ?: 'none'));

    if (!$cardId) {
        error_log('🟢 ADMIN APPROVE CARD API DEBUG: No card ID provided');
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Card ID is required']);
        exit;
    }

    // Validate card ID
    if (!is_numeric($cardId)) {
        error_log('🟢 ADMIN APPROVE CARD API DEBUG: Invalid card ID format');
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid card ID']);
        exit;
    }

    enforceDemoUserAdminAccessForCardId((int)$cardId);

    try {
        // Initialize models
        $cardModel = new Card();
        $adminModel = new Admin();

        error_log('🟢 ADMIN APPROVE CARD API DEBUG: Models initialized successfully');

        // Get card details
        $card = $cardModel->findById($cardId);
        error_log('🟢 ADMIN APPROVE CARD API DEBUG: Card lookup result: ' . ($card ? 'found' : 'not found'));

        if (!$card) {
            error_log('🟢 ADMIN APPROVE CARD API DEBUG: Card not found in database');
            ob_end_clean();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Card not found']);
            exit;
        }

        error_log('🟢 ADMIN APPROVE CARD API DEBUG: Card found - ID: ' . $card['id'] . ', Status: ' . $card['status'] . ', User ID: ' . $card['user_id']);

        // Check if card is already approved
        if ($card['status'] === 'active') {
            error_log('🟢 ADMIN APPROVE CARD API DEBUG: Card is already active');
            ob_end_clean();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Card is already approved']);
            exit;
        }

        // Check if card is not pending
        if ($card['status'] !== 'pending') {
            error_log('🟢 ADMIN APPROVE CARD API DEBUG: Card is not pending - status: ' . $card['status']);
            ob_end_clean();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Only pending cards can be approved']);
            exit;
        }

        // Approve the card
        $approveResult = $cardModel->approve($cardId);
        error_log('🟢 ADMIN APPROVE CARD API DEBUG: Approve result: ' . ($approveResult ? 'success' : 'failed'));

        if (!$approveResult) {
            error_log('🟢 ADMIN APPROVE CARD API DEBUG: Card approval failed');
            ob_end_clean();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to approve card']);
            exit;
        }

        // Log admin action
        error_log("Admin $adminId approved card application #$cardId");

        // NOTE:
        // Card::approve() already creates notifications and sends emails.
        // This API previously attempted to send an additional notification using a different
        // signature, which could trigger a PHP fatal and make the browser see a 500 even
        // though the card was actually approved. Keep the API response clean and reliable.

        error_log('🟢 ADMIN APPROVE CARD API DEBUG: Card approval completed successfully');
        
        // Clean any output before sending JSON
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Card approved successfully']);

    } catch (\Throwable $e) {
        error_log('🟢 ADMIN APPROVE CARD API DEBUG: Throwable in card approval: ' . $e->getMessage());
        error_log('🟢 ADMIN APPROVE CARD API DEBUG: Stack trace: ' . $e->getTraceAsString());
        
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'An error occurred while approving the card']);
    }

} catch (\Throwable $e) {
    error_log('🟢 ADMIN APPROVE CARD API DEBUG: Fatal/Throwable error: ' . $e->getMessage());
    error_log('🟢 ADMIN APPROVE CARD API DEBUG: Stack trace: ' . $e->getTraceAsString());
    
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Internal server error']);
}
?>