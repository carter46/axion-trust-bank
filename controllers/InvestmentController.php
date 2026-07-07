<?php
class InvestmentController {
    
    public function index() {
        requireLogin();
        
        // Get user investment statistics
        $userModel = new User();
        $user = $userModel->findById($_SESSION['user_id']);
        $investmentBalance = (float)($user['investment_balance'] ?? 0);
        
        $userInvestmentModel = new UserInvestment();
        $userInvestments = $userInvestmentModel->getUserInvestments($_SESSION['user_id']);
        
        // Calculate totals
        $totalInvested = 0;
        $totalROIEarned = 0;
        $totalCurrentValue = 0;
        $activeInvestments = 0;
        
        foreach ($userInvestments as $inv) {
            $totalInvested += (float)$inv['amount_principal'];
            $totalROIEarned += (float)($inv['current_accrued'] ?? 0);
            $totalCurrentValue += (float)($userInvestmentModel->getCurrentValue($inv));
            if ($inv['status'] === 'active') {
                $activeInvestments++;
            }
        }
        
        // Check KYC status
        $userModel = new User();
        $user = $userModel->findById($_SESSION['user_id']);
        $kycVerified = ($user['kyc_status'] ?? '') === 'verified';
        
        $productModel = new InvestmentProduct();
        
        // Get filters
        $filters = [
            'type' => $_GET['type'] ?? null,
            'search' => $_GET['search'] ?? null,
            'sort' => $_GET['sort'] ?? 'newest',
            'status' => 'active'
        ];
        
        $products = $productModel->getAll($filters);
        
        // Group by type
        $groupedProducts = [
            'stocks' => [],
            'forex' => [],
            'crypto' => []
        ];
        
        foreach ($products as $product) {
            $groupedProducts[$product['type']][] = $product;
        }
        
        // Pass statistics to view
        $investmentStats = [
            'investment_balance' => $investmentBalance,
            'total_invested' => $totalInvested,
            'total_roi_earned' => $totalROIEarned,
            'total_current_value' => $totalCurrentValue,
            'active_investments' => $activeInvestments,
            'total_investments' => count($userInvestments)
        ];
        
        // Get user accounts for withdrawal/funding modals
        $accountModel = new Account();
        $userAccounts = $accountModel->getUserAccounts($_SESSION['user_id']);
        
        // Variables are available directly in view
        include __DIR__ . '/../views/investment/index.php';
    }
    
    public function view($id) {
        requireLogin();
        
        // Check KYC status
        $userModel = new User();
        $user = $userModel->findById($_SESSION['user_id']);
        $kycVerified = ($user['kyc_status'] ?? '') === 'verified';
        
        $productModel = new InvestmentProduct();
        
        // Try slug first, then ID
        $product = $productModel->findBySlug($id);
        if (!$product) {
            $product = $productModel->findById($id);
        }
        
        if (!$product || $product['status'] !== 'active') {
            $_SESSION['error'] = 'Investment product not found';
            redirect('/investment');
        }
        
        // No need to fetch accounts - investments use investment balance only
        
        // Calculate remaining capacity
        $remainingCapacity = $productModel->getRemainingCapacity($product['id']);
        
        // Get user's total investment in this product
        $userInvestmentModel = new UserInvestment();
        $userTotalInvested = $userInvestmentModel->getUserProductTotal($_SESSION['user_id'], $product['id']);
        
        // Parse ROI config
        $roiConfig = json_decode($product['roi_config'], true);
        $dailyROI = $productModel->getDailyROI($product);
        $annualROI = $productModel->getAnnualROI($product);
        
        // Variables are available directly in view
        include __DIR__ . '/../views/investment/view.php';
    }
    
