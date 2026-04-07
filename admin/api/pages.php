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
            $page = sanitize($_GET['page'] ?? '');
            $section = sanitize($_GET['section'] ?? '');
            if ($page && $section) {
                $stmt = $pdo->prepare("SELECT * FROM page_sections WHERE page = ? AND section_key = ?");
                $stmt->execute([$page, $section]);
                $sectionData = $stmt->fetch();
                if ($sectionData) jsonResponse(['success' => true, 'section' => $sectionData]);
                jsonResponse(['success' => false, 'message' => 'Section not found'], 404);
            }
            if ($page) {
                $stmt = $pdo->prepare("SELECT * FROM page_sections WHERE page = ? ORDER BY section_key");
                $stmt->execute([$page]);
                jsonResponse(['success' => true, 'sections' => $stmt->fetchAll()]);
            }
            jsonResponse(['success' => false, 'message' => 'Page parameter required'], 400);
            break;

        case 'POST':
            $headers = getAllHeaders();
            $csrfToken = $headers['X-CSRF-Token'] ?? ($_POST['csrf_token'] ?? '');
            if (!verifyCSRFToken($csrfToken)) {
                jsonResponse(['success' => false, 'message' => 'Invalid security token'], 403);
            }
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                jsonResponse(['success' => false, 'message' => 'Invalid JSON data'], 400);
            }
            if (!isset($data['page']) || !isset($data['section_key'])) {
                jsonResponse(['success' => false, 'message' => 'Page and section_key required'], 400);
            }

            $pageName = sanitize($data['page']);
            $sectionKey = sanitize($data['section_key']);
            $contentType = sanitize($data['content_type'] ?? 'text');
            $content = $data['content'] ?? '';

            $stmt = $pdo->prepare("INSERT INTO page_sections (page, section_key, content_type, content)
                                   VALUES (?, ?, ?, ?)
                                   ON DUPLICATE KEY UPDATE content = ?, content_type = ?, updated_at = NOW()");
            $stmt->execute([$pageName, $sectionKey, $contentType, $content, $content, $contentType]);
            jsonResponse(['success' => true, 'message' => 'Section updated successfully']);
            break;

        default:
            jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
    }
} catch(PDOException $e) {
    error_log("Pages API error: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Database error occurred'], 500);
}

