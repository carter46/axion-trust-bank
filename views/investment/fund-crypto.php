<?php 
$pageTitle = 'Crypto Payment - ' . getSiteName();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/security.php';

requireLogin();

// Get funding and wallet from controller
if (!isset($funding) || !isset($wallet)) {
    $_SESSION['error'] = 'Invalid funding request';
    redirect('/investment');
    exit;
}

// Get user currency
$userModel = new User();
$user = $userModel->findById($_SESSION['user_id']);
$userCurrency = getUserDisplayCurrency($user);

// Include head and sidebar
include __DIR__ . '/../../includes/head.php';
include __DIR__ . '/../../includes/sidebar.php';

if (!$funding || !$wallet) {
    redirect('/investment');
    exit;
}

include __DIR__ . '/../../includes/restricted-banner.php';

// Generate QR code data URL (using a simple library or API)
// For now, we'll use a QR code service or library
$qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($wallet['wallet_address']);

$cryptoLabels = [
    'btc' => 'Bitcoin',
    'eth' => 'Ethereum',
    'usdt' => 'USDT',
    'ltc' => 'Litecoin'
];

$cryptoLabel = $cryptoLabels[$wallet['crypto_type']] ?? strtoupper($wallet['crypto_type']);
?>

<style>
.page-header {
    margin-bottom: 30px;
}

.page-header h1 {
    font-size: 32px;
    font-weight: 700;
    color: #032B44;
    margin-bottom: 8px;
}

.crypto-payment-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 24px;
    padding: 40px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    max-width: 600px;
    margin: 0 auto;
}

.payment-info-box {
    background: #f0fdf4;
    border: 2px solid #86efac;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 24px;
}

.payment-info-box h3 {
    color: #065f46;
    margin-bottom: 16px;
    font-size: 18px;
}

.amount-display {
    font-size: 32px;
    font-weight: 700;
    color: #032B44;
    text-align: center;
    margin: 20px 0;
}

.wallet-address-box {
    background: #f9fafb;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 20px;
    position: relative;
}

.wallet-address {
    font-family: monospace;
    font-size: 14px;
    word-break: break-all;
    color: #374151;
    margin-bottom: 12px;
}

.copy-btn {
    padding: 8px 16px;
    background: #032B44;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s;
}

.copy-btn:hover {
    background: #024a6b;
}

.copy-btn.copied {
    background: #10b981;
}

.qr-code-container {
    text-align: center;
    margin: 30px 0;
    padding: 20px;
    background: white;
    border-radius: 12px;
}

.qr-code-container img {
    max-width: 300px;
    border: 4px solid #e5e7eb;
    border-radius: 12px;
}

.warning-box {
    background: #fef3c7;
    border: 2px solid #fbbf24;
    border-radius: 12px;
    padding: 16px;
    margin-top: 24px;
}

.warning-box i {
    color: #f59e0b;
    margin-right: 8px;
}

.steps-list {
    list-style: none;
    padding: 0;
    margin: 20px 0;
}

.steps-list li {
    padding: 12px 0;
    padding-left: 32px;
    position: relative;
    color: #374151;
}

.steps-list li:before {
    content: counter(step-counter);
    counter-increment: step-counter;
    position: absolute;
    left: 0;
    width: 24px;
    height: 24px;
    background: #032B44;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 12px;
}

.steps-list {
    counter-reset: step-counter;
}

.btn-back {
    display: inline-block;
    padding: 12px 24px;
    background: #e5e7eb;
    color: #374151;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    margin-top: 24px;
    transition: all 0.3s;
}

.btn-back:hover {
    background: #d1d5db;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #374151;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 16px;
    font-family: inherit;
}

.form-control:focus {
    outline: none;
    border-color: #032B44;
    box-shadow: 0 0 0 3px rgba(3, 43, 68, 0.1);
}

