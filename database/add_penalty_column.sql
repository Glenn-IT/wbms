-- Add penalty column to billing_list table
ALTER TABLE `billing_list` ADD COLUMN `penalty` FLOAT(12,2) NOT NULL DEFAULT 0.00 AFTER `total`;

-- Verify the column was added
DESCRIBE `billing_list`;
