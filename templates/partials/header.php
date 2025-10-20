<?php /* templates/partials/header.php — نسخه نهایی با منوی موبایل + سوییچر تم */ ?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?= isset($page_title) ? e($page_title) : 'Dr. S. Aliaghayee' ?></title>
  <meta name="description" content="<?= isset($page_desc) ? e($page_desc) : 'وبلاگ فارسی با PHP' ?>">

  <?php $BASE = rtrim($CONFIG['base_url'] ?: base_url(), '/'); ?>

  <!-- فونت فارسی -->
  <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn/Vazirmatn-font-face.css" rel="stylesheet">

  <!-- تم‌ها: روشن پیش‌فرض، تاریک غیرفعال (toggle با JS) -->
  <link id="theme-light" rel="stylesheet" href="<?= $BASE ?>/assets/css/light.css">
  <link id="theme-dark"  rel="stylesheet" href="<?= $BASE ?>/assets/css/dark.css" disabled>

  <!-- اسکریپت سوییچر تم + همبرگر منوی موبایل -->
  <script defer src="<?= $BASE ?>/assets/js/theme-toggle.js"></script>
</head>
<body>
<header class="site-header">
  <div class="container header-inner">
    <a class="brand" href="<?= $BASE ?>/index.php" aria-label="خانه">Dr. S. Aliaghayee</a>

    <!-- دکمه همبرگری (در موبایل نشان داده می‌شود) -->
    <button id="navToggle" class="nav-toggle" aria-label="باز/بستن منو" aria-controls="mainNav" aria-expanded="false">☰</button>

    <nav id="mainNav" class="main-nav" aria-label="منوی اصلی">
      <ul>
        <li><a href="<?= $BASE ?>/index.php">خانه</a></li>
        <li><a href="<?= $BASE ?>/archive.php">آرشیو</a></li>
        <li><a href<?= "=\"$BASE/about.php\"" ?>>درباره ما</a></li>
        <li><a href="<?= $BASE ?>/contact.php">تماس</a></li>
        <li><a href="<?= $BASE ?>/search.php">جستجو</a></li>
      </ul>
    </nav>
<div id="navBackdrop" class="nav-backdrop"></div>

    <button id="themeToggle" class="theme-toggle" aria-label="تغییر تم">🌓</button>
  </div>
</header>

<main class="container main-content">
