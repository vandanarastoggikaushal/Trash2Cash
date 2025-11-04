<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode([ 'ok' => false, 'error' => 'Method not allowed' ]);
  exit;
}

// Handle form-encoded data (from mobile app) - check POST first
$data = null;
$rawInput = file_get_contents('php://input');
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';

// First try $_POST (works on most servers)
if (isset($_POST['payload'])) {
  $data = json_decode($_POST['payload'], true);
}
if (!is_array($data) && isset($_POST['p'])) {
  $data = json_decode($_POST['p'], true);
}

// If $_POST is empty, parse form-encoded data manually (for PHP built-in server)
if (!is_array($data) && strpos($contentType, 'application/x-www-form-urlencoded') !== false && !empty($rawInput)) {
  parse_str($rawInput, $parsed);
  if (isset($parsed['payload'])) {
    $data = json_decode($parsed['payload'], true);
  }
  if (!is_array($data) && isset($parsed['p'])) {
    $data = json_decode($parsed['p'], true);
  }
}

// Also handle raw JSON (from web form)
if (!is_array($data) && !empty($rawInput)) {
  $data = json_decode($rawInput, true);
}

if (!is_array($data)) {
  http_response_code(400);
  echo json_encode([ 'ok' => false, 'error' => 'Invalid body', 'debug' => [
    'has_post_payload' => isset($_POST['payload']),
    'has_post_p' => isset($_POST['p']),
    'content_type' => $contentType,
    'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'not set',
    'raw_input_length' => strlen($rawInput),
    'raw_input_preview' => substr($rawInput, 0, 100),
  ]]);
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

// Send email notification
$to = 'collect@trash2cash.co.nz';
$subject = 'New Pickup Request - ' . htmlspecialchars($data['person']['fullName'] ?? 'Unknown');

// Build email message
$emailMessage = "New Pickup Request Submitted\n";
$emailMessage .= "====================================\n\n";
$emailMessage .= "REFERENCE ID: " . $id . "\n";
$emailMessage .= "Submitted at: " . date('Y-m-d H:i:s') . " NZST\n\n";

// Person details
$emailMessage .= "PERSON DETAILS:\n";
$emailMessage .= "---------------\n";
$emailMessage .= "Name: " . htmlspecialchars($data['person']['fullName'] ?? '') . "\n";
$emailMessage .= "Email: " . htmlspecialchars($data['person']['email'] ?? '') . "\n";
$emailMessage .= "Phone: " . htmlspecialchars($data['person']['phone'] ?? '') . "\n";
if (!empty($data['person']['marketingOptIn'])) {
  $emailMessage .= "Marketing opt-in: Yes\n";
}
$emailMessage .= "\n";

// Address details
if (isset($data['address'])) {
  $emailMessage .= "ADDRESS:\n";
  $emailMessage .= "--------\n";
  $emailMessage .= "Street: " . htmlspecialchars($data['address']['street'] ?? '') . "\n";
  $emailMessage .= "Suburb: " . htmlspecialchars($data['address']['suburb'] ?? '') . "\n";
  $emailMessage .= "City: " . htmlspecialchars($data['address']['city'] ?? '') . "\n";
  $emailMessage .= "Postcode: " . htmlspecialchars($data['address']['postcode'] ?? '') . "\n";
  if (!empty($data['address']['accessNotes'])) {
    $emailMessage .= "Access notes: " . htmlspecialchars($data['address']['accessNotes']) . "\n";
  }
  $emailMessage .= "\n";
}

// Pickup details
$totalApplianceCredit = 0;
$totalCansReward = 0;

if (isset($data['pickup'])) {
  $emailMessage .= "PICKUP DETAILS:\n";
  $emailMessage .= "---------------\n";
  $emailMessage .= "Type: " . htmlspecialchars($data['pickup']['type'] ?? '') . "\n\n";
  
  // Cans details
  if (!empty($data['pickup']['cansEstimate'])) {
    $cansEstimate = intval($data['pickup']['cansEstimate']);
    $totalCansReward = floor($cansEstimate / 100);
    $emailMessage .= "CANS:\n";
    $emailMessage .= "  Estimate: " . number_format($cansEstimate) . " cans\n";
    $emailMessage .= "  Estimated reward: $" . $totalCansReward . " (at $1 per 100 cans)\n\n";
  }
  
  // Appliances details
  if (!empty($data['pickup']['appliances']) && is_array($data['pickup']['appliances'])) {
    $emailMessage .= "APPLIANCES:\n";
    
    // Load appliance credits from config
    require_once __DIR__ . '/../includes/config.php';
    $applianceMap = [];
    foreach ($APPLIANCE_CREDITS as $app) {
      $applianceMap[$app['slug']] = $app;
    }
    
    foreach ($data['pickup']['appliances'] as $appliance) {
      $qty = intval($appliance['qty'] ?? 0);
      if ($qty > 0) {
        $slug = $appliance['slug'] ?? '';
        $label = $applianceMap[$slug]['label'] ?? $slug;
        $credit = $applianceMap[$slug]['credit'] ?? 0;
        $itemTotal = $credit * $qty;
        $totalApplianceCredit += $itemTotal;
        
        $emailMessage .= "  " . htmlspecialchars($label) . ": " . $qty . " x $" . $credit . " = $" . $itemTotal . "\n";
      }
    }
    if ($totalApplianceCredit > 0) {
      $emailMessage .= "  TOTAL APPLIANCE CREDIT: $" . $totalApplianceCredit . "\n";
    }
    $emailMessage .= "\n";
  }
  
  if (!empty($data['pickup']['preferredDate'])) {
    $emailMessage .= "Preferred date: " . htmlspecialchars($data['pickup']['preferredDate']) . "\n";
  }
  if (!empty($data['pickup']['preferredWindow'])) {
    $emailMessage .= "Preferred time: " . htmlspecialchars($data['pickup']['preferredWindow']) . "\n";
  }
  
  // Calculate total reward
  $totalReward = $totalCansReward + $totalApplianceCredit;
  
  if ($totalReward > 0) {
    $emailMessage .= "\n";
    $emailMessage .= "TOTAL ESTIMATED REWARD: $" . $totalReward . "\n";
    if ($totalCansReward > 0) {
      $emailMessage .= "  - Cans reward: $" . $totalCansReward . "\n";
    }
    if ($totalApplianceCredit > 0) {
      $emailMessage .= "  - Appliance credits: $" . $totalApplianceCredit . "\n";
    }
  }
  $emailMessage .= "\n";
}

// Payout details
if (isset($data['payout'])) {
  $emailMessage .= "PAYOUT PREFERENCE:\n";
  $emailMessage .= "------------------\n";
  $emailMessage .= "Method: " . htmlspecialchars($data['payout']['method'] ?? '') . "\n";
  
  if ($data['payout']['method'] === 'bank' && isset($data['payout']['bank'])) {
    $emailMessage .= "Bank: " . htmlspecialchars($data['payout']['bank']['name'] ?? '') . "\n";
    $emailMessage .= "Account: " . htmlspecialchars($data['payout']['bank']['accountNumber'] ?? '') . "\n";
  } elseif ($data['payout']['method'] === 'child_account' && isset($data['payout']['child'])) {
    $emailMessage .= "Child name: " . htmlspecialchars($data['payout']['child']['childName'] ?? '') . "\n";
    if (!empty($data['payout']['child']['bankAccount'])) {
      $emailMessage .= "Bank account: " . htmlspecialchars($data['payout']['child']['bankAccount']) . "\n";
    }
  } elseif ($data['payout']['method'] === 'kiwisaver' && isset($data['payout']['kiwiSaver'])) {
    $emailMessage .= "Provider: " . htmlspecialchars($data['payout']['kiwiSaver']['provider'] ?? '') . "\n";
    $emailMessage .= "Member ID: " . htmlspecialchars($data['payout']['kiwiSaver']['memberId'] ?? '') . "\n";
  }
  $emailMessage .= "\n";
}

$emailMessage .= "---\n";
$emailMessage .= "Please process this pickup request and contact the customer to confirm details.\n";

// Prepare email headers
$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "Reply-To: " . htmlspecialchars($data['person']['fullName'] ?? '', ENT_QUOTES) . " <" . htmlspecialchars($data['person']['email'] ?? '', ENT_QUOTES) . ">\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

// Validate email address
$personEmail = $data['person']['email'] ?? '';
if (!filter_var($personEmail, FILTER_VALIDATE_EMAIL)) {
  error_log('[email] Invalid email address: ' . $personEmail);
  $emailSent = false;
} else {
  // Clear any previous errors
  error_clear_last();
  
  // Send email
  $emailSent = mail($to, $subject, $emailMessage, $headers);
  
  // Log detailed information
  if ($emailSent) {
    error_log('[email] SUCCESS - Pickup request email sent. ID: ' . $id . ' | To: ' . $to);
  } else {
    $lastError = error_get_last();
    $errorMsg = 'Unknown error';
    if ($lastError && isset($lastError['message'])) {
      $errorMsg = $lastError['message'];
    }
    error_log('[email] FAILED - Pickup request email NOT sent. ID: ' . $id . ' | Error: ' . $errorMsg);
    
    // Try alternative - minimal headers
    $simpleHeaders = "Reply-To: " . htmlspecialchars($personEmail, ENT_QUOTES) . "\r\n";
    $emailSent = mail($to, $subject, $emailMessage, $simpleHeaders);
    if ($emailSent) {
      error_log('[email] SUCCESS with minimal headers');
    }
  }
}

error_log('[email] New lead submitted: ' . json_encode($data));

echo json_encode([ 'ok' => true, 'id' => $id ]);
?>

