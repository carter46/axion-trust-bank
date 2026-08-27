<?php 
// This page is included by AuthController::verify2fa(), so config and session are already set up
// Ensure branding variables are set
$siteName = isset($siteName) ? $siteName : (getSiteName() ?? 'SecureBank');
$siteInitials = isset($siteInitials) ? $siteInitials : (getSiteInitials() ?? 'SB');
$logoUrl = isset($logoUrl) ? $logoUrl : getSiteLogo();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?php echo htmlspecialchars($siteName); ?> — Two-Factor Authentication</title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/fontawesome/css/all.min.css">

  <style>
    :root{
      --navy-900: #041826;
      --navy-800: #073049;
      --teal-500: #0ea5a4;
      --muted: #f3f6f9;
      --card-bg: #ffffff;
      --accent: #0b6b8a;
      --radius: 14px;
      --glass: rgba(255,255,255,0.06);
      --max-width: 1200px;
      --transition: 180ms cubic-bezier(.2,.8,.2,1);
      font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, "Helvetica Neue", Arial;
    }

    html,body{
      height:100%;
      margin:0;
      background: linear-gradient(180deg,var(--navy-900), #042b3a 60%);
      color: #072033;
      -webkit-font-smoothing:antialiased;
      -moz-osx-font-smoothing:grayscale;
      overflow-x: hidden;
    }

    .login-root{
      min-height:100vh;
      display:flex;
      align-items:stretch;
      justify-content:center;
      padding:20px;
      box-sizing:border-box;
    }

    .login-wrap{
      width:100%;
      max-width:var(--max-width);
      min-height: calc(100vh - 40px);
      display:flex;
      border-radius:18px;
      overflow:hidden;
      box-shadow: 0 20px 60px rgba(3,18,26,0.45);
      background:linear-gradient(90deg, rgba(4,22,33,0.04), rgba(255,255,255,0.02));
    }

    .login-visual{
      flex: 0 0 70%;
      position:relative;
      display:block;
      min-height:100%;
      background: linear-gradient(180deg, rgba(2,28,42,0.9), rgba(3,20,30,0.9));
    }

    .visual-video{
      position:absolute;
      inset:0;
      overflow:hidden;
    }
    .visual-video video{
      width:100%;
      height:100%;
      object-fit:cover;
      transform:scale(1.05);
      filter: contrast(1.02) saturate(1.05) brightness(0.85);
    }

    .visual-overlay{
      position:absolute;
      inset:0;
      background: linear-gradient(90deg, rgba(4,20,30,0.6) 10%, rgba(4,20,30,0.3) 60%, rgba(4,20,30,0.6) 100%);
      display:flex;
      align-items:flex-end;
      padding:40px;
      box-sizing:border-box;
    }

    .visual-overlay .brand-message{
      color: #cce7ef;
      max-width:540px;
      line-height:1.45;
      font-weight:500;
    }
    .visual-overlay h2{
      margin:0 0 8px 0;
      font-size:28px;
      color:#e6f7fb;
      font-weight:700;
    }
    .visual-overlay p{ margin:0; opacity:0.9; }

    .login-form-wrap{
      flex:0 0 30%;
      background:var(--card-bg);
      display:flex;
      flex-direction:column;
      padding:28px;
      gap:18px;
      box-sizing:border-box;
      align-items:stretch;
      justify-content:flex-start;
      border-left:1px solid rgba(6,30,40,0.04);
      overflow-y: auto;
    }

    .form-card{
      width:100%;
      display:flex;
      flex-direction:column;
      gap:14px;
      flex: 1;
    }

    .brand {
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
    }
    .brand img{ height:40px; width:auto; display:block; }
    .brand h1{
      font-size:18px;
      margin:0;
      color:var(--navy-900);
      font-weight:700;
    }

    .brand a {
      display:flex;
      align-items:center;
      gap:12px;
      text-decoration:none;
    }

    .brand > div:last-child {
      font-size:13px;
      color:#6b8892;
      font-weight:600;
    }

    h2{
      margin:6px 0 0 0;
      font-size:20px;
      color:var(--navy-900);
      font-weight:700;
    }
    p{
      margin:6px 0 0 0;
      color:#6b8892;
      font-size:14px;
    }

    label{
      display:block;
      font-size:13px;
      font-weight:600;
      color:var(--navy-900);
      margin-bottom:6px;
    }

    .input{
      position:relative;
      display:flex;
      align-items:center;
    }
    .input input{
      width:100%;
      padding:12px 16px;
      border:1px solid #e0e7ef;
      border-radius:var(--radius);
      font-size:14px;
      transition:var(--transition);
      background:var(--card-bg);
      box-sizing:border-box;
    }
    .input input:focus{
      outline:none;
      border-color:var(--accent);
      box-shadow:0 0 0 3px rgba(11,107,138,0.1);
    }
    .input input::placeholder{ color:#9ca3af; }

    /* Code input styling */
    .input input.code-input {
      text-align:center;
      font-size:32px;
      font-weight:700;
      letter-spacing:12px;
      font-family:'Courier New', monospace;
      padding:16px;
      color:var(--navy-900);
    }

    button[type="submit"]{
      width:100%;
      padding:14px;
      background:linear-gradient(135deg,var(--navy-800),var(--navy-900));
      color:white;
      border:none;
      border-radius:var(--radius);
      font-size:15px;
      font-weight:600;
      cursor:pointer;
      transition:var(--transition);
      margin-top:8px;
    }
    button[type="submit"]:hover{
      transform:translateY(-1px);
      box-shadow:0 6px 20px rgba(4,24,38,0.3);
    }
    button[type="submit"]:active{ transform:translateY(0); }

    .error-message, .success-message {
      padding:12px 16px;
      border-radius:var(--radius);
      font-size:14px;
      margin-bottom:12px;
    }
    .error-message{
      background:#fee2e2;
      color:#991b1b;
      border:1px solid #fecaca;
    }
    .success-message{
      background:#d1fae5;
      color:#065f46;
      border:1px solid #a7f3d0;
    }

    .resend-section {
      text-align:center;
      padding:20px 0;
      border-top:1px solid #e0e7ef;
      margin-top:20px;
    }
    .resend-section p {
      margin:0 0 12px 0;
      color:#6b8892;
      font-size:13px;
    }
    .resend-btn {
      background:none;
      border:none;
      color:var(--accent);
      font-size:14px;
      font-weight:600;
      cursor:pointer;
      text-decoration:underline;
      padding:0;
    }
    .resend-btn:hover {
      color:var(--navy-800);
    }
    .resend-btn:disabled {
      opacity:0.5;
      cursor:not-allowed;
    }

    .back-link {
      display:inline-flex;
      align-items:center;
      gap:8px;
      color:var(--accent);
      text-decoration:none;
      font-size:14px;
      font-weight:600;
      margin-top:16px;
      transition:var(--transition);
    }
    .back-link:hover {
      color:var(--navy-800);
    }

    .help-link {
      text-align:center;
      margin-top:20px;
      padding-top:20px;
      border-top:1px solid #e0e7ef;
    }
    .help-link a {
      color:var(--accent);
      text-decoration:none;
      font-size:13px;
      font-weight:500;
    }
    .help-link a:hover {
      text-decoration:underline;
    }

    .footer-link {
      text-align:center;
      margin-top:auto;
      padding-top:20px;
      font-size:12px;
      color:#6b8892;
    }
    .footer-link a {
      color:var(--accent);
      text-decoration:none;
    }
    .footer-link a:hover {
      text-decoration:underline;
    }

    @media (max-width: 960px) {
      .login-wrap{
        flex-direction:column;
      }
      .login-visual{
        flex:0 0 40%;
        min-height:200px;
      }
      .login-form-wrap{
        flex:1;
      }
    }
    @media (max-width: 560px) {
      .login-root{
        padding:0;
      }
      .login-wrap{
        border-radius:0;
        min-height:100vh;
      }
      .login-visual{
        display:none;
      }
      .login-form-wrap{
        padding:24px;
      }
    }
  </style>
  <?php include __DIR__ . '/../../includes/translation.php'; ?>
</head>
<body>

  <div class="login-root">
    <div class="login-wrap" role="main" aria-label="Two-factor authentication">
      <!-- Left visual (video background) -->
      <div class="login-visual" aria-hidden="true">
        <div class="visual-video">
          <video autoplay muted loop playsinline poster="https://images.unsplash.com/photo-1601597111158-2fceff292cdc?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80">
            <source src="https://assets.mixkit.co/videos/preview/mixkit-modern-bank-lobby-1574-large.mp4" type="video/mp4">
          </video>
        </div>
        <div class="visual-overlay">
          <div>
            <h2>Secure Your Account</h2>
            <p class="brand-message">Your security is our priority. Enter the verification code sent to your email or phone to complete your login.</p>
          </div>
        </div>
      </div>

      <!-- Right form column -->
      <aside class="login-form-wrap" aria-label="2FA verification form">
        <div class="form-card" role="form">
          <!-- Logo + brand -->
          <div class="brand">
            <div style="display:flex;align-items:center;gap:12px;">
              <a href="<?php echo SITE_URL; ?>/" style="display:flex;align-items:center;gap:12px;text-decoration:none;">
                <?php if (!empty($logoUrl)): ?>
                  <img src="<?php echo htmlspecialchars($logoUrl); ?>" alt="<?php echo htmlspecialchars($siteName); ?> logo" style="height:40px;width:auto;display:block;object-fit:contain;">
                <?php else: ?>
                  <div style="height:40px;width:140px;background:linear-gradient(135deg,var(--navy-800),var(--navy-900));color:white;display:flex;align-items:center;justify-content:center;font-weight:700;border-radius:8px;font-size:14px;">
                    <?php echo htmlspecialchars($siteInitials); ?>
                  </div>
                <?php endif; ?>
              </a>
            </div>
            <div>Secure Access</div>
          </div>

          <!-- Error/Success Messages -->
          <?php if (isset($_SESSION['error'])): ?>
            <div class="error-message" role="alert">
              <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
          <?php endif; ?>
          
          <?php if (isset($_SESSION['success'])): ?>
            <div class="success-message" role="alert">
              <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            </div>
          <?php endif; ?>

          <!-- Page heading -->
          <div>
            <h2>Two-Factor Authentication</h2>
            <p>Enter the verification code sent to your email or phone</p>
          </div>

          <!-- Form -->
          <form method="POST" action="<?php echo SITE_URL; ?>/auth/verify-2fa">
            <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
            
            <div>
              <label for="code">Verification Code</label>
              <div class="input">
                <input id="code" name="code" type="text" class="code-input" 
                       placeholder="000000" maxlength="6" pattern="[0-9]{6}" 
                       inputmode="numeric" required autofocus autocomplete="one-time-code">
              </div>
            </div>

            <button type="submit">
              Verify Code
            </button>
          </form>
          
          <div class="resend-section">
            <p>Didn't receive the code?</p>
            <button type="button" class="resend-btn" id="resendBtn" onclick="resend2FACode()">
              Resend Code
            </button>
          </div>

          <a href="<?php echo SITE_URL; ?>/auth/login" class="back-link">
            ← Back to Sign In
          </a>

          <div class="footer-link">
            Need help? <a href="<?php echo SITE_URL; ?>/help-center">Visit the Help Center</a>
          </div>
        </div>
      </aside>
    </div>
  </div>

  <script>
    // Auto-focus code input
    document.getElementById('code').focus();

    // Format code input — single submit only (auto-submit can race with button click)
    const codeInput = document.getElementById('code');
    const verifyForm = codeInput.closest('form');
    let verifySubmitting = false;
    if (verifyForm) {
      verifyForm.addEventListener('submit', function (e) {
        if (verifySubmitting) {
          e.preventDefault();
          return false;
        }
        verifySubmitting = true;
        const btn = verifyForm.querySelector('button[type="submit"]');
        if (btn) {
          btn.disabled = true;
          btn.textContent = 'Verifying...';
        }
      });
    }
    codeInput.addEventListener('input', function(e) {
      this.value = this.value.replace(/\D/g, ''); // Only numbers
      if (this.value.length === 6 && !verifySubmitting && verifyForm) {
        verifyForm.requestSubmit ? verifyForm.requestSubmit() : verifyForm.submit();
      }
    });

    // Resend 2FA code
    function resend2FACode() {
      const btn = document.getElementById('resendBtn');
      btn.disabled = true;
      btn.textContent = 'Sending...';
      
      fetch('<?php echo SITE_URL; ?>/auth/resend-2fa', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({})
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Code resent successfully!');
          btn.textContent = 'Resend Code';
        } else {
          alert('Error: ' + (data.message || 'Failed to resend code'));
          btn.textContent = 'Resend Code';
        }
        btn.disabled = false;
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
        btn.textContent = 'Resend Code';
        btn.disabled = false;
      });
    }
  </script>

</body>
</html>
