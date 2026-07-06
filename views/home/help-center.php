<?php 
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

// Get dynamic branding with error handling
try {
    if (!function_exists('getSiteName')) {
        error_log("[Help Center Debug] getSiteName() function not found");
        $siteName = 'Cosmopolitan Trust Bank';
        $siteInitials = 'CTB';
    } else {
        $siteName = getSiteName();
        $siteInitials = getSiteInitials();
    }
    
    // Ensure site name is set
    if (empty($siteName)) {
        $siteName = 'Cosmopolitan Trust Bank';
    }
    if (empty($siteInitials)) {
        $siteInitials = 'CTB';
    }
    $pageTitle = 'Help Center - ' . $siteName;
    
    // Get bank settings
    $siteEmail = function_exists('getSetting') ? getSetting('site_email', SMTP_FROM) : SMTP_FROM;
    $supportPhone = function_exists('getSetting') ? getSetting('support_phone', '+1 (555) 123-4567') : '+1 (555) 123-4567';
    $supportHours = function_exists('getSetting') ? getSetting('support_hours', 'Monday - Friday, 8:00 AM - 6:00 PM EST') : 'Monday - Friday, 8:00 AM - 6:00 PM EST';
    $bankAddress = function_exists('getSetting') ? getSetting('bank_address', '123 Banking Street, New York, NY 10001') : '123 Banking Street, New York, NY 10001';
} catch (Exception $e) {
    error_log("[Help Center Debug] Critical error: " . $e->getMessage());
    // Fallback values
    $siteName = getSiteName() ?: 'Cosmopolitan Trust Bank';
    $siteInitials = getSiteInitials() ?: 'CTB';
    $pageTitle = 'Help Center - ' . $siteName;
    $siteEmail = SMTP_FROM;
    $supportPhone = '+1 (555) 123-4567';
    $supportHours = 'Monday - Friday, 8:00 AM - 6:00 PM EST';
    $bankAddress = '123 Banking Street, New York, NY 10001';
}

// Check if logged in
$isLoggedIn = isLoggedIn();

// Include head
include __DIR__ . '/../../includes/head.php';

// Include sidebar if logged in
if ($isLoggedIn) {
    include __DIR__ . '/../../includes/sidebar.php';
}
?>

<style>
.help-center-hero {
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    color: white;
    padding: 80px 0;
    text-align: center;
}

.help-center-hero h1 {
    font-size: 48px;
    font-weight: 700;
    margin-bottom: 16px;
}

.help-center-hero p {
    font-size: 20px;
    opacity: 0.9;
}

.search-box {
    max-width: 600px;
    margin: 40px auto 0;
    position: relative;
}

