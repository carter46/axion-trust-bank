<?php 
$pageTitle = 'Sign In - ' . getSiteName();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/security.php';

// Get site branding
$siteName = getSiteName();
$siteInitials = getSiteInitials();
$logoUrl = getSiteLogo(); // Use same function as header

// Check if user is already logged in
if (isLoggedIn()) {
    redirect('/dashboard');
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?php echo htmlspecialchars($siteName); ?> — Sign In</title>

  <!-- Replace with your preferred web font if desired -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Font Awesome Icons -->
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

    /* Page container */
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

    /* Left: video column (70%) */
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

    /* subtle gradient overlay for better contrast */
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

    /* Right: form column (30%) */
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
    }

    /* form card container */
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
      gap:12px;
    }
    .brand img{ height:40px; width:auto; display:block; }
    .brand h1{
      font-size:18px;
      margin:0;
      color:var(--navy-900);
      font-weight:700;
    }

    /* Tabs for Password / Quick PIN */
    .auth-tabs{
      display:grid;
      grid-template-columns: 1fr 1fr;
      gap:12px;
      margin-top:6px;
    }
    .tab-btn{
      padding:12px 12px;
      border-radius:10px;
      border:1px solid rgba(6,30,40,0.06);
      background:transparent;
      text-align:center;
      cursor:pointer;
      transition:var(--transition);
      font-weight:600;
      color:var(--navy-900);
      font-size: 14px;
    }
    .tab-btn.active{
      background: linear-gradient(180deg,var(--navy-800), var(--navy-900));
      color: #fff;
      box-shadow: 0 6px 18px rgba(5,30,45,0.14);
      transform:translateY(-2px);
    }

    label{
      display:block;
      font-size:14px;
      color:#133240;
      margin-bottom:6px;
      font-weight:600;
    }

    .input{
      display:flex;
      align-items:center;
      gap:10px;
      background:#f6f9fb;
      border-radius:10px;
      padding:12px 14px;
      border:1px solid rgba(10,30,50,0.04);
    }
    .input input{
      border:0;
      outline:0;
      background:transparent;
      width:100%;
      font-size:16px; /* Prevents zoom on iOS */
      color:#072033;
    }

    .input .eye-btn{
      background:transparent;
      border:0;
      cursor:pointer;
      padding:6px;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      color:#09435a;
      font-size: 18px;
    }

    .row{
      display:flex;
      align-items:center;
      justify-content:space-between;
    }

    .remember{
      display:flex;
      align-items:center;
      gap:8px;
      font-size:14px;
      color:#12343e;
    }

    .btn-primary{
      margin-top:8px;
      width:100%;
      border:0;
      background: linear-gradient(180deg,var(--navy-800), var(--navy-900));
      color:white;
      padding:14px;
      border-radius:10px;
      font-weight:700;
      cursor:pointer;
      letter-spacing:0.2px;
      box-shadow: 0 10px 26px rgba(3,18,26,0.22);
      transition: transform 160ms ease, box-shadow 160ms ease;
      font-size: 16px;
    }
    .btn-primary:active{ transform: translateY(1px); }
    .links{
      display:flex;
      gap:12px;
      justify-content:center;
      padding-top:8px;
      border-top:1px solid rgba(6,30,40,0.04);
      margin-top:6px;
    }
    .links a{
      color:var(--accent);
      font-weight:600;
      text-decoration:none;
      font-size:14px;
    }

    .small-muted{
      color:#6c8090;
      font-size:13px;
      text-align:center;
      padding-top:6px;
    }

    /* Error/Success Messages */
    .error-message{
      color: #b91c1c;
      font-weight:600;
      font-size:13px;
      margin-top:6px;
      text-align:left;
      padding: 10px 12px;
      background: rgba(185, 28, 28, 0.1);
      border-radius: 8px;
      border: 1px solid rgba(185, 28, 28, 0.2);
    }
    
    .success-message{
      color: #059669;
      font-weight:600;
      font-size:13px;
      margin-top:6px;
      text-align:left;
      padding: 10px 12px;
      background: rgba(5, 150, 105, 0.1);
      border-radius: 8px;
      border: 1px solid rgba(5, 150, 105, 0.2);
    }

    /* Responsive behavior */
    @media (max-width: 1024px){
      .login-wrap{ 
        flex-direction: column; 
        height: auto; 
        min-height: auto;
        max-height: none; 
        border-radius: 12px; 
        overflow: visible; 
        box-shadow: none; 
        background: transparent; 
      }
      
      .login-visual{ 
        order: 2; 
        flex: 0 0 auto; 
        height: 300px; 
        border-radius: 12px 12px 0 0; 
        min-height: 300px;
      }
      
      .login-form-wrap{ 
        order: 1; 
        flex: 0 0 auto; 
        width: 100%; 
        padding: 24px; 
        border-left: 0; 
        border-radius: 0 0 12px 12px; 
        margin-top: -60px; 
        background: rgba(255,255,255,0.98); 
        box-shadow: 0 12px 36px rgba(6,30,40,0.12); 
        position: relative;
        z-index: 2;
      }
      
      .visual-overlay {
        padding: 24px;
      }
      
      .visual-overlay h2 {
        font-size: 24px;
      }
      
      .visual-overlay .brand-message {
        font-size: 14px;
      }
    }

    @media (max-width: 768px){
      .login-root {
        padding: 16px;
      }
      
      .login-visual{ 
        height: 250px;
        min-height: 250px;
      }
      
      .login-form-wrap{ 
        margin-top: -40px;
        padding: 20px;
      }
      
      .visual-overlay {
        padding: 20px;
      }
      
      .visual-overlay h2 {
        font-size: 22px;
      }
      
      .form-card {
        gap: 16px;
      }
      
      /* 2 columns on mobile for tabs */
      .auth-tabs{
        grid-template-columns: 1fr 1fr;
        gap: 8px;
      }
    }

    @media (max-width: 560px){
      .login-root {
        padding: 12px;
      }
      
      .login-wrap {
        border-radius: 12px;
      }
      
      .login-visual{ 
        height: 200px;
        min-height: 200px;
        border-radius: 12px 12px 0 0;
      }
      
      .login-form-wrap{ 
        margin: 0;
        border-radius: 0 0 12px 12px;
        padding: 20px 16px;
        margin-top: -30px;
      }
      
      .visual-overlay{ 
        padding: 16px;
      }
      
      .visual-overlay h2 {
        font-size: 20px;
        margin-bottom: 6px;
      }
      
      .visual-overlay .brand-message {
        font-size: 13px;
      }
      
      .brand {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
      }
      
      .auth-tabs {
        grid-template-columns: 1fr 1fr;
        gap: 8px;
      }
      
      .tab-btn {
        padding: 14px 12px;
      }
      
      .input {
        padding: 14px 12px;
      }
      
      .btn-primary {
        padding: 16px;
      }
      
      .row {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
      }
      
      .links {
        flex-direction: column;
        gap: 10px;
        text-align: center;
      }
    }

    @media (max-width: 380px){
      .login-visual{ 
        height: 180px;
        min-height: 180px;
      }
      
      .login-form-wrap{ 
        margin-top: -20px;
      }
      
      .visual-overlay h2 {
        font-size: 18px;
      }
      
      .visual-overlay .brand-message {
        font-size: 12px;
      }
    }

    /* small UX polish */
    .card-note{ font-size:13px; color:#446168; text-align:center; margin-top:6px; }
    
    /* Focus styles for accessibility */
    .tab-btn:focus,
    .input input:focus,
    .btn-primary:focus {
      outline: 2px solid var(--accent);
      outline-offset: 2px;
    }

    /* Login Loading Animation - Exact copy from reference */
    .page-loading {
      position: fixed;
      top: 0;
      right: 0;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 100%;
      transition: all .4s .2s ease-in-out;
      background-color: #ffffff;
      visibility: hidden;
      z-index: 99999;
    }
    .page-loading.active {
      opacity: 1;
      visibility: visible;
    }
    .page-loading-inner {
      position: absolute;
      top: 50%;
      left: 0;
      width: 100%;
      text-align: center;
      transform: translateY(-50%);
      transition: opacity .2s ease-in-out;
      opacity: 0;
    }
    .page-loading.active>.page-loading-inner {
      opacity: 1;
    }
    
    .loading-container {
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
    }
    
    .loading-animation {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 80px;
      height: 80px;
      margin-bottom: 1rem;
      position: relative;
    }
    
    .loading-animation .circle {
      position: absolute;
      width: 100%;
      height: 100%;
      border-radius: 50%;
      border: 4px solid transparent;
      mix-blend-mode: overlay;
      animation: rotateCircle 1.5s linear infinite;
    }
    
    .loading-animation .circle:nth-child(1) {
      border-top-color: var(--navy-800);
      animation-delay: 0s;
    }
    
    .loading-animation .circle:nth-child(2) {
      border-right-color: var(--navy-800);
      animation-delay: 0.2s;
    }
    
    .loading-animation .circle:nth-child(3) {
      border-bottom-color: var(--navy-900);
      animation-delay: 0.4s;
    }
    
    .loading-animation .circle:nth-child(4) {
      border-left-color: var(--navy-800);
      animation-delay: 0.6s;
    }
    
    .loading-animation .core {
      width: 20px;
      height: 20px;
      border-radius: 50%;
      background: linear-gradient(45deg, var(--navy-800), var(--navy-900));
      box-shadow: 0 0 15px rgba(7, 48, 73, 0.5);
      animation: pulse 1s ease-in-out infinite alternate;
    }
    
    .page-loading .text {
      color: var(--navy-900);
      font-weight: 500;
      letter-spacing: 0.05em;
      margin-top: 0.5rem;
      font-size: 0.875rem;
      background: linear-gradient(90deg, var(--navy-900), var(--navy-800), var(--navy-900));
      background-size: 200% auto;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      animation: gradient 2s linear infinite;
    }
    
    @keyframes rotateCircle {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    
    @keyframes pulse {
      from { transform: scale(0.8); opacity: 0.8; }
      to { transform: scale(1.2); opacity: 1; }
    }
    
    @keyframes gradient {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }
  </style>
  <?php include __DIR__ . '/../../includes/translation.php'; ?>
</head>
<body>

  <div class="login-root">
    <div class="login-wrap" role="main" aria-label="<?php echo htmlspecialchars($siteName); ?> login">

      <!-- Left visual (video background) -->
      <div class="login-visual" aria-hidden="true">
        <div class="visual-video" id="visualVideo">
          <!-- Using a placeholder video - you can replace with your YouTube video -->
          <video id="bgVideo" autoplay muted loop playsinline poster="https://images.unsplash.com/photo-1601597111158-2fceff292cdc?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80">
            <source src="https://assets.mixkit.co/videos/preview/mixkit-modern-bank-lobby-1574-large.mp4" type="video/mp4">
            <!-- If browser can't play, fallback image or color will show -->
          </video>
        </div>

        <div class="visual-overlay">
          <div>
            <h2>Bank Globally. Invest Confidently.</h2>
            <p class="brand-message">Secure offshore accounts, multi-asset investing, and 24/7 digital banking — crafted for people and businesses operating across borders.</p>
          </div>
        </div>
      </div>

      <!-- Right form column -->
      <aside class="login-form-wrap" aria-label="Sign in form">
        <div class="form-card" role="form" aria-labelledby="loginHeading">
          <!-- Logo + brand -->
          <div class="brand" style="justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:12px;">
              <!-- Site Logo - Clickable link to homepage -->
              <a href="<?php echo SITE_URL; ?>/" style="display:flex;align-items:center;gap:12px;text-decoration:none;">
                <?php if (!empty($logoUrl)): ?>
                  <img src="<?php echo htmlspecialchars($logoUrl); ?>" alt="<?php echo htmlspecialchars($siteName); ?> logo" id="siteLogo" style="height:40px;width:auto;display:block;object-fit:contain;cursor:pointer;">
                <?php else: ?>
                  <div style="height:40px;width:140px;background:linear-gradient(135deg,var(--navy-800),var(--navy-900));color:white;display:flex;align-items:center;justify-content:center;font-weight:700;border-radius:8px;font-size:14px;cursor:pointer;">
                    <?php echo htmlspecialchars($siteInitials); ?>
                  </div>
                <?php endif; ?>
              </a>
              <div style="display:none;">
                <h1><?php echo htmlspecialchars($siteName); ?></h1>
              </div>
            </div>

            <div style="font-size:13px;color:#6b8892;font-weight:600;">Secure Access</div>
          </div>

          <!-- Error/Success Messages -->
          <?php if (!empty($_GET['timeout'])): ?>
            <div class="error-message" role="alert">
              Your session expired due to inactivity. Please log in again.
            </div>
          <?php endif; ?>
          <?php if (isset($_SESSION['error'])): ?>
            <div class="error-message" role="alert">
              <?php 
              $errorMsg = $_SESSION['error'];
              echo htmlspecialchars($errorMsg);
              $unverifiedEmail = $_SESSION['unverified_email'] ?? '';
              unset($_SESSION['error']);
              unset($_SESSION['unverified_email']);
              
              // Check if this is an email verification error
              if ((strpos($errorMsg, 'verify your email') !== false || strpos($errorMsg, 'email address') !== false) && !empty($unverifiedEmail)) {
                  echo '<div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid rgba(185, 28, 28, 0.2);">';
                  echo '<p style="margin: 0 0 8px 0; font-size: 13px; color: #991b1b;">Didn\'t receive the verification email?</p>';
                  echo '<button type="button" onclick="resendVerificationEmail(\'' . htmlspecialchars($unverifiedEmail) . '\')" id="resendVerificationBtn" style="background: #dc2626; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 13px; width: 100%; transition: all 0.2s;">Resend Verification Email</button>';
                  echo '</div>';
              }
              ?>
            </div>
          <?php endif; ?>
          
          <?php if (isset($_SESSION['success'])): ?>
            <div class="success-message" role="alert">
              <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            </div>
          <?php endif; ?>

          <!-- Page heading -->
          <div>
            <h2 id="loginHeading" style="margin:6px 0 0 0; font-size:20px; color:var(--navy-900);">Welcome Back</h2>
            <p style="margin:6px 0 0 0; color:#6b8892; font-size:14px;">Sign in to your secure banking account</p>
          </div>

          <!-- Tabs -->
          <div class="auth-tabs" role="tablist" aria-label="Authentication Methods" style="display:none;">
            <button class="tab-btn active" id="tabPassword" role="tab" aria-selected="true" aria-controls="panelPassword">Password</button>
          </div>

          <!-- FORM: Password -->
          <form method="POST" action="<?php echo SITE_URL; ?>/auth/login" id="passwordForm">
            <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
            <input type="hidden" name="login_method" value="password">
            
            <div id="panelPassword" role="tabpanel" aria-labelledby="tabPassword">
              <div>
                <label for="email">Email Address</label>
                <div class="input">
                  <input id="email" name="email" type="email" placeholder="you@domain.com" autocomplete="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
              </div>

              <div>
                <label for="password">Password</label>
                <div class="input">
                  <input id="password" name="password" type="password" placeholder="Enter your password" autocomplete="current-password" required>
                  <button type="button" class="eye-btn" id="togglePwd" aria-label="Show password" title="Show/Hide password">👁️</button>
                </div>
              </div>

              <div class="row" style="margin-top:6px;">
                <label class="remember"><input type="checkbox" id="remember" name="remember" value="1"> Remember me</label>
                <a href="<?php echo SITE_URL; ?>/auth/forgot-password" style="color:var(--accent); font-weight:700; text-decoration:none; font-size:14px;">Forgot Password?</a>
              </div>

              <div>
                <button type="submit" class="btn-primary" id="signInBtn">Sign In</button>
              </div>

              <div class="card-note">By signing in, you agree to our <a href="<?php echo SITE_URL; ?>/terms" style="color:var(--accent);text-decoration:underline;">Terms of Service</a> and <a href="<?php echo SITE_URL; ?>/terms" style="color:var(--accent);text-decoration:underline;">Privacy Policy</a>.</div>
            </div>
          </form>

          <!-- bottom links -->
          <div class="links" style="margin-top:auto;">
            <a href="<?php echo SITE_URL; ?>/auth/forgot-password" id="forgotMobile">Forgot Password?</a>
            <a href="<?php echo SITE_URL; ?>/auth/register" id="createAcc">Create Account</a>
          </div>

          <div class="small-muted">Need help? <a href="<?php echo SITE_URL; ?>/help-center" style="color:var(--accent);text-decoration:underline;">Visit the Help Center</a></div>

        </div>
      </aside>

    </div>
  </div>

  <!-- Login Loading Overlay - Exact structure from reference -->
  <div class="page-loading" id="loginLoader">
    <div class="page-loading-inner">
      <div class="loading-container">
        <div class="loading-animation">
          <div class="circle"></div>
          <div class="circle"></div>
          <div class="circle"></div>
          <div class="circle"></div>
          <div class="core"></div>
        </div>
        <div class="text"><?php echo htmlspecialchars($siteName); ?></div>
      </div>
    </div>
  </div>

  <script>
    // ===============================
    // Replace the video src dynamically if needed
    // ===============================
    (function(){
      // If you want to set the video URL via JS, set VIDEO_URL here:
      const VIDEO_URL = ""; // e.g. "https://cdn.example.com/bank-hall.mp4"
      if(VIDEO_URL){
        const videoEl = document.querySelector('#bgVideo source');
        if(videoEl){
          videoEl.src = VIDEO_URL;
          document.querySelector('#bgVideo').load();
        }
      }
    })();

    // ===============================
    // Password visibility (password-only login)
    // ===============================
    const passwordForm = document.getElementById('passwordForm');

    // ===============================
    // Toggle password visibility
    // ===============================
    const togglePwd = document.getElementById('togglePwd');
    const pwdInput = document.getElementById('password');
    if (togglePwd && pwdInput) {
      togglePwd.addEventListener('click', (e)=>{
        e.preventDefault();
        if(pwdInput.type === 'password'){ 
          pwdInput.type = 'text'; 
          togglePwd.textContent = '🙈'; 
          togglePwd.setAttribute('aria-label','Hide password'); 
        }
        else { 
          pwdInput.type = 'password'; 
          togglePwd.textContent = '👁️'; 
          togglePwd.setAttribute('aria-label','Show password'); 
        }
      });
    }

    // ===============================
    // Login Loading Animation - Exact from reference
    // ===============================
    const loginLoader = document.getElementById('loginLoader');
    
    function showLoginLoader() {
      if (loginLoader) {
        loginLoader.classList.add('active');
      }
    }

    function hideLoginLoader() {
      if (loginLoader) {
        loginLoader.classList.remove('active');
      }
    }

    // ===============================
    // Form submissions with loading delay
    // ===============================
    const signInBtn = document.getElementById('signInBtn');
    if (signInBtn) {
      signInBtn.addEventListener('click', (e)=>{
        const email = document.getElementById('email').value.trim();
        if (email) {
          localStorage.setItem('lastLoginEmail', email);
        }
      });
    }

    // Store email when password form is submitted and show loader
    if (passwordForm) {
      passwordForm.addEventListener('submit', function(e) {
        const email = document.getElementById('email').value.trim();
        if (email) {
          localStorage.setItem('lastLoginEmail', email);
        }
        
        // Show loading animation immediately
        showLoginLoader();
        
        // Allow form to submit normally - don't prevent default
        // The loading animation will remain until page redirects
        // Don't use fetch as it doesn't handle PHP redirects properly
      });
    }

    // Accessibility: allow Enter key to submit in inputs
    document.querySelectorAll('.input input').forEach(inp=>{
      inp.addEventListener('keydown', (ev)=>{
        if(ev.key === 'Enter'){
          if(signInBtn) signInBtn.click();
        }
      });
    });

    // Prevent autoplay issues: attempt to play video if paused
    window.addEventListener('load', ()=>{
      const video = document.getElementById('bgVideo');
      if(video && video.paused){
        const playPromise = video.play();
        if (playPromise !== undefined) {
          playPromise.catch(()=>{ /* autoplay blocked; it's fine */ });
        }
      }
    });

    // Email verification celebration overlay
    <?php if (isset($_GET['email_verified']) && $_GET['email_verified'] == '1'): ?>
    (function() {
      // Create celebration overlay
      const overlay = document.createElement('div');
      overlay.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.8);z-index:10000;display:flex;align-items:center;justify-content:center;animation:fadeIn 0.3s;';
      
      const content = document.createElement('div');
      content.style.cssText = 'background:white;border-radius:20px;padding:50px 40px;text-align:center;max-width:500px;margin:20px;box-shadow:0 20px 60px rgba(0,0,0,0.3);animation:slideUp 0.5s ease-out;';
      
      // Celebration icon with animation
      const icon = document.createElement('div');
      icon.style.cssText = 'width:100px;height:100px;border-radius:50%;background:linear-gradient(135deg,#10b981,#059669);display:flex;align-items:center;justify-content:center;margin:0 auto 25px;animation:scaleIn 0.6s ease-out;';
      icon.innerHTML = '<i class="fas fa-check" style="font-size:50px;color:white;"></i>';
      
      // Confetti animation
      for(let i=0;i<50;i++){
        const confetti = document.createElement('div');
        confetti.style.cssText = `position:absolute;width:10px;height:10px;background:hsl(${Math.random()*360},70%,60%);border-radius:50%;left:${Math.random()*100}%;top:-10px;animation:confettiFall ${2+Math.random()*2}s linear forwards;animation-delay:${Math.random()*0.5}s;`;
        overlay.appendChild(confetti);
      }
      
      const title = document.createElement('h2');
      title.style.cssText = 'font-size:28px;color:#041826;margin:0 0 12px 0;font-weight:700;';
      title.textContent = 'Email Verified!';
      
      const message = document.createElement('p');
      message.style.cssText = 'font-size:16px;color:#666;margin:0;line-height:1.6;';
      message.textContent = 'Your email has been successfully verified. You can now log in to your account.';
      
      content.appendChild(icon);
      content.appendChild(title);
      content.appendChild(message);
      overlay.appendChild(content);
      document.body.appendChild(overlay);
      
      // Add CSS animations
      const style = document.createElement('style');
      style.textContent = `
        @keyframes fadeIn{from{opacity:0;}to{opacity:1;}}
        @keyframes slideUp{from{transform:translateY(30px);opacity:0;}to{transform:translateY(0);opacity:1;}}
        @keyframes scaleIn{from{transform:scale(0);opacity:0;}to{transform:scale(1);opacity:1;}}
        @keyframes confettiFall{to{transform:translateY(100vh) rotate(360deg);opacity:0;}}
      `;
      document.head.appendChild(style);
      
      // Auto-close after 2 seconds
      setTimeout(() => {
        overlay.style.animation = 'fadeOut 0.3s';
        setTimeout(() => {
          overlay.remove();
          // Remove email_verified parameter from URL
          const url = new URL(window.location.href);
          url.searchParams.delete('email_verified');
          window.history.replaceState({}, '', url);
        }, 300);
      }, 2000);
    })();
    <?php endif; ?>
    
    // Resend verification email function
    function resendVerificationEmail(email) {
        if (!email) {
            alert('Email address is required');
            return;
        }
        
        const btn = document.getElementById('resendVerificationBtn');
        if (!btn) return;
        
        const originalText = btn.textContent;
        btn.disabled = true;
        btn.textContent = 'Sending...';
        btn.style.opacity = '0.7';
        btn.style.cursor = 'not-allowed';
        
        fetch('<?php echo SITE_URL; ?>/auth/resend-verification-email', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ email: email })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                btn.textContent = 'Email Sent ✓';
                btn.style.background = '#059669';
                setTimeout(() => {
                    btn.textContent = originalText;
                    btn.style.background = '#dc2626';
                    btn.disabled = false;
                    btn.style.opacity = '1';
                    btn.style.cursor = 'pointer';
                }, 3000);
            } else {
                alert('Error: ' + (data.message || 'Failed to resend email'));
                btn.textContent = originalText;
                btn.disabled = false;
                btn.style.opacity = '1';
                btn.style.cursor = 'pointer';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
            btn.textContent = originalText;
            btn.disabled = false;
            btn.style.opacity = '1';
            btn.style.cursor = 'pointer';
        });
    }
  </script>
  <style>
    @keyframes fadeOut{from{opacity:1;}to{opacity:0;}}
  </style>
</body>
</html>
