<?php
/** templates/partials/footer.php
 * فوتر کاربردی سایت + بستن تگ‌های main/body/html
 * ایمیل از settings خوانده می‌شود؛ اگر نبود، fallback = samadaliagha@gmail.com
 */
$BASE        = rtrim($CONFIG['base_url'] ?: base_url(), '/');
$owner_name  = 'دکتر صمد علی‌آقایی (Dr. S. Aliaghayee)';
$owner_email = e(get_setting($pdo, 'admin_email', 'samadaliagha@gmail.com'));
?>
</main>

<footer class="site-footer">
  <div class="container" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem;align-items:start">
    <!-- ستون ۱: اطلاعات صاحب سایت -->
    <div>
      <p style="margin:.25rem 0"><strong><?= $owner_name ?></strong></p>
      <p style="margin:.25rem 0">
        ایمیل: <a href="mailto:<?= $owner_email ?>"><?= $owner_email ?></a>
      </p>
      <p style="margin:.25rem 0">
        <a href="<?= $BASE ?>/about.php">درباره</a> ·
        <a href="<?= $BASE ?>/contact.php">تماس</a>
      </p>
    </div>

    <!-- ستون ۲: پیوندهای سریع داخل سایت -->
    <nav aria-label="پیوندهای سریع">
      <ul style="list-style:none;padding:0;margin:0;display:grid;gap:.35rem">
        <li><a href="<?= $BASE ?>/index.php">صفحه اصلی</a></li>
        <li><a href="<?= $BASE ?>/archive.php">آرشیو مطالب</a></li>
        <li><a href="<?= $BASE ?>/search.php">جستجو</a></li>
      </ul>
    </nav>

    <!-- ستون ۳: پروفایل‌های بیرونی (فقط دو سایت خواسته‌شده) -->
    <div>
      <p style="margin:.25rem 0"><strong>پروفایل‌های پژوهشی</strong></p>
      <ul style="list-style:none;padding:0;margin:0;display:grid;gap:.35rem">
        <li><a href="https://www.magiran.com/author/profile/460300" target="_blank" rel="noopener">مگیران</a></li>
        <li><a href="https://civilica.com/p/262162/" target="_blank" rel="noopener">سیویلیکا</a></li>
      </ul>
    </div>

    <!-- ستون ۴: ابزارها -->
    <div>
      <p style="margin:.25rem 0"><strong>ابزارها</strong></p>
      <ul style="list-style:none;padding:0;margin:0;display:grid;gap:.35rem">
        <li><a href="#" onclick="window.scrollTo({top:0,behavior:'smooth'});return false;">⬆ بازگشت به بالا</a></li>
      </ul>
    </div>
  </div>

  <div class="container" style="margin-top:1rem;border-top:1px solid var(--border);padding-top:.75rem;display:flex;justify-content:space-between;gap:1rem;flex-wrap:wrap;color:var(--muted)">
  <span>© <?= date('Y'); ?> همه حقوق محفوظ است.</span>
  <span>
    ساخته شده توسط
    <a href="https://www.farzamaskary.ir" target="_blank" rel="noopener">فرزام</a>
  </span>
</div>

</footer>

</body>
</html>
