<?php
$pageTitle = 'Schedule Pickup';
$pageDescription = 'Schedule a free door-to-door pickup for your aluminium cans and old appliances in Wellington. Earn $1 per 100 cans and appliance credits.';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin('/schedule-pickup.php');
$user = getCurrentUser();
if (!$user) {
    header('Location: /login.php');
    exit;
}

$firstName = trim($user['firstName'] ?? '');
$lastName = trim($user['lastName'] ?? '');
$fullName = trim(($firstName . ' ' . $lastName)) ?: ($user['username'] ?? '');
$userEmail = $user['email'] ?? '';
$userPhone = $user['phone'] ?? '';
$marketingOptIn = !empty($user['marketingOptIn']);
$payoutMethod = $user['payoutMethod'] ?? 'bank';
$payoutBankName = $user['payoutBankName'] ?? '';
$payoutBankAccount = $user['payoutBankAccount'] ?? '';
$payoutChildName = $user['payoutChildName'] ?? '';
$payoutChildBankAccount = $user['payoutChildBankAccount'] ?? '';
$payoutKiwisaverProvider = $user['payoutKiwisaverProvider'] ?? '';
$payoutKiwisaverMemberId = $user['payoutKiwisaverMemberId'] ?? '';
$profileIssues = [];
if ($userPhone === '') {
    $profileIssues[] = 'phone number';
}
if ($payoutMethod === 'bank' && ($payoutBankName === '' || $payoutBankAccount === '')) {
    $profileIssues[] = 'bank payout details';
}
if ($payoutMethod === 'child_account' && $payoutChildName === '') {
    $profileIssues[] = 'child account details';
}
if ($payoutMethod === 'kiwisaver' && ($payoutKiwisaverProvider === '' || $payoutKiwisaverMemberId === '')) {
    $profileIssues[] = 'KiwiSaver payout details';
}
$profileIncomplete = !empty($profileIssues);

