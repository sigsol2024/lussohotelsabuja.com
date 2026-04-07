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
                        <button type="button" class="password-toggle" data-password-toggle="current_password" aria-label="Show password" aria-pressed="false">
                            <i class="fas fa-eye" aria-hidden="true"></i>
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
                        <button type="button" class="password-toggle" data-password-toggle="new_password" aria-label="Show password" aria-pressed="false">
                            <i class="fas fa-eye" aria-hidden="true"></i>
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
                        <button type="button" class="password-toggle" data-password-toggle="confirm_password" aria-label="Show password" aria-pressed="false">
                            <i class="fas fa-eye" aria-hidden="true"></i>
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
function initPasswordToggles(context) {
  (context || document).querySelectorAll('[data-password-toggle]').forEach(function (btn) {
    if (btn.dataset.lussoPwBound) return;
    btn.dataset.lussoPwBound = '1';
    btn.addEventListener('click', function () {
      var id = btn.getAttribute('data-password-toggle');
      var input = id ? document.getElementById(id) : null;
      if (!input) return;
      var show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
      btn.setAttribute('aria-pressed', show ? 'true' : 'false');
      var icon = btn.querySelector('i');
      if (icon) icon.className = show ? 'fas fa-eye-slash' : 'fas fa-eye';
    });
  });
}
initPasswordToggles(document);

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
            ['current_password', 'new_password', 'confirm_password'].forEach(function (id) {
                var el = document.getElementById(id);
                if (el) {
                    el.value = '';
                    el.type = 'password';
                }
            });
            document.querySelectorAll('#passwordForm [data-password-toggle]').forEach(function (btn) {
                var icon = btn.querySelector('i');
                if (icon) icon.className = 'fas fa-eye';
                btn.setAttribute('aria-pressed', 'false');
                btn.setAttribute('aria-label', 'Show password');
            });
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
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
