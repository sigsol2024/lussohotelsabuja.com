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

$gtk = is_array($room['good_to_know'] ?? null) ? $room['good_to_know'] : [];
$gtkCheckIn = (string)($gtk['check_in'] ?? '');
$gtkCheckOut = (string)($gtk['check_out'] ?? '');
$gtkFloorplanUrl = (string)($gtk['floorplan_url'] ?? '');
$gtkFloorplanLabel = (string)($gtk['floorplan_label'] ?? '');
$gtkTourUrl = (string)($gtk['tour_url'] ?? '');

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
        <div class="form-group">
          <label for="urgency_message">Amenities intro copy (optional)</label>
          <input id="urgency_message" name="urgency_message" type="text" value="<?= sanitize($room['urgency_message'] ?? '') ?>" placeholder="Shown beside Refined Amenities heading">
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

      <div class="form-row">
        <div class="form-group">
          <label for="location">Location text</label>
          <input id="location" name="location" type="text" value="<?= sanitize($room['location']) ?>">
        </div>
        <div class="form-group">
          <label for="book_url">Book URL</label>
          <input id="book_url" name="book_url" type="text" value="<?= sanitize($room['book_url']) ?>">
        </div>
      </div>

      <div class="form-group">
        <label for="short_description">Concept / pull quote (hero editorial line)</label>
        <textarea id="short_description" name="short_description" rows="2"><?= sanitize($room['short_description']) ?></textarea>
      </div>
      <div class="form-group">
        <label for="description">Description (first block = “The Space”, second block after blank line = “The Experience”)</label>
        <textarea id="description" name="description" rows="8"><?= sanitize($room['description']) ?></textarea>
      </div>

      <div class="card card--nested">
        <div class="card-header"><h3>Guest info &amp; links</h3></div>
        <div class="card-body card-body--stack">
          <div class="form-row">
            <div class="form-group">
              <label for="gtk_check_in">Check-in hint (booking bar)</label>
              <input id="gtk_check_in" type="text" value="<?= sanitize($gtkCheckIn) ?>" placeholder="Oct 24, 2023">
            </div>
            <div class="form-group">
              <label for="gtk_check_out">Check-out hint</label>
              <input id="gtk_check_out" type="text" value="<?= sanitize($gtkCheckOut) ?>" placeholder="Oct 28, 2023">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="gtk_floorplan_url">Floorplan URL (optional)</label>
              <input id="gtk_floorplan_url" type="text" value="<?= sanitize($gtkFloorplanUrl) ?>">
            </div>
            <div class="form-group">
              <label for="gtk_floorplan_label">Floorplan label on image</label>
              <input id="gtk_floorplan_label" type="text" value="<?= sanitize($gtkFloorplanLabel) ?>" placeholder="Presidential Floorplan">
            </div>
          </div>
          <div class="form-group">
            <label for="gtk_tour_url">Virtual tour / video URL (optional)</label>
            <input id="gtk_tour_url" type="text" value="<?= sanitize($gtkTourUrl) ?>" placeholder="https://…">
          </div>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Main image</label>
          <button type="button" class="btn btn-outline" onclick="openMediaModal('main_image','main_image_preview')">Select Image</button>
          <input type="hidden" id="main_image" name="main_image" value="<?= sanitize($room['main_image']) ?>">
          <div id="main_image_preview" class="image-preview" style="<?= !empty($room['main_image']) ? 'display:block;' : 'display:none;' ?>">
            <?php if (!empty($room['main_image'])): ?><img src="<?= SITE_URL . ltrim($room['main_image'], '/') ?>" style="max-width:300px;max-height:200px;"><?php endif; ?>
          </div>
        </div>
        <div class="form-group">
          <label>Gallery images</label>
          <button type="button" class="btn btn-outline" onclick="openMediaModal('gallery_images','gallery_images_preview', true)">Select Images</button>
          <input type="hidden" id="gallery_images" name="gallery_images" value="<?= htmlspecialchars(json_encode($room['gallery_images']), ENT_QUOTES, 'UTF-8') ?>">
          <div id="gallery_images_preview" class="image-preview" style="<?= !empty($room['gallery_images']) ? 'display:block;' : 'display:none;' ?>">
            <?php foreach (($room['gallery_images'] ?? []) as $img): ?>
              <img src="<?= SITE_URL . ltrim($img, '/') ?>" style="max-width:120px;max-height:120px;display:inline-block;margin:5px;object-fit:cover;">
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <div class="card card--nested" style="margin-top: 1rem;">
        <div class="card-header"><h3>Listing highlights</h3></div>
        <div class="card-body card-body--stack">
          <p class="form-help">Short labels used on the rooms listing (size, bed, view, etc.).</p>
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

  if (target === 'main_image') {
    const first = selected[0];
    document.getElementById('main_image').value = first.path;
    const preview = document.getElementById('main_image_preview');
    preview.style.display = 'block';
    preview.innerHTML = '<img src=\"<?= SITE_URL ?>' + first.path.replace(/^\\//,'') + '\" style=\"max-width:300px;max-height:200px;\">';
    closeMediaModal();
    return true;
  }

  if (target === 'gallery_images') {
    const paths = selected.map(s => s.path);
    document.getElementById('gallery_images').value = JSON.stringify(paths);
    const preview = document.getElementById('gallery_images_preview');
    preview.style.display = 'block';
    preview.innerHTML = paths.map(p => '<img src=\"<?= SITE_URL ?>' + p.replace(/^\\//,'') + '\" style=\"max-width:120px;max-height:120px;display:inline-block;margin:5px;object-fit:cover;\">').join('');
    closeMediaModal();
    return true;
  }
  return false;
};

document.getElementById('roomForm').addEventListener('submit', function(e){
  e.preventDefault();
  const csrfToken = document.querySelector('meta[name=\"csrf-token\"]')?.getAttribute('content') || '';
  const payload = {
    title: document.getElementById('title').value,
    slug: document.getElementById('slug').value,
    room_type: document.getElementById('room_type').value,
    urgency_message: document.getElementById('urgency_message').value,
    price: document.getElementById('price').value,
    display_order: document.getElementById('display_order').value,
    size: document.getElementById('size').value,
    max_guests: document.getElementById('max_guests').value,
    location: document.getElementById('location').value,
    book_url: document.getElementById('book_url').value,
    short_description: document.getElementById('short_description').value,
    description: document.getElementById('description').value,
    main_image: document.getElementById('main_image').value,
    gallery_images: (() => { try { return JSON.parse(document.getElementById('gallery_images').value || '[]'); } catch (err) { return []; } })(),
    features: lussoCollectFeatures(),
    amenities: lussoCollectAmenities(),
    included_items: lussoCollectIncluded(),
    good_to_know: {
      check_in: document.getElementById('gtk_check_in').value,
      check_out: document.getElementById('gtk_check_out').value,
      floorplan_url: document.getElementById('gtk_floorplan_url').value,
      floorplan_label: document.getElementById('gtk_floorplan_label').value,
      tour_url: document.getElementById('gtk_tour_url').value
    },
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
      showToast('Saved', 'success');
      if (!isEdit && data.room_id) {
        setTimeout(function () {
          window.location.href = '<?= ADMIN_URL ?>pages/rooms/edit.php?id=' + encodeURIComponent(data.room_id);
        }, 400);
      }
    } else {
      showToast(data.message || 'Save failed', 'error');
    }
  }).catch(() => showToast('Save failed', 'error'));
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

