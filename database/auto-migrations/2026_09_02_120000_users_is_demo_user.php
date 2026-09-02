<?php
/**
 * Demo users for Hub integration — managed in Admin Settings, hidden from /admin/users.
 */

return [
    'id' => '2026_09_02_users_is_demo_user',
    'description' => 'Add is_demo_user flag to users table',
    'up' => function ($db) {
        $conn = $db->getConnection();
        $stmt = $conn->query("SHOW COLUMNS FROM users LIKE 'is_demo_user'");
        if ($stmt && $stmt->fetch()) {
            return;
        }
        $conn->exec("ALTER TABLE users ADD COLUMN is_demo_user tinyint(1) NOT NULL DEFAULT 0 AFTER is_super_admin");
    },
];
