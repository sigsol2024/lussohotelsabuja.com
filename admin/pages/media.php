<?php
$pageTitle = 'Media Library';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

$page = max(1, (int)($_GET['p'] ?? 1));
$perPage = 24;
$offset = ($page - 1) * $perPage;
$search = trim((string)($_GET['q'] ?? ''));
$where = [];
$params = [];
if ($search !== '') {
    $term = '%' . $search . '%';
    $where[] = '(m.original_name LIKE ? OR m.filename LIKE ? OR m.file_path LIKE ?)';
    $params[] = $term;
    $params[] = $term;
    $params[] = $term;
}
$whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$total = 0;
$items = [];
try {
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM media m $whereClause");
    $countStmt->execute($params);
    $total = (int)$countStmt->fetchColumn();

    $listStmt = $pdo->prepare("SELECT m.*, u.username AS uploaded_by_name
        FROM media m
        LEFT JOIN admin_users u ON m.uploaded_by = u.id
        $whereClause
        ORDER BY m.uploaded_at DESC
        LIMIT ? OFFSET ?");
    $bi = 1;
    foreach ($params as $p) {
        $listStmt->bindValue($bi++, $p);
    }
    $listStmt->bindValue($bi++, $perPage, PDO::PARAM_INT);
    $listStmt->bindValue($bi++, $offset, PDO::PARAM_INT);
    $listStmt->execute();
    $items = $listStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('Media page: ' . $e->getMessage());
}

$pages = $total > 0 ? (int)ceil($total / $perPage) : 1;
if ($page > $pages && $pages > 0) {
    $page = $pages;
}

function lusso_media_public_url($filePath) {
    return rtrim(SITE_URL, '/') . '/' . ltrim((string)$filePath, '/');
}

?>

<div class="card media-library-card">
  <div class="card-header card-header--split media-library-card__head">
    <div>
      <h2 class="media-library-card__title">All media</h2>
      <p class="text-muted" style="margin:4px 0 0;font-size:13px;">Upload images, copy URLs into editors, and delete what you don’t need. <?= (int)$total ?> file<?= $total === 1 ? '' : 's' ?><?= $search !== '' ? ' match your search.' : '.' ?></p>
    </div>
    <button class="btn btn-primary" type="button" onclick="openMediaModal('dummy','dummyPreview')">
      <i class="fas fa-cloud-upload-alt"></i> Upload
    </button>
  </div>

  <div class="media-library-toolbar">
    <form method="get" action="" class="media-library-toolbar__search">
      <input type="hidden" name="p" value="1">
      <label class="sr-only" for="media_q">Search</label>
      <div class="media-library-search-wrap">
        <i class="fas fa-search" aria-hidden="true"></i>
        <input type="search" id="media_q" name="q" value="<?= sanitize($search) ?>" placeholder="Search by file name…" autocomplete="off">
      </div>
      <button type="submit" class="btn btn-primary">Search</button>
      <?php if ($search !== ''): ?>
        <a href="media.php" class="btn btn-outline">Clear</a>
      <?php endif; ?>
    </form>

    <div class="media-library-toolbar__right">
      <span class="media-library-toolbar__label">View</span>
      <div class="media-view-toggle" role="group" aria-label="Layout">
        <button type="button" class="media-view-toggle__btn is-active" id="mediaViewGridBtn" data-view="grid" title="Grid view">
          <i class="fas fa-th-large"></i><span>Grid</span>
        </button>
        <button type="button" class="media-view-toggle__btn" id="mediaViewListBtn" data-view="list" title="List view">
          <i class="fas fa-list"></i><span>List</span>
        </button>
      </div>
    </div>
  </div>

  <div id="mediaBulkBar" class="media-bulk-bar" hidden>
    <label class="media-bulk-bar__check">
      <input type="checkbox" id="mediaSelectAll" title="Select all on this page">
      <span>Select all on page</span>
    </label>
    <span id="mediaBulkCount" class="media-bulk-bar__count">0 selected</span>
    <button type="button" class="btn btn-sm btn-danger" id="mediaBulkDeleteBtn" disabled>
      <i class="fas fa-trash-alt"></i> Delete selected
    </button>
    <button type="button" class="btn btn-sm btn-outline" id="mediaBulkClearBtn">Clear selection</button>
  </div>

  <input type="hidden" id="dummy" value="">
  <span id="dummyPreview" style="display:none;"></span>

  <?php if (empty($items)): ?>
    <div class="media-empty">
      <i class="fas fa-images" aria-hidden="true"></i>
      <?php if ($total === 0 && $search === ''): ?>
        <p><strong>No files yet.</strong> Upload images to see them here.</p>
        <button type="button" class="btn btn-primary" onclick="openMediaModal('dummy','dummyPreview')">Upload images</button>
      <?php else: ?>
        <p>No matches for your search.</p>
        <a href="media.php" class="btn btn-outline">Clear search</a>
      <?php endif; ?>
    </div>
  <?php else: ?>

    <div id="mediaViewGrid" class="media-grid">
      <?php foreach ($items as $row):
        $url = lusso_media_public_url($row['file_path'] ?? '');
        $id = (int)($row['id'] ?? 0);
        ?>
        <article class="media-card" data-id="<?= $id ?>">
          <label class="media-card__select">
            <input type="checkbox" class="js-media-pick" name="media_pick[]" value="<?= $id ?>" data-url="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>">
            <span class="sr-only">Select</span>
          </label>
          <a href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="media-card__thumb">
            <img src="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>" alt="" loading="lazy" width="280" height="280">
          </a>
          <div class="media-card__body">
            <p class="media-card__name" title="<?= sanitize($row['original_name'] ?? '') ?>"><?= sanitize($row['original_name'] ?? '') ?></p>
            <p class="media-card__meta"><?= sanitize(formatFileSize((int)($row['file_size'] ?? 0))) ?></p>
            <div class="media-card__actions">
              <button type="button" class="btn btn-sm btn-outline js-media-copy" data-url="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>" title="Copy URL">
                <i class="fas fa-link"></i> Copy URL
              </button>
              <button type="button" class="btn btn-sm btn-danger js-media-delete" data-id="<?= $id ?>" title="Delete">
                <i class="fas fa-trash-alt"></i>
              </button>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>

    <div id="mediaViewList" class="media-list-wrap" hidden>
      <div class="table-wrapper">
        <table class="table media-table">
          <thead>
            <tr>
              <th class="media-table__check"><span class="sr-only">Select</span></th>
              <th style="width:88px;">Preview</th>
              <th>Name</th>
              <th>Path</th>
              <th>Size</th>
              <th>Uploaded</th>
              <th>By</th>
              <th style="width:200px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($items as $row):
              $url = lusso_media_public_url($row['file_path'] ?? '');
              $id = (int)($row['id'] ?? 0);
              ?>
              <tr data-id="<?= $id ?>">
                <td class="media-table__check">
                  <input type="checkbox" class="js-media-pick" name="media_pick[]" value="<?= $id ?>" data-url="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>">
                </td>
                <td>
                  <a href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="media-table__thumb">
                    <img src="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>" alt="" loading="lazy" width="64" height="64">
                  </a>
                </td>
                <td><?= sanitize($row['original_name'] ?? '') ?></td>
                <td><code class="media-table__path"><?= sanitize($row['file_path'] ?? '') ?></code></td>
                <td><?= sanitize(formatFileSize((int)($row['file_size'] ?? 0))) ?></td>
                <td class="text-muted" style="white-space:nowrap;font-size:13px;"><?= sanitize($row['uploaded_at'] ?? '') ?></td>
                <td class="text-muted" style="font-size:13px;"><?= sanitize($row['uploaded_by_name'] ?? '—') ?></td>
                <td>
                  <div style="display:flex;gap:6px;flex-wrap:wrap;">
                    <button type="button" class="btn btn-sm btn-outline js-media-copy" data-url="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>">Copy URL</button>
                    <button type="button" class="btn btn-sm btn-danger js-media-delete" data-id="<?= $id ?>">Delete</button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <?php if ($pages > 1):
      $baseQs = [];
      if ($search !== '') {
          $baseQs['q'] = $search;
      }
      ?>
      <nav class="media-pagination" aria-label="Pagination">
        <?php if ($page > 1): ?>
          <?php $baseQs['p'] = $page - 1; ?>
          <a class="btn btn-sm btn-outline" href="<?= htmlspecialchars('media.php?' . http_build_query($baseQs), ENT_QUOTES, 'UTF-8') ?>">Previous</a>
        <?php endif; ?>
        <span class="media-pagination__status">Page <?= (int)$page ?> of <?= (int)$pages ?></span>
        <?php if ($page < $pages): ?>
          <?php $baseQs['p'] = $page + 1; ?>
          <a class="btn btn-sm btn-outline" href="<?= htmlspecialchars('media.php?' . http_build_query($baseQs), ENT_QUOTES, 'UTF-8') ?>">Next</a>
        <?php endif; ?>
      </nav>
    <?php endif; ?>

    <p class="text-muted" style="margin:16px 0 0;font-size:13px;">Showing <?= count($items) ?> of <?= (int)$total ?> on this page.</p>
  <?php endif; ?>
</div>

<script>
(function () {
  var csrf = document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').getAttribute('content') || '';
  var apiBase = <?= json_encode(rtrim(ADMIN_URL, '/') . '/', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;

  function copyText(text) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
      return navigator.clipboard.writeText(text).then(function () {
        if (typeof showToast === 'function') showToast('URL copied to clipboard', 'success');
      }).catch(function () {
        fallbackCopy(text);
      });
    }
    fallbackCopy(text);
  }

  function fallbackCopy(text) {
    var ta = document.createElement('textarea');
    ta.value = text;
    ta.style.position = 'fixed';
    ta.style.left = '-9999px';
    document.body.appendChild(ta);
    ta.select();
    try {
      document.execCommand('copy');
      if (typeof showToast === 'function') showToast('URL copied to clipboard', 'success');
    } catch (e) {
      if (typeof showToast === 'function') showToast('Could not copy', 'error');
    }
    document.body.removeChild(ta);
  }

  function getPicks() {
    var seen = {};
    var arr = [];
    document.querySelectorAll('.js-media-pick:checked').forEach(function (cb) {
      var v = cb.value;
      if (!seen[v]) {
        seen[v] = true;
        arr.push(cb);
      }
    });
    return arr;
  }

  function updateBulkBar() {
    var picks = getPicks();
    var bar = document.getElementById('mediaBulkBar');
    var countEl = document.getElementById('mediaBulkCount');
    var delBtn = document.getElementById('mediaBulkDeleteBtn');
    var n = picks.length;
    if (!bar || !countEl || !delBtn) return;
    if (n > 0) {
      bar.hidden = false;
      countEl.textContent = n + ' selected';
      delBtn.disabled = false;
    } else {
      bar.hidden = true;
      delBtn.disabled = true;
    }
    var all = document.querySelectorAll('.js-media-pick');
    var selAll = document.getElementById('mediaSelectAll');
    if (selAll && all.length) {
      selAll.checked = n > 0 && n === all.length;
      selAll.indeterminate = n > 0 && n < all.length;
    }
  }

  function syncPickDuplicates(changed) {
    var v = changed.value;
    var on = changed.checked;
    document.querySelectorAll('.js-media-pick[value="' + v + '"]').forEach(function (cb) {
      if (cb !== changed) cb.checked = on;
    });
  }

  function setView(mode) {
    var grid = document.getElementById('mediaViewGrid');
    var list = document.getElementById('mediaViewList');
    var btnG = document.getElementById('mediaViewGridBtn');
    var btnL = document.getElementById('mediaViewListBtn');
    if (!grid || !list) return;
    if (mode === 'list') {
      grid.setAttribute('hidden', '');
      list.removeAttribute('hidden');
      if (btnG) { btnG.classList.remove('is-active'); }
      if (btnL) { btnL.classList.add('is-active'); }
    } else {
      list.setAttribute('hidden', '');
      grid.removeAttribute('hidden');
      if (btnG) { btnG.classList.add('is-active'); }
      if (btnL) { btnL.classList.remove('is-active'); }
    }
    try { localStorage.setItem('lusso_media_view', mode); } catch (e) {}
    updateBulkBar();
  }

  try {
    var saved = localStorage.getItem('lusso_media_view');
    if (saved === 'list') setView('list');
  } catch (e) {}

  document.addEventListener('change', function (e) {
    if (e.target && e.target.classList && e.target.classList.contains('js-media-pick')) {
      syncPickDuplicates(e.target);
      updateBulkBar();
    }
  });

  document.getElementById('mediaSelectAll') && document.getElementById('mediaSelectAll').addEventListener('change', function () {
    var on = this.checked;
    document.querySelectorAll('#mediaViewGrid:not([hidden]) .js-media-pick, #mediaViewList:not([hidden]) .js-media-pick').forEach(function (cb) {
      cb.checked = on;
    });
    document.querySelectorAll('.js-media-pick').forEach(function (cb) {
      cb.checked = on;
    });
    updateBulkBar();
  });

  document.getElementById('mediaBulkClearBtn') && document.getElementById('mediaBulkClearBtn').addEventListener('click', function () {
    document.querySelectorAll('.js-media-pick').forEach(function (cb) { cb.checked = false; });
    var selAll = document.getElementById('mediaSelectAll');
    if (selAll) selAll.checked = false;
    updateBulkBar();
  });

  document.addEventListener('click', function (e) {
    var copyBtn = e.target.closest && e.target.closest('.js-media-copy');
    if (copyBtn) {
      var url = copyBtn.getAttribute('data-url');
      if (url) copyText(url);
      return;
    }
    var delBtn = e.target.closest && e.target.closest('.js-media-delete');
    if (delBtn) {
      var id = delBtn.getAttribute('data-id');
      if (!id || !confirm('Delete this file from the library? This cannot be undone.')) return;
      fetch(apiBase + 'api/media.php?id=' + encodeURIComponent(id) + '&csrf_token=' + encodeURIComponent(csrf), {
        method: 'DELETE',
        credentials: 'include'
      }).then(function (r) { return r.json(); }).then(function (data) {
        if (data.success) {
          if (typeof showToast === 'function') showToast(data.message || 'Deleted', 'success');
          window.location.reload();
        } else {
          if (typeof showToast === 'function') showToast(data.message || 'Delete failed', 'error');
        }
      }).catch(function () {
        if (typeof showToast === 'function') showToast('Delete failed', 'error');
      });
    }
  });

  document.getElementById('mediaBulkDeleteBtn') && document.getElementById('mediaBulkDeleteBtn').addEventListener('click', function () {
    var picks = getPicks();
    if (!picks.length) return;
    if (!confirm('Delete ' + picks.length + ' file(s)? This cannot be undone.')) return;
    var ids = Array.from(picks).map(function (cb) { return parseInt(cb.value, 10); }).filter(function (x) { return x > 0; });
    var body = new FormData();
    body.append('csrf_token', csrf);
    body.append('action', 'bulk_delete');
    body.append('ids', JSON.stringify(ids));
    fetch(apiBase + 'api/media.php', { method: 'POST', body: body, credentials: 'include' })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (data.success) {
          if (typeof showToast === 'function') showToast(data.message || 'Deleted', 'success');
          window.location.reload();
        } else {
          if (typeof showToast === 'function') showToast(data.message || 'Delete failed', 'error');
        }
      })
      .catch(function () {
        if (typeof showToast === 'function') showToast('Delete failed', 'error');
      });
  });

  var gridBtn = document.getElementById('mediaViewGridBtn');
  var listBtn = document.getElementById('mediaViewListBtn');
  if (gridBtn) gridBtn.addEventListener('click', function () { setView('grid'); });
  if (listBtn) listBtn.addEventListener('click', function () { setView('list'); });
})();
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
