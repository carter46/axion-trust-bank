<?php
class JointAccount {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Search account by account number and return primary owner info
     */
    public function searchAccount($accountNumber) {
        $sql = "SELECT a.id, a.account_type, a.account_number, u.id as owner_id, u.full_name as owner_name, u.email as owner_email
                FROM accounts a
                INNER JOIN users u ON a.user_id = u.id
                WHERE a.account_number = ? AND a.status = 'active'";
        $stmt = $this->db->query($sql, [$accountNumber]);
        $account = $stmt->fetch();
        
        if (!$account) {
            return false;
        }
        
        // Allow any active account type (checking, savings, business) to accept joint owners
        // The account_type field should remain as-is (checking, savings, business)
        // Joint access is managed via account_owners table, not account_type
        // Only prevent joining accounts that are 'join_existing' type (which shouldn't exist in normal flow)
        if ($account['account_type'] === 'join_existing') {
            return false; // Can't join a join_existing account (this is a registration flow type, not a real account type)
        }
        
        return [
            'account_id' => $account['id'],
            'account_number' => $account['account_number'],
            'account_type' => ucfirst($account['account_type']),
            'owner_name' => $account['owner_name'],
            'owner_email' => $account['owner_email'],
            'owner_id' => $account['owner_id']
        ];
    }
    
    /**
     * Create a joint account request
     */
    public function createJoinRequest($requestingUserId, $accountId) {
        // Get account and primary owner
        $account = $this->db->query("SELECT * FROM accounts WHERE id = ?", [$accountId])->fetch();
        if (!$account) {
            return false;
        }
        
        $primaryOwnerId = $account['user_id'];
        
        // Check if user is already an owner
        $existingOwner = $this->db->query(
            "SELECT * FROM account_owners WHERE account_id = ? AND user_id = ?",
            [$accountId, $requestingUserId]
        )->fetch();
        
        if ($existingOwner) {
            return false; // Already an owner
        }
        
        // Check if there's already a pending request
        $existingRequest = $this->db->query(
            "SELECT * FROM joint_account_requests 
             WHERE account_id = ? AND requesting_user_id = ? AND status = 'pending'",
            [$accountId, $requestingUserId]
        )->fetch();
        
        if ($existingRequest) {
            return false; // Request already exists
        }
        
        // Create request
        $expiresAt = date('Y-m-d H:i:s', strtotime('+7 days'));
        $sql = "INSERT INTO joint_account_requests (account_id, primary_owner_id, requesting_user_id, status, expires_at)
                VALUES (?, ?, ?, 'pending', ?)";
        $result = $this->db->query($sql, [$accountId, $primaryOwnerId, $requestingUserId, $expiresAt]);
        
        if ($result) {
            $requestId = $this->db->lastInsertId();
            
            // Send email to primary owner
            $this->sendJointRequestEmail($requestId, $accountId, $primaryOwnerId, $requestingUserId);
            
            // Create notification for primary owner
            $this->createJointRequestNotification($primaryOwnerId, $requestId, $accountId, $requestingUserId);
            
            // Note: Confirmation email to requesting user will be sent after email verification
            // This is handled in AuthController::verifyEmail()
            
            return $requestId;
        }
        
        return false;
    }
    
