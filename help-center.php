<?php 
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/security.php';

$pageTitle = 'Help Center - ' . getSiteName();

// Get bank settings
$siteName = getSiteName();
$siteEmail = getSetting('site_email', SMTP_FROM);
$supportPhone = getSetting('support_phone', '+1 (555) 123-4567');
$supportHours = getSetting('support_hours', 'Monday - Friday, 8:00 AM - 6:00 PM EST');
$bankAddress = getSetting('bank_address', '123 Banking Street, New York, NY 10001');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_form'])) {
    $name = Security::sanitize($_POST['name'] ?? '');
    $email = Security::sanitize($_POST['email'] ?? '');
    $subject = Security::sanitize($_POST['subject'] ?? '');
    $message = Security::sanitize($_POST['message'] ?? '');

    $emailSubject = 'Help Center Contact - ' . ($subject ?: 'General Inquiry');
    $body = "<p><strong>From:</strong> {$name} ({$email})</p>"
          . "<p><strong>Subject:</strong> {$subject}</p>"
          . "<p><strong>Message:</strong><br>" . nl2br($message) . "</p>";

    // Use IMAP_USER (or SMTP_USER) so messages appear in the inbox
    // This ensures contact form messages are received in the admin email inbox
    $supportEmail = defined('IMAP_USER') ? IMAP_USER : (defined('SMTP_USER') ? SMTP_USER : getSetting('site_email', SMTP_FROM));
    // Send email with Reply-To set to the person's email so replies go to them
    if (sendEmail($supportEmail, $emailSubject, $body, true, $email)) {
        $_SESSION['success'] = 'Your message has been sent. We will get back to you soon.';
    } else {
        $_SESSION['error'] = 'Failed to send message. Please try again.';
    }

    // Redirect to contact form section with anchor so it scrolls to the form
    header('Location: ' . SITE_URL . '/help-center#contact-support');
    exit;
}

include __DIR__ . '/views/layouts/header.php';
?>

<style>
.help-center-page {
    background: var(--ui-base);
    color: var(--grey-color-800);
    font-family: var(--font-main);
}

.help-hero {
    background: linear-gradient(120deg, var(--moonstone-color-900) 0%, var(--grey-color-900) 100%);
    color: var(--white-color);
    padding: 7.2rem 0 6rem;
}

.hero-container {
    max-width: var(--max-width-xl-fix);
    margin: 0 auto;
    padding: 0 2.4rem;
    display: grid;
    gap: 2.4rem;
    justify-items: center;
    text-align: center;
}

.hero-overline {
    text-transform: uppercase;
    letter-spacing: 0.32rem;
    font-size: 1.2rem;
    font-weight: 600;
    opacity: 0.75;
}

.help-hero h1 {
    font-size: clamp(3.4rem, 4.8vw, 4.4rem);
    font-weight: 700;
}

.help-hero p {
    max-width: 60rem;
    font-size: 1.6rem;
    line-height: 1.7;
    opacity: 0.88;
}

.help-search {
    width: min(44rem, 100%);
    position: relative;
}

.help-search input {
    width: 100%;
    padding: 1.4rem 5.2rem 1.4rem 2.4rem;
    border-radius: 999px;
    border: none;
    background: rgba(255, 255, 255, 0.16);
    color: var(--white-color);
    font-size: 1.5rem;
    backdrop-filter: blur(6px);
}

.help-search input::placeholder {
    color: rgba(255, 255, 255, 0.7);
}

.help-search button {
    position: absolute;
    right: 0.8rem;
    top: 50%;
    transform: translateY(-50%);
    width: 42px;
    height: 42px;
    border-radius: 50%;
    border: none;
    background: var(--white-color);
    color: var(--moonstone-color-900);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.18);
}

.help-main {
    padding: 6.4rem 0 4.8rem;
}

.help-container {
    max-width: var(--max-width-xl-fix);
    margin: 0 auto;
    padding: 0 2.4rem;
}

.help-main-grid {
    display: grid;
    grid-template-columns: minmax(0, 2fr) minmax(0, 1fr);
    gap: 4.8rem;
    align-items: start;
}

.faq-card {
    background: var(--white-color);
    border-radius: 2.4rem;
    padding: 3.2rem;
    box-shadow: 0 18px 40px rgba(17, 34, 68, 0.08);
    display: grid;
    gap: 2.4rem;
}

