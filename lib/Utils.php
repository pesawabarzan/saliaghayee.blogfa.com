<?php
/** lib/Utils.php */

function e(string $s): string {
  return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function slugify(string $title): string {
  $s = trim($title);
  $s = preg_replace('~[\s]+~u', '-', $s);
  $s = preg_replace('~[^\pL\pN\-]+~u', '', $s);
  $s = trim($s, '-');
  return mb_strtolower($s, 'UTF-8');
}

function paginate(int $page, int $per_page, int $total): array {
  $pages = (int)ceil($total / max(1, $per_page));
  $page = max(1, min($page, max(1, $pages)));
  $offset = ($page - 1) * $per_page;
  return ['page' => $page, 'pages' => $pages, 'offset' => $offset, 'limit' => $per_page];
}

function redirect(string $url) {
  // base-aware redirect
  global $CONFIG;
  $base = rtrim($CONFIG['base_url'] ?: base_url(), '/');
  if (substr($url, 0, 1) === '/') {
    header('Location: ' . $base . $url);
  } else {
    header('Location: ' . $url);
  }
  exit;
}

function base_url(): string {
  $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
           (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);
  $scheme = $https ? 'https' : 'http';
  $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
  return $scheme . '://' . $host;
}
