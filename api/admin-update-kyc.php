<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/kyc-config.php';
require_once __DIR__ . '/../models/Kyc.php';

requireLogin();
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$kycId = intval($_POST['kyc_id'] ?? 0);
$adminNotes = Security::sanitize($_POST['admin_notes'] ?? '');

if (empty($kycId)) {
    echo json_encode(['success' => false, 'message' => 'KYC ID is required']);
    exit;
}

try {
    $kycModel = new Kyc();
    $existingKyc = $kycModel->findById($kycId);

    if (!$existingKyc) {
        echo json_encode(['success' => false, 'message' => 'KYC submission not found']);
        exit;
    }

    $userId = (int)$existingKyc['user_id'];
    $postData = array_merge($_POST, ['user_id' => $userId]);
    $validation = validateKycSubmission($postData, $_FILES, $existingKyc, $userId);

    if (!$validation['valid']) {
        echo json_encode(['success' => false, 'message' => implode(' ', $validation['errors'])]);
        exit;
    }

    $data = buildKycSubmissionData($_POST, $_FILES, $existingKyc, $userId);

    if (($data['account_type'] ?? 'individual') === 'business') {
        $data['business_name'] = Security::sanitize($_POST['business_name'] ?? '');
        $data['business_address'] = Security::sanitize($_POST['business_address'] ?? '');
        $data['business_city'] = Security::sanitize($_POST['business_city'] ?? '');
        $data['business_state'] = Security::sanitize($_POST['business_state'] ?? '');
        $data['business_country'] = Security::sanitize($_POST['business_country'] ?? 'United States');
        $data['business_zip'] = Security::sanitize($_POST['business_zip'] ?? '');

        if (!empty($_POST['ein'])) {
            $data['ein'] = encryptData(Security::sanitize($_POST['ein']));
        } elseif (!empty($existingKyc['ein'])) {
            $data['ein'] = $existingKyc['ein'];
        } else {
            $data['ein'] = null;
        }

        if (isset($_FILES['business_formation_doc']) && $_FILES['business_formation_doc']['error'] === UPLOAD_ERR_OK) {
            $upload = uploadFile($_FILES['business_formation_doc'], 'kyc');
            if ($upload['success']) {
                $data['business_formation_doc'] = $upload['path'];
            }
        } elseif (!empty($existingKyc['business_formation_doc'])) {
            $data['business_formation_doc'] = $existingKyc['business_formation_doc'];
        }
    }

    $result = $kycModel->adminUpdate($kycId, $data, $_SESSION['user_id'], $adminNotes);

    if (!empty($result['success'])) {
        logActivity($_SESSION['user_id'], 'KYC_ADMIN_EDIT', 'Updated KYC ID: ' . $kycId . ' for user ID: ' . $userId);
        echo json_encode(['success' => true, 'message' => 'KYC details updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => $result['message'] ?? 'Failed to update KYC details']);
    }
} catch (Exception $e) {
    error_log("KYC Admin Update API Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}
