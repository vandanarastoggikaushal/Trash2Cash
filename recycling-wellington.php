<?php
$pageTitle = 'Wellington Recycling Pickup & Trash Collection Service';
$pageDescription = 'Door-to-door recycling and trash collection across Wellington. Book aluminium can pickup, appliance recycling and metal collection with Trash2Cash NZ.';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-16">
  <div class="max-w-4xl mx-auto space-y-10">
    <header class="space-y-6 text-center">
      <span class="inline-flex items-center gap-2 rounded-full bg-emerald-100 px-4 py-1 text-sm font-semibold text-brand">
        <span>📍</span>
        <span>Serving Wellington &amp; surrounding suburbs</span>
      </span>
      <h1 class="text-5xl font-extrabold text-slate-900 leading-tight">
        Wellington recycling pickup for cans, appliances &amp; scrap metal
      </h1>
      <p class="text-lg text-slate-600 leading-relaxed">
        Trash2Cash NZ is the friendly recycling alternative to the skip. We collect aluminium cans, whiteware and e-waste from
        homes, schools and businesses across the Wellington region. Earn cash, pay kids’ savings, or contribute to KiwiSaver while
        reducing landfill.
      </p>
      <div class="flex flex-wrap justify-center gap-4">
        <a href="/schedule-pickup" class="btn text-base px-6 py-3">Schedule a recycling pickup</a>
        <a href="/resources" class="btn-secondary text-base px-6 py-3">Read recycling guides</a>
      </div>
    </header>

    <section class="rounded-3xl border-2 border-emerald-100 bg-white p-8 shadow-xl space-y-6">
      <h2 class="text-3xl font-bold text-slate-900">What we collect during Wellington recycling visits</h2>
      <p class="text-slate-600">
        Every pickup is tailored to your household or organisation. Mix and match aluminium cans, metal kitchen appliances, computer hardware
        and other recyclable metals. We provide reference receipts and update your Trash2Cash balance after each collection.
      </p>
      <div class="grid gap-4 sm:grid-cols-2">
        <div class="rounded-xl border border-emerald-100 bg-emerald-50/80 p-5">
          <h3 class="text-xl font-semibold text-slate-900 mb-2">Aluminium &amp; metal cans</h3>
          <p class="text-sm text-slate-700">Rinsed aluminium drink cans, food tins and fundraising collection bags.</p>
        </div>
        <div class="rounded-xl border border-emerald-100 bg-emerald-50/80 p-5">
          <h3 class="text-xl font-semibold text-slate-900 mb-2">Household appliances</h3>
          <p class="text-sm text-slate-700">Washing machines, dryers, microwaves, dishwashers, PCs and laptops.</p>
        </div>
        <div class="rounded-xl border border-emerald-100 bg-emerald-50/80 p-5">
          <h3 class="text-xl font-semibold text-slate-900 mb-2">Community &amp; school collections</h3>
          <p class="text-sm text-slate-700">Turn PTA drives, sports club fundraisers and workplace recycling into cash quickly.</p>
        </div>
        <div class="rounded-xl border border-emerald-100 bg-emerald-50/80 p-5">
          <h3 class="text-xl font-semibold text-slate-900 mb-2">Business recycling runs</h3>
          <p class="text-sm text-slate-700">Scheduled pickups for cafés, offices, and construction recycling in Wellington CBD.</p>
        </div>
      </div>
    </section>

    <section class="space-y-5">
      <h2 class="text-3xl font-bold text-slate-900">Suburbs covered by our Wellington recycling service</h2>
      <p class="text-slate-600">
        We travel across the Wellington region each week. Find your suburb below and request a free quote if you do not see it listed.
        We also service one-off events and bulk collections — just mention the details in your booking.
      </p>
      <div class="grid gap-4 sm:grid-cols-2">
        <?php foreach ($SERVICE_AREAS as $area): 
          $anchor = strtolower(str_replace([' ', '&'], ['-', 'and'], $area));
        ?>
          <article id="<?php echo htmlspecialchars($anchor); ?>" class="rounded-2xl border-2 border-emerald-100 bg-white p-6 shadow-lg">
            <h3 class="text-xl font-bold text-slate-900"><?php echo htmlspecialchars($area); ?></h3>
            <p class="mt-3 text-sm text-slate-700">
              Weekly recycling pickup for cans, appliances and scrap metal in <?php echo htmlspecialchars($area); ?>. We handle loading and provide a reference receipt for your records.
            </p>
          </article>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="rounded-3xl border-2 border-emerald-100 bg-gradient-to-br from-white to-emerald-50/40 p-8 shadow-xl space-y-6">
      <h2 class="text-3xl font-bold text-slate-900">How Wellington recycling pickups work</h2>
      <ol class="space-y-4 text-slate-700">
        <li><strong>1. Book online:</strong> Use the <a href="/schedule-pickup" class="text-brand font-semibold hover:underline">schedule form</a> to choose a pickup date and share what you are recycling.</li>
        <li><strong>2. Prep your items:</strong> Rinse cans, bundle them, and make sure appliances are disconnected with clear access.</li>
        <li><strong>3. We collect &amp; weigh:</strong> Our team confirms volumes on-site and updates your Trash2Cash balance.</li>
        <li><strong>4. Pick your payout:</strong> Transfer to a bank account, a child’s savings, or your KiwiSaver after the first successful collection.</li>
      </ol>
      <p class="text-slate-600">
        Need help with regular recycling at schools, gyms or offices? Contact us via <a href="/contact" class="text-brand font-semibold hover:underline">the contact form</a> and we’ll tailor a recycling programme.
      </p>
    </section>

    <section class="rounded-3xl border-2 border-emerald-200 bg-emerald-600 p-8 text-white shadow-2xl">
      <h2 class="text-3xl font-bold mb-4">Ready to book your Wellington recycling pickup?</h2>
      <p class="text-emerald-100 mb-6">
        Keep aluminium, appliances and scrap metal out of landfill — and turn them into real value. We can usually collect within a few days anywhere in the Wellington region.
      </p>
      <div class="flex flex-wrap gap-4">
        <a href="/schedule-pickup" class="btn bg-white text-emerald-700 hover:bg-emerald-50 text-base px-6 py-3">Book a pickup</a>
        <a href="/faq" class="btn-secondary text-base px-6 py-3 text-white border-white hover:text-emerald-700 hover:bg-white">View recycling FAQ</a>
      </div>
    </section>
  </div>
</div>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Service",
  "serviceType": "Recycling pickup and trash collection",
  "provider": {
    "@type": "Organization",
    "name": "<?php echo COMPANY_NAME; ?>",
    "areaServed": <?php echo json_encode($SERVICE_AREAS, JSON_UNESCAPED_UNICODE); ?>,
    "url": "<?php echo $SITE['url']; ?>"
  },
  "areaServed": {
    "@type": "City",
    "name": "Wellington"
  },
  "availableChannel": {
    "@type": "ServiceChannel",
    "serviceUrl": "<?php echo $SITE['url']; ?>/schedule-pickup.php"
  }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

