<?php
/** @var array $items  @var bool $alt */
$alt = $alt ?? false;
if (!$items) return;
?>
  <section class="section<?= $alt ? ' section-alt' : '' ?>">
    <div class="container">
      <div class="row g-5">
        <div class="col-lg-5" data-aos="fade-right">
          <span class="eyebrow"><?= e(setting('faq_eyebrow')) ?></span>
          <h2 class="display-6 fw-bold mt-2"><?= hl(setting('faq_title')) ?></h2>
          <p class="text-muted-2 mt-3"><?= e(setting('faq_text')) ?></p>
<?php if (setting('faq_btn_text') !== ''): ?>
          <a href="<?= url('contact') ?>" class="btn btn-gradient mt-2 px-4"><?= e(setting('faq_btn_text')) ?></a>
<?php endif; ?>
        </div>
        <div class="col-lg-7" data-aos="fade-left">
          <div class="accordion" id="faqAccordion">
<?php foreach ($items as $i => $f): $open = $i === 0; ?>
            <div class="accordion-item">
              <h3 class="accordion-header"><button class="accordion-button<?= $open ? '' : ' collapsed' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#faq<?= (int) $f['id'] ?>" aria-expanded="<?= $open ? 'true' : 'false' ?>"><?= e($f['question']) ?></button></h3>
              <div id="faq<?= (int) $f['id'] ?>" class="accordion-collapse collapse<?= $open ? ' show' : '' ?>" data-bs-parent="#faqAccordion"><div class="accordion-body"><?= nl2br(e($f['answer'])) ?></div></div>
            </div>
<?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </section>
