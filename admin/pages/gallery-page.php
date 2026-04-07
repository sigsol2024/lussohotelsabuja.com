<?php
$pageTitle = 'Gallery Page';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
require_once __DIR__ . '/../includes/header.php';

$sections = [];
try {
    $stmt = $pdo->prepare("SELECT section_key, content FROM page_sections WHERE page = 'gallery'");
    $stmt->execute();
    foreach ($stmt->fetchAll() as $row) {
        $sections[$row['section_key']] = $row['content'];
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
}
$cmsDefaults = require __DIR__ . '/../../includes/cms-defaults.php';
$itemsDefault = json_encode($cmsDefaults['gallery_items'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
$itemsRaw = $sections['items_json'] ?? '';
if (trim($itemsRaw) === '') {
    $itemsRaw = $itemsDefault;
}
?>

<form id="galleryPageForm">
  <div class="card">
    <div class="card-header"><h2>Hero</h2></div>
    <div style="padding:20px;">
      <div class="form-group">
        <label for="page_title">Page title</label>
        <input type="text" id="page_title" name="page_title" value="<?= sanitize($sections['page_title'] ?? 'Lusso Visual Gallery') ?>">
      </div>
      <div class="form-group">
        <label for="hero_kicker">Kicker</label>
        <input type="text" id="hero_kicker" name="hero_kicker" value="<?= sanitize($sections['hero_kicker'] ?? 'The Collection') ?>">
      </div>
      <div class="form-group">
        <label for="hero_title_html">Title (HTML)</label>
        <textarea id="hero_title_html" name="hero_title_html" rows="2"><?= htmlspecialchars($sections['hero_title_html'] ?? 'VISUAL <span class="font-bold italic text-primary">NARRATIVES</span>', ENT_QUOTES, 'UTF-8') ?></textarea>
      </div>
      <div class="form-group">
        <label for="hero_subtitle">Subtitle</label>
        <textarea id="hero_subtitle" name="hero_subtitle" rows="2"><?= sanitize($sections['hero_subtitle'] ?? '') ?></textarea>
      </div>
      <div class="form-group">
        <label>Hero background</label>
        <button type="button" class="btn btn-outline" onclick="openMediaModal('hero_bg','hero_bg_preview')">Select</button>
        <input type="hidden" id="hero_bg" name="hero_bg" value="<?= sanitize($sections['hero_bg'] ?? '') ?>">
        <div id="hero_bg_preview" class="image-preview" style="<?= !empty($sections['hero_bg']) ? 'display:block;' : 'display:none;' ?>">
          <?php if (!empty($sections['hero_bg'])): ?>
            <img src="<?= SITE_URL . ltrim($sections['hero_bg'], '/') ?>" style="max-width:500px;max-height:280px;">
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><h2>Gallery grid (JSON)</h2></div>
    <div style="padding:20px;">
      <p class="form-help">Array of objects: <code>src</code>, <code>alt</code>, <code>category</code>, <code>title</code>, <code>ratio</code> (3/4, video, 2/3, square, 16/10, 3/5, 4/5).</p>
      <div class="form-group">
        <textarea id="items_json" name="items_json" rows="20" style="font-family:monospace;font-size:12px;"><?= htmlspecialchars($itemsRaw, ENT_QUOTES, 'UTF-8') ?></textarea>
      </div>
    </div>
  </div>

  <button type="submit" class="btn btn-primary">Save</button>
</form>

<script>
window.insertSelectedMediaOverride = function () {
  var s = mediaModalState.selectedMedia;
  if (!s || mediaModalState.targetInputId !== 'hero_bg') return false;
  document.getElementById('hero_bg').value = s.path;
  var p = document.getElementById('hero_bg_preview');
  p.style.display = 'block';
  p.innerHTML = '<img src="<?= SITE_URL ?>' + s.path.replace(/^\/+/, '') + '" style="max-width:500px;max-height:280px;">';
  closeMediaModal();
  return true;
};
document.getElementById('galleryPageForm').addEventListener('submit', function (e) {
  e.preventDefault();
  var csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  var keys = ['page_title', 'hero_kicker', 'hero_title_html', 'hero_subtitle', 'hero_bg', 'items_json'];
  var form = this;
  Promise.all(keys.map(function (key) {
    var el = form.querySelector('[name="' + key + '"]');
    var val = el ? el.value : '';
    var ct = 'text';
    if (key === 'hero_title_html') ct = 'html';
    if (key === 'hero_bg') ct = 'image';
    if (key === 'items_json') ct = 'json';
    return fetch('<?= ADMIN_URL ?>api/pages.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrf },
      body: JSON.stringify({ page: 'gallery', section_key: key, content_type: ct, content: val })
    }).then(function (r) { return r.json(); });
  })).then(function (results) {
    var ok = results.every(function (r) { return r.success; });
    showToast(ok ? 'Saved' : 'Save failed', ok ? 'success' : 'error');
  });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
