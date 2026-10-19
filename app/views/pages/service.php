<?php
/** @var array $service (set by the router) */
$pageTitle       = $service['title'] . ' | ' . setting('site_name');
$metaDescription = $service['description'];
$canonicalPath   = url('services/' . $service['slug']);
$activeNav       = 'services';
if (!empty($service['image'])) {
    $ogImage = $service['image'];
}
$others   = array_values(array_filter(active_rows('services'), fn($s) => $s['id'] !== $service['id']));
$features = lines($service['features'] ?? '');
require APP . '/views/layout_top.php';

section('page_hero', [
    'eyebrow'  => 'Service',
    'title'    => $service['title'],
    'subtitle' => $service['description'],
    'crumbs'   => [['Services', url('services')], [$service['title'], null]],
]);
?>
  <section class="section pt-5">
    <div class="container">
      <div class="row g-5">
        <div class="col-lg-8">
<?php if (!empty($service['image'])): ?>
          <div class="detail-image mb-5" data-aos="fade-up"><img src="<?= e(media($service['image'])) ?>" alt="<?= e($service['title']) ?>"></div>
<?php endif; ?>
<?php if (trim((string) ($service['content'] ?? '')) !== ''): ?>
          <div class="page-content mx-0" data-aos="fade-up"><?= $service['content'] /* admin-authored HTML */ ?></div>
<?php endif; ?>
<?php if ($features): ?>
          <h2 class="h3 fw-bold mt-5 mb-4" data-aos="fade-up">What's <span class="text-gradient">included</span></h2>
          <div class="row g-3 feature-grid">
<?php foreach ($features as $i => $f): ?>
            <div class="col-md-6" data-aos="fade-up" data-aos-delay="<?= ($i % 2) * 80 ?>">
              <div class="feature-item"><i class="bi bi-check-circle-fill"></i><span><?= e($f) ?></span></div>
            </div>
<?php endforeach; ?>
          </div>
<?php endif; ?>
        </div>
        <aside class="col-lg-4">
          <div class="side-sticky">
            <div class="side-card side-cta">
              <div class="icon-box mb-3"><i class="bi <?= e($service['icon']) ?>"></i></div>
              <h3 class="h5 fw-bold"><?= e(setting('service_cta_title', 'Interested in this service?')) ?></h3>
              <p class="text-muted-2 small"><?= e(setting('service_cta_text', 'Tell us about your project and get a free quote within one business day.')) ?></p>
              <a href="#enquiry" class="btn btn-gradient w-100 mb-2">Get a free quote</a>
<?php if (setting('whatsapp_number') !== ''): ?>
              <a href="<?= e(wa_link('Hi, I am interested in ' . $service['title'] . '.')) ?>" target="_blank" rel="noopener" class="btn btn-outline-light w-100"><i class="bi bi-whatsapp me-1"></i> Chat on WhatsApp</a>
<?php endif; ?>
            </div>
<?php if ($others): ?>
            <div class="side-card">
              <h3 class="h6 fw-bold text-uppercase ls-wide small mb-3">Other services</h3>
              <ul class="side-links">
<?php foreach ($others as $o): ?>
                <li><a href="<?= url('services/' . $o['slug']) ?>"><i class="bi <?= e($o['icon']) ?>"></i><?= e($o['title']) ?><i class="bi bi-chevron-right ms-auto"></i></a></li>
<?php endforeach; ?>
              </ul>
            </div>
<?php endif; ?>
          </div>
        </aside>
      </div>
    </div>
  </section>
<?php
if (on('show_process')) {
    section('process', ['items' => active_rows('process_steps'), 'alt' => true]);
}
section('contact', ['preselect' => $service['title']]);
require APP . '/views/layout_bottom.php';
