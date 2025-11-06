<?php
$pageTitle = 'Contact Us';
$pageDescription = 'Contact Trash2Cash NZ for recycling services in Wellington. Call +64221758458 or email collect@trash2cash.co.nz for questions about aluminium can and appliance pickups.';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-16">
  <div class="text-center mb-12">
    <h1 class="text-5xl font-extrabold text-slate-900 mb-4">
      <span class="gradient-text">📧 Contact Us</span>
    </h1>
    <p class="text-xl text-slate-600 max-w-2xl mx-auto">Get in touch with our team - we're here to help!</p>
  </div>
  
  <div class="grid gap-8 lg:grid-cols-2 max-w-5xl mx-auto">
    <div class="rounded-2xl bg-gradient-to-br from-emerald-50 to-green-50 p-8 border-2 border-emerald-100 shadow-xl">
      <h2 class="text-2xl font-bold text-slate-900 mb-6 flex items-center gap-2">
        <span class="text-3xl">💬</span> Get in Touch
      </h2>
      <div class="space-y-4">
        <div class="flex items-center gap-3 p-4 rounded-lg bg-white border-2 border-emerald-100">
          <span class="text-2xl">📞</span>
          <div>
            <p class="font-semibold text-slate-900">Phone</p>
            <p class="text-brand font-bold"><?php echo SUPPORT_PHONE; ?></p>
          </div>
        </div>
        <div class="flex items-center gap-3 p-4 rounded-lg bg-white border-2 border-emerald-100">
          <span class="text-2xl">✉️</span>
          <div>
            <p class="font-semibold text-slate-900">Email</p>
            <a href="mailto:<?php echo SUPPORT_EMAIL; ?>" class="text-brand font-bold hover:underline"><?php echo SUPPORT_EMAIL; ?></a>
          </div>
        </div>
        <div class="flex items-center gap-3 p-4 rounded-lg bg-white border-2 border-emerald-100">
          <span class="text-2xl">📍</span>
          <div>
            <p class="font-semibold text-slate-900">Location</p>
            <p class="text-slate-700"><?php echo CITY; ?>, New Zealand</p>
          </div>
        </div>
      </div>
    </div>
    
    <div class="rounded-2xl bg-gradient-to-br from-white to-emerald-50/50 p-8 border-2 border-emerald-200 shadow-xl">
      <h2 class="text-2xl font-bold text-slate-900 mb-6 flex items-center gap-2">
        <span class="text-3xl">📝</span> Send us a Message
      </h2>
      <form id="contact-form" class="space-y-6">
        <div>
          <label class="block text-sm font-semibold text-slate-900 mb-2" for="name">Name</label>
          <input id="name" name="name" type="text" class="w-full rounded-lg border-2 border-emerald-200 px-4 py-3 focus:border-brand focus:ring-2 focus:ring-emerald-200 transition-all" required />
        </div>
        <div>
          <label class="block text-sm font-semibold text-slate-900 mb-2" for="email">Email</label>
          <input id="email" name="email" type="email" class="w-full rounded-lg border-2 border-emerald-200 px-4 py-3 focus:border-brand focus:ring-2 focus:ring-emerald-200 transition-all" required />
        </div>
        <div>
          <label class="block text-sm font-semibold text-slate-900 mb-2" for="message">Message</label>
          <textarea id="message" name="message" rows="6" class="w-full rounded-lg border-2 border-emerald-200 px-4 py-3 focus:border-brand focus:ring-2 focus:ring-emerald-200 transition-all" required></textarea>
        </div>
        <button class="w-full btn text-lg px-8 py-4 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all font-bold" type="submit" id="submit-btn">
          ✨ Send Message
        </button>
      </form>
      <div id="contact-success" class="mt-4 hidden rounded-lg bg-gradient-to-r from-emerald-100 to-green-100 border-2 border-brand p-4 text-emerald-900 font-semibold">
        ✅ Message sent successfully! We'll get back to you soon.
      </div>
      <div id="contact-error" class="mt-4 hidden rounded-lg bg-gradient-to-r from-red-100 to-pink-100 border-2 border-red-300 p-4 text-red-900 font-semibold">
        ❌ Something went wrong. Please try again or email us directly.
      </div>
    </div>
  </div>
</div>

<script src="/assets/contact.js"></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

