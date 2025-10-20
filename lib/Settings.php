<?php
/** lib/Settings.php
 * خواندن/نوشتن تنظیمات از جدول settings
 */
function get_setting(PDO $pdo, string $key, $default = null) {
  $stmt = $pdo->prepare("SELECT value FROM settings WHERE `key` = ? LIMIT 1");
  $stmt->execute([$key]);
  $row = $stmt->fetch();
  return $row ? $row['value'] : $default;
}

function set_setting(PDO $pdo, string $key, string $value): bool {
  $stmt = $pdo->prepare("INSERT INTO settings(`key`, `value`) VALUES(?, ?) ON DUPLICATE KEY UPDATE value = VALUES(value)");
  return $stmt->execute([$key, $value]);
}
