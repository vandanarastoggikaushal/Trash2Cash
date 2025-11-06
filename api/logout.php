<?php
/**
 * Logout API endpoint
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    logout();
    echo json_encode(['ok' => true, 'message' => 'Logged out successfully']);
} else {
    // Also allow GET for simple logout links
    logout();
    header('Location: /');
    exit;
}