.faq-card-header span {
    text-transform: uppercase;
    letter-spacing: 0.28rem;
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--moonstone-color-800);
}

.faq-card-header h2 {
    font-size: clamp(2.6rem, 4.2vw, 3.4rem);
    color: var(--grey-color-900);
}

.faq-card-header p {
    font-size: 1.5rem;
    line-height: 1.7;
    color: var(--grey-color-700);
}

.faq-container {
    display: grid;
    gap: 1.2rem;
}

.faq-item {
    background: var(--grey-color-100);
    border-radius: 1.6rem;
    border: 1px solid rgba(35, 46, 58, 0.08);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.faq-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 18px 30px rgba(17, 34, 68, 0.08);
}

.faq-question {
    padding: 1.8rem 2.4rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
}

.faq-question h3 {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--grey-color-900);
}

.faq-answer {
    padding: 0 2.4rem;
    max-height: 0;
    overflow: hidden;
    opacity: 0;
    transition: max-height 0.3s ease, padding 0.3s ease, opacity 0.3s ease;
    color: var(--grey-color-700);
    line-height: 1.6;
    font-size: 1.4rem;
}

.faq-item.active .faq-answer {
    max-height: 360px;
    padding: 0 2.4rem 2.4rem;
    opacity: 1;
}

.faq-icon {
    font-size: 1.6rem;
    color: var(--moonstone-color-900);
    transition: transform 0.3s ease;
}

.faq-item.active .faq-icon {
    transform: rotate(180deg);
}

.support-sidebar {
    display: grid;
    gap: 2.4rem;
}

.support-card {
    background: var(--white-color);
    border-radius: 2.4rem;
    padding: 2.8rem;
    box-shadow: 0 16px 32px rgba(17, 34, 68, 0.08);
    border: 1px solid rgba(35, 46, 58, 0.05);
}

.support-card--highlight {
    background: linear-gradient(135deg, rgba(53, 158, 180, 0.12), rgba(35, 46, 58, 0.12));
    border: 1px solid rgba(53, 158, 180, 0.4);
}

.support-card span {
    text-transform: uppercase;
    letter-spacing: 0.24rem;
    font-size: 1.1rem;
    color: var(--moonstone-color-800);
    font-weight: 600;
}

.support-card h3 {
    font-size: 2.2rem;
    color: var(--grey-color-900);
    margin: 1.2rem 0;
}

.support-card p {
    font-size: 1.4rem;
    line-height: 1.7;
    color: var(--grey-color-700);
}

.support-card ul {
    list-style: none;
    margin: 1.8rem 0 0;
    padding: 0;
    display: grid;
    gap: 1.2rem;
}

.support-card ul li {
    display: flex;
    gap: 1rem;
    font-size: 1.4rem;
    color: var(--grey-color-800);
    line-height: 1.6;
}

.support-card ul li i {
    color: var(--moonstone-color-800);
    margin-top: 0.2rem;
}

.support-card .cta-link {
    display: inline-flex;
    align-items: center;
    gap: 0.8rem;
    margin-top: 2rem;
    color: var(--moonstone-color-900);
    font-weight: 600;
    text-decoration: none;
}

.support-links a {
    color: var(--grey-color-900);
    text-decoration: none;
    transition: color 0.2s ease;
}

.support-links a:hover {
    color: var(--moonstone-color-900);
}

.contact-section {
    padding: 6.4rem 0 8rem;
    background: var(--white-color);
}

.contact-header {
    text-align: center;
    display: grid;
    gap: 1.2rem;
    margin-bottom: 3.6rem;
}

.contact-header span {
    text-transform: uppercase;
    letter-spacing: 0.28rem;
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--moonstone-color-800);
}

.contact-header h2 {
    font-size: clamp(2.6rem, 4vw, 3.2rem);
    color: var(--grey-color-900);
}

.contact-header p {
    font-size: 1.5rem;
    color: var(--grey-color-700);
    max-width: 60rem;
    margin: 0 auto;
    line-height: 1.6;
}

.help-contact-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.5fr) minmax(0, 1fr);
    gap: 3.2rem;
    align-items: start;
}

.contact-form-card {
    background: var(--grey-color-100);
    border-radius: 2.4rem;
    padding: 3.2rem;
    box-shadow: 0 16px 36px rgba(17, 34, 68, 0.08);
    border: 1px solid rgba(35, 46, 58, 0.05);
}

