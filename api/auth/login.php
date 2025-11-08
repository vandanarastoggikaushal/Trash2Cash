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

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid JSON payload']);
    exit;
}

$username = trim($input['username'] ?? '');
$password = $input['password'] ?? '';

if ($username === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Username and password are required']);
    exit;
}

$user = verifyUser($username, $password);
if (!$user) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Invalid username or password']);
    exit;
}

$token = generateApiToken($user['id']);
if (!$token) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Unable to generate access token']);
    exit;
}

$userRow = fetchUserRowById($user['id']);
if (!$userRow) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Unable to load user profile']);
    exit;
}

$profile = formatUserForApi($userRow);

echo json_encode([
    'success' => true,
    'token' => $token,
    'user' => $profile,
]);

