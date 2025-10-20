<?php
/** config/bootstrap.php
 * راه‌اندازی اولیه: لود کانفیگ، ایجاد PDO، include کتابخانه‌ها، تنظیم error handling
 */
require __DIR__ . '/config.php';
require __DIR__ . '/../lib/Database.php';
require __DIR__ . '/../lib/Utils.php';
require __DIR__ . '/../lib/Settings.php';
require __DIR__ . '/../lib/CSRF.php';
require __DIR__ . '/../lib/Auth.php';
require __DIR__ . '/../lib/Markdown.php';
require __DIR__ . '/../lib/SimpleMailer.php';

// خطاها در production مخفی و در لاگ ذخیره شوند
if ($CONFIG['display_errors'] ?? false) {
  ini_set('display_errors', '1');
  error_reporting(E_ALL);
} else {
  ini_set('display_errors', '0');
  error_reporting(E_ALL & ~E_NOTICE);
}

$db = new Database($CONFIG);
$pdo = $db->pdo;

// محدودسازی روش‌های خطرناک در صورت فعال بودن magic quotes (قدیمی)، غیرضروری در نسخه‌های جدید
if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
  function stripslashes_deep($value) { return is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value); }
  $_POST = array_map('stripslashes_deep', $_POST);
  $_GET = array_map('stripslashes_deep', $_GET);
  $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
}

// محافظت از کلیک‌جکینگ
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
