<?php
/** User-safe Hub consume error page */
$hubConsumeMessage = $hubConsumeMessage ?? 'This login link has expired or is invalid. Return to 7th Trade Hub and try again.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login unavailable</title>
</head>
<body style="margin:0;background:#ffffff;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;">
    <p style="color:#333;max-width:420px;text-align:center;padding:24px;line-height:1.5;margin:0;">
        <?php echo htmlspecialchars($hubConsumeMessage); ?>
    </p>
</body>
</html>
