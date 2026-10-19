<?php
/*
 * Command-line installer. Creates tables, adds default content and the first admin.
 * Safe to re-run: existing data is never overwritten.
 *
 *   php app/install.php --email=you@example.com [--name="Your Name"]
 *   php app/install.php --reset-password=you@example.com
 */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require __DIR__ . '/bootstrap.php';
require APP . '/schema.php';
require APP . '/seed.php';
require APP . '/seed_details.php';

$opts = getopt('', ['email:', 'name:', 'reset-password:']);

function random_password(): string
{
    return rtrim(strtr(base64_encode(random_bytes(12)), '+/', 'Kx'), '=');
}

if (!empty($opts['reset-password'])) {
    $admin = row('SELECT id FROM admins WHERE email = ?', [$opts['reset-password']]);
    if (!$admin) {
        fwrite(STDERR, "No admin with that email.\n");
        exit(1);
    }
    $pass = random_password();
    q('UPDATE admins SET password_hash = ? WHERE id = ?', [password_hash($pass, PASSWORD_DEFAULT), $admin['id']]);
    echo "New password for {$opts['reset-password']}: $pass\nChange it after logging in (Admin > My Profile).\n";
    exit(0);
}

foreach (schema_statements(db_driver()) as $sql) {
    db()->exec($sql);
}
foreach (schema_migrations() as [$table, $column, $definition]) {
    if (!column_exists($table, $column)) {
        db()->exec("ALTER TABLE $table ADD COLUMN $column $definition");
        echo "Added column $table.$column\n";
    }
}
echo "Tables ready (" . db_driver() . ").\n";

seed_database();
seed_details();
echo "Default content ready.\n";

if ((int) val('SELECT COUNT(*) FROM admins') === 0) {
    $email = $opts['email'] ?? '';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        fwrite(STDERR, "No admin yet. Re-run with --email=you@example.com to create one.\n");
        exit(1);
    }
    $pass = random_password();
    insert_row('admins', [
        'name'          => $opts['name'] ?? 'Admin',
        'email'         => $email,
        'password_hash' => password_hash($pass, PASSWORD_DEFAULT),
        'created_at'    => now(),
    ]);
    echo "Admin created.\n  Email:    $email\n  Password: $pass\nChange the password after logging in (Admin > My Profile).\n";
} else {
    echo "Admin already exists — skipped.\n";
}
