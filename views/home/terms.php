<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

// Get dynamic branding
$siteName = getSiteName();
$siteInitials = getSiteInitials();
$pageTitle = 'Terms of Service | ' . $siteName;

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

.public-page-wrapper .legal-content,
.public-page-wrapper .faq-list {
    max-width: 900px;
    margin: 0 auto;
}

.public-page-wrapper .legal-content h2,
.public-page-wrapper .faq-item h2 {
    font-size: clamp(2.4rem, 3vw, 3rem);
    font-weight: 700;
    color: #1a2b5f;
    margin: 3rem 0 1.5rem 0;
    padding-bottom: 1rem;
    border-bottom: 2px solid #e5e7eb;
}

.public-page-wrapper .legal-content p,
.public-page-wrapper .faq-item p {
    font-size: clamp(1.6rem, 1.8vw, 1.8rem);
    line-height: 1.8;
    color: #475569;
    margin-bottom: 1.5rem;
}

.public-page-wrapper .faq-item {
    padding: 2rem 0;
    border-bottom: 1px solid #e5e7eb;
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
        <span class="hero__eyebrow">Legal</span>
        <h1>Terms of Service</h1>
        <p>Review the policies that govern your relationship with <?php echo htmlspecialchars($siteName); ?>.</p>
    </div>
</section>

<section class="section">
    <div class="section-container">
        <div class="legal-content">
        <h2>Introduction</h2>
        <p>These Terms of Service govern your access to and use of <?php echo htmlspecialchars($siteName); ?> products, platforms, and services. By opening an account or using our services, you agree to these terms.</p>

        <h2>Eligibility &amp; Account Opening</h2>
        <p>Clients must be 18 years or older and provide valid identification, proof of address, and source-of-funds documentation. <?php echo htmlspecialchars($siteName); ?> reserves the right to approve or decline applications at its discretion.</p>

        <h2>Deposit &amp; Withdrawal Policies</h2>
        <p>Deposits and withdrawals may be subject to verification and compliance review. Settlement times vary by currency, location, and payment network.</p>

        <h2>Investment Risks</h2>
        <p>All investments involve risk. Past performance does not guarantee future results. Clients are responsible for understanding the risks associated with stocks, ETFs, forex, and digital assets.</p>

        <h2>Card Usage</h2>
        <p>Cards must be used in accordance with network rules. Fraudulent or prohibited activity may result in card suspension or account closure.</p>

        <h2>Data Protection</h2>
        <p>We collect and process personal data in line with our Privacy Policy and applicable data protection laws. Security measures protect your information, but no system is entirely immune to unauthorized access.</p>

        <h2>AML &amp; Compliance Policy</h2>
        <p><?php echo htmlspecialchars($siteName); ?> adheres to global AML, KYC, and sanctions screening requirements. Suspicious activity may be reported to relevant authorities.</p>

        <h2>Termination of Service</h2>
        <p>Either party may terminate the banking relationship upon written notice. <?php echo htmlspecialchars($siteName); ?> may suspend or terminate services for compliance breaches or misuse.</p>

        <h2>Governing Law</h2>
        <p>These terms are governed by the laws of the United States. Disputes shall be resolved in the state and federal courts located in New York.</p>
        </div>
    </div>
</section>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>

