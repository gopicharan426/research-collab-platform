-- Add role-based columns to users table
-- Run this SQL in phpMyAdmin or MySQL command line

ALTER TABLE users 
ADD COLUMN role ENUM('student', 'professor') DEFAULT NULL AFTER is_admin,
ADD COLUMN department VARCHAR(100) DEFAULT NULL AFTER role,
ADD COLUMN class VARCHAR(50) DEFAULT NULL AFTER department,
ADD COLUMN designation VARCHAR(100) DEFAULT NULL AFTER class;

-- Verify the changes
DESCRIBE users;
