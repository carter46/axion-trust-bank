<?php 
$pageTitle = 'Settings - ' . getSiteName();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/currency.php';

requireLogin();

// Fetch user data
$db = Database::getInstance();
$userId = $_SESSION['user_id'];

$stmt = $db->query("SELECT * FROM users WHERE id = ?", [$userId]);
$user = $stmt->fetch();

// SECURITY: Verify user exists (should be caught by requireLogin, but double-check)
if (!$user) {
    // User account was deleted during session - destroy session and redirect to login
    session_destroy();
    $_SESSION = [];
    session_start();
    $_SESSION['error'] = 'Your account is no longer active.';
    header('Location: ' . SITE_URL . '/auth/login');
    exit;
}

// Get user preferences (with defaults)
$languagePreference = $user['language'] ?? 'en';
$defaultCurrency = $user['currency'] ?? DEFAULT_CURRENCY;

// Get supported currencies
$currencyHelper = new Currency();
$supportedCurrencies = $currencyHelper->getSupportedCurrencies();

// Get timezone and theme from metadata JSON
$metadata = json_decode($user['metadata'] ?? '{}', true);
$timezone = $metadata['timezone'] ?? 'America/New_York';
$themePreference = $metadata['theme_preference'] ?? 'light';

// Include head
include __DIR__ . '/../../includes/head.php';
include __DIR__ . '/../../includes/sidebar.php';
?>

<style>
        * {
            box-sizing: border-box;
        }

        /* Override parent content-area styles */
        .main-content-area .content-area {
            background: #f5f7fa !important;
            padding: 15px !important;
        }

        .octobank-settings {
            max-width: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 25px;
            width: 100%;
            box-sizing: border-box;
        }
        
        @media (max-width: 768px) {
            .octobank-settings {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            
            .settings-card {
                width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box;
                overflow-x: hidden;
            }
        }

        /* ===== PAGE HEADER STANDARD (Same as Dashboard) ===== */
        .header {
            margin-top: 0;
            margin-bottom: 0;
            padding: 0;
        }

        .header h1 {
            font-size: 28px;
            color: #2d3748;
            padding-top: 20px;
            margin: 0 0 8px 0;
            font-weight: 600;
            text-align: left;
        }
        
        .header p {
            font-size: 15px;
            color: #6c757d;
            margin: 0;
            padding-bottom: 20px;
            text-align: left;
        }

        /* Header with button */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
            gap: 20px;
        }
        
        .header-left {
            flex: 1;
            min-width: 0;
        }

        /* Settings Cards */
        .settings-card {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }

        .settings-card h3 {
            font-size: 20px;
            font-weight: 600;
            color: #2d3748;
            margin: 0 0 20px 0;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .settings-card h3 i {
            color: #1e3a8a;
            font-size: 22px;
        }

        .settings-card p {
            color: #6c757d;
            font-size: 14px;
            margin: 0 0 20px 0;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            font-size: 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            transition: all 0.3s;
            box-sizing: border-box;
            background: white;
            color: #1f2937;
        }

        .form-control:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-control select {
            cursor: pointer;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            font-size: 16px;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: #f3f4f6;
            color: #374151;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
            margin-bottom: 20px;
        }

        .back-button:hover {
            background: #e5e7eb;
            transform: translateX(-4px);
        }

        @media (max-width: 768px) {
            .settings-card {
                padding: 20px;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
</style>

<a href="<?php echo SITE_URL; ?>/profile" class="back-button">
    <i class="fas fa-arrow-left"></i> Back to Profile
</a>

<div class="octobank-settings">
    <!-- Header -->
    <div class="page-header">
        <div class="header-left">
            <div class="header">
                <h1>Settings</h1>
                <p>Manage your account preferences and settings</p>
            </div>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo SITE_URL; ?>/profile/settings">
        <!-- Language & Region Settings -->
        <div class="settings-card" id="language">
            <h3><i class="fas fa-globe"></i> Language & Region</h3>
            <p>Choose your preferred language and regional settings</p>

            <div class="form-group">
                <label>Website Translator</label>
                <div class="gtranslate_wrapper" id="settingsGTranslateMount" aria-label="Language selector" style="position: static !important; display:inline-block !important; bottom:auto !important; left:auto !important; right:auto !important; top:auto !important; transform:none !important; margin-top: 12px;"></div>
            </div>

            <div class="form-group">
                <label for="timezone">Timezone</label>
                <select name="timezone" id="timezone" class="form-control">
                    <option value="America/New_York" <?php echo $timezone === 'America/New_York' ? 'selected' : ''; ?>>Eastern Time (ET)</option>
                    <option value="America/Chicago" <?php echo $timezone === 'America/Chicago' ? 'selected' : ''; ?>>Central Time (CT)</option>
                    <option value="America/Denver" <?php echo $timezone === 'America/Denver' ? 'selected' : ''; ?>>Mountain Time (MT)</option>
                    <option value="America/Los_Angeles" <?php echo $timezone === 'America/Los_Angeles' ? 'selected' : ''; ?>>Pacific Time (PT)</option>
                    <option value="Europe/London" <?php echo $timezone === 'Europe/London' ? 'selected' : ''; ?>>London (GMT)</option>
                    <option value="Europe/Madrid" <?php echo $timezone === 'Europe/Madrid' ? 'selected' : ''; ?>>Madrid (CET)</option>
                    <option value="Europe/Rome" <?php echo $timezone === 'Europe/Rome' ? 'selected' : ''; ?>>Rome (CET)</option>
                    <option value="Africa/Lagos" <?php echo $timezone === 'Africa/Lagos' ? 'selected' : ''; ?>>Lagos (WAT)</option>
                </select>
            </div>
        </div>

        <!-- Currency Settings -->
        <div class="settings-card">
            <h3><i class="fas fa-dollar-sign"></i> Currency Preferences</h3>
            <p>Set your default currency for account balances and transactions</p>
            
            <div class="form-group">
                <label for="currency">Default Currency</label>
                <select name="currency" id="currency" class="form-control">
                    <?php foreach ($supportedCurrencies as $code => $name): ?>
                        <option value="<?php echo $code; ?>" <?php echo $defaultCurrency === $code ? 'selected' : ''; ?>>
                            <?php echo $name; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Data Export -->
        <div class="settings-card">
            <h3><i class="fas fa-download"></i> Export Data</h3>
            <p>Download your account information and transaction history</p>
            
            <div style="margin-top: 20px;">
                <a href="<?php echo SITE_URL; ?>/transaction" class="btn btn-secondary" style="width: auto; display: inline-block;">
                    <i class="fas fa-list"></i> View Transaction History
                </a>
                <?php if (file_exists(__DIR__ . '/../../api/download-statement.php')): ?>
                <a href="<?php echo SITE_URL; ?>/api/download-statement.php" class="btn btn-secondary" style="width: auto; display: inline-block; margin-left: 10px;">
                    <i class="fas fa-file-pdf"></i> Download Statement (PDF)
                </a>
                <?php endif; ?>
            </div>
            
            <p style="margin-top: 15px; font-size: 13px; color: #6c757d;">
                <i class="fas fa-info-circle"></i> Your transaction history can be viewed and exported from the Transactions page.
            </p>
        </div>

        <!-- Form Actions -->
        <div class="settings-card">
            <div class="form-actions">
                <a href="<?php echo SITE_URL; ?>/profile" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Settings
                </button>
            </div>
        </div>
    </form>
</div>


<?php
// Include mobile navigation and closing tags
include __DIR__ . '/../../includes/mobile-nav.php';
?>