$streetDefault = '';
$suburbDefault = '';
$cityDefault = CITY;
$postcodeDefault = '';
if (!empty($user['address'])) {
    $addressLines = preg_split('/\r\n|\r|\n/', $user['address']);
    $streetDefault = trim($addressLines[0] ?? '');
    $suburbDefault = trim($addressLines[1] ?? '');
    $cityLine = trim($addressLines[2] ?? '');
    if ($cityLine !== '') {
        if (preg_match('/(.+)\s+(\d{4})$/', $cityLine, $matches)) {
            $cityDefault = trim($matches[1]);
            $postcodeDefault = trim($matches[2]);
        } else {
            $cityDefault = $cityLine;
        }
    }
}
if ($postcodeDefault === '' && preg_match('/\b(\d{4})\b/', $user['address'] ?? '', $firstPostcodeMatch)) {
    $postcodeDefault = trim($firstPostcodeMatch[1]);
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-16">
  <div class="text-center mb-12">
    <h1 class="text-5xl font-extrabold text-slate-900 mb-4">
      <span class="gradient-text">📅 Schedule a Pickup</span>
    </h1>
    <p class="text-xl text-slate-600 max-w-2xl mx-auto">Let's turn your recyclables into cash or savings!</p>
  </div>
  
  <form id="pickup-form" class="grid gap-8 lg:grid-cols-2">
    <input type="hidden" id="fullName" name="fullName" value="<?php echo htmlspecialchars($fullName); ?>">
    <input type="hidden" id="email" name="email" value="<?php echo htmlspecialchars($userEmail); ?>">
    <input type="hidden" id="phone" name="phone" value="<?php echo htmlspecialchars($userPhone); ?>">
    <?php if ($marketingOptIn): ?>
    <input type="hidden" name="marketingOptIn" value="on">
    <?php endif; ?>
    <input type="hidden" name="payoutMethod" value="<?php echo htmlspecialchars($payoutMethod); ?>">
    <input type="hidden" name="bankName" value="<?php echo htmlspecialchars($payoutBankName); ?>">
    <input type="hidden" name="bankAccount" value="<?php echo htmlspecialchars($payoutBankAccount); ?>">
    <input type="hidden" name="childName" value="<?php echo htmlspecialchars($payoutChildName); ?>">
    <input type="hidden" name="childBankAccount" value="<?php echo htmlspecialchars($payoutChildBankAccount); ?>">
    <input type="hidden" name="kiwisaverProvider" value="<?php echo htmlspecialchars($payoutKiwisaverProvider); ?>">
    <input type="hidden" name="kiwisaverMemberId" value="<?php echo htmlspecialchars($payoutKiwisaverMemberId); ?>">
    <div class="space-y-6">
      <div class="rounded-xl border-2 border-emerald-100 bg-gradient-to-br from-white to-emerald-50/40 p-6 shadow-md">
        <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
          <span>👤</span> Your Details
        </h2>
        <dl class="mt-4 space-y-2 text-sm text-slate-700">
          <div class="flex items-start justify-between gap-4">
            <dt class="font-semibold text-slate-600">Name</dt>
            <dd class="text-right font-semibold text-slate-900"><?php echo htmlspecialchars(strtoupper($fullName)); ?></dd>
          </div>
          <div class="flex items-start justify-between gap-4">
            <dt class="font-semibold text-slate-600">Email</dt>
            <dd class="text-right"><?php echo htmlspecialchars($userEmail); ?></dd>
          </div>
          <div class="flex items-start justify-between gap-4">
            <dt class="font-semibold text-slate-600">Phone</dt>
            <dd class="text-right"><?php echo htmlspecialchars($userPhone); ?></dd>
          </div>
          <div class="flex items-start justify-between gap-4">
            <dt class="font-semibold text-slate-600">Payout</dt>
            <dd class="text-right">
              <?php
                $payoutLabelMap = [
                  'bank' => 'Bank account',
                  'child_account' => 'Child account',
                  'kiwisaver' => 'KiwiSaver'
                ];
                echo htmlspecialchars($payoutLabelMap[$payoutMethod] ?? ucfirst($payoutMethod));
              ?>
            </dd>
          </div>
        </dl>
        <?php if ($profileIncomplete): ?>
        <div class="mt-4 rounded-lg border border-amber-300 bg-amber-50 px-4 py-3 text-xs text-amber-800">
          Some account details are missing (<?php echo htmlspecialchars(implode(', ', $profileIssues)); ?>). Please contact us at
          <a class="text-amber-900 font-semibold" href="mailto:<?php echo SUPPORT_EMAIL; ?>"><?php echo SUPPORT_EMAIL; ?></a>
          before scheduling a pickup.
        </div>
        <?php else: ?>
        <div class="mt-4 text-xs text-slate-500">
          Need to update these details? Contact us at <a class="text-brand font-semibold" href="mailto:<?php echo SUPPORT_EMAIL; ?>"><?php echo SUPPORT_EMAIL; ?></a>.
        </div>
        <?php endif; ?>
      </div>

      <fieldset class="rounded-xl border-2 border-emerald-100 bg-gradient-to-br from-white to-emerald-50/30 p-6 shadow-md">
        <legend class="px-3 text-base font-bold text-slate-900 flex items-center gap-2">
          <span>📍</span> Address
        </legend>
        <div class="grid gap-4 sm:grid-cols-2">
          <?php if (defined('ENABLE_ADDRESS_SEARCH') && ENABLE_ADDRESS_SEARCH): ?>
          <div class="sm:col-span-2">
            <label class="block text-sm font-medium" for="address-search">🔍 Search Address</label>
            <div class="relative">
              <input 
                id="address-search" 
                type="text" 
                class="mt-1 w-full rounded-md border px-3 py-2" 
                placeholder="Start typing your address..."
                autocomplete="off"
              />
              <div id="address-suggestions" class="hidden absolute z-50 w-full mt-1 bg-white border rounded-md shadow-lg max-h-60 overflow-y-auto"></div>
            </div>
            <p class="mt-1 text-xs text-slate-600">Search for your address and select from suggestions</p>
          </div>
          <?php endif; ?>
          <div class="sm:col-span-2">
            <label class="block text-sm font-medium" for="street">Street <span class="text-xs font-normal text-slate-500">(read-only)</span></label>
            <input id="street" name="street" type="text" class="mt-1 w-full rounded-md border px-3 py-2 bg-slate-100 cursor-not-allowed" required value="<?php echo htmlspecialchars($streetDefault); ?>" readonly />
          </div>
          <div>
            <label class="block text-sm font-medium" for="suburb">Suburb <span class="text-xs font-normal text-slate-500">(read-only)</span></label>
            <input id="suburb" name="suburb" type="text" class="mt-1 w-full rounded-md border px-3 py-2 bg-slate-100 cursor-not-allowed" required value="<?php echo htmlspecialchars($suburbDefault); ?>" readonly />
          </div>
          <div>
            <label class="block text-sm font-medium" for="city">City <span class="text-xs font-normal text-slate-500">(read-only)</span></label>
            <input id="city" name="city" type="text" class="mt-1 w-full rounded-md border px-3 py-2 bg-slate-100 cursor-not-allowed" required value="<?php echo htmlspecialchars($cityDefault); ?>" readonly />
          </div>
          <div>
            <label class="block text-sm font-medium" for="postcode">Postcode <span class="text-xs font-normal text-slate-500">(read-only)</span></label>
            <input id="postcode" name="postcode" type="text" pattern="\d{4}" maxlength="4" class="mt-1 w-full rounded-md border px-3 py-2 bg-slate-100 cursor-not-allowed" required value="<?php echo htmlspecialchars($postcodeDefault); ?>" readonly />
          </div>
          <div class="sm:col-span-2">
            <label class="block text-sm font-medium" for="accessNotes">Access notes</label>
            <input id="accessNotes" name="accessNotes" type="text" class="mt-1 w-full rounded-md border px-3 py-2" />
          </div>
        </div>
      </fieldset>

      <fieldset class="rounded-xl border-2 border-emerald-100 bg-gradient-to-br from-white to-emerald-50/30 p-6 shadow-md">
        <legend class="px-3 text-base font-bold text-slate-900 flex items-center gap-2">
          <span>♻️</span> Pickup
        </legend>
        <div class="space-y-4">
          <div class="flex gap-4 text-sm">
            <label class="inline-flex items-center gap-2 rounded-lg border-2 border-emerald-200 px-4 py-2 cursor-pointer hover:bg-emerald-50 transition-all pickup-type-label" data-type="cans">
              <input type="radio" name="pickupType" value="cans" checked class="cursor-pointer" />
              <span class="font-semibold">🥤 Cans Only</span>
            </label>
            <label class="inline-flex items-center gap-2 rounded-lg border-2 border-blue-200 px-4 py-2 cursor-pointer hover:bg-blue-50 transition-all pickup-type-label" data-type="appliances">
              <input type="radio" name="pickupType" value="appliances" class="cursor-pointer" />
              <span class="font-semibold">🔧 Appliances Only</span>
            </label>
            <label class="inline-flex items-center gap-2 rounded-lg border-2 border-purple-200 px-4 py-2 cursor-pointer hover:bg-purple-50 transition-all pickup-type-label" data-type="both">
              <input type="radio" name="pickupType" value="both" class="cursor-pointer" />
              <span class="font-semibold">♻️ Both</span>
            </label>
          </div>
          <div class="space-y-4">
            <div id="cans-section" class="p-4 rounded-lg bg-emerald-50/50 border-2 border-emerald-200 transition-all">
              <label class="block text-sm font-semibold text-slate-900 mb-2" for="cansEstimate">
                🥤 Number of Cans (estimate)
              </label>
              <div class="flex items-center gap-4">
                <input id="cansEstimate" name="cansEstimate" type="number" min="0" step="10" class="flex-1 rounded-lg border-2 border-emerald-200 px-4 py-3 focus:border-brand focus:ring-2 focus:ring-emerald-200 transition-all" value="0" placeholder="Enter number of cans" />
                <div class="text-sm">
                  <span class="font-semibold text-slate-700">Est. reward:</span>
                  <span id="cans-reward-estimate" class="ml-2 text-lg font-bold text-brand">$0</span>
                </div>
              </div>
              <p class="mt-2 text-xs text-slate-600">
                💡 $1 per 100 cans • Example: 500 cans = $5
              </p>
            </div>
            
            <div id="appliances-section" class="p-4 rounded-lg bg-blue-50/50 border-2 border-blue-200 transition-all" style="display: none;">
              <label class="block text-sm font-semibold text-slate-900 mb-3">
                🔧 Appliances (quantity per type)
              </label>
              <div class="grid gap-3 sm:grid-cols-2" id="appliances-list">
                <?php foreach ($APPLIANCE_CREDITS as $appliance): ?>
                  <div class="flex items-center justify-between gap-3 p-2 rounded-lg bg-white border border-blue-100">
                    <span class="text-sm font-medium text-slate-700"><?php echo htmlspecialchars($appliance['label']); ?></span>
                    <div class="flex items-center gap-2">
                      <input type="number" min="0" name="appliances[<?php echo $appliance['slug']; ?>]" class="w-20 rounded-md border-2 border-blue-200 px-2 py-1 text-center appliance-qty focus:border-brand" data-slug="<?php echo $appliance['slug']; ?>" data-credit="<?php echo $appliance['credit']; ?>" value="0" />
                      <span class="text-xs text-slate-500">$<?php echo $appliance['credit']; ?></span>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
              <div class="mt-3 pt-3 border-t border-blue-200">
                <span class="text-sm font-semibold text-slate-700">Total appliance credit:</span>
                <span id="appliances-reward-estimate" class="ml-2 text-lg font-bold text-blue-600">$0</span>
              </div>
            </div>
            
            <div class="grid gap-4 sm:grid-cols-2">
              <div>
                <label class="block text-sm font-semibold text-slate-900 mb-2" for="preferredDate">📅 Preferred date</label>
                <input id="preferredDate" name="preferredDate" type="date" class="w-full rounded-lg border-2 border-emerald-200 px-4 py-3 focus:border-brand focus:ring-2 focus:ring-emerald-200 transition-all" />
              </div>
              <div>
                <label class="block text-sm font-semibold text-slate-900 mb-2" for="preferredWindow">🕐 Time window</label>
                <select id="preferredWindow" name="preferredWindow" class="w-full rounded-lg border-2 border-emerald-200 px-4 py-3 focus:border-brand focus:ring-2 focus:ring-emerald-200 transition-all">
                  <option value="">Select</option>
                  <option value="Morning">Morning</option>
                  <option value="Afternoon">Afternoon</option>
                  <option value="Evening">Evening</option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </fieldset>

      <div class="space-y-2 text-sm">
        <label class="flex items-center gap-2">
          <input type="checkbox" name="itemsAreClean" id="itemsAreClean" required />
          Items are clean and safe to handle.
        </label>
        <label class="flex items-center gap-2">
          <input type="checkbox" name="acceptedTerms" id="acceptedTerms" required />
          I accept the terms.
        </label>
      </div>
      <button class="w-full btn text-lg px-8 py-4 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all font-bold" type="submit" id="submit-btn">
        ✨ Submit Request
      </button>
    </div>
    <aside class="space-y-4">
      <div class="rounded-2xl border-2 border-emerald-200 bg-gradient-to-br from-emerald-50 to-green-50 p-6 shadow-xl">
        <div class="flex items-center gap-3 mb-4">
          <span class="text-4xl">🌟</span>
          <h2 class="text-2xl font-bold text-slate-900">Why Trash2Cash?</h2>
        </div>
        <ul class="space-y-3 text-slate-700">
          <li class="flex items-start gap-3">
            <span class="text-brand text-xl">✓</span>
            <span class="font-semibold">$1 per 100 aluminium cans—simple and transparent</span>
          </li>
          <li class="flex items-start gap-3">
            <span class="text-brand text-xl">✓</span>
            <span class="font-semibold">Kids' accounts & KiwiSaver options</span>
          </li>
          <li class="flex items-start gap-3">
            <span class="text-brand text-xl">✓</span>
            <span class="font-semibold">We handle the heavy lifting</span>
          </li>
        </ul>
      </div>
    </aside>
  </form>
  <div id="pickup-success" class="mt-6 hidden rounded-2xl bg-gradient-to-r from-emerald-100 to-green-100 border-2 border-brand p-6 text-emerald-900 shadow-xl">
    <div class="flex items-center gap-3 mb-3">
      <span class="text-3xl">✅</span>
      <h2 class="text-2xl font-bold">Thanks! Your request is in.</h2>
    </div>
    <p class="mt-2 font-semibold">Reference ID: <span id="reference-id" class="font-mono text-lg bg-white px-2 py-1 rounded">-</span></p>
    <p class="mt-4 text-slate-800">What happens next: we'll confirm a pickup window by email or SMS and handle the rest.</p>
  </div>
  <div id="pickup-error" class="mt-6 hidden rounded-2xl bg-gradient-to-r from-red-100 to-pink-100 border-2 border-red-300 p-6 text-red-900 shadow-xl">
    <div class="flex items-center gap-3">
      <span class="text-3xl">❌</span>
      <p class="font-semibold">There was a problem submitting your request. Please try again or contact us directly.</p>
    </div>
  </div>
</div>

<script src="/assets/schedule-pickup.js"></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

