<?php 
$pageTitle = 'Manage Banks - Admin - Octobank';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

// Ensure admin access
requireLogin();
requireAdmin();

$db = Database::getInstance();
$userId = $_SESSION['user_id'];

// Success and error messages are now handled via session in the controller
$successMessage = isset($_SESSION['success']) ? $_SESSION['success'] : '';
$errorMessage = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['success'], $_SESSION['error']);

// Handle Delete Bank
if (isset($_GET['delete'])) {
    $bankId = intval($_GET['delete']);
    $sql = "DELETE FROM banks WHERE id = ?";
    $result = $db->query($sql, [$bankId]);
    
    if ($result) {
        $_SESSION['success'] = 'Bank deleted successfully!';
        logActivity($userId, 'bank_deleted', "Deleted bank ID: $bankId");
        redirect('/admin/banks');
    } else {
        $_SESSION['error'] = 'Failed to delete bank.';
        redirect('/admin/banks');
    }
}

// Get all banks
$sql = "SELECT * FROM banks ORDER BY country ASC, name ASC";
$stmt = $db->query($sql);
$banks = $stmt ? $stmt->fetchAll() : [];

$regionNames = [
    'north-america' => 'North America',
    'south-america' => 'South America',
    'europe' => 'Europe',
    'asia' => 'Asia',
    'africa' => 'Africa',
    'oceania' => 'Oceania',
    'middle-east' => 'Middle East',
];

// Nested: region => country => banks[]
$banksTree = [];
$uniqueCountries = [];
foreach ($banks as $bank) {
    $region = $bank['region'] ?: 'other';
    $country = $bank['country'] ?: 'Unknown';
    if (!isset($banksTree[$region])) {
        $banksTree[$region] = [];
    }
    if (!isset($banksTree[$region][$country])) {
        $banksTree[$region][$country] = [];
    }
    $banksTree[$region][$country][] = $bank;
    $uniqueCountries[$country] = true;
}

// Stable region order
$orderedRegions = [];
foreach (array_keys($regionNames) as $regionKey) {
    if (isset($banksTree[$regionKey])) {
        $orderedRegions[$regionKey] = $banksTree[$regionKey];
        unset($banksTree[$regionKey]);
    }
}
foreach ($banksTree as $regionKey => $countries) {
    $orderedRegions[$regionKey] = $countries;
}

// Sort countries within each region
foreach ($orderedRegions as $regionKey => &$countriesMap) {
    ksort($countriesMap, SORT_NATURAL | SORT_FLAG_CASE);
}
unset($countriesMap);

// Include head
include __DIR__ . '/../../includes/head.php';

// Include sidebar and main structure
include __DIR__ . '/../../includes/admin-sidebar.php';
include __DIR__ . '/../../includes/admin-modals.php';
?>

<style>
.content-area {
    background: #f5f7fa;
    min-height: 100vh;
    padding: 20px;
}

.banks-container {
    max-width: 1400px;
    margin: 0 auto;
}

.banks-header {
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

.banks-title {
    font-size: 32px;
    font-weight: 700;
    color: #2d3748;
}

.btn-add {
    background: linear-gradient(135deg, #1a73e8 0%, #0d62d3 100%);
    color: white;
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(26, 115, 232, 0.3);
}

.btn-add:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(26, 115, 232, 0.4);
}

.banks-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
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
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(26, 115, 232, 0.4);
}

.banks-table {
    width: 100%;
    border-collapse: collapse;
}

.banks-table th {
    background: #f7fafc;
    padding: 12px;
    text-align: left;
    font-weight: 600;
    color: #4a5568;
    border-bottom: 2px solid #e2e8f0;
}

.banks-table td {
    padding: 12px;
    border-bottom: 1px solid #e2e8f0;
}

.btn-delete {
    background: #dc2626;
    color: white;
    padding: 6px 12px;
    border: none;
    border-radius: 4px;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-delete:hover {
    background: #b91c1c;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: linear-gradient(135deg, #1a73e8 0%, #0d62d3 100%);
    color: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(26, 115, 232, 0.3);
}

.stat-number {
    font-size: 36px;
    font-weight: 700;
    margin-bottom: 8px;
}

.stat-label {
    font-size: 14px;
    opacity: 0.9;
}

.banks-breadcrumb {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    margin-bottom: 18px;
    font-size: 14px;
    color: #64748b;
}

.banks-breadcrumb button {
    background: none;
    border: none;
    color: #1a73e8;
    font-weight: 600;
    cursor: pointer;
    padding: 0;
    font-size: 14px;
}

.banks-breadcrumb button:hover {
    text-decoration: underline;
}

.banks-breadcrumb .sep {
    color: #94a3b8;
}

.banks-breadcrumb .current {
    color: #0f172a;
    font-weight: 700;
}

.browse-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 14px;
}

