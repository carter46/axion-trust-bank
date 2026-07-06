<?php 
$pageTitle = 'Change Password - SecureBank';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

// Include head
include __DIR__ . '/../../includes/head.php';

// Include sidebar and main structure
include __DIR__ . '/../../includes/sidebar.php';
?>

<!-- ===== CHANGE PASSWORD PAGE CONTENT ===== -->

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

.card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 24px;
    padding: 30px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    color: #032B44;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    font-size: 16px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    transition: all 0.3s;
}

.form-control:focus {
    outline: none;
    border-color: #032B44;
    box-shadow: 0 0 0 3px rgba(3, 43, 68, 0.1);
}

.btn {
    display: inline-block;
    padding: 12px 24px;
    font-size: 16px;
    font-weight: 600;
    text-align: center;
    text-decoration: none;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.btn-primary {
    background: linear-gradient(135deg, #032B44 0%, #024a6b 100%);
    color: white;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #024a6b 0%, #032B44 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(3, 43, 68, 0.3);
}

@media (max-width: 768px) {
    .page-header h1 {
        font-size: 24px;
    }
    
    .card {
        padding: 20px;
    }
}
</style>

<div class="page-header">
    <h1>Change Password</h1>
    <p style="color: #666;">Update your account password</p>
</div>

<div class="card">
    <h3 style="color: #032B44; margin-bottom: 20px;">Password Settings</h3>
    <form method="POST">
        <div class="form-group">
            <label class="form-label">Current Password</label>
            <input type="password" class="form-control" name="current_password" required>
        </div>
        
        <div class="form-group">
            <label class="form-label">New Password</label>
            <input type="password" class="form-control" name="new_password" required>
        </div>
        
        <div class="form-group">
            <label class="form-label">Confirm New Password</label>
            <input type="password" class="form-control" name="confirm_password" required>
        </div>
        
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-key"></i> Update Password
        </button>
    </form>
</div>

<?php
include __DIR__ . '/../../includes/mobile-nav.php';
?>
