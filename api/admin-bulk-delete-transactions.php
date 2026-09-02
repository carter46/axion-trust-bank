<?php
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

ob_end_clean();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit;
}

$transactionIds = $input['transaction_ids'] ?? [];
$reason = trim($input['reason'] ?? '');
$restrictUserId = intval($input['user_id'] ?? 0);

if (!is_array($transactionIds) || empty($transactionIds)) {
    echo json_encode(['success' => false, 'message' => 'Select at least one transaction to delete']);
    exit;
}

$transactionIds = array_values(array_unique(array_filter(array_map('intval', $transactionIds))));
if (empty($transactionIds)) {
    echo json_encode(['success' => false, 'message' => 'No valid transaction IDs provided']);
    exit;
}

if ($reason === '') {
    echo json_encode(['success' => false, 'message' => 'Reason is required for deletion']);
    exit;
}

if ($restrictUserId > 0) {
    enforceDemoUserAdminAccessForUserId($restrictUserId);
}

if (count($transactionIds) > 100) {
    echo json_encode(['success' => false, 'message' => 'Maximum 100 transactions per bulk delete']);
    exit;
}

try {
    $db = Database::getInstance();
    $placeholders = implode(',', array_fill(0, count($transactionIds), '?'));
    $sql = "SELECT t.*, u.email AS user_email, u.role AS user_role
            FROM transactions t
            JOIN users u ON t.user_id = u.id
            WHERE t.id IN ($placeholders)";
    $stmt = $db->query($sql, $transactionIds);
    $rows = $stmt ? $stmt->fetchAll() : [];

    if (count($rows) !== count($transactionIds)) {
        echo json_encode(['success' => false, 'message' => 'One or more transactions were not found']);
        exit;
    }

    foreach ($rows as $row) {
        if (($row['user_role'] ?? '') === 'admin') {
            echo json_encode(['success' => false, 'message' => 'Cannot delete admin user transactions']);
            exit;
        }
        enforceDemoUserAdminAccessForUserId((int)$row['user_id']);
        if ($restrictUserId > 0 && (int)$row['user_id'] !== $restrictUserId) {
            echo json_encode(['success' => false, 'message' => 'Selected transactions must belong to the same user']);
            exit;
        }
    }

    $balanceDeltas = adminComputeDeletionBalanceDeltas($db, $rows);
    $deleteIdSet = array_flip($transactionIds);
    $pairRowsToDelete = [];
    $processedPairIds = [];
    foreach ($rows as $row) {
        if (($row['transaction_type'] ?? '') !== 'debit' || ($row['category'] ?? '') !== 'transfer') {
            continue;
        }
        $meta = json_decode($row['metadata'] ?? '{}', true);
        if (!is_array($meta)) {
            $meta = [];
        }
        if (($meta['transfer_scope'] ?? $meta['transfer_type'] ?? '') !== 'internal') {
            continue;
        }
        $pair = adminFindInternalTransferPair($db->getConnection(), $row);
        $pairId = (int)($pair['id'] ?? 0);
        if (!$pair || isset($deleteIdSet[$pairId]) || isset($processedPairIds[$pairId])) {
            continue;
        }
        $processedPairIds[$pairId] = true;
        $pairRowsToDelete[] = $pair;
        $accountId = (int)$pair['account_id'];
        $balanceDeltas[$accountId] = ($balanceDeltas[$accountId] ?? 0) + adminBalanceChangeOnDelete($pair);
    }
    $affectedBatchIds = [];
    foreach ($rows as $row) {
        $batchId = adminParseGeneratorBatchId($row);
        if ($batchId) {
            $affectedBatchIds[] = $batchId;
        }
    }

    $db->beginTransaction();

    foreach (array_keys($balanceDeltas) as $accountId) {
        if (abs($balanceDeltas[$accountId]) < 0.00001) {
            continue;
        }
        $acctStmt = $db->query(
            "SELECT balance, available_balance FROM accounts WHERE id = ? FOR UPDATE",
            [$accountId]
        );
        $account = $acctStmt ? $acctStmt->fetch() : false;
        if (!$account) {
            throw new Exception('Account not found for transaction reversal');
        }
        $newBalance = round((float)$account['balance'] + $balanceDeltas[$accountId], 2);
        if ($newBalance < 0) {
            throw new Exception('Cannot delete: reversal would result in negative balance on account ' . $accountId);
        }
        $db->query(
            "UPDATE accounts SET balance = ?, available_balance = ?, updated_at = NOW() WHERE id = ?",
            [$newBalance, $newBalance, $accountId]
        );
    }

    $deletePlaceholders = implode(',', array_fill(0, count($transactionIds), '?'));
    $db->query("DELETE FROM transactions WHERE id IN ($deletePlaceholders)", $transactionIds);

    foreach ($pairRowsToDelete as $pairRow) {
        $db->query("DELETE FROM transactions WHERE id = ?", [(int)$pairRow['id']]);
    }

    adminMarkEmptyGeneratorBatchesUndone($db, $affectedBatchIds);
    $db->commit();

    $totalBalanceAdjusted = round(array_sum($balanceDeltas), 2);

    $refs = array_map(fn($r) => $r['transaction_ref'], $rows);
    logActivity(
        $_SESSION['user_id'],
        'ADMIN_BULK_DELETE_TRANSACTIONS',
        sprintf(
            'Deleted %d transaction(s). Refs: %s. Reason: %s',
            count($rows),
            implode(', ', array_slice($refs, 0, 10)) . (count($refs) > 10 ? '...' : ''),
            $reason
        )
    );

    echo json_encode([
        'success' => true,
        'message' => count($rows) . ' transaction(s) deleted successfully',
        'deleted_count' => count($rows),
        'balance_adjusted' => $totalBalanceAdjusted,
    ]);
} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollback();
    }
    error_log('Admin Bulk Delete Transactions Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage() ?: 'An error occurred while deleting transactions',
    ]);
}
