<?php
$pageTitle = 'Rewards & Pricing';
$pageDescription = 'Earn $1 per 100 aluminium cans plus appliance credits. Washing machines $6, dishwashers $5, laptops $3, microwaves $2. Deposit to kids accounts or KiwiSaver.';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-16">
  <div class="text-center mb-12">
    <h1 class="text-5xl font-extrabold text-slate-900 mb-4">
      <span class="gradient-text">💰 Rewards</span>
    </h1>
    <p class="text-xl text-slate-600 max-w-2xl mx-auto">
      Find out how much you can earn recycling cans, appliances and scrap metal with Trash2Cash NZ.
    </p>
  </div>

  <div class="max-w-5xl mx-auto mb-16">
    <div class="grid gap-8 lg:grid-cols-2 rounded-3xl border-2 border-emerald-200 bg-white shadow-2xl overflow-hidden">
      <div class="bg-gradient-to-br from-emerald-600 via-green-600 to-emerald-500 text-white p-10 flex flex-col justify-between">
        <div>
          <p class="text-sm uppercase tracking-wide text-emerald-100 font-semibold mb-3">Interactive calculator</p>
          <h2 class="text-3xl font-bold mb-4">Estimate your yearly recycling payouts</h2>
          <p class="text-emerald-100 leading-relaxed mb-6">
            Adjust the sliders to match the cans you collect each week and any appliances you recycle through Trash2Cash NZ.
            We will show your estimated yearly, monthly and per-pickup payouts.
          </p>
        </div>
        <div class="space-y-3 text-sm text-emerald-100/90">
          <div class="flex items-start gap-3">
            <span class="mt-1 text-lg">✅</span>
            <p>Serving Wellington City, the Hutt Valley, Porirua, Kapiti Coast and nearby suburbs.</p>
          </div>
          <div class="flex items-start gap-3">
            <span class="mt-1 text-lg">🏦</span>
            <p>Send payouts to your bank, a child&apos;s savings account, or your KiwiSaver provider.</p>
          </div>
          <div class="flex items-start gap-3">
            <span class="mt-1 text-lg">📅</span>
            <p>Schedule your first pickup and update payout details in your account dashboard.</p>
          </div>
        </div>
      </div>
      <div class="p-10">
        <form id="rewards-calculator" class="space-y-8">
          <div>
            <label for="calc-cans-per-week" class="block text-sm font-semibold text-slate-700 mb-2">
              Cans collected per week
            </label>
            <input
              type="number"
              id="calc-cans-per-week"
              name="cans_per_week"
              min="0"
              value="10"
              class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-lg shadow-inner focus:border-brand focus:ring-brand"
            />
            <p class="mt-2 text-sm text-slate-500">We pay $1 for every 100 aluminium cans.</p>
          </div>

          <div>
            <h3 class="text-sm font-semibold text-slate-700 mb-4">Appliances recycled each year</h3>
            <div class="space-y-4">
              <?php foreach ($APPLIANCE_CREDITS as $slug => $appliance): ?>
                <div class="flex items-center justify-between gap-4 rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                  <div>
                    <p class="text-base font-semibold text-slate-900"><?php echo htmlspecialchars($appliance['label']); ?></p>
                    <p class="text-xs text-slate-500">Credit value: $<?php echo $appliance['credit']; ?></p>
                  </div>
                  <input
                    type="number"
                    min="0"
                    value="0"
                    data-appliance-credit="<?php echo $appliance['credit']; ?>"
                    class="w-20 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-center text-lg font-semibold focus:border-brand focus:ring-brand"
                  />
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </form>

        <div class="mt-8 grid gap-4">
          <div class="rounded-2xl bg-gradient-to-r from-emerald-500 to-green-500 p-6 text-white shadow-xl">
            <p class="text-sm uppercase tracking-wide text-emerald-100 font-semibold">Estimated yearly earnings</p>
            <p id="calc-total-annual" class="text-4xl font-bold mt-2">$520</p>
            <p id="calc-total-breakdown" class="text-sm mt-3 text-emerald-100">
              ≈ $43 per month • ≈ $3 per pickup (weekly)
            </p>
          </div>
          <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-6">
            <p class="text-sm font-semibold text-emerald-600 uppercase tracking-wide mb-2">What&apos;s included</p>
            <ul class="space-y-2 text-sm text-slate-600">
              <li class="flex items-start gap-2">
                <span class="text-emerald-600 mt-0.5">•</span>
                <span id="calc-cans-reward-text">$5 from cans collected</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-emerald-600 mt-0.5">•</span>
                <span id="calc-appliance-reward-text">$0 from appliance credits</span>
              </li>
            </ul>
          </div>
        </div>

        <div class="mt-10 flex flex-wrap gap-4">
          <a
            href="register.php"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand px-6 py-3 text-base font-semibold text-white shadow-lg shadow-emerald-500/30 hover:bg-emerald-700 transition"
          >
            Create an account
            <span aria-hidden="true">→</span>
          </a>
          <a
            href="schedule-pickup.php"
            class="inline-flex items-center justify-center gap-2 rounded-xl border border-brand px-6 py-3 text-base font-semibold text-brand hover:bg-emerald-50 transition"
          >
            Schedule a pickup
            <span aria-hidden="true">→</span>
          </a>
          <a
            href="recycling-wellington.php"
            class="inline-flex items-center justify-center gap-2 rounded-xl border border-transparent px-6 py-3 text-base font-semibold text-emerald-700 bg-emerald-100 hover:bg-emerald-200 transition"
          >
            Explore Wellington recycling tips
            <span aria-hidden="true">→</span>
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="grid gap-8 lg:grid-cols-2 mb-16">
    <!-- Cans Rewards -->
    <div class="rounded-2xl bg-gradient-to-br from-emerald-50 via-green-50 to-white p-8 border-2 border-emerald-200 shadow-xl">
      <div class="flex items-center gap-4 mb-6">
        <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-emerald-400 to-green-500 flex items-center justify-center text-4xl shadow-lg">
          🥤
        </div>
        <h2 class="text-3xl font-bold text-slate-900">$1 per 100 cans</h2>
      </div>
      <div class="space-y-4">
        <div class="flex items-center justify-between p-4 rounded-xl bg-white border-2 border-emerald-100 hover:border-brand transition-all">
          <span class="text-lg font-semibold">100 cans</span>
          <span class="text-2xl font-bold text-brand">→ $1</span>
        </div>
        <div class="flex items-center justify-between p-4 rounded-xl bg-white border-2 border-emerald-100 hover:border-brand transition-all">
          <span class="text-lg font-semibold">500 cans</span>
          <span class="text-2xl font-bold text-brand">→ $5</span>
        </div>
        <div class="flex items-center justify-between p-4 rounded-xl bg-white border-2 border-emerald-100 hover:border-brand transition-all">
          <span class="text-lg font-semibold">1,000 cans</span>
          <span class="text-2xl font-bold text-brand">→ $10</span>
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
    <p class="text-emerald-100 text-lg">
      Deposits available with consent and ID check at payout time. Help your kids save or grow their KiwiSaver while recycling!
    </p>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('rewards-calculator');
    if (!form) return;

    const cansInput = document.getElementById('calc-cans-per-week');
    const applianceInputs = Array.from(form.querySelectorAll('[data-appliance-credit]'));
    const totalAnnualEl = document.getElementById('calc-total-annual');
    const breakdownEl = document.getElementById('calc-total-breakdown');
    const cansRewardTextEl = document.getElementById('calc-cans-reward-text');
    const applianceRewardTextEl = document.getElementById('calc-appliance-reward-text');

    const formatCurrency = (value) => {
      return new Intl.NumberFormat('en-NZ', { style: 'currency', currency: 'NZD', maximumFractionDigits: 0 }).format(value);
    };

    const calculateRewards = () => {
      const cansPerWeek = Math.max(0, parseInt(cansInput.value, 10) || 0);
      const cansReward = Math.floor((cansPerWeek * 52) / 100);

      let applianceReward = 0;
      applianceInputs.forEach((input) => {
        const qty = Math.max(0, parseInt(input.value, 10) || 0);
        const credit = Number(input.dataset.applianceCredit) || 0;
        applianceReward += qty * credit;
      });

      const total = cansReward + applianceReward;
      const monthly = Math.round(total / 12);
      const perPickup = Math.round((cansPerWeek * 4) / 100);

      totalAnnualEl.textContent = formatCurrency(total);
      breakdownEl.textContent = `≈ ${formatCurrency(monthly)} per month • ≈ ${formatCurrency(perPickup)} per pickup (weekly)`;
      cansRewardTextEl.textContent = `${formatCurrency(cansReward)} from cans collected`;
      applianceRewardTextEl.textContent = `${formatCurrency(applianceReward)} from appliance credits`;
    };

    cansInput.addEventListener('input', calculateRewards);
    applianceInputs.forEach((input) => input.addEventListener('input', calculateRewards));

    calculateRewards();
  });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