.btn-invest {
    background: linear-gradient(135deg, #032B44 0%, #024a6b 100%);
    color: white;
    padding: 14px 24px;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
}

.btn-invest:hover {
    background: linear-gradient(135deg, #024a6b 0%, #032B44 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(3, 43, 68, 0.3);
}

.btn-invest:disabled {
    background: #9ca3af;
    cursor: not-allowed;
    transform: none;
    opacity: 0.6;
}
</style>

<?php 
// Check if transaction hash has been submitted (do this first for header)
$hashSubmitted = !empty($funding['crypto_tx_hash']);
$showSuccess = isset($_SESSION['success']);
$showError = isset($_SESSION['error']);

// Display success/error messages first
if ($showSuccess) {
    $successMsg = $_SESSION['success'];
    unset($_SESSION['success']);
}
if ($showError) {
    $errorMsg = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>

<div class="page-header">
    <h1>Pay with <?php echo $cryptoLabel; ?></h1>
    <?php if (!$hashSubmitted): ?>
    <p style="color: #666;">Send <?php echo formatInvestmentAmountForUser($funding['amount'], $user); ?> worth of <?php echo $cryptoLabel; ?> to the address below</p>
    <?php else: ?>
    <p style="color: #666;">Transaction hash submitted - pending admin verification</p>
    <?php endif; ?>
</div>

<div class="crypto-payment-card">
    <?php 
    // If hash already submitted, hide ALL payment details and show success state only
    if ($hashSubmitted):
    ?>
    <!-- Success State - Hash Submitted -->
    <div style="background: #d1fae5; border: 2px solid #10b981; color: #065f46; padding: 24px; border-radius: 12px; margin-top: 24px; text-align: center;">
        <i class="fas fa-check-circle" style="font-size: 48px; margin-bottom: 16px;"></i>
        <h3 style="margin: 0 0 12px 0; color: #065f46;">Transaction Hash Submitted</h3>
        <p style="margin: 0; font-size: 16px; line-height: 1.6;">
            Transaction hash submitted successfully. Your payment is now pending admin verification. You can track it in your investment transaction history.
        </p>
        <?php if (!empty($funding['crypto_tx_hash'])): ?>
        <div style="margin-top: 16px; padding: 12px; background: rgba(16, 185, 129, 0.1); border-radius: 8px;">
            <strong>Transaction Hash:</strong><br>
            <code style="font-family: monospace; font-size: 12px; word-break: break-all;"><?php echo htmlspecialchars($funding['crypto_tx_hash']); ?></code>
        </div>
        <?php endif; ?>
        <div style="margin-top: 20px;">
            <a href="<?php echo SITE_URL; ?>/investment/transactions" class="btn-invest" style="width: auto; display: inline-flex;">
                <i class="fas fa-history"></i> View Transaction History
            </a>
        </div>
    </div>
    <?php else: ?>
    <!-- Normal State - Payment Instructions -->
    <div class="payment-info-box">
        <h3><i class="fas fa-info-circle"></i> Payment Instructions</h3>
        <ol class="steps-list">
            <li>Copy the wallet address below</li>
            <li>Open your crypto wallet app</li>
            <li>Send exactly <?php echo formatInvestmentAmountForUser($funding['amount'], $user); ?> worth of <?php echo $cryptoLabel; ?></li>
            <li>Paste the transaction hash after sending</li>
            <li>Wait for admin confirmation (usually within 24 hours)</li>
        </ol>
    </div>
    
    <?php if ($showSuccess): ?>
    <div style="background: #d1fae5; border: 2px solid #10b981; color: #065f46; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($successMsg); ?>
    </div>
    <?php endif; ?>
    
    <?php if ($showError): ?>
    <div style="background: #fee2e2; border: 2px solid #ef4444; color: #991b1b; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($errorMsg); ?>
    </div>
    <?php endif; ?>
    
    <div class="amount-display">
        <?php echo formatInvestmentAmountForUser($funding['amount'], $user); ?>
    </div>
    
    <?php if ($wallet['network']): ?>
    <div style="text-align: center; color: #6b7280; margin-bottom: 16px;">
        <i class="fas fa-network-wired"></i> Network: <?php echo htmlspecialchars($wallet['network']); ?>
    </div>
    <?php endif; ?>
    
    <div class="wallet-address-box">
        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151;">
            Wallet Address
        </label>
        <div class="wallet-address" id="walletAddress"><?php echo htmlspecialchars($wallet['wallet_address']); ?></div>
        <button class="copy-btn" onclick="copyAddress()">
            <i class="fas fa-copy"></i> Copy Address
        </button>
    </div>
    
    <div class="qr-code-container">
        <p style="font-weight: 600; margin-bottom: 12px; color: #374151;">Scan QR Code</p>
        <img src="<?php echo htmlspecialchars($qrCodeUrl); ?>" alt="QR Code">
    </div>
    
    <div class="warning-box">
        <i class="fas fa-exclamation-triangle"></i>
        <strong>Important:</strong> Only send <?php echo $cryptoLabel; ?> to this address. Sending other cryptocurrencies may result in permanent loss of funds.
    </div>
    
    <form method="POST" action="<?php echo SITE_URL; ?>/investment/fund-crypto/<?php echo $funding['id']; ?>" style="margin-top: 24px;">
        <div class="form-group">
            <label class="form-label">Transaction Hash (Required - submit after payment)</label>
            <input type="text" name="tx_hash" class="form-control" placeholder="Enter transaction hash after sending payment" value="<?php echo htmlspecialchars($_POST['tx_hash'] ?? ''); ?>" required>
            <small style="color: #6b7280; display: block; margin-top: 8px;">After sending crypto payment, paste the transaction hash here to track your payment.</small>
            <input type="hidden" name="funding_id" value="<?php echo $funding['id']; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
        </div>
        <button type="submit" class="btn-invest">
            <i class="fas fa-check"></i> Submit Transaction Hash
        </button>
    </form>
    <?php endif; ?>
    
    <a href="<?php echo SITE_URL; ?>/investment" class="btn-back">
        <i class="fas fa-arrow-left"></i> Back to Investments
    </a>
</div>

<script>
function copyAddress() {
    const address = document.getElementById('walletAddress').textContent;
    navigator.clipboard.writeText(address).then(function() {
        const btn = event.target.closest('.copy-btn');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
        btn.classList.add('copied');
        setTimeout(function() {
            btn.innerHTML = originalText;
            btn.classList.remove('copied');
        }, 2000);
    });
}
</script>

<?php include __DIR__ . '/../../includes/mobile-nav.php'; ?>

