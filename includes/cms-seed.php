<?php
/**
 * Default CMS seed data for fresh deployments.
 * Used by tools/seed_cms_defaults.php — does not overwrite existing rows (INSERT IGNORE).
 */

$cmsDefaults = require __DIR__ . '/cms-defaults.php';

$siteSettings = [
    'site_name' => 'Lusso Hotels Abuja',
    'site_tagline' => 'Refined luxury in the heart of Abuja.',
    'room_detail_hero_badge' => 'Lusso Abuja',
    'currency_symbol' => '$',
    'footer_tagline' => 'Refining the art of hospitality in the heart of Abuja.',
    'footer_address' => "15 Luxury Avenue,\nMaitama, Abuja",
    'footer_phone' => '+234 800 LUSSO 00',
    'footer_email' => 'concierge@lussohotels.com',
    'footer_copyright' => '© 2026 Lusso Hotels Abuja. All rights reserved.',
    'contact_email' => 'concierge@lussohotels.com',
    'whatsapp_number' => '',
    'whatsapp_link' => '',
    'nav_suites_label' => 'Suites',
    'nav_dining_label' => 'Dining',
    'nav_experience_label' => 'Amenities',
    'nav_events_label' => 'Gallery',
    'nav_suites_href' => '/rooms',
    'nav_dining_href' => '/dining',
    'nav_experience_href' => '/amenities',
    'nav_events_href' => '/gallery',
    'nav_cta_label' => 'Check Availability',
    'nav_cta_href' => '/rooms',
    'footer_privacy_href' => '#',
    'footer_terms_href' => '#',
    'social_media_json' => '[]',
    'google_maps_api_key' => '',
    'smtp_host' => '',
    'smtp_port' => '587',
    'smtp_username' => '',
    'smtp_password' => '',
    'smtp_encryption' => 'tls',
    'smtp_from_email' => '',
    'smtp_from_name' => 'Lusso Hotels Abuja',
    'header_scripts' => '',
    'body_scripts' => '',
    'footer_scripts' => '',
    'site_logo' => '',
    'site_logo_light' => '',
    'site_favicon' => '',
];

$jsonEncode = static function ($data) {
    return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
};

