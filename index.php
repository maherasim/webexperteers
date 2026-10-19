<?php
/*
 * Front controller: every page request without a matching file comes here (see .htaccess).
 * Local dev: php -S 127.0.0.1:8000 index.php
 */
if (PHP_SAPI === 'cli-server') {
    $file = __DIR__ . urldecode((string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    if ($file !== __FILE__ && (is_file($file) || is_file(rtrim($file, '/') . '/index.php'))) {
        return false; // let the built-in server serve real files and folders (assets, admin, api)
    }
}

require __DIR__ . '/app/bootstrap.php';

if (!db_ready()) {
    http_response_code(503);
    exit('Site is being set up. Please check back shortly.');
}

$path = urldecode((string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$base = rtrim((string) config('base_path', ''), '/');
if ($base !== '' && str_starts_with($path, $base)) {
    $path = substr($path, strlen($base));
}
$path = trim($path, '/');
$seg  = $path === '' ? [] : explode('/', $path);

$view = function (string $name, array $vars = []): never {
    extract($vars);
    require APP . "/views/pages/$name.php";
    exit;
};
$find = fn(string $table, string $slug) => row("SELECT * FROM $table WHERE slug = ? AND is_active = 1", [$slug]);

switch ($seg[0] ?? '') {
    case '':
        $view('home');
    case 'index.php':
        redirect(url());
    case 'about':
        if (count($seg) === 1 && on('show_about')) $view('about');
        break;
    case 'services':
        if (count($seg) === 1) $view('services');
        if (count($seg) === 2 && ($service = $find('services', $seg[1]))) $view('service', ['service' => $service]);
        break;
    case 'portfolio':
        if (!on('show_portfolio')) break;
        if (count($seg) === 1) $view('portfolio');
        if (count($seg) === 2 && ($project = $find('projects', $seg[1]))) $view('project', ['project' => $project]);
        break;
    case 'pricing':
        if (count($seg) === 1 && on('show_pricing')) $view('pricing');
        break;
    case 'faq':
        if (count($seg) === 1 && on('show_faq')) $view('faq');
        break;
    case 'contact':
        if (count($seg) === 1) $view('contact');
        break;
    case 'page':
        if (count($seg) === 2 && ($page = $find('pages', $seg[1]))) $view('page', ['page' => $page]);
        break;
    case 'sitemap.xml':
        require APP . '/views/sitemap.php';
        exit;
}

$view('404');
