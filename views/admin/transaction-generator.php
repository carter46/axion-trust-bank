<?php
$pageTitle = 'Transaction History Generator - Admin';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAdmin();

include __DIR__ . '/../../includes/head.php';
include __DIR__ . '/../../includes/admin-sidebar.php';
include __DIR__ . '/../../includes/admin-modals.php';

$defaultCurrency = getSiteDefaultCurrency();
$templateReady = !empty($template);
?>

<style>
.page-header { margin-bottom: 30px; }
.page-header h1 { font-size: 32px; font-weight: 700; color: #032B44; margin-bottom: 8px; }
.page-header p { color: #666; margin: 0; }

.gen-container { max-width: 1100px; margin: 0 auto; }
.gen-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
    border: 1px solid #e2e8f0;
}
.gen-card h2 { font-size: 18px; font-weight: 600; color: #032B44; margin: 0 0 16px; }
.gen-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
@media (max-width: 900px) { .gen-grid { grid-template-columns: 1fr; } }
.gen-container .form-group { margin-bottom: 16px; }
.gen-container .form-group label { display: block; font-weight: 600; margin-bottom: 8px; color: #374151; font-size: 14px; }
.gen-container .form-group .help { font-size: 12px; color: #6b7280; margin-top: 4px; }
.gen-container .form-control {
    width: 100%; padding: 10px 12px; border: 1px solid #d1d5db;
    border-radius: 8px; font-size: 14px; background: white;
}
.impact-row { display: flex; gap: 8px; align-items: stretch; }
.impact-sign {
    width: 56px; border: 1px solid #d1d5db; border-radius: 8px;
    background: #f8fafc; font-weight: 700; cursor: pointer; font-size: 18px;
}
.computed-balance {
    background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px;
    padding: 12px 16px; font-size: 16px; color: #166534; font-weight: 600;
}
.computed-balance.neutral { background: #f8fafc; border-color: #e2e8f0; color: #334155; }
.btn-row { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 8px; }
.gen-container .btn-sm { padding: 6px 12px; font-size: 12px; }
.alert-box { padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; }
.alert-box.warning { background: #fffbeb; border: 1px solid #fcd34d; color: #92400e; }
.alert-box.error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
.preview-table, .batch-table { width: 100%; border-collapse: collapse; font-size: 14px; }
.preview-table th, .preview-table td,
.batch-table th, .batch-table td { padding: 10px 12px; border-bottom: 1px solid #e5e7eb; text-align: left; vertical-align: middle; }
.preview-table th, .batch-table th { color: #6b7280; font-weight: 600; background: #f9fafb; }
.badge { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 12px; font-weight: 600; }
.badge-completed { background: #dcfce7; color: #166534; }
.badge-undone { background: #f1f5f9; color: #475569; }
.checkbox-row { display: flex; align-items: center; gap: 8px; }
</style>

<div class="gen-container">
    <div class="page-header">
        <h1>Transaction History Generator</h1>
        <p>Append realistic generated history without modifying existing user, ADM, or MIG transactions.</p>
    </div>

    <?php if (!$templateReady): ?>
        <div class="alert-box error">
            Transaction template tables are not seeded yet. Run
            <code>2026_03_19_safe_schema_upgrade.sql</code> (Section 7) and
            <code>2026_06_12_seed_default_transaction_template.sql</code> before using this tool.
        </div>
    <?php endif; ?>

    <div class="gen-card">
        <h2>Generation settings</h2>
        <div class="gen-grid">
            <div class="form-group">
                <label for="userSelect">User</label>
                <select id="userSelect" class="form-control" <?php echo $templateReady ? '' : 'disabled'; ?>>
                    <option value="">Select user...</option>
                    <?php foreach ($allUsers as $u): ?>
                        <option value="<?php echo (int)$u['id']; ?>">
                            <?php echo htmlspecialchars($u['full_name'] . ' (' . $u['email'] . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="accountSelect">Account</label>
                <select id="accountSelect" class="form-control" disabled>
                    <option value="">Select account...</option>
                </select>
            </div>
            <div class="form-group">
                <label>Current balance</label>
                <div id="currentBalanceDisplay" class="computed-balance neutral">—</div>
            </div>
            <div class="form-group">
                <label for="historyImpact">History impact</label>
                <div class="impact-row">
                    <button type="button" id="impactSignBtn" class="impact-sign" title="Toggle increase/decrease">+</button>
                    <input type="number" id="historyImpact" class="form-control" step="0.01" min="0.01" placeholder="200.00" value="200">
                </div>
                <div class="help">Net change applied to the account from generated history (+ increases, − decreases).</div>
            </div>
            <div class="form-group">
                <label for="startDate">Start date</label>
                <input type="date" id="startDate" class="form-control" value="<?php echo date('Y-m-d', strtotime('-90 days')); ?>">
            </div>
            <div class="form-group">
                <label for="endDate">End date</label>
                <input type="date" id="endDate" class="form-control" value="<?php echo date('Y-m-d', strtotime('-1 day')); ?>">
            </div>
            <div class="form-group">
                <label for="density">Density</label>
                <select id="density" class="form-control">
                    <option value="light">Light (25 transactions)</option>
                    <option value="normal" selected>Normal (70 transactions)</option>
                    <option value="heavy">Heavy (150 transactions)</option>
                </select>
            </div>
            <div class="form-group">
                <label>New account balance</label>
                <div id="newBalanceDisplay" class="computed-balance">—</div>
                <div class="help" id="balanceEstimateNote">Estimate only until preview is run (replace mode needs preview for exact value).</div>
            </div>
        </div>
        <div class="form-group checkbox-row">
            <input type="checkbox" id="replacePrevious" checked>
            <label for="replacePrevious" style="margin:0;font-weight:500;">Replace previous generated history for this account (GEN rows only)</label>
        </div>
        <div class="btn-row">
            <button type="button" id="previewBtn" class="btn btn-secondary" <?php echo $templateReady ? '' : 'disabled'; ?>>Preview</button>
            <button type="button" id="generateBtn" class="btn btn-primary" disabled>Generate</button>
        </div>
    </div>

    <div class="gen-card" id="previewCard" style="display:none;">
        <h2>Preview</h2>
        <div id="previewSummary"></div>
        <div id="previewWarnings"></div>
        <table class="preview-table" id="previewTable">
            <thead>
                <tr><th>Date</th><th>Description</th><th>Amount</th><th>Status</th></tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <div class="gen-card">
        <h2>Recent batches</h2>
        <table class="batch-table">
            <thead>
                <tr>
                    <th>Batch</th><th>User</th><th>Account</th><th>Impact</th><th>Count</th><th>Status</th><th>Created</th><th></th>
                </tr>
            </thead>
            <tbody id="batchTableBody">
                <?php if (empty($batches)): ?>
                    <tr><td colspan="8" style="color:#6b7280;">No batches yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($batches as $batch): ?>
                        <tr data-batch-id="<?php echo htmlspecialchars($batch['batch_id']); ?>">
                            <td><code><?php echo htmlspecialchars($batch['batch_id']); ?></code></td>
                            <td><?php echo htmlspecialchars($batch['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($batch['account_number']); ?></td>
                            <td><?php echo ($batch['history_impact'] >= 0 ? '+' : '') . formatCurrency($batch['history_impact'], $defaultCurrency, $defaultCurrency); ?></td>
                            <td><?php echo (int)$batch['transaction_count']; ?></td>
                            <td><span class="badge badge-<?php echo $batch['status'] === 'completed' ? 'completed' : 'undone'; ?>"><?php echo htmlspecialchars($batch['status']); ?></span></td>
                            <td><?php echo htmlspecialchars($batch['created_at']); ?></td>
                            <td>
                                <?php if ($batch['status'] === 'completed'): ?>
                                    <button type="button" class="btn btn-danger btn-sm undo-batch-btn" data-batch-id="<?php echo htmlspecialchars($batch['batch_id']); ?>">Undo</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
const SITE_URL = <?php echo json_encode(SITE_URL); ?>;
const DEFAULT_CURRENCY = <?php echo json_encode($defaultCurrency); ?>;

let previewSeed = '';
let idempotencyKey = '';
let impactSign = 1;
let lastPreview = null;
let pendingUndoBatchId = null;

const userSelect = document.getElementById('userSelect');
const accountSelect = document.getElementById('accountSelect');
const historyImpactInput = document.getElementById('historyImpact');
const impactSignBtn = document.getElementById('impactSignBtn');
const currentBalanceDisplay = document.getElementById('currentBalanceDisplay');
const newBalanceDisplay = document.getElementById('newBalanceDisplay');

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function formatMoney(amount) {
    const sign = amount < 0 ? '-' : '';
    return sign + new Intl.NumberFormat('en-US', { style: 'currency', currency: DEFAULT_CURRENCY }).format(Math.abs(amount));
}

function getSignedImpact() {
    const raw = parseFloat(historyImpactInput.value || '0');
    if (!raw || raw <= 0) return 0;
    return impactSign * raw;
}

function invalidatePreview() {
    lastPreview = null;
    previewSeed = '';
    idempotencyKey = '';
    document.getElementById('generateBtn').disabled = true;
    document.getElementById('previewCard').style.display = 'none';
}

function updateNewBalance() {
    const currentText = currentBalanceDisplay.dataset.value;
    if (currentText === undefined) {
        newBalanceDisplay.textContent = '—';
        return;
    }
    const current = parseFloat(currentBalanceDisplay.dataset.value || '0');
    const impact = getSignedImpact();
    if (!impact) {
        newBalanceDisplay.textContent = '—';
        return;
    }
    newBalanceDisplay.textContent = formatMoney(current + impact);
}

impactSignBtn.addEventListener('click', () => {
    impactSign = impactSign === 1 ? -1 : 1;
    impactSignBtn.textContent = impactSign === 1 ? '+' : '−';
    invalidatePreview();
    updateNewBalance();
});

['historyImpact', 'startDate', 'endDate', 'density', 'replacePrevious'].forEach(id => {
    document.getElementById(id).addEventListener('change', () => {
        invalidatePreview();
        updateNewBalance();
    });
});
historyImpactInput.addEventListener('input', () => {
    invalidatePreview();
    updateNewBalance();
});

userSelect.addEventListener('change', async () => {
    invalidatePreview();
    accountSelect.innerHTML = '<option value="">Loading...</option>';
    accountSelect.disabled = true;
    currentBalanceDisplay.textContent = '—';
    delete currentBalanceDisplay.dataset.value;
    updateNewBalance();

    const userId = parseInt(userSelect.value, 10);
    if (!userId) {
        accountSelect.innerHTML = '<option value="">Select account...</option>';
        return;
    }

    try {
        const res = await fetch(SITE_URL + '/api/get-user-accounts.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: userId })
        });
        const data = await res.json();
        accountSelect.innerHTML = '<option value="">Select account...</option>';
        if (data.success && Array.isArray(data.accounts)) {
            data.accounts.forEach(acc => {
                const opt = document.createElement('option');
                opt.value = acc.id;
                opt.textContent = `${acc.account_type} (${acc.account_number}) — ${formatMoney(parseFloat(acc.balance || 0))}`;
                opt.dataset.balance = acc.balance;
                accountSelect.appendChild(opt);
            });
            accountSelect.disabled = false;
        } else {
            showToast(data.message || 'Failed to load accounts.', 'error');
        }
    } catch (e) {
        showToast('Failed to load accounts.', 'error');
    }
});

accountSelect.addEventListener('change', () => {
    invalidatePreview();
    const opt = accountSelect.selectedOptions[0];
    if (!opt || !opt.value) {
        currentBalanceDisplay.textContent = '—';
        delete currentBalanceDisplay.dataset.value;
    } else {
        const bal = parseFloat(opt.dataset.balance || '0');
        currentBalanceDisplay.textContent = formatMoney(bal);
        currentBalanceDisplay.dataset.value = String(bal);
    }
    updateNewBalance();
});

function buildPayload() {
    return {
        user_id: parseInt(userSelect.value, 10),
        account_id: parseInt(accountSelect.value, 10),
        start_date: document.getElementById('startDate').value,
        end_date: document.getElementById('endDate').value,
        density: document.getElementById('density').value,
        history_impact: getSignedImpact(),
        preview_seed: previewSeed || undefined,
        idempotency_key: idempotencyKey || undefined,
        replace_previous: document.getElementById('replacePrevious').checked
    };
}

document.getElementById('previewBtn').addEventListener('click', async () => {
    const payload = buildPayload();
    if (!payload.user_id || !payload.account_id || !payload.history_impact) {
        showToast('Select user, account, and enter a non-zero history impact.', 'error');
        return;
    }

    try {
        const res = await fetch(SITE_URL + '/api/admin-preview-transaction-history.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (!data.success) {
            showToast(data.message || 'Preview failed.', 'error');
            return;
        }

        lastPreview = data;
        previewSeed = data.preview_seed;
        idempotencyKey = 'gen-' + payload.account_id + '-' + Date.now() + '-' + Math.random().toString(36).slice(2, 10);

        document.getElementById('previewCard').style.display = 'block';
        document.getElementById('previewSummary').innerHTML =
            `<p><strong>${data.transaction_count}</strong> transactions |
            Current ${formatMoney(data.previous_balance)} → New ${formatMoney(data.new_account_balance)}
            (impact ${data.history_impact >= 0 ? '+' : ''}${formatMoney(data.history_impact)})</p>`;
        newBalanceDisplay.textContent = formatMoney(data.new_account_balance);

        const warningsEl = document.getElementById('previewWarnings');
        let warnHtml = '';
        if (data.duplicate_batch_warning) {
            warnHtml += `<div class="alert-box warning">${escapeHtml(data.duplicate_batch_warning.message)} (${escapeHtml(data.duplicate_batch_warning.batch_id)})</div>`;
        }
        if (Array.isArray(data.warnings)) {
            data.warnings.forEach(w => { warnHtml += `<div class="alert-box warning">${escapeHtml(w)}</div>`; });
        }
        warningsEl.innerHTML = warnHtml;

        const tbody = document.querySelector('#previewTable tbody');
        tbody.innerHTML = '';
        (data.sample_transactions || []).forEach(row => {
            const tr = document.createElement('tr');
            tr.innerHTML = `<td>${escapeHtml(row.date)}</td><td>${escapeHtml(row.description)}</td><td>${escapeHtml(row.signed_display)}</td><td>${escapeHtml(row.status)}</td>`;
            tbody.appendChild(tr);
        });

        document.getElementById('generateBtn').disabled = false;
        showToast('Preview ready. Review sample rows, then Generate.', 'success');
    } catch (e) {
        showToast('Preview request failed.', 'error');
    }
});

document.getElementById('generateBtn').addEventListener('click', async () => {
    if (!lastPreview) {
        showToast('Run preview first.', 'error');
        return;
    }

    const payload = buildPayload();
    payload.idempotency_key = idempotencyKey;
    payload.preview_seed = previewSeed;

    showModal(
        'Confirm generation',
        `Generate ${lastPreview.transaction_count} transactions?\n\nBalance: ${formatMoney(lastPreview.previous_balance)} → ${formatMoney(lastPreview.new_account_balance)}\nHistory impact: ${lastPreview.history_impact >= 0 ? '+' : ''}${formatMoney(lastPreview.history_impact)}`,
        'confirm',
        async () => {
            document.getElementById('generateBtn').disabled = true;
            try {
                const res = await fetch(SITE_URL + '/api/admin-generate-transaction-history.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (!data.success) {
                    showToast(data.message || 'Generate failed.', 'error');
                    document.getElementById('generateBtn').disabled = false;
                    return;
                }
                showToast(data.message + ' Batch: ' + data.batch_id, 'success');
                setTimeout(() => window.location.reload(), 1200);
            } catch (e) {
                showToast('Generate request failed.', 'error');
                document.getElementById('generateBtn').disabled = false;
            }
        }
    );
});

async function undoBatch(batchId, confirmWithActivity = false) {
    const res = await fetch(SITE_URL + '/api/admin-undo-transaction-batch.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ batch_id: batchId, confirm_with_activity: confirmWithActivity })
    });
    return { status: res.status, data: await res.json() };
}

document.querySelectorAll('.undo-batch-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
        const batchId = btn.dataset.batchId;
        showModal('Undo batch', 'Undo batch ' + batchId + '?', 'confirm', async () => {
            const result = await undoBatch(batchId, false);
            if (result.status === 409 && result.data.requires_confirmation) {
                pendingUndoBatchId = batchId;
                const impact = parseFloat(result.data.history_impact || 0);
                const current = parseFloat(result.data.current_balance || 0);
                const newBal = current - impact;
                showModal(
                    'Confirm forced undo',
                    `Additional activity exists after this batch.\n\nCurrent balance: ${formatMoney(current)}\nHistory impact to reverse: ${impact >= 0 ? '+' : ''}${formatMoney(impact)}\nBalance after undo: ${formatMoney(newBal)}\n\nLater transactions/adjustments will be preserved.`,
                    'confirm',
                    async () => {
                        const forced = await undoBatch(pendingUndoBatchId, true);
                        pendingUndoBatchId = null;
                        if (forced.data.success) {
                            showToast(forced.data.message, 'success');
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            showToast(forced.data.message || 'Forced undo failed.', 'error');
                        }
                    }
                );
                return;
            }

            if (result.data.success) {
                showToast(result.data.message, 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showToast(result.data.message || 'Undo failed.', 'error');
            }
        });
    });
});
</script>

</div></div></div>
