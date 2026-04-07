<?php
/**
 * Lusso shared header / navigation.
 * Requires: content-loader.php included before this file.
 */

if (!function_exists('getSiteSetting')) {
    function getSiteSetting($key, $default = '') { return $default; }
}
if (!function_exists('e')) {
    function e($string) { return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8'); }
}

// Optional: site-wide injected body scripts
if (function_exists('getSiteSetting')) {
    $bodyScripts = getSiteSetting('body_scripts', '');
    if (!empty($bodyScripts)) {
        echo "\n<!-- Custom Body Scripts -->\n";
        echo $bodyScripts . "\n";
    }
}

$siteName = getSiteSetting('site_name', 'Lusso Hotels Abuja');
$navSuitesLabel = getSiteSetting('nav_suites_label', 'Suites');
$navDiningLabel = getSiteSetting('nav_dining_label', 'Dining');
$navExperienceLabel = getSiteSetting('nav_experience_label', 'Experience');
$navEventsLabel = getSiteSetting('nav_events_label', 'Events');

$navSuitesHref = getSiteSetting('nav_suites_href', 'rooms.php');
$navDiningHref = getSiteSetting('nav_dining_href', 'dining.php');
$navExperienceHref = getSiteSetting('nav_experience_href', '#');
$navEventsHref = getSiteSetting('nav_events_href', '#');

$ctaLabel = getSiteSetting('nav_cta_label', 'Book Your Stay');
$ctaHref = getSiteSetting('nav_cta_href', 'contact.php');
?>

<!-- Sticky Navigation -->
<nav class="sticky top-0 z-50 w-full transition-all duration-300 bg-white/95 backdrop-blur-md border-b border-black/[0.06]">
  <div class="max-w-[1440px] mx-auto px-6 lg:px-12 h-20 flex items-center justify-between">
    <a class="flex items-center gap-2" href="index.php" aria-label="<?= e($siteName) ?>">
      <span class="material-symbols-outlined text-primary text-3xl">diamond</span>
      <span class="font-serif text-2xl font-bold tracking-tight text-text-main"><?= e($siteName) ?></span>
    </a>
    <div class="hidden md:flex items-center gap-10">
      <a class="text-sm font-medium text-text-main hover:text-primary transition-colors" href="<?= e($navSuitesHref) ?>"><?= e($navSuitesLabel) ?></a>
      <a class="text-sm font-medium text-text-main hover:text-primary transition-colors" href="<?= e($navDiningHref) ?>"><?= e($navDiningLabel) ?></a>
      <a class="text-sm font-medium text-text-main hover:text-primary transition-colors" href="<?= e($navExperienceHref) ?>"><?= e($navExperienceLabel) ?></a>
      <a class="text-sm font-medium text-text-main hover:text-primary transition-colors" href="<?= e($navEventsHref) ?>"><?= e($navEventsLabel) ?></a>
    </div>
    <div class="flex items-center gap-4">
      <a class="hidden md:flex bg-primary text-white hover:bg-primary-light transition-all px-6 py-2.5 rounded-full text-sm font-bold tracking-wide" href="<?= e($ctaHref) ?>">
        <?= e($ctaLabel) ?>
      </a>
      <button class="md:hidden p-2 text-text-main" type="button" aria-label="Menu">
        <span class="material-symbols-outlined text-2xl">menu</span>
      </button>
    </div>
  </div>
</nav>

