<?php
// Check if we're being included or accessed directly
// EMAIL_SUBPAGE is set to true when included by controller (head/sidebar already included)
if (!defined('EMAIL_SUBPAGE') || EMAIL_SUBPAGE !== true) {
    // If accessed directly, include full structure
    $pageTitle = 'Send & Receive Email - Admin';
    require_once __DIR__ . '/../../config/config.php';
    require_once __DIR__ . '/../../includes/functions.php';
    
    requireAdmin();
    
    include __DIR__ . '/../../includes/head.php';
    include __DIR__ . '/../../includes/admin-sidebar.php';
}

// Get all users for dropdown
$db = Database::getInstance();
$usersSql = "SELECT id, full_name, email, status FROM users WHERE role = 'user' ORDER BY full_name ASC";
$usersStmt = $db->query($usersSql);
$allUsers = $usersStmt ? $usersStmt->fetchAll() : [];

// Count active users
$activeUsersCount = 0;
foreach ($allUsers as $user) {
    if ($user['status'] === 'active') {
        $activeUsersCount++;
    }
}
?>

<style>
    .email-send-container {
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .email-send-card {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
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
        min-height: 150px;
        resize: vertical;
    }
    
    .form-input:focus, .form-select:focus, .form-textarea:focus {
        outline: none;
        border-color: #1e3a8a;
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
    }
    
    
    .recipient-section {
        display: none;
    }
    
    .recipient-section.active {
        display: block;
    }
    
    .user-select-wrapper {
        position: relative;
    }
    
    .user-select-wrapper .form-select {
        padding-right: 40px;
    }
    
    .email-tags-container {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        padding: 12px 16px;
        border: 1px solid #dadce0;
        border-radius: 8px;
        min-height: 50px;
        background: white;
        cursor: text;
    }
    
    .email-tags-container:focus-within {
        border-color: #1e3a8a;
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
    }
    
    .email-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #e0e7ff;
        color: #1e3a8a;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 500;
    }
    
    .email-tag.invalid {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .email-tag-remove {
        cursor: pointer;
        margin-left: 4px;
        font-size: 16px;
        line-height: 1;
        opacity: 0.7;
        transition: opacity 0.2s;
    }
    
    .email-tag-remove:hover {
        opacity: 1;
    }
    
    .email-tags-input {
        flex: 1;
        min-width: 150px;
        border: none;
        outline: none;
        font-size: 14px;
        padding: 0;
        background: transparent;
    }
    
    .email-tags-input::placeholder {
        color: #999;
    }
    
    .help-text {
        font-size: 13px;
        color: #666;
        margin-top: 6px;
    }
    
    .email-count-badge {
        display: inline-block;
        padding: 4px 8px;
        background: #e0e7ff;
        color: #1e3a8a;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        margin-left: 8px;
    }
    
    .btn {
        padding: 14px 28px;
        border: none;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        color: white;
        width: 100%;
        justify-content: center;
    }
    
    .btn-primary:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(30, 58, 138, 0.3);
    }
    
    .btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
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
    
    .spinner {
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top: 3px solid white;
        width: 18px;
        height: 18px;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .email-preview {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        border: 2px dashed #dadce0;
        margin-top: 16px;
    }
    
    .email-preview h4 {
        margin: 0 0 12px 0;
        color: #202124;
        font-size: 16px;
    }
    
    .email-preview-content {
        background: white;
        padding: 20px;
        border-radius: 6px;
        border: 1px solid #e5e7eb;
        font-size: 14px;
        color: #333;
        line-height: 1.6;
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
    
    .inbox-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 20px;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .inbox-tab {
        padding: 12px 24px;
        border: none;
        background: transparent;
        color: #666;
        font-size: 15px;
        font-weight: 500;
        cursor: pointer;
        border-bottom: 3px solid transparent;
        margin-bottom: -2px;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .inbox-tab:hover {
        color: #1e3a8a;
        background: #f8f9fa;
    }
    
    .inbox-tab.active {
        color: #1e3a8a;
        border-bottom-color: #1e3a8a;
    }
    
    .inbox-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .btn-refresh {
        padding: 10px 20px;
        background: #f8f9fa;
        border: 1px solid #dadce0;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        color: #202124;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-refresh:hover {
        background: #e8f0fe;
        border-color: #1e3a8a;
        color: #1e3a8a;
    }
    
    .inbox-loading {
        display: flex;
        align-items: center;
        gap: 12px;
        color: #666;
        font-size: 14px;
    }
    
    .inbox-container {
        min-height: 200px;
        max-height: 600px;
        overflow-y: auto;
    }
    
    .inbox-empty {
        text-align: center;
        padding: 60px 20px;
        color: #999;
    }
    
    .email-item {
        padding: 16px;
        border-bottom: 1px solid #e5e7eb;
        cursor: pointer;
        transition: background 0.2s;
        display: flex;
        align-items: flex-start;
        gap: 16px;
    }
    
    .email-item:hover {
        background: #f8f9fa;
    }
    
    .email-item.unread {
        background: #f0f4ff;
        font-weight: 600;
    }
    
    .email-item.unread:hover {
        background: #e8f0fe;
    }
    
    .email-item-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 16px;
        flex-shrink: 0;
    }
    
    .email-item-content {
        flex: 1;
        min-width: 0;
    }
    
    .email-item-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 6px;
    }
    
    .email-item-from {
        font-weight: 600;
        color: #202124;
        font-size: 15px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .email-item-date {
        color: #666;
        font-size: 13px;
        flex-shrink: 0;
        margin-left: 12px;
    }
    
    .email-item-subject {
        font-weight: 500;
        color: #202124;
        font-size: 15px;
        margin-bottom: 4px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .email-item-preview {
        color: #666;
        font-size: 13px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .email-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    
    .email-modal.active {
        display: flex;
    }
    
    .email-modal-content {
        background: white;
        border-radius: 12px;
        max-width: 800px;
        width: 100%;
        max-height: 90vh;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    }
    
    .email-modal-header {
        padding: 24px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }
    
    .email-modal-actions {
        display: flex;
        gap: 12px;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid #e5e7eb;
    }
    
    .btn-reply {
        padding: 10px 20px;
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        color: white;
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
    
    .btn-reply:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
    }
    
    .email-modal-title {
        font-size: 20px;
        font-weight: 600;
        color: #202124;
        margin-bottom: 8px;
    }
    
    .email-modal-close {
        background: none;
        border: none;
        font-size: 24px;
        color: #666;
        cursor: pointer;
        padding: 0;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.2s;
    }
    
    .email-modal-close:hover {
        background: #f0f0f0;
        color: #202124;
    }
    
    .email-modal-body {
        padding: 24px;
        overflow-y: auto;
        flex: 1;
    }
    
    .email-detail-item {
        margin-bottom: 16px;
    }
    
    .email-detail-label {
        font-size: 12px;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }
    
    .email-detail-value {
        font-size: 15px;
        color: #202124;
    }
    
    .email-detail-body {
        margin-top: 24px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 8px;
        line-height: 1.6;
    }
</style>

<div class="email-send-container">
    <a href="<?php echo SITE_URL; ?>/admin/email" class="back-button">
        <i class="fas fa-arrow-left"></i> Back to Email Management
    </a>
    
    <div class="page-header">
        <h1>
            <i class="fas fa-paper-plane"></i>
            Send & Receive Email
        </h1>
        <p>Send emails to users, all users, or external email addresses</p>
    </div>
    
    <div class="email-send-card">
        <div id="messageContainer"></div>
        
        <h2 class="card-title">
            <i class="fas fa-paper-plane"></i>
            Send Email
        </h2>
    
    <form id="sendEmailForm">
        <!-- Recipient Type Selection -->
        <div class="form-group">
            <label class="form-label" for="recipient_type">Recipient Type *</label>
            <select class="form-select" id="recipient_type" name="recipient_type" required>
                <option value="single">Single User</option>
                <option value="all">All Users (<?php echo $activeUsersCount; ?> active)</option>
                <option value="external">External Email(s)</option>
            </select>
        </div>
        
        <!-- Single User Selection -->
        <div class="recipient-section active" id="singleUserSection">
            <div class="form-group">
                <label class="form-label" for="user_id">Select User</label>
                <div class="user-select-wrapper">
                    <select class="form-select" id="user_id" name="user_id">
                        <option value="">-- Select a user --</option>
                        <?php foreach ($allUsers as $user): ?>
                            <option value="<?php echo $user['id']; ?>" data-email="<?php echo htmlspecialchars($user['email']); ?>">
                                <?php echo htmlspecialchars($user['full_name']); ?> (<?php echo htmlspecialchars($user['email']); ?>) - <?php echo ucfirst($user['status']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- All Users Section -->
        <div class="recipient-section" id="allUsersSection">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <div>
                    <strong>Bulk Email</strong><br>
                    This will send the email to all <strong><?php echo $activeUsersCount; ?> active users</strong> in the system.
                    The email will be personalized with each user's name.
                </div>
            </div>
        </div>
        
        <!-- External Emails Section -->
        <div class="recipient-section" id="externalEmailsSection">
            <div class="form-group">
                <label class="form-label" for="external_emails">Email Addresses</label>
                <div class="email-tags-container" id="emailTagsContainer">
                    <input 
                        type="text" 
                        class="email-tags-input" 
                        id="external_emails_input" 
                        placeholder="Type email address and press Enter or Space"
                        autocomplete="off">
                </div>
                <p class="help-text">
                    <i class="fas fa-info-circle"></i>
                    Type an email address and press Enter or Space to add it. Click the X to remove.
                </p>
            </div>
        </div>
        
        <!-- Email Subject -->
        <div class="form-group">
            <label class="form-label" for="email_subject">Subject *</label>
            <input type="text" class="form-input" id="email_subject" name="email_subject" required placeholder="Enter email subject">
        </div>
        
        <!-- Email Content -->
        <div class="form-group">
            <label class="form-label" for="email_content">Message *</label>
            <textarea class="form-textarea" id="email_content" name="email_content" required placeholder="Enter your email message. You can use HTML formatting."></textarea>
            <p class="help-text">
                <i class="fas fa-code"></i>
                HTML is supported. Use &lt;br&gt; for line breaks, &lt;strong&gt; for bold, etc.
            </p>
        </div>
        
        <!-- Send Button -->
        <button type="submit" class="btn btn-primary" id="sendButton">
            <i class="fas fa-paper-plane"></i>
            <span>Send Email</span>
        </button>
    </form>
    
    <div class="email-preview" id="emailPreview" style="display: none;">
        <h4><i class="fas fa-eye"></i> Email Preview</h4>
        <div class="email-preview-content" id="previewContent"></div>
    </div>
    </div>
    
    <!-- Inbox Section -->
    <div class="email-send-card" style="margin-top: 30px;">
        <h2 class="card-title">
            <i class="fas fa-inbox"></i>
            Inbox
        </h2>
        
        <div class="inbox-tabs">
            <button class="inbox-tab active" data-folder="INBOX">
                <i class="fas fa-inbox"></i> Inbox
            </button>
            <button class="inbox-tab" data-folder="SPAM">
                <i class="fas fa-exclamation-triangle"></i> Spam
            </button>
        </div>
        
        <div class="inbox-controls">
            <button class="btn-refresh" id="refreshInbox">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
            <div class="inbox-loading" id="inboxLoading" style="display: none;">
                <div class="spinner"></div>
                <span>Loading emails...</span>
            </div>
        </div>
        
        <div id="inboxContainer" class="inbox-container">
            <div class="inbox-empty">
                <i class="fas fa-inbox" style="font-size: 48px; color: #ccc; margin-bottom: 16px;"></i>
                <p>Click "Refresh" to load emails</p>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    'use strict';
    
    const form = document.getElementById('sendEmailForm');
    const sendButton = document.getElementById('sendButton');
    const messageContainer = document.getElementById('messageContainer');
    const recipientTypeSelect = document.getElementById('recipient_type');
    const recipientSections = document.querySelectorAll('.recipient-section');
    const emailPreview = document.getElementById('emailPreview');
    const previewContent = document.getElementById('previewContent');
    const emailContent = document.getElementById('email_content');
    const emailSubject = document.getElementById('email_subject');
    const emailTagsContainer = document.getElementById('emailTagsContainer');
    const emailTagsInput = document.getElementById('external_emails_input');
    
    let currentRecipientType = 'single';
    let emailTags = []; // Store email tags
    
    // Email validation function
    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }
    
    // Create email tag
    function createEmailTag(email) {
        const trimmedEmail = email.trim();
        if (!trimmedEmail) return false;
        
        // Check if email already exists
        if (emailTags.includes(trimmedEmail)) {
            return false;
        }
        
        const isValid = isValidEmail(trimmedEmail);
        const tag = document.createElement('span');
        tag.className = 'email-tag' + (isValid ? '' : ' invalid');
        tag.dataset.email = trimmedEmail;
        
        const emailText = document.createTextNode(trimmedEmail);
        const removeBtn = document.createElement('span');
        removeBtn.className = 'email-tag-remove';
        removeBtn.textContent = '×';
        removeBtn.addEventListener('click', function() {
            removeEmailTag(trimmedEmail);
        });
        
        tag.appendChild(emailText);
        tag.appendChild(removeBtn);
        
        // Insert before the input
        emailTagsContainer.insertBefore(tag, emailTagsInput);
        
        if (isValid) {
            emailTags.push(trimmedEmail);
            // Also update global reference
            if (typeof window.emailTagsArray !== 'undefined') {
                window.emailTagsArray.push(trimmedEmail);
            }
        }
        
        return true;
    }
    
    // Expose createEmailTag globally for reply function
    window.createEmailTagForReply = createEmailTag;
    
    // Remove email tag
    function removeEmailTag(email) {
        const tag = emailTagsContainer.querySelector(`[data-email="${email}"]`);
        if (tag) {
            tag.remove();
            emailTags = emailTags.filter(e => e !== email);
        }
    }
    
    // Handle email input
    emailTagsInput.addEventListener('keydown', function(e) {
        const value = this.value.trim();
        
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            if (value) {
                createEmailTag(value);
                this.value = '';
            }
        } else if (e.key === 'Backspace' && !value && emailTags.length > 0) {
            // Remove last tag if input is empty and backspace is pressed
            const lastTag = emailTagsContainer.querySelector('.email-tag:last-of-type');
            if (lastTag) {
                removeEmailTag(lastTag.dataset.email);
            }
        }
    });
    
    // Handle paste - parse comma or newline separated emails
    emailTagsInput.addEventListener('paste', function(e) {
        e.preventDefault();
        const pastedText = (e.clipboardData || window.clipboardData).getData('text');
        const emails = pastedText.split(/[,\n]/).map(e => e.trim()).filter(e => e);
        
        emails.forEach(email => {
            createEmailTag(email);
        });
        
        this.value = '';
    });
    
    // Handle recipient type dropdown change
    function updateRecipientSections() {
        const type = recipientTypeSelect.value;
        currentRecipientType = type;
        
        // Update sections
        recipientSections.forEach(s => s.classList.remove('active'));
        const sectionId = type === 'single' ? 'singleUserSection' : 
                         type === 'all' ? 'allUsersSection' : 
                         'externalEmailsSection';
        document.getElementById(sectionId).classList.add('active');
        
        // Clear validation
        form.querySelectorAll('.form-input, .form-select, .form-textarea').forEach(input => {
            input.classList.remove('error');
        });
        
        // Clear email tags when switching away from external
        if (type !== 'external') {
            emailTagsContainer.querySelectorAll('.email-tag').forEach(tag => tag.remove());
            emailTags = [];
            emailTagsInput.value = '';
        }
    }
    
    recipientTypeSelect.addEventListener('change', updateRecipientSections);
    
    // Initialize on load
    updateRecipientSections();
    
    // Update preview as user types
    emailContent.addEventListener('input', updatePreview);
    emailSubject.addEventListener('input', updatePreview);
    
    function updatePreview() {
        const subject = emailSubject.value.trim();
        const content = emailContent.value.trim();
        
        if (subject || content) {
            emailPreview.style.display = 'block';
            previewContent.innerHTML = `
                <strong>Subject:</strong> ${subject || '(No subject)'}<br><br>
                <strong>Message:</strong><br>
                ${content || '(No message)'}
            `;
        } else {
            emailPreview.style.display = 'none';
        }
    }
    
    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate based on recipient type
        let recipientData = {};
        
        if (currentRecipientType === 'single') {
            const userId = document.getElementById('user_id').value;
            if (!userId) {
                showMessage('error', 'Please select a user');
                return;
            }
            recipientData = {
                type: 'single',
                user_id: userId
            };
        } else if (currentRecipientType === 'all') {
            recipientData = {
                type: 'all'
            };
        } else if (currentRecipientType === 'external') {
            // Try to add any remaining text in the input field
            const remainingText = emailTagsInput.value.trim();
            if (remainingText) {
                createEmailTag(remainingText);
                emailTagsInput.value = '';
            }
            
            // Get all valid email tags
            const emailTags = document.querySelectorAll('.email-tag:not(.invalid)');
            const emailList = Array.from(emailTags).map(tag => tag.dataset.email).filter(e => e);
            
            if (emailList.length === 0) {
                showMessage('error', 'Please enter at least one valid email address');
                return;
            }
            
            // Check if there are any invalid emails
            const invalidTags = document.querySelectorAll('.email-tag.invalid');
            if (invalidTags.length > 0) {
                showMessage('error', 'Please fix invalid email addresses before sending');
                return;
            }
            
            recipientData = {
                type: 'external',
                emails: emailList
            };
        }
        
        const subject = emailSubject.value.trim();
        const content = emailContent.value.trim();
        
        if (!subject || !content) {
            showMessage('error', 'Please fill in both subject and message');
            return;
        }
        
        // Disable button and show loading
        sendButton.disabled = true;
        sendButton.innerHTML = '<div class="spinner"></div><span>Sending...</span>';
        
        // Send email
        fetch('<?php echo SITE_URL; ?>/api/send-custom-email.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                recipient: recipientData,
                subject: subject,
                content: content
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const recipientInfo = currentRecipientType === 'single' ? 'to the selected user' :
                                     currentRecipientType === 'all' ? `to ${data.sent_count || 0} users` :
                                     `to ${data.sent_count || 0} external email(s)`;
                
                showMessage('success', `<strong>Email Sent Successfully!</strong><br>Email sent ${recipientInfo}.`);
                form.reset();
                emailPreview.style.display = 'none';
            } else {
                showMessage('error', `<strong>Email Send Failed!</strong><br>${data.message || 'Please try again.'}`);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('error', '<strong>Error!</strong><br>Failed to send email. Check console for details.');
        })
        .finally(() => {
            // Re-enable button
            sendButton.disabled = false;
            sendButton.innerHTML = '<i class="fas fa-paper-plane"></i><span>Send Email</span>';
        });
    });
    
    function showMessage(type, message) {
        const className = type === 'success' ? 'alert-success' : 'alert-error';
        const icon = type === 'success' ? 'check-circle' : 'exclamation-circle';
        
        messageContainer.innerHTML = `
            <div class="alert ${className}">
                <i class="fas fa-${icon}"></i>
                <div>${message}</div>
            </div>
        `;
        
        // Scroll to message
        messageContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        
        // Auto-hide success messages after 8 seconds
        if (type === 'success') {
            setTimeout(() => {
                messageContainer.innerHTML = '';
            }, 8000);
        }
    }
})();
</script>