    /**
     * Get pending requests for a primary owner with full user details
     */
    /**
     * Get pending requests for a primary owner with full user details
     */
    public function getPendingRequests($primaryOwnerId) {
        $sql = "SELECT jr.*, 
                       a.account_number, 
                       a.account_type, 
                       u.id as requesting_user_id,
                       u.full_name as requesting_user_name, 
                       u.email as requesting_user_email,
                       u.phone as requesting_user_phone,
                       u.date_of_birth as requesting_user_dob,
                       u.address as requesting_user_address,
                       u.city as requesting_user_city,
                       u.state as requesting_user_state,
                       u.country as requesting_user_country,
                       u.postal_code as requesting_user_postal_code,
                       COALESCE(jr.requested_at, jr.created_at, NOW()) as requested_at
                FROM joint_account_requests jr
                INNER JOIN accounts a ON jr.account_id = a.id
                INNER JOIN users u ON jr.requesting_user_id = u.id
                WHERE jr.primary_owner_id = ? AND jr.status = 'pending' AND jr.expires_at > NOW()
                ORDER BY COALESCE(jr.requested_at, jr.created_at, NOW()) DESC";
        $stmt = $this->db->query($sql, [$primaryOwnerId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Check if user has pending joint account request
     */
    public function hasPendingRequest($userId) {
        $sql = "SELECT COUNT(*) as count 
                FROM joint_account_requests 
                WHERE requesting_user_id = ? AND status = 'pending' AND expires_at > NOW()";
        $stmt = $this->db->query($sql, [$userId]);
        $result = $stmt->fetch();
        return ($result && $result['count'] > 0);
    }
    
    /**
     * Check if user joined via joint account (is a secondary owner, not primary)
     */
    public function isJointAccountUser($userId) {
        // Check if user has any accounts where they are NOT the primary owner (user_id != their id)
        // but they ARE in account_owners table (secondary owner)
        $sql = "SELECT COUNT(*) as count
                FROM account_owners ao
                INNER JOIN accounts a ON ao.account_id = a.id
                WHERE ao.user_id = ? 
                AND ao.status = 'active'
                AND ao.is_primary = 0
                AND a.user_id != ?";
        $stmt = $this->db->query($sql, [$userId, $userId]);
        $result = $stmt->fetch();
        return ($result && $result['count'] > 0);
    }
    
    /**
     * Get accounts user OWNS (primary owner only, not joint access)
     */
    public function getUserOwnedAccounts($userId) {
        $sql = "SELECT * FROM accounts 
                WHERE user_id = ? AND status != 'closed'
                ORDER BY created_at ASC";
        $stmt = $this->db->query($sql, [$userId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Sync joint account access - ensure user has access to all accounts of primary owner
     * This fixes cases where approval happened before the "all accounts" fix
     */
    public function syncJointAccountAccess($requestingUserId, $primaryOwnerId) {
        // Get all active accounts of primary owner
        $allAccounts = $this->db->query(
            "SELECT id FROM accounts WHERE user_id = ? AND status = 'active'",
            [$primaryOwnerId]
        )->fetchAll();
        
        // Add user as owner to all accounts (if not already added)
        foreach ($allAccounts as $account) {
            $this->addAccountOwner($account['id'], $requestingUserId, false);
        }
        
        return true;
    }
    
    /**
     * Approve joint account request
     */
    public function approveRequest($requestId, $primaryOwnerId) {
        // Verify request belongs to primary owner
        $request = $this->db->query(
            "SELECT * FROM joint_account_requests WHERE id = ? AND primary_owner_id = ? AND status = 'pending'",
            [$requestId, $primaryOwnerId]
        )->fetch();
        
        if (!$request) {
            return false;
        }
        
        // Start transaction
        $this->db->beginTransaction();
        
        try {
            // Update request status
            $this->db->query(
                "UPDATE joint_account_requests SET status = 'approved', responded_at = NOW() WHERE id = ?",
                [$requestId]
            );
            
            // Get primary owner's user ID
            $primaryOwnerId = $request['primary_owner_id'];
            
            // Grant access to ALL accounts owned by the primary owner (not just the one in the request)
            // This ensures the new user has access to all accounts (checking, savings, business, etc.)
            $allAccounts = $this->db->query(
                "SELECT id FROM accounts WHERE user_id = ? AND status = 'active'",
                [$primaryOwnerId]
            )->fetchAll();
            
            // Add user as owner to all primary owner's accounts
            foreach ($allAccounts as $account) {
                $this->addAccountOwner($account['id'], $request['requesting_user_id'], false);
            }
            
            // DO NOT change account_type to 'joint' - accounts keep their original type (checking, savings, business)
            // Joint access is managed via account_owners table, not account_type field
            
            // Mark user as approved (they can now login)
            // Update user status if needed - but keep as pending/active based on KYC
            // The user will be able to login now
            
            // Send approval email (use the first account for email context)
            $firstAccountId = !empty($allAccounts) ? $allAccounts[0]['id'] : $request['account_id'];
            $this->sendJointApprovalEmail($firstAccountId, $request['requesting_user_id']);
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Joint account approval error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Reject joint account request
     */
    public function rejectRequest($requestId, $primaryOwnerId) {
        $request = $this->db->query(
            "SELECT * FROM joint_account_requests WHERE id = ? AND primary_owner_id = ? AND status = 'pending'",
            [$requestId, $primaryOwnerId]
        )->fetch();
        
        if (!$request) {
            return false;
        }
        
        $result = $this->db->query(
            "UPDATE joint_account_requests SET status = 'rejected', responded_at = NOW() WHERE id = ?",
            [$requestId]
        );
        
        if ($result) {
            // Send rejection email
            $this->sendJointRejectionEmail($request['account_id'], $request['requesting_user_id']);
        }
        
        return $result;
    }
    
    /**
     * Add account owner
     */
    public function addAccountOwner($accountId, $userId, $isPrimary = false) {
        // Check if already exists
        $existing = $this->db->query(
            "SELECT * FROM account_owners WHERE account_id = ? AND user_id = ?",
            [$accountId, $userId]
        )->fetch();
        
        if ($existing) {
            // Update status to active
            return $this->db->query(
                "UPDATE account_owners SET status = 'active', is_primary = ? WHERE id = ?",
                [$isPrimary ? 1 : 0, $existing['id']]
            );
        }
        
        $sql = "INSERT INTO account_owners (account_id, user_id, is_primary, status)
                VALUES (?, ?, ?, 'active')";
        return $this->db->query($sql, [$accountId, $userId, $isPrimary ? 1 : 0]);
    }
    
    /**
     * Get all owners for an account with full details
     */
    public function getAccountOwners($accountId) {
        $sql = "SELECT ao.*, 
                       u.full_name, 
                       u.email, 
                       u.phone,
                       u.last_login,
                       u.created_at as user_created_at,
                       u.profile_picture
                FROM account_owners ao
                INNER JOIN users u ON ao.user_id = u.id
                WHERE ao.account_id = ? AND ao.status = 'active'
                ORDER BY ao.is_primary DESC, ao.joined_at ASC";
        $stmt = $this->db->query($sql, [$accountId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get joint account request approval date for an account
     */
    public function getJointAccountApprovalDate($accountId, $userId) {
        // Get the earliest approval date for this user joining this account
        $sql = "SELECT MIN(responded_at) as approval_date
                FROM joint_account_requests
                WHERE account_id = ? 
                AND requesting_user_id = ? 
                AND status = 'approved'";
        $stmt = $this->db->query($sql, [$accountId, $userId]);
        $result = $stmt->fetch();
        return $result['approval_date'] ?? null;
    }
    
    /**
     * Get joint account relationship information for a user
     * Returns primary owner and secondary owner(s) with joint account details
     */
    public function getJointAccountRelationship($userId) {
        // Check if user is a joint account user (secondary owner)
        $isJointUser = $this->isJointAccountUser($userId);
        
        if ($isJointUser) {
            // User is secondary owner - get primary owner info
            $sql = "SELECT DISTINCT 
                           a.user_id as primary_owner_id,
                           pu.full_name as primary_owner_name,
                           pu.email as primary_owner_email,
                           pu.phone as primary_owner_phone,
                           pu.last_login as primary_owner_last_login,
                           pu.profile_picture as primary_owner_picture,
                           MIN(jr.responded_at) as joint_account_created_at
                    FROM account_owners ao
                    INNER JOIN accounts a ON ao.account_id = a.id
                    INNER JOIN users pu ON a.user_id = pu.id
                    LEFT JOIN joint_account_requests jr ON jr.requesting_user_id = ? 
                        AND jr.primary_owner_id = a.user_id 
                        AND jr.status = 'approved'
                    WHERE ao.user_id = ? 
                    AND ao.status = 'active'
                    AND ao.is_primary = 0
                    AND a.user_id != ?
                    GROUP BY a.user_id, pu.full_name, pu.email, pu.phone, pu.last_login, pu.profile_picture";
            $stmt = $this->db->query($sql, [$userId, $userId, $userId]);
            $primaryOwner = $stmt->fetch();
            
            if ($primaryOwner) {
                // Get secondary owner (current user) info
                $secondaryOwner = $this->db->query(
                    "SELECT id, full_name, email, phone, last_login, profile_picture 
                     FROM users WHERE id = ?",
                    [$userId]
                )->fetch();
                
                return [
                    'primary_owner' => $primaryOwner,
                    'secondary_owner' => $secondaryOwner,
                    'joint_account_created_at' => $primaryOwner['joint_account_created_at']
                ];
            }
        } else {
            // User is primary owner - get secondary owner(s) info
            $sql = "SELECT DISTINCT 
                           ao.user_id as secondary_owner_id,
                           su.full_name as secondary_owner_name,
                           su.email as secondary_owner_email,
                           su.phone as secondary_owner_phone,
                           su.last_login as secondary_owner_last_login,
                           su.profile_picture as secondary_owner_picture,
                           MIN(jr.responded_at) as joint_account_created_at
                    FROM account_owners ao
                    INNER JOIN accounts a ON ao.account_id = a.id
                    INNER JOIN users su ON ao.user_id = su.id
                    LEFT JOIN joint_account_requests jr ON jr.requesting_user_id = ao.user_id 
                        AND jr.primary_owner_id = ?
                        AND jr.status = 'approved'
                    WHERE a.user_id = ?
                    AND ao.status = 'active'
                    AND ao.is_primary = 0
                    AND ao.user_id != ?
                    GROUP BY ao.user_id, su.full_name, su.email, su.phone, su.last_login, su.profile_picture
                    ORDER BY joint_account_created_at ASC";
            $stmt = $this->db->query($sql, [$userId, $userId, $userId]);
            $secondaryOwners = $stmt->fetchAll();
            
            if (!empty($secondaryOwners)) {
                // Get primary owner (current user) info
                $primaryOwner = $this->db->query(
                    "SELECT id, full_name, email, phone, last_login, profile_picture 
                     FROM users WHERE id = ?",
                    [$userId]
                )->fetch();
                
                // Use the earliest joint account creation date
                $jointAccountCreatedAt = null;
                foreach ($secondaryOwners as $so) {
                    if ($so['joint_account_created_at'] && 
                        (!$jointAccountCreatedAt || strtotime($so['joint_account_created_at']) < strtotime($jointAccountCreatedAt))) {
                        $jointAccountCreatedAt = $so['joint_account_created_at'];
                    }
                }
                
                return [
                    'primary_owner' => $primaryOwner,
                    'secondary_owners' => $secondaryOwners,
                    'joint_account_created_at' => $jointAccountCreatedAt
                ];
            }
        }
        
        return null;
    }
    
    /**
     * Get accounts user has access to (own + joint)
     */
    public function getUserAccessibleAccounts($userId) {
        // Get accounts where user is primary owner OR has access via account_owners
        // Use GROUP BY to ensure each account appears only once (even if user matches both conditions)
        $sql = "SELECT a.*
                FROM accounts a
                LEFT JOIN account_owners ao ON a.id = ao.account_id AND ao.status = 'active'
                WHERE (a.user_id = ? OR ao.user_id = ?)
                AND a.status != 'closed'
                GROUP BY a.id
                ORDER BY a.created_at ASC";
        $stmt = $this->db->query($sql, [$userId, $userId]);
        $accounts = $stmt->fetchAll();
        
        // Fix legacy 'joint' account_type by inferring from account_name
        foreach ($accounts as &$account) {
            if ($account['account_type'] === 'joint') {
                $accountName = strtolower($account['account_name'] ?? '');
                if (stripos($accountName, 'savings') !== false) {
                    $account['account_type'] = 'savings';
                } elseif (stripos($accountName, 'business') !== false) {
                    $account['account_type'] = 'business';
                } else {
                    $account['account_type'] = 'checking'; // Default fallback
                }
            }
        }
        
        return $accounts;
    }
    
    /**
     * Check if user has access to account
     */
    public function userHasAccess($userId, $accountId) {
        $sql = "SELECT COUNT(*) as count
                FROM accounts a
                LEFT JOIN account_owners ao ON a.id = ao.account_id
                WHERE a.id = ? AND (a.user_id = ? OR (ao.user_id = ? AND ao.status = 'active'))";
        $stmt = $this->db->query($sql, [$accountId, $userId, $userId]);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
    
    /**
     * Send joint request email to primary owner
     */
    private function sendJointRequestEmail($requestId, $accountId, $primaryOwnerId, $requestingUserId) {
        $account = $this->db->query("SELECT * FROM accounts WHERE id = ?", [$accountId])->fetch();
        $requestingUser = $this->db->query("SELECT * FROM users WHERE id = ?", [$requestingUserId])->fetch();
        $primaryOwner = $this->db->query("SELECT * FROM users WHERE id = ?", [$primaryOwnerId])->fetch();
        
        if (!$account || !$requestingUser || !$primaryOwner) {
            return false;
        }
        
        require_once __DIR__ . '/../includes/email-template.php';
        require_once __DIR__ . '/../includes/functions.php';
        $emailTemplate = new EmailTemplate();
        $requestsPageLink = SITE_URL . "/account/joint-requests";
        
        $accountTypeDisplay = ucfirst($account['account_type']);
        $siteName = function_exists('getSiteName') ? getSiteName() : 'Octobank';
        
        $content = <<<HTML
<h2>Joint Account Request</h2>
<p>Hello {$primaryOwner['full_name']},</p>
<p>{$requestingUser['full_name']} ({$requestingUser['email']}) has requested to join your account.</p>

<div class="info-box">
    <p style="margin: 0;"><strong>Account Details:</strong></p>
    <ul style="margin: 10px 0 0 0; padding-left: 20px;">
        <li>Account Number: {$account['account_number']}</li>
        <li>Account Type: {$accountTypeDisplay}</li>
        <li>Requested By: {$requestingUser['full_name']}</li>
    </ul>
</div>

<p>Please review the request and the user's details to approve or reject:</p>

<div style="text-align: center; margin: 30px 0;">
    <a href="{$requestsPageLink}" class="btn" style="display: inline-block; padding: 14px 32px; background: #3b82f6; color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">View Joint Account Requests</a>
</div>

<div class="warning-box">
    <p style="margin: 0;"><strong>Important:</strong></p>
    <p style="margin: 5px 0 0 0;">This request will expire in 7 days. If approved, {$requestingUser['full_name']} will have full access to this account.</p>
</div>

<p>Best regards,<br><strong>{$siteName} Team</strong></p>
HTML;
        
        $emailContent = $emailTemplate->render('Joint Account Request - ' . $siteName, $content);
        sendEmail($primaryOwner['email'], 'Joint Account Request - ' . $siteName, $emailContent);
    }
    
    /**
     * Send approval email to requesting user
     */
    private function sendJointApprovalEmail($accountId, $userId) {
        $account = $this->db->query("SELECT * FROM accounts WHERE id = ?", [$accountId])->fetch();
        $user = $this->db->query("SELECT * FROM users WHERE id = ?", [$userId])->fetch();
        
        if (!$account || !$user) {
            return false;
        }
        
        require_once __DIR__ . '/../includes/email-template.php';
        require_once __DIR__ . '/../includes/functions.php';
        $emailTemplate = new EmailTemplate();
        
        $accountTypeDisplay = ucfirst($account['account_type']);
        $siteName = function_exists('getSiteName') ? getSiteName() : 'Octobank';
        $siteUrl = defined('SITE_URL') ? SITE_URL : '';
        $loginUrl = $siteUrl . '/auth/login';
        
        $content = <<<HTML
<h2>Joint Account Request Approved</h2>
<p>Hello {$user['full_name']},</p>
<p>Great news! Your request to join account {$account['account_number']} has been approved by the primary account owner.</p>

<div class="success-box">
    <p style="margin: 0;"><strong>✓ Request Approved</strong></p>
    <p style="margin: 5px 0 0 0;">You now have full access to this joint account.</p>
</div>

<p><strong>Account Details:</strong></p>
<ul style="color: #666;">
    <li>Account Number: {$account['account_number']}</li>
    <li>Account Type: {$accountTypeDisplay}</li>
</ul>

<div class="info-box" style="background: #f0f9ff; border: 1px solid #bae6fd; padding: 16px; border-radius: 8px; margin: 20px 0;">
    <p style="margin: 0;"><strong>Next Steps:</strong></p>
    <p style="margin: 5px 0 0 0;">When you log in for the first time, you'll be guided through the security setup process to configure your PIN and other security settings.</p>
</div>

<div style="text-align: center; margin: 30px 0;">
    <a href="{$loginUrl}" class="btn" style="display: inline-block; padding: 14px 32px; background: #3b82f6; color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">Log In Now</a>
</div>

<p>Best regards,<br><strong>{$siteName} Team</strong></p>
HTML;
        
        $emailContent = $emailTemplate->render('Joint Account Request Approved - ' . $siteName, $content);
        sendEmail($user['email'], 'Joint Account Request Approved - ' . $siteName, $emailContent);
    }
    
    /**
     * Send confirmation email to requesting user after registration
     */
    public function sendJointRequestConfirmationEmail($accountId, $userId) {
        $account = $this->db->query("SELECT * FROM accounts WHERE id = ?", [$accountId])->fetch();
        $user = $this->db->query("SELECT * FROM users WHERE id = ?", [$userId])->fetch();
        $primaryOwner = $this->db->query("SELECT * FROM users WHERE id = ?", [$account['user_id']])->fetch();
        
        if (!$account || !$user || !$primaryOwner) {
            return false;
        }
        
        require_once __DIR__ . '/../includes/email-template.php';
        require_once __DIR__ . '/../includes/functions.php';
        $emailTemplate = new EmailTemplate();
        
        $accountTypeDisplay = ucfirst($account['account_type']);
        $siteName = function_exists('getSiteName') ? getSiteName() : 'Octobank';
        
        $content = <<<HTML
<h2>Joint Account Request Submitted</h2>
<p>Hello {$user['full_name']},</p>
<p>Your request to join account {$account['account_number']} has been successfully submitted and is currently being processed.</p>

<div class="info-box">
    <p style="margin: 0;"><strong>What happens next?</strong></p>
    <ul style="margin: 10px 0 0 0; padding-left: 20px;">
        <li>The primary account owner ({$primaryOwner['full_name']}) has been notified of your request</li>
        <li>They will review your request and account details</li>
        <li>You will receive an email notification once they respond</li>
        <li>The request will expire in 7 days if not responded to</li>
    </ul>
</div>

<div class="warning-box" style="background: #fef3c7; border: 1px solid #fde68a; padding: 16px; border-radius: 8px; margin: 20px 0; color: #92400e;">
    <p style="margin: 0;"><strong>Important:</strong> You will not be able to log in until the primary account owner accepts your request. Once approved, you'll receive an email with login instructions and will be guided through the security setup process.</p>
</div>

<p>Best regards,<br><strong>{$siteName} Team</strong></p>
HTML;
        
        $emailContent = $emailTemplate->render('Joint Account Request Submitted - ' . $siteName, $content);
        sendEmail($user['email'], 'Joint Account Request Submitted - ' . $siteName, $emailContent);
    }
    
    /**
     * Create notification for primary owner about joint account request
     */
    private function createJointRequestNotification($primaryOwnerId, $requestId, $accountId, $requestingUserId) {
        $account = $this->db->query("SELECT * FROM accounts WHERE id = ?", [$accountId])->fetch();
        $requestingUser = $this->db->query("SELECT * FROM users WHERE id = ?", [$requestingUserId])->fetch();
        
        if (!$account || !$requestingUser) {
            return false;
        }
        
        try {
            require_once __DIR__ . '/Notification.php';
            $notification = new Notification();
            
            $title = "New Joint Account Request";
            $message = $requestingUser['full_name'] . " has requested to join your account (" . $account['account_number'] . ")";
            $link = SITE_URL . "/account/joint-requests";
            $metadata = [
                'request_id' => $requestId,
                'account_id' => $accountId,
                'requesting_user_id' => $requestingUserId
            ];
            
            $notification->create($primaryOwnerId, $title, $message, 'info', $link, $metadata);
        } catch (Exception $e) {
            error_log("Failed to create joint account request notification: " . $e->getMessage());
        }
    }
    
    /**
     * Send rejection email to requesting user
     */
    private function sendJointRejectionEmail($accountId, $userId) {
        $account = $this->db->query("SELECT * FROM accounts WHERE id = ?", [$accountId])->fetch();
        $user = $this->db->query("SELECT * FROM users WHERE id = ?", [$userId])->fetch();
        
        if (!$account || !$user) {
            return false;
        }
        
        require_once __DIR__ . '/../includes/email-template.php';
        require_once __DIR__ . '/../includes/functions.php';
        $emailTemplate = new EmailTemplate();
        
        $siteName = function_exists('getSiteName') ? getSiteName() : 'Octobank';
        
        $content = <<<HTML
<h2>Joint Account Request</h2>
<p>Hello {$user['full_name']},</p>
<p>Your request to join account {$account['account_number']} has been declined by the primary account owner.</p>

<div class="info-box">
    <p style="margin: 0;">If you have questions about this decision, please contact the account owner directly or reach out to our support team.</p>
</div>

<p>Best regards,<br><strong>{$siteName} Team</strong></p>
HTML;
        
        $emailContent = $emailTemplate->render('Joint Account Request - ' . $siteName, $content);
        sendEmail($user['email'], 'Joint Account Request - ' . $siteName, $emailContent);
    }
}

