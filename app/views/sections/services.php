<?php
/** @var array $items  @var bool $alt  @var bool $head  @var bool $more */
$alt  = $alt ?? false;
$head = $head ?? true;
$more = $more ?? false;
if (!$items) return;
?>
  <section class="section<?= $alt ? ' section-alt' : '' ?>">
    <div class="container">
<?php if ($head): ?>
      <div class="section-head text-center" data-aos="fade-up">
        <span class="eyebrow"><?= e(setting('services_eyebrow')) ?></span>
        <h2 class="display-6 fw-bold mt-2"><?= hl(setting('services_title')) ?></h2>
        <p class="text-muted-2"><?= e(setting('services_subtitle')) ?></p>
      </div>
<?php endif; ?>
      <div class="row g-4">
<?php foreach ($items as $i => $s): $link = url('services/' . $s['slug']); ?>
        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= ($i % 3) * 100 ?>">
          <div class="service-card<?= !empty($s['image']) ? ' has-image' : '' ?>">
<?php if (!empty($s['image'])): ?>
            <div class="service-img"><img src="<?= e(media($s['image'])) ?>" alt="<?= e($s['title']) ?>" loading="lazy"></div>
<?php endif; ?>
            <div class="icon-box"><i class="bi <?= e($s['icon']) ?>"></i></div>
            <h3><a href="<?= $link ?>" class="stretched-link text-reset"><?= e($s['title']) ?></a></h3>
            <p><?= e($s['description']) ?></p>
            <span class="link-arrow"><?= e($s['link_text'] ?: 'Learn more') ?> <i class="bi bi-arrow-right"></i></span>
          </div>
        </div>
<?php endforeach; ?>
      </div>
<?php if ($more): ?>
      <div class="text-center mt-5" data-aos="fade-up">
        <a href="<?= url('services') ?>" class="btn btn-outline-light btn-lg px-5"><?= e(setting('services_more_text', 'View all services')) ?> <i class="bi bi-arrow-right ms-1"></i></a>
      </div>
<?php endif; ?>
    </div>
  </section>
