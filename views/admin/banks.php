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
$banks = $stmt->fetchAll();

// Group banks by region
$banksByRegion = [];
foreach ($banks as $bank) {
    $banksByRegion[$bank['region']][] = $bank;
}

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

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
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

.region-section {
    margin-bottom: 30px;
}

.region-title {
    font-size: 20px;
    font-weight: 600;
    color: #1a73e8;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #1a73e8;
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
            <p style="color: #6c757d;">Add and manage international banks for wire transfers</p>
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
            <div class="stat-number"><?php echo count($banksByRegion); ?></div>
            <div class="stat-label">Regions Covered</div>
        </div>
    </div>
    
    <?php 
    $regionNames = [
        'north-america' => 'North America',
        'south-america' => 'South America',
        'europe' => 'Europe',
        'asia' => 'Asia',
        'africa' => 'Africa',
        'oceania' => 'Oceania',
        'middle-east' => 'Middle East'
    ];
    
    foreach ($banksByRegion as $region => $regionBanks): 
    ?>
        <div class="banks-card region-section">
            <h2 class="region-title"><?php echo $regionNames[$region] ?? ucfirst($region); ?> (<?php echo count($regionBanks); ?> banks)</h2>
            
            <table class="banks-table">
                <thead>
                    <tr>
                        <th>Bank Name</th>
                        <th>Country</th>
                        <th>SWIFT Code</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($regionBanks as $bank): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($bank['name']); ?></td>
                            <td><?php echo htmlspecialchars($bank['country']); ?></td>
                            <td><?php echo htmlspecialchars($bank['swift_code'] ?? '-'); ?></td>
                            <td>
                                <button class="btn-delete" onclick="confirmDelete(<?php echo $bank['id']; ?>, '<?php echo addslashes($bank['name']); ?>')">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endforeach; ?>
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
?>
<script>
    const countries = <?php echo json_encode($banksRegionCountryMap); ?>;
    
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
            `Are you sure you want to delete "${bankName}"? This action cannot be undone.`,
            'danger',
            function() {
                window.location.href = '?delete=' + bankId;
            }
        );
    }
    
    // Close modal when clicking outside
    document.getElementById('addBankModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeAddModal();
        }
    });
    
    // Reset form and close modal on successful submission
    <?php if ($successMessage): ?>
    document.addEventListener('DOMContentLoaded', function() {
        // Close modal if it was open
        closeAddModal();
        
        // Reset form
        const form = document.querySelector('#addBankModal form');
        if (form) {
            form.reset();
            // Reset country dropdown
            const countrySelect = document.getElementById('country');
            if (countrySelect) {
                countrySelect.innerHTML = '<option value="">Select country</option>';
            }
        }
    });
    <?php endif; ?>
    
    // Validate form before submission
    document.querySelector('#addBankModal form').addEventListener('submit', function(e) {
        const region = document.getElementById('region').value;
        const country = document.getElementById('country').value;
        const bankName = document.getElementById('bank_name').value;
        
        if (!region || !country || !bankName) {
            e.preventDefault();
            alert('Please fill in all required fields (Region, Country, and Bank Name).');
            return false;
        }
        
        // Ensure country is selected (not just the default option)
        if (country === '') {
            e.preventDefault();
            alert('Please select a country from the dropdown.');
            return false;
        }
        
        return true;
    });
</script>


