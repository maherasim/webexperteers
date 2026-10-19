<?php if (!on('show_cta')) return; ?>
  <section class="cta-section">
    <div class="container">
      <div class="cta-box" data-aos="zoom-in">
        <h2 class="display-6 fw-bold"><?= e(setting('cta_title')) ?></h2>
        <p><?= e(setting('cta_text')) ?></p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
          <a href="<?= url('contact') ?>" class="btn btn-light btn-lg px-5 fw-semibold"><?= e(setting('cta_btn_text')) ?></a>
<?php if (setting('whatsapp_number') !== ''): ?>
          <a href="<?= e(wa_link(setting('whatsapp_message'))) ?>" target="_blank" rel="noopener" class="btn btn-outline-light btn-lg px-4"><i class="bi bi-whatsapp me-1"></i> WhatsApp us</a>
<?php endif; ?>
        </div>
      </div>
    </div>
  </section>
