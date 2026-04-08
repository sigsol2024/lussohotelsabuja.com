<?php
$pageTitle = 'Dining Page';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
require_once __DIR__ . '/../includes/header.php';

$sections = [];
try {
    $stmt = $pdo->prepare("SELECT section_key, content FROM page_sections WHERE page = 'dining'");
    $stmt->execute();
    foreach ($stmt->fetchAll() as $row) {
        $sections[$row['section_key']] = $row['content'];
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
}
$cmsDefaults = require __DIR__ . '/../../includes/cms-defaults.php';
$menuDef = json_encode($cmsDefaults['dining_menu'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
$masonryDef = json_encode($cmsDefaults['dining_masonry'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
$menuRaw = trim($sections['menu_json'] ?? '') !== '' ? $sections['menu_json'] : $menuDef;
$masonryRaw = trim($sections['masonry_json'] ?? '') !== '' ? $sections['masonry_json'] : $masonryDef;
?>

<form id="diningPageForm">
  <div class="card"><div class="card-header"><h2>Meta</h2></div><div style="padding:20px;">
    <div class="form-group">
      <label for="page_title">Page title</label>
      <input type="text" id="page_title" name="page_title" value="<?= sanitize($sections['page_title'] ?? 'Lusso Fine Dining Experience') ?>">
    </div>
  </div></div>

  <div class="card"><div class="card-header"><h2>Hero</h2></div><div style="padding:20px;">
    <div class="form-group">
      <label for="hero_kicker">Kicker</label>
      <input type="text" id="hero_kicker" name="hero_kicker" value="<?= sanitize($sections['hero_kicker'] ?? 'Fine Dining — Abuja') ?>">
    </div>
    <div class="form-group">
      <label for="hero_title_html">Title (HTML)</label>
      <textarea id="hero_title_html" name="hero_title_html" rows="3"><?= htmlspecialchars($sections['hero_title_html'] ?? 'Culinary Artistry <br/><i class="font-light opacity-90">at Altitude</i>', ENT_QUOTES, 'UTF-8') ?></textarea>
    </div>
    <div class="form-group">
      <label for="hero_subtitle">Subtitle</label>
      <textarea id="hero_subtitle" name="hero_subtitle" rows="2"><?= sanitize($sections['hero_subtitle'] ?? '') ?></textarea>
    </div>
    <div class="form-group">
      <label for="hero_hours">Hours line (sidebar)</label>
      <input type="text" id="hero_hours" name="hero_hours" value="<?= sanitize($sections['hero_hours'] ?? 'Open Daily: 6pm — 11pm') ?>">
    </div>
    <div class="form-group">
      <label>Hero background</label>
      <button type="button" class="btn btn-outline" onclick="openMediaModal('hero_bg','hero_bg_preview')">Select</button>
      <input type="hidden" id="hero_bg" name="hero_bg" value="<?= sanitize($sections['hero_bg'] ?? '') ?>">
      <div id="hero_bg_preview" class="image-preview" style="<?= !empty($sections['hero_bg']) ? 'display:block;' : 'display:none;' ?>">
        <?php if (!empty($sections['hero_bg'])): ?>
          <img src="<?= SITE_URL . ltrim($sections['hero_bg'], '/') ?>" style="max-width:500px;">
        <?php endif; ?>
      </div>
    </div>
  </div></div>

  <div class="card"><div class="card-header"><h2>Chef / intro</h2></div><div style="padding:20px;">
    <div class="form-group">
      <label for="intro_vertical">Vertical label (desktop)</label>
      <input type="text" id="intro_vertical" name="intro_vertical" value="<?= sanitize($sections['intro_vertical'] ?? 'Est. 2024 — Lusso Abuja') ?>">
    </div>
    <div class="form-group">
      <label for="chef_title_html">Section title (HTML)</label>
      <textarea id="chef_title_html" name="chef_title_html" rows="2"><?= htmlspecialchars($sections['chef_title_html'] ?? 'The Chef\'s <br/> <span class="text-primary italic">Philosophy</span>', ENT_QUOTES, 'UTF-8') ?></textarea>
    </div>
    <div class="form-group">
      <label for="chef_body_html">Body (HTML)</label>
      <textarea id="chef_body_html" name="chef_body_html" rows="6"><?= htmlspecialchars($sections['chef_body_html'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
    </div>
    <div class="form-group">
      <label for="chef_signature">Signature line</label>
      <input type="text" id="chef_signature" name="chef_signature" value="<?= sanitize($sections['chef_signature'] ?? 'Antonio Rossi, Executive Chef') ?>">
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Chef portrait (tall)</label>
        <button type="button" class="btn btn-outline" onclick="openMediaModal('chef_main_img','chef_main_preview')">Select</button>
        <input type="hidden" id="chef_main_img" name="chef_main_img" value="<?= sanitize($sections['chef_main_img'] ?? '') ?>">
        <div id="chef_main_preview" class="image-preview" style="<?= !empty($sections['chef_main_img']) ? 'display:block;' : 'display:none;' ?>">
          <?php if (!empty($sections['chef_main_img'])): ?>
            <img src="<?= SITE_URL . ltrim($sections['chef_main_img'], '/') ?>" style="max-width:200px;">
          <?php endif; ?>
        </div>
      </div>
      <div class="form-group">
        <label>Circle detail image</label>
        <button type="button" class="btn btn-outline" onclick="openMediaModal('chef_circle_img','chef_circle_preview')">Select</button>
        <input type="hidden" id="chef_circle_img" name="chef_circle_img" value="<?= sanitize($sections['chef_circle_img'] ?? '') ?>">
        <div id="chef_circle_preview" class="image-preview" style="<?= !empty($sections['chef_circle_img']) ? 'display:block;' : 'display:none;' ?>">
          <?php if (!empty($sections['chef_circle_img'])): ?>
            <img src="<?= SITE_URL . ltrim($sections['chef_circle_img'], '/') ?>" style="max-width:200px;">
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div></div>

  <div class="card"><div class="card-header"><h2>Visual narrative strip</h2></div><div style="padding:20px;">
    <div class="form-row">
      <div class="form-group">
        <label for="visual_title">Heading</label>
        <input type="text" id="visual_title" name="visual_title" value="<?= sanitize($sections['visual_title'] ?? 'Visual Narrative') ?>">
      </div>
      <div class="form-group">
        <label for="visual_link_href">Gallery link</label>
        <input type="text" id="visual_link_href" name="visual_link_href" value="<?= sanitize($sections['visual_link_href'] ?? 'gallery.php') ?>">
      </div>
    </div>
    <div class="form-group">
      <label>Masonry images (JSON array: src, tag, caption)</label>
      <textarea id="masonry_json" name="masonry_json" rows="14" style="font-family:monospace;font-size:12px;"><?= htmlspecialchars($masonryRaw, ENT_QUOTES, 'UTF-8') ?></textarea>
    </div>
  </div></div>

  <div class="card"><div class="card-header"><h2>Menu block</h2></div><div style="padding:20px;">
    <div class="form-group">
      <label for="menu_kicker">Kicker</label>
      <input type="text" id="menu_kicker" name="menu_kicker" value="<?= sanitize($sections['menu_kicker'] ?? 'Taste of Excellence') ?>">
    </div>
    <div class="form-group">
      <label for="menu_heading">Heading</label>
      <input type="text" id="menu_heading" name="menu_heading" value="<?= sanitize($sections['menu_heading'] ?? 'Curated Selections') ?>">
    </div>
    <div class="form-group">
      <label for="menu_quote">Quote line</label>
      <input type="text" id="menu_quote" name="menu_quote" value="<?= sanitize($sections['menu_quote'] ?? '"Simplicity is the ultimate sophistication."') ?>">
    </div>
    <div class="form-group">
      <label for="menu_iframe_url">Menu iframe URL</label>
      <input type="text" id="menu_iframe_url" name="menu_iframe_url" value="<?= sanitize($sections['menu_iframe_url'] ?? 'https://our-menu.online/restaurant/the-lusso-restaurant') ?>">
      <p class="form-help">Paste the menu URL. Note: some providers block iframe embedding via <code>X-Frame-Options</code>; in that case the site will show an “Open menu” button instead of an embedded frame.</p>
    </div>
    <details style="margin-top:14px;">
      <summary style="cursor:pointer; color: var(--text-muted);">Advanced (legacy JSON menu)</summary>
      <div class="form-group" style="margin-top:10px;">
        <label>Menu rows JSON (name, desc, price)</label>
        <textarea id="menu_json" name="menu_json" rows="12" style="font-family:monospace;font-size:12px;"><?= htmlspecialchars($menuRaw, ENT_QUOTES, 'UTF-8') ?></textarea>
      </div>
    </details>
  </div></div>

  <div class="card"><div class="card-header"><h2>Reservation CTA</h2></div><div style="padding:20px;">
    <div class="form-group">
      <label>CTA background</label>
      <button type="button" class="btn btn-outline" onclick="openMediaModal('cta_bg','cta_bg_preview')">Select</button>
      <input type="hidden" id="cta_bg" name="cta_bg" value="<?= sanitize($sections['cta_bg'] ?? '') ?>">
      <div id="cta_bg_preview" class="image-preview" style="<?= !empty($sections['cta_bg']) ? 'display:block;' : 'display:none;' ?>">
        <?php if (!empty($sections['cta_bg'])): ?>
          <img src="<?= SITE_URL . ltrim($sections['cta_bg'], '/') ?>" style="max-width:400px;">
        <?php endif; ?>
      </div>
    </div>
    <div class="form-group">
      <label for="cta_title">Title</label>
      <input type="text" id="cta_title" name="cta_title" value="<?= sanitize($sections['cta_title'] ?? 'Secure Your Table at the Center of Abuja') ?>">
    </div>
    <div class="form-group">
      <label for="cta_body">Body</label>
      <textarea id="cta_body" name="cta_body" rows="2"><?= sanitize($sections['cta_body'] ?? '') ?></textarea>
    </div>
    <div class="form-row">
      <div class="form-group"><label for="cta_btn1">Button 1</label><input type="text" id="cta_btn1" name="cta_btn1" value="<?= sanitize($sections['cta_btn1'] ?? 'Make a Reservation') ?>"></div>
      <div class="form-group"><label for="cta_btn2">Button 2</label><input type="text" id="cta_btn2" name="cta_btn2" value="<?= sanitize($sections['cta_btn2'] ?? 'Private Dining') ?>"></div>
    </div>
  </div></div>

  <button type="submit" class="btn btn-primary">Save all</button>
</form>

<script>
(function () {
  var map = { hero_bg: 'hero_bg_preview', chef_main_img: 'chef_main_preview', chef_circle_img: 'chef_circle_preview', cta_bg: 'cta_bg_preview' };
  window.insertSelectedMediaOverride = function () {
    var s = mediaModalState.selectedMedia;
    if (!s) return false;
    var tid = mediaModalState.targetInputId;
    var pid = map[tid];
    if (!pid) return false;
    document.getElementById(tid).value = s.path;
    var p = document.getElementById(pid);
    p.style.display = 'block';
    p.innerHTML = '<img src="<?= SITE_URL ?>' + s.path.replace(/^\/+/, '') + '" style="max-width:500px;">';
    closeMediaModal();
    return true;
  };
})();
document.getElementById('diningPageForm').addEventListener('submit', function (e) {
  e.preventDefault();
  savePageForm(this, 'dining')
    .then(function () { showToast('Saved', 'success'); })
    .catch(function (err) { showToast(err.message || 'Save failed', 'error'); });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
