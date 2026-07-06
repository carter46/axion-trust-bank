<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

// Get dynamic branding
$siteName = getSiteName() ?: 'Cosmopolitan Trust Bank';
$siteInitials = getSiteInitials() ?: 'CTB';

$pageTitle = 'About ' . $siteName;

// Helper function to safely get image URL
function getImageUrl($filename) {
    $imagePath = __DIR__ . '/uploads/images/' . $filename;
    if (file_exists($imagePath)) {
        return SITE_URL . '/uploads/images/' . rawurlencode($filename);
    }
    // Return placeholder or alternative image if file doesn't exist
    return SITE_URL . '/assets/images/placeholder.jpg';
}

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

.public-page-wrapper .product-landing h3 {
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

/* ===== CONTENT BLOCK - DESKTOP ===== */
.public-page-wrapper .content-block {
    max-width: 900px;
    margin: 0 auto;
}

.public-page-wrapper .content-block h2 {
    font-size: clamp(2.8rem, 4vw, 4rem);
    font-weight: 700;
    letter-spacing: -0.02em;
    line-height: 1.3;
    color: #232e3a;
    margin-bottom: 2rem;
    text-align: center;
}

.public-page-wrapper .content-block p {
    font-size: 1.8rem;
    line-height: 1.6;
    color: #6b747f;
    margin-bottom: 2rem;
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
        <span class="hero__eyebrow">Who We Are</span>
        <h1><?php echo htmlspecialchars($siteName); ?> (<?php echo htmlspecialchars($siteInitials); ?>)</h1>
        <p>Opening a world of borderless financial opportunity.</p>
    </div>
</section>

<!-- Who We Are Section -->
<section class="section">
    <div class="section-container">
        <div class="content-block">
            <p class="section-description" style="text-align: left; margin-bottom: 2rem;">Founded in the United States, <?php echo htmlspecialchars($siteInitials); ?> is a forward-thinking digital and offshore bank serving clients across continents. We blend traditional banking expertise with innovative technology to deliver secure, seamless, and globally accessible financial solutions.</p>
        </div>
    </div>
</section>

<!-- Our Purpose Section -->
<section class="section">
    <div class="section-container">
        <div class="content-block">
            <h2 class="section-heading">Our Purpose</h2>
            <p class="section-description">To redefine the future of international banking — empowering people, businesses, and nations to thrive through secure, transparent, and borderless financial systems.</p>
            <p class="section-description" style="text-align: left; margin-top: 2rem;">We connect individuals and enterprises to a world of opportunity by simplifying global transactions, enabling offshore wealth management, and driving inclusive financial access across developed and emerging markets.</p>
        </div>
    </div>
</section>

<!-- Mission & Vision Section -->
<section class="section">
    <div class="section-container">
        <div class="section-product-landing">
            <div class="product-landing">
                <h3>Our Mission</h3>
                <p>To empower global citizens and businesses with innovative, compliant, and secure banking services that promote financial freedom without borders.</p>
            </div>
            <div class="product-landing">
                <h3>Our Vision</h3>
                <p>To become the most trusted offshore and digital banking institution — bridging currencies, nations, and opportunities through next-generation financial innovation.</p>
            </div>
        </div>
    </div>
</section>

<!-- Our Values Section -->
<section class="section">
    <div class="section-container">
        <div class="content-block">
            <h2 class="section-heading">Our Values</h2>
            <div class="section-product-landing" style="justify-content: center;">
                <div class="product-landing">
                    <h3>Integrity</h3>
                    <p>Trust and transparency in every transaction.</p>
                </div>
                <div class="product-landing">
                    <h3>Innovation</h3>
                    <p>Leveraging technology to simplify complex global finance.</p>
                </div>
                <div class="product-landing">
                    <h3>Security</h3>
                    <p>Uncompromising protection for client data and funds.</p>
                </div>
                <div class="product-landing">
                    <h3>Inclusion</h3>
                    <p>Creating equal access to banking, regardless of geography.</p>
                </div>
                <div class="product-landing">
                    <h3>Sustainability</h3>
                    <p>Operating responsibly to support communities and the planet.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Global Presence Section -->
<section class="section media-section">
    <div class="container media-layout media-layout--reverse">
        <div class="media-content">
            <h2>Our Global Presence</h2>
            <p><?php echo htmlspecialchars($siteInitials); ?> operates across multiple financial hubs — including the United States, United Kingdom, Spain, Canada, Italy, and Nigeria — providing our clients with offshore flexibility and local expertise.</p>
            <p style="margin-top: 1.5rem;">Our teams of regional specialists offer multilingual support, regulatory compliance expertise, and deep insight into over 30 global markets.</p>
        </div>
        <div class="media-image">
            <img src="<?php echo getImageUrl('businesswomen-going-work-together-city-while-talking-drinking-coffee copy.jpg'); ?>" alt="<?php echo htmlspecialchars($siteName); ?> global presence" loading="lazy" onerror="this.src='<?php echo getImageUrl('co-working-people-working-together copy.jpg'); ?>'; this.onerror=null;">
        </div>
    </div>
</section>

<!-- Leadership Section -->
<section class="section media-section">
    <div class="container media-layout">
        <div class="media-image">
            <img src="<?php echo getImageUrl('man-woman-looking-clipboard copy.jpg'); ?>" alt="<?php echo htmlspecialchars($siteName); ?> leadership" loading="lazy" onerror="this.src='<?php echo getImageUrl('co-working-people-working-together copy.jpg'); ?>'; this.onerror=null;">
        </div>
        <div class="media-content">
            <h2>Leadership With Global Perspective</h2>
            <p>Our leadership combines Wall Street expertise with international experience. Together, they guide <?php echo htmlspecialchars($siteInitials); ?> toward sustainable growth, regulatory excellence, and innovative banking solutions that protect and empower our clients.</p>
            <ul class="media-list">
                <li>Decades of combined experience in global finance</li>
                <li>Regional specialists ensuring market-specific compliance</li>
                <li>Transparent governance and client-first policies</li>
            </ul>
        </div>
    </div>
</section>

<!-- Sustainability Section -->
<section class="section">
    <div class="section-container">
        <div class="content-block">
            <h2 class="section-heading">Sustainability and Responsible Banking</h2>
            <p class="section-description">At <?php echo htmlspecialchars($siteInitials); ?>, we believe banking should serve a greater purpose. We are committed to ethical banking practices, supporting environmental sustainability, and funding initiatives that promote economic development in underserved regions.</p>
            <p class="section-description" style="text-align: left; margin-top: 2rem;">Our long-term goal is to become a net-zero bank by 2050, aligning our operations and investments with global sustainability standards.</p>
        </div>
    </div>
</section>

<!-- Innovation Section -->
<section class="section media-section">
    <div class="container media-layout media-layout--reverse">
        <div class="media-content">
            <h2>Innovation and Digital Banking</h2>
            <p>We continuously invest in cutting-edge technology to deliver safe, fast, and user-friendly digital banking experiences. From cross-border payments to automated financial tools, <?php echo htmlspecialchars($siteInitials); ?> makes global banking simple and secure for everyone.</p>
        </div>
        <div class="media-image">
            <img src="<?php echo getImageUrl('about-img-5.webp'); ?>" alt="<?php echo htmlspecialchars($siteName); ?> innovation" loading="lazy" onerror="this.src='<?php echo SITE_URL; ?>/assets/images/placeholder.jpg'; this.onerror=null;">
        </div>
    </div>
</section>

<!-- Join Section -->
<section class="section">
    <div class="section-container">
        <div class="content-block">
            <h2 class="section-heading">Join the <?php echo htmlspecialchars($siteName); ?> Future</h2>
            <p class="section-description">Whether you're an entrepreneur, investor, or global citizen, <?php echo htmlspecialchars($siteName); ?> provides the platform, technology, and expertise to help you grow confidently in a connected world.</p>
            <p class="section-description" style="font-size: 2rem; font-weight: 600; color: #232e3a; margin-top: 2rem;">Discover global banking — without borders.</p>
            <div style="text-align: center; margin-top: 3rem;">
                <a href="<?php echo SITE_URL; ?>/auth/register" class="get-started-btn" style="display: inline-block; margin: 0 1rem 1rem 1rem; text-decoration: none;">Open an Account</a>
                <a href="<?php echo SITE_URL; ?>/help-center" class="login-btn" style="display: inline-block; margin: 0 1rem 1rem 1rem; padding: 10px 20px; border: 2px solid #1a2b5f; border-radius: 6px; text-decoration: none;">Speak to a Specialist</a>
            </div>
        </div>
    </div>
</section>
</div>

<?php include __DIR__ . '/views/layouts/footer.php'; ?>
