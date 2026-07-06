<?php 
$pageTitle = 'Create New Account - Octobank';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

// Check if user joined via joint account - they cannot create accounts
require_once __DIR__ . '/../../models/JointAccount.php';
$jointAccount = new JointAccount();
if ($jointAccount->isJointAccountUser($_SESSION['user_id'])) {
    $_SESSION['error'] = 'You cannot create new accounts. You have access to shared accounts only.';
    redirect('/account');
}

// Get user's OWNED accounts only (not joint access) to check which types they already have
$accountModel = new Account();
$ownedAccounts = $jointAccount->getUserOwnedAccounts($_SESSION['user_id']);

// Get existing account types from owned accounts only
$existingAccountTypes = array_column($ownedAccounts, 'account_type');

// Include head
include __DIR__ . '/../../includes/head.php';

// Include sidebar and main structure
include __DIR__ . '/../../includes/sidebar.php';
?>

<!-- ===== CREATE ACCOUNT PAGE CONTENT ===== -->

<style>
    /* Override parent content-area styles */
    .main-content-area .content-area {
        background: #f5f5f5 !important;
        padding: 15px !important;
    }

    .page-header {
        margin-bottom: 30px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
    }

    .page-header-content {
        flex: 1;
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
        margin: 0;
    }

    .create-account-card {
        background: white;
        border-radius: 16px;
        padding: 32px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        margin-bottom: 30px;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #374151;
        font-size: 14px;
    }

    .form-input {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 16px;
        transition: border-color 0.3s ease;
        background: #f9fafb;
    }

    .form-input:focus {
        outline: none;
        border-color: #1e3a8a;
        background: white;
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
    }

    .form-select {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 16px;
        background: #f9fafb;
        cursor: pointer;
        transition: border-color 0.3s ease;
    }

    .form-select:focus {
        outline: none;
        border-color: #1e3a8a;
        background: white;
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
    }

    .form-select option:disabled {
        background-color: #f3f4f6;
        color: #9ca3af;
        font-style: italic;
    }

    .account-type-disabled {
        background: #f3f4f6;
        color: #9ca3af;
        cursor: not-allowed;
    }

    .existing-account-info {
        background: #fef3c7;
        border: 1px solid #f59e0b;
        border-radius: 8px;
        padding: 12px;
        margin-top: 8px;
        font-size: 14px;
        color: #92400e;
    }

    .existing-account-info i {
        margin-right: 8px;
    }

    .account-type-info {
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        border-radius: 8px;
        padding: 15px;
        margin-top: 10px;
        font-size: 14px;
        color: #0369a1;
    }

    .account-type-info h4 {
        margin: 0 0 8px 0;
        font-size: 14px;
        font-weight: 600;
    }

    .account-type-info ul {
        margin: 0;
        padding-left: 20px;
    }

    .account-type-info li {
        margin-bottom: 4px;
    }

    .btn-create {
        width: 100%;
        background: #1e3a8a;
        color: white;
        border: none;
        padding: 15px;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .btn-create:hover {
        background: #1e40af;
    }

    .btn-create:disabled {
        opacity: 0.6;
        cursor: not-allowed;
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

    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .account-benefits {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #e5e7eb;
    }

    .account-benefits h3 {
        margin: 0 0 15px 0;
        color: #374151;
        font-size: 18px;
        font-weight: 600;
    }

    .benefits-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 15px;
    }

    .benefit-item {
        display: flex;
        align-items: center;
    }

    .benefit-item i {
        margin-right: 10px;
        font-size: 16px;
    }

    .benefit-item span {
        font-size: 14px;
        color: #6b7280;
    }

    @media (max-width: 768px) {
        .main-content-area .content-area {
            padding: 10px !important;
        }
        
        .create-account-card {
            margin: 5px;
            padding: 15px;
            border-radius: 8px;
        }
        
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }
        
        .page-header h1 {
            font-size: 22px;
        }
        
        .page-header p {
            font-size: 14px;
        }
        
        .benefits-grid {
            grid-template-columns: 1fr;
        }
    }
    
    @media (max-width: 480px) {
        .main-content-area .content-area {
            padding: 5px !important;
        }
        
        .create-account-card {
            margin: 2px;
            padding: 12px;
        }
        
        .page-header h1 {
            font-size: 20px;
        }
        
        .page-header p {
            font-size: 13px;
        }
    }
</style>

