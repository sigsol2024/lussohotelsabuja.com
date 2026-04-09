<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
requireLogin();

if (defined('LUSSO_ROOM_FORM_ADD') && LUSSO_ROOM_FORM_ADD) {
  $id = 0;
} else {
  $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
  if ($id < 1) {
    header('Location: ' . ADMIN_URL . 'pages/rooms/add.php');
    exit;
  }
}
$pageTitle = $id ? 'Edit room' : 'Add room';
require_once __DIR__ . '/../../includes/header.php';
$room = [
  'title' => '',
  'slug' => '',
  'price' => '',
  'room_type' => '',
  'short_description' => '',
  'description' => '',
  'main_image' => '',
  'gallery_images' => [],
  'features' => [],
  'amenities' => [],
  'included_items' => [],
  'good_to_know' => [],
  'urgency_message' => '',
  'size' => '',
  'max_guests' => 0,
  'location' => '',
  'book_url' => '',
  'is_active' => 1,
  'is_featured' => 0,
  'display_order' => 0,
];

if ($id) {
  try {
    $stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = ?");
    $stmt->execute([$id]);
    $dbRoom = $stmt->fetch();
    if ($dbRoom) {
      // Historical bug: some fields were stored HTML-escaped (e.g. "&amp;" in DB).
      // Decode once for editing; output escaping still happens in templates via sanitize().
      foreach (['title','slug','room_type','short_description','description','main_image','urgency_message','size','location','book_url'] as $k) {
        if (isset($dbRoom[$k]) && is_string($dbRoom[$k]) && strpos($dbRoom[$k], '&') !== false) {
          $dbRoom[$k] = html_entity_decode($dbRoom[$k], ENT_QUOTES, 'UTF-8');
        }
      }
      $room = array_merge($room, $dbRoom);
      $room['gallery_images'] = json_decode($dbRoom['gallery_images'] ?? '[]', true) ?: [];
      $room['features'] = json_decode($dbRoom['features'] ?? '[]', true) ?: [];
      $room['amenities'] = json_decode($dbRoom['amenities'] ?? '[]', true) ?: [];
      $room['included_items'] = json_decode($dbRoom['included_items'] ?? '[]', true) ?: [];
      $gtk = json_decode($dbRoom['good_to_know'] ?? '{}', true);
      $room['good_to_know'] = is_array($gtk) ? $gtk : [];
    }
  } catch (PDOException $e) {}
}


require_once dirname(__DIR__, 3) . '/includes/url.php';
$roomPublicUrlBase = rtrim((string)(defined('SITE_URL') ? SITE_URL : ''), '/');
?>

