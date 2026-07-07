<?php
class TransferController {
    
    public function index() {
        requireLogin();
        
        // Get user accounts for balance display
        $accountModel = new Account();
        $accounts = $accountModel->getUserAccounts($_SESSION['user_id']);
        
        // Get recent transfers
        $transactionModel = new Transaction();
        $recent_transfers = $transactionModel->getUserTransactions($_SESSION['user_id'], ['limit' => 10]);
        
        include __DIR__ . '/../views/transfer/index.php';
    }
    
    public function status() {
        requireLogin();
        include __DIR__ . '/../views/transfer/status.php';
    }
    
    public function internal() {
        requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            requireNotRestrictedForFinancialActions();
            $fromAccountId = intval($_POST['from_account']);
            $toAccountId = intval($_POST['to_account']);
            $amount = floatval($_POST['amount']);
            $description = Security::sanitize($_POST['description']);
            
            if ($fromAccountId === $toAccountId) {
                $_SESSION['error'] = 'Cannot transfer to the same account';
                redirect('/transfer/internal');
            }
            
            if ($amount <= 0) {
                $_SESSION['error'] = 'Invalid amount';
                redirect('/transfer/internal');
            }
            
            $transferModel = new Transfer();
            $result = $transferModel->internal($fromAccountId, $toAccountId, $amount, $description);
            
            if ($result['success']) {
                $_SESSION['success'] = 'Transfer successful! Reference: ' . $result['debit_ref'];
                redirect('/dashboard');
            } else {
                $_SESSION['error'] = $result['message'];
                redirect('/transfer/internal');
            }
        }
        
        // Get user accounts
        $accountModel = new Account();
        $accounts = $accountModel->getUserAccounts($_SESSION['user_id']);
        
