<?php
require __DIR__ . '/_init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) {
        flash('danger', 'Your session expired. Please try again.');
        redirect('messages.php');
    }
    $id = (int) ($_POST['id'] ?? 0);
    switch ($_POST['_op'] ?? '') {
        case 'delete':
            q('DELETE FROM messages WHERE id = ?', [$id]);
            flash('success', 'Message deleted.');
            break;
        case 'unread':
            q('UPDATE messages SET is_read = 0 WHERE id = ?', [$id]);
            flash('success', 'Marked as unread.');
            break;
        case 'read_all':
            q('UPDATE messages SET is_read = 1 WHERE is_read = 0');
            flash('success', 'All messages marked as read.');
            break;
    }
    redirect('messages.php');
}

/* ---------- Single message ---------- */
if (isset($_GET['id'])) {
    $m = row('SELECT * FROM messages WHERE id = ?', [(int) $_GET['id']]);
    if (!$m) {
        flash('warning', 'Message not found.');
        redirect('messages.php');
    }
    if (!$m['is_read']) {
        q('UPDATE messages SET is_read = 1 WHERE id = ?', [$m['id']]);
    }
    $replySubject = rawurlencode('Re: Your enquiry to ' . setting('site_name'));
    $waNumber = preg_replace('/\D+/', '', (string) $m['phone']);

    admin_header('Message', 'messages');
    ?>
<div class="mb-3"><a href="messages.php" class="text-decoration-none small"><i class="bi bi-arrow-left"></i> Back to messages</a></div>
<div class="card">
  <div class="card-body">
    <div class="d-flex flex-wrap align-items-start gap-3 mb-4">
      <span class="avatar-lg"><?= e(initials($m['name'])) ?></span>
      <div>
        <h2 class="h5 mb-1"><?= e($m['name']) ?></h2>
        <a href="mailto:<?= e($m['email']) ?>"><?= e($m['email']) ?></a>
        <?php if ($m['phone']): ?><span class="text-secondary"> · <?= e($m['phone']) ?></span><?php endif; ?>
      </div>
      <div class="ms-auto text-secondary small"><?= e(date('M j, Y · g:i a', strtotime($m['created_at']))) ?></div>
    </div>
    <div class="row g-3 mb-4">
      <div class="col-sm-6"><div class="meta-box"><small>Service</small><?= e($m['service'] ?: '—') ?></div></div>
      <div class="col-sm-6"><div class="meta-box"><small>Budget</small><?= e($m['budget'] ?: '—') ?></div></div>
    </div>
    <div class="message-body"><?= nl2br(e($m['message'])) ?></div>
  </div>
  <div class="card-footer bg-white d-flex flex-wrap gap-2">
    <a class="btn btn-gradient" href="mailto:<?= e($m['email']) ?>?subject=<?= $replySubject ?>"><i class="bi bi-reply me-1"></i>Reply by email</a>
<?php if (strlen($waNumber) >= 8): ?>
    <a class="btn btn-success" href="https://wa.me/<?= e($waNumber) ?>" target="_blank" rel="noopener"><i class="bi bi-whatsapp me-1"></i>WhatsApp</a>
<?php endif; ?>
    <form method="post" class="ms-auto d-flex gap-2">
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
      <button class="btn btn-light" name="_op" value="unread">Mark unread</button>
      <button class="btn btn-light text-danger" name="_op" value="delete" onclick="return confirm('Delete this message?');"><i class="bi bi-trash"></i> Delete</button>
    </form>
  </div>
</div>
<?php
    admin_footer();
    exit;
}

/* ---------- List ---------- */
$messages = rows('SELECT id, name, email, service, message, is_read, created_at FROM messages ORDER BY id DESC LIMIT 500');
$unread = count(array_filter($messages, fn($m) => !$m['is_read']));

admin_header('Messages', 'messages');
?>
<div class="d-flex align-items-center mb-3">
  <span class="text-secondary small"><?= count($messages) ?> messages · <?= $unread ?> unread</span>
<?php if ($unread): ?>
  <form method="post" class="ms-auto"><?= csrf_field() ?><button class="btn btn-light btn-sm" name="_op" value="read_all"><i class="bi bi-check2-all me-1"></i>Mark all as read</button></form>
<?php endif; ?>
</div>
<div class="card">
  <div class="list-group list-group-flush">
<?php if (!$messages): ?>
    <div class="text-center text-secondary py-5">No messages yet.</div>
<?php endif; ?>
<?php foreach ($messages as $m): ?>
    <a href="messages.php?id=<?= (int) $m['id'] ?>" class="list-group-item list-group-item-action message-row<?= $m['is_read'] ? '' : ' unread' ?>">
      <div class="d-flex align-items-center gap-2">
        <?= $m['is_read'] ? '' : '<span class="dot"></span>' ?>
        <strong><?= e($m['name']) ?></strong>
        <span class="text-secondary small d-none d-sm-inline"><?= e($m['email']) ?></span>
        <span class="ms-auto text-secondary small text-nowrap"><?= e(date('M j, g:i a', strtotime($m['created_at']))) ?></span>
      </div>
      <div class="small text-secondary text-truncate"><?php if ($m['service']): ?><span class="badge text-bg-light me-1"><?= e($m['service']) ?></span><?php endif; ?><?= e(str_limit($m['message'], 140)) ?></div>
    </a>
<?php endforeach; ?>
  </div>
</div>
<?php admin_footer();
