<?php
/** @var array $items  @var bool $alt  @var bool $head  @var bool $filters  @var bool $more */
$alt     = $alt ?? false;
$head    = $head ?? true;
$filters = $filters ?? false;
$more    = $more ?? false;
if (!$items) return;

$categories = [];
foreach ($items as $p) {
    if (trim((string) $p['category']) !== '') {
        $categories[slugify($p['category'])] = $p['category'];
    }
}
?>
  <section class="section<?= $alt ? ' section-alt' : '' ?>">
    <div class="container">
<?php if ($head): ?>
      <div class="section-head text-center" data-aos="fade-up">
        <span class="eyebrow"><?= e(setting('portfolio_eyebrow')) ?></span>
        <h2 class="display-6 fw-bold mt-2"><?= hl(setting('portfolio_title')) ?></h2>
        <p class="text-muted-2"><?= e(setting('portfolio_subtitle')) ?></p>
      </div>
<?php endif; ?>

<?php if ($filters && count($categories) > 1): ?>
      <div class="portfolio-filters text-center mb-5" data-aos="fade-up">
        <button class="active" data-filter="all">All</button>
<?php foreach ($categories as $slug => $label): ?>
        <button data-filter="<?= e($slug) ?>"><?= e($label) ?></button>
<?php endforeach; ?>
      </div>
<?php endif; ?>

      <div class="row g-4 portfolio-grid">
<?php foreach ($items as $i => $p): ?>
        <div class="col-md-6 col-lg-4 portfolio-item" data-category="<?= e(slugify((string) $p['category'])) ?>" data-aos="fade-up" data-aos-delay="<?= ($i % 3) * 100 ?>">
          <div class="project-card">
            <div class="project-thumb <?= e($p['color'] ?: 'g-2') ?>">
<?php if ($p['image']): ?>
              <img src="<?= e(media($p['image'])) ?>" alt="<?= e($p['title']) ?>" loading="lazy">
<?php else: ?>
              <i class="bi <?= e($p['icon'] ?: 'bi-window') ?>"></i>
<?php endif; ?>
              <span class="project-view"><i class="bi bi-arrow-up-right"></i></span>
            </div>
            <div class="project-info">
<?php if ($p['category']): ?><span class="tag"><?= e($p['category']) ?></span><?php endif; ?>
              <h4><a href="<?= url('portfolio/' . $p['slug']) ?>" class="stretched-link text-reset"><?= e($p['title']) ?></a></h4>
              <p><?= e($p['description']) ?></p>
            </div>
          </div>
        </div>
<?php endforeach; ?>
      </div>
<?php if ($more): ?>
      <div class="text-center mt-5" data-aos="fade-up">
        <a href="<?= url('portfolio') ?>" class="btn btn-outline-light btn-lg px-5"><?= e(setting('portfolio_more_text', 'View full portfolio')) ?> <i class="bi bi-arrow-right ms-1"></i></a>
      </div>
<?php endif; ?>
    </div>
  </section>
