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
    <div class="card-header"><h2>Full-screen sections</h2></div>
    <div style="padding:20px;">
      <p class="form-help" style="margin-top:0;">Visual editor. Add/remove sections and edit content below (saved as JSON behind the scenes).</p>

      <textarea id="sections_json" name="sections_json" style="display:none;"><?= htmlspecialchars($raw, ENT_QUOTES, 'UTF-8') ?></textarea>
      <div id="amenitiesSectionsEditor"></div>
      <div style="margin-top:12px; display:flex; gap:10px; flex-wrap:wrap;">
        <button type="button" class="btn btn-outline btn-sm" id="amenitiesAddSectionBtn">Add section</button>
      </div>

      <details style="margin-top:14px;">
        <summary style="cursor:pointer; color: var(--text-muted);">Advanced JSON (optional)</summary>
        <textarea id="sections_json_advanced" rows="18" style="margin-top:10px;font-family:monospace;font-size:12px;"></textarea>
        <div style="margin-top:10px;">
          <button type="button" class="btn btn-outline btn-sm" id="amenitiesApplyJsonBtn">Apply JSON</button>
        </div>
      </details>
    </div>
  </div>

  <button type="submit" class="btn btn-primary">Save</button>
</form>

<script>
function amenitiesSafeParseJson(text, fallback) {
  try { return JSON.parse(text || ''); } catch (e) { return fallback; }
}
function amenitiesEscHtml(s) {
  return String(s || '').replace(/[&<>"']/g, function (m) {
    return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[m];
  });
}
function amenitiesNormalizeImgUrl(val) {
  var v = String(val || '').trim();
  if (!v) return '';
  if (v.indexOf('http') === 0) return v;
  return '<?= SITE_URL ?>' + v.replace(/^\/+/, '');
}

function amenitiesGetItemsFromDom() {
  var out = [];
  document.querySelectorAll('#amenitiesSectionsEditor .js-amen-sec').forEach(function (card) {
    var bg = (card.querySelector('.js-bg')?.value || '').trim();
    var gradient = (card.querySelector('.js-gradient')?.value || '').trim();
    var kicker = (card.querySelector('.js-kicker')?.value || '').trim();
    var icon = (card.querySelector('.js-icon')?.value || '').trim();
    var title_html = (card.querySelector('.js-title-html')?.value || '').trim();
    var body = (card.querySelector('.js-body')?.value || '').trim();
    var btn = (card.querySelector('.js-btn')?.value || '').trim();
    var btn_href = (card.querySelector('.js-btn-href')?.value || '').trim();
    var layout = (card.querySelector('.js-layout')?.value || 'bottom').trim() || 'bottom';
    out.push({ bg: bg, gradient: gradient, kicker: kicker, icon: icon, title_html: title_html, body: body, btn: btn, btn_href: btn_href, layout: layout });
  });
  return out;
}

function amenitiesSyncHiddenJson() {
  var items = amenitiesGetItemsFromDom();
  var hidden = document.getElementById('sections_json');
  if (hidden) hidden.value = JSON.stringify(items);
  var adv = document.getElementById('sections_json_advanced');
  if (adv) adv.value = JSON.stringify(items, null, 2);
}

