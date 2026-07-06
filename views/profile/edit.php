<?php 
$pageTitle = 'User Information - ' . getSiteName();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

requireLogin();

// Fetch user data
$db = Database::getInstance();
$userId = $_SESSION['user_id'];

$stmt = $db->query("SELECT * FROM users WHERE id = ?", [$userId]);
$user = $stmt->fetch();

// SECURITY: Verify user exists (should be caught by requireLogin, but double-check)
if (!$user) {
    // User account was deleted during session - destroy session and redirect to login
    session_destroy();
    $_SESSION = [];
    session_start();
    $_SESSION['error'] = 'Your account is no longer active.';
    header('Location: ' . SITE_URL . '/auth/login');
    exit;
}

// Include head
include __DIR__ . '/../../includes/head.php';
include __DIR__ . '/../../includes/sidebar.php';
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
    display: flex;
    align-items: center;
    gap: 12px;
}

.page-header p {
    color: #666;
    font-size: 16px;
}

.back-button {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: #f3f4f6;
    color: #374151;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s;
    margin-bottom: 20px;
}

.back-button:hover {
    background: #e5e7eb;
    transform: translateX(-4px);
}

/* User Info Page Styles */
.user-info-container {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 24px;
}

.profile-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.profile-photo-section {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.profile-photo {
    width: 180px;
    height: 180px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #e5e7eb;
    margin-bottom: 20px;
}

.profile-photo-placeholder {
    width: 180px;
    height: 180px;
    border-radius: 50%;
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 64px;
    color: white;
    font-weight: 700;
    margin-bottom: 20px;
    border: 4px solid #e5e7eb;
}

.btn-upload {
    display: inline-block;
    padding: 10px 20px;
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    color: white;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s;
    border: none;
}

.btn-upload:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
}

#profilePhotoInput {
    display: none;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-group label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
}

.form-control {
    width: 100%;
    padding: 12px;
    font-size: 16px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    transition: all 0.3s;
    box-sizing: border-box;
}

.form-control:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-control:disabled {
    background: #f9fafb;
    color: #6b7280;
    cursor: not-allowed;
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
}

.btn-primary {
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    color: white;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
}

.btn-secondary {
    background: #f3f4f6;
    color: #374151;
}

.btn-secondary:hover {
    background: #e5e7eb;
}

.form-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    margin-top: 30px;
}

.alert {
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: none;
}

.alert.show {
    display: block;
}

.alert-success {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #6ee7b7;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fca5a5;
}

