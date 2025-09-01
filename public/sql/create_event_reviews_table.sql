-- Create event_reviews table for Culture Radar
-- This table stores user reviews and ratings for events

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
    UNIQUE KEY `unique_user_event_review` (`user_id`, `event_id`),
    CONSTRAINT `fk_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add some indexes for better query performance
CREATE INDEX idx_event_rating ON event_reviews(event_id, rating);
CREATE INDEX idx_user_reviews ON event_reviews(user_id, created_at DESC);