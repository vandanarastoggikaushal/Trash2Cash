<?php
$pageTitle = 'Recycling Resources & Guides | Trash2Cash NZ';
$pageDescription = 'Discover recycling guides, trash collection tips, and sustainability resources for Wellington households and businesses.';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';

$resources = [
  [
    'title' => 'Wellington Recycling Guide: How to prepare cans & appliances',
    'description' => 'Step-by-step advice for rinsing aluminium cans, preparing appliances, and booking Wellington recycling pickups.',
    'url' => '/resources/wellington-recycling-guide',
    'icon' => '📘',
    'category' => 'Household recycling'
  ],
];
?>

<div class="container py-16">
  <header class="text-center max-w-3xl mx-auto mb-12 space-y-4">
    <span class="inline-flex items-center gap-2 rounded-full bg-emerald-100 px-4 py-1 text-sm font-semibold text-brand">
      <span>📚</span>
      <span>Learn, recycle, and earn more</span>
    </span>
    <h1 class="text-5xl font-extrabold text-slate-900">Recycling resources for Wellington households &amp; businesses</h1>
    <p class="text-lg text-slate-600">
      Browse the latest Trash2Cash guides covering recycling best practice, trash collection hacks, and tips on growing your KiwiSaver
      through aluminium and appliance recycling in Wellington.
    </p>
  </header>

  <section class="grid gap-6 md:grid-cols-2">
    <?php foreach ($resources as $resource): ?>
      <article class="rounded-3xl border-2 border-emerald-100 bg-white p-8 shadow-lg hover:shadow-2xl transition-transform hover:-translate-y-1">
        <div class="flex items-start gap-4">
          <span class="text-4xl"><?php echo $resource['icon']; ?></span>
          <div class="space-y-3">
            <p class="text-xs uppercase tracking-wide text-emerald-600 font-semibold"><?php echo htmlspecialchars($resource['category']); ?></p>
            <h2 class="text-2xl font-bold text-slate-900">
              <a href="<?php echo htmlspecialchars($resource['url']); ?>" class="hover:underline">
                <?php echo htmlspecialchars($resource['title']); ?>
              </a>
            </h2>
            <p class="text-sm text-slate-700"><?php echo htmlspecialchars($resource['description']); ?></p>
            <a href="<?php echo htmlspecialchars($resource['url']); ?>" class="inline-flex items-center gap-2 font-semibold text-brand hover:underline">
              Read guide
              <span>➡️</span>
            </a>
          </div>
        </div>
      </article>
    <?php endforeach; ?>
  </section>

  <section class="mt-16 rounded-3xl border-2 border-emerald-200 bg-gradient-to-br from-white to-emerald-50/40 p-8 shadow-xl">
    <h2 class="text-3xl font-bold text-slate-900 mb-4">Want a specific recycling topic covered?</h2>
    <p class="text-slate-600 mb-6">
      We regularly update our recycling knowledge hub with new local insights. Suggest a topic about Wellington trash collection,
      school fundraising, or eco-friendly living and we will add it to the queue.
    </p>
    <a href="/contact" class="btn text-base px-6 py-3">Suggest a guide</a>
  </section>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

