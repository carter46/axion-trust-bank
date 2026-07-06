<?php
/**
 * Diagnostic script to check user balance calculation
 * Run this to see what's causing the balance discrepancy
 */

require_once __DIR__ . '/config/config.php';

$userId = 46; // jadejordan6040@gmail.com
$db = Database::getInstance();

echo "=== Checking User Balance for User ID: $userId ===\n\n";

// 1. Check accounts table directly
echo "1. Accounts in accounts table (user_id = $userId):\n";
$sql = "SELECT id, account_number, account_type, balance, status FROM accounts WHERE user_id = ?";
$stmt = $db->query($sql, [$userId]);
$accounts = $stmt->fetchAll();
$totalFromAccounts = 0;
foreach ($accounts as $acc) {
    echo "   Account ID: {$acc['id']}, Number: {$acc['account_number']}, Type: {$acc['account_type']}, Balance: " . number_format($acc['balance'], 2) . ", Status: {$acc['status']}\n";
    $totalFromAccounts += $acc['balance'];
}
echo "   Total from accounts table: " . number_format($totalFromAccounts, 2) . "\n\n";

// 2. Check account_owners table
echo "2. Entries in account_owners table (user_id = $userId):\n";
$sql = "SELECT ao.*, a.account_number, a.balance, a.user_id as primary_owner_id 
        FROM account_owners ao 
        INNER JOIN accounts a ON ao.account_id = a.id 
        WHERE ao.user_id = ?";
$stmt = $db->query($sql, [$userId]);
$owners = $stmt->fetchAll();
foreach ($owners as $owner) {
    echo "   Account ID: {$owner['account_id']}, Account Number: {$owner['account_number']}, ";
    echo "Is Primary: {$owner['is_primary']}, Status: {$owner['status']}, ";
    echo "Primary Owner ID: {$owner['primary_owner_id']}, Balance: " . number_format($owner['balance'], 2) . "\n";
}
echo "   Total entries: " . count($owners) . "\n\n";

// 3. Check for duplicate account_owners entries
echo "3. Checking for duplicate account_owners entries:\n";
$sql = "SELECT account_id, user_id, COUNT(*) as count 
        FROM account_owners 
        WHERE user_id = ? 
        GROUP BY account_id, user_id 
        HAVING COUNT(*) > 1";
$stmt = $db->query($sql, [$userId]);
$duplicates = $stmt->fetchAll();
if (empty($duplicates)) {
    echo "   No duplicate entries found\n";
} else {
    echo "   WARNING: Duplicate entries found:\n";
    foreach ($duplicates as $dup) {
        echo "   Account ID: {$dup['account_id']}, Count: {$dup['count']}\n";
    }
}
echo "\n";

// 4. Test getUserAccessibleAccounts query
echo "4. Testing getUserAccessibleAccounts query:\n";
require_once __DIR__ . '/models/JointAccount.php';
$jointAccount = new JointAccount();
$accessibleAccounts = $jointAccount->getUserAccessibleAccounts($userId);
$totalFromQuery = 0;
echo "   Accounts returned: " . count($accessibleAccounts) . "\n";
foreach ($accessibleAccounts as $acc) {
    echo "   Account ID: {$acc['id']}, Number: {$acc['account_number']}, Balance: " . number_format($acc['balance'], 2) . "\n";
    $totalFromQuery += $acc['balance'];
}
echo "   Total from getUserAccessibleAccounts: " . number_format($totalFromQuery, 2) . "\n\n";

// 5. Check if user is both primary owner AND in account_owners
echo "5. Checking if user is both primary owner AND in account_owners:\n";
$sql = "SELECT a.id, a.account_number, a.user_id as primary_owner, 
               COUNT(ao.id) as owner_entries
        FROM accounts a
        LEFT JOIN account_owners ao ON a.id = ao.account_id AND ao.user_id = ?
        WHERE a.user_id = ?
        GROUP BY a.id, a.account_number, a.user_id";
$stmt = $db->query($sql, [$userId, $userId]);
$both = $stmt->fetchAll();
foreach ($both as $b) {
    echo "   Account ID: {$b['id']}, Number: {$b['account_number']}, ";
    echo "Primary Owner: {$b['primary_owner']}, Owner Entries: {$b['owner_entries']}\n";
    if ($b['owner_entries'] > 0) {
        echo "   ⚠️  WARNING: User is primary owner AND has account_owners entry!\n";
    }
}
echo "\n";

// 6. Raw SQL query test (what getUserAccessibleAccounts does)
echo "6. Raw SQL query result (before GROUP BY fix):\n";
$sql = "SELECT DISTINCT a.*
        FROM accounts a
        LEFT JOIN account_owners ao ON a.id = ao.account_id
        WHERE (a.user_id = ? OR (ao.user_id = ? AND ao.status = 'active'))
        AND a.status != 'closed'
        ORDER BY a.created_at ASC";
$stmt = $db->query($sql, [$userId, $userId]);
$rawAccounts = $stmt->fetchAll();
echo "   Accounts returned (with DISTINCT): " . count($rawAccounts) . "\n";
$totalRaw = 0;
foreach ($rawAccounts as $acc) {
    $totalRaw += $acc['balance'];
}
echo "   Total balance (with DISTINCT): " . number_format($totalRaw, 2) . "\n\n";

echo "7. Raw SQL query result (with GROUP BY fix):\n";
$sql = "SELECT a.*
        FROM accounts a
        LEFT JOIN account_owners ao ON a.id = ao.account_id
        WHERE (a.user_id = ? OR (ao.user_id = ? AND ao.status = 'active'))
        AND a.status != 'closed'
        GROUP BY a.id
        ORDER BY a.created_at ASC";
$stmt = $db->query($sql, [$userId, $userId]);
$groupedAccounts = $stmt->fetchAll();
echo "   Accounts returned (with GROUP BY): " . count($groupedAccounts) . "\n";
$totalGrouped = 0;
foreach ($groupedAccounts as $acc) {
    $totalGrouped += $acc['balance'];
}
echo "   Total balance (with GROUP BY): " . number_format($totalGrouped, 2) . "\n\n";

echo "=== Summary ===\n";
echo "Expected balance: 26,550,000.00\n";
echo "Total from accounts table: " . number_format($totalFromAccounts, 2) . "\n";
echo "Total from getUserAccessibleAccounts: " . number_format($totalFromQuery, 2) . "\n";
echo "Total with DISTINCT: " . number_format($totalRaw, 2) . "\n";
echo "Total with GROUP BY: " . number_format($totalGrouped, 2) . "\n";

