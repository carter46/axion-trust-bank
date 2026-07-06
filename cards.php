<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

// Get dynamic branding
$siteName = getSiteName();
$siteInitials = getSiteInitials();
$pageTitle = 'Cards | ' . $siteName;

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
    background-image: url('<?php echo SITE_URL . '/uploads/images/' . rawurlencode('credit-card-finance-held-by-hand-banking-campaign.jpg'); ?>');
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

.public-page-wrapper .feature-block {
    max-width: 144rem;
    margin: 6rem auto;
    width: 100%;
    padding: 0 2rem;
}

.public-page-wrapper .feature-block__content {
    max-width: 900px;
    margin: 0 auto;
}

.public-page-wrapper .feature-block__content h2 {
    font-size: clamp(2.8rem, 4vw, 4rem);
    font-weight: 700;
    letter-spacing: -0.02em;
    line-height: 1.3;
    color: #232e3a;
    margin-bottom: 2rem;
}

.public-page-wrapper .feature-block__content p {
    font-size: 1.8rem;
    line-height: 1.6;
    color: #6b747f;
    margin-bottom: 2rem;
}

/* ===== PRODUCT LANDING CARDS - DESKTOP ===== */
.public-page-wrapper .section-product-landing {
    display: flex;
    gap: 2rem;
    flex-wrap: wrap;
    justify-content: center;
    margin-top: 3rem;
}

.public-page-wrapper .product-landing {
    flex: 1;
    min-width: 300px;
    max-width: 380px;
    background-color: #f8f9fa;
    padding: 3rem;
    border-radius: 1.2rem;
    transition: all 0.3s ease;
}

.public-page-wrapper .product-landing:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.public-page-wrapper .product-landing h3 {
    font-size: clamp(1.8rem, 2vw, 2.4rem);
    font-weight: 700;
    letter-spacing: -0.02em;
    line-height: 1.3;
    color: #232e3a;
    margin-bottom: 1.5rem;
}

.public-page-wrapper .product-landing p {
    font-size: clamp(1.6rem, 1.8vw, 1.8rem);
    line-height: 1.6;
    color: #6b747f;
    margin-bottom: 1.5rem;
}

/* ===== TRIPLE GRID STYLES ===== */
.public-page-wrapper .triple-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
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

.public-page-wrapper .info-card h3 {
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
    .public-page-wrapper .feature-block {
        padding: 0 2rem;
        margin: 4rem auto;
    }
    
    .public-page-wrapper .triple-grid {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .public-page-wrapper .product-landing {
        max-width: 100%;
    }
}
</style>

<div class="public-page-wrapper">
<section class="page-hero">
    <div class="container page-hero__content">
        <span class="hero__eyebrow">Cards</span>
        <h1>Wherever You Go, Your Card Goes Further</h1>
        <p>Globally accepted, secure, and packed with perks for every lifestyle.</p>
    </div>
</section>

<section class="section section--content">
    <div class="container feature-block">
        <div class="feature-block__content">
            <h2 id="virtual">Virtual Credit Cards</h2>
            <p>Instantly issued, tokenized cards for secure online payments. Set custom limits, freeze with a tap, and rotate numbers after each transaction.</p>
        </div>
    </div>
</section>

<section class="section section--alt">
    <div class="container feature-block">
        <div class="feature-block__content">
            <h2 id="physical">Physical Credit Cards</h2>
            <p>Premium metal and eco-friendly plastic options with travel rewards, lounge access, and concierge support – protected with chip, PIN, and biometric verification.</p>
        </div>
    </div>
</section>

<section class="section section--content">
    <div class="container feature-block">
        <div class="feature-block__content">
            <h2 id="debit">Debit Cards</h2>
            <p>Direct access to your funds with ATM rebates, multi-currency wallets, and zero foreign transaction fees on premium tiers.</p>
        </div>
    </div>
</section>

<section class="section section--grid">
    <div class="container triple-grid">
        <article class="info-card">
            <h3>Cashback Rewards</h3>
            <p>Earn up to 3% cashback on travel, dining, and international spends.</p>
        </article>
        <article class="info-card">
            <h3>Fraud Protection</h3>
            <p>AI-driven monitoring, instant alerts, and liability protection on every card.</p>
        </article>
        <article class="info-card">
            <h3>Smart Controls</h3>
            <p>Set spend limits, merchant categories, and geolocation rules for each card.</p>
        </article>
    </div>
</section>
</div>

<?php include __DIR__ . '/views/layouts/footer.php'; ?>

