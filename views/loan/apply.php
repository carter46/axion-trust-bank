<?php 
$pageTitle = 'Apply for Loan - SecureBank';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

requireLogin();

// Get user currency
$userModel = new User();
$user = $userModel->findById($_SESSION['user_id']);
$userCurrency = getUserDisplayCurrency($user);

// Include head
include __DIR__ . '/../../includes/head.php';

// Include sidebar
include __DIR__ . '/../../includes/sidebar.php';

// Get user accounts
$accountModel = new Account();
$accounts = $accountModel->getUserAccounts($_SESSION['user_id']);
?>

<?php include __DIR__ . '/../../includes/restricted-banner.php'; ?>

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

.page-header p {
    color: #666;
    font-size: 16px;
}

.card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 24px;
    padding: 40px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    max-width: 900px;
    margin: 0 auto;
}

.form-section {
    margin-bottom: 30px;
}

.section-title {
    font-size: 18px;
    font-weight: 700;
    color: #032B44;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #f0f0f0;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
}

.required {
    color: #ef4444;
}

.form-control {
    width: 100%;
    padding: 12px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s;
}

.form-control:focus {
    outline: none;
    border-color: #032B44;
    box-shadow: 0 0 0 3px rgba(3, 43, 68, 0.1);
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.loan-type-selector {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.loan-type-option {
    position: relative;
}

.loan-type-option input[type="radio"] {
    position: absolute;
    opacity: 0;
}

.loan-type-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s;
    text-align: center;
    height: 100%;
}

.loan-type-option input[type="radio"]:checked + .loan-type-label {
    border-color: #032B44;
    background: rgba(3, 43, 68, 0.05);
}

.loan-type-label i {
    font-size: 32px;
    margin-bottom: 10px;
    color: #032B44;
}

.loan-type-name {
    font-weight: 600;
    color: #032B44;
    margin-bottom: 5px;
}

.loan-type-desc {
    font-size: 12px;
    color: #666;
}

.calculation-box {
    background: #f8f9fa;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 20px;
    margin-top: 20px;
}

.calculation-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #e5e7eb;
}

.calculation-row:last-child {
    border-bottom: none;
    font-weight: 700;
    font-size: 18px;
    color: #032B44;
    margin-top: 10px;
}

.calculation-label {
    color: #666;
}

.calculation-value {
    font-weight: 600;
    color: #032B44;
}

.file-upload-area {
    border: 2px dashed #e5e7eb;
    border-radius: 8px;
    padding: 30px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
}

.file-upload-area:hover {
    border-color: #032B44;
    background: rgba(3, 43, 68, 0.02);
}

.file-upload-area i {
    font-size: 48px;
    color: #032B44;
    margin-bottom: 15px;
}

.btn {
    padding: 14px 32px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 10px;
}

