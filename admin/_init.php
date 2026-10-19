<?php
declare(strict_types=1);

require dirname(__DIR__) . '/app/bootstrap.php';

start_session();
header('X-Frame-Options: DENY');
header('Cache-Control: no-store');
header('X-Robots-Tag: noindex, nofollow');

const ADMIN_IDLE_TIMEOUT = 7200; // seconds

$RESOURCES = require APP . '/resources.php';

function current_admin(): ?array
{
    static $admin = false;
    if ($admin === false) {
        $admin = !empty($_SESSION['admin_id'])
            ? row('SELECT id, name, email FROM admins WHERE id = ?', [$_SESSION['admin_id']])
            : null;
    }
    return $admin;
}

function require_admin(): void
{
    if (!empty($_SESSION['last_seen']) && time() - $_SESSION['last_seen'] > ADMIN_IDLE_TIMEOUT) {
        $_SESSION = [];
        session_regenerate_id(true);
        flash('warning', 'You were logged out after a period of inactivity.');
    }
    if (!current_admin()) {
        redirect('login.php');
    }
    $_SESSION['last_seen'] = time();
}

if (!defined('ADMIN_PUBLIC')) {
    if (!db_ready()) {
        exit('Database not installed. Run: php app/install.php --email=you@example.com');
    }
    require_admin();
}

/** Admin pages live one level below the site root. */
function media_url(?string $path): string
{
    return $path ? '../' . $path : '';
}

function admin_header(string $title, string $active = ''): void
{
    global $RESOURCES;
    $unread = (int) val('SELECT COUNT(*) FROM messages WHERE is_read = 0');
    $admin  = current_admin();
    $nav = function (string $href, string $icon, string $label, string $key, string $badge = '') use ($active) {
        $cls = $key === $active ? ' active' : '';
        echo '<a class="side-link' . $cls . '" href="' . e($href) . '"><i class="bi ' . e($icon) . '"></i><span>' . e($label) . '</span>' . $badge . '</a>';
    };
    ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <title><?= e($title) ?> · Admin · <?= e(setting('site_name')) ?></title>
  <link rel="icon" href="../<?= e(setting('favicon') ?: 'assets/img/favicon.svg') ?>">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
  <link href="assets/admin.css?v=3" rel="stylesheet">
</head>
<body>
  <aside class="sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="sidebar">
    <div class="sidebar-brand">
      <img src="../assets/img/logo-mark.svg" alt="" class="brand-mark-img">
      <span><?= e(setting('site_name')) ?></span>
      <button type="button" class="btn-close btn-close-white d-lg-none ms-auto" data-bs-dismiss="offcanvas" data-bs-target="#sidebar" aria-label="Close"></button>
    </div>
    <nav class="sidebar-nav">
      <?php
      $nav('index.php', 'bi-speedometer2', 'Dashboard', 'dashboard');
      $nav('messages.php', 'bi-inbox', 'Messages', 'messages', $unread ? '<span class="badge rounded-pill ms-auto">' . $unread . '</span>' : '');
      echo '<div class="side-label">Content</div>';
      $nav('settings.php', 'bi-sliders', 'Site Settings', 'settings');
      foreach ($RESOURCES as $key => $res) {
          if ($key === 'subscribers') {
              continue;
          }
          $nav('crud.php?r=' . $key, $res['icon'], $res['title'], 'crud:' . $key);
      }
      echo '<div class="side-label">Audience</div>';
      $nav('crud.php?r=subscribers', 'bi-people', 'Subscribers', 'crud:subscribers');
      echo '<div class="side-label">Account</div>';
      $nav('profile.php', 'bi-person-circle', 'My Profile', 'profile');
      $nav('../index.php', 'bi-box-arrow-up-right', 'View Website', 'site');
      ?>
    </nav>
  </aside>

  <div class="main">
    <header class="topbar">
      <button class="btn btn-light d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar" aria-label="Menu"><i class="bi bi-list"></i></button>
      <h1 class="topbar-title"><?= e($title) ?></h1>
      <div class="dropdown ms-auto">
        <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="bi bi-person-circle me-1"></i> <span class="d-none d-sm-inline"><?= e($admin['name'] ?? '') ?></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i>My Profile</a></li>
          <li><hr class="dropdown-divider"></li>
          <li>
            <form action="logout.php" method="post"><?= csrf_field() ?>
              <button class="dropdown-item text-danger" type="submit"><i class="bi bi-box-arrow-right me-2"></i>Log out</button>
            </form>
          </li>
        </ul>
      </div>
    </header>
    <main class="content">
<?php foreach (take_flashes() as [$type, $msg]): ?>
      <div class="alert alert-<?= e($type) ?> alert-dismissible fade show" role="alert">
        <?= e($msg) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
<?php endforeach;
}

function admin_footer(): void
{
    ?>
    </main>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
  <script src="assets/admin.js?v=1"></script>
</body>
</html>
<?php
}

/** Render one form field. Used by both crud.php and settings.php. */
function render_field(string $name, array $f, $value): void
{
    $type  = $f['type'] ?? 'text';
    $label = $f['label'] ?? ucfirst(str_replace('_', ' ', $name));
    $id    = 'f_' . $name;
    $req   = !empty($f['required']) ? ' required' : '';
    $value = (string) ($value ?? '');
    echo '<div class="mb-3">';

    if ($type === 'checkbox') {
        echo '<div class="form-check form-switch">'
            . '<input class="form-check-input" type="checkbox" role="switch" id="' . $id . '" name="' . e($name) . '" value="1"' . ($value === '1' ? ' checked' : '') . '>'
            . '<label class="form-check-label" for="' . $id . '">' . e($label) . '</label></div>';
    } else {
        echo '<label class="form-label" for="' . $id . '">' . e($label) . ($req ? ' <span class="text-danger">*</span>' : '') . '</label>';
        switch ($type) {
            case 'textarea':
            case 'lines':
                echo '<textarea class="form-control" id="' . $id . '" name="' . e($name) . '" rows="' . ($type === 'lines' ? 5 : 3) . '"' . $req . '>' . e($value) . '</textarea>';
                break;
            case 'code':
                echo '<textarea class="form-control font-monospace small" id="' . $id . '" name="' . e($name) . '" rows="5" spellcheck="false">' . e($value) . '</textarea>';
                break;
            case 'html':
                echo '<div class="rich-editor" data-target="' . $id . '"></div>'
                    . '<textarea class="d-none" id="' . $id . '" name="' . e($name) . '">' . e($value) . '</textarea>';
                break;
            case 'number':
                echo '<input type="number" class="form-control" id="' . $id . '" name="' . e($name) . '" value="' . e($value) . '"' . $req . '>';
                break;
            case 'select':
                echo '<select class="form-select" id="' . $id . '" name="' . e($name) . '">';
                foreach ($f['options'] as $optVal => $optLabel) {
                    $sel = (string) $optVal === $value ? ' selected' : '';
                    echo '<option value="' . e($optVal) . '"' . $sel . '>' . e($optLabel) . '</option>';
                }
                echo '</select>';
                break;
            case 'icon':
                echo '<div class="input-group">'
                    . '<span class="input-group-text icon-preview"><i class="bi ' . e($value) . '"></i></span>'
                    . '<input type="text" class="form-control icon-input" id="' . $id . '" name="' . e($name) . '" value="' . e($value) . '" placeholder="bi-star">'
                    . '<a class="btn btn-outline-secondary" href="https://icons.getbootstrap.com/" target="_blank" rel="noopener">Browse icons</a>'
                    . '</div>';
                $f['help'] = $f['help'] ?? 'Bootstrap Icons class, e.g. bi-rocket. Click "Browse icons", pick one and copy its name (add "bi-" in front).';
                break;
            case 'image':
                if ($value !== '') {
                    echo '<div class="image-current mb-2"><img src="' . e(media_url($value)) . '" alt="">'
                        . '<div class="form-check"><input class="form-check-input" type="checkbox" name="' . e($name) . '_remove" value="1" id="' . $id . '_rm">'
                        . '<label class="form-check-label small" for="' . $id . '_rm">Remove image</label></div></div>';
                }
                echo '<input type="file" class="form-control" id="' . $id . '" name="' . e($name) . '" accept="image/jpeg,image/png,image/webp,image/gif,image/x-icon">';
                break;
            default:
                $inputType = in_array($type, ['email', 'url'], true) ? $type : 'text';
                echo '<input type="' . $inputType . '" class="form-control" id="' . $id . '" name="' . e($name) . '" value="' . e($value) . '"' . $req . '>';
        }
    }

    if (!empty($f['help'])) {
        echo '<div class="form-text">' . e($f['help']) . '</div>';
    }
    echo '</div>';
}
