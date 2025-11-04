<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// NZ Post Address API endpoint
// Note: You'll need to register and get API credentials from NZ Post
// Register at: https://www.nzpost.co.nz/business/ecommerce/shipping-apis

// Configuration - Replace with your actual API credentials
define('NZ_POST_API_KEY', getenv('NZ_POST_API_KEY') ?: '');
define('NZ_POST_API_SECRET', getenv('NZ_POST_API_SECRET') ?: '');

// For development, you can use a free alternative like AddressFinder
// Or use NZ Post's test API if available

$query = $_GET['q'] ?? '';

if (empty($query) || strlen($query) < 3) {
  echo json_encode(['suggestions' => []]);
  exit;
}

// Option 1: Use NZ Post Address API (requires credentials)
// Uncomment and configure when you have API credentials
/*
$url = 'https://api.nzpost.co.nz/addresses/autocomplete?query=' . urlencode($query);
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  'Authorization: Bearer ' . getAccessToken(), // Implement OAuth token retrieval
  'Content-Type: application/json'
]);
$response = curl_exec($ch);
curl_close($ch);
echo $response;
*/

// Option 2: Use AddressFinder API (easier to set up, free tier available)
// Register at: https://addressfinder.co.nz/
$addressFinderApiKey = getenv('ADDRESSFINDER_API_KEY') ?: '';

if (!empty($addressFinderApiKey)) {
  $url = 'https://api.addressfinder.io/api/nz/address/autocomplete?key=' . urlencode($addressFinderApiKey) . '&q=' . urlencode($query);
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_TIMEOUT, 5);
  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
  
  if ($httpCode === 200 && $response) {
    $data = json_decode($response, true);
    if (isset($data['completions'])) {
      // Format AddressFinder response
      $suggestions = array_map(function($item) {
        return [
          'id' => $item['a'] ?? '',
          'address' => $item['a'] ?? '',
          'full_address' => $item['a'] ?? '',
        ];
      }, $data['completions']);
      echo json_encode(['suggestions' => $suggestions]);
      exit;
    }
  }
}

// Option 3: Fallback - return empty suggestions
// This allows the form to still work without API
echo json_encode(['suggestions' => []]);
?>