@media (max-width: 1024px) {
    .user-info-container {
        grid-template-columns: 1fr;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .profile-photo, .profile-photo-placeholder {
        width: 140px;
        height: 140px;
        font-size: 48px;
    }
}
</style>

<a href="<?php echo SITE_URL; ?>/profile" class="back-button">
    <i class="fas fa-arrow-left"></i> Back to Profile
</a>

<div class="page-header">
    <h1>
        <i class="fas fa-user-edit"></i>
        User Information
    </h1>
    <p>Update your personal details and profile photo</p>
</div>

<div class="user-info-container">
    <!-- Profile Photo Card -->
    <div class="profile-card profile-photo-section">
        <h4 style="color: #1e3a8a; margin: 0 0 20px 0;">Profile Photo</h4>
        
        <?php if (!empty($user['profile_picture']) && file_exists(BASE_PATH . $user['profile_picture'])): ?>
            <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="profile-photo" id="profilePhotoPreview">
        <?php else: 
            $initials = '';
            $nameParts = explode(' ', $user['full_name']);
            foreach ($nameParts as $part) {
                $initials .= strtoupper(substr($part, 0, 1));
            }
            $initials = substr($initials, 0, 2);
        ?>
            <div class="profile-photo-placeholder" id="profilePhotoPreview">
                <?php echo htmlspecialchars($initials); ?>
            </div>
        <?php endif; ?>
        
        <form id="photoUploadForm" enctype="multipart/form-data">
            <input type="file" id="profilePhotoInput" name="profile_photo" accept="image/*">
            <label for="profilePhotoInput" class="btn-upload">
                <i class="fas fa-camera"></i> Change Photo
            </label>
        </form>
        
        <p style="color: #666; font-size: 12px; margin-top: 12px;">
            Accepted: JPG, PNG, GIF<br>
            Max size: 2MB
        </p>
    </div>
    
    <!-- Personal Information Card -->
    <div class="profile-card">
        <h4 style="color: #1e3a8a; margin: 0 0 20px 0;">Personal Information</h4>
        
        <div class="alert alert-error" id="errorMessage"></div>
        <div class="alert alert-success" id="successMessage"></div>
        
        <form id="userInfoForm">
            <div class="form-grid">
                <div class="form-group">
                    <label for="fullName">Full Name *</label>
                    <input type="text" class="form-control" id="fullName" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number *</label>
                    <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="dateOfBirth">Date of Birth *</label>
                    <input type="date" class="form-control" id="dateOfBirth" name="date_of_birth" value="<?php echo htmlspecialchars($user['date_of_birth']); ?>" required>
                </div>
                
                <div class="form-group full-width">
                    <label for="address">Address *</label>
                    <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="city">City *</label>
                    <input type="text" class="form-control" id="city" name="city" value="<?php echo htmlspecialchars($user['city']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="state">State/Province *</label>
                    <input type="text" class="form-control" id="state" name="state" value="<?php echo htmlspecialchars($user['state']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="country">Country *</label>
                    <input type="text" class="form-control" id="country" name="country" value="<?php echo htmlspecialchars($user['country']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="postalCode">Postal Code *</label>
                    <input type="text" class="form-control" id="postalCode" name="postal_code" value="<?php echo htmlspecialchars($user['postal_code']); ?>" required>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="button" onclick="window.location.href='<?php echo SITE_URL; ?>/profile'" class="btn btn-secondary">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Profile Photo Upload
document.getElementById('profilePhotoInput').addEventListener('change', async function(e) {
    const file = e.target.files[0];
    if (!file) return;
    
    // Validate file type
    if (!file.type.startsWith('image/')) {
        alert('Please select an image file');
        return;
    }
    
    // Validate file size (2MB max)
    if (file.size > 2 * 1024 * 1024) {
        alert('Image size must be less than 2MB');
        return;
    }
    
    // Preview image
    const reader = new FileReader();
    reader.onload = function(e) {
        const preview = document.getElementById('profilePhotoPreview');
        
        if (preview.tagName === 'IMG') {
            preview.src = e.target.result;
        } else {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'profile-photo';
            preview.parentNode.replaceChild(img, preview);
        }
    };
    reader.readAsDataURL(file);
    
    // Upload to server
    const formData = new FormData();
    formData.append('profile_picture', file);
    
    try {
        const response = await fetch('<?php echo SITE_URL; ?>/api/upload-profile-picture.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showSuccess('Profile photo updated successfully!');
            
            // Reload page after 1 second to update sidebar/header
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showError(data.message || 'Failed to upload photo');
        }
    } catch (error) {
        console.error('Upload error:', error);
        showError('An error occurred while uploading the photo');
    }
});

// User Info Form Submit
document.getElementById('userInfoForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    try {
        const response = await fetch('<?php echo SITE_URL; ?>/api/update-user-info.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showSuccess('User information updated successfully!');
            
            // Update session if name or email changed
            if (data.full_name) {
                <?php echo "// Update session name\n"; ?>
            }
            
            setTimeout(() => {
                window.location.href = '<?php echo SITE_URL; ?>/profile';
            }, 1500);
        } else {
            showError(result.message || 'Failed to update user information');
        }
    } catch (error) {
        showError('An error occurred. Please try again.');
    }
});

function showSuccess(message) {
    const successDiv = document.getElementById('successMessage');
    const errorDiv = document.getElementById('errorMessage');
    
    errorDiv.classList.remove('show');
    successDiv.textContent = message;
    successDiv.classList.add('show');
    
    setTimeout(() => {
        successDiv.classList.remove('show');
    }, 5000);
}

function showError(message) {
    const errorDiv = document.getElementById('errorMessage');
    const successDiv = document.getElementById('successMessage');
    
    successDiv.classList.remove('show');
    errorDiv.textContent = message;
    errorDiv.classList.add('show');
}
</script>

<?php
// Include mobile navigation and closing tags
include __DIR__ . '/../../includes/mobile-nav.php';
?>
