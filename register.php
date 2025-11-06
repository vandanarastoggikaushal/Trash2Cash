<?php
$pageTitle = 'Register';
$pageDescription = 'Create a new Trash2Cash account';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: /');
    exit;
}

$error = '';
$success = '';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';
    $email = trim($_POST['email'] ?? '');
    
    // Validation
    if (empty($username)) {
        $error = 'Username is required';
    } elseif (strlen($username) < 3) {
        $error = 'Username must be at least 3 characters';
    } elseif (empty($password)) {
        $error = 'Password is required';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } elseif ($password !== $passwordConfirm) {
        $error = 'Passwords do not match';
    } else {
        $user = createUser($username, $password, $email);
        if ($user) {
            // Auto-login after registration
            login($username, $password);
            header('Location: /');
            exit;
        } else {
            $error = 'Username already exists. Please choose a different username.';
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-16">
  <div class="max-w-md mx-auto">
    <div class="text-center mb-8">
      <h1 class="text-4xl font-extrabold text-slate-900 mb-2">
        <span class="gradient-text">✨ Register</span>
      </h1>
      <p class="text-slate-600">Create a new account</p>
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

      <form method="POST" action="" class="space-y-6">
        <input type="hidden" name="action" value="register">
        
        <div>
          <label for="username" class="block text-sm font-semibold text-slate-900 mb-2">
            Username <span class="text-red-500">*</span>
          </label>
          <input 
            id="username" 
            name="username" 
            type="text" 
            required 
            autofocus
            minlength="3"
            class="w-full rounded-lg border-2 border-emerald-200 px-4 py-3 focus:border-brand focus:ring-2 focus:ring-emerald-200 transition-all"
            placeholder="Choose a username (min 3 characters)"
            value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
          />
        </div>

        <div>
          <label for="email" class="block text-sm font-semibold text-slate-900 mb-2">
            Email (optional)
          </label>
          <input 
            id="email" 
            name="email" 
            type="email"
            class="w-full rounded-lg border-2 border-emerald-200 px-4 py-3 focus:border-brand focus:ring-2 focus:ring-emerald-200 transition-all"
            placeholder="your.email@example.com"
            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
          />
        </div>

        <div>
          <label for="password" class="block text-sm font-semibold text-slate-900 mb-2">
            Password <span class="text-red-500">*</span>
          </label>
          <input 
            id="password" 
            name="password" 
            type="password" 
            required
            minlength="6"
            class="w-full rounded-lg border-2 border-emerald-200 px-4 py-3 focus:border-brand focus:ring-2 focus:ring-emerald-200 transition-all"
            placeholder="Enter password (min 6 characters)"
          />
        </div>

        <div>
          <label for="password_confirm" class="block text-sm font-semibold text-slate-900 mb-2">
            Confirm Password <span class="text-red-500">*</span>
          </label>
          <input 
            id="password_confirm" 
            name="password_confirm" 
            type="password" 
            required
            minlength="6"
            class="w-full rounded-lg border-2 border-emerald-200 px-4 py-3 focus:border-brand focus:ring-2 focus:ring-emerald-200 transition-all"
            placeholder="Confirm your password"
          />
        </div>

        <button 
          type="submit" 
          class="w-full btn text-lg px-8 py-4 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all font-bold"
        >
          ✨ Create Account
        </button>
      </form>

      <div class="mt-6 pt-6 border-t border-emerald-200 text-center">
        <p class="text-sm text-slate-600">
          Already have an account? 
          <a href="/login.php" class="text-brand font-semibold hover:underline">Login here</a>
        </p>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

