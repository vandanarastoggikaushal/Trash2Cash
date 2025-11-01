<?php
$pageTitle = 'How It Works';
$pageDescription = 'Register, prepare recyclables, schedule pickup, and get paid or deposit to KiwiSaver.';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-16">
  <div class="text-center mb-12">
    <h1 class="text-5xl font-extrabold text-slate-900 mb-4">
      <span class="gradient-text">How It Works</span>
    </h1>
    <p class="text-xl text-slate-600 max-w-2xl mx-auto">Simple steps to turn your recyclables into cash or savings</p>
  </div>
  
  <div class="relative">
    <div class="absolute inset-0 flex items-center justify-center">
      <div class="hidden lg:block w-full border-t-2 border-dashed border-emerald-200"></div>
    </div>
    <ol class="relative grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
      <?php
      $steps = [
        ['title' => 'Register', 'icon' => '📝', 'desc' => 'Quick signup'],
        ['title' => 'Prepare recyclables', 'icon' => '🧹', 'desc' => 'Rinse & organize'],
        ['title' => 'Schedule pickup', 'icon' => '📅', 'desc' => 'Book online'],
        ['title' => 'Get paid / KiwiSaver', 'icon' => '💰', 'desc' => 'Earn money']
      ];
      foreach ($steps as $i => $step):
      ?>
        <li class="relative card-modern rounded-2xl border-2 border-emerald-100 bg-gradient-to-br from-white to-emerald-50/50 p-8 shadow-lg hover-lift">
          <div class="flex flex-col items-center text-center">
            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-emerald-400 to-green-500 flex items-center justify-center text-4xl mb-4 shadow-lg">
              <?php echo $step['icon']; ?>
            </div>
            <div class="absolute top-4 right-4 w-8 h-8 rounded-full bg-brand text-white flex items-center justify-center text-sm font-bold shadow-md">
              <?php echo $i + 1; ?>
            </div>
            <div class="text-sm font-semibold text-brand mb-2">Step <?php echo $i + 1; ?></div>
            <h3 class="text-xl font-bold text-slate-900 mb-2"><?php echo htmlspecialchars($step['title']); ?></h3>
            <p class="text-sm text-slate-600"><?php echo htmlspecialchars($step['desc']); ?></p>
          </div>
        </li>
      <?php endforeach; ?>
    </ol>
  </div>
  
  <div class="mt-16 grid gap-8 lg:grid-cols-2">
    <div class="rounded-2xl bg-gradient-to-br from-emerald-50 to-green-50 p-8 border-2 border-emerald-100">
      <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-2">
        <span class="text-3xl">💡</span> Preparation Tips
      </h2>
      <ul class="space-y-3 text-slate-700">
        <li class="flex items-start gap-3">
          <span class="text-brand text-xl">✓</span>
          <span>Rinse cans quickly (crushing optional)</span>
        </li>
        <li class="flex items-start gap-3">
          <span class="text-brand text-xl">✓</span>
          <span>Keep appliances safe to move</span>
        </li>
        <li class="flex items-start gap-3">
          <span class="text-brand text-xl">✓</span>
          <span>Typical turnaround is a few days depending on suburb</span>
        </li>
      </ul>
    </div>
    
    <div class="rounded-2xl bg-gradient-to-br from-blue-50 to-cyan-50 p-8 border-2 border-blue-100">
      <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-2">
        <span class="text-3xl">📍</span> Service Areas
      </h2>
      <p class="text-slate-700 mb-4">We currently serve:</p>
      <div class="flex flex-wrap gap-2">
        <?php foreach ($SERVICE_AREAS as $area): ?>
          <span class="badge bg-gradient-to-r from-blue-500 to-cyan-500"><?php echo htmlspecialchars($area); ?></span>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

