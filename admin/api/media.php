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
            $perPage = 20;
            $offset = ($page - 1) * $perPage;

            $search = sanitize($_GET['search'] ?? '');
            $where = [];
            $params = [];
            if ($search) {
                $where[] = "(original_name LIKE ? OR filename LIKE ?)";
                $searchTerm = "%{$search}%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

            $countStmt = $pdo->prepare("SELECT COUNT(*) FROM media {$whereClause}");
            $countStmt->execute($params);
            $total = (int)$countStmt->fetchColumn();

            $stmt = $pdo->prepare("SELECT * FROM media {$whereClause} ORDER BY uploaded_at DESC LIMIT ? OFFSET ?");
            $params[] = $perPage;
            $params[] = $offset;
            $stmt->execute($params);
            $media = $stmt->fetchAll();

            foreach ($media as &$item) {
                $item['url'] = SITE_URL . $item['file_path'];
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

