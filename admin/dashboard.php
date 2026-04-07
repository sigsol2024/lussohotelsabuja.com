<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();
require_once __DIR__ . '/includes/header.php';
?>

<div class="card">
  <div class="card-header">
    <h2>Welcome</h2>
  </div>
  <p>Use the sidebar to edit pages, manage rooms, and upload media.</p>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

