<?php
/**
 * Create Update Package API
 * Creates a ZIP file containing all files for an update package
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/version-control-config.php';

header('Content-Type: application/json');

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Check if POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get input
$version = $_POST['version'] ?? '';
$notes = $_POST['notes'] ?? '';

// Validate version format (e.g., 1.0.0, 1.2.3)
if (empty($version) || !preg_match('/^\d+\.\d+\.\d+$/', $version)) {
    echo json_encode(['success' => false, 'message' => 'Invalid version format. Use format: X.Y.Z (e.g., 1.0.0)']);
    exit;
}

try {
    $db = Database::getInstance();
    $userId = $_SESSION['user_id'];
    
    // Check if version already exists
    $sql = "SELECT id FROM system_versions WHERE version = ?";
    $stmt = $db->query($sql, [$version]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Version already exists']);
        exit;
    }
    
    // Load config
    $config = require __DIR__ . '/../includes/version-control-config.php';
    
    // Create temp directory
    $tempDir = sys_get_temp_dir() . '/update_package_' . uniqid();
    if (!mkdir($tempDir, 0755, true)) {
        throw new Exception('Failed to create temp directory');
    }
    
    $fileCount = 0;
    $totalSize = 0;
    $migrationTotalSize = 0; // Track migration file sizes separately
    
    // Function to check if file should be excluded
    function shouldExcludeFile($filePath, $excludePatterns) {
        foreach ($excludePatterns as $pattern) {
            if (preg_match($pattern, $filePath)) {
                return true;
            }
        }
        return false;
    }
    
    // Function to check if file should be included
    function shouldIncludeFile($filePath, $includePatterns) {
        foreach ($includePatterns as $pattern) {
            if (preg_match($pattern, $filePath)) {
                return true;
            }
        }
        return false;
    }
    
    // Recursive function to add files to ZIP
    function addFilesToDirectory($sourceDir, $targetDir, $basePath, $config, &$fileCount, &$totalSize) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sourceDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $file) {
            $sourcePath = $file->getRealPath();
            $relativePath = str_replace($basePath . DIRECTORY_SEPARATOR, '', $sourcePath);
            $relativePath = str_replace('\\', '/', $relativePath);
            
            // Skip migration files - they're handled separately and copied to migrations/ folder
            // Match various migration file patterns: *_migration.sql, migration-*.sql, *migration*.sql
            if (preg_match('/^database\/.*migration.*\.sql$/i', $relativePath)) {
                continue; // Skip migration files here - they'll be handled separately
            }
            
            // CRITICAL: Explicitly exclude config.php (double-check)
            if ($relativePath === 'config/config.php' || preg_match('/^config\/config\.php$/i', $relativePath)) {
                error_log("Version Control: ✅ Explicitly excluded config/config.php from package");
                continue; // Skip config.php - CRITICAL: must be excluded
            }
            
            // Check if should be excluded
            if (shouldExcludeFile($relativePath, $config['exclude_patterns'])) {
                // Log exclusions for debugging (but not for every file to avoid spam)
                if (strpos($relativePath, 'config') !== false || strpos($relativePath, '.env') !== false || strpos($relativePath, 'uploads') !== false) {
                    error_log("Version Control: ✅ Excluded file: {$relativePath}");
                }
                continue;
            }
            
            // Check if should be included
            if (!shouldIncludeFile($relativePath, $config['include_patterns'])) {
                continue;
            }
            
            $targetPath = $targetDir . DIRECTORY_SEPARATOR . $relativePath;
            $targetDirPath = dirname($targetPath);
            
            if (!is_dir($targetDirPath)) {
                mkdir($targetDirPath, 0755, true);
            }
            
            if ($file->isFile()) {
                copy($sourcePath, $targetPath);
                $fileCount++;
                $totalSize += filesize($sourcePath);
            }
        }
    }
    
    // Add files
    addFilesToDirectory(BASE_PATH, $tempDir, BASE_PATH, $config, $fileCount, $totalSize);
    
    // Verify config.php is NOT in the package (safety check)
    $configPhpPath = $tempDir . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';
    if (file_exists($configPhpPath)) {
        error_log("Version Control: ❌ ERROR - config.php found in package! Removing it...");
        unlink($configPhpPath);
        error_log("Version Control: ✅ Removed config.php from package");
    } else {
        error_log("Version Control: ✅ Verified: config.php is NOT in package (correctly excluded)");
    }
    
    // Create migrations directory structure
    $migrationsDir = $tempDir . '/migrations';
    if (!mkdir($migrationsDir, 0755, true)) {
        throw new Exception('Failed to create migrations directory');
    }
    
    // Automatically detect and include migration files from database/ directory
    $migrations = [];
    $databaseDir = BASE_PATH . '/database';
    if (is_dir($databaseDir)) {
        // Look for migration files matching multiple patterns:
        // Pattern 1: *_migration.sql (e.g., update-old-site_migration.sql)
        // Pattern 2: migration-*.sql (e.g., migration-update-old-site.sql)
        // Pattern 3: *migration*.sql (any file with "migration" in the name)
        $migrationFiles1 = glob($databaseDir . '/*_migration.sql');
        $migrationFiles2 = glob($databaseDir . '/migration-*.sql');
        $migrationFiles3 = glob($databaseDir . '/*migration*.sql');
        
        // Merge all patterns and remove duplicates
        $migrationFiles = array_unique(array_merge($migrationFiles1, $migrationFiles2, $migrationFiles3));
        
        // Filter out full database dumps (they're large SQL files, not migrations)
        $migrationFiles = array_filter($migrationFiles, function($file) {
            $filename = basename($file);
            // Exclude full database dumps (usually named like database_name.sql or u502532383_online.sql)
            // Migrations should have "migration" in the name
            return stripos($filename, 'migration') !== false && filesize($file) < 10 * 1024 * 1024; // Less than 10MB
        });
        
        if (!empty($migrationFiles)) {
            foreach ($migrationFiles as $migrationFile) {
                $migrationFileName = basename($migrationFile);
                
                // Copy migration file to migrations directory in package
                $targetMigrationPath = $migrationsDir . '/' . $migrationFileName;
                if (copy($migrationFile, $targetMigrationPath)) {
                    // Extract migration name from filename
                    // Handle different naming patterns:
                    // - update-old-site_migration.sql -> "Update Old Site"
                    // - migration-update-old-site.sql -> "Update Old Site"
                    $migrationName = $migrationFileName;
                    $migrationName = str_replace('_migration.sql', '', $migrationName); // Remove suffix
                    $migrationName = str_replace('migration-', '', $migrationName); // Remove prefix
                    $migrationName = str_replace('.sql', '', $migrationName); // Remove extension
                    $migrationName = str_replace('_', ' ', $migrationName); // Convert underscores to spaces
                    $migrationName = str_replace('-', ' ', $migrationName); // Convert hyphens to spaces
                    $migrationName = ucwords($migrationName); // Capitalize words
                    
                    $migrationSize = filesize($migrationFile);
                    $migrationTotalSize += $migrationSize;
                    
                    // Add to migrations array for manifest
                    $migrations[] = [
                        'name' => $migrationName,
                        'file' => $migrationFileName,
                        'size' => $migrationSize
                    ];
                    
                    error_log("Version Control: ✅ Found and included migration file: {$migrationFileName} ({$migrationSize} bytes)");
                } else {
                    error_log("Version Control: ⚠️ Failed to copy migration file: {$migrationFileName}");
                }
            }
        } else {
            error_log("Version Control: ℹ️ No migration files found in database/ directory (pattern: *_migration.sql)");
        }
    } else {
        error_log("Version Control: ⚠️ Database directory not found: {$databaseDir}");
    }
    
    // Create manifest file
    $manifest = [
        'version' => $version,
        'release_date' => date('Y-m-d H:i:s'),
        'notes' => $notes,
        'file_count' => $fileCount,
        'package_size' => $totalSize + $migrationTotalSize, // Include migration files in total size
        'created_by' => $userId,
        'migrations' => $migrations, // Automatically detected migration files
        'excluded_files' => [
            'config/config.php', // CRITICAL: Fully excluded - each site keeps its own DB/SMTP configuration
            'config/database.php', // Fully excluded - database connection stays protected
            'includes/translation.php', // Translation widget settings
            'includes/currency-converter.php', // Currency API keys
            // Live chat embed is managed via system_settings.live_chat_script
            '.env', // Environment variables
            '.htaccess', // Server configuration
            'uploads/', // User-generated content
            'database/.*\.sql$/', // Database dumps (excluded, but migration files are handled separately)
            'logs/', // Log files
        ],
        'note' => 'config.php is fully excluded from packages. Each site must maintain its own database and SMTP configuration. The config.php file is never overwritten during updates to prevent connection errors.'
    ];
    
    file_put_contents($migrationsDir . '/manifest.json', json_encode($manifest, JSON_PRETTY_PRINT));
    
    // Create ZIP file
    $zipFile = $tempDir . '/update-v' . $version . '-' . date('Y-m-d') . '.zip';
    $zip = new ZipArchive();
    
    if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
        throw new Exception('Failed to create ZIP file');
    }
    
    // Add all files to ZIP
    $zipIterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($tempDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::LEAVES_ONLY
    );
    
    foreach ($zipIterator as $file) {
        if ($file->isFile()) {
            $filePath = $file->getRealPath();
            $relativePath = str_replace($tempDir . DIRECTORY_SEPARATOR, '', $filePath);
            $relativePath = str_replace('\\', '/', $relativePath);
            $zip->addFile($filePath, $relativePath);
        }
    }
    
    $zip->close();
    
    // Move ZIP to a permanent location (or return for download)
    $packageDir = BASE_PATH . '/packages';
    if (!is_dir($packageDir)) {
        mkdir($packageDir, 0755, true);
    }
    
    $finalZipPath = $packageDir . '/update-v' . $version . '-' . date('Y-m-d') . '.zip';
    
    // If file already exists, add timestamp to make it unique
    if (file_exists($finalZipPath)) {
        $finalZipPath = $packageDir . '/update-v' . $version . '-' . date('Y-m-d-His') . '.zip';
    }
    
    if (!rename($zipFile, $finalZipPath)) {
        throw new Exception('Failed to move ZIP file to packages directory');
    }
    
    $packageSize = filesize($finalZipPath);
    if ($packageSize === false) {
        $packageSize = 0;
    }
    
    // Save version to database
    $sql = "INSERT INTO system_versions (version, release_date, notes, created_by, package_size, file_count) 
            VALUES (?, NOW(), ?, ?, ?, ?)";
    $result = $db->query($sql, [$version, $notes, $userId, $packageSize, $fileCount]);
    
    if (!$result) {
        error_log('Failed to save version to database: ' . print_r($db->errorInfo(), true));
    }
    
    // Clean up temp directory
    function deleteDirectory($dir) {
        if (!is_dir($dir)) return;
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }
    deleteDirectory($tempDir);
    
    $message = 'Update package created successfully';
    if (!empty($migrations)) {
        $message .= '. Included ' . count($migrations) . ' migration file(s) for database structure updates.';
    } else {
        $message .= '. No migration files found (database structure will not be updated).';
    }
    
    echo json_encode([
        'success' => true,
        'message' => $message,
        'version' => $version,
        'file_count' => $fileCount,
        'package_size' => filesize($finalZipPath), // Final ZIP size (includes migrations)
        'migrations_count' => count($migrations),
        'download_url' => SITE_URL . '/packages/update-v' . $version . '-' . date('Y-m-d') . '.zip'
    ]);
    
} catch (Exception $e) {
    error_log('Create update package error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to create update package: ' . $e->getMessage()
    ]);
}

