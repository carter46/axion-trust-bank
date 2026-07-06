<?php
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

require_once '../config/config.php';
require_once '../includes/functions.php';

ob_end_clean();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if (!isForceSecuritySetupEnabled()) {
    unset($_SESSION['security_onboarding']);
    unset($_SESSION['security_setup_required']);
    echo json_encode(['success' => true, 'redirect' => SITE_URL . '/dashboard']);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Security setup is required and cannot be skipped.']);
