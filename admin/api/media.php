<?php
ob_start();

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['admin_id'])) {
    $_SESSION['last_activity'] = time();
}

requireLogin();
ob_clean();

header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'POST':
            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!verifyCSRFToken($csrfToken)) {
                jsonResponse(['success' => false, 'message' => 'Invalid security token. Please refresh and try again.'], 403);
            }
            $action = $_POST['action'] ?? '';
            if ($action === 'bulk_delete') {
                $idsRaw = $_POST['ids'] ?? '[]';
                $ids = is_string($idsRaw) ? json_decode($idsRaw, true) : $idsRaw;
                if (!is_array($ids) || empty($ids)) {
                    jsonResponse(['success' => false, 'message' => 'No items selected.'], 400);
                }
                $ids = array_values(array_unique(array_filter(array_map('intval', $ids), function ($id) {
                    return $id > 0;
                })));
                if (empty($ids)) {
                    jsonResponse(['success' => false, 'message' => 'Invalid selection.'], 400);
                }
                if (count($ids) > 100) {
                    jsonResponse(['success' => false, 'message' => 'Too many items (max 100 per request).'], 400);
                }
                $deleted = 0;
                foreach ($ids as $mid) {
                    $stmt = $pdo->prepare('SELECT file_path FROM media WHERE id = ?');
                    $stmt->execute([$mid]);
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    if (!$row) {
                        continue;
                    }
                    deleteFile($row['file_path']);
                    $del = $pdo->prepare('DELETE FROM media WHERE id = ?');
                    $del->execute([$mid]);
                    if ($del->rowCount() > 0) {
                        $deleted++;
                    }
                }
                jsonResponse([
                    'success' => true,
                    'message' => $deleted === 1 ? '1 file deleted.' : ($deleted . ' files deleted.'),
                    'deleted' => $deleted
                ]);
            }
            if (!isset($_FILES['file'])) {
                jsonResponse(['success' => false, 'message' => 'No file uploaded.'], 400);
            }
            $file = $_FILES['file'];
            if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
                jsonResponse(['success' => false, 'message' => 'Upload error.'], 400);
            }

            $subdirectory = sanitize($_POST['subdirectory'] ?? '');
            $result = uploadImage($file, $subdirectory);
            if (!$result['success']) {
                jsonResponse(['success' => false, 'message' => implode(', ', $result['errors'] ?? ['Upload failed'])], 400);
            }

            $stmt = $pdo->prepare("INSERT INTO media (filename, original_name, file_path, file_type, file_size, uploaded_by)
                                   VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $result['filename'],
                $file['name'],
                $result['path'],
                $file['type'] ?? '',
                $file['size'] ?? 0,
                getCurrentUserId()
            ]);
            $mediaId = $pdo->lastInsertId();

            $imageUrl = SITE_URL . $result['path'];
            jsonResponse([
                'success' => true,
                'message' => 'File uploaded successfully',
                'media' => [
                    'id' => $mediaId,
                    'path' => $result['path'],
                    'url' => $imageUrl,
                    'filename' => $result['filename'],
                    'original_name' => $file['name'] ?? ''
                ]
            ]);
            break;

        case 'GET':
            $page = max(1, intval($_GET['page'] ?? 1));
            $perPage = intval($_GET['per_page'] ?? 20);
            if ($perPage < 1) $perPage = 20;
            if ($perPage > 60) $perPage = 60;
            $offset = ($page - 1) * $perPage;

            $search = sanitize($_GET['search'] ?? '');
            $where = [];
            $params = [];
            if ($search) {
                $where[] = '(m.original_name LIKE ? OR m.filename LIKE ?)';
                $searchTerm = "%{$search}%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

            $countStmt = $pdo->prepare("SELECT COUNT(*) FROM media m {$whereClause}");
            $countStmt->execute($params);
            $total = (int)$countStmt->fetchColumn();

            $stmt = $pdo->prepare("SELECT m.* FROM media m {$whereClause} ORDER BY m.uploaded_at DESC LIMIT ? OFFSET ?");
            $params[] = $perPage;
            $params[] = $offset;
            $stmt->execute($params);
            $media = $stmt->fetchAll();

            foreach ($media as &$item) {
                $item['url'] = rtrim(SITE_URL, '/') . '/' . ltrim((string)($item['file_path'] ?? ''), '/');
            }
            unset($item);

            jsonResponse([
                'success' => true,
                'media' => $media,
                'pagination' => [
                    'page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'pages' => (int)ceil($total / $perPage)
                ]
            ]);
            break;

        case 'DELETE':
            $csrfToken = $_GET['csrf_token'] ?? '';
            if (!verifyCSRFToken($csrfToken)) {
                jsonResponse(['success' => false, 'message' => 'Invalid security token.'], 403);
            }
            $id = (int)($_GET['id'] ?? 0);
            if ($id <= 0) {
                jsonResponse(['success' => false, 'message' => 'Invalid media ID.'], 400);
            }
            $stmt = $pdo->prepare('SELECT file_path FROM media WHERE id = ?');
            $stmt->execute([$id]);
            $media = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$media) {
                jsonResponse(['success' => false, 'message' => 'Media not found.'], 404);
            }
            deleteFile($media['file_path']);
            try {
                $stmt = $pdo->prepare('DELETE FROM media WHERE id = ?');
                $stmt->execute([$id]);
                jsonResponse(['success' => true, 'message' => 'Media deleted successfully.']);
            } catch (PDOException $e) {
                error_log('Media delete DB error: ' . $e->getMessage());
                jsonResponse(['success' => false, 'message' => 'Failed to delete media record.'], 500);
            }

        default:
            jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
    }
} catch(PDOException $e) {
    error_log("Media API error: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Database error occurred'], 500);
} catch(Exception $e) {
    error_log("Media API error: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'An error occurred'], 500);
}

