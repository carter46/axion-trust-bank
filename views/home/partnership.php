<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

// Get dynamic branding
$siteName = getSiteName();
$siteInitials = getSiteInitials();
$siteEmail = getSiteEmail();
$pageTitle = 'Partnerships | ' . $siteName;

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
    background-image: url('<?php echo SITE_URL . '/uploads/images/' . rawurlencode('brown-high-skyscrapers copy.jpg'); ?>');
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
    max-width: 900px;
    margin-left: auto;
    margin-right: auto;
}

/* ===== PARTNERSHIP CARDS GRID - DESKTOP ===== */
.public-page-wrapper .partners-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 3rem;
    max-width: 144rem;
    margin: 4rem auto;
    width: 100%;
    padding: 0 2rem;
}

.public-page-wrapper .partner-card {
    background-color: #f2f3f4;
    padding: 3rem;
    border-radius: 1.8rem;
    transition: all 0.3s;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 1.5rem;
    box-sizing: border-box;
}

.public-page-wrapper .partner-card:hover {
    transform: translateY(-0.5rem);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.public-page-wrapper .partner-logo {
    width: 100%;
    max-width: 180px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
}

.public-page-wrapper .partner-logo img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    filter: grayscale(0);
    transition: filter 0.3s;
}

.public-page-wrapper .partner-card:hover .partner-logo img {
    filter: grayscale(0);
}

.public-page-wrapper .partner-card h3 {
    font-size: 2rem;
    font-weight: 700;
    letter-spacing: -0.02em;
    line-height: 1.3;
    color: #232e3a;
    margin: 0;
}

.public-page-wrapper .partner-card p {
    font-size: 1.6rem;
    line-height: 1.6;
    color: #6b747f;
    margin: 0;
}

.public-page-wrapper .partner-card.placeholder {
    background-color: #e6e7e9;
}

.public-page-wrapper .partner-card.placeholder .partner-logo {
    background-color: #ffffff;
    border-radius: 0.8rem;
    padding: 1rem;
}

.public-page-wrapper .partner-card.placeholder .partner-logo::after {
    content: '🏢';
    font-size: 3rem;
}

/* ===== CTA SECTION - DESKTOP ===== */
.public-page-wrapper .cta-section {
    background: radial-gradient(farthest-corner circle at 120% 115%, #359eb4, #232e3a 65% 60%);
    padding: 6rem 2rem;
    text-align: center;
    margin: 6rem 0;
}

.public-page-wrapper .cta-section__inner {
    max-width: 800px;
    margin: 0 auto;
}

.public-page-wrapper .cta-section h2 {
    font-size: clamp(2.8rem, 4vw, 4rem);
    font-weight: 700;
    letter-spacing: -0.02em;
    line-height: 1.3;
    color: #ffffff;
    margin-bottom: 2rem;
}

.public-page-wrapper .cta-section p {
    font-size: clamp(1.6rem, 1.8vw, 1.8rem);
    line-height: 1.6;
    color: #ffffff;
    margin-bottom: 3rem;
}

.public-page-wrapper .cta-section a {
    display: inline-block;
    color: #359eb4;
    font-size: 1.8rem;
    font-weight: 600;
    text-decoration: none;
    background-color: #ffffff;
    padding: 1.4rem 3rem;
    border-radius: 1rem;
    transition: all 0.3s;
}

.public-page-wrapper .cta-section a:hover {
    background-color: #f2f3f4;
    transform: translateY(-2px);
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
    
    .public-page-wrapper .partners-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 2rem;
        padding: 0 2rem;
    }
    
    .public-page-wrapper .partner-logo {
        max-width: 120px;
        height: 60px;
    }
    
    .public-page-wrapper .partner-card h3 {
        font-size: 1.8rem;
    }
    
    .public-page-wrapper .partner-card p {
        font-size: 1.4rem;
    }
    
    .public-page-wrapper .cta-section {
        padding: 4rem 2rem;
    }
}

