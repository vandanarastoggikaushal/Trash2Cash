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
if (!is_array($data)) {
  http_response_code(400);
  echo json_encode([ 'ok' => false, 'error' => 'Invalid body' ]);
  exit;
}

$id = bin2hex(random_bytes(6));
$name = $data['name'] ?? '';
$email = $data['email'] ?? '';
$message = $data['message'] ?? '';

$record = [
  'id' => $id,
  'createdAt' => gmdate('c'),
  'name' => $name,
  'email' => $email,
  'message' => $message
];

// Save to JSON file
$dir = __DIR__ . '/../data';
@mkdir($dir, 0755, true);
$file = $dir . '/messages.json';

$existing = [];
if (file_exists($file)) {
  $content = file_get_contents($file);
  $json = json_decode($content, true);
  if (is_array($json)) { $existing = $json; }
}

$existing[] = $record;
file_put_contents($file, json_encode($existing, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

// Send email
$to = 'collect@trash2cash.co.nz';
$subject = 'New Contact Form Message from ' . htmlspecialchars($name);
$emailMessage = "You have received a new message from the Trash2Cash contact form.\n\n";
$emailMessage .= "Name: " . htmlspecialchars($name) . "\n";
$emailMessage .= "Email: " . htmlspecialchars($email) . "\n\n";
$emailMessage .= "Message:\n" . htmlspecialchars($message) . "\n\n";
$emailMessage .= "---\n";
$emailMessage .= "Submitted at: " . gmdate('Y-m-d H:i:s') . " UTC\n";
$emailMessage .= "Reference ID: " . $id;

$headers = "From: noreply@trash2cash.co.nz\r\n";
$headers .= "Reply-To: " . htmlspecialchars($email) . "\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

$emailSent = @mail($to, $subject, $emailMessage, $headers);
error_log('[email] New contact message: ' . json_encode($record) . ' | Email sent: ' . ($emailSent ? 'yes' : 'no'));

echo json_encode([ 'ok' => true, 'id' => $id ]);
?>

