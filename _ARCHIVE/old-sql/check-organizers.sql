-- Check current organizers table structure
DESCRIBE organizers;

-- Show first 5 organizers with their key fields
SELECT 
    id,
    COALESCE(name, company_name) as display_name,
    company_name,
    email,
    CASE 
        WHEN password IS NULL OR password = '' THEN 'NO PASSWORD'
        WHEN LENGTH(password) < 20 THEN 'NEEDS HASHING'
        ELSE 'HASHED'
    END as password_status,
    COALESCE(subscription_type, 
        CASE 
            WHEN subscription_plan IN ('enterprise', 'pro') THEN 'premium'
            WHEN subscription_plan = 'starter' THEN 'basic'
            ELSE 'free'
        END
    ) as subscription_type,
    COALESCE(is_active, 1) as is_active,
    is_verified,
    created_at
FROM organizers
LIMIT 5;

-- Count organizers by subscription type
SELECT 
    COALESCE(subscription_type, 
        CASE 
            WHEN subscription_plan IN ('enterprise', 'pro') THEN 'premium'
            WHEN subscription_plan = 'starter' THEN 'basic'
            ELSE 'free'
        END
    ) as subscription_level,
    COUNT(*) as count
FROM organizers
GROUP BY subscription_level;

-- Show organizers that can be used for testing
SELECT 
    email,
    COALESCE(name, company_name) as name,
    COALESCE(subscription_type, 'free') as subscription,
    CASE 
        WHEN password IS NOT NULL AND LENGTH(password) > 20 THEN 'Ready to login'
        ELSE 'Needs password setup'
    END as status
FROM organizers
WHERE email IN ('contact@livenation.fr', 'marie@jazzparis.org', 'hello@indie-events.fr', 
                'culture@paris.fr', 'info@festprod.com')
   OR email LIKE '%@%radar%'
   OR email LIKE '%test%';