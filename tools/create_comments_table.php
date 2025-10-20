<?php
require __DIR__ . '/../config/bootstrap.php';
$sql = <<<SQL
CREATE TABLE IF NOT EXISTS comments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  post_id INT UNSIGNED NOT NULL,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) DEFAULT NULL,
  body TEXT NOT NULL,
  ip_addr VARBINARY(16) NULL,
  user_agent VARCHAR(255) NULL,
  is_approved TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_comments_post FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
  INDEX (post_id), INDEX (is_approved), INDEX (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
SQL;
$pdo->exec($sql);
echo "comments table is ready.\n";
