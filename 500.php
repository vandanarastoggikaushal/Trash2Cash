<?php
http_response_code(500);
$pageTitle = 'Server Error';
$pageDescription = 'We encountered an error processing your request.';

// Load config and header with error suppression (in case they're causing the error)
@require_once __DIR__ . '/includes/config.php';
@require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-16">
  <div class="max-w-2xl mx-auto text-center">
    <div class="mb-8">
      <div class="text-9xl font-extrabold text-red-100 mb-4">500</div>
      <h1 class="text-5xl font-extrabold text-slate-900 mb-4">
        <span class="gradient-text">Server Error</span>
      </h1>
      <p class="text-xl text-slate-600 mb-8">
        We're sorry, but something went wrong on our end. Our team has been notified.
      </p>
    </div>

    <div class="rounded-2xl border-2 border-red-100 bg-gradient-to-br from-white to-red-50/30 p-8 shadow-xl mb-8">
      <div class="text-6xl mb-4">⚠️</div>
      <p class="text-slate-700 mb-6">
        Don't worry, this is usually temporary. Please try again in a few moments.
      </p>
      <div class="space-y-4">
        <a href="/" class="block btn text-lg px-8 py-4 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all">
          🏠 Go to Homepage
        </a>
        <a href="javascript:location.reload()" class="block btn-secondary text-lg px-8 py-4 hover:border-brand hover:text-brand transition-all">
          🔄 Try Again
        </a>
      </div>
    </div>

    <div class="rounded-lg bg-slate-50 p-6 text-left">
      <h3 class="font-bold text-slate-900 mb-3">What you can do:</h3>
      <ul class="space-y-2 text-slate-700 text-sm">
        <li class="flex items-start gap-2">
          <span class="text-brand">✓</span>
          <span>Wait a few moments and try again</span>
        </li>
        <li class="flex items-start gap-2">
          <span class="text-brand">✓</span>
          <span>Clear your browser cache and refresh</span>
        </li>
        <li class="flex items-start gap-2">
          <span class="text-brand">✓</span>
          <span>If the problem persists, <a href="/contact" class="text-brand hover:underline font-semibold">contact us</a></span>
        </li>
      </ul>
    </div>

    <div class="mt-8 text-sm text-slate-500">
      <p>Error Reference: <code class="bg-slate-100 px-2 py-1 rounded"><?php echo bin2hex(random_bytes(4)); ?></code></p>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

