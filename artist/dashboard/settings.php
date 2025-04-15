<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';


$userId = $_SESSION['user_id'];

// Get current user data
$user = $db->selectOne("SELECT firstname, lastname, email, profile_image, username FROM users WHERE user_id = ?", [$_SESSION['user_id']]);
// No need to fetch since selectOne returns the row directly

// Check for success messages
$success = isset($_GET['success']) ? $_GET['success'] : null;
$error = isset($_GET['error']) ? $_GET['error'] : null;

include '../includes/header.php';
?><style>/* Account Settings */
.account-settings {
    max-width: 800px;
    margin: 2rem auto;
    padding: 2rem;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.settings-tabs {
    display: flex;
    border-bottom: 1px solid #ddd;
    margin-bottom: 2rem;
}

.tab-btn {
    padding: 0.75rem 1.5rem;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1rem;
    color: #666;
    position: relative;
}

.tab-btn.active {
    color: #333;
    font-weight: bold;
}

.tab-btn.active::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0;
    right: 0;
    height: 2px;
    background: #4a6fa5;
}

.tab-content {
    display: none;
    padding: 1rem 0;
}

.tab-content.active {
    display: block;
}

.avatar-upload {
    margin-bottom: 2rem;
    text-align: center;
}

.avatar-preview {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 1rem;
    border: 3px solid #eee;
}

.avatar-upload label {
    display: inline-block;
    padding: 0.5rem 1rem;
    background: #4a6fa5;
    color: white;
    border-radius: 4px;
    cursor: pointer;
    transition: background 0.3s;
}

.avatar-upload label:hover {
    background: #3a5a80;
}

.avatar-upload input[type="file"] {
    display: none;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.form-group input[type="text"],
.form-group input[type="email"],
.form-group input[type="password"] {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
}

.checkbox {
    display: flex;
    align-items: center;
}

.checkbox input {
    margin-right: 0.5rem;
}

.btn-primary {
    background: #4a6fa5;
    color: white;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    cursor: pointer;
    transition: background 0.3s;
}

.btn-primary:hover {
    background: #3a5a80;
}

.alert {
    padding: 1rem;
    margin-bottom: 1.5rem;
    border-radius: 4px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
}
</style>

<div class="account-settings">
    <h1>Account Settings</h1>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="settings-tabs">
        <button class="tab-btn active" onclick="openTab('profile-tab')">Profile</button>
        <button class="tab-btn" onclick="openTab('password-tab')">Password</button>
        <!-- <button class="tab-btn" onclick="openTab('privacy-tab')">Privacy</button> -->
    </div>

    <!-- Profile Tab -->
    <div id="profile-tab" class="tab-content active">
        <h2>Profile Information</h2>
        <form action="../api/update_profile.php" method="POST" enctype="multipart/form-data">
            <div class="row mb-3">
                <div class="col-md-4 text-center">
                    <div class="profile-image-container mb-3">
                        <img src="<?php echo htmlspecialchars($user['profile_image'] ?: '/uploads/avatars/default.png'); ?>" 
                             alt="Profile Image" class="img-thumbnail rounded-circle profile-image">
                    </div>
                    <div class="mb-3">
                        <label for="avatar" class="form-label">Change Profile Picture</label>
                        <input type="file" class="form-control" id="avatar" name="avatar" accept="image/jpeg,image/png,image/gif">
                        <small class="text-muted">Max file size: 5MB. Allowed formats: JPG, PNG, GIF</small>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?php echo htmlspecialchars($user['username']); ?>" required>
                        <small class="text-muted">3-50 characters. Letters, numbers, and underscores only.</small>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="firstname" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="firstname" name="firstname" 
                                   value="<?php echo htmlspecialchars($user['firstname']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="lastname" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="lastname" name="lastname" 
                                   value="<?php echo htmlspecialchars($user['lastname']); ?>" required>
                        </div>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Password Tab -->
    <div id="password-tab" class="tab-content">
        <h2>Change Password</h2>
        <form action="../api/change_password.php" method="post">
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>
            
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required>
                <small>Must be at least 8 characters long</small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <button type="submit" class="btn-primary">Update Password</button>
        </form>
    </div>

    <!-- Privacy Tab -->
    <!-- <div id="privacy-tab" class="tab-content">
        <h2>Privacy Settings</h2>
        <form action="../api/update_privacy.php" method="post">
            <div class="form-group checkbox">
                <input type="checkbox" id="public_profile" name="public_profile" 
                       <?= $user['is_public'] ? 'checked' : '' ?>>
                <label for="public_profile">Make my profile public</label>
            </div>
            
            <div class="form-group checkbox">
                <input type="checkbox" id="show_email" name="show_email" 
                       <?= $user['show_email'] ? 'checked' : '' ?>>
                <label for="show_email">Show my email on profile</label>
            </div>
            
            <button type="submit" class="btn-primary">Save Privacy Settings</button>
        </form>
    </div> -->
</div>

<script>
function openTab(tabId) {
    // Hide all tab contents
    const tabContents = document.getElementsByClassName('tab-content');
    for (let i = 0; i < tabContents.length; i++) {
        tabContents[i].classList.remove('active');
    }
    
    // Remove active class from all buttons
    const tabButtons = document.getElementsByClassName('tab-btn');
    for (let i = 0; i < tabButtons.length; i++) {
        tabButtons[i].classList.remove('active');
    }
    
    // Show the selected tab
    document.getElementById(tabId).classList.add('active');
    event.currentTarget.classList.add('active');
    
    // Update URL hash
    window.location.hash = tabId;
}

// Preview avatar before upload
document.getElementById('avatarInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            document.getElementById('avatarPreview').src = event.target.result;
        };
        reader.readAsDataURL(file);
    }
});

// Check for hash on page load to open correct tab
window.addEventListener('load', function() {
    if (window.location.hash) {
        const tabId = window.location.hash.substring(1);
        const tabBtn = document.querySelector(`.tab-btn[onclick="openTab('${tabId}')"]`);
        if (tabBtn) tabBtn.click();
    }
});
</script>

<?php include '../includes/footer.php'; ?>