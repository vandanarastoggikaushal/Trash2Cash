<?php
$pageTitle = 'Frequently Asked Questions';
$pageDescription = 'FAQ about Trash2Cash NZ recycling service: what we collect, payment options, KiwiSaver deposits, service areas in Wellington, hygiene requirements, and more.';
require_once __DIR__ . '/includes/config.php';

$faqs = [
  ['q' => 'What items can Trash2Cash recycle in Wellington?', 'a' => 'We collect rinsed aluminium cans, metal drink bottles, whiteware (washing machines, dryers, fridges), benchtop appliances, PC cases, laptops and other recyclable metals.'],
  ['q' => 'How does the Wellington recycling pickup work?', 'a' => 'Book a slot online, leave cans or appliances at your doorway, and our team will weigh, collect and update your Trash2Cash balance the same day.'],
  ['q' => 'Which Wellington suburbs do you service?', 'a' => 'We cover the entire region including Wellington CBD, Lower Hutt, Upper Hutt, Porirua, Tawa, Johnsonville, Karori, Churton Park, Newlands and Kapiti Coast suburbs.'],
  ['q' => 'Can my payment go to a child or KiwiSaver?', 'a' => 'Yes. After your first pickup, add payout details in your account to send funds to a child\'s bank account or KiwiSaver provider.'],
  ['q' => 'Do cans need to be crushed or labelled?', 'a' => 'No label required—just give them a quick rinse. Crushing is optional and can save space if you have a large volume.'],
  ['q' => 'How quickly can you collect?', 'a' => 'Most Wellington recycling pickups happen within 2–4 business days. Larger community or business collections may be scheduled separately.'],
  ['q' => 'What condition should appliances be in?', 'a' => 'Appliances should be disconnected, safe to move and free from food residue. We take care of lifting and recycling.'],
  ['q' => 'Do you offer recycling for schools, clubs or events?', 'a' => 'Absolutely. We help PTAs, sports clubs and festivals run profitable can drives—contact us for bulk pickup scheduling.'],
  ['q' => 'What happens if I need to change my booking?', 'a' => 'Let us know as soon as plans change. We can reschedule your trash collection at no charge.'],
  ['q' => 'How do I track payouts and balances?', 'a' => 'Log in to your Trash2Cash account to see pending and completed payments, update bank details and download reference receipts.'],
  ['q' => 'Is there a fee for Wellington recycling pickups?', 'a' => 'No pickup fee. We simply deduct recycling costs from the value of your cans or appliances. You always see the final payout before it is sent.'],
  ['q' => 'Do you provide documentation for business recycling?', 'a' => 'Yes. We supply digital reference IDs and summary statements suitable for sustainability or ESG reporting.']
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

