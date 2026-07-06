<?php
/**
 * Generate Simulation Receipt PDF
 * Creates a downloadable receipt for simulation transactions
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Get parameters
$transactionRef = $_GET['ref'] ?? '';
$templateId = intval($_GET['template_id'] ?? 0);
$templateType = strtolower(trim($_GET['template_type'] ?? 'simple'));

// Validate template type
if (!in_array($templateType, ['simple', 'advanced'])) {
    $templateType = 'simple';
}

if (empty($transactionRef) || empty($templateId)) {
    http_response_code(400);
    die('Missing required parameters');
}

// Validate transaction ref format (should start with SIM-)
if (!preg_match('/^SIM-[0-9]+-[a-zA-Z0-9]+$/', $transactionRef)) {
    http_response_code(400);
    die('Invalid transaction reference format');
}

try {
    $db = Database::getInstance();
    
    // Get template data
    $templateSql = "SELECT * FROM email_simulation_templates WHERE id = ? AND is_active = 1";
    $templateStmt = $db->query($templateSql, [$templateId]);
    $template = $templateStmt ? $templateStmt->fetch() : null;
    
    if (!$template) {
        http_response_code(404);
        die('Template not found');
    }
    
    // Extract template colors
    $primaryColor = $template['primary_color'] ?? '#359eb4';
    $secondaryColor = $template['secondary_color'] ?? '#2a7e90';
    $accentColor = $template['accent_color'] ?? '#10b981';
    
    // Validate colors
    if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $primaryColor)) $primaryColor = '#359eb4';
    if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $secondaryColor)) $secondaryColor = '#2a7e90';
    if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $accentColor)) $accentColor = '#10b981';
    
    $siteName = htmlspecialchars($template['template_name'], ENT_QUOTES, 'UTF-8');
    $logoUrl = $template['logo_url'] ?? SITE_URL . '/assets/images/logo.svg';
    
    // Ensure logo URL is absolute
    if ($logoUrl && !filter_var($logoUrl, FILTER_VALIDATE_URL)) {
        if (strpos($logoUrl, 'http') !== 0) {
            $logoUrl = SITE_URL . '/' . ltrim($logoUrl, '/');
        }
    }
    if (!$logoUrl || !filter_var($logoUrl, FILTER_VALIDATE_URL)) {
        $logoUrl = SITE_URL . '/assets/images/logo.svg';
    }
    $logoUrlEscaped = htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8');
    
    // Get address from template or use system default
    $defaultAddress = '123 Banking Street, New York, NY 10001';
    if (class_exists('SystemSettings')) {
        try {
            $systemSettings = SystemSettings::getInstance();
            $defaultAddress = $systemSettings->get('bank_address', $defaultAddress);
        } catch (Exception $e) {
            // Use default if SystemSettings fails
        }
    }
    $address = htmlspecialchars($template['address'] ?? $defaultAddress, ENT_QUOTES, 'UTF-8');
    
    // Get transaction data from session or generate from ref
    // For simulation, we'll extract data from the transaction ref pattern
    // In a real scenario, you'd store this in a table
    
    // Parse transaction ref (format: SIM-timestamp-random)
    $parts = explode('-', $transactionRef);
    $timestamp = isset($parts[1]) ? intval($parts[1]) : time();
    $date = date('F j, Y g:i A', $timestamp);
    
    // For now, we'll use GET parameters or defaults
    $recipientName = htmlspecialchars($_GET['recipient_name'] ?? 'Customer', ENT_QUOTES, 'UTF-8');
    $recipientBank = htmlspecialchars($_GET['recipient_bank'] ?? '', ENT_QUOTES, 'UTF-8');
    $amount = floatval($_GET['amount'] ?? 0);
    $currency = strtoupper(trim($_GET['currency'] ?? 'USD'));
    $description = htmlspecialchars($_GET['description'] ?? 'Transaction', ENT_QUOTES, 'UTF-8');
    $statusRaw = trim($_GET['status'] ?? 'successful');
    $accountNumber = htmlspecialchars($_GET['account_number'] ?? '', ENT_QUOTES, 'UTF-8');
    $swiftCode = htmlspecialchars($_GET['swift_code'] ?? '', ENT_QUOTES, 'UTF-8');
    $fromValue = htmlspecialchars($_GET['from'] ?? 'Payment Received!', ENT_QUOTES, 'UTF-8');
    $senderName = htmlspecialchars($_GET['sender_name'] ?? '', ENT_QUOTES, 'UTF-8');
    $senderAccount = htmlspecialchars($_GET['sender_account'] ?? '', ENT_QUOTES, 'UTF-8');
    $senderBank = htmlspecialchars($_GET['sender_bank'] ?? '', ENT_QUOTES, 'UTF-8');
    
    // Validate status - map old "completed" to "successful" for backward compatibility
    if (strtolower($statusRaw) === 'completed') {
        $statusRaw = 'successful';
    }
    $validStatuses = ['successful', 'pending', 'processing', 'failed'];
    $status = in_array(strtolower($statusRaw), $validStatuses) ? strtolower($statusRaw) : 'successful';
    $status = htmlspecialchars($status, ENT_QUOTES, 'UTF-8');
    
    // Validate currency
    require_once __DIR__ . '/../includes/currency.php';
    $currencyHelper = new Currency();
    $supportedCurrencies = $currencyHelper->getSupportedCurrencies();
    if (!isset($supportedCurrencies[$currency]) || strlen($currency) !== 3) {
        $currency = 'USD'; // Default to USD if invalid
    }
    
    $formattedAmount = formatCurrency($amount, $currency, $currency);
    
    // Status colors
    $statusColors = [
        'successful' => '#10b981',  // Green
        'pending' => '#f59e0b',      // Yellow/Amber
        'processing' => '#f59e0b',   // Yellow/Amber (same as pending)
        'failed' => '#ef4444'        // Red
    ];
    // Map old "completed" to "successful" for backward compatibility
    if ($status === 'completed') {
        $status = 'successful';
    }
    $statusColor = $statusColors[$status] ?? '#10b981';
    
    // Title color based on status
    $titleColor = $statusColor;
    
    // Check if this is admin view (redirected from simulation test page)
    $isAdminView = isset($_GET['admin_view']) && $_GET['admin_view'] == '1';
    
    // Capitalize status for display and format as "Transaction [Status]"
    $statusDisplay = ucfirst($status);
    
    // Generate receipt content (without HTML wrapper for admin view)
    $receiptContent = <<<HTML
        <div class="receipt-container">
        <div class="receipt-header">
            <img src="{$logoUrlEscaped}" alt="Logo" class="bank-logo" onerror="this.style.display='none'">
            <div class="receipt-title">
                <h1 style="color: {$titleColor};">Transaction {$statusDisplay}</h1>
            </div>
        </div>
        
        <div class="amount-highlight">
            +{$formattedAmount}
        </div>
        
        <div class="receipt-info">
            <div class="info-row">
                <div class="info-label">Transaction Reference:</div>
                <div class="info-value">{$transactionRef}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Date & Time:</div>
                <div class="info-value">{$date}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Recipient:</div>
                <div class="info-value">{$recipientName}</div>
            </div>
HTML;
    
    if (!empty($recipientBank)) {
        $receiptContent .= "
            <div class=\"info-row\">
                <div class=\"info-label\">Recipient Bank:</div>
                <div class=\"info-value\">{$recipientBank}</div>
            </div>";
    }
    
    if (!empty($accountNumber)) {
        $receiptContent .= "
            <div class=\"info-row\">
                <div class=\"info-label\">Account Number:</div>
                <div class=\"info-value\">{$accountNumber}</div>
            </div>";
    }
    
    if (!empty($swiftCode)) {
        $receiptContent .= "
            <div class=\"info-row\">
                <div class=\"info-label\">SWIFT Code:</div>
                <div class=\"info-value\">{$swiftCode}</div>
            </div>";
    }
    
    // Add sender information for advanced templates only
    // Show sender section if template is advanced and at least one sender field is provided
    if ($templateType === 'advanced' && (!empty($senderName) || !empty($senderAccount) || !empty($senderBank))) {
        $receiptContent .= "
            <div class=\"info-row\" style=\"margin-top: 15px; padding-top: 15px; border-top: 2px solid #e0e0e0;\">
                <div class=\"info-label\" style=\"font-weight: 700; color: {$primaryColor};\">Sender Information:</div>
                <div class=\"info-value\"></div>
            </div>";
        
        // Always show sender name first if provided
        if (!empty($senderName)) {
            $receiptContent .= "
            <div class=\"info-row\">
                <div class=\"info-label\">Sender Name:</div>
                <div class=\"info-value\">{$senderName}</div>
            </div>";
        }
        
        // Then show sender bank if provided (after sender name)
        if (!empty($senderBank)) {
            $receiptContent .= "
            <div class=\"info-row\">
                <div class=\"info-label\">Sender Bank:</div>
                <div class=\"info-value\">{$senderBank}</div>
            </div>";
        }
        
        // Then show sender account if provided
        if (!empty($senderAccount)) {
            $receiptContent .= "
            <div class=\"info-row\">
                <div class=\"info-label\">Sender Account:</div>
                <div class=\"info-value\">{$senderAccount}</div>
            </div>";
        }
    }
    
    $receiptContent .= <<<HTML
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">
                    <span class="status-badge">{$status}</span>
                </div>
            </div>
        </div>
        
        <div class="description-box">
            <p>{$description}</p>
        </div>
        
        <div class="receipt-footer">
            <p><strong>{$siteName}</strong></p>
            <p class="address">{$address}</p>
            <p>This is an official transaction receipt</p>
            <p>Generated on {$date}</p>
            <p style="margin-top: 15px; color: #999;">This is a simulation receipt for testing purposes</p>
        </div>
    </div>
HTML;
    
    // Generate receipt styles
    $receiptStyles = <<<CSS
        <style>
        @media print {
            body { 
                margin: 0; 
                padding: 0;
                background: white;
            }
            .no-print { display: none !important; }
            .receipt-container {
                max-width: 100%;
                padding: 20px;
                box-shadow: none;
                margin: 0;
            }
            .receipt-header {
                page-break-inside: avoid;
            }
            .amount-highlight {
                page-break-inside: avoid;
            }
            .receipt-info {
                page-break-inside: avoid;
            }
            .description-box {
                page-break-inside: avoid;
            }
            @page {
                margin: 15mm;
                size: A4;
            }
        }
        .receipt-container * {
            box-sizing: border-box;
        }
        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: #333;
        }
        .receipt-header {
            text-align: center;
            padding-bottom: 20px;
            margin-bottom: 30px;
            border-bottom: 3px solid {$primaryColor};
        }
        .bank-logo {
            max-width: 300px;
            max-height: 90px;
            margin: 0 auto 15px auto;
            display: block;
        }
        .receipt-title {
            text-align: center;
        }
        .receipt-title h1 {
            font-size: 28px;
            color: {$titleColor};
            margin-bottom: 5px;
            font-weight: 700;
        }
        .receipt-title .subtitle {
            font-size: 14px;
            color: #666;
        }
        .receipt-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 4px solid {$accentColor};
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #666;
            width: 200px;
        }
        .info-value {
            text-align: right;
            color: #202124;
            font-weight: 600;
            flex: 1;
        }
        .amount-highlight {
            font-size: 32px;
            color: #10b981;
            font-weight: 700;
            text-align: center;
            padding: 20px;
            background: #f0fdf4;
            border-radius: 8px;
            margin: 20px 0;
            border: 2px solid #10b981;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            background: {$statusColor};
            color: white;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .description-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid {$secondaryColor};
        }
        .description-box p {
            margin: 0;
            color: #666;
            line-height: 1.6;
        }
        .receipt-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid {$secondaryColor};
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        .receipt-footer .address {
            margin: 10px 0;
            color: #666;
            line-height: 1.6;
        }
        .download-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: {$primaryColor};
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            z-index: 1000;
        }
        .download-btn:hover {
            background: {$secondaryColor};
            transform: translateY(-2px);
        }
         @media (max-width: 768px) {
             .receipt-container {
                 padding: 20px 15px;
                 max-width: 100%;
             }
             .receipt-title h1 {
                 font-size: 22px;
             }
             .amount-highlight {
                 font-size: 24px;
                 padding: 15px;
                 margin: 15px 0;
             }
             .receipt-info {
                 padding: 15px;
             }
             .info-row {
                 flex-direction: column;
                 padding: 8px 0;
             }
             .info-label {
                 width: 100%;
                 margin-bottom: 4px;
                 font-size: 13px;
             }
             .info-value {
                 text-align: left;
                 font-size: 14px;
             }
             .description-box {
                 padding: 15px;
             }
             .bank-logo {
                 max-width: 250px;
                 max-height: 75px;
             }
             .page-header h1 {
                 font-size: 24px !important;
             }
             .action-buttons {
                 width: 100%;
             }
             .action-buttons .btn {
                 flex: 1;
                 min-width: 0;
                 font-size: 13px;
                 padding: 10px 16px !important;
             }
         }
         @media (max-width: 480px) {
             .receipt-container {
                 padding: 15px 10px;
             }
             .receipt-title h1 {
                 font-size: 20px;
             }
             .amount-highlight {
                 font-size: 20px;
                 padding: 12px;
             }
             .info-label {
                 font-size: 12px;
             }
             .info-value {
                 font-size: 13px;
             }
             .page-header h1 {
                 font-size: 20px !important;
             }
             .action-buttons {
                 flex-direction: column;
             }
             .action-buttons .btn {
                 width: 100%;
             }
         }
        .receipt-container {
            background: white;
        }
        @media print {
            .no-print { display: none !important; }
            .receipt-container {
                box-shadow: none;
            }
        }
    </style>
CSS;
    
    // Output based on view type
    if ($isAdminView) {
        // For admin view, output admin layout with embedded receipt
        requireAdmin();
        
        $pageTitle = 'Simulation Receipt - Admin';
        include __DIR__ . '/../includes/head.php';
        include __DIR__ . '/../includes/admin-sidebar.php';
        
        echo '<div class="main-content-area">
            <div class="page-header" style="margin-bottom: 30px;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
                    <h1 style="font-size: 32px; font-weight: 700; color: ' . $titleColor . '; margin: 0;">Transaction ' . $statusDisplay . '</h1>
                    <div class="action-buttons" style="display: flex; gap: 12px; flex-wrap: wrap;">
                        <a href="' . SITE_URL . '/admin/email" class="btn btn-outline" style="padding: 12px 24px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                            <i class="fas fa-arrow-left"></i> Back to Email Management
                        </a>
                        <button onclick="downloadReceipt()" class="btn btn-primary" style="padding: 12px 24px; border-radius: 8px; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                            <i class="fas fa-download"></i> Download PDF
                        </button>
                    </div>
                </div>
            </div>
            <div style="padding: 0;">';
        
        // Output receipt styles
        echo $receiptStyles;
        
        // Output receipt content
        echo $receiptContent;
        
        // Include html2pdf library and script
        echo '<script src="' . SITE_URL . '/assets/js/html2pdf.bundle.min.js"></script>
        <script>
            function downloadReceipt() {
                const receiptContainer = document.querySelector(".receipt-container");
                
                if (!receiptContainer) {
                    alert("Receipt not found");
                    return;
                }
                
                const downloadBtn = document.querySelector("[onclick=\"downloadReceipt()\"]");
                const originalText = downloadBtn.innerHTML;
                downloadBtn.innerHTML = "<i class=\"fas fa-spinner fa-spin\"></i> Generating PDF...";
                downloadBtn.disabled = true;
                
                 const options = {
                     margin: [10, 10, 10, 10],
                     filename: "simulation_receipt_' . htmlspecialchars($transactionRef, ENT_QUOTES, 'UTF-8') . '.pdf",
                     image: { 
                         type: "jpeg", 
                         quality: 0.92 
                     },
                     html2canvas: { 
                         scale: 1.5,
                         useCORS: true,
                         allowTaint: false,
                         backgroundColor: "#ffffff",
                         logging: false,
                         letterRendering: false
                     },
                     jsPDF: { 
                         unit: "mm", 
                         format: "a4", 
                         orientation: "portrait"
                     }
                 };
                
                html2pdf().set(options).from(receiptContainer).save().then(() => {
                    downloadBtn.innerHTML = originalText;
                    downloadBtn.disabled = false;
                }).catch((error) => {
                    console.error("PDF generation failed:", error);
                    alert("Failed to generate PDF. Please try again.");
                    downloadBtn.innerHTML = originalText;
                    downloadBtn.disabled = false;
                });
            }
        </script>';
        
        echo '</div></div></div>';
        include __DIR__ . '/../includes/footer.php';
    } else {
        // For standalone view, output full HTML document
        header('Content-Type: text/html; charset=UTF-8');
        
        $siteUrl = SITE_URL; // For use in heredoc
        echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Receipt - {$transactionRef}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    {$receiptStyles}
</head>
<body>
    {$receiptContent}
    <script src="{$siteUrl}/assets/js/html2pdf.bundle.min.js"></script>
    <script>
        function downloadReceipt() {
            const receiptContainer = document.querySelector(".receipt-container");
            if (!receiptContainer) {
                alert("Receipt not found");
                return;
            }
             const options = {
                 margin: [10, 10, 10, 10],
                 filename: "simulation_receipt_{$transactionRef}.pdf",
                 image: { type: "jpeg", quality: 0.92 },
                 html2canvas: { scale: 1.5, useCORS: true, allowTaint: false, backgroundColor: "#ffffff", logging: false, letterRendering: false },
                 jsPDF: { unit: "mm", format: "a4", orientation: "portrait" }
             };
            html2pdf().set(options).from(receiptContainer).save().catch((error) => {
                console.error("PDF generation failed:", error);
                alert("Failed to generate PDF. Please try again.");
            });
        }
         // No auto-download - user can click button if needed
    </script>
</body>
</html>
HTML;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    error_log('Receipt Generation Error: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
    echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Error - Receipt Generation</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #f5f7fa;
            margin: 0;
            padding: 20px;
        }
        .error-container {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            max-width: 500px;
            text-align: center;
        }
        .error-icon {
            font-size: 48px;
            color: #ef4444;
            margin-bottom: 20px;
        }
        h1 {
            color: #202124;
            margin-bottom: 10px;
        }
        p {
            color: #666;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">⚠️</div>
        <h1>Error Generating Receipt</h1>
        <p>We encountered an error while generating your receipt. Please try again later.</p>
        <p style="margin-top: 20px; font-size: 14px; color: #999;">If this problem persists, please contact support.</p>
    </div>
</body>
</html>';
}

