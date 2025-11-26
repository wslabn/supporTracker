-- Add read status tracking to messages
ALTER TABLE ticket_messages 
ADD COLUMN is_read BOOLEAN DEFAULT FALSE,
ADD COLUMN read_at TIMESTAMP NULL;