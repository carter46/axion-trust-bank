<?php 
$pageTitle = 'My Cards - SecureBank';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

// Get dynamic site name for branding
$siteName = getSiteName() ?? 'SecureBank';
$siteNameShort = getSiteInitials() ?? 'CTB';

// Include head
include __DIR__ . '/../../includes/head.php';

// Include sidebar
include __DIR__ . '/../../includes/sidebar.php';

// Get user currency
$userModel = new User();
$user = $userModel->findById($_SESSION['user_id']);
$userCurrency = getUserDisplayCurrency($user);

// Get user's cards from database
$cardModel = new Card();
$userCards = $cardModel->getUserCards($_SESSION['user_id']);
$pendingCards = $cardModel->getUserPendingCards($_SESSION['user_id']);

// Get card transactions (for the first card or selected card)
$cardTransactions = [];
if (!empty($userCards)) {
    // Get transactions for all cards
    foreach ($userCards as $card) {
        $transactions = $cardModel->getCardTransactions($card['id'], 50);
        $cardTransactions = array_merge($cardTransactions, $transactions);
    }
    // Sort by date descending
    usort($cardTransactions, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
    // Limit to 10 most recent
    $cardTransactions = array_slice($cardTransactions, 0, 10);
}
?>

<?php include __DIR__ . '/../../includes/restricted-banner.php'; ?>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /* Override parent content-area styles */
    .main-content-area .content-area {
        background: #f5f7fa !important;
        padding: 15px !important;
    }

    .cards-container {
        max-width: 100%;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 25px;
    }

    /* ===== PAGE HEADER STANDARD (Same as Dashboard) ===== */
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

    /* Header with button */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        gap: 20px;
    }
    
    .header-left {
        flex: 1;
        min-width: 0;
    }

    .header-buttons {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .add-card-btn, .add-funds-btn {
        background: #032B44;
        color: white;
        border: none;
        border-radius: 12px;
        padding: 12px 24px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        min-width: 140px;
        white-space: nowrap;
    }

    .add-card-btn:hover, .add-funds-btn:hover {
        background: #024a6b;
        transform: translateY(-2px);
    }

    .add-card-btn:disabled, .add-funds-btn:disabled {
        background: #ccc;
        cursor: not-allowed;
        transform: none;
    }

    /* Main Content - 2-Column Grid (Like Dashboard) */
    .content-grid {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 25px;
        width: 100%;
    }

    /* Left Column - Transactions */
    .left-column {
        display: flex;
        flex-direction: column;
        gap: 25px;
        min-width: 0;
    }

    /* Right Column - Card Display & Details */
    .right-column {
        display: flex;
        flex-direction: column;
        gap: 30px;
        max-width: 400px;
    }

    /* Dashboard Cards */
    .dashboard-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 24px;
        padding: 30px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s;
    }

    .dashboard-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 25px 50px rgba(0,0,0,0.15);
    }

    .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .card-title {
        font-size: 18px;
        font-weight: 600;
        color: #032B44;
        margin: 0;
    }

    .card-action {
        color: #032B44;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
    }

    .card-action:hover {
        text-decoration: underline;
    }

    /* ===== PERFECT CREDIT CARD DESIGN ===== */
    .credit-card-container {
        width: 100%;
        max-width: 100%;
        margin: 0 auto;
        perspective: 1000px;
    }

    .credit-card {
        position: relative;
        width: 100%;
        height: 0;
        padding-bottom: 63%;
        transform-style: preserve-3d;
        transition: transform 0.6s;
        cursor: pointer;
    }

    .credit-card.is-flipped {
        transform: rotateY(180deg);
    }

    .card-face {
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 12px;
        backface-visibility: hidden;
        overflow: hidden;
        box-sizing: border-box;
    }

    .front-face {
        background: linear-gradient(135deg, #0a2a43, #114678);
        color: white;
        padding: 20px;
        display: grid;
        grid-template-rows: auto 1fr auto;
        grid-template-areas: 
            "top"
            "middle"
            "bottom";
        gap: 10px;
    }

    .card-top-section {
        grid-area: top;
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 30px;
    }

    .site-logo {
        font-size: 16px;
        font-weight: bold;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 60%;
    }

    .mastercard-logo {
        /* Prevent huge logos on mobile / global img styles */
        display: block;
        width: 50px;
        max-width: 50px;
        height: auto;
        object-fit: contain;
        flex-shrink: 0;
    }

    .card-middle-section {
        grid-area: middle;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: flex-start;
        position: relative;
        min-height: 60px;
    }

    .chip {
        width: 40px;
        margin-bottom: 10px;
    }

    .card-number {
        font-size: 18px;
        letter-spacing: 1.5px;
        word-spacing: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        width: 100%;
    }

    .card-bottom-section {
        grid-area: bottom;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        height: 50px;
    }

    .card-holder {
        font-size: 14px;
        font-weight: bold;
        text-transform: uppercase;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        flex: 1;
        padding-right: 15px;
        min-width: 0;
    }

    .card-expiry {
        text-align: right;
        min-width: 70px;
    }

    .valid-thru {
        font-size: 10px;
        opacity: 0.8;
        margin-bottom: 2px;
    }

    .exp-date {
        font-size: 14px;
    }

    .back-face {
        background: #232323;
        color: white;
        transform: rotateY(180deg);
        display: flex;
        flex-direction: column;
    }

    .back-content {
        padding: 20px;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .customer-service {
        font-size: 10px;
        text-align: center;
        color: rgba(255,255,255,0.7);
        margin-bottom: 15px;
    }

    .magnetic-strip {
        height: 40px;
        background: #000;
        width: 100%;
        margin: 10px 0;
    }

    .signature-strip {
        background: #e5e5e5;
        height: 30px;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        padding-right: 15px;
        margin: 15px 0;
    }

    .cvv {
        color: #333;
        font-style: italic;
        font-size: 14px;
    }

    .hologram {
        position: absolute;
        right: 20px;
        top: 80px;
        width: 50px;
        height: 30px;
        background: linear-gradient(45deg, 
            rgba(255,255,255,0.6), 
            rgba(255,255,255,0.2));
        border-radius: 4px;
    }

    .legal-text {
        font-size: 9px;
        text-align: center;
        color: rgba(255,255,255,0.6);
        margin-top: auto;
        padding-top: 10px;
    }

    /* Card Balance Info */
    .card-balance-info {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 16px;
        margin-top: 16px;
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

    /* Card Information */
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

    /* New Transaction Button */
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
    }

    .new-transaction-btn:hover {
        background: #024a6b;
        transform: translateY(-2px);
        box-shadow: 0 12px 25px rgba(3, 43, 68, 0.4);
    }

    /* Modal Styles - Matching Site Design */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        animation: fadeIn 0.3s ease;
    }

    .modal.show {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: 12px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        max-width: 500px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        animation: slideIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modal-header {
        padding: 20px 24px 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #e5e7eb;
        margin-bottom: 20px;
    }

    .modal-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
        color: #1f2937;
    }

    .close {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #6b7280;
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: background 0.2s;
    }

    .close:hover {
        background: #f3f4f6;
        color: #374151;
    }

    .modal-body {
        padding: 0 24px;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        color: #1f2937;
    }

    .form-group select,
    .form-group input[type="number"],
    .form-group input[type="text"] {
        width: 100%;
        padding: 12px 16px;
        font-size: 16px;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        transition: border-color 0.2s, box-shadow 0.2s;
        background-color: #fff;
        box-sizing: border-box;
    }

    .form-group select:focus,
    .form-group input[type="number"]:focus,
    .form-group input[type="text"]:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-group select:hover,
    .form-group input[type="number"]:hover,
    .form-group input[type="text"]:hover {
        border-color: #d1d5db;
    }

    .form-group select {
        cursor: pointer;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23333' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 40px;
    }

    .form-group input[type="number"]::-webkit-outer-spin-button,
    .form-group input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .form-group input[type="number"] {
        appearance: textfield;
        -moz-appearance: textfield;
    }

    /* Form validation states */
    .form-group input.error,
    .form-group select.error {
        border-color: #e53e3e;
        box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.1);
    }

    .form-group input.success,
    .form-group select.success {
        border-color: #38a169;
        box-shadow: 0 0 0 3px rgba(56, 161, 105, 0.1);
    }

    .form-group .error-message {
        color: #e53e3e;
        font-size: 12px;
        margin-top: 4px;
        display: none;
    }

    .form-group.error .error-message {
        display: block;
    }

    /* Enhanced select dropdown styling */
    .form-group select option {
        padding: 8px 12px;
        background-color: #fff;
        color: #2d3748;
    }

    .form-group select option:hover {
        background-color: #f7fafc;
    }

    /* Amount input specific styling */
    .form-group input[type="number"] {
        font-weight: 500;
        letter-spacing: 0.5px;
    }

    /* Description input styling */
    .form-group input[type="text"] {
        font-style: italic;
    }

    .form-group input[type="text"]:focus {
        font-style: normal;
    }

    .modal-actions {
        padding: 0 24px 24px;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 14px;
    }

    .btn-primary {
        background: #3b82f6;
        color: white;
    }

    .btn-primary:hover {
        background: #2563eb;
        transform: translateY(-1px);
    }

    .btn-secondary {
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #d1d5db;
    }

    .btn-secondary:hover {
        background: #e5e7eb;
    }

    /* Notification animations */
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    /* Mobile responsive */
    @media (max-width: 768px) {
        .main-content-area .content-area {
            padding: 10px !important;
        }
        
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
        
        .content-grid {
            grid-template-columns: 1fr;
            gap: 15px;
            display: flex;
            flex-direction: column;
        }
        
        .right-column {
            order: 1;
            width: 100%;
            max-width: 100%;
            gap: 20px;
        }
        
        .left-column {
            order: 2;
            width: 100%;
        }
        
        .page-header {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
        }
        
        .header-buttons {
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        
        .add-card-btn, .add-funds-btn {
            width: 100%;
            justify-content: center;
            min-width: auto;
        }
        
        .modal-content {
            width: 95%;
            max-height: 85vh;
            margin: 10px;
        }
        
        .modal-header {
            padding: 16px 20px 0;
            margin-bottom: 16px;
        }
        
        .modal-header h3 {
            font-size: 16px;
        }
        
        .modal-body {
            padding: 0 20px;
            margin-bottom: 16px;
        }
        
        .modal-actions {
            padding: 0 20px 20px;
            flex-direction: column;
            gap: 8px;
        }
        
        .btn {
            width: 100%;
            padding: 12px 16px;
        }
        
        .dashboard-card {
            padding: 20px;
        }
        
        /* Card aspect ratio on mobile (keep it realistic, not stretched) */
        .credit-card {
            /* Standard card ratio ≈ 85.6×54.0 => 63.1% height */
            padding-bottom: 63% !important;
        }
        
        .front-face, .back-face {
            padding: 25px !important; /* Increase padding for more space */
        }
        
        .card-bottom-section {
            height: 60px !important; /* Increase bottom section height */
        }
        
        .card-holder {
            font-size: 13px !important; /* Slightly smaller font */
        }
        
        .exp-date {
            font-size: 13px !important; /* Slightly smaller font */
        }
        
        .card-number {
            font-size: 16px !important; /* Slightly smaller card number */
        }
    }
    
    @media (max-width: 480px) {
        .main-content-area .content-area {
            padding: 5px !important;
        }
        
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
        
        .dashboard-card {
            padding: 15px;
        }
        
        /* Further adjust card for small mobile screens (still keep realistic ratio) */
        .credit-card {
            padding-bottom: 63% !important;
        }
        
        .front-face, .back-face {
            padding: 20px !important; /* Slightly less padding for small screens */
        }
        
        .card-bottom-section {
            height: 55px !important; /* Adjust bottom section */
        }
        
        .card-holder {
            font-size: 12px !important; /* Smaller font for small screens */
        }
        
        .exp-date {
            font-size: 12px !important; /* Smaller font for small screens */
        }
        
        .card-number {
            font-size: 15px !important; /* Smaller card number for small screens */
        }
        
        .site-logo {
            font-size: 14px !important; /* Smaller logo text */
        }
        
        .mastercard-logo {
            width: 45px !important; /* Smaller logo */
            max-width: 45px !important;
            height: auto !important;
            object-fit: contain;
        }
        
        .chip {
            width: 35px !important; /* Smaller chip */
        }
    }

    /* Dashboard Cards */
    .dashboard-card {
        background: white;
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        transition: all 0.3s;
    }

    .dashboard-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0,0,0,0.12);
    }

    .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .card-title {
        font-size: 18px;
        font-weight: 600;
        color: #032B44;
        margin: 0;
    }

    .card-action {
        color: #032B44;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
    }

    .card-action:hover {
        text-decoration: underline;
    }

    /* ===== REALISTIC CREDIT CARD DESIGN ===== */
    .credit-card-container {
        width: 100%;
        max-width: 100%;
        margin: 0 auto;
        perspective: 1000px;
    }

    .credit-card {
        position: relative;
        width: 100%;
        height: 0;
        padding-bottom: 56.25%; /* Realistic card proportion (16:9 ratio) */
        transform-style: preserve-3d;
        transition: transform 0.6s;
        cursor: pointer;
        max-width: 450px; /* Larger for desktop full-width layout */
        margin: 0 auto;
    }

    .credit-card.is-flipped {
        transform: rotateY(180deg);
    }

    .card-face {
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 12px;
        backface-visibility: hidden;
        overflow: hidden;
        box-sizing: border-box;
    }

    .front-face {
        background: linear-gradient(135deg, #0a2a43, #114678);
        color: white;
        padding: 20px;
        display: grid;
        grid-template-rows: auto 1fr auto;
        grid-template-areas: 
            "top"
            "middle"
            "bottom";
        gap: 10px;
    }

    .card-top-section {
        grid-area: top;
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 30px;
    }

    .site-logo {
        font-size: 16px;
        font-weight: bold;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 60%;
    }

    .mastercard-logo {
        /* Keep consistent sizing across duplicated blocks */
        display: block;
        width: 45px;
        max-width: 45px;
        height: auto;
        object-fit: contain;
        flex-shrink: 0;
    }

    .card-middle-section {
        grid-area: middle;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: flex-start;
        position: relative;
        min-height: 60px;
    }

    .chip {
        width: 35px;
        margin-bottom: 10px;
    }

    .card-number {
        font-size: 16px;
        letter-spacing: 1.5px;
        word-spacing: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        width: 100%;
    }

    .card-bottom-section {
        grid-area: bottom;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        height: 50px;
    }

    .card-holder {
        font-size: 13px;
        font-weight: bold;
        text-transform: uppercase;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        flex: 1;
        padding-right: 15px;
        min-width: 0;
    }

    .card-expiry {
        text-align: right;
        min-width: 65px;
    }

    .valid-thru {
        font-size: 9px;
        opacity: 0.8;
        margin-bottom: 2px;
    }

    .exp-date {
        font-size: 13px;
    }

    .back-face {
        background: #232323;
        color: white;
        transform: rotateY(180deg);
        display: flex;
        flex-direction: column;
    }

    .back-content {
        padding: 18px;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .customer-service {
        font-size: 9px;
        text-align: center;
        color: rgba(255,255,255,0.7);
        margin-bottom: 12px;
    }

    .magnetic-strip {
        height: 35px;
        background: #000;
        width: 100%;
        margin: 8px 0;
    }

    .signature-strip {
        background: #e5e5e5;
        height: 28px;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        padding-right: 15px;
        margin: 12px 0;
    }

    .cvv {
        color: #333;
        font-style: italic;
        font-size: 13px;
    }

    .hologram {
        position: absolute;
        right: 18px;
        top: 70px;
        width: 45px;
        height: 25px;
        background: linear-gradient(45deg, 
            rgba(255,255,255,0.6), 
            rgba(255,255,255,0.2));
        border-radius: 4px;
    }

    .legal-text {
        font-size: 8px;
        text-align: center;
        color: rgba(255,255,255,0.6);
        margin-top: auto;
        padding-top: 8px;
    }

    /* Card Balance Info */
    .card-balance-info {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 16px;
        margin-top: 16px;
        max-width: 450px;
        margin-left: auto;
        margin-right: auto;
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

    /* Card Controls */
    .card-controls {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-top: 20px;
        max-width: 450px;
        margin-left: auto;
        margin-right: auto;
    }

    .control-btn {
        padding: 10px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: white;
        color: #374151;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }

    .control-btn:hover {
        background: #f8f9fa;
        border-color: #d1d5db;
    }

    .control-btn.freeze {
        background: #fef3f2;
        color: #dc2626;
        border-color: #fecaca;
    }

    .control-btn.freeze:hover {
        background: #fef2f2;
    }

    .control-btn.reveal {
        background: #f0f9ff;
        color: #0369a1;
        border-color: #bae6fd;
    }

    .control-btn.reveal:hover {
        background: #e0f2fe;
    }

    /* Transactions List - Expanded for 70% width */
    .transactions-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
        max-height: 400px;
        overflow-y: auto;
    }

    .transaction-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 12px;
        border-radius: 12px;
        transition: all 0.3s ease;
        cursor: pointer;
        border: 1px solid transparent;
    }

    .transaction-item:hover {
        background: #f8f9fa;
        border-color: #e5e7eb;
    }

    .transaction-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        color: white;
    }

    .icon-shopping { background: #8b5cf6; }
    .icon-food { background: #f59e0b; }
    .icon-transport { background: #ef4444; }
    .icon-entertainment { background: #ec4899; }
    .icon-bills { background: #06b6d4; }
    .icon-transfer { background: #10b981; }
    .icon-loan { background: #f59e0b; }

    .transaction-details {
        flex: 1;
    }

    .transaction-title {
        font-weight: 600;
        color: #2d3748;
        font-size: 14px;
        margin-bottom: 2px;
    }

    .transaction-date {
        font-size: 12px;
        color: #6c757d;
    }

    .transaction-amount {
        font-weight: 600;
        font-size: 14px;
    }

    .amount-negative {
        color: #dc3545;
    }

    .amount-positive {
        color: #28a745;
    }

    /* Analytics Chart - Matching Expenses by Category Design */
    .expenses-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
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

    .month-filter .custom-select {
        padding: 8px 16px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: white;
        color: #2d3748;
        font-size: 14px;
        cursor: pointer;
    }

    /* Information Card */
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

    .status-frozen {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #ef4444;
        font-weight: 600;
        font-size: 14px;
    }
    
    .status-pending {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #f59e0b;
        font-weight: 600;
        font-size: 14px;
    }
    
    .status-rejected {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #ef4444;
        font-weight: 600;
        font-size: 14px;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .dot-active { background: #10b981; }
    .dot-frozen { background: #ef4444; }
    .dot-pending { background: #f59e0b; }
    .dot-rejected { background: #ef4444; }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6b7280;
    }

    .empty-icon {
        font-size: 64px;
        color: #d1d5db;
        margin-bottom: 20px;
    }

    .empty-title {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 10px;
        color: #374151;
    }

    .empty-description {
        font-size: 14px;
        margin-bottom: 25px;
    }

    /* ===== MOBILE RESPONSIVE DESIGN ===== */
    @media (max-width: 1024px) {
        .content-grid {
            gap: 20px;
        }

        /* Reset card max-width for mobile */
        .credit-card {
            max-width: 100%;
        }

        .card-balance-info,
        .card-controls {
            max-width: 100%;
        }
    }

    /* Responsive grid adjustments */
    @media (max-width: 1200px) {
        .content-grid {
            grid-template-columns: 1fr 350px;
        }
        .right-column {
            width: 350px;
        }
    }

    @media (max-width: 900px) {
        .content-grid {
            grid-template-columns: 1fr 320px;
        }
        .right-column {
            width: 320px;
        }
    }

    /* Mobile - Vertical Layout with Card First */
    @media (max-width: 768px) {
        .content-grid {
            grid-template-columns: 1fr;
            gap: 15px;
            display: flex;
            flex-direction: column;
        }

        /* Card section appears first on mobile */
        .right-column {
            order: 1;
            width: 100%;
        }

        /* Transactions appear second on mobile */
        .left-column {
            order: 2;
            width: 100%;
        }

        .header {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
        }

        .header h1 {
            font-size: 28px;
        }

        .add-card-btn {
            width: 100%;
            justify-content: center;
        }

        .dashboard-card {
            padding: 20px;
        }

        .card-controls {
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .chart-container {
            height: 250px;
        }

        .transactions-list {
            max-height: 300px;
        }

        .transaction-item {
            padding: 10px;
            gap: 12px;
        }

        .transaction-icon {
            width: 35px;
            height: 35px;
            font-size: 14px;
        }

        /* Mobile-specific card adjustments */
        .credit-card {
            padding-bottom: 56.25%; /* Maintain realistic proportion */
        }

        .front-face,
        .back-content {
            padding: 15px;
        }

        .card-number {
            font-size: 15px;
            letter-spacing: 1px;
        }

        .card-holder {
            font-size: 12px;
        }

        .card-expiry {
            min-width: 60px;
        }

        .exp-date {
            font-size: 12px;
        }

        .mastercard-logo {
            width: 40px;
            max-width: 40px;
            height: auto;
            object-fit: contain;
        }

        .chip {
            width: 30px;
        }
    }

    @media (max-width: 480px) {
        .cards-container {
            gap: 20px;
        }

        .dashboard-card {
            padding: 15px;
            border-radius: 12px;
        }

        .card-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 15px;
        }

        .card-title {
            font-size: 16px;
        }

        .chart-controls {
            flex-wrap: wrap;
            justify-content: center;
        }

        .chart-btn {
            flex: 1;
            min-width: 80px;
            text-align: center;
        }

        .balance-row {
            flex-direction: column;
            gap: 5px;
            align-items: flex-start;
        }

        .balance-row strong {
            font-size: 16px;
        }

        /* Extra small mobile adjustments */
        .credit-card {
            padding-bottom: 56.25%;
        }

        .card-number {
            font-size: 14px;
        }
    }

    @media (max-width: 360px) {
        .header h1 {
            font-size: 24px;
        }

        .card-number {
            font-size: 13px;
            letter-spacing: 0.5px;
        }

        .transaction-item {
            padding: 8px;
            gap: 10px;
        }

        .transaction-icon {
            width: 30px;
            height: 30px;
            font-size: 12px;
        }
    }
    
    /* Card type specific colors */
    .front-face.debit-card {
        background: linear-gradient(135deg, #0a2a43, #114678); /* Blue gradient for debit */
    }
    
    .front-face.credit-card {
        background: linear-gradient(135deg, #d4af37, #ffd700); /* Gold gradient for credit */
        color: #1a1a1a; /* Dark text for gold background */
    }
    
    .front-face.prepaid-card {
        background: linear-gradient(135deg, #8e44ad, #9b59b6); /* Purple gradient for prepaid */
        color: white;
    }
    
    .front-face.virtual-card {
        background: linear-gradient(135deg, #27ae60, #2ecc71); /* Green gradient for virtual */
        color: white;
    }
    
    .front-face.pending-card {
        background: linear-gradient(135deg, #f59e0b, #fbbf24); /* Orange gradient for pending */
        color: white;
    }

    /* ===== Mastercard logo hard-fix (mobile + global overrides) =====
       Some deployments include global img rules or later CSS that can blow up/position the
       .mastercard-logo. This ensures it always stays a small, properly placed logo. */
    .credit-card-container .card-top-section img.mastercard-logo {
        width: 56px !important;
        max-width: 56px !important;
        height: 22px !important;
        max-height: 22px !important;
        object-fit: contain !important;
        object-position: right center !important;
        position: static !important;
        inset: auto !important;
        transform: none !important;
        margin: 0 !important;
        display: block !important;
        flex-shrink: 0 !important;
    }

    @media (max-width: 768px) {
        .credit-card-container .card-top-section {
            height: 32px !important; /* Ensure there's room for the logo */
            align-items: flex-start !important;
        }
        .credit-card-container .card-top-section img.mastercard-logo {
            width: 52px !important;
            max-width: 52px !important;
            height: 20px !important;
            max-height: 20px !important;
        }
    }

    @media (max-width: 480px) {
        .credit-card-container .card-top-section {
            height: 30px !important;
            align-items: flex-start !important;
        }
        .credit-card-container .card-top-section img.mastercard-logo {
            width: 48px !important;
            max-width: 48px !important;
            height: 18px !important;
            max-height: 18px !important;
        }
    }

    /* ===== Mobile flip + transactions hard-fix =====
       iOS Safari and some Android WebViews need WebKit-prefixed 3D/backface rules,
       otherwise the back can render like the front.
       Also fixes flex text truncation in transaction rows on small screens. */

    .credit-card {
        -webkit-transform-style: preserve-3d;
        transform-style: preserve-3d;
        will-change: transform;
    }

    .card-face {
        -webkit-backface-visibility: hidden;
        backface-visibility: hidden;
        -webkit-transform: translateZ(0);
        transform: translateZ(0);
    }

    .credit-card.is-flipped {
        -webkit-transform: rotateY(180deg);
        transform: rotateY(180deg);
    }

    .back-face {
        -webkit-transform: rotateY(180deg) translateZ(0);
        transform: rotateY(180deg) translateZ(0);
    }

    @media (max-width: 768px) {
        /* Transaction row text/amount alignment on mobile */
        .transaction-details {
            min-width: 0; /* allow flex child to shrink so text can ellipsis */
        }
        .transaction-title {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .transaction-amount {
            flex-shrink: 0;
            white-space: nowrap;
        }
    }

    @media (max-width: 480px) {
        .transaction-item {
            align-items: flex-start;
        }
        .transaction-title {
            max-width: 100%;
        }
        .transaction-date {
            white-space: nowrap;
        }
    }
</style>

<div class="cards-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-left">
            <div class="header">
                <h1>My Cards</h1>
                <p>Manage your credit and debit cards</p>
            </div>
        </div>
        <div class="header-buttons">
            <a href="<?php echo SITE_URL; ?>/card/create" class="add-card-btn">
                <i class="fas fa-plus"></i>
                Add New Card
            </a>
            <button class="add-funds-btn" onclick="showAddFundsModal()">
                <i class="fas fa-credit-card"></i>
                Add Funds
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content-grid">
        <!-- Left Column - Transactions & Analytics (70% width) -->
        <div class="left-column">
            <!-- Transactions Section -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title">Recent Transactions</h3>
                    <a href="<?php echo SITE_URL; ?>/transaction" class="card-action">View all</a>
                </div>
                <div class="transactions-list" id="transactionsList">
                    <?php if (!empty($cardTransactions)): ?>
                        <?php foreach ($cardTransactions as $transaction): ?>
                            <?php
                            $iconClass = 'icon-transfer';
                            $iconFA = 'fa-exchange-alt';
                            if (!empty($transaction['expense_category'])) {
                                switch ($transaction['expense_category']) {
                                    case 'shopping':
                                    case 'groceries':
                                        $iconClass = 'icon-shopping';
                                        $iconFA = 'fa-shopping-bag';
                                        break;
                                    case 'food':
                                    case 'dining':
                                    case 'restaurants':
                                        $iconClass = 'icon-food';
                                        $iconFA = 'fa-utensils';
                                        break;
                                    case 'transport':
                                    case 'transportation':
                                    case 'fuel':
                                        $iconClass = 'icon-transport';
                                        $iconFA = 'fa-car';
                                        break;
                                    case 'entertainment':
                                        $iconClass = 'icon-entertainment';
                                        $iconFA = 'fa-film';
                                        break;
                                    case 'bills':
                                    case 'utilities':
                                        $iconClass = 'icon-bills';
                                        $iconFA = 'fa-file-invoice-dollar';
                                        break;
                                }
                            }
                            
                            $amountClass = $transaction['transaction_type'] === 'credit' ? 'amount-positive' : 'amount-negative';
                            $amountPrefix = $transaction['transaction_type'] === 'credit' ? '+' : '-';
                            ?>
                            <div class="transaction-item">
                                <div class="transaction-icon <?php echo $iconClass; ?>">
                                    <i class="fas <?php echo $iconFA; ?>"></i>
                                </div>
                                <div class="transaction-details">
                                    <div class="transaction-title"><?php echo htmlspecialchars($transaction['description']); ?></div>
                                    <div class="transaction-date"><?php echo date('M d, Y', strtotime($transaction['created_at'])); ?></div>
                                </div>
                                <div class="transaction-amount <?php echo $amountClass; ?>">
                                    <?php echo $amountPrefix; ?><?php echo formatTransactionAmountForUser($transaction, $user, 'amount'); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state" style="padding: 30px 20px;">
                            <div class="empty-icon" style="font-size: 48px;">
                                <i class="fas fa-receipt"></i>
                            </div>
                            <p class="empty-description">No transactions yet</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Analytics Section -->
            <div class="dashboard-card">
                <div class="expenses-header">
                    <h3 class="card-title">Spending Analytics</h3>
                    <div class="month-filter">
                        <select id="spendingPeriodSelect" class="custom-select" onchange="loadCardSpendingData()">
                            <option value="category">By Category</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                        </select>
                    </div>
                </div>
                <div class="expenses-container">
                    <div class="expenses-chart">
                        <div class="chart-wrapper">
                            <canvas id="analyticsChart"></canvas>
                        </div>
                        <div class="chart-center-text">
                            <div class="chart-total" id="spendingTotal"><?php echo formatDisplayCurrencyAmount(0, $userCurrency); ?></div>
                            <div class="chart-label">Total</div>
                        </div>
                    </div>
                    <div class="expenses-legend">
                        <div class="expenses-list" id="spendingList">
                            <div class="expense-item">
                                <div class="expense-info">
                                    <div class="category-dot" style="background: #e2e8f0;"></div>
                                    <div class="category-name">No spending data</div>
                                </div>
                                <div class="category-percentage">0%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Card Display (30% width) -->
        <div class="right-column">
            <?php if (!empty($userCards)): ?>
            <!-- My Cards Section -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title">My Cards</h3>
                </div>
                <div style="margin-bottom: 20px;">
                    <select id="cardSelector" style="background: #f8f9fa; border: none; padding: 8px 12px; border-radius: 6px; color: #666; cursor: pointer; width: 100%; max-width: 300px;">
                        <?php 
                        // Combine active and pending cards for the selector
                        $allCards = array_merge($userCards, $pendingCards);
                        foreach ($allCards as $card): 
                        ?>
                            <option value="<?php echo $card['id']; ?>" 
                                    data-card='<?php echo json_encode($card); ?>'
                                    <?php if ($card['status'] === 'pending'): ?>style="color: #f59e0b;"<?php endif; ?>>
                                <?php echo htmlspecialchars($card['card_name']); ?>
                                <?php if ($card['status'] === 'pending'): ?> (Pending)<?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="credit-card-container">
                    <div class="credit-card">
                        <!-- Front Side -->
                        <div class="card-face front-face <?php echo !empty($userCards) ? $userCards[0]['card_type'] . '-card' : 'debit-card'; ?>">
                            <div class="card-top-section">
                                <div class="site-logo"><?php echo htmlspecialchars(SITE_NAME); ?></div>
                                <img class="mastercard-logo" src="<?php echo SITE_URL; ?>/uploads/images/Mastercard-logo.svg.webp" alt="Mastercard">
                            </div>
                            
                            <div class="card-middle-section">
                                <img class="chip" src="<?php echo SITE_URL; ?>/uploads/images/pngegg-25-copy.webp" alt="Chip">
                                <div class="card-number" id="cardNumber">Loading...</div>
                            </div>
                            
                            <div class="card-bottom-section">
                                <div class="card-holder"><?php echo strtoupper(htmlspecialchars($_SESSION['user_name'] ?? 'USER')); ?></div>
                                <div class="card-expiry">
                                    <div class="valid-thru">VALID THRU</div>
                                    <div class="exp-date" id="cardExpiry">Loading...</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Back Side -->
                        <div class="card-face back-face">
                            <div class="back-content">
                                <div class="customer-service">Customer Service: 1-800-123-4567</div>
                                <div class="magnetic-strip"></div>
                                <div class="signature-strip">
                                    <div class="cvv">CVV •••</div>
                                </div>
                                <div class="hologram"></div>
                                <div class="legal-text">Issued by <?php echo htmlspecialchars(SITE_NAME); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-balance-info">
                    <div class="balance-row">
                        <span>Card balance</span>
                        <strong><?php 
                            if (!empty($userCards) && isset($userCards[0]['balance'])) {
                                echo formatCardAmountForUser($userCards[0]['balance'], $user, $userCards[0]);
                            } else {
                                echo formatDisplayCurrencyAmount(0, $userCurrency);
                            }
                        ?></strong>
                    </div>
                    <div class="balance-row">
                        <span>Available credit</span>
                        <strong><?php 
                            if (!empty($userCards) && isset($userCards[0]['available_credit'])) {
                                echo formatCardAmountForUser($userCards[0]['available_credit'], $user, $userCards[0]);
                            } else {
                                echo formatDisplayCurrencyAmount(0, $userCurrency);
                            }
                        ?></strong>
                    </div>
                </div>
            </div>

            <!-- Card Information -->
            <?php if (!empty($userCards)): ?>
            <div class="dashboard-card" id="cardInfoSection">
                <div class="card-header">
                    <h3 class="card-title">Card Information</h3>
                    <a href="#" class="card-action">More details</a>
                </div>
                <div class="info-list">
                    <div class="info-item">
                        <span>Status:</span>
                        <div class="status-active" id="cardStatus">
                            <span class="status-dot"></span>
                            Active
                        </div>
                    </div>
                    <div class="info-item">
                        <span>Card Type:</span>
                        <span id="cardTypeText">MasterCard</span>
                    </div>
                    <div class="info-item">
                        <span>Currency:</span>
                        <span><?php echo strtoupper($userCurrency); ?></span>
                    </div>
                    <div class="info-item">
                        <span>Last Updated:</span>
                        <span id="lastUpdated">11h ago</span>
                    </div>
                </div>
            </div>

            <!-- Card Controls -->
            <div class="dashboard-card" id="cardControls">
                <div class="card-header">
                    <h3 class="card-title">Card Controls</h3>
                </div>
                <div class="card-controls">
                    <button class="control-btn freeze" id="freezeBtn">
                        <i class="fas fa-snowflake"></i>
                        Freeze Card
                    </button>
                    <button class="control-btn reveal" id="revealBtn">
                        <i class="fas fa-eye"></i>
                        Show Details
                    </button>
                    <button class="control-btn" id="replaceBtn">
                        <i class="fas fa-sync-alt"></i>
                        Replace Card
                    </button>
                    <button class="control-btn" id="reportBtn">
                        <i class="fas fa-flag"></i>
                        Report Issue
                    </button>
                    <button class="control-btn delete" id="deleteBtn" style="background: #fee2e2; color: #dc2626; border-color: #fecaca; grid-column: 1 / -1; margin-top: 10px;">
                        <i class="fas fa-trash"></i>
                        Delete Card
                    </button>
                </div>
            </div>

            <!-- New Transaction Button -->
            <button class="new-transaction-btn" onclick="window.location.href='<?php echo SITE_URL; ?>/transfer'">
                <i class="fas fa-plus"></i>
                New transaction
            </button>
            <?php endif; ?>
            <?php else: ?>
            <!-- No Cards Message -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3 class="card-title">My Cards</h3>
                </div>
                <div class="empty-state">
                    <?php if (!empty($pendingCards)): ?>
                        <!-- Pending Cards Info -->
                        <div class="pending-cards-info" style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
                            <div style="display: flex; align-items: center; margin-bottom: 15px;">
                                <i class="fas fa-clock" style="color: #856404; font-size: 20px; margin-right: 10px;"></i>
                                <h4 style="color: #856404; margin: 0;">Card Applications Pending</h4>
                            </div>
                            <p style="color: #856404; margin-bottom: 15px;">You have <?php echo count($pendingCards); ?> card application(s) pending admin approval.</p>
                            <div style="display: flex; gap: 10px;">
                                <a href="<?php echo SITE_URL; ?>/card/create" class="btn btn-warning">
                                    <i class="fas fa-plus"></i>
                                    Apply for Another Card
                                </a>
                                <a href="<?php echo SITE_URL; ?>/card/applications" class="btn btn-outline-warning">
                                    <i class="fas fa-list"></i>
                                    View Applications
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- No Cards Message -->
                        <div class="empty-icon" style="font-size: 64px; color: #ccc;">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <h4 style="color: #666; margin: 20px 0 10px;">No Cards Found</h4>
                        <p style="color: #999; margin-bottom: 30px;">You don't have any cards yet. Apply for a new card to get started.</p>
                    <?php endif; ?>
                    
                    <a href="<?php echo SITE_URL; ?>/card/create" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Apply for New Card
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add Funds Modal -->
<div id="addFundsModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Add Funds to Card</h3>
            <span class="close" onclick="closeAddFundsModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="addFundsForm">
                <div class="form-group">
                    <label for="sourceAccount">Select Account to Fund From:</label>
                    <select id="sourceAccount" name="source_account" required>
                        <option value="">Choose an account...</option>
                        <?php
                        // Get user accounts for funding
                        $accountModel = new Account();
                        $userAccounts = $accountModel->getUserAccounts($_SESSION['user_id']);
                        foreach ($userAccounts as $account) {
                            echo "<option value='{$account['id']}' data-balance='{$account['balance']}' data-currency='{$userCurrency}'>";
                            echo htmlspecialchars($account['account_type']) . " - " . formatAccountBalance($account['balance'], $account, $userCurrency);
                            echo "</option>";
                        }
                        ?>
                    </select>
                    <div class="error-message">Please select an account</div>
                </div>
                
                <div class="form-group">
                    <label for="fundAmount">Amount to Add:</label>
                    <input type="number" id="fundAmount" name="amount" step="0.01" min="0.01" required placeholder="Enter amount...">
                    <div class="error-message">Please enter a valid amount</div>
                </div>
                
                <div class="form-group">
                    <label for="fundDescription">Description (Optional):</label>
                    <input type="text" id="fundDescription" name="description" placeholder="e.g., Card funding for purchases">
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeAddFundsModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Funds</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <script src="<?php echo SITE_URL; ?>/assets/js/chart.min.js"></script>
<script>
    // Card data from PHP
    const cardData = <?php echo json_encode($userCards); ?>;
    
    // User currency for formatting
    const userCurrency = <?php echo json_encode($userCurrency); ?>;
    const defaultCurrency = <?php echo json_encode(DEFAULT_CURRENCY); ?>;
    
    // Currency symbols mapping
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
    
    // Exchange rate cache
    let exchangeRateCache = {};
    
    // Fetch exchange rate on page load
    async function loadExchangeRate() {
        if (defaultCurrency !== userCurrency) {
            try {
                const response = await fetch(`<?php echo SITE_URL; ?>/api/get-exchange-rate.php?from=${defaultCurrency}&to=${userCurrency}`);
                const data = await response.json();
                if (data.success && data.rate) {
                    exchangeRateCache[`${defaultCurrency}_${userCurrency}`] = data.rate;
                }
            } catch (error) {
                console.error('Error loading exchange rate:', error);
                exchangeRateCache[`${defaultCurrency}_${userCurrency}`] = 1.0;
            }
        } else {
            exchangeRateCache[`${defaultCurrency}_${userCurrency}`] = 1.0;
        }
    }
    
    // Format currency in JavaScript (with conversion)
    function formatCurrencyJS(amount, currency = userCurrency, fromCurrency = defaultCurrency) {
        // Convert amount if currencies are different and we have exchange rate
        if (fromCurrency !== currency) {
            const cacheKey = `${fromCurrency}_${currency}`;
            if (exchangeRateCache[cacheKey]) {
                amount = parseFloat(amount) * exchangeRateCache[cacheKey];
            }
        }
        
        const symbol = currencySymbols[currency] || (currency + ' ');
        const decimals = ['JPY', 'KRW', 'VND', 'CLP'].includes(currency) ? 0 : 2;
        return symbol + parseFloat(amount).toFixed(decimals).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }
    
    let currentCard = null;
    let analyticsChart = null;

    // Credit card flip functionality
    document.addEventListener("DOMContentLoaded", function() {
        // Load exchange rate on page load
        loadExchangeRate();
        
        console.log('🚀 CARD INDEX DEBUG: Page loaded, checking elements...');
        
        // Check if delete button exists
        const deleteButton = document.getElementById('deleteBtn');
        if (deleteButton) {
            console.log('✅ DELETE BUTTON DEBUG: Delete button found:', deleteButton);
            console.log('✅ DELETE BUTTON DEBUG: Button text:', deleteButton.textContent.trim());
            console.log('✅ DELETE BUTTON DEBUG: Button style:', deleteButton.style.cssText);
        } else {
            console.error('❌ DELETE BUTTON DEBUG: Delete button NOT FOUND!');
        }
        
        // Check if freeze button exists
        const freezeButton = document.getElementById('freezeBtn');
        if (freezeButton) {
            console.log('❄️ FREEZE BUTTON DEBUG: Freeze button found:', freezeButton);
        } else {
            console.error('❌ FREEZE BUTTON DEBUG: Freeze button NOT FOUND!');
        }
        
        // Check card data
        console.log('📊 CARD DATA DEBUG: Card data:', cardData);
        console.log('📊 CARD DATA DEBUG: Card data length:', cardData ? cardData.length : 'null');
        
        const card = document.querySelector(".credit-card");
        if(card) {
            card.addEventListener("click", function() {
                this.classList.toggle("is-flipped");
            });
        }
        
        // Card controls functionality
        setupCardControls();
        
        // Initialize card state based on data
        initializeCardState();
        
        // Initialize card display and chart
        if (cardData.length > 0) {
            currentCard = cardData[0];
            console.log('🚀 INIT DEBUG: Initial currentCard:', currentCard);
            displayCurrentCard();
            updateCardInfo();
        } else {
            // If no cards, still initialize the static display
            updateStaticCardDisplay();
        }
        initializeAnalyticsChart();
        
        // Add funds form handling
        setupAddFundsForm();
        
        console.log('🚀 CARD INDEX DEBUG: Initialization complete');
    });
    
    function setupCardControls() {
        const freezeBtn = document.getElementById('freezeBtn');
        const revealBtn = document.getElementById('revealBtn');
        const replaceBtn = document.getElementById('replaceBtn');
        const reportBtn = document.getElementById('reportBtn');
        const deleteBtn = document.getElementById('deleteBtn');
        
        if (freezeBtn) {
            freezeBtn.addEventListener('click', function() {
                console.log('❄️ FREEZE CARD DEBUG: Freeze button clicked');
                
                // Get the actual card status from database data, not button class
                const currentCardId = cardData && cardData.length > 0 ? cardData[0].id : null;
                const actualCardStatus = cardData && cardData.length > 0 ? cardData[0].status : null;
                const isFrozen = actualCardStatus === 'frozen';
                
                console.log('❄️ FREEZE CARD DEBUG: Card ID:', currentCardId);
                console.log('❄️ FREEZE CARD DEBUG: Actual card status:', actualCardStatus);
                console.log('❄️ FREEZE CARD DEBUG: Is frozen (from DB):', isFrozen);
                console.log('❄️ FREEZE CARD DEBUG: Button has frozen class:', this.classList.contains('frozen'));
                
                if (!currentCardId) {
                    showNotification('No card found to freeze/unfreeze', 'error');
                    return;
                }
                
                const action = isFrozen ? 'unfreeze' : 'freeze';
                const apiEndpoint = isFrozen ? '<?php echo SITE_URL; ?>/api/card-unfreeze.php' : '<?php echo SITE_URL; ?>/api/card-freeze.php';
                
                console.log('❄️ FREEZE CARD DEBUG: Action:', action, 'API:', apiEndpoint);
                
                // Make API call
                fetch(apiEndpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        card_id: currentCardId
                    })
                })
                .then(response => {
                    console.log('❄️ FREEZE CARD DEBUG: API response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('❄️ FREEZE CARD DEBUG: API response data:', data);
                    if (data.success) {
                        // Update the card data to reflect the new status
                        if (cardData && cardData.length > 0) {
                            cardData[0].status = isFrozen ? 'active' : 'frozen';
                            console.log('❄️ FREEZE CARD DEBUG: Updated card data status to:', cardData[0].status);
                        }
                        
                        // Update UI based on API response
                        if (isFrozen) {
                            this.classList.remove('frozen');
                            this.innerHTML = '<i class="fas fa-snowflake"></i> Freeze Card';
                            showNotification('Card unfrozen successfully', 'success');
                        } else {
                            this.classList.add('frozen');
                            this.innerHTML = '<i class="fas fa-fire"></i> Unfreeze Card';
                            showNotification('Card frozen successfully', 'success');
                        }
                        
                        // Update card display to show freeze effect
                        updateCardFreezeDisplay(!isFrozen);
                    } else {
                        console.error('❄️ FREEZE CARD DEBUG: API returned error:', data.message);
                        showNotification(data.message || 'Failed to freeze/unfreeze card', 'error');
                    }
                })
                .catch(error => {
                    console.error('❄️ FREEZE CARD DEBUG: Error occurred:', error);
                    showNotification('An error occurred while freezing/unfreezing the card', 'error');
                });
            });
        }
        
        if (revealBtn) {
            revealBtn.addEventListener('click', function() {
                const cardNumber = document.getElementById('cardNumber');
                if (cardNumber) {
                    if (cardNumber.style.filter === 'blur(5px)') {
                        cardNumber.style.filter = 'none';
                        this.innerHTML = '<i class="fas fa-eye-slash"></i> Hide Details';
                        showNotification('Card details revealed', 'info');
                    } else {
                        cardNumber.style.filter = 'blur(5px)';
                        this.innerHTML = '<i class="fas fa-eye"></i> Show Details';
                        showNotification('Card details hidden', 'info');
                    }
                }
            });
        }
        
        if (replaceBtn) {
            replaceBtn.addEventListener('click', function() {
                if (confirm('Are you sure you want to replace this card? This action cannot be undone.')) {
                    showNotification('Card replacement request submitted', 'info');
                }
            });
        }
        
        if (reportBtn) {
            reportBtn.addEventListener('click', function() {
                const issue = prompt('Please describe the issue with your card:');
                if (issue && issue.trim()) {
                    showNotification('Issue reported successfully. We will contact you soon.', 'success');
                }
            });
        }
        
        if (deleteBtn) {
            deleteBtn.addEventListener('click', function() {
                console.log('🔴 DELETE CARD DEBUG: Delete button clicked');
                
                if (!confirm('Are you sure you want to delete this card? This action cannot be undone.')) {
                    console.log('🔴 DELETE CARD DEBUG: User cancelled first confirmation');
                    return;
                }
                
                if (!confirm('This will permanently delete your card and all associated data. Are you absolutely sure?')) {
                    console.log('🔴 DELETE CARD DEBUG: User cancelled second confirmation');
                    return;
                }
                
                console.log('🔴 DELETE CARD DEBUG: Proceeding with deletion...');
                
                // Get current card ID
                const currentCardId = cardData && cardData.length > 0 ? cardData[0].id : null;
                console.log('🔴 DELETE CARD DEBUG: Card ID:', currentCardId);
                
                if (!currentCardId) {
                    showNotification('No card found to delete', 'error');
                    return;
                }
                
                // Make API call to delete card
                fetch('<?php echo SITE_URL; ?>/api/card-delete.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        card_id: currentCardId
                    })
                })
                .then(response => {
                    console.log('🔴 DELETE CARD DEBUG: API response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('🔴 DELETE CARD DEBUG: API response data:', data);
                    if (data.success) {
                        console.log('🔴 DELETE CARD DEBUG: Deletion successful, redirecting...');
                        showNotification('Card deleted successfully', 'success');
                        setTimeout(() => {
                            window.location.href = '<?php echo SITE_URL; ?>/card';
                        }, 1500);
                    } else {
                        console.log('🔴 DELETE CARD DEBUG: Deletion failed:', data.message);
                        showNotification(data.message || 'Failed to delete card', 'error');
                    }
                })
                .catch(error => {
                    console.error('🔴 DELETE CARD DEBUG: Error occurred:', error);
                    showNotification('An error occurred while deleting the card', 'error');
                });
            });
        }
    }
    
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 20px;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            z-index: 10000;
            animation: slideIn 0.3s ease-out;
        `;
        
        if (type === 'success') notification.style.background = '#10b981';
        else if (type === 'error') notification.style.background = '#ef4444';
        else notification.style.background = '#3b82f6';
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease-in';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
    
    function updateCardFreezeDisplay(isFrozen) {
        console.log('❄️ FREEZE DISPLAY DEBUG: Updating card freeze display, isFrozen:', isFrozen);
        
        const cardElement = document.querySelector('.credit-card');
        const frozenOverlay = document.querySelector('.frozen-overlay');
        
        if (cardElement) {
            if (isFrozen) {
                cardElement.classList.add('frozen');
                console.log('❄️ FREEZE DISPLAY DEBUG: Added frozen class to card');
                
                // Create frozen overlay if it doesn't exist
                if (!frozenOverlay) {
                    const overlay = document.createElement('div');
                    overlay.className = 'frozen-overlay';
                    overlay.innerHTML = '<i class="fas fa-snowflake"></i><span>FROZEN</span>';
                    overlay.style.cssText = `
                        position: absolute;
                        top: 0;
                        left: 0;
                        right: 0;
                        bottom: 0;
                        background: rgba(0, 0, 0, 0.7);
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: center;
                        color: white;
                        font-size: 18px;
                        font-weight: bold;
                        border-radius: 12px;
                        z-index: 10;
                    `;
                    cardElement.appendChild(overlay);
                    console.log('❄️ FREEZE DISPLAY DEBUG: Created frozen overlay');
                }
            } else {
                cardElement.classList.remove('frozen');
                console.log('❄️ FREEZE DISPLAY DEBUG: Removed frozen class from card');
                
                // Remove frozen overlay if it exists
                if (frozenOverlay) {
                    frozenOverlay.remove();
                    console.log('❄️ FREEZE DISPLAY DEBUG: Removed frozen overlay');
                }
            }
        } else {
            console.error('❄️ FREEZE DISPLAY DEBUG: Card element not found');
        }
    }
    
    function initializeCardState() {
        console.log('🚀 INITIALIZE CARD STATE DEBUG: Initializing card state...');
        
        if (cardData && cardData.length > 0) {
            const currentCard = cardData[0];
            console.log('🚀 INITIALIZE CARD STATE DEBUG: Current card data:', currentCard);
            
            // Initialize freeze button state
            const freezeBtn = document.getElementById('freezeBtn');
            if (freezeBtn && currentCard.status) {
                const isFrozen = currentCard.status === 'frozen';
                console.log('🚀 INITIALIZE CARD STATE DEBUG: Card status:', currentCard.status, 'Is frozen:', isFrozen);
                
                if (isFrozen) {
                    freezeBtn.classList.add('frozen');
                    freezeBtn.innerHTML = '<i class="fas fa-play"></i> Unfreeze Card';
                    updateCardFreezeDisplay(true);
                } else {
                    freezeBtn.classList.remove('frozen');
                    freezeBtn.innerHTML = '<i class="fas fa-snowflake"></i> Freeze Card';
                    updateCardFreezeDisplay(false);
                }
            }
        } else {
            console.log('🚀 INITIALIZE CARD STATE DEBUG: No card data available');
        }
    }
    
    // Modal functions - Global scope
    window.showAddFundsModal = function() {
        console.log('showAddFundsModal called');
        const modal = document.getElementById('addFundsModal');
        console.log('Modal element:', modal);
        if (modal) {
            modal.classList.add('show');
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            console.log('Modal should be visible now');
        } else {
            console.error('Modal element not found');
        }
    };
    
    window.closeAddFundsModal = function() {
        console.log('closeAddFundsModal called');
        const modal = document.getElementById('addFundsModal');
        if (modal) {
            modal.classList.remove('show');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
            // Reset form
            const form = document.getElementById('addFundsForm');
            if (form) {
                form.reset();
            }
        }
    };
    
    // Close modal when clicking outside
    document.addEventListener('click', function(event) {
        const modal = document.getElementById('addFundsModal');
        if (event.target === modal) {
            closeAddFundsModal();
        }
    });
    
    function setupAddFundsForm() {
        const form = document.getElementById('addFundsForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                addFundsToCard();
            });
            
            // Add real-time validation
            const sourceAccount = document.getElementById('sourceAccount');
            const fundAmount = document.getElementById('fundAmount');
            
            if (sourceAccount) {
                sourceAccount.addEventListener('change', function() {
                    validateField(this);
                });
            }
            
            if (fundAmount) {
                fundAmount.addEventListener('input', function() {
                    validateField(this);
                });
            }
        }
    }
    
    function validateField(field) {
        const formGroup = field.closest('.form-group');
        const errorMessage = formGroup.querySelector('.error-message');
        
        // Remove previous validation classes
        field.classList.remove('error', 'success');
        formGroup.classList.remove('error');
        
        if (field.hasAttribute('required') && !field.value.trim()) {
            field.classList.add('error');
            formGroup.classList.add('error');
            return false;
        }
        
        // Additional validation for amount field
        if (field.type === 'number') {
            const value = parseFloat(field.value);
            if (value <= 0) {
                field.classList.add('error');
                formGroup.classList.add('error');
                return false;
            }
        }
        
        // Success state
        if (field.value.trim()) {
            field.classList.add('success');
        }
        
        return true;
    }
    
    async function addFundsToCard() {
        const form = document.getElementById('addFundsForm');
        const formData = new FormData(form);
        
        const sourceAccount = formData.get('source_account');
        const amount = parseFloat(formData.get('amount'));
        const description = formData.get('description') || 'Card funding';
        
        // Validation
        if (!sourceAccount) {
            showNotification('Please select a source account', 'error');
            return;
        }
        
        if (!amount || amount <= 0) {
            showNotification('Please enter a valid amount', 'error');
            return;
        }
        
        // Check if user has enough balance
        const selectedOption = document.querySelector(`#sourceAccount option[value="${sourceAccount}"]`);
        if (!selectedOption) {
            showNotification('Invalid account selected', 'error');
            return;
        }
        
        const accountBalance = parseFloat(selectedOption.dataset.balance);
        
        if (amount > accountBalance) {
            showNotification('Insufficient balance in selected account', 'error');
            return;
        }
        
        // Debug: Log card data
        console.log('Card data:', cardData);
        console.log('Card data length:', cardData ? cardData.length : 'null');
        
        // Get current card ID (assuming first card for now)
        const currentCardId = cardData && cardData.length > 0 ? cardData[0].id : null;
        console.log('Current card ID:', currentCardId);
        
        if (!currentCardId) {
            showNotification('No cards found. Please create a card first.', 'error');
            return;
        }
        
        try {
            const requestData = {
                card_id: currentCardId,
                source_account_id: sourceAccount,
                amount: amount,
                description: description
            };
            
            console.log('Sending request data:', requestData);
            
            const response = await fetch('<?php echo SITE_URL; ?>/api/card-add-funds.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(requestData)
            });
            
            const result = await response.json();
            
            if (result.success) {
                showNotification('Funds added successfully!', 'success');
                closeAddFundsModal();
                
                // Update card balance display
                updateCardBalance(amount);
                
                // Refresh transactions and card balance without page reload
                if (typeof loadTransactions === 'function') {
                    loadTransactions();
                }
                
                // Note: Card balance and transactions will be updated dynamically
            } else {
                showNotification(result.message || 'Failed to add funds', 'error');
            }
        } catch (error) {
            console.error('Error adding funds:', error);
            showNotification('Network error. Please try again.', 'error');
        }
    }
    
    function updateCardBalance(amount) {
        const balanceElements = document.querySelectorAll('.card-balance-info strong');
        if (balanceElements.length >= 1) {
            // Update card balance (first balance element)
            const balanceElement = balanceElements[0];
            const currentBalance = parseFloat(balanceElement.textContent.replace('$', '').replace(',', ''));
            const newBalance = currentBalance + amount;
            balanceElement.textContent = '$' + newBalance.toFixed(2);
        }
    }
    let cardDetailsVisible = false;

    // DOM Elements
    const cardDisplay = document.getElementById('cardDisplay');
    const cardInfoSection = document.getElementById('cardInfoSection');
    const cardControls = document.getElementById('cardControls');
    const cardSelector = document.getElementById('cardSelector');
    const cardStatus = document.getElementById('cardStatus');
    const cardTypeText = document.getElementById('cardTypeText');
    const lastUpdated = document.getElementById('lastUpdated');
    const freezeBtn = document.getElementById('freezeBtn');
    const revealBtn = document.getElementById('revealBtn');

    // Initialize
    function init() {
        if (cardData.length > 0) {
            currentCard = cardData[0];
            console.log('🚀 INIT DEBUG: Initial currentCard:', currentCard);
            displayCurrentCard();
            updateCardInfo();
        } else {
            // If no cards, still initialize the static display
            updateStaticCardDisplay();
        }
        initializeAnalyticsChart();
        setupEventListeners();
    }

    // Update static card display elements
    function updateStaticCardDisplay() {
        if (!currentCard) return;
        
        // Update card number
        const cardNumberElement = document.getElementById('cardNumber');
        if (cardNumberElement) {
            if (currentCard.status === 'pending') {
                cardNumberElement.textContent = '•••• •••• •••• ••••';
            } else {
                const cardNumber = currentCard.card_number || '•••• •••• •••• ••••';
                // Remove spaces to get last 4 digits
                const cardNumberClean = cardNumber.replace(/\s/g, '');
                const last4 = cardNumberClean.slice(-4);
                const maskedNumber = '•••• •••• •••• ' + last4;
                cardNumberElement.textContent = cardDetailsVisible ? cardNumber : maskedNumber;
            }
        }
        
        // Update expiry date
        const cardExpiryElement = document.getElementById('cardExpiry');
        if (cardExpiryElement) {
            cardExpiryElement.textContent = formatExpiry(currentCard.expiry_date);
        }
        
        // Update card type class
        const cardFaceElement = document.querySelector('.front-face');
        if (cardFaceElement) {
            // Remove all card type classes
            cardFaceElement.classList.remove('debit-card', 'credit-card', 'prepaid-card', 'virtual-card', 'pending-card');
            
            // Add the correct class based on card status and type
            if (currentCard.status === 'pending') {
                cardFaceElement.classList.add('pending-card');
            } else {
                cardFaceElement.classList.add(currentCard.card_type + '-card');
            }
        }
    }

    // Display current card
    function displayCurrentCard() {
        if (!currentCard) return;
        
        // Update static card display
        updateStaticCardDisplay();
        
        // Check if cardDisplay element exists
        if (!cardDisplay) return;

        // Handle different card statuses
        let displayNumber, displayCVV, cardTypeClass;
        
        if (currentCard.status === 'pending') {
            // For pending cards, show placeholder data
            displayNumber = '•••• •••• •••• ••••';
            displayCVV = '•••';
            cardTypeClass = 'pending-card';
        } else {
            // For active/frozen cards, show real data
            const cardNumber = currentCard.card_number || '•••• •••• •••• ••••';
            // Remove spaces to get last 4 digits
            const cardNumberClean = cardNumber.replace(/\s/g, '');
            const last4 = cardNumberClean.slice(-4);
            const maskedNumber = '•••• •••• •••• ' + last4;
            displayNumber = cardDetailsVisible ? cardNumber : maskedNumber;
            
            const cvv = currentCard.cvv || '•••';
            displayCVV = cardDetailsVisible ? cvv : '•••';
            cardTypeClass = currentCard.card_type + '-card';
        }

        cardDisplay.innerHTML = `
            <div class="credit-card-container">
                <div class="credit-card" id="creditCard">
                    <!-- Front Side -->
                    <div class="card-face front-face ${cardTypeClass}">
                        <div class="card-top-section">
                            <div class="site-logo"><?php echo htmlspecialchars($siteNameShort); ?></div>
                            <img class="mastercard-logo" src="<?php echo SITE_URL; ?>/uploads/images/Mastercard-logo.svg.webp" alt="Card">
                        </div>
                        
                        <div class="card-middle-section">
                            <img class="chip" src="<?php echo SITE_URL; ?>/uploads/images/pngegg-25-copy.webp" alt="Chip">
                            <div class="card-number">${displayNumber}</div>
                        </div>
                        
                        <div class="card-bottom-section">
                            <div class="card-holder"><?php echo strtoupper($_SESSION['user_name'] ?? 'CARD HOLDER'); ?></div>
                            <div class="card-expiry">
                                <div class="valid-thru">VALID THRU</div>
                                <div class="exp-date">${formatExpiry(currentCard.expiry_date)}</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Back Side -->
                    <div class="card-face back-face">
                        <div class="back-content">
                            <div class="customer-service">Customer Service: <?php echo htmlspecialchars(getSetting('support_phone', '1-800-SUPPORT') ?? '1-800-SUPPORT'); ?></div>
                            <div class="magnetic-strip"></div>
                            <div class="signature-strip">
                                <div class="cvv">CVV ${displayCVV}</div>
                            </div>
                            <div class="hologram"></div>
                            <div class="legal-text">Issued by <?php echo htmlspecialchars($siteName); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-balance-info">
                ${currentCard.card_type === 'credit' ? `
                    <div class="balance-row">
                        <span>Available credit</span>
                        <strong>$${formatNumber(currentCard.available_credit || 0)}</strong>
                    </div>
                    <div class="balance-row">
                        <span>Credit limit</span>
                        <strong>$${formatNumber(currentCard.credit_limit || 0)}</strong>
                    </div>
                ` : `
                    <div class="balance-row">
                        <span>Daily limit</span>
                        <strong>$${formatNumber(currentCard.daily_limit || 0)}</strong>
                    </div>
                    <div class="balance-row">
                        <span>Monthly limit</span>
                        <strong>$${formatNumber(currentCard.monthly_limit || 0)}</strong>
                    </div>
                `}
            </div>
        `;

        // Setup card flip functionality
        setupCardFlip();
    }

    // Update card information
    function updateCardInfo() {
        if (!currentCard) return;

        console.log('❄️ UPDATE CARD INFO DEBUG: Updating card info for status:', currentCard.status);

        // Update status
        if (currentCard.status === 'active') {
            cardStatus.innerHTML = `
                <span class="status-dot dot-active"></span>
                Active
            `;
            cardStatus.className = 'status-active';
            freezeBtn.innerHTML = '<i class="fas fa-snowflake"></i> Freeze Card';
            freezeBtn.className = 'control-btn freeze';
            freezeBtn.classList.remove('frozen');
            freezeBtn.style.display = 'block';
        } else if (currentCard.status === 'frozen') {
            cardStatus.innerHTML = `
                <span class="status-dot dot-frozen"></span>
                Frozen
            `;
            cardStatus.className = 'status-frozen';
            freezeBtn.innerHTML = '<i class="fas fa-play"></i> Unfreeze Card';
            freezeBtn.className = 'control-btn';
            freezeBtn.classList.add('frozen');
            freezeBtn.style.display = 'block';
        } else if (currentCard.status === 'pending') {
            cardStatus.innerHTML = `
                <span class="status-dot dot-pending"></span>
                Pending Approval
            `;
            cardStatus.className = 'status-pending';
            freezeBtn.style.display = 'none'; // Hide freeze button for pending cards
        } else if (currentCard.status === 'rejected') {
            cardStatus.innerHTML = `
                <span class="status-dot dot-rejected"></span>
                Rejected
            `;
            cardStatus.className = 'status-rejected';
            freezeBtn.style.display = 'none'; // Hide freeze button for rejected cards
        }

        cardTypeText.textContent = currentCard.card_name || 'Card';
        lastUpdated.textContent = formatRelativeTime(currentCard.created_at);
        
        // Update static card display
        updateStaticCardDisplay();
        
        console.log('❄️ UPDATE CARD INFO DEBUG: Button classes after update:', freezeBtn.className);
    }

    // Initialize analytics chart
    function initializeAnalyticsChart() {
        const ctx = document.getElementById('analyticsChart');
        if (!ctx) return;
        
        // Destroy existing chart if it exists
        if (analyticsChart) {
            analyticsChart.destroy();
        }
        
        // Load initial data
        loadCardSpendingData();
    }

    // Load card spending data from API
    async function loadCardSpendingData() {
        if (!currentCard || !currentCard.id) {
            // No card selected, show empty state
            updateSpendingChart([], [], [], 0, 'doughnut', []);
            return;
        }

        const periodSelect = document.getElementById('spendingPeriodSelect');
        const period = periodSelect ? periodSelect.value : 'category';

        try {
            const response = await fetch(`<?php echo SITE_URL; ?>/api/get-card-spending-data.php?card_id=${currentCard.id}&period=${period}`);
            const result = await response.json();

            if (result.success) {
                updateSpendingChart(
                    result.labels || [],
                    result.data || [],
                    result.colors || [],
                    result.total || 0,
                    result.type || 'doughnut',
                    result.legend || []
                );
            } else {
                console.error('Failed to load spending data:', result.message);
                updateSpendingChart([], [], [], 0, 'doughnut', []);
            }
        } catch (error) {
            console.error('Error loading spending data:', error);
            updateSpendingChart([], [], [], 0, 'doughnut', []);
        }
    }

    // Update spending chart and legend
    function updateSpendingChart(labels, data, colors, total, chartType, legend) {
        const ctx = document.getElementById('analyticsChart');
        if (!ctx) return;

        // Destroy existing chart
        if (analyticsChart) {
            analyticsChart.destroy();
        }

        // Update total display
        const totalElement = document.getElementById('spendingTotal');
        if (totalElement) {
            totalElement.textContent = formatCurrencyJS(total, userCurrency);
        }

        // Update legend
        const legendContainer = document.getElementById('spendingList');
        if (legendContainer) {
            if (legend.length === 0) {
                legendContainer.innerHTML = `
                    <div class="expense-item">
                        <div class="expense-info">
                            <div class="category-dot" style="background: #e2e8f0;"></div>
                            <div class="category-name">No spending data</div>
                        </div>
                        <div class="category-percentage">0%</div>
                    </div>
                `;
            } else {
                let legendHTML = '';
                legend.forEach(item => {
                    legendHTML += `
                        <div class="expense-item">
                            <div class="expense-info">
                                <div class="category-dot" style="background: ${item.color};"></div>
                                <div class="category-name">${item.category}</div>
                            </div>
                            <div class="category-percentage">${item.percentage.toFixed(1)}%</div>
                        </div>
                    `;
                });
                legendContainer.innerHTML = legendHTML;
            }
        }

        // Create new chart
        if (data.length === 0) {
            // Show empty chart
            analyticsChart = new Chart(ctx, {
                type: chartType,
                data: {
                    labels: ['No Data'],
                    datasets: [{
                        data: [1],
                        backgroundColor: ['#e2e8f0']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    cutout: chartType === 'doughnut' ? '60%' : 0
                }
            });
            return;
        }

        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false // We use custom legend
                }
            }
        };

        if (chartType === 'doughnut') {
            chartOptions.cutout = '60%';
            analyticsChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: colors,
                        borderWidth: 0,
                        borderRadius: 8,
                        spacing: 2
                    }]
                },
                options: chartOptions
            });
        } else {
            // Bar chart for week/month
            analyticsChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Spending',
                        data: data,
                        backgroundColor: colors[0] || '#4f46e5',
                        borderRadius: 8
                    }]
                },
                options: {
                    ...chartOptions,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    }

    // Format expiry date
    function formatExpiry(expiryDate) {
        if (!expiryDate) return '12/28';
        const date = new Date(expiryDate);
        return date.toLocaleDateString('en-US', {month: '2-digit', year: '2-digit'});
    }

    // Refresh card data from server
    async function refreshCardData() {
        try {
            const response = await fetch(`<?php echo SITE_URL; ?>/api/get-card-details.php?card_id=${currentCard.id}`);
            const data = await response.json();
            
            if (data.success && data.card) {
                // Update the current card with fresh data
                Object.assign(currentCard, data.card);
                console.log('🔄 REFRESH DEBUG: Card data refreshed:', currentCard);
            }
        } catch (error) {
            console.error('🔄 REFRESH DEBUG: Error refreshing card data:', error);
        }
    }

    // Load transactions for a specific card
    async function loadCardTransactions(cardId) {
        try {
            const response = await fetch(`<?php echo SITE_URL; ?>/api/get-card-transactions.php?card_id=${cardId}`);
            const data = await response.json();
            
            if (data.success) {
                updateTransactionsDisplay(data.transactions);
            }
        } catch (error) {
            console.error('Error loading card transactions:', error);
        }
    }
    
    // Update the transactions display
    function updateTransactionsDisplay(transactions) {
        const transactionsContainer = document.querySelector('.recent-transactions-list');
        if (!transactionsContainer) return;
        
        if (transactions.length === 0) {
            transactionsContainer.innerHTML = '<div class="no-transactions">No recent transactions</div>';
            return;
        }
        
        let html = '';
        transactions.forEach(transaction => {
            const amount = parseFloat(transaction.amount);
            const isDebit = transaction.transaction_type === 'debit';
            const amountClass = isDebit ? 'amount-debit' : 'amount-credit';
            const amountPrefix = isDebit ? '-' : '+';
            
            html += `
                <div class="transaction-item">
                    <div class="transaction-icon ${isDebit ? 'debit' : 'credit'}">
                        <i class="fas fa-${isDebit ? 'arrow-down' : 'arrow-up'}"></i>
                    </div>
                    <div class="transaction-details">
                        <div class="transaction-description">${transaction.description}</div>
                        <div class="transaction-date">${new Date(transaction.created_at).toLocaleDateString()}</div>
                    </div>
                    <div class="transaction-amount ${amountClass}">
                        ${amountPrefix}$${Math.abs(amount).toFixed(2)}
                    </div>
                </div>
            `;
        });
        
        transactionsContainer.innerHTML = html;
    }

    // Setup event listeners
    function setupEventListeners() {
        if (!cardSelector) return;

        cardSelector.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            currentCard = JSON.parse(selectedOption.dataset.card);
            cardDetailsVisible = false;
            displayCurrentCard();
            updateCardInfo();
            
            // Load transactions for the selected card
            loadCardTransactions(currentCard.id);
            
            // Reload spending analytics for the selected card
            loadCardSpendingData();
        });

        if (freezeBtn) {
            freezeBtn.addEventListener('click', async function() {
                if (!currentCard) return;
                
                // First, refresh the card data to get the latest status
                await refreshCardData();
                
                // Determine action based on current card status from database
                const action = currentCard.status === 'frozen' ? 'unfreeze' : 'freeze';
                const url = `<?php echo SITE_URL; ?>/api/card-${action}.php`;
                
                console.log('❄️ FREEZE DEBUG: Card status after refresh:', currentCard.status, 'Action:', action);
                
                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({ card_id: currentCard.id })
                    });
                    const data = await response.json();
                    
                    console.log('❄️ FREEZE DEBUG: API response:', data);
                    
                    if (data.success) {
                        // Update the card status
                        currentCard.status = action === 'freeze' ? 'frozen' : 'active';
                        
                        // Update the button state
                        if (action === 'freeze') {
                            this.classList.add('frozen');
                            this.innerHTML = '<i class="fas fa-play"></i> Unfreeze Card';
                        } else {
                            this.classList.remove('frozen');
                            this.innerHTML = '<i class="fas fa-snowflake"></i> Freeze Card';
                        }
                        
                        // Update card info display
                        updateCardInfo();
                        
                        // Update visual freeze state
                        updateCardFreezeDisplay(action === 'freeze');
                        
                        // Show success message
                        showNotification(data.message, 'success');
                        
                        console.log('❄️ FREEZE DEBUG: Card updated successfully');
                    } else {
                        console.log('❄️ FREEZE DEBUG: API returned error:', data.message);
                        showNotification(data.message, 'error');
                    }
                } catch (error) {
                    console.error('❄️ FREEZE DEBUG: Error occurred:', error);
                    showNotification('An error occurred. Please try again.', 'error');
                }
            });
        }

        if (revealBtn) {
            revealBtn.addEventListener('click', function() {
                cardDetailsVisible = !cardDetailsVisible;
                displayCurrentCard();
                revealBtn.innerHTML = cardDetailsVisible ? 
                    '<i class="fas fa-eye-slash"></i> Hide Details' : 
                    '<i class="fas fa-eye"></i> Show Details';
            });
        }
    }

    // Setup card flip functionality
    function setupCardFlip() {
        const card = document.getElementById('creditCard');
        if (card) {
            card.addEventListener('click', function() {
                this.classList.toggle('is-flipped');
            });
        }
    }

    // This function is no longer needed as we use select dropdown
    // Keeping it for backward compatibility
    function changeChartView(view) {
        const select = document.getElementById('spendingPeriodSelect');
        if (select) {
            select.value = view === 'week' ? 'week' : view === 'month' ? 'month' : 'category';
            loadCardSpendingData();
        }
    }

    // Helper functions
    function formatNumber(num) {
        return parseFloat(num).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function formatExpiry(dateString) {
        const date = new Date(dateString);
        const month = ('0' + (date.getMonth() + 1)).slice(-2);
        const year = date.getFullYear().toString().slice(-2);
        return `${month}/${year}`;
    }

    function formatRelativeTime(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);

        if (diffMins < 1) return 'Just now';
        if (diffMins < 60) return `${diffMins}m ago`;
        if (diffHours < 24) return `${diffHours}h ago`;
        if (diffDays < 7) return `${diffDays}d ago`;
        return date.toLocaleDateString();
    }

    // Initialization is done in DOMContentLoaded above
</script>

<?php
include __DIR__ . '/../../includes/mobile-nav.php';
?>
