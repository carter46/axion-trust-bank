<?php
/**
 * Send Custom Email API
 * Allows admin to send custom emails to users or external email addresses
 */

// Start output buffering to catch any errors
ob_start();

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set JSON header FIRST
header('Content-Type: application/json');

// Enable error logging but don't display errors
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Check authentication
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Check if POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    // Log that we're starting
    error_log('Send custom email API: Starting request');
    
    require_once __DIR__ . '/../config/config.php';
    require_once __DIR__ . '/../includes/functions.php';
    require_once __DIR__ . '/../includes/system-settings.php';
    require_once __DIR__ . '/../includes/email-template.php';
    
    // Set up autoloader for models (API is called directly, not through router)
    if (!class_exists('User')) {
        spl_autoload_register(function ($class_name) {
            $paths = [
                'models/',
                'controllers/',
                'classes/'
            ];
            
            foreach ($paths as $path) {
                $file = BASE_PATH . '/' . $path . $class_name . '.php';
                if (file_exists($file)) {
                    require_once $file;
                    return;
                }
            }
        });
    }
    
    // Clear any output buffer
    ob_clean();
    
    error_log('Send custom email API: Files loaded successfully');
    
    // Get input
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);
    
    if (!$input) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
        exit;
    }
    
    $recipient = $input['recipient'] ?? null;
    $subject = trim($input['subject'] ?? '');
    $content = trim($input['content'] ?? '');
    
    // Validate input
    if (!$recipient || !$subject || !$content) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }
    
    $db = Database::getInstance();
    $userId = $_SESSION['user_id'];
    $systemSettings = SystemSettings::getInstance();
    $siteName = $systemSettings->get('site_name', 'SecureBank Online');
    $emailTemplate = new EmailTemplate();
    
    $sentCount = 0;
    $errors = [];
    $recipientType = $recipient['type'] ?? '';
    
    // Get email template wrapper
    function wrapEmailContent($content, $siteName, $emailTemplate) {
        // Use the render method to wrap content in branded template
        $htmlContent = $emailTemplate->render('Custom Message', $content, '');
        return $htmlContent;
    }
    
    if ($recipientType === 'single') {
        // Send to single user
        $user_id = intval($recipient['user_id'] ?? 0);
        
        if (!$user_id) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
            exit;
        }
        
        error_log('Send custom email API: Loading user with ID: ' . $user_id);
        
        $userModel = new User();
        $user = $userModel->findById($user_id);
        
        if (!$user) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'User not found']);
            exit;
        }
        
        error_log('Send custom email API: User found: ' . $user['email']);
        
        // Personalize content (replace {name} with user's name)
        $personalizedContent = str_replace('{name}', $user['full_name'], $content);
        $personalizedContent = str_replace('{full_name}', $user['full_name'], $personalizedContent);
        
        $htmlContent = wrapEmailContent($personalizedContent, $siteName, $emailTemplate);
        
        if (sendEmail($user['email'], $subject, $htmlContent, true)) {
            $sentCount++;
            
            // Log activity
            logActivity($userId, 'ADMIN_SEND_EMAIL', "Sent custom email to user: {$user['email']}");
        } else {
            $errors[] = "Failed to send to {$user['email']}";
        }
        
    } elseif ($recipientType === 'all') {
        // Send to all active users
        $sql = "SELECT id, full_name, email FROM users WHERE role = 'user' AND status = 'active'";
        $stmt = $db->query($sql);
        $users = $stmt->fetchAll();
        
        foreach ($users as $user) {
            // Personalize content for each user
            $personalizedContent = str_replace('{name}', $user['full_name'], $content);
            $personalizedContent = str_replace('{full_name}', $user['full_name'], $personalizedContent);
            
            $htmlContent = wrapEmailContent($personalizedContent, $siteName, $emailTemplate);
            
            if (sendEmail($user['email'], $subject, $htmlContent, true)) {
                $sentCount++;
            } else {
                $errors[] = "Failed to send to {$user['email']}";
            }
        }
        
        // Log activity
        logActivity($userId, 'ADMIN_SEND_BULK_EMAIL', "Sent bulk email to {$sentCount} users");
        
    } elseif ($recipientType === 'external') {
        // Send to external email addresses
        $emails = $recipient['emails'] ?? [];
        
        if (empty($emails) || !is_array($emails)) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'No email addresses provided']);
            exit;
        }
        
        $htmlContent = wrapEmailContent($content, $siteName, $emailTemplate);
        
        foreach ($emails as $email) {
            $email = trim($email);
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid email: {$email}";
                continue;
            }
            
            if (sendEmail($email, $subject, $htmlContent, true)) {
                $sentCount++;
            } else {
                $errors[] = "Failed to send to {$email}";
            }
        }
        
        // Log activity
        logActivity($userId, 'ADMIN_SEND_EXTERNAL_EMAIL', "Sent email to {$sentCount} external address(es)");
        
    } else {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Invalid recipient type']);
        exit;
    }
    
    // Return result
    ob_end_clean();
    if ($sentCount > 0) {
        echo json_encode([
            'success' => true,
            'message' => "Email sent successfully to {$sentCount} recipient(s)",
            'sent_count' => $sentCount,
            'errors' => $errors
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to send email to any recipients',
            'errors' => $errors
        ]);
    }
    
} catch (Exception $e) {
    ob_end_clean();
    $errorMsg = $e->getMessage();
    $errorFile = $e->getFile();
    $errorLine = $e->getLine();
    
    error_log('Send custom email Exception: ' . $errorMsg);
    error_log('Error file: ' . $errorFile);
    error_log('Error line: ' . $errorLine);
    error_log('Stack trace: ' . $e->getTraceAsString());
    
    // Return detailed error for debugging (you can remove details in production)
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $errorMsg,
        'error_file' => basename($errorFile),
        'error_line' => $errorLine
    ]);
} catch (Error $e) {
    ob_end_clean();
    $errorMsg = $e->getMessage();
    $errorFile = $e->getFile();
    $errorLine = $e->getLine();
    
    error_log('Send custom email Fatal Error: ' . $errorMsg);
    error_log('Error file: ' . $errorFile);
    error_log('Error line: ' . $errorLine);
    error_log('Stack trace: ' . $e->getTraceAsString());
    
    // Return detailed error for debugging
    echo json_encode([
        'success' => false,
        'message' => 'A fatal error occurred: ' . $errorMsg,
        'error_file' => basename($errorFile),
        'error_line' => $errorLine,
        'error_type' => get_class($e)
    ]);
} catch (Throwable $e) {
    ob_end_clean();
    $errorMsg = $e->getMessage();
    $errorFile = $e->getFile();
    $errorLine = $e->getLine();
    
    error_log('Send custom email Throwable: ' . $errorMsg);
    error_log('Error file: ' . $errorFile);
    error_log('Error line: ' . $errorLine);
    error_log('Stack trace: ' . $e->getTraceAsString());
    
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred: ' . $errorMsg,
        'error_file' => basename($errorFile),
        'error_line' => $errorLine,
        'error_type' => get_class($e)
    ]);
}

