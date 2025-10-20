# PHP Blog (Production-Ready)

نسخه‌ی آماده برای هاست اشتراکی/VPS با PHP 7.4+، MySQL، PDO، و تم روشن/تاریک.

## نصب سریع
1. یک دیتابیس MySQL بسازید.
2. فایل‌های `db/schema.sql` و سپس `db/sample_data.sql` را ایمپورت کنید.
3. فایل `config/config.php` را با مقادیر واقعی پر کنید (از نمونه‌ی زیر استفاده کنید).
4. پوشه پروژه را روی هاست کپی کنید. اطمینان بگیرید `mod_rewrite` در Apache فعال است.
5. دسترسی به `/admin` و ورود با کاربر پیش‌فرض:
   - **username:** `admin`
   - **password:** `Admin@12345` (حتماً در اولین ورود از صفحه‌ی کاربران/SQL تغییر دهید)

> **نکته امنیتی:** مقدار `app_secret` و رمز ادمین را فوراً تغییر دهید.

## امنیت و پیکربندی
- در `config/config.php` مقدار `display_errors=false` برای production.
- `Options -Indexes` در `.htaccess` برای جلوگیری از لیست‌برداری پوشه‌ها.
- دسترسی به `/config` و `/db` مسدود شده است.
- تمام کوئری‌ها با Prepared Statements از طریق PDO.
- CSRF token برای فرم‌های حساس (login، ایجاد/ویرایش پست، تماس).
- خروجی‌گذاری امن (`e()` و `md_to_html()` بدون HTML خام).
- نشست‌ها با `httponly` و `samesite=Lax`؛ `session_regenerate_id` هنگام ورود.
- ری‌رایت آدرس پست‌ها: `/post/{slug}` به `post.php?slug=...`

## SMTP (ارسال ایمیل)
- پیش‌فرض از کلاس `SimpleMailer` استفاده می‌شود (SMTP ساده). برای محیط‌های تولید پیشنهاد می‌شود PHPMailer را نصب کنید:
  - کپی پوشه PHPMailer در `vendor/PHPMailer` و تغییر `lib/SimpleMailer.php` برای استفاده از آن.
- تنظیمات SMTP را در `config/config.php` اعمال کنید.

## ساختار پوشه‌ها
```
index.php
post.php
archive.php
about.php
contact.php
search.php
admin/
  index.php
  dashboard.php
  posts.php
  post_edit.php
  messages.php
  export.php
  import.php
  logout.php
assets/
  css/
    light.css
    dark.css
  js/
    theme-toggle.js
  img/
config/
  config.php           (از نمونه استفاده کنید)
  bootstrap.php
db/
  schema.sql
  sample_data.sql
lib/
  Auth.php
  CSRF.php
  Database.php
  Markdown.php
  Settings.php
  SimpleMailer.php
  Utils.php
templates/
  partials/
    header.php
    footer.php
.htaccess
README.md
```

## نکات Deploy
### Apache
- فعال بودن `mod_rewrite`
- `AllowOverride All` برای دایرکتوری پروژه
- `display_errors=Off` در php.ini یا `.htaccess`

### Nginx (نمونه)
```nginx
server {
  listen 80;
  server_name example.com;
  root /var/www/blog;

  index index.php index.html;

  location / {
    try_files $uri $uri/ /index.php?$args;
  }

  location ~ \.php$ {
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    fastcgi_pass unix:/run/php/php8.1-fpm.sock;
  }

  location ~ ^/(config|db)/ { deny all; }
}
```

## چک‌لیست امنیتی قبل از آپلود
- [ ] تغییر رمز ادمین و ایجاد کاربر جدید نقش editor در صورت نیاز
- [ ] تغییر `app_secret` در `config/config.php`
- [ ] تنظیم `display_errors=false`
- [ ] اطمینان از مجوز فایل‌ها/پوشه‌ها (`644` فایل‌ها، `755` پوشه‌ها)
- [ ] فعال بودن HTTPS و گواهی SSL
- [ ] محدودسازی IP برای /admin در سطح وب‌سرور (اختیاری)

## سفارشی‌سازی
- فونت: لینک CDN در `templates/partials/header.php` را به فونت دلخواه تغییر دهید.
- رنگ‌ها/تم: در `assets/css/light.css` و `assets/css/dark.css`.
- افزودن پست: از پنل `/admin` یا import JSON.
- اسلاگ خودکار از عنوان هنگام ایجاد پست (در صورت خالی بودن فیلد اسلاگ).

## یادداشت
- هش نمونه‌ی ادمین در `db/sample_data.sql` صرفاً placeholder است؛ در اولین ورود، رمز را تغییر دهید یا مستقیماً در DB یک `password_hash()` جدید بنویسید.
