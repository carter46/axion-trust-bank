<?php
class AdminController {
    private $adminModel;
    
    public function __construct() {
        $this->adminModel = new Admin();
    }
    
    public function index() {
        requireAdmin();
        
        // Get comprehensive dashboard stats
        $stats = $this->adminModel->getDashboardStats();
        $transactionChart = $this->adminModel->getTransactionChart(30);
        $suspiciousTransactions = $this->adminModel->getSuspiciousTransactions(10);
        
        // Get recent audit logs (limit to 10 most recent)
        $audit_logs = $this->adminModel->getAuditLogs([], 10);
        
        // Get system alerts
        $db = Database::getInstance();
        $sql = "SELECT * FROM system_alerts WHERE is_resolved = 0 ORDER BY severity DESC, created_at DESC LIMIT 10";
        $stmt = $db->query($sql);
        $alerts = $stmt->fetchAll();
        
        // Variables are passed directly to the view
        // $stats, $transactionChart, $suspiciousTransactions, $audit_logs, $alerts
        
        include __DIR__ . '/../views/admin/dashboard.php';
    }
    
    public function users() {
        requireAdmin();
        
        // Show all users (excluding admins and demo users)
        $db = Database::getInstance();
        $sql = "SELECT * FROM users WHERE role = 'user' AND COALESCE(is_demo_user, 0) = 0 ORDER BY created_at DESC";
        $stmt = $db->query($sql);
        $users = $stmt->fetchAll();
        
        include __DIR__ . '/../views/admin/users.php';
    }
    
    public function loginAs($userId) {
        requireAdmin();
        
        // Get the target user
        $userModel = new User();
        $targetUser = $userModel->findById($userId);
        
        if (!$targetUser) {
            $_SESSION['error'] = 'User not found';
            redirect('/admin/users');
        }
        
        // Prevent admins from logging in as other admins or demo users
        if ($targetUser['role'] === 'admin' || !empty($targetUser['is_demo_user'])) {
            $_SESSION['error'] = 'Cannot login as this account';
            redirect('/admin/users');
        }
        
        // Store admin's original session info for switching back
        $_SESSION['admin_impersonating'] = true;
        $_SESSION['admin_original_id'] = $_SESSION['user_id'];
        $_SESSION['admin_original_email'] = $_SESSION['user_email'];
        $_SESSION['admin_original_name'] = $_SESSION['user_name'];
        $_SESSION['admin_original_role'] = $_SESSION['user_role'];
        $_SESSION['admin_original_photo'] = $_SESSION['user_photo'] ?? null;
        
        // Create session as the target user
        $_SESSION['user_id'] = $targetUser['id'];
        $_SESSION['user_email'] = $targetUser['email'];
        $_SESSION['user_name'] = $targetUser['full_name'];
        $_SESSION['user_role'] = $targetUser['role'];
        
        // Set profile picture if exists
        if (!empty($targetUser['profile_picture']) && file_exists(BASE_PATH . $targetUser['profile_picture'])) {
            $_SESSION['user_photo'] = $targetUser['profile_picture'];
        } else {
            $_SESSION['user_photo'] = null;
        }
        
        // Log the impersonation
        logActivity($_SESSION['admin_original_id'], 'ADMIN_LOGIN_AS_USER', 
            "Logged in as user {$targetUser['email']} (ID: {$targetUser['id']})");
        
        // Redirect to dashboard
        $_SESSION['success'] = 'You are now logged in as ' . $targetUser['full_name'];
        redirect('/dashboard');
    }
    
    public function stopImpersonating() {
        // Check if admin is impersonating
        if (!isset($_SESSION['admin_impersonating']) || !$_SESSION['admin_impersonating']) {
            redirect('/admin');
        }
        
        // Get the original admin info
        $originalId = $_SESSION['admin_original_id'] ?? null;
        $originalEmail = $_SESSION['admin_original_email'] ?? null;
        $originalName = $_SESSION['admin_original_name'] ?? null;
        $originalRole = $_SESSION['admin_original_role'] ?? null;
        $originalPhoto = $_SESSION['admin_original_photo'] ?? null;
        
        if (!$originalId) {
            redirect('/admin');
        }
        
        // Restore admin session
        $_SESSION['user_id'] = $originalId;
        $_SESSION['user_email'] = $originalEmail;
        $_SESSION['user_name'] = $originalName;
        $_SESSION['user_role'] = $originalRole;
        $_SESSION['user_photo'] = $originalPhoto;
        
        // Clear impersonation flags
        unset($_SESSION['admin_impersonating']);
        unset($_SESSION['admin_original_id']);
        unset($_SESSION['admin_original_email']);
        unset($_SESSION['admin_original_name']);
        unset($_SESSION['admin_original_role']);
        unset($_SESSION['admin_original_photo']);
        
        // Log the action
        logActivity($originalId, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account');
        
        // Redirect to admin panel
        $_SESSION['success'] = 'Switched back to admin account';
        redirect('/admin');
    }
    
    public function user($id) {
        // Check if there's a sub-action
        $action = $_GET['action'] ?? null;
        
        switch($action) {
            case 'info':
            case 'edit':
                $this->userEdit($id);
                break;
            case 'security':
                $this->userSecurity($id);
                break;
            case 'transactions':
                $this->userTransactions($id);
                break;
            case 'status':
                $this->userStatus($id);
                break;
            case 'balance':
                $this->userBalance($id);
                break;
            default:
                $this->userView($id);
                break;
        }
    }
    
    public function userView($id) {
        requireAdmin();
        
        // Set global ID for the view
        $GLOBALS['id'] = $id;
        
        $userModel = new User();
        $user = $userModel->findById($id);
        
        if (!$user) {
            $_SESSION['error'] = 'User not found';
            redirect('/admin/users');
        }
        
        // Get user accounts
        $accountModel = new Account();
        $accounts = $accountModel->getUserAccounts($id);
        $userCurrency = getUserDisplayCurrency($user);
        $totalUserBalance = getUserTotalBalanceForDisplay($user, $accounts);
        
        // Get recent transactions
        $transactionModel = new Transaction();
        $transactions = $transactionModel->getUserTransactions($id, ['limit' => 20]);
        
        // Get loans
        $loanModel = new Loan();
        $loans = $loanModel->getUserLoans($id);
        
        // Get cards
        $cardModel = new Card();
        $cards = $cardModel->getUserCards($id);
        
        // Get activity logs
        $db = Database::getInstance();
        $sql = "SELECT * FROM activity_logs WHERE user_id = ? ORDER BY created_at DESC LIMIT 50";
        $stmt = $db->query($sql, [$id]);
        $activityLogs = $stmt->fetchAll();
        
        include __DIR__ . '/../views/admin/user-view.php';
    }
    
    public function userUpdate($id) {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'];
            
            $userModel = new User();
            
            switch ($action) {
                case 'update_status':
                    $status = Security::sanitize($_POST['status']);
                    $userModel->update($id, ['status' => $status]);
                    $_SESSION['success'] = 'User status updated';
                    break;
                    
                case 'update_kyc':
                    $kycStatus = Security::sanitize($_POST['kyc_status']);
                    $userModel->update($id, ['kyc_status' => $kycStatus]);
                    $_SESSION['success'] = 'KYC status updated';
                    break;
                    
                case 'update_role':
                    $role = Security::sanitize($_POST['role']);
                    $userModel->update($id, ['role' => $role]);
                    $_SESSION['success'] = 'User role updated';
                    break;
            }
            
            redirect('/admin/user-view/' . $id);
        }
    }
    
    public function transactions() {
        requireAdmin();
        
        $db = Database::getInstance();
        
        // Get filter parameters
        $userId = isset($_GET['user_id']) && !empty($_GET['user_id']) ? intval($_GET['user_id']) : null;
        $fromDate = $_GET['from_date'] ?? '';
        $toDate = $_GET['to_date'] ?? '';
        
        // Build SQL query with filters
        $sql = "SELECT t.*, u.full_name, u.email, a.account_number, a.account_type 
                FROM transactions t 
                JOIN users u ON t.user_id = u.id 
                LEFT JOIN accounts a ON t.account_id = a.id 
                WHERE u.role = 'user'";
        
        $params = [];
        
        // Add user filter
        if ($userId) {
            $sql .= " AND t.user_id = ?";
            $params[] = $userId;
        }
        
        // Add date range filters
        if (!empty($fromDate)) {
            $sql .= " AND DATE(t.created_at) >= ?";
            $params[] = $fromDate;
        }
        
        if (!empty($toDate)) {
            $sql .= " AND DATE(t.created_at) <= ?";
            $params[] = $toDate;
        }
        
        $sql .= " ORDER BY t.created_at DESC LIMIT 1000";
        
        $stmt = $db->query($sql, $params);
        $transactions = $stmt ? $stmt->fetchAll() : [];
        
        // Get all users for dropdown
        $usersSql = "SELECT id, full_name, email FROM users WHERE role = 'user' ORDER BY full_name ASC";
        $usersStmt = $db->query($usersSql);
        $allUsers = $usersStmt ? $usersStmt->fetchAll() : [];
        
        include __DIR__ . '/../views/admin/transactions.php';
    }
    
