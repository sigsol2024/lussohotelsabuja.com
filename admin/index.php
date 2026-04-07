<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    redirect(ADMIN_URL . 'dashboard.php');
}

$error = '';
$rateLimitWarning = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!verifyCSRFToken($csrfToken)) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $error = 'Please enter both username and password.';
        } else {
            if (!checkLoginRateLimit($username)) {
                $error = 'Too many login attempts. Please wait before trying again.';
                $rateLimitWarning = true;
            } else {
                $result = login($username, $password);
                if ($result['success']) {
                    $redirect = $_SESSION['redirect_after_login'] ?? ADMIN_URL . 'dashboard.php';
                    unset($_SESSION['redirect_after_login']);
                    redirect($redirect);
                } else {
                    $error = $result['message'];
                    $rateLimitWarning = true;
                }
            }
        }
    }
}

$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login - Lusso CMS</title>
  <link rel="stylesheet" href="<?= ADMIN_URL ?>assets/css/admin.css">
</head>
<body class="login-page">
  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <div class="login-logo">L</div>
        <h1>Admin Portal</h1>
        <p class="login-subtitle">Sign in to access the dashboard</p>
      </div>

      <?php if ($error): ?>
        <div class="alert alert-error" style="margin-bottom: 20px; padding: 12px; border-left: 4px solid var(--error-color); background: #fef2f2;">
          <?= sanitize($error) ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="" class="login-form">
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

        <div class="form-group">
          <label for="username">Username</label>
          <div class="input-wrapper">
            <input type="text" id="username" name="username" required autofocus autocomplete="username">
          </div>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <div class="input-wrapper">
            <input type="password" id="password" name="password" required autocomplete="current-password">
          </div>
        </div>

        <button type="submit" class="btn-login"><span>Sign In</span></button>
      </form>
    </div>
  </div>
</body>
</html>

