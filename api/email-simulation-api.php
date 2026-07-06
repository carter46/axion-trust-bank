<?php
/**
 * Email Simulation API
 * Handles CRUD operations for alert captions and email templates
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Check admin authentication
requireAdmin();

// Set JSON header
header('Content-Type: application/json');

// Get action from request (support both GET and POST)
$action = $_GET['action'] ?? null;

// For POST requests, get data from JSON or form data
$postData = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    if (strpos($contentType, 'application/json') !== false) {
        $rawInput = file_get_contents('php://input');
        $postData = json_decode($rawInput, true) ?? [];
        $action = $postData['action'] ?? $action;
    } else {
        $postData = $_POST;
        $action = $postData['action'] ?? $action;
    }
}

if (!$action) {
    echo json_encode(['success' => false, 'message' => 'No action specified']);
    exit;
}

$db = Database::getInstance();
$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

try {
    switch ($action) {
        case 'list-captions':
            $sql = "SELECT * FROM email_simulation_alert_captions WHERE is_active = 1 ORDER BY caption_text ASC";
            $stmt = $db->query($sql);
            $captions = $stmt ? $stmt->fetchAll() : [];
            echo json_encode(['success' => true, 'data' => $captions]);
            break;
            
        case 'list-templates':
            $sql = "SELECT * FROM email_simulation_templates WHERE is_active = 1 ORDER BY template_name ASC";
            $stmt = $db->query($sql);
            $templates = $stmt ? $stmt->fetchAll() : [];
            echo json_encode(['success' => true, 'data' => $templates]);
            break;
            
        case 'add-caption':
            $captionText = trim($postData['caption_text'] ?? '');
            
            if (empty($captionText)) {
                echo json_encode(['success' => false, 'message' => 'Caption text is required']);
                exit;
            }
            
            if (strlen($captionText) > 255) {
                echo json_encode(['success' => false, 'message' => 'Caption text must be 255 characters or less']);
                exit;
            }
            
            // Check if caption already exists
            $checkSql = "SELECT id FROM email_simulation_alert_captions WHERE caption_text = ?";
            $checkStmt = $db->query($checkSql, [$captionText]);
            if ($checkStmt && $checkStmt->rowCount() > 0) {
                echo json_encode(['success' => false, 'message' => 'This caption already exists']);
                exit;
            }
            
            $sql = "INSERT INTO email_simulation_alert_captions (caption_text, is_active) VALUES (?, 1)";
            $stmt = $db->query($sql, [$captionText]);
            
            if ($stmt) {
                logActivity($userId, 'EMAIL_SIMULATION', "Added alert caption: $captionText");
                echo json_encode(['success' => true, 'message' => 'Alert caption added successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add alert caption']);
            }
            break;
            
        case 'update-caption':
            $id = intval($postData['id'] ?? 0);
            $captionText = trim($postData['caption_text'] ?? '');
            $isActive = intval($postData['is_active'] ?? 1);
            
            if (empty($id)) {
                echo json_encode(['success' => false, 'message' => 'Caption ID is required']);
                exit;
            }
            
            if (empty($captionText)) {
                echo json_encode(['success' => false, 'message' => 'Caption text is required']);
                exit;
            }
            
            if (strlen($captionText) > 255) {
                echo json_encode(['success' => false, 'message' => 'Caption text must be 255 characters or less']);
                exit;
            }
            
            // Check if caption already exists (excluding current one)
            $checkSql = "SELECT id FROM email_simulation_alert_captions WHERE caption_text = ? AND id != ?";
            $checkStmt = $db->query($checkSql, [$captionText, $id]);
            if ($checkStmt && $checkStmt->rowCount() > 0) {
                echo json_encode(['success' => false, 'message' => 'This caption already exists']);
                exit;
            }
            
            $sql = "UPDATE email_simulation_alert_captions SET caption_text = ?, is_active = ? WHERE id = ?";
            $stmt = $db->query($sql, [$captionText, $isActive, $id]);
            
            if ($stmt) {
                logActivity($userId, 'EMAIL_SIMULATION', "Updated alert caption ID $id: $captionText");
                echo json_encode(['success' => true, 'message' => 'Alert caption updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update alert caption']);
            }
            break;
            
        case 'delete-caption':
            $id = intval($postData['id'] ?? 0);
            
            if (empty($id)) {
                echo json_encode(['success' => false, 'message' => 'Caption ID is required']);
                exit;
            }
            
            // Soft delete (set is_active to 0)
            $sql = "UPDATE email_simulation_alert_captions SET is_active = 0 WHERE id = ?";
            $stmt = $db->query($sql, [$id]);
            
            if ($stmt) {
                logActivity($userId, 'EMAIL_SIMULATION', "Deleted alert caption ID $id");
                echo json_encode(['success' => true, 'message' => 'Alert caption deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete alert caption']);
            }
            break;
            
        case 'add-template':
            $templateName = trim($postData['template_name'] ?? '');
            $templateType = trim($postData['template_type'] ?? 'simple');
            $primaryColor = trim($postData['primary_color'] ?? '#359eb4');
            $secondaryColor = trim($postData['secondary_color'] ?? '#2a7e90');
            $accentColor = trim($postData['accent_color'] ?? '#10b981');
            $logoUrl = trim($postData['logo_url'] ?? '');
            $logoAltText = trim($postData['logo_alt_text'] ?? 'Bank Logo');
            $address = trim($postData['address'] ?? '');
            $isActive = intval($postData['is_active'] ?? 1);
            
            if (!in_array($templateType, ['simple', 'advanced'])) {
                $templateType = 'simple';
            }
            
            if (empty($templateName)) {
                echo json_encode(['success' => false, 'message' => 'Template name is required']);
                exit;
            }
            
            if (strlen($templateName) > 100) {
                echo json_encode(['success' => false, 'message' => 'Template name must be 100 characters or less']);
                exit;
            }
            
            // Validate color format
            if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $primaryColor) || 
                !preg_match('/^#[0-9A-Fa-f]{6}$/', $secondaryColor) || 
                !preg_match('/^#[0-9A-Fa-f]{6}$/', $accentColor)) {
                echo json_encode(['success' => false, 'message' => 'Invalid color format. Use hex format (#RRGGBB)']);
                exit;
            }
            
            // Check if template name already exists
            $checkSql = "SELECT id FROM email_simulation_templates WHERE template_name = ?";
            $checkStmt = $db->query($checkSql, [$templateName]);
            if ($checkStmt && $checkStmt->rowCount() > 0) {
                echo json_encode(['success' => false, 'message' => 'Template name already exists']);
                exit;
            }
            
            $sql = "INSERT INTO email_simulation_templates 
                    (template_name, template_type, primary_color, secondary_color, accent_color, logo_url, logo_alt_text, address, is_active) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $db->query($sql, [
                $templateName,
                $templateType,
                $primaryColor, 
                $secondaryColor, 
                $accentColor, 
                $logoUrl ?: null, 
                $logoAltText,
                !empty($address) ? $address : null,
                $isActive
            ]);
            
            if ($stmt) {
                logActivity($userId, 'EMAIL_SIMULATION', "Added email template: $templateName");
                echo json_encode(['success' => true, 'message' => 'Template added successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add template']);
            }
            break;
            
        case 'update-template':
            $id = intval($postData['id'] ?? 0);
            $templateName = trim($postData['template_name'] ?? '');
            $templateType = trim($postData['template_type'] ?? 'simple');
            $primaryColor = trim($postData['primary_color'] ?? '#359eb4');
            $secondaryColor = trim($postData['secondary_color'] ?? '#2a7e90');
            $accentColor = trim($postData['accent_color'] ?? '#10b981');
            $logoUrl = trim($postData['logo_url'] ?? '');
            $logoAltText = trim($postData['logo_alt_text'] ?? 'Bank Logo');
            $address = trim($postData['address'] ?? '');
            $isActive = intval($postData['is_active'] ?? 1);
            
            if (!in_array($templateType, ['simple', 'advanced'])) {
                $templateType = 'simple';
            }
            
            if (empty($id)) {
                echo json_encode(['success' => false, 'message' => 'Template ID is required']);
                exit;
            }
            
            if (empty($templateName)) {
                echo json_encode(['success' => false, 'message' => 'Template name is required']);
                exit;
            }
            
            if (strlen($templateName) > 100) {
                echo json_encode(['success' => false, 'message' => 'Template name must be 100 characters or less']);
                exit;
            }
            
            // Validate color format
            if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $primaryColor) || 
                !preg_match('/^#[0-9A-Fa-f]{6}$/', $secondaryColor) || 
                !preg_match('/^#[0-9A-Fa-f]{6}$/', $accentColor)) {
                echo json_encode(['success' => false, 'message' => 'Invalid color format. Use hex format (#RRGGBB)']);
                exit;
            }
            
            // Check if template name already exists (excluding current one)
            $checkSql = "SELECT id FROM email_simulation_templates WHERE template_name = ? AND id != ?";
            $checkStmt = $db->query($checkSql, [$templateName, $id]);
            if ($checkStmt && $checkStmt->rowCount() > 0) {
                echo json_encode(['success' => false, 'message' => 'Template name already exists']);
                exit;
            }
            
            $sql = "UPDATE email_simulation_templates 
                    SET template_name = ?, template_type = ?, primary_color = ?, secondary_color = ?, accent_color = ?, 
                        logo_url = ?, logo_alt_text = ?, address = ?, is_active = ? 
                    WHERE id = ?";
            $stmt = $db->query($sql, [
                $templateName,
                $templateType,
                $primaryColor, 
                $secondaryColor, 
                $accentColor, 
                $logoUrl ?: null, 
                $logoAltText,
                !empty($address) ? $address : null,
                $isActive,
                $id
            ]);
            
            if ($stmt) {
                logActivity($userId, 'EMAIL_SIMULATION', "Updated email template ID $id: $templateName");
                echo json_encode(['success' => true, 'message' => 'Template updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update template']);
            }
            break;
            
        case 'delete-template':
            $id = intval($postData['id'] ?? 0);
            
            if (empty($id)) {
                echo json_encode(['success' => false, 'message' => 'Template ID is required']);
                exit;
            }
            
            // Soft delete (set is_active to 0)
            $sql = "UPDATE email_simulation_templates SET is_active = 0 WHERE id = ?";
            $stmt = $db->query($sql, [$id]);
            
            if ($stmt) {
                logActivity($userId, 'EMAIL_SIMULATION', "Deleted email template ID $id");
                echo json_encode(['success' => true, 'message' => 'Template deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete template']);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    error_log('Email Simulation API Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}

