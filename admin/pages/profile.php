<?php
/**
 * Admin Profile Page
 * Update email and password
 */

$pageTitle = 'My Profile';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/header.php';

// Get current user info
try {
    $userId = getCurrentUserId();
    $stmt = $pdo->prepare("SELECT id, username, email, created_at, last_login FROM admin_users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if (!$user) {
        redirect(ADMIN_URL . 'dashboard.php');
    }
} catch(PDOException $e) {
    error_log("Profile page error: " . $e->getMessage());
    redirect(ADMIN_URL . 'dashboard.php');
}

$csrfToken = generateCSRFToken();
?>

<div style="max-width: 800px;">
    <!-- Account Information -->
    <div class="card">
        <div class="card-header">
            <h2>Account Information</h2>
        </div>
        <div style="padding: 20px;">
            <div class="form-group">
                <label>Username</label>
                <input type="text" value="<?= sanitize($user['username']) ?>" disabled style="background: #f5f5f5; cursor: not-allowed;">
                <p class="form-help">Username cannot be changed</p>
            </div>
            
            <div class="form-group">
                <label>Account Created</label>
                <input type="text" value="<?= sanitize(date('F j, Y', strtotime($user['created_at']))) ?>" disabled style="background: #f5f5f5; cursor: not-allowed;">
            </div>
            
            <?php if ($user['last_login']): ?>
            <div class="form-group">
                <label>Last Login</label>
                <input type="text" value="<?= sanitize(date('F j, Y g:i A', strtotime($user['last_login']))) ?>" disabled style="background: #f5f5f5; cursor: not-allowed;">
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Update Email -->
    <div class="card" style="margin-top: 20px;">
        <div class="card-header">
            <h2>Update Email Address</h2>
        </div>
        <div style="padding: 20px;">
            <form id="emailForm">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="<?= sanitize($user['email']) ?>"
                        required
                        placeholder="your.email@example.com">
                    <p class="form-help">Your email address will be used for account notifications</p>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Email
                </button>
            </form>
        </div>
    </div>
    
    <!-- Change Password -->
    <div class="card" style="margin-top: 20px;">
        <div class="card-header">
            <h2>Change Password</h2>
        </div>
        <div style="padding: 20px;">
            <form id="passwordForm">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <div class="input-wrapper">
                        <input 
                            type="password" 
                            id="current_password" 
                            name="current_password" 
                            required
                            placeholder="Enter your current password"
                            autocomplete="current-password">
                        <button type="button" class="password-toggle" onclick="togglePassword('current_password')" aria-label="Toggle password visibility">
                            <svg class="eye-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10 4C6 4 2.73 6.11 1 9.5C2.73 12.89 6 15 10 15C14 15 17.27 12.89 19 9.5C17.27 6.11 14 4 10 4Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="10" cy="9.5" r="2.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <svg class="eye-off-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: none;">
                                <path d="M2 2L18 18M8.88 8.88C8.3 9.46 8 10.22 8 11C8 12.66 9.34 14 11 14C11.78 14 12.54 13.7 13.12 13.12M14.71 11.29C15.1 10.9 15.33 10.4 15.33 9.83C15.33 8.17 13.99 6.83 12.33 6.83C11.76 6.83 11.26 7.06 10.87 7.45M6.61 6.61C4.06 7.82 2 9.5 1 11.5C2.73 14.89 6 17 10 17C11.5 17 12.9 16.7 14.17 16.17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <div class="input-wrapper">
                        <input 
                            type="password" 
                            id="new_password" 
                            name="new_password" 
                            required
                            minlength="8"
                            placeholder="Enter your new password"
                            autocomplete="new-password">
                        <button type="button" class="password-toggle" onclick="togglePassword('new_password')" aria-label="Toggle password visibility">
                            <svg class="eye-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10 4C6 4 2.73 6.11 1 9.5C2.73 12.89 6 15 10 15C14 15 17.27 12.89 19 9.5C17.27 6.11 14 4 10 4Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="10" cy="9.5" r="2.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <svg class="eye-off-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: none;">
                                <path d="M2 2L18 18M8.88 8.88C8.3 9.46 8 10.22 8 11C8 12.66 9.34 14 11 14C11.78 14 12.54 13.7 13.12 13.12M14.71 11.29C15.1 10.9 15.33 10.4 15.33 9.83C15.33 8.17 13.99 6.83 12.33 6.83C11.76 6.83 11.26 7.06 10.87 7.45M6.61 6.61C4.06 7.82 2 9.5 1 11.5C2.73 14.89 6 17 10 17C11.5 17 12.9 16.7 14.17 16.17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                    <p class="form-help">Password must be at least 8 characters long</p>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <div class="input-wrapper">
                        <input 
                            type="password" 
                            id="confirm_password" 
                            name="confirm_password" 
                            required
                            minlength="8"
                            placeholder="Confirm your new password"
                            autocomplete="new-password">
                        <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')" aria-label="Toggle password visibility">
                            <svg class="eye-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10 4C6 4 2.73 6.11 1 9.5C2.73 12.89 6 15 10 15C14 15 17.27 12.89 19 9.5C17.27 6.11 14 4 10 4Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="10" cy="9.5" r="2.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <svg class="eye-off-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: none;">
                                <path d="M2 2L18 18M8.88 8.88C8.3 9.46 8 10.22 8 11C8 12.66 9.34 14 11 14C11.78 14 12.54 13.7 13.12 13.12M14.71 11.29C15.1 10.9 15.33 10.4 15.33 9.83C15.33 8.17 13.99 6.83 12.33 6.83C11.76 6.83 11.26 7.06 10.87 7.45M6.61 6.61C4.06 7.82 2 9.5 1 11.5C2.73 14.89 6 17 10 17C11.5 17 12.9 16.7 14.17 16.17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-key"></i> Change Password
                </button>
            </form>
        </div>
    </div>
