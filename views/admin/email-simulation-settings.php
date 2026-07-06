<?php
// This file is included as a sub-page, so we don't need full HTML structure
// Check if we're being included or accessed directly
if (!defined('EMAIL_SUBPAGE') || EMAIL_SUBPAGE !== true) {
    $pageTitle = 'Email Simulation Settings - Admin';
    require_once __DIR__ . '/../../config/config.php';
    require_once __DIR__ . '/../../includes/functions.php';
    
    requireAdmin();
    
    include __DIR__ . '/../../includes/head.php';
    include __DIR__ . '/../../includes/admin-sidebar.php';
}

$db = Database::getInstance();

// Get all alert captions
$alertCaptions = [];
try {
    $captionsSql = "SELECT * FROM email_simulation_alert_captions ORDER BY created_at DESC";
    $captionsStmt = $db->query($captionsSql);
    $alertCaptions = $captionsStmt ? $captionsStmt->fetchAll() : [];
} catch (Exception $e) {
    error_log('Error loading alert captions: ' . $e->getMessage());
    $alertCaptions = [];
}

// Get all templates
$templates = [];
try {
    $templatesSql = "SELECT * FROM email_simulation_templates ORDER BY created_at DESC";
    $templatesStmt = $db->query($templatesSql);
    $templates = $templatesStmt ? $templatesStmt->fetchAll() : [];
} catch (Exception $e) {
    error_log('Error loading templates: ' . $e->getMessage());
    $templates = [];
}

// Get site logo from system settings
$systemSettings = SystemSettings::getInstance();
$siteLogo = $systemSettings->get('site_logo', '');
$siteName = $systemSettings->get('site_name', 'Bank');
?>