<form id="roomForm">
  <div class="card">
    <div class="card-header card-header--split">
      <div>
        <h2><?= $id ? 'Room details' : 'New room' ?></h2>
        <p class="text-muted" style="margin: 6px 0 0; font-size: 14px; font-weight: 400;"><?= $id ? 'Update content, media, and guest information below.' : 'Fill in the basics, then save to create the listing.' ?></p>
      </div>
      <a href="<?= ADMIN_URL ?>pages/rooms/list.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back to rooms</a>
    </div>
    <div class="card-body card-body--stack">
      <?php if ($id && empty($room['is_active'])): ?>
      <div class="card card--nested" style="margin-bottom: 1.25rem; border-color: var(--warning, #b8860b); background: rgba(184, 134, 11, 0.08);">
        <div class="card-body" style="padding: 14px 16px;">
          <strong>Draft</strong> — This room is not visible on the public site. Update the title and slug if needed, then check <strong>Active</strong> and save to publish.
        </div>
      </div>
      <?php endif; ?>
      <div class="form-row">
        <div class="form-group">
          <label for="title">Title</label>
          <input id="title" name="title" type="text" value="<?= sanitize($room['title']) ?>" autocomplete="off">
        </div>
        <div class="form-group">
          <label for="slug">Slug (URL segment)</label>
          <input id="slug" name="slug" type="text" value="<?= sanitize($room['slug']) ?>" autocomplete="off">
          <p class="form-help" id="slugPreview">Public URL updates as you type.</p>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="room_type">Room type (hero accent, e.g. Suite)</label>
          <input id="room_type" name="room_type" type="text" value="<?= sanitize($room['room_type'] ?? '') ?>" placeholder="Suite">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="price">Price</label>
          <input id="price" name="price" type="number" step="0.01" value="<?= sanitize($room['price']) ?>">
        </div>
        <div class="form-group">
          <label for="display_order">Display order</label>
          <input id="display_order" name="display_order" type="number" value="<?= (int)$room['display_order'] ?>">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="size">Size (e.g. 45 SQM)</label>
          <input id="size" name="size" type="text" value="<?= sanitize($room['size']) ?>">
        </div>
        <div class="form-group">
          <label for="max_guests">Max guests</label>
          <input id="max_guests" name="max_guests" type="number" value="<?= (int)$room['max_guests'] ?>">
        </div>
      </div>

      <div class="form-group">
        <label for="book_url">Book URL</label>
        <input id="book_url" name="book_url" type="text" value="<?= sanitize($room['book_url']) ?>">
      </div>

      <div class="form-group">
        <label for="short_description">Concept / pull quote (hero editorial line)</label>
        <textarea id="short_description" name="short_description" rows="2"><?= sanitize($room['short_description']) ?></textarea>
      </div>
      <div class="form-group">
        <label for="description">Description (first block = “The Space”, second block after blank line = “The Experience”)</label>
        <textarea id="description" name="description" rows="8"><?= sanitize($room['description']) ?></textarea>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Main image</label>
          <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
            <button type="button" class="btn btn-outline" onclick="openMediaModal('main_image','main_image_preview')">Select Image</button>
            <button type="button" class="btn btn-outline btn-sm" id="mainImageRemoveBtn" style="display:none;">Remove</button>
          </div>
          <input type="hidden" id="main_image" name="main_image" value="<?= sanitize($room['main_image']) ?>">
          <div id="main_image_preview" class="image-preview" style="<?= !empty($room['main_image']) ? 'display:block;' : 'display:none;' ?>">
            <?php if (!empty($room['main_image'])): ?>
              <div class="lusso-media-thumb" style="position:relative; display:inline-block; margin-top:10px;">
                <img src="<?= SITE_URL . ltrim($room['main_image'], '/') ?>" style="max-width:300px;max-height:200px;border-radius:6px;display:block;object-fit:cover;">
              </div>
            <?php endif; ?>
          </div>
        </div>
        <div class="form-group">
          <label>Gallery images</label>
          <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
            <button type="button" class="btn btn-outline" onclick="openMediaModal('gallery_images','gallery_images_preview', true)">Select Images</button>
            <button type="button" class="btn btn-outline btn-sm" id="galleryClearBtn" style="display:none;">Clear all</button>
          </div>
          <input type="hidden" id="gallery_images" name="gallery_images" value="<?= htmlspecialchars(json_encode($room['gallery_images']), ENT_QUOTES, 'UTF-8') ?>">
          <div id="gallery_images_preview" class="image-preview" style="<?= !empty($room['gallery_images']) ? 'display:block;' : 'display:none;' ?>">
            <?php foreach (($room['gallery_images'] ?? []) as $i => $img): ?>
              <div class="lusso-media-thumb" data-i="<?= (int)$i ?>" style="position:relative; display:inline-block; margin:6px;">
                <img src="<?= SITE_URL . ltrim($img, '/') ?>" style="width:120px;height:120px;display:block;border-radius:8px;object-fit:cover;border:1px solid rgba(0,0,0,0.08);">
                <button type="button" class="btn btn-outline btn-sm js-room-gallery-remove" data-i="<?= (int)$i ?>" style="position:absolute; top:6px; right:6px; padding:4px 6px; line-height:1;">×</button>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <div class="card card--nested" style="margin-top: 1rem;">
        <div class="card-header"><h3>Listing highlights</h3></div>
        <div class="card-body card-body--stack">
          <p class="form-help">Short labels shown as highlights on the Rooms listing and Room details pages (e.g. King bed, City view, 45 SQM).</p>
          <div id="features-container">
            <?php
            $featList = $room['features'] ?? [];
            if (!is_array($featList)) {
                $featList = [];
            }
            foreach ($featList as $f) {
                $ft = is_array($f) ? (string)($f['title'] ?? $f['name'] ?? '') : (string)$f;
                if ($ft === '') {
                    continue;
                }
                ?>
            <div class="form-row lusso-repeat-row" style="align-items: flex-end; gap: 8px;">
              <div class="form-group" style="flex: 1; margin-bottom: 0;">
                <input type="text" class="form-control js-feature-input" value="<?= sanitize($ft) ?>" placeholder="e.g. King bed, City view">
              </div>
              <button type="button" class="btn btn-outline btn-sm" onclick="lussoRemoveRepeatRow(this)">Remove</button>
            </div>
            <?php } ?>
          </div>
          <button type="button" class="btn btn-outline btn-sm" onclick="lussoAddFeature()">Add feature</button>
        </div>
      </div>

      <div class="card card--nested">
        <div class="card-header"><h3>Refined amenities (detail page)</h3></div>
        <div class="card-body card-body--stack">
          <p class="form-help">Material Symbols icon name, title, and optional description (shown as cards on the room page).</p>
          <div id="amenities-container">
            <?php
            $amList = $room['amenities'] ?? [];
            if (!is_array($amList)) {
                $amList = [];
            }
            foreach ($amList as $a) {
                $icon = 'check_circle';
                $amTitle = '';
                $amDesc = '';
                if (is_array($a)) {
                    $icon = (string)($a['icon'] ?? 'check_circle');
                    $amTitle = (string)($a['title'] ?? $a['name'] ?? '');
                    $amDesc = (string)($a['description'] ?? $a['desc'] ?? '');
                } else {
                    $amTitle = trim((string)$a);
                }
                if ($amTitle === '') {
                    continue;
                }
                ?>
            <div class="card js-amenity-card" style="margin-bottom: 12px; padding: 12px;">
              <div class="form-row">
                <div class="form-group">
                  <label>Icon</label>
                  <input type="text" class="form-control js-amenity-icon" value="<?= sanitize($icon) ?>" placeholder="wifi">
                </div>
                <div class="form-group">
                  <label>Title</label>
                  <input type="text" class="form-control js-amenity-title" value="<?= sanitize($amTitle) ?>" placeholder="High-speed Wi‑Fi">
                </div>
              </div>
              <div class="form-group">
                <label>Description (optional)</label>
                <input type="text" class="form-control js-amenity-desc" value="<?= sanitize($amDesc) ?>" placeholder="Included">
              </div>
              <button type="button" class="btn btn-outline btn-sm" onclick="this.closest('.js-amenity-card').remove()">Remove amenity</button>
            </div>
            <?php } ?>
          </div>
          <button type="button" class="btn btn-outline btn-sm" onclick="lussoAddAmenity()">Add amenity</button>
        </div>
      </div>

      <div class="card card--nested">
        <div class="card-header"><h3>Included privileges</h3></div>
        <div class="card-body card-body--stack">
          <div id="included-container">
            <?php foreach (($room['included_items'] ?? []) as $line) {
                $line = is_string($line) ? $line : '';
                if ($line === '') {
                    continue;
                } ?>
            <div class="form-row lusso-repeat-row" style="align-items: flex-end; gap: 8px;">
              <div class="form-group" style="flex: 1; margin-bottom: 0;">
                <input type="text" class="form-control js-included-input" value="<?= sanitize($line) ?>" placeholder="Airport transfer">
              </div>
              <button type="button" class="btn btn-outline btn-sm" onclick="lussoRemoveRepeatRow(this)">Remove</button>
            </div>
            <?php } ?>
          </div>
          <button type="button" class="btn btn-outline btn-sm" onclick="lussoAddIncluded()">Add included item</button>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label><input type="checkbox" id="is_active" <?= (int)$room['is_active'] ? 'checked' : '' ?>> Active</label>
        </div>
        <div class="form-group">
          <label><input type="checkbox" id="is_featured" <?= (int)$room['is_featured'] ? 'checked' : '' ?>> Featured</label>
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?= $id ? 'Save changes' : 'Create room' ?></button>
        <a href="<?= ADMIN_URL ?>pages/rooms/list.php" class="btn btn-outline">Cancel</a>
      </div>
    </div>
  </div>
