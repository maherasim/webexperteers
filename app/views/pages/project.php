<?php
/** @var array $project (set by the router) */
$pageTitle       = $project['title'] . ' | Portfolio | ' . setting('site_name');
$metaDescription = $project['description'];
$canonicalPath   = url('portfolio/' . $project['slug']);
$activeNav       = 'portfolio';
if (!empty($project['image'])) {
    $ogImage = $project['image'];
}
$all = active_rows('projects');
$pos = 0;
foreach ($all as $i => $p) {
    if ($p['id'] === $project['id']) {
        $pos = $i;
    }
}
$prev    = $all[($pos - 1 + count($all)) % count($all)];
$next    = $all[($pos + 1) % count($all)];
$related = array_slice(array_values(array_filter($all, fn($p) => $p['id'] !== $project['id'])), 0, 3);
$tech    = lines($project['tech_stack'] ?? '');
require APP . '/views/layout_top.php';

section('page_hero', [
    'eyebrow'  => $project['category'] ?: 'Project',
    'title'    => $project['title'],
    'subtitle' => $project['description'],
    'crumbs'   => [['Portfolio', url('portfolio')], [$project['title'], null]],
]);
?>
  <section class="section pt-5">
    <div class="container">
      <div class="detail-image detail-image-lg mb-5 project-thumb <?= e($project['color'] ?: 'g-2') ?>" data-aos="fade-up">
<?php if ($project['image']): ?>
        <img src="<?= e(media($project['image'])) ?>" alt="<?= e($project['title']) ?>">
<?php else: ?>
        <i class="bi <?= e($project['icon'] ?: 'bi-window') ?>"></i>
<?php endif; ?>
      </div>
      <div class="row g-5">
        <div class="col-lg-8" data-aos="fade-up">
<?php if (trim((string) ($project['content'] ?? '')) !== ''): ?>
          <div class="page-content mx-0"><?= $project['content'] /* admin-authored HTML */ ?></div>
<?php else: ?>
          <p class="lead text-muted-2"><?= e($project['description']) ?></p>
<?php endif; ?>
        </div>
        <aside class="col-lg-4">
          <div class="side-card side-sticky">
            <dl class="project-meta">
              <dt>Client</dt>
              <dd><?= $project['client'] ? e($project['client']) : '<span class="tag">Sample design</span>' ?></dd>
<?php if ($project['category']): ?>
              <dt>Category</dt><dd><?= e($project['category']) ?></dd>
<?php endif; ?>
<?php if ($tech): ?>
              <dt>Technologies</dt>
              <dd class="tech-chips"><?php foreach ($tech as $t): ?><span><?= e($t) ?></span><?php endforeach; ?></dd>
<?php endif; ?>
            </dl>
<?php if ($project['url']): ?>
            <a href="<?= e($project['url']) ?>" target="_blank" rel="noopener" class="btn btn-outline-light w-100 mb-2">Visit live site <i class="bi bi-box-arrow-up-right ms-1"></i></a>
<?php endif; ?>
            <a href="<?= url('contact') ?>" class="btn btn-gradient w-100">Start a similar project</a>
          </div>
        </aside>
      </div>

<?php if (count($all) > 1): ?>
      <div class="project-nav mt-5">
        <a href="<?= url('portfolio/' . $prev['slug']) ?>"><i class="bi bi-arrow-left"></i><div><small>Previous</small><?= e($prev['title']) ?></div></a>
        <a href="<?= url('portfolio/' . $next['slug']) ?>" class="text-end"><div><small>Next</small><?= e($next['title']) ?></div><i class="bi bi-arrow-right"></i></a>
      </div>
<?php endif; ?>
    </div>
  </section>
<?php
if ($related) {
    echo '<div class="section-alt pt-5"><div class="container"><h2 class="h3 fw-bold text-center mb-0">More <span class="text-gradient">projects</span></h2></div></div>';
    section('portfolio', ['items' => $related, 'head' => false, 'alt' => true, 'more' => true]);
}
section('cta');
require APP . '/views/layout_bottom.php';
