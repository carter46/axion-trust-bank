<?php
/**
 * Add unsecure_network to users.status so admins can block sign-in
 * with a network/location message.
 */

return [
    'id' => '2026_10_29_users_unsecure_network_status',
    'description' => 'Add unsecure_network to users.status enum',
    'up' => function ($db) {
        $conn = $db->getConnection();
        $column = $conn->query("SHOW COLUMNS FROM users LIKE 'status'")->fetch(PDO::FETCH_ASSOC);
        $type = strtolower((string)($column['Type'] ?? ''));
        if ($type === '') {
            throw new RuntimeException('Could not read users.status column');
        }
        if (strpos($type, 'unsecure_network') !== false) {
            return;
        }
        if (strpos($type, 'enum(') !== 0) {
            throw new RuntimeException('users.status is not an ENUM (' . $type . ')');
        }
        $conn->exec(
            "ALTER TABLE users
             MODIFY COLUMN status ENUM(
                'active',
                'suspended',
                'blocked',
                'pending',
                'restricted',
                'closed',
                'hold',
                'unsecure_network'
             ) DEFAULT 'pending'"
        );
    },
];
