<?php
// Configuration constants
define('CAN_REWARD_PER_100', 1);

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

if (!isset($SITE)) {
  $SITE = [
    'name' => COMPANY_NAME,
    'url' => 'https://trash2cash.co.nz',
    'description' => "We collect clean aluminium cans and old appliances from your home across Wellington. Earn \$1 per 100 cans—deposit to kids' accounts or KiwiSaver.",
    'ogImage' => '/og.svg'
  ];
}