@media (max-width: 480px) {
    .public-page-wrapper .partners-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="public-page-wrapper">
<!-- Hero Section -->
<section class="page-hero">
    <div class="container page-hero__content">
        <span class="hero__eyebrow">Partnerships</span>
        <h1>Global Partnerships, Unified Vision</h1>
        <p>Building strong alliances with world-leading companies to drive innovation, security, and financial accessibility.</p>
    </div>
</section>

<!-- Financial & Banking Partners Section -->
<section id="financial-banking-partners" class="section">
    <div class="section-container">
        <h2 class="section-heading">🏦 Financial & Banking Partners</h2>
        <p class="section-description">Our financial alliances ensure global connectivity, compliance, and transaction efficiency.</p>
        
        <div class="partners-grid">
            <div id="visa" class="partner-card">
                <div class="partner-logo">
                    <img src="<?php echo SITE_URL . '/uploads/images/' . rawurlencode('visa logo.png'); ?>" alt="Visa" loading="lazy">
                </div>
                <h3>Visa</h3>
                <p>Enables seamless international payments and card issuance for our global customers.</p>
            </div>
            
            <div id="mastercard" class="partner-card">
                <div class="partner-logo">
                    <img src="<?php echo SITE_URL . '/uploads/images/' . rawurlencode('mastercard logo.png'); ?>" alt="Mastercard" loading="lazy">
                </div>
                <h3>Mastercard</h3>
                <p>Partnered for advanced card technology, digital wallets, and secure global transactions.</p>
            </div>
            
            <div id="american-express" class="partner-card">
                <div class="partner-logo">
                    <img src="<?php echo SITE_URL . '/uploads/images/' . rawurlencode('American-Express-Color logo.png'); ?>" alt="American Express" loading="lazy">
                </div>
                <h3>American Express</h3>
                <p>Supporting premium credit products and worldwide merchant acceptance.</p>
            </div>
            
            <div id="swift" class="partner-card">
                <div class="partner-logo">
                    <img src="<?php echo SITE_URL . '/uploads/images/' . rawurlencode('Swift-Logo.jpg'); ?>" alt="SWIFT" loading="lazy">
                </div>
                <h3>SWIFT</h3>
                <p>Powering our secure global transfer network across 200+ nations.</p>
            </div>
        </div>
    </div>
</section>

<!-- Technology & Fintech Partners Section -->
<section id="technology-fintech-partners" class="section">
    <div class="section-container">
        <h2 class="section-heading">💻 Technology & Fintech Partners</h2>
        <p class="section-description">We collaborate with leading tech innovators to build a future-ready digital bank.</p>
        
        <div class="partners-grid">
            <div id="microsoft-azure" class="partner-card">
                <div class="partner-logo">
                    <img src="<?php echo SITE_URL . '/uploads/images/' . rawurlencode('microsoft_azure_logo_icon.png'); ?>" alt="Microsoft Azure" loading="lazy">
                </div>
                <h3>Microsoft Azure</h3>
                <p>Provides our cloud infrastructure, ensuring data privacy, uptime, and scalability.</p>
            </div>
            
            <div id="aws" class="partner-card">
                <div class="partner-logo">
                    <img src="<?php echo SITE_URL . '/uploads/images/' . rawurlencode('amazon-web-services-logo.png'); ?>" alt="Amazon Web Services" loading="lazy">
                </div>
                <h3>Amazon Web Services (AWS)</h3>
                <p>Powers our secure backend systems and real-time banking operations.</p>
            </div>
            
            <div id="stripe" class="partner-card">
                <div class="partner-logo">
                    <img src="<?php echo SITE_URL . '/uploads/images/' . rawurlencode('Stripe_Logo.png'); ?>" alt="Stripe" loading="lazy">
                </div>
                <h3>Stripe</h3>
                <p>Enables businesses to accept and automate payments globally.</p>
            </div>
            
            <div id="paypal" class="partner-card">
                <div class="partner-logo">
                    <img src="<?php echo SITE_URL . '/uploads/images/' . rawurlencode('Paypal-Logo.png'); ?>" alt="PayPal" loading="lazy">
                </div>
                <h3>PayPal</h3>
                <p>Facilitates smooth digital wallet integration and secure online payments.</p>
            </div>
            
            <div id="revolut" class="partner-card">
                <div class="partner-logo">
                    <img src="<?php echo SITE_URL . '/uploads/images/' . rawurlencode('Revolut-Logo.png'); ?>" alt="Revolut" loading="lazy">
                </div>
                <h3>Revolut</h3>
                <p>Streamlining multi-currency transactions for personal and business accounts.</p>
            </div>
        </div>
    </div>
</section>

<!-- Investment & Advisory Partners Section -->
<section id="investment-advisory-partners" class="section">
    <div class="section-container">
        <h2 class="section-heading">💹 Investment & Advisory Partners</h2>
        <p class="section-description">Our investment partnerships deliver trusted expertise and market intelligence.</p>
        
        <div class="partners-grid">
            <div id="blackrock" class="partner-card">
                <div class="partner-logo">
                    <img src="<?php echo SITE_URL . '/uploads/images/' . rawurlencode('blackrock-logo-png-transparent.png'); ?>" alt="BlackRock" loading="lazy">
                </div>
                <h3>BlackRock</h3>
                <p>Offers global portfolio solutions and sustainable investment products.</p>
            </div>
            
            <div id="goldman-sachs" class="partner-card">
                <div class="partner-logo">
                    <img src="<?php echo SITE_URL . '/uploads/images/' . rawurlencode('goldman-sachs-logo.png'); ?>" alt="Goldman Sachs" loading="lazy">
                </div>
                <h3>Goldman Sachs</h3>
                <p>Collaborates on structured investments and capital advisory services.</p>
            </div>
            
            <div id="vanguard" class="partner-card">
                <div class="partner-logo">
                    <img src="<?php echo SITE_URL . '/uploads/images/' . rawurlencode('Vanguard-logo.png'); ?>" alt="Vanguard" loading="lazy">
                </div>
                <h3>Vanguard</h3>
                <p>Provides diversified investment options for long-term wealth growth.</p>
            </div>
            
            <div id="bloomberg" class="partner-card">
                <div class="partner-logo">
                    <img src="<?php echo SITE_URL . '/uploads/images/' . rawurlencode('Bloomberg-Logo.png'); ?>" alt="Bloomberg" loading="lazy">
                </div>
                <h3>Bloomberg</h3>
                <p>Supplies market analytics, news, and data for smarter investment decisions.</p>
            </div>
        </div>
    </div>
</section>

<!-- Corporate & Lifestyle Partners Section -->
<section id="corporate-lifestyle-partners" class="section">
    <div class="section-container">
        <h2 class="section-heading">🌍 Corporate & Lifestyle Partners</h2>
        <p class="section-description">We extend value beyond banking — offering convenience and access worldwide.</p>
        
        <div class="partners-grid">
            <div id="delta-airlines" class="partner-card">
                <div class="partner-logo">
                    <img src="<?php echo SITE_URL . '/uploads/images/' . rawurlencode('Delta-Air-Lines-Second-era-Logo.png'); ?>" alt="Delta Air Lines" loading="lazy">
                </div>
                <h3>Delta Air Lines</h3>
                <p>Partnered for exclusive travel rewards and global mobility programs.</p>
            </div>
            
            <div id="marriott" class="partner-card">
                <div class="partner-logo">
                    <img src="<?php echo SITE_URL . '/uploads/images/' . rawurlencode('marriott-international-logo-png_seeklogo-484457.png'); ?>" alt="Marriott International" loading="lazy">
                </div>
                <h3>Marriott International</h3>
                <p>Offers hospitality privileges and executive accommodation services.</p>
            </div>
            
            <div id="uber" class="partner-card">
                <div class="partner-logo">
                    <img src="<?php echo SITE_URL . '/uploads/images/' . rawurlencode('UberforBusiness_Logo_Black_RGB-1.png'); ?>" alt="Uber for Business" loading="lazy">
                </div>
                <h3>Uber for Business</h3>
                <p>Simplifies transportation and business travel management for our clients.</p>
            </div>
            
            <div id="deloitte" class="partner-card">
                <div class="partner-logo">
                    <img src="<?php echo SITE_URL . '/uploads/images/' . rawurlencode('Deloitte logo.png'); ?>" alt="Deloitte" loading="lazy">
                </div>
                <h3>Deloitte</h3>
                <p>Provides compliance and financial consulting for global operations.</p>
            </div>
            
            <div id="fedex" class="partner-card">
                <div class="partner-logo">
                    <img src="<?php echo SITE_URL . '/uploads/images/' . rawurlencode('FedEx_Express.svg.png'); ?>" alt="FedEx" loading="lazy">
                </div>
                <h3>FedEx</h3>
                <p>Supports logistics and document delivery across international borders.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section cta-section">
    <div class="cta-section__inner">
        <h2>🤝 Join Our Global Network</h2>
        <p>We're constantly expanding our partnerships across finance, technology, and global services.</p>
        <p>If your organization shares our vision of innovation, transparency, and borderless finance — let's connect.</p>
        <a href="mailto:<?php echo htmlspecialchars($siteEmail); ?>">📧 <?php echo htmlspecialchars($siteEmail); ?></a>
    </div>
</section>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>