.form-group {
    margin-bottom: 18px;
}

.form-label {
    display: block;
    font-weight: 600;
    color: var(--grey-color-900);
    margin-bottom: 8px;
    font-size: 1.35rem;
}

.form-input,
.form-textarea {
    width: 100%;
    padding: 14px 16px;
    border: 1.5px solid #d8dbe0;
    border-radius: 12px;
    font-size: 1.45rem;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    background: var(--white-color);
    color: var(--grey-color-800);
}

.form-input:focus,
.form-textarea:focus {
    outline: none;
    border-color: var(--moonstone-color-900);
    box-shadow: 0 0 0 3px rgba(53, 158, 180, 0.2);
}

.form-textarea {
    min-height: 160px;
    resize: vertical;
}

.btn-submit {
    background: linear-gradient(135deg, var(--moonstone-color-900) 0%, var(--moonstone-color-600) 100%);
    color: var(--white-color);
    border: none;
    padding: 16px 0;
    border-radius: 999px;
    font-size: 1.5rem;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    width: 100%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.8rem;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 14px 32px rgba(53, 158, 180, 0.35);
}

.alert {
    padding: 16px 20px;
    border-radius: 12px;
    font-weight: 500;
    margin-bottom: 20px;
    font-size: 1.4rem;
}

.alert-success {
    background: rgba(16, 185, 129, 0.12);
    color: #047857;
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.alert-error {
    background: rgba(239, 68, 68, 0.12);
    color: #b91c1c;
    border: 1px solid rgba(239, 68, 68, 0.35);
}

.contact-info-stack {
    display: grid;
    gap: 2.4rem;
}

.info-cards {
    display: grid;
    gap: 1.6rem;
}

.info-card {
    background: var(--white-color);
    border-radius: 2rem;
    padding: 2.4rem;
    box-shadow: 0 14px 32px rgba(17, 34, 68, 0.08);
    border: 1px solid rgba(35, 46, 58, 0.06);
    display: grid;
    gap: 0.6rem;
}

.info-card-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: var(--moonstone-color-100);
    color: var(--moonstone-color-900);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.6rem;
}

.info-card h3 {
    font-size: 1.6rem;
    color: var(--grey-color-900);
    font-weight: 600;
}

.info-card p {
    font-size: 1.4rem;
    color: var(--grey-color-700);
    line-height: 1.6;
    word-break: break-word;
}

.info-meta {
    font-size: 1.25rem;
    color: var(--grey-color-500);
}

.map-card {
    background: var(--grey-color-100);
    border-radius: 2rem;
    padding: 1.6rem;
    box-shadow: 0 14px 32px rgba(17, 34, 68, 0.08);
    border: 1px solid rgba(35, 46, 58, 0.05);
}

.map-iframe {
    width: 100%;
    height: 280px;
    border-radius: 1.6rem;
    border: none;
}

@media (max-width: 1200px) {
    .help-main-grid,
    .help-contact-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .help-hero {
        padding: 5.6rem 0 4.8rem;
    }

    .hero-container {
        padding: 0 1.6rem;
    }

    .faq-card,
    .contact-form-card {
        padding: 2.4rem;
    }

    .btn-submit {
        font-size: 1.4rem;
    }

    .map-iframe {
        height: 220px;
    }
}
</style>

