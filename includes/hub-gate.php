<?php
/**
 * 7th Trade Hub shutdown bootstrap (tracked in git — always deployed).
 *
 * Root cause of “Admin shows ACTIVE / public site stays open” on some domains:
 * - config/config.php is gitignored (per-domain DB/SMTP secrets).
 * - New domains often copy an older config that never require()s seventh-tradehub.php.
 * - Admin Settings loads Hub explicitly, so Connection logs / banner work.
 * - Public pages only went through config → Hub never loaded → no gate.
 *
 * Loaded from includes/functions.php (tracked). Every page/API that boots the app
 * loads functions.php via config, so the gate cannot be skipped by a bad domain config.
 */
if (!function_exists('seventhTradeHubMaybeEnforceShutdown')) {
    $hubFile = __DIR__ . DIRECTORY_SEPARATOR . 'seventh-tradehub.php';
    if (!is_file($hubFile)) {
        return;
    }
    require_once $hubFile;
}

if (!function_exists('seventhTradeHubMaybeEnforceShutdown')) {
    return;
}

if (function_exists('seventhTradeHubIsCliRequest') && seventhTradeHubIsCliRequest()) {
    return;
}

seventhTradeHubMaybeEnforceShutdown(true);
