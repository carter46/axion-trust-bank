    </main>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-links-container">
                <div>
                    <nav>
                        <p class="footer-links-heading">
                            <span class="no-link">Quick Links</span>
                        </p>
                        <ul class="footer-nav">
                            <li><a class="footer-link" href="<?php echo SITE_URL; ?>/">Home</a></li>
                            <li><a class="footer-link" href="<?php echo SITE_URL; ?>/about">About</a></li>
                            <li><a class="footer-link" href="<?php echo SITE_URL; ?>/services">Services</a></li>
                            <li><a class="footer-link" href="<?php echo SITE_URL; ?>/help-center">Help Center</a></li>
                            <li><a class="footer-link" href="<?php echo SITE_URL; ?>/charity">Charity</a></li>
                        </ul>
                    </nav>
                </div>
                <div>
                    <nav>
                        <p class="footer-links-heading">
                            <span class="no-link">Products</span>
                        </p>
                        <ul class="footer-nav">
                            <li><a class="footer-link" href="<?php echo SITE_URL; ?>/accounts">Accounts</a></li>
                            <li><a class="footer-link" href="<?php echo SITE_URL; ?>/cards">Cards</a></li>
                            <li><a class="footer-link" href="<?php echo SITE_URL; ?>/loans">Loans</a></li>
                            <li><a class="footer-link" href="<?php echo SITE_URL; ?>/investments">Investments</a></li>
                        </ul>
                    </nav>
                </div>
                <div>
                    <nav>
                        <p class="footer-links-heading">
                            <span class="no-link">Support</span>
                        </p>
                        <ul class="footer-nav">
                            <li><a class="footer-link" href="<?php echo SITE_URL; ?>/security">Security</a></li>
                            <li><a class="footer-link" href="<?php echo SITE_URL; ?>/faqs">FAQs</a></li>
                            <li><a class="footer-link" href="<?php echo SITE_URL; ?>/terms">Terms</a></li>
                        </ul>
                    </nav>
                </div>
                <div>
                    <nav>
                        <p class="footer-links-heading">
                            <span class="no-link">Partnership</span>
                        </p>
                        <ul class="footer-nav">
                            <li><a class="footer-link" href="<?php echo SITE_URL; ?>/partnership#visa">Visa</a></li>
                            <li><a class="footer-link" href="<?php echo SITE_URL; ?>/partnership#mastercard">Mastercard</a></li>
                            <li><a class="footer-link" href="<?php echo SITE_URL; ?>/partnership#american-express">American Express</a></li>
                            <li><a class="footer-link" href="<?php echo SITE_URL; ?>/partnership#swift">SWIFT</a></li>
                            <li><a class="footer-link" href="<?php echo SITE_URL; ?>/partnership" style="font-weight: 600; color: #359eb4;">Load More →</a></li>
                        </ul>
                    </nav>
                </div>
                <div>
                    <nav>
                        <p class="footer-links-heading">
                            <span class="no-link">Transfer Money</span>
                        </p>
                        <ul class="footer-nav">
                            <li><a class="footer-link" href="<?php echo SITE_URL; ?>/auth/login">Register/Login</a></li>
                            <li><a class="footer-link" href="<?php echo SITE_URL; ?>/dashboard">IBank Transfer</a></li>
                            <li><a class="footer-link" href="<?php echo SITE_URL; ?>/dashboard">USA Money Transfer</a></li>
                            <li><a class="footer-link" href="<?php echo SITE_URL; ?>/dashboard">UK Money Transfer</a></li>
                            <li><a class="footer-link" href="<?php echo SITE_URL; ?>/dashboard">Euro Money Transfer</a></li>
                        </ul>
                    </nav>
                </div>
                </div>
            <?php
            // Get dynamic branding for footer with error handling
            try {
                if (!function_exists('getSiteName')) {
                    error_log("[Footer Debug] getSiteName() function not found");
                    $siteName = 'Cosmopolitan Trust Bank';
                } else {
                    $siteName = getSiteName();
                }
                
                if (!function_exists('getSiteLogo')) {
                    error_log("[Footer Debug] getSiteLogo() function not found");
                    $siteLogo = SITE_URL . '/assets/images/logo.svg';
                } else {
                    $siteLogo = getSiteLogo();
                }
                
                // Ensure values are not empty
                $siteName = !empty($siteName) ? $siteName : 'Cosmopolitan Trust Bank';
            } catch (Exception $e) {
                error_log("[Footer Debug] Critical error in branding: " . $e->getMessage());
                $siteName = 'Cosmopolitan Trust Bank';
                $siteLogo = SITE_URL . '/assets/images/logo.svg';
            }
            ?>
            <div class="footer-social">
                <a class="social-icon" href="<?php echo SITE_URL; ?>/">
                    <?php
                    // Use logo image if available, otherwise show text
                    $showFooterLogo = false;
                    
                    if (!empty($siteLogo) && strpos($siteLogo, 'http') !== false) {
                        if (strpos($siteLogo, 'logo.svg') === false) {
                            try {
                                $cleanLogoUrl = strtok($siteLogo, '?');
                                
                                if (defined('BASE_PATH')) {
                                    $logoPath = str_replace(SITE_URL, BASE_PATH, $cleanLogoUrl);
                                    if (file_exists($logoPath)) {
                                        $showFooterLogo = true;
                                    } else {
                                        error_log("[Footer Debug] Logo file not found at: " . $logoPath);
                                    }
                                } else {
                                    error_log("[Footer Debug] BASE_PATH not defined in footer");
                                }
                            } catch (Exception $e) {
                                error_log("[Footer Debug] Error checking footer logo: " . $e->getMessage());
                            }
                        }
                    }
                    
                    if ($showFooterLogo): ?>
                        <img loading="lazy" src="<?php echo htmlspecialchars($siteLogo); ?>" alt="<?php echo htmlspecialchars($siteName); ?>" style="max-height: 50px; max-width: 200px; object-fit: contain;">
                    <?php else: ?>
                        <span style="font-size: 24px; font-weight: 700; color: #1a2b5f;"><?php echo htmlspecialchars($siteName); ?></span>
                    <?php endif; ?>
                </a>
                <ul class="footer-social-links">
                    <li><a class="social-icon" href="https://www.facebook.com" target="_blank">
                        <img loading="lazy" class="footer-social-icon" src="https://images.ctfassets.net/2rrb5ka4jpe4/6w7ijp2Z3AubpLCuWO9YJq/6a11dbdbbcf9d10f1bc97059f8db4ef2/ico-facebook.svg" alt="Facebook Icon">
                    </a></li>
                    <li><a class="social-icon" href="https://www.instagram.com" target="_blank">
                        <img loading="lazy" class="footer-social-icon" src="https://images.ctfassets.net/2rrb5ka4jpe4/59mEtPWYQYTUZu5lMicffY/086584f5e54b0fb647bed2f4450f85b0/ico-instagram.svg" alt="Instagram Logo">
                    </a></li>
                    <li><a class="social-icon" href="https://twitter.com" target="_blank">
                        <img loading="lazy" class="footer-social-icon" src="https://images.ctfassets.net/2rrb5ka4jpe4/4Qthn6uaRlKMIAusXkjbFx/877a9c5dc4efacb0b13a27f491e68e9c/ico-twitter.svg" alt="Share on Twitter - Icon">
                    </a></li>
                    <li><a class="social-icon" href="https://www.linkedin.com" target="_blank">
                        <img loading="lazy" class="footer-social-icon" src="https://images.ctfassets.net/2rrb5ka4jpe4/6BPljhJwlPO0NjD4nh5hMp/22ea291ed0792fdf4827d871c38bee95/ico-linkedin.svg" alt="Share on LinkedIn - Icon">
                    </a></li>
                    </ul>
                </div>
            <div class="separator"></div>
            <div class="footer-terms-container">
                <div>
                    <p><?php echo htmlspecialchars($siteName); ?> designs and operates the banking website and app. We are committed to providing secure, borderless financial services globally.</p>
                    <p><i>All financial products are subject to terms and conditions. Please refer to our Terms of Service for details.</i></p>
                </div>
                <div class="pink-800">Support the Cosmos Charity Home and help change lives.</div>
                <div class="copyright">
                    <span>© <?php echo htmlspecialchars($siteName); ?> <?php echo date('Y'); ?></span>
                    <div class="reserved-text">
                        <p>All rights reserved.</p>
                        <p>Registered Office: 12 Park Avenue, New York, NY, USA</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="<?php echo SITE_URL; ?>/assets/js/app.js?v=<?php echo defined('ASSET_VERSION') ? ASSET_VERSION : time(); ?>"></script>
    <?php if (isset($additionalScripts)): ?>
        <?php echo $additionalScripts; ?>
    <?php endif; ?>
    
    <?php
    // Include livechat - it will automatically show only on allowed pages (home, contact, help-center)
    include __DIR__ . '/../../includes/livechat.php';
    ?>
    
    <!-- Translation widget container (marketing/public layout) -->
    <div class="gtranslate_wrapper"></div>
    
    <?php
    // Include translation widget scripts - translates all content on the site
    include __DIR__ . '/../../includes/translation.php';
    ?>
</body>
</html>
