<?php
/**
 * Admin: set user country (flag) and auto-apply that country's primary currency (EC → USD).
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
if (!function_exists('getCountryByName')) {
    require_once __DIR__ . '/../includes/countries.php';
}

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
$country = trim((string)($input['country'] ?? ''));

if ($targetUserId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
    exit;
}

if ($country === '') {
    echo json_encode(['success' => false, 'message' => 'Country is required']);
    exit;
}

enforceDemoUserAdminAccessForUserId($targetUserId);

$resolved = getCountryByName($country);
if (!$resolved && preg_match('/^[A-Za-z]{2}$/', $country)) {
    $resolved = getCountryByCode($country);
}
if (!$resolved) {
    echo json_encode(['success' => false, 'message' => 'Unknown country']);
    exit;
}
$countryName = $resolved['name'];

try {
    $db = Database::getInstance();
    $stmt = $db->query("SELECT id, email, full_name, role FROM users WHERE id = ? LIMIT 1", [$targetUserId]);
    $target = $stmt ? $stmt->fetch() : null;
    if (!$target || ($target['role'] ?? '') === 'admin') {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }

    $updated = $db->query(
        "UPDATE users SET country = ?, updated_at = NOW() WHERE id = ?",
        [$countryName, $targetUserId]
    );
    if ($updated === false) {
        echo json_encode(['success' => false, 'message' => 'Failed to update user country']);
        exit;
    }

    syncUserCurrencyFromCountry($targetUserId, $countryName);

    $afterStmt = $db->query("SELECT currency, country FROM users WHERE id = ? LIMIT 1", [$targetUserId]);
    $after = $afterStmt ? $afterStmt->fetch() : null;
    $currency = strtoupper(trim((string)($after['currency'] ?? '')));

    logActivity(
        $_SESSION['user_id'],
        'ADMIN_SET_USER_COUNTRY',
        "Set country={$countryName} (currency={$currency}) for user {$target['email']} (ID: {$targetUserId})"
    );

    echo json_encode([
        'success' => true,
        'message' => 'User country updated successfully',
        'country' => $countryName,
        'currency' => $currency,
        'display_currency' => $currency,
    ]);
} catch (Throwable $e) {
    error_log('admin-set-user-country error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to update user country']);
}
