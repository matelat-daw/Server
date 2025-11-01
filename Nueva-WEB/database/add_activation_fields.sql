-- Add activation fields to users table
ALTER TABLE users 
ADD COLUMN is_active BOOLEAN DEFAULT 0 AFTER profile_img,
ADD COLUMN activation_token VARCHAR(64) DEFAULT NULL AFTER is_active,
ADD COLUMN activation_token_expires DATETIME DEFAULT NULL AFTER activation_token;

-- Create index for faster token lookup
CREATE INDEX idx_activation_token ON users(activation_token);

-- Update existing users to be active (backward compatibility)
UPDATE users SET is_active = 1 WHERE activation_token IS NULL;
