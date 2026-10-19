<?php
/**
 * Shared <head> + navbar.
 * Expects: $pageTitle, $metaDescription, $canonicalPath, $activeNav ('home', 'about', 'services', ...).
 */
$siteName    = setting('site_name', 'WebExpertees');
$baseUrl     = rtrim((string) config('base_url', ''), '/');
$favicon     = setting('favicon');
$ogImage     = $ogImage ?? (setting('og_image') ?: 'assets/img/og-image.png');
$activeNav   = $activeNav ?? '';
$navServices = active_rows('services');
$navItems    = [
    ['', 'Home', 'home', true],
    ['about', 'About', 'about', on('show_about')],
    ['services', 'Services', 'services', (bool) $navServices],
    ['portfolio', 'Portfolio', 'portfolio', on('show_portfolio')],
    ['pricing', 'Pricing', 'pricing', on('show_pricing')],
    ['faq', 'FAQ', 'faq', on('show_faq')],
    ['contact', 'Contact', 'contact', true],
];
$jsonLd = [
    '@context'    => 'https://schema.org',
    '@type'       => 'ProfessionalService',
    'name'        => $siteName,
    'url'         => $baseUrl . '/',
    'logo'        => $baseUrl . url('assets/img/icon-512.png'),
    'description' => setting('meta_description'),
    'email'       => setting('contact_email'),
    'areaServed'  => 'Worldwide',
];
if (setting('phone') !== '') {
    $jsonLd['telephone'] = setting('phone');
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle) ?></title>
  <meta name="description" content="<?= e($metaDescription) ?>">
<?php if ($activeNav === 'home' && setting('meta_keywords') !== ''): ?>
  <meta name="keywords" content="<?= e(setting('meta_keywords')) ?>">
<?php endif; ?>
  <link rel="canonical" href="<?= e($baseUrl . $canonicalPath) ?>">
  <meta name="theme-color" content="#0b0d17">
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="<?= e($siteName) ?>">
  <meta property="og:url" content="<?= e($baseUrl . $canonicalPath) ?>">
  <meta property="og:title" content="<?= e($pageTitle) ?>">
  <meta property="og:description" content="<?= e($metaDescription) ?>">
  <meta property="og:image" content="<?= e($baseUrl . url($ogImage)) ?>">
  <meta name="twitter:card" content="summary_large_image">
<?php if ($favicon !== ''): ?>
  <link rel="icon" href="<?= e(media($favicon)) ?>">
<?php else: ?>
  <link rel="icon" href="<?= url('favicon.ico') ?>" sizes="48x48">
  <link rel="icon" href="<?= url('assets/img/favicon.svg') ?>" type="image/svg+xml">
<?php endif; ?>
  <link rel="apple-touch-icon" href="<?= url('assets/img/apple-touch-icon.png') ?>">
  <link rel="manifest" href="<?= url('site.webmanifest') ?>">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
  <link href="<?= url('assets/css/style.css?v=7') ?>" rel="stylesheet">
  <script type="application/ld+json"><?= json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?></script>
<?= setting('head_code') /* admin-provided tracking code, output as-is */ ?>
</head>
<body id="top">

  <nav id="mainNav" class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
      <a class="navbar-brand" href="<?= url() ?>"><?php require __DIR__ . '/logo.php'; ?></a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-controls="navMenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navMenu">
        <ul class="navbar-nav ms-auto align-items-lg-center">
<?php foreach ($navItems as [$path, $label, $key, $show]):
    if (!$show) continue;
    $isActive = $activeNav === $key;
    if ($key === 'services'): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle<?= $isActive ? ' active' : '' ?>" href="<?= url('services') ?>" role="button" data-bs-toggle="dropdown" aria-expanded="false"><?= e($label) ?></a>
            <ul class="dropdown-menu dropdown-menu-dark nav-dropdown">
              <li><a class="dropdown-item fw-semibold" href="<?= url('services') ?>"><i class="bi bi-grid me-2"></i>All services</a></li>
              <li><hr class="dropdown-divider"></li>
<?php foreach ($navServices as $s): ?>
              <li><a class="dropdown-item" href="<?= url('services/' . $s['slug']) ?>"><i class="bi <?= e($s['icon']) ?> me-2"></i><?= e($s['title']) ?></a></li>
<?php endforeach; ?>
            </ul>
          </li>
<?php else: ?>
          <li class="nav-item"><a class="nav-link<?= $isActive ? ' active' : '' ?>" href="<?= url($path) ?>"<?= $isActive ? ' aria-current="page"' : '' ?>><?= e($label) ?></a></li>
<?php endif; endforeach; ?>
          <li class="nav-item ms-lg-3 mt-2 mt-lg-0"><a class="btn btn-gradient btn-sm px-4" href="<?= url('contact') ?>"><?= e(setting('nav_cta_text', 'Get a Quote')) ?></a></li>
        </ul>
      </div>
    </div>
  </nav>
