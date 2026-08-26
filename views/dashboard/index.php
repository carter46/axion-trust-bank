<?php 
$pageTitle = 'Dashboard - ' . getSiteName();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/system-settings.php';

// Get user info
$userFullName = $user['full_name'] ?? 'User';
$userDisplayName = formatDisplayName($userFullName);
$userInitials = function_exists('mb_substr')
    ? mb_strtoupper(mb_substr($userDisplayName, 0, 1, 'UTF-8'), 'UTF-8')
    : strtoupper(substr($userDisplayName, 0, 1));
$currentBalance = sumAccountBalancesForDisplay($userAccounts ?? [], getUserDisplayCurrency($user));
$accountNumber = $primaryAccount['account_number'] ?? 'N/A';
$userCurrency = getUserDisplayCurrency($user);
$primaryAccountCurrency = $primaryAccount ? getAccountStoredCurrency($primaryAccount) : DEFAULT_CURRENCY;
$userStoredCurrency = getUserStoredCurrency($user);
$currentMonth = (int)date('n'); // Current month (1-12) for the expense filter

// Get greeting based on time
$hour = (int)date('H');
if ($hour < 12) {
    $greeting = 'Good Morning';
} elseif ($hour < 17) {
    $greeting = 'Good Afternoon';
} else {
    $greeting = 'Good Evening';
}

// Prepare expense chart data
$expenseChartData = [];
$expenseChartLabels = [];
$expenseChartColors = ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6'];
$colorIndex = 0;
foreach ($expenseCategories as $cat) {
    if ((float)$cat['total'] > 0) {
        $expenseChartData[] = (float)$cat['total'];
        $expenseChartLabels[] = ucfirst($cat['expense_category'] ?? 'other');
        $colorIndex++;
    }
}

// Ensure we have totalExpenses variable (from controller, but add fallback)
if (!isset($totalExpenses)) {
    $totalExpenses = array_sum(array_column($expenseCategories, 'total'));
    if ($totalExpenses == 0) {
        $totalExpenses = 1; // Avoid division by zero
    }
}

// Set additional variables for compatibility
$firstAccount = $primaryAccount ?? null;
$userInfo = $user ?? [];

// Check if user needs onboarding (for modal)
if (!isset($currentUser)) {
    $currentUser = $user ?? [];
}
$needsOnboarding = isset($currentUser['onboarding_completed']) && !$currentUser['onboarding_completed'] ? true : false;

// Check if currency popup should be shown (from controller)
$showCurrencyPopup = isset($showCurrencyPopup) ? $showCurrencyPopup : false;
$detectedCurrency = isset($detectedCurrency) ? $detectedCurrency : null;

$bankOperatingCountry = SystemSettings::getInstance()->get('bank_operating_country', 'United States');
$bankCountryFlagUrl = countryFlagCdnUrl($bankOperatingCountry);
$bankCountryDescriptor = countryToAccountDescriptor($bankOperatingCountry);
// Personal account label + flag follow the user's display currency country (not bank operating country)
$userCountryForFlag = currencyToPrimaryCountry($userCurrency);
$userCountryFlagUrl = countryFlagCdnUrl($userCountryForFlag);
$userCountryFlagEmoji = countryFlagEmoji($userCountryForFlag);
$userCountryDescriptor = countryToAccountDescriptor($userCountryForFlag);
$showKycPrompt = shouldShowKycDashboardPrompt($_SESSION['user_id'] ?? null);

// Include head
include __DIR__ . '/../../includes/head.php';

// Include sidebar and main structure
include __DIR__ . '/../../includes/sidebar.php';
?>

<!-- ===== DASHBOARD PAGE CONTENT ===== -->

<!-- Google Fonts (Poppins) - CSP compliant -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" crossorigin="anonymous">
<!-- Chart.js (local - CSP compliant) -->
<script src="<?php echo SITE_URL; ?>/assets/js/chart.umd.min.js"></script>

