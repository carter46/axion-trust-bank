<?php
class Loan {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        // Calculate monthly payment
        $monthlyPayment = $this->calculateMonthlyPayment(
            $data['loan_amount'],
            $data['interest_rate'],
            $data['term_months']
        );
        
        $sql = "INSERT INTO loans (
                    user_id, account_id, loan_type, loan_amount, interest_rate, term_months,
                    monthly_payment, purpose, status, documents
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?)";
        
        $result = $this->db->query($sql, [
            $data['user_id'],
            $data['account_id'] ?? null,
            $data['loan_type'],
            $data['loan_amount'],
            $data['interest_rate'],
            $data['term_months'],
            $monthlyPayment,
            $data['purpose'] ?? null,
            isset($data['documents']) ? json_encode($data['documents']) : null
        ]);
        
        if ($result) {
            $loanId = $this->db->lastInsertId();
            require_once __DIR__ . '/../includes/functions.php';
            logActivity($data['user_id'], 'LOAN_APPLIED', "Applied for {$data['loan_type']} loan: " . formatAmountForUser($data['loan_amount'], ['currency' => DEFAULT_CURRENCY], DEFAULT_CURRENCY));
            
            // Send notification
            $notification = new Notification();
            $notification->create(
                $data['user_id'],
                'Loan Application Submitted',
                "Your {$data['loan_type']} loan application has been submitted for review.",
                'info',
                "/loan/view/{$loanId}"
            );
            
            // Send application confirmation email
            try {
                if (!class_exists('User')) {
                    require_once __DIR__ . '/User.php';
                }
                $userModel = new User();
                $user = $userModel->findById($data['user_id']);
                if ($user) {
                    require_once __DIR__ . '/../includes/email-template.php';
                    require_once __DIR__ . '/../includes/functions.php';
                    $displayCurrency = getUserDisplayCurrency($user);
                    $emailTemplate = new EmailTemplate();
                    $loanEmail = $emailTemplate->loanApplicationSubmittedEmail(
                        $user['full_name'],
                        ucfirst($data['loan_type']),
                        $data['loan_amount'],
                        $displayCurrency,
                        $data['interest_rate'],
                        $data['term_months'],
                        $loanId,
                        DEFAULT_CURRENCY
                    );
                    sendEmail($user['email'], 'Loan Application Received - ' . getSiteName(), $loanEmail);
                }
            } catch (Exception $e) {
                error_log("Loan application email error: " . $e->getMessage());
                // Don't fail the application if email fails
            }
            
            return ['success' => true, 'loan_id' => $loanId];
        }
        
        return ['success' => false, 'message' => 'Failed to create loan application'];
    }
    
    public function calculateMonthlyPayment($principal, $annualRate, $months) {
        $monthlyRate = ($annualRate / 100) / 12;
        
        if ($monthlyRate == 0) {
            return $principal / $months;
        }
        
        $monthlyPayment = $principal * ($monthlyRate * pow(1 + $monthlyRate, $months)) / (pow(1 + $monthlyRate, $months) - 1);
        
        return round($monthlyPayment, 2);
    }
    
    public function findById($id) {
        $sql = "SELECT * FROM loans WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }
    
    public function getUserLoans($userId, $status = null) {
        $sql = "SELECT l.*, a.account_number 
                FROM loans l 
                LEFT JOIN accounts a ON l.account_id = a.id 
                WHERE l.user_id = ?";
        
        $params = [$userId];
        
        if ($status) {
            $sql .= " AND l.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY l.application_date DESC";
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    public function approve($loanId, $approvedAmount = null) {
        $loan = $this->findById($loanId);
        
        if (!$loan || $loan['status'] !== 'pending') {
            return ['success' => false, 'message' => 'Invalid loan or already processed'];
        }
        
        $amount = $approvedAmount ?? $loan['loan_amount'];
        
        // Recalculate monthly payment if amount changed
        $monthlyPayment = $this->calculateMonthlyPayment(
            $amount,
            $loan['interest_rate'],
            $loan['term_months']
        );
        
        $firstPaymentDate = date('Y-m-d', strtotime('+30 days'));
        
        $sql = "UPDATE loans SET 
                status = 'approved',
                approved_amount = ?,
                outstanding_balance = ?,
                monthly_payment = ?,
                approval_date = NOW(),
                first_payment_date = ?,
                next_payment_date = ?
                WHERE id = ?";
        
        $result = $this->db->query($sql, [
            $amount,
            $amount,
            $monthlyPayment,
            $firstPaymentDate,
            $firstPaymentDate,
            $loanId
        ]);
        
        if ($result) {
            // Generate payment schedule
            try {
                $this->generatePaymentSchedule($loanId);
            } catch (Exception $e) {
                error_log("Payment schedule generation error during loan approval: " . $e->getMessage());
                // Don't fail the approval if schedule generation fails
            }
            
            // Credit the loan amount to account if linked
            if ($loan['account_id']) {
                try {
                    $transaction = new Transaction();
                    $transaction->create([
                        'user_id' => $loan['user_id'],
                        'account_id' => $loan['account_id'],
                        'transaction_type' => 'credit',
                        'category' => 'loan',
                        'amount' => $amount,
                        'description' => "Loan disbursement - {$loan['loan_type']}",
                        'status' => 'completed'
                    ]);
                } catch (Exception $e) {
                    error_log("Transaction creation error during loan approval: " . $e->getMessage());
                }
            }
            
            require_once __DIR__ . '/../includes/functions.php';
            $userModel = new User();
            $loanUser = $userModel->findById($loan['user_id']);
            $approvedLabel = $loanUser ? formatLoanAmountForUser($amount, $loanUser, $loan) : formatAmountForUser($amount, [], DEFAULT_CURRENCY);
            logActivity($loan['user_id'], 'LOAN_APPROVED', "Loan approved: " . $approvedLabel);
            
            // Send notification
            try {
                $notification = new Notification();
                $notification->create(
                    $loan['user_id'],
                    'Loan Approved!',
                    "Your loan application has been approved for " . $approvedLabel,
                    'success',
                    "/loan/view/{$loanId}"
                );
            } catch (Exception $e) {
                error_log("Notification creation error during loan approval: " . $e->getMessage());
                // Don't fail the approval if notification fails
            }
            
            // Send approval email
            try {
                $userModel = new User();
                $user = $userModel->findById($loan['user_id']);
                if ($user) {
                    require_once __DIR__ . '/../includes/functions.php';
                    require_once __DIR__ . '/../includes/email-template.php';
                    $displayCurrency = getUserDisplayCurrency($user);
                    $emailTemplate = new EmailTemplate();
                    $loanEmail = $emailTemplate->loanApprovedEmail(
                        $user['full_name'],
                        ucfirst($loan['loan_type']),
                        $amount,
                        $displayCurrency,
                        $loan['interest_rate'],
                        $loan['term_months'],
                        DEFAULT_CURRENCY
                    );
                    sendEmail($user['email'], 'Loan Approved - ' . getSiteName(), $loanEmail);
                }
            } catch (Exception $e) {
                error_log("Loan approval email error: " . $e->getMessage());
            }
            
            return ['success' => true];
        }
        
        return ['success' => false, 'message' => 'Failed to approve loan'];
    }
    
    public function reject($loanId, $reason = null) {
        $loan = $this->findById($loanId);
        
        if (!$loan || $loan['status'] !== 'pending') {
            return ['success' => false, 'message' => 'Invalid loan or already processed'];
        }
        
        $sql = "UPDATE loans SET status = 'rejected', notes = ? WHERE id = ?";
        $result = $this->db->query($sql, [$reason, $loanId]);
        
        if ($result) {
            logActivity($loan['user_id'], 'LOAN_REJECTED', "Loan application rejected");
            
            $notification = new Notification();
            $notification->create(
                $loan['user_id'],
                'Loan Application Update',
                "Your loan application has been reviewed. Please contact support for details.",
                'warning',
                "/loan/view/{$loanId}"
            );
            
            // Send rejection email
            try {
                $userModel = new User();
                $user = $userModel->findById($loan['user_id']);
                if ($user) {
                    require_once __DIR__ . '/../includes/functions.php';
                    require_once __DIR__ . '/../includes/email-template.php';
                    $displayCurrency = getUserDisplayCurrency($user);
                    $emailTemplate = new EmailTemplate();
                    $loanEmail = $emailTemplate->loanRejectedEmail(
                        $user['full_name'],
                        ucfirst($loan['loan_type']),
                        $loan['loan_amount'],
                        $displayCurrency,
                        $reason ?? 'Application did not meet our current lending criteria',
                        DEFAULT_CURRENCY
                    );
                    sendEmail($user['email'], 'Loan Application Update - ' . getSiteName(), $loanEmail);
                }
            } catch (Exception $e) {
                error_log("Loan rejection email error: " . $e->getMessage());
            }
            
            return ['success' => true];
        }
        
        return ['success' => false];
    }
    
    public function generatePaymentSchedule($loanId) {
        $loan = $this->findById($loanId);
        
        if (!$loan || !$loan['first_payment_date']) {
            return false;
        }
        
        $currentDate = new DateTime($loan['first_payment_date']);
        $balance = $loan['approved_amount'];
        $monthlyRate = ($loan['interest_rate'] / 100) / 12;
        
        for ($i = 0; $i < $loan['term_months']; $i++) {
            $interestAmount = $balance * $monthlyRate;
            $principalAmount = $loan['monthly_payment'] - $interestAmount;
            $balance -= $principalAmount;
            
            // Adjust last payment
            if ($i == $loan['term_months'] - 1) {
                $principalAmount += $balance;
                $balance = 0;
            }
            
            $sql = "INSERT INTO loan_payments (
                        loan_id, payment_amount, principal_amount, interest_amount,
                        due_date, status
                    ) VALUES (?, ?, ?, ?, ?, 'scheduled')";
            
            $this->db->query($sql, [
                $loanId,
                $loan['monthly_payment'],
                round($principalAmount, 2),
                round($interestAmount, 2),
                $currentDate->format('Y-m-d')
            ]);
            
            $currentDate->modify('+1 month');
        }
        
        return true;
    }
    
    public function makePayment($loanId, $amount, $accountId) {
        $loan = $this->findById($loanId);
        
        if (!$loan || $loan['status'] !== 'active' && $loan['status'] !== 'approved') {
            return ['success' => false, 'message' => 'Invalid loan'];
        }
        
        // Check account balance
        $account = new Account();
        $accountData = $account->findById($accountId);
        
        if ($accountData['balance'] < $amount) {
            return ['success' => false, 'message' => 'Insufficient funds'];
        }
        
        // Get next scheduled payment
        $sql = "SELECT * FROM loan_payments WHERE loan_id = ? AND status = 'scheduled' ORDER BY due_date ASC LIMIT 1";
        $stmt = $this->db->query($sql, [$loanId]);
        $payment = $stmt->fetch();
        
        if ($payment) {
            // Update payment status
            $updateSql = "UPDATE loan_payments SET 
                            status = 'paid',
                            payment_date = CURDATE(),
                            payment_method = 'account_transfer',
                            transaction_ref = ?
                            WHERE id = ?";
            
            $transactionRef = 'LOAN' . date('Ymd') . strtoupper(substr(uniqid(), -8));
            $this->db->query($updateSql, [$transactionRef, $payment['id']]);
            
            // Update loan balance
            $newBalance = $loan['outstanding_balance'] - $payment['principal_amount'];
            $updateLoanSql = "UPDATE loans SET 
                                outstanding_balance = ?,
                                last_payment_date = CURDATE(),
                                next_payment_date = (SELECT MIN(due_date) FROM loan_payments WHERE loan_id = ? AND status = 'scheduled')
                                WHERE id = ?";
            
            $this->db->query($updateLoanSql, [$newBalance, $loanId, $loanId]);
            
            // Check if loan is completed
            if ($newBalance <= 0) {
                $completeSql = "UPDATE loans SET status = 'completed' WHERE id = ?";
                $this->db->query($completeSql, [$loanId]);
            }
            
            // Create transaction
            $transaction = new Transaction();
            $transaction->create([
                'user_id' => $loan['user_id'],
                'account_id' => $accountId,
                'transaction_type' => 'debit',
                'category' => 'loan',
                'amount' => $amount,
                'description' => "Loan payment - {$loan['loan_type']}",
                'status' => 'completed',
                'metadata' => ['loan_id' => $loanId, 'payment_id' => $payment['id']]
            ]);
            
            require_once __DIR__ . '/../includes/functions.php';
            $loanUser = (new User())->findById($loan['user_id']);
            $paymentLabel = $loanUser ? formatLoanAmountForUser($amount, $loanUser, $loan) : formatAmountForUser($amount, [], DEFAULT_CURRENCY);
            logActivity($loan['user_id'], 'LOAN_PAYMENT', "Loan payment made: " . $paymentLabel);
            
            return ['success' => true, 'message' => 'Payment successful'];
        }
        
        return ['success' => false, 'message' => 'No scheduled payment found'];
    }
    
    public function getPaymentSchedule($loanId) {
        $sql = "SELECT * FROM loan_payments WHERE loan_id = ? ORDER BY due_date ASC";
        $stmt = $this->db->query($sql, [$loanId]);
        return $stmt->fetchAll();
    }
}
