<?php 
$pageTitle = 'Card Details - SecureBank';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

// Include head
include __DIR__ . '/../../includes/head.php';

// Include sidebar
include __DIR__ . '/../../includes/sidebar.php';

// Get user currency
$userModel = new User();
$user = $userModel->findById($_SESSION['user_id']);
$userCurrency = getUserDisplayCurrency($user);

// Card data is passed from controller
$card = $card ?? null;
$transactions = $transactions ?? [];

if (!$card) {
    redirect('/card');
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

.card-container {
    display: grid;
    grid-template-columns: 400px 1fr;
    gap: 25px;
    margin-bottom: 30px;
}

.card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 24px;
    padding: 30px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.credit-card-display {
    background: linear-gradient(135deg, #032B44 0%, #024a6b 100%);
    border-radius: 20px;
    padding: 30px;
    color: white;
    position: relative;
    overflow: hidden;
    min-height: 220px;
}

.credit-card-display::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 300px;
    height: 300px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
}

.credit-card-display.frozen-card {
    background: linear-gradient(135deg, #bdc3c7 0%, #2c3e50 100%);
    opacity: 0.8;
    filter: grayscale(30%);
}

.frozen-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    z-index: 100;
    border-radius: 20px;
    backdrop-filter: blur(2px);
}

.frozen-overlay i {
    font-size: 48px;
    color: #ffffff;
    margin-bottom: 10px;
    animation: pulse 2s infinite;
}

.frozen-overlay span {
    font-size: 24px;
    font-weight: bold;
    color: #ffffff;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

.status-active {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.status-frozen {
    background: #d1ecf1;
    color: #0c5460;
    border: 1px solid #bee5eb;
}

.status-blocked, .status-cancelled {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.status-expired {
    background: #e2e3e5;
    color: #383d41;
    border: 1px solid #d6d8db;
}

.card-chip {
    width: 50px;
    height: 40px;
    background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
    border-radius: 8px;
    margin-bottom: 30px;
}

.card-number {
    font-size: 22px;
    letter-spacing: 4px;
    margin-bottom: 25px;
    font-family: 'Courier New', monospace;
}

.card-info {
    display: flex;
    justify-content: space-between;
}

.card-holder,
.card-expiry {
    display: flex;
    flex-direction: column;
}

.card-label {
    font-size: 10px;
    text-transform: uppercase;
    opacity: 0.7;
    margin-bottom: 5px;
}

.card-value {
    font-size: 16px;
    font-weight: 600;
}

.status-badge {
    padding: 6px 14px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    text-transform: capitalize;
}

.status-active {
    background: #d1fae5;
    color: #065f46;
}

.status-frozen {
    background: #dbeafe;
    color: #1e40af;
}

.status-blocked {
    background: #fee2e2;
    color: #991b1b;
}

.info-grid {
    display: grid;
    gap: 15px;
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

.action-buttons {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
    margin-top: 20px;
}

.btn {
    padding: 12px 20px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    text-align: center;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.btn-primary {
    background: #032B44;
    color: white;
}

.btn-danger {
    background: #ef4444;
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

@media (max-width: 968px) {
    .card-container {
        grid-template-columns: 1fr;
    }
    
    .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .action-buttons {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }
    
    /* Make sure delete button is always visible and spans full width */
    .action-buttons button:last-child,
    .delete-card-btn {
        grid-column: 1 / -1 !important;
        margin-top: 10px !important;
        width: 100% !important;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        background-color: #dc3545 !important;
        color: white !important;
    }
    
    .frozen-overlay i {
        font-size: 36px;
    }
    
    .frozen-overlay span {
        font-size: 18px;
    }
}
</style>

<div class="page-header">
    <div>
        <h1><?php echo htmlspecialchars($card['card_name']); ?></h1>
        <p style="color: #666;">Card details and transaction history</p>
        <div class="status-badge status-<?php echo $card['status']; ?>" style="margin-top: 10px; display: inline-block;">
            <?php 
            $statusText = [
                'pending' => '⏳ Pending Approval',
                'active' => '✅ Active',
                'frozen' => '❄️ Frozen',
                'blocked' => '🚫 Blocked',
                'cancelled' => '❌ Cancelled',
                'expired' => '⏰ Expired'
            ];
            echo $statusText[$card['status']] ?? ucfirst($card['status']);
            ?>
        </div>
    </div>
    <a href="<?php echo SITE_URL; ?>/card" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Back to Cards
    </a>
</div>

<div class="card-container">
    <!-- Card Display -->
    <div>
        <div class="credit-card-display <?php echo $card['status'] === 'frozen' ? 'frozen-card' : ''; ?>">
            <?php if ($card['status'] === 'frozen'): ?>
                <div class="frozen-overlay">
                    <i class="fas fa-snowflake"></i>
                    <span>FROZEN</span>
                </div>
            <?php endif; ?>
            <div class="card-chip"></div>
            <div class="card-number" id="cardNumber">
                <?php 
                    if (!empty($card['card_number_masked'])) {
                        echo $card['card_number_masked'];
                    } else {
                        echo '•••• •••• •••• ••••';
                    }
                ?>
            </div>
            <div class="card-info">
                <div class="card-holder">
                    <div class="card-label">Cardholder</div>
                    <div class="card-value"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'CARDHOLDER'); ?></div>
                </div>
                <div class="card-expiry">
                    <div class="card-label">Expires</div>
                    <div class="card-value"><?php echo date('m/y', strtotime($card['expiry_date'])); ?></div>
                </div>
            </div>
        </div>

        <div class="card" style="margin-top: 20px;">
            <h3 style="color: #032B44; margin-bottom: 20px;">Card Controls</h3>
            
            <!-- Status Alerts (separate from buttons) -->
            <?php if ($card['status'] === 'pending'): ?>
                <div class="alert alert-warning" style="margin-bottom: 20px;">
                    <i class="fas fa-clock"></i>
                    <strong>Card Pending Approval</strong><br>
                    Your card application is being reviewed by our admin team. You will be notified once it's approved.
                </div>
            <?php elseif ($card['status'] === 'frozen'): ?>
                <div class="alert alert-info" style="margin-bottom: 20px;">
                    <i class="fas fa-snowflake"></i>
                    <strong>Card Frozen</strong><br>
                    Your card is temporarily frozen and cannot be used for transactions.
                </div>
            <?php elseif ($card['status'] !== 'active'): ?>
                <div class="alert alert-danger" style="margin-bottom: 20px;">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Card <?php echo ucfirst($card['status']); ?></strong><br>
                    This card is no longer active.
                </div>
            <?php endif; ?>
            
            <!-- Action Buttons (buttons only - will use 2-column layout) -->
            <div class="action-buttons">
                <!-- Freeze/Unfreeze Button -->
                <?php if ($card['status'] === 'active'): ?>
                    <button class="btn btn-danger" onclick="freezeCard(<?php echo $card['id']; ?>)">
                        <i class="fas fa-snowflake"></i>
                        Freeze Card
                    </button>
                <?php elseif ($card['status'] === 'frozen'): ?>
                    <button class="btn btn-success" onclick="unfreezeCard(<?php echo $card['id']; ?>)">
                        <i class="fas fa-check"></i>
                        Unfreeze Card
                    </button>
                <?php endif; ?>
                
                <!-- Show Details Button (for active and frozen cards) -->
                <?php if (in_array($card['status'], ['active', 'frozen'])): ?>
                    <button class="btn btn-primary" onclick="showCardDetails()">
                        <i class="fas fa-eye"></i>
                        Show Details
                    </button>
                <?php endif; ?>
                
                <!-- Report Issue Button (for active and frozen cards) -->
                <?php if (in_array($card['status'], ['active', 'frozen'])): ?>
                    <button class="btn btn-secondary" onclick="reportIssue()">
                        <i class="fas fa-flag"></i>
                        Report Issue
                    </button>
                <?php endif; ?>
                
                <!-- Replace Card Button (for active cards only) -->
                <?php if ($card['status'] === 'active'): ?>
                    <button class="btn btn-secondary" onclick="replaceCard()">
                        <i class="fas fa-sync-alt"></i>
                        Replace Card
                    </button>
                <?php endif; ?>
                
                <!-- Delete Card Button (always visible) -->
                <button class="btn btn-danger delete-card-btn" onclick="deleteCard(<?php echo $card['id']; ?>)" style="grid-column: 1 / -1 !important; margin-top: 10px !important; width: 100% !important; display: block !important;">
                    <i class="fas fa-trash"></i>
                    <?php echo $card['status'] === 'pending' ? 'Cancel Application' : 'Delete Card'; ?>
                </button>
            </div>
        </div>
    </div>

    <!-- Card Information -->
    <div class="card">
        <h3 style="color: #032B44; margin-bottom: 20px;">Card Information</h3>
        
        <div class="info-grid">
            <div class="info-row">
                <span class="info-label">Card Type:</span>
                <span class="info-value" style="text-transform: capitalize;"><?php echo $card['card_type']; ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Status:</span>
                <span class="status-badge status-<?php echo $card['status']; ?>">
                    <?php echo ucfirst($card['status']); ?>
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Card Format:</span>
                <span class="info-value"><?php echo $card['is_virtual'] ? 'Virtual' : 'Physical'; ?></span>
            </div>
            <?php if ($card['card_type'] === 'credit'): ?>
                <div class="info-row">
                    <span class="info-label">Credit Limit:</span>
                    <span class="info-value"><?php echo formatCardAmountForUser($card['credit_limit'] ?? 0, $user, $card); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Available Credit:</span>
                    <span class="info-value"><?php echo formatCardAmountForUser(($card['credit_limit'] ?? 0) - ($card['balance'] ?? 0), $user, $card); ?></span>
                </div>
            <?php endif; ?>
            <div class="info-row">
                <span class="info-label">Daily Limit:</span>
                <span class="info-value"><?php echo formatCardAmountForUser($card['daily_limit'], $user, $card); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Monthly Limit:</span>
                <span class="info-value"><?php echo formatCardAmountForUser($card['monthly_limit'], $user, $card); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Created:</span>
                <span class="info-value"><?php echo date('M d, Y', strtotime($card['created_at'])); ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Transaction History -->
<div class="card">
    <h3 style="color: #032B44; margin-bottom: 20px;">Recent Transactions</h3>
    
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Merchant</th>
                    <th>Category</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($transactions)): ?>
                    <?php foreach ($transactions as $txn): ?>
                        <tr>
                            <td><?php echo date('M d, Y H:i', strtotime($txn['created_at'])); ?></td>
                            <td><?php echo htmlspecialchars($txn['merchant'] ?? $txn['description']); ?></td>
                            <td style="text-transform: capitalize;">
                                <?php echo $txn['expense_category'] ?? 'Other'; ?>
                            </td>
                            <td style="font-weight: 600; color: <?php echo $txn['transaction_type'] === 'debit' ? '#ef4444' : '#10b981'; ?>">
                                <?php echo $txn['transaction_type'] === 'debit' ? '-' : '+'; ?><?php echo formatTransactionAmountForUser($txn, $user, 'amount'); ?>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $txn['status']; ?>">
                                    <?php echo ucfirst($txn['status']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: #999; padding: 40px;">
                            No transactions found for this card
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// ===== PAGE LOAD DEBUGGING =====
console.log('🚀 CARD VIEW DEBUG: Page loaded, checking elements...');

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 CARD VIEW DEBUG: DOM loaded, checking button elements...');
    
    // Check if delete button exists
    const deleteButton = document.querySelector('.delete-card-btn');
    if (deleteButton) {
        console.log('✅ DELETE BUTTON DEBUG: Delete button found:', deleteButton);
        console.log('✅ DELETE BUTTON DEBUG: Button text:', deleteButton.textContent.trim());
        console.log('✅ DELETE BUTTON DEBUG: Button onclick:', deleteButton.onclick);
        console.log('✅ DELETE BUTTON DEBUG: Button style:', deleteButton.style.cssText);
        console.log('✅ DELETE BUTTON DEBUG: Button computed style:', window.getComputedStyle(deleteButton));
    } else {
        console.error('❌ DELETE BUTTON DEBUG: Delete button NOT FOUND!');
    }
    
    // Check if freeze/unfreeze buttons exist
    const freezeButtons = document.querySelectorAll('button[onclick*="freezeCard"], button[onclick*="unfreezeCard"]');
    console.log('❄️ FREEZE BUTTONS DEBUG: Found freeze/unfreeze buttons:', freezeButtons.length);
    freezeButtons.forEach((btn, index) => {
        console.log(`❄️ FREEZE BUTTON ${index + 1} DEBUG:`, btn);
        console.log(`❄️ FREEZE BUTTON ${index + 1} DEBUG: onclick:`, btn.onclick);
        console.log(`❄️ FREEZE BUTTON ${index + 1} DEBUG: text:`, btn.textContent.trim());
    });
    
    // Check card status
    const statusBadge = document.querySelector('.status-badge');
    if (statusBadge) {
        console.log('📊 CARD STATUS DEBUG: Card status:', statusBadge.textContent.trim());
        console.log('📊 CARD STATUS DEBUG: Status classes:', statusBadge.className);
    }
    
    // Check if frozen overlay exists
    const frozenOverlay = document.querySelector('.frozen-overlay');
    if (frozenOverlay) {
        console.log('❄️ FROZEN OVERLAY DEBUG: Frozen overlay found:', frozenOverlay);
        console.log('❄️ FROZEN OVERLAY DEBUG: Overlay style:', frozenOverlay.style.cssText);
        console.log('❄️ FROZEN OVERLAY DEBUG: Overlay computed style:', window.getComputedStyle(frozenOverlay));
    } else {
        console.log('❄️ FROZEN OVERLAY DEBUG: No frozen overlay found (card is not frozen)');
    }
    
    // Check card display
    const cardDisplay = document.querySelector('.credit-card-display');
    if (cardDisplay) {
        console.log('💳 CARD DISPLAY DEBUG: Card display found:', cardDisplay);
        console.log('💳 CARD DISPLAY DEBUG: Card classes:', cardDisplay.className);
        console.log('💳 CARD DISPLAY DEBUG: Card style:', cardDisplay.style.cssText);
    }
    
    console.log('🚀 CARD VIEW DEBUG: Element check complete');
});

function showCardDetails() {
    fetch('<?php echo SITE_URL; ?>/card/showDetails/<?php echo $card['id']; ?>')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const formatted = data.card_number.match(/.{1,4}/g).join(' ');
                document.getElementById('cardNumber').textContent = formatted;
                
                alert(`Card Number: ${data.card_number}\nCVV: ${data.cvv}\nExpiry: ${data.expiry}`);
                
                // Hide after 10 seconds
                setTimeout(() => {
                    document.getElementById('cardNumber').textContent = '<?php echo $card['card_number_masked'] ?? '•••• •••• •••• ••••'; ?>';
                }, 10000);
            } else {
                alert(data.message || 'Failed to show card details');
            }
        })
        .catch(error => {
            alert('An error occurred');
        });
}

function freezeCard(cardId) {
    console.log('❄️ FREEZE CARD DEBUG: Function called with cardId:', cardId);
    
    if (!confirm('Are you sure you want to freeze this card? You can unfreeze it anytime.')) {
        console.log('❄️ FREEZE CARD DEBUG: User cancelled freeze confirmation');
        return;
    }
    
    console.log('❄️ FREEZE CARD DEBUG: Proceeding with freeze, making API call...');
    
    fetch('<?php echo SITE_URL; ?>/api/card-freeze.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            card_id: cardId
        })
    })
    .then(response => {
        console.log('❄️ FREEZE CARD DEBUG: API response status:', response.status);
        console.log('❄️ FREEZE CARD DEBUG: API response headers:', response.headers);
        return response.json();
    })
    .then(data => {
        console.log('❄️ FREEZE CARD DEBUG: API response data:', data);
        if (data.success) {
            console.log('❄️ FREEZE CARD DEBUG: Freeze successful, reloading page...');
            alert('Card frozen successfully');
            location.reload();
        } else {
            console.log('❄️ FREEZE CARD DEBUG: Freeze failed:', data.message);
            alert(data.message || 'Failed to freeze card');
        }
    })
    .catch(error => {
        console.error('❄️ FREEZE CARD DEBUG: Error occurred:', error);
        alert('An error occurred while freezing the card');
    });
}

