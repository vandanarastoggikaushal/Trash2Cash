<?php
$pageTitle = 'My Account';
$pageDescription = 'View your Trash2Cash payout history and balance.';

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/payments.php';

requireLogin('/account.php');

$user = getCurrentUser();
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
        <?php if ($payoutMethod === 'bank' && ($payoutBankName || $payoutBankAccount)): ?>
        <div>
          <p class="text-xs uppercase tracking-wide text-slate-500">Bank details</p>
          <p class="text-sm font-semibold text-slate-900 mt-1">
            <?php echo htmlspecialchars(trim($payoutBankName . ' ' . $payoutBankAccount)); ?>
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

<?php require_once __DIR__ . '/includes/footer.php'; ?>