<div class="help-center-page">
    <section class="help-hero">
        <div class="hero-container">
            <span class="hero-overline">Customer Support</span>
            <h1>Help Center</h1>
            <p>Find answers, explore guides, and connect with the <?php echo htmlspecialchars($siteName); ?> support team.</p>
            <div class="help-search">
                <input type="text" placeholder="Search for help..." id="searchInput">
                <button type="button" onclick="searchFAQ()">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </section>

    <section class="help-main">
        <div class="help-container">
            <div class="help-main-grid">
                <div class="faq-card">
                    <div class="faq-card-header">
                        <span>Knowledge base</span>
                        <h2>Frequently Asked Questions</h2>
                        <p>Start with the topics our customers ask about most often. Use the search above to quickly filter through the answers.</p>
                    </div>

                    <div class="faq-container" id="faqContainer">
                        <div class="faq-item">
                            <div class="faq-question" onclick="toggleFAQ(this)">
                                <h3>How do I create an account?</h3>
                                <i class="fas fa-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Click on the “Register” button in the top navigation and complete the sign-up form. After submitting, confirm your email address via the verification link sent to your inbox.</p>
                            </div>
                        </div>

                        <div class="faq-item">
                            <div class="faq-question" onclick="toggleFAQ(this)">
                                <h3>How do I transfer money?</h3>
                                <i class="fas fa-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Navigate to the Transfer section in your dashboard. Choose the transfer type, enter the recipient details and amount, then authorize the transfer with your four-digit Transfer PIN.</p>
                            </div>
                        </div>

                        <div class="faq-item">
                            <div class="faq-question" onclick="toggleFAQ(this)">
                                <h3>How do I reset my password?</h3>
                                <i class="fas fa-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Select “Forgot Password” on the login page, submit your registered email address, and follow the secure reset link sent to you.</p>
                            </div>
                        </div>

                        <div class="faq-item">
                            <div class="faq-question" onclick="toggleFAQ(this)">
                                <h3>What are the transfer fees?</h3>
                                <i class="fas fa-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Internal transfers are free. Domestic transfers carry a small percentage fee, while international transfers include FX and compliance charges. Fees are displayed before you confirm every transaction.</p>
                            </div>
                        </div>

                        <div class="faq-item">
                            <div class="faq-question" onclick="toggleFAQ(this)">
                                <h3>How do I apply for a loan?</h3>
                                <i class="fas fa-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Open the Loans section and select “Apply for Loan”. Provide the requested information and documentation. Our credit team reviews submissions within 24–48 hours.</p>
                            </div>
                        </div>

                        <div class="faq-item">
                            <div class="faq-question" onclick="toggleFAQ(this)">
                                <h3>How do I apply for a card?</h3>
                                <i class="fas fa-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Visit the Cards section to choose between debit, credit, prepaid, or virtual cards. Submit the application and we’ll notify you once it is approved and ready.</p>
                            </div>
                        </div>

                        <div class="faq-item">
                            <div class="faq-question" onclick="toggleFAQ(this)">
                                <h3>How do I complete KYC verification?</h3>
                                <i class="fas fa-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Head to Profile &gt; KYC Verification. Upload a valid government ID and proof of address. You’ll receive confirmation as soon as our compliance team finishes the review.</p>
                            </div>
                        </div>

                        <div class="faq-item">
                            <div class="faq-question" onclick="toggleFAQ(this)">
                                <h3>What security measures are in place?</h3>
                                <i class="fas fa-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>We protect every session with bank-grade encryption, two-factor authentication, device monitoring, and fraud detection. Learn more in the Security Center.</p>
                            </div>
                        </div>

                        <div class="faq-item">
                            <div class="faq-question" onclick="toggleFAQ(this)">
                                <h3>How long do transactions take?</h3>
                                <i class="fas fa-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Internal transfers are instant. Domestic transfers typically settle in 1–3 business days, while international wires may require up to 5 business days depending on the destination bank.</p>
                            </div>
                        </div>

                        <div class="faq-item">
                            <div class="faq-question" onclick="toggleFAQ(this)">
                                <h3>How do I view my transaction history?</h3>
                                <i class="fas fa-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Open the Transactions section and filter by date, type, or account. You can export the full history as PDF or CSV for your records.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <aside class="support-sidebar">
                    <div class="support-card support-card--highlight">
                        <span>Need an answer fast?</span>
                        <h3>Talk with our specialists</h3>
                        <p>Our customer success team is available Monday to Friday, 8:00 AM – 6:00 PM EST.</p>
                        <ul>
                            <li><i class="fas fa-comments"></i>Start a live chat using the widget in the lower-right corner.</li>
                            <li><i class="fas fa-phone-alt"></i>Call us at <?php echo htmlspecialchars($supportPhone); ?></li>
                            <li><i class="fas fa-envelope"></i>Email <?php echo htmlspecialchars($siteEmail); ?></li>
                        </ul>
                        <a class="cta-link" href="#contact-support">
                            <i class="fas fa-paper-plane"></i>
                            Send us a secure message
                        </a>
                    </div>

                    <div class="support-card">
                        <h3>Popular resources</h3>
                        <p>Explore detailed guides to keep your banking experience secure and seamless.</p>
                        <ul class="support-links">
                            <li><i class="fas fa-shield-alt"></i><a href="<?php echo SITE_URL; ?>/security">Security center</a></li>
                            <li><i class="fas fa-question-circle"></i><a href="<?php echo SITE_URL; ?>/faqs">Full FAQ library</a></li>
                            <li><i class="fas fa-file-alt"></i><a href="<?php echo SITE_URL; ?>/terms">Terms &amp; policies</a></li>
                        </ul>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <section class="contact-section" id="contact-support">
        <div class="help-container">
            <div class="contact-header">
                <span>Contact</span>
                <h2>Talk with our support team</h2>
                <p>Share the details of your request and we’ll respond within one business day.</p>
            </div>

            <div class="help-contact-grid">
                <div class="contact-form-card">
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo SITE_URL; ?>/help-center">
                        <input type="hidden" name="contact_form" value="1">

                        <div class="form-group">
                            <label class="form-label" for="contactName">Your Name *</label>
                            <input type="text" id="contactName" name="name" class="form-input" placeholder="Enter your full name" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="contactEmail">Your Email *</label>
                            <input type="email" id="contactEmail" name="email" class="form-input" placeholder="Enter your email address" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="contactSubject">Subject *</label>
                            <input type="text" id="contactSubject" name="subject" class="form-input" placeholder="How can we help you?" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="contactMessage">Message *</label>
                            <textarea id="contactMessage" name="message" class="form-textarea" placeholder="Provide additional details so we can assist faster." required></textarea>
                        </div>

                        <button type="submit" class="btn-submit">
                            <i class="fas fa-paper-plane"></i>
                            Send Message
                        </button>
                    </form>
                </div>

                <div class="contact-info-stack">
                    <div class="info-cards">
                        <div class="info-card">
                            <div class="info-card-icon"><i class="fas fa-envelope"></i></div>
                            <h3>Email Us</h3>
                            <p><?php echo htmlspecialchars($siteEmail); ?></p>
                        </div>
                        <div class="info-card">
                            <div class="info-card-icon"><i class="fas fa-phone-alt"></i></div>
                            <h3>Call Us</h3>
                            <p><?php echo htmlspecialchars($supportPhone); ?></p>
                            <span class="info-meta"><?php echo htmlspecialchars($supportHours); ?></span>
                        </div>
                        <div class="info-card">
                            <div class="info-card-icon"><i class="fas fa-map-marker-alt"></i></div>
                            <h3>Visit Us</h3>
                            <p><?php echo htmlspecialchars($bankAddress); ?></p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>

