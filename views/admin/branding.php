<?php
$pageTitle = 'Branding Management - Admin';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAdmin();

$systemSettings = SystemSettings::getInstance();
$siteName = $systemSettings->get('site_name', 'SecureBank Online');
$siteTagline = $systemSettings->get('site_tagline', 'Your Trusted Banking Partner');
$siteLogo = getSiteLogo();
$siteFavicon = getSiteFavicon();

// Check if logo file exists
$hasLogo = false;
if ($siteLogo) {
    // Remove query parameters (like ?v=timestamp) from URL
    $cleanLogoUrl = strtok($siteLogo, '?');
    $logoPath = str_replace(SITE_URL, BASE_PATH, $cleanLogoUrl);
    $hasLogo = file_exists($logoPath);
    
    // Debug logging
    error_log("Branding Page - Logo URL: " . $siteLogo);
    error_log("Branding Page - Clean Logo URL: " . $cleanLogoUrl);
    error_log("Branding Page - Logo Path: " . $logoPath);
    error_log("Branding Page - Logo Exists: " . ($hasLogo ? 'YES' : 'NO'));
}

// Check if favicon file exists
$hasFavicon = false;
if ($siteFavicon) {
    // Remove query parameters (like ?v=timestamp) from URL
    $cleanFaviconUrl = strtok($siteFavicon, '?');
    $faviconPath = str_replace(SITE_URL, BASE_PATH, $cleanFaviconUrl);
    $hasFavicon = file_exists($faviconPath);
    
    // Debug logging
    error_log("Branding Page - Favicon URL: " . $siteFavicon);
    error_log("Branding Page - Clean Favicon URL: " . $cleanFaviconUrl);
    error_log("Branding Page - Favicon Path: " . $faviconPath);
    error_log("Branding Page - Favicon Exists: " . ($hasFavicon ? 'YES' : 'NO'));
}

include __DIR__ . '/../../includes/head.php';
include __DIR__ . '/../../includes/admin-sidebar.php';
?>

