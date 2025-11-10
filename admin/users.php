<?php
$pageTitle = 'Client Management';
$pageDescription = 'Manage registered users, update their details, reset passwords and send account communications.';

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/payments.php';

requireLogin('/admin/users.php');

if (!function_exists('hasRole') || !hasRole('admin')) {
    header('Location: /');
    exit;
}

/**
 * Parse a stored address string into individual components for form use.
 *
 * @param string|null $address
 * @return array{street:string,suburb:string,city:string,postcode:string}
 */
function parseAddressParts(?string $address): array
{
    $parts = [
        'street' => '',
        'suburb' => '',
        'city' => '',
        'postcode' => '',
    ];

    if (empty($address)) {
        return $parts;
    }

    $lines = preg_split("/\r\n|\r|\n/", trim($address));
    if (!$lines) {
        return $parts;
    }

    $lines = array_values(array_filter(array_map('trim', $lines), static function ($value) {
        return $value !== '';
    }));

    if (!empty($lines[0])) {
        $parts['street'] = $lines[0];
    }
    if (!empty($lines[1])) {
        $parts['suburb'] = $lines[1];
    }

    $cityLine = $lines[2] ?? '';
    if (!empty($cityLine)) {
        if (preg_match('/^(.*)\s+(\d{3,4})$/', $cityLine, $matches)) {
            $parts['city'] = trim($matches[1]);
            $parts['postcode'] = trim($matches[2]);
        } else {
            $parts['city'] = $cityLine;
        }
    }

    if ($parts['postcode'] === '' && !empty($lines[3])) {
        $parts['postcode'] = trim($lines[3]);
    }

    return $parts;
}

$alerts = [
    'success' => [],
    'error' => [],
];

/**
 * Append a success message.
 */
function addSuccess(string $message): void
{
    global $alerts;
    $alerts['success'][] = $message;
}

/**
 * Append an error message.
 */
