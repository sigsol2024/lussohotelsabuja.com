<?php
/**
 * Settings API — save / load site_settings (BlueOrange-compatible)
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            $key = $_GET['key'] ?? null;

            if ($key) {
                $value = getSetting($key);
                jsonResponse(['success' => true, 'key' => $key, 'value' => $value]);
            }

            $stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings ORDER BY setting_key");
            $settings = $stmt->fetchAll();

            $settingsArray = [];
            foreach ($settings as $setting) {
                $settingsArray[$setting['setting_key']] = $setting['setting_value'];
            }

            jsonResponse(['success' => true, 'settings' => $settingsArray]);
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

            if (!is_array($data)) {
                jsonResponse(['success' => false, 'message' => 'Invalid data format'], 400);
            }

            $updated = [];
            foreach ($data as $key => $value) {
                if (updateSetting($key, $value)) {
                    $updated[] = $key;
                }
            }

            jsonResponse([
                'success' => true,
                'message' => count($updated) . ' setting(s) updated successfully',
                'updated' => $updated
            ]);
            break;

        default:
            jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
    }
} catch (PDOException $e) {
    error_log("Settings API error: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Database error occurred'], 500);
}