<style>
    .email-simulation-settings-container {
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .page-header {
        margin-bottom: 30px;
    }
    
    .page-header h1 {
        font-size: 32px;
        font-weight: 700;
        color: #032B44;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .page-header p {
        color: #666;
        font-size: 16px;
        margin: 0;
    }
    
    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: #f3f4f6;
        color: #374151;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s;
        margin-bottom: 20px;
    }
    
    .back-button:hover {
        background: #e5e7eb;
        transform: translateX(-4px);
    }
    
    .settings-card {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        margin-bottom: 24px;
    }
    
    .card-title {
        font-size: 20px;
        color: #1e3a8a;
        font-weight: 600;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-label {
        display: block;
        font-weight: 600;
        color: #202124;
        margin-bottom: 8px;
        font-size: 14px;
    }
    
    .form-input, .form-select, .form-textarea {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #dadce0;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s;
        font-family: inherit;
    }
    
    .form-textarea {
        resize: vertical;
        min-height: 100px;
    }
    
    .form-input:focus, .form-select:focus, .form-textarea:focus {
        outline: none;
        border-color: #1e3a8a;
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
    }
    
    .color-input-group {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
    }
    
    .color-input-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .color-picker {
        width: 60px;
        height: 40px;
        border: 1px solid #dadce0;
        border-radius: 8px;
        cursor: pointer;
        flex-shrink: 0;
    }
    
    .color-input {
        flex: 1;
    }
    
    .btn {
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        color: white;
    }
    
    .btn-primary:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(30, 58, 138, 0.3);
    }
    
    .btn-success {
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        color: white;
    }
    
    .btn-danger {
        background: linear-gradient(135deg, #ef4444 0%, #f87171 100%);
        color: white;
    }
    
    .btn-secondary {
        background: #6b7280;
        color: white;
    }
    
    .btn-sm {
        padding: 8px 16px;
        font-size: 13px;
    }
    
    .items-list {
        margin-top: 20px;
    }
    
    .item-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 12px;
        border-left: 4px solid #1e3a8a;
    }
    
    .item-info {
        flex: 1;
    }
    
    .item-text {
        font-weight: 600;
        color: #202124;
        margin-bottom: 4px;
    }
    
    .item-status {
        font-size: 12px;
        color: #666;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .status-badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .status-active {
        background: #d1fae5;
        color: #065f46;
    }
    
    .status-inactive {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .item-actions {
        display: flex;
        gap: 8px;
    }
    
    .template-card {
        display: grid;
        grid-template-columns: auto 1fr auto;
        gap: 20px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 16px;
        border-left: 4px solid #1e3a8a;
    }
    
    .template-colors {
        display: flex;
        gap: 8px;
        align-items: center;
    }
    
    .color-swatch {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        border: 2px solid #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .template-info {
        flex: 1;
    }
    
    .template-name {
        font-weight: 600;
        color: #202124;
        margin-bottom: 8px;
        font-size: 16px;
    }
    
    .template-details {
        font-size: 13px;
        color: #666;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    
    .template-logo-preview {
        max-width: 60px;
        max-height: 60px;
        border-radius: 8px;
        object-fit: contain;
    }
    
    .alert {
        padding: 16px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }
    
    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border-left: 4px solid #10b981;
    }
    
    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border-left: 4px solid #ef4444;
    }
    
    .help-text {
        font-size: 13px;
        color: #666;
        margin-top: 6px;
    }
    
    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .checkbox-group input[type="checkbox"] {
        width: 20px;
        height: 20px;
        cursor: pointer;
    }
    
    @media (max-width: 768px) {
        .email-simulation-settings-container {
            padding: 15px;
        }
        
        .color-input-group {
            grid-template-columns: 1fr;
        }
        
        .item-row, .template-card {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .item-actions {
            width: 100%;
            justify-content: flex-end;
        }
    }
</style>

<div class="email-simulation-settings-container">
    <a href="<?php echo SITE_URL; ?>/admin/email" class="back-button">
        <i class="fas fa-arrow-left"></i> Back to Email Management
    </a>
    
    <div class="page-header">
        <h1>
            <i class="fas fa-cog"></i>
            Email Simulation Settings
        </h1>
        <p>Manage alert captions and email templates for testing</p>
    </div>
    
    <div id="messageContainer"></div>
    
    <!-- Alert Captions Section -->
    <div class="settings-card">
        <h2 class="card-title">
            <i class="fas fa-heading"></i>
            Alert Captions
        </h2>
        <p style="color: #666; margin-bottom: 20px;">Manage alert caption options that appear in simulation emails</p>
        
        <!-- Add New Caption Form -->
        <form id="addCaptionForm" style="margin-bottom: 30px;">
            <div class="form-group">
                <label class="form-label" for="new_caption_text">Add New Alert Caption *</label>
                <div style="display: flex; gap: 12px;">
                    <input type="text" class="form-input" id="new_caption_text" name="caption_text" 
                           placeholder="e.g., Funds Received" required maxlength="255" style="flex: 1;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add
                    </button>
                </div>
                <p class="help-text">Enter a caption that will appear as the main alert heading in simulation emails</p>
            </div>
        </form>
        
        <!-- Captions List -->
        <div class="items-list" id="captionsList">
            <?php if (empty($alertCaptions)): ?>
                <p style="color: #666; text-align: center; padding: 40px;">No alert captions found. Add one above to get started.</p>
            <?php else: ?>
                <?php foreach ($alertCaptions as $caption): ?>
                    <div class="item-row" data-id="<?php echo $caption['id']; ?>">
                        <div class="item-info">
                            <div class="item-text"><?php echo htmlspecialchars($caption['caption_text']); ?></div>
                            <div class="item-status">
                                <span class="status-badge <?php echo $caption['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                    <?php echo $caption['is_active'] ? 'Active' : 'Inactive'; ?>
                                </span>
                            </div>
                        </div>
                        <div class="item-actions">
                            <button type="button" class="btn btn-secondary btn-sm edit-caption-btn" 
                                    data-id="<?php echo $caption['id']; ?>"
                                    data-text="<?php echo htmlspecialchars($caption['caption_text']); ?>"
                                    data-active="<?php echo $caption['is_active']; ?>">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button type="button" class="btn btn-danger btn-sm delete-caption-btn" 
                                    data-id="<?php echo $caption['id']; ?>">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Email Templates Section -->
    <div class="settings-card">
        <h2 class="card-title">
            <i class="fas fa-palette"></i>
            Email Templates
        </h2>
        <p style="color: #666; margin-bottom: 20px;">Manage email templates with custom colors and logos for simulation testing</p>
        
        <!-- Add New Template Form -->
        <form id="addTemplateForm" style="margin-bottom: 30px;">
            <div class="form-group">
                <label class="form-label" for="new_template_name">Template Name *</label>
                <input type="text" class="form-input" id="new_template_name" name="template_name" 
                       placeholder="e.g., Corporate Blue Theme" required maxlength="100">
                <p class="help-text">Enter a unique name for this template</p>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="new_template_type">Template Type *</label>
                <select class="form-input" id="new_template_type" name="template_type" required>
                    <option value="simple" selected>Simple</option>
                    <option value="advanced">Advanced</option>
                </select>
                <p class="help-text">
                    <strong>Simple:</strong> Basic transaction details only<br>
                    <strong>Advanced:</strong> Includes account number, SWIFT code, and detailed information
                </p>
            </div>
            
            <div class="form-group">
                <label class="form-label">Template Colors</label>
                <div class="color-input-group">
                    <div class="color-input-wrapper">
                        <input type="color" class="color-picker" id="new_primary_color" name="primary_color" value="#359eb4">
                        <input type="text" class="form-input color-input" id="new_primary_color_text" value="#359eb4" 
                               pattern="^#[0-9A-Fa-f]{6}$" maxlength="7">
                        <label class="form-label" style="min-width: 80px; margin: 0;">Primary</label>
                    </div>
                    <div class="color-input-wrapper">
                        <input type="color" class="color-picker" id="new_secondary_color" name="secondary_color" value="#2a7e90">
                        <input type="text" class="form-input color-input" id="new_secondary_color_text" value="#2a7e90" 
                               pattern="^#[0-9A-Fa-f]{6}$" maxlength="7">
                        <label class="form-label" style="min-width: 80px; margin: 0;">Secondary</label>
                    </div>
                    <div class="color-input-wrapper">
                        <input type="color" class="color-picker" id="new_accent_color" name="accent_color" value="#10b981">
                        <input type="text" class="form-input color-input" id="new_accent_color_text" value="#10b981" 
                               pattern="^#[0-9A-Fa-f]{6}$" maxlength="7">
                        <label class="form-label" style="min-width: 80px; margin: 0;">Accent</label>
                    </div>
                </div>
                <p class="help-text">Primary: Headers & buttons | Secondary: Borders & accents | Accent: Success indicators</p>
            </div>
            
            <div class="form-group">
                <label class="form-label">Template Logo</label>
                <div class="upload-box" id="newLogoUploadBox" onclick="document.getElementById('new_logo_input').click()" style="cursor: pointer; border: 2px dashed #dadce0; border-radius: 8px; padding: 20px; text-align: center; background: #f8f9fa;">
                    <div id="new_logo_preview" style="display: none; margin-bottom: 10px;">
                        <img id="new_logo_preview_img" src="" alt="Logo Preview" style="max-width: 150px; max-height: 60px; object-fit: contain;">
                    </div>
                    <div class="upload-icon" style="font-size: 32px; color: #666; margin-bottom: 8px;">
                        <i class="fas fa-image"></i>
                    </div>
                    <div class="upload-text" style="color: #666;">
                        <p style="margin: 0 0 8px 0; font-weight: 600;">Click to upload logo</p>
                        <p style="margin: 0; font-size: 12px;">JPG, PNG, SVG, WebP (Max 2MB)</p>
                    </div>
                    <input type="file" id="new_logo_input" accept=".jpg,.jpeg,.png,.svg,.webp" style="display: none;" onchange="uploadTemplateLogo('new', this)">
                    <input type="hidden" id="new_logo_url" name="logo_url">
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="new_logo_alt_text">Logo Alt Text</label>
                <input type="text" class="form-input" id="new_logo_alt_text" name="logo_alt_text" 
                       placeholder="Bank Logo" value="Bank Logo" maxlength="255">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="new_template_address">Address</label>
                <input type="text" class="form-input" id="new_template_address" name="address" 
                       placeholder="123 Banking Street, New York, NY 10001" maxlength="500">
                <p class="help-text">Enter the address to display in the email footer (optional)</p>
            </div>
            
            <div class="form-group">
                <div class="checkbox-group">
                    <input type="checkbox" id="new_template_active" name="is_active" checked>
                    <label for="new_template_active" class="form-label" style="margin: 0; font-weight: 500;">Active (available for use)</label>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Template
            </button>
        </form>
        
        <!-- Templates List -->
        <div class="items-list" id="templatesList">
            <?php if (empty($templates)): ?>
                <p style="color: #666; text-align: center; padding: 40px;">No templates found. Add one above to get started.</p>
            <?php else: ?>
                <?php foreach ($templates as $template): ?>
                    <div class="template-card" data-id="<?php echo $template['id']; ?>">
                        <div class="template-colors">
                            <div class="color-swatch" style="background-color: <?php echo htmlspecialchars($template['primary_color']); ?>" 
                                 title="Primary: <?php echo htmlspecialchars($template['primary_color']); ?>"></div>
                            <div class="color-swatch" style="background-color: <?php echo htmlspecialchars($template['secondary_color']); ?>" 
                                 title="Secondary: <?php echo htmlspecialchars($template['secondary_color']); ?>"></div>
                            <div class="color-swatch" style="background-color: <?php echo htmlspecialchars($template['accent_color']); ?>" 
                                 title="Accent: <?php echo htmlspecialchars($template['accent_color']); ?>"></div>
                        </div>
                        <div class="template-info">
                            <div class="template-name"><?php echo htmlspecialchars($template['template_name']); ?></div>
                            <div class="template-details">
                                <span><strong>Type:</strong> <?php echo ucfirst($template['template_type'] ?? 'simple'); ?></span>
                                <span><strong>Logo:</strong> <?php echo $template['logo_url'] ? 'Custom' : 'System Default'; ?></span>
                                <span><strong>Status:</strong> 
                                    <span class="status-badge <?php echo $template['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                        <?php echo $template['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </span>
                            </div>
                        </div>
                        <div class="item-actions">
                            <button type="button" class="btn btn-secondary btn-sm edit-template-btn" 
                                    data-id="<?php echo $template['id']; ?>"
                                    data-name="<?php echo htmlspecialchars($template['template_name']); ?>"
                                    data-type="<?php echo htmlspecialchars($template['template_type'] ?? 'simple'); ?>"
                                    data-primary="<?php echo htmlspecialchars($template['primary_color']); ?>"
                                    data-secondary="<?php echo htmlspecialchars($template['secondary_color']); ?>"
                                    data-accent="<?php echo htmlspecialchars($template['accent_color']); ?>"
                                    data-logo="<?php echo htmlspecialchars($template['logo_url'] ?? ''); ?>"
                                    data-alt="<?php echo htmlspecialchars($template['logo_alt_text']); ?>"
                                    data-address="<?php echo htmlspecialchars($template['address'] ?? ''); ?>"
                                    data-active="<?php echo $template['is_active']; ?>">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button type="button" class="btn btn-danger btn-sm delete-template-btn" 
                                    data-id="<?php echo $template['id']; ?>">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Edit Caption Modal -->
<div id="editCaptionModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center;">
    <div style="background: white; padding: 30px; border-radius: 12px; max-width: 500px; width: 90%;">
        <h3 style="margin: 0 0 20px 0; font-size: 20px; color: #202124;">Edit Alert Caption</h3>
        <form id="editCaptionForm">
            <input type="hidden" id="edit_caption_id" name="id">
            <div class="form-group">
                <label class="form-label" for="edit_caption_text">Caption Text *</label>
                <input type="text" class="form-input" id="edit_caption_text" name="caption_text" required maxlength="255">
            </div>
            <div class="form-group">
                <div class="checkbox-group">
                    <input type="checkbox" id="edit_caption_active" name="is_active">
                    <label for="edit_caption_active" class="form-label" style="margin: 0; font-weight: 500;">Active</label>
                </div>
            </div>
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeEditCaptionModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Template Modal -->
<div id="editTemplateModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center; overflow-y: auto; padding: 20px;">
    <div style="background: white; padding: 30px; border-radius: 12px; max-width: 600px; width: 90%; margin: auto;">
        <h3 style="margin: 0 0 20px 0; font-size: 20px; color: #202124;">Edit Template</h3>
        <form id="editTemplateForm">
            <input type="hidden" id="edit_template_id" name="id">
            <div class="form-group">
                <label class="form-label" for="edit_template_name">Template Name *</label>
                <input type="text" class="form-input" id="edit_template_name" name="template_name" required maxlength="100">
            </div>
            <div class="form-group">
                <label class="form-label" for="edit_template_type">Template Type *</label>
                <select class="form-input" id="edit_template_type" name="template_type" required>
                    <option value="simple">Simple</option>
                    <option value="advanced">Advanced</option>
                </select>
                <p class="help-text">
                    <strong>Simple:</strong> Basic transaction details only<br>
                    <strong>Advanced:</strong> Includes account number, SWIFT code, and detailed information
                </p>
            </div>
            <div class="form-group">
                <label class="form-label">Template Colors</label>
                <div class="color-input-group">
                    <div class="color-input-wrapper">
                        <input type="color" class="color-picker" id="edit_primary_color" name="primary_color">
                        <input type="text" class="form-input color-input" id="edit_primary_color_text" 
                               pattern="^#[0-9A-Fa-f]{6}$" maxlength="7">
                        <label class="form-label" style="min-width: 80px; margin: 0;">Primary</label>
                    </div>
                    <div class="color-input-wrapper">
                        <input type="color" class="color-picker" id="edit_secondary_color" name="secondary_color">
                        <input type="text" class="form-input color-input" id="edit_secondary_color_text" 
                               pattern="^#[0-9A-Fa-f]{6}$" maxlength="7">
                        <label class="form-label" style="min-width: 80px; margin: 0;">Secondary</label>
                    </div>
                    <div class="color-input-wrapper">
                        <input type="color" class="color-picker" id="edit_accent_color" name="accent_color">
                        <input type="text" class="form-input color-input" id="edit_accent_color_text" 
                               pattern="^#[0-9A-Fa-f]{6}$" maxlength="7">
                        <label class="form-label" style="min-width: 80px; margin: 0;">Accent</label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Template Logo</label>
                <div class="upload-box" id="editLogoUploadBox" onclick="document.getElementById('edit_logo_input').click()" style="cursor: pointer; border: 2px dashed #dadce0; border-radius: 8px; padding: 20px; text-align: center; background: #f8f9fa;">
                    <div id="edit_logo_preview" style="display: none; margin-bottom: 10px;">
                        <img id="edit_logo_preview_img" src="" alt="Logo Preview" style="max-width: 150px; max-height: 60px; object-fit: contain;">
                    </div>
                    <div class="upload-icon" style="font-size: 32px; color: #666; margin-bottom: 8px;">
                        <i class="fas fa-image"></i>
                    </div>
                    <div class="upload-text" style="color: #666;">
                        <p style="margin: 0 0 8px 0; font-weight: 600;">Click to upload logo</p>
                        <p style="margin: 0; font-size: 12px;">JPG, PNG, SVG, WebP (Max 2MB)</p>
                    </div>
                    <input type="file" id="edit_logo_input" accept=".jpg,.jpeg,.png,.svg,.webp" style="display: none;" onchange="uploadTemplateLogo('edit', this)">
                    <input type="hidden" id="edit_logo_url" name="logo_url">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="edit_logo_alt_text">Logo Alt Text</label>
                <input type="text" class="form-input" id="edit_logo_alt_text" name="logo_alt_text" maxlength="255">
            </div>
            <div class="form-group">
                <label class="form-label" for="edit_template_address">Address</label>
                <input type="text" class="form-input" id="edit_template_address" name="address" 
                       placeholder="123 Banking Street, New York, NY 10001" maxlength="500">
                <p class="help-text">Enter the address to display in the email footer (optional)</p>
            </div>
            <div class="form-group">
                <div class="checkbox-group">
                    <input type="checkbox" id="edit_template_active" name="is_active">
                    <label for="edit_template_active" class="form-label" style="margin: 0; font-weight: 500;">Active</label>
                </div>
            </div>
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeEditTemplateModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
// Color picker synchronization
function syncColorPickers() {
    // New template form
    ['primary', 'secondary', 'accent'].forEach(color => {
        const picker = document.getElementById(`new_${color}_color`);
        const text = document.getElementById(`new_${color}_color_text`);
        if (picker && text) {
            picker.addEventListener('input', () => text.value = picker.value);
            text.addEventListener('input', () => {
                if (/^#[0-9A-Fa-f]{6}$/i.test(text.value)) {
                    picker.value = text.value;
                }
            });
        }
    });
    
    // Edit template form
    ['primary', 'secondary', 'accent'].forEach(color => {
        const picker = document.getElementById(`edit_${color}_color`);
        const text = document.getElementById(`edit_${color}_color_text`);
        if (picker && text) {
            picker.addEventListener('input', () => text.value = picker.value);
            text.addEventListener('input', () => {
                if (/^#[0-9A-Fa-f]{6}$/i.test(text.value)) {
                    picker.value = text.value;
                }
            });
        }
    });
}

syncColorPickers();

// Upload template logo
function uploadTemplateLogo(formType, input) {
    const file = input.files[0];
    if (!file) return;
    
    const allowedTypes = ['image/jpeg', 'image/png', 'image/svg+xml', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
        showMessage('error', 'Invalid file type. Only JPG, PNG, SVG, and WebP are allowed.');
        input.value = '';
        return;
    }
    
    const maxSize = 2 * 1024 * 1024; // 2MB
    if (file.size > maxSize) {
        showMessage('error', 'File size too large. Maximum size is 2MB.');
        input.value = '';
        return;
    }
    
    const preview = document.getElementById(`${formType}_logo_preview`);
    const previewImg = document.getElementById(`${formType}_logo_preview_img`);
    if (preview && previewImg) {
        const reader = new FileReader();
        reader.onload = (e) => {
            preview.style.display = 'block';
            previewImg.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
    
    const formData = new FormData();
    formData.append('logo', file);
    
    fetch('<?php echo SITE_URL; ?>/api/upload-template-logo.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const hiddenInput = document.getElementById(`${formType}_logo_url`);
            if (hiddenInput) {
                hiddenInput.value = data.logo_url;
            }
            showMessage('success', 'Logo uploaded successfully.');
        } else {
            showMessage('error', data.message || 'Failed to upload logo.');
            input.value = '';
            if (preview) {
                preview.style.display = 'none';
            }
        }
    })
    .catch(error => {
        console.error(error);
        showMessage('error', 'Failed to upload logo. Please try again.');
        input.value = '';
        if (preview) {
            preview.style.display = 'none';
        }
    });
}

// API Base URL
const apiUrl = '<?php echo SITE_URL; ?>/api/email-simulation-api.php';

// Show message
function showMessage(type, message) {
    const container = document.getElementById('messageContainer');
    const className = type === 'success' ? 'alert-success' : 'alert-error';
    const icon = type === 'success' ? 'check-circle' : 'exclamation-circle';
    
    container.innerHTML = `
        <div class="alert ${className}">
            <i class="fas fa-${icon}"></i>
            <div>${message}</div>
        </div>
    `;
    
    container.scrollIntoView({ behavior: 'smooth', block: 'start' });
    
    if (type === 'success') {
        setTimeout(() => container.innerHTML = '', 5000);
    }
}

// Add Caption
document.getElementById('addCaptionForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData);
    data.action = 'add-caption';
    
    try {
        const response = await fetch(apiUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await response.json();
        
        if (result.success) {
            showMessage('success', result.message);
            e.target.reset();
            setTimeout(() => location.reload(), 1000);
        } else {
            showMessage('error', result.message || 'Failed to add caption');
        }
    } catch (error) {
        showMessage('error', 'Error: ' + error.message);
    }
});

// Edit Caption
document.querySelectorAll('.edit-caption-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('edit_caption_id').value = btn.dataset.id;
        document.getElementById('edit_caption_text').value = btn.dataset.text;
        document.getElementById('edit_caption_active').checked = btn.dataset.active === '1';
        document.getElementById('editCaptionModal').style.display = 'flex';
    });
});

