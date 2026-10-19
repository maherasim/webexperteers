<?php
/** @var array $items  @var bool $alt  @var bool $head */
$alt  = $alt ?? false;
$head = $head ?? true;
if (!$items) return;
?>
  <section class="section<?= $alt ? ' section-alt' : '' ?>">
    <div class="container">
<?php if ($head): ?>
      <div class="section-head text-center" data-aos="fade-up">
        <span class="eyebrow"><?= e(setting('pricing_eyebrow')) ?></span>
        <h2 class="display-6 fw-bold mt-2"><?= hl(setting('pricing_title')) ?></h2>
        <p class="text-muted-2"><?= e(setting('pricing_subtitle')) ?></p>
      </div>
<?php endif; ?>
      <div class="row g-4 justify-content-center">
<?php foreach ($items as $i => $pl): ?>
        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= ($i % 3) * 100 ?>">
          <div class="price-card<?= $pl['is_featured'] ? ' featured' : '' ?>">
<?php if ($pl['is_featured'] && $pl['badge_text']): ?><span class="badge-pop"><?= e($pl['badge_text']) ?></span><?php endif; ?>
            <h4><?= e($pl['name']) ?></h4>
            <p class="text-muted-2"><?= e($pl['subtitle']) ?></p>
            <div class="price"><?= e($pl['price']) ?><?php if ($pl['period']): ?><small><?= e($pl['period']) ?></small><?php endif; ?></div>
            <ul>
<?php foreach (lines($pl['features']) as $f): ?>
              <li><i class="bi bi-check2"></i> <?= e($f) ?></li>
<?php endforeach; ?>
            </ul>
            <a href="<?= e(href($pl['button_url'])) ?>" class="btn <?= $pl['is_featured'] ? 'btn-gradient' : 'btn-outline-light' ?> w-100"><?= e($pl['button_text'] ?: 'Get Started') ?></a>
          </div>
        </div>
<?php endforeach; ?>
      </div>
    </div>
  </section>
