<?php
/**
 * Get Email Content API
 * Fetches full content of a specific email
 */

// Prevent output before JSON
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set JSON header FIRST
header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    require_once __DIR__ . '/../config/config.php';
    require_once __DIR__ . '/../includes/functions.php';
    
    // Clear any output buffer
    ob_clean();
    
    $messageId = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $folder = $_GET['folder'] ?? 'INBOX';
    
    if (!$messageId) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Message ID required']);
        exit;
    }
    
    // Validate folder
    if (!in_array($folder, ['INBOX', 'SPAM'])) {
        $folder = 'INBOX';
    }
    
    // Check if IMAP extension is available
    if (!function_exists('imap_open')) {
        ob_end_clean();
        echo json_encode([
            'success' => false,
            'message' => 'IMAP extension is not enabled on this server.'
        ]);
        exit;
    }
    
    // Connect to IMAP server
    $imapUser = IMAP_USER;
    $imapPass = IMAP_PASS;
    $imapHost = IMAP_HOST;
    $imapPort = IMAP_PORT;
    
    // Build IMAP connection string
    $mailbox = "{{$imapHost}:{$imapPort}/imap/ssl}";
    
    // Open connection
    $connection = @imap_open($mailbox . $folder, $imapUser, $imapPass);
    
    if (!$connection) {
        $error = imap_last_error();
        ob_end_clean();
        echo json_encode([
            'success' => false,
            'message' => 'Failed to connect to email server: ' . ($error ?: 'Unknown error')
        ]);
        exit;
    }
    
    // Get header
    $header = imap_headerinfo($connection, $messageId);
    
    if (!$header) {
        imap_close($connection);
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Email not found']);
        exit;
    }
    
    // Extract email data
    $from = isset($header->from[0]) ? $header->from[0] : null;
    $fromEmail = $from ? ($from->mailbox . '@' . $from->host) : 'Unknown';
    $fromName = $from && isset($from->personal) ? $from->personal : $fromEmail;
    
    // Check for Reply-To header (for contact form emails)
    $replyToEmail = $fromEmail; // Default to From email
    $replyToName = $fromName;
    if (isset($header->reply_to) && is_array($header->reply_to) && count($header->reply_to) > 0) {
        $replyTo = $header->reply_to[0];
        $replyToEmail = $replyTo->mailbox . '@' . $replyTo->host;
        $replyToName = isset($replyTo->personal) ? $replyTo->personal : $replyToEmail;
    }
    
    $to = isset($header->to) ? $header->to : [];
    $toEmails = [];
    if (is_array($to)) {
        foreach ($to as $addr) {
            $toEmails[] = $addr->mailbox . '@' . $addr->host;
        }
    }
    
    $subject = isset($header->subject) ? imap_utf8($header->subject) : '(No Subject)';
    $date = isset($header->date) ? $header->date : '';
    
    // Get structure to determine content type
    $structure = imap_fetchstructure($connection, $messageId);
    $isHtml = false;
    $body = '';
    
    // Helper function to decode body part
    function decodeBody($connection, $messageId, $partNum, $encoding) {
        $body = imap_fetchbody($connection, $messageId, $partNum);
        
        switch ($encoding) {
            case 0: // 7BIT
            case 1: // 8BIT
                return $body;
            case 2: // BINARY
                return $body;
            case 3: // BASE64
                return base64_decode($body);
            case 4: // QUOTED-PRINTABLE
                return quoted_printable_decode($body);
            case 5: // OTHER
            default:
                return $body;
        }
    }
    
    // Check if multipart
    if (isset($structure->parts) && is_array($structure->parts)) {
        // Try to get HTML part first, then text
        foreach ($structure->parts as $partNum => $part) {
            $subtype = isset($part->subtype) ? strtoupper($part->subtype) : '';
            $encoding = isset($part->encoding) ? $part->encoding : 0;
            
            if ($subtype === 'HTML') {
                $body = decodeBody($connection, $messageId, $partNum + 1, $encoding);
                $isHtml = true;
                break;
            } elseif ($subtype === 'PLAIN' && empty($body)) {
                $body = decodeBody($connection, $messageId, $partNum + 1, $encoding);
            }
        }
    }
    
    // If no body found, get main body
    if (empty($body)) {
        $body = imap_body($connection, $messageId);
        // Check if main body is HTML by looking at structure
        if (isset($structure->subtype) && strtoupper($structure->subtype) === 'HTML') {
            $isHtml = true;
        }
    }
    
    // Decode UTF-8
    $body = imap_utf8($body);
    
    // If not HTML, convert line breaks and escape
    if (!$isHtml) {
        $body = htmlspecialchars($body, ENT_QUOTES, 'UTF-8');
        $body = nl2br($body);
    } else {
        // For HTML emails, decode any HTML entities that might have been double-encoded
        // This handles cases where the email body might have been HTML-encoded
        $body = html_entity_decode($body, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    
    // Mark as read
    imap_setflag_full($connection, $messageId, "\\Seen");
    
    // Close connection
    imap_close($connection);
    
    ob_end_clean();
    echo json_encode([
        'success' => true,
        'email' => [
            'from' => [
                'name' => $fromName,
                'email' => $fromEmail
            ],
            'reply_to' => [
                'name' => $replyToName,
                'email' => $replyToEmail
            ],
            'to' => $toEmails,
            'subject' => $subject,
            'date' => $date,
            'body' => $body,
            'is_html' => $isHtml
        ]
    ]);
    
} catch (Exception $e) {
    ob_end_clean();
    error_log('Get email content error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
} catch (Error $e) {
    ob_end_clean();
    error_log('Get email content fatal error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'A fatal error occurred: ' . $e->getMessage()
    ]);
}

