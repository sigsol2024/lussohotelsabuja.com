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
if (!is_array($galleryItems) || count($galleryItems) === 0) {
    $galleryItems = $cmsDefaults['gallery_items'];
}

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
  </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display antialiased text-text-main dark:text-white transition-colors duration-300">
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="relative flex h-auto min-h-screen w-full flex-col overflow-x-hidden">
  <section class="relative w-full h-[60vh] md:h-[70vh] flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat transform scale-105"
         style='background-image: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.5)), url("<?= e($hero_bg) ?>");'>
    </div>
    <div class="relative z-10 text-center px-4 max-w-4xl mx-auto flex flex-col items-center gap-4">
      <span class="text-primary text-xs md:text-sm font-bold tracking-[0.3em] uppercase mb-2"><?= e($hero_kicker) ?></span>
      <h1 class="text-white text-5xl md:text-7xl font-light tracking-[-0.02em] font-display">
        <?= $hero_title_html ?>
      </h1>
      <p class="text-white/90 text-sm md:text-base font-light tracking-wide max-w-lg leading-relaxed mt-4">
        <?= e($hero_subtitle) ?>
      </p>
    </div>
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 text-white/50 animate-bounce">
      <span class="material-symbols-outlined">keyboard_arrow_down</span>
    </div>
  </section>

  <div class="sticky top-[73px] z-40 bg-background-light dark:bg-background-dark py-6 md:py-8 border-b border-gray-100 dark:border-white/5">
    <div class="flex justify-center overflow-x-auto no-scrollbar px-6">
      <div class="flex items-center gap-8 md:gap-12 min-w-max">
        <span class="text-xs font-bold tracking-[0.15em] text-text-main dark:text-white border-b border-primary pb-2">ALL</span>
        <span class="text-xs font-bold tracking-[0.15em] text-gray-500 dark:text-gray-400">ARCHITECTURE</span>
        <span class="text-xs font-bold tracking-[0.15em] text-gray-500 dark:text-gray-400">INTERIORS</span>
        <span class="text-xs font-bold tracking-[0.15em] text-gray-500 dark:text-gray-400">LIFESTYLE</span>
        <span class="text-xs font-bold tracking-[0.15em] text-gray-500 dark:text-gray-400">DINING</span>
      </div>
    </div>
  </div>

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
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex flex-col justify-end p-6 md:p-8">
          <span class="text-primary text-[10px] font-bold tracking-[0.2em] uppercase translate-y-4 group-hover:translate-y-0 transition-transform duration-500 delay-75"><?= e($cat) ?></span>
          <h3 class="text-white text-xl md:text-2xl font-light translate-y-4 group-hover:translate-y-0 transition-transform duration-500 delay-100 mt-2"><?= e($tit) ?></h3>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="flex justify-center mt-20 mb-8">
      <button class="group flex items-center gap-3 text-sm font-semibold tracking-[0.2em] text-gray-500 hover:text-primary transition-colors duration-300 uppercase" type="button">
        Load More
        <span class="material-symbols-outlined transition-transform duration-300 group-hover:translate-y-1">expand_more</span>
      </button>
    </div>
  </main>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
