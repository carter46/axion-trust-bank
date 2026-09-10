<?php
/**
 * Persist On Hold as its own transactions.status value.
 * Older code mapped on_hold → pending, so the UI could never show On Hold.
 */

return [
    'id' => '2026_09_10_transaction_on_hold_status',
    'description' => 'Add on_hold to transactions.status enum',
    'up' => function ($db) {
        try {
            $db->query(
                "ALTER TABLE transactions
                 MODIFY COLUMN status ENUM(
                    'pending',
                    'processing',
                    'successful',
                    'completed',
                    'failed',
                    'reversed',
                    'cancelled',
                    'on_hold'
                 ) DEFAULT 'pending'"
            );
        } catch (Throwable $e) {
            $msg = strtolower($e->getMessage());
            if (strpos($msg, 'on_hold') === false && strpos($msg, 'duplicate') === false) {
                throw $e;
            }
        }
    },
];
