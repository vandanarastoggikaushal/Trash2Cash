<?php
require_once __DIR__ . '/config.php';

// Load auth functions if available (for login/logout display)
// Suppress errors to prevent 500 errors if auth.php has issues
if (file_exists(__DIR__ . '/auth.php')) {
    try {
        require_once __DIR__ . '/auth.php';
    } catch (Exception $e) {
        // Auth file has errors - log but don't break the site
        error_log('Error loading auth.php in header: ' . $e->getMessage());
        // Define a fallback function to prevent errors
        if (!function_exists('isLoggedIn')) {
            function isLoggedIn() { return false; }
        }
    }
}

// Determine display name for logged-in users
$userDisplayName = '';
if (function_exists('isLoggedIn') && isLoggedIn()) {
    if (function_exists('getUserDisplayName')) {
        $userDisplayName = getUserDisplayName(true);
    } else {
        $usernameFallback = $_SESSION['username'] ?? '';
        $userDisplayName = strtoupper($usernameFallback);
    }
}

// Get current page URL
$currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$baseUrl = isset($SITE) && isset($SITE['url']) ? $SITE['url'] : 'https://trash2cash.co.nz';

// Prepare page-specific data
$pageTitleFull = isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' . COMPANY_NAME : COMPANY_NAME;
$pageDesc = isset($pageDescription) ? htmlspecialchars($pageDescription) : (isset($SITE) && isset($SITE['description']) ? htmlspecialchars($SITE['description']) : 'Trash2Cash NZ - Turn your trash into cash or KiwiSaver. We collect clean aluminium cans and old appliances from your home across Wellington.');
$ogImage = $baseUrl . (isset($SITE) && isset($SITE['ogImage']) ? $SITE['ogImage'] : '/favicon.svg');
$canonicalUrl = $currentUrl;

