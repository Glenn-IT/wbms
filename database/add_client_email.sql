-- Add email column to client_list table
ALTER TABLE `client_list` 
ADD COLUMN `email` VARCHAR(255) NULL DEFAULT NULL AFTER `contact`;
