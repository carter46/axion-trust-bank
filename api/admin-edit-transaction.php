<?php
@ini_set('display_errors', 0);
@error_reporting(E_ALL);
ob_start();

try {
    require_once __DIR__ . '/../config/config.php';
    require_once __DIR__ . '/../includes/functions.php';

    $output = ob_get_clean();
    if (!empty($output)) {
        error_log('admin-edit-transaction.php: Unexpected output before headers: ' . substr($output, 0, 200));
    }

    header('Content-Type: application/json; charset=UTF-8');
    header('X-Content-Type-Options: nosniff');
} catch (Throwable $e) {
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode([
        'success' => false,
        'message' => 'Setup error: ' . $e->getMessage(),
    ]);
    exit;
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit;
}

$transactionId = (int)($input['transaction_id'] ?? 0);
$newAmount = (float)($input['amount'] ?? 0);
$newDescription = trim((string)($input['description'] ?? ''));
$newStatusUi = trim((string)($input['status'] ?? ''));
$newDate = $input['date'] ?? null;
$newCategory = trim((string)($input['category'] ?? ''));
$newExpenseCategory = array_key_exists('expense_category', $input)
    ? normalizeExpenseCategory($input['expense_category'])
    : null;
$newFee = array_key_exists('fee', $input) ? (float)$input['fee'] : null;
$newRecipientName = array_key_exists('recipient_name', $input) ? trim((string)$input['recipient_name']) : null;
$newRecipientAccount = array_key_exists('recipient_account', $input) ? trim((string)$input['recipient_account']) : null;
$newRecipientBank = array_key_exists('recipient_bank', $input) ? trim((string)$input['recipient_bank']) : null;
$transferScope = array_key_exists('transfer_scope', $input) ? trim((string)$input['transfer_scope']) : null;

if ($transactionId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Transaction ID required']);
    exit;
}

if ($newAmount <= 0) {
    echo json_encode(['success' => false, 'message' => 'Amount must be greater than 0']);
    exit;
}

if ($newDescription === '') {
    echo json_encode(['success' => false, 'message' => 'Description is required']);
    exit;
}

$validStatusesUi = ['completed', 'pending', 'failed', 'on_hold', 'processing'];
if ($newStatusUi !== '' && !in_array($newStatusUi, $validStatusesUi, true)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid status. Allowed values: ' . implode(', ', $validStatusesUi),
    ]);
    exit;
}

