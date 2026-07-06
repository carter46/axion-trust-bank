<?php
// Prevent output before JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);
ob_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Clear any accidental output
ob_end_clean();

header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit;
}

$userIdToDelete = intval($input['user_id'] ?? 0);

if (!$userIdToDelete) {
    echo json_encode(['success' => false, 'message' => 'User ID is required']);
    exit;
}

// Prevent deleting self
if ($userIdToDelete == $_SESSION['user_id']) {
    echo json_encode(['success' => false, 'message' => 'You cannot delete your own account']);
    exit;
}

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    // Check if user exists and is not an admin
    $sql = "SELECT id, email, role FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$userIdToDelete]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }

    // Prevent deleting other admins
    if ($user['role'] === 'admin') {
        echo json_encode(['success' => false, 'message' => 'Cannot delete admin users. Use Admin Settings to manage administrators.']);
        exit;
    }

    // Start transaction for safety
    $conn->beginTransaction();
    
    try {
        // Delete in correct order to handle foreign key constraints
        
        // 1. First, get all account IDs for this user
        $sql = "SELECT id FROM accounts WHERE user_id = ?";
        $accStmt = $conn->prepare($sql);
        $accStmt->execute([$userIdToDelete]);
        $accountIds = $accStmt->fetchAll(PDO::FETCH_COLUMN);
        
        // 2. Delete user_investments that reference these accounts (by account_used_id)
        if (!empty($accountIds)) {
            $placeholders = str_repeat('?,', count($accountIds) - 1) . '?';
            $sql = "DELETE FROM user_investments WHERE account_used_id IN ($placeholders)";
            $deleteInvStmt = $conn->prepare($sql);
            $deleteInvStmt->execute($accountIds);
        }
        
        // 3. Delete user_investments by user_id (to catch any others)
        $sql = "DELETE FROM user_investments WHERE user_id = ?";
        $deleteInvStmt2 = $conn->prepare($sql);
        $deleteInvStmt2->execute([$userIdToDelete]);
        
        // 4. Delete investment_transactions (they reference user_investments and users)
        $sql = "DELETE FROM investment_transactions WHERE user_id = ?";
        $deleteInvTxnStmt = $conn->prepare($sql);
        $deleteInvTxnStmt->execute([$userIdToDelete]);
        
        // 5. Delete investment_withdrawals
        $sql = "DELETE FROM investment_withdrawals WHERE user_id = ?";
        $deleteInvWithStmt = $conn->prepare($sql);
        $deleteInvWithStmt->execute([$userIdToDelete]);
        
        // 6. Delete investment_funding
        $sql = "DELETE FROM investment_funding WHERE user_id = ?";
        $deleteInvFundStmt = $conn->prepare($sql);
        $deleteInvFundStmt->execute([$userIdToDelete]);
        
        // 7. Delete bill_payments (they reference accounts)
        $sql = "DELETE FROM bill_payments WHERE user_id = ?";
        $deleteBillStmt = $conn->prepare($sql);
        $deleteBillStmt->execute([$userIdToDelete]);
        
        // 8. Delete user loans
        $sql = "DELETE FROM loans WHERE user_id = ?";
        $deleteLoanStmt = $conn->prepare($sql);
        $deleteLoanStmt->execute([$userIdToDelete]);
        
        // 9. Delete transactions (they reference accounts)
        $sql = "DELETE FROM transactions WHERE user_id = ?";
        $deleteTxnStmt = $conn->prepare($sql);
        $deleteTxnStmt->execute([$userIdToDelete]);
        
        // 10. Delete cards (they reference accounts)
        $sql = "DELETE FROM cards WHERE user_id = ?";
        $deleteCardStmt = $conn->prepare($sql);
        $deleteCardStmt->execute([$userIdToDelete]);
        
        // 11. Now delete accounts (foreign key constraints should be satisfied)
        $sql = "DELETE FROM accounts WHERE user_id = ?";
        $deleteAccStmt = $conn->prepare($sql);
        $deleteAccResult = $deleteAccStmt->execute([$userIdToDelete]);
        if (!$deleteAccResult) {
            $errorInfo = $deleteAccStmt->errorInfo();
            throw new Exception('Failed to delete accounts: ' . ($errorInfo[2] ?? 'Database error'));
        }
        
        // 12. Delete notifications
        $sql = "DELETE FROM notifications WHERE user_id = ?";
        $deleteNotifStmt = $conn->prepare($sql);
        $deleteNotifStmt->execute([$userIdToDelete]);
        
        // 13. Delete activity logs (if they exist)
        // Note: activity_logs only has user_id column, not admin_id
        $sql = "DELETE FROM activity_logs WHERE user_id = ?";
        $deleteLogStmt = $conn->prepare($sql);
        $deleteLogStmt->execute([$userIdToDelete]);
        
        // 14. Delete other related tables with ON DELETE CASCADE (for safety, even though CASCADE should handle them)
        // These have CASCADE but deleting explicitly ensures order and avoids any edge cases
        $sql = "DELETE FROM beneficiaries WHERE user_id = ?";
        $deleteBenStmt = $conn->prepare($sql);
        $deleteBenStmt->execute([$userIdToDelete]);
        
        $sql = "DELETE FROM kyc_verifications WHERE user_id = ?";
        $deleteKycStmt = $conn->prepare($sql);
        $deleteKycStmt->execute([$userIdToDelete]);
        
        $sql = "DELETE FROM password_reset_tokens WHERE user_id = ?";
        $deleteTokenStmt = $conn->prepare($sql);
        $deleteTokenStmt->execute([$userIdToDelete]);
        
        $sql = "DELETE FROM email_verification_tokens WHERE user_id = ?";
        $deleteEmailTokenStmt = $conn->prepare($sql);
        $deleteEmailTokenStmt->execute([$userIdToDelete]);
        
        // 15. Finally, delete the user
        $sql = "DELETE FROM users WHERE id = ?";
        $deleteStmt = $conn->prepare($sql);
        $deleteResult = $deleteStmt->execute([$userIdToDelete]);
        
        if (!$deleteResult) {
            $errorInfo = $deleteStmt->errorInfo();
            throw new Exception('Failed to delete user: ' . ($errorInfo[2] ?? 'Database error'));
        }
        
        // Verify deletion succeeded
        $verifySql = "SELECT id FROM users WHERE id = ?";
        $verifyStmt = $conn->prepare($verifySql);
        $verifyStmt->execute([$userIdToDelete]);
        $stillExists = $verifyStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($stillExists) {
            throw new Exception('User deletion verification failed - user still exists');
        }
        
        // Commit transaction
        $conn->commit();

        // CRITICAL SECURITY: Destroy the deleted user's session if they are logged in
        // This prevents them from continuing to use the system after account deletion
        try {
            // Get all active sessions (if using file-based sessions, we'd need to iterate)
            // For now, we'll log the deletion so session checks can catch it
            // The requireLogin() function now checks if user exists, so they'll be logged out on next page load
            
            // If the deleted user is currently logged in, we should invalidate their session
            // Since we can't easily access their session file from here, we rely on requireLogin() validation
            // But we can log this for monitoring
            error_log("User account deleted - User ID: {$userIdToDelete}, Email: {$user['email']}");
            
        } catch (Exception $e) {
            error_log("Warning: Could not log user deletion for session invalidation: " . $e->getMessage());
        }

        // Log activity
        logActivity($_SESSION['user_id'], 'USER_DELETED', "Deleted user: {$user['email']} (ID: {$userIdToDelete})");

        echo json_encode([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
        exit;

    } catch (Exception $e) {
        $conn->rollBack();
        throw $e;
    }

} catch (Exception $e) {
    error_log('Delete User Error: ' . $e->getMessage());
    error_log('User ID: ' . ($userIdToDelete ?? 'N/A'));
    error_log('Input Data: ' . json_encode($input ?? []));
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage() ?: 'An error occurred while deleting the user',
        'error_details' => [
            'type' => get_class($e),
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ]);
    exit;
}

