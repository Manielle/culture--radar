-- MySQL Compatible Version - Update organizers table structure
-- This script checks for columns before adding them

-- Step 1: Check and add 'name' column
SET @dbname = DATABASE();
SET @tablename = 'organizers';
SET @columnname = 'name';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
      AND TABLE_NAME = @tablename
      AND COLUMN_NAME = @columnname
  ) > 0,
  "SELECT 'Column name already exists'",
  "ALTER TABLE organizers ADD COLUMN name VARCHAR(255) AFTER id"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Step 2: Check and add 'is_active' column
SET @columnname = 'is_active';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
      AND TABLE_NAME = @tablename
      AND COLUMN_NAME = @columnname
  ) > 0,
  "SELECT 'Column is_active already exists'",
  "ALTER TABLE organizers ADD COLUMN is_active BOOLEAN DEFAULT TRUE AFTER commission_rate"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Step 3: Check and add 'subscription_type' column
SET @columnname = 'subscription_type';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
      AND TABLE_NAME = @tablename
      AND COLUMN_NAME = @columnname
  ) > 0,
  "SELECT 'Column subscription_type already exists'",
  "ALTER TABLE organizers ADD COLUMN subscription_type ENUM('free', 'basic', 'premium') DEFAULT 'free' AFTER annual_events"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Step 4: Update the values for existing rows
UPDATE organizers 
SET name = CASE 
    WHEN name IS NULL OR name = '' THEN company_name 
    ELSE name 
END;

UPDATE organizers 
SET is_active = CASE 
    WHEN is_active IS NULL THEN 1 
    ELSE is_active 
END;

UPDATE organizers 
SET subscription_type = CASE 
    WHEN subscription_type IS NULL OR subscription_type = '' THEN
        CASE 
            WHEN subscription_plan IN ('enterprise', 'pro') THEN 'premium'
            WHEN subscription_plan = 'starter' THEN 'basic'
            ELSE 'free'
        END
    ELSE subscription_type
END;

-- Step 5: Set passwords for test accounts
UPDATE organizers 
SET password = '$2y$10$YmG8w.C2mQXKlvLp7RP8w.uxzLlYxL2wJFfHxhNOyg0BKJLXvUqCe',
    is_active = 1
WHERE email IN (
    'contact@livenation.fr',
    'marie@jazzparis.org', 
    'hello@indie-events.fr',
    'culture@paris.fr',
    'direction@theatre-national.fr',
    'contact@mam-paris.fr',
    'info@festprod.com',
    'booking@concertsco.fr',
    'administration@opera-paris.fr',
    'events@grandpalais.fr',
    'contact@startupculture.io',
    'lucas@eventprod.fr'
);

-- Step 6: Insert a test account if it doesn't exist
INSERT INTO organizers (
    name, company_name, contact_name, email, password,
    subscription_type, subscription_plan, is_active, is_verified
)
SELECT 
    'Test Organizer',
    'Test Events Company',
    'Test Admin',
    'test@organizer.fr',
    '$2y$10$YmG8w.C2mQXKlvLp7RP8w.uxzLlYxL2wJFfHxhNOyg0BKJLXvUqCe',
    'premium',
    'pro',
    1,
    1
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM organizers WHERE email = 'test@organizer.fr'
);

-- Step 7: Show available accounts
SELECT 
    email,
    COALESCE(name, company_name) as display_name,
    COALESCE(subscription_type, 'free') as subscription,
    CASE 
        WHEN is_active = 1 THEN 'Active'
        ELSE 'Inactive'
    END as status,
    'organizer123' as password_to_use
FROM organizers
WHERE is_active = 1
ORDER BY 
    CASE subscription_type
        WHEN 'premium' THEN 1
        WHEN 'basic' THEN 2
        WHEN 'free' THEN 3
        ELSE 4
    END,
    id DESC
LIMIT 10;