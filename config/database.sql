-- Lusso CMS Database Schema (based on BlueOrange CMS)
-- MySQL 5.7+ / MariaDB 10.3+

CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `last_login` TIMESTAMP NULL DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  INDEX `idx_username` (`username`),
  INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `site_settings` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` TEXT,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `rooms` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `price` DECIMAL(10,2) NOT NULL,
  `room_type` VARCHAR(50) DEFAULT NULL,
  `max_guests` INT(11) DEFAULT NULL,
  `description` TEXT,
  `short_description` TEXT,
  `main_image` VARCHAR(255) DEFAULT NULL,
  `gallery_images` TEXT DEFAULT NULL COMMENT 'JSON array of image paths',
  `features` TEXT DEFAULT NULL COMMENT 'JSON array of features',
  `amenities` TEXT DEFAULT NULL COMMENT 'JSON array of amenities',
  `tags` TEXT DEFAULT NULL COMMENT 'JSON array of tags',
  `included_items` TEXT DEFAULT NULL COMMENT 'JSON array of included items',
  `good_to_know` TEXT DEFAULT NULL COMMENT 'JSON object',
  `book_url` TEXT DEFAULT NULL,
  `original_price` DECIMAL(10,2) DEFAULT NULL,
  `urgency_message` TEXT DEFAULT NULL,
  `rating` INT(1) DEFAULT 5,
  `rating_score` DECIMAL(3,1) DEFAULT NULL,
  `location` VARCHAR(255) DEFAULT NULL,
  `size` VARCHAR(255) DEFAULT NULL,
  `is_featured` TINYINT(1) DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `display_order` INT(11) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `page_sections` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `page` VARCHAR(50) NOT NULL,
  `section_key` VARCHAR(100) NOT NULL,
  `content_type` ENUM('text', 'html', 'image', 'json') DEFAULT 'text',
  `content` TEXT,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_page_section` (`page`, `section_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `media` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `filename` VARCHAR(255) NOT NULL,
  `original_name` VARCHAR(255) DEFAULT NULL,
  `file_path` VARCHAR(500) NOT NULL,
  `file_type` VARCHAR(50) DEFAULT NULL,
  `file_size` INT(11) DEFAULT NULL,
  `uploaded_by` INT(11) DEFAULT NULL,
  `uploaded_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_file_path` (`file_path`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default admin: admin / password  (change immediately after first login)
-- Hash below is bcrypt for the string `password` (Laravel factory example hash).
INSERT INTO `admin_users` (`username`, `email`, `password_hash`, `is_active`) VALUES
('admin', 'admin@lusso.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1)
ON DUPLICATE KEY UPDATE `username` = `username`;

-- Demo content (site_settings, all page_sections, sample rooms with images/copy from the HTML prototypes):
-- After importing this file, run from the project root:
--   php tools/seed_cms_defaults.php
-- Uses INSERT IGNORE — safe on existing data; will not overwrite keys/sections you already have.

