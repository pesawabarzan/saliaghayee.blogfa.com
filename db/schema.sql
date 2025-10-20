-- db/schema.sql
-- UTF-8 / InnoDB / utf8mb4 برای پشتیبانی کامل از فارسی و ایموجی
SET NAMES utf8mb4;
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(64) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','editor') NOT NULL DEFAULT 'admin',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_users_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(255) NOT NULL,
  title VARCHAR(255) NOT NULL,
  content MEDIUMTEXT NOT NULL,
  excerpt VARCHAR(500) DEFAULT '',
  author_id INT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  is_published TINYINT(1) NOT NULL DEFAULT 0,
  CONSTRAINT fk_posts_author
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE KEY uq_posts_slug (slug),
  KEY idx_posts_published (is_published),
  KEY idx_posts_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS contact_messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(255) NOT NULL,
  subject VARCHAR(255) NOT NULL,
  message TEXT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  KEY idx_contact_created (created_at),
  KEY idx_contact_unread (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS settings (
  `key` VARCHAR(191) PRIMARY KEY,
  `value` TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== Comments (هماهنگ با posts.id از نوع INT (SIGNED)) =====
CREATE TABLE IF NOT EXISTS comments (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  post_id     INT NOT NULL,                 -- << هماهنگ با posts.id
  name        VARCHAR(120) NOT NULL,
  email       VARCHAR(190) DEFAULT NULL,
  body        TEXT NOT NULL,
  ip_addr     VARBINARY(16) NULL,           -- IPv4/IPv6 به صورت باینری
  user_agent  VARCHAR(255) NULL,
  is_approved TINYINT(1) NOT NULL DEFAULT 0,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_comments_post
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
  KEY idx_comments_post (post_id),
  KEY idx_comments_approved (is_approved),
  KEY idx_comments_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