.btn-primary {
    background: linear-gradient(135deg, #032B44 0%, #024a6b 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(3, 43, 68, 0.3);
}

.btn-secondary {
    background: #e5e7eb;
    color: #374151;
}

.btn-secondary:hover {
    background: #d1d5db;
}

.form-actions {
    display: flex;
    gap: 15px;
    justify-content: flex-end;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 2px solid #f0f0f0;
}

.help-text {
    font-size: 13px;
    color: #666;
    margin-top: 5px;
}

.alert {
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.alert-info {
    background: #dbeafe;
    color: #1e40af;
    border-left: 4px solid #3b82f6;
}

.alert-warning {
    background: #fef3c7;
    color: #92400e;
    border-left: 4px solid #f59e0b;
}

@media (max-width: 768px) {
    .card {
        padding: 25px;
    }
    
    .form-grid,
    .loan-type-selector {
        grid-template-columns: 1fr;
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

<div class="page-header">
    <h1>Apply for Loan</h1>
    <p>Choose your loan type and submit your application</p>
</div>

<div class="card">
    <?php if (empty($accounts)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            You need at least one active account to apply for a loan. Please contact support to create an account first.
        </div>
        <a href="<?php echo SITE_URL; ?>/loan" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Back to Loans
        </a>
    <?php else: ?>
        <form method="POST" action="<?php echo SITE_URL; ?>/loan/apply" id="loanApplicationForm" enctype="multipart/form-data">
            <!-- Loan Type Selection -->
            <div class="form-section">
                <h3 class="section-title">Select Loan Type</h3>
                <div class="loan-type-selector">
                    <div class="loan-type-option">
                        <input type="radio" name="loan_type" id="personal" value="personal" checked>
                        <label for="personal" class="loan-type-label">
                            <i class="fas fa-user"></i>
                            <div class="loan-type-name">Personal Loan</div>
                            <div class="loan-type-desc">For personal expenses</div>
                        </label>
                    </div>
                    
                    <div class="loan-type-option">
                        <input type="radio" name="loan_type" id="business" value="business">
                        <label for="business" class="loan-type-label">
                            <i class="fas fa-briefcase"></i>
                            <div class="loan-type-name">Business Loan</div>
                            <div class="loan-type-desc">For business needs</div>
                        </label>
                    </div>
                    
                    <div class="loan-type-option">
                        <input type="radio" name="loan_type" id="mortgage" value="mortgage">
                        <label for="mortgage" class="loan-type-label">
                            <i class="fas fa-home"></i>
                            <div class="loan-type-name">Mortgage</div>
                            <div class="loan-type-desc">For property purchase</div>
                        </label>
                    </div>
                    
                    <div class="loan-type-option">
                        <input type="radio" name="loan_type" id="auto" value="auto">
                        <label for="auto" class="loan-type-label">
                            <i class="fas fa-car"></i>
                            <div class="loan-type-name">Auto Loan</div>
                            <div class="loan-type-desc">For vehicle purchase</div>
                        </label>
                    </div>
                    
                    <div class="loan-type-option">
                        <input type="radio" name="loan_type" id="education" value="education">
                        <label for="education" class="loan-type-label">
                            <i class="fas fa-graduation-cap"></i>
                            <div class="loan-type-name">Education</div>
                            <div class="loan-type-desc">For tuition & fees</div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Loan Details -->
            <div class="form-section">
                <h3 class="section-title">Loan Details</h3>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">
                            Loan Amount (<?php echo $userCurrency; ?>) <span class="required">*</span>
                        </label>
                        <input type="number" name="loan_amount" id="loanAmount" class="form-control" 
                               value="10000" min="1000" max="1000000" step="1000" required>
                        <div class="help-text">Amount you wish to borrow</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Loan Term (Months) <span class="required">*</span>
                        </label>
                        <select name="term_months" id="termMonths" class="form-control" required>
                            <option value="12">12 months (1 year)</option>
                            <option value="24">24 months (2 years)</option>
                            <option value="36" selected>36 months (3 years)</option>
                            <option value="48">48 months (4 years)</option>
                            <option value="60">60 months (5 years)</option>
                            <option value="120">120 months (10 years)</option>
                            <option value="180">180 months (15 years)</option>
                            <option value="240">240 months (20 years)</option>
                            <option value="360">360 months (30 years)</option>
                        </select>
                        <div class="help-text">Duration to repay the loan</div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Deposit Account <span class="required">*</span>
                    </label>
                    <select name="account_id" class="form-control" required>
                        <option value="">Select Account for Deposit</option>
                        <?php foreach ($accounts as $account): ?>
                            <option value="<?php echo $account['id']; ?>">
                                <?php echo ucfirst($account['account_type']); ?> - <?php echo $account['account_number']; ?> 
                                (Balance: <?php echo formatAccountBalance($account['balance'], $account, $userCurrency); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="help-text">Account where loan funds will be deposited</div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Interest Rate (%) <span class="required">*</span>
                    </label>
                    <input type="number" name="interest_rate" id="interestRate" class="form-control" 
                           value="8.5" min="3" max="25" step="0.1" required readonly>
                    <div class="help-text">Rate will be determined based on your loan type and term</div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Purpose of Loan <span class="required">*</span>
                    </label>
                    <textarea name="purpose" class="form-control" rows="4" required
                              placeholder="Please describe the purpose of this loan..."></textarea>
                </div>

                <!-- Loan Calculation -->
                <div class="calculation-box">
                    <h4 style="color: #032B44; margin-bottom: 15px;">Loan Estimate</h4>
                    <div class="calculation-row">
                        <span class="calculation-label">Loan Amount:</span>
                        <span class="calculation-value" id="calcAmount"><?php echo formatLoanAmountForUser(10000, $user); ?></span>
                    </div>
                    <div class="calculation-row">
                        <span class="calculation-label">Interest Rate:</span>
                        <span class="calculation-value" id="calcRate">8.5%</span>
                    </div>
                    <div class="calculation-row">
                        <span class="calculation-label">Loan Term:</span>
                        <span class="calculation-value" id="calcTerm">36 months</span>
                    </div>
                    <div class="calculation-row">
                        <span class="calculation-label">Total Interest:</span>
                        <span class="calculation-value" id="calcInterest"><?php echo formatLoanAmountForUser(1405.89, $user); ?></span>
                    </div>
                    <div class="calculation-row">
                        <span class="calculation-label">Monthly Payment:</span>
                        <span class="calculation-value" id="calcMonthly"><?php echo formatLoanAmountForUser(316.83, $user); ?></span>
                    </div>
                    <div class="calculation-row">
                        <span class="calculation-label">Total Repayment:</span>
                        <span class="calculation-value" id="calcTotal"><?php echo formatLoanAmountForUser(11405.89, $user); ?></span>
                    </div>
                </div>
            </div>

            <!-- Supporting Documents -->
            <div class="form-section">
                <h3 class="section-title">Supporting Documents (Optional)</h3>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    For faster approval, please upload supporting documents (ID, proof of income, etc.)
                </div>
                
                <div class="form-group">
                    <label for="fileUpload" class="file-upload-area">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <div style="font-weight: 600; color: #032B44; margin-bottom: 5px;">
                            Click to upload documents
                        </div>
                        <div style="font-size: 13px; color: #666;">
                            PDF, JPG, PNG (Max 5MB each)
                        </div>
                    </label>
                    <input type="file" name="documents[]" id="fileUpload" multiple 
                           accept=".pdf,.jpg,.jpeg,.png" style="display: none;">
                </div>
                <div id="fileList" style="margin-top: 10px;"></div>
            </div>

            <!-- Terms and Conditions -->
            <div class="form-section">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <input type="checkbox" name="terms" id="terms" required style="width: 20px; height: 20px;">
                    <label for="terms" class="form-label" style="margin-bottom: 0;">
                        I agree to the <a href="#" style="color: #032B44;">Loan Terms and Conditions</a> <span class="required">*</span>
                    </label>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="<?php echo SITE_URL; ?>/loan" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i>
                    Submit Application
                </button>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
// User currency for formatting
const userCurrency = <?php echo json_encode($userCurrency); ?>;
const defaultCurrency = <?php echo json_encode(DEFAULT_CURRENCY); ?>;

// Exchange rate cache
let exchangeRateCache = {};

// Currency symbols mapping (complete list matching PHP formatCurrency)
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

// Format currency in JavaScript (with conversion)
function formatCurrencyJS(amount, currency = userCurrency, fromCurrency = defaultCurrency) {
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

// Interest rate based on loan type
const interestRates = {
    'personal': 8.5,
    'business': 7.5,
    'mortgage': 4.5,
    'auto': 6.5,
    'education': 5.5
};

// Update interest rate when loan type changes
document.querySelectorAll('input[name="loan_type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const rate = interestRates[this.value] || 8.5;
        document.getElementById('interestRate').value = rate;
        calculateLoan();
    });
});

// Calculate loan when inputs change
document.getElementById('loanAmount').addEventListener('input', calculateLoan);
document.getElementById('termMonths').addEventListener('change', calculateLoan);
document.getElementById('interestRate').addEventListener('input', calculateLoan);

function calculateLoan() {
    const amount = parseFloat(document.getElementById('loanAmount').value) || 0;
    const term = parseInt(document.getElementById('termMonths').value) || 36;
    const rate = parseFloat(document.getElementById('interestRate').value) || 8.5;
    
    // Monthly interest rate
    const monthlyRate = (rate / 100) / 12;
    
    // Calculate monthly payment using amortization formula
    const monthlyPayment = amount * (monthlyRate * Math.pow(1 + monthlyRate, term)) / 
                          (Math.pow(1 + monthlyRate, term) - 1);
    
    const totalRepayment = monthlyPayment * term;
    const totalInterest = totalRepayment - amount;
    
    // Update display with proper currency formatting
    document.getElementById('calcAmount').textContent = formatCurrencyJS(amount);
    document.getElementById('calcRate').textContent = rate.toFixed(1) + '%';
    document.getElementById('calcTerm').textContent = term + ' months';
    document.getElementById('calcInterest').textContent = formatCurrencyJS(totalInterest);
    document.getElementById('calcMonthly').textContent = formatCurrencyJS(monthlyPayment);
    document.getElementById('calcTotal').textContent = formatCurrencyJS(totalRepayment);
}

// File upload handling
document.getElementById('fileUpload').addEventListener('change', function(e) {
    const fileList = document.getElementById('fileList');
    fileList.innerHTML = '';
    
    Array.from(this.files).forEach(file => {
        const fileItem = document.createElement('div');
        fileItem.style.cssText = 'padding: 10px; background: #f8f9fa; border-radius: 8px; margin-bottom: 5px; display: flex; justify-content: space-between; align-items: center;';
        fileItem.innerHTML = `
            <span><i class="fas fa-file"></i> ${file.name} (${(file.size / 1024).toFixed(1)} KB)</span>
        `;
        fileList.appendChild(fileItem);
    });
});

// Calculate on page load
calculateLoan();
</script>

<?php
include __DIR__ . '/../../includes/mobile-nav.php';
?>

