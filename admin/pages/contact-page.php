<?php
$pageTitle = 'Contact Page';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
require_once __DIR__ . '/../includes/header.php';

$sections = [];
try {
    $stmt = $pdo->prepare("SELECT section_key, content FROM page_sections WHERE page = 'contact'");
    $stmt->execute();
    foreach ($stmt->fetchAll() as $row) {
        $sections[$row['section_key']] = $row['content'];
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
}
?>

<form id="contactPageForm">
  <div class="card">
    <div class="card-header"><h2>Meta & intro</h2></div>
    <div style="padding:20px;">
      <div class="form-group">
        <label for="page_title">Page title</label>
        <input type="text" id="page_title" name="page_title" value="<?= sanitize($sections['page_title'] ?? 'Contact Lusso Hotels Abuja') ?>">
      </div>
      <div class="form-group">
        <label for="intro_kicker">Kicker</label>
        <input type="text" id="intro_kicker" name="intro_kicker" value="<?= sanitize($sections['intro_kicker'] ?? 'Concierge Services') ?>">
      </div>
      <div class="form-group">
        <label for="intro_title">Heading</label>
        <input type="text" id="intro_title" name="intro_title" value="<?= sanitize($sections['intro_title'] ?? 'Get in Touch') ?>">
      </div>
      <div class="form-group">
        <label for="intro_body">Intro body</label>
        <textarea id="intro_body" name="intro_body" rows="3"><?= sanitize($sections['intro_body'] ?? '') ?></textarea>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><h2>Contact details & map</h2></div>
    <div style="padding:20px;">
      <div class="form-group">
        <label for="address_html">Address (HTML, use &lt;br/&gt;)</label>
        <textarea id="address_html" name="address_html" rows="4"><?= htmlspecialchars($sections['address_html'] ?? "123 Diplomatic Drive<br/>\nCentral Business District,<br/>\nAbuja, Nigeria", ENT_QUOTES, 'UTF-8') ?></textarea>
      </div>
      <div class="form-group">
        <label for="directions_href">Directions link</label>
        <input type="text" id="directions_href" name="directions_href" value="<?= sanitize($sections['directions_href'] ?? '#') ?>">
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="concierge_phone">Concierge phone</label>
          <input type="text" id="concierge_phone" name="concierge_phone" value="<?= sanitize($sections['concierge_phone'] ?? '+234 800 LUSSO') ?>">
        </div>
        <div class="form-group">
          <label for="inquiries_email">Inquiries email</label>
          <input type="text" id="inquiries_email" name="inquiries_email" value="<?= sanitize($sections['inquiries_email'] ?? 'concierge@lussohotels.com') ?>">
        </div>
      </div>
      <div class="form-group">
        <label>Map / aerial image</label>
        <button type="button" class="btn btn-outline" onclick="openMediaModal('map_image','map_image_preview')">Select</button>
        <input type="hidden" id="map_image" name="map_image" value="<?= sanitize($sections['map_image'] ?? '') ?>">
        <div id="map_image_preview" class="image-preview" style="<?= !empty($sections['map_image']) ? 'display:block;' : 'display:none;' ?>">
          <?php if (!empty($sections['map_image'])): ?>
            <img src="<?= SITE_URL . ltrim($sections['map_image'], '/') ?>" style="max-width:500px;">
          <?php endif; ?>
        </div>
      </div>
      <div class="form-group">
        <label for="map_pin_label">Map pin label</label>
        <input type="text" id="map_pin_label" name="map_pin_label" value="<?= sanitize($sections['map_pin_label'] ?? 'Lusso Abuja') ?>">
      </div>
    </div>
  </div>

  <button type="submit" class="btn btn-primary">Save</button>
</form>

<script>
window.insertSelectedMediaOverride = function () {
  var s = mediaModalState.selectedMedia;
  if (!s || mediaModalState.targetInputId !== 'map_image') return false;
  document.getElementById('map_image').value = s.path;
  var p = document.getElementById('map_image_preview');
  p.style.display = 'block';
  p.innerHTML = '<img src="<?= SITE_URL ?>' + s.path.replace(/^\/+/, '') + '" style="max-width:500px;">';
  closeMediaModal();
  return true;
};
document.getElementById('contactPageForm').addEventListener('submit', function (e) {
  e.preventDefault();
  var csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  var keys = ['page_title', 'intro_kicker', 'intro_title', 'intro_body', 'address_html', 'directions_href', 'concierge_phone', 'inquiries_email', 'map_image', 'map_pin_label'];
  var form = this;
  Promise.all(keys.map(function (key) {
    var el = form.querySelector('[name="' + key + '"]');
    var val = el ? el.value : '';
    var ct = (key === 'address_html') ? 'html' : (key === 'map_image' ? 'image' : 'text');
    return fetch('<?= ADMIN_URL ?>api/pages.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrf },
      body: JSON.stringify({ page: 'contact', section_key: key, content_type: ct, content: val })
    }).then(function (r) { return r.json(); });
  })).then(function (results) {
    var ok = results.every(function (r) { return r.success; });
    showToast(ok ? 'Saved' : 'Save failed', ok ? 'success' : 'error');
  });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
