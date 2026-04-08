<?php
$pageTitle = 'Terms & Conditions Page';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
require_once __DIR__ . '/../includes/header.php';

$sections = [];
try {
    $stmt = $pdo->prepare("SELECT section_key, content FROM page_sections WHERE page = 'terms-and-conditions'");
    $stmt->execute();
    foreach ($stmt->fetchAll() as $row) {
        $sections[$row['section_key']] = $row['content'];
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
}
?>

<form id="termsAndConditionsPageForm">
  <div class="card">
    <div class="card-header"><h2>Meta</h2></div>
    <div style="padding:20px;">
      <div class="form-group">
        <label for="page_title">Page title</label>
        <input type="text" id="page_title" name="page_title" value="<?= sanitize($sections['page_title'] ?? 'Terms & Conditions') ?>">
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><h2>Hero</h2></div>
    <div style="padding:20px;">
      <div class="form-row">
        <div class="form-group">
          <label for="hero_kicker">Kicker</label>
          <input type="text" id="hero_kicker" name="hero_kicker" value="<?= sanitize($sections['hero_kicker'] ?? 'Legal') ?>">
        </div>
        <div class="form-group">
          <label for="last_updated">Last updated</label>
          <input type="text" id="last_updated" name="last_updated" value="<?= sanitize($sections['last_updated'] ?? 'Last updated: April 8, 2026') ?>">
        </div>
      </div>
      <div class="form-group">
        <label for="hero_title">Hero title</label>
        <input type="text" id="hero_title" name="hero_title" value="<?= sanitize($sections['hero_title'] ?? 'Terms & Conditions') ?>">
      </div>
      <div class="form-group">
        <label for="hero_subtitle">Hero subtitle</label>
        <textarea id="hero_subtitle" name="hero_subtitle" rows="2"><?= sanitize($sections['hero_subtitle'] ?? 'The terms that govern use of our website and services.') ?></textarea>
      </div>
      <p class="form-help">Tip: This page is linked only in the footer (not in the header menu).</p>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><h2>Content</h2></div>
    <div style="padding:20px;">
      <div class="form-group">
        <label for="body_html">Body (HTML)</label>
        <textarea id="body_html" name="body_html" rows="18"><?= htmlspecialchars($sections['body_html'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        <p class="form-help">Use basic HTML like &lt;h2&gt;, &lt;p&gt;, &lt;ul&gt;, &lt;li&gt;.</p>
      </div>
    </div>
  </div>

  <button type="submit" class="btn btn-primary">Save</button>
</form>

<script>
document.getElementById('termsAndConditionsPageForm').addEventListener('submit', function (e) {
  e.preventDefault();
  savePageForm(this, 'terms-and-conditions')
    .then(function () { showToast('Saved', 'success'); })
    .catch(function (err) { showToast(err.message || 'Save failed', 'error'); });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