.browse-card {
    display: flex;
    flex-direction: column;
    gap: 8px;
    text-align: left;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px 18px;
    cursor: pointer;
    transition: all 0.2s ease;
    width: 100%;
    font: inherit;
    color: inherit;
}

.browse-card:hover {
    border-color: #1a73e8;
    background: #eff6ff;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(26, 115, 232, 0.12);
}

.browse-card__title {
    font-size: 16px;
    font-weight: 700;
    color: #1e293b;
}

.browse-card__meta {
    font-size: 13px;
    color: #64748b;
}

.browse-card__arrow {
    margin-top: 4px;
    color: #1a73e8;
    font-size: 13px;
    font-weight: 600;
}

.panel-title {
    font-size: 22px;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 16px;
}

.banks-empty {
    padding: 28px;
    text-align: center;
    color: #64748b;
    background: #f8fafc;
    border-radius: 12px;
    border: 1px dashed #cbd5e1;
}

.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 10040;
    backdrop-filter: blur(5px);
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
    max-width: 600px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
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

@media (max-width: 768px) {
    .banks-header {
        flex-direction: column;
        align-items: stretch;
    }
    
    .btn-add {
        width: 100%;
    }
    
    .banks-table {
        font-size: 14px;
    }
    
    .banks-table th, .banks-table td {
        padding: 8px;
    }
}
</style>

<div class="banks-container">
    <div class="banks-header">
        <div>
            <h1 class="banks-title">Manage Banks</h1>
            <p style="color: #6c757d;">Browse by region → country, then manage banks</p>
        </div>
        <button class="btn-add" onclick="openAddModal()">+ Add Bank</button>
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
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?php echo count($banks); ?></div>
            <div class="stat-label">Total Banks</div>
        </div>
        <div class="stat-card" style="background: linear-gradient(135deg, #059669 0%, #047857 100%);">
            <div class="stat-number"><?php echo count($orderedRegions); ?></div>
            <div class="stat-label">Regions Covered</div>
        </div>
        <div class="stat-card" style="background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);">
            <div class="stat-number"><?php echo count($uniqueCountries); ?></div>
            <div class="stat-label">Countries Covered</div>
        </div>
    </div>

    <div class="banks-card">
        <div class="banks-breadcrumb" id="banksBreadcrumb"></div>

        <!-- Level 1: Regions -->
        <div id="regionsPanel">
            <h2 class="panel-title">Regions</h2>
            <?php if (empty($orderedRegions)): ?>
                <div class="banks-empty">No banks found. Add a bank to get started.</div>
            <?php else: ?>
                <div class="browse-grid">
                    <?php foreach ($orderedRegions as $regionKey => $countriesMap):
                        $regionBankCount = 0;
                        foreach ($countriesMap as $countryBanks) {
                            $regionBankCount += count($countryBanks);
                        }
                        $regionLabel = $regionNames[$regionKey] ?? ucwords(str_replace('-', ' ', $regionKey));
                    ?>
                        <button type="button" class="browse-card"
                                onclick="openRegion(<?php echo htmlspecialchars(json_encode($regionKey), ENT_QUOTES, 'UTF-8'); ?>)">
                            <span class="browse-card__title"><?php echo htmlspecialchars($regionLabel); ?></span>
                            <span class="browse-card__meta">
                                <?php echo count($countriesMap); ?> countries · <?php echo $regionBankCount; ?> banks
                            </span>
                            <span class="browse-card__arrow">View countries →</span>
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Level 2: Countries in region -->
        <div id="countriesPanel" style="display:none;">
            <h2 class="panel-title" id="countriesPanelTitle">Countries</h2>
            <div class="browse-grid" id="countriesGrid"></div>
        </div>

        <!-- Level 3: Banks in country -->
        <div id="banksPanel" style="display:none;">
            <h2 class="panel-title" id="banksPanelTitle">Banks</h2>
            <div id="banksTableWrap"></div>
        </div>
    </div>
