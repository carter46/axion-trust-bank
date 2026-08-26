<?php
/**
 * User self-service currency update is disabled.
 * Currency is admin-owned via /admin/user/{id} and api/admin-set-user-currency.php.
 */
header('Content-Type: application/json');
http_response_code(403);
echo json_encode([
    'success' => false,
    'message' => 'Currency is managed by your bank administrator and cannot be changed here.'
]);
