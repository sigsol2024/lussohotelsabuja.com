<?php
$pageTitle = 'Homepage Editor';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
require_once __DIR__ . '/../includes/header.php';

$sectionsArray = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM page_sections WHERE page = 'index' ORDER BY section_key");
    $stmt->execute();
    foreach ($stmt->fetchAll() as $section) {
        $sectionsArray[$section['section_key']] = $section['content'];
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
}

function hsec($sectionsArray, $key, $default = '') {
    return sanitize($sectionsArray[$key] ?? $default);
}
?>

<form id="homepageForm">
  <div class="card">
    <div class="card-header"><h2>Hero</h2></div>
    <div style="padding:20px;">
      <div class="form-group">
        <label for="hero_kicker">Kicker</label>
        <input id="hero_kicker" name="hero_kicker" type="text" value="<?= hsec($sectionsArray, 'hero_kicker', 'Welcome to Abuja') ?>">
      </div>
      <div class="form-group">
        <label for="hero_title">Title (HTML allowed)</label>
        <textarea id="hero_title" name="hero_title" rows="3"><?= htmlspecialchars($sectionsArray['hero_title'] ?? 'Refined Luxury in <br/><span class="italic text-primary">Absolute Silence</span>', ENT_QUOTES, 'UTF-8') ?></textarea>
      </div>
      <div class="form-group">
        <label for="hero_subtitle">Subtitle</label>
        <textarea id="hero_subtitle" name="hero_subtitle" rows="2"><?= hsec($sectionsArray, 'hero_subtitle') ?></textarea>
      </div>
      <div class="form-group">
        <label>Hero Background</label>
        <div style="margin-bottom:10px;">
          <button type="button" class="btn btn-outline" onclick="openMediaModal('hero_bg','hero_bg_preview')">Select Image</button>
        </div>
        <input type="hidden" id="hero_bg" name="hero_bg" value="<?= hsec($sectionsArray, 'hero_bg') ?>">
        <div id="hero_bg_preview" class="image-preview" style="<?= !empty($sectionsArray['hero_bg']) ? 'display:block;' : 'display:none;' ?>">
          <?php if (!empty($sectionsArray['hero_bg'])): ?>
            <img src="<?= SITE_URL . ltrim($sectionsArray['hero_bg'], '/') ?>" style="max-width:500px;max-height:300px;">
          <?php endif; ?>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="hero_cta_text">CTA Text</label>
          <input id="hero_cta_text" name="hero_cta_text" type="text" value="<?= hsec($sectionsArray, 'hero_cta_text', 'Discover Suites') ?>">
        </div>
        <div class="form-group">
          <label for="hero_cta_href">CTA Link</label>
          <input id="hero_cta_href" name="hero_cta_href" type="text" value="<?= hsec($sectionsArray, 'hero_cta_href', '/rooms') ?>">
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><h2>Booking widget (bridge)</h2></div>
    <div style="padding:20px;">
      <div class="form-group">
        <label for="booking_widget_html">Widget HTML</label>
        <textarea id="booking_widget_html" name="booking_widget_html" rows="10" style="font-family:monospace;font-size:12px;"><?= htmlspecialchars($sectionsArray['booking_widget_html'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        <p class="form-help">Paste the booking engine embed HTML here. It appears between the hero and the next section (floating over the hero edge on desktop), inside a styled card.</p>
        <p class="form-help">If your provider needs a script tag, add it under <strong>Admin → Settings → Header scripts</strong> or <strong>Body scripts</strong>. The bridge styles target <code>#booking-lusso #booking-widget</code> / <code>#booking-form</code> when your embed uses those ids (e.g. StayEazi).</p>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><h2>Philosophy block</h2></div>
    <div style="padding:20px;">
      <div class="form-group">
        <label for="home_philosophy_kicker">Kicker</label>
        <input id="home_philosophy_kicker" name="home_philosophy_kicker" type="text" value="<?= hsec($sectionsArray, 'home_philosophy_kicker', 'Our Philosophy') ?>">
      </div>
      <div class="form-group">
        <label for="home_philosophy_title_html">Title (HTML)</label>
        <textarea id="home_philosophy_title_html" name="home_philosophy_title_html" rows="2"><?= htmlspecialchars($sectionsArray['home_philosophy_title_html'] ?? 'The Lusso <br/> Standard', ENT_QUOTES, 'UTF-8') ?></textarea>
      </div>
      <div class="form-group">
        <label for="home_philosophy_body">Body</label>
        <textarea id="home_philosophy_body" name="home_philosophy_body" rows="4"><?= hsec($sectionsArray, 'home_philosophy_body') ?></textarea>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="home_philosophy_link_text">Link text</label>
          <input id="home_philosophy_link_text" name="home_philosophy_link_text" type="text" value="<?= hsec($sectionsArray, 'home_philosophy_link_text', 'Read Our Story') ?>">
        </div>
        <div class="form-group">
          <label for="home_philosophy_link_href">Link URL</label>
          <input id="home_philosophy_link_href" name="home_philosophy_link_href" type="text" value="<?= hsec($sectionsArray, 'home_philosophy_link_href', '/about') ?>">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Main image</label>
          <button type="button" class="btn btn-outline" onclick="openMediaModal('home_philosophy_main_img','home_philosophy_main_preview')">Select</button>
          <input type="hidden" id="home_philosophy_main_img" name="home_philosophy_main_img" value="<?= hsec($sectionsArray, 'home_philosophy_main_img') ?>">
          <div id="home_philosophy_main_preview" class="image-preview" style="<?= !empty($sectionsArray['home_philosophy_main_img']) ? 'display:block;' : 'display:none;' ?>">
            <?php if (!empty($sectionsArray['home_philosophy_main_img'])): ?>
              <img src="<?= SITE_URL . ltrim($sectionsArray['home_philosophy_main_img'], '/') ?>" style="max-width:400px;max-height:260px;">
            <?php endif; ?>
          </div>
        </div>
        <div class="form-group">
          <label>Secondary (floating) image</label>
          <button type="button" class="btn btn-outline" onclick="openMediaModal('home_philosophy_secondary_img','home_philosophy_secondary_preview')">Select</button>
          <input type="hidden" id="home_philosophy_secondary_img" name="home_philosophy_secondary_img" value="<?= hsec($sectionsArray, 'home_philosophy_secondary_img') ?>">
          <div id="home_philosophy_secondary_preview" class="image-preview" style="<?= !empty($sectionsArray['home_philosophy_secondary_img']) ? 'display:block;' : 'display:none;' ?>">
            <?php if (!empty($sectionsArray['home_philosophy_secondary_img'])): ?>
              <img src="<?= SITE_URL . ltrim($sectionsArray['home_philosophy_secondary_img'], '/') ?>" style="max-width:400px;max-height:260px;">
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><h2>Architecture block</h2></div>
    <div style="padding:20px;">
      <div class="form-group">
        <label>Wide image</label>
        <button type="button" class="btn btn-outline" onclick="openMediaModal('home_arch_image','home_arch_preview')">Select</button>
        <input type="hidden" id="home_arch_image" name="home_arch_image" value="<?= hsec($sectionsArray, 'home_arch_image') ?>">
        <div id="home_arch_preview" class="image-preview" style="<?= !empty($sectionsArray['home_arch_image']) ? 'display:block;' : 'display:none;' ?>">
          <?php if (!empty($sectionsArray['home_arch_image'])): ?>
            <img src="<?= SITE_URL . ltrim($sectionsArray['home_arch_image'], '/') ?>" style="max-width:500px;max-height:280px;">
          <?php endif; ?>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="home_arch_badge_title">Badge title</label>
          <input id="home_arch_badge_title" name="home_arch_badge_title" type="text" value="<?= hsec($sectionsArray, 'home_arch_badge_title', '5 Star') ?>">
        </div>
        <div class="form-group">
          <label for="home_arch_badge_sub">Badge subtitle</label>
          <input id="home_arch_badge_sub" name="home_arch_badge_sub" type="text" value="<?= hsec($sectionsArray, 'home_arch_badge_sub', 'Diamond Award') ?>">
        </div>
      </div>
      <div class="form-group">
        <label for="home_arch_title">Heading</label>
        <input id="home_arch_title" name="home_arch_title" type="text" value="<?= hsec($sectionsArray, 'home_arch_title', 'Architectural Marvel') ?>">
      </div>
      <div class="form-group">
        <label for="home_arch_body">Body</label>
        <textarea id="home_arch_body" name="home_arch_body" rows="3"><?= hsec($sectionsArray, 'home_arch_body') ?></textarea>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="home_arch_list_1">Bullet 1</label>
          <input id="home_arch_list_1" name="home_arch_list_1" type="text" value="<?= hsec($sectionsArray, 'home_arch_list_1', 'Bespoke Art Installations') ?>">
        </div>
        <div class="form-group">
          <label for="home_arch_list_2">Bullet 2</label>
          <input id="home_arch_list_2" name="home_arch_list_2" type="text" value="<?= hsec($sectionsArray, 'home_arch_list_2', 'Panoramic City Views') ?>">
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><h2>Featured rooms strip</h2></div>
    <div style="padding:20px;">
      <p class="form-help">Cards are loaded from <strong>Rooms</strong> where <strong>Featured</strong> is checked. If none are featured, active rooms are shown instead.</p>
      <div class="form-group">
        <label for="home_rooms_kicker">Kicker</label>
        <input id="home_rooms_kicker" name="home_rooms_kicker" type="text" value="<?= hsec($sectionsArray, 'home_rooms_kicker', 'Accommodations') ?>">
      </div>
      <div class="form-group">
        <label for="home_rooms_title">Title</label>
        <input id="home_rooms_title" name="home_rooms_title" type="text" value="<?= hsec($sectionsArray, 'home_rooms_title', 'Stay in Style') ?>">
      </div>
      <div class="form-group">
        <label for="home_rooms_view_all_href">View all link</label>
        <input id="home_rooms_view_all_href" name="home_rooms_view_all_href" type="text" value="<?= hsec($sectionsArray, 'home_rooms_view_all_href', '/rooms') ?>">
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><h2>Dining teaser</h2></div>
    <div style="padding:20px;">
      <div class="form-group">
        <label>Image</label>
        <button type="button" class="btn btn-outline" onclick="openMediaModal('home_dining_image','home_dining_preview')">Select</button>
        <input type="hidden" id="home_dining_image" name="home_dining_image" value="<?= hsec($sectionsArray, 'home_dining_image') ?>">
        <div id="home_dining_preview" class="image-preview" style="<?= !empty($sectionsArray['home_dining_image']) ? 'display:block;' : 'display:none;' ?>">
          <?php if (!empty($sectionsArray['home_dining_image'])): ?>
            <img src="<?= SITE_URL . ltrim($sectionsArray['home_dining_image'], '/') ?>" style="max-width:500px;max-height:280px;">
          <?php endif; ?>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="home_dining_kicker">Kicker</label>
          <input id="home_dining_kicker" name="home_dining_kicker" type="text" value="<?= hsec($sectionsArray, 'home_dining_kicker', 'Dining') ?>">
        </div>
        <div class="form-group">
          <label for="home_dining_title">Title</label>
          <input id="home_dining_title" name="home_dining_title" type="text" value="<?= hsec($sectionsArray, 'home_dining_title', 'Culinary Excellence') ?>">
        </div>
      </div>
      <div class="form-group">
        <label for="home_dining_body_html">Body (HTML allowed)</label>
        <textarea id="home_dining_body_html" name="home_dining_body_html" rows="3"><?= htmlspecialchars($sectionsArray['home_dining_body_html'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="home_dining_cta1">Button 1</label>
          <input id="home_dining_cta1" name="home_dining_cta1" type="text" value="<?= hsec($sectionsArray, 'home_dining_cta1', 'Reserve a Table') ?>">
        </div>
        <div class="form-group">
          <label for="home_dining_cta1_href">Button 1 URL</label>
          <input id="home_dining_cta1_href" name="home_dining_cta1_href" type="text" value="<?= hsec($sectionsArray, 'home_dining_cta1_href', '/dining') ?>">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="home_dining_cta2">Button 2</label>
          <input id="home_dining_cta2" name="home_dining_cta2" type="text" value="<?= hsec($sectionsArray, 'home_dining_cta2', 'View Menu') ?>">
        </div>
        <div class="form-group">
          <label for="home_dining_cta2_href">Button 2 URL</label>
          <input id="home_dining_cta2_href" name="home_dining_cta2_href" type="text" value="<?= hsec($sectionsArray, 'home_dining_cta2_href', '/dining') ?>">
        </div>
      </div>
    </div>
  </div>

  <button type="submit" class="btn btn-primary">Save all</button>
</form>

<script>
(function () {
  var map = {
    hero_bg: 'hero_bg_preview',
    home_philosophy_main_img: 'home_philosophy_main_preview',
    home_philosophy_secondary_img: 'home_philosophy_secondary_preview',
    home_arch_image: 'home_arch_preview',
    home_dining_image: 'home_dining_preview'
  };
  window.insertSelectedMediaOverride = function () {
    var selected = mediaModalState.selectedMedia;
    if (!selected) return false;
    var tid = mediaModalState.targetInputId;
    var prevId = map[tid];
    if (!prevId) return false;
    var input = document.getElementById(tid);
    var preview = document.getElementById(prevId);
    if (input) input.value = selected.path;
    if (preview) {
      preview.style.display = 'block';
      preview.innerHTML = '<img src="<?= SITE_URL ?>' + selected.path.replace(/^\\//, '') + '" style="max-width:500px;max-height:300px;">';
    }
    closeMediaModal();
    if (typeof showToast === 'function') showToast('Image selected', 'success');
    return true;
  };
})();

document.getElementById('homepageForm').addEventListener('submit', function (e) {
  e.preventDefault();
  var formData = new FormData(this);
  var keys = [];
  formData.forEach(function (_, k) { if (keys.indexOf(k) === -1) keys.push(k); });
  var htmlKeys = { hero_title: 1, home_philosophy_title_html: 1, home_dining_body_html: 1, booking_widget_html: 1 };
  var imageKeys = { hero_bg: 1, home_philosophy_main_img: 1, home_philosophy_secondary_img: 1, home_arch_image: 1, home_dining_image: 1 };
  var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

  Promise.all(keys.map(function (key) {
    return fetch('<?= ADMIN_URL ?>api/pages.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrfToken },
      body: JSON.stringify({
        page: 'index',
        section_key: key,
        content_type: htmlKeys[key] ? 'html' : (imageKeys[key] ? 'image' : 'text'),
        content: formData.get(key) || ''
      })
    }).then(function (r) { return r.json(); });
  })).then(function (results) {
    var ok = results.every(function (r) { return r.success; });
    showToast(ok ? 'Saved' : 'Some fields failed', ok ? 'success' : 'warning');
  }).catch(function () { showToast('Save failed', 'error'); });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
