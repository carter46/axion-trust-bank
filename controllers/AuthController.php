<?php
class AuthController {
    
    public function index() {
        redirect('/auth/login');
    }
    
    public function login() {
        if (isLoggedIn()) {
            redirect('/dashboard');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = Security::sanitize($_POST['email']);
            $loginMethod = $_POST['login_method'] ?? 'password';
            
            // Validate CSRF token
            if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
                $_SESSION['error'] = 'Invalid request. Please try again.';
                redirect('/auth/login');
            }
            
            // Check for too many login attempts
            if (Security::checkLoginAttempts($email)) {
                $_SESSION['error'] = 'Too many login attempts. Please try again later.';
                redirect('/auth/login');
            }
            
            // Find user
            $userModel = new User();
            $user = $userModel->findByEmail($email);
            
            if (!$user) {
                Security::recordLoginAttempt($email, false);
                $_SESSION['error'] = 'Invalid email or credentials';
                redirect('/auth/login');
            }
            
            // Verify credentials based on login method
            $authenticated = false;
            
            if ($loginMethod === 'password') {
                $password = $_POST['password'] ?? '';
                $authenticated = Security::verifyPassword($password, $user['password_hash']);
                
                if (!$authenticated) {
                    Security::recordLoginAttempt($email, false);
                    $_SESSION['error'] = 'Invalid email or password';
                    redirect('/auth/login');
                }
            } elseif ($loginMethod === 'pin') {
                $loginPin = $_POST['login_pin'] ?? '';
                
                // Check if user has set up a login PIN
                if (empty($user['login_pin'])) {
                    $_SESSION['error'] = 'Login PIN not set up. Please use password to login.';
                    redirect('/auth/login');
                }
                
                $authenticated = password_verify($loginPin, $user['login_pin']);
                
                if (!$authenticated) {
                    Security::recordLoginAttempt($email, false);
                    $_SESSION['error'] = 'Invalid login PIN';
                    redirect('/auth/login');
                }
            } else {
                $_SESSION['error'] = 'Invalid login method';
                redirect('/auth/login');
            }
            
            // Check account status
            // Restricted users may still log in, but they will be blocked from financial actions.
            $status = $user['status'] ?? 'active';
            $isRestricted = function_exists('isRestrictedStatus') ? isRestrictedStatus($status) : false;
            
            $role = strtolower(trim((string)($user['role'] ?? 'user')));
            $isAdmin = ($role === 'admin');
            $isStaff = $isAdmin || ($role === 'support') || !empty($user['is_super_admin']);

            // Check if email is verified (skip for staff accounts)
            if (!$user['email_verified'] && !$isStaff) {
                $_SESSION['error'] = 'Please verify your email address before logging in.';
                $_SESSION['unverified_email'] = $email; // Store email for resend button
                redirect('/auth/login');
            }
            
            // Check if user has pending joint account request - prevent login until approved
            if (!$isStaff) {
                require_once __DIR__ . '/../models/JointAccount.php';
                $jointAccount = new JointAccount();
                if ($jointAccount->hasPendingRequest($user['id'])) {
                    $_SESSION['error'] = 'Your joint account request is pending approval. You will be able to log in once the primary account owner accepts your request.';
                    redirect('/auth/login');
                }
            }
            
            // Staff accounts must never be forced into OTP/2FA during login

            // Check if 2FA is required system-wide or enabled for user
            require_once __DIR__ . '/../includes/system-settings.php';
            $systemSettings = SystemSettings::getInstance();
            $twoFactorRequired = $systemSettings->is2FARequired();
            $twoFactorDisabled = $systemSettings->is2FADisabled();
            
            // Check if user has 2FA enabled (only check user's setting, not system requirement)
            // System requirement will be checked after login
            // Skip 2FA if it's disabled system-wide
            if (!$isStaff && $user['two_factor_enabled'] && !$twoFactorDisabled) {
                // Generate 2FA code
                $twoFactorMethod = $user['two_factor_method'] ?? 'email';
                $code = Security::generate2FACode($user['id'], $twoFactorMethod, 'login');
                
                // Send code
                if ($twoFactorMethod === 'email') {
                    require_once __DIR__ . '/../includes/email-template.php';
                    $emailTemplate = new EmailTemplate();
                    $emailContent = $emailTemplate->twoFactorEmail($user['full_name'], $code, 10);
                    $siteName = getSiteName() ?? 'SecureBank';
                    sendEmail($user['email'], 'Two-Factor Authentication Code - ' . $siteName, $emailContent);
                } else if ($twoFactorMethod === 'sms') {
                    sendSMS($user['phone'], "Your SecureBank verification code is: $code");
                }
                
                // Store user ID in session temporarily
                $_SESSION['temp_user_id'] = $user['id'];
                $_SESSION['success'] = 'Verification code sent to your ' . $twoFactorMethod;
                redirect('/auth/verify-2fa');
            }
            
            // Login successful
            Security::recordLoginAttempt($email, true);
            $this->createSession($user);
            
            // Staff accounts always go to /admin and skip any 2FA requirement
            if ($isStaff) {
                $redirectTo = $_SESSION['redirect_after_login'] ?? '/admin';
                unset($_SESSION['redirect_after_login']);
                $separator = strpos($redirectTo, '?') !== false ? '&' : '?';
                redirect($redirectTo . $separator . 'logged_in=1');
            }

            // Restricted users go straight to dashboard with a clear banner message
            if ($isRestricted) {
                $_SESSION['warning'] = restrictedAccountMessage();
                redirect('/dashboard?restricted=1&logged_in=1');
            }
            
            // 2FA is optional — do not force-redirect when disabled.
            // Check if security setup is incomplete (Transfer PIN, Login PIN only)
            if (isSecuritySetupIncomplete($user['id'])) {
                $_SESSION['security_setup_required'] = true;
                redirect('/profile/security?logged_in=1');
            }
            
            // Redirect regular users to dashboard
            $redirectTo = $_SESSION['redirect_after_login'] ?? '/dashboard';
            unset($_SESSION['redirect_after_login']);
            
            // Add login parameter to show loading screen after login
            $separator = strpos($redirectTo, '?') !== false ? '&' : '?';
            redirect($redirectTo . $separator . 'logged_in=1');
        }
        
