<?php
$pageTitle       = setting('meta_title', setting('site_name'));
$metaDescription = setting('meta_description');
$canonicalPath   = url();
$activeNav       = 'home';

$technologies = on('show_tech') ? active_rows('technologies') : [];
$typedWords   = lines(setting('hero_typed_words'));
$avatars      = array_filter(array_map('trim', explode(',', setting('hero_avatars'))));

require APP . '/views/layout_top.php';
?>
  <header class="hero">
    <div class="hero-glow glow-1"></div>
    <div class="hero-glow glow-2"></div>
    <div class="container position-relative">
      <div class="row align-items-center g-5">
        <div class="col-lg-6" data-aos="fade-right">
<?php if (setting('hero_badge') !== ''): ?>
          <span class="eyebrow"><i class="bi bi-stars"></i> <?= e(setting('hero_badge')) ?></span>
<?php endif; ?>
          <h1 class="display-4 fw-bold mt-3">
            <?= e(setting('hero_title')) ?>
<?php if ($typedWords): ?>
            <span class="text-gradient typed" data-words="<?= e(json_encode($typedWords, JSON_UNESCAPED_UNICODE)) ?>"><?= e($typedWords[0]) ?></span>
<?php endif; ?>
          </h1>
          <p class="lead text-muted-2 mt-3"><?= e(setting('hero_text')) ?></p>
          <div class="d-flex flex-wrap gap-3 mt-4">
<?php if (setting('hero_btn1_text') !== ''): ?>
            <a href="<?= e(href(setting('hero_btn1_link'), 'contact')) ?>" class="btn btn-gradient btn-lg px-4"><?= e(setting('hero_btn1_text')) ?> <i class="bi bi-arrow-right ms-1"></i></a>
<?php endif; ?>
<?php if (setting('hero_btn2_text') !== ''): ?>
            <a href="<?= e(href(setting('hero_btn2_link'), 'portfolio')) ?>" class="btn btn-outline-light btn-lg px-4"><?= e(setting('hero_btn2_text')) ?></a>
<?php endif; ?>
          </div>
<?php if (setting('hero_trust_text') !== ''): ?>
          <div class="hero-trust mt-5">
            <div class="avatars">
<?php foreach ($avatars as $a): ?><span><?= e(mb_substr($a, 0, 2)) ?></span><?php endforeach; ?>
            </div>
            <div>
              <div class="stars"><?= str_repeat('<i class="bi bi-star-fill"></i>', 5) ?></div>
              <small class="text-muted-2"><?= e(setting('hero_trust_text')) ?></small>
            </div>
          </div>
<?php endif; ?>
        </div>
        <div class="col-lg-6" data-aos="fade-left" data-aos-delay="150">
          <div class="hero-visual">
            <div class="browser">
              <div class="browser-bar"><span></span><span></span><span></span><div class="url"><?= e(setting('site_domain')) ?></div></div>
              <div class="browser-body">
                <div class="mock-nav"></div>
                <div class="mock-hero">
                  <div class="mock-line w-75"></div>
                  <div class="mock-line w-50"></div>
                  <div class="mock-btn"></div>
                </div>
                <div class="mock-cards"><div></div><div></div><div></div></div>
              </div>
            </div>
<?php for ($i = 1; $i <= 3; $i++): if (setting("hero_card{$i}_value") === '') continue; ?>
            <div class="float-card fc-<?= $i ?>"><i class="bi <?= e(setting("hero_card{$i}_icon")) ?>"></i><div><strong><?= e(setting("hero_card{$i}_value")) ?></strong><small><?= e(setting("hero_card{$i}_label")) ?></small></div></div>
<?php endfor; ?>
          </div>
        </div>
      </div>
    </div>
  </header>

<?php if ($technologies): ?>
  <section class="tech-strip">
    <div class="container">
      <p class="text-center text-muted-2 small text-uppercase mb-4 ls-wide"><?= e(setting('tech_title')) ?></p>
      <div class="marquee">
        <div class="marquee-track">
<?php for ($loop = 0; $loop < 2; $loop++): foreach ($technologies as $t): ?>
          <span<?= $loop ? ' aria-hidden="true"' : '' ?>><i class="bi <?= e($t['icon']) ?>"></i> <?= e($t['name']) ?></span>
<?php endforeach; endfor; ?>
        </div>
      </div>
    </div>
  </section>
<?php endif; ?>

<?php
if (on('show_services')) {
    section('services', ['items' => array_slice(active_rows('services'), 0, 6), 'more' => true]);
}
if (on('show_about')) {
    section('about', ['alt' => true, 'more' => true]);
}
if (on('show_stats')) {
    section('stats', ['items' => active_rows('stats'), 'alt' => true]);
}
if (on('show_portfolio')) {
    section('portfolio', ['items' => array_slice(active_rows('projects'), 0, 6), 'more' => true]);
}
if (on('show_process')) {
    section('process', ['items' => active_rows('process_steps'), 'alt' => true]);
}
if (on('show_testimonials')) {
    section('testimonials', ['items' => active_rows('testimonials')]);
}
section('cta');
require APP . '/views/layout_bottom.php';
