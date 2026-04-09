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

/**
 * Social platform helpers (admin saves {platform,url}; legacy may have {icon,url}).
 */
function lusso_social_platform_from_url($url) {
    $u = strtolower((string)$url);
    if (strpos($u, 'instagram.com') !== false) return 'instagram';
    if (strpos($u, 'tiktok.com') !== false) return 'tiktok';
    if (strpos($u, 'twitter.com') !== false || strpos($u, 'x.com') !== false) return 'x';
    if (strpos($u, 'facebook.com') !== false || strpos($u, 'fb.com') !== false) return 'facebook';
    return '';
}
function lusso_social_normalize_platform($p) {
    $v = strtolower(trim((string)$p));
    if ($v === 'twitter' || $v === 'x-twitter') return 'x';
    if ($v === 'ig') return 'instagram';
    return $v;
}
function lusso_social_svg($platform) {
    $p = lusso_social_normalize_platform($platform);
    $cls = 'w-6 h-6';
    if ($p === 'facebook') {
        return '<svg class="'.$cls.'" viewBox="0 0 24 24" aria-hidden="true" fill="currentColor"><path d="M22 12.06C22 6.504 17.523 2 12 2S2 6.504 2 12.06C2 17.08 5.657 21.245 10.438 22v-7.03H7.898v-2.91h2.54V9.845c0-2.522 1.492-3.915 3.777-3.915 1.094 0 2.238.196 2.238.196v2.476h-1.26c-1.242 0-1.63.776-1.63 1.57v1.888h2.773l-.443 2.91h-2.33V22C18.343 21.245 22 17.08 22 12.06z"/></svg>';
    }
    if ($p === 'instagram') {
        return '<svg class="'.$cls.'" viewBox="0 0 24 24" aria-hidden="true" fill="currentColor"><path d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5zm10 2H7a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3z"/><path d="M12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10zm0 2a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/><path d="M17.5 6.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/></svg>';
    }
    if ($p === 'tiktok') {
        // Simple TikTok note mark (monochrome, works with currentColor)
        return '<svg class="'.$cls.'" viewBox="0 0 24 24" aria-hidden="true" fill="currentColor"><path d="M14 3v10.2a3.8 3.8 0 1 1-2.4-3.55V6.1c0-.6.5-1.1 1.1-1.1H14z"/><path d="M14 3c.9 2.6 2.8 4.3 5 4.6v2.3c-2-.1-3.8-.9-5-2.1V3z"/></svg>';
    }
    if ($p === 'x') {
        return '<svg class="'.$cls.'" viewBox="0 0 24 24" aria-hidden="true" fill="currentColor"><path d="M18.9 2H22l-6.8 7.8L23 22h-6.2l-4.8-6.6L6 22H3l7.3-8.4L1 2h6.4l4.3 6L18.9 2zm-1.1 18h1.7L7.3 3.9H5.5L17.8 20z"/></svg>';
    }
    return '';
}

$hotelPolicySlug = 'hotel-policy';
$privacyPolicySlug = 'privacy-policy';
$termsSlug = 'terms-and-conditions';
?>

<!-- Footer -->
<footer class="bg-primary text-background-light pt-[15px] pb-[15px]">
  <div class="max-w-[1280px] mx-auto px-6 lg:px-12">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-12 lg:gap-16 mb-16">
      <!-- Brand (logo + socials) -->
      <div class="min-w-0<?= empty($socialMediaList) ? ' h-[254px]' : '' ?>">
        <div class="lusso-brand-logo lusso-brand-logo--footer flex items-center justify-center md:justify-start mb-8 md:mb-10 min-h-[5rem] md:min-h-[7.5rem] py-2 md:py-3">
          <?php if ($useFooterLogo): ?>
          <img src="<?= e($siteLogoLightUrl) ?>" alt="<?= e($siteName) ?>" class="h-[206px] w-auto md:h-[120px] lg:h-[144px] max-w-[min(100%,32rem)] object-contain object-center md:object-left" decoding="async"/>
          <?php else: ?>
          <span class="material-symbols-outlined text-champagne text-2xl">diamond</span>
          <span class="font-serif text-xl font-bold tracking-tight text-background-light"><?= e($siteName) ?></span>
          <?php endif; ?>
        </div>
        <?php if (!empty($socialMediaList)): ?>
        <div class="flex gap-4">
          <?php foreach ($socialMediaList as $social): ?>
            <?php
              $url = (string)($social['url'] ?? '');
              $platform = (string)($social['platform'] ?? '');
              if ($platform === '') {
                  $platform = lusso_social_platform_from_url($url);
              }
              $platform = lusso_social_normalize_platform($platform);
              $svg = $platform !== '' ? lusso_social_svg($platform) : '';
            ?>
            <?php if ($url !== '' && $svg !== ''): ?>
          <a class="text-background-light/55 hover:text-champagne transition-colors"
             href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>"
             target="_blank" rel="noopener noreferrer"
             aria-label="<?= htmlspecialchars(ucfirst($platform), ENT_QUOTES, 'UTF-8') ?>">
            <?= $svg ?>
          </a>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

      <!-- Brand description (2nd column) -->
      <div class="min-w-0">
        <h4 class="font-serif text-lg mb-6 text-champagne">About</h4>
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
      <div class="flex flex-wrap items-center justify-center md:justify-start gap-x-2 gap-y-2">
        <p class="whitespace-nowrap"><?= e($footerCopyright) ?></p>

        <?php if (lusso_public_page_exists($hotelPolicySlug)): ?>
          <span class="mx-1 text-background-light/40">|</span>
          <a class="hover:text-champagne transition-colors whitespace-nowrap" href="<?= e(lusso_url($hotelPolicySlug)) ?>">Hotel Policy</a>
        <?php endif; ?>

        <?php if (lusso_public_page_exists($privacyPolicySlug)): ?>
          <span class="mx-1 text-background-light/40">|</span>
          <a class="hover:text-champagne transition-colors whitespace-nowrap" href="<?= e(lusso_url($privacyPolicySlug)) ?>">Privacy Policy</a>
        <?php endif; ?>

        <?php if (lusso_public_page_exists($termsSlug)): ?>
          <span class="mx-1 text-background-light/40">|</span>
          <a class="hover:text-champagne transition-colors whitespace-nowrap" href="<?= e(lusso_url($termsSlug)) ?>">Terms &amp; Conditions</a>
        <?php endif; ?>
      </div>
      <div class="flex gap-6">
        <a class="hover:text-champagne transition-colors" href="https://signature-solutions.com/" target="_blank" rel="noopener noreferrer">Designed By Signature Solutions</a>
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

