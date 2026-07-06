<?php 
$pageTitle = 'Create Investment Product - Admin - ' . getSiteName();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

// Include head and admin sidebar
include __DIR__ . '/../../includes/head.php';
include __DIR__ . '/../../includes/admin-sidebar.php';
?>

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

.form-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 24px;
    padding: 30px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.form-group {
    margin-bottom: 24px;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #374151;
}

.form-control, .form-select {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 16px;
}

.form-control:focus, .form-select:focus {
    outline: none;
    border-color: #032B44;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-actions {
    display: flex;
    gap: 12px;
    margin-top: 30px;
}

.btn {
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    border: none;
    text-decoration: none;
    display: inline-block;
}

.btn-primary {
    background: linear-gradient(135deg, #032B44 0%, #024a6b 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(3, 43, 68, 0.3);
}

.btn-secondary {
    background: #e5e7eb;
    color: #374151;
}

.btn-secondary:hover {
    background: #d1d5db;
}

.roi-mode-section {
    background: #f9fafb;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="page-header">
    <h1>Create Investment Product</h1>
    <p style="color: #666;">Add a new investment product for users</p>
</div>

<?php if (isset($_SESSION['error'])): ?>
<div style="background: #fee2e2; border: 2px solid #ef4444; border-radius: 12px; padding: 16px; margin-bottom: 24px; color: #991b1b;">
    <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
</div>
<?php endif; ?>

<div class="form-card">
    <form method="POST" action="<?php echo SITE_URL; ?>/admin/investment-create" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
        
        <div class="form-group">
            <label class="form-label">Title *</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Type *</label>
                <select name="type" class="form-select" required>
                    <option value="stocks">Stocks</option>
                    <option value="forex">Forex</option>
                    <option value="crypto">Crypto</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Status *</label>
                <select name="status" class="form-select" required>
                    <option value="draft">Draft</option>
                    <option value="active">Active</option>
                    <option value="paused">Paused</option>
                    <option value="closed">Closed</option>
                </select>
            </div>
        </div>
        
          <div class="form-group">
              <label class="form-label">Product Image</label>
              <input type="file" name="product_image" id="product_image" class="form-control" accept="image/*" onchange="previewImage(this)">
              <small style="color: #6b7280; display: block; margin-top: 4px;">Upload an image or use URL below (JPEG, PNG, GIF - Max 5MB)</small>
              
              <div id="imagePreview" style="margin-top: 12px; display: none;">
                  <img id="previewImg" src="" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 2px solid #e5e7eb;">
              </div>
              
              <div style="margin-top: 12px; text-align: center; color: #6b7280;">OR</div>
              
              <input type="url" name="image_url" class="form-control" style="margin-top: 12px;" placeholder="https://example.com/image.jpg">
              <small style="color: #6b7280; display: block; margin-top: 4px;">Enter image URL if not uploading file</small>
          </div>
        
        <div class="form-group">
            <label class="form-label">Short Description</label>
            <textarea name="short_description" class="form-control" rows="2"></textarea>
        </div>
        
        <div class="form-group">
            <label class="form-label">Full Description</label>
            <textarea name="full_description" class="form-control" rows="6"></textarea>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Minimum Amount *</label>
                <input type="number" name="min_amount" class="form-control" step="0.01" min="0" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Maximum Amount</label>
                <input type="number" name="max_amount" class="form-control" step="0.01" min="0">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Min Duration (days) *</label>
                <input type="number" name="min_duration_days" class="form-control" min="1" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Max Duration (days)</label>
                <input type="number" name="max_duration_days" class="form-control" min="1">
            </div>
        </div>
        
        <div class="roi-mode-section">
            <label class="form-label">ROI Configuration *</label>
            <div class="form-group">
                <select name="roi_mode" class="form-select" required onchange="toggleROIMode()">
                    <option value="fixed_daily">Fixed Daily ROI</option>
                    <option value="annual">Annual ROI (converted to daily)</option>
                </select>
            </div>
            
            <div id="dailyROISection">
                <div class="form-group">
                    <label class="form-label">Daily ROI Percentage *</label>
                    <input type="number" name="daily_percent" class="form-control" step="0.0001" min="0" placeholder="0.05">
                    <small style="color: #6b7280;">e.g., 0.05 for 0.05% daily</small>
                </div>
            </div>
            
            <div id="annualROISection" style="display: none;">
                <div class="form-group">
                    <label class="form-label">Annual ROI Percentage *</label>
                    <input type="number" name="annual_percent" class="form-control" step="0.01" min="0" placeholder="18.25">
                    <small style="color: #6b7280;">e.g., 18.25 for 18.25% annual (≈ 0.05% daily)</small>
                </div>
            </div>
            
            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="compound" value="1">
                    <span>Enable Compounding (ROI added to principal daily)</span>
                </label>
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">Payout Type *</label>
            <select name="payout_type" class="form-select" required>
                <option value="compound_daily">Compound Daily</option>
                <option value="simple_daily">Simple Daily Accrual</option>
                <option value="payout_at_maturity">Payout at Maturity</option>
            </select>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Start Date</label>
                <input type="datetime-local" name="start_date" class="form-control">
            </div>
            
            <div class="form-group">
                <label class="form-label">End Date</label>
                <input type="datetime-local" name="end_date" class="form-control">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Total Capacity</label>
                <input type="number" name="capacity_total" class="form-control" step="0.01" min="0">
                <small style="color: #6b7280;">Maximum total investment allowed (leave empty for unlimited)</small>
            </div>
            
            <div class="form-group">
                <label class="form-label">Per-User Maximum</label>
                <input type="number" name="per_user_max" class="form-control" step="0.01" min="0">
                <small style="color: #6b7280;">Maximum investment per user (leave empty for unlimited)</small>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Risk Level</label>
                <select name="risk_level" class="form-select">
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Display Order</label>
                <input type="number" name="display_order" class="form-control" value="0">
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Create Product</button>
            <a href="<?php echo SITE_URL; ?>/admin/investments" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
function toggleROIMode() {
    const mode = document.querySelector('select[name="roi_mode"]').value;       
    document.getElementById('dailyROISection').style.display = mode === 'fixed_daily' ? 'block' : 'none';                                                       
    document.getElementById('annualROISection').style.display = mode === 'annual' ? 'block' : 'none';                                                           
}

function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
    }
}
</script>


