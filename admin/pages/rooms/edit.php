<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
requireLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
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
          <input id="title" name="title" type="text" value="<?= sanitize($room['title']) ?>">
        </div>
        <div class="form-group">
          <label for="slug">Slug</label>
          <input id="slug" name="slug" type="text" value="<?= sanitize($room['slug']) ?>">
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

      <div class="form-group">
        <label for="included_items">Included privileges (one per line)</label>
        <textarea id="included_items" name="included_items" rows="5"><?php foreach (($room['included_items'] ?? []) as $line) { echo sanitize(is_string($line) ? $line : '') . "\n"; } ?></textarea>
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

      <div class="form-row">
        <div class="form-group">
          <label for="features">Features (one per line)</label>
          <textarea id="features" name="features" rows="4"><?php foreach (($room['features'] ?? []) as $f) { echo sanitize($f) . "\n"; } ?></textarea>
        </div>
        <div class="form-group">
          <label for="amenities">Amenities — one per line, or JSON objects with icon, title, description (Material icon names)</label>
          <textarea id="amenities" name="amenities" rows="6"><?php
            foreach (($room['amenities'] ?? []) as $a) {
              if (is_array($a)) {
                echo sanitize(json_encode($a)) . "\n";
              } else {
                echo sanitize((string)$a) . "\n";
              }
            }
          ?></textarea>
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

function parseAmenitiesLines(text) {
  const lines = text.split(/\r?\n/).map(s => s.trim()).filter(Boolean);
  return lines.map(line => {
    if (line.startsWith('{')) {
      try { return JSON.parse(line); } catch (e) { return line; }
    }
    return line;
  });
}

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
    features: document.getElementById('features').value.split(/\r?\n/).map(s => s.trim()).filter(Boolean),
    amenities: parseAmenitiesLines(document.getElementById('amenities').value),
    included_items: document.getElementById('included_items').value.split(/\r?\n/).map(s => s.trim()).filter(Boolean),
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
      if (!isEdit) setTimeout(() => window.location.href = '<?= ADMIN_URL ?>pages/rooms/list.php', 600);
    } else {
      showToast(data.message || 'Save failed', 'error');
    }
  }).catch(() => showToast('Save failed', 'error'));
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

