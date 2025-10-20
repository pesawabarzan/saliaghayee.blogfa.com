-- db/sample_data.sql
INSERT INTO users(username, password_hash, role) VALUES
('admin', '$2y$10$abcdefghijklmnopqrstuvJ7xK4wYf4wZPpR2k3x2hQYy1H3z7r5nG', 'admin');

INSERT INTO posts(slug,title,content,excerpt,author_id,created_at,updated_at,is_published) VALUES
('report-term1','تحلیل عملکرد دانش‌آموزان - ترم اول',
'# گزارش ترم اول\n\n**خلاصه:** این گزارش به بررسی نمرات می‌پردازد.\n\n- ریاضی: 18.2\n- فیزیک: 17.2\n- ادبیات: 18.5\n\nبرای جزئیات بیشتر به فایل‌های داخلی مراجعه کنید.',
'خلاصه‌ای از تحلیل نمرات دانش‌آموزان در ترم اول.', 1, NOW(), NOW(), 1),
('science-competition','گزارش مسابقات علمی مدرسه',
'# مسابقات علمی\n\n- مقام اول: کاوشگران\n- مقام دوم: اندیشه\n\n**عکس‌ها** به زودی افزوده می‌شوند.',
'خلاصه‌ای از مسابقات علمی و نتایج تیم‌ها.', 1, NOW(), NOW(), 1),
('welcome','خوش آمدید به وبلاگ',
'# سلام دنیا!\nاین اولین پست نمونه است. *موفق باشید!*',
'پست آغازین بلاگ.', 1, NOW(), NOW(), 1);

INSERT INTO comments (post_id, name, email, body, is_approved, created_at)
VALUES
(1, N'کاربر نمونه', 'user@example.com', N'پست خوبی بود، موفق باشید!', 1, NOW());