$pageSections = [
    // --- Homepage (index) ---
    ['index', 'hero_kicker', 'text', 'Welcome to Abuja'],
    ['index', 'hero_title', 'html', 'Refined Luxury in <br/><span class="italic text-primary lusso-hero-accent-text">Absolute Silence</span>'],
    ['index', 'hero_subtitle', 'text', 'The Lusso Hotels & Suites delivers an unparalleled guest experience.'],
    ['index', 'hero_cta_text', 'text', 'Discover Suites'],
    ['index', 'hero_cta_href', 'text', '/rooms'],
    ['index', 'hero_bg', 'image', 'https://lh3.googleusercontent.com/aida-public/AB6AXuA09AOzJGi3HFlO4iws6G405bZGiytnUaZEFTya_spJrXDa5fTKSrBScsDkxZAQCuS6ae2mJpC0laUldei8amf2jOsK9UIg9NX305aHkrG5uIMWhPQ-1e4r8CAydwyR5KzlbYjN4mWRnao2gNBHBrofxEv7u5nEs6wpDuCE4GwvUSepjITkua6sUOfXNKlnd3aW_eBFeHSCedk94uypJTs6palB8AtN0hFG3qGsOckYndru2W3fVdobc9Goi1Jn_x4wNASClu7QbTw'],
    ['index', 'hero_bg_slides', 'json', '[]'],
    ['index', 'home_philosophy_kicker', 'text', 'Our Philosophy'],
    ['index', 'home_philosophy_title_html', 'html', 'The Lusso <br/> Standard'],
    ['index', 'home_philosophy_body', 'text', 'A sanctuary where modern elegance meets timeless hospitality. Every detail is curated for the discerning traveler, offering an escape into a world of refined comfort and understated opulence.'],
    ['index', 'home_philosophy_link_text', 'text', 'Read Our Story'],
    ['index', 'home_philosophy_link_href', 'text', '/about'],
    ['index', 'home_philosophy_main_img', 'image', 'https://lh3.googleusercontent.com/aida-public/AB6AXuBFg7gJlVa6HBS6q1iLjtlnwOEcX73nQSMu6S215ldLAwhsmFWj_gEsJmkFEUQbWJX6ra4pBbFSdzncdNx6Kq2Nsk92-B0IGEEhvbTVS7o3_R5CitRPP620Oup6zyH3aHM4MJWZ2gjCfGZsiQwqgdvffFn6dIea_rdhlf65xdl7YOvhPXIH6oswTgJFJ5dVVuhhT4tTybzqtymSgn4zKcmCCE6ou2dvro0c12b-62hwQ0ICLkRobWTb4P5RG2A-QYLr_R4vJ1CEfpA'],
    ['index', 'home_philosophy_secondary_img', 'image', 'https://lh3.googleusercontent.com/aida-public/AB6AXuD6pAMGgzxkcnf_QvXCk0vaVBMa1ZfDYY5pu5Z19ue-xQElZrSmfMgt7MMpgdd0x-MaWdaGFfkt0iDkFIojqbJbHleL994JEjdTMV1OlHEmgD1IVLChUht47aNEtqph_k-GYbEpP8MsYb4RMGeEIl7Fz6Tulv-yKdtcm_Vqq5OKetKpLpmgndACjPi-EF3lHZwtiVHX95Hai-ZglRi_TJo-lwjryAZx5S7fIxxrvpg6Y5kcYQ3roV9xVFkIN5VH2NA8u3zsKiRnzTY'],
    ['index', 'home_arch_image', 'image', 'https://lh3.googleusercontent.com/aida-public/AB6AXuAwrveUZUqfyJW3ZGFhXbQ9Z-BJGvu7JmOsZGfhLzxtP81Ajz71Zux2sc_77ivxwhzTeVdyAZjru0FQcSRC5VEazY5_2JEGwssiSsC2vgrIbDMoNRpOx5tsEj-Ehi-d9KCrTuXhu0kytoXiUDJw9nGJdPlVGaSNVJJjnnwRyWn9oLYR3YT504jEV295_zvndgHLLGjhpkWWdqt7Pxr4cYjnp0g8qZIExKHZDCAcuXU09mKpNZ7vLrrCtlev8tZDGNuAwaFEBMQJwoY'],
    ['index', 'home_arch_badge_title', 'text', '5 Star'],
    ['index', 'home_arch_badge_sub', 'text', 'Diamond Award'],
    ['index', 'home_arch_title', 'text', 'Architectural Marvel'],
    ['index', 'home_arch_body', 'text', "Designed to inspire with asymmetry and light. Our spaces are not just built; they are sculpted to capture the essence of Abuja's golden sunsets."],
    ['index', 'home_arch_list_1', 'text', 'Bespoke Art Installations'],
    ['index', 'home_arch_list_2', 'text', 'Panoramic City Views'],
    ['index', 'home_rooms_kicker', 'text', 'Accommodations'],
    ['index', 'home_rooms_title', 'text', 'Stay in Style'],
    ['index', 'home_rooms_view_all_href', 'text', '/rooms'],
    ['index', 'home_dining_image', 'image', 'https://lh3.googleusercontent.com/aida-public/AB6AXuC8oI0b9xyk-0D9dg2dS7xsD8ImpFKMSmhDX5t2LSDPnlxBYqkD4ieHnvVpSZPhTL538QOvuQzAzRmVLu8SPHkpbqeeXqC8oY6rDvpvhzrGCRL3Z6mtgX5IFnjIel6nvJQDwB46i6mQVOFcE3iLSQZgxjZeUYZ_30zsT68gKbNNbKQLnZxn1IruJ1A9RqUnu7b3UfUoFJ1I4_4ot8harp8ziirHYve6PlytFVANpiUsB6uixZ9LHHZT_RnMDp7YLaH0N1rItTPP9Dg'],
    ['index', 'home_dining_kicker', 'text', 'Dining'],
    ['index', 'home_dining_title', 'text', 'Culinary Excellence'],
    ['index', 'home_dining_body_html', 'html', 'Savor world-class cuisine at <span class="text-text-main font-semibold">The Azure</span>, our rooftop dining experience offering panoramic views of Abuja. From local delicacies to international gastronomy, every dish is a journey.'],
    ['index', 'home_dining_cta1', 'text', 'Reserve a Table'],
    ['index', 'home_dining_cta1_href', 'text', '/dining'],
    ['index', 'home_dining_cta2', 'text', 'View Menu'],
    ['index', 'home_dining_cta2_href', 'text', '/dining'],
    ['index', 'booking_widget_html', 'html', ''],

    // --- About ---
    ['about', 'page_title', 'text', 'The Lusso Legacy - About Us'],
    ['about', 'hero_established', 'text', 'Established 2024'],
    ['about', 'hero_title_html', 'html', 'The Lusso <br/><span class="font-bold italic text-primary/90 lusso-hero-accent-text">Legacy</span>'],
    ['about', 'hero_subtitle', 'text', 'Redefining luxury hospitality in the heart of Abuja through architectural excellence and timeless Nigerian warmth.'],
    ['about', 'hero_bg', 'image', 'https://lh3.googleusercontent.com/aida-public/AB6AXuCKUqHq-GN4lZRDb3VpJoBQf0wCp1L0HT1ffMFvon2Fpx5JFUEIVencwF0zLiODNpnveRzU99J5BKJy0pzhV-qqantGLstOLFb5JPlyGtStyFoC0Udyi7ds1hHqs3bQea5IP5yTl64R0lpZUOwrFHM8IU1hGWk8IxE92fhLliSnZOPp9qURFWlXFpaKeyn4pfcHGu8C-Cy6RL5CGYSU1gY2VZcN4xsr9MTU9pn5C9k765bD6huMgag92SUZDmWZnTRfI9TroGluz6U'],
    ['about', 'story_title_html', 'html', 'Defining Abuja <br/><span class="font-semibold text-primary">Luxury</span>'],
    ['about', 'story_p1', 'text', 'We believe that true luxury lies not just in opulence, but in the meticulous attention to detail and the warmth of genuine hospitality. Our philosophy is rooted in creating sanctuary-like spaces where every interaction is a curated experience.'],
    ['about', 'story_p2', 'text', 'Lusso Hotels stands as a beacon of sophistication, merging contemporary design with the rich cultural heritage of Nigeria.'],
    ['about', 'story_image', 'image', 'https://lh3.googleusercontent.com/aida-public/AB6AXuC4lqSBAhhqs4N0scumYaXQVT9ZEKAqsT_RPiR2q01XspmWMfxYxiD7UuiobDr5yZBBKrDx4Hofa_5b3JtjrNNfAQitapKQnelBzcfd0ifQMU_E2voX7irqZkMDtTEY5c-8MtVvKBkg7E2f_0kPteIGEmhNqGdu__3OIeyxumRk3L-Z5ROyKmXnp5aLY7vvpaGzgbcsWSJ3hKRUGQ9WSJAEN3py7TcPvohXQWjbd98ECZq53hLEZ2iMDwWRyaY0IVPkaLFyRqVk768'],
    ['about', 'story_quote', 'text', '"A sanctuary where time slows down and memories are crafted."'],
    ['about', 'values_kicker', 'text', 'Our Core Values'],
    ['about', 'values_title', 'text', 'Artistry in Service'],
    ['about', 'values_image', 'image', 'https://lh3.googleusercontent.com/aida-public/AB6AXuCy3zS6w7bzryizdISWmfQ_S0JNETi-CNKg-hvJBSLvedYviP7f1Y09Q5t3sMnmMYs21w8obHIIGOb1cQydo18DhPNqcU6t-dpeSXh19ItorLF9eHU8fGaH6E-FlUlWKCaViR6axwv17cKdIMPLxK_tnVCpu9Lgs2g8DqC3v8fs87feUGps5ypr_Pko0JAGNCO8AkRZka4k1aadMxSlooiKWR4svNelcEujv6CkGSmjV0TqQjLjcqDe4Vx0gh2ceUf1oPD-I6d1aVs'],
    ['about', 'values_card_icon', 'text', 'spa'],
    ['about', 'values_card_title', 'text', 'Sanctuary for the Senses'],
    ['about', 'values_card_body', 'text', 'From the gentle aroma of our signature scent in the lobby to the curated art pieces adorning the walls, every element at Lusso Hotels is designed to evoke a sense of calm and wonder.'],
    ['about', 'values_card_link', 'text', 'Read about our wellness'],
    ['about', 'values_card_link_href', 'text', '/amenities'],
    ['about', 'journey_title_html', 'html', 'A Historic <span class="font-bold italic text-primary">Journey</span>'],
    ['about', 'journey_subtitle', 'text', 'Tracing the milestones that shaped our vision of hospitality.'],
    ['about', 'team_kicker', 'text', 'Leadership'],
    ['about', 'team_heading', 'text', 'The Curators'],
    ['about', 'team_intro', 'text', 'Meet the visionaries dedicated to crafting your perfect stay.'],
    ['about', 'parallax_bg', 'image', 'https://lh3.googleusercontent.com/aida-public/AB6AXuD3CpM9PF0quzu5ENNbyfrW4zTzCEMO_H7AEdFnIbDMIhurw-MN5oG3CYu33yyX74nXm8XqyQ5rWuDUK1LqHo3YeVaAe44npBDrPxoJGJWPJcCt3loAI3ZdpZJTxEJxAGbs_PGZ1BEhCN76N2fSJuaomMfPIYYOx3btJ8FOZQRmrtxs0FQOI0OZJPPLI5WoBwzJ_pl1gq96qrcFCdOdsgIzrVnxyfqS_Zk71pTDD1FaNXjggESLZ5KhoJfqv-Q2WqqyBwV2KxvS-n8'],
    ['about', 'parallax_quote', 'text', '"Where elegance meets heritage."'],
    ['about', 'cta_title', 'text', 'Ready to Experience the Legend?'],
    ['about', 'cta_body', 'text', 'Your journey into the extraordinary begins here. Reserve your sanctuary in Abuja today.'],
    ['about', 'cta_btn1', 'text', 'Check Availability'],
    ['about', 'cta_btn1_href', 'text', '/rooms'],
    ['about', 'cta_btn2', 'text', 'Contact Concierge'],
    ['about', 'cta_btn2_href', 'text', '/contact'],

    // --- Contact ---
    ['contact', 'page_title', 'text', 'Contact Lusso Hotels Abuja'],
    ['contact', 'intro_kicker', 'text', 'Concierge Services'],
    ['contact', 'intro_title', 'text', 'Get in Touch'],
    ['contact', 'intro_body', 'text', 'Experience the quiet luxury of Lusso Abuja. Whether planning a stay or an event, we are here to assist with your every request.'],
    ['contact', 'address_html', 'html', "123 Diplomatic Drive<br/>\nCentral Business District,<br/>\nAbuja, Nigeria"],
    ['contact', 'directions_href', 'text', '#'],
    ['contact', 'concierge_phone', 'text', '+234 800 LUSSO'],
    ['contact', 'inquiries_email', 'text', 'concierge@lussohotels.com'],
    ['contact', 'map_image', 'image', 'https://lh3.googleusercontent.com/aida-public/AB6AXuAK_uzWkbR7rEovHVUwT02daL4jG0KZoBuT3aKk-b0PKZbeHgLb-bs6-sAYadAWnRTYyZV42HkpJOXsw-Toccx_1HckozB8hIVInA8MzcVlmiFHtgd5_9kHvcm3Jz-0JmZVLiq6EDmrVRV9sT-s6InQ3Y09Bp24mO10aVSdVRVTPo-vrGoO1R4oYFd6VjZaOLskG8CrXcQwSVzoV9i30JsEevxP9EKAww9JrTXljPIZyLtC5tbq6OFt_dyKltcSXMLFAnwIQ-e6yBs'],
    ['contact', 'map_pin_label', 'text', 'Lusso Abuja'],

    // --- Gallery ---
    ['gallery', 'page_title', 'text', 'Lusso Visual Gallery'],
    ['gallery', 'hero_kicker', 'text', 'The Collection'],
    ['gallery', 'hero_title_html', 'html', 'VISUAL <span class="font-bold italic text-primary">NARRATIVES</span>'],
    ['gallery', 'hero_subtitle', 'text', 'A curated glimpse into the architecture, lifestyle, and moments that define Lusso Abuja.'],
    ['gallery', 'hero_bg', 'image', 'https://lh3.googleusercontent.com/aida-public/AB6AXuCdJfaCSOs5MQG_DTVMby3bOBjCkoGkef0FMU9Qry3ryiP5bNDsyiy__h8ek57_WP4qOYB8oMBkIQx3SfBGNd3bmf9BIRd68F7CgdZyK3nqgEhC32fKLPyXK59qpMNfuuZYox51JBGo0ezJWITKOmkoYSEwCRm0yq-vMBJ2A4MjeExfWzIRLYbt-DtWEZuBAFzukpACYT5ly4EnGU2ABz-ZIpUw4VM9bWWIlBuJMnPezmfGiJ7wNV2U3WSDzxglmX2ClVUbEW5mKp0'],

    // --- Dining ---
    ['dining', 'page_title', 'text', 'Lusso Fine Dining Experience'],
    ['dining', 'hero_kicker', 'text', 'Fine Dining — Abuja'],
    ['dining', 'hero_title_html', 'html', 'Culinary Artistry <br/><i class="font-light opacity-90">at Altitude</i>'],
    ['dining', 'hero_subtitle', 'text', 'An ode to Italian heritage meeting Nigerian warmth. Experience the pinnacle of fine dining in the heart of the capital.'],
    ['dining', 'hero_hours', 'text', 'Open Daily: 6pm — 11pm'],
    ['dining', 'hero_bg', 'image', 'https://lh3.googleusercontent.com/aida-public/AB6AXuAvhrhShd5tNgVFn390MBWNroawzxnz7dopK8zru3FfSOCtxj84pS6JMtU5HoIP1d21T-g-pbw0UcPYkC1OFFRMNjcf7yWmMWVomUX7BTEEV_1Ef26Hul_vr9pqpoiB9ZBYt-Tiamu78r3aLT2G6yVoCFZ7efgIwR5NWzyG4CO07l5WDg5a2i-g2FW-x8XezFaKslgDsoMUYKQtMvQ-52S66ZxS5aBkxCCZ5aBC9OGNWaTOvPxQU5rBgWbZjucOEyCc72gtNYQ4KcI'],
    ['dining', 'intro_vertical', 'text', 'Est. 2024 — Lusso Abuja'],
    ['dining', 'chef_title_html', 'html', 'The Chef\'s <br/> <span class="text-primary italic">Philosophy</span>'],
    ['dining', 'chef_body_html', 'html', '<p><span class="text-5xl float-left mr-3 mt-[-10px] font-serif text-primary">L</span>usso is not merely a restaurant; it is a stage. Our culinary philosophy marries the rustic charm of Italian tradition with the vibrant, earthy soul of Nigerian ingredients.</p><p>Every plate is a canvas, and every bite tells a story of travel, passion, and meticulous craftsmanship. We believe in the purity of flavor, sourcing locally where possible, and importing only the essential roots of our heritage.</p>'],
    ['dining', 'chef_signature', 'text', 'Antonio Rossi, Executive Chef'],
    ['dining', 'chef_main_img', 'image', 'https://lh3.googleusercontent.com/aida-public/AB6AXuCMqBwKd31GPaNNbHm8BOdQvNxOn63IyFn_kazuiOgjnvrX4QEUDvbWppPsK5WZLtpJ17k7q2KG6ql6VNX-IlUxxpIXXSeeQ_Y27i-N4X-omNimDGAwEKbmt2WZyvGhfZgSgVHd3anwhg89qKLfjxWRgTqPCw9Eu0W4PlctyVJrpIDZH0_SOX-qvRXmvzdJPFdKX_rTzapedq4JK0e7WhLGdob-CIX2G7zgxeoExFlpxkiquCatw_qd_hsDpssjjHDN9N11IeMDUwY'],
    ['dining', 'chef_circle_img', 'image', 'https://lh3.googleusercontent.com/aida-public/AB6AXuA3t3cT8mXAIbE15aEv4Ieh__7hgIGP0dEZAFWL13N31vsGR68Ck6shIv0oQQJcl-hMJo4EbTcsVZsRDphJoRXn1KWQWPDwSihnU_CV1IOlbQ-dBZdZijVnG4DreU3M1rq_-y8TrtOCAwqjY1Sid7n1XXs_13YIpZF-bxV2PX-VKMkHW2NR0T_40YYlegeQ_VtkR51jMf4QvtmHtCrB2zYHQPzrHtsI6aJ2YLMMH7NhXiX2xWbrk9FsbZ0v2zHuvWQ2YcxQhrciOgg'],
    ['dining', 'visual_title', 'text', 'Visual Narrative'],
    ['dining', 'visual_link_href', 'text', '/gallery'],
    ['dining', 'menu_kicker', 'text', 'Taste of Excellence'],
    ['dining', 'menu_heading', 'text', 'Curated Selections'],
    ['dining', 'menu_quote', 'text', '"Simplicity is the ultimate sophistication."'],
    ['dining', 'cta_bg', 'image', 'https://lh3.googleusercontent.com/aida-public/AB6AXuBZoPyfe7cRP0q9ToC-kCbnj2Yws5qW5M58VV4qYbK59oZAuf6TKHu8vXFMnhatIXH1DIw2IA-MbLMY0oNVR3Y81CYw7sAC_Ylg2snuoexujkVWl4nCTmj4ziiBzwDaapX2R1LIDldwze7229ujQONsw8UzLmfcinrTyVF8Nx9BlrJUQG9frPxonXfC55w3UvZVgnepOMfXjTSmCGjh5ONWGWNyMDpd037iiu1xqpYF7S_lqgEHcstFAwZwtU31DhY7BTaEHtas-Ms'],
    ['dining', 'cta_title', 'text', 'Secure Your Table at the Center of Abuja'],
    ['dining', 'cta_body', 'text', 'We recommend booking at least 48 hours in advance for weekend dinner service.'],
    ['dining', 'cta_btn1', 'text', 'Make a Reservation'],
    ['dining', 'cta_btn2', 'text', 'Private Dining'],
    ['dining', 'sticky_kicker', 'text', 'Book Now'],
    ['dining', 'sticky_subtitle', 'text', 'Limited Availability'],

    // --- Rooms listing CMS ---
    ['rooms', 'page_title', 'text', 'Lusso Rooms & Suites Listing'],
    ['rooms', 'hero_title', 'html', 'Sanctuaries of <br/><span class="font-bold italic font-serif">Serenity</span>'],
    ['rooms', 'hero_subtitle', 'text', 'Experience the pinnacle of luxury in our meticulously designed rooms and suites in Abuja. Where architectural precision meets organic comfort.'],
    ['rooms', 'hero_bg', 'image', 'https://lh3.googleusercontent.com/aida-public/AB6AXuACfCcU52cQzGcvXpfeoUxA6gX-lvIEIwkyZX2KoBlPWe0pI_qjdpUW7jGLvS9PnOzKcru4E0yPYmfybadhICjDcNrkau7o83qlek4lOCFQMs1ESNS65Cq3MBJiRhMZaOdna-7YwmxojNijSAQX_i0epjSoyq4FDKrQ3bMd9y-7QnHNvBAT31pNfiZAz8AKThIPxl278F_xb8KU0SKh1Do-Ac1BXxaz1DZ0ZsURc3mEdLICiZ2mpa7xxpMX6S-ZFJtvaqs9_W0-Y-M'],

    // --- Amenities ---
    ['amenities', 'page_title', 'text', 'Lusso Signature Amenities'],
];

$pageSections[] = ['about', 'timeline_json', 'json', $jsonEncode($cmsDefaults['about_timeline'])];
$pageSections[] = ['about', 'team_json', 'json', $jsonEncode($cmsDefaults['about_team'])];
$pageSections[] = ['gallery', 'items_json', 'json', $jsonEncode($cmsDefaults['gallery_items'])];
$pageSections[] = ['dining', 'masonry_json', 'json', $jsonEncode($cmsDefaults['dining_masonry'])];
$pageSections[] = ['dining', 'menu_json', 'json', $jsonEncode($cmsDefaults['dining_menu'])];
$pageSections[] = ['amenities', 'sections_json', 'json', $jsonEncode($cmsDefaults['amenities_sections'])];

$rooms = [
    [
        'title' => 'The Deluxe Room',
        'slug' => 'deluxe-room',
        'price' => 450.00,
        'room_type' => 'Deluxe',
        'max_guests' => 2,
        'short_description' => 'A haven of comfort featuring bespoke furnishings and panoramic city views.',
        'description' => "A haven of comfort featuring bespoke furnishings and panoramic city views. Designed for the discerning traveler seeking a quiet retreat amidst the urban energy.\n\nUnwind in tailored calm with curated materials, soft lighting, and a restful sense of space that feels uniquely Lusso.",
        'main_image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCQFHhQg_1wPkT5EstjzQnaFC600qqYH89L3FNMjbh4-bY1-OrRqgwRamewPOZC69NQlUaI-pvd1v2SrK7E-CpzbSokA01enDwpfore2oyeuVis07b1fORurAjjTSP5LvO_14TPZ0ydrNK6boF1JtiBJY5wIG40NsJN1MZXojo8Xo4XG1FoV-F_JQHEEPTC_thEEog3PPHWqDR5RD5kbQHpRiix4mlzccxEnFAe2UWrgI4_3lOYOzWDepTYS9-nAS6mZZ1NvVvs8fE',
        'gallery_images' => [
            'https://lh3.googleusercontent.com/aida-public/AB6AXuDn-wEXEEmpIcmGY5wWvYK9hEQDiQ3NG1RJqvRb_OD-wkNZjbYHJVUWhCHAMall9jL2rLPqUo3QJZyI4UjGiLa0NN5s-rslW6N7AUj83RI6NV9nEAChOAn8PzREhgwYiI7ztN88dL4xwJNKU2jTrSZ8jSOBUuZPp-faNfKosZF4N3u97HTVF1FzDZ75U5YcOukmNsXU-REma1zsQEoiy3pP_gf_3CFUAJlY0BNSt8oyc9lPdIbwgKpwfk-rSuo3UYF8L7I0uBvuGpo',
        ],
        'features' => ['45 SQM', 'King Bed', 'City View', 'Hi-Speed Wifi'],
        'size' => '45 SQM',
        'location' => 'City View',
        'is_featured' => 1,
        'display_order' => 10,
    ],
    [
        'title' => 'The Executive Suite',
        'slug' => 'executive-suite',
        'price' => 750.00,
        'room_type' => 'Suite',
        'max_guests' => 3,
        'short_description' => 'Spacious living areas designed for both relaxation and productivity.',
        'description' => "Spacious living areas designed for both relaxation and productivity. Separate lounge access ensures privacy for business or leisure.\n\nIdeal for extended stays, the Executive Suite pairs residential comfort with hotel precision—so every moment feels effortless.",
        'main_image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDS8EVEZOokYBfKQdg__qSaYK7bEITHAoO_TCM1GBQoauWjXvmofmLgObkeUpBEhER9lfQkORJRNhKWrrocgAs4SQbj1jJcT_IKn0pcm7oi2YJPEocEwx4pk7rfhBQ7VA5dFqFBjfoQTpE_eGIDEf18fMFz4InNX3FUI7D7lRTaMy-v8LEnZL4VohGscYssoUPcIpr_xCwUJHYW4T55BTVUsuC1iUrLNslf1--oHjPSG28srKLAPNq-K9-W-0oVSK9w9g8m5QHZvio',
        'gallery_images' => [
            'https://lh3.googleusercontent.com/aida-public/AB6AXuD0gbBabGNVVrQVZDBZdAQJQhKHOK0McHLBzF1WjgPV9SihHDed6_k6zyIvsjFfzybjgpMNpzkNl8Hx2ApPPYR9oO2GmK0_UBHQ_x_NVA8SgkFoGD3BbR0MLGVfYqxtUw-7AfZA2Q37Uh6OWpmSMJva8h-Vi3STVkED0keQ3uCKcl13TzwCu7RaW4FvcM6x73RxpXfQVSzuCUEIbAbI7zfAksBtGE6YVm8D5KyaCFd-2KffaJj-UL457_LWgby5eLo9Pr7JT24Tvn0',
        ],
        'features' => ['65 SQM', 'Super King', 'Lounge Area', 'Minibar'],
        'size' => '65 SQM',
        'location' => 'Executive Floor',
        'is_featured' => 1,
        'display_order' => 20,
    ],
    [
        'title' => 'Presidential Penthouse',
        'slug' => 'presidential-penthouse',
        'price' => 2500.00,
        'room_type' => 'Penthouse',
        'max_guests' => 4,
        'short_description' => 'The crown jewel of Lusso—private terrace, butler service, and a marble spa sanctuary.',
        'description' => "The crown jewel of Lusso. Unrivaled luxury spanning the entire top floor, featuring a private terrace, butler service, and a marble spa sanctuary.\n\nDesigned for those who expect the exceptional—an immersive residence above the city with space to entertain, restore, and celebrate.",
        'main_image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCgtNLeA5QvIvRpJFDevlSr47gPxnDHXP1XIxO68wURBVq8I2RVudxeV1RO7m6RXGmtoRlFSpWVBU5lGBtOazpIDaRTMvIztQVzCP5ceY4AuHLwkwkQNvmq4NxwmUkB7mtIYcnraRVkqB8VVBNCjJemnhRVjANKJftwNbB8HNGSbTToD62qAb1Iq1P4Ww5th4CqVeDnqPPYPZ6yIzu789NZ_JU-c_Q2Y0caXKSPQICGNH6GWQhxJlkSemuk29TjtoJ0uSedDfVTljQ',
        'gallery_images' => [
            'https://lh3.googleusercontent.com/aida-public/AB6AXuDNwKPSSoEjR84lkptigGuTo5u3SAtv-P4iUPebdc2QgBSnqjC5GUeY4elChdvLxVHAJIbUztMF84HlWWzc5JOcVmu-n0hWZxIVgjFOdbqONJdL39bRchR_ISqOFPTufFWpcdQdHF5r4eiqHb8Hk4lJkgssIoSzMEnSzuz_qfHo90fuDOgNkmUVTjT173JVhiDj_F0Jh3ugvOPFMcBkdFTtNm-snjp7gC6gz0epQElZpCNIZqYBjXuax7zgAatH_MGXOrbmMAKRai0',
        ],
        'features' => ['180 SQM', 'Butler 24/7', 'Private Spa', 'Terrace'],
        'size' => '180 SQM',
        'location' => 'Penthouse',
        'is_featured' => 1,
        'display_order' => 30,
    ],
];

return [
    'site_settings' => $siteSettings,
    'page_sections' => $pageSections,
    'rooms' => $rooms,
];
