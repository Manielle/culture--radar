-- Culture Radar - Complete Database Fix
-- Run this script in phpMyAdmin to fix all database issues

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS `culture_radar` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `culture_radar`;

-- 1. Create events table if not exists
CREATE TABLE IF NOT EXISTS `events` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `category` VARCHAR(100),
    `location` VARCHAR(255),
    `address` VARCHAR(500),
    `latitude` DECIMAL(10, 8),
    `longitude` DECIMAL(11, 8),
    `start_date` DATETIME,
    `end_date` DATETIME,
    `price` DECIMAL(10, 2) DEFAULT 0,
    `is_free` BOOLEAN DEFAULT FALSE,
    `image_url` VARCHAR(500),
    `source` VARCHAR(100),
    `external_id` VARCHAR(255),
    `external_url` VARCHAR(500),
    `tags` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_category` (`category`),
    KEY `idx_location` (`location`),
    KEY `idx_start_date` (`start_date`),
    KEY `idx_external_id` (`external_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Create event_reviews table if not exists
CREATE TABLE IF NOT EXISTS `event_reviews` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `event_id` VARCHAR(255) NOT NULL,
    `user_id` INT(11) NOT NULL,
    `rating` INT(1) NOT NULL CHECK (rating >= 1 AND rating <= 5),
    `review` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_event_id` (`event_id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_created_at` (`created_at`),
    UNIQUE KEY `unique_user_event_review` (`user_id`, `event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Create reservations table if not exists
CREATE TABLE IF NOT EXISTS `reservations` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `event_id` INT(11) NOT NULL,
    `user_id` INT(11) NOT NULL,
    `status` ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    `ticket_count` INT DEFAULT 1,
    `total_amount` DECIMAL(10, 2) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_event_id` (`event_id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Create users table if not exists
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `username` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(255),
    `avatar_url` VARCHAR(500),
    `role` ENUM('user', 'admin', 'moderator') DEFAULT 'user',
    `is_active` BOOLEAN DEFAULT TRUE,
    `email_verified` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_email` (`email`),
    KEY `idx_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Create user_profiles table if not exists
CREATE TABLE IF NOT EXISTS `user_profiles` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NOT NULL UNIQUE,
    `bio` TEXT,
    `location` VARCHAR(255),
    `city` VARCHAR(100),
    `country` VARCHAR(100),
    `preferences` TEXT,
    `interests` TEXT,
    `birth_date` DATE,
    `phone` VARCHAR(20),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    CONSTRAINT `fk_profile_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Insert sample data if tables are empty
-- Sample user (password: test123)
INSERT IGNORE INTO `users` (`email`, `username`, `password`, `full_name`) 
VALUES ('test@culture-radar.fr', 'testuser', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Test User');

-- Sample events
INSERT IGNORE INTO `events` (`title`, `description`, `category`, `location`, `address`, `price`, `is_free`, `start_date`) VALUES
('Exposition Art Moderne', 'Découvrez les œuvres des artistes contemporains', 'Art', 'Paris', 'Musée d\'Art Moderne, Paris', 0, TRUE, NOW() + INTERVAL 1 DAY),
('Concert Jazz', 'Soirée jazz avec les meilleurs musiciens', 'Musique', 'Paris', 'Le Sunset, Rue des Lombards', 25, FALSE, NOW() + INTERVAL 2 DAY),
('Festival de Théâtre', 'Trois jours de représentations théâtrales', 'Théâtre', 'Lyon', 'Théâtre des Célestins, Lyon', 35, FALSE, NOW() + INTERVAL 3 DAY);

-- Show summary
SELECT 'Database setup complete!' as Status;
SELECT COUNT(*) as 'Total Tables' FROM information_schema.tables WHERE table_schema = 'culture_radar';
SELECT COUNT(*) as 'Total Events' FROM events;
SELECT COUNT(*) as 'Total Users' FROM users;