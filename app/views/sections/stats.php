<?php
/** @var array $items  @var bool $alt */
$alt = $alt ?? false;
if (!$items) return;
?>
  <section class="section-tight<?= $alt ? ' section-alt' : '' ?>">
    <div class="container">
      <div class="row g-4 stats">
<?php foreach ($items as $i => $st): ?>
        <div class="col-6 col-lg-3" data-aos="zoom-in" data-aos-delay="<?= ($i % 4) * 100 ?>">
          <div class="stat"><span class="counter" data-target="<?= (int) $st['value'] ?>">0</span><span><?= e($st['suffix']) ?></span><p><?= e($st['label']) ?></p></div>
        </div>
<?php endforeach; ?>
      </div>
    </div>
  </section>