</form>

<script>
(function () {
  var siteBase = <?= json_encode($roomPublicUrlBase) ?>;
  var pathTemplate = <?= json_encode(lusso_url('room-details', ['slug' => '__SLUG__'])) ?>;

  function generateSlugClient(text) {
    if (!text) return '';
    return String(text).toLowerCase().trim()
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/-+/g, '-')
      .replace(/^-|-$/g, '');
  }

  function updateSlugPreview() {
    var slugEl = document.getElementById('slug');
    var prev = document.getElementById('slugPreview');
    if (!slugEl || !prev) return;
    var s = slugEl.value.trim();
    if (!s) {
      prev.textContent = 'Slug appears in the room URL. It is generated from the title until you edit it manually.';
      return;
    }
    var path = pathTemplate.replace('__SLUG__', encodeURIComponent(s));
    var full = siteBase ? (siteBase + path) : path;
    prev.textContent = 'Public URL: ' + full;
  }

  var titleInput = document.getElementById('title');
  var slugInput = document.getElementById('slug');
  var initialTitle = titleInput ? titleInput.value : '';
  var initialSlug = slugInput ? slugInput.value : '';
  var slugManual = false;
  if (slugInput && initialSlug !== '' && generateSlugClient(initialTitle) !== initialSlug) {
    slugManual = true;
  }

  if (titleInput && slugInput) {
    titleInput.addEventListener('input', function () {
      if (!slugManual) {
        slugInput.value = generateSlugClient(this.value);
      }
      updateSlugPreview();
    });
    slugInput.addEventListener('input', function () {
      slugManual = true;
      updateSlugPreview();
    });
    slugInput.addEventListener('focus', function () {
      slugManual = true;
    });
  }
  updateSlugPreview();
})();

