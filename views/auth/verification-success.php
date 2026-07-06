<?php
$pageTitle = 'Email Verified - ' . getSiteName();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

if (!isLoggedIn()) {
    redirect('/auth/login');
}

$siteName = getSiteName();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?php echo htmlspecialchars($siteName); ?> — Email Verified</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/fontawesome/css/all.min.css">
  <style>
    :root{
      --navy-900: #041826;
      --card-bg: #ffffff;
      font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
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
      max-width:480px;
      width:100%;
      text-align:center;
      box-shadow: 0 20px 60px rgba(3,18,26,0.45);
    }
    .icon{
      width:100px;
      height:100px;
      border-radius:50%;
      background:linear-gradient(135deg,#10b981,#059669);
      display:flex;
      align-items:center;
      justify-content:center;
      margin:0 auto 24px;
      animation:scaleIn 0.5s ease-out;
    }
    .icon i{ font-size:48px; color:white; }
    @keyframes scaleIn{
      from{ transform:scale(0); opacity:0; }
      to{ transform:scale(1); opacity:1; }
    }
    h1{ font-size:28px; color:var(--navy-900); margin:0 0 12px; }
    p{ color:#666; font-size:16px; margin:0 0 24px; line-height:1.6; }
    .spinner{
      width:40px;
      height:40px;
      border:3px solid #e5e7eb;
      border-top-color:#0ea5a4;
      border-radius:50%;
      margin:0 auto;
      animation:spin 0.8s linear infinite;
    }
    @keyframes spin{ to{ transform:rotate(360deg); } }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <div class="icon"><i class="fas fa-check"></i></div>
      <h1>Email Verified Successfully</h1>
      <p>Preparing your account&hellip;</p>
      <div class="spinner" role="status" aria-label="Loading"></div>
    </div>
  </div>
  <script>
    setTimeout(function() {
      window.location.href = '<?php echo SITE_URL; ?>/profile/security?verified=1&setup=1';
    }, 1500);
  </script>
</body>
</html>
