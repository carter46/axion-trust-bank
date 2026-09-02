<?php
class Admin {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
        
        // Include required models
        if (!class_exists('User')) {
            require_once __DIR__ . '/User.php';
        }
        if (!class_exists('Account')) {
            require_once __DIR__ . '/Account.php';
        }
        if (!class_exists('Transaction')) {
            require_once __DIR__ . '/Transaction.php';
        }
        if (!class_exists('Loan')) {
            require_once __DIR__ . '/Loan.php';
        }
        if (!class_exists('Notification')) {
            require_once __DIR__ . '/Notification.php';
        }
        // EmailTemplate class doesn't exist, skip it
    }
    
    // ============ USER MANAGEMENT ============
    
    public function createUser($data) {
        $userModel = new User();
        $userId = $userModel->create($data);
        
        if ($userId) {
            $this->logAdminAction('USER_CREATED', "Created user: {$data['email']}", ['user_id' => $userId]);
        }
        
        return $userId;
    }
    
    public function updateUser($userId, $data) {
        $userModel = new User();
        $result = $userModel->update($userId, $data);
        
        if ($result) {
            $this->logAdminAction('USER_UPDATED', "Updated user ID: $userId", ['user_id' => $userId, 'changes' => $data]);
        }
        
        return $result;
    }
    
    public function deleteUser($userId) {
        $sql = "DELETE FROM users WHERE id = ?";
        $result = $this->db->query($sql, [$userId]);
        
        if ($result) {
            $this->logAdminAction('USER_DELETED', "Deleted user ID: $userId", ['user_id' => $userId]);
        }
        
        return $result;
    }
    
    public function resetUserPassword($userId, $newPassword) {
        $userModel = new User();
        $result = $userModel->updatePassword($userId, $newPassword);
        
        if ($result) {
            $this->logAdminAction('PASSWORD_RESET', "Reset password for user ID: $userId", ['user_id' => $userId]);
        }
        
        return $result;
    }
    
    public function toggleUser2FA($userId, $enable) {
        $userModel = new User();
        $result = $enable ? $userModel->enable2FA($userId) : $userModel->disable2FA($userId);
        
        if ($result) {
            $action = $enable ? 'enabled' : 'disabled';
            $this->logAdminAction('2FA_TOGGLED', "2FA $action for user ID: $userId", ['user_id' => $userId]);
        }
        
        return $result;
    }
    
    public function flagUserHighRisk($userId, $reason) {
        $sql = "UPDATE users SET metadata = JSON_SET(COALESCE(metadata, '{}'), '$.high_risk', 1, '$.risk_reason', ?) WHERE id = ?";
        $result = $this->db->query($sql, [$reason, $userId]);
        
        if ($result) {
            $this->logAdminAction('USER_FLAGGED_HIGH_RISK', "Flagged user ID $userId as high risk: $reason", ['user_id' => $userId]);
        }
        
        return $result;
    }
    
    // ============ ACCOUNT MANAGEMENT ============
    
    public function adjustAccountBalance($accountId, $amount, $type, $reason) {
        $accountModel = new Account();
        $account = $accountModel->findById($accountId);
        
        if (!$account) {
            return ['success' => false, 'message' => 'Account not found'];
        }
        
        // Create admin adjustment transaction
        $transactionModel = new Transaction();
        $result = $transactionModel->create([
            'user_id' => $account['user_id'],
            'account_id' => $accountId,
            'transaction_type' => $type,
            'category' => 'other',
            'amount' => abs($amount),
            'description' => "Admin adjustment: $reason",
            'status' => 'completed',
            'metadata' => ['admin_adjustment' => true, 'admin_id' => $_SESSION['user_id']]
        ]);
        
        if ($result['success']) {
            $accountModel->updateBalance($accountId, abs($amount), $type);
            $this->logAdminAction('BALANCE_ADJUSTED', "Adjusted balance for account $accountId: $type $amount - $reason", [
                'account_id' => $accountId,
                'amount' => $amount,
                'type' => $type
            ]);
        }
        
        return $result;
    }
    
    public function freezeAccount($accountId, $reason) {
        $accountModel = new Account();
        $result = $accountModel->freeze($accountId);
        
        if ($result) {
            $this->logAdminAction('ACCOUNT_FROZEN', "Froze account $accountId: $reason", ['account_id' => $accountId]);
        }
        
        return $result;
    }
    
    public function unfreezeAccount($accountId) {
        $accountModel = new Account();
        $result = $accountModel->unfreeze($accountId);
        
        if ($result) {
            $this->logAdminAction('ACCOUNT_UNFROZEN', "Unfroze account $accountId", ['account_id' => $accountId]);
        }
        
        return $result;
    }
    
    // ============ TRANSACTION MANAGEMENT ============
    
    public function editTransaction($transactionId, $data) {
        $fields = [];
        $values = [];
        
        $allowedFields = ['amount', 'description', 'recipient_account', 'recipient_name', 'status'];
        
        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $fields[] = "$key = ?";
                $values[] = $value;
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $transactionId;
        
        $sql = "UPDATE transactions SET " . implode(', ', $fields) . " WHERE id = ?";
        $result = $this->db->query($sql, $values);
        
        if ($result) {
            $this->logAdminAction('TRANSACTION_EDITED', "Edited transaction $transactionId", [
                'transaction_id' => $transactionId,
                'changes' => $data
            ]);
        }
        
        return $result;
    }
    
    public function deleteTransaction($transactionId, $reason) {
        $sql = "DELETE FROM transactions WHERE id = ?";
        $result = $this->db->query($sql, [$transactionId]);
        
        if ($result) {
            $this->logAdminAction('TRANSACTION_DELETED', "Deleted transaction $transactionId: $reason", [
                'transaction_id' => $transactionId,
                'reason' => $reason
            ]);
        }
        
        return $result;
    }
    
    public function reverseTransaction($transactionId, $reason) {
        $transactionModel = new Transaction();
        $result = $transactionModel->reverse($transactionId, "Admin reversal: $reason");
        
        if ($result['success']) {
            $this->logAdminAction('TRANSACTION_REVERSED', "Reversed transaction $transactionId: $reason", [
                'transaction_id' => $transactionId
            ]);
        }
        
        return $result;
    }
    
    public function flagTransactionSuspicious($transactionId, $reason) {
        $sql = "UPDATE transactions SET metadata = JSON_SET(COALESCE(metadata, '{}'), '$.suspicious', 1, '$.flag_reason', ?) WHERE id = ?";
        $result = $this->db->query($sql, [$reason, $transactionId]);
        
        if ($result) {
            $this->logAdminAction('TRANSACTION_FLAGGED', "Flagged transaction $transactionId as suspicious: $reason", [
                'transaction_id' => $transactionId
            ]);
        }
        
        return $result;
    }
    
    // ============ LOAN MANAGEMENT ============
    
    public function approveLoan($loanId, $approvedAmount, $notes = null) {
        $loanModel = new Loan();
        $result = $loanModel->approve($loanId, $approvedAmount);
        
        if ($result['success']) {
            if ($notes) {
                $this->db->query("UPDATE loans SET notes = ? WHERE id = ?", [$notes, $loanId]);
            }
            $this->logAdminAction('LOAN_APPROVED', "Approved loan $loanId for $approvedAmount", [
                'loan_id' => $loanId,
                'amount' => $approvedAmount
            ]);
        }
        
        return $result;
    }
    
    public function rejectLoan($loanId, $reason) {
        $loanModel = new Loan();
        $result = $loanModel->reject($loanId, $reason);
        
        if ($result['success']) {
            $this->logAdminAction('LOAN_REJECTED', "Rejected loan $loanId: $reason", ['loan_id' => $loanId]);
        }
        
        return $result;
    }
    
    public function editLoan($loanId, $data) {
        $fields = [];
        $values = [];
        
        $allowedFields = ['loan_amount', 'approved_amount', 'interest_rate', 'term_months', 'monthly_payment', 'notes'];
        
        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $fields[] = "$key = ?";
                $values[] = $value;
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $loanId;
        
        $sql = "UPDATE loans SET " . implode(', ', $fields) . " WHERE id = ?";
        $result = $this->db->query($sql, $values);
        
        if ($result) {
            $this->logAdminAction('LOAN_EDITED', "Edited loan $loanId", [
                'loan_id' => $loanId,
                'changes' => $data
            ]);
        }
        
        return $result;
    }
    
    public function forgiveLoan($loanId, $reason) {
        $sql = "UPDATE loans SET outstanding_balance = 0, status = 'completed', notes = CONCAT(COALESCE(notes, ''), ' | Forgiven: ?') WHERE id = ?";
        $result = $this->db->query($sql, [$reason, $loanId]);
        
        if ($result) {
            $this->logAdminAction('LOAN_FORGIVEN', "Forgave loan $loanId: $reason", ['loan_id' => $loanId]);
        }
        
        return $result;
    }
    
    // ============ CARD MANAGEMENT ============
    
    public function approveCard($cardId, $notes = null) {
        $sql = "UPDATE cards SET status = 'active' WHERE id = ?";
        $result = $this->db->query($sql, [$cardId]);
        
        if ($result && $notes) {
            $this->db->query("UPDATE cards SET metadata = JSON_SET(COALESCE(metadata, '{}'), '$.admin_notes', ?) WHERE id = ?", [$notes, $cardId]);
        }
        
        if ($result) {
            $this->logAdminAction('CARD_APPROVED', "Approved card $cardId", ['card_id' => $cardId]);
            
            // Get card details to send notification to user
            $cardSql = "SELECT user_id, card_type FROM cards WHERE id = ?";
            $cardStmt = $this->db->query($cardSql, [$cardId]);
            $card = $cardStmt->fetch();
            
            if ($card) {
                $notification = new Notification();
                $notification->create(
                    $card['user_id'],
                    'Card Approved',
                    "Your {$card['card_type']} card has been approved and is now active.",
                    'success',
                    "/card/view/{$cardId}"
                );
            }
        }
        
        return $result;
    }
    
    public function rejectCard($cardId, $reason) {
        $sql = "UPDATE cards SET status = 'cancelled', metadata = JSON_SET(COALESCE(metadata, '{}'), '$.rejection_reason', ?) WHERE id = ?";
        $result = $this->db->query($sql, [$reason, $cardId]);
        
        if ($result) {
            $this->logAdminAction('CARD_REJECTED', "Rejected card $cardId: $reason", ['card_id' => $cardId]);
            
            // Get card details to send notification to user
            $cardSql = "SELECT user_id, card_type FROM cards WHERE id = ?";
            $cardStmt = $this->db->query($cardSql, [$cardId]);
            $card = $cardStmt->fetch();
            
            if ($card) {
                $notification = new Notification();
                $notification->create(
                    $card['user_id'],
                    'Card Application Rejected',
                    "Your {$card['card_type']} card application has been rejected. Reason: $reason",
                    'error',
                    "/card"
                );
            }
        }
        
        return $result;
    }
    
    public function editCardLimits($cardId, $dailyLimit, $monthlyLimit) {
        $sql = "UPDATE cards SET daily_limit = ?, monthly_limit = ? WHERE id = ?";
        $result = $this->db->query($sql, [$dailyLimit, $monthlyLimit, $cardId]);
        
        if ($result) {
            $this->logAdminAction('CARD_LIMITS_UPDATED', "Updated limits for card $cardId", [
                'card_id' => $cardId,
                'daily_limit' => $dailyLimit,
                'monthly_limit' => $monthlyLimit
            ]);
        }
        
        return $result;
    }
    
    // ============ KYC/COMPLIANCE ============
    
    public function approveKYC($userId, $notes = null) {
        $sql = "UPDATE users SET kyc_status = 'verified', status = 'active' WHERE id = ?";
        $result = $this->db->query($sql, [$userId]);
        
        if ($result && $notes) {
            $this->addUserNote($userId, "KYC Approved: $notes");
        }
        
        if ($result) {
            $this->logAdminAction('KYC_APPROVED', "Approved KYC for user $userId", ['user_id' => $userId]);
            
            // Send notification
            $notification = new Notification();
            $notification->create($userId, 'KYC Approved', 'Your account has been verified and is now active.', 'success');
            
            // Send approval email
            try {
                $userModel = new User();
                $user = $userModel->findById($userId);
                if ($user) {
                    try {
                        require_once __DIR__ . '/../includes/email-template.php';
                        $emailTemplate = new EmailTemplate();
                        $kycEmail = $emailTemplate->kycApprovedEmail($user['full_name']);
                        sendEmail($user['email'], 'KYC Approved - ' . getSiteName(), $kycEmail);
                    } catch (Exception $e) {
                        error_log("EmailTemplate error: " . $e->getMessage());
                        // Send simple email without template
                        sendEmail($user['email'], 'KYC Approved - ' . getSiteName(), 'Your KYC verification has been approved.');
                    }
                }
            } catch (Exception $e) {
                error_log("KYC approval email error: " . $e->getMessage());
            }
        }
        
        return $result;
    }
    
    public function rejectKYC($userId, $reason) {
        $sql = "UPDATE users SET kyc_status = 'rejected' WHERE id = ?";
        $result = $this->db->query($sql, [$userId]);
        
        if ($result) {
            $this->addUserNote($userId, "KYC Rejected: $reason");
            $this->logAdminAction('KYC_REJECTED', "Rejected KYC for user $userId: $reason", ['user_id' => $userId]);
            
            // Send notification
            $notification = new Notification();
            $notification->create($userId, 'KYC Rejected', 'Your verification has been rejected. Please contact support.', 'warning');
            
            // Send rejection email
            try {
                $userModel = new User();
                $user = $userModel->findById($userId);
                if ($user) {
                    try {
                        require_once __DIR__ . '/../includes/email-template.php';
                        $emailTemplate = new EmailTemplate();
                        $kycEmail = $emailTemplate->kycRejectedEmail($user['full_name'], $reason);
                        sendEmail($user['email'], 'KYC Submission - Action Required', $kycEmail);
                    } catch (Exception $e) {
                        error_log("EmailTemplate error: " . $e->getMessage());
                        // Send simple email without template
                        sendEmail($user['email'], 'KYC Submission - Action Required', 'Your KYC verification has been rejected. Please contact support.');
                    }
                }
            } catch (Exception $e) {
                error_log("KYC rejection email error: " . $e->getMessage());
            }
        }
        
        return $result;
    }
    
    public function addUserNote($userId, $note) {
        $sql = "INSERT INTO user_notes (user_id, admin_id, note, created_at) VALUES (?, ?, ?, NOW())";
        return $this->db->query($sql, [$userId, $_SESSION['user_id'], $note]);
    }
    
    // ============ SYSTEM SETTINGS ============
    
    public function updateSystemSetting($key, $value) {
        $sql = "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?";
        $result = $this->db->query($sql, [$value, $key]);
        
        if ($result) {
            $this->logAdminAction('SETTING_UPDATED', "Updated setting: $key = $value", ['setting' => $key]);
        }
        
        return $result;
    }
    
    public function enableMaintenanceMode($message = null) {
        $this->updateSystemSetting('maintenance_mode', '1');
        if ($message) {
            $this->updateSystemSetting('maintenance_message', $message);
        }
        $this->logAdminAction('MAINTENANCE_ENABLED', 'Enabled maintenance mode');
    }
    
    public function disableMaintenanceMode() {
        $this->updateSystemSetting('maintenance_mode', '0');
        $this->logAdminAction('MAINTENANCE_DISABLED', 'Disabled maintenance mode');
    }
    
    // ============ REPORTS & ANALYTICS ============
    
    public function getDashboardStats() {
        $stats = [];
        
        // User stats (ALL users excluding admins)
        $sql = "SELECT COUNT(*) as total, 
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN status = 'suspended' THEN 1 ELSE 0 END) as suspended
                FROM users WHERE role = 'user' AND COALESCE(is_demo_user, 0) = 0 AND COALESCE(is_demo_user, 0) = 0";
        $stmt = $this->db->query($sql);
        $stats['users'] = $stmt->fetch();
        
        // Count actual pending KYC submissions (not users with pending kyc_status)
        // Only count KYC submissions that are actually pending review
        $kycSql = "SELECT COUNT(*) as pending_kyc 
                   FROM kyc_verifications 
                   WHERE status IN ('pending', 'under_review', 'requires_action')";
        $kycStmt = $this->db->query($kycSql);
        $kycResult = $kycStmt->fetch();
        $stats['users']['pending_kyc'] = $kycResult['pending_kyc'] ?? 0;
        
        // Account stats (ALL user accounts)
        $sql = "SELECT COUNT(*) as total,
                SUM(balance) as total_balance,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active
                FROM accounts WHERE user_id IN (SELECT id FROM users WHERE role = 'user' AND COALESCE(is_demo_user, 0) = 0)";
        $stmt = $this->db->query($sql);
        $stats['accounts'] = $stmt->fetch();
        
        // Transaction stats (ALL users, today)
        $sql = "SELECT COUNT(*) as count, SUM(amount) as total
                FROM transactions 
                WHERE user_id IN (SELECT id FROM users WHERE role = 'user' AND COALESCE(is_demo_user, 0) = 0) 
                AND DATE(created_at) = CURDATE()";
        $stmt = $this->db->query($sql);
        $stats['transactions_today'] = $stmt->fetch();
        
        // Loan stats (ALL users)
        $sql = "SELECT 
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                SUM(outstanding_balance) as total_outstanding
                FROM loans WHERE user_id IN (SELECT id FROM users WHERE role = 'user' AND COALESCE(is_demo_user, 0) = 0)";
        $stmt = $this->db->query($sql);
        $stats['loans'] = $stmt->fetch();
        
        // Card stats (ALL users)
        $sql = "SELECT COUNT(*) as total,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN status = 'frozen' THEN 1 ELSE 0 END) as frozen
                FROM cards WHERE user_id IN (SELECT id FROM users WHERE role = 'user' AND COALESCE(is_demo_user, 0) = 0)";
        $stmt = $this->db->query($sql);
        $stats['cards'] = $stmt->fetch();
        
        return $stats;
    }
    
    public function getTransactionChart($days = 30) {
        $sql = "SELECT DATE(created_at) as date, 
                COUNT(*) as count,
                SUM(CASE WHEN transaction_type = 'credit' THEN amount ELSE 0 END) as credits,
                SUM(CASE WHEN transaction_type = 'debit' THEN amount ELSE 0 END) as debits
                FROM transactions 
                WHERE user_id IN (SELECT id FROM users WHERE role = 'user' AND COALESCE(is_demo_user, 0) = 0)
                AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                GROUP BY DATE(created_at)
                ORDER BY date ASC";
        
        $stmt = $this->db->query($sql, [$days]);
        return $stmt->fetchAll();
    }
    
    public function getSuspiciousTransactions($limit = 50) {
        $sql = "SELECT t.*, u.full_name, u.email 
                FROM transactions t
                JOIN users u ON t.user_id = u.id
                WHERE u.role = 'user' AND COALESCE(u.is_demo_user, 0) = 0 
                AND (t.amount > 10000 OR JSON_EXTRACT(t.metadata, '$.suspicious') = 1)
                ORDER BY t.created_at DESC
                LIMIT ?";
        
        $stmt = $this->db->query($sql, [$limit]);
        return $stmt->fetchAll();
    }
    
    // ============ AUDIT LOGGING ============
    
    private function logAdminAction($action, $description, $metadata = null) {
        $sql = "INSERT INTO admin_logs (admin_id, action, description, metadata, ip_address, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        
        $this->db->query($sql, [
            $_SESSION['user_id'],
            $action,
            $description,
            $metadata ? json_encode($metadata) : null,
            $_SERVER['REMOTE_ADDR']
        ]);
    }
    
    public function getAuditLogs($filters = [], $limit = 100) {
        $sql = "SELECT a.*, u.full_name as admin_name, u.email as admin_email
                FROM admin_logs a
                JOIN users u ON a.admin_id = u.id
                WHERE 1=1";
        
        $params = [];
        
        if (isset($filters['admin_id'])) {
            $sql .= " AND a.admin_id = ?";
            $params[] = $filters['admin_id'];
        }
        
        if (isset($filters['action'])) {
            $sql .= " AND a.action = ?";
            $params[] = $filters['action'];
        }
        
        if (isset($filters['date_from'])) {
            $sql .= " AND DATE(a.created_at) >= ?";
            $params[] = $filters['date_from'];
        }
        
        if (isset($filters['date_to'])) {
            $sql .= " AND DATE(a.created_at) <= ?";
            $params[] = $filters['date_to'];
        }
        
        $sql .= " ORDER BY a.created_at DESC LIMIT ?";
        $params[] = $limit;
        
        $stmt = $this->db->query($sql, $params);
        $logs = $stmt->fetchAll();
        
        // If we're fetching for dashboard (limit = 10), automatically maintain only 10 most recent logs
        if ($limit == 10 && empty($filters)) {
            // Get the total count
            $countSql = "SELECT COUNT(*) as total FROM admin_logs";
            $countStmt = $this->db->query($countSql);
            $countResult = $countStmt->fetch();
            $totalLogs = $countResult['total'] ?? 0;
            
            // If there are more than 10 logs, delete the older ones
            if ($totalLogs > 10) {
                // Get the 10th most recent log's created_at timestamp
                $top10Sql = "SELECT created_at FROM admin_logs ORDER BY created_at DESC LIMIT 1 OFFSET 9";
                $top10Stmt = $this->db->query($top10Sql);
                $top10Result = $top10Stmt->fetch();
                
                if ($top10Result) {
                    // Delete all logs older than the 10th most recent
                    $deleteSql = "DELETE FROM admin_logs WHERE created_at < ?";
                    $this->db->query($deleteSql, [$top10Result['created_at']]);
                }
            }
        }
        
        return $logs;
    }

    /**
     * Clear all admin recent-activity / audit log entries.
     */
    public function clearAuditLogs() {
        $result = $this->db->query("DELETE FROM admin_logs");
        return $result !== false;
    }
    
    // ============ ADMIN ROLES ============
    
    public function hasPermission($permission) {
        $role = $_SESSION['user_role'];
        
        $permissions = [
            'admin' => ['*'], // Super admin - all permissions
            'compliance' => ['kyc_approve', 'kyc_reject', 'view_users', 'flag_transactions'],
            'support' => ['view_users', 'reset_password', 'view_transactions'],
            'accountant' => ['view_transactions', 'view_accounts', 'export_reports']
        ];
        
        if (!isset($permissions[$role])) {
            return false;
        }
        
        return in_array('*', $permissions[$role]) || in_array($permission, $permissions[$role]);
    }
}
