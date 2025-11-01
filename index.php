<?php
$pageTitle = 'Turn Your Trash Into Cash (or KiwiSaver)';
$pageDescription = "We collect clean aluminium cans and old appliances from your home across Wellington. Earn \$1 per 50 cans—deposit to kids' accounts or KiwiSaver.";
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';
?>

<div>
  <section class="relative overflow-hidden bg-gradient-to-b from-emerald-50 to-white">
    <div class="container grid gap-8 py-16 lg:grid-cols-2 lg:items-center">
      <div>
        <h1 class="text-4xl font-bold tracking-tight text-slate-900 sm:text-5xl">
          Turn Your Trash Into Cash (or KiwiSaver)
        </h1>
        <p class="mt-4 text-lg text-slate-700">
          We collect your clean aluminium cans and old appliances from home. You earn $1 for every 50 cans—and can send it straight to your kids' accounts or KiwiSaver.
        </p>
        <div class="mt-6 flex gap-3">
          <a class="btn" href="/schedule-pickup.php">Schedule a Pickup</a>
          <a class="btn-secondary" href="/how-it-works.php">How It Works</a>
        </div>
        <p class="mt-6 text-sm text-slate-600">Most households throw away around $500/year in recyclable value—and get nothing for it. Let's change that.</p>
      </div>
      <div class="h-64 rounded-xl bg-emerald-100/60 lg:h-80"></div>
    </div>
  </section>

  <section class="container grid gap-6 py-12 sm:grid-cols-2 lg:grid-cols-3">
    <div class="rounded-xl border bg-white p-6 shadow-sm">
      <svg class="text-brand" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" />
      </svg>
      <h3 class="mt-3 text-lg font-semibold">Door-to-door pickup</h3>
      <p class="mt-1 text-sm text-slate-600">Across Wellington & suburbs</p>
    </div>
    <div class="rounded-xl border bg-white p-6 shadow-sm">
      <svg class="text-brand" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10" />
        <path d="M12 6v6l4 2" />
      </svg>
      <h3 class="mt-3 text-lg font-semibold">$1 per 50 cans</h3>
      <p class="mt-1 text-sm text-slate-600">Simple and transparent</p>
    </div>
    <div class="rounded-xl border bg-white p-6 shadow-sm">
      <svg class="text-brand" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.29 1.51 4.04 3 5.5l7 7Z" />
      </svg>
      <h3 class="mt-3 text-lg font-semibold">Kids & KiwiSaver</h3>
      <p class="mt-1 text-sm text-slate-600">Grow value over time</p>
    </div>
  </section>

  <section class="container">
    <h2 class="text-xl font-semibold">What we collect</h2>
    <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
      <?php foreach (['Aluminium cans', 'Washing machines', 'Microwaves', 'PC cases', 'Laptops', 'Dishwashers'] as $item): ?>
        <div class="rounded-lg border bg-white p-4 text-sm"><?php echo htmlspecialchars($item); ?></div>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="container my-12" id="rewards-calculator">
    <div class="rounded-xl border bg-white p-6 shadow-sm">
      <h2 class="text-xl font-semibold">Rewards Calculator</h2>
      <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="space-y-6">
          <div>
            <label for="cans" class="block text-sm font-medium">Cans per week: <span id="cans-display">10</span></label>
            <input id="cans" type="range" min="0" max="100" value="10" class="mt-2 w-full" />
          </div>
          <div>
            <label class="block text-sm font-medium">Appliances per year</label>
            <div class="mt-2 grid gap-3 sm:grid-cols-2" id="appliances-inputs"></div>
          </div>
        </div>
        <div class="rounded-lg bg-slate-50 p-6">
          <div class="space-y-2 text-sm">
            <div class="flex justify-between"><span>Cans reward</span><span class="font-semibold" id="cans-reward">$0</span></div>
            <div class="flex justify-between"><span>Appliance credits</span><span class="font-semibold" id="appliance-reward">$0</span></div>
          </div>
          <div class="mt-4 flex items-center justify-between border-t pt-4 text-lg font-semibold">
            <span>Estimated yearly earnings</span>
            <span id="total-earnings">$0</span>
          </div>
          <div class="mt-6 flex items-center justify-between text-sm">
            <label class="flex items-center gap-2">
              <input type="checkbox" id="show-kiwisaver" />
              KiwiSaver growth preview (5%, 10yrs)
            </label>
            <span class="font-semibold hidden" id="kiwisaver-preview">$0</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="container my-12">
    <div class="flex flex-col items-center justify-between gap-4 rounded-xl bg-emerald-600 px-6 py-10 text-white sm:flex-row">
      <div>
        <h3 class="text-xl font-semibold">Ready to turn trash into cash?</h3>
        <p class="text-emerald-100">Door-to-door pickups across Wellington & suburbs.</p>
      </div>
      <a href="/schedule-pickup.php" class="btn bg-white text-emerald-700 hover:bg-slate-100">Schedule a Pickup</a>
    </div>
  </section>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
