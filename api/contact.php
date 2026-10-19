<?php
require dirname(__DIR__) . '/app/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !same_origin()) {
    json_out(['ok' => false, 'message' => 'Invalid request.'], 400);
}

// Honeypot: real visitors never fill this hidden field.
if (!empty($_POST['website'])) {
    json_out(['ok' => true, 'message' => setting('contact_success', 'Thank you!')]);
}

$field = fn(string $k, int $max) => mb_substr(trim((string) ($_POST[$k] ?? '')), 0, $max);
$data = [
    'name'    => $field('name', 100),
    'email'   => $field('email', 190),
    'phone'   => $field('phone', 50),
    'service' => $field('service', 100),
    'budget'  => $field('budget', 100),
    'message' => $field('message', 5000),
];

if ($data['name'] === '' || $data['message'] === '' || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    json_out(['ok' => false, 'message' => 'Please fill in your name, a valid email and your message.'], 422);
}

$recent = (int) val(
    'SELECT COUNT(*) FROM messages WHERE ip = ? AND created_at > ?',
    [client_ip(), date('Y-m-d H:i:s', time() - 600)]
);
if ($recent >= 3) {
    json_out(['ok' => false, 'message' => 'You have sent several messages already. Please wait a few minutes or contact us on WhatsApp.'], 429);
}

insert_row('messages', $data + ['ip' => client_ip(), 'is_read' => 0, 'created_at' => now()]);

$body = "New enquiry from your website\n\n"
    . "Name:    {$data['name']}\n"
    . "Email:   {$data['email']}\n"
    . "Phone:   {$data['phone']}\n"
    . "Service: {$data['service']}\n"
    . "Budget:  {$data['budget']}\n\n"
    . $data['message'] . "\n";
send_mail(setting('notify_email'), 'New enquiry: ' . $data['name'], $body, $data['email']);

json_out(['ok' => true, 'message' => setting('contact_success', 'Thank you! Your message has been sent.')]);
