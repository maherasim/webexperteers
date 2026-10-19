<?php
$pageTitle       = 'Services | ' . setting('site_name');
$metaDescription = setting('services_subtitle');
$canonicalPath   = url('services');
$activeNav       = 'services';
require APP . '/views/layout_top.php';

section('page_hero', [
    'eyebrow'  => setting('services_eyebrow'),
    'title'    => setting('services_title'),
    'subtitle' => setting('services_subtitle'),
    'crumbs'   => [['Services', null]],
]);
section('services', ['items' => active_rows('services'), 'head' => false]);
if (on('show_process')) {
    section('process', ['items' => active_rows('process_steps'), 'alt' => true]);
}
if (on('show_faq')) {
    section('faq', ['items' => array_slice(active_rows('faqs'), 0, 5)]);
}
section('cta');
require APP . '/views/layout_bottom.php';
