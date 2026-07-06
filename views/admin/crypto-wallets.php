<?php 
$pageTitle = 'Crypto Wallets - Admin - ' . getSiteName();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/security.php';

// Include head and admin sidebar
include __DIR__ . '/../../includes/head.php';
include __DIR__ . '/../../includes/admin-sidebar.php';

if (empty($wallets)) {
    $wallets = [];
}
?>

<style>
.page-header {
    margin-bottom: 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

.page-header h1 {
    font-size: 32px;
    font-weight: 700;
    color: #032B44;
}

.btn-create {
    padding: 12px 24px;
    background: linear-gradient(135deg, #032B44 0%, #024a6b 100%);
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-create:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(3, 43, 68, 0.3);
}

.wallets-table {
    width: 100%;
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.wallets-table table {
    width: 100%;
    border-collapse: collapse;
}

.wallets-table th {
    background: #f9fafb;
    padding: 16px;
    text-align: left;
    font-weight: 600;
    color: #374151;
    border-bottom: 2px solid #e5e7eb;
}

.wallets-table td {
    padding: 16px;
    border-bottom: 1px solid #e5e7eb;
}

.wallets-table tr:hover {
    background: #f9fafb;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.status-active {
    background: #d1fae5;
    color: #065f46;
}

.status-inactive {
    background: #fee2e2;
    color: #991b1b;
}

.btn-action {
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    margin-right: 8px;
    display: inline-block;
}

.btn-edit {
    background: #dbeafe;
    color: #1e40af;
}

.btn-delete {
    background: #fee2e2;
    color: #991b1b;
}

.wallet-address {
    font-family: monospace;
    font-size: 12px;
    word-break: break-all;
    max-width: 300px;
}

.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.7);
    z-index: 10000; /* Higher than mobile nav (9999) */
    align-items: center;
    justify-content: center;
    padding: 20px;
}

/* Mobile Wallet Cards */
.mobile-wallet-cards {
    display: none;
}

.wallet-card-mobile {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.wallet-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.wallet-info-mobile {
    flex: 1;
}

.wallet-type-mobile {
    font-weight: 600;
    color: #1f2937;
    font-size: 16px;
    margin-bottom: 4px;
}

.wallet-address-mobile {
    color: #6b7280;
    font-size: 12px;
    font-family: monospace;
    word-break: break-all;
}

.expand-btn {
    background: #f3f4f6;
    border: none;
    width: 36px;
    height: 36px;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #374151;
    font-size: 16px;
    transition: all 0.3s;
}

.expand-btn:hover {
    background: #e5e7eb;
}

.expand-btn.active {
    background: #3b82f6;
    color: white;
    transform: rotate(180deg);
}

.wallet-details-mobile {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
}

.wallet-details-mobile.expanded {
    max-height: 400px;
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid #e5e7eb;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    font-size: 14px;
}

.detail-label {
    color: #6b7280;
    font-weight: 500;
}

.detail-value {
    color: #1f2937;
    font-weight: 600;
}

.mobile-actions {
    display: flex;
    gap: 8px;
    margin-top: 12px;
}

.mobile-actions button,
.mobile-actions form {
    flex: 1;
}

.mobile-actions button {
    padding: 10px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    border: none;
    cursor: pointer;
    transition: all 0.3s;
    width: 100%;
}

.btn-edit-mobile {
    background: #dbeafe;
    color: #1e40af;
}

.btn-delete-mobile {
    background: #fee2e2;
    color: #991b1b;
}

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .page-header h1 {
        font-size: 24px;
    }
    
    .wallets-table {
        display: none;
    }
    
    .mobile-wallet-cards {
        display: block;
    }
    
    .modal {
        z-index: 10000; /* Ensure modals are above mobile nav on mobile */
    }
}

.modal.active {
    display: flex;
}

.modal-content {
    background: white;
    border-radius: 24px;
    padding: 30px;
    max-width: 600px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 1px solid #e5e7eb;
}

.modal-header h2 {
    font-size: 24px;
    color: #032B44;
}

.close-modal {
    background: none;
    border: none;
    font-size: 28px;
    color: #6b7280;
    cursor: pointer;
    padding: 0;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #374151;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 16px;
}

.form-control:focus {
    outline: none;
    border-color: #032B44;
}
</style>

<div class="page-header">
    <h1>Crypto Wallets</h1>
    <button class="btn-create" onclick="openCreateModal()">
        <i class="fas fa-plus"></i> Add Wallet
    </button>
</div>

<?php if (isset($_SESSION['success'])): ?>
<div style="background: #d1fae5; border: 2px solid #10b981; border-radius: 12px; padding: 16px; margin-bottom: 24px; color: #065f46;">
    <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
</div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
<div style="background: #fee2e2; border: 2px solid #ef4444; border-radius: 12px; padding: 16px; margin-bottom: 24px; color: #991b1b;">
    <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
</div>
<?php endif; ?>

<div class="wallets-table">
    <table>
        <thead>
            <tr>
                <th>Type</th>
                <th>Address</th>
                <th>Network</th>
                <th>Label</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($wallets)): ?>
            <tr>
                <td colspan="6" style="text-align: center; padding: 40px; color: #6b7280;">
                    No crypto wallets configured. <a href="#" onclick="openCreateModal(); return false;">Add your first wallet</a>
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($wallets as $wallet): ?>
            <tr>
                <td><strong><?php echo strtoupper($wallet['crypto_type']); ?></strong></td>
                <td class="wallet-address"><?php echo htmlspecialchars($wallet['wallet_address']); ?></td>
                <td><?php echo htmlspecialchars($wallet['network'] ?? '-'); ?></td>
                <td><?php echo htmlspecialchars($wallet['label'] ?? '-'); ?></td>
                <td>
                    <span class="status-badge status-<?php echo $wallet['is_active'] ? 'active' : 'inactive'; ?>">
                        <?php echo $wallet['is_active'] ? 'Active' : 'Inactive'; ?>
                    </span>
                </td>
                <td>
                    <button class="btn-action btn-edit" onclick="openEditModal(<?php echo $wallet['id']; ?>, '<?php echo $wallet['crypto_type']; ?>', '<?php echo htmlspecialchars($wallet['wallet_address'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($wallet['network'] ?? '', ENT_QUOTES); ?>', '<?php echo htmlspecialchars($wallet['label'] ?? '', ENT_QUOTES); ?>', <?php echo $wallet['is_active']; ?>)">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this wallet?');">
                        <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="wallet_id" value="<?php echo $wallet['id']; ?>">
                        <button type="submit" class="btn-action btn-delete">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Mobile View -->
