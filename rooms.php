<?php
require_once __DIR__ . '/includes/content-loader.php';

$pageTitle = getPageSection('rooms', 'page_title', 'Lusso Rooms & Suites Listing');
$heroTitle = getPageSection('rooms', 'hero_title', 'Sanctuaries of <br/><span class="font-bold italic font-serif">Serenity</span>');
$heroSubtitle = getPageSection('rooms', 'hero_subtitle', "Experience the pinnacle of luxury in our meticulously designed rooms and suites in Abuja. Where architectural precision meets organic comfort.");
$heroBg = getPageSection('rooms', 'hero_bg', 'https://lh3.googleusercontent.com/aida-public/AB6AXuACfCcU52cQzGcvXpfeoUxA6gX-lvIEIwkyZX2KoBlPWe0pI_qjdpUW7jGLvS9PnOzKcru4E0yPYmfybadhICjDcNrkau7o83qlek4lOCFQMs1ESNS65Cq3MBJiRhMZaOdna-7YwmxojNijSAQX_i0epjSoyq4FDKrQ3bMd9y-7QnHNvBAT31pNfiZAz8AKThIPxl278F_xb8KU0SKh1Do-Ac1BXxaz1DZ0ZsURc3mEdLICiZ2mpa7xxpMX6S-ZFJtvaqs9_W0-Y-M');

