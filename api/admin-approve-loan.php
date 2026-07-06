<?php
// Start output buffering immediately to catch any errors
ob_start();

try {
    require_once __DIR__ . '/../config/config.php';
    require_once __DIR__ . '/../includes/functions.php';
    require_once __DIR__ . '/../models/Loan.php';
    require_once __DIR__ . '/../models/Transaction.php';
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
    $approvedAmount = floatval($_POST['approved_amount'] ?? 0);
    $adminNotes = Security::sanitize($_POST['admin_notes'] ?? '');

    if (!$loanId || $approvedAmount <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Loan ID and approved amount are required']);
        exit;
    }

    $loanModel = new Loan();
    $result = $loanModel->approve($loanId, $approvedAmount);
    
    if ($result['success']) {
        // Update admin notes if provided
        if (!empty($adminNotes)) {
            $db = Database::getInstance();
            $sql = "UPDATE loans SET notes = ? WHERE id = ?";
            $db->query($sql, [$adminNotes, $loanId]);
        }
        
        // Log activity
        logActivity($_SESSION['user_id'], 'LOAN_APPROVED', "Approved loan application #$loanId for amount $" . number_format($approvedAmount, 2));
        
        echo json_encode([
            'success' => true,
            'message' => 'Loan approved and funds disbursed successfully'
        ]);
    } else {
        echo json_encode($result);
    }

} catch (Exception $e) {
    error_log('Loan Approval Error: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
    
    ob_end_clean();
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
} catch (Error $e) {
    error_log('Loan Approval Fatal Error: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
    
    ob_end_clean();
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>
