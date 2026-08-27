<?php 
$pageTitle = 'Create Account - ' . getSiteName();
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
  <title><?php echo htmlspecialchars($siteName); ?> — Create Account</title>

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
      gap:12px;
    }
    .brand img{ height:40px; width:auto; display:block; }
    .brand h1{
      font-size:18px;
      margin:0;
      color:var(--navy-900);
      font-weight:700;
    }

    /* Step Progress */
    .step-progress{
      display:flex;
      justify-content:space-between;
      margin-bottom:20px;
      position:relative;
    }
    .step-progress::before{
      content:'';
      position:absolute;
      top:20px;
      left:0;
      right:0;
      height:2px;
      background:#e5e7eb;
      z-index:0;
    }
    .step-item{
      flex:1;
      display:flex;
      flex-direction:column;
      align-items:center;
      position:relative;
      z-index:1;
    }
    .step-number{
      width:40px;
      height:40px;
      border-radius:50%;
      background:#e5e7eb;
      color:#6b7280;
      display:flex;
      align-items:center;
      justify-content:center;
      font-weight:700;
      font-size:14px;
      margin-bottom:8px;
      transition:var(--transition);
    }
    .step-item.active .step-number{
      background:linear-gradient(180deg,var(--navy-800),var(--navy-900));
      color:white;
      box-shadow:0 4px 12px rgba(5,30,45,0.2);
    }
    .step-item.completed .step-number{
      background:#059669;
      color:white;
    }
    .step-label{
      font-size:11px;
      color:#6b7280;
      text-align:center;
      font-weight:600;
    }
    .step-item.active .step-label{
      color:var(--navy-900);
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
      margin-bottom:16px;
    }
    .input input{
      border:0;
      outline:0;
      background:transparent;
      width:100%;
      font-size:16px;
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

    .form-grid{
      display:grid;
      grid-template-columns:1fr 1fr;
      gap:12px;
      margin-bottom:16px;
    }

    .btn-primary{
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
      margin-top:8px;
    }
    .btn-primary:active{ transform: translateY(1px); }
    
    .btn-secondary{
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
      margin-top:8px;
    }
    .btn-secondary:hover{ background:#f6f9fb; }

    .step-actions{
      display:flex;
      gap:12px;
      margin-top:20px;
    }
    .step-actions button{ flex:1; }

    .remember{
      display:flex;
      align-items:center;
      gap:8px;
      font-size:14px;
      color:#12343e;
      margin-bottom:16px;
    }
    .remember input[type="checkbox"]{
      width:18px;
      height:18px;
      cursor:pointer;
    }

    .step-panel{
      display:none;
    }
    .step-panel.active{
      display:block;
    }

    .password-strength{
      margin-top:8px;
      height:4px;
      border-radius:2px;
      background:#e5e7eb;
      overflow:hidden;
      margin-bottom:16px;
    }
    .password-strength-bar{
      height:100%;
      transition:all 0.3s;
      border-radius:2px;
    }
    .strength-weak{ background:#dc3545; width:25%; }
    .strength-fair{ background:#ffc107; width:50%; }
    .strength-good{ background:#17a2b8; width:75%; }
    .strength-strong{ background:#28a745; width:100%; }

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
      margin-bottom:16px;
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
      margin-bottom:16px;
      text-align:left;
      padding: 10px 12px;
      background: rgba(5, 150, 105, 0.1);
      border-radius: 8px;
      border: 1px solid rgba(5, 150, 105, 0.2);
    }

    .card-note{ font-size:13px; color:#446168; text-align:center; margin-top:6px; }

    .review-item{
      padding:12px;
      background:#f6f9fb;
      border-radius:8px;
      margin-bottom:12px;
    }
    .review-item strong{
      display:block;
      font-size:12px;
      color:#6b7280;
      margin-bottom:4px;
    }
    .review-item span{
      color:#072033;
      font-size:14px;
    }

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
        max-height: none;
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
      
      .form-grid{
        grid-template-columns:1fr;
      }
      
      .step-progress{
        margin-bottom:16px;
      }
      .step-number{
        width:36px;
        height:36px;
        font-size:12px;
      }
      .step-label{
        font-size:10px;
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
            <h2>Join Us Today</h2>
            <p class="brand-message">Create your secure banking account in minutes and start managing your finances with confidence.</p>
          </div>
        </div>
      </div>

      <!-- Right form column -->
      <aside class="login-form-wrap" aria-label="Registration form">
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

            <div style="font-size:13px;color:#6b8892;font-weight:600;">Create Account</div>
          </div>

          <!-- Error/Success Messages -->
          <div id="errorMessage" class="error-message" role="alert" style="display: none;"></div>
          <?php if (isset($_SESSION['error'])): ?>
            <div class="error-message" role="alert">
              <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
          <?php endif; ?>

          <!-- Page heading -->
          <div>
            <h2 id="registerHeading" style="margin:6px 0 0 0; font-size:20px; color:var(--navy-900);">Create Your Account</h2>
            <p style="margin:6px 0 16px 0; color:#6b8892; font-size:14px;">Complete the steps below to get started</p>
          </div>

          <!-- Step Progress Indicator -->
          <div class="step-progress">
            <div class="step-item active" data-step="1">
              <div class="step-number">1</div>
              <div class="step-label">Personal</div>
            </div>
            <div class="step-item" data-step="2">
              <div class="step-number">2</div>
              <div class="step-label">Address</div>
            </div>
            <div class="step-item" data-step="3">
              <div class="step-number">3</div>
              <div class="step-label">Security</div>
            </div>
            <div class="step-item" data-step="4">
              <div class="step-number">4</div>
              <div class="step-label">Review</div>
            </div>
          </div>

          <!-- Registration Form -->
          <form method="POST" action="<?php echo SITE_URL; ?>/auth/register" id="registerForm">
            <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
            
            <!-- Step 1: Personal Information -->
            <div class="step-panel active" data-step="1">
              <div class="form-grid">
                <div>
                  <label for="full_name">Full Name *</label>
                  <div class="input">
                    <input id="full_name" name="full_name" type="text" placeholder="John Doe" required value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
                  </div>
                </div>
                
                <div>
                  <label for="email">Email Address *</label>
                  <div class="input">
                    <input id="email" name="email" type="email" placeholder="you@domain.com" autocomplete="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                  </div>
                </div>
              </div>

              <div class="form-grid">
                <div>
                  <label for="phone">Phone Number *</label>
                  <div class="input">
                    <input id="phone" name="phone" type="tel" placeholder="+1234567890" required value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                  </div>
                </div>
                
                <div>
                  <label for="date_of_birth">Date of Birth *</label>
                  <div class="input">
                    <input id="date_of_birth" name="date_of_birth" type="date" max="<?php echo date('Y-m-d', strtotime('-18 years')); ?>" required value="<?php echo htmlspecialchars($_POST['date_of_birth'] ?? ''); ?>">
                  </div>
                </div>
              </div>

              <div class="form-grid">
                <div>
                  <label for="gender">Gender</label>
                  <div class="input">
                    <select id="gender" name="gender" style="border:0;outline:0;background:transparent;width:100%;font-size:16px;color:#072033;">
                      <option value="">Select Gender</option>
                      <option value="male" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'male') ? 'selected' : ''; ?>>Male</option>
                      <option value="female" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'female') ? 'selected' : ''; ?>>Female</option>
                      <option value="other" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'other') ? 'selected' : ''; ?>>Other</option>
                    </select>
                  </div>
                </div>
                
                <div>
                  <label for="account_type">Account Type *</label>
                  <div class="input">
                    <select id="account_type" name="account_type" required style="border:0;outline:0;background:transparent;width:100%;font-size:16px;color:#072033;">
                      <option value="">Select Account Type</option>
                      <option value="checking" <?php echo (isset($_POST['account_type']) && $_POST['account_type'] === 'checking') ? 'selected' : ''; ?>>Checking Account</option>
                      <option value="savings" <?php echo (isset($_POST['account_type']) && $_POST['account_type'] === 'savings') ? 'selected' : ''; ?>>Savings Account</option>
                      <option value="business" <?php echo (isset($_POST['account_type']) && $_POST['account_type'] === 'business') ? 'selected' : ''; ?>>Business Account</option>
                      <option value="joint" <?php echo (isset($_POST['account_type']) && $_POST['account_type'] === 'joint') ? 'selected' : ''; ?>>Joint Account</option>
                      <option value="join_existing" <?php echo (isset($_POST['account_type']) && $_POST['account_type'] === 'join_existing') ? 'selected' : ''; ?>>Join Existing Account</option>
                    </select>
                  </div>
                </div>
              </div>

              <!-- Joint Account Fields (shown when join_existing is selected) -->
              <div id="joinExistingFields" style="display: none; margin-top: 16px;">
                <div>
                  <label for="existing_account_number">Existing Account Number *</label>
                  <div class="input" style="display: flex; gap: 8px;">
                    <input id="existing_account_number" name="existing_account_number" type="text" placeholder="Enter account number" style="flex: 1;">
                    <button type="button" id="searchAccountBtn" class="btn-primary" style="width: auto; padding: 12px 20px; margin: 0;">Search Account</button>
                  </div>
                </div>
                
                <div id="accountSearchResults" style="display: none; margin-top: 16px;">
                  <div style="background: #f0f4ff; padding: 16px; border-radius: 8px; border-left: 4px solid #3b82f6;">
                    <h4 style="margin: 0 0 12px 0; color: #1e3a8a; font-size: 16px;">Account Found</h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                      <div>
                        <label style="font-size: 12px; color: #666; display: block; margin-bottom: 4px;">Primary Owner Name</label>
                        <input type="text" id="primary_owner_name" readonly style="width: 100%; padding: 8px; border: 1px solid #e5e7eb; border-radius: 6px; background: #f9fafb; color: #666;">
                      </div>
                      <div>
                        <label style="font-size: 12px; color: #666; display: block; margin-bottom: 4px;">Primary Owner Email</label>
                        <input type="text" id="primary_owner_email" readonly style="width: 100%; padding: 8px; border: 1px solid #e5e7eb; border-radius: 6px; background: #f9fafb; color: #666;">
                      </div>
                    </div>
                    <div>
                      <label style="font-size: 12px; color: #666; display: block; margin-bottom: 4px;">Account Type</label>
                      <input type="text" id="found_account_type" readonly style="width: 100%; padding: 8px; border: 1px solid #e5e7eb; border-radius: 6px; background: #f9fafb; color: #666;">
                    </div>
                    <input type="hidden" id="found_account_id" name="found_account_id">
                    <button type="button" id="confirmJointRequestBtn" class="btn-primary" style="width: 100%; margin-top: 12px;">Confirm Joint Account Request</button>
                  </div>
                </div>
              </div>

              <div class="step-actions">
                <button type="button" class="btn-secondary" onclick="changeStep(2)">Next</button>
              </div>
            </div>

            <!-- Step 2: Address Information -->
            <div class="step-panel" data-step="2">
              <div>
                <label for="address">Street Address *</label>
                <div class="input">
                  <input id="address" name="address" type="text" placeholder="No P.O. Boxes" required value="<?php echo htmlspecialchars($_POST['address'] ?? ''); ?>">
                </div>
              </div>

              <div class="form-grid">
                <div>
                  <label for="city">City *</label>
                  <div class="input">
                    <input id="city" name="city" type="text" placeholder="City" required value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>">
                  </div>
                </div>
                
                <div>
                  <label for="state">State / Province</label>
                  <div class="input">
                    <input id="state" name="state" type="text" placeholder="State or province (optional)" value="<?php echo htmlspecialchars($_POST['state'] ?? ''); ?>">
                  </div>
                </div>
              </div>

              <div class="form-grid">
                <div>
                  <label for="postal_code">ZIP Code *</label>
                  <div class="input">
                    <input id="postal_code" name="postal_code" type="text" placeholder="12345" required value="<?php echo htmlspecialchars($_POST['postal_code'] ?? ''); ?>">
                  </div>
                </div>
                
                <div>
                  <label for="country">Country *</label>
                  <div class="input">
                    <?php
                    $registerCountriesByRegion = getCountriesByRegion();
                    $registerRegionLabels = [
                        'north-america' => 'North America',
                        'south-america' => 'South America',
                        'europe' => 'Europe',
                        'asia' => 'Asia',
                        'africa' => 'Africa',
                        'oceania' => 'Oceania',
                        'middle-east' => 'Middle East',
                    ];
                    $selectedCountry = $_POST['country'] ?? 'US';
                    ?>
                    <select id="country" name="country" required style="border:0;outline:0;background:transparent;width:100%;font-size:16px;color:#072033;">
                      <option value="">Select country</option>
                      <?php foreach ($registerCountriesByRegion as $region => $countries): ?>
                        <optgroup label="<?php echo htmlspecialchars($registerRegionLabels[$region] ?? ucwords(str_replace('-', ' ', $region))); ?>">
                          <?php foreach ($countries as $country): ?>
                            <option value="<?php echo htmlspecialchars($country['code']); ?>"
                              <?php echo ($selectedCountry === $country['code'] || $selectedCountry === $country['name']) ? 'selected' : ''; ?>>
                              <?php echo htmlspecialchars($country['name']); ?>
                            </option>
                          <?php endforeach; ?>
                        </optgroup>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
              </div>

              <div class="step-actions">
                <button type="button" class="btn-secondary" onclick="changeStep(1)">Back</button>
                <button type="button" class="btn-primary" onclick="changeStep(3)">Next</button>
              </div>
            </div>

            <!-- Step 3: Security -->
            <div class="step-panel" data-step="3">
              <div>
                <label for="password">Password *</label>
                <div class="input">
                  <input id="password" name="password" type="password" placeholder="Enter password" autocomplete="new-password" required>
                  <button type="button" class="eye-btn" id="togglePwd" aria-label="Show password">👁️</button>
                </div>
                <div class="password-strength">
                  <div class="password-strength-bar" id="strength-bar"></div>
                </div>
                <p style="font-size: 12px; color: #6b7280; margin-top: 4px; margin-bottom: 0;">
                  Must be at least 8 characters with uppercase, lowercase, and number
                </p>
              </div>

              <div>
                <label for="confirm_password">Confirm Password *</label>
                <div class="input">
                  <input id="confirm_password" name="confirm_password" type="password" placeholder="Confirm password" autocomplete="new-password" required>
                  <button type="button" class="eye-btn" id="toggleConfirmPwd" aria-label="Show password">👁️</button>
                </div>
              </div>

              <div class="step-actions">
                <button type="button" class="btn-secondary" onclick="changeStep(2)">Back</button>
                <button type="button" class="btn-primary" onclick="changeStep(4)">Next</button>
              </div>
            </div>

            <!-- Step 4: Review & Submit -->
            <div class="step-panel" data-step="4">
              <div>
                <h3 style="font-size:16px;color:var(--navy-900);margin-bottom:16px;">Review Your Information</h3>
                
                <div class="review-item">
                  <strong>Full Name</strong>
                  <span id="review-full_name">-</span>
                </div>
                
                <div class="review-item">
                  <strong>Email</strong>
                  <span id="review-email">-</span>
                </div>
                
                <div class="review-item">
                  <strong>Phone</strong>
                  <span id="review-phone">-</span>
                </div>
                
                <div class="review-item">
                  <strong>Date of Birth</strong>
                  <span id="review-date_of_birth">-</span>
                </div>
                
                <div class="review-item">
                  <strong>Gender</strong>
                  <span id="review-gender">-</span>
                </div>
                
                <div class="review-item">
                  <strong>Account Type</strong>
                  <span id="review-account_type">-</span>
                </div>
                
                <div class="review-item">
                  <strong>Address</strong>
                  <span id="review-address">-</span>
                </div>
                
                <div class="review-item">
                  <strong>City, State, ZIP</strong>
                  <span id="review-location">-</span>
                </div>
                
                <div class="review-item">
                  <strong>Country</strong>
                  <span id="review-country">-</span>
                </div>
              </div>

              <div class="remember">
                <input type="checkbox" id="terms" name="terms" value="1" required>
                <label for="terms">I agree to the <a href="<?php echo SITE_URL; ?>/terms" style="color:var(--accent);text-decoration:underline;">Terms of Service</a> and <a href="<?php echo SITE_URL; ?>/terms" style="color:var(--accent);text-decoration:underline;">Privacy Policy</a></label>
              </div>

              <div class="remember">
                <input type="checkbox" id="newsletter" name="newsletter" value="1">
                <label for="newsletter">Subscribe to banking updates and offers</label>
              </div>

              <div class="step-actions">
                <button type="button" class="btn-secondary" onclick="changeStep(3)">Back</button>
                <button type="submit" class="btn-primary">Create Account</button>
              </div>
            </div>
          </form>

          <!-- bottom links -->
          <div class="links" style="margin-top:auto;">
            <a href="<?php echo SITE_URL; ?>/auth/login">Already have an account? Sign In</a>
          </div>

          <div class="small-muted">Need help? <a href="<?php echo SITE_URL; ?>/help-center" style="color:var(--accent);text-decoration:underline;">Visit the Help Center</a></div>

        </div>
      </aside>

    </div>
  </div>

  <script>
    let currentStep = 1;
    const totalSteps = 4;

    function changeStep(step) {
      // Validate current step before proceeding
      if (step > currentStep) {
        if (!validateStep(currentStep)) {
          return;
        }
      }

      // Hide all panels
      document.querySelectorAll('.step-panel').forEach(panel => {
        panel.classList.remove('active');
      });

      // Show target panel
      const targetPanel = document.querySelector(`.step-panel[data-step="${step}"]`);
      if (targetPanel) {
        targetPanel.classList.add('active');
      }

      // Update progress indicator
      document.querySelectorAll('.step-item').forEach((item, index) => {
        const stepNum = index + 1;
        item.classList.remove('active', 'completed');
        if (stepNum < step) {
          item.classList.add('completed');
        } else if (stepNum === step) {
          item.classList.add('active');
        }
      });

      currentStep = step;

      // Update review on step 4
      if (step === 4) {
        updateReview();
      }
    }

    // Show form error message immediately
    function showFormError(message) {
      const errorDiv = document.getElementById('errorMessage');
      if (errorDiv) {
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
        // Scroll to error message
        errorDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        // Hide after 5 seconds
        setTimeout(() => {
          errorDiv.style.display = 'none';
        }, 5000);
      } else {
        alert(message);
      }
    }

    function validateStep(step) {
      const stepPanel = document.querySelector(`.step-panel[data-step="${step}"]`);
      const inputs = stepPanel.querySelectorAll('input[required]');
      const selects = stepPanel.querySelectorAll('select[required]');
      
      // Clear previous errors
      const errorDiv = document.getElementById('errorMessage');
      if (errorDiv) errorDiv.style.display = 'none';
      
      let isValid = true;
      inputs.forEach(input => {
        if (!input.value.trim()) {
          isValid = false;
          input.style.borderColor = '#b91c1c';
          setTimeout(() => {
            input.style.borderColor = '';
          }, 2000);
        } else {
          // Special validation for password
          if (input.id === 'password' && step === 3) {
            const password = input.value;
            // Updated: Only require uppercase, lowercase, and number (special chars optional)
            if (password.length < 8 || !/[a-z]/.test(password) || !/[A-Z]/.test(password) || !/[0-9]/.test(password)) {
              isValid = false;
              showFormError('Password must be at least 8 characters with uppercase, lowercase, and number');
              input.style.borderColor = '#b91c1c';
              return false;
            }
            
            const confirmPassword = document.getElementById('confirm_password').value;
            if (password !== confirmPassword) {
              isValid = false;
              showFormError('Passwords do not match');
              document.getElementById('confirm_password').style.borderColor = '#b91c1c';
              return false;
            }
          }
        }
      });
      
      // Validate required select elements
      selects.forEach(select => {
        if (!select.value || select.value.trim() === '') {
          isValid = false;
          select.style.borderColor = '#b91c1c';
          setTimeout(() => {
            select.style.borderColor = '';
          }, 2000);
        }
      });

      if (!isValid) {
        showFormError('Please fill in all required fields correctly');
      }
      
      return isValid;
    }

    // Form submit validation - show errors immediately before submission
    document.getElementById('registerForm').addEventListener('submit', function(e) {
      // Validate all steps before submission
      let allValid = true;
      
      // Validate step 1
      if (!validateStep(1)) {
        allValid = false;
        changeStep(1);
      }
      
      // Validate step 2
      if (allValid && !validateStep(2)) {
        allValid = false;
        changeStep(2);
      }
      
      // Validate step 3 (password)
      if (allValid && !validateStep(3)) {
        allValid = false;
        changeStep(3);
      }
      
      // Check terms checkbox
      if (allValid && !document.getElementById('terms').checked) {
        allValid = false;
        changeStep(4);
        showFormError('You must agree to the Terms of Service and Privacy Policy');
      }
      
      if (!allValid) {
        e.preventDefault();
        return false;
      }
      
      // If all valid, allow form submission
      return true;
    });

    function updateReview() {
      document.getElementById('review-full_name').textContent = document.getElementById('full_name').value || '-';
      document.getElementById('review-email').textContent = document.getElementById('email').value || '-';
      document.getElementById('review-phone').textContent = document.getElementById('phone').value || '-';
      document.getElementById('review-date_of_birth').textContent = document.getElementById('date_of_birth').value || '-';
      
      const genderSelect = document.getElementById('gender');
      const genderText = genderSelect.options[genderSelect.selectedIndex].text;
      document.getElementById('review-gender').textContent = genderSelect.value ? genderText : '-';
      
      const accountTypeSelect = document.getElementById('account_type');
      const accountTypeText = accountTypeSelect.options[accountTypeSelect.selectedIndex].text;
      document.getElementById('review-account_type').textContent = accountTypeSelect.value ? accountTypeText : '-';
      
      document.getElementById('review-address').textContent = document.getElementById('address').value || '-';
      
      const city = document.getElementById('city').value || '';
      const state = document.getElementById('state').value || '';
      const zip = document.getElementById('postal_code').value || '';
      document.getElementById('review-location').textContent = [city, state, zip].filter(Boolean).join(', ') || '-';
      
      const countrySelect = document.getElementById('country');
      const countryText = countrySelect.options[countrySelect.selectedIndex]?.text || '-';
      document.getElementById('review-country').textContent = countrySelect.value ? countryText : '-';
    }

    // Password visibility toggles
    const togglePwd = document.getElementById('togglePwd');
    const pwdInput = document.getElementById('password');
    if (togglePwd && pwdInput) {
      togglePwd.addEventListener('click', (e) => {
        e.preventDefault();
        if (pwdInput.type === 'password') {
          pwdInput.type = 'text';
          togglePwd.textContent = '🙈';
        } else {
          pwdInput.type = 'password';
          togglePwd.textContent = '👁️';
        }
      });
    }

    const toggleConfirmPwd = document.getElementById('toggleConfirmPwd');
    const confirmPwdInput = document.getElementById('confirm_password');
    if (toggleConfirmPwd && confirmPwdInput) {
      toggleConfirmPwd.addEventListener('click', (e) => {
        e.preventDefault();
        if (confirmPwdInput.type === 'password') {
          confirmPwdInput.type = 'text';
          toggleConfirmPwd.textContent = '🙈';
        } else {
          confirmPwdInput.type = 'password';
          toggleConfirmPwd.textContent = '👁️';
        }
      });
    }

    // Password strength indicator
    if (pwdInput) {
      pwdInput.addEventListener('input', function() {
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
    }

    // Form submission - validate all steps
    document.getElementById('registerForm').addEventListener('submit', function(e) {
      // Validate all steps before submitting
      for (let i = 1; i <= 3; i++) {
        if (!validateStep(i)) {
          e.preventDefault();
          changeStep(i);
          return false;
        }
      }

      // Check terms checkbox
      if (!document.getElementById('terms').checked) {
        e.preventDefault();
        alert('Please accept the Terms of Service and Privacy Policy');
        return false;
      }

      // Validate passwords match
      const password = document.getElementById('password').value;
      const confirmPassword = document.getElementById('confirm_password').value;
      if (password !== confirmPassword) {
        e.preventDefault();
        alert('Passwords do not match');
        changeStep(3);
        return false;
      }
    });

    // Prevent autoplay issues
    window.addEventListener('load', () => {
      const video = document.getElementById('bgVideo');
      if (video && video.paused) {
        const playPromise = video.play();
        if (playPromise !== undefined) {
          playPromise.catch(() => { /* autoplay blocked; it's fine */ });
        }
      }
    });

    // Joint Account Handling
    const accountTypeSelect = document.getElementById('account_type');
    const joinExistingFields = document.getElementById('joinExistingFields');
    const searchAccountBtn = document.getElementById('searchAccountBtn');
    const accountSearchResults = document.getElementById('accountSearchResults');
    const existingAccountNumber = document.getElementById('existing_account_number');
    const confirmJointRequestBtn = document.getElementById('confirmJointRequestBtn');

    // Show/hide join existing fields based on account type
    if (accountTypeSelect) {
      accountTypeSelect.addEventListener('change', function() {
        if (this.value === 'join_existing') {
          joinExistingFields.style.display = 'block';
          existingAccountNumber.required = true;
        } else {
          joinExistingFields.style.display = 'none';
          accountSearchResults.style.display = 'none';
          existingAccountNumber.required = false;
          existingAccountNumber.value = '';
        }
      });
    }

    // Search Account Button
    if (searchAccountBtn) {
      searchAccountBtn.addEventListener('click', async function() {
        const accountNumber = existingAccountNumber.value.trim();
        if (!accountNumber) {
          showFormError('Please enter an account number');
          return;
        }

        searchAccountBtn.disabled = true;
        searchAccountBtn.textContent = 'Searching...';

        try {
          const response = await fetch('<?php echo SITE_URL; ?>/api/search-account.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ account_number: accountNumber })
          });

          const data = await response.json();

          if (data.success) {
            document.getElementById('primary_owner_name').value = data.owner_name || '';
            document.getElementById('primary_owner_email').value = data.owner_email || '';
            document.getElementById('found_account_type').value = data.account_type || '';
            document.getElementById('found_account_id').value = data.account_id || '';
            accountSearchResults.style.display = 'block';
          } else {
            showFormError(data.message || 'Account not found');
            accountSearchResults.style.display = 'none';
          }
        } catch (error) {
          showFormError('Error searching for account. Please try again.');
          accountSearchResults.style.display = 'none';
        } finally {
          searchAccountBtn.disabled = false;
          searchAccountBtn.textContent = 'Search Account';
        }
      });
    }

    // Confirm Joint Request Button
    if (confirmJointRequestBtn) {
      confirmJointRequestBtn.addEventListener('click', function() {
        // This will be handled during form submission
        // The form will submit with found_account_id
        const foundAccountId = document.getElementById('found_account_id').value;
        if (!foundAccountId) {
          showFormError('Please search and select an account first');
          return;
        }
        // Allow form to proceed to next step
        changeStep(2);
      });
    }
  </script>
</body>
</html>
