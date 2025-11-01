<?php
$pageTitle = 'How It Works';
$pageDescription = 'Register, prepare recyclables, schedule pickup, and get paid or deposit to KiwiSaver.';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-10">
  <h1 class="text-3xl font-bold">How it works</h1>
  <ol class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <?php
    $steps = ['Register', 'Prepare recyclables', 'Schedule pickup', 'Get paid / KiwiSaver'];
    foreach ($steps as $i => $step):
    ?>
      <li class="rounded-lg border bg-white p-4">
        <div class="text-sm text-slate-500">Step <?php echo $i + 1; ?></div>
        <div class="font-semibold"><?php echo htmlspecialchars($step); ?></div>
      </li>
    <?php endforeach; ?>
  </ol>
  <div class="prose mt-8">
    <p>Rinse cans quickly (crushing optional). Keep appliances safe to move. Typical turnaround is a few days depending on suburb.</p>
    <p>Current service areas: <?php echo implode(', ', $SERVICE_AREAS); ?></p>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

