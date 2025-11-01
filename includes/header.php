<?php
require_once __DIR__ . '/config.php';
?>
<!doctype html>
<html lang="en-NZ">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' : ''; ?><?php echo COMPANY_NAME; ?></title>
  <meta name="description" content="<?php echo isset($pageDescription) ? htmlspecialchars($pageDescription) : (isset($SITE) ? $SITE['description'] : 'Trash2Cash NZ'); ?>" />
  <meta property="og:title" content="<?php echo COMPANY_NAME; ?>" />
  <meta property="og:description" content="<?php echo isset($SITE) ? $SITE['description'] : 'Trash2Cash NZ'; ?>" />
  <meta property="og:url" content="<?php echo isset($SITE) ? $SITE['url'] : 'https://trash2cash.nz'; ?><?php echo isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : ''; ?>" />
  <meta property="og:image" content="<?php echo isset($SITE) ? $SITE['url'] : 'https://trash2cash.nz'; ?><?php echo isset($SITE) ? $SITE['ogImage'] : '/og.svg'; ?>" />
  <meta property="og:type" content="website" />
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
          }
        }
      }
    }
  </script>
  <link rel="stylesheet" href="/assets/styles.css" />
</head>
<body class="min-h-screen bg-white text-slate-800">
  <header class="sticky top-0 z-50 w-full border-b bg-white/90 backdrop-blur">
    <div class="container flex h-16 items-center justify-between">
      <a href="/" class="flex items-center gap-2 font-semibold">
        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-brand text-white">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10" />
            <path d="M12 6v6l4 2" />
          </svg>
        </span>
        <span><?php echo COMPANY_NAME; ?></span>
      </a>
      <nav class="hidden gap-6 md:flex">
        <a href="/" class="text-sm font-medium hover:text-brand <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'text-brand' : 'text-slate-700'; ?>">Home</a>
        <a href="/how-it-works.php" class="text-sm font-medium hover:text-brand <?php echo (basename($_SERVER['PHP_SELF']) == 'how-it-works.php') ? 'text-brand' : 'text-slate-700'; ?>">How it Works</a>
        <a href="/rewards.php" class="text-sm font-medium hover:text-brand <?php echo (basename($_SERVER['PHP_SELF']) == 'rewards.php') ? 'text-brand' : 'text-slate-700'; ?>">Rewards</a>
        <a href="/schedule-pickup.php" class="text-sm font-medium hover:text-brand <?php echo (basename($_SERVER['PHP_SELF']) == 'schedule-pickup.php') ? 'text-brand' : 'text-slate-700'; ?>">Schedule Pickup</a>
        <a href="/partners.php" class="text-sm font-medium hover:text-brand <?php echo (basename($_SERVER['PHP_SELF']) == 'partners.php') ? 'text-brand' : 'text-slate-700'; ?>">Partners</a>
        <a href="/faq.php" class="text-sm font-medium hover:text-brand <?php echo (basename($_SERVER['PHP_SELF']) == 'faq.php') ? 'text-brand' : 'text-slate-700'; ?>">FAQ</a>
        <a href="/contact.php" class="text-sm font-medium hover:text-brand <?php echo (basename($_SERVER['PHP_SELF']) == 'contact.php') ? 'text-brand' : 'text-slate-700'; ?>">Contact</a>
      </nav>
      <div class="md:hidden">
        <a href="/schedule-pickup.php" class="btn text-sm">Schedule Pickup</a>
      </div>
    </div>
  </header>
  <main>

