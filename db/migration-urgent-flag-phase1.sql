-- ================================================================
-- MIGRATION: Add Urgent Complaint Flag - PHASE 1
-- Issue: #137 - Add "Urgent Complaint" Flag
-- Created: 2026-01-20
-- Description: Adds support for marking complaints as urgent
--              with justification and timestamp tracking
-- ================================================================

-- Add is_urgent column to mark time-sensitive complaints
-- TINYINT(1) acts as boolean: 0 = not urgent, 1 = urgent
ALTER TABLE complaints 
ADD COLUMN is_urgent TINYINT(1) DEFAULT 0 NOT NULL COMMENT 'Flag for time-sensitive emergencies';

-- Add urgency_justification column for required explanation
-- TEXT allows up to 65,535 characters for detailed explanations
ALTER TABLE complaints 
ADD COLUMN urgency_justification TEXT DEFAULT NULL COMMENT 'Required explanation when complaint marked urgent';

-- Add urgent_marked_at timestamp for tracking when marked urgent
-- NULL for non-urgent complaints, set to NOW() when marked urgent
ALTER TABLE complaints 
ADD COLUMN urgent_marked_at TIMESTAMP NULL DEFAULT NULL COMMENT 'Timestamp when complaint was marked urgent';

-- Create index on is_urgent for faster filtering of urgent complaints
-- This improves query performance when admins filter by urgent status
CREATE INDEX idx_complaints_urgent ON complaints(is_urgent);

-- ================================================================
-- VERIFICATION QUERIES (Run after migration to verify)
-- ================================================================

-- Check if columns were added successfully
-- SHOW COLUMNS FROM complaints LIKE '%urgent%';

-- Verify index was created
-- SHOW INDEX FROM complaints WHERE Key_name = 'idx_complaints_urgent';

-- Test insert with urgent flag
-- INSERT INTO complaints (user_id, complaint_code, crime_type, description, is_urgent, urgency_justification, urgent_marked_at)
-- VALUES (1, 'TEST-2026-00001', 'Test', 'Test urgent complaint', 1, 'This is a test justification with more than 10 characters', NOW());

-- Query urgent complaints
-- SELECT complaint_code, is_urgent, urgency_justification, urgent_marked_at FROM complaints WHERE is_urgent = 1;

-- ================================================================
-- ROLLBACK (Use only if you need to undo this migration)
-- ================================================================

-- DROP INDEX idx_complaints_urgent ON complaints;
-- ALTER TABLE complaints DROP COLUMN urgent_marked_at;
-- ALTER TABLE complaints DROP COLUMN urgency_justification;
-- ALTER TABLE complaints DROP COLUMN is_urgent;