function lussoRemoveRepeatRow(btn) {
  var row = btn.closest('.lusso-repeat-row');
  if (row) row.remove();
}

function lussoAddFeature() {
  var c = document.getElementById('features-container');
  if (!c) return;
  var wrap = document.createElement('div');
  wrap.className = 'form-row lusso-repeat-row';
  wrap.style.cssText = 'align-items:flex-end;gap:8px;';
  wrap.innerHTML = '<div class="form-group" style="flex:1;margin-bottom:0;"><input type="text" class="form-control js-feature-input" value="" placeholder="e.g. King bed"></div>' +
    '<button type="button" class="btn btn-outline btn-sm" onclick="lussoRemoveRepeatRow(this)">Remove</button>';
  c.appendChild(wrap);
}

function lussoAddAmenity() {
  var c = document.getElementById('amenities-container');
  if (!c) return;
  var card = document.createElement('div');
  card.className = 'card js-amenity-card';
  card.style.cssText = 'margin-bottom:12px;padding:12px;';
  card.innerHTML = '<div class="form-row"><div class="form-group"><label>Icon</label><input type="text" class="form-control js-amenity-icon" value="check_circle" placeholder="wifi"></div>' +
    '<div class="form-group"><label>Title</label><input type="text" class="form-control js-amenity-title" value="" placeholder="Title"></div></div>' +
    '<div class="form-group"><label>Description (optional)</label><input type="text" class="form-control js-amenity-desc" value="" placeholder="Included"></div>' +
    '<button type="button" class="btn btn-outline btn-sm" onclick="this.closest(\'.js-amenity-card\').remove()">Remove amenity</button>';
  c.appendChild(card);
}

function lussoAddIncluded() {
  var c = document.getElementById('included-container');
  if (!c) return;
  var wrap = document.createElement('div');
  wrap.className = 'form-row lusso-repeat-row';
  wrap.style.cssText = 'align-items:flex-end;gap:8px;';
  wrap.innerHTML = '<div class="form-group" style="flex:1;margin-bottom:0;"><input type="text" class="form-control js-included-input" value="" placeholder="Included item"></div>' +
    '<button type="button" class="btn btn-outline btn-sm" onclick="lussoRemoveRepeatRow(this)">Remove</button>';
  c.appendChild(wrap);
}

function lussoCollectFeatures() {
  var out = [];
  document.querySelectorAll('#features-container .js-feature-input').forEach(function (inp) {
    var v = (inp.value || '').trim();
    if (v) out.push(v);
  });
  return out;
}

function lussoCollectAmenities() {
  var out = [];
  document.querySelectorAll('#amenities-container .js-amenity-card').forEach(function (card) {
    var icon = (card.querySelector('.js-amenity-icon') && card.querySelector('.js-amenity-icon').value || '').trim() || 'check_circle';
    var title = (card.querySelector('.js-amenity-title') && card.querySelector('.js-amenity-title').value || '').trim();
    var desc = (card.querySelector('.js-amenity-desc') && card.querySelector('.js-amenity-desc').value || '').trim();
    if (!title) return;
    out.push({ icon: icon, title: title, description: desc });
  });
  return out;
}

function lussoCollectIncluded() {
  var out = [];
  document.querySelectorAll('#included-container .js-included-input').forEach(function (inp) {
    var v = (inp.value || '').trim();
    if (v) out.push(v);
  });
  return out;
}

