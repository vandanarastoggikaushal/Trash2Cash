<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Get address details from NZ Post or AddressFinder API
$addressId = $_GET['id'] ?? '';

if (empty($addressId)) {
  http_response_code(400);
  echo json_encode(['error' => 'Address ID required']);
  exit;
}

// AddressFinder API - Get full address details
$addressFinderApiKey = getenv('ADDRESSFINDER_API_KEY') ?: '';

if (!empty($addressFinderApiKey)) {
  $url = 'https://api.addressfinder.io/api/nz/address/details?key=' . urlencode($addressFinderApiKey) . '&id=' . urlencode($addressId);
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_TIMEOUT, 5);
  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
  
  if ($httpCode === 200 && $response) {
    $data = json_decode($response, true);
    if (isset($data['address'])) {
      $addr = $data['address'];
      echo json_encode([
        'street' => ($addr['line_1'] ?? '') . (!empty($addr['line_2']) ? ', ' . $addr['line_2'] : ''),
        'suburb' => $addr['suburb'] ?? '',
        'city' => $addr['city'] ?? '',
        'postcode' => $addr['postcode'] ?? '',
      ]);
      exit;
    }
  }
}

// Fallback - Try to parse address from ID if it's actually an address string
// This handles cases where fallback suggestions don't have real IDs
if (!empty($addressId)) {
  // If addressId looks like an address string (contains comma), try to parse it
  if (strpos($addressId, ',') !== false) {
    $parts = array_map('trim', explode(',', $addressId));
    $result = [
      'street' => '',
      'suburb' => '',
      'city' => 'Wellington',
      'postcode' => ''
    ];
    
    if (count($parts) >= 1) {
      $result['street'] = $parts[0];
    }
    if (count($parts) >= 2) {
      $result['suburb'] = $parts[1];
    }
    if (count($parts) >= 3) {
      // Check if last part has postcode
      $lastPart = $parts[count($parts) - 1];
      if (preg_match('/(\d{4})/', $lastPart, $matches)) {
        $result['postcode'] = $matches[1];
        if (count($parts) >= 4) {
          $result['city'] = $parts[count($parts) - 2];
        }
      } else {
        $result['city'] = $lastPart;
      }
    }
    
    echo json_encode($result);
    exit;
  }
}

// Fallback - Address not found
http_response_code(404);
echo json_encode(['error' => 'Address not found']);
?>

