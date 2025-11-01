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
$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$message = trim($data['message'] ?? '');

// Validate required fields before processing
if (empty($name) || empty($email) || empty($message)) {
  http_response_code(400);
  echo json_encode([ 'ok' => false, 'error' => 'Missing required fields' ]);
  exit;
}

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
  // Use Hostinger's default mail configuration - let Hostinger handle the From address
  // This works better when sending to external email accounts like M365
  $headers = "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
  // Let Hostinger use its default From address (usually based on server/domain config)
  // Just specify Reply-To so replies go to the form submitter
  $headers .= "Reply-To: " . htmlspecialchars($name, ENT_QUOTES) . " <" . htmlspecialchars($email, ENT_QUOTES) . ">\r\n";
  $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
  
  // Clear any previous errors
  error_clear_last();
  
  // Send email - let Hostinger choose the From address based on its configuration
  $emailSent = mail($to, $subject, $emailMessage, $headers);
  
  // Log detailed information
  if ($emailSent) {
    error_log('[email] mail() returned TRUE - Contact message ID: ' . $id);
    error_log('[email] Details - To: ' . $to . ' | Reply-To: ' . $email . ' | Subject: ' . $subject);
    error_log('[email] Message length: ' . strlen($emailMessage) . ' bytes');
    error_log('[email] Using Hostinger default mail configuration');
  } else {
    $lastError = error_get_last();
    $errorMsg = 'Unknown error';
    if ($lastError && isset($lastError['message'])) {
      $errorMsg = $lastError['message'];
    }
    error_log('[email] FAILED - mail() returned FALSE. ID: ' . $id . ' | Error: ' . $errorMsg);
    
    // Try alternative - even simpler headers
    $simpleHeaders = "Reply-To: " . htmlspecialchars($email, ENT_QUOTES) . "\r\n";
    $emailSent = mail($to, $subject, $emailMessage, $simpleHeaders);
    if ($emailSent) {
      error_log('[email] SUCCESS with minimal headers');
    }
  }
}

echo json_encode([ 'ok' => true, 'id' => $id ]);
?>

