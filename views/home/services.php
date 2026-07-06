<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

// Get dynamic branding
$siteName = getSiteName();
$siteInitials = getSiteInitials();
$pageTitle = 'Services | ' . $siteName;

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
    background-image: url('<?php echo SITE_URL . '/uploads/images/' . rawurlencode('co-working-people-working-together copy.jpg'); ?>');
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

.public-page-wrapper .section-heading {
    font-size: clamp(2.8rem, 4vw, 4.8rem);
    font-weight: 700;
    letter-spacing: -0.02em;
    line-height: 1.3;
    text-align: center;
    margin-bottom: 2rem;
    color: #232e3a;
}

.public-page-wrapper .section-description {
    text-align: center;
    font-size: clamp(1.6rem, 1.8vw, 1.8rem);
    line-height: 1.5;
    margin-bottom: 3rem;
    color: #6b747f;
}

/* ===== PRODUCT CARDS GRID - DESKTOP ===== */
.public-page-wrapper .section-product-landing {
    display: flex;
    flex-wrap: wrap;
    gap: 3.2rem;
    justify-content: space-between;
    align-items: stretch;
}

.public-page-wrapper .product-landing {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    background-color: #f2f3f4;
    padding: 2.4rem;
    border-radius: 1.8rem;
    gap: 1.5rem;
    transition: all .4s;
    flex: 1;
    min-width: 300px;
    max-width: 380px;
    box-sizing: border-box;
}

.public-page-wrapper .product-landing:hover {
    transform: translateY(-0.5rem);
}

.public-page-wrapper .product-landing h2 {
    font-size: 2rem;
    font-weight: 600;
    line-height: 1.3;
    color: #232e3a;
    margin-bottom: 1rem;
}

.public-page-wrapper .product-landing p {
    font-size: 1.6rem;
    line-height: 1.5;
    color: #6b747f;
}

/* ===== MEDIA SECTION - DESKTOP ===== */
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
    width: 100%;
    height: 400px;
    border-radius: 1.8rem;
    overflow: hidden;
}

.public-page-wrapper .media-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
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
    margin: 0;
}

.public-page-wrapper .media-list li {
    font-size: 1.6rem;
    line-height: 1.8;
    color: #232e3a;
    padding: 1rem 0;
    padding-left: 2.5rem;
    position: relative;
}

.public-page-wrapper .media-list li:before {
    content: "✓";
    position: absolute;
    left: 0;
    color: #359eb4;
    font-weight: 700;
    font-size: 1.8rem;
}

/* ===== FEATURE BLOCK - DESKTOP ===== */
.public-page-wrapper .feature-block {
    max-width: 144rem;
    margin: 6rem auto;
    padding: 0 2rem;
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
    margin: 0;
}

.public-page-wrapper .feature-block__content ul li {
    font-size: 1.6rem;
    line-height: 1.8;
    color: #232e3a;
    padding: 1rem 0;
    padding-left: 2.5rem;
    position: relative;
}

.public-page-wrapper .feature-block__content ul li:before {
    content: "✓";
    position: absolute;
    left: 0;
    color: #359eb4;
    font-weight: 700;
    font-size: 1.8rem;
}

/* ===== MOBILE STYLES ===== */
@media (max-width: 767px) {
    .public-page-wrapper .page-hero {
        min-height: 50vh;
        padding: 4rem 2rem;
    }
    
    .public-page-wrapper .page-hero h1 {
        font-size: 2.8rem;
    }
    
    .public-page-wrapper .page-hero p {
        font-size: 1.6rem;
    }
    
    .public-page-wrapper .media-layout {
        grid-template-columns: 1fr;
        gap: 3rem;
    }
    
    .public-page-wrapper .product-landing {
        min-width: 100%;
        max-width: 100%;
    }
    
    .public-page-wrapper .section-container {
        padding: 0 2rem;
    }
    
    .public-page-wrapper .media-image {
        height: 300px;
    }
}

/* ===== TABLET/DESKTOP ===== */
@media (min-width: 768px) {
    .public-page-wrapper .media-layout--reverse {
        grid-template-columns: 1fr 1fr;
    }
    
    .public-page-wrapper .media-layout--reverse .media-image {
        order: 2;
    }
    
    .public-page-wrapper .media-layout--reverse .media-content {
        order: 1;
    }
}
</style>

