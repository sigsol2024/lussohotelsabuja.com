<?php
require_once __DIR__ . '/includes/content-loader.php';

$cmsDefaults = require __DIR__ . '/includes/cms-defaults.php';
$pageTitle = getPageSection('gallery', 'page_title', 'Lusso Visual Gallery');
$hero_kicker = getPageSection('gallery', 'hero_kicker', 'The Collection');
$hero_title_html = getPageSection('gallery', 'hero_title_html', 'VISUAL <span class="font-bold italic text-primary">NARRATIVES</span>');
$hero_subtitle = getPageSection('gallery', 'hero_subtitle', 'A curated glimpse into the architecture, lifestyle, and moments that define Lusso Abuja.');
$hero_bg = getPageSection('gallery', 'hero_bg', 'https://lh3.googleusercontent.com/aida-public/AB6AXuCdJfaCSOs5MQG_DTVMby3bOBjCkoGkef0FMU9Qry3ryiP5bNDsyiy__h8ek57_WP4qOYB8oMBkIQx3SfBGNd3bmf9BIRd68F7CgdZyK3nqgEhC32fKLPyXK59qpMNfuuZYox51JBGo0ezJWITKOmkoYSEwCRm0yq-vMBJ2A4MjeExfWzIRLYbt-DtWEZuBAFzukpACYT5ly4EnGU2ABz-ZIpUw4VM9bWWIlBuJMnPezmfGiJ7wNV2U3WSDzxglmX2ClVUbEW5mKp0');

$itemsRaw = getPageSection('gallery', 'items_json', '');
$galleryItems = json_decode($itemsRaw, true);
// New format only: ["path1","path2",...]
// If old object-based JSON exists, we intentionally ignore it (admin will normalize it on edit).
$isValidPathArray =
    is_array($galleryItems) &&
    (count($galleryItems) === 0 || (isset($galleryItems[0]) && is_string($galleryItems[0])));
if (!$isValidPathArray) {
    $galleryItems = $cmsDefaults['gallery_items'];
}
$galleryItems = array_values(array_filter(array_map(static function ($src) {
    $src = is_string($src) ? trim($src) : '';
    if ($src === '') return null;
    return [
        'src' => $src,
        'alt' => '',
        'category' => '',
        'title' => '',
        'ratio' => '3/4',
    ];
}, (array)$galleryItems)));

function gallery_ratio_class($ratio) {
    $r = (string)$ratio;
    $map = [
        '3/4' => 'aspect-[3/4]',
        'video' => 'aspect-video',
        '2/3' => 'aspect-[2/3]',
        'square' => 'aspect-square',
        '16/10' => 'aspect-[16/10]',
        '3/5' => 'aspect-[3/5]',
        '4/5' => 'aspect-[4/5]',
    ];
    return $map[$r] ?? 'aspect-[3/4]';
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
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #d1d1d1; border-radius: 4px; }
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    /* Hero accent stroke (match home/amenities outline feel) */
    #gallery-hero h1 .text-primary {
      -webkit-text-stroke: 1px rgba(255, 255, 255, 0.88);
      text-shadow: 0 0 1px rgba(255, 255, 255, 0.35);
    }
  </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display antialiased text-text-main dark:text-white transition-colors duration-300">
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="relative flex h-auto min-h-screen w-full flex-col overflow-x-hidden">
  <!-- Hero Section (match About hero pattern) -->
  <section id="gallery-hero" class="relative min-h-screen w-full flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0 z-0">
      <div class="absolute inset-0 z-10" style="background: rgba(107, 51, 39, 0.72);"></div>
      <div class="w-full h-full bg-cover bg-center bg-no-repeat scale-105"
           style='background-image: url("<?= e($hero_bg) ?>");'></div>
    </div>
    <div class="relative z-20 container mx-auto px-6 lg:px-12 flex flex-col items-center text-center pt-20">
      <span class="text-white/90 text-xs md:text-sm font-bold tracking-[0.3em] uppercase mb-2"><?= e($hero_kicker) ?></span>
      <h1 class="text-white text-5xl md:text-7xl font-light tracking-[-0.02em] font-display">
        <?= $hero_title_html ?>
      </h1>
      <p class="text-white/90 text-sm md:text-base font-light tracking-wide max-w-lg leading-relaxed mt-4">
        <?= e($hero_subtitle) ?>
      </p>
      <div class="h-16 w-[1px] bg-gradient-to-b from-white/40 to-transparent mt-10"></div>
    </div>
  </section>

  <main class="flex-grow px-4 md:px-12 py-12 max-w-[1600px] mx-auto w-full">
    <div class="columns-1 md:columns-2 lg:columns-3 gap-6 md:gap-8 space-y-6 md:space-y-8">
      <?php foreach ($galleryItems as $item):
        $src = (string)($item['src'] ?? '');
        $alt = (string)($item['alt'] ?? '');
        $cat = (string)($item['category'] ?? '');
        $tit = (string)($item['title'] ?? '');
        $ratio = (string)($item['ratio'] ?? '3/4');
        $ac = gallery_ratio_class($ratio);
        ?>
      <div class="group relative overflow-hidden rounded-md border border-primary/30 break-inside-avoid cursor-pointer">
        <div class="<?= e($ac) ?> relative w-full bg-gray-200">
          <img alt="<?= e($alt) ?>" class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" src="<?= e($src) ?>" loading="lazy" decoding="async"/>
        </div>
        <?php if ($cat !== '' || $tit !== ''): ?>
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex flex-col justify-end p-6 md:p-8">
          <?php if ($cat !== ''): ?>
          <span class="text-primary text-[10px] font-bold tracking-[0.2em] uppercase translate-y-4 group-hover:translate-y-0 transition-transform duration-500 delay-75"><?= e($cat) ?></span>
          <?php endif; ?>
          <?php if ($tit !== ''): ?>
          <h3 class="text-white text-xl md:text-2xl font-light translate-y-4 group-hover:translate-y-0 transition-transform duration-500 delay-100 mt-2"><?= e($tit) ?></h3>
          <?php endif; ?>
        </div>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </main>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