$currency = getSiteSetting('currency_symbol', '$');
$rooms = getRooms(['is_active' => 1]);
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
    ::-webkit-scrollbar-track { background: #efe8d6; }
    ::-webkit-scrollbar-thumb { background: #c9bfab; border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: #411d13; }
  </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-text-main antialiased overflow-x-hidden relative">
<?php require_once __DIR__ . '/includes/header.php'; ?>

<!-- Hero Section -->
<section class="relative min-h-[90vh] flex items-center justify-center pt-20 overflow-hidden">
  <div class="absolute inset-0 z-0">
    <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-transparent to-background-light z-10"></div>
    <div class="w-full h-full bg-cover bg-center bg-no-repeat bg-fixed scale-105"
         data-alt="Wide angle view of a dimly lit, ultra-luxury hotel bedroom with floor-to-ceiling windows"
         style="background-image: url('<?= e($heroBg) ?>');">
    </div>
  </div>
  <div class="relative z-20 container mx-auto px-6 text-center max-w-4xl mt-12">
    <h2 class="text-primary text-sm md:text-base uppercase tracking-[0.3em] font-bold mb-4">Accommodations</h2>
    <h1 class="text-white text-5xl md:text-7xl font-light leading-tight mb-8 drop-shadow-lg">
      <?= $heroTitle ?>
    </h1>
    <p class="text-gray-200 text-lg md:text-xl font-light max-w-2xl mx-auto leading-relaxed drop-shadow-md">
      <?= e($heroSubtitle) ?>
    </p>
    <div class="mt-12 flex justify-center">
      <span class="material-symbols-outlined text-white/50 animate-bounce text-4xl">keyboard_arrow_down</span>
    </div>
  </div>
</section>

<!-- Listings -->
<main class="relative z-10 bg-background-light dark:bg-background-dark bg-subtle-pattern pb-32">
  <div class="max-w-[1280px] mx-auto px-6 lg:px-12 py-24 flex flex-col gap-32 lg:gap-48">
    <?php if (empty($rooms)): ?>
      <div class="text-center text-text-muted">No rooms available yet.</div>
    <?php else: ?>
      <?php
      $i = 0;
      foreach ($rooms as $room):
        $i++;
        $reverse = ($i % 2 === 0);
        $title = $room['title'] ?? '';
        $slug = $room['slug'] ?? '';
        $price = is_numeric($room['price'] ?? null) ? number_format((float)$room['price'], 0) : '';
        $desc = $room['short_description'] ?: ($room['description'] ?? '');
        $mainImage = $room['main_image'] ?? '';
        $gallery = is_array($room['gallery_images'] ?? null) ? $room['gallery_images'] : [];
        $detailImage = !empty($gallery) ? ($gallery[0] ?? '') : '';
        $size = $room['size'] ?? '';
        $bed = '';
        $view = '';
        $wifi = 'Hi-Speed Wifi';

        $featuresRaw = $room['features'] ?? [];
        $features = [];
        if (is_array($featuresRaw)) {
          foreach ($featuresRaw as $f) {
            if (is_string($f) && trim($f) !== '') $features[] = trim($f);
            if (is_array($f) && !empty($f['title'])) $features[] = (string)$f['title'];
          }
        }
        foreach ($features as $f) {
          $lf = strtolower($f);
          if (!$bed && (str_contains($lf, 'bed') || str_contains($lf, 'king'))) $bed = $f;
          if (!$view && str_contains($lf, 'view')) $view = $f;
          if (!$size && (str_contains($lf, 'sqm') || str_contains($lf, 'sqft') || str_contains($lf, 'm²'))) $size = $f;
        }
        ?>

      <article class="group relative flex flex-col <?= $reverse ? 'lg:flex-row-reverse' : 'lg:flex-row' ?> items-stretch lg:items-center gap-12 lg:gap-24">
        <div class="w-full lg:w-3/5 flex flex-col items-center gap-6 min-w-0<?= !empty($detailImage) ? ' md:pb-16 lg:pb-0' : '' ?>">
          <div class="relative w-full aspect-[4/3] overflow-hidden rounded-lg shadow-2xl">
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat transition-transform duration-700 group-hover:scale-105"
                 data-alt="<?= e($title) ?>"
                 style="background-image: url('<?= e($mainImage) ?>');"></div>
          </div>
          <?php if (!empty($detailImage)): ?>
          <div class="relative w-40 max-w-[min(100%,240px)] aspect-square shrink-0 rounded-lg border-4 border-white shadow-xl overflow-hidden z-10 md:absolute md:mt-0 md:w-64 md:max-w-none md:-bottom-12 <?= $reverse ? 'md:-left-6 lg:-left-12' : 'md:-right-6 lg:-right-12' ?>">
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat hover:scale-110 transition-transform duration-500"
                 data-alt="<?= e($title) ?> detail"
                 style="background-image: url('<?= e($detailImage) ?>');"></div>
          </div>
          <?php endif; ?>
        </div>

        <div class="w-full lg:w-2/5 flex flex-col justify-center min-w-0 <?= $reverse ? 'lg:items-end lg:text-right' : '' ?>">
          <span class="text-primary text-xs font-bold tracking-[0.2em] uppercase mb-3">Starting at <?= e($currency) ?><?= e($price) ?></span>
          <h2 class="text-4xl md:text-5xl font-light text-text-main mb-6"><?= e($title) ?></h2>
          <p class="text-text-muted text-base leading-relaxed mb-8"><?= e($desc) ?></p>

          <div class="grid grid-cols-2 gap-4 mb-8 opacity-60 group-hover:opacity-100 transition-opacity duration-300 <?= $reverse ? 'w-full lg:w-auto' : '' ?>" <?= $reverse ? 'dir="rtl"' : '' ?>>
            <div class="flex items-center gap-3 <?= $reverse ? 'lg:flex-row-reverse' : '' ?>">
              <span class="material-symbols-outlined text-primary">square_foot</span>
              <span class="text-sm font-medium uppercase tracking-wider text-text-main"><?= e($size ?: '—') ?></span>
            </div>
            <div class="flex items-center gap-3 <?= $reverse ? 'lg:flex-row-reverse' : '' ?>">
              <span class="material-symbols-outlined text-primary">king_bed</span>
              <span class="text-sm font-medium uppercase tracking-wider text-text-main"><?= e($bed ?: '—') ?></span>
            </div>
            <div class="flex items-center gap-3 <?= $reverse ? 'lg:flex-row-reverse' : '' ?>">
              <span class="material-symbols-outlined text-primary">location_city</span>
              <span class="text-sm font-medium uppercase tracking-wider text-text-main"><?= e($view ?: '—') ?></span>
            </div>
            <div class="flex items-center gap-3 <?= $reverse ? 'lg:flex-row-reverse' : '' ?>">
              <span class="material-symbols-outlined text-primary">wifi</span>
              <span class="text-sm font-medium uppercase tracking-wider text-text-main"><?= e($wifi) ?></span>
            </div>
          </div>

          <a class="inline-flex items-center justify-center px-8 py-3 border border-primary/50 text-primary font-bold uppercase text-xs tracking-widest rounded-lg hover:bg-primary hover:text-white transition-all duration-300 w-fit"
             href="<?= e(lusso_url('room-details', ['slug' => $slug])) ?>">
            View Room Details
          </a>
        </div>
      </article>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

