<?php
require __DIR__ . '/../config/bootstrap.php';
$auth = new Auth($pdo);
$auth->requireRole('admin');
$BASE = rtrim($CONFIG['base_url'] ?: base_url(), '/');
$page_title = 'پیام‌های تماس';

if (isset($_GET['toggle'])) {
  CSRF::init();
  if (!isset($_GET['csrf']) || !CSRF::validate($_GET['csrf'])) { http_response_code(400); exit('CSRF نامعتبر'); }
  $id = (int)$_GET['toggle'];
  $pdo->prepare("UPDATE contact_messages SET is_read = 1 - is_read WHERE id = ?")->execute([$id]);
  redirect('/admin/messages.php');
}
if (isset($_GET['delete'])) {
  CSRF::init();
  if (!isset($_GET['csrf']) || !CSRF::validate($_GET['csrf'])) { http_response_code(400); exit('CSRF نامعتبر'); }
  $id = (int)$_GET['delete'];
  $pdo->prepare("DELETE FROM contact_messages WHERE id = ?")->execute([$id]);
  redirect('/admin/messages.php');
}

/*include __DIR__ . '/../templates/partials/header.php';*/
include __DIR__ . '/../templates/partials/admin_nav.php';

$rows = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll();
?>
<h1>پیام‌ها</h1>
<table class="table">
  <thead><tr><th>ID</th><th>نام</th><th>ایمیل</th><th>موضوع</th><th>تاریخ</th><th>وضعیت</th><th>عملیات</th></tr></thead>
  <tbody>
  <?php foreach ($rows as $r): ?>
    <tr>
      <td><?= $r['id'] ?></td>
      <td><?= e($r['name']) ?></td>
      <td><?= e($r['email']) ?></td>
      <td><?= e($r['subject']) ?></td>
      <td><?= e(date('Y/m/d H:i', strtotime($r['created_at']))) ?></td>
      <td><?= $r['is_read'] ? 'خوانده شده' : 'خوانده نشده' ?></td>
      <td>
        <a href="<?= $BASE ?>/admin/messages.php?toggle=<?= $r['id'] ?>&csrf=<?= CSRF::token(); ?>">تغییر وضعیت</a>
        <a class="danger" href="<?= $BASE ?>/admin/messages.php?delete=<?= $r['id'] ?>&csrf=<?= CSRF::token(); ?>" onclick="return confirm('حذف پیام؟')">حذف</a>
      </td>
    </tr>
    <tr><td colspan="7"><pre class="msg-body"><?= e($r['message']) ?></pre></td></tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php include __DIR__ . '/../templates/partials/footer.php'; ?>