    public function myInvestments() {
        requireLogin();
        
        $userId = $_SESSION['user_id'] ?? null;
        
        // Debug: Log session info
        error_log("InvestmentController::myInvestments - Session user_id: " . ($userId ?? 'NULL') . ", Type: " . gettype($userId));
        error_log("InvestmentController::myInvestments - Full session: " . print_r($_SESSION, true));
        
        if (!$userId) {
            error_log("ERROR: No user_id in session!");
            $_SESSION['error'] = 'Please log in to view your investments';
            redirect('/auth/login');
            return;
        }
        
        $userInvestmentModel = new UserInvestment();
        $investments = $userInvestmentModel->getUserInvestments($userId);
        
        // Debug: Log the result
        error_log("InvestmentController::myInvestments - User ID: {$userId}, Investments count: " . count($investments));
        if (count($investments) > 0) {
            error_log("InvestmentController::myInvestments - First investment: " . print_r($investments[0], true));
        }
        
        // Calculate current values
        if (!empty($investments)) {
            foreach ($investments as &$investment) {
                $investment['current_value'] = $userInvestmentModel->getCurrentValue($investment);
            }
        }
        
        // Variables are available directly in view
        include __DIR__ . '/../views/investment/my-investments.php';
    }
    
    public function invest() {
        requireLogin();
        requireNotRestrictedForFinancialActions();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/investment');
        }
        
        // Check KYC
        $userModel = new User();
        $user = $userModel->findById($_SESSION['user_id']);
        
        if (($user['kyc_status'] ?? '') !== 'verified') {
            $_SESSION['error'] = 'Please complete KYC verification before investing';
            redirect('/profile/kyc');
        }
        
