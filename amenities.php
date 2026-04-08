<?php
require_once __DIR__ . '/includes/content-loader.php';

$cmsDefaults = require __DIR__ . '/includes/cms-defaults.php';
$pageTitle = getPageSection('amenities', 'page_title', 'Lusso Signature Amenities');

$raw = getPageSection('amenities', 'sections_json', '');
$sections = json_decode($raw, true);
if (!is_array($sections) || count($sections) === 0) {
    $sections = $cmsDefaults['amenities_sections'];
}

function amenities_inner_wrap_class($layout) {
    switch ($layout) {
        case 'right':
            return 'flex-col justify-center items-end text-right max-w-[1920px] mx-auto';
        case 'top':
            return 'flex-col justify-start items-start max-w-[1920px] mx-auto pt-32 md:pt-40';
        case 'center':
            return 'flex-col justify-end items-center text-center max-w-[1920px] mx-auto';
        default:
            return 'flex-col justify-end items-start max-w-[1920px] mx-auto';
    }
}

function amenities_kicker_row_class($layout) {
    if ($layout === 'right') {
        return 'flex items-center gap-4 text-white/60 mb-2 flex-row-reverse';
    }
    if ($layout === 'center') {
        return 'flex items-center gap-4 text-white/60 mb-2';
    }
    return 'flex items-center gap-4 text-white/60 mb-2';
}

function amenities_title_wrap_class($layout) {
    return $layout === 'right' ? 'flex-col items-end gap-2' : 'flex-col gap-2';
}

function amenities_body_class($layout) {
    if ($layout === 'right') {
        return 'text-white/90 text-lg md:text-xl font-light leading-relaxed max-w-md border-r-2 border-primary pr-6 mt-4';
    }
    if ($layout === 'center') {
        return 'text-white/80 text-lg md:text-xl font-light leading-relaxed max-w-lg';
    }
    if ($layout === 'top') {
        return 'text-white/90 text-lg md:text-xl font-light leading-relaxed max-w-md mt-4';
    }
    return 'text-white/90 text-lg md:text-xl font-light leading-relaxed max-w-md border-l-2 border-primary pl-6 mt-4';
}
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title><?= e($pageTitle) ?></title>
  <?php require_once __DIR__ . '/includes/head-header.php'; ?>
  <style>
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: #221d10; }
    ::-webkit-scrollbar-thumb { background: #411d13; border-radius: 4px; }
    .amenity-section { scroll-snap-align: start; scroll-snap-stop: normal; }
    html { scroll-snap-type: y proximity; scroll-behavior: smooth; }
    .text-outline { text-shadow: 0px 0px 1px rgba(255,255,255,0.3); }
    .lusso-modal-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.72); backdrop-filter: blur(8px); display:none; z-index: 9999; }
    .lusso-modal-backdrop.open { display: flex; }
    /* Prevent modal from exceeding viewport height */
    .lusso-amenities-modal-panel { max-height: 90vh; }
    .lusso-amenities-modal-body { min-height: 0; }
  </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display antialiased overflow-x-hidden">
<?php require_once __DIR__ . '/includes/header.php'; ?>

