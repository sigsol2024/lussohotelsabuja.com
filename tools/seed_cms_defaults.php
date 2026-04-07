<?php
/**
 * Seed default site_settings, page_sections, and demo rooms for a fresh database.
 *
 * Safe to run multiple times: uses INSERT IGNORE (won’t overwrite existing keys/slugs).
 *
 * Usage (from project root):
 *   php tools/seed_cms_defaults.php
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    echo "CLI only.\n";
    exit(1);
}

$root = dirname(__DIR__);
require_once $root . '/admin/includes/config.php';

if (!isset($pdo) || $pdo === null) {
    fwrite(STDERR, "Database connection failed. Check admin/includes/config (or config.secrets.php).\n");
    exit(1);
}

$seed = require $root . '/includes/cms-seed.php';

if (!is_array($seed) || empty($seed['site_settings']) || empty($seed['page_sections'])) {
    fwrite(STDERR, "Invalid seed data.\n");
    exit(1);
}

$insertSetting = $pdo->prepare(
    'INSERT IGNORE INTO site_settings (setting_key, setting_value) VALUES (?, ?)'
);
$insertSection = $pdo->prepare(
    'INSERT IGNORE INTO page_sections (page, section_key, content_type, content) VALUES (?, ?, ?, ?)'
);

$settingsCount = 0;
$sectionsCount = 0;

try {
    $pdo->beginTransaction();

    foreach ($seed['site_settings'] as $key => $value) {
        $insertSetting->execute([(string)$key, (string)$value]);
        $settingsCount += $insertSetting->rowCount();
    }

    foreach ($seed['page_sections'] as $row) {
        if (count($row) !== 4) {
            continue;
        }
        $insertSection->execute([(string)$row[0], (string)$row[1], (string)$row[2], (string)$row[3]]);
        $sectionsCount += $insertSection->rowCount();
    }

    $roomsInserted = 0;
    if (!empty($seed['rooms']) && is_array($seed['rooms'])) {
        $roomSql = 'INSERT IGNORE INTO rooms (
            title, slug, price, room_type, max_guests, description, short_description,
            main_image, gallery_images, features, amenities, tags, included_items, good_to_know,
            book_url, urgency_message, size, location, is_featured, is_active, display_order
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )';
        $insRoom = $pdo->prepare($roomSql);

        $emptyAmenities = '[]';
        $emptyTags = '[]';
        $emptyIncluded = '[]';
        $emptyGood = '{}';

        foreach ($seed['rooms'] as $r) {
            $gallery = isset($r['gallery_images']) && is_array($r['gallery_images'])
                ? json_encode($r['gallery_images'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                : '[]';
            $features = isset($r['features']) && is_array($r['features'])
                ? json_encode($r['features'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                : '[]';

            $insRoom->execute([
                (string)($r['title'] ?? ''),
                (string)($r['slug'] ?? ''),
                (float)($r['price'] ?? 0),
                (string)($r['room_type'] ?? ''),
                (int)($r['max_guests'] ?? 0),
                (string)($r['description'] ?? ''),
                (string)($r['short_description'] ?? ''),
                (string)($r['main_image'] ?? ''),
                $gallery,
                $features,
                $emptyAmenities,
                $emptyTags,
                $emptyIncluded,
                $emptyGood,
                (string)($r['book_url'] ?? 'contact.php'),
                (string)($r['urgency_message'] ?? ''),
                (string)($r['size'] ?? ''),
                (string)($r['location'] ?? ''),
                (int)($r['is_featured'] ?? 1),
                1,
                (int)($r['display_order'] ?? 0),
            ]);
            $roomsInserted += $insRoom->rowCount();
        }
    }

    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollBack();
    fwrite(STDERR, "Seed failed: " . $e->getMessage() . "\n");
    exit(1);
}

echo "CMS seed completed.\n";
echo "- site_settings rows inserted (new keys only): {$settingsCount}\n";
echo "- page_sections rows inserted (new sections only): {$sectionsCount}\n";
echo "- rooms rows inserted (new slugs only): {$roomsInserted}\n";
