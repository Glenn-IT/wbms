-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 24, 2025 at 01:05 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wbms_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `billing_list`
--

CREATE TABLE `billing_list` (
  `id` int(30) NOT NULL,
  `client_id` int(30) NOT NULL,
  `reading_date` date NOT NULL,
  `due_date` date NOT NULL,
  `reading` float(12,2) NOT NULL DEFAULT 0.00,
  `previous` float(12,2) NOT NULL DEFAULT 0.00,
  `rate` float(12,2) NOT NULL DEFAULT 0.00,
  `total` float(12,2) NOT NULL DEFAULT 0.00,
  `penalty` float(12,2) NOT NULL DEFAULT 0.00,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0= pending,\r\n1= paid',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `billing_list`
--

INSERT INTO `billing_list` (`id`, `client_id`, `reading_date`, `due_date`, `reading`, `previous`, `rate`, `total`, `penalty`, `status`, `date_created`, `date_updated`) VALUES
(24, 24, '2025-07-30', '2025-08-30', 2345.00, 0.00, 10.75, 26469.19, 1260.44, 1, '2025-10-24 18:41:04', '2025-10-24 18:53:22'),
(25, 25, '2025-06-04', '2025-07-04', 9999.00, 0.00, 10.75, 107489.25, 0.00, 0, '2025-10-24 19:00:14', '2025-10-24 19:00:14');

-- --------------------------------------------------------

--
-- Table structure for table `category_list`
--

CREATE TABLE `category_list` (
  `id` int(30) NOT NULL,
  `name` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `delete_flag` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category_list`
--

INSERT INTO `category_list` (`id`, `name`, `status`, `delete_flag`, `date_created`, `date_updated`) VALUES
(1, 'Residential', 1, 0, '2022-05-02 15:13:02', '2022-05-02 15:13:02'),
(2, 'Commercial', 1, 0, '2022-05-02 15:13:09', '2022-05-02 15:13:09');

-- --------------------------------------------------------

--
-- Table structure for table `client_issue_list`
--

CREATE TABLE `client_issue_list` (
  `id` int(30) NOT NULL,
  `client_id` int(30) NOT NULL,
  `issue_title` varchar(255) NOT NULL,
  `remarks` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=Pending, 1=Resolved',
  `date_resolved` datetime DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `client_issue_list`
--

INSERT INTO `client_issue_list` (`id`, `client_id`, `issue_title`, `remarks`, `image_path`, `status`, `date_resolved`, `date_created`, `date_updated`) VALUES
(1, 14, 'Water Meter Not Working', 'Customer reported that the water meter stopped functioning properly. Need to inspect and repair or replace.', '20250815145656_689eda38e89e6.jpg', 0, NULL, '2025-08-15 14:56:06', '2025-08-15 14:56:56'),
(2, 15, 'Billing Discrepancy', 'Customer disputes the current billing amount. Claims usage reading is incorrect.', NULL, 1, '2025-08-15 15:05:42', '2025-08-15 14:56:06', '2025-08-15 15:05:42'),
(3, 16, 'Leak in Connection', 'Water leak detected near the meter connection. Requires immediate attention to prevent water wastage.', NULL, 1, '2025-08-15 14:56:43', '2025-08-15 14:56:06', '2025-08-15 14:58:27'),
(4, 14, 'Oks wer', 'skpdwm', '20250815150036_689edb1408efd.jpg', 0, NULL, '2025-08-15 15:00:36', '2025-08-15 15:00:36');

-- --------------------------------------------------------

--
-- Table structure for table `client_list`
--

CREATE TABLE `client_list` (
  `id` int(30) NOT NULL,
  `code` varchar(100) NOT NULL,
  `category_id` int(30) NOT NULL,
  `firstname` text NOT NULL,
  `middlename` text DEFAULT NULL,
  `lastname` text NOT NULL,
  `contact` text NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text NOT NULL,
  `meter_code` varchar(100) NOT NULL,
  `first_reading` float(12,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `delete_flag` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `client_list`
--

INSERT INTO `client_list` (`id`, `code`, `category_id`, `firstname`, `middlename`, `lastname`, `contact`, `email`, `address`, `meter_code`, `first_reading`, `status`, `delete_flag`, `date_created`, `date_updated`) VALUES
(3, '202508040001', 1, 'Sample', 'Sample', 'Sample', '09798987722', NULL, 'Zone 5', '1122', 1320.00, 1, 1, '2025-08-04 12:19:56', '2025-08-15 12:54:40'),
(4, '202508040002', 1, 'Sample2', 'Sample2', 'Sample2', '09987987987', NULL, 'Zone 4', '0123', 231.00, 1, 1, '2025-08-04 12:21:58', '2025-08-15 12:54:37'),
(5, '202508040003', 1, 'Glenard', 'U', 'Pagurayan', '09879798798', NULL, 'Zone 2', 'PS409', 200.00, 1, 1, '2025-08-04 16:32:02', '2025-08-15 12:54:32'),
(6, '202508040004', 1, 'Glenard', 'U', 'Pagurayan', '09787979879', NULL, 'Zone 1', 'LV645', 200.00, 1, 1, '2025-08-04 16:53:39', '2025-08-15 12:54:30'),
(7, '202508040005', 1, 'Glenard', 'U', 'Pagurayan', '08979797987', NULL, 'Zone 1', 'JG574', 3000.00, 1, 1, '2025-08-04 16:54:29', '2025-08-15 12:54:27'),
(8, '202508040006', 1, 'Glenard', 'U', 'Pagurayan', '03213213213', NULL, 'Zone 1', 'ZD709', 200.00, 1, 1, '2025-08-04 16:56:01', '2025-08-15 12:54:24'),
(9, '202508150001', 1, 'Kiowe', 'Koks', 'Namsa', '09879878979', NULL, 'Zone 1', 'RL634', 55.00, 1, 1, '2025-08-15 12:55:25', '2025-08-15 13:13:25'),
(10, '202508150002', 1, 'Jsud', 'JJust', 'Kosid', '09787987775', NULL, 'Zone 2', 'JF681', 36.00, 1, 1, '2025-08-15 12:59:04', '2025-08-15 13:13:22'),
(11, '202508150003', 1, 'Kolsp', 'LLpao', 'Iuwe', '09789798789', NULL, 'Zone 3', 'QQ099', 25.00, 1, 1, '2025-08-15 12:59:33', '2025-08-15 13:13:17'),
(12, '202508150004', 1, 'Lopd', 'OOks', 'Sudiwj', '09987987987', NULL, 'Zone 4', 'LC094', 57.00, 1, 1, '2025-08-15 13:02:08', '2025-08-15 13:13:14'),
(13, '202508150005', 1, 'Kosdw', 'Sdweq', 'Opdw', '09798797979', NULL, 'Zone 5', 'MJ165', 25.00, 1, 1, '2025-08-15 13:04:14', '2025-08-15 13:13:11'),
(14, '202508150001', 1, 'Kidow', 'Lokdw', 'Okwe', '09798798798', NULL, 'Zone 1', 'DL991', 0.00, 1, 1, '2025-08-15 13:13:38', '2025-09-11 11:35:24'),
(15, '202508150002', 1, 'Lowep', 'Poew', 'Kidw', '09879879879', NULL, 'Zone 2', 'IW246', 0.00, 1, 1, '2025-08-15 13:13:50', '2025-09-11 11:35:21'),
(16, '202508150003', 1, 'Lopeq', 'POwed', 'Kiduw', '09879798798', NULL, 'Zone 3', 'VE071', 0.00, 1, 1, '2025-08-15 13:14:00', '2025-09-11 11:35:15'),
(17, '202509110001', 1, 'Kiweo', 'Kdwu', 'Hudwy', '09987989787', NULL, 'Zone 1', 'BGF276394', 0.00, 1, 1, '2025-09-11 13:00:52', '2025-10-24 16:28:52'),
(18, '202509110002', 1, 'Huywe', 'Juidwk', 'Lopqwe', '09787977987', NULL, 'Zone 2', 'JHDUWY273', 0.00, 1, 1, '2025-09-11 13:01:13', '2025-10-24 16:28:49'),
(19, '202509110003', 1, 'Judiwk', 'Kjdwi', 'Oklwd', '09789788787', NULL, 'Zone 3', 'BHYDW7162', 0.00, 1, 1, '2025-09-11 13:07:43', '2025-09-11 13:12:54'),
(20, '202509110004', 1, 'Lfojwa', 'aiwej', 'Jdiwad', '09789778879', NULL, 'Zone 4', 'BHHYW7232', 0.00, 1, 1, '2025-09-11 13:12:36', '2025-10-24 16:28:47'),
(21, '202510240001', 1, 'Sample', 'awe', 'Samplee', '09712938712', NULL, 'Zone 1', 'UH7642154', 0.00, 1, 1, '2025-10-24 16:23:46', '2025-10-24 16:28:43'),
(22, '202510240001', 1, 'Glenard', 'U', 'Pagurayan', '09798128371', NULL, 'Zone 1', 'LV3915093', 0.00, 1, 1, '2025-10-24 16:29:39', '2025-10-24 18:40:25'),
(23, '202510240002', 1, 'Glenardod', 'Uert', 'Pagurayanewq', '09897123978', 'glenard2308@gmail.com', 'Zone 4', 'LZ5247015', 0.00, 1, 1, '2025-10-24 18:31:30', '2025-10-24 18:40:22'),
(24, '202510240001', 1, 'Glenard', 'U', 'Pagurayan', '09798798789', 'glenard2308@gmail.com', 'Zone 2', 'PO3958031', 0.00, 1, 0, '2025-10-24 18:40:48', '2025-10-24 18:40:48'),
(25, '202510240002', 1, 'Jomari', 'S', 'Estrada', '09897192371', 'jomariestrada220@gmail.com', 'Zone 1', 'ZD1021201', 0.00, 1, 0, '2025-10-24 18:59:47', '2025-10-24 18:59:47');

-- --------------------------------------------------------

--
-- Table structure for table `system_info`
--

CREATE TABLE `system_info` (
  `id` int(30) NOT NULL,
  `meta_field` text NOT NULL,
  `meta_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_info`
--

INSERT INTO `system_info` (`id`, `meta_field`, `meta_value`) VALUES
(1, 'name', 'Water Billing Management System of Barangay Catarauan, Piat, Cagayan'),
(6, 'short_name', 'WBMS - PHP'),
(11, 'logo', 'uploads/logo.png?v=1651282049'),
(13, 'user_avatar', 'uploads/user_avatar.jpg'),
(14, 'cover', 'uploads/cover.png?v=1651282061'),
(15, 'rate', '10.75');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(50) NOT NULL,
  `firstname` varchar(250) NOT NULL,
  `middlename` text DEFAULT NULL,
  `lastname` varchar(250) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `avatar` text DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 0,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `security_question` varchar(255) DEFAULT NULL,
  `security_answer` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `middlename`, `lastname`, `username`, `password`, `avatar`, `last_login`, `type`, `date_added`, `date_updated`, `security_question`, `security_answer`) VALUES
(1, 'Adminstrator', '', 'Admin', 'admin', '7488e331b8b64e5794da3fa4eb10ad5d', 'uploads/avatars/1.png?v=1649834664', NULL, 1, '2021-01-20 14:02:37', '2025-08-15 12:23:25', 'pet', 'sample'),
(5, 'New', '', 'Admin', 'newadmin', '7488e331b8b64e5794da3fa4eb10ad5d', NULL, NULL, 1, '2025-07-31 17:34:20', '2025-07-31 17:35:30', 'pet', 'dog');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `billing_list`
--
ALTER TABLE `billing_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `category_list`
--
ALTER TABLE `category_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `client_issue_list`
--
ALTER TABLE `client_issue_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `client_list`
--
ALTER TABLE `client_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `system_info`
--
ALTER TABLE `system_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `billing_list`
--
ALTER TABLE `billing_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `category_list`
--
ALTER TABLE `category_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `client_issue_list`
--
ALTER TABLE `client_issue_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `client_list`
--
ALTER TABLE `client_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `system_info`
--
ALTER TABLE `system_info`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `billing_list`
--
ALTER TABLE `billing_list`
  ADD CONSTRAINT `client_id_fk_bl` FOREIGN KEY (`client_id`) REFERENCES `client_list` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `client_issue_list`
--
ALTER TABLE `client_issue_list`
  ADD CONSTRAINT `client_issue_list_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `client_list` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `client_list`
--
ALTER TABLE `client_list`
  ADD CONSTRAINT `category_id_fk_cl` FOREIGN KEY (`category_id`) REFERENCES `category_list` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
