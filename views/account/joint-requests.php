<?php
$pageTitle = 'Joint Account Requests - ' . getSiteName();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();

// Include head
include __DIR__ . '/../../includes/head.php';

// Include sidebar and main structure
include __DIR__ . '/../../includes/sidebar.php';
?>

<!-- ===== JOINT ACCOUNT REQUESTS PAGE CONTENT ===== -->

<style>
    .main-content-area .content-area {
        background: #f5f5f5 !important;
        padding: 15px !important;
    }

    .page-header {
        margin-bottom: 30px;
    }

    .page-header h1 {
        font-size: 32px;
        font-weight: 700;
        color: #032B44;
        margin-bottom: 8px;
    }

    .requests-card {
        background: white;
        border-radius: 16px;
        padding: 32px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        margin-bottom: 30px;
    }

    .request-item {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 20px;
        background: #f9fafb;
    }

    .request-item:last-child {
        margin-bottom: 0;
    }

    .request-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 16px;
    }

    .request-info h3 {
        margin: 0 0 8px 0;
        color: #032B44;
        font-size: 18px;
    }

    .request-info p {
        margin: 4px 0;
        color: #666;
        font-size: 14px;
    }

    .request-actions {
        display: flex;
        gap: 12px;
    }

    .btn-approve {
        background: #10b981;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
    }

    .btn-approve:hover {
        background: #059669;
    }

    .btn-reject {
        background: #dc2626;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
    }

    .btn-reject:hover {
        background: #b91c1c;
    }

    .account-details {
        background: white;
        padding: 16px;
        border-radius: 8px;
        margin-top: 12px;
    }

    .account-details h4 {
        margin: 0 0 12px 0;
        color: #032B44;
        font-size: 16px;
    }

    .account-details ul {
        margin: 0;
        padding-left: 20px;
        color: #666;
    }

    .account-details li {
        margin-bottom: 6px;
    }

    .no-requests {
        text-align: center;
        padding: 60px 20px;
        color: #666;
    }

    .no-requests i {
        font-size: 64px;
        color: #d1d5db;
        margin-bottom: 20px;
    }

    .no-requests h3 {
        margin: 0 0 12px 0;
        color: #374151;
    }

    .expires-badge {
        display: inline-block;
        background: #fef3c7;
        color: #92400e;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        margin-top: 8px;
    }
</style>

<div class="main-content-area">
    <div class="content-area">
        <div class="page-header">
            <h1>Joint Account Requests</h1>
            <p>Review and manage requests to join your accounts</p>
        </div>

        <div class="requests-card">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success" style="margin-bottom: 20px;">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error" style="margin-bottom: 20px;">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if (empty($requests)): ?>
                <div class="no-requests">
                    <i class="fas fa-inbox"></i>
                    <h3>No Pending Requests</h3>
                    <p>You don't have any pending joint account requests at this time.</p>
                </div>
            <?php else: ?>
                <?php foreach ($requests as $request): ?>
                    <div class="request-item">
                        <div class="request-header">
                            <div class="request-info">
                                <h3><?php echo htmlspecialchars($request['requesting_user_name']); ?></h3>
                                <p><strong>Requested:</strong> <?php echo date('F j, Y g:i A', strtotime($request['requested_at'])); ?></p>
                                <?php if ($request['expires_at']): ?>
                                    <span class="expires-badge">
                                        Expires: <?php echo date('F j, Y', strtotime($request['expires_at'])); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="request-actions">
                                <a href="/account/joint-approve/<?php echo $request['id']; ?>" class="btn-approve" onclick="return confirm('Are you sure you want to approve this request? The user will have full access to this account.');">
                                    <i class="fas fa-check"></i> Approve
                                </a>
                                <a href="/account/joint-reject/<?php echo $request['id']; ?>" class="btn-reject" onclick="return confirm('Are you sure you want to reject this request?');">
                                    <i class="fas fa-times"></i> Reject
                                </a>
                            </div>
                        </div>
                        
                        <div class="account-details" style="margin-top: 20px;">
                            <h4>Requesting User Details</h4>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 12px;">
                                <div>
                                    <strong>Full Name:</strong><br>
                                    <span style="color: #666;"><?php echo htmlspecialchars($request['requesting_user_name']); ?></span>
                                </div>
                                <div>
                                    <strong>Email:</strong><br>
                                    <span style="color: #666;"><?php echo htmlspecialchars($request['requesting_user_email']); ?></span>
                                </div>
                                <?php if (!empty($request['requesting_user_phone'])): ?>
                                <div>
                                    <strong>Phone:</strong><br>
                                    <span style="color: #666;"><?php echo htmlspecialchars($request['requesting_user_phone']); ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($request['requesting_user_dob'])): ?>
                                <div>
                                    <strong>Date of Birth:</strong><br>
                                    <span style="color: #666;"><?php echo date('F j, Y', strtotime($request['requesting_user_dob'])); ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($request['requesting_user_address'])): ?>
                                <div style="grid-column: 1 / -1;">
                                    <strong>Address:</strong><br>
                                    <span style="color: #666;">
                                        <?php echo htmlspecialchars($request['requesting_user_address']); ?><br>
                                        <?php 
                                        $addressParts = array_filter([
                                            $request['requesting_user_city'] ?? '',
                                            $request['requesting_user_state'] ?? '',
                                            $request['requesting_user_postal_code'] ?? ''
                                        ]);
                                        if (!empty($addressParts)) {
                                            echo htmlspecialchars(implode(', ', $addressParts));
                                        }
                                        if (!empty($request['requesting_user_country'])) {
                                            echo (!empty($addressParts) ? ', ' : '') . htmlspecialchars($request['requesting_user_country']);
                                        }
                                        ?>
                                    </span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="account-details" style="margin-top: 16px;">
                            <h4>Account Details</h4>
                            <ul>
                                <li><strong>Account Number:</strong> <?php echo htmlspecialchars($request['account_number']); ?></li>
                                <li><strong>Account Type:</strong> <?php echo ucfirst($request['account_type']); ?></li>
                            </ul>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

