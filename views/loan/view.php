<?php 
$pageTitle = 'Loan Details - SecureBank';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

requireLogin();

// Get user currency
$userModel = new User();
$user = $userModel->findById($_SESSION['user_id']);
$userCurrency = getUserDisplayCurrency($user);

// Include head
include __DIR__ . '/../../includes/head.php';

// Include sidebar
include __DIR__ . '/../../includes/sidebar.php';

// Loan data is passed from controller
$loan = $loan ?? null;
$payment_schedule = $payment_schedule ?? [];

if (!$loan) {
    redirect('/loan');
    exit;
}
?>

<?php include __DIR__ . '/../../includes/restricted-banner.php'; ?>

<style>
.page-header {
    margin-bottom: 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.page-header h1 {
    font-size: 32px;
    font-weight: 700;
    color: #032B44;
    margin-bottom: 8px;
}

.card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 24px;
    padding: 30px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    margin-bottom: 25px;
}

.loan-summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.summary-item {
    text-align: center;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 12px;
}

.summary-label {
    font-size: 13px;
    color: #666;
    margin-bottom: 8px;
    text-transform: uppercase;
}

.summary-value {
    font-size: 28px;
    font-weight: 700;
    color: #032B44;
}

.summary-sub {
    font-size: 12px;
    color: #666;
    margin-top: 5px;
}

.status-badge {
    padding: 8px 16px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    text-transform: capitalize;
    display: inline-block;
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
}

.status-approved,
.status-active {
    background: #d1fae5;
    color: #065f46;
}

.status-rejected {
    background: #fee2e2;
    color: #991b1b;
}

.status-completed {
    background: #e0e7ff;
    color: #3730a3;
}

.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 25px;
}

.info-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #f0f0f0;
}

.info-label {
    color: #666;
    font-weight: 500;
}

.info-value {
    color: #2d3748;
    font-weight: 600;
}

