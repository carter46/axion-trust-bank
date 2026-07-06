<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

// Get dynamic branding
$siteName = getSiteName();
$siteInitials = getSiteInitials();
$pageTitle = 'Security | ' . $siteName;

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

.public-page-wrapper .double-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 3rem;
    max-width: 1200px;
    margin: 0 auto;
}

.public-page-wrapper .info-card {
    background: #ffffff;
    padding: 3rem;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.public-page-wrapper .info-card h2 {
    font-size: clamp(2rem, 2.5vw, 2.4rem);
    font-weight: 700;
    color: #1a2b5f;
    margin-bottom: 1.5rem;
}

.public-page-wrapper .info-card p {
    font-size: clamp(1.6rem, 1.8vw, 1.8rem);
    line-height: 1.8;
    color: #475569;
}

.public-page-wrapper .feature-block {
    max-width: 900px;
    margin: 0 auto;
    padding: 3rem;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.public-page-wrapper .feature-block__content h2 {
    font-size: clamp(2.4rem, 3vw, 3rem);
    font-weight: 700;
    color: #1a2b5f;
    margin-bottom: 1.5rem;
}

.public-page-wrapper .feature-block__content p {
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
    <div class="section-container page-hero__content">
        <span class="hero__eyebrow">Security</span>
        <h1>Your Security Comes First</h1>
        <p><?php echo htmlspecialchars($siteName); ?> protects every transaction with enterprise-grade technology and offshore compliance.</p>
    </div>
</section>

<section class="section">
    <div class="section-container">
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

<section class="section">
    <div class="section-container">
        <div class="feature-block">
            <div class="feature-block__content">
                <h2>Security Operations Center</h2>
                <p>Global SOC teams monitor infrastructure in real time, performing proactive penetration testing and vulnerability assessments.</p>
            </div>
        </div>
    </div>
</section>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>