</div>

<script>
// Handle email update form
document.getElementById('emailForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = {
        email: formData.get('email')
    };
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    fetch('<?= ADMIN_URL ?>api/profile.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': csrfToken
        },
        body: JSON.stringify({ action: 'update_email', ...data })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('HTTP ' + response.status + ': ' + response.statusText);
        }
        return response.text().then(text => {
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Invalid JSON response:', text);
                throw new Error('Server returned invalid response. Please check server logs.');
            }
        });
    })
    .then(data => {
        if (data.success) {
            showToast(data.message || 'Email updated successfully', 'success');
            // Update the email field value
            document.getElementById('email').value = data.email || document.getElementById('email').value;
        } else {
            showToast(data.message || 'Failed to update email', 'error');
        }
    })
    .catch(error => {
        console.error('Email update error:', error);
        showToast('Error: ' + error.message, 'error');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

// Handle password change form
document.getElementById('passwordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    // Validate passwords match
    if (newPassword !== confirmPassword) {
        showToast('New passwords do not match', 'error');
        return;
    }
    
    // Validate password length
    if (newPassword.length < 8) {
        showToast('Password must be at least 8 characters long', 'error');
        return;
    }
    
    const formData = new FormData(this);
    const data = {
        current_password: formData.get('current_password'),
        new_password: formData.get('new_password')
    };
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Changing...';
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    fetch('<?= ADMIN_URL ?>api/profile.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': csrfToken
        },
        body: JSON.stringify({ action: 'change_password', ...data })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('HTTP ' + response.status + ': ' + response.statusText);
        }
        return response.text().then(text => {
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Invalid JSON response:', text);
                throw new Error('Server returned invalid response. Please check server logs.');
            }
        });
    })
    .then(data => {
        if (data.success) {
            showToast(data.message || 'Password changed successfully', 'success');
            // Clear password fields
            document.getElementById('current_password').value = '';
            document.getElementById('new_password').value = '';
            document.getElementById('confirm_password').value = '';
        } else {
            showToast(data.message || 'Failed to change password', 'error');
        }
    })
    .catch(error => {
        console.error('Password change error:', error);
        showToast('Error: ' + error.message, 'error');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

// Password visibility toggle function
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const toggleBtn = input.nextElementSibling;
    const eyeIcon = toggleBtn.querySelector('.eye-icon');
    const eyeOffIcon = toggleBtn.querySelector('.eye-off-icon');
    
    if (input.type === 'password') {
        input.type = 'text';
        eyeIcon.style.display = 'none';
        eyeOffIcon.style.display = 'block';
    } else {
        input.type = 'password';
        eyeIcon.style.display = 'block';
        eyeOffIcon.style.display = 'none';
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
