<?php
$pageTitle       = 'Pricing | ' . setting('site_name');
$metaDescription = setting('pricing_subtitle');
$canonicalPath   = url('pricing');
$activeNav       = 'pricing';
require APP . '/views/layout_top.php';

section('page_hero', [
    'eyebrow'  => setting('pricing_eyebrow'),
    'title'    => setting('pricing_title'),
    'subtitle' => setting('pricing_subtitle'),
    'crumbs'   => [['Pricing', null]],
]);
section('pricing', ['items' => active_rows('pricing_plans'), 'head' => false]);
if (on('show_faq')) {
    section('faq', ['items' => array_slice(active_rows('faqs'), 0, 5), 'alt' => true]);
}
section('cta');
require APP . '/views/layout_bottom.php';
