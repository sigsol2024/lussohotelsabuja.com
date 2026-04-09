<?php
require_once __DIR__ . '/includes/content-loader.php';

$slug = $_GET['slug'] ?? '';
if (empty($slug)) {
    header('Location: ' . lusso_url('rooms'));
    exit;
}

$room = getRoomBySlug($slug);
if (!$room) {
    header('Location: ' . lusso_url('rooms'));
    exit;
}

$siteName = getSiteSetting('site_name', 'Lusso');
$whatsappLink = getSiteSetting('whatsapp_link', '');
$heroBadge = getSiteSetting('room_detail_hero_badge', 'Lusso Abuja');

$title = (string)($room['title'] ?? '');
$roomType = trim((string)($room['room_type'] ?? ''));
$pageTitle = $title ? ($title . ' - ' . $siteName) : ('Room Details - ' . $siteName);

$mainImage = (string)($room['main_image'] ?? '');
$images = [];
if ($mainImage !== '') {
    $images[] = $mainImage;
}
if (!empty($room['gallery_images']) && is_array($room['gallery_images'])) {
    foreach ($room['gallery_images'] as $img) {
        if (is_string($img) && trim($img) !== '') {
            $images[] = trim($img);
        }
    }
}
$images = array_values(array_unique($images));

$stickyImage = $images[1] ?? $images[0] ?? '';
$mobileDividerImage = $images[2] ?? $images[1] ?? '';

$priceRaw = is_numeric($room['price'] ?? null) ? (float)$room['price'] : null;
$price = $priceRaw !== null ? number_format($priceRaw, 0) : '';
$currency = getSiteSetting('currency_symbol', '$');

$description = trim((string)($room['description'] ?? ''));
$short = trim((string)($room['short_description'] ?? ''));
$paras = preg_split('/\r?\n\s*\r?\n/', $description, -1, PREG_SPLIT_NO_EMPTY);
$spacePara = isset($paras[0]) ? trim($paras[0]) : $description;
$experiencePara = isset($paras[1]) ? trim($paras[1]) : ($short !== '' ? $short : $spacePara);

$conceptQuote = $short;
if ($conceptQuote !== '' && !preg_match('/^["«]/u', $conceptQuote)) {
    $conceptQuote = '"' . $conceptQuote . '"';
}

$size = trim((string)($room['size'] ?? ''));
$maxGuests = (int)($room['max_guests'] ?? 0);

$featuresRaw = $room['features'] ?? [];
$features = [];
$bed = '';
$view = '';
if (is_array($featuresRaw)) {
    foreach ($featuresRaw as $f) {
        if (is_string($f) && trim($f) !== '') {
            $features[] = trim($f);
        }
        if (is_array($f) && !empty($f['title'])) {
            $features[] = trim((string)$f['title']);
        }
    }
}
foreach ($features as $f) {
    $lf = strtolower($f);
    if ($bed === '' && (str_contains($lf, 'bed') || str_contains($lf, 'king'))) {
        $bed = $f;
    }
    if ($view === '' && str_contains($lf, 'view')) {
        $view = $f;
    }
    if ($size === '' && (str_contains($lf, 'sqm') || str_contains($lf, 'sqft') || str_contains($lf, 'm²'))) {
        $size = $f;
    }
}

$featureChips = array_values(array_filter(array_map(static function ($s) {
    return is_string($s) ? trim($s) : '';
}, $features), static fn ($x) => $x !== ''));
$featureChips = array_values(array_unique($featureChips));

$includedItems = is_array($room['included_items'] ?? null) ? $room['included_items'] : [];
$includedItems = array_values(array_filter(array_map(function ($i) {
    return is_string($i) ? trim($i) : '';
}, $includedItems), fn ($x) => $x !== ''));

// Legacy guest info (good_to_know) removed from public page.

$amenitiesRaw = is_array($room['amenities'] ?? null) ? $room['amenities'] : [];
$amenityCards = [];
foreach ($amenitiesRaw as $a) {
    if (is_string($a) && trim($a) !== '') {
        $amenityCards[] = ['icon' => 'check_circle', 'title' => trim($a), 'desc' => 'Included'];
    } elseif (is_array($a)) {
        $t = trim((string)($a['title'] ?? $a['name'] ?? ''));
        if ($t === '') {
            continue;
        }
        $amenityCards[] = [
            'icon' => trim((string)($a['icon'] ?? 'check_circle')),
            'title' => $t,
            'desc' => trim((string)($a['description'] ?? $a['desc'] ?? 'Included')),
        ];
    }
}

