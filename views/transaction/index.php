<?php 
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/head.php';

// Get user currency from database (consistent with other pages)
$userModel = new User();
$user = $userModel->findById($_SESSION['user_id']);
$userCurrency = getUserDisplayCurrency($user);

// Include sidebar
include __DIR__ . '/../../includes/sidebar.php';
?>

<style>
    /* Override parent content-area styles (Same as Dashboard & Account) */
    .main-content-area .content-area {
        background: #f5f7fa !important;
        padding: 15px !important;
        overflow-x: hidden !important;
    }
    
    .transaction-page-container {
        max-width: 100%;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 25px;
    }
    
    /* ===== PAGE HEADER STANDARD (Same as Dashboard & Account) ===== */
    .header {
        margin-top: 0;
        margin-bottom: 0;
        padding: 0;
    }

    .header h1 {
        font-size: 28px;
        color: #2d3748;
        padding-top: 20px;
        margin: 0 0 8px 0;
        font-weight: 600;
        text-align: left;
    }
    
    .header p {
        font-size: 15px;
        color: #6c757d;
        margin: 0;
        padding-bottom: 20px;
        text-align: left;
    }
    
    /* Filter Section */
    .filter-container {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        max-width: 100%;
        overflow-x: hidden;
    }
    
    .filter-title {
        font-size: 18px;
        font-weight: 600;
        color: #0033a0;
        margin-bottom: 15px;
    }
    
    .filter-form {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 15px;
        margin-bottom: 15px;
        max-width: 100%;
    }
    
    .filter-group {
        display: flex;
        flex-direction: column;
    }
    
    .filter-group label {
        font-size: 14px;
        margin-bottom: 5px;
        color: #666;
        font-weight: 500;
    }
    
    .filter-group input, .filter-group select {
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #ddd;
        font-size: 14px;
    }
    
    .search-group {
        grid-column: 1 / -1;
    }
    
    .filter-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .filter-buttons {
        display: flex;
        gap: 10px;
    }
    
    .btn {
        padding: 10px 20px;
        border-radius: 5px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s;
    }
    
    .btn-primary {
        background: #0033a0;
        color: white;
    }
    
    .btn-primary:hover {
        background: #002266;
    }
    
    .btn-secondary {
        background: #f0f2f5;
        color: #333;
    }
    
    .btn-secondary:hover {
        background: #e0e5eb;
    }
    
    .mobile-filter-btn {
        display: none;
        width: 100%;
        padding: 12px;
        background: #0033a0;
        color: white;
        border: none;
        border-radius: 5px;
        font-weight: 600;
        margin-bottom: 15px;
        cursor: pointer;
    }
    
    /* Chart Section */
    .chart-container-wrapper {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .chart-container-wrapper canvas {
        height: 200px !important;
        max-height: 200px;
    }
    
    @media (max-width: 768px) {
        .chart-container-wrapper {
            padding: 15px;
        }
        
        .chart-container-wrapper canvas {
            height: 180px !important;
        }
        
        .chart-title {
            font-size: 16px;
        }
    }
    
    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .chart-title {
        font-size: 18px;
        font-weight: 600;
        color: #0033a0;
    }
    
    .chart-actions {
        display: flex;
        gap: 10px;
    }
    
    .chart-actions button {
        background: #f0f2f5;
        border: none;
        padding: 5px 10px;
        border-radius: 5px;
        font-size: 12px;
        cursor: pointer;
        transition: background 0.3s;
    }
    
    .chart-actions button.active {
        background: #0033a0;
        color: white;
    }
    
    .chart-actions button:hover {
        background: #e0e5eb;
    }
    
    .chart-actions button.active:hover {
        background: #002266;
    }
    
    /* Accounts Section */
    .accounts-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 25px;
        max-width: 100%;
    }
    
    .account-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    @media (max-width: 768px) {
        .accounts-container {
            gap: 12px;
        }
        
        .account-card {
            padding: 16px;
            border-radius: 12px;
        }
        
        .account-name {
            font-size: 14px;
        }
        
        .account-number {
            font-size: 11px;
        }
        
        .account-balance {
            font-size: 20px;
        }
        
        .account-change {
            font-size: 13px;
        }
    }
    
    .account-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .account-name {
        font-weight: 600;
        color: #0033a0;
    }
    
    .account-number {
        font-size: 12px;
        color: #666;
    }
    
    .account-balance {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 5px;
    }
    
    .account-change {
        font-size: 14px;
        color: #28a745;
    }
    
    .account-change.negative {
        color: #dc3545;
    }
    
    /* Transactions Section */
    .transactions-container {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        overflow-x: auto;
        max-width: 100%;
    }
    
    .transactions-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .transactions-title {
        font-size: 18px;
        font-weight: 600;
        color: #0033a0;
    }
    
    .toggle-mobile {
        display: none;
        background: #0033a0;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 5px;
        font-weight: 600;
        cursor: pointer;
    }
    
    table {
        width: 100%;
        border-collapse: collapse;
        display: table; /* Show on desktop */
    }
    
    /* Hide mobile transactions on desktop */
    .mobile-transactions {
        display: none;
    }
    
    th {
        text-align: left;
        padding: 12px 15px;
        border-bottom: 2px solid #f0f0f0;
        color: #666;
        font-weight: 600;
    }
    
    td {
        padding: 12px 15px;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .transaction-amount {
        font-weight: 600;
    }
    
    .transaction-amount.positive {
        color: #28a745;
    }
    
    .transaction-amount.negative {
        color: #dc3545;
    }
    
    .transaction-category {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 12px;
        background: #f0f2f5;
    }
    
    .transaction-actions {
        display: flex;
        gap: 8px;
    }
    
    .action-btn {
        background: #f0f2f5;
        border: none;
        padding: 6px 10px;
        border-radius: 4px;
        font-size: 12px;
        cursor: pointer;
        transition: background 0.3s;
    }
    
    .action-btn:hover {
        background: #e0e5eb;
    }
    
    .action-btn.view {
        background: #0033a0;
        color: white;
    }
    
    .action-btn.view:hover {
        background: #002266;
    }
    
    /* Dashboard Transaction Styles */
    .dashboard-card {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        font-family: 'Inter', sans-serif;
    }

    .transactions-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }

    .card-title {
        font-size: 18px;
        font-weight: 600;
        color: #0a0a0a;
    }

    .transactions-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .transaction-item {
        background: #fafbff;
        border-radius: 10px;
        padding: 14px 16px;
        cursor: pointer;
        transition: background 0.2s;
    }

    .transaction-item:hover {
        background: #f0f3ff;
    }

    .transaction-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .transaction-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .transaction-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        flex-shrink: 0;
        font-size: 14px;
    }

    .transaction-info {
        display: flex;
        flex-direction: column;
    }

    .transaction-title {
        font-weight: 500;
        color: #1d1d1d;
        font-size: 15px;
    }

    .transaction-date {
        font-size: 13px;
        color: #777;
        margin-top: 2px;
    }

    .transaction-amount {
        font-size: 15px;
        font-weight: 600;
        white-space: nowrap;
    }

    .transaction-amount.positive {
        color: #00c853;
    }

    .transaction-amount.negative {
        color: #dc3545;
    }

    .transaction-bottom {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 10px;
        border-top: 1px solid #eaeaea;
        padding-top: 10px;
    }

    .transaction-status-badge {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .status-dot {
        width: 6px;
        height: 6px;
        background: #00c853;
        border-radius: 50%;
    }

    .status-text {
        color: #6b7280;
        text-transform: uppercase;
        font-size: 12px;
        font-weight: 600;
    }

    .transaction-arrow {
        font-size: 18px;
        color: #999;
        line-height: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 1px;
    }

    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .pagination a, .pagination span {
        padding: 8px 12px;
        border-radius: 6px;
        text-decoration: none;
        color: #666;
        font-size: 14px;
        transition: all 0.2s;
    }

    .pagination a:hover {
        background: #f0f3ff;
        color: #0033a0;
    }

    .pagination .active {
        background: #0033a0;
        color: white;
        font-weight: 600;
    }

    .pagination .disabled {
        color: #ccc;
        cursor: not-allowed;
    }

    /* Mobile Responsive Styles */
    /* Mobile Responsive (Same as Dashboard) */
    @media (max-width: 768px) {
        .header h1 {
            font-size: 24px;
            padding-top: 15px;
        }
        
        .header p {
            font-size: 14px;
            padding-bottom: 15px;
        }
        
        .mobile-filter-btn {
            display: block;
        }
        
        .filter-container {
            display: none;
        }
        
        .filter-container.active {
            display: block;
        }
        
        .filter-form {
            grid-template-columns: 1fr;
        }
        
        .filter-actions {
            flex-direction: column;
            align-items: stretch;
        }
        
        .filter-buttons {
            width: 100%;
        }
        
        .btn {
            flex: 1;
            text-align: center;
        }
        
        .accounts-container {
            grid-template-columns: 1fr;
            gap: 12px;
        }
        
        .filter-container {
            padding: 15px;
        }
        
        .filter-title {
            font-size: 16px;
        }
        
        .transactions-container {
            padding: 15px;
        }
        
        .transactions-title {
            font-size: 16px;
        }
        
        .transactions-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .toggle-mobile {
            display: block;
            margin-top: 10px;
            width: 100%;
        }
        
        /* Mobile table styles */
        table {
            display: none;
        }
        
        table.active {
            display: table;
        }
        
        .mobile-transactions {
            display: block; /* Show on mobile */
        }
        
        .transaction-card {
            background: white;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border: 1px solid #e8e8e8;
        }
        
        .transaction-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .transaction-date {
            font-weight: 600;
            font-size: 13px;
            color: #666;
        }
        
        .transaction-amount-mobile {
            font-weight: 700;
            font-size: 18px;
        }
        
        .transaction-amount-mobile.positive {
            color: #28a745;
        }
        
        .transaction-amount-mobile.negative {
            color: #dc3545;
        }
        
        .transaction-description {
            margin-bottom: 10px;
            font-weight: 600;
            font-size: 15px;
            color: #2d3748;
        }
        
        .transaction-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }
        
        .transaction-category-mobile {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            background: #e8f0fe;
            color: #0033a0;
        }
        
        .transaction-actions-mobile {
            display: flex;
            gap: 6px;
        }
        
        .transaction-actions-mobile .action-btn {
            font-size: 11px;
            padding: 5px 10px;
        }
        
        .transaction-top {
            flex-direction: column;
            align-items: flex-start;
            gap: 6px;
        }

        .transaction-amount {
            align-self: flex-start;
            margin-top: 4px;
        }

        .transaction-bottom {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .transaction-arrow {
            align-self: center;
            margin-top: 0;
        }
        
        th, td {
            padding: 8px 10px;
        }
    }
    
    @media (max-width: 480px) {
        .header h1 {
            font-size: 22px;
        }
        
        .header p {
            font-size: 13px;
        }
        
        .transaction-page-container {
            padding: 0;
        }
        
        .chart-container-wrapper, .account-card, .transactions-container {
            padding: 15px;
        }
        
        table {
            font-size: 14px;
        }
    }

    /* Pagination */
    .pagination {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-top: 20px;
        flex-wrap: wrap;
    }

    .pagination a, .pagination span {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 5px;
        text-decoration: none;
        color: #0033a0;
        transition: all 0.3s;
    }

    .pagination a:hover {
        background: #0033a0;
        color: white;
    }

    .pagination .active {
        background: #0033a0;
        color: white;
        border-color: #0033a0;
    }

    .pagination .disabled {
        color: #ccc;
        cursor: not-allowed;
    }
    
    /* Hide footer completely on this page */
    footer {
        display: none !important;
    }
</style>

<div class="transaction-page-container">
    <!-- Page Header (Standard Pattern) -->
    <div class="header">
        <h1>Transaction History</h1>
        <p>View and manage all your transactions and download statements</p>
    </div>
    
    <!-- Mobile Filter Button -->
    <button class="mobile-filter-btn" id="mobileFilterBtn">
        <i class="fas fa-filter"></i> Show Filters
    </button>
    
    <!-- Filter Section -->
    <div class="filter-container" id="filterContainer">
        <div class="filter-title">Filter Transactions</div>
        <form method="GET" action="">
            <div class="filter-form">
                <div class="filter-group">
                    <label for="from_date">From Date</label>
                    <input type="date" id="from_date" name="from_date" value="<?php echo htmlspecialchars($data['from_date']); ?>">
                </div>
                <div class="filter-group">
                    <label for="to_date">To Date</label>
                    <input type="date" id="to_date" name="to_date" value="<?php echo htmlspecialchars($data['to_date']); ?>">
                </div>
                <div class="filter-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="">All Status</option>
                        <option value="completed" <?php echo $data['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="pending" <?php echo $data['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="failed" <?php echo $data['status'] === 'failed' ? 'selected' : ''; ?>>Failed</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="type">Transaction Type</label>
                    <select id="type" name="type">
                        <option value="">All Types</option>
                        <option value="deposit" <?php echo $data['type'] === 'deposit' ? 'selected' : ''; ?>>Deposit</option>
                        <option value="withdrawal" <?php echo $data['type'] === 'withdrawal' ? 'selected' : ''; ?>>Withdrawal</option>
                        <option value="transfer" <?php echo $data['type'] === 'transfer' ? 'selected' : ''; ?>>Transfer</option>
                        <option value="payment" <?php echo $data['type'] === 'payment' ? 'selected' : ''; ?>>Payment</option>
                    </select>
                </div>
                <div class="filter-group search-group">
                    <label for="search">Search transactions...</label>
                    <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($data['search']); ?>" placeholder="Enter keyword...">
                </div>
            </div>
            <div class="filter-actions">
                <div class="filter-buttons">
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='<?php echo SITE_URL; ?>/transaction'">Reset</button>
                </div>
                <button type="button" class="btn btn-secondary" onclick="downloadStatement()">
                    <i class="fas fa-download"></i> Download PDF
                </button>
            </div>
        </form>
    </div>
    
    <!-- Chart Section -->
    <div class="chart-container-wrapper">
        <div class="chart-header">
            <div class="chart-title">Account Balance Trend</div>
        </div>
        <div style="height: 200px; position: relative;">
            <canvas id="balanceChart"></canvas>
        </div>
    </div>
    
    <!-- Accounts Section -->
    <div class="accounts-container">
        <?php 
        $db = Database::getInstance();
        $userId = $_SESSION['user_id'];
        $sql = "SELECT * FROM accounts WHERE user_id = ? AND status = 'active' ORDER BY account_type";
        $stmt = $db->query($sql, [$userId]);
        $accounts = $stmt->fetchAll();
        
        foreach ($accounts as $account):
            // Calculate change (recent transactions)
            $sqlChange = "SELECT SUM(CASE WHEN transaction_type = 'credit' THEN amount ELSE -amount END) as change 
                          FROM transactions 
                          WHERE account_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            $stmtChange = $db->query($sqlChange, [$account['id']]);
            $change = 0;
            if ($stmtChange) {
                $changeData = $stmtChange->fetch();
                $change = $changeData['change'] ?? 0;
            }
            
        ?>
        <div class="account-card">
            <div class="account-header">
                <div class="account-name"><?php echo htmlspecialchars(ucfirst($account['account_type'])); ?> Account</div>
                <div class="account-number">***<?php echo substr($account['account_number'], -3); ?></div>
            </div>
            <div class="account-balance"><?php echo formatAccountBalance($account['balance'], $account, $userCurrency); ?></div>
            <div class="account-change <?php echo $change < 0 ? 'negative' : ''; ?>">
                <?php echo $change >= 0 ? '+' : ''; ?><?php echo formatAmountForUser(abs($change), $user, getAccountStoredCurrency($account)); ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Transactions Section - Using Dashboard Style -->
    <div class="dashboard-card" style="margin-top: 25px;">
        <div class="transactions-header">
            <h3 class="card-title">
                Transactions 
                <span style="font-size: 14px; color: #666; font-weight: normal;">
                    (Showing <?php echo count($data['transactions'] ?? []); ?> of <?php echo $data['total_records'] ?? 0; ?>)
                </span>
            </h3>
        </div>
        <div class="transactions-list">
            <?php 
            if (!empty($data['transactions']) && is_array($data['transactions'])) {
                $avatarColors = ['#6C63FF', '#FF6BAA', '#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'];
                $colorIndex = 0;
                foreach ($data['transactions'] as $transaction): 
                    $isNegative = ($transaction['transaction_type'] === 'debit');
                    $amountClass = $isNegative ? 'negative' : 'positive';
                    $amountSign = $isNegative ? '-' : '+';
                    
                    // Get initials from description
                    $words = explode(' ', $transaction['description'] ?? 'Transaction');
                    $initials = '';
                    foreach ($words as $word) {
                        if (!empty($word)) {
                            $initials .= strtoupper(substr($word, 0, 1));
                            if (strlen($initials) >= 2) break;
                        }
                    }
                    if (strlen($initials) < 2) $initials = strtoupper(substr($transaction['description'] ?? 'T', 0, 2));
                    
                    $avatarColor = $avatarColors[$colorIndex % count($avatarColors)];
                    $colorIndex++;
                    
                    $date = date('M d, Y', strtotime($transaction['created_at']));
                    
                    // Get status
                    $status = $transaction['status'] ?? 'completed';
                    $statusDotColor = '#00c853'; // Default completed (green)
                    $statusLabel = 'COMPLETED';
                    if ($status === 'failed') {
                        $statusDotColor = '#ef4444';
                        $statusLabel = 'FAILED';
                    } elseif ($status === 'pending') {
                        $statusDotColor = '#f59e0b';
                        $statusLabel = 'PENDING';
                    } elseif ($status === 'processing') {
                        $statusDotColor = '#4f46e5';
                        $statusLabel = 'PROCESSING';
                    }
            ?>
            <div class="transaction-item" onclick="window.location.href='<?php echo SITE_URL; ?>/transaction?id=<?php echo $transaction['id']; ?>'">
                <div class="transaction-top">
                    <div class="transaction-left">
                        <div class="transaction-avatar" style="background:<?php echo $avatarColor; ?>;"><?php echo htmlspecialchars($initials); ?></div>
                        <div class="transaction-info">
                            <div class="transaction-title"><?php echo htmlspecialchars($transaction['description'] ?? 'Transaction'); ?></div>
                            <div class="transaction-date"><?php echo $date; ?></div>
                        </div>
                    </div>
                    <div class="transaction-amount <?php echo $amountClass; ?>"><?php echo $amountSign; ?><?php echo formatTransactionAmountForUser($transaction, $user, 'amount'); ?></div>
                </div>
                <div class="transaction-bottom">
                    <div class="transaction-status-badge">
                        <span class="status-dot" style="background:<?php echo $statusDotColor; ?>;"></span>
                        <span class="status-text"><?php echo $statusLabel; ?></span>
                    </div>
                    <div class="transaction-arrow">»</div>
                </div>
            </div>
            <?php 
                endforeach;
            } else {
            ?>
            <div class="transaction-item">
                <div class="transaction-top">
                    <div class="transaction-left">
                        <div class="transaction-avatar" style="background:#6C63FF;">NT</div>
                        <div class="transaction-info">
                            <div class="transaction-title">No transactions found</div>
                            <div class="transaction-date"><?php echo date('M d, Y'); ?></div>
                        </div>
                    </div>
                    <div class="transaction-amount positive"><?php echo formatDisplayCurrencyAmount(0, $userCurrency); ?></div>
                </div>
                <div class="transaction-bottom">
                    <div class="transaction-status-badge">
                        <span class="status-dot" style="background:#00c853;"></span>
                        <span class="status-text">-</span>
                    </div>
                    <div class="transaction-arrow">»</div>
                </div>
            </div>
            <?php } ?>
        </div>
        
        <?php if (($data['total_pages'] ?? 0) > 1): ?>
        <!-- Pagination -->
        <div class="pagination" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eaeaea;">
            <?php if (($data['current_page'] ?? 1) > 1): ?>
                <a href="?page=<?php echo ($data['current_page'] ?? 1) - 1; ?>&limit=<?php echo $data['limit'] ?? 20; ?>&from_date=<?php echo $data['from_date'] ?? ''; ?>&to_date=<?php echo $data['to_date'] ?? ''; ?>&status=<?php echo $data['status'] ?? ''; ?>&type=<?php echo $data['type'] ?? ''; ?>&search=<?php echo urlencode($data['search'] ?? ''); ?>">Previous</a>
            <?php else: ?>
                <span class="disabled">Previous</span>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= ($data['total_pages'] ?? 1); $i++): ?>
                <?php if ($i == ($data['current_page'] ?? 1)): ?>
                    <span class="active"><?php echo $i; ?></span>
                <?php else: ?>
                    <a href="?page=<?php echo $i; ?>&limit=<?php echo $data['limit'] ?? 20; ?>&from_date=<?php echo $data['from_date'] ?? ''; ?>&to_date=<?php echo $data['to_date'] ?? ''; ?>&status=<?php echo $data['status'] ?? ''; ?>&type=<?php echo $data['type'] ?? ''; ?>&search=<?php echo urlencode($data['search'] ?? ''); ?>"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>
            
            <?php if (($data['current_page'] ?? 1) < ($data['total_pages'] ?? 1)): ?>
                <a href="?page=<?php echo ($data['current_page'] ?? 1) + 1; ?>&limit=<?php echo $data['limit'] ?? 20; ?>&from_date=<?php echo $data['from_date'] ?? ''; ?>&to_date=<?php echo $data['to_date'] ?? ''; ?>&status=<?php echo $data['status'] ?? ''; ?>&type=<?php echo $data['type'] ?? ''; ?>&search=<?php echo urlencode($data['search'] ?? ''); ?>">Next</a>
            <?php else: ?>
                <span class="disabled">Next</span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<script src="<?php echo SITE_URL; ?>/assets/js/chart.umd.min.js"></script>
<script>
    // Chart initialization
    document.addEventListener('DOMContentLoaded', function() {
        const balanceChartElement = document.getElementById('balanceChart');
        if (!balanceChartElement) {
            console.error('balanceChart element not found');
            return;
        }
        const ctx = balanceChartElement.getContext('2d');
        
        // Get balance data from PHP
        const balanceData = <?php 
            try {
                // Get monthly balance trend (last 7 days)
                $db = Database::getInstance();
                $userId = $_SESSION['user_id'];
                
                // Get primary account for trend
                $sqlAccount = "SELECT id, balance FROM accounts WHERE user_id = ? AND status = 'active' ORDER BY account_type LIMIT 1";
                $stmtAccount = $db->query($sqlAccount, [$userId]);
                $primaryAccount = $stmtAccount ? $stmtAccount->fetch() : null;
                
                if ($primaryAccount && $primaryAccount['id']) {
                    // Get transactions for last 7 days
                    $labels = [];
                    $balances = [];
                    $currentBalance = floatval($primaryAccount['balance']);
                    
                    // Calculate balance for each of last 7 days
                    for ($i = 6; $i >= 0; $i--) {
                        $date = date('Y-m-d', strtotime("-$i days"));
                        $labels[] = date('M d', strtotime($date));
                        
                        // Get transactions after this date
                        $sqlTrans = "SELECT SUM(CASE WHEN transaction_type = 'credit' THEN amount ELSE -amount END) as change 
                                     FROM transactions 
                                     WHERE account_id = ? AND DATE(created_at) > ? AND status = 'completed'";
                        $stmtTrans = $db->query($sqlTrans, [$primaryAccount['id'], $date]);
                        if ($stmtTrans) {
                            $transData = $stmtTrans->fetch();
                            $change = floatval($transData['change'] ?? 0);
                        } else {
                            $change = 0;
                        }
                        
                        $balances[] = round($currentBalance - $change, 2);
                    }
                    
                    echo json_encode(['labels' => $labels, 'balances' => $balances, 'success' => true]);
                } else {
                    // No account found, create sample data
                    echo json_encode([
                        'labels' => ['Oct 8', 'Oct 9', 'Oct 10', 'Oct 11', 'Oct 12', 'Oct 13', 'Oct 14'],
                        'balances' => [10000, 10500, 11000, 10800, 11200, 11500, 12000],
                        'success' => false
                    ]);
                }
            } catch (Exception $e) {
                // Error handling - return sample data
                echo json_encode([
                    'labels' => ['Oct 8', 'Oct 9', 'Oct 10', 'Oct 11', 'Oct 12', 'Oct 13', 'Oct 14'],
                    'balances' => [10000, 10500, 11000, 10800, 11200, 11500, 12000],
                    'success' => false,
                    'error' => $e->getMessage()
                ]);
            }
        ?>;
        
        // Check if we have data
        if (!balanceData.labels || balanceData.labels.length === 0) {
            console.error('No chart data available');
            document.getElementById('balanceChart').parentElement.innerHTML = '<div style="text-align: center; padding: 60px; color: #999;">No transaction data available for chart</div>';
            return;
        }
        
        // Chart data
        const data = {
            labels: balanceData.labels,
            datasets: [{
                label: 'Account Balance',
                data: balanceData.balances,
                borderColor: '#0033a0',
                backgroundColor: 'rgba(0, 51, 160, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        };
        
        // Chart configuration
        const config = {
            type: 'line',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                const userCurrency = '<?php echo $userCurrency; ?>';
                                const currencySymbols = {
                                    'USD': '$', 'EUR': '€', 'GBP': '£', 'JPY': '¥',
                                    'CNY': '¥', 'INR': '₹', 'CAD': 'CA$', 'AUD': 'A$',
                                    'NGN': '₦', 'ZAR': 'R', 'AED': 'د.إ', 'SAR': 'ر.س',
                                    'QAR': 'ر.ق', 'KWD': 'د.ك', 'KES': 'KSh', 'GHS': '₵',
                                    'PKR': '₨', 'BDT': '৳', 'LKR': 'Rs', 'SGD': 'S$',
                                    'MYR': 'RM', 'THB': '฿', 'IDR': 'Rp', 'PHP': '₱',
                                    'VND': '₫', 'KRW': '₩', 'BRL': 'R$', 'MXN': '$',
                                    'ARS': '$', 'CLP': '$', 'COP': '$', 'TRY': '₺',
                                    'ILS': '₪', 'NZD': 'NZ$', 'HKD': 'HK$', 'TWD': 'NT$',
                                    'CHF': 'Fr', 'SEK': 'kr', 'NOK': 'kr', 'DKK': 'kr',
                                    'EGP': 'E£', 'MAD': 'د.م.', 'TND': 'د.ت', 'DZD': 'د.ج',
                                    'PLN': 'zł', 'RUB': '₽', 'CZK': 'Kč', 'HUF': 'Ft',
                                    'RON': 'lei', 'BGN': 'лв', 'PEN': 'S/', 'XOF': 'CFA',
                                    'ZMW': 'ZK'
                                };
                                const symbol = currencySymbols[userCurrency] || (userCurrency + ' ');
                                return `Balance: ${symbol}${context.parsed.y.toLocaleString()}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        ticks: {
                            callback: function(value) {
                                const userCurrency = '<?php echo $userCurrency; ?>';
                                const currencySymbols = {
                                    'USD': '$', 'EUR': '€', 'GBP': '£', 'JPY': '¥',
                                    'CNY': '¥', 'INR': '₹', 'CAD': 'CA$', 'AUD': 'A$',
                                    'NGN': '₦', 'ZAR': 'R', 'AED': 'د.إ', 'SAR': 'ر.س',
                                    'QAR': 'ر.ق', 'KWD': 'د.ك', 'KES': 'KSh', 'GHS': '₵',
                                    'PKR': '₨', 'BDT': '৳', 'LKR': 'Rs', 'SGD': 'S$',
                                    'MYR': 'RM', 'THB': '฿', 'IDR': 'Rp', 'PHP': '₱',
                                    'VND': '₫', 'KRW': '₩', 'BRL': 'R$', 'MXN': '$',
                                    'ARS': '$', 'CLP': '$', 'COP': '$', 'TRY': '₺',
                                    'ILS': '₪', 'NZD': 'NZ$', 'HKD': 'HK$', 'TWD': 'NT$',
                                    'CHF': 'Fr', 'SEK': 'kr', 'NOK': 'kr', 'DKK': 'kr',
                                    'EGP': 'E£', 'MAD': 'د.م.', 'TND': 'د.ت', 'DZD': 'د.ج',
                                    'PLN': 'zł', 'RUB': '₽', 'CZK': 'Kč', 'HUF': 'Ft',
                                    'RON': 'lei', 'BGN': 'лв', 'PEN': 'S/', 'XOF': 'CFA',
                                    'ZMW': 'ZK'
                                };
                                const symbol = currencySymbols[userCurrency] || (userCurrency + ' ');
                                if (value >= 1000) {
                                    return symbol + (value/1000).toFixed(1) + 'k';
                                }
                                return symbol + value.toFixed(0);
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        };
        
        // Create the chart
        const balanceChart = new Chart(ctx, config);
        
        // Mobile filter toggle
        const mobileFilterBtn = document.getElementById('mobileFilterBtn');
        const filterContainer = document.getElementById('filterContainer');
        
        mobileFilterBtn.addEventListener('click', function() {
            filterContainer.classList.toggle('active');
            const icon = this.querySelector('i');
            const text = filterContainer.classList.contains('active') ? 'Hide Filters' : 'Show Filters';
            this.innerHTML = `<i class="fas fa-filter"></i> ${text}`;
        });
        
        // Removed table/mobile toggle - now using dashboard-style list for all screens
    });
    
    function downloadStatement() {
        const fromDate = document.getElementById('from_date').value;
        const toDate = document.getElementById('to_date').value;
        const status = document.getElementById('status').value;
        
        const params = new URLSearchParams({
            from_date: fromDate,
            to_date: toDate,
            status: status
        });
        
        window.open('<?php echo SITE_URL; ?>/api/download-statement.php?' + params.toString(), '_blank');
    }
</script>

<?php include __DIR__ . '/../../includes/mobile-nav.php'; ?>

</body>
</html>
