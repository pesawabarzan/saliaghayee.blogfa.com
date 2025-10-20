<?php
/** lib/CSRF.php
 * توکن CSRF برای فرم‌های حساس
 */
class CSRF {
  public static function init(): void {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (!isset($_SESSION['csrf_token'])) {
      $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
  }

  public static function token(): string {
    self::init();
    return $_SESSION['csrf_token'];
  }

  public static function validate(string $token): bool {
    self::init();
    return hash_equals($_SESSION['csrf_token'], $token);
  }

  public static function input(): string {
    $t = self::token();
    return '<input type="hidden" name="csrf_token" value="'.htmlspecialchars($t, ENT_QUOTES, 'UTF-8').'">';
  }
}