function addError(string $message): void
{
    global $alerts;
    $alerts['error'][] = $message;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $targetUserId = trim($_POST['user_id'] ?? '');

    if ($targetUserId === '') {
        addError('Please select a valid user before performing that action.');
    } else {
        $targetUser = getUserById($targetUserId);
        if (!$targetUser) {
            addError('The selected user could not be found.');
        } else {
            switch ($action) {
                case 'update_account':
                    $accountData = [
                        'firstName' => $_POST['first_name'] ?? '',
                        'lastName' => $_POST['last_name'] ?? '',
                        'email' => $_POST['email'] ?? '',
                        'username' => $_POST['username'] ?? '',
                        'role' => $_POST['role'] ?? $targetUser['role'] ?? 'user',
                    ];

                    $accountError = null;
                    if (adminUpdateUserAccount($targetUserId, $accountData, $accountError)) {
                        addSuccess('Account details updated successfully.');
                    } else {
                        addError($accountError ?? 'Unable to update account details.');
                    }
                    break;

                case 'update_profile':
                    $profileData = [
                        'street' => $_POST['address_street'] ?? '',
                        'suburb' => $_POST['address_suburb'] ?? '',
                        'city' => $_POST['address_city'] ?? '',
                        'postcode' => $_POST['address_postcode'] ?? '',
                        'phone' => $_POST['phone'] ?? '',
                        'marketingOptIn' => !empty($_POST['marketing_opt_in']),
                        'payoutMethod' => $_POST['payout_method'] ?? 'bank',
                        'bankName' => $_POST['bank_name'] ?? '',
                        'bankAccount' => $_POST['bank_account'] ?? '',
                        'childName' => $_POST['child_name'] ?? '',
                        'childBankAccount' => $_POST['child_bank_account'] ?? '',
                        'kiwisaverProvider' => $_POST['kiwisaver_provider'] ?? '',
                        'kiwisaverMemberId' => $_POST['kiwisaver_member_id'] ?? '',
                    ];

                    if (updateUserProfile($targetUserId, $profileData)) {
                        addSuccess('Contact, address and payout details updated.');
                    } else {
                        addError('Failed to update contact or payout details. Please try again.');
                    }
                    break;

                case 'reset_password':
                    $newPassword = $_POST['new_password'] ?? '';
                    $confirmPassword = $_POST['new_password_confirm'] ?? '';

                    if ($newPassword !== $confirmPassword) {
                        addError('New password and confirmation do not match.');
                    } else {
                        $passwordError = null;
                        if (adminResetUserPassword($targetUserId, $newPassword, $passwordError)) {
                            addSuccess('Password reset successfully. The user will need to use the new password on their next login.');
                        } else {
                            addError($passwordError ?? 'Unable to reset password.');
                        }
                    }
                    break;

                case 'send_email':
                    $emailSubject = trim($_POST['email_subject'] ?? '');
                    $emailBody = trim($_POST['email_body'] ?? '');
                    $copyMe = !empty($_POST['email_copy']);

                    if (empty($targetUser['email'])) {
                        addError('This user does not have an email address on file.');
                    } elseif ($emailSubject === '' || $emailBody === '') {
                        addError('Please provide both a subject and a message before sending an email.');
                    } else {
                        $headers = [
                            'From: ' . SUPPORT_EMAIL,
                            'Reply-To: ' . SUPPORT_EMAIL,
                            'Content-Type: text/plain; charset=UTF-8',
                        ];
                        if ($copyMe) {
                            $headers[] = 'Cc: ' . SUPPORT_EMAIL;
                        }

                        $sent = @mail(
                            $targetUser['email'],
                            $emailSubject,
                            $emailBody,
                            implode("\r\n", $headers)
                        );

                        if ($sent) {
                            addSuccess('Email sent to ' . htmlspecialchars($targetUser['email'], ENT_QUOTES, 'UTF-8') . '.');
                        } else {
                            addError('Unable to send email. Please check your mail server configuration.');
                        }
                    }
                    break;

                case 'delete_user':
                    $currentAdminId = $_SESSION['user_id'] ?? null;
                    $adminUsers = array_filter(getUsers(), static function ($user) {
                        return ($user['role'] ?? 'user') === 'admin';
                    });

                    if ($targetUserId === $currentAdminId) {
                        addError('You cannot delete your own administrator account.');
                    } elseif (($targetUser['role'] ?? 'user') === 'admin' && count($adminUsers) <= 1) {
                        addError('At least one administrator account must remain.');
                    } elseif (getUserBalance($targetUserId, ['pending', 'processing']) > 0) {
                        addError('This user has pending or processing payouts and cannot be deleted.');
                    } else {
                        if (deleteUserById($targetUserId)) {
                            addSuccess('User account deleted successfully.');
                        } else {
                            addError('Failed to delete user account. Please try again.');
                        }
                    }
                    break;

                default:
                    addError('Unknown action requested.');
                    break;
            }
        }
    }
}

// Refresh user dataset after any actions.
$allUsers = getUsers();
$balances = getAllUserBalances();
$pendingBalances = getAllUserBalances(['pending', 'processing']);

$totalUsers = count($allUsers);
$adminCount = 0;
foreach ($allUsers as $user) {
    if (($user['role'] ?? 'user') === 'admin') {
        $adminCount++;
    }
}

