<?php 
$pageTitle = 'Currency Management - Admin - Octobank';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/currency.php';

// Ensure admin access
requireLogin();
requireAdmin();

$db = Database::getInstance();
$userId = $_SESSION['user_id'];
$currency = new Currency();

$successMessage = '';
$errorMessage = '';

// Handle Update User Currency
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user_currency'])) {
    $targetUserId = intval($_POST['user_id'] ?? 0);
    $newCurrency = Security::sanitize($_POST['currency'] ?? 'USD');
    
    if ($targetUserId > 0) {
        $sql = "UPDATE users SET currency = ?, currency_selection_shown = 1 WHERE id = ?";
        $result = $db->query($sql, [$newCurrency, $targetUserId]);
        
        if ($result) {
            $successMessage = "User currency updated successfully!";
            logActivity($userId, 'currency_updated', "Updated currency for user ID: $targetUserId to $newCurrency");
        } else {
            $errorMessage = "Failed to update user currency.";
        }
    }
}

// Handle Refresh Rates
if (isset($_GET['refresh_rates'])) {
    $sql = "SELECT setting_value FROM system_settings WHERE setting_key = 'default_currency'";
    $stmt = $db->query($sql);
    $result = $stmt->fetch();
    $baseCurrency = $result['setting_value'] ?? 'USD';
    
    if ($currency->refreshRates($baseCurrency)) {
        $successMessage = "Exchange rates refreshed successfully!";
        logActivity($userId, 'rates_refreshed', "Refreshed exchange rates");
    } else {
        $errorMessage = "Failed to refresh exchange rates.";
    }
}

// Get all users
$sql = "SELECT id, full_name, email, currency, status FROM users WHERE role != 'admin' ORDER BY full_name ASC";
$stmt = $db->query($sql);
$users = $stmt->fetchAll();

// Get recent exchange rates
$sql = "SELECT * FROM exchange_rates ORDER BY updated_at DESC LIMIT 20";
$stmt = $db->query($sql);
$recentRates = $stmt->fetchAll();

$supportedCurrencies = $currency->getSupportedCurrencies();

// Include head
include __DIR__ . '/../../includes/head.php';

// Include sidebar and main structure
include __DIR__ . '/../../includes/admin-sidebar.php';
?>

<style>
.content-area {
    background: #f5f7fa;
    min-height: 100vh;
    padding: 20px;
}

.currency-container {
    max-width: 1400px;
    margin: 0 auto;
}

.currency-header {
    background: white;
    border-radius: 16px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.currency-title {
    font-size: 32px;
    font-weight: 700;
    color: #2d3748;
}

.btn-refresh {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    color: white;
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
}

.btn-refresh:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(5, 150, 105, 0.4);
}

.currency-card {
    background: white;
    border-radius: 16px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.card-title {
    font-size: 24px;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #e2e8f0;
}

.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: 500;
}

.alert-success {
    background: #d1fae5;
    color: #065f46;
    border-left: 4px solid #059669;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    border-left: 4px solid #dc2626;
}

.users-table {
    width: 100%;
    border-collapse: collapse;
}

.users-table th {
    background: #f7fafc;
    padding: 12px;
    text-align: left;
    font-weight: 600;
    color: #4a5568;
    border-bottom: 2px solid #e2e8f0;
}

.users-table td {
    padding: 12px;
    border-bottom: 1px solid #e2e8f0;
}

.currency-badge {
    background: #e8f0fe;
    color: #1a73e8;
    padding: 4px 12px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 13px;
}

.btn-change {
    background: #1a73e8;
    color: white;
    padding: 6px 12px;
    border: none;
    border-radius: 4px;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-change:hover {
    background: #0d62d3;
}

.rates-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 15px;
}

.rate-card {
    background: #f7fafc;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #1a73e8;
}

.rate-pair {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 8px;
}

.rate-value {
    font-size: 18px;
    color: #1a73e8;
    font-weight: 700;
}

.rate-time {
    font-size: 12px;
    color: #6c757d;
    margin-top: 4px;
}

.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 10000; /* Higher than mobile nav (9999) */
    backdrop-filter: blur(5px);
}

@media (max-width: 768px) {
    .modal {
        z-index: 10000; /* Ensure modals are above mobile nav on mobile */
    }
}

.modal.active {
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: white;
    padding: 30px;
    border-radius: 16px;
    max-width: 500px;
    width: 90%;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.modal-title {
    font-size: 24px;
    font-weight: 600;
    color: #2d3748;
}

.btn-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #6c757d;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-weight: 600;
    color: #4a5568;
    margin-bottom: 8px;
    font-size: 15px;
}

