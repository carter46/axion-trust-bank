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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

if ($action !== 'dismiss') {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}

try {
    $db = Database::getInstance();
    $userId = (int)$_SESSION['user_id'];
    $db->query(
        "UPDATE users SET kyc_prompt_dismissed = 1, updated_at = NOW() WHERE id = ?",
        [$userId]
    );
    logActivity($userId, 'KYC_PROMPT_DISMISSED', 'User dismissed dashboard KYC prompt');
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    error_log('kyc-prompt-action error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Could not save preference']);
}