<style>
        :root{
            --bg:#f5f7fb;
            --panel-bg:#ffffff;
            --muted:#8b98a8;
            --card-radius:14px;
            --accent-blue-start:#0d1b3a;
            --accent-blue-end:#1a2d5a;
            --soft-shadow: 0 6px 20px rgba(18,40,80,0.06);
            --glass: rgba(255,255,255,0.65);
            --top-blue:#e6f0ff;
            --top-green:#e9fff0;
            --top-pink:#fff0f2;
            --top-purple:#f6eeff;
            --stat-border:#eef3f8;
            --green:#24c26b;
            --red:#ff6b6b;
            --light: #f3f7fb;
            --navy: #0d1b3a;
            --navy-light: #1a2d5a;
            --gray-btn: #f0f2f5;
            --gray-btn-text: #4a5568;
            --purple: #8b5cf6;
            --teal: #14b8a6;
            --orange: #f97316;
            --pink: #ec4899;
        }
        * {
            box-sizing: border-box;
        }
        html,body{
            height:100%;
            margin:0;
            padding: 0;
            font-family:'Poppins',system-ui,-apple-system,Segoe UI,Roboto,"Helvetica Neue",Arial;
            background: linear-gradient(180deg,var(--bg),#ffffff 60%);
            -webkit-font-smoothing:antialiased;
            -moz-osx-font-smoothing:grayscale;
            color:#223;
            font-size:14px;
            overflow-x: hidden;
        }
        /* FORCE content-area to be full width - dashboard specific */
        .main-content-area .content-area {
            flex: 1 1 auto !important;
            padding: 20px !important;
            width: 100% !important;
            max-width: 100% !important;
            background: transparent !important;
            min-height: 100vh;
            overflow-x: hidden !important;
            margin: 0 !important;
            box-sizing: border-box !important;
        }
        
        /* Dashboard container should not limit width */
        .dashboard-wrapper,
        .container {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        
        /* Ensure main-content-area uses full width */
        .main-content-area {
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box;
        }
        
        /* Remove container constraints - dashboard uses full width */
        .container{
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            box-sizing: border-box;
        }

        /* Grid layout for dashboard - full width - ORIGINAL DESIGN: 320px sidebar */
        .grid {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 20px;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box;
            padding: 0;
            margin: 0;
            position: relative;
            align-items: start;
        }

        .octobank-dashboard {
            max-width: 100% !important;
            margin: 0 !important;
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 20px;
            width: 100% !important;
            box-sizing: border-box;
            align-items: start;
        }
        
        .octobank-dashboard > * {
            min-width: 0;
        }
        
        /* Left column in grid */
        .grid > div:first-child,
        .octobank-dashboard > div:first-child {
            min-width: 0;
            width: 100%;
            box-sizing: border-box;
        }
        
        /* Prevent sidebar class from applying sidebar styles inside dashboard */
        .content-area .sidebar {
            position: static !important;
            width: auto !important;
            height: auto !important;
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            transform: none !important;
            overflow: visible !important;
            display: block !important;
            margin: 0 !important;
            padding: 0 !important;
            flex-direction: column !important;
        }

        /* ===== PAGE HEADER STANDARD =====
         * This is the site-wide standard for page titles
         * Use this exact positioning on ALL pages for consistency
         * Acts like breadcrumb text - left-aligned, modest spacing
         */
        .header {
            grid-column: 1 / -1;
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
            word-wrap: break-word;
            text-align: left;
        }
        
        .header p {
            font-size: 15px;
            color: #6c757d;
            margin: 0;
            padding-bottom: 20px;
            text-align: left;
        }
        
        /* Mobile - maintain left alignment for consistency */
        @media (max-width: 768px) {
            .header h1 {
                font-size: 22px;
                padding-top: 15px;
                margin-bottom: 6px;
                text-align: left;
            }
            
            .header p {
                font-size: 14px;
                padding-bottom: 18px;
            }
        }
        
        @media (max-width: 480px) {
            .header h1 {
                font-size: 20px;
                padding-top: 12px;
                margin-bottom: 5px;
                text-align: left;
            }
            
            .header p {
                font-size: 13px;
                padding-bottom: 15px;
            }
        }

        /* Main Content */
        .main-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            width: 100%;
        }

        /* Card Styles */
        .dashboard-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            width: 100%;
            overflow: hidden;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: #2d3748;
        }

        /* Expenses Section */
        .expenses-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .month-filter {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .month-filter select {
            padding: 8px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: white;
            color: #2d3748;
            font-size: 14px;
            cursor: pointer;
        }

        .expenses-container {
            display: flex;
            gap: 30px;
            align-items: center;
        }

        .expenses-chart {
            flex: 1;
            position: relative;
        }

        .chart-wrapper {
            width: 180px;
            height: 180px;
            margin: 0 auto;
        }

        .chart-center-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .chart-total {
            font-size: 22px;
            font-weight: 700;
            color: #2d3748;
        }

        .chart-label {
            font-size: 13px;
            color: #6c757d;
        }

        .expenses-legend {
            flex: 1;
            min-width: 0;
        }

        .expenses-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .expense-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            min-width: 0;
        }

        .expense-info {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
            flex: 1;
        }

        .category-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .category-1 { background: #4f46e5; }
        .category-2 { background: #10b981; }
        .category-3 { background: #f59e0b; }
        .category-4 { background: #ef4444; }
        .category-5 { background: #8b5cf6; }

        .category-name {
            font-weight: 500;
            color: #2d3748;
            font-size: 14px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            min-width: 0;
        }

        .category-percentage {
            font-weight: 600;
            color: #2d3748;
            font-size: 14px;
            flex-shrink: 0;
            margin-left: 10px;
        }

        /* Transactions Section */
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

        .view-all {
            color: #14213d;
            font-size: 14px;
            text-decoration: none;
            font-weight: 500;
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

        /* --- Top Row (Avatar + Text + Amount) --- */
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

        /* --- Bottom Row (Status + Arrow) --- */
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

        /* --- Responsive --- */
        @media (max-width: 600px) {
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
        }

        /* Analytics Chart Section */
        .analytics-chart-container {
            grid-column: 1 / -1;
            margin-top: 10px;
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .chart-title {
            font-size: 18px;
            font-weight: 600;
            color: #2d3748;
        }

        .chart-controls {
            display: flex;
            gap: 10px;
        }

        .chart-controls button {
            padding: 8px 16px;
            border: 1px solid #e2e8f0;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .chart-controls button.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        .chart-container {
            height: 250px;
            position: relative;
        }

        .chart-stats {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            padding: 0 20px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #2d3748;
        }

        .stat-label {
            font-size: 14px;
            color: #6c757d;
            margin-top: 5px;
        }

        /* RIGHT COLUMN STYLES */
        .right-column {
            display: flex;
            flex-direction: column;
            gap: 30px;
            width: 100%;
            min-width: 0;
            box-sizing: border-box;
        }

        /* Enhanced Credit Card Styles */
        .credit-card-container {
            width: 100%;
            max-width: 100%;
            margin: 10px 0;
            perspective: 1000px;
        }

        .credit-card {
            position: relative;
            width: 100%;
            height: 0;
            padding-bottom: 63%;
            transform-style: preserve-3d;
            transition: transform 0.8s ease-in-out;
            cursor: pointer;
            border-radius: 12px;
        }

        .credit-card.is-flipped {
            transform: rotateY(180deg);
        }

        .credit-card.initial-spin {
            animation: cardSpin 1.2s ease-in-out;
        }

        @keyframes cardSpin {
            0% {
                transform: rotateY(0deg);
            }
            50% {
                transform: rotateY(360deg);
            }
            100% {
                transform: rotateY(720deg);
            }
        }

        .card-face {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 12px;
            backface-visibility: hidden;
            overflow: hidden;
            box-sizing: border-box;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        /* Card Color Themes */
        .card-checking {
            background: linear-gradient(135deg, #0a2a43, #114678);
        }

        .card-savings {
            background: linear-gradient(135deg, #dc2626, #f59e0b);
        }

        .card-business {
            background: linear-gradient(135deg, #059669, #0d9488);
        }

        .front-face {
            color: white;
            padding: 25px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card-top-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .account-info {
            flex: 1;
        }

        .account-balance {
            margin-bottom: 10px;
        }

        .balance-label {
            font-size: 14px;
            opacity: 0.8;
            margin-bottom: 8px;
        }

        .balance-amount {
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .account-number {
            font-size: 16px;
            opacity: 0.9;
            margin-top: 15px;
        }

        .card-info-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
        }

        .current-time {
            text-align: left;
        }

        .time-date {
            font-size: 14px;
            opacity: 0.8;
            margin-bottom: 4px;
        }

        .time-clock {
            font-size: 16px;
            font-weight: 600;
        }

        .country-flag {
            width: 40px;
            height: 30px;
            border-radius: 4px;
            background-size: cover;
            background-position: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            line-height: 1;
            background-color: rgba(255,255,255,0.12);
        }

        /* BACK CARD - SIMPLIFIED AND CLEAN */
        .back-face {
            background: #232323;
            color: white;
            transform: rotateY(180deg);
            display: flex;
            flex-direction: column;
            padding: 0;
        }

        .back-content {
            padding: 20px;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .customer-service {
            font-size: 12px;
            text-align: center;
            color: rgba(255,255,255,0.7);
            margin-bottom: 20px;
            padding: 10px;
            background: rgba(0,0,0,0.3);
            border-radius: 6px;
        }

        .magnetic-strip {
            height: 40px;
            background: #000;
            width: 100%;
            margin: 15px 0;
        }

        .signature-area {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 20px 0;
        }

        .signature-strip {
            background: #e5e5e5;
            height: 35px;
            flex: 1;
            border-radius: 4px;
            margin-right: 15px;
            display: flex;
            align-items: center;
            padding: 0 15px;
        }

        .signature-label {
            color: #666;
            font-size: 10px;
            font-style: italic;
        }

        .cvv-strip {
            background: white;
            padding: 8px 12px;
            border-radius: 4px;
            min-width: 70px;
            text-align: center;
        }

        .cvv-label {
            font-size: 9px;
            color: #666;
            margin-bottom: 2px;
        }

        .cvv-value {
            font-size: 12px;
            color: #333;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .customer-info-section {
            margin-top: 15px;
            padding: 15px;
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
        }

        .customer-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 8px;
            color: white;
        }

        .customer-contact {
            font-size: 11px;
            line-height: 1.4;
            color: rgba(255,255,255,0.8);
        }

        .contact-item {
            margin-bottom: 3px;
        }

        .legal-text {
            font-size: 9px;
            text-align: center;
            color: rgba(255,255,255,0.6);
            margin-top: auto;
            padding-top: 15px;
        }

        .card-balance-info {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 16px;
            margin-top: 20px;
        }

        .balance-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .balance-row:last-child {
            margin-bottom: 0;
        }

        .balance-row span {
            color: #666;
            font-size: 14px;
        }

        .balance-row strong {
            color: #032B44;
            font-weight: 600;
        }

        .info-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .info-item span:first-child {
            color: #666;
            font-size: 14px;
        }

        .info-item span:last-child {
            color: #032B44;
            font-weight: 600;
        }

        .status-active {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #10b981;
            font-weight: 600;
            font-size: 14px;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
        }

        .new-transaction-btn {
            width: 100%;
            background: #032B44;
            color: white;
            border: none;
            border-radius: 16px;
            padding: 20px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 8px 20px rgba(3, 43, 68, 0.3);
            margin-top: 10px;
        }

        .new-transaction-btn:hover {
            background: #024a6b;
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(3, 43, 68, 0.4);
        }

        /* Responsive grid adjustments - Desktop first approach */
        @media (min-width: 1441px) {
            .octobank-dashboard,
            .grid {
                grid-template-columns: 1fr 400px;
                width: 100% !important;
                max-width: 100% !important;
            }
        }
        
        @media (min-width: 1201px) and (max-width: 1440px) {
            .octobank-dashboard,
            .grid {
                grid-template-columns: 1fr 400px;
                width: 100% !important;
                max-width: 100% !important;
            }
        }
        
        @media (min-width: 901px) and (max-width: 1200px) {
            .octobank-dashboard,
            .grid {
                grid-template-columns: 1fr 350px;
                gap: 15px;
                width: 100% !important;
                max-width: 100% !important;
            }
        }
        
        @media (min-width: 769px) and (max-width: 900px) {
            .octobank-dashboard,
            .grid {
                grid-template-columns: 1fr 320px;
                gap: 15px;
                width: 100% !important;
                max-width: 100% !important;
            }
        }

        /* ===== MOBILE RESPONSIVE ONLY ===== */
        @media (max-width: 768px) {
            .main-content-area .content-area {
                padding: 10px !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box;
            }
            
            .octobank-dashboard,
            .grid {
                grid-template-columns: 1fr !important;
                gap: 15px;
                display: flex;
                flex-direction: column;
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-sizing: border-box;
            }
            
            /* Ensure all cards are full width */
            .dashboard-card,
            .hero,
            .top-chips,
            .chip,
            .main-content,
            .right-column,
            .transaction-item,
            .stat-item {
                width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box;
            }
            
            /* Mobile Reordering - Card at top, then Transactions, Information, Expenses, Analytics */
            .right-column {
                order: 1;
                width: 100%;
                gap: 15px;
            }
            
            .main-content {
                order: 2;
                grid-template-columns: 1fr;
                gap: 15px;
                display: flex;
                flex-direction: column;
            }
            
            /* Reorder sections for mobile */
            .right-column .dashboard-card:nth-child(1) { /* My Account Card */
                order: 1;
            }
            
            .main-content > .dashboard-card:nth-child(2) { /* Transactions */
                order: 2;
            }
            
            .right-column .dashboard-card:nth-child(2) { /* Information */
                order: 3;
            }
            
            .main-content > .dashboard-card:nth-child(1) { /* Expenses by category */
                order: 4;
            }
            
            .main-content > .analytics-chart-container { /* Analytics */
                order: 5;
            }
            
            .right-column .new-transaction-btn { /* New Transaction Button */
                order: 6;
            }
            
            .dashboard-card {
                padding: 20px;
            }
            
            /* Improved Expenses Section for Mobile */
            .expenses-container {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }
            
            .expenses-legend {
                order: 1;
                width: 100%;
            }
            
            .expenses-chart {
                order: 2;
            }
            
            .expenses-list {
                width: 100%;
                max-width: 280px;
                margin: 0 auto;
            }
            
            .chart-wrapper {
                width: 160px;
                height: 160px;
            }
            
            .chart-total {
                font-size: 18px;
            }
            
            .transaction-item {
                padding: 10px;
                gap: 12px;
                min-width: 0;
                overflow: hidden;
                flex-wrap: nowrap;
                align-items: center !important;
            }
            
            .transaction-details {
                flex: 1 1 auto !important;
                min-width: 0 !important;
                max-width: 100% !important;
                overflow: hidden !important;
            }
            
            .transaction-title,
            .transaction-date {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 100%;
            }
            
            .transaction-item {
                flex-direction: column;
                align-items: flex-start;
                padding: 12px;
            }

            .transaction-status-amount {
                justify-content: space-between;
                width: 100%;
                margin-top: 10px;
            }

            .transaction-amount {
                font-size: 14px;
            }
            
            .avatar {
                width: 35px !important;
                height: 35px !important;
                font-size: 13px;
                flex-shrink: 0;
                min-width: 35px;
                min-height: 35px;
                max-width: 35px;
                max-height: 35px;
                border-radius: 50% !important;
                aspect-ratio: 1 / 1;
            }
            .avatar-img {
                width: 100% !important;
                height: 100% !important;
                min-width: 100% !important;
                min-height: 100% !important;
                max-width: 100% !important;
                max-height: 100% !important;
                object-fit: cover !important;
            }
            
            /* Fix transaction avatar specifically */
            .transaction-avatar {
                width: 40px !important;
                height: 40px !important;
                min-width: 40px !important;
                min-height: 40px !important;
                max-width: 40px !important;
                max-height: 40px !important;
                flex-shrink: 0 !important;
                border-radius: 50% !important;
                aspect-ratio: 1 / 1 !important;
                box-sizing: border-box !important;
            }
            
            .chart-container {
                height: 200px;
            }
            
            .chart-stats {
                gap: 10px;
            }
            
            .stat-item {
                min-width: 80px;
            }
            
            .stat-value {
                font-size: 18px;
            }
            
            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .expenses-header,
            .transactions-header,
            .chart-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .chart-controls {
                width: 100%;
                justify-content: center;
            }
            
            .month-filter {
                width: 100%;
                justify-content: flex-start;
            }

            /* Mobile card adjustments */
            .front-face,
            .back-content {
                padding: 20px;
            }
            
            .balance-amount {
                font-size: 28px;
            }
            
            .account-number {
                font-size: 14px;
            }
            
            .country-flag {
                width: 35px;
                height: 26px;
            }

            .customer-name {
                font-size: 14px;
            }

            .customer-contact {
                font-size: 10px;
            }
        }

        @media (max-width: 480px) {
            .main-content-area .content-area {
                padding: 5px !important;
            }
            
            .dashboard-card {
                padding: 15px;
            }
            
            .chart-wrapper {
                width: 140px;
                height: 140px;
            }
            
            .transaction-item {
                padding: 8px;
                gap: 10px;
            }
            
            .avatar {
                width: 32px;
                height: 32px;
                min-width: 32px;
                min-height: 32px;
                max-width: 32px;
                max-height: 32px;
                font-size: 12px;
            }
            .avatar.avatar-img {
                width: 32px !important;
                height: 32px !important;
                min-width: 32px !important;
                min-height: 32px !important;
                max-width: 32px !important;
                max-height: 32px !important;
                border-radius: 50% !important;
                object-fit: cover !important;
                flex-shrink: 0 !important;
                flex-grow: 0 !important;
                box-sizing: border-box !important;
                aspect-ratio: 1 / 1 !important;
            }
            
            .new-transaction-btn {
                padding: 16px;
                font-size: 15px;
            }
            
            /* Mobile card adjustments */
            .front-face,
            .back-content {
                padding: 15px;
            }
            
            .balance-amount {
                font-size: 24px;
            }
        }

        /* Tablet and larger screen adjustments */
        @media (min-width: 769px) and (max-width: 1024px) {
            .balance-amount {
                font-size: 36px;
            }
            
            .account-number {
                font-size: 18px;
            }
            
            .time-date,
            .time-clock {
                font-size: 15px;
            }
            
            .country-flag {
                width: 45px;
                height: 34px;
            }
        }

        @media (min-width: 1025px) {
            .balance-amount {
                font-size: 38px;
            }
            
            .account-number {
                font-size: 18px;
            }
            
            .time-date,
            .time-clock {
                font-size: 16px;
            }
            
            .country-flag {
                width: 50px;
                height: 38px;
            }
        }

        /* ===== NEW DASHBOARD DESIGN CSS ===== */
        /* Top summary chips */
        .top-chips{
            display:flex;
            gap:18px;
            align-items:center;
            margin-bottom:18px;
            flex-wrap:wrap;
        }
        .chip{
            flex: 1 1 calc(25% - 18px);
            min-width: 200px;
            border-radius:12px;
            padding:14px 18px;
            box-shadow:var(--soft-shadow);
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:12px;
            background:linear-gradient(90deg,#fff,#fff);
            border:1px solid rgba(0,0,0,0.03);
        }
        .chip .left{
            display:flex;
            gap:12px;
            align-items:center;
        }
        .chip .title{
            font-size:13px;
            color:var(--muted);
        }
        .chip .value{
            font-weight:700;
            font-size:18px;
            color:var(--navy);
        }
        .chip.blue,
        .chip.green,
        .chip.pink,
        .chip.purple{
            background: #f3f4f6;
            border:1px solid #e5e7eb;
        }
        .chip .icon{
            width:48px;
            height:48px;
            border-radius:10px;
            display:flex;
            align-items:center;
            justify-content:center;
            box-shadow: none;
            font-size:18px;
            color:#fff;
            background: #e5e7eb;
        }
        .chip.blue .icon,
        .chip.green .icon,
        .chip.pink .icon,
        .chip.purple .icon{
            background: #e5e7eb;
            color: #334155;
        }
        /* Main layout: left big card + right sidebar */
        .grid{
            display:grid;
            grid-template-columns: 1fr 320px;
            gap:20px;
            align-items:start;
        }
        /* Main hero card (navy blue gradient) */
        .hero{
            background: linear-gradient(180deg, var(--accent-blue-start), var(--accent-blue-end));
            border-radius:var(--card-radius);
            padding:22px;
            color: #fff;
            position:relative;
            box-shadow: 0 12px 30px rgba(7,44,75,0.14);
            overflow:hidden;
            min-height:180px;
        }
        .hero .top-row{
            display:flex;
            align-items:flex-start;
            justify-content:space-between;
            gap:12px;
            margin-bottom:10px;
        }
        .user{
            display:flex;
            gap:14px;
            align-items:center;
        }
        .avatar{
            width:44px;
            height:44px;
            min-width:44px;
            min-height:44px;
            max-width:44px;
            max-height:44px;
            border-radius:50%;
            border:2px solid rgba(255,255,255,0.18);
            overflow:hidden;
            box-shadow:0 4px 12px rgba(6,28,50,0.12);
            flex-shrink:0;
            background:linear-gradient(135deg,#ffd6b6,#ffd6b6);
            display:flex;
            align-items:center;
            justify-content:center;
            font-weight:700;
            color:#fff;
            box-sizing:border-box;
            aspect-ratio:1/1;
        }
        .avatar.avatar-img{
            width:44px;
            height:44px;
            min-width:44px;
            min-height:44px;
            max-width:44px;
            max-height:44px;
            border-radius:50%;
            object-fit:cover;
            border:2px solid rgba(255,255,255,0.18);
            flex-shrink:0;
            flex-grow:0;
            box-sizing:border-box;
            aspect-ratio:1/1;
            display:block;
        }
        .user .greet{
            font-size:13px;
            opacity:0.95;
        }
        .user .name{
            font-size:18px;
            font-weight:700;
            letter-spacing:0.2px;
            margin-top:2px;
        }
        .clock{
            text-align:right;
            font-weight:600;
        }
        .clock .time{
            font-size:16px;
            font-weight:700;
            letter-spacing:0.6px;
        }
        .clock .date{
            font-size:11px;
            color:rgba(255,255,255,0.85);
            margin-top:2px;
        }
        .balance{
            margin-top:6px;
            margin-bottom:18px;
        }
        .balance-label{
            font-size:14px;
            opacity:0.8;
            margin-bottom:5px;
        }
        .balance-amount{
            font-size:32px;
            font-weight:800;
            letter-spacing:0.6px;
        }
        .account-row{
            display:flex;
            gap:16px;
            align-items:center;
            justify-content:flex-start;
        }
        .account-box{
            background: rgba(255,255,255,0.12);
            border-radius:12px;
            padding:12px 16px;
            display:flex;
            gap:14px;
            align-items:center;
            min-width:240px;
            box-shadow: inset 0 -12px 30px rgba(255,255,255,0.02);
        }
        .account-box .acc-left{
            display:flex;
            gap:12px;
            align-items:center;
        }
        .shield{
            width:36px;
            height:36px;
            border-radius:8px;
            background:rgba(255,255,255,0.08);
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:22px;
            line-height:1;
            color:rgba(255,255,255,0.9);
            overflow:hidden;
            flex-shrink:0;
        }
        .shield .flag-emoji{
            font-size:22px;
            line-height:1;
            display:block;
        }
        .acc-details .lbl{
            font-size:11px;
            color:rgba(255,255,255,0.9);
        }
        .acc-details .num{
            font-weight:700;
            font-size:14px;
            margin-top:4px;
            color:#fff;
        }
        .badge{
            margin-left:8px;
            border-radius:12px;
            font-weight:600;
            padding:4px 8px;
            font-size:11px;
            background:rgba(0,0,0,0.12);
            color:#dfffe9;
            display:inline-flex;
            align-items:center;
            gap:6px;
        }
        .hero .actions{
            margin-left:auto;
            display:flex;
            gap:10px;
            align-items:center;
        }
        .btn{
            padding:8px 12px;
            border-radius:10px;
            border:1px solid rgba(255,255,255,0.14);
            font-weight:700;
            background:rgba(255,255,255,0.06);
            color:#fff;
            cursor:pointer;
            min-width:80px;
            text-align:center;
            box-shadow: 0 4px 12px rgba(6,28,50,0.08);
            transition: all 0.2s ease;
            font-size: 13px;
        }
        .btn.secondary{
            background: var(--gray-btn);
            color: var(--navy);
            border: 1px solid rgba(13, 27, 58, 0.1);
        }
        .btn.primary{
            background: linear-gradient(135deg, var(--navy-light), var(--navy));
            color: white;
            border: none;
        }
        .account-type-selector {
            margin-top: 15px;
            display: flex;
            gap: 8px;
            align-items: center;
            margin-bottom: 20px;
        }
        .account-type-selector select {
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.2);
            background: rgba(255,255,255,0.1);
            color: white;
            font-size: 13px;
            outline: none;
            cursor: pointer;
            min-width: 160px;
            max-width: 200px;
            width: 100%;
        }
        .account-type-selector select option {
            background: var(--navy);
            color: white;
        }
        .sidebar{
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .dashboard-card {
            background: var(--panel-bg);
            border-radius: 12px;
            padding: 18px;
            box-shadow: var(--soft-shadow);
            border: 1px solid var(--stat-border);
        }
        .dashboard-card h3{
            margin:0 0 12px 0;
            font-size:16px;
            font-weight:600;
            color:var(--navy);
        }
        .stat{
            display:flex;
            align-items:center;
            gap:12px;
            padding:14px 0;
            border-bottom:1px solid #f1f6fb;
        }
        .stat:last-child{ border-bottom:none; padding-bottom:0; }
        .stat .icon{
            width:46px;
            height:46px;
            border-radius:10px;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:18px;
            color:#fff;
            flex-shrink:0;
        }
        .stat .meta{
            display:flex;
            flex-direction:column;
        }
        .stat .meta .label{ font-size:13px; color:var(--muted); }
        .stat .meta .value{ font-weight:700; margin-top:6px; font-size:15px; color:var(--navy); }
        .icon.blue,
        .icon.yellow,
        .icon.green,
        .icon.purple{
            background: #e5e7eb;
            color: #334155;
        }
        .actions-panel{
            background:var(--panel-bg);
            border-radius:12px;
            padding:18px;
            margin-top:20px;
            box-shadow:var(--soft-shadow);
            border:1px solid var(--stat-border);
        }
        .actions-panel h4{ margin:0 0 6px 0; font-weight:700; font-size:16px; color: var(--navy);}
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 10px;
            margin-top: 16px;
        }
        .action-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 12px 8px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
            min-height: 75px;
            color: white;
        }
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.2);
        }
        .action-btn .icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 6px;
            font-size: 16px;
            color: white;
            background: rgba(255,255,255,0.2);
        }
        .action-btn .text {
            font-weight: 600;
            font-size: 11px;
        }
        .btn-transfer {
            background: linear-gradient(135deg, var(--navy-light), var(--navy));
        }
        .btn-account {
            background: linear-gradient(135deg, var(--navy-light), var(--navy));
        }
        .btn-card {
            background: linear-gradient(135deg, var(--navy-light), var(--navy));
        }
        .btn-loan {
            background: linear-gradient(135deg, var(--navy-light), var(--navy));
        }
        .btn-accounts {
            background: linear-gradient(135deg, var(--navy-light), var(--navy));
        }
        .btn-investments {
            background: linear-gradient(135deg, var(--navy-light), var(--navy));
        }
        .btn-support {
            background: linear-gradient(135deg, var(--navy-light), var(--navy));
        }
        .expenses-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }
        .month-filter select {
            padding: 6px 10px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            background: white;
            font-size: 12px;
            color: var(--navy);
            outline: none;
            cursor: pointer;
            min-width: 100px;
        }
        .expenses-container {
            display: flex;
            gap: 16px;
            align-items: center;
        }
        .expenses-chart {
            position: relative;
            width: 100px;
            height: 100px;
            flex-shrink: 0;
        }
        .chart-wrapper {
            width: 100%;
            height: 100%;
        }
        .chart-center-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }
        .chart-total {
            font-size: 14px;
            font-weight: 700;
            color: var(--navy);
            line-height: 1.2;
        }
        .chart-label {
            font-size: 10px;
            color: var(--muted);
            line-height: 1.2;
        }
        .expenses-legend {
            flex: 1;
        }
        .expenses-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .expense-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 4px 0;
        }
        .expense-info {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .category-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }
        .category-name {
            font-size: 12px;
            color: var(--navy);
            text-transform: capitalize;
        }
        .category-percentage {
            font-size: 12px;
            font-weight: 600;
            color: var(--navy);
        }
        /* Duplicate transaction styles removed - using new design from main Transactions Section */
        .mobile-layout {
            display: none;
        }
        @media (max-width: 1024px){
            .chip{
                flex: 1 1 calc(50% - 18px);
            }
        }
        @media (max-width: 980px){
            .grid{
                grid-template-columns: 1fr;
            }
            .sidebar{
                order: 3;
            }
            .hero{
                order: 1;
            }
            .top-chips{
                order: 2;
                margin-top: 20px;
            }
            .top-chips .chip{
                min-width:180px;
                flex:1 1 180px;
            }
            .mobile-layout {
                display: block;
            }
            .desktop-layout {
                display: none;
            }
            .actions-grid {
                grid-template-columns: repeat(3, 1fr);
            }
            .expenses-container {
                justify-content: center;
            }
            .expenses-chart {
                width: 120px;
                height: 120px;
            }
        }
        @media (max-width: 768px){
            .container{
                padding: 16px !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box;
            }
            .hero .top-row {
                flex-direction: row;
                align-items: flex-start;
                gap: 16px;
            }
            .clock {
                text-align: right;
                width: auto;
            }
            .account-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }
            .account-box {
                min-width: 100%;
                width: 100%;
            }
            .hero .actions {
                width: 100%;
                justify-content: space-between;
            }
        }
        @media (max-width:520px){
            .container{ 
                padding: 12px !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box;
            }
            .chip{
                padding:12px;
                flex: 1 1 100%;
            }
            .hero{ padding:16px; border-radius:12px; }
            .avatar{ width:40px; height:40px; }
            .balance-amount{ font-size:24px; }
            .account-box{ min-width:100%; padding:10px; }
            .user .greet { font-size: 12px; }
            .user .name { font-size: 16px; }
            .clock .time{ font-size:14px; }
            .clock .date{ font-size:10px; }
            .chip .value{ font-size:16px; }
            .chip .icon{ width:42px; height:42px; }
            .action-btn {
                padding: 10px 6px;
                min-height: 70px;
            }
            .action-btn .icon {
                width: 28px;
                height: 28px;
                font-size: 14px;
                margin-bottom: 4px;
            }
            .action-btn .text {
                font-size: 10px;
            }
            .btn {
                min-width: 100px;
                padding: 10px 12px;
            }
            .transaction-item {
                flex-wrap: wrap;
            }
            .transaction-status-amount {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
                width: 100%;
                margin-top: 8px;
            }
            .actions-grid {
                gap: 10px;
            }
            .expenses-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
            .month-filter {
                width: 100%;
            }
            .month-filter select {
                width: 100%;
            }
            .expenses-container {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            .expenses-legend {
                width: 100%;
            }
            .expenses-list {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }
        }
        .hero::after{
            content:'';
            position:absolute;
            right:-80px;
            top:30px;
            width:220px;
            height:220px;
            background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.06), rgba(255,255,255,0) 40%);
            transform: rotate(20deg);
            pointer-events:none;
        }

        /* Onboarding Modal Styles */
        .onboarding-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            z-index: 10001;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease-in-out;
        }

        .onboarding-modal {
            background: white;
            border-radius: 16px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            animation: slideUp 0.4s ease-out;
        }

        .onboarding-header {
            text-align: center;
            padding: 40px 30px 30px;
            background: linear-gradient(135deg, #f8fafc 0%, #e8f0ff 100%);
            border-bottom: 1px solid #e0e0e0;
        }

        .onboarding-icon {
            margin-bottom: 20px;
        }

        .onboarding-header h2 {
            font-size: 28px;
            color: #202124;
            margin: 0 0 10px 0;
            font-weight: 700;
        }

        .onboarding-header p {
            color: #666;
            font-size: 15px;
            margin: 0;
        }

        .onboarding-content {
            padding: 30px;
        }

        .onboarding-checklist {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .checklist-item {
            display: flex;
            gap: 16px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 12px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s;
        }

        .checklist-item:hover {
            border-color: #1e3a8a;
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.1);
        }

        .checklist-item.completed {
            background: #f0f9ff;
            border-color: #4caf50;
        }

        .checklist-icon {
            font-size: 24px;
            flex-shrink: 0;
        }

        .checklist-details h4 {
            margin: 0 0 8px 0;
            font-size: 18px;
            color: #202124;
            font-weight: 600;
        }

        .checklist-details p {
            margin: 0 0 12px 0;
            font-size: 14px;
            color: #666;
        }

        .checklist-action {
            display: inline-block;
            padding: 8px 16px;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .checklist-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-badge.success {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .onboarding-footer {
            display: flex;
            gap: 12px;
            padding: 20px 30px;
            border-top: 1px solid #e0e0e0;
            justify-content: flex-end;
        }

        .onboarding-footer .btn-secondary {
            padding: 12px 24px;
            background: #f5f5f5;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            color: #666;
            transition: all 0.2s;
        }

        .onboarding-footer .btn-secondary:hover {
            background: #e0e0e0;
        }

        .onboarding-footer .btn-primary {
            padding: 12px 24px;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            color: white;
            transition: all 0.2s;
        }

        .onboarding-footer .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(30, 58, 138, 0.3);
        }

        @media (max-width: 768px) {
            .onboarding-modal {
                width: 95%;
                margin: 20px;
            }
            
            .onboarding-header {
                padding: 30px 20px 20px;
            }
            
            .onboarding-header h2 {
                font-size: 24px;
            }
            
            .onboarding-content {
                padding: 20px;
            }
            
            .checklist-item {
                flex-direction: column;
                text-align: center;
            }
            
            .onboarding-footer {
                flex-direction: column;
            }
            
            .onboarding-footer button {
                width: 100%;
            }
        }

        /* Currency Selection Modal Styles */
        .currency-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            z-index: 10002;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease-in-out;
        }

        .currency-modal {
            background: white;
            border-radius: 16px;
            width: 90%;
            max-width: 550px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            animation: slideUp 0.4s ease-out;
        }

        .currency-modal-header {
            text-align: center;
            padding: 40px 30px 30px;
            background: linear-gradient(135deg, #f8fafc 0%, #e8f0ff 100%);
            border-bottom: 1px solid #e0e0e0;
            border-radius: 16px 16px 0 0;
        }

        .currency-icon {
            margin-bottom: 20px;
        }

        .currency-modal-header h2 {
            font-size: 28px;
            color: #202124;
            margin: 0 0 10px 0;
            font-weight: 700;
        }

        .currency-modal-header p {
            color: #666;
            font-size: 15px;
            margin: 0;
            line-height: 1.5;
        }

        .currency-modal-content {
            padding: 30px;
        }

        .currency-info {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .currency-option {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 12px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s;
        }

        .currency-option:hover {
            border-color: #1e3a8a;
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.1);
        }

        .currency-option-header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            font-size: 16px;
            color: #202124;
            font-weight: 600;
        }

        .currency-option-value {
            font-size: 18px;
            color: #333;
            margin-top: 8px;
        }

        .currency-code {
            font-weight: 700;
            color: #1e3a8a;
            font-size: 20px;
        }

        .currency-divider {
            text-align: center;
            position: relative;
            margin: 10px 0;
        }

        .currency-divider::before {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            top: 50%;
            height: 1px;
            background: #e0e0e0;
        }

        .currency-divider span {
            background: white;
            padding: 0 15px;
            color: #999;
            font-weight: 600;
            position: relative;
            z-index: 1;
        }

        .currency-note {
            margin-top: 20px;
            padding: 12px;
            background: #f0f9ff;
            border-left: 4px solid #3b82f6;
            border-radius: 6px;
            font-size: 13px;
            color: #666;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .currency-modal-footer {
            display: flex;
            gap: 12px;
            padding: 20px 30px;
            border-top: 1px solid #e0e0e0;
            justify-content: space-between;
        }

        .btn-currency-secondary,
        .btn-currency-primary {
            flex: 1;
            padding: 14px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.2s;
        }

        .btn-currency-secondary {
            background: #f5f5f5;
            color: #666;
        }

        .btn-currency-secondary:hover {
            background: #e0e0e0;
            transform: translateY(-2px);
        }

        .btn-currency-primary {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
        }

        .btn-currency-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(30, 58, 138, 0.3);
        }

        @media (max-width: 768px) {
            .currency-modal {
                width: 95%;
                margin: 20px;
            }
            
            .currency-modal-header {
                padding: 30px 20px 20px;
            }
            
            .currency-modal-header h2 {
                font-size: 24px;
            }
            
            .currency-modal-content {
                padding: 20px;
            }
            
            .currency-modal-footer {
                flex-direction: column;
            }
            
            .currency-modal-footer button {
                width: 100%;
            }
        }
</style>

<!-- Dashboard Content Container - NEW DESIGN -->
<!-- Full width - no container wrapper, padding handled by content-area -->
<?php if (function_exists('isRestrictedStatus') && isRestrictedStatus($user['status'] ?? '')): ?>
    <div style="
        margin: 0 0 16px 0;
        padding: 14px 16px;
        border-radius: 12px;
        background: rgba(239, 68, 68, 0.10);
        border: 1px solid rgba(239, 68, 68, 0.25);
        color: #7f1d1d;
        display: flex;
        gap: 10px;
        align-items: flex-start;
    ">
        <div style="font-size: 18px; line-height: 1;">⚠️</div>
        <div>
            <div style="font-weight: 700; margin-bottom: 2px;">Account Restricted</div>
            <div style="font-weight: 500;"><?php echo htmlspecialchars(restrictedAccountMessage()); ?></div>
        </div>
    </div>
<?php endif; ?>
<div class="grid">
        <!-- Left main area -->
        <div>
            <!-- Mobile layout - hero first -->
            <div class="mobile-layout">
                <div class="hero" role="region" aria-label="main account card">
                    <div class="top-row">
                        <div class="user" aria-hidden="true">
                            <?php if (!empty($user['profile_picture']) && file_exists(BASE_PATH . $user['profile_picture'])): ?>
                                <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="avatar avatar-img" title="avatar">
                            <?php else: ?>
                                <div class="avatar" title="avatar">
                                    <?php echo $userInitials; ?>
                                </div>
                            <?php endif; ?>
                            <div>
                                <div class="greet"><?php echo $greeting; ?></div>
                                <div class="name"><?php echo htmlspecialchars($userDisplayName); ?></div>
                            </div>
                        </div>
                        <div class="clock" aria-hidden="true">
                            <div class="time" id="clock">--:--:--</div>
                            <div class="date" id="date"><?php echo date('m/d/Y'); ?></div>
                        </div>
                    </div>
                    <div class="balance">
                        <div class="balance-label">Available Balance</div>
                        <div class="balance-amount"><?php echo $primaryAccount ? formatAccountBalance($primaryAccount['balance'] ?? 0, $primaryAccount, $userCurrency) : formatCurrency(0, $userCurrency, $userCurrency); ?></div>
                    </div>
                    <!-- Account Type Selector -->
                    <div class="account-type-selector">
                        <select id="accountType" class="custom-select" data-custom-select="true" data-label="Select Account" onchange="changeAccount(this.value)">
                            <?php if (!empty($userAccounts)): ?>
                                <?php foreach ($userAccounts as $acc): ?>
                                    <option value="<?php echo $acc['id']; ?>" data-number="<?php echo htmlspecialchars($acc['account_number']); ?>" <?php echo ($acc['id'] == ($primaryAccount['id'] ?? null)) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars(ucfirst($acc['account_type'] ?? 'checking')); ?> - <?php echo htmlspecialchars($acc['account_number']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="">No accounts</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="account-row">
                        <div class="account-box" aria-hidden="true">
                            <div class="acc-left">
                                <div class="shield" title="<?php echo htmlspecialchars($userCountryForFlag); ?>" aria-hidden="true">
                                    <?php if (!empty($userCountryFlagEmoji)): ?>
                                        <span class="flag-emoji"><?php echo $userCountryFlagEmoji; ?></span>
                                    <?php elseif ($userCountryFlagUrl): ?>
                                        <img src="<?php echo htmlspecialchars($userCountryFlagUrl); ?>" alt="<?php echo htmlspecialchars($userCountryForFlag); ?>" style="width: 36px; height: 36px; border-radius: 10px; object-fit: cover; display: block;">
                                    <?php else: ?>
                                        <span class="flag-emoji">🏳️</span>
                                    <?php endif; ?>
                                </div>
                                <div class="acc-details">
                                    <div class="lbl">Your ( <?php echo htmlspecialchars($userCountryDescriptor); ?> ) Account Number</div>
                                    <div class="num" id="accountNumberDisplay"><?php echo htmlspecialchars($accountNumber); ?></div>
                                </div>
                                <div class="badge" style="margin-left:10px;background:rgba(0,255,128,0.12);color:#07b36a;">
                                    <span style="font-size:10px;">●</span>
                                    Active
                                </div>
                            </div>
                        </div>
                        <div class="actions">
                            <a href="<?php echo SITE_URL; ?>/transaction" class="btn secondary" title="Transactions">
                                Transactions
                            </a>
                            <a href="<?php echo SITE_URL; ?>/transfer" class="btn primary" title="Send Money">
                                Send Money
                            </a>
                        </div>
                    </div>
                </div>
                <!-- Mobile chips after hero -->
                <div class="top-chips" role="region" aria-label="quick-summary">
                    <div class="chip blue">
                        <div class="left">
                            <div>
                                <div class="title">Total Balance</div>
                                <div class="value"><?php echo formatCurrency($currentBalance, $userCurrency, $userCurrency); ?></div>
                            </div>
                        </div>
                        <div class="icon" aria-hidden="true">💰</div>
                    </div>
                    <div class="chip green">
                        <div class="left">
                            <div>
                                <div class="title">Monthly Income</div>
                                <div class="value" style="color:#2d9b4a;"><?php echo formatCurrency($monthlyIncome, $userCurrency, $primaryAccountCurrency); ?></div>
                            </div>
                        </div>
                        <div class="icon" aria-hidden="true">📈</div>
                    </div>
                    <div class="chip pink">
                        <div class="left">
                            <div>
                                <div class="title">Monthly Outgoing</div>
                                <div class="value" style="color:#d44f5a;"><?php echo formatCurrency($monthlyOutgoing, $userCurrency, $primaryAccountCurrency); ?></div>
                            </div>
                        </div>
                        <div class="icon" aria-hidden="true">📉</div>
                    </div>
                    <div class="chip purple">
                        <div class="left">
                            <div>
                                <div class="title">Investment Balance</div>
                                <div class="value" style="color:#7a4be6;"><?php echo formatCurrency($investmentBalance, $userCurrency, $userStoredCurrency); ?></div>
                            </div>
                        </div>
                        <div class="icon" aria-hidden="true">💎</div>
                    </div>
                </div>
            </div>
            <!-- Desktop hero -->
            <div class="desktop-layout">
                <div class="hero" role="region" aria-label="main account card">
                    <div class="top-row">
                        <div class="user" aria-hidden="true">
                            <?php if (!empty($user['profile_picture']) && file_exists(BASE_PATH . $user['profile_picture'])): ?>
                                <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="avatar avatar-img" title="avatar">
                            <?php else: ?>
                                <div class="avatar" title="avatar">
                                    <?php echo $userInitials; ?>
                                </div>
                            <?php endif; ?>
                            <div>
                                <div class="greet"><?php echo $greeting; ?></div>
                                <div class="name"><?php echo htmlspecialchars($userDisplayName); ?></div>
                            </div>
                        </div>
                        <div class="clock" aria-hidden="true">
                            <div class="time" id="clock2">--:--:--</div>
                            <div class="date" id="date2"><?php echo date('m/d/Y'); ?></div>
                        </div>
                    </div>
                    <div class="balance">
                        <div class="balance-label">Available Balance</div>
                        <div class="balance-amount"><?php echo $primaryAccount ? formatAccountBalance($primaryAccount['balance'] ?? 0, $primaryAccount, $userCurrency) : formatCurrency(0, $userCurrency, $userCurrency); ?></div>
                    </div>
                    <!-- Account Type Selector -->
                    <div class="account-type-selector">
                        <select id="accountType2" class="custom-select" data-custom-select="true" data-label="Select Account" onchange="changeAccount(this.value)">
                            <?php if (!empty($userAccounts)): ?>
                                <?php foreach ($userAccounts as $acc): ?>
                                    <option value="<?php echo $acc['id']; ?>" data-number="<?php echo htmlspecialchars($acc['account_number']); ?>" <?php echo ($acc['id'] == ($primaryAccount['id'] ?? null)) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars(ucfirst($acc['account_type'] ?? 'checking')); ?> - <?php echo htmlspecialchars($acc['account_number']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="">No accounts</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="account-row">
                        <div class="account-box" aria-hidden="true">
                            <div class="acc-left">
                                <div class="shield" title="<?php echo htmlspecialchars($userCountryForFlag); ?>" aria-hidden="true">
                                    <?php if (!empty($userCountryFlagEmoji)): ?>
                                        <span class="flag-emoji"><?php echo $userCountryFlagEmoji; ?></span>
                                    <?php elseif ($userCountryFlagUrl): ?>
                                        <img src="<?php echo htmlspecialchars($userCountryFlagUrl); ?>" alt="<?php echo htmlspecialchars($userCountryForFlag); ?>" style="width: 36px; height: 36px; border-radius: 10px; object-fit: cover; display: block;">
                                    <?php else: ?>
                                        <span class="flag-emoji">🏳️</span>
                                    <?php endif; ?>
                                </div>
                                <div class="acc-details">
                                    <div class="lbl">Your ( <?php echo htmlspecialchars($userCountryDescriptor); ?> ) Account Number</div>
                                    <div class="num" id="accountNumberDisplay2"><?php echo htmlspecialchars($accountNumber); ?></div>
                                </div>
                                <div class="badge" style="margin-left:10px;background:rgba(0,255,128,0.12);color:#07b36a;">
                                    <span style="font-size:10px;">●</span>
                                    Active
                                </div>
                            </div>
                        </div>
                        <div class="actions">
                            <a href="<?php echo SITE_URL; ?>/transaction" class="btn secondary" title="Transactions">
                                Transactions
                            </a>
                            <a href="<?php echo SITE_URL; ?>/transfer" class="btn primary" title="Send Money">
                                Send Money
                            </a>
                        </div>
                    </div>
                </div>
                <!-- Desktop chips -->
                <div class="top-chips" role="region" aria-label="quick-summary" style="margin-top: 25px;">
                    <div class="chip blue">
                        <div class="left">
                            <div>
                                <div class="title">Total Balance</div>
                                <div class="value"><?php echo formatCurrency($currentBalance, $userCurrency, $userCurrency); ?></div>
                            </div>
                        </div>
                        <div class="icon" aria-hidden="true">💰</div>
                    </div>
                    <div class="chip green">
                        <div class="left">
                            <div>
                                <div class="title">Monthly Income</div>
                                <div class="value" style="color:#2d9b4a;"><?php echo formatCurrency($monthlyIncome, $userCurrency, $primaryAccountCurrency); ?></div>
                            </div>
                        </div>
                        <div class="icon" aria-hidden="true">📈</div>
                    </div>
                    <div class="chip pink">
                        <div class="left">
                            <div>
                                <div class="title">Monthly Outgoing</div>
                                <div class="value" style="color:#d44f5a;"><?php echo formatCurrency($monthlyOutgoing, $userCurrency, $primaryAccountCurrency); ?></div>
                            </div>
                        </div>
                        <div class="icon" aria-hidden="true">📉</div>
                    </div>
                    <div class="chip purple">
                        <div class="left">
                            <div>
                                <div class="title">Investment Balance</div>
                                <div class="value" style="color:#7a4be6;"><?php echo formatCurrency($investmentBalance, $userCurrency, $userStoredCurrency); ?></div>
                            </div>
                        </div>
                        <div class="icon" aria-hidden="true">💎</div>
                    </div>
                </div>
            </div>
            <!-- Section below hero: "What would you like to do today?" -->
            <div class="actions-panel">
                <h4>What would you like to do today?</h4>
                <div class="actions-grid">
                    <a href="<?php echo SITE_URL; ?>/transfer" class="action-btn btn-transfer">
                        <div class="icon">💸</div>
                        <div class="text">Transfer</div>
                    </a>
                    <a href="<?php echo SITE_URL; ?>/account" class="action-btn btn-account">
                        <div class="icon">🏦</div>
                        <div class="text">Accounts</div>
                    </a>
                    <a href="<?php echo SITE_URL; ?>/card" class="action-btn btn-card">
                        <div class="icon">💳</div>
                        <div class="text">Cards</div>
                    </a>
                    <a href="<?php echo SITE_URL; ?>/loan" class="action-btn btn-loan">
                        <div class="icon">💰</div>
                        <div class="text">Loans</div>
                    </a>
                    <a href="<?php echo SITE_URL; ?>/investments" class="action-btn btn-investments">
                        <div class="icon">📊</div>
                        <div class="text">Investments</div>
                    </a>
                    <a href="<?php echo SITE_URL; ?>/help-center" class="action-btn btn-support">
                        <div class="icon">💬</div>
                        <div class="text">Support</div>
                    </a>
                </div>
            </div>
            <!-- Transactions Section -->
            <div class="dashboard-card" style="margin-top: 20px;">
                <div class="transactions-header">
                    <h3 class="card-title">Transactions</h3>
                    <a href="<?php echo SITE_URL; ?>/transaction" class="view-all">View all</a>
                </div>
                <div class="transactions-list">
                    <?php 
                    if (!empty($recentTransactions)) {
                        $avatarColors = ['#6C63FF', '#FF6BAA', '#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'];
                        $colorIndex = 0;
                        foreach ($recentTransactions as $transaction) {
                            $amount = $transaction['amount'];
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
                        }
                    } else {
                    ?>
                    <div class="transaction-item">
                        <div class="transaction-top">
                            <div class="transaction-left">
                                <div class="transaction-avatar" style="background:#6C63FF;">NT</div>
                                <div class="transaction-info">
                                    <div class="transaction-title">No transactions yet</div>
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
            </div>
        </div>

        <!-- Right sidebar - INDEPENDENT CARDS (Original Design) -->
        <aside class="right-column" role="complementary">
            <!-- Account Statistics Card -->
            <div class="dashboard-card">
                <h3>Account Statistics</h3>
                <div class="stat">
                    <div class="icon blue">📊</div>
                    <div class="meta">
                        <div class="label">Transaction Limit</div>
                        <div class="value" id="statTransactionLimit"><?php echo formatCurrency($transactionLimit, $userCurrency, DEFAULT_CURRENCY); ?></div>
                    </div>
                </div>
                <div class="stat">
                    <div class="icon yellow">⏳</div>
                    <div class="meta">
                        <div class="label">Pending Transactions</div>
                        <div class="value" id="statPendingTransactions"><?php echo formatCurrency($pendingTransactions, $userCurrency, $primaryAccountCurrency); ?></div>
                    </div>
                </div>
                <div class="stat">
                    <div class="icon green">💵</div>
                    <div class="meta">
                        <div class="label">Transaction Volume</div>
                        <div class="value" id="statTransactionVolume"><?php echo formatCurrency($transactionVolume, $userCurrency, $primaryAccountCurrency); ?></div>
                    </div>
                </div>
                <div class="stat">
                    <div class="icon purple">📈</div>
                    <div class="meta">
                        <div class="label">Active Status</div>
                        <div class="value" style="color: var(--green);">Active</div>
                    </div>
                </div>
            </div>

            <!-- Expenses by Category Card - SEPARATE INDEPENDENT CARD (Original Design) -->
            <div class="dashboard-card">
                <div class="expenses-header">
                    <h3 class="card-title">Expenses by category</h3>
                    <div class="month-filter">
                        <select id="monthSelect" class="custom-select" data-custom-select="true" data-label="Select Month" onchange="loadExpenseData()">
                            <option value="1" <?php echo $currentMonth == 1 ? 'selected' : ''; ?>>January</option>
                            <option value="2" <?php echo $currentMonth == 2 ? 'selected' : ''; ?>>February</option>
                            <option value="3" <?php echo $currentMonth == 3 ? 'selected' : ''; ?>>March</option>
                            <option value="4" <?php echo $currentMonth == 4 ? 'selected' : ''; ?>>April</option>
                            <option value="5" <?php echo $currentMonth == 5 ? 'selected' : ''; ?>>May</option>
                            <option value="6" <?php echo $currentMonth == 6 ? 'selected' : ''; ?>>June</option>
                            <option value="7" <?php echo $currentMonth == 7 ? 'selected' : ''; ?>>July</option>
                            <option value="8" <?php echo $currentMonth == 8 ? 'selected' : ''; ?>>August</option>
                            <option value="9" <?php echo $currentMonth == 9 ? 'selected' : ''; ?>>September</option>
                            <option value="10" <?php echo $currentMonth == 10 ? 'selected' : ''; ?>>October</option>
                            <option value="11" <?php echo $currentMonth == 11 ? 'selected' : ''; ?>>November</option>
                            <option value="12" <?php echo $currentMonth == 12 ? 'selected' : ''; ?>>December</option>
                        </select>
        </div>
                </div>
                <div class="expenses-container">
                    <div class="expenses-chart">
                        <div class="chart-wrapper">
                            <canvas id="expensesChart"></canvas>
                        </div>
                        <div class="chart-center-text">
                            <div class="chart-total" id="expenseTotal"><?php echo formatCurrency($totalExpenses, $userCurrency, $primaryAccountCurrency); ?></div>
                            <div class="chart-label">Total</div>
                        </div>
                    </div>
                    <div class="expenses-legend">
                        <div class="expenses-list" id="expensesList">
                            <?php
                            $categoryColors = ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6'];
                            $colorIndex = 0;
                            if (!empty($expenseCategories)) {
                                foreach ($expenseCategories as $cat) {
                                    $percentage = ($cat['total'] / $totalExpenses) * 100;
                                    $color = $categoryColors[$colorIndex % count($categoryColors)];
                                    $colorIndex++;
                                    ?>
                                    <div class="expense-item">
                                        <div class="expense-info">
                                            <div class="category-dot" style="background: <?php echo $color; ?>;"></div>
                                            <div class="category-name"><?php echo htmlspecialchars(ucfirst($cat['expense_category'])); ?></div>
                                        </div>
                                        <div class="category-percentage"><?php echo number_format($percentage, 1); ?>%</div>
                                    </div>
                                    <?php
                                }
                            } else {
                                ?>
                                <div class="expense-item">
                                    <div class="expense-info">
                                        <div class="category-dot" style="background: #e2e8f0;"></div>
                                        <div class="category-name">No expenses this month</div>
                                    </div>
                                    <div class="category-percentage">0%</div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>

<script>
    // Update clock function
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const timeString = `${hours}:${minutes}:${seconds}`;
        // Format date as MM/DD/YYYY
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const year = now.getFullYear();
        const dateString = `${month}/${day}/${year}`;
        
        const clock1 = document.getElementById('clock');
        const date1 = document.getElementById('date');
        const clock2 = document.getElementById('clock2');
        const date2 = document.getElementById('date2');
        
        if (clock1) clock1.textContent = timeString;
        if (date1) date1.textContent = dateString;
        if (clock2) clock2.textContent = timeString;
        if (date2) date2.textContent = dateString;
    }
    
    // Update account selector - Full dynamic update
    function changeAccount(accountId) {
        console.log('changeAccount called with accountId:', accountId);
        
        // Validate accountId
        if (!accountId || accountId === '' || accountId === '0') {
            console.warn('Invalid accountId:', accountId);
            return;
        }
        
        const select = document.getElementById('accountType') || document.getElementById('accountType2');
        if (!select) {
            console.error('Select element not found');
            return;
        }
        
        // Sync both selectors
        const accountType1 = document.getElementById('accountType');
        const accountType2 = document.getElementById('accountType2');
        if (accountType1 && accountType2) {
            accountType1.value = accountId;
            accountType2.value = accountId;
            console.log('Selectors synced to:', accountId);
        }
        
        // Fetch account data from API
        const apiUrl = `<?php echo SITE_URL; ?>/api/get-account-data.php?account_id=${accountId}`;
        console.log('Fetching account data from:', apiUrl);
        
        fetch(apiUrl)
            .then(response => {
                console.log('API response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text(); // Get raw response first to catch PHP errors
            })
            .then(text => {
                console.log('Raw API response:', text);
                try {
                    const data = JSON.parse(text);
                    console.log('Parsed API response data:', data);
                    
                    if (data.success && data.data) {
                        const acc = data.data;
                        console.log('Updating UI with account data:', acc);
                        
                        // Update account numbers
                        const display1 = document.getElementById('accountNumberDisplay');
                        const display2 = document.getElementById('accountNumberDisplay2');
                        if (display1) {
                            display1.textContent = acc.account_number || 'N/A';
                            console.log('Updated accountNumberDisplay:', acc.account_number);
                        }
                        if (display2) {
                            display2.textContent = acc.account_number || 'N/A';
                            console.log('Updated accountNumberDisplay2:', acc.account_number);
                        }
                        
                        // Update "Available Balance" to show the SELECTED account balance (changes with account selector)
                        const balanceAmounts = document.querySelectorAll('.balance-amount');
                        console.log('Found', balanceAmounts.length, 'balance-amount elements');
                        balanceAmounts.forEach(el => {
                            el.textContent = acc.balance; // Use selected account balance
                            console.log('Updated Available Balance (selected account):', acc.balance);
                        });
                        
                        // Update Current Balance chip to show TOTAL balance of ALL accounts (static - doesn't change)
                        const currentBalanceChips = document.querySelectorAll('.chip.blue .value');
                        console.log('Found', currentBalanceChips.length, 'Current Balance chip elements');
                        currentBalanceChips.forEach(el => {
                            el.textContent = acc.total_balance; // Use total balance of all accounts
                            console.log('Updated Current Balance chip (total of all accounts):', acc.total_balance);
                        });
                        
                        // Update Monthly Income chip (both mobile and desktop)
                        const monthlyIncomeChips = document.querySelectorAll('.chip.green .value');
                        console.log('Found', monthlyIncomeChips.length, 'Monthly Income chip elements');
                        monthlyIncomeChips.forEach(el => {
                            el.textContent = acc.monthly_income;
                            console.log('Updated Monthly Income chip:', acc.monthly_income);
                        });
                        
                        // Update Monthly Outgoing chip (both mobile and desktop)
                        const monthlyOutgoingChips = document.querySelectorAll('.chip.pink .value');
                        console.log('Found', monthlyOutgoingChips.length, 'Monthly Outgoing chip elements');
                        monthlyOutgoingChips.forEach(el => {
                            el.textContent = acc.monthly_outgoing;
                            console.log('Updated Monthly Outgoing chip:', acc.monthly_outgoing);
                        });
                        
                        // Update Account Statistics in right sidebar
                        const transactionLimitEl = document.getElementById('statTransactionLimit');
                        if (transactionLimitEl && acc.transaction_limit) {
                            transactionLimitEl.textContent = acc.transaction_limit;
                            console.log('Updated Transaction Limit:', acc.transaction_limit);
                        }
                        
                        const pendingTransactionsEl = document.getElementById('statPendingTransactions');
                        if (pendingTransactionsEl && acc.pending_transactions) {
                            pendingTransactionsEl.textContent = acc.pending_transactions;
                            console.log('Updated Pending Transactions:', acc.pending_transactions);
                        }
                        
                        const transactionVolumeEl = document.getElementById('statTransactionVolume');
                        if (transactionVolumeEl && acc.transaction_volume) {
                            transactionVolumeEl.textContent = acc.transaction_volume;
                            console.log('Updated Transaction Volume:', acc.transaction_volume);
                        }
                        
                        console.log('Account data update complete');
                    } else {
                        console.error('Failed to load account data:', data.message || 'Unknown error');
                    }
                } catch (parseError) {
                    console.error('JSON parse error:', parseError);
                    console.error('Response text:', text);
                    // Try to extract error message from HTML if it's a PHP error
                    if (text.includes('<b>') || text.includes('Fatal error') || text.includes('Warning')) {
                        console.error('PHP error detected in response. Response starts with:', text.substring(0, 200));
                    }
                    // Note: showToast might not be available, so we just log to console
                    if (typeof showToast === 'function') {
                        showToast('Error parsing server response. Please check console for details.', 'error');
                    }
                }
            })
            .catch(error => {
                console.error('Error loading account data:', error);
                // Fallback: update account number from select option data attribute
        const selectedOption = select.options[select.selectedIndex];
        const accountNumber = selectedOption ? selectedOption.getAttribute('data-number') : '';
        const display1 = document.getElementById('accountNumberDisplay');
        const display2 = document.getElementById('accountNumberDisplay2');
        if (display1) display1.textContent = accountNumber || 'N/A';
        if (display2) display2.textContent = accountNumber || 'N/A';
                // Note: Balance cannot be updated without API response - user will need to refresh
                console.warn('Balance update failed - API error. User may need to refresh page.');
            });
    }
    
    // Initialize expense chart
    let expensesChart;
    
    // Load expense data dynamically - MUST be in global scope for inline onchange
    window.loadExpenseData = function() {
        const monthSelect = document.getElementById('monthSelect');
        if (!monthSelect) return;
        
        const month = monthSelect.value;
        
        // Show loading state
        monthSelect.disabled = true;
        const expenseTotal = document.getElementById('expenseTotal');
        if (expenseTotal) {
            expenseTotal.textContent = 'Loading...';
        }
        
        fetch(`<?php echo SITE_URL; ?>/api/get-expense-data.php?month=${month}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('API Response:', data);
                if (data.success) {
                    updateExpenseChart(data.data || [], data.total || 0);
                } else {
                    console.error('Failed to load expense data:', data.message);
                    if (expenseTotal) {
                        expenseTotal.textContent = 'Error';
                    }
                }
                // Re-enable select
                monthSelect.disabled = false;
            })
            .catch(error => {
                console.error('Error loading expense data:', error);
                if (expenseTotal) {
                    expenseTotal.textContent = 'Error';
                }
                // Re-enable select
                monthSelect.disabled = false;
            });
    };
    
    // Update expense chart with new data - MUST be in global scope
    window.updateExpenseChart = function(expenseData, total) {
        console.log('Updating expense chart with data:', expenseData, 'Total:', total);
        
        // Get user currency from the page (stored in PHP variable)
        const userCurrency = '<?php echo strtoupper($userCurrency); ?>';
        const currencySymbol = userCurrency === 'USD' ? '$' : (userCurrency === 'EUR' ? '€' : (userCurrency === 'GBP' ? '£' : '$'));
        
        // Update total with proper currency formatting (ensure total is a number)
        const totalAmount = parseFloat(total) || 0;
        const formattedTotal = currencySymbol + totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        const expenseTotalEl = document.getElementById('expenseTotal');
        if (expenseTotalEl) {
            expenseTotalEl.textContent = formattedTotal;
        }
        
        // Update legend
        const expensesList = document.getElementById('expensesList');
        if (!expensesList) return;
        expensesList.innerHTML = '';
        
        if (expenseData.length === 0 || total === 0) {
            expensesList.innerHTML = `
                <div class="expense-item">
                    <div class="expense-info">
                        <div class="category-dot" style="background: #e2e8f0;"></div>
                        <div class="category-name">No expenses this month</div>
                    </div>
                    <div class="category-percentage">0%</div>
                </div>
            `;
        } else {
            expenseData.forEach((expense, index) => {
                const expenseItem = document.createElement('div');
                expenseItem.className = 'expense-item';
                const categoryName = expense.category ? (expense.category.charAt(0).toUpperCase() + expense.category.slice(1).toLowerCase()) : 'Other';
                expenseItem.innerHTML = `
                    <div class="expense-info">
                        <div class="category-dot" style="background: ${expense.color || '#4f46e5'};"></div>
                        <div class="category-name">${categoryName}</div>
                    </div>
                    <div class="category-percentage">${expense.percentage || 0}%</div>
                `;
                expensesList.appendChild(expenseItem);
            });
        }
        
        // Update chart
        const canvas = document.getElementById('expensesChart');
        if (!canvas) {
            console.error('Chart canvas not found');
            return;
        }
        
        // Destroy existing chart if it exists
        if (expensesChart) {
            try {
                expensesChart.destroy();
            } catch (e) {
                console.warn('Error destroying chart:', e);
            }
            expensesChart = null;
        }
        
        // Prepare chart data
        let chartData, chartLabels, chartColors;
        
        if (expenseData.length === 0 || total === 0) {
            // Show empty state - gray circle
            chartData = [100];
            chartLabels = ['No expenses'];
            chartColors = ['#e2e8f0'];
        } else {
            chartData = expenseData.map(e => parseFloat(e.amount) || 0);
            chartLabels = expenseData.map(e => {
                const cat = e.category || 'Other';
                return cat.charAt(0).toUpperCase() + cat.slice(1).toLowerCase();
            });
            chartColors = expenseData.map(e => e.color || '#4f46e5');
        }
        
        // Small delay to ensure chart is fully destroyed
        setTimeout(() => {
            // Get context and create new chart
            const ctx = canvas.getContext('2d');
            if (!ctx) {
                console.error('Could not get canvas context');
                return;
            }
            
            const ChartConstructor = Chart.Chart || Chart;
            
            expensesChart = new ChartConstructor(ctx, {
                type: 'doughnut',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        data: chartData,
                        backgroundColor: chartColors,
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: expenseData.length > 0 && total > 0,
                            callbacks: {
                                label: function(context) {
                                    if (expenseData.length === 0 || total === 0) {
                                        return 'No expenses this month';
                                    }
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const datasetTotal = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = datasetTotal > 0 ? ((value / datasetTotal) * 100).toFixed(1) : 0;
                                    const currencySymbol = userCurrency === 'USD' ? '$' : (userCurrency === 'EUR' ? '€' : (userCurrency === 'GBP' ? '£' : '$'));
                                    return label + ': ' + currencySymbol + value.toFixed(2) + ' (' + percentage + '%)';
                                }
                            }
                        }
                    },
                    elements: {
                        arc: {
                            borderWidth: 0
                        }
                    }
                }
            });
            
            console.log('Chart updated successfully with', expenseData.length, 'categories, Total:', total);
        }, 50); // Small delay to ensure previous chart is destroyed
    };
    
    function initializeExpensesChart() {
        // Check if Chart.js is loaded
        if (typeof Chart === 'undefined') {
            console.error('Chart.js is not loaded. Please check if chart.umd.min.js is loaded correctly.');
            return;
        }
        
        const ctx = document.getElementById('expensesChart');
        if (!ctx) return;
        
        const chartData = <?php echo json_encode($expenseChartData); ?>;
        const chartLabels = <?php echo json_encode($expenseChartLabels); ?>;
        const colors = <?php echo json_encode($expenseChartColors); ?>;
        
        // Filter out zero values for display
        const filteredData = [];
        const filteredLabels = [];
        const filteredColors = [];
        chartData.forEach((value, index) => {
            if (value > 0) {
                filteredData.push(value);
                filteredLabels.push(chartLabels[index] || 'Other');
                filteredColors.push(colors[index % colors.length] || '#4f46e5');
            }
        });
        
        // Use filtered data or default to empty
        const finalData = filteredData.length > 0 ? filteredData : [1];
        const finalLabels = filteredData.length > 0 ? filteredLabels : ['No expenses'];
        const finalColors = filteredData.length > 0 ? filteredColors : ['#e2e8f0'];
        
        // Use Chart.Chart for UMD format, fallback to Chart
        const ChartConstructor = Chart.Chart || Chart;
        expensesChart = new ChartConstructor(ctx, {
            type: 'doughnut',
            data: {
                labels: finalLabels,
                datasets: [{
                    data: finalData,
                    backgroundColor: finalColors,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return label + ': $' + value.toFixed(2) + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
        
        // Update total in center if needed
        const total = filteredData.length > 0 ? filteredData.reduce((a, b) => a + b, 0) : <?php echo $totalExpenses; ?>;
        const totalElement = document.getElementById('expenseTotal');
        if (totalElement) {
            const currencySymbol = '<?php echo $userCurrency === "USD" ? "$" : ($userCurrency === "EUR" ? "€" : ($userCurrency === "GBP" ? "£" : "")); ?>';
            totalElement.textContent = currencySymbol + total.toFixed(2);
        }
    }
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateClock();
        setInterval(updateClock, 1000);
        
        // Sync account selectors
        const accountType1 = document.getElementById('accountType');
        const accountType2 = document.getElementById('accountType2');
        if (accountType1 && accountType2) {
            accountType1.addEventListener('change', function() {
                changeAccount(this.value);
            });
            accountType2.addEventListener('change', function() {
                changeAccount(this.value);
            });
        }
        
        // Wait for Chart.js to be fully loaded before initializing
        if (typeof Chart !== 'undefined') {
            // Initialize expense chart immediately
            initializeExpensesChart();
        } else {
            // Wait a bit longer for Chart.js to load
            setTimeout(function() {
                if (typeof Chart !== 'undefined') {
                    initializeExpensesChart();
                } else {
                    console.error('Chart.js failed to load. Chart functionality will be unavailable.');
                }
            }, 500);
        }
        
        // Month selector change is handled by onchange="loadExpenseData()" in the HTML
        // No need for additional event listener that reloads the page
    });
</script>

<!-- Old sections (kept for reference, hidden) -->
<div style="display:none;">
        <!-- My Account Section -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">My Account</h3>
                <select id="accountSelect" style="background: #f8f9fa; border: none; padding: 8px 12px; border-radius: 6px; color: #666; cursor: pointer;">
                    <?php 
                    if (!empty($userAccounts)) {
                        foreach ($userAccounts as $index => $account) {
                            $accountTypeName = ucfirst($account['account_type']) . ' Account';
                            $selected = ($index === 0) ? 'selected' : '';
                            $accountNumber = htmlspecialchars($account['account_number']);
                            echo '<option value="' . htmlspecialchars($account['id']) . '" data-type="' . htmlspecialchars($account['account_type']) . '" data-number="' . $accountNumber . '" ' . $selected . '>' . htmlspecialchars($accountTypeName) . '</option>';
                        }
                    } else {
                        echo '<option value="0" data-number="N/A">No Account</option>';
                    }
                    ?>
                </select>
            </div>
            
            <div class="credit-card-container">
                <?php 
                // Determine the card class based on first account type
                $firstAccount = !empty($userAccounts) ? $userAccounts[0] : null;
                $cardClass = 'card-checking';
                if ($firstAccount) {
                    if ($firstAccount['account_type'] === 'savings') {
                        $cardClass = 'card-savings';
                    } elseif ($firstAccount['account_type'] === 'business') {
                        $cardClass = 'card-business';
                    }
                }
                ?>
                <div class="credit-card initial-spin <?php echo $cardClass; ?>" id="accountCard">
                    <!-- Front Side -->
                    <div class="card-face front-face">
                        <div class="card-top-section">
                            <div class="account-info">
                                <div class="account-balance">
                                    <div class="balance-label">Account Balance</div>
                                    <div class="balance-amount" id="accountBalance">
                                        <?php echo $firstAccount ? formatAccountBalance($firstAccount['balance'], $firstAccount, $userCurrency) : formatCurrency(0, $userCurrency, $userCurrency); ?>
                                    </div>
                                </div>
                                <div class="account-number" id="accountNumber">
                                    <?php echo $firstAccount ? 'ACC: ' . htmlspecialchars($firstAccount['account_number']) : 'ACC: N/A'; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-info-section">
                            <div class="current-time">
                                <div class="time-date" id="currentDate"><?php echo date('m/d/Y'); ?></div>
                                <div class="time-clock" id="currentTime"><?php echo date('H:i:s'); ?></div>
                            </div>
                            <div class="country-flag" title="<?php echo htmlspecialchars($userCountryForFlag); ?>">
                                <?php echo !empty($userCountryFlagEmoji) ? $userCountryFlagEmoji : '🏳️'; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Back Side - Clean and Professional -->
                    <div class="card-face back-face">
                        <div class="back-content">
                            <div class="customer-service">Customer Service: 1-800-OCTOBANK</div>
                            
                            <div class="magnetic-strip"></div>
                            
                            <div class="signature-area">
                                <div class="signature-strip">
                                    <span class="signature-label">Authorized Signature</span>
                                </div>
                                <div class="cvv-strip">
                                    <div class="cvv-label">CVV</div>
                                    <div class="cvv-value">***</div>
                                </div>
                            </div>
                            
                            <div class="customer-info-section">
                                <div class="customer-name"><?php echo htmlspecialchars($userDisplayName); ?></div>
                                <div class="customer-contact">
                                <div class="contact-item"><?php echo htmlspecialchars($user['email'] ?? ''); ?></div>
                                <div class="contact-item"><?php echo htmlspecialchars($user['phone'] ?? ''); ?></div>
                                <div class="contact-item"><?php echo htmlspecialchars($user['address'] ?? ''); ?></div>
                                </div>
                            </div>
                            
                            <div class="legal-text">This card is property of Octobank. If found, please return to any branch.</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-balance-info">
                <div class="balance-row">
                    <span>Credit limit</span>
                    <strong id="creditLimit">
                        <?php echo $primaryAccount ? formatAccountBalance(($primaryAccount['balance'] ?? 0) * 2, $primaryAccount, $userCurrency) : formatCurrency(0, $userCurrency, $userCurrency); ?>
                    </strong>
                </div>
                <div class="balance-row">
                    <span>Daily limit</span>
                    <strong id="dailyLimit">
                        <?php 
                        if ($primaryAccount) {
                            require_once __DIR__ . '/../../includes/functions.php';
                            $dailyLimit = getDailyLimitForAccountType($primaryAccount['account_type']);
                            echo formatCurrency($dailyLimit, $userCurrency, DEFAULT_CURRENCY);
                        } else {
                            echo formatDisplayCurrencyAmount(0, $userCurrency);
                        }
                        ?>
                    </strong>
                </div>
            </div>
        </div>

        <!-- Information Card -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3 class="card-title">Information</h3>
                <a href="<?php echo SITE_URL; ?>/profile" style="color: #667eea; text-decoration: none; font-size: 14px; font-weight: 500;">More details ></a>
            </div>
            <div class="info-list">
                <div class="info-item">
                    <span>Status:</span>
                    <div class="status-active">
                        <span class="status-dot"></span>
                        <?php echo ucfirst($user['status'] ?? 'active'); ?>
                    </div>
                </div>
                <div class="info-item">
                    <span>Account type:</span>
                    <span id="accountType"><?php echo $primaryAccount ? ucfirst($primaryAccount['account_type']) : 'N/A'; ?></span>
                </div>
                <div class="info-item">
                    <span>Currency:</span>
                    <span><?php echo strtoupper($userCurrency); ?></span>
                </div>
            </div>
        </div>

        <!-- New Transaction Button -->
        <button class="new-transaction-btn" onclick="window.location.href='<?php echo SITE_URL; ?>/transfer'">
            <i class="fas fa-plus"></i>
            New transaction
        </button>
</div>
<!-- Close grid -->
</div>

<!-- Old sections hidden (backward compatibility) -->
<div style="display:none;">
<?php
// Initialize accountsDataJS for old hidden section
$accountsDataJS = [];
if (!empty($userAccounts)) {
    foreach ($userAccounts as $account) {
        $accountId = $account['id'];
        
        // Format balance
        $displayBalance = $account['balance'];
        $balance = formatAccountBalance($displayBalance, $account, $userCurrency);
        $number = 'ACC: ' . htmlspecialchars($account['account_number']);
        $creditLimit = formatAccountBalance($displayBalance * 2, $account, $userCurrency);
        // Get daily limit from system settings based on account type
        require_once __DIR__ . '/../../includes/functions.php';
        $dailyLimitValue = getDailyLimitForAccountType($account['account_type']);
        $dailyLimit = formatCurrency($dailyLimitValue, $userCurrency, DEFAULT_CURRENCY);
        $accountType = ucfirst($account['account_type']);
        
        $colorClass = 'card-checking';
        if ($account['account_type'] === 'savings') {
            $colorClass = 'card-savings';
        } elseif ($account['account_type'] === 'business') {
            $colorClass = 'card-business';
        }
        
        $accountsDataJS[$accountId] = [
            'balance' => $balance,
            'number' => $number,
            'creditLimit' => $creditLimit,
            'dailyLimit' => $dailyLimit,
            'accountType' => $accountType,
            'colorClass' => $colorClass
        ];
    }
} else {
    $accountsDataJS['0'] = [
        'balance' => formatDisplayCurrencyAmount(0, $userCurrency),
        'number' => 'ACC: N/A',
        'creditLimit' => formatDisplayCurrencyAmount(0, $userCurrency),
        'dailyLimit' => formatDisplayCurrencyAmount(0, $userCurrency),
        'accountType' => 'N/A',
        'colorClass' => 'card-checking'
    ];
}
?>
    const accountData = <?php echo json_encode($accountsDataJS); ?>;

    // Credit card functionality
    document.addEventListener("DOMContentLoaded", function() {
        const card = document.querySelector(".credit-card");
        const accountSelect = document.getElementById('accountSelect');
        
        // Remove initial spin animation after it completes
        setTimeout(() => {
            if(card) {
                card.classList.remove('initial-spin');
                
                // Add click flip functionality after initial animation
                card.addEventListener("click", function() {
                    this.classList.toggle("is-flipped");
                });
            }
        }, 1200);

        // Account selection handler
        accountSelect.addEventListener('change', function() {
            const selectedAccountId = this.value;
            updateAccountDisplay(selectedAccountId);
        });

        // Update real-time clock for credit card section
        function updateCardClock() {
            const now = new Date();
            const dateElement = document.getElementById('currentDate');
            const timeElement = document.getElementById('currentTime');
            
            if(dateElement && timeElement) {
                // Format date as MM/DD/YYYY for consistency
                const month = String(now.getMonth() + 1).padStart(2, '0');
                const day = String(now.getDate()).padStart(2, '0');
                const year = now.getFullYear();
                dateElement.textContent = `${month}/${day}/${year}`;
                
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                timeElement.textContent = `${hours}:${minutes}:${seconds}`;
            }
        }

        // Update account display
        function updateAccountDisplay(accountId) {
            const data = accountData[accountId];
            if (!data) return;
            
            const card = document.getElementById('accountCard');
            
            // Remove all color classes
            card.classList.remove('card-checking', 'card-savings', 'card-business');
            // Add new color class
            card.classList.add(data.colorClass);
            
            // Update card content
            document.getElementById('accountBalance').textContent = data.balance;
            document.getElementById('accountNumber').textContent = data.number;
            document.getElementById('creditLimit').textContent = data.creditLimit;
            document.getElementById('dailyLimit').textContent = data.dailyLimit;
            document.getElementById('accountType').textContent = data.accountType;
        }

        // Update card clock immediately and then every second
        updateCardClock();
        setInterval(updateCardClock, 1000);

        // Initialize charts
        initializeExpensesChart();
        initializeAnalyticsChart('week');
    });

    function initializeAnalyticsChart(type) {
        // Load real analytics data
        loadAnalyticsData(type);
    }

    function loadAnalyticsData(type) {
        fetch(`<?php echo SITE_URL; ?>/api/get-analytics-data.php?type=${type}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateAnalyticsChart(data, type);
                    updateAnalyticsStats(data);
                } else {
                    console.error('Failed to load analytics data:', data.message);
                    // Fallback to empty chart
                    updateAnalyticsChart({
                        labels: [],
                        income_data: [],
                        expense_data: []
                    }, type);
                }
            })
            .catch(error => {
                console.error('Error loading analytics data:', error);
                // Fallback to empty chart
                updateAnalyticsChart({
                    labels: [],
                    income_data: [],
                    expense_data: []
                }, type);
            });
    }

    function updateAnalyticsChart(data, type) {
        const ctx = document.getElementById('analyticsChart').getContext('2d');
        
        if (analyticsChart) {
            analyticsChart.destroy();
        }

        // Create combined data for balance trend
        const balanceData = [];
        let runningBalance = 0;
        
        for (let i = 0; i < data.labels.length; i++) {
            runningBalance += (data.income_data[i] || 0) - (data.expense_data[i] || 0);
            balanceData.push(runningBalance);
        }

        const ChartConstructor = Chart.Chart || Chart;
        analyticsChart = new ChartConstructor(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Balance Trend',
                    data: balanceData,
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#667eea',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
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
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleFont: { size: 14 },
                        bodyFont: { size: 13 },
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                const index = context.dataIndex;
                                const income = data.income_data[index] || 0;
                                const expense = data.expense_data[index] || 0;
                                const balance = balanceData[index];
                                return [
                                    `Balance: $${balance.toLocaleString()}`,
                                    `Income: $${income.toLocaleString()}`,
                                    `Expense: $${expense.toLocaleString()}`
                                ];
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        },
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    }

    function updateAnalyticsStats(data) {
        document.getElementById('totalIncome').textContent = '$' + data.total_income.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        document.getElementById('totalExpense').textContent = '$' + data.total_expense.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        document.getElementById('netProfit').textContent = '$' + data.net_profit.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function changeChartType(type) {
        // Update active button
        document.querySelectorAll('.chart-controls button').forEach(btn => {
            btn.classList.remove('active');
        });
        event.target.classList.add('active');
        
        // Update chart
        initializeAnalyticsChart(type);
    }

    // Handle window resize for better mobile experience
    window.addEventListener('resize', function() {
        if (expensesChart) {
            expensesChart.resize();
        }
        if (analyticsChart) {
            analyticsChart.resize();
        }
    });
    
    // Check if user needs onboarding (new users who haven't completed setup)
    <?php
    // Check if user needs onboarding (already set above, but ensure it exists)
    if (!isset($currentUser)) {
        $currentUser = $user ?? [];
    }
    if (!isset($needsOnboarding)) {
        $needsOnboarding = isset($currentUser['onboarding_completed']) && !$currentUser['onboarding_completed'] ? true : false;
    }
    
    // Get userId from session if not available
    if (!isset($userId)) {
        $userId = $_SESSION['user_id'] ?? null;
    }
    
    if ($needsOnboarding) {
        echo "const needsOnboarding = true;";
        echo "const hasTransferPin = " . (empty($currentUser['transfer_pin']) ? 'false' : 'true') . ";";
        echo "const hasSecurityPin = " . (empty($currentUser['security_pin']) ? 'false' : 'true') . ";";
        echo "const kycStatus = '" . ($currentUser['kyc_status'] ?? 'pending') . "';";
    } else {
        echo "const needsOnboarding = false;";
    }
    ?>
    
    // Show onboarding modal if needed
    if (needsOnboarding) {
        setTimeout(function() {
            showOnboardingModal();
        }, 1000); // Show after 1 second delay
    }
    
    // Check if currency popup should be shown
    <?php if ($showCurrencyPopup && $detectedCurrency): ?>
    const showCurrencyPopup = true;
    const detectedCurrency = '<?php echo strtoupper($detectedCurrency); ?>';
    <?php else: ?>
    const showCurrencyPopup = false;
    <?php endif; ?>
    
    // Show currency selection modal if needed (after onboarding or alone)
    if (showCurrencyPopup) {
        setTimeout(function() {
            showCurrencySelectionModal();
        }, needsOnboarding ? 2500 : 1000); // Show after onboarding modal (2.5s) or alone (1s)
    }
    
    function showOnboardingModal() {
        document.getElementById('onboardingModal').style.display = 'flex';
    }
    
    function hideOnboardingModal() {
        document.getElementById('onboardingModal').style.display = 'none';
    }
    
    function completeOnboarding() {
        // Mark onboarding as completed
        fetch('<?php echo SITE_URL; ?>/api/complete-onboarding.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                hideOnboardingModal();
            }
        })
        .catch(error => console.error('Error:', error));
    }
    
    function showCurrencySelectionModal() {
        document.getElementById('currencySelectionModal').style.display = 'flex';
    }
    
    function hideCurrencySelectionModal() {
        document.getElementById('currencySelectionModal').style.display = 'none';
    }
    
    function selectCurrency(useDetected) {
        const currency = useDetected ? detectedCurrency : '<?php echo DEFAULT_CURRENCY; ?>';
        
        fetch('<?php echo SITE_URL; ?>/api/select-currency.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                currency: currency,
                markAsShown: true
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                hideCurrencySelectionModal();
                // Reload page to update currency display
                if (useDetected) {
                    window.location.reload();
                }
            } else {
                alert('Failed to update currency: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating currency. Please try again later.');
        });
    }

    <?php if (!empty($showKycPrompt)): ?>
    function showKycPromptModal() {
        var modal = document.getElementById('kycPromptModal');
        if (modal) modal.style.display = 'flex';
    }
    function hideKycPromptModal() {
        var modal = document.getElementById('kycPromptModal');
        if (modal) modal.style.display = 'none';
    }
    function dismissKycPrompt() {
        fetch('<?php echo SITE_URL; ?>/api/kyc-prompt-action.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'dismiss' })
        }).finally(function() {
            hideKycPromptModal();
        });
    }
    setTimeout(function() {
        showKycPromptModal();
    }, needsOnboarding ? 3000 : 1500);
    <?php endif; ?>
</script>

<!-- Onboarding Modal -->
<div id="onboardingModal" class="onboarding-modal-overlay" style="display: none;">
    <div class="onboarding-modal">
        <div class="onboarding-header">
            <div class="onboarding-icon">
                <i class="fas fa-rocket" style="font-size: 48px; color: #1e3a8a;"></i>
            </div>
            <h2>Welcome to <?php echo getSiteName(); ?>!</h2>
            <p>Let's get your account set up for a secure banking experience</p>
        </div>
        
        <div class="onboarding-content">
            <div class="onboarding-checklist">
                <?php if (isset($needsOnboarding) && $needsOnboarding): ?>
                    
                    <!-- KYC Verification -->
                    <div class="checklist-item <?php echo ($currentUser['kyc_status'] === 'verified') ? 'completed' : ''; ?>">
                        <div class="checklist-icon">
                            <?php if ($currentUser['kyc_status'] === 'verified'): ?>
                                <i class="fas fa-check-circle" style="color: #4caf50;"></i>
                            <?php else: ?>
                                <i class="fas fa-circle" style="color: #ddd;"></i>
                            <?php endif; ?>
                        </div>
                        <div class="checklist-details">
                            <h4>Submit KYC Verification</h4>
                            <p>Verify your identity to unlock all features</p>
                            <?php if ($currentUser['kyc_status'] !== 'verified'): ?>
                                <a href="<?php echo SITE_URL; ?>/profile#kyc" class="checklist-action">Submit Documents</a>
                            <?php else: ?>
                                <span class="status-badge success">Verified</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Transfer PIN -->
                    <div class="checklist-item <?php echo (!empty($currentUser['transfer_pin'])) ? 'completed' : ''; ?>">
                        <div class="checklist-icon">
                            <?php if (!empty($currentUser['transfer_pin'])): ?>
                                <i class="fas fa-check-circle" style="color: #4caf50;"></i>
                            <?php else: ?>
                                <i class="fas fa-circle" style="color: #ddd;"></i>
                            <?php endif; ?>
                        </div>
                        <div class="checklist-details">
                            <h4>Set Transfer PIN</h4>
                            <p>Secure your transfers with a 4-digit PIN</p>
                            <?php if (empty($currentUser['transfer_pin'])): ?>
                                <a href="<?php echo SITE_URL; ?>/profile#security" class="checklist-action">Set PIN</a>
                            <?php else: ?>
                                <span class="status-badge success">Configured</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Security PIN -->
                    <div class="checklist-item <?php echo (!empty($currentUser['security_pin'])) ? 'completed' : ''; ?>">
                        <div class="checklist-icon">
                            <?php if (!empty($currentUser['security_pin'])): ?>
                                <i class="fas fa-check-circle" style="color: #4caf50;"></i>
                            <?php else: ?>
                                <i class="fas fa-circle" style="color: #ddd;"></i>
                            <?php endif; ?>
                        </div>
                        <div class="checklist-details">
                            <h4>Set Security PIN</h4>
                            <p>Add an extra layer of account security</p>
                            <?php if (empty($currentUser['security_pin'])): ?>
                                <a href="<?php echo SITE_URL; ?>/profile#security" class="checklist-action">Set PIN</a>
                            <?php else: ?>
                                <span class="status-badge success">Configured</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                <?php endif; ?>
            </div>
        </div>
        
        <div class="onboarding-footer">
            <button class="btn-secondary" onclick="hideOnboardingModal()">I'll Do This Later</button>
            <button class="btn-primary" onclick="completeOnboarding()">
                <i class="fas fa-check"></i> Mark as Complete
            </button>
        </div>
    </div>
</div>
</div>

<!-- KYC Prompt Modal -->
<?php if (!empty($showKycPrompt)): ?>
<div id="kycPromptModal" class="onboarding-modal-overlay" style="display: none;">
    <div class="onboarding-modal" style="max-width: 480px;">
        <div class="onboarding-modal-header">
            <div class="onboarding-icon" style="background: linear-gradient(135deg, #1e3a8a, #3b82f6);">
                <i class="fas fa-id-card" style="font-size: 36px; color: white;"></i>
            </div>
            <h2>Complete Your Identity Verification</h2>
            <p>KYC verification is required to unlock transfers and full account features.</p>
        </div>
        <div class="onboarding-modal-actions" style="flex-direction: column; gap: 10px;">
            <a href="<?php echo SITE_URL; ?>/profile/kyc" class="btn-primary" style="text-align:center;text-decoration:none;">
                Set up KYC
            </a>
            <button type="button" class="btn-secondary" onclick="hideKycPromptModal()" style="width:100%;">
                Remind me later
            </button>
            <button type="button" onclick="dismissKycPrompt()" style="background:none;border:none;color:#64748b;font-size:13px;cursor:pointer;padding:8px;">
                Don't show again
            </button>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Currency Selection Modal -->
<div id="currencySelectionModal" class="currency-modal-overlay" style="display: none;">
    <div class="currency-modal">
        <div class="currency-modal-header">
            <div class="currency-icon">
                <i class="fas fa-globe" style="font-size: 48px; color: #1e3a8a;"></i>
            </div>
            <h2>Welcome to <?php echo getSiteName(); ?>!</h2>
            <p>We've detected your location. Would you like to use your local currency?</p>
        </div>
        
        <div class="currency-modal-content">
            <div class="currency-info">
                <div class="currency-option">
                    <div class="currency-option-header">
                        <i class="fas fa-map-marker-alt" style="color: #10b981; margin-right: 8px;"></i>
                        <strong>Detected Currency</strong>
                    </div>
                    <div class="currency-option-value" id="detectedCurrencyDisplay">
                        <?php 
                        if (isset($detectedCurrency) && $detectedCurrency) {
                            require_once __DIR__ . '/../../includes/functions.php';
                            $currencyNames = [
                                'USD' => 'US Dollar', 'EUR' => 'Euro', 'GBP' => 'British Pound',
                                'NGN' => 'Nigerian Naira', 'KES' => 'Kenyan Shilling', 'ZAR' => 'South African Rand',
                                'GHS' => 'Ghanaian Cedi', 'INR' => 'Indian Rupee', 'PKR' => 'Pakistani Rupee',
                                'CAD' => 'Canadian Dollar', 'AUD' => 'Australian Dollar', 'CNY' => 'Chinese Yuan',
                                'JPY' => 'Japanese Yen', 'KRW' => 'South Korean Won', 'SGD' => 'Singapore Dollar',
                                'MYR' => 'Malaysian Ringgit', 'THB' => 'Thai Baht', 'IDR' => 'Indonesian Rupiah',
                                'PHP' => 'Philippine Peso', 'VND' => 'Vietnamese Dong', 'AED' => 'UAE Dirham',
                                'SAR' => 'Saudi Riyal', 'BRL' => 'Brazilian Real', 'MXN' => 'Mexican Peso',
                                'TRY' => 'Turkish Lira', 'ILS' => 'Israeli Shekel', 'NZD' => 'New Zealand Dollar',
                                'HKD' => 'Hong Kong Dollar', 'CHF' => 'Swiss Franc', 'SEK' => 'Swedish Krona',
                                'NOK' => 'Norwegian Krone', 'DKK' => 'Danish Krone'
                            ];
                            $detectedName = $currencyNames[$detectedCurrency] ?? $detectedCurrency;
                            echo '<span class="currency-code">' . strtoupper($detectedCurrency) . '</span> - ' . $detectedName;
                        } else {
                            echo 'Currency not detected';
                        }
                        ?>
                    </div>
                </div>
                
                <div class="currency-divider">
                    <span>OR</span>
                </div>
                
                <div class="currency-option">
                    <div class="currency-option-header">
                        <i class="fas fa-dollar-sign" style="color: #667eea; margin-right: 8px;"></i>
                        <strong>Site Default</strong>
                    </div>
                    <div class="currency-option-value">
                        <?php 
                        if (!isset($currencyNames)) {
                            $currencyNames = [
                                'USD' => 'US Dollar', 'EUR' => 'Euro', 'GBP' => 'British Pound',
                                'NGN' => 'Nigerian Naira', 'KES' => 'Kenyan Shilling', 'ZAR' => 'South African Rand',
                                'GHS' => 'Ghanaian Cedi', 'INR' => 'Indian Rupee', 'PKR' => 'Pakistani Rupee',
                                'CAD' => 'Canadian Dollar', 'AUD' => 'Australian Dollar', 'CNY' => 'Chinese Yuan',
                                'JPY' => 'Japanese Yen', 'KRW' => 'South Korean Won', 'SGD' => 'Singapore Dollar',
                                'MYR' => 'Malaysian Ringgit', 'THB' => 'Thai Baht', 'IDR' => 'Indonesian Rupiah',
                                'PHP' => 'Philippine Peso', 'VND' => 'Vietnamese Dong', 'AED' => 'UAE Dirham',
                                'SAR' => 'Saudi Riyal', 'BRL' => 'Brazilian Real', 'MXN' => 'Mexican Peso',
                                'TRY' => 'Turkish Lira', 'ILS' => 'Israeli Shekel', 'NZD' => 'New Zealand Dollar',
                                'HKD' => 'Hong Kong Dollar', 'CHF' => 'Swiss Franc', 'SEK' => 'Swedish Krona',
                                'NOK' => 'Norwegian Krone', 'DKK' => 'Danish Krone'
                            ];
                        }
                        $defaultName = $currencyNames[DEFAULT_CURRENCY] ?? DEFAULT_CURRENCY;
                        echo '<span class="currency-code">' . strtoupper(DEFAULT_CURRENCY) . '</span> - ' . $defaultName;
                        ?>
                    </div>
                </div>
            </div>
            
            <p class="currency-note">
                <i class="fas fa-info-circle"></i> 
                You can always change your currency preference later in Settings.
            </p>
        </div>
        
        <div class="currency-modal-footer">
            <button class="btn-currency-secondary" onclick="selectCurrency(false)">
                No, Keep Default
            </button>
            <button class="btn-currency-primary" onclick="selectCurrency(true)">
                Yes, Switch to <?php echo isset($detectedCurrency) && $detectedCurrency ? strtoupper($detectedCurrency) : ''; ?>
            </button>
        </div>
    </div>
</div>

<?php
// Include live chat on customer dashboard only (includes/livechat.php handles gating)
include __DIR__ . '/../../includes/livechat.php';

// Include mobile navigation and closing tags
include __DIR__ . '/../../includes/mobile-nav.php';
?>

</body>
</html>