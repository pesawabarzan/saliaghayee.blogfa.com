<?php
require __DIR__ . '/../config/bootstrap.php';
$auth = new Auth($pdo);
$auth->requireRole('admin');

$rows = $pdo->query("SELECT id, slug, title, excerpt, content, author_id, created_at, updated_at, is_published FROM posts ORDER BY id")->fetchAll();
header('Content-Type: application/json; charset=utf-8');
header('Content-Disposition: attachment; filename="posts_export.json"');
echo json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
