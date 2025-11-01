<?php
$pageTitle = 'Rewards';
$pageDescription = 'Earn $1 per 50 aluminium cans plus appliance pickup credits.';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-10">
  <h1 class="text-3xl font-bold">Rewards</h1>
  <div class="prose mt-6">
    <h2>$1 per 50 cans</h2>
    <ul>
      <li>50 cans → $1</li>
      <li>250 cans → $5</li>
      <li>1,000 cans → $20</li>
    </ul>
    <p>Average NZ household ≈ $500/year in recyclable value. Kids' accounts & KiwiSaver deposits available with consent and ID check at payout time.</p>
  </div>
  <div class="mt-8">
    <h2 class="text-xl font-semibold">Appliance pickup credits</h2>
    <div class="mt-3 overflow-hidden rounded-lg border">
      <table class="w-full text-left text-sm">
        <thead class="bg-slate-50">
          <tr>
            <th class="p-3">Appliance</th>
            <th class="p-3">Credit ($)</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($APPLIANCE_CREDITS as $appliance): ?>
            <tr class="border-t">
              <td class="p-3"><?php echo htmlspecialchars($appliance['label']); ?></td>
              <td class="p-3"><?php echo $appliance['credit']; ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

