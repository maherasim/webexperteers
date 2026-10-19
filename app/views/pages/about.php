<?php
$pageTitle       = 'About Us | ' . setting('site_name');
$metaDescription = setting('about_text');
$canonicalPath   = url('about');
$activeNav       = 'about';
require APP . '/views/layout_top.php';

section('page_hero', [
    'eyebrow'  => setting('about_page_eyebrow', 'About us'),
    'title'    => setting('about_page_title', setting('about_title')),
    'subtitle' => setting('about_page_subtitle'),
    'crumbs'   => [['About', null]],
]);
section('about');
if (on('show_stats')) {
    section('stats', ['items' => active_rows('stats'), 'alt' => true]);
}
if (on('show_process')) {
    section('process', ['items' => active_rows('process_steps')]);
}
if (on('show_testimonials')) {
    section('testimonials', ['items' => active_rows('testimonials'), 'alt' => true]);
}
section('cta');
require APP . '/views/layout_bottom.php';