</div>

<!-- Add Bank Modal -->
<div class="modal" id="addBankModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Add New Bank</h2>
            <button class="btn-close" onclick="closeAddModal()">&times;</button>
        </div>
        
        <form method="POST" action="<?php echo SITE_URL; ?>/admin/banks">
            <div class="form-group">
                <label class="form-label" for="region">Region *</label>
                <select class="form-select" id="region" name="region" required onchange="updateCountries()">
                    <option value="">Select region</option>
                    <option value="north-america">North America</option>
                    <option value="south-america">South America</option>
                    <option value="europe">Europe</option>
                    <option value="asia">Asia</option>
                    <option value="africa">Africa</option>
                    <option value="oceania">Oceania</option>
                    <option value="middle-east">Middle East</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="country">Country *</label>
                <select class="form-select" id="country" name="country" required>
                    <option value="">Select country</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="bank_name">Bank Name *</label>
                <input type="text" class="form-input" id="bank_name" name="bank_name" required placeholder="Enter bank name">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="swift_code">SWIFT Code (Optional)</label>
                <input type="text" class="form-input" id="swift_code" name="swift_code" placeholder="Enter SWIFT code">
            </div>
            
            <button type="submit" name="add_bank" class="btn-submit">Add Bank</button>
        </form>
    </div>
</div>

<?php
require_once __DIR__ . '/../../includes/countries.php';

$banksRegionCountryMap = [];
foreach (getCountriesByRegion() as $region => $countries) {
    $banksRegionCountryMap[$region] = array_map(function ($c) {
        return ['code' => $c['code'], 'name' => $c['name']];
    }, $countries);
}