<main class="w-full flex flex-col">
  <?php foreach ($sections as $sec):
    $bg = (string)($sec['bg'] ?? '');
    $gradient = (string)($sec['gradient'] ?? 'linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.7))');
    $kicker = (string)($sec['kicker'] ?? '');
    $kickerDisplay = preg_replace('/^\s*\d+\s*\/\s*/', '', $kicker);
    $icon = (string)($sec['icon'] ?? 'star');
    $titleHtml = (string)($sec['title_html'] ?? '');
    $body = (string)($sec['body'] ?? '');
    $btnHref = (string)($sec['btn_href'] ?? '#');
    $gallery = $sec['gallery'] ?? ($sec['gallery_images'] ?? []);
    if (!is_array($gallery)) { $gallery = []; }
    $gallery = array_values(array_filter(array_map(static function ($p) {
        return is_string($p) ? trim($p) : '';
    }, $gallery), static function ($p) { return $p !== ''; }));
    $galleryJsonAttr = htmlspecialchars(json_encode($gallery, JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8');
    $layout = (string)($sec['layout'] ?? 'bottom');
    $inner = amenities_inner_wrap_class($layout);
    $zPad = ($layout === 'top') ? 'p-8 md:p-20 pt-32 md:pt-40' : 'p-8 md:p-20';
    ?>
  <section class="amenity-section relative w-full h-screen min-h-[700px] flex group overflow-hidden">
    <div class="absolute inset-0 z-0 transition-transform duration-1000 group-hover:scale-105 bg-cover bg-center bg-no-repeat"
         style="background-image: <?= e($gradient) ?>, url('<?= e($bg) ?>');">
    </div>
    <div class="relative z-10 w-full h-full <?= $zPad ?> flex <?= $inner ?>">
      <div class="flex flex-col gap-6 max-w-2xl <?= $layout === 'center' ? 'items-center max-w-3xl' : ($layout === 'right' ? 'items-end' : ($layout === 'top' ? 'items-start' : '')) ?>">
        <div class="<?= e(amenities_kicker_row_class($layout)) ?>">
          <span class="text-xs font-bold tracking-[0.3em] uppercase"><?= e((string)$kickerDisplay) ?></span>
          <div class="h-[1px] w-12 bg-white/40"></div>
        </div>
        <div class="<?= e(amenities_title_wrap_class($layout)) ?>">
          <span class="material-symbols-outlined text-5xl md:text-6xl text-white/90 font-light mb-4"><?= e($icon) ?></span>
          <h1 class="text-white text-6xl md:text-8xl font-light leading-[0.9] tracking-tighter">
            <?= $titleHtml ?>
          </h1>
        </div>
        <p class="<?= e(amenities_body_class($layout)) ?>">
          <?= e($body) ?>
        </p>
        <?php
          $btnIcon = ($layout === 'center') ? 'arrow_outward' : (($layout === 'top') ? 'water_drop' : 'arrow_forward');
          $btnBaseClass = ($layout === 'center')
            ? 'inline-flex w-fit items-center gap-3 px-8 py-4 bg-white/10 backdrop-blur-sm border border-white/30 text-white rounded font-bold hover:bg-white/20 transition-all duration-300'
            : 'mt-4 inline-flex w-fit items-center gap-3 px-8 py-4 bg-white/10 backdrop-blur-sm border border-white/30 text-white rounded hover:bg-white/20 transition-all duration-300 group/btn';
        ?>
        <button class="<?= e($btnBaseClass) ?> js-amenity-gallery-btn"
                type="button"
                data-gallery="<?= $galleryJsonAttr ?>"
                data-fallback-href="<?= e(lusso_href($btnHref)) ?>">
          <span class="text-sm font-bold tracking-[0.1em] uppercase">View Gallery</span>
          <span class="material-symbols-outlined text-lg <?= $layout === 'center' ? '' : 'group-hover/btn:translate-x-1 transition-transform' ?>"><?= e($btnIcon) ?></span>
        </button>
      </div>
    </div>
  </section>
  <?php endforeach; ?>
</main>

<!-- Gallery modal (per section) -->
<div id="amenitiesGalleryModal" class="lusso-modal-backdrop items-center justify-center p-4" role="dialog" aria-modal="true" aria-hidden="true">
  <div class="lusso-amenities-modal-panel w-full max-w-5xl bg-black/70 border border-white/10 rounded-2xl overflow-hidden shadow-2xl flex flex-col">
    <div class="flex items-center justify-between px-5 py-4 border-b border-white/10">
      <div class="text-white/80 text-xs font-bold tracking-[0.25em] uppercase">
        Gallery <span id="amenitiesGalleryCount" class="text-white/60"></span>
      </div>
      <button type="button" id="amenitiesGalleryClose" class="text-white/70 hover:text-white transition-colors">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>
    <div class="lusso-amenities-modal-body relative w-full bg-black flex-1">
      <img id="amenitiesGalleryImg" src="" alt="Gallery image" class="absolute inset-0 w-full h-full object-contain"/>
      <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent pointer-events-none"></div>
      <button type="button" id="amenitiesGalleryPrev" class="absolute left-3 top-1/2 -translate-y-1/2 size-12 rounded-full bg-white/10 border border-white/15 text-white hover:bg-white/20 backdrop-blur-sm transition-colors flex items-center justify-center">
        <span class="material-symbols-outlined">chevron_left</span>
      </button>
      <button type="button" id="amenitiesGalleryNext" class="absolute right-3 top-1/2 -translate-y-1/2 size-12 rounded-full bg-white/10 border border-white/15 text-white hover:bg-white/20 backdrop-blur-sm transition-colors flex items-center justify-center">
        <span class="material-symbols-outlined">chevron_right</span>
      </button>
    </div>
    <div class="px-5 py-4 bg-black/50 border-t border-white/10 shrink-0">
      <div id="amenitiesGalleryThumbs" class="flex gap-3 overflow-x-auto pb-1 max-h-24"></div>
    </div>
  </div>
</div>

<script>
(function () {
  var modal = document.getElementById('amenitiesGalleryModal');
  var imgEl = document.getElementById('amenitiesGalleryImg');
  var thumbsEl = document.getElementById('amenitiesGalleryThumbs');
  var countEl = document.getElementById('amenitiesGalleryCount');
  var closeBtn = document.getElementById('amenitiesGalleryClose');
  var prevBtn = document.getElementById('amenitiesGalleryPrev');
  var nextBtn = document.getElementById('amenitiesGalleryNext');

  if (!modal || !imgEl || !thumbsEl || !closeBtn || !prevBtn || !nextBtn) return;

  var images = [];
  var idx = 0;

  function normSrc(p) {
    if (!p) return '';
    if (String(p).indexOf('http') === 0) return String(p);
    return '<?= rtrim((string)(defined('SITE_URL') ? SITE_URL : ''), '/') ?>/' + String(p).replace(/^\/+/, '');
  }

  function render() {
    if (!images.length) {
      imgEl.removeAttribute('src');
      imgEl.style.display = 'none';
      prevBtn.style.display = 'none';
      nextBtn.style.display = 'none';
      countEl.textContent = '';
      thumbsEl.innerHTML = '<div style="color: rgba(255,255,255,0.7); font-size: 14px; padding: 18px 4px;">No gallery images yet.</div>';
      return;
    }
    imgEl.style.display = 'block';
    prevBtn.style.display = '';
    nextBtn.style.display = '';
    idx = (idx + images.length) % images.length;
    imgEl.src = normSrc(images[idx]);
    countEl.textContent = (idx + 1) + ' / ' + images.length;
    Array.prototype.forEach.call(thumbsEl.querySelectorAll('button[data-i]'), function (b) {
      b.classList.toggle('ring-2', parseInt(b.getAttribute('data-i'), 10) === idx);
      b.classList.toggle('ring-white/70', parseInt(b.getAttribute('data-i'), 10) === idx);
    });
  }

  function open(gallery, fallbackHref) {
    images = Array.isArray(gallery) ? gallery.filter(Boolean) : [];
    idx = 0;
    thumbsEl.innerHTML = '';
    images.forEach(function (p, i) {
      var btn = document.createElement('button');
      btn.type = 'button';
      btn.setAttribute('data-i', String(i));
      btn.className = 'shrink-0 rounded-lg overflow-hidden border border-white/10 ring-offset-0';
      btn.style.width = '84px';
      btn.style.height = '56px';
      btn.innerHTML = '<img src="' + normSrc(p).replace(/"/g, '&quot;') + '" alt="" style="width:100%;height:100%;object-fit:cover;display:block;" />';
      btn.addEventListener('click', function () { idx = i; render(); });
      thumbsEl.appendChild(btn);
    });

    modal.classList.add('open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    render();
  }

  function close() {
    modal.classList.remove('open');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  document.querySelectorAll('.js-amenity-gallery-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var raw = btn.getAttribute('data-gallery') || '[]';
      var fallbackHref = btn.getAttribute('data-fallback-href') || '';
      var g;
      try { g = JSON.parse(raw); } catch (e) { g = []; }
      open(g, fallbackHref);
    });
  });

  closeBtn.addEventListener('click', close);
  modal.addEventListener('click', function (e) { if (e.target === modal) close(); });
  prevBtn.addEventListener('click', function () { idx--; render(); });
  nextBtn.addEventListener('click', function () { idx++; render(); });
  document.addEventListener('keydown', function (e) {
    if (!modal.classList.contains('open')) return;
    if (e.key === 'Escape') close();
    if (e.key === 'ArrowLeft') { idx--; render(); }
    if (e.key === 'ArrowRight') { idx++; render(); }
  });
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
