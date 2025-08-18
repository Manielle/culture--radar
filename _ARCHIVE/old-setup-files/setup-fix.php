<?php
/**
 * Setup Fix Script for Culture Radar
 * This script fixes common setup issues
 */

echo "=== Culture Radar Setup Fix ===\n\n";

// Create a simple .env file with default settings
$envContent = <<<ENV
# Culture Radar Environment Configuration
APP_NAME="Culture Radar"
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost:8888

# Database Configuration
# For MAMP default settings
DB_HOST=localhost
DB_PORT=8889
DB_NAME=culture_radar
DB_USER=root
DB_PASS=root

# For XAMPP/WAMP (uncomment if using)
#DB_HOST=localhost
#DB_PORT=3306
#DB_NAME=culture_radar
#DB_USER=root
#DB_PASS=

# API Keys (optional)
OPENAGENDA_API_KEY=
GOOGLE_MAPS_API_KEY=
WEATHER_API_KEY=
ENV;

// Check if .env exists
if (!file_exists(__DIR__ . '/.env')) {
    file_put_contents(__DIR__ . '/.env', $envContent);
    echo "✓ Created .env file with default settings\n";
} else {
    echo "✓ .env file already exists\n";
}

// Create a setup SQL file that can be imported manually
$setupSQL = <<<SQL
-- ====================================
-- Culture Radar Database Setup
-- ====================================

-- Create database
CREATE DATABASE IF NOT EXISTS culture_radar CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE culture_radar;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    accepts_newsletter BOOLEAN DEFAULT FALSE,
    onboarding_completed BOOLEAN DEFAULT FALSE,
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_active_users (is_active, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create user profiles table
CREATE TABLE IF NOT EXISTS user_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    location VARCHAR(255),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    preferences JSON,
    budget_max DECIMAL(8, 2) DEFAULT 0,
    transport_mode ENUM('walking', 'transit', 'driving', 'cycling', 'all') DEFAULT 'all',
    max_distance INT DEFAULT 10,
    accessibility_required BOOLEAN DEFAULT FALSE,
    notification_enabled BOOLEAN DEFAULT TRUE,
    onboarding_completed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_location (location),
    INDEX idx_user_location (user_id, latitude, longitude)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create events table
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    external_id VARCHAR(100),
    source VARCHAR(50) DEFAULT 'openagenda',
    title VARCHAR(255) NOT NULL,
    description TEXT,
    category VARCHAR(100),
    sub_category VARCHAR(100),
    venue_name VARCHAR(255),
    venue_id INT,
    address VARCHAR(500),
    city VARCHAR(100),
    postal_code VARCHAR(20),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    start_date DATETIME NOT NULL,
    end_date DATETIME,
    price DECIMAL(8, 2) DEFAULT 0,
    price_details VARCHAR(255),
    is_free BOOLEAN DEFAULT FALSE,
    image_url VARCHAR(500),
    website_url VARCHAR(500),
    booking_url VARCHAR(500),
    organizer_name VARCHAR(255),
    organizer_contact VARCHAR(255),
    capacity INT,
    accessibility_info TEXT,
    is_accessible BOOLEAN DEFAULT FALSE,
    tags JSON,
    featured BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_city (city),
    INDEX idx_dates (start_date, end_date),
    INDEX idx_location (latitude, longitude),
    INDEX idx_active_featured (is_active, featured)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create venues table
CREATE TABLE IF NOT EXISTS venues (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    type VARCHAR(100),
    address VARCHAR(500),
    city VARCHAR(100),
    postal_code VARCHAR(20),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    phone VARCHAR(50),
    email VARCHAR(255),
    website VARCHAR(500),
    opening_hours JSON,
    accessibility_info TEXT,
    is_accessible BOOLEAN DEFAULT FALSE,
    capacity INT,
    image_url VARCHAR(500),
    rating DECIMAL(3, 2),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_city (city),
    INDEX idx_location (latitude, longitude),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create remember tokens table
CREATE TABLE IF NOT EXISTS remember_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) UNIQUE NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_user_token (user_id, expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Verify tables were created
SHOW TABLES;

-- Insert a test event (optional)
INSERT IGNORE INTO events (title, description, category, venue_name, city, start_date, is_free) 
VALUES ('Test Event', 'This is a test event', 'Culture', 'Test Venue', 'Paris', NOW() + INTERVAL 1 DAY, TRUE);

SELECT 'Database setup complete!' as message;
SQL;

// Save the SQL file
file_put_contents(__DIR__ . '/database/setup-complete.sql', $setupSQL);
echo "✓ Created setup-complete.sql file\n";

// Update the Auth.php file to handle the password_confirm field better
$authFixContent = file_get_contents(__DIR__ . '/classes/Auth.php');
if (strpos($authFixContent, 'password_confirm') === false) {
    // The validation function needs to handle password_confirm properly
    // Let's update the validateRegistration method
    $authFixContent = str_replace(
        "if (\$data['password'] !== \$data['password_confirm']) {",
        "if (isset(\$data['password_confirm']) && \$data['password'] !== \$data['password_confirm']) {",
        $authFixContent
    );
    file_put_contents(__DIR__ . '/classes/Auth.php', $authFixContent);
    echo "✓ Fixed Auth.php validation\n";
}

// Also let's update the register method in api/auth.php to handle the form data properly
echo "\n=== Instructions ===\n";
echo "1. Make sure your web server (MAMP/XAMPP/WAMP) is running\n";
echo "2. Import the database using one of these methods:\n";
echo "   a) PHPMyAdmin: Import /database/setup-complete.sql\n";
echo "   b) Command line: mysql -u root -p < database/setup-complete.sql\n";
echo "3. Update .env file with your database credentials if needed\n";
echo "4. Access the site at http://localhost:8888 (or your configured port)\n";
echo "\n✓ Setup fix complete!\n";