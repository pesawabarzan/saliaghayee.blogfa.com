<?php
require __DIR__ . '/../config/bootstrap.php';
$auth = new Auth($pdo);
$auth->logout();
redirect('/admin/index.php');
