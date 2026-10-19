<?php
require __DIR__ . '/_init.php';

$admin = current_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) {
        flash('danger', 'Your session expired. Please try again.');
        redirect('profile.php');
    }
    $full = row('SELECT * FROM admins WHERE id = ?', [$admin['id']]);
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $current = (string) ($_POST['current_password'] ?? '');
    $new = (string) ($_POST['new_password'] ?? '');
    $confirm = (string) ($_POST['confirm_password'] ?? '');

    if (!password_verify($current, $full['password_hash'])) {
        flash('danger', 'Your current password is incorrect.');
    } elseif ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        flash('danger', 'Please enter your name and a valid email.');
    } elseif ($new !== '' && strlen($new) < 8) {
        flash('danger', 'The new password must be at least 8 characters.');
    } elseif ($new !== $confirm) {
        flash('danger', 'The new passwords do not match.');
    } elseif ((int) val('SELECT COUNT(*) FROM admins WHERE email = ? AND id <> ?', [$email, $admin['id']])) {
        flash('danger', 'That email is used by another admin.');
    } else {
        $data = ['name' => $name, 'email' => $email];
        if ($new !== '') {
            $data['password_hash'] = password_hash($new, PASSWORD_DEFAULT);
            session_regenerate_id(true);
        }
        update_row('admins', (int) $admin['id'], $data);
        flash('success', $new !== '' ? 'Profile and password updated.' : 'Profile updated.');
    }
    redirect('profile.php');
}

admin_header('My Profile', 'profile');
?>
<form method="post" class="card" style="max-width: 640px">
  <div class="card-body">
    <?= csrf_field() ?>
    <div class="mb-3">
      <label class="form-label" for="name">Name</label>
      <input type="text" class="form-control" id="name" name="name" value="<?= e($admin['name']) ?>" required>
    </div>
    <div class="mb-4">
      <label class="form-label" for="email">Login email</label>
      <input type="email" class="form-control" id="email" name="email" value="<?= e($admin['email']) ?>" required autocomplete="username">
    </div>
    <h2 class="h6 fw-bold">Change password</h2>
    <p class="small text-secondary">Leave empty to keep your current password.</p>
    <div class="row g-3 mb-4">
      <div class="col-md-6">
        <label class="form-label" for="new_password">New password</label>
        <input type="password" class="form-control" id="new_password" name="new_password" minlength="8" autocomplete="new-password">
      </div>
      <div class="col-md-6">
        <label class="form-label" for="confirm_password">Confirm new password</label>
        <input type="password" class="form-control" id="confirm_password" name="confirm_password" autocomplete="new-password">
      </div>
    </div>
    <div class="border-top pt-3">
      <label class="form-label" for="current_password">Current password <span class="text-danger">*</span></label>
      <input type="password" class="form-control" id="current_password" name="current_password" required autocomplete="current-password">
      <div class="form-text">Required to save any change.</div>
    </div>
  </div>
  <div class="card-footer bg-white">
    <button class="btn btn-gradient px-4" type="submit"><i class="bi bi-check2 me-1"></i>Save</button>
  </div>
</form>
<?php admin_footer();
