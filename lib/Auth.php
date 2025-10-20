<?php
// lib/Auth.php
class Auth {
  private PDO $pdo;

  public function __construct(PDO $pdo) {
    $this->pdo = $pdo;
    if (session_status() !== PHP_SESSION_ACTIVE) {
      session_start([
        'cookie_httponly' => true,
        'cookie_secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'cookie_samesite' => 'Lax',
      ]);
    }
  }

  public function login(string $username, string $password): bool {
    $stmt = $this->pdo->prepare("SELECT id, username, password_hash, role FROM users WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $u = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$u) return false;
    if (!password_verify($password, $u['password_hash'])) return false;

    session_regenerate_id(true);
    // 👇 مهم: id هم داخل سشن ذخیره شود
    $_SESSION['user'] = [
      'id'       => (int)$u['id'],
      'username' => $u['username'],
      'role'     => $u['role'],
    ];
    return true;
  }

  public function logout(): void {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
      $params = session_get_cookie_params();
      setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
  }

  public function check(): bool {
    return isset($_SESSION['user']['id']);
  }

  public function user(): ?array {
    return $_SESSION['user'] ?? null;
  }

  public function id(): int {
    return isset($_SESSION['user']['id']) ? (int)$_SESSION['user']['id'] : 0;
  }

  public function role(): ?string {
    return $_SESSION['user']['role'] ?? null;
  }

  // اجازهٔ دسترسی: editor => (editor, admin) / admin => (admin)
  public function requireRole(string $role): void {
    if (!$this->check()) {
      redirect('/admin/index.php');
    }
    $current = $this->role();
    if ($role === 'admin' && $current !== 'admin') {
      http_response_code(403); exit('Forbidden');
    }
    if ($role === 'editor' && !in_array($current, ['admin','editor'], true)) {
      http_response_code(403); exit('Forbidden');
    }
  }
}
