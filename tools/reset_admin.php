<?php
// php-blog/tools/reset_admin.php
// یک‌بار اجرا کن، بعد فایل را حذف کن.
require __DIR__ . '/../config/bootstrap.php';

$username = 'admin';
$newPassword = 'admin'; // رمز دلخواه؛ الان همون "admin"

// هش امن با bcrypt
$hash = password_hash($newPassword, PASSWORD_BCRYPT);

// اگر کاربر هست، آپدیت کن؛ وگرنه بساز
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
$stmt->execute([$username]);
$row = $stmt->fetch();

if ($row) {
  $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
  $stmt->execute([$hash, (int)$row['id']]);
  echo "✅ Password updated for user '{$username}'.";
} else {
  $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, 'admin')");
  $stmt->execute([$username, $hash]);
  echo "✅ Admin user created with username '{$username}'.";
}

// نکته: بعد از موفقیت، این فایل را پاک کن.