<div class="public-page-wrapper">
<!-- Hero Section -->
<section class="page-hero">
    <div class="container page-hero__content">
        <h1>Global Banking. One Seamless Platform.</h1>
        <p>From daily banking to wealth management, <?php echo htmlspecialchars($siteName); ?> delivers secure, borderless, and technology-driven financial solutions for individuals, businesses, and institutions worldwide.</p>
        <div style="display: flex; gap: 1.5rem; justify-content: center; margin-top: 3rem; flex-wrap: wrap;">
            <a href="<?php echo SITE_URL; ?>/auth/register" class="get-started-btn" style="display: inline-block; text-decoration: none;">Open an Account</a>
            <a href="<?php echo SITE_URL; ?>/help-center" class="login-btn" style="display: inline-block; padding: 10px 20px; border: 2px solid #ffffff; border-radius: 6px; color: #ffffff; text-decoration: none;">Speak with an Advisor</a>
        </div>
    </div>
</section>

<!-- Accounts Section -->
<section class="section">
    <div class="section-container">
        <h2 class="section-heading">🏦 Accounts</h2>
        <div class="section-product-landing">
            <div class="product-landing">
                <h3>Checking Accounts</h3>
                <p>Everyday banking made effortless — manage deposits, payments, and transfers anywhere, anytime.</p>
                <ul class="media-list" style="margin: 1.5rem 0;">
                    <li>Real-time balance updates</li>
                    <li>Contactless & mobile wallet support</li>
                </ul>
                <a href="<?php echo SITE_URL; ?>/accounts" class="nav-link" style="display: inline-block; margin-top: 1rem;">Learn more →</a>
            </div>
            <div class="product-landing">
                <h3>Savings Accounts</h3>
                <p>Save securely and earn competitive rates while keeping global access to your funds.</p>
                <ul class="media-list" style="margin: 1.5rem 0;">
                    <li>Tiered interest options</li>
                    <li>Instant transfer flexibility</li>
                </ul>
                <a href="<?php echo SITE_URL; ?>/accounts" class="nav-link" style="display: inline-block; margin-top: 1rem;">Learn more →</a>
            </div>
            <div class="product-landing">
                <h3>Business Accounts</h3>
                <p>Built for global entrepreneurs with multi-currency and offshore compliance tools.</p>
                <ul class="media-list" style="margin: 1.5rem 0;">
                    <li>USD/EUR/GBP/Naira support</li>
                    <li>Integrated invoicing & payroll</li>
                </ul>
                <a href="<?php echo SITE_URL; ?>/accounts" class="nav-link" style="display: inline-block; margin-top: 1rem;">Learn more →</a>
            </div>
    </div>
    </div>
</section>

<!-- Cards Section -->
<section class="section media-section">
    <div class="container media-layout">
        <div class="media-content">
            <h2>💳 Credit & Debit Cards</h2>
            <p>Physical, virtual, and travel cards engineered for global acceptance and total control.</p>
            <ul class="media-list">
                <li>Instant virtual issuance for online use</li>
                <li>0% foreign transaction fees on premium tiers</li>
                <li>Real-time spend notifications</li>
                <li>AI-driven fraud protection</li>
            </ul>
            <div style="margin-top: 2rem;">
                <h3 style="font-size: 1.8rem; margin-bottom: 1rem;">Prepaid & Travel Cards</h3>
                <p>Perfect for international travelers with multi-currency loading and instant freeze/unfreeze.</p>
                <ul class="media-list">
                    <li>Cashback on foreign spend</li>
                    <li>In-app controls & tracking</li>
                </ul>
                <a href="<?php echo SITE_URL; ?>/cards" class="get-started-btn" style="display: inline-block; margin-top: 1.5rem; text-decoration: none;">Apply for a Card →</a>
            </div>
        </div>
        <div class="media-image">
            <img src="<?php echo SITE_URL . '/uploads/images/' . rawurlencode('close-up-images-multiple-credit-card-handsets copy.jpg'); ?>" alt="<?php echo htmlspecialchars($siteName . ' cards'); ?>" loading="lazy">
        </div>
    </div>
</section>

