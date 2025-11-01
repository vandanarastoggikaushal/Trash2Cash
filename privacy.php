<?php
$pageTitle = 'Privacy Policy';
$pageDescription = 'Plain-English privacy policy for New Zealand context.';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container prose py-10">
  <h1>Privacy Policy</h1>
  <p>We collect only what we need to schedule pickups and pay you. This includes your contact details, address, item estimates, and payout preferences.</p>
  <p>We store this data securely in New Zealand. We do not sell your data. You can request a copy or deletion by contacting us.</p>
  <p>For KiwiSaver or child payouts, we verify identity before transferring. We keep records as required by NZ law.</p>
  <p>Contact: <?php echo COMPANY_NAME; ?>, <?php echo CITY; ?>, New Zealand.</p>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

