-- Add organizer_id to events table if it doesn't exist
ALTER TABLE events 
ADD COLUMN IF NOT EXISTS organizer_id INT DEFAULT NULL AFTER external_url,
ADD INDEX IF NOT EXISTS idx_organizer_id (organizer_id);

-- Link some existing events to organizers (optional)
UPDATE events SET organizer_id = 1 WHERE id IN (1, 2, 3, 4, 5) AND organizer_id IS NULL;
UPDATE events SET organizer_id = 2 WHERE id IN (6, 7, 8) AND organizer_id IS NULL;  
UPDATE events SET organizer_id = 3 WHERE id IN (9, 10) AND organizer_id IS NULL;