<?php
$pageTitle = 'Rewards';
$pageDescription = 'Earn $1 per 50 aluminium cans plus appliance pickup credits.';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-16">
  <div class="text-center mb-12">
    <h1 class="text-5xl font-extrabold text-slate-900 mb-4">
      <span class="gradient-text">💰 Rewards</span>
    </h1>
    <p class="text-xl text-slate-600 max-w-2xl mx-auto">Earn money while helping the environment</p>
  </div>

  <div class="grid gap-8 lg:grid-cols-2 mb-16">
    <!-- Cans Rewards -->
    <div class="rounded-2xl bg-gradient-to-br from-emerald-50 via-green-50 to-white p-8 border-2 border-emerald-200 shadow-xl">
      <div class="flex items-center gap-4 mb-6">
        <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-emerald-400 to-green-500 flex items-center justify-center text-4xl shadow-lg">
          🥤
        </div>
        <h2 class="text-3xl font-bold text-slate-900">$1 per 50 cans</h2>
      </div>
      <div class="space-y-4">
        <div class="flex items-center justify-between p-4 rounded-xl bg-white border-2 border-emerald-100 hover:border-brand transition-all">
          <span class="text-lg font-semibold">50 cans</span>
          <span class="text-2xl font-bold text-brand">→ $1</span>
        </div>
        <div class="flex items-center justify-between p-4 rounded-xl bg-white border-2 border-emerald-100 hover:border-brand transition-all">
          <span class="text-lg font-semibold">250 cans</span>
          <span class="text-2xl font-bold text-brand">→ $5</span>
        </div>
        <div class="flex items-center justify-between p-4 rounded-xl bg-white border-2 border-emerald-100 hover:border-brand transition-all">
          <span class="text-lg font-semibold">1,000 cans</span>
          <span class="text-2xl font-bold text-brand">→ $20</span>
        </div>
      </div>
      <div class="mt-6 p-4 rounded-lg bg-gradient-to-r from-emerald-100 to-green-100 border-l-4 border-brand">
        <p class="text-sm font-semibold text-slate-800">
          💡 Average NZ household ≈ <span class="text-brand text-lg">$500/year</span> in recyclable value!
        </p>
      </div>
    </div>

    <!-- Appliance Credits -->
    <div class="rounded-2xl bg-gradient-to-br from-blue-50 via-cyan-50 to-white p-8 border-2 border-blue-200 shadow-xl">
      <div class="flex items-center gap-4 mb-6">
        <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-blue-400 to-cyan-500 flex items-center justify-center text-4xl shadow-lg">
          🔧
        </div>
        <h2 class="text-3xl font-bold text-slate-900">Appliance Credits</h2>
      </div>
      <div class="overflow-hidden rounded-xl border-2 border-blue-100 bg-white">
        <table class="w-full text-left">
          <thead class="bg-gradient-to-r from-blue-500 to-cyan-500 text-white">
            <tr>
              <th class="p-4 font-bold">Appliance</th>
              <th class="p-4 font-bold text-right">Credit</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($APPLIANCE_CREDITS as $idx => $appliance): ?>
              <tr class="<?php echo $idx % 2 === 0 ? 'bg-white' : 'bg-blue-50/50'; ?> hover:bg-blue-100 transition-colors">
                <td class="p-4 font-semibold text-slate-900"><?php echo htmlspecialchars($appliance['label']); ?></td>
                <td class="p-4 text-right">
                  <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-gradient-to-r from-blue-500 to-cyan-500 text-white font-bold">
                    $<?php echo $appliance['credit']; ?>
                  </span>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="rounded-2xl bg-gradient-to-r from-emerald-600 to-green-600 p-8 text-white shadow-2xl">
    <div class="flex items-center gap-3 mb-4">
      <span class="text-4xl">💝</span>
      <h3 class="text-2xl font-bold">Kids' Accounts & KiwiSaver</h3>
    </div>
    <p class="text-emerald-100 text-lg">Deposits available with consent and ID check at payout time. Help your kids save or grow their KiwiSaver while recycling!</p>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

