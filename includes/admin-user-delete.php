<?php
/**
 * Permanently delete a regular (non-admin) user and related rows.
 *
 * @return array{success:bool,message:string,email?:string}
 */
function adminPermanentlyDeleteRegularUser(int $userIdToDelete, int $actingAdminId): array
{
    if ($userIdToDelete <= 0) {
        return ['success' => false, 'message' => 'User ID is required'];
    }
    if ($actingAdminId <= 0) {
        return ['success' => false, 'message' => 'Unauthorized'];
    }
    if ($userIdToDelete === $actingAdminId) {
        return ['success' => false, 'message' => 'You cannot delete your own account'];
    }

    $denied = denyDemoUserAdminAccessJson($userIdToDelete);
    if ($denied) {
        return $denied;
    }

    $db = Database::getInstance();
    $conn = $db->getConnection();

    $stmt = $conn->prepare('SELECT id, email, role FROM users WHERE id = ?');
    $stmt->execute([$userIdToDelete]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        return ['success' => false, 'message' => 'User not found'];
    }
    if (($user['role'] ?? '') === 'admin') {
        return ['success' => false, 'message' => 'Cannot delete admin users. Use Admin Settings to manage administrators.'];
    }

    $conn->beginTransaction();
    try {
        $accStmt = $conn->prepare('SELECT id FROM accounts WHERE user_id = ?');
        $accStmt->execute([$userIdToDelete]);
        $accountIds = $accStmt->fetchAll(PDO::FETCH_COLUMN);

        if (!empty($accountIds)) {
            $placeholders = implode(',', array_fill(0, count($accountIds), '?'));
            try {
                $conn->prepare("DELETE FROM user_investments WHERE account_used_id IN ($placeholders)")->execute($accountIds);
            } catch (Throwable $e) {
                // Column or table may not exist on older installs
            }
            try {
                $conn->prepare("DELETE FROM account_owners WHERE account_id IN ($placeholders)")->execute($accountIds);
            } catch (Throwable $e) {
                // Table may not exist on older installs
            }
        }

        $byUserDeletes = [
            'DELETE FROM user_investments WHERE user_id = ?',
            'DELETE FROM investment_transactions WHERE user_id = ?',
            'DELETE FROM investment_withdrawals WHERE user_id = ?',
            'DELETE FROM investment_funding WHERE user_id = ?',
            'DELETE FROM bill_payments WHERE user_id = ?',
            'DELETE FROM loans WHERE user_id = ?',
            'DELETE FROM card_transactions WHERE user_id = ?',
            'DELETE FROM card_applications WHERE user_id = ?',
            'DELETE FROM transactions WHERE user_id = ?',
            'DELETE FROM cards WHERE user_id = ?',
            'DELETE FROM crypto_wallets WHERE user_id = ?',
            'DELETE FROM notifications WHERE user_id = ?',
            'DELETE FROM activity_logs WHERE user_id = ?',
            'DELETE FROM beneficiaries WHERE user_id = ?',
            'DELETE FROM kyc_verifications WHERE user_id = ?',
            'DELETE FROM password_reset_tokens WHERE user_id = ?',
            'DELETE FROM email_verification_tokens WHERE user_id = ?',
            'DELETE FROM two_factor_codes WHERE user_id = ?',
            'DELETE FROM support_messages WHERE user_id = ?',
            'DELETE FROM support_tickets WHERE user_id = ?',
            'DELETE FROM user_notes WHERE user_id = ?',
            'DELETE FROM transaction_generation_batches WHERE user_id = ?',
            'DELETE FROM joint_account_requests WHERE primary_owner_id = ? OR requesting_user_id = ?',
        ];
        foreach ($byUserDeletes as $sql) {
            try {
                $params = (substr_count($sql, '?') === 2) ? [$userIdToDelete, $userIdToDelete] : [$userIdToDelete];
                $conn->prepare($sql)->execute($params);
            } catch (Throwable $e) {
                // Skip tables that do not exist on this install
            }
        }

        try {
            $conn->prepare('DELETE FROM account_owners WHERE user_id = ?')->execute([$userIdToDelete]);
        } catch (Throwable $e) {
        }

        $deleteAccStmt = $conn->prepare('DELETE FROM accounts WHERE user_id = ?');
        if (!$deleteAccStmt->execute([$userIdToDelete])) {
            $errorInfo = $deleteAccStmt->errorInfo();
            throw new Exception('Failed to delete accounts: ' . ($errorInfo[2] ?? 'Database error'));
        }

        $deleteStmt = $conn->prepare('DELETE FROM users WHERE id = ?');
        if (!$deleteStmt->execute([$userIdToDelete])) {
            $errorInfo = $deleteStmt->errorInfo();
            throw new Exception('Failed to delete user: ' . ($errorInfo[2] ?? 'Database error'));
        }

        $verifyStmt = $conn->prepare('SELECT id FROM users WHERE id = ?');
        $verifyStmt->execute([$userIdToDelete]);
        if ($verifyStmt->fetch(PDO::FETCH_ASSOC)) {
            throw new Exception('User deletion verification failed - user still exists');
        }

        $conn->commit();
        logActivity($actingAdminId, 'USER_DELETED', "Deleted user: {$user['email']} (ID: {$userIdToDelete})");

        return [
            'success' => true,
            'message' => 'User deleted successfully',
            'email' => (string)$user['email'],
        ];
    } catch (Throwable $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        error_log('adminPermanentlyDeleteRegularUser: ' . $e->getMessage());
        return ['success' => false, 'message' => $e->getMessage() ?: 'An error occurred while deleting the user'];
    }
}
