<?php
$pageTitle       = 'Portfolio | ' . setting('site_name');
$metaDescription = setting('portfolio_subtitle');
$canonicalPath   = url('portfolio');
$activeNav       = 'portfolio';
require APP . '/views/layout_top.php';

section('page_hero', [
    'eyebrow'  => setting('portfolio_eyebrow'),
    'title'    => setting('portfolio_title'),
    'subtitle' => setting('portfolio_subtitle'),
    'crumbs'   => [['Portfolio', null]],
]);
section('portfolio', ['items' => active_rows('projects'), 'head' => false, 'filters' => true]);
if (on('show_testimonials')) {
    section('testimonials', ['items' => active_rows('testimonials'), 'alt' => true]);
}
section('cta');
require APP . '/views/layout_bottom.php';
