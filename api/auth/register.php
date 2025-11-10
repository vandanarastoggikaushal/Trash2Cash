<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/payments.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid JSON payload']);
    exit;
}

$username = trim($input['username'] ?? '');
$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';
$passwordConfirm = $input['passwordConfirm'] ?? '';
$firstName = trim($input['firstName'] ?? '');
$lastName = trim($input['lastName'] ?? '');
$phone = trim($input['phone'] ?? '');
$street = trim($input['street'] ?? '');
$suburb = trim($input['suburb'] ?? '');
$city = trim($input['city'] ?? '');
$postcode = trim($input['postcode'] ?? '');
$marketingOptIn = !empty($input['marketingOptIn']);
$setupPayoutNow = array_key_exists('setupPayoutNow', $input) ? (bool)$input['setupPayoutNow'] : true;
$payoutMethod = $input['payoutMethod'] ?? 'bank';
$bankName = trim($input['bankName'] ?? '');
$bankAccount = trim($input['bankAccount'] ?? '');
$childName = trim($input['childName'] ?? '');
$childBankAccount = trim($input['childBankAccount'] ?? '');
$kiwiProvider = trim($input['kiwisaverProvider'] ?? '');
$kiwiMemberId = trim($input['kiwisaverMemberId'] ?? '');

$effectivePayoutMethod = $payoutMethod;
if (!$setupPayoutNow) {
    $effectivePayoutMethod = 'bank';
    $bankName = '';
    $bankAccount = '';
    $childName = '';
    $childBankAccount = '';
    $kiwiProvider = '';
    $kiwiMemberId = '';
}

$errors = [];

if ($username === '' || strlen($username) < 3) {
    $errors[] = 'Username must be at least 3 characters.';
}
if ($firstName === '') {
    $errors[] = 'First name is required.';
}
if ($lastName === '') {
    $errors[] = 'Last name is required.';
}
if ($street === '' || $suburb === '' || $city === '' || $postcode === '') {
    $errors[] = 'Street, suburb, city and postcode are required.';
} elseif (!preg_match('/^\d{4}$/', $postcode)) {
    $errors[] = 'Postcode must be a 4-digit NZ postcode.';
}
if ($phone === '' || !preg_match('/^(\+64|0)[2-9]\d{7,8}$/', $phone)) {
    $errors[] = 'Please enter a valid NZ phone number.';
}
if ($setupPayoutNow && !in_array($payoutMethod, ['bank', 'child_account', 'kiwisaver'], true)) {
    $errors[] = 'Please choose a valid payout method.';
}
if ($setupPayoutNow && $payoutMethod === 'bank') {
    if ($bankName === '') {
        $errors[] = 'Account holder name is required.';
    }
    if ($bankAccount === '') {
        $errors[] = 'Bank account number is required.';
    } else {
        $digitsOnly = preg_replace('/\D/', '', $bankAccount);
        $digitCount = strlen($digitsOnly);
        if ($digitCount < 12 || $digitCount > 17) {
            $errors[] = 'Please enter a valid NZ bank account number (e.g. 12-1234-1234567-00).';
        } else {
            $parts = [
                substr($digitsOnly, 0, 2),
                substr($digitsOnly, 2, 4),
                substr($digitsOnly, 6, max(0, $digitCount - 8)),
                substr($digitsOnly, -2)
            ];
            $parts[2] = ltrim($parts[2], '0');
            if ($parts[2] === '') {
                $parts[2] = '0';
            }
            $bankAccount = $parts[0] . '-' . $parts[1] . '-' . $parts[2] . '-' . $parts[3];
        }
    }
} elseif ($setupPayoutNow && $payoutMethod === 'child_account' && $childName === '') {
    $errors[] = 'Child name is required for child account payouts.';
} elseif ($setupPayoutNow && $payoutMethod === 'kiwisaver' && ($kiwiProvider === '' || $kiwiMemberId === '')) {
    $errors[] = 'KiwiSaver provider and member ID are required.';
}
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'A valid email address is required.';
}
if ($password === '' || strlen($password) < 6) {
    $errors[] = 'Password must be at least 6 characters.';
}
if ($password !== $passwordConfirm) {
    $errors[] = 'Passwords do not match.';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => implode(' ', $errors)]);
    exit;
}

$address = $street . "\n" . $suburb . "\n" . $city . ' ' . $postcode;
$extra = [
    'phone' => $phone,
    'marketingOptIn' => $marketingOptIn,
    'payoutMethod' => $effectivePayoutMethod,
    'payoutBankName' => $bankName,
    'payoutBankAccount' => $bankAccount,
    'payoutChildName' => $childName,
    'payoutChildBankAccount' => $childBankAccount,
    'payoutKiwisaverProvider' => $kiwiProvider,
    'payoutKiwisaverMemberId' => $kiwiMemberId,
];

$user = createUser($username, $password, $email, 'user', $firstName, $lastName, $address, $extra);
if (!$user) {
    http_response_code(409);
    echo json_encode(['success' => false, 'error' => 'Username already exists. Please choose a different username.']);
    exit;
}

updateUserProfile($user['id'], [
    'street' => $street,
    'suburb' => $suburb,
    'city' => $city,
    'postcode' => $postcode,
    'phone' => $phone,
    'marketingOptIn' => $marketingOptIn,
    'payoutMethod' => $effectivePayoutMethod,
    'bankName' => $bankName,
    'bankAccount' => $bankAccount,
    'childName' => $childName,
    'childBankAccount' => $childBankAccount,
    'kiwisaverProvider' => $kiwiProvider,
    'kiwisaverMemberId' => $kiwiMemberId,
]);

if (defined('PROMO_BONUS_AMOUNT') && PROMO_BONUS_AMOUNT > 0) {
    recordUserPayment(
        $user['id'],
        PROMO_BONUS_AMOUNT,
        'Welcome bonus',
        'Registration bonus payable on first collection',
        gmdate('Y-m-d'),
        defined('PROMO_BONUS_STATUS') ? PROMO_BONUS_STATUS : 'pending',
        defined('PROMO_BONUS_CURRENCY') ? PROMO_BONUS_CURRENCY : 'NZD'
    );
}

$token = generateApiToken($user['id']);
if (!$token) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Unable to generate access token']);
    exit;
}

$userRow = fetchUserRowById($user['id']);
$profile = $userRow ? formatUserForApi($userRow) : formatUserForApi($user);

echo json_encode([
    'success' => true,
    'token' => $token,
    'user' => $profile,
]);

