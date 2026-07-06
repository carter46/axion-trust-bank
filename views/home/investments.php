<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

// Get dynamic branding
$siteName = getSiteName();
$siteInitials = getSiteInitials();
$pageTitle = 'Investments | ' . $siteName;

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
    background-image: url('<?php echo SITE_URL . '/uploads/images/' . rawurlencode('stacks-coins-arranged-bar-graph.jpg'); ?>');
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

.public-page-wrapper .triple-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 3rem;
    max-width: 144rem;
    margin: 6rem auto;
    width: 100%;
    padding: 0 2rem;
}

.public-page-wrapper .info-card {
    background-color: #f2f3f4;
    padding: 3rem;
    border-radius: 1.8rem;
    transition: transform 0.3s;
}

.public-page-wrapper .info-card:hover {
    transform: translateY(-0.5rem);
}

.public-page-wrapper .info-card h2 {
    font-size: 2.4rem;
    font-weight: 700;
    letter-spacing: -0.02em;
    line-height: 1.3;
    color: #232e3a;
    margin-bottom: 1.5rem;
}

.public-page-wrapper .info-card p {
    font-size: 1.6rem;
    line-height: 1.6;
    color: #6b747f;
}

.public-page-wrapper .media-section {
    margin: 6rem 0;
}

.public-page-wrapper .media-layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    align-items: center;
    max-width: 144rem;
    margin: 0 auto;
    padding: 0 2rem;
}

.public-page-wrapper .media-image {
    position: relative;
    width: 100%;
    height: 400px;
    overflow: hidden;
    border-radius: 1.8rem;
}

.public-page-wrapper .media-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.public-page-wrapper .media-content h2 {
    font-size: clamp(2.8rem, 4vw, 4rem);
    font-weight: 700;
    letter-spacing: -0.02em;
    line-height: 1.3;
    color: #232e3a;
    margin-bottom: 2rem;
}

.public-page-wrapper .media-content p {
    font-size: 1.8rem;
    line-height: 1.6;
    color: #6b747f;
    margin-bottom: 2rem;
}

.public-page-wrapper .media-list {
    list-style: none;
    padding: 0;
    margin: 2rem 0;
}

.public-page-wrapper .media-list li {
    font-size: 1.6rem;
    line-height: 1.8;
    color: #6b747f;
    padding: 0.8rem 0;
    padding-left: 2.5rem;
    position: relative;
}

.public-page-wrapper .media-list li::before {
    content: '✓';
    position: absolute;
    left: 0;
    color: #359eb4;
    font-weight: 600;
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
    .public-page-wrapper .triple-grid,
    .public-page-wrapper .media-layout {
        padding: 0 2rem;
        margin: 4rem auto;
    }
    
    .public-page-wrapper .triple-grid {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .public-page-wrapper .media-layout {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .public-page-wrapper .media-image {
        height: 300px;
    }
    
    .public-page-wrapper .info-card {
        padding: 2rem;
    }
}

@media (max-width: 767px) {
    .public-page-wrapper .section-container > div[style*="grid-template-columns: repeat(3"] {
        grid-template-columns: 1fr !important;
        gap: 2rem !important;
    }
}
</style>

<div class="public-page-wrapper">
<!-- Hero Section -->
<section class="page-hero">
    <div class="section-container page-hero__content">
        <span class="hero__eyebrow">Investments</span>
        <h1>Trade, Diversify, and Grow with Confidence</h1>
        <p>Unlock global markets, crypto assets, and forex from one secure platform.</p>
    </div>
</section>

<!-- Investments Grid Section -->
<section class="section">
    <div class="section-container">
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 3rem; max-width: 1200px; margin: 0 auto;">
            <article class="info-card" id="stocks" style="background: #ffffff; padding: 3rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);">
                <h2 style="font-size: clamp(2rem, 2.5vw, 2.4rem); font-weight: 700; color: #1a2b5f; margin-bottom: 1.5rem;">Stocks &amp; ETFs</h2>
                <p style="font-size: clamp(1.6rem, 1.8vw, 1.8rem); line-height: 1.8; color: #475569;">Trade U.S. and global exchanges with advanced order types, real-time data, and analyst-grade research.</p>
            </article>
            <article class="info-card" id="crypto" style="background: #ffffff; padding: 3rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);">
                <h2 style="font-size: clamp(2rem, 2.5vw, 2.4rem); font-weight: 700; color: #1a2b5f; margin-bottom: 1.5rem;">Crypto Portfolio</h2>
                <p style="font-size: clamp(1.6rem, 1.8vw, 1.8rem); line-height: 1.8; color: #475569;">Buy, hold, and stake leading cryptocurrencies with cold-storage custody and institutional-grade security.</p>
            </article>
            <article class="info-card" id="forex" style="background: #ffffff; padding: 3rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);">
                <h2 style="font-size: clamp(2rem, 2.5vw, 2.4rem); font-weight: 700; color: #1a2b5f; margin-bottom: 1.5rem;">Forex Trading</h2>
                <p style="font-size: clamp(1.6rem, 1.8vw, 1.8rem); line-height: 1.8; color: #475569;">Access real-time FX tools, smart hedging strategies, and deep liquidity across major and exotic pairs.</p>
            </article>
        </div>
    </div>
</section>

<!-- Media Section -->
<section class="section media-section">
    <div class="section-container">
        <div class="media-layout">
            <div class="media-content">
                <h2>Professional Dashboard</h2>
                <p>Monitor performance, set automated alerts, and track gains with our customizable trading workstation.</p>
                <ul class="media-list">
                    <li>Multi-asset watchlists and scenario planning</li>
                    <li>Risk analytics, Value-at-Risk, and portfolio heat maps</li>
                    <li>Downloadable statements and tax-ready reports</li>
                </ul>
            </div>
            <div class="media-image">
                <img src="<?php echo SITE_URL . '/uploads/images/' . rawurlencode('luxury-yacht-elegant-interior-design-travel-comfort-generated-by-ai copy.jpg'); ?>" alt="Investment lifestyle" loading="lazy">
            </div>
        </div>
    </div>
</section>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
