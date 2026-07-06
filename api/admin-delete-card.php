<?php
// Start output buffering immediately to catch any errors
ob_start();

try {
    // Log the request
    error_log('🔴 ADMIN DELETE CARD API DEBUG: Request received at ' . date('Y-m-d H:i:s'));

    require_once __DIR__ . '/../config/config.php';
    require_once __DIR__ . '/../includes/functions.php';
    require_once __DIR__ . '/../models/Card.php';
    require_once __DIR__ . '/../models/Admin.php';

    // Prevent output before JSON - AFTER config includes
    error_reporting(0);
    ini_set('display_errors', 0);

    // Start session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Debug session data
    error_log('🔴 ADMIN DELETE CARD API DEBUG: Session ID: ' . session_id());

    // Check if user is admin - use correct session variable names
    $userId = $_SESSION['user_id'] ?? null;
    $userRole = $_SESSION['user_role'] ?? null;

error_log('🔴 ADMIN DELETE CARD API DEBUG: User ID found: ' . ($userId ?? 'not set'));
error_log('🔴 ADMIN DELETE CARD API DEBUG: User role found: ' . ($userRole ?? 'not set'));

if (!$userId || $userRole !== 'admin') {
    error_log('🔴 ADMIN DELETE CARD API DEBUG: User not admin or not logged in');
    error_log('🔴 ADMIN DELETE CARD API DEBUG: Available session keys: ' . implode(', ', array_keys($_SESSION)));
    
    // Clean any output before sending JSON
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized - Admin access required']);
    exit;
}

    $adminId = $userId;
    error_log('🔴 ADMIN DELETE CARD API DEBUG: Admin ID: ' . $adminId);

    // Read request body ONCE (php://input is a stream)
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);
    error_log('🔴 ADMIN DELETE CARD API DEBUG: Raw input: ' . $rawInput);
    error_log('🔴 ADMIN DELETE CARD API DEBUG: Parsed input: ' . json_encode($input));

    $cardId = intval($input['card_id'] ?? 0);
    error_log('🔴 ADMIN DELETE CARD API DEBUG: Card ID: ' . $cardId);

if (!$cardId) {
    error_log('🔴 ADMIN DELETE CARD API DEBUG: No card ID provided');
    
    // Clean any output before sending JSON
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Card ID is required']);
    exit;
}

try {
    error_log('🔴 ADMIN DELETE CARD API DEBUG: Starting admin card deletion process...');
    
    $cardModel = new Card();
    $card = $cardModel->findById($cardId);

    error_log('🔴 ADMIN DELETE CARD API DEBUG: Card found: ' . json_encode($card));

    if (!$card) {
        error_log('🔴 ADMIN DELETE CARD API DEBUG: Card not found');
        
        // Clean any output before sending JSON
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Card not found']);
        exit;
    }

    // Log admin action before deleting
    // NOTE: Admin::logAdminAction() is private in models/Admin.php, calling it here causes a PHP fatal.
    // Use admin_logs table directly instead.
    error_log('🔴 ADMIN DELETE CARD API DEBUG: Logging admin action...');
    try {
        $db = Database::getInstance();
        $desc = "Admin deleted card {$cardId}";
        $db->query(
            "INSERT INTO admin_logs (admin_id, user_id, action, description, created_at) VALUES (?, ?, 'card_deleted', ?, NOW())",
            [$adminId, ($card['user_id'] ?? null), $desc]
        );
        error_log('🔴 ADMIN DELETE CARD API DEBUG: Admin action logged successfully');
    } catch (\Throwable $e) {
        error_log('🔴 ADMIN DELETE CARD API DEBUG: Failed to log admin action: ' . $e->getMessage());
        // Continue with deletion even if logging fails
    }

    error_log('🔴 ADMIN DELETE CARD API DEBUG: Proceeding with card deletion...');
    try {
        $result = $cardModel->delete($cardId);
        error_log('🔴 ADMIN DELETE CARD API DEBUG: Delete result: ' . json_encode($result));
    } catch (\Throwable $e) {
        error_log('🔴 ADMIN DELETE CARD API DEBUG: Card deletion failed with throwable: ' . $e->getMessage());
        error_log('🔴 ADMIN DELETE CARD API DEBUG: Trace: ' . $e->getTraceAsString());
        
        // Clean any output before sending JSON
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Card deletion failed: ' . $e->getMessage()]);
        exit;
    }

    if ($result['success']) {
        error_log('🔴 ADMIN DELETE CARD API DEBUG: Admin card deletion successful');
        
        // Clean any output before sending JSON
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Card deleted successfully']);
        exit;
    } else {
        error_log('🔴 ADMIN DELETE CARD API DEBUG: Admin card deletion failed: ' . $result['message']);
        
        // Clean any output before sending JSON
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $result['message']]);
        exit;
    }

} catch (\Throwable $e) {
    error_log('🔴 ADMIN DELETE CARD API DEBUG: Throwable occurred: ' . $e->getMessage());
    error_log('🔴 ADMIN DELETE CARD API DEBUG: Trace: ' . $e->getTraceAsString());
    
    // Clean any output before sending JSON
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'An error occurred', 'error' => $e->getMessage()]);
    exit;
}

} catch (\Throwable $e) {
    // Catch any fatal errors from includes or early execution
    error_log('🔴 ADMIN DELETE CARD API DEBUG: Fatal/Throwable error: ' . $e->getMessage());
    error_log('🔴 ADMIN DELETE CARD API DEBUG: Trace: ' . $e->getTraceAsString());
    
    // Clean any output before sending JSON
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Fatal error occurred', 'error' => $e->getMessage()]);
    exit;
}
?>
