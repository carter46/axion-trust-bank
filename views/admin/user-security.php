<?php
$pageTitle = 'User Security Settings - Admin - SecureBank';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

// Get user data
$userId = $GLOBALS['id'];
$userModel = new User();
$user = $userModel->findById($userId);

if (!$user) {
    $_SESSION['error'] = 'User not found';
    redirect(getAdminUserListBackUrl());
}

requireDemoUserAdminAccess($user);

include __DIR__ . '/../../includes/head.php';
include __DIR__ . '/../../includes/admin-sidebar.php';
include __DIR__ . '/../../includes/admin-modals.php';
?>

<style>
.security-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 24px;
}

.header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 32px;
}

.back-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    color: #0f172a;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
    transition: all 0.2s;
}

.back-btn:hover {
    background: #f8fafc;
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.12);
}

.user-header {
    display: flex;
    align-items: center;
    gap: 16px;
}

.avatar {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 20px;
}

.user-info h1 {
    font-size: 24px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}

.user-info .email {
    color: #64748b;
    font-size: 14px;
    margin-top: 4px;
}

.security-grid {
    display: grid;
    gap: 24px;
}

.security-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.security-card h3 {
    font-size: 18px;
    font-weight: 600;
    color: #0f172a;
    margin: 0 0 16px 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.security-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: #1e293b;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
    font-size: 14px;
}

.form-input {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s;
}

.form-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 14px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-primary {
    background: #3b82f6;
    color: white;
}

.btn-primary:hover {
    background: #2563eb;
    transform: translateY(-1px);
}

.btn-success {
    background: #10b981;
    color: white;
}

.btn-success:hover {
    background: #059669;
    transform: translateY(-1px);
}

.btn-danger {
    background: #ef4444;
    color: white;
}

.btn-danger:hover {
    background: #dc2626;
    transform: translateY(-1px);
}

.btn-warning {
    background: #f59e0b;
    color: white;
}

.btn-warning:hover {
    background: #d97706;
    transform: translateY(-1px);
}

.toggle-switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 34px;
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .toggle-slider {
    background-color: #10b981;
}

input:checked + .toggle-slider:before {
    transform: translateX(26px);
}

.toggle-label {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
}

.toggle-text {
    font-weight: 600;
    color: #374151;
}

.status-info {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 20px;
}

.status-info h4 {
    margin: 0 0 8px 0;
    color: #374151;
    font-size: 14px;
    font-weight: 600;
}

.status-info p {
    margin: 0;
    color: #6b7280;
    font-size: 13px;
    line-height: 1.5;
}

@media (max-width: 768px) {
    .security-container {
        padding: 16px;
    }
    
    .header {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
    
    .user-header {
        width: 100%;
    }
}
</style>

<div class="security-container">
    <!-- Header -->
    <div class="header">
        <a href="/admin/user/<?php echo $userId; ?>" class="back-btn">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                <path d="M15 6l-6 6 6 6" stroke="currentColor" stroke-width="2"/>
            </svg>
            Back to User
        </a>
        
        <div class="user-header">
            <div class="avatar"><?php echo strtoupper(substr($user['full_name'], 0, 2)); ?></div>
            <div class="user-info">
                <h1>Security Settings</h1>
                <div class="email"><?php echo htmlspecialchars($user['email']); ?></div>
            </div>
        </div>
    </div>

    <div class="security-grid">
        <!-- Password Management -->
        <div class="security-card">
            <h3>
                <div class="security-icon">🔑</div>
                Password Management
            </h3>
            
            <div class="status-info">
                <h4>Set New Password</h4>
                <p>Enter a new password for this user. They will be required to use this password on their next login.</p>
            </div>
            
            <form id="passwordForm" onsubmit="updatePassword(event)">
                <div class="form-group">
                    <label class="form-label" for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" class="form-input" 
                           placeholder="Enter new password" required minlength="8">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-input" 
                           placeholder="Confirm new password" required minlength="8">
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    Update Password
                </button>
            </form>
        </div>

        <!-- Two-Factor Authentication -->
        <div class="security-card">
            <h3>
                <div class="security-icon">🛡️</div>
                Two-Factor Authentication
            </h3>
            
            <div class="status-info">
                <h4>Current Status</h4>
                <p>2FA provides an extra layer of security by requiring a second verification step.</p>
            </div>
            
            <div class="toggle-label">
                <label class="toggle-switch">
                    <input type="checkbox" id="twoFactorToggle" <?php echo $user['two_factor_enabled'] ? 'checked' : ''; ?> 
                           onchange="toggle2FA()">
                    <span class="toggle-slider"></span>
                </label>
                <span class="toggle-text" id="twoFactorStatus">
                    <?php echo $user['two_factor_enabled'] ? 'Two-Factor Authentication is ENABLED' : 'Two-Factor Authentication is DISABLED'; ?>
                </span>
            </div>
        </div>

        <!-- Transaction PINs -->
        <div class="security-card">
            <h3>
                <div class="security-icon">🔢</div>
                Transaction PINs
            </h3>
            
            <div class="status-info">
                <h4>Login PIN</h4>
                <p>PIN required for account login access.</p>
            </div>
            
            <form id="loginPinForm" onsubmit="updateLoginPin(event)">
                <div class="form-group">
                    <label class="form-label" for="new_login_pin">New Login PIN</label>
                    <input type="password" id="new_login_pin" name="new_login_pin" class="form-input" 
                           placeholder="Enter 4-digit PIN" pattern="[0-9]{4}" maxlength="4" required>
                </div>
                
                <button type="submit" class="btn btn-warning">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    Update Login PIN
                </button>
            </form>
            
            <hr style="margin: 24px 0; border: none; border-top: 1px solid #e5e7eb;">
            
            <div class="status-info">
                <h4>Transfer PIN</h4>
                <p>PIN required for all financial transactions and transfers.</p>
            </div>
            
            <form id="transferPinForm" onsubmit="updateTransferPin(event)">
                <div class="form-group">
                    <label class="form-label" for="new_transfer_pin">New Transfer PIN</label>
                    <input type="password" id="new_transfer_pin" name="new_transfer_pin" class="form-input" 
                           placeholder="Enter 4-digit PIN" pattern="[0-9]{4}" maxlength="4" required>
                </div>
                
                <button type="submit" class="btn btn-warning">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    Update Transfer PIN
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function updatePassword(event) {
    event.preventDefault();
    
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (newPassword !== confirmPassword) {
        showToast('Passwords do not match', 'error');
        return;
    }
    
    if (newPassword.length < 8) {
        showToast('Password must be at least 8 characters long', 'error');
        return;
    }
    
    const userId = <?php echo $userId; ?>;
    
    fetch('/api/admin-reset-user-password.php', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            user_id: userId,
            new_password: newPassword
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Password updated successfully', 'success');
            document.getElementById('passwordForm').reset();
        } else {
            showToast('Error: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred while updating password', 'error');
    });
}

