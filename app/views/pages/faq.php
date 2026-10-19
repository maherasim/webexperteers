<?php
$pageTitle       = 'FAQ | ' . setting('site_name');
$metaDescription = setting('faq_text');
$canonicalPath   = url('faq');
$activeNav       = 'faq';
require APP . '/views/layout_top.php';

section('page_hero', [
    'eyebrow'  => setting('faq_eyebrow'),
    'title'    => setting('faq_title'),
    'subtitle' => setting('faq_text'),
    'crumbs'   => [['FAQ', null]],
]);
section('faq', ['items' => active_rows('faqs')]);
section('cta');
require APP . '/views/layout_bottom.php';
