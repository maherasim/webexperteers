<?php
require __DIR__ . '/_init.php';

$count = fn(string $sql) => (int) val($sql);
$cards = [
    ['Unread messages', $count('SELECT COUNT(*) FROM messages WHERE is_read = 0'), 'bi-envelope-exclamation', 'messages.php', 'c-1'],
    ['Total messages', $count('SELECT COUNT(*) FROM messages'), 'bi-inbox', 'messages.php', 'c-2'],
    ['Subscribers', $count('SELECT COUNT(*) FROM subscribers'), 'bi-people', 'crud.php?r=subscribers', 'c-3'],
    ['Portfolio projects', $count('SELECT COUNT(*) FROM projects'), 'bi-briefcase', 'crud.php?r=projects', 'c-4'],
];
$recent = rows('SELECT id, name, email, service, is_read, created_at FROM messages ORDER BY id DESC LIMIT 6');

admin_header('Dashboard', 'dashboard');
?>
<div class="row g-3 mb-4">
<?php foreach ($cards as [$label, $num, $icon, $href, $cls]): ?>
  <div class="col-6 col-xl-3">
    <a href="<?= e($href) ?>" class="stat-card <?= $cls ?>">
      <div><div class="stat-num"><?= $num ?></div><div class="stat-label"><?= e($label) ?></div></div>
      <i class="bi <?= e($icon) ?>"></i>
    </a>
  </div>
<?php endforeach; ?>
</div>

<div class="row g-4">
  <div class="col-xl-8">
    <div class="card">
      <div class="card-header d-flex align-items-center">
        <span>Latest messages</span>
        <a href="messages.php" class="ms-auto small">View all</a>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <tbody>
<?php if (!$recent): ?>
            <tr><td class="text-center text-secondary py-4">No messages yet. They will appear here when visitors use the contact form.</td></tr>
<?php endif; ?>
<?php foreach ($recent as $m): ?>
            <tr class="<?= $m['is_read'] ? '' : 'fw-semibold' ?>">
              <td><a href="messages.php?id=<?= (int) $m['id'] ?>" class="text-reset text-decoration-none"><?= $m['is_read'] ? '' : '<span class="dot"></span>' ?><?= e($m['name']) ?></a></td>
              <td class="text-secondary small d-none d-md-table-cell"><?= e($m['email']) ?></td>
              <td class="small d-none d-lg-table-cell"><?= e($m['service']) ?></td>
              <td class="text-secondary small text-end"><?= e(date('M j, g:i a', strtotime($m['created_at']))) ?></td>
            </tr>
<?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="col-xl-4">
    <div class="card">
      <div class="card-header">Quick edit</div>
      <div class="list-group list-group-flush">
        <a href="settings.php?g=hero" class="list-group-item list-group-item-action"><i class="bi bi-stars me-2"></i>Hero section</a>
        <a href="settings.php?g=contact" class="list-group-item list-group-item-action"><i class="bi bi-envelope me-2"></i>Contact details</a>
        <a href="settings.php?g=visibility" class="list-group-item list-group-item-action"><i class="bi bi-eye me-2"></i>Show / hide sections</a>
        <a href="crud.php?r=projects&action=new" class="list-group-item list-group-item-action"><i class="bi bi-plus-circle me-2"></i>Add portfolio project</a>
        <a href="crud.php?r=testimonials" class="list-group-item list-group-item-action"><i class="bi bi-chat-quote me-2"></i>Testimonials</a>
        <a href="crud.php?r=pages" class="list-group-item list-group-item-action"><i class="bi bi-file-earmark-text me-2"></i>Pages</a>
      </div>
    </div>
  </div>
</div>
<?php admin_footer();
