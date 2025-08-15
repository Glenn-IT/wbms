-- Table structure for table `client_issue_list`

CREATE TABLE `client_issue_list` (
  `id` int(30) NOT NULL,
  `client_id` int(30) NOT NULL,
  `issue_title` varchar(255) NOT NULL,
  `remarks` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=Pending, 1=Resolved',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for table `client_issue_list`
--

ALTER TABLE `client_issue_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`);

--
-- AUTO_INCREMENT for table `client_issue_list`
--

ALTER TABLE `client_issue_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT;

--
-- Constraints for table `client_issue_list`
--

ALTER TABLE `client_issue_list`
  ADD CONSTRAINT `client_issue_list_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `client_list` (`id`) ON DELETE CASCADE;

COMMIT;