<script>
// Inbox functionality
(function() {
    'use strict';
    
    let currentFolder = 'INBOX';
    const inboxContainer = document.getElementById('inboxContainer');
    const refreshBtn = document.getElementById('refreshInbox');
    const inboxLoading = document.getElementById('inboxLoading');
    const inboxTabs = document.querySelectorAll('.inbox-tab');
    
    // Tab switching
    inboxTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            inboxTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            currentFolder = this.dataset.folder;
            loadEmails();
        });
    });
    
    // Refresh button
    refreshBtn.addEventListener('click', loadEmails);
    
    // Load emails
    function loadEmails() {
        inboxLoading.style.display = 'flex';
        inboxContainer.innerHTML = '<div class="inbox-empty"><div class="spinner"></div><p>Loading emails...</p></div>';
        
        fetch(`<?php echo SITE_URL; ?>/api/get-inbox-emails.php?folder=${currentFolder}&limit=20`)
            .then(response => response.json())
            .then(data => {
                inboxLoading.style.display = 'none';
                
                if (data.success) {
                    displayEmails(data.emails, data.total);
                } else {
                    inboxContainer.innerHTML = `
                        <div class="inbox-empty">
                            <i class="fas fa-exclamation-circle" style="font-size: 48px; color: #ef4444; margin-bottom: 16px;"></i>
                            <p>${data.message || 'Failed to load emails'}</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                inboxLoading.style.display = 'none';
                console.error('Error:', error);
                inboxContainer.innerHTML = `
                    <div class="inbox-empty">
                        <i class="fas fa-exclamation-circle" style="font-size: 48px; color: #ef4444; margin-bottom: 16px;"></i>
                        <p>Error loading emails. Please try again.</p>
                    </div>
                `;
            });
    }
    
    // Display emails
    function displayEmails(emails, total) {
        if (emails.length === 0) {
            inboxContainer.innerHTML = `
                <div class="inbox-empty">
                    <i class="fas fa-inbox" style="font-size: 48px; color: #ccc; margin-bottom: 16px;"></i>
                    <p>No emails in ${currentFolder === 'INBOX' ? 'inbox' : 'spam'}</p>
                </div>
            `;
            return;
        }
        
        const emailsHtml = emails.map(email => {
            // Use Reply-To email/name if available (for contact form emails), otherwise use From
            const displayEmail = email.reply_to && email.reply_to.email ? email.reply_to : email.from;
            const displayName = displayEmail.name || displayEmail.email;
            const fromInitial = displayName.charAt(0).toUpperCase();
            const date = new Date(email.date);
            const formattedDate = date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            
            return `
                <div class="email-item ${email.unread ? 'unread' : ''}" onclick="viewEmail(${email.id}, '${currentFolder}')">
                    <div class="email-item-icon">${fromInitial}</div>
                    <div class="email-item-content">
                        <div class="email-item-header">
                            <div class="email-item-from">${escapeHtml(displayName)}</div>
                            <div class="email-item-date">${formattedDate}</div>
                        </div>
                        <div class="email-item-subject">${escapeHtml(email.subject)}</div>
                        <div class="email-item-preview">${escapeHtml(email.preview || '')}</div>
                    </div>
                </div>
            `;
        }).join('');
        
        inboxContainer.innerHTML = emailsHtml;
    }
    
    // View email details
    window.viewEmail = function(messageId, folder) {
        const modal = document.getElementById('emailModal');
        if (!modal) {
            createEmailModal();
        }
        
        const emailModal = document.getElementById('emailModal');
        const emailModalBody = document.getElementById('emailModalBody');
        
        emailModalBody.innerHTML = '<div style="text-align: center; padding: 40px;"><div class="spinner"></div><p>Loading email...</p></div>';
        emailModal.classList.add('active');
        
        fetch(`<?php echo SITE_URL; ?>/api/get-email-content.php?id=${messageId}&folder=${folder}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const email = data.email;
                    const date = new Date(email.date);
                    const formattedDate = date.toLocaleString();
                    
                    // Use Reply-To email if available (for contact form emails), otherwise use From
                    const replyEmail = email.reply_to && email.reply_to.email ? email.reply_to.email : email.from.email;
                    const replyName = email.reply_to && email.reply_to.name ? email.reply_to.name : (email.from.name || email.from.email);
                    
                    // Build email details HTML
                    const emailDetails = document.createElement('div');
                    emailDetails.innerHTML = `
                        <div class="email-detail-item">
                            <div class="email-detail-label">From</div>
                            <div class="email-detail-value">${escapeHtml(email.from.name || email.from.email)} &lt;${escapeHtml(email.from.email)}&gt;</div>
                        </div>
                        ${email.reply_to && email.reply_to.email !== email.from.email ? `
                        <div class="email-detail-item">
                            <div class="email-detail-label">Reply-To</div>
                            <div class="email-detail-value">${escapeHtml(replyName)} &lt;${escapeHtml(replyEmail)}&gt;</div>
                        </div>
                        ` : ''}
                        <div class="email-detail-item">
                            <div class="email-detail-label">To</div>
                            <div class="email-detail-value">${email.to.map(e => escapeHtml(e)).join(', ')}</div>
                        </div>
                        <div class="email-detail-item">
                            <div class="email-detail-label">Subject</div>
                            <div class="email-detail-value">${escapeHtml(email.subject)}</div>
                        </div>
                        <div class="email-detail-item">
                            <div class="email-detail-label">Date</div>
                            <div class="email-detail-value">${formattedDate}</div>
                        </div>
                        <div class="email-detail-body" style="${email.is_html ? '' : 'white-space: pre-wrap;'}"></div>
                        <div class="email-modal-actions">
                            <button class="btn-reply" onclick="replyToEmail('${escapeHtml(replyEmail)}', '${escapeHtml(replyName)}', '${escapeHtml(email.subject)}', '${escapeHtml(email.date)}')">
                                <i class="fas fa-reply"></i>
                                Reply
                            </button>
                        </div>
                    `;
                    
                    // Insert email body separately to avoid escaping HTML
                    const bodyContainer = emailDetails.querySelector('.email-detail-body');
                    if (email.is_html) {
                        bodyContainer.innerHTML = email.body;
                    } else {
                        bodyContainer.textContent = email.body;
                    }
                    
                    // Store email data for reply
                    emailDetails.dataset.replyEmail = email.from.email;
                    emailDetails.dataset.replyName = email.from.name || email.from.email;
                    emailDetails.dataset.replySubject = email.subject;
                    emailDetails.dataset.replyDate = email.date;
                    emailDetails.dataset.replyBody = email.is_html ? email.body : email.body;
                    emailDetails.dataset.replyIsHtml = email.is_html;
                    
                    emailModalBody.innerHTML = '';
                    emailModalBody.appendChild(emailDetails);
                } else {
                    emailModalBody.innerHTML = `
                        <div style="text-align: center; padding: 40px;">
                            <i class="fas fa-exclamation-circle" style="font-size: 48px; color: #ef4444; margin-bottom: 16px;"></i>
                            <p>${data.message || 'Failed to load email'}</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                emailModalBody.innerHTML = `
                    <div style="text-align: center; padding: 40px;">
                        <i class="fas fa-exclamation-circle" style="font-size: 48px; color: #ef4444; margin-bottom: 16px;"></i>
                        <p>Error loading email. Please try again.</p>
                    </div>
                `;
            });
    };
    
    // Create email modal
    function createEmailModal() {
        const modal = document.createElement('div');
        modal.id = 'emailModal';
        modal.className = 'email-modal';
        modal.innerHTML = `
            <div class="email-modal-content">
                <div class="email-modal-header">
                    <div>
                        <div class="email-modal-title">Email Details</div>
                    </div>
                    <button class="email-modal-close" onclick="closeEmailModal()">×</button>
                </div>
                <div class="email-modal-body" id="emailModalBody"></div>
            </div>
        `;
        document.body.appendChild(modal);
        
        // Close on background click
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeEmailModal();
            }
        });
    }
    
    // Close email modal
    window.closeEmailModal = function() {
        const modal = document.getElementById('emailModal');
        if (modal) {
            modal.classList.remove('active');
        }
    };
    
    // Reply to email
    window.replyToEmail = function(replyEmail, replyName, originalSubject, originalDate) {
        // Close modal
        closeEmailModal();
        
        // Scroll to send email form
        const sendForm = document.getElementById('sendEmailForm');
        if (sendForm) {
            sendForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
            
            // Set recipient type to external
            const recipientTypeSelect = document.getElementById('recipient_type');
            if (recipientTypeSelect) {
                recipientTypeSelect.value = 'external';
                // Trigger change event to show external section
                recipientTypeSelect.dispatchEvent(new Event('change'));
                
                // Wait a bit for the section to show, then add email
                setTimeout(() => {
                    const emailTagsInput = document.getElementById('external_emails_input');
                    const emailTagsContainer = document.getElementById('emailTagsContainer');
                    
                    if (emailTagsInput && emailTagsContainer) {
                        // Clear any existing tags
                        emailTagsContainer.querySelectorAll('.email-tag').forEach(tag => tag.remove());
                        // Clear emailTags array
                        if (typeof window.emailTagsArray !== 'undefined') {
                            window.emailTagsArray.length = 0;
                        }
                        
                        // Add reply email as tag
                        if (typeof window.createEmailTagForReply !== 'undefined') {
                            window.createEmailTagForReply(replyEmail);
                        } else {
                            // Fallback: create tag manually
                            const trimmedEmail = replyEmail.trim();
                            if (trimmedEmail && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(trimmedEmail)) {
                                const tag = document.createElement('span');
                                tag.className = 'email-tag';
                                tag.dataset.email = trimmedEmail;
                                
                                const emailText = document.createTextNode(trimmedEmail);
                                const removeBtn = document.createElement('span');
                                removeBtn.className = 'email-tag-remove';
                                removeBtn.textContent = '×';
                                removeBtn.addEventListener('click', function() {
                                    tag.remove();
                                });
                                
                                tag.appendChild(emailText);
                                tag.appendChild(removeBtn);
                                emailTagsContainer.insertBefore(tag, emailTagsInput);
                            }
                        }
                        
                        // Set subject with "Re: " prefix if not already present
                        const subjectInput = document.getElementById('email_subject');
                        if (subjectInput) {
                            let replySubject = originalSubject.trim();
                            if (!replySubject.toLowerCase().startsWith('re:')) {
                                replySubject = 'Re: ' + replySubject;
                            }
                            subjectInput.value = replySubject;
                        }
                        
                        // Set email content with original message
                        const contentTextarea = document.getElementById('email_content');
                        if (contentTextarea) {
                            const originalDateFormatted = new Date(originalDate).toLocaleString();
                            const replyContent = `\n\n--- Original Message ---\nFrom: ${replyName} <${replyEmail}>\nDate: ${originalDateFormatted}\nSubject: ${originalSubject}\n\n`;
                            contentTextarea.value = replyContent;
                            // Focus and move cursor to the beginning
                            setTimeout(() => {
                                contentTextarea.focus();
                                contentTextarea.setSelectionRange(0, 0);
                            }, 50);
                        }
                    }
                }, 200);
            }
        }
    };
    
    // Escape HTML (only for plain text)
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Auto-load inbox on page load
    // loadEmails(); // Uncomment to auto-load on page load
})();
</script>

<?php
// Only close HTML if accessed directly (not included by controller)
if (!defined('EMAIL_SUBPAGE') || EMAIL_SUBPAGE !== true) {
    echo '</div></div></div>'; // Close content-area, main-content-area, dashboard-container
    echo '</body></html>';
}
?>

