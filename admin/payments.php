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

$users = getUsers();
$userOptions = [];
foreach ($users as $user) {
    $userOptions[$user['id']] = [
        'username' => $user['username'] ?? '',
        'firstName' => $user['firstName'] ?? ($user['first_name'] ?? ''),
        'lastName' => $user['lastName'] ?? ($user['last_name'] ?? ''),
        'email' => $user['email'] ?? ''
    ];
}

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'record_payment') {
    $selectedUserId = $_POST['user_id'] ?? '';
    $amountInput = $_POST['amount'] ?? '';
    $reference = trim($_POST['reference'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    $paymentDate = trim($_POST['payment_date'] ?? '');
    $status = $_POST['status'] ?? 'completed';
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
                // Reset form values
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
}

$balancesMap = getAllUserBalances();
$recentPayments = getRecentPayments(50);

// Prepare balances list combining users and totals
$userBalances = [];
foreach ($userOptions as $id => $details) {
    $balance = $balancesMap[$id] ?? 0.0;
    $userBalances[] = [
        'id' => $id,
        'username' => $details['username'],
        'firstName' => $details['firstName'],
        'lastName' => $details['lastName'],
        'email' => $details['email'],
        'balance' => $balance
    ];
}

usort($userBalances, function ($a, $b) {
    return $b['balance'] <=> $a['balance'];
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
            <li>Use completed status for confirmed payouts</li>
            <li>Pending/processing won’t count toward balances</li>
            <li>All amounts are stored in the selected currency</li>
          </ul>
        </div>
      </div>
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
                <?php $selectedStatus = $_POST['status'] ?? 'completed'; ?>
                <option value="completed" <?php echo ($selectedStatus === 'completed') ? 'selected' : ''; ?>>Completed</option>
                <option value="pending" <?php echo ($selectedStatus === 'pending') ? 'selected' : ''; ?>>Pending</option>
                <option value="processing" <?php echo ($selectedStatus === 'processing') ? 'selected' : ''; ?>>Processing</option>
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
          <span>Current Balances</span>
        </h2>
        <div class="overflow-x-auto rounded-2xl border border-emerald-200 bg-white shadow-md">
          <table class="min-w-full divide-y divide-emerald-200">
            <thead class="bg-emerald-100">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-emerald-700">User</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-emerald-700">Contact</th>
                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-emerald-700">Balance (NZD)</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-emerald-100">
              <?php foreach ($userBalances as $user): ?>
                <?php
                  $displayName = trim($user['firstName'] . ' ' . $user['lastName']);
                  if ($displayName === '') {
                      $displayName = $user['username'];
                  }
                ?>
                <tr class="hover:bg-emerald-50/50 transition-colors">
                  <td class="px-4 py-4 text-sm font-semibold text-slate-700">
                    <?php echo htmlspecialchars($displayName); ?><br>
                    <span class="text-xs font-medium text-slate-500"><?php echo htmlspecialchars($user['username']); ?></span>
                  </td>
                  <td class="px-4 py-4 text-sm text-slate-600">
                    <?php echo $user['email'] ? htmlspecialchars($user['email']) : '<span class="text-slate-400 italic">No email</span>'; ?>
                  </td>
                  <td class="px-4 py-4 text-right text-sm font-bold text-slate-900">
                    <?php echo 'NZ$' . number_format($user['balance'], 2); ?>
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