    public function loans() {
        requireAdmin();
        
        $status = $_GET['status'] ?? null;
        
        $db = Database::getInstance();
        $sql = "SELECT l.*, u.full_name, u.email 
                FROM loans l 
                JOIN users u ON l.user_id = u.id
                WHERE u.role = 'user'";
        
        $params = [];
        
        if ($status) {
            $sql .= " AND l.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY l.application_date DESC";
        
        $stmt = $db->query($sql, $params);
        $loans = $stmt->fetchAll();
        
        include __DIR__ . '/../views/admin/loans.php';
    }
    
    public function loanAction($id) {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'];
            $loanModel = new Loan();
            
            switch ($action) {
                case 'approve':
                    $approvedAmount = floatval($_POST['approved_amount']);
                    $result = $loanModel->approve($id, $approvedAmount);
                    break;
                    
                case 'reject':
                    $reason = Security::sanitize($_POST['reason']);
                    $result = $loanModel->reject($id, $reason);
                    break;
            }
            
            if ($result['success']) {
                $_SESSION['success'] = 'Loan ' . $action . 'd successfully';
            } else {
                $_SESSION['error'] = $result['message'];
            }
            
            redirect('/admin/loans');
        }
    }
    
    public function branding() {
        requireAdmin();
        include __DIR__ . '/../views/admin/branding.php';
    }
    
    public function systemSettings() {
        requireAdmin();
        include __DIR__ . '/../views/admin/system-settings.php';
    }
    
