<?php
// Prevent output before JSON
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

ob_end_clean();

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];
$month = $_GET['month'] ?? date('n'); // Default to current month

try {
    $db = Database::getInstance();
    
    // Get account IDs user has access to (own + joint accounts)
    $accountIds = [];
    if (class_exists('JointAccount')) {
        require_once __DIR__ . '/../models/JointAccount.php';
        $jointAccount = new JointAccount();
        $accessibleAccounts = $jointAccount->getUserAccessibleAccounts($userId);
        $accountIds = array_column($accessibleAccounts, 'id');
    } else {
        // Fallback: get user's own accounts
        require_once __DIR__ . '/../models/Account.php';
        $accountModel = new Account();
        $userAccounts = $accountModel->getUserAccounts($userId);
        $accountIds = array_column($userAccounts, 'id');
    }
    
    if (empty($accountIds)) {
        echo json_encode([
            'success' => true,
            'data' => [],
            'total' => 0,
            'month' => intval($month)
        ]);
        exit;
    }
    
    // Get expense data by expense_category for the specified month
    $placeholders = implode(',', array_fill(0, count($accountIds), '?'));
    $sql = "SELECT 
                expense_category as category,
                SUM(amount) as total_amount,
                COUNT(*) as transaction_count
            FROM transactions 
            WHERE account_id IN ($placeholders)
            AND transaction_type = 'debit'
            AND status = 'completed'
            AND MONTH(created_at) = ?
            AND YEAR(created_at) = YEAR(NOW())
            AND expense_category IS NOT NULL
            GROUP BY expense_category
            ORDER BY total_amount DESC";
    
    $params = array_merge($accountIds, [$month]);
    $stmt = $db->query($sql, $params);
    $expenses = $stmt->fetchAll();
    
    // Calculate total expenses
    $totalExpenses = array_sum(array_column($expenses, 'total_amount'));
    
    // Format data for chart
    $chartData = [];
    $colors = ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#f97316', '#84cc16'];
    
    foreach ($expenses as $index => $expense) {
        $percentage = $totalExpenses > 0 ? round(($expense['total_amount'] / $totalExpenses) * 100) : 0;
        
        $chartData[] = [
            'category' => $expense['category'],
            'amount' => floatval($expense['total_amount']),
            'percentage' => $percentage,
            'transaction_count' => intval($expense['transaction_count']),
            'color' => $colors[$index % count($colors)]
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $chartData,
        'total' => floatval($totalExpenses),
        'month' => intval($month)
    ]);
    
} catch (Exception $e) {
    error_log('Expense data error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to load expense data'
    ]);
}
?>
