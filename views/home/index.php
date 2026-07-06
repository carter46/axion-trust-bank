<?php 
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

// Get dynamic branding with error handling
try {
    // Verify functions exist
    if (!function_exists('getSiteName')) {
        error_log("[Homepage Debug] getSiteName() function not found");
        $siteName = 'Cosmopolitan Trust Bank';
    } else {
        $siteName = getSiteName();
    }
    
    if (!function_exists('getSiteInitials')) {
        error_log("[Homepage Debug] getSiteInitials() function not found");
        $siteInitials = 'CTB';
    } else {
        $siteInitials = getSiteInitials();
    }
    
    if (!function_exists('getSetting')) {
        error_log("[Homepage Debug] getSetting() function not found");
        $siteTagline = 'Bank Globally. Invest Confidently.';
    } else {
        $siteTagline = getSetting('site_tagline', 'Bank Globally. Invest Confidently.');
    }
    
    // Ensure values are not empty
    $siteName = !empty($siteName) ? $siteName : 'Cosmopolitan Trust Bank';
    $siteInitials = !empty($siteInitials) ? $siteInitials : 'CTB';
    $siteTagline = !empty($siteTagline) ? $siteTagline : 'Bank Globally. Invest Confidently.';
    
    $pageTitle = $siteName . ' – ' . $siteTagline;
} catch (Exception $e) {
    error_log("[Homepage Debug] Critical error in branding: " . $e->getMessage());
    // Fallback values
    $siteName = 'Cosmopolitan Trust Bank';
    $siteInitials = 'CTB';
    $siteTagline = 'Bank Globally. Invest Confidently.';
    $pageTitle = $siteName . ' – ' . $siteTagline;
}

// -----------------------------
// Home page hero (brand-driven)
// -----------------------------
$heroTitlePrefix = getSetting('homepage_hero_title_prefix', 'Unleash the Power of');
$heroTitleHighlight = getSetting('homepage_hero_title_highlight', 'Global Banking Infrastructure');
$heroSubtitle = getSetting('homepage_hero_subtitle', $siteTagline);

$heroPrimaryCtaLabel = getSetting('homepage_hero_primary_cta_label', 'Open Account');
$heroSecondaryCtaLabel = getSetting('homepage_hero_secondary_cta_label', 'Login');
$heroDisclaimer = getSetting('homepage_hero_disclaimer', '');
$heroSocialProofText = getSetting('homepage_hero_social_proof_text', 'Trusted by customers worldwide');
$heroNavFeaturesLabel = getSetting('homepage_hero_nav_features_label', 'Features');
$heroNavProgramsLabel = getSetting('homepage_hero_nav_programs_label', 'Programs');
$heroNavContactLabel = getSetting('homepage_hero_nav_contact_label', 'Contact');

// Program cards for the hero carousel.
// If a JSON array is stored in system_settings under `homepage_hero_program_cards_json`, it will be used.
// Otherwise we fall back to existing images already referenced in this page.
$heroPrograms = json_decode(getSetting('homepage_hero_program_cards_json', '[]'), true);
if (!is_array($heroPrograms) || count($heroPrograms) === 0) {
    $defaultHeroProgramImages = [
        'borderless.webp',
        'co-working-people-working-together.jpg',
        'happy-couple-analyzing-their-home-budget-while-paying-bill-computer.jpg',
        'man-woman-looking-clipboard.jpg',
    ];

    $heroPrograms = [];
    $categoryPrefix = getSetting('homepage_hero_program_category_prefix', 'Program');
    foreach ($defaultHeroProgramImages as $idx => $imgFile) {
        $basename = pathinfo($imgFile, PATHINFO_FILENAME);
        $humanTitle = trim(ucwords(str_replace(['-', '_'], ' ', $basename)));
        $heroPrograms[] = [
            'image' => $imgFile,
            'category' => $categoryPrefix . ' ' . ($idx + 1),
            'title' => $humanTitle,
            'href' => SITE_URL . '/auth/register',
        ];
    }
}

// Social proof avatars: prefer configured images, otherwise reuse hero program images
$heroSocialProofAvatars = json_decode(getSetting('homepage_hero_social_proof_avatars_json', '[]'), true);
if (!is_array($heroSocialProofAvatars) || count($heroSocialProofAvatars) === 0) {
    $heroSocialProofAvatars = [];
    foreach ($heroPrograms as $program) {
        if (!empty($program['image'])) {
            $heroSocialProofAvatars[] = $program['image'];
        }
        if (count($heroSocialProofAvatars) >= 4) {
            break;
        }
    }
}

include __DIR__ . '/../layouts/header.php';
?>

<style>
/* ===== HOMEPAGE SCOPED STYLES - ISOLATED FROM OTHER PAGES ===== */
.homepage-wrapper {
    isolation: isolate;
    position: relative;
    width: 100%;
    overflow-x: hidden;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-rendering: optimizeLegibility;
}

/* ===== BROWSER-SPECIFIC FIXES FOR CONSISTENT MOBILE RENDERING ===== */
@supports (-webkit-touch-callout: none) {
    /* Safari/iOS specific */
    .homepage-wrapper {
        -webkit-overflow-scrolling: touch;
    }
}

/* Fix for Chrome mobile viewport issues */
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

/* ===== HOMEPAGE SECTIONS - EACH SECTION ISOLATED ===== */
/* ===== HERO SECTION - DESKTOP STYLES ===== */
.homepage-wrapper .section-landing-hero {
    background: linear-gradient(180deg, #E8F0FF 0%, #F5F9FF 50%, #FFFFFF 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
    padding: 6rem 2rem 4rem 2rem;
    position: relative;
    z-index: 1;
    isolation: isolate;
    overflow: hidden;
    contain: layout style paint;
}

.homepage-wrapper .section-landing-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle at 100% -20%, rgba(53, 158, 180, 0.25), rgba(255,255,255,0.65) 55%, rgba(255,255,255,1) 100%);
    z-index: 0;
    pointer-events: none;
}

/* Pattern overlay image (repeat) - hero background only */
.homepage-wrapper .section-landing-hero::after {
    content: '';
    position: absolute;
    inset: 0;
    z-index: 0;
    pointer-events: none;
    background-image: url('<?php echo SITE_URL; ?>/assets/images/bg_pattrn.png');
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center;
    opacity: 0.28;
}

.homepage-wrapper .section-landing-hero > * {
    position: relative;
    z-index: 1;
}

.homepage-wrapper .section {
    position: relative;
    isolation: isolate;
    contain: layout style;
    overflow: hidden;
}

.homepage-wrapper .container {
    max-width: 144rem;
    margin: 0 auto;
    width: 100%;
    padding: 0 2rem;
}

/* ===== HERO CONTENT BOX - DESKTOP ===== */
.homepage-wrapper .landing-hero-content-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    max-width: 64rem;
    width: 100%;
    margin: 0 auto;
    padding: 0 1rem;
    gap: 32px;
}

.homepage-wrapper .hero-landing-layout {
    display: flex;
    flex-direction: column;
    align-items: stretch;
    justify-content: center;
    gap: 2.5rem;
}

@media (max-width: 767px) {
    .homepage-wrapper .landing-hero-header,
    .homepage-wrapper .landing-hero-content {
        text-align: center;
    }

    .homepage-wrapper .landing-hero-content-box {
        align-items: center;
        text-align: center;
    }
}

/* ===== HERO HEADING TEXT - DESKTOP ===== */
.homepage-wrapper .landing-hero-header {
    font-size: clamp(36px, 6vw, 72px);
    font-weight: 700;
    letter-spacing: -0.02em;
    line-height: 1.1;
    font-family: Inter, sans-serif;
    color: #1a1a1a;
    width: 100%;
    text-align: center;
    white-space: normal;
    margin-bottom: 0;
}

/* ===== HERO DESCRIPTION TEXT - DESKTOP ===== */
.homepage-wrapper .landing-hero-content {
    color: #4a5568;
    font-size: clamp(16px, 2vw, 20px);
    line-height: 1.6;
    text-align: center;
    margin-bottom: 0;
    max-width: 600px;
    font-family: Inter, sans-serif;
}

/* ===== HERO BUTTONS CONTAINER - DESKTOP ===== */
.homepage-wrapper .landing-btn {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 1.5rem;
    flex-wrap: wrap;
    justify-content: center;
    width: 100%;
}

/* ===== HERO BUTTONS - DESKTOP ===== */
.homepage-wrapper .landing-btn .btn {
    flex: 1;
    min-width: 200px;
    max-width: 250px;
    text-align: center;
}

/* ===== HERO IMAGE - DESKTOP ===== */
.homepage-wrapper .landing-img {
    width: 100%;
    max-width: 100%;
    height: auto;
    object-fit: cover;
    vertical-align: bottom;
}

/* ===== PulseFit-like hero additions (scoped) ===== */
.homepage-wrapper .hero-pulse-header {
    width: 100%;
    max-width: 1400px;
    margin: 0 auto;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    text-align: left;
    gap: 0;
    padding-top: 32px;
    padding-bottom: 32px;
    padding-left: 2rem;
    padding-right: 2rem;
    position: relative;
    z-index: 20;
}

.homepage-wrapper .hero-pulse-logo {
    font-family: Inter, sans-serif;
    font-weight: 700;
    font-size: 24px;
    color: #1a1a1a;
}

.homepage-wrapper .hero-pulse-nav {
    display: none;
    flex-direction: row;
    align-items: center;
    gap: 2rem;
}

.homepage-wrapper .hero-pulse-nav a {
    font-family: Inter, sans-serif;
    font-size: 16px;
    font-weight: 400;
    color: #4a5568;
    text-decoration: none;
    transition: opacity .2s;
}

.homepage-wrapper .hero-pulse-nav a:hover {
    opacity: 0.7;
}

.homepage-wrapper .hero-pulse-header-cta {
    text-decoration: none;
    font-family: Inter, sans-serif;
    font-size: 16px;
    font-weight: 500;
    color: #1a1a1a;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    padding: 0.9rem 1.6rem;
    border-radius: 9999px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: transform .15s ease, box-shadow .15s ease;
}

.homepage-wrapper .hero-pulse-header-cta:hover {
    transform: scale(1.03);
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.10);
}

/* ===== Hero Main Action Buttons ===== */
.homepage-wrapper .hero-pulse-action-primary,
.homepage-wrapper .hero-pulse-action-secondary {
    text-decoration: none;
    font-family: Inter, sans-serif;
    font-size: 18px;
    font-weight: 500;
    border-radius: 9999px;
    display: inline-flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 1rem 2rem;
    transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease, background-color .15s ease, color .15s ease;
}

.homepage-wrapper .hero-pulse-action-primary {
    background: #1a1a1a;
    color: #ffffff;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    border: 0;
}

