<?php
$pageTitle = 'Payments Admin';
$pageDescription = 'Record and review payouts for registered users.';

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/payments.php';

requireLogin('/admin/payments.php');

if (!function_exists('hasRole') || !hasRole('admin')) {
    header('Location: /');
    exit;
}

if (!function_exists('buildAdminUserData')) {
    /**
     * Load active users and option data for admin screens.
     *
     * @return array{active: array<string, array>, options: array<string, array>}
     */
    function buildAdminUserData() {
        $rawUsers = getUsers();
        $active = [];
        $options = [];
        foreach ($rawUsers as $user) {
            if (($user['role'] ?? 'user') === 'deleted') {
                continue;
            }
            if (empty($user['id'])) {
                continue;
            }
            $active[$user['id']] = $user;
            $options[$user['id']] = [
                'username' => $user['username'] ?? '',
                'firstName' => $user['firstName'] ?? ($user['first_name'] ?? ''),
                'lastName' => $user['lastName'] ?? ($user['last_name'] ?? ''),
                'email' => $user['email'] ?? ''
            ];
        }
        return ['active' => $active, 'options' => $options];
    }
}

$currentAdminId = $_SESSION['user_id'] ?? null;
$userData = buildAdminUserData();
$activeUsers = $userData['active'];
$userOptions = $userData['options'];

$successMessage = '';
$errorMessage = '';
$deleteSuccessMessage = '';
$deleteErrorMessage = '';
$statusSuccessMessage = '';
$statusErrorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'delete_user') {
        $userIdToDelete = $_POST['delete_user_id'] ?? '';
        if (empty($userIdToDelete) || !isset($userOptions[$userIdToDelete])) {
            $deleteErrorMessage = 'Please select a valid user to delete.';
        } elseif ($userIdToDelete === $currentAdminId) {
            $deleteErrorMessage = 'You cannot delete your own administrator account.';
        } elseif (getUserBalance($userIdToDelete, ['pending', 'processing']) > 0) {
            $deleteErrorMessage = 'Cannot delete a user with pending or processing payouts.';
        } else {
            if (deleteUserById($userIdToDelete)) {
                $deleteSuccessMessage = 'User account deleted successfully.';
                $_POST = [];
            } else {
                $deleteErrorMessage = 'Failed to delete user. Please try again.';
            }
        }
        $userData = buildAdminUserData();
        $activeUsers = $userData['active'];
        $userOptions = $userData['options'];
    } elseif ($action === 'record_payment') {
        $selectedUserId = $_POST['user_id'] ?? '';
        $amountInput = $_POST['amount'] ?? '';
        $reference = trim($_POST['reference'] ?? '');
        $notes = trim($_POST['notes'] ?? '');
        $paymentDate = trim($_POST['payment_date'] ?? '');
        $status = $_POST['status'] ?? 'pending';
        $currency = strtoupper(trim($_POST['currency'] ?? 'NZD'));

        if (!isset($userOptions[$selectedUserId])) {
            $errorMessage = 'Please select a valid user.';
        } else {
            $amount = filter_var($amountInput, FILTER_VALIDATE_FLOAT);
            if ($amount === false) {
                $errorMessage = 'Please enter a valid payment amount.';
            } elseif ($amount <= 0) {
                $errorMessage = 'Payment amount must be greater than zero.';
            } else {
                if (empty($paymentDate)) {
                    $paymentDate = gmdate('Y-m-d');
                }

                $isRecorded = recordUserPayment(
                    $selectedUserId,
                    $amount,
                    $reference ?: null,
                    $notes ?: null,
                    $paymentDate,
                    $status,
                    $currency
                );

                if ($isRecorded) {
                    $successMessage = 'Payment recorded successfully.';
                    $_POST = [
                        'currency' => $currency,
                        'status' => $status,
                        'payment_date' => $paymentDate
                    ];
                } else {
                    $errorMessage = 'Failed to record payment. Please try again.';
                }
            }
        }
    } elseif ($action === 'update_payment_status') {
        $paymentId = $_POST['payment_id'] ?? '';
        $nextStatus = strtolower(trim($_POST['next_status'] ?? ''));

        if ($paymentId === '' || $nextStatus === '') {
            $statusErrorMessage = 'Please choose a payout entry and action.';
        } else {
            $payment = getPaymentById($paymentId);
            if (!$payment) {
                $statusErrorMessage = 'The selected payout could not be found.';
            } else {
                $currentStatus = strtolower($payment['status'] ?? '');
                $allowedTransitions = [
                    'pending' => ['processing', 'completed'],
                    'processing' => ['completed']
                ];

                if (!isset($allowedTransitions[$currentStatus]) || !in_array($nextStatus, $allowedTransitions[$currentStatus], true)) {
                    $statusErrorMessage = 'This payout cannot transition to the selected status.';
                } else {
                    $updates = [];
                    if ($nextStatus === 'completed') {
                        $updates['payment_date'] = gmdate('Y-m-d');
                    }

                    if (updatePaymentStatus($paymentId, $nextStatus, $updates)) {
                        $statusSuccessMessage = 'Payout updated to ' . ucfirst($nextStatus) . '.';
                        $_POST = [];
                    } else {
                        $statusErrorMessage = 'Unable to update payout status. Please try again.';
                    }
                }
            }
        }
    }
}

