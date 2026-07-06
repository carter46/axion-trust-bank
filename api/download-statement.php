<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(401);
    die('Unauthorized');
}

$userId = $_SESSION['user_id'];
$fromDate = $_GET['from_date'] ?? date('Y-m-01');
$toDate = $_GET['to_date'] ?? date('Y-m-d');
$status = $_GET['status'] ?? '';

try {
    $db = Database::getInstance();
    
    // Get user info
    $userModel = new User();
    $userInfo = $userModel->findById($userId);
    
    // Build query
    $sql = "SELECT t.*, a.account_number, a.account_type 
            FROM transactions t
            LEFT JOIN accounts a ON t.account_id = a.id
            WHERE t.user_id = ? 
            AND DATE(t.created_at) BETWEEN ? AND ?";
    
    $params = [$userId, $fromDate, $toDate];
    
    if (!empty($status)) {
        $sql .= " AND t.status = ?";
        $params[] = $status;
    }
    
    $sql .= " ORDER BY t.created_at DESC";
    
    $stmt = $db->query($sql, $params);
    $transactions = $stmt->fetchAll();
    
    // Calculate totals
    $totalDebit = 0;
    $totalCredit = 0;
    
    foreach ($transactions as $transaction) {
        if ($transaction['transaction_type'] === 'debit') {
            $totalDebit += $transaction['amount'];
        } else {
            $totalCredit += $transaction['amount'];
        }
    }
    
    // Generate HTML for PDF
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Account Statement</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                padding: 40px;
                color: #333;
            }
            .header {
                text-align: center;
                margin-bottom: 30px;
                border-bottom: 3px solid #1e3a8a;
                padding-bottom: 20px;
            }
            .bank-name {
                font-size: 28px;
                font-weight: 800;
                color: #1e3a8a;
                margin-bottom: 10px;
            }
            .statement-title {
                font-size: 22px;
                font-weight: 600;
                margin-bottom: 5px;
            }
            .statement-period {
                font-size: 14px;
                color: #666;
            }
            .account-info {
                margin: 30px 0;
                padding: 20px;
                background: #f8fafc;
                border-radius: 8px;
            }
            .info-row {
                display: flex;
                margin-bottom: 10px;
            }
            .info-label {
                font-weight: 600;
                width: 200px;
            }
            .summary-boxes {
                display: flex;
                gap: 20px;
                margin: 30px 0;
            }
            .summary-box {
                flex: 1;
                padding: 20px;
                border-radius: 8px;
                text-align: center;
            }
            .summary-box.credit {
                background: #d1fae5;
            }
            .summary-box.debit {
                background: #fee2e2;
            }
            .summary-box.transactions {
                background: #e0f2fe;
            }
            .summary-label {
                font-size: 14px;
                color: #666;
                margin-bottom: 8px;
            }
            .summary-value {
                font-size: 28px;
                font-weight: 700;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 30px;
            }
            th, td {
                padding: 12px;
                text-align: left;
                border-bottom: 1px solid #e2e8f0;
            }
            th {
                background: #f1f5f9;
                font-weight: 700;
                font-size: 12px;
                text-transform: uppercase;
                color: #1e3a8a;
            }
            .amount-credit {
                color: #059669;
                font-weight: 600;
            }
            .amount-debit {
                color: #dc2626;
                font-weight: 600;
            }
            .status-completed {
                color: #059669;
            }
            .status-pending {
                color: #f59e0b;
            }
            .status-failed {
                color: #dc2626;
            }
            .footer {
                margin-top: 50px;
                padding-top: 20px;
                border-top: 2px solid #e2e8f0;
                font-size: 12px;
                color: #666;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <div class="bank-name">SECUREBANK ONLINE</div>
            <div class="statement-title">Account Statement</div>
            <div class="statement-period">Period: ' . date('F d, Y', strtotime($fromDate)) . ' - ' . date('F d, Y', strtotime($toDate)) . '</div>
        </div>
        
        <div class="account-info">
            <div class="info-row">
                <div class="info-label">Account Holder:</div>
                <div>' . htmlspecialchars($userInfo['full_name']) . '</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div>' . htmlspecialchars($userInfo['email']) . '</div>
            </div>
            <div class="info-row">
                <div class="info-label">Statement Date:</div>
                <div>' . date('F d, Y') . '</div>
            </div>
        </div>
        
        <div class="summary-boxes">
            <div class="summary-box credit">
                <div class="summary-label">Total Credits</div>
                <div class="summary-value">$' . number_format($totalCredit, 2) . '</div>
            </div>
            <div class="summary-box debit">
                <div class="summary-label">Total Debits</div>
                <div class="summary-value">$' . number_format($totalDebit, 2) . '</div>
            </div>
            <div class="summary-box transactions">
                <div class="summary-label">Total Transactions</div>
                <div class="summary-value">' . count($transactions) . '</div>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Reference</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>';
    
    foreach ($transactions as $transaction) {
        $isDebit = $transaction['transaction_type'] === 'debit';
        $amountClass = $isDebit ? 'amount-debit' : 'amount-credit';
        $amountSign = $isDebit ? '-' : '+';
        
        $statusClass = 'status-' . strtolower($transaction['status']);
        
        $html .= '<tr>
            <td>' . date('M d, Y', strtotime($transaction['created_at'])) . '</td>
            <td>' . htmlspecialchars($transaction['description']) . '</td>
            <td style="font-family: monospace; font-size: 11px;">' . htmlspecialchars($transaction['transaction_ref']) . '</td>
            <td class="' . $amountClass . '">' . $amountSign . '$' . number_format(abs($transaction['amount']), 2) . '</td>
            <td class="' . $statusClass . '">' . ucfirst($transaction['status']) . '</td>
        </tr>';
    }
    
    $html .= '</tbody>
        </table>
        
        <div class="footer">
            <p>This is an official statement from SecureBank Online</p>
            <p>Generated on ' . date('F d, Y h:i A') . '</p>
            <p>For questions or concerns, please contact customer support</p>
        </div>
    </body>
    </html>';
    
    // Set headers for HTML display (browsers can save as PDF)
    header('Content-Type: text/html; charset=UTF-8');
    header('Content-Disposition: inline; filename="statement_' . date('Y-m-d') . '.html"');
    
    echo $html;
    
} catch (Exception $e) {
    http_response_code(500);
    echo 'Error generating statement: ' . $e->getMessage();
}

