<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

$userId = $_SESSION['user_id'];
$period = $_GET['period'] ?? 'category'; // category, week, month
$cardId = $_GET['card_id'] ?? null;

if (!$cardId) {
    echo json_encode(['success' => false, 'message' => 'Card ID required']);
    exit;
}

try {
    $db = Database::getInstance();
    
    // Verify user owns this card
    $cardModel = new Card();
    $card = $cardModel->findById($cardId);
    
    if (!$card || $card['user_id'] != $userId) {
        echo json_encode(['success' => false, 'message' => 'Card not found']);
        exit;
    }
    
    $categoryColors = ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#f97316', '#84cc16'];
    
    if ($period === 'category') {
        // Get spending by category
        $sql = "SELECT 
                    expense_category as category,
                    SUM(amount) as total,
                    COUNT(*) as count
                FROM card_transactions 
                WHERE card_id = ? 
                AND transaction_type = 'debit'
                AND status = 'completed'
                AND expense_category IS NOT NULL
                GROUP BY expense_category
                ORDER BY total DESC";
        
        $stmt = $db->query($sql, [$cardId]);
        $spending = $stmt->fetchAll();
        
        $totalSpending = array_sum(array_column($spending, 'total'));
        
        $labels = [];
        $data = [];
        $colors = [];
        $legend = [];
        
        foreach ($spending as $index => $item) {
            $labels[] = ucfirst($item['category']);
            $data[] = (float)$item['total'];
            $colors[] = $categoryColors[$index % count($categoryColors)];
            
            $percentage = $totalSpending > 0 ? ($item['total'] / $totalSpending) * 100 : 0;
            $legend[] = [
                'category' => ucfirst($item['category']),
                'total' => $item['total'],
                'percentage' => $percentage,
                'color' => $categoryColors[$index % count($categoryColors)]
            ];
        }
        
        echo json_encode([
            'success' => true,
            'type' => 'doughnut',
            'labels' => $labels,
            'data' => $data,
            'colors' => $colors,
            'total' => $totalSpending,
            'legend' => $legend
        ]);
        
    } elseif ($period === 'week') {
        // Get last 7 days
        $labels = [];
        $data = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $dayName = date('D', strtotime($date));
            $labels[] = $dayName;
            
            $sql = "SELECT COALESCE(SUM(amount), 0) as total 
                    FROM card_transactions 
                    WHERE card_id = ? 
                    AND transaction_type = 'debit'
                    AND status = 'completed'
                    AND DATE(created_at) = ?";
            $stmt = $db->query($sql, [$cardId, $date]);
            $result = $stmt->fetch();
            $data[] = (float)$result['total'];
        }
        
        $totalSpending = array_sum($data);
        
        echo json_encode([
            'success' => true,
            'type' => 'bar',
            'labels' => $labels,
            'data' => $data,
            'colors' => [$categoryColors[0]],
            'total' => $totalSpending
        ]);
        
    } else { // month
        // Get last 6 months
        $labels = [];
        $data = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = date('Y-m-01', strtotime("-{$i} months"));
            $monthName = date('M', strtotime($date));
            $labels[] = $monthName;
            
            $sql = "SELECT COALESCE(SUM(amount), 0) as total 
                    FROM card_transactions 
                    WHERE card_id = ? 
                    AND transaction_type = 'debit'
                    AND status = 'completed'
                    AND YEAR(created_at) = YEAR(?) 
                    AND MONTH(created_at) = MONTH(?)";
            $stmt = $db->query($sql, [$cardId, $date, $date]);
            $result = $stmt->fetch();
            $data[] = (float)$result['total'];
        }
        
        $totalSpending = array_sum($data);
        
        echo json_encode([
            'success' => true,
            'type' => 'bar',
            'labels' => $labels,
            'data' => $data,
            'colors' => [$categoryColors[0]],
            'total' => $totalSpending
        ]);
    }
    
} catch (Exception $e) {
    error_log("Card spending data error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch spending data'
    ]);
}
?>