<script>
function toggleFAQ(element) {
    const faqItem = element.parentElement;
    const isActive = faqItem.classList.contains('active');
    
    // Close all FAQ items
    document.querySelectorAll('.faq-item').forEach(item => {
        item.classList.remove('active');
    });
    
    // Open clicked item if it wasn't active
    if (!isActive) {
        faqItem.classList.add('active');
    }
}

function searchFAQ() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const faqItems = document.querySelectorAll('.faq-item');
    
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question h3').textContent.toLowerCase();
        const answer = item.querySelector('.faq-answer p').textContent.toLowerCase();
        
        if (question.includes(searchTerm) || answer.includes(searchTerm)) {
            item.style.display = 'block';
            item.classList.add('active');
        } else {
            item.style.display = 'none';
        }
    });
}

// Allow Enter key to trigger search
document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        searchFAQ();
    }
});

// Scroll to contact form after form submission if there's a success/error message
document.addEventListener('DOMContentLoaded', function() {
    // Check if there's a success or error alert
    const successAlert = document.querySelector('.alert-success');
    const errorAlert = document.querySelector('.alert-error');
    
    // Check if URL has the contact-support anchor
    const hasAnchor = window.location.hash === '#contact-support';
    
    // If there's an alert or anchor, scroll to the contact form
    if ((successAlert || errorAlert) || hasAnchor) {
        setTimeout(function() {
            const contactSection = document.getElementById('contact-support');
            if (contactSection) {
                // Scroll to the form with smooth behavior
                contactSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                
                // If there's an alert, also scroll to it specifically for better visibility
                if (successAlert || errorAlert) {
                    setTimeout(function() {
                        const alert = successAlert || errorAlert;
                        if (alert) {
                            alert.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    }, 300);
                }
            }
        }, 100);
    }
});
</script>

<?php include __DIR__ . '/views/layouts/footer.php'; ?>

