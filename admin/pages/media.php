<?php
$pageTitle = 'Media Library';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
require_once __DIR__ . '/../includes/header.php';
?>

<div class="card">
  <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
    <h2>Media Library</h2>
    <button class="btn btn-primary" type="button" onclick="openMediaModal('dummy','dummyPreview')"><i class="fas fa-upload"></i> Upload / Select</button>
  </div>
  <div style="padding:20px;color:var(--text-muted);">
    Use the “Upload / Select” button to open the media modal, upload images, then reuse them in page editors.
    <input id="dummy" type="hidden" value="">
    <div id="dummyPreview" style="display:none;margin-top:10px;"></div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

