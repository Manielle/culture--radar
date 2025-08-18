-- Culture Radar Database Optimization Script
-- Run this to improve performance

-- ====================================
-- INDEXES FOR BETTER QUERY PERFORMANCE
-- ====================================

-- Users table indexes
CREATE INDEX IF NOT EXISTS idx_users_email_password ON users(email, password);
CREATE INDEX IF NOT EXISTS idx_users_last_login ON users(last_login);
CREATE INDEX IF NOT EXISTS idx_users_created_date ON users(DATE(created_at));

-- Events table indexes  
CREATE INDEX IF NOT EXISTS idx_events_category_city_date ON events(category, city, start_date);
CREATE INDEX IF NOT EXISTS idx_events_price_range ON events(price, is_free);
CREATE INDEX IF NOT EXISTS idx_events_date_range ON events(start_date, end_date);
CREATE INDEX IF NOT EXISTS idx_events_location_active ON events(latitude, longitude, is_active);
CREATE INDEX IF NOT EXISTS idx_events_featured ON events(featured, start_date) WHERE featured = 1;
CREATE FULLTEXT INDEX IF NOT EXISTS idx_events_search ON events(title, description, venue_name);

-- User interactions table indexes
CREATE INDEX IF NOT EXISTS idx_interactions_user_type_date ON user_interactions(user_id, interaction_type, created_at);
CREATE INDEX IF NOT EXISTS idx_interactions_event_rating ON user_interactions(event_id, rating) WHERE rating IS NOT NULL;
CREATE INDEX IF NOT EXISTS idx_interactions_recent ON user_interactions(created_at) WHERE created_at > DATE_SUB(NOW(), INTERVAL 30 DAY);

-- User recommendations table indexes
CREATE INDEX IF NOT EXISTS idx_recommendations_user_score ON user_recommendations(user_id, match_score DESC);
CREATE INDEX IF NOT EXISTS idx_recommendations_not_viewed ON user_recommendations(user_id, is_viewed) WHERE is_viewed = 0;

-- Venues table indexes
CREATE INDEX IF NOT EXISTS idx_venues_city_category ON venues(city, category);
CREATE INDEX IF NOT EXISTS idx_venues_badge ON venues(has_culture_radar_badge) WHERE has_culture_radar_badge = 1;
CREATE SPATIAL INDEX IF NOT EXISTS idx_venues_spatial ON venues(latitude, longitude);

-- ====================================
-- PARTITIONING FOR LARGE TABLES
-- ====================================

-- Partition user_interactions by month (for better performance with historical data)
ALTER TABLE user_interactions 
PARTITION BY RANGE (TO_DAYS(created_at)) (
    PARTITION p_2024_01 VALUES LESS THAN (TO_DAYS('2024-02-01')),
    PARTITION p_2024_02 VALUES LESS THAN (TO_DAYS('2024-03-01')),
    PARTITION p_2024_03 VALUES LESS THAN (TO_DAYS('2024-04-01')),
    PARTITION p_2024_04 VALUES LESS THAN (TO_DAYS('2024-05-01')),
    PARTITION p_2024_05 VALUES LESS THAN (TO_DAYS('2024-06-01')),
    PARTITION p_2024_06 VALUES LESS THAN (TO_DAYS('2024-07-01')),
    PARTITION p_2024_07 VALUES LESS THAN (TO_DAYS('2024-08-01')),
    PARTITION p_2024_08 VALUES LESS THAN (TO_DAYS('2024-09-01')),
    PARTITION p_2024_09 VALUES LESS THAN (TO_DAYS('2024-10-01')),
    PARTITION p_2024_10 VALUES LESS THAN (TO_DAYS('2024-11-01')),
    PARTITION p_2024_11 VALUES LESS THAN (TO_DAYS('2024-12-01')),
    PARTITION p_2024_12 VALUES LESS THAN (TO_DAYS('2025-01-01')),
    PARTITION p_future VALUES LESS THAN MAXVALUE
);

-- ====================================
-- MATERIALIZED VIEWS (using tables)
-- ====================================

