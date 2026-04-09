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

            if (!empty($data['duplicate_from_id'])) {
                $srcId = (int)$data['duplicate_from_id'];
                if ($srcId < 1) {
                    jsonResponse(['success' => false, 'message' => 'Invalid room ID'], 400);
                }
                $srcStmt = $pdo->prepare('SELECT * FROM rooms WHERE id = ?');
                $srcStmt->execute([$srcId]);
                $src = $srcStmt->fetch(PDO::FETCH_ASSOC);
                if (!$src) {
                    jsonResponse(['success' => false, 'message' => 'Room not found'], 404);
                }
                $suffix = ' -copy';
                $baseTitle = (string)$src['title'];
                $newTitle = $baseTitle . $suffix;
                if (function_exists('mb_strlen') && mb_strlen($newTitle) > 255) {
                    $newTitle = mb_substr($baseTitle, 0, max(0, 255 - mb_strlen($suffix))) . $suffix;
                } elseif (strlen($newTitle) > 255) {
                    $newTitle = substr($baseTitle, 0, 255 - strlen($suffix)) . $suffix;
                }
                $slugBase = generateSlug((string)$src['slug'] . '-copy');
                $candidate = $slugBase !== '' ? $slugBase : 'room-copy';
                $n = 2;
                while (true) {
                    $dupChk = $pdo->prepare('SELECT id FROM rooms WHERE slug = ?');
                    $dupChk->execute([$candidate]);
                    if (!$dupChk->fetch()) {
                        break;
                    }
                    $candidate = $slugBase . '-' . $n;
                    $n++;
                    if (strlen($candidate) > 255) {
                        $candidate = generateSlug($slugBase . '-' . time());
                        break;
                    }
                }
                unset($src['id'], $src['created_at'], $src['updated_at']);
                $src['title'] = trim((string)$newTitle);
                $src['slug'] = $candidate;
                $src['is_active'] = 0;
                $src['is_featured'] = 0;
                $maxOrder = (int)$pdo->query('SELECT COALESCE(MAX(display_order), 0) FROM rooms')->fetchColumn();
                $src['display_order'] = $maxOrder + 1;
                $cols = array_keys($src);
                $placeholders = implode(',', array_fill(0, count($cols), '?'));
                $sql = 'INSERT INTO rooms (`' . implode('`,`', $cols) . '`) VALUES (' . $placeholders . ')';
                $ins = $pdo->prepare($sql);
                $ins->execute(array_values($src));
                $newId = (int)$pdo->lastInsertId();
                jsonResponse(['success' => true, 'message' => 'Duplicated as draft (title & slug end with -copy). Edit and set Active to publish.', 'room_id' => $newId]);
            }

            $title = trim((string)($data['title'] ?? ''));
            $slug = generateSlug($data['slug'] ?? $title);
            $price = (float)($data['price'] ?? 0);
            $roomType = trim((string)($data['room_type'] ?? ''));
            $short = trim((string)($data['short_description'] ?? ''));
            $desc = trim((string)($data['description'] ?? ''));
            $mainImage = trim((string)($data['main_image'] ?? ''));
            $galleryImages = json_encode($data['gallery_images'] ?? []);
            $features = json_encode($data['features'] ?? []);
            $amenities = json_encode($data['amenities'] ?? []);
            $includedItems = json_encode($data['included_items'] ?? []);
            $goodToKnowRaw = $data['good_to_know'] ?? [];
            $goodToKnow = is_array($goodToKnowRaw) ? $goodToKnowRaw : [];
            $goodToKnow = array_intersect_key($goodToKnow, array_flip([]));
            foreach ($goodToKnow as $k => $v) {
                if (is_string($v)) {
                    $goodToKnow[$k] = trim($v);
                } elseif (is_numeric($v)) {
                    $goodToKnow[$k] = (string)$v;
                } else {
                    $goodToKnow[$k] = '';
                }
            }
            $goodToKnowJson = json_encode($goodToKnow);
            $urgencyMessage = trim((string)($data['urgency_message'] ?? ''));
            $size = trim((string)($data['size'] ?? ''));
            $maxGuests = (int)($data['max_guests'] ?? 0);
            $location = trim((string)($data['location'] ?? ''));
            $bookUrl = trim((string)($data['book_url'] ?? ''));
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

            $title = trim((string)($data['title'] ?? ''));
            $slug = generateSlug($data['slug'] ?? $title);
            $dupStmt = $pdo->prepare("SELECT id FROM rooms WHERE slug = ? AND id != ?");
            $dupStmt->execute([$slug, $id]);
            if ($dupStmt->fetch()) {
                $slug = $slug . '-' . time();
            }
            $price = (float)($data['price'] ?? 0);
            $roomType = trim((string)($data['room_type'] ?? ''));
            $short = trim((string)($data['short_description'] ?? ''));
            $desc = trim((string)($data['description'] ?? ''));
            $mainImage = trim((string)($data['main_image'] ?? ''));
            $galleryImages = json_encode($data['gallery_images'] ?? []);
            $features = json_encode($data['features'] ?? []);
            $amenities = json_encode($data['amenities'] ?? []);
            $includedItems = json_encode($data['included_items'] ?? []);
            $goodToKnowRaw = $data['good_to_know'] ?? [];
            $goodToKnow = is_array($goodToKnowRaw) ? $goodToKnowRaw : [];
            $goodToKnow = array_intersect_key($goodToKnow, array_flip([]));
            foreach ($goodToKnow as $k => $v) {
                if (is_string($v)) {
                    $goodToKnow[$k] = trim($v);
                } elseif (is_numeric($v)) {
                    $goodToKnow[$k] = (string)$v;
                } else {
                    $goodToKnow[$k] = '';
                }
            }
            $goodToKnowJson = json_encode($goodToKnow);
            $urgencyMessage = trim((string)($data['urgency_message'] ?? ''));
            $size = trim((string)($data['size'] ?? ''));
            $maxGuests = (int)($data['max_guests'] ?? 0);
            $location = trim((string)($data['location'] ?? ''));
            $bookUrl = trim((string)($data['book_url'] ?? ''));
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

