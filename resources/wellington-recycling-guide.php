<?php
$pageTitle = 'Wellington Recycling Guide: Prepare Cans & Appliances';
$pageDescription = 'Learn how to sort, clean and prepare aluminium cans and household appliances for recycling in Wellington with Trash2Cash NZ.';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/header.php';
?>

<article class="container py-16 prose prose-emerald max-w-3xl">
  <header class="not-prose mb-10 space-y-4 text-center">
    <span class="inline-flex items-center gap-2 rounded-full bg-emerald-100 px-4 py-1 text-sm font-semibold text-brand">
      <span>📘</span>
      <span>Recycling resource</span>
    </span>
    <h1 class="text-5xl font-extrabold text-slate-900">Wellington recycling guide: prepare cans &amp; appliances</h1>
    <p class="text-lg text-slate-600 max-w-2xl mx-auto">
      This quick-start guide shows Wellington households, schools and small businesses how to get the most out of Trash2Cash NZ recycling pickups.
      Follow the steps below to earn cash faster and keep valuable materials in the circular economy.
    </p>
  </header>

  <nav class="not-prose mb-10 rounded-2xl border-2 border-emerald-100 bg-white p-6 shadow-lg">
    <h2 class="text-2xl font-bold text-slate-900 mb-3">Table of contents</h2>
    <ol class="space-y-2 text-slate-700">
      <li><a href="#step-aluminium" class="text-brand font-semibold hover:underline">1. Sort &amp; rinse aluminium cans</a></li>
      <li><a href="#step-appliances" class="text-brand font-semibold hover:underline">2. Prepare household appliances for pickup</a></li>
      <li><a href="#step-schedule" class="text-brand font-semibold hover:underline">3. Schedule a recycling collection</a></li>
      <li><a href="#step-payout" class="text-brand font-semibold hover:underline">4. Choose your payout &amp; track balances</a></li>
      <li><a href="#step-community" class="text-brand font-semibold hover:underline">Community recycling ideas</a></li>
    </ol>
  </nav>

  <section id="step-aluminium">
    <h2>1. Sort &amp; rinse aluminium cans</h2>
    <p>
      Aluminium is infinitely recyclable and attracts the best recycling rebates. Rinse drink cans with a quick splash of water and allow them to drain.
      You can lightly crush cans to save space, but keep the barcode visible where possible.
    </p>
    <ul>
      <li>Collect cans in strong rubbish bags, cardboard boxes, reusable supermarket bags, or council recycling crates.</li>
      <li>Keep glass separate — we only accept metal items during Trash2Cash pickups.</li>
      <li>Add a sticky note with your name if you are coordinating a school or club recycling drive.</li>
    </ul>
  </section>

  <section id="step-appliances">
    <h2>2. Prepare household appliances for pickup</h2>
    <p>
      We recycle whiteware, benchtop appliances, laptops, PC cases and other metal-rich items. Before collection:
    </p>
    <ul>
      <li>Unplug appliances and ensure hoses or water lines are safely disconnected.</li>
      <li>Remove food residue from fridges, microwaves or dishwashers.</li>
      <li>Make sure the access path is clear; our team does the heavy lifting.</li>
      <li>Bundle smaller e-waste items (keyboards, cords, routers) into a labelled box.</li>
    </ul>
  </section>

  <section id="step-schedule">
    <h2>3. Schedule a recycling collection</h2>
    <p>
      Use the <a href="/schedule-pickup.php">online scheduling form</a> to pick a date for your Wellington recycling pickup.
      We service the entire region — including <?php echo implode(', ', array_map('htmlspecialchars', $SERVICE_AREAS)); ?> — and can usually confirm a slot within a few days.
    </p>
    <p>
      When filling out the form, tell us roughly how many bags of cans or which appliances you have.
      The more detail you provide, the quicker we can route our truck and co-ordinate your trash collection.
    </p>
  </section>

  <section id="step-payout">
    <h2>4. Choose your payout &amp; track balances</h2>
    <p>
      Every pickup is recorded in your Trash2Cash account, and we pay <strong>$1 per 100 aluminium cans</strong>.
      After your first collection you can add <a href="/account.php">payout details in the account page</a>.
    </p>
    <ul>
      <li>Deposit into a bank account or nominate a child’s savings account.</li>
      <li>Send funds to KiwiSaver by entering your provider and member number.</li>
      <li>Track pending and completed payments under “Payout history”.</li>
    </ul>
  </section>

  <section id="step-community">
    <h2>Community recycling ideas for Wellington</h2>
    <p>
      Trash2Cash recycling pickups help community groups raise money fast. Try these ideas:
    </p>
    <ul>
      <li>Run a month-long “Can Drive” at school and book a pickup once your cages are full.</li>
      <li>Partner with local cafés or offices to collect their aluminium drink cans.</li>
      <li>Advertise on Neighbourly or community Facebook groups with a link to <a href="/recycling-wellington.php">our Wellington service page</a>.</li>
    </ul>
  </section>

  <footer class="not-prose mt-12 rounded-3xl border-2 border-emerald-100 bg-emerald-600 px-6 py-8 text-white shadow-2xl">
    <h2 class="text-3xl font-bold mb-3">Book your next recycling pickup</h2>
    <p class="text-emerald-100 mb-5">
      Ready to turn household recycling into savings? Schedule a pickup and we will handle everything from collection to payment.
    </p>
    <a href="/schedule-pickup.php" class="btn bg-white text-emerald-700 hover:bg-emerald-50 text-base px-6 py-3">Schedule a Wellington pickup</a>
  </footer>
</article>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

