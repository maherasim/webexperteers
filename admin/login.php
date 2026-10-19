<?php
define('ADMIN_PUBLIC', true);
require __DIR__ . '/_init.php';

if (current_admin()) {
    redirect('index.php');
}

const MAX_ATTEMPTS = 5;
const LOCK_MINUTES = 15;

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));
    $since = date('Y-m-d H:i:s', time() - LOCK_MINUTES * 60);
    $attempts = (int) val('SELECT COUNT(*) FROM login_attempts WHERE ip = ? AND attempted_at > ?', [client_ip(), $since]);

    if (!csrf_valid()) {
        $error = 'Your session expired. Please try again.';
    } elseif ($attempts >= MAX_ATTEMPTS) {
        $error = 'Too many failed attempts. Please wait ' . LOCK_MINUTES . ' minutes and try again.';
    } else {
        $admin = row('SELECT * FROM admins WHERE email = ?', [$email]);
        if ($admin && password_verify((string) ($_POST['password'] ?? ''), $admin['password_hash'])) {
            q('DELETE FROM login_attempts WHERE ip = ?', [client_ip()]);
            if (password_needs_rehash($admin['password_hash'], PASSWORD_DEFAULT)) {
                q('UPDATE admins SET password_hash = ? WHERE id = ?', [password_hash($_POST['password'], PASSWORD_DEFAULT), $admin['id']]);
            }
            q('UPDATE admins SET last_login = ? WHERE id = ?', [now(), $admin['id']]);
            session_regenerate_id(true);
            $_SESSION['admin_id']  = (int) $admin['id'];
            $_SESSION['last_seen'] = time();
            redirect('index.php');
        }
        insert_row('login_attempts', ['ip' => client_ip(), 'attempted_at' => now()]);
        q('DELETE FROM login_attempts WHERE attempted_at < ?', [date('Y-m-d H:i:s', time() - 86400)]);
        $error = 'Incorrect email or password.';
    }
}
$flashes = take_flashes();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <title>Admin Login · <?= e(setting('site_name')) ?></title>
  <link rel="icon" href="../<?= e(setting('favicon') ?: 'assets/img/favicon.svg') ?>">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="assets/admin.css?v=3" rel="stylesheet">
</head>
<body class="login-page">
  <div class="login-card">
    <div class="text-center mb-4">
      <img src="../assets/img/logo-mark.svg" alt="" class="brand-mark-img lg">
      <h1 class="h4 fw-bold mt-3 mb-1"><?= e(setting('site_name')) ?> Admin</h1>
      <p class="text-secondary small mb-0">Sign in to manage your website</p>
    </div>
<?php foreach ($flashes as [$type, $msg]): ?>
    <div class="alert alert-<?= e($type) ?> small"><?= e($msg) ?></div>
<?php endforeach; ?>
<?php if ($error): ?>
    <div class="alert alert-danger small"><?= e($error) ?></div>
<?php endif; ?>
    <form method="post" autocomplete="on">
      <?= csrf_field() ?>
      <div class="mb-3">
        <label class="form-label" for="email">Email</label>
        <input type="email" class="form-control" id="email" name="email" value="<?= e($email) ?>" required autofocus autocomplete="username">
      </div>
      <div class="mb-4">
        <label class="form-label" for="password">Password</label>
        <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password">
      </div>
      <button class="btn btn-gradient w-100 py-2" type="submit">Sign in</button>
    </form>
    <p class="text-center mt-4 mb-0"><a href="../index.php" class="small text-secondary"><i class="bi bi-arrow-left"></i> Back to website</a></p>
  </div>
</body>
</html>
