<?php
/** Inner-page header. @var string $title  @var string $eyebrow  @var string $subtitle  @var array $crumbs [[label, url|null], ...] */
$eyebrow  = $eyebrow ?? '';
$subtitle = $subtitle ?? '';
$crumbs   = $crumbs ?? [];
?>
  <header class="page-hero">
    <div class="hero-glow glow-1"></div>
    <div class="hero-glow glow-2"></div>
    <div class="container position-relative">
      <nav aria-label="breadcrumb">
        <ol class="crumbs">
          <li><a href="<?= url() ?>"><i class="bi bi-house-door"></i> Home</a></li>
<?php foreach ($crumbs as [$label, $link]): ?>
          <li><?php if ($link): ?><a href="<?= e($link) ?>"><?= e($label) ?></a><?php else: ?><span aria-current="page"><?= e($label) ?></span><?php endif; ?></li>
<?php endforeach; ?>
        </ol>
      </nav>
<?php if ($eyebrow !== ''): ?>
      <span class="eyebrow mt-3"><?= e($eyebrow) ?></span>
<?php endif; ?>
      <h1 class="display-5 fw-bold mt-3"><?= hl($title) ?></h1>
<?php if ($subtitle !== ''): ?>
      <p class="lead text-muted-2 mt-3 page-hero-sub"><?= e($subtitle) ?></p>
<?php endif; ?>
    </div>
  </header>
