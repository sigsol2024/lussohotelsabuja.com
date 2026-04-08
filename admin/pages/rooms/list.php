<?php
$pageTitle = 'Rooms';
require_once __DIR__ . '/../../includes/config.php';
require_once BASE_PATH . '/includes/url.php';
require_once __DIR__ . '/../../includes/auth.php';
requireLogin();
require_once __DIR__ . '/../../includes/header.php';

try {
    $rooms = $pdo->query("SELECT id,title,slug,price,is_active,is_featured,display_order,created_at FROM rooms ORDER BY display_order ASC, created_at DESC")->fetchAll();
} catch (PDOException $e) {
    $rooms = [];
}
?>

<div class="card">
  <div class="card-header card-header--split">
    <h2>Rooms</h2>
    <a class="btn btn-primary" href="<?= ADMIN_URL ?>pages/rooms/add.php"><i class="fas fa-plus"></i> Add room</a>
  </div>

  <div class="table-wrapper">
    <table class="table">
      <thead>
        <tr>
          <th>Title</th>
          <th>Slug</th>
          <th>Price</th>
          <th>Status</th>
          <th>Featured</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php if (empty($rooms)): ?>
        <tr><td colspan="6" style="color:var(--text-muted);">No rooms yet.</td></tr>
      <?php else: ?>
        <?php foreach ($rooms as $r): ?>
          <tr>
            <td><?= sanitize($r['title']) ?></td>
            <td><?= sanitize($r['slug']) ?></td>
            <td><?= sanitize($r['price']) ?></td>
            <td><?= (int)$r['is_active'] ? 'Live' : 'Draft' ?></td>
            <td><?= (int)$r['is_featured'] ? 'Yes' : 'No' ?></td>
            <td>
              <a class="btn btn-sm btn-outline" href="<?= ADMIN_URL ?>pages/rooms/edit.php?id=<?= (int)$r['id'] ?>">Edit</a>
              <button type="button" class="btn btn-sm btn-outline js-duplicate-room" data-id="<?= (int)$r['id'] ?>" data-title="<?= htmlspecialchars($r['title'], ENT_QUOTES, 'UTF-8') ?>">Duplicate</button>
              <?php if ((int)$r['is_active']): ?>
              <a class="btn btn-sm btn-outline" target="_blank" href="<?= htmlspecialchars(lusso_site_href(lusso_url('room-details', ['slug' => (string)$r['slug']])), ENT_QUOTES, 'UTF-8') ?>">View</a>
              <?php endif; ?>
              <button type="button" class="btn btn-sm btn-outline danger-delete-room" data-id="<?= (int)$r['id'] ?>" data-title="<?= htmlspecialchars($r['title'], ENT_QUOTES, 'UTF-8') ?>">Delete</button>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
document.querySelectorAll('.js-duplicate-room').forEach(function (btn) {
  btn.addEventListener('click', function () {
    var id = this.getAttribute('data-id');
    var title = this.getAttribute('data-title') || 'this room';
    if (!id || !confirm('Duplicate "' + title + '"? A draft copy will be created (title and slug get -copy). It stays hidden until you set Active.')) return;
    var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    fetch('<?= ADMIN_URL ?>api/rooms.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrfToken },
      body: JSON.stringify({ duplicate_from_id: parseInt(id, 10) })
    }).then(function (r) { return r.json(); }).then(function (data) {
      if (data.success && data.room_id) {
        showToast(data.message || 'Duplicated', 'success');
        window.location.href = '<?= ADMIN_URL ?>pages/rooms/edit.php?id=' + encodeURIComponent(data.room_id);
      } else {
        showToast(data.message || 'Duplicate failed', 'error');
      }
    }).catch(function () { showToast('Duplicate failed', 'error'); });
  });
});
document.querySelectorAll('.danger-delete-room').forEach(function (btn) {
  btn.addEventListener('click', function () {
    var id = this.getAttribute('data-id');
    var title = this.getAttribute('data-title') || 'this room';
    if (!id || !confirm('Delete ' + title + '? This cannot be undone.')) return;
    var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    fetch('<?= ADMIN_URL ?>api/rooms.php?id=' + encodeURIComponent(id), {
      method: 'DELETE',
      headers: { 'X-CSRF-Token': csrfToken }
    }).then(function (r) { return r.json(); }).then(function (data) {
      if (data.success) {
        showToast('Room deleted', 'success');
        window.location.reload();
      } else {
        showToast(data.message || 'Delete failed', 'error');
      }
    }).catch(function () { showToast('Delete failed', 'error'); });
  });
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

