-- Lusso Hotels & Suites — legal pages + contact map fields (MySQL 5.7+)
-- Run in phpMyAdmin against your site database.
-- Safe to re-run: INSERT IGNORE (will NOT overwrite any existing/live content).
-- Backup your database before running in production.

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------------
-- Contact page: map embed fields (new) — insert only if missing
-- ---------------------------------------------------------------------------
INSERT IGNORE INTO `page_sections` (`page`, `section_key`, `content_type`, `content`) VALUES
('contact', 'map_address', 'text', ''),
('contact', 'map_embed_url', 'text', '');

-- ---------------------------------------------------------------------------
-- Hotel Policy page — insert only if missing
-- ---------------------------------------------------------------------------
INSERT IGNORE INTO `page_sections` (`page`, `section_key`, `content_type`, `content`) VALUES
('hotel-policy', 'page_title', 'text', 'Hotel Policy - Lusso Hotels Abuja'),
('hotel-policy', 'hero_kicker', 'text', 'Guest Information'),
('hotel-policy', 'hero_title', 'text', 'Hotel Policy'),
('hotel-policy', 'hero_subtitle', 'text', 'A simple guide to ensure a calm, seamless stay for every guest.'),
('hotel-policy', 'last_updated', 'text', 'Last updated: April 8, 2026'),
('hotel-policy', 'body_html', 'html',
'<p>We value quiet luxury and thoughtful hospitality. These policies help ensure comfort, safety, and consistency for all guests at <strong>The Lusso Hotels &amp; Suites</strong>.</p>

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
<p>To preserve the calm atmosphere of the property, we observe quiet hours from <strong>10:00 PM – 7:00 AM</strong>. Please keep hallway noise and in-room audio to a minimum during this time.</p>

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
<p>Guests are responsible for maintaining rooms and fixtures in good condition. The hotel may charge for missing items, deep cleaning, or damages beyond normal wear.</p>')
;

-- ---------------------------------------------------------------------------
-- Privacy Policy page — insert only if missing
-- ---------------------------------------------------------------------------
INSERT IGNORE INTO `page_sections` (`page`, `section_key`, `content_type`, `content`) VALUES
('privacy-policy', 'page_title', 'text', 'Privacy Policy - Lusso Hotels Abuja'),
('privacy-policy', 'hero_kicker', 'text', 'Legal'),
('privacy-policy', 'hero_title', 'text', 'Privacy Policy'),
('privacy-policy', 'hero_subtitle', 'text', 'How we collect, use, and protect your personal information.'),
('privacy-policy', 'last_updated', 'text', 'Last updated: April 8, 2026'),
('privacy-policy', 'body_html', 'html',
'<p>This Privacy Policy explains how <strong>The Lusso Hotels &amp; Suites</strong> collects, uses, discloses, and safeguards your information when you visit our website, contact our concierge, or make a reservation.</p>

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

<h2>Contact</h2>
<p>If you have questions about this Privacy Policy, please contact our concierge.</p>')
;

-- ---------------------------------------------------------------------------
-- Terms & Conditions page — insert only if missing
-- ---------------------------------------------------------------------------
INSERT IGNORE INTO `page_sections` (`page`, `section_key`, `content_type`, `content`) VALUES
('terms-and-conditions', 'page_title', 'text', 'Terms & Conditions - Lusso Hotels Abuja'),
('terms-and-conditions', 'hero_kicker', 'text', 'Legal'),
('terms-and-conditions', 'hero_title', 'text', 'Terms & Conditions'),
('terms-and-conditions', 'hero_subtitle', 'text', 'The terms that govern use of our website and services.'),
('terms-and-conditions', 'last_updated', 'text', 'Last updated: April 8, 2026'),
('terms-and-conditions', 'body_html', 'html',
'<p>These Terms &amp; Conditions ("Terms") apply to your use of The Lusso Hotels &amp; Suites website and related concierge services. By accessing or using our website, you agree to these Terms.</p>

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
<p>All content on this site—including text, photos, video, logos, and design—is owned by or licensed to The Lusso Hotels &amp; Suites and is protected by applicable intellectual property laws. You may not reproduce or distribute any content without prior written permission.</p>

<h2>Disclaimer</h2>
<p>This website is provided on an "as is" and "as available" basis. While we strive for accuracy, we do not warrant that the site will be uninterrupted, error-free, or free of harmful components.</p>

<h2>Limitation of liability</h2>
<p>To the maximum extent permitted by law, The Lusso Hotels &amp; Suites shall not be liable for any indirect, incidental, special, consequential, or punitive damages arising out of or relating to your use of the website or services.</p>

<h2>Changes to these Terms</h2>
<p>We may revise these Terms from time to time. Updated Terms will be posted on this page with a revised "Last updated" date.</p>

<h2>Contact</h2>
<p>If you have questions about these Terms, please contact our concierge.</p>')
;