$bookUrl = htmlspecialchars_decode((string)($room['book_url'] ?? ''), ENT_QUOTES);
$bookUrl = trim($bookUrl);
if ($bookUrl === '') {
    $bookUrl = htmlspecialchars_decode((string)$whatsappLink, ENT_QUOTES);
    $bookUrl = trim($bookUrl);
}
if ($bookUrl === '') {
    $bookUrl = '#';
}

$occupancyLabel = $maxGuests > 0
    ? 'Up to ' . $maxGuests . ' guest' . ($maxGuests > 1 ? 's' : '')
    : '—';
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title><?= e($pageTitle) ?></title>
  <?php require_once __DIR__ . '/includes/head-header.php'; ?>
  <style>
    .glass-panel {
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border-top: 1px solid rgba(255, 255, 255, 0.3);
    }
  </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-text-main font-display antialiased overflow-x-hidden selection:bg-primary/30">
<?php require_once __DIR__ . '/includes/header.php'; ?>

<main class="w-full pt-20 pb-32">
  <section class="relative w-full h-[85vh] min-h-[600px] bg-background-light overflow-hidden flex items-center justify-center">
    <div class="absolute inset-0 w-full h-full z-0">
      <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-transparent to-black/60 z-10"></div>
      <div class="w-full h-full bg-cover bg-center bg-no-repeat transform scale-105"
           data-alt="<?= e($title) ?>"
           style="background-image: url('<?= e($images[0] ?? '') ?>');"></div>
    </div>

    <div class="relative z-20 text-center px-6 max-w-5xl mx-auto flex flex-col items-center gap-6">
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-white/30 bg-white/10 backdrop-blur-sm text-white mb-2">
        <span class="text-xs font-bold tracking-[0.15em] uppercase"><?= e($heroBadge) ?></span>
      </div>
      <h1 class="text-white text-5xl md:text-7xl lg:text-8xl font-medium tracking-tight leading-[1.1]">
        <?= e($title) ?><?php if ($roomType !== ''): ?><br/><span class="font-serif italic text-primary"><?= e($roomType) ?></span><?php endif; ?>
      </h1>
      <div class="mt-8 flex flex-col md:flex-row gap-4 items-center">
        <?php if (count($images) > 1): ?>
        <a class="h-12 px-8 bg-primary hover:bg-primary-light text-white rounded-lg text-sm font-bold tracking-wide transition-all transform hover:scale-105 flex items-center gap-2"
           href="#gallery">
          <span>View Gallery</span>
          <span class="material-symbols-outlined text-lg">arrow_forward</span>
        </a>
        <?php endif; ?>
      </div>
    </div>
    <div class="absolute bottom-10 left-1/2 -translate-x-1/2 z-20 animate-bounce text-white/70">
      <a href="#concept" class="inline-flex" aria-label="Scroll to details">
        <span class="material-symbols-outlined text-3xl">keyboard_arrow_down</span>
      </a>
    </div>
  </section>

  <?php if ($conceptQuote !== ''): ?>
  <section id="concept" class="px-6 py-20 md:py-32 max-w-4xl mx-auto text-center">
    <span class="block text-primary text-sm font-bold tracking-[0.2em] uppercase mb-6">The Concept</span>
    <h2 class="text-3xl md:text-5xl font-light leading-tight text-text-main font-serif">
      <?= e($conceptQuote) ?>
    </h2>
  </section>
  <?php endif; ?>

  <section class="max-w-[1440px] mx-auto px-6 lg:px-12 pb-24">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-24">
      <div class="lg:col-span-5 relative min-w-0 w-full">
        <div class="sticky top-24 md:top-32 min-h-[300px] h-[52vh] max-h-[600px] md:h-[600px] w-full rounded-2xl overflow-hidden shadow-2xl">
          <div class="w-full min-h-[300px] h-full bg-cover bg-center"
               data-alt="<?= e($title) ?>"
               style="background-image: url('<?= e($stickyImage) ?>');"></div>
        </div>
      </div>

      <div class="lg:col-span-7 flex flex-col gap-20 py-8 lg:py-0">
        <div class="flex flex-col gap-6">
          <div class="flex items-center gap-4 mb-2">
            <span class="h-[1px] w-12 bg-primary"></span>
            <span class="text-xs font-bold tracking-[0.2em] text-text-muted uppercase">Architecture</span>
          </div>
          <h3 class="text-4xl font-medium text-text-main">The Space</h3>
          <?php if ($spacePara !== ''): ?>
          <p class="text-lg leading-relaxed text-[#5a5445]"><?= nl2br(e($spacePara)) ?></p>
          <?php endif; ?>

          <?php if (!empty($featureChips)): ?>
          <div class="mt-6 flex flex-wrap gap-2">
            <?php foreach (array_slice($featureChips, 0, 10) as $chip): ?>
              <span class="text-xs font-bold uppercase tracking-widest px-3 py-1 rounded-full border border-[#e5e5e5] bg-white/70"><?= e($chip) ?></span>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
          <ul class="grid grid-cols-2 gap-y-4 gap-x-8 mt-4 border-t border-[#e5e5e5] pt-6">
            <li class="flex flex-col gap-1">
              <span class="text-xs text-text-muted uppercase tracking-wide">Size</span>
              <span class="font-bold text-text-main"><?= e($size !== '' ? $size : '—') ?></span>
            </li>
            <li class="flex flex-col gap-1">
              <span class="text-xs text-text-muted uppercase tracking-wide">Occupancy</span>
              <span class="font-bold text-text-main"><?= e($occupancyLabel) ?></span>
            </li>
            <li class="flex flex-col gap-1">
              <span class="text-xs text-text-muted uppercase tracking-wide">View</span>
              <span class="font-bold text-text-main"><?= e($view !== '' ? $view : '—') ?></span>
            </li>
            <li class="flex flex-col gap-1">
              <span class="text-xs text-text-muted uppercase tracking-wide">Bed</span>
              <span class="font-bold text-text-main"><?= e($bed !== '' ? $bed : '—') ?></span>
            </li>
          </ul>
        </div>

        <?php if ($mobileDividerImage !== ''): ?>
        <div class="block lg:hidden h-[300px] w-full rounded-xl overflow-hidden my-4">
          <div class="w-full h-full bg-cover bg-center"
               style="background-image: url('<?= e($mobileDividerImage) ?>');"></div>
        </div>
        <?php endif; ?>

        <div class="flex flex-col gap-6">
          <div class="flex items-center gap-4 mb-2">
            <span class="h-[1px] w-12 bg-primary"></span>
            <span class="text-xs font-bold tracking-[0.2em] text-text-muted uppercase">Service</span>
          </div>
          <h3 class="text-4xl font-medium text-text-main">The Experience</h3>
          <?php if ($experiencePara !== ''): ?>
          <p class="text-lg leading-relaxed text-[#5a5445]"><?= nl2br(e($experiencePara)) ?></p>
          <?php endif; ?>

          <?php if (!empty($includedItems)): ?>
          <div class="bg-[#f4f3f0] p-6 rounded-xl mt-4 border border-[#e5e4e0]">
            <h4 class="font-bold text-text-main mb-2 flex items-center gap-2">
              <span class="material-symbols-outlined text-primary">verified</span>
              Included Privileges
            </h4>
            <ul class="space-y-3 mt-4 text-sm text-[#5a5445]">
              <?php foreach ($includedItems as $item): ?>
              <li class="flex items-start gap-3">
                <span class="w-1.5 h-1.5 rounded-full bg-primary mt-1.5 shrink-0"></span>
                <span><?= e($item) ?></span>
              </li>
              <?php endforeach; ?>
            </ul>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>

  <?php if (!empty($amenityCards)): ?>
  <section class="w-full bg-background-dark text-white py-24">
    <div class="max-w-[1440px] mx-auto px-6 lg:px-12">
      <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
        <div>
          <span class="text-primary text-xs font-bold tracking-[0.2em] uppercase block mb-3">Curated Comforts</span>
          <h3 class="text-4xl md:text-5xl font-light">Refined Amenities</h3>
        </div>
      </div>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-x-8 gap-y-12">
        <?php foreach (array_slice($amenityCards, 0, 12) as $card): ?>
        <div class="group flex flex-col gap-4 p-4 border border-white/5 hover:border-primary/30 rounded-lg transition-all hover:bg-white/5">
          <span class="material-symbols-outlined text-4xl text-primary font-light group-hover:scale-110 transition-transform origin-left"><?= e($card['icon']) ?></span>
          <div>
            <h4 class="font-bold text-lg mb-1"><?= e($card['title']) ?></h4>
            <p class="text-xs text-white/50 leading-relaxed"><?= e($card['desc'] !== '' ? $card['desc'] : 'Included') ?></p>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <?php if (count($images) > 1): ?>
  <section id="gallery" class="max-w-[1440px] mx-auto px-6 lg:px-12 py-24">
    <h3 class="text-4xl font-medium text-text-main mb-12 text-center md:text-left">Details</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 auto-rows-[300px]">
      <?php
      $g = array_values($images);
      $n = count($g);
      if ($n >= 4) {
          ?>
      <div class="md:col-span-2 row-span-1 min-h-[300px] h-full rounded-lg overflow-hidden relative group cursor-zoom-in">
        <div class="w-full min-h-[300px] h-full bg-cover bg-center transition-transform duration-700 group-hover:scale-105" style="background-image:url('<?= e($g[0]) ?>');"></div>
      </div>
      <div class="md:col-span-1 row-span-2 min-h-[300px] h-full rounded-lg overflow-hidden relative group cursor-zoom-in">
        <div class="w-full min-h-[300px] h-full bg-cover bg-center transition-transform duration-700 group-hover:scale-105" style="background-image:url('<?= e($g[1]) ?>');"></div>
      </div>
      <div class="md:col-span-1 row-span-1 min-h-[300px] h-full rounded-lg overflow-hidden relative group cursor-zoom-in">
        <div class="w-full min-h-[300px] h-full bg-cover bg-center transition-transform duration-700 group-hover:scale-105" style="background-image:url('<?= e($g[2]) ?>');"></div>
      </div>
      <div class="md:col-span-1 row-span-1 min-h-[300px] h-full rounded-lg overflow-hidden relative group cursor-zoom-in">
        <div class="w-full min-h-[300px] h-full bg-cover bg-center transition-transform duration-700 group-hover:scale-105" style="background-image:url('<?= e($g[3]) ?>');"></div>
      </div>
          <?php
      } else {
          foreach (array_slice($g, 0, 5) as $idx => $img) {
              $span = ($idx === 0 && $n > 1) ? 'md:col-span-2' : 'md:col-span-1';
              echo '<div class="' . $span . ' rounded-lg overflow-hidden relative group cursor-zoom-in">';
              echo '<div class="w-full h-full min-h-[260px] bg-cover bg-center transition-transform duration-700 group-hover:scale-105" style="background-image:url(\'' . e($img) . '\');"></div>';
              echo '</div>';
          }
      }
      ?>
    </div>
  </section>
  <?php endif; ?>
</main>

<div class="fixed bottom-0 left-0 w-full z-40 glass-panel shadow-[0_-4px_20px_rgba(0,0,0,0.05)]">
  <div class="max-w-[1440px] mx-auto px-6 lg:px-12 h-20 md:h-24 flex items-center justify-between gap-4">
    <div class="flex flex-col min-w-0">
      <p class="text-xs text-text-muted font-bold uppercase tracking-widest mb-1">Best Rate</p>
      <div class="flex items-baseline gap-1">
        <span class="text-xl md:text-2xl font-bold text-primary"><?= e($currency) ?><?= e($price !== '' ? $price : '—') ?></span>
        <span class="text-sm text-text-muted">/ Night</span>
      </div>
    </div>
    <a class="h-12 px-6 md:px-10 shrink-0 bg-primary hover:bg-primary-light text-white rounded-lg text-sm md:text-base font-bold tracking-wide transition-all shadow-lg shadow-primary/20 flex items-center gap-2"
       href="<?= e($bookUrl) ?>">
      <span>Reserve</span>
      <span class="material-symbols-outlined text-lg">arrow_forward</span>
    </a>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
