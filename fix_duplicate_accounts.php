<?php
/**
 * Script to fix duplicate account_owners entries
 * This removes duplicate entries where a user is both primary owner AND in account_owners table
 */

require_once __DIR__ . '/config/config.php';

$userId = 46; // jadejordan6040@gmail.com
$db = Database::getInstance();

echo "=== Fixing Duplicate Account Owners for User ID: $userId ===\n\n";

// Start transaction
$db->beginTransaction();

try {
    // 1. Find accounts where user is primary owner
    $sql = "SELECT id FROM accounts WHERE user_id = ? AND status != 'closed'";
    $stmt = $db->query($sql, [$userId]);
    $ownedAccounts = $stmt->fetchAll();
    
    echo "Accounts where user is primary owner: " . count($ownedAccounts) . "\n";
    
    // 2. Remove account_owners entries where user is already the primary owner
    $removedCount = 0;
    foreach ($ownedAccounts as $account) {
        $accountId = $account['id'];
        
        // Check if user has entry in account_owners for their own account
        $checkSql = "SELECT id FROM account_owners WHERE account_id = ? AND user_id = ?";
        $checkStmt = $db->query($checkSql, [$accountId, $userId]);
        $ownerEntry = $checkStmt->fetch();
        
        if ($ownerEntry) {
            // Remove the duplicate entry
            $deleteSql = "DELETE FROM account_owners WHERE account_id = ? AND user_id = ?";
            $db->query($deleteSql, [$accountId, $userId]);
            $removedCount++;
            echo "Removed duplicate account_owners entry for account ID: $accountId\n";
        }
    }
    
    // 3. Remove any duplicate account_owners entries (same account_id + user_id combination)
    echo "\nChecking for other duplicate account_owners entries...\n";
    $sql = "SELECT account_id, user_id, COUNT(*) as count, GROUP_CONCAT(id) as ids
            FROM account_owners
            WHERE user_id = ?
            GROUP BY account_id, user_id
            HAVING COUNT(*) > 1";
    $stmt = $db->query($sql, [$userId]);
    $duplicates = $stmt->fetchAll();
    
    $duplicateRemoved = 0;
    foreach ($duplicates as $dup) {
        $ids = explode(',', $dup['ids']);
        // Keep the first one, delete the rest
        $keepId = array_shift($ids);
        foreach ($ids as $idToDelete) {
            $deleteSql = "DELETE FROM account_owners WHERE id = ?";
            $db->query($deleteSql, [$idToDelete]);
            $duplicateRemoved++;
            echo "Removed duplicate account_owners entry ID: $idToDelete (keeping ID: $keepId)\n";
        }
    }
    
    // Commit transaction
    $db->commit();
    
    echo "\n=== Summary ===\n";
    echo "Removed entries where user is primary owner: $removedCount\n";
    echo "Removed duplicate entries: $duplicateRemoved\n";
    echo "Total removed: " . ($removedCount + $duplicateRemoved) . "\n";
    echo "\nFix completed successfully!\n";
    
} catch (Exception $e) {
    $db->rollBack();
    echo "Error: " . $e->getMessage() . "\n";
    echo "Transaction rolled back.\n";
}

