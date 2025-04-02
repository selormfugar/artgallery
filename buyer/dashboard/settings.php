<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings</title>
    <style>
        :root {
            --primary-color: #4a6cfa;
            --secondary-color: #f3f4f6;
            --text-color: #333;
            --border-color: #ddd;
            --success-color: #10b981;
            --danger-color: #ef4444;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f9fafb;
            color: var(--text-color);
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 30px;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: var(--primary-color);
        }
        
        nav ul {
            display: flex;
            list-style: none;
        }
        
        nav ul li {
            margin-left: 20px;
        }
        
        nav ul li a {
            text-decoration: none;
            color: var(--text-color);
            font-weight: 500;
        }
        
        nav ul li a:hover {
            color: var(--primary-color);
        }
        
        .page-title {
            font-size: 28px;
            margin-bottom: 20px;
            color: var(--text-color);
        }
        
        .settings-container {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 30px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        
        .settings-sidebar {
            background-color: var(--secondary-color);
            padding: 30px 20px;
        }
        
        .settings-sidebar ul {
            list-style: none;
        }
        
        .settings-sidebar li {
            margin-bottom: 15px;
        }
        
        .settings-sidebar a {
            display: block;
            padding: 10px 15px;
            text-decoration: none;
            color: var(--text-color);
            border-radius: 5px;
            font-weight: 500;
        }
        
        .settings-sidebar a:hover, .settings-sidebar a.active {
            background-color: white;
            color: var(--primary-color);
        }
        
        .settings-sidebar a.active {
            border-left: 3px solid var(--primary-color);
        }
        
        .settings-content {
            padding: 30px;
        }
        
        .settings-form {
            max-width: 600px;
        }
        
        .form-section {
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 20px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            font-size: 16px;
        }
        
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(74, 108, 250, 0.2);
        }
        
        .btn {
            display: inline-block;
            padding: 12px 20px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .btn:hover {
            background-color: #3a57dc;
        }
        
        .btn-secondary {
            background-color: white;
            border: 1px solid var(--border-color);
            color: var(--text-color);
        }
        
        .btn-secondary:hover {
            background-color: var(--secondary-color);
        }
        
        .btn-danger {
            background-color: var(--danger-color);
        }
        
        .btn-danger:hover {
            background-color: #dc2626;
        }
        
        .form-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        
        .alert {
            padding: 12px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background-color: #ecfdf5;
            color: var(--success-color);
            border: 1px solid #a7f3d0;
        }
        
        .alert-danger {
            background-color: #fef2f2;
            color: var(--danger-color);
            border: 1px solid #fecaca;
        }
        
        @media (max-width: 768px) {
            .settings-container {
                grid-template-columns: 1fr;
            }
            
            .settings-sidebar {
                padding: 20px;
            }
            
            .settings-sidebar ul {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
            }
            
            .settings-sidebar li {
                margin-bottom: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
<?php 
 include_once '../includes/header.php';
 
 ?>
        
        <h1 class="page-title">Account Settings</h1>
        
        <div class="settings-container">
            <div class="settings-sidebar">
                <ul>
                    <li><a href="#" class="active">Personal Information</a></li>
                    <li><a href="#">Password</a></li>
                    <li><a href="#">Notification Settings</a></li>
                    <li><a href="#">Privacy Settings</a></li>
                    <li><a href="#">Billing & Payments</a></li>
                    <li><a href="#">Delete Account</a></li>
                </ul>
            </div>
            
            <div class="settings-content">
                <div class="alert alert-success">
                    Your profile has been updated successfully!
                </div>
                
                <form class="settings-form">
                    <div class="form-section">
                        <h2 class="section-title">Personal Information</h2>
                        
                        <div class="form-group">
                            <label for="firstName">First Name</label>
                            <input type="text" id="firstName" name="firstName" value="John">
                        </div>
                        
                        <div class="form-group">
                            <label for="lastName">Last Name</label>
                            <input type="text" id="lastName" name="lastName" value="Doe">
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" value="john.doe@example.com">
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn">Save Changes</button>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h2 class="section-title">Change Password</h2>
                        
                        <div class="form-group">
                            <label for="currentPassword">Current Password</label>
                            <input type="password" id="currentPassword" name="currentPassword">
                        </div>
                        
                        <div class="form-group">
                            <label for="newPassword">New Password</label>
                            <input type="password" id="newPassword" name="newPassword">
                        </div>
                        
                        <div class="form-group">
                            <label for="confirmPassword">Confirm New Password</label>
                            <input type="password" id="confirmPassword" name="confirmPassword">
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn">Update Password</button>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h2 class="section-title">Delete Account</h2>
                        <p>This action is permanent and cannot be undone. All your data will be permanently deleted.</p>
                        
                        <div class="form-actions">
                            <button type="button" class="btn btn-danger">Delete Account</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
<script>

    // account-settings.js - Frontend logic for user account settings
document.addEventListener('DOMContentLoaded', function() {
    // Get form elements
    const personalInfoForm = document.getElementById('personalInfoForm');
    const passwordForm = document.getElementById('passwordForm');
    const deleteAccountBtn = document.getElementById('deleteAccountBtn');
    
    // Success and error message containers
    const messageContainer = document.getElementById('messageContainer');
    
    // Personal Information Form Submission
    personalInfoForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        const formData = {
            firstName: document.getElementById('firstName').value,
            lastName: document.getElementById('lastName').value,
            email: document.getElementById('email').value
        };
        
        // Validate inputs
        if (!formData.firstName || !formData.lastName || !formData.email) {
            showMessage('All fields are required', 'error');
            return;
        }
        
        // Email validation
        if (!isValidEmail(formData.email)) {
            showMessage('Please enter a valid email address', 'error');
            return;
        }
        
        // Send AJAX request to update personal information
        updatePersonalInfo(formData);
    });
    
    // Password Form Submission
    passwordForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        const formData = {
            currentPassword: document.getElementById('currentPassword').value,
            newPassword: document.getElementById('newPassword').value,
            confirmPassword: document.getElementById('confirmPassword').value
        };
        
        // Validate inputs
        if (!formData.currentPassword || !formData.newPassword || !formData.confirmPassword) {
            showMessage('All password fields are required', 'error');
            return;
        }
        
        // Check password length
        if (formData.newPassword.length < 8) {
            showMessage('Password must be at least 8 characters long', 'error');
            return;
        }
        
        // Check password match
        if (formData.newPassword !== formData.confirmPassword) {
            showMessage('New passwords do not match', 'error');
            return;
        }
        
        // Send AJAX request to update password
        updatePassword(formData);
    });
    
    // Delete Account Button Click
    deleteAccountBtn.addEventListener('click', function() {
        // Show confirmation dialog
        const confirmed = confirm('Are you sure you want to delete your account? This action cannot be undone and all your data will be permanently deleted.');
        
        if (confirmed) {
            deleteAccount();
        }
    });
    
    // Function to update personal information via API
   // Function to update personal information via API
function updatePersonalInfo(data) {
    // Show loading state
    const submitBtn = personalInfoForm.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Saving...';
    
    fetch('/api/user.php?action=profile', {  // Updated URL with action parameter
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(response => {  // Parse the standardized response format
        if (response.success) {
            showMessage(response.message, 'success');
        } else {
            showMessage(response.message || 'Failed to update personal information', 'error');
        }
    })
    .catch(error => {
        console.error('Error updating personal information:', error);
        showMessage('Failed to update personal information. Please try again.', 'error');
    })
    .finally(() => {
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.textContent = 'Save Changes';
    });
}

// Function to update password via API
function updatePassword(data) {
    // Show loading state
    const submitBtn = passwordForm.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Updating...';
    
    fetch('/api/user.php?action=password', {  // Updated URL with action parameter
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        const json = response.json();
        if (!response.ok) {
            return json.then(data => {
                throw new Error(data.message || 'Network response was not ok');
            });
        }
        return json;
    })
    .then(response => {
        if (response.success) {
            showMessage(response.message, 'success');
            // Clear password fields
            document.getElementById('currentPassword').value = '';
            document.getElementById('newPassword').value = '';
            document.getElementById('confirmPassword').value = '';
        } else {
            showMessage(response.message || 'Failed to update password', 'error');
        }
    })
    .catch(error => {
        console.error('Error updating password:', error);
        showMessage(error.message || 'Failed to update password. Please try again.', 'error');
    })
    .finally(() => {
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.textContent = 'Update Password';
    });
}

// Function to delete account via API
function deleteAccount() {
    fetch('/api/user.php?action=account', {  // Updated URL with action parameter
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.message || 'Network response was not ok');
            });
        }
        return response.json();
    })
    .then(response => {
        if (response.success) {
            showMessage(response.message, 'success');
            // Redirect to logout or homepage after short delay
            setTimeout(() => {
                window.location.href = '/logout.php';
            }, 2000);
        } else {
            showMessage(response.message || 'Failed to delete account', 'error');
        }
    })
    .catch(error => {
        console.error('Error deleting account:', error);
        showMessage(error.message || 'Failed to delete account. Please try again.', 'error');
    });
}