// Close Edit Caption Modal
function closeEditCaptionModal() {
    document.getElementById('editCaptionModal').style.display = 'none';
}

// Update Caption
document.getElementById('editCaptionForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData);
    data.action = 'update-caption';
    data.is_active = document.getElementById('edit_caption_active').checked ? 1 : 0;
    
    try {
        const response = await fetch(apiUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await response.json();
        
        if (result.success) {
            showMessage('success', result.message);
            closeEditCaptionModal();
            setTimeout(() => location.reload(), 1000);
        } else {
            showMessage('error', result.message || 'Failed to update caption');
        }
    } catch (error) {
        showMessage('error', 'Error: ' + error.message);
    }
});

// Delete Caption
document.querySelectorAll('.delete-caption-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
        if (!confirm('Are you sure you want to delete this caption?')) return;
        
        try {
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete-caption', id: btn.dataset.id })
            });
            const result = await response.json();
            
            if (result.success) {
                showMessage('success', result.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                showMessage('error', result.message || 'Failed to delete caption');
            }
        } catch (error) {
            showMessage('error', 'Error: ' + error.message);
        }
    });
});

// Add Template
document.getElementById('addTemplateForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData);
    data.action = 'add-template';
    data.is_active = document.getElementById('new_template_active').checked ? 1 : 0;
    
    try {
        const response = await fetch(apiUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await response.json();
        
        if (result.success) {
            showMessage('success', result.message);
            e.target.reset();
            document.getElementById('new_template_active').checked = true;
            document.getElementById('new_template_type').value = 'simple';
            document.getElementById('new_logo_url').value = '';
            const newPreview = document.getElementById('new_logo_preview');
            const newPreviewImg = document.getElementById('new_logo_preview_img');
            if (newPreview && newPreviewImg) {
                newPreview.style.display = 'none';
                newPreviewImg.src = '';
            }
            syncColorPickers();
            setTimeout(() => location.reload(), 1000);
        } else {
            showMessage('error', result.message || 'Failed to add template');
        }
    } catch (error) {
        showMessage('error', 'Error: ' + error.message);
    }
});

