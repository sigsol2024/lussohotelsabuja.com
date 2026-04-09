<?php
/**
 * Content Loader Helper
 * Functions to load dynamic content from database for frontend
 */

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

require_once BASE_PATH . '/includes/url.php';

// Include database config if not already included
if (!isset($pdo)) {
    try {
        require_once BASE_PATH . '/admin/includes/config.php';
    } catch (Exception $e) {
        // If config fails to load, set $pdo to null so functions can handle gracefully
        $pdo = null;
        error_log("Failed to load config: " . $e->getMessage());
    }
}

/**
 * Get page section content
 */
function getPageSection($page, $sectionKey, $default = '') {
    global $pdo;
    
    if (!isset($pdo) || $pdo === null) {
        return $default;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT content FROM page_sections WHERE page = ? AND section_key = ?");
        $stmt->execute([$page, $sectionKey]);
        $result = $stmt->fetch();
        
        return $result ? $result['content'] : $default;
    } catch(PDOException $e) {
        error_log("Content loader error: " . $e->getMessage());
        return $default;
    }
}

/**
 * Homepage hero title: replace legacy boxed span (border/rounded) with text-stroke class.
 * Stored DB content may still have the old markup after the design change.
 */
function lusso_normalize_home_hero_title_html(string $html): string {
    $legacyClass = 'class="italic text-primary border border-white/90 rounded-lg px-3 py-1 inline-block shadow-sm"';
    $strokeClass = 'class="italic text-primary lusso-hero-accent-text"';
    $html = str_replace($legacyClass, $strokeClass, $html);
    // Any other variant that still uses border-white/90 on the accent span
    $replaced = preg_replace(
        '/class="([^"]*\bitalic\b[^"]*\btext-primary\b)[^"]*\bborder-white\/90[^"]*"/',
        'class="italic text-primary lusso-hero-accent-text"',
        $html
    );
    return is_string($replaced) ? $replaced : $html;
}

/**
 * Get site setting
 */
function getSiteSetting($key, $default = '') {
    global $pdo;
    
    if (!isset($pdo) || $pdo === null) {
        return $default;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        
        return $result ? $result['setting_value'] : $default;
    } catch(PDOException $e) {
        return $default;
    }
}

/**
 * Get all rooms (with optional filters)
 */
function getRooms($filters = []) {
    global $pdo;
    
    if (!isset($pdo) || $pdo === null) {
        return [];
    }
    
    $where = [];
    $params = [];
    
    if (isset($filters['is_active'])) {
        $where[] = "is_active = ?";
        $params[] = intval($filters['is_active']);
    }
    
    if (isset($filters['is_featured'])) {
        $where[] = "is_featured = ?";
        $params[] = intval($filters['is_featured']);
    }
    
    if (isset($filters['limit'])) {
        $limit = intval($filters['limit']);
    } else {
        $limit = 1000;
    }
    
    $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
    
    try {
        // Sort by display_order (non-zero first), then stable by id.
        // This avoids "most recently created/edited" looking order when many rooms share display_order=0.
        $stmt = $pdo->prepare("SELECT * FROM rooms {$whereClause} ORDER BY (display_order = 0) ASC, display_order ASC, id ASC LIMIT ?");
        $params[] = $limit;
        $stmt->execute($params);
        $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Decode JSON fields
        foreach ($rooms as &$roomItem) {
            $roomItem['gallery_images'] = json_decode($roomItem['gallery_images'] ?? '[]', true);
            $roomItem['features'] = json_decode($roomItem['features'] ?? '[]', true);
            $roomItem['amenities'] = json_decode($roomItem['amenities'] ?? '[]', true);
            $roomItem['tags'] = json_decode($roomItem['tags'] ?? '[]', true);
            $roomItem['included_items'] = json_decode($roomItem['included_items'] ?? '[]', true);
            $roomItem['good_to_know'] = json_decode($roomItem['good_to_know'] ?? '{}', true);
        }
        unset($roomItem);
        
        return $rooms;
    } catch(PDOException $e) {
        error_log("Get rooms error: " . $e->getMessage());
        return [];
    }
}

/**
 * Featured rooms for homepage carousel: prefer is_featured, else fill from active rooms.
 */
function getFeaturedRoomsForHome($limit = 12) {
    $limit = max(1, (int)$limit);
    $rooms = getRooms(['is_active' => 1, 'is_featured' => 1, 'limit' => $limit]);
    if (empty($rooms)) {
        $rooms = getRooms(['is_active' => 1, 'limit' => min(8, $limit)]);
    }
    return $rooms;
}

/**
 * Get single room by slug
 */
function getRoomBySlug($slug) {
    global $pdo;
    
    if (!isset($pdo) || $pdo === null) {
        return null;
    }
    
    $slug = preg_replace('/[^a-z0-9\-]/', '', strtolower(trim($slug)));
    if (empty($slug)) {
        return null;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM rooms WHERE slug = ? AND is_active = 1 LIMIT 1");
        $stmt->execute([$slug]);
        
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $room = $stmt->fetch();
        
        if ($room) {
            $room['gallery_images'] = json_decode($room['gallery_images'] ?? '[]', true);
            $room['features'] = json_decode($room['features'] ?? '[]', true);
            $room['amenities'] = json_decode($room['amenities'] ?? '[]', true);
            $room['tags'] = json_decode($room['tags'] ?? '[]', true);
            $room['included_items'] = json_decode($room['included_items'] ?? '[]', true);
            $room['good_to_know'] = json_decode($room['good_to_know'] ?? '{}', true);
        }
        
        return $room ? $room : null;
    } catch(PDOException $e) {
        error_log("Get room by slug error: " . $e->getMessage());
        return null;
    }
}

/**
 * Escape output for HTML
 */
function e($string) {
    if (is_array($string) || is_object($string)) {
        return '';
    }
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

