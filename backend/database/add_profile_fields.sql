-- Add user profile fields to users table
-- Run this SQL in phpMyAdmin or MySQL command line

ALTER TABLE users 
ADD COLUMN bio TEXT DEFAULT NULL AFTER designation,
ADD COLUMN profile_picture VARCHAR(255) DEFAULT NULL AFTER bio,
ADD COLUMN phone VARCHAR(20) DEFAULT NULL AFTER profile_picture,
ADD COLUMN location VARCHAR(100) DEFAULT NULL AFTER phone,
ADD COLUMN website VARCHAR(255) DEFAULT NULL AFTER location,
ADD COLUMN linkedin VARCHAR(255) DEFAULT NULL AFTER website,
ADD COLUMN research_interests TEXT DEFAULT NULL AFTER linkedin;

-- Verify the changes
DESCRIBE users;
