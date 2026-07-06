<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

// Get dynamic branding
$siteName = getSiteName();
$siteInitials = getSiteInitials();
$pageTitle = 'Terms of Service | ' . $siteName;

include __DIR__ . '/views/layouts/header.php';
?>

<style>
/* ===== PUBLIC PAGE STYLES - MATCHING HOMEPAGE DESIGN ===== */
.public-page-wrapper {
    isolation: isolate;
    position: relative;
    width: 100%;
    overflow-x: hidden;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-rendering: optimizeLegibility;
}

/* ===== BROWSER-SPECIFIC FIXES FOR CONSISTENT MOBILE RENDERING ===== */
@media screen and (max-width: 767px) {
    html {
        -webkit-text-size-adjust: 100%;
        -moz-text-size-adjust: 100%;
        -ms-text-size-adjust: 100%;
        text-size-adjust: 100%;
    }
    
    body {
        -webkit-text-size-adjust: 100%;
        -moz-text-size-adjust: 100%;
        -ms-text-size-adjust: 100%;
        text-size-adjust: 100%;
    }
}

/* ===== HERO SECTION - DESKTOP ===== */
.public-page-wrapper .page-hero {
    background-image: url('<?php echo SITE_URL . '/uploads/images/' . rawurlencode('about-img-5.webp'); ?>');
    background-size: cover;
    background-position: center center;
    background-repeat: no-repeat;
    min-height: 60vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 8rem 2rem;
    position: relative;
    z-index: 1;
    isolation: isolate;
    overflow: hidden;
    contain: layout style paint;
}

.public-page-wrapper .page-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle at 100% -20%, rgba(53, 158, 180, 0.85), rgba(48, 80, 102, 0.9), rgba(35, 46, 58, 0.95), rgba(35, 46, 58, 0.98));
    z-index: 0;
    pointer-events: none;
}

.public-page-wrapper .page-hero > * {
    position: relative;
    z-index: 1;
}

.public-page-wrapper .container {
    max-width: 144rem;
    margin: 0 auto;
    width: 100%;
    padding: 0 2rem;
}

.public-page-wrapper .page-hero__content {
    max-width: 144rem;
    margin: 0 auto;
    width: 100%;
    padding: 0 2rem;
    text-align: center;
    z-index: 2;
    position: relative;
}

.public-page-wrapper .hero__eyebrow {
    display: inline-block;
    color: #359eb4;
    font-size: 1.6rem;
    font-weight: 600;
    margin-bottom: 1rem;
    text-transform: uppercase;
    letter-spacing: 0.1em;
}

.public-page-wrapper .page-hero h1 {
    font-size: clamp(3.2rem, 6vw, 5.8rem);
    font-weight: 700;
    letter-spacing: -0.02em;
    line-height: 1.2;
    color: #ffffff;
    margin-bottom: 2rem;
}

.public-page-wrapper .page-hero p {
    font-size: clamp(1.8rem, 2.5vw, 2.2rem);
    line-height: 1.5;
    color: #ffffff;
    max-width: 800px;
    margin: 0 auto;
}

/* ===== SECTION BASE STYLES - DESKTOP ===== */
.public-page-wrapper .section {
    position: relative;
    isolation: isolate;
    contain: layout style;
    overflow: hidden;
}

/* ===== SECTION CONTAINER - DESKTOP ===== */
.public-page-wrapper .section-container {
    max-width: 144rem;
    margin: 6rem auto;
    width: 100%;
    padding: 0 2rem;
}

/* ===== LEGAL CONTENT STYLES ===== */
.public-page-wrapper .legal-content {
    max-width: 900px;
    margin: 0 auto;
}

.public-page-wrapper .legal-content h2 {
    font-size: clamp(2.4rem, 3vw, 3rem);
    font-weight: 700;
    letter-spacing: -0.02em;
    line-height: 1.3;
    color: #232e3a;
    margin: 3rem 0 1.5rem 0;
    padding-bottom: 1rem;
    border-bottom: 2px solid #e5e7eb;
}

.public-page-wrapper .legal-content p {
    font-size: clamp(1.6rem, 1.8vw, 1.8rem);
    line-height: 1.8;
    color: #6b747f;
    margin-bottom: 1.5rem;
}

/* ===== MOBILE STYLES ===== */
@media (max-width: 767px) {
    .public-page-wrapper .page-hero {
        min-height: 50vh;
        padding: 4rem 2rem;
        background-attachment: scroll;
    }
    
    .public-page-wrapper .page-hero h1 {
        font-size: 2.8rem;
    }
    
    .public-page-wrapper .page-hero p {
        font-size: 1.6rem;
    }
    
    .public-page-wrapper .section-container {
        padding: 0 2rem;
        margin: 4rem auto;
    }
}
</style>

<div class="public-page-wrapper">
<section class="page-hero">
    <div class="container page-hero__content">
        <span class="hero__eyebrow">Legal</span>
        <h1>Terms of Service</h1>
        <p>Review the policies that govern your relationship with <?php echo htmlspecialchars($siteName); ?>.</p>
    </div>
</section>

<section class="section section--content">
    <div class="container section-container">
        <div class="legal-content">
            <h2>Introduction</h2>
            <p>These Terms of Service govern your access to and use of <?php echo htmlspecialchars($siteName); ?> products, platforms, and services. By opening an account or using our services, you agree to these terms.</p>

            <h2>Eligibility &amp; Account Opening</h2>
            <p>Clients must be 18 years or older and provide valid identification, proof of address, and source-of-funds documentation. <?php echo htmlspecialchars($siteName); ?> reserves the right to approve or decline applications at its discretion.</p>

            <h2>Deposit &amp; Withdrawal Policies</h2>
            <p>Deposits and withdrawals may be subject to verification and compliance review. Settlement times vary by currency, location, and payment network.</p>

            <h2>Investment Risks</h2>
            <p>All investments involve risk. Past performance does not guarantee future results. Clients are responsible for understanding the risks associated with stocks, ETFs, forex, and digital assets.</p>

            <h2>Card Usage</h2>
            <p>Cards must be used in accordance with network rules. Fraudulent or prohibited activity may result in card suspension or account closure.</p>

            <h2>Data Protection</h2>
            <p>We collect and process personal data in line with our Privacy Policy and applicable data protection laws. Security measures protect your information, but no system is entirely immune to unauthorized access.</p>

            <h2>AML &amp; Compliance Policy</h2>
            <p><?php echo htmlspecialchars($siteName); ?> adheres to global AML, KYC, and sanctions screening requirements. Suspicious activity may be reported to relevant authorities.</p>

            <h2>Termination of Service</h2>
            <p>Either party may terminate the banking relationship upon written notice. <?php echo htmlspecialchars($siteName); ?> may suspend or terminate services for compliance breaches or misuse.</p>

            <h2>Governing Law</h2>
            <p>These terms are governed by the laws of the United States. Disputes shall be resolved in the state and federal courts located in New York.</p>
        </div>
    </div>
</section>
</div>

<?php include __DIR__ . '/views/layouts/footer.php'; ?>

