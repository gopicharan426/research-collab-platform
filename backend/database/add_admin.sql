-- Add admin role to users table
ALTER TABLE users ADD COLUMN is_admin TINYINT(1) DEFAULT 0;

-- Make test user an admin (password: password123)
UPDATE users SET is_admin = 1 WHERE email = 'test@example.com';
