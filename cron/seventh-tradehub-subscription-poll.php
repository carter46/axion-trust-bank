<?php
/**
 * Poll Hub for owned-tool subscription (run every 5–15 minutes via cron).
 *
 * Example cron:
 *   Every 10 minutes: php /path/to/project/cron/seventh-tradehub-subscription-poll.php
 */
set_time_limit(120);

require_once __DIR__ . '/../config/config.php';

$logFile = __DIR__ . '/../logs/seventh-tradehub-poll.log';
$logDir = dirname($logFile);
if (!is_dir($logDir)) {
    @mkdir($logDir, 0755, true);
}

function hubPollLog(string $message): void
{
    global $logFile;
    $line = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
    @file_put_contents($logFile, $line, FILE_APPEND);
    echo $line;
}

try {
    hubPollLog('=== 7th Trade Hub subscription poll started ===');

    $owned = seventhTradeHubGetByContext(SEVENTH_TRADEHUB_CONTEXT_OWNED);
    if (!$owned || !seventhTradeHubIsIntegrationOperational($owned)) {
        hubPollLog('Owned integration not configured or disabled — skipping');
        exit(0);
    }

    $result = seventhTradeHubPollSubscription($owned);
    if ($result === null) {
        hubPollLog('Poll failed or returned no data');
        exit(1);
    }

    $status = (string)($result['status'] ?? 'unknown');
    hubPollLog('Poll OK — status: ' . $status);

    if (seventhTradeHubIsOwnedSiteShutdown()) {
        hubPollLog('Owned subscription expired — site shutdown active');
    }

    hubPollLog('=== Poll complete ===');
} catch (Throwable $e) {
    hubPollLog('ERROR: ' . $e->getMessage());
    exit(1);
}
