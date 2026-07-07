<?php
require_once __DIR__ . '/../models/Card.php';

class CardController {
    
    public function index() {
        requireLogin();
        
        $cardModel = new Card();
        $userCards = $cardModel->getUserCards($_SESSION['user_id']);
        
        include __DIR__ . '/../views/card/index.php';
    }
    
    public function applications() {
        requireLogin();
        
        $cardModel = new Card();
        $pendingCards = $cardModel->getUserPendingCards($_SESSION['user_id']);
        $rejectedCards = $cardModel->getUserRejectedCards($_SESSION['user_id']);
        
        include __DIR__ . '/../views/card/applications.php';
    }
    
    public function view($id) {
        requireLogin();
        
        $cardModel = new Card();
        $card = $cardModel->getCardDetails($id, false);
        
        if (!$card || $card['user_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Card not found';
            redirect('/card');
        }
        
        // Get card transactions
        $transactions = $cardModel->getCardTransactions($id, 50);
        
        $data = [
            'card' => $card,
            'transactions' => $transactions
        ];
        
        include __DIR__ . '/../views/card/view.php';
    }
    
    public function create() {
        requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            requireNotRestrictedForFinancialActions();
            try {
                $data = [
                    'user_id' => $_SESSION['user_id'],
                    'account_id' => intval($_POST['account_id'] ?? 0),
                    'card_type' => Security::sanitize($_POST['card_type'] ?? ''),
                    'card_name' => Security::sanitize($_POST['card_name'] ?? ''),
                    'is_virtual' => isset($_POST['is_virtual']) ? 1 : 0,
                    'is_single_use' => isset($_POST['is_single_use']) ? 1 : 0,
                    'daily_limit' => floatval($_POST['daily_limit'] ?? 5000),
                    'monthly_limit' => floatval($_POST['monthly_limit'] ?? 50000)
                ];
                
                // Basic validation to prevent fatals and bad inserts
                if (!$data['account_id']) {
                    $_SESSION['error'] = 'Please select an account to link this card to.';
                    redirect('/card/create');
                }
                if (empty($data['card_type'])) {
                    $_SESSION['error'] = 'Please select a card type.';
                    redirect('/card/create');
                }
                if (empty($data['card_name'])) {
                    $_SESSION['error'] = 'Please enter a card name.';
                    redirect('/card/create');
                }
                
                if ($data['card_type'] === 'credit') {
                    $data['credit_limit'] = floatval($_POST['credit_limit'] ?? 10000);
                }
                
                $cardModel = new Card();
                $result = $cardModel->create($data);
                
                // Defensive: ensure we got the expected response shape
                if (is_array($result) && !empty($result['success'])) {
                    $_SESSION['success'] = 'Card application submitted successfully';
                    redirect('/card/view/' . $result['card_id']);
                }
                
                $message = (is_array($result) && !empty($result['message'])) ? $result['message'] : 'Failed to submit card application.';
                $_SESSION['error'] = $message;
                redirect('/card/create');
            } catch (\Throwable $t) {
                // Prevent white-screen 500s on production: log and show a friendly error
                error_log('Card create POST error: ' . $t->getMessage() . ' in ' . $t->getFile() . ':' . $t->getLine());
                $_SESSION['error'] = 'Something went wrong while submitting your card application. Please try again or contact support.';
                redirect('/card/create');
            }
        }
        
        // Get user accounts
        $accountModel = new Account();
        $accounts = $accountModel->getUserAccounts($_SESSION['user_id']);
        
        include __DIR__ . '/../views/card/create.php';
    }
    
    public function freeze($id) {
        requireLogin();
        requireNotRestrictedForFinancialActions();
        
        $cardModel = new Card();
        $card = $cardModel->findById($id);
        
        if (!$card || $card['user_id'] != $_SESSION['user_id']) {
            jsonResponse(['success' => false, 'message' => 'Card not found'], 404);
        }
        
        if ($cardModel->freeze($id)) {
            jsonResponse(['success' => true, 'message' => 'Card frozen successfully']);
        } else {
            jsonResponse(['success' => false, 'message' => 'Failed to freeze card'], 500);
        }
    }
    
    public function unfreeze($id) {
        requireLogin();
        requireNotRestrictedForFinancialActions();
        
        $cardModel = new Card();
        $card = $cardModel->findById($id);
        
        if (!$card || $card['user_id'] != $_SESSION['user_id']) {
            jsonResponse(['success' => false, 'message' => 'Card not found'], 404);
        }
        
        if ($cardModel->unfreeze($id)) {
            jsonResponse(['success' => true, 'message' => 'Card activated successfully']);
        } else {
            jsonResponse(['success' => false, 'message' => 'Failed to activate card'], 500);
        }
    }
    
    public function showDetails($id) {
        requireLogin();
        requireNotRestrictedForFinancialActions();
        
        $cardModel = new Card();
        $card = $cardModel->getCardDetails($id, true);
        
        if (!$card || $card['user_id'] != $_SESSION['user_id']) {
            jsonResponse(['success' => false, 'message' => 'Card not found'], 404);
        }
        
        jsonResponse([
            'success' => true,
            'card_number' => $card['card_number_decrypted'],
            'cvv' => $card['cvv_decrypted'],
            'expiry' => date('m/y', strtotime($card['expiry_date']))
        ]);
    }
    
    public function delete($id) {
        requireLogin();
        requireNotRestrictedForFinancialActions();
        
        $cardModel = new Card();
        $card = $cardModel->findById($id);
        
        if (!$card || $card['user_id'] != $_SESSION['user_id']) {
            jsonResponse(['success' => false, 'message' => 'Card not found'], 404);
        }
        
        $result = $cardModel->delete($id);
        
        if ($result['success']) {
            jsonResponse(['success' => true, 'message' => 'Card deleted successfully']);
        } else {
            jsonResponse(['success' => false, 'message' => $result['message']], 500);
        }
    }
}
