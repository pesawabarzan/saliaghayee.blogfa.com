<?php
// admin/posts.php
require dirname(__DIR__) . '/config/bootstrap.php';

$auth = new Auth($pdo);
$auth->requireRole('editor');

CSRF::init();
$BASE = rtrim($CONFIG['base_url'] ?: base_url(), '/');

// حذف امن (POST + CSRF)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
  if (!isset($_POST['csrf_token']) || !CSRF::validate($_POST['csrf_token'])) {
    http_response_code(400); exit('CSRF نامعتبر');
  }
  $id = (int)$_POST['delete_id'];
  $pdo->prepare("DELETE FROM posts WHERE id = ?")->execute([$id]);
  redirect('/admin/posts.php');
}

$page_title = 'مدیریت پست‌ها';
/*include dirname(__DIR__) . '/templates/partials/header.php';*/
include dirname(__DIR__) . '/templates/partials/admin_nav.php';

$sql = "SELECT p.id, p.slug, p.title, p.is_published, p.created_at,
               u.username AS author, p.cover_image
        FROM posts p
        JOIN users u ON p.author_id = u.id
        ORDER BY p.created_at DESC";
$rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$csrf = CSRF::token();
?>
<h1>پست‌ها</h1>

<p><a class="button" href="<?= $BASE ?>/admin/post_edit.php">+ پست جدید</a></p>

<?php if (!$rows): ?>
  <div class="alert">هنوز پستی ثبت نشده است.</div>
<?php else: ?>
  <table class="table">
    <thead>
      <tr>
        <th>ID</th><th>عنوان</th><th>کاور</th><th>اسلاگ</th>
        <th>منتشر</th><th>نویسنده</th><th>تاریخ</th><th>عملیات</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td><?= (int)$r['id'] ?></td>
        <td><?= e($r['title']) ?></td>
        <td><?php if (!empty($r['cover_image'])): ?>
          <img src="<?= e($r['cover_image']) ?>" alt="کاور" style="height:40px;border-radius:6px">
        <?php endif; ?></td>
        <td><?= e($r['slug']) ?></td>
        <td><?= $r['is_published'] ? 'بله' : 'خیر' ?></td>
        <td><?= e($r['author']) ?></td>
        <td><?= e(date('Y/m/d', strtotime($r['created_at']))) ?></td>
        <td style="display:flex;gap:.5rem;align-items:center">
          <a href="<?= $BASE ?>/admin/post_edit.php?id=<?= (int)$r['id'] ?>">ویرایش</a>
          <form method="post" action="<?= $BASE ?>/admin/posts.php" onsubmit="return confirm('حذف شود؟')" style="margin:0">
            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
            <input type="hidden" name="delete_id" value="<?= (int)$r['id'] ?>">
            <button type="submit" class="btn-link danger" style="padding:.25rem .6rem">حذف</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>

<?php include dirname(__DIR__) . '/templates/partials/footer.php'; ?>
