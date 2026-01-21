-- ====================================================================
-- MIGRATION: Anonymous Complaints Feature
-- Description: Add support for anonymous crime reporting
-- Date: 2026-01-16
-- Issue: #131 - Add anonymous crime reporting option
-- ====================================================================

USE jan_suraksha;

-- Step 1: Add is_anonymous flag column
ALTER TABLE complaints 
ADD COLUMN is_anonymous TINYINT(1) DEFAULT 0 NOT NULL 
COMMENT 'Flag: 1 = Anonymous complaint, 0 = Regular complaint';

-- Step 2: Add anonymous tracking ID column
ALTER TABLE complaints 
ADD COLUMN anonymous_tracking_id VARCHAR(100) DEFAULT NULL 
COMMENT 'Unique tracking ID for anonymous complaints (format: ANON-YYYY-XXXXXX)';

-- Step 3: Make personal information fields NULLABLE for anonymous complaints
ALTER TABLE complaints 
MODIFY COLUMN complainant_name VARCHAR(255) DEFAULT NULL,
MODIFY COLUMN mobile VARCHAR(50) DEFAULT NULL;

-- Step 4: Add unique constraint on anonymous_tracking_id
ALTER TABLE complaints 
ADD UNIQUE KEY unique_anonymous_tracking_id (anonymous_tracking_id);

-- Step 5: Add index on is_anonymous for faster filtering
ALTER TABLE complaints 
ADD INDEX idx_is_anonymous (is_anonymous);

-- Step 6: Add composite index for anonymous complaint lookups
ALTER TABLE complaints 
ADD INDEX idx_anonymous_lookup (is_anonymous, anonymous_tracking_id);

-- ====================================================================
-- VERIFICATION QUERY
-- Run this to verify the migration was successful:
-- ====================================================================
-- DESCRIBE complaints;
-- SHOW INDEX FROM complaints;

-- ====================================================================
-- ROLLBACK SCRIPT (Use only if migration needs to be reverted)
-- ====================================================================
/*
USE jan_suraksha;

-- Remove indexes
ALTER TABLE complaints DROP INDEX idx_anonymous_lookup;
ALTER TABLE complaints DROP INDEX idx_is_anonymous;
ALTER TABLE complaints DROP INDEX unique_anonymous_tracking_id;

-- Make personal fields NOT NULL again (careful: may fail if NULL data exists)
ALTER TABLE complaints 
MODIFY COLUMN complainant_name VARCHAR(255) NOT NULL,
MODIFY COLUMN mobile VARCHAR(50) NOT NULL;

-- Remove anonymous columns
ALTER TABLE complaints DROP COLUMN anonymous_tracking_id;
ALTER TABLE complaints DROP COLUMN is_anonymous;
*/

-- ====================================================================
-- TEST DATA (Optional - for testing purposes only)
-- ====================================================================
/*
-- Insert a test anonymous complaint
INSERT INTO complaints (
    user_id, 
    complaint_code, 
    complainant_name, 
    mobile, 
    crime_type, 
    location, 
    description, 
    status,
    is_anonymous,
    anonymous_tracking_id
) VALUES (
    0,
    'TEST/2026/00001',
    NULL,
    NULL,
    'Cybercrime',
    'Test Location',
    'This is a test anonymous complaint',
    'Pending',
    1,
    'ANON-2026-TEST01'
);

-- Verify the test data
SELECT * FROM complaints WHERE is_anonymous = 1;

-- Clean up test data
DELETE FROM complaints WHERE complaint_code = 'TEST/2026/00001';
*/

-- ====================================================================
-- MIGRATION COMPLETE
-- ====================================================================
