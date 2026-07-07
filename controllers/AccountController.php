<?php
class AccountController {
    
    public function index() {
        requireLogin();
        
        $accountModel = new Account();
        $accounts = $accountModel->getUserAccounts($_SESSION['user_id']);
        
        include __DIR__ . '/../views/account/index.php';
    }
    
    public function view($id) {
        requireLogin();
        
        $accountModel = new Account();
        $account = $accountModel->findById($id);
        
        // Check if user has access (own account or joint owner)
        require_once __DIR__ . '/../models/JointAccount.php';
        $jointAccount = new JointAccount();
        $hasAccess = ($account && $account['user_id'] == $_SESSION['user_id']) || 
                     ($account && $jointAccount->userHasAccess($_SESSION['user_id'], $id));
        
        if (!$account || !$hasAccess) {
            $_SESSION['error'] = 'Account not found';
            redirect('/account');
        }
        
        // Get transactions
        $transactionModel = new Transaction();
        $transactions = $transactionModel->getAccountTransactions($id, 100, $_SESSION['user_id']);
        
        $data = [
            'account' => $account,
            'transactions' => $transactions
        ];
        
        include __DIR__ . '/../views/account/view.php';
    }
    
    public function create() {
        requireLogin();
        
        // Check if user joined via joint account - they cannot create accounts
        require_once __DIR__ . '/../models/JointAccount.php';
        $jointAccount = new JointAccount();
        if ($jointAccount->isJointAccountUser($_SESSION['user_id'])) {
            $_SESSION['error'] = 'You cannot create new accounts. You have access to shared accounts only.';
            redirect('/account');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $type = Security::sanitize($_POST['account_type']);
            // Generate account name based on type and user's OWNED accounts (not joint access)
            $accountModel = new Account();
            $ownedAccounts = $jointAccount->getUserOwnedAccounts($_SESSION['user_id']);
            $sameTypeCount = count(array_filter($ownedAccounts, function($acc) use ($type) {
                return $acc['account_type'] === $type;
            }));
            $name = ucfirst($type) . ' Account' . ($sameTypeCount > 0 ? ' ' . ($sameTypeCount + 1) : '');
            
            $accountId = $accountModel->create($_SESSION['user_id'], $type, $name);
            
            if ($accountId) {
                $_SESSION['success'] = 'Account created successfully';
                redirect('/account/view/' . $accountId);
            } else {
                $_SESSION['error'] = 'Failed to create account';
                redirect('/account/create');
            }
        }
        
        include __DIR__ . '/../views/account/create.php';
    }
    
    public function statement($id) {
        requireLogin();
        
        $accountModel = new Account();
        $account = $accountModel->findById($id);
        
        if (!$account || $account['user_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Account not found';
            redirect('/account');
        }
        
        // Get date range from query params
        $dateFrom = $_GET['date_from'] ?? date('Y-m-01');
        $dateTo = $_GET['date_to'] ?? date('Y-m-t');
        
        $transactionModel = new Transaction();
        $transactions = $transactionModel->getUserTransactions($_SESSION['user_id'], [
            'account_id' => $id,
            'date_from' => $dateFrom,
            'date_to' => $dateTo
        ]);
        
        $data = [
            'account' => $account,
            'transactions' => $transactions,
            'date_from' => $dateFrom,
            'date_to' => $dateTo
        ];
        
        include __DIR__ . '/../views/account/statement.php';
    }
    
    public function transaction($id) {
        requireLogin();
        
        $transactionModel = new Transaction();
        $transaction = $transactionModel->findById($id);
        
        if (!$transaction) {
            $_SESSION['error'] = 'Transaction not found';
            redirect('/dashboard');
        }
        
        // Check if user has access to this transaction (own or joint account)
        require_once __DIR__ . '/../models/JointAccount.php';
        $jointAccount = new JointAccount();
        $hasAccess = ($transaction['user_id'] == $_SESSION['user_id']) || 
                     ($transaction['account_id'] && $jointAccount->userHasAccess($_SESSION['user_id'], $transaction['account_id']));
        
        if (!$hasAccess) {
            $_SESSION['error'] = 'Transaction not found';
            redirect('/dashboard');
        }
        
        include __DIR__ . '/../views/account/transaction.php';
    }
    
    public function jointApprove($requestId) {
        requireLogin();
        
        require_once __DIR__ . '/../models/JointAccount.php';
        $jointAccount = new JointAccount();
        
        // Verify user is the primary owner
        $request = $jointAccount->getPendingRequests($_SESSION['user_id']);
        $validRequest = false;
        foreach ($request as $req) {
            if ($req['id'] == $requestId) {
                $validRequest = true;
                break;
            }
        }
        
        if (!$validRequest) {
            $_SESSION['error'] = 'Request not found or you do not have permission to approve it';
            redirect('/account/joint-requests');
        }
        
        $result = $jointAccount->approveRequest($requestId, $_SESSION['user_id']);
        
        if ($result) {
            $_SESSION['success'] = 'Joint account request approved successfully';
        } else {
            $_SESSION['error'] = 'Failed to approve request';
        }
        
        redirect('/account/joint-requests');
    }
    
    public function jointReject($requestId) {
        requireLogin();
        
        require_once __DIR__ . '/../models/JointAccount.php';
        $jointAccount = new JointAccount();
        
        // Verify user is the primary owner
        $request = $jointAccount->getPendingRequests($_SESSION['user_id']);
        $validRequest = false;
        foreach ($request as $req) {
            if ($req['id'] == $requestId) {
                $validRequest = true;
                break;
            }
        }
        
        if (!$validRequest) {
            $_SESSION['error'] = 'Request not found or you do not have permission to reject it';
            redirect('/account/joint-requests');
        }
        
        $result = $jointAccount->rejectRequest($requestId, $_SESSION['user_id']);
        
        if ($result) {
            $_SESSION['success'] = 'Joint account request rejected';
        } else {
            $_SESSION['error'] = 'Failed to reject request';
        }
        
        redirect('/account/joint-requests');
    }
    
    public function jointRequests() {
        requireLogin();
        
        require_once __DIR__ . '/../models/JointAccount.php';
        $jointAccount = new JointAccount();
        $requests = $jointAccount->getPendingRequests($_SESSION['user_id']);
        
        include __DIR__ . '/../views/account/joint-requests.php';
    }
}
