<?php
/*
 * Copy this file to config.php and adjust. config.php is not committed to git.
 */
return [
    'db' => [
        // 'sqlite' needs no setup. For MySQL, create the database in hPanel and fill in the fields below.
        'driver'      => 'sqlite',
        'sqlite_path' => dirname(__DIR__) . '/storage/database.sqlite',
        'host'        => 'localhost',
        'name'        => '',
        'user'        => '',
        'pass'        => '',
        'charset'     => 'utf8mb4',
    ],
    'base_url'  => 'https://webexpertees.com',
    'base_path' => '',  // set to e.g. '/site' only if the website lives in a sub-folder
    'mail_from' => 'no-reply@webexpertees.com',
    'timezone'  => 'Asia/Karachi',
    'debug'     => false,
];
