<?php 
$pageTitle = 'External Transfer - SecureBank';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

// Include head
include __DIR__ . '/../../includes/head.php';

// Include sidebar and main structure
include __DIR__ . '/../../includes/sidebar.php';
?>

<!-- ===== EXTERNAL TRANSFER PAGE CONTENT ===== -->

<?php include __DIR__ . '/../../includes/restricted-banner.php'; ?>

<style>
.page-header {
    background: linear-gradient(135deg, #032B44 0%, #024a6b 100%);
    color: white;
    padding: 40px;
    margin: -30px -30px 40px -30px;
    border-radius: 0 0 24px 24px;
}

.page-header h1 {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 8px;
}

.page-header p {
    font-size: 16px;
    opacity: 0.9;
    margin: 0;
}

.card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 24px;
    padding: 30px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    color: #032B44;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    font-size: 16px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    transition: all 0.3s;
}

.form-control:focus {
    outline: none;
    border-color: #032B44;
    box-shadow: 0 0 0 3px rgba(3, 43, 68, 0.1);
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
    .page-header {
        padding: 30px 20px;
        margin: -15px -15px 20px -15px;
    }
    
    .page-header h1 {
        font-size: 24px;
    }
}
</style>

<div class="page-header">
    <h1>External Transfer</h1>
    <p>Transfer money to other banks or recipients</p>
</div>

<div class="card">
    <h3 style="color: #032B44; margin-bottom: 20px;">Transfer Details</h3>
    <form method="POST">
        <div class="form-group">
            <label class="form-label">From Account</label>
            <select class="form-control" name="from_account" required>
                <option value="">Select account</option>
            </select>
        </div>
        
        <div class="form-group">
            <label class="form-label">Recipient Bank</label>
            <input type="text" class="form-control" name="recipient_bank" placeholder="Enter bank name" required>
        </div>
        
        <div class="form-group">
            <label class="form-label">Recipient Account Number</label>
            <input type="text" class="form-control" name="recipient_account" placeholder="Enter account number" required>
        </div>
        
        <div class="form-group">
            <label class="form-label">Recipient Name</label>
            <input type="text" class="form-control" name="recipient_name" placeholder="Enter recipient name" required>
        </div>
        
        <div class="form-group">
            <label class="form-label">Amount</label>
            <input type="number" class="form-control" name="amount" placeholder="0.00" step="0.01" required>
        </div>
        
        <div class="form-group">
            <label class="form-label">Description (Optional)</label>
            <input type="text" class="form-control" name="description" placeholder="Enter description">
        </div>
        
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-paper-plane"></i> Transfer Now
        </button>
    </form>
</div>

<?php
include __DIR__ . '/../../includes/mobile-nav.php';
?>
