<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Set JSON header
header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$userId = $_SESSION['user_id'];
$type = $_GET['type'] ?? 'week'; // week, month, year
$db = Database::getInstance();

try {
    $data = [];
    $labels = [];
    $incomeData = [];
    $expenseData = [];
    $totalIncome = 0;
    $totalExpense = 0;

    if ($type === 'week') {
        // Get last 7 days
        $labels = [];
        $incomeData = [];
        $expenseData = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $dayName = date('D', strtotime($date));
            $labels[] = $dayName;
            
            // Get income for this day
            $incomeSQL = "SELECT COALESCE(SUM(amount), 0) as total 
                         FROM transactions 
                         WHERE user_id = ? 
                         AND transaction_type = 'credit' 
                         AND status IN ('successful', 'completed')
                         AND DATE(created_at) = ?";
            $stmt = $db->query($incomeSQL, [$userId, $date]);
            $income = $stmt->fetch()['total'];
            $incomeData[] = (float)$income;
            $totalIncome += $income;
            
            // Get expenses for this day
            $expenseSQL = "SELECT COALESCE(SUM(amount), 0) as total 
                          FROM transactions 
                          WHERE user_id = ? 
                          AND transaction_type = 'debit' 
                          AND status IN ('successful', 'completed')
                          AND DATE(created_at) = ?";
            $stmt = $db->query($expenseSQL, [$userId, $date]);
            $expense = $stmt->fetch()['total'];
            $expenseData[] = (float)$expense;
            $totalExpense += $expense;
        }
        
    } elseif ($type === 'month') {
        // Get last 6 months
        $labels = [];
        $incomeData = [];
        $expenseData = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = date('Y-m-01', strtotime("-{$i} months"));
            $monthName = date('M', strtotime($date));
            $labels[] = $monthName;
            
            // Get income for this month
            $incomeSQL = "SELECT COALESCE(SUM(amount), 0) as total 
                         FROM transactions 
                         WHERE user_id = ? 
                         AND transaction_type = 'credit' 
                         AND status IN ('successful', 'completed')
                         AND YEAR(created_at) = YEAR(?) 
                         AND MONTH(created_at) = MONTH(?)";
            $stmt = $db->query($incomeSQL, [$userId, $date, $date]);
            $income = $stmt->fetch()['total'];
            $incomeData[] = (float)$income;
            $totalIncome += $income;
            
            // Get expenses for this month
            $expenseSQL = "SELECT COALESCE(SUM(amount), 0) as total 
                          FROM transactions 
                          WHERE user_id = ? 
                          AND transaction_type = 'debit' 
                          AND status IN ('successful', 'completed')
                          AND YEAR(created_at) = YEAR(?) 
                          AND MONTH(created_at) = MONTH(?)";
            $stmt = $db->query($expenseSQL, [$userId, $date, $date]);
            $expense = $stmt->fetch()['total'];
            $expenseData[] = (float)$expense;
            $totalExpense += $expense;
        }
        
    } else { // year
        // Get last 4 quarters
        $labels = [];
        $incomeData = [];
        $expenseData = [];
        
        for ($i = 3; $i >= 0; $i--) {
            $quarter = 4 - $i;
            $labels[] = "Q{$quarter}";
            
            // Calculate quarter start and end dates
            $year = date('Y', strtotime("-{$i} quarters"));
            $quarterStart = ($quarter - 1) * 3 + 1;
            $startDate = "{$year}-" . str_pad($quarterStart, 2, '0', STR_PAD_LEFT) . "-01";
            $endDate = date('Y-m-t', strtotime($startDate . " +2 months"));
            
            // Get income for this quarter
            $incomeSQL = "SELECT COALESCE(SUM(amount), 0) as total 
                         FROM transactions 
                         WHERE user_id = ? 
                         AND transaction_type = 'credit' 
                         AND status IN ('successful', 'completed')
                         AND created_at >= ? 
                         AND created_at <= ?";
            $stmt = $db->query($incomeSQL, [$userId, $startDate, $endDate]);
            $income = $stmt->fetch()['total'];
            $incomeData[] = (float)$income;
            $totalIncome += $income;
            
            // Get expenses for this quarter
            $expenseSQL = "SELECT COALESCE(SUM(amount), 0) as total 
                          FROM transactions 
                          WHERE user_id = ? 
                          AND transaction_type = 'debit' 
                          AND status IN ('successful', 'completed')
                          AND created_at >= ? 
                          AND created_at <= ?";
            $stmt = $db->query($expenseSQL, [$userId, $startDate, $endDate]);
            $expense = $stmt->fetch()['total'];
            $expenseData[] = (float)$expense;
            $totalExpense += $expense;
        }
    }

    // Calculate net profit
    $netProfit = $totalIncome - $totalExpense;

    $data = [
        'success' => true,
        'labels' => $labels,
        'income_data' => $incomeData,
        'expense_data' => $expenseData,
        'total_income' => $totalIncome,
        'total_expense' => $totalExpense,
        'net_profit' => $netProfit
    ];

    echo json_encode($data);

} catch (Exception $e) {
    error_log("Analytics API Error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Failed to fetch analytics data'
    ]);
}
?>