window.insertSelectedMediaOverride = function() {
  const selected = mediaModalState.allowMultiple ? mediaModalState.selectedMediaMultiple : (mediaModalState.selectedMedia ? [mediaModalState.selectedMedia] : []);
  if (!selected.length) return false;
  const target = mediaModalState.targetInputId;

  function lussoSetMainImage(path) {
    const p = String(path || '').trim();
    const input = document.getElementById('main_image');
    const preview = document.getElementById('main_image_preview');
    const removeBtn = document.getElementById('mainImageRemoveBtn');
    if (input) input.value = p;
    if (preview) {
      if (!p) {
        preview.style.display = 'none';
        preview.innerHTML = '';
      } else {
        preview.style.display = 'block';
        preview.innerHTML =
          '<div class="lusso-media-thumb" style="position:relative; display:inline-block; margin-top:10px;">' +
            '<img src="<?= SITE_URL ?>' + p.replace(/^\/+/, '') + '" style="max-width:300px;max-height:200px;border-radius:6px;display:block;object-fit:cover;">' +
          '</div>';
      }
    }
    if (removeBtn) removeBtn.style.display = p ? 'inline-flex' : 'none';
  }

  function lussoGetGalleryPaths() {
    try {
      const raw = document.getElementById('gallery_images')?.value || '[]';
      const v = JSON.parse(raw);
      if (!Array.isArray(v)) return [];
      return v.map(s => String(s || '').trim()).filter(Boolean);
    } catch (e) {
      return [];
    }
  }

  function lussoSetGalleryPaths(paths) {
    const clean = (paths || []).map(s => String(s || '').trim()).filter(Boolean);
    const input = document.getElementById('gallery_images');
    const preview = document.getElementById('gallery_images_preview');
    const clearBtn = document.getElementById('galleryClearBtn');
    if (input) input.value = JSON.stringify(clean);
    if (preview) {
      if (!clean.length) {
        preview.style.display = 'none';
        preview.innerHTML = '';
      } else {
        preview.style.display = 'block';
        preview.innerHTML = clean.map((p, i) =>
          '<div class="lusso-media-thumb" data-i="' + i + '" style="position:relative; display:inline-block; margin:6px;">' +
            '<img src="<?= SITE_URL ?>' + p.replace(/^\/+/, '') + '" style="width:120px;height:120px;display:block;border-radius:8px;object-fit:cover;border:1px solid rgba(0,0,0,0.08);">' +
            '<button type="button" class="btn btn-outline btn-sm js-room-gallery-remove" data-i="' + i + '" style="position:absolute; top:6px; right:6px; padding:4px 6px; line-height:1;">×</button>' +
          '</div>'
        ).join('');
      }
    }
    if (clearBtn) clearBtn.style.display = clean.length ? 'inline-flex' : 'none';
  }

  if (target === 'main_image') {
    const first = selected[0];
    lussoSetMainImage(first.path);
    closeMediaModal();
    return true;
  }

  if (target === 'gallery_images') {
    const paths = selected.map(s => s.path);
    lussoSetGalleryPaths(paths);
    closeMediaModal();
    return true;
  }
  return false;
};