<div class="mobile-wallet-cards">
    <?php if (empty($wallets)): ?>
        <div style="text-align: center; color: #666; padding: 40px;">
            No crypto wallets configured. <a href="#" onclick="openCreateModal(); return false;">Add your first wallet</a>
        </div>
    <?php else: ?>
        <?php foreach ($wallets as $wallet): ?>
            <div class="wallet-card-mobile">
                <div class="wallet-card-header">
                    <div class="wallet-info-mobile">
                        <div class="wallet-type-mobile"><?php echo strtoupper($wallet['crypto_type']); ?></div>
                        <div class="wallet-address-mobile"><?php echo htmlspecialchars($wallet['wallet_address']); ?></div>
                    </div>
                    <button class="expand-btn" onclick="toggleWalletDetails(this)">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
                <div class="wallet-details-mobile">
                    <div class="detail-row">
                        <span class="detail-label">Network</span>
                        <span class="detail-value"><?php echo htmlspecialchars($wallet['network'] ?? '-'); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Label</span>
                        <span class="detail-value"><?php echo htmlspecialchars($wallet['label'] ?? '-'); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Status</span>
                        <span class="status-badge status-<?php echo $wallet['is_active'] ? 'active' : 'inactive'; ?>">
                            <?php echo $wallet['is_active'] ? 'Active' : 'Inactive'; ?>
                        </span>
                    </div>
                    <div class="mobile-actions">
                        <button class="btn-edit-mobile" onclick="openEditModal(<?php echo $wallet['id']; ?>, '<?php echo $wallet['crypto_type']; ?>', '<?php echo htmlspecialchars($wallet['wallet_address'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($wallet['network'] ?? '', ENT_QUOTES); ?>', '<?php echo htmlspecialchars($wallet['label'] ?? '', ENT_QUOTES); ?>', <?php echo $wallet['is_active']; ?>)">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <form method="POST" style="display: inline; flex: 1;" onsubmit="return confirm('Delete this wallet?');">
                            <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="wallet_id" value="<?php echo $wallet['id']; ?>">
                            <button type="submit" class="btn-delete-mobile">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Create/Edit Modal -->
