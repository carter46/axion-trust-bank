<?php
// Prevent any output before JSON
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Clear any accidental output
ob_end_clean();

header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit;
}

$userId = intval($input['user_id'] ?? 0);
$kycStatus = trim(strtolower($input['kyc_status'] ?? ''));
$reason = trim($input['reason'] ?? '');
$notes = trim($input['notes'] ?? '');
$activateAccount = !empty($input['activate_account']);

if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'User ID required']);
    exit;
}

if (!in_array($kycStatus, ['pending', 'verified', 'rejected'], true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid kyc_status. Must be: pending, verified, rejected']);
    exit;
}

if ($kycStatus === 'rejected' && $reason === '') {
    echo json_encode(['success' => false, 'message' => 'Rejection reason is required']);
    exit;
}

try {
    $db = Database::getInstance();

    // Check if user exists and is not an admin
    $stmt = $db->query(
        "SELECT id, email, full_name, status, kyc_status, date_of_birth, address, city, state, country, postal_code
         FROM users
         WHERE id = ? AND role != 'admin'",
        [$userId]
    );
    $user = $stmt ? $stmt->fetch() : null;

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }

    // Ensure there is a kyc_verifications record when admin sets verified/rejected.
    // The /profile/kyc page uses kyc_verifications existence + status to decide whether to show the form.
    $adminId = $_SESSION['user_id'];
    $kycStmt = $db->query("SELECT id FROM kyc_verifications WHERE user_id = ? LIMIT 1", [$userId]);
    $existingKycRow = $kycStmt ? $kycStmt->fetch() : null;

    if (!$existingKycRow && in_array($kycStatus, ['verified', 'rejected'], true)) {
        // Create minimal KYC row (no documents required) so UI shows correct status.
        $insertSql = "INSERT INTO kyc_verifications (
                user_id, account_type, full_legal_name, date_of_birth,
                residential_address, residential_city, residential_state, residential_country, residential_zip,
                status, verified_by, verified_at, rejection_reason, admin_notes, submitted_at
            ) VALUES (
                ?, 'individual', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW()
            )";

        $verifiedAt = ($kycStatus === 'verified' || $kycStatus === 'rejected') ? date('Y-m-d H:i:s') : null;
        $rejectionReason = ($kycStatus === 'rejected') ? $reason : null;

        $adminNotesToStore = $notes;
        if ($adminNotesToStore === '') {
            $adminNotesToStore = ($kycStatus === 'verified')
                ? 'Account verified by admin (no documents submitted)'
                : 'Account rejected by admin (no documents submitted)';
        }

        $db->query($insertSql, [
            $userId,
            $user['full_name'] ?? null,
            $user['date_of_birth'] ?? null,
            $user['address'] ?? null,
            $user['city'] ?? null,
            $user['state'] ?? null,
            $user['country'] ?? null,
            $user['postal_code'] ?? null,
            $kycStatus,
            $adminId,
            $verifiedAt,
            $rejectionReason,
            $adminNotesToStore
        ]);

        // Update user kyc_submitted_at for consistency
        $db->query("UPDATE users SET kyc_submitted_at = NOW() WHERE id = ?", [$userId]);
    } elseif ($existingKycRow) {
        // Update existing KYC record status to match admin action
        $kycId = intval($existingKycRow['id']);

        if ($kycStatus === 'verified') {
            $db->query(
                "UPDATE kyc_verifications
                 SET status = 'verified',
                     verified_by = ?,
                     verified_at = NOW(),
                     rejection_reason = NULL,
                     admin_notes = ?,
                     updated_at = NOW()
                 WHERE id = ?",
                [$adminId, ($notes !== '' ? $notes : 'Account verified by admin'), $kycId]
            );
        } elseif ($kycStatus === 'rejected') {
            $db->query(
                "UPDATE kyc_verifications
                 SET status = 'rejected',
                     verified_by = ?,
                     verified_at = NOW(),
                     rejection_reason = ?,
                     admin_notes = ?,
                     updated_at = NOW()
                 WHERE id = ?",
                [$adminId, $reason, ($notes !== '' ? $notes : 'Account rejected by admin'), $kycId]
            );
        } else {
            // pending
            $db->query(
                "UPDATE kyc_verifications
                 SET status = 'pending',
                     verified_by = NULL,
                     verified_at = NULL,
                     rejection_reason = NULL,
                     admin_notes = ?,
                     updated_at = NOW()
                 WHERE id = ?",
                [($notes !== '' ? $notes : NULL), $kycId]
            );
        }
    }

    // Update user KYC status (and optionally activate account)
    if ($kycStatus === 'verified' && $activateAccount) {
        $db->query("UPDATE users SET kyc_status = ?, status = 'active', updated_at = NOW() WHERE id = ?", [$kycStatus, $userId]);
    } else {
        $db->query("UPDATE users SET kyc_status = ?, updated_at = NOW() WHERE id = ?", [$kycStatus, $userId]);
    }

    // Log admin action
    $desc = "Changed KYC status from '{$user['kyc_status']}' to '{$kycStatus}'"
        . (($activateAccount && $kycStatus === 'verified') ? " (also activated account)" : "")
        . ($reason ? " - Reason: {$reason}" : "")
        . ($notes ? " - Notes: {$notes}" : "");

    $db->query(
        "INSERT INTO admin_logs (admin_id, user_id, action, description, created_at) VALUES (?, ?, 'kyc_status_change', ?, NOW())",
        [$adminId, $userId, $desc]
    );

    echo json_encode(['success' => true, 'message' => 'KYC status updated', 'kyc_status' => $kycStatus]);
} catch (Exception $e) {
    error_log('Admin Set KYC Status Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while updating KYC status']);
}


