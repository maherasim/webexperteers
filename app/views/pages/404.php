<?php
http_response_code(404);
$pageTitle       = 'Page not found | ' . setting('site_name');
$metaDescription = setting('meta_description');
$canonicalPath   = url();
$activeNav       = '';
require APP . '/views/layout_top.php';
?>
  <header class="page-hero text-center notfound">
    <div class="hero-glow glow-1"></div>
    <div class="hero-glow glow-2"></div>
    <div class="container position-relative">
      <div class="notfound-code text-gradient">404</div>
      <h1 class="display-6 fw-bold mt-2">This page doesn't exist</h1>
      <p class="lead text-muted-2 mt-3">The page may have moved, or the link might be wrong.</p>
      <div class="d-flex flex-wrap justify-content-center gap-3 mt-4">
        <a href="<?= url() ?>" class="btn btn-gradient btn-lg px-4"><i class="bi bi-house-door me-1"></i> Back to home</a>
        <a href="<?= url('contact') ?>" class="btn btn-outline-light btn-lg px-4">Contact us</a>
      </div>
    </div>
  </header>
<?php
require APP . '/views/layout_bottom.php';
