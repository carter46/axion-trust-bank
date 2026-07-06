<?php 
$pageTitle = 'Reset Password - SecureBank';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

// Get dynamic site name for branding
$siteName = getSiteName() ?? 'SecureBank';

require_once __DIR__ . '/../../includes/security.php';
include __DIR__ . '/../../includes/auth-head.php';
?>

<div class="auth-container">
    <div class="auth-header">
        <a href="<?php echo SITE_URL; ?>/" class="logo">
            <i class="fas fa-university"></i>
            <?php echo htmlspecialchars($siteName); ?>
        </a>
        <h1><i class="fas fa-lock"></i> Reset Password</h1>
        <p>Enter your new password below</p>
    </div>
    
    <div class="auth-body">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></span>
                <button class="alert-close">&times;</button>
            </div>
        <?php endif; ?>

        <?php if (!isset($_GET['token']) || empty($_GET['token'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <span>Invalid or missing reset token. Please request a new password reset.</span>
            </div>
            <div class="auth-links">
                <a href="<?php echo SITE_URL; ?>/auth/forgot-password">
                    <i class="fas fa-key"></i> Request New Reset
                </a>
            </div>
        <?php else: ?>
            <form method="POST" action="<?php echo SITE_URL; ?>/auth/reset-password" data-validate>
                <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
                
                <div class="form-group">
                    <label class="form-label" for="password">New Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <div class="password-strength">
                        <div class="password-strength-bar" id="strength-bar"></div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="confirm_password">Confirm New Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Reset Password
                </button>
            </form>
            
            <div class="auth-links">
                <a href="<?php echo SITE_URL; ?>/auth/login">
                    <i class="fas fa-arrow-left"></i> Back to Sign In
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Password strength indicator
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const strengthBar = document.getElementById('strength-bar');
    
    let strength = 0;
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    
    strengthBar.className = 'password-strength-bar';
    if (strength <= 2) strengthBar.classList.add('strength-weak');
    else if (strength === 3) strengthBar.classList.add('strength-fair');
    else if (strength === 4) strengthBar.classList.add('strength-good');
    else if (strength === 5) strengthBar.classList.add('strength-strong');
});

// Auto-dismiss alerts
document.querySelectorAll('.alert-close').forEach(button => {
    button.addEventListener('click', function() {
        this.closest('.alert').remove();
    });
});
</script>

<?php include __DIR__ . '/../../includes/auth-foot.php'; ?>