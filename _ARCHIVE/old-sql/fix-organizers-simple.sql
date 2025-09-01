-- SIMPLE VERSION - Run each section separately if needed

-- Section 1: First check your current table structure
DESCRIBE organizers;

-- Section 2: Try to add missing columns (ignore errors if columns already exist)
-- Run each ALTER TABLE separately and ignore "Duplicate column" errors

ALTER TABLE organizers ADD COLUMN name VARCHAR(255) AFTER id;
-- If error "Duplicate column", that's OK, continue to next

ALTER TABLE organizers ADD COLUMN is_active BOOLEAN DEFAULT TRUE AFTER commission_rate;  
-- If error "Duplicate column", that's OK, continue to next

ALTER TABLE organizers ADD COLUMN subscription_type ENUM('free', 'basic', 'premium') DEFAULT 'free' AFTER annual_events;
-- If error "Duplicate column", that's OK, continue to next

-- Section 3: Update values for all rows
UPDATE organizers 
SET name = COALESCE(name, company_name)
WHERE name IS NULL OR name = '';

UPDATE organizers 
SET is_active = 1
WHERE is_active IS NULL;

UPDATE organizers 
SET subscription_type = 
    CASE 
        WHEN subscription_plan IN ('enterprise', 'pro') THEN 'premium'
        WHEN subscription_plan = 'starter' THEN 'basic'
        ELSE 'free'
    END
WHERE subscription_type IS NULL OR subscription_type = '';

-- Section 4: Set password for ALL existing organizers (for testing)
-- Password: organizer123
UPDATE organizers 
SET password = '$2y$10$YmG8w.C2mQXKlvLp7RP8w.uxzLlYxL2wJFfHxhNOyg0BKJLXvUqCe',
    is_active = 1;

-- Section 5: Check the results
SELECT 
    id,
    email,
    name,
    company_name,
    subscription_type,
    subscription_plan,
    is_active,
    'organizer123' as test_password
FROM organizers
LIMIT 10;