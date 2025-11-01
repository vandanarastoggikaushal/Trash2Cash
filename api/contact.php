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

// Create email message
$emailMessage = "You have received a new message from the Trash2Cash contact form.\n\n";
$emailMessage .= "Name: " . htmlspecialchars($name) . "\n";
$emailMessage .= "Email: " . htmlspecialchars($email) . "\n\n";
$emailMessage .= "Message:\n" . htmlspecialchars($message) . "\n\n";
$emailMessage .= "---\n";
$emailMessage .= "Submitted at: " . date('Y-m-d H:i:s') . " NZST\n";
$emailMessage .= "Reference ID: " . $id;

// Validate email addresses first
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  error_log('[email] Invalid email address: ' . $email);
  $emailSent = false;
} else {
  // Prepare headers - important for Hostinger
  // Use array format and proper line endings
  $headers = [];
  $headers[] = 'MIME-Version: 1.0';
  $headers[] = 'Content-Type: text/plain; charset=UTF-8';
  $headers[] = 'From: Trash2Cash NZ <noreply@trash2cash.co.nz>';
  $headers[] = 'Reply-To: ' . htmlspecialchars($name, ENT_QUOTES) . ' <' . htmlspecialchars($email, ENT_QUOTES) . '>';
  $headers[] = 'X-Mailer: PHP/' . phpversion();
  $headers[] = 'X-Priority: 3';
  $headers[] = 'Return-Path: noreply@trash2cash.co.nz';
  
  // Join headers with proper line endings
  $headersString = implode("\r\n", $headers);
  
  // Clear any previous errors
  error_clear_last();
  
  // Try to send email - remove @ to see errors
  $emailSent = mail($to, $subject, $emailMessage, $headersString);
  
  // Log detailed information
  if ($emailSent) {
    error_log('[email] SUCCESS - Contact message sent. ID: ' . $id . ' | To: ' . $to . ' | From: ' . $email);
  } else {
    $lastError = error_get_last();
    $errorMsg = 'Unknown error';
    if ($lastError && isset($lastError['message'])) {
      $errorMsg = $lastError['message'];
    }
    error_log('[email] FAILED - Contact message NOT sent. ID: ' . $id . ' | To: ' . $to . ' | Error: ' . $errorMsg);
    
    // Also try alternative header format (sometimes needed on Hostinger)
    $altHeaders = "MIME-Version: 1.0\r\n";
    $altHeaders .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $altHeaders .= "From: noreply@trash2cash.co.nz\r\n";
    $altHeaders .= "Reply-To: " . htmlspecialchars($email, ENT_QUOTES) . "\r\n";
    
    $emailSent = mail($to, $subject, $emailMessage, $altHeaders);
    if ($emailSent) {
      error_log('[email] SUCCESS with alternative headers. ID: ' . $id);
    }
  }
}

echo json_encode([ 'ok' => true, 'id' => $id ]);
?>

