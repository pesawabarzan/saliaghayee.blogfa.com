<?php
/**
 * config/config.php — تنظیمات اصلی برنامه
 * - در محیط تولید، حتماً app_secret، اطلاعات SMTP و نمایش خطاها را به‌روز کنید.
 */

$CONFIG = [
  // ===== Database =====
  'db_host' => 'localhost',
  'db_name' => 'blog_db',
  'db_user' => 'root',
  'db_pass' => '',

  // ===== Base URL =====
  // برای لوکال XAMPP/WAMP:
  'base_url' => 'http://localhost/php-blog',
  // در هاست/دامنه واقعی، این را به آدرس دامنه تغییر دهید. مثال:
  // 'base_url' => 'https://example.com',

  // ===== App =====
  'app_secret' => 'CHANGE_THIS_IN_PROD', // در تولید، مقدار امن و طولانی بگذارید.
  'display_errors' => true,              // فقط در لوکال. در تولید: false

  // ===== Admin Contact =====
  'admin_email' => 'admin@example.com',  // ایمیل مقصد فرم تماس/فوتر

  // ===== SMTP (برای ارسال ایمیل فرم تماس) =====
  'smtp_enabled' => false,               // در لوکال خاموش؛ در تولید اگر لازم شد true
  'smtp_host'    => 'smtp.example.com',
  'smtp_port'    => 587,
  'smtp_secure'  => 'tls',               // 'tls' یا 'ssl'
  'smtp_user'    => '',
  'smtp_pass'    => '',

  // ===== Comments (نظرات) =====
  'comments' => [
    'enabled'           => true,  // نمایش فرم نظر زیر هر پست
    'require_moderation'=> true,  // تأیید مدیر قبل از نمایش
    'min_secs_between'  => 60,    // ریت‌لیمیت بر اساس IP (ثانیه)
  ],
];
