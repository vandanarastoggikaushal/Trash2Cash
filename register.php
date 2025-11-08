<?php
$pageTitle = 'Register';
$pageDescription = 'Create a new Trash2Cash account';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/payments.php';

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
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $marketingOptIn = isset($_POST['marketingOptIn']) && $_POST['marketingOptIn'] === 'on';
    $payoutMethod = $_POST['payoutMethod'] ?? 'bank';
    $bankName = trim($_POST['bankName'] ?? '');
    $bankAccount = trim($_POST['bankAccount'] ?? '');
    $childName = trim($_POST['childName'] ?? '');
    $childBankAccount = trim($_POST['childBankAccount'] ?? '');
    $kiwisaverProvider = trim($_POST['kiwisaverProvider'] ?? '');
    $kiwisaverMemberId = trim($_POST['kiwisaverMemberId'] ?? '');
    $street = trim($_POST['address_street'] ?? '');
    $suburb = trim($_POST['address_suburb'] ?? '');
    $city = trim($_POST['address_city'] ?? '');
    $postcode = trim($_POST['address_postcode'] ?? '');
    $address = '';
    $normalizedAddressKey = '';
    
    // Validation
    if (empty($username)) {
        $error = 'Username is required';
    } elseif (strlen($username) < 3) {
        $error = 'Username must be at least 3 characters';
    } elseif (empty($firstName)) {
        $error = 'First name is required';
    } elseif (empty($lastName)) {
        $error = 'Last name is required';
    } elseif (empty($street)) {
        $error = 'Street is required';
    } elseif (empty($suburb)) {
        $error = 'Suburb is required';
    } elseif (empty($city)) {
        $error = 'City is required';
    } elseif (empty($postcode)) {
        $error = 'Postcode is required';
    } elseif (!preg_match('/^\d{4}$/', $postcode)) {
        $error = 'Please enter a valid 4-digit postcode';
    } elseif (empty($phone)) {
        $error = 'Phone number is required';
    } elseif (!preg_match('/^(\+64|0)[2-9]\d{7,8}$/', $phone)) {
        $error = 'Please enter a valid NZ phone number';
    } elseif (!in_array($payoutMethod, ['bank', 'child_account', 'kiwisaver'], true)) {
        $error = 'Please choose a valid payout method';
    } elseif ($payoutMethod === 'bank' && (empty($bankName) || empty($bankAccount))) {
        $error = 'Please provide your bank name and account number';
    } elseif ($payoutMethod === 'child_account' && empty($childName)) {
        $error = 'Please provide the child name for the child account payout method';
    } elseif ($payoutMethod === 'kiwisaver' && (empty($kiwisaverProvider) || empty($kiwisaverMemberId))) {
        $error = 'Please provide your KiwiSaver provider and member ID';
    } elseif (empty($email)) {
        $error = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } elseif (empty($password)) {
        $error = 'Password is required';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } elseif ($password !== $passwordConfirm) {
        $error = 'Passwords do not match';
    } else {
        $address = $street . "\n" . $suburb . "\n" . $city . ' ' . $postcode;
        $normalizedAddressKey = strtolower(preg_replace('/\s+/', ' ', str_replace(["\r", "\n"], ' ', $address)));

        // Enforce unique address (one registration per household)
        if (isDatabaseAvailable()) {
            $existingUser = dbQueryOne(
                "SELECT id FROM users WHERE LOWER(REPLACE(REPLACE(address, '\r', ' '), '\n', ' ')) = :address",
                [':address' => $normalizedAddressKey]
            );
            if ($existingUser) {
                $error = 'An account already exists for this address. Please contact support if you need assistance.';
            }
        } else {
            $existingUsers = getUsers();
            foreach ($existingUsers as $existing) {
                $existingAddress = $existing['address'] ?? '';
                $existingKey = strtolower(preg_replace('/\s+/', ' ', str_replace(["\r", "\n"], ' ', $existingAddress)));
                if ($existingKey === $normalizedAddressKey) {
                    $error = 'An account already exists for this address. Please contact support if you need assistance.';
                    break;
                }
            }
        }
    }

    if (empty($error)) {
        $extra = [
            'phone' => $phone,
            'marketingOptIn' => $marketingOptIn,
            'payoutMethod' => $payoutMethod,
            'payoutBankName' => $bankName,
            'payoutBankAccount' => $bankAccount,
            'payoutChildName' => $childName,
            'payoutChildBankAccount' => $childBankAccount,
            'payoutKiwisaverProvider' => $kiwisaverProvider,
            'payoutKiwisaverMemberId' => $kiwisaverMemberId
        ];
        $user = createUser($username, $password, $email, 'user', $firstName, $lastName, $address, $extra);
        if ($user) {
            // Record promotional welcome credit (pending until first collection)
            if (defined('PROMO_BONUS_AMOUNT') && PROMO_BONUS_AMOUNT > 0) {
                $bonusReference = 'Welcome bonus';
                $bonusNotes = 'Registration bonus payable on first collection';
                recordUserPayment(
                    $user['id'],
                    PROMO_BONUS_AMOUNT,
                    $bonusReference,
                    $bonusNotes,
                    gmdate('Y-m-d'),
                    defined('PROMO_BONUS_STATUS') ? PROMO_BONUS_STATUS : 'pending',
                    defined('PROMO_BONUS_CURRENCY') ? PROMO_BONUS_CURRENCY : 'NZD'
                );
            }

            // Auto-login after registration
            login($username, $password);
            header('Location: /');
            exit;
        } else {
            $error = 'Username already exists. Please choose a different username.';
        }
    }
}

