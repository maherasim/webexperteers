<?php
// Old address (page.php?slug=...) — permanently redirect to the new /page/slug URL.
require __DIR__ . '/app/bootstrap.php';
$slug = (string) ($_GET['slug'] ?? '');
header('Location: ' . ($slug !== '' ? url('page/' . rawurlencode($slug)) : url()), true, 301);
