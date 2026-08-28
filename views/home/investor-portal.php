<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

// Get dynamic branding
$siteName = getSiteName();
$siteInitials = getSiteInitials();
$siteEmail = getSiteEmail();
$pageTitle = 'Investor Portal | ' . $siteName;

include __DIR__ . '/../layouts/header.php';
?>

<section class="page-hero page-hero--gradient">
    <div class="container page-hero__content">
        <span class="hero__eyebrow">Investor Relations</span>
        <h1>Investor Portal</h1>
        <p>Access investor updates, financial reports, and governance information.</p>
    </div>
</section>

<section class="section section--content">
    <div class="container content-block">
        <h2>Coming Soon</h2>
        <p>The dedicated investor portal is currently under development. Accredited investors and partners can contact <a href="mailto:<?php echo htmlspecialchars($siteEmail); ?>"><?php echo htmlspecialchars($siteEmail); ?></a> for interim reporting and updates.</p>
    </div>
</section>

<?php include __DIR__ . '/../layouts/footer.php'; ?>