$banksTreeJson = [];
foreach ($orderedRegions as $regionKey => $countriesMap) {
    $banksTreeJson[$regionKey] = [
        'label' => $regionNames[$regionKey] ?? ucwords(str_replace('-', ' ', $regionKey)),
        'countries' => [],
    ];
    foreach ($countriesMap as $countryName => $countryBanks) {
        $banksTreeJson[$regionKey]['countries'][$countryName] = array_map(function ($b) {
            return [
                'id' => (int)$b['id'],
                'name' => $b['name'],
                'swift_code' => $b['swift_code'] ?? '',
                'is_active' => (int)($b['is_active'] ?? 1),
            ];
        }, $countryBanks);
    }
}
?>
<script>
    const countries = <?php echo json_encode($banksRegionCountryMap); ?>;
    const banksTree = <?php echo json_encode($banksTreeJson); ?>;
    const siteUrl = <?php echo json_encode(SITE_URL); ?>;

    let currentRegion = null;
    let currentCountry = null;

    function escapeHtml(str) {
        return String(str == null ? '' : str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function showPanel(panelId) {
        ['regionsPanel', 'countriesPanel', 'banksPanel'].forEach(function (id) {
            const el = document.getElementById(id);
            if (el) el.style.display = (id === panelId) ? 'block' : 'none';
        });
    }

    function renderBreadcrumb() {
        const el = document.getElementById('banksBreadcrumb');
        if (!el) return;
        const parts = [];
        parts.push('<button type="button" onclick="showRegions()">All Regions</button>');

        if (currentRegion && banksTree[currentRegion]) {
            parts.push('<span class="sep">/</span>');
            if (currentCountry) {
                parts.push('<button type="button" onclick="openRegion(\'' + currentRegion.replace(/'/g, "\\'") + '\')">' +
                    escapeHtml(banksTree[currentRegion].label) + '</button>');
                parts.push('<span class="sep">/</span>');
                parts.push('<span class="current">' + escapeHtml(currentCountry) + '</span>');
            } else {
                parts.push('<span class="current">' + escapeHtml(banksTree[currentRegion].label) + '</span>');
            }
        }

        el.innerHTML = parts.join(' ');
    }

    function showRegions() {
        currentRegion = null;
        currentCountry = null;
        showPanel('regionsPanel');
        renderBreadcrumb();
    }

    function openRegion(regionKey) {
        currentRegion = regionKey;
        currentCountry = null;
        const region = banksTree[regionKey];
        if (!region) return;

        document.getElementById('countriesPanelTitle').textContent =
            region.label + ' — Countries';

        const grid = document.getElementById('countriesGrid');
        const countryNames = Object.keys(region.countries || {}).sort(function (a, b) {
            return a.localeCompare(b);
        });

        if (!countryNames.length) {
            grid.innerHTML = '<div class="banks-empty">No countries with banks in this region.</div>';
        } else {
            grid.innerHTML = countryNames.map(function (countryName) {
                const list = region.countries[countryName] || [];
                return '<button type="button" class="browse-card" onclick="openCountry(' +
                    JSON.stringify(regionKey) + ', ' + JSON.stringify(countryName) + ')">' +
                    '<span class="browse-card__title">' + escapeHtml(countryName) + '</span>' +
                    '<span class="browse-card__meta">' + list.length + ' bank' + (list.length === 1 ? '' : 's') + '</span>' +
                    '<span class="browse-card__arrow">View banks →</span>' +
                    '</button>';
            }).join('');
        }

        showPanel('countriesPanel');
        renderBreadcrumb();
    }

    function openCountry(regionKey, countryName) {
        currentRegion = regionKey;
        currentCountry = countryName;
        const region = banksTree[regionKey];
        const list = (region && region.countries && region.countries[countryName]) ? region.countries[countryName] : [];

        document.getElementById('banksPanelTitle').textContent =
            countryName + ' — Banks (' + list.length + ')';

        const wrap = document.getElementById('banksTableWrap');
        if (!list.length) {
            wrap.innerHTML = '<div class="banks-empty">No banks for this country.</div>';
        } else {
            wrap.innerHTML =
                '<table class="banks-table">' +
                '<thead><tr><th>Bank Name</th><th>SWIFT Code</th><th>Status</th><th>Action</th></tr></thead>' +
                '<tbody>' +
                list.map(function (bank) {
                    const status = bank.is_active ? 'Active' : 'Inactive';
                    return '<tr>' +
                        '<td>' + escapeHtml(bank.name) + '</td>' +
                        '<td>' + escapeHtml(bank.swift_code || '-') + '</td>' +
                        '<td>' + status + '</td>' +
                        '<td><button class="btn-delete" onclick="confirmDelete(' + bank.id + ', ' +
                        JSON.stringify(bank.name) + ')">Delete</button></td>' +
                        '</tr>';
                }).join('') +
                '</tbody></table>';
        }

        showPanel('banksPanel');
        renderBreadcrumb();
    }

    function openAddModal() {
        document.getElementById('addBankModal').classList.add('active');
    }
    
    function closeAddModal() {
        document.getElementById('addBankModal').classList.remove('active');
    }
    
    function updateCountries() {
        const region = document.getElementById('region').value;
        const countrySelect = document.getElementById('country');
        
        countrySelect.innerHTML = '<option value="">Select country</option>';
        
        if (region && countries[region]) {
            countries[region].forEach(function(country) {
                const option = document.createElement('option');
                option.value = country.name;
                option.textContent = country.name;
                option.dataset.code = country.code;
                countrySelect.appendChild(option);
            });
        }
    }
    
    function confirmDelete(bankId, bankName) {
        showModal(
            'Delete Bank',
            'Are you sure you want to delete "' + bankName + '"? This action cannot be undone.',
            'danger',
            function() {
                window.location.href = '?delete=' + bankId;
            }
        );
    }
    
    document.getElementById('addBankModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeAddModal();
        }
    });
    
    <?php if ($successMessage): ?>
    document.addEventListener('DOMContentLoaded', function() {
        closeAddModal();
        const form = document.querySelector('#addBankModal form');
        if (form) {
            form.reset();
            const countrySelect = document.getElementById('country');
            if (countrySelect) {
                countrySelect.innerHTML = '<option value="">Select country</option>';
            }
        }
    });
    <?php endif; ?>
    
    document.querySelector('#addBankModal form').addEventListener('submit', function(e) {
        const region = document.getElementById('region').value;
        const country = document.getElementById('country').value;
        const bankName = document.getElementById('bank_name').value;
        
        if (!region || !country || !bankName) {
            e.preventDefault();
            alert('Please fill in all required fields (Region, Country, and Bank Name).');
            return false;
        }
        
        if (country === '') {
            e.preventDefault();
            alert('Please select a country from the dropdown.');
            return false;
        }
        
        return true;
    });

    document.addEventListener('DOMContentLoaded', function () {
        showRegions();
    });
</script>


