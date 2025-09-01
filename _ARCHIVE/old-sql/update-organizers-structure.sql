-- Update existing organizers table structure without losing data
-- Add missing columns if they don't exist

-- Add 'name' column if it doesn't exist (required by dashboard)
ALTER TABLE organizers 
ADD COLUMN IF NOT EXISTS name VARCHAR(255) AFTER id;

-- Update name from company_name if it's NULL
UPDATE organizers 
SET name = company_name 
WHERE name IS NULL OR name = '';

-- Add 'is_active' column if it doesn't exist (required by login)
ALTER TABLE organizers 
ADD COLUMN IF NOT EXISTS is_active BOOLEAN DEFAULT TRUE AFTER commission_rate;

-- Add 'subscription_type' column if it doesn't exist (required by dashboard UI)
ALTER TABLE organizers 
ADD COLUMN IF NOT EXISTS subscription_type ENUM('free', 'basic', 'premium') DEFAULT 'free' AFTER annual_events;

-- Update subscription_type based on subscription_plan if it exists
UPDATE organizers 
SET subscription_type = CASE 
    WHEN subscription_plan IN ('enterprise', 'pro') THEN 'premium'
    WHEN subscription_plan = 'starter' THEN 'basic'
    ELSE 'free'
END
WHERE subscription_type IS NULL OR subscription_type = '';

-- Set all existing organizers as active
UPDATE organizers SET is_active = 1 WHERE is_active IS NULL;

-- Make name NOT NULL after populating it
ALTER TABLE organizers 
MODIFY COLUMN name VARCHAR(255) NOT NULL;

-- Add indexes if they don't exist
ALTER TABLE organizers 
ADD INDEX IF NOT EXISTS idx_active (is_active);

-- Check if we need to update any existing organizer passwords
-- This will show you which organizers need password updates
SELECT id, name, email, 
       CASE 
           WHEN password IS NULL OR password = '' THEN 'Needs password'
           WHEN LENGTH(password) < 20 THEN 'Password too short (not hashed)'
           ELSE 'OK'
       END as password_status
FROM organizers;

-- If you need to set a default password for testing (organizer123), uncomment below:
-- UPDATE organizers 
-- SET password = '$2y$10$YmG8w.C2mQXKlvLp7RP8w.uxzLlYxL2wJFfHxhNOyg0BKJLXvUqCe'
-- WHERE password IS NULL OR password = '' OR LENGTH(password) < 20;