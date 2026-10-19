<?php
require __DIR__ . '/_init.php';

$groups = require APP . '/settings_schema.php';
$g = (string) ($_GET['g'] ?? array_key_first($groups));
if (!isset($groups[$g])) {
    $g = array_key_first($groups);
}
$group = $groups[$g];
$self = 'settings.php?g=' . urlencode($g);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) {
        flash('danger', 'Your session expired. Please try again.');
        redirect($self);
    }
    $errors = 0;
    foreach ($group['fields'] as $key => $f) {
        $type = $f['type'] ?? 'text';
        if ($type === 'checkbox') {
            save_setting($key, isset($_POST[$key]) ? '1' : '0');
        } elseif ($type === 'image') {
            try {
                $new = upload_image($key);
            } catch (RuntimeException $ex) {
                flash('danger', ($f['label'] ?? $key) . ': ' . $ex->getMessage());
                $errors++;
                continue;
            }
            if ($new) {
                delete_upload(setting($key));
                save_setting($key, $new);
            } elseif (!empty($_POST[$key . '_remove'])) {
                delete_upload(setting($key));
                save_setting($key, '');
            }
        } else {
            $value = trim((string) ($_POST[$key] ?? ''));
            if ($type === 'email' && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                flash('danger', ($f['label'] ?? $key) . ' must be a valid email address.');
                $errors++;
                continue;
            }
            save_setting($key, $value);
        }
    }
    if (!$errors) {
        flash('success', $group['label'] . ' saved.');
    }
    redirect($self);
}

admin_header('Site Settings', 'settings');
?>
<div class="row g-4">
  <div class="col-lg-3">
    <div class="list-group settings-tabs">
<?php foreach ($groups as $key => $grp): ?>
      <a href="settings.php?g=<?= e($key) ?>" class="list-group-item list-group-item-action<?= $key === $g ? ' active' : '' ?>"><i class="bi <?= e($grp['icon']) ?> me-2"></i><?= e($grp['label']) ?></a>
<?php endforeach; ?>
    </div>
  </div>
  <div class="col-lg-9">
    <form method="post" enctype="multipart/form-data" class="card">
      <div class="card-header"><?= e($group['label']) ?></div>
      <div class="card-body">
        <?= csrf_field() ?>
<?php if ($g === 'sections'): ?>
        <div class="alert alert-info small"><i class="bi bi-lightbulb me-1"></i>Tip: wrap a word in <code>*asterisks*</code> in a heading to show it with the gradient colour, e.g. <code>Featured *projects*</code>.</div>
<?php endif; ?>
<?php foreach ($group['fields'] as $key => $f) {
        render_field($key, $f, setting($key, ($f['type'] ?? '') === 'checkbox' ? '0' : ''));
    } ?>
      </div>
      <div class="card-footer bg-white">
        <button class="btn btn-gradient px-4" type="submit"><i class="bi bi-check2 me-1"></i>Save changes</button>
      </div>
    </form>
  </div>
</div>
<?php admin_footer();