usort($allUsers, static function ($a, $b) {
    $nameA = strtolower(($a['firstName'] ?? '') . ' ' . ($a['lastName'] ?? '') . ' ' . ($a['username'] ?? ''));
    $nameB = strtolower(($b['firstName'] ?? '') . ' ' . ($b['lastName'] ?? '') . ' ' . ($b['username'] ?? ''));
    return $nameA <=> $nameB;
});

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-16">
  <div class="space-y-10">
    <div class="rounded-3xl border-2 border-emerald-200 bg-gradient-to-r from-emerald-50 via-white to-emerald-50/60 p-8 shadow-xl">
      <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-4xl font-extrabold text-slate-900 mb-2 flex items-center gap-3">
            <span class="text-emerald-600 text-5xl">👥</span>
            <span>Client Management</span>
          </h1>
          <p class="text-slate-600 max-w-3xl">
            View and manage every registered Trash2Cash member. Update their personal details, reset passwords,
            send one-off email messages and tidy up accounts when needed.
          </p>
          <div class="mt-4 flex flex-wrap gap-4 text-sm font-semibold text-slate-600">
            <span class="inline-flex items-center gap-2 rounded-full bg-emerald-100 px-3 py-1 text-emerald-700">
              <span>👤</span> <span>Total users: <?php echo (int) $totalUsers; ?></span>
            </span>
            <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-slate-700">
              <span>⭐</span> <span>Admins: <?php echo (int) $adminCount; ?></span>
            </span>
          </div>
        </div>
        <div class="rounded-2xl border-2 border-emerald-200 bg-white px-6 py-4 text-sm text-slate-600 shadow-md">
          <p class="font-semibold text-slate-500 uppercase tracking-[0.2em]">Tips</p>
          <ul class="mt-2 space-y-1 list-disc list-inside">
            <li>Email sends use the site mail configuration.</li>
            <li>Resetting a password logs the user out of the mobile app.</li>
            <li>Keep at least one admin account available.</li>
          </ul>
        </div>
      </div>
    </div>

    <?php foreach ($alerts['error'] as $errorMessage): ?>
      <div class="rounded-2xl border-2 border-red-200 bg-red-50/80 px-4 py-3 text-sm text-red-700">
        <?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?>
      </div>
    <?php endforeach; ?>

    <?php foreach ($alerts['success'] as $successMessage): ?>
      <div class="rounded-2xl border-2 border-emerald-200 bg-emerald-50/80 px-4 py-3 text-sm text-emerald-700">
        <?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?>
      </div>
    <?php endforeach; ?>

    <div class="rounded-3xl border border-emerald-100 bg-white p-6 shadow-lg">
      <label for="user-search" class="block text-sm font-semibold text-slate-700 mb-2">Search users</label>
      <input
        id="user-search"
        type="search"
        placeholder="Search by name, email, username or phone..."
        class="w-full rounded-xl border-2 border-emerald-100 px-4 py-3 text-sm shadow-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/40"
      />
    </div>

    <div class="space-y-8" id="user-list">
      <?php if (empty($allUsers)): ?>
        <div class="rounded-3xl border-2 border-slate-200 bg-white/80 p-8 text-center text-slate-600 shadow">
          No registered users found yet.
        </div>
      <?php else: ?>
        <?php foreach ($allUsers as $user): ?>
          <?php
            $userId = $user['id'] ?? '';
            $firstName = $user['firstName'] ?? '';
            $lastName = $user['lastName'] ?? '';
            $displayName = trim($firstName . ' ' . $lastName);
            if ($displayName === '') {
                $displayName = $user['username'] ?? 'Unknown user';
            }
            $addressParts = parseAddressParts($user['address'] ?? '');
            $marketingOptIn = !empty($user['marketingOptIn']);
            $balance = $balances[$userId] ?? 0.0;
            $pendingBalance = $pendingBalances[$userId] ?? 0.0;
            $role = $user['role'] ?? 'user';
            $created = $user['created_at'] ?? ($user['createdAt'] ?? null);
            $lastLogin = $user['last_login'] ?? ($user['lastLogin'] ?? null);
          ?>
          <section
            class="rounded-3xl border-2 border-emerald-100 bg-white p-8 shadow-xl transition hover:-translate-y-1 hover:shadow-2xl"
            data-user-card
            data-filter-text="<?php echo htmlspecialchars(strtolower($displayName . ' ' . ($user['email'] ?? '') . ' ' . ($user['username'] ?? '') . ' ' . ($user['phone'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>"
          >
            <header class="flex flex-col gap-4 border-b border-emerald-100 pb-5 md:flex-row md:items-start md:justify-between">
              <div>
                <div class="flex items-center gap-3">
                  <h2 class="text-2xl font-bold text-slate-900"><?php echo htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8'); ?></h2>
                  <?php if ($role === 'admin'): ?>
                    <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">Admin</span>
                  <?php endif; ?>
                </div>
                <p class="mt-1 text-sm text-slate-600 flex flex-wrap gap-3">
                  <span class="flex items-center gap-1">
                    <span>🧑</span>
                    <span><?php echo htmlspecialchars($user['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                  </span>
                  <?php if (!empty($user['email'])): ?>
                    <span class="flex items-center gap-1">
                      <span>✉️</span>
                      <a href="mailto:<?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?>" class="text-brand hover:underline">
                        <?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?>
                      </a>
                    </span>
                  <?php endif; ?>
                  <?php if (!empty($user['phone'])): ?>
                    <span class="flex items-center gap-1">
                      <span>📞</span>
                      <a href="tel:<?php echo htmlspecialchars($user['phone'], ENT_QUOTES, 'UTF-8'); ?>" class="text-brand hover:underline">
                        <?php echo htmlspecialchars($user['phone'], ENT_QUOTES, 'UTF-8'); ?>
                      </a>
                    </span>
                  <?php endif; ?>
                </p>
              </div>
              <div class="grid gap-2 text-sm text-slate-600">
                <div class="inline-flex items-center gap-2 rounded-full bg-emerald-100 px-3 py-1 text-emerald-700 font-semibold">
                  <span>💰</span>
                  <span>Balance: $<?php echo number_format($balance, 2); ?></span>
                </div>
                <?php if ($pendingBalance > 0): ?>
                  <div class="inline-flex items-center gap-2 rounded-full bg-amber-100 px-3 py-1 text-amber-700 font-semibold">
                    <span>⏳</span>
                    <span>Pending payouts: $<?php echo number_format($pendingBalance, 2); ?></span>
                  </div>
                <?php endif; ?>
                <?php if (!empty($created)): ?>
                  <div class="flex items-center gap-2">
                    <span>🗓️</span>
                    <span>Joined: <?php echo htmlspecialchars(date('d M Y', strtotime($created)), ENT_QUOTES, 'UTF-8'); ?></span>
                  </div>
                <?php endif; ?>
                <?php if (!empty($lastLogin)): ?>
                  <div class="flex items-center gap-2 text-slate-500">
                    <span>🔑</span>
                    <span>Last login: <?php echo htmlspecialchars(date('d M Y H:i', strtotime($lastLogin)), ENT_QUOTES, 'UTF-8'); ?></span>
                  </div>
                <?php endif; ?>
              </div>
            </header>

            <div class="mt-6 grid gap-8 lg:grid-cols-2">
              <form method="post" class="space-y-4 rounded-2xl border border-emerald-100 bg-emerald-50/40 p-6 shadow-inner">
                <input type="hidden" name="action" value="update_account" />
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($userId, ENT_QUOTES, 'UTF-8'); ?>" />
                <h3 class="text-lg font-semibold text-slate-900 flex items-center gap-2">
                  <span>🪪</span>
                  <span>Account details</span>
                </h3>
                <div class="grid gap-4 sm:grid-cols-2">
                  <div>
                    <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">First name</label>
                    <input
                      type="text"
                      name="first_name"
                      value="<?php echo htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8'); ?>"
                      class="mt-1 w-full rounded-xl border border-emerald-100 px-3 py-2 text-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/40"
                    />
                  </div>
                  <div>
                    <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Last name</label>
                    <input
                      type="text"
                      name="last_name"
                      value="<?php echo htmlspecialchars($lastName, ENT_QUOTES, 'UTF-8'); ?>"
                      class="mt-1 w-full rounded-xl border border-emerald-100 px-3 py-2 text-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/40"
                    />
                  </div>
                </div>
                <div>
                  <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Email</label>
                  <input
                    type="email"
                    name="email"
                    value="<?php echo htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                    class="mt-1 w-full rounded-xl border border-emerald-100 px-3 py-2 text-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/40"
                  />
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                  <div>
                    <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Username</label>
                    <input
                      type="text"
                      name="username"
                      required
                      value="<?php echo htmlspecialchars($user['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                      class="mt-1 w-full rounded-xl border border-emerald-100 px-3 py-2 text-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/40"
                    />
                  </div>
                  <div>
                    <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Role</label>
                    <select
                      name="role"
                      class="mt-1 w-full rounded-xl border border-emerald-100 px-3 py-2 text-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/40"
                    >
                      <option value="user" <?php echo $role === 'user' ? 'selected' : ''; ?>>User</option>
                      <option value="admin" <?php echo $role === 'admin' ? 'selected' : ''; ?>>Administrator</option>
                    </select>
                  </div>
                </div>
                <button type="submit" class="btn text-sm">Update account</button>
              </form>

              <form method="post" class="space-y-4 rounded-2xl border border-slate-200 bg-slate-50/60 p-6 shadow-inner">
                <input type="hidden" name="action" value="update_profile" />
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($userId, ENT_QUOTES, 'UTF-8'); ?>" />
                <h3 class="text-lg font-semibold text-slate-900 flex items-center gap-2">
                  <span>📬</span>
                  <span>Contact &amp; payout</span>
                </h3>
                <div>
                  <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Phone</label>
                  <input
                    type="text"
                    name="phone"
                    value="<?php echo htmlspecialchars($user['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                    class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/40"
                  />
                </div>
                <div>
                  <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Street</label>
                  <input
                    type="text"
                    name="address_street"
                    value="<?php echo htmlspecialchars($addressParts['street'], ENT_QUOTES, 'UTF-8'); ?>"
                    class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/40"
                  />
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                  <div>
                    <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Suburb</label>
                    <input
                      type="text"
                      name="address_suburb"
                      value="<?php echo htmlspecialchars($addressParts['suburb'], ENT_QUOTES, 'UTF-8'); ?>"
                      class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/40"
                    />
                  </div>
                  <div>
                    <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">City</label>
                    <input
                      type="text"
                      name="address_city"
                      value="<?php echo htmlspecialchars($addressParts['city'], ENT_QUOTES, 'UTF-8'); ?>"
                      class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/40"
                    />
                  </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                  <div>
                    <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Postcode</label>
                    <input
                      type="text"
                      name="address_postcode"
                      maxlength="4"
                      value="<?php echo htmlspecialchars($addressParts['postcode'], ENT_QUOTES, 'UTF-8'); ?>"
                      class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/40"
                    />
                  </div>
                  <div class="flex items-center gap-3 pt-5">
                    <input type="hidden" name="marketing_opt_in" value="0" />
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                      <input
                        type="checkbox"
                        name="marketing_opt_in"
                        value="1"
                        <?php echo $marketingOptIn ? 'checked' : ''; ?>
                        class="h-4 w-4 rounded border-slate-300 text-brand focus:ring-brand"
                      />
                      Subscribe to updates
                    </label>
                  </div>
                </div>
                <div>
                  <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Payout method</label>
                  <?php $payoutMethod = $user['payoutMethod'] ?? 'bank'; ?>
                  <select
                    name="payout_method"
                    class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/40"
                  >
                    <option value="bank" <?php echo $payoutMethod === 'bank' ? 'selected' : ''; ?>>Bank</option>
                    <option value="child_account" <?php echo $payoutMethod === 'child_account' ? 'selected' : ''; ?>>Child account</option>
                    <option value="kiwisaver" <?php echo $payoutMethod === 'kiwisaver' ? 'selected' : ''; ?>>KiwiSaver</option>
                  </select>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                  <div>
                    <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Account holder name</label>
                    <input
                      type="text"
                      name="bank_name"
                      value="<?php echo htmlspecialchars($user['payoutBankName'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                      class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/40"
                    />
                  </div>
                  <div>
                    <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Bank account</label>
                    <input
                      type="text"
                      name="bank_account"
                      value="<?php echo htmlspecialchars($user['payoutBankAccount'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                      class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/40"
                    />
                  </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                  <div>
                    <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Child name</label>
                    <input
                      type="text"
                      name="child_name"
                      value="<?php echo htmlspecialchars($user['payoutChildName'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                      class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/40"
                    />
                  </div>
                  <div>
                    <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Child bank account</label>
                    <input
                      type="text"
                      name="child_bank_account"
                      value="<?php echo htmlspecialchars($user['payoutChildBankAccount'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                      class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/40"
                    />
                  </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                  <div>
                    <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">KiwiSaver provider</label>
                    <input
                      type="text"
                      name="kiwisaver_provider"
                      value="<?php echo htmlspecialchars($user['payoutKiwisaverProvider'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                      class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/40"
                    />
                  </div>
                  <div>
                    <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">KiwiSaver member ID</label>
                    <input
                      type="text"
                      name="kiwisaver_member_id"
                      value="<?php echo htmlspecialchars($user['payoutKiwisaverMemberId'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                      class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/40"
                    />
                  </div>
                </div>
                <button type="submit" class="btn-secondary text-sm">Save contact &amp; payout</button>
              </form>
            </div>

            <div class="mt-8 grid gap-8 lg:grid-cols-2">
              <form method="post" class="space-y-4 rounded-2xl border border-emerald-100 bg-white p-6 shadow-inner">
                <input type="hidden" name="action" value="reset_password" />
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($userId, ENT_QUOTES, 'UTF-8'); ?>" />
                <h3 class="text-lg font-semibold text-slate-900 flex items-center gap-2">
                  <span>🔐</span>
                  <span>Reset password</span>
                </h3>
                <div>
                  <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">New password</label>
                  <input
                    type="password"
                    name="new_password"
                    minlength="6"
                    required
                    class="mt-1 w-full rounded-xl border border-emerald-100 px-3 py-2 text-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/40"
                  />
                </div>
                <div>
                  <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Confirm password</label>
                  <input
                    type="password"
                    name="new_password_confirm"
                    minlength="6"
                    required
                    class="mt-1 w-full rounded-xl border border-emerald-100 px-3 py-2 text-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/40"
                  />
                </div>
                <button type="submit" class="btn text-sm">Reset password</button>
              </form>

              <form method="post" class="space-y-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-inner">
                <input type="hidden" name="action" value="send_email" />
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($userId, ENT_QUOTES, 'UTF-8'); ?>" />
                <h3 class="text-lg font-semibold text-slate-900 flex items-center gap-2">
                  <span>📧</span>
                  <span>Send quick email</span>
                </h3>
                <div>
                  <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Subject</label>
                  <input
                    type="text"
                    name="email_subject"
                    maxlength="150"
                    class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/40"
                    placeholder="Subject line"
                  />
                </div>
                <div>
                  <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Message</label>
                  <textarea
                    name="email_body"
                    rows="5"
                    class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/40"
                    placeholder="Write a personalised message..."
                  ></textarea>
                </div>
                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                  <input
                    type="checkbox"
                    name="email_copy"
                    value="1"
                    class="h-4 w-4 rounded border-slate-300 text-brand focus:ring-brand"
                  />
                  Send a copy to <?php echo htmlspecialchars(SUPPORT_EMAIL, ENT_QUOTES, 'UTF-8'); ?>
                </label>
                <button type="submit" class="btn-secondary text-sm">Send email</button>
              </form>
            </div>

            <div class="mt-8 rounded-2xl border-2 border-red-200 bg-red-50/70 p-6">
              <form method="post" onsubmit="return confirm('Are you sure you want to delete this account? This action cannot be undone.');">
                <input type="hidden" name="action" value="delete_user" />
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($userId, ENT_QUOTES, 'UTF-8'); ?>" />
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                  <div>
                    <h3 class="text-lg font-semibold text-red-700 flex items-center gap-2">
                      <span>⚠️</span>
                      <span>Danger zone</span>
                    </h3>
                    <p class="text-sm text-red-600">
                      Permanently delete this account and all associated payment history.
                      Ensure all payouts are settled before removing a user.
                    </p>
                  </div>
                  <button type="submit" class="inline-flex items-center justify-center rounded-xl border border-red-400 bg-white px-4 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-100">
                    Delete account
                  </button>
                </div>
              </form>
            </div>
          </section>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
  (function() {
    const searchInput = document.getElementById('user-search');
    if (!searchInput) {
      return;
    }
    const cards = Array.from(document.querySelectorAll('[data-user-card]'));

    searchInput.addEventListener('input', function() {
      const needle = this.value.trim().toLowerCase();
      cards.forEach(function(card) {
        const haystack = card.getAttribute('data-filter-text') || '';
        if (needle === '' || haystack.indexOf(needle) !== -1) {
          card.classList.remove('hidden');
        } else {
          card.classList.add('hidden');
        }
      });
    });
  })();
</script>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>

