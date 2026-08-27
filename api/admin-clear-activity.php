<?php
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../models/Admin.php';

ob_end_clean();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $adminModel = new Admin();
    if (!$adminModel->clearAuditLogs()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to clear recent activity']);
        exit;
    }

    echo json_encode([
        'success' => true,
        'message' => 'All recent activity has been cleared'
    ]);
} catch (Throwable $e) {
    error_log('admin-clear-activity: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Unable to clear recent activity']);
}
