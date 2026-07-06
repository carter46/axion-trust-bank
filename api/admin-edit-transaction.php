<?php
// Prevent any output before JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);
ob_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Clear any accidental output
ob_end_clean();

header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit;
}

$transactionId = intval($input['transaction_id'] ?? 0);
$newAmount = floatval($input['amount'] ?? 0);
$newDescription = trim($input['description'] ?? '');
$newStatus = trim($input['status'] ?? '');
$newDate = $input['date'] ?? null; // Format: 'YYYY-MM-DD HH:mm:ss'

// Validate
if (!$transactionId) {
    echo json_encode(['success' => false, 'message' => 'Transaction ID required']);
    exit;
}

if ($newAmount <= 0) {
    echo json_encode(['success' => false, 'message' => 'Amount must be greater than 0']);
    exit;
}

if (empty($newDescription)) {
    echo json_encode(['success' => false, 'message' => 'Description is required']);
    exit;
}

// Validate status
$validStatuses = ['completed', 'pending', 'failed', 'on_hold', 'processing'];
if (!empty($newStatus) && !in_array($newStatus, $validStatuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status. Allowed values: ' . implode(', ', $validStatuses)]);
    exit;
}

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    // Check if transaction exists - use direct PDO for consistency
    $sql = "SELECT t.*, u.email as user_email FROM transactions t 
            JOIN users u ON t.user_id = u.id 
            WHERE t.id = ? AND u.role != 'admin'";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$transactionId]);
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$transaction) {
        echo json_encode(['success' => false, 'message' => 'Transaction not found']);
        exit;
    }
    
    // Start transaction
    $conn->beginTransaction();
    
    try {
        $oldAmount = floatval($transaction['amount']);
        $oldStatus = $transaction['status'] ?? 'completed';
        $amountDifference = $newAmount - $oldAmount;
        $statusChanged = !empty($newStatus) && $newStatus !== $oldStatus;
        
        // Build UPDATE query
        $updateFields = [];
        $updateValues = [];
        
        $updateFields[] = "amount = ?";
        $updateValues[] = $newAmount;
        
        $updateFields[] = "description = ?";
        $updateValues[] = $newDescription;
        
        // Update status if provided
        if (!empty($newStatus)) {
            $updateFields[] = "status = ?";
            $updateValues[] = $newStatus;
            
            // Update completed_at based on status
            if ($newStatus === 'completed') {
                $updateFields[] = "completed_at = NOW()";
            } elseif ($oldStatus === 'completed' && $newStatus !== 'completed') {
                // If changing from completed to another status, clear completed_at
                $updateFields[] = "completed_at = NULL";
            }
        }
        
        // Update created_at if date is provided
        if ($newDate) {
            // Validate date format
            $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $newDate);
            if (!$dateTime) {
                throw new Exception('Invalid date format. Expected: YYYY-MM-DD HH:mm:ss. Received: ' . $newDate);
            }
            $formattedDate = $dateTime->format('Y-m-d H:i:s');
            $updateFields[] = "created_at = ?";
            $updateValues[] = $formattedDate;
        }
        
        // Build and execute UPDATE query
        $sql = "UPDATE transactions SET " . implode(', ', $updateFields) . " WHERE id = ?";
        $updateValues[] = $transactionId;
        
        // Use direct PDO connection for more reliable execution
        $updateStmt = $conn->prepare($sql);
        $updateResult = $updateStmt->execute($updateValues);
        
        if (!$updateResult) {
            $errorInfo = $updateStmt->errorInfo();
            throw new Exception('Failed to execute UPDATE query: ' . ($errorInfo[2] ?? 'Unknown error'));
        }
        
        // Verify the update by fetching the record
        $verifySql = "SELECT amount, description, status, created_at FROM transactions WHERE id = ?";
        $verifyStmt = $conn->prepare($verifySql);
        $verifyStmt->execute([$transactionId]);
        $updatedTransaction = $verifyStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$updatedTransaction) {
            throw new Exception('Transaction not found after update');
        }
        
        // Check if update was successful
        $amountUpdated = abs(floatval($updatedTransaction['amount']) - $newAmount) < 0.01;
        $descriptionUpdated = trim($updatedTransaction['description']) === trim($newDescription);
        $statusUpdated = empty($newStatus) || ($updatedTransaction['status'] === $newStatus);
        $dateUpdated = true;
        
        if ($newDate) {
            $dbDate = date('Y-m-d H:i:s', strtotime($updatedTransaction['created_at']));
            $dateUpdated = $dbDate === $formattedDate;
        }
        
        if (!$amountUpdated || !$descriptionUpdated || !$statusUpdated || !$dateUpdated) {
            throw new Exception('Update verification failed - values do not match');
        }
        
        // Initialize variables for email notification
        $shouldSendFailedEmail = false;
        $refundAmount = 0;
        $transactionType = $transaction['transaction_type'] ?? 'debit';
        
        // Handle balance updates based on status changes and amount changes
        if (!empty($transaction['account_id'])) {
            // Verify account still exists
            $sql = "SELECT id, balance FROM accounts WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$transaction['account_id']]);
            $accountCheck = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($accountCheck) {
                $transactionCategory = $transaction['category'] ?? '';
                $currentStatus = $updatedTransaction['status'];
                $wasCompleted = ($oldStatus === 'completed');
                $wasPending = ($oldStatus === 'pending' || $oldStatus === 'processing' || $oldStatus === 'on_hold');
                $wasFailed = ($oldStatus === 'failed');
                $isNowCompleted = ($currentStatus === 'completed');
                $isNowFailed = ($currentStatus === 'failed');
                
                // Calculate balance change needed
                $balanceChange = 0;
                
                // If status changed to completed, apply the transaction
                if (!$wasCompleted && $isNowCompleted) {
                    // Transaction is now completing - apply it
                    $balanceChange = ($transactionType === 'credit') ? $newAmount : -$newAmount;
                }
                // If status changed from completed/pending to failed, reverse the transaction
                // (Both completed and pending transactions affect balance in this system)
                elseif (($wasCompleted || $wasPending) && $isNowFailed) {
                    // Transaction was affecting balance but is now failed - reverse it
                    $balanceChange = ($transactionType === 'credit') ? -$oldAmount : $oldAmount;
                    
                    // Mark that we should send failed email notification (for debit transactions with refund)
                    if ($transactionType === 'debit' && $balanceChange > 0) {
                        $shouldSendFailedEmail = true;
                        $refundAmount = $oldAmount;
                    }
                }
                // If status changed from failed to completed/pending, apply the transaction
                elseif ($wasFailed && ($isNowCompleted || $currentStatus === 'pending' || $currentStatus === 'processing' || $currentStatus === 'on_hold')) {
                    // Transaction was failed but is now active - apply it
                    $balanceChange = ($transactionType === 'credit') ? $newAmount : -$newAmount;
                }
                // If amount changed and transaction was/is completed or pending
                elseif ($amountDifference != 0 && (($wasCompleted || $wasPending || $isNowCompleted || $currentStatus === 'pending') && !$isNowFailed)) {
                    // Adjust balance for the amount difference
                    $balanceChange = ($transactionType === 'credit') ? $amountDifference : -$amountDifference;
                }
                
                // Apply balance changes
                if ($balanceChange != 0) {
                    // Improved negative balance check: Check for any scenario that would result in negative balance
                    $newBalanceAfterChange = floatval($accountCheck['balance']) + $balanceChange;
                    if ($newBalanceAfterChange < 0) {
                        throw new Exception('Cannot process: would result in negative balance (Current: ' . number_format($accountCheck['balance'], 2) . ', Change: ' . number_format($balanceChange, 2) . ', Result: ' . number_format($newBalanceAfterChange, 2) . ')');
                    }
                    
                    // Handle internal transfer recipient reversal (if sender's transaction is being marked as failed)
                    // When a sender's debit transaction is marked as failed, the recipient's credit transaction should also be reversed
                    if ($transactionType === 'debit' && $transactionCategory === 'transfer' && ($wasCompleted || $wasPending) && $isNowFailed) {
                        // Try to find the corresponding recipient credit transaction
                        $recipientAccountNumber = $transaction['recipient_account'] ?? null;
                        if ($recipientAccountNumber) {
                            // Get sender's account number for matching
                            $sqlSenderAccount = "SELECT account_number FROM accounts WHERE id = ?";
                            $stmtSenderAccount = $conn->prepare($sqlSenderAccount);
                            $stmtSenderAccount->execute([$transaction['account_id']]);
                            $senderAccount = $stmtSenderAccount->fetch(PDO::FETCH_ASSOC);
                            $senderAccountNumber = $senderAccount['account_number'] ?? null;
                            
                            if ($senderAccountNumber) {
                                // Find recipient's credit transaction that matches this transfer
                                // Recipient transaction has: account_number = recipient's account, recipient_account = sender's account number
                                $sqlRecipient = "SELECT t.* 
                                               FROM transactions t 
                                               JOIN accounts a ON t.account_id = a.id 
                                               WHERE a.account_number = ? 
                                               AND t.transaction_type = 'credit' 
                                               AND t.category = 'transfer' 
                                               AND t.status IN ('completed', 'pending', 'processing', 'on_hold')
                                               AND t.recipient_account = ? 
                                               AND t.created_at BETWEEN DATE_SUB(?, INTERVAL 5 MINUTE) AND DATE_ADD(?, INTERVAL 5 MINUTE)
                                               AND ABS(t.amount - ?) < 0.01
                                               ORDER BY t.created_at DESC 
                                               LIMIT 1";
                                // Use transaction created_at as reference point for matching
                                $createdAt = $transaction['created_at'];
                                
                                $stmtRecipient = $conn->prepare($sqlRecipient);
                                $stmtRecipient->execute([
                                    $recipientAccountNumber, // Recipient's account number
                                    $senderAccountNumber,    // Sender's account number (stored in recipient transaction's recipient_account field)
                                    $createdAt,
                                    $createdAt,
                                    $oldAmount
                                ]);
                                $recipientTransaction = $stmtRecipient->fetch(PDO::FETCH_ASSOC);
                                
                                if ($recipientTransaction) {
                                    // Reverse the recipient's credit transaction
                                    $recipientAccountId = $recipientTransaction['account_id'];
                                    $recipientBalanceChange = -$recipientTransaction['amount']; // Negative because it was a credit
                                    
                                    // Check current recipient account balance
                                    $sqlRecipientAccount = "SELECT balance FROM accounts WHERE id = ?";
                                    $stmtRecipientAccount = $conn->prepare($sqlRecipientAccount);
                                    $stmtRecipientAccount->execute([$recipientAccountId]);
                                    $recipientAccountInfo = $stmtRecipientAccount->fetch(PDO::FETCH_ASSOC);
                                    
                                    if ($recipientAccountInfo) {
                                        $recipientCurrentBalance = floatval($recipientAccountInfo['balance']);
                                        $recipientNewBalance = $recipientCurrentBalance + $recipientBalanceChange;
                                        
                                        if ($recipientNewBalance < 0) {
                                            // Log warning but don't block - recipient might have spent the money
                                            error_log("Warning: Reversing recipient transaction would result in negative balance. Recipient Transaction ID: {$recipientTransaction['id']}, Current Balance: {$recipientCurrentBalance}, Change: {$recipientBalanceChange}");
                                        }
                                        
                                        // Update recipient account balance
                                        $sqlRecipientUpdate = "UPDATE accounts SET 
                                                              balance = balance + ?, 
                                                              available_balance = available_balance + ?,
                                                              updated_at = NOW()
                                                              WHERE id = ?";
                                        $recipientBalanceStmt = $conn->prepare($sqlRecipientUpdate);
                                        $recipientBalanceStmt->execute([$recipientBalanceChange, $recipientBalanceChange, $recipientAccountId]);
                                        
                                        // Mark recipient transaction as failed
                                        $sqlRecipientStatus = "UPDATE transactions SET status = 'failed', completed_at = NULL WHERE id = ?";
                                        $recipientStatusStmt = $conn->prepare($sqlRecipientStatus);
                                        $recipientStatusStmt->execute([$recipientTransaction['id']]);
                                        
                                        // Update recipient transaction balance_after
                                        $sqlRecipientBalance = "SELECT balance FROM accounts WHERE id = ?";
                                        $stmtRecipientBalance = $conn->prepare($sqlRecipientBalance);
                                        $stmtRecipientBalance->execute([$recipientAccountId]);
                                        $recipientAccountData = $stmtRecipientBalance->fetch(PDO::FETCH_ASSOC);
                                        if ($recipientAccountData) {
                                            $sqlRecipientBalanceAfter = "UPDATE transactions SET balance_after = ? WHERE id = ?";
                                            $recipientBalanceAfterStmt = $conn->prepare($sqlRecipientBalanceAfter);
                                            $recipientBalanceAfterStmt->execute([$recipientAccountData['balance'], $recipientTransaction['id']]);
                                        }
                                        
                                        error_log("Admin Edit Transaction: Reversed recipient transaction ID {$recipientTransaction['id']} for internal transfer reversal");
                                    }
                                } else {
                                    // Log but don't fail - recipient transaction might not exist or might have been deleted
                                    error_log("Admin Edit Transaction: Could not find matching recipient transaction for internal transfer. Sender Transaction ID: {$transactionId}, Recipient Account: {$recipientAccountNumber}");
                                }
                            }
                        }
                    }
                    
                    // Update account balance
                    $sql = "UPDATE accounts SET 
                            balance = balance + ?, 
                            available_balance = available_balance + ?,
                            updated_at = NOW()
                            WHERE id = ?";
                    $balanceStmt = $conn->prepare($sql);
                    $balanceStmt->execute([$balanceChange, $balanceChange, $transaction['account_id']]);
                }
                
                // Always update balance_after to keep it accurate (even if no balance change occurred)
                // This ensures balance_after reflects the current account balance after any status changes
                $sql = "SELECT balance FROM accounts WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$transaction['account_id']]);
                $accountData = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($accountData) {
                    $newBalance = $accountData['balance'];
                    $sql = "UPDATE transactions SET balance_after = ? WHERE id = ?";
                    $balanceAfterStmt = $conn->prepare($sql);
                    $balanceAfterStmt->execute([$newBalance, $transactionId]);
                }
            }
        }
        
        // Commit transaction
        $conn->commit();
        
        // Log activity
        $transactionRef = $transaction['transaction_ref'] ?? $transaction['reference_number'] ?? 'ID:' . $transactionId;
        $logMessage = "Edited transaction {$transactionRef} for user {$transaction['user_email']}. " .
            "Amount changed from {$oldAmount} to {$newAmount}";
        if (!empty($newStatus) && $statusChanged) {
            $logMessage .= ". Status changed from {$oldStatus} to {$newStatus}";
        }
        if ($newDate) {
            $logMessage .= ". Date changed from {$transaction['created_at']} to {$formattedDate}";
        }
        logActivity($_SESSION['user_id'], 'ADMIN_EDIT_TRANSACTION', $logMessage);
        
        // Send email notification if transaction was marked as failed and refund was processed
        if ($shouldSendFailedEmail && $statusChanged && !empty($transaction['account_id'])) {
            try {
                // Get user info with currency and notification preferences
                $sqlUser = "SELECT full_name, email, currency, currency_selection_shown, notification_preferences FROM users WHERE id = ?";
                $stmtUser = $conn->prepare($sqlUser);
                $stmtUser->execute([$transaction['user_id']]);
                $userInfo = $stmtUser->fetch(PDO::FETCH_ASSOC);
                
                if ($userInfo) {
                    // Parse notification preferences
                    $userPrefs = json_decode($userInfo['notification_preferences'] ?? '{}', true);
                    
                    // Check if email notifications are enabled (default to true if not set)
                    $emailEnabled = ($userPrefs['email_notifications'] ?? true) && 
                                   ($userPrefs['transaction_alerts'] ?? true);
                    
                    if ($emailEnabled) {
                        // Get current account balance
                        $sqlBalance = "SELECT balance, currency FROM accounts WHERE id = ?";
                        $stmtBalance = $conn->prepare($sqlBalance);
                        $stmtBalance->execute([$transaction['account_id']]);
                        $accountData = $stmtBalance->fetch(PDO::FETCH_ASSOC);
                        $currentBalance = $accountData['balance'] ?? 0;
                        
                        $userDisplayCurrency = getUserDisplayCurrency($userInfo);
                        $txnStoredCurrency = strtoupper(trim($transaction['currency'] ?? $accountData['currency'] ?? DEFAULT_CURRENCY));
                        
                        // Get transaction description
                        $transactionDescription = $updatedTransaction['description'] ?? 
                                                 $transaction['description'] ?? 
                                                 'Transaction';
                        
                        // Format date
                        $transactionDate = date('F j, Y g:i A', strtotime($updatedTransaction['created_at'] ?? $transaction['created_at']));
                        
                        // Send email using email template
                        require_once __DIR__ . '/../includes/email-template.php';
                        require_once __DIR__ . '/../includes/functions.php';
                        
                        $emailTemplate = new EmailTemplate();
                        $emailContent = $emailTemplate->transactionFailedEmail(
                            $userInfo['full_name'],
                            $refundAmount, // Amount that was refunded
                            $userDisplayCurrency,
                            $transactionRef,
                            $transactionDescription,
                            $currentBalance,
                            $transactionDate,
                            $txnStoredCurrency,
                            $txnStoredCurrency
                        );
                        
                        sendEmail(
                            $userInfo['email'],
                            'Transaction Failed - Refund Processed',
                            $emailContent
                        );
                    }
                }
            } catch (Exception $e) {
                // Log email error but don't fail the transaction update
                error_log("Transaction failed email notification error: " . $e->getMessage());
            }
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Transaction updated successfully',
            'updated_data' => [
                'created_at' => $updatedTransaction['created_at'],
                'amount' => $updatedTransaction['amount'],
                'status' => $updatedTransaction['status'],
                'description' => $updatedTransaction['description']
            ]
        ]);
        
    } catch (Exception $e) {
        $conn->rollBack();
        throw $e;
    }
    
} catch (Exception $e) {
    error_log('Admin Edit Transaction Error: ' . $e->getMessage());
    error_log('Transaction ID: ' . ($transactionId ?? 'N/A'));
    error_log('Input Data: ' . json_encode($input ?? []));
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage() ?: 'An error occurred while updating the transaction',
        'error_details' => [
            'type' => get_class($e),
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ]);
}