// Edit Template
document.querySelectorAll('.edit-template-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('edit_template_id').value = btn.dataset.id;
        document.getElementById('edit_template_name').value = btn.dataset.name;
        document.getElementById('edit_template_type').value = btn.dataset.type || 'simple';
        document.getElementById('edit_primary_color').value = btn.dataset.primary;
        document.getElementById('edit_primary_color_text').value = btn.dataset.primary;
        document.getElementById('edit_secondary_color').value = btn.dataset.secondary;
        document.getElementById('edit_secondary_color_text').value = btn.dataset.secondary;
        document.getElementById('edit_accent_color').value = btn.dataset.accent;
        document.getElementById('edit_accent_color_text').value = btn.dataset.accent;
        document.getElementById('edit_logo_url').value = btn.dataset.logo || '';
        if (btn.dataset.logo) {
            document.getElementById('edit_logo_preview').style.display = 'block';
            document.getElementById('edit_logo_preview_img').src = btn.dataset.logo;
        } else {
            document.getElementById('edit_logo_preview').style.display = 'none';
            document.getElementById('edit_logo_preview_img').src = '';
        }
        document.getElementById('edit_logo_alt_text').value = btn.dataset.alt || 'Bank Logo';
        document.getElementById('edit_template_address').value = btn.dataset.address || '';
        document.getElementById('edit_template_active').checked = btn.dataset.active === '1';
        document.getElementById('editTemplateModal').style.display = 'flex';
    });
});

// Close Edit Template Modal
function closeEditTemplateModal() {
    document.getElementById('editTemplateModal').style.display = 'none';
}

// Update Template
document.getElementById('editTemplateForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData);
    data.action = 'update-template';
    data.is_active = document.getElementById('edit_template_active').checked ? 1 : 0;
    
    try {
        const response = await fetch(apiUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await response.json();
        
        if (result.success) {
            showMessage('success', result.message);
            closeEditTemplateModal();
            setTimeout(() => location.reload(), 1000);
        } else {
            showMessage('error', result.message || 'Failed to update template');
        }
    } catch (error) {
        showMessage('error', 'Error: ' + error.message);
    }
});

// Delete Template
document.querySelectorAll('.delete-template-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
        if (!confirm('Are you sure you want to delete this template?')) return;
        
        try {
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete-template', id: btn.dataset.id })
            });
            const result = await response.json();
            
            if (result.success) {
                showMessage('success', result.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                showMessage('error', result.message || 'Failed to delete template');
            }
        } catch (error) {
            showMessage('error', 'Error: ' + error.message);
        }
    });
});
</script>

<?php
// Only close HTML if accessed directly (not included by controller)
if (!defined('EMAIL_SUBPAGE') || EMAIL_SUBPAGE !== true) {
    echo '</div></div></div>'; // Close content-area, main-content-area, dashboard-container
    echo '</body></html>';
}
?>

