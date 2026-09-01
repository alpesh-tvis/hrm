-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 26, 2026 at 05:22 AM
-- Server version: 10.11.18-MariaDB-cll-lve
-- PHP Version: 8.4.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hrm`
--

-- --------------------------------------------------------

--
-- Table structure for table `workreports`
--

CREATE TABLE `workreports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `work_type` varchar(255) DEFAULT NULL,
  `activity_type` varchar(255) DEFAULT NULL,
  `project_id` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `emp_ids` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `work_date` date DEFAULT NULL,
  `work_time` time DEFAULT NULL,
  `sift` varchar(255) DEFAULT NULL,
  `timer_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `workreports`
--

INSERT INTO `workreports` (`id`, `user_id`, `work_type`, `activity_type`, `project_id`, `description`, `emp_ids`, `created_at`, `updated_at`, `work_date`, `work_time`, `sift`, `timer_id`) VALUES
(108197, '2', '2', '8', '1', 'Code analysis', NULL, '2026-08-26 15:55:51', '2026-08-26 15:55:51', '2026-08-26', NULL, NULL, NULL),
(108168, '2', '1', '1', NULL, NULL, NULL, '2026-08-26 13:49:13', '2026-08-26 15:55:51', '2026-08-26', '02:06:38', NULL, NULL),
(108156, '2', '3', '1', NULL, NULL, NULL, '2026-08-26 13:06:21', '2026-08-26 13:49:13', '2026-08-26', '00:42:52', NULL, NULL),
(108141, '2', '2', '8', '1', 'Code analysis', NULL, '2026-08-26 10:25:36', '2026-08-26 13:06:21', '2026-08-26', '02:40:45', NULL, NULL),
(108134, '2', '1', '1', NULL, NULL, NULL, '2026-08-26 09:57:33', '2026-08-26 10:25:36', '2026-08-26', '00:28:03', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `workreports`
--
ALTER TABLE `workreports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `user_id_2` (`user_id`),
  ADD KEY `work_type` (`work_type`,`created_at`,`work_date`,`work_time`,`sift`),
  ADD KEY `user_id_3` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `workreports`
--
ALTER TABLE `workreports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108222;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
