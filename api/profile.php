<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, OPTIONS');

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if (!in_array($method, ['GET', 'POST', 'PUT', 'PATCH'], true)) {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$token = getBearerTokenFromHeaders();
if (empty($token)) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Authorization token missing']);
    exit;
}

$userRow = fetchUserRowByApiToken($token);
if (!$userRow || empty($userRow['id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Invalid or expired token']);
    exit;
}

if ($method === 'GET') {
    echo json_encode([
        'success' => true,
        'user' => formatUserForApi($userRow),
    ]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid JSON payload']);
    exit;
}

$errors = [];
$profileUpdate = [];
$firstName = isset($input['firstName']) ? trim($input['firstName']) : null;
$lastName = isset($input['lastName']) ? trim($input['lastName']) : null;

if (($firstName !== null && $firstName === '') || ($lastName !== null && $lastName === '')) {
    $errors[] = 'First and last name cannot be empty.';
}

$addressFields = ['street', 'suburb', 'city', 'postcode'];
$addressProvided = array_filter($addressFields, fn($field) => array_key_exists($field, $input));
if (!empty($addressProvided)) {
    foreach ($addressFields as $field) {
        if (!isset($input[$field]) || trim($input[$field]) === '') {
            $errors[] = 'Street, suburb, city and postcode are required.';
            break;
        }
    }
    if (isset($input['postcode']) && !preg_match('/^\d{4}$/', $input['postcode'])) {
        $errors[] = 'Postcode must be a 4-digit NZ postcode.';
    }
}

if (isset($input['phone'])) {
    $phone = trim($input['phone']);
    if ($phone === '' || !preg_match('/^(\+64|0)[2-9]\d{7,8}$/', $phone)) {
        $errors[] = 'Please enter a valid NZ phone number.';
    }
    $profileUpdate['phone'] = $phone;
}

if (isset($input['marketingOptIn'])) {
    $profileUpdate['marketingOptIn'] = (bool)$input['marketingOptIn'];
}

$payoutMethod = $input['payoutMethod'] ?? null;
if ($payoutMethod !== null) {
    if (!in_array($payoutMethod, ['bank', 'child_account', 'kiwisaver'], true)) {
        $errors[] = 'Please choose a valid payout method.';
    } else {
        $profileUpdate['payoutMethod'] = $payoutMethod;
    }
}

$bankName = $input['bankName'] ?? null;
$bankAccount = $input['bankAccount'] ?? null;
$childName = $input['childName'] ?? null;
$childBankAccount = $input['childBankAccount'] ?? null;
$kiwiProvider = $input['kiwisaverProvider'] ?? null;
$kiwiMemberId = $input['kiwisaverMemberId'] ?? null;

if (($profileUpdate['payoutMethod'] ?? $userRow['payout_method'] ?? 'bank') === 'bank') {
    if ($bankName !== null) {
        $profileUpdate['bankName'] = trim($bankName);
    }
    if ($bankAccount !== null) {
        $rawAccount = preg_replace('/\D/', '', $bankAccount);
        $digits = strlen($rawAccount);
        if ($digits < 12 || $digits > 17) {
            $errors[] = 'Please enter a valid NZ bank account number (e.g. 12-1234-1234567-00).';
        } else {
            $parts = [
                substr($rawAccount, 0, 2),
                substr($rawAccount, 2, 4),
                substr($rawAccount, 6, max(0, $digits - 8)),
                substr($rawAccount, -2)
            ];
            $parts[2] = ltrim($parts[2], '0');
            if ($parts[2] === '') {
                $parts[2] = '0';
            }
            $profileUpdate['bankAccount'] = $parts[0] . '-' . $parts[1] . '-' . $parts[2] . '-' . $parts[3];
        }
    }
} elseif (($profileUpdate['payoutMethod'] ?? $userRow['payout_method']) === 'child_account') {
    if ($childName !== null) {
        $childNameTrimmed = trim($childName);
        if ($childNameTrimmed === '') {
            $errors[] = 'Child name is required for child account payouts.';
        } else {
            $profileUpdate['childName'] = $childNameTrimmed;
        }
    }
    if ($childBankAccount !== null) {
        $profileUpdate['childBankAccount'] = trim($childBankAccount);
    }
} elseif (($profileUpdate['payoutMethod'] ?? $userRow['payout_method']) === 'kiwisaver') {
    if ($kiwiProvider !== null) {
        $kiwiProvider = trim($kiwiProvider);
        if ($kiwiProvider === '') {
            $errors[] = 'KiwiSaver provider is required.';
        } else {
            $profileUpdate['kiwisaverProvider'] = $kiwiProvider;
        }
    }
    if ($kiwiMemberId !== null) {
        $kiwiMemberId = trim($kiwiMemberId);
        if ($kiwiMemberId === '') {
            $errors[] = 'KiwiSaver member ID is required.';
        } else {
            $profileUpdate['kiwisaverMemberId'] = $kiwiMemberId;
        }
    }
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => implode(' ', $errors)]);
    exit;
}

if (!empty($addressProvided)) {
    $profileUpdate['street'] = trim($input['street']);
    $profileUpdate['suburb'] = trim($input['suburb']);
    $profileUpdate['city'] = trim($input['city']);
    $profileUpdate['postcode'] = trim($input['postcode']);
}

if (!empty($profileUpdate)) {
    updateUserProfile($userRow['id'], $profileUpdate);
}

if ($firstName !== null || $lastName !== null) {
    dbExecute(
        'UPDATE users SET first_name = :first_name, last_name = :last_name WHERE id = :id',
        [
            ':first_name' => $firstName !== null ? $firstName : ($userRow['first_name'] ?? ''),
            ':last_name' => $lastName !== null ? $lastName : ($userRow['last_name'] ?? ''),
            ':id' => $userRow['id']
        ]
    );
}

$updatedRow = fetchUserRowById($userRow['id']);

echo json_encode([
    'success' => true,
    'user' => formatUserForApi($updatedRow ?: $userRow),
]);

