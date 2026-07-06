<?php
// Start output buffering immediately to catch any errors
ob_start();

try {
    require_once __DIR__ . '/../config/config.php';
    require_once __DIR__ . '/../includes/functions.php';
    require_once __DIR__ . '/../models/Loan.php';
    require_once __DIR__ . '/../models/Notification.php';
    require_once __DIR__ . '/../models/User.php';
    require_once __DIR__ . '/../models/Account.php';
    require_once __DIR__ . '/../includes/security.php';

    ob_end_clean();

    header('Content-Type: application/json');

    // Start session if not started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Check if admin is logged in
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $loanId = intval($_POST['loan_id'] ?? 0);
    $rejectionReason = Security::sanitize($_POST['rejection_reason'] ?? '');

    if (!$loanId || empty($rejectionReason)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Loan ID and rejection reason are required']);
        exit;
    }

    $loanModel = new Loan();
    $result = $loanModel->reject($loanId, $rejectionReason);
    
    if ($result['success']) {
        // Log activity
        logActivity($_SESSION['user_id'], 'LOAN_REJECTED', "Rejected loan application #$loanId");
        
        echo json_encode([
            'success' => true,
            'message' => 'Loan application rejected successfully'
        ]);
    } else {
        echo json_encode($result);
    }
    
} catch (Exception $e) {
    error_log('Loan Rejection Error: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
    
    ob_end_clean();
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
} catch (Error $e) {
    error_log('Loan Rejection Fatal Error: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
    
    ob_end_clean();
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>
