<?php
/** @var array $items  @var bool $alt */
$alt = $alt ?? false;
if (!$items) return;
?>
  <section class="section<?= $alt ? ' section-alt' : '' ?>">
    <div class="container">
      <div class="section-head text-center" data-aos="fade-up">
        <span class="eyebrow"><?= e(setting('process_eyebrow')) ?></span>
        <h2 class="display-6 fw-bold mt-2"><?= hl(setting('process_title')) ?></h2>
      </div>
      <div class="row g-4 process justify-content-center">
<?php foreach ($items as $i => $st): ?>
        <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="<?= ($i % 4) * 100 ?>">
          <div class="step"><span class="step-num"><?= sprintf('%02d', $i + 1) ?></span><i class="bi <?= e($st['icon']) ?>"></i><h4><?= e($st['title']) ?></h4><p><?= e($st['description']) ?></p></div>
        </div>
<?php endforeach; ?>
      </div>
    </div>
  </section>
