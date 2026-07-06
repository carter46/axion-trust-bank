<?php
// Prevent output before JSON
error_reporting(0);
ini_set('display_errors', 0);

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set JSON header FIRST
header('Content-Type: application/json');

// Catch ALL output
ob_start();

try {
    // Check authentication
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
    
    // Get input
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);
    
    if (!$input) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
        exit;
    }
    
    $email = trim($input['email'] ?? '');
    $template = trim($input['template'] ?? 'test');
    
    // Validate email
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Valid email address is required']);
        exit;
    }
    
    // Load required files
    require_once __DIR__ . '/../config/config.php';
    require_once __DIR__ . '/../includes/functions.php';
    require_once __DIR__ . '/../includes/system-settings.php';
    require_once __DIR__ . '/../includes/email-template.php';
    
    // Get email template instance
    $emailTemplate = new EmailTemplate();
    $systemSettings = SystemSettings::getInstance();
    $siteName = $systemSettings->get('site_name', 'SecureBank Online');
    
    // Generate email content based on template type
    switch ($template) {
        case 'welcome':
            $subject = 'Welcome to ' . $siteName . ' - Test Email';
            $html = $emailTemplate->welcomeEmail('Test User');
            break;
            
        case 'transaction_debit':
            $subject = 'Transaction Alert - Debit - Test Email';
            $html = $emailTemplate->debitAlertEmail(
                'Test User',
                500.00,
                'USD',
                'Jane Doe',
                2500.00,
                'TXN-TEST123',
                date('F j, Y g:i A')
            );
            break;
            
        case 'transaction_credit':
            $subject = 'Transaction Alert - Credit - Test Email';
            $html = $emailTemplate->creditAlertEmail(
                'Test User',
                1000.00,
                'USD',
                'John Doe',
                3500.00,
                'TXN-TEST456',
                date('F j, Y g:i A')
            );
            break;
            
        case 'password_reset':
            $subject = 'Password Reset Request - Test Email';
            $html = $emailTemplate->passwordResetEmail('Test User', SITE_URL . '/auth/reset-password/test-token');
            break;
            
        case 'kyc_approved':
            $subject = 'KYC Approved - Test Email';
            $html = $emailTemplate->kycApprovedEmail('Test User');
            break;
            
        case 'card_approved':
            $subject = 'Card Application Approved - Test Email';
            $html = $emailTemplate->cardApprovedEmail('Test User', 'Credit', 'Premium Rewards Card');
            break;
            
        case 'loan_approved':
            $subject = 'Loan Approved - Test Email';
            $html = $emailTemplate->loanApprovedEmail(
                'Test User',
                'Personal',
                10000.00,
                'USD',
                5.5,
                36
            );
            break;
            
        case '2fa':
            $subject = 'Two-Factor Authentication Code - Test Email';
            $html = $emailTemplate->twoFactorEmail('Test User', '123456', 10);
            break;
            
        case 'test':
        default:
            $subject = 'SMTP Test Email from ' . $siteName;
            $html = $emailTemplate->render(
                'SMTP Configuration Test',
                '<h2>Email Configuration Test</h2>
                <p>Hello,</p>
                <p>If you\'re reading this, your SMTP configuration is working correctly!</p>
                
                <div class="success-box">
                    <p style="margin: 0;"><strong>✓ SMTP Connection Successful</strong></p>
                    <p style="margin: 5px 0 0 0;">Your email server is properly configured and emails are being sent successfully.</p>
                </div>
                
                <div class="info-box">
                    <p style="margin: 0;"><strong>Test Details:</strong></p>
                    <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                        <li>Test performed: ' . date('F j, Y g:i A') . '</li>
                        <li>Sent from: ' . (defined('SMTP_HOST') ? SMTP_HOST : 'Unknown') . '</li>
                        <li>Bank name: ' . $siteName . '</li>
                    </ul>
                </div>
                
                <p>This is a test email sent from your banking system\'s admin panel.</p>
                
                <p><strong>What\'s working:</strong></p>
                <ul style="color: #666;">
                    <li>✓ SMTP server connection</li>
                    <li>✓ Email authentication</li>
                    <li>✓ Email delivery</li>
                    <li>✓ Branded email template</li>
                    <li>✓ Dynamic logo and branding</li>
                </ul>
                
                <p>You can now confidently use the email system for all customer notifications.</p>
                
                <p>Best regards,<br><strong>' . $siteName . ' Team</strong></p>',
                '<p style="color: #999;">This is an automated test message from your admin panel.</p>'
            );
            break;
    }
    
    // Clear any accidental output
    ob_end_clean();
    
    // Send the email
    $result = sendEmail($email, $subject, $html);
    
    if ($result) {
        // Log the test
        logActivity($_SESSION['user_id'], 'EMAIL_TEST', "Sent test email ($template) to $email");
        
        echo json_encode([
            'success' => true,
            'message' => 'Test email sent successfully to ' . $email
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to send email. Please check your SMTP configuration in config.php'
        ]);
    }
    exit;
    
} catch (Exception $e) {
    ob_end_clean();
    error_log('Email Test Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage(),
        'file' => basename($e->getFile()),
        'line' => $e->getLine()
    ]);
    exit;
} catch (Error $e) {
    ob_end_clean();
    error_log('Email Test Fatal Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Fatal error: ' . $e->getMessage()
    ]);
    exit;
}