        include __DIR__ . '/../views/transfer/internal.php';
    }
    
    public function domestic() {
        requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            requireNotRestrictedForFinancialActions();
            $fromAccountId = intval($_POST['from_account']);
            $beneficiaryId = intval($_POST['beneficiary']);
            $amount = floatval($_POST['amount']);
            $description = Security::sanitize($_POST['description']);
            
            if ($amount <= 0) {
                $_SESSION['error'] = 'Invalid amount';
                redirect('/transfer/domestic');
            }
            
            $transferModel = new Transfer();
            $result = $transferModel->domestic($fromAccountId, $beneficiaryId, $amount, $description);
            
            if ($result['success']) {
                $_SESSION['success'] = 'Transfer initiated! Reference: ' . $result['transaction_ref'];
                redirect('/dashboard');
            } else {
                $_SESSION['error'] = $result['message'];
                redirect('/transfer/domestic');
            }
        }
        
        // Get user accounts and beneficiaries
        $accountModel = new Account();
        $accounts = $accountModel->getUserAccounts($_SESSION['user_id']);
        
        $db = Database::getInstance();
        $sql = "SELECT * FROM beneficiaries WHERE user_id = ? AND beneficiary_type = 'domestic' ORDER BY beneficiary_name";
        $stmt = $db->query($sql, [$_SESSION['user_id']]);
        $beneficiaries = $stmt->fetchAll();
        
        $data = [
            'accounts' => $accounts,
            'beneficiaries' => $beneficiaries
        ];
        
        include __DIR__ . '/../views/transfer/domestic.php';
    }
    
    public function international() {
        requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            requireNotRestrictedForFinancialActions();
            $fromAccountId = intval($_POST['from_account']);
            $beneficiaryId = intval($_POST['beneficiary']);
            $amount = floatval($_POST['amount']);
            $description = Security::sanitize($_POST['description']);
            
            if ($amount <= 0) {
                $_SESSION['error'] = 'Invalid amount';
                redirect('/transfer/international');
            }
            
            $transferModel = new Transfer();
            $result = $transferModel->international($fromAccountId, $beneficiaryId, $amount, $description);
            
            if ($result['success']) {
                $_SESSION['success'] = 'International transfer initiated! Reference: ' . $result['transaction_ref'];
                redirect('/dashboard');
            } else {
                $_SESSION['error'] = $result['message'];
                redirect('/transfer/international');
            }
        }
        
        // Get user accounts and beneficiaries
        $accountModel = new Account();
        $accounts = $accountModel->getUserAccounts($_SESSION['user_id']);
        
        $db = Database::getInstance();
        $sql = "SELECT * FROM beneficiaries WHERE user_id = ? AND beneficiary_type = 'international' ORDER BY beneficiary_name";
        $stmt = $db->query($sql, [$_SESSION['user_id']]);
        $beneficiaries = $stmt->fetchAll();
        
        $data = [
            'accounts' => $accounts,
            'beneficiaries' => $beneficiaries
        ];
        
        include __DIR__ . '/../views/transfer/international.php';
    }
    
    public function external() {
        requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fromAccountId = intval($_POST['from_account']);
            $beneficiaryId = intval($_POST['beneficiary']);
            $amount = floatval($_POST['amount']);
            $description = Security::sanitize($_POST['description']);
            
            if ($amount <= 0) {
                $_SESSION['error'] = 'Invalid amount';
                redirect('/transfer/external');
            }
            
            $transferModel = new Transfer();
            $result = $transferModel->international($fromAccountId, $beneficiaryId, $amount, $description);
            
            if ($result['success']) {
                $_SESSION['success'] = 'External transfer initiated! Reference: ' . $result['transaction_ref'];
                redirect('/dashboard');
            } else {
                $_SESSION['error'] = $result['message'];
                redirect('/transfer/external');
            }
        }
        
        // Get user accounts and beneficiaries
        $accountModel = new Account();
        $accounts = $accountModel->getUserAccounts($_SESSION['user_id']);
        
        $db = Database::getInstance();
        $sql = "SELECT * FROM beneficiaries WHERE user_id = ? AND beneficiary_type IN ('international', 'external') ORDER BY beneficiary_name";
        $stmt = $db->query($sql, [$_SESSION['user_id']]);
        $beneficiaries = $stmt->fetchAll();
        
        $data = [
            'accounts' => $accounts,
            'beneficiaries' => $beneficiaries
        ];
        
        include __DIR__ . '/../views/transfer/external.php';
    }
    
    public function beneficiary() {
        requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'user_id' => $_SESSION['user_id'],
                'beneficiary_name' => Security::sanitize($_POST['beneficiary_name']),
                'account_number' => Security::sanitize($_POST['account_number']),
                'bank_name' => Security::sanitize($_POST['bank_name']),
                'bank_code' => Security::sanitize($_POST['bank_code'] ?? ''),
                'swift_code' => Security::sanitize($_POST['swift_code'] ?? ''),
                'country' => Security::sanitize($_POST['country'] ?? ''),
                'currency' => Security::sanitize($_POST['currency'] ?? 'USD'),
                'nickname' => Security::sanitize($_POST['nickname'] ?? ''),
                'beneficiary_type' => Security::sanitize($_POST['beneficiary_type'])
            ];
            
            $sql = "INSERT INTO beneficiaries (user_id, beneficiary_name, account_number, bank_name, bank_code, swift_code, country, currency, nickname, beneficiary_type) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $db = Database::getInstance();
            $result = $db->query($sql, array_values($data));
            
            if ($result) {
                $_SESSION['success'] = 'Beneficiary added successfully';
            } else {
                $_SESSION['error'] = 'Failed to add beneficiary';
            }
            
            redirect('/transfer/beneficiary');
        }
        
        // Get all beneficiaries
        $db = Database::getInstance();
        $sql = "SELECT * FROM beneficiaries WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $db->query($sql, [$_SESSION['user_id']]);
        $beneficiaries = $stmt->fetchAll();
        
        include __DIR__ . '/../views/transfer/beneficiary.php';
    }
}
