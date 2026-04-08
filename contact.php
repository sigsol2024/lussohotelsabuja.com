<?php
require_once __DIR__ . '/includes/content-loader.php';

$pageTitle = getPageSection('contact', 'page_title', 'Contact Lusso Hotels Abuja');
$intro_kicker = getPageSection('contact', 'intro_kicker', 'Concierge Services');
$intro_title = getPageSection('contact', 'intro_title', 'Get in Touch');
$intro_body = getPageSection('contact', 'intro_body', 'Experience the quiet luxury of Lusso Abuja. Whether planning a stay or an event, we are here to assist with your every request.');
$address_html = getPageSection('contact', 'address_html', "123 Diplomatic Drive<br/>\nCentral Business District,<br/>\nAbuja, Nigeria");
$directions_href = getPageSection('contact', 'directions_href', '#');
$concierge_phone = getPageSection('contact', 'concierge_phone', '+234 800 LUSSO');
$inquiries_email = getPageSection('contact', 'inquiries_email', 'concierge@lussohotels.com');
$map_address = getPageSection('contact', 'map_address', '');
$map_embed_url = getPageSection('contact', 'map_embed_url', '');
$googleMapsApiKey = getSiteSetting('google_maps_api_key', '');

$mapAddressPlain = trim(strip_tags(str_replace(['<br/>', '<br />', '<br>'], ', ', (string)$address_html)));
if ($map_address !== '' && is_string($map_address)) {
    $mapAddressPlain = trim($map_address);
}

$mapUrl = '';
if (trim((string)$map_embed_url) !== '') {
    $mapUrl = trim((string)$map_embed_url);
} elseif (trim((string)$googleMapsApiKey) !== '' && $mapAddressPlain !== '') {
    $mapUrl = 'https://www.google.com/maps/embed/v1/place?key=' . urlencode((string)$googleMapsApiKey) . '&q=' . urlencode($mapAddressPlain);
} elseif ($mapAddressPlain !== '') {
    $mapUrl = 'https://www.google.com/maps?q=' . urlencode($mapAddressPlain) . '&output=embed';
}

$directionsUrl = trim((string)$directions_href);
if ($directionsUrl === '' || $directionsUrl === '#') {
    $directionsUrl = $mapAddressPlain !== ''
        ? ('https://www.google.com/maps/dir/?api=1&destination=' . urlencode($mapAddressPlain))
        : '#';
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
    body.contact-page .text-text-muted { color: #5c5c5c; }
    .map-filter { filter: grayscale(100%) contrast(90%) sepia(10%); }
    .no-scrollbar::-webkit-scrollbar { display: none; }
  </style>
</head>
<body class="contact-page bg-background-light text-text-main font-display antialiased overflow-x-hidden">
<div class="fixed inset-0 pointer-events-none z-0 bg-architectural-pattern opacity-60"></div>
<div class="relative z-10 flex min-h-screen flex-col w-full">
<?php require_once __DIR__ . '/includes/header.php'; ?>

<main class="flex-grow w-full max-w-[1440px] mx-auto px-6 lg:px-20 py-12 lg:py-20">
  <div class="grid grid-cols-1 md:grid-cols-12 gap-16 md:gap-12 lg:gap-24 items-start">
    <div class="md:col-span-5 flex flex-col md:pt-8">
      <div class="mb-10">
        <span class="block text-primary text-xs font-bold uppercase tracking-[0.2em] mb-3"><?= e($intro_kicker) ?></span>
        <h1 class="text-4xl lg:text-6xl font-display font-medium tracking-tight text-text-main mb-6">
          <?= e($intro_title) ?>
        </h1>
        <p class="text-text-muted text-lg font-body font-light leading-relaxed max-w-md">
          <?= e($intro_body) ?>
        </p>
      </div>
      <div class="mt-8 grid grid-cols-1 gap-4 max-w-md">
        <a class="flex items-center justify-between gap-4 bg-white rounded-lg border border-black/[0.06] px-6 py-5 shadow-elevation hover:-translate-y-0.5 hover:shadow-lg transition-all"
           href="tel:<?= e(preg_replace('/[^0-9+]/', '', $concierge_phone)) ?>">
          <div class="flex items-center gap-4 min-w-0">
            <span class="material-symbols-outlined text-primary">call</span>
            <div class="min-w-0">
              <div class="text-xs font-bold uppercase tracking-widest text-text-muted">Call Concierge</div>
              <div class="font-semibold text-text-main truncate"><?= e($concierge_phone) ?></div>
            </div>
          </div>
          <span class="material-symbols-outlined text-text-muted">arrow_forward</span>
        </a>

        <a class="flex items-center justify-between gap-4 bg-white rounded-lg border border-black/[0.06] px-6 py-5 shadow-elevation hover:-translate-y-0.5 hover:shadow-lg transition-all"
           href="mailto:<?= e($inquiries_email) ?>">
          <div class="flex items-center gap-4 min-w-0">
            <span class="material-symbols-outlined text-primary">mail</span>
            <div class="min-w-0">
              <div class="text-xs font-bold uppercase tracking-widest text-text-muted">Email Inquiries</div>
              <div class="font-semibold text-text-main truncate"><?= e($inquiries_email) ?></div>
            </div>
          </div>
          <span class="material-symbols-outlined text-text-muted">arrow_forward</span>
        </a>

        <a class="flex items-center justify-between gap-4 bg-primary text-white rounded-lg px-6 py-5 shadow-lg shadow-primary/25 hover:bg-primary-light transition-all"
           href="<?= e(lusso_href((string)$directions_href)) ?>">
          <div class="flex items-center gap-4 min-w-0">
            <span class="material-symbols-outlined">location_on</span>
            <div class="min-w-0">
              <div class="text-xs font-bold uppercase tracking-widest text-white/80">Directions</div>
              <div class="font-semibold truncate">Get Directions</div>
            </div>
          </div>
          <span class="material-symbols-outlined">arrow_forward</span>
        </a>
      </div>
    </div>
    <div class="md:col-span-7 flex flex-col gap-10 md:pt-8">
      <div class="bg-white rounded-2xl border border-black/[0.06] shadow-elevation p-8 lg:p-10">
        <h3 class="text-xs font-bold uppercase tracking-widest text-text-muted mb-5">Address</h3>
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
          <div class="md:col-span-7">
            <div class="text-text-main font-body leading-loose">
              <?= $address_html ?>
            </div>
          </div>
          <div class="md:col-span-5">
            <div class="space-y-5">
              <div>
                <div class="text-xs font-bold uppercase tracking-widest text-text-muted mb-2">Concierge</div>
                <div class="font-semibold text-text-main"><?= e($concierge_phone) ?></div>
              </div>
              <div>
                <div class="text-xs font-bold uppercase tracking-widest text-text-muted mb-2">Inquiries</div>
                <div class="font-semibold text-text-main">
                  <a class="hover:text-primary transition-colors" href="mailto:<?= e($inquiries_email) ?>"><?= e($inquiries_email) ?></a>
                </div>
              </div>
              <div class="pt-2">
                <a class="inline-flex items-center gap-2 text-sm font-bold uppercase tracking-widest text-primary hover:text-primary-light transition-colors"
                   href="<?= e(lusso_href((string)$directions_href)) ?>">
                  <span>Get Directions</span>
                  <span class="material-symbols-outlined text-lg">arrow_forward</span>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="relative w-full h-[400px] lg:h-[500px] overflow-hidden rounded-sm bg-gray-100 shadow-elevation group">
        <?php if ($mapUrl !== ''): ?>
          <iframe
            class="w-full h-full border-0"
            src="<?= e($mapUrl) ?>"
            allowfullscreen
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            title="Map showing <?= e($pageTitle) ?> location"></iframe>
        <?php else: ?>
          <div class="w-full h-full flex items-center justify-center text-sm text-text-muted">
            Map not configured yet.
          </div>
        <?php endif; ?>

        <div class="absolute inset-0 pointer-events-none bg-gradient-to-t from-background-light/10 to-transparent"></div>

        <?php if ($directionsUrl !== '#'): ?>
          <div class="absolute bottom-4 right-4">
            <a href="<?= e($directionsUrl) ?>" target="_blank" rel="noopener noreferrer"
               class="bg-white text-text-main px-4 py-2 rounded-lg shadow-lg text-sm font-bold flex items-center gap-2 hover:bg-gray-50 transition-colors">
              <span class="material-symbols-outlined text-primary">directions</span>
              <span>Get Directions</span>
            </a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</main>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
