<?php 
$pageTitle = 'Edit User - Admin - SecureBank';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAdmin();

// Get user ID from URL
$userId = isset($GLOBALS['id']) ? intval($GLOBALS['id']) : 0;

if (!$userId) {
    $_SESSION['error'] = 'Invalid user ID';
    redirect('/admin/users');
}

// Fetch user data
$db = Database::getInstance();
$sql = "SELECT * FROM users WHERE id = ? AND role != 'admin'";
$stmt = $db->query($sql, [$userId]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['error'] = 'User not found';
    redirect(getAdminUserListBackUrl());
}

requireDemoUserAdminAccess($user);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = [
            'full_name' => Security::sanitize($_POST['full_name']),
            'email' => Security::sanitize($_POST['email']),
            'phone' => Security::sanitize($_POST['phone'] ?? ''),
            'date_of_birth' => Security::sanitize($_POST['date_of_birth'] ?? ''),
            'address' => Security::sanitize($_POST['address'] ?? ''),
            'city' => Security::sanitize($_POST['city'] ?? ''),
            'state' => Security::sanitize($_POST['state'] ?? ''),
            'country' => Security::sanitize($_POST['country'] ?? ''),
            'postal_code' => Security::sanitize($_POST['postal_code'] ?? ''),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        // Validate required fields
        if (empty($data['full_name']) || empty($data['email'])) {
            $_SESSION['error'] = 'Full name and email are required';
        } else {
            // Check if email is already taken by another user
            $emailCheckSql = "SELECT id FROM users WHERE email = ? AND id != ?";
            $emailCheckStmt = $db->query($emailCheckSql, [$data['email'], $userId]);
            if ($emailCheckStmt->fetch()) {
                $_SESSION['error'] = 'Email address is already taken by another user';
            } else {
                // Update user
                $updateSql = "UPDATE users SET 
                    full_name = ?, email = ?, phone = ?, date_of_birth = ?, 
                    address = ?, city = ?, state = ?, country = ?, postal_code = ?, updated_at = ?
                    WHERE id = ?";
                
                $updateStmt = $db->query($updateSql, [
                    $data['full_name'], $data['email'], $data['phone'], $data['date_of_birth'],
                    $data['address'], $data['city'], $data['state'], $data['country'], $data['postal_code'], $data['updated_at'],
                    $userId
                ]);
                
                if ($updateStmt) {
                    $_SESSION['success'] = 'User information updated successfully';
                    redirect('/admin/user/' . $userId);
                } else {
                    $_SESSION['error'] = 'Failed to update user information';
                }
            }
        }
    } catch (Exception $e) {
        $_SESSION['error'] = 'An error occurred while updating user information';
    }
}

include __DIR__ . '/../../includes/head.php';
include __DIR__ . '/../../includes/admin-sidebar.php';
include __DIR__ . '/../../includes/admin-modals.php';
?>

<style>
.edit-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 24px;
}

.header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 32px;
}

.back-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    color: #0f172a;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
    transition: all 0.2s;
}

.back-btn:hover {
    background: #f8fafc;
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.12);
}

.user-header {
    display: flex;
    align-items: center;
    gap: 16px;
}

.avatar {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 20px;
}

.user-info h1 {
    font-size: 24px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}

.user-info .email {
    color: #64748b;
    font-size: 14px;
    margin-top: 4px;
}

.form-grid {
    display: grid;
    gap: 24px;
}

.form-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.form-card h3 {
    font-size: 18px;
    font-weight: 600;
    color: #0f172a;
    margin: 0 0 16px 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.form-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: #1e293b;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
    font-size: 14px;
}

.form-input, .form-select {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s;
}

.form-input:focus, .form-select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.profile-picture-section {
    text-align: center;
    padding: 20px;
    border: 2px dashed #d1d5db;
    border-radius: 8px;
    margin-bottom: 20px;
    transition: all 0.2s;
}

.profile-picture-section:hover {
    border-color: #3b82f6;
    background: #f8fafc;
}

.current-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 24px;
    margin: 0 auto 16px;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 14px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-primary {
    background: #3b82f6;
    color: white;
}

.btn-primary:hover {
    background: #2563eb;
    transform: translateY(-1px);
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
    transform: translateY(-1px);
}

.btn-outline {
    background: transparent;
    color: #3b82f6;
    border: 1px solid #3b82f6;
}

.btn-outline:hover {
    background: #3b82f6;
    color: white;
}

.form-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    margin-top: 24px;
    padding-top: 24px;
    border-top: 1px solid #e5e7eb;
}

