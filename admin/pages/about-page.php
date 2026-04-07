<?php
$pageTitle = 'About Page';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
require_once __DIR__ . '/../includes/header.php';

$sections = [];
try {
    $stmt = $pdo->prepare("SELECT section_key, content FROM page_sections WHERE page = 'about'");
    $stmt->execute();
    foreach ($stmt->fetchAll() as $row) {
        $sections[$row['section_key']] = $row['content'];
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
}
$d = require __DIR__ . '/../../includes/cms-defaults.php';
$timelineDef = json_encode($d['about_timeline'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
$teamDef = json_encode($d['about_team'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
$timelineRaw = trim($sections['timeline_json'] ?? '') !== '' ? $sections['timeline_json'] : $timelineDef;
$teamRaw = trim($sections['team_json'] ?? '') !== '' ? $sections['team_json'] : $teamDef;
?>

<form id="aboutPageForm">
  <div class="card"><div class="card-header"><h2>Meta & hero</h2></div><div style="padding:20px;">
    <div class="form-group">
      <label for="page_title">Page title</label>
      <input type="text" id="page_title" name="page_title" value="<?= sanitize($sections['page_title'] ?? 'The Lusso Legacy - About Us') ?>">
    </div>
    <div class="form-group">
      <label for="hero_established">Hero kicker</label>
      <input type="text" id="hero_established" name="hero_established" value="<?= sanitize($sections['hero_established'] ?? 'Established 2024') ?>">
    </div>
    <div class="form-group">
      <label for="hero_title_html">Hero title (HTML)</label>
      <textarea id="hero_title_html" name="hero_title_html" rows="3"><?= htmlspecialchars($sections['hero_title_html'] ?? 'The Lusso <br/><span class="font-bold italic text-primary/90">Legacy</span>', ENT_QUOTES, 'UTF-8') ?></textarea>
    </div>
    <div class="form-group">
      <label for="hero_subtitle">Hero subtitle</label>
      <textarea id="hero_subtitle" name="hero_subtitle" rows="2"><?= sanitize($sections['hero_subtitle'] ?? '') ?></textarea>
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

  <div class="card"><div class="card-header"><h2>Story block</h2></div><div style="padding:20px;">
    <div class="form-group">
      <label for="story_title_html">Title (HTML)</label>
      <textarea id="story_title_html" name="story_title_html" rows="2"><?= htmlspecialchars($sections['story_title_html'] ?? 'Defining Abuja <br/><span class="font-semibold text-primary">Luxury</span>', ENT_QUOTES, 'UTF-8') ?></textarea>
    </div>
    <div class="form-group"><textarea name="story_p1" id="story_p1" rows="3"><?= sanitize($sections['story_p1'] ?? '') ?></textarea></div>
    <div class="form-group"><textarea name="story_p2" id="story_p2" rows="3"><?= sanitize($sections['story_p2'] ?? '') ?></textarea></div>
    <div class="form-group">
      <label>Story image</label>
      <button type="button" class="btn btn-outline" onclick="openMediaModal('story_image','story_image_preview')">Select</button>
      <input type="hidden" id="story_image" name="story_image" value="<?= sanitize($sections['story_image'] ?? '') ?>">
      <div id="story_image_preview" class="image-preview" style="<?= !empty($sections['story_image']) ? 'display:block;' : 'display:none;' ?>">
        <?php if (!empty($sections['story_image'])): ?>
          <img src="<?= SITE_URL . ltrim($sections['story_image'], '/') ?>" style="max-width:400px;">
        <?php endif; ?>
      </div>
    </div>
    <div class="form-group">
      <label for="story_quote">Pull quote</label>
      <textarea id="story_quote" name="story_quote" rows="2"><?= sanitize($sections['story_quote'] ?? '"A sanctuary where time slows down and memories are crafted."') ?></textarea>
    </div>
  </div></div>

  <div class="card"><div class="card-header"><h2>Values / artistry</h2></div><div style="padding:20px;">
    <div class="form-group"><label for="values_kicker">Kicker</label><input type="text" id="values_kicker" name="values_kicker" value="<?= sanitize($sections['values_kicker'] ?? 'Our Core Values') ?>"></div>
    <div class="form-group"><label for="values_title">Title</label><input type="text" id="values_title" name="values_title" value="<?= sanitize($sections['values_title'] ?? 'Artistry in Service') ?>"></div>
    <div class="form-group">
      <label>Large image</label>
      <button type="button" class="btn btn-outline" onclick="openMediaModal('values_image','values_image_preview')">Select</button>
      <input type="hidden" id="values_image" name="values_image" value="<?= sanitize($sections['values_image'] ?? '') ?>">
      <div id="values_image_preview" class="image-preview" style="<?= !empty($sections['values_image']) ? 'display:block;' : 'display:none;' ?>">
        <?php if (!empty($sections['values_image'])): ?>
          <img src="<?= SITE_URL . ltrim($sections['values_image'], '/') ?>" style="max-width:500px;">
        <?php endif; ?>
      </div>
    </div>
    <div class="form-group"><label for="values_card_icon">Card icon (Material)</label><input type="text" id="values_card_icon" name="values_card_icon" value="<?= sanitize($sections['values_card_icon'] ?? 'spa') ?>"></div>
    <div class="form-group"><label for="values_card_title">Card title</label><input type="text" id="values_card_title" name="values_card_title" value="<?= sanitize($sections['values_card_title'] ?? 'Sanctuary for the Senses') ?>"></div>
    <div class="form-group"><textarea name="values_card_body" id="values_card_body" rows="3"><?= sanitize($sections['values_card_body'] ?? '') ?></textarea></div>
    <div class="form-row">
      <div class="form-group"><label for="values_card_link">Card link text</label><input type="text" id="values_card_link" name="values_card_link" value="<?= sanitize($sections['values_card_link'] ?? 'Read about our wellness') ?>"></div>
      <div class="form-group"><label for="values_card_link_href">Card URL</label><input type="text" id="values_card_link_href" name="values_card_link_href" value="<?= sanitize($sections['values_card_link_href'] ?? 'amenities.php') ?>"></div>
    </div>
  </div></div>

  <div class="card"><div class="card-header"><h2>Timeline & team (JSON)</h2></div><div style="padding:20px;">
    <p class="form-help">Timeline: array of <code>year</code>, <code>kind</code> (<code>circle</code>|<code>dot</code>|<code>dot_primary</code>), <code>title</code>, <code>body</code>.</p>
    <textarea id="timeline_json" name="timeline_json" rows="14" style="font-family:monospace;font-size:12px;"><?= htmlspecialchars($timelineRaw, ENT_QUOTES, 'UTF-8') ?></textarea>
    <p class="form-help" style="margin-top:16px;">Team: array of <code>name</code>, <code>role</code>, <code>image</code> (URL or media path).</p>
    <textarea id="team_json" name="team_json" rows="12" style="font-family:monospace;font-size:12px;"><?= htmlspecialchars($teamRaw, ENT_QUOTES, 'UTF-8') ?></textarea>
  </div></div>

  <div class="card"><div class="card-header"><h2>Journey header</h2></div><div style="padding:20px;">
    <div class="form-group">
      <label for="journey_title_html">Title (HTML)</label>
      <textarea id="journey_title_html" name="journey_title_html" rows="2"><?= htmlspecialchars($sections['journey_title_html'] ?? 'A Historic <span class="font-bold italic text-primary">Journey</span>', ENT_QUOTES, 'UTF-8') ?></textarea>
    </div>
    <div class="form-group"><label for="journey_subtitle">Subtitle</label><input type="text" id="journey_subtitle" name="journey_subtitle" value="<?= sanitize($sections['journey_subtitle'] ?? 'Tracing the milestones that shaped our vision of hospitality.') ?>"></div>
  </div></div>

  <div class="card"><div class="card-header"><h2>Leadership intro</h2></div><div style="padding:20px;">
    <div class="form-group"><label for="team_kicker">Kicker</label><input type="text" id="team_kicker" name="team_kicker" value="<?= sanitize($sections['team_kicker'] ?? 'Leadership') ?>"></div>
    <div class="form-group"><label for="team_heading">Heading</label><input type="text" id="team_heading" name="team_heading" value="<?= sanitize($sections['team_heading'] ?? 'The Curators') ?>"></div>
    <div class="form-group"><textarea name="team_intro" id="team_intro" rows="2"><?= sanitize($sections['team_intro'] ?? 'Meet the visionaries dedicated to crafting your perfect stay.') ?></textarea></div>
  </div></div>

  <div class="card"><div class="card-header"><h2>Parallax & CTA</h2></div><div style="padding:20px;">
    <div class="form-group">
      <label>Parallax image</label>
      <button type="button" class="btn btn-outline" onclick="openMediaModal('parallax_bg','parallax_bg_preview')">Select</button>
      <input type="hidden" id="parallax_bg" name="parallax_bg" value="<?= sanitize($sections['parallax_bg'] ?? '') ?>">
      <div id="parallax_bg_preview" class="image-preview" style="<?= !empty($sections['parallax_bg']) ? 'display:block;' : 'display:none;' ?>">
        <?php if (!empty($sections['parallax_bg'])): ?><img src="<?= SITE_URL . ltrim($sections['parallax_bg'], '/') ?>" style="max-width:500px;"><?php endif; ?>
      </div>
    </div>
    <div class="form-group"><label for="parallax_quote">Parallax quote</label><input type="text" id="parallax_quote" name="parallax_quote" value="<?= sanitize($sections['parallax_quote'] ?? '"Where elegance meets heritage."') ?>"></div>
    <div class="form-group"><label for="cta_title">CTA title</label><input type="text" id="cta_title" name="cta_title" value="<?= sanitize($sections['cta_title'] ?? 'Ready to Experience the Legend?') ?>"></div>
    <div class="form-group"><textarea name="cta_body" id="cta_body" rows="2"><?= sanitize($sections['cta_body'] ?? 'Your journey into the extraordinary begins here. Reserve your sanctuary in Abuja today.') ?></textarea></div>
    <div class="form-row">
      <div class="form-group"><label for="cta_btn1">Button 1</label><input type="text" id="cta_btn1" name="cta_btn1" value="<?= sanitize($sections['cta_btn1'] ?? 'Check Availability') ?>"></div>
      <div class="form-group"><label for="cta_btn1_href">URL</label><input type="text" id="cta_btn1_href" name="cta_btn1_href" value="<?= sanitize($sections['cta_btn1_href'] ?? 'rooms.php') ?>"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label for="cta_btn2">Button 2</label><input type="text" id="cta_btn2" name="cta_btn2" value="<?= sanitize($sections['cta_btn2'] ?? 'Contact Concierge') ?>"></div>
      <div class="form-group"><label for="cta_btn2_href">URL</label><input type="text" id="cta_btn2_href" name="cta_btn2_href" value="<?= sanitize($sections['cta_btn2_href'] ?? 'contact.php') ?>"></div>
    </div>
  </div></div>

  <button type="submit" class="btn btn-primary">Save all</button>
</form>

<script>
(function () {
  var map = { hero_bg: 'hero_bg_preview', story_image: 'story_image_preview', values_image: 'values_image_preview', parallax_bg: 'parallax_bg_preview' };
  window.insertSelectedMediaOverride = function () {
    var s = mediaModalState.selectedMedia;
    if (!s) return false;
    var pid = map[mediaModalState.targetInputId];
    if (!pid) return false;
    document.getElementById(mediaModalState.targetInputId).value = s.path;
    var p = document.getElementById(pid);
    p.style.display = 'block';
    p.innerHTML = '<img src="<?= SITE_URL ?>' + s.path.replace(/^\/+/, '') + '" style="max-width:500px;">';
    closeMediaModal();
    return true;
  };
})();
document.getElementById('aboutPageForm').addEventListener('submit', function (e) {
  e.preventDefault();
  var csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  var form = this;
  var keys = ['page_title', 'hero_established', 'hero_title_html', 'hero_subtitle', 'hero_bg', 'story_title_html', 'story_p1', 'story_p2', 'story_image', 'story_quote', 'values_kicker', 'values_title', 'values_image', 'values_card_icon', 'values_card_title', 'values_card_body', 'values_card_link', 'values_card_link_href', 'timeline_json', 'journey_title_html', 'journey_subtitle', 'team_kicker', 'team_heading', 'team_intro', 'team_json', 'parallax_bg', 'parallax_quote', 'cta_title', 'cta_body', 'cta_btn1', 'cta_btn1_href', 'cta_btn2', 'cta_btn2_href'];
  Promise.all(keys.map(function (key) {
    var el = form.querySelector('[name="' + key + '"]');
    var val = el ? el.value : '';
    var ct = 'text';
    if (key.endsWith('_html')) ct = 'html';
    if (key.endsWith('_json')) ct = 'json';
    if (key.endsWith('_bg') || key.endsWith('_image')) ct = 'image';
    return fetch('<?= ADMIN_URL ?>api/pages.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrf },
      body: JSON.stringify({ page: 'about', section_key: key, content_type: ct, content: val })
    }).then(function (r) { return r.json(); });
  })).then(function (results) {
    var ok = results.every(function (r) { return r.success; });
    showToast(ok ? 'Saved' : 'Save failed', ok ? 'success' : 'error');
  });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
