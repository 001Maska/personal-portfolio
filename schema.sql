-- MySQL schema for portfolio backend

CREATE DATABASE IF NOT EXISTS `portfolio` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `portfolio`;

CREATE TABLE IF NOT EXISTS `contacts` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(120) NOT NULL,
  `email` VARCHAR(180) NOT NULL,
  `message` TEXT NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `projects` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(180) NOT NULL,
  `subtitle` VARCHAR(255) DEFAULT NULL,
  `description` TEXT NOT NULL,
  `tech` VARCHAR(255) NOT NULL,
  `repo_url` VARCHAR(255) DEFAULT NULL,
  `live_url` VARCHAR(255) DEFAULT NULL,
  `image_url` VARCHAR(255) DEFAULT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed example projects
INSERT INTO `projects` (`title`, `subtitle`, `description`, `tech`, `live_url`, `sort_order`) VALUES
('Süleyman Seven Website', 'Business Website', 'A polished business site featuring responsive layouts and strong visual storytelling. Clean design with modern interactions.', 'HTML, CSS, JavaScript', 'https://suleymanseven.com/', 2),
('MRT Kurubuz Website', 'Landing Page', 'A modern landing page experience built for clarity, conversion, and brand trust. Optimized for mobile and desktop.', 'HTML, CSS, JavaScript', 'https://mrtkurubuz.com/', 1);

CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(180) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
