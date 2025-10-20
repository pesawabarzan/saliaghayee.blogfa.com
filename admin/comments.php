<?php
require dirname(__DIR__) . '/config/bootstrap.php';

$auth = new Auth($pdo);
$auth->requireRole('editor');
CSRF::init();
$BASE = rtrim($CONFIG['base_url'] ?: base_url(), '/');

// اقدامات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($_POST['csrf_token']) || !CSRF::validate($_POST['csrf_token'])) {
    http_response_code(400); exit('CSRF نامعتبر');
  }
  $id = (int)($_POST['id'] ?? 0);

  if (isset($_POST['approve'])) {
    $pdo->prepare("UPDATE comments SET is_approved=1 WHERE id=?")->execute([$id]);
  } elseif (isset($_POST['delete'])) {
    $pdo->prepare("DELETE FROM comments WHERE id=?")->execute([$id]);
  }
  redirect('/admin/comments.php');
}

include dirname(__DIR__) . '/templates/partials/admin_nav.php';

$rows = $pdo->query("
  SELECT c.id, c.post_id, c.name, c.email, c.body, c.is_approved, c.created_at,
         p.title, p.slug
  FROM comments c
  JOIN posts p ON p.id = c.post_id
  ORDER BY c.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

$csrf = CSRF::token();
?>
<h1>نظرات</h1>
<?php if (!$rows): ?>
  <div class="alert">نظری وجود ندارد.</div>
<?php else: ?>
<table class="table">
  <thead>
    <tr>
      <th>ID</th><th>پست</th><th>نام</th><th>ایمیل</th>
      <th>متن</th><th>تاریخ</th><th>وضعیت</th><th>عملیات</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($rows as $r): ?>
    <tr>
      <td><?= (int)$r['id'] ?></td>
      <td><a href="<?= $BASE ?>/post.php?slug=<?= urlencode($r['slug']) ?>" target="_blank">
        <?= e($r['title']) ?></a></td>
      <td><?= e($r['name']) ?></td>
      <td><?= e($r['email'] ?? '-') ?></td>
      <td style="text-align:right"><?= nl2br(e(mb_strimwidth($r['body'],0,200,'…','UTF-8'))) ?></td>
      <td><?= e(date('Y/m/d H:i', strtotime($r['created_at']))) ?></td>
      <td><?= $r['is_approved'] ? 'تأیید شده' : 'در انتظار' ?></td>
      <td style="display:flex;gap:.4rem;justify-content:center">
        <?php if (!$r['is_approved']): ?>
          <form method="post" action="<?= $BASE ?>/admin/comments.php" style="margin:0">
            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
            <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
            <button type="submit" name="approve" class="button" style="padding:.25rem .6rem">تأیید</button>
          </form>
        <?php endif; ?>
        <form method="post" action="<?= $BASE ?>/admin/comments.php" style="margin:0" onsubmit="return confirm('حذف شود؟')">
          <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
          <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
          <button type="submit" name="delete" class="btn-link danger" style="padding:.25rem .6rem">حذف</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>
<?php include dirname(__DIR__) . '/templates/partials/footer.php'; ?>
