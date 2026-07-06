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
$adminNotes = Security::sanitize($_POST['notes'] ?? $_POST['admin_notes'] ?? '');

if (empty($id)) {
    echo json_encode(['success' => false, 'message' => 'KYC ID is required']);
    exit;
}

try {
    $kycModel = new Kyc();
    
    // Get KYC data first to get user_id
    $db = Database::getInstance();
    $sql = "SELECT user_id FROM kyc_verifications WHERE id = ?";
    $stmt = $db->query($sql, [$id]);
    $kycCheck = $stmt ? $stmt->fetch() : null;
    
    if (!$kycCheck) {
        echo json_encode(['success' => false, 'message' => 'KYC submission not found']);
        exit;
    }
    
    if ($kycModel->verify($id, $_SESSION['user_id'], $adminNotes)) {
        logActivity($_SESSION['user_id'], 'KYC_APPROVED', 'Approved KYC ID: ' . $id);
        
        echo json_encode(['success' => true, 'message' => 'KYC approved successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to approve KYC']);
    }
} catch (Exception $e) {
    error_log("KYC Approve Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}

