-- ============================================
-- Chat & Messaging System Database Schema
-- File: chat_messaging_schema.sql
-- Description: Database tables for real-time chat and messaging
-- ============================================

USE tweakorder;

-- Conversations Table
-- Stores conversation metadata between users/clients
CREATE TABLE IF NOT EXISTS conversations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    type ENUM('direct', 'group', 'support') DEFAULT 'direct',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_archived BOOLEAN DEFAULT 0,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_type (type),
    INDEX idx_updated (updated_at)
);

-- Conversation Participants Table
-- Links users/clients to conversations
CREATE TABLE IF NOT EXISTS conversation_participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT NOT NULL,
    user_id INT,
    client_id INT,
    participant_type ENUM('staff', 'client') NOT NULL,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    left_at TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT 1,
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    INDEX idx_conversation (conversation_id),
    INDEX idx_user (user_id),
    INDEX idx_client (client_id)
);

-- Messages Table
-- Stores all chat messages
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT NOT NULL,
    sender_id INT,
    sender_client_id INT,
    sender_type ENUM('staff', 'client') NOT NULL,
    message_text TEXT NOT NULL,
    message_type ENUM('text', 'image', 'file', 'system') DEFAULT 'text',
    attachment_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_deleted BOOLEAN DEFAULT 0,
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (sender_client_id) REFERENCES clients(id) ON DELETE SET NULL,
    INDEX idx_conversation (conversation_id),
    INDEX idx_created (created_at)
);

-- Message Read Status Table
-- Tracks which messages have been read by which participants
CREATE TABLE IF NOT EXISTS message_read_status (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message_id INT NOT NULL,
    user_id INT,
    client_id INT,
    reader_type ENUM('staff', 'client') NOT NULL,
    read_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (message_id) REFERENCES messages(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    UNIQUE KEY unique_read (message_id, user_id, client_id),
    INDEX idx_message (message_id)
);

-- Chat Notifications Table
-- Stores chat-specific notifications
CREATE TABLE IF NOT EXISTS chat_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT NOT NULL,
    message_id INT,
    user_id INT,
    client_id INT,
    recipient_type ENUM('staff', 'client') NOT NULL,
    notification_type ENUM('new_message', 'new_conversation', 'mention') DEFAULT 'new_message',
    is_read BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (message_id) REFERENCES messages(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_client (client_id),
    INDEX idx_is_read (is_read)
);

-- Insert sample conversations for testing
INSERT INTO conversations (title, type, created_by) VALUES
('Support Team', 'group', 1),
('General Discussion', 'group', 1);

-- Insert sample participants
INSERT INTO conversation_participants (conversation_id, user_id, participant_type) VALUES
(1, 1, 'staff'),
(1, 2, 'staff'),
(2, 1, 'staff'),
(2, 2, 'staff'),
(2, 3, 'staff');

-- Insert sample messages
INSERT INTO messages (conversation_id, sender_id, sender_type, message_text, message_type) VALUES
(1, 1, 'staff', 'Welcome to the support team chat!', 'system'),
(2, 1, 'staff', 'Hello team! This is our general discussion channel.', 'text'),
(2, 2, 'staff', 'Great to be here!', 'text');

-- ============================================
-- End of File: chat_messaging_schema.sql
-- ============================================
