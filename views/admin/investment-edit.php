<?php 
$pageTitle = 'Edit Investment Product - Admin - ' . getSiteName();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

// Include head and admin sidebar
include __DIR__ . '/../../includes/head.php';
include __DIR__ . '/../../includes/admin-sidebar.php';

if (!$product) {
    redirect('/admin/investments');
    exit;
}

$roiConfig = $roiConfig ?? [];
$roiMode = $roiConfig['mode'] ?? 'fixed_daily';
?>

<style>
/* Same styles as create page */
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
    <h1>Edit Investment Product</h1>
    <p style="color: #666;">Update investment product details</p>
</div>

<?php if (isset($_SESSION['error'])): ?>
<div style="background: #fee2e2; border: 2px solid #ef4444; border-radius: 12px; padding: 16px; margin-bottom: 24px; color: #991b1b;">
    <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
</div>
<?php endif; ?>

<div class="form-card">
    <form method="POST" action="<?php echo SITE_URL; ?>/admin/investment-edit/<?php echo $product['id']; ?>" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
        
        <div class="form-group">
            <label class="form-label">Title *</label>
            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($product['title']); ?>" required>
        </div>
        
        <div class="form-group">
            <label class="form-label">Slug *</label>
            <input type="text" name="slug" class="form-control" value="<?php echo htmlspecialchars($product['slug']); ?>" required>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Type *</label>
                <select name="type" class="form-select" required>
                    <option value="stocks" <?php echo $product['type'] === 'stocks' ? 'selected' : ''; ?>>Stocks</option>
                    <option value="forex" <?php echo $product['type'] === 'forex' ? 'selected' : ''; ?>>Forex</option>
                    <option value="crypto" <?php echo $product['type'] === 'crypto' ? 'selected' : ''; ?>>Crypto</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Status *</label>
                <select name="status" class="form-select" required>
                    <option value="draft" <?php echo $product['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
                    <option value="active" <?php echo $product['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="paused" <?php echo $product['status'] === 'paused' ? 'selected' : ''; ?>>Paused</option>
                    <option value="closed" <?php echo $product['status'] === 'closed' ? 'selected' : ''; ?>>Closed</option>
                </select>
            </div>
        </div>
        
          <div class="form-group">
              <label class="form-label">Product Image</label>
              <?php if (!empty($product['image_url'])): ?>
                  <div style="margin-bottom: 12px;">
                      <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="Current Image" style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 2px solid #e5e7eb;">
                  </div>
              <?php endif; ?>
              
              <input type="file" name="product_image" id="product_image" class="form-control" accept="image/*" onchange="previewImage(this)">
              <small style="color: #6b7280; display: block; margin-top: 4px;">Upload a new image or use URL below (JPEG, PNG, GIF - Max 5MB)</small>
              
              <div id="imagePreview" style="margin-top: 12px; display: none;">
                  <img id="previewImg" src="" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 2px solid #e5e7eb;">
              </div>
              
              <div style="margin-top: 12px; text-align: center; color: #6b7280;">OR</div>
              
              <input type="url" name="image_url" class="form-control" style="margin-top: 12px;" value="<?php echo htmlspecialchars($product['image_url'] ?? ''); ?>" placeholder="https://example.com/image.jpg">
              <small style="color: #6b7280; display: block; margin-top: 4px;">Enter image URL if not uploading file (leave empty to keep current)</small>
          </div>
        
        <div class="form-group">
            <label class="form-label">Short Description</label>
            <textarea name="short_description" class="form-control" rows="2"><?php echo htmlspecialchars($product['short_description'] ?? ''); ?></textarea>
        </div>
        
        <div class="form-group">
            <label class="form-label">Full Description</label>
            <textarea name="full_description" class="form-control" rows="6"><?php echo htmlspecialchars($product['full_description'] ?? ''); ?></textarea>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Minimum Amount *</label>
                <input type="number" name="min_amount" class="form-control" step="0.01" min="0" value="<?php echo $product['min_amount']; ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Maximum Amount</label>
                <input type="number" name="max_amount" class="form-control" step="0.01" min="0" value="<?php echo $product['max_amount'] ?? ''; ?>">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Min Duration (days) *</label>
                <input type="number" name="min_duration_days" class="form-control" min="1" value="<?php echo $product['min_duration_days']; ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Max Duration (days)</label>
                <input type="number" name="max_duration_days" class="form-control" min="1" value="<?php echo $product['max_duration_days'] ?? ''; ?>">
            </div>
        </div>
        
        <div class="roi-mode-section">
            <label class="form-label">ROI Configuration *</label>
            <div class="form-group">
                <select name="roi_mode" class="form-select" required onchange="toggleROIMode()">
                    <option value="fixed_daily" <?php echo $roiMode === 'fixed_daily' ? 'selected' : ''; ?>>Fixed Daily ROI</option>
                    <option value="annual" <?php echo $roiMode === 'annual' ? 'selected' : ''; ?>>Annual ROI (converted to daily)</option>
                </select>
            </div>
            
            <div id="dailyROISection" style="display: <?php echo $roiMode === 'fixed_daily' ? 'block' : 'none'; ?>;">
                <div class="form-group">
                    <label class="form-label">Daily ROI Percentage *</label>
                    <input type="number" name="daily_percent" class="form-control" step="0.0001" min="0" value="<?php echo $roiConfig['daily_percent'] ?? ''; ?>">
                </div>
            </div>
            
            <div id="annualROISection" style="display: <?php echo $roiMode === 'annual' ? 'block' : 'none'; ?>;">
                <div class="form-group">
                    <label class="form-label">Annual ROI Percentage *</label>
                    <input type="number" name="annual_percent" class="form-control" step="0.01" min="0" value="<?php echo $roiConfig['annual_percent'] ?? ''; ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="compound" value="1" <?php echo !empty($roiConfig['compound']) ? 'checked' : ''; ?>>
                    <span>Enable Compounding</span>
                </label>
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">Payout Type *</label>
            <select name="payout_type" class="form-select" required>
                <option value="compound_daily" <?php echo $product['payout_type'] === 'compound_daily' ? 'selected' : ''; ?>>Compound Daily</option>
                <option value="simple_daily" <?php echo $product['payout_type'] === 'simple_daily' ? 'selected' : ''; ?>>Simple Daily Accrual</option>
                <option value="payout_at_maturity" <?php echo $product['payout_type'] === 'payout_at_maturity' ? 'selected' : ''; ?>>Payout at Maturity</option>
            </select>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Start Date</label>
                <input type="datetime-local" name="start_date" class="form-control" value="<?php echo $product['start_date'] ? date('Y-m-d\TH:i', strtotime($product['start_date'])) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label">End Date</label>
                <input type="datetime-local" name="end_date" class="form-control" value="<?php echo $product['end_date'] ? date('Y-m-d\TH:i', strtotime($product['end_date'])) : ''; ?>">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Total Capacity</label>
                <input type="number" name="capacity_total" class="form-control" step="0.01" min="0" value="<?php echo $product['capacity_total'] ?? ''; ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label">Per-User Maximum</label>
                <input type="number" name="per_user_max" class="form-control" step="0.01" min="0" value="<?php echo $product['per_user_max'] ?? ''; ?>">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Risk Level</label>
                <select name="risk_level" class="form-select">
                    <option value="low" <?php echo $product['risk_level'] === 'low' ? 'selected' : ''; ?>>Low</option>
                    <option value="medium" <?php echo $product['risk_level'] === 'medium' ? 'selected' : ''; ?>>Medium</option>
                    <option value="high" <?php echo $product['risk_level'] === 'high' ? 'selected' : ''; ?>>High</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Display Order</label>
                <input type="number" name="display_order" class="form-control" value="<?php echo $product['display_order']; ?>">
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Product</button>
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


