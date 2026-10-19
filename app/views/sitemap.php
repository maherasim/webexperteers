<?php
header('Content-Type: application/xml; charset=utf-8');
$site = rtrim((string) config('base_url', ''), '/');
$urls = [[url(), '1.0'], [url('services'), '0.9'], [url('contact'), '0.8']];
foreach (['about' => 'show_about', 'portfolio' => 'show_portfolio', 'pricing' => 'show_pricing', 'faq' => 'show_faq'] as $p => $flag) {
    if (on($flag)) {
        $urls[] = [url($p), '0.8'];
    }
}
foreach (active_rows('services') as $s) {
    $urls[] = [url('services/' . $s['slug']), '0.8'];
}
if (on('show_portfolio')) {
    foreach (active_rows('projects') as $p) {
        $urls[] = [url('portfolio/' . $p['slug']), '0.6'];
    }
}
foreach (active_rows('pages') as $p) {
    $urls[] = [url('page/' . $p['slug']), '0.3'];
}
echo '<?xml version="1.0" encoding="UTF-8"?>', "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', "\n";
foreach ($urls as [$loc, $priority]) {
    echo '  <url><loc>', e($site . $loc), '</loc><priority>', $priority, '</priority></url>', "\n";
}
echo '</urlset>', "\n";
