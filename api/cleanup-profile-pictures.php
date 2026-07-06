<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$db = Database::getInstance();

try {
    // Get all users with profile pictures
    $sql = "SELECT id, profile_picture FROM users WHERE profile_picture IS NOT NULL AND profile_picture != ''";
    $stmt = $db->query($sql);
    $users = $stmt->fetchAll();
    
    $cleaned = 0;
    $errors = [];
    
    foreach ($users as $user) {
        $profilePath = $user['profile_picture'];
        $fullPath = BASE_PATH . $profilePath;
        
        // Check if file exists
        if (!file_exists($fullPath)) {
            // Update user to remove invalid profile picture path
            $updateSql = "UPDATE users SET profile_picture = NULL WHERE id = ?";
            $updateStmt = $db->query($updateSql, [$user['id']]);
            
            if ($updateStmt) {
                $cleaned++;
                error_log("Cleaned invalid profile picture for user {$user['id']}: {$profilePath}");
            } else {
                $errors[] = "Failed to clean user {$user['id']}";
            }
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => "Cleaned {$cleaned} invalid profile pictures",
        'cleaned' => $cleaned,
        'errors' => $errors
    ]);
    
} catch (Exception $e) {
    error_log("Profile picture cleanup error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Cleanup failed: ' . $e->getMessage()
    ]);
}
?>
