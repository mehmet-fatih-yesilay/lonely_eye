-- Database Updates for Hybrid Logic Support
-- Run this file to add necessary columns and tables

USE lonely_eye;

-- Add google_id and language columns to items table
ALTER TABLE items 
ADD COLUMN google_id VARCHAR(255) UNIQUE NULL AFTER id,
ADD COLUMN language VARCHAR(10) NULL AFTER genre_id,
ADD INDEX idx_google_id (google_id);

-- Create favorites table
CREATE TABLE IF NOT EXISTS favorites (
    user_id INT NOT NULL,
    item_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, item_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
