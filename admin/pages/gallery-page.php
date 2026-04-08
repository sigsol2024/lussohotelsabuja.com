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
      <p class="form-help">Tip: the word wrapped in <code>text-primary</code> in the hero title gets a subtle white outline automatically.</p>
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
    <div class="card-header"><h2>Gallery grid</h2></div>
    <div style="padding:20px;">
      <p class="form-help" style="margin-top:0;">Visual editor. Add/remove/reorder images (saved as JSON behind the scenes).</p>
      <div class="form-group">
        <textarea id="items_json" name="items_json" style="display:none;"><?= htmlspecialchars($itemsRaw, ENT_QUOTES, 'UTF-8') ?></textarea>
        <div id="galleryItemsEditor"></div>
        <div style="margin-top:12px; display:flex; gap:10px; flex-wrap:wrap;">
          <button type="button" class="btn btn-outline btn-sm" id="galleryAddItemBtn">Add image</button>
          <button type="button" class="btn btn-outline btn-sm" id="galleryAddFromLibraryBtn"><i class="fas fa-images"></i> Add from library</button>
        </div>

        <details style="margin-top:14px;">
          <summary style="cursor:pointer; color: var(--text-muted);">Advanced JSON (optional)</summary>
          <textarea id="items_json_advanced" rows="18" style="margin-top:10px;font-family:monospace;font-size:12px;"></textarea>
          <div style="margin-top:10px;">
            <button type="button" class="btn btn-outline btn-sm" id="galleryApplyJsonBtn">Apply JSON</button>
          </div>
        </details>
      </div>
    </div>
  </div>

  <button type="submit" class="btn btn-primary">Save</button>
</form>

<script>
function gallerySafeParseJson(text, fallback) {
  try { return JSON.parse(text || ''); } catch (e) { return fallback; }
}
function galleryEscHtml(s) {
  return String(s || '').replace(/[&<>"']/g, function (m) {
    return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[m];
  });
}
function galleryNormalizeImgUrl(val) {
  var v = String(val || '').trim();
  if (!v) return '';
  if (v.indexOf('http') === 0) return v;
  return '<?= SITE_URL ?>' + v.replace(/^\/+/, '');
}

function galleryGetItemsFromDom() {
  var out = [];
  document.querySelectorAll('#galleryItemsEditor .js-gal-item').forEach(function (card) {
    var src = (card.querySelector('.js-src')?.value || '').trim();
    var alt = (card.querySelector('.js-alt')?.value || '').trim();
    var category = (card.querySelector('.js-category')?.value || '').trim();
    var title = (card.querySelector('.js-title')?.value || '').trim();
    var ratio = (card.querySelector('.js-ratio')?.value || '3/4').trim() || '3/4';
    if (!src && !alt && !category && !title) return;
    out.push({ src: src, alt: alt, category: category, title: title, ratio: ratio });
  });
  return out;
}

function gallerySyncHiddenJson() {
  var items = galleryGetItemsFromDom();
  var hidden = document.getElementById('items_json');
  if (hidden) hidden.value = JSON.stringify(items);
  var adv = document.getElementById('items_json_advanced');
  if (adv) adv.value = JSON.stringify(items, null, 2);
}

