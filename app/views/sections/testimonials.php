<?php
/** @var array $items  @var bool $alt */
$alt = $alt ?? false;
if (!$items) return;
?>
  <section class="section<?= $alt ? ' section-alt' : '' ?>">
    <div class="container">
      <div class="section-head text-center" data-aos="fade-up">
        <span class="eyebrow"><?= e(setting('testimonials_eyebrow')) ?></span>
        <h2 class="display-6 fw-bold mt-2"><?= hl(setting('testimonials_title')) ?></h2>
      </div>
      <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel" data-aos="fade-up">
        <div class="carousel-inner">
<?php foreach ($items as $i => $t): ?>
          <div class="carousel-item<?= $i === 0 ? ' active' : '' ?>">
            <div class="testimonial">
              <i class="bi bi-quote"></i>
<?php if ((int) $t['rating'] > 0): ?>
              <div class="stars mt-2"><?= str_repeat('<i class="bi bi-star-fill"></i>', min(5, (int) $t['rating'])) ?></div>
<?php endif; ?>
              <p>"<?= e($t['quote']) ?>"</p>
              <div class="person">
<?php if ($t['avatar']): ?>
                <img src="<?= e(media($t['avatar'])) ?>" alt="<?= e($t['name']) ?>" class="avatar avatar-img" loading="lazy">
<?php else: ?>
                <span class="avatar"><?= e(initials($t['name'])) ?></span>
<?php endif; ?>
                <div><strong><?= e($t['name']) ?></strong><small><?= e($t['role']) ?></small></div>
              </div>
            </div>
          </div>
<?php endforeach; ?>
        </div>
<?php if (count($items) > 1): ?>
        <div class="carousel-indicators position-static mt-4">
<?php foreach ($items as $i => $t): ?>
          <button type="button" data-bs-target="#testimonialCarousel" data-bs-slide-to="<?= $i ?>"<?= $i === 0 ? ' class="active" aria-current="true"' : '' ?> aria-label="Testimonial <?= $i + 1 ?>"></button>
<?php endforeach; ?>
        </div>
<?php endif; ?>
      </div>
    </div>
  </section>
