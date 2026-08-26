<?php 
$pageTitle = 'Transfer Status - ' . SITE_NAME;
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

requireLogin();

// Get user currency preference
$userModel = new User();
$userInfo = $userModel->findById($_SESSION['user_id']);
$user = $userInfo;
$userCurrency = getUserDisplayCurrency($user);

// Get transaction ID from URL
$transactionId = isset($_GET['id']) ? Security::sanitize($_GET['id']) : null;

if (!$transactionId) {
    header('Location: ' . SITE_URL . '/transfer');
    exit;
}

// Fetch transaction details
$db = Database::getInstance();
$stmt = $db->query(
    "SELECT t.*, a.account_name as from_account_name, a.account_number as from_account_number
     FROM transactions t
     LEFT JOIN accounts a ON t.account_id = a.id
     WHERE t.transaction_ref = ? AND t.user_id = ?",
    [$transactionId, $_SESSION['user_id']]
);

$transaction = $stmt->fetch();

if (!$transaction) {
    header('Location: ' . SITE_URL . '/transfer');
    exit;
}

// Determine status
$status = strtolower($transaction['status']);
$isSuccess = isSuccessfulTransactionStatus($status);
$statusClass = 'status-' . ($isSuccess ? 'success' : ($status === 'failed' ? 'failed' : 'pending'));
$statusIcon = $isSuccess ? 'check' : ($status === 'failed' ? 'times' : 'clock');
$statusTitle = $isSuccess ? 'Transfer Successful' : ($status === 'failed' ? 'Transfer Failed' : 'Transfer Pending');
$statusText = $isSuccess
    ? ($status === 'completed' ? 'Completed' : 'Successful')
    : ($status === 'failed' ? 'Failed' : 'Pending');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $statusTitle; ?> - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/fontawesome/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            background-color: white;
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: var(--status-color, #4CAF50);
        }

        .success-animation {
            margin: 0 auto 30px;
            position: relative;
            width: 150px;
            height: 150px;
        }

        .circle {
            position: absolute;
            top: 0;
            left: 0;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: var(--status-color, #4CAF50);
            animation: scaleIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
        }

        .status-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0);
            font-size: 60px;
            color: white;
            animation: iconIn 0.5s ease-out 0.6s forwards;
        }

        .confetti {
            position: absolute;
            width: 12px;
            height: 12px;
            background-color: #ffd700;
            border-radius: 50%;
            opacity: 0;
        }

        .status-success .confetti {
            animation: confetti 1.5s ease-out 0.8s forwards;
        }

        .confetti:nth-child(1) { top: -5px; left: 30px; animation-delay: 0.8s; background-color: #ff6b6b; }
        .confetti:nth-child(2) { top: 10px; right: 20px; animation-delay: 1s; background-color: #4ecdc4; }
        .confetti:nth-child(3) { bottom: 20px; left: 10px; animation-delay: 1.2s; background-color: #ffd166; }
        .confetti:nth-child(4) { bottom: -5px; right: 30px; animation-delay: 1.4s; background-color: #06d6a0; }
        .confetti:nth-child(5) { top: 40px; left: -5px; animation-delay: 1.1s; background-color: #118ab2; }
        .confetti:nth-child(6) { top: -5px; right: 50px; animation-delay: 0.9s; background-color: #ef476f; }

        .sparkle {
            position: absolute;
            width: 8px;
            height: 8px;
            background-color: white;
            border-radius: 50%;
            opacity: 0;
        }

        .status-success .sparkle {
            animation: sparkle 1.2s ease-in-out infinite;
        }

        .sparkle:nth-child(7) { top: 20px; left: 20px; animation-delay: 0.2s; }
        .sparkle:nth-child(8) { top: 60px; right: 25px; animation-delay: 0.5s; }
        .sparkle:nth-child(9) { bottom: 30px; left: 40px; animation-delay: 0.8s; }
        .sparkle:nth-child(10) { bottom: 50px; right: 15px; animation-delay: 0.3s; }

        @keyframes scaleIn {
            0% {
                transform: scale(0);
                opacity: 0;
            }
            70% {
                transform: scale(1.1);
                opacity: 1;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes iconIn {
            0% {
                transform: translate(-50%, -50%) scale(0);
                opacity: 0;
            }
            50% {
                transform: translate(-50%, -50%) scale(1.2);
                opacity: 1;
            }
            100% {
                transform: translate(-50%, -50%) scale(1);
                opacity: 1;
            }
        }

        @keyframes confetti {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            100% {
                transform: translateY(100px) rotate(360deg);
                opacity: 0;
            }
        }

        @keyframes sparkle {
            0%, 100% {
                opacity: 0;
                transform: scale(0.5);
            }
            50% {
                opacity: 1;
                transform: scale(1.2);
            }
        }

        h1 {
            color: var(--status-color, #4CAF50);
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .amount {
            font-size: 42px;
            font-weight: 800;
            color: #333;
            margin: 20px 0;
            position: relative;
            display: inline-block;
        }

        .amount::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 10%;
            width: 80%;
            height: 3px;
            background: linear-gradient(90deg, transparent, var(--status-color, #4CAF50), transparent);
        }

        .recipient {
            color: #555;
            font-size: 20px;
            margin-bottom: 30px;
        }

        .recipient span {
            font-weight: 700;
            color: #333;
        }

        .help-link {
            color: #6c757d;
            text-decoration: none;
            font-size: 17px;
            margin-bottom: 40px;
            display: inline-block;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .help-link:hover {
            color: var(--status-color, #4CAF50);
            text-decoration: underline;
            transform: translateY(-2px);
        }

        .buttons {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .button {
            padding: 18px 24px;
            border-radius: 12px;
            font-size: 17px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
        }

        .button-primary {
            background: var(--status-color, #4CAF50);
            color: white;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
        }

        .button-primary:hover {
            background: var(--status-color-dark, #3d8b40);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
        }

        .button-secondary {
            background-color: white;
            color: var(--status-color, #4CAF50);
            border: 2px solid var(--status-color, #4CAF50);
        }

        .button-secondary:hover {
            background-color: rgba(76, 175, 80, 0.05);
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .transaction-details {
            background-color: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
            text-align: left;
        }

        .transaction-details h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #666;
            font-weight: 500;
        }

        .detail-value {
            color: #333;
            font-weight: 600;
            text-align: right;
        }

        /* Desktop styles */
        @media (min-width: 768px) {
            .container {
                max-width: 500px;
                padding: 50px 40px;
            }

            .buttons {
                flex-direction: row;
                justify-content: space-between;
            }

            .button {
                flex: 1;
                margin: 0 10px;
            }
        }

        /* Status-specific styles */
        .status-success {
            --status-color: #4CAF50;
            --status-color-dark: #3d8b40;
        }

        .status-failed {
            --status-color: #f44336;
            --status-color-dark: #d32f2f;
        }

        .status-pending {
            --status-color: #FFC107;
            --status-color-dark: #FFA000;
        }
    </style>
    <?php include __DIR__ . '/../../includes/translation.php'; ?>
</head>
<body>
    <div class="container <?php echo $statusClass; ?>">
        <?php include __DIR__ . '/../../includes/restricted-banner.php'; ?>
        <div class="success-animation">
            <div class="circle"></div>
            <div class="status-icon">
                <i class="fas fa-<?php echo $statusIcon; ?>"></i>
            </div>
            <div class="confetti"></div>
            <div class="confetti"></div>
            <div class="confetti"></div>
            <div class="confetti"></div>
            <div class="confetti"></div>
            <div class="confetti"></div>
            <div class="sparkle"></div>
            <div class="sparkle"></div>
            <div class="sparkle"></div>
            <div class="sparkle"></div>
        </div>
        
        <h1><?php echo $statusTitle; ?></h1>
        
        <div class="amount"><?php echo formatTransactionReceiptTotal($transaction, $user); ?></div>
        
        <div class="recipient">to <span><?php echo htmlspecialchars($transaction['recipient_name']); ?></span></div>
        
        <div class="transaction-details">
            <h3><i class="fas fa-receipt"></i> Transaction Details</h3>
            <div class="detail-row">
                <span class="detail-label">Transaction ID</span>
                <span class="detail-value"><?php echo htmlspecialchars($transaction['transaction_ref']); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Date & Time</span>
                <span class="detail-value"><?php echo date('M d, Y · h:i A', strtotime($transaction['created_at'])); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Transfer Type</span>
                <span class="detail-value"><?php echo ucfirst($transaction['transaction_type']); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">From Account</span>
                <span class="detail-value"><?php echo htmlspecialchars($transaction['from_account_name'] ?? 'N/A'); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status</span>
                <span class="detail-value" style="color: var(--status-color);"><?php echo $statusText; ?></span>
            </div>
        </div>
        
        <a href="<?php echo SITE_URL; ?>/help-center" class="help-link"><i class="fas fa-question-circle"></i> Need Help?</a>
        
        <div class="buttons">
            <button class="button button-secondary" onclick="shareReceipt()">
                <i class="fas fa-share-alt"></i> Share Receipt
            </button>
            <a href="<?php echo SITE_URL; ?>/transaction?id=<?php echo urlencode($transaction['transaction_ref']); ?>" class="button button-primary">
                <i class="fas fa-eye"></i> View Receipt
            </a>
        </div>
    </div>

    <script src="<?php echo SITE_URL; ?>/assets/js/html2canvas.min.js"></script>
    <script src="<?php echo SITE_URL; ?>/assets/js/jspdf.umd.min.js"></script>
    <script>
        async function shareReceipt() {
            try {
                // Show loading state
                const shareButton = document.querySelector('.button-secondary');
                const originalText = shareButton.innerHTML;
                shareButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating PDF...';
                shareButton.disabled = true;
                
                // Create iframe to load the actual receipt page
                const iframe = document.createElement('iframe');
                iframe.style.cssText = `
                    position: absolute;
                    left: -9999px;
                    top: -9999px;
                    width: 210mm;
                    height: 297mm;
                    border: none;
                `;
                
                // Load the receipt page with pdf=1 parameter to hide action buttons
                const receiptUrl = `<?php echo SITE_URL; ?>/transaction?id=<?php echo urlencode($transaction['transaction_ref']); ?>&pdf=1`;
                iframe.src = receiptUrl;
                
                document.body.appendChild(iframe);
                
                iframe.onload = async () => {
                    try {
                        // Wait for content to load
                        await new Promise(resolve => setTimeout(resolve, 2000));
                        
                        // Get the receipt container from iframe
                        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                        const receiptContainer = iframeDoc.querySelector('.receipt-container');
                        
                        if (!receiptContainer) {
                            throw new Error('Receipt container not found in iframe');
                        }
                        
                        console.log('Found receipt container:', receiptContainer);
                        
                        // Wait for images and fonts to load completely
                        await new Promise(resolve => setTimeout(resolve, 1000));
                        
                        // Use html2canvas to capture the element (WordPress approach)
                        const canvas = await html2canvas(receiptContainer, {
                            scale: 2,
                            useCORS: true,
                            allowTaint: true,
                            backgroundColor: '#FFFFFF',
                            logging: true, // Enable logging for debugging
                            scrollX: 0,
                            scrollY: 0,
                            imageTimeout: 15000 // Increase timeout for images
                        });
                        
                        // Convert canvas to data URL
                        const imgData = canvas.toDataURL('image/png', 1.0);
                        const pdfWidth = (canvas.width * 0.264583); // Convert px to mm
                        const pdfHeight = (canvas.height * 0.264583);
                        
                        // Create PDF using jsPDF (exact WordPress approach)
                        const { jsPDF } = window.jspdf;
                        const pdf = new jsPDF({
                            orientation: pdfWidth > pdfHeight ? 'landscape' : 'portrait',
                            unit: 'mm',
                            format: [pdfWidth, pdfHeight]
                        });
                        
                        // Add image to PDF
                        pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
                        
                        // Generate PDF blob
                        const pdfBlob = pdf.output('blob');
                        
                        // Clean up
                        document.body.removeChild(iframe);
                        
                        // Reset button
                        shareButton.innerHTML = originalText;
                        shareButton.disabled = false;
                        
                        // Try native sharing with PDF file
                        try {
            if (navigator.share) {
                                const pdfFile = new File([pdfBlob], `transfer-receipt-<?php echo htmlspecialchars($transaction['transaction_ref']); ?>.pdf`, {
                                    type: 'application/pdf'
                                });
                                
                                await navigator.share({
                                    title: 'Transfer Receipt - <?php echo htmlspecialchars($transaction['transaction_ref']); ?>',
                                    text: 'Transfer receipt for <?php echo formatTransactionReceiptTotal($transaction, $user); ?> to <?php echo htmlspecialchars($transaction['recipient_name']); ?>',
                                    files: [pdfFile]
                                });
            } else {
                                throw new Error('Native sharing not supported');
                            }
                        } catch (shareError) {
                            console.log('Native sharing failed, falling back to download:', shareError);
                            // Fallback: Download the PDF
                            const url = URL.createObjectURL(pdfBlob);
                            const a = document.createElement('a');
                            a.href = url;
                            a.download = `transfer-receipt-<?php echo htmlspecialchars($transaction['transaction_ref']); ?>.pdf`;
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);
                            URL.revokeObjectURL(url);
                            
                            alert('Receipt PDF downloaded! You can now share the downloaded file.');
                        }
                        
                    } catch (error) {
                        document.body.removeChild(iframe);
                        throw error;
                    }
                };
                
                iframe.onerror = () => {
                    document.body.removeChild(iframe);
                    throw new Error('Failed to load receipt page');
                };
                
            } catch (error) {
                console.error('Error sharing receipt:', error);
                alert('Error generating receipt PDF. Please try again.');
                
                // Reset button
                const shareButton = document.querySelector('.button-secondary');
                shareButton.innerHTML = '<i class="fas fa-share-alt"></i> Share Receipt';
                shareButton.disabled = false;
            }
        }

        // Add pulse animation to amount
        document.addEventListener('DOMContentLoaded', function() {
            const amount = document.querySelector('.amount');
            setInterval(() => {
                amount.style.transform = 'scale(1.02)';
                setTimeout(() => {
                    amount.style.transform = 'scale(1)';
                }, 300);
            }, 3000);
        });
    </script>
</body>
</html>