.homepage-wrapper .hero-pulse-action-primary:hover {
    transform: scale(1.05);
}

.homepage-wrapper .hero-pulse-action-secondary {
    background: transparent;
    border: 1px solid #cbd5e0;
    color: #1a1a1a;
    box-shadow: none;
}

.homepage-wrapper .hero-pulse-action-secondary:hover {
    transform: scale(1.05);
}

.homepage-wrapper .hero-pulse-social-proof {
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    margin-top: -1rem;
    margin-bottom: 1.5rem;
}

.homepage-wrapper .hero-pulse-avatar-row {
    display: flex;
    flex-direction: row;
    align-items: center;
}

.homepage-wrapper .hero-pulse-avatar {
    width: 40px;
    height: 40px;
    border-radius: 9999px;
    border: 2px solid #ffffff;
    background: #ffffff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    object-fit: cover;
    font-family: Inter, sans-serif;
    font-size: 14px;
    font-weight: 700;
    color: #4a5568;
    margin-left: -10px;
}

.homepage-wrapper .hero-pulse-avatar:first-child {
    margin-left: 0;
}

.homepage-wrapper .hero-pulse-social-proof span {
    font-family: Inter, sans-serif;
    font-size: 14px;
    font-weight: 500;
    color: #4a5568;
}

.homepage-wrapper .hero-pulse-programs-carousel {
    width: 100%;
    overflow: hidden;
    margin-top: 0;
    padding-top: 0;
    padding-bottom: 0;
    position: relative;
    z-index: 10;
}

.homepage-wrapper .hero-pulse-programs-gradient-left,
.homepage-wrapper .hero-pulse-programs-gradient-right {
    position: absolute;
    top: 0;
    bottom: 0;
    width: 0px;
    opacity: 0;
    z-index: 10;
    pointer-events: none;
}

