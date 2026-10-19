<?php
declare(strict_types=1);

define('ROOT', dirname(__DIR__));
define('APP', __DIR__);

$configFile = is_file(APP . '/config.php') ? APP . '/config.php' : APP . '/config.sample.php';
$config = require $configFile;

date_default_timezone_set($config['timezone'] ?? 'UTC');
error_reporting(E_ALL);
ini_set('display_errors', !empty($config['debug']) ? '1' : '0');

require APP . '/db.php';
require APP . '/helpers.php';

function config(string $key, $default = null)
{
    global $config;
    return $config[$key] ?? $default;
}

/** Sessions are only used by the admin panel, so public pages stay cookie-free and cacheable. */
function start_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    session_name('wx_admin');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => is_https(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}