<style>
    .branding-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .branding-header {
        margin-bottom: 30px;
    }
    
    .branding-header h1 {
        font-size: 28px;
        color: #202124;
        margin: 0 0 8px 0;
        font-weight: 600;
    }
    
    .branding-header p {
        color: #666;
        font-size: 15px;
        margin: 0;
    }
    
    .branding-section {
        background: white;
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    
    .section-title {
        font-size: 20px;
        color: #1e3a8a;
        font-weight: 600;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .upload-area {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-top: 20px;
    }
    
    .upload-box {
        border: 2px dashed #dadce0;
        border-radius: 12px;
        padding: 30px;
        text-align: center;
        transition: all 0.3s;
        cursor: pointer;
    }
    
    .upload-box:hover {
        border-color: #1e3a8a;
        background: #f8fafc;
    }
    
    .upload-box.dragover {
        border-color: #3b82f6;
        background: #e8f0ff;
    }
    
    .current-image {
        max-width: 200px;
        max-height: 100px;
        margin: 0 auto 20px;
        display: block;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .upload-icon {
        font-size: 48px;
        color: #1e3a8a;
        margin-bottom: 15px;
    }
    
    .upload-text h3 {
        font-size: 18px;
        color: #202124;
        margin: 0 0 8px 0;
    }
    
    .upload-text p {
        font-size: 14px;
        color: #666;
        margin: 0 0 15px 0;
    }
    
    .upload-specs {
        font-size: 12px;
        color: #999;
        margin-top: 10px;
    }
    
    .btn-upload {
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        color: white;
        padding: 10px 24px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .btn-upload:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
    }
    
    .preview-box {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-top: 20px;
    }
    
    .preview-title {
        font-size: 16px;
        font-weight: 600;
        color: #202124;
        margin-bottom: 15px;
    }
    
    .preview-items {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    
    .preview-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px;
        background: white;
        border-radius: 6px;
    }
    
    .preview-item strong {
        color: #202124;
    }
    
    .preview-item span {
        color: #666;
        font-size: 14px;
    }
    
    .success-message {
        background: #4caf50;
        color: white;
        padding: 16px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        animation: slideDown 0.3s ease-out;
    }
    
    .error-message {
        background: #f44336;
        color: white;
        padding: 16px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @media (max-width: 768px) {
        .upload-area {
            grid-template-columns: 1fr;
        }
        
        .branding-container {
            padding: 15px;
        }
    }
</style>

<div class="branding-container">
    <div id="messageContainer"></div>
    
    <div class="branding-header">
        <h1><i class="fas fa-palette"></i> Branding Management</h1>
        <p>Upload and manage your bank's logo, favicon, and branding assets</p>
    </div>
    
    <!-- Logo & Favicon Upload -->
    <div class="branding-section">
        <div class="section-title">
            <i class="fas fa-image"></i> Logo & Favicon
        </div>
        
        <div class="upload-area">
            <!-- Logo Upload -->
            <div class="upload-box" id="logoUploadBox" onclick="document.getElementById('logoInput').click()">
                <?php if ($hasLogo): ?>
                    <img src="<?php echo htmlspecialchars($siteLogo); ?>" alt="Current Logo" class="current-image" id="logoPreview">
                <?php endif; ?>
                <div class="upload-icon">
                    <i class="fas fa-image"></i>
                </div>
                <div class="upload-text">
                    <h3>Bank Logo</h3>
                    <p>Upload your bank's logo</p>
                    <button class="btn-upload" type="button">
                        <i class="fas fa-upload"></i> Choose File
                    </button>
                    <div class="upload-specs">
                        Recommended: 300x100px<br>
                        Formats: JPG, JPEG, PNG, SVG, WebP<br>
                        Max size: 2MB
                    </div>
                </div>
                <input type="file" id="logoInput" accept=".jpg,.jpeg,.png,.svg,.webp" style="display: none;" onchange="uploadFile('logo', this)">
            </div>
            
            <!-- Favicon Upload -->
            <div class="upload-box" id="faviconUploadBox" onclick="document.getElementById('faviconInput').click()">
                <?php if ($hasFavicon): ?>
                    <img src="<?php echo htmlspecialchars($siteFavicon); ?>" alt="Current Favicon" class="current-image" id="faviconPreview" style="max-width: 64px; max-height: 64px;">
                <?php endif; ?>
                <div class="upload-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="upload-text">
                    <h3>Favicon</h3>
                    <p>Upload your site's favicon</p>
                    <button class="btn-upload" type="button">
                        <i class="fas fa-upload"></i> Choose File
                    </button>
                    <div class="upload-specs">
                        Recommended: 32x32px or 64x64px<br>
                        Formats: ICO, PNG, JPG, JPEG, WebP<br>
                        Max size: 500KB
                    </div>
                </div>
                <input type="file" id="faviconInput" accept=".ico,.png,.jpg,.jpeg,.webp" style="display: none;" onchange="uploadFile('favicon', this)">
            </div>
        </div>
    </div>
    
    <!-- Current Branding Info -->
    <div class="branding-section">
        <div class="section-title">
            <i class="fas fa-info-circle"></i> Current Branding
        </div>
        <div class="preview-box">
            <div class="preview-title">Current Settings:</div>
            <div class="preview-items">
                <div class="preview-item">
                    <strong>Bank Name:</strong>
                    <span><?php echo htmlspecialchars($siteName); ?></span>
                </div>
                <div class="preview-item">
                    <strong>Tagline:</strong>
                    <span><?php echo htmlspecialchars($siteTagline); ?></span>
                </div>
                <div class="preview-item">
                    <strong>Logo:</strong>
                    <span>
                        <?php if ($hasLogo): ?>
                            <i class="fas fa-check-circle" style="color: #4caf50;"></i> Uploaded
                        <?php else: ?>
                            <i class="fas fa-times-circle" style="color: #f44336;"></i> Not uploaded
                        <?php endif; ?>
                    </span>
                </div>
                <div class="preview-item">
                    <strong>Favicon:</strong>
                    <span>
                        <?php if ($hasFavicon): ?>
                            <i class="fas fa-check-circle" style="color: #4caf50;"></i> Uploaded
                        <?php else: ?>
                            <i class="fas fa-times-circle" style="color: #f44336;"></i> Not uploaded
                        <?php endif; ?>
                    </span>
                </div>
            </div>
        </div>
        <p style="margin-top: 15px; color: #666; font-size: 14px;">
            <i class="fas fa-lightbulb"></i> 
            <strong>Tip:</strong> To update bank name, tagline, and other text settings, go to 
            <a href="<?php echo SITE_URL; ?>/admin/system-settings" style="color: #1e3a8a;">System Settings</a>
        </p>
    </div>
</div>

<script>
function uploadFile(type, input) {
    const file = input.files[0];
    if (!file) return;
    
    const formData = new FormData();
    formData.append('file', file);
    formData.append('type', type);
    
    // Show loading message
    showMessage('info', `Uploading ${type}...`);
    
    fetch('<?php echo SITE_URL; ?>/api/upload-branding.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('success', data.message);
            
            // Update preview
            if (type === 'logo') {
                const preview = document.getElementById('logoPreview');
                if (preview) {
                    preview.src = data.url;
                } else {
                    const img = document.createElement('img');
                    img.src = data.url;
                    img.alt = 'Current Logo';
                    img.className = 'current-image';
                    img.id = 'logoPreview';
                    document.getElementById('logoUploadBox').insertBefore(img, document.getElementById('logoUploadBox').firstChild);
                }
            } else {
                const preview = document.getElementById('faviconPreview');
                if (preview) {
                    preview.src = data.url;
                } else {
                    const img = document.createElement('img');
                    img.src = data.url;
                    img.alt = 'Current Favicon';
                    img.className = 'current-image';
                    img.id = 'faviconPreview';
                    img.style.maxWidth = '64px';
                    img.style.maxHeight = '64px';
                    document.getElementById('faviconUploadBox').insertBefore(img, document.getElementById('faviconUploadBox').firstChild);
                }
                
                // Update actual favicon in page
                let link = document.querySelector("link[rel~='icon']");
                if (!link) {
                    link = document.createElement('link');
                    link.rel = 'icon';
                    document.getElementsByTagName('head')[0].appendChild(link);
                }
                link.href = data.url;
            }
            
            // Reload page after 2 seconds to show new branding everywhere
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            showMessage('error', data.message);
        }
    })
    .catch(error => {
        console.error('Upload error:', error);
        showMessage('error', 'Upload failed. Please try again.');
    });
}

function showMessage(type, message) {
    const container = document.getElementById('messageContainer');
    const className = type === 'success' ? 'success-message' : (type === 'error' ? 'error-message' : 'success-message');
    const icon = type === 'success' ? 'check-circle' : (type === 'error' ? 'exclamation-circle' : 'spinner fa-spin');
    
    container.innerHTML = `
        <div class="${className}">
            <i class="fas fa-${icon}" style="font-size: 20px;"></i>
            <span>${message}</span>
        </div>
    `;
    
    if (type !== 'info') {
        setTimeout(() => {
            container.innerHTML = '';
        }, 5000);
    }
}

// Drag and drop support
['logoUploadBox', 'faviconUploadBox'].forEach(boxId => {
    const box = document.getElementById(boxId);
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        box.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
        box.addEventListener(eventName, () => box.classList.add('dragover'), false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        box.addEventListener(eventName, () => box.classList.remove('dragover'), false);
    });
    
    box.addEventListener('drop', function(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        
        if (files.length > 0) {
            const type = boxId === 'logoUploadBox' ? 'logo' : 'favicon';
            const input = document.getElementById(type + 'Input');
            input.files = files;
            uploadFile(type, input);
        }
    }, false);
});
</script>

</body>
</html>