$paidBalancesMap = getAllUserBalances(['completed']);
$owedBalancesMap = getAllUserBalances(['pending', 'processing']);
$pendingPayments = getPaymentsByStatus(['pending', 'processing']);
$recentPayments = getRecentPayments(50);
$totalOwed = 0.0;
foreach ($owedBalancesMap as $owedAmount) {
    $totalOwed += (float) $owedAmount;
}
$pendingPaymentCount = count($pendingPayments);

// Prepare balances list combining users and totals
$userBalances = [];
foreach ($activeUsers as $id => $details) {
    $firstName = $details['firstName'] ?? ($details['first_name'] ?? '');
    $lastName = $details['lastName'] ?? ($details['last_name'] ?? '');
    $owedBalance = $owedBalancesMap[$id] ?? 0.0;
    $paidBalance = $paidBalancesMap[$id] ?? 0.0;
    $userBalances[] = [
        'id' => $id,
        'username' => $details['username'] ?? '',
        'firstName' => $firstName,
        'lastName' => $lastName,
        'email' => $details['email'] ?? '',
        'owed' => $owedBalance,
        'paid' => $paidBalance
    ];
}

usort($userBalances, function ($a, $b) {
    return $b['owed'] <=> $a['owed'];
});

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-16">
  <div class="space-y-12">
    <div class="rounded-3xl border-2 border-emerald-200 bg-gradient-to-r from-emerald-50 via-white to-emerald-50/60 p-8 shadow-xl">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
          <h1 class="text-4xl font-extrabold text-slate-900 mb-2 flex items-center gap-3">
            <span class="text-emerald-600 text-5xl">🧾</span>
            <span>Payments Admin</span>
          </h1>
          <p class="text-slate-600 max-w-2xl">
            Record payouts and review balances for all registered users. This interface is restricted to administrators.
          </p>
        </div>
        <div class="rounded-2xl border-2 border-emerald-200 bg-white px-6 py-4 text-sm text-slate-600 shadow-md">
          <p class="font-semibold text-slate-500 uppercase tracking-[0.2em]">Quick Tips</p>
          <ul class="mt-2 space-y-1 list-disc list-inside">
            <li>Pickup credits land as pending until you send the payout</li>
            <li>Use “Initiate payout” when you start a transfer</li>
            <li>Mark payouts as completed once funds are sent</li>
          </ul>
        </div>
      </div>
    </div>

    <?php if (!empty($deleteErrorMessage)): ?>
    <div class="rounded-2xl border-2 border-red-200 bg-red-50/80 px-4 py-3 text-sm text-red-700">
      <?php echo htmlspecialchars($deleteErrorMessage); ?>
    </div>
    <?php elseif (!empty($deleteSuccessMessage)): ?>
    <div class="rounded-2xl border-2 border-emerald-200 bg-emerald-50/80 px-4 py-3 text-sm text-emerald-700">
      <?php echo htmlspecialchars($deleteSuccessMessage); ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($statusErrorMessage)): ?>
    <div class="rounded-2xl border-2 border-red-200 bg-red-50/80 px-4 py-3 text-sm text-red-700">
      <?php echo htmlspecialchars($statusErrorMessage); ?>
    </div>
    <?php elseif (!empty($statusSuccessMessage)): ?>
    <div class="rounded-2xl border-2 border-emerald-200 bg-emerald-50/80 px-4 py-3 text-sm text-emerald-700">
      <?php echo htmlspecialchars($statusSuccessMessage); ?>
    </div>
    <?php endif; ?>

    <div class="rounded-3xl border-2 border-emerald-100 bg-emerald-50/40 p-8 shadow-xl">
      <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
        <div class="flex items-center gap-2">
          <span class="text-emerald-600 text-3xl">⏳</span>
          <h2 class="text-2xl font-bold text-slate-900">Pending Payouts</h2>
        </div>
        <div class="flex flex-wrap gap-3 text-sm font-semibold text-slate-600">
          <span class="inline-flex items-center gap-2 rounded-full bg-emerald-100 px-3 py-1 text-emerald-700">
            <span>💵</span>
            <span>Owed: $<?php echo number_format($totalOwed, 2); ?></span>
          </span>
          <span class="inline-flex items-center gap-2 rounded-full bg-emerald-100 px-3 py-1 text-emerald-700">
            <span>📋</span>
            <span><?php echo (int) $pendingPaymentCount; ?> open payout<?php echo $pendingPaymentCount === 1 ? '' : 's'; ?></span>
          </span>
        </div>
      </div>

      <?php if (empty($pendingPayments)): ?>
        <div class="rounded-2xl border-2 border-dashed border-emerald-200 bg-white/70 p-6 text-center text-slate-600">
          No pending payouts right now. New pickup requests and bonuses will appear here automatically.
        </div>
      <?php else: ?>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-emerald-200">
            <thead class="bg-emerald-100">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-emerald-700">User</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-emerald-700">Reference</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-emerald-700">Created</th>
                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-emerald-700">Amount</th>
                <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-emerald-700">Status</th>
                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-emerald-700">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-emerald-100">
              <?php foreach ($pendingPayments as $payment): ?>
                <?php
                  $status = strtolower($payment['status'] ?? 'pending');
                  $statusStyles = [
                    'pending' => 'bg-amber-100 text-amber-700',
                    'processing' => 'bg-blue-100 text-blue-700',
                    'completed' => 'bg-emerald-100 text-emerald-700',
                    'failed' => 'bg-red-100 text-red-700',
                    'cancelled' => 'bg-slate-200 text-slate-700'
                  ];
                  $statusClass = $statusStyles[$status] ?? 'bg-slate-200 text-slate-700';
                  $notesPlain = trim((string)($payment['notes'] ?? ''));
                  $createdAt = $payment['createdAt'] ?? $payment['created_at'] ?? null;
                  $createdDisplay = $createdAt ? date('M j, Y H:i', strtotime($createdAt)) : '—';
                  $displayUserName = trim(($payment['firstName'] ?? '') . ' ' . ($payment['lastName'] ?? ''));
                  if ($displayUserName === '') {
                    $displayUserName = $payment['username'] ?? 'Unknown user';
                  }
                ?>
                <tr class="hover:bg-emerald-50/50 transition-colors">
                  <td class="px-4 py-4 text-sm font-semibold text-slate-700">
                    <?php echo htmlspecialchars($displayUserName); ?><br>
                    <span class="text-xs font-medium text-slate-500"><?php echo htmlspecialchars($payment['username'] ?? ''); ?></span>
                  </td>
                  <td class="px-4 py-4 text-sm text-slate-600">
                    <div class="font-semibold text-slate-800"><?php echo htmlspecialchars($payment['reference'] ?: '—'); ?></div>
                    <?php if ($notesPlain !== ''): ?>
                      <div class="mt-1 text-xs text-slate-500"><?php echo nl2br(htmlspecialchars($notesPlain)); ?></div>
                    <?php endif; ?>
                  </td>
                  <td class="px-4 py-4 text-sm text-slate-600">
                    <?php echo htmlspecialchars($createdDisplay); ?>
                  </td>
                  <td class="px-4 py-4 text-right text-sm font-bold text-slate-900">
                    <?php echo 'NZ$' . number_format((float) ($payment['amount'] ?? 0), 2); ?>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold <?php echo $statusClass; ?>">
                      <span class="text-base">
                        <?php echo $status === 'pending' ? '⏳' : ($status === 'processing' ? '🚚' : '✅'); ?>
                      </span>
                      <span><?php echo htmlspecialchars(ucfirst($status)); ?></span>
                    </span>
                  </td>
                  <td class="px-4 py-4 text-right text-sm">
                    <div class="flex flex-wrap justify-end gap-2">
                      <?php if ($status === 'pending'): ?>
                        <form method="POST">
                          <input type="hidden" name="action" value="update_payment_status">
                          <input type="hidden" name="payment_id" value="<?php echo htmlspecialchars($payment['id']); ?>">
                          <input type="hidden" name="next_status" value="processing">
                          <button type="submit" class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700 hover:bg-blue-200">
                            <span>🚚</span>
                            <span>Initiate payout</span>
                          </button>
                        </form>
                      <?php endif; ?>
                      <?php if (in_array($status, ['pending', 'processing'], true)): ?>
                        <form method="POST" onsubmit="return confirm('Mark this payout as completed?');">
                          <input type="hidden" name="action" value="update_payment_status">
                          <input type="hidden" name="payment_id" value="<?php echo htmlspecialchars($payment['id']); ?>">
                          <input type="hidden" name="next_status" value="completed">
                          <button type="submit" class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 hover:bg-emerald-200">
                            <span>✅</span>
                            <span>Mark completed</span>
                          </button>
                        </form>
                      <?php else: ?>
                        <span class="text-xs text-slate-400">No actions</span>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

    <div class="grid gap-10 lg:grid-cols-2">
      <div class="rounded-3xl border-2 border-emerald-100 bg-white p-8 shadow-xl">
        <h2 class="text-2xl font-bold text-slate-900 flex items-center gap-2 mb-6">
          <span class="text-emerald-600 text-3xl">➕</span>
          <span>Record a Payment</span>
        </h2>

        <?php if (!empty($errorMessage)): ?>
          <div class="mb-6 rounded-2xl border-2 border-red-200 bg-red-50/70 px-4 py-3 text-red-700">
            <?php echo htmlspecialchars($errorMessage); ?>
          </div>
        <?php elseif (!empty($successMessage)): ?>
          <div class="mb-6 rounded-2xl border-2 border-emerald-200 bg-emerald-50/70 px-4 py-3 text-emerald-700">
            <?php echo htmlspecialchars($successMessage); ?>
          </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
          <input type="hidden" name="action" value="record_payment">

          <div>
            <label for="user_id" class="block text-sm font-semibold text-slate-900 mb-2">Select User</label>
            <select
              id="user_id"
              name="user_id"
              required
              class="w-full rounded-xl border-2 border-emerald-200 px-4 py-3 focus:border-brand focus:ring-2 focus:ring-emerald-200 transition-all"
            >
              <option value="">-- Choose a user --</option>
              <?php foreach ($userBalances as $user): ?>
                <?php
                  $optionLabelParts = [];
                  if (!empty($user['firstName']) || !empty($user['lastName'])) {
                      $optionLabelParts[] = trim($user['firstName'] . ' ' . $user['lastName']);
                  }
                  $optionLabelParts[] = $user['username'];
                  if (!empty($user['email'])) {
                      $optionLabelParts[] = $user['email'];
                  }
                  $optionLabel = implode(' • ', array_filter($optionLabelParts));
                ?>
                <option value="<?php echo htmlspecialchars($user['id']); ?>" <?php echo (($_POST['user_id'] ?? '') === $user['id']) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($optionLabel); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <label for="amount" class="block text-sm font-semibold text-slate-900 mb-2">Amount (e.g. 25.50)</label>
              <input
                id="amount"
                name="amount"
                type="number"
                step="0.01"
                min="0"
                required
                class="w-full rounded-xl border-2 border-emerald-200 px-4 py-3 focus:border-brand focus:ring-2 focus:ring-emerald-200 transition-all"
                value="<?php echo htmlspecialchars($_POST['amount'] ?? ''); ?>"
              />
            </div>
            <div>
              <label for="currency" class="block text-sm font-semibold text-slate-900 mb-2">Currency</label>
              <select
                id="currency"
                name="currency"
                class="w-full rounded-xl border-2 border-emerald-200 px-4 py-3 focus:border-brand focus:ring-2 focus:ring-emerald-200 transition-all"
              >
                <?php $selectedCurrency = $_POST['currency'] ?? 'NZD'; ?>
                <option value="NZD" <?php echo ($selectedCurrency === 'NZD') ? 'selected' : ''; ?>>NZD</option>
                <option value="AUD" <?php echo ($selectedCurrency === 'AUD') ? 'selected' : ''; ?>>AUD</option>
                <option value="USD" <?php echo ($selectedCurrency === 'USD') ? 'selected' : ''; ?>>USD</option>
              </select>
            </div>
          </div>

          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <label for="payment_date" class="block text-sm font-semibold text-slate-900 mb-2">Payment Date</label>
              <input
                id="payment_date"
                name="payment_date"
                type="date"
                class="w-full rounded-xl border-2 border-emerald-200 px-4 py-3 focus:border-brand focus:ring-2 focus:ring-emerald-200 transition-all"
                value="<?php echo htmlspecialchars($_POST['payment_date'] ?? gmdate('Y-m-d')); ?>"
              />
            </div>
            <div>
              <label for="status" class="block text-sm font-semibold text-slate-900 mb-2">Status</label>
              <select
                id="status"
                name="status"
                class="w-full rounded-xl border-2 border-emerald-200 px-4 py-3 focus:border-brand focus:ring-2 focus:ring-emerald-200 transition-all"
              >
                <?php $selectedStatus = $_POST['status'] ?? 'pending'; ?>
                <option value="pending" <?php echo ($selectedStatus === 'pending') ? 'selected' : ''; ?>>Pending</option>
                <option value="processing" <?php echo ($selectedStatus === 'processing') ? 'selected' : ''; ?>>Processing</option>
                <option value="completed" <?php echo ($selectedStatus === 'completed') ? 'selected' : ''; ?>>Completed</option>
                <option value="failed" <?php echo ($selectedStatus === 'failed') ? 'selected' : ''; ?>>Failed</option>
                <option value="cancelled" <?php echo ($selectedStatus === 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
              </select>
            </div>
          </div>

          <div>
            <label for="reference" class="block text-sm font-semibold text-slate-900 mb-2">Reference (optional)</label>
            <input
              id="reference"
              name="reference"
              type="text"
              maxlength="100"
              class="w-full rounded-xl border-2 border-emerald-200 px-4 py-3 focus:border-brand focus:ring-2 focus:ring-emerald-200 transition-all"
              value="<?php echo htmlspecialchars($_POST['reference'] ?? ''); ?>"
              placeholder="e.g. INV-2025-001"
            />
          </div>

          <div>
            <label for="notes" class="block text-sm font-semibold text-slate-900 mb-2">Notes (optional)</label>
            <textarea
              id="notes"
              name="notes"
              rows="4"
              class="w-full rounded-xl border-2 border-emerald-200 px-4 py-3 focus:border-brand focus:ring-2 focus:ring-emerald-200 transition-all"
              placeholder="Include payout details for reporting"><?php echo htmlspecialchars($_POST['notes'] ?? ''); ?></textarea>
          </div>

          <button
            type="submit"
            class="w-full btn text-lg px-6 py-4 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all font-bold"
          >
            💸 Record Payment
          </button>
        </form>
      </div>

      <div class="rounded-3xl border-2 border-emerald-100 bg-emerald-50/40 p-8 shadow-xl">
        <h2 class="text-2xl font-bold text-slate-900 flex items-center gap-2 mb-6">
          <span class="text-emerald-600 text-3xl">📊</span>
          <span>Account Balances</span>
        </h2>
        <div class="overflow-x-auto rounded-2xl border border-emerald-200 bg-white shadow-md">
          <table class="min-w-full divide-y divide-emerald-200">
            <thead class="bg-emerald-100">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-emerald-700">User</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-emerald-700">Contact</th>
                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-emerald-700">Owed (NZD)</th>
                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-emerald-700">Paid to Date</th>
                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-emerald-700">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-emerald-100">
              <?php foreach ($userBalances as $user): ?>
                <?php
                  $displayName = trim($user['firstName'] . ' ' . $user['lastName']);
                  if ($displayName === '') {
                      $displayName = $user['username'];
                  }
                  $owedAmount = $user['owed'] ?? 0.0;
                  $paidAmount = $user['paid'] ?? 0.0;
                  $canDelete = ($user['id'] !== $currentAdminId) && ($owedAmount <= 0);
                ?>
                <tr class="hover:bg-emerald-50/50 transition-colors">
                  <td class="px-4 py-4 text-sm font-semibold text-slate-700">
                    <?php echo htmlspecialchars($displayName); ?><br>
                    <span class="text-xs font-medium text-slate-500"><?php echo htmlspecialchars($user['username']); ?></span>
                  </td>
                  <td class="px-4 py-4 text-sm text-slate-600">
                    <?php echo $user['email'] ? htmlspecialchars($user['email']) : '<span class="text-slate-400 italic">No email</span>'; ?>
                  </td>
                  <td class="px-4 py-4 text-right text-sm font-bold <?php echo ($owedAmount > 0) ? 'text-emerald-700' : 'text-slate-500'; ?>">
                    <?php echo 'NZ$' . number_format($owedAmount, 2); ?>
                  </td>
                  <td class="px-4 py-4 text-right text-sm font-semibold text-slate-900">
                    <?php echo 'NZ$' . number_format($paidAmount, 2); ?>
                  </td>
                  <td class="px-4 py-4 text-right text-sm">
                    <?php if (!$canDelete && $user['id'] === $currentAdminId): ?>
                      <span class="text-xs text-slate-400">This is you</span>
                    <?php elseif (!$canDelete && $owedAmount > 0): ?>
                      <span class="text-xs text-amber-600">Pending payouts</span>
                    <?php else: ?>
                      <form method="POST" onsubmit="return confirm('Delete user <?php echo htmlspecialchars($displayName); ?>? This action cannot be undone.');">
                        <input type="hidden" name="action" value="delete_user">
                        <input type="hidden" name="delete_user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                        <button type="submit" class="inline-flex items-center gap-1 rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700 hover:bg-red-200">
                          <span>🗑️</span> Delete
                        </button>
                      </form>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="rounded-3xl border-2 border-emerald-100 bg-white p-8 shadow-xl">
      <div class="flex items-center justify-between gap-4 mb-6">
        <h2 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
          <span class="text-emerald-600 text-3xl">📜</span>
          <span>Recent Payments</span>
        </h2>
        <span class="rounded-full bg-emerald-100 px-3 py-1 text-sm font-semibold text-emerald-700">Last 50 entries</span>
      </div>

      <?php if (empty($recentPayments)): ?>
        <div class="rounded-2xl border-2 border-dashed border-emerald-200 bg-emerald-50/60 p-6 text-center text-slate-600">
          No payments have been recorded yet. Add your first payment using the form above.
        </div>
      <?php else: ?>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-emerald-200">
            <thead class="bg-emerald-100">
              <tr>
                <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-emerald-700">Date</th>
                <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-emerald-700">User</th>
                <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-emerald-700">Reference</th>
                <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-emerald-700">Status</th>
                <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-emerald-700">Notes</th>
                <th class="px-3 py-3 text-right text-xs font-semibold uppercase tracking-wider text-emerald-700">Amount</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-emerald-100">
              <?php foreach ($recentPayments as $payment): ?>
                <?php
                  $status = strtolower($payment['status'] ?? 'completed');
                  $statusStyles = [
                    'completed' => 'bg-emerald-100 text-emerald-700',
                    'pending' => 'bg-amber-100 text-amber-700',
                    'processing' => 'bg-blue-100 text-blue-700',
                    'failed' => 'bg-red-100 text-red-700',
                    'cancelled' => 'bg-slate-200 text-slate-700'
                  ];
                  $statusClass = $statusStyles[$status] ?? 'bg-slate-200 text-slate-700';

                  $fullName = trim(($payment['firstName'] ?? '') . ' ' . ($payment['lastName'] ?? ''));
                  $displayUser = $fullName !== '' ? strtoupper($fullName) : ($payment['username'] ?? 'UNKNOWN USER');
                  $notes = trim((string)($payment['notes'] ?? ''));
                ?>
                <tr class="hover:bg-emerald-50/40 transition-colors">
                  <td class="px-3 py-3 text-sm font-semibold text-slate-700">
                    <?php echo htmlspecialchars(date('M j, Y', strtotime($payment['paymentDate']))); ?>
                  </td>
                  <td class="px-3 py-3 text-sm text-slate-600">
                    <span class="font-semibold text-slate-800"><?php echo htmlspecialchars($displayUser); ?></span><br>
                    <span class="text-xs text-slate-500"><?php echo htmlspecialchars($payment['username'] ?? ''); ?></span>
                  </td>
                  <td class="px-3 py-3 text-sm text-slate-600">
                    <?php echo htmlspecialchars($payment['reference'] ?: '—'); ?>
                  </td>
                  <td class="px-3 py-3">
                    <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold <?php echo $statusClass; ?>">
                      <span class="text-base">
                        <?php echo $status === 'completed' ? '✅' : ($status === 'pending' ? '⏳' : 'ℹ️'); ?>
                      </span>
                      <span><?php echo htmlspecialchars(ucfirst($status)); ?></span>
                    </span>
                  </td>
                  <td class="px-3 py-3 text-sm text-slate-600">
                    <?php echo $notes !== '' ? nl2br(htmlspecialchars($notes)) : '—'; ?>
                  </td>
                  <td class="px-3 py-3 text-right text-sm font-bold text-slate-900">
                    <?php echo htmlspecialchars(strtoupper($payment['currency'])) . ' ' . number_format($payment['amount'], 2); ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