<!-- Loans & Financing Section -->
<section class="section">
    <div class="section-container">
        <h2 class="section-heading">💼 Loans & Financing</h2>
        <div class="section-product-landing">
            <div class="product-landing">
                <h3>Personal Loans</h3>
                <p>Fast, flexible, and transparent lending designed for your life goals.</p>
                <ul class="media-list" style="margin: 1.5rem 0;">
                    <li>Personal, auto, and education loans</li>
                    <li>No hidden fees, no early repayment penalties</li>
                </ul>
            </div>
            <div class="product-landing">
                <h3>Mortgage Loans</h3>
                <p>Finance your property locally or offshore with competitive rates.</p>
                <ul class="media-list" style="margin: 1.5rem 0;">
                    <li>Fixed & adjustable options</li>
                    <li>Real-time tracking and digital documentation</li>
                </ul>
            </div>
            <div class="product-landing">
                <h3>Business Loans</h3>
                <p>Empower growth with working capital, trade, or asset financing.</p>
                <ul class="media-list" style="margin: 1.5rem 0;">
                    <li>Short-term and long-term options</li>
                    <li>Fast approval process with flexible repayment</li>
                </ul>
            </div>
        </div>
        <div style="text-align: center; margin-top: 3rem;">
            <a href="<?php echo SITE_URL; ?>/loans" class="get-started-btn" style="display: inline-block; text-decoration: none;">View Loan Options →</a>
        </div>
    </div>
</section>

<!-- Investments Section - Dark Theme -->
<section class="section" style="background: linear-gradient(135deg, #232e3a, #1a2b5f); color: #ffffff; padding: 6rem 0;">
    <div class="section-container">
        <h2 class="section-heading" style="color: #ffffff;">💹 Investments & Wealth</h2>
        <p class="section-description" style="color: rgba(255,255,255,0.9);">Empowering your portfolio with institutional-grade tools, insights, and security.</p>
        <div class="section-product-landing">
            <div class="product-landing" style="background-color: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);">
                <h3 style="color: #ffffff;">Global Investments</h3>
                <p style="color: rgba(255,255,255,0.9);">Trade stocks, bonds, ETFs, futures, and crypto—all in one secure platform.</p>
                <ul class="media-list" style="margin: 1.5rem 0;">
                    <li style="color: rgba(255,255,255,0.9);">Access 190+ markets</li>
                    <li style="color: rgba(255,255,255,0.9);">Smart allocation & rebalancing tools</li>
                </ul>
            </div>
            <div class="product-landing" style="background-color: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);">
                <h3 style="color: #ffffff;">Wealth Management</h3>
                <p style="color: rgba(255,255,255,0.9);">Tailored portfolio management for high-net-worth clients.</p>
                <ul class="media-list" style="margin: 1.5rem 0;">
                    <li style="color: rgba(255,255,255,0.9);">Risk profiling</li>
                    <li style="color: rgba(255,255,255,0.9);">Tax and estate planning</li>
                    <li style="color: rgba(255,255,255,0.9);">Personal advisory</li>
                </ul>
            </div>
            <div class="product-landing" style="background-color: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);">
                <h3 style="color: #ffffff;">Financial Advisory</h3>
                <p style="color: rgba(255,255,255,0.9);">Dedicated advisors for strategic and global financial guidance.</p>
                <ul class="media-list" style="margin: 1.5rem 0;">
                    <li style="color: rgba(255,255,255,0.9);">Treasury & liquidity management</li>
                    <li style="color: rgba(255,255,255,0.9);">Offshore corporate structuring</li>
            </ul>
            </div>
        </div>
        <div style="text-align: center; margin-top: 3rem;">
            <a href="<?php echo SITE_URL; ?>/investments" class="get-started-btn" style="display: inline-block; text-decoration: none; background: #359eb4;">Explore Investment Solutions →</a>
        </div>
    </div>
</section>

<!-- International Banking Section -->
<section class="section media-section">
    <div class="container media-layout media-layout--reverse">
        <div class="media-content">
            <h2>🌍 International & Offshore Banking</h2>
            <p>Expand your reach with compliant, secure offshore accounts and global payments.</p>
            <ul class="media-list">
                <li>Open multi-currency offshore accounts</li>
                <li>Access SWIFT, SEPA, and blockchain-enabled rails</li>
                <li>Automate cross-border transactions in 50+ currencies</li>
                <li>Dedicated relationship managers and reporting tools</li>
            </ul>
            <a href="<?php echo SITE_URL; ?>/accounts" class="get-started-btn" style="display: inline-block; margin-top: 2rem; text-decoration: none;">Open a Global Account →</a>
        </div>
        <div class="media-image">
            <img src="<?php echo SITE_URL . '/uploads/images/' . rawurlencode('brown-high-skyscrapers copy.jpg'); ?>" alt="International Banking" loading="lazy" onerror="this.src='<?php echo SITE_URL . '/uploads/images/' . rawurlencode('businesswomen-going-work-together-city-while-talking-drinking-coffee copy.jpg'); ?>'">
        </div>
    </div>