function unfreezeCard(cardId) {
    console.log('🔥 UNFREEZE CARD DEBUG: Function called with cardId:', cardId);
    
    console.log('🔥 UNFREEZE CARD DEBUG: Proceeding with unfreeze, making API call...');
    
    fetch('<?php echo SITE_URL; ?>/api/card-unfreeze.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            card_id: cardId
        })
    })
    .then(response => {
        console.log('🔥 UNFREEZE CARD DEBUG: API response status:', response.status);
        console.log('🔥 UNFREEZE CARD DEBUG: API response headers:', response.headers);
        return response.json();
    })
    .then(data => {
        console.log('🔥 UNFREEZE CARD DEBUG: API response data:', data);
        if (data.success) {
            console.log('🔥 UNFREEZE CARD DEBUG: Unfreeze successful, reloading page...');
            alert('Card activated successfully');
            location.reload();
        } else {
            console.log('🔥 UNFREEZE CARD DEBUG: Unfreeze failed:', data.message);
            alert(data.message || 'Failed to activate card');
        }
    })
    .catch(error => {
        console.error('🔥 UNFREEZE CARD DEBUG: Error occurred:', error);
        alert('An error occurred while activating the card');
    });
}

function reportIssue() {
    alert('Report issue feature coming soon. Please contact support.');
}

