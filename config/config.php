<?php
/**
 * config/config.php — نسخه Production-ready
 * - روی سرور، متغیرهای محیطی (ENV) را ست کن.
 * - در لوکال، مقادیر پیش‌فرض زیر فعال می‌شوند.
 */

function env(string $key, $default = null) {
  $v = getenv($key);
  return ($v === false || $v === '') ? $default : $v;
}

$CONFIG = [
  // ===== Database =====
  'db_host' => env('DB_HOST', 'localhost'),
  'db_name' => env('DB_NAME', 'blog_db'),
  'db_user' => env('DB_USER', 'root'),
  'db_pass' => env('DB_PASS', ''),

  // ===== Base URL =====
  // روی سرور حتماً BASE_URL را ست کن (بدون اسلش آخر).
  'base_url' => rtrim(env('BASE_URL', 'http://localhost/php-blog'), '/'),

  // ===== App =====
  'app_secret'     => env('APP_SECRET', 'CHANGE_THIS_IN_PROD'),
  'display_errors' => env('APP_DEBUG', '0') === '1' ? true : false,

  // ===== Admin Contact =====
  'admin_email' => env('ADMIN_EMAIL', 'admin@example.com'),

  // ===== SMTP (برای فرم تماس) =====
  'smtp_enabled' => env('SMTP_ENABLED', '0') === '1',
  'smtp_host'    => env('SMTP_HOST', 'smtp.example.com'),
  'smtp_port'    => (int) env('SMTP_PORT', 587),
  'smtp_secure'  => env('SMTP_SECURE', 'tls'),  // tls | ssl
  'smtp_user'    => env('SMTP_USER', ''),
  'smtp_pass'    => env('SMTP_PASS', ''),

  // ===== Comments =====
  'comments' => [
    'enabled'            => env('COMMENTS_ENABLED', '1') === '1',
    'require_moderation' => env('COMMENTS_REQUIRE_MOD', '1') === '1',
    'min_secs_between'   => (int) env('COMMENTS_MIN_SECS', 60),
  ],
];

// تنظیم نمایش خطا بر اساس پیکربندی
if ($CONFIG['display_errors']) {
  ini_set('display_errors', '1');
  error_reporting(E_ALL);
} else {
  ini_set('display_errors', '0');
}
