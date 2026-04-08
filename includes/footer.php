<?php
/**
 * Lusso shared footer.
 * Requires: content-loader.php included before this file.
 */

if (!function_exists('getSiteSetting')) {
    function getSiteSetting($key, $default = '') { return $default; }
}
if (!function_exists('e')) {
    function e($string) { return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8'); }
}

$siteName = getSiteSetting('site_name', 'LUSSO');
/** Light / reversed logo for dark primary footer: CMS or assets/images/logo/logo-light.png — do not reuse dark header logo here */
$siteLogoLightPath = lusso_brand_logo_path((string)getSiteSetting('site_logo_light', ''), 'assets/images/logo/logo-light.png');
$siteLogoLightUrl = $siteLogoLightPath !== '' ? lusso_media_src($siteLogoLightPath) : '';
$useFooterLogo = $siteLogoLightUrl !== '';
$footerTagline = getSiteSetting('footer_tagline', 'Refining the art of hospitality in the heart of Abuja.');
$footerAddress = getSiteSetting('footer_address', "15 Luxury Avenue,\nMaitama, Abuja");
$footerPhone = getSiteSetting('footer_phone', '+234 800 LUSSO 00');
$footerEmail = getSiteSetting('footer_email', 'concierge@lusso.com');
$footerCopyright = getSiteSetting('footer_copyright', '© 2024 Lusso Hotels. All rights reserved.');

$socialMediaJson = getSiteSetting('social_media_json', '[]');
$socialMediaList = json_decode($socialMediaJson, true);
if (!is_array($socialMediaList)) {
    $socialMediaList = [];
}

$privacyHref = getSiteSetting('footer_privacy_href', '#');
$termsHref = getSiteSetting('footer_terms_href', '#');
?>

<!-- Footer -->
<footer class="bg-primary text-background-light pt-20 pb-10">
  <div class="max-w-[1280px] mx-auto px-6 lg:px-12">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-12 lg:gap-16 mb-16">
      <!-- Brand (logo + socials) -->
      <div class="min-w-0">
        <div class="lusso-brand-logo lusso-brand-logo--footer flex items-center mb-8 md:mb-10 min-h-[5rem] md:min-h-[7.5rem] py-2 pe-4 md:py-3 md:pe-6">
          <?php if ($useFooterLogo): ?>
          <img src="<?= e($siteLogoLightUrl) ?>" alt="<?= e($siteName) ?>" class="h-[104px] w-auto md:h-[120px] lg:h-[144px] max-w-[min(100%,32rem)] object-contain object-left" decoding="async"/>
          <?php else: ?>
          <span class="material-symbols-outlined text-champagne text-2xl">diamond</span>
          <span class="font-serif text-xl font-bold tracking-tight text-background-light"><?= e($siteName) ?></span>
          <?php endif; ?>
        </div>
        <?php if (!empty($socialMediaList)): ?>
        <div class="flex gap-4">
          <?php foreach ($socialMediaList as $social): ?>
            <?php if (!empty($social['icon']) && !empty($social['url'])): ?>
          <a class="text-background-light/55 hover:text-champagne transition-colors"
             href="<?= htmlspecialchars((string)$social['url'], ENT_QUOTES, 'UTF-8') ?>"
             target="_blank" rel="noopener noreferrer"
             aria-label="Social link">
            <span class="material-symbols-outlined"><?= htmlspecialchars((string)$social['icon'], ENT_QUOTES, 'UTF-8') ?></span>
          </a>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

      <!-- Brand description (2nd column) -->
      <div class="min-w-0">
        <p class="text-background-light/70 text-sm leading-relaxed">
          <?= e($footerTagline) ?>
        </p>
      </div>

      <!-- Links: only routes that have a real page at site root -->
      <div class="min-w-0">
        <h4 class="font-serif text-lg mb-6 text-champagne">Explore</h4>
        <ul class="space-y-3 text-sm text-background-light/80">
          <?php
          $footerExplore = [
              ['about', 'Our Story'],
              ['rooms', 'Suites & Rooms'],
              ['dining', 'Dining'],
              ['amenities', 'Amenities'],
              ['gallery', 'Gallery'],
              ['contact', 'Contact'],
          ];
          foreach ($footerExplore as $fe):
              [$slug, $lbl] = $fe;
              if (!lusso_public_page_exists($slug)) {
                  continue;
              }
              ?>
          <li><a class="hover:text-champagne transition-colors" href="<?= e(lusso_url($slug)) ?>"><?= e($lbl) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <!-- Contact -->
      <div class="min-w-0">
        <h4 class="font-serif text-lg mb-6 text-champagne">Contact</h4>
        <ul class="space-y-3 text-sm text-background-light/80">
          <li class="flex items-start gap-3">
            <span class="material-symbols-outlined text-sm mt-1">location_on</span>
            <span><?= nl2br(e($footerAddress)) ?></span>
          </li>
          <li class="flex items-center gap-3">
            <span class="material-symbols-outlined text-sm">call</span>
            <span><?= e($footerPhone) ?></span>
          </li>
          <li class="flex items-center gap-3">
            <span class="material-symbols-outlined text-sm">mail</span>
            <span><?= e($footerEmail) ?></span>
          </li>
        </ul>
      </div>
    </div>

    <!-- Bottom -->
    <div class="border-t border-background-light/15 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-background-light/50">
      <p><?= e($footerCopyright) ?></p>
      <div class="flex gap-6">
        <a class="hover:text-champagne transition-colors" href="https://signature-solutions.com/" target="_blank" rel="noopener noreferrer">Designed By Signature Solutions</a>
        <?php if (lusso_is_valid_site_nav_href((string)$privacyHref)): ?>
        <a class="hover:text-champagne transition-colors" href="<?= e(lusso_href($privacyHref)) ?>">Privacy Policy</a>
        <?php endif; ?>
        <?php if (lusso_is_valid_site_nav_href((string)$termsHref)): ?>
        <a class="hover:text-champagne transition-colors" href="<?= e(lusso_href($termsHref)) ?>">Terms of Service</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</footer>

<?php
// Optional: site-wide injected footer scripts
if (function_exists('getSiteSetting')) {
    $footerScripts = getSiteSetting('footer_scripts', '');
    if (!empty($footerScripts)) {
        echo "\n<!-- Custom Footer Scripts -->\n";
        echo $footerScripts . "\n";
    }
}
?>
</body></html>

