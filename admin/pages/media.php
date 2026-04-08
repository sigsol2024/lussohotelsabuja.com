<?php
$pageTitle = 'Media Library';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
require_once __DIR__ . '/../includes/functions.php';

$page = max(1, (int)($_GET['p'] ?? 1));
$perPage = 24;
$offset = ($page - 1) * $perPage;
$search = trim((string)($_GET['q'] ?? ''));
$where = [];
$params = [];
if ($search !== '') {
    $term = '%' . $search . '%';
    $where[] = '(original_name LIKE ? OR filename LIKE ? OR file_path LIKE ?)';
    $params[] = $term;
    $params[] = $term;
    $params[] = $term;
}
$whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$total = 0;
$items = [];
try {
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM media $whereClause");
    $countStmt->execute($params);
    $total = (int)$countStmt->fetchColumn();

    $listStmt = $pdo->prepare("SELECT * FROM media $whereClause ORDER BY uploaded_at DESC LIMIT ? OFFSET ?");
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

function formatBytesMedia($b) {
    $b = (int)$b;
    if ($b >= 1048576) {
        return number_format($b / 1048576, 1) . ' MB';
    }
    if ($b >= 1024) {
        return number_format($b / 1024, 1) . ' KB';
    }
    return $b . ' B';
}
?>

<div class="card">
  <div class="card-header card-header--split">
    <h2>Media Library</h2>
    <button class="btn btn-primary" type="button" onclick="openMediaModal('dummy','dummyPreview')"><i class="fas fa-upload"></i> Upload / Select</button>
  </div>
  <div class="card-body card-body--stack">
    <p class="text-muted" style="margin-top:0;">Upload images and reuse them across page editors and settings. <input id="dummy" type="hidden" value=""><span id="dummyPreview" style="display:none;"></span></p>

    <form method="get" action="" class="form-row" style="align-items:flex-end;margin-bottom:16px;gap:12px;flex-wrap:wrap;">
      <div class="form-group" style="margin-bottom:0;flex:1;min-width:200px;">
        <label for="media_q">Search</label>
        <input type="search" id="media_q" name="q" value="<?= sanitize($search) ?>" placeholder="File name…">
      </div>
      <button type="submit" class="btn btn-outline">Search</button>
      <?php if ($search !== ''): ?>
        <a href="<?= htmlspecialchars($_SERVER['PHP_SELF'] ?? 'media.php', ENT_QUOTES, 'UTF-8') ?>" class="btn btn-outline">Clear</a>
      <?php endif; ?>
    </form>

    <?php if (empty($items)): ?>
      <p class="text-muted" style="padding:24px;text-align:center;border:1px dashed var(--border-color);border-radius:8px;">
        <?php if ($total === 0 && $search === ''): ?>No files yet. Use <strong>Upload / Select</strong> to add images.<?php else: ?>No matches.<?php endif; ?>
      </p>
    <?php else: ?>
      <div class="table-wrapper">
        <table class="table">
          <thead>
            <tr>
              <th style="width:90px;">Preview</th>
              <th>Original name</th>
              <th>Path</th>
              <th>Size</th>
              <th>Uploaded</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($items as $row): ?>
              <?php
                $url = SITE_URL . ltrim((string)$row['file_path'], '/');
              ?>
              <tr>
                <td>
                  <a href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="image-thumb-link">
                    <img src="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>" alt="" style="width:72px;height:72px;object-fit:cover;border-radius:4px;border:1px solid var(--border-color);">
                  </a>
                </td>
                <td><?= sanitize($row['original_name'] ?? '') ?></td>
                <td><code style="font-size:12px;word-break:break-all;"><?= sanitize($row['file_path'] ?? '') ?></code></td>
                <td><?= sanitize(formatBytesMedia($row['file_size'] ?? 0)) ?></td>
                <td class="text-muted" style="white-space:nowrap;font-size:13px;"><?= sanitize($row['uploaded_at'] ?? '') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <?php if ($pages > 1): ?>
        <nav class="media-pagination" style="display:flex;gap:8px;justify-content:center;flex-wrap:wrap;margin-top:20px;align-items:center;">
          <?php
            $base = 'media.php';
            $qs = [];
            if ($search !== '') {
                $qs['q'] = $search;
            }
            for ($p = 1; $p <= $pages; $p++):
              $qs['p'] = $p;
              $href = $base . '?' . http_build_query($qs);
              ?>
            <?php if ($p === $page): ?>
              <span class="btn btn-sm btn-primary" style="pointer-events:none;"><?= (int)$p ?></span>
            <?php else: ?>
              <a class="btn btn-sm btn-outline" href="<?= htmlspecialchars($href, ENT_QUOTES, 'UTF-8') ?>"><?= (int)$p ?></a>
            <?php endif; ?>
          <?php endfor; ?>
        </nav>
      <?php endif; ?>

      <p class="text-muted" style="margin-bottom:0;font-size:13px;">Showing <?= count($items) ?> of <?= (int)$total ?> file(s).</p>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
