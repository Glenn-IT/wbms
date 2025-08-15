-- Sample data for client_issue_list table

INSERT INTO `client_issue_list` (`client_id`, `issue_title`, `remarks`, `status`, `date_created`) VALUES
(14, 'Water Meter Not Working', 'Customer reported that the water meter stopped functioning properly. Need to inspect and repair or replace.', 0, '2025-08-15 10:30:00'),
(15, 'Billing Discrepancy', 'Customer disputes the current billing amount. Claims usage reading is incorrect.', 0, '2025-08-15 11:15:00'),
(16, 'Leak in Connection', 'Water leak detected near the meter connection. Requires immediate attention to prevent water wastage.', 1, '2025-08-15 09:45:00');
