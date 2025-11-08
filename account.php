<?php
$pageTitle = 'My Account';
$pageDescription = 'View your Trash2Cash payout history and balance.';

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/payments.php';

requireLogin('/account.php');

$user = getCurrentUser();
$addressSuccess = '';
$addressError = '';
$payoutSuccess = '';
$payoutError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $currentUserId = $user['id'] ?? null;
    if (!$currentUserId) {
        requireLogin('/account.php');
    }

    if ($_POST['action'] === 'update_address') {
        $street = trim($_POST['address_street'] ?? '');
        $suburb = trim($_POST['address_suburb'] ?? '');
        $cityInput = trim($_POST['address_city'] ?? '');
        $postcode = trim($_POST['address_postcode'] ?? '');
        $phoneInput = trim($_POST['phone'] ?? '');
        $marketingOptInInput = isset($_POST['marketingOptIn']) && $_POST['marketingOptIn'] === 'on';

        if ($street === '' || $suburb === '' || $cityInput === '' || $postcode === '') {
            $addressError = 'Please fill in street, suburb, city, and postcode.';
        } elseif (!preg_match('/^\d{4}$/', $postcode)) {
            $addressError = 'Postcode must be a 4-digit NZ postcode.';
        } elseif ($phoneInput === '' || !preg_match('/^(\+64|0)[2-9]\d{7,8}$/', $phoneInput)) {
            $addressError = 'Please enter a valid NZ phone number.';
        } else {
            $updated = updateUserProfile($currentUserId, [
                'street' => $street,
                'suburb' => $suburb,
                'city' => $cityInput,
                'postcode' => $postcode,
                'phone' => $phoneInput,
                'marketingOptIn' => $marketingOptInInput
            ]);
            if ($updated) {
                $addressSuccess = 'Profile updated successfully.';
                $user = getCurrentUser();
            } else {
                $addressError = 'No changes were detected.';
            }
        }
    } elseif ($_POST['action'] === 'update_payout') {
        $payoutMethodInput = $_POST['payoutMethod'] ?? 'bank';
        $bankNameInput = trim($_POST['bankName'] ?? '');
        $bankAccountInput = trim($_POST['bankAccount'] ?? '');
        $childNameInput = trim($_POST['childName'] ?? '');
        $childBankAccountInput = trim($_POST['childBankAccount'] ?? '');
        $kiwiProviderInput = trim($_POST['kiwisaverProvider'] ?? '');
        $kiwiMemberInput = trim($_POST['kiwisaverMemberId'] ?? '');

        $sanitizedBankAccount = $bankAccountInput;
        if (!in_array($payoutMethodInput, ['bank', 'child_account', 'kiwisaver'], true)) {
            $payoutError = 'Please choose a valid payout method.';
        } elseif ($payoutMethodInput === 'bank') {
            if ($bankNameInput === '') {
                $payoutError = 'Bank name is required.';
            } elseif ($bankAccountInput === '') {
                $payoutError = 'Bank account number is required.';
            } else {
                $digitsOnly = preg_replace('/\D/', '', $bankAccountInput);
                $digitCount = strlen($digitsOnly);
                if ($digitCount < 12 || $digitCount > 17) {
                    $payoutError = 'Please enter a valid NZ bank account number (e.g. 12-1234-1234567-00).';
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
                    $sanitizedBankAccount = $parts[0] . '-' . $parts[1] . '-' . $parts[2] . '-' . $parts[3];
                }
            }
        } elseif ($payoutMethodInput === 'child_account' && $childNameInput === '') {
            $payoutError = 'Child name is required for child account payouts.';
        } elseif ($payoutMethodInput === 'kiwisaver' && ($kiwiProviderInput === '' || $kiwiMemberInput === '')) {
            $payoutError = 'KiwiSaver provider and member ID are required.';
        }

        if ($payoutError === '') {
            $updated = updateUserProfile($currentUserId, [
                'payoutMethod' => $payoutMethodInput,
                'bankName' => $bankNameInput,
                'bankAccount' => $sanitizedBankAccount,
                'childName' => $childNameInput,
                'childBankAccount' => $childBankAccountInput,
                'kiwisaverProvider' => $kiwiProviderInput,
                'kiwisaverMemberId' => $kiwiMemberInput
            ]);
            if ($updated) {
                $payoutSuccess = 'Payout settings updated successfully.';
                $user = getCurrentUser();
            } else {
                $payoutError = 'No changes were detected.';
            }
        }
    }
}

$displayName = function_exists('getUserDisplayName') ? getUserDisplayName(true) : strtoupper($user['username'] ?? '');
$balance = getUserBalance($user['id']);
$pendingBalance = getUserBalance($user['id'], ['pending', 'processing']);
$payments = getUserPayments($user['id']);
$currencySymbol = 'NZ$';
$formattedBalance = $currencySymbol . number_format($balance, 2);
$formattedPending = $pendingBalance > 0 ? $currencySymbol . number_format($pendingBalance, 2) : null;
$dataSource = isPaymentsDatabaseAvailable() ? 'database' : 'backup storage';
$phone = $user['phone'] ?? '';
$marketingOptIn = !empty($user['marketingOptIn']);
$payoutMethod = $user['payoutMethod'] ?? 'bank';
$payoutBankName = $user['payoutBankName'] ?? '';
$payoutBankAccount = $user['payoutBankAccount'] ?? '';
$payoutChildName = $user['payoutChildName'] ?? '';
$payoutChildBankAccount = $user['payoutChildBankAccount'] ?? '';
$payoutKiwisaverProvider = $user['payoutKiwisaverProvider'] ?? '';
$payoutKiwisaverMemberId = $user['payoutKiwisaverMemberId'] ?? '';

$addressLines = preg_split('/\r\n|\r|\n/', $user['address'] ?? '');
$addressStreetValue = $addressLines[0] ?? '';
$addressSuburbValue = $addressLines[1] ?? '';
$addressCityLine = $addressLines[2] ?? '';
$addressCityValue = $addressCityLine ?: CITY;
$addressPostcodeValue = '';
if ($addressCityLine && preg_match('/(.+)\s+(\d{4})$/', $addressCityLine, $matches)) {
    $addressCityValue = trim($matches[1]);
    $addressPostcodeValue = trim($matches[2]);
}
if ($addressPostcodeValue === '' && preg_match('/\b(\d{4})\b/', $user['address'] ?? '', $matchPost)) {
    $addressPostcodeValue = $matchPost[1];
}

if ($addressError !== '' && ($_POST['action'] ?? '') === 'update_address') {
    $addressStreetValue = $_POST['address_street'] ?? $addressStreetValue;
    $addressSuburbValue = $_POST['address_suburb'] ?? $addressSuburbValue;
    $addressCityValue = $_POST['address_city'] ?? $addressCityValue;
    $addressPostcodeValue = $_POST['address_postcode'] ?? $addressPostcodeValue;
}
$phoneValue = ($addressError !== '' && ($_POST['action'] ?? '') === 'update_address')
    ? ($_POST['phone'] ?? $phone)
    : $phone;
$marketingChecked = ($addressError !== '' && ($_POST['action'] ?? '') === 'update_address')
    ? !empty($_POST['marketingOptIn'])
    : $marketingOptIn;

