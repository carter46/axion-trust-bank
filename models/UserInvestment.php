<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/InvestmentProduct.php';
require_once __DIR__ . '/Account.php';
require_once __DIR__ . '/Transaction.php';
require_once __DIR__ . '/InvestmentTransaction.php';
require_once __DIR__ . '/User.php';
require_once __DIR__ . '/Notification.php';

class UserInvestment {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // Create investment
    public function create($data) {
        try {
            // Start transaction
            $this->db->beginTransaction();
            
            // Calculate maturity date
            $startDate = $data['start_date'] ?? date('Y-m-d');
            $maturityDate = date('Y-m-d', strtotime($startDate . ' + ' . $data['duration_days'] . ' days'));
            
            // Get product to calculate effective ROI and get product name
            $productModel = new InvestmentProduct();
            $product = $productModel->findById($data['product_id']);
            $dailyROI = $product ? $productModel->getDailyROI($product) : 0;
            $productTitle = $product ? ($product['title'] ?? 'Unknown Product') : 'Unknown Product';
            
            // Handle account_used_id - it's NOT NULL with FOREIGN KEY constraint
            // Since investments use investment_balance (not bank accounts), we need to get a valid account
            $accountId = $data['account_used_id'] ?? null;
            
            if ($accountId === null) {
                // Get user's first active account as fallback (for foreign key constraint)
                $accountModel = new Account();
                $userAccounts = $accountModel->getUserAccounts($data['user_id']);
                
                if (!empty($userAccounts)) {
                    // Use first active account
                    $accountId = $userAccounts[0]['id'];
                    error_log("UserInvestment::create - No account provided, using user's first account ID: {$accountId}");
                } else {
                    // No account exists - this is a problem since foreign key requires valid account
                    // We'll need to create a placeholder or handle this differently
                    // For now, throw an error explaining the issue
                    throw new Exception('Investment requires a bank account. Please create an account first, or the system needs to be configured to allow investments without accounts.');
                }
            }
            
            $sql = "INSERT INTO user_investments (
                user_id, product_id, amount_principal, duration_days,
                start_date, maturity_date, status, daily_percent_effective,
                account_used_id
            ) VALUES (?, ?, ?, ?, ?, ?, 'pending', ?, ?)";
            
            // Log the data being inserted
            error_log("UserInvestment::create - Attempting to insert investment");
            error_log("UserInvestment::create - Final Data: " . json_encode([
                'user_id' => $data['user_id'],
                'product_id' => $data['product_id'],
                'amount_principal' => $data['amount_principal'],
                'duration_days' => $data['duration_days'],
                'start_date' => $startDate,
                'maturity_date' => $maturityDate,
                'daily_percent_effective' => $dailyROI,
                'account_used_id' => $accountId
            ]));
            
            // Prepare parameters with proper types
            $params = [
                (int)$data['user_id'],
                (int)$data['product_id'],
                (float)$data['amount_principal'],
                (int)$data['duration_days'],
                $startDate,
                $maturityDate,
                $dailyROI === null ? null : (float)$dailyROI,
                (int)$accountId
            ];
            
            error_log("UserInvestment::create - Executing INSERT with params: " . json_encode($params));
            
            // Execute query with proper error handling
            try {
                $stmt = $this->db->query($sql, $params);
            } catch (Exception $e) {
                error_log("UserInvestment::create - Exception during query: " . $e->getMessage());
                error_log("UserInvestment::create - Exception trace: " . $e->getTraceAsString());
                throw $e;
            }
            
            // Check if query failed
            if (!$stmt) {
                $conn = $this->db->getConnection();
                $errorInfo = $conn->errorInfo();
                $stmtErrorInfo = null; // Can't get errorInfo from null statement
                
                $errorMsg = 'Unknown error';
                if ($errorInfo && isset($errorInfo[2]) && $errorInfo[2]) {
                    $errorMsg = $errorInfo[2];
                } elseif ($stmtErrorInfo && isset($stmtErrorInfo[2]) && $stmtErrorInfo[2]) {
                    $errorMsg = $stmtErrorInfo[2];
                }
                
                error_log("UserInvestment::create - Query FAILED");
                error_log("UserInvestment::create - Connection Error Info: " . print_r($errorInfo, true));
                error_log("UserInvestment::create - Statement Error Info: " . print_r($stmtErrorInfo, true));
                error_log("UserInvestment::create - SQL: " . $sql);
                error_log("UserInvestment::create - Params: " . json_encode($params));
                error_log("UserInvestment::create - Param types: " . json_encode([
                    'user_id' => gettype($params[0]) . '=' . $params[0],
                    'product_id' => gettype($params[1]) . '=' . $params[1],
                    'amount_principal' => gettype($params[2]) . '=' . $params[2],
                    'duration_days' => gettype($params[3]) . '=' . $params[3],
                    'start_date' => gettype($params[4]) . '=' . $params[4],
                    'maturity_date' => gettype($params[5]) . '=' . $params[5],
                    'daily_percent_effective' => gettype($params[6]) . '=' . $params[6],
                    'account_used_id' => gettype($params[7]) . '=' . $params[7]
                ]));
                
                throw new Exception('Database query failed: ' . $errorMsg);
            }
            
            $investmentId = $this->db->lastInsertId();
            
            error_log("UserInvestment::create - lastInsertId returned: " . ($investmentId ?: 'FALSE/EMPTY'));
            
            if (!$investmentId) {
                $errorInfo = $this->db->errorInfo();
                error_log("UserInvestment::create - Failed to get insert ID. Error info: " . print_r($errorInfo, true));
                throw new Exception('Failed to create investment record. Insert ID not returned. Database error: ' . ($errorInfo[2] ?? 'Unknown error'));
            }
            
            // Deduct from user's investment balance ONLY (no bank account fallback)
            // Fetch user balance inside transaction with FOR UPDATE to avoid race conditions
            $sql = "SELECT investment_balance FROM users WHERE id = ? FOR UPDATE";
            $stmt = $this->db->query($sql, [$data['user_id']]);
            $user = $stmt ? $stmt->fetch() : null;
            
            if (!$user) {
                throw new Exception('User not found');
            }
            
            $investmentBalance = (float)($user['investment_balance'] ?? 0);
            $amount = (float)$data['amount_principal'];
            
            // Validate investment balance is sufficient (double-check inside transaction)
            if ($investmentBalance < $amount) {
                require_once __DIR__ . '/../includes/functions.php';
                throw new Exception('Insufficient investment balance. Available: ' . formatUserInvestmentBalanceForUser($investmentBalance, $user) . ', Required: ' . formatInvestmentAmountForUser($amount, $user));
            }
            
            // Deduct from investment balance
            $newInvestmentBalance = $investmentBalance - $amount;
            $sql = "UPDATE users SET investment_balance = ? WHERE id = ?";
            $updateResult = $this->db->query($sql, [$newInvestmentBalance, $data['user_id']]);
            
            if (!$updateResult) {
                throw new Exception('Failed to update investment balance');
            }
            
            $balanceBefore = $investmentBalance;
            $balanceAfter = $newInvestmentBalance;
            
            // Create transaction record
            $transactionModel = new InvestmentTransaction();
            $transactionModel->create([
                'user_id' => $data['user_id'],
                'user_investment_id' => $investmentId,
                'type' => 'debit',
                'amount' => $data['amount_principal'],
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'reference' => 'INV-' . $investmentId,
                'description' => "Investment in {$productTitle}"
            ]);
            
            // Create transaction record in main transactions table (no bank account)
            $mainTransaction = new Transaction();
            $transactionResult = $mainTransaction->create([
                'user_id' => $data['user_id'],
                'account_id' => null, // No bank account - investment balance only
                'transaction_type' => 'debit',
                'category' => 'investment',
                'amount' => $data['amount_principal'],
                'description' => "Investment in {$productTitle}",
                'status' => 'completed',
                'balance_before' => $balanceBefore, // Investment balance before
                'balance_after' => $balanceAfter   // Investment balance after
            ]);
            
            // Log if transaction creation fails (non-critical)
            if (!$transactionResult || (is_array($transactionResult) && !$transactionResult['success'])) {
                error_log("Warning: Failed to create transaction record for investment {$investmentId}");
            }
            
            // Activate investment
            $activated = $this->activate($investmentId);
            if (!$activated) {
                throw new Exception('Failed to activate investment');
            }
            
            $this->db->commit();
            
            // Debug: Log successful creation
            error_log("UserInvestment Created Successfully - ID: {$investmentId}, User: {$data['user_id']}, Status: active");
            
            return ['success' => true, 'investment_id' => $investmentId];
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("UserInvestment Create Error: " . $e->getMessage());
            error_log("UserInvestment Create Error Trace: " . $e->getTraceAsString());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Activate investment
    public function activate($investmentId) {
        $sql = "UPDATE user_investments SET status = 'active' WHERE id = ?";
        return $this->db->query($sql, [$investmentId]);
    }
    
    // Find by ID
    public function findById($id) {
        $sql = "SELECT ui.*, ip.title as product_title, ip.type as product_type, ip.payout_type, ip.roi_config
                FROM user_investments ui
                JOIN investment_products ip ON ui.product_id = ip.id
                WHERE ui.id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt ? $stmt->fetch() : null;
    }
    
    // Get user investments
    public function getUserInvestments($userId) {
        // Convert userId to integer to ensure proper matching
        $userId = (int)$userId;
        
        // First, check if ANY investments exist for this user (simple query)
        $checkSql = "SELECT COUNT(*) as count FROM user_investments WHERE user_id = ?";
        $checkStmt = $this->db->query($checkSql, [$userId]);
        $checkResult = $checkStmt ? $checkStmt->fetch() : null;
        $totalCount = $checkResult ? (int)$checkResult['count'] : 0;
        
        error_log("UserInvestment::getUserInvestments - User ID: {$userId} (type: " . gettype($userId) . "), Total investments in DB: {$totalCount}");
        
        // If no investments found, check if user_id column type matches
        if ($totalCount == 0) {
            // Check what user_ids exist in the table
            $allUsersSql = "SELECT DISTINCT user_id FROM user_investments LIMIT 10";
            $allUsersStmt = $this->db->query($allUsersSql);
            $allUsers = $allUsersStmt ? $allUsersStmt->fetchAll() : [];
            if (!empty($allUsers)) {
                $userIds = array_column($allUsers, 'user_id');
                error_log("UserInvestment::getUserInvestments - Sample user_ids in table: " . implode(', ', $userIds));
            }
        }
        
        // Use LEFT JOIN to ensure investments are returned even if product is deleted
        $sql = "SELECT ui.*, 
                       COALESCE(ip.title, 'Unknown Product') as product_title, 
                       COALESCE(ip.type, 'unknown') as product_type, 
                       ip.slug as product_slug,
                       ip.payout_type, 
                       ip.roi_config
                FROM user_investments ui
                LEFT JOIN investment_products ip ON ui.product_id = ip.id
                WHERE ui.user_id = ?
                ORDER BY ui.created_at DESC";
        
        $stmt = $this->db->query($sql, [$userId]);
        
        // Check for query errors
        if (!$stmt) {
            $error = $this->db->errorInfo();
            error_log("UserInvestment::getUserInvestments - Query ERROR: " . print_r($error, true));
            return [];
        }
        
        $result = $stmt->fetchAll();
        
        // Debug logging
        error_log("UserInvestment::getUserInvestments - User ID: {$userId}, Query returned: " . count($result) . " investments");
        if (count($result) > 0) {
            error_log("Sample investment: ID=" . ($result[0]['id'] ?? 'N/A') . ", Status=" . ($result[0]['status'] ?? 'N/A') . ", Product Title: " . ($result[0]['product_title'] ?? 'N/A') . ", Product ID: " . ($result[0]['product_id'] ?? 'N/A'));
        } else if ($totalCount > 0) {
            error_log("WARNING: Found {$totalCount} investments in database but JOIN query returned 0 results. Possible JOIN issue.");
            // Try without JOIN as fallback
            $simpleSql = "SELECT ui.*, 'Unknown Product' as product_title, 'unknown' as product_type 
                         FROM user_investments ui 
                         WHERE ui.user_id = ? 
                         ORDER BY ui.created_at DESC";
            $simpleStmt = $this->db->query($simpleSql, [$userId]);
            if ($simpleStmt) {
                $simpleResult = $simpleStmt->fetchAll();
                error_log("Simple query (no JOIN) returned: " . count($simpleResult) . " investments");
                if (count($simpleResult) > 0) {
                    // Return simple result with placeholder product info
                    foreach ($simpleResult as &$inv) {
                        $inv['product_title'] = 'Unknown Product';
                        $inv['product_type'] = 'unknown';
                        $inv['product_slug'] = null;
                        $inv['payout_type'] = null;
                        $inv['roi_config'] = null;
                    }
                    return $simpleResult;
                }
            }
        }
        
        return $result;
    }
    
    // Get active investments for accrual (that haven't been accrued today)
    public function getActiveInvestmentsForAccrual($date) {
        $sql = "SELECT ui.*, ip.title as product_title, ip.type as product_type, 
                       ip.payout_type, ip.roi_config
                FROM user_investments ui
                JOIN investment_products ip ON ui.product_id = ip.id
                WHERE ui.status = 'active'
                AND ui.start_date <= ?
                AND ui.maturity_date > ?
                AND (ui.last_accrual_date IS NULL OR ui.last_accrual_date < ?)";
        $stmt = $this->db->query($sql, [$date, $date, $date]);
        return $stmt ? $stmt->fetchAll() : [];
    }
    
    // Get matured investments
    public function getMaturedInvestments($date) {
        $sql = "SELECT ui.*, ip.title as product_title, ip.type as product_type,
                       ip.payout_type, ip.roi_config
                FROM user_investments ui
                JOIN investment_products ip ON ui.product_id = ip.id
                WHERE ui.status = 'active'
                AND ui.maturity_date <= ?";
        $stmt = $this->db->query($sql, [$date]);
        return $stmt ? $stmt->fetchAll() : [];
    }
    
    // Accrue daily ROI
    public function accrueROI($investmentId, $date = null) {
        $investment = $this->findById($investmentId);
        if (!$investment) {
            return false;
        }
        
        $productModel = new InvestmentProduct();
        $dailyROI = $productModel->getDailyROI($investment);
        $dailyAmount = ((float)$investment['amount_principal'] * (float)$dailyROI) / 100;
        
        // Update accrued ROI (using database column name: current_accrued)
        $newAccrued = (float)($investment['current_accrued'] ?? 0) + $dailyAmount;
        $sql = "UPDATE user_investments SET 
                current_accrued = ?,
                last_accrual_date = ?
                WHERE id = ?";
        
        $this->db->query($sql, [$newAccrued, $date, $investmentId]);
        
        // Handle payout based on payout_type
        // Note: Database enum only has: compound_daily, simple_daily, payout_at_maturity
        if ($investment['payout_type'] === 'simple_daily') {
            // Credit user investment balance immediately
            $this->payoutROI($investmentId, $dailyAmount, $date);
        } else {
            // Compound or payout at maturity - just accrue
            // If compound, update principal for next day's calculation
            if ($investment['payout_type'] === 'compound_daily') {
                $newPrincipal = (float)$investment['amount_principal'] + $dailyAmount;
                $sql = "UPDATE user_investments SET amount_principal = ? WHERE id = ?";
                $this->db->query($sql, [$newPrincipal, $investmentId]);
            }
        }
        
        // Log transaction
        $transactionModel = new InvestmentTransaction();
        $transactionModel->create([
            'user_id' => $investment['user_id'],
            'user_investment_id' => $investmentId,
            'type' => 'accrual',
            'amount' => $dailyAmount,
            'balance_before' => $investment['current_accrued'] ?? 0,
            'balance_after' => $newAccrued,
            'reference' => 'ROI-ACCRUAL-' . ($date ?? date('Y-m-d')),
            'description' => "Daily ROI accrual for investment #{$investmentId}"
        ]);
        
        return true;
    }
    
    // Get current value of investment
    public function getCurrentValue($investment) {
        $principal = (float)$investment['amount_principal'];
        $accrued = (float)($investment['current_accrued'] ?? 0);
        return $principal + $accrued;
    }
    
    // Payout ROI to user investment balance (not main account)
    public function payoutROI($investmentId, $amount, $date = null) {
        $investment = $this->findById($investmentId);
        if (!$investment) {
            return false;
        }
        
        // Get user's current investment balance
        $userModel = new User();
        $user = $userModel->findById($investment['user_id']);
        if (!$user) {
            return false;
        }
        
        $currentBalance = (float)($user['investment_balance'] ?? 0);
        $newBalance = $currentBalance + (float)$amount;
        
        // Update user's investment balance
        $sql = "UPDATE users SET investment_balance = ? WHERE id = ?";
        $this->db->query($sql, [$newBalance, $investment['user_id']]);
        
        // Update total ROI paid in investment record
        $sql = "UPDATE user_investments SET 
                total_roi_paid = total_roi_paid + ?
                WHERE id = ?";
        $this->db->query($sql, [$amount, $investmentId]);
        
        // Log transaction
        $transactionModel = new InvestmentTransaction();
        $transactionModel->create([
            'user_id' => $investment['user_id'],
            'user_investment_id' => $investmentId,
            'type' => 'payout',
            'amount' => $amount,
            'balance_before' => $currentBalance,
            'balance_after' => $newBalance,
            'reference' => 'ROI-PAYOUT-' . ($date ?? date('Y-m-d')),
            'description' => "ROI payout for investment #{$investmentId}"
        ]);
        
        return true;
    }
    
    // Process maturity
    public function processMaturity($investmentId) {
        $investment = $this->findById($investmentId);
        if (!$investment) {
            return false;
        }
        
        $productModel = new InvestmentProduct();
        $product = $productModel->findById($investment['product_id']);
        
        $this->db->beginTransaction();
        
        try {
            // Get user
            $userModel = new User();
            $user = $userModel->findById($investment['user_id']);
            $currentInvestmentBalance = (float)($user['investment_balance'] ?? 0);
            
            // Calculate total to payout
            $accruedROI = (float)($investment['current_accrued'] ?? 0);
            
            // Handle payout type
            if ($product['payout_type'] === 'payout_at_maturity') {
                // Payout both principal and ROI to investment balance
                $totalPayout = (float)$investment['amount_principal'] + $accruedROI;
                $newBalance = $currentInvestmentBalance + $totalPayout;
                
                // Update investment balance
                $sql = "UPDATE users SET investment_balance = ? WHERE id = ?";
                $this->db->query($sql, [$newBalance, $investment['user_id']]);
                
                // Log transaction for principal return (if it was deducted from balance)
                if ($accruedROI > 0) {
                    $this->payoutROI($investmentId, $accruedROI, date('Y-m-d'));
                }
                
                // Log principal return
                $transactionModel = new InvestmentTransaction();
                $transactionModel->create([
                    'user_id' => $investment['user_id'],
                    'user_investment_id' => $investmentId,
                    'type' => 'payout',
                    'amount' => (float)$investment['amount_principal'],
                    'balance_before' => $currentInvestmentBalance,
                    'balance_after' => $newBalance,
                    'reference' => 'MATURITY-PRINCIPAL-' . date('Y-m-d'),
                    'description' => "Principal return on maturity - Investment #{$investmentId}"
                ]);
            } else {
                // For simple_daily and compound_daily, principal already returned or stays invested
                // Just payout any remaining accrued ROI
                if ($accruedROI > 0) {
                    $this->payoutROI($investmentId, $accruedROI, date('Y-m-d'));
                }
                
                // For compound, principal may have grown
                if ($product['payout_type'] === 'compound_daily') {
                    $principalPayout = (float)$investment['amount_principal'];
                    $newBalance = $currentInvestmentBalance + $principalPayout;
                    $sql = "UPDATE users SET investment_balance = ? WHERE id = ?";
                    $this->db->query($sql, [$newBalance, $investment['user_id']]);
                    
                    // Log principal return
                    $transactionModel = new InvestmentTransaction();
                    $transactionModel->create([
                        'user_id' => $investment['user_id'],
                        'user_investment_id' => $investmentId,
                        'type' => 'payout',
                        'amount' => $principalPayout,
                        'balance_before' => $currentInvestmentBalance,
                        'balance_after' => $newBalance,
                        'reference' => 'MATURITY-PRINCIPAL-' . date('Y-m-d'),
                        'description' => "Principal return on maturity (compound) - Investment #{$investmentId}"
                    ]);
                }
            }
            
            // Mark as matured
            $sql = "UPDATE user_investments SET status = 'matured' WHERE id = ?";
            $this->db->query($sql, [$investmentId]);
            
            // Send notification
            try {
                $notificationModel = new Notification();
                $notificationModel->create(
                    $investment['user_id'],
                    'Investment Matured',
                    "Your investment in {$product['title']} has matured.",
                    'success',
                    '/investments/my-investments'
                );
            } catch (Exception $e) {
                error_log("Notification error: " . $e->getMessage());
            }
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Process Maturity Error: " . $e->getMessage());
            return false;
        }
    }
    
    // Get total invested by user for a product
    public function getUserProductTotal($userId, $productId) {
        $sql = "SELECT COALESCE(SUM(amount_principal), 0) as total 
                FROM user_investments 
                WHERE user_id = ? AND product_id = ? AND status IN ('active', 'matured')";
        $stmt = $this->db->query($sql, [$userId, $productId]);
        $result = $stmt ? $stmt->fetch() : null;
        return $result ? (float)$result['total'] : 0;
    }
    
    // Get current value (calculated)
    // Note: This method already exists above, but keeping for reference
}
