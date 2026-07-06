<?php 
$pageTitle = ($product['title'] ?? 'Investment') . ' - ' . getSiteName();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../models/InvestmentProduct.php';
require_once __DIR__ . '/../../models/UserInvestment.php';
require_once __DIR__ . '/../../models/User.php';

requireLogin();

// Get user currency
$userModel = new User();
$user = $userModel->findById($_SESSION['user_id']);
$userCurrency = getUserDisplayCurrency($user);

// Include head and sidebar
include __DIR__ . '/../../includes/head.php';
include __DIR__ . '/../../includes/sidebar.php';

include __DIR__ . '/../../includes/restricted-banner.php';

if (!$product) {
    redirect('/investment');
    exit;
}

// Calculate projected returns for preview
function calculateProjectedReturns($principal, $durationDays, $dailyROI, $payoutType, $compound = false) {
    $totalDays = $durationDays;
    $currentPrincipal = $principal;
    $totalAccrued = 0;
    
    for ($day = 1; $day <= $totalDays; $day++) {
        $dailyAmount = $currentPrincipal * ($dailyROI / 100);
        $totalAccrued += $dailyAmount;
        
        if ($compound) {
            $currentPrincipal += $dailyAmount;
        }
    }
    
    return [
        'total_accrued' => $totalAccrued,
        'final_amount' => $principal + $totalAccrued,
        'projected_profit' => $totalAccrued
    ];
}

$roiConfigData = $roiConfig ?? [];
$compound = ($roiConfigData['compound'] ?? false) || ($product['payout_type'] === 'compound_daily');
$projectedReturns = calculateProjectedReturns(
    $product['min_amount'], 
    $product['min_duration_days'], 
    $dailyROI, 
    $product['payout_type'],
    $compound
);
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

.investment-detail-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 24px;
    padding: 0;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    margin-bottom: 30px;
    overflow: hidden;
}

.hero-image-wrapper {
    width: 100%;
    height: 300px;
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.hero-image {
    max-width: 100%;
    max-height: 100%;
    width: auto;
    height: auto;
    object-fit: contain;
    object-position: center;
    padding: 40px;
}

.hero-image-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #032B44 0%, #024a6b 100%);
    color: white;
    font-size: 72px;
    font-weight: 700;
    text-transform: uppercase;
}

.card-content {
    padding: 30px;
}

.roi-display {
    display: inline-block;
    padding: 12px 24px;
    border-radius: 12px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    font-weight: 700;
    font-size: 24px;
    margin-bottom: 20px;
}

.details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin: 30px 0;
}

.detail-card {
    padding: 20px;
    background: #f9fafb;
    border-radius: 12px;
}

.detail-label {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 8px;
}

.detail-value {
    font-size: 20px;
    font-weight: 700;
    color: #032B44;
}

.description-section {
    margin: 30px 0;
    line-height: 1.8;
    color: #374151;
}

.btn-invest {
    position: sticky;
    bottom: 20px;
    width: 100%;
    max-width: 600px;
    margin: 30px auto 0;
    padding: 16px 32px;
    background: linear-gradient(135deg, #032B44 0%, #024a6b 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-weight: 700;
    font-size: 18px;
    cursor: pointer;
    transition: all 0.3s;
    display: block;
}

.btn-invest:hover {
    background: linear-gradient(135deg, #024a6b 0%, #032B44 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(3, 43, 68, 0.4);
}

.btn-invest:disabled {
    background: #9ca3af;
    cursor: not-allowed;
    transform: none;
}

.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.7);
    z-index: 10000; /* Higher than mobile nav (9999) */
    align-items: center;
    justify-content: center;
    padding: 20px;
}

@media (max-width: 768px) {
    .modal {
        z-index: 10000; /* Ensure modals are above mobile nav on mobile */
    }
}

.modal.active {
    display: flex;
}