.search-input {
    width: 100%;
    padding: 16px 50px 16px 20px;
    border: none;
    border-radius: 50px;
    font-size: 16px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.search-button {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    background: #1e3a8a;
    color: white;
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.help-content {
    padding: 60px 0;
}

.section {
    margin-bottom: 60px;
}

.section-title {
    font-size: 32px;
    font-weight: 700;
    color: #032B44;
    margin-bottom: 30px;
    text-align: center;
}

.faq-container {
    max-width: 800px;
    margin: 0 auto;
}

.faq-item {
    background: white;
    border-radius: 12px;
    margin-bottom: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    overflow: hidden;
}

.faq-question {
    padding: 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    transition: background 0.3s;
}

.faq-question:hover {
    background: #f8f9fa;
}

.faq-question h3 {
    margin: 0;
    font-size: 18px;
    color: #032B44;
}

.faq-answer {
    padding: 0 24px;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s, padding 0.3s;
}

.faq-item.active .faq-answer {
    max-height: 1000px;
    padding: 0 24px 24px;
}

.faq-icon {
    font-size: 20px;
    color: #1e3a8a;
    transition: transform 0.3s;
}

.faq-item.active .faq-icon {
    transform: rotate(180deg);
}

.contact-section {
    background: #f8f9fa;
    padding: 60px 0;
}

.contact-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    margin-bottom: 50px;
}

.contact-card {
    background: white;
    padding: 32px;
    border-radius: 16px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    text-align: center;
}

.contact-icon {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 28px;
    color: white;
}

.contact-card h3 {
    color: #032B44;
    margin-bottom: 12px;
    font-size: 20px;
}

.contact-card p {
    color: #666;
    margin: 0;
}

.contact-form-container {
    max-width: 600px;
    margin: 0 auto;
    background: white;
    padding: 40px;
    border-radius: 16px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.form-group {
    margin-bottom: 24px;
}

.form-label {
    display: block;
    font-weight: 600;
    color: #032B44;
    margin-bottom: 8px;
}

.form-input,
.form-textarea {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 16px;
    transition: border-color 0.3s;
}

.form-input:focus,
.form-textarea:focus {
    outline: none;
    border-color: #1e3a8a;
}

.form-textarea {
    min-height: 150px;
    resize: vertical;
}

.btn-submit {
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    color: white;
    border: none;
    padding: 14px 32px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.2s;
    width: 100%;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
}

.map-container {
    max-width: 100%;
    margin-top: 40px;
}

.map-iframe {
    width: 100%;
    height: 400px;
    border-radius: 12px;
    border: none;
}

@media (max-width: 768px) {
    .help-center-hero h1 {
        font-size: 32px;
    }
    
    .help-center-hero p {
        font-size: 18px;
    }
    
    .section-title {
        font-size: 24px;
    }
    
    .contact-grid {
        grid-template-columns: 1fr;
    }
    
    .contact-form-container {
        padding: 24px;
    }
    
    .search-input {
        padding: 12px 40px 12px 16px;
    }
}
</style>

<div class="help-center-hero">
    <div class="container">
        <h1>Help Center</h1>
        <p>Find answers to your questions or get in touch with our support team</p>
        
        <div class="search-box">
            <input type="text" class="search-input" placeholder="Search for help..." id="searchInput">
            <button class="search-button" onclick="searchFAQ()">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>
</div>

<div class="help-content">
    <div class="container">
        <!-- FAQ Section -->
        <div class="section">
            <h2 class="section-title">Frequently Asked Questions</h2>
            
            <div class="faq-container" id="faqContainer">
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <h3>How do I create an account?</h3>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        <p>To create an account, click on the "Register" button in the top navigation. You'll need to provide your personal information, email address, and create a secure password. Once registered, you'll receive a verification email to activate your account.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <h3>How do I transfer money?</h3>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Navigate to the Transfer section in your dashboard. You can transfer money internally to other accounts within the bank or externally to other banks. Enter the recipient's details, amount, and confirm the transaction using your transfer PIN.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <h3>How do I reset my password?</h3>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        <p>If you've forgotten your password, click on the "Forgot Password" link on the login page. Enter your email address and you'll receive a password reset link. Follow the instructions in the email to create a new password.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <h3>What are the transfer fees?</h3>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Internal transfers within our bank are free of charge. Domestic transfers have a small fee percentage, and international transfers have a higher fee percentage. All fees are clearly displayed before you confirm your transaction.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <h3>How do I apply for a loan?</h3>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Go to the Loans section in your dashboard and click "Apply for Loan". Fill out the loan application form with your details, requested amount, and purpose. Our team will review your application and get back to you within 24-48 hours.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <h3>How do I apply for a card?</h3>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Navigate to the Cards section and click "Apply for Card". Choose between debit, credit, prepaid, or virtual cards. Complete the application and our team will review it. Once approved, your card will be issued and mailed to your registered address.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <h3>How do I complete KYC verification?</h3>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Go to Profile > KYC Verification. Upload a valid government-issued ID (driver's license, passport, or national ID) and proof of address. Our compliance team will review your documents and you'll be notified once verification is complete.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <h3>What security measures are in place?</h3>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        <p>We use bank-level encryption, two-factor authentication, transaction monitoring, and secure login procedures. Your data is encrypted both in transit and at rest. We also offer optional biometric authentication for added security.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <h3>How long do transactions take?</h3>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Internal transfers are instant. Domestic transfers typically take 1-3 business days, while international wire transfers may take 3-5 business days depending on the destination country and bank.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <h3>How do I view my transaction history?</h3>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Go to the Transactions section in your dashboard. You can filter transactions by date range, type, category, and account. You can also download your transaction history as a PDF or CSV file for your records.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Contact Section -->
    <div class="contact-section">
        <div class="container">
            <h2 class="section-title">Contact Us</h2>
            
            <div class="contact-grid">
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3>Email Us</h3>
                    <p><?php echo htmlspecialchars($siteEmail); ?></p>
                </div>
                
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <h3>Call Us</h3>
                    <p><?php echo htmlspecialchars($supportPhone); ?></p>
                    <p style="margin-top: 8px; font-size: 14px;"><?php echo htmlspecialchars($supportHours); ?></p>
                </div>
                
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h3>Visit Us</h3>
                    <p><?php echo htmlspecialchars($bankAddress); ?></p>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div class="contact-form-container">
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-error">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <input type="hidden" name="contact_form" value="1">
                    
                    <div class="form-group">
                        <label class="form-label">Your Name *</label>
                        <input type="text" name="name" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Your Email *</label>
                        <input type="email" name="email" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Subject *</label>
                        <input type="text" name="subject" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Message *</label>
                        <textarea name="message" class="form-textarea" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn-submit">Send Message</button>
                </form>
            </div>
        </div>
    </div>
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
</script>

<?php
// Include proper closing based on login status
if ($isLoggedIn) {
    include __DIR__ . '/../../includes/mobile-nav.php';
}
?>

