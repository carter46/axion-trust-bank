<?php 
$pageTitle = 'Joint Account Request Submitted - ' . getSiteName();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/security.php';

// Get site branding
$siteName = getSiteName();
$siteInitials = getSiteInitials();
$logoUrl = getSiteLogo();

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
  <title><?php echo htmlspecialchars($siteName); ?> — Joint Account Request Submitted</title>
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
      font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, "Helvetica Neue", Arial;
    }
    html,body{
      height:100%;
      margin:0;
      background: linear-gradient(180deg,var(--navy-900), #042b3a 60%);
      color: #072033;
      -webkit-font-smoothing:antialiased;
      overflow-x: hidden;
    }
    .confirmation-root{
      min-height:100vh;
      display:flex;
      align-items:center;
      justify-content:center;
      padding:20px;
      box-sizing:border-box;
    }
    .confirmation-container{
      background:var(--card-bg);
      border-radius:18px;
      padding:60px 40px;
      max-width:600px;
      width:100%;
      text-align:center;
      box-shadow: 0 20px 60px rgba(3,18,26,0.45);
    }
    .confirmation-icon{
      width:100px;
      height:100px;
      border-radius:50%;
      background:linear-gradient(135deg, #3b82f6, #1e3a8a);
      display:flex;
      align-items:center;
      justify-content:center;
      margin:0 auto 30px;
      animation: scaleIn 0.5s ease-out;
    }
    .confirmation-icon i{
      font-size:50px;
      color:white;
    }
    @keyframes scaleIn{
      from{transform:scale(0);opacity:0;}
      to{transform:scale(1);opacity:1;}
    }
    .confirmation-container h1{
      font-size:32px;
      color:var(--navy-900);
      margin:0 0 16px 0;
      font-weight:700;
    }
    .confirmation-container p{
      font-size:18px;
      color:#666;
      line-height:1.6;
      margin:0 0 20px 0;
    }
    .info-box{
      background:#f0f9ff;
      border:1px solid #bae6fd;
      border-radius:12px;
      padding:20px;
      margin:30px 0;
      text-align:left;
    }
    .info-box h3{
      margin:0 0 12px 0;
      color:#0369a1;
      font-size:18px;
    }
    .info-box ul{
      margin:0;
      padding-left:20px;
      color:#0c4a6e;
    }
    .info-box li{
      margin-bottom:8px;
    }
    .warning-box{
      background:#fef3c7;
      border:1px solid #fde68a;
      border-radius:12px;
      padding:16px;
      margin:20px 0;
      color:#92400e;
      font-size:14px;
    }
  </style>
  <?php include __DIR__ . '/../../includes/translation.php'; ?>
</head>
<body>
  <div class="confirmation-root">
    <div class="confirmation-container">
      <div class="confirmation-icon">
        <i class="fas fa-clock"></i>
      </div>
      <h1>Joint Account Request Submitted</h1>
      <p>Your request to join an existing account has been successfully submitted and is currently being processed.</p>
      
      <div class="info-box">
        <h3>What happens next?</h3>
        <ul>
          <li>The primary account owner has been notified of your request</li>
          <li>They will review your request and account details</li>
          <li>You will receive an email notification once they respond</li>
          <li>The request will expire in 7 days if not responded to</li>
        </ul>
      </div>
      
      <div class="warning-box">
        <strong>Important:</strong> You will not be able to log in until the primary account owner accepts your request. Once approved, you'll receive an email with login instructions.
      </div>
      
      <p style="margin-top: 30px; color: #999; font-size: 14px;">
        Check your email for a confirmation message with the same information.
      </p>
      
      <div style="margin-top: 40px; text-align: center;">
        <a href="<?php echo SITE_URL; ?>/auth/login" style="display: inline-block; padding: 14px 32px; background: #3b82f6; color: white; text-decoration: none; border-radius: 8px; font-weight: 600; transition: background 0.3s;">
          <i class="fas fa-sign-in-alt" style="margin-right: 8px;"></i>
          Go to Login Page
        </a>
      </div>
    </div>
  </div>
</body>
</html>


