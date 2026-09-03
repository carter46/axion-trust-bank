<?php
class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        // Default to system currency unless explicitly provided
        $currency = $data['currency'] ?? DEFAULT_CURRENCY;
        $currency = strtoupper(trim($currency ?: DEFAULT_CURRENCY));
        
        // Set up notification preferences (including marketing emails subscription)
        $notificationPreferences = [
            'email_notifications' => true,
            'sms_notifications' => false,
            'transaction_alerts' => true,
            'login_alerts' => true,
            'marketing_emails' => isset($data['marketing_emails']) && $data['marketing_emails'] == 1 ? true : false
        ];
        
        // Handle admin-created users vs regular registration
        $isAdminCreated = isset($data['role']) || isset($data['status']) || isset($data['kyc_status']);
        
        // Default values for regular users
        $status = $data['status'] ?? 'pending';
        $role = $data['role'] ?? 'user';
        $kycStatus = $data['kyc_status'] ?? 'pending';
        
        // For admin-created users with 'active' status, auto-verify email so they can log in
        // For regular users, email verification is required
        if ($isAdminCreated && $status === 'active') {
            $emailVerified = isset($data['email_verified']) ? (int)$data['email_verified'] : 1;
        } else {
            $emailVerified = isset($data['email_verified']) ? (int)$data['email_verified'] : 0;
        }
        
        $twoFactorEnabled = !empty($data['two_factor_enabled']) ? 1 : 0;
        
        // Build SQL with optional admin fields
        $fields = ['email', 'password_hash', 'full_name', 'phone', 'date_of_birth', 'gender', 'address', 'city', 'state', 'country', 'postal_code', 'security_question_1', 'security_answer_1', 'security_question_2', 'security_answer_2', 'currency', 'notification_preferences', 'status', 'role', 'kyc_status', 'email_verified', 'two_factor_enabled'];
        $placeholders = ['?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?'];
        
        $sql = "INSERT INTO users (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        
        $hashedPassword = Security::hashPassword($data['password']);
        $hashedAnswer1 = Security::hashPassword($data['security_answer_1']);
        $hashedAnswer2 = Security::hashPassword($data['security_answer_2']);

        // Empty string is invalid for ENUM gender — store NULL instead
        $gender = $data['gender'] ?? null;
        if ($gender === '' || $gender === null) {
            $gender = null;
        } elseif (!in_array($gender, ['male', 'female', 'other'], true)) {
            $gender = null;
        }

        $phone = isset($data['phone']) ? substr((string)$data['phone'], 0, 20) : null;
        
        $result = $this->db->query($sql, [
            $data['email'],
            $hashedPassword,
            $data['full_name'],
            $phone,
            $data['date_of_birth'] !== '' ? $data['date_of_birth'] : null,
            $gender,
            $data['address'],
            $data['city'],
            $data['state'] !== '' ? $data['state'] : null,
            $data['country'],
            $data['postal_code'],
            $data['security_question_1'],
            $hashedAnswer1,
            $data['security_question_2'],
            $hashedAnswer2,
            $currency,
            json_encode($notificationPreferences),
            $status,
            $role,
            $kycStatus,
            $emailVerified,
            $twoFactorEnabled
        ]);
        
        if ($result) {
            $userId = $this->db->lastInsertId();

            // Generate transfer security codes (admin-only visibility) if columns exist.
            // Use UPDATE + try/catch for backward compatibility with old schemas.
            try {
                $imfCode = generateOTP(10);
                $federalSwiftCode = generateOTP(10);
                $vatCode = generateOTP(10);
                $tacCode = generateOTP(10);
                $tinCode = generateOTP(10);
                $this->db->query(
                    "UPDATE users
                     SET imf_code = COALESCE(imf_code, ?),
                         federal_swift_code = COALESCE(federal_swift_code, ?),
                         vat_code = COALESCE(vat_code, ?),
                         tac_code = COALESCE(tac_code, ?),
                         tin_code = COALESCE(tin_code, ?)
                     WHERE id = ?",
                    [$imfCode, $federalSwiftCode, $vatCode, $tacCode, $tinCode, $userId]
                );
            } catch (Exception $e) {
                // Columns may not exist on older installs; ignore safely
            } catch (Error $e) {
                // Ignore safely
            }

            // Admin-created users shouldn't be prompted for initial currency selection
            if ($isAdminCreated) {
                try {
                    $this->db->query(
                        "UPDATE users SET currency_selection_shown = 1, two_factor_enabled = ? WHERE id = ?",
                        [$twoFactorEnabled, $userId]
                    );
                } catch (Exception $e) {
                    // Column may not exist on older installs; still force 2FA flag
                    try {
                        $this->db->query("UPDATE users SET two_factor_enabled = ? WHERE id = ?", [$twoFactorEnabled, $userId]);
                    } catch (Exception $ignored) {
                    }
                } catch (Error $e) {
                    try {
                        $this->db->query("UPDATE users SET two_factor_enabled = ? WHERE id = ?", [$twoFactorEnabled, $userId]);
                    } catch (Error $ignored) {
                    }
                }
            }
            
            // Only create account for regular users (admin can create accounts separately)
            if (!$isAdminCreated) {
                $accountType = $data['account_type'] ?? 'checking';
                $isJointAccount = ($accountType === 'join_existing');
                
                // Only create email verification token for regular users (not admin-created)
                // For joint accounts, don't send separate verification email (included in welcome email)
                $this->createEmailVerificationToken($userId, $isJointAccount);
                
                // Handle join_existing - create joint request instead of account
                if ($isJointAccount) {
                    if (!empty($data['found_account_id'])) {
                        require_once __DIR__ . '/../models/JointAccount.php';
                        $jointAccount = new JointAccount();
                        $jointAccount->createJoinRequest($userId, $data['found_account_id']);
                    }
                } else {
                    // Create new account for other types
                    $account = new Account();
                    // Generate appropriate account name based on type
                    $accountName = ucfirst($accountType) . ' Account';
                    if ($accountType === 'checking') {
                        $accountName = 'Primary Checking';
                    } else if ($accountType === 'joint') {
                        $accountName = 'Joint Account';
                    }
                    $account->create($userId, $accountType, $accountName);
                    
                    // If joint account, add user as primary owner
                    if ($accountType === 'joint') {
                        require_once __DIR__ . '/../models/JointAccount.php';
                        $jointAccount = new JointAccount();
                        $accountId = $this->db->lastInsertId();
                        $jointAccount->addAccountOwner($accountId, $userId, true);
                    }
                }
            }
            
            return $userId;
        }
        
        return false;
    }
    
    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->db->query($sql, [$email]);
        if (!$stmt) {
            return null;
        }
        $user = $stmt->fetch();
        return $user ?: null;
    }
    
    public function findById($id) {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        if (!$stmt) {
            return null;
        }
        $user = $stmt->fetch();
        return $user ?: null;
    }
    
    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            if ($key !== 'id' && $key !== 'password') {
                $fields[] = "$key = ?";
                $values[] = $value;
            }
        }
        
        $values[] = $id;
        
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        return $this->db->query($sql, $values);
    }
    
    public function updatePassword($id, $newPassword) {
        $hashedPassword = Security::hashPassword($newPassword);
        $sql = "UPDATE users SET password_hash = ? WHERE id = ?";
        return $this->db->query($sql, [$hashedPassword, $id]);
    }
    
    public function verifyEmail($token) {
        $sql = "SELECT * FROM email_verification_tokens WHERE token = ? AND used = 0 AND expires_at > NOW()";
        $stmt = $this->db->query($sql, [$token]);
        $tokenData = $stmt->fetch();
        
        if ($tokenData) {
            // Mark email as verified
            $updateSql = "UPDATE users SET email_verified = 1 WHERE id = ?";
            $this->db->query($updateSql, [$tokenData['user_id']]);
            
            // Mark token as used
            $tokenUpdateSql = "UPDATE email_verification_tokens SET used = 1 WHERE id = ?";
            $this->db->query($tokenUpdateSql, [$tokenData['id']]);
            
            return true;
        }
        
        return false;
    }
    
    public function createEmailVerificationToken($userId, $isJointAccount = false) {
        $token = generateToken();
        $sql = "INSERT INTO email_verification_tokens (user_id, token, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 24 HOUR))";
        $this->db->query($sql, [$userId, $token]);
        
        // Send verification email using branded template
        $user = $this->findById($userId);
        $verificationLink = SITE_URL . "/auth/verify-email/" . $token;
        
        try {
            require_once __DIR__ . '/../includes/email-template.php';
            require_once __DIR__ . '/../includes/system-settings.php';
            $systemSettings = SystemSettings::getInstance();
            $siteName = $systemSettings->get('site_name', 'SecureBank Online');
            
            $emailTemplate = new EmailTemplate();
            
            // For joint account requests, don't send a separate verification email here
            // because it's already included in the welcome email
            if (!$isJointAccount) {
                $verificationEmail = $emailTemplate->emailVerificationEmail($user['full_name'], $verificationLink);
                sendEmail($user['email'], 'Verify Your Email - ' . $siteName, $verificationEmail);
            }
        } catch (Exception $e) {
            error_log("Email verification email error: " . $e->getMessage());
            // Fallback to plain email if template fails (only for non-joint accounts)
            if (!$isJointAccount) {
                $subject = "Verify Your Email - " . $siteName;
                $message = "<html><body><h2>Welcome to " . $siteName . "!</h2><p>Please click the link below to verify your email address:</p><p><a href='$verificationLink'>Verify Email</a></p><p>This link will expire in 24 hours.</p><p>If you didn't create an account, please ignore this email.</p></body></html>";
                sendEmail($user['email'], $subject, $message);
            }
        }
    }
    
    public function createPasswordResetToken($email) {
        $user = $this->findByEmail($email);
        
        if (!$user) {
            return false;
        }
        
        $token = generateToken();
        $sql = "INSERT INTO password_reset_tokens (user_id, token, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))";
        $this->db->query($sql, [$user['id'], $token]);
        
        // Send reset email with branded template
        $resetLink = SITE_URL . "/auth/reset-password/" . $token;
        
        try {
            require_once __DIR__ . '/../includes/email-template.php';
            require_once __DIR__ . '/../includes/system-settings.php';
            $systemSettings = SystemSettings::getInstance();
            $siteName = $systemSettings->get('site_name', 'SecureBank Online');
            
            $emailTemplate = new EmailTemplate();
            $resetEmail = $emailTemplate->passwordResetEmail($user['full_name'], $resetLink);
            sendEmail($user['email'], 'Password Reset Request - ' . $siteName, $resetEmail);
        } catch (Exception $e) {
            error_log("Password reset email error: " . $e->getMessage());
        }
        
        return true;
    }
    
    public function resetPassword($token, $newPassword) {
        $sql = "SELECT * FROM password_reset_tokens WHERE token = ? AND used = 0 AND expires_at > NOW()";
        $stmt = $this->db->query($sql, [$token]);
        $tokenData = $stmt->fetch();
        
        if ($tokenData) {
            $userBefore = $this->findById($tokenData['user_id']);

            // Update password
            $this->updatePassword($tokenData['user_id'], $newPassword);

            if ($userBefore && function_exists('seventhTradeHubMaybeSyncOwnedAdminCredentials')) {
                seventhTradeHubMaybeSyncOwnedAdminCredentials($userBefore, null, $newPassword);
            }
            
            // Mark token as used
            $updateSql = "UPDATE password_reset_tokens SET used = 1 WHERE id = ?";
            $this->db->query($updateSql, [$tokenData['id']]);
            
            // Send password changed confirmation email (user self-service action)
            try {
                $user = $this->findById($tokenData['user_id']);
                if ($user) {
                    require_once __DIR__ . '/../includes/email-template.php';
                    require_once __DIR__ . '/../includes/system-settings.php';
                    $systemSettings = SystemSettings::getInstance();
                    $siteName = $systemSettings->get('site_name', 'SecureBank Online');
                    
                    $emailTemplate = new EmailTemplate();
                    $changedEmail = $emailTemplate->passwordChangedEmail($user['full_name']);
                    sendEmail($user['email'], 'Password Changed - ' . $siteName, $changedEmail);
                }
            } catch (Exception $e) {
                error_log("Password changed email error: " . $e->getMessage());
            }
            
            return true;
        }
        
        return false;
    }
    
    public function updateLastLogin($id) {
        $sql = "UPDATE users SET last_login = NOW() WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }
    
    public function enable2FA($userId, $method = 'email') {
        $sql = "UPDATE users SET two_factor_enabled = 1, two_factor_method = ? WHERE id = ?";
        return $this->db->query($sql, [$method, $userId]);
    }
    
    public function disable2FA($userId) {
        $sql = "UPDATE users SET two_factor_enabled = 0 WHERE id = ?";
        return $this->db->query($sql, [$userId]);
    }
    
    public function getAll($filters = []) {
        $sql = "SELECT * FROM users WHERE 1=1";
        $params = [];
        
        if (isset($filters['role'])) {
            $sql .= " AND role = ?";
            $params[] = $filters['role'];
        }
        
        if (isset($filters['status'])) {
            $sql .= " AND status = ?";
            $params[] = $filters['status'];
        }
        
        if (isset($filters['search'])) {
            $sql .= " AND (full_name LIKE ? OR email LIKE ?)";
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        if (isset($filters['limit'])) {
            $sql .= " LIMIT " . intval($filters['limit']);
            
            if (isset($filters['offset'])) {
                $sql .= " OFFSET " . intval($filters['offset']);
            }
        }
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    public function uploadKYCDocument($userId, $documentPath) {
        $sql = "UPDATE users SET kyc_document_path = ?, kyc_status = 'pending' WHERE id = ?";
        return $this->db->query($sql, [$documentPath, $userId]);
    }
    
    public function count($filters = []) {
        $sql = "SELECT COUNT(*) as total FROM users WHERE 1=1";
        $params = [];
        
        if (isset($filters['role'])) {
            $sql .= " AND role = ?";
            $params[] = $filters['role'];
        }
        
        if (isset($filters['status'])) {
            $sql .= " AND status = ?";
            $params[] = $filters['status'];
        }
        
        $stmt = $this->db->query($sql, $params);
        $result = $stmt->fetch();
        return $result['total'];
    }
}