-- Create materialized view for event statistics
DROP TABLE IF EXISTS mv_event_stats;
CREATE TABLE mv_event_stats AS
SELECT 
    e.id as event_id,
    e.title,
    e.category,
    e.city,
    COUNT(DISTINCT ui.user_id) as unique_viewers,
    COUNT(CASE WHEN ui.interaction_type = 'save' THEN 1 END) as save_count,
    COUNT(CASE WHEN ui.interaction_type = 'share' THEN 1 END) as share_count,
    AVG(ui.rating) as avg_rating,
    COUNT(ui.rating) as rating_count,
    MAX(ui.created_at) as last_interaction
FROM events e
LEFT JOIN user_interactions ui ON e.id = ui.event_id
GROUP BY e.id;

CREATE INDEX idx_mv_event_stats_category ON mv_event_stats(category);
CREATE INDEX idx_mv_event_stats_city ON mv_event_stats(city);
CREATE INDEX idx_mv_event_stats_popularity ON mv_event_stats(unique_viewers DESC, avg_rating DESC);

-- Create materialized view for user preferences analysis
DROP TABLE IF EXISTS mv_user_preferences;
CREATE TABLE mv_user_preferences AS
SELECT 
    u.id as user_id,
    u.name,
    up.location,
    up.preferences,
    COUNT(DISTINCT ui.event_id) as events_interacted,
    COUNT(DISTINCT e.category) as categories_explored,
    AVG(e.price) as avg_event_price,
    MAX(ui.created_at) as last_activity
FROM users u
LEFT JOIN user_profiles up ON u.id = up.user_id
LEFT JOIN user_interactions ui ON u.id = ui.user_id
LEFT JOIN events e ON ui.event_id = e.id
GROUP BY u.id;

CREATE INDEX idx_mv_user_preferences_location ON mv_user_preferences(location);
CREATE INDEX idx_mv_user_preferences_activity ON mv_user_preferences(last_activity DESC);

-- ====================================
-- STORED PROCEDURES FOR COMPLEX QUERIES
-- ====================================

DELIMITER $$

-- Procedure to get trending events
DROP PROCEDURE IF EXISTS sp_get_trending_events$$
CREATE PROCEDURE sp_get_trending_events(
    IN p_limit INT,
    IN p_days INT
)
BEGIN
    SELECT 
        e.*,
        COUNT(DISTINCT ui.user_id) as interaction_count,
        AVG(ui.rating) as avg_rating
    FROM events e
    INNER JOIN user_interactions ui ON e.id = ui.event_id
    WHERE 
        e.is_active = 1 
        AND e.start_date > NOW()
        AND ui.created_at >= DATE_SUB(NOW(), INTERVAL p_days DAY)
    GROUP BY e.id
    ORDER BY interaction_count DESC, avg_rating DESC
    LIMIT p_limit;
END$$

-- Procedure to get personalized recommendations
DROP PROCEDURE IF EXISTS sp_get_recommendations$$
CREATE PROCEDURE sp_get_recommendations(
    IN p_user_id INT,
    IN p_limit INT
)
BEGIN
    DECLARE user_city VARCHAR(100);
    DECLARE user_budget DECIMAL(8,2);
    DECLARE user_prefs JSON;
    
    -- Get user preferences
    SELECT location, budget_max, preferences 
    INTO user_city, user_budget, user_prefs
    FROM user_profiles 
    WHERE user_id = p_user_id;
    
    -- Get recommended events
    SELECT 
        e.*,
        CASE 
            WHEN JSON_CONTAINS(user_prefs, JSON_QUOTE(e.category)) THEN 40
            ELSE 0
        END +
        CASE 
            WHEN e.city = user_city THEN 25
            ELSE 0
        END +
        CASE 
            WHEN e.price <= user_budget THEN 15
            ELSE 0
        END +
        CASE 
            WHEN e.is_free = 1 THEN 10
            ELSE 0
        END +
        COALESCE(es.unique_viewers * 0.1, 0) +
        COALESCE(es.avg_rating * 2, 0) AS score
    FROM events e
    LEFT JOIN mv_event_stats es ON e.id = es.event_id
    WHERE 
        e.is_active = 1 
        AND e.start_date > NOW()
        AND e.id NOT IN (
            SELECT event_id 
            FROM user_interactions 
            WHERE user_id = p_user_id 
            AND interaction_type IN ('view', 'save')
        )
    ORDER BY score DESC
    LIMIT p_limit;
