<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$token = getBearerTokenFromHeaders();
if (empty($token)) {
    $input = json_decode(file_get_contents('php://input'), true);
    if (is_array($input)) {
        $token = $input['token'] ?? '';
    }
}

if (!empty($token)) {
    $userRow = fetchUserRowByApiToken($token);
    if ($userRow && !empty($userRow['id'])) {
        invalidateApiToken($userRow['id']);
    }
}

echo json_encode(['success' => true]);