.modal-content {
    background: white;
    border-radius: 24px;
    padding: 30px;
    max-width: 600px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.modal-header h2 {
    font-size: 24px;
    color: #032B44;
}

.close-modal {
    background: none;
    border: none;
    font-size: 28px;
    color: #6b7280;
    cursor: pointer;
    padding: 0;
    width: 32px;
    height: 32px;
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
}

.form-control:focus {
    outline: none;
    border-color: #032B44;
}

.returns-preview {
    background: #f0fdf4;
    border: 2px solid #86efac;
    border-radius: 12px;
    padding: 20px;
    margin: 20px 0;
}

.returns-preview h3 {
    color: #065f46;
    margin-bottom: 12px;
}

.returns-item {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #d1fae5;
}

.returns-item:last-child {
    border-bottom: none;
    font-weight: 700;
    font-size: 18px;
    color: #032B44;
    margin-top: 8px;
}

.kyc-warning-box {
    background: #fef3c7;
    border: 2px solid #fbbf24;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.kyc-warning-box i {
    font-size: 24px;
    color: #f59e0b;
}

@media (max-width: 768px) {
    .modal-content {
        padding: 20px;
    }
    
    .details-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php if (isset($_SESSION['success'])): ?>
<div style="background: #d1fae5; border: 2px solid #10b981; border-radius: 12px; padding: 16px; margin-bottom: 24px; color: #065f46;">
    <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
</div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
<div style="background: #fee2e2; border: 2px solid #ef4444; border-radius: 12px; padding: 16px; margin-bottom: 24px; color: #991b1b;">
    <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
</div>
<?php endif; ?>

<div class="page-header">
    <h1><?php echo htmlspecialchars($product['title']); ?></h1>
</div>

<div class="investment-detail-card">
    <div class="hero-image-wrapper">
        <?php if (!empty($product['image_url'])): ?>
        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>" class="hero-image" onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 400 400%27%3E%3Crect fill=%27%23f9fafb%27 width=%27400%27 height=%27400%27/%3E%3Ctext x=%2750%25%27 y=%2750%25%27 text-anchor=%27middle%27 fill=%27%239ca3af%27 font-size=%2732%27 dy=%27.3em%27%3E<?php echo htmlspecialchars($product['title']); ?>%3C/text%3E%3C/svg%3E';">
        <?php else: ?>
        <div class="hero-image-placeholder">
            <span><?php echo htmlspecialchars(mb_substr($product['title'], 0, 5)); ?></span>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="card-content">
        <div class="roi-display">
            <?php if ($annualROI > 0): ?>
                <?php echo number_format($annualROI, 2); ?>% Annual ROI
            <?php else: ?>
                <?php echo number_format($dailyROI, 4); ?>% Daily ROI
            <?php endif; ?>
        </div>
        
        <p style="color: #666; font-size: 16px; margin-bottom: 30px;"><?php echo htmlspecialchars($product['short_description'] ?? ''); ?></p>
        
        <div class="details-grid">
            <div class="detail-card">
                <div class="detail-label">Minimum Investment</div>
                <div class="detail-value"><?php echo formatInvestmentAmountForUser($product['min_amount'], $user); ?></div>
            </div>
            
            <div class="detail-card">
                <div class="detail-label">Maximum Investment</div>
                <div class="detail-value"><?php echo $product['max_amount'] ? formatInvestmentAmountForUser($product['max_amount'], $user) : 'No Limit'; ?></div>
            </div>
            
            <div class="detail-card">
                <div class="detail-label">Min Duration</div>
                <div class="detail-value"><?php echo $product['min_duration_days']; ?> days</div>
            </div>
            
            <div class="detail-card">
                <div class="detail-label">Max Duration</div>
                <div class="detail-value"><?php echo $product['max_duration_days'] ? $product['max_duration_days'] . ' days' : 'No Limit'; ?></div>
            </div>
            
            <div class="detail-card">
                <div class="detail-label">Risk Level</div>
                <div class="detail-value" style="text-transform: capitalize;"><?php echo $product['risk_level']; ?></div>
            </div>
            
            <?php if ($remainingCapacity !== null): ?>
            <div class="detail-card">
                <div class="detail-label">Remaining Capacity</div>
                <div class="detail-value"><?php echo formatInvestmentAmountForUser($remainingCapacity, $user); ?></div>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($product['full_description'])): ?>
        <div class="description-section">
            <h3 style="margin-bottom: 16px; color: #032B44;">Description</h3>
            <div><?php echo nl2br(htmlspecialchars($product['full_description'])); ?></div>
        </div>
        <?php endif; ?>
        
        <?php if (!$kycVerified): ?>
        <div class="kyc-warning-box">
            <i class="fas fa-exclamation-triangle"></i>
            <div>
                <strong>KYC Verification Required</strong><br>
                Please complete your KYC verification before investing.
                <a href="<?php echo SITE_URL; ?>/profile/kyc" style="color: #032B44; font-weight: 600;">Complete Now →</a>
            </div>
        </div>
        <?php endif; ?>
        
        <button class="btn-invest" onclick="openInvestModal()" <?php echo !$kycVerified ? 'disabled' : ''; ?>>
            <i class="fas fa-chart-line"></i> Invest Now
        </button>
    </div>
</div>

<!-- Invest Modal -->
<div id="investModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Invest in <?php echo htmlspecialchars($product['title']); ?></h2>
            <button class="close-modal" onclick="closeInvestModal()">&times;</button>
        </div>
        
        <form id="investForm" method="POST" action="<?php echo SITE_URL; ?>/investment/invest">
            <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
            
            <?php 
            // Get user's investment balance
            $userModel = new User();
            $user = $userModel->findById($_SESSION['user_id']);
            $investmentBalance = (float)($user['investment_balance'] ?? 0);
            ?>
            
            <div class="form-group">
                <label class="form-label">Investment Balance</label>
                <div style="padding: 12px 16px; background: #f9fafb; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 18px; font-weight: 600; color: <?php echo $investmentBalance >= $product['min_amount'] ? '#10b981' : '#ef4444'; ?>;">
                    <?php echo formatUserInvestmentBalanceForUser($investmentBalance, $user); ?> Available
                </div>
                <?php if ($investmentBalance < $product['min_amount']): ?>
                <small style="color: #ef4444; display: block; margin-top: 8px;">
                    <i class="fas fa-exclamation-circle"></i> Insufficient balance. You need at least <?php echo formatInvestmentAmountForUser($product['min_amount'], $user); ?> to invest. 
                    <a href="<?php echo SITE_URL; ?>/investment" style="color: #032B44; text-decoration: underline;">Fund your investment account</a> first.
                </small>
                <?php else: ?>
                <small style="color: #6b7280; display: block; margin-top: 8px;">
                    This investment will be deducted from your investment balance.
                </small>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label class="form-label">Investment Amount</label>
                <input type="number" name="amount" class="form-control" 
                       min="<?php echo $product['min_amount']; ?>" 
                       max="<?php echo $product['max_amount'] ?? 999999999; ?>"
                       step="0.01" 
                       value="<?php echo $product['min_amount']; ?>"
                       required 
                       onchange="updateReturns()"
                       onkeyup="updateReturns()">
                <small style="color: #6b7280;">Minimum: <?php echo formatInvestmentAmountForUser($product['min_amount'], $user); ?><?php echo $product['max_amount'] ? ' | Maximum: ' . formatInvestmentAmountForUser($product['max_amount'], $user) : ''; ?></small>
            </div>
            
                   <div class="form-group">
                       <label class="form-label">Duration (days)</label>
                       <select name="duration_days" class="form-control custom-select" data-custom-select="true" data-label="Select Duration" required onchange="updateReturns()">
                    <?php 
                    $minDays = $product['min_duration_days'];
                    $maxDays = $product['max_duration_days'] ?? ($minDays + 365);
                    for ($days = $minDays; $days <= $maxDays; $days += max(1, floor(($maxDays - $minDays) / 10))):
                        if ($days > $maxDays) break;
                    ?>
                    <option value="<?php echo $days; ?>" <?php echo $days === $minDays ? 'selected' : ''; ?>>
                        <?php echo $days; ?> days
                    </option>
                    <?php endfor; ?>
                </select>
            </div>
            
            <div id="returnsPreview" class="returns-preview">
                <h3>Projected Returns</h3>
                <div class="returns-item">
                    <span>Principal:</span>
                    <span id="previewPrincipal"><?php echo formatInvestmentAmountForUser($product['min_amount'], $user); ?></span>
                </div>
                <div class="returns-item">
                    <span>Daily ROI Rate:</span>
                    <span><?php echo number_format($dailyROI, 4); ?>%</span>
                </div>
                <div class="returns-item">
                    <span>Estimated Profit:</span>
                    <span id="previewProfit"><?php echo formatInvestmentAmountForUser($projectedReturns['projected_profit'], $user); ?></span>
                </div>
                <div class="returns-item">
                    <span>Total at Maturity:</span>
                    <span id="previewTotal"><?php echo formatInvestmentAmountForUser($projectedReturns['final_amount'], $user); ?></span>
                </div>
                <div class="returns-item">
                    <span>Projected End Date:</span>
                    <span id="previewEndDate"><?php echo date('F j, Y', strtotime("+{$product['min_duration_days']} days")); ?></span>
                </div>
            </div>
            
            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" required>
                    <span>I accept the terms and conditions for this investment</span>
                </label>
            </div>
            
            <button type="submit" class="btn-invest">
                <i class="fas fa-check-circle"></i> Confirm Investment
            </button>
        </form>
    </div>
</div>

<script>
const dailyROI = <?php echo $dailyROI; ?>;
const compound = <?php echo $compound ? 'true' : 'false'; ?>;

function openInvestModal() {
    document.getElementById('investModal').classList.add('active');
    updateReturns();
}

function closeInvestModal() {
    document.getElementById('investModal').classList.remove('active');
}

function updateReturns() {
    const amount = parseFloat(document.querySelector('input[name="amount"]').value) || 0;
    const duration = parseInt(document.querySelector('select[name="duration_days"]').value) || 0;
    const investmentBalance = <?php echo $investmentBalance; ?>;
    
    // Validate amount doesn't exceed investment balance
    if (amount > investmentBalance) {
        const amountInput = document.querySelector('input[name="amount"]');
        amountInput.setCustomValidity('Amount cannot exceed your investment balance of ' + formatCurrency(investmentBalance));
        amountInput.reportValidity();
        return;
    } else {
        document.querySelector('input[name="amount"]').setCustomValidity('');
    }
    
    if (amount <= 0 || duration <= 0) return;
    
    let totalAccrued = 0;
    let currentPrincipal = amount;
    
    for (let day = 1; day <= duration; day++) {
        const dailyAmount = currentPrincipal * (dailyROI / 100);
        totalAccrued += dailyAmount;
        if (compound) {
            currentPrincipal += dailyAmount;
        }
    }
    
    const finalAmount = amount + totalAccrued;
    const startDate = new Date();
    const endDate = new Date(startDate);
    endDate.setDate(startDate.getDate() + duration);
    
    document.getElementById('previewPrincipal').textContent = formatCurrency(amount);
    document.getElementById('previewProfit').textContent = formatCurrency(totalAccrued);
    document.getElementById('previewTotal').textContent = formatCurrency(finalAmount);
    document.getElementById('previewEndDate').textContent = endDate.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
}

const userCurrency = '<?php echo $userCurrency; ?>';
const defaultCurrency = '<?php echo DEFAULT_CURRENCY; ?>';

// Exchange rate cache
let exchangeRateCache = {};

// Currency symbols mapping
const currencySymbols = {
    'USD': '$', 'EUR': '€', 'GBP': '£', 'JPY': '¥',
    'CNY': '¥', 'INR': '₹', 'CAD': 'CA$', 'AUD': 'A$',
    'NGN': '₦', 'ZAR': 'R', 'AED': 'د.إ', 'SAR': 'ر.س',
    'QAR': 'ر.ق', 'KWD': 'د.ك', 'KES': 'KSh', 'GHS': '₵',
    'PKR': '₨', 'BDT': '৳', 'LKR': 'Rs', 'SGD': 'S$',
    'MYR': 'RM', 'THB': '฿', 'IDR': 'Rp', 'PHP': '₱',
    'VND': '₫', 'KRW': '₩', 'BRL': 'R$', 'MXN': '$',
    'ARS': '$', 'CLP': '$', 'COP': '$', 'TRY': '₺',
    'ILS': '₪', 'NZD': 'NZ$', 'HKD': 'HK$', 'TWD': 'NT$',
    'CHF': 'Fr', 'SEK': 'kr', 'NOK': 'kr', 'DKK': 'kr',
    'EGP': 'E£', 'MAD': 'د.م.', 'TND': 'د.ت', 'DZD': 'د.ج',
    'PLN': 'zł', 'RUB': '₽', 'CZK': 'Kč', 'HUF': 'Ft',
    'RON': 'lei', 'BGN': 'лв', 'PEN': 'S/', 'XOF': 'CFA',
    'ZMW': 'ZK'
};

// Fetch exchange rate on page load
async function loadExchangeRate() {
    if (defaultCurrency !== userCurrency) {
        try {
            const response = await fetch(`<?php echo SITE_URL; ?>/api/get-exchange-rate.php?from=${defaultCurrency}&to=${userCurrency}`);
            const data = await response.json();
            if (data.success && data.rate) {
                exchangeRateCache[`${defaultCurrency}_${userCurrency}`] = data.rate;
            }
        } catch (error) {
            console.error('Error loading exchange rate:', error);
            exchangeRateCache[`${defaultCurrency}_${userCurrency}`] = 1.0;
        }
    } else {
        exchangeRateCache[`${defaultCurrency}_${userCurrency}`] = 1.0;
    }
}

// Format currency with conversion
function formatCurrency(amount, currency = userCurrency, fromCurrency = defaultCurrency) {
    // Convert amount if currencies are different and we have exchange rate
    if (fromCurrency !== currency) {
        const cacheKey = `${fromCurrency}_${currency}`;
        if (exchangeRateCache[cacheKey]) {
            amount = parseFloat(amount) * exchangeRateCache[cacheKey];
        }
    }
    
    const symbol = currencySymbols[currency] || (currency + ' ');
    const decimals = ['JPY', 'KRW', 'VND', 'CLP'].includes(currency) ? 0 : 2;
    return symbol + parseFloat(amount).toFixed(decimals).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

// Load exchange rate on page load
loadExchangeRate();

// Close modal on outside click
document.getElementById('investModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeInvestModal();
    }
});
</script>

<?php include __DIR__ . '/../../includes/mobile-nav.php'; ?>