@media (max-width: 768px) {
    .edit-container {
        padding: 16px;
    }
    
    .header {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
    
    .user-header {
        width: 100%;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
    }
}
</style>

<div class="edit-container">
    <!-- Header -->
    <div class="header">
        <a href="/admin/user/<?php echo $userId; ?>" class="back-btn">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                <path d="M15 6l-6 6 6 6" stroke="currentColor" stroke-width="2"/>
            </svg>
            Back to User
        </a>
        
        <div class="user-header">
            <div class="avatar"><?php echo strtoupper(substr($user['full_name'], 0, 2)); ?></div>
            <div class="user-info">
                <h1>Edit User Information</h1>
                <div class="email"><?php echo htmlspecialchars($user['email']); ?></div>
            </div>
        </div>
    </div>

    <form method="POST" class="form-grid">
        <!-- Profile Picture Section -->
        <div class="form-card">
            <h3>
                <div class="form-icon">📷</div>
                Profile Picture
            </h3>
            
            <div class="profile-picture-section">
                <div class="current-avatar" id="currentAvatar">
                    <?php if ($user['profile_picture'] && file_exists(BASE_PATH . $user['profile_picture'])): ?>
                        <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                    <?php else: ?>
                        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white; font-weight: 700; font-size: 24px; border-radius: 50%;">
                            <?php echo strtoupper(substr($user['full_name'], 0, 2)); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <p style="margin: 0 0 16px 0; color: #6b7280; font-size: 14px;">
                    Click to upload a new profile picture
                </p>
                <input type="file" id="profilePictureInput" accept="image/*" style="display: none;" onchange="handleProfilePictureUpload()">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('profilePictureInput').click()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5-5-5 5M12 3v12" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    Upload Picture
                </button>
            </div>
        </div>

        <!-- Personal Information -->
        <div class="form-card">
            <h3>
                <div class="form-icon">👤</div>
                Personal Information
            </h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="full_name">Full Name *</label>
                    <input type="text" id="full_name" name="full_name" class="form-input" 
                           value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="email">Email Address *</label>
                    <input type="email" id="email" name="email" class="form-input" 
                           value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" class="form-input" 
                           value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="date_of_birth">Date of Birth</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" class="form-input" 
                           value="<?php echo $user['date_of_birth'] ?? ''; ?>">
                </div>
            </div>
        </div>

        <!-- Address Information -->
        <div class="form-card">
            <h3>
                <div class="form-icon">🏠</div>
                Address Information
            </h3>
            
            <div class="form-group">
                <label class="form-label" for="address">Street Address</label>
                <input type="text" id="address" name="address" class="form-input" 
                       value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="city">City</label>
                    <input type="text" id="city" name="city" class="form-input" 
                           value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="state">State/Province</label>
                    <input type="text" id="state" name="state" class="form-input" 
                           value="<?php echo htmlspecialchars($user['state'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="country">Country</label>
                    <input type="text" id="country" name="country" class="form-input" 
                           value="<?php echo htmlspecialchars($user['country'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="postal_code">Postal Code</label>
                    <input type="text" id="postal_code" name="postal_code" class="form-input" 
                           value="<?php echo htmlspecialchars($user['postal_code'] ?? ''); ?>">
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-card">
            <div class="form-actions">
                <a href="/admin/user/<?php echo $userId; ?>" class="btn btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M15 6l-6 6 6 6" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    Save Changes
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function handleProfilePictureUpload() {
    const fileInput = document.getElementById('profilePictureInput');
    const file = fileInput.files[0];
    
    if (!file) {
        return;
    }
    
    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
        showToast('Invalid file type. Only JPEG, PNG, GIF, and WebP images are allowed.', 'error');
        fileInput.value = '';
        return;
    }
    
    // Validate file size (5MB max)
    const maxSize = 5 * 1024 * 1024; // 5MB
    if (file.size > maxSize) {
        showToast('File size too large. Maximum size is 5MB.', 'error');
        fileInput.value = '';
        return;
    }
    
    // Show preview immediately
    const reader = new FileReader();
    reader.onload = function(e) {
        const avatar = document.getElementById('currentAvatar');
        avatar.innerHTML = `<img src="${e.target.result}" alt="Profile Picture" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;
    };
    reader.readAsDataURL(file);
    
    // Upload file
    const formData = new FormData();
    formData.append('profile_picture', file);
    formData.append('user_id', <?php echo $userId; ?>);
    
    showToast('Uploading profile picture...', 'info');
    
    fetch('/api/upload-profile-picture.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Profile picture updated successfully!', 'success');
            // Update the image source to use the server path
            const avatar = document.getElementById('currentAvatar');
            avatar.innerHTML = `<img src="${data.profile_picture_url}" alt="Profile Picture" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;
        } else {
            showToast('Error: ' + data.message, 'error');
            // Revert to initials on error
            const avatar = document.getElementById('currentAvatar');
            avatar.innerHTML = '<div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white; font-weight: 700; font-size: 24px; border-radius: 50%;"><?php echo strtoupper(substr($user['full_name'], 0, 2)); ?></div>';
        }
        fileInput.value = '';
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred while uploading profile picture', 'error');
        // Revert to initials on error
        const avatar = document.getElementById('currentAvatar');
        avatar.innerHTML = '<div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white; font-weight: 700; font-size: 24px; border-radius: 50%;"><?php echo strtoupper(substr($user['full_name'], 0, 2)); ?></div>';
        fileInput.value = '';
    });
}

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const fullName = document.getElementById('full_name').value.trim();
    const email = document.getElementById('email').value.trim();
    
    if (!fullName || !email) {
        e.preventDefault();
        showToast('Full name and email are required', 'error');
        return false;
    }
    
    if (!isValidEmail(email)) {
        e.preventDefault();
        showToast('Please enter a valid email address', 'error');
        return false;
    }
});

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}
</script>