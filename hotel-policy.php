<?php
require_once __DIR__ . '/includes/content-loader.php';

$siteName = getSiteSetting('site_name', 'Lusso Hotels & Suites');
$footerEmail = getSiteSetting('footer_email', 'concierge@lussohotels.com');
$footerPhone = getSiteSetting('footer_phone', '+234 800 LUSSO');

$pageKey = 'hotel-policy';
$pageTitle = getPageSection($pageKey, 'page_title', 'Hotel Policy');
$hero_kicker = getPageSection($pageKey, 'hero_kicker', 'Guest Information');
$hero_title = getPageSection($pageKey, 'hero_title', 'Hotel Policy');
$hero_subtitle = getPageSection($pageKey, 'hero_subtitle', 'A simple guide to ensure a calm, seamless stay for every guest.');
$last_updated = getPageSection($pageKey, 'last_updated', 'Last updated: April 8, 2026');
$body_html = getPageSection($pageKey, 'body_html', '');

if (trim((string)$body_html) === '') {
  $body_html = <<<HTML
<p>We value quiet luxury and thoughtful hospitality. These policies help ensure comfort, safety, and consistency for all guests at <strong>{$siteName}</strong>.</p>

<h2>Check-in / Check-out</h2>
<ul>
  <li><strong>Check-in:</strong> 2:00 PM (or as confirmed with concierge)</li>
  <li><strong>Check-out:</strong> 12:00 PM</li>
  <li>Early check-in / late check-out may be available upon request and subject to occupancy.</li>
</ul>

<h2>Identification</h2>
<p>All guests must present a valid government-issued ID at check-in. Additional verification may be requested for security and fraud prevention.</p>

<h2>Payments, deposits, and incidentals</h2>
<p>A deposit or card authorization may be required at check-in to cover incidentals and potential damages. Accepted payment methods and deposit requirements may vary by rate plan and reservation channel.</p>

<h2>Quiet hours</h2>
<p>To preserve the calm atmosphere of the property, we observe quiet hours from <strong>10:00 PM – 7:00 AM</strong>. Please keep hallway noise and in-room music to a minimum during this time.</p>

<h2>Smoking</h2>
<p>Smoking and vaping are not permitted in guest rooms or indoor areas unless specifically designated. A cleaning fee may apply if smoking occurs in a non-smoking area.</p>

<h2>Visitors</h2>
<p>For guest security, unregistered visitors may be restricted. Please inform concierge in advance if you expect visitors. The hotel may limit visitor access during late hours.</p>

<h2>Children</h2>
<p>Children are welcome. For safety, minors must be supervised by a parent/guardian in all public areas, including the pool and fitness facilities.</p>

<h2>Pets</h2>
<p>Pets are permitted only where expressly stated in your booking confirmation. Service animals are welcome.</p>

<h2>Lost &amp; found</h2>
<p>If an item is found after departure, we will hold it for a limited period. Shipping may be arranged at the guest’s expense.</p>

<h2>Property care</h2>
<p>Guests are responsible for maintaining rooms and fixtures in good condition. The hotel may charge for missing items, deep cleaning, or damages beyond normal wear.</p>

<h2>Contact</h2>
<p>If you need clarification on any policy, please contact our concierge at <a href="mailto:{$footerEmail}">{$footerEmail}</a> or call <a href="tel:{$footerPhone}">{$footerPhone}</a>.</p>
HTML;
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
    .lusso-legal-content h2 { margin-top: 28px; margin-bottom: 10px; font-weight: 600; letter-spacing: -0.01em; }
    .lusso-legal-content p { margin-top: 10px; line-height: 1.8; }
    .lusso-legal-content ul { margin-top: 10px; padding-left: 1.25rem; list-style: disc; }
    .lusso-legal-content li { margin-top: 8px; line-height: 1.7; }
    .lusso-legal-content a { text-decoration: underline; }
  </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display antialiased text-text-main dark:text-white transition-colors duration-300 overflow-x-hidden">
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="relative flex min-h-screen w-full flex-col">
  <main class="flex-grow w-full max-w-[980px] mx-auto px-6 lg:px-12 py-12 lg:py-20">
    <div class="mb-10">
      <span class="block text-primary text-xs font-bold uppercase tracking-[0.25em] mb-3"><?= e($hero_kicker) ?></span>
      <h1 class="text-4xl md:text-5xl lg:text-6xl font-display font-medium tracking-tight mb-4"><?= e($hero_title) ?></h1>
      <p class="text-text-muted text-base md:text-lg font-body font-light leading-relaxed max-w-2xl"><?= e($hero_subtitle) ?></p>
      <p class="text-xs text-text-muted mt-4"><?= e($last_updated) ?></p>
    </div>

    <div class="bg-white rounded-2xl border border-black/[0.06] shadow-elevation p-7 md:p-10">
      <div class="lusso-legal-content text-text-main">
        <?= $body_html ?>
      </div>
    </div>
  </main>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
