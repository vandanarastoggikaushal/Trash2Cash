<?php
$pageTitle = 'Schedule Pickup';
$pageDescription = 'Request a pickup for aluminium cans and appliances in Wellington.';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-10">
  <h1 class="text-3xl font-bold">Schedule a Pickup</h1>
  <form id="pickup-form" class="mt-6 grid gap-8 lg:grid-cols-2">
    <div class="space-y-6">
      <fieldset class="rounded-lg border p-4">
        <legend class="px-1 text-sm font-semibold">Person</legend>
        <div class="grid gap-4 sm:grid-cols-2">
          <div>
            <label class="block text-sm font-medium" for="fullName">Full name</label>
            <input id="fullName" name="fullName" type="text" class="mt-1 w-full rounded-md border px-3 py-2" required />
          </div>
          <div>
            <label class="block text-sm font-medium" for="email">Email</label>
            <input id="email" name="email" type="email" class="mt-1 w-full rounded-md border px-3 py-2" required />
          </div>
          <div>
            <label class="block text-sm font-medium" for="phone">Phone</label>
            <input id="phone" name="phone" type="tel" class="mt-1 w-full rounded-md border px-3 py-2" required placeholder="e.g. 0212345678" />
          </div>
          <div class="sm:col-span-2">
            <label class="inline-flex items-center gap-2 text-sm">
              <input type="checkbox" name="marketingOptIn" id="marketingOptIn" />
              I'd like to receive updates
            </label>
          </div>
        </div>
      </fieldset>

      <fieldset class="rounded-lg border p-4">
        <legend class="px-1 text-sm font-semibold">Address</legend>
        <div class="grid gap-4 sm:grid-cols-2">
          <div class="sm:col-span-2">
            <label class="block text-sm font-medium" for="street">Street</label>
            <input id="street" name="street" type="text" class="mt-1 w-full rounded-md border px-3 py-2" required />
          </div>
          <div>
            <label class="block text-sm font-medium" for="suburb">Suburb</label>
            <input id="suburb" name="suburb" type="text" class="mt-1 w-full rounded-md border px-3 py-2" required />
          </div>
          <div>
            <label class="block text-sm font-medium" for="city">City</label>
            <input id="city" name="city" type="text" class="mt-1 w-full rounded-md border px-3 py-2" required value="<?php echo CITY; ?>" />
          </div>
          <div>
            <label class="block text-sm font-medium" for="postcode">Postcode</label>
            <input id="postcode" name="postcode" type="text" pattern="\d{4}" maxlength="4" class="mt-1 w-full rounded-md border px-3 py-2" required />
          </div>
          <div class="sm:col-span-2">
            <label class="block text-sm font-medium" for="accessNotes">Access notes</label>
            <input id="accessNotes" name="accessNotes" type="text" class="mt-1 w-full rounded-md border px-3 py-2" />
          </div>
        </div>
      </fieldset>

      <fieldset class="rounded-lg border p-4">
        <legend class="px-1 text-sm font-semibold">Pickup</legend>
        <div class="space-y-4">
          <div class="flex gap-4 text-sm">
            <label class="inline-flex items-center gap-2 rounded-md border px-3 py-2">
              <input type="radio" name="pickupType" value="cans" checked />
              Cans
            </label>
            <label class="inline-flex items-center gap-2 rounded-md border px-3 py-2">
              <input type="radio" name="pickupType" value="appliances" />
              Appliances
            </label>
            <label class="inline-flex items-center gap-2 rounded-md border px-3 py-2">
              <input type="radio" name="pickupType" value="both" />
              Both
            </label>
          </div>
          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <label class="block text-sm font-medium" for="cansEstimate">Cans estimate</label>
              <input id="cansEstimate" name="cansEstimate" type="number" min="0" class="mt-1 w-full rounded-md border px-3 py-2" value="0" />
            </div>
            <div>
              <label class="block text-sm font-medium">Appliances</label>
              <div class="mt-2 grid gap-2" id="appliances-list">
                <?php foreach ($APPLIANCE_CREDITS as $appliance): ?>
                  <div class="flex items-center justify-between gap-3">
                    <span class="text-sm"><?php echo htmlspecialchars($appliance['label']); ?></span>
                    <input type="number" min="0" name="appliances[<?php echo $appliance['slug']; ?>]" class="w-24 rounded-md border px-2 py-1 appliance-qty" data-slug="<?php echo $appliance['slug']; ?>" value="0" />
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium" for="preferredDate">Preferred date</label>
              <input id="preferredDate" name="preferredDate" type="date" class="mt-1 w-full rounded-md border px-3 py-2" />
            </div>
            <div>
              <label class="block text-sm font-medium" for="preferredWindow">Time window</label>
              <select id="preferredWindow" name="preferredWindow" class="mt-1 w-full rounded-md border px-3 py-2">
                <option value="">Select</option>
                <option value="Morning">Morning</option>
                <option value="Afternoon">Afternoon</option>
                <option value="Evening">Evening</option>
              </select>
            </div>
          </div>
        </div>
      </fieldset>

      <fieldset class="rounded-lg border p-4">
        <legend class="px-1 text-sm font-semibold">Payout preference</legend>
        <div class="space-y-3 text-sm">
          <label class="flex items-center gap-2">
            <input type="radio" name="payoutMethod" value="bank" checked />
            Bank
          </label>
          <div id="payout-bank" class="ml-6 grid gap-3 sm:grid-cols-2">
            <input name="bankName" placeholder="Bank name" class="rounded-md border px-3 py-2" />
            <input name="bankAccount" placeholder="Account number" class="rounded-md border px-3 py-2" />
          </div>
          <label class="flex items-center gap-2">
            <input type="radio" name="payoutMethod" value="child_account" />
            Child account
          </label>
          <div id="payout-child" class="ml-6 hidden grid gap-3 sm:grid-cols-2">
            <input name="childName" placeholder="Child name" class="rounded-md border px-3 py-2" />
            <input name="childBankAccount" placeholder="Optional bank account" class="rounded-md border px-3 py-2" />
          </div>
          <label class="flex items-center gap-2">
            <input type="radio" name="payoutMethod" value="kiwisaver" />
            KiwiSaver
          </label>
          <div id="payout-kiwisaver" class="ml-6 hidden grid gap-3 sm:grid-cols-2">
            <input name="kiwisaverProvider" placeholder="Provider" class="rounded-md border px-3 py-2" />
            <input name="kiwisaverMemberId" placeholder="Member ID" class="rounded-md border px-3 py-2" />
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
      <button class="btn" type="submit" id="submit-btn">Submit request</button>
    </div>
    <aside class="space-y-4">
      <div class="rounded-lg border bg-white p-4">
        <h2 class="font-semibold">Why Trash2Cash?</h2>
        <ul class="mt-2 list-disc pl-5 text-sm text-slate-700">
          <li>$1 per 50 aluminium cans—simple and transparent</li>
          <li>Kids' accounts & KiwiSaver options</li>
          <li>We handle the heavy lifting</li>
        </ul>
      </div>
    </aside>
  </form>
  <div id="pickup-success" class="mt-6 hidden rounded-lg bg-emerald-50 p-4 text-emerald-800">
    <h2 class="text-xl font-semibold">Thanks! Your request is in.</h2>
    <p class="mt-2">Reference ID: <span id="reference-id" class="font-mono font-semibold"></span></p>
    <p class="mt-4 text-sm">What happens next: we'll confirm a pickup window by email or SMS and handle the rest.</p>
  </div>
  <div id="pickup-error" class="mt-6 hidden rounded-lg bg-red-50 p-4 text-red-800">
    There was a problem submitting your request. Please try again or contact us directly.
  </div>
</div>

<script src="/assets/schedule-pickup.js"></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

