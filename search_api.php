<?php
// search_api.php — API جستجوی زنده (JSON)
require __DIR__ . '/config/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

$q = trim($_GET['q'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

function like_escape(string $s): string {
  return strtr($s, ['\\' => '\\\\', '%' => '\%', '_' => '\_']);
}

if ($q === '') {
  echo json_encode(['total' => 0, 'items' => []], JSON_UNESCAPED_UNICODE);
  exit;
}

$like = '%' . like_escape($q) . '%';

// شمارش
$sqlCount = "
  SELECT COUNT(*)
  FROM posts
  WHERE is_published = 1
    AND (title LIKE :q1 OR excerpt LIKE :q2 OR content LIKE :q3)
";
$st = $pdo->prepare($sqlCount);
$st->bindValue(':q1', $like, PDO::PARAM_STR);
$st->bindValue(':q2', $like, PDO::PARAM_STR);
$st->bindValue(':q3', $like, PDO::PARAM_STR);
$st->execute();
$total = (int)$st->fetchColumn();

// لیست
$sql = "
  SELECT slug, title, excerpt, content, created_at, cover_image
  FROM posts
  WHERE is_published = 1
    AND (title LIKE :q1 OR excerpt LIKE :q2 OR content LIKE :q3)
  ORDER BY created_at DESC
  LIMIT :lim OFFSET :off
";
$st = $pdo->prepare($sql);
$st->bindValue(':q1', $like, PDO::PARAM_STR);
$st->bindValue(':q2', $like, PDO::PARAM_STR);
$st->bindValue(':q3', $like, PDO::PARAM_STR);
$st->bindValue(':lim', (int)$limit, PDO::PARAM_INT);
$st->bindValue(':off', (int)$offset, PDO::PARAM_INT);
$st->execute();
$rows = $st->fetchAll(PDO::FETCH_ASSOC);

// خلاصه‌ی fallback
foreach ($rows as &$r) {
  if (empty($r['excerpt'])) {
    $plain = trim(mb_substr(strip_tags($r['content'] ?? ''), 0, 140, 'UTF-8'));
    $r['excerpt'] = $plain !== '' ? ($plain . '…') : '';
  }
  unset($r['content']); // خروجی را جمع‌وجور کنیم
}

echo json_encode(['total' => $total, 'items' => $rows], JSON_UNESCAPED_UNICODE);
