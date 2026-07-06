<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

// Get dynamic branding
$siteName = getSiteName();
$siteInitials = getSiteInitials();
$pageTitle = 'Security | ' . $siteName;

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

/* ===== DOUBLE GRID STYLES ===== */
.public-page-wrapper .double-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 3rem;
    max-width: 144rem;
    margin: 0 auto;
    padding: 0 2rem;
}

.public-page-wrapper .info-card {
    background-color: #f8f9fa;
    padding: 3rem;
    border-radius: 1.2rem;
    transition: all 0.3s ease;
}

.public-page-wrapper .info-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.public-page-wrapper .info-card h2 {
    font-size: clamp(2rem, 2.5vw, 2.4rem);
    font-weight: 700;
    letter-spacing: -0.02em;
    line-height: 1.3;
    color: #232e3a;
    margin-bottom: 1.5rem;
}

.public-page-wrapper .info-card p {
    font-size: clamp(1.6rem, 1.8vw, 1.8rem);
    line-height: 1.6;
    color: #6b747f;
}

/* ===== FEATURE BLOCK STYLES ===== */
.public-page-wrapper .feature-block {
    max-width: 900px;
    margin: 0 auto;
    background-color: #f8f9fa;
    padding: 3rem;
    border-radius: 1.2rem;
}

.public-page-wrapper .feature-block__content h2 {
    font-size: clamp(2.4rem, 3vw, 3rem);
    font-weight: 700;
    letter-spacing: -0.02em;
    line-height: 1.3;
    color: #232e3a;
    margin-bottom: 1.5rem;
}

.public-page-wrapper .feature-block__content p {
    font-size: clamp(1.6rem, 1.8vw, 1.8rem);
    line-height: 1.6;
    color: #6b747f;
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
    
    .public-page-wrapper .double-grid {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .public-page-wrapper .info-card {
        padding: 2rem;
    }
}
</style>

<div class="public-page-wrapper">
<section class="page-hero">
    <div class="container page-hero__content">
        <span class="hero__eyebrow">Security</span>
        <h1>Your Security Comes First</h1>
        <p><?php echo htmlspecialchars($siteName); ?> protects every transaction with enterprise-grade technology and offshore compliance.</p>
    </div>
</section>

<section class="section section--grid">
    <div class="container section-container">
        <div class="double-grid">
            <article class="info-card">
                <h2>256-bit SSL Encryption</h2>
                <p>All sessions and data transfers are encrypted end-to-end with modern TLS standards and key rotation.</p>
            </article>
            <article class="info-card">
                <h2>Multi-factor Authentication</h2>
                <p>Layered authentication using biometrics, OTP, hardware tokens, or authenticator apps for every account.</p>
            </article>
            <article class="info-card">
                <h2>Continuous Monitoring</h2>
                <p>AI-driven threat monitoring, anomaly detection, and automated response protocols protect your assets 24/7.</p>
            </article>
            <article class="info-card">
                <h2>Offshore Data Compliance</h2>
                <p>Data centers located in top-tier jurisdictions with GDPR, SOC 2, and ISO 27001 certifications.</p>
            </article>
            <article class="info-card">
                <h2>Digital Asset Insurance</h2>
                <p>Institutional custody solutions backed by insurance coverage on digital assets and segregated accounts.</p>
            </article>
        </div>
    </div>
</section>

<section class="section section--content">
    <div class="container section-container">
        <div class="feature-block">
            <div class="feature-block__content">
                <h2>Security Operations Center</h2>
                <p>Global SOC teams monitor infrastructure in real time, performing proactive penetration testing and vulnerability assessments.</p>
            </div>
        </div>
    </div>
</section>
</div>

<?php include __DIR__ . '/views/layouts/footer.php'; ?>