.form-input, .form-select {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 16px;
    transition: all 0.3s ease;
}

.form-input:focus, .form-select:focus {
    outline: none;
    border-color: #1a73e8;
    box-shadow: 0 0 0 3px rgba(26, 115, 232, 0.1);
}

.btn-submit {
    background: linear-gradient(135deg, #1a73e8 0%, #0d62d3 100%);
    color: white;
    padding: 14px 32px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(26, 115, 232, 0.3);
    width: 100%;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(26, 115, 232, 0.4);
}

@media (max-width: 768px) {
    .currency-header {
        flex-direction: column;
        align-items: stretch;
    }
    
    .btn-refresh {
        width: 100%;
    }
    
    .users-table {
        font-size: 14px;
    }
    
    .users-table th, .users-table td {
        padding: 8px;
    }
    
    .rates-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="currency-container">
    <div class="currency-header">
        <div>
            <h1 class="currency-title">Currency Management</h1>
            <p style="color: #6c757d;">Manage user currencies and view exchange rates</p>
        </div>
        <button class="btn-refresh" onclick="window.location.href='?refresh_rates=1'">
            🔄 Refresh Exchange Rates
        </button>
    </div>
    
    <?php if ($successMessage): ?>
        <div class="alert alert-success">
            ✓ <?php echo htmlspecialchars($successMessage); ?>
        </div>
    <?php endif; ?>
    
    <?php if ($errorMessage): ?>
        <div class="alert alert-error">
            ✗ <?php echo htmlspecialchars($errorMessage); ?>
        </div>
    <?php endif; ?>
    
    <div class="currency-card">
        <h2 class="card-title">User Currency Preferences</h2>
        
        <table class="users-table">
            <thead>
                <tr>
                    <th>User Name</th>
                    <th>Email</th>
                    <th>Current Currency</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><span class="currency-badge"><?php echo htmlspecialchars(getUserDisplayCurrency($user)); ?></span></td>
                        <td><?php echo ucfirst($user['status']); ?></td>
                        <td>
                            <button class="btn-change" onclick="openCurrencyModal(<?php echo $user['id']; ?>, '<?php echo addslashes($user['full_name']); ?>', '<?php echo $user['currency']; ?>')">
                                Change Currency
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div class="currency-card">
        <h2 class="card-title">Recent Exchange Rates</h2>
        <p style="color: #6c757d; margin-bottom: 20px;">Rates are cached for 1 hour and automatically refreshed</p>
        
        <div class="rates-grid">
            <?php foreach ($recentRates as $rate): ?>
                <div class="rate-card">
                    <div class="rate-pair"><?php echo htmlspecialchars($rate['from_currency']); ?> → <?php echo htmlspecialchars($rate['to_currency']); ?></div>
                    <div class="rate-value"><?php echo number_format($rate['rate'], 4); ?></div>
                    <div class="rate-time">Updated: <?php echo date('M d, Y H:i', strtotime($rate['updated_at'])); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Change Currency Modal -->
<div class="modal" id="currencyModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Change User Currency</h2>
            <button class="btn-close" onclick="closeCurrencyModal()">&times;</button>
        </div>
        
        <form method="POST" action="">
            <input type="hidden" id="modal_user_id" name="user_id">
            
            <div class="form-group">
                <label class="form-label">User</label>
                <input type="text" class="form-input" id="modal_user_name" readonly>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="modal_currency">Select New Currency *</label>
                <select class="form-select" id="modal_currency" name="currency" required>
                    <?php foreach ($supportedCurrencies as $code => $name): ?>
                        <option value="<?php echo $code; ?>"><?php echo $code; ?> - <?php echo $name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit" name="update_user_currency" class="btn-submit">Update Currency</button>
        </form>
    </div>
</div>

<script>
    let currentUserId = null;
    
    function openCurrencyModal(userId, userName, currentCurrency) {
        currentUserId = userId;
        document.getElementById('modal_user_id').value = userId;
        document.getElementById('modal_user_name').value = userName;
        document.getElementById('modal_currency').value = currentCurrency;
        document.getElementById('currencyModal').classList.add('active');
    }
    
    function closeCurrencyModal() {
        document.getElementById('currencyModal').classList.remove('active');
    }
    
    // Close modal when clicking outside
    document.getElementById('currencyModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeCurrencyModal();
        }
    });
</script>