<div id="walletModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Add Crypto Wallet</h2>
            <button class="close-modal" onclick="closeWalletModal()">&times;</button>
        </div>
        
        <form method="POST" id="walletForm">
            <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
            <input type="hidden" name="action" id="formAction" value="create">
            <input type="hidden" name="wallet_id" id="walletId">
            
            <div class="form-group">
                <label class="form-label">Crypto Type *</label>
                <select name="crypto_type" id="cryptoType" class="form-control" required>
                    <option value="btc">Bitcoin (BTC)</option>
                    <option value="eth">Ethereum (ETH)</option>
                    <option value="usdt">USDT</option>
                    <option value="ltc">Litecoin (LTC)</option>
                    <option value="bch">Bitcoin Cash (BCH)</option>
                    <option value="doge">Dogecoin (DOGE)</option>
                    <option value="other">Other</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Wallet Address *</label>
                <input type="text" name="wallet_address" id="walletAddress" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Network</label>
                <input type="text" name="network" id="walletNetwork" class="form-control" placeholder="e.g., ERC20, TRC20, mainnet">
            </div>
            
            <div class="form-group">
                <label class="form-label">Label</label>
                <input type="text" name="label" id="walletLabel" class="form-control" placeholder="Optional label for this wallet">
            </div>
            
            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="is_active" id="walletActive" value="1" checked>
                    <span>Active (visible to users)</span>
                </label>
            </div>
            
            <button type="submit" class="btn-create" style="width: 100%; border: none; cursor: pointer;">
                <i class="fas fa-save"></i> Save Wallet
            </button>
        </form>
    </div>
</div>

<script>
function openCreateModal() {
    document.getElementById('modalTitle').textContent = 'Add Crypto Wallet';
    document.getElementById('formAction').value = 'create';
    document.getElementById('walletForm').reset();
    document.getElementById('walletId').value = '';
    document.getElementById('walletModal').classList.add('active');
}

function openEditModal(id, type, address, network, label, active) {
    document.getElementById('modalTitle').textContent = 'Edit Crypto Wallet';
    document.getElementById('formAction').value = 'update';
    document.getElementById('walletId').value = id;
    document.getElementById('cryptoType').value = type;
    document.getElementById('walletAddress').value = address;
    document.getElementById('walletNetwork').value = network || '';
    document.getElementById('walletLabel').value = label || '';
    document.getElementById('walletActive').checked = active == 1;
    document.getElementById('walletModal').classList.add('active');
}

function closeWalletModal() {
    document.getElementById('walletModal').classList.remove('active');
}

document.getElementById('walletModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeWalletModal();
});

function toggleWalletDetails(button) {
    const card = button.closest('.wallet-card-mobile');
    const details = card.querySelector('.wallet-details-mobile');
    const isExpanded = details.classList.contains('expanded');
    
    if (isExpanded) {
        details.classList.remove('expanded');
        button.classList.remove('active');
    } else {
        details.classList.add('expanded');
        button.classList.add('active');
    }
}
</script>


