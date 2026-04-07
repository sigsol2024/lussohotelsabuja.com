<?php
$pageTitle = 'Amenities Page';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
require_once __DIR__ . '/../includes/header.php';

$sections = [];
try {
    $stmt = $pdo->prepare("SELECT section_key, content FROM page_sections WHERE page = 'amenities'");
    $stmt->execute();
    foreach ($stmt->fetchAll() as $row) {
        $sections[$row['section_key']] = $row['content'];
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
}
$cmsDefaults = require __DIR__ . '/../../includes/cms-defaults.php';
$defaultJson = json_encode($cmsDefaults['amenities_sections'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
$raw = $sections['sections_json'] ?? '';
if (trim($raw) === '') {
    $raw = $defaultJson;
}
?>

<form id="amenitiesPageForm">
  <div class="card">
    <div class="card-header"><h2>Page title</h2></div>
    <div style="padding:20px;">
      <div class="form-group">
        <label for="page_title">Browser title</label>
        <input type="text" id="page_title" name="page_title" value="<?= sanitize($sections['page_title'] ?? 'Lusso Signature Amenities') ?>">
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><h2>Full-screen sections (JSON)</h2></div>
    <div style="padding:20px;">
      <p class="form-help">Array of objects with: <code>bg</code>, <code>gradient</code>, <code>kicker</code>, <code>icon</code> (Material symbol), <code>title_html</code>, <code>body</code>, <code>btn</code>, <code>btn_href</code>, <code>layout</code> — one of <code>bottom</code>, <code>right</code>, <code>top</code>, <code>center</code>.</p>
      <textarea id="sections_json" name="sections_json" rows="28" style="font-family:monospace;font-size:12px;"><?= htmlspecialchars($raw, ENT_QUOTES, 'UTF-8') ?></textarea>
    </div>
  </div>

  <button type="submit" class="btn btn-primary">Save</button>
</form>

<script>
document.getElementById('amenitiesPageForm').addEventListener('submit', function (e) {
  e.preventDefault();
  var csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  var form = this;
  function save(key, val, ct) {
    return fetch('<?= ADMIN_URL ?>api/pages.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrf },
      body: JSON.stringify({ page: 'amenities', section_key: key, content_type: ct, content: val })
    }).then(function (r) { return r.json(); });
  }
  Promise.all([
    save('page_title', form.querySelector('[name="page_title"]').value, 'text'),
    save('sections_json', form.querySelector('[name="sections_json"]').value, 'json')
  ]).then(function (results) {
    var ok = results.every(function (r) { return r.success; });
    showToast(ok ? 'Saved' : 'Save failed', ok ? 'success' : 'error');
  });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