function amenitiesRender(items) {
  var host = document.getElementById('amenitiesSectionsEditor');
  if (!host) return;
  host.innerHTML = '';

  (items || []).forEach(function (it, idx) {
    var bg = (it && it.bg) || '';
    var gradient = (it && it.gradient) || 'linear-gradient(rgba(0, 0, 0, 0.35) 0%, rgba(0, 0, 0, 0.75) 100%)';
    var kicker = (it && it.kicker) || '';
    var icon = (it && it.icon) || 'star';
    var titleHtml = (it && it.title_html) || '';
    var body = (it && it.body) || '';
    var btn = (it && it.btn) || '';
    var btnHref = (it && it.btn_href) || '';
    var layout = (it && it.layout) || 'bottom';

    var inputId = 'amenity_section_' + idx + '_bg';
    var prevId = 'amenity_section_' + idx + '_bg_preview';
    var imgUrl = amenitiesNormalizeImgUrl(bg);

    var wrap = document.createElement('div');
    wrap.className = 'card js-amen-sec';
    wrap.style.cssText = 'margin-bottom: 14px;';
    wrap.innerHTML =
      '<div class="card-header card-header--split" style="display:flex; justify-content:space-between; align-items:center; gap: 10px;">' +
        '<h3 style="margin:0;">Section ' + (idx + 1) + '</h3>' +
        '<div style="display:flex; gap:8px; flex-wrap:wrap; justify-content:flex-end;">' +
          '<button type="button" class="btn btn-outline btn-sm js-up">Up</button>' +
          '<button type="button" class="btn btn-outline btn-sm js-down">Down</button>' +
          '<button type="button" class="btn btn-outline btn-sm js-remove">Remove</button>' +
        '</div>' +
      '</div>' +
      '<div class="card-body card-body--stack" style="padding: 14px 16px;">' +
        '<div class="form-group">' +
          '<label>Background image</label>' +
          '<div style="display:flex; gap: 10px; align-items:center; flex-wrap:wrap;">' +
            '<button type="button" class="btn btn-outline js-pick">Select from media</button>' +
            '<input type="text" id="' + amenitiesEscHtml(inputId) + '" class="form-control js-bg" value="' + amenitiesEscHtml(bg) + '" placeholder="/assets/uploads/... or https://...">' +
          '</div>' +
          '<div id="' + amenitiesEscHtml(prevId) + '" class="image-preview" style="' + (imgUrl ? 'display:block;margin-top:10px;' : 'display:none;margin-top:10px;') + '">' +
            (imgUrl ? ('<img src="' + amenitiesEscHtml(imgUrl) + '" style="max-width:420px;max-height:240px;border-radius:6px;">') : '') +
          '</div>' +
        '</div>' +
        '<div class="form-row">' +
          '<div class="form-group" style="flex:1;">' +
            '<label>Gradient overlay (CSS)</label>' +
            '<input type="text" class="form-control js-gradient" value="' + amenitiesEscHtml(gradient) + '" placeholder="linear-gradient(...)">' +
          '</div>' +
        '</div>' +
        '<div class="form-row">' +
          '<div class="form-group" style="flex:1;">' +
            '<label>Kicker</label>' +
            '<input type="text" class="form-control js-kicker" value="' + amenitiesEscHtml(kicker) + '" placeholder="01 / Dining">' +
          '</div>' +
          '<div class="form-group" style="flex:1;">' +
            '<label>Icon (Material symbol name)</label>' +
            '<input type="text" class="form-control js-icon" value="' + amenitiesEscHtml(icon) + '" placeholder="restaurant">' +
          '</div>' +
          '<div class="form-group" style="flex:1;">' +
            '<label>Layout</label>' +
            '<select class="form-control js-layout">' +
              '<option value="bottom"' + (layout === 'bottom' ? ' selected' : '') + '>bottom</option>' +
              '<option value="right"' + (layout === 'right' ? ' selected' : '') + '>right</option>' +
              '<option value="top"' + (layout === 'top' ? ' selected' : '') + '>top</option>' +
              '<option value="center"' + (layout === 'center' ? ' selected' : '') + '>center</option>' +
            '</select>' +
          '</div>' +
        '</div>' +
        '<div class="form-group">' +
          '<label>Title (HTML)</label>' +
          '<textarea class="form-control js-title-html" rows="3" style="font-family:monospace;font-size:12px;">' + amenitiesEscHtml(titleHtml) + '</textarea>' +
        '</div>' +
        '<div class="form-group">' +
          '<label>Body</label>' +
          '<textarea class="form-control js-body" rows="3">' + amenitiesEscHtml(body) + '</textarea>' +
        '</div>' +
        '<div class="form-row">' +
          '<div class="form-group" style="flex:1;">' +
            '<label>Button label</label>' +
            '<input type="text" class="form-control js-btn" value="' + amenitiesEscHtml(btn) + '" placeholder="Explore">' +
          '</div>' +
          '<div class="form-group" style="flex:1;">' +
            '<label>Button link</label>' +
            '<input type="text" class="form-control js-btn-href" value="' + amenitiesEscHtml(btnHref) + '" placeholder="/amenities">' +
          '</div>' +
        '</div>' +
      '</div>';

    wrap.querySelector('.js-pick').addEventListener('click', function () {
      openMediaModal(inputId, prevId, false);
    });
    wrap.querySelector('.js-remove').addEventListener('click', function () {
      wrap.remove();
      amenitiesSyncHiddenJson();
      // Re-render to fix indexes + media ids
      amenitiesRender(amenitiesGetItemsFromDom());
      amenitiesSyncHiddenJson();
    });
    wrap.querySelector('.js-up').addEventListener('click', function () {
      var all = amenitiesGetItemsFromDom();
      if (idx <= 0) return;
      var tmp = all[idx - 1];
      all[idx - 1] = all[idx];
      all[idx] = tmp;
      amenitiesRender(all);
      amenitiesSyncHiddenJson();
    });
    wrap.querySelector('.js-down').addEventListener('click', function () {
      var all = amenitiesGetItemsFromDom();
      if (idx >= all.length - 1) return;
      var tmp = all[idx + 1];
      all[idx + 1] = all[idx];
      all[idx] = tmp;
      amenitiesRender(all);
      amenitiesSyncHiddenJson();
    });
    wrap.addEventListener('input', function () {
      // Update preview if user pastes URL/path
      var v = (wrap.querySelector('.js-bg')?.value || '').trim();
      var p = document.getElementById(prevId);
      if (p) {
        var u = amenitiesNormalizeImgUrl(v);
        if (!u) { p.style.display = 'none'; p.innerHTML = ''; }
        else { p.style.display = 'block'; p.innerHTML = '<img src="' + amenitiesEscHtml(u) + '" style="max-width:420px;max-height:240px;border-radius:6px;">'; }
      }
      amenitiesSyncHiddenJson();
    });
    wrap.addEventListener('change', function () { amenitiesSyncHiddenJson(); });
    host.appendChild(wrap);
  });
}

