<?php
require __DIR__ . '/_init.php';

$key = (string) ($_GET['r'] ?? '');
if (!isset($RESOURCES[$key])) {
    http_response_code(404);
    exit('Unknown section.');
}
$res    = $RESOURCES[$key];
$table  = $res['table'];
$fields = $res['fields'];
$self   = 'crud.php?r=' . urlencode($key);
$action = (string) ($_GET['action'] ?? 'list');

/* ---------- POST: save / delete ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) {
        flash('danger', 'Your session expired. Please try again.');
        redirect($self);
    }
    $op = $_POST['_op'] ?? '';
    $id = (int) ($_POST['id'] ?? 0);
    $existing = $id ? row("SELECT * FROM $table WHERE id = ?", [$id]) : null;

    if ($op === 'delete') {
        if ($existing) {
            foreach ($fields as $name => $f) {
                if (($f['type'] ?? '') === 'image') {
                    delete_upload($existing[$name]);
                }
            }
            q("DELETE FROM $table WHERE id = ?", [$id]);
            flash('success', $res['singular'] . ' deleted.');
        }
        redirect($self);
    }

    if ($op === 'save') {
        if ($id && !$existing) {
            redirect($self);
        }
        $data = [];
        $errors = [];
        foreach ($fields as $name => $f) {
            $type = $f['type'] ?? 'text';
            $label = $f['label'] ?? $name;
            switch ($type) {
                case 'checkbox':
                    $data[$name] = isset($_POST[$name]) ? 1 : 0;
                    break;
                case 'number':
                    $data[$name] = (int) ($_POST[$name] ?? 0);
                    break;
                case 'image':
                    try {
                        $new = upload_image($name);
                    } catch (RuntimeException $ex) {
                        $errors[] = "$label: " . $ex->getMessage();
                        break;
                    }
                    if ($new) {
                        delete_upload($existing[$name] ?? null);
                        $data[$name] = $new;
                    } elseif (!empty($_POST[$name . '_remove']) && $existing) {
                        delete_upload($existing[$name]);
                        $data[$name] = null;
                    }
                    break;
                case 'slug':
                    $raw = trim((string) ($_POST[$name] ?? ''));
                    $data[$name] = slugify($raw !== '' ? $raw : (string) ($_POST[$f['from']] ?? ''));
                    break;
                default:
                    $data[$name] = trim((string) ($_POST[$name] ?? ''));
                    if ($type === 'email' && $data[$name] !== '' && !filter_var($data[$name], FILTER_VALIDATE_EMAIL)) {
                        $errors[] = "$label must be a valid email address.";
                    }
            }
            if (!empty($f['required']) && $type !== 'image' && (string) ($data[$name] ?? '') === '') {
                $errors[] = "$label is required.";
            }
        }
        foreach ($res['unique'] ?? [] as $col) {
            if (isset($data[$col]) && (int) val("SELECT COUNT(*) FROM $table WHERE $col = ? AND id <> ?", [$data[$col], $id])) {
                $errors[] = ($fields[$col]['label'] ?? $col) . ' "' . $data[$col] . '" is already in use.';
            }
        }

        if ($errors) {
            foreach ($errors as $err) {
                flash('danger', $err);
            }
            $_SESSION['old_input'] = $_POST;
            redirect($self . ($id ? "&action=edit&id=$id" : '&action=new'));
        }

        $ts = $res['timestamps'] ?? [];
        if (in_array('updated_at', $ts, true)) {
            $data['updated_at'] = now();
        }
        if (!$existing && in_array('created_at', $ts, true)) {
            $data['created_at'] = now();
        }

        if ($existing) {
            update_row($table, $id, $data);
        } else {
            $id = insert_row($table, $data);
        }
        flash('success', $res['singular'] . ' saved.');
        redirect(isset($_POST['_stay']) ? "$self&action=edit&id=$id" : $self);
    }
    redirect($self);
}

/* ---------- Add / edit form ---------- */
if ($action === 'new' || $action === 'edit') {
    $item = null;
    if ($action === 'edit') {
        $item = row("SELECT * FROM $table WHERE id = ?", [(int) ($_GET['id'] ?? 0)]);
        if (!$item) {
            flash('warning', $res['singular'] . ' not found.');
            redirect($self);
        }
    } else {
        $item = [];
        foreach ($fields as $name => $f) {
            $item[$name] = $f['default'] ?? '';
        }
    }
    if (!empty($_SESSION['old_input'])) {
        $old = $_SESSION['old_input'];
        foreach ($fields as $name => $f) {
            $type = $f['type'] ?? 'text';
            if ($type === 'image') {
                continue;
            }
            $item[$name] = $type === 'checkbox' ? (isset($old[$name]) ? 1 : 0) : ($old[$name] ?? '');
        }
        unset($_SESSION['old_input']);
    }

    admin_header(($action === 'new' ? 'Add ' : 'Edit ') . $res['singular'], 'crud:' . $key);
    ?>
<div class="mb-3"><a href="<?= e($self) ?>" class="text-decoration-none small"><i class="bi bi-arrow-left"></i> Back to <?= e($res['title']) ?></a></div>
<form method="post" enctype="multipart/form-data" class="card card-body">
  <?= csrf_field() ?>
  <input type="hidden" name="_op" value="save">
  <input type="hidden" name="id" value="<?= (int) ($item['id'] ?? 0) ?>">
  <div class="row">
    <div class="col-xl-9">
<?php foreach ($fields as $name => $f) {
        $v = $item[$name] ?? '';
        render_field($name, $f, ($f['type'] ?? '') === 'checkbox' ? ((int) $v ? '1' : '0') : $v);
    } ?>
    </div>
  </div>
  <div class="d-flex flex-wrap gap-2 border-top pt-3">
    <button class="btn btn-gradient px-4" type="submit"><i class="bi bi-check2 me-1"></i>Save</button>
    <button class="btn btn-outline-secondary" type="submit" name="_stay" value="1">Save &amp; keep editing</button>
<?php if (!empty($res['view_url']) && !empty($item['id'])): ?>
    <a class="btn btn-link" href="../<?= e(str_replace('{slug}', rawurlencode((string) $item['slug']), $res['view_url'])) ?>" target="_blank">View on site <i class="bi bi-box-arrow-up-right"></i></a>
<?php endif; ?>
    <a class="btn btn-link text-secondary ms-auto" href="<?= e($self) ?>">Cancel</a>
  </div>
</form>
<?php
    admin_footer();
    exit;
}

/* ---------- List ---------- */
$hasSort = isset($fields['sort_order']);
$orderBy = $res['order'] ?? ($hasSort ? 'sort_order, id' : 'id DESC');
$items = rows("SELECT * FROM $table ORDER BY $orderBy");

admin_header($res['title'], 'crud:' . $key);
?>
<div class="d-flex align-items-center mb-3">
  <span class="text-secondary small"><?= count($items) ?> <?= count($items) === 1 ? 'item' : 'items' ?></span>
  <a href="<?= e($self) ?>&action=new" class="btn btn-gradient ms-auto"><i class="bi bi-plus-lg me-1"></i>Add <?= e($res['singular']) ?></a>
</div>
<div class="card">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead>
        <tr>
<?php foreach ($res['list'] as $col):
        $label = $col === 'is_active' ? 'Status' : ($col === 'sort_order' ? 'Order' : ($col === 'created_at' ? 'Added' : ($fields[$col]['label'] ?? ucfirst($col)))); ?>
          <th><?= e($label) ?></th>
<?php endforeach; ?>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
<?php if (!$items): ?>
        <tr><td colspan="<?= count($res['list']) + 1 ?>" class="text-center text-secondary py-5">Nothing here yet. Click "Add <?= e($res['singular']) ?>" to create the first one.</td></tr>
<?php endif; ?>
<?php foreach ($items as $it): ?>
        <tr>
<?php foreach ($res['list'] as $col):
            $type = $fields[$col]['type'] ?? 'text';
            $v = $it[$col] ?? ''; ?>
          <td>
<?php if ($type === 'image'): ?>
            <?= $v ? '<img src="' . e(media_url($v)) . '" class="thumb" alt="">' : '<span class="thumb thumb-empty"><i class="bi bi-image"></i></span>' ?>
<?php elseif ($type === 'icon'): ?>
            <span class="icon-chip"><i class="bi <?= e($v) ?>"></i></span>
<?php elseif ($col === 'is_active'): ?>
            <?= (int) $v ? '<span class="badge text-bg-success-subtle text-success">Visible</span>' : '<span class="badge text-bg-secondary">Hidden</span>' ?>
<?php elseif ($type === 'checkbox'): ?>
            <?= (int) $v ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<span class="text-secondary">—</span>' ?>
<?php elseif ($col === 'created_at'): ?>
            <span class="small text-secondary"><?= $v ? e(date('M j, Y', strtotime($v))) : '' ?></span>
<?php else: ?>
            <?= e(str_limit((string) $v, 70)) ?>
<?php endif; ?>
          </td>
<?php endforeach; ?>
          <td class="text-end text-nowrap">
            <a href="<?= e($self) ?>&action=edit&id=<?= (int) $it['id'] ?>" class="btn btn-sm btn-light" title="Edit"><i class="bi bi-pencil"></i></a>
            <form method="post" class="d-inline" onsubmit="return confirm('Delete this <?= e(strtolower($res['singular'])) ?>? This cannot be undone.');">
              <?= csrf_field() ?>
              <input type="hidden" name="_op" value="delete">
              <input type="hidden" name="id" value="<?= (int) $it['id'] ?>">
              <button class="btn btn-sm btn-light text-danger" title="Delete"><i class="bi bi-trash"></i></button>
            </form>
          </td>
        </tr>
<?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php admin_footer();
