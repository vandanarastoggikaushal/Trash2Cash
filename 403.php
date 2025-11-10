<?php
http_response_code(403);
$pageTitle = 'Access Forbidden';
$pageDescription = 'You do not have permission to access this resource.';

// Load config and header with error suppression
@require_once __DIR__ . '/includes/config.php';
@require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-16">
  <div class="max-w-2xl mx-auto text-center">
    <div class="mb-8">
      <div class="text-9xl font-extrabold text-orange-100 mb-4">403</div>
      <h1 class="text-5xl font-extrabold text-slate-900 mb-4">
        <span class="gradient-text">Access Forbidden</span>
      </h1>
      <p class="text-xl text-slate-600 mb-8">
        You don't have permission to access this page or resource.
      </p>
    </div>

    <div class="rounded-2xl border-2 border-orange-100 bg-gradient-to-br from-white to-orange-50/30 p-8 shadow-xl mb-8">
      <div class="text-6xl mb-4">🔒</div>
      <p class="text-slate-700 mb-6">
        This page requires special permissions or you may need to log in.
      </p>
      <div class="space-y-4">
        <a href="/" class="block btn text-lg px-8 py-4 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all">
          🏠 Go to Homepage
        </a>
        <a href="/login" class="block btn-secondary text-lg px-8 py-4 hover:border-brand hover:text-brand transition-all">
          🔐 Login
        </a>
      </div>
    </div>

    <div class="text-sm text-slate-500">
      <p>If you believe you should have access, please <a href="/contact" class="text-brand hover:underline font-semibold">contact us</a>.</p>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