        // Validate CSRF
        if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid request. Please try again.';
            redirect('/investment');
        }
        
        $productId = intval($_POST['product_id'] ?? 0);
        $amount = floatval($_POST['amount'] ?? 0);
        $durationDays = intval($_POST['duration_days'] ?? 0);
        
        // Validate inputs (no account_id needed)
        if ($productId <= 0 || $amount <= 0 || $durationDays <= 0) {
            $_SESSION['error'] = 'Please fill in all required fields';
            redirect('/investment/view/' . $productId);
        }
        
        $userModel = new User();
        $user = $userModel->findById($_SESSION['user_id']);

        // Get product
        $productModel = new InvestmentProduct();
        $product = $productModel->findById($productId);
        
        if (!$product || $product['status'] !== 'active') {
            $_SESSION['error'] = 'Investment product not available';
            redirect('/investment');
        }
        
        // Validate amount
        if ($amount < (float)$product['min_amount']) {
            $_SESSION['error'] = 'Amount is below minimum investment of ' . formatInvestmentAmountForUser($product['min_amount'], $user);
            redirect('/investment/view/' . $productId);
        }
        
        if ($product['max_amount'] && $amount > (float)$product['max_amount']) {
            $_SESSION['error'] = 'Amount exceeds maximum investment of ' . formatInvestmentAmountForUser($product['max_amount'], $user);
            redirect('/investment/view/' . $productId);
        }
        
        // Validate duration
        if ($durationDays < (int)$product['min_duration_days']) {
            $_SESSION['error'] = 'Duration is below minimum of ' . $product['min_duration_days'] . ' days';
            redirect('/investment/view/' . $productId);
        }
        
        if ($product['max_duration_days'] && $durationDays > (int)$product['max_duration_days']) {
            $_SESSION['error'] = 'Duration exceeds maximum of ' . $product['max_duration_days'] . ' days';
            redirect('/investment/view/' . $productId);
        }
        
        // Check capacity
        $remainingCapacity = $productModel->getRemainingCapacity($productId);
        if ($remainingCapacity !== null && $amount > $remainingCapacity) {
            $_SESSION['error'] = 'Insufficient product capacity. Remaining: ' . formatInvestmentAmountForUser($remainingCapacity, $user);
            redirect('/investment/view/' . $productId);
        }
        
        // Check per-user limit
        $userInvestmentModel = new UserInvestment();
        $userTotal = $userInvestmentModel->getUserProductTotal($_SESSION['user_id'], $productId);
        if ($product['per_user_max'] && ($userTotal + $amount) > (float)$product['per_user_max']) {
            $_SESSION['error'] = 'Investment would exceed your maximum limit of ' . formatInvestmentAmountForUser($product['per_user_max'], $user);
            redirect('/investment/view/' . $productId);
        }
        
        // Check investment balance (not bank account)
        $investmentBalance = (float)($user['investment_balance'] ?? 0);
        
        if ($investmentBalance < $amount) {
            $_SESSION['error'] = 'Insufficient investment balance. You have ' . formatUserInvestmentBalanceForUser($investmentBalance, $user) . ' available. Please fund your investment account first.';
            redirect('/investment/view/' . $productId);
        }
        
        // Create investment (no account_id needed - uses investment balance only)
        $result = $userInvestmentModel->create([
            'user_id' => $_SESSION['user_id'],
            'product_id' => $productId,
            'amount_principal' => $amount,
            'duration_days' => $durationDays,
            'account_used_id' => null // No bank account needed
        ]);
        
        if ($result['success']) {
            // Send notification
            // Get user's preferred currency for notification
            require_once __DIR__ . '/../models/User.php';
            $userModel = new User();
            $user = $userModel->findById($_SESSION['user_id']);
            $userCurrency = getUserDisplayCurrency($user);
            
            $notification = new Notification();
            $notification->create(
                $_SESSION['user_id'],
                'Investment Created',
                "Your investment of " . formatAmountForUser($amount, $user, DEFAULT_CURRENCY) . " in {$product['title']} has been created successfully.",
                'success',
                '/investment/my-investments'
            );
            
            // Send email using HTML template
            try {
                require_once __DIR__ . '/../includes/email-template.php';
                require_once __DIR__ . '/../includes/system-settings.php';
                $emailTemplate = new EmailTemplate();
                $systemSettings = SystemSettings::getInstance();
                $siteName = $systemSettings->get('site_name', getSiteName());
                
                $subject = "Investment Created - {$siteName}";
                $maturityDate = date('F j, Y', strtotime("+{$durationDays} days"));
                
                $content = "<h2>Investment Successfully Created</h2>";
                $content .= "<p>Hello {$user['full_name']},</p>";
                $content .= "<p>Your investment has been successfully created. Here are the details:</p>";
                $content .= "<div style='background: #f9fafb; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
                $content .= "<p><strong>Product:</strong> {$product['title']}</p>";
                $content .= "<p><strong>Amount:</strong> " . formatInvestmentAmountForUser($amount, $user) . "</p>";
                $content .= "<p><strong>Duration:</strong> {$durationDays} days</p>";
                $content .= "<p><strong>Expected Maturity Date:</strong> {$maturityDate}</p>";
                $content .= "</div>";
                $content .= "<p>You can track your investment progress in your dashboard.</p>";
                $content .= "<p>Thank you for investing with us!</p>";
                
                $htmlMessage = $emailTemplate->render($subject, $content);
                // Send as HTML email (isHTML = true by default)
                sendEmail($user['email'], $subject, $htmlMessage, true);
            } catch (Exception $e) {
                error_log("Investment email error: " . $e->getMessage());
            }
            
            $_SESSION['success'] = 'Investment created successfully!';
            redirect('/investment/my-investments');
        } else {
            $_SESSION['error'] = $result['message'] ?? 'Failed to create investment';
            redirect('/investment/view/' . $productId);
        }
    }
    
    public function fund() {
        requireLogin();
        requireNotRestrictedForFinancialActions();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
                $_SESSION['error'] = 'Invalid request. Please try again.';
                redirect('/investment');
            }
            
            require_once __DIR__ . '/../models/InvestmentFunding.php';
            $fundingModel = new InvestmentFunding();
            
            // Extract crypto currency from funding_method if it's crypto
            $fundingMethod = Security::sanitize($_POST['funding_method']);
            $cryptoCurrency = '';
            if (strpos($fundingMethod, 'crypto_') === 0) {
                $cryptoCurrency = strtoupper(str_replace('crypto_', '', $fundingMethod));
            }
            
            // Validate amount
            $amount = floatval($_POST['amount']);
            if ($amount <= 0) {
                $_SESSION['error'] = 'Invalid amount. Please enter a positive number.';
                redirect('/investment');
                return;
            }
            
            // Validate account belongs to user if bank funding
            $accountId = null;
            if ($fundingMethod === 'bank_balance') {
                $accountId = !empty($_POST['account_id']) ? intval($_POST['account_id']) : null;
                if (empty($accountId)) {
                    $_SESSION['error'] = 'Please select an account for bank funding.';
                    redirect('/investment');
                    return;
                }
                
                // Verify account belongs to user
                require_once __DIR__ . '/../models/Account.php';
                $accountModel = new Account();
                $account = $accountModel->findById($accountId);
                if (!$account || $account['user_id'] != $_SESSION['user_id']) {
                    $_SESSION['error'] = 'Invalid account selected.';
                    redirect('/investment');
                    return;
                }
            }
            
            $result = $fundingModel->create([
                'user_id' => $_SESSION['user_id'],
                'amount' => $amount,
                'funding_method' => $fundingMethod,
                'account_id' => $accountId,
                'crypto_currency' => $cryptoCurrency ?: null,
                'crypto_address' => Security::sanitize($_POST['crypto_address'] ?? ''),
                'notes' => Security::sanitize($_POST['notes'] ?? '')
            ]);
            
            if ($result['success']) {
                if (isset($result['requires_crypto_payment']) && $result['requires_crypto_payment']) {
                    $_SESSION['funding_id'] = $result['funding_id'];
                    redirect('/investment/fund-crypto/' . $result['funding_id']);
                } else {
                    $_SESSION['success'] = 'Investment account funded successfully!';
                    redirect('/investment');
                }
            } else {
                $_SESSION['error'] = $result['message'] ?? 'Failed to process funding request';
                redirect('/investment');
            }
        }
        redirect('/investment');
    }
    
    public function fundCrypto($fundingId) {
        requireLogin();
        requireNotRestrictedForFinancialActions();
        
        // Handle TX hash submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
                $_SESSION['error'] = 'Invalid request. Please try again.';
                redirect('/investment/fund-crypto/' . $fundingId);
                return;
            }
            
            require_once __DIR__ . '/../config/database.php';
            require_once __DIR__ . '/../includes/security.php';
            require_once __DIR__ . '/../models/InvestmentFunding.php';
            $fundingModel = new InvestmentFunding();
            $funding = $fundingModel->findById(intval($_POST['funding_id'] ?? $fundingId));
            
            if ($funding && $funding['user_id'] == $_SESSION['user_id']) {
                $txHash = Security::sanitize($_POST['tx_hash'] ?? '');
                
                if (!empty($txHash)) {
                    // Update funding with TX hash
                    $sql = "UPDATE investment_funding SET crypto_tx_hash = ?, notes = CONCAT(COALESCE(notes, ''), '\nTransaction Hash: ', ?) WHERE id = ?";
                    $db = Database::getInstance();
                    $db->query($sql, [$txHash, $txHash, $funding['id']]);
                    $_SESSION['success'] = 'Transaction hash submitted successfully. Your payment is now pending admin verification. You can track it in your investment transaction history.';
                    
                    // Create a transaction record for tracking
                    require_once __DIR__ . '/../models/Transaction.php';
                    $transactionModel = new Transaction();
                    $transactionModel->create([
                        'user_id' => $_SESSION['user_id'],
                        'account_id' => null, // No bank account for crypto
                        'transaction_type' => 'credit',
                        'category' => 'investment_funding',
                        'amount' => $funding['amount'],
                        'description' => 'Crypto Investment Funding - ' . strtoupper($funding['crypto_currency'] ?? '') . ' - Hash: ' . substr($txHash, 0, 16) . '...',
                        'status' => 'pending',
                        'transaction_ref' => 'CRYPTO-' . $funding['id']
                    ]);
                } else {
                    $_SESSION['error'] = 'Please enter a transaction hash.';
                    redirect('/investment/fund-crypto/' . $fundingId);
                    return;
                }
                
                // Stay on the funding page to show success message
                redirect('/investment/fund-crypto/' . $fundingId);
                return;
            } else {
                $_SESSION['error'] = 'Funding request not found or unauthorized.';
                redirect('/investment');
                return;
            }
        }
        
        require_once __DIR__ . '/../models/InvestmentFunding.php';
        $fundingModel = new InvestmentFunding();
        $funding = $fundingModel->findById($fundingId);
        
        if (!$funding || $funding['user_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Funding request not found';
            redirect('/investment');
        }
        
        require_once __DIR__ . '/../models/CryptoWallet.php';
        $cryptoWalletModel = new CryptoWallet();
        $cryptoType = str_replace('crypto_', '', $funding['funding_method']);
        $wallet = $cryptoWalletModel->getActiveWallet($cryptoType);
        
        if (!$wallet) {
            $_SESSION['error'] = 'Crypto wallet not configured. Please contact support.';
            redirect('/investment');
        }
        
        // Variables available in view
        include __DIR__ . '/../views/investment/fund-crypto.php';
    }
    
    public function withdraw() {
        requireLogin();
        requireNotRestrictedForFinancialActions();
        
        // Check KYC requirement
        $userModel = new User();
        $user = $userModel->findById($_SESSION['user_id']);
        
        require_once __DIR__ . '/../includes/system-settings.php';
        $systemSettings = SystemSettings::getInstance();
        
        if ($systemSettings->isKYCRequired() && ($user['kyc_status'] ?? '') !== 'verified') {
            $_SESSION['error'] = 'Please complete KYC verification before making withdrawals';
            redirect('/profile/kyc');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
                $_SESSION['error'] = 'Invalid request. Please try again.';
                redirect('/investment');
            }
            
            require_once __DIR__ . '/../models/InvestmentWithdrawal.php';
            $withdrawalModel = new InvestmentWithdrawal();
            
            // Prepare recipient info based on method
            $recipientInfo = [];
            $accountId = null;
            
            if ($_POST['withdrawal_method'] === 'bank_balance') {
                $accountId = intval($_POST['account_id']);
                $recipientInfo = ['account_id' => $accountId];
            } elseif ($_POST['withdrawal_method'] === 'external_account') {
                $recipientInfo = [
                    'account_number' => Security::sanitize($_POST['account_number'] ?? ''),
                    'routing_number' => Security::sanitize($_POST['routing_number'] ?? ''),
                    'account_name' => Security::sanitize($_POST['account_name'] ?? ''),
                    'bank_name' => Security::sanitize($_POST['bank_name'] ?? '')
                ];
            } elseif ($_POST['withdrawal_method'] === 'paypal') {
                $recipientInfo = ['email' => Security::sanitize($_POST['paypal_email'] ?? '')];
            } elseif ($_POST['withdrawal_method'] === 'venmo') {
                $recipientInfo = ['phone' => Security::sanitize($_POST['venmo_phone'] ?? '')];
            } else { // Crypto
                $recipientInfo = [
                    'address' => Security::sanitize($_POST['crypto_address'] ?? ''),
                    'network' => Security::sanitize($_POST['crypto_network'] ?? '')
                ];
            }
            
            $result = $withdrawalModel->create([
                'user_id' => $_SESSION['user_id'],
                'amount' => floatval($_POST['amount']),
                'withdrawal_method' => Security::sanitize($_POST['withdrawal_method']),
                'recipient_type' => Security::sanitize($_POST['recipient_type'] ?? ''),
                'recipient_info' => $recipientInfo,
                'account_id' => $accountId,
                'notes' => Security::sanitize($_POST['notes'] ?? '')
            ]);
            
            if ($result['success']) {
                if (isset($result['requires_processing']) && $result['requires_processing']) {
                    $_SESSION['success'] = 'Withdrawal request submitted. Admin will process it shortly.';
                } else {
                    $_SESSION['success'] = 'Withdrawal completed successfully!';
                }
                redirect('/investment');
            } else {
                $_SESSION['error'] = $result['message'] ?? 'Failed to process withdrawal request';
                redirect('/investment');
            }
        }
        redirect('/investment');
    }
    
    public function transactions() {
        requireLogin();
        
        $userId = $_SESSION['user_id'];
        require_once __DIR__ . '/../models/InvestmentFunding.php';
        require_once __DIR__ . '/../models/Transaction.php';
        require_once __DIR__ . '/../models/InvestmentTransaction.php';
        
        $fundingModel = new InvestmentFunding();
        $transactionModel = new Transaction();
        $investmentTransactionModel = new InvestmentTransaction();
        
        // Get investment funding history - automatically filters out pending crypto without hash
        $fundingHistory = $fundingModel->getUserFundingWithHashFilter($userId, 100);
        
        // Get investment transactions (investments, ROI, withdrawals)
        $investmentTransactions = $investmentTransactionModel->getByUser($userId, 100);
        
        // Get regular transactions related to investments
        $bankTransactions = $transactionModel->getUserTransactions($userId, [
            'category' => 'investment_funding'
        ]);
        
        // Combine and sort all transactions
        $allTransactions = [];
        
        // Ensure arrays are not null
        $fundingHistory = $fundingHistory ?? [];
        $investmentTransactions = $investmentTransactions ?? [];
        $bankTransactions = $bankTransactions ?? [];
        
        foreach ($fundingHistory as $funding) {
            $allTransactions[] = [
                'type' => 'funding',
                'date' => $funding['created_at'],
                'amount' => $funding['amount'],
                'status' => $funding['status'],
                'method' => $funding['funding_method'],
                'crypto_currency' => $funding['crypto_currency'],
                'crypto_tx_hash' => $funding['crypto_tx_hash'],
                'description' => 'Investment Funding - ' . ($funding['funding_method'] === 'bank_balance' ? 'Bank Transfer' : strtoupper($funding['crypto_currency'] ?? '')),
                'id' => $funding['id']
            ];
        }
        
        // Add investment transactions (ROI, withdrawals, etc.)
        foreach ($investmentTransactions as $invTxn) {
            // Use product title if available, otherwise use description
            $description = $invTxn['description'] ?? '';
            
            // If description contains "Product ID" and we have product_title, replace it
            if (!empty($invTxn['product_title']) && strpos($description, 'Product ID') !== false) {
                // Replace "Product ID X" with product title
                $description = preg_replace('/Product ID \d+/', $invTxn['product_title'], $description);
            } elseif (!empty($invTxn['product_title']) && empty($description)) {
                // If no description but we have product title, use it
                $txnType = $invTxn['type'] ?? '';
                if ($txnType === 'accrual') {
                    $description = "ROI Accrual - {$invTxn['product_title']}";
                } elseif ($txnType === 'payout') {
                    $description = "ROI Payout - {$invTxn['product_title']}";
                } else {
                    $description = "Investment - {$invTxn['product_title']}";
                }
            } elseif (empty($description)) {
                // Fallback if no description
                $txnType = $invTxn['type'] ?? '';
                $description = $txnType === 'roi' ? 'ROI Payment' : 'Investment Transaction';
            }
            
            $allTransactions[] = [
                'type' => 'investment',
                'date' => $invTxn['created_at'] ?? $invTxn['transaction_date'],
                'amount' => $invTxn['amount'] ?? $invTxn['roi_amount'] ?? 0,
                'status' => 'completed',
                'transaction_type' => $invTxn['type'] === 'credit' || $invTxn['type'] === 'payout' ? 'credit' : 'debit',
                'description' => $description,
                'reference' => $invTxn['reference'] ?? 'INV-' . ($invTxn['id'] ?? ''),
                'id' => $invTxn['id']
            ];
        }
        
        // Add bank transactions related to investments
        foreach ($bankTransactions as $txn) {
            $allTransactions[] = [
                'type' => 'bank_transaction',
                'date' => $txn['created_at'],
                'amount' => $txn['amount'],
                'status' => $txn['status'],
                'transaction_type' => $txn['transaction_type'],
                'description' => $txn['description'],
                'reference' => $txn['transaction_ref'],
                'id' => $txn['id']
            ];
        }
        
        // Sort by date (newest first)
        usort($allTransactions, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        
        include __DIR__ . '/../views/investment/transactions.php';
    }
}