document.getElementById('amenitiesPageForm').addEventListener('submit', function (e) {
  e.preventDefault();
  savePageForm(this, 'amenities')
    .then(function () { showToast('Saved', 'success'); })
    .catch(function (err) { showToast(err.message || 'Save failed', 'error'); });
});

document.addEventListener('DOMContentLoaded', function () {
  var raw = document.getElementById('sections_json')?.value || '[]';
  var items = amenitiesSafeParseJson(raw, []);
  if (!Array.isArray(items)) items = [];
  if (items.length === 0) items = [{
    bg: '',
    gradient: 'linear-gradient(rgba(0, 0, 0, 0.35) 0%, rgba(0, 0, 0, 0.75) 100%)',
    kicker: '',
    icon: 'star',
    title_html: '',
    body: '',
    btn: '',
    btn_href: '',
    layout: 'bottom'
  }];
  amenitiesRender(items);
  amenitiesSyncHiddenJson();

  var addBtn = document.getElementById('amenitiesAddSectionBtn');
  if (addBtn) addBtn.addEventListener('click', function () {
    var cur = amenitiesGetItemsFromDom();
    cur.push({
      bg: '',
      gradient: 'linear-gradient(rgba(0, 0, 0, 0.35) 0%, rgba(0, 0, 0, 0.75) 100%)',
      kicker: '',
      icon: 'star',
      title_html: '',
      body: '',
      btn: '',
      btn_href: '',
      layout: 'bottom'
    });
    amenitiesRender(cur);
    amenitiesSyncHiddenJson();
  });

  var applyBtn = document.getElementById('amenitiesApplyJsonBtn');
  if (applyBtn) applyBtn.addEventListener('click', function () {
    var t = document.getElementById('sections_json_advanced')?.value || '';
    var v = amenitiesSafeParseJson(t, null);
    if (!Array.isArray(v)) {
      showToast('Sections JSON must be an array', 'error');
      return;
    }
    amenitiesRender(v);
    amenitiesSyncHiddenJson();
    showToast('Sections applied', 'success');
  });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
