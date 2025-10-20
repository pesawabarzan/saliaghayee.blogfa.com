<?php
require __DIR__ . '/config/bootstrap.php';
CSRF::init();

$BASE = rtrim($CONFIG['base_url'] ?: base_url(), '/');
$page_title = 'تماس';

$errors = []; $success = false;

// Rate limit ساده: بر اساس IP هر 60 ثانیه
$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
if (!isset($_SESSION['contact_last'])) $_SESSION['contact_last'] = [];
$now = time();
if (isset($_SESSION['contact_last'][$ip]) && $now - $_SESSION['contact_last'][$ip] < 60) {
  $errors[] = 'لطفاً کمی بعد دوباره تلاش کنید.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$errors) {
  if (!isset($_POST['csrf_token']) || !CSRF::validate($_POST['csrf_token'])) {
    $errors[] = 'توکن نامعتبر است.';
  }

  // honeypot
  if (!empty($_POST['hp_field'] ?? '')) {
    $errors[] = 'درخواست نامعتبر.';
  }

  $name    = trim($_POST['name'] ?? '');
  $email   = trim($_POST['email'] ?? '');
  $subject = trim($_POST['subject'] ?? '');
  $message = trim($_POST['message'] ?? '');

  if ($name === '')    $errors[] = 'نام الزامی است.';
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'ایمیل معتبر نیست.';
  if ($subject === '') $errors[] = 'موضوع الزامی است.';
  if ($message === '') $errors[] = 'پیام را بنویسید.';

  if (!$errors) {
    // ذخیره DB
    $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message, created_at, is_read) VALUES (?, ?, ?, ?, NOW(), 0)");
    $stmt->execute([$name, $email, $subject, $message]);

    // ایمیل (اختیاری)
    if (!empty($CONFIG['smtp_enabled'])) {
      try {
        // اگر PHPMailer را قبلاً اضافه کرده‌ای، این بخش را فعال کن:
        // $mail = mailer();  // مثلاً تابعی که در bootstrap تعریف شده
        // $mail->addAddress($CONFIG['admin_email']);
        // $mail->Subject = "پیام جدید از فرم تماس: $subject";
        // $mail->Body    = "نام: $name\nایمیل: $email\n\n$message";
        // $mail->send();
      } catch (Throwable $e) { /* لاگ اختیاری */ }
    }

    $_SESSION['contact_last'][$ip] = $now;
    $success = true;
  }
}

include __DIR__ . '/templates/partials/header.php';
?>
<h1>تماس با ما</h1>

<?php if ($success): ?>
  <div class="alert success">پیام شما ارسال شد. ممنون!</div>
<?php else: ?>
  <?php if ($errors): ?>
    <div class="alert error"><ul><?php foreach ($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?></ul></div>
  <?php endif; ?>

  <form method="post" action="<?= $BASE ?>/contact.php" class="contact-form" novalidate>
    <?= CSRF::input(); ?>
    <input type="text" name="hp_field" style="display:none" tabindex="-1" autocomplete="off">

    <label for="name">نام</label>
    <input id="name" name="name" required value="<?= e($_POST['name'] ?? '') ?>">

    <label for="email">ایمیل</label>
    <input id="email" name="email" type="email" required value="<?= e($_POST['email'] ?? '') ?>">

    <label for="subject">موضوع</label>
    <input id="subject" name="subject" required value="<?= e($_POST['subject'] ?? '') ?>">

    <label for="message">پیام</label>
    <textarea id="message" name="message" rows="6" required><?= e($_POST['message'] ?? '') ?></textarea>

    <button type="submit">ارسال</button>
  </form>
<?php endif; ?>

<?php include __DIR__ . '/templates/partials/footer.php'; ?>
