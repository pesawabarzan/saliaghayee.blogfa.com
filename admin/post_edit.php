<?php
require __DIR__ . '/../config/bootstrap.php';

$auth = new Auth($pdo);
$auth->requireRole('editor');

CSRF::init();
$BASE = rtrim($CONFIG['base_url'] ?: base_url(), '/');

$uploadDir = __DIR__ . '/../assets/img/uploads';
if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0755, true); }

$maxSize = 2 * 1024 * 1024; // 2MB
$allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'];

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$is_edit = $id > 0;

$post = [
  'title' => '',
  'slug'  => '',
  'excerpt' => '',
  'content' => '',
  'is_published' => 0,
  'cover_image' => null
];

if ($is_edit) {
  $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ? LIMIT 1");
  $stmt->execute([$id]);
  $row = $stmt->fetch();
  if ($row) $post = $row;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($_POST['csrf_token']) || !CSRF::validate($_POST['csrf_token'])) {
    $errors[] = 'توکن نامعتبر است.';
  } else {
    $title = trim($_POST['title'] ?? '');
    $slug  = trim($_POST['slug'] ?? '');
    $excerpt = trim($_POST['excerpt'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $is_published = isset($_POST['is_published']) ? 1 : 0;

    if ($title === '') $errors[] = 'عنوان الزامی است.';
    if ($slug === '')  $slug = slugify($title);

    // یکتا بودن slug
    $stmt = $pdo->prepare("SELECT id FROM posts WHERE slug = ? AND id <> ? LIMIT 1");
    $stmt->execute([$slug, $is_edit ? $id : 0]);
    if ($stmt->fetch()) $errors[] = 'اسلاگ تکراری است.';

    // ===== کاور =====
    // پایه: مقدار فعلی (اگر در حالت ادیت هستیم)
    $coverImage = $is_edit ? ($post['cover_image'] ?? null) : null;

    // اگر فایل آپلود شد، جایگزین می‌شود
    if (!empty($_FILES['cover_file']['name'] ?? '')) {
      if ($_FILES['cover_file']['error'] === UPLOAD_ERR_OK) {
        if ($_FILES['cover_file']['size'] > $maxSize) {
          $errors[] = 'حجم تصویر نباید بیش از 2 مگابایت باشد.';
        } else {
          $finfo = finfo_open(FILEINFO_MIME_TYPE);
          $mime  = finfo_file($finfo, $_FILES['cover_file']['tmp_name']);
          finfo_close($finfo);
          if (!isset($allowed[$mime])) {
            $errors[] = 'فرمت تصویر مجاز نیست. (jpg, png, webp, gif)';
          } else {
            $ext  = $allowed[$mime];
            $safe = bin2hex(random_bytes(8)) . '.' . $ext;
            $dest = $uploadDir . '/' . $safe;
            if (move_uploaded_file($_FILES['cover_file']['tmp_name'], $dest)) {
              $coverImage = $BASE . '/assets/img/uploads/' . $safe;
            } else {
              $errors[] = 'آپلود تصویر ناموفق بود.';
            }
          }
        }
      } else {
        $errors[] = 'خطا در آپلود فایل.';
      }
    } else {
      // اگر لینک جدید وارد شده باشد، همان را ست کن
      $link = trim($_POST['cover_image_url'] ?? '');
      if ($link !== '') {
        if (!filter_var($link, FILTER_VALIDATE_URL)) {
          $errors[] = 'آدرس تصویر معتبر نیست.';
        } else {
          $coverImage = $link;
        }
      }
      // اگر لینک خالی و فایل هم نبود: coverImage همان مقدار قبلی می‌ماند
    }

    if (!$errors) {
      if ($is_edit) {
        $stmt = $pdo->prepare("UPDATE posts SET slug=?, title=?, content=?, excerpt=?, cover_image=?, updated_at=NOW(), is_published=? WHERE id=?");
        $stmt->execute([$slug, $title, $content, $excerpt, $coverImage, $is_published, $id]);
      } else {
        $stmt = $pdo->prepare("INSERT INTO posts (slug, title, content, excerpt, cover_image, author_id, created_at, updated_at, is_published)
                               VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW(), ?)");
        $stmt->execute([$slug, $title, $content, $excerpt, $coverImage, $auth->id(), $is_published]);
        $id = (int)$pdo->lastInsertId();
        $is_edit = true;
      }
      redirect('/admin/posts.php');
    } else {
      // نگه داشتن مقادیر فرم
      $post = array_merge($post, compact('title','slug','excerpt','content'));
      $post['is_published'] = $is_published;
      $post['cover_image']  = $coverImage;
    }
  }
}

$page_title = $is_edit ? 'ویرایش پست' : 'پست جدید';
/*include __DIR__ . '/../templates/partials/header.php';*/
include __DIR__ . '/../templates/partials/admin_nav.php';
?>
<h1><?= $is_edit ? 'ویرایش پست' : 'پست جدید' ?></h1>

<?php if ($errors): ?>
  <div class="alert error"><ul><?php foreach ($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<form method="post" action="" class="form form-horizontal" enctype="multipart/form-data">
  <?= CSRF::input(); ?>

  <div class="row">

    <div class="field">
      <label for="title">عنوان</label>
      <input id="title" name="title" value="<?= e($post['title']) ?>" required>
    </div>

    <div class="field">
      <label for="slug">اسلاگ</label>
      <input id="slug" name="slug" value="<?= e($post['slug']) ?>" placeholder="مثل: salam-donya">
    </div>

    <div class="field">
      <label for="excerpt">خلاصه</label>
      <input id="excerpt" name="excerpt" value="<?= e($post['excerpt']) ?>">
    </div>

    <div class="field full">
      <label for="content">متن</label>
      <textarea id="content" name="content" rows="10" required><?= e($post['content']) ?></textarea>
    </div>

    <fieldset class="full">
      <legend>تصویر کاور (اختیاری)</legend>

      <div class="field">
        <label for="cover_file">آپلود فایل</label>
        <input id="cover_file" type="file" name="cover_file" accept="image/*">
      </div>

      <div class="field">
        <label for="cover_image_url">یا لینک تصویر</label>
        <input id="cover_image_url" type="url" name="cover_image_url"
               value="<?= e($post['cover_image'] ?? '') ?>" placeholder="https://...">
      </div>

      <?php if (!empty($post['cover_image'])): ?>
        <div style="margin:.5rem 0">
          <img src="<?= e($post['cover_image']) ?>" alt="کاور" style="max-width:220px;border-radius:8px">
        </div>
      <?php endif; ?>
    </fieldset>

    <div class="field">
      <label for="is_published">منتشر شود</label>
      <input id="is_published" type="checkbox" name="is_published" value="1" <?= $post['is_published'] ? 'checked' : '' ?>>
    </div>

    <div class="actions">
      <button type="submit">ذخیره</button>
      <a class="button secondary" href="<?= $BASE ?>/admin/posts.php">انصراف</a>
    </div>

  </div>
</form>

<?php include __DIR__ . '/../templates/partials/footer.php'; ?>