function galleryRender(items) {
  var host = document.getElementById('galleryItemsEditor');
  if (!host) return;
  host.innerHTML = '';

  (items || []).forEach(function (it, idx) {
    var src = (it && it.src) || '';
    var alt = (it && it.alt) || '';
    var category = (it && it.category) || '';
    var title = (it && it.title) || '';
    var ratio = (it && it.ratio) || '3/4';

    var srcInputId = 'gallery_item_' + idx + '_src';
    var prevId = 'gallery_item_' + idx + '_preview';
    var imgUrl = galleryNormalizeImgUrl(src);

    var wrap = document.createElement('div');
    wrap.className = 'card js-gal-item';
    wrap.style.cssText = 'margin-bottom: 14px;';
    wrap.innerHTML =
      '<div class="card-header card-header--split" style="display:flex; justify-content:space-between; align-items:center; gap: 10px;">' +
        '<h3 style="margin:0;">Image ' + (idx + 1) + '</h3>' +
        '<div style="display:flex; gap:8px; flex-wrap:wrap; justify-content:flex-end;">' +
          '<button type="button" class="btn btn-outline btn-sm js-up">Up</button>' +
          '<button type="button" class="btn btn-outline btn-sm js-down">Down</button>' +
          '<button type="button" class="btn btn-outline btn-sm js-remove">Remove</button>' +
        '</div>' +
      '</div>' +
      '<div class="card-body card-body--stack" style="padding: 14px 16px;">' +
        '<div class="form-group">' +
          '<label>Image</label>' +
          '<div style="display:flex; gap: 10px; align-items:center; flex-wrap:wrap;">' +
            '<button type="button" class="btn btn-outline js-pick">Select from media</button>' +
            '<input type="text" id="' + galleryEscHtml(srcInputId) + '" class="form-control js-src" value="' + galleryEscHtml(src) + '" placeholder="/assets/uploads/... or https://...">' +
          '</div>' +
          '<div id="' + galleryEscHtml(prevId) + '" class="image-preview" style="' + (imgUrl ? 'display:block;margin-top:10px;' : 'display:none;margin-top:10px;') + '">' +
            (imgUrl ? ('<img src="' + galleryEscHtml(imgUrl) + '" style="max-width:420px;max-height:240px;border-radius:6px;">') : '') +
          '</div>' +
        '</div>' +
        '<div class="form-row">' +
          '<div class="form-group" style="flex:1;">' +
            '<label>Title</label>' +
            '<input type="text" class="form-control js-title" value="' + galleryEscHtml(title) + '">' +
          '</div>' +
          '<div class="form-group" style="flex:1;">' +
            '<label>Category</label>' +
            '<input type="text" class="form-control js-category" value="' + galleryEscHtml(category) + '" placeholder="Architecture">' +
          '</div>' +
        '</div>' +
        '<div class="form-row">' +
          '<div class="form-group" style="flex:2;">' +
            '<label>Alt text</label>' +
            '<input type="text" class="form-control js-alt" value="' + galleryEscHtml(alt) + '" placeholder="Describe the image">' +
          '</div>' +
          '<div class="form-group" style="flex:1;">' +
            '<label>Ratio</label>' +
            '<select class="form-control js-ratio">' +
              '<option value="3/4"' + (ratio === '3/4' ? ' selected' : '') + '>3/4</option>' +
              '<option value="video"' + (ratio === 'video' ? ' selected' : '') + '>video</option>' +
              '<option value="2/3"' + (ratio === '2/3' ? ' selected' : '') + '>2/3</option>' +
              '<option value="square"' + (ratio === 'square' ? ' selected' : '') + '>square</option>' +
              '<option value="16/10"' + (ratio === '16/10' ? ' selected' : '') + '>16/10</option>' +
              '<option value="3/5"' + (ratio === '3/5' ? ' selected' : '') + '>3/5</option>' +
              '<option value="4/5"' + (ratio === '4/5' ? ' selected' : '') + '>4/5</option>' +
            '</select>' +
          '</div>' +
        '</div>' +
      '</div>';

    wrap.querySelector('.js-pick').addEventListener('click', function () {
      openMediaModal(srcInputId, prevId, false);
    });
    wrap.querySelector('.js-remove').addEventListener('click', function () {
      wrap.remove();
      gallerySyncHiddenJson();
      galleryRender(galleryGetItemsFromDom());
      gallerySyncHiddenJson();
    });
    wrap.querySelector('.js-up').addEventListener('click', function () {
      var all = galleryGetItemsFromDom();
      if (idx <= 0) return;
      var t = all[idx - 1]; all[idx - 1] = all[idx]; all[idx] = t;
      galleryRender(all); gallerySyncHiddenJson();
    });
    wrap.querySelector('.js-down').addEventListener('click', function () {
      var all = galleryGetItemsFromDom();
      if (idx >= all.length - 1) return;
      var t = all[idx + 1]; all[idx + 1] = all[idx]; all[idx] = t;
      galleryRender(all); gallerySyncHiddenJson();
    });
    wrap.addEventListener('input', function () {
      // Live preview when pasting URL/path
      var v = (wrap.querySelector('.js-src')?.value || '').trim();
      var p = document.getElementById(prevId);
      if (p) {
        var u = galleryNormalizeImgUrl(v);
        if (!u) { p.style.display = 'none'; p.innerHTML = ''; }
        else { p.style.display = 'block'; p.innerHTML = '<img src="' + galleryEscHtml(u) + '" style="max-width:420px;max-height:240px;border-radius:6px;">'; }
      }
      gallerySyncHiddenJson();
    });
    wrap.addEventListener('change', function () { gallerySyncHiddenJson(); });
    host.appendChild(wrap);
  });
}

