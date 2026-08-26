<?php
/**
 * Add transactions.status value "successful" (user transfers).
 * "completed" remains available for admin-set finalization.
 */

return [
    'id' => '2026_08_26_transaction_successful_status',
    'description' => 'Add successful status to transactions enum for transfer receipts',
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
                    'cancelled'
                 ) DEFAULT 'pending'"
            );
        } catch (Throwable $e) {
            // Some DBs may already include successful or use a different enum set
            $msg = strtolower($e->getMessage());
            if (strpos($msg, 'successful') === false && strpos($msg, 'duplicate') === false) {
                // Retry without cancelled if that value is unsupported historically
                try {
                    $db->query(
                        "ALTER TABLE transactions
                         MODIFY COLUMN status ENUM(
                            'pending',
                            'processing',
                            'successful',
                            'completed',
                            'failed',
                            'reversed'
                         ) DEFAULT 'pending'"
                    );
                } catch (Throwable $e2) {
                    throw $e2;
                }
            }
        }
    },
];
