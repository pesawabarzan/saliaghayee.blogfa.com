<?php
require __DIR__ . '/config/bootstrap.php';
$BASE = rtrim($CONFIG['base_url'] ?: base_url(), '/');
$page_title = 'آرشیو';
include __DIR__ . '/templates/partials/header.php';

$items = $pdo->query("SELECT slug, title, created_at FROM posts WHERE is_published=1 ORDER BY created_at DESC")->fetchAll();
?>
<h1>آرشیو</h1>
<ul class="archive-list">
  <?php foreach ($items as $it): ?>
    <li>
      <a href="<?= $BASE ?>/post.php?slug=<?= e($it['slug']) ?>"><?= e($it['title']) ?></a>
      <small>(<?= e(date('Y/m/d', strtotime($it['created_at']))) ?>)</small>
    </li>
  <?php endforeach; ?>
</ul>
<?php include __DIR__ . '/templates/partials/footer.php'; ?>
