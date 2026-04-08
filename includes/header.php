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

$navHomeHref = lusso_href('/');
$navAboutHref = lusso_href('/about');
$navSuitesHref = lusso_href(getSiteSetting('nav_suites_href', '/rooms'));
$navDiningHref = lusso_href(getSiteSetting('nav_dining_href', '/dining'));
$navExperienceHref = lusso_href(getSiteSetting('nav_experience_href', '/amenities'));
$navEventsHref = lusso_href(getSiteSetting('nav_events_href', '/gallery'));
$navContactHref = lusso_href('/contact');

$ctaLabel = getSiteSetting('nav_cta_label', 'Check Availability');
$ctaHref = lusso_href(getSiteSetting('nav_cta_href', '/rooms'));

$headerNavLinks = [
    ['Home', $navHomeHref],
    ['About Us', $navAboutHref],
    [$navSuitesLabel, $navSuitesHref],
    [$navDiningLabel, $navDiningHref],
    [$navExperienceLabel, $navExperienceHref],
    [$navEventsLabel, $navEventsHref],
    ['Contact Us', $navContactHref],
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
      <img src="<?= e($siteLogoDarkUrl) ?>" alt="<?= e($siteName) ?>" class="h-11 w-auto md:h-14 lg:h-16 max-w-[min(100%,18rem)] object-contain object-left" decoding="async"/>
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
      <a class="hidden md:inline-flex items-center justify-center shrink min-w-0 max-w-[10.5rem] sm:max-w-none bg-primary text-white hover:bg-primary-light transition-all px-3 py-2 sm:px-6 sm:py-2.5 rounded-full text-xs sm:text-sm font-bold tracking-wide text-center leading-tight" href="<?= e(lusso_href($ctaHref)) ?>">
        <?= e($ctaLabel) ?>
      </a>
      <?php endif; ?>
      <button class="md:hidden p-2 rounded-xl text-text-main hover:bg-black/[0.04] focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/30" type="button" id="lussoMobileMenuBtn" aria-label="Open menu" aria-expanded="false" aria-controls="lussoMobileMenuOverlay">
        <span class="material-symbols-outlined text-3xl">menu</span>
      </button>
    </div>
  </div>
</nav>

<!-- Mobile menu: centered modal (not sidebar) -->
<div id="lussoMobileMenuOverlay" class="lusso-mobile-menu-overlay fixed inset-0 z-[60] bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 md:hidden" aria-hidden="true">
  <div id="lussoMobileMenuModal" class="lusso-mobile-menu-modal w-full max-w-sm bg-background-light rounded-2xl shadow-2xl border border-black/[0.08] overflow-hidden max-h-[90vh] flex flex-col">
    <div class="flex items-center justify-between px-5 py-4 border-b border-black/[0.08] shrink-0">
      <span class="font-serif text-lg font-semibold text-text-main"><?= e($siteName) ?></span>
      <button type="button" id="lussoMobileMenuClose" class="p-2 rounded-xl text-text-muted hover:bg-black/[0.05] hover:text-text-main focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/30" aria-label="Close menu">
        <span class="material-symbols-outlined text-2xl">close</span>
      </button>
    </div>
    <nav class="flex flex-col p-3 gap-0.5 overflow-y-auto" aria-label="Mobile">
      <?php foreach ($headerNavLinks as $navRow):
        [$navLabel, $navHref] = $navRow;
        if (!lusso_is_valid_site_nav_href((string)$navHref)) {
            continue;
        } ?>
      <a class="lusso-mobile-menu-link px-4 py-3.5 rounded-xl text-text-main font-medium hover:bg-primary/10 hover:text-primary transition-colors" href="<?= e(lusso_href((string)$navHref)) ?>"><?= e((string)$navLabel) ?></a>
      <?php endforeach; ?>
      <?php if ($showNavCta): ?>
      <a class="lusso-mobile-menu-link mt-2 mx-1 px-4 py-3.5 rounded-xl bg-primary text-white font-bold text-center hover:bg-primary-light transition-colors" href="<?= e(lusso_href($ctaHref)) ?>"><?= e($ctaLabel) ?></a>
      <?php endif; ?>
    </nav>
  </div>
</div>
<script>
(function () {
  var overlay = document.getElementById('lussoMobileMenuOverlay');
  var openBtn = document.getElementById('lussoMobileMenuBtn');
  var closeBtn = document.getElementById('lussoMobileMenuClose');
  var modal = document.getElementById('lussoMobileMenuModal');
  function openMenu() {
    if (!overlay) return;
    overlay.classList.add('open');
    overlay.setAttribute('aria-hidden', 'false');
    if (openBtn) openBtn.setAttribute('aria-expanded', 'true');
    document.body.style.overflow = 'hidden';
  }
  function closeMenu() {
    if (!overlay) return;
    overlay.classList.remove('open');
    overlay.setAttribute('aria-hidden', 'true');
    if (openBtn) openBtn.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
  }
  if (openBtn) openBtn.addEventListener('click', openMenu);
  if (closeBtn) closeBtn.addEventListener('click', closeMenu);
  if (overlay) {
    overlay.addEventListener('click', function (e) {
      if (e.target === overlay) closeMenu();
    });
  }
  if (modal) {
    modal.addEventListener('click', function (e) {
      e.stopPropagation();
    });
  }
  var links = document.querySelectorAll('.lusso-mobile-menu-link');
  for (var i = 0; i < links.length; i++) {
    links[i].addEventListener('click', closeMenu);
  }
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && overlay && overlay.classList.contains('open')) closeMenu();
  });
})();
</script>

