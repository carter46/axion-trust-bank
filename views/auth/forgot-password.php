<?php 
$pageTitle = 'Forgot Password - ' . getSiteName();
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
  <title><?php echo htmlspecialchars($siteName); ?> — Reset Password</title>

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
      gap:12px;
    }
    .brand img{ height:40px; width:auto; display:block; }
    .brand h1{
      font-size:18px;
      margin:0;
      color:var(--navy-900);
      font-weight:700;
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
      font-size:16px;
      color:#072033;
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
    
    .btn-secondary{
      margin-top:8px;
      width:100%;
      border:1px solid rgba(6,30,40,0.15);
      background:transparent;
      color:var(--navy-900);
      padding:14px;
      border-radius:10px;
      font-weight:600;
      cursor:pointer;
      letter-spacing:0.2px;
      transition: all 160ms ease;
      font-size: 16px;
      text-decoration:none;
      display:inline-block;
      text-align:center;
      box-sizing:border-box;
    }
    .btn-secondary:hover{ background:#f6f9fb; }

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

    .card-note{ font-size:13px; color:#446168; text-align:center; margin-top:6px; }

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
    }

    .tab-btn:focus,
    .input input:focus,
    .btn-primary:focus {
      outline: 2px solid var(--accent);
      outline-offset: 2px;
    }
  </style>
  <?php include __DIR__ . '/../../includes/translation.php'; ?>
</head>
<body>

  <div class="login-root">
    <div class="login-wrap" role="main">

      <!-- Left visual (video background) -->
      <div class="login-visual" aria-hidden="true">
        <div class="visual-video" id="visualVideo">
          <video id="bgVideo" autoplay muted loop playsinline poster="https://images.unsplash.com/photo-1601597111158-2fceff292cdc?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80">
            <source src="https://assets.mixkit.co/videos/preview/mixkit-modern-bank-lobby-1574-large.mp4" type="video/mp4">
          </video>
        </div>

        <div class="visual-overlay">
          <div>
            <h2>Reset Your Password</h2>
            <p class="brand-message">Enter your email address and we'll send you instructions to reset your password securely.</p>
          </div>
        </div>
      </div>

      <!-- Right form column -->
      <aside class="login-form-wrap" aria-label="Reset password form">
        <div class="form-card" role="form">
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
            </div>

            <div style="font-size:13px;color:#6b8892;font-weight:600;">Password Reset</div>
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
            <h2 id="resetHeading" style="margin:6px 0 0 0; font-size:20px; color:var(--navy-900);">Forgot Password?</h2>
            <p style="margin:6px 0 0 0; color:#6b8892; font-size:14px;">Enter your email to receive reset instructions</p>
          </div>

          <!-- Reset Form -->
          <form method="POST" action="<?php echo SITE_URL; ?>/auth/forgot-password">
            <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
            
            <div>
              <label for="email">Email Address</label>
              <div class="input">
                <input id="email" name="email" type="email" placeholder="you@domain.com" autocomplete="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
              </div>
            </div>

            <div>
              <button type="submit" class="btn-primary">Send Reset Instructions</button>
            </div>

            <div class="card-note">We'll send password reset instructions to your registered email address.</div>
          </form>

          <!-- bottom links -->
          <div class="links" style="margin-top:auto;">
            <a href="<?php echo SITE_URL; ?>/auth/login">Back to Sign In</a>
            <a href="<?php echo SITE_URL; ?>/auth/register">Create Account</a>
          </div>

          <div class="small-muted">Need help? <a href="<?php echo SITE_URL; ?>/help-center" style="color:var(--accent);text-decoration:underline;">Visit the Help Center</a></div>

        </div>
      </aside>

    </div>
  </div>

  <script>
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
  </script>
</body>
</html>
