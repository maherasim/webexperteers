<?php
$socials = [
    'social_facebook' => ['bi-facebook', 'Facebook'],
    'social_instagram' => ['bi-instagram', 'Instagram'],
    'social_linkedin' => ['bi-linkedin', 'LinkedIn'],
    'social_twitter' => ['bi-twitter-x', 'X'],
    'social_youtube' => ['bi-youtube', 'YouTube'],
    'social_tiktok' => ['bi-tiktok', 'TikTok'],
    'social_github' => ['bi-github', 'GitHub'],
];
$footerPages    = rows('SELECT title, slug FROM pages WHERE is_active = 1 AND show_in_footer = 1 ORDER BY sort_order, id');
$footerServices = array_slice($navServices ?? active_rows('services'), 0, 6);
?>
  <footer class="footer">
    <div class="container">
      <div class="row g-4">
        <div class="col-lg-4">
          <a class="navbar-brand mb-3 d-inline-flex" href="<?= url() ?>"><?php require __DIR__ . '/logo.php'; ?></a>
          <p class="text-muted-2"><?= e(setting('footer_about')) ?></p>
          <div class="socials">
<?php foreach ($socials as $key => [$icon, $label]): if (setting($key) === '') continue; ?>
            <a href="<?= e(setting($key)) ?>" target="_blank" rel="noopener" aria-label="<?= e($label) ?>"><i class="bi <?= e($icon) ?>"></i></a>
<?php endforeach; ?>
          </div>
        </div>
        <div class="col-6 col-lg-2">
          <h5>Company</h5>
          <ul>
<?php if (on('show_about')): ?>            <li><a href="<?= url('about') ?>">About us</a></li><?php endif; ?>
<?php if (on('show_portfolio')): ?>            <li><a href="<?= url('portfolio') ?>">Portfolio</a></li><?php endif; ?>
<?php if (on('show_pricing')): ?>            <li><a href="<?= url('pricing') ?>">Pricing</a></li><?php endif; ?>
<?php if (on('show_faq')): ?>            <li><a href="<?= url('faq') ?>">FAQ</a></li><?php endif; ?>
            <li><a href="<?= url('contact') ?>">Contact</a></li>
<?php foreach ($footerPages as $p): ?>
            <li><a href="<?= url('page/' . rawurlencode($p['slug'])) ?>"><?= e($p['title']) ?></a></li>
<?php endforeach; ?>
          </ul>
        </div>
        <div class="col-6 col-lg-3">
          <h5>Services</h5>
          <ul>
<?php foreach ($footerServices as $s): ?>
            <li><a href="<?= url('services/' . $s['slug']) ?>"><?= e($s['title']) ?></a></li>
<?php endforeach; ?>
          </ul>
        </div>
<?php if (on('show_newsletter')): ?>
        <div class="col-lg-3">
          <h5><?= e(setting('newsletter_title')) ?></h5>
          <p class="text-muted-2 small"><?= e(setting('newsletter_text')) ?></p>
          <form class="newsletter" id="newsletterForm" action="<?= url('api/subscribe.php') ?>" method="post">
            <input type="email" name="email" class="form-control" placeholder="Your email" required aria-label="Email">
            <input type="text" name="website" class="hp-field" tabindex="-1" autocomplete="off" aria-hidden="true">
            <button class="btn btn-gradient" type="submit" aria-label="Subscribe"><i class="bi bi-arrow-right"></i></button>
          </form>
          <p class="small mt-2 mb-0 form-note" id="newsletterStatus" role="status"></p>
<?php if (setting('contact_email') !== ''): ?>
          <p class="small text-muted-2 mt-3 mb-1"><i class="bi bi-envelope me-2"></i><a class="footer-contact" href="mailto:<?= e(setting('contact_email')) ?>"><?= e(setting('contact_email')) ?></a></p>
<?php endif; ?>
<?php if (setting('whatsapp_number') !== ''): ?>
          <p class="small text-muted-2 mb-0"><i class="bi bi-whatsapp me-2"></i><a class="footer-contact" href="<?= e(wa_link(setting('whatsapp_message'))) ?>" target="_blank" rel="noopener"><?= e(setting('whatsapp_display', setting('whatsapp_number'))) ?></a></p>
<?php endif; ?>
        </div>
<?php endif; ?>
      </div>
      <hr>
      <div class="d-flex flex-column flex-md-row justify-content-between small text-muted-2 gap-2">
        <span>&copy; <?= date('Y') ?> <?= e(setting('footer_copyright')) ?></span>
        <span><?= e(setting('footer_credit')) ?></span>
      </div>
    </div>
  </footer>

<?php if (on('whatsapp_float') && setting('whatsapp_number') !== ''): ?>
  <a href="<?= e(wa_link(setting('whatsapp_message'))) ?>" class="whatsapp-float" target="_blank" rel="noopener" aria-label="Chat on WhatsApp"><i class="bi bi-whatsapp"></i></a>
<?php endif; ?>
  <a href="#top" class="back-to-top" aria-label="Back to top"><i class="bi bi-arrow-up"></i></a>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
  <script src="<?= url('assets/js/main.js?v=3') ?>"></script>
</body>
</html>
