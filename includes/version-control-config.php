<?php
/**
 * Version Control Configuration
 * Defines which files to include/exclude when creating update packages
 */

return [
    // Files and directories to EXCLUDE from update packages
    'exclude_patterns' => [
        // Configuration files (site-specific) - NEVER overwrite these during restore
        '/^config\/config\.php$/', // CRITICAL: Excluded - each site has its own DB/SMTP config
        '/^config\/database\.php$/', // Database connection stays protected
        '/^\.env$/',
        '/^\.htaccess$/', // May be site-specific
        
        // User-generated content
        '/^uploads\//',
        
        // Third-party integrations and site-specific includes
        '/^includes\/livechat\.php$/', // Live chat API keys (Smartsupp, Tawk.to, etc.)
        '/^includes\/translation\.php$/', // Translation widget settings (GTranslate, etc.)
        '/^includes\/currency-converter\.php$/', // Currency API keys (Open Exchange Rates, etc.)
        // Add any other site-specific files here:
        // '/^includes\/custom-integration\.php$/',
        
        // Database files
        '/^database\/.*\.sql$/',
        
        // Logs
        '/^logs\//',
        '/.*\.log$/',
        
        // Version control
        '/^\.git\//',
        '/^\.gitignore$/',
        '/^\.gitattributes$/',
        
        // Node modules and dependencies
        '/^node_modules\//',
        '/^vendor\//',
        '/^composer\.(json|lock)$/',
        '/^package(-lock)?\.json$/',
        
        // IDE and editor files
        '/^\.vscode\//',
        '/^\.idea\//',
        '/^\.DS_Store$/',
        '/^Thumbs\.db$/',
        
        // Temporary files
        '/^tmp\//',
        '/^temp\//',
        '/.*\.tmp$/',
        '/.*\.bak$/',
        
        // Version control package files themselves
        '/.*update.*\.zip$/',
        '/.*backup.*\.zip$/',
    ],
    
    // Files and directories to INCLUDE in update packages
    'include_patterns' => [
        '/^controllers\//',
        '/^models\//',
        '/^views\//',
        '/^api\//',
        '/^includes\//', // except livechat.php (handled by exclude)
        '/^assets\//',
        '/^cron\//',
        '/^index\.php$/',
        '/^.*\.php$/', // Root PHP files
        '/^robots\.txt$/',
        '/^favicon\.(ico|svg)$/',
    ],
    
    // Directories to always include (even if empty)
    'include_directories' => [
        'controllers',
        'models',
        'views',
        'api',
        'includes',
        'assets',
        'cron',
    ],
    
    // Migration file patterns
    'migration_patterns' => [
        '/^database\/.*_migration\.sql$/',
        '/^migrations\/.*\.sql$/',
    ],
];