// Clean up canonical URL (remove query strings for static pages)
$canonicalUrl = preg_replace('/\?.*$/', '', $canonicalUrl);
?>
<!doctype html>
<html lang="en-NZ">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  
  <!-- Primary Meta Tags -->
  <title><?php echo $pageTitleFull; ?></title>
  <meta name="title" content="<?php echo $pageTitleFull; ?>" />
  <meta name="description" content="<?php echo $pageDesc; ?>" />
  <meta name="keywords" content="recycling, aluminium cans, appliance recycling, Wellington, New Zealand, KiwiSaver, cash for cans, trash to cash, recycling service, door-to-door pickup, Wellington recycling" />
  <meta name="author" content="<?php echo COMPANY_NAME; ?>" />
  <meta name="robots" content="index, follow" />
  <meta name="language" content="English" />
  <meta name="revisit-after" content="7 days" />
  <meta name="geo.region" content="NZ-WGN" />
  <meta name="geo.placename" content="Wellington" />
  <meta name="geo.position" content="-41.2865;174.7762" />
  <meta name="ICBM" content="-41.2865, 174.7762" />
  
  <!-- Canonical URL -->
  <link rel="canonical" href="<?php echo htmlspecialchars($canonicalUrl); ?>" />
  
  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="website" />
  <meta property="og:url" content="<?php echo htmlspecialchars($canonicalUrl); ?>" />
  <meta property="og:title" content="<?php echo htmlspecialchars($pageTitleFull); ?>" />
  <meta property="og:description" content="<?php echo $pageDesc; ?>" />
  <meta property="og:image" content="<?php echo htmlspecialchars($ogImage); ?>" />
  <meta property="og:image:width" content="1200" />
  <meta property="og:image:height" content="630" />
  <meta property="og:image:alt" content="<?php echo COMPANY_NAME; ?> - Recycling Service in Wellington, New Zealand" />
  <meta property="og:site_name" content="<?php echo COMPANY_NAME; ?>" />
  <meta property="og:locale" content="en_NZ" />
  
  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:url" content="<?php echo htmlspecialchars($canonicalUrl); ?>" />
  <meta name="twitter:title" content="<?php echo htmlspecialchars($pageTitleFull); ?>" />
  <meta name="twitter:description" content="<?php echo $pageDesc; ?>" />
  <meta name="twitter:image" content="<?php echo htmlspecialchars($ogImage); ?>" />
  <meta name="twitter:image:alt" content="<?php echo COMPANY_NAME; ?> - Recycling Service in Wellington, New Zealand" />
  
  <!-- Favicons -->
  <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
  <link rel="icon" type="image/png" href="/favicon.png" />
  <link rel="apple-touch-icon" href="/favicon.png" />
  
  <!-- Additional SEO -->
  <meta name="theme-color" content="#15803d" />
  <meta name="msapplication-TileColor" content="#15803d" />
  
  <!-- Structured Data (JSON-LD) -->
  <?php
  // Add LocalBusiness schema for homepage
  if (basename($_SERVER['PHP_SELF']) == 'index.php' || $_SERVER['REQUEST_URI'] == '/' || $_SERVER['REQUEST_URI'] == '/index.php'):
  ?>
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "LocalBusiness",
    "name": "<?php echo COMPANY_NAME; ?>",
    "description": "<?php echo htmlspecialchars($pageDesc, ENT_QUOTES, 'UTF-8'); ?>",
    "url": "<?php echo $baseUrl; ?>",
    "telephone": "<?php echo SUPPORT_PHONE; ?>",
    "email": "<?php echo SUPPORT_EMAIL; ?>",
    "address": {
      "@type": "PostalAddress",
      "addressLocality": "<?php echo CITY; ?>",
      "addressRegion": "Wellington",
      "addressCountry": "NZ"
    },
    "geo": {
      "@type": "GeoCoordinates",
      "latitude": -41.2865,
      "longitude": 174.7762
    },
    "areaServed": [
      "Wellington City",
      "Churton Park",
      "Johnsonville",
      "Karori",
      "Newlands",
      "Tawa",
      "Lower Hutt",
      "Upper Hutt",
      "Porirua"
    ],
    "priceRange": "$",
    "serviceType": "Recycling Service",
    "image": "<?php echo $ogImage; ?>",
    "sameAs": []
  }
  </script>
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "<?php echo COMPANY_NAME; ?>",
    "url": "<?php echo $baseUrl; ?>",
    "logo": "<?php echo $baseUrl; ?>/favicon.svg",
    "contactPoint": {
      "@type": "ContactPoint",
      "telephone": "<?php echo SUPPORT_PHONE; ?>",
      "contactType": "Customer Service",
      "email": "<?php echo SUPPORT_EMAIL; ?>",
      "areaServed": "NZ",
      "availableLanguage": "en-NZ"
    }
  }
  </script>
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "WebSite",
    "name": "<?php echo COMPANY_NAME; ?>",
    "url": "<?php echo $baseUrl; ?>",
    "potentialAction": {
      "@type": "SearchAction",
      "target": "<?php echo $baseUrl; ?>?s={search_term_string}",
      "query-input": "required name=search_term_string"
    }
  }
  </script>
  <?php endif; ?>
  
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
      <nav class="hidden gap-2 md:flex" role="navigation" aria-label="Main navigation">
        <a href="/" class="px-4 py-2 rounded-lg text-sm font-semibold transition-all <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'bg-emerald-100 text-brand shadow-md' : 'text-slate-700 hover:bg-emerald-50'; ?>" aria-label="Home page">🏠 Home</a>
        <a href="/how-it-works.php" class="px-4 py-2 rounded-lg text-sm font-semibold transition-all <?php echo (basename($_SERVER['PHP_SELF']) == 'how-it-works.php') ? 'bg-emerald-100 text-brand shadow-md' : 'text-slate-700 hover:bg-emerald-50'; ?>" aria-label="How it works">📖 How it Works</a>
        <a href="/rewards.php" class="px-4 py-2 rounded-lg text-sm font-semibold transition-all <?php echo (basename($_SERVER['PHP_SELF']) == 'rewards.php') ? 'bg-emerald-100 text-brand shadow-md' : 'text-slate-700 hover:bg-emerald-50'; ?>" aria-label="Rewards and pricing">💰 Rewards</a>
        <a href="/partners.php" class="px-4 py-2 rounded-lg text-sm font-semibold transition-all <?php echo (basename($_SERVER['PHP_SELF']) == 'partners.php') ? 'bg-emerald-100 text-brand shadow-md' : 'text-slate-700 hover:bg-emerald-50'; ?>" aria-label="Partners and fundraising">🤝 Partners</a>
        <a href="/faq.php" class="px-4 py-2 rounded-lg text-sm font-semibold transition-all <?php echo (basename($_SERVER['PHP_SELF']) == 'faq.php') ? 'bg-emerald-100 text-brand shadow-md' : 'text-slate-700 hover:bg-emerald-50'; ?>" aria-label="Frequently asked questions">❓ FAQ</a>
        <a href="/contact.php" class="px-4 py-2 rounded-lg text-sm font-semibold transition-all <?php echo (basename($_SERVER['PHP_SELF']) == 'contact.php') ? 'bg-emerald-100 text-brand shadow-md' : 'text-slate-700 hover:bg-emerald-50'; ?>" aria-label="Contact us">📧 Contact</a>
      </nav>
      <div class="flex items-center gap-2">
        <a href="/schedule-pickup.php" class="btn text-sm hidden md:inline-flex">Schedule</a>
        <a href="/schedule-pickup.php" class="btn text-sm md:hidden">Schedule</a>
        <div class="relative">
          <button
            id="user-menu-toggle"
            type="button"
            class="inline-flex h-11 w-11 items-center justify-center rounded-full border-2 border-emerald-200 bg-white text-slate-700 shadow-sm transition-all hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-brand"
            aria-haspopup="true"
            aria-expanded="false"
          >
            <span class="sr-only">Toggle user menu</span>
            <span class="flex flex-col items-center justify-center gap-1">
              <span class="block h-0.5 w-6 rounded-full bg-slate-700"></span>
              <span class="block h-0.5 w-6 rounded-full bg-slate-700"></span>
              <span class="block h-0.5 w-6 rounded-full bg-slate-700"></span>
            </span>
          </button>
          <div
            id="user-menu-panel"
            class="hidden absolute right-0 z-50 mt-3 w-60 rounded-2xl border-2 border-emerald-100 bg-white shadow-2xl"
            role="menu"
            aria-labelledby="user-menu-toggle"
          >
            <?php if (isLoggedIn()): ?>
              <div class="px-4 py-3 border-b border-emerald-100">
                <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Signed in as</p>
                <p class="mt-1 text-sm font-bold text-slate-800"><?php echo htmlspecialchars($userDisplayName); ?></p>
                <?php if (!empty($_SESSION['user_email'])): ?>
                <p class="text-xs text-slate-500 truncate"><?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
                <?php endif; ?>
              </div>
              <a href="/account.php" class="flex items-center gap-2 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-emerald-50" role="menuitem">
                <span>💼</span>
                <span>Account</span>
              </a>
              <?php if (function_exists('hasRole') && hasRole('admin')): ?>
              <a href="/admin/payments.php" class="flex items-center gap-2 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-emerald-50" role="menuitem">
                <span>🧾</span>
                <span>Payments Admin</span>
              </a>
              <?php endif; ?>
              <div class="border-t border-emerald-100"></div>
              <a href="/api/logout.php" class="flex items-center gap-2 px-4 py-3 text-sm font-semibold text-red-600 hover:bg-red-50" role="menuitem">
                <span>🚪</span>
                <span>Logout</span>
              </a>
            <?php else: ?>
              <a href="/login.php" class="flex items-center gap-2 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-emerald-50" role="menuitem">
                <span>🔐</span>
                <span>Login</span>
              </a>
              <a href="/register.php" class="flex items-center gap-2 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-emerald-50" role="menuitem">
                <span>✨</span>
                <span>Register</span>
              </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </header>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var toggle = document.getElementById('user-menu-toggle');
      var panel = document.getElementById('user-menu-panel');

      if (!toggle || !panel) {
        return;
      }

      function openMenu() {
        panel.classList.remove('hidden');
        toggle.setAttribute('aria-expanded', 'true');
      }

      function closeMenu() {
        if (!panel.classList.contains('hidden')) {
          panel.classList.add('hidden');
          toggle.setAttribute('aria-expanded', 'false');
        }
      }

      toggle.addEventListener('click', function (event) {
        event.stopPropagation();
        if (panel.classList.contains('hidden')) {
          openMenu();
        } else {
          closeMenu();
        }
      });

      document.addEventListener('click', function (event) {
        if (!panel.contains(event.target) && !toggle.contains(event.target)) {
          closeMenu();
        }
      });

      document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
          closeMenu();
          toggle.focus();
        }
      });
    });
  </script>
  <main>

