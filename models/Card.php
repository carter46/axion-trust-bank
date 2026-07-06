<?php
class Card {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
        
        // Include required models
        if (!class_exists('Notification')) {
            require_once __DIR__ . '/Notification.php';
        }
        if (!class_exists('CardTransaction')) {
            require_once __DIR__ . '/CardTransaction.php';
        }
        if (!class_exists('Account')) {
            require_once __DIR__ . '/Account.php';
        }
        if (!class_exists('Transaction')) {
            require_once __DIR__ . '/Transaction.php';
        }
    }
    
    public function create($data) {
        try {
            $cardNumber = generateCardNumber();
            $cvv = generateCVV();
            $expiryDate = date('Y-m-d', strtotime('+3 years'));
            
            // Encrypt sensitive data
            $encryptedCard = encryptData($cardNumber);
            $encryptedCVV = encryptData($cvv);
            
            $sql = "INSERT INTO cards (
                        user_id, account_id, card_number, card_type, card_name, cvv, expiry_date,
                        credit_limit, available_credit, is_virtual, is_single_use, daily_limit, monthly_limit, status
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $creditLimit = $data['credit_limit'] ?? 0;
            
            $result = $this->db->query($sql, [
                $data['user_id'],
                $data['account_id'],
                $encryptedCard,
                $data['card_type'],
                $data['card_name'] ?? 'Primary Card',
                $encryptedCVV,
                $expiryDate,
                $creditLimit,
                $creditLimit, // Available credit same as limit initially
                $data['is_virtual'] ?? 0,
                $data['is_single_use'] ?? 0,
                $data['daily_limit'] ?? 5000,
                $data['monthly_limit'] ?? 50000,
                'pending' // Set status to pending for admin approval
            ]);
            
            if ($result) {
                $cardId = $this->db->lastInsertId();
                logActivity($data['user_id'], 'CARD_CREATED', "New {$data['card_type']} card created");
                
                // Send notification
                $notification = new Notification();
                $notification->create(
                    $data['user_id'],
                    'Card Application Submitted',
                    "Your {$data['card_type']} card application has been submitted and is pending admin approval.",
                    'info',
                    "/card/view/{$cardId}"
                );
                
                // Send application confirmation email (never allow this to break the request)
                try {
                    if (!class_exists('User')) {
                        require_once __DIR__ . '/User.php';
                    }
                    $userModel = new User();
                    $user = $userModel->findById($data['user_id']);
                    if ($user) {
                        require_once __DIR__ . '/../includes/email-template.php';
                        require_once __DIR__ . '/../includes/functions.php';
                        $emailTemplate = new EmailTemplate();
                        $cardEmail = $emailTemplate->cardApplicationSubmittedEmail(
                            $user['full_name'],
                            ucfirst($data['card_type']),
                            $data['card_name'] ?? 'Primary Card',
                            $cardId
                        );
                        sendEmail($user['email'], 'Card Application Received - ' . getSiteName(), $cardEmail);
                    }
                } catch (\Throwable $t) {
                    error_log("Card application email error: " . $t->getMessage());
                }
                
                return ['success' => true, 'card_id' => $cardId];
            }
            
            return ['success' => false, 'message' => 'Failed to create card'];
        } catch (\Throwable $t) {
            error_log('Card model create error: ' . $t->getMessage() . ' in ' . $t->getFile() . ':' . $t->getLine());
            return ['success' => false, 'message' => 'An error occurred while creating the card.'];
        }
    }
    
    public function findById($id) {
        $sql = "SELECT * FROM cards WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        if (!$stmt) {
            error_log("Card findById query failed for ID: $id");
            return false;
        }
        return $stmt->fetch();
    }
    
    public function getUserCards($userId) {
        $sql = "SELECT c.*, a.account_number, a.account_type, a.currency AS account_currency 
                FROM cards c 
                JOIN accounts a ON c.account_id = a.id 
                WHERE c.user_id = ? AND c.status IN ('active', 'frozen') 
                ORDER BY c.created_at DESC";
        
        $stmt = $this->db->query($sql, [$userId]);
        $cards = ($stmt && $stmt !== false) ? $stmt->fetchAll() : [];
        
        // Decrypt card numbers for display
        foreach ($cards as &$card) {
            if (!empty($card['card_number'])) {
                $card['card_number'] = decryptData($card['card_number']);
                // Remove spaces and format: XXXX XXXX XXXX XXXX
                $cardNumber = str_replace(' ', '', $card['card_number']);
                $card['card_number'] = chunk_split($cardNumber, 4, ' ');
                $card['card_number'] = trim($card['card_number']);
            }
            if (!empty($card['cvv'])) {
                $card['cvv'] = decryptData($card['cvv']);
            }
        }
        
        return $cards;
    }
    
    public function getUserPendingCards($userId) {
        $sql = "SELECT c.*, a.account_number, a.account_type 
                FROM cards c 
                JOIN accounts a ON c.account_id = a.id 
                WHERE c.user_id = ? AND c.status = 'pending' 
                ORDER BY c.created_at DESC";
        
        $stmt = $this->db->query($sql, [$userId]);
        $cards = ($stmt && $stmt !== false) ? $stmt->fetchAll() : [];
        
        // Decrypt card numbers for display (even for pending cards, in case they're approved and need display)
        foreach ($cards as &$card) {
            if (!empty($card['card_number'])) {
                $card['card_number'] = decryptData($card['card_number']);
                // Remove spaces and format: XXXX XXXX XXXX XXXX
                $cardNumber = str_replace(' ', '', $card['card_number']);
                $card['card_number'] = chunk_split($cardNumber, 4, ' ');
                $card['card_number'] = trim($card['card_number']);
            }
            if (!empty($card['cvv'])) {
                $card['cvv'] = decryptData($card['cvv']);
            }
        }
        
        return $cards;
    }
    
    public function getUserRejectedCards($userId) {
        $sql = "SELECT c.*, a.account_number, a.account_type 
                FROM cards c 
                JOIN accounts a ON c.account_id = a.id 
                WHERE c.user_id = ? AND c.status = 'rejected' 
                ORDER BY c.created_at DESC";
        
        $stmt = $this->db->query($sql, [$userId]);
        return ($stmt && $stmt !== false) ? $stmt->fetchAll() : [];
    }
    
    public function getCardDetails($cardId, $showSensitive = false) {
        $sql = "SELECT c.*, a.currency AS account_currency 
                FROM cards c 
                JOIN accounts a ON c.account_id = a.id 
                WHERE c.id = ?";
        $stmt = $this->db->query($sql, [$cardId]);
        $card = $stmt ? $stmt->fetch() : null;
        
        if (!$card) {
            return null;
        }
        
        if ($showSensitive) {
            $card['card_number_decrypted'] = decryptData($card['card_number']);
            $card['cvv_decrypted'] = decryptData($card['cvv']);
        } else {
            $card['card_number_masked'] = maskCardNumber(decryptData($card['card_number']));
            unset($card['cvv']);
        }
        
        unset($card['card_number']); // Remove encrypted version
        
        return $card;
    }
    
    public function freeze($cardId) {
        $sql = "UPDATE cards SET status = 'frozen' WHERE id = ?";
        $result = $this->db->query($sql, [$cardId]);
        
        if ($result) {
            $card = $this->findById($cardId);
            logActivity($card['user_id'], 'CARD_FROZEN', "Card ending in **** frozen");
            
            $notification = new Notification();
            $notification->create(
                $card['user_id'],
                'Card Frozen',
                'Your card has been temporarily frozen.',
                'warning',
                "/card/view/{$cardId}"
            );
        }
        
        return $result;
    }
    
    public function unfreeze($cardId) {
        $sql = "UPDATE cards SET status = 'active' WHERE id = ?";
        $result = $this->db->query($sql, [$cardId]);
        
        if ($result) {
            $card = $this->findById($cardId);
            logActivity($card['user_id'], 'CARD_UNFROZEN', "Card ending in **** unfrozen");
            
            $notification = new Notification();
            $notification->create(
                $card['user_id'],
                'Card Activated',
                'Your card has been activated and is ready to use.',
                'success',
                "/card/view/{$cardId}"
            );
        }
        
        return $result;
    }
    
    public function approve($cardId) {
        // First get the card details before updating
        $card = $this->findById($cardId);
        if (!$card) {
            return false;
        }
        
        // Set up financial details based on card type
        $balance = 0;
        $availableCredit = 0;
        $creditLimit = 0;
        
        if ($card['card_type'] === 'debit') {
            // For debit cards, get balance from linked account
            $accountModel = new Account();
            $account = $accountModel->findById($card['account_id']);
            $balance = $account ? $account['balance'] : 0;
            $availableCredit = 0; // Debit cards don't have credit
            $creditLimit = 0; // Debit cards don't have credit limit
        } elseif ($card['card_type'] === 'credit') {
            // For credit cards, set up credit limits (funding from bank)
            $creditLimit = $card['credit_limit'] ?? 10000; // Default credit limit
            $balance = 0; // Credit cards start with 0 balance (no money owed yet)
            $availableCredit = $creditLimit; // Available credit equals limit initially
        } elseif ($card['card_type'] === 'prepaid') {
            // For prepaid cards, user needs to load money first
            $balance = 0; // Prepaid cards start empty until loaded
            $availableCredit = 0; // Prepaid cards don't have credit
            $creditLimit = 0; // Prepaid cards don't have credit limit
        } else {
            // Virtual cards - similar to debit but no physical card
            $accountModel = new Account();
            $account = $accountModel->findById($card['account_id']);
            $balance = $account ? $account['balance'] : 0;
            $availableCredit = 0;
            $creditLimit = 0;
        }
        
        // Update card with proper financial details
        $sql = "UPDATE cards SET 
                    status = 'active', 
                    balance = ?, 
                    available_credit = ?,
                    credit_limit = ?,
                    daily_limit = COALESCE(daily_limit, 5000),
                    monthly_limit = COALESCE(monthly_limit, 50000)
                WHERE id = ? AND status = 'pending'";
        
        $result = $this->db->query($sql, [$balance, $availableCredit, $creditLimit, $cardId]);
        
        if ($result) {
            // Create initial transaction record for card activation
            $this->createCardActivationTransaction($cardId, $card);
            
            logActivity($card['user_id'], 'CARD_APPROVED', "Card application approved");
            
            $notification = new Notification();
            $notification->create(
                $card['user_id'],
                'Card Approved',
                'Your card application has been approved!',
                'success',
                "/card/view/{$cardId}"
            );
            
            // Send approval email
            try {
                if (!class_exists('User')) {
                    require_once __DIR__ . '/User.php';
                }
                $userModel = new User();
                $user = $userModel->findById($card['user_id']);
                if ($user) {
                    require_once __DIR__ . '/../includes/email-template.php';
                    require_once __DIR__ . '/../includes/functions.php';
                    $emailTemplate = new EmailTemplate();
                    $cardEmail = $emailTemplate->cardApprovedEmail(
                        $user['full_name'],
                        ucfirst($card['card_type']),
                        $card['card_name'] ?? 'Primary Card'
                    );
                    sendEmail($user['email'], 'Card Application Approved - ' . getSiteName(), $cardEmail);
                }
            } catch (Exception $e) {
                error_log("Card approval email error: " . $e->getMessage());
            }
        }
        
        return $result;
    }
    
    private function createCardActivationTransaction($cardId, $card) {
        try {
            $transactionModel = new Transaction();
            $cardTransactionModel = new CardTransaction();
            
            if ($card['card_type'] === 'debit') {
                // For debit cards, create account transaction (funding from account)
                $transactionData = [
                    'user_id' => $card['user_id'],
                    'account_id' => $card['account_id'],
                    'transaction_type' => 'debit',
                    'category' => 'card_activation',
                    'amount' => 0, // No money moved, just card activation
                    'description' => 'Debit card activated and linked to account',
                    'status' => 'completed'
                ];
                $transactionModel->create($transactionData);
                
                // Create card transaction record
                $cardTransactionData = [
                    'card_id' => $cardId,
                    'user_id' => $card['user_id'],
                    'transaction_type' => 'card_activation',
                    'amount' => 0,
                    'description' => 'Debit card activated',
                    'status' => 'completed'
                ];
                $cardTransactionModel->create($cardTransactionData);
                
            } elseif ($card['card_type'] === 'credit') {
                // For credit cards, create card transaction (bank funding)
                $cardTransactionData = [
                    'card_id' => $cardId,
                    'user_id' => $card['user_id'],
                    'transaction_type' => 'credit_limit_approved',
                    'amount' => $card['credit_limit'] ?? 10000,
                    'description' => 'Credit limit approved by bank',
                    'status' => 'completed'
                ];
                $cardTransactionModel->create($cardTransactionData);
                
            } elseif ($card['card_type'] === 'prepaid') {
                // For prepaid cards, create activation record
                $cardTransactionData = [
                    'card_id' => $cardId,
                    'user_id' => $card['user_id'],
                    'transaction_type' => 'card_activation',
                    'amount' => 0,
                    'description' => 'Prepaid card activated - ready for funding',
                    'status' => 'completed'
                ];
                $cardTransactionModel->create($cardTransactionData);
            }
        } catch (Exception $e) {
            error_log("Failed to create card activation transaction: " . $e->getMessage());
        }
    }
    
    public function reject($cardId, $reason = null) {
        $sql = "UPDATE cards SET status = 'rejected' WHERE id = ? AND status = 'pending'";
        $result = $this->db->query($sql, [$cardId]);
        
        if ($result) {
            $card = $this->findById($cardId);
            if ($card) {
                logActivity($card['user_id'], 'CARD_REJECTED', "Card application rejected");
                
                $notification = new Notification();
                $notification->create(
                    $card['user_id'],
                    'Card Rejected',
                    'Your card application has been rejected.',
                    'error',
                    "/card/applications"
                );
                
                // Send rejection email
                try {
                    if (!class_exists('User')) {
                        require_once __DIR__ . '/User.php';
                    }
                    $userModel = new User();
                    $user = $userModel->findById($card['user_id']);
                    if ($user) {
                        require_once __DIR__ . '/../includes/email-template.php';
                        require_once __DIR__ . '/../includes/functions.php';
                        $emailTemplate = new EmailTemplate();
                        $cardEmail = $emailTemplate->cardRejectedEmail(
                            $user['full_name'],
                            ucfirst($card['card_type']),
                            $reason ?? 'Application did not meet our current card issuance criteria'
                        );
                        sendEmail($user['email'], 'Card Application Update - ' . getSiteName(), $cardEmail);
                    }
                } catch (Exception $e) {
                    error_log("Card rejection email error: " . $e->getMessage());
                }
            }
        }
        
        return $result;
    }
    
    public function block($cardId, $reason = null) {
        $sql = "UPDATE cards SET status = 'blocked' WHERE id = ?";
        $result = $this->db->query($sql, [$cardId]);
        
        if ($result) {
            $card = $this->findById($cardId);
            logActivity($card['user_id'], 'CARD_BLOCKED', "Card blocked" . ($reason ? ": $reason" : ""));
        }
        
        return $result;
    }
    
    public function updateLimits($cardId, $dailyLimit, $monthlyLimit) {
        $sql = "UPDATE cards SET daily_limit = ?, monthly_limit = ? WHERE id = ?";
        return $this->db->query($sql, [$dailyLimit, $monthlyLimit, $cardId]);
    }
    
    public function refreshCVV($cardId) {
        $newCVV = generateCVV();
        $encryptedCVV = encryptData($newCVV);
        
        $sql = "UPDATE cards SET cvv = ? WHERE id = ?";
        $result = $this->db->query($sql, [$encryptedCVV, $cardId]);
        
        if ($result) {
            $card = $this->findById($cardId);
            logActivity($card['user_id'], 'CVV_REFRESHED', "Card CVV refreshed for security");
        }
        
        return $result;
    }
    
    public function getCardTransactions($cardId, $limit = 50) {
        $cardTransaction = new CardTransaction();
        return $cardTransaction->getCardTransactions($cardId, $limit);
    }
    
    public function delete($cardId) {
        // First check if card exists and belongs to user
        $card = $this->findById($cardId);
        if (!$card) {
            return ['success' => false, 'message' => 'Card not found'];
        }
        
        // Soft delete by setting status to cancelled
        $sql = "UPDATE cards SET status = 'cancelled', updated_at = NOW() WHERE id = ?";
        $result = $this->db->query($sql, [$cardId]);
        
        if ($result) {
            logActivity($card['user_id'], 'CARD_DELETED', "Card ending in **** deleted");
            
            $notification = new Notification();
            $notification->create(
                $card['user_id'],
                'Card Deleted',
                'Your card has been permanently deleted.',
                'warning',
                "/card"
            );
            
            return ['success' => true, 'message' => 'Card deleted successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to delete card'];
    }
}
