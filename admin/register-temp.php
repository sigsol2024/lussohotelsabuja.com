<?php
/**
 * TEMPORARY — first-time admin bootstrap only.
 *
 * DELETE THIS FILE after you have created your admin account.
 *
 * Open: /admin/register-temp.php
 * Optional hardening: set LUSSO_TEMP_REGISTER_SECRET to a random string, then add ?key=that-string to the URL.
 */
declare(strict_types=1);

// Set to false or delete this file when finished.
const LUSSO_TEMP_REGISTER_ENABLED = true;

/** Non-empty = only works if ?key=... (and hidden field on POST) matches exactly. */
const LUSSO_TEMP_REGISTER_SECRET = '';

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

if (!LUSSO_TEMP_REGISTER_ENABLED) {
    http_response_code(410);
    header('Content-Type: text/plain; charset=UTF-8');
    echo "This registration form is disabled. Remove LUSSO_TEMP_REGISTER_ENABLED or restore it only if you still need this file (not recommended).\n";
    exit;
}

if (LUSSO_TEMP_REGISTER_SECRET !== '') {
    $provided = isset($_POST['reg_key']) ? (string)$_POST['reg_key'] : (string)($_GET['key'] ?? '');
    if ($provided === '' || !hash_equals(LUSSO_TEMP_REGISTER_SECRET, $provided)) {
        http_response_code(403);
        header('Content-Type: text/html; charset=UTF-8');
        echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Forbidden</title></head><body>';
        echo '<p>Missing or invalid key. Use <code>?key=…</code> matching <code>LUSSO_TEMP_REGISTER_SECRET</code> in <code>register-temp.php</code>.</p>';
        echo '</body></html>';
        exit;
    }
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!verifyCSRFToken($csrf)) {
        $error = 'Invalid security token. Refresh the page and try again!';
    } else {
        $username = trim((string)($_POST['username'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        $password2 = (string)($_POST['password_confirm'] ?? '');

        if ($username === '' || strlen($username) < 3) {
            $error = 'Username must be at least 3 characters.';
        } elseif (!preg_match('/^[a-zA-Z0-9_@.-]+$/', $username)) {
            $error = 'Username may only contain letters, numbers, and _ @ . -';
        } elseif ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } elseif (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters.';
        } elseif ($password !== $password2) {
            $error = 'Passwords do not match.';
        } else {
            try {
                $check = $pdo->prepare('SELECT id FROM admin_users WHERE username = ? OR email = ? LIMIT 1');
                $check->execute([$username, $email]);
                if ($check->fetch()) {
                    $error = 'That username or email is already registered.';
                } else {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $ins = $pdo->prepare(
                        'INSERT INTO admin_users (username, email, password_hash, is_active) VALUES (?, ?, ?, 1)'
                    );
                    $ins->execute([$username, $email, $hash]);
                    $success = 'Admin account created. You can log in now. Delete <code>admin/register-temp.php</code> from the server.';
                }
            } catch (PDOException $e) {
                error_log('register-temp: ' . $e->getMessage());
                $error = 'Database error. Check that the `admin_users` table exists and credentials in config are correct.';
            }
        }
    }
}

$csrfToken = generateCSRFToken();
$registerAction = 'register-temp.php' . (LUSSO_TEMP_REGISTER_SECRET !== '' ? ('?key=' . rawurlencode(LUSSO_TEMP_REGISTER_SECRET)) : '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Temporary Admin Registration - Lusso CMS</title>
  <link rel="stylesheet" href="<?= htmlspecialchars(ADMIN_URL . 'assets/css/admin.css', ENT_QUOTES, 'UTF-8') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
</head>
<body class="login-page">
  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <div class="login-logo" style="background:#d63638;"><i class="fas fa-user-plus" style="font-size:28px;"></i></div>
        <h1>Temp admin signup</h1>
        <p class="login-subtitle" style="color:#b45309;">
          Remove <strong>register-temp.php</strong> after you log in. Anyone who can open this URL can create an admin.
        </p>
      </div>

      <?php if ($success): ?>
        <div class="alert alert-error" style="margin-bottom:20px;padding:12px;border-left:4px solid #00a32a;background:#ecfdf5;color:#166534;">
          <?= $success ?>
        </div>
        <p style="text-align:center;"><a class="btn-login" style="display:inline-block;width:auto;text-decoration:none;" href="<?= htmlspecialchars(ADMIN_URL . 'index.php', ENT_QUOTES, 'UTF-8') ?>">Go to login</a></p>
      <?php else: ?>

      <?php if ($error): ?>
        <div class="alert alert-error" style="margin-bottom:20px;padding:12px;border-left:4px solid var(--error-color);background:#fef2f2;">
          <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="<?= htmlspecialchars($registerAction, ENT_QUOTES, 'UTF-8') ?>" class="login-form" autocomplete="off">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <?php if (LUSSO_TEMP_REGISTER_SECRET !== ''): ?>
        <input type="hidden" name="reg_key" value="<?= htmlspecialchars(LUSSO_TEMP_REGISTER_SECRET, ENT_QUOTES, 'UTF-8') ?>">
        <?php endif; ?>

        <div class="form-group">
          <label for="username">Username</label>
          <div class="input-wrapper">
            <input type="text" id="username" name="username" required minlength="3" maxlength="100"
                   autocomplete="off" value="<?= isset($_POST['username']) ? htmlspecialchars((string)$_POST['username'], ENT_QUOTES, 'UTF-8') : '' ?>">
          </div>
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <div class="input-wrapper">
            <input type="email" id="email" name="email" required maxlength="255"
                   autocomplete="off" value="<?= isset($_POST['email']) ? htmlspecialchars((string)$_POST['email'], ENT_QUOTES, 'UTF-8') : '' ?>">
          </div>
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <div class="input-wrapper">
            <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password">
            <button type="button" class="password-toggle" data-password-toggle="password" aria-label="Show password" aria-pressed="false">
              <i class="fas fa-eye" aria-hidden="true"></i>
            </button>
          </div>
        </div>
        <div class="form-group">
          <label for="password_confirm">Confirm password</label>
          <div class="input-wrapper">
            <input type="password" id="password_confirm" name="password_confirm" required minlength="8" autocomplete="new-password">
            <button type="button" class="password-toggle" data-password-toggle="password_confirm" aria-label="Show password" aria-pressed="false">
              <i class="fas fa-eye" aria-hidden="true"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="btn-login"><span>Create admin</span></button>
      </form>
      <p style="text-align:center;margin-top:20px;"><a href="<?= htmlspecialchars(ADMIN_URL . 'index.php', ENT_QUOTES, 'UTF-8') ?>" style="color:#6b7280;">Back to login</a></p>
      <?php endif; ?>
    </div>
  </div>
  <script>
  (function () {
    document.querySelectorAll('[data-password-toggle]').forEach(function (btn) {
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
  })();
  </script>
</body>
</html>
