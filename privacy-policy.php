<?php
require_once __DIR__ . '/includes/content-loader.php';

$siteName = getSiteSetting('site_name', 'Lusso Hotels & Suites');
$footerEmail = getSiteSetting('footer_email', 'concierge@lussohotels.com');
$footerPhone = getSiteSetting('footer_phone', '+234 800 LUSSO');

$pageKey = 'privacy-policy';
$pageTitle = getPageSection($pageKey, 'page_title', 'Privacy Policy');
$hero_kicker = getPageSection($pageKey, 'hero_kicker', 'Legal');
$hero_title = getPageSection($pageKey, 'hero_title', 'Privacy Policy');
$hero_subtitle = getPageSection($pageKey, 'hero_subtitle', 'How we collect, use, and protect your personal information.');
$last_updated = getPageSection($pageKey, 'last_updated', 'Last updated: April 8, 2026');
$body_html = getPageSection($pageKey, 'body_html', '');

if (trim((string)$body_html) === '') {
  $body_html = <<<HTML
<p>This Privacy Policy explains how <strong>{$siteName}</strong> collects, uses, discloses, and safeguards your information when you visit our website, contact our concierge, or make a reservation. By using our services, you agree to the practices described below.</p>

<h2>Information we collect</h2>
<ul>
  <li><strong>Contact details</strong> (such as name, email, phone number) when you inquire or request assistance.</li>
  <li><strong>Reservation details</strong> (such as stay dates, number of guests, room preferences) when you book or modify a booking.</li>
  <li><strong>Payment-related data</strong> processed by our payment providers (we do not store full card details on our servers).</li>
  <li><strong>Technical data</strong> (such as device, browser, and approximate location) collected through standard server logs and analytics.</li>
  <li><strong>Communications</strong> you send to us (emails, messages, and requests).</li>
</ul>

<h2>How we use your information</h2>
<ul>
  <li>To provide, manage, and improve reservations and guest services.</li>
  <li>To respond to inquiries and concierge requests.</li>
  <li>To send service-related communications (confirmations, updates, and support).</li>
  <li>To maintain site security, prevent fraud, and troubleshoot issues.</li>
  <li>To comply with legal obligations and enforce our terms.</li>
</ul>

<h2>Cookies and analytics</h2>
<p>We may use cookies and similar technologies to enhance site functionality, remember preferences, and understand site usage. You can control cookies through your browser settings. Disabling cookies may affect site functionality.</p>

<h2>Sharing and disclosure</h2>
<p>We may share information with trusted service providers who help us operate our website and services (for example, booking platforms, email delivery, analytics, and payment processing). These providers are permitted to use information only as necessary to provide services to us.</p>
<p>We may also disclose information if required by law or to protect the rights, safety, and security of our guests, staff, and business.</p>

<h2>Data retention</h2>
<p>We retain personal information only for as long as necessary to fulfill the purposes described in this policy, unless a longer retention period is required or permitted by law.</p>

<h2>Security</h2>
<p>We use reasonable administrative, technical, and physical safeguards designed to protect your information. No method of transmission over the internet is 100% secure; therefore, we cannot guarantee absolute security.</p>

<h2>Your choices</h2>
<ul>
  <li>You may request access, correction, or deletion of certain personal information, subject to applicable laws.</li>
  <li>You may opt out of non-essential marketing communications at any time.</li>
</ul>

<h2>Contact us</h2>
<p>If you have questions about this Privacy Policy or would like to make a request, contact us at <a href="mailto:{$footerEmail}">{$footerEmail}</a> or call <a href="tel:{$footerPhone}">{$footerPhone}</a>.</p>
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
