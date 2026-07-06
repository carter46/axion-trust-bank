<?php
/**
 * Professional Email Template System
 * All emails use this branded template
 */

class EmailTemplate {
    private $systemSettings;
    private $siteName;
    private $siteLogo;
    private $siteEmail;
    private $supportPhone;
    private $bankAddress;
    private $siteUrl;
    
    public function __construct() {
        $this->systemSettings = SystemSettings::getInstance();
        $this->siteName = $this->systemSettings->get('site_name', 'SecureBank Online');
        
        // Use getSiteLogo() function to get the logo URL (same as used throughout the site)
        require_once __DIR__ . '/system-settings.php';
        if (function_exists('getSiteLogo')) {
            $this->siteLogo = getSiteLogo();
        } else {
            // Fallback if function doesn't exist
            $this->siteLogo = $this->systemSettings->get('site_logo_url', SITE_URL . '/assets/images/logo.svg');
        }
        
        // CRITICAL: Ensure logo URL is a full absolute URL for email clients
        // Email clients require full URLs (http/https) to display images
        if ($this->siteLogo) {
            // If it's not already a full URL, convert it to one
            if (!filter_var($this->siteLogo, FILTER_VALIDATE_URL)) {
                // If it doesn't start with http/https, prepend SITE_URL
                if (strpos($this->siteLogo, 'http') !== 0) {
                    $this->siteLogo = SITE_URL . '/' . ltrim($this->siteLogo, '/');
                }
            }
            
            // Final validation - ensure it's now a valid URL
            if (!filter_var($this->siteLogo, FILTER_VALIDATE_URL)) {
                // If still not valid, use default
                error_log("Email Template: Invalid logo URL, using default");
                $this->siteLogo = SITE_URL . '/assets/images/logo.svg';
            } else {
                error_log("Email Template: Using logo URL: " . $this->siteLogo);
            }
        } else {
            // If logo is empty, use default
            $this->siteLogo = SITE_URL . '/assets/images/logo.svg';
        }
        
        $this->siteEmail = $this->systemSettings->get('site_email', 'support@securebank.com');
        $this->supportPhone = $this->systemSettings->get('support_phone', '+1 (555) 123-4567');
        $this->bankAddress = $this->systemSettings->get('bank_address', '123 Banking Street, New York, NY 10001');
        $this->siteUrl = $this->systemSettings->get('site_url', SITE_URL);
    }
    
