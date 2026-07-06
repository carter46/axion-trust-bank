<?php
$pageTitle = 'Verification Link Expired - ' . getSiteName();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/security.php';

$siteName = getSiteName();
$email = isset($_GET['email']) ? Security::sanitize($_GET['email']) : '';
$reason = isset($_GET['reason']) ? Security::sanitize($_GET['reason']) : 'expired';
$headline = ($reason === 'used') ? 'Verification Link Already Used' : 'Verification Link Expired';
$subtext = ($reason === 'used')
    ? 'This verification link has already been used. If you still need access, request a new link below.'
    : 'This verification link has expired. Request a new verification email to continue.';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?php echo htmlspecialchars($siteName); ?> — <?php echo htmlspecialchars($headline); ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/fontawesome/css/all.min.css">
  <style>
    :root{
      --navy-900: #041826;
      --navy-800: #073049;
      --card-bg: #ffffff;
      font-family: 'Inter', system-ui, sans-serif;
    }
    html,body{
      height:100%;
      margin:0;
      background: linear-gradient(180deg,var(--navy-900), #042b3a 60%);
    }
    .wrap{
      min-height:100vh;
      display:flex;
      align-items:center;
      justify-content:center;
      padding:20px;
    }
    .card{
      background:var(--card-bg);
      border-radius:18px;
      padding:50px 40px;
      max-width:520px;
      width:100%;
      text-align:center;
      box-shadow: 0 20px 60px rgba(3,18,26,0.45);
    }
    .icon{
      width:90px;
      height:90px;
      border-radius:50%;
      background:linear-gradient(135deg,#f59e0b,#d97706);
      display:flex;
      align-items:center;
      justify-content:center;
      margin:0 auto 24px;
    }
    .icon i{ font-size:42px; color:white; }
    h1{ font-size:26px; color:var(--navy-900); margin:0 0 12px; }
    p{ color:#666; font-size:16px; line-height:1.6; margin:0 0 24px; }
    .btn-primary{
      display:inline-block;
      background:linear-gradient(180deg,var(--navy-800), var(--navy-900));
      color:white;
      padding:14px 28px;
      border-radius:10px;
      font-weight:600;
      border:none;
      cursor:pointer;
      font-size:15px;
    }
    .btn-primary:disabled{ opacity:0.6; cursor:not-allowed; }
    .btn-link{
      display:inline-block;
      margin-top:16px;
      color:#0b6b8a;
      text-decoration:none;
      font-size:14px;
    }
    .status-msg{ margin-top:16px; font-size:14px; min-height:20px; }
    .status-msg.success{ color:#059669; }
    .status-msg.error{ color:#dc2626; }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <div class="icon"><i class="fas fa-clock"></i></div>
      <h1><?php echo htmlspecialchars($headline); ?></h1>
      <p><?php echo htmlspecialchars($subtext); ?></p>
      <?php if ($email): ?>
      <button type="button" class="btn-primary" id="resendBtn" onclick="resendVerification()">
        Resend Verification Email
      </button>
      <div class="status-msg" id="statusMsg"></div>
      <?php else: ?>
      <p style="font-size:14px;color:#888;">Enter your email on the login page to request a new verification link.</p>
      <?php endif; ?>
      <a href="<?php echo SITE_URL; ?>/auth/login" class="btn-link">Back to Login</a>
    </div>
  </div>
  <?php if ($email): ?>
  <script>
    function resendVerification() {
      var btn = document.getElementById('resendBtn');
      var msg = document.getElementById('statusMsg');
      btn.disabled = true;
      btn.textContent = 'Sending...';
      msg.textContent = '';
      msg.className = 'status-msg';

      fetch('<?php echo SITE_URL; ?>/auth/resend-verification-email', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email: <?php echo json_encode($email); ?> })
      })
      .then(function(r) { return r.json(); })
      .then(function(data) {
        btn.disabled = false;
        btn.textContent = 'Resend Verification Email';
        if (data.success) {
          msg.textContent = data.message || 'Verification email sent. Please check your inbox.';
          msg.className = 'status-msg success';
        } else {
          msg.textContent = data.message || 'Could not send verification email.';
          msg.className = 'status-msg error';
        }
      })
      .catch(function() {
        btn.disabled = false;
        btn.textContent = 'Resend Verification Email';
        msg.textContent = 'Network error. Please try again.';
        msg.className = 'status-msg error';
      });
    }
  </script>
  <?php endif; ?>
</body>
</html>
