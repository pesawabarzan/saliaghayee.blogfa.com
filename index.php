<?php
require __DIR__ . '/config/bootstrap.php';
$BASE = rtrim($CONFIG['base_url'] ?: base_url(), '/');
$page_title = 'خانه';
include __DIR__ . '/templates/partials/header.php';

// نمونه کوئری (ممکن است از قبل داشته باشی)
$per = 10; $page = (int)($_GET['page'] ?? 1);
$total = (int)$pdo->query("SELECT COUNT(*) FROM posts WHERE is_published=1")->fetchColumn();
$pg = paginate($page, $per, $total);
$stmt = $pdo->prepare("SELECT p.*, u.username FROM posts p JOIN users u ON p.author_id=u.id WHERE is_published=1 ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->bindValue(1, $pg['limit'], PDO::PARAM_INT);
$stmt->bindValue(2, $pg['offset'], PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll();
?>

<h1>آخرین پست‌ها</h1>
<?php foreach ($posts as $post): ?>
  <article class="post-card">
    <h2><a href="<?= $BASE ?>/post.php?slug=<?= e($post['slug']) ?>"><?= e($post['title']) ?></a></h2>

    <?php if (!empty($post['cover_image'])): ?>
      <img class="cover" src="<?= e($post['cover_image']) ?>" alt="<?= e($post['title']) ?>" style="max-width:100%;border-radius:12px;margin:.5rem 0">
    <?php endif; ?>

    <p class="meta">
      تاریخ: <?= e(date('Y/m/d', strtotime($post['created_at']))) ?>،
      نویسنده: <?= e($post['username']) ?>
    </p>
    <p><?= e($post['excerpt']) ?></p>

    <a class="read-more" href="<?= $BASE ?>/post.php?slug=<?= e($post['slug']) ?>">ادامه…</a>
  </article>
<?php endforeach; ?>

<?php if ($pg['pages'] > 1): ?>
<nav class="pagination" aria-label="صفحه‌بندی">
  <?php for ($i=1; $i<=$pg['pages']; $i++): ?>
    <a class="page-link <?= $i==$pg['page'] ? 'active' : '' ?>" href="<?= $BASE ?>/index.php?page=<?= $i ?>"><?= $i ?></a>
  <?php endfor; ?>
</nav>
<?php endif; ?>

<?php include __DIR__ . '/templates/partials/footer.php'; ?>
