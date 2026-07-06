<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

echo "Starting profile picture cleanup...\n";

$db = Database::getInstance();

try {
    // Get all users with profile pictures
    $sql = "SELECT id, profile_picture FROM users WHERE profile_picture IS NOT NULL AND profile_picture != ''";
    $stmt = $db->query($sql);
    $users = $stmt->fetchAll();
    
    $cleaned = 0;
    $errors = [];
    
    echo "Found " . count($users) . " users with profile pictures\n";
    
    foreach ($users as $user) {
        $profilePath = $user['profile_picture'];
        $fullPath = BASE_PATH . $profilePath;
        
        echo "Checking user {$user['id']}: {$profilePath}\n";
        
        // Check if file exists
        if (!file_exists($fullPath)) {
            echo "  -> File not found, cleaning...\n";
            
            // Update user to remove invalid profile picture path
            $updateSql = "UPDATE users SET profile_picture = NULL WHERE id = ?";
            $updateStmt = $db->query($updateSql, [$user['id']]);
            
            if ($updateStmt) {
                $cleaned++;
                echo "  -> Cleaned successfully\n";
            } else {
                $errors[] = "Failed to clean user {$user['id']}";
                echo "  -> Failed to clean\n";
            }
        } else {
            echo "  -> File exists, keeping\n";
        }
    }
    
    echo "\nCleanup completed!\n";
    echo "Cleaned: {$cleaned} invalid profile pictures\n";
    echo "Errors: " . count($errors) . "\n";
    
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "  - {$error}\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
