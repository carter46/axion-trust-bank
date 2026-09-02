<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/currency.php';

requireLogin();
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    $input = $_POST;
}

$targetUserId = intval($input['user_id'] ?? 0);
$currency = strtoupper(trim((string)($input['currency'] ?? '')));

if ($targetUserId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
    exit;
}

enforceDemoUserAdminAccessForUserId($targetUserId);

$currencyHelper = new Currency();
$supported = $currencyHelper->getSupportedCurrencies();
if ($currency === '' || !isset($supported[$currency])) {
    echo json_encode(['success' => false, 'message' => 'Invalid currency code']);
    exit;
}

try {
    $db = Database::getInstance();
    $stmt = $db->query("SELECT id, email, full_name, role FROM users WHERE id = ? LIMIT 1", [$targetUserId]);
    $target = $stmt->fetch();
    if (!$target || ($target['role'] ?? '') === 'admin') {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }

    $country = currencyToPrimaryCountry($currency);

    $db->query(
        "UPDATE users SET currency = ?, currency_selection_shown = 1, country = ?, updated_at = NOW() WHERE id = ?",
        [$currency, $country, $targetUserId]
    );

    logActivity(
        $_SESSION['user_id'],
        'ADMIN_SET_USER_CURRENCY',
        "Set currency={$currency} (country={$country}) for user {$target['email']} (ID: {$targetUserId})"
    );

    echo json_encode([
        'success' => true,
        'message' => 'User currency updated successfully',
        'currency' => $currency,
        'country' => $country,
        'display_currency' => $currency,
    ]);
} catch (Exception $e) {
    error_log('admin-set-user-currency error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to update user currency']);
}
