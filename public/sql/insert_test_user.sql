-- Insert test user with correct column names
-- Password: test123

INSERT IGNORE INTO `users` (`email`, `username`, `password`, `name`) 
VALUES ('test@culture-radar.fr', 'testuser', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Test User');

-- Alternative: Insert multiple test users
INSERT IGNORE INTO `users` (`email`, `username`, `password`, `name`) VALUES
('demo@culture-radar.fr', 'demo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Demo User'),
('admin@culture-radar.fr', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User');

-- Verify users were created
SELECT id, name, username, email, is_active FROM users WHERE email LIKE '%culture-radar.fr';

-- Create user profile for test user (if user_profiles table exists)
INSERT IGNORE INTO `user_profiles` (`user_id`, `city`, `preferences`) 
SELECT id, 'Paris', '{"categories":["Art","Musique","Théâtre"],"budget_max":50}' 
FROM users 
WHERE email = 'test@culture-radar.fr';

-- Insert sample events
INSERT IGNORE INTO `events` (`id`, `title`, `description`, `category`, `location`, `address`, `price`, `is_free`, `start_date`) VALUES
(1, 'Exposition Art Moderne', 'Découvrez les œuvres des artistes contemporains', 'Art', 'Paris', 'Musée d\'Art Moderne, Paris', 0, TRUE, NOW() + INTERVAL 1 DAY),
(2, 'Concert Jazz', 'Soirée jazz avec les meilleurs musiciens', 'Musique', 'Paris', 'Le Sunset, Rue des Lombards', 25, FALSE, NOW() + INTERVAL 2 DAY),
(3, 'Festival de Théâtre', 'Trois jours de représentations théâtrales', 'Théâtre', 'Lyon', 'Théâtre des Célestins, Lyon', 35, FALSE, NOW() + INTERVAL 3 DAY),
(4, 'Vernissage Galerie', 'Vernissage de l\'exposition "Lumières Urbaines"', 'Art', 'Paris', 'Galerie Perrotin, 76 Rue de Turenne', 0, TRUE, NOW() + INTERVAL 4 DAY),
(5, 'Atelier Poterie', 'Initiation à la poterie pour débutants', 'Atelier', 'Paris', 'Atelier de la Terre, 15 Rue Oberkampf', 45, FALSE, NOW() + INTERVAL 5 DAY);

-- Show summary
SELECT 'Users created:' as Status, COUNT(*) as Count FROM users WHERE email LIKE '%culture-radar.fr';
SELECT 'Events created:' as Status, COUNT(*) as Count FROM events;