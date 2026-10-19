<?php
require dirname(__DIR__) . '/app/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !same_origin()) {
    json_out(['ok' => false, 'message' => 'Invalid request.'], 400);
}

$email = mb_substr(trim((string) ($_POST['email'] ?? '')), 0, 190);

if (!empty($_POST['website'])) {
    json_out(['ok' => true, 'message' => 'Thanks for subscribing!']);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_out(['ok' => false, 'message' => 'Please enter a valid email.'], 422);
}

if (!(int) val('SELECT COUNT(*) FROM subscribers WHERE email = ?', [$email])) {
    insert_row('subscribers', ['email' => $email, 'created_at' => now()]);
}

json_out(['ok' => true, 'message' => 'Thanks for subscribing!']);