    public function versionControl() {
        requireAdmin();
        
        $db = Database::getInstance();
        
        // Initialize variables with default values
        $currentVersion = null;
        $versions = [];
        $updateLogs = [];
        $appliedMigrations = [];
        
        try {
            // Check if version control tables exist first
            $checkTables = $db->query("SHOW TABLES LIKE 'system_version_info'");
            $tablesExist = $checkTables && $checkTables->rowCount() > 0;
            
            if ($tablesExist) {
                // Get current system version
                try {
                    $sql = "SELECT * FROM system_version_info LIMIT 1";
                    $stmt = $db->query($sql);
                    if ($stmt) {
                        $result = $stmt->fetch();
                        if ($result) {
                            $currentVersion = $result;
                        }
                    }
                } catch (Exception $e) {
                    error_log("Error fetching current version: " . $e->getMessage());
                }
                
                // Get all versions
                try {
                    $sql = "SELECT * FROM system_versions ORDER BY release_date DESC";
                    $stmt = $db->query($sql);
                    if ($stmt) {
                        $result = $stmt->fetchAll();
                        $versions = is_array($result) ? $result : [];
                    }
                } catch (Exception $e) {
                    error_log("Error fetching versions: " . $e->getMessage());
                }
                
                // Get update logs
                try {
                    $sql = "SELECT ul.*, u.full_name, u.email 
                            FROM update_logs ul 
                            LEFT JOIN users u ON ul.applied_by = u.id 
                            ORDER BY ul.applied_date DESC 
                            LIMIT 20";
                    $stmt = $db->query($sql);
                    if ($stmt) {
                        $result = $stmt->fetchAll();
                        $updateLogs = is_array($result) ? $result : [];
                    }
                } catch (Exception $e) {
                    error_log("Error fetching update logs: " . $e->getMessage());
                }
                
                // Get applied migrations
                try {
                    $sql = "SELECT * FROM schema_migrations ORDER BY applied_at DESC LIMIT 50";
                    $stmt = $db->query($sql);
                    if ($stmt) {
                        $result = $stmt->fetchAll();
                        $appliedMigrations = is_array($result) ? $result : [];
                    }
                } catch (Exception $e) {
                    error_log("Error fetching migrations: " . $e->getMessage());
                }
            } else {
                // Tables don't exist - initialize them inline (don't require API file)
                try {
                    $db->beginTransaction();
                    
                    // Create system_versions table
                    $db->query("CREATE TABLE IF NOT EXISTS `system_versions` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `version` varchar(20) NOT NULL,
                        `release_date` datetime NOT NULL,
                        `notes` text DEFAULT NULL,
                        `created_by` int(11) DEFAULT NULL,
                        `package_size` bigint(20) DEFAULT 0,
                        `file_count` int(11) DEFAULT 0,
                        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                        PRIMARY KEY (`id`),
                        UNIQUE KEY `version` (`version`),
                        KEY `idx_version` (`version`),
                        KEY `idx_release_date` (`release_date`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
                    
                    // Create schema_migrations table
                    $db->query("CREATE TABLE IF NOT EXISTS `schema_migrations` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `version` varchar(20) NOT NULL,
                        `migration_name` varchar(255) NOT NULL,
                        `migration_file` varchar(255) NOT NULL,
                        `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
                        `applied_by` int(11) DEFAULT NULL,
                        `status` enum('success','failed','skipped') NOT NULL DEFAULT 'success',
                        `error_message` text DEFAULT NULL,
                        PRIMARY KEY (`id`),
                        UNIQUE KEY `unique_migration` (`version`,`migration_name`),
                        KEY `idx_version` (`version`),
                        KEY `idx_applied_at` (`applied_at`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
                    
                    // Create update_logs table
                    $db->query("CREATE TABLE IF NOT EXISTS `update_logs` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `version` varchar(20) NOT NULL,
                        `applied_date` datetime NOT NULL,
                        `applied_by` int(11) DEFAULT NULL,
                        `status` enum('success','failed','partial') NOT NULL DEFAULT 'success',
                        `log_details` text DEFAULT NULL,
                        `files_updated` int(11) DEFAULT 0,
                        `migrations_applied` int(11) DEFAULT 0,
                        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                        PRIMARY KEY (`id`),
                        KEY `idx_version` (`version`),
                        KEY `idx_applied_date` (`applied_date`),
                        KEY `idx_status` (`status`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
                    
                    // Create system_version_info table
                    $db->query("CREATE TABLE IF NOT EXISTS `system_version_info` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `current_version` varchar(20) NOT NULL,
                        `database_version` varchar(20) NOT NULL,
                        `last_updated` datetime NOT NULL,
                        `updated_by` int(11) DEFAULT NULL,
                        PRIMARY KEY (`id`),
                        UNIQUE KEY `unique_info` (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
                    
                    // Insert initial version info if table was just created
                    $checkData = $db->query("SELECT COUNT(*) as count FROM system_version_info");
                    $result = $checkData->fetch();
                    $dataExists = $result && $result['count'] > 0;
                    
                    if (!$dataExists) {
                        $db->query("INSERT INTO `system_version_info` (`current_version`, `database_version`, `last_updated`, `updated_by`) 
                                   VALUES ('1.0.0', '1.0.0', NOW(), NULL)");
                    }
                    
                    $db->commit();
                    
                    // After initialization, get the version info
                    $sql = "SELECT * FROM system_version_info LIMIT 1";
                    $stmt = $db->query($sql);
                    if ($stmt) {
                        $currentVersion = $stmt->fetch();
                    }
                } catch (Exception $initException) {
                    $db->rollBack();
                    error_log("Failed to initialize version control tables: " . $initException->getMessage());
                    // Continue with null/empty values - page will show initialization needed
                }
            }
        } catch (Exception $e) {
            // Log error but don't break the page
            error_log("Version control page error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            // Continue with empty arrays - page will show that tables need to be initialized
        }
        
        include __DIR__ . '/../views/admin/version-control.php';
    }
    
    public function settings() {
        requireAdmin();
        // Transfer/bank settings live on System Settings — keep old URL working
        redirect('/admin/system-settings');
    }
    
    // ============ USER MANAGEMENT ACTIONS ============
    
    public function userCreate() {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = strtolower(trim((string)Security::sanitize($_POST['email'] ?? '')));
            $gender = Security::sanitize($_POST['gender'] ?? '');
            $gender = in_array($gender, ['male', 'female', 'other'], true) ? $gender : null;
            $phone = trim((string)Security::sanitize($_POST['phone'] ?? ''));
            if (strlen($phone) > 20) {
                $phone = substr($phone, 0, 20);
            }

            $data = [
                'full_name' => Security::sanitize($_POST['full_name'] ?? ''),
                'email' => $email,
                'phone' => $phone,
                'password' => $_POST['password'] ?? '',
                'date_of_birth' => Security::sanitize($_POST['date_of_birth'] ?? ''),
                'gender' => $gender,
                'address' => Security::sanitize($_POST['address'] ?? ''),
                'city' => Security::sanitize($_POST['city'] ?? ''),
                'state' => Security::sanitize($_POST['state'] ?? ''),
                'postal_code' => Security::sanitize($_POST['postal_code'] ?? ''),
                'status' => Security::sanitize($_POST['status'] ?? 'active'),
                'kyc_status' => Security::sanitize($_POST['kyc_status'] ?? 'pending'),
                'currency' => Security::sanitize($_POST['currency'] ?? DEFAULT_CURRENCY),
                'two_factor_enabled' => (isset($_POST['two_factor_enabled']) && (string)$_POST['two_factor_enabled'] === '1') ? 1 : 0,
                'security_question_1' => 'Admin created account',
                'security_answer_1' => 'admin',
                'security_question_2' => 'Admin created account',
                'security_answer_2' => 'admin',
                'role' => 'user' // Always set to 'user' - admins are created via admin-settings page
            ];
            $data['currency'] = strtoupper(trim($data['currency'] ?: DEFAULT_CURRENCY));
            // Prefer posted country; otherwise derive from display currency
            $postedCountry = Security::sanitize($_POST['country'] ?? '');
            $data['country'] = $postedCountry !== '' ? $postedCountry : currencyToPrimaryCountry($data['currency']);

            if ($email === '' || $data['full_name'] === '' || $data['password'] === '') {
                $_SESSION['error'] = 'Full name, email, and password are required.';
                redirect('/admin/user-create');
            }

            if (!Security::validateEmail($email)) {
                $_SESSION['error'] = 'Please enter a valid email address.';
                redirect('/admin/user-create');
            }

            if (strlen($data['password']) < 8) {
                $_SESSION['error'] = 'Password must be at least 8 characters long.';
                redirect('/admin/user-create');
            }

            // Clear duplicate-email failure before DB unique constraint
            $existing = (new User())->findByEmail($email);
            if ($existing) {
                $_SESSION['error'] = 'A user with this email already exists.';
                redirect('/admin/user-create');
            }
            
            $userId = $this->adminModel->createUser($data);
            
            if ($userId) {
                // Auto-verify KYC if admin selected "verified" status
                if ($data['kyc_status'] === 'verified') {
                    require_once __DIR__ . '/../models/Kyc.php';
                    require_once __DIR__ . '/../models/Notification.php';
                    
                    $kycModel = new Kyc();
                    
                    // Create a minimal KYC verification record (admin-verified, no documents required)
                    $kycData = [
                        'user_id' => $userId,
                        'account_type' => 'individual',
                        'full_legal_name' => $data['full_name'],
                        'date_of_birth' => $data['date_of_birth'],
                        'residential_address' => $data['address'],
                        'residential_city' => $data['city'],
                        'residential_state' => $data['state'],
                        'residential_country' => $data['country'],
                        'residential_zip' => $data['postal_code']
                    ];
                    
                    $kycResult = $kycModel->create($kycData);
                    
                    if ($kycResult && isset($kycResult['success']) && $kycResult['success'] && isset($kycResult['kyc_id'])) {
                        // Immediately verify the KYC
                        $adminNotes = 'Account verified by admin during user creation - no documents required';
                        $kycModel->verify($kycResult['kyc_id'], $_SESSION['user_id'], $adminNotes);
                        
                        // Log the action
                        logActivity($_SESSION['user_id'], 'KYC_AUTO_VERIFIED', "Auto-verified KYC for user $userId during account creation");
                    }
                }
                
                // Handle initial account creation if requested
                if (isset($_POST['create_accounts']) && $_POST['create_accounts'] == '1') {
                    $accountType = Security::sanitize($_POST['account_type'] ?? '');
                    $accountBalance = floatval($_POST['account_balance'] ?? 0);
                    
                    // Validate account type
                    $validAccountTypes = ['checking', 'savings', 'business'];
                    if (!empty($accountType) && in_array($accountType, $validAccountTypes)) {
                        $accountModel = new Account();
                        
                        // Map account type to display name
                        $accountNames = [
                            'checking' => 'Checking Account',
                            'savings' => 'Savings Account',
                            'business' => 'Business Account'
                        ];
                        
                        $accountName = $accountNames[$accountType] ?? ucfirst($accountType) . ' Account';
                        
                        // Create the account
                        $accountId = $accountModel->create($userId, $accountType, $accountName);
                        
                        if ($accountId && $accountBalance > 0) {
                            // Set initial balance if provided
                            $accountModel->updateBalance($accountId, $accountBalance, 'credit');
                        }
                    }
                }
                
                $_SESSION['success'] = 'User created successfully';
                redirect('/admin/user-view/' . $userId);
            } else {
                $_SESSION['error'] = 'Failed to create user. Please check the details and try again.';
                redirect('/admin/user-create');
            }
        }
        
        include __DIR__ . '/../views/admin/user-create.php';
    }
    
    public function userEdit($id) {
        requireAdmin();
        
        // Set global ID for the view
        $GLOBALS['id'] = $id;
        
        include __DIR__ . '/../views/admin/user-edit.php';
    }
    
    public function userDelete($id) {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reason = Security::sanitize($_POST['reason']);
            
            if ($this->adminModel->deleteUser($id)) {
                $_SESSION['success'] = 'User deleted successfully';
                redirect('/admin/users');
            } else {
                $_SESSION['error'] = 'Failed to delete user';
                redirect('/admin/user-view/' . $id);
            }
        }
    }
    
    public function userResetPassword($id) {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newPassword = $_POST['new_password'];
            
            if ($this->adminModel->resetUserPassword($id, $newPassword)) {
                $_SESSION['success'] = 'Password reset successfully';
            } else {
                $_SESSION['error'] = 'Failed to reset password';
            }
            
            redirect('/admin/user-view/' . $id);
        }
    }
    
    public function userToggle2FA($id) {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $enable = isset($_POST['enable']) && $_POST['enable'] == '1';
            
            if ($this->adminModel->toggleUser2FA($id, $enable)) {
                $_SESSION['success'] = '2FA ' . ($enable ? 'enabled' : 'disabled') . ' successfully';
            } else {
                $_SESSION['error'] = 'Failed to toggle 2FA';
            }
            
            redirect('/admin/user-view/' . $id);
        }
    }
    
    public function userFlagRisk($id) {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reason = Security::sanitize($_POST['reason']);
            
            if ($this->adminModel->flagUserHighRisk($id, $reason)) {
                $_SESSION['success'] = 'User flagged as high risk';
            } else {
                $_SESSION['error'] = 'Failed to flag user';
            }
            
            redirect('/admin/user-view/' . $id);
        }
    }
    
    // ============ ACCOUNT MANAGEMENT ACTIONS ============
    
    public function accountAdjustBalance($id) {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $amount = floatval($_POST['amount']);
            $type = Security::sanitize($_POST['type']);
            $reason = Security::sanitize($_POST['reason']);
            
            $result = $this->adminModel->adjustAccountBalance($id, $amount, $type, $reason);
            
            if ($result['success']) {
                $_SESSION['success'] = 'Account balance adjusted successfully';
            } else {
                $_SESSION['error'] = $result['message'];
            }
            
            redirect('/admin/user-view/' . $_POST['user_id']);
        }
    }
    
    public function accountFreeze($id) {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reason = Security::sanitize($_POST['reason']);
            
            if ($this->adminModel->freezeAccount($id, $reason)) {
                $_SESSION['success'] = 'Account frozen successfully';
            } else {
                $_SESSION['error'] = 'Failed to freeze account';
            }
            
            redirect('/admin/user-view/' . $_POST['user_id']);
        }
    }
    
    public function accountUnfreeze($id) {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->adminModel->unfreezeAccount($id)) {
                $_SESSION['success'] = 'Account unfrozen successfully';
            } else {
                $_SESSION['error'] = 'Failed to unfreeze account';
            }
            
            redirect('/admin/user-view/' . $_POST['user_id']);
        }
    }
    
    // ============ TRANSACTION MANAGEMENT ACTIONS ============
    
    public function transactionEdit($id) {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'amount' => floatval($_POST['amount']),
                'description' => Security::sanitize($_POST['description']),
                'status' => Security::sanitize($_POST['status'])
            ];
            
            if ($this->adminModel->editTransaction($id, $data)) {
                $_SESSION['success'] = 'Transaction updated successfully';
            } else {
                $_SESSION['error'] = 'Failed to update transaction';
            }
            
            redirect('/admin/transactions');
        }
    }
    
    public function transactionDelete($id) {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reason = Security::sanitize($_POST['reason']);
            
            if ($this->adminModel->deleteTransaction($id, $reason)) {
                $_SESSION['success'] = 'Transaction deleted successfully';
            } else {
                $_SESSION['error'] = 'Failed to delete transaction';
            }
            
            redirect('/admin/transactions');
        }
    }
    
    public function transactionReverse($id) {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reason = Security::sanitize($_POST['reason']);
            
            $result = $this->adminModel->reverseTransaction($id, $reason);
            
            if ($result['success']) {
                $_SESSION['success'] = 'Transaction reversed successfully';
            } else {
                $_SESSION['error'] = $result['message'];
            }
            
            redirect('/admin/transactions');
        }
    }
    
    public function transactionFlag($id) {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reason = Security::sanitize($_POST['reason']);
            
            if ($this->adminModel->flagTransactionSuspicious($id, $reason)) {
                $_SESSION['success'] = 'Transaction flagged as suspicious';
            } else {
                $_SESSION['error'] = 'Failed to flag transaction';
            }
            
            redirect('/admin/transactions');
        }
    }
    
    // ============ LOAN MANAGEMENT ACTIONS ============
    
    public function loanApprove($id) {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $approvedAmount = floatval($_POST['approved_amount']);
            $notes = Security::sanitize($_POST['notes'] ?? '');
            
            $result = $this->adminModel->approveLoan($id, $approvedAmount, $notes);
            
            if ($result['success']) {
                $_SESSION['success'] = 'Loan approved successfully';
            } else {
                $_SESSION['error'] = $result['message'];
            }
            
            redirect('/admin/loans');
        }
    }
    
    public function loanReject($id) {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reason = Security::sanitize($_POST['reason']);
            
            $result = $this->adminModel->rejectLoan($id, $reason);
            
            if ($result['success']) {
                $_SESSION['success'] = 'Loan rejected successfully';
            } else {
                $_SESSION['error'] = $result['message'];
            }
            
            redirect('/admin/loans');
        }
    }
    
    public function loanEdit($id) {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'interest_rate' => floatval($_POST['interest_rate']),
                'term_months' => intval($_POST['term_months']),
                'monthly_payment' => floatval($_POST['monthly_payment']),
                'notes' => Security::sanitize($_POST['notes'])
            ];
            
            if ($this->adminModel->editLoan($id, $data)) {
                $_SESSION['success'] = 'Loan updated successfully';
            } else {
                $_SESSION['error'] = 'Failed to update loan';
            }
            
            redirect('/admin/loans');
        }
    }
    
    public function loanForgive($id) {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reason = Security::sanitize($_POST['reason']);
            
            if ($this->adminModel->forgiveLoan($id, $reason)) {
                $_SESSION['success'] = 'Loan forgiven successfully';
            } else {
                $_SESSION['error'] = 'Failed to forgive loan';
            }
            
            redirect('/admin/loans');
        }
    }
    
    // ============ BANK MANAGEMENT ============
    
    public function banks() {
        requireAdmin();
        
        $db = Database::getInstance();
        $userId = $_SESSION['user_id'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle Add Bank (from form with add_bank button)
            if (isset($_POST['add_bank'])) {
                $bankName = Security::sanitize($_POST['bank_name'] ?? '');
                $region = Security::sanitize($_POST['region'] ?? '');
                $country = Security::sanitize($_POST['country'] ?? '');
                $swiftCode = Security::sanitize($_POST['swift_code'] ?? '');
                
                if ($bankName && $region && $country) {
                    $sql = "INSERT INTO banks (name, region, country, swift_code, created_by, is_active) VALUES (?, ?, ?, ?, ?, 1)";
                    $result = $db->query($sql, [$bankName, $region, $country, $swiftCode, $userId]);
                    
                    if ($result) {
                        $_SESSION['success'] = 'Bank added successfully!';
                        logActivity($userId, 'bank_added', "Added bank: $bankName");
                    } else {
                        $_SESSION['error'] = 'Failed to add bank.';
                    }
                } else {
                    $_SESSION['error'] = 'Please fill in all required fields.';
                }
                
                redirect('/admin/banks');
            }
            
            // Handle other actions
            $action = Security::sanitize($_POST['action'] ?? '');
            
            switch ($action) {
                case 'toggle':
                    $id = (int) $_POST['id'];
                    $is_active = (int) $_POST['is_active'];
                    
                    $sql = "UPDATE banks SET is_active = ?, updated_at = NOW() WHERE id = ?";
                    if ($db->query($sql, [$is_active, $id])) {
                        $_SESSION['success'] = 'Bank status updated';
                    } else {
                        $_SESSION['error'] = 'Failed to update bank status';
                    }
                    redirect('/admin/banks');
                    break;
            }
        }
        
        // Get all banks
        $sql = "SELECT * FROM banks ORDER BY country ASC, name ASC";
        $stmt = $db->query($sql);
        $banks = $stmt->fetchAll();
        
        include __DIR__ . '/../views/admin/banks.php';
    }
    
    // ============ CURRENCY MANAGEMENT ============
    
    public function currencies() {
        requireAdmin();
        
        $db = Database::getInstance();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = Security::sanitize($_POST['action'] ?? '');
            
            switch ($action) {
                case 'add':
                    $code = strtoupper(Security::sanitize($_POST['code']));
                    $name = Security::sanitize($_POST['name']);
                    $symbol = Security::sanitize($_POST['symbol']);
                    $rate = floatval($_POST['rate']);
                    
                    $sql = "INSERT INTO currencies (code, name, symbol, exchange_rate, is_active, created_at) VALUES (?, ?, ?, ?, 1, NOW())";
                    if ($db->query($sql, [$code, $name, $symbol, $rate])) {
                        $_SESSION['success'] = 'Currency added successfully';
                    } else {
                        $_SESSION['error'] = 'Failed to add currency';
                    }
                    break;
                    
                case 'update_rate':
                    $id = (int) $_POST['id'];
                    $rate = floatval($_POST['rate']);
                    
                    $sql = "UPDATE currencies SET exchange_rate = ?, updated_at = NOW() WHERE id = ?";
                    if ($db->query($sql, [$rate, $id])) {
                        $codeStmt = $db->query("SELECT code FROM currencies WHERE id = ?", [$id]);
                        $codeRow = $codeStmt->fetch();
                        if ($codeRow && !empty($codeRow['code'])) {
                            require_once __DIR__ . '/../includes/exchange-rates.php';
                            // currencies.exchange_rate is stored relative to USD in admin UI
                            $sqlRate = "INSERT INTO exchange_rates (from_currency, to_currency, rate, updated_at)
                                        VALUES ('USD', ?, ?, NOW())
                                        ON DUPLICATE KEY UPDATE rate = VALUES(rate), updated_at = NOW()";
                            $db->query($sqlRate, [strtoupper($codeRow['code']), $rate]);
                        }
                        $_SESSION['success'] = 'Exchange rate updated';
                    } else {
                        $_SESSION['error'] = 'Failed to update exchange rate';
                    }
                    break;
                    
                case 'toggle':
                    $id = (int) $_POST['id'];
                    $is_active = (int) $_POST['is_active'];
                    
                    $sql = "UPDATE currencies SET is_active = ?, updated_at = NOW() WHERE id = ?";
                    if ($db->query($sql, [$is_active, $id])) {
                        $_SESSION['success'] = 'Currency status updated';
                    } else {
                        $_SESSION['error'] = 'Failed to update currency status';
                    }
                    break;
            }
            
            redirect('/admin/currencies');
        }
        
        // Get all currencies
        $sql = "SELECT * FROM currencies ORDER BY code ASC";
        $stmt = $db->query($sql);
        $currencies = $stmt->fetchAll();
        
        include __DIR__ . '/../views/admin/currencies.php';
    }
    
    // ============ CARD MANAGEMENT ACTIONS ============
    
    public function cardApprove($id) {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $notes = Security::sanitize($_POST['notes'] ?? '');
            
            if ($this->adminModel->approveCard($id, $notes)) {
                $_SESSION['success'] = 'Card approved successfully';
            } else {
                $_SESSION['error'] = 'Failed to approve card';
            }
            
            redirect('/admin/cards');
        }
    }
    
    public function cardReject($id) {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reason = Security::sanitize($_POST['reason']);
            
            if ($this->adminModel->rejectCard($id, $reason)) {
                $_SESSION['success'] = 'Card rejected successfully';
            } else {
                $_SESSION['error'] = 'Failed to reject card';
            }
            
            redirect('/admin/cards');
        }
    }
    
    public function cardEditLimits($id) {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dailyLimit = floatval($_POST['daily_limit']);
            $monthlyLimit = floatval($_POST['monthly_limit']);
            
            if ($this->adminModel->editCardLimits($id, $dailyLimit, $monthlyLimit)) {
                $_SESSION['success'] = 'Card limits updated successfully';
            } else {
                $_SESSION['error'] = 'Failed to update limits';
            }
            
            redirect('/admin/cards');
        }
    }
    
    // ============ KYC/COMPLIANCE ACTIONS ============
    
    public function kycApprove($id) {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $notes = Security::sanitize($_POST['notes'] ?? '');
            
            $kycModel = new Kyc();
            if ($kycModel->verify($id, $_SESSION['user_id'], $notes)) {
                $_SESSION['success'] = 'KYC approved successfully';
                logActivity($_SESSION['user_id'], 'KYC_APPROVED', 'Approved KYC ID: ' . $id);
            } else {
                $_SESSION['error'] = 'Failed to approve KYC';
            }
            
            redirect('/admin/kyc');
        }
    }
    
    public function kycReject($id) {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reason = Security::sanitize($_POST['reason']);
            $notes = Security::sanitize($_POST['notes'] ?? '');
            
            $kycModel = new Kyc();
            if ($kycModel->reject($id, $_SESSION['user_id'], $reason, $notes)) {
                $_SESSION['success'] = 'KYC rejected successfully';
                logActivity($_SESSION['user_id'], 'KYC_REJECTED', 'Rejected KYC ID: ' . $id);
            } else {
                $_SESSION['error'] = 'Failed to reject KYC';
            }
            
            redirect('/admin/kyc');
        }
    }
    
    public function kyc() {
        requireAdmin();
        
        $kycModel = new Kyc();
        $status = $_GET['status'] ?? 'pending';
        
        $filters = ['status' => $status];
        $pending_kyc = $kycModel->getAll($filters);
        
        include __DIR__ . '/../views/admin/kyc.php';
    }
    
    public function kycView($id) {
        requireAdmin();
        
        $kycModel = new Kyc();
        $db = Database::getInstance();
        
        // Get KYC with user info
        $sql = "SELECT kv.*, u.full_name, u.email, u.phone 
                FROM kyc_verifications kv 
                LEFT JOIN users u ON kv.user_id = u.id 
                WHERE kv.id = ?";
        $stmt = $db->query($sql, [$id]);
        $kyc = $stmt ? $stmt->fetch() : null;
        
        if (!$kyc) {
            $_SESSION['error'] = 'KYC submission not found';
            redirect('/admin/kyc');
        }
        
        // Get beneficial owners if business
        $beneficialOwners = [];
        if ($kyc['account_type'] === 'business') {
            $beneficialOwners = $kycModel->getBeneficialOwners($id);
        }
        
        include __DIR__ . '/../views/admin/kyc-view.php';
    }
    
    // ============ REPORTS & ANALYTICS ============
    
    public function reports() {
        requireAdmin();
        include __DIR__ . '/../views/admin/reports.php';
    }
    
    public function auditLogs() {
        requireAdmin();
        
        $filters = [];
        if (isset($_GET['admin_id'])) $filters['admin_id'] = $_GET['admin_id'];
        if (isset($_GET['action'])) $filters['action'] = $_GET['action'];
        if (isset($_GET['date_from'])) $filters['date_from'] = $_GET['date_from'];
        if (isset($_GET['date_to'])) $filters['date_to'] = $_GET['date_to'];
        
        $logs = $this->adminModel->getAuditLogs($filters, 200);
        
        include __DIR__ . '/../views/admin/audit-logs.php';
    }
    
    // ============ MAINTENANCE MODE ============
    
    public function maintenanceToggle() {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $enable = isset($_POST['enable']) && $_POST['enable'] == '1';
            $message = Security::sanitize($_POST['message'] ?? '');
            
            if ($enable) {
                $this->adminModel->enableMaintenanceMode($message);
                $_SESSION['success'] = 'Maintenance mode enabled';
            } else {
                $this->adminModel->disableMaintenanceMode();
                $_SESSION['success'] = 'Maintenance mode disabled';
            }
            
            redirect('/admin/system-settings');
        }
    }
    
    // ============ CARDS VIEW ============
    
    public function cards() {
        requireAdmin();
        
        // This view handles its own data fetching
        include __DIR__ . '/../views/admin/cards.php';
    }
    
    // ============ ADMIN SETTINGS ============
    
    public function adminSettings() {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            
            if ($action === 'change_password') {
                // Change admin password
                $currentPassword = $_POST['current_password'];
                $newPassword = $_POST['new_password'];
                $confirmPassword = $_POST['confirm_password'];
                
                // Get current admin user
                $userModel = new User();
                $admin = $userModel->findById($_SESSION['user_id']);
                
                // Verify current password
                if (!Security::verifyPassword($currentPassword, $admin['password_hash'])) {
                    $_SESSION['error'] = 'Current password is incorrect';
                    redirect('/admin/admin-settings');
                }
                
                // Validate new password
                if (!Security::validatePassword($newPassword)) {
                    $_SESSION['error'] = 'New password must be at least 8 characters with uppercase, lowercase, and number';
                    redirect('/admin/admin-settings');
                }
                
                if ($newPassword !== $confirmPassword) {
                    $_SESSION['error'] = 'New passwords do not match';
                    redirect('/admin/admin-settings');
                }
                
                // Update password
                $userModel->updatePassword($_SESSION['user_id'], $newPassword);
                
                // Send confirmation email
                try {
                    require_once 'includes/email-template.php';
                    $emailTemplate = new EmailTemplate();
                    $changedEmail = $emailTemplate->passwordChangedEmail($admin['full_name']);
                    sendEmail($admin['email'], 'Password Changed - ' . getSiteName(), $changedEmail);
                } catch (Exception $e) {
                    error_log("Admin password change email error: " . $e->getMessage());
                }
                
                // Log activity
                logActivity($_SESSION['user_id'], 'ADMIN_PASSWORD_CHANGED', 'Admin changed their own password');
                
                $_SESSION['success'] = 'Password changed successfully!';
                redirect('/admin/admin-settings');
                
            } elseif ($action === 'add_admin') {
                $fullName = Security::sanitize($_POST['full_name']);
                $email = Security::sanitize($_POST['email']);
                $password = $_POST['password'];
                $accountType = Security::sanitize($_POST['account_type'] ?? 'admin');

                if (!in_array($accountType, ['admin', 'demo_user'], true)) {
                    $_SESSION['error'] = 'Invalid account type';
                    redirect('/admin/admin-settings');
                }

                if ($accountType === 'demo_user' && !isSuperAdmin()) {
                    $_SESSION['error'] = 'Only Super Administrators can create demo users';
                    redirect('/admin/admin-settings');
                }

                if (!Security::validateEmail($email)) {
                    $_SESSION['error'] = 'Invalid email address';
                    redirect('/admin/admin-settings');
                }

                $userModel = new User();
                if ($userModel->findByEmail($email)) {
                    $_SESSION['error'] = 'Email address already registered';
                    redirect('/admin/admin-settings');
                }

                if (strlen($password) < 8) {
                    $_SESSION['error'] = 'Password must be at least 8 characters';
                    redirect('/admin/admin-settings');
                }

                $db = Database::getInstance();
                $passwordHash = Security::hashPassword($password);

                if ($accountType === 'demo_user') {
                    $sql = "INSERT INTO users (full_name, email, password_hash, role, is_demo_user, status, kyc_status, email_verified, created_at)
                            VALUES (?, ?, ?, 'user', 1, 'active', 'verified', 1, NOW())";
                    $result = $db->query($sql, [$fullName, $email, $passwordHash]);
                    $logLabel = 'demo user';
                } else {
                    $sql = "INSERT INTO users (full_name, email, password_hash, role, is_demo_user, status, kyc_status, email_verified, created_at)
                            VALUES (?, ?, ?, 'admin', 0, 'active', 'verified', 1, NOW())";
                    $result = $db->query($sql, [$fullName, $email, $passwordHash]);
                    $logLabel = 'admin user';
                }

                if ($result) {
                    logActivity($_SESSION['user_id'], 'ADMIN_USER_CREATED', "Created new $logLabel: $email");
                    $_SESSION['success'] = $accountType === 'demo_user'
                        ? "Demo user $fullName added successfully."
                        : "Administrator $fullName added successfully! They can now log in with their credentials.";
                } else {
                    $_SESSION['error'] = 'Failed to create account. Please try again.';
                }

                redirect('/admin/admin-settings');
            }
        }
        
        include __DIR__ . '/../views/admin/admin-settings.php';
    }
    
    // ============ EMAIL TESTING ============
    
    public function email($subPage = null) {
        requireAdmin();
        
        // Handle sub-pages
        if ($subPage === 'send') {
            $pageTitle = 'Send & Receive Email - Admin';
            require_once __DIR__ . '/../config/config.php';
            require_once __DIR__ . '/../includes/functions.php';
            include __DIR__ . '/../includes/head.php';
            include __DIR__ . '/../includes/admin-sidebar.php';
            define('EMAIL_SUBPAGE', true);
            include __DIR__ . '/../views/admin/email-send.php';
            echo '</div></div></div>'; // Close content-area, main-content-area, dashboard-container
            echo '</body></html>';
            return;
        } elseif ($subPage === 'test') {
            $pageTitle = 'Email Testing - Admin';
            require_once __DIR__ . '/../config/config.php';
            require_once __DIR__ . '/../includes/functions.php';
            include __DIR__ . '/../includes/head.php';
            include __DIR__ . '/../includes/admin-sidebar.php';
            define('EMAIL_SUBPAGE', true);
            include __DIR__ . '/../views/admin/email-test.php';
            echo '</div></div></div>'; // Close content-area, main-content-area, dashboard-container
            echo '</body></html>';
            return;
        } elseif ($subPage === 'simulation-settings') {
            $pageTitle = 'Email Simulation Settings - Admin';
            require_once __DIR__ . '/../config/config.php';
            require_once __DIR__ . '/../includes/functions.php';
            include __DIR__ . '/../includes/head.php';
            include __DIR__ . '/../includes/admin-sidebar.php';
            define('EMAIL_SUBPAGE', true);
            include __DIR__ . '/../views/admin/email-simulation-settings.php';
            echo '</div></div></div>'; // Close content-area, main-content-area, dashboard-container
            echo '</body></html>';
            return;
        } elseif ($subPage === 'simulation-test') {
            $pageTitle = 'Simulation Flash Test - Admin';
            require_once __DIR__ . '/../config/config.php';
            require_once __DIR__ . '/../includes/functions.php';
            include __DIR__ . '/../includes/head.php';
            include __DIR__ . '/../includes/admin-sidebar.php';
            define('EMAIL_SUBPAGE', true);
            include __DIR__ . '/../views/admin/email-simulation-test.php';
            echo '</div></div></div>'; // Close content-area, main-content-area, dashboard-container
            echo '</body></html>';
            return;
        }
        
        // Main email page (list)
        include __DIR__ . '/../views/admin/email.php';
    }
    
    public function emailSend() {
        requireAdmin();
        
        $pageTitle = 'Send & Receive Email - Admin';
        require_once __DIR__ . '/../config/config.php';
        require_once __DIR__ . '/../includes/functions.php';
        include __DIR__ . '/../includes/head.php';
        include __DIR__ . '/../includes/admin-sidebar.php';
        define('EMAIL_SUBPAGE', true);
        include __DIR__ . '/../views/admin/email-send.php';
        echo '</div></div></div>'; // Close content-area, main-content-area, dashboard-container
        echo '</body></html>';
    }
    
    public function emailTest() {
        requireAdmin();
        
        $pageTitle = 'Email Testing - Admin';
        require_once __DIR__ . '/../config/config.php';
        require_once __DIR__ . '/../includes/functions.php';
        include __DIR__ . '/../includes/head.php';
        include __DIR__ . '/../includes/admin-sidebar.php';
        define('EMAIL_SUBPAGE', true);
        include __DIR__ . '/../views/admin/email-test.php';
        echo '</div></div></div>'; // Close content-area, main-content-area, dashboard-container
        echo '</body></html>';
    }
    
    
    public function userProfile($id) {
        requireAdmin();
        
        // Set global ID for the view
        $GLOBALS['id'] = $id;
        
        $userModel = new User();
        $user = $userModel->findById($id);
        
        if (!$user) {
            $_SESSION['error'] = 'User not found';
            redirect('/admin/users');
        }
        
        // Get comprehensive user data
        $accountModel = new Account();
        $accounts = $accountModel->getUserAccounts($id);
        
        $transactionModel = new Transaction();
        $transactions = $transactionModel->getUserTransactions($id, ['limit' => 50]);
        
        $loanModel = new Loan();
        $loans = $loanModel->getUserLoans($id);
        
        $cardModel = new Card();
        $cards = $cardModel->getUserCards($id);
        
        // Get activity logs
        $db = Database::getInstance();
        $sql = "SELECT * FROM activity_logs WHERE user_id = ? ORDER BY created_at DESC LIMIT 100";
        $stmt = $db->query($sql, [$id]);
        $activityLogs = $stmt->fetchAll();
        
        include __DIR__ . '/../views/admin/user-profile.php';
    }
    
    public function userTransactions($id) {
        requireAdmin();
        
        // Set global ID for the view
        $GLOBALS['id'] = $id;
        
        $userModel = new User();
        $user = $userModel->findById($id);
        
        if (!$user) {
            $_SESSION['error'] = 'User not found';
            redirect('/admin/users');
        }
        
        // Get user accounts with balances
        $accountModel = new Account();
        $accounts = $accountModel->getUserAccounts($id);
        $userCurrency = getUserDisplayCurrency($user);
        $totalUserBalance = getUserTotalBalanceForDisplay($user, $accounts);
        
        // Get investment balance if exists
        $investmentBalance = floatval($user['investment_balance'] ?? 0);
        
        // Paginated user transactions (100 per page for bulk delete limit)
        $db = Database::getInstance();
        $perPage = 100;
        $currentPage = max(1, intval($_GET['page'] ?? 1));

        $countStmt = $db->query(
            "SELECT COUNT(*) AS cnt FROM transactions WHERE user_id = ?",
            [$id]
        );
        $totalTransactions = (int)(($countStmt ? $countStmt->fetch() : [])['cnt'] ?? 0);
        $totalPages = $totalTransactions > 0 ? (int)ceil($totalTransactions / $perPage) : 1;
        $currentPage = min($currentPage, $totalPages);
        $offset = ($currentPage - 1) * $perPage;

        $sql = "SELECT t.*, a.account_number, a.account_type 
                FROM transactions t 
                LEFT JOIN accounts a ON t.account_id = a.id 
                WHERE t.user_id = ? 
                ORDER BY t.created_at DESC
                LIMIT " . (int)$perPage . " OFFSET " . (int)$offset;
        $stmt = $db->query($sql, [$id]);
        $transactions = $stmt ? $stmt->fetchAll() : [];
        
        include __DIR__ . '/../views/admin/user-transactions.php';
    }
    
    public function userSecurity($id) {
        requireAdmin();
        
        // Set global ID for the view
        $GLOBALS['id'] = $id;
        
        // Fetch user data
        $userModel = new User();
        $user = $userModel->findById($id);
        
        if (!$user) {
            $_SESSION['error'] = 'User not found';
            redirect('/admin/users');
        }
        
        include __DIR__ . '/../views/admin/user-security.php';
    }
    
    public function userStatus($id) {
        requireAdmin();
        
        // Set global ID for the view
        $GLOBALS['id'] = $id;
        
        // Fetch user data
        $userModel = new User();
        $user = $userModel->findById($id);
        
        if (!$user) {
            $_SESSION['error'] = 'User not found';
            redirect('/admin/users');
        }
        
        include __DIR__ . '/../views/admin/user-status.php';
    }
    
    public function userBalance($id) {
        requireAdmin();
        
        // Set global ID for the view
        $GLOBALS['id'] = $id;
        
        // Fetch user data
        $userModel = new User();
        $user = $userModel->findById($id);
        
        if (!$user) {
            $_SESSION['error'] = 'User not found';
            redirect('/admin/users');
        }
        
        // Fetch user accounts
        $accountModel = new Account();
        $accounts = $accountModel->getUserAccounts($id);
        
        include __DIR__ . '/../views/admin/user-balance.php';
    }
    
    // ============ INVESTMENT MANAGEMENT ============
    
    public function investments() {
        requireAdmin();
        
        $productModel = new InvestmentProduct();
        $status = $_GET['status'] ?? null;
        
        $filters = [];
        if ($status) {
            $filters['status'] = $status;
        }
        
        $products = $productModel->getAll($filters);
        
        // Get stats
        $db = Database::getInstance();
        $sql = "SELECT 
                    COUNT(DISTINCT ui.id) as total_investments,
                    COUNT(DISTINCT ui.user_id) as total_investors,
                    COALESCE(SUM(ui.amount_principal), 0) as total_invested,
                    COALESCE(SUM(ui.current_accrued), 0) as total_accrued,
                    COALESCE(SUM(ui.total_roi_paid), 0) as total_paid
                FROM user_investments ui
                WHERE ui.status IN ('active', 'matured')";
        $stmt = $db->query($sql);
        $stats = $stmt ? $stmt->fetch() : [];
        
        // Variables are available directly in view
        include __DIR__ . '/../views/admin/investments.php';
    }
    
    public function investmentCreate() {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF
            if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
                $_SESSION['error'] = 'Invalid request. Please try again.';
                redirect('/admin/investments');
            }
            
            $title = Security::sanitize($_POST['title']);
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
            
            $productModel = new InvestmentProduct();
            $counter = 1;
            $originalSlug = $slug;
            while ($productModel->findBySlug($slug)) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            
            $roiMode = $_POST['roi_mode'] ?? 'fixed_daily';
            $roiConfig = [];
            
            if ($roiMode === 'fixed_daily') {
                $roiConfig = [
                    'mode' => 'fixed_daily',
                    'daily_percent' => floatval($_POST['daily_percent'] ?? 0),
                    'compound' => isset($_POST['compound'])
                ];
            } elseif ($roiMode === 'annual') {
                $roiConfig = [
                    'mode' => 'annual',
                    'annual_percent' => floatval($_POST['annual_percent'] ?? 0),
                    'compound' => isset($_POST['compound'])
                ];
            }
            
            // Handle image upload
            $imageUrl = '';
            if (!empty($_FILES['product_image']['name']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
                require_once __DIR__ . '/../includes/functions.php';
                $uploadResult = uploadFile($_FILES['product_image'], 'investments');
                if ($uploadResult['success']) {
                    $imageUrl = SITE_URL . '/uploads/' . $uploadResult['path'];
                }
            } elseif (!empty($_POST['image_url'])) {
                $imageUrl = Security::sanitize($_POST['image_url']);
            }
            
            $data = [
                'title' => $title,
                'slug' => $slug,
                'type' => Security::sanitize($_POST['type']),
                'image_url' => $imageUrl,
                'short_description' => Security::sanitize($_POST['short_description'] ?? ''),
                'full_description' => $_POST['full_description'] ?? '',
                'status' => Security::sanitize($_POST['status'] ?? 'draft'),
                'min_amount' => floatval($_POST['min_amount']),
                'max_amount' => !empty($_POST['max_amount']) ? floatval($_POST['max_amount']) : null,
                'min_duration_days' => intval($_POST['min_duration_days']),
                'max_duration_days' => !empty($_POST['max_duration_days']) ? intval($_POST['max_duration_days']) : null,
                'roi_config' => $roiConfig,
                'payout_type' => Security::sanitize($_POST['payout_type'] ?? 'compound_daily'),
                'start_date' => !empty($_POST['start_date']) ? $_POST['start_date'] : null,
                'end_date' => !empty($_POST['end_date']) ? $_POST['end_date'] : null,
                'capacity_total' => !empty($_POST['capacity_total']) ? floatval($_POST['capacity_total']) : null,
                'per_user_max' => !empty($_POST['per_user_max']) ? floatval($_POST['per_user_max']) : null,
                'risk_level' => Security::sanitize($_POST['risk_level'] ?? 'medium'),
                'display_order' => intval($_POST['display_order'] ?? 0),
                'created_by_admin_id' => $_SESSION['user_id']
            ];
            
            $productId = $productModel->create($data);
            
            if ($productId) {
                $_SESSION['success'] = 'Investment product created successfully';
                redirect('/admin/investments');
            } else {
                $_SESSION['error'] = 'Failed to create investment product';
            }
        }
        
        include __DIR__ . '/../views/admin/investment-create.php';
    }
    
    public function investmentEdit($id) {
        requireAdmin();
        
        $productModel = new InvestmentProduct();
        $product = $productModel->findById($id);
        
        if (!$product) {
            $_SESSION['error'] = 'Investment product not found';
            redirect('/admin/investments');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF
            if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
                $_SESSION['error'] = 'Invalid request. Please try again.';
                redirect('/admin/investments');
            }
            
            $roiMode = $_POST['roi_mode'] ?? 'fixed_daily';
            $roiConfig = [];
            
            if ($roiMode === 'fixed_daily') {
                $roiConfig = [
                    'mode' => 'fixed_daily',
                    'daily_percent' => floatval($_POST['daily_percent'] ?? 0),
                    'compound' => isset($_POST['compound'])
                ];
            } elseif ($roiMode === 'annual') {
                $roiConfig = [
                    'mode' => 'annual',
                    'annual_percent' => floatval($_POST['annual_percent'] ?? 0),
                    'compound' => isset($_POST['compound'])
                ];
            }
            
            // Handle image upload
            $imageUrl = $product['image_url'] ?? ''; // Keep existing by default
            if (!empty($_FILES['product_image']['name']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
                require_once __DIR__ . '/../includes/functions.php';
                $uploadResult = uploadFile($_FILES['product_image'], 'investments');
                if ($uploadResult['success']) {
                    $imageUrl = SITE_URL . '/uploads/' . $uploadResult['path'];
                }
            } elseif (!empty($_POST['image_url'])) {
                $imageUrl = Security::sanitize($_POST['image_url']);
            } elseif (isset($_POST['image_url']) && $_POST['image_url'] === '') {
                // If URL is explicitly cleared, clear it
                $imageUrl = '';
            }
            
            $data = [
                'title' => Security::sanitize($_POST['title']),
                'slug' => Security::sanitize($_POST['slug']),
                'type' => Security::sanitize($_POST['type']),
                'image_url' => $imageUrl,
                'short_description' => Security::sanitize($_POST['short_description'] ?? ''),
                'full_description' => $_POST['full_description'] ?? '',
                'status' => Security::sanitize($_POST['status'] ?? 'draft'),
                'min_amount' => floatval($_POST['min_amount']),
                'max_amount' => !empty($_POST['max_amount']) ? floatval($_POST['max_amount']) : null,
                'min_duration_days' => intval($_POST['min_duration_days']),
                'max_duration_days' => !empty($_POST['max_duration_days']) ? intval($_POST['max_duration_days']) : null,
                'roi_config' => $roiConfig,
                'payout_type' => Security::sanitize($_POST['payout_type'] ?? 'compound_daily'),
                'start_date' => !empty($_POST['start_date']) ? $_POST['start_date'] : null,
                'end_date' => !empty($_POST['end_date']) ? $_POST['end_date'] : null,
                'capacity_total' => !empty($_POST['capacity_total']) ? floatval($_POST['capacity_total']) : null,
                'per_user_max' => !empty($_POST['per_user_max']) ? floatval($_POST['per_user_max']) : null,
                'risk_level' => Security::sanitize($_POST['risk_level'] ?? 'medium'),
                'display_order' => intval($_POST['display_order'] ?? 0)
            ];
            
            if ($productModel->update($id, $data)) {
                $_SESSION['success'] = 'Investment product updated successfully';
                redirect('/admin/investments');
            } else {
                $_SESSION['error'] = 'Failed to update investment product';
            }
        }
        
        $roiConfig = json_decode($product['roi_config'], true);
        
        // Variables are available directly in view
        include __DIR__ . '/../views/admin/investment-edit.php';
    }
    
    public function investmentInvestors($id) {
        requireAdmin();
        
        $productModel = new InvestmentProduct();
        $product = $productModel->findById($id);
        
        if (!$product) {
            $_SESSION['error'] = 'Investment product not found';
            redirect('/admin/investments');
        }
        
        $db = Database::getInstance();
        $sql = "SELECT ui.*, u.full_name, u.email, a.account_number
                FROM user_investments ui
                JOIN users u ON ui.user_id = u.id
                JOIN accounts a ON ui.account_used_id = a.id
                WHERE ui.product_id = ?
                ORDER BY ui.created_at DESC";
        $stmt = $db->query($sql, [$id]);
        $investments = $stmt ? $stmt->fetchAll() : [];
        
        // Variables are available directly in view
        include __DIR__ . '/../views/admin/investment-investors.php';
    }
    
    public function runAccrual() {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF
            if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
                $_SESSION['error'] = 'Invalid request. Please try again.';
                redirect('/admin/investments');
            }
            
            $date = $_POST['date'] ?? date('Y-m-d');
            
            $userInvestmentModel = new UserInvestment();
            $activeInvestments = $userInvestmentModel->getActiveInvestmentsForAccrual($date);
            
            $processed = 0;
            $errors = 0;
            
            foreach ($activeInvestments as $investment) {
                if ($userInvestmentModel->accrueROI($investment['id'], $date)) {
                    $processed++;
                } else {
                    $errors++;
                }
            }
            
            $maturedInvestments = $userInvestmentModel->getMaturedInvestments($date);
            foreach ($maturedInvestments as $investment) {
                $userInvestmentModel->processMaturity($investment['id']);
            }
            
            $_SESSION['success'] = "Accrual run completed. Processed: {$processed}, Errors: {$errors}, Matured: " . count($maturedInvestments);
            redirect('/admin/investments');
        }
    }
    
    public function cryptoWallets() {
        requireAdmin();
        
        require_once __DIR__ . '/../models/CryptoWallet.php';
        $cryptoWalletModel = new CryptoWallet();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
                $_SESSION['error'] = 'Invalid request.';
                redirect('/admin/crypto-wallets');
            }
            
            $action = $_POST['action'] ?? '';
            
            if ($action === 'create') {
                $walletId = $cryptoWalletModel->create([
                    'crypto_type' => Security::sanitize($_POST['crypto_type']),
                    'wallet_address' => Security::sanitize($_POST['wallet_address']),
                    'network' => Security::sanitize($_POST['network'] ?? ''),
                    'label' => Security::sanitize($_POST['label'] ?? ''),
                    'is_active' => isset($_POST['is_active']) ? 1 : 0,
                    'created_by' => $_SESSION['user_id']
                ]);
                
                if ($walletId) {
                    $_SESSION['success'] = 'Crypto wallet added successfully';
                } else {
                    $_SESSION['error'] = 'Failed to add crypto wallet';
                }
            } elseif ($action === 'update') {
                $result = $cryptoWalletModel->update(intval($_POST['wallet_id']), [
                    'crypto_type' => Security::sanitize($_POST['crypto_type']),
                    'wallet_address' => Security::sanitize($_POST['wallet_address']),
                    'network' => Security::sanitize($_POST['network'] ?? ''),
                    'label' => Security::sanitize($_POST['label'] ?? ''),
                    'is_active' => isset($_POST['is_active']) ? 1 : 0
                ]);
                
                if ($result) {
                    $_SESSION['success'] = 'Crypto wallet updated successfully';
                } else {
                    $_SESSION['error'] = 'Failed to update crypto wallet';
                }
            } elseif ($action === 'delete') {
                $result = $cryptoWalletModel->delete(intval($_POST['wallet_id']));
                if ($result) {
                    $_SESSION['success'] = 'Crypto wallet deleted successfully';
                } else {
                    $_SESSION['error'] = 'Failed to delete crypto wallet';
                }
            }
            
            redirect('/admin/crypto-wallets');
        }
        
        $wallets = $cryptoWalletModel->getAll(false);
        
        // Variables available in view
        include __DIR__ . '/../views/admin/crypto-wallets.php';
    }
    
    public function investmentFunding() {
        requireAdmin();
        
        require_once __DIR__ . '/../models/InvestmentFunding.php';
        $fundingModel = new InvestmentFunding();
        
        // Handle approve/reject actions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
                $_SESSION['error'] = 'Invalid request.';
                redirect('/admin/investment-funding');
                return;
            }
            
            $action = $_POST['action'] ?? '';
            $fundingId = intval($_POST['funding_id'] ?? 0);
            
            if ($action === 'approve') {
                $result = $fundingModel->approveCryptoFunding($fundingId, $_SESSION['user_id']);
                if ($result['success']) {
                    $_SESSION['success'] = 'Crypto funding approved successfully';
                    logActivity($_SESSION['user_id'], 'CRYPTO_FUNDING_APPROVED', "Approved crypto funding #$fundingId");
                } else {
                    $_SESSION['error'] = $result['message'] ?? 'Failed to approve funding';
                }
            } elseif ($action === 'reject') {
                $reason = Security::sanitize($_POST['reason'] ?? '');
                if (empty($reason)) {
                    $_SESSION['error'] = 'Rejection reason is required';
                    redirect('/admin/investment-funding');
                    return;
                }
                
                $result = $fundingModel->rejectCryptoFunding($fundingId, $_SESSION['user_id'], $reason);
                if ($result['success']) {
                    $_SESSION['success'] = 'Crypto funding rejected successfully';
                    logActivity($_SESSION['user_id'], 'CRYPTO_FUNDING_REJECTED', "Rejected crypto funding #$fundingId: $reason");
                } else {
                    $_SESSION['error'] = $result['message'] ?? 'Failed to reject funding';
                }
            }
            
            redirect('/admin/investment-funding');
            return;
        }
        
        // Get pending crypto funding (only those with hash submitted)
        $pendingFunding = $fundingModel->getPendingCryptoFunding(100);
        
        // Get ALL crypto funding history (including all statuses, with or without hash)
        $allFunding = $fundingModel->getAllCryptoFunding(200);
        
        // Ensure arrays are never null
        $pendingFunding = $pendingFunding ?? [];
        $allFunding = $allFunding ?? [];
        
        // Debug: Log counts and sample data for troubleshooting
        error_log("Admin Investment Funding - Pending: " . count($pendingFunding) . ", All: " . count($allFunding));
        if (count($pendingFunding) > 0) {
            error_log("Sample pending funding: " . json_encode($pendingFunding[0]));
        }
        if (count($allFunding) > 0) {
            error_log("Sample all funding: " . json_encode($allFunding[0]));
        }
        
        include __DIR__ . '/../views/admin/investment-funding.php';
    }

    public function transactionGenerator() {
        requireAdmin();

        require_once __DIR__ . '/../includes/transaction-history-generator.php';
        require_once __DIR__ . '/../includes/generator-data/generator-helpers.php';
        $db = Database::getInstance();
        $generator = new TransactionHistoryGenerator();

        $usersStmt = $db->query(
            "SELECT id, full_name, email FROM users WHERE role = 'user' ORDER BY full_name ASC"
        );
        $allUsers = $usersStmt ? $usersStmt->fetchAll() : [];

        $template = $generator->getDefaultTemplate();
        $templateReady = true;
        $generatorPersonas = getGeneratorPersonas();
        $generatorPresets = getGeneratorPresets();
        $selectedAccountId = isset($_GET['account_id']) ? intval($_GET['account_id']) : 0;
        $batches = [];
        try {
            $batches = $generator->listBatches($selectedAccountId ?: null, 50);
        } catch (Throwable $e) {
            error_log('transactionGenerator listBatches: ' . $e->getMessage());
        }

        include __DIR__ . '/../views/admin/transaction-generator.php';
    }
}
