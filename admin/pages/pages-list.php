<?php
$pageTitle = 'Pages';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
require_once __DIR__ . '/../includes/header.php';

$availablePages = [
  'index' => ['name' => 'Homepage', 'editor' => 'homepage.php', 'icon' => 'fa-home'],
  'about' => ['name' => 'About', 'editor' => 'about-page.php', 'icon' => 'fa-info-circle'],
  'rooms' => ['name' => 'Rooms listing (hero & intro)', 'editor' => 'rooms-page.php', 'icon' => 'fa-list'],
  'contact' => ['name' => 'Contact', 'editor' => 'contact-page.php', 'icon' => 'fa-envelope'],
  'gallery' => ['name' => 'Gallery', 'editor' => 'gallery-page.php', 'icon' => 'fa-images'],
  'dining' => ['name' => 'Dining', 'editor' => 'dining-page.php', 'icon' => 'fa-utensils'],
  'amenities' => ['name' => 'Amenities', 'editor' => 'amenities-page.php', 'icon' => 'fa-spa'],
  'hotel-policy' => ['name' => 'Hotel Policy', 'editor' => 'hotel-policy-page.php', 'icon' => 'fa-clipboard-list'],
  'privacy-policy' => ['name' => 'Privacy Policy', 'editor' => 'privacy-policy-page.php', 'icon' => 'fa-user-shield'],
  'terms-and-conditions' => ['name' => 'Terms & Conditions', 'editor' => 'terms-and-conditions-page.php', 'icon' => 'fa-file-contract'],
];

try {
    $stmt = $pdo->prepare("SELECT DISTINCT page FROM page_sections ORDER BY page");
    $stmt->execute();
    $pagesInDb = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $pagesInDb = [];
}
?>

<div class="card">
  <div class="card-header"><h2>Pages</h2></div>
  <div style="padding:20px;">
    <p style="margin-bottom:20px;color:var(--text-muted);">Edit copy and images per page. <strong>Rooms</strong> are managed under <a href="<?= ADMIN_URL ?>pages/rooms/list.php">Rooms</a> (including Featured for the homepage slider).</p>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:20px;">
      <?php foreach ($availablePages as $pageKey => $info): ?>
        <div style="border:1px solid var(--border-color);border-radius:4px;padding:20px;background:white;">
          <div style="display:flex;align-items:center;margin-bottom:15px;">
            <i class="fas <?= $info['icon'] ?>" style="font-size:32px;color:var(--primary-color);margin-right:15px;"></i>
            <div>
              <h3 style="margin:0;font-size:18px;font-weight:600;"><?= sanitize($info['name']) ?></h3>
              <?php if (in_array($pageKey, $pagesInDb)): ?>
                <small style="color: var(--success-color);"><i class="fas fa-check-circle"></i> Has saved content</small>
              <?php else: ?>
                <small style="color: var(--text-muted);"><i class="fas fa-circle"></i> Using site defaults</small>
              <?php endif; ?>
            </div>
          </div>
          <a href="<?= ADMIN_URL ?>pages/<?= $info['editor'] ?>" class="btn btn-primary" style="width:100%;"><i class="fas fa-edit"></i> Edit Page</a>
        </div>
      <?php endforeach; ?>
    </div>
    <div style="margin-top:24px;padding-top:20px;border-top:1px solid var(--border-color);">
      <a href="<?= ADMIN_URL ?>pages/rooms/list.php" class="btn btn-outline"><i class="fas fa-bed"></i> Manage all rooms</a>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