function replaceCard() {
    if (!confirm('Request a replacement card? This will deactivate your current card.')) return;
    alert('Replace card feature coming soon. Please contact support.');
}

function deleteCard(cardId) {
    console.log('🔴 DELETE CARD DEBUG: Function called with cardId:', cardId);
    
    if (!confirm('Are you sure you want to delete this card? This action cannot be undone.')) {
        console.log('🔴 DELETE CARD DEBUG: User cancelled first confirmation');
        return;
    }
    
    if (!confirm('This will permanently delete your card and all associated data. Are you absolutely sure?')) {
        console.log('🔴 DELETE CARD DEBUG: User cancelled second confirmation');
        return;
    }
    
    console.log('🔴 DELETE CARD DEBUG: Proceeding with deletion, making API call...');
    
    fetch('<?php echo SITE_URL; ?>/api/card-delete.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            card_id: cardId
        })
    })
    .then(response => {
        console.log('🔴 DELETE CARD DEBUG: API response status:', response.status);
        console.log('🔴 DELETE CARD DEBUG: API response headers:', response.headers);
        return response.json();
    })
    .then(data => {
        console.log('🔴 DELETE CARD DEBUG: API response data:', data);
        if (data.success) {
            console.log('🔴 DELETE CARD DEBUG: Deletion successful, redirecting...');
            alert('Card deleted successfully');
            window.location.href = '<?php echo SITE_URL; ?>/card';
        } else {
            console.log('🔴 DELETE CARD DEBUG: Deletion failed:', data.message);
            alert(data.message || 'Failed to delete card');
        }
    })
    .catch(error => {
        console.error('🔴 DELETE CARD DEBUG: Error occurred:', error);
        alert('An error occurred while deleting the card');
    });
}
</script>

<?php
include __DIR__ . '/../../includes/mobile-nav.php';
?>

