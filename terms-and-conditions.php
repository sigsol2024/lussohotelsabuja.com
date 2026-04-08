<?php
require_once __DIR__ . '/includes/content-loader.php';

$siteName = getSiteSetting('site_name', 'Lusso Hotels & Suites');
$footerEmail = getSiteSetting('footer_email', 'concierge@lussohotels.com');

$pageKey = 'terms-and-conditions';
$pageTitle = getPageSection($pageKey, 'page_title', 'Terms & Conditions');
$hero_kicker = getPageSection($pageKey, 'hero_kicker', 'Legal');
$hero_title = getPageSection($pageKey, 'hero_title', 'Terms & Conditions');
$hero_subtitle = getPageSection($pageKey, 'hero_subtitle', 'The terms that govern use of our website and services.');
$last_updated = getPageSection($pageKey, 'last_updated', 'Last updated: April 8, 2026');
$body_html = getPageSection($pageKey, 'body_html', '');

if (trim((string)$body_html) === '') {
  $body_html = <<<HTML
<p>These Terms &amp; Conditions ("Terms") apply to your use of the <strong>{$siteName}</strong> website and related concierge services. By accessing or using our website, you agree to these Terms.</p>

<h2>Use of the website</h2>
<ul>
  <li>You may use this website for lawful purposes and in accordance with these Terms.</li>
  <li>You must not attempt to disrupt, damage, or gain unauthorized access to the website or its systems.</li>
  <li>We may update, suspend, or discontinue any part of the site without notice.</li>
</ul>

<h2>Reservations and services</h2>
<p>Reservation availability, rates, inclusions, and policies may change. Specific booking terms (including cancellation, deposits, and no-show policies) may be provided at the time of booking and will apply to your reservation.</p>

<h2>Third-party links and embeds</h2>
<p>Our website may contain links to third-party websites and services (including maps and booking providers). We do not control third-party services and are not responsible for their content or policies.</p>

<h2>Intellectual property</h2>
<p>All content on this site—including text, photos, video, logos, and design—is owned by or licensed to {$siteName} and is protected by applicable intellectual property laws. You may not reproduce or distribute any content without prior written permission.</p>

<h2>Disclaimer</h2>
<p>This website is provided on an "as is" and "as available" basis. While we strive for accuracy, we do not warrant that the site will be uninterrupted, error-free, or free of harmful components.</p>

<h2>Limitation of liability</h2>
<p>To the maximum extent permitted by law, {$siteName} shall not be liable for any indirect, incidental, special, consequential, or punitive damages arising out of or relating to your use of the website or services.</p>

<h2>Changes to these Terms</h2>
<p>We may revise these Terms from time to time. Updated Terms will be posted on this page with a revised "Last updated" date.</p>

<h2>Contact</h2>
<p>If you have questions about these Terms, please contact us at <a href="mailto:{$footerEmail}">{$footerEmail}</a>.</p>
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
