<?php
// Configuration constants
define('CAN_REWARD_PER_100', 1);

// Load custom error handler (for branded error pages)
// Only load if not already on an error page to prevent issues
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$isErrorPage = strpos($requestUri, '404.php') !== false ||
               strpos($requestUri, '500.php') !== false ||
               strpos($requestUri, '403.php') !== false ||
               strpos($requestUri, '503.php') !== false;

if (!$isErrorPage) {
    $errorHandlerFile = __DIR__ . '/error-handler.php';
    if (file_exists($errorHandlerFile)) {
        try {
            require_once $errorHandlerFile;
        } catch (Exception $e) {
            // Error handler itself has errors - log but don't break
            error_log('Error loading error-handler.php: ' . $e->getMessage());
        }
    }
}

$APPLIANCE_CREDITS = [
  ['slug' => 'washing_machine', 'label' => 'Washing machine', 'credit' => 6],
  ['slug' => 'dishwasher', 'label' => 'Dishwasher', 'credit' => 5],
  ['slug' => 'microwave', 'label' => 'Microwave', 'credit' => 2],
  ['slug' => 'pc_case', 'label' => 'PC case (metal)', 'credit' => 2],
  ['slug' => 'laptop', 'label' => 'Laptop (metal body)', 'credit' => 3]
];

$SERVICE_AREAS = [
  'Wellington City',
  'Churton Park',
  'Johnsonville',
  'Karori',
  'Newlands',
  'Tawa',
  'Lower Hutt',
  'Upper Hutt',
  'Porirua'
];

define('SUPPORT_EMAIL', 'collect@trash2cash.co.nz');
define('SUPPORT_PHONE', '+64221758458');
define('COMPANY_NAME', 'Trash2Cash NZ');
define('CITY', 'Wellington');

// Address search feature - Set to true when you have AddressFinder or NZ Post API key
define('ENABLE_ADDRESS_SEARCH', false);

/**
 * Get current version from VERSION file
 * @return string Version number
 */
function getVersion() {
    $versionFile = __DIR__ . '/../VERSION';
    if (file_exists($versionFile)) {
        return trim(file_get_contents($versionFile));
    }
    return '1.0.0';
}

if (!isset($SITE)) {
  $SITE = [
    'name' => COMPANY_NAME,
    'url' => 'https://trash2cash.co.nz',
    'description' => "We collect clean aluminium cans and old appliances from your home across Wellington. Earn \$1 per 100 cans—deposit to kids' accounts or KiwiSaver.",
    'ogImage' => '/og.svg'
  ];
}