$payoutMethodValue = ($payoutError !== '' && ($_POST['action'] ?? '') === 'update_payout') ? ($_POST['payoutMethod'] ?? $payoutMethod) : $payoutMethod;
$bankNameValue = ($payoutError !== '' && ($_POST['action'] ?? '') === 'update_payout') ? ($_POST['bankName'] ?? $payoutBankName) : $payoutBankName;
$bankAccountValue = ($payoutError !== '' && ($_POST['action'] ?? '') === 'update_payout') ? ($_POST['bankAccount'] ?? $payoutBankAccount) : $payoutBankAccount;
$childNameValue = ($payoutError !== '' && ($_POST['action'] ?? '') === 'update_payout') ? ($_POST['childName'] ?? $payoutChildName) : $payoutChildName;
$childBankAccountValue = ($payoutError !== '' && ($_POST['action'] ?? '') === 'update_payout') ? ($_POST['childBankAccount'] ?? $payoutChildBankAccount) : $payoutChildBankAccount;
$kiwiProviderValue = ($payoutError !== '' && ($_POST['action'] ?? '') === 'update_payout') ? ($_POST['kiwisaverProvider'] ?? $payoutKiwisaverProvider) : $payoutKiwisaverProvider;
$kiwiMemberValue = ($payoutError !== '' && ($_POST['action'] ?? '') === 'update_payout') ? ($_POST['kiwisaverMemberId'] ?? $payoutKiwisaverMemberId) : $payoutKiwisaverMemberId;

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-16">
  <div class="max-w-5xl mx-auto space-y-10">
    <div class="rounded-3xl border-2 border-emerald-100 bg-gradient-to-r from-emerald-50 via-white to-emerald-50/40 p-8 shadow-xl">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
          <h1 class="text-4xl font-extrabold text-slate-900 mb-2 flex items-center gap-3">
            <span class="text-emerald-600 text-5xl">💼</span>
            <span>My Account</span>
          </h1>
          <p class="text-slate-600">Kia ora, <?php echo htmlspecialchars($displayName); ?>. Here’s your latest payout summary.</p>
        </div>
        <div class="rounded-2xl border-2 border-emerald-200 bg-white px-8 py-5 text-center shadow-md">
          <p class="text-sm font-semibold text-slate-500 uppercase tracking-[0.2em]">Current Balance</p>
          <p class="mt-2 text-4xl font-black text-brand"><?php echo htmlspecialchars($formattedBalance); ?></p>
          <?php if (!empty($formattedPending)): ?>
          <div class="mt-3 rounded-xl bg-amber-50 border border-amber-200 px-4 py-2 text-sm font-semibold text-amber-700">
            Pending credits: <?php echo htmlspecialchars($formattedPending); ?> (includes welcome bonus)
          </div>
          <?php endif; ?>
        </div>
      </div>
      <div class="mt-6 flex flex-wrap items-center gap-4 text-sm text-slate-500">
        <span class="inline-flex items-center gap-2 rounded-full bg-emerald-100 px-3 py-1 font-semibold text-emerald-700">
          <span class="text-base">🗂️</span>
          <span>Data source: <?php echo htmlspecialchars($dataSource); ?></span>
        </span>
        <span>Balances include completed payouts only.</span>
      </div>
    </div>

    <div class="grid gap-8 lg:grid-cols-2">
      <div class="rounded-3xl border-2 border-emerald-100 bg-white p-8 shadow-xl">
        <div class="flex items-center gap-2 mb-4">
          <span class="text-emerald-600 text-2xl">🏠</span>
          <h2 class="text-xl font-bold text-slate-900">Update Address & Contact</h2>
        </div>
        <?php if ($addressError): ?>
          <div class="mb-4 rounded-xl border-2 border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <?php echo htmlspecialchars($addressError); ?>
          </div>
        <?php elseif ($addressSuccess): ?>
          <div class="mb-4 rounded-xl border-2 border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            <?php echo htmlspecialchars($addressSuccess); ?>
          </div>
        <?php endif; ?>
        <form method="POST" class="space-y-4">
          <input type="hidden" name="action" value="update_address">
          <div>
            <label class="block text-sm font-semibold text-slate-900 mb-2" for="account-address-street">Street</label>
            <input id="account-address-street" name="address_street" type="text" required class="w-full rounded-lg border-2 border-emerald-200 px-4 py-3 focus:border-brand focus:ring-2 focus:ring-emerald-200 transition-all" value="<?php echo htmlspecialchars($addressStreetValue); ?>">
          </div>
          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <label class="block text-sm font-semibold text-slate-900 mb-2" for="account-address-suburb">Suburb</label>
              <input id="account-address-suburb" name="address_suburb" type="text" required class="w-full rounded-lg border-2 border-emerald-200 px-4 py-3 focus:border-brand focus:ring-2 focus:ring-emerald-200 transition-all" value="<?php echo htmlspecialchars($addressSuburbValue); ?>">
            </div>
            <div>
              <label class="block text-sm font-semibold text-slate-900 mb-2" for="account-address-city">City</label>
              <input id="account-address-city" name="address_city" type="text" required class="w-full rounded-lg border-2 border-emerald-200 px-4 py-3 focus:border-brand focus:ring-2 focus:ring-emerald-200 transition-all" value="<?php echo htmlspecialchars($addressCityValue); ?>">
            </div>
          </div>
          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <label class="block text-sm font-semibold text-slate-900 mb-2" for="account-address-postcode">Postcode</label>
              <input id="account-address-postcode" name="address_postcode" type="text" required pattern="\d{4}" maxlength="4" class="w-full rounded-lg border-2 border-emerald-200 px-4 py-3 focus:border-brand focus:ring-2 focus:ring-emerald-200 transition-all" value="<?php echo htmlspecialchars($addressPostcodeValue); ?>">
            </div>
            <div>
              <label class="block text-sm font-semibold text-slate-900 mb-2" for="account-phone">Phone</label>
              <input id="account-phone" name="phone" type="tel" required class="w-full rounded-lg border-2 border-emerald-200 px-4 py-3 focus:border-brand focus:ring-2 focus:ring-emerald-200 transition-all" value="<?php echo htmlspecialchars($phoneValue); ?>">
              <p class="mt-1 text-xs text-slate-500">Format: 0212345678 or +64212345678</p>
            </div>
          </div>
          <label class="inline-flex items-center gap-2 text-sm text-slate-700 font-semibold">
            <input type="checkbox" name="marketingOptIn" <?php echo $marketingChecked ? 'checked' : ''; ?>>
            I'd like to receive Trash2Cash updates and offers
          </label>
          <button type="submit" class="w-full btn text-sm px-4 py-3">Save address details</button>
        </form>
      </div>

      <div class="rounded-3xl border-2 border-emerald-100 bg-white p-8 shadow-xl">
        <div class="flex items-center gap-2 mb-4">
          <span class="text-emerald-600 text-2xl">💰</span>
          <h2 class="text-xl font-bold text-slate-900">Update Payout Preferences</h2>
        </div>
        <?php if ($payoutError): ?>
          <div class="mb-4 rounded-xl border-2 border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <?php echo htmlspecialchars($payoutError); ?>
          </div>
        <?php elseif ($payoutSuccess): ?>
          <div class="mb-4 rounded-xl border-2 border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            <?php echo htmlspecialchars($payoutSuccess); ?>
          </div>
        <?php endif; ?>
        <form method="POST" class="space-y-4">
          <input type="hidden" name="action" value="update_payout">
          <div class="space-y-2">
            <label class="flex items-center gap-2 text-sm font-semibold text-slate-900">
              <input type="radio" name="payoutMethod" value="bank" <?php echo $payoutMethodValue === 'bank' ? 'checked' : ''; ?>> Bank account
            </label>
            <div class="grid gap-3 sm:grid-cols-2 <?php echo $payoutMethodValue === 'bank' ? '' : 'hidden'; ?>" id="account-bank-fields">
              <input name="bankName" placeholder="Bank name" class="rounded-md border-2 border-emerald-200 px-3 py-2" value="<?php echo htmlspecialchars($bankNameValue); ?>">
              <input name="bankAccount" placeholder="Account number (e.g. 12-1234-1234567-00)" class="rounded-md border-2 border-emerald-200 px-3 py-2" value="<?php echo htmlspecialchars($bankAccountValue); ?>">
            </div>
            <label class="flex items-center gap-2 text-sm font-semibold text-slate-900">
              <input type="radio" name="payoutMethod" value="child_account" <?php echo $payoutMethodValue === 'child_account' ? 'checked' : ''; ?>> Child account
            </label>
            <div class="grid gap-3 sm:grid-cols-2 <?php echo $payoutMethodValue === 'child_account' ? '' : 'hidden'; ?>" id="account-child-fields">
              <input name="childName" placeholder="Child name" class="rounded-md border-2 border-emerald-200 px-3 py-2" value="<?php echo htmlspecialchars($childNameValue); ?>">
              <input name="childBankAccount" placeholder="Optional bank account" class="rounded-md border-2 border-emerald-200 px-3 py-2" value="<?php echo htmlspecialchars($childBankAccountValue); ?>">
            </div>
            <label class="flex items-center gap-2 text-sm font-semibold text-slate-900">
              <input type="radio" name="payoutMethod" value="kiwisaver" <?php echo $payoutMethodValue === 'kiwisaver' ? 'checked' : ''; ?>> KiwiSaver
            </label>
            <div class="grid gap-3 sm:grid-cols-2 <?php echo $payoutMethodValue === 'kiwisaver' ? '' : 'hidden'; ?>" id="account-kiwi-fields">
              <input name="kiwisaverProvider" placeholder="Provider" class="rounded-md border-2 border-emerald-200 px-3 py-2" value="<?php echo htmlspecialchars($kiwiProviderValue); ?>">
              <input name="kiwisaverMemberId" placeholder="Member ID" class="rounded-md border-2 border-emerald-200 px-3 py-2" value="<?php echo htmlspecialchars($kiwiMemberValue); ?>">
            </div>
          </div>
          <button type="submit" class="w-full btn text-sm px-4 py-3">Save payout settings</button>
        </form>
      </div>
    </div>

    <div class="rounded-3xl border-2 border-emerald-100 bg-white p-8 shadow-xl">
      <div class="mb-6 flex items-center gap-3">
        <span class="text-emerald-600 text-3xl">👤</span>
        <h2 class="text-2xl font-bold text-slate-900">Account Details</h2>
      </div>
      <div class="grid gap-4 sm:grid-cols-2">
        <div>
          <p class="text-xs uppercase tracking-wide text-slate-500">Full name</p>
          <p class="text-sm font-semibold text-slate-900 mt-1"><?php echo htmlspecialchars($displayName); ?></p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-slate-500">Email</p>
          <p class="text-sm font-semibold text-slate-900 mt-1"><?php echo htmlspecialchars($user['email'] ?? ''); ?></p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-slate-500">Phone</p>
          <p class="text-sm font-semibold text-slate-900 mt-1"><?php echo htmlspecialchars($phone); ?></p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-slate-500">Marketing updates</p>
          <p class="text-sm font-semibold text-slate-900 mt-1"><?php echo $marketingOptIn ? 'Subscribed' : 'Not subscribed'; ?></p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-slate-500">Address</p>
          <p class="text-sm font-semibold text-slate-900 mt-1 whitespace-pre-line"><?php echo htmlspecialchars($user['address'] ?? ''); ?></p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-slate-500">Payout method</p>
          <p class="text-sm font-semibold text-slate-900 mt-1">
            <?php
              $payoutLabels = [
                'bank' => 'Bank account',
                'child_account' => 'Child account',
                'kiwisaver' => 'KiwiSaver'
              ];
              echo htmlspecialchars($payoutLabels[$payoutMethod] ?? ucfirst($payoutMethod));
            ?>
          </p>
        </div>
        <?php if ($payoutMethod === 'bank'): ?>
        <div>
          <p class="text-xs uppercase tracking-wide text-slate-500">Bank name</p>
          <p class="text-sm font-semibold text-slate-900 mt-1">
            <?php echo $payoutBankName ? htmlspecialchars($payoutBankName) : '—'; ?>
          </p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wide text-slate-500">Bank account</p>
          <p class="text-sm font-semibold text-slate-900 mt-1 font-mono tracking-wide">
            <?php echo $payoutBankAccount ? htmlspecialchars($payoutBankAccount) : '—'; ?>
          </p>
        </div>
        <?php endif; ?>
        <?php if ($payoutMethod === 'child_account' && $payoutChildName): ?>
        <div>
          <p class="text-xs uppercase tracking-wide text-slate-500">Child account</p>
          <p class="text-sm font-semibold text-slate-900 mt-1">
            <?php echo htmlspecialchars(trim($payoutChildName . ' ' . $payoutChildBankAccount)); ?>
          </p>
        </div>
        <?php endif; ?>
        <?php if ($payoutMethod === 'kiwisaver' && $payoutKiwisaverProvider): ?>
        <div>
          <p class="text-xs uppercase tracking-wide text-slate-500">KiwiSaver details</p>
          <p class="text-sm font-semibold text-slate-900 mt-1">
            <?php echo htmlspecialchars(trim($payoutKiwisaverProvider . ' ' . $payoutKiwisaverMemberId)); ?>
          </p>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="rounded-3xl border-2 border-emerald-100 bg-white p-8 shadow-xl">
      <div class="flex items-center justify-between gap-4 mb-6">
        <h2 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
          <span class="text-emerald-600 text-3xl">📄</span>
          <span>Payout History</span>
        </h2>
        <a href="/contact.php" class="btn text-sm md:text-base">Need help?</a>
      </div>

      <?php if (empty($payments)): ?>
        <div class="rounded-2xl border-2 border-dashed border-emerald-200 bg-emerald-50/60 p-8 text-center">
          <p class="text-lg font-semibold text-emerald-700 mb-2">No payments recorded yet</p>
          <p class="text-slate-600 max-w-2xl mx-auto">Once your pickups are processed, your payouts will appear here with dates, references, and amounts.</p>
        </div>
      <?php else: ?>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-emerald-200">
            <thead class="bg-emerald-50">
              <tr>
                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-emerald-700">Date</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-emerald-700">Reference</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-emerald-700">Notes</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-emerald-700">Status</th>
                <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-emerald-700">Amount</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-emerald-100">
              <?php foreach ($payments as $payment): ?>
                <?php
                  $amountFormatted = $currencySymbol . number_format($payment['amount'], 2);
                  $status = strtolower($payment['status'] ?? 'completed');
                  $statusStyles = [
                    'completed' => 'bg-emerald-100 text-emerald-700',
                    'pending' => 'bg-amber-100 text-amber-700',
                    'processing' => 'bg-blue-100 text-blue-700',
                    'failed' => 'bg-red-100 text-red-700',
                    'cancelled' => 'bg-slate-200 text-slate-700'
                  ];
                  $statusClass = $statusStyles[$status] ?? 'bg-slate-200 text-slate-700';
                  $notes = trim((string)($payment['notes'] ?? ''));
                ?>
                <tr class="hover:bg-emerald-50/40 transition-colors">
                  <td class="px-4 py-4 text-sm font-semibold text-slate-700">
                    <?php echo htmlspecialchars(date('F j, Y', strtotime($payment['paymentDate']))); ?>
                  </td>
                  <td class="px-4 py-4 text-sm text-slate-600">
                    <?php echo htmlspecialchars($payment['reference'] ?: '—'); ?>
                  </td>
                  <td class="px-4 py-4 text-sm text-slate-600">
                    <?php echo $notes !== '' ? nl2br(htmlspecialchars($notes)) : '—'; ?>
                  </td>
                  <td class="px-4 py-4">
                    <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold <?php echo $statusClass; ?>">
                      <span class="text-base">
                        <?php echo $status === 'completed' ? '✅' : ($status === 'pending' ? '⏳' : 'ℹ️'); ?>
                      </span>
                      <span><?php echo htmlspecialchars(ucfirst($status)); ?></span>
                    </span>
                  </td>
                  <td class="px-4 py-4 text-right text-sm font-bold text-slate-900">
                    <?php echo htmlspecialchars($amountFormatted); ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>

      <div class="mt-6 rounded-2xl bg-emerald-50/70 border border-emerald-200 px-6 py-4 text-sm text-emerald-800">
        <p class="font-semibold">Monthly Statements</p>
        <p>We recommend exporting data at the end of each month to send updates to your registered users. Automated reports are coming soon.</p>
      </div>
    </div>

    <?php if (function_exists('hasRole') && hasRole('admin')): ?>
    <div class="rounded-3xl border-2 border-slate-200 bg-gradient-to-r from-slate-50 via-white to-emerald-50/50 p-8 shadow-xl">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
          <h2 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
            <span class="text-slate-700 text-3xl">🧾</span>
            <span>Admin Payments</span>
          </h2>
          <p class="text-slate-600 mt-2 max-w-2xl">
            You have administrator access. Use the payments console to record payouts, view balances for all users, and manage monthly reports.
          </p>
        </div>
        <a href="/admin/payments.php" class="btn text-base px-6 py-3 bg-slate-800 hover:bg-slate-900">
          Go to Payments Admin
        </a>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const methodRadios = document.querySelectorAll('input[name="payoutMethod"]');
    const bankFields = document.getElementById('account-bank-fields');
    const childFields = document.getElementById('account-child-fields');
    const kiwiFields = document.getElementById('account-kiwi-fields');

    function togglePayoutSections(selected) {
      if (bankFields) bankFields.classList.toggle('hidden', selected !== 'bank');
      if (childFields) childFields.classList.toggle('hidden', selected !== 'child_account');
      if (kiwiFields) kiwiFields.classList.toggle('hidden', selected !== 'kiwisaver');
    }

    methodRadios.forEach(function (radio) {
      radio.addEventListener('change', function () {
        togglePayoutSections(this.value);
      });
    });

    const checked = document.querySelector('input[name="payoutMethod"]:checked');
    if (checked) {
      togglePayoutSections(checked.value);
    }
  });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