    /**
     * Generate a complete HTML email
     */
    public function render($title, $content, $footerNote = '') {
        $currentYear = date('Y');
        
        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f5f7fa;
            line-height: 1.6;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .email-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            padding: 30px 40px;
            text-align: center;
        }
        .email-header img {
            max-width: 300px;
            height: auto;
            margin-bottom: 10px;
        }
        .email-header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .email-body {
            padding: 40px;
            color: #333333;
        }
        .email-body h2 {
            color: #1e3a8a;
            font-size: 20px;
            margin: 0 0 20px 0;
        }
        .email-body p {
            margin: 0 0 15px 0;
            color: #666666;
        }
        .btn {
            display: inline-block;
            padding: 14px 32px;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 20px 0;
            text-align: center;
        }
        .info-box {
            background-color: #f0f4ff;
            border-left: 4px solid #3b82f6;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .warning-box {
            background-color: #fff9e6;
            border-left: 4px solid #f59e0b;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .success-box {
            background-color: #f0fdf4;
            border-left: 4px solid #10b981;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .transaction-details {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .transaction-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .transaction-details td {
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .transaction-details td:first-child {
            color: #666666;
            font-weight: 500;
        }
        .transaction-details td:last-child {
            text-align: right;
            color: #202124;
            font-weight: 600;
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 30px 40px;
            text-align: center;
            color: #666666;
            font-size: 13px;
        }
        .email-footer p {
            margin: 5px 0;
        }
        .email-footer a {
            color: #1e3a8a;
            text-decoration: none;
        }
        .social-links {
            margin: 20px 0 10px;
        }
        .social-links a {
            display: inline-block;
            margin: 0 8px;
            color: #666666;
            text-decoration: none;
            font-size: 18px;
        }
        .divider {
            height: 1px;
            background-color: #e0e0e0;
            margin: 20px 0;
        }
        @media only screen and (max-width: 600px) {
            .email-header, .email-body, .email-footer {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <img src="{$this->siteLogo}" alt="{$this->siteName}" style="max-width: 200px; height: auto;">
            <h1>{$this->siteName}</h1>
        </div>
        
        <!-- Body -->
        <div class="email-body">
            {$content}
        </div>
        
        <!-- Footer -->
        <div class="email-footer">
            <p><strong>{$this->siteName}</strong></p>
            <p>{$this->bankAddress}</p>
            <div class="divider"></div>
            <p>
                <strong>Contact Us:</strong><br>
                Email: <a href="mailto:{$this->siteEmail}">{$this->siteEmail}</a><br>
                Phone: {$this->supportPhone}
            </p>
            <div class="divider"></div>
            {$footerNote}
            <p style="color: #999999; font-size: 12px; margin-top: 20px;">
                © {$currentYear} {$this->siteName}. All rights reserved.<br>
                This email was sent to you because you have an account with us.
            </p>
        </div>
    </div>
</body>
</html>
HTML;
    }
    
    /**
     * 2FA/OTP Email
     */
    public function twoFactorEmail($recipientName, $otpCode, $expiryMinutes = 10) {
        $content = <<<HTML
<h2>Two-Factor Authentication</h2>
<p>Hello {$recipientName},</p>
<p>You are receiving this email because someone (hopefully you) is trying to log in to your account.</p>

<div class="info-box">
    <p style="margin: 0; font-size: 16px;"><strong>Your One-Time Password (OTP):</strong></p>
    <p style="margin: 10px 0 0 0; font-size: 32px; font-weight: 700; color: #1e3a8a; letter-spacing: 8px;">{$otpCode}</p>
</div>

<p>This code will expire in <strong>{$expiryMinutes} minutes</strong>.</p>

<div class="warning-box">
    <p style="margin: 0;"><strong>⚠️ Security Notice:</strong></p>
    <p style="margin: 5px 0 0 0;">If you did not request this code, please ignore this email and secure your account immediately by changing your password.</p>
</div>

<p>For your security, never share this code with anyone, including our staff.</p>

<p>Best regards,<br><strong>{$this->siteName} Security Team</strong></p>
HTML;
        
        return $this->render('Two-Factor Authentication Code', $content, '<p style="color: #999;">This is an automated security message.</p>');
    }
    
    /**
     * Transaction Alert - Debit (Money Out)
     */
    public function debitAlertEmail($recipientName, $amount, $currency, $recipient, $balance, $transactionRef, $date, $amountFromCurrency = null, $balanceFromCurrency = null) {
        require_once __DIR__ . '/functions.php';
        $displayCurrency = strtoupper(trim((string)$currency));
        $amountFrom = strtoupper(trim((string)($amountFromCurrency ?: $displayCurrency)));
        $balanceFrom = strtoupper(trim((string)($balanceFromCurrency ?: $amountFrom)));
        $formattedAmount = formatCurrency($amount, $displayCurrency, $amountFrom);
        $formattedBalance = formatCurrency($balance, $displayCurrency, $balanceFrom);
        
        $content = <<<HTML
<h2>Transaction Alert - Debit</h2>
<p>Hello {$recipientName},</p>
<p>A debit transaction has been processed on your account.</p>

<div class="transaction-details">
    <table>
        <tr>
            <td>Transaction Type:</td>
            <td style="color: #f44336;">DEBIT</td>
        </tr>
        <tr>
            <td>Amount:</td>
            <td>-{$formattedAmount}</td>
        </tr>
        <tr>
            <td>Recipient:</td>
            <td>{$recipient}</td>
        </tr>
        <tr>
            <td>Date & Time:</td>
            <td>{$date}</td>
        </tr>
        <tr>
            <td>Transaction Ref:</td>
            <td>{$transactionRef}</td>
        </tr>
        <tr>
            <td><strong>Available Balance:</strong></td>
            <td><strong>{$formattedBalance}</strong></td>
        </tr>
    </table>
</div>

<div class="warning-box">
    <p style="margin: 0;"><strong>⚠️ Not You?</strong></p>
    <p style="margin: 5px 0 0 0;">If you did not authorize this transaction, please contact us immediately.</p>
</div>

<a href="{$this->siteUrl}/transaction" class="btn">View Transaction History</a>

<p>Thank you for banking with us.</p>

<p>Best regards,<br><strong>{$this->siteName} Team</strong></p>
HTML;
        
        return $this->render('Transaction Alert - Debit', $content, '<p style="color: #999;">This is an automated transaction notification.</p>');
    }
    
    /**
     * Transaction Alert - Credit (Money In)
     */
    public function creditAlertEmail($recipientName, $amount, $currency, $sender, $balance, $transactionRef, $date, $amountFromCurrency = null, $balanceFromCurrency = null) {
        require_once __DIR__ . '/functions.php';
        $displayCurrency = strtoupper(trim((string)$currency));
        $amountFrom = strtoupper(trim((string)($amountFromCurrency ?: $displayCurrency)));
        $balanceFrom = strtoupper(trim((string)($balanceFromCurrency ?: $amountFrom)));
        $formattedAmount = formatCurrency($amount, $displayCurrency, $amountFrom);
        $formattedBalance = formatCurrency($balance, $displayCurrency, $balanceFrom);
        
        $content = <<<HTML
<h2>Transaction Alert - Credit</h2>
<p>Hello {$recipientName},</p>
<p>A credit transaction has been processed on your account.</p>

<div class="transaction-details">
    <table>
        <tr>
            <td>Transaction Type:</td>
            <td style="color: #10b981;">CREDIT</td>
        </tr>
        <tr>
            <td>Amount:</td>
            <td>+{$formattedAmount}</td>
        </tr>
        <tr>
            <td>Date & Time:</td>
            <td>{$date}</td>
        </tr>
        <tr>
            <td>Transaction Ref:</td>
            <td>{$transactionRef}</td>
        </tr>
        <tr>
            <td><strong>Available Balance:</strong></td>
            <td><strong>{$formattedBalance}</strong></td>
        </tr>
    </table>
</div>

<div class="success-box">
    <p style="margin: 0;"><strong>✓ Funds Received</strong></p>
    <p style="margin: 5px 0 0 0;">The funds have been successfully credited to your account and are now available.</p>
</div>

<a href="{$this->siteUrl}/transaction" class="btn">View Transaction History</a>

<p>Thank you for banking with us.</p>

<p>Best regards,<br><strong>{$this->siteName} Team</strong></p>
HTML;
        
        return $this->render('Transaction Alert - Credit', $content, '<p style="color: #999;">This is an automated transaction notification.</p>');
    }
    
    /**
     * Simulation Credit Alert Email - For testing with customizable template
     */
    public function simulationCreditAlertEmail($siteName, $recipientName, $amount, $currency, $alertCaption, $description, $transactionStatus, $templateData, $transactionRef, $balance, $accountNumber = '', $swiftCode = '', $footerContactMethod = 'email', $footerContactValue = '', $templateAddress = '', $sender = '', $senderName = '', $recipientBank = '', $senderBank = '') {
        require_once __DIR__ . '/functions.php';
        $displayCurrency = strtoupper(trim((string)$currency));
        $formattedAmount = formatCurrency($amount, $displayCurrency, $displayCurrency);
        $formattedBalance = formatCurrency($balance, $displayCurrency, $displayCurrency);
        
        // Extract and sanitize template data
        // Validate and sanitize colors to prevent CSS injection
        $primaryColor = $templateData['primary_color'] ?? '#359eb4';
        $secondaryColor = $templateData['secondary_color'] ?? '#2a7e90';
        $accentColor = $templateData['accent_color'] ?? '#10b981';
        
        // Validate color format (hex only)
        if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $primaryColor)) $primaryColor = '#359eb4';
        if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $secondaryColor)) $secondaryColor = '#2a7e90';
        if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $accentColor)) $accentColor = '#10b981';
        
        // Get logo URL - prioritize template logo, fallback to site logo
        $logoUrl = $templateData['logo_url'] ?? null;
        if (empty($logoUrl) || trim($logoUrl) === '') {
            $logoUrl = $this->siteLogo;
        }
        $logoAltText = htmlspecialchars($templateData['logo_alt_text'] ?? $siteName, ENT_QUOTES, 'UTF-8');
        
        // Use template address if provided and not empty, otherwise fallback to system address
        // Priority: templateData['address'] (from DB) > templateAddress parameter > system bankAddress
        $emailAddress = $this->bankAddress; // Default fallback
        if (isset($templateData['address']) && !empty($templateData['address']) && trim($templateData['address']) !== '') {
            $emailAddress = trim($templateData['address']);
        } elseif (!empty($templateAddress) && trim($templateAddress) !== '') {
            $emailAddress = trim($templateAddress);
        }
        $emailAddress = htmlspecialchars($emailAddress, ENT_QUOTES, 'UTF-8');
        
        // Ensure logo URL is absolute and validate
        // First, trim any whitespace
        if ($logoUrl) {
            $logoUrl = trim($logoUrl);
        }
        
        // Convert relative paths to absolute URLs
        if ($logoUrl && !filter_var($logoUrl, FILTER_VALIDATE_URL)) {
            // If it doesn't start with http/https, make it relative to SITE_URL
            if (strpos($logoUrl, 'http') !== 0 && strpos($logoUrl, '//') !== 0) {
                // Remove leading slash if present, then add SITE_URL
                $logoUrl = SITE_URL . '/' . ltrim($logoUrl, '/');
            }
        }
        
        // Final validation - ensure it's a valid URL
        if (!$logoUrl || !filter_var($logoUrl, FILTER_VALIDATE_URL)) {
            // Use site logo as fallback
            $logoUrl = $this->siteLogo;
        }
        
        // Ensure we always have a logo URL (final fallback)
        if (empty($logoUrl) || !filter_var($logoUrl, FILTER_VALIDATE_URL)) {
            $logoUrl = SITE_URL . '/assets/images/logo.svg';
        }
        
        // Double-check it's a valid absolute URL
        if (!filter_var($logoUrl, FILTER_VALIDATE_URL)) {
            error_log("Email Template: Invalid logo URL after processing: " . $logoUrl);
            $logoUrl = SITE_URL . '/assets/images/logo.svg';
        }
        
        // Escape logo URL for HTML attribute (even though validated, escape for safety)
        $logoUrlEscaped = htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8');
        
        // Also escape site logo for fallback in onerror handler
        $siteLogoEscaped = htmlspecialchars($this->siteLogo, ENT_QUOTES, 'UTF-8');
        
        // Escape user inputs to prevent XSS
        $siteName = htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8');
        $recipientName = htmlspecialchars($recipientName, ENT_QUOTES, 'UTF-8');
        $alertCaption = htmlspecialchars($alertCaption, ENT_QUOTES, 'UTF-8');
        $description = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
        $transactionRef = htmlspecialchars($transactionRef, ENT_QUOTES, 'UTF-8');
        $accountNumber = htmlspecialchars($accountNumber, ENT_QUOTES, 'UTF-8');
        $swiftCode = htmlspecialchars($swiftCode, ENT_QUOTES, 'UTF-8');
        $recipientBank = htmlspecialchars($recipientBank, ENT_QUOTES, 'UTF-8');
        $senderBank = htmlspecialchars($senderBank, ENT_QUOTES, 'UTF-8');
        
        // Account Number - always show in email (required for advanced templates)
        $accountRow = '';
        if (!empty($accountNumber)) {
            $accountRow = "
                    <tr>
                        <td>Account Number:</td>
                        <td>{$accountNumber}</td>
                    </tr>";
        } else {
            // For simple templates, we might not have account number, but show it if available
            // This will be empty for simple templates
        }
        
        // SWIFT Code should NOT be shown in email - only on receipt
        $swiftRow = '';
        
        // Recipient Bank Name row (only for advanced templates)
        $recipientBankRow = '';
        if ($templateType === 'advanced' && !empty($recipientBank)) {
            $recipientBankRow = "
                    <tr>
                        <td>Recipient Bank:</td>
                        <td>{$recipientBank}</td>
                    </tr>";
        }
        
        // Sender Bank Name row (only for advanced templates, shows after sender's name)
        $senderBankRow = '';
        if ($templateType === 'advanced' && !empty($senderBank)) {
            $senderBankRow = "
                    <tr>
                        <td>Sender's Bank:</td>
                        <td>{$senderBank}</td>
                    </tr>";
        }
        
        // Determine "Sender's Name" value based on template type
        $templateType = $templateData['template_type'] ?? 'simple';
        if ($templateType === 'simple' && !empty($sender)) {
            $fromValue = htmlspecialchars($sender, ENT_QUOTES, 'UTF-8');
        } elseif ($templateType === 'advanced' && !empty($senderName)) {
            // For advanced templates, use sender_name from the form
            $fromValue = htmlspecialchars($senderName, ENT_QUOTES, 'UTF-8');
        } else {
            $fromValue = 'Payment Received!';
        }
        
        // Map old "completed" to "successful" for backward compatibility
        if ($transactionStatus === 'completed') {
            $transactionStatus = 'successful';
        }
        
        // Status badge colors - fixed colors for each status
        $statusColors = [
            'successful' => '#10b981',  // Green
            'pending' => '#f59e0b',     // Orange/Amber
            'processing' => '#3b82f6',  // Blue
            'failed' => '#ef4444'       // Red
        ];
        $statusColor = $statusColors[$transactionStatus] ?? '#10b981';
        
        // Status labels
        $statusLabels = [
            'successful' => 'Successful',
            'pending' => 'Pending',
            'processing' => 'Processing',
            'failed' => 'Failed'
        ];
        $statusLabel = $statusLabels[$transactionStatus] ?? 'Successful';
        
        // Generate footer contact HTML
        $footerContactHtml = $this->getFooterContactHtml($footerContactMethod, $footerContactValue, $primaryColor);
        
        $currentDate = date('F j, Y');
        $currentTime = date('g:i A');
        $currentYear = date('Y');
        
        // Generate email HTML with custom colors
        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Alert - Credit</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f5f7fa;
            line-height: 1.6;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .email-header {
            background: linear-gradient(135deg, {$primaryColor} 0%, {$secondaryColor} 100%);
            padding: 30px 40px;
            text-align: center;
        }
        .email-header img {
            max-width: 300px;
            height: auto;
            margin-bottom: 10px;
        }
        .email-header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .email-body {
            padding: 40px;
            color: #333333;
        }
        .alert-caption {
            color: {$accentColor};
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 20px 0;
            text-align: center;
        }
        .email-body h2 {
            color: {$primaryColor};
            font-size: 20px;
            margin: 0 0 20px 0;
        }
        .email-body p {
            margin: 0 0 15px 0;
            color: #666666;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            background-color: {$statusColor};
            color: #ffffff;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            margin: 10px 0;
        }
        .btn {
            display: inline-block;
            padding: 14px 32px;
            background: linear-gradient(135deg, {$primaryColor} 0%, {$secondaryColor} 100%);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 20px 0;
            text-align: center;
        }
        .description-box {
            background-color: #f8f9fa;
            border-left: 4px solid {$secondaryColor};
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .success-box {
            background-color: #f0fdf4;
            border-left: 4px solid {$accentColor};
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .transaction-details {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .transaction-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .transaction-details td {
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .transaction-details td:first-child {
            color: #666666;
            font-weight: 500;
        }
        .transaction-details td:last-child {
            text-align: right;
            color: #202124;
            font-weight: 600;
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 30px 40px;
            text-align: center;
            color: #666666;
            font-size: 13px;
        }
        .email-footer p {
            margin: 5px 0;
        }
        .email-footer a {
            color: {$primaryColor};
            text-decoration: none;
        }
        .divider {
            height: 1px;
            background-color: #e0e0e0;
            margin: 20px 0;
        }
        @media only screen and (max-width: 600px) {
            .email-container {
                max-width: 100% !important;
                width: 100% !important;
            }
            .email-header, .email-body, .email-footer {
                padding: 15px !important;
            }
            .email-header img {
                max-width: 250px !important;
                max-height: 75px !important;
            }
            .alert-caption {
                font-size: 22px !important;
            }
            .amount-highlight-box {
                padding: 20px !important;
            }
            .amount-value {
                font-size: 28px !important;
            }
            .email-body h2 {
                font-size: 18px !important;
            }
            .transaction-details table {
                font-size: 13px !important;
                width: 100% !important;
            }
            .transaction-details td {
                padding: 10px 8px !important;
                display: table-cell !important;
                vertical-align: top;
            }
            .transaction-details td:first-child {
                font-weight: 600;
                color: #666;
                width: 35% !important;
                padding-right: 8px !important;
            }
            .transaction-details td:first-child::after {
                content: ":";
                margin-left: 2px;
            }
            .transaction-details td:last-child {
                text-align: left !important;
                color: #202124;
                width: 65% !important;
                padding-left: 8px !important;
            }
            .transaction-details tr {
                border-bottom: 1px solid #f0f0f0;
            }
            .transaction-details tr:last-child {
                border-bottom: none;
            }
            .status-badge {
                font-size: 11px !important;
                padding: 6px 12px !important;
            }
            .description-box {
                padding: 15px !important;
            }
            .info-box {
                padding: 15px !important;
            }
        }
        @media only screen and (max-width: 480px) {
            .email-header, .email-body, .email-footer {
                padding: 12px !important;
            }
            .email-header img {
                max-width: 250px !important;
            }
            .alert-caption {
                font-size: 20px !important;
            }
            .amount-highlight-box {
                padding: 15px !important;
            }
            .amount-value {
                font-size: 24px !important;
            }
            .transaction-details {
                font-size: 12px !important;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <img src="{$logoUrlEscaped}" alt="{$logoAltText}" style="max-width: 300px; height: auto; display: block; margin: 0 auto;" onerror="this.onerror=null; this.src='{$siteLogoEscaped}';">
        </div>
        
        <!-- Body -->
        <div class="email-body">
            <div class="alert-caption">{$alertCaption}</div>
            
            <p>Hello {$recipientName},</p>
            <p>We wish to inform you that a credit transaction occurred on your account with us.</p>
            
            <div class="status-badge">{$statusLabel}</div>
            
            <div class="amount-highlight-box" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 2px solid #10b981; border-radius: 12px; padding: 25px; margin: 25px 0; text-align: center;">
                <div style="color: #666; font-size: 14px; font-weight: 600; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">Transaction Amount</div>
                <div class="amount-value" style="color: #10b981; font-size: 36px; font-weight: 700; line-height: 1.2;">+{$formattedAmount}</div>
            </div>
            
            <h2 style="color: {$primaryColor}; font-size: 20px; margin: 20px 0 15px 0;">Transaction Details</h2>
            
            <div class="transaction-details">
                <table>
                    {$accountRow}
                    <tr>
                        <td>Sender's Name:</td>
                        <td>{$fromValue}</td>
                    </tr>
                    {$senderBankRow}
                    {$recipientBankRow}
                    <tr>
                        <td>Date:</td>
                        <td>{$currentDate}</td>
                    </tr>
                    <tr>
                        <td>Time:</td>
                        <td>{$currentTime}</td>
                    </tr>
                    <tr>
                        <td>Narration:</td>
                        <td>{$description}</td>
                    </tr>
                    <tr>
                        <td>Transaction Ref:</td>
                        <td>{$transactionRef}</td>
                    </tr>
                    {$swiftRow}
                </table>
            </div>
            
            <div class="info-box" style="background-color: #f0f4ff; border-left: 4px solid {$primaryColor}; padding: 20px; margin: 20px 0; border-radius: 4px;">
                <p style="margin: 0; font-weight: 600; color: #202124;">Need Assistance?</p>
                <p style="margin: 8px 0 0 0; color: #666666;">If you have any questions or concerns about this transaction, please contact our support team using the contact information provided at the bottom of this email.</p>
            </div>
            
            <p>Thank you for banking with us.</p>
            
            <p>Best regards,<br><strong>{$siteName} Team</strong></p>
        </div>
        
        <!-- Footer -->
        <div class="email-footer">
            <p><strong>{$siteName}</strong></p>
            <p>{$emailAddress}</p>
            <div class="divider"></div>
            <p>
                <strong>Contact Us:</strong><br>
                {$footerContactHtml}
            </p>
            <div class="divider"></div>
            <p style="color: #999;">This is a simulation email for testing purposes.</p>
            <p style="color: #999999; font-size: 12px; margin-top: 20px;">
                © {$currentYear} {$siteName}. All rights reserved.<br>
                This email was sent to you because you have an account with us.
            </p>
        </div>
    </div>
</body>
</html>
HTML;
    }
    
    /**
     * Get footer contact HTML based on contact method
     */
    private function getFooterContactHtml($contactMethod, $contactValue, $primaryColor = null) {
        // Validate and sanitize color
        $linkColor = '#1e3a8a'; // Default
        if ($primaryColor && preg_match('/^#[0-9A-Fa-f]{6}$/', $primaryColor)) {
            $linkColor = $primaryColor;
        } elseif (isset($this->primaryColor) && preg_match('/^#[0-9A-Fa-f]{6}$/', $this->primaryColor)) {
            $linkColor = $this->primaryColor;
        }
        
        if (empty($contactValue)) {
            // Fallback to default
            return "Email: <a href=\"mailto:{$this->siteEmail}\" style=\"color: {$linkColor}; text-decoration: none;\">{$this->siteEmail}</a><br>Phone: {$this->supportPhone}";
        }
        
        if ($contactMethod === 'whatsapp') {
            // Clean WhatsApp number for wa.me link (remove +, spaces, dashes, etc.)
            $whatsappNumber = preg_replace('/[^0-9]/', '', $contactValue); // Remove all non-numeric characters
            $contactValueEscaped = htmlspecialchars($contactValue, ENT_QUOTES, 'UTF-8');
            return "WhatsApp: <a href=\"https://wa.me/{$whatsappNumber}\" style=\"color: {$linkColor}; text-decoration: none;\">{$contactValueEscaped}</a>";
        } else {
            $contactValueEscaped = htmlspecialchars($contactValue, ENT_QUOTES, 'UTF-8');
            return "Email: <a href=\"mailto:{$contactValueEscaped}\" style=\"color: {$linkColor}; text-decoration: none;\">{$contactValueEscaped}</a>";
        }
    }
    
    /**
     * Transaction Failed Email - Notifies user when transaction fails and amount is refunded
     */
    public function transactionFailedEmail($recipientName, $amount, $currency, $transactionRef, $description, $balance, $date, $amountFromCurrency = null, $balanceFromCurrency = null) {
        require_once __DIR__ . '/functions.php';
        $displayCurrency = strtoupper(trim((string)$currency));
        $amountFrom = strtoupper(trim((string)($amountFromCurrency ?: $displayCurrency)));
        $balanceFrom = strtoupper(trim((string)($balanceFromCurrency ?: $amountFrom)));
        $formattedAmount = formatCurrency($amount, $displayCurrency, $amountFrom);
        $formattedBalance = formatCurrency($balance, $displayCurrency, $balanceFrom);
        
        $content = <<<HTML
<h2>Transaction Failed - Refund Processed</h2>
<p>Hello {$recipientName},</p>
<p>We regret to inform you that your transaction has been marked as <strong>FAILED</strong>.</p>

<div class="transaction-details">
    <table>
        <tr>
            <td>Transaction Status:</td>
            <td style="color: #dc2626; font-weight: 600;">FAILED</td>
        </tr>
        <tr>
            <td>Transaction Description:</td>
            <td>{$description}</td>
        </tr>
        <tr>
            <td>Transaction Ref:</td>
            <td>{$transactionRef}</td>
        </tr>
        <tr>
            <td>Date & Time:</td>
            <td>{$date}</td>
        </tr>
        <tr>
            <td>Amount Refunded:</td>
            <td style="color: #10b981; font-weight: 600;">+{$formattedAmount}</td>
        </tr>
        <tr>
            <td><strong>Current Account Balance:</strong></td>
            <td><strong style="color: #1e3a8a;">{$formattedBalance}</strong></td>
        </tr>
    </table>
</div>

<div class="success-box">
    <p style="margin: 0;"><strong>✓ Refund Processed</strong></p>
    <p style="margin: 5px 0 0 0;">The amount of <strong>{$formattedAmount}</strong> has been refunded to your account balance. Your funds are now available in your account.</p>
</div>

<div class="info-box">
    <p style="margin: 0;"><strong>ℹ️ What This Means:</strong></p>
    <p style="margin: 5px 0 0 0;">Your transaction could not be completed. The funds that were debited from your account have been fully refunded. If you have any questions or concerns, please contact our support team.</p>
</div>

<a href="{$this->siteUrl}/transaction" class="btn">View Transaction History</a>

<p>If you have any questions about this transaction, please don't hesitate to contact our support team.</p>

<p>Thank you for banking with us.</p>

<p>Best regards,<br><strong>{$this->siteName} Team</strong></p>
HTML;
        
        return $this->render('Transaction Failed - Refund Processed', $content, '<p style="color: #999;">This is an automated transaction notification.</p>');
    }
    
    /**
     * Email Verification Email
     */
    public function emailVerificationEmail($recipientName, $verificationLink) {
        $content = <<<HTML
<h2>Complete Your Registration</h2>
<p>Hello {$recipientName},</p>
<p>Thank you for registering with {$this->siteName}. We need to confirm your email address to activate your account.</p>

<p>To activate your account, please use the link below:</p>
<p style="word-break: break-all; background: #f5f7fa; padding: 15px; border-radius: 8px; margin: 20px 0;">
    <a href="{$verificationLink}" style="color: #1e3a8a; text-decoration: none;">{$verificationLink}</a>
</p>

<a href="{$verificationLink}" class="btn">Activate Account</a>

<div class="info-box">
    <p style="margin: 0;"><strong>Important:</strong> This link expires in 24 hours.</p>
    <p style="margin: 5px 0 0 0;">If the link expires, you can request a new activation email from the login page.</p>
</div>

<div class="warning-box">
    <p style="margin: 0;"><strong>Didn't register with us?</strong></p>
    <p style="margin: 5px 0 0 0;">If you didn't create an account with {$this->siteName}, you can safely ignore this message. No account will be created without email confirmation.</p>
</div>

<p><strong>What happens after activation:</strong></p>
<ul style="color: #666;">
    <li>Your account will be fully activated</li>
    <li>You'll receive important account notifications</li>
    <li>You can reset your password if needed</li>
    <li>You'll have access to all account features</li>
</ul>

<p>After activation, you can log in and start using your account.</p>

<p>Best regards,<br><strong>{$this->siteName} Customer Service</strong></p>
HTML;
        
        return $this->render('Activate Your Account - ' . $this->siteName, $content, '<p style="color: #999;">This is an automated account activation message from {$this->siteName}.</p>');
    }
    
    /**
     * Welcome Email
     */
    public function welcomeEmail($recipientName) {
        $content = <<<HTML
<h2>Your Account is Ready</h2>
<p>Hello {$recipientName},</p>
<p>Thank you for joining {$this->siteName}. Your account has been successfully created.</p>

<div class="success-box">
    <p style="margin: 0;"><strong>Account Created</strong></p>
    <p style="margin: 5px 0 0 0;">Your account is now active and ready to use.</p>
</div>

<p><strong>Getting Started:</strong></p>
<ul style="color: #666;">
    <li>Complete your identity verification (KYC)</li>
    <li>Set up your Transfer PIN for transactions</li>
    <li>Set up your Login PIN for quick access</li>
    <li>Review our banking features and services</li>
</ul>

<p>You can access your account dashboard here:</p>
<p style="word-break: break-all; background: #f5f7fa; padding: 15px; border-radius: 8px; margin: 20px 0;">
    <a href="{$this->siteUrl}/dashboard" style="color: #1e3a8a; text-decoration: none;">{$this->siteUrl}/dashboard</a>
</p>

<a href="{$this->siteUrl}/dashboard" class="btn">Access Your Account</a>

<div class="info-box">
    <p style="margin: 0;"><strong>Questions?</strong></p>
    <p style="margin: 5px 0 0 0;">Our customer service team is available {$this->systemSettings->get('support_hours', '24/7')} to help you.</p>
</div>

<p>We look forward to serving your banking needs.</p>

<p>Best regards,<br><strong>{$this->siteName} Customer Service</strong></p>
HTML;
        
        return $this->render('Your Account is Ready - ' . $this->siteName, $content);
    }
    
    /**
     * Welcome Email for Joint Account Request
     */
    public function welcomeJointAccountEmail($recipientName, $verificationLink) {
        $content = <<<HTML
<h2>Welcome - Joint Account Request</h2>
<p>Hello {$recipientName},</p>
<p>Thank you for requesting to join an existing account with {$this->siteName}.</p>

<div class="info-box">
    <p style="margin: 0;"><strong>Your Request Status</strong></p>
    <p style="margin: 5px 0 0 0;">Your joint account request has been received and is being processed.</p>
</div>

<p><strong>Next Steps:</strong></p>
<ol style="color: #666;">
    <li><strong>Verify Your Email Address</strong> - Click the button below to verify your email. This is required to proceed with your request.</li>
    <li><strong>Wait for Approval</strong> - The primary account owner has been notified and will review your request.</li>
    <li><strong>Get Notified</strong> - Once your request is approved, you'll receive an email with login instructions.</li>
</ol>

<div style="text-align: center; margin: 30px 0;">
    <a href="{$verificationLink}" class="btn" style="display: inline-block; padding: 14px 32px; background: #3b82f6; color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">Verify Your Email</a>
</div>

<p style="word-break: break-all; background: #f5f7fa; padding: 15px; border-radius: 8px; margin: 20px 0; text-align: center; font-size: 12px; color: #666;">
    <strong>Or copy this link:</strong><br>
    <a href="{$verificationLink}" style="color: #1e3a8a; text-decoration: none;">{$verificationLink}</a>
</p>

<div class="warning-box" style="background: #fef3c7; border: 1px solid #fde68a; padding: 16px; border-radius: 8px; margin: 20px 0; color: #92400e;">
    <p style="margin: 0;"><strong>Important:</strong></p>
    <ul style="margin: 8px 0 0 20px; padding-left: 0;">
        <li>You will not be able to log in until the primary account owner approves your request</li>
        <li>This verification link expires in 24 hours</li>
        <li>Your joint account request will expire in 7 days if not approved</li>
    </ul>
</div>

<p>After you verify your email, you'll see a confirmation page with more details about your request status.</p>

<div class="info-box">
    <p style="margin: 0;"><strong>Questions?</strong></p>
    <p style="margin: 5px 0 0 0;">Our customer service team is available {$this->systemSettings->get('support_hours', '24/7')} to help you.</p>
</div>

<p>Best regards,<br><strong>{$this->siteName} Customer Service</strong></p>
HTML;
        
        return $this->render('Welcome - Joint Account Request - ' . $this->siteName, $content);
    }
    
    /**
     * Password Reset Email
     */
    public function passwordResetEmail($recipientName, $resetLink) {
        $content = <<<HTML
<h2>Password Reset Request</h2>
<p>Hello {$recipientName},</p>
<p>We received a request to reset your password. If you made this request, click the button below to create a new password.</p>

<a href="{$resetLink}" class="btn">Reset Password</a>

<div class="info-box">
    <p style="margin: 0;"><strong>Link Valid For:</strong> 1 hour</p>
    <p style="margin: 5px 0 0 0;">This link will expire after 1 hour for security reasons.</p>
</div>

<div class="warning-box">
    <p style="margin: 0;"><strong>⚠️ Didn't Request This?</strong></p>
    <p style="margin: 5px 0 0 0;">If you didn't request a password reset, please ignore this email. Your password will remain unchanged and your account is secure.</p>
</div>

<p><strong>Security Tips:</strong></p>
<ul style="color: #666;">
    <li>Never share your password with anyone</li>
    <li>Use a strong, unique password</li>
    <li>Enable two-factor authentication for extra security</li>
</ul>

<p>Best regards,<br><strong>{$this->siteName} Security Team</strong></p>
HTML;
        
        return $this->render('Password Reset Request - ' . $this->siteName, $content, '<p style="color: #999;">This is an automated security message.</p>');
    }
    
    /**
     * Password Changed Confirmation Email
     */
    public function passwordChangedEmail($recipientName) {
        $content = <<<HTML
<h2>Password Successfully Changed</h2>
<p>Hello {$recipientName},</p>
<p>This email confirms that your password has been successfully changed.</p>

<div class="success-box">
    <p style="margin: 0;"><strong>✓ Password Updated</strong></p>
    <p style="margin: 5px 0 0 0;">Your account password has been changed on {$this->siteName}.</p>
</div>

<div class="transaction-details">
    <table>
        <tr>
            <td>Change Date:</td>
            <td><strong>{$this->formatDate(date('Y-m-d H:i:s'))}</strong></td>
        </tr>
        <tr>
            <td>Account Status:</td>
            <td><strong style="color: #10b981;">Secure</strong></td>
        </tr>
    </table>
</div>

<div class="warning-box">
    <p style="margin: 0;"><strong>⚠️ Didn't Make This Change?</strong></p>
    <p style="margin: 5px 0 0 0;">If you didn't change your password, please contact us immediately at {$this->siteEmail} or call {$this->supportPhone}.</p>
</div>

<p>Best regards,<br><strong>{$this->siteName} Security Team</strong></p>
HTML;
        
        return $this->render('Password Changed - ' . $this->siteName, $content);
    }
    
    /**
     * PIN Changed Notification Email
     */
    public function pinChangedEmail($recipientName, $pinType) {
        $pinName = $pinType === 'transfer' ? 'Transfer PIN' : ($pinType === 'login' ? 'Login PIN' : 'Security PIN');
        
        $content = <<<HTML
<h2>{$pinName} Successfully Updated</h2>
<p>Hello {$recipientName},</p>
<p>This email confirms that your {$pinName} has been successfully changed.</p>

<div class="success-box">
    <p style="margin: 0;"><strong>✓ {$pinName} Updated</strong></p>
    <p style="margin: 5px 0 0 0;">Your {$pinName} has been changed on {$this->siteName}.</p>
</div>

<div class="transaction-details">
    <table>
        <tr>
            <td>PIN Type:</td>
            <td><strong>{$pinName}</strong></td>
        </tr>
        <tr>
            <td>Change Date:</td>
            <td><strong>{$this->formatDate(date('Y-m-d H:i:s'))}</strong></td>
        </tr>
        <tr>
            <td>Security Status:</td>
            <td><strong style="color: #10b981;">Active</strong></td>
        </tr>
    </table>
</div>

<div class="warning-box">
    <p style="margin: 0;"><strong>⚠️ Didn't Make This Change?</strong></p>
    <p style="margin: 5px 0 0 0;">If you didn't change your {$pinName}, please contact us immediately. Your account security may be compromised.</p>
</div>

<p><strong>Security Reminder:</strong></p>
<ul style="color: #666;">
    <li>Never share your PIN with anyone</li>
    <li>Don't use easily guessable numbers (birthdays, sequential numbers)</li>
    <li>Change your PIN regularly for better security</li>
</ul>

<p>Best regards,<br><strong>{$this->siteName} Security Team</strong></p>
HTML;
        
        return $this->render($pinName . ' Updated - ' . $this->siteName, $content);
    }
    
    /**
     * KYC Approved Email
     */
    public function kycApprovedEmail($recipientName) {
        $content = <<<HTML
<h2>KYC Verification Approved</h2>
<p>Hello {$recipientName},</p>
<p>Great news! Your KYC (Know Your Customer) verification has been approved.</p>

<div class="success-box">
    <p style="margin: 0;"><strong>✓ Verification Complete</strong></p>
    <p style="margin: 5px 0 0 0;">Your identity has been successfully verified. You now have full access to all banking features.</p>
</div>

<p><strong>What This Means:</strong></p>
<ul style="color: #666;">
    <li>✓ Higher transaction limits</li>
    <li>✓ Access to loan applications</li>
    <li>✓ Access to card applications</li>
    <li>✓ International wire transfers</li>
    <li>✓ Full account features unlocked</li>
</ul>

<a href="{$this->siteUrl}/dashboard" class="btn">Access Your Account</a>

<p>Thank you for completing the verification process. Your trust in {$this->siteName} is greatly appreciated.</p>

<p>Best regards,<br><strong>{$this->siteName} Compliance Team</strong></p>
HTML;
        
        return $this->render('KYC Approved - ' . $this->siteName, $content);
    }
    
    /**
     * KYC Rejected Email
     */
    public function kycRejectedEmail($recipientName, $reason) {
        $content = <<<HTML
<h2>KYC Verification - Action Required</h2>
<p>Hello {$recipientName},</p>
<p>We've reviewed your KYC (Know Your Customer) submission, and unfortunately, we need additional information.</p>

<div class="warning-box">
    <p style="margin: 0;"><strong>Verification Status: Pending Review</strong></p>
    <p style="margin: 5px 0 0 0;">Your submission couldn't be verified at this time.</p>
</div>

<div class="info-box">
    <p style="margin: 0;"><strong>Reason:</strong></p>
    <p style="margin: 10px 0 0 0;">{$reason}</p>
</div>

<p><strong>Next Steps:</strong></p>
<ol style="color: #666;">
    <li>Review the reason for rejection above</li>
    <li>Prepare the required documents</li>
    <li>Resubmit your KYC information</li>
</ol>

<a href="{$this->siteUrl}/profile/kyc" class="btn">Resubmit KYC Documents</a>

<p><strong>Need Help?</strong> Our support team is here to assist you. Contact us at {$this->siteEmail} or call {$this->supportPhone}.</p>

<p>Best regards,<br><strong>{$this->siteName} Compliance Team</strong></p>
HTML;
        
        return $this->render('KYC Submission - Action Required', $content);
    }
    
    /**
     * Card Application Approved Email
     */
    public function cardApprovedEmail($recipientName, $cardType, $cardName) {
        $content = <<<HTML
<h2>Card Application Approved</h2>
<p>Hello {$recipientName},</p>
<p>Congratulations! Your card application has been approved.</p>

<div class="success-box">
    <p style="margin: 0;"><strong>✓ Application Approved</strong></p>
    <p style="margin: 5px 0 0 0;">Your new card is ready to use.</p>
</div>

<div class="transaction-details">
    <table>
        <tr>
            <td>Card Type:</td>
            <td><strong>{$cardType}</strong></td>
        </tr>
        <tr>
            <td>Card Name:</td>
            <td><strong>{$cardName}</strong></td>
        </tr>
        <tr>
            <td>Status:</td>
            <td><strong style="color: #10b981;">Active</strong></td>
        </tr>
        <tr>
            <td>Approval Date:</td>
            <td><strong>{$this->formatDate(date('Y-m-d H:i:s'))}</strong></td>
        </tr>
    </table>
</div>

<a href="{$this->siteUrl}/card" class="btn">View Your Card</a>

<div class="info-box">
    <p style="margin: 0;"><strong>Getting Started:</strong></p>
    <p style="margin: 5px 0 0 0;">Your card details are available in your account. You can set spending limits, freeze/unfreeze your card, and view transactions anytime.</p>
</div>

<p>Thank you for choosing {$this->siteName}.</p>

<p>Best regards,<br><strong>{$this->siteName} Card Services</strong></p>
HTML;
        
        return $this->render('Card Application Approved - ' . $this->siteName, $content);
    }
    
    /**
     * Card Application Rejected Email
     */
    public function cardRejectedEmail($recipientName, $cardType, $reason) {
        $content = <<<HTML
<h2>Card Application Update</h2>
<p>Hello {$recipientName},</p>
<p>Thank you for your interest in applying for a {$cardType} card with {$this->siteName}.</p>

<div class="warning-box">
    <p style="margin: 0;"><strong>Application Status: Not Approved</strong></p>
    <p style="margin: 5px 0 0 0;">We're unable to approve your card application at this time.</p>
</div>

<div class="info-box">
    <p style="margin: 0;"><strong>Reason:</strong></p>
    <p style="margin: 10px 0 0 0;">{$reason}</p>
</div>

<p><strong>What You Can Do:</strong></p>
<ul style="color: #666;">
    <li>Review your account status and transaction history</li>
    <li>Ensure your KYC verification is complete</li>
    <li>Build your account history with regular transactions</li>
    <li>Reapply after 30 days</li>
</ul>

<p>This decision doesn't affect your current account or existing services. You can continue using all your account features as usual.</p>

<p>If you have questions, please contact our support team at {$this->siteEmail} or call {$this->supportPhone}.</p>

<p>Best regards,<br><strong>{$this->siteName} Card Services</strong></p>
HTML;
        
        return $this->render('Card Application Update - ' . $this->siteName, $content);
    }
    
    /**
     * Loan Application Approved Email
     */
    public function loanApprovedEmail($recipientName, $loanType, $approvedAmount, $currency, $interestRate, $termMonths, $amountFromCurrency = null) {
        require_once __DIR__ . '/functions.php';
        $displayCurrency = strtoupper(trim((string)$currency));
        $amountFrom = strtoupper(trim((string)($amountFromCurrency ?: DEFAULT_CURRENCY)));
        $formattedAmount = formatCurrency($approvedAmount, $displayCurrency, $amountFrom);
        
        $content = <<<HTML
<h2>Loan Application Approved</h2>
<p>Hello {$recipientName},</p>
<p>Excellent news! Your loan application has been approved.</p>

<div class="success-box">
    <p style="margin: 0;"><strong>✓ Loan Approved & Disbursed</strong></p>
    <p style="margin: 5px 0 0 0;">The funds have been credited to your account and are ready to use.</p>
</div>

<div class="transaction-details">
    <table>
        <tr>
            <td>Loan Type:</td>
            <td><strong>{$loanType}</strong></td>
        </tr>
        <tr>
            <td>Approved Amount:</td>
            <td><strong>{$formattedAmount}</strong></td>
        </tr>
        <tr>
            <td>Interest Rate:</td>
            <td><strong>{$interestRate}%</strong></td>
        </tr>
        <tr>
            <td>Loan Term:</td>
            <td><strong>{$termMonths} months</strong></td>
        </tr>
        <tr>
            <td>Approval Date:</td>
            <td><strong>{$this->formatDate(date('Y-m-d H:i:s'))}</strong></td>
        </tr>
    </table>
</div>

<a href="{$this->siteUrl}/loan" class="btn">View Loan Details</a>

<div class="info-box">
    <p style="margin: 0;"><strong>Important Information:</strong></p>
    <ul style="margin: 10px 0 0 0; padding-left: 20px;">
        <li>Your first payment is due next month</li>
        <li>You can view your full payment schedule in your account</li>
        <li>Early repayment options are available</li>
        <li>Make payments anytime from your dashboard</li>
    </ul>
</div>

<p>Thank you for choosing {$this->siteName} for your financing needs.</p>

<p>Best regards,<br><strong>{$this->siteName} Loan Department</strong></p>
HTML;
        
        return $this->render('Loan Approved - ' . $this->siteName, $content);
    }
    
    /**
     * Loan Application Rejected Email
     */
    public function loanRejectedEmail($recipientName, $loanType, $requestedAmount, $currency, $reason, $amountFromCurrency = null) {
        require_once __DIR__ . '/functions.php';
        $displayCurrency = strtoupper(trim((string)$currency));
        $amountFrom = strtoupper(trim((string)($amountFromCurrency ?: DEFAULT_CURRENCY)));
        $formattedAmount = formatCurrency($requestedAmount, $displayCurrency, $amountFrom);
        
        $content = <<<HTML
<h2>Loan Application Update</h2>
<p>Hello {$recipientName},</p>
<p>Thank you for your {$loanType} loan application with {$this->siteName}.</p>

<div class="warning-box">
    <p style="margin: 0;"><strong>Application Status: Not Approved</strong></p>
    <p style="margin: 5px 0 0 0;">We're unable to approve your loan request for {$formattedAmount} at this time.</p>
</div>

<div class="info-box">
    <p style="margin: 0;"><strong>Reason:</strong></p>
    <p style="margin: 10px 0 0 0;">{$reason}</p>
</div>

<p><strong>Next Steps:</strong></p>
<ul style="color: #666;">
    <li>Review and improve your credit profile</li>
    <li>Maintain regular account activity</li>
    <li>Ensure all KYC documents are verified</li>
    <li>Consider applying for a smaller amount</li>
    <li>Reapply after 60 days</li>
</ul>

<p>This decision doesn't affect your current account or services. You can continue using all existing features.</p>

<p>For personalized assistance, contact our loan officers at {$this->siteEmail} or call {$this->supportPhone}.</p>

<p>Best regards,<br><strong>{$this->siteName} Loan Department</strong></p>
HTML;
        
        return $this->render('Loan Application Update - ' . $this->siteName, $content);
    }
    
    /**
     * Loan Application Submitted Email
     */
    public function loanApplicationSubmittedEmail($recipientName, $loanType, $loanAmount, $currency, $interestRate, $termMonths, $applicationId, $amountFromCurrency = null) {
        require_once __DIR__ . '/functions.php';
        $displayCurrency = strtoupper(trim((string)$currency));
        $amountFrom = strtoupper(trim((string)($amountFromCurrency ?: DEFAULT_CURRENCY)));
        $formattedAmount = formatCurrency($loanAmount, $displayCurrency, $amountFrom);
        
        $content = <<<HTML
<h2>Loan Application Received</h2>
<p>Hello {$recipientName},</p>
<p>Thank you for applying for a {$loanType} loan with {$this->siteName}. We've successfully received your application and it's now under review.</p>

<div class="info-box">
    <p style="margin: 0;"><strong>✓ Application Submitted</strong></p>
    <p style="margin: 5px 0 0 0;">Your application has been received and is being processed by our loan department.</p>
</div>

<div class="transaction-details">
    <table>
        <tr>
            <td>Application ID:</td>
            <td><strong>#{$applicationId}</strong></td>
        </tr>
        <tr>
            <td>Loan Type:</td>
            <td><strong>{$loanType}</strong></td>
        </tr>
        <tr>
            <td>Requested Amount:</td>
            <td><strong>{$formattedAmount}</strong></td>
        </tr>
        <tr>
            <td>Interest Rate:</td>
            <td><strong>{$interestRate}%</strong></td>
        </tr>
        <tr>
            <td>Loan Term:</td>
            <td><strong>{$termMonths} months</strong></td>
        </tr>
        <tr>
            <td>Application Date:</td>
            <td><strong>{$this->formatDate(date('Y-m-d H:i:s'))}</strong></td>
        </tr>
        <tr>
            <td>Status:</td>
            <td><strong style="color: #f59e0b;">Pending Review</strong></td>
        </tr>
    </table>
</div>

<a href="{$this->siteUrl}/loan/view/{$applicationId}" class="btn">View Application Status</a>

<div class="info-box">
    <p style="margin: 0;"><strong>What Happens Next?</strong></p>
    <ul style="margin: 10px 0 0 0; padding-left: 20px;">
        <li>Our loan team will review your application</li>
        <li>We may contact you for additional information</li>
        <li>You'll receive an email notification when a decision is made</li>
        <li>Approval typically takes 2-5 business days</li>
    </ul>
</div>

<div class="warning-box">
    <p style="margin: 0;"><strong>⚠️ Security Reminder:</strong></p>
    <p style="margin: 5px 0 0 0;">If you didn't submit this application, please contact our support team immediately at {$this->siteEmail} or call {$this->supportPhone}.</p>
</div>

<p>We appreciate your interest in {$this->siteName} and look forward to serving you.</p>

<p>Best regards,<br><strong>{$this->siteName} Loan Department</strong></p>
HTML;
        
        return $this->render('Loan Application Received - ' . $this->siteName, $content);
    }
    
    /**
     * Card Application Submitted Email
     */
    public function cardApplicationSubmittedEmail($recipientName, $cardType, $cardName, $applicationId) {
        $content = <<<HTML
<h2>Card Application Received</h2>
<p>Hello {$recipientName},</p>
<p>Thank you for applying for a {$cardType} card with {$this->siteName}. We've successfully received your application and it's now under review.</p>

<div class="info-box">
    <p style="margin: 0;"><strong>✓ Application Submitted</strong></p>
    <p style="margin: 5px 0 0 0;">Your card application has been received and is being processed by our card services team.</p>
</div>

<div class="transaction-details">
    <table>
        <tr>
            <td>Application ID:</td>
            <td><strong>#{$applicationId}</strong></td>
        </tr>
        <tr>
            <td>Card Type:</td>
            <td><strong>{$cardType}</strong></td>
        </tr>
        <tr>
            <td>Card Name:</td>
            <td><strong>{$cardName}</strong></td>
        </tr>
        <tr>
            <td>Application Date:</td>
            <td><strong>{$this->formatDate(date('Y-m-d H:i:s'))}</strong></td>
        </tr>
        <tr>
            <td>Status:</td>
            <td><strong style="color: #f59e0b;">Pending Review</strong></td>
        </tr>
    </table>
</div>

<a href="{$this->siteUrl}/card/view/{$applicationId}" class="btn">View Application Status</a>

<div class="info-box">
    <p style="margin: 0;"><strong>What Happens Next?</strong></p>
    <ul style="margin: 10px 0 0 0; padding-left: 20px;">
        <li>Our card services team will review your application</li>
        <li>We may verify your account information and KYC status</li>
        <li>You'll receive an email notification when a decision is made</li>
        <li>Approval typically takes 1-3 business days</li>
    </ul>
</div>

<div class="warning-box">
    <p style="margin: 0;"><strong>⚠️ Security Reminder:</strong></p>
    <p style="margin: 5px 0 0 0;">If you didn't submit this application, please contact our support team immediately at {$this->siteEmail} or call {$this->supportPhone}.</p>
</div>

<p>We appreciate your interest in {$this->siteName} and look forward to serving you.</p>

<p>Best regards,<br><strong>{$this->siteName} Card Services</strong></p>
HTML;
        
        return $this->render('Card Application Received - ' . $this->siteName, $content);
    }
    
    /**
     * Login Alert Email
     */
    public function loginAlertEmail($recipientName, $ipAddress, $device, $location) {
        $content = <<<HTML
<h2>New Login Detected</h2>
<p>Hello {$recipientName},</p>
<p>We detected a new login to your {$this->siteName} account.</p>

<div class="info-box">
    <p style="margin: 0;"><strong>Login Details:</strong></p>
</div>

<div class="transaction-details">
    <table>
        <tr>
            <td>Date & Time:</td>
            <td><strong>{$this->formatDate(date('Y-m-d H:i:s'))}</strong></td>
        </tr>
        <tr>
            <td>IP Address:</td>
            <td><strong>{$ipAddress}</strong></td>
        </tr>
        <tr>
            <td>Device:</td>
            <td><strong>{$device}</strong></td>
        </tr>
        <tr>
            <td>Location:</td>
            <td><strong>{$location}</strong></td>
        </tr>
    </table>
</div>

<div class="warning-box">
    <p style="margin: 0;"><strong>⚠️ Was This You?</strong></p>
    <p style="margin: 5px 0 0 0;">If you didn't log in, please secure your account immediately by changing your password and contacting us.</p>
</div>

<a href="{$this->siteUrl}/profile/security" class="btn">Secure My Account</a>

<p><strong>Security Tips:</strong></p>
<ul style="color: #666;">
    <li>Always log out after using public or shared devices</li>
    <li>Enable two-factor authentication</li>
    <li>Use strong, unique passwords</li>
    <li>Never share your login credentials</li>
</ul>

<p>Best regards,<br><strong>{$this->siteName} Security Team</strong></p>
HTML;
        
        return $this->render('Login Alert - ' . $this->siteName, $content, '<p style="color: #999;">This is an automated security notification.</p>');
    }
    
    /**
     * Helper function to format dates
     */
    private function formatDate($date) {
        return date('F j, Y g:i A', strtotime($date));
    }
}
