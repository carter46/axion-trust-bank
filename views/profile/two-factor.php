<?php 
$pageTitle = 'Two-Factor Authentication - SecureBank';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

// Include head
include __DIR__ . '/../../includes/head.php';

// Include sidebar and main structure
include __DIR__ . '/../../includes/sidebar.php';
?>

<!-- ===== TWO-FACTOR AUTH PAGE CONTENT ===== -->

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
    text-align: center;
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
    <h1>Two-Factor Authentication</h1>
    <p style="color: #666;">Secure your account with 2FA</p>
</div>

<div class="card">
    <i class="fas fa-shield-alt fa-3x" style="color: #032B44; margin-bottom: 20px;"></i>
    <h3>Enable Two-Factor Authentication</h3>
    <p style="color: #666; margin: 16px 0;">Add an extra layer of security to your account by enabling two-factor authentication.</p>
    <button class="btn btn-primary">
        <i class="fas fa-lock"></i> Enable 2FA
    </button>
</div>

<?php
include __DIR__ . '/../../includes/mobile-nav.php';
?>
