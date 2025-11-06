<?php
$pageTitle = 'Login';
$pageDescription = 'Login to your Trash2Cash account';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    $redirect = $_GET['redirect'] ?? '/';
    header('Location: ' . $redirect);
    exit;
}

$error = '';
$success = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        $user = login($username, $password);
        if ($user) {
            $redirect = $_GET['redirect'] ?? '/';
            header('Location: ' . $redirect);
            exit;
        } else {
            $error = 'Invalid username or password';
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-16">
  <div class="max-w-md mx-auto">
    <div class="text-center mb-8">
      <h1 class="text-4xl font-extrabold text-slate-900 mb-2">
        <span class="gradient-text">🔐 Login</span>
      </h1>
      <p class="text-slate-600">Sign in to your account</p>
    </div>

    <div class="rounded-2xl border-2 border-emerald-100 bg-gradient-to-br from-white to-emerald-50/30 p-8 shadow-xl">
      <?php if ($error): ?>
        <div class="mb-6 p-4 rounded-lg bg-red-100 border-2 border-red-300 text-red-800">
          <div class="flex items-center gap-2">
            <span class="text-xl">❌</span>
            <span class="font-semibold"><?php echo htmlspecialchars($error); ?></span>
          </div>
        </div>
      <?php endif; ?>

      <?php if ($success): ?>
        <div class="mb-6 p-4 rounded-lg bg-emerald-100 border-2 border-emerald-300 text-emerald-800">
          <div class="flex items-center gap-2">
            <span class="text-xl">✅</span>
            <span class="font-semibold"><?php echo htmlspecialchars($success); ?></span>
          </div>
        </div>
      <?php endif; ?>

      <form method="POST" action="" class="space-y-6">
        <input type="hidden" name="action" value="login">
        
        <div>
          <label for="username" class="block text-sm font-semibold text-slate-900 mb-2">
            Username
          </label>
          <input 
            id="username" 
            name="username" 
            type="text" 
            required 
            autofocus
            class="w-full rounded-lg border-2 border-emerald-200 px-4 py-3 focus:border-brand focus:ring-2 focus:ring-emerald-200 transition-all"
            placeholder="Enter your username"
            value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
          />
        </div>

        <div>
          <label for="password" class="block text-sm font-semibold text-slate-900 mb-2">
            Password
          </label>
          <input 
            id="password" 
            name="password" 
            type="password" 
            required
            class="w-full rounded-lg border-2 border-emerald-200 px-4 py-3 focus:border-brand focus:ring-2 focus:ring-emerald-200 transition-all"
            placeholder="Enter your password"
          />
        </div>

        <button 
          type="submit" 
          class="w-full btn text-lg px-8 py-4 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all font-bold"
        >
          🔐 Sign In
        </button>
      </form>

      <div class="mt-6 pt-6 border-t border-emerald-200 text-center">
        <p class="text-sm text-slate-600">
          Don't have an account? 
          <a href="/register.php" class="text-brand font-semibold hover:underline">Register here</a>
        </p>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

