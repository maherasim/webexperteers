<?php
/** @var array $page (set by the router) */
$pageTitle       = $page['title'] . ' | ' . setting('site_name');
$metaDescription = $page['meta_description'] ?: setting('meta_description');
$canonicalPath   = url('page/' . $page['slug']);
$activeNav       = '';
require APP . '/views/layout_top.php';

section('page_hero', ['title' => $page['title'], 'crumbs' => [[$page['title'], null]]]);
?>
  <section class="section pt-5">
    <div class="container">
      <div class="page-content">
        <?= $page['content'] /* admin-authored HTML */ ?>
      </div>
    </div>
  </section>
<?php
require APP . '/views/layout_bottom.php';
