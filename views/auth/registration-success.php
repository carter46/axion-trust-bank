<?php 
$pageTitle = 'Registration Successful - ' . getSiteName();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/security.php';

$siteName = getSiteName();

if (isLoggedIn()) {
    redirect('/dashboard');
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?php echo htmlspecialchars($siteName); ?> — Check Your Email</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/fontawesome/css/all.min.css">
  <style>
    :root{
      --navy-900: #041826;
      --navy-800: #073049;
      --card-bg: #ffffff;
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
    .success-root{
      min-height:100vh;
      display:flex;
      align-items:center;
      justify-content:center;
      padding:20px;
      box-sizing:border-box;
    }
    .success-container{
      background:var(--card-bg);
      border-radius:18px;
      padding:60px 40px;
      max-width:600px;
      width:100%;
      text-align:center;
      box-shadow: 0 20px 60px rgba(3,18,26,0.45);
    }
    .envelope-wrap{
      width:120px;
      height:90px;
      margin:0 auto 30px;
      position:relative;
      perspective:600px;
    }
    .envelope{
      width:120px;
      height:80px;
      background:linear-gradient(180deg,#e8f4fc,#cfe8f5);
      border-radius:4px;
      position:relative;
      box-shadow:0 8px 24px rgba(7,48,73,0.2);
      animation: envelopeFloat 3s ease-in-out infinite;
    }
    .envelope-flap{
      position:absolute;
      top:0;
      left:0;
      width:0;
      height:0;
      border-left:60px solid transparent;
      border-right:60px solid transparent;
      border-top:45px solid #93c5fd;
      transform-origin:top center;
      animation: flapOpen 2.5s ease-in-out infinite;
    }
    .envelope-body{
      position:absolute;
      bottom:0;
      left:0;
      right:0;
      height:50px;
      background:linear-gradient(180deg,#bfdbfe,#93c5fd);
      border-radius:0 0 4px 4px;
    }
    .envelope-letter{
      position:absolute;
      left:50%;
      bottom:12px;
      transform:translateX(-50%);
      width:70px;
      height:40px;
      background:white;
      border-radius:2px;
      box-shadow:0 2px 8px rgba(0,0,0,0.1);
      animation: letterPeek 2.5s ease-in-out infinite;
    }
    @keyframes envelopeFloat{
      0%,100%{ transform:translateY(0); }
      50%{ transform:translateY(-6px); }
    }
    @keyframes flapOpen{
      0%,100%{ transform:rotateX(0deg); }
      40%,60%{ transform:rotateX(180deg); }
    }
    @keyframes letterPeek{
      0%,100%{ bottom:12px; }
      40%,60%{ bottom:28px; }
    }
    .success-container h1{
      font-size:32px;
      color:var(--navy-900);
      margin:0 0 16px 0;
      font-weight:700;
    }
    .success-container p{
      font-size:17px;
      color:#555;
      line-height:1.7;
      margin:0 0 20px 0;
    }
    .hint-list{
      text-align:left;
      background:#f8fafc;
      border-radius:10px;
      padding:16px 20px;
      margin:24px 0 0;
      font-size:15px;
      color:#64748b;
      line-height:1.6;
    }
    .hint-list li{ margin-bottom:6px; }
    .do-not-login{
      margin-top:20px;
      padding:12px 16px;
      background:#fef3c7;
      border-radius:8px;
      color:#92400e;
      font-size:14px;
      font-weight:600;
    }
  </style>
  <?php include __DIR__ . '/../../includes/translation.php'; ?>
</head>
<body>
  <div class="success-root">
    <div class="success-container">
      <div class="envelope-wrap" aria-hidden="true">
        <div class="envelope">
          <div class="envelope-flap"></div>
          <div class="envelope-body"></div>
          <div class="envelope-letter"></div>
        </div>
      </div>
      <h1>Check Your Inbox</h1>
      <p>We've sent a verification link to your email address. Open your inbox and click the link to activate your account.</p>
      <div class="do-not-login">
        <i class="fas fa-info-circle"></i> Do not log in until you have verified your email.
      </div>
      <ul class="hint-list">
        <li>The verification link expires in 24 hours</li>
        <li>Check your spam or junk folder if you don't see it</li>
        <li>Whitelist our emails to receive important account updates</li>
      </ul>
    </div>
  </div>
</body>
</html>