// Media picker overrides (hero background + gallery item images + multi-add)
window.insertSelectedMediaOverride = function () {
  var tid = mediaModalState.targetInputId || '';
  var allowMultiple = !!mediaModalState.allowMultiple;
  var selected = allowMultiple ? mediaModalState.selectedMediaMultiple : (mediaModalState.selectedMedia ? [mediaModalState.selectedMedia] : []);
  if (!selected.length) return false;

  // Hero BG
  if (tid === 'hero_bg') {
    var s = selected[0];
    document.getElementById('hero_bg').value = s.path;
    var p = document.getElementById('hero_bg_preview');
    p.style.display = 'block';
    p.innerHTML = '<img src="<?= SITE_URL ?>' + s.path.replace(/^\/+/, '') + '" style="max-width:500px;max-height:280px;">';
    closeMediaModal();
    return true;
  }

  // Add multiple images at once
  if (tid === 'gallery_items_pick') {
    var cur = galleryGetItemsFromDom();
    selected.forEach(function (s) {
      cur.push({ src: s.path, alt: s.original_name || '', category: '', title: '', ratio: '3/4' });
    });
    galleryRender(cur);
    gallerySyncHiddenJson();
    closeMediaModal();
    if (typeof showToast === 'function') showToast(selected.length + ' images added', 'success');
    return true;
  }

  // Per-item src update
  if (tid.indexOf('gallery_item_') === 0 && tid.indexOf('_src') !== -1) {
    var one = selected[0];
    var input = document.getElementById(tid);
    if (input) input.value = one.path;
    var prev = mediaModalState.targetPreviewId ? document.getElementById(mediaModalState.targetPreviewId) : null;
    if (prev) {
      prev.style.display = 'block';
      prev.innerHTML = '<img src="<?= SITE_URL ?>' + one.path.replace(/^\/+/, '') + '" style="max-width:420px;max-height:240px;border-radius:6px;">';
    }
    closeMediaModal();
    gallerySyncHiddenJson();
    if (typeof showToast === 'function') showToast('Image selected', 'success');
    return true;
  }

  return false;
};

document.addEventListener('DOMContentLoaded', function () {
  var raw = document.getElementById('items_json')?.value || '[]';
  var items = gallerySafeParseJson(raw, []);
  if (!Array.isArray(items)) items = [];
  if (items.length === 0) items = [{ src: '', alt: '', category: '', title: '', ratio: '3/4' }];
  galleryRender(items);
  gallerySyncHiddenJson();

  var addBtn = document.getElementById('galleryAddItemBtn');
  if (addBtn) addBtn.addEventListener('click', function () {
    var cur = galleryGetItemsFromDom();
    cur.push({ src: '', alt: '', category: '', title: '', ratio: '3/4' });
    galleryRender(cur);
    gallerySyncHiddenJson();
  });

  var addLibBtn = document.getElementById('galleryAddFromLibraryBtn');
  if (addLibBtn) addLibBtn.addEventListener('click', function () {
    openMediaModal('gallery_items_pick', null, true);
  });

  var applyBtn = document.getElementById('galleryApplyJsonBtn');
  if (applyBtn) applyBtn.addEventListener('click', function () {
    var t = document.getElementById('items_json_advanced')?.value || '';
    var v = gallerySafeParseJson(t, null);
    if (!Array.isArray(v)) {
      showToast('Gallery JSON must be an array', 'error');
      return;
    }
    galleryRender(v);
    gallerySyncHiddenJson();
    showToast('Gallery applied', 'success');
  });
});

document.getElementById('galleryPageForm').addEventListener('submit', function (e) {
  e.preventDefault();
  savePageForm(this, 'gallery')
    .then(function () { showToast('Saved', 'success'); })
    .catch(function (err) { showToast(err.message || 'Save failed', 'error'); });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
