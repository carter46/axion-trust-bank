<?php
class ProfileController {
    
    public function index() {
        requireLogin();
        
        $userModel = new User();
        $user = $userModel->findById($_SESSION['user_id']);
        
        include 'views/profile/index.php';
    }
    
    public function update() {
        requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'full_name' => Security::sanitize($_POST['full_name']),
                'phone' => Security::sanitize($_POST['phone']),
                'address' => Security::sanitize($_POST['address']),
                'city' => Security::sanitize($_POST['city']),
                'state' => Security::sanitize($_POST['state']),
                'country' => Security::sanitize($_POST['country']),
                'postal_code' => Security::sanitize($_POST['postal_code'])
            ];
            
            // Handle profile picture upload
            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                $upload = uploadFile($_FILES['profile_picture'], 'profiles');
                if ($upload['success']) {
                    $data['profile_picture'] = $upload['path'];
                }
            }
            
            $userModel = new User();
            if ($userModel->update($_SESSION['user_id'], $data)) {
                $_SESSION['success'] = 'Profile updated successfully';
            } else {
                $_SESSION['error'] = 'Failed to update profile';
            }
            
            redirect('/profile');
        }
    }
    
    public function security() {
        requireLogin();
        
        $userModel = new User();
        $user = $userModel->findById($_SESSION['user_id']);
        
        include 'views/profile/security.php';
    }
    
    public function changePassword() {
        requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $currentPassword = $_POST['current_password'];
            $newPassword = $_POST['new_password'];
            $confirmPassword = $_POST['confirm_password'];
            
            $userModel = new User();
            $user = $userModel->findById($_SESSION['user_id']);
            
            if (!Security::verifyPassword($currentPassword, $user['password_hash'])) {
                $_SESSION['error'] = 'Current password is incorrect';
                redirect('/profile/security');
            }
            
            if (!Security::validatePassword($newPassword)) {
                $_SESSION['error'] = 'Password must be at least 8 characters with uppercase, lowercase, and number';
                redirect('/profile/security');
            }
            
            if ($newPassword !== $confirmPassword) {
                $_SESSION['error'] = 'Passwords do not match';
                redirect('/profile/security');
            }
            
            if ($userModel->updatePassword($_SESSION['user_id'], $newPassword)) {
                $_SESSION['success'] = 'Password changed successfully';
                logActivity($_SESSION['user_id'], 'PASSWORD_CHANGED', 'User changed password');
                
                // Send password changed email notification (user self-service action)
                try {
                    require_once __DIR__ . '/../includes/email-template.php';
                    require_once __DIR__ . '/../includes/system-settings.php';
                    $systemSettings = SystemSettings::getInstance();
                    $siteName = $systemSettings->get('site_name', 'SecureBank Online');
                    
                    $emailTemplate = new EmailTemplate();
                    $changedEmail = $emailTemplate->passwordChangedEmail($user['full_name']);
                    sendEmail($user['email'], 'Password Changed - ' . $siteName, $changedEmail);
                } catch (Exception $e) {
                    error_log("Password change email error: " . $e->getMessage());
                }
            } else {
                $_SESSION['error'] = 'Failed to change password';
            }
            
            redirect('/profile/security');
        }
    }
    
    public function toggle2fa() {
        requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'];
            $method = Security::sanitize($_POST['method'] ?? 'email');
            
            $userModel = new User();
            
            if ($action === 'enable') {
                if ($userModel->enable2FA($_SESSION['user_id'], $method)) {
                    $_SESSION['success'] = '2FA enabled successfully';
                    logActivity($_SESSION['user_id'], '2FA_ENABLED', "2FA enabled via $method");
                }
            } else {
                if ($userModel->disable2FA($_SESSION['user_id'])) {
                    $_SESSION['success'] = '2FA disabled successfully';
                    logActivity($_SESSION['user_id'], '2FA_DISABLED', '2FA disabled');
                }
            }
            
            redirect('/profile/security');
        }
    }
    
    public function notifications() {
        requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $preferences = [
                'email_notifications' => isset($_POST['email_notifications']),
                'sms_notifications' => isset($_POST['sms_notifications']),
                'transaction_alerts' => isset($_POST['transaction_alerts']),
                'login_alerts' => isset($_POST['login_alerts']),
                'marketing_emails' => isset($_POST['marketing_emails'])
            ];
            
            $userModel = new User();
            $userModel->update($_SESSION['user_id'], [
                'notification_preferences' => json_encode($preferences)
            ]);
            
            $_SESSION['success'] = 'Notification preferences updated';
            redirect('/profile/notifications');
        }
        
        $userModel = new User();
        $user = $userModel->findById($_SESSION['user_id']);
        $preferences = json_decode($user['notification_preferences'] ?? '{}', true);
        
        $data = ['preferences' => $preferences];
        
        include 'views/profile/notifications.php';
    }
    
    public function edit() {
        requireLogin();
        
        $userModel = new User();
        $user = $userModel->findById($_SESSION['user_id']);
        
        include 'views/profile/edit.php';
    }
    
    public function twoFactor() {
        requireLogin();
        
        $userModel = new User();
        $user = $userModel->findById($_SESSION['user_id']);
        
        include 'views/profile/two-factor.php';
    }
    
    public function kyc() {
        requireLogin();
        require_once __DIR__ . '/../includes/kyc-config.php';
        
        $userModel = new User();
        $user = $userModel->findById($_SESSION['user_id']);
        
        if (!$user) {
            $_SESSION['error'] = 'User not found';
            redirect('/profile');
        }
        
        $kycModel = new Kyc();
        $existingKyc = $kycModel->findByUserId($_SESSION['user_id']);
        
        // Ensure $existingKyc is an array, not null or false
        if (!is_array($existingKyc)) {
            $existingKyc = [];
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $postData = array_merge($_POST, ['user_id' => $_SESSION['user_id']]);
                $validation = validateKycSubmission($postData, $_FILES, $existingKyc ?: null, $_SESSION['user_id']);
                
                if (!$validation['valid']) {
                    $_SESSION['error'] = implode(' ', $validation['errors']);
                    redirect('/profile/kyc');
                }
                
                $data = buildKycSubmissionData($_POST, $_FILES, $existingKyc ?: null, $_SESSION['user_id']);
                
                // Handle business information if applicable
                if (($data['account_type'] ?? 'individual') === 'business') {
                    $data['business_name'] = Security::sanitize($_POST['business_name'] ?? '');
                    $data['business_address'] = Security::sanitize($_POST['business_address'] ?? '');
                    $data['business_city'] = Security::sanitize($_POST['business_city'] ?? '');
                    $data['business_state'] = Security::sanitize($_POST['business_state'] ?? '');
                    $data['business_country'] = Security::sanitize($_POST['business_country'] ?? 'United States');
                    $data['business_zip'] = Security::sanitize($_POST['business_zip'] ?? '');
                    if (!empty($_POST['ein'])) {
                        $data['ein'] = encryptData(Security::sanitize($_POST['ein']));
                    } elseif (is_array($existingKyc) && !empty($existingKyc['ein'])) {
                        $data['ein'] = $existingKyc['ein'];
                    } else {
                        $data['ein'] = null;
                    }
                    
                    if (isset($_FILES['business_formation_doc']) && $_FILES['business_formation_doc']['error'] === UPLOAD_ERR_OK) {
                        $upload = uploadFile($_FILES['business_formation_doc'], 'kyc');
                        if ($upload['success']) {
                            $data['business_formation_doc'] = $upload['path'];
                        }
                    } elseif (is_array($existingKyc) && !empty($existingKyc['business_formation_doc'])) {
                        $data['business_formation_doc'] = $existingKyc['business_formation_doc'];
                    }
                }
                
                // Create or update KYC
                if ($existingKyc) {
                    $result = $kycModel->update($existingKyc['id'], $data);
                    if ($result) {
                        $_SESSION['success'] = 'KYC information updated successfully.';
                        logActivity($_SESSION['user_id'], 'KYC_RESUBMITTED', 'User resubmitted KYC verification');
                    } else {
                        $_SESSION['error'] = 'Failed to update KYC information.';
                    }
                } else {
                    $result = $kycModel->create($data);
                    if ($result['success']) {
                        $_SESSION['success'] = 'KYC verification submitted successfully. Your application is under review.';
                        logActivity($_SESSION['user_id'], 'KYC_SUBMITTED', 'User submitted KYC verification');
                    } else {
                        $_SESSION['error'] = 'Failed to submit KYC verification. Please try again.';
                    }
                }
                
            } catch (Exception $e) {
                error_log("KYC Submission Error: " . $e->getMessage());
                $_SESSION['error'] = 'An error occurred. Please try again.';
            }
            
            redirect('/profile/kyc');
            return;
        }
        
        // Reload existing KYC after potential submission
        $existingKyc = $kycModel->findByUserId($_SESSION['user_id']);
        if (!is_array($existingKyc)) {
            $existingKyc = [];
        }
        
        // Get beneficial owners if exists
        $beneficialOwners = [];
        if (!empty($existingKyc) && $existingKyc['account_type'] === 'business') {
            $beneficialOwners = $kycModel->getBeneficialOwners($existingKyc['id']);
        }
        
        include 'views/profile/kyc.php';
    }
    
    public function settings() {
        requireLogin();
        
        $userModel = new User();
        $user = $userModel->findById($_SESSION['user_id']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle settings update
            $data = [];
            
            if (isset($_POST['language'])) {
                $data['language'] = Security::sanitize($_POST['language']);
            }
            
            if (isset($_POST['currency'])) {
                $data['currency'] = Security::sanitize($_POST['currency']);
                // Mark as an explicit user choice so getUserDisplayCurrency() applies it.
                $data['currency_selection_shown'] = 1;
            }
            
            // Get existing metadata and update preferences
            $currentMetadata = json_decode($user['metadata'] ?? '{}', true);
            if (!is_array($currentMetadata)) {
                $currentMetadata = [];
            }
            
            $metadataUpdated = false;
            
            if (isset($_POST['timezone'])) {
                $currentMetadata['timezone'] = Security::sanitize($_POST['timezone']);
                $metadataUpdated = true;
            }
            
            
            // Update metadata if any preference changed
            if ($metadataUpdated) {
                $data['metadata'] = json_encode($currentMetadata);
            }
            
            if (!empty($data)) {
                if ($userModel->update($_SESSION['user_id'], $data)) {
                    $_SESSION['success'] = 'Settings updated successfully';
                } else {
                    $_SESSION['error'] = 'Failed to update settings';
                }
            }
            
            redirect('/profile/settings');
        }
        
        include 'views/profile/settings.php';
    }
}
