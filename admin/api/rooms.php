<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            if (isset($_GET['id'])) {
                $id = (int)$_GET['id'];
                $stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = ?");
                $stmt->execute([$id]);
                $room = $stmt->fetch();
                if ($room) {
                    $room['gallery_images'] = json_decode($room['gallery_images'] ?? '[]', true);
                    $room['features'] = json_decode($room['features'] ?? '[]', true);
                    $room['amenities'] = json_decode($room['amenities'] ?? '[]', true);
                    $room['tags'] = json_decode($room['tags'] ?? '[]', true);
                    $room['included_items'] = json_decode($room['included_items'] ?? '[]', true);
                    $room['good_to_know'] = json_decode($room['good_to_know'] ?? '{}', true);
                    if (!is_array($room['included_items'])) {
                        $room['included_items'] = [];
                    }
                    if (!is_array($room['good_to_know'])) {
                        $room['good_to_know'] = [];
                    }
                    jsonResponse(['success' => true, 'room' => $room]);
                }
                jsonResponse(['success' => false, 'message' => 'Room not found'], 404);
            }

            $stmt = $pdo->query("SELECT id,title,slug,price,is_active,is_featured,display_order,created_at FROM rooms ORDER BY display_order ASC, created_at DESC");
            jsonResponse(['success' => true, 'rooms' => $stmt->fetchAll()]);
            break;

        case 'POST':
            $headers = getAllHeaders();
            $csrfToken = $headers['X-CSRF-Token'] ?? ($_POST['csrf_token'] ?? '');
            if (!verifyCSRFToken($csrfToken)) {
                jsonResponse(['success' => false, 'message' => 'Invalid security token'], 403);
            }
            $data = json_decode(file_get_contents('php://input'), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                jsonResponse(['success' => false, 'message' => 'Invalid JSON'], 400);
            }

            $title = sanitize($data['title'] ?? '');
            $slug = generateSlug($data['slug'] ?? $title);
            $price = (float)($data['price'] ?? 0);
            $roomType = sanitize($data['room_type'] ?? '');
            $short = sanitize($data['short_description'] ?? '');
            $desc = sanitize($data['description'] ?? '');
            $mainImage = sanitize($data['main_image'] ?? '');
            $galleryImages = json_encode($data['gallery_images'] ?? []);
            $features = json_encode($data['features'] ?? []);
            $amenities = json_encode($data['amenities'] ?? []);
            $includedItems = json_encode($data['included_items'] ?? []);
            $goodToKnowRaw = $data['good_to_know'] ?? [];
            $goodToKnow = is_array($goodToKnowRaw) ? $goodToKnowRaw : [];
            $goodToKnow = array_intersect_key($goodToKnow, array_flip([
                'check_in', 'check_out', 'pets', 'floorplan_url', 'floorplan_label', 'tour_url',
            ]));
            foreach ($goodToKnow as $k => $v) {
                if (is_string($v)) {
                    $goodToKnow[$k] = sanitize($v);
                }
            }
            $goodToKnowJson = json_encode($goodToKnow);
            $urgencyMessage = sanitize($data['urgency_message'] ?? '');
            $size = sanitize($data['size'] ?? '');
            $maxGuests = (int)($data['max_guests'] ?? 0);
            $location = sanitize($data['location'] ?? '');
            $bookUrl = sanitize($data['book_url'] ?? '');
            $isActive = isset($data['is_active']) ? (int)$data['is_active'] : 1;
            $isFeatured = isset($data['is_featured']) ? (int)$data['is_featured'] : 0;
            $displayOrder = (int)($data['display_order'] ?? 0);

            $checkStmt = $pdo->prepare("SELECT id FROM rooms WHERE slug = ?");
            $checkStmt->execute([$slug]);
            if ($checkStmt->fetch()) {
                $slug = $slug . '-' . time();
            }

            $stmt = $pdo->prepare("INSERT INTO rooms (title,slug,price,room_type,short_description,description,main_image,gallery_images,features,amenities,included_items,good_to_know,urgency_message,size,max_guests,location,book_url,is_active,is_featured,display_order)
                                   VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([$title, $slug, $price, $roomType, $short, $desc, $mainImage, $galleryImages, $features, $amenities, $includedItems, $goodToKnowJson, $urgencyMessage, $size, $maxGuests, $location, $bookUrl, $isActive, $isFeatured, $displayOrder]);
            $newId = (int)$pdo->lastInsertId();
            jsonResponse(['success' => true, 'message' => 'Room created', 'room_id' => $newId]);
            break;

        case 'PUT':
            $headers = getAllHeaders();
            $csrfToken = $headers['X-CSRF-Token'] ?? '';
            if (!verifyCSRFToken($csrfToken)) {
                jsonResponse(['success' => false, 'message' => 'Invalid security token'], 403);
            }
            $id = (int)($_GET['id'] ?? 0);
            if (!$id) jsonResponse(['success' => false, 'message' => 'Invalid room ID'], 400);
            $data = json_decode(file_get_contents('php://input'), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                jsonResponse(['success' => false, 'message' => 'Invalid JSON'], 400);
            }

            $title = sanitize($data['title'] ?? '');
            $slug = generateSlug($data['slug'] ?? $title);
            $dupStmt = $pdo->prepare("SELECT id FROM rooms WHERE slug = ? AND id != ?");
            $dupStmt->execute([$slug, $id]);
            if ($dupStmt->fetch()) {
                $slug = $slug . '-' . time();
            }
            $price = (float)($data['price'] ?? 0);
            $roomType = sanitize($data['room_type'] ?? '');
            $short = sanitize($data['short_description'] ?? '');
            $desc = sanitize($data['description'] ?? '');
            $mainImage = sanitize($data['main_image'] ?? '');
            $galleryImages = json_encode($data['gallery_images'] ?? []);
            $features = json_encode($data['features'] ?? []);
            $amenities = json_encode($data['amenities'] ?? []);
            $includedItems = json_encode($data['included_items'] ?? []);
            $goodToKnowRaw = $data['good_to_know'] ?? [];
            $goodToKnow = is_array($goodToKnowRaw) ? $goodToKnowRaw : [];
            $goodToKnow = array_intersect_key($goodToKnow, array_flip([
                'check_in', 'check_out', 'pets', 'floorplan_url', 'floorplan_label', 'tour_url',
            ]));
            foreach ($goodToKnow as $k => $v) {
                if (is_string($v)) {
                    $goodToKnow[$k] = sanitize($v);
                }
            }
            $goodToKnowJson = json_encode($goodToKnow);
            $urgencyMessage = sanitize($data['urgency_message'] ?? '');
            $size = sanitize($data['size'] ?? '');
            $maxGuests = (int)($data['max_guests'] ?? 0);
            $location = sanitize($data['location'] ?? '');
            $bookUrl = sanitize($data['book_url'] ?? '');
            $isActive = isset($data['is_active']) ? (int)$data['is_active'] : 1;
            $isFeatured = isset($data['is_featured']) ? (int)$data['is_featured'] : 0;
            $displayOrder = (int)($data['display_order'] ?? 0);

            $stmt = $pdo->prepare("UPDATE rooms SET title=?, slug=?, price=?, room_type=?, short_description=?, description=?, main_image=?, gallery_images=?, features=?, amenities=?, included_items=?, good_to_know=?, urgency_message=?, size=?, max_guests=?, location=?, book_url=?, is_active=?, is_featured=?, display_order=? WHERE id=?");
            $stmt->execute([$title, $slug, $price, $roomType, $short, $desc, $mainImage, $galleryImages, $features, $amenities, $includedItems, $goodToKnowJson, $urgencyMessage, $size, $maxGuests, $location, $bookUrl, $isActive, $isFeatured, $displayOrder, $id]);
            jsonResponse(['success' => true, 'message' => 'Room updated']);
            break;

        case 'DELETE':
            $headers = getAllHeaders();
            $csrfToken = $headers['X-CSRF-Token'] ?? '';
            if (!verifyCSRFToken($csrfToken)) {
                jsonResponse(['success' => false, 'message' => 'Invalid security token'], 403);
            }
            $id = (int)($_GET['id'] ?? 0);
            if (!$id) {
                jsonResponse(['success' => false, 'message' => 'Invalid room ID'], 400);
            }
            $stmt = $pdo->prepare("DELETE FROM rooms WHERE id = ?");
            $stmt->execute([$id]);
            jsonResponse(['success' => true, 'message' => 'Room deleted']);
            break;

        default:
            jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
    }
} catch (PDOException $e) {
    error_log("Rooms API error: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Database error'], 500);
}

