<?php 
$pageTitle = 'Create User - Admin - SecureBank';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

// Include head
include __DIR__ . '/../../includes/head.php';

// Include admin sidebar
include __DIR__ . '/../../includes/admin-sidebar.php';
include __DIR__ . '/../../includes/admin-modals.php';
?>

<style>
.page-header {
    margin-bottom: 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.page-header h1 {
    font-size: 32px;
    font-weight: 700;
    color: #032B44;
    margin-bottom: 8px;
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

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-primary {
    background: #10b981;
    color: white;
}

.btn-primary:hover {
    background: #059669;
    transform: translateY(-2px);
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

.password-strength {
    height: 4px;
    background: #e5e7eb;
    border-radius: 2px;
    margin-top: 8px;
    overflow: hidden;
}

.password-strength-bar {
    height: 100%;
    width: 0%;
    transition: all 0.3s;
}

.strength-weak { background: #ef4444; width: 33%; }
.strength-medium { background: #f59e0b; width: 66%; }
.strength-strong { background: #10b981; width: 100%; }

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .card {
        padding: 25px;
    }
    
    .form-grid {
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
    <div>
        <h1>Create New User</h1>
        <p style="color: #666;">Add a new user to the banking system</p>
    </div>
    <a href="<?php echo SITE_URL; ?>/admin/users" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Back to Users
    </a>
</div>

<div class="card">
    <form method="POST" action="<?php echo SITE_URL; ?>/admin/user-create" id="createUserForm">
        
        <!-- Personal Information -->
        <div class="form-section">
            <h3 class="section-title">Personal Information</h3>
            
            <div class="form-group">
                <label class="form-label">
                    Full Name <span class="required">*</span>
                </label>
                <input type="text" name="full_name" class="form-control" 
                       placeholder="John Doe" required maxlength="100">
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">
                        Email Address <span class="required">*</span>
                    </label>
                    <input type="email" name="email" class="form-control" 
                           placeholder="john@example.com" required>
                    <div class="help-text">Will be used for login</div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Phone Number <span class="required">*</span>
                    </label>
                    <input type="tel" name="phone" class="form-control" 
                           placeholder="+1 (555) 123-4567" required>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">
                        Date of Birth <span class="required">*</span>
                    </label>
                    <input type="date" name="date_of_birth" class="form-control" 
                           max="<?php echo date('Y-m-d', strtotime('-18 years')); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Gender
                    </label>
                    <select name="gender" class="form-control">
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Address Information -->
        <div class="form-section">
            <h3 class="section-title">Address Information</h3>
            
            <div class="form-group">
                <label class="form-label">
                    Street Address <span class="required">*</span>
                </label>
                <input type="text" name="address" class="form-control" 
                       placeholder="123 Main Street" required>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">
                        City <span class="required">*</span>
                    </label>
                    <input type="text" name="city" class="form-control" 
                           placeholder="New York" required>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        State/Province <span class="required">*</span>
                    </label>
                    <input type="text" name="state" class="form-control" 
                           placeholder="NY" required>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">
                        Postal Code <span class="required">*</span>
                    </label>
                    <input type="text" name="postal_code" class="form-control" 
                           placeholder="10001" required>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Country <span class="required">*</span>
                    </label>
                    <input type="text" name="country" class="form-control" 
                           value="United States" required>
                </div>
            </div>
        </div>

        <!-- Account Settings -->
        <div class="form-section">
            <h3 class="section-title">Account Settings</h3>
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">
                        Password <span class="required">*</span>
                    </label>
                    <input type="password" name="password" id="password" class="form-control" 
                           placeholder="••••••••" required minlength="8">
                    <div class="password-strength">
                        <div class="password-strength-bar" id="strengthBar"></div>
                    </div>
                    <div class="help-text" id="strengthText">
                        Minimum 8 characters, include uppercase, lowercase, number, and symbol
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Confirm Password <span class="required">*</span>
                    </label>
                    <input type="password" name="confirm_password" id="confirmPassword" class="form-control" 
                           placeholder="••••••••" required minlength="8">
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">
                        Account Status <span class="required">*</span>
                    </label>
                    <select name="status" class="form-control" required>
                        <option value="active" selected>Active</option>
                        <option value="pending">Pending</option>
                        <option value="suspended">Suspended</option>
                        <option value="blocked">Blocked</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        KYC Status
                    </label>
                    <select name="kyc_status" class="form-control">
                        <option value="pending">Pending</option>
                        <option value="verified">Verified</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">
                    Currency
                </label>
                <select name="currency" class="form-control">
                    <?php
                    require_once __DIR__ . '/../../includes/currency.php';
                    $currencyHelper = new Currency();
                    $supportedCurrencies = $currencyHelper->getSupportedCurrencies();
                    $defaultCurrency = 'USD';
                    
                    foreach ($supportedCurrencies as $code => $name) {
                        $selected = ($code === $defaultCurrency) ? 'selected' : '';
                        echo "<option value=\"{$code}\" {$selected}>{$code} - {$name}</option>";
                    }
                    ?>
                </select>
                <div class="help-text">Default currency for this user's account</div>
            </div>

            <div style="display: flex; align-items: center; gap: 10px; margin-top: 15px;">
                <input type="hidden" name="two_factor_enabled" value="0">
                <input type="checkbox" name="two_factor_enabled" id="twoFactor" 
                       value="1" style="width: 20px; height: 20px;">
                <label for="twoFactor" class="form-label" style="margin-bottom: 0;">
                    Enable Two-Factor Authentication (2FA)
                </label>
            </div>
            <div class="help-text" style="margin-top: 6px;">
                Leave unchecked to create the account with 2FA off. You can change this later on the user Security page.
            </div>
        </div>

        <!-- Initial Deposit (Optional) -->
        <div class="form-section">
            <h3 class="section-title">Initial Account Setup (Optional)</h3>
            
            <div class="help-text" style="margin-bottom: 15px;">
                You can create initial bank accounts for this user here, or they can create them later.
            </div>

            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                <input type="checkbox" name="create_accounts" id="createAccounts" 
                       value="1" style="width: 20px; height: 20px;">
                <label for="createAccounts" class="form-label" style="margin-bottom: 0;">
                    Create initial bank account for this user
                </label>
            </div>

            <div id="accountsSection" style="display: none;">
                <div class="form-group">
                    <label class="form-label">
                        Account Type <span class="required">*</span>
                    </label>
                    <select name="account_type" id="accountType" class="form-control" required>
                        <option value="">Select Account Type</option>
                        <option value="checking">Checking Account</option>
                        <option value="savings">Savings Account</option>
                        <option value="business">Business Account</option>
                    </select>
                    <div class="help-text">Select the type of account to create for this user</div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Initial Balance ($)
                    </label>
                    <input type="number" name="account_balance" id="accountBalance" class="form-control" 
                           value="0" min="0" step="0.01">
                    <div class="help-text">Enter the initial balance for this account (default: $0)</div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <a href="<?php echo SITE_URL; ?>/admin/users" class="btn btn-secondary">
                <i class="fas fa-times"></i>
                Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-user-plus"></i>
                Create User
            </button>
        </div>
    </form>
</div>

<script>
// Password strength checker
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');
    
    let strength = 0;
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^a-zA-Z0-9]/.test(password)) strength++;
    
    strengthBar.className = 'password-strength-bar';
    if (strength === 0 || strength === 1) {
        strengthBar.classList.add('strength-weak');
        strengthText.textContent = 'Weak password';
        strengthText.style.color = '#ef4444';
    } else if (strength === 2 || strength === 3) {
        strengthBar.classList.add('strength-medium');
        strengthText.textContent = 'Medium strength password';
        strengthText.style.color = '#f59e0b';
    } else {
        strengthBar.classList.add('strength-strong');
        strengthText.textContent = 'Strong password';
        strengthText.style.color = '#10b981';
    }
});

// Password match validation
document.getElementById('confirmPassword').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirm = this.value;
    
    if (confirm && password !== confirm) {
        this.setCustomValidity('Passwords do not match');
    } else {
        this.setCustomValidity('');
    }
});

// Toggle accounts section
document.getElementById('createAccounts').addEventListener('change', function() {
    const accountsSection = document.getElementById('accountsSection');
    const accountType = document.getElementById('accountType');
    
    if (this.checked) {
        accountsSection.style.display = 'block';
        accountType.setAttribute('required', 'required');
    } else {
        accountsSection.style.display = 'none';
        accountType.removeAttribute('required');
        accountType.value = '';
    }
});

// Form validation
document.getElementById('createUserForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirm = document.getElementById('confirmPassword').value;
    
    if (password !== confirm) {
        e.preventDefault();
        showToast('Passwords do not match!', 'error');
        return false;
    }
    
    if (password.length < 8) {
        e.preventDefault();
        showToast('Password must be at least 8 characters long', 'error');
        return false;
    }
});
</script>


