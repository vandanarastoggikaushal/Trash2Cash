<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode([ 'ok' => false, 'error' => 'Method not allowed' ]);
  exit;
}

// Load database helper if available
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/payments.php';
$dbFile = __DIR__ . '/../includes/db.php';
if (file_exists($dbFile)) {
    require_once $dbFile;
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

$authenticatedUserId = null;
if (function_exists('isLoggedIn') && isLoggedIn()) {
  $authenticatedUserId = $_SESSION['user_id'] ?? null;
}

if (!$authenticatedUserId) {
  $token = getBearerTokenFromHeaders();
  if (!empty($token)) {
    $tokenUser = fetchUserRowByApiToken($token);
    if (!empty($tokenUser['id'])) {
      $authenticatedUserId = $tokenUser['id'];
    }
  }
}

if (!$authenticatedUserId && isPaymentsDatabaseAvailable() && function_exists('dbQueryOne')) {
  $emailLookup = trim((string) ($data['person']['email'] ?? ''));
  if ($emailLookup !== '') {
    $existingUser = dbQueryOne(
      'SELECT id FROM users WHERE email = :email LIMIT 1',
      [':email' => $emailLookup]
    );
    if ($existingUser && !empty($existingUser['id'])) {
      $authenticatedUserId = $existingUser['id'];
    }
  }
}

$pickup = $data['pickup'] ?? [];
$cansEstimate = isset($pickup['cansEstimate']) ? (int) $pickup['cansEstimate'] : 0;
$cansReward = $cansEstimate > 0 ? floor($cansEstimate / 100) * CAN_REWARD_PER_100 : 0;
$applianceReward = 0;

if (!empty($pickup['appliances']) && is_array($pickup['appliances'])) {
  $applianceMap = [];
  foreach ($APPLIANCE_CREDITS as $applianceDefinition) {
    if (!empty($applianceDefinition['slug'])) {
      $applianceMap[$applianceDefinition['slug']] = $applianceDefinition['credit'] ?? 0;
    }
  }
  foreach ($pickup['appliances'] as $appliance) {
    $slug = $appliance['slug'] ?? '';
    $qty = isset($appliance['qty']) ? (int) $appliance['qty'] : 0;
    if ($slug !== '' && $qty > 0 && isset($applianceMap[$slug])) {
      $applianceReward += (int) $applianceMap[$slug] * $qty;
    }
  }
}

$estimatedReward = round((float) ($cansReward + $applianceReward), 2);

$id = bin2hex(random_bytes(6));
$data['id'] = $id;
$data['createdAt'] = gmdate('c');

// Check if database is available
$useDatabase = function_exists('getDB') && getDB() !== null;

if ($useDatabase) {
  // Save to database
  $person = $data['person'] ?? [];
  $address = $data['address'] ?? [];
  $pickup = $data['pickup'] ?? [];
  $payout = $data['payout'] ?? [];
  $confirm = $data['confirm'] ?? [];
  
  // Extract appliances
  $appliances = [];
  if (isset($pickup['appliances']) && is_array($pickup['appliances'])) {
    $appliances = $pickup['appliances'];
  }
  
  $sql = "INSERT INTO leads (
    id, user_id, person_name, person_email, person_phone, person_marketing_optin,
    address_street, address_suburb, address_city, address_postcode, address_access_notes,
    pickup_type, pickup_cans_estimate, pickup_preferred_date, pickup_preferred_window,
    payout_method, payout_bank_name, payout_bank_account,
    payout_child_name, payout_child_bank_account,
    payout_kiwisaver_provider, payout_kiwisaver_member_id,
    items_are_clean, accepted_terms, appliances_json, estimated_reward, created_at, status
  ) VALUES (
    :id, :user_id, :person_name, :person_email, :person_phone, :person_marketing_optin,
    :address_street, :address_suburb, :address_city, :address_postcode, :address_access_notes,
    :pickup_type, :pickup_cans_estimate, :pickup_preferred_date, :pickup_preferred_window,
    :payout_method, :payout_bank_name, :payout_bank_account,
    :payout_child_name, :payout_child_bank_account,
    :payout_kiwisaver_provider, :payout_kiwisaver_member_id,
    :items_are_clean, :accepted_terms, :appliances_json, :estimated_reward, :created_at, :status
  )";
  
  $params = [
    ':id' => $id,
    ':user_id' => $authenticatedUserId,
    ':person_name' => $person['fullName'] ?? '',
    ':person_email' => $person['email'] ?? '',
    ':person_phone' => $person['phone'] ?? '',
    ':person_marketing_optin' => ($person['marketingOptIn'] ?? false) ? 1 : 0,
    ':address_street' => $address['street'] ?? '',
    ':address_suburb' => $address['suburb'] ?? '',
    ':address_city' => $address['city'] ?? '',
    ':address_postcode' => $address['postcode'] ?? '',
    ':address_access_notes' => $address['accessNotes'] ?? null,
    ':pickup_type' => $pickup['type'] ?? 'cans',
    ':pickup_cans_estimate' => $pickup['cansEstimate'] ?? null,
    ':pickup_preferred_date' => $pickup['preferredDate'] ?? null,
    ':pickup_preferred_window' => $pickup['preferredWindow'] ?? null,
    ':payout_method' => $payout['method'] ?? 'bank',
    ':payout_bank_name' => $payout['bank']['name'] ?? null,
    ':payout_bank_account' => $payout['bank']['accountNumber'] ?? null,
    ':payout_child_name' => $payout['child']['childName'] ?? null,
    ':payout_child_bank_account' => $payout['child']['bankAccount'] ?? null,
    ':payout_kiwisaver_provider' => $payout['kiwiSaver']['provider'] ?? null,
    ':payout_kiwisaver_member_id' => $payout['kiwiSaver']['memberId'] ?? null,
    ':items_are_clean' => ($confirm['itemsAreClean'] ?? false) ? 1 : 0,
    ':accepted_terms' => ($confirm['acceptedTerms'] ?? false) ? 1 : 0,
    ':appliances_json' => !empty($appliances) ? json_encode($appliances) : null,
    ':estimated_reward' => $estimatedReward,
    ':created_at' => gmdate('Y-m-d H:i:s'),
    ':status' => 'pending'
  ];
  
  $saved = dbExecute($sql, $params) !== false;
  if (!$saved) {
    // Fallback to JSON if database save fails
    $useDatabase = false;
  }
}

