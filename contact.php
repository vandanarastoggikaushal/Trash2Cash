<?php
$pageTitle = 'Contact';
$pageDescription = 'Get in touch with our team in Wellington.';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-10">
  <h1 class="text-3xl font-bold">Contact</h1>
  <form id="contact-form" class="mt-6 max-w-xl space-y-4">
    <div>
      <label class="block text-sm font-medium" for="name">Name</label>
      <input id="name" name="name" type="text" class="mt-1 w-full rounded-md border px-3 py-2" required />
    </div>
    <div>
      <label class="block text-sm font-medium" for="email">Email</label>
      <input id="email" name="email" type="email" class="mt-1 w-full rounded-md border px-3 py-2" required />
    </div>
    <div>
      <label class="block text-sm font-medium" for="message">Message</label>
      <textarea id="message" name="message" rows="6" class="mt-1 w-full rounded-md border px-3 py-2" required></textarea>
    </div>
    <button class="btn" type="submit" id="submit-btn">Send Message</button>
  </form>
  <div id="contact-success" class="mt-4 hidden rounded-lg bg-emerald-50 p-4 text-emerald-800">
    Message sent successfully! We'll get back to you soon.
  </div>
  <div id="contact-error" class="mt-4 hidden rounded-lg bg-red-50 p-4 text-red-800">
    Something went wrong. Please try again or email us directly.
  </div>
</div>

<script src="/assets/contact.js"></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

