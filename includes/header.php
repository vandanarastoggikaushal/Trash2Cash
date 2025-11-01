<?php
require_once __DIR__ . '/config.php';
?>
<!doctype html>
<html lang="en-NZ">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' : ''; ?><?php echo COMPANY_NAME; ?></title>
  <meta name="description" content="<?php echo isset($pageDescription) ? htmlspecialchars($pageDescription) : (isset($SITE) && isset($SITE['description']) ? htmlspecialchars($SITE['description']) : 'Trash2Cash NZ'); ?>" />
  <meta property="og:title" content="<?php echo COMPANY_NAME; ?>" />
  <meta property="og:description" content="<?php echo isset($SITE) && isset($SITE['description']) ? htmlspecialchars($SITE['description']) : 'Trash2Cash NZ'; ?>" />
  <meta property="og:url" content="<?php echo isset($SITE) && isset($SITE['url']) ? htmlspecialchars($SITE['url']) : 'https://trash2cash.nz'; ?><?php echo isset($_SERVER['REQUEST_URI']) ? htmlspecialchars($_SERVER['REQUEST_URI']) : ''; ?>" />
  <meta property="og:image" content="<?php echo isset($SITE) && isset($SITE['url']) ? htmlspecialchars($SITE['url']) : 'https://trash2cash.nz'; ?><?php echo isset($SITE) && isset($SITE['ogImage']) ? htmlspecialchars($SITE['ogImage']) : '/og.svg'; ?>" />
  <meta property="og:type" content="website" />
  <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
  <link rel="icon" type="image/png" href="/favicon.png" />
  <link rel="apple-touch-icon" href="/favicon.png" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: {
              DEFAULT: '#15803d',
              light: '#22c55e',
              dark: '#166534'
            }
          },
          borderRadius: {
            lg: '12px',
            xl: '14px'
          }
        }
      }
    }
  </script>
  <link rel="stylesheet" href="/assets/styles.css" />
</head>
<body class="min-h-screen bg-white text-slate-800">
  <header class="sticky top-0 z-50 w-full border-b-2 border-emerald-200 bg-white/95 backdrop-blur-lg shadow-sm">
    <div class="container flex h-16 items-center justify-between">
      <a href="/" class="flex items-center gap-3 font-bold text-xl hover:scale-105 transition-transform">
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-green-600 text-white shadow-lg">
          <span class="text-xl">♻️</span>
        </span>
        <span class="gradient-text"><?php echo COMPANY_NAME; ?></span>
      </a>
      <nav class="hidden gap-2 md:flex">
        <a href="/" class="px-4 py-2 rounded-lg text-sm font-semibold transition-all <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'bg-emerald-100 text-brand shadow-md' : 'text-slate-700 hover:bg-emerald-50'; ?>">🏠 Home</a>
        <a href="/how-it-works.php" class="px-4 py-2 rounded-lg text-sm font-semibold transition-all <?php echo (basename($_SERVER['PHP_SELF']) == 'how-it-works.php') ? 'bg-emerald-100 text-brand shadow-md' : 'text-slate-700 hover:bg-emerald-50'; ?>">📖 How it Works</a>
        <a href="/rewards.php" class="px-4 py-2 rounded-lg text-sm font-semibold transition-all <?php echo (basename($_SERVER['PHP_SELF']) == 'rewards.php') ? 'bg-emerald-100 text-brand shadow-md' : 'text-slate-700 hover:bg-emerald-50'; ?>">💰 Rewards</a>
        <a href="/schedule-pickup.php" class="px-4 py-2 rounded-lg text-sm font-semibold transition-all <?php echo (basename($_SERVER['PHP_SELF']) == 'schedule-pickup.php') ? 'bg-emerald-100 text-brand shadow-md' : 'text-slate-700 hover:bg-emerald-50'; ?>">📅 Schedule</a>
        <a href="/partners.php" class="px-4 py-2 rounded-lg text-sm font-semibold transition-all <?php echo (basename($_SERVER['PHP_SELF']) == 'partners.php') ? 'bg-emerald-100 text-brand shadow-md' : 'text-slate-700 hover:bg-emerald-50'; ?>">🤝 Partners</a>
        <a href="/faq.php" class="px-4 py-2 rounded-lg text-sm font-semibold transition-all <?php echo (basename($_SERVER['PHP_SELF']) == 'faq.php') ? 'bg-emerald-100 text-brand shadow-md' : 'text-slate-700 hover:bg-emerald-50'; ?>">❓ FAQ</a>
        <a href="/contact.php" class="px-4 py-2 rounded-lg text-sm font-semibold transition-all <?php echo (basename($_SERVER['PHP_SELF']) == 'contact.php') ? 'bg-emerald-100 text-brand shadow-md' : 'text-slate-700 hover:bg-emerald-50'; ?>">📧 Contact</a>
      </nav>
      <div class="md:hidden">
        <a href="/schedule-pickup.php" class="btn text-sm">Schedule Pickup</a>
      </div>
    </div>
  </header>
  <main>

