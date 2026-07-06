<?php
/**
 * Get Inbox Emails API
 * Fetches emails from IMAP inbox and spam folders
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
    
    $folder = $_GET['folder'] ?? 'INBOX'; // INBOX or SPAM
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 20;
    $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
    
    // Validate folder
    if (!in_array($folder, ['INBOX', 'SPAM'])) {
        $folder = 'INBOX';
    }
    
    // Check if IMAP extension is available
    if (!function_exists('imap_open')) {
        ob_end_clean();
        echo json_encode([
            'success' => false,
            'message' => 'IMAP extension is not enabled on this server. Please contact your hosting provider.'
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
    
    // Get total number of messages
    $totalMessages = imap_num_msg($connection);
    
    // Calculate range (most recent first)
    $start = max(1, $totalMessages - $offset - $limit + 1);
    $end = $totalMessages - $offset;
    
    $emails = [];
    
    if ($start <= $end && $end > 0) {
        // Fetch messages (most recent first)
        for ($msgno = $end; $msgno >= $start; $msgno--) {
            try {
                // Get header
                $header = imap_headerinfo($connection, $msgno);
                
                if (!$header) {
                    continue;
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
                
                $subject = isset($header->subject) ? imap_utf8($header->subject) : '(No Subject)';
                $date = isset($header->date) ? $header->date : '';
                $messageId = isset($header->message_id) ? $header->message_id : '';
                $unseen = isset($header->Unseen) ? $header->Unseen : false;
                
                // Get body preview (first 200 chars)
                $body = '';
                $structure = imap_fetchstructure($connection, $msgno);
                if ($structure) {
                    $bodyText = imap_body($connection, $msgno, FT_PEEK);
                    if ($bodyText) {
                        // Try to decode
                        $body = imap_utf8($bodyText);
                        // Strip HTML tags for preview
                        $body = strip_tags($body);
                        // Get first 200 chars
                        $body = mb_substr($body, 0, 200);
                    }
                }
                
                $emails[] = [
                    'id' => $msgno,
                    'from' => [
                        'name' => $fromName,
                        'email' => $fromEmail
                    ],
                    'reply_to' => [
                        'name' => $replyToName,
                        'email' => $replyToEmail
                    ],
                    'subject' => $subject,
                    'date' => $date,
                    'preview' => $body,
                    'unread' => $unseen,
                    'message_id' => $messageId
                ];
                
            } catch (Exception $e) {
                error_log("Error fetching email $msgno: " . $e->getMessage());
                continue;
            }
        }
    }
    
    // Close connection
    imap_close($connection);
    
    ob_end_clean();
    echo json_encode([
        'success' => true,
        'emails' => $emails,
        'total' => $totalMessages,
        'folder' => $folder,
        'count' => count($emails)
    ]);
    
} catch (Exception $e) {
    ob_end_clean();
    error_log('Get inbox emails error: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
} catch (Error $e) {
    ob_end_clean();
    error_log('Get inbox emails fatal error: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
    echo json_encode([
        'success' => false,
        'message' => 'A fatal error occurred: ' . $e->getMessage()
    ]);
}

