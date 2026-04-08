-- Lusso Hotels & Suites — content refresh (phpMyAdmin / MySQL 5.7+)
-- Run against your site database. Updates existing keys via ON DUPLICATE KEY UPDATE.
-- Admins can still edit everything in Settings, Homepage editor, and page editors afterward.
-- Backup your database before running in production.

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------------
-- site_settings (unique: setting_key)
-- ---------------------------------------------------------------------------
INSERT INTO `site_settings` (`setting_key`, `setting_value`) VALUES
('site_name', 'The Lusso Hotels & Suites'),
('site_tagline', 'Superior luxury hospitality in Abuja — refined sophistication, genuine care, and uncompromising quality.'),
('footer_tagline', 'A distinguished luxury hospitality brand committed to excellence, innovation, and people development — professionally managed, strategically located in Maitama.'),
('footer_address', 'No. 33 Usuma Street, off Gana Street, Maitama, Abuja'),
('footer_phone', '+234 813 480 7718 | +234 907 676 0923'),
('footer_email', 'concierge@lussohotelsabuja.com'),
('contact_email', 'concierge@lussohotelsabuja.com'),
('footer_copyright', '© 2026 The Lusso Hotels & Suites. All rights reserved.')
ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`), `updated_at` = CURRENT_TIMESTAMP;

-- ---------------------------------------------------------------------------
-- page_sections (unique: page + section_key)
-- ---------------------------------------------------------------------------

-- Homepage (index): hero + philosophy teaser
INSERT INTO `page_sections` (`page`, `section_key`, `content_type`, `content`) VALUES
('index', 'hero_kicker', 'text', 'Welcome to Abuja'),
('index', 'hero_title', 'html', 'Refined Luxury in <br/><span class="italic text-primary lusso-hero-accent-text">Absolute Silence</span>'),
('index', 'hero_subtitle', 'text', 'The Lusso Hotels & Suites delivers an unparalleled guest experience: refined sophistication, genuine care, and uncompromising quality — with a strong operational framework and a people-centric culture.'),
('index', 'home_philosophy_body', 'text', 'We are dedicated to creating a world-class environment that supports exceptional service delivery and professional growth. Our 36 luxurious guest rooms, modern facilities, and strategic Maitama location — approximately 30 minutes from Nnamdi Azikiwe International Airport and minutes from key embassies and business districts — make us a hotel of choice for discerning travellers and events.'),
('index', 'hero_bg_slides', 'json', '[]')
ON DUPLICATE KEY UPDATE `content` = VALUES(`content`), `content_type` = VALUES(`content_type`), `updated_at` = CURRENT_TIMESTAMP;

-- About page: story + values copy (condensed from brand brief)
INSERT INTO `page_sections` (`page`, `section_key`, `content_type`, `content`) VALUES
('about', 'hero_subtitle', 'text', 'Strategically located at No. 33 Usuma Street, off Gana Street, Maitama, Abuja — within Nigeria''s premier commercial district — offering accessibility, proximity to landmarks, and strong business integration.'),
('about', 'story_p1', 'text', 'The Lusso Hotels & Suites features 36 well-appointed guest rooms and modern hospitality facilities for corporate stays, seminars, meetings, conferences, and private events. Our delivery is guided by Security, Accessibility, Functionality, and Excellence — with reliable power, water, HVAC, safety systems, high-speed Wi‑Fi, and professional security.'),
('about', 'story_p2', 'text', 'Facilities include reception and lobby, restaurants and kitchen, bar, lounge, swimming pool with showers, conference and event spaces, gymnasium, and spa services. Management invests in continuous training, performance accountability, and employee welfare — positioning Lusso as more than a destination: a professionally managed institution built on Superior Luxury in Hospitality.'),
('about', 'team_intro', 'text', 'Our seasoned hospitality professionals bring proven industry expertise and a commitment to inclusive, high-performance service — empowering every team member to deliver superior luxury with modern tools and a secure, conducive workplace.'),
('about', 'values_card_body', 'text', 'Health, safety, and security remain paramount: fire protection systems, emergency signage, 24-hour CCTV, controlled access, and routine compliance. Technology includes Samsung Hospitality TVs, integrated ELV systems, and background music in common areas — alongside landscaped grounds, parking, drainage, and external lighting.')
ON DUPLICATE KEY UPDATE `content` = VALUES(`content`), `content_type` = VALUES(`content_type`), `updated_at` = CURRENT_TIMESTAMP;

-- Contact page
INSERT INTO `page_sections` (`page`, `section_key`, `content_type`, `content`) VALUES
('contact', 'intro_body', 'text', 'Reach our concierge for reservations, events, and corporate stays. The Lusso Hotels & Suites welcomes you to Maitama — where strategic location meets superior service.'),
('contact', 'address_html', 'html', 'No. 33 Usuma Street, off Gana Street,<br/>Maitama, Abuja<br/>Nigeria'),
('contact', 'map_pin_label', 'text', 'The Lusso Hotels & Suites'),
('contact', 'intro_kicker', 'text', 'Concierge Services'),
('contact', 'intro_title', 'text', 'Visit & Connect')
ON DUPLICATE KEY UPDATE `content` = VALUES(`content`), `content_type` = VALUES(`content_type`), `updated_at` = CURRENT_TIMESTAMP;

-- Rooms listing page meta copy
INSERT INTO `page_sections` (`page`, `section_key`, `content_type`, `content`) VALUES
('rooms', 'hero_subtitle', 'text', '36 luxurious guest rooms and suites — corporate accommodation, events, and leisure — in a serene, secure, and functional environment.')
ON DUPLICATE KEY UPDATE `content` = VALUES(`content`), `content_type` = VALUES(`content_type`), `updated_at` = CURRENT_TIMESTAMP;
