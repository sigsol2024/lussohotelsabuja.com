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
  </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display antialiased overflow-x-hidden">
<?php require_once __DIR__ . '/includes/header.php'; ?>

<main class="w-full flex flex-col">
  <?php foreach ($sections as $sec):
    $bg = (string)($sec['bg'] ?? '');
    $gradient = (string)($sec['gradient'] ?? 'linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.7))');
    $kicker = (string)($sec['kicker'] ?? '');
    $icon = (string)($sec['icon'] ?? 'star');
    $titleHtml = (string)($sec['title_html'] ?? '');
    $body = (string)($sec['body'] ?? '');
    $btn = (string)($sec['btn'] ?? '');
    $btnHref = (string)($sec['btn_href'] ?? '#');
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
          <span class="text-xs font-bold tracking-[0.3em] uppercase"><?= e($kicker) ?></span>
          <div class="h-[1px] w-12 bg-white/40"></div>
        </div>
        <div class="<?= e(amenities_title_wrap_class($layout)) ?>">
          <span class="material-symbols-outlined text-5xl md:text-6xl text-primary font-light mb-4"><?= e($icon) ?></span>
          <h1 class="text-white text-6xl md:text-8xl font-light leading-[0.9] tracking-tighter">
            <?= $titleHtml ?>
          </h1>
        </div>
        <p class="<?= e(amenities_body_class($layout)) ?>">
          <?= e($body) ?>
        </p>
        <?php if ($layout === 'center'): ?>
        <button class="flex items-center gap-3 px-8 py-4 bg-primary text-background-dark rounded font-bold hover:bg-white hover:text-black transition-all duration-300" type="button" onclick="location.href='<?= e(lusso_href($btnHref)) ?>'">
          <span class="text-sm tracking-[0.1em] uppercase"><?= e($btn) ?></span>
          <span class="material-symbols-outlined text-lg">arrow_outward</span>
        </button>
        <?php elseif ($layout === 'top'): ?>
        <button class="mt-6 flex items-center gap-3 px-8 py-4 border border-white text-white rounded hover:bg-primary hover:border-primary hover:text-white transition-all duration-300 group/btn" type="button" onclick="location.href='<?= e(lusso_href($btnHref)) ?>'">
          <span class="text-sm font-bold tracking-[0.1em] uppercase"><?= e($btn) ?></span>
          <span class="material-symbols-outlined text-lg group-hover/btn:translate-x-1 transition-transform">water_drop</span>
        </button>
        <?php else: ?>
        <button class="mt-6 flex items-center gap-3 px-8 py-4 border border-white text-white rounded hover:bg-primary hover:border-primary hover:text-white transition-all duration-300 group/btn" type="button" onclick="location.href='<?= e(lusso_href($btnHref)) ?>'">
          <span class="text-sm font-bold tracking-[0.1em] uppercase"><?= e($btn) ?></span>
          <span class="material-symbols-outlined text-lg group-hover/btn:translate-x-1 transition-transform">arrow_forward</span>
        </button>
        <?php endif; ?>
      </div>
    </div>
  </section>
  <?php endforeach; ?>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