$newStatusDb = $newStatusUi !== '' ? adminMapTransactionStatusForDb($newStatusUi) : '';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    $sql = "SELECT t.*, u.email AS user_email
            FROM transactions t
            JOIN users u ON t.user_id = u.id
            WHERE t.id = ? AND u.role != 'admin'";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$transactionId]);
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$transaction) {
        echo json_encode(['success' => false, 'message' => 'Transaction not found']);
        exit;
    }

    $conn->beginTransaction();

    try {
        $oldAmount = (float)$transaction['amount'];
        $oldStatus = (string)($transaction['status'] ?? 'completed');
        $statusChanged = $newStatusDb !== '' && $newStatusDb !== $oldStatus;
        $formattedDate = null;

        $updateFields = ['amount = ?', 'description = ?'];
        $updateValues = [$newAmount, $newDescription];

        if ($newStatusDb !== '') {
            $updateFields[] = 'status = ?';
            $updateValues[] = $newStatusDb;

            if ($newStatusDb === 'completed') {
                $updateFields[] = 'completed_at = NOW()';
            } elseif ($oldStatus === 'completed' && $newStatusDb !== 'completed') {
                $updateFields[] = 'completed_at = NULL';
            }
        }

        if ($newDate) {
            $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', (string)$newDate);
            if (!$dateTime) {
                throw new Exception('Invalid date format. Expected: YYYY-MM-DD HH:mm:ss. Received: ' . $newDate);
            }
            $formattedDate = $dateTime->format('Y-m-d H:i:s');
            $updateFields[] = 'created_at = ?';
            $updateValues[] = $formattedDate;
        }

        if ($newCategory !== '' && in_array($newCategory, getValidStructuralCategories(), true)) {
            $updateFields[] = 'category = ?';
            $updateValues[] = $newCategory;
        }

        if (array_key_exists('expense_category', $input)) {
            if ($newExpenseCategory === null || $newExpenseCategory === '') {
                $updateFields[] = 'expense_category = NULL';
            } elseif (in_array($newExpenseCategory, getValidExpenseCategoryDbValues(), true)) {
                $updateFields[] = 'expense_category = ?';
                $updateValues[] = $newExpenseCategory;
            } else {
                $updateFields[] = 'expense_category = ?';
                $updateValues[] = 'other';
            }
        }

        if ($newFee !== null && $newFee >= 0) {
            $updateFields[] = 'fee = ?';
            $updateValues[] = $newFee;
        }

        if ($newRecipientName !== null) {
            $updateFields[] = 'recipient_name = ?';
            $updateValues[] = $newRecipientName;
        }
        if ($newRecipientAccount !== null) {
            $updateFields[] = 'recipient_account = ?';
            $updateValues[] = $newRecipientAccount;
        }
        if ($newRecipientBank !== null) {
            $updateFields[] = 'recipient_bank = ?';
            $updateValues[] = $newRecipientBank;
        }

        $existingMeta = json_decode($transaction['metadata'] ?? '{}', true);
        if (!is_array($existingMeta)) {
            $existingMeta = [];
        }
        if ($transferScope !== null && $transferScope !== '') {
            $existingMeta['transfer_scope'] = $transferScope;
        }
        if (!empty($input['metadata']) && is_array($input['metadata'])) {
            $existingMeta = array_merge($existingMeta, $input['metadata']);
        }

        $metadataJson = json_encode($existingMeta, JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
        if ($metadataJson === false) {
            throw new Exception('Failed to encode transaction metadata');
        }
        $updateFields[] = 'metadata = ?';
        $updateValues[] = $metadataJson;

        $sql = 'UPDATE transactions SET ' . implode(', ', $updateFields) . ' WHERE id = ?';
        $updateValues[] = $transactionId;

        $updateStmt = $conn->prepare($sql);
        $updateStmt->execute($updateValues);

        $verifySql = 'SELECT amount, description, status, created_at, fee FROM transactions WHERE id = ?';
        $verifyStmt = $conn->prepare($verifySql);
        $verifyStmt->execute([$transactionId]);
        $updatedTransaction = $verifyStmt->fetch(PDO::FETCH_ASSOC);

        if (!$updatedTransaction) {
            throw new Exception('Transaction not found after update');
        }

        $amountUpdated = abs((float)$updatedTransaction['amount'] - $newAmount) < 0.01;
        $descriptionUpdated = trim((string)$updatedTransaction['description']) === $newDescription;
        $statusUpdated = $newStatusDb === '' || ((string)$updatedTransaction['status'] === $newStatusDb);
        $dateUpdated = true;

        if ($formattedDate !== null) {
            $dbDate = date('Y-m-d H:i:s', strtotime((string)$updatedTransaction['created_at']));
            $dateUpdated = $dbDate === $formattedDate;
        }

        if (!$amountUpdated || !$descriptionUpdated || !$statusUpdated || !$dateUpdated) {
            throw new Exception('Update verification failed - values do not match');
        }

        $shouldSendFailedEmail = false;
        $refundAmount = 0.0;
        $transactionType = (string)($transaction['transaction_type'] ?? 'debit');
        $transactionCategory = (string)($transaction['category'] ?? '');
        $currentStatusDb = (string)($updatedTransaction['status'] ?? $oldStatus);
        $resolvedFee = $newFee !== null ? $newFee : (float)($transaction['fee'] ?? 0);

        if (!empty($transaction['account_id'])) {
            $stmt = $conn->prepare('SELECT id, balance FROM accounts WHERE id = ? FOR UPDATE');
            $stmt->execute([(int)$transaction['account_id']]);
            $accountCheck = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($accountCheck) {
                $oldTxnState = $transaction;
                $newTxnState = array_merge($transaction, [
                    'amount' => $newAmount,
                    'fee' => $resolvedFee,
                    'status' => $currentStatusDb,
                ]);

                $balanceChange = adminComputeEditBalanceDelta($oldTxnState, $newTxnState);

                if (abs($balanceChange) > 0.00001) {
                    $newBalanceAfterChange = (float)$accountCheck['balance'] + $balanceChange;
                    if ($newBalanceAfterChange < 0) {
                        throw new Exception(
                            'Cannot process: would result in negative balance (Current: '
                            . number_format((float)$accountCheck['balance'], 2)
                            . ', Change: ' . number_format($balanceChange, 2)
                            . ', Result: ' . number_format($newBalanceAfterChange, 2) . ')'
                        );
                    }

                    $wasActive = adminShouldReverseBalanceOnDelete($oldTxnState);
                    $isNowFailed = $currentStatusDb === 'failed';
                    if (
                        $transactionType === 'debit'
                        && $transactionCategory === 'transfer'
                        && $wasActive
                        && $isNowFailed
                    ) {
                        adminReverseInternalTransferPair($conn, $transaction);
                    }

                    $balanceStmt = $conn->prepare(
                        'UPDATE accounts SET balance = balance + ?, available_balance = available_balance + ?, updated_at = NOW() WHERE id = ?'
                    );
                    $balanceStmt->execute([$balanceChange, $balanceChange, (int)$transaction['account_id']]);

                    if ($transactionType === 'debit' && $balanceChange > 0 && $isNowFailed && $statusChanged) {
                        $shouldSendFailedEmail = true;
                        $refundAmount = $oldAmount;
                    }
                }

                $stmt = $conn->prepare('SELECT balance FROM accounts WHERE id = ?');
                $stmt->execute([(int)$transaction['account_id']]);
                $accountData = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($accountData) {
                    $balanceAfterStmt = $conn->prepare('UPDATE transactions SET balance_after = ? WHERE id = ?');
                    $balanceAfterStmt->execute([(float)$accountData['balance'], $transactionId]);
                }
            }
        }

        $conn->commit();

        $transactionRef = $transaction['transaction_ref'] ?? $transaction['reference_number'] ?? ('ID:' . $transactionId);
        $logMessage = "Edited transaction {$transactionRef} for user {$transaction['user_email']}. Amount changed from {$oldAmount} to {$newAmount}";
        if ($statusChanged) {
            $logMessage .= ". Status changed from {$oldStatus} to {$currentStatusDb}";
        }
        if ($formattedDate !== null) {
            $logMessage .= ". Date changed from {$transaction['created_at']} to {$formattedDate}";
        }
        logActivity((int)$_SESSION['user_id'], 'ADMIN_EDIT_TRANSACTION', $logMessage);

        if ($shouldSendFailedEmail && !empty($transaction['account_id'])) {
            try {
                $stmtUser = $conn->prepare(
                    'SELECT full_name, email, currency, currency_selection_shown, notification_preferences FROM users WHERE id = ?'
                );
                $stmtUser->execute([(int)$transaction['user_id']]);
                $userInfo = $stmtUser->fetch(PDO::FETCH_ASSOC);

                if ($userInfo) {
                    $userPrefs = json_decode($userInfo['notification_preferences'] ?? '{}', true);
                    $emailEnabled = ($userPrefs['email_notifications'] ?? true) && ($userPrefs['transaction_alerts'] ?? true);

                    if ($emailEnabled) {
                        $stmtBalance = $conn->prepare('SELECT balance, currency FROM accounts WHERE id = ?');
                        $stmtBalance->execute([(int)$transaction['account_id']]);
                        $accountData = $stmtBalance->fetch(PDO::FETCH_ASSOC);
                        $currentBalance = (float)($accountData['balance'] ?? 0);
                        $userDisplayCurrency = getUserDisplayCurrency($userInfo);
                        $txnStoredCurrency = strtoupper(trim((string)($transaction['currency'] ?? $accountData['currency'] ?? DEFAULT_CURRENCY)));
                        $transactionDescription = (string)($updatedTransaction['description'] ?? $transaction['description'] ?? 'Transaction');
                        $transactionDate = date('F j, Y g:i A', strtotime((string)($updatedTransaction['created_at'] ?? $transaction['created_at'])));

                        require_once __DIR__ . '/../includes/email-template.php';
                        $emailTemplate = new EmailTemplate();
                        $emailContent = $emailTemplate->transactionFailedEmail(
                            $userInfo['full_name'],
                            $refundAmount,
                            $userDisplayCurrency,
                            $transactionRef,
                            $transactionDescription,
                            $currentBalance,
                            $transactionDate,
                            $txnStoredCurrency,
                            $txnStoredCurrency
                        );

                        sendEmail($userInfo['email'], 'Transaction Failed - Refund Processed', $emailContent);
                    }
                }
            } catch (Throwable $e) {
                error_log('Transaction failed email notification error: ' . $e->getMessage());
            }
        }

        echo json_encode([
            'success' => true,
            'message' => 'Transaction updated successfully',
            'updated_data' => [
                'created_at' => $updatedTransaction['created_at'],
                'amount' => $updatedTransaction['amount'],
                'status' => $updatedTransaction['status'],
                'description' => $updatedTransaction['description'],
            ],
        ]);
    } catch (Throwable $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        throw $e;
    }
} catch (Throwable $e) {
    error_log('Admin Edit Transaction Error: ' . $e->getMessage());
    error_log('Transaction ID: ' . ($transactionId ?? 'N/A'));
    error_log('Input Data: ' . json_encode($input ?? []));

    http_response_code(200);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage() ?: 'An error occurred while updating the transaction',
        'error_details' => [
            'type' => get_class($e),
            'message' => $e->getMessage(),
            'file' => basename($e->getFile()),
            'line' => $e->getLine(),
        ],
    ]);
}
