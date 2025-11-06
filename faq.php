<?php
$pageTitle = 'Frequently Asked Questions';
$pageDescription = 'FAQ about Trash2Cash NZ recycling service: what we collect, payment options, KiwiSaver deposits, service areas in Wellington, hygiene requirements, and more.';
require_once __DIR__ . '/includes/config.php';

$faqs = [
  ['q' => 'What do you collect?', 'a' => 'Clean aluminium cans and common household metal appliances.'],
  ['q' => 'How do payments work?', 'a' => 'We tally your items, then pay out or transfer as chosen.'],
  ['q' => 'Kids & KiwiSaver?', 'a' => 'Name a child beneficiary or provide KiwiSaver provider/member ID; we transfer after verification.'],
  ['q' => 'Do I need to crush cans?', 'a' => 'Optional; please give a quick rinse.'],
  ['q' => 'Which suburbs?', 'a' => 'Current service areas across Wellington region; more coming soon.'],
  ['q' => 'Appliance condition?', 'a' => 'Must be safe to move; we handle recycling.'],
  ['q' => 'Turnaround time?', 'a' => 'Usually a few days depending on suburb and volume.'],
  ['q' => 'Cancelled pickups?', 'a' => 'Let us know ASAP - no worries, we will reschedule.'],
  ['q' => 'Hygiene?', 'a' => 'Please rinse cans to keep collections clean and safe.'],
  ['q' => 'Heavy items?', 'a' => 'We handle the heavy lifting—just ensure clear access.'],
  ['q' => 'Data privacy?', 'a' => 'We store minimal details securely and never sell your data.'],
  ['q' => 'Receipts?', 'a' => 'You will receive a reference ID after scheduling and confirmation after pickup.']
];

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-16">
  <div class="text-center mb-12">
    <h1 class="text-5xl font-extrabold text-slate-900 mb-4">
      <span class="gradient-text">❓ FAQ</span>
    </h1>
    <p class="text-xl text-slate-600">Everything you need to know</p>
  </div>
  
  <div class="grid gap-4 max-w-4xl mx-auto">
    <?php foreach ($faqs as $idx => $faq): ?>
      <div class="card-modern rounded-xl border-2 border-emerald-100 bg-gradient-to-br from-white to-emerald-50/30 p-6 shadow-md hover:shadow-lg transition-all hover:border-brand">
        <div class="flex items-start gap-4">
          <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-br from-emerald-400 to-green-500 flex items-center justify-center text-white font-bold shadow-md">
            <?php echo $idx + 1; ?>
          </div>
          <div class="flex-1">
            <h3 class="text-lg font-bold text-slate-900 mb-2 flex items-center gap-2">
              <span>💬</span>
              <?php echo htmlspecialchars($faq['q']); ?>
            </h3>
            <p class="text-slate-700 leading-relaxed"><?php echo htmlspecialchars($faq['a']); ?></p>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  
  <!-- FAQ Structured Data -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
      <?php
      $faqItems = [];
      foreach ($faqs as $faq) {
        $faqItems[] = '{
          "@type": "Question",
          "name": ' . json_encode($faq['q'], JSON_UNESCAPED_UNICODE) . ',
          "acceptedAnswer": {
            "@type": "Answer",
            "text": ' . json_encode($faq['a'], JSON_UNESCAPED_UNICODE) . '
          }
        }';
      }
      echo implode(",\n      ", $faqItems);
      ?>
    ]
  }
  </script>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

