<?php
$pageTitle = 'Version Control - Admin';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAdmin();

// Helper function to format bytes (define before use)
if (!function_exists('formatBytes')) {
    function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

// Get data if not already set by controller
if (!isset($currentVersion) || !isset($versions) || !isset($updateLogs)) {
    $db = Database::getInstance();
    
    // Initialize with empty values
    $currentVersion = null;
    $versions = [];
    $updateLogs = [];
    
    try {
        // Check if version control tables exist
        $checkTables = $db->query("SHOW TABLES LIKE 'system_version_info'");
        $tablesExist = $checkTables && $checkTables->rowCount() > 0;
        
        if ($tablesExist) {
            // Get current system version
            $sql = "SELECT * FROM system_version_info LIMIT 1";
            $stmt = $db->query($sql);
            $currentVersion = $stmt ? $stmt->fetch() : null;
            
            // Get all versions
            $sql = "SELECT * FROM system_versions ORDER BY release_date DESC";
            $stmt = $db->query($sql);
            $versions = $stmt ? $stmt->fetchAll() : [];
            
            // Get update logs
            $sql = "SELECT ul.*, u.full_name, u.email 
                    FROM update_logs ul 
                    LEFT JOIN users u ON ul.applied_by = u.id 
                    ORDER BY ul.applied_date DESC 
                    LIMIT 20";
            $stmt = $db->query($sql);
            $updateLogs = $stmt ? $stmt->fetchAll() : [];
        }
    } catch (Exception $e) {
        // If tables don't exist or query fails, continue with empty arrays
        error_log("Version control tables not found or error: " . $e->getMessage());
        $currentVersion = null;
        $versions = [];
        $updateLogs = [];
    }
}

include __DIR__ . '/../../includes/head.php';
include __DIR__ . '/../../includes/admin-sidebar.php';
include __DIR__ . '/../../includes/admin-modals.php';
?>

<style>
    .version-control-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .page-header {
        margin-bottom: 30px;
    }
    
    .page-header h1 {
        font-size: 28px;
        color: #202124;
        margin: 0 0 8px 0;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .page-header p {
        color: #666;
        font-size: 15px;
        margin: 0;
    }
    
    .current-version-badge {
        display: inline-block;
        padding: 6px 12px;
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        color: white;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        margin-left: 12px;
    }
    
    .sections-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 24px;
    }
    
    @media (max-width: 768px) {
        .sections-grid {
            grid-template-columns: 1fr;
        }
        
        .version-control-container {
            padding: 15px;
        }
        
        .section-card {
            padding: 20px;
        }
        
        .versions-table {
            font-size: 12px;
        }
        
        .versions-table th,
        .versions-table td {
            padding: 8px 4px;
        }
        
        .page-header h1 {
            font-size: 22px;
            flex-wrap: wrap;
        }
        
        .current-version-badge {
            margin-left: 0;
            margin-top: 8px;
        }
    }
    
    .section-card {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        margin-bottom: 24px;
    }
    
    .section-card.full-width {
        grid-column: 1 / -1;
    }
    
    .section-card:last-child {
        margin-bottom: 0;
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
    
    .form-input {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #dadce0;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s;
    }
    
    .form-input:focus {
        outline: none;
        border-color: #1e3a8a;
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
    }
    
    .form-textarea {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #dadce0;
        border-radius: 8px;
        font-size: 14px;
        min-height: 100px;
        resize: vertical;
        font-family: inherit;
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
        box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
    }
    
    .btn-success {
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        color: white;
    }
    
    .btn-success:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }
    
    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    .versions-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .versions-table th,
    .versions-table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .versions-table th {
        background: #f8f9fa;
        font-weight: 600;
        color: #202124;
        font-size: 13px;
    }
    
    .versions-table tr:hover {
        background: #f8f9fa;
    }
    
    .table-responsive {
        overflow-x: auto;
    }
    
    /* Mobile Version Cards */
    .mobile-version-cards {
        display: none;
    }
    
    .version-card-mobile {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .version-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .version-info-mobile {
        flex: 1;
    }
    
    .version-name-mobile {
        font-weight: 600;
        color: #1f2937;
        font-size: 16px;
        margin-bottom: 4px;
    }
    
    .version-date-mobile {
        color: #6b7280;
        font-size: 14px;
    }
    
    .expand-btn {
        background: #f3f4f6;
        border: none;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #374151;
        font-size: 16px;
        transition: all 0.3s;
    }
    
    .expand-btn:hover {
        background: #e5e7eb;
    }
    
    .expand-btn.active {
        background: #3b82f6;
        color: white;
        transform: rotate(180deg);
    }
    
    .version-details-mobile {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
    }
    
    .version-details-mobile.expanded {
        max-height: 500px;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid #e5e7eb;
    }
    
    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        font-size: 14px;
    }
    
    .detail-label {
        color: #6b7280;
        font-weight: 500;
    }
    
    .detail-value {
        color: #1f2937;
        font-weight: 600;
    }
    
    .mobile-actions {
        display: flex;
        gap: 8px;
        margin-top: 12px;
        flex-wrap: wrap;
    }
    
    .mobile-actions a, .mobile-actions button {
        flex: 1;
        min-width: 120px;
        padding: 10px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        border: none;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .btn-download-mobile {
        background: #eff6ff;
        color: #1d4ed8;
    }
    
    .btn-delete-mobile {
        background: #fee2e2;
        color: #dc2626;
    }
    
    .btn-delete {
        background: #fee2e2;
        color: #dc2626;
        padding: 6px 12px;
        font-size: 12px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-left: 8px;
    }
    
    .btn-delete:hover {
        background: #fecaca;
        transform: translateY(-1px);
    }
    
    .version-badge {
        display: inline-block;
        padding: 4px 8px;
        background: #e0e7ff;
        color: #1e3a8a;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .status-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .status-success {
        background: #d1fae5;
        color: #065f46;
    }
    
    .status-failed {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .status-partial {
        background: #fef3c7;
        color: #92400e;
    }
    
    .file-upload-area {
        border: 2px dashed #dadce0;
        border-radius: 8px;
        padding: 40px;
        text-align: center;
        transition: all 0.3s;
        cursor: pointer;
        position: relative;
        z-index: 1;
    }
    
    .file-upload-area:hover {
        border-color: #1e3a8a;
        background: #f8f9fa;
    }
    
    .file-upload-area.dragover {
        border-color: #1e3a8a;
        background: #e0e7ff;
    }
    
    .file-upload-area input[type="file"] {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
        z-index: 2;
    }
    
    .progress-bar {
        width: 100%;
        height: 8px;
        background: #e5e7eb;
        border-radius: 4px;
        overflow: hidden;
        margin-top: 12px;
        display: none;
    }
    
    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #1e3a8a 0%, #3b82f6 100%);
        width: 0%;
        transition: width 0.3s;
    }
    
    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    
    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }
    
    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }
    
    .alert-info {
        background: #dbeafe;
        color: #1e40af;
        border: 1px solid #93c5fd;
    }
    
    .loading-spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid #ffffff;
        border-top-color: transparent;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    @media (max-width: 768px) {
        .sections-grid {
            grid-template-columns: 1fr;
        }
        
        .version-control-container {
            padding: 15px;
        }
        
        .section-card {
            padding: 20px;
        }
        
        .table-responsive {
            display: none;
        }
        
        .mobile-version-cards {
            display: block;
        }
        
        .page-header h1 {
            font-size: 22px;
            flex-wrap: wrap;
        }
        
        .current-version-badge {
            margin-left: 0;
            margin-top: 8px;
            display: block;
            width: fit-content;
        }
        
        .file-upload-area {
            padding: 30px 20px;
        }
        
        .file-upload-area i {
            font-size: 36px !important;
        }
        
        .btn {
            padding: 10px 16px;
            font-size: 13px;
        }
        
        .download-text {
            display: none;
        }
        
        .card-title {
            font-size: 18px;
        }
    }
    
    @media (max-width: 480px) {
        .versions-table {
            font-size: 11px;
        }
        
        .versions-table th,
        .versions-table td {
            padding: 6px 3px;
        }
        
        .version-badge {
            font-size: 10px;
            padding: 3px 6px;
        }
        
        .status-badge {
            font-size: 10px;
            padding: 3px 6px;
        }
    }
</style>

<div class="version-control-container">
    <div class="page-header">
        <h1>
            <i class="fas fa-code-branch"></i>
            Version Control & Backup System
            <?php if ($currentVersion): ?>
                <span class="current-version-badge">Current: v<?php echo htmlspecialchars($currentVersion['current_version'] ?? '1.0.0'); ?></span>
            <?php endif; ?>
        </h1>
        <p>
            <strong>How it works:</strong> This is a backup and restore system. 
            <br>1. <strong>Create Package</strong> - On an updated site, create a backup package with all latest files and database updates
            <br>2. <strong>Apply Package</strong> - On an outdated site, upload the backup package to update files and database structures automatically
        </p>
    </div>
    
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>
    
    
    <div class="sections-grid">
        <!-- Create Package Section -->
        <div class="section-card">
            <h2 class="card-title">
                <i class="fas fa-download"></i>
                Create Backup Package (From Updated Site)
            </h2>
            <p style="color: #666; font-size: 13px; margin-bottom: 20px;">
                Use this on your <strong>updated/main site</strong> to create a backup package containing all latest files and database updates. 
                This package can then be uploaded to outdated sites to bring them up to date.
            </p>
            
            <form id="createPackageForm" method="POST" action="#">
                <div class="form-group">
                    <label class="form-label">Version Number</label>
                    <input type="text" name="version" class="form-input" placeholder="e.g., 1.2.0" pattern="^\d+\.\d+\.\d+$" required>
                    <small style="color: #666; font-size: 12px;">Format: X.Y.Z (e.g., 1.0.0, 1.2.3)</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Release Notes</label>
                    <textarea name="notes" class="form-textarea" placeholder="Describe what's new in this version..."></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary" id="createPackageBtn">
                    <i class="fas fa-download"></i>
                    Create Package
                </button>
                
                <div class="progress-bar" id="createProgress">
                    <div class="progress-fill" id="createProgressFill"></div>
                </div>
            </form>
        </div>
        
        <!-- Apply Update Section -->
        <div class="section-card">
            <h2 class="card-title">
                <i class="fas fa-upload"></i>
                Apply Backup Package (To Outdated Site)
            </h2>
            <p style="color: #666; font-size: 13px; margin-bottom: 20px;">
                Use this on an <strong>outdated site</strong> to upload a backup package from your updated site. 
                The system will automatically update files, create missing database structures, and apply database migrations.
            </p>
            
            <form id="applyUpdateForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="form-label">Upload Update Package (ZIP)</label>
                    <div class="file-upload-area" id="fileUploadArea">
                        <i class="fas fa-cloud-upload-alt" style="font-size: 48px; color: #dadce0; margin-bottom: 12px;"></i>
                        <p style="margin: 0; color: #666;">Drag & drop ZIP file here or click to browse</p>
                        <input type="file" name="update_package" id="updatePackageFile" accept=".zip" required>
                    </div>
                    <div id="fileName" style="margin-top: 12px; font-size: 14px; color: #666; display: none;"></div>
                </div>
                
                <button type="submit" class="btn btn-success" id="applyUpdateBtn">
                    <i class="fas fa-check-circle"></i>
                    Apply Update
                </button>
                
                <div class="progress-bar" id="applyProgress">
                    <div class="progress-fill" id="applyProgressFill"></div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Versions List -->
    <div class="section-card full-width">
        <h2 class="card-title">
            <i class="fas fa-list"></i>
            Available Versions
        </h2>
        
        <?php if (empty($versions)): ?>
            <p style="color: #666; text-align: center; padding: 40px;">No versions created yet</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="versions-table">
                    <thead>
                        <tr>
                            <th>Version</th>
                            <th>Release Date</th>
                            <th>Notes</th>
                            <th>File Count</th>
                            <th>Package Size</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($versions as $version): ?>
                            <?php
                            // Calculate package size
                            $packageSize = isset($version['package_size']) ? (int)$version['package_size'] : 0;
                            $packageDir = BASE_PATH . '/packages';
                            
                            if (!is_dir($packageDir)) {
                                @mkdir($packageDir, 0755, true);
                            }
                            
                            if ($packageSize <= 0) {
                                $packageFiles = glob($packageDir . '/update-v' . $version['version'] . '-*.zip');
                                if (!empty($packageFiles) && file_exists($packageFiles[0])) {
                                    $actualSize = @filesize($packageFiles[0]);
                                    if ($actualSize !== false) {
                                        $packageSize = $actualSize;
                                    }
                                }
                            }
                            
                            // Find package file
                            $packageFiles = glob($packageDir . '/update-v' . $version['version'] . '-*.zip');
                            $downloadUrl = null;
                            $packageFile = null;
                            
                            if (!empty($packageFiles) && file_exists($packageFiles[0])) {
                                $packageFile = basename($packageFiles[0]);
                                $downloadUrl = '/packages/' . $packageFile;
                            } else {
                                $releaseDate = date('Y-m-d', strtotime($version['release_date']));
                                $possibleFiles = [
                                    $packageDir . '/update-v' . $version['version'] . '-' . $releaseDate . '.zip',
                                    $packageDir . '/update-v' . $version['version'] . '-' . date('Ymd', strtotime($version['release_date'])) . '.zip'
                                ];
                                foreach ($possibleFiles as $possibleFile) {
                                    if (file_exists($possibleFile)) {
                                        $packageFile = basename($possibleFile);
                                        $downloadUrl = '/packages/' . $packageFile;
                                        break;
                                    }
                                }
                            }
                            ?>
                            <tr>
                                <td><span class="version-badge">v<?php echo htmlspecialchars($version['version']); ?></span></td>
                                <td><?php echo date('Y-m-d H:i', strtotime($version['release_date'])); ?></td>
                                <td><?php echo htmlspecialchars(substr($version['notes'] ?? '', 0, 100)); ?></td>
                                <td><?php echo number_format($version['file_count'] ?? 0); ?></td>
                                <td><?php echo $packageSize > 0 ? formatBytes($packageSize) : 'N/A'; ?></td>
                                <td>
                                    <?php if ($downloadUrl && $packageFile): ?>
                                        <a href="<?php echo htmlspecialchars($downloadUrl); ?>" 
                                           class="btn btn-primary" 
                                           style="padding: 6px 12px; font-size: 12px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;" 
                                           download
                                           onclick="return confirm('Download package v<?php echo htmlspecialchars($version['version']); ?>?');">
                                            <i class="fas fa-download"></i> <span class="download-text">Download</span>
                                        </a>
                                        <button onclick="deleteVersion(<?php echo $version['id']; ?>, '<?php echo htmlspecialchars($version['version']); ?>')" 
                                                class="btn-delete"
                                                title="Delete this version">
                                            <i class="fas fa-trash"></i> <span class="download-text">Delete</span>
                                        </button>
                                    <?php else: ?>
                                        <span style="color: #999; font-size: 12px;">Package not found</span>
                                        <button onclick="deleteVersion(<?php echo $version['id']; ?>, '<?php echo htmlspecialchars($version['version']); ?>')" 
                                                class="btn-delete"
                                                title="Delete this version">
                                            <i class="fas fa-trash"></i> <span class="download-text">Delete</span>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Mobile View -->
            <div class="mobile-version-cards">
                <?php foreach ($versions as $version): ?>
                    <?php
                    // Calculate package size
                    $packageSize = isset($version['package_size']) ? (int)$version['package_size'] : 0;
                    $packageDir = BASE_PATH . '/packages';
                    
                    if (!is_dir($packageDir)) {
                        @mkdir($packageDir, 0755, true);
                    }
                    
                    if ($packageSize <= 0) {
                        $packageFiles = glob($packageDir . '/update-v' . $version['version'] . '-*.zip');
                        if (!empty($packageFiles) && file_exists($packageFiles[0])) {
                            $actualSize = @filesize($packageFiles[0]);
                            if ($actualSize !== false) {
                                $packageSize = $actualSize;
                            }
                        }
                    }
                    
                    // Find package file
                    $packageFiles = glob($packageDir . '/update-v' . $version['version'] . '-*.zip');
                    $downloadUrl = null;
                    $packageFile = null;
                    
                    if (!empty($packageFiles) && file_exists($packageFiles[0])) {
                        $packageFile = basename($packageFiles[0]);
                        $downloadUrl = '/packages/' . $packageFile;
                    } else {
                        $releaseDate = date('Y-m-d', strtotime($version['release_date']));
                        $possibleFiles = [
                            $packageDir . '/update-v' . $version['version'] . '-' . $releaseDate . '.zip',
                            $packageDir . '/update-v' . $version['version'] . '-' . date('Ymd', strtotime($version['release_date'])) . '.zip'
                        ];
                        foreach ($possibleFiles as $possibleFile) {
                            if (file_exists($possibleFile)) {
                                $packageFile = basename($possibleFile);
                                $downloadUrl = '/packages/' . $packageFile;
                                break;
                            }
                        }
                    }
                    ?>
                    <div class="version-card-mobile">
                        <div class="version-card-header">
                            <div class="version-info-mobile">
                                <div class="version-name-mobile">
                                    <span class="version-badge">v<?php echo htmlspecialchars($version['version']); ?></span>
                                </div>
                                <div class="version-date-mobile"><?php echo date('Y-m-d H:i', strtotime($version['release_date'])); ?></div>
                            </div>
                            <button class="expand-btn" onclick="toggleVersionDetails(this)">
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </div>
                        <div class="version-details-mobile">
                            <div class="detail-row">
                                <span class="detail-label">Release Notes</span>
                                <span class="detail-value" style="text-align: right; max-width: 60%;"><?php echo htmlspecialchars(substr($version['notes'] ?? 'No notes', 0, 50)); ?><?php echo strlen($version['notes'] ?? '') > 50 ? '...' : ''; ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">File Count</span>
                                <span class="detail-value"><?php echo number_format($version['file_count'] ?? 0); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Package Size</span>
                                <span class="detail-value"><?php echo $packageSize > 0 ? formatBytes($packageSize) : 'N/A'; ?></span>
                            </div>
                            <div class="mobile-actions">
                                <?php if ($downloadUrl && $packageFile): ?>
                                    <a href="<?php echo htmlspecialchars($downloadUrl); ?>" 
                                       class="btn-download-mobile"
                                       download
                                       onclick="return confirm('Download package v<?php echo htmlspecialchars($version['version']); ?>?');">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                <?php endif; ?>
                                <button onclick="deleteVersion(<?php echo $version['id']; ?>, '<?php echo htmlspecialchars($version['version']); ?>')" 
                                        class="btn-delete-mobile">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Update Logs -->
    <div class="section-card full-width">
        <h2 class="card-title">
            <i class="fas fa-history"></i>
            Update History
        </h2>
        
        <?php if (empty($updateLogs)): ?>
            <p style="color: #666; text-align: center; padding: 40px;">No updates applied yet</p>
        <?php else: ?>
            <table class="versions-table">
                <thead>
                    <tr>
                        <th>Version</th>
                        <th>Applied Date</th>
                        <th>Applied By</th>
                        <th>Status</th>
                        <th>Files Updated</th>
                        <th>Migrations Applied</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($updateLogs as $log): ?>
                        <tr>
                            <td><span class="version-badge">v<?php echo htmlspecialchars($log['version']); ?></span></td>
                            <td><?php echo date('Y-m-d H:i', strtotime($log['applied_date'])); ?></td>
                            <td><?php echo htmlspecialchars($log['full_name'] ?? 'System'); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo htmlspecialchars($log['status']); ?>">
                                    <?php echo ucfirst(htmlspecialchars($log['status'])); ?>
                                </span>
                            </td>
                            <td><?php echo number_format($log['files_updated']); ?></td>
                            <td><?php echo number_format($log['migrations_applied']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<script type="text/javascript">
console.log('Version Control Script: Script file loaded');
(function() {
    'use strict';
    
    console.log('Version Control Script: Starting initialization...');
    
    // Helper function to format bytes
    function formatBytes(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }
    
    function initVersionControl() {
        console.log('Version Control Script: Initializing...');
        // Create Package Form
        const createPackageForm = document.getElementById('createPackageForm');
        const createPackageBtn = document.getElementById('createPackageBtn');
        
        console.log('Version Control Script: Form elements found:', {
            form: !!createPackageForm,
            button: !!createPackageBtn
        });
        
        if (createPackageForm && createPackageBtn) {
            console.log('Version Control Script: Attaching create package form handler...');
            // Handle form submission
            createPackageForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const formData = new FormData(this);
                const version = formData.get('version');
                const notes = formData.get('notes');
                
                if (!version || !version.match(/^\d+\.\d+\.\d+$/)) {
                    alert('Please enter a valid version number (e.g., 1.0.0)');
                    return false;
                }
                
                const btn = document.getElementById('createPackageBtn');
                const progressBar = document.getElementById('createProgress');
                const progressFill = document.getElementById('createProgressFill');
                
                btn.disabled = true;
                btn.innerHTML = '<span class="loading-spinner"></span> Creating...';
                if (progressBar) progressBar.style.display = 'block';
                if (progressFill) progressFill.style.width = '10%';
                
                // Use absolute path from root for API
                const apiUrl = '/api/create-update-package.php';
                
                try {
                    const response = await fetch(apiUrl, {
                        method: 'POST',
                        body: formData
                    });
                    
                    if (progressFill) progressFill.style.width = '50%';
                    
                    if (!response.ok) {
                        const errorText = await response.text();
                        throw new Error('Server error: ' + response.status + ' - ' + errorText.substring(0, 200));
                    }
                    
                    const responseText = await response.text();
                    let data;
                    try {
                        data = JSON.parse(responseText);
                    } catch (parseError) {
                        throw new Error('Invalid response from server. Check console for details.');
                    }
                    
                    if (progressFill) progressFill.style.width = '100%';
                    
                    if (data.success) {
                        setTimeout(() => {
                            alert('Package created successfully!\n\nVersion: ' + data.version + '\nFiles: ' + (data.file_count || 0) + '\nSize: ' + formatBytes(data.package_size || 0));
                            location.reload();
                        }, 500);
                    } else {
                        alert('Error: ' + (data.message || 'Failed to create package'));
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-download"></i> Create Package';
                        if (progressBar) progressBar.style.display = 'none';
                    }
                } catch (error) {
                    alert('Error: ' + error.message + '\n\nCheck browser console (F12) for details.');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-download"></i> Create Package';
                    if (progressBar) progressBar.style.display = 'none';
                }
                
                return false;
            });
        }
        
        // File Upload Area
        const fileUploadArea = document.getElementById('fileUploadArea');
        const fileInput = document.getElementById('updatePackageFile');
        const fileName = document.getElementById('fileName');
        
        console.log('Version Control Script: Upload elements found:', {
            uploadArea: !!fileUploadArea,
            fileInput: !!fileInput,
            fileName: !!fileName
        });
        
        if (fileUploadArea && fileInput) {
            console.log('Version Control Script: Attaching upload handlers...');
            
            // Make sure file input is accessible
            fileInput.style.position = 'absolute';
            fileInput.style.top = '0';
            fileInput.style.left = '0';
            fileInput.style.width = '100%';
            fileInput.style.height = '100%';
            fileInput.style.opacity = '0';
            fileInput.style.cursor = 'pointer';
            fileInput.style.zIndex = '2';
            
            // Click handler for upload area
            fileUploadArea.addEventListener('click', function(e) {
                // Don't prevent default if clicking directly on the input
                if (e.target !== fileInput) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                // Trigger file input click
                fileInput.click();
            });
            
            // Also allow direct clicking on the file input
            fileInput.addEventListener('click', function(e) {
                e.stopPropagation(); // Prevent bubbling to upload area
            });
            
            fileInput.addEventListener('change', function() {
                if (this.files && this.files.length > 0) {
                    const file = this.files[0];
                    if (!file.name.toLowerCase().endsWith('.zip')) {
                        alert('Please select a ZIP file');
                        this.value = '';
                        if (fileName) fileName.style.display = 'none';
                        return;
                    }
                    if (fileName) {
                        fileName.textContent = 'Selected: ' + file.name + ' (' + formatBytes(file.size) + ')';
                        fileName.style.display = 'block';
                    }
                }
            });
            
            fileUploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                e.stopPropagation();
                fileUploadArea.classList.add('dragover');
            });
            
            fileUploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                e.stopPropagation();
                fileUploadArea.classList.remove('dragover');
            });
            
            fileUploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                fileUploadArea.classList.remove('dragover');
                
                if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                    const droppedFile = e.dataTransfer.files[0];
                    
                    if (!droppedFile.name.toLowerCase().endsWith('.zip')) {
                        alert('Please drop a ZIP file');
                        return;
                    }
                    
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(droppedFile);
                    fileInput.files = dataTransfer.files;
                    
                    const changeEvent = new Event('change', { bubbles: true });
                    fileInput.dispatchEvent(changeEvent);
                }
            });
        }
        
        // Apply Update Form
        const applyUpdateForm = document.getElementById('applyUpdateForm');
        
        console.log('Version Control Script: Apply form found:', !!applyUpdateForm);
        
        if (applyUpdateForm) {
            console.log('Version Control Script: Attaching apply update form handler...');
            applyUpdateForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const fileInput = document.getElementById('updatePackageFile');
                if (!fileInput || !fileInput.files || !fileInput.files.length) {
                    alert('Please select a ZIP file');
                    return false;
                }
                
                const formData = new FormData(this);
                
                // Debug: Check if file is in FormData
                console.log('File input:', fileInput);
                console.log('File selected:', fileInput.files[0]);
                console.log('File name:', fileInput.files[0]?.name);
                console.log('File size:', fileInput.files[0]?.size);
                
                // Verify file is in FormData
                if (!formData.has('update_package')) {
                    alert('Error: File not found in form data. Please try selecting the file again.');
                    return false;
                }
                
                const btn = document.getElementById('applyUpdateBtn');
                const progressBar = document.getElementById('applyProgress');
                const progressFill = document.getElementById('applyProgressFill');
                
                btn.disabled = true;
                btn.innerHTML = '<span class="loading-spinner"></span> Applying...';
                if (progressBar) progressBar.style.display = 'block';
                if (progressFill) progressFill.style.width = '10%';
                
                // Use absolute path from root for API
                const apiUrl = '/api/apply-update.php';
                
                try {
                    if (progressFill) progressFill.style.width = '30%';
                    
                    console.log('Sending request to:', apiUrl);
                    console.log('FormData entries:', Array.from(formData.entries()).map(([k, v]) => [k, v instanceof File ? v.name + ' (' + v.size + ' bytes)' : v]));
                    
                    const response = await fetch(apiUrl, {
                        method: 'POST',
                        body: formData,
                        // Don't set Content-Type header - let browser set it with boundary
                    });
                    
                    if (progressFill) progressFill.style.width = '70%';
                    
                    if (!response.ok) {
                        const errorText = await response.text();
                        console.error('Server response error:', response.status, errorText);
                        throw new Error('Server error: ' + response.status + ' - ' + errorText.substring(0, 200));
                    }
                    
                    const responseText = await response.text();
                    console.log('Server response:', responseText.substring(0, 500));
                    
                    let data;
                    try {
                        data = JSON.parse(responseText);
                    } catch (e) {
                        console.error('Failed to parse JSON response:', e);
                        console.error('Response text:', responseText);
                        throw new Error('Invalid server response. Check console for details.');
                    }
                    
                    if (progressFill) progressFill.style.width = '100%';
                    
                    if (data.success) {
                        setTimeout(() => {
                            let message = 'Update applied successfully!\n\n';
                            message += 'Version: ' + data.version + '\n';
                            message += 'Files Updated: ' + (data.files_updated || 0) + '\n';
                            message += 'Migrations Applied: ' + (data.migrations_applied || 0);
                            if (data.errors && data.errors.length > 0) {
                                message += '\n\n⚠️ Warnings/Errors:\n';
                                data.errors.forEach((error, index) => {
                                    message += (index + 1) + '. ' + error + '\n';
                                });
                                message += '\nNote: Some files may not have been updated. Check server logs for details.';
                            }
                            alert(message);
                            location.reload();
                        }, 500);
                    } else {
                        let errorMsg = 'Error: ' + (data.message || 'Unknown error');
                        if (data.errors && data.errors.length > 0) {
                            errorMsg += '\n\nDetails:\n';
                            data.errors.forEach((error, index) => {
                                errorMsg += (index + 1) + '. ' + error + '\n';
                            });
                        }
                        alert(errorMsg);
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-check-circle"></i> Apply Update';
                        if (progressBar) progressBar.style.display = 'none';
                    }
                } catch (error) {
                    alert('Error: ' + error.message + '\n\nCheck browser console (F12) for details.');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-check-circle"></i> Apply Update';
                    if (progressBar) progressBar.style.display = 'none';
                }
                
                return false;
            });
        }
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        console.log('Version Control Script: Waiting for DOMContentLoaded...');
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Version Control Script: DOMContentLoaded fired, initializing...');
            initVersionControl();
        });
    } else {
        console.log('Version Control Script: DOM already ready, initializing immediately...');
        initVersionControl();
    }
    
    console.log('Version Control Script: Setup complete');
})();

// Delete version function
function deleteVersion(versionId, versionNumber) {
    if (!confirm('Are you sure you want to delete version v' + versionNumber + '?\n\nThis will:\n- Delete the version record\n- Delete the package file(s)\n- This action cannot be undone')) {
        return;
    }
    
    fetch('/api/delete-version.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ version_id: versionId, version: versionNumber })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Version deleted successfully!');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to delete version'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the version: ' + error.message);
    });
}

// Toggle version details for mobile
function toggleVersionDetails(button) {
    const card = button.closest('.version-card-mobile');
    const details = card.querySelector('.version-details-mobile');
    const isExpanded = details.classList.contains('expanded');
    
    if (isExpanded) {
        details.classList.remove('expanded');
        button.classList.remove('active');
    } else {
        details.classList.add('expanded');
        button.classList.add('active');
    }
}
</script>
</body>
</html>

