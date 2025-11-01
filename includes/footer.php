  </main>
  <footer class="mt-16 border-t bg-slate-50">
    <div class="container grid gap-6 py-10 sm:grid-cols-2 lg:grid-cols-4">
      <div>
        <h3 class="font-semibold"><?php echo COMPANY_NAME; ?></h3>
        <p class="mt-2 text-sm text-slate-600">Based in <?php echo CITY; ?>, New Zealand</p>
        <p class="mt-2 text-sm"><?php echo SUPPORT_PHONE; ?></p>
        <p class="text-sm"><a href="mailto:<?php echo SUPPORT_EMAIL; ?>" class="underline"><?php echo SUPPORT_EMAIL; ?></a></p>
      </div>
      <div>
        <h3 class="font-semibold">Company</h3>
        <ul class="mt-2 space-y-2 text-sm">
          <li><a href="/how-it-works.php" class="hover:underline">How it Works</a></li>
          <li><a href="/rewards.php" class="hover:underline">Rewards</a></li>
          <li><a href="/partners.php" class="hover:underline">Partners</a></li>
        </ul>
      </div>
      <div>
        <h3 class="font-semibold">Support</h3>
        <ul class="mt-2 space-y-2 text-sm">
          <li><a href="/faq.php" class="hover:underline">FAQ</a></li>
          <li><a href="/contact.php" class="hover:underline">Contact</a></li>
        </ul>
      </div>
      <div>
        <h3 class="font-semibold">Legal</h3>
        <ul class="mt-2 space-y-2 text-sm">
          <li><a href="/terms.php" class="hover:underline">Terms</a></li>
          <li><a href="/privacy.php" class="hover:underline">Privacy</a></li>
        </ul>
      </div>
    </div>
    <div class="border-t py-4 text-center text-xs text-slate-500">
      © <?php echo date('Y'); ?> <?php echo COMPANY_NAME; ?>. All rights reserved.
    </div>
  </footer>
  <script src="/assets/main.js"></script>
</body>
</html>