function toggle2FA() {
    const isEnabled = document.getElementById('twoFactorToggle').checked;
    const statusText = document.getElementById('twoFactorStatus');
    const userId = <?php echo $userId; ?>;
    
    fetch('/api/toggle-2fa.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            user_id: userId,
            enabled: isEnabled
        })
    })
    .then(response => {
        // Check if response is actually JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
          return response.text().then(text => {
            console.error('Non-JSON response:', text);
            throw new Error('Invalid response format. Server may have returned an error page.');
          });
        }
        return response.json();
      })
      .then(data => {
        if (data && data.success) {
            statusText.textContent = isEnabled ? 'Two-Factor Authentication is ENABLED' : 'Two-Factor Authentication is DISABLED';
            if (typeof showToast === 'function') {
              showToast('2FA ' + (isEnabled ? 'enabled' : 'disabled') + ' successfully', 'success');
            } else {
              alert('2FA ' + (isEnabled ? 'enabled' : 'disabled') + ' successfully');
            }
        } else {
            // Revert toggle if failed
            document.getElementById('twoFactorToggle').checked = !isEnabled;
            const errorMsg = data && data.message ? data.message : 'Unknown error';
            if (typeof showToast === 'function') {
              showToast('Error: ' + errorMsg, 'error');
            } else {
              alert('Error: ' + errorMsg);
            }
        }
      })
      .catch(error => {
        console.error('Error:', error);
        document.getElementById('twoFactorToggle').checked = !isEnabled;
        const errorMsg = error.message || 'An error occurred while toggling 2FA';
        if (typeof showToast === 'function') {
          showToast(errorMsg, 'error');
        } else {
          alert(errorMsg);
        }
      });
}

function updateLoginPin(event) {
    event.preventDefault();
    
    const newPin = document.getElementById('new_login_pin').value;
    
    if (!/^\d{4}$/.test(newPin)) {
        showToast('Login PIN must be exactly 4 digits', 'error');
        return;
    }
    
    const userId = <?php echo $userId; ?>;
    
    fetch('/api/admin-update-login-pin.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            user_id: userId,
            new_pin: newPin
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Login PIN updated successfully', 'success');
            document.getElementById('loginPinForm').reset();
        } else {
            showToast('Error: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred while updating login PIN', 'error');
    });
}

function updateTransferPin(event) {
    event.preventDefault();
    
    const newPin = document.getElementById('new_transfer_pin').value;
    
    if (!/^\d{4}$/.test(newPin)) {
        showToast('Transfer PIN must be exactly 4 digits', 'error');
        return;
    }
    
    const userId = <?php echo $userId; ?>;
    
    fetch('/api/admin-update-transfer-pin.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            user_id: userId,
            new_pin: newPin
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Transfer PIN updated successfully', 'success');
            document.getElementById('transferPinForm').reset();
        } else {
            showToast('Error: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred while updating transfer PIN', 'error');
    });
}
</script>