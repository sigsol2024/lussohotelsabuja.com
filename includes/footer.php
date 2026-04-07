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
    <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-16">
      <!-- Brand -->
      <div class="col-span-1 md:col-span-1">
        <div class="flex items-center gap-2 mb-6">
          <span class="material-symbols-outlined text-champagne text-2xl">diamond</span>
          <span class="font-serif text-xl font-bold tracking-tight text-background-light"><?= e($siteName) ?></span>
        </div>
        <p class="text-background-light/70 text-sm leading-relaxed mb-6">
          <?= e($footerTagline) ?>
        </p>
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

      <!-- Links -->
      <div>
        <h4 class="font-serif text-lg mb-6 text-champagne">Explore</h4>
        <ul class="space-y-3 text-sm text-background-light/80">
          <li><a class="hover:text-champagne transition-colors" href="<?= e(lusso_url('about')) ?>">Our Story</a></li>
          <li><a class="hover:text-champagne transition-colors" href="<?= e(lusso_url('rooms')) ?>">Suites &amp; Rooms</a></li>
          <li><a class="hover:text-champagne transition-colors" href="<?= e(lusso_url('dining')) ?>">Dining</a></li>
          <li><a class="hover:text-champagne transition-colors" href="<?= e(lusso_url('amenities')) ?>">Wellness</a></li>
        </ul>
      </div>

      <!-- Contact -->
      <div>
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

      <!-- Newsletter (placeholder form) -->
      <div>
        <h4 class="font-serif text-lg mb-6 text-champagne">Newsletter</h4>
        <p class="text-background-light/65 text-sm mb-4">Subscribe for exclusive offers.</p>
        <div class="flex border-b border-background-light/25 pb-2">
          <input class="bg-transparent border-none text-background-light placeholder-background-light/45 focus:ring-0 w-full p-0 text-sm" placeholder="Your email address" type="email"/>
          <button class="text-champagne uppercase text-xs font-bold tracking-widest hover:text-white transition-colors" type="button">Join</button>
        </div>
      </div>
    </div>

    <!-- Bottom -->
    <div class="border-t border-background-light/15 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-background-light/50">
      <p><?= e($footerCopyright) ?></p>
      <div class="flex gap-6">
        <a class="hover:text-champagne transition-colors" href="<?= e(lusso_href($privacyHref)) ?>">Privacy Policy</a>
        <a class="hover:text-champagne transition-colors" href="<?= e(lusso_href($termsHref)) ?>">Terms of Service</a>
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

