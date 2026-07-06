<?php
/**
 * Investment ROI Accrual Cron Job
 * 
 * This script should be run daily (via cron) to accrue ROI for active investments.
 * 
 * Cron setup example:
 * 0 0 * * * /usr/bin/php /path/to/your/project/cron/investment-accrual.php
 * 
 * Or run manually: php cron/investment-accrual.php
 */

// Set script to run for a long time if needed
set_time_limit(300);

// Load dependencies
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../models/UserInvestment.php';
require_once __DIR__ . '/../models/InvestmentProduct.php';

// Autoload models
spl_autoload_register(function ($class_name) {
    $paths = ['models/', 'controllers/'];
    foreach ($paths as $path) {
        $file = __DIR__ . '/../' . $path . $class_name . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Log start
$logFile = __DIR__ . '/../logs/investment-accrual.log';
$logDir = dirname($logFile);
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

function writeLog($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[{$timestamp}] {$message}\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
    echo $logMessage;
}

try {
    writeLog("=== Investment Accrual Job Started ===");
    
    // Initialize database connection
    $db = Database::getInstance();
    
    $date = date('Y-m-d');
    $userInvestmentModel = new UserInvestment();
    
    // Get active investments that need accrual
    $activeInvestments = $userInvestmentModel->getActiveInvestmentsForAccrual($date);
    writeLog("Found " . count($activeInvestments) . " active investments requiring accrual");
    
    $processed = 0;
    $errors = 0;
    
    foreach ($activeInvestments as $investment) {
        try {
            if ($userInvestmentModel->accrueROI($investment['id'], $date)) {
                $processed++;
                writeLog("✓ Processed investment ID {$investment['id']} for user {$investment['user_id']}");
            } else {
                $errors++;
                writeLog("✗ Failed to process investment ID {$investment['id']}");
            }
        } catch (Exception $e) {
            $errors++;
            writeLog("✗ Error processing investment ID {$investment['id']}: " . $e->getMessage());
        }
    }
    
    // Process matured investments
    $maturedInvestments = $userInvestmentModel->getMaturedInvestments($date);
    writeLog("Found " . count($maturedInvestments) . " matured investments");
    
    $maturedCount = 0;
    foreach ($maturedInvestments as $investment) {
        try {
            if ($userInvestmentModel->processMaturity($investment['id'])) {
                $maturedCount++;
                writeLog("✓ Processed maturity for investment ID {$investment['id']}");
            }
        } catch (Exception $e) {
            writeLog("✗ Error processing maturity for investment ID {$investment['id']}: " . $e->getMessage());
        }
    }
    
    writeLog("=== Job Complete ===");
    writeLog("Processed: {$processed} | Errors: {$errors} | Matured: {$maturedCount}");
    writeLog("");
    
} catch (Exception $e) {
    writeLog("FATAL ERROR: " . $e->getMessage());
    writeLog("Stack trace: " . $e->getTraceAsString());
    exit(1);
}

exit(0);

