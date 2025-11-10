<?php
http_response_code(503);
$pageTitle = 'Service Unavailable';
$pageDescription = 'The service is temporarily unavailable for maintenance.';

// Load config and header with error suppression
@require_once __DIR__ . '/includes/config.php';
@require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-16">
  <div class="max-w-2xl mx-auto text-center">
    <div class="mb-8">
      <div class="text-9xl font-extrabold text-blue-100 mb-4">503</div>
      <h1 class="text-5xl font-extrabold text-slate-900 mb-4">
        <span class="gradient-text">Service Unavailable</span>
      </h1>
      <p class="text-xl text-slate-600 mb-8">
        We're currently performing maintenance. We'll be back shortly!
      </p>
    </div>

    <div class="rounded-2xl border-2 border-blue-100 bg-gradient-to-br from-white to-blue-50/30 p-8 shadow-xl mb-8">
      <div class="text-6xl mb-4">🔧</div>
      <p class="text-slate-700 mb-6">
        We're working hard to improve your experience. Please check back in a few minutes.
      </p>
      <div class="space-y-4">
        <a href="javascript:location.reload()" class="block btn text-lg px-8 py-4 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all">
          🔄 Refresh Page
        </a>
        <a href="/" class="block btn-secondary text-lg px-8 py-4 hover:border-brand hover:text-brand transition-all">
          🏠 Go to Homepage
        </a>
      </div>
    </div>

    <div class="rounded-lg bg-slate-50 p-6">
      <h3 class="font-bold text-slate-900 mb-3">What's happening?</h3>
      <p class="text-slate-700 text-sm mb-4">
        We're performing scheduled maintenance to improve our services. This usually takes just a few minutes.
      </p>
      <p class="text-slate-600 text-xs">
        If you have urgent questions, please <a href="/contact" class="text-brand hover:underline font-semibold">contact us</a>.
      </p>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