.homepage-wrapper .hero-pulse-programs-gradient-left {
    left: 0;
    background: linear-gradient(90deg, #FFFFFF 0%, rgba(255, 255, 255, 0) 100%);
}

.homepage-wrapper .hero-pulse-programs-gradient-right {
    right: 0;
    background: linear-gradient(270deg, #FFFFFF 0%, rgba(255, 255, 255, 0) 100%);
}

/* ===== Hero entry animations (approximate framer-motion on this PHP stack) ===== */
@keyframes pulseHeroHeaderIn {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes pulseHeroContentIn {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes pulseHeroDisclaimerIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes pulseHeroSocialIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes pulseHeroProgramsIn {
    from { opacity: 0; transform: translateY(100px); }
    to { opacity: 1; transform: translateY(0); }
}

.homepage-wrapper .hero-pulse-header {
    opacity: 0;
    transform: translateY(-20px);
    animation: pulseHeroHeaderIn 0.6s ease forwards;
}

.homepage-wrapper .landing-hero-content-box {
    opacity: 0;
    transform: translateY(30px);
    animation: pulseHeroContentIn 0.8s ease forwards;
    animation-delay: 0.2s;
}

.homepage-wrapper .hero-pulse-disclaimer {
    opacity: 0;
    animation: pulseHeroDisclaimerIn 0.6s ease forwards;
    animation-delay: 0.6s;
}

.homepage-wrapper .hero-pulse-social-proof {
    opacity: 0;
    transform: translateY(20px);
    animation: pulseHeroSocialIn 0.6s ease forwards;
    animation-delay: 0.7s;
}

.homepage-wrapper .hero-pulse-programs-carousel {
    opacity: 0;
    transform: translateY(100px);
    animation: pulseHeroProgramsIn 1s ease forwards;
    animation-delay: 0.8s;
}

.homepage-wrapper .hero-pulse-programs-track {
    display: flex;
    flex-direction: row;
    width: 100%;
    gap: 0;
    animation: none;
    will-change: transform;
    padding-left: 0px;
    transform: translateX(0%);
    transition: transform 500ms ease;
}

.homepage-wrapper .hero-pulse-program-card {
    flex: 0 0 100%;
    width: 100%;
    height: 320px;
    border-radius: 0;
    overflow: hidden;
    position: relative;
    cursor: pointer;
    transition: opacity .2s ease;
    background: #ffffff;
}

.homepage-wrapper .hero-pulse-program-card:hover {
    opacity: 0.98;
}

.homepage-wrapper .hero-pulse-program-card img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    display: block;
    padding: 2px;
    background: #ffffff;
}

.homepage-wrapper .hero-pulse-program-card::after {
    content: none;
}

.homepage-wrapper .hero-pulse-program-card-content {
    display: none;
}

.homepage-wrapper .hero-pulse-program-category {
    font-family: Inter, sans-serif;
    font-size: 12px;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.8);
    text-transform: uppercase;
    letter-spacing: 0.1em;
}

.homepage-wrapper .hero-pulse-program-title {
    font-family: Inter, sans-serif;
    font-size: 24px;
    font-weight: 600;
    color: #ffffff;
    line-height: 1.3;
}

@media (min-width: 1024px) {
    .homepage-wrapper .hero-pulse-nav {
        display: flex;
    }
    .homepage-wrapper .hero-pulse-header {
        padding-left: 4rem;
        padding-right: 4rem;
    }
}

/* ============================================ */
/* ===== CONTAINER STYLES - DESKTOP ===== */
/* ============================================ */
.homepage-wrapper .section-container {
    max-width: 144rem;
    margin: 6rem auto;
    width: 100%;
    padding: 0 2rem;
}

.homepage-wrapper .section-container-inside {
    max-width: 144rem;
    margin: 6rem auto 0 auto;
    width: 100%;
    position: relative;
    padding: 0 2rem;
}

.homepage-wrapper .section-container-full {
    max-width: 144rem;
    margin: 6rem auto;
    width: 100%;
    padding: 0 2rem;
}

/* ===== SECTION HEADING - DESKTOP ===== */
.homepage-wrapper .section-heading {
    font-size: clamp(2.8rem, 4vw, 4.8rem);
    font-weight: 700;
    letter-spacing: -0.02em;
    line-height: 1.3;
    text-align: center;
    margin-bottom: 2rem;
}

/* ===== SECTION DESCRIPTION - DESKTOP ===== */
.homepage-wrapper .section-description {
    text-align: center;
    font-size: clamp(1.6rem, 1.8vw, 1.8rem);
    line-height: 1.5;
    margin-bottom: 3rem;
}

/* ============================================ */
/* ===== CUSTOMER LOGOS SECTION - DESKTOP ===== */
/* ============================================ */
.homepage-wrapper .section-customer-logos {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
    gap: 2rem;
}

.homepage-wrapper .customer-logos {
    display: flex;
    justify-content: center;
    padding: 1.5rem;
    max-width: 25%;
    flex: 0 0 calc(25% - 2rem);
}

.homepage-wrapper .customer-logos img {
    height: 3.5rem;
    width: 100%;
    max-width: 100%;
    object-fit: contain;
    filter: brightness(0);
    opacity: .4;
}

.homepage-wrapper .customer-logos img.no-brightness-opacity {
    filter: none;
    opacity: unset;
}

/* ============================================ */
/* ===== PRODUCT LANDING SECTION (Global finances, Unified solutions) - DESKTOP ===== */
/* ============================================ */
.homepage-wrapper .section-product-landing {
    display: flex;
    flex-wrap: wrap;
    gap: 3.2rem;
    justify-content: space-between;
    align-items: stretch;
}

.homepage-wrapper .product-landing {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    background-color: #f2f3f4;
    padding: 2.4rem;
    border-radius: 1.8rem;
    gap: 1.5rem;
    cursor: pointer;
    transition: all .4s;
    flex: 1;
    min-width: 350px;
    max-width: 380px;
    box-sizing: border-box;
}

.homepage-wrapper .product-landing > a.card-link {
    text-decoration: none;
    color: inherit;
    display: flex;
    flex-direction: column;
    height: 100%;
    gap: 1.5rem;
    justify-content: space-between;
}

.homepage-wrapper .product-landing:hover {
    transform: translateY(-0.5rem);
}

.homepage-wrapper .product-landing-content, .homepage-wrapper .business-container-content {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.homepage-wrapper .product-landing-content {
    margin-bottom: 2rem;
}

.homepage-wrapper .product-landing-img {
    width: 100%;
    max-width: 100%;
    height: auto;
    vertical-align: bottom;
}

.homepage-wrapper .product-landing-heading {
    font-size: 1.6rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.6rem;
}

.homepage-wrapper .product-landing-heading p {
    margin: 0;
}

.homepage-wrapper .product-landing-description {
    vertical-align: top;
    font-size: 1.5rem;
    line-height: 1.5;
    margin-bottom: 2rem;
}

.homepage-wrapper .product-landing-description p {
    margin: 0;
}

.homepage-wrapper .product-landing figure {
    margin: 0;
    text-align: center;
}

/* ============================================ */
/* ===== BUSINESS SECTION (Startups, Mid-size, Enterprises) - DESKTOP ===== */
/* ============================================ */
.homepage-wrapper .section-business {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 2.5rem;
}

.homepage-wrapper .business-container {
    display: flex;
    flex-direction: column;
    border-radius: 1.8rem;
    gap: 2rem;
}

.homepage-wrapper .startups {
    background-image: linear-gradient(55deg, #49a8bc, #232e3a 25% 25%, #232e3a 73% 73%, #49a8bc);
    padding: 2rem;
    border-radius: 1.8rem;
}

.homepage-wrapper .business-container-heading {
    font-size: clamp(2rem, 3vw, 2.4rem);
    font-weight: 600;
    line-height: 1.3;
}

.homepage-wrapper .business-container-description {
    vertical-align: top;
    font-size: clamp(1.5rem, 2vw, 1.8rem);
    line-height: 1.5;
}

/* ============================================ */
/* ===== BE TRULY BORDERLESS SECTION - DESKTOP ===== */
/* ============================================ */
.homepage-wrapper .section-be-truly {
    display: grid;
    grid-template-columns: 1fr;
    gap: 3rem;
    align-items: center;
    padding: 0 2rem;
}

.homepage-wrapper .be-truly {
    background-image: radial-gradient(farthest-corner circle at 70% 115%, #fadee3, #e6f0fc 60% 60%);
    border-radius: 1.8rem;
    padding: 3rem 2rem;
}

.homepage-wrapper .be-truly-heading {
    font-size: clamp(2.4rem, 4vw, 4rem);
    font-weight: 700;
    letter-spacing: -0.02em;
    line-height: 1.3;
    text-align: center;
    margin-bottom: 2rem;
}

.homepage-wrapper .play-store {
    display: flex;
    flex-direction: row;
    gap: 1.5rem;
    margin-top: 2rem;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
}

.homepage-wrapper .play-store-btn {
    height: 4.5rem;
    max-width: 150px;
}

/* ============================================ */
/* ===== REGULATED EXCELLENCE SECTION - DESKTOP ===== */
/* ============================================ */
.homepage-wrapper .section-regulated-excellence {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
}

.homepage-wrapper .regulated-excellence {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    background-color: #f2f3f4;
    padding: 2rem;
    border-radius: 1.8rem;
    gap: 1.5rem;
    align-items: center;
    text-align: center;
}

.homepage-wrapper .regulated-flags {
    height: 5rem;
    max-width: 80px;
}

.homepage-wrapper .regulated-excellence-heading {
    text-align: center;
    text-transform: capitalize;
    font-size: clamp(1.5rem, 2vw, 1.8rem);
    font-weight: 600;
    line-height: 1.3;
}

/* ============================================ */
/* ===== GLOBAL ECOSYSTEM SECTION (Payment Ecosystem) - DESKTOP ===== */
/* ============================================ */
.homepage-wrapper .payment-ecosystem-wrapper {
    overflow: hidden;
    position: relative;
    isolation: isolate;
    contain: layout style paint;
}

.homepage-wrapper .payment-ecosystem-wrapper .section-container-inside {
    position: relative;
    isolation: isolate;
    min-height: 600px;
    contain: layout style;
    padding: 4rem 2rem;
    z-index: 1;
}

.homepage-wrapper .payment-ecosystem-wrapper .section-heading,
.homepage-wrapper .payment-ecosystem-wrapper .section-description {
    position: relative;
    z-index: 2;
}

.homepage-wrapper .section-global-ecosystem {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2.5rem;
    position: relative;
    z-index: 2;
}

.homepage-wrapper .global-ecosystem {
    display: flex;
    gap: 1.5rem;
    flex-direction: column;
}

.homepage-wrapper .global-ecosystem-border {
    border-left: 2px solid #359eb4;
    height: 4rem;
}

.homepage-wrapper .global-ecosystem-value {
    display: flex;
    color: #ffffff;
    gap: 1rem;
    font-size: clamp(2.8rem, 4vw, 4.8rem);
    font-weight: 700;
    letter-spacing: -0.02em;
    line-height: 1.3;
    align-items: center;
}

.homepage-wrapper .global-ecosystem-value .global-ecosystem-value {
    font-size: inherit;
    display: inline;
}

.homepage-wrapper .global-ecosystem-content {
    color: #ffffff;
    padding-left: 1rem;
    font-size: 1.5rem;
    line-height: 1.5;
}

.homepage-wrapper .payment-ecosystem-wrapper .globe-canvas {
    width: auto;
    max-width: 800px;
    height: auto;
    max-height: 80%;
    object-fit: contain;
    object-position: bottom right;
    position: absolute;
    right: 0;
    bottom: 0;
    top: auto;
    transform: none;
    z-index: 0;
    margin: 0;
    pointer-events: none;
    opacity: 0.25;
}

.homepage-wrapper .payment-ecosystem-wrapper .globe-inner-wrapper {
    position: absolute;
    width: 50%;
    height: 100%;
    top: 0;
    right: 0;
    left: auto;
    bottom: 0;
    overflow: hidden;
    z-index: 0;
    pointer-events: none;
}

.homepage-wrapper .payment-ecosystem-wrapper .globe-inner-wrapper::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to left, rgba(35, 46, 58, 0.4), rgba(35, 46, 58, 0.6));
    z-index: 1;
    pointer-events: none;
}

.homepage-wrapper .global-ecosystem-image {
    position: absolute;
    width: 50%;
    height: 100%;
    top: 0;
    right: 0;
    bottom: 0;
    z-index: 0;
    overflow: hidden;
    pointer-events: none;
}

.homepage-wrapper .trusted-innovators-image {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    max-width: 100%;
    height: auto;
}

/* ============================================ */
/* ===== TRUSTED INNOVATORS SECTION - DESKTOP ===== */
/* ============================================ */
.homepage-wrapper .trusted-innovators {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 200px;
    padding: 2rem;
}

.homepage-wrapper .trusted-innovators-content {
    font-size: clamp(1.8rem, 2.5vw, 3.2rem);
    font-weight: 400;
    line-height: 1.4;
    color: #ffffff;
    text-align: left;
}

.homepage-wrapper .tabs-light {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 1rem;
}

.homepage-wrapper .tabs-light label {
    order: 1;
    display: block;
    cursor: pointer;
    transition: background ease .2s;
    padding: 1rem 1.5rem;
    color: #6b747f;
    border: 1px solid #ffffff;
    font-size: 1.5rem;
    line-height: 1.4;
    font-weight: 500;
    border-radius: 2rem;
    flex: 1;
    min-width: 120px;
    text-align: center;
}

.homepage-wrapper .tabs-light label:hover {
    color: #359eb4;
}

.homepage-wrapper .tabs-light label.active {
    border: 1px solid #359eb4;
    border-radius: 3.2rem;
    color: #359eb4;
}

.homepage-wrapper .tabs-light .tab-light {
    order: 99;
    flex-grow: 1;
    width: 100%;
    display: none;
    margin-top: 3rem;
    background-color: #232e3a;
    border-radius: 1.8rem;
    padding: 3rem 2rem;
}

.homepage-wrapper .tabs-light .tab-light.active {
    display: block;
}

/* ============================================ */
/* ===== SECURITY & COMPLIANCE SECTION - DESKTOP ===== */
/* ============================================ */
.homepage-wrapper .security-compliance {
    display: flex;
    flex-direction: column;
    gap: 3rem;
    border-radius: 1.8rem;
    padding: 3rem 2rem;
    justify-content: space-between;
}

.homepage-wrapper .security-compliance-content {
    display: grid;
    grid-template-columns: 1fr;
    gap: 3rem;
}

.homepage-wrapper .security-compliance-heading {
    margin-top: 2rem;
    font-size: 2rem;
    font-weight: 600;
    line-height: 1.3;
}

.homepage-wrapper .security-compliance-description {
    margin-top: 1.5rem;
    font-size: 1.6rem;
    line-height: 1.5;
}

.homepage-wrapper .security-compliance-badges {
    display: flex;
    flex-direction: row;
    gap: 2rem;
    width: 100%;
    justify-content: center;
    flex-wrap: wrap;
}

.homepage-wrapper .security-compliance-badges img {
    max-width: 150px;
    height: auto;
}

/* ============================================ */
/* ===== DISCOVER POWER SECTION - DESKTOP ===== */
/* ============================================ */
.homepage-wrapper .section-discover-power {
    display: grid;
    grid-template-columns: 1fr;
    background-image: radial-gradient(farthest-corner circle at 120% 115%, #359eb4, #232e3a 65% 60%);
    border-radius: 1.8rem;
    overflow: hidden;
    gap: 3rem;
    align-items: center;
    padding: 3rem 2rem;
}

.homepage-wrapper .discover-power-content {
    display: flex;
    padding: 0;
    flex-direction: column;
    gap: 2.5rem;
    text-align: center;
}

.homepage-wrapper .discover-power-image {
    justify-self: center;
    display: flex;
    align-self: center;
    padding-top: 0;
}

.homepage-wrapper .discover-power-image img {
    max-width: 300px;
    width: 100%;
    height: auto;
}

.homepage-wrapper .discover-power-heading {
    color: #ffffff;
    font-size: clamp(2.4rem, 4vw, 4rem);
    font-weight: 700;
    letter-spacing: -0.02em;
    line-height: 1.3;
}

/* ============================================ */
/* ===== LATEST NEWS SECTION - DESKTOP ===== */
/* ============================================ */
.homepage-wrapper .section-latest-news-wrapper {
    width: 100%;
    overflow-x: auto;
    padding-left: 2rem;
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.homepage-wrapper .section-latest-news-wrapper::-webkit-scrollbar {
    display: none;
}

.homepage-wrapper .section-latest-news {
    display: flex;
    gap: 2rem;
    scrollbar-width: none;
    padding-top: 3rem;
    padding-right: 2rem;
}

.homepage-wrapper .latest-news-container {
    display: flex;
    flex-direction: column;
    border-radius: 1.8rem;
    gap: 1rem;
    cursor: pointer;
    transition: all .4s;
    flex: 0 0 auto;
    min-width: 280px;
    max-width: 315px;
}

.homepage-wrapper .latest-news-container:hover {
    transform: translateY(-0.5rem);
    color: #359eb4;
}

.homepage-wrapper .latest-news-image {
    display: contents;
}

.homepage-wrapper .latest-news-content {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    padding: 1rem;
}

.homepage-wrapper .latest-news-heading {
    font-size: 1.6rem;
    line-height: 1.4;
    font-weight: 500;
}

.homepage-wrapper .latest-news-heading:hover {
    color: #359eb4;
}

.homepage-wrapper .badge {
    border-radius: 1.8rem;
    background-color: #f2f3f4;
    padding: 0.6rem 1rem;
    width: fit-content;
    font-size: 1.4rem;
    line-height: 1.3;
    font-weight: 600;
}

.homepage-wrapper .badge:hover {
    background-color: #359eb4;
    color: #ffffff;
}

.homepage-wrapper .cs_ratio {
    display: block;
    position: relative;
}

.homepage-wrapper .cs_ratio:before {
    content: "";
    padding-top: 70.86%;
    display: block;
}

.homepage-wrapper .cs_ratio > img {
    width: 100%;
    height: 100%;
    position: absolute;
    left: 0;
    top: 0;
    object-fit: cover;
}

.homepage-wrapper .stretched-link {
    position: absolute;
    inset: 0;
    z-index: 1;
}

/* ============================================ */
/* ===== TODAY'S RATES SECTION - DESKTOP ===== */
/* ============================================ */
.homepage-wrapper .section-todays-rates {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2.5rem;
    margin-bottom: 3rem;
    justify-items: center;
}

.homepage-wrapper .todays-rates-card {
    display: flex;
    flex-direction: column;
    background-color: transparent;
    padding: 0;
    gap: 1.5rem;
    width: 100%;
    max-width: 100%;
    text-align: center;
    align-items: center;
}

.homepage-wrapper .todays-rates-title {
    font-size: clamp(1.8rem, 2vw, 2.4rem);
    font-weight: 600;
    line-height: 1.3;
    color: #359eb4;
    margin: 0 0 0.8rem 0;
    text-align: center;
}

.homepage-wrapper .todays-rates-min-balance {
    font-size: 1.4rem;
    color: #6b747f;
    line-height: 1.5;
    margin: 0 0 2rem 0;
    text-align: center;
}

.homepage-wrapper .todays-rates-apy {
    display: flex;
    flex-direction: column;
    gap: 0;
    margin: 2rem 0;
    line-height: 1.2;
    align-items: center;
}

.homepage-wrapper .todays-rates-apy > div {
    display: flex;
    align-items: baseline;
    gap: 0.8rem;
    flex-wrap: wrap;
    justify-content: center;
}

.homepage-wrapper .todays-rates-up-to {
    font-size: 1.4rem;
    color: #232e3a;
    font-weight: 500;
    margin: 0 0 0.8rem 0;
    display: block;
    text-align: center;
}

.homepage-wrapper .todays-rates-percentage {
    font-size: 5.6rem;
    font-weight: 700;
    line-height: 1;
    color: #232e3a;
    margin: 0;
    display: inline-block;
    letter-spacing: -0.02em;
}

.homepage-wrapper .todays-rates-label {
    font-size: 1.8rem;
    color: #232e3a;
    font-weight: 700;
    margin: 0;
    display: inline-block;
    line-height: 1;
}

.homepage-wrapper .todays-rates-link {
    display: inline-block;
    color: #ea566e;
    text-decoration: underline;
    font-size: 1.6rem;
    font-weight: 500;
    margin-top: 2rem;
    transition: color 0.3s;
    text-align: center;
}

.homepage-wrapper .todays-rates-link:hover {
    color: #bb4558;
    text-decoration: underline;
}

.homepage-wrapper .todays-rates-disclaimer {
    margin-top: 3rem;
    padding: 0;
    background-color: transparent;
    font-size: 1.2rem;
    line-height: 1.8;
    color: #6b747f;
    max-width: 100%;
    text-align: center;
}

.homepage-wrapper .todays-rates-disclaimer p {
    margin: 0 0 1.5rem 0;
    text-align: center;
}

.homepage-wrapper .todays-rates-disclaimer p:last-child {
    margin-bottom: 0;
}

.homepage-wrapper .todays-rates-disclaimer strong {
    color: #232e3a;
    font-weight: 600;
}

/* ============================================ */
/* ===== BUTTONS & GENERAL STYLES - DESKTOP ===== */
/* ============================================ */
.homepage-wrapper .btn, .homepage-wrapper .btn-outline, .homepage-wrapper .btn:link, .homepage-wrapper .btn:visited {
    padding: 1.4rem 2rem;
    text-decoration: none;
    border-radius: 1rem;
    display: inline-block;
    transition: background-color .3s;
    cursor: pointer;
    font-size: 1.5rem;
    font-weight: 500;
    font-family: inherit;
    text-align: center;
    border: none;
    width: 100%;
    max-width: 250px;
}

.homepage-wrapper .btn, .homepage-wrapper .btn:link, .homepage-wrapper .btn:visited {
    background-color: #359eb4;
    border: 1px solid #359eb4;
    color: #ffffff;
}

.homepage-wrapper .btn-full:link, .homepage-wrapper .btn-full:visited {
    background-color: #359eb4;
    color: #ffffff;
    border: none;
}

.homepage-wrapper .btn-full:hover, .homepage-wrapper .btn-full:active {
    background-color: #2a7e90;
    color: #ffffff;
    border: none;
}

.homepage-wrapper .btn-outline {
    background-color: #ffffff;
    border: 1px solid #359eb4;
    color: #359eb4;
}

.homepage-wrapper .btn-outline:hover, .homepage-wrapper .btn-outline:active {
    background-color: #ebf5f8;
    color: #359eb4;
    border: 1px solid #2a7e90;
}

.homepage-wrapper .link, .homepage-wrapper .link:visited, .homepage-wrapper .link-xl, .homepage-wrapper .link-xl:visited {
    text-decoration: none;
    text-align: left;
    color: #359eb4;
    transition: all .3s;
}

.homepage-wrapper .link-xl, .homepage-wrapper .link-xl:visited {
    font-size: 1.6rem;
    line-height: 1.5;
    font-weight: 500;
}

.homepage-wrapper .link:hover, .homepage-wrapper .link:active, .homepage-wrapper .link-xl:hover, .homepage-wrapper .link-xl:active {
    color: #2a7e90;
}

.homepage-wrapper .arrow {
    margin-left: 0.4rem;
    display: inline-block;
    transition: transform .3s ease;
}

.homepage-wrapper .link-xl:hover .arrow, .homepage-wrapper .link:hover .arrow {
    transform: translate(5px);
}

.homepage-wrapper .highlight {
    color: #359eb4;
}

.homepage-wrapper .position-relative {
    position: relative;
}

.homepage-wrapper .mb-xl-4 {
    margin-bottom: 2.5rem;
}

.homepage-wrapper .mb-3 {
    margin-bottom: 2rem;
}

.homepage-wrapper .moonstone {
    color: #359eb4;
}

.homepage-wrapper .pink-800 {
    color: #ea566e !important;
}

.homepage-wrapper .white {
    color: #ffffff;
}

.homepage-wrapper .grey-900 {
    background-color: #232e3a;
}

.homepage-wrapper .grey-100 {
    background-color: #f2f3f4;
}

.homepage-wrapper .white-100 {
    background-color: #ffffff;
}

.homepage-wrapper .align-c {
    text-align: center;
}

.homepage-wrapper .align-l {
    text-align: left !important;
}

.homepage-wrapper .mar-l-8 {
    margin-left: 0.8rem;
}

.homepage-wrapper .mar-l-16 {
    margin-left: 1.6rem;
}

.homepage-wrapper .mar-b-24 {
    margin-bottom: 2rem;
}

.homepage-wrapper .mar-b-32 {
    margin-bottom: 2.5rem;
}

.homepage-wrapper .mar-b-48 {
    margin-bottom: 3rem;
}

.homepage-wrapper .mar-t-a {
    margin-top: auto;
}

.homepage-wrapper .mar-t-24 {
    margin-top: 2rem;
}

.homepage-wrapper .wid-50, .homepage-wrapper .wid-75 {
    display: inline-block;
    width: 100% !important;
    max-width: 100%;
}

.homepage-wrapper .show {
    display: block;
}

.homepage-wrapper .font-size-m {
    font-size: 1.6rem;
}

/* ============================================ */
/* ===== MOBILE STYLES (max-width: 767px) ===== */
/* ============================================ */
@media (max-width: 767px) {
    
    /* ===== HERO SECTION - MOBILE BACKGROUND ===== */
    .homepage-wrapper .section-landing-hero {
        background-attachment: scroll;
    }

    .homepage-wrapper .section-landing-hero::after {
        background-size: cover;
        opacity: 0.24;
    }
    
    /* ===== PRODUCT LANDING CARDS - MOBILE ===== */
    .homepage-wrapper .product-landing {
        min-width: 100%;
        max-width: 100%;
        flex: 0 0 100%;
    }
    
    /* ===== HERO SECTION - MOBILE ===== */
    .homepage-wrapper .section-landing-hero {
        min-height: 70vh;
        align-items: center;
        justify-content: center;
        padding-top: 2rem;
        padding-bottom: 0;
        position: relative;
    }
    
    /* ===== HERO CONTAINER - MOBILE ===== */
    .homepage-wrapper .section-landing-hero .container {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        min-height: 70vh;
        position: relative;
        padding-bottom: 0;
    }
    
    /* ===== HERO CONTENT BOX - MOBILE ===== */
    .homepage-wrapper .landing-hero-content-box {
        padding: 1rem 0.5rem;
        flex-shrink: 0;
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    
    /* ===== HERO IMAGE - MOBILE ===== */
    .homepage-wrapper .landing-img {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        width: 100%;
        margin-top: 0;
        align-self: flex-end;
    }
    
    /* ===== HERO HEADING TEXT - MOBILE ===== */
    .homepage-wrapper .landing-hero-header {
        font-size: clamp(30px, 9vw, 42px);
        line-height: 1.15;
        margin-bottom: 0;
        padding: 0;
    }
    
    /* ===== HERO DESCRIPTION TEXT - MOBILE ===== */
    .homepage-wrapper .landing-hero-content {
        font-size: clamp(16px, 2vw, 20px);
        line-height: 1.6;
        margin-bottom: 0;
        padding: 0;
    }
    
    /* ===== HERO BUTTONS CONTAINER - MOBILE ===== */
    .homepage-wrapper .landing-btn {
        flex-direction: column;
        flex-wrap: nowrap;
        gap: 0.5rem;
        justify-content: center;
        margin-top: 0.5rem;
    }
    
    /* ===== HERO BUTTONS - MOBILE ===== */
    .homepage-wrapper .landing-btn .btn {
        flex: 0 1 auto;
        min-width: 100px;
        max-width: 130px;
        padding: 0.7rem 0.8rem;
        font-size: 1rem;
        margin: 0;
    }
    
    /* ===== CUSTOMER LOGOS - MOBILE ===== */
    .homepage-wrapper .customer-logos {
        max-width: 50%;
        flex: 0 0 calc(50% - 1rem);
        padding: 1rem;
    }
    
    .homepage-wrapper .customer-logos img {
        height: 5rem;
        width: 100%;
        max-width: 100%;
        object-fit: contain;
    }

    /* ===== Program carousel cards - MOBILE ===== */
    .homepage-wrapper .hero-pulse-program-card {
        height: 280px;
    }

    .homepage-wrapper .hero-pulse-program-card-content {
        display: none;
    }
    
    .homepage-wrapper .hero-pulse-program-card img {
        padding: 1px;
    }

    /* Reduce side gradient overlays so they don't wash out the images */
    .homepage-wrapper .hero-pulse-programs-gradient-left,
    .homepage-wrapper .hero-pulse-programs-gradient-right {
        width: 0px;
        opacity: 0;
    }
    
    /* ===== REGULATED EXCELLENCE - MOBILE ===== */
    .homepage-wrapper .section-regulated-excellence {
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }
    
    /* ===== GLOBAL ECOSYSTEM SECTION - MOBILE ===== */
    .homepage-wrapper .payment-ecosystem-wrapper .section-container-inside {
        min-height: 500px;
        padding: 3rem 2rem;
    }
    
    .homepage-wrapper .payment-ecosystem-wrapper .globe-canvas {
        max-width: 400px;
        max-height: 70%;
        width: auto;
        height: auto;
        bottom: 0;
        right: 0;
        top: auto;
        opacity: 0.25;
    }
    
    .homepage-wrapper .payment-ecosystem-wrapper .globe-inner-wrapper {
        width: 100%;
        height: 100%;
        right: 0;
        bottom: 0;
    }
    
    .homepage-wrapper .payment-ecosystem-wrapper .globe-inner-wrapper::after {
        background: linear-gradient(to left, rgba(35, 46, 58, 0.5), rgba(35, 46, 58, 0.7));
    }
    
    .homepage-wrapper .global-ecosystem-image {
        width: 100%;
        height: 100%;
        right: 0;
        bottom: 0;
    }
    
    .homepage-wrapper .section-global-ecosystem {
        z-index: 2;
        position: relative;
    }
    
    /* ===== TODAY'S RATES SECTION - MOBILE ===== */
    .homepage-wrapper .section-todays-rates {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
}

/* ============================================ */
/* ===== TABLET/DESKTOP (min-width: 768px) ===== */
/* ============================================ */
@media (min-width: 768px) {
    
    /* ===== HERO SECTION - TABLET/DESKTOP ===== */
    .homepage-wrapper .section-landing-hero {
        padding: 6rem 2rem 4rem 2rem;
    }
    
    /* ===== HERO CONTENT BOX - TABLET/DESKTOP ===== */
    .homepage-wrapper .landing-hero-content-box {
        padding: 6rem 2rem;
        align-items: flex-start;
        text-align: left;
    }

    .homepage-wrapper .hero-landing-layout {
        flex-direction: column;
        justify-content: center;
        align-items: stretch;
        gap: 2.5rem;
    }

    .homepage-wrapper .landing-hero-header,
    .homepage-wrapper .landing-hero-content {
        text-align: left;
    }

    .homepage-wrapper .landing-btn {
        justify-content: flex-start;
    }

    .homepage-wrapper .hero-pulse-program-card {
        height: 360px;
    }

    /* ===== HERO BUTTONS - TABLET/DESKTOP ===== */
    .homepage-wrapper .landing-btn {
        flex-direction: row;
        gap: 1.5rem;
    }
    
    /* ===== HERO HEADING TEXT - TABLET/DESKTOP ===== */
    .homepage-wrapper .landing-hero-header {
        font-size: 5.2rem;
        margin-bottom: 0.75rem;
    }
    
    /* ===== HERO DESCRIPTION TEXT - TABLET/DESKTOP ===== */
    .homepage-wrapper .landing-hero-content {
        font-size: 1.8rem;
        margin-bottom: 1.5rem;
    }
    
    /* ===== CONTAINERS - TABLET/DESKTOP ===== */
    .homepage-wrapper .section-container,
    .homepage-wrapper .section-container-inside,
    .homepage-wrapper .section-container-full {
        padding: 0 2rem;
    }
    
    /* ===== BE TRULY BORDERLESS - TABLET/DESKTOP ===== */
    .homepage-wrapper .section-be-truly {
        grid-template-columns: 1fr 1fr;
        gap: 4rem;
        padding: 0 2rem;
    }
    
    .homepage-wrapper .be-truly-heading {
        text-align: left;
    }
    
    .homepage-wrapper .play-store {
        justify-content: flex-start;
    }
    
    /* ===== TRUSTED INNOVATORS - TABLET/DESKTOP ===== */
    .homepage-wrapper .trusted-innovators {
        grid-template-columns: 1fr 1fr;
    }
    
    /* ===== SECURITY & COMPLIANCE - TABLET/DESKTOP ===== */
    .homepage-wrapper .security-compliance {
        flex-direction: row;
        padding: 4rem;
    }
    
    .homepage-wrapper .security-compliance-content {
        grid-template-columns: 1fr 1fr;
    }
    
    /* ===== DISCOVER POWER - TABLET/DESKTOP ===== */
    .homepage-wrapper .section-discover-power {
        grid-template-columns: 1fr 1fr;
        padding: 4rem;
    }
    
    .homepage-wrapper .discover-power-content {
        text-align: left;
        padding-left: 0;
    }
    
    /* ===== CUSTOMER LOGOS - TABLET/DESKTOP ===== */
    .homepage-wrapper .customer-logos {
        flex: 0 0 calc(25% - 2rem);
    }
}

@media (min-width: 1024px) {
    .homepage-wrapper .hero-landing-layout {
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        gap: 3rem;
    }

    .homepage-wrapper .landing-hero-content-box {
        flex: 1 1 auto;
        max-width: 64rem;
    }

    .homepage-wrapper .hero-pulse-programs-carousel {
        flex: 0 0 560px;
        max-width: 560px;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
    }

    .homepage-wrapper .hero-pulse-program-card {
        height: 400px;
    }

    .homepage-wrapper .hero-pulse-program-card img {
        padding: 2px;
    }
}

/* ============================================ */
/* ===== LARGE DESKTOP (min-width: 1024px) ===== */
/* ============================================ */
@media (min-width: 1024px) {
    /* ===== HERO SECTION - LARGE DESKTOP ===== */
    .homepage-wrapper .section-landing-hero {
        padding: 6rem 2rem 4rem 2rem;
    }
    
    /* ===== CONTAINERS - LARGE DESKTOP ===== */
    .homepage-wrapper .section-container,
    .homepage-wrapper .section-container-inside,
    .homepage-wrapper .section-container-full {
        padding: 0 2rem;
    }
    
    /* ===== BUSINESS SECTION - LARGE DESKTOP ===== */
    .homepage-wrapper .section-business {
        grid-template-columns: repeat(3, 1fr);
    }
    
    /* ===== REGULATED EXCELLENCE - LARGE DESKTOP ===== */
    .homepage-wrapper .section-regulated-excellence {
        grid-template-columns: repeat(6, 1fr);
    }
    
    /* ===== GLOBAL ECOSYSTEM - LARGE DESKTOP ===== */
    .homepage-wrapper .section-global-ecosystem {
        grid-template-columns: repeat(3, 1fr);
    }
    
    /* ===== TABS - LARGE DESKTOP ===== */
    .homepage-wrapper .tabs-light label {
        flex: 0 1 auto;
    }
    
    /* ===== WIDTH UTILITIES - LARGE DESKTOP ===== */
    .homepage-wrapper .wid-75 {
        width: 75% !important;
    }
    
    .homepage-wrapper .wid-50 {
        width: 50% !important;
    }
}

/* ============================================ */
/* ===== LARGE DESKTOP (min-width: 1200px) ===== */
/* ============================================ */
@media (min-width: 1200px) {
    /* ===== CONTAINERS - LARGE DESKTOP ===== */
    .homepage-wrapper .section-container,
    .homepage-wrapper .section-container-inside,
    .homepage-wrapper .section-container-full {
        padding: 0 2rem;
    }
}

</style>

<div class="homepage-wrapper">
<!-- Hero Section - PulseFit-style Design -->
<section class="section-landing-hero">
    <div class="container hero-landing-layout">
        <div class="landing-hero-content-box">
            <h1 class="landing-hero-header">
                <?php echo htmlspecialchars($heroTitlePrefix); ?>
                <span class="moonstone"><?php echo htmlspecialchars($heroTitleHighlight); ?></span>
            </h1>

            <div class="landing-hero-content">
                <p><?php echo htmlspecialchars($heroSubtitle); ?></p>
            </div>

            <div class="landing-btn">
                <a
                    class="hero-pulse-action-primary"
                    href="<?php echo SITE_URL; ?>/auth/register"
                >
                    <?php echo htmlspecialchars($heroPrimaryCtaLabel); ?>
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <path
                            d="M7 10H13M13 10L10 7M13 10L10 13"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>
                </a>

                <a
                    class="hero-pulse-action-secondary"
                    href="<?php echo SITE_URL; ?>/auth/login"
                >
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M15 3H19C20.1046 3 21 3.89543 21 5V19C21 20.1046 20.1046 21 19 21H15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M10 17L15 12L10 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M15 12H3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <?php echo htmlspecialchars($heroSecondaryCtaLabel); ?>
                </a>
            </div>

            <?php if (!empty($heroDisclaimer)): ?>
                <p class="hero-pulse-disclaimer" style="font-family: Inter, sans-serif; font-size: 13px; font-weight: 400; color: #718096; font-style: italic; margin-top: 1.25rem; margin-bottom: 0;">
                    <?php echo htmlspecialchars($heroDisclaimer); ?>
                </p>
            <?php endif; ?>

            <?php if (!empty($heroSocialProofText)): ?>
                <div class="hero-pulse-social-proof" aria-label="Social proof">
                    <div class="hero-pulse-avatar-row">
                        <?php for ($i = 0; $i < 4; $i++): ?>
                            <?php $avatarFile = $heroSocialProofAvatars[$i] ?? ''; ?>
                            <?php if (!empty($avatarFile)): ?>
                                <img
                                    src="<?php echo SITE_URL . '/uploads/images/' . rawurlencode((string)$avatarFile); ?>"
                                    alt="User <?php echo (int)($i + 1); ?>"
                                    class="hero-pulse-avatar"
                                />
                            <?php else: ?>
                                <span class="hero-pulse-avatar" aria-hidden="true">
                                    <?php echo htmlspecialchars(substr($siteInitials, 0, 2)); ?>
                                </span>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                    <span><?php echo htmlspecialchars($heroSocialProofText); ?></span>
                </div>
            <?php endif; ?>
        </div>

        <?php $heroProgramTrack = $heroPrograms; ?>
        <div class="hero-pulse-programs-carousel" aria-label="Programs carousel">
            <div class="hero-pulse-programs-track">
                <?php foreach ($heroProgramTrack as $program): ?>
                    <?php
                        $programImage = isset($program['image']) ? (string) $program['image'] : '';
                        $programCategory = isset($program['category']) ? (string) $program['category'] : 'Program';
                        $programTitle = isset($program['title']) ? (string) $program['title'] : '';
                        $programHref = isset($program['href']) ? (string) $program['href'] : (SITE_URL . '/auth/register');
                    ?>
                    <a class="hero-pulse-program-card" href="<?php echo htmlspecialchars($programHref); ?>">
                        <?php if (!empty($programImage)): ?>
                            <img
                                src="<?php echo SITE_URL . '/uploads/images/' . rawurlencode($programImage); ?>"
                                alt="<?php echo htmlspecialchars($programTitle); ?>"
                            />
                        <?php endif; ?>

                        <div class="hero-pulse-program-card-content">
                            <span class="hero-pulse-program-category">
                                <?php echo htmlspecialchars($programCategory); ?>
                            </span>
                            <h3 class="hero-pulse-program-title">
                                <?php echo htmlspecialchars($programTitle); ?>
                            </h3>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- Customer Logos Section -->
<section class="section">
    <div class="section-container">
        <h2 class="section-heading mar-b-32">Smart businesses choose <?php echo htmlspecialchars($siteName); ?></h2>
        <div class="section-description">
            <p class="wid-75">From entrepreneurs to global corporations, our clients rely on <?php echo htmlspecialchars($siteInitials); ?> to simplify their international banking needs. We make it easy to send, receive, and manage funds securely across borders.</p>
        </div>
        <div class="section-customer-logos">
            <figure class="customer-logos">
                <img loading="lazy" alt="Visa Mastercard American Express" src="<?php echo SITE_URL . '/uploads/images/' . rawurlencode('discover-card-mastercard-american-express-visa-credit-card-mastercard.png'); ?>" class="no-brightness-opacity">
            </figure>
            <figure class="customer-logos">
                <img loading="lazy" alt="SWIFT" src="<?php echo SITE_URL . '/uploads/images/' . rawurlencode('Swift-Logo.jpg'); ?>" class="no-brightness-opacity">
            </figure>
            <figure class="customer-logos">
                <img loading="lazy" alt="Microsoft Azure" src="<?php echo SITE_URL . '/uploads/images/' . rawurlencode('microsoft_azure_logo_icon.png'); ?>" class="no-brightness-opacity">
            </figure>
            <figure class="customer-logos">
                <img loading="lazy" alt="Amazon Web Services" src="<?php echo SITE_URL . '/uploads/images/' . rawurlencode('amazon-web-services-logo.png'); ?>" class="no-brightness-opacity">
            </figure>
            <figure class="customer-logos">
                <img loading="lazy" alt="Stripe" src="<?php echo SITE_URL . '/uploads/images/' . rawurlencode('Stripe_Logo.png'); ?>" class="no-brightness-opacity">
            </figure>
            <figure class="customer-logos">
                <img loading="lazy" alt="PayPal" src="<?php echo SITE_URL . '/uploads/images/' . rawurlencode('Paypal-Logo.png'); ?>" class="no-brightness-opacity">
            </figure>
            <figure class="customer-logos">
                <img loading="lazy" alt="Marriott International" src="<?php echo SITE_URL . '/uploads/images/' . rawurlencode('marriott-international-logo-png_seeklogo-484457.png'); ?>" class="no-brightness-opacity">
            </figure>
            <figure class="customer-logos">
                <img loading="lazy" alt="IBM" src="<?php echo SITE_URL . '/uploads/images/' . rawurlencode('IBM_logo.svg.png'); ?>" class="no-brightness-opacity">
            </figure>
        </div>
    </div>
</section>

<!-- Unified Solutions Section -->
<section class="section white-100 unified-section">
    <div class="section-container">
        <h2 class="section-heading mar-b-32">Unify your <span class="pink-800">global finances</span> in one powerful platform</h2>
        <p class="section-description mar-b-48 align-c">
            <span class="wid-75">Take full control of your finances with our secure and intelligent banking system. Manage your accounts, handle foreign exchange, streamline operations, and grow your business or personal wealth — all within <?php echo htmlspecialchars($siteName); ?>.</span>
        </p>
        <div class="section-product-landing">
            <div class="product-landing">
                <a class="card-link" href="#">
                    <div class="product-landing-content">
                        <h5 class="product-landing-heading">
                            <div><p><?php echo htmlspecialchars($siteInitials); ?> Accounts</p></div>
                            <img fetchpriority="low" loading="lazy" alt="arrow" class="mar-l-8" src="https://images.ctfassets.net/2rrb5ka4jpe4/QcaTeCpnj47m4874dZ0sX/b7b3e286572b2f31ce08354f45bb5237/Vector__2_.svg">
                        </h5>
                        <div class="product-landing-description">
                            <p>Grow your financial reach globally. Open checking, savings, or business accounts with ease. Simplify international transfers and manage your funds across multiple currencies in real time.</p>
                        </div>
                    </div>
                    <figure>
                        <img fetchpriority="low" loading="lazy" class="product-landing-img" src="https://images.ctfassets.net/2rrb5ka4jpe4/2e4H17h3JUBr6PD9REww25/0a28e3b0745ebe6ade66180cdfa54f58/business-accounts.svg" alt="PS Business Accounts">
                    </figure>
                </a>
            </div>
            <div class="product-landing">
                <a class="card-link" href="#">
                    <div class="product-landing-content">
                        <h5 class="product-landing-heading">
                            <div><p><?php echo htmlspecialchars($siteInitials); ?> Atlas</p></div>
                            <img fetchpriority="low" loading="lazy" alt="arrow" class="mar-l-8" src="https://images.ctfassets.net/2rrb5ka4jpe4/4bjIy0FE3M8F5BPxFTb7l1/6c36f63496b518f6fdbba9f0683b3bdc/Vector__2_.svg">
                        </h5>
                        <div class="product-landing-description">
                            <p>Connect your customers to a world of opportunities. Our digital infrastructure allows individuals and organizations to transact, invest, and expand seamlessly across borders. With <?php echo htmlspecialchars($siteInitials); ?> Atlas, businesses can operate in emerging and established markets alike.</p>
                        </div>
                    </div>
                    <figure>
                        <img fetchpriority="low" loading="lazy" class="product-landing-img" src="https://images.ctfassets.net/2rrb5ka4jpe4/2IznnkaznkhEBy2LdDm1Lb/534d6bd8d2d3c6be6ab2ebed671509d5/verto_atlas__2_.svg" alt="verto atlas">
                    </figure>
                </a>
            </div>
            <div class="product-landing">
                <a class="card-link" href="#">
                    <div class="product-landing-content">
                        <h5 class="product-landing-heading">
                            <div><p><?php echo htmlspecialchars($siteInitials); ?> FX</p></div>
                            <img fetchpriority="low" loading="lazy" alt="arrow" class="mar-l-8" src="https://images.ctfassets.net/2rrb5ka4jpe4/QcaTeCpnj47m4874dZ0sX/b7b3e286572b2f31ce08354f45bb5237/Vector__2_.svg">
                        </h5>
                        <div class="product-landing-description">
                            <p>Trade, convert, and transfer funds at lightning speed. Access competitive exchange rates, manage multi-currency funds, and complete global transfers securely — backed by our cutting-edge foreign exchange network.</p>
                        </div>
                    </div>
                    <figure>
                        <img fetchpriority="low" loading="lazy" class="product-landing-img" src="https://images.ctfassets.net/2rrb5ka4jpe4/2e4H17h3JUBr6PD9REww25/0a28e3b0745ebe6ade66180cdfa54f58/business-accounts.svg" alt="Verto FX">
                    </figure>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Business Section -->
<section class="section white-100 financial-section">
    <div class="section-container-inside">
        <h2 class="section-heading mar-b-48">
            <span class="wid-75">Fuel growth at every stage with customized financial solutions</span>
        </h2>
        <div class="section-business">
            <div class="business-container">
                <figure class="startups">
                    <img loading="lazy" class="product-landing-img" src="https://images.ctfassets.net/2rrb5ka4jpe4/2mPE1xyfh2moorlHU0ehB6/6bda4721d9fea75027b740ef07165449/img-startup.webp" alt="PS Homepage Startups">
                </figure>
                <div class="business-container-content">
                    <h5 class="business-container-heading">Startups</h5>
                    <div class="business-container-description">
                        <p>Simplify your global payments with our multi-currency accounts and payment links. Accelerate your growth and receive payments anywhere in the world.</p>
                    </div>
                </div>
                <a class="link-xl mar-t-a show" href="<?php echo SITE_URL; ?>/dashboard" target="_self">Discover our startup product suite <span class="font-size-m arrow">→</span></a>
            </div>
            <div class="business-container">
                <figure class="startups">
                    <img loading="lazy" class="product-landing-img" src="https://images.ctfassets.net/2rrb5ka4jpe4/68f6ql89pGk3CZvWdYKsz7/a925e5b6a6feda62e4369dea353a37ca/img-midsize.webp" alt="PS Homepage Mid Size Companies">
                </figure>
                <div class="business-container-content">
                    <h5 class="business-container-heading">Mid-size companies</h5>
                    <div class="business-container-description">
                        <p>Take control of your expenses with integrated account management and corporate card tools — perfect for scaling operations efficiently.</p>
                    </div>
                </div>
                <a class="link-xl mar-t-a show" href="<?php echo SITE_URL; ?>/dashboard" target="_self">Discover our mid-size product suite <span class="font-size-m arrow">→</span></a>
            </div>
            <div class="business-container">
                <figure class="startups">
                    <img loading="lazy" class="product-landing-img" src="https://images.ctfassets.net/2rrb5ka4jpe4/3HzV7cqGxM8UhwL1dlFJ3E/f3ef9b942153a3a15d422ac5a6c9dc27/img-enterprise.webp" alt="PS Homepage Enterprise">
                </figure>
                <div class="business-container-content">
                    <h5 class="business-container-heading">Enterprises</h5>
                    <div class="business-container-description">
                        <p>Navigate international markets confidently with advanced treasury, liquidity, and risk management tools designed for complex operations.</p>
                    </div>
                </div>
                <a class="link-xl mar-t-a show" href="<?php echo SITE_URL; ?>/dashboard" target="_self">Discover our enterprise product suite <span class="font-size-m arrow">→</span></a>
            </div>
        </div>
    </div>
</section>

<!-- Be Truly Borderless Section -->
<section class="section white-100">
    <div class="section-container-inside">
        <div class="be-truly">
            <div class="section-be-truly">
                <div class="be-truly-heading">
                    Experience Borderless Banking with <?php echo htmlspecialchars($siteInitials); ?>
                    <p style="margin: 1.5rem 0; font-size: 1.6rem; line-height: 1.6;">Stay connected to your accounts anywhere with our secure online and mobile platforms. Monitor, invest, and transact globally — anytime, anywhere.</p>
                    <div class="play-store">
                        <a target="_blank" href="#">
                            <img loading="lazy" alt="Button 1" class="play-store-btn" src="https://images.ctfassets.net/2rrb5ka4jpe4/4GDd6Ni8azrRDftwyDFxQl/ef1ef22a3a64710acb2de0cbedc046d6/app-store.svg">
                        </a>
                        <a target="_blank" href="#">
                            <img loading="lazy" alt="Button 2" class="play-store-btn google-btn" src="https://images.ctfassets.net/2rrb5ka4jpe4/6Y7gnkDvjDfhch7pPzSwCx/987019dcbd16c487821f8f8285ed5123/play-store.svg">
                        </a>
                    </div>
                </div>
                <img loading="lazy" alt="Borderless Banking" src="<?php echo SITE_URL . '/uploads/images/' . rawurlencode('borderless.webp'); ?>">
            </div>
        </div>
    </div>
</section>

<!-- Regulated Excellence Section -->
<section class="section white-100">
    <div class="section-container-inside">
        <h2 class="section-heading mar-b-32">Built on trust, transparency, and global compliance</h2>
        <p class="section-description">
            <span class="wid-75 mar-b-48"><?php echo htmlspecialchars($siteName); ?> upholds the highest international banking standards, ensuring all client transactions meet regulatory requirements in every region we operate. Our operations are rooted in integrity, safety, and accountability.</span>
        </p>
        <div class="section-regulated-excellence">
            <div class="regulated-excellence">
                <img alt="Brazil flag" loading="lazy" class="regulated-flags" src="<?php echo SITE_URL . '/uploads/images/' . rawurlencode('brazil.png'); ?>">
                <div class="regulated-excellence-heading">Brazil</div>
            </div>
            <div class="regulated-excellence">
                <img alt loading="lazy" class="regulated-flags" src="https://images.ctfassets.net/2rrb5ka4jpe4/1an6veZGMZ89vZkyUuVX2R/77822da9b22cbd08275e68a330a4a76f/Flags__11_.svg">
                <div class="regulated-excellence-heading">United Kingdom</div>
            </div>
            <div class="regulated-excellence">
                <img alt loading="lazy" class="regulated-flags" src="https://images.ctfassets.net/2rrb5ka4jpe4/4weIbmp6mY3Jd64FNfP99B/2214d21340e02609cfc361034dedc719/Flags__40_.svg">
                <div class="regulated-excellence-heading">South Africa</div>
            </div>
            <div class="regulated-excellence">
                <img alt="Canada flag" loading="lazy" class="regulated-flags" src="<?php echo SITE_URL . '/uploads/images/' . rawurlencode('canada.png'); ?>">
                <div class="regulated-excellence-heading">Canada</div>
            </div>
            <div class="regulated-excellence">
                <img alt loading="lazy" class="regulated-flags" src="https://images.ctfassets.net/2rrb5ka4jpe4/1CuEfUXrAJBtKQiAljvlt0/6117b2aa17e302bec2d40dcdec53392b/Flags__11___1_.svg">
                <div class="regulated-excellence-heading">United States</div>
            </div>
            <div class="regulated-excellence">
                <img alt loading="lazy" class="regulated-flags" src="https://images.ctfassets.net/2rrb5ka4jpe4/4m63VY1kBfSLKC8ooBFRvG/491bc9230e0469937b5fcd4c10cca740/Dubai.svg">
                <div class="regulated-excellence-heading">UAE (DFSA)</div>
            </div>
            <div class="regulated-excellence">
                <img alt="China flag" loading="lazy" class="regulated-flags" src="https://flagcdn.com/cn.svg">
                <div class="regulated-excellence-heading">China</div>
            </div>
            <div class="regulated-excellence">
                <img alt="South Korea flag" loading="lazy" class="regulated-flags" src="https://flagcdn.com/kr.svg">
                <div class="regulated-excellence-heading">South Korea</div>
            </div>
            <div class="regulated-excellence">
                <img alt="Malaysia flag" loading="lazy" class="regulated-flags" src="https://flagcdn.com/my.svg">
                <div class="regulated-excellence-heading">Malaysia</div>
            </div>
            <div class="regulated-excellence">
                <img alt="Philippines flag" loading="lazy" class="regulated-flags" src="https://flagcdn.com/ph.svg">
                <div class="regulated-excellence-heading">Philippines</div>
            </div>
            <div class="regulated-excellence">
                <img alt="Thailand flag" loading="lazy" class="regulated-flags" src="https://flagcdn.com/th.svg">
                <div class="regulated-excellence-heading">Thailand</div>
            </div>
            <div class="regulated-excellence">
                <img alt="India flag" loading="lazy" class="regulated-flags" src="https://flagcdn.com/in.svg">
                <div class="regulated-excellence-heading">India</div>
            </div>
            <div class="regulated-excellence">
                <img alt="Japan flag" loading="lazy" class="regulated-flags" src="https://flagcdn.com/jp.svg">
                <div class="regulated-excellence-heading">Japan</div>
            </div>
            <div class="regulated-excellence">
                <img alt="Nepal flag" loading="lazy" class="regulated-flags" src="https://flagcdn.com/np.svg">
                <div class="regulated-excellence-heading">Nepal</div>
            </div>
            <div class="regulated-excellence">
                <img alt="Spain flag" loading="lazy" class="regulated-flags" src="https://flagcdn.com/es.svg">
                <div class="regulated-excellence-heading">Spain</div>
            </div>
            <div class="regulated-excellence">
                <img alt="Italy flag" loading="lazy" class="regulated-flags" src="https://flagcdn.com/it.svg">
                <div class="regulated-excellence-heading">Italy</div>
            </div>
            <div class="regulated-excellence">
                <img alt="Israel flag" loading="lazy" class="regulated-flags" src="https://flagcdn.com/il.svg">
                <div class="regulated-excellence-heading">Israel</div>
            </div>
            <div class="regulated-excellence">
                <img alt="France flag" loading="lazy" class="regulated-flags" src="https://flagcdn.com/fr.svg">
                <div class="regulated-excellence-heading">France</div>
            </div>
            <div class="regulated-excellence">
                <img alt="Russia flag" loading="lazy" class="regulated-flags" src="https://flagcdn.com/ru.svg">
                <div class="regulated-excellence-heading">Russia</div>
            </div>
            <div class="regulated-excellence">
                <img alt="Ukraine flag" loading="lazy" class="regulated-flags" src="https://flagcdn.com/ua.svg">
                <div class="regulated-excellence-heading">Ukraine</div>
            </div>
            <div class="regulated-excellence">
                <img alt="Poland flag" loading="lazy" class="regulated-flags" src="https://flagcdn.com/pl.svg">
                <div class="regulated-excellence-heading">Poland</div>
            </div>
            <div class="regulated-excellence">
                <img alt="Egypt flag" loading="lazy" class="regulated-flags" src="https://flagcdn.com/eg.svg">
                <div class="regulated-excellence-heading">Egypt</div>
            </div>
            <div class="regulated-excellence">
                <img alt="Switzerland flag" loading="lazy" class="regulated-flags" src="https://flagcdn.com/ch.svg">
                <div class="regulated-excellence-heading">Switzerland</div>
            </div>
            <div class="regulated-excellence">
                <img alt="Argentina flag" loading="lazy" class="regulated-flags" src="https://flagcdn.com/ar.svg">
                <div class="regulated-excellence-heading">Argentina</div>
            </div>
        </div>
    </div>
</section>

<!-- Global Ecosystem Section -->
<section class="section grey-900 payment-ecosystem-wrapper">
    <div class="section-container-inside">
        <h2 class="section-heading mar-b-48 white">Global reach. Local expertise. Borderless opportunity.</h2>
        <p class="section-description mar-b-48 align-c white">
            <span class="wid-50">Manage, invest, and transfer funds globally with full transparency and control. <?php echo htmlspecialchars($siteName); ?> empowers clients in over 190 countries through trusted cross-border payment and investment systems.</span>
        </p>
        <div class="section-global-ecosystem">
            <div class="global-ecosystem">
                <div class="global-ecosystem-value">
                    <span class="global-ecosystem-border"></span>
                    <span class="global-ecosystem-value">190+</span>
                </div>
                <p class="global-ecosystem-content">Countries served</p>
            </div>
            <div class="global-ecosystem">
                <div class="global-ecosystem-value">
                    <span class="global-ecosystem-border"></span>
                    <span class="global-ecosystem-value">50+</span>
                </div>
                <p class="global-ecosystem-content">Local account regions</p>
            </div>
            <div class="global-ecosystem">
                <div class="global-ecosystem-value">
                    <span class="global-ecosystem-border"></span>
                    <span class="global-ecosystem-value">10B+</span>
                </div>
                <p class="global-ecosystem-content">In processed payments annually</p>
            </div>
            <div class="global-ecosystem">
                <div class="global-ecosystem-value">
                    <span class="global-ecosystem-border"></span>
                    <span class="global-ecosystem-value">30%</span>
                </div>
                <p class="global-ecosystem-content">Faster processing speed</p>
            </div>
            <div class="global-ecosystem">
                <div class="global-ecosystem-value">
                    <span class="global-ecosystem-border"></span>
                    <span class="global-ecosystem-value">40%</span>
                </div>
                <p class="global-ecosystem-content">Lower transfer costs on average</p>
            </div>
        </div>
        <div class="global-ecosystem-image globe-inner-wrapper">
            <img loading="lazy" alt="Globe Canvas" class="globe-canvas" src="https://images.ctfassets.net/2rrb5ka4jpe4/7iXBQs9ZI9M1vPOooYRN09/61ba45b327789719bc43dd847e3ad061/verto_globe.webp">
        </div>
    </div>
</section>

<!-- Trusted Innovators Section -->
<section class="section white-100">
    <div class="section-container-inside align-c">
        <h2 class="section-heading mar-b-48">Trusted by innovators and industry leaders</h2>
        <div class="tabs-light">
            <label data-bs-toggle="tab" type="button" role="tab" class="active" id="tab-1" data-bs-target="#panel-1" aria-selected="true">Visa/Mastercard/Amex</label>
            <label data-bs-toggle="tab" type="button" role="tab" class id="tab-2" data-bs-target="#panel-2" aria-selected="false">SWIFT</label>
            <label data-bs-toggle="tab" type="button" role="tab" class id="tab-3" data-bs-target="#panel-3" aria-selected="false">Microsoft Azure</label>
            <label data-bs-toggle="tab" type="button" role="tab" class id="tab-4" data-bs-target="#panel-4" aria-selected="false">AWS</label>
            
            <div role="tabpanel" class="tab-light show active" aria-labelledby="tab-1" id="panel-1">
                <div class="trusted-innovators">
                    <figure class="trusted-innovators-image">
                        <img alt="Visa, Mastercard, American Express" loading="lazy" src="<?php echo SITE_URL . '/uploads/images/' . rawurlencode('discover-card-mastercard-american-express-visa-credit-card-mastercard.png'); ?>" style="max-height: 150px; width: auto; object-fit: contain;">
                    </figure>
                </div>
            </div>
            <div role="tabpanel" class="tab-light" aria-labelledby="tab-2" id="panel-2">
                <div class="trusted-innovators">
                    <figure class="trusted-innovators-image">
                        <img alt="SWIFT" loading="lazy" src="<?php echo SITE_URL . '/uploads/images/' . rawurlencode('Swift-Logo.jpg'); ?>" style="max-height: 150px; width: auto; object-fit: contain;">
                    </figure>
                </div>
            </div>
            <div role="tabpanel" class="tab-light" aria-labelledby="tab-3" id="panel-3">
                <div class="trusted-innovators">
                    <figure class="trusted-innovators-image">
                        <img alt="Microsoft Azure" loading="lazy" src="<?php echo SITE_URL . '/uploads/images/' . rawurlencode('microsoft_azure_logo_icon.png'); ?>" style="max-height: 150px; width: auto; object-fit: contain;">
                    </figure>
                </div>
            </div>
            <div role="tabpanel" class="tab-light" aria-labelledby="tab-4" id="panel-4">
                <div class="trusted-innovators">
                    <figure class="trusted-innovators-image">
                        <img alt="Amazon Web Services" loading="lazy" src="<?php echo SITE_URL . '/uploads/images/' . rawurlencode('amazon-web-services-logo.png'); ?>" style="max-height: 150px; width: auto; object-fit: contain;">
                    </figure>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Security Compliance Section -->
<section class="section white-100">
    <div class="section-container-inside">
        <h2 class="section-heading mar-b-48">Your security is our highest priority</h2>
        <div class="security-compliance grey-100">
            <div class="security-compliance-content">
                <div>
                    <img alt loading="lazy" src="https://images.ctfassets.net/2rrb5ka4jpe4/3maEQ5DyWxyzekiaRv9ytQ/2c65440787791c843c26b25533f7bc54/Security__2_.svg">
                    <div class="security-compliance-heading">Data protection compliant</div>
                    <p class="security-compliance-description">Fully compliant with GDPR, CCPA, and global data standards. Every transaction and account is secured through multi-layer encryption, strict regulatory compliance, and continuous system monitoring.</p>
                </div>
                <div>
                    <img alt loading="lazy" src="https://images.ctfassets.net/2rrb5ka4jpe4/2VMZu32HV93TUn9lrq1Dsb/9d9babbcd64e832305ae16027467e630/Security__1_.svg">
                    <div class="security-compliance-heading">Safeguarding Customer Funds</div>
                    <p class="security-compliance-description">Client funds held in segregated, safeguarded accounts. Real-time fraud detection and risk prevention systems. Licensed under international electronic money regulations. We take data protection and fund safety seriously.</p>
                </div>
            </div>
            <div class="security-compliance-badges">
                <img alt loading="lazy" src="https://images.ctfassets.net/2rrb5ka4jpe4/Wbn6JW5gNW7bcT6Oi3hOK/4f6dddb6d916c340a57b3094b8d02a7e/Group_27041.svg">
                <img alt loading="lazy" src="https://images.ctfassets.net/2rrb5ka4jpe4/665thWnqWQawMzZb6AuDgx/de2b4379eb5a228f4b04074b99fdd958/Group_27042.svg">
            </div>
        </div>
    </div>
</section>

<!-- Discover Power Section -->
<section class="section white-100">
    <div class="section-container white-100">
        <div class="section-discover-power">
            <div class="discover-power-content">
                <div class="discover-power-heading">Experience the Future of <span class="highlight">Global Banking</span></div>
                <p style="margin: 1.5rem 0 2rem 0; font-size: 1.6rem; line-height: 1.6; color: #F29AA8;">Join thousands of individuals and businesses who trust <?php echo htmlspecialchars($siteName); ?> for their financial growth, offshore solutions, and global transactions.</p>
                <div class="landing-btn">
                    <a class="btn btn-full" href="<?php echo SITE_URL; ?>/auth/register" target="_self">Open Account</a>
                    <a class="btn btn-outline" href="<?php echo SITE_URL; ?>/help-center">Speak to Our Team</a>
                </div>
            </div>
            <div class="discover-power-image">
                <img loading="lazy" alt="Experience the Future of Global Banking" src="<?php echo SITE_URL . '/uploads/images/' . rawurlencode('discover-power-card.webp'); ?>">
            </div>
        </div>
    </div>
</section>

<!-- Today's Rates Section -->
<section class="section white-100">
    <div class="section-container-inside">
        <h2 class="section-heading mar-b-48">Today's Rates</h2>
        <div class="section-todays-rates">
            <div class="todays-rates-card">
                <h3 class="todays-rates-title">Online Personal Checking</h3>
                <div class="todays-rates-min-balance">$100 minimum opening balance</div>
                <div class="todays-rates-apy">
                    <div>
                        <span class="todays-rates-percentage">0.05%</span>
                        <span class="todays-rates-label">APY</span>
                    </div>
                </div>
                <a href="<?php echo SITE_URL; ?>/auth/register" class="todays-rates-link">Open an Account →</a>
            </div>
            <div class="todays-rates-card">
                <h3 class="todays-rates-title">Online Personal Savings</h3>
                <div class="todays-rates-min-balance">$250 minimum opening balance</div>
                <div class="todays-rates-apy">
                    <div class="todays-rates-up-to">Up to</div>
                    <div>
                        <span class="todays-rates-percentage">3.30%</span>
                        <span class="todays-rates-label">APY</span>
                    </div>
                </div>
                <a href="<?php echo SITE_URL; ?>/auth/register" class="todays-rates-link">Open an Account →</a>
            </div>
            <div class="todays-rates-card">
                <h3 class="todays-rates-title">Online Personal 12 Month CD</h3>
                <div class="todays-rates-min-balance">$5,000 minimum opening balance</div>
                <div class="todays-rates-apy">
                    <div>
                        <span class="todays-rates-percentage">3.00%</span>
                        <span class="todays-rates-label">APY*</span>
                    </div>
                </div>
                <a href="<?php echo SITE_URL; ?>/auth/register" class="todays-rates-link">Open an Account →</a>
            </div>
        </div>
        <div class="todays-rates-disclaimer">
            <p><strong>*Annual Percentage Yields (APYs) are accurate as of 11/04/2025.</strong></p>
            <p>Your interest rate and APY may change after the account is opened. Account is subject to early withdrawal penalty. Fees could reduce earnings on the account.</p>
            <p>Additional terms and conditions may apply. Please see our account disclosure for more details.</p>
            <p>Online application is subject to approval. Restrictions and limitations apply. Local branches may offer different products with varying terms and conditions.</p>
        </div>
    </div>
</section>

<!-- Latest News Section -->
<section class="section">
    <div class="section-container-full">
        <h2 class="section-heading"><span>Insights from <?php echo htmlspecialchars($siteName); ?></span></h2>
        <div class="section-latest-news-wrapper">
            <div class="section-latest-news">
                <div class="latest-news-container">
                    <figure class="position-relative latest-news-image">
                        <div class="cs_ratio mb-xl-4 mb-3">
                            <img loading="lazy" style="border-radius: 24px;" src="https://images.ctfassets.net/2rrb5ka4jpe4/6ZkyOw9PdEv5Gfm5vP2oCs/627c9ca13f1ee4f088236904ff0156df/Currency_hedging.jpg" alt="Mastering FX Risk Management With Currency Hedging">
                        </div>
                        <a class="stretched-link" href="#"></a>
                    </figure>
                    <div class="latest-news-content">
                        <div class="badge">Product</div>
                        <h5 class="latest-news-heading">Managing FX Risk with Currency Hedging</h5>
                    </div>
                    <a class="stretched-link" href="#"></a>
                </div>
                <div class="latest-news-container">
                    <figure class="position-relative latest-news-image">
                        <div class="cs_ratio mb-xl-4 mb-3">
                            <img loading="lazy" style="border-radius: 24px;" src="https://images.ctfassets.net/2rrb5ka4jpe4/5SkPLkuWui9J8BdEo2KOIW/d4dcc7d25e2ed93e7cc2e2d1cc64dea6/Currency_marketplace.jpg" alt="Solving FX Liquidity Issues With Peer-to-Peer Currency Marketplaces">
    </div>
                        <a class="stretched-link" href="#"></a>
                    </figure>
                    <div class="latest-news-content">
                        <div class="badge">Product</div>
                        <h5 class="latest-news-heading">Solving Liquidity Challenges in Emerging Markets</h5>
            </div>
                    <a class="stretched-link" href="#"></a>
                </div>
                <div class="latest-news-container">
                    <figure class="position-relative latest-news-image">
                        <div class="cs_ratio mb-xl-4 mb-3">
                            <img loading="lazy" style="border-radius: 24px;" src="https://images.ctfassets.net/2rrb5ka4jpe4/7y9KPMeWQ9UL2IOCfBlJh2/7ea80685668f7555c42f2020accce27e/Marketplace_blog__1_.jpg" alt="FX Trading Made Easy with Verto Marketplace">
                </div>
                        <a class="stretched-link" href="#"></a>
                    </figure>
                    <div class="latest-news-content">
                        <div class="badge">Product</div>
                        <h5 class="latest-news-heading">Smarter Cross-Border Transfers with <?php echo htmlspecialchars($siteInitials); ?> FX Tools</h5>
                </div>
                    <a class="stretched-link" href="#"></a>
                </div>
        </div>
    </div>
</div>
</section>
</div>

<script>
// Simple tab functionality for trusted innovators section
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.tabs-light label');
    const panels = document.querySelectorAll('.tabs-light .tab-light');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetId = this.getAttribute('data-bs-target');
            
            // Remove active class from all tabs and panels
            tabs.forEach(t => {
                t.classList.remove('active');
                t.setAttribute('aria-selected', 'false');
            });
            panels.forEach(p => {
                p.classList.remove('show', 'active');
            });
            
            // Add active class to clicked tab and corresponding panel
            this.classList.add('active');
            this.setAttribute('aria-selected', 'true');
            const targetPanel = document.querySelector(targetId);
            if (targetPanel) {
                targetPanel.classList.add('show', 'active');
            }
        });
    });

    // Hero programs slider (one slide visible at a time)
    const heroCarousel = document.querySelector('.hero-pulse-programs-carousel');
    const heroTrack = heroCarousel ? heroCarousel.querySelector('.hero-pulse-programs-track') : null;
    const heroSlides = heroTrack ? Array.from(heroTrack.querySelectorAll('.hero-pulse-program-card')) : [];

    if (heroCarousel && heroTrack && heroSlides.length > 0) {
        let index = 0;
        let timerId = null;

        const prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        function goTo(nextIndex) {
            index = ((nextIndex % heroSlides.length) + heroSlides.length) % heroSlides.length;
            heroTrack.style.transform = 'translateX(' + (-index * 100) + '%)';
        }

        function start() {
            if (prefersReducedMotion) return;
            stop();
            timerId = window.setInterval(function() {
                goTo(index + 1);
            }, 4500);
        }

        function stop() {
            if (timerId) {
                window.clearInterval(timerId);
                timerId = null;
            }
        }

        // Swipe support
        let startX = 0;
        let startY = 0;
        let dragging = false;

        heroCarousel.addEventListener('touchstart', function(e) {
            if (!e.touches || e.touches.length !== 1) return;
            dragging = true;
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
            stop();
        }, { passive: true });

        heroCarousel.addEventListener('touchend', function(e) {
            if (!dragging) return;
            dragging = false;

            const touch = (e.changedTouches && e.changedTouches[0]) ? e.changedTouches[0] : null;
            if (!touch) {
                start();
                return;
            }

            const dx = touch.clientX - startX;
            const dy = touch.clientY - startY;

            // Only treat as swipe if mostly horizontal
            if (Math.abs(dx) > 50 && Math.abs(dx) > Math.abs(dy)) {
                if (dx < 0) goTo(index + 1);
                else goTo(index - 1);
            }

            start();
        }, { passive: true });

        heroCarousel.addEventListener('mouseenter', stop);
        heroCarousel.addEventListener('mouseleave', start);
        heroCarousel.addEventListener('focusin', stop);
        heroCarousel.addEventListener('focusout', start);

        goTo(0);
        start();
    }
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
