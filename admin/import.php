<?php
// admin/import.php
require dirname(__DIR__) . '/config/bootstrap.php';

$auth = new Auth($pdo);
$auth->requireRole('editor');

CSRF::init();
$BASE   = rtrim($CONFIG['base_url'] ?: base_url(), '/');
$errors = [];
$report = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // --- 1) CSRF ---
  if (!isset($_POST['csrf_token']) || !CSRF::validate($_POST['csrf_token'])) {
    $errors[] = 'توکن امنیتی نامعتبر است.';
  }

  // --- 2) ورودی‌ها ---
  $downloadImages = !empty($_POST['download_images']);
  $setPublished   = !empty($_POST['force_published']);
  $authorId       = $auth->id() ?: 1;

  // --- 3) فایل JSON ---
  if (empty($_FILES['json_file']['name'])) {
    $errors[] = 'فایل JSON انتخاب نشده است.';
  } else {
    $f = $_FILES['json_file'];
    if ($f['error'] !== UPLOAD_ERR_OK) {
      $errors[] = 'خطا در آپلود فایل.';
    } else {
      $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
      if ($ext !== 'json') {
        $errors[] = 'فقط فایل JSON مجاز است.';
      }
      if ($f['size'] > 5 * 1024 * 1024) {
        $errors[] = 'حجم فایل نباید از 5MB بیشتر باشد.';
      }
    }
  }

  // --- 4) خواندن JSON ---
  $data = null;
  if (!$errors) {
    $raw  = file_get_contents($f['tmp_name']);
    $data = json_decode($raw, true, 512, JSON_UNESCAPED_UNICODE);
    if (!is_array($data) || !isset($data['posts']) || !is_array($data['posts'])) {
      $errors[] = 'ساختار JSON نامعتبر است (کلید posts یافت نشد).';
    }
  }

  // --- 5) ایمپورت ---
  if (!$errors && $data) {
    date_default_timezone_set('Asia/Tehran');

    $uploadDir = dirname(__DIR__) . '/assets/img/uploads';
    if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0755, true); }

    $imported = 0; $skipped = 0; $failed = 0; $notes = [];

    foreach ($data['posts'] as $idx => $p) {
      try {
        $title   = trim($p['title'] ?? '');
        $slug    = trim($p['slug'] ?? '');
        $excerpt = trim($p['excerpt'] ?? '');
        $html    = trim($p['content_html'] ?? '');
        $cover   = trim($p['cover_image'] ?? '');
        $isPub   = isset($p['is_published']) ? (int)!!$p['is_published'] : 1;
        $created = trim($p['created_at'] ?? '');

        if ($title === '' || $html === '') {
          $skipped++;
          $notes[] = "[$idx] بدون عنوان یا محتوا؛ رد شد.";
          continue;
        }

        if ($slug === '') { $slug = slugify($title); }

        // یکتا کردن اسلاگ
        $baseSlug = $slug;
        $stmt = $pdo->prepare("SELECT id FROM posts WHERE slug = ? LIMIT 1");
        $i = 1;
        while (true) {
          $stmt->execute([$slug]);
          if (!$stmt->fetchColumn()) break;
          $i++;
          $slug = $baseSlug . '-' . $i;
        }

        // تاریخ
        $created_at = $created ? date('Y-m-d H:i:s', strtotime($created)) : date('Y-m-d H:i:s');

        // دانلود کاور (اختیاری)
        if ($downloadImages && $cover && preg_match('~^https?://~i', $cover)) {
          $ext = strtolower(pathinfo(parse_url($cover, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
          if (!in_array($ext, ['jpg','jpeg','png','webp','gif'])) { $ext = 'jpg'; }
          $safe = bin2hex(random_bytes(8)) . '.' . $ext;
          $dest = $uploadDir . '/' . $safe;

          // دانلود ساده با cURL
          $ch = curl_init($cover);
          curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
          ]);
          $bin  = curl_exec($ch);
          $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
          curl_close($ch);

          if ($bin && $http >= 200 && $http < 300) {
            file_put_contents($dest, $bin);
            $cover = $BASE . '/assets/img/uploads/' . $safe;
          } else {
            $notes[] = "[$idx] دانلود کاور ناموفق: $cover";
            // اگر دانلود نشد، همان URL اصلی بماند
          }
        }

        if ($setPublished) { $isPub = 1; }

        // درج پست
        $ins = $pdo->prepare(
          "INSERT INTO posts
           (slug, title, content, excerpt, cover_image, author_id, created_at, updated_at, is_published)
           VALUES (?,?,?,?,?,?,?,NOW(),?)"
        );
        $ins->execute([
          $slug, $title, $html, $excerpt ?: null, $cover ?: null, $authorId, $created_at, $isPub
        ]);

        $imported++;
      } catch (Throwable $e) {
        $failed++;
        $notes[] = "[$idx] خطا: " . $e->getMessage();
      }
    }

    $report = [
      'imported' => $imported,
      'skipped'  => $skipped,
      'failed'   => $failed,
      'notes'    => $notes,
    ];
  }
}
?>
<?php
// نمایش UI
$page_title = 'Import آرشیو';
/*include dirname(__DIR__) . '/templates/partials/header.php';*/
include dirname(__DIR__) . '/templates/partials/admin_nav.php';
?>
<h1>ایمپورت آرشیو (JSON)</h1>

<?php if (!empty($errors)): ?>
  <div class="alert error">
    <ul><?php foreach ($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?></ul>
  </div>
<?php endif; ?>

<form method="post" action="<?= $BASE ?>/admin/import.php" enctype="multipart/form-data" class="form" style="max-width:720px">
  <?= CSRF::input(); ?>
  <label for="json_file">فایل JSON</label>
  <input id="json_file" type="file" name="json_file" accept="application/json" required>

  <label style="display:flex;gap:.5rem;align-items:center">
    <input type="checkbox" name="download_images" value="1"> دانلود و کپی تصاویر کاور در هاست
  </label>

  <label style="display:flex;gap:.5rem;align-items:center">
    <input type="checkbox" name="force_published" value="1"> انتشار همهٔ پست‌ها (نادیده‌گرفتن is_published)
  </label>

  <div class="actions">
    <button type="submit">ایمپورت</button>
    <a class="button secondary" href="<?= $BASE ?>/admin/posts.php">بازگشت</a>
  </div>
</form>

<?php if ($report): ?>
  <div class="card" style="margin-top:1rem">
    <h3>خلاصهٔ عملیات</h3>
    <ul>
      <li>وارد شده: <?= (int)$report['imported'] ?></li>
      <li>رد شده: <?= (int)$report['skipped'] ?></li>
      <li>ناموفق: <?= (int)$report['failed'] ?></li>
    </ul>
    <?php if (!empty($report['notes'])): ?>
      <details>
        <summary>جزییات</summary>
        <ul><?php foreach ($report['notes'] as $n): ?><li><?= e($n) ?></li><?php endforeach; ?></ul>
      </details>
    <?php endif; ?>
  </div>
<?php endif; ?>

<?php include dirname(__DIR__) . '/templates/partials/footer.php'; ?>
