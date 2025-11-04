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

// Fallback
http_response_code(404);
echo json_encode(['error' => 'Address not found']);
?>

