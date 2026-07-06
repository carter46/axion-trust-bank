<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

// Get dynamic branding
$siteName = getSiteName();
$siteInitials = getSiteInitials();
$pageTitle = 'Accounts | ' . $siteName;

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
    background-image: url('<?php echo SITE_URL . '/uploads/images/' . rawurlencode('man-woman-looking-clipboard copy.jpg'); ?>');
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

.public-page-wrapper .feature-block__content ul {
    list-style: none;
    padding: 0;
    margin: 2rem 0;
}

.public-page-wrapper .feature-block__content ul li {
    font-size: 1.6rem;
    line-height: 1.8;
    color: #6b747f;
    padding: 0.8rem 0;
    padding-left: 2.5rem;
    position: relative;
}

.public-page-wrapper .feature-block__content ul li::before {
    content: '✓';
    position: absolute;
    left: 0;
    color: #359eb4;
    font-weight: 600;
}

/* ===== CONTENT BLOCK STYLES ===== */
.public-page-wrapper .content-block {
    max-width: 900px;
    margin: 0 auto;
    text-align: center;
}

.public-page-wrapper .section-heading {
    font-size: clamp(2.8rem, 4vw, 4rem);
    font-weight: 700;
    letter-spacing: -0.02em;
    line-height: 1.3;
    color: #232e3a;
    margin-bottom: 2rem;
    text-align: center;
}

.public-page-wrapper .section-description {
    font-size: clamp(1.6rem, 1.8vw, 1.8rem);
    line-height: 1.6;
    color: #6b747f;
    margin-bottom: 2rem;
    text-align: center;
}

.public-page-wrapper .media-list {
    list-style: none;
    padding: 0;
    margin: 2rem 0;
    text-align: left;
}

.public-page-wrapper .media-list li {
    font-size: clamp(1.6rem, 1.8vw, 1.8rem);
    line-height: 1.8;
    color: #232e3a;
    padding: 0.8rem 0;
    padding-left: 2.5rem;
    position: relative;
}

