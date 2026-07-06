<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../models/Kyc.php';
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../models/User.php';

requireLogin();
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$id = intval($_POST['kyc_id'] ?? $_POST['id'] ?? 0);
$rejectionReason = Security::sanitize($_POST['reason'] ?? $_POST['rejection_reason'] ?? '');
$adminNotes = Security::sanitize($_POST['notes'] ?? $_POST['admin_notes'] ?? '');

if (empty($id)) {
    echo json_encode(['success' => false, 'message' => 'KYC ID is required']);
    exit;
}

if (empty($rejectionReason)) {
    echo json_encode(['success' => false, 'message' => 'Rejection reason is required']);
    exit;
}

try {
    $kycModel = new Kyc();
    
    // Get KYC data first to verify it exists
    $db = Database::getInstance();
    $sql = "SELECT user_id FROM kyc_verifications WHERE id = ?";
    $stmt = $db->query($sql, [$id]);
    $kycCheck = $stmt ? $stmt->fetch() : null;
    
    if (!$kycCheck) {
        echo json_encode(['success' => false, 'message' => 'KYC submission not found']);
        exit;
    }
    
    if ($kycModel->reject($id, $_SESSION['user_id'], $rejectionReason, $adminNotes)) {
        logActivity($_SESSION['user_id'], 'KYC_REJECTED', 'Rejected KYC ID: ' . $id . ' - ' . $rejectionReason);
        
        echo json_encode(['success' => true, 'message' => 'KYC rejected successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to reject KYC']);
    }
} catch (Exception $e) {
    error_log("KYC Reject Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}

