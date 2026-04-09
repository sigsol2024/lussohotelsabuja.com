            </div>
        </div>
    </div>

    <div id="toastContainer" class="toast-container"></div>
    <?php require_once __DIR__ . '/media-modal.php'; ?>

    <?php
      $adminJs = __DIR__ . '/../assets/js/admin.js';
      $mediaJs = __DIR__ . '/../assets/js/media-library.js';
      $adminV = is_file($adminJs) ? (string)filemtime($adminJs) : '1';
      $mediaV = is_file($mediaJs) ? (string)filemtime($mediaJs) : '1';
    ?>
    <script src="<?= ADMIN_URL ?>assets/js/admin.js?v=<?= htmlspecialchars($adminV, ENT_QUOTES, 'UTF-8') ?>"></script>
    <script src="<?= ADMIN_URL ?>assets/js/media-library.js?v=<?= htmlspecialchars($mediaV, ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>

