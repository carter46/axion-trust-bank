<?php 
$pageTitle = 'My Loans - SecureBank';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

requireLogin();

// Get user currency
$userModel = new User();
$user = $userModel->findById($_SESSION['user_id']);
$userCurrency = getUserDisplayCurrency($user);

// Include head
include __DIR__ . '/../../includes/head.php';

// Include sidebar and main structure
include __DIR__ . '/../../includes/sidebar.php';
?>

<!-- ===== LOANS PAGE CONTENT ===== -->

<?php include __DIR__ . '/../../includes/restricted-banner.php'; ?>

<style>
.page-header {
    margin-bottom: 30px;
}

.page-header h1 {
    font-size: 32px;
    font-weight: 700;
    color: #032B44;
    margin-bottom: 8px;
}

.page-header p {
    color: #666;
    font-size: 16px;
}

.card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 24px;
    padding: 30px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s;
    text-align: center;
}

.btn {
    display: inline-block;
    padding: 12px 24px;
    font-size: 16px;
    font-weight: 600;
    text-align: center;
    text-decoration: none;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.btn-primary {
    background: linear-gradient(135deg, #032B44 0%, #024a6b 100%);
    color: white;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #024a6b 0%, #032B44 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(3, 43, 68, 0.3);
}

@media (max-width: 768px) {
    .page-header h1 {
        font-size: 24px;
    }
    
    .card {
        padding: 20px;
    }
    
    /* Fix horizontal scroll - make grid responsive */
    .content-area [style*="grid-template-columns"] {
        grid-template-columns: 1fr !important;
        max-width: 100% !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        box-sizing: border-box;
    }
}

/* Fix the loan grid specifically */
.loans-grid {
    max-width: 100%;
    width: 100%;
    box-sizing: border-box;
}

@media (max-width: 768px) {
    .loans-grid {
        grid-template-columns: 1fr !important;
        gap: 15px !important;
        margin-bottom: 20px !important;
        max-width: 100% !important;
        width: 100% !important;
        box-sizing: border-box;
    }
}
</style>

<div class="page-header">
    <h1>My Loans</h1>
    <p>View and manage your loan applications</p>
</div>

<?php if (!empty($loans)): ?>
    <div class="loans-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(400px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <?php foreach ($loans as $loan): ?>
            <div class="card" style="text-align: left;">
                <div style="padding: 25px;">
                    <!-- Loan Type & Status -->
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <span style="background: #032B44; color: white; padding: 6px 14px; border-radius: 12px; font-size: 12px; font-weight: 600; text-transform: uppercase;">
                            <?php echo htmlspecialchars($loan['loan_type']); ?> Loan
                        </span>
                        <span style="padding: 6px 14px; border-radius: 12px; font-size: 12px; font-weight: 500; 
                            <?php 
                            if ($loan['status'] === 'active') echo 'background: #d1fae5; color: #065f46;';
                            elseif ($loan['status'] === 'pending') echo 'background: #fef3c7; color: #92400e;';
                            elseif ($loan['status'] === 'approved') echo 'background: #dbeafe; color: #1e40af;';
                            elseif ($loan['status'] === 'rejected') echo 'background: #fee2e2; color: #991b1b;';
                            elseif ($loan['status'] === 'completed') echo 'background: #d1fae5; color: #065f46;';
                            else echo 'background: #e5e7eb; color: #1f2937;';
                            ?>">
                            <?php echo ucfirst($loan['status']); ?>
                        </span>
                    </div>
                    
                    <!-- Loan Amount -->
                    <div style="margin-bottom: 20px;">
                        <p style="font-size: 13px; color: #999; margin-bottom: 6px;">Loan Amount</p>
                        <h2 style="font-size: 32px; font-weight: 700; color: #032B44; margin: 0;">
                            <?php echo formatLoanAmountForUser($loan['loan_amount'], $user, $loan); ?>
                        </h2>
                    </div>
                    
                    <!-- Loan Details Grid -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 12px;">
                        <?php if ($loan['status'] === 'active' || $loan['status'] === 'completed'): ?>
                            <div>
                                <p style="font-size: 12px; color: #999; margin-bottom: 4px;">Outstanding</p>
                                <p style="font-size: 16px; font-weight: 600; color: #ef4444;">
                                    <?php echo formatLoanAmountForUser($loan['outstanding_balance'] ?? 0, $user, $loan); ?>
                                </p>
                            </div>
                            <div>
                                <p style="font-size: 12px; color: #999; margin-bottom: 4px;">Monthly Payment</p>
                                <p style="font-size: 16px; font-weight: 600; color: #032B44;">
                                    <?php echo formatLoanAmountForUser($loan['monthly_payment'] ?? 0, $user, $loan); ?>
                                </p>
                            </div>
                        <?php endif; ?>
                        <div>
                            <p style="font-size: 12px; color: #999; margin-bottom: 4px;">Interest Rate</p>
                            <p style="font-size: 16px; font-weight: 600; color: #032B44;">
                                <?php echo number_format($loan['interest_rate'], 2); ?>%
                            </p>
                        </div>
                        <div>
                            <p style="font-size: 12px; color: #999; margin-bottom: 4px;">Term</p>
                            <p style="font-size: 16px; font-weight: 600; color: #032B44;">
                                <?php echo $loan['term_months']; ?> months
                            </p>
                        </div>
                    </div>
                    
                    <!-- Purpose -->
                    <?php if (!empty($loan['purpose'])): ?>
                        <div style="margin-bottom: 20px;">
                            <p style="font-size: 12px; color: #999; margin-bottom: 4px;">Purpose</p>
                            <p style="font-size: 14px; color: #666;">
                                <?php echo htmlspecialchars($loan['purpose']); ?>
                            </p>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Application Date -->
                    <div style="margin-bottom: 20px;">
                        <p style="font-size: 12px; color: #999; margin-bottom: 4px;">Applied</p>
                        <p style="font-size: 14px; color: #666;">
                            <?php echo date('M d, Y', strtotime($loan['application_date'])); ?>
                        </p>
                    </div>
                    
                    <!-- Action Button -->
                    <a href="<?php echo SITE_URL; ?>/loan/view/<?php echo $loan['id']; ?>" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-eye"></i> View Details
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div style="text-align: center;">
        <a href="<?php echo SITE_URL; ?>/loan/apply" class="btn btn-primary">
            <i class="fas fa-plus"></i> Apply for New Loan
        </a>
    </div>
<?php else: ?>
    <div class="card text-center">
        <div class="card-body">
            <i class="fas fa-hand-holding-usd fa-3x" style="color: #666; margin-bottom: 20px;"></i>
            <h3>No Loans Found</h3>
            <p style="color: #666;">You don't have any active loans.</p>
            <a href="<?php echo SITE_URL; ?>/loan/apply" class="btn btn-primary">
                <i class="fas fa-plus"></i> Apply for Loan
            </a>
        </div>
    </div>
<?php endif; ?>

<?php
// Include mobile navigation and closing tags
include __DIR__ . '/../../includes/mobile-nav.php';
?>