<div class="main-content-area">
    <div class="content-area">
        <!-- Page Header with Back Button -->
        <div class="page-header">
            <div class="page-header-content">
                <h1>Create New Account</h1>
                <p>Open a new account to manage your finances</p>
            </div>
            <a href="/account" class="back-button">
                <i class="fas fa-arrow-left"></i>
                Back to My Accounts
            </a>
        </div>

        <!-- Create Account Card -->
        <div class="create-account-card">

            <!-- Success/Error Messages -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <!-- Create Account Form -->
            <form method="POST" action="/account/create" id="createAccountForm">
                <!-- Account Type -->
                <div class="form-group">
                    <label for="account_type">Account Type *</label>
                    <select name="account_type" id="account_type" class="form-select" required>
                        <option value="">Select account type</option>
                        <option value="checking" <?php echo in_array('checking', $existingAccountTypes) ? 'disabled' : ''; ?>>
                            Checking Account<?php echo in_array('checking', $existingAccountTypes) ? ' (Already Owned)' : ''; ?>
                        </option>
                        <option value="savings" <?php echo in_array('savings', $existingAccountTypes) ? 'disabled' : ''; ?>>
                            Savings Account<?php echo in_array('savings', $existingAccountTypes) ? ' (Already Owned)' : ''; ?>
                        </option>
                        <option value="business" <?php echo in_array('business', $existingAccountTypes) ? 'disabled' : ''; ?>>
                            Business Account<?php echo in_array('business', $existingAccountTypes) ? ' (Already Owned)' : ''; ?>
                        </option>
                    </select>
                    
                    <!-- Show existing account info if user has accounts -->
                    <?php if (!empty($existingAccountTypes)): ?>
                        <div class="existing-account-info">
                            <i class="fas fa-info-circle"></i>
                            You already have <?php echo count($existingAccountTypes); ?> account<?php echo count($existingAccountTypes) > 1 ? 's' : ''; ?>. 
                            Select a different account type to create a new one.
                        </div>
                    <?php endif; ?>
                    
                    <!-- Account Type Information -->
                    <div class="account-type-info" id="accountTypeInfo" style="display: none;">
                        <h4>Account Features:</h4>
                        <ul id="accountFeatures">
                            <!-- Features will be populated by JavaScript -->
                        </ul>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-create" id="createBtn">
                    <i class="fas fa-plus"></i>
                    Create Account
                </button>
            </form>

            <!-- Account Benefits -->
            <div class="account-benefits">
                <h3>
                    <i class="fas fa-shield-alt"></i> Account Benefits
                </h3>
                <div class="benefits-grid">
                    <div class="benefit-item">
                        <i class="fas fa-lock" style="color: #10b981;"></i>
                        <span>Bank-level security</span>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-mobile-alt" style="color: #3b82f6;"></i>
                        <span>Mobile banking access</span>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-chart-line" style="color: #f59e0b;"></i>
                        <span>Real-time balance updates</span>
                    </div>
                    <div class="benefit-item">
                        <i class="fas fa-headset" style="color: #8b5cf6;"></i>
                        <span>24/7 customer support</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const accountTypeSelect = document.getElementById('account_type');
    const accountTypeInfo = document.getElementById('accountTypeInfo');
    const accountFeatures = document.getElementById('accountFeatures');
    const createForm = document.getElementById('createAccountForm');
    const createBtn = document.getElementById('createBtn');

    // Account type features
    const accountFeaturesData = {
        'checking': [
            'Unlimited transactions',
            'Debit card included',
            'Online banking access',
            'Direct deposit available'
        ],
        'savings': [
            'Higher interest rates',
            'Limited transactions (6 per month)',
            'No monthly fees',
            'Automatic savings tools'
        ],
        'business': [
            'Business debit card',
            'Multiple user access',
            'Expense tracking',
            'Business reporting tools'
        ]
    };

    // Show account type information when selected
    accountTypeSelect.addEventListener('change', function() {
        const selectedType = this.value;
        const selectedOption = this.options[this.selectedIndex];
        
        // Don't show info for disabled options
        if (selectedOption.disabled) {
            accountTypeInfo.style.display = 'none';
            return;
        }
        
        if (selectedType && accountFeaturesData[selectedType]) {
            accountFeatures.innerHTML = accountFeaturesData[selectedType]
                .map(feature => `<li>${feature}</li>`)
                .join('');
            accountTypeInfo.style.display = 'block';
        } else {
            accountTypeInfo.style.display = 'none';
        }
    });

    // Form submission
    createForm.addEventListener('submit', function(e) {
        const selectedOption = accountTypeSelect.options[accountTypeSelect.selectedIndex];
        
        // Prevent submission if disabled option is selected
        if (selectedOption.disabled) {
            e.preventDefault();
            alert('You already have this account type. Please select a different account type.');
            return;
        }
        
        // Disable button and show loading
        createBtn.disabled = true;
        createBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account...';
        
        // Submit form
        this.submit();
    });

});
</script>

<?php include __DIR__ . '/../../includes/auth-foot.php'; ?>
