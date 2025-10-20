<?php
require __DIR__ . '/../config/bootstrap.php';
$auth = new Auth($pdo);
if (!$auth->check()) redirect('/admin/index.php');

$BASE = rtrim($CONFIG['base_url'] ?: base_url(), '/');

$cnt_posts  = (int)$pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
$cnt_unread = (int)$pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn();

$page_title = 'داشبورد';
/*include __DIR__ . '/../templates/partials/header.php';*/
include __DIR__ . '/../templates/partials/admin_nav.php';

?>
<h1>داشبورد</h1>
<div class="grid">
  <div class="card">تعداد کل پست‌ها: <strong><?= $cnt_posts ?></strong></div>
  <div class="card">پیام‌های خوانده نشده: <strong><?= $cnt_unread ?></strong></div>
</div>

<?php include __DIR__ . '/../templates/partials/footer.php'; ?>
