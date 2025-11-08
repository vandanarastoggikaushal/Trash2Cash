<?php
$pageTitle = 'Dashboard';
$pageDescription = 'User dashboard';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';

// Require login to access dashboard
requireLogin();

$user = getCurrentUser();
$displayName = function_exists('getUserDisplayName') ? getUserDisplayName(true) : strtoupper($user['username'] ?? '');
require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-16">
  <div class="max-w-4xl mx-auto">
    <div class="mb-8">
      <h1 class="text-4xl font-extrabold text-slate-900 mb-2">
        <span class="gradient-text">👤 Dashboard</span>
      </h1>
      <p class="text-slate-600">Welcome back, <?php echo htmlspecialchars($displayName); ?>!</p>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
      <!-- User Info Card -->
      <div class="rounded-2xl border-2 border-emerald-100 bg-gradient-to-br from-white to-emerald-50/30 p-6 shadow-xl">
        <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-2">
          <span>👤</span> Account Information
        </h2>
        <div class="space-y-3">
          <?php if (!empty($user['firstName'])): ?>
          <div>
            <span class="text-sm font-semibold text-slate-600">First Name:</span>
            <span class="ml-2 text-slate-900 font-bold"><?php echo htmlspecialchars(strtoupper($user['firstName'])); ?></span>
          </div>
          <?php endif; ?>
          <?php if (!empty($user['lastName'])): ?>
          <div>
            <span class="text-sm font-semibold text-slate-600">Last Name:</span>
            <span class="ml-2 text-slate-900 font-bold"><?php echo htmlspecialchars(strtoupper($user['lastName'])); ?></span>
          </div>
          <?php endif; ?>
          <div>
            <span class="text-sm font-semibold text-slate-600">Username:</span>
            <span class="ml-2 text-slate-900 font-bold"><?php echo htmlspecialchars($user['username']); ?></span>
          </div>
          <?php if (!empty($user['address'])): ?>
          <div>
            <span class="text-sm font-semibold text-slate-600">Address:</span>
            <span class="ml-2 text-slate-900"><?php echo nl2br(htmlspecialchars($user['address'])); ?></span>
          </div>
          <?php endif; ?>
          <?php if (!empty($user['email'])): ?>
          <div>
            <span class="text-sm font-semibold text-slate-600">Email:</span>
            <span class="ml-2 text-slate-900"><?php echo htmlspecialchars($user['email']); ?></span>
          </div>
          <?php endif; ?>
          <div>
            <span class="text-sm font-semibold text-slate-600">Role:</span>
            <span class="ml-2 px-2 py-1 rounded-full bg-emerald-100 text-emerald-800 text-sm font-semibold">
              <?php echo htmlspecialchars($user['role'] ?? 'user'); ?>
            </span>
          </div>
          <?php if (!empty($user['lastLogin'])): ?>
          <div>
            <span class="text-sm font-semibold text-slate-600">Last Login:</span>
            <span class="ml-2 text-slate-900">
              <?php echo date('F j, Y g:i A', strtotime($user['lastLogin'])); ?>
            </span>
          </div>
          <?php endif; ?>
          <div>
            <span class="text-sm font-semibold text-slate-600">Member Since:</span>
            <span class="ml-2 text-slate-900">
              <?php echo date('F j, Y', strtotime($user['createdAt'])); ?>
            </span>
          </div>
        </div>
      </div>

      <!-- Quick Actions Card -->
      <div class="rounded-2xl border-2 border-blue-100 bg-gradient-to-br from-white to-blue-50/30 p-6 shadow-xl">
        <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-2">
          <span>⚡</span> Quick Actions
        </h2>
        <div class="space-y-3">
          <a href="/schedule-pickup.php" class="block w-full px-4 py-3 rounded-lg bg-emerald-600 text-white font-semibold hover:bg-emerald-700 transition-all text-center">
            📅 Schedule a Pickup
          </a>
          <a href="/contact.php" class="block w-full px-4 py-3 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition-all text-center">
            📧 Contact Us
          </a>
          <a href="/rewards.php" class="block w-full px-4 py-3 rounded-lg bg-purple-600 text-white font-semibold hover:bg-purple-700 transition-all text-center">
            💰 View Rewards
          </a>
        </div>
      </div>
    </div>

    <?php if (hasRole('admin')): ?>
    <div class="mt-6 rounded-2xl border-2 border-red-100 bg-gradient-to-br from-red-50 to-pink-50/30 p-6 shadow-xl">
      <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-2">
        <span>🔧</span> Admin Panel
      </h2>
      <p class="text-slate-700 mb-4">You have administrator access.</p>
      <div class="space-y-2 text-sm text-slate-600">
        <p>• View all user accounts</p>
        <p>• Manage pickup requests</p>
        <p>• System configuration</p>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

