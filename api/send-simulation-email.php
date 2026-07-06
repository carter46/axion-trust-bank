<?php
/**
 * Send Simulation Email API
 * Handles sending simulation credit alert emails with customizable templates
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/email-template.php';

// Check admin authentication
requireAdmin();

// Set JSON header
header('Content-Type: application/json');

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
    exit;
}

try {
    // Validate required fields
    $errors = [];
    
    $recipientName = trim($input['recipient_name'] ?? '');
    $recipientEmail = trim($input['recipient_email'] ?? '');
    $recipientBank = trim($input['recipient_bank'] ?? '');
    $sender = trim($input['sender'] ?? '');
    $accountNumber = trim($input['account_number'] ?? '');
    $swiftCode = strtoupper(trim($input['swift_code'] ?? ''));
    $senderName = trim($input['sender_name'] ?? '');
    $senderAccount = trim($input['sender_account'] ?? '');
    $senderBank = trim($input['sender_bank'] ?? '');
    $footerContactMethod = trim($input['footer_contact_method'] ?? '');
    $footerContactValue = trim($input['footer_contact_value'] ?? '');
    $amount = $input['amount'] ?? '';
    $currency = strtoupper(trim($input['currency'] ?? ''));
    $transactionStatus = trim($input['transaction_status'] ?? '');
    $alertCaption = trim($input['alert_caption'] ?? '');
    $description = trim($input['description'] ?? '');
    $templateId = intval($input['template_id'] ?? 0);
    
    // Validation
    if (empty($recipientName)) {
        $errors[] = 'Recipient name is required';
    }
    
    if (empty($recipientEmail) || !filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid recipient email address is required';
    }
    
    if (!in_array($footerContactMethod, ['email', 'whatsapp'])) {
        $errors[] = 'Invalid footer contact method';
    }
    
    if (empty($footerContactValue)) {
        $errors[] = 'Footer contact value is required';
    }
    
    if ($footerContactMethod === 'email' && !filter_var($footerContactValue, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid footer email address';
    }
    
    if ($footerContactMethod === 'whatsapp' && !preg_match('/^\+[1-9]\d{1,14}$/', $footerContactValue)) {
        $errors[] = 'Invalid footer WhatsApp number format. Use international format (e.g., +1234567890)';
    }
    
    if (empty($amount) || !is_numeric($amount) || floatval($amount) <= 0) {
        $errors[] = 'Amount must be a positive number';
    }
    
    if (empty($currency) || strlen($currency) !== 3) {
        $errors[] = 'Invalid currency code';
    }
    
    // Validate currency against supported currencies
    require_once __DIR__ . '/../includes/currency.php';
    $currencyHelper = new Currency();
    $supportedCurrencies = $currencyHelper->getSupportedCurrencies();
    if (!isset($supportedCurrencies[$currency])) {
        $errors[] = 'Currency not supported';
    }
    
    // Map old "completed" to "successful" for backward compatibility
    if ($transactionStatus === 'completed') {
        $transactionStatus = 'successful';
    }
    if (!in_array($transactionStatus, ['successful', 'pending', 'processing', 'failed'])) {
        $errors[] = 'Invalid transaction status';
    }
    
    if (empty($alertCaption)) {
        $errors[] = 'Alert caption is required';
    }
    
    if (empty($description)) {
        $errors[] = 'Description is required';
    }
    
    if (empty($templateId)) {
        $errors[] = 'Template ID is required';
    }
    
    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $errors
        ]);
        exit;
    }
    // Load template data
    $db = Database::getInstance();
    $templateSql = "SELECT * FROM email_simulation_templates WHERE id = ? AND is_active = 1";
    $templateStmt = $db->query($templateSql, [$templateId]);
    $template = $templateStmt ? $templateStmt->fetch() : null;
    
    if (!$template) {
        echo json_encode([
            'success' => false,
            'message' => 'Template not found or inactive'
        ]);
        exit;
    }
    
    $templateType = $template['template_type'] ?? 'simple';
    $requiresAdvanced = $templateType === 'advanced';
    $isSimple = $templateType === 'simple';
    $templateSpecificErrors = [];
    
    if ($requiresAdvanced) {
        if (empty($accountNumber)) {
            $templateSpecificErrors[] = 'Account number is required for advanced templates';
        }
        if (empty($swiftCode) || strlen($swiftCode) < 8 || strlen($swiftCode) > 11) {
            $templateSpecificErrors[] = 'SWIFT code must be between 8 and 11 characters for advanced templates';
        }
        if (empty($recipientBank)) {
            $templateSpecificErrors[] = 'Recipient bank name is required for advanced templates';
        }
        $sender = ''; // Clear sender for advanced templates
    } else {
        // For simple templates, account number and SWIFT code are optional but can be shown if provided
        // Don't clear them - let them be shown if admin provides them
        if (empty($sender)) {
            $templateSpecificErrors[] = 'Sender is required for simple templates';
        }
    }
    
    if (!empty($templateSpecificErrors)) {
        echo json_encode([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $templateSpecificErrors
        ]);
        exit;
    }
    
    $siteName = $template['template_name'] ?? 'Simulation Template';
    
    // Normalize logo URL - ensure it's an absolute URL
    $logoUrl = $template['logo_url'] ?? null;
    if ($logoUrl) {
        $logoUrl = trim($logoUrl);
        // If it's not already an absolute URL, make it one
        if (!filter_var($logoUrl, FILTER_VALIDATE_URL)) {
            if (strpos($logoUrl, 'http') !== 0 && strpos($logoUrl, '//') !== 0) {
                $logoUrl = SITE_URL . '/' . ltrim($logoUrl, '/');
            }
        }
        // Final validation
        if (!filter_var($logoUrl, FILTER_VALIDATE_URL)) {
            $logoUrl = null; // Will fallback to site logo
        }
    }
    
    // Prepare template data
    $templateData = [
        'primary_color' => $template['primary_color'] ?? '#359eb4',
        'secondary_color' => $template['secondary_color'] ?? '#2a7e90',
        'accent_color' => $template['accent_color'] ?? '#10b981',
        'logo_url' => $logoUrl,
        'logo_alt_text' => $template['logo_alt_text'] ?? 'Bank Logo',
        'address' => $template['address'] ?? null,
        'template_type' => $templateType
    ];
    
    // Generate transaction reference
    $transactionRef = 'SIM-' . time() . '-' . rand(1000, 9999);
    
    // Calculate fake balance (amount * random multiplier between 2 and 10)
    $balance = floatval($amount) * rand(200, 1000) / 100;
    
    // Generate email using simulation method
    $emailTemplate = new EmailTemplate();
    // Get sender name for advanced templates
    $senderNameForEmail = '';
    if ($templateType === 'advanced') {
        $senderNameForEmail = $senderName ?? '';
    }
    
    $emailHtml = $emailTemplate->simulationCreditAlertEmail(
        $siteName,
        $recipientName,
        $amount,
        $currency,
        $alertCaption,
        $description,
        $transactionStatus,
        $templateData,
        $transactionRef,
        $balance,
        $accountNumber,
        $swiftCode,
        $footerContactMethod,
        $footerContactValue,
        $templateData['address'] ?? '',
        $sender,
        $senderNameForEmail,
        $recipientBank,
        $senderBank
    );
    
    // Email subject (escape to prevent header injection)
    $siteNameEscaped = str_replace(["\r", "\n"], '', $siteName);
    $alertCaptionEscaped = str_replace(["\r", "\n"], '', $alertCaption);
    $subject = "[{$siteNameEscaped}] Transaction Alert - Credit - {$alertCaptionEscaped}";
    
    // Generate receipt download URL
    $receiptParams = http_build_query([
        'ref' => $transactionRef,
        'template_id' => $templateId,
        'recipient_name' => $recipientName,
        'amount' => $amount,
        'currency' => $currency,
        'description' => $description,
        'status' => $transactionStatus,
        'account_number' => $accountNumber,
        'swift_code' => $swiftCode,
        'recipient_bank' => $recipientBank,
        'from' => $templateType === 'simple' && !empty($sender) ? $sender : 'Payment Received!',
        'sender_name' => $senderName,
        'sender_account' => $senderAccount,
        'sender_bank' => $senderBank,
        'template_type' => $templateType
    ]);
    $receiptUrl = SITE_URL . '/api/generate-simulation-receipt.php?' . $receiptParams;
    
    // Send email with custom "From" name for simulation emails
    // Note: Receipt link is NOT added to email - it will be shown on admin page instead
    $result = sendEmail($recipientEmail, $subject, $emailHtml, true, null, null, 'Payment Received!');
    
    if ($result) {
        // Log activity
        logActivity($_SESSION['user_id'], 'EMAIL_SIMULATION', "Sent simulation email to {$recipientEmail} using template: {$template['template_name']}");
        
        echo json_encode([
            'success' => true,
            'message' => "Simulation email sent successfully to {$recipientEmail}",
            'transaction_ref' => $transactionRef,
            'receipt_url' => $receiptUrl,
            'sent_via' => 'email'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to send email. Please check your SMTP configuration.'
        ]);
    }
    
} catch (Exception $e) {
    error_log('Send Simulation Email Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}