.progress-bar {
    width: 100%;
    height: 8px;
    background: #e5e7eb;
    border-radius: 4px;
    overflow: hidden;
    margin: 15px 0;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #10b981 0%, #059669 100%);
    transition: width 0.3s;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-primary {
    background: linear-gradient(135deg, #032B44 0%, #024a6b 100%);
    color: white;
}

.btn-secondary {
    background: #e5e7eb;
    color: #374151;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

table {
    width: 100%;
    border-collapse: collapse;
}

table th {
    padding: 12px;
    text-align: left;
    border-bottom: 2px solid #e0e0e0;
    color: #032B44;
    font-weight: 600;
    font-size: 14px;
}

table td {
    padding: 12px;
    border-bottom: 1px solid #f0f0f0;
    font-size: 14px;
}

.payment-form {
    background: #f8f9fa;
    padding: 25px;
    border-radius: 12px;
    margin-top: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
}

.form-control {
    width: 100%;
    padding: 12px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
}

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .loan-summary,
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .card {
        padding: 20px;
    }
}
</style>

<div class="page-header">
    <div>
        <h1><?php echo ucfirst($loan['loan_type']); ?> Loan</h1>
        <p style="color: #666;">Loan details and payment schedule</p>
    </div>
    <a href="<?php echo SITE_URL; ?>/loan" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Back to Loans
    </a>
</div>

<!-- Loan Summary -->
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h3 style="color: #032B44; margin: 0;">Loan Overview</h3>
        <span class="status-badge status-<?php echo $loan['status']; ?>">
            <?php echo ucfirst($loan['status']); ?>
        </span>
    </div>
    
    <div class="loan-summary">
        <div class="summary-item">
            <div class="summary-label">Loan Amount</div>
            <div class="summary-value"><?php echo formatLoanAmountForUser($loan['loan_amount'] ?? 0, $user, $loan); ?></div>
        </div>
        
        <div class="summary-item">
            <div class="summary-label">Outstanding Balance</div>
            <div class="summary-value" style="color: #ef4444;">
                <?php echo formatLoanAmountForUser($loan['outstanding_balance'] ?? 0, $user, $loan); ?>
            </div>
        </div>
        
        <div class="summary-item">
            <div class="summary-label">Monthly Payment</div>
            <div class="summary-value" style="font-size: 22px;">
                <?php echo formatLoanAmountForUser($loan['monthly_payment'] ?? 0, $user, $loan); ?>
            </div>
            <div class="summary-sub">for <?php echo $loan['term_months']; ?> months</div>
        </div>
        
        <div class="summary-item">
            <div class="summary-label">Interest Rate</div>
            <div class="summary-value" style="font-size: 22px;">
                <?php echo $loan['interest_rate']; ?>%
            </div>
            <div class="summary-sub">APR</div>
        </div>
    </div>

    <!-- Progress Bar -->
    <div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
            <span style="font-size: 14px; color: #666;">Repayment Progress</span>
            <span style="font-size: 14px; font-weight: 600; color: #032B44;">
                <?php 
                $progress = $loan['loan_amount'] > 0 ? (($loan['loan_amount'] - $loan['outstanding_balance']) / $loan['loan_amount']) * 100 : 0;
                echo number_format($progress, 1); 
                ?>%
            </span>
        </div>
        <div class="progress-bar">
            <div class="progress-fill" style="width: <?php echo $progress; ?>%"></div>
        </div>
        <div style="display: flex; justify-content: space-between; margin-top: 5px;">
            <span style="font-size: 12px; color: #666;">
                        Paid: <?php echo formatLoanAmountForUser(($loan['loan_amount'] ?? 0) - ($loan['outstanding_balance'] ?? 0), $user, $loan); ?>
            </span>
            <span style="font-size: 12px; color: #666;">
                Remaining: <?php echo formatLoanAmountForUser($loan['outstanding_balance'] ?? 0, $user, $loan); ?>
            </span>
        </div>
    </div>
</div>

<!-- Loan Details -->
<div class="card">
    <h3 style="color: #032B44; margin-bottom: 20px;">Loan Details</h3>
    
    <div class="info-grid">
        <div>
            <div class="info-row">
                <span class="info-label">Loan Type:</span>
                <span class="info-value" style="text-transform: capitalize;"><?php echo $loan['loan_type']; ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Purpose:</span>
                <span class="info-value"><?php echo htmlspecialchars($loan['purpose']); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Application Date:</span>
                <span class="info-value"><?php echo date('M d, Y', strtotime($loan['application_date'])); ?></span>
            </div>
            <?php if ($loan['approval_date']): ?>
                <div class="info-row">
                    <span class="info-label">Approval Date:</span>
                    <span class="info-value"><?php echo date('M d, Y', strtotime($loan['approval_date'])); ?></span>
                </div>
            <?php endif; ?>
        </div>
        
        <div>
            <div class="info-row">
                <span class="info-label">Term:</span>
                <span class="info-value"><?php echo $loan['term_months']; ?> months</span>
            </div>
            <div class="info-row">
                <span class="info-label">Start Date:</span>
                <span class="info-value">
                    <?php echo isset($loan['start_date']) && $loan['start_date'] ? date('M d, Y', strtotime($loan['start_date'])) : 'N/A'; ?>
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">End Date:</span>
                <span class="info-value">
                    <?php echo isset($loan['end_date']) && $loan['end_date'] ? date('M d, Y', strtotime($loan['end_date'])) : 'N/A'; ?>
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Next Payment:</span>
                <span class="info-value">
                    <?php 
                    $nextPayment = 'N/A';
                    if (!empty($payment_schedule)) {
                        foreach ($payment_schedule as $payment) {
                            if ($payment['status'] === 'pending') {
                                $nextPayment = date('M d, Y', strtotime($payment['due_date']));
                                break;
                            }
                        }
                    }
                    echo $nextPayment;
                    ?>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Make Payment -->
<?php if ($loan['status'] === 'active'): ?>
    <div class="card">
        <h3 style="color: #032B44; margin-bottom: 20px;">Make a Payment</h3>
        
        <form method="POST" action="<?php echo SITE_URL; ?>/loan/payment/<?php echo $loan['id']; ?>" class="payment-form">
            <div class="form-group">
                <label class="form-label">Payment Amount (<?php echo $userCurrency; ?>)</label>
                <input type="number" name="amount" class="form-control" 
                       value="<?php echo $loan['monthly_payment'] ?? 0; ?>" 
                       min="0" max="<?php echo $loan['outstanding_balance'] ?? 0; ?>" 
                       step="0.01" required>
                <div style="font-size: 13px; color: #666; margin-top: 5px;">
                    Minimum: <?php echo formatLoanAmountForUser($loan['monthly_payment'] ?? 0, $user, $loan); ?> | 
                    Maximum: <?php echo formatLoanAmountForUser($loan['outstanding_balance'] ?? 0, $user, $loan); ?>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Payment Account</label>
                <select name="account_id" class="form-control" required>
                    <?php
                    $accountModel = new Account();
                    $accounts = $accountModel->getUserAccounts($_SESSION['user_id']);
                    foreach ($accounts as $account):
                    ?>
                        <option value="<?php echo $account['id']; ?>">
                            <?php echo ucfirst($account['account_type']); ?> - <?php echo $account['account_number']; ?> 
                            (Balance: <?php echo formatAccountBalance($account['balance'], $account, $userCurrency); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">
                <i class="fas fa-credit-card"></i>
                Make Payment
            </button>
        </form>
    </div>
<?php endif; ?>

<!-- Payment Schedule -->
<div class="card">
    <h3 style="color: #032B44; margin-bottom: 20px;">Payment Schedule</h3>
    
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Due Date</th>
                    <th>Principal</th>
                    <th>Interest</th>
                    <th>Total Payment</th>
                    <th>Remaining Balance</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($payment_schedule)): ?>
                    <?php foreach ($payment_schedule as $index => $payment): ?>
                        <tr style="<?php echo $payment['status'] === 'paid' ? 'opacity: 0.6;' : ''; ?>">
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo date('M d, Y', strtotime($payment['due_date'])); ?></td>
                            <td><?php echo formatLoanAmountForUser($payment['principal_amount'], $user, $loan); ?></td>
                            <td><?php echo formatLoanAmountForUser($payment['interest_amount'], $user, $loan); ?></td>
                            <td style="font-weight: 600;">
                                <?php echo formatLoanAmountForUser($payment['payment_amount'], $user, $loan); ?>
                            </td>
                            <td><?php echo formatLoanAmountForUser($payment['remaining_balance'], $user, $loan); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $payment['status']; ?>">
                                    <?php echo ucfirst($payment['status']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: #999; padding: 40px;">
                            Payment schedule will be available once loan is approved
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
include __DIR__ . '/../../includes/mobile-nav.php';
?>

