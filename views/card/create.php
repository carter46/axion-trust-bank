<?php 
$pageTitle = 'Apply for Card - SecureBank';
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
    max-width: 800px;
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

.card-type-selector {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    margin-bottom: 20px;
}

.card-type-option {
    position: relative;
}

.card-type-option input[type="radio"] {
    position: absolute;
    opacity: 0;
}

.card-type-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s;
    text-align: center;
}

.card-type-option input[type="radio"]:checked + .card-type-label {
    border-color: #032B44;
    background: rgba(3, 43, 68, 0.05);
}

.card-type-label i {
    font-size: 32px;
    margin-bottom: 10px;
    color: #032B44;
}

.card-type-name {
    font-weight: 600;
    color: #032B44;
    margin-bottom: 5px;
}

.card-type-desc {
    font-size: 12px;
    color: #666;
}

.checkbox-group {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 15px;
}

.checkbox-group input[type="checkbox"] {
    width: 20px;
    height: 20px;
    cursor: pointer;
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

@media (max-width: 768px) {
    .card {
        padding: 25px;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .card-type-selector {
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
    <h1>Apply for Card</h1>
    <p>Choose your card type and fill in the application details</p>
</div>

<div class="card">
    <?php if (empty($accounts)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            You need at least one active account to apply for a card. Please contact support to create an account first.
        </div>
        <a href="<?php echo SITE_URL; ?>/card" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Back to Cards
        </a>
    <?php else: ?>
        <form method="POST" action="<?php echo SITE_URL; ?>/card/create" id="cardApplicationForm">
            <!-- Card Type Selection -->
            <div class="form-section">
                <h3 class="section-title">Select Card Type</h3>
                <div class="card-type-selector">
                    <div class="card-type-option">
                        <input type="radio" name="card_type" id="debit" value="debit" checked>
                        <label for="debit" class="card-type-label">
                            <i class="fas fa-credit-card"></i>
                            <div class="card-type-name">Debit Card</div>
                            <div class="card-type-desc">Spend from your account balance</div>
                        </label>
                    </div>
                    
                    <div class="card-type-option">
                        <input type="radio" name="card_type" id="credit" value="credit">
                        <label for="credit" class="card-type-label">
                            <i class="fas fa-credit-card"></i>
                            <div class="card-type-name">Credit Card</div>
                            <div class="card-type-desc">Borrow up to your limit</div>
                        </label>
                    </div>
                    
                    <div class="card-type-option">
                        <input type="radio" name="card_type" id="prepaid" value="prepaid">
                        <label for="prepaid" class="card-type-label">
                            <i class="fas fa-credit-card"></i>
                            <div class="card-type-name">Prepaid Card</div>
                            <div class="card-type-desc">Load and spend as needed</div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Basic Information -->
            <div class="form-section">
                <h3 class="section-title">Card Details</h3>
                
                <div class="form-group">
                    <label class="form-label">
                        Link to Account <span class="required">*</span>
                    </label>
                    <select name="account_id" class="form-control" required>
                        <option value="">Select Account</option>
                        <?php foreach ($accounts as $account): ?>
                            <option value="<?php echo $account['id']; ?>">
                                <?php echo ucfirst($account['account_type']); ?> - <?php echo $account['account_number']; ?> 
                                (Balance: <?php echo formatAccountBalance($account['balance'], $account, $userCurrency); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="help-text">Select the account to link this card to</div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Card Name <span class="required">*</span>
                    </label>
                    <input type="text" name="card_name" class="form-control" 
                           placeholder="e.g., My Shopping Card" required maxlength="50">
                    <div class="help-text">Give your card a memorable name</div>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" name="is_virtual" id="is_virtual" value="1">
                    <label for="is_virtual" class="form-label" style="margin-bottom: 0;">
                        Virtual Card (Instant digital card for online use)
                    </label>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" name="is_single_use" id="is_single_use" value="1">
                    <label for="is_single_use" class="form-label" style="margin-bottom: 0;">
                        Single-Use Card (Card expires after first use)
                    </label>
                </div>
            </div>

            <!-- Spending Limits -->
            <div class="form-section">
                <h3 class="section-title">Set Spending Limits</h3>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Daily Limit (<?php echo $userCurrency; ?>)</label>
                        <input type="number" name="daily_limit" class="form-control" 
                               value="5000" min="100" max="50000" step="100">
                        <div class="help-text">Maximum daily spending</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Monthly Limit (<?php echo $userCurrency; ?>)</label>
                        <input type="number" name="monthly_limit" class="form-control" 
                               value="50000" min="1000" max="500000" step="1000">
                        <div class="help-text">Maximum monthly spending</div>
                    </div>
                </div>
            </div>

            <!-- Credit Card Specific -->
            <div class="form-section" id="creditCardSection" style="display: none;">
                <h3 class="section-title">Credit Card Information</h3>
                
                <div class="form-group">
                    <label class="form-label">
                        Requested Credit Limit (<?php echo $userCurrency; ?>) <span class="required">*</span>
                    </label>
                    <input type="number" name="credit_limit" class="form-control" 
                           value="10000" min="1000" max="100000" step="1000">
                    <div class="help-text">Amount you wish to borrow (subject to approval)</div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Purpose of Credit Card
                    </label>
                    <textarea name="purpose" class="form-control" rows="3" 
                              placeholder="e.g., Travel, Business expenses, Emergency fund"></textarea>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Employment Status</label>
                        <select name="employment_status" class="form-control">
                            <option value="employed">Employed</option>
                            <option value="self-employed">Self-Employed</option>
                            <option value="unemployed">Unemployed</option>
                            <option value="retired">Retired</option>
                            <option value="student">Student</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Annual Income (<?php echo $userCurrency; ?>)</label>
                        <input type="number" name="annual_income" class="form-control" 
                               placeholder="50000" min="0" step="1000">
                    </div>
                </div>
            </div>

            <!-- Terms and Conditions -->
            <div class="form-section">
                <div class="checkbox-group">
                    <input type="checkbox" name="terms" id="terms" required>
                    <label for="terms" class="form-label" style="margin-bottom: 0;">
                        I agree to the <a href="#" style="color: #032B44;">Terms and Conditions</a> <span class="required">*</span>
                    </label>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="<?php echo SITE_URL; ?>/card" class="btn btn-secondary">
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
// Show/hide credit card section based on card type
document.querySelectorAll('input[name="card_type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const creditSection = document.getElementById('creditCardSection');
        if (this.value === 'credit') {
            creditSection.style.display = 'block';
        } else {
            creditSection.style.display = 'none';
        }
    });
});

// Form validation
document.getElementById('cardApplicationForm').addEventListener('submit', function(e) {
    const cardType = document.querySelector('input[name="card_type"]:checked').value;
    
    if (cardType === 'credit') {
        const creditLimit = document.querySelector('input[name="credit_limit"]').value;
        if (!creditLimit || creditLimit < 1000) {
            e.preventDefault();
            alert('Please enter a credit limit of at least <?php echo formatInvestmentAmountForUser(1000, $user); ?>');
            return false;
        }
    }
    
    const terms = document.getElementById('terms');
    if (!terms.checked) {
        e.preventDefault();
        alert('Please agree to the Terms and Conditions');
        return false;
    }
});
</script>

<?php
include __DIR__ . '/../../includes/mobile-nav.php';
?>

