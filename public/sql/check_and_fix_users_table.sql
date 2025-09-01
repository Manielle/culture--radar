-- Check and fix users table structure
-- Run this in phpMyAdmin

-- First, let's see what columns exist in the users table
DESCRIBE users;

-- Add missing columns if they don't exist
ALTER TABLE `users` 
ADD COLUMN IF NOT EXISTS `full_name` VARCHAR(255) AFTER `password`,
ADD COLUMN IF NOT EXISTS `avatar_url` VARCHAR(500) AFTER `full_name`,
ADD COLUMN IF NOT EXISTS `role` ENUM('user', 'admin', 'moderator') DEFAULT 'user' AFTER `avatar_url`,
ADD COLUMN IF NOT EXISTS `is_active` BOOLEAN DEFAULT TRUE AFTER `role`,
ADD COLUMN IF NOT EXISTS `email_verified` BOOLEAN DEFAULT FALSE AFTER `is_active`;

-- Alternative: Insert user without full_name if column doesn't exist
-- Use this if you can't modify the table structure:
INSERT IGNORE INTO `users` (`email`, `username`, `password`) 
VALUES ('test@culture-radar.fr', 'testuser', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Check if user was created
SELECT * FROM users WHERE email = 'test@culture-radar.fr';