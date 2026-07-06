<?php
/**
 * Migration Helper Tool
 * Helps generate migration files by comparing current database schema
 * with expected schema from code (models, controllers, etc.)
 * 
 * NOTE: This generates SUGGESTIONS only - you must review and create the file manually
 */

require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json');

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $db = Database::getInstance();
    
    // Get all tables from current database
    $tables = [];
    $result = $db->query("SHOW TABLES");
    while ($row = $result->fetch(PDO::FETCH_NUM)) {
        $tableName = $row[0];
        $tables[$tableName] = [];
        
        // Get columns for each table
        $columnsResult = $db->query("SHOW COLUMNS FROM `{$tableName}`");
        while ($col = $columnsResult->fetch(PDO::FETCH_ASSOC)) {
            $tables[$tableName][$col['Field']] = [
                'type' => $col['Type'],
                'null' => $col['Null'],
                'key' => $col['Key'],
                'default' => $col['Default'],
                'extra' => $col['Extra']
            ];
        }
    }
    
    // This is a simplified example - in reality, you'd need to:
    // 1. Parse PHP files to find model definitions
    // 2. Compare expected schema vs actual schema
    // 3. Generate migration SQL
    
    echo json_encode([
        'success' => true,
        'message' => 'Schema analysis complete',
        'tables' => array_keys($tables),
        'table_count' => count($tables),
        'note' => 'This is a helper tool. Migration files must still be created manually in database/ directory with naming pattern: *_migration.sql or migration-*.sql'
    ]);
    
} catch (Exception $e) {
    error_log('Migration helper error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to analyze schema: ' . $e->getMessage()
    ]);
}