// Load user data on page load
function loadUserData() {
    fetch('/api/user.php?action=profile', {  // Updated URL with action parameter
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.message || 'Failed to load user data');
            });
        }
        return response.json();
    })
    .then(response => {
        if (response.success && response.data) {
            // Populate form fields with user data
            document.getElementById('firstName').value = response.data.firstname || '';
            document.getElementById('lastName').value = response.data.lastname || '';
            document.getElementById('email').value = response.data.email || '';
        } else {
            throw new Error(response.message || 'Failed to load user data');
        }
    })
    .catch(error => {
        console.error('Error loading user data:', error);
        showMessage('Failed to load user data. Please refresh the page.', 'error');
    });
}
    // Helper function to display messages
    function showMessage(message, type) {
        messageContainer.innerHTML = '';
        
        const alert = document.createElement('div');
        alert.className = `alert alert-${type === 'success' ? 'success' : 'danger'}`;
        alert.textContent = message;
        
        messageContainer.appendChild(alert);
        
        // Auto-dismiss success messages after 5 seconds
        if (type === 'success') {
            setTimeout(() => {
                alert.remove();
            }, 5000);
        }
    }
    
    // Helper function to validate email format
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    // Load user data on page load
    function loadUserData() {
        fetch('/api/user/profile', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to load user data');
            }
            return response.json();
        })
        .then(data => {
            // Populate form fields with user data
            document.getElementById('firstName').value = data.firstname || '';
            document.getElementById('lastName').value = data.lastname || '';
            document.getElementById('email').value = data.email || '';
        })
        .catch(error => {
            console.error('Error loading user data:', error);
            showMessage('Failed to load user data. Please refresh the page.', 'error');
        });
    }
    
    // Initialize page by loading user data
    loadUserData();
});
</script>
</html>