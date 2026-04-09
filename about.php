<?php
require_once __DIR__ . '/includes/content-loader.php';

$cmsDefaults = require __DIR__ . '/includes/cms-defaults.php';
$pageTitle = getPageSection('about', 'page_title', 'The Lusso Legacy - About Us');

$hero_established = getPageSection('about', 'hero_established', 'Established 2024');
$hero_title_html = getPageSection('about', 'hero_title_html', 'The Lusso <br/><span class="font-bold italic text-primary/90 lusso-hero-accent-text">Legacy</span>');
$hero_subtitle = getPageSection('about', 'hero_subtitle', 'Redefining luxury hospitality in the heart of Abuja through architectural excellence and timeless Nigerian warmth.');
$hero_bg = getPageSection('about', 'hero_bg', 'https://lh3.googleusercontent.com/aida-public/AB6AXuCKUqHq-GN4lZRDb3VpJoBQf0wCp1L0HT1ffMFvon2Fpx5JFUEIVencwF0zLiODNpnveRzU99J5BKJy0pzhV-qqantGLstOLFb5JPlyGtStyFoC0Udyi7ds1hHqs3bQea5IP5yTl64R0lpZUOwrFHM8IU1hGWk8IxE92fhLliSnZOPp9qURFWlXFpaKeyn4pfcHGu8C-Cy6RL5CGYSU1gY2VZcN4xsr9MTU9pn5C9k765bD6huMgag92SUZDmWZnTRfI9TroGluz6U');

$story_title_html = getPageSection('about', 'story_title_html', 'Defining Abuja <br/><span class="font-semibold text-primary">Luxury</span>');
$story_p1 = getPageSection('about', 'story_p1', 'We believe that true luxury lies not just in opulence, but in the meticulous attention to detail and the warmth of genuine hospitality. Our philosophy is rooted in creating sanctuary-like spaces where every interaction is a curated experience.');
$story_p2 = getPageSection('about', 'story_p2', 'Lusso Hotels stands as a beacon of sophistication, merging contemporary design with the rich cultural heritage of Nigeria.');
$story_image = getPageSection('about', 'story_image', 'https://lh3.googleusercontent.com/aida-public/AB6AXuC4lqSBAhhqs4N0scumYaXQVT9ZEKAqsT_RPiR2q01XspmWMfxYxiD7UuiobDr5yZBBKrDx4Hofa_5b3JtjrNNfAQitapKQnelBzcfd0ifQMU_E2voX7irqZkMDtTEY5c-8MtVvKBkg7E2f_0kPteIGEmhNqGdu__3OIeyxumRk3L-Z5ROyKmXnp5aLY7vvpaGzgbcsWSJ3hKRUGQ9WSJAEN3py7TcPvohXQWjbd98ECZq53hLEZ2iMDwWRyaY0IVPkaLFyRqVk768');

$values_kicker = getPageSection('about', 'values_kicker', 'Our Core Values');
$values_title = getPageSection('about', 'values_title', 'Artistry in Service');
$values_image = getPageSection('about', 'values_image', 'https://lh3.googleusercontent.com/aida-public/AB6AXuCy3zS6w7bzryizdISWmfQ_S0JNETi-CNKg-hvJBSLvedYviP7f1Y09Q5t3sMnmMYs21w8obHIIGOb1cQydo18DhPNqcU6t-dpeSXh19ItorLF9eHU8fGaH6E-FlUlWKCaViR6axwv17cKdIMPLxK_tnVCpu9Lgs2g8DqC3v8fs87feUGps5ypr_Pko0JAGNCO8AkRZka4k1aadMxSlooiKWR4svNelcEujv6CkGSmjV0TqQjLjcqDe4Vx0gh2ceUf1oPD-I6d1aVs');
$values_card_icon = getPageSection('about', 'values_card_icon', 'spa');
$values_card_title = getPageSection('about', 'values_card_title', 'Sanctuary for the Senses');
$values_card_body = getPageSection('about', 'values_card_body', 'From the gentle aroma of our signature scent in the lobby to the curated art pieces adorning the walls, every element at Lusso Hotels is designed to evoke a sense of calm and wonder.');
$values_card_link = getPageSection('about', 'values_card_link', 'Read about our wellness');
$values_card_link_href = getPageSection('about', 'values_card_link_href', '/amenities');

$timelineRaw = (string)getPageSection('about', 'timeline_json', '');
$timeline = json_decode($timelineRaw, true);
// Only fall back to defaults when the section is missing/invalid.
// If admin intentionally saves an empty list ([]), keep it empty on the public page.
if (trim($timelineRaw) === '' || !is_array($timeline)) {
    $timeline = $cmsDefaults['about_timeline'];
}

$journey_title_html = getPageSection('about', 'journey_title_html', 'A Historic <span class="font-bold italic text-primary">Journey</span>');
$journey_subtitle = getPageSection('about', 'journey_subtitle', 'Tracing the milestones that shaped our vision of hospitality.');

$team_kicker = getPageSection('about', 'team_kicker', 'Leadership');
$team_heading = getPageSection('about', 'team_heading', 'The Curators');
$team_intro = getPageSection('about', 'team_intro', 'Meet the visionaries dedicated to crafting your perfect stay.');
$teamRaw = (string)getPageSection('about', 'team_json', '');
$team = json_decode($teamRaw, true);
// Only fall back to defaults when the section is missing/invalid.
// If admin intentionally saves an empty list ([]), keep it empty on the public page.
if (trim($teamRaw) === '' || !is_array($team)) {
    $team = $cmsDefaults['about_team'];
}

// Normalize team_json to an images-only gallery array.
// Supports:
// - New format: ["path1","path2",...]
// - Legacy format: [{image:"..."}, ...]
$teamImages = [];
if (is_array($team) && isset($team[0]) && is_string($team[0])) {
    $teamImages = array_values(array_filter(array_map(static function ($src) {
        $src = is_string($src) ? trim($src) : '';
        return $src !== '' ? $src : null;
    }, $team)));
} elseif (is_array($team)) {
    $teamImages = array_values(array_filter(array_map(static function ($row) {
        if (!is_array($row)) return null;
        $src = (string)($row['image'] ?? ($row['src'] ?? ''));
        $src = trim($src);
        return $src !== '' ? $src : null;
    }, $team)));
}

$parallax_bg = getPageSection('about', 'parallax_bg', 'https://lh3.googleusercontent.com/aida-public/AB6AXuD3CpM9PF0quzu5ENNbyfrW4zTzCEMO_H7AEdFnIbDMIhurw-MN5oG3CYu33yyX74nXm8XqyQ5rWuDUK1LqHo3YeVaAe44npBDrPxoJGJWPJcCt3loAI3ZdpZJTxEJxAGbs_PGZ1BEhCN76N2fSJuaomMfPIYYOx3btJ8FOZQRmrtxs0FQOI0OZJPPLI5WoBwzJ_pl1gq96qrcFCdOdsgIzrVnxyfqS_Zk71pTDD1FaNXjggESLZ5KhoJfqv-Q2WqqyBwV2KxvS-n8');
$parallax_quote = getPageSection('about', 'parallax_quote', '"Where elegance meets heritage."');

$cta_title = getPageSection('about', 'cta_title', 'Ready to Experience the Legend?');
$cta_body = getPageSection('about', 'cta_body', 'Your journey into the extraordinary begins here. Reserve your sanctuary in Abuja today.');
$cta_btn1 = getPageSection('about', 'cta_btn1', 'Check Availability');
$cta_btn1_href = getPageSection('about', 'cta_btn1_href', '/rooms');
$cta_btn2 = getPageSection('about', 'cta_btn2', 'Contact Concierge');
$cta_btn2_href = getPageSection('about', 'cta_btn2_href', '/contact');
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title><?= e($pageTitle) ?></title>
  <?php require_once __DIR__ . '/includes/head-header.php'; ?>
</head>
<body class="bg-background-light dark:bg-background-dark text-text-main dark:text-background-light font-display antialiased overflow-x-hidden selection:bg-primary selection:text-white">
<?php require_once __DIR__ . '/includes/header.php'; ?>

<section class="relative min-h-screen w-full flex items-center justify-center overflow-hidden">
  <div class="absolute inset-0 z-0">
    <div class="absolute inset-0 z-10" style="background: rgba(107, 51, 39, 0.72);"></div>
    <div class="w-full h-full bg-cover bg-center bg-no-repeat scale-105" style='background-image: url("<?= e($hero_bg) ?>");'>
    </div>
  </div>
  <div class="relative z-20 container mx-auto px-6 lg:px-12 flex flex-col items-center text-center pt-20">
    <span class="text-white/90 text-sm uppercase tracking-[0.2em] font-bold mb-4 animate-[fadeIn_1s_ease-out]"><?= e($hero_established) ?></span>
    <h1 class="text-white text-5xl md:text-7xl lg:text-8xl font-light tracking-tight leading-[1.1] mb-8 max-w-4xl drop-shadow-2xl">
      <?= $hero_title_html ?>
    </h1>
    <p class="text-gray-200 text-lg md:text-xl font-light max-w-xl leading-relaxed mb-10 opacity-90">
      <?= e($hero_subtitle) ?>
    </p>
    <div class="h-16 w-[1px] bg-gradient-to-b from-primary to-transparent"></div>
  </div>
</section>

<section class="py-[26px] relative bg-background-light dark:bg-background-dark">
  <div class="container mx-auto px-6 lg:px-12">
    <div class="flex flex-col lg:flex-row items-stretch lg:items-center gap-12 lg:gap-24">
      <div class="w-full lg:w-1/2 flex flex-col gap-8 min-w-0">
        <h2 class="text-4xl md:text-5xl font-light text-text-main dark:text-white leading-tight">
          <?= $story_title_html ?>
        </h2>
        <div class="w-20 h-[2px] bg-primary/30"></div>
        <p class="text-lg text-gray-600 dark:text-gray-300 leading-loose font-light"><?= e($story_p1) ?></p>
        <p class="text-lg text-gray-600 dark:text-gray-300 leading-loose font-light"><?= e($story_p2) ?></p>
      </div>
      <div class="w-full lg:w-1/2 relative min-w-0">
        <div class="aspect-[4/5] rounded-lg overflow-hidden relative shadow-2xl w-full">
          <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style='background-image: url("<?= e($story_image) ?>");'></div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="py-24 lg:py-32 relative bg-texture-pattern bg-fixed">
  <div class="container mx-auto px-6 lg:px-12 relative">
    <div class="text-center mb-20">
      <span class="text-primary text-xs font-bold tracking-widest uppercase mb-3 block"><?= e($values_kicker) ?></span>
      <h3 class="text-3xl md:text-4xl font-bold text-text-main dark:text-white"><?= e($values_title) ?></h3>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-14 items-center">
      <div class="relative z-10 min-w-0">
        <div class="relative aspect-[16/9] w-full rounded-lg overflow-hidden shadow-2xl">
          <div class="absolute inset-0 bg-cover bg-center bg-no-repeat transition-transform duration-700 hover:scale-105" style='background-image: url("<?= e($values_image) ?>");'></div>
        </div>
      </div>
      <div class="relative z-20 min-w-0">
        <div class="bg-white dark:bg-surface-dark p-10 md:p-14 rounded-lg shadow-2xl border border-gray-100 dark:border-white/5">
          <span class="material-symbols-outlined text-primary text-4xl mb-6"><?= e($values_card_icon) ?></span>
          <h4 class="text-2xl font-bold mb-4 text-text-main dark:text-white"><?= e($values_card_title) ?></h4>
          <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-6 font-light"><?= e($values_card_body) ?></p>
          <a class="inline-flex items-center gap-2 text-primary font-semibold text-sm hover:underline" href="<?= e(lusso_href((string)$values_card_link_href)) ?>">
            <?= e($values_card_link) ?>
            <span class="material-symbols-outlined text-sm">arrow_forward</span>
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="py-24 bg-white dark:bg-surface-dark relative overflow-hidden">
  <div class="absolute top-0 right-0 w-1/3 h-full bg-primary/5 -skew-x-12 z-0"></div>
  <div class="container mx-auto px-6 lg:px-12 relative z-10">
    <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
      <div class="max-w-xl">
        <h2 class="text-4xl md:text-5xl font-light text-text-main dark:text-white mb-6"><?= $journey_title_html ?></h2>
        <p class="text-gray-600 dark:text-gray-300 text-lg font-light"><?= e($journey_subtitle) ?></p>
      </div>
    </div>
    <div class="relative mt-20">
      <div class="absolute top-[28px] left-0 w-full h-[1px] bg-gray-200 dark:bg-white/10"></div>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <?php foreach ($timeline as $item):
          $year = (string)($item['year'] ?? '');
          $kind = (string)($item['kind'] ?? 'dot');
          $t = (string)($item['title'] ?? '');
          $b = (string)($item['body'] ?? '');
          if ($kind === 'circle'): ?>
        <div class="relative group">
          <div class="size-14 rounded-full bg-background-light dark:bg-background-dark border-2 border-primary flex items-center justify-center relative z-10 mb-6 group-hover:scale-110 transition-transform duration-300">
            <span class="text-sm font-bold"><?= e($year) ?></span>
          </div>
          <h4 class="text-xl font-bold mb-2 dark:text-white"><?= e($t) ?></h4>
          <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed"><?= e($b) ?></p>
        </div>
          <?php elseif ($kind === 'dot_primary'): ?>
        <div class="relative group">
          <div class="size-4 rounded-full bg-primary mb-11 mt-[20px] relative z-10 shadow-[0_0_18px_rgba(65,29,19,0.45)]"></div>
          <div class="border-l border-gray-200 dark:border-white/10 pl-4 md:pl-0 md:border-l-0">
            <span class="text-xs font-bold text-primary mb-2 block"><?= e($year) ?></span>
            <h4 class="text-xl font-bold mb-2 dark:text-white"><?= e($t) ?></h4>
            <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed"><?= e($b) ?></p>
          </div>
        </div>
          <?php else: /* dot */ ?>
        <div class="relative group">
          <div class="size-4 rounded-full bg-gray-300 dark:bg-gray-600 mb-11 mt-[20px] relative z-10 group-hover:bg-primary transition-colors"></div>
          <div class="border-l border-gray-200 dark:border-white/10 pl-4 md:pl-0 md:border-l-0">
            <span class="text-xs font-bold text-gray-400 mb-2 block"><?= e($year) ?></span>
            <h4 class="text-xl font-bold mb-2 dark:text-white"><?= e($t) ?></h4>
            <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed"><?= e($b) ?></p>
          </div>
        </div>
          <?php endif;
        endforeach; ?>
      </div>
    </div>
  </div>
</section>

<?php if (!empty($teamImages)): ?>
<section class="py-24 lg:py-32 bg-background-light dark:bg-background-dark">
  <div class="container mx-auto px-6 lg:px-12">
    <div class="flex flex-col items-center text-center mb-16">
      <span class="text-primary text-xs font-bold tracking-widest uppercase mb-3"><?= e($team_kicker) ?></span>
      <h2 class="text-4xl font-light text-text-main dark:text-white"><?= e($team_heading) ?></h2>
      <p class="text-gray-500 dark:text-gray-400 mt-4 max-w-2xl font-light"><?= e($team_intro) ?></p>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
      <?php foreach ($teamImages as $src):
        $url = lusso_media_src((string)$src);
        ?>
      <div class="group">
        <div class="aspect-square w-full overflow-hidden rounded-lg relative border border-black/[0.06] bg-gray-100">
          <img src="<?= e($url) ?>" alt="" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" loading="lazy" decoding="async">
          <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-black/0 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="w-full h-[400px] md:h-[600px] relative bg-fixed bg-center bg-cover" style='background-image: url("<?= e($parallax_bg) ?>");'>
  <div class="absolute inset-0 bg-black/30 flex items-center justify-center">
    <h2 class="text-white text-3xl md:text-5xl font-light italic tracking-tight"><?= e($parallax_quote) ?></h2>
  </div>
</section>

<section class="py-10 bg-surface-ink text-white">
  <div class="container mx-auto px-6 lg:px-12 text-center">
    <div class="max-w-3xl mx-auto flex flex-col items-center gap-8">
      <span class="material-symbols-outlined text-champagne text-5xl">diamond</span>
      <h2 class="text-4xl md:text-5xl font-light leading-tight"><?= e($cta_title) ?></h2>
      <p class="text-gray-400 text-lg leading-relaxed"><?= e($cta_body) ?></p>
      <div class="flex flex-row flex-nowrap gap-2 sm:gap-4 w-full max-w-xl mx-auto justify-center pt-4 min-w-0">
        <a href="<?= e(lusso_href((string)$cta_btn1_href)) ?>" class="flex-1 min-w-0 h-12 sm:h-14 px-4 sm:px-10 bg-primary text-white font-bold text-sm sm:text-base rounded-lg hover:bg-primary-light transition-all duration-300 inline-flex items-center justify-center text-center leading-tight shadow-lg shadow-primary/30"><?= e($cta_btn1) ?></a>
        <a href="<?= e(lusso_href((string)$cta_btn2_href)) ?>" class="flex-1 min-w-0 h-12 sm:h-14 px-4 sm:px-10 bg-transparent border border-white/20 text-white font-bold text-sm sm:text-base rounded-lg hover:bg-white/10 transition-all duration-300 inline-flex items-center justify-center text-center leading-tight"><?= e($cta_btn2) ?></a>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
