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
// Force new format only: JSON array of image paths.
// If old object-array is found (e.g. [{src: "..."}]), normalize it to ["...","..."] so the admin UI stays clean.
$decodedItems = json_decode($itemsRaw, true);
if (is_array($decodedItems) && count($decodedItems) > 0) {
    if (is_array($decodedItems[0]) && isset($decodedItems[0]['src'])) {
        $decodedItems = array_values(array_filter(array_map(static function ($o) {
            $src = is_array($o) ? (string)($o['src'] ?? '') : '';
            $src = trim($src);
            return $src !== '' ? $src : null;
        }, $decodedItems)));
        $itemsRaw = json_encode($decodedItems, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    } elseif (!is_string($decodedItems[0])) {
        // Unknown / invalid structure: reset to defaults (new format).
        $itemsRaw = $itemsDefault;
    }
} elseif (!is_array($decodedItems)) {
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
      <p class="form-help" style="margin-top:0;">Simple picker. Select multiple images at once, remove individual images, or clear all (saved as JSON array of image paths).</p>
      <div class="form-group">
        <textarea id="items_json" name="items_json" style="display:none;"><?= htmlspecialchars($itemsRaw, ENT_QUOTES, 'UTF-8') ?></textarea>
        <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
          <button type="button" class="btn btn-outline btn-sm" id="galleryPickBtn"><i class="fas fa-images"></i> Select images</button>
          <button type="button" class="btn btn-outline btn-sm" id="galleryClearBtn">Clear all</button>
          <span class="text-muted" id="galleryCount" style="font-size: 12px;"></span>
        </div>
        <div id="galleryPreview" class="image-preview" style="display:block; margin-top:12px;"></div>
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
function galleryGetPaths() {
  var raw = document.getElementById('items_json')?.value || '[]';
  var v = gallerySafeParseJson(raw, []);
  if (Array.isArray(v)) return v.map(function (p) { return String(p || '').trim(); }).filter(Boolean);
  return [];
}
function gallerySetPaths(paths) {
  var hidden = document.getElementById('items_json');
  if (hidden) hidden.value = JSON.stringify(paths || []);
  renderPreview();
}
function renderPreview() {
  var paths = galleryGetPaths();
  var host = document.getElementById('galleryPreview');
  var count = document.getElementById('galleryCount');
  if (count) count.textContent = paths.length ? (paths.length + ' images selected') : 'No images selected yet.';
  if (!host) return;
  if (!paths.length) {
    host.innerHTML = '<div style="color: var(--text-muted); font-size: 12px; padding: 8px 0;">No images selected.</div>';
    return;
  }
  host.innerHTML = paths.map(function (p, i) {
    var u = galleryNormalizeImgUrl(p);
    return '' +
      '<div style="display:inline-block; position:relative; margin:6px;">' +
        '<img src="' + galleryEscHtml(u) + '" style="width:140px;height:105px;object-fit:cover;border-radius:8px;display:block;border:1px solid rgba(0,0,0,0.08);">' +
        '<button type="button" data-i="' + i + '" class="btn btn-outline btn-sm js-remove-one" style="position:absolute; top:6px; right:6px; padding:4px 6px; line-height:1;">×</button>' +
      '</div>';
  }).join('');
  host.querySelectorAll('.js-remove-one').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var idx = parseInt(btn.getAttribute('data-i') || '0', 10);
      var cur = galleryGetPaths();
      cur.splice(idx, 1);
      gallerySetPaths(cur);
    });
  });
}

window.insertSelectedMediaOverride = function () {
  var tid = mediaModalState.targetInputId || '';
  var selected = mediaModalState.allowMultiple ? mediaModalState.selectedMediaMultiple : (mediaModalState.selectedMedia ? [mediaModalState.selectedMedia] : []);
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

  // Gallery multi-pick
  if (tid === 'items_json_pick') {
    var paths = selected.map(function (s) { return s.path; });
    gallerySetPaths(paths);
    closeMediaModal();
    if (typeof showToast === 'function') showToast(paths.length + ' images selected', 'success');
    return true;
  }

  return false;
};

document.addEventListener('DOMContentLoaded', function () {
  renderPreview();
  var pickBtn = document.getElementById('galleryPickBtn');
  var clearBtn = document.getElementById('galleryClearBtn');
  if (pickBtn) pickBtn.addEventListener('click', function () {
    openMediaModal('items_json_pick', 'galleryPreview', true);
  });
  if (clearBtn) clearBtn.addEventListener('click', function () {
    gallerySetPaths([]);
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