$selectedPayoutMethod = $_POST['payoutMethod'] ?? 'bank';
$bankNameValue = $_POST['bankName'] ?? '';
$bankAccountValue = $_POST['bankAccount'] ?? '';
$childNameValue = $_POST['childName'] ?? '';
$childBankAccountValue = $_POST['childBankAccount'] ?? '';
$kiwiProviderValue = $_POST['kiwisaverProvider'] ?? '';
$kiwiMemberValue = $_POST['kiwisaverMemberId'] ?? '';

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

        <div class="grid gap-6 md:grid-cols-2">
          <div>
            <label for="first_name" class="block text-sm font-semibold text-slate-900 mb-2">
              First Name <span class="text-red-500">*</span>
            </label>
            <input
              id="first_name"
              name="first_name"
              type="text"
              required
              class="w-full rounded-lg border-2 border-emerald-200 px-4 py-3 focus:border-brand focus:ring-2 focus:ring-emerald-200 transition-all"
              placeholder="Your first name"
              value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>"
            />
          </div>

          <div>
            <label for="last_name" class="block text-sm font-semibold text-slate-900 mb-2">
              Last Name <span class="text-red-500">*</span>
            </label>
            <input
              id="last_name"
              name="last_name"
              type="text"
              required
              class="w-full rounded-lg border-2 border-emerald-200 px-4 py-3 focus:border-brand focus:ring-2 focus:ring-emerald-200 transition-all"
              placeholder="Your last name"
              value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>"
            />
          </div>
        </div>

        <div>
          <label for="email" class="block text-sm font-semibold text-slate-900 mb-2">
            Email <span class="text-red-500">*</span>
          </label>
          <input 
            id="email" 
            name="email" 
            type="email"
            required
            class="w-full rounded-lg border-2 border-emerald-200 px-4 py-3 focus:border-brand focus:ring-2 focus:ring-emerald-200 transition-all"
            placeholder="your.email@example.com"
            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
          />
        </div>

        <div>
          <label for="phone" class="block text-sm font-semibold text-slate-900 mb-2">
            Phone <span class="text-red-500">*</span>
          </label>
          <input
            id="phone"
            name="phone"
            type="tel"
            required
            pattern="^(\+64|0)[2-9]\d{7,8}$"
            class="w-full rounded-lg border-2 border-emerald-200 px-4 py-3 focus:border-brand focus:ring-2 focus:ring-emerald-200 transition-all"
            placeholder="e.g. 0212345678"
            value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
          />
          <p class="mt-1 text-xs text-slate-500">We use your phone number to coordinate pickups.</p>
        </div>

        <div>
          <label class="inline-flex items-center gap-2 text-sm text-slate-700 font-semibold">
            <input type="checkbox" name="marketingOptIn" value="on" <?php echo !empty($_POST['marketingOptIn']) ? 'checked' : ''; ?> />
            I'd like to receive Trash2Cash updates and offers
          </label>
        </div>

        <div class="rounded-2xl border-2 border-emerald-100 bg-white/70 p-4 shadow-inner space-y-4">
          <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
            <span>📍</span> Address Details
          </h2>
          <div class="grid gap-4">
            <div>
              <label for="address_street" class="block text-sm font-semibold text-slate-900 mb-2">
                Street <span class="text-red-500">*</span>
              </label>
              <input
                id="address_street"
                name="address_street"
                type="text"
                required
                class="w-full rounded-lg border-2 border-emerald-200 px-4 py-3 focus:border-brand focus:ring-2 focus:ring-emerald-200 transition-all"
                placeholder="e.g. 123 Example Street"
                value="<?php echo htmlspecialchars($_POST['address_street'] ?? ''); ?>"
              />
            </div>
            <div class="grid gap-4 md:grid-cols-2">
              <div>
                <label for="address_suburb" class="block text-sm font-semibold text-slate-900 mb-2">
                  Suburb <span class="text-red-500">*</span>
                </label>
                <input
                  id="address_suburb"
                  name="address_suburb"
                  type="text"
                  required
                  class="w-full rounded-lg border-2 border-emerald-200 px-4 py-3 focus:border-brand focus:ring-2 focus:ring-emerald-200 transition-all"
                  placeholder="e.g. Johnsonville"
                  value="<?php echo htmlspecialchars($_POST['address_suburb'] ?? ''); ?>"
                />
              </div>
              <div>
                <label for="address_city" class="block text-sm font-semibold text-slate-900 mb-2">
                  City <span class="text-red-500">*</span>
                </label>
                <input
                  id="address_city"
                  name="address_city"
                  type="text"
                  required
                  class="w-full rounded-lg border-2 border-emerald-200 px-4 py-3 focus:border-brand focus:ring-2 focus:ring-emerald-200 transition-all"
                  placeholder="e.g. Wellington"
                  value="<?php echo htmlspecialchars($_POST['address_city'] ?? CITY); ?>"
                />
              </div>
            </div>
            <div>
              <label for="address_postcode" class="block text-sm font-semibold text-slate-900 mb-2">
                Postcode <span class="text-red-500">*</span>
              </label>
              <input
                id="address_postcode"
                name="address_postcode"
                type="text"
                required
                pattern="\d{4}"
                maxlength="4"
                inputmode="numeric"
                class="w-full rounded-lg border-2 border-emerald-200 px-4 py-3 focus:border-brand focus:ring-2 focus:ring-emerald-200 transition-all"
                placeholder="e.g. 6011"
                value="<?php echo htmlspecialchars($_POST['address_postcode'] ?? ''); ?>"
              />
              <p class="text-xs text-slate-500 mt-1">Enter the 4-digit New Zealand postcode.</p>
            </div>
          </div>
        </div>

        <fieldset class="rounded-2xl border-2 border-emerald-100 bg-white/70 p-4 shadow-inner space-y-4">
          <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
            <span>💰</span> Payout Preference
          </h2>
          <p class="text-sm text-slate-600">Choose how you'd like to receive your rewards. You can update this later by contacting us.</p>
          <div class="space-y-3 text-sm font-semibold text-slate-700">
            <label class="flex items-center gap-2">
              <input type="radio" name="payoutMethod" value="bank" <?php echo $selectedPayoutMethod === 'bank' ? 'checked' : ''; ?> />
              Bank account
            </label>
            <div id="register-payout-bank" class="ml-6 grid gap-3 sm:grid-cols-2 <?php echo $selectedPayoutMethod === 'bank' ? '' : 'hidden'; ?>">
              <input name="bankName" placeholder="Bank name" class="rounded-md border-2 border-emerald-200 px-3 py-2" value="<?php echo htmlspecialchars($bankNameValue); ?>" />
              <input name="bankAccount" placeholder="Account number" class="rounded-md border-2 border-emerald-200 px-3 py-2" value="<?php echo htmlspecialchars($bankAccountValue); ?>" />
            </div>

            <label class="flex items-center gap-2">
              <input type="radio" name="payoutMethod" value="child_account" <?php echo $selectedPayoutMethod === 'child_account' ? 'checked' : ''; ?> />
              Child account
            </label>
            <div id="register-payout-child" class="ml-6 grid gap-3 sm:grid-cols-2 <?php echo $selectedPayoutMethod === 'child_account' ? '' : 'hidden'; ?>">
              <input name="childName" placeholder="Child name" class="rounded-md border-2 border-emerald-200 px-3 py-2" value="<?php echo htmlspecialchars($childNameValue); ?>" />
              <input name="childBankAccount" placeholder="Optional bank account" class="rounded-md border-2 border-emerald-200 px-3 py-2" value="<?php echo htmlspecialchars($childBankAccountValue); ?>" />
            </div>

            <label class="flex items-center gap-2">
              <input type="radio" name="payoutMethod" value="kiwisaver" <?php echo $selectedPayoutMethod === 'kiwisaver' ? 'checked' : ''; ?> />
              KiwiSaver
            </label>
            <div id="register-payout-kiwi" class="ml-6 grid gap-3 sm:grid-cols-2 <?php echo $selectedPayoutMethod === 'kiwisaver' ? '' : 'hidden'; ?>">
              <input name="kiwisaverProvider" placeholder="Provider" class="rounded-md border-2 border-emerald-200 px-3 py-2" value="<?php echo htmlspecialchars($kiwiProviderValue); ?>" />
              <input name="kiwisaverMemberId" placeholder="Member ID" class="rounded-md border-2 border-emerald-200 px-3 py-2" value="<?php echo htmlspecialchars($kiwiMemberValue); ?>" />
            </div>
          </div>
        </fieldset>

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

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const payoutRadios = document.querySelectorAll('input[name="payoutMethod"]');
    const bankSection = document.getElementById('register-payout-bank');
    const childSection = document.getElementById('register-payout-child');
    const kiwiSection = document.getElementById('register-payout-kiwi');

    function togglePayoutSections(method) {
      if (bankSection) {
        bankSection.classList.toggle('hidden', method !== 'bank');
      }
      if (childSection) {
        childSection.classList.toggle('hidden', method !== 'child_account');
      }
      if (kiwiSection) {
        kiwiSection.classList.toggle('hidden', method !== 'kiwisaver');
      }
    }

    payoutRadios.forEach(function (radio) {
      radio.addEventListener('change', function () {
        togglePayoutSections(this.value);
      });
    });

    const checked = document.querySelector('input[name="payoutMethod"]:checked');
    if (checked) {
      togglePayoutSections(checked.value);
    }
  });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