.public-page-wrapper .media-list li::before {
    content: '✓';
    position: absolute;
    left: 0;
    color: #359eb4;
    font-weight: 700;
    font-size: 1.8rem;
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

/* ===== MEDIA SECTIONS - DESKTOP ===== */
.public-page-wrapper .media-section {
    background-color: #f8f9fa;
    padding: 6rem 0;
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

.public-page-wrapper .media-layout--reverse {
    grid-template-columns: 1fr 1fr;
}

.public-page-wrapper .media-layout--reverse .media-content {
    order: 2;
}

.public-page-wrapper .media-layout--reverse .media-image {
    order: 1;
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
    font-size: clamp(1.6rem, 1.8vw, 1.8rem);
    line-height: 1.6;
    color: #6b747f;
    margin-bottom: 2rem;
}

.public-page-wrapper .media-image {
    width: 100%;
    height: 400px;
    border-radius: 1.2rem;
    overflow: hidden;
}

.public-page-wrapper .media-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.public-page-wrapper .get-started-btn {
    display: inline-block;
    background: linear-gradient(135deg, #359eb4, #2a7a8a);
    color: #ffffff;
    font-size: 1.6rem;
    font-weight: 600;
    padding: 1.2rem 3rem;
    border-radius: 0.8rem;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.public-page-wrapper .get-started-btn:hover {
    background: linear-gradient(135deg, #2a7a8a, #359eb4);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(53, 158, 180, 0.3);
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
    
    .public-page-wrapper .section-product-landing {
        flex-direction: column;
        gap: 2rem;
    }
    
    .public-page-wrapper .product-landing {
        max-width: 100%;
    }
    
    .public-page-wrapper .media-layout {
        grid-template-columns: 1fr;
        gap: 3rem;
    }
    
    .public-page-wrapper .media-layout--reverse .media-content {
        order: 1;
    }
    
    .public-page-wrapper .media-layout--reverse .media-image {
        order: 2;
    }
    
    .public-page-wrapper .media-image {
        height: 300px;
    }
    
    .public-page-wrapper .media-section {
        padding: 4rem 0;
    }
}
</style>

<div class="public-page-wrapper">
<!-- Hero Section -->
<section class="page-hero">
    <div class="container page-hero__content">
        <span class="hero__eyebrow">Accounts</span>
        <h1>Powerful Accounts for Global Banking</h1>
        <p>Checking, savings, and business accounts built for cross-border finance.</p>
    </div>
</section>

<!-- Account Types Overview Section -->
<section class="section">
    <div class="section-container">
        <h2 class="section-heading">🏦 Account Types</h2>
        <p class="section-description">Choose the perfect account for your personal or business banking needs.</p>
        
        <div class="section-product-landing">
            <div class="product-landing">
                <h3>Checking Account</h3>
                <p>Everyday banking made effortless. Manage deposits, bills, and payments from anywhere with instant notifications and real-time balance updates.</p>
                <ul class="media-list" style="margin: 1.5rem 0;">
                    <li>Multi-currency support with competitive exchange rates</li>
                    <li>Unlimited domestic and international transfers</li>
                    <li>Integrated bill pay and scheduled payments</li>
                    <li>Real-time balance updates</li>
                    <li>Contactless & mobile wallet support</li>
                </ul>
                <a href="<?php echo SITE_URL; ?>/auth/register" class="get-started-btn" style="display: inline-block; margin-top: 1rem; text-decoration: none;">Open Account →</a>
            </div>
            
            <div class="product-landing">
                <h3>Savings Account</h3>
                <p>Secure savings with competitive interest and global access. Grow your wealth with tiered rates, automated goals, and investor-grade security.</p>
                <ul class="media-list" style="margin: 1.5rem 0;">
                    <li>High-yield rates with flexible terms</li>
                    <li>Goal tracking and automated contributions</li>
                    <li>FDIC-equivalent offshore insurance protection</li>
                    <li>Instant transfer flexibility</li>
                    <li>Multi-currency savings options</li>
                </ul>
                <a href="<?php echo SITE_URL; ?>/auth/register" class="get-started-btn" style="display: inline-block; margin-top: 1rem; text-decoration: none;">Start Saving →</a>
            </div>
            
            <div class="product-landing">
                <h3>Business Account</h3>
                <p>Designed for international entrepreneurs with multi-currency support, offshore compliance tools, and treasury-grade controls.</p>
                <ul class="media-list" style="margin: 1.5rem 0;">
                    <li>Dedicated IBANs and SWIFT access in 40+ currencies</li>
                    <li>Role-based approvals and corporate card controls</li>
                    <li>Seamless integrations with ERP and accounting systems</li>
                    <li>USD/EUR/GBP/Naira support</li>
                    <li>Integrated invoicing & payroll</li>
                </ul>
                <a href="<?php echo SITE_URL; ?>/auth/register" class="get-started-btn" style="display: inline-block; margin-top: 1rem; text-decoration: none;">Open Business Account →</a>
            </div>
        </div>
    </div>
</section>

<!-- Multi-Currency Banking Section -->
<section class="section media-section">
    <div class="container media-layout">
        <div class="media-content">
            <h2>🌍 Multi-Currency Banking</h2>
            <p>Manage multiple currencies in a single account. Convert funds at competitive rates, hold balances in USD, EUR, GBP, and 40+ other currencies, all from one unified dashboard.</p>
            <ul class="media-list">
                <li>Real-time currency conversion at interbank rates</li>
                <li>Multi-currency balances with instant transfers</li>
                <li>Currency hedging options for businesses</li>
                <li>Global payment routing and SWIFT integration</li>
            </ul>
            <a href="<?php echo SITE_URL; ?>/auth/register" class="get-started-btn" style="display: inline-block; margin-top: 2rem; text-decoration: none;">Get Started →</a>
        </div>
        <div class="media-image">
            <img src="<?php echo SITE_URL . '/uploads/images/' . rawurlencode('man-woman-looking-clipboard copy.jpg'); ?>" alt="<?php echo htmlspecialchars($siteName . ' multi-currency accounts'); ?>" loading="lazy">
        </div>
    </div>
</section>

<!-- Features & Benefits Section -->
<section class="section">
    <div class="section-container">
        <div class="content-block">
            <h2 class="section-heading">Why Choose <?php echo htmlspecialchars($siteName); ?> Accounts?</h2>
            <p class="section-description">Experience banking designed for a connected world with cutting-edge technology and global reach.</p>
        </div>
        
        <div class="section-product-landing" style="margin-top: 3rem;">
            <div class="product-landing">
                <h3>🔒 Enterprise-Grade Security</h3>
                <p>256-bit SSL encryption, multi-factor authentication, and real-time fraud monitoring protect every transaction and account.</p>
            </div>
            
            <div class="product-landing">
                <h3>⚡ Instant Global Transfers</h3>
                <p>Send and receive money worldwide in minutes. Support for SWIFT, SEPA, and local payment networks across 190+ countries.</p>
            </div>
            
            <div class="product-landing">
                <h3>📱 Digital-First Experience</h3>
                <p>Full banking control from our mobile app or web platform. Open accounts, manage funds, and track spending anywhere.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section" style="background: linear-gradient(135deg, #359eb4, #232e3a); padding: 6rem 0; margin: 6rem 0;">
    <div class="section-container">
        <div class="content-block" style="color: #ffffff;">
            <h2 class="section-heading" style="color: #ffffff;">Ready to Get Started?</h2>
            <p class="section-description" style="color: rgba(255,255,255,0.9);">Open your account in minutes and start banking globally today.</p>
            <div style="display: flex; gap: 1.5rem; justify-content: center; margin-top: 3rem; flex-wrap: wrap;">
                <a href="<?php echo SITE_URL; ?>/auth/register" class="get-started-btn" style="background: #ffffff; color: #359eb4; text-decoration: none;">Open Account →</a>
                <a href="<?php echo SITE_URL; ?>/help-center" class="get-started-btn" style="background: transparent; border: 2px solid #ffffff; color: #ffffff; text-decoration: none;">Speak to Our Team →</a>
            </div>
        </div>
    </div>
</section>
</div>

<?php include __DIR__ . '/views/layouts/footer.php'; ?>
