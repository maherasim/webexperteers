<?php
$pageTitle       = 'Contact Us | ' . setting('site_name');
$metaDescription = setting('contact_text');
$canonicalPath   = url('contact');
$activeNav       = 'contact';
require APP . '/views/layout_top.php';

section('page_hero', [
    'eyebrow'  => setting('contact_eyebrow'),
    'title'    => setting('contact_page_title', 'Get in *touch*'),
    'subtitle' => setting('contact_text'),
    'crumbs'   => [['Contact', null]],
]);
section('contact', ['preselect' => (string) ($_GET['service'] ?? '')]);
if (on('show_faq')) {
    section('faq', ['items' => array_slice(active_rows('faqs'), 0, 4), 'alt' => true]);
}
require APP . '/views/layout_bottom.php';
