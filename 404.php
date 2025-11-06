<?php
http_response_code(404);
$pageTitle = 'Page Not Found';
$pageDescription = 'The page you are looking for could not be found.';

// Load config and header with error suppression
@require_once __DIR__ . '/includes/config.php';
@require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-16">
  <div class="max-w-2xl mx-auto text-center">
    <div class="mb-8">
      <div class="text-9xl font-extrabold text-emerald-100 mb-4">404</div>
      <h1 class="text-5xl font-extrabold text-slate-900 mb-4">
        <span class="gradient-text">Page Not Found</span>
      </h1>
      <p class="text-xl text-slate-600 mb-8">
        Oops! The page you're looking for doesn't exist or has been moved.
      </p>
    </div>

    <div class="rounded-2xl border-2 border-emerald-100 bg-gradient-to-br from-white to-emerald-50/30 p-8 shadow-xl mb-8">
      <div class="text-6xl mb-4">🔍</div>
      <p class="text-slate-700 mb-6">
        Don't worry! Here are some helpful links to get you back on track:
      </p>
      <div class="grid gap-4 sm:grid-cols-2">
        <a href="/" class="btn text-lg px-6 py-3 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all">
          🏠 Home
        </a>
        <a href="/schedule-pickup.php" class="btn text-lg px-6 py-3 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all">
          📅 Schedule Pickup
        </a>
        <a href="/how-it-works.php" class="btn-secondary text-lg px-6 py-3 hover:border-brand hover:text-brand transition-all">
          📖 How It Works
        </a>
        <a href="/contact.php" class="btn-secondary text-lg px-6 py-3 hover:border-brand hover:text-brand transition-all">
          📧 Contact Us
        </a>
      </div>
    </div>

    <div class="text-sm text-slate-500">
      <p>If you believe this is an error, please <a href="/contact.php" class="text-brand hover:underline font-semibold">contact us</a>.</p>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