</section>

<!-- Mobile & Online Banking Section -->
<section class="section media-section">
    <div class="container media-layout">
        <div class="media-content">
            <h2>📱 Mobile & Online Banking</h2>
            <p>Your entire bank—on your phone, tablet, or laptop.</p>
            <ul class="media-list">
                <li>Biometric login & 2FA</li>
                <li>Instant payments & transfers</li>
                <li>Customizable dashboard & notifications</li>
                <li>Real-time transaction tracking</li>
            </ul>
            <a href="#" class="get-started-btn" style="display: inline-block; margin-top: 2rem; text-decoration: none;">Download App →</a>
        </div>
        <div class="media-image">
            <img src="<?php echo SITE_URL . '/uploads/images/' . rawurlencode('credit-card-finance-held-by-hand-banking-campaign.jpg'); ?>" alt="Mobile Banking" loading="lazy" onerror="this.src='<?php echo SITE_URL . '/uploads/images/' . rawurlencode('co-working-people-working-together copy.jpg'); ?>'">
        </div>
    </div>
</section>

<!-- Sustainability & Charity Section -->
<section class="page-hero" style="min-height: 50vh; margin: 6rem 0; background-image: url('<?php echo SITE_URL . '/uploads/images/' . rawurlencode('happy-hispanic-young-man-smiling.jpg'); ?>'); background-size: cover; background-position: center; position: relative;">
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: radial-gradient(circle at 100% -20%, rgba(53, 158, 180, 0.85), rgba(48, 80, 102, 0.9), rgba(35, 46, 58, 0.95), rgba(35, 46, 58, 0.98)); z-index: 0; pointer-events: none;"></div>
    <div class="container page-hero__content" style="position: relative; z-index: 1;">
        <h2 style="font-size: clamp(2.8rem, 4vw, 4rem); color: #ffffff; margin-bottom: 2rem;">💖 Giving Back with Cosmos Charity Home</h2>
        <p style="font-size: clamp(1.6rem, 2vw, 1.8rem); color: #ffffff; max-width: 800px; margin: 0 auto 3rem;">Through the Cosmos Charity Home, we provide education, food, and shelter for orphans and vulnerable communities across the globe. Your banking helps us make a difference—1% of <?php echo htmlspecialchars($siteInitials); ?>'s annual profits support humanitarian projects.</p>
        <a href="<?php echo SITE_URL; ?>/charity" class="get-started-btn" style="display: inline-block; text-decoration: none; background: #359eb4;">Visit Charity Page →</a>
    </div>
</section>

<!-- Why Choose <?php echo htmlspecialchars($siteInitials); ?> Section -->
<section class="section">
    <div class="section-container">
        <h2 class="section-heading">Why Choose <?php echo htmlspecialchars($siteName); ?></h2>
        <div class="section-product-landing" style="justify-content: center;">
            <div class="product-landing">
                <h3>🌐 Licensed in multiple jurisdictions</h3>
                <p>Operate with confidence across global markets with full regulatory compliance.</p>
            </div>
            <div class="product-landing">
                <h3>🔒 End-to-end encryption & AI-driven fraud protection</h3>
                <p>Your data and funds are protected with military-grade security standards.</p>
            </div>
            <div class="product-landing">
                <h3>💬 24/7 multilingual global support</h3>
                <p>Get help anytime, anywhere, in your preferred language.</p>
            </div>
            <div class="product-landing">
                <h3>💼 Dedicated relationship managers</h3>
                <p>Personalized service for premium clients and businesses.</p>
            </div>
            <div class="product-landing">
                <h3>📊 Smart dashboards & financial insights</h3>
                <p>Make informed decisions with real-time analytics and reporting.</p>
            </div>
        </div>
        <div style="text-align: center; margin-top: 3rem;">
            <a href="<?php echo SITE_URL; ?>/auth/register" class="get-started-btn" style="display: inline-block; text-decoration: none;">Get Started →</a>
        </div>
    </div>
</section>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
