<?php /* templates/partials/header.php */ ?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?= isset($page_title) ? e($page_title) : 'وبلاگ' ?></title>
  <meta name="description" content="<?= isset($page_desc) ? e($page_desc) : 'وبلاگ فارسی با PHP' ?>">

  <?php $BASE = rtrim($CONFIG['base_url'] ?: base_url(), '/'); ?>

  <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn/Vazirmatn-font-face.css" rel="stylesheet">
<link id="theme-light" rel="stylesheet" href="<?= $BASE ?>/assets/css/light.css">
<link id="theme-dark"  rel="stylesheet" href="<?= $BASE ?>/assets/css/dark.css" disabled>
<script defer src="<?= $BASE ?>/assets/js/theme-toggle.js"></script>
</head>
<body>
<header class="site-header">
  <div class="container header-inner">
    <a class="brand" href="<?= $BASE ?>/index.php" aria-label="خانه">مدیریت</a>
    <nav class="main-nav" aria-label="منوی اصلی">
    <a href="<?= $BASE ?>/admin/dashboard.php">داشبورد</a>
    <a href="<?= $BASE ?>/admin/posts.php">مدیریت پست‌ها</a>
    <a href="<?= $BASE ?>/admin/messages.php">پیام‌ها</a>
    <a href="<?= $BASE ?>/admin/comments.php">نظرات</a>
    <a href="<?= $BASE ?>/admin/export.php">Export پست‌ها</a>
    <a href="<?= $BASE ?>/admin/import.php">Import پست‌ها</a>
    <span style="flex:1"></span>
    <a href="<?= $BASE ?>/admin/logout.php" class="danger">خروج</a>
    </nav>
    <button id="themeToggle" class="theme-toggle" aria-label="تغییر تم">🌓</button>
  </div>
</header>
<main class="container main-content">
