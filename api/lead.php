<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode([ 'ok' => false, 'error' => 'Method not allowed' ]);
  exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);
if (!is_array($data) && isset($_POST['payload'])) {
  $data = json_decode($_POST['payload'], true);
}
if (!is_array($data) && isset($_POST['p'])) { // alternate key to bypass some WAF rules
  $data = json_decode($_POST['p'], true);
}
if (!is_array($data)) {
  http_response_code(400);
  echo json_encode([ 'ok' => false, 'error' => 'Invalid body' ]);
  exit;
}

if (empty($data['person']['fullName']) || empty($data['person']['email'])) {
  http_response_code(400);
  echo json_encode([ 'ok' => false, 'error' => 'Missing required fields' ]);
  exit;
}

$id = bin2hex(random_bytes(6));
$data['id'] = $id;
$data['createdAt'] = gmdate('c');

$dir = __DIR__ . '/../data';
@mkdir($dir, 0755, true);
$file = $dir . '/leads.json';

$existing = [];
if (file_exists($file)) {
  $content = file_get_contents($file);
  $json = json_decode($content, true);
  if (is_array($json)) { $existing = $json; }
}

$existing[] = $data;
file_put_contents($file, json_encode($existing, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
error_log('[email] New lead submitted: ' . json_encode($data));

echo json_encode([ 'ok' => true, 'id' => $id ]);
?>