// Init remove/clear buttons on load
document.addEventListener('DOMContentLoaded', function () {
  // Main image remove
  const mainRemoveBtn = document.getElementById('mainImageRemoveBtn');
  if (mainRemoveBtn) {
    const hasMain = !!(document.getElementById('main_image')?.value || '').trim();
    mainRemoveBtn.style.display = hasMain ? 'inline-flex' : 'none';
    mainRemoveBtn.addEventListener('click', function () {
      document.getElementById('main_image').value = '';
      const p = document.getElementById('main_image_preview');
      if (p) { p.style.display = 'none'; p.innerHTML = ''; }
      mainRemoveBtn.style.display = 'none';
    });
  }

  // Gallery clear all
  const clearBtn = document.getElementById('galleryClearBtn');
  if (clearBtn) {
    const cur = (function () { try { return JSON.parse(document.getElementById('gallery_images')?.value || '[]'); } catch (e) { return []; } })();
    clearBtn.style.display = (Array.isArray(cur) && cur.length) ? 'inline-flex' : 'none';
    clearBtn.addEventListener('click', function () {
      document.getElementById('gallery_images').value = '[]';
      const p = document.getElementById('gallery_images_preview');
      if (p) { p.style.display = 'none'; p.innerHTML = ''; }
      clearBtn.style.display = 'none';
    });
  }

  // Gallery remove one (event delegation)
  const galPrev = document.getElementById('gallery_images_preview');
  if (galPrev) {
    galPrev.addEventListener('click', function (e) {
      const btn = e.target.closest('.js-room-gallery-remove');
      if (!btn) return;
      e.preventDefault();
      const idx = parseInt(btn.getAttribute('data-i') || '0', 10);
      let cur = [];
      try { cur = JSON.parse(document.getElementById('gallery_images')?.value || '[]'); } catch (err) { cur = []; }
      if (!Array.isArray(cur)) cur = [];
      cur.splice(idx, 1);
      document.getElementById('gallery_images').value = JSON.stringify(cur);
      // Re-render by simulating selection override helper
      const preview = document.getElementById('gallery_images_preview');
      const clearBtn = document.getElementById('galleryClearBtn');
      if (!cur.length) {
        if (preview) { preview.style.display = 'none'; preview.innerHTML = ''; }
        if (clearBtn) clearBtn.style.display = 'none';
        return;
      }
      if (preview) {
        preview.style.display = 'block';
        preview.innerHTML = cur.map((p, i) =>
          '<div class="lusso-media-thumb" data-i="' + i + '" style="position:relative; display:inline-block; margin:6px;">' +
            '<img src="<?= SITE_URL ?>' + String(p || '').replace(/^\/+/, '') + '" style="width:120px;height:120px;display:block;border-radius:8px;object-fit:cover;border:1px solid rgba(0,0,0,0.08);">' +
            '<button type="button" class="btn btn-outline btn-sm js-room-gallery-remove" data-i="' + i + '" style="position:absolute; top:6px; right:6px; padding:4px 6px; line-height:1;">×</button>' +
          '</div>'
        ).join('');
      }
      if (clearBtn) clearBtn.style.display = 'inline-flex';
    });
  }
});

document.getElementById('roomForm').addEventListener('submit', function(e){
  e.preventDefault();
  const submitBtn = this.querySelector('button[type=\"submit\"]');
  if (typeof setSaveButtonSavingState === 'function') setSaveButtonSavingState(submitBtn, true);
  const csrfToken = document.querySelector('meta[name=\"csrf-token\"]')?.getAttribute('content') || '';
  const payload = {
    title: document.getElementById('title').value,
    slug: document.getElementById('slug').value,
    room_type: document.getElementById('room_type').value,
    price: document.getElementById('price').value,
    display_order: document.getElementById('display_order').value,
    size: document.getElementById('size').value,
    max_guests: document.getElementById('max_guests').value,
    book_url: document.getElementById('book_url').value,
    short_description: document.getElementById('short_description').value,
    description: document.getElementById('description').value,
    main_image: document.getElementById('main_image').value,
    gallery_images: (() => { try { return JSON.parse(document.getElementById('gallery_images').value || '[]'); } catch (err) { return []; } })(),
    features: lussoCollectFeatures(),
    amenities: lussoCollectAmenities(),
    included_items: lussoCollectIncluded(),
    good_to_know: {},
    is_active: document.getElementById('is_active').checked ? 1 : 0,
    is_featured: document.getElementById('is_featured').checked ? 1 : 0
  };

  const isEdit = <?= $id ? 'true' : 'false' ?>;
  const url = isEdit ? ('<?= ADMIN_URL ?>api/rooms.php?id=<?= (int)$id ?>') : ('<?= ADMIN_URL ?>api/rooms.php');
  const method = isEdit ? 'PUT' : 'POST';

  fetch(url, {
    method,
    headers: {'Content-Type':'application/json','X-CSRF-Token': csrfToken},
    body: JSON.stringify(payload)
  }).then(r => r.json()).then(data => {
    if (data.success) {
      if (data.display_order !== undefined && data.display_order !== null) {
        const orderEl = document.getElementById('display_order');
        if (orderEl) orderEl.value = String(data.display_order);
      }
      showToast(data.message || 'Saved', 'success');
      if (!isEdit && data.room_id) {
        setTimeout(function () {
          window.location.href = '<?= ADMIN_URL ?>pages/rooms/edit.php?id=' + encodeURIComponent(data.room_id);
        }, 400);
      }
    } else {
      showToast(data.message || 'Save failed', 'error');
    }
  }).catch(() => showToast('Save failed', 'error'))
  .finally(function () {
    if (typeof setSaveButtonSavingState === 'function') setSaveButtonSavingState(submitBtn, false);
  });
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

