-- Add/Update test organizer accounts with proper passwords
-- Password for all test accounts: organizer123
-- Hashed password: $2y$10$YmG8w.C2mQXKlvLp7RP8w.uxzLlYxL2wJFfHxhNOyg0BKJLXvUqCe

-- First, update the structure to ensure all required fields exist
ALTER TABLE organizers 
ADD COLUMN IF NOT EXISTS name VARCHAR(255) AFTER id,
ADD COLUMN IF NOT EXISTS is_active BOOLEAN DEFAULT TRUE AFTER commission_rate,
ADD COLUMN IF NOT EXISTS subscription_type ENUM('free', 'basic', 'premium') DEFAULT 'free' AFTER annual_events;

-- Update existing records to have proper values
UPDATE organizers 
SET name = COALESCE(name, company_name),
    is_active = COALESCE(is_active, 1),
    subscription_type = COALESCE(subscription_type, 
        CASE 
            WHEN subscription_plan IN ('enterprise', 'pro') THEN 'premium'
            WHEN subscription_plan = 'starter' THEN 'basic'
            ELSE 'free'
        END
    );

-- Update passwords for existing test accounts (if they exist)
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

-- Insert a simple test organizer if none exist with known emails
INSERT IGNORE INTO organizers (
    name, company_name, contact_name, email, password,
    subscription_type, subscription_plan, is_active, is_verified
)
VALUES (
    'Test Organizer',
    'Test Events Company',
    'Test Admin',
    'test@organizer.fr',
    '$2y$10$YmG8w.C2mQXKlvLp7RP8w.uxzLlYxL2wJFfHxhNOyg0BKJLXvUqCe',
    'premium',
    'pro',
    1,
    1
);

-- Show updated test accounts
SELECT 
    email,
    name,
    subscription_type,
    is_active,
    'organizer123' as password_to_use
FROM organizers
WHERE is_active = 1
  AND password IS NOT NULL
  AND LENGTH(password) > 20
ORDER BY subscription_type DESC, id
LIMIT 10;