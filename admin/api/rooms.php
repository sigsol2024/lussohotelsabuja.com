<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

/**
 * Ensure display_order is unique across rooms.
 * If requested order is taken (or invalid), pick the smallest available positive integer.
 */
function lussoResolveUniqueDisplayOrder(PDO $pdo, $requestedOrder, $excludeId = null) {
    $req = (int)$requestedOrder;
    if ($req < 1) {
        $req = 0;
    }

    $params = [];
    $where = "display_order > 0";
    if ($excludeId !== null) {
        $where .= " AND id <> ?";
        $params[] = (int)$excludeId;
    }

    $stmt = $pdo->prepare("SELECT display_order FROM rooms WHERE {$where}");
    $stmt->execute($params);
    $used = [];
    foreach ($stmt->fetchAll(PDO::FETCH_COLUMN, 0) as $v) {
        $n = (int)$v;
        if ($n > 0) $used[$n] = true;
    }

    if ($req > 0 && empty($used[$req])) {
        return $req;
    }

    $candidate = 1;
    while (isset($used[$candidate])) {
        $candidate++;
        if ($candidate > 1000000) { // sanity cap
            break;
        }
    }
    return $candidate;
}

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

            $stmt = $pdo->query("SELECT id,title,slug,price,is_active,is_featured,display_order,created_at FROM rooms ORDER BY (display_order = 0) ASC, display_order ASC, id ASC");
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

            if (!empty($data['normalize_display_order'])) {
                $pdo->beginTransaction();
                $rows = $pdo->query("SELECT id, display_order FROM rooms ORDER BY (display_order = 0) ASC, display_order ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
                $used = [];
                $nextFree = 1;
                $changed = 0;
                $updates = $pdo->prepare("UPDATE rooms SET display_order = ? WHERE id = ?");

                foreach ($rows as $row) {
                    $id = (int)($row['id'] ?? 0);
                    $cur = (int)($row['display_order'] ?? 0);
                    if ($id < 1) continue;

                    if ($cur > 0 && empty($used[$cur])) {
                        $used[$cur] = true;
                        continue;
                    }

                    while (isset($used[$nextFree])) {
                        $nextFree++;
                        if ($nextFree > 1000000) break;
                    }
                    $assigned = $nextFree;
                    $used[$assigned] = true;
                    $nextFree++;

                    $updates->execute([$assigned, $id]);
                    $changed++;
                }

                $pdo->commit();
                jsonResponse(['success' => true, 'message' => "Normalized display order for {$changed} room(s).", 'changed' => $changed]);
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
                $pdo->beginTransaction();
                $src['display_order'] = lussoResolveUniqueDisplayOrder($pdo, 0, null);
                $cols = array_keys($src);
                $placeholders = implode(',', array_fill(0, count($cols), '?'));
                $sql = 'INSERT INTO rooms (`' . implode('`,`', $cols) . '`) VALUES (' . $placeholders . ')';
                $ins = $pdo->prepare($sql);
                $ins->execute(array_values($src));
                $newId = (int)$pdo->lastInsertId();
                $pdo->commit();
                jsonResponse(['success' => true, 'message' => 'Duplicated as draft (title & slug end with -copy). Edit and set Active to publish.', 'room_id' => $newId, 'display_order' => (int)$src['display_order']]);
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
            $requestedDisplayOrder = (int)($data['display_order'] ?? 0);

            $checkStmt = $pdo->prepare("SELECT id FROM rooms WHERE slug = ?");
            $checkStmt->execute([$slug]);
            if ($checkStmt->fetch()) {
                $slug = $slug . '-' . time();
            }

            $pdo->beginTransaction();
            $displayOrder = lussoResolveUniqueDisplayOrder($pdo, $requestedDisplayOrder, null);
            $stmt = $pdo->prepare("INSERT INTO rooms (title,slug,price,room_type,short_description,description,main_image,gallery_images,features,amenities,included_items,good_to_know,urgency_message,size,max_guests,location,book_url,is_active,is_featured,display_order)
                                   VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([$title, $slug, $price, $roomType, $short, $desc, $mainImage, $galleryImages, $features, $amenities, $includedItems, $goodToKnowJson, $urgencyMessage, $size, $maxGuests, $location, $bookUrl, $isActive, $isFeatured, $displayOrder]);
            $newId = (int)$pdo->lastInsertId();
            $pdo->commit();
            $msg = ($displayOrder !== $requestedDisplayOrder && $requestedDisplayOrder > 0)
                ? ("Room created (display order adjusted to {$displayOrder})")
                : 'Room created';
            jsonResponse(['success' => true, 'message' => $msg, 'room_id' => $newId, 'display_order' => $displayOrder]);
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
            $requestedDisplayOrder = (int)($data['display_order'] ?? 0);

            $pdo->beginTransaction();
            $displayOrder = lussoResolveUniqueDisplayOrder($pdo, $requestedDisplayOrder, $id);
            $stmt = $pdo->prepare("UPDATE rooms SET title=?, slug=?, price=?, room_type=?, short_description=?, description=?, main_image=?, gallery_images=?, features=?, amenities=?, included_items=?, good_to_know=?, urgency_message=?, size=?, max_guests=?, location=?, book_url=?, is_active=?, is_featured=?, display_order=? WHERE id=?");
            $stmt->execute([$title, $slug, $price, $roomType, $short, $desc, $mainImage, $galleryImages, $features, $amenities, $includedItems, $goodToKnowJson, $urgencyMessage, $size, $maxGuests, $location, $bookUrl, $isActive, $isFeatured, $displayOrder, $id]);
            $pdo->commit();
            $msg = ($displayOrder !== $requestedDisplayOrder && $requestedDisplayOrder > 0)
                ? ("Room updated (display order adjusted to {$displayOrder})")
                : 'Room updated';
            jsonResponse(['success' => true, 'message' => $msg, 'display_order' => $displayOrder]);
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

