<?php
$pageTitle = 'Partners & Fundraising';
$pageDescription = 'Partner with Trash2Cash NZ for fundraising. Schools, clubs, and businesses can earn money by aggregating aluminium can and appliance pickups with a group code.';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-16">
  <div class="text-center mb-12">
    <h1 class="text-5xl font-extrabold text-slate-900 mb-4">
      <span class="gradient-text">🤝 Partners</span>
    </h1>
    <p class="text-xl text-slate-600 max-w-2xl mx-auto">Fundraise together, earn together!</p>
  </div>

  <div class="max-w-4xl mx-auto">
    <div class="rounded-2xl bg-gradient-to-br from-blue-50 via-cyan-50 to-white p-10 border-2 border-blue-200 shadow-xl mb-8">
      <div class="flex items-center gap-4 mb-6">
        <div class="w-20 h-20 rounded-xl bg-gradient-to-br from-blue-400 to-cyan-500 flex items-center justify-center text-5xl shadow-lg">
          🎯
        </div>
        <h2 class="text-3xl font-bold text-slate-900">Fundraiser Mode</h2>
      </div>
      <p class="text-lg text-slate-700 leading-relaxed mb-6">
        We provide a <strong class="text-brand font-bold">group code</strong> so households can tag their pickups. We aggregate cans and appliance credits and pay out to your group or directly to participating kids' accounts.
      </p>
      <p class="text-lg text-slate-700 leading-relaxed">
        <strong class="text-brand font-bold">Perfect for:</strong> schools, clubs, and local initiatives!
      </p>
    </div>

    <div class="grid gap-6 sm:grid-cols-3">
      <div class="card-modern rounded-xl border-2 border-emerald-100 bg-gradient-to-br from-white to-emerald-50/50 p-6 shadow-lg hover-lift text-center">
        <div class="text-5xl mb-4">🏫</div>
        <h3 class="text-xl font-bold text-slate-900 mb-2">Schools</h3>
        <p class="text-slate-600">Fundraise for your school while teaching kids about recycling!</p>
      </div>
      <div class="card-modern rounded-xl border-2 border-emerald-100 bg-gradient-to-br from-white to-emerald-50/50 p-6 shadow-lg hover-lift text-center">
        <div class="text-5xl mb-4">🎪</div>
        <h3 class="text-xl font-bold text-slate-900 mb-2">Clubs</h3>
        <p class="text-slate-600">Raise funds for your sports team or community group!</p>
      </div>
      <div class="card-modern rounded-xl border-2 border-emerald-100 bg-gradient-to-br from-white to-emerald-50/50 p-6 shadow-lg hover-lift text-center">
        <div class="text-5xl mb-4">🌱</div>
        <h3 class="text-xl font-bold text-slate-900 mb-2">Initiatives</h3>
        <p class="text-slate-600">Support local environmental and community projects!</p>
      </div>
    </div>

    <div class="mt-12 rounded-2xl bg-gradient-to-r from-emerald-600 to-green-600 p-8 text-white shadow-2xl text-center">
      <h3 class="text-2xl font-bold mb-4 flex items-center justify-center gap-2">
        <span class="text-4xl">💡</span> Interested in Partnering?
      </h3>
      <p class="text-emerald-100 text-lg mb-6">Contact us to set up your group code and start fundraising today!</p>
      <a href="/contact" class="inline-block btn bg-white text-emerald-700 hover:bg-emerald-50 text-lg px-8 py-4 shadow-2xl transform hover:scale-105 transition-all font-bold">
        📧 Contact Us
      </a>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

