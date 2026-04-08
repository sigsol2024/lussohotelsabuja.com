<?php
$pageTitle = 'Rooms Listing Page';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
require_once __DIR__ . '/../includes/header.php';

$sectionsArray = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM page_sections WHERE page = 'rooms' ORDER BY section_key");
    $stmt->execute();
    foreach ($stmt->fetchAll() as $section) {
        $sectionsArray[$section['section_key']] = $section['content'];
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
}
?>

<form id="roomsPageForm">
  <div class="card">
    <div class="card-header"><h2>Rooms listing hero</h2></div>
    <div style="padding:20px;">
      <p class="form-help">Room cards on this page are loaded from the database. Edit individual rooms under <a href="<?= ADMIN_URL ?>pages/rooms/list.php">Rooms</a>.</p>
      <div class="form-group">
        <label for="page_title">Browser / SEO title</label>
        <input type="text" id="page_title" name="page_title" value="<?= sanitize($sectionsArray['page_title'] ?? 'Lusso Rooms & Suites Listing') ?>">
      </div>
      <div class="form-group">
        <label for="hero_title">Hero title (HTML)</label>
        <textarea id="hero_title" name="hero_title" rows="3"><?= htmlspecialchars($sectionsArray['hero_title'] ?? 'Sanctuaries of <br/><span class="font-bold italic font-serif">Serenity</span>', ENT_QUOTES, 'UTF-8') ?></textarea>
      </div>
      <div class="form-group">
        <label for="hero_subtitle">Hero subtitle</label>
        <textarea id="hero_subtitle" name="hero_subtitle" rows="3"><?= sanitize($sectionsArray['hero_subtitle'] ?? '') ?></textarea>
      </div>
      <div class="form-group">
        <label>Hero background</label>
        <button type="button" class="btn btn-outline" onclick="openMediaModal('hero_bg','hero_bg_preview')">Select image</button>
        <input type="hidden" id="hero_bg" name="hero_bg" value="<?= sanitize($sectionsArray['hero_bg'] ?? '') ?>">
        <div id="hero_bg_preview" class="image-preview" style="<?= !empty($sectionsArray['hero_bg']) ? 'display:block;' : 'display:none;' ?>">
          <?php if (!empty($sectionsArray['hero_bg'])): ?>
            <img src="<?= SITE_URL . ltrim($sectionsArray['hero_bg'], '/') ?>" style="max-width:500px;max-height:280px;">
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  <button type="submit" class="btn btn-primary">Save</button>
</form>

<script>
window.insertSelectedMediaOverride = function () {
  var selected = mediaModalState.selectedMedia;
  if (!selected || mediaModalState.targetInputId !== 'hero_bg') return false;
  document.getElementById('hero_bg').value = selected.path;
  var preview = document.getElementById('hero_bg_preview');
  preview.style.display = 'block';
  preview.innerHTML = '<img src="<?= SITE_URL ?>' + selected.path.replace(/^\/+/, '') + '" style="max-width:500px;max-height:280px;">';
  closeMediaModal();
  return true;
};
document.getElementById('roomsPageForm').addEventListener('submit', function (e) {
  e.preventDefault();
  savePageForm(this, 'rooms', { hero_title: 'html' })
    .then(function () { showToast('Saved', 'success'); })
    .catch(function (err) { showToast(err.message || 'Save failed', 'error'); });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
