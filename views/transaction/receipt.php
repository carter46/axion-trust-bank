<?php 
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/transfer-rails.php';
require_once __DIR__ . '/../../includes/head.php';

// Get user currency
$userModel = new User();
$user = $userModel->findById($_SESSION['user_id']);
$userCurrency = getUserDisplayCurrency($user);
$receiptCurrencyCode = strtoupper(getTransactionEntryCurrency($data['transaction'] ?? []));
if ($receiptCurrencyCode === getTransactionStoredCurrency($data['transaction'] ?? [])) {
    $receiptCurrencyCode = strtoupper($userCurrency);
}

// Get dynamic site name for branding
$siteName = getSiteName() ?? 'SecureBank Online';

// Determine if this is an international wire transfer
$isInternationalWire = ($data['transfer_type'] === 'international');
$metadata = $data['metadata'];
$receiptFields = $data['receipt_fields'] ?? [];
$receiptTitle = $data['receipt_title'] ?? getTransferReceiptTitle($data['transfer_type'], $metadata);
?>

<?php if ($isInternationalWire): ?>
<!-- International Wire Transfer Receipt (Bank of America Style) -->
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .receipt-container, .receipt-container * {
            visibility: visible;
        }
        .receipt-container {
            position: absolute;
            left: 0;
            top: 0;
            font-size: 11px !important;
            line-height: 1.2 !important;
            padding: 12px !important;
            max-width: 700px !important;
            width: 100% !important;
        }
        .no-print {
            display: none !important;
        }
        .action-buttons {
            display: none !important;
        }
        
        /* Force desktop layout for printing - override mobile responsive */
        .receipt-container .info-row {
            display: flex !important;
            flex-direction: row !important;
            justify-content: space-between !important;
            align-items: flex-start !important;
        }
        
        .receipt-container .info-label {
            width: 320px !important;
            flex-shrink: 0 !important;
        }
        
        .receipt-container .info-value {
            text-align: right !important;
            flex: 1 !important;
        }
        
        /* Force table to stay horizontal */
        .receipt-container table {
            width: 100% !important;
            table-layout: fixed !important;
        }
        
        .receipt-container th,
        .receipt-container td {
            width: 25% !important;
            word-wrap: break-word !important;
        }
        
        /* Apply compact layout for printing */
        .receipt-container .bank-header {
            margin-bottom: 8px !important;
            padding-bottom: 6px !important;
        }
        
        .receipt-container .bank-logo {
            font-size: 16px !important;
            margin-bottom: 4px !important;
        }
        
        .receipt-container .bank-name {
            font-size: 12px !important;
            margin-bottom: 4px !important;
        }
        
        .receipt-container .receipt-title {
            font-size: 12px !important;
            margin-bottom: 3px !important;
        }
        
        .receipt-container .subtitle {
            font-size: 10px !important;
        }
        
        .receipt-container .section {
            margin-bottom: 6px !important;
        }
        
        .receipt-container .section-title {
            font-size: 10px !important;
            margin-bottom: 5px !important;
            padding-bottom: 3px !important;
        }
        
        .receipt-container .info-row {
            margin-bottom: 4px !important;
        }
        
        .receipt-container .table-container {
            margin-top: 6px !important;
        }
        
        .receipt-container th,
        .receipt-container td {
            padding: 4px 5px !important;
            font-size: 9px !important;
        }
        
        .receipt-container th {
            font-size: 9px !important;
        }
        
        /* Override mobile responsive rules for printing */
        .receipt-container .receipt-info {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 30px !important;
        }
        
        .receipt-container .info-section {
            width: auto !important;
            margin-bottom: 0 !important;
        }
        
        /* Ensure sections stay in horizontal layout */
        .receipt-container .section {
            display: block !important;
            width: 100% !important;
        }
        
        /* Force horizontal table layout */
        .receipt-container .table-container {
            overflow: visible !important;
        }
        
        .receipt-container .table-container table {
            display: table !important;
        }
        
        .receipt-container .table-container thead {
            display: table-header-group !important;
        }
        
        .receipt-container .table-container tbody {
            display: table-row-group !important;
        }
        
        .receipt-container .table-container tr {
            display: table-row !important;
        }
        
        .receipt-container .table-container th,
        .receipt-container .table-container td {
            display: table-cell !important;
        }
    }

    .receipt-page {
        background-color: #f5f5f5;
        min-height: 100vh;
        padding: 40px 20px;
    }

    .receipt-container {
        width: 100%;
        max-width: 700px;
        background-color: white;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        padding: 15px;
        margin: 0 auto;
        font-size: 11px;
        line-height: 1.2;
    }

    .bank-header {
        text-align: right;
        margin-bottom: 10px;
        border-bottom: 1px solid #1e3a8a;
        padding-bottom: 8px;
    }

    .bank-logo {
        font-size: 18px;
        font-weight: 800;
        color: #1e3a8a;
        margin-bottom: 4px;
        letter-spacing: 1px;
    }
    
    .bank-logo img {
        max-height: 40px;
        max-width: 200px;
        object-fit: contain;
    }

    .bank-name {
        color: #1e3a8a;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .receipt-title {
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 3px;
        color: #1e293b;
    }

    .subtitle {
        font-size: 10px;
        color: #64748b;
        margin-bottom: 0;
    }

    .section-divider {
        border: none;
        border-top: 1px solid #e2e8f0;
        margin: 8px 0;
    }

    .section {
        margin-bottom: 8px;
    }

    .section-title {
        font-size: 11px;
        font-weight: 700;
        margin-bottom: 6px;
        color: #1e3a8a;
        padding-bottom: 3px;
        border-bottom: 1px solid #e2e8f0;
    }

    .info-row {
        display: flex;
        margin-bottom: 4px;
        align-items: flex-start;
    }

    .info-label {
        font-weight: 600;
        width: 320px;
        flex-shrink: 0;
        color: #475569;
    }

    .info-value {
        flex-grow: 1;
        color: #1e293b;
    }

    .amount-highlight {
        font-weight: 800;
        font-size: 20px;
        color: #1e3a8a;
    }


    .table-container {
        margin-top: 8px;
        overflow-x: auto;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        padding: 4px 6px;
        text-align: left;
        border: 1px solid #e2e8f0;
        vertical-align: top;
        font-size: 9px;
    }

    th {
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        font-weight: 700;
        font-size: 9px;
        color: #1e3a8a;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    td {
        font-size: 9px;
        color: #475569;
    }

    .action-buttons {
        display: flex;
        gap: 16px;
        margin-top: 40px;
        padding-top: 30px;
        border-top: 2px solid #e2e8f0;
        flex-wrap: wrap;
    }

    /* PDF-specific compact layout */
    .pdf-mode {
        font-size: 11px !important;
        line-height: 1.2 !important;
        padding: 12px !important;
    }

    .pdf-mode .action-buttons,
    .pdf-mode .pdf-hide {
        display: none !important;
    }

    /* Force desktop layout for PDF generation - override mobile responsive */
    .pdf-mode .info-row {
        display: flex !important;
        flex-direction: row !important;
        justify-content: space-between !important;
        align-items: flex-start !important;
    }

    .pdf-mode .info-label {
        width: 320px !important;
        flex-shrink: 0 !important;
    }

    .pdf-mode .info-value {
        text-align: right !important;
        flex: 1 !important;
    }

    .pdf-mode .receipt-info {
        display: grid !important;
        grid-template-columns: 1fr 1fr !important;
        gap: 30px !important;
    }

    .pdf-mode .info-section {
        width: auto !important;
        margin-bottom: 0 !important;
    }

    .pdf-mode .table-container {
        overflow: visible !important;
    }

    .pdf-mode table {
        width: 100% !important;
        table-layout: fixed !important;
    }

    .pdf-mode th,
    .pdf-mode td {
        width: 25% !important;
        word-wrap: break-word !important;
    }

    .pdf-mode .bank-header {
        margin-bottom: 8px !important;
        padding-bottom: 6px !important;
    }

    .pdf-mode .bank-logo {
        font-size: 16px !important;
        margin-bottom: 4px !important;
    }

    .pdf-mode .bank-name {
        font-size: 12px !important;
        margin-bottom: 4px !important;
    }

    .pdf-mode .receipt-title {
        font-size: 12px !important;
        margin-bottom: 3px !important;
    }

    .pdf-mode .subtitle {
        font-size: 10px !important;
    }

    .pdf-mode .section {
        margin-bottom: 6px !important;
    }

    .pdf-mode .section-title {
        font-size: 10px !important;
        margin-bottom: 5px !important;
        padding-bottom: 3px !important;
    }

    .pdf-mode .info-row {
        margin-bottom: 4px !important;
    }

    .pdf-mode .table-container {
        margin-top: 6px !important;
    }

    .pdf-mode th,
    .pdf-mode td {
        padding: 4px 5px !important;
        font-size: 9px !important;
    }

    .pdf-mode th {
        font-size: 9px !important;
    }

    .btn {
        padding: 14px 32px;
        border-radius: var(--border-radius);
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        font-size: 15px;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .btn-primary {
        background: #3b82f6;
        background: var(--gradient-primary, #3b82f6);
        color: white;
        border: 2px solid #3b82f6;
    }

    .btn-primary:hover {
        background: #2563eb;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .btn-success {
        background: #10b981;
        background: var(--gradient-success, #10b981);
        color: white;
        border: 2px solid #10b981;
    }

    .btn-success:hover {
        background: #059669;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    .btn-outline {
        background: transparent;
        color: #6b7280;
        border: 2px solid #d1d5db;
    }

    .btn-outline:hover {
        background: #f9fafb;
        color: #374151;
        border-color: #9ca3af;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(156, 163, 175, 0.3);
    }

    @media (max-width: 768px) {
        .receipt-container {
            padding: 24px;
        }

        .info-row {
            flex-direction: column;
            margin-bottom: 20px;
        }

        .info-label {
            width: 100%;
            margin-bottom: 6px;
            font-size: 13px;
        }

        .info-value {
            font-size: 15px;
        }

        table {
            font-size: 12px;
        }

        th, td {
            padding: 10px 12px;
        }

        .bank-logo {
            font-size: 24px;
        }

        .receipt-title {
            font-size: 18px;
        }

        .subtitle {
            font-size: 16px;
        }
    }

    @media (max-width: 480px) {
        .receipt-page {
            padding: 20px 10px;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="receipt-page">
    <div class="receipt-container">
        <!-- Bank Header -->
        <div class="bank-header">
            <div class="bank-logo">
                <img src="<?php echo getSiteLogo(); ?>" alt="<?php echo getSiteName(); ?>" style="max-height: 40px; max-width: 200px; object-fit: contain;">
            </div>
            <div class="bank-name"><?php echo getSiteName(); ?></div>
            <div class="receipt-title"><?php echo htmlspecialchars($receiptTitle); ?></div>
            <div class="subtitle">Official Transaction Receipt</div>
        </div>

        <!-- Transaction Details -->
        <div class="section">
            <div class="section-title">Transaction Details:</div>
            <div class="info-row">
                <div class="info-label">Date:</div>
                <div class="info-value"><?php echo date('F jS, Y', strtotime($data['transaction']['created_at'])); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Time:</div>
                <div class="info-value"><?php echo date('h:i A', strtotime($data['transaction']['created_at'])); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Transaction Reference Number:</div>
                <div class="info-value" style="font-weight: 700; color: #1e3a8a;"><?php echo htmlspecialchars($data['transaction']['transaction_ref']); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">
                    <span style="display: inline-block; padding: 4px 12px; border-radius: 20px; font-weight: 600; font-size: 13px;
                        <?php 
                        $status = $data['transaction']['status'];
                        if (isSuccessfulTransactionStatus($status)) {
                            echo 'background: #d1fae5; color: #065f46;';
                        } elseif ($status === 'pending' || $status === 'processing') {
                            echo 'background: #fef3c7; color: #78350f;';
                        } else {
                            echo 'background: #fee2e2; color: #991b1b;';
                        }
                        ?>">
                        <?php echo htmlspecialchars(formatTransactionStatusLabel($status)); ?>
                    </span>
                </div>
            </div>
        </div>

        <hr class="section-divider">

        <?php if ($data['transaction']['transaction_type'] === 'credit'): ?>
        <!-- Sender Information (for income transactions - where money is coming FROM) -->
        <div class="section">
            <div class="section-title">Sender Information:</div>
            <div class="info-row">
                <div class="info-label">Full Name:</div>
                <div class="info-value"><?php echo htmlspecialchars($data['transaction']['recipient_name'] ?? 'Unknown Sender'); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Account Number:</div>
                <div class="info-value"><?php echo htmlspecialchars($data['transaction']['recipient_account'] ?? 'N/A'); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Bank:</div>
                <div class="info-value"><?php echo htmlspecialchars($data['transaction']['recipient_bank'] ?? 'Unknown Bank'); ?></div>
            </div>
            <?php foreach ($receiptFields as $field): ?>
            <div class="info-row">
                <div class="info-label"><?php echo htmlspecialchars($field['label']); ?>:</div>
                <div class="info-value"><?php echo htmlspecialchars($field['value']); ?></div>
            </div>
            <?php endforeach; ?>
            <div class="info-row">
                <div class="info-label">Amount Received:</div>
                <div class="info-value amount-highlight"><?php echo formatTransactionReceiptAmount($data['transaction'], $user, 'amount'); ?></div>
            </div>
        </div>
        <?php else: ?>
        <!-- Sender Information (for outgoing transactions) -->
        <div class="section">
            <div class="section-title">Sender Information:</div>
            <div class="info-row">
                <div class="info-label">Full Name:</div>
                <div class="info-value"><?php echo htmlspecialchars($data['transaction']['user_name']); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Bank:</div>
                <div class="info-value"><?php echo htmlspecialchars($siteName); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Account Number:</div>
                <div class="info-value"><?php echo htmlspecialchars($data['transaction']['account_number']); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Account Type:</div>
                <div class="info-value"><?php echo ucfirst($data['transaction']['account_type']); ?> Account</div>
            </div>
        </div>

        <!-- Recipient Information (for outgoing transactions) -->
        <div class="section">
            <div class="section-title">Recipient Information:</div>
            <div class="info-row">
                <div class="info-label">Full Name:</div>
                <div class="info-value"><?php echo htmlspecialchars($data['transaction']['recipient_name'] ?? 'Unknown Recipient'); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Account Number:</div>
                <div class="info-value"><?php echo htmlspecialchars($data['transaction']['recipient_account'] ?? 'N/A'); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Bank:</div>
                <div class="info-value"><?php echo htmlspecialchars($data['transaction']['recipient_bank'] ?? 'Unknown Bank'); ?></div>
            </div>
            <?php if (!empty($metadata['country'])): ?>
            <div class="info-row">
                <div class="info-label">Country:</div>
                <div class="info-value"><?php echo htmlspecialchars($metadata['country']); ?></div>
            </div>
            <?php endif; ?>
            <?php foreach ($receiptFields as $field): ?>
            <div class="info-row">
                <div class="info-label"><?php echo htmlspecialchars($field['label']); ?>:</div>
                <div class="info-value"><?php echo htmlspecialchars($field['value']); ?></div>
            </div>
            <?php endforeach; ?>
            <div class="info-row">
                <div class="info-label">Transfer Amount:</div>
                <div class="info-value amount-highlight"><?php echo formatTransactionReceiptNet($data['transaction'], $user); ?></div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Detailed Table -->
        <div class="table-container">
            <?php if ($data['transaction']['transaction_type'] === 'credit'): ?>
            <!-- Income Transaction Table -->
            <table>
                <thead>
                    <tr>
                        <th>TRANSACTION DETAILS</th>
                        <th>AMOUNT RECEIVED</th>
                        <th>PURPOSE & CATEGORY</th>
                        <th>CONFIRMATION</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Amount Received: <?php echo formatTransactionReceiptAmount($data['transaction'], $user, 'amount'); ?></td>
                        <td>Currency: <?php echo htmlspecialchars($receiptCurrencyCode); ?></td>
                        <td rowspan="2"><?php echo !empty($data['transaction']['expense_category']) ? ucfirst(str_replace('_', ' ', $data['transaction']['expense_category'])) : 'Income'; ?></td>
                        <td rowspan="2">
                            Confirmation Number:<br>
                            <strong><?php echo htmlspecialchars($data['transaction']['transaction_ref']); ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td>Transaction Type: <?php echo ucfirst($data['transaction']['transaction_type']); ?></td>
                        <td>Current Balance: <?php echo formatTransactionBalanceForUser($data['transaction'], $user, 'balance_after'); ?></td>
                    </tr>
                    <tr>
                        <td>Source: <?php echo htmlspecialchars($data['transaction']['description']); ?></td>
                        <td>Account: <?php echo htmlspecialchars($data['transaction']['account_number']); ?></td>
                        <td>Category: <?php echo ucfirst($data['transaction']['category']); ?></td>
                        <td>Completed: NA</td>
                    </tr>
                </tbody>
            </table>
            <?php else: ?>
            <!-- Outgoing Transaction Table -->
            <table>
                <thead>
                    <tr>
                        <th>TRANSACTION DETAILS</th>
                        <th>FEE & CHARGES</th>
                        <th>PURPOSE & CATEGORY</th>
                        <th>CONFIRMATION</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Total Amount Sent: <?php echo formatTransactionReceiptNet($data['transaction'], $user); ?></td>
                        <td>Wire Transfer Fee: <?php echo formatTransactionReceiptAmount($data['transaction'], $user, 'fee'); ?></td>
                        <td rowspan="2"><?php echo !empty($data['transaction']['expense_category']) ? ucfirst(str_replace('_', ' ', $data['transaction']['expense_category'])) : 'General Transfer'; ?></td>
                        <td rowspan="2">
                            Confirmation Number:<br>
                            <strong><?php echo htmlspecialchars($data['transaction']['transaction_ref']); ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td>Currency: <?php echo htmlspecialchars($receiptCurrencyCode); ?></td>
                        <td>Total Deducted: <?php echo formatTransactionReceiptTotal($data['transaction'], $user); ?></td>
                    </tr>
                    <tr>
                        <td>Region: <?php echo htmlspecialchars($metadata['region'] ?? 'N/A'); ?></td>
                        <td>Balance Before: <?php echo formatTransactionBalanceForUser($data['transaction'], $user, 'balance_before'); ?></td>
                        <td><?php echo htmlspecialchars($data['transaction']['description']); ?></td>
                        <td>Expected Delivery: NA</td>
                    </tr>
                    <tr>
                        <td>Country: <?php echo htmlspecialchars($metadata['country'] ?? 'N/A'); ?></td>
                        <td>Balance After: <?php echo formatTransactionBalanceForUser($data['transaction'], $user, 'balance_after'); ?></td>
                        <td>Category: <?php echo ucfirst($data['transaction']['category']); ?></td>
                        <td>Completed: NA</td>
                    </tr>
                </tbody>
            </table>
            <?php endif; ?>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons no-print pdf-hide">
            <a href="<?php echo SITE_URL; ?>/transaction" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i>
                <span>Back to History</span>
            </a>
            <a href="<?php echo SITE_URL; ?>/dashboard" class="btn btn-primary">
                <i class="fas fa-home"></i>
                <span>Back Home</span>
            </a>
            <button class="btn btn-success" onclick="downloadReceipt()">
                <i class="fas fa-download"></i>
                <span>Download PDF</span>
            </button>
        </div>
    </div>
</div>

<?php else: ?>
<!-- Regular Transaction Receipt (For Domestic/Internal/Other) -->
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .receipt-container, .receipt-container * {
            visibility: visible;
        }
        .receipt-container {
            position: absolute;
            left: 0;
            top: 0;
            font-size: 11px !important;
            line-height: 1.2 !important;
            padding: 12px !important;
            max-width: 700px !important;
            width: 100% !important;
        }
        .no-print {
            display: none !important;
        }
        .action-buttons {
            display: none !important;
        }
        
        /* Force desktop layout for printing - override mobile responsive */
        .receipt-container .info-row {
            display: flex !important;
            flex-direction: row !important;
            justify-content: space-between !important;
            align-items: flex-start !important;
        }
        
        .receipt-container .info-label {
            width: 320px !important;
            flex-shrink: 0 !important;
        }
        
        .receipt-container .info-value {
            text-align: right !important;
            flex: 1 !important;
        }
        
        /* Force table to stay horizontal */
        .receipt-container table {
            width: 100% !important;
            table-layout: fixed !important;
        }
        
        .receipt-container th,
        .receipt-container td {
            width: 25% !important;
            word-wrap: break-word !important;
        }
        
        /* Apply compact layout for printing */
        .receipt-container .bank-header {
            margin-bottom: 8px !important;
            padding-bottom: 6px !important;
        }
        
        .receipt-container .bank-logo {
            font-size: 16px !important;
            margin-bottom: 4px !important;
        }
        
        .receipt-container .bank-name {
            font-size: 12px !important;
            margin-bottom: 4px !important;
        }
        
        .receipt-container .receipt-title {
            font-size: 12px !important;
            margin-bottom: 3px !important;
        }
        
        .receipt-container .subtitle {
            font-size: 10px !important;
        }
        
        .receipt-container .section {
            margin-bottom: 6px !important;
        }
        
        .receipt-container .section-title {
            font-size: 10px !important;
            margin-bottom: 5px !important;
            padding-bottom: 3px !important;
        }
        
        .receipt-container .info-row {
            margin-bottom: 4px !important;
        }
        
        .receipt-container .table-container {
            margin-top: 6px !important;
        }
        
        .receipt-container th,
        .receipt-container td {
            padding: 4px 5px !important;
            font-size: 9px !important;
        }
        
        .receipt-container th {
            font-size: 9px !important;
        }
        
        /* Override mobile responsive rules for printing */
        .receipt-container .receipt-info {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 30px !important;
        }
        
        .receipt-container .info-section {
            width: auto !important;
            margin-bottom: 0 !important;
        }
        
        /* Ensure sections stay in horizontal layout */
        .receipt-container .section {
            display: block !important;
            width: 100% !important;
        }
        
        /* Force horizontal table layout */
        .receipt-container .table-container {
            overflow: visible !important;
        }
        
        .receipt-container .table-container table {
            display: table !important;
        }
        
        .receipt-container .table-container thead {
            display: table-header-group !important;
        }
        
        .receipt-container .table-container tbody {
            display: table-row-group !important;
        }
        
        .receipt-container .table-container tr {
            display: table-row !important;
        }
        
        .receipt-container .table-container th,
        .receipt-container .table-container td {
            display: table-cell !important;
        }
    }

    .receipt-page {
        background-color: #f5f5f5;
        min-height: 100vh;
        padding: 40px 20px;
    }

    .receipt-container {
        width: 100%;
        max-width: 700px;
        background-color: white;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        padding: 15px;
        margin: 0 auto;
        font-size: 11px;
        line-height: 1.2;
    }

    .bank-header {
        text-align: right;
        margin-bottom: 10px;
        border-bottom: 1px solid #1e3a8a;
        padding-bottom: 8px;
    }

    .bank-logo {
        font-size: 18px;
        font-weight: 800;
        color: #1e3a8a;
        margin-bottom: 4px;
        letter-spacing: 1px;
    }
    
    .bank-logo img {
        max-height: 40px;
        max-width: 200px;
        object-fit: contain;
    }

    .bank-name {
        color: #1e3a8a;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .receipt-title {
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 3px;
        color: #1e293b;
    }

    .subtitle {
        font-size: 10px;
        color: #64748b;
        margin-bottom: 0;
    }

    .section-divider {
        border: none;
        border-top: 1px solid #e2e8f0;
        margin: 8px 0;
    }

    .section {
        margin-bottom: 8px;
    }

    .section-title {
        font-size: 11px;
        font-weight: 700;
        margin-bottom: 6px;
        color: #1e3a8a;
        padding-bottom: 3px;
        border-bottom: 1px solid #e2e8f0;
    }

    .info-row {
        display: flex;
        margin-bottom: 4px;
        align-items: flex-start;
    }

    .info-label {
        font-weight: 600;
        width: 320px;
        flex-shrink: 0;
        color: #475569;
    }

    .info-value {
        flex-grow: 1;
        color: #1e293b;
    }

    .amount-highlight {
        font-weight: 800;
        font-size: 20px;
        color: #1e3a8a;
    }

    .table-container {
        margin-top: 8px;
        overflow-x: auto;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        padding: 4px 6px;
        text-align: left;
        border: 1px solid #e2e8f0;
        vertical-align: top;
        font-size: 9px;
    }

    th {
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        font-weight: 700;
        font-size: 9px;
        color: #1e3a8a;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    td {
        font-size: 9px;
        color: #475569;
    }

    .action-buttons {
        display: flex;
        gap: 16px;
        margin-top: 40px;
        padding-top: 30px;
        border-top: 2px solid #e2e8f0;
        flex-wrap: wrap;
    }

    /* PDF-specific compact layout */
    .pdf-mode {
        font-size: 11px !important;
        line-height: 1.2 !important;
        padding: 12px !important;
    }

    .pdf-mode .action-buttons,
    .pdf-mode .pdf-hide {
        display: none !important;
    }

    /* Force desktop layout for PDF generation - override mobile responsive */
    .pdf-mode .info-row {
        display: flex !important;
        flex-direction: row !important;
        justify-content: space-between !important;
        align-items: flex-start !important;
    }

    .pdf-mode .info-label {
        width: 320px !important;
        flex-shrink: 0 !important;
    }

    .pdf-mode .info-value {
        text-align: right !important;
        flex: 1 !important;
    }

    .pdf-mode .receipt-info {
        display: grid !important;
        grid-template-columns: 1fr 1fr !important;
        gap: 30px !important;
    }

    .pdf-mode .info-section {
        width: auto !important;
        margin-bottom: 0 !important;
    }

    .pdf-mode .table-container {
        overflow: visible !important;
    }

    .pdf-mode table {
        width: 100% !important;
        table-layout: fixed !important;
    }

    .pdf-mode th,
    .pdf-mode td {
        width: 25% !important;
        word-wrap: break-word !important;
    }

    .pdf-mode .bank-header {
        margin-bottom: 8px !important;
        padding-bottom: 6px !important;
    }

    .pdf-mode .bank-logo {
        font-size: 16px !important;
        margin-bottom: 4px !important;
    }

    .pdf-mode .bank-name {
        font-size: 12px !important;
        margin-bottom: 4px !important;
    }

    .pdf-mode .receipt-title {
        font-size: 12px !important;
        margin-bottom: 3px !important;
    }

    .pdf-mode .subtitle {
        font-size: 10px !important;
    }

    .pdf-mode .section {
        margin-bottom: 6px !important;
    }

    .pdf-mode .section-title {
        font-size: 10px !important;
        margin-bottom: 5px !important;
        padding-bottom: 3px !important;
    }

    .pdf-mode .info-row {
        margin-bottom: 4px !important;
    }

    .pdf-mode .table-container {
        margin-top: 6px !important;
    }

    .pdf-mode th,
    .pdf-mode td {
        padding: 4px 5px !important;
        font-size: 9px !important;
    }

    .pdf-mode th {
        font-size: 9px !important;
    }

    .btn {
        padding: 14px 32px;
        border-radius: var(--border-radius);
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        font-size: 15px;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .btn-primary {
        background: #3b82f6;
        background: var(--gradient-primary, #3b82f6);
        color: white;
        border: 2px solid #3b82f6;
    }

    .btn-primary:hover {
        background: #2563eb;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .btn-success {
        background: #10b981;
        background: var(--gradient-success, #10b981);
        color: white;
        border: 2px solid #10b981;
    }

    .btn-success:hover {
        background: #059669;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    .btn-outline {
        background: transparent;
        color: #6b7280;
        border: 2px solid #d1d5db;
    }

    .btn-outline:hover {
        background: #f9fafb;
        color: #374151;
        border-color: #9ca3af;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(156, 163, 175, 0.3);
    }

    @media (max-width: 768px) {
        .receipt-container {
            padding: 24px;
        }

        .info-row {
            flex-direction: column;
            margin-bottom: 20px;
        }

        .info-label {
            width: 100%;
            margin-bottom: 6px;
            font-size: 13px;
        }

        .info-value {
            font-size: 15px;
        }

        table {
            font-size: 12px;
        }

        th, td {
            padding: 10px 12px;
        }

        .bank-logo {
            font-size: 24px;
        }

        .receipt-title {
            font-size: 18px;
        }

        .subtitle {
            font-size: 16px;
        }
    }

    @media (max-width: 480px) {
        .receipt-page {
            padding: 20px 10px;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="receipt-page">
    <div class="receipt-container">
        <!-- Bank Header -->
        <div class="bank-header">
            <div class="bank-logo">
                <img src="<?php echo getSiteLogo(); ?>" alt="<?php echo getSiteName(); ?>" style="max-height: 40px; max-width: 200px; object-fit: contain;">
            </div>
            <div class="bank-name"><?php echo getSiteName(); ?></div>
            <div class="receipt-title"><?php echo htmlspecialchars($receiptTitle); ?></div>
            <div class="subtitle">Official Transaction Receipt</div>
        </div>

        <!-- Transaction Details -->
        <div class="section">
            <div class="section-title">Transaction Details:</div>
            <div class="info-row">
                <div class="info-label">Date:</div>
                <div class="info-value"><?php echo date('F jS, Y', strtotime($data['transaction']['created_at'])); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Time:</div>
                <div class="info-value"><?php echo date('h:i A', strtotime($data['transaction']['created_at'])); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Transaction Reference Number:</div>
                <div class="info-value" style="font-weight: 700; color: #1e3a8a;"><?php echo htmlspecialchars($data['transaction']['transaction_ref']); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">
                    <span style="display: inline-block; padding: 4px 12px; border-radius: 20px; font-weight: 600; font-size: 13px;
                        <?php 
                        $status = $data['transaction']['status'];
                        if (isSuccessfulTransactionStatus($status)) {
                            echo 'background: #d1fae5; color: #065f46;';
                        } elseif ($status === 'pending' || $status === 'processing') {
                            echo 'background: #fef3c7; color: #78350f;';
                        } else {
                            echo 'background: #fee2e2; color: #991b1b;';
                        }
                        ?>">
                        <?php echo htmlspecialchars(formatTransactionStatusLabel($status)); ?>
                    </span>
                </div>
            </div>
        </div>

        <hr class="section-divider">

        <?php if ($data['transaction']['transaction_type'] === 'credit'): ?>
        <!-- Sender Information (for income transactions - where money is coming FROM) -->
        <div class="section">
            <div class="section-title">Sender Information:</div>
            <div class="info-row">
                <div class="info-label">Full Name:</div>
                <div class="info-value"><?php echo htmlspecialchars($data['transaction']['recipient_name'] ?? 'Unknown Sender'); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Account Number:</div>
                <div class="info-value"><?php echo htmlspecialchars($data['transaction']['recipient_account'] ?? 'N/A'); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Bank:</div>
                <div class="info-value"><?php echo htmlspecialchars($data['transaction']['recipient_bank'] ?? 'Unknown Bank'); ?></div>
            </div>
            <?php foreach ($receiptFields as $field): ?>
            <div class="info-row">
                <div class="info-label"><?php echo htmlspecialchars($field['label']); ?>:</div>
                <div class="info-value"><?php echo htmlspecialchars($field['value']); ?></div>
            </div>
            <?php endforeach; ?>
            <div class="info-row">
                <div class="info-label">Amount Received:</div>
                <div class="info-value amount-highlight"><?php echo formatTransactionReceiptAmount($data['transaction'], $user, 'amount'); ?></div>
            </div>
        </div>

        <?php else: ?>
        <!-- Sender Information (for outgoing transactions) -->
        <div class="section">
            <div class="section-title">Sender Information:</div>
            <div class="info-row">
                <div class="info-label">Full Name:</div>
                <div class="info-value"><?php echo htmlspecialchars($data['transaction']['user_name']); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Bank:</div>
                <div class="info-value"><?php echo htmlspecialchars($siteName); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Account Number:</div>
                <div class="info-value"><?php echo htmlspecialchars($data['transaction']['account_number']); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Account Type:</div>
                <div class="info-value"><?php echo ucfirst($data['transaction']['account_type']); ?> Account</div>
            </div>
        </div>

        <!-- Recipient Information (for outgoing transactions) -->
        <div class="section">
            <div class="section-title">Recipient Information:</div>
            <div class="info-row">
                <div class="info-label">Full Name:</div>
                <div class="info-value"><?php echo htmlspecialchars($data['transaction']['recipient_name'] ?? 'Unknown Recipient'); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Account Number:</div>
                <div class="info-value"><?php echo htmlspecialchars($data['transaction']['recipient_account'] ?? 'N/A'); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Bank:</div>
                <div class="info-value"><?php echo htmlspecialchars($data['transaction']['recipient_bank'] ?? 'Unknown Bank'); ?></div>
            </div>
            <?php foreach ($receiptFields as $field): ?>
            <div class="info-row">
                <div class="info-label"><?php echo htmlspecialchars($field['label']); ?>:</div>
                <div class="info-value"><?php echo htmlspecialchars($field['value']); ?></div>
            </div>
            <?php endforeach; ?>
            <div class="info-row">
                <div class="info-label">Transfer Amount:</div>
                <div class="info-value amount-highlight"><?php echo formatTransactionReceiptNet($data['transaction'], $user); ?></div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Detailed Table -->
        <div class="table-container">
            <?php if ($data['transaction']['transaction_type'] === 'credit'): ?>
            <!-- Income Transaction Table -->
            <table>
                <thead>
                    <tr>
                        <th>TRANSACTION DETAILS</th>
                        <th>AMOUNT RECEIVED</th>
                        <th>PURPOSE & CATEGORY</th>
                        <th>CONFIRMATION</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Amount Received: <?php echo formatTransactionReceiptAmount($data['transaction'], $user, 'amount'); ?></td>
                        <td>Currency: <?php echo htmlspecialchars($receiptCurrencyCode); ?></td>
                        <td rowspan="2"><?php echo !empty($data['transaction']['expense_category']) ? ucfirst(str_replace('_', ' ', $data['transaction']['expense_category'])) : 'Income'; ?></td>
                        <td rowspan="2">
                            Confirmation Number:<br>
                            <strong><?php echo htmlspecialchars($data['transaction']['transaction_ref']); ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td>Transaction Type: <?php echo ucfirst($data['transaction']['transaction_type']); ?></td>
                        <td>Current Balance: <?php echo formatTransactionBalanceForUser($data['transaction'], $user, 'balance_after'); ?></td>
                    </tr>
                    <tr>
                        <td>Source: <?php echo htmlspecialchars($data['transaction']['description']); ?></td>
                        <td>Account: <?php echo htmlspecialchars($data['transaction']['account_number']); ?></td>
                        <td>Category: <?php echo ucfirst($data['transaction']['category']); ?></td>
                        <td>Completed: NA</td>
                    </tr>
                </tbody>
            </table>
            <?php else: ?>
            <!-- Outgoing Transaction Table -->
            <table>
                <thead>
                    <tr>
                        <th>TRANSACTION DETAILS</th>
                        <th>FEE & CHARGES</th>
                        <th>PURPOSE & CATEGORY</th>
                        <th>CONFIRMATION</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Total Amount Sent: <?php echo formatTransactionReceiptNet($data['transaction'], $user); ?></td>
                        <td>Transfer Fee: <?php echo formatTransactionReceiptAmount($data['transaction'], $user, 'fee'); ?></td>
                        <td rowspan="2"><?php echo !empty($data['transaction']['expense_category']) ? ucfirst(str_replace('_', ' ', $data['transaction']['expense_category'])) : 'General Transfer'; ?></td>
                        <td rowspan="2">
                            Confirmation Number:<br>
                            <strong><?php echo htmlspecialchars($data['transaction']['transaction_ref']); ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td>Currency: <?php echo htmlspecialchars($receiptCurrencyCode); ?></td>
                        <td>Total Deducted: <?php echo formatTransactionReceiptTotal($data['transaction'], $user); ?></td>
                    </tr>
                    <tr>
                        <td>Transfer Type: <?php echo ucfirst($data['transfer_type']); ?></td>
                        <td>Balance Before: <?php echo formatTransactionBalanceForUser($data['transaction'], $user, 'balance_before'); ?></td>
                        <td><?php echo htmlspecialchars($data['transaction']['description']); ?></td>
                        <td>Expected Delivery: NA</td>
                    </tr>
                    <tr>
                        <td>Category: <?php echo ucfirst($data['transaction']['category']); ?></td>
                        <td>Balance After: <?php echo formatTransactionBalanceForUser($data['transaction'], $user, 'balance_after'); ?></td>
                        <td>Transaction Type: <?php echo ucfirst($data['transaction']['transaction_type']); ?></td>
                        <td>Completed: NA</td>
                    </tr>
                </tbody>
            </table>
            <?php endif; ?>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons no-print pdf-hide">
            <a href="<?php echo SITE_URL; ?>/transaction" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i>
                <span>Back to History</span>
            </a>
            <a href="<?php echo SITE_URL; ?>/dashboard" class="btn btn-primary">
                <i class="fas fa-home"></i>
                <span>Back Home</span>
            </a>
            <button class="btn btn-success" onclick="downloadReceipt()">
                <i class="fas fa-download"></i>
                <span>Download PDF</span>
            </button>
        </div>
    </div>
</div>

<?php endif; ?>

<!-- Include html2pdf.js -->
<script src="<?php echo SITE_URL; ?>/assets/js/html2pdf.bundle.min.js"></script>

<script>
    // Auto-download PDF if shared
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('share') === '1') {
            // Hide action buttons for shared view
            const actionButtons = document.querySelector('.action-buttons');
            if (actionButtons) {
                actionButtons.style.display = 'none';
            }
            
            // Auto-download PDF after a short delay
            setTimeout(() => {
                downloadReceipt();
            }, 1000);
        }
        
        // Hide action buttons for PDF generation
        if (urlParams.get('pdf') === '1') {
            const actionButtons = document.querySelector('.action-buttons');
            if (actionButtons) {
                actionButtons.style.display = 'none';
            }
        }
    });

    function downloadReceipt() {
        // Get the receipt container
        const receiptContainer = document.querySelector('.receipt-container');
        
        if (!receiptContainer) {
            alert('Receipt not found');
            return;
        }
        
        // Debug: Log container info
        console.log('Receipt container found:', receiptContainer);
        console.log('Container height:', receiptContainer.offsetHeight);
        console.log('Container content:', receiptContainer.innerHTML.substring(0, 200));
        
        // Get transaction ID for filename
        const urlParams = new URLSearchParams(window.location.search);
        const transactionId = urlParams.get('id') || 'receipt';
        
        // Configure PDF options
        const options = {
            margin: [10, 10, 10, 10],
            filename: `transfer_receipt_${transactionId}.pdf`,
            image: { 
                type: 'jpeg', 
                quality: 0.98 
            },
            html2canvas: { 
                scale: 2,
                useCORS: true,
                allowTaint: true,
                backgroundColor: '#ffffff',
                logging: false,
                letterRendering: true
            },
            jsPDF: { 
                unit: 'mm', 
                format: 'a4', 
                orientation: 'portrait'
            }
        };
        
        // Show loading message
        const downloadBtn = document.querySelector('[onclick="downloadReceipt()"]');
        const originalText = downloadBtn.innerHTML;
        downloadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating PDF...';
        downloadBtn.disabled = true;
        
        // Add PDF-specific class for more compact layout
        receiptContainer.classList.add('pdf-mode');
        
        // Wait a moment for CSS changes to apply
        setTimeout(() => {
            // Generate and download PDF
            html2pdf().set(options).from(receiptContainer).save().then(() => {
                // Remove PDF class
                receiptContainer.classList.remove('pdf-mode');
                // Reset button
                downloadBtn.innerHTML = originalText;
                downloadBtn.disabled = false;
            }).catch((error) => {
                console.error('PDF generation failed:', error);
                // Remove PDF class
                receiptContainer.classList.remove('pdf-mode');
                alert('Failed to generate PDF. Please try again.');
                // Reset button
                downloadBtn.innerHTML = originalText;
                downloadBtn.disabled = false;
            });
        }, 100); // Wait 100ms for CSS changes to apply
    }
    
    // Enhanced print function for better PDF generation
    function printReceipt() {
        const receiptContainer = document.querySelector('.receipt-container');
        
        if (!receiptContainer) {
            alert('Receipt not found');
            return;
        }
        
        // Add PDF-specific class for compact layout
        receiptContainer.classList.add('pdf-mode');
        
        // Hide action buttons during print
        const actionButtons = document.querySelectorAll('.action-buttons, .no-print');
        actionButtons.forEach(btn => btn.style.display = 'none');
        
        // Wait a moment for CSS changes to apply, then print
        setTimeout(() => {
            window.print();
            
            // Clean up after printing
            setTimeout(() => {
                receiptContainer.classList.remove('pdf-mode');
                actionButtons.forEach(btn => btn.style.display = '');
            }, 1000);
        }, 100);
    }
</script>

</body>
</html>

