<?php
$pageTitle = 'Wellington Recycling & Trash Collection | Trash2Cash NZ';
$pageDescription = "Trash2Cash NZ offers Wellington recycling pickup, trash collection for aluminium cans and appliances, and convenient door-to-door service across local suburbs. Earn \$1 per 100 cans and support kids’ savings or KiwiSaver.";
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';
?>

<div>
  <section class="relative overflow-hidden bg-gradient-to-br from-emerald-50 via-green-50 to-white py-20">
    <div class="container relative grid gap-8 py-16 lg:grid-cols-2 lg:items-center">
      <div class="animate-fade-in">
        <div class="inline-flex items-center gap-2 rounded-full bg-emerald-100 px-4 py-1.5 text-sm font-semibold text-brand mb-4">
          <span class="animate-pulse-slow">♻️</span>
          <span>Local recycling pickup across Wellington</span>
        </div>
        <h1 class="text-5xl font-extrabold tracking-tight text-slate-900 sm:text-6xl">
          <span class="gradient-text">Wellington Recycling</span><br />
          <span class="text-slate-800">Pickup & Trash Collection</span>
        </h1>
        <p class="mt-6 text-xl text-slate-700 leading-relaxed">
          Trash2Cash NZ provides door-to-door recycling in Wellington. We collect clean aluminium cans and old appliances, recycle them responsibly, and pay you <strong class="text-brand font-bold">$1 per 100 cans</strong>. Choose cash, kids' savings, or KiwiSaver and keep valuable materials out of landfill.
        </p>
        <div class="mt-8 flex flex-wrap gap-4">
          <a class="btn text-lg px-8 py-3 shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all" href="/schedule-pickup.php">
            ✨ Schedule a Pickup
          </a>
          <a class="btn-secondary text-lg px-8 py-3 hover:border-brand hover:text-brand transition-all" href="/how-it-works.php">📖 How It Works</a>
        </div>
        <div class="mt-8 p-4 rounded-lg bg-gradient-to-r from-emerald-100 to-green-100 border-l-4 border-brand">
          <p class="text-sm font-semibold text-slate-800">
            💡 <span class="text-brand">$500/year</span> — That's the average recyclable value most households throw away. Let's change that.
          </p>
        </div>
      </div>
      <div class="relative animate-float">
        <div class="relative h-80 rounded-2xl bg-gradient-to-br from-emerald-200 via-green-100 to-emerald-50 shadow-2xl overflow-hidden">
          <div class="absolute inset-0 flex items-center justify-center">
            <div class="text-center">
              <div class="text-8xl mb-4">♻️</div>
              <div class="text-6xl font-bold text-white drop-shadow-lg">$</div>
            </div>
          </div>
          <div class="absolute top-4 right-4 bg-white rounded-full px-3 py-1 text-sm font-bold text-brand shadow-lg">
            +$500/year
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="container my-16">
    <div class="grid gap-8 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
      <div>
        <h2 class="text-4xl font-bold text-slate-900 mb-4">Recycling guides &amp; resources for Wellington households</h2>
        <p class="text-lg text-slate-700 leading-relaxed mb-4">
          Want to recycle more, earn more, and keep Wellington beautiful? Our recycling hub covers aluminium can preparation,
          appliance disposal tips, and the latest advice on council recycling rules. Check back regularly or subscribe to our updates for new guides.
        </p>
        <ul class="space-y-3 text-slate-700 text-base">
          <li class="flex items-start gap-3"><span>📄</span> <a href="/resources/wellington-recycling-guide.php" class="text-brand font-semibold hover:underline">Wellington Recycling Guide: How to prepare cans &amp; appliances</a></li>
          <li class="flex items-start gap-3"><span>🧠</span> <span>Expert tips on boosting household recycling rebates and rewards.</span></li>
          <li class="flex items-start gap-3"><span>🌱</span> <span>Community initiatives and school fundraisers using Trash2Cash pickups.</span></li>
        </ul>
        <div class="mt-6">
          <a href="/resources.php" class="btn-secondary text-base px-6 py-3">Browse all recycling resources</a>
        </div>
      </div>
      <div class="rounded-3xl border-2 border-emerald-200 bg-white p-6 shadow-lg space-y-4">
        <h3 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
          <span>📝</span> Latest resource highlights
        </h3>
        <div class="space-y-3 text-sm text-slate-600">
          <p><strong class="text-slate-900">Seasonal recycling checklist:</strong> make the most of summer events and community clean-ups.</p>
          <p><strong class="text-slate-900">Commercial workspace recycling:</strong> contact us for repeat office pickups across Wellington CBD.</p>
          <p><strong class="text-slate-900">Schools &amp; charities:</strong> raise funds by recycling cans with our free promotion kit.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="container py-16" id="wellington-recycling-areas">
    <div class="text-center mb-10">
      <h2 class="text-4xl font-bold text-slate-900 mb-3">Wellington suburbs we recycle in</h2>
      <p class="text-lg text-slate-600 max-w-3xl mx-auto">
        From the CBD to the Kapiti Coast, Trash2Cash NZ offers recycling pickup and trash collection across the region.
        Book a visit, leave your rinsed cans or metal appliances by the door, and we will handle the recycling.
      </p>
    </div>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
      <?php foreach ($SERVICE_AREAS as $area): ?>
        <a href="/recycling-wellington.php#<?php echo urlencode(strtolower(str_replace(' ', '-', $area))); ?>"
          class="group rounded-2xl border-2 border-emerald-100 bg-white p-6 shadow-md transition-all hover:border-brand hover:shadow-xl">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm uppercase tracking-wide text-emerald-600 font-semibold">Service area</p>
              <h3 class="text-xl font-bold text-slate-900 mt-1"><?php echo htmlspecialchars($area); ?></h3>
            </div>
            <span class="text-2xl group-hover:translate-x-1 transition-transform">➡️</span>
          </div>
          <p class="mt-4 text-sm text-slate-600">
            Recycling pickup, trash collection and appliance removal in <?php echo htmlspecialchars($area); ?>.
          </p>
        </a>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="container py-14">
    <div class="grid gap-10 lg:grid-cols-2 lg:items-center">
      <div class="space-y-6">
        <h2 class="text-4xl font-extrabold text-slate-900 leading-tight">
          Wellington recycling pickup that keeps tins, cans and appliances out of landfill
        </h2>
        <p class="text-lg text-slate-700 leading-relaxed">
          Searching for <strong>“recycling Wellington”</strong>, <strong>“trash collection”</strong> or even
          <strong>“recycle collection near me”</strong>? We provide a friendly, local alternative to the skip.
          Our trained team schedules a pickup, collects cans, whiteware and e-waste, and makes sure everything is diverted to the correct Wellington recycling streams.
        </p>
        <ul class="space-y-3 text-slate-700">
          <li class="flex items-start gap-3"><span>✅</span> <span>Certified recycling partners for aluminium, appliances and scrap metals.</span></li>
          <li class="flex items-start gap-3"><span>✅</span> <span>Fast turnaround across Wellington City, Hutt Valley, Porirua and Kapiti suburbs.</span></li>
          <li class="flex items-start gap-3"><span>✅</span> <span>Transparent pricing with digital receipts and account tracking.</span></li>
        </ul>
        <div class="flex flex-wrap gap-4">
          <a href="/recycling-wellington.php" class="btn-secondary text-base px-6 py-3">Learn about our Wellington service</a>
          <a href="/faq.php" class="btn-secondary text-base px-6 py-3">Read recycling FAQs</a>
        </div>
      </div>
      <div class="rounded-3xl border-2 border-emerald-100 bg-white shadow-xl p-8 space-y-6">
        <h3 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
          <span>📍</span> Popular recycling searches we answer
        </h3>
        <div class="grid gap-3 sm:grid-cols-2">
          <?php
          $keywordPills = [
            'Recycling Wellington pickup',
            'Trash collection Wellington NZ',
            'Recycle collection for aluminium cans',
            'Appliance recycling Wellington',
            'Doorstep recycling service NZ',
            'Wellington metal recycling collection',
          ];
          foreach ($keywordPills as $pill): ?>
            <span class="inline-flex items-center justify-center rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700">
              <?php echo htmlspecialchars($pill); ?>
            </span>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>

  <section class="container grid gap-6 py-16 sm:grid-cols-2 lg:grid-cols-3">
    <div class="card-modern rounded-2xl border-2 border-emerald-100 bg-gradient-to-br from-white to-emerald-50/50 p-8 shadow-lg hover-lift">
      <div class="icon-wrapper mb-4">
        <svg class="text-brand" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" />
        </svg>
      </div>
      <h3 class="text-xl font-bold text-slate-900 mb-2">🚚 Door-to-door pickup</h3>
      <p class="text-slate-700">Across Wellington & suburbs - We come to you!</p>
    </div>
    <div class="card-modern rounded-2xl border-2 border-emerald-100 bg-gradient-to-br from-white to-emerald-50/50 p-8 shadow-lg hover-lift">
      <div class="icon-wrapper mb-4">
        <svg class="text-brand" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <circle cx="12" cy="12" r="10" />
          <path d="M12 6v6l4 2" />
        </svg>
      </div>
      <h3 class="text-xl font-bold text-slate-900 mb-2">💰 $1 per 100 cans</h3>
      <p class="text-slate-700">Simple and transparent pricing - no hidden fees!</p>
    </div>
    <div class="card-modern rounded-2xl border-2 border-emerald-100 bg-gradient-to-br from-white to-emerald-50/50 p-8 shadow-lg hover-lift">
      <div class="icon-wrapper mb-4">
        <svg class="text-brand" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.29 1.51 4.04 3 5.5l7 7Z" />
        </svg>
      </div>
      <h3 class="text-xl font-bold text-slate-900 mb-2">💝 Kids & KiwiSaver</h3>
      <p class="text-slate-700">Grow value over time with smart savings options!</p>
    </div>
  </section>

  <section class="container py-12">
    <div class="text-center mb-10">
      <h2 class="text-4xl font-bold text-slate-900 mb-3">What We Collect</h2>
      <p class="text-lg text-slate-600">Turn your old items into cash!</p>
    </div>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
      <?php 
      $items = [
        ['name' => 'Aluminium cans', 'icon' => '🥤', 'color' => 'from-blue-50 to-blue-100'],
        ['name' => 'Washing machines', 'icon' => '🔧', 'color' => 'from-slate-50 to-slate-100'],
        ['name' => 'Microwaves', 'icon' => '📻', 'color' => 'from-purple-50 to-purple-100'],
        ['name' => 'PC cases', 'icon' => '💻', 'color' => 'from-indigo-50 to-indigo-100'],
        ['name' => 'Laptops', 'icon' => '💾', 'color' => 'from-cyan-50 to-cyan-100'],
        ['name' => 'Dishwashers', 'icon' => '🧽', 'color' => 'from-teal-50 to-teal-100']
      ];
      foreach ($items as $item): ?>
        <div class="group rounded-xl bg-gradient-to-br <?php echo $item['color']; ?> border-2 border-transparent hover:border-brand p-6 transition-all hover:shadow-lg transform hover:-translate-y-1">
          <div class="text-4xl mb-3"><?php echo $item['icon']; ?></div>
          <div class="font-semibold text-slate-900"><?php echo htmlspecialchars($item['name']); ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="container my-16" id="rewards-calculator">
    <div class="rounded-2xl border-2 border-emerald-200 bg-gradient-to-br from-white to-emerald-50/30 p-8 shadow-xl">
      <div class="flex items-center gap-3 mb-6">
        <span class="text-4xl">🧮</span>
        <h2 class="text-3xl font-bold text-slate-900">Rewards Calculator</h2>
      </div>
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

  <section class="container my-16">
    <div class="relative overflow-hidden flex flex-col items-center justify-between gap-6 rounded-2xl bg-gradient-to-r from-emerald-600 via-green-600 to-emerald-700 px-8 py-12 text-white shadow-2xl sm:flex-row">
      <div class="relative z-10">
        <div class="text-5xl mb-4">🚀</div>
        <h3 class="text-3xl font-bold mb-2">Ready to turn trash into cash?</h3>
        <p class="text-emerald-100 text-lg">Door-to-door pickups across Wellington & suburbs.</p>
      </div>
      <a href="/schedule-pickup.php" class="relative z-10 btn bg-white text-emerald-700 hover:bg-emerald-50 text-lg px-8 py-4 shadow-2xl transform hover:scale-105 transition-all font-bold">
        ✨ Schedule a Pickup Now
      </a>
    </div>
  </section>
</div>

<?php
// Get version dynamically
$version = getVersion();
?>
<div class="fixed bottom-4 right-4 z-50">
  <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-white/80 backdrop-blur-sm text-xs text-slate-600 shadow-md border border-slate-200">
    <span>📦</span>
    <span>Version <?php echo htmlspecialchars($version); ?></span>
  </span>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
