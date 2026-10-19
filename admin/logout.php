<?php
define('ADMIN_PUBLIC', true);
require __DIR__ . '/_init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_valid()) {
    $_SESSION = [];
    session_regenerate_id(true);
    flash('success', 'You have been logged out.');
}
redirect('login.php');
