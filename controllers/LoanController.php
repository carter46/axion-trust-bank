<?php
class LoanController {
    
    public function index() {
        requireLogin();
        
        $loanModel = new Loan();
        $loans = $loanModel->getUserLoans($_SESSION['user_id']);
        
        include __DIR__ . '/../views/loan/index.php';
    }
    
    public function view($id) {
        requireLogin();
        
        $loanModel = new Loan();
        $loan = $loanModel->findById($id);
        
        if (!$loan || $loan['user_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Loan not found';
            redirect('/loan');
        }
        
        // Get payment schedule
        $paymentSchedule = $loanModel->getPaymentSchedule($id);
        
        $data = [
            'loan' => $loan,
            'payment_schedule' => $paymentSchedule
        ];
        
        include __DIR__ . '/../views/loan/view.php';
    }
    
    public function apply() {
        requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            requireNotRestrictedForFinancialActions();
            $data = [
                'user_id' => $_SESSION['user_id'],
                'account_id' => intval($_POST['account_id']),
                'loan_type' => Security::sanitize($_POST['loan_type']),
                'loan_amount' => floatval($_POST['loan_amount']),
                'interest_rate' => floatval($_POST['interest_rate']),
                'term_months' => intval($_POST['term_months']),
                'purpose' => Security::sanitize($_POST['purpose'])
            ];
            
            // Handle document uploads
            if (isset($_FILES['documents']) && $_FILES['documents']['error'][0] === UPLOAD_ERR_OK) {
                $documents = [];
                for ($i = 0; $i < count($_FILES['documents']['name']); $i++) {
                    $file = [
                        'name' => $_FILES['documents']['name'][$i],
                        'type' => $_FILES['documents']['type'][$i],
                        'tmp_name' => $_FILES['documents']['tmp_name'][$i],
                        'error' => $_FILES['documents']['error'][$i],
                        'size' => $_FILES['documents']['size'][$i]
                    ];
                    
                    $upload = uploadFile($file, 'loans');
                    if ($upload['success']) {
                        $documents[] = $upload['path'];
                    }
                }
                $data['documents'] = $documents;
            }
            
            $loanModel = new Loan();
            $result = $loanModel->create($data);
            
            if ($result['success']) {
                $_SESSION['success'] = 'Loan application submitted successfully';
                redirect('/loan/view/' . $result['loan_id']);
            } else {
                $_SESSION['error'] = $result['message'];
                redirect('/loan/apply');
            }
        }
        
        // Get user accounts
        $accountModel = new Account();
        $accounts = $accountModel->getUserAccounts($_SESSION['user_id']);
        
        include __DIR__ . '/../views/loan/apply.php';
    }
    
    public function payment($id) {
        requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            requireNotRestrictedForFinancialActions();
            $amount = floatval($_POST['amount']);
            $accountId = intval($_POST['account_id']);
            
            $loanModel = new Loan();
            $result = $loanModel->makePayment($id, $amount, $accountId);
            
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            
            redirect('/loan/view/' . $id);
        }
    }
}
