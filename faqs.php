<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

// Get dynamic branding
$siteName = getSiteName();
$siteInitials = getSiteInitials();
$pageTitle = 'FAQs | ' . $siteName;

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

/* ===== FAQ STYLES ===== */
.public-page-wrapper .faq-list {
    max-width: 900px;
    margin: 6rem auto;
    padding: 0 2rem;
}

.public-page-wrapper .faq-item {
    background-color: #f8f9fa;
    padding: 3rem;
    border-radius: 1.2rem;
    margin-bottom: 2rem;
    transition: all 0.3s ease;
}

.public-page-wrapper .faq-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.public-page-wrapper .faq-item h2 {
    font-size: clamp(2rem, 2.5vw, 2.4rem);
    font-weight: 700;
    letter-spacing: -0.02em;
    line-height: 1.3;
    color: #232e3a;
    margin-bottom: 1.5rem;
}

.public-page-wrapper .faq-item p {
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
    
    .public-page-wrapper .section-container,
    .public-page-wrapper .faq-list {
        padding: 0 2rem;
        margin: 4rem auto;
    }
    
    .public-page-wrapper .faq-item {
        padding: 2rem;
    }
}
</style>

<div class="public-page-wrapper">
<section class="page-hero">
    <div class="container page-hero__content">
        <span class="hero__eyebrow">FAQs</span>
        <h1>Frequently Asked Questions</h1>
        <p>Find quick answers to the most common questions about <?php echo htmlspecialchars($siteName); ?>.</p>
    </div>
</section>

<section class="section section--content">
    <div class="container faq-list">
        <article class="faq-item">
            <h2>How do I open an account?</h2>
            <p>Click "Open Account" from the main navigation, complete the digital onboarding form, upload your KYC documents, and a specialist will verify your profile within 24 hours.</p>
        </article>
        <article class="faq-item">
            <h2>Can non-U.S. citizens open accounts?</h2>
            <p>Yes. <?php echo htmlspecialchars($siteName); ?> serves clients worldwide. Provide a valid passport, proof of address, and source-of-funds documentation during onboarding.</p>
        </article>
        <article class="faq-item">
            <h2>What currencies do you support?</h2>
            <p>We support 40+ currencies including USD, EUR, GBP, JPY, AED, SGD, and more. Multi-currency wallets let you hold, convert, and transfer seamlessly.</p>
        </article>
        <article class="faq-item">
            <h2>Are crypto and forex investments insured?</h2>
            <p>Digital assets are safeguarded with insured custody partners. Forex positions include negative balance protection and margin controls.</p>
        </article>
        <article class="faq-item">
            <h2>How secure are my online transactions?</h2>
            <p>Every transaction uses 256-bit SSL encryption, multi-factor authentication, and real-time fraud monitoring to protect your funds.</p>
        </article>
    </div>
</section>
</div>

<?php include __DIR__ . '/views/layouts/footer.php'; ?>

