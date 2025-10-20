<?php
// /post.php — نمایش یک پست + ثبت نظر
require __DIR__ . '/config/bootstrap.php';  // ← از روت

CSRF::init();
$BASE = rtrim($CONFIG['base_url'] ?: base_url(), '/');

// 404 اگر اسلاگ نداریم
$slug = trim($_GET['slug'] ?? '');
if ($slug === '') { http_response_code(404); exit('پست یافت نشد'); }

// واکشی پست (فقط منتشرشده‌ها)
$sql = "SELECT p.*, u.username AS author
        FROM posts p
        JOIN users u ON p.author_id = u.id
        WHERE p.slug = ? AND p.is_published = 1
        LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([$slug]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$post) { http_response_code(404); exit('پست یافت نشد'); }

// کاور: مطلق/نسبی
$cover = '';
if (!empty($post['cover_image'])) {
  if (preg_match('~^https?://~i', $post['cover_image'])) {
    $cover = $post['cover_image'];
  } else {
    $cover = $BASE . '/' . ltrim($post['cover_image'], '/');
  }
}

// ----- ثبت نظر -----
$errors = [];
$success = false;

$commentsEnabled    = $CONFIG['comments']['enabled']            ?? true;
$requireModeration  = $CONFIG['comments']['require_moderation']  ?? true;
$minSecsBetweenByIP = $CONFIG['comments']['min_secs_between']    ?? 60;

if ($commentsEnabled && $_SERVER['REQUEST_METHOD'] === 'POST'
    && ($_POST['action'] ?? '') === 'add_comment') {

  if (!isset($_POST['csrf_token']) || !CSRF::validate($_POST['csrf_token'])) {
    $errors[] = 'توکن نامعتبر است.';
  }
  if (!empty($_POST['hp_field'] ?? '')) {
    $errors[] = 'درخواست نامعتبر.';
  }

  $name  = trim($_POST['name']  ?? '');
  $email = trim($_POST['email'] ?? '');
  $body  = trim($_POST['body']  ?? '');

  if ($name === '' || mb_strlen($name) < 2) $errors[] = 'نام را صحیح وارد کنید.';
  if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'ایمیل نامعتبر است.';
  if ($body === '' || mb_strlen($body) < 5) $errors[] = 'متن نظر خیلی کوتاه است.';

  // rate limit
  $ipStr = $_SERVER['REMOTE_ADDR'] ?? '';
  $ipBin = $ipStr ? @inet_pton($ipStr) : null;
  if ($ipBin) {
    $q = $pdo->prepare("SELECT COUNT(*) FROM comments WHERE ip_addr = ? AND created_at > DATE_SUB(NOW(), INTERVAL ? SECOND)");
    $q->execute([$ipBin, (int)$minSecsBetweenByIP]);
    if ((int)$q->fetchColumn() > 0) $errors[] = 'لطفاً کمی صبر کنید و دوباره تلاش کنید.';
  }

  if (!$errors) {
    $approved = $requireModeration ? 0 : 1;
    $ins = $pdo->prepare("INSERT INTO comments (post_id, name, email, body, ip_addr, user_agent, is_approved, created_at)
                          VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);
    $ins->bindValue(1, (int)$post['id'], PDO::PARAM_INT);
    $ins->bindValue(2, $name, PDO::PARAM_STR);
    $ins->bindValue(3, $email !== '' ? $email : null, $email !== '' ? PDO::PARAM_STR : PDO::PARAM_NULL);
    $ins->bindValue(4, $body, PDO::PARAM_STR);
    $ipBin ? $ins->bindValue(5, $ipBin, PDO::PARAM_LOB) : $ins->bindValue(5, null, PDO::PARAM_NULL);
    $ins->bindValue(6, $ua, PDO::PARAM_STR);
    $ins->bindValue(7, $approved, PDO::PARAM_INT);
    $ins->execute();

    $success = true;
    $_POST = [];
  }
}

// لیست نظرات تأییدشده
$comments = [];
if ($commentsEnabled) {
  $c = $pdo->prepare("SELECT id, name, body, created_at
                      FROM comments
                      WHERE post_id = ? AND is_approved = 1
                      ORDER BY created_at ASC");
  $c->execute([(int)$post['id']]);
  $comments = $c->fetchAll(PDO::FETCH_ASSOC);
}

$page_title = $post['title'] ?? 'پست';
include __DIR__ . '/templates/partials/header.php';
?>
<article class="post">
  <h1><?= e($post['title']) ?></h1>

  <div class="meta">
    <span>نویسنده: <?= e($post['author']) ?></span>
    <span>تاریخ: <?= e(date('Y/m/d', strtotime($post['created_at']))) ?></span>
  </div>

  <?php if ($cover): ?>
    <figure style="margin:1rem 0">
      <img src="<?= e($cover) ?>" alt="<?= e($post['title']) ?>" style="max-width:100%;border-radius:12px">
    </figure>
  <?php endif; ?>

  <div class="content msg-body">
    <?= nl2br(e($post['content'])) ?>
  </div>
</article>

<hr>

<?php if ($commentsEnabled): ?>
<section id="comments" class="container">
  <h2>نظرات (<?= count($comments) ?>)</h2>

  <?php if ($success): ?>
    <div class="alert success">
      <?= $requireModeration ? 'نظر شما ثبت شد و پس از تأیید نمایش داده می‌شود.' : 'نظر شما با موفقیت ثبت شد.' ?>
    </div>
  <?php endif; ?>

  <?php if ($comments): ?>
    <div class="grid">
      <?php foreach ($comments as $cm): ?>
        <div class="card">
          <div style="font-weight:600;margin-bottom:.25rem"><?= e($cm['name']) ?></div>
          <div style="font-size:.85rem;color:var(--muted)"><?= e(date('Y/m/d H:i', strtotime($cm['created_at']))) ?></div>
          <div class="msg-body" style="margin-top:.5rem"><?= nl2br(e($cm['body'])) ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="alert">هنوز نظری ثبت نشده است.</div>
  <?php endif; ?>

  <h3 style="margin-top:1.25rem">ارسال نظر</h3>
  <?php if ($errors): ?>
    <div class="alert error"><ul><?php foreach ($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?></ul></div>
  <?php endif; ?>

  <form method="post" action="<?= $BASE ?>/post.php?slug=<?= urlencode($slug) ?>" class="post-form" novalidate>
    <?= CSRF::input(); ?>
    <input type="hidden" name="action" value="add_comment">
    <input type="text" name="hp_field" style="display:none" tabindex="-1" autocomplete="off">

    <label>نام</label>
    <input type="text" name="name" value="<?= e($_POST['name'] ?? '') ?>" required>

    <label>ایمیل (اختیاری)</label>
    <input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>">

    <label>متن نظر</label>
    <textarea name="body" rows="5" required><?= e($_POST['body'] ?? '') ?></textarea>

    <button type="submit">ثبت نظر</button>
  </form>
</section>
<?php endif; ?>

<?php include __DIR__ . '/templates/partials/footer.php'; ?>