END$$

-- Procedure to refresh materialized views
DROP PROCEDURE IF EXISTS sp_refresh_materialized_views$$
CREATE PROCEDURE sp_refresh_materialized_views()
BEGIN
    -- Refresh event stats
    TRUNCATE TABLE mv_event_stats;
    INSERT INTO mv_event_stats
    SELECT 
        e.id as event_id,
        e.title,
        e.category,
        e.city,
        COUNT(DISTINCT ui.user_id) as unique_viewers,
        COUNT(CASE WHEN ui.interaction_type = 'save' THEN 1 END) as save_count,
        COUNT(CASE WHEN ui.interaction_type = 'share' THEN 1 END) as share_count,
        AVG(ui.rating) as avg_rating,
        COUNT(ui.rating) as rating_count,
        MAX(ui.created_at) as last_interaction
    FROM events e
    LEFT JOIN user_interactions ui ON e.id = ui.event_id
    GROUP BY e.id;
    
    -- Refresh user preferences
    TRUNCATE TABLE mv_user_preferences;
    INSERT INTO mv_user_preferences
    SELECT 
        u.id as user_id,
        u.name,
        up.location,
        up.preferences,
        COUNT(DISTINCT ui.event_id) as events_interacted,
        COUNT(DISTINCT e.category) as categories_explored,
        AVG(e.price) as avg_event_price,
        MAX(ui.created_at) as last_activity
    FROM users u
    LEFT JOIN user_profiles up ON u.id = up.user_id
    LEFT JOIN user_interactions ui ON u.id = ui.user_id
    LEFT JOIN events e ON ui.event_id = e.id
    GROUP BY u.id;
END$$

DELIMITER ;

-- ====================================
-- QUERY OPTIMIZATION EXAMPLES
-- ====================================

-- Example: Optimized query for homepage events
-- Before: Full table scan
-- SELECT * FROM events WHERE is_active = 1 AND start_date > NOW() ORDER BY start_date LIMIT 10;

-- After: Uses index
-- SELECT /*+ INDEX(events idx_events_date_range) */ 
--     * FROM events 
-- WHERE is_active = 1 AND start_date > NOW() 
-- ORDER BY start_date 
-- LIMIT 10;

-- ====================================
-- MAINTENANCE TASKS
-- ====================================

-- Analyze tables for optimizer statistics
ANALYZE TABLE users;
ANALYZE TABLE events;
ANALYZE TABLE user_interactions;
ANALYZE TABLE user_recommendations;
ANALYZE TABLE venues;

-- Optimize tables to reclaim space
OPTIMIZE TABLE user_interactions;
OPTIMIZE TABLE user_recommendations;

-- ====================================
-- SCHEDULED EVENTS FOR AUTOMATION
-- ====================================

-- Create event to refresh materialized views daily
DROP EVENT IF EXISTS refresh_views_daily;
CREATE EVENT refresh_views_daily
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_DATE + INTERVAL 2 HOUR
DO CALL sp_refresh_materialized_views();

-- Create event to clean old interactions monthly
DROP EVENT IF EXISTS clean_old_data_monthly;
CREATE EVENT clean_old_data_monthly
ON SCHEDULE EVERY 1 MONTH
STARTS CURRENT_DATE + INTERVAL 1 DAY
DO DELETE FROM user_interactions 
   WHERE created_at < DATE_SUB(NOW(), INTERVAL 12 MONTH);

-- Enable event scheduler
SET GLOBAL event_scheduler = ON;

-- ====================================
-- MONITORING QUERIES
-- ====================================

-- Check slow queries
-- SELECT * FROM mysql.slow_log ORDER BY query_time DESC LIMIT 10;

-- Check table sizes
-- SELECT 
--     table_name,
--     ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb
-- FROM information_schema.tables
-- WHERE table_schema = 'culture_radar'
-- ORDER BY (data_length + index_length) DESC;

-- Check index usage
-- SELECT 
--     t.table_name,
--     s.index_name,
--     s.cardinality
-- FROM information_schema.statistics s
-- JOIN information_schema.tables t ON s.table_name = t.table_name
-- WHERE t.table_schema = 'culture_radar'
-- ORDER BY t.table_name, s.index_name;