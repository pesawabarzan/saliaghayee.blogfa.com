<?php
require __DIR__ . '/../config/bootstrap.php';
$auth = new Auth($pdo);
$BASE = rtrim($CONFIG['base_url'] ?: base_url(), '/');

if ($auth->check()) redirect('/admin/dashboard.php');

CSRF::init();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($_POST['csrf_token']) || !CSRF::validate($_POST['csrf_token'])) {
    $errors[] = 'توکن نامعتبر است.';
  } else {
    $u = trim($_POST['username'] ?? '');
    $p = trim($_POST['password'] ?? '');
    if (!$auth->login($u, $p)) $errors[] = 'نام کاربری یا رمز اشتباه است.';
    else redirect('/admin/dashboard.php');
  }
}
$page_title = 'ورود مدیر';
include __DIR__ . '/../templates/partials/header.php';
?>
<h1>ورود</h1>
<?php if ($errors): ?>
  <div class="alert error"><ul><?php foreach ($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>
<form method="post" action="<?= $BASE ?>/admin/index.php" class="auth-form">
  <?= CSRF::input(); ?>
  <label>نام کاربری</label>
  <input name="username" required>
  <label>رمز عبور</label>
  <input type="password" name="password" required>
  <button type="submit">ورود</button>
</form>
<?php include __DIR__ . '/../templates/partials/footer.php'; ?>
