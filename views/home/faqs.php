<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

// Get dynamic branding
$siteName = getSiteName();
$siteInitials = getSiteInitials();
$pageTitle = 'FAQs | ' . $siteName;

include __DIR__ . '/../layouts/header.php';
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

/* ===== HERO SECTION ===== */
.public-page-wrapper .page-hero {
    background: linear-gradient(135deg, #1a2b5f 0%, #359eb4 100%);
    min-height: 50vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 8rem 2rem;
    position: relative;
}

.public-page-wrapper .page-hero__content {
    text-align: center;
    color: #ffffff;
    max-width: 800px;
    margin: 0 auto;
}

.public-page-wrapper .hero__eyebrow {
    font-size: 1.4rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    opacity: 0.9;
    margin-bottom: 1rem;
    display: block;
}

.public-page-wrapper .page-hero h1 {
    font-size: clamp(3.2rem, 5vw, 5rem);
    font-weight: 700;
    margin-bottom: 1.5rem;
    color: #ffffff;
}

.public-page-wrapper .page-hero p {
    font-size: clamp(1.6rem, 2vw, 1.8rem);
    line-height: 1.6;
    opacity: 0.95;
}

/* ===== CONTENT SECTIONS ===== */
.public-page-wrapper .section {
    padding: 6rem 0;
    position: relative;
}

.public-page-wrapper .section-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}

.public-page-wrapper .faq-list {
    max-width: 900px;
    margin: 0 auto;
}

.public-page-wrapper .faq-item {
    padding: 2rem 0;
    border-bottom: 1px solid #e5e7eb;
}

.public-page-wrapper .faq-item:last-child {
    border-bottom: none;
}

.public-page-wrapper .faq-item h2 {
    font-size: clamp(2.4rem, 3vw, 3rem);
    font-weight: 700;
    color: #1a2b5f;
    margin-bottom: 1rem;
}

.public-page-wrapper .faq-item p {
    font-size: clamp(1.6rem, 1.8vw, 1.8rem);
    line-height: 1.8;
    color: #475569;
}

@media (max-width: 767px) {
    .public-page-wrapper .page-hero {
        min-height: 40vh;
        padding: 4rem 2rem;
    }
    
    .public-page-wrapper .section {
        padding: 4rem 0;
    }
    
    .public-page-wrapper .section-container {
        padding: 0 2rem;
    }
}
</style>

<div class="public-page-wrapper">
<section class="page-hero">
    <div class="section-container page-hero__content">
        <span class="hero__eyebrow">FAQs</span>
        <h1>Frequently Asked Questions</h1>
        <p>Find quick answers to the most common questions about <?php echo htmlspecialchars($siteName); ?>.</p>
    </div>
</section>

<section class="section">
    <div class="section-container">
        <div class="faq-list">
        <article class="faq-item">
            <h2>How do I open an account?</h2>
            <p>Click “Open Account” from the main navigation, complete the digital onboarding form, upload your KYC documents, and a specialist will verify your profile within 24 hours.</p>
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
    </div>
</section>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>

