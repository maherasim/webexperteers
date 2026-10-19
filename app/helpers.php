<?php
declare(strict_types=1);

/* ---------- Output ---------- */

function e($value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Escape text, then turn *words* into gradient-highlighted words. */
function hl($value): string
{
    return preg_replace('/\*(.+?)\*/u', '<span class="text-gradient">$1</span>', e($value));
}

/** Split a textarea into trimmed, non-empty lines. */
function lines($value): array
{
    $parts = preg_split('/\R/u', (string) $value) ?: [];
    return array_values(array_filter(array_map('trim', $parts), fn($l) => $l !== ''));
}

function str_limit(string $s, int $max = 80): string
{
    return mb_strlen($s) > $max ? mb_substr($s, 0, $max - 1) . '…' : $s;
}

function slugify(string $s): string
{
    $s = strtolower(trim((string) preg_replace('/[^A-Za-z0-9]+/', '-', $s), '-'));
    return $s !== '' ? $s : 'item';
}

function initials(string $name): string
{
    $out = '';
    foreach (preg_split('/\s+/', trim($name)) ?: [] as $part) {
        if ($part !== '') {
            $out .= mb_strtoupper(mb_substr($part, 0, 1));
        }
    }
    return mb_substr($out, 0, 2) ?: '?';
}

/* ---------- Settings & content ---------- */

function settings_all(bool $refresh = false): array
{
    static $cache = null;
    if ($cache === null || $refresh) {
        $cache = [];
        try {
            foreach (rows('SELECT setting_key, setting_value FROM settings') as $r) {
                $cache[$r['setting_key']] = (string) $r['setting_value'];
            }
        } catch (Throwable $e) {
            // Not installed yet.
        }
    }
    return $cache;
}

function setting(string $key, string $default = ''): string
{
    return settings_all()[$key] ?? $default;
}

function on(string $key): bool
{
    return setting($key, '0') === '1';
}

function save_setting(string $key, string $value): void
{
    if ((int) val('SELECT COUNT(*) FROM settings WHERE setting_key = ?', [$key])) {
        q('UPDATE settings SET setting_value = ? WHERE setting_key = ?', [$value, $key]);
    } else {
        q('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)', [$key, $value]);
    }
}

function active_rows(string $table): array
{
    return rows("SELECT * FROM $table WHERE is_active = 1 ORDER BY sort_order, id");
}

function wa_link(string $text = ''): string
{
    $num = preg_replace('/\D+/', '', setting('whatsapp_number'));
    return 'https://wa.me/' . $num . ($text !== '' ? '?text=' . rawurlencode($text) : '');
}

/* ---------- URLs ---------- */

/** Root-relative URL for an internal path, e.g. url('services/seo') => /services/seo */
function url(string $path = ''): string
{
    return rtrim((string) config('base_path', ''), '/') . '/' . ltrim($path, '/');
}

/** URL for a stored file path such as uploads/x.webp; empty stays empty. */
function media(?string $path): string
{
    return $path ? url($path) : '';
}

/** Admin-entered links: keep absolute/anchor/mailto links as-is, make internal paths root-relative. */
function href(?string $link, string $fallback = 'contact'): string
{
    $link = trim((string) $link);
    if ($link === '') {
        return url($fallback);
    }
    if (preg_match('~^(https?:|mailto:|tel:|#|//)~i', $link)) {
        return $link;
    }
    return url($link);
}

/** Render a reusable section partial from app/views/sections with its own variables. */
function section(string $name, array $vars = []): void
{
    extract($vars, EXTR_SKIP);
    require APP . "/views/sections/$name.php";
}

/* ---------- Requests ---------- */

function is_https(): bool
{
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https'
        || ($_SERVER['SERVER_PORT'] ?? '') === '443';
}

function client_ip(): string
{
    return substr((string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'), 0, 45);
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function json_out(array $data, int $code = 200): never
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

/** Reject cross-site POSTs to public endpoints (browsers always send Origin on fetch POST). */
function same_origin(): bool
{
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    if ($origin === '') {
        return true;
    }
    return parse_url($origin, PHP_URL_HOST) === parse_url('http://' . ($_SERVER['HTTP_HOST'] ?? ''), PHP_URL_HOST);
}

function now(): string
{
    return date('Y-m-d H:i:s');
}

/* ---------- CSRF & flash (admin, session required) ---------- */

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
}

function csrf_valid(): bool
{
    $t = $_POST['_token'] ?? '';
    return is_string($t) && !empty($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $t);
}

function flash(string $type, string $msg): void
{
    $_SESSION['flash'][] = [$type, $msg];
}

function take_flashes(): array
{
    $f = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $f;
}

/* ---------- Uploads ---------- */

/**
 * Store an uploaded image under /uploads and return its relative path,
 * or null when no file was sent. Throws RuntimeException on invalid input.
 */
function upload_image(string $field): ?string
{
    if (empty($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    $f = $_FILES[$field];
    if ($f['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload failed (error code ' . $f['error'] . ').');
    }
    if ($f['size'] > 5 * 1024 * 1024) {
        throw new RuntimeException('Image must be 5 MB or smaller.');
    }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
    $ext = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
        'image/x-icon' => 'ico',
        'image/vnd.microsoft.icon' => 'ico',
    ][$mime] ?? null;
    if (!$ext) {
        throw new RuntimeException('Only JPG, PNG, WEBP, GIF or ICO images are allowed.');
    }
    $dir = ROOT . '/uploads';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $name = date('Ymd') . '-' . bin2hex(random_bytes(6)) . '.' . $ext;
    if (!move_uploaded_file($f['tmp_name'], "$dir/$name")) {
        throw new RuntimeException('Could not save the uploaded file.');
    }
    return 'uploads/' . $name;
}

function delete_upload(?string $path): void
{
    if ($path && str_starts_with($path, 'uploads/') && !str_contains($path, '..')) {
        @unlink(ROOT . '/' . $path);
    }
}

/* ---------- Mail ---------- */

function send_mail(string $to, string $subject, string $body, string $replyTo = ''): bool
{
    if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    $from = config('mail_from', 'no-reply@' . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
    $headers = [
        'From: ' . setting('site_name', 'Website') . ' <' . $from . '>',
        'Content-Type: text/plain; charset=UTF-8',
    ];
    if ($replyTo !== '' && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
        $headers[] = 'Reply-To: ' . $replyTo;
    }
    $subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
    return @mail($to, $subject, $body, implode("\r\n", $headers));
}
