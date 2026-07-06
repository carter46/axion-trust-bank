<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

// Get dynamic branding
$siteName = getSiteName() ?: 'Cosmopolitan Trust Bank';
$siteInitials = getSiteInitials() ?: 'CTB';

$pageTitle = 'Cosmos Charity Home | ' . $siteName;

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
    background-image: url('<?php echo SITE_URL . '/uploads/images/' . rawurlencode('happy-hispanic-young-man-smiling.jpg'); ?>');
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
    margin-bottom: 3rem;
}

.public-page-wrapper .btn-secondary {
    background-color: #ffffff;
    color: #359eb4;
    border: 2px solid #ffffff;
    padding: 1.4rem 3rem;
    font-size: 1.6rem;
    font-weight: 600;
    border-radius: 1rem;
    cursor: pointer;
    transition: all 0.3s;
    font-family: inherit;
    text-decoration: none;
    display: inline-block;
}

.public-page-wrapper .btn-secondary:hover {
    background-color: #359eb4;
    color: #ffffff;
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
    
    .public-page-wrapper .product-landing {
        min-width: 100%;
        max-width: 100%;
    }
    
    .public-page-wrapper .section-container {
        padding: 0 2rem;
    }
    
    .public-page-wrapper .cta-section {
        padding: 4rem 2rem;
    }
}
</style>

<div class="public-page-wrapper">
<!-- Hero Section -->
<section class="page-hero">
    <div class="container page-hero__content">
        <span class="hero__eyebrow">Cosmos Charity Home</span>
        <h1>Banking with Purpose</h1>
        <p>Every account opened with <?php echo htmlspecialchars($siteName); ?> helps fund essential programs for communities in need.</p>
    </div>
</section>

<!-- Our Commitment Section -->
<section class="section">
    <div class="section-container">
        <div class="content-block">
            <h2 class="section-heading">Our Commitment</h2>
            <p class="section-description">Through Cosmos Charity Home, we give back to society by supporting orphanages, education programs, and emergency aid for those in need. A portion of our profits is reinvested into sustainable, community-driven initiatives.</p>
        </div>
    </div>
</section>

<!-- Programs Section -->
<section class="section">
    <div class="section-container">
        <div class="section-product-landing">
            <div class="product-landing">
                <h3>Orphanage Support</h3>
                <p>Providing safe housing, healthcare, and education to vulnerable children in multiple regions.</p>
            </div>
            <div class="product-landing">
                <h3>Education Programs</h3>
                <p>Scholarships, digital classrooms, and mentorship programs that prepare students for a global future.</p>
            </div>
            <div class="product-landing">
                <h3>Emergency Aid</h3>
                <p>Rapid-response funding for disaster relief, medical supplies, and essential community infrastructure.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section cta-section">
    <div class="container cta-section__inner">
        <h2>Support the Cosmos Charity Home and help change lives.</h2>
        <a href="<?php echo SITE_URL; ?>/help-center" class="btn-secondary">Learn About Our Impact</a>
    </div>
</section>
</div>

<?php include __DIR__ . '/views/layouts/footer.php'; ?>
