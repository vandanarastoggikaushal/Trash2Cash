  </main>
  <footer class="mt-16 border-t-2 border-emerald-200 bg-gradient-to-br from-slate-50 to-emerald-50/30">
    <div class="container grid gap-8 py-12 sm:grid-cols-2 lg:grid-cols-4">
      <div>
        <div class="flex items-center gap-2 mb-4">
          <span class="text-2xl">♻️</span>
          <h3 class="text-xl font-bold gradient-text"><?php echo COMPANY_NAME; ?></h3>
        </div>
        <p class="mt-2 text-sm text-slate-700 flex items-center gap-2">
          <span>📍</span> Based in <?php echo CITY; ?>, New Zealand
        </p>
        <p class="mt-2 text-sm text-slate-700 flex items-center gap-2">
          <span>📞</span> <?php echo SUPPORT_PHONE; ?>
        </p>
        <p class="mt-2 text-sm flex items-center gap-2">
          <span>✉️</span> <a href="mailto:<?php echo SUPPORT_EMAIL; ?>" class="text-brand hover:underline font-semibold"><?php echo SUPPORT_EMAIL; ?></a>
        </p>
      </div>
      <div>
        <h3 class="text-lg font-bold text-slate-900 mb-4">Company</h3>
        <ul class="space-y-3 text-sm">
          <li><a href="/how-it-works.php" class="text-slate-700 hover:text-brand font-semibold transition-colors flex items-center gap-2">📖 How it Works</a></li>
          <li><a href="/rewards.php" class="text-slate-700 hover:text-brand font-semibold transition-colors flex items-center gap-2">💰 Rewards</a></li>
          <li><a href="/partners.php" class="text-slate-700 hover:text-brand font-semibold transition-colors flex items-center gap-2">🤝 Partners</a></li>
        </ul>
      </div>
      <div>
        <h3 class="text-lg font-bold text-slate-900 mb-4">Support</h3>
        <ul class="space-y-3 text-sm">
          <li><a href="/faq.php" class="text-slate-700 hover:text-brand font-semibold transition-colors flex items-center gap-2">❓ FAQ</a></li>
          <li><a href="/contact.php" class="text-slate-700 hover:text-brand font-semibold transition-colors flex items-center gap-2">📧 Contact</a></li>
        </ul>
      </div>
      <div>
        <h3 class="text-lg font-bold text-slate-900 mb-4">Legal</h3>
        <ul class="space-y-3 text-sm">
          <li><a href="/terms.php" class="text-slate-700 hover:text-brand font-semibold transition-colors flex items-center gap-2">📄 Terms</a></li>
          <li><a href="/privacy.php" class="text-slate-700 hover:text-brand font-semibold transition-colors flex items-center gap-2">🔒 Privacy</a></li>
        </ul>
      </div>
    </div>
    <div class="border-t-2 border-emerald-200 py-6 text-center">
      <p class="text-sm text-slate-700 font-semibold">
        © <?php echo date('Y'); ?> <?php echo COMPANY_NAME; ?>. All rights reserved. ♻️
      </p>
    </div>
  </footer>
  <script src="/assets/main.js"></script>
</body>
</html>

