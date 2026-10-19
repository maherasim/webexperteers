<?php
/** @var bool $alt  @var bool $more  (true on the homepage: button links to /about) */
$alt  = $alt ?? false;
$more = $more ?? false;
?>
  <section class="section<?= $alt ? ' section-alt' : '' ?>">
    <div class="container">
      <div class="row align-items-center g-5">
        <div class="col-lg-6" data-aos="fade-right">
          <div class="about-visual">
            <div class="about-card big">
              <i class="bi <?= e(setting('about_card1_icon')) ?>"></i>
              <h4><?= e(setting('about_card1_title')) ?></h4>
              <p><?= e(setting('about_card1_text')) ?></p>
            </div>
            <div class="about-card">
              <i class="bi <?= e(setting('about_card2_icon')) ?>"></i>
              <h4><?= e(setting('about_card2_title')) ?></h4>
            </div>
            <div class="about-card">
              <i class="bi <?= e(setting('about_card3_icon')) ?>"></i>
              <h4><?= e(setting('about_card3_title')) ?></h4>
            </div>
          </div>
        </div>
        <div class="col-lg-6" data-aos="fade-left">
          <span class="eyebrow"><?= e(setting('about_eyebrow')) ?></span>
          <h2 class="display-6 fw-bold mt-2"><?= hl(setting('about_title')) ?></h2>
          <p class="text-muted-2 mt-3"><?= e(setting('about_text')) ?></p>
          <ul class="check-list mt-4">
<?php foreach (lines(setting('about_points')) as $point): ?>
            <li><i class="bi bi-check-circle-fill"></i> <?= e($point) ?></li>
<?php endforeach; ?>
          </ul>
<?php if ($more): ?>
          <a href="<?= url('about') ?>" class="btn btn-gradient mt-3 px-4"><?= e(setting('about_more_text', 'More about us')) ?> <i class="bi bi-arrow-right ms-1"></i></a>
<?php elseif (setting('about_btn_text') !== ''): ?>
          <a href="<?= url('contact') ?>" class="btn btn-gradient mt-3 px-4"><?= e(setting('about_btn_text')) ?></a>
<?php endif; ?>
        </div>
      </div>
    </div>
  </section>
