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
/** Dark / full-color logo for light backgrounds (header): CMS or assets/images/logo/logo-dark.png */
$siteLogoDarkPath = lusso_brand_logo_path((string)getSiteSetting('site_logo', ''), 'assets/images/logo/logo-dark.png');
$siteLogoDarkUrl = $siteLogoDarkPath !== '' ? lusso_media_src($siteLogoDarkPath) : '';
$useHeaderLogo = $siteLogoDarkUrl !== '';
$navSuitesLabel = getSiteSetting('nav_suites_label', 'Suites');
$navDiningLabel = getSiteSetting('nav_dining_label', 'Dining');
$navExperienceLabel = getSiteSetting('nav_experience_label', 'Amenities');
$navEventsLabel = getSiteSetting('nav_events_label', 'Gallery');

$navSuitesHref = lusso_href(getSiteSetting('nav_suites_href', '/rooms'));
$navDiningHref = lusso_href(getSiteSetting('nav_dining_href', '/dining'));
$navExperienceHref = lusso_href(getSiteSetting('nav_experience_href', '/amenities'));
$navEventsHref = lusso_href(getSiteSetting('nav_events_href', '/gallery'));

$ctaLabel = getSiteSetting('nav_cta_label', 'Book Your Stay');
$ctaHref = lusso_href(getSiteSetting('nav_cta_href', '/contact'));

$headerNavLinks = [
    [$navSuitesLabel, $navSuitesHref],
    [$navDiningLabel, $navDiningHref],
    [$navExperienceLabel, $navExperienceHref],
    [$navEventsLabel, $navEventsHref],
];
$headerNavLinks = array_values(array_filter($headerNavLinks, static function ($row) {
    return lusso_is_valid_site_nav_href((string)$row[1]);
}));

$showNavCta = lusso_is_valid_site_nav_href($ctaHref);
?>

<!-- Sticky Navigation — light background: use dark logo variant (brand guidelines) -->
<nav class="sticky top-0 z-50 w-full transition-all duration-300 bg-background-light/95 backdrop-blur-md border-b border-black/[0.06]">
  <div class="max-w-[1440px] mx-auto px-6 lg:px-12 min-h-[5rem] flex items-center justify-between gap-4">
    <a class="lusso-brand-logo lusso-brand-logo--header flex items-center shrink-0 rounded-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/30 py-3 ps-1 pe-3 md:py-4 md:ps-2 md:pe-5 lg:pe-8 -ms-1 md:-ms-2" href="<?= e(lusso_url('index')) ?>" aria-label="<?= e($siteName) ?>">
      <?php if ($useHeaderLogo): ?>
      <img src="<?= e($siteLogoDarkUrl) ?>" alt="<?= e($siteName) ?>" class="h-[40px] w-auto md:h-[48px] lg:h-[60px] max-w-[min(100%,14rem)] object-contain object-left" decoding="async"/>
      <?php else: ?>
      <span class="material-symbols-outlined text-primary text-3xl">diamond</span>
      <span class="font-serif text-2xl font-bold tracking-tight text-text-main"><?= e($siteName) ?></span>
      <?php endif; ?>
    </a>
    <div class="hidden md:flex items-center gap-10">
      <?php foreach ($headerNavLinks as $navRow):
        [$navLabel, $navHref] = $navRow; ?>
      <a class="text-sm font-medium text-text-main hover:text-primary transition-colors" href="<?= e(lusso_href((string)$navHref)) ?>"><?= e((string)$navLabel) ?></a>
      <?php endforeach; ?>
    </div>
    <div class="flex items-center gap-4">
      <?php if ($showNavCta): ?>
      <a class="hidden md:flex bg-primary text-white hover:bg-primary-light transition-all px-6 py-2.5 rounded-full text-sm font-bold tracking-wide" href="<?= e(lusso_href($ctaHref)) ?>">
        <?= e($ctaLabel) ?>
      </a>
      <?php endif; ?>
      <button class="md:hidden p-2 text-text-main" type="button" aria-label="Menu">
        <span class="material-symbols-outlined text-2xl">menu</span>
      </button>
    </div>
  </div>
</nav>