        include __DIR__ . '/../views/auth/login.php';
    }
    
    public function register() {
        if (isLoggedIn()) {
            redirect('/dashboard');
        }
        
        // Check if registrations are allowed
        require_once __DIR__ . '/../includes/system-settings.php';
        $systemSettings = SystemSettings::getInstance();
        if (!$systemSettings->allowNewRegistrations()) {
            $_SESSION['error'] = 'New registrations are currently disabled. Please contact support for assistance.';
            redirect('/auth/login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF token
            if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
                $_SESSION['error'] = 'Invalid request. Please try again.';
                redirect('/auth/register');
            }
            
            // Sanitize inputs
            $data = [
                'full_name' => Security::sanitize($_POST['full_name']),
                'email' => Security::sanitize($_POST['email']),
                'phone' => Security::sanitize($_POST['phone']),
                'date_of_birth' => Security::sanitize($_POST['date_of_birth']),
                'gender' => Security::sanitize($_POST['gender'] ?? null),
                'address' => Security::sanitize($_POST['address']),
                'city' => Security::sanitize($_POST['city']),
                'state' => Security::sanitize($_POST['state']),
                'country' => Security::sanitize($_POST['country']),
                'postal_code' => Security::sanitize($_POST['postal_code']),
                'password' => $_POST['password'],
                'confirm_password' => $_POST['confirm_password'],
                'account_type' => Security::sanitize($_POST['account_type'] ?? ''),
                // Security questions - set defaults if not provided (registration form doesn't include them)
                'security_question_1' => Security::sanitize($_POST['security_question_1'] ?? 'What city were you born in?'),
                'security_answer_1' => Security::sanitize($_POST['security_answer_1'] ?? 'default'),
                'security_question_2' => Security::sanitize($_POST['security_question_2'] ?? 'What was the name of your first pet?'),
                'security_answer_2' => Security::sanitize($_POST['security_answer_2'] ?? 'default'),
                'marketing_emails' => isset($_POST['newsletter']) && $_POST['newsletter'] == '1' ? 1 : 0
            ];
            
            // Default to 'checking' for backward compatibility (form field is required, but this handles edge cases)
            if (empty($data['account_type'])) {
                $data['account_type'] = 'checking';
            }
            
            // Validate
            $errors = [];
            
            // Validate account type
            $validAccountTypes = ['checking', 'savings', 'business', 'joint', 'join_existing'];
            if (!in_array($data['account_type'], $validAccountTypes)) {
                $errors[] = 'Invalid account type selected';
            }
            
            // For join_existing, validate that account was found
            if ($data['account_type'] === 'join_existing') {
                if (empty($_POST['found_account_id'])) {
                    $errors[] = 'Please search and select an existing account to join';
                } else {
                    $data['found_account_id'] = (int)$_POST['found_account_id'];
                }
            }
            
            if (empty($data['full_name'])) {
                $errors[] = 'Full name is required';
            }
            
            if (!Security::validateEmail($data['email'])) {
                $errors[] = 'Valid email is required';
            }
            
            if (!Security::validatePassword($data['password'])) {
                $errors[] = 'Password must be at least 8 characters with uppercase, lowercase, and number';
            }
            
            if ($data['password'] !== $data['confirm_password']) {
                $errors[] = 'Passwords do not match';
            }
            
            // Check if email already exists
            $userModel = new User();
            if ($userModel->findByEmail($data['email'])) {
                $errors[] = 'Email already registered';
            }
            
            if (!empty($errors)) {
                $_SESSION['error'] = implode('<br>', $errors);
                redirect('/auth/register');
            }
            
            // Create user
            $userId = $userModel->create($data);
            
            if ($userId) {
                // Joint accounts get welcome email with embedded verify link; regular users receive verification only
                if ($data['account_type'] === 'join_existing') {
                    try {
                        require_once 'includes/email-template.php';
                        $emailTemplate = new EmailTemplate();
                        $db = Database::getInstance();
                        $sql = "SELECT token FROM email_verification_tokens WHERE user_id = ? AND used = 0 ORDER BY id DESC LIMIT 1";
                        $stmt = $db->query($sql, [$userId]);
                        $tokenData = $stmt->fetch();

                        if ($tokenData) {
                            $verificationLink = SITE_URL . "/auth/verify-email/" . $tokenData['token'];
                            $welcomeEmail = $emailTemplate->welcomeJointAccountEmail($data['full_name'], $verificationLink);
                            sendEmail($data['email'], 'Welcome - Joint Account Request - ' . getSiteName(), $welcomeEmail);
                        } else {
                            $welcomeEmail = $emailTemplate->welcomeJointAccountEmail($data['full_name'], SITE_URL . '/auth/login');
                            sendEmail($data['email'], 'Welcome - Joint Account Request - ' . getSiteName(), $welcomeEmail);
                        }
                    } catch (Exception $e) {
                        error_log("Joint welcome email error: " . $e->getMessage());
                    }
                }

                redirect('/auth/registration-success');
            } else {
                $_SESSION['error'] = 'Registration failed. Please try again.';
                redirect('/auth/register');
            }
        }
        
        include __DIR__ . '/../views/auth/register.php';
    }
    
    public function verify2fa() {
        if (!isset($_SESSION['temp_user_id'])) {
            redirect('/auth/login');
        }

        // If a staff user ever lands here (legacy state), bypass OTP entirely.
        $userModel = new User();
        $user = $userModel->findById($_SESSION['temp_user_id']);
        $role = strtolower(trim((string)($user['role'] ?? 'user')));
        $isStaff = ($role === 'admin') || ($role === 'support') || !empty($user['is_super_admin']);
        if ($user && $isStaff) {
            unset($_SESSION['temp_user_id']);
            $this->createSession($user);
            redirect('/admin?logged_in=1');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = Security::sanitize($_POST['code']);
            $userId = $_SESSION['temp_user_id'];
            
            if (Security::validate2FA($userId, $code, 'login')) {
                // Login successful
                $user = $userModel->findById($userId);
                
                unset($_SESSION['temp_user_id']);
                $this->createSession($user);
                
                // Redirect based on role (with login parameter for loading screen)
                if ($user['role'] === 'admin') {
                    redirect('/admin?logged_in=1');
                } else {
                    redirect('/dashboard?logged_in=1');
                }
            } else {
                $_SESSION['error'] = 'Invalid verification code';
                redirect('/auth/verify-2fa');
            }
        }
        
        include __DIR__ . '/../views/auth/verify-2fa.php';
    }
    
    public function resend2fa() {
        // Set JSON header
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['temp_user_id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid session']);
            exit;
        }
        
        $userId = $_SESSION['temp_user_id'];
        $userModel = new User();
        $user = $userModel->findById($userId);
        
        if (!$user || !$user['two_factor_enabled']) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'User not found or 2FA not enabled']);
            exit;
        }
        
        // Generate new 2FA code
        $code = Security::generate2FACode($userId, $user['two_factor_method'], 'login');
        
        // Send code
        if ($user['two_factor_method'] === 'email') {
            require_once __DIR__ . '/../includes/email-template.php';
            $emailTemplate = new EmailTemplate();
            $emailContent = $emailTemplate->twoFactorEmail($user['full_name'], $code, 10);
            $siteName = getSiteName() ?? 'SecureBank';
            sendEmail($user['email'], 'Two-Factor Authentication Code - ' . $siteName, $emailContent);
        } else if ($user['two_factor_method'] === 'sms') {
            sendSMS($user['phone'], "Your SecureBank verification code is: $code");
        }
        
        echo json_encode(['success' => true, 'message' => 'Code resent successfully']);
    }
    
    public function resendVerificationEmail() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $email = $input['email'] ?? '';
        
        if (empty($email)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Email is required']);
            exit;
        }
        
        $userModel = new User();
        $user = $userModel->findByEmail($email);
        
        if (!$user) {
            // Don't reveal if user exists or not for security
            echo json_encode(['success' => true, 'message' => 'If an account exists with this email, a verification email has been sent.']);
            exit;
        }
        
        // Check if email is already verified
        if ($user['email_verified']) {
            echo json_encode(['success' => false, 'message' => 'Email is already verified']);
            exit;
        }
        
        // Create new verification token and send email
        $userModel->createEmailVerificationToken($user['id']);
        
        echo json_encode(['success' => true, 'message' => 'Verification email sent successfully']);
    }
    
    public function registrationSuccess() {
        include __DIR__ . '/../views/auth/registration-success.php';
    }

    public function verificationSuccess() {
        if (!isLoggedIn()) {
            redirect('/auth/login');
        }
        include __DIR__ . '/../views/auth/verification-success.php';
    }

    public function verificationExpired() {
        include __DIR__ . '/../views/auth/verification-expired.php';
    }
    
    public function verifyEmail($token) {
        $userModel = new User();
        $db = Database::getInstance();

        $sql = "SELECT evt.*, u.email, u.email_verified
                FROM email_verification_tokens evt
                JOIN users u ON u.id = evt.user_id
                WHERE evt.token = ?";
        $stmt = $db->query($sql, [$token]);
        $tokenRow = $stmt->fetch();

        if (!$tokenRow) {
            $_SESSION['error'] = 'Invalid verification link.';
            redirect('/auth/login');
        }

        $email = $tokenRow['email'];
        $isExpired = strtotime($tokenRow['expires_at']) < time();
        $isUsed = !empty($tokenRow['used']);

        if ($isUsed || $isExpired) {
            $reason = $isUsed ? 'used' : 'expired';
            redirect('/auth/verification-expired?email=' . urlencode($email) . '&reason=' . $reason);
        }

        if (!$userModel->verifyEmail($token)) {
            redirect('/auth/verification-expired?email=' . urlencode($email) . '&reason=expired');
        }

        $userId = (int)$tokenRow['user_id'];
        $user = $userModel->findById($userId);

        require_once __DIR__ . '/../models/JointAccount.php';
        $jointAccount = new JointAccount();

        $sql = "SELECT COUNT(*) as count FROM joint_account_requests
                WHERE requesting_user_id = ? AND status = 'pending' AND expires_at > NOW()";
        $stmt = $db->query($sql, [$userId]);
        $result = $stmt->fetch();

        if ($result && $result['count'] > 0) {
            $sql = "SELECT account_id FROM joint_account_requests
                    WHERE requesting_user_id = ? AND status = 'pending' AND expires_at > NOW()
                    LIMIT 1";
            $stmt = $db->query($sql, [$userId]);
            $requestData = $stmt->fetch();

            if ($requestData) {
                $jointAccount->sendJointRequestConfirmationEmail($requestData['account_id'], $userId);
            }

            redirect('/auth/joint-account-confirmation');
        }

        try {
            require_once __DIR__ . '/../includes/email-template.php';
            $emailTemplate = new EmailTemplate();
            $welcomeEmail = $emailTemplate->welcomeEmail($user['full_name']);
            sendEmail($user['email'], 'Welcome to ' . getSiteName(), $welcomeEmail);
        } catch (Exception $e) {
            error_log("Welcome email after verification error: " . $e->getMessage());
        }

        establishUserSession($user);
        $_SESSION['security_onboarding'] = true;
        redirect('/auth/verification-success');
    }
    
    public function jointAccountConfirmation() {
        include __DIR__ . '/../views/auth/joint-account-confirmation.php';
    }
    
    public function forgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = Security::sanitize($_POST['email']);
            
            $userModel = new User();
            if ($userModel->createPasswordResetToken($email)) {
                $_SESSION['success'] = 'Password reset link sent to your email.';
            } else {
                $_SESSION['error'] = 'Email not found.';
            }
            
            redirect('/auth/forgot-password');
        }
        
        include __DIR__ . '/../views/auth/forgot-password.php';
    }
    
    public function resetPassword($token = null) {
        if (!$token) {
            redirect('/auth/forgot-password');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirm_password'];
            
            if (!Security::validatePassword($password)) {
                $_SESSION['error'] = 'Password must be at least 8 characters with uppercase, lowercase, and number';
                redirect('/auth/reset-password/' . $token);
            }
            
            if ($password !== $confirmPassword) {
                $_SESSION['error'] = 'Passwords do not match';
                redirect('/auth/reset-password/' . $token);
            }
            
            $userModel = new User();
            if ($userModel->resetPassword($token, $password)) {
                $_SESSION['success'] = 'Password reset successful! You can now login.';
                redirect('/auth/login');
            } else {
                $_SESSION['error'] = 'Invalid or expired reset link.';
                redirect('/auth/forgot-password');
            }
        }
        
        include __DIR__ . '/../views/auth/reset-password.php';
    }
    
    public function logout() {
        if (isLoggedIn()) {
            logActivity($_SESSION['user_id'], 'LOGOUT', 'User logged out');
        }
        
        session_unset();
        session_destroy();
        redirect('/'); // Redirect to homepage instead of login
    }
    
    private function createSession($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_role'] = $user['role'];
        
        // Mark session domain for domain migration detection
        $_SESSION['session_domain'] = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
        
        // Check if profile picture exists before setting it
        $profilePicture = $user['profile_picture'] ?? null;
        if ($profilePicture && file_exists(BASE_PATH . $profilePicture)) {
            $_SESSION['user_photo'] = $profilePicture;
        } else {
            $_SESSION['user_photo'] = null;
        }
        
        // Update last login
        $userModel = new User();
        $userModel->updateLastLogin($user['id']);
        
        // Log activity
        logActivity($user['id'], 'LOGIN', 'User logged in');
        
        // Login alert emails disabled (per admin request)
    }
    
    private function sendLoginAlert($user) {
        // Check system setting first - if enabled, send to all users
        require_once __DIR__ . '/../includes/system-settings.php';
        $systemSettings = SystemSettings::getInstance();
        $systemEmailOnLogin = $systemSettings->get('email_on_login', '0') === '1';
        
        // Check if user has login alerts enabled (only if system setting is disabled)
        $userEmailLoginAlert = false;
        if (!$systemEmailOnLogin) {
            $preferences = [];
            if (!empty($user['notification_preferences'])) {
                $preferences = json_decode($user['notification_preferences'], true) ?? [];
            }
            $userEmailLoginAlert = $preferences['email_login_alert'] ?? false;
        }
        
        // Send email if system setting OR user preference is enabled
        if ($systemEmailOnLogin || $userEmailLoginAlert) {
            // Get login details
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
            $loginTime = date('F j, Y, g:i a');
            
            // Detect device/browser
            $device = 'Unknown Device';
            if (preg_match('/mobile/i', $userAgent)) {
                $device = 'Mobile Device';
            } elseif (preg_match('/tablet/i', $userAgent)) {
                $device = 'Tablet';
            } else {
                $device = 'Desktop Computer';
            }
            
            $browser = 'Unknown Browser';
            if (preg_match('/Firefox/i', $userAgent)) {
                $browser = 'Firefox';
            } elseif (preg_match('/Chrome/i', $userAgent)) {
                $browser = 'Chrome';
            } elseif (preg_match('/Safari/i', $userAgent)) {
                $browser = 'Safari';
            } elseif (preg_match('/Edge/i', $userAgent)) {
                $browser = 'Edge';
            }
            
            // Prepare email body
            $emailBody = "
                <h2>New Login to Your Account</h2>
                <p>Hello {$user['full_name']},</p>
                <p>We detected a new login to your account. If this was you, you can safely ignore this email.</p>
                
                <div style='background: #f3f4f6; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                    <h3 style='margin-top: 0;'>Login Details:</h3>
                    <p><strong>Time:</strong> {$loginTime}</p>
                    <p><strong>IP Address:</strong> {$ipAddress}</p>
                    <p><strong>Device:</strong> {$device}</p>
                    <p><strong>Browser:</strong> {$browser}</p>
                </div>
                
                <p><strong style='color: #dc2626;'>If this wasn't you, please:</strong></p>
                <ul>
                    <li>Change your password immediately</li>
                    <li>Contact our support team</li>
                    <li>Review your recent account activity</li>
                </ul>
                
                <p>Stay safe!</p>
            ";
            
            // Send email (using the email template function if it exists)
            try {
                sendEmail(
                    $user['email'],
                    'New Login Alert - ' . getSiteName(),
                    $emailBody
                );
            } catch (Exception $e) {
                // Log error but don't stop login process
                error_log("Login alert email error: " . $e->getMessage());
            }
        }
    }
}