if (!$useDatabase) {
  // Fallback to JSON file storage
  $dir = __DIR__ . '/../data';
  @mkdir($dir, 0755, true);
  $file = $dir . '/leads.json';
  
  $existing = [];
  if (file_exists($file)) {
    $content = file_get_contents($file);
    $json = json_decode($content, true);
    if (is_array($json)) { $existing = $json; }
  }

  $data['userId'] = $authenticatedUserId;
  $data['estimatedReward'] = $estimatedReward;
  
  $existing[] = $data;
  file_put_contents($file, json_encode($existing, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

if ($authenticatedUserId && $estimatedReward > 0) {
  $reference = 'Pickup #' . strtoupper($id);
  if (!paymentExistsForReference($authenticatedUserId, $reference)) {
    $noteParts = [];
    if ($cansReward > 0) {
      $noteParts[] = 'Cans $' . number_format($cansReward, 2);
    }
    if ($applianceReward > 0) {
      $noteParts[] = 'Appliances $' . number_format($applianceReward, 2);
    }
    $notes = 'Auto credit from pickup request ' . strtoupper($id);
    if (!empty($noteParts)) {
      $notes .= ' (' . implode('; ', $noteParts) . ')';
    }

    recordUserPayment(
      $authenticatedUserId,
      $estimatedReward,
      $reference,
      $notes,
      gmdate('Y-m-d'),
      'pending',
      'NZD'
    );
  }
}

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
  
  // Check if we're on local development server (mail won't work)
  $isLocalDev = (strpos($_SERVER['SERVER_NAME'] ?? '', 'localhost') !== false || 
                 strpos($_SERVER['SERVER_NAME'] ?? '', '192.168.') !== false ||
                 strpos($_SERVER['SERVER_NAME'] ?? '', '127.0.0.1') !== false);
  
  if ($isLocalDev) {
    // On local development, just log the email (mail() won't work)
    error_log('[email] LOCAL DEV - Email would be sent to: ' . $to);
    error_log('[email] LOCAL DEV - Subject: ' . $subject);
    error_log('[email] LOCAL DEV - From: ' . $personEmail);
    error_log('[email] LOCAL DEV - Message preview: ' . substr($emailMessage, 0, 200));
    $emailSent = true; // Mark as "sent" for local dev
  } else {
    // On production (Hostinger), actually send the email
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
}

error_log('[email] New lead submitted: ' . json_encode($data));

echo json_encode([ 'ok' => true, 'id' => $id ]);
?>
