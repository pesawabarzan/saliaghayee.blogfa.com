<?php
require __DIR__ . '/config/bootstrap.php';
$page_title = 'درباره ما | دکتر صمد علی‌آقایی';

$BASE  = rtrim($CONFIG['base_url'] ?: base_url(), '/');
$email = e(get_setting($pdo, 'admin_email', 'samadaliagha@gmail.com'));  // قابل تغییر از settings
$addr  = e(get_setting($pdo, 'clinic_address', 'بوکان، آذربایجان غربی')); // نمونه؛ اگر خواستی حذف/ویرایش کن
$phone = e(get_setting($pdo, 'clinic_phone', ''));                        // اختیاری

include __DIR__ . '/templates/partials/header.php';
?>
<article class="card" style="margin-top:1rem">
    <header style="display:flex;gap:1rem;align-items:center;flex-wrap:wrap">
        <img src="<?= $BASE ?>/assets/img/doctor.jpg" alt="دکتر صمد علی‌آقایی"
            style="width:120px;height:120px;object-fit:cover;border-radius:50%;border:1px solid var(--border)"
            onerror="this.style.display='none'">
        <div>
            <h1 style="margin:.25rem 0">درباره دکتر صمد علی‌آقایی</h1>
            <p style="margin:0;color:var(--muted)">دانش‌آموخته دکتری زبان‌شناسی/زبان انگلیسی، مدرس دانشگاه و پژوهشگر</p>
        </div>
    </header>

    <section style="margin-top:1rem">
        <h2>معرفی</h2>
        <p>
            دکتر صمد علی‌آقایی پژوهشگر و مدرس حوزهٔ زبان‌شناسی و زبان و ادبیات انگلیسی است. در صفحهٔ عمومی ایشان
            به‌عنوان «دکترای زبان‌شناسی همگانی / Applied Linguistics» معرفی شده‌اند و پیش‌تر نیز در وبلاگ شخصی خود با
            عناوین «PhD of Applied Linguistics، M.A و B.A ادبیات انگلیسی، A.A آموزش زبان» فعالیت‌های آموزشی و محتوایی منتشر کرده‌اند.
            این وبگاه محلی است برای اشتراک‌گذاری یادداشت‌ها، نتایج پژوهش، و معرفی منابع آموزش زبان برای دانشجویان و علاقه‌مندان.
        </p>
    </section>

    <section>
        <h2>تحصیلات و گرایش‌ها</h2>
        <ul>
            <li>دکتری زبان‌شناسی (دانشگاه کردستان، ۱۳۹۹)؛ گرایش‌های مرتبط: زبان‌شناسی و زبان و ادبیات انگلیسی. </li>
            <li>کارشناسی ارشد زبان و ادبیات انگلیسی (دانشگاه آزاد اسلامی تبریز، ۱۳۸۶). </li>
            <li>کارشناسی زبان و ادبیات انگلیسی (دانشگاه ارومیه، ۱۳۸۴). </li>
            <li>کاردانی آموزش زبان انگلیسی (دانشگاه تهران، ۱۳۸۰). </li>
        </ul>
    </section>

    <section>
        <h2>فعالیت حرفه‌ای و آموزشی</h2>
        <p>
            سابقهٔ آموزشی ایشان شامل تدریس در مدرسه و دانشگاه و همکاری با مجموعه‌های آموزش زبان است؛ در پروفایل مگیران
            «مدرس مدرسه و دانشگاه» ذکر شده و تسلط به چند زبان از جمله فارسی، عربی، کردی، انگلیسی و فرانسوی نیز آمده است.
        </p>
    </section>

    <section>
        <h2>پژوهش‌ها و انتشارات منتخب</h2>
        <p>
            محور عمدهٔ نوشته‌های پژوهشی دکتر علی‌آقایی در سال‌های اخیر، تحلیل‌های شناختی و بلاغی در زبان کردی و ادبیات
            (از جمله کلان‌استعاره‌ها و مجاز مفهومی) بوده است. فهرست مقالات نمایه‌شدهٔ ایشان در مگیران برای نمونه شامل:
        </p>
        <ul>
            <li>«کلان‌استعارهٔ شناختی آزادی در زبان کردی…» (۱۳۹۹)؛ «رویکردی شناختی به کلان‌استعارهٔ عشق…» (۱۳۹۹)؛
                «بررسی مجاز مفهومی در زبان کردی…» (۱۳۹۹)؛ «کلان‌استعارهٔ دانش…» (۱۴۰۰)؛ و «واژه‌بست‌های ضمیری کردی موکری…» (۱۴۰۰). </li>
        </ul>
        <p>
            صفحهٔ پژوهشی ایشان در سیویلیکا نیز با شناسهٔ پژوهشگر (262162) و شناسهٔ ORCID ثبت شده و ایمیل تماس عمومی درج گردیده است.
        </p>
    </section>

    <section>
        <h2>وبلاگ و فعالیت‌های محتوایی</h2>
        <p>
            وبلاگ شخصی دکتر علی‌آقایی شامل یادداشت‌های آموزشی زبان انگلیسی، نمونه‌سؤالات و نتایج آزمون‌های کلاسی، و پیوندهای مفید برای
            یادگیری زبان است و از سال‌های گذشته مرجع دسترسی دانشجویان به محتوای کلاسی بوده است.
        </p>
    </section>

    <section>
        <h2>تماس</h2>
        <p>
            <strong>ایمیل:</strong> <a href="mailto:<?= $email ?>"><?= $email ?></a>
            <?php if ($phone): ?><br><strong>تلفن:</strong> <a href="tel:<?= $phone ?>"><?= $phone ?></a><?php endif; ?>
            <?php if ($addr): ?><br><strong>آدرس:</strong> <?= $addr ?><?php endif; ?>
        </p>

        <!-- 
    <p>
      پیوندها: 
      <a href="https://saliaghayee.blogfa.com" rel="noopener" target="_blank">وبلاگ شخصی</a> ·
      <a href="https://magiran.com/R460300" rel="noopener" target="_blank">پروفایل مگیران</a> ·
      <a href="https://civilica.com/p/262162/" rel="noopener" target="_blank">صفحهٔ سیویلیکا</a>
    </p> 
    -->

    </section>
</article>

<!-- اسکیما (SEO) -->
<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Person",
        "name": "دکتر صمد علی‌آقایی",
        "jobTitle": "مدرس و پژوهشگر زبان‌شناسی/زبان انگلیسی",
        "url": "<?= $BASE ?>/about.php",
        "email": "mailto:<?= $email ?>",
        "knowsLanguage": ["fa", "ar", "ku", "en", "fr"],
        "sameAs": [
            "https://saliaghayee.blogfa.com",
            "https://magiran.com/R460300",
            "https://civilica.com/p/262162/"
        ]
    }
</script>

<?php include __DIR__ . '/templates/partials/footer.php'; ?>