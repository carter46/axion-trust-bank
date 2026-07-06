-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 02, 2026 at 11:28 PM
-- Server version: 11.8.6-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u502532383_firstnation`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `account_number` varchar(20) NOT NULL,
  `account_type` enum('checking','savings','business','investment','retirement','joint','join_existing') NOT NULL,
  `account_name` varchar(255) DEFAULT NULL,
  `balance` decimal(15,2) DEFAULT 0.00,
  `available_balance` decimal(15,2) DEFAULT 0.00,
  `currency` varchar(10) DEFAULT 'USD',
  `interest_rate` decimal(5,2) DEFAULT 0.00,
  `overdraft_limit` decimal(15,2) DEFAULT 0.00,
  `daily_limit` decimal(15,2) DEFAULT 5000.00,
  `status` enum('active','frozen','closed') DEFAULT 'active',
  `opened_at` timestamp NULL DEFAULT current_timestamp(),
  `closed_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `user_id`, `account_number`, `account_type`, `account_name`, `balance`, `available_balance`, `currency`, `interest_rate`, `overdraft_limit`, `daily_limit`, `status`, `opened_at`, `closed_at`, `created_at`, `updated_at`) VALUES
(128, 134, '202642569179', 'checking', 'Primary Checking', 365.84, 365.84, 'CAD', 0.00, 0.00, 500000.00, 'active', '2026-05-31 01:22:17', NULL, '2026-05-31 01:22:17', '2026-05-31 01:43:40'),
(129, 135, '202646663507', 'savings', 'Savings Account', 20000.00, 20000.00, 'CAD', 0.00, 0.00, 500000.00, 'active', '2026-05-31 22:38:34', NULL, '2026-05-31 22:38:34', '2026-05-31 23:01:17');

-- --------------------------------------------------------

--
-- Table structure for table `account_owners`
--

CREATE TABLE `account_owners` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `status` enum('active','pending','rejected','removed') DEFAULT 'active',
  `joined_at` timestamp NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `details`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 3, 'LOGIN', 'User logged in', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 19:37:45'),
(2, 3, 'USER_DELETED', 'Deleted user: user@demo.com (ID: 2)', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 23:24:32'),
(4, 3, 'ADMIN_UPLOAD_PROFILE_PICTURE', 'Uploaded profile picture for user mr.carter.tech07@gmail.com (ID: 5)', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 23:26:43'),
(5, 3, 'TWO_FACTOR_ENABLED', 'User enabled two-factor authentication', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 23:27:13'),
(6, 3, 'TWO_FACTOR_DISABLED', 'User disabled two-factor authentication', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 23:27:16'),
(7, 3, 'LOGOUT', 'User logged out', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 23:50:37'),
(10, 3, 'LOGIN', 'User logged in', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 23:59:19'),
(11, 3, 'LOGOUT', 'User logged out', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-16 23:59:58'),
(16, 3, 'LOGIN', 'User logged in', '190.2.151.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-17 02:56:03'),
(17, 3, 'ADMIN_UPLOAD_PROFILE_PICTURE', 'Uploaded profile picture for user mr.carter.tech07@gmail.com (ID: 5)', '190.2.151.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-17 02:56:32'),
(18, 3, 'TWO_FACTOR_ENABLED', 'User enabled two-factor authentication', '190.2.151.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-17 03:08:43'),
(19, 3, 'TWO_FACTOR_DISABLED', 'User disabled two-factor authentication', '190.2.151.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-17 03:08:51'),
(20, 3, 'LOGOUT', 'User logged out', '190.2.151.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-17 03:58:09'),
(23, 3, 'LOGIN', 'User logged in', '190.2.151.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-17 04:21:36'),
(24, 3, 'LOGOUT', 'User logged out', '190.2.151.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-17 04:31:15'),
(27, 3, 'LOGIN', 'User logged in', '190.2.151.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-17 04:33:43'),
(28, 3, 'LOGIN', 'User logged in', '190.2.151.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-17 05:35:41'),
(29, 3, 'LOGOUT', 'User logged out', '190.2.151.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-17 05:53:12'),
(53, 3, 'LOGIN', 'User logged in', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 03:35:24'),
(54, 3, 'LOGOUT', 'User logged out', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 03:39:10'),
(63, 3, 'LOGIN', 'User logged in', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 04:07:45'),
(68, 3, 'LOGOUT', 'User logged out', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 04:31:35'),
(74, 3, 'LOGIN', 'User logged in', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 06:04:53'),
(75, 3, 'LOGOUT', 'User logged out', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 06:07:41'),
(78, 3, 'LOGIN', 'User logged in', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 06:08:10'),
(79, 3, 'LOGOUT', 'User logged out', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 06:08:58'),
(82, 3, 'LOGIN', 'User logged in', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 06:09:31'),
(83, 3, 'ADMIN_SET_TRANSACTION_MODE', 'Set transaction mode to \'force_pending\' for user mr.carter.tech07@gmail.com (ID: 5)', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 06:10:02'),
(84, 3, 'LOGOUT', 'User logged out', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 06:10:11'),
(88, 3, 'LOGIN', 'User logged in', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 06:12:14'),
(89, 3, 'ADMIN_USER_CREATED', 'Created new admin user: admin@online.cosmopolitantrustbankpf.com', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 06:13:05'),
(90, 3, 'LOGOUT', 'User logged out', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 06:13:13'),
(95, 3, 'LOGIN', 'User logged in', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 15:04:35'),
(96, 3, 'LOGOUT', 'User logged out', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 15:50:14'),
(99, 3, 'LOGIN', 'User logged in', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 16:12:53'),
(100, 3, 'LOGOUT', 'User logged out', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 16:19:17'),
(103, 3, 'LOGIN', 'User logged in', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 16:20:36'),
(104, 3, 'ADMIN_DELETE_TRANSACTION', 'Deleted transaction ADM20251017015233332 for user mr.carter.tech07@gmail.com. Reason: gdgg', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 16:31:40'),
(105, 3, 'LOGOUT', 'User logged out', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 16:31:47'),
(110, 3, 'LOGIN', 'User logged in', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 16:37:40'),
(111, 3, 'LOGOUT', 'User logged out', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 16:43:11'),
(114, 3, 'LOGIN', 'User logged in', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 16:52:42'),
(115, 3, 'ADMIN_DELETE_TRANSACTION', 'Deleted transaction TXN68F3AA1DBE8AA for user mr.carter.tech07@gmail.com. Reason: fsf', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 16:56:08'),
(116, 3, 'LOGOUT', 'User logged out', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 16:56:12'),
(119, 3, 'LOGIN', 'User logged in', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 17:07:05'),
(120, 3, 'LOGOUT', 'User logged out', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 17:08:09'),
(123, 3, 'LOGIN', 'User logged in', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 17:16:36'),
(124, 3, 'LOGOUT', 'User logged out', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 17:31:21'),
(127, 3, 'LOGIN', 'User logged in', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 17:32:59'),
(128, 3, 'LOGOUT', 'User logged out', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 17:33:49'),
(132, 3, 'LOGIN', 'User logged in', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 17:38:21'),
(133, 3, 'LOGOUT', 'User logged out', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 17:39:20'),
(136, 3, 'LOGIN', 'User logged in', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 17:39:53'),
(137, 3, 'LOGOUT', 'User logged out', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 17:40:33'),
(139, 3, 'LOGIN', 'User logged in', '160.152.115.229', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-10-18 18:19:08'),
(140, 3, 'LOGOUT', 'User logged out', '160.152.115.229', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-10-18 18:24:40'),
(141, 3, 'LOGIN', 'User logged in', '160.152.115.229', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-10-18 18:25:24'),
(145, 3, 'LOGIN', 'User logged in', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 19:00:38'),
(146, 3, 'LOGOUT', 'User logged out', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 19:01:18'),
(150, 3, 'LOGIN', 'User logged in', '105.112.104.157', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-10-18 19:17:23'),
(151, 3, 'LOGOUT', 'User logged out', '105.112.104.157', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-10-18 19:18:08'),
(156, 3, 'LOGIN', 'User logged in', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 21:59:10'),
(157, 3, 'LOGOUT', 'User logged out', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 22:00:15'),
(160, 3, 'LOGIN', 'User logged in', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 22:00:46'),
(162, 3, 'LOGOUT', 'User logged out', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-18 22:19:53'),
(168, 3, 'LOGIN', 'User logged in', '77.81.142.101', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-10-19 07:58:48'),
(171, 3, 'LOGIN', 'User logged in', '105.112.107.149', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-10-20 01:39:10'),
(173, 3, 'LOGOUT', 'User logged out', '105.112.107.149', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-10-20 01:57:56'),
(174, 3, 'LOGIN', 'User logged in', '105.112.107.149', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-10-20 02:01:57'),
(175, 3, 'LOGOUT', 'User logged out', '105.112.107.149', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-10-20 02:02:47'),
(177, 3, 'LOGIN', 'User logged in', '105.112.107.149', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-10-20 03:10:11'),
(178, 3, 'LOGIN', 'User logged in', '105.112.107.149', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-10-20 03:12:55'),
(179, 3, 'LOGOUT', 'User logged out', '105.112.107.149', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-10-20 03:24:33'),
(180, 3, 'LOGIN', 'User logged in', '105.112.107.149', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-10-20 03:25:13'),
(181, 3, 'LOGOUT', 'User logged out', '105.112.107.149', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-10-20 03:37:38'),
(182, 3, 'LOGIN', 'User logged in', '105.112.107.149', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-10-20 03:41:18'),
(183, 3, 'LOGOUT', 'User logged out', '105.112.107.149', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-10-20 04:02:36'),
(184, 3, 'LOGIN', 'User logged in', '105.112.107.149', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-10-20 04:05:09'),
(185, 3, 'LOGIN', 'User logged in', '105.112.107.149', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-10-20 05:03:08'),
(197, 3, 'LOGIN', 'User logged in', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-20 22:07:29'),
(198, 3, 'LOGOUT', 'User logged out', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-20 22:09:52'),
(205, 3, 'LOGIN', 'User logged in', '197.211.59.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-20 23:15:30'),
(206, 3, 'LOGOUT', 'User logged out', '197.211.59.104', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-21 10:01:59'),
(210, 3, 'LOGIN', 'User logged in', '197.211.59.104', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-21 10:03:07'),
(212, 3, 'LOGOUT', 'User logged out', '197.211.59.104', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-21 10:20:56'),
(221, 3, 'LOGIN', 'User logged in', '197.211.59.104', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-21 12:09:03'),
(223, 3, 'LOGOUT', 'User logged out', '197.211.59.104', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-21 12:09:17'),
(235, 3, 'LOGIN', 'User logged in', '197.211.59.104', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-21 13:13:09'),
(239, 3, 'LOGIN', 'User logged in', '105.112.209.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-10-22 03:04:41'),
(240, 3, 'LOGOUT', 'User logged out', '105.112.209.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-10-22 03:16:29'),
(241, 3, 'LOGIN', 'User logged in', '105.112.209.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-10-22 03:16:48'),
(242, 3, 'LOGOUT', 'User logged out', '105.112.209.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-10-22 03:18:51'),
(245, 3, 'LOGIN', 'User logged in', '105.112.209.143', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0', '2025-10-22 03:20:42'),
(256, 3, 'LOGIN', 'User logged in', '197.211.59.112', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-27 11:12:34'),
(257, 3, 'LOGIN', 'User logged in', '197.211.59.112', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-27 12:21:27'),
(260, 3, 'LOAN_APPROVED', 'Approved loan application #4 for amount $10,000.00', '197.211.59.112', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-27 12:30:19'),
(263, 3, 'LOGIN', 'User logged in', '197.211.59.112', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-27 13:13:32'),
(266, 3, 'LOGIN', 'User logged in', '102.88.108.166', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-29 01:30:42'),
(272, 3, 'LOGIN', 'User logged in', '197.211.59.112', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-29 12:36:19'),
(273, 3, 'USER_DELETED', 'Deleted user: bst671930@gmail.com (ID: 7)', '197.211.59.112', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-29 12:36:27'),
(274, 3, 'USER_DELETED', 'Deleted user: mr.carter.tech07@gmail.com (ID: 5)', '197.211.59.112', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-29 12:36:31'),
(276, 3, 'LOGOUT', 'User logged out', '197.211.59.112', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-29 12:41:16'),
(281, 3, 'LOGIN', 'User logged in', '102.88.108.122', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-29 13:05:34'),
(282, 3, 'LOGOUT', 'User logged out', '102.88.108.122', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-29 13:28:36'),
(301, 3, 'LOGIN', 'User logged in', '102.89.82.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-02 18:25:35'),
(302, 3, 'LOGOUT', 'User logged out', '102.89.82.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-02 18:26:54'),
(304, 3, 'LOGIN', 'User logged in', '197.211.59.196', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-02 21:22:43'),
(305, 3, 'LOGOUT', 'User logged out', '197.211.59.196', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-02 21:22:56'),
(319, 3, 'ADMIN_TOGGLE_2FA', 'Admin admin@demo.com enabled two-factor authentication for user mr.carter.tech07@gmail.com', '197.211.59.196', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-03 03:26:05'),
(320, 3, 'ADMIN_TOGGLE_2FA', 'Admin admin@demo.com enabled two-factor authentication for user mr.carter.tech07@gmail.com', '197.211.59.196', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-03 03:26:11'),
(321, 3, 'ADMIN_TOGGLE_2FA', 'Admin admin@demo.com enabled two-factor authentication for user mr.carter.tech07@gmail.com', '197.211.59.196', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-03 03:27:14'),
(322, 3, 'ADMIN_TOGGLE_2FA', 'Admin admin@demo.com enabled two-factor authentication for user mr.carter.tech07@gmail.com', '197.211.59.196', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', '2025-11-03 03:27:40'),
(323, 3, 'ADMIN_TOGGLE_2FA', 'Admin admin@demo.com disabled two-factor authentication for user mr.carter.tech07@gmail.com', '197.211.59.196', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-03 03:28:10'),
(324, 3, 'ADMIN_TOGGLE_2FA', 'Admin admin@demo.com enabled two-factor authentication for user mr.carter.tech07@gmail.com', '197.211.59.196', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-03 03:30:16'),
(328, 3, 'ADMIN_SET_TRANSACTION_MODE', 'Set transaction mode to \'force_pending\' for user mr.carter.tech07@gmail.com (ID: 9)', '197.211.59.196', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-03 03:39:44'),
(337, 3, 'LOGIN', 'User logged in', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-03 14:19:52'),
(341, 3, 'KYC_APPROVED', 'Approved KYC ID: 1', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-03 15:29:47'),
(343, 3, 'LOGIN', 'User logged in', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-03 16:48:26'),
(345, 3, 'CRYPTO_FUNDING_APPROVED', 'Approved crypto funding #9', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-03 18:16:39'),
(349, 3, 'LOGIN', 'User logged in', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-03 21:27:57'),
(354, 3, 'LOGIN', 'User logged in', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 00:06:40'),
(356, 3, 'ADMIN_SET_TRANSACTION_MODE', 'Set transaction mode to \'force_failed\' for user mr.carter.tech07@gmail.com (ID: 9)', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 00:14:46'),
(359, 3, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction TXN69094964DD876 for user mr.carter.tech07@gmail.com. Amount changed from 100.00 to 100. Date changed to 2025-10-15 08:31:00', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 00:47:44'),
(360, 3, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction TXN69094964DD876 for user mr.carter.tech07@gmail.com. Amount changed from 100.00 to 100. Date changed to 2025-09-11 08:31:00', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 00:48:13'),
(361, 3, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction TXN69094964DD876 for user mr.carter.tech07@gmail.com. Amount changed from 100.00 to 100. Date changed to 2025-06-11 08:31:00', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 00:49:26'),
(362, 3, 'LOGIN', 'User logged in', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 03:55:16'),
(364, 3, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction TXN69094964DD876 for user mr.carter.tech07@gmail.com. Amount changed from 100.00 to 100. Date changed from 2025-11-04 00:31:32 to 2025-06-19 08:31:00', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 03:59:06'),
(365, 3, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction TXN6909491ABD768 for user mr.carter.tech07@gmail.com. Amount changed from 78.00 to 78. Date changed from 2025-11-04 00:30:18 to 2025-06-19 08:30:00', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 04:04:58'),
(366, 3, 'LOGOUT', 'User logged out', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 04:21:38'),
(367, 3, 'LOGIN', 'User logged in', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 04:21:56'),
(368, 3, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction TXN6909454C6F10C for user mr.carter.tech07@gmail.com. Amount changed from 100.50 to 100.5. Date changed from 2025-11-04 00:14:04 to 2025-06-15 08:14:00', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 04:22:17'),
(369, 3, 'LOGOUT', 'User logged out', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 04:23:25'),
(370, 3, 'LOGIN', 'User logged in', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 04:23:34'),
(371, 3, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction TXN2025110379D758B2 for user mr.carter.tech07@gmail.com. Amount changed from 500.00 to 500. Date changed from 2025-11-03 20:59:09 to 2022-02-15 04:59:00', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 04:24:07'),
(372, 3, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction TXN69083185CED11 for user mr.carter.tech07@gmail.com. Amount changed from 44.22 to 44.22. Date changed from 2025-11-03 04:37:25 to 2024-06-21 12:37:00', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 04:25:07'),
(373, 3, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction TXN69082BD32D2A7 for user mr.carter.tech07@gmail.com. Amount changed from 34.17 to 34.17. Date changed from 2025-11-03 04:13:07 to 2025-03-20 12:13:00', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 04:36:35'),
(374, 3, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction TXN69082861EEB9F for user mr.carter.tech07@gmail.com. Amount changed from 603 to 603. Date changed from 2025-11-03 03:58:25 to 2025-02-06 11:58:00', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 04:54:04'),
(375, 3, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction TXN690824346584F for user mr.carter.tech07@gmail.com. Amount changed from 123.62 to 123.62. Date changed from 2025-11-03 03:40:36 to 2022-03-31 13:40:00', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 04:55:00'),
(376, 3, 'USER_DELETED', 'Deleted user: mr.carter.tech07@gmail.com (ID: 9)', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 04:56:32'),
(377, 3, 'USER_DELETED', 'Deleted user: mr.carter.tech07@gmail.com (ID: 9)', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 04:56:51'),
(378, 3, 'USER_DELETED', 'Deleted user: mr.carter.tech07@gmail.com (ID: 9)', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 04:57:14'),
(379, 3, 'USER_DELETED', 'Deleted user: mr.carter.tech07@gmail.com (ID: 9)', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 04:57:29'),
(380, 3, 'USER_DELETED', 'Deleted user: mr.carter.tech07@gmail.com (ID: 9)', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 04:58:21'),
(381, 3, 'LOGIN', 'User logged in', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 05:47:24'),
(382, 3, 'USER_DELETED', 'Deleted user: mr.carter.tech07@gmail.com (ID: 9)', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 06:21:30'),
(383, 3, 'LOGIN', 'User logged in', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 07:03:43'),
(384, 3, 'LOGIN', 'User logged in', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 09:12:43'),
(395, 3, 'LOGIN', 'User logged in', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 11:29:20'),
(396, 3, 'LOGOUT', 'User logged out', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 11:29:35'),
(397, 3, 'LOGIN', 'User logged in', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 11:29:48'),
(398, 3, 'LOGOUT', 'User logged out', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 11:42:33'),
(399, 3, 'LOGIN', 'User logged in', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 11:42:44'),
(400, 3, 'USER_DELETED', 'Deleted user: billyfredrickgibbons@gmail.com (ID: 10)', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 11:42:56'),
(408, 3, 'LOGIN', 'User logged in', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 12:29:15'),
(409, 3, 'USER_DELETED', 'Deleted user: billyfredrickgibbons@gmail.com (ID: 11)', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 12:29:33'),
(419, 3, 'LOGIN', 'User logged in', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 13:18:23'),
(420, 3, 'KYC_APPROVED', 'Approved KYC ID: 3', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 13:18:47'),
(421, 3, 'LOGIN', 'User logged in', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 17:17:06'),
(425, 3, 'LOGIN', 'User logged in', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 20:01:29'),
(434, 3, 'LOAN_APPROVED', 'Approved loan application #6 for amount $10,000.00', '149.22.82.30', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 20:13:38'),
(438, 3, 'LOGIN', 'User logged in', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 23:57:10'),
(439, 3, 'USER_DELETED', 'Deleted user: billyfredrickgibbons@gmail.com (ID: 12)', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-04 23:57:23'),
(441, 3, 'LOGIN', 'User logged in', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-05 00:52:22'),
(454, 3, 'USER_DELETED', 'Deleted user: billyfredrickgibbons@gmail.com (ID: 13)', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-05 01:35:54'),
(463, 3, 'KYC_APPROVED', 'Approved KYC ID: 4', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-05 01:55:55'),
(470, 3, 'LOAN_APPROVED', 'Approved loan application #7 for amount $50,000.00', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-05 02:00:53'),
(474, 3, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction TXN690AB0B1D29ED for user billyfredrickgibbons@gmail.com. Amount changed from 5025 to 5025. Date changed from 2025-11-05 02:04:33 to 2025-10-09 10:04:00', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-05 02:28:15'),
(476, 3, 'ADMIN_SET_TRANSACTION_MODE', 'Set transaction mode to \'force_pending\' for user billyfredrickgibbons@gmail.com (ID: 14)', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-05 02:30:38'),
(479, 3, 'LOGIN', 'User logged in', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-05 03:05:53'),
(480, 3, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction TXN690AB70D3243C for user billyfredrickgibbons@gmail.com. Amount changed from 512.5 to 512.5. Status changed from pending to failed. Date changed from 2025-11-05 02:31:41 to 2025-11-05 10:31:00', '197.211.59.116', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-05 03:06:33'),
(486, 3, 'LOGIN', 'User logged in', '197.211.59.118', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-05 22:00:45'),
(487, 3, 'USER_DELETED', 'Deleted user: billyfredrickgibbons@gmail.com (ID: 14)', '197.211.59.118', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-05 22:01:00'),
(489, 3, 'USER_DELETED', 'Deleted user: billyfredrickgibbons@gmail.com (ID: 16)', '197.211.59.118', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-05 22:02:32'),
(491, 3, 'USER_DELETED', 'Deleted user: billyfredrickgibbons@gmail.com (ID: 17)', '197.211.59.118', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-05 22:08:57'),
(492, 3, 'USER_DELETED', 'Deleted user: kingsleynicholas981@gmail.com (ID: 15)', '197.211.59.118', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-05 22:27:38'),
(493, 3, 'LOGOUT', 'User logged out', '197.211.59.118', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-05 22:27:54'),
(494, 3, 'LOGIN', 'User logged in', '197.211.59.118', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-05 22:28:00'),
(495, 3, 'LOGOUT', 'User logged out', '197.211.59.118', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-05 22:30:38'),
(506, 3, 'LOGIN', 'User logged in', '197.211.59.118', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-05 22:46:44'),
(507, 3, 'KYC_APPROVED', 'Approved KYC ID: 5', '197.211.59.118', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-05 22:48:37'),
(513, 3, 'LOAN_APPROVED', 'Approved loan application #8 for amount $10,000.00', '197.211.59.118', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-05 22:50:19'),
(514, 3, 'CRYPTO_FUNDING_APPROVED', 'Approved crypto funding #12', '197.211.59.118', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-05 22:51:35'),
(515, 3, 'LOGIN', 'User logged in', '197.211.59.118', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-05 23:42:18'),
(518, 3, 'LOGIN', 'User logged in', '102.89.23.13', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 00:46:40'),
(520, 3, 'LOGIN', 'User logged in', '102.89.23.13', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 01:04:56'),
(521, 3, 'LOGOUT', 'User logged out', '102.89.23.13', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 01:26:11'),
(522, 3, 'LOGIN', 'User logged in', '102.89.23.13', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 01:51:51'),
(523, 3, 'USER_DELETED', 'Deleted user: billyfredrickgibbons@gmail.com (ID: 18)', '102.89.23.13', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 01:52:03'),
(524, 3, 'LOGIN', 'User logged in', '129.205.124.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 15:16:36'),
(525, 3, 'LOGIN', 'User logged in', '129.205.124.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 16:11:27'),
(526, 3, 'LOGOUT', 'User logged out', '129.205.124.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 16:15:13'),
(527, 3, 'LOGIN', 'User logged in', '129.205.124.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 16:19:12'),
(528, 3, 'rates_refreshed', 'Refreshed exchange rates', '129.205.124.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 16:47:26'),
(529, 3, 'LOGIN', 'User logged in', '102.88.111.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 18:26:56'),
(530, 3, 'LOGOUT', 'User logged out', '102.88.111.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 18:33:43'),
(531, 3, 'LOGIN', 'User logged in', '102.88.111.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 18:33:52'),
(532, 3, 'USER_DELETED', 'Deleted user: mr.carter.tech07@gmail.com (ID: 20)', '102.88.111.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 18:34:06'),
(533, 3, 'LOGOUT', 'User logged out', '102.88.111.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 18:34:12'),
(544, 3, 'LOGIN', 'User logged in', '102.88.111.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 18:46:59'),
(545, 3, 'ADMIN_SET_TRANSACTION_MODE', 'Set transaction mode to \'force_pending\' for user billyfredrickgibbons@gmail.com (ID: 21)', '102.88.111.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 18:48:36'),
(546, 3, 'ADMIN_SET_TRANSACTION_MODE', 'Set transaction mode to \'force_failed\' for user billyfredrickgibbons@gmail.com (ID: 21)', '102.88.111.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 18:48:40'),
(547, 3, 'ADMIN_SET_TRANSACTION_MODE', 'Set transaction mode to \'force_success\' for user billyfredrickgibbons@gmail.com (ID: 21)', '102.88.111.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 18:48:44'),
(548, 3, 'KYC_APPROVED', 'Approved KYC ID: 6', '102.88.111.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 18:51:16'),
(557, 3, 'LOAN_APPROVED', 'Approved loan application #9 for amount $10,000.00', '102.88.111.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 18:53:17'),
(558, 3, 'CRYPTO_FUNDING_APPROVED', 'Approved crypto funding #13', '102.88.111.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-06 18:54:15'),
(561, 3, 'LOGIN', 'User logged in', '129.205.124.214', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-09 18:59:37'),
(566, 3, 'LOGIN', 'User logged in', '129.205.124.214', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-09 21:02:19'),
(571, 3, 'LOGIN', 'User logged in', '197.211.59.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-21 08:24:36'),
(576, 3, 'KYC_APPROVED', 'Approved KYC ID: 7', '197.211.59.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-21 08:53:34'),
(579, 3, 'LOGIN', 'User logged in', '2c0f:2a80:a87:5908:cfc:7e0e:5f04:a8b6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-21 11:50:46'),
(582, 3, 'ADMIN_SET_TRANSACTION_MODE', 'Set transaction mode to \'force_pending\' for user donjnjglobal@gmail.com (ID: 22)', '2c0f:2a80:a87:5908:a02f:5f04:ce82:20ae', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-21 12:21:22'),
(583, 3, 'LOGIN', 'User logged in', '2c0f:2a80:a87:5908:a02f:5f04:ce82:20ae', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-21 12:47:45'),
(584, 3, 'EMAIL_TEST', 'Sent test email (transaction_credit) to mr.carter.tech07@gmail.com', '197.211.59.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-21 13:14:51'),
(586, 3, 'LOGIN', 'User logged in', '197.211.59.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-21 14:14:50'),
(591, 3, 'LOGIN', 'User logged in', '197.211.59.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-21 21:49:59'),
(592, 3, 'LOGIN', 'User logged in', '197.211.59.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-21 23:27:08'),
(593, 3, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction TXN6910EAFD82310 for user billyfredrickgibbons@gmail.com. Amount changed from 8040 to 8040. Status changed from completed to failed. Date changed from 2025-11-09 19:26:53 to 2025-11-10 03:26:00', '197.211.59.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-22 00:52:05'),
(594, 3, 'LOGIN', 'User logged in', '197.211.59.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-22 01:38:17'),
(595, 3, 'LOGOUT', 'User logged out', '197.211.59.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-22 01:50:49'),
(596, 3, 'LOGIN', 'User logged in', '197.211.59.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-22 01:51:00'),
(597, 3, 'ADMIN_USER_PASSWORD_RESET', 'Admin reset password for user: billyfredrickgibbons@gmail.com (ID: 21)', '197.211.59.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-22 01:51:18'),
(598, 3, 'LOGOUT', 'User logged out', '197.211.59.121', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-22 01:51:25'),
(617, 3, 'LOGIN', 'User logged in', '197.211.53.87', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-26 11:30:30'),
(618, 3, 'EMAIL_SIMULATION', 'Added email template: PayPal', '197.211.53.87', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-26 11:35:27'),
(619, 3, 'EMAIL_SIMULATION', 'Updated email template ID 2: PayPal', '197.211.53.87', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-26 11:37:56'),
(620, 3, 'EMAIL_SIMULATION', 'Sent simulation email to mr.carter.tech07@gmail.com using template: PayPal', '197.211.53.87', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-26 11:40:54'),
(621, 3, 'LOGIN', 'User logged in', '197.211.59.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-26 19:21:17'),
(622, 3, 'EMAIL_SIMULATION', 'Updated email template ID 2: PayPal', '197.211.59.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-26 19:21:58'),
(623, 3, 'EMAIL_SIMULATION', 'Updated email template ID 2: PayPal', '105.113.90.232', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-26 19:38:20'),
(624, 3, 'EMAIL_SIMULATION', 'Sent simulation email to billyfredrickgibbons@gmail.com using template: PayPal', '197.211.59.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-26 20:05:15'),
(625, 3, 'EMAIL_SIMULATION', 'Updated email template ID 2: PayPal', '197.211.59.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-26 20:16:39'),
(626, 3, 'EMAIL_SIMULATION', 'Updated email template ID 2: PayPal', '197.211.59.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-26 20:17:15'),
(627, 3, 'EMAIL_SIMULATION', 'Added email template: Venmo', '197.211.59.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-26 20:19:01');
INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `details`, `ip_address`, `user_agent`, `created_at`) VALUES
(628, 3, 'EMAIL_SIMULATION', 'Added email template: cash app', '197.211.59.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-26 20:20:38'),
(629, 3, 'LOGIN', 'User logged in', '197.211.59.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-26 20:55:16'),
(630, 3, 'USER_DELETED', 'Deleted user: mr.carter.tech07@gmail.com (ID: 23)', '197.211.59.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-26 20:55:28'),
(631, 3, 'USER_DELETED', 'Deleted user: donjnjglobal@gmail.com (ID: 22)', '197.211.59.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-26 20:55:32'),
(632, 3, 'USER_DELETED', 'Deleted user: billyfredrickgibbons@gmail.com (ID: 21)', '197.211.59.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-26 20:55:35'),
(633, 3, 'ADMIN_DELETED', 'Deleted administrator: manager99 (admin@online.cosmopolitantrustbankpf.com)', '197.211.59.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-26 20:56:38'),
(634, 3, 'ADMIN_USER_CREATED', 'Created new admin user: support@zentropay-global.pro', '197.211.59.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-26 20:57:08'),
(635, 3, 'LOGIN', 'User logged in', '197.211.59.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-26 21:21:40'),
(636, 3, 'EMAIL_SIMULATION', 'Added email template: zelle', '197.211.59.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-26 21:24:27'),
(637, 3, 'EMAIL_SIMULATION', 'Added email template: Wells Fargo', '197.211.59.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-26 21:26:28'),
(638, 3, 'EMAIL_SIMULATION', 'Added email template: BAO', '197.211.59.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-26 21:33:21'),
(639, 3, 'EMAIL_SIMULATION', 'Sent simulation email to billyfredrickgibbons@gmail.com using template: Wells Fargo', '197.211.59.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-26 21:36:13'),
(640, 3, 'EMAIL_SIMULATION', 'Updated email template ID 4: cash app', '197.211.59.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-26 21:56:04'),
(641, 3, 'EMAIL_SIMULATION', 'Updated email template ID 3: Venmo', '197.211.59.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-26 21:56:30'),
(642, 3, 'EMAIL_SIMULATION', 'Updated email template ID 2: PayPal', '197.211.59.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-26 21:56:50'),
(643, 3, 'EMAIL_SIMULATION', 'Updated email template ID 3: Venmo', '197.211.59.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-26 22:12:11'),
(644, 3, 'EMAIL_SIMULATION', 'Sent simulation email to billyfredrickgibbons@gmail.com using template: BAO', '197.211.59.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-26 22:34:10'),
(645, 3, 'EMAIL_SIMULATION', 'Sent simulation email to billyfredrickgibbons@gmail.com using template: BAO', '197.211.59.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-26 22:52:57'),
(646, 3, 'LOGIN', 'User logged in', '197.211.59.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-27 00:01:16'),
(647, 3, 'EMAIL_SIMULATION', 'Sent simulation email to billyfredrickgibbons@gmail.com using template: BAO', '197.211.59.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-27 00:26:27'),
(648, 3, 'EMAIL_SIMULATION', 'Sent simulation email to billyfredrickgibbons@gmail.com using template: BAO', '197.211.59.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-27 00:29:58'),
(649, 3, 'EMAIL_SIMULATION', 'Sent simulation email to billyfredrickgibbons@gmail.com using template: cash app', '197.211.59.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-27 00:31:52'),
(650, 3, 'LOGIN', 'User logged in', '197.211.59.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-27 12:10:01'),
(653, 3, 'LOGIN', 'User logged in', '129.205.124.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-05 22:17:44'),
(654, 3, 'LOGIN', 'User logged in', '129.205.124.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-05 23:27:17'),
(655, 3, 'LOGOUT', 'User logged out', '129.205.124.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-05 23:28:06'),
(656, 3, 'LOGIN', 'User logged in', '129.205.124.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-05 23:28:24'),
(657, 3, 'KYC_AUTO_VERIFIED', 'Auto-verified KYC for user 27 during account creation', '129.205.124.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-05 23:30:58'),
(659, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user hkr.fred@outlook.com (ID: 27)', '129.205.124.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-05 23:31:19'),
(663, 3, 'LOGIN', 'User logged in', '129.205.124.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-05 23:35:00'),
(664, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user hkr.fred@outlook.com (ID: 27)', '129.205.124.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-05 23:50:48'),
(666, 3, 'LOGIN', 'User logged in', '129.205.124.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-05 23:51:53'),
(667, 3, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction ADM20251205190029145 for user hkr.fred@outlook.com. Amount changed from 27500 to 27500. Status changed from completed to failed. Date changed from 2024-01-19 12:01:00 to 2024-01-19 20:01:00', '129.205.124.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-06 00:01:02'),
(668, 3, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction ADM20251205190029145 for user hkr.fred@outlook.com. Amount changed from 27500 to 27500. Date changed from 2024-01-19 20:01:00 to 2024-01-20 03:01:00', '129.205.124.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-06 00:01:27'),
(669, 3, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction ADM20251205190029145 for user hkr.fred@outlook.com. Amount changed from 27500 to 27500. Date changed from 2024-01-20 03:01:00 to 2024-01-20 23:58:00', '129.205.124.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-06 00:01:50'),
(670, 3, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction ADM20251205190029145 for user hkr.fred@outlook.com. Amount changed from 27500 to 27500. Date changed from 2024-01-20 23:58:00 to 2024-01-19 12:00:00', '129.205.124.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-06 00:02:22'),
(671, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user hkr.fred@outlook.com (ID: 27)', '129.205.124.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-06 00:12:49'),
(678, 3, 'LOGIN', 'User logged in', '129.205.124.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-06 22:27:14'),
(679, 3, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction ADM20251205190936451 for user hkr.fred@outlook.com. Amount changed from 3120 to 3120. Date changed from 2024-09-08 21:16:00 to 2024-12-17 04:16:00', '129.205.124.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-06 22:32:15'),
(680, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user hkr.fred@outlook.com (ID: 27)', '129.205.124.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-06 22:33:42'),
(703, 3, 'LOGIN', 'User logged in', '129.205.124.245', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-07 21:12:03'),
(704, 3, 'USER_DELETED', 'Deleted user: odufubesumalua@gmail.com (ID: 33)', '129.205.124.245', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-07 21:12:18'),
(705, 3, 'USER_DELETED', 'Deleted user: official8153419@gmail.com (ID: 32)', '129.205.124.245', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-07 21:12:22'),
(706, 3, 'USER_DELETED', 'Deleted user: florian9153@protonmail.com (ID: 31)', '129.205.124.245', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-07 21:12:32'),
(707, 3, 'USER_DELETED', 'Deleted user: momaojason004@gmail.com (ID: 30)', '129.205.124.245', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-07 21:12:40'),
(708, 3, 'USER_DELETED', 'Deleted user: tommmichael466@gmail.com (ID: 29)', '129.205.124.245', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-07 21:12:45'),
(709, 3, 'USER_DELETED', 'Deleted user: karinaanna2did@gmail.com (ID: 28)', '129.205.124.245', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-07 21:12:52'),
(710, 3, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction ADM20251205191222495 for user hkr.fred@outlook.com. Amount changed from 2350000 to 2350000. Date changed from 2025-10-06 16:11:00 to 2024-12-16 23:11:00', '129.205.124.245', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-07 21:13:31'),
(711, 3, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction ADM20251205191222495 for user hkr.fred@outlook.com. Amount changed from 2350000 to 2350000. Date changed from 2024-12-16 23:11:00 to 2024-12-05 07:11:00', '129.205.124.245', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-07 21:14:11'),
(712, 3, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction ADM20251205190701787 for user hkr.fred@outlook.com. Amount changed from 62900 to 62900. Date changed from 2024-07-15 07:06:00 to 2024-12-16 14:06:00', '129.205.124.245', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-07 21:14:37'),
(713, 3, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction TXN6933CB00306C2 for user hkr.fred@outlook.com. Amount changed from 8492.25 to 8492.25. Date changed from 2025-12-06 06:19:44 to 2025-11-26 14:19:00', '129.205.124.245', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-07 21:16:05'),
(714, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user hkr.fred@outlook.com (ID: 27)', '129.205.124.245', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-07 21:18:37'),
(718, 3, 'LOGIN', 'User logged in', '98.97.77.76', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-08 13:10:05'),
(719, 3, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction ADM20251205190701787 for user hkr.fred@outlook.com. Amount changed from 62900 to 62900. Date changed from 2024-12-16 14:06:00 to 2025-11-07 22:06:00', '98.97.77.76', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-08 13:11:34'),
(720, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user hkr.fred@outlook.com (ID: 27)', '98.97.77.76', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-08 13:11:45'),
(756, 3, 'LOGIN', 'User logged in', '185.184.192.250', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-15 15:23:33'),
(757, 3, 'KYC_AUTO_VERIFIED', 'Auto-verified KYC for user 37 during account creation', '185.184.192.250', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-15 15:24:50'),
(759, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user billyfredrickgibbons@gmail.com (ID: 37)', '185.184.192.250', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-15 15:25:11'),
(773, 3, 'LOGIN', 'User logged in', '129.205.124.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-16 00:02:02'),
(774, 3, 'USER_DELETED', 'Deleted user: paulegwolome@gmail.com (ID: 38)', '129.205.124.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-16 00:02:13'),
(775, 3, 'USER_DELETED', 'Deleted user: aspinalladam5@gmail.com (ID: 36)', '129.205.124.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-16 00:02:18'),
(776, 3, 'USER_DELETED', 'Deleted user: nathanwhite8155@gmail.com (ID: 35)', '129.205.124.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-16 00:02:22'),
(777, 3, 'USER_DELETED', 'Deleted user: sprt.theme@gmail.com (ID: 34)', '129.205.124.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-16 00:02:30'),
(778, 3, 'USER_DELETED', 'Deleted user: babanice353@gmail.com (ID: 25)', '129.205.124.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-16 00:02:38'),
(779, 3, 'USER_DELETED', 'Deleted user: eurpeter@gmail.com (ID: 26)', '129.205.124.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-16 00:02:43'),
(815, 3, 'LOGIN', 'User logged in', '129.205.124.216', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-06 17:47:14'),
(816, 3, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction ADM20260106130030980 for user jadejordan6040@gmail.com. Amount changed from 4000000 to 4000000. Date changed from 2021-10-29 09:59:00 to 2020-07-30 16:59:00', '129.205.124.216', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-06 18:01:00'),
(817, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user jadejordan6040@gmail.com (ID: 46)', '129.205.124.216', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-06 18:10:57'),
(819, 3, 'LOGIN', 'User logged in', '129.205.124.216', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-06 18:14:23'),
(820, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user jadejordan6040@gmail.com (ID: 46)', '129.205.124.216', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-06 18:18:33'),
(824, 3, 'LOGIN', 'User logged in', '129.205.124.216', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 01:26:54'),
(825, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user jadejordan6040@gmail.com (ID: 46)', '129.205.124.216', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 01:32:34'),
(826, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '129.205.124.216', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 01:33:01'),
(827, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user jadejordan6040@gmail.com (ID: 46)', '129.205.124.216', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 01:34:41'),
(828, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '129.205.124.216', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 01:44:27'),
(829, 3, 'USER_DELETED', 'Deleted user: santosdc1424@gmail.com (ID: 44)', '129.205.124.216', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 01:48:13'),
(830, 3, 'USER_DELETED', 'Deleted user: inspector1424@gmail.com (ID: 43)', '129.205.124.216', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 01:48:18'),
(831, 3, 'USER_DELETED', 'Deleted user: jerrhsv8788@gmail.com (ID: 42)', '129.205.124.216', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 01:48:26'),
(832, 3, 'USER_DELETED', 'Deleted user: litekkt@gmail.com (ID: 45)', '129.205.124.216', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 01:48:37'),
(833, 3, 'USER_DELETED', 'Deleted user: okekechukwudubem155@gmail.com (ID: 41)', '129.205.124.216', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 01:48:42'),
(834, 3, 'USER_DELETED', 'Deleted user: stevolalalala@gmail.com (ID: 40)', '129.205.124.216', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 01:48:46'),
(835, 3, 'USER_DELETED', 'Deleted user: wuiyeyeg@gmail.com (ID: 39)', '129.205.124.216', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 01:48:55'),
(836, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user jadejordan6040@gmail.com (ID: 46)', '129.205.124.216', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 01:53:41'),
(846, 3, 'LOGIN', 'User logged in', '169.150.218.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 08:31:48'),
(847, 3, 'USER_DELETED', 'Deleted user: esewidice@gmail.com (ID: 47)', '169.150.218.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 08:32:10'),
(848, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user jadejordan6040@gmail.com (ID: 46)', '169.150.218.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 08:33:57'),
(849, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '169.150.218.56', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-07 08:34:41'),
(897, 3, 'LOGIN', 'User logged in', '129.205.124.216', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-11 20:01:38'),
(898, 3, 'USER_DELETED', 'Deleted user: emperormethuselah@gmail.com (ID: 52)', '129.205.124.216', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-11 20:01:59'),
(899, 3, 'USER_DELETED', 'Deleted user: jg6871808@gmail.com (ID: 51)', '129.205.124.216', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-11 20:02:02'),
(900, 3, 'USER_DELETED', 'Deleted user: angeloleelouisbusk@gmail.com (ID: 50)', '129.205.124.216', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-11 20:02:06'),
(901, 3, 'USER_DELETED', 'Deleted user: stephenmark8118@gmail.com (ID: 49)', '129.205.124.216', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-11 20:02:10'),
(902, 3, 'USER_DELETED', 'Deleted user: abdurrahmuhammad@gmail.com (ID: 48)', '129.205.124.216', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-11 20:02:14'),
(903, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user hkr.fred@outlook.com (ID: 27)', '129.205.124.216', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-11 20:02:21'),
(905, 3, 'LOGIN', 'User logged in', '129.205.124.216', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-11 20:04:24'),
(906, 3, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction TXN6963971BF1568 for user hkr.fred@outlook.com. Amount changed from 4613.96 to 4600. Status changed from completed to failed. Date changed from 2026-01-11 12:27:07 to 2026-01-12 20:27:00', '129.205.124.216', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-11 20:14:56'),
(908, 3, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction ADM20251207161826394 for user hkr.fred@outlook.com. Amount changed from 35700 to 35700. Status changed from failed to completed. Date changed from 2025-12-02 06:51:00 to 2025-12-02 14:51:00', '105.113.90.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-11 20:38:54'),
(955, 3, 'LOGIN', 'User logged in', '102.89.75.41', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-25 21:34:14'),
(956, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user billyfredrickgibbons@gmail.com (ID: 37)', '102.89.75.41', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-25 21:37:40'),
(958, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.89.75.41', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-25 21:38:30'),
(965, 3, 'LOAN_APPROVED', 'Approved loan application #15 for amount $10,000.00', '102.89.75.41', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-25 21:39:21'),
(974, 3, 'LOGIN', 'User logged in', '102.89.69.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 23:07:14'),
(975, 3, 'USER_DELETED', 'Deleted user: graciromao27@gmail.com (ID: 59)', '102.89.69.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 23:08:46'),
(976, 3, 'USER_DELETED', 'Deleted user: schrisski21@gmail.com (ID: 58)', '102.89.69.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 23:08:50'),
(977, 3, 'USER_DELETED', 'Deleted user: kinsleybrune7@gmail.com (ID: 57)', '102.89.69.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 23:08:53'),
(978, 3, 'USER_DELETED', 'Deleted user: mrmichaeljpratt@gmail.com (ID: 56)', '102.89.69.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 23:08:57'),
(979, 3, 'USER_DELETED', 'Deleted user: davidgeorge9125@gmail.com (ID: 55)', '102.89.69.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 23:09:00'),
(980, 3, 'USER_DELETED', 'Deleted user: helena14smith@gmail.com (ID: 54)', '102.89.69.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 23:09:04'),
(981, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user billyfredrickgibbons@gmail.com (ID: 37)', '102.89.69.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 23:09:35'),
(983, 3, 'LOGIN', 'User logged in', '102.89.69.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Cursor/2.4.21 Chrome/142.0.7444.235 Electron/39.2.7 Safari/537.36', '2026-01-27 23:14:52'),
(984, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user billyfredrickgibbons@gmail.com (ID: 37)', '102.89.69.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Cursor/2.4.21 Chrome/142.0.7444.235 Electron/39.2.7 Safari/537.36', '2026-01-27 23:15:06'),
(985, 3, 'LOGIN', 'User logged in', '102.89.69.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 23:16:01'),
(986, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user billyfredrickgibbons@gmail.com (ID: 37)', '102.89.69.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 23:16:16'),
(992, 3, 'LOGIN', 'User logged in', '102.89.69.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 23:34:15'),
(1002, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user billyfredrickgibbons@gmail.com (ID: 37)', '102.89.69.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 23:38:45'),
(1004, 3, 'LOGIN', 'User logged in', '102.89.69.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 23:43:29'),
(1005, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user billyfredrickgibbons@gmail.com (ID: 37)', '102.89.69.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 23:44:10'),
(1010, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.89.69.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 23:45:38'),
(1014, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user billyfredrickgibbons@gmail.com (ID: 37)', '102.89.69.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 23:46:49'),
(1016, 3, 'LOGIN', 'User logged in', '102.89.68.171', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 00:34:35'),
(1017, 3, 'USER_DELETED', 'Deleted user: emilylouisaz1zz@gmail.com (ID: 53)', '102.89.68.171', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 00:35:01'),
(1018, 3, 'LOGIN', 'User logged in', '102.89.68.171', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 02:17:06'),
(1019, 3, 'ADMIN_DELETED', 'Deleted administrator: admin user (support@zentropay-global.pro)', '102.89.68.171', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 02:17:27'),
(1020, 3, 'ADMIN_USER_CREATED', 'Created new admin user: support@cosmopolitantrustbankpf.com', '102.89.68.171', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 02:21:13'),
(1021, 60, 'LOGIN', 'User logged in', '98.97.76.229', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0.1 Mobile/15E148 Safari/604.1', '2026-01-29 14:07:49'),
(1022, 60, 'LOGIN', 'User logged in', '95.181.235.147', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-01-29 14:45:31'),
(1023, 60, 'LOGIN', 'User logged in', '2605:59c1:19e5:d610:e1fa:9d4a:1fae:da67', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 14:55:58'),
(1024, 60, 'LOGOUT', 'User logged out', '2605:59c1:19e5:d610:e1fa:9d4a:1fae:da67', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 15:08:03'),
(1025, 60, 'LOGIN', 'User logged in', '2605:59c1:19e5:d610:e1fa:9d4a:1fae:da67', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 15:08:31'),
(1026, 60, 'USER_DELETED', 'Deleted user: aishagaddafi3992@gmail.com (ID: 61)', '2605:59c1:19e5:d610:e1fa:9d4a:1fae:da67', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 15:11:22'),
(1027, 60, 'LOGOUT', 'User logged out', '2605:59c1:19e5:d610:e1fa:9d4a:1fae:da67', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 15:11:40'),
(1028, 60, 'LOGIN', 'User logged in', '2605:59c1:19e5:d610:e1fa:9d4a:1fae:da67', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 15:11:44'),
(1029, 60, 'LOGOUT', 'User logged out', '105.112.217.80', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 15:16:45'),
(1030, 60, 'LOGIN', 'User logged in', '105.112.217.80', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 15:18:24'),
(1031, 60, 'ADMIN_UPLOAD_PROFILE_PICTURE', 'Uploaded profile picture for user aishagaddafi3992@gmail.com (ID: 66)', '105.112.217.80', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 15:24:09'),
(1032, 60, 'ADMIN_UPLOAD_PROFILE_PICTURE', 'Uploaded profile picture for user aishagaddafi3992@gmail.com (ID: 66)', '105.112.217.80', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 15:24:41'),
(1033, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user aishagaddafi3992@gmail.com (ID: 66)', '105.112.217.80', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 15:49:10'),
(1037, 60, 'LOGIN', 'User logged in', '105.112.217.80', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 16:00:17'),
(1038, 60, 'LOGOUT', 'User logged out', '105.112.217.80', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 16:01:17'),
(1039, 60, 'LOGIN', 'User logged in', '105.112.217.80', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 16:01:41'),
(1040, 60, 'USER_DELETED', 'Deleted user: aishagaddafi3992@gmail.com (ID: 66)', '105.112.217.80', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 16:01:56'),
(1041, 60, 'LOGOUT', 'User logged out', '2605:59c1:19e5:d610:5d3c:b172:96a2:df12', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 16:29:36'),
(1042, 60, 'LOGIN', 'User logged in', '2605:59c1:19e5:d610:c87:f164:f520:4729', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-30 04:50:38'),
(1043, 60, 'USER_DELETED', 'Deleted user: aishamuammarg81@gmail.com (ID: 69)', '2605:59c1:19e5:d610:c87:f164:f520:4729', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-30 04:51:43'),
(1044, 60, 'USER_DELETED', 'Deleted user: aishagaddafi3992@gmail.com (ID: 68)', '2605:59c1:19e5:d610:c87:f164:f520:4729', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-30 04:51:46'),
(1045, 60, 'LOGOUT', 'User logged out', '2605:59c1:19e5:d610:c87:f164:f520:4729', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-30 05:00:55'),
(1046, 60, 'LOGIN', 'User logged in', '2605:59c1:19e5:d610:c87:f164:f520:4729', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-30 05:01:29'),
(1047, 60, 'LOGOUT', 'User logged out', '2605:59c1:19e5:d610:c87:f164:f520:4729', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-30 05:03:18'),
(1048, 60, 'LOGIN', 'User logged in', '2605:59c1:19e5:d610:c87:f164:f520:4729', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-30 06:24:28'),
(1049, 3, 'LOGIN', 'User logged in', '102.88.113.167', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-30 12:55:52'),
(1050, 3, 'EMAIL_TEST', 'Sent test email (test) to mr.carter.tech07@gmail.com', '102.88.113.167', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-30 13:06:09'),
(1051, 60, 'LOGIN', 'User logged in', '105.112.107.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-01-31 13:35:09'),
(1052, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user gaddafiayeshamaummar@gmail.com (ID: 71)', '105.112.107.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-01-31 13:38:18'),
(1057, 60, 'LOGIN', 'User logged in', '105.112.107.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-01-31 13:43:26'),
(1058, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user gaddafiayeshamaummar@gmail.com (ID: 71)', '105.112.107.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-01-31 13:51:03'),
(1061, 60, 'LOGIN', 'User logged in', '105.112.107.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-01-31 14:08:25'),
(1062, 60, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction TXN697E0AF9A407B for user gaddafiayeshamaummar@gmail.com. Amount changed from 10250 to 10250. Date changed from 2026-01-31 14:00:25 to 2005-11-15 13:00:00', '105.112.107.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-01-31 14:09:40'),
(1063, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user gaddafiayeshamaummar@gmail.com (ID: 71)', '105.112.107.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-01-31 14:10:36'),
(1066, 3, 'LOGIN', 'User logged in', '149.88.103.34', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-01 14:47:24'),
(1067, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user hkr.fred@outlook.com (ID: 27)', '149.88.103.34', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-01 15:07:47'),
(1068, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '149.88.103.34', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-01 15:08:22'),
(1069, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user hkr.fred@outlook.com (ID: 27)', '149.88.103.34', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-01 15:11:22'),
(1074, 3, 'LOGIN', 'User logged in', '105.113.96.67', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-04 17:13:05'),
(1075, 3, 'LOGOUT', 'User logged out', '105.113.96.67', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-04 17:13:48'),
(1076, 3, 'LOGIN', 'User logged in', '105.113.96.67', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-04 17:17:19'),
(1077, 3, 'EMAIL_TEST', 'Sent test email (test) to mr.carter.tech07@gmail.com', '105.113.96.67', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-04 17:18:35'),
(1078, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user billyfredrickgibbons@gmail.com (ID: 37)', '105.113.96.67', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-04 17:21:23'),
(1080, 60, 'LOGIN', 'User logged in', '2605:59c0:e40:4600:9444:6779:544f:fa43', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-04 20:48:56'),
(1081, 60, 'LOGOUT', 'User logged out', '2605:59c0:e40:4600:9444:6779:544f:fa43', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-04 20:50:02'),
(1082, 60, 'LOGIN', 'User logged in', '2605:59c0:e40:4600:9444:6779:544f:fa43', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-04 20:50:14'),
(1083, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user gaddafiayeshamaummar@gmail.com (ID: 71)', '2605:59c0:e40:4600:9444:6779:544f:fa43', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-04 20:53:40'),
(1084, 60, 'LOGIN', 'User logged in', '173.239.247.138', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0.1 Mobile/15E148 Safari/604.1', '2026-02-04 22:55:22'),
(1085, 60, 'LOGOUT', 'User logged out', '173.239.247.138', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0.1 Mobile/15E148 Safari/604.1', '2026-02-04 22:58:18'),
(1086, 60, 'LOGIN', 'User logged in', '173.239.247.138', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0.1 Mobile/15E148 Safari/604.1', '2026-02-04 22:58:29'),
(1087, 60, 'ADMIN_UPLOAD_PROFILE_PICTURE', 'Uploaded profile picture for user gaddafiayeshamaummar@gmail.com (ID: 71)', '173.239.247.138', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0.1 Mobile/15E148 Safari/604.1', '2026-02-04 23:02:53'),
(1088, 60, 'ADMIN_UPLOAD_PROFILE_PICTURE', 'Uploaded profile picture for user gaddafiayeshamaummar@gmail.com (ID: 71)', '173.239.247.138', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0.1 Mobile/15E148 Safari/604.1', '2026-02-04 23:06:19'),
(1089, 60, 'rates_refreshed', 'Refreshed exchange rates', '173.239.247.138', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0.1 Mobile/15E148 Safari/604.1', '2026-02-04 23:47:49'),
(1090, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user gaddafiayeshamaummar@gmail.com (ID: 71)', '173.239.247.138', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0.1 Mobile/15E148 Safari/604.1', '2026-02-04 23:49:32'),
(1092, 60, 'LOGIN', 'User logged in', '173.239.247.138', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0.1 Mobile/15E148 Safari/604.1', '2026-02-04 23:53:02'),
(1093, 60, 'LOGOUT', 'User logged out', '173.239.247.138', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0.1 Mobile/15E148 Safari/604.1', '2026-02-04 23:53:19'),
(1094, 60, 'LOGIN', 'User logged in', '173.239.247.138', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0.1 Mobile/15E148 Safari/604.1', '2026-02-04 23:53:30'),
(1100, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user gaddafiayeshamaummar@gmail.com (ID: 71)', '173.239.247.138', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0.1 Mobile/15E148 Safari/604.1', '2026-02-05 00:21:07'),
(1103, 60, 'LOGIN', 'User logged in', '173.239.247.138', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0.1 Mobile/15E148 Safari/604.1', '2026-02-05 00:24:27'),
(1104, 60, 'LOGOUT', 'User logged out', '173.239.247.138', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0.1 Mobile/15E148 Safari/604.1', '2026-02-05 00:24:35'),
(1105, 60, 'LOGIN', 'User logged in', '173.239.247.138', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0.1 Mobile/15E148 Safari/604.1', '2026-02-05 00:24:42'),
(1106, 60, 'LOGOUT', 'User logged out', '173.239.247.138', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0.1 Mobile/15E148 Safari/604.1', '2026-02-05 00:25:09'),
(1107, 60, 'LOGIN', 'User logged in', '173.239.247.138', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0.1 Mobile/15E148 Safari/604.1', '2026-02-05 00:25:18'),
(1108, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user gaddafiayeshamaummar@gmail.com (ID: 71)', '173.239.247.138', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0.1 Mobile/15E148 Safari/604.1', '2026-02-05 00:27:24'),
(1142, 60, 'LOGIN', 'User logged in', '105.112.102.62', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-06 22:08:33'),
(1143, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user gaddafiayeshamaummar@gmail.com (ID: 71)', '105.112.102.62', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-06 22:09:08'),
(1158, 60, 'LOGIN', 'User logged in', '2605:59c0:ec1:1310:c465:6926:fa38:9ea6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 19:56:32'),
(1169, 60, 'LOGIN', 'User logged in', '2605:59c0:ec1:1310:e479:71de:fd1:df97', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 01:04:58'),
(1189, 60, 'LOGIN', 'User logged in', '105.112.106.183', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_6_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Mobile/15E148 Safari/604.1', '2026-02-09 19:23:52'),
(1190, 60, 'LOGIN', 'User logged in', '105.112.106.183', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_6_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Mobile/15E148 Safari/604.1', '2026-02-09 20:36:30'),
(1191, 60, 'LOGOUT', 'User logged out', '105.112.106.183', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_6_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Mobile/15E148 Safari/604.1', '2026-02-09 20:39:43'),
(1192, 60, 'LOGIN', 'User logged in', '105.112.106.183', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_6_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Mobile/15E148 Safari/604.1', '2026-02-09 20:40:00'),
(1193, 60, 'LOGOUT', 'User logged out', '105.112.106.183', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_6_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Mobile/15E148 Safari/604.1', '2026-02-09 20:50:43'),
(1196, 60, 'LOGIN', 'User logged in', '2605:59c0:ec1:1310:7ce0:5d4f:9309:2cfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 21:10:18'),
(1197, 60, 'LOGOUT', 'User logged out', '2605:59c0:ec1:1310:7ce0:5d4f:9309:2cfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 21:10:50'),
(1198, 60, 'LOGIN', 'User logged in', '2605:59c0:ec1:1310:7ce0:5d4f:9309:2cfb', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 21:11:02'),
(1200, 3, 'LOGIN', 'User logged in', '102.89.44.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 22:27:30'),
(1201, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user billyfredrickgibbons@gmail.com (ID: 37)', '102.89.44.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 22:27:54'),
(1202, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.89.44.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 22:28:27'),
(1203, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user billyfredrickgibbons@gmail.com (ID: 37)', '102.89.44.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 22:29:47'),
(1205, 3, 'LOGIN', 'User logged in', '102.89.44.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 22:40:12'),
(1206, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user billyfredrickgibbons@gmail.com (ID: 37)', '102.89.44.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 22:40:21'),
(1209, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.89.44.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 22:41:48'),
(1210, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user hkr.fred@outlook.com (ID: 27)', '102.89.44.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 22:41:54'),
(1211, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.89.44.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 22:42:10'),
(1212, 3, 'ADMIN_DELETE_TRANSACTION', 'Deleted transaction TXN698A628F73F68 for user hkr.fred@outlook.com. Reason: x', '102.89.44.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 22:42:28');
INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `details`, `ip_address`, `user_agent`, `created_at`) VALUES
(1213, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user billyfredrickgibbons@gmail.com (ID: 37)', '102.89.44.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 22:43:51'),
(1214, 3, 'LOGIN', 'User logged in', '102.89.44.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 22:43:58'),
(1220, 60, 'LOGIN', 'User logged in', '2605:59c0:ec1:1310:eff:6ee3:af62:f671', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-10 02:16:19'),
(1221, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user officialaishagaddafi1@gmail.com (ID: 80)', '2605:59c0:ec1:1310:eff:6ee3:af62:f671', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-10 02:16:48'),
(1223, 60, 'LOGIN', 'User logged in', '2605:59c0:ec1:1310:eff:6ee3:af62:f671', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-10 02:19:13'),
(1224, 60, 'LOGOUT', 'User logged out', '2605:59c0:ec1:1310:eff:6ee3:af62:f671', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-10 02:21:37'),
(1226, 60, 'LOGIN', 'User logged in', '2605:59c0:ec1:1310:eff:6ee3:af62:f671', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-10 02:24:32'),
(1227, 60, 'ADMIN_USER_PASSWORD_RESET', 'Admin reset password for user: gaddafiayeshamaummar@gmail.com (ID: 71)', '2605:59c0:ec1:1310:eff:6ee3:af62:f671', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-10 02:26:04'),
(1228, 60, 'LOGOUT', 'User logged out', '2605:59c0:ec1:1310:eff:6ee3:af62:f671', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-10 02:26:37'),
(1229, 60, 'LOGIN', 'User logged in', '2605:59c0:ec1:1310:eff:6ee3:af62:f671', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-10 02:32:40'),
(1230, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user gaddafiayeshamaummar@gmail.com (ID: 71)', '2605:59c0:ec1:1310:eff:6ee3:af62:f671', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-10 02:34:46'),
(1232, 60, 'LOGIN', 'User logged in', '2605:59c0:ec1:1310:eff:6ee3:af62:f671', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-10 02:54:37'),
(1233, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user gaddafiayeshamaummar@gmail.com (ID: 71)', '2605:59c0:ec1:1310:eff:6ee3:af62:f671', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-10 03:04:35'),
(1236, 60, 'LOGIN', 'User logged in', '2605:59c0:ec1:1310:eff:6ee3:af62:f671', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-10 03:11:05'),
(1237, 60, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction TXN698AA1530CD93 for user gaddafiayeshamaummar@gmail.com. Amount changed from 10250 to 10250. Date changed from 2026-02-10 03:09:07 to 2009-10-28 02:09:00', '2605:59c0:ec1:1310:eff:6ee3:af62:f671', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-10 03:14:52'),
(1238, 60, 'ADMIN_SET_TRANSACTION_MODE', 'Set transaction mode to \'force_pending\' for user gaddafiayeshamaummar@gmail.com (ID: 71)', '2605:59c0:ec1:1310:eff:6ee3:af62:f671', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-10 03:18:14'),
(1239, 60, 'LOGOUT', 'User logged out', '2605:59c0:ec1:1310:eff:6ee3:af62:f671', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-10 03:19:31'),
(1273, 60, 'LOGIN', 'User logged in', '2605:59c0:ec1:1310:1977:fbc9:4485:c20', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 12:42:38'),
(1276, 60, 'LOGIN', 'User logged in', '2605:59c0:ec1:1310:8fe1:ed4b:3d3f:5e76', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-11 20:00:22'),
(1277, 60, 'LOGOUT', 'User logged out', '2605:59c0:ec1:1310:8fe1:ed4b:3d3f:5e76', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-11 20:00:36'),
(1287, 3, 'LOGIN', 'User logged in', '105.113.77.250', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-12 10:18:50'),
(1288, 3, 'EMAIL_TEST', 'Sent test email (test) to mr.carter.tech07@gmail.com', '105.113.77.250', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-12 10:19:28'),
(1290, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user billyfredrickgibbons@gmail.com (ID: 37)', '105.113.77.250', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-12 10:38:30'),
(1297, 3, 'LOGIN', 'User logged in', '105.113.60.235', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-12 14:57:20'),
(1304, 60, 'LOGIN', 'User logged in', '2605:59c0:ec1:1310:bd6b:4341:8258:9bb9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-13 04:32:47'),
(1306, 60, 'LOGIN', 'User logged in', '2605:59c0:ec1:1310:60e7:417e:b711:bea1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_6_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Mobile/15E148 Safari/604.1', '2026-02-13 12:22:27'),
(1307, 60, 'LOGIN', 'User logged in', '2605:59c0:ec1:1310:6db4:d695:b224:70c2', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-13 12:25:10'),
(1308, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user officialaishagaddafi1@gmail.com (ID: 80)', '2605:59c0:ec1:1310:6db4:d695:b224:70c2', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-13 12:27:51'),
(1310, 60, 'LOGIN', 'User logged in', '2605:59c0:ec1:1310:6db4:d695:b224:70c2', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-13 12:34:16'),
(1311, 60, 'LOGIN', 'User logged in', '2605:59c0:ec1:1310:b917:2c17:5c7b:389c', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-13 12:37:25'),
(1312, 60, 'USER_DELETED', 'Deleted user: officialaishagaddafi1@gmail.com (ID: 80)', '2605:59c0:ec1:1310:b917:2c17:5c7b:389c', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-13 12:39:15'),
(1313, 60, 'KYC_AUTO_VERIFIED', 'Auto-verified KYC for user 90 during account creation', '2605:59c0:ec1:1310:b917:2c17:5c7b:389c', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-13 12:49:44'),
(1315, 60, 'ADMIN_UPLOAD_PROFILE_PICTURE', 'Uploaded profile picture for user officialaishagaddafi1@gmail.com (ID: 90)', '2605:59c0:ec1:1310:b917:2c17:5c7b:389c', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-13 12:52:14'),
(1316, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user officialaishagaddafi1@gmail.com (ID: 90)', '2605:59c0:ec1:1310:b917:2c17:5c7b:389c', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-13 12:52:48'),
(1321, 60, 'LOGIN', 'User logged in', '105.112.212.60', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_6_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Mobile/15E148 Safari/604.1', '2026-02-13 13:30:53'),
(1322, 60, 'LOGOUT', 'User logged out', '105.112.212.60', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_6_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Mobile/15E148 Safari/604.1', '2026-02-13 13:33:32'),
(1327, 60, 'LOGIN', 'User logged in', '2605:59c0:ec1:1310:4fc:ea5e:35ba:e468', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-13 13:55:05'),
(1328, 60, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction TXN698F25BD92141 for user officialaishagaddafi1@gmail.com. Amount changed from 205000 to 205000. Date changed from 2026-02-13 13:23:09 to 2005-08-24 12:23:00', '2605:59c0:ec1:1310:4fc:ea5e:35ba:e468', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-13 14:25:31'),
(1329, 60, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction TXN698F294FA1A0D for user officialaishagaddafi1@gmail.com. Amount changed from 153750 to 153750. Date changed from 2026-02-13 13:38:23 to 2006-05-03 12:38:00', '2605:59c0:ec1:1310:4fc:ea5e:35ba:e468', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-13 14:26:07'),
(1331, 60, 'LOGIN', 'User logged in', '2605:59c0:ec1:1310:64df:ded3:467d:7f3f', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-15 13:41:43'),
(1332, 60, 'LOGIN', 'User logged in', '2605:59c0:ec1:1310:4e3c:fea1:7a18:20b6', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-15 13:53:01'),
(1333, 60, 'LOGOUT', 'User logged out', '2605:59c0:ec1:1310:4e3c:fea1:7a18:20b6', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-15 13:55:02'),
(1334, 60, 'LOGIN', 'User logged in', '2605:59c0:ec1:1310:4e3c:fea1:7a18:20b6', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-15 13:55:41'),
(1335, 60, 'ADMIN_USER_PASSWORD_RESET', 'Admin reset password for user: gaddafiayeshamaummar@gmail.com (ID: 71)', '2605:59c0:ec1:1310:4e3c:fea1:7a18:20b6', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-15 13:57:54'),
(1336, 60, 'LOGOUT', 'User logged out', '2605:59c0:ec1:1310:4e3c:fea1:7a18:20b6', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-15 13:58:51'),
(1338, 60, 'LOGIN', 'User logged in', '2605:59c0:ec1:1310:64df:ded3:467d:7f3f', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-15 14:23:26'),
(1345, 60, 'LOGIN', 'User logged in', '2605:59c0:ec1:1310:c1e8:ad79:4d15:33bd', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-16 23:23:16'),
(1346, 60, 'ADMIN_TOGGLE_2FA', 'Admin support@cosmopolitantrustbankpf.com disabled two-factor authentication for user gaddafiayeshamaummar@gmail.com', '2605:59c0:ec1:1310:c1e8:ad79:4d15:33bd', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-16 23:29:46'),
(1348, 60, 'ADMIN_TOGGLE_2FA', 'Admin support@cosmopolitantrustbankpf.com disabled two-factor authentication for user gaddafiayeshamaummar@gmail.com', '2605:59c0:ec1:1310:c1e8:ad79:4d15:33bd', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-16 23:36:16'),
(1349, 60, 'ADMIN_TOGGLE_2FA', 'Admin support@cosmopolitantrustbankpf.com enabled two-factor authentication for user gaddafiayeshamaummar@gmail.com', '2605:59c0:ec1:1310:c1e8:ad79:4d15:33bd', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-16 23:37:20'),
(1358, 60, 'LOGIN', 'User logged in', '2605:59c0:ec1:1310:542e:6e28:c121:f5b', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-17 13:43:29'),
(1359, 60, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction ADM20260217094057562 for user officialaishagaddafi1@gmail.com. Amount changed from 413000 to 413000. Date changed from 2026-02-17 15:34:00 to 2010-01-01 14:34:00', '2605:59c0:ec1:1310:542e:6e28:c121:f5b', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-17 14:42:28'),
(1360, 60, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction ADM20260217090848479 for user officialaishagaddafi1@gmail.com. Amount changed from 78000 to 78000. Date changed from 2026-02-17 02:57:00 to 2009-09-17 09:57:00', '2605:59c0:ec1:1310:542e:6e28:c121:f5b', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Mobile Safari/537.36', '2026-02-17 14:43:48'),
(1363, 60, 'LOGIN', 'User logged in', '143.105.174.198', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-18 08:05:07'),
(1372, 60, 'LOGIN', 'User logged in', '2605:59c0:ec1:1310:d0a0:fc5c:efdf:d9a3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-19 00:30:43'),
(1373, 60, 'LOGIN', 'User logged in', '105.112.213.171', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-19 07:53:20'),
(1374, 60, 'ADMIN_DELETE_TRANSACTION', 'Deleted transaction TXN69957429B7BF8 for user officialaishagaddafi1@gmail.com. Reason: There was a mistake in the transaction', '105.112.213.171', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-19 07:55:55'),
(1375, 60, 'LOGIN', 'User logged in', '105.112.212.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-19 08:34:12'),
(1376, 60, 'KYC_AUTO_VERIFIED', 'Auto-verified KYC for user 96 during account creation', '105.112.212.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-19 08:44:04'),
(1378, 60, 'ADMIN_UPLOAD_PROFILE_PICTURE', 'Uploaded profile picture for user aishamuammar87@gmail.com (ID: 96)', '105.112.212.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-19 08:45:39'),
(1379, 60, 'ADMIN_UPLOAD_PROFILE_PICTURE', 'Uploaded profile picture for user aishamuammar87@gmail.com (ID: 96)', '105.112.212.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-19 08:46:05'),
(1381, 60, 'LOGIN', 'User logged in', '105.112.212.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-19 09:51:00'),
(1382, 60, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction ADM20260219045030616 for user aishamuammar87@gmail.com. Amount changed from 341000 to 500000. Date changed from 2006-09-15 15:45:00 to 2006-11-07 13:45:00', '105.112.212.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-19 09:55:32'),
(1383, 60, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction ADM20260219045030616 for user aishamuammar87@gmail.com. Amount changed from 500000 to 500000. Date changed from 2006-11-07 13:45:00 to 2006-11-07 12:45:00', '105.112.212.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-19 09:56:45'),
(1387, 60, 'LOGIN', 'User logged in', '105.112.212.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-19 10:05:02'),
(1388, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user gaddafiayeshamaummar@gmail.com (ID: 71)', '105.112.212.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-19 10:08:38'),
(1395, 60, 'ADMIN_DELETE_TRANSACTION', 'Deleted transaction TXN6996E764886D5 for user aishamuammar87@gmail.com. Reason: mistake', '105.112.212.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-19 10:35:44'),
(1400, 60, 'LOGIN', 'User logged in', '143.105.174.26', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-19 22:31:57'),
(1401, 60, 'ADMIN_DELETE_TRANSACTION', 'Deleted transaction TXN69978F146EAC6 for user aishamuammar87@gmail.com. Reason: Mistake', '143.105.174.26', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-19 22:32:18'),
(1406, 60, 'LOGIN', 'User logged in', '2605:59c0:ec1:1310:7de1:375b:ae23:8bb7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-19 23:50:13'),
(1407, 60, 'ADMIN_DELETE_TRANSACTION', 'Deleted transaction TXN6997A16E64EE4 for user officialaishagaddafi1@gmail.com. Reason: mistake', '2605:59c0:ec1:1310:7de1:375b:ae23:8bb7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-19 23:50:44'),
(1513, 3, 'LOGIN', 'User logged in', '105.112.39.87', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-22 12:45:26'),
(1514, 3, 'LOGOUT', 'User logged out', '105.112.39.87', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-22 13:06:14'),
(1523, 60, 'LOGIN', 'User logged in', '197.211.53.98', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-23 03:57:01'),
(1524, 60, 'LOGOUT', 'User logged out', '197.211.53.98', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-23 03:59:06'),
(1525, 60, 'LOGIN', 'User logged in', '197.211.53.98', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-23 03:59:18'),
(1527, 60, 'LOGOUT', 'User logged out', '197.211.53.98', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-23 04:01:15'),
(1528, 60, 'LOGIN', 'User logged in', '197.211.53.98', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-23 04:03:50'),
(1529, 60, 'LOGOUT', 'User logged out', '197.211.53.98', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-23 04:04:39'),
(1530, 60, 'LOGIN', 'User logged in', '197.211.53.98', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-23 04:08:23'),
(1532, 60, 'LOGIN', 'User logged in', '197.211.52.179', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-23 12:22:57'),
(1534, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user gaddafiayeshamaummar@gmail.com (ID: 71)', '129.205.124.218', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-23 12:33:48'),
(1538, 60, 'LOGIN', 'User logged in', '2605:59c0:ec1:1310:f581:8939:9db4:7263', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-23 20:45:23'),
(1539, 60, 'bank_added', 'Added bank: Vietcom bank', '2605:59c0:ec1:1310:f581:8939:9db4:7263', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-23 20:48:36'),
(1540, 60, 'bank_added', 'Added bank: VietinBank', '2605:59c0:ec1:1310:f581:8939:9db4:7263', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-23 20:50:05'),
(1541, 60, 'LOGIN', 'User logged in', '105.112.203.83', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_6_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Mobile/15E148 Safari/604.1', '2026-02-23 23:44:12'),
(1542, 60, 'KYC_AUTO_VERIFIED', 'Auto-verified KYC for user 108 during account creation', '105.112.203.83', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_6_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Mobile/15E148 Safari/604.1', '2026-02-23 23:53:29'),
(1544, 60, 'LOGIN', 'User logged in', '129.222.206.233', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3 Mobile/15E148 Safari/604.1', '2026-02-24 05:33:47'),
(1545, 60, 'LOGIN', 'User logged in', '129.222.206.233', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3 Mobile/15E148 Safari/604.1', '2026-02-24 05:37:59'),
(1546, 60, 'bank_added', 'Added bank: Vietnam International Commercial Joint Stock Bank (VIB)', '129.222.206.233', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3 Mobile/15E148 Safari/604.1', '2026-02-24 05:38:29'),
(1547, 60, 'bank_added', 'Added bank: BIDV (Bank for Investment and Development of Vietnam', '129.222.206.233', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3 Mobile/15E148 Safari/604.1', '2026-02-24 05:41:13'),
(1548, 60, 'bank_added', 'Added bank: Agribank', '129.222.206.233', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3 Mobile/15E148 Safari/604.1', '2026-02-24 05:42:07'),
(1549, 60, 'bank_added', 'Added bank: MBBank (Military Commercial Joint Stock Bank', '129.222.206.233', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3 Mobile/15E148 Safari/604.1', '2026-02-24 05:42:56'),
(1550, 60, 'bank_added', 'Added bank: Techcombank (Vietnam Technological and Commercial Joint Stock Bank', '129.222.206.233', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3 Mobile/15E148 Safari/604.1', '2026-02-24 05:43:55'),
(1551, 60, 'bank_added', 'Added bank: HDBank (Ho Chi Minh City Development Joint Stock Commercial Bank', '129.222.206.233', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3 Mobile/15E148 Safari/604.1', '2026-02-24 05:45:25'),
(1552, 60, 'bank_added', 'Added bank: ACB (Asia Commercial Joint Stock Bank)', '129.222.206.233', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3 Mobile/15E148 Safari/604.1', '2026-02-24 05:46:29'),
(1553, 60, 'bank_added', 'Added bank: Techcombank (Vietnam Technological and Commercial Joint Stock Bank )', '129.222.206.233', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3 Mobile/15E148 Safari/604.1', '2026-02-24 05:47:24'),
(1554, 60, 'bank_added', 'Added bank: Sacombank (Saigon Thuong Tin Commercial Joint Stock Bank )', '129.222.206.233', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3 Mobile/15E148 Safari/604.1', '2026-02-24 05:48:15'),
(1555, 60, 'bank_added', 'Added bank: MSB (Vietnam Maritime Commercial Joint Stock Bank )', '129.222.206.233', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3 Mobile/15E148 Safari/604.1', '2026-02-24 05:49:13'),
(1556, 60, 'bank_added', 'Added bank: LPBank (Loc Phat Vietnam Commercial Joint Stock Bank )', '129.222.206.233', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3 Mobile/15E148 Safari/604.1', '2026-02-24 05:53:31'),
(1557, 60, 'bank_added', 'Added bank: VIB (Vietnam International Commercial Joint Stock Bank )', '129.222.206.233', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3 Mobile/15E148 Safari/604.1', '2026-02-24 05:54:26'),
(1558, 60, 'bank_added', 'Added bank: HCM City Development Bank (HDBank)', '129.222.206.233', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3 Mobile/15E148 Safari/604.1', '2026-02-24 05:56:24'),
(1559, 60, 'LOGIN', 'User logged in', '129.222.206.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-24 06:04:52'),
(1560, 60, 'LOGOUT', 'User logged out', '129.222.206.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-24 06:26:22'),
(1561, 60, 'LOGIN', 'User logged in', '129.222.206.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-24 06:30:08'),
(1562, 60, 'LOGOUT', 'User logged out', '129.222.206.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-24 06:30:19'),
(1563, 60, 'LOGIN', 'User logged in', '129.222.206.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-24 06:30:37'),
(1564, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user gaddafiayeshamaummar@gmail.com (ID: 71)', '129.222.206.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-24 06:30:51'),
(1571, 60, 'LOGIN', 'User logged in', '2605:59c0:ec1:1310:ac8a:629:14fa:5666', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-24 08:02:08'),
(1572, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user gaddafiayeshamaummar@gmail.com (ID: 71)', '2605:59c0:ec1:1310:ac8a:629:14fa:5666', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-24 08:08:01'),
(1573, 60, 'LOGIN', 'User logged in', '2605:59c0:ec1:1310:ac8a:629:14fa:5666', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 08:12:34'),
(1574, 60, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction ADM20260224040223647 for user officialaishagaddafi1@gmail.com. Amount changed from 878000 to 878000. Date changed from 2026-02-24 15:06:00 to 2008-11-28 14:06:00', '2605:59c0:ec1:1310:ac8a:629:14fa:5666', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 09:03:47'),
(1575, 60, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction ADM20260224035141804 for user officialaishagaddafi1@gmail.com. Amount changed from 340000 to 340000. Date changed from 2026-02-24 13:50:00 to 2009-01-07 12:50:00', '2605:59c0:ec1:1310:ac8a:629:14fa:5666', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 09:05:14'),
(1577, 60, 'LOGIN', 'User logged in', '105.112.209.72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-24 11:42:57'),
(1578, 60, 'LOGIN', 'User logged in', '197.210.55.62', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 11:54:12'),
(1579, 60, 'LOGIN', 'User logged in', '105.115.5.73', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 11:58:43'),
(1580, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user officialaishagaddafi1@gmail.com (ID: 108)', '105.112.209.72', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-24 12:06:58'),
(1581, 60, 'KYC_AUTO_VERIFIED', 'Auto-verified KYC for user 109 during account creation', '105.115.5.73', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 12:11:14'),
(1583, 60, 'ADMIN_UPLOAD_PROFILE_PICTURE', 'Uploaded profile picture for user aishamuammar87@gmail.com (ID: 109)', '105.115.5.73', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 12:12:07'),
(1584, 60, 'ADMIN_USER_PASSWORD_RESET', 'Admin reset password for user: aishamuammar87@gmail.com (ID: 109)', '105.115.5.73', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 12:15:47'),
(1585, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user aishamuammar87@gmail.com (ID: 109)', '105.115.5.73', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 12:17:27'),
(1589, 60, 'LOGIN', 'User logged in', '105.115.5.73', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 12:22:32'),
(1592, 60, 'LOGIN', 'User logged in', '105.115.5.73', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 13:10:54'),
(1593, 60, 'ADMIN_DELETE_TRANSACTION', 'Deleted transaction ADM20260224081031621 for user aishamuammar87@gmail.com. Reason: MISTAKE', '105.115.5.73', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 13:11:20'),
(1594, 60, 'ADMIN_DELETE_TRANSACTION', 'Deleted transaction ADM20260224081031785 for user aishamuammar87@gmail.com. Reason: MISTAKE', '105.115.5.73', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 13:11:51'),
(1595, 60, 'ADMIN_DELETE_TRANSACTION', 'Deleted transaction ADM20260224081031879 for user aishamuammar87@gmail.com. Reason: MISTAKE', '105.115.5.73', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 13:11:58'),
(1596, 60, 'ADMIN_DELETE_TRANSACTION', 'Deleted transaction ADM20260224081031405 for user aishamuammar87@gmail.com. Reason: MISTAKE', '105.115.5.73', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 13:12:32'),
(1597, 60, 'ADMIN_DELETE_TRANSACTION', 'Deleted transaction ADM20260224081031413 for user aishamuammar87@gmail.com. Reason: MISTAKE', '105.115.5.73', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 13:12:32'),
(1598, 60, 'ADMIN_DELETE_TRANSACTION', 'Deleted transaction ADM20260224081031853 for user aishamuammar87@gmail.com. Reason: MISTAKE', '105.115.5.73', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 13:12:37'),
(1599, 60, 'ADMIN_DELETE_TRANSACTION', 'Deleted transaction ADM20260224081031316 for user aishamuammar87@gmail.com. Reason: MISTAKE', '105.115.5.73', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 13:12:54'),
(1600, 60, 'ADMIN_DELETE_TRANSACTION', 'Deleted transaction ADM20260224081031105 for user aishamuammar87@gmail.com. Reason: MISTAKE', '105.115.5.73', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 13:12:54'),
(1601, 60, 'ADMIN_DELETE_TRANSACTION', 'Deleted transaction ADM20260224081031156 for user aishamuammar87@gmail.com. Reason: MISTAKE', '105.115.5.73', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 13:12:57'),
(1602, 60, 'ADMIN_DELETE_TRANSACTION', 'Deleted transaction ADM20260224081031235 for user aishamuammar87@gmail.com. Reason: MISTAKE', '105.115.5.73', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 13:13:04'),
(1603, 60, 'ADMIN_DELETE_TRANSACTION', 'Deleted transaction ADM20260224081031993 for user aishamuammar87@gmail.com. Reason: MISTAKE', '105.115.5.73', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 13:13:23'),
(1604, 60, 'ADMIN_DELETE_TRANSACTION', 'Deleted transaction ADM20260224081031947 for user aishamuammar87@gmail.com. Reason: MISTAKE', '105.115.5.73', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 13:13:28'),
(1605, 60, 'ADMIN_DELETE_TRANSACTION', 'Deleted transaction ADM20260224081031494 for user aishamuammar87@gmail.com. Reason: MISTAKE', '105.115.5.73', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 13:13:34'),
(1606, 60, 'ADMIN_DELETE_TRANSACTION', 'Deleted transaction ADM20260224081031112 for user aishamuammar87@gmail.com. Reason: MISTAKE', '105.115.5.73', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 13:13:42'),
(1609, 60, 'LOGIN', 'User logged in', '105.115.5.73', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 13:28:01'),
(1610, 60, 'LOGIN', 'User logged in', '143.105.174.70', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-24 13:39:53'),
(1611, 60, 'bank_added', 'Added bank: Bank Muscat (SAOG)', '143.105.174.70', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-24 13:45:26'),
(1612, 60, 'bank_added', 'Added bank: National Bank of Oman (NBO)', '143.105.174.70', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-24 13:46:13'),
(1613, 60, 'bank_added', 'Added bank: Bank Dhofar (S.A.O.G.)', '143.105.174.70', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-24 13:46:37'),
(1614, 60, 'bank_added', 'Added bank: Oman Arab Bank (OAB)', '143.105.174.70', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-24 13:46:59'),
(1615, 60, 'bank_added', 'Added bank: Sohar International (formerly Bank Sohar):', '143.105.174.70', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-24 13:47:26'),
(1616, 60, 'LOGOUT', 'User logged out', '105.112.204.65', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 13:47:56'),
(1617, 60, 'bank_added', 'Added bank: Ahli Bank', '143.105.174.70', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-24 13:47:57'),
(1618, 60, 'LOGIN', 'User logged in', '105.112.204.65', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 13:48:06'),
(1619, 60, 'bank_added', 'Added bank: Oman Development Bank / Oman Housing Bank', '143.105.174.70', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-24 13:48:31'),
(1620, 60, 'bank_added', 'Added bank: Bank Nizwa', '143.105.174.70', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-24 13:49:05'),
(1621, 60, 'bank_added', 'Added bank: Standard Chartered Bank', '143.105.174.70', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-24 13:49:40'),
(1622, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user aishamuammar87@gmail.com (ID: 109)', '105.112.204.65', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 14:15:51'),
(1623, 60, 'LOGIN', 'User logged in', '105.112.204.65', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 14:25:06'),
(1624, 60, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction ADM20260224081604408 for user aishamuammar87@gmail.com. Amount changed from 85000 to 41482.31. Date changed from 2009-02-24 14:13:00 to 2009-02-24 13:13:00', '105.112.204.65', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 14:26:15'),
(1625, 60, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction ADM20260224081604408 for user aishamuammar87@gmail.com. Amount changed from 41482.31 to 48092.65. Date changed from 2009-02-24 13:13:00 to 2009-02-24 12:13:00', '105.112.204.65', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 14:30:27'),
(1626, 60, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction ADM20260224081604408 for user aishamuammar87@gmail.com. Amount changed from 48092.65 to 60309.22. Date changed from 2009-02-24 12:13:00 to 2009-02-24 11:13:00', '105.112.204.65', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 14:32:17'),
(1627, 60, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction ADM20260224081604408 for user aishamuammar87@gmail.com. Amount changed from 60309.22 to 35876.08. Date changed from 2009-02-24 11:13:00 to 2009-02-24 10:13:00', '105.112.204.65', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 14:34:37'),
(1628, 60, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction ADM20260224081604408 for user aishamuammar87@gmail.com. Amount changed from 35876.08 to 34020.38. Date changed from 2009-02-24 10:13:00 to 2009-02-24 09:13:00', '105.112.204.65', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 14:36:11'),
(1629, 60, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction ADM20260224081604408 for user aishamuammar87@gmail.com. Amount changed from 34020.38 to 33738.5. Date changed from 2009-02-24 09:13:00 to 2009-02-24 08:13:00', '105.112.204.65', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 14:37:52'),
(1636, 60, 'LOGIN', 'User logged in', '105.112.216.184', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-25 09:06:31'),
(1637, 60, 'LOGOUT', 'User logged out', '105.112.216.184', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-25 09:06:51'),
(1638, 60, 'LOGIN', 'User logged in', '105.112.216.184', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-25 09:06:56'),
(1639, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user aishamuammar87@gmail.com (ID: 109)', '105.112.216.184', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-25 09:07:24'),
(1641, 60, 'LOGIN', 'User logged in', '105.112.216.184', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-25 09:08:29'),
(1642, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user aishamuammar87@gmail.com (ID: 109)', '105.112.216.184', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-25 09:09:27'),
(1646, 60, 'LOGIN', 'User logged in', '105.113.67.140', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_6_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Mobile/15E148 Safari/604.1', '2026-02-25 13:02:54'),
(1649, 60, 'LOGIN', 'User logged in', '2605:59c0:ec1:1310:d802:a082:7e94:22d9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-25 13:58:23'),
(1650, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user officialaishagaddafi1@gmail.com (ID: 108)', '2605:59c0:ec1:1310:d802:a082:7e94:22d9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-25 13:58:53'),
(1652, 60, 'LOGIN', 'User logged in', '2605:59c0:ec1:1310:d802:a082:7e94:22d9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-25 14:00:55'),
(1653, 60, 'ADMIN_REVERSE_TRANSACTION', 'Reversed transaction TXN699E7618C2340 for user officialaishagaddafi1@gmail.com', '2605:59c0:ec1:1310:d802:a082:7e94:22d9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-25 14:03:05'),
(1654, 60, 'ADMIN_SET_TRANSACTION_MODE', 'Set transaction mode to \'force_pending\' for user officialaishagaddafi1@gmail.com (ID: 108)', '2605:59c0:ec1:1310:d802:a082:7e94:22d9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-25 14:03:40'),
(1655, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user officialaishagaddafi1@gmail.com (ID: 108)', '2605:59c0:ec1:1310:d802:a082:7e94:22d9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-25 14:08:30'),
(1657, 60, 'LOGIN', 'User logged in', '2605:59c0:ec1:1310:8d1a:9218:c55e:dca8', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-26 01:23:52'),
(1658, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user officialaishagaddafi1@gmail.com (ID: 108)', '2605:59c0:ec1:1310:8d1a:9218:c55e:dca8', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-26 01:25:19'),
(1659, 60, 'LOGIN', 'User logged in', '2605:59c0:ec1:1310:8d1a:9218:c55e:dca8', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-26 02:13:02'),
(1660, 60, 'LOGOUT', 'User logged out', '2605:59c0:ec1:1310:8d1a:9218:c55e:dca8', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-26 02:13:09'),
(1661, 60, 'LOGIN', 'User logged in', '2605:59c0:ec1:1310:8d1a:9218:c55e:dca8', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-26 02:13:16'),
(1662, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user officialaishagaddafi1@gmail.com (ID: 108)', '2605:59c0:ec1:1310:8d1a:9218:c55e:dca8', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-02-26 02:13:41'),
(1665, 60, 'LOGIN', 'User logged in', '2605:59c0:ec1:1310:94d4:4a8e:5e76:2fd3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-26 09:43:37'),
(1666, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user officialaishagaddafi1@gmail.com (ID: 108)', '2605:59c0:ec1:1310:94d4:4a8e:5e76:2fd3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-26 09:44:18'),
(1670, 60, 'LOGIN', 'User logged in', '2605:59c0:ec1:1310:2c7e:72d8:45bc:9e76', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-28 07:33:02'),
(1671, 60, 'USER_DELETED', 'Deleted user: baelaycash@gmail.com (ID: 114)', '2605:59c0:ec1:1310:2c7e:72d8:45bc:9e76', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-28 07:33:18'),
(1672, 60, 'USER_DELETED', 'Deleted user: bael@gmail.com (ID: 113)', '2605:59c0:ec1:1310:2c7e:72d8:45bc:9e76', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-28 07:33:22'),
(1673, 60, 'USER_DELETED', 'Deleted user: mcstephen1090@gmail.com (ID: 111)', '2605:59c0:ec1:1310:2c7e:72d8:45bc:9e76', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-28 07:33:35'),
(1674, 60, 'USER_DELETED', 'Deleted user: elon93604@gmail.com (ID: 112)', '2605:59c0:ec1:1310:2c7e:72d8:45bc:9e76', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-28 07:33:48'),
(1675, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user officialaishagaddafi1@gmail.com (ID: 108)', '2605:59c0:ec1:1310:2c7e:72d8:45bc:9e76', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-28 07:34:07'),
(1676, 60, 'LOGIN', 'User logged in', '2605:59c0:ec1:1310:480b:d738:8a1:f41d', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-28 11:10:06'),
(1677, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user officialaishagaddafi1@gmail.com (ID: 108)', '2605:59c0:ec1:1310:480b:d738:8a1:f41d', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-02-28 11:10:22'),
(1685, 60, 'LOGIN', 'User logged in', '105.112.100.175', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-03-01 23:10:54'),
(1686, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user gaddafiayeshamaummar@gmail.com (ID: 71)', '105.112.100.175', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-03-01 23:11:17'),
(1687, 60, 'LOGIN', 'User logged in', '105.112.100.175', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-03-01 23:55:36'),
(1688, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user gaddafiayeshamaummar@gmail.com (ID: 71)', '105.112.100.175', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-03-01 23:56:06'),
(1689, 60, 'LOGIN', 'User logged in', '105.112.100.175', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-03-02 01:01:07'),
(1690, 60, 'LOGOUT', 'User logged out', '105.112.100.175', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-03-02 01:01:16'),
(1691, 60, 'LOGIN', 'User logged in', '105.112.100.175', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-03-02 01:01:23'),
(1692, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user gaddafiayeshamaummar@gmail.com (ID: 71)', '105.112.100.175', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-03-02 01:01:38'),
(1694, 60, 'LOGIN', 'User logged in', '105.112.100.175', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-03-02 01:10:43'),
(1695, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user officialaishagaddafi1@gmail.com (ID: 108)', '105.112.100.175', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-03-02 01:11:11'),
(1696, 3, 'LOGIN', 'User logged in', '102.89.76.252', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-02 01:42:19'),
(1697, 60, 'LOGIN', 'User logged in', '105.112.100.175', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-03-02 01:54:34'),
(1698, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user officialaishagaddafi1@gmail.com (ID: 108)', '105.112.100.175', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-03-02 01:55:11'),
(1699, 60, 'LOGIN', 'User logged in', '105.112.100.175', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-03-02 02:05:02');
INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `details`, `ip_address`, `user_agent`, `created_at`) VALUES
(1700, 60, 'LOGOUT', 'User logged out', '105.112.100.175', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-03-02 02:05:15'),
(1701, 60, 'LOGIN', 'User logged in', '105.112.100.175', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-03-02 02:05:22'),
(1702, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user officialaishagaddafi1@gmail.com (ID: 108)', '2605:59c0:ec1:1310:4037:7b7:f475:932e', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-03-02 02:10:27'),
(1704, 60, 'LOGIN', 'User logged in', '2605:59c0:ec1:1310:4037:7b7:f475:932e', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-03-02 02:25:51'),
(1705, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user gaddafiayeshamaummar@gmail.com (ID: 71)', '102.90.101.165', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-03-02 02:28:37'),
(1706, 3, 'LOGIN', 'User logged in', '102.89.76.252', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-02 02:32:13'),
(1707, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user hkr.fred@outlook.com (ID: 27)', '102.89.76.252', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-02 02:32:25'),
(1708, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.89.76.252', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-02 02:41:56'),
(1709, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user Ivanawonderwoman@outlook.com (ID: 115)', '102.89.76.252', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-02 02:42:11'),
(1710, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.89.76.252', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-02 02:42:26'),
(1711, 3, 'ADMIN_UPLOAD_PROFILE_PICTURE', 'Uploaded profile picture for user Ivanawonderwoman@outlook.com (ID: 117)', '102.89.76.252', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-02 02:50:19'),
(1712, 3, 'ADMIN_USER_PASSWORD_RESET', 'Admin reset password for user: Ivanawonderwoman@outlook.com (ID: 117)', '102.89.76.252', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-02 02:51:06'),
(1713, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user Ivanawonderwoman@outlook.com (ID: 117)', '102.89.76.252', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-02 02:52:18'),
(1717, 60, 'LOGIN', 'User logged in', '197.210.54.239', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-03-02 03:01:00'),
(1718, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user officialaishagaddafi1@gmail.com (ID: 108)', '197.210.54.239', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-03-02 03:01:20'),
(1721, 60, 'LOGIN', 'User logged in', '197.210.54.239', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-03-02 03:10:58'),
(1722, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user gaddafiayeshamaummar@gmail.com (ID: 71)', '197.210.54.239', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-03-02 03:11:10'),
(1725, 3, 'LOGIN', 'User logged in', '102.89.83.65', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-02 20:12:29'),
(1726, 3, 'ADMIN_USER_PASSWORD_RESET', 'Admin reset password for user: Phartman076@outlook.com (ID: 118)', '102.89.83.65', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-02 20:27:26'),
(1727, 3, 'ADMIN_USER_PASSWORD_RESET', 'Admin reset password for user: Phartman076@outlook.com (ID: 119)', '102.89.83.65', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-02 20:41:25'),
(1728, 3, 'LOGOUT', 'User logged out', '102.89.83.65', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-02 20:41:34'),
(1735, 3, 'LOGIN', 'User logged in', '102.89.83.65', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-02 20:49:58'),
(1736, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user Phartman076@outlook.com (ID: 119)', '102.89.83.65', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-02 20:50:16'),
(1750, 60, 'LOGIN', 'User logged in', '105.113.34.23', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_6_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Mobile/15E148 Safari/604.1', '2026-03-06 11:54:29'),
(1751, 60, 'LOGIN', 'User logged in', '105.113.34.23', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-06 11:55:07'),
(1752, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user gaddafiayeshamaummar@gmail.com (ID: 71)', '105.113.34.23', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '2026-03-06 11:55:25'),
(1762, 60, 'LOGIN', 'User logged in', '105.112.213.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-03-15 08:56:42'),
(1764, 60, 'ADMIN_DELETE_TRANSACTION', 'Deleted transaction TXN699E7618C2340 for user officialaishagaddafi1@gmail.com. Reason: invalid', '2605:59c0:ec1:1310:e5c5:d268:2289:7457', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-03-15 09:19:15'),
(1765, 60, 'ADMIN_DELETE_TRANSACTION', 'Deleted transaction REVTXN699E7618C2340 for user officialaishagaddafi1@gmail.com. Reason: invalid', '2605:59c0:ec1:1310:e5c5:d268:2289:7457', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-03-15 09:19:25'),
(1766, 60, 'ADMIN_DELETE_TRANSACTION', 'Deleted transaction TXN69A19B3D55F0C for user officialaishagaddafi1@gmail.com. Reason: invalid', '2605:59c0:ec1:1310:e5c5:d268:2289:7457', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2026-03-15 09:19:43'),
(1768, 3, 'LOGIN', 'User logged in', '102.89.68.205', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-17 19:51:13'),
(1769, 3, 'USER_DELETED', 'Deleted user: allstarjp260@gmail.com (ID: 122)', '102.89.68.205', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-17 19:51:43'),
(1770, 3, 'USER_DELETED', 'Deleted user: smoothpicsstudio@gmail.com (ID: 121)', '102.89.68.205', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-17 19:51:53'),
(1771, 3, 'KYC_AUTO_VERIFIED', 'Auto-verified KYC for user 123 during account creation', '102.89.68.205', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-17 19:57:34'),
(1773, 3, 'ADMIN_UPLOAD_PROFILE_PICTURE', 'Uploaded profile picture for user hille3498@gmail.com (ID: 123)', '102.89.68.205', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-17 19:58:30'),
(1774, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user hille3498@gmail.com (ID: 123)', '102.89.68.205', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-17 19:58:49'),
(1775, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.89.68.205', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-17 19:59:08'),
(1776, 3, 'LOGIN', 'User logged in', '102.89.47.66', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 21:41:45'),
(1777, 3, 'EMAIL_TEST', 'Sent test email (test) to mr.carter.tech07@gmail.com', '102.89.47.66', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 21:55:56'),
(1778, 3, 'ADMIN_PASSWORD_UPDATED', 'Updated password for support@cosmopolitantrustbankpf.com', '102.89.47.66', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 21:57:03'),
(1779, 3, 'ADMIN_INFO_UPDATED', 'Updated info for support@cosmopolitantrustbankpf.com to support@saveridgecapital.com', '102.89.47.66', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 21:57:03'),
(1780, 3, 'USER_DELETED', 'Deleted user: mathewtan7319@gmail.com (ID: 120)', '102.89.47.66', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 22:04:20'),
(1781, 3, 'USER_DELETED', 'Deleted user: Phartman076@outlook.com (ID: 119)', '102.89.47.66', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 22:04:25'),
(1782, 3, 'USER_DELETED', 'Deleted user: Ivanawonderwoman@outlook.com (ID: 117)', '102.89.47.66', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 22:04:40'),
(1783, 3, 'USER_DELETED', 'Deleted user: alioly943@gmail.com (ID: 110)', '102.89.47.66', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 22:04:44'),
(1784, 3, 'USER_DELETED', 'Deleted user: aishamuammar87@gmail.com (ID: 109)', '102.89.47.66', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 22:04:48'),
(1785, 3, 'USER_DELETED', 'Deleted user: officialaishagaddafi1@gmail.com (ID: 108)', '102.89.47.66', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 22:04:51'),
(1786, 3, 'USER_DELETED', 'Deleted user: justnah340@gmail.com (ID: 107)', '102.89.47.66', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 22:05:00'),
(1787, 3, 'USER_DELETED', 'Deleted user: annbrick40@gmail.com (ID: 106)', '102.89.47.66', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 22:05:05'),
(1788, 3, 'USER_DELETED', 'Deleted user: gaddafiayeshamaummar@gmail.com (ID: 71)', '102.89.47.66', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 22:05:09'),
(1789, 3, 'USER_DELETED', 'Deleted user: jadejordan6040@gmail.com (ID: 46)', '102.89.47.66', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 22:05:32'),
(1790, 3, 'USER_DELETED', 'Deleted user: hille3498@gmail.com (ID: 123)', '102.89.47.66', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 22:05:38'),
(1791, 60, 'LOGIN', 'User logged in', '102.90.82.44', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-18 23:33:51'),
(1792, 60, 'LOGOUT', 'User logged out', '102.90.82.44', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-18 23:41:55'),
(1793, 60, 'LOGIN', 'User logged in', '102.90.82.44', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-18 23:44:42'),
(1794, 60, 'USER_DELETED', 'Deleted user: billyfredrickgibbons@gmail.com (ID: 37)', '102.90.82.44', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-18 23:44:57'),
(1795, 60, 'USER_DELETED', 'Deleted user: hkr.fred@outlook.com (ID: 27)', '102.90.82.44', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-18 23:45:01'),
(1796, 60, 'KYC_AUTO_VERIFIED', 'Auto-verified KYC for user 124 during account creation', '102.90.82.44', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-18 23:48:46'),
(1797, 60, 'LOGOUT', 'User logged out', '102.90.82.44', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-18 23:51:35'),
(1804, 60, 'LOGIN', 'User logged in', '102.90.82.44', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-18 23:56:03'),
(1805, 60, 'USER_UPLOAD_PROFILE_PICTURE', 'Updated own profile picture', '102.90.82.44', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-18 23:58:35'),
(1806, 60, 'LOGOUT', 'User logged out', '102.90.82.44', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-18 23:59:18'),
(1807, 60, 'LOGIN', 'User logged in', '102.90.82.44', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-18 23:59:26'),
(1808, 60, 'ADMIN_SET_TRANSACTION_MODE', 'Set transaction mode to \'force_success\' for user alexwanghengry@gmail.com (ID: 124)', '102.90.82.44', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-19 00:01:42'),
(1809, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user alexwanghengry@gmail.com (ID: 124)', '102.90.82.44', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-19 00:21:44'),
(1822, 60, 'LOGIN', 'User logged in', '102.90.82.44', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-19 00:36:20'),
(1823, 60, 'LOGOUT', 'User logged out', '102.90.82.44', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-19 00:36:36'),
(1824, 60, 'LOGIN', 'User logged in', '102.90.82.44', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-19 00:36:45'),
(1825, 60, 'LOGOUT', 'User logged out', '102.90.82.44', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-19 00:36:51'),
(1826, 60, 'LOGIN', 'User logged in', '102.90.82.44', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-19 00:37:05'),
(1827, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user chukwukap19@gmail.com (ID: 125)', '102.90.82.44', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-19 00:40:01'),
(1828, 60, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.90.82.44', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-19 00:41:31'),
(1829, 60, 'ADMIN_TOGGLE_2FA', 'Admin support@saveridgecapital.com enabled two-factor authentication for user chukwukap19@gmail.com', '102.90.82.44', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-19 00:41:55'),
(1830, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user chukwukap19@gmail.com (ID: 125)', '102.90.82.44', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-19 00:42:43'),
(1838, 60, 'LOGIN', 'User logged in', '102.90.82.44', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-19 01:04:07'),
(1839, 60, 'LOGIN', 'User logged in', '102.90.102.32', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-19 02:03:24'),
(1840, 60, 'KYC_AUTO_VERIFIED', 'Auto-verified KYC for user 126 during account creation', '102.90.82.44', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-19 02:23:23'),
(1842, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user alexwanghenry@gmail.com (ID: 126)', '102.90.82.44', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-19 02:25:16'),
(1846, 60, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.90.82.44', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-19 02:27:22'),
(1847, 60, 'ADMIN_TOGGLE_2FA', 'Admin support@saveridgecapital.com enabled two-factor authentication for user alexwanghenry@gmail.com', '102.90.82.44', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-19 02:27:43'),
(1848, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user alexwanghenry@gmail.com (ID: 126)', '102.90.82.44', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-19 02:27:51'),
(1854, 60, 'LOGIN', 'User logged in', '102.90.82.44', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-19 02:44:24'),
(1857, 60, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction TXN69BB613476C4C for user alexwanghenry@gmail.com. Amount changed from 3015 to 3015. Date changed from 2026-03-19 02:36:36 to 2024-03-09 23:36:00', '102.90.82.44', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-19 02:56:18'),
(1858, 60, 'ADMIN_UPLOAD_PROFILE_PICTURE', 'Uploaded profile picture for user alexwanghenry@gmail.com (ID: 126)', '102.90.82.44', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-19 02:57:12'),
(1861, 60, 'LOGIN', 'User logged in', '102.90.96.129', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-19 08:52:21'),
(1862, 60, 'LOGIN', 'User logged in', '102.89.83.251', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-19 09:03:26'),
(1864, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user alexwanghenry@gmail.com (ID: 126)', '102.89.83.251', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-19 09:05:39'),
(1866, 60, 'LOGIN', 'User logged in', '102.89.83.251', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-19 09:06:30'),
(1867, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user alexwanghenry@gmail.com (ID: 126)', '102.89.83.251', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-19 09:07:16'),
(1868, 60, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.89.83.251', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-19 09:12:11'),
(1869, 60, 'LOGIN', 'User logged in', '102.89.83.251', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-19 09:46:51'),
(1870, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user alexwanghenry@gmail.com (ID: 126)', '102.89.83.251', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-19 09:56:14'),
(1871, 60, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.89.83.251', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-19 09:56:56'),
(1872, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user alexwanghenry@gmail.com (ID: 126)', '102.89.83.251', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-19 09:57:15'),
(1875, 60, 'LOGIN', 'User logged in', '102.90.102.32', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-19 10:22:10'),
(1876, 60, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction TXN69BBCDC030472 for user alexwanghenry@gmail.com. Amount changed from 2010 to 2010. Date changed from 2026-03-19 10:19:44 to 2024-01-19 11:45:00', '102.90.102.32', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-19 10:24:51'),
(1877, 60, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction TXN69BB4B19B5369 for user chukwukap19@gmail.com. Amount changed from 3075 to 3075. Date changed from 2026-03-19 01:02:17 to 2024-08-19 03:02:00', '102.90.102.32', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-19 10:25:30'),
(1878, 60, 'ADMIN_DELETE_TRANSACTION', 'Deleted transaction TXN69BBCDC030472 for user alexwanghenry@gmail.com. Reason: Not needed', '102.90.102.32', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-19 10:37:57'),
(1879, 60, 'ADMIN_DELETE_TRANSACTION', 'Deleted transaction TXN69BB613476C4C for user alexwanghenry@gmail.com. Reason: Not needed', '102.90.102.32', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-19 10:38:17'),
(1880, 60, 'ADMIN_DELETE_TRANSACTION', 'Deleted transaction TXN69BBB6638D46F for user alexwanghenry@gmail.com. Reason: Not needed', '102.90.102.32', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-19 10:38:29'),
(1889, 60, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction TXN69BBDFEE614E7 for user alexwanghenry@gmail.com. Amount changed from 2512.5 to 2512.5. Status changed from pending to completed. Date changed from 2026-03-19 11:37:18 to 2026-03-19 11:37:00', '102.90.102.32', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-19 11:40:23'),
(1890, 60, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction TXN69BBDFEE614E7 for user alexwanghenry@gmail.com. Amount changed from 2512.5 to 2512.5. Date changed from 2026-03-19 11:37:00 to 2026-02-10 18:02:00', '102.90.102.32', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-19 11:41:23'),
(1894, 60, 'LOGIN', 'User logged in', '197.210.227.226', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-19 13:06:24'),
(1901, 60, 'LOGIN', 'User logged in', '102.88.110.174', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Cursor/2.6.19 Chrome/142.0.7444.265 Electron/39.4.0 Safari/537.36', '2026-03-19 17:06:18'),
(1902, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user alexwanghenry@gmail.com (ID: 126)', '102.88.110.174', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Cursor/2.6.19 Chrome/142.0.7444.265 Electron/39.4.0 Safari/537.36', '2026-03-19 17:06:49'),
(1903, 60, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.88.110.174', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Cursor/2.6.19 Chrome/142.0.7444.265 Electron/39.4.0 Safari/537.36', '2026-03-19 17:22:01'),
(1905, 60, 'LOGIN', 'User logged in', '102.89.83.251', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-19 17:44:24'),
(1906, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user alexwanghenry@gmail.com (ID: 126)', '102.89.83.251', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-19 17:44:35'),
(1907, 60, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.89.83.251', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-19 17:45:15'),
(1908, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user alexwanghenry@gmail.com (ID: 126)', '102.89.83.251', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-19 17:46:31'),
(1909, 60, 'LOGIN', 'User logged in', '102.88.115.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-19 23:48:23'),
(1910, 60, 'LOGOUT', 'User logged out', '102.88.115.94', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-19 23:49:24'),
(1921, 60, 'LOGIN', 'User logged in', '102.89.43.160', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-20 22:00:45'),
(1923, 60, 'LOGIN', 'User logged in', '102.90.42.173', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-20 22:58:01'),
(1924, 60, 'LOGOUT', 'User logged out', '102.90.42.173', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-20 22:58:16'),
(1925, 60, 'LOGIN', 'User logged in', '102.90.42.173', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-20 22:58:31'),
(1941, 60, 'LOGIN', 'User logged in', '102.90.42.173', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-21 09:45:28'),
(1946, 60, 'LOGIN', 'User logged in', '197.210.54.228', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-21 16:58:51'),
(1947, 60, 'LOGOUT', 'User logged out', '197.210.54.228', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-21 16:58:57'),
(1948, 60, 'LOGIN', 'User logged in', '197.210.54.228', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-21 16:59:04'),
(1949, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user alexwanghenry@gmail.com (ID: 126)', '197.210.54.228', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-21 16:59:20'),
(1950, 60, 'LOGIN', 'User logged in', '197.210.54.228', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-21 17:19:44'),
(1951, 60, 'LOGOUT', 'User logged out', '197.210.54.228', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-21 17:19:54'),
(1952, 60, 'LOGIN', 'User logged in', '197.210.54.228', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-21 17:19:59'),
(1953, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user chukwukap19@gmail.com (ID: 125)', '197.210.54.228', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-21 17:22:54'),
(1957, 60, 'LOGIN', 'User logged in', '197.210.54.228', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-21 17:30:07'),
(1958, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user alexwanghenry@gmail.com (ID: 126)', '197.210.54.228', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-21 17:36:38'),
(1962, 60, 'LOGIN', 'User logged in', '197.210.54.228', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-21 17:44:46'),
(1964, 60, 'LOGIN', 'User logged in', '105.113.65.22', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-21 19:18:45'),
(1976, 60, 'LOGIN', 'User logged in', '102.90.96.145', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-24 14:02:02'),
(1977, 60, 'LOGOUT', 'User logged out', '102.90.96.145', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-24 14:02:09'),
(1978, 60, 'LOGIN', 'User logged in', '102.90.96.145', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-24 14:02:14'),
(1985, 60, 'LOGIN', 'User logged in', '129.205.124.209', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 20:38:51'),
(1987, 60, 'LOGIN', 'User logged in', '146.70.246.136', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-25 23:32:38'),
(1988, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user alexwanghenry@gmail.com (ID: 126)', '146.70.246.136', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-25 23:32:46'),
(1991, 60, 'LOGIN', 'User logged in', '102.89.46.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 03:07:21'),
(1992, 60, 'LOGOUT', 'User logged out', '102.89.46.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 03:07:42'),
(1993, 60, 'LOGIN', 'User logged in', '102.89.46.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 03:08:12'),
(1994, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user alexwanghenry@gmail.com (ID: 126)', '102.89.46.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 03:08:21'),
(1995, 60, 'LOGIN', 'User logged in', '102.89.46.172', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 10:44:35'),
(1996, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user alexwanghenry@gmail.com (ID: 126)', '102.89.46.172', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 10:44:58'),
(1997, 60, 'LOGIN', 'User logged in', '102.89.47.175', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 11:18:45'),
(1998, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user alexwanghenry@gmail.com (ID: 126)', '102.89.47.175', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 11:18:57'),
(1999, 60, 'LOGIN', 'User logged in', '102.90.99.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-28 12:55:11'),
(2000, 60, 'ADMIN_PASSWORD_UPDATED', 'Updated password for support@saveridgecapital.com', '102.90.99.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-28 12:56:37'),
(2001, 60, 'LOGOUT', 'User logged out', '102.90.99.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-28 12:56:52'),
(2002, 60, 'LOGIN', 'User logged in', '102.90.99.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.1 Mobile/15E148 Safari/604.1', '2026-03-28 12:57:02'),
(2003, 3, 'LOGIN', 'User logged in', '102.89.47.175', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 19:01:35'),
(2004, 3, 'KYC_AUTO_VERIFIED', 'Auto-verified KYC for user 127 during account creation', '102.89.47.175', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 19:21:06'),
(2018, 3, 'LOGIN', 'User logged in', '31.14.252.10', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-13 02:45:54'),
(2019, 3, 'EMAIL_TEST', 'Sent test email (test) to mr.carter.tech07@gmail.com', '31.14.252.10', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-13 02:48:33'),
(2020, 3, 'USER_DELETED', 'Deleted user: andyjoycemaris@gmail.com (ID: 127)', '31.14.252.10', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-13 02:48:43'),
(2021, 3, 'USER_DELETED', 'Deleted user: alexwanghenry@gmail.com (ID: 126)', '31.14.252.10', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-13 02:48:47'),
(2022, 3, 'USER_DELETED', 'Deleted user: chukwukap18@gmail.com (ID: 125)', '31.14.252.10', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-13 02:48:51'),
(2023, 3, 'USER_DELETED', 'Deleted user: alexwanghengry@gmail.com (ID: 124)', '31.14.252.10', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-13 02:48:55'),
(2024, 3, 'KYC_AUTO_VERIFIED', 'Auto-verified KYC for user 128 during account creation', '31.14.252.10', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-13 02:54:13'),
(2026, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ybo5758@gmail (ID: 128)', '31.14.252.10', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-13 02:54:32'),
(2034, 3, 'LOGIN', 'User logged in', '31.14.252.10', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-13 02:56:52'),
(2035, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ybo5758@gmail (ID: 128)', '31.14.252.10', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-13 02:57:23'),
(2037, 3, 'LOGIN', 'User logged in', '105.113.67.3', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-13 15:40:18'),
(2043, 3, 'LOGIN', 'User logged in', '102.89.23.23', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 10:52:49'),
(2044, 3, 'KYC_AUTO_VERIFIED', 'Auto-verified KYC for user 129 during account creation', '102.89.23.23', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 10:53:41'),
(2046, 3, 'EMAIL_TEST', 'Sent test email (test) to mr.carter.tech07@gmail.com', '102.89.23.23', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 10:54:10'),
(2048, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user mr.carter.tech07@gmail.com (ID: 129)', '102.89.23.23', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22 10:59:39'),
(2051, 3, 'LOGIN', 'User logged in', '102.88.108.255', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 10:04:46'),
(2052, 3, 'EMAIL_TEST', 'Sent test email (test) to mr.carter.tech07@gmail.com', '102.88.108.255', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 10:05:15'),
(2053, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user mr.carter.tech07@gmail.com (ID: 129)', '102.88.108.255', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-30 10:05:52'),
(2062, 3, 'LOGIN', 'User logged in', '102.89.83.31', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-16 19:19:36'),
(2063, 3, 'LOGIN', 'User logged in', '102.89.76.10', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 23:40:47'),
(2064, 3, 'USER_DELETED', 'Deleted user: williamsjohnson277533@gmail.com (ID: 128)', '102.89.76.10', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 23:41:04'),
(2065, 3, 'USER_DELETED', 'Deleted user: mingxayuen@gmail.com (ID: 130)', '102.89.76.10', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-18 23:41:09'),
(2067, 3, 'LOGIN', 'User logged in', '102.88.108.48', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-19 11:51:20'),
(2068, 3, 'ADMIN_TOGGLE_IMF', 'Set imf_required=1 for user hkr.fred@outlook.com (ID: 131)', '102.88.108.48', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-19 11:53:07'),
(2069, 3, 'LOGIN', 'User logged in', '102.88.108.48', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-19 15:31:14'),
(2070, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user hkr.fred@outlook.com (ID: 131)', '102.88.108.48', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-19 15:38:55'),
(2075, 3, 'LOGIN', 'User logged in', '102.88.108.48', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-19 16:24:59'),
(2076, 3, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction ADM20260519123318667 for user hkr.fred@outlook.com. Amount changed from 4600 to 4600. Status changed from completed to failed. Date changed from 2026-05-20 17:28:00 to 2026-05-20 16:28:00', '102.88.108.48', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-19 16:33:36'),
(2077, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user hkr.fred@outlook.com (ID: 131)', '102.88.108.48', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-19 16:37:23'),
(2085, 3, 'LOGIN', 'User logged in', '102.88.115.173', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-28 21:27:52'),
(2086, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user waltazite@gmail.com (ID: 132)', '102.88.115.173', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-28 21:55:42'),
(2088, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.88.115.173', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-28 22:07:27'),
(2089, 3, 'ADMIN_TOGGLE_IMF', 'Set imf_required=1 for user waltazite@gmail.com (ID: 132)', '102.88.115.173', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-28 22:09:33'),
(2090, 3, 'ADMIN_TOGGLE_FEDERAL_SWIFT', 'Set federal_swift_required=1 for user waltazite@gmail.com (ID: 132)', '102.88.115.173', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-28 22:09:39'),
(2091, 3, 'ADMIN_TOGGLE_VAT', 'Set vat_required=1 for user waltazite@gmail.com (ID: 132)', '102.88.115.173', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-28 22:09:42'),
(2092, 3, 'ADMIN_TOGGLE_TAC', 'Set tac_required=1 for user waltazite@gmail.com (ID: 132)', '102.88.115.173', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-28 22:09:45'),
(2093, 3, 'ADMIN_TOGGLE_TIN', 'Set tin_required=1 for user waltazite@gmail.com (ID: 132)', '102.88.115.173', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-28 22:09:48'),
(2094, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user waltazite@gmail.com (ID: 132)', '102.88.115.173', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-28 22:10:06'),
(2095, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.88.115.173', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-28 22:20:39'),
(2096, 3, 'LOGIN', 'User logged in', '102.89.83.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-30 22:07:09'),
(2097, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user mr.carter.tech07@gmail.com (ID: 129)', '102.89.83.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-30 22:07:23'),
(2099, 3, 'LOGIN', 'User logged in', '102.89.83.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-30 22:09:14'),
(2100, 3, 'USER_DELETED', 'Deleted user: mr.carter.tech07@gmail.com (ID: 129)', '102.89.83.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-30 22:09:23'),
(2101, 3, 'LOGOUT', 'User logged out', '102.89.83.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-30 22:09:32'),
(2110, 3, 'LOGIN', 'User logged in', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-30 22:40:11'),
(2111, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user mr.carter.tech07@gmail.com (ID: 133)', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-30 22:41:26'),
(2113, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-30 22:47:28'),
(2114, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user mr.carter.tech07@gmail.com (ID: 133)', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-30 22:48:16'),
(2115, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-30 22:49:11'),
(2116, 3, 'KYC_APPROVED', 'Approved KYC ID: 35', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-30 22:49:45'),
(2117, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user mr.carter.tech07@gmail.com (ID: 133)', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-30 22:50:19'),
(2118, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-30 22:52:44'),
(2119, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user mr.carter.tech07@gmail.com (ID: 133)', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-30 22:53:52'),
(2122, 3, 'LOGIN', 'User logged in', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-30 23:06:38'),
(2123, 3, 'LOGIN', 'User logged in', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 01:19:23'),
(2124, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user mr.carter.tech07@gmail.com (ID: 133)', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 01:19:53'),
(2125, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 01:20:44'),
(2126, 3, 'USER_DELETED', 'Deleted user: mr.carter.tech07@gmail.com (ID: 133)', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 01:20:50'),
(2127, 3, 'LOGOUT', 'User logged out', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 01:21:00'),
(2128, 134, 'ACCOUNT_CREATED', 'Created checking account: 202642569179', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 01:22:17'),
(2129, 134, 'LOGIN', 'User session established', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 01:23:18'),
(2130, 134, 'LOGIN_PIN_UPDATED', 'User updated their login PIN', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 01:23:37');
INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `details`, `ip_address`, `user_agent`, `created_at`) VALUES
(2131, 134, 'TRANSFER_PIN_UPDATED', 'User updated their transfer PIN', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 01:23:49'),
(2132, 134, 'TWO_FACTOR_ENABLED', 'User enabled two-factor authentication', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 01:23:54'),
(2133, 134, 'LOGOUT', 'User logged out', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 01:25:44'),
(2134, 3, 'LOGIN', 'User logged in', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 01:26:05'),
(2135, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user mr.carter.tech07@gmail.com (ID: 134)', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 01:26:45'),
(2136, 134, 'KYC_SUBMITTED', 'User submitted KYC verification', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 01:41:31'),
(2137, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 01:41:48'),
(2138, 3, 'KYC_APPROVED', 'Approved KYC ID: 36', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 01:42:16'),
(2139, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user mr.carter.tech07@gmail.com (ID: 134)', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 01:42:33'),
(2140, 134, 'transfer_funds', 'Transferred $233.00 to werty mum (Fee: $1.17)', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 01:43:40'),
(2141, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 01:45:05'),
(2142, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user mr.carter.tech07@gmail.com (ID: 134)', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 01:46:07'),
(2143, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 01:47:51'),
(2144, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user mr.carter.tech07@gmail.com (ID: 134)', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 01:48:14'),
(2145, 3, 'LOGIN', 'User logged in', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 02:46:11'),
(2146, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user mr.carter.tech07@gmail.com (ID: 134)', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 02:46:19'),
(2147, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 02:46:46'),
(2148, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user mr.carter.tech07@gmail.com (ID: 134)', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 02:47:13'),
(2149, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 02:47:50'),
(2150, 3, 'LOGOUT', 'User logged out', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 02:47:55'),
(2151, 3, 'LOGIN', 'User logged in', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 02:53:50'),
(2152, 3, 'LOGIN', 'User logged in', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 03:10:46'),
(2153, 3, 'USER_DELETED', 'Deleted user: hkr.fred@outlook.com (ID: 131)', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 03:11:02'),
(2154, 3, 'USER_DELETED', 'Deleted user: waltazite@gmail.com (ID: 132)', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 03:11:06'),
(2155, 3, 'ADMIN_PASSWORD_UPDATED', 'Updated password for support@saveridgecapital.com', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 03:14:07'),
(2156, 3, 'ADMIN_INFO_UPDATED', 'Updated info for support@saveridgecapital.com to support@firstnationalfn.com', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 03:14:07'),
(2157, 3, 'EMAIL_TEST', 'Sent test email (test) to mr.carter.tech07@gmail.com', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 03:14:44'),
(2158, 3, 'LOGOUT', 'User logged out', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 03:15:21'),
(2159, 60, 'LOGIN', 'User logged in', '102.90.98.168', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '2026-05-31 22:16:18'),
(2160, 135, 'ACCOUNT_CREATED', 'Created savings account: 202646663507', '102.90.98.168', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Mobile Safari/537.36', '2026-05-31 22:38:34'),
(2161, 135, 'LOGIN', 'User session established', '102.90.98.168', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Mobile Safari/537.36', '2026-05-31 22:39:00'),
(2162, 135, 'LOGIN_PIN_UPDATED', 'User updated their login PIN', '102.90.98.168', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Mobile Safari/537.36', '2026-05-31 22:39:19'),
(2163, 135, 'TRANSFER_PIN_UPDATED', 'User updated their transfer PIN', '102.90.98.168', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Mobile Safari/537.36', '2026-05-31 22:39:29'),
(2164, 135, 'TWO_FACTOR_ENABLED', 'User enabled two-factor authentication', '102.90.98.168', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Mobile Safari/537.36', '2026-05-31 22:44:50'),
(2165, 60, 'LOGIN', 'User logged in', '102.90.98.168', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 22:57:52'),
(2166, 60, 'bank_added', 'Added bank: First Abu Dhabi Bank (FAB)', '102.90.98.168', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 23:29:34'),
(2167, 60, 'bank_added', 'Added bank: Emirates NBD', '102.90.98.168', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 23:30:05'),
(2168, 60, 'bank_deleted', 'Deleted bank ID: 276', '102.90.98.168', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 23:30:44'),
(2169, 60, 'bank_deleted', 'Deleted bank ID: 222', '102.90.98.168', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 23:31:13'),
(2170, 60, 'bank_added', 'Added bank: Commercial Bank of Dubai (CBD)', '102.90.98.168', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 23:32:49'),
(2171, 60, 'bank_added', 'Added bank: RAKBANK', '102.90.98.168', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 23:34:02'),
(2172, 60, 'bank_added', 'Added bank: HSBC Bank Middle East', '102.90.98.168', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 23:34:45'),
(2173, 60, 'bank_added', 'Added bank: Sharjah Islamic Bank', '102.90.98.168', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 23:35:19'),
(2174, 60, 'bank_added', 'Added bank: United Arab Bank (UAB)', '102.90.98.168', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 23:35:56'),
(2175, 60, 'bank_added', 'Added bank: Abu Dhabi Islamic Bank (ADIB)', '102.90.98.168', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 23:37:57'),
(2176, 60, 'bank_added', 'Added bank: Al Hilal Bank', '102.90.98.168', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 23:39:01'),
(2177, 60, 'bank_added', 'Added bank: Al Maryah Community Bank', '102.90.98.168', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 23:39:59'),
(2178, 60, 'bank_added', 'Added bank: Emirates Islamic', '102.90.98.168', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 23:41:13'),
(2179, 60, 'bank_added', 'Added bank: Ajman Bank', '102.90.98.168', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 23:42:30'),
(2180, 60, 'LOGIN', 'User logged in', '102.89.84.194', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 17:11:57'),
(2181, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user mr.carter.tech07@gmail.com (ID: 134)', '102.89.84.194', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 17:12:28'),
(2182, 134, 'LOGOUT', 'User logged out', '102.89.84.194', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 19:35:32'),
(2183, 60, 'LOGIN', 'User logged in', '102.89.84.194', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 19:35:41'),
(2184, 60, 'LOGOUT', 'User logged out', '102.89.84.194', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 19:40:04'),
(2185, 60, 'LOGIN', 'User logged in', '102.90.118.131', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 22:20:09'),
(2186, 135, 'LOGIN', 'User logged in', '102.90.118.131', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-06-02 22:21:20'),
(2187, 135, 'LOGOUT', 'User logged out', '102.90.118.131', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-06-02 22:25:44'),
(2188, 135, 'LOGIN', 'User logged in', '102.90.118.131', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-06-02 22:27:43'),
(2189, 60, 'LOGIN', 'User logged in', '102.88.110.54', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 22:42:23'),
(2190, 60, 'ADMIN_LOGIN_AS_USER', 'Logged in as user mr.carter.tech07@gmail.com (ID: 134)', '102.88.110.54', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 22:42:32'),
(2191, 60, 'LOGIN', 'User logged in', '102.90.118.131', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 23:11:50'),
(2192, 135, 'LOGIN', 'User logged in', '102.90.118.131', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-06-02 23:12:34');

-- --------------------------------------------------------

--
-- Table structure for table `admin_audit_logs`
--

CREATE TABLE `admin_audit_logs` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_logs`
--

CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_logs`
--

INSERT INTO `admin_logs` (`id`, `admin_id`, `user_id`, `action`, `description`, `metadata`, `ip_address`, `created_at`) VALUES
(248, 3, 131, 'balance_adjustment', 'Created credit transaction of USD 4230000 for user hkr.fred@outlook.com (ID: 131) - Status: completed', NULL, NULL, '2026-05-19 15:38:40'),
(249, 3, 131, 'balance_adjustment', 'Created debit transaction of USD 4600 for user hkr.fred@outlook.com (ID: 131) - Status: completed', NULL, NULL, '2026-05-19 16:33:18'),
(250, 3, 131, 'balance_adjustment', 'Created debit transaction of USD 35700 for user hkr.fred@outlook.com (ID: 131) - Status: completed', NULL, NULL, '2026-05-19 16:35:39'),
(251, 3, 131, 'balance_adjustment', 'Created debit transaction of USD 8492 for user hkr.fred@outlook.com (ID: 131) - Status: completed', NULL, NULL, '2026-05-19 16:37:13'),
(252, 3, 132, 'balance_adjustment', 'Created credit transaction of USD 500900 for user waltazite@gmail.com (ID: 132) - Status: completed', NULL, NULL, '2026-05-28 21:28:39'),
(253, 3, 132, 'status_change', 'Changed user status from \'pending\' to \'active\'', NULL, NULL, '2026-05-28 21:28:58'),
(254, 3, 132, 'kyc_status_change', 'Changed KYC status from \'pending\' to \'verified\'', NULL, NULL, '2026-05-28 21:55:26'),
(255, 3, 133, 'balance_adjustment', 'Created credit transaction of CAD 500 for user mr.carter.tech07@gmail.com (ID: 133) - Status: completed', NULL, NULL, '2026-05-30 22:53:21'),
(256, 3, 134, 'balance_adjustment', 'Created credit transaction of CAD 600 for user mr.carter.tech07@gmail.com (ID: 134) - Status: completed', NULL, NULL, '2026-05-31 01:26:37'),
(257, 60, 135, 'balance_adjustment', 'Created credit transaction of CAD 20000 for user simplyhiredremotejobs@gmail.com (ID: 135) - Status: completed', NULL, NULL, '2026-05-31 23:01:17');

-- --------------------------------------------------------

--
-- Table structure for table `admin_sessions`
--

CREATE TABLE `admin_sessions` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `session_token` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `last_activity` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `banks`
--

CREATE TABLE `banks` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(10) DEFAULT NULL,
  `region` varchar(50) NOT NULL,
  `country` varchar(100) NOT NULL,
  `swift_code` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `banks`
--

INSERT INTO `banks` (`id`, `name`, `code`, `region`, `country`, `swift_code`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'JPMorgan Chase Bank', 'JPM', 'north-america', 'United States', 'CHASUS33', 1, NULL, '2025-01-15 10:00:00', '2025-01-15 10:00:00'),
(2, 'Bank of America', 'BAC', 'north-america', 'United States', 'BOFAUS3N', 1, NULL, '2025-01-15 10:00:00', '2025-01-15 10:00:00'),
(3, 'Wells Fargo Bank', 'WFC', 'north-america', 'United States', 'WFBIUS6S', 1, NULL, '2025-01-15 10:00:00', '2025-01-15 10:00:00'),
(4, 'Citibank', 'C', 'north-america', 'United States', 'CITIUS33', 1, NULL, '2025-01-15 10:00:00', '2025-01-15 10:00:00'),
(5, 'U.S. Bank', 'USB', 'north-america', 'United States', 'USBKUS44', 1, NULL, '2025-01-15 10:00:00', '2025-01-15 10:00:00'),
(6, 'PNC Bank', 'PNC', 'north-america', 'United States', 'PNCCUS33', 1, NULL, '2025-01-15 10:00:00', '2025-01-15 10:00:00'),
(7, 'TD Bank', 'TD', 'north-america', 'United States', 'NRTHUS33', 1, NULL, '2025-01-15 10:00:00', '2025-01-15 10:00:00'),
(8, 'Capital One Bank', 'COF', 'north-america', 'United States', 'NFBKUS33', 1, NULL, '2025-01-15 10:00:00', '2025-01-15 10:00:00'),
(9, 'Goldman Sachs Bank', 'GS', 'north-america', 'United States', 'GSCMUS33', 1, NULL, '2025-01-15 10:00:00', '2025-01-15 10:00:00'),
(10, 'Morgan Stanley Bank', 'MS', 'north-america', 'United States', 'MSINUS33', 1, NULL, '2025-01-15 10:00:00', '2025-01-15 10:00:00'),
(101, 'JPMorgan Chase Bank', 'JPM', 'north-america', 'United States', 'CHASUS33', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(102, 'Bank of America', 'BAC', 'north-america', 'United States', 'BOFAUS3N', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(103, 'Wells Fargo Bank', 'WFC', 'north-america', 'United States', 'WFBIUS6S', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(104, 'Citibank', 'C', 'north-america', 'United States', 'CITIUS33', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(105, 'Goldman Sachs Bank', 'GS', 'north-america', 'United States', 'GOLDUS33', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(106, 'Morgan Stanley Bank', 'MS', 'north-america', 'United States', 'MRMDUS33', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(107, 'US Bank', 'USB', 'north-america', 'United States', 'USBKUS44', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(108, 'PNC Bank', 'PNC', 'north-america', 'United States', 'PNCCUS33', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(109, 'TD Bank', 'TD', 'north-america', 'United States', 'TDOMUS33', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(110, 'Capital One Bank', 'COF', 'north-america', 'United States', 'HIBKUS44', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(111, 'Royal Bank of Canada', 'RBC', 'north-america', 'Canada', 'ROYCCAT2', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(112, 'Toronto-Dominion Bank', 'TD', 'north-america', 'Canada', 'TDOMCATT', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(113, 'Bank of Nova Scotia', 'BNS', 'north-america', 'Canada', 'NOSCCATT', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(114, 'Canadian Imperial Bank', 'CM', 'north-america', 'Canada', 'CIBCCATT', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(115, 'Bank of Montreal', 'BMO', 'north-america', 'Canada', 'BOFMCAT2', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(116, 'Banco Nacional de México', 'BANAMEX', 'north-america', 'Mexico', 'BNMXMXMM', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(117, 'Banco Santander México', 'SANMEX', 'north-america', 'Mexico', 'BSMXMXMM', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(118, 'BBVA Bancomer', 'BBVA', 'north-america', 'Mexico', 'BCMRMXMM', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(119, 'HSBC México', 'HSBC', 'north-america', 'Mexico', 'HSBCMXMM', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(120, 'Banco Inbursa', 'INBURSA', 'north-america', 'Mexico', 'INBUMXMM', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(121, 'Banco do Brasil', 'BBAS', 'south-america', 'Brazil', 'BRASBRRJ', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(122, 'Itaú Unibanco', 'ITUB', 'south-america', 'Brazil', 'ITAUUSSP', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(123, 'Banco Bradesco', 'BBD', 'south-america', 'Brazil', 'BRADBRSP', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(124, 'Banco Santander Brasil', 'SANB11', 'south-america', 'Brazil', 'BSCHBRSP', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(125, 'Caixa Econômica Federal', 'CEF', 'south-america', 'Brazil', 'CEFABRSP', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(126, 'Banco de la Nación Argentina', 'BNA', 'south-america', 'Argentina', 'NACNARBA', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(127, 'Banco Santander Río', 'BSR', 'south-america', 'Argentina', 'BSRIARBA', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(128, 'Banco Galicia', 'GGAL', 'south-america', 'Argentina', 'GALIARBA', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(129, 'BBVA Argentina', 'BBAR', 'south-america', 'Argentina', 'BBVAARBA', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(130, 'Banco Macro', 'BMA', 'south-america', 'Argentina', 'MACRARBA', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(131, 'Bancolombia', 'CIB', 'south-america', 'Colombia', 'COLOCOBM', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(132, 'Banco de Bogotá', 'BOGOTA', 'south-america', 'Colombia', 'BOGOCOBM', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(133, 'Banco Davivienda', 'DAVIVIENDA', 'south-america', 'Colombia', 'DAVICOBA', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(134, 'Banco de Occidente', 'OCCIDENTE', 'south-america', 'Colombia', 'OCCDCOBM', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(135, 'Banco Popular', 'POPULAR', 'south-america', 'Colombia', 'POPUCOBM', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(136, 'Banco de Chile', 'BCHILE', 'south-america', 'Chile', 'BCHICLRM', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(137, 'Banco Santander Chile', 'BSANTANDER', 'south-america', 'Chile', 'BSCHCLRM', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(138, 'Banco de Crédito del Perú', 'BCP', 'south-america', 'Peru', 'BCPLPEPL', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(139, 'Banco de la Nación del Perú', 'BNP', 'south-america', 'Peru', 'NACNPEPL', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(140, 'Banco de Venezuela', 'BDV', 'south-america', 'Venezuela', 'BDVEVECA', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(141, 'HSBC Bank UK', 'HSBC', 'europe', 'United Kingdom', 'HBUKGB4L', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(142, 'Barclays Bank', 'BARC', 'europe', 'United Kingdom', 'BARCGB22', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(143, 'Lloyds Bank', 'LLOYDS', 'europe', 'United Kingdom', 'LOYDGB2L', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(144, 'NatWest Bank', 'NWB', 'europe', 'United Kingdom', 'NWBKGB2L', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(145, 'Royal Bank of Scotland', 'RBS', 'europe', 'United Kingdom', 'RBOSGB2L', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(146, 'Deutsche Bank', 'DB', 'europe', 'Germany', 'DEUTDEFF', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(147, 'Commerzbank', 'CBK', 'europe', 'Germany', 'COBADEFF', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(148, 'DZ Bank', 'DZBANK', 'europe', 'Germany', 'GENODEFF', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(149, 'KfW Bank', 'KFW', 'europe', 'Germany', 'KFWIDEFF', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(150, 'UniCredit Bank', 'UCG', 'europe', 'Germany', 'UNCRDEFF', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(151, 'BNP Paribas', 'BNP', 'europe', 'France', 'BNPAFRPP', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(152, 'Crédit Agricole', 'ACA', 'europe', 'France', 'AGRIFRPP', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(153, 'Société Générale', 'GLE', 'europe', 'France', 'SOGEFRPP', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(154, 'Banco Santander', 'SAN', 'europe', 'Spain', 'BSCHESMM', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(155, 'BBVA', 'BBVA', 'europe', 'Spain', 'BBVAESMM', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(156, 'CaixaBank', 'CABK', 'europe', 'Spain', 'CAIXESBB', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(157, 'Intesa Sanpaolo', 'ISP', 'europe', 'Italy', 'BCITITMM', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(158, 'UniCredit', 'UCG', 'europe', 'Italy', 'UNCRITMM', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(159, 'ING Bank', 'ING', 'europe', 'Netherlands', 'INGBNL2A', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(160, 'Rabobank', 'RABO', 'europe', 'Netherlands', 'RABONL2U', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(161, 'Industrial and Commercial Bank of China', 'ICBC', 'asia', 'China', 'ICBKCNBJ', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(162, 'China Construction Bank', 'CCB', 'asia', 'China', 'PCBCCNBJ', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(163, 'Agricultural Bank of China', 'ABC', 'asia', 'China', 'ABOCCNBJ', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(164, 'Bank of China', 'BOC', 'asia', 'China', 'BKCHCNBJ', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(165, 'Mitsubishi UFJ Financial Group', 'MUFG', 'asia', 'Japan', 'BOTKJPJT', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(166, 'Sumitomo Mitsui Banking Corporation', 'SMBC', 'asia', 'Japan', 'SMBCJPJT', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(167, 'Mizuho Bank', 'MHBK', 'asia', 'Japan', 'MHCBJPJT', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(168, 'State Bank of India', 'SBI', 'asia', 'India', 'SBININBB', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(169, 'HDFC Bank', 'HDFC', 'asia', 'India', 'HDFCINBB', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(170, 'ICICI Bank', 'ICICI', 'asia', 'India', 'ICICINBB', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(171, 'Axis Bank', 'AXIS', 'asia', 'India', 'AXISINBB', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(172, 'Kotak Mahindra Bank', 'KOTAK', 'asia', 'India', 'KKBKINBB', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(173, 'Shinhan Bank', 'SHINHAN', 'asia', 'South Korea', 'SHBKKRSE', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(174, 'KB Kookmin Bank', 'KB', 'asia', 'South Korea', 'CZNBKRSE', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(175, 'Woori Bank', 'WOORI', 'asia', 'South Korea', 'HVBKKRSE', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(176, 'DBS Bank', 'DBS', 'asia', 'Singapore', 'DBSSSGSG', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(177, 'OCBC Bank', 'OCBC', 'asia', 'Singapore', 'OCBCSGSG', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(178, 'United Overseas Bank', 'UOB', 'asia', 'Singapore', 'UOVBSGSG', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(179, 'HSBC Hong Kong', 'HSBC', 'asia', 'Hong Kong', 'HSBCHKHH', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(180, 'Bank of China Hong Kong', 'BOCHK', 'asia', 'Hong Kong', 'BKCHHKHH', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(181, 'Standard Bank of South Africa', 'SBZA', 'africa', 'South Africa', 'SBZAZAJJ', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(182, 'FirstRand Bank', 'FSR', 'africa', 'South Africa', 'FIRNZAJJ', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(183, 'Nedbank', 'NED', 'africa', 'South Africa', 'NEDSZAJJ', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(184, 'Absa Bank', 'ABSA', 'africa', 'South Africa', 'ABSAZAJJ', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(185, 'Investec Bank', 'INV', 'africa', 'South Africa', 'INVEZAJJ', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(186, 'Access Bank', 'ACCESS', 'africa', 'Nigeria', 'ABNGNGLA', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(187, 'Guaranty Trust Bank', 'GTB', 'africa', 'Nigeria', 'GTBINGLA', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(188, 'Zenith Bank', 'ZENITH', 'africa', 'Nigeria', 'ZEIBNGLA', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(189, 'United Bank for Africa', 'UBA', 'africa', 'Nigeria', 'UNAFNGLA', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(190, 'First Bank of Nigeria', 'FBN', 'africa', 'Nigeria', 'FBNINGLA', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(191, 'National Bank of Egypt', 'NBE', 'africa', 'Egypt', 'NBELEGCX', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(192, 'Commercial International Bank', 'CIB', 'africa', 'Egypt', 'CIBKEGCX', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(193, 'Banque Misr', 'BMISR', 'africa', 'Egypt', 'BMISEGCX', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(194, 'Equity Bank', 'EQTY', 'africa', 'Kenya', 'EQBLKENA', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(195, 'Kenya Commercial Bank', 'KCB', 'africa', 'Kenya', 'KCBLKENX', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(196, 'Cooperative Bank of Kenya', 'COOP', 'africa', 'Kenya', 'COOPKENA', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(197, 'Ecobank Ghana', 'ECO', 'africa', 'Ghana', 'ECOEGHAC', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(198, 'Ghana Commercial Bank', 'GCB', 'africa', 'Ghana', 'GCBLGHAC', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(199, 'Attijariwafa Bank', 'AWB', 'africa', 'Morocco', 'BCMAMAMC', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(200, 'Banque Populaire', 'BP', 'africa', 'Morocco', 'BCMAMAMC', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(201, 'Commonwealth Bank of Australia', 'CBA', 'oceania', 'Australia', 'CTBAAU2S', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(202, 'Westpac Banking Corporation', 'WBC', 'oceania', 'Australia', 'WPACAU2S', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(203, 'Australia and New Zealand Banking Group', 'ANZ', 'oceania', 'Australia', 'ANZBAU3M', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(204, 'National Australia Bank', 'NAB', 'oceania', 'Australia', 'NATAAU33', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(205, 'Macquarie Bank', 'MQG', 'oceania', 'Australia', 'MACQAU2S', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(206, 'Suncorp Bank', 'SUN', 'oceania', 'Australia', 'METWAU4B', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(207, 'Bendigo Bank', 'BEN', 'oceania', 'Australia', 'BENDAU21', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(208, 'Bank of Queensland', 'BOQ', 'oceania', 'Australia', 'BOQAAU4B', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(209, 'AMP Bank', 'AMP', 'oceania', 'Australia', 'AMPBAU2S', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(210, 'ING Bank Australia', 'ING', 'oceania', 'Australia', 'INGBAU2S', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(211, 'ANZ Bank New Zealand', 'ANZ', 'oceania', 'New Zealand', 'ANZBNZ22', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(212, 'ASB Bank', 'ASB', 'oceania', 'New Zealand', 'ASBBNZ22', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(213, 'Bank of New Zealand', 'BNZ', 'oceania', 'New Zealand', 'BKNZNZ22', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(214, 'Westpac New Zealand', 'WBC', 'oceania', 'New Zealand', 'WPACNZ2W', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(215, 'Kiwibank', 'KIWI', 'oceania', 'New Zealand', 'KIWINZ22', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(216, 'Reserve Bank of Fiji', 'RBF', 'oceania', 'Fiji', 'RBFIFJFX', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(217, 'Bank of South Pacific', 'BSP', 'oceania', 'Fiji', 'BSPFFJFX', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(218, 'ANZ Bank Fiji', 'ANZ', 'oceania', 'Fiji', 'ANZBFJFX', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(219, 'Bank of Papua New Guinea', 'BPNG', 'oceania', 'Papua New Guinea', 'BPNGPGPM', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(220, 'Bank South Pacific PNG', 'BSP', 'oceania', 'Papua New Guinea', 'BSPPPGPM', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(221, 'Emirates NBD', 'ENBD', 'middle-east', 'United Arab Emirates', 'EBILAEAD', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(223, 'Abu Dhabi Commercial Bank', 'ADCB', 'middle-east', 'United Arab Emirates', 'ADCBAEAA', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(224, 'Dubai Islamic Bank', 'DIB', 'middle-east', 'United Arab Emirates', 'DUIBAEAD', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(225, 'Mashreq Bank', 'MASHREQ', 'middle-east', 'United Arab Emirates', 'BOMLAEAD', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(226, 'Bank Hapoalim', 'HAPOALIM', 'middle-east', 'Israel', 'POALILIT', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(227, 'Bank Leumi', 'LEUMI', 'middle-east', 'Israel', 'LUMIILIT', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(228, 'Israel Discount Bank', 'IDB', 'middle-east', 'Israel', 'IDBLILIT', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(229, 'Mizrahi Tefahot Bank', 'MIZRAHI', 'middle-east', 'Israel', 'MZBKILIT', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(230, 'Ziraat Bankası', 'ZIRAAT', 'middle-east', 'Turkey', 'TCZBTR2A', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(231, 'İş Bankası', 'ISBANK', 'middle-east', 'Turkey', 'ISBKTRIS', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(232, 'Garanti BBVA', 'GARANTI', 'middle-east', 'Turkey', 'TGBATRIS', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(233, 'Akbank', 'AKBANK', 'middle-east', 'Turkey', 'AKBKTRIS', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(234, 'Yapı Kredi Bankası', 'YKB', 'middle-east', 'Turkey', 'YAPITRIS', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(235, 'Al Rajhi Bank', 'ALRAJHI', 'middle-east', 'Saudi Arabia', 'RJHISARI', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(236, 'Saudi National Bank', 'SNB', 'middle-east', 'Saudi Arabia', 'NCBKSAJE', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(237, 'Riyad Bank', 'RIYAD', 'middle-east', 'Saudi Arabia', 'RIBLSARI', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(238, 'Saudi British Bank', 'SABB', 'middle-east', 'Saudi Arabia', 'SABBSARI', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(239, 'Banque Saudi Fransi', 'BSF', 'middle-east', 'Saudi Arabia', 'BSFRSARI', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(240, 'Qatar National Bank', 'QNB', 'middle-east', 'Qatar', 'QNBAQAQA', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(241, 'Commercial Bank of Qatar', 'CBQ', 'middle-east', 'Qatar', 'CBQAQAQA', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(242, 'Doha Bank', 'DOHA', 'middle-east', 'Qatar', 'DOHBQAQA', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(243, 'National Bank of Kuwait', 'NBK', 'middle-east', 'Kuwait', 'NBOKKWKW', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(244, 'Kuwait Finance House', 'KFH', 'middle-east', 'Kuwait', 'KFHBKWKW', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(245, 'Gulf Bank', 'GB', 'middle-east', 'Kuwait', 'GULBKWKW', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(246, 'Bank Muscat', 'MUSCAT', 'middle-east', 'Oman', 'BMUSOMRX', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(247, 'National Bank of Oman', 'NBO', 'middle-east', 'Oman', 'NBOMOMRX', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(248, 'Ahli United Bank', 'AUB', 'middle-east', 'Bahrain', 'AUBBBHBM', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(249, 'National Bank of Bahrain', 'NBB', 'middle-east', 'Bahrain', 'NBOBBHBM', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(250, 'Arab Bank', 'ARAB', 'middle-east', 'Jordan', 'ARABJOAX', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
(251, 'Vietcom bank', NULL, 'asia', 'Vietnam', '', 1, 60, '2026-02-23 20:48:36', '2026-02-23 20:48:36'),
(252, 'VietinBank', NULL, 'asia', 'Vietnam', '', 1, 60, '2026-02-23 20:50:05', '2026-02-23 20:50:05'),
(253, 'Vietnam International Commercial Joint Stock Bank (VIB)', NULL, 'asia', 'Vietnam', '', 1, 60, '2026-02-24 05:38:29', '2026-02-24 05:38:29'),
(254, 'BIDV (Bank for Investment and Development of Vietnam', NULL, 'asia', 'Vietnam', '', 1, 60, '2026-02-24 05:41:13', '2026-02-24 05:41:13'),
(255, 'Agribank', NULL, 'asia', 'Vietnam', '', 1, 60, '2026-02-24 05:42:07', '2026-02-24 05:42:07'),
(256, 'MBBank (Military Commercial Joint Stock Bank', NULL, 'asia', 'Vietnam', '', 1, 60, '2026-02-24 05:42:56', '2026-02-24 05:42:56'),
(257, 'Techcombank (Vietnam Technological and Commercial Joint Stock Bank', NULL, 'asia', 'Vietnam', '', 1, 60, '2026-02-24 05:43:55', '2026-02-24 05:43:55'),
(258, 'HDBank (Ho Chi Minh City Development Joint Stock Commercial Bank', NULL, 'asia', 'Vietnam', '', 1, 60, '2026-02-24 05:45:25', '2026-02-24 05:45:25'),
(259, 'ACB (Asia Commercial Joint Stock Bank)', NULL, 'asia', 'Vietnam', '', 1, 60, '2026-02-24 05:46:29', '2026-02-24 05:46:29'),
(260, 'Techcombank (Vietnam Technological and Commercial Joint Stock Bank )', NULL, 'asia', 'Vietnam', '', 1, 60, '2026-02-24 05:47:24', '2026-02-24 05:47:24'),
(261, 'Sacombank (Saigon Thuong Tin Commercial Joint Stock Bank )', NULL, 'asia', 'Vietnam', '', 1, 60, '2026-02-24 05:48:15', '2026-02-24 05:48:15'),
(262, 'MSB (Vietnam Maritime Commercial Joint Stock Bank )', NULL, 'asia', 'Vietnam', '', 1, 60, '2026-02-24 05:49:13', '2026-02-24 05:49:13'),
(263, 'LPBank (Loc Phat Vietnam Commercial Joint Stock Bank )', NULL, 'asia', 'Vietnam', '', 1, 60, '2026-02-24 05:53:31', '2026-02-24 05:53:31'),
(264, 'VIB (Vietnam International Commercial Joint Stock Bank )', NULL, 'asia', 'Vietnam', '', 1, 60, '2026-02-24 05:54:26', '2026-02-24 05:54:26'),
(265, 'HCM City Development Bank (HDBank)', NULL, 'asia', 'Vietnam', '', 1, 60, '2026-02-24 05:56:24', '2026-02-24 05:56:24'),
(266, 'Bank Muscat (SAOG)', NULL, 'middle-east', 'Oman', '', 1, 60, '2026-02-24 13:45:26', '2026-02-24 13:45:26'),
(267, 'National Bank of Oman (NBO)', NULL, 'middle-east', 'Oman', '', 1, 60, '2026-02-24 13:46:13', '2026-02-24 13:46:13'),
(268, 'Bank Dhofar (S.A.O.G.)', NULL, 'middle-east', 'Oman', '', 1, 60, '2026-02-24 13:46:37', '2026-02-24 13:46:37'),
(269, 'Oman Arab Bank (OAB)', NULL, 'middle-east', 'Oman', '', 1, 60, '2026-02-24 13:46:59', '2026-02-24 13:46:59'),
(270, 'Sohar International (formerly Bank Sohar):', NULL, 'middle-east', 'Oman', '', 1, 60, '2026-02-24 13:47:26', '2026-02-24 13:47:26'),
(271, 'Ahli Bank', NULL, 'middle-east', 'Oman', '', 1, 60, '2026-02-24 13:47:57', '2026-02-24 13:47:57'),
(272, 'Oman Development Bank / Oman Housing Bank', NULL, 'middle-east', 'Oman', '', 1, 60, '2026-02-24 13:48:31', '2026-02-24 13:48:31'),
(273, 'Bank Nizwa', NULL, 'middle-east', 'Oman', '', 1, 60, '2026-02-24 13:49:05', '2026-02-24 13:49:05'),
(274, 'Standard Chartered Bank', NULL, 'middle-east', 'Oman', '', 1, 60, '2026-02-24 13:49:40', '2026-02-24 13:49:40'),
(275, 'First Abu Dhabi Bank (FAB)', NULL, 'middle-east', 'United Arab Emirates', 'NBADAEAA', 1, 60, '2026-05-31 23:29:34', '2026-05-31 23:29:34'),
(277, 'Commercial Bank of Dubai (CBD)', NULL, 'middle-east', 'United Arab Emirates', 'CBDUAEAD', 1, 60, '2026-05-31 23:32:49', '2026-05-31 23:32:49'),
(278, 'RAKBANK', NULL, 'middle-east', 'United Arab Emirates', 'NRAKAEAK', 1, 60, '2026-05-31 23:34:02', '2026-05-31 23:34:02'),
(279, 'HSBC Bank Middle East', NULL, 'middle-east', 'United Arab Emirates', 'BBMEAEAD', 1, 60, '2026-05-31 23:34:45', '2026-05-31 23:34:45'),
(280, 'Sharjah Islamic Bank', NULL, 'middle-east', 'United Arab Emirates', 'NBSHAEAS', 1, 60, '2026-05-31 23:35:19', '2026-05-31 23:35:19'),
(281, 'United Arab Bank (UAB)', NULL, 'middle-east', 'United Arab Emirates', 'UNBEAEAA', 1, 60, '2026-05-31 23:35:56', '2026-05-31 23:35:56'),
(282, 'Abu Dhabi Islamic Bank (ADIB)', NULL, 'middle-east', 'United Arab Emirates', 'ABDIAEAD', 1, 60, '2026-05-31 23:37:57', '2026-05-31 23:37:57'),
(283, 'Al Hilal Bank', NULL, 'middle-east', 'United Arab Emirates', 'HLALAEAA', 1, 60, '2026-05-31 23:39:01', '2026-05-31 23:39:01'),
(284, 'Al Maryah Community Bank', NULL, 'middle-east', 'United Arab Emirates', 'E097AEXX', 1, 60, '2026-05-31 23:39:59', '2026-05-31 23:39:59'),
(285, 'Emirates Islamic', NULL, 'middle-east', 'United Arab Emirates', 'MEBLAEAD', 1, 60, '2026-05-31 23:41:13', '2026-05-31 23:41:13'),
(286, 'Ajman Bank', NULL, 'middle-east', 'United Arab Emirates', 'AJMNAEAJ', 1, 60, '2026-05-31 23:42:30', '2026-05-31 23:42:30');

-- --------------------------------------------------------

--
-- Table structure for table `beneficiaries`
--

CREATE TABLE `beneficiaries` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `beneficiary_name` varchar(255) NOT NULL,
  `account_number` varchar(50) NOT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `bank_code` varchar(50) DEFAULT NULL,
  `swift_code` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `currency` varchar(10) DEFAULT 'USD',
  `nickname` varchar(100) DEFAULT NULL,
  `beneficiary_type` enum('domestic','international') DEFAULT 'domestic',
  `is_verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bill_payments`
--

CREATE TABLE `bill_payments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `biller_name` varchar(255) NOT NULL,
  `biller_category` enum('utilities','phone','internet','insurance','credit_card','other') NOT NULL,
  `account_number` varchar(100) DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `currency` varchar(10) DEFAULT 'USD',
  `payment_date` date NOT NULL,
  `is_recurring` tinyint(1) DEFAULT 0,
  `recurring_frequency` enum('weekly','monthly','quarterly','yearly') DEFAULT NULL,
  `next_payment_date` date DEFAULT NULL,
  `status` enum('scheduled','processing','paid','failed','cancelled') DEFAULT 'scheduled',
  `transaction_ref` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cards`
--

CREATE TABLE `cards` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `card_number` varchar(255) NOT NULL,
  `card_type` enum('debit','credit','prepaid','virtual') NOT NULL,
  `card_name` varchar(100) DEFAULT NULL,
  `cvv` varchar(255) DEFAULT NULL,
  `expiry_date` date NOT NULL,
  `credit_limit` decimal(15,2) DEFAULT NULL,
  `available_credit` decimal(15,2) DEFAULT NULL,
  `balance` decimal(15,2) DEFAULT 0.00,
  `billing_cycle` int(11) DEFAULT 1,
  `is_virtual` tinyint(1) DEFAULT 0,
  `is_single_use` tinyint(1) DEFAULT 0,
  `status` enum('pending','active','frozen','blocked','expired','cancelled','rejected') DEFAULT 'pending',
  `daily_limit` decimal(15,2) DEFAULT 5000.00,
  `monthly_limit` decimal(15,2) DEFAULT 50000.00,
  `pin_hash` varchar(255) DEFAULT NULL,
  `last_used` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `expires_at` datetime DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `card_applications`
--

CREATE TABLE `card_applications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `card_type` enum('debit','credit','prepaid','virtual') NOT NULL,
  `card_name` varchar(100) DEFAULT NULL,
  `requested_credit_limit` decimal(15,2) DEFAULT NULL,
  `is_virtual` tinyint(1) DEFAULT 0,
  `purpose` text DEFAULT NULL,
  `employment_status` varchar(100) DEFAULT NULL,
  `annual_income` decimal(15,2) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `applied_date` timestamp NULL DEFAULT current_timestamp(),
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_date` datetime DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `card_applications`
--

INSERT INTO `card_applications` (`id`, `user_id`, `account_id`, `card_type`, `card_name`, `requested_credit_limit`, `is_virtual`, `purpose`, `employment_status`, `annual_income`, `status`, `applied_date`, `reviewed_by`, `reviewed_date`, `admin_notes`, `rejection_reason`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 'debit', 'Primary Debit Card', NULL, 0, 'Daily transactions and purchases', 'Employed', 75000.00, 'pending', '2025-10-14 08:00:00', NULL, NULL, NULL, NULL, '2025-10-14 08:00:00', '2025-10-14 08:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `card_transactions`
--

CREATE TABLE `card_transactions` (
  `id` int(11) NOT NULL,
  `card_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `transaction_type` enum('credit','debit') NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `balance_before` decimal(15,2) NOT NULL,
  `balance_after` decimal(15,2) NOT NULL,
  `status` enum('pending','completed','failed','cancelled') DEFAULT 'completed',
  `reference` varchar(100) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `merchant_name` varchar(255) DEFAULT NULL,
  `merchant_category` varchar(100) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crypto_wallets`
--

CREATE TABLE `crypto_wallets` (
  `id` int(11) NOT NULL,
  `crypto_type` enum('btc','eth','usdt','ltc','bch','doge','other') NOT NULL,
  `wallet_address` varchar(255) NOT NULL,
  `network` varchar(50) DEFAULT NULL COMMENT 'e.g., ERC20, TRC20, mainnet',
  `label` varchar(255) DEFAULT NULL COMMENT 'Admin label for this wallet',
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `crypto_wallets`
--

INSERT INTO `crypto_wallets` (`id`, `crypto_type`, `wallet_address`, `network`, `label`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'btc', '0xb308d62953fc5ED11FAf7B47d671422dE28519d9', 'Bitcoin', '', 1, 3, '2025-11-03 15:34:53', '2025-11-03 15:34:53');

-- --------------------------------------------------------

--
-- Table structure for table `currencies`
--

CREATE TABLE `currencies` (
  `id` int(11) NOT NULL,
  `code` varchar(3) NOT NULL,
  `name` varchar(100) NOT NULL,
  `symbol` varchar(10) NOT NULL,
  `exchange_rate` decimal(15,6) DEFAULT 1.000000,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `currencies`
--

INSERT INTO `currencies` (`id`, `code`, `name`, `symbol`, `exchange_rate`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'USD', 'US Dollar', '$', 1.000000, 1, '2025-01-15 10:00:00', '2025-01-15 10:00:00'),
(2, 'EUR', 'Euro', '€', 0.850000, 1, '2025-01-15 10:00:00', '2025-01-15 10:00:00'),
(3, 'GBP', 'British Pound', '£', 0.750000, 1, '2025-01-15 10:00:00', '2025-01-15 10:00:00'),
(4, 'JPY', 'Japanese Yen', '¥', 110.000000, 1, '2025-01-15 10:00:00', '2025-01-15 10:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `email_simulation_alert_captions`
--

CREATE TABLE `email_simulation_alert_captions` (
  `id` int(11) NOT NULL,
  `caption_text` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email_simulation_alert_captions`
--

INSERT INTO `email_simulation_alert_captions` (`id`, `caption_text`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Funds Received', 1, '2025-11-26 11:28:54', '2025-11-26 11:28:54'),
(2, 'Transaction Successful', 1, '2025-11-26 11:28:54', '2025-11-26 11:28:54'),
(3, 'Payment Confirmed', 1, '2025-11-26 11:28:54', '2025-11-26 11:28:54'),
(4, 'Deposit Completed', 1, '2025-11-26 11:28:54', '2025-11-26 11:28:54'),
(5, 'Transfer Received', 1, '2025-11-26 11:28:54', '2025-11-26 11:28:54'),
(6, 'Account Credited', 1, '2025-11-26 11:28:54', '2025-11-26 11:28:54');

-- --------------------------------------------------------

--
-- Table structure for table `email_simulation_templates`
--

CREATE TABLE `email_simulation_templates` (
  `id` int(11) NOT NULL,
  `template_name` varchar(100) NOT NULL,
  `template_type` enum('simple','advanced') DEFAULT 'simple',
  `primary_color` varchar(7) DEFAULT '#359eb4',
  `secondary_color` varchar(7) DEFAULT '#2a7e90',
  `accent_color` varchar(7) DEFAULT '#10b981',
  `logo_url` varchar(500) DEFAULT NULL,
  `logo_alt_text` varchar(255) DEFAULT 'Bank Logo',
  `address` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email_simulation_templates`
--

INSERT INTO `email_simulation_templates` (`id`, `template_name`, `template_type`, `primary_color`, `secondary_color`, `accent_color`, `logo_url`, `logo_alt_text`, `address`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Default Bank Template', 'advanced', '#359eb4', '#2a7e90', '#10b981', NULL, 'Bank Logo', NULL, 1, '2025-11-26 11:28:54', '2025-11-26 11:28:54'),
(2, 'PayPal', 'simple', '#00457c', '#0079c1', '#b5b5b5', 'https://online.cosmopolitantrustbankpf.com/assets/images/template-logos/template-logo-1764194208-7478.png', 'logo1', '2211 North First Street, San Jose, CA 95131', 1, '2025-11-26 11:35:27', '2025-11-26 21:56:50'),
(3, 'Venmo', 'simple', '#008cff', '#ffffff', '#c2c2c2', 'https://online.cosmopolitantrustbankpf.com/assets/images/template-logos/template-logo-1764195129-1035.png', 'ven11', '117 Barrow St, New York, NY', 1, '2025-11-26 20:19:01', '2025-11-26 22:12:11'),
(4, 'cash app', 'simple', '#00d54b', '#ffffff', '#00d54b', 'https://online.cosmopolitantrustbankpf.com/assets/images/template-logos/template-logo-1764194161-5726.png', 'cc22', 'Oakland, California, at 1955 Broadway', 1, '2025-11-26 20:20:38', '2025-11-26 21:56:04'),
(5, 'zelle', 'simple', '#6534d1', '#333333', '#6534d1', 'https://online.cosmopolitantrustbankpf.com/assets/images/template-logos/template-logo-1764192235-7342.png', 'zl22', '16552 N 90th St, Scottsdale, AZ, United States.', 1, '2025-11-26 21:24:27', '2025-11-26 21:24:27'),
(6, 'Wells Fargo', 'advanced', '#d71e28', '#ffffff', '#ffcd41', 'https://online.cosmopolitantrustbankpf.com/assets/images/template-logos/template-logo-1764192382-6142.png', 'wl33', '333 Market Street, San Francisco, CA 94105', 1, '2025-11-26 21:26:28', '2025-11-26 21:26:28'),
(7, 'BAO', 'advanced', '#012169', '#ffffff', '#e31837', 'https://online.cosmopolitantrustbankpf.com/assets/images/template-logos/template-logo-1764192795-3145.png', 'BB45', '100 North Tryon Street, Charlotte, NC 28255', 1, '2025-11-26 21:33:21', '2025-11-26 21:33:21');

-- --------------------------------------------------------

--
-- Table structure for table `email_verification_tokens`
--

CREATE TABLE `email_verification_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email_verification_tokens`
--

INSERT INTO `email_verification_tokens` (`id`, `user_id`, `token`, `expires_at`, `used`, `created_at`) VALUES
(136, 134, '30b8fffccd79fd4395af94b4078d07a40bdf07293aeceaec8579fe7d53b84496', '2026-06-01 01:22:17', 1, '2026-05-31 01:22:17'),
(137, 135, '28fa96d91234c03c487c6ded6418cc3418bc53c4db8bf6ab60cf0ed955dd006b', '2026-06-01 22:38:33', 1, '2026-05-31 22:38:33');

-- --------------------------------------------------------

--
-- Table structure for table `exchange_rates`
--

CREATE TABLE `exchange_rates` (
  `id` int(11) NOT NULL,
  `from_currency` varchar(10) NOT NULL,
  `to_currency` varchar(10) NOT NULL,
  `rate` decimal(10,4) NOT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exchange_rates`
--

INSERT INTO `exchange_rates` (`id`, `from_currency`, `to_currency`, `rate`, `updated_at`) VALUES
(1, 'USD', 'NGN', 1375.4204, '2026-05-01 23:52:44'),
(4, 'USD', 'EUR', 0.8672, '2026-03-18 23:55:07'),
(7, 'USD', 'USD', 1.0000, '2026-02-04 23:47:49'),
(8, 'USD', 'AED', 3.6725, '2026-02-04 23:47:49'),
(9, 'USD', 'AFN', 65.7705, '2026-02-04 23:47:49'),
(10, 'USD', 'ALL', 81.8516, '2026-02-04 23:47:49'),
(11, 'USD', 'AMD', 378.6385, '2026-02-04 23:47:49'),
(12, 'USD', 'ANG', 1.7900, '2026-02-04 23:47:49'),
(13, 'USD', 'AOA', 923.0647, '2026-02-04 23:47:49'),
(14, 'USD', 'ARS', 1452.2500, '2026-02-04 23:47:49'),
(15, 'USD', 'AUD', 1.4263, '2026-02-04 23:47:49'),
(16, 'USD', 'AWG', 1.7900, '2026-02-04 23:47:49'),
(17, 'USD', 'AZN', 1.6996, '2026-02-04 23:47:49'),
(18, 'USD', 'BAM', 1.6562, '2026-02-04 23:47:49'),
(19, 'USD', 'BBD', 2.0000, '2026-02-04 23:47:49'),
(20, 'USD', 'BDT', 122.3076, '2026-02-04 23:47:49'),
(21, 'USD', 'BGN', 1.6044, '2026-02-04 23:47:49'),
(22, 'USD', 'BHD', 0.3760, '2026-02-04 23:47:49'),
(23, 'USD', 'BIF', 2970.3155, '2026-02-04 23:47:49'),
(24, 'USD', 'BMD', 1.0000, '2026-02-04 23:47:49'),
(25, 'USD', 'BND', 1.2703, '2026-02-04 23:47:49'),
(26, 'USD', 'BOB', 6.9468, '2026-02-04 23:47:49'),
(27, 'USD', 'BRL', 5.2345, '2026-02-04 23:47:49'),
(28, 'USD', 'BSD', 1.0000, '2026-02-04 23:47:49'),
(29, 'USD', 'BTN', 90.3586, '2026-02-04 23:47:49'),
(30, 'USD', 'BWP', 13.4877, '2026-02-04 23:47:49'),
(31, 'USD', 'BYN', 2.8617, '2026-02-04 23:47:49'),
(32, 'USD', 'BZD', 2.0000, '2026-02-04 23:47:49'),
(33, 'USD', 'CAD', 1.3651, '2026-02-04 23:47:49'),
(34, 'USD', 'CDF', 2199.2680, '2026-02-04 23:47:49'),
(35, 'USD', 'CHF', 0.7764, '2026-02-04 23:47:49'),
(36, 'USD', 'CLF', 0.0219, '2026-02-04 23:47:49'),
(37, 'USD', 'CLP', 864.8020, '2026-02-04 23:47:49'),
(38, 'USD', 'CNH', 6.9358, '2026-02-04 23:47:49'),
(39, 'USD', 'CNY', 6.9456, '2026-02-04 23:47:49'),
(40, 'USD', 'COP', 3626.3380, '2026-02-04 23:47:49'),
(41, 'USD', 'CRC', 496.8925, '2026-02-04 23:47:49'),
(42, 'USD', 'CUP', 24.0000, '2026-02-04 23:47:49'),
(43, 'USD', 'CVE', 93.3745, '2026-02-04 23:47:49'),
(44, 'USD', 'CZK', 20.5978, '2026-02-04 23:47:49'),
(45, 'USD', 'DJF', 177.7210, '2026-02-04 23:47:49'),
(46, 'USD', 'DKK', 6.3206, '2026-02-04 23:47:49'),
(47, 'USD', 'DOP', 63.2781, '2026-02-04 23:47:49'),
(48, 'USD', 'DZD', 129.8768, '2026-02-04 23:47:49'),
(49, 'USD', 'EGP', 47.0058, '2026-02-04 23:47:49'),
(50, 'USD', 'ERN', 15.0000, '2026-02-04 23:47:49'),
(51, 'USD', 'ETB', 154.3615, '2026-02-04 23:47:49'),
(53, 'USD', 'FJD', 2.2009, '2026-02-04 23:47:49'),
(54, 'USD', 'FKP', 0.7305, '2026-02-04 23:47:49'),
(55, 'USD', 'FOK', 6.3209, '2026-02-04 23:47:49'),
(56, 'USD', 'GBP', 0.7376, '2026-05-01 23:52:13'),
(57, 'USD', 'GEL', 2.6898, '2026-02-04 23:47:49'),
(58, 'USD', 'GGP', 0.7305, '2026-02-04 23:47:49'),
(59, 'USD', 'GHS', 10.9907, '2026-02-16 16:38:54'),
(60, 'USD', 'GIP', 0.7305, '2026-02-04 23:47:49'),
(61, 'USD', 'GMD', 74.1107, '2026-02-04 23:47:49'),
(62, 'USD', 'GNF', 8754.5260, '2026-02-04 23:47:49'),
(63, 'USD', 'GTQ', 7.6730, '2026-02-04 23:47:49'),
(64, 'USD', 'GYD', 209.2151, '2026-02-04 23:47:49'),
(65, 'USD', 'HKD', 7.8133, '2026-02-04 23:47:49'),
(66, 'USD', 'HNL', 26.4382, '2026-02-04 23:47:49'),
(67, 'USD', 'HRK', 6.3804, '2026-02-04 23:47:49'),
(68, 'USD', 'HTG', 131.0192, '2026-02-04 23:47:49'),
(69, 'USD', 'HUF', 322.3097, '2026-02-04 23:47:49'),
(70, 'USD', 'IDR', 16780.9204, '2026-02-04 23:47:49'),
(71, 'USD', 'ILS', 3.0926, '2026-02-04 23:47:49'),
(72, 'USD', 'IMP', 0.7305, '2026-02-04 23:47:49'),
(73, 'USD', 'INR', 90.3520, '2026-02-04 23:47:49'),
(74, 'USD', 'IQD', 1310.9449, '2026-02-04 23:47:49'),
(75, 'USD', 'IRR', 999999.9999, '2026-02-04 23:47:49'),
(76, 'USD', 'ISK', 122.8591, '2026-02-04 23:47:49'),
(77, 'USD', 'JEP', 0.7305, '2026-02-04 23:47:49'),
(78, 'USD', 'JMD', 156.9138, '2026-02-04 23:47:49'),
(79, 'USD', 'JOD', 0.7090, '2026-02-04 23:47:49'),
(80, 'USD', 'JPY', 155.0060, '2026-02-20 00:11:54'),
(81, 'USD', 'KES', 128.9537, '2026-02-04 23:47:49'),
(82, 'USD', 'KGS', 87.4387, '2026-02-04 23:47:49'),
(83, 'USD', 'KHR', 4019.1676, '2026-02-04 23:47:49'),
(84, 'USD', 'KID', 1.4262, '2026-02-04 23:47:49'),
(85, 'USD', 'KMF', 416.6076, '2026-02-04 23:47:49'),
(86, 'USD', 'KRW', 1449.3582, '2026-02-04 23:47:49'),
(87, 'USD', 'KWD', 0.3070, '2026-02-04 23:47:49'),
(88, 'USD', 'KYD', 0.8333, '2026-02-04 23:47:49'),
(89, 'USD', 'KZT', 502.4827, '2026-02-04 23:47:49'),
(90, 'USD', 'LAK', 21672.3248, '2026-02-04 23:47:49'),
(91, 'USD', 'LBP', 89500.0000, '2026-02-04 23:47:49'),
(92, 'USD', 'LKR', 309.3294, '2026-02-04 23:47:49'),
(93, 'USD', 'LRD', 186.0974, '2026-02-04 23:47:49'),
(94, 'USD', 'LSL', 15.9641, '2026-02-04 23:47:49'),
(95, 'USD', 'LYD', 6.3156, '2026-02-04 23:47:49'),
(96, 'USD', 'MAD', 9.1535, '2026-02-04 23:47:49'),
(97, 'USD', 'MDL', 16.9275, '2026-02-04 23:47:49'),
(98, 'USD', 'MGA', 4429.2550, '2026-02-04 23:47:49'),
(99, 'USD', 'MKD', 52.0999, '2026-02-04 23:47:49'),
(100, 'USD', 'MMK', 2101.4434, '2026-02-04 23:47:49'),
(101, 'USD', 'MNT', 3541.0549, '2026-02-04 23:47:49'),
(102, 'USD', 'MOP', 8.0477, '2026-02-04 23:47:49'),
(103, 'USD', 'MRU', 39.9391, '2026-02-04 23:47:49'),
(104, 'USD', 'MUR', 45.8618, '2026-02-04 23:47:49'),
(105, 'USD', 'MVR', 15.4441, '2026-02-04 23:47:49'),
(106, 'USD', 'MWK', 1744.2702, '2026-02-04 23:47:49'),
(107, 'USD', 'MXN', 17.2641, '2026-02-04 23:47:49'),
(108, 'USD', 'MYR', 3.9333, '2026-02-04 23:47:49'),
(109, 'USD', 'MZN', 63.5673, '2026-02-04 23:47:49'),
(110, 'USD', 'NAD', 15.9641, '2026-02-04 23:47:49'),
(112, 'USD', 'NIO', 36.8388, '2026-02-04 23:47:49'),
(113, 'USD', 'NOK', 9.6555, '2026-02-04 23:47:49'),
(114, 'USD', 'NPR', 144.5737, '2026-02-04 23:47:49'),
(115, 'USD', 'NZD', 1.6556, '2026-02-04 23:47:49'),
(116, 'USD', 'OMR', 0.3845, '2026-02-04 23:47:49'),
(117, 'USD', 'PAB', 1.0000, '2026-02-04 23:47:49'),
(118, 'USD', 'PEN', 3.3647, '2026-02-04 23:47:49'),
(119, 'USD', 'PGK', 4.2908, '2026-02-04 23:47:49'),
(120, 'USD', 'PHP', 59.0755, '2026-02-04 23:47:49'),
(121, 'USD', 'PKR', 280.2982, '2026-02-04 23:47:49'),
(122, 'USD', 'PLN', 3.5748, '2026-02-04 23:47:49'),
(123, 'USD', 'PYG', 6657.6613, '2026-02-04 23:47:49'),
(124, 'USD', 'QAR', 3.6400, '2026-02-04 23:47:49'),
(125, 'USD', 'RON', 4.3190, '2026-02-04 23:47:49'),
(126, 'USD', 'RSD', 99.4434, '2026-02-04 23:47:49'),
(127, 'USD', 'RUB', 76.9343, '2026-02-04 23:47:49'),
(128, 'USD', 'RWF', 1459.9876, '2026-02-04 23:47:49'),
(129, 'USD', 'SAR', 3.7500, '2026-02-04 23:47:49'),
(130, 'USD', 'SBD', 7.9427, '2026-02-04 23:47:49'),
(131, 'USD', 'SCR', 14.4819, '2026-02-04 23:47:49'),
(132, 'USD', 'SDG', 449.6478, '2026-02-04 23:47:49'),
(133, 'USD', 'SEK', 8.9253, '2026-02-04 23:47:49'),
(134, 'USD', 'SGD', 1.2703, '2026-02-04 23:47:49'),
(135, 'USD', 'SHP', 0.7305, '2026-02-04 23:47:49'),
(136, 'USD', 'SLE', 24.3209, '2026-02-04 23:47:49'),
(137, 'USD', 'SLL', 24320.9465, '2026-02-04 23:47:49'),
(138, 'USD', 'SOS', 570.9345, '2026-02-04 23:47:49'),
(139, 'USD', 'SRD', 38.0257, '2026-02-04 23:47:49'),
(140, 'USD', 'SSP', 4720.5727, '2026-02-04 23:47:49'),
(141, 'USD', 'STN', 20.7471, '2026-02-04 23:47:49'),
(142, 'USD', 'SYP', 112.3150, '2026-02-04 23:47:49'),
(143, 'USD', 'SZL', 15.9641, '2026-02-04 23:47:49'),
(144, 'USD', 'THB', 31.5765, '2026-02-04 23:47:49'),
(145, 'USD', 'TJS', 9.3272, '2026-02-04 23:47:49'),
(146, 'USD', 'TMT', 3.5000, '2026-02-04 23:47:49'),
(147, 'USD', 'TND', 2.8593, '2026-02-04 23:47:49'),
(148, 'USD', 'TOP', 2.3642, '2026-02-04 23:47:49'),
(149, 'USD', 'TRY', 43.4989, '2026-02-04 23:47:49'),
(150, 'USD', 'TTD', 6.7392, '2026-02-04 23:47:49'),
(151, 'USD', 'TVD', 1.4262, '2026-02-04 23:47:49'),
(152, 'USD', 'TWD', 31.6004, '2026-02-04 23:47:49'),
(153, 'USD', 'TZS', 2552.4557, '2026-02-04 23:47:49'),
(154, 'USD', 'UAH', 43.2159, '2026-02-04 23:47:49'),
(155, 'USD', 'UGX', 3536.6690, '2026-02-04 23:47:49'),
(156, 'USD', 'UYU', 38.7840, '2026-02-04 23:47:49'),
(157, 'USD', 'UZS', 12237.3961, '2026-02-04 23:47:49'),
(158, 'USD', 'VES', 375.0825, '2026-02-04 23:47:49'),
(159, 'USD', 'VND', 25937.7451, '2026-02-04 23:47:49'),
(160, 'USD', 'VUV', 119.5202, '2026-02-04 23:47:49'),
(161, 'USD', 'WST', 2.6915, '2026-02-04 23:47:49'),
(162, 'USD', 'XAF', 555.4768, '2026-02-04 23:47:49'),
(163, 'USD', 'XCD', 2.7000, '2026-02-04 23:47:49'),
(164, 'USD', 'XCG', 1.7900, '2026-02-04 23:47:49'),
(165, 'USD', 'XDR', 0.7277, '2026-02-04 23:47:49'),
(166, 'USD', 'XOF', 555.4768, '2026-02-04 23:47:49'),
(167, 'USD', 'XPF', 101.0526, '2026-02-04 23:47:49'),
(168, 'USD', 'YER', 238.2901, '2026-02-04 23:47:49'),
(169, 'USD', 'ZAR', 15.9671, '2026-02-04 23:47:49'),
(170, 'USD', 'ZMW', 19.6962, '2026-02-04 23:47:49'),
(171, 'USD', 'ZWL', 25.6451, '2026-02-04 23:47:49'),
(358, 'USD', 'ZWG', 25.6451, '2026-02-04 23:47:49'),
(413, 'GBP', 'NGN', 1805.1732, '2026-03-19 09:56:31'),
(414, 'GBP', 'USD', 1.3408, '2026-04-13 02:54:14'),
(420, 'CAD', 'USD', 0.7251, '2026-05-31 02:47:13'),
(421, 'CAD', 'NGN', 1010.5161, '2026-05-30 22:07:24'),
(422, 'AED', 'CAD', 0.3767, '2026-06-02 22:23:28'),
(423, 'CAD', 'AED', 2.6549, '2026-06-02 22:24:41'),
(425, 'GBP', 'CAD', 1.8549, '2026-05-31 22:16:32'),
(426, 'CAD', 'GBP', 0.5391, '2026-05-31 22:17:32'),
(429, 'AUD', 'CAD', 0.9912, '2026-06-02 22:43:17'),
(430, 'CAD', 'JPY', 115.3522, '2026-06-02 23:10:11'),
(431, 'CAD', 'AUD', 1.0089, '2026-06-02 23:10:24'),
(432, 'CAD', 'HKD', 5.6646, '2026-06-02 23:13:03');

-- --------------------------------------------------------

--
-- Table structure for table `investment_funding`
--

CREATE TABLE `investment_funding` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `funding_method` varchar(50) NOT NULL COMMENT 'Funding method: bank_balance or crypto_{currency}',
  `crypto_currency` varchar(20) DEFAULT NULL,
  `crypto_address` varchar(255) DEFAULT NULL,
  `crypto_tx_hash` varchar(255) DEFAULT NULL,
  `account_id` int(11) DEFAULT NULL COMMENT 'Bank account used for funding',
  `status` enum('pending','completed','failed','cancelled') DEFAULT 'pending',
  `processed_at` datetime DEFAULT NULL,
  `processed_by` int(11) DEFAULT NULL COMMENT 'Admin who processed',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `investment_products`
--

CREATE TABLE `investment_products` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `type` enum('stocks','forex','crypto') NOT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `short_description` text DEFAULT NULL,
  `full_description` text DEFAULT NULL,
  `status` enum('draft','active','paused','closed') DEFAULT 'draft',
  `min_amount` decimal(15,2) NOT NULL,
  `max_amount` decimal(15,2) DEFAULT NULL,
  `min_duration_days` int(11) NOT NULL,
  `max_duration_days` int(11) DEFAULT NULL,
  `roi_config` text DEFAULT NULL COMMENT 'JSON configuration for ROI calculation',
  `payout_type` enum('compound_daily','simple_daily','payout_at_maturity') DEFAULT 'compound_daily',
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `capacity_total` decimal(15,2) DEFAULT NULL COMMENT 'Maximum total investment capacity',
  `per_user_max` decimal(15,2) DEFAULT NULL COMMENT 'Maximum investment per user',
  `risk_level` enum('low','medium','high') DEFAULT 'medium',
  `display_order` int(11) DEFAULT 0,
  `created_by_admin_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `investment_products`
--

INSERT INTO `investment_products` (`id`, `title`, `slug`, `type`, `image_url`, `short_description`, `full_description`, `status`, `min_amount`, `max_amount`, `min_duration_days`, `max_duration_days`, `roi_config`, `payout_type`, `start_date`, `end_date`, `capacity_total`, `per_user_max`, `risk_level`, `display_order`, `created_by_admin_id`, `created_at`, `updated_at`) VALUES
(1, 'Bitcoin', 'bitcoin', 'crypto', 'https://assets.coingecko.com/coins/images/1/large/bitcoin.png', 'Invest in the most valuable and stable cryptocurrency.', 'Bitcoin (BTC) is the world\'s first and most valuable cryptocurrency. With proven stability and widespread adoption, Bitcoin offers a reliable investment opportunity in the digital asset space.', 'active', 100.00, 50000.00, 30, 180, '{\"mode\": \"daily\", \"daily_percent\": 0.35}', 'compound_daily', NULL, NULL, NULL, NULL, 'medium', 1, NULL, '2025-10-28 23:59:51', '2025-10-28 23:59:51'),
(2, 'Ethereum', 'ethereum', 'crypto', 'https://assets.coingecko.com/coins/images/279/large/ethereum.png', 'Smart contract blockchain powering decentralized apps.', 'Ethereum (ETH) is a decentralized platform that enables smart contracts and decentralized applications (dApps). It\'s the second-largest cryptocurrency by market cap.', 'active', 100.00, 30000.00, 30, 120, '{\"mode\": \"daily\", \"daily_percent\": 0.32}', 'compound_daily', NULL, NULL, NULL, NULL, 'medium', 2, NULL, '2025-10-28 23:59:51', '2025-10-28 23:59:51'),
(3, 'Ripple', 'ripple', 'crypto', 'https://assets.coingecko.com/coins/images/44/large/xrp-symbol-white-128.png', 'Fast and low-cost blockchain for global payments.', 'Ripple (XRP) facilitates fast, low-cost international payments. Used by banks and financial institutions worldwide for cross-border transactions.', 'active', 50.00, 10000.00, 15, 90, '{\"mode\": \"daily\", \"daily_percent\": 0.28}', 'compound_daily', NULL, NULL, NULL, NULL, 'medium', 3, NULL, '2025-10-28 23:59:51', '2025-10-28 23:59:51'),
(4, 'Cardano', 'cardano', 'crypto', 'https://assets.coingecko.com/coins/images/975/large/cardano.png', 'Eco-friendly blockchain with staking and smart contracts.', 'Cardano (ADA) is an eco-friendly blockchain platform with a focus on sustainability, security, and scalability. Features smart contracts and staking rewards.', 'active', 50.00, 5000.00, 30, 120, '{\"mode\": \"daily\", \"daily_percent\": 0.27}', 'compound_daily', NULL, NULL, NULL, NULL, 'medium', 4, NULL, '2025-10-28 23:59:51', '2025-10-28 23:59:51'),
(5, 'Solana', 'solana', 'crypto', 'https://assets.coingecko.com/coins/images/4128/large/solana.png', 'Ultra-fast blockchain powering NFTs and DeFi apps.', 'Solana (SOL) is a high-performance blockchain supporting decentralized apps and crypto-currencies. Known for its speed and low transaction costs.', 'active', 75.00, 15000.00, 15, 90, '{\"mode\": \"daily\", \"daily_percent\": 0.30}', 'compound_daily', NULL, NULL, NULL, NULL, 'high', 5, NULL, '2025-10-28 23:59:51', '2025-10-28 23:59:51'),
(6, 'Binance Coin', 'binance-coin', 'crypto', 'https://assets.coingecko.com/coins/images/825/large/bnb-icon2_2x.png', 'Core token of the Binance exchange and smart chain.', 'Binance Coin (BNB) is the native cryptocurrency of the Binance exchange and Binance Smart Chain, used for trading fee discounts and network fees.', 'active', 100.00, 20000.00, 30, 120, '{\"mode\": \"daily\", \"daily_percent\": 0.33}', 'compound_daily', NULL, NULL, NULL, NULL, 'medium', 6, NULL, '2025-10-28 23:59:51', '2025-10-28 23:59:51'),
(7, 'Polkadot', 'polkadot', 'crypto', 'https://assets.coingecko.com/coins/images/12171/large/polkadot.png', 'Multi-chain protocol for Web3 interoperability.', 'Polkadot (DOT) enables different blockchains to transfer messages and value in a trust-free fashion, sharing their unique features while pooling their security.', 'active', 50.00, 10000.00, 30, 120, '{\"mode\": \"daily\", \"daily_percent\": 0.26}', 'compound_daily', NULL, NULL, NULL, NULL, 'medium', 7, NULL, '2025-10-28 23:59:51', '2025-10-28 23:59:51'),
(8, 'Avalanche', 'avalanche', 'crypto', 'https://assets.coingecko.com/coins/images/12559/large/avalanche-avax-logo.png', 'Scalable blockchain for DeFi and enterprise apps.', 'Avalanche (AVAX) is a highly scalable blockchain platform designed for building custom blockchain networks and DeFi applications.', 'active', 75.00, 10000.00, 15, 90, '{\"mode\": \"daily\", \"daily_percent\": 0.29}', 'compound_daily', NULL, NULL, NULL, NULL, 'high', 8, NULL, '2025-10-28 23:59:51', '2025-10-28 23:59:51'),
(9, 'Chainlink', 'chainlink', 'crypto', 'https://assets.coingecko.com/coins/images/877/large/chainlink-new-logo.png', 'Oracle network connecting real-world data to blockchains.', 'Chainlink (LINK) is a decentralized oracle network that enables smart contracts to securely access off-chain data feeds, events, and payment methods.', 'active', 50.00, 5000.00, 30, 90, '{\"mode\": \"daily\", \"daily_percent\": 0.25}', 'compound_daily', NULL, NULL, NULL, NULL, 'medium', 9, NULL, '2025-10-28 23:59:51', '2025-10-28 23:59:51'),
(10, 'Dogecoin', 'dogecoin', 'crypto', 'https://assets.coingecko.com/coins/images/5/large/dogecoin.png', 'Fun, community-driven crypto with global adoption.', 'Dogecoin (DOGE) started as a joke but has evolved into a widely accepted cryptocurrency with a passionate community and real-world utility.', 'active', 25.00, 3000.00, 15, 60, '{\"mode\": \"daily\", \"daily_percent\": 0.24}', 'compound_daily', NULL, NULL, NULL, NULL, 'medium', 10, NULL, '2025-10-28 23:59:51', '2025-10-28 23:59:51'),
(11, 'EUR/USD', 'eurusd', 'forex', 'https://cdn-icons-png.flaticon.com/512/2331/2331969.png', 'Trade the Euro against the U.S. Dollar. High liquidity.', 'The EUR/USD pair is the most traded currency pair in the world. It represents the Euro against the U.S. Dollar and offers high liquidity and tight spreads.', 'active', 100.00, 20000.00, 30, 120, '{\"mode\": \"daily\", \"daily_percent\": 0.20}', 'simple_daily', NULL, NULL, NULL, NULL, 'low', 11, NULL, '2025-10-28 23:59:51', '2025-10-28 23:59:51'),
(12, 'GBP/USD', 'gbpusd', 'forex', 'https://cdn-icons-png.flaticon.com/512/2331/2331969.png', 'Pound vs Dollar pair — strong volatility.', 'GBP/USD (Cable) is one of the most volatile major currency pairs. The pound\'s relationship with the dollar creates significant trading opportunities.', 'active', 100.00, 15000.00, 15, 90, '{\"mode\": \"daily\", \"daily_percent\": 0.21}', 'simple_daily', NULL, NULL, NULL, NULL, 'medium', 12, NULL, '2025-10-28 23:59:51', '2025-10-28 23:59:51'),
(13, 'USD/JPY', 'usdjpy', 'forex', 'https://cdn-icons-png.flaticon.com/512/2331/2331969.png', 'Dollar vs Yen — known for long-term stability.', 'USD/JPY is one of the most liquid currency pairs. Known for long-term stability and predictable trends, making it ideal for strategic trading.', 'active', 100.00, 20000.00, 30, 120, '{\"mode\": \"daily\", \"daily_percent\": 0.19}', 'simple_daily', NULL, NULL, NULL, NULL, 'low', 13, NULL, '2025-10-28 23:59:51', '2025-10-28 23:59:51'),
(14, 'AUD/USD', 'audusd', 'forex', 'https://cdn-icons-png.flaticon.com/512/2331/2331969.png', 'Commodity-linked pair tied to the Australian economy.', 'AUD/USD (Aussie) is heavily influenced by commodity prices, particularly gold and iron ore. Reflects the strength of the Australian economy.', 'active', 75.00, 10000.00, 15, 90, '{\"mode\": \"daily\", \"daily_percent\": 0.20}', 'simple_daily', NULL, NULL, NULL, NULL, 'medium', 14, NULL, '2025-10-28 23:59:51', '2025-10-28 23:59:51'),
(15, 'USD/CAD', 'usdcad', 'forex', 'https://cdn-icons-png.flaticon.com/512/2331/2331969.png', 'Dollar vs Canadian Dollar — oil-price sensitive.', 'USD/CAD (Loonie) is closely tied to oil prices due to Canada\'s significant oil exports. Offers steady trading opportunities.', 'active', 75.00, 10000.00, 15, 90, '{\"mode\": \"daily\", \"daily_percent\": 0.19}', 'simple_daily', NULL, NULL, NULL, NULL, 'medium', 15, NULL, '2025-10-28 23:59:51', '2025-10-28 23:59:51'),
(16, 'EUR/GBP', 'eurgbp', 'forex', 'https://cdn-icons-png.flaticon.com/512/2331/2331969.png', 'Euro–Pound cross rate with moderate swings.', 'EUR/GBP is a cross currency pair that doesn\'t include the U.S. Dollar. Offers moderate volatility and clear trading ranges.', 'active', 50.00, 8000.00, 15, 60, '{\"mode\": \"daily\", \"daily_percent\": 0.18}', 'simple_daily', NULL, NULL, NULL, NULL, 'low', 16, NULL, '2025-10-28 23:59:51', '2025-10-28 23:59:51'),
(17, 'NZD/USD', 'nzdusd', 'forex', 'https://cdn-icons-png.flaticon.com/512/2331/2331969.png', '\"Kiwi\" pair — great for short-term traders.', 'NZD/USD (Kiwi) is influenced by dairy and commodity prices. Known for its responsiveness to market sentiment and economic data.', 'active', 50.00, 8000.00, 15, 60, '{\"mode\": \"daily\", \"daily_percent\": 0.19}', 'simple_daily', NULL, NULL, NULL, NULL, 'medium', 17, NULL, '2025-10-28 23:59:51', '2025-10-28 23:59:51'),
(18, 'USD/CHF', 'usdchf', 'forex', 'https://cdn-icons-png.flaticon.com/512/2331/2331969.png', 'Dollar vs Swiss Franc — low-risk, steady movements.', 'USD/CHF (Swissie) is known as a safe-haven pair. The Swiss Franc is considered a stable currency, making this pair ideal for conservative traders.', 'active', 100.00, 12000.00, 30, 90, '{\"mode\": \"daily\", \"daily_percent\": 0.20}', 'simple_daily', NULL, NULL, NULL, NULL, 'low', 18, NULL, '2025-10-28 23:59:51', '2025-10-28 23:59:51'),
(19, 'EUR/JPY', 'eurjpy', 'forex', 'https://cdn-icons-png.flaticon.com/512/2331/2331969.png', 'Active pair with strong momentum in Asia–EU markets.', 'EUR/JPY combines two major economies and offers strong momentum trading opportunities, especially during overlapping Asian and European market hours.', 'active', 75.00, 10000.00, 15, 90, '{\"mode\": \"daily\", \"daily_percent\": 0.21}', 'simple_daily', NULL, NULL, NULL, NULL, 'medium', 19, NULL, '2025-10-28 23:59:51', '2025-10-28 23:59:51'),
(20, 'GBP/JPY', 'gbpjpy', 'forex', 'https://cdn-icons-png.flaticon.com/512/2331/2331969.png', 'High volatility, high reward pair. Great for experts.', 'GBP/JPY is one of the most volatile currency pairs. Offers high profit potential but requires experience due to rapid price movements.', 'active', 100.00, 15000.00, 15, 60, '{\"mode\": \"daily\", \"daily_percent\": 0.22}', 'simple_daily', NULL, NULL, NULL, NULL, 'high', 20, NULL, '2025-10-28 23:59:51', '2025-10-28 23:59:51'),
(21, 'Apple Inc.', 'apple-inc', 'stocks', 'https://logo.clearbit.com/apple.com', 'Invest in the world\'s top tech innovator.', 'Apple Inc. (AAPL) is a global technology leader known for innovative products including iPhone, iPad, Mac, and services. Consistently ranked among the world\'s most valuable companies.', 'active', 200.00, 50000.00, 60, 180, '{\"mode\": \"daily\", \"daily_percent\": 0.30}', 'compound_daily', NULL, NULL, NULL, NULL, 'medium', 21, NULL, '2025-10-28 23:59:51', '2025-10-28 23:59:51'),
(22, 'Tesla Inc.', 'tesla-inc', 'stocks', 'https://logo.clearbit.com/tesla.com', 'High-growth EV and energy company.', 'Tesla Inc. (TSLA) is revolutionizing transportation and energy with electric vehicles, solar panels, and energy storage solutions. A leader in sustainable technology.', 'active', 200.00, 50000.00, 60, 180, '{\"mode\": \"daily\", \"daily_percent\": 0.35}', 'compound_daily', NULL, NULL, NULL, NULL, 'high', 22, NULL, '2025-10-28 23:59:51', '2025-10-28 23:59:51'),
(23, 'Amazon.com Inc.', 'amazon-inc', 'stocks', 'https://logo.clearbit.com/amazon.com', 'E-commerce and cloud leader with global presence.', 'Amazon.com Inc. (AMZN) is the world\'s largest online retailer and cloud computing provider. Dominates e-commerce and leads in cloud services with AWS.', 'active', 150.00, 30000.00, 30, 120, '{\"mode\": \"daily\", \"daily_percent\": 0.28}', 'compound_daily', NULL, NULL, NULL, NULL, 'medium', 23, NULL, '2025-10-28 23:59:51', '2025-10-28 23:59:51'),
(24, 'Microsoft Corp.', 'microsoft-corp', 'stocks', 'https://logo.clearbit.com/microsoft.com', 'Software and AI leader driving digital transformation.', 'Microsoft Corp. (MSFT) is a technology giant providing software, cloud services, and AI solutions. Powers businesses and consumers worldwide with innovative technology.', 'active', 150.00, 30000.00, 30, 120, '{\"mode\": \"daily\", \"daily_percent\": 0.27}', 'compound_daily', NULL, NULL, NULL, NULL, 'medium', 24, NULL, '2025-10-28 23:59:51', '2025-10-28 23:59:51'),
(25, 'Alphabet Inc.', 'alphabet-inc', 'stocks', 'https://logo.clearbit.com/google.com', 'Parent company of Google and YouTube.', 'Alphabet Inc. (GOOGL) is the parent company of Google, YouTube, and other technology ventures. Dominates online search, advertising, and digital services.', 'active', 150.00, 25000.00, 30, 120, '{\"mode\": \"daily\", \"daily_percent\": 0.26}', 'compound_daily', NULL, NULL, NULL, NULL, 'medium', 25, NULL, '2025-10-28 23:59:51', '2025-10-28 23:59:51'),
(26, 'Nvidia Corp.', 'nvidia-corp', 'stocks', 'https://logo.clearbit.com/nvidia.com', 'GPU & AI chipmaker shaping the future of computing.', 'Nvidia Corp. (NVDA) is a leading manufacturer of graphics processing units (GPUs) and AI chips. Powers gaming, data centers, and artificial intelligence applications.', 'active', 200.00, 30000.00, 30, 90, '{\"mode\": \"daily\", \"daily_percent\": 0.32}', 'compound_daily', NULL, NULL, NULL, NULL, 'high', 26, NULL, '2025-10-28 23:59:51', '2025-10-28 23:59:51'),
(27, 'Meta Platforms Inc.', 'meta-platforms', 'stocks', 'https://logo.clearbit.com/meta.com', 'Social media and metaverse technology giant.', 'Meta Platforms Inc. (META), formerly Facebook, owns Facebook, Instagram, WhatsApp, and Oculus. Leading in social media and building the metaverse.', 'active', 150.00, 25000.00, 30, 90, '{\"mode\": \"daily\", \"daily_percent\": 0.27}', 'compound_daily', NULL, NULL, NULL, NULL, 'medium', 27, NULL, '2025-10-28 23:59:51', '2025-10-28 23:59:51'),
(28, 'JPMorgan Chase & Co.', 'jpmorgan-chase', 'stocks', 'https://logo.clearbit.com/jpmorganchase.com', 'Trusted global investment bank and financial firm.', 'JPMorgan Chase & Co. (JPM) is one of the largest and most respected financial institutions globally, offering banking, investment, and wealth management services.', 'active', 100.00, 15000.00, 30, 120, '{\"mode\": \"daily\", \"daily_percent\": 0.24}', 'compound_daily', NULL, NULL, NULL, NULL, 'low', 28, NULL, '2025-10-28 23:59:51', '2025-10-28 23:59:51'),
(29, 'Berkshire Hathaway', 'berkshire-hathaway', 'stocks', 'https://logo.clearbit.com/berkshirehathaway.com', 'Long-term value investment by Warren Buffett\'s firm.', 'Berkshire Hathaway (BRK.B) is Warren Buffett\'s investment holding company. Known for long-term value investing and diverse portfolio of quality businesses.', 'active', 200.00, 50000.00, 60, 180, '{\"mode\": \"daily\", \"daily_percent\": 0.29}', 'compound_daily', NULL, NULL, NULL, NULL, 'low', 29, NULL, '2025-10-28 23:59:51', '2025-10-28 23:59:51'),
(30, 'ExxonMobil Corp.', 'exxonmobil-corp', 'stocks', 'https://logo.clearbit.com/exxonmobil.com', 'Global energy leader with strong dividend potential.', 'ExxonMobil Corp. (XOM) is one of the world\'s largest publicly traded energy companies. Provides oil, natural gas, and petrochemical products globally.', 'active', 100.00, 15000.00, 30, 90, '{\"mode\": \"daily\", \"daily_percent\": 0.23}', 'compound_daily', NULL, NULL, NULL, NULL, 'medium', 30, NULL, '2025-10-28 23:59:51', '2025-10-28 23:59:51');

-- --------------------------------------------------------

--
-- Table structure for table `investment_transactions`
--

CREATE TABLE `investment_transactions` (
  `id` int(11) NOT NULL,
  `user_investment_id` int(11) DEFAULT NULL COMMENT 'Linked investment if applicable',
  `user_id` int(11) NOT NULL,
  `type` enum('deposit','debit','payout','accrual','admin_adjustment','refund') NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `balance_before` decimal(15,2) DEFAULT NULL,
  `balance_after` decimal(15,2) DEFAULT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `investment_withdrawals`
--

CREATE TABLE `investment_withdrawals` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `withdrawal_method` enum('bank_balance','external_account','paypal','venmo','crypto_btc','crypto_eth','crypto_usdt','crypto_ltc','crypto_other') NOT NULL,
  `recipient_type` enum('bank_account','paypal_email','venmo_phone','crypto_address') DEFAULT NULL,
  `recipient_info` text DEFAULT NULL COMMENT 'JSON: account details, email, phone, address',
  `status` enum('pending','processing','completed','failed','cancelled','rejected') DEFAULT 'pending',
  `processed_at` datetime DEFAULT NULL,
  `processed_by` int(11) DEFAULT NULL COMMENT 'Admin who processed',
  `rejection_reason` text DEFAULT NULL,
  `transaction_ref` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ip_access_control`
--

CREATE TABLE `ip_access_control` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `type` enum('whitelist','blacklist') NOT NULL,
  `reason` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `joint_account_requests`
--

CREATE TABLE `joint_account_requests` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `primary_owner_id` int(11) NOT NULL,
  `requesting_user_id` int(11) NOT NULL,
  `status` enum('pending','approved','rejected','expired') DEFAULT 'pending',
  `requested_at` timestamp NULL DEFAULT current_timestamp(),
  `responded_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kyc_beneficial_owners`
--

CREATE TABLE `kyc_beneficial_owners` (
  `id` int(11) NOT NULL,
  `kyc_verification_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `ownership_percentage` decimal(5,2) NOT NULL,
  `id_type` enum('drivers_license','state_id','passport','military_id') DEFAULT NULL,
  `id_number` varchar(255) DEFAULT NULL,
  `id_document_front` varchar(255) DEFAULT NULL,
  `id_document_back` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `zip` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kyc_verifications`
--

CREATE TABLE `kyc_verifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `account_type` enum('individual','business') NOT NULL DEFAULT 'individual',
  `full_legal_name` varchar(255) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `ssn` varchar(255) DEFAULT NULL COMMENT 'Encrypted SSN/ITIN',
  `residential_address` text DEFAULT NULL,
  `residential_city` varchar(100) DEFAULT NULL,
  `residential_state` varchar(100) DEFAULT NULL,
  `residential_country` varchar(100) DEFAULT NULL,
  `residential_zip` varchar(20) DEFAULT NULL,
  `id_type` varchar(50) DEFAULT NULL,
  `id_number` varchar(255) DEFAULT NULL,
  `id_issued_date` date DEFAULT NULL,
  `id_expiry_date` date DEFAULT NULL,
  `id_issued_state` varchar(100) DEFAULT NULL,
  `id_issued_country` varchar(100) DEFAULT NULL,
  `id_document_front` varchar(255) DEFAULT NULL,
  `id_document_back` varchar(255) DEFAULT NULL,
  `proof_of_address` varchar(255) DEFAULT NULL,
  `signature_image` varchar(255) DEFAULT NULL,
  `business_name` varchar(255) DEFAULT NULL,
  `business_address` text DEFAULT NULL,
  `business_city` varchar(100) DEFAULT NULL,
  `business_state` varchar(100) DEFAULT NULL,
  `business_country` varchar(100) DEFAULT NULL,
  `business_zip` varchar(20) DEFAULT NULL,
  `ein` varchar(255) DEFAULT NULL COMMENT 'Encrypted EIN',
  `business_formation_doc` varchar(255) DEFAULT NULL,
  `source_of_funds` text DEFAULT NULL COMMENT 'Declaration of source of funds',
  `account_purpose` text DEFAULT NULL COMMENT 'Purpose of account declaration',
  `extra_fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`extra_fields`)),
  `status` enum('pending','under_review','verified','rejected','requires_action') DEFAULT 'pending',
  `verified_by` int(11) DEFAULT NULL COMMENT 'Admin user ID who verified',
  `verified_at` datetime DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kyc_verifications`
--

INSERT INTO `kyc_verifications` (`id`, `user_id`, `account_type`, `full_legal_name`, `date_of_birth`, `ssn`, `residential_address`, `residential_city`, `residential_state`, `residential_country`, `residential_zip`, `id_type`, `id_number`, `id_issued_date`, `id_expiry_date`, `id_issued_state`, `id_issued_country`, `id_document_front`, `id_document_back`, `proof_of_address`, `signature_image`, `business_name`, `business_address`, `business_city`, `business_state`, `business_country`, `business_zip`, `ein`, `business_formation_doc`, `source_of_funds`, `account_purpose`, `extra_fields`, `status`, `verified_by`, `verified_at`, `rejection_reason`, `admin_notes`, `submitted_at`, `updated_at`) VALUES
(36, 134, 'individual', 'carter tech', '1987-06-25', 'S0F2RmswT3RZcDI5dzdKN2t1NVo5UT09OjoOyQv7B9h/pNIpZdpaFijb', '177 Ago Palace Way,, Lagos , Lagos', 'Oshodi Isolo', 'Lagos', 'AE', '110224', 'drivers_license', '2455252', '2026-05-14', '2026-05-29', 'Lagos', 'Canada', 'kyc/6a1b91cb92388_1780191691_7HRYTw_3KdHHFaGStmHl9pqQEfPrA_sd.jpeg', 'kyc/6a1b91cb92625_1780191691_7HRYTw_3KdHHFaGStmHl9pqQEfPrA_sd.jpeg', 'kyc/6a1b91cb92848_1780191691_7HRYTw_3KdHHFaGStmHl9pqQEfPrA_sd.jpeg', 'kyc/6a1b91cb92a5f_1780191691_7HRYTw_3KdHHFaGStmHl9pqQEfPrA_sd.jpeg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'weed', 'weed', NULL, 'verified', 3, '2026-05-31 01:42:15', NULL, '', '2026-05-31 01:41:31', '2026-05-31 01:42:15');

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `account_id` int(11) DEFAULT NULL,
  `loan_type` enum('personal','auto','mortgage','business','education') NOT NULL,
  `loan_amount` decimal(15,2) NOT NULL,
  `approved_amount` decimal(15,2) DEFAULT NULL,
  `outstanding_balance` decimal(15,2) DEFAULT NULL,
  `interest_rate` decimal(5,2) NOT NULL,
  `term_months` int(11) NOT NULL,
  `monthly_payment` decimal(15,2) DEFAULT NULL,
  `purpose` text DEFAULT NULL,
  `status` enum('pending','approved','rejected','active','completed','defaulted') DEFAULT 'pending',
  `application_date` timestamp NULL DEFAULT current_timestamp(),
  `approval_date` datetime DEFAULT NULL,
  `first_payment_date` date DEFAULT NULL,
  `last_payment_date` date DEFAULT NULL,
  `next_payment_date` date DEFAULT NULL,
  `documents` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`documents`)),
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loan_payments`
--

CREATE TABLE `loan_payments` (
  `id` int(11) NOT NULL,
  `loan_id` int(11) NOT NULL,
  `payment_amount` decimal(15,2) NOT NULL,
  `principal_amount` decimal(15,2) NOT NULL,
  `interest_amount` decimal(15,2) NOT NULL,
  `penalty_amount` decimal(15,2) DEFAULT 0.00,
  `payment_date` date NOT NULL,
  `due_date` date NOT NULL,
  `status` enum('scheduled','paid','overdue','waived') DEFAULT 'scheduled',
  `payment_method` varchar(50) DEFAULT NULL,
  `transaction_ref` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `attempted_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `email`, `ip_address`, `attempted_at`) VALUES
(3, 'timwangtuyen@gmail.com', '77.81.142.51', '2025-10-19 09:23:33'),
(4, 'timwangtuyen@gmail.com', '77.81.142.51', '2025-10-19 09:24:13'),
(5, 'timwangtuyen@gmail.com', '77.81.142.51', '2025-10-19 09:25:33'),
(6, 'timwangtuyen@gmail.com', '105.112.107.149', '2025-10-20 01:38:55'),
(12, 'chanyeol6829@gmail.com', '37.19.196.159', '2025-11-19 09:17:52'),
(13, 'donjnjglobal@gmail.com', '23.154.136.106', '2025-11-22 14:55:59'),
(14, 'donjnjglobal@gmail.com', '23.154.136.106', '2025-11-22 14:56:22'),
(15, 'donjnjglobal@gmail.com', '23.154.136.106', '2025-11-22 14:58:14'),
(16, 'wuiyeyeg@gmail.com', '102.90.101.160', '2025-12-05 21:41:33'),
(17, 'karinaanna2did@gmail.com', '197.211.59.119', '2025-12-06 01:14:04'),
(18, 'karinaanna2did@gmail.com', '197.211.59.119', '2025-12-06 01:14:18'),
(23, 'odufubesumalua@gmail.com', '105.112.105.232', '2025-12-07 21:05:23'),
(24, 'odufubesumalua@gmail.com', '105.112.105.232', '2025-12-07 21:08:09'),
(25, 'Wilsonramodise@gmail.com', '105.0.1.39', '2025-12-12 21:25:00'),
(26, 'Wilsonramodise@gmail.com', '105.0.1.39', '2025-12-12 21:25:33'),
(27, 'Wilsonramodise@gmail.com', '105.0.1.39', '2025-12-12 21:25:45'),
(29, 'nathanwhite8155@gmail.com', '139.64.164.66', '2025-12-15 02:30:04'),
(30, 'nathanwhite8155@gmail.com', '139.64.164.66', '2025-12-15 02:30:15'),
(31, 'wilsonramodise@gmail.com', '105.4.7.34', '2025-12-15 05:46:55'),
(32, 'wilsonramodise@gmail.com', '105.4.7.34', '2025-12-15 05:47:13'),
(33, 'wilsonramodise@gmail.com', '105.4.7.34', '2025-12-15 05:47:51'),
(34, 'sprt.theme@gmail.com', '197.211.52.70', '2025-12-16 13:21:13'),
(35, 'nathanwhite8155@gmail.com', '197.211.63.107', '2025-12-17 18:26:58'),
(36, 'nathanwhite8155@gmail.com', '197.211.63.107', '2025-12-17 18:27:02'),
(37, 'nathanwhite8155@gmail.com', '197.211.63.107', '2025-12-17 18:27:08'),
(38, 'nathanwhite8155@gmail.com', '149.7.16.202', '2025-12-19 22:47:44'),
(39, 'nathanwhite8155@gmail.com', '149.7.16.202', '2025-12-19 22:47:50'),
(40, 'nathanwhite8155@gmail.com', '149.7.16.202', '2025-12-19 22:47:56'),
(41, 'aspinalladam5@gmail.com', '149.7.16.202', '2025-12-19 22:49:15'),
(42, 'aspinalladam5@gmail.com', '149.7.16.202', '2025-12-19 22:49:19'),
(43, 'aspinalladam5@gmail.com', '57.129.130.33', '2025-12-23 08:34:34'),
(44, 'aspinalladam5@gmail.com', '57.129.130.33', '2025-12-23 08:35:19'),
(45, 'aspinalladam5@gmail.com', '57.129.130.33', '2025-12-23 08:35:19'),
(46, 'okekechukwudubem155@gmail.com', '105.112.211.184', '2025-12-24 23:16:42'),
(47, 'okekechukwudubem155@gmail.com', '105.112.211.184', '2025-12-24 23:17:03'),
(48, 'okekechukwudubem155@gmail.com', '105.112.211.184', '2025-12-24 23:17:42'),
(49, 'aspinalladam5@gmail.com', '197.211.53.87', '2025-12-31 20:27:43'),
(50, 'aspinalladam5@gmail.com', '197.211.53.87', '2025-12-31 20:27:45'),
(52, 'jadejordan6040@gmail.com', '197.211.59.184', '2026-01-06 19:23:31'),
(53, 'jadejordan6040@gmail.com', '197.211.59.184', '2026-01-06 19:24:01'),
(54, 'jadejordan6040@gmail.com', '197.211.59.184', '2026-01-07 03:57:37'),
(55, 'jadejordan6040@gmail.com', '197.211.59.184', '2026-01-07 03:57:52'),
(56, 'jadejordan6040@gmail.com', '197.211.59.184', '2026-01-07 10:08:44'),
(57, 'henryahmedgeorge@gmail.com', '179.36.74.104', '2026-01-07 19:18:06'),
(58, 'henryahmedgeorge@gmail.com', '179.36.74.104', '2026-01-07 19:18:21'),
(59, 'henryahmedgeorge@gmail.com', '179.36.74.104', '2026-01-07 19:18:39'),
(60, 'angeloleelouisbusk@gmail.com', '105.119.25.39', '2026-01-08 17:55:49'),
(61, 'angeloleelouisbusk@gmail.com', '105.119.25.39', '2026-01-08 18:00:10'),
(62, 'emperormethuselah@gmail.com', '102.90.102.49', '2026-01-12 10:43:31'),
(63, 'emperormethuselah@gmail.com', '102.90.102.49', '2026-01-12 10:43:36'),
(64, 'emperormethuselah@gmail.com', '102.90.102.49', '2026-01-12 10:43:50'),
(65, 'jadejordan6040@gmail.com', '174.212.224.100', '2026-01-13 14:39:07'),
(66, 'jadejordan6040@gmail.com', '174.212.224.100', '2026-01-13 14:42:42'),
(67, 'helena14smith@gmail.com', '2c0f:f5c0:739:3166:70f6:1aff:fe8e:bf42', '2026-01-13 16:26:30'),
(68, 'helena14smith@gmail.com', '2c0f:f5c0:620:ad2:70f6:1aff:fe8e:bf42', '2026-01-15 21:49:45'),
(69, 'mrmichaeljpratt@domain.com', '102.90.101.1', '2026-01-16 03:23:21'),
(70, 'mrmichaeljpratt@domain.com', '102.90.101.1', '2026-01-16 03:23:32'),
(71, 'ellsasheila88@gmail.com', '36.77.26.118', '2026-01-27 11:51:31'),
(72, 'ellsasheila88@gmail.com', '36.77.26.118', '2026-01-27 11:51:53'),
(73, 'ellsasheila88@gmail.com', '36.77.26.118', '2026-01-27 11:53:03'),
(74, 'ellsasheila88@gmail.com', '114.10.45.206', '2026-01-28 07:01:45'),
(75, 'ellsasheila88@gmail.com', '114.10.45.206', '2026-01-28 07:02:14'),
(76, 'ellsasheila88@gmail.com', '114.10.45.206', '2026-01-28 07:02:28'),
(79, 'aishagaddafi3992@gmail.com', '173.239.247.173', '2026-02-01 20:59:10'),
(80, 'aishagaddafi3992@gmail.com', '173.239.247.173', '2026-02-01 20:59:42'),
(81, 'billyfredrickgibbons@gmail.com', '102.91.78.56', '2026-02-04 17:10:51'),
(82, 'billyfredrickgibbons@gmail.com', '102.91.78.56', '2026-02-04 17:11:35'),
(83, 'janmartin840@gmail.com', '105.118.5.15', '2026-02-06 02:43:07'),
(84, 'biggordons48@gmail.com', '102.90.98.209', '2026-02-06 23:51:39'),
(85, 'ellsasheila88@gmail.com', '2400:9800:25a:80c4:1891:e782:395f:b645', '2026-02-07 08:48:35'),
(86, 'ellsasheila88@gmail.com', '2400:9800:25a:80c4:1891:e782:395f:b645', '2026-02-07 08:48:55'),
(87, 'ellsasheila88@gmail.com', '2400:9800:25a:80c4:1891:e782:395f:b645', '2026-02-07 09:23:33'),
(88, 'ellsasheila88@gmail.com', '2400:9800:25a:80c4:1891:e782:395f:b645', '2026-02-07 09:24:19'),
(89, 'ellsasheila88@gmail.com', '2400:9800:25a:80c4:1891:e782:395f:b645', '2026-02-07 09:24:38'),
(97, 'promisepine@gmail.com', '2c0f:f5c0:482:3165:884c:a4ff:fe3a:3a95', '2026-02-10 08:48:11'),
(98, 'promisepine@gmail.com', '2c0f:f5c0:482:3165:884c:a4ff:fe3a:3a95', '2026-02-10 08:49:02'),
(99, 'muhammadauwalm95@gmail.com', '197.211.57.8', '2026-02-11 00:18:10'),
(100, 'muhammadauwalm95@gmail.com', '197.211.57.8', '2026-02-11 00:19:50'),
(101, 'muhammadauwalm95@gmail.com', '197.211.57.8', '2026-02-11 00:20:19'),
(102, 'muhammadauwalm95@gmail.com', '197.211.57.8', '2026-02-11 00:20:27'),
(103, 'muhammadauwalm95@gmail.com', '197.211.57.8', '2026-02-11 00:23:59'),
(105, 'officialaishagaddafi1@gmail.com', '105.112.212.60', '2026-02-13 13:33:54'),
(106, 'officialaishagaddafi1@gmail.com', '105.112.212.60', '2026-02-13 13:34:05'),
(107, 'officialaishagaddafi1@gmail.com', '105.112.212.60', '2026-02-13 13:34:19'),
(108, 'officialaishagaddafi1@gmail.com', '105.112.212.60', '2026-02-13 13:34:33'),
(109, 'officialaishagaddafi1@gmail.com', '105.112.212.60', '2026-02-13 13:34:33'),
(110, 'officialaishagaddafi1@gmail.com', '2605:59c0:ec1:1310:e4ca:b5d0:9b23:da6b', '2026-02-13 13:44:44'),
(121, 'larrymalone8375@gmail.com', '49.204.8.27', '2026-02-15 16:30:19'),
(122, 'helenbessie20@gmail.com', '41.204.44.21', '2026-02-16 16:37:34'),
(123, 'helenbessie20@gmail.com', '41.204.44.21', '2026-02-16 16:37:42'),
(125, 'ayeshamaummargaddafi@gmail.com', '98.97.79.34', '2026-02-16 23:31:50'),
(126, 'gaddafiayeshamaunmar@gmail.com', '2605:59c0:ec1:1310:e553:23ed:b6fc:2a0c', '2026-02-16 23:33:11'),
(128, 'boygentwo3@gmail.com', '102.90.118.232', '2026-02-17 23:48:04'),
(129, 'officialaishagaddafi1@gmail.com', '2605:59c0:ec1:1310:7de1:375b:ae23:8bb7', '2026-02-19 23:28:06'),
(130, 'officialaishagaddafi1@gmail.com', '2605:59c0:ec1:1310:7de1:375b:ae23:8bb7', '2026-02-19 23:28:18'),
(139, 'ayodelejames2000@gmail.com', '102.89.69.56', '2026-02-20 12:37:04'),
(140, 'pannapanatcha66@gmail.com', '197.211.52.64', '2026-02-20 13:18:07'),
(141, 'pannapanatcha66@gmail.com', '197.211.52.64', '2026-02-20 13:18:36'),
(142, 'pannapanatcha66@gmail.com', '197.211.52.64', '2026-02-20 13:19:17'),
(143, 'pannapanatcha66@gmail.com', '197.211.52.64', '2026-02-20 13:30:25'),
(144, 'pannapanatcha66@gmail.com', '197.211.52.64', '2026-02-20 13:30:44'),
(145, 'pannapanatcha66@gmail.com', '197.211.52.64', '2026-02-20 13:31:00'),
(146, 'pannapanatcha66@gmail.com', '197.211.52.64', '2026-02-20 13:31:27'),
(147, 'pannapanatcha66@gmail.com', '197.211.52.64', '2026-02-20 13:33:29'),
(149, 'edafeogiso11@gmail.com', '102.88.113.163', '2026-02-20 18:52:17'),
(150, 'eioovaiv947@gmail.com', '102.90.98.211', '2026-02-20 22:05:50'),
(151, 'edafeogiso790@gmail.com', '102.90.100.146', '2026-02-20 22:42:03'),
(152, 'mondaymknah@gmail.com', '197.211.52.68', '2026-02-20 23:58:34'),
(153, 'nick04552@gmail.com', '105.112.201.188', '2026-02-21 01:05:22'),
(154, 'edafeogiso11@gmail.com', '102.90.79.134', '2026-02-21 01:56:05'),
(155, 'nick04552@gmail.com', '105.113.57.97', '2026-02-23 03:28:20'),
(156, 'nick04552@gmail.com', '105.113.57.97', '2026-02-23 03:28:25'),
(157, 'nick04552@gmail.com', '102.89.44.236', '2026-02-23 03:56:29'),
(158, 'nick04552@gmail.com', '102.89.44.236', '2026-02-23 03:57:19'),
(159, 'officialaishagaddafi1@gmail.com', '197.211.53.98', '2026-02-23 04:05:41'),
(160, 'officialaishagaddafi1@gmail.com', '197.211.53.98', '2026-02-23 04:06:48'),
(161, 'officialaishagaddafi1@gmail.com', '197.211.53.98', '2026-02-23 04:08:03'),
(162, 'officialaishagaddafi1@gmail.com', '197.211.53.98', '2026-02-23 04:51:07'),
(163, 'pannapanatcha66@gmail.com', '197.211.52.67', '2026-02-23 14:35:51'),
(164, 'pannapanatcha66@gmail.com', '197.211.52.67', '2026-02-23 14:36:01'),
(165, 'pannapanatcha66@gmail.com', '197.211.52.67', '2026-02-23 14:36:13'),
(166, 'pannapanatcha66@gmail.com', '197.211.52.67', '2026-02-23 14:36:19'),
(167, 'nick04552@gmail.com', '156.146.39.220', '2026-02-23 14:40:43'),
(168, 'officialaishagaddafi1@gmail.com', '105.112.203.83', '2026-02-23 23:40:25'),
(169, 'aishamuammar87@gmail.com', '197.210.55.62', '2026-02-24 11:46:22'),
(170, 'aishamuammar87@gmail.com', '197.210.55.62', '2026-02-24 12:06:32'),
(171, 'elon93604@gmail.com', '2c0f:f5c0:90c:54c:2824:e6ff:fe94:69bc', '2026-02-25 18:11:31'),
(172, 'elon0913musk@gmail.com', '2c0f:f5c0:90c:54c:2824:e6ff:fe94:69bc', '2026-02-25 18:12:08'),
(173, 'baelay@gmail.com', '102.90.102.103', '2026-02-26 04:08:58'),
(174, 'support@us.cosmopolitantrustbankpf.com', '2605:59c1:192a:d308:4957:8a28:6cb:7bf6', '2026-03-01 10:32:46'),
(175, 'support@us.cosmopolitantrustbankpf.com', '2605:59c1:192a:d308:4957:8a28:6cb:7bf6', '2026-03-01 10:33:24'),
(176, 'support@us.cosmopolitantrustbankpf.com', '2605:59c1:192a:d308:158:e013:7605:b199', '2026-03-01 10:50:01'),
(177, 'support@us.cosmopolitantrustbankpf.com', '2605:59c1:192a:d308:d8e7:9be6:57d8:647a', '2026-03-02 00:29:27'),
(178, 'dongchan6281@gmail.com', '2605:59c1:192a:d308:5d42:8e74:9cbb:16f4', '2026-03-02 13:19:38'),
(179, 'support@us.cosmopolitantrustbankpf.com', '2605:59c1:192a:d308:bdb7:9f6b:f6af:769a', '2026-03-02 14:43:53'),
(180, 'dongchan6281@gmail.com', '2605:59c1:192a:d308:bdb7:9f6b:f6af:769a', '2026-03-02 15:08:49'),
(183, 'pannapanatcha66@gmail.com', '105.113.64.185', '2026-03-03 12:16:48'),
(184, 'pannapanatcha66@gmail.com', '105.113.64.185', '2026-03-03 12:17:02'),
(185, 'pannapanatcha66@gmail.com', '105.113.64.185', '2026-03-03 12:17:13'),
(186, 'smoothpicsstudio@gmail.com', '102.90.99.96', '2026-03-03 19:27:39'),
(187, 'jadejordan6040@gmail.com', '102.88.113.27', '2026-03-04 12:04:44'),
(200, 'officialaishagaddafi@gmail.com', '185.26.180.210', '2026-03-15 00:22:52'),
(201, 'officalaishagaddafi@gmail.com', '111.49.57.148', '2026-03-15 03:52:46'),
(202, 'officalaishagaddafi@gmail.com', '111.49.57.148', '2026-03-15 03:54:41'),
(203, 'officialaishagaddafi@gmail.com', '111.49.57.148', '2026-03-15 04:03:11'),
(204, 'officialaishagaddafi@gmail.com', '111.49.57.148', '2026-03-15 04:06:54'),
(205, 'officialaishagaddafi@gmail.com', '111.49.57.148', '2026-03-15 04:08:45'),
(206, 'officialaishagaddafi@gmail.com', '111.49.57.148', '2026-03-15 04:29:24'),
(207, 'officalaishagaddafi@gmail.com', '111.49.57.148', '2026-03-15 04:31:34'),
(208, 'officialaishagaddafi@gmail.com', '111.49.57.148', '2026-03-15 05:22:51'),
(209, 'officialaishagaddafi@gmail.com', '111.49.57.148', '2026-03-15 05:24:45'),
(210, 'officialaishagaddafi@gmail.com', '111.49.57.148', '2026-03-15 06:25:01'),
(211, 'officialaishagaddafi@gmail.com', '111.49.57.148', '2026-03-15 11:07:28'),
(212, 'officialaishagaddafi@gmail.com', '111.49.57.148', '2026-03-15 11:12:50'),
(213, 'officialaishagaddafi@gmail.com', '111.49.57.148', '2026-03-15 11:15:42'),
(214, 'officialaishagaddafi@gmail.com', '111.49.57.148', '2026-03-15 11:42:59'),
(215, 'officialmuammaraisha@gmail.com', '111.49.57.148', '2026-03-15 11:45:09'),
(216, 'officialaishagaddafi@gmail.com', '111.49.57.148', '2026-03-16 04:57:56'),
(217, 'officialaishagaddafi@gmail.com', '111.49.57.148', '2026-03-16 04:59:06'),
(218, 'officialmuammaraishagaddafi@gmail.com', '111.49.57.148', '2026-03-16 05:02:04'),
(219, 'officialmuammaraishagaddafi@gmail.com', '111.49.57.148', '2026-03-16 05:03:45'),
(220, 'officialaishagaddafi@gmail.com', '111.49.57.148', '2026-03-16 14:05:43'),
(221, 'officialaishagaddafi@gmail.com', '111.49.57.148', '2026-03-16 14:10:46'),
(222, 'manager@saveridgecapital.com', '102.89.47.66', '2026-03-18 21:41:08'),
(223, 'alexwanghengry@gmail.com', '102.90.82.44', '2026-03-18 22:37:27'),
(224, 'alexwanghengry@gmail.com', '102.90.82.44', '2026-03-18 22:37:55'),
(225, 'alexwanghengry@gmail.com', '102.90.82.44', '2026-03-18 22:38:22'),
(226, 'alexwanghengry@gmail.com', '102.90.82.44', '2026-03-18 22:39:20'),
(227, 'alexwanghengry@gmail.com', '102.90.82.44', '2026-03-18 22:39:31'),
(228, 'alexwanghengry@gmail.com', '102.90.82.44', '2026-03-18 22:40:34'),
(229, 'alexwanghengry@gmail.com', '102.90.82.44', '2026-03-18 22:40:43'),
(245, 'billyfredrickgibbons@gmail.com', '146.70.253.151', '2026-03-19 08:57:30'),
(246, 'billyfredrickgibbons@gmail.com', '146.70.253.151', '2026-03-19 08:57:55'),
(247, 'cccjtenvzSt@ingushetdomaz.pro', '91.212.150.122', '2026-03-20 14:24:07'),
(248, 'rhlpnehoqPa@problemno.shop', '85.198.109.165', '2026-03-20 14:24:07'),
(249, 'ozkubhqufEi@igurant1.online', '95.142.46.155', '2026-03-22 08:28:14'),
(250, 'asatgboyxSl@igurant1.online', '95.142.46.155', '2026-03-26 16:18:32'),
(251, 'support@saveridgecapital.com', '102.89.47.175', '2026-03-28 19:01:18'),
(258, 'williamsjohnson277533@gmail.com', '105.112.219.33', '2026-04-13 16:33:17'),
(259, 'williamsjohnson277533@gmail.com', '105.112.219.33', '2026-04-13 18:26:54'),
(260, 'mingxiayuen@gmail.com', '102.90.45.35', '2026-04-30 10:12:06'),
(261, 'mingxiayuen@gmail.com', '102.90.45.35', '2026-04-30 10:12:49'),
(262, 'Mingxiayuen@gmail.com', '102.90.45.35', '2026-04-30 10:14:51'),
(263, 'Mingxiayuen@gmail.com', '102.90.45.35', '2026-04-30 10:20:50'),
(264, 'rjsxgyofzKi@zolon.store', '46.28.65.4', '2026-06-01 20:45:29');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','success','warning','error','transaction','security') DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT 0,
  `link` varchar(255) DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `type`, `is_read`, `link`, `metadata`, `created_at`) VALUES
(154, 134, 'KYC Verification Approved', 'Your KYC verification has been approved. You now have full access to all banking services.', 'success', 0, '/profile/kyc', NULL, '2026-05-31 01:42:15');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `schema_migrations`
--

CREATE TABLE `schema_migrations` (
  `id` int(11) NOT NULL,
  `version` varchar(20) NOT NULL,
  `migration_name` varchar(255) NOT NULL,
  `migration_file` varchar(255) NOT NULL,
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `applied_by` int(11) DEFAULT NULL,
  `status` enum('success','failed','skipped') NOT NULL DEFAULT 'success',
  `error_message` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `schema_migrations`
--

INSERT INTO `schema_migrations` (`id`, `version`, `migration_name`, `migration_file`, `applied_at`, `applied_by`, `status`, `error_message`) VALUES
(1, '2026.03.19', 'safe_schema_upgrade', '2026_03_19_safe_schema_upgrade.sql', '2026-04-13 18:19:21', NULL, 'success', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `support_messages`
--

CREATE TABLE `support_messages` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_staff_reply` tinyint(1) DEFAULT 0,
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attachments`)),
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ticket_number` varchar(50) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `category` enum('account','transaction','card','loan','technical','other') NOT NULL,
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `status` enum('open','pending','resolved','closed') DEFAULT 'open',
  `assigned_to` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `resolved_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_alerts`
--

CREATE TABLE `system_alerts` (
  `id` int(11) NOT NULL,
  `alert_type` enum('security','compliance','system','fraud') NOT NULL,
  `severity` enum('low','medium','high','critical') NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `related_user_id` int(11) DEFAULT NULL,
  `related_transaction_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `is_resolved` tinyint(1) DEFAULT 0,
  `resolved_by` int(11) DEFAULT NULL,
  `resolved_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('string','number','boolean','json') DEFAULT 'string',
  `description` text DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `description`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'First National Capital FN', 'string', 'Website name displayed throughout the site', 60, '2025-10-08 22:44:52', '2026-06-02 23:16:48'),
(2, 'site_url', 'https://firstnationalfn.com', 'string', 'Website URL', 60, '2025-10-08 22:44:52', '2026-06-02 23:16:48'),
(3, 'site_email', 'support@firstnationalfn.com', 'string', 'Primary contact email', 60, '2025-10-08 22:44:52', '2026-06-02 23:16:48'),
(4, 'default_currency', 'CAD', 'string', 'Default system currency', 60, '2025-10-08 22:44:52', '2026-06-02 23:16:48'),
(5, 'min_transfer_amount', '1', 'number', 'Minimum transfer amount', 60, '2025-10-08 22:44:52', '2026-06-02 23:16:48'),
(6, 'max_transfer_amount', '5000000.12', 'number', 'Maximum transfer amount per transaction', 60, '2025-10-08 22:44:52', '2026-06-02 23:16:48'),
(7, 'transfer_fee_domestic', '0', 'number', 'Domestic transfer fee (deprecated)', 60, '2025-10-08 22:44:52', '2026-06-02 23:16:48'),
(8, 'transfer_fee_international', '0.5', 'number', 'International transfer fee (deprecated)', 60, '2025-10-08 22:44:52', '2026-06-02 23:16:48'),
(9, 'interest_rate_savings', '2.5', 'number', 'Savings account interest rate percentage', 60, '2025-10-08 22:44:52', '2026-06-02 23:16:48'),
(10, 'maintenance_mode', '0', 'boolean', 'Enable maintenance mode', 60, '2025-10-08 22:44:52', '2026-06-02 23:16:48'),
(11, 'require_kyc', '0', 'boolean', 'Require KYC verification', 60, '2025-10-08 22:44:52', '2026-06-02 23:16:48'),
(12, 'two_factor_required', '1', 'boolean', 'Force 2FA for all users', 60, '2025-10-08 22:44:52', '2026-06-02 23:16:48'),
(13, 'allow_new_registrations', '1', 'boolean', 'Enable/disable new user registrations', 60, '2025-10-08 22:44:52', '2026-06-02 23:16:48'),
(14, 'loan_service_enabled', '1', 'boolean', 'Enable/disable loan applications', 60, '2025-10-08 22:44:52', '2026-06-02 23:16:48'),
(15, 'card_service_enabled', '1', 'boolean', 'Enable/disable card requests', 60, '2025-10-08 22:44:52', '2026-06-02 23:16:48'),
(16, 'maintenance_message', 'System maintenance in progress', 'string', 'Maintenance mode message', 60, '2025-10-08 22:44:52', '2026-06-02 23:16:48'),
(17, 'max_daily_transfer_amount', '50000', 'number', 'Maximum daily transfer amount per user', 3, '2025-10-08 22:44:52', '2025-11-09 19:09:06'),
(18, 'max_transaction_amount', '10000000', 'number', 'Maximum single transaction amount', 60, '2025-10-08 22:44:52', '2026-06-02 23:16:48'),
(19, 'kyc_required_for_transfer', '1', 'boolean', 'Require KYC verification for transfers', 60, '2025-10-08 22:44:52', '2026-06-02 23:16:48'),
(20, 'auto_flag_large_transactions', '0', 'boolean', 'Auto-flag transactions over threshold', 60, '2025-10-08 22:44:52', '2026-06-02 23:16:48'),
(21, 'large_transaction_threshold', '10000', 'number', 'Amount threshold for flagging', 60, '2025-10-08 22:44:52', '2026-06-02 23:16:48'),
(25, 'bank_operating_country', 'United Arab Emirates', 'string', 'Country where the bank operates', 60, '2025-10-14 00:00:00', '2026-06-02 23:16:48'),
(26, 'bank_operating_region', 'north-america', 'string', 'Region where the bank operates', 3, '2025-10-14 00:00:00', '2025-10-18 03:39:05'),
(27, 'site_logo_url', 'https://firstnationalfn.com/assets/images/bank-logo.webp?v=1780429189', 'string', 'URL to site logo image', 60, '2025-10-14 00:00:00', '2026-06-02 23:16:48'),
(28, 'site_tagline', 'Your Trusted Banking Partner', 'string', 'Site tagline/slogan', 60, '2025-10-14 00:00:00', '2026-06-02 23:16:48'),
(29, 'site_description', 'Secure online banking with 24/7 access to your accounts', 'string', 'Site description for SEO', 60, '2025-10-14 00:00:00', '2026-06-02 23:16:48'),
(30, 'support_phone', '+44882769***', 'string', 'Customer support phone number', 60, '2025-10-14 00:00:00', '2026-06-02 23:16:48'),
(31, 'support_hours', 'Monday - Friday, 8:00 AM - 6:00 PM EST', 'string', 'Customer support hours', 60, '2025-10-14 00:00:00', '2026-06-02 23:16:48'),
(32, 'bank_address', '2015 Northwest Hwy, Garland, TX 75041, London, United Kingdom ', 'string', 'Physical bank address', 60, '2025-10-14 00:00:00', '2026-06-02 23:16:48'),
(34, 'interest_rate_checking', '0', 'number', 'Checking account interest rate percentage', 60, '2025-10-14 00:00:00', '2026-06-02 23:16:48'),
(35, 'overdraft_fee', '35', 'number', 'Overdraft fee amount', 60, '2025-10-14 00:00:00', '2026-06-02 23:16:48'),
(36, 'monthly_maintenance_fee', '0', 'number', 'Monthly account maintenance fee', 60, '2025-10-14 00:00:00', '2026-06-02 23:16:48'),
(37, 'require_transfer_pin', '1', 'boolean', 'Require Transfer PIN for transactions', 60, '2025-10-14 00:00:00', '2026-06-02 23:16:48'),
(38, 'max_login_attempts', '10', 'number', 'Maximum failed login attempts before lockout', 60, '2025-10-14 00:00:00', '2026-06-02 23:16:48'),
(39, 'login_lockout_duration', '5', 'number', 'Login lockout duration in minutes', 60, '2025-01-15 10:00:00', '2026-06-02 23:16:48'),
(40, 'session_timeout', '30', 'number', 'Session timeout in minutes', 60, '2025-10-14 00:00:00', '2026-06-02 23:16:48'),
(41, 'email_on_transfer', '1', 'boolean', 'Send email notification on transfers', 60, '2025-10-14 00:00:00', '2026-06-02 23:16:48'),
(42, 'email_on_login', '1', 'boolean', 'Send email notification on login', 60, '2025-10-14 00:00:00', '2026-06-02 23:16:48'),
(43, 'site_favicon_url', 'https://firstnationalfn.com/favicon.png?v=1780428957', 'string', 'URL to site favicon', 60, '2025-10-14 00:00:00', '2026-06-02 23:16:48'),
(44, 'transfer_internal_fee', '0', 'number', 'Internal transfer fee percentage', 3, '2025-10-14 00:00:00', '2025-10-18 03:39:05'),
(45, 'transfer_domestic_fee', '0.5', 'number', 'Domestic transfer fee percentage', 3, '2025-10-14 00:00:00', '2025-10-18 03:39:05'),
(46, 'transfer_international_fee', '2.5', 'number', 'International transfer fee percentage', 3, '2025-10-14 00:00:00', '2025-10-18 03:39:05'),
(47, 'sms_on_transfer', '1', 'boolean', 'Send SMS notification on transfers', 60, '2025-10-14 00:00:00', '2026-06-02 23:16:48'),
(48, 'daily_limit_checking', '500000', 'number', 'Daily transaction limit for Checking accounts', 60, '2025-11-03 02:27:47', '2026-06-02 23:16:48'),
(49, 'daily_limit_savings', '500000', 'number', 'Daily transaction limit for Savings accounts', 60, '2025-11-03 02:27:47', '2026-06-02 23:16:48'),
(50, 'daily_limit_business', '2000000', 'number', 'Daily transaction limit for Business accounts', 60, '2025-11-03 02:27:47', '2026-06-02 23:16:48'),
(51, 'monthly_limit_checking', '12000000', 'number', 'Monthly transaction limit for Checking accounts', 60, '2025-11-03 02:27:47', '2026-06-02 23:16:48'),
(52, 'monthly_limit_savings', '1000000000', 'number', 'Monthly transaction limit for Savings accounts', 60, '2025-11-03 02:27:47', '2026-06-02 23:16:48'),
(53, 'monthly_limit_business', '20000000', 'number', 'Monthly transaction limit for Business accounts', 60, '2025-11-03 02:27:47', '2026-06-02 23:16:48'),
(142, 'enable_currency_conversion', '1', 'boolean', 'Enable currency conversion. When enabled, users can view balances and amounts in their preferred currency. When disabled, all amounts are displayed in the site default currency.', 60, '2025-11-04 17:17:14', '2026-06-02 23:16:48'),
(414, 'disable_2fa_entirely', '0', 'boolean', 'Disable 2FA entirely for all users. When enabled, users cannot enable 2FA and existing 2FA will be disabled. This overrides the \"Force 2FA\" setting.', 60, '2026-02-10 02:55:45', '2026-06-02 23:16:48'),
(1415, 'force_security_setup', '1', 'boolean', 'When enabled, users must complete Login PIN and Transfer PIN (+ 2FA if required) before accessing the dashboard', 60, '2026-05-31 01:13:56', '2026-06-02 23:16:48'),
(1416, 'kyc_use_custom_fields', '0', 'boolean', 'Use custom admin-defined KYC fields instead of country profile defaults', 60, '2026-05-31 01:13:56', '2026-06-02 23:16:48'),
(1417, 'kyc_custom_fields', '[]', 'json', 'JSON array of custom KYC field definitions when kyc_use_custom_fields is enabled', 60, '2026-05-31 01:13:56', '2026-06-02 23:16:48');

-- --------------------------------------------------------

--
-- Table structure for table `system_versions`
--

CREATE TABLE `system_versions` (
  `id` int(11) NOT NULL,
  `version` varchar(20) NOT NULL,
  `release_date` datetime NOT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `package_size` bigint(20) DEFAULT 0,
  `file_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_version_info`
--

CREATE TABLE `system_version_info` (
  `id` int(11) NOT NULL,
  `current_version` varchar(20) NOT NULL,
  `database_version` varchar(20) NOT NULL,
  `last_updated` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_version_info`
--

INSERT INTO `system_version_info` (`id`, `current_version`, `database_version`, `last_updated`, `updated_by`) VALUES
(1, '1.0.0', '1.0.0', '2025-11-26 11:28:54', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `transaction_ref` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `transaction_type` enum('debit','credit') NOT NULL,
  `category` enum('transfer','payment','deposit','withdrawal','fee','interest','loan','card','other') NOT NULL,
  `expense_category` enum('shopping','food','transport','bills','entertainment','healthcare','travel','education','salary','investment','rent','insurance','gift','personal','other') DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `currency` varchar(10) DEFAULT 'USD',
  `balance_before` decimal(15,2) DEFAULT NULL,
  `balance_after` decimal(15,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `recipient_account` varchar(255) DEFAULT NULL,
  `recipient_name` varchar(255) DEFAULT NULL,
  `recipient_bank` varchar(255) DEFAULT NULL,
  `status` enum('pending','processing','completed','failed','reversed') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `fee` decimal(10,2) DEFAULT 0.00,
  `exchange_rate` decimal(10,4) DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `transaction_ref`, `user_id`, `account_id`, `transaction_type`, `category`, `expense_category`, `amount`, `currency`, `balance_before`, `balance_after`, `description`, `recipient_account`, `recipient_name`, `recipient_bank`, `status`, `payment_method`, `fee`, `exchange_rate`, `metadata`, `ip_address`, `created_at`, `completed_at`) VALUES
(646, 'ADM20260530212637403', 134, 128, 'credit', 'deposit', '', 600.00, 'CAD', 0.00, 600.00, 'Transfer from pascal paul at wells Fargo', '35644775', 'pascal paul', 'wells Fargo', 'completed', NULL, 0.00, NULL, '{\"admin_id\":3,\"reason\":\"Administrative adjustment\",\"method\":\"domestic\",\"method_fields\":{\"recipient_bank\":\"wells Fargo\",\"recipient_account\":\"35644775\",\"recipient_name\":\"pascal paul\"},\"admin_action\":true}', '102.89.82.233', '2026-05-31 02:26:00', '2026-05-31 02:26:00'),
(647, 'TXN6A1B924C3D7E3', 134, 128, 'debit', 'transfer', '', 234.17, 'CAD', 600.00, 365.84, 'Domestic Transfer to werty mum at Bank of Nova Scotia', '24252252', 'werty mum', 'Bank of Nova Scotia', 'completed', 'eft', 1.17, NULL, '{\"transfer_method\":\"eft\",\"transfer_method_label\":\"EFT\",\"country_code\":\"CA\",\"bank_name\":\"Bank of Nova Scotia\",\"account_number\":\"24252252\",\"transit_number\":\"24242\",\"institution_number\":\"424\",\"transaction_override\":\"normal\",\"failed_reason\":null}', '102.89.82.233', '2026-05-31 01:43:40', '2026-05-31 01:43:40'),
(648, 'ADM20260531190117340', 135, 129, 'credit', 'transfer', '', 20000.00, 'CAD', 0.00, 20000.00, 'Admin balance update', '', 'Internal Transfer', 'First National Capital FN', 'completed', NULL, 0.00, NULL, '{\"admin_id\":60,\"reason\":\"Administrative adjustment\",\"method\":\"internal\",\"method_fields\":{\"recipient_account\":\"\",\"recipient_name\":\"Internal Transfer\",\"recipient_bank\":\"First National Capital FN\"},\"admin_action\":true}', '102.90.98.168', '2026-05-31 22:58:00', '2026-05-31 22:58:00');

-- --------------------------------------------------------

--
-- Table structure for table `two_factor_codes`
--

CREATE TABLE `two_factor_codes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `code` varchar(10) NOT NULL,
  `method` enum('sms','email','app') NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `purpose` varchar(20) NOT NULL DEFAULT 'login'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `two_factor_codes`
--

INSERT INTO `two_factor_codes` (`id`, `user_id`, `code`, `method`, `used`, `expires_at`, `created_at`, `purpose`) VALUES
(367, 134, '971842', 'email', 1, '2026-05-31 01:53:15', '2026-05-31 01:43:15', 'transfer'),
(368, 135, '929293', 'email', 1, '2026-06-02 22:30:56', '2026-06-02 22:20:56', 'login'),
(369, 135, '981022', 'email', 1, '2026-06-02 22:35:58', '2026-06-02 22:25:58', 'login'),
(370, 135, '166049', 'email', 1, '2026-06-02 23:22:07', '2026-06-02 23:12:07', 'login');

-- --------------------------------------------------------

--
-- Table structure for table `update_logs`
--

CREATE TABLE `update_logs` (
  `id` int(11) NOT NULL,
  `version` varchar(20) NOT NULL,
  `applied_date` datetime NOT NULL,
  `applied_by` int(11) DEFAULT NULL,
  `status` enum('success','failed','partial') NOT NULL DEFAULT 'success',
  `log_details` text DEFAULT NULL,
  `files_updated` int(11) DEFAULT 0,
  `migrations_applied` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `role` enum('user','business','admin','support') DEFAULT 'user',
  `is_super_admin` tinyint(1) DEFAULT 0,
  `status` enum('active','suspended','blocked','pending','restricted','closed','hold') DEFAULT 'pending',
  `kyc_status` enum('pending','verified','rejected') DEFAULT 'pending',
  `kyc_prompt_dismissed` tinyint(1) NOT NULL DEFAULT 0,
  `kyc_document_path` varchar(255) DEFAULT NULL,
  `kyc_submitted_at` datetime DEFAULT NULL COMMENT 'Timestamp when user submitted KYC verification',
  `two_factor_enabled` tinyint(1) DEFAULT 0,
  `two_factor_method` enum('sms','email','app') DEFAULT 'email',
  `security_question_1` varchar(255) DEFAULT NULL,
  `security_answer_1` varchar(255) DEFAULT NULL,
  `security_question_2` varchar(255) DEFAULT NULL,
  `security_answer_2` varchar(255) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `email_verified` tinyint(1) DEFAULT 0,
  `phone_verified` tinyint(1) DEFAULT 0,
  `notification_preferences` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`notification_preferences`)),
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `language` varchar(10) DEFAULT 'en',
  `currency` varchar(10) DEFAULT 'USD',
  `investment_balance` decimal(15,2) DEFAULT 0.00,
  `transfer_pin` varchar(255) DEFAULT NULL,
  `security_pin` varchar(255) DEFAULT NULL,
  `login_pin` varchar(255) DEFAULT NULL,
  `onboarding_completed` tinyint(1) DEFAULT 0,
  `transaction_override` varchar(20) DEFAULT 'normal' COMMENT 'Admin override for transaction processing: normal, force_success, force_pending, force_failed',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `currency_selection_shown` tinyint(1) DEFAULT 0,
  `transfer_otp_required` tinyint(1) DEFAULT 1,
  `imf_code` varchar(20) DEFAULT NULL,
  `imf_required` tinyint(1) DEFAULT 0,
  `federal_swift_code` varchar(20) DEFAULT NULL,
  `federal_swift_required` tinyint(1) DEFAULT 0,
  `vat_code` varchar(20) DEFAULT NULL,
  `vat_required` tinyint(1) DEFAULT 0,
  `tac_code` varchar(20) DEFAULT NULL,
  `tac_required` tinyint(1) DEFAULT 0,
  `tin_code` varchar(20) DEFAULT NULL,
  `tin_required` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password_hash`, `full_name`, `phone`, `date_of_birth`, `gender`, `address`, `city`, `state`, `country`, `postal_code`, `profile_picture`, `role`, `is_super_admin`, `status`, `kyc_status`, `kyc_prompt_dismissed`, `kyc_document_path`, `kyc_submitted_at`, `two_factor_enabled`, `two_factor_method`, `security_question_1`, `security_answer_1`, `security_question_2`, `security_answer_2`, `last_login`, `email_verified`, `phone_verified`, `notification_preferences`, `metadata`, `language`, `currency`, `investment_balance`, `transfer_pin`, `security_pin`, `login_pin`, `onboarding_completed`, `transaction_override`, `created_at`, `updated_at`, `currency_selection_shown`, `transfer_otp_required`, `imf_code`, `imf_required`, `federal_swift_code`, `federal_swift_required`, `vat_code`, `vat_required`, `tac_code`, `tac_required`, `tin_code`, `tin_required`) VALUES
(3, 'admin@demo.com', '$2y$10$zmylYDB3CckAH1EVQuQ17uTeu.mPsyS5HcyUfgHOhGmEw6NGoUSmu', 'Admin User', '+1234567891', '1985-01-01', NULL, '456 Admin Avenue', 'Admin City', 'Admin State', 'United States', '54321', NULL, 'admin', 1, 'active', 'verified', 0, NULL, NULL, 0, 'email', NULL, NULL, NULL, NULL, '2026-05-31 03:10:46', 1, 0, NULL, NULL, 'en', 'USD', 0.00, '$2y$10$Q1PjPMemugsGthLoGy37GOFdWdbAKDyk9P8cnGHw3iotKzcR3Iaa6', '$2y$10$ASwi5xJx4ax.EBuEkJVfr.wa15SBxxNbIMQ42fWKvYE/fGB25TATK', '$2y$10$bZlUWmGoHKLIMvACEDK1muZ.b7gCp3lTClANesOuPE1nT8ATEYsD6', 1, 'normal', '2025-10-08 22:44:52', '2026-05-31 03:10:46', 0, 1, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0),
(60, 'support@firstnationalfn.com', '$2y$12$41gAqMmSARORXxe8iqup2OBVNk1mzEC5.xveeCRP9Jqp37I2QH/5.', 'admin user', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '/uploads/profile-pictures/user_60_1773878315.jpeg', 'admin', 0, 'active', 'verified', 0, NULL, NULL, 0, 'email', NULL, NULL, NULL, NULL, '2026-06-02 23:11:50', 1, 0, NULL, '{\"timezone\":\"America\\/New_York\"}', 'en', 'GBP', 0.00, NULL, NULL, NULL, 0, 'normal', '2026-01-29 02:21:13', '2026-06-02 23:11:50', 0, 1, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0),
(134, 'mr.carter.tech07@gmail.com', '$2y$12$UoMAjX65auLONnphd3D1qed.wKwv2avpQcqBbL83FurS7lcLS6NSe', 'carter tech', '+35391347593', '1987-06-25', 'male', '177 Ago Palace Way,, Lagos , Lagos', 'Oshodi Isolo', 'Lagos', 'AE', '110224', NULL, 'user', 0, 'active', 'verified', 0, NULL, '2026-05-31 01:41:31', 1, 'email', 'What city were you born in?', '$2y$12$fkoFmO8aaouTQf2Yyp8p3OPt1GG3lcbOVOlZrKKI.vtULYBWM4S1C', 'What was the name of your first pet?', '$2y$12$Rd4PyvMolIDX0xoxmMg1J.ES.GvYnxQI8XyPmDRNWVYQ0/AXVxUWC', '2026-05-31 01:23:18', 1, 0, '{\"email_notifications\":true,\"sms_notifications\":false,\"transaction_alerts\":true,\"login_alerts\":true,\"marketing_emails\":true}', '{\"timezone\":\"America\\/New_York\"}', 'en', 'AUD', 0.00, '$2y$10$RBdnBPuUDv2aTAzt9Mp7e.7XMcpt2N46QpODI3yeOh97DrakpTT7u', NULL, '$2y$10$xUuiGYaPbGnPiDqFNWVyM.6A0lD56L6ETz7HamLBGdIP/SVvAXjOq', 0, 'normal', '2026-05-31 01:22:17', '2026-06-02 23:10:22', 1, 1, '8520754522', 0, '8040899300', 0, '0754036616', 0, '4982540279', 0, '2786731439', 0),
(135, 'simplyhiredremotejobs@gmail.com', '$2y$12$Z0J/PgyD.GRHyAY9lGkuIubL6XUwHcd2yeGoLgMJmfsWURCuKEoAe', 'Rayo valencano', '8085455457', '1997-05-16', 'male', 'Marina Bay, Dubai', 'Dubai', 'Dubai', 'AE', '353546', NULL, 'user', 0, 'pending', 'pending', 0, NULL, NULL, 1, 'email', 'What city were you born in?', '$2y$12$XuK4VEcrt/lTYq.Urr4BxeQvyoam9A8aHMUmnFAE4whA1y3txUJ9G', 'What was the name of your first pet?', '$2y$12$GZ7vXckD2epWIZo4x7mPa.xKI5lXDBv.geE15h65B8OljfP6a04EO', '2026-06-02 23:12:34', 1, 0, '{\"email_notifications\":true,\"sms_notifications\":false,\"transaction_alerts\":true,\"login_alerts\":true,\"marketing_emails\":true}', '{\"timezone\":\"America\\/New_York\"}', 'en', 'AED', 0.00, '$2y$10$U/AllVP.KZTIKrH4NLEPp.W8NcTAY62Vcb51sSDqPqFURFfAc1wt.', NULL, '$2y$10$9AZ.qt3HCW0E1FxLRryOve96GF2i9blXHa7/ZeMQpq3pimYh6jzoi', 0, 'normal', '2026-05-31 22:38:33', '2026-06-02 23:14:27', 1, 1, '0230761081', 0, '8147711250', 0, '8474568045', 0, '7823190659', 0, '0557398061', 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_investments`
--

CREATE TABLE `user_investments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `amount_principal` decimal(15,2) NOT NULL,
  `duration_days` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `maturity_date` date NOT NULL,
  `status` enum('pending','active','matured','closed','cancelled') DEFAULT 'pending',
  `daily_percent_effective` decimal(10,6) DEFAULT NULL COMMENT 'Effective daily ROI percentage',
  `current_accrued` decimal(15,2) DEFAULT 0.00 COMMENT 'Accumulated ROI to date',
  `last_accrual_date` date DEFAULT NULL,
  `total_roi_paid` decimal(15,2) DEFAULT 0.00 COMMENT 'Total ROI paid out to user',
  `account_used_id` int(11) NOT NULL COMMENT 'Account ID used for investment',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_notes`
--

CREATE TABLE `user_notes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `note` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `account_number` (`account_number`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_account_number` (`account_number`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `account_owners`
--
ALTER TABLE `account_owners`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_account_user` (`account_id`,`user_id`),
  ADD KEY `idx_account_id` (`account_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `admin_audit_logs`
--
ALTER TABLE `admin_audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_admin_id` (`admin_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `admin_sessions`
--
ALTER TABLE `admin_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `session_token` (`session_token`),
  ADD KEY `idx_admin_id` (`admin_id`),
  ADD KEY `idx_session_token` (`session_token`),
  ADD KEY `idx_expires_at` (`expires_at`);

--
-- Indexes for table `banks`
--
ALTER TABLE `banks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_region` (`region`),
  ADD KEY `idx_country` (`country`),
  ADD KEY `idx_bank_name` (`name`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `beneficiaries`
--
ALTER TABLE `beneficiaries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `bill_payments`
--
ALTER TABLE `bill_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_id` (`account_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_payment_date` (`payment_date`);

--
-- Indexes for table `cards`
--
ALTER TABLE `cards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_account_id` (`account_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_card_status` (`status`);

--
-- Indexes for table `card_applications`
--
ALTER TABLE `card_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_account_id` (`account_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_reviewed_by` (`reviewed_by`);

--
-- Indexes for table `card_transactions`
--
ALTER TABLE `card_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_card_id` (`card_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_transaction_type` (`transaction_type`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_reference` (`reference`);

--
-- Indexes for table `crypto_wallets`
--
ALTER TABLE `crypto_wallets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `crypto_type` (`crypto_type`),
  ADD KEY `is_active` (`is_active`);

--
-- Indexes for table `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `email_simulation_alert_captions`
--
ALTER TABLE `email_simulation_alert_captions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `email_simulation_templates`
--
ALTER TABLE `email_simulation_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `template_name` (`template_name`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_name` (`template_name`),
  ADD KEY `idx_type` (`template_type`);

--
-- Indexes for table `email_verification_tokens`
--
ALTER TABLE `email_verification_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_token` (`token`);

--
-- Indexes for table `exchange_rates`
--
ALTER TABLE `exchange_rates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_pair` (`from_currency`,`to_currency`);

--
-- Indexes for table `investment_funding`
--
ALTER TABLE `investment_funding`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `account_id` (`account_id`),
  ADD KEY `status` (`status`),
  ADD KEY `funding_method` (`funding_method`);

--
-- Indexes for table `investment_products`
--
ALTER TABLE `investment_products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_display_order` (`display_order`);

--
-- Indexes for table `investment_transactions`
--
ALTER TABLE `investment_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_investment_id` (`user_investment_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `investment_withdrawals`
--
ALTER TABLE `investment_withdrawals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `status` (`status`),
  ADD KEY `withdrawal_method` (`withdrawal_method`);

--
-- Indexes for table `ip_access_control`
--
ALTER TABLE `ip_access_control`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_ip_address` (`ip_address`),
  ADD KEY `idx_type` (`type`);

--
-- Indexes for table `joint_account_requests`
--
ALTER TABLE `joint_account_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_account_id` (`account_id`),
  ADD KEY `idx_primary_owner_id` (`primary_owner_id`),
  ADD KEY `idx_requesting_user_id` (`requesting_user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_expires_at` (`expires_at`);

--
-- Indexes for table `kyc_beneficial_owners`
--
ALTER TABLE `kyc_beneficial_owners`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_kyc_verification` (`kyc_verification_id`);

--
-- Indexes for table `kyc_verifications`
--
ALTER TABLE `kyc_verifications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id_unique` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `kyc_verifications_ibfk_2` (`verified_by`);

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_id` (`account_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_loan_status` (`status`);

--
-- Indexes for table `loan_payments`
--
ALTER TABLE `loan_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_loan_id` (`loan_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_due_date` (`due_date`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_attempted_at` (`attempted_at`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_is_read` (`is_read`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_expires_at` (`expires_at`);

--
-- Indexes for table `schema_migrations`
--
ALTER TABLE `schema_migrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_migration` (`version`,`migration_name`),
  ADD KEY `idx_version` (`version`),
  ADD KEY `idx_applied_at` (`applied_at`);

--
-- Indexes for table `support_messages`
--
ALTER TABLE `support_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_ticket_id` (`ticket_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ticket_number` (`ticket_number`),
  ADD KEY `assigned_to` (`assigned_to`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_ticket_number` (`ticket_number`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `system_alerts`
--
ALTER TABLE `system_alerts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `related_user_id` (`related_user_id`),
  ADD KEY `related_transaction_id` (`related_transaction_id`),
  ADD KEY `resolved_by` (`resolved_by`),
  ADD KEY `idx_alert_type` (`alert_type`),
  ADD KEY `idx_severity` (`severity`),
  ADD KEY `idx_is_read` (`is_read`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `idx_setting_key` (`setting_key`);

--
-- Indexes for table `system_versions`
--
ALTER TABLE `system_versions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `version` (`version`),
  ADD KEY `idx_version` (`version`),
  ADD KEY `idx_release_date` (`release_date`);

--
-- Indexes for table `system_version_info`
--
ALTER TABLE `system_version_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_info` (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_ref` (`transaction_ref`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_account_id` (`account_id`),
  ADD KEY `idx_transaction_ref` (`transaction_ref`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_transaction_amount` (`amount`),
  ADD KEY `idx_transaction_status_created` (`status`,`created_at`);

--
-- Indexes for table `two_factor_codes`
--
ALTER TABLE `two_factor_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_code` (`code`),
  ADD KEY `idx_expires_at` (`expires_at`);

--
-- Indexes for table `update_logs`
--
ALTER TABLE `update_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_version` (`version`),
  ADD KEY `idx_applied_date` (`applied_date`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_user_status` (`status`),
  ADD KEY `idx_user_kyc_status` (`kyc_status`);

--
-- Indexes for table `user_investments`
--
ALTER TABLE `user_investments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_product_id` (`product_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_maturity_date` (`maturity_date`),
  ADD KEY `idx_last_accrual_date` (`last_accrual_date`),
  ADD KEY `idx_start_date` (`start_date`),
  ADD KEY `user_investments_ibfk_3` (`account_used_id`);

--
-- Indexes for table `user_notes`
--
ALTER TABLE `user_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=130;

--
-- AUTO_INCREMENT for table `account_owners`
--
ALTER TABLE `account_owners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2193;

--
-- AUTO_INCREMENT for table `admin_audit_logs`
--
ALTER TABLE `admin_audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=258;

--
-- AUTO_INCREMENT for table `admin_sessions`
--
ALTER TABLE `admin_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `banks`
--
ALTER TABLE `banks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=287;

--
-- AUTO_INCREMENT for table `beneficiaries`
--
ALTER TABLE `beneficiaries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bill_payments`
--
ALTER TABLE `bill_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cards`
--
ALTER TABLE `cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `card_applications`
--
ALTER TABLE `card_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `card_transactions`
--
ALTER TABLE `card_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `crypto_wallets`
--
ALTER TABLE `crypto_wallets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `email_simulation_alert_captions`
--
ALTER TABLE `email_simulation_alert_captions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `email_simulation_templates`
--
ALTER TABLE `email_simulation_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `email_verification_tokens`
--
ALTER TABLE `email_verification_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=138;

--
-- AUTO_INCREMENT for table `exchange_rates`
--
ALTER TABLE `exchange_rates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=433;

--
-- AUTO_INCREMENT for table `investment_funding`
--
ALTER TABLE `investment_funding`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `investment_products`
--
ALTER TABLE `investment_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `investment_transactions`
--
ALTER TABLE `investment_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `investment_withdrawals`
--
ALTER TABLE `investment_withdrawals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ip_access_control`
--
ALTER TABLE `ip_access_control`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `joint_account_requests`
--
ALTER TABLE `joint_account_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kyc_beneficial_owners`
--
ALTER TABLE `kyc_beneficial_owners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kyc_verifications`
--
ALTER TABLE `kyc_verifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `loan_payments`
--
ALTER TABLE `loan_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=145;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=265;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=155;

--
-- AUTO_INCREMENT for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `schema_migrations`
--
ALTER TABLE `schema_migrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `support_messages`
--
ALTER TABLE `support_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_alerts`
--
ALTER TABLE `system_alerts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1717;

--
-- AUTO_INCREMENT for table `system_versions`
--
ALTER TABLE `system_versions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_version_info`
--
ALTER TABLE `system_version_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=649;

--
-- AUTO_INCREMENT for table `two_factor_codes`
--
ALTER TABLE `two_factor_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=371;

--
-- AUTO_INCREMENT for table `update_logs`
--
ALTER TABLE `update_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=136;

--
-- AUTO_INCREMENT for table `user_investments`
--
ALTER TABLE `user_investments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_notes`
--
ALTER TABLE `user_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `accounts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `account_owners`
--
ALTER TABLE `account_owners`
  ADD CONSTRAINT `account_owners_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `account_owners_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `admin_audit_logs`
--
ALTER TABLE `admin_audit_logs`
  ADD CONSTRAINT `admin_audit_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `admin_sessions`
--
ALTER TABLE `admin_sessions`
  ADD CONSTRAINT `admin_sessions_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `beneficiaries`
--
ALTER TABLE `beneficiaries`
  ADD CONSTRAINT `beneficiaries_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bill_payments`
--
ALTER TABLE `bill_payments`
  ADD CONSTRAINT `bill_payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bill_payments_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cards`
--
ALTER TABLE `cards`
  ADD CONSTRAINT `cards_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cards_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `card_transactions`
--
ALTER TABLE `card_transactions`
  ADD CONSTRAINT `fk_card_transactions_card_id` FOREIGN KEY (`card_id`) REFERENCES `cards` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_card_transactions_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `email_verification_tokens`
--
ALTER TABLE `email_verification_tokens`
  ADD CONSTRAINT `email_verification_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `investment_funding`
--
ALTER TABLE `investment_funding`
  ADD CONSTRAINT `investment_funding_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `investment_funding_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `investment_transactions`
--
ALTER TABLE `investment_transactions`
  ADD CONSTRAINT `investment_transactions_ibfk_1` FOREIGN KEY (`user_investment_id`) REFERENCES `user_investments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `investment_transactions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `investment_withdrawals`
--
ALTER TABLE `investment_withdrawals`
  ADD CONSTRAINT `investment_withdrawals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ip_access_control`
--
ALTER TABLE `ip_access_control`
  ADD CONSTRAINT `ip_access_control_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `joint_account_requests`
--
ALTER TABLE `joint_account_requests`
  ADD CONSTRAINT `joint_account_requests_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `joint_account_requests_ibfk_2` FOREIGN KEY (`primary_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `joint_account_requests_ibfk_3` FOREIGN KEY (`requesting_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kyc_beneficial_owners`
--
ALTER TABLE `kyc_beneficial_owners`
  ADD CONSTRAINT `kyc_beneficial_owners_ibfk_1` FOREIGN KEY (`kyc_verification_id`) REFERENCES `kyc_verifications` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kyc_verifications`
--
ALTER TABLE `kyc_verifications`
  ADD CONSTRAINT `kyc_verifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kyc_verifications_ibfk_2` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `loans`
--
ALTER TABLE `loans`
  ADD CONSTRAINT `loans_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `loans_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `loan_payments`
--
ALTER TABLE `loan_payments`
  ADD CONSTRAINT `loan_payments_ibfk_1` FOREIGN KEY (`loan_id`) REFERENCES `loans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD CONSTRAINT `password_reset_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `support_messages`
--
ALTER TABLE `support_messages`
  ADD CONSTRAINT `support_messages_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `support_messages_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD CONSTRAINT `support_tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `support_tickets_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `system_alerts`
--
ALTER TABLE `system_alerts`
  ADD CONSTRAINT `system_alerts_ibfk_1` FOREIGN KEY (`related_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `system_alerts_ibfk_2` FOREIGN KEY (`related_transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `system_alerts_ibfk_3` FOREIGN KEY (`resolved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `two_factor_codes`
--
ALTER TABLE `two_factor_codes`
  ADD CONSTRAINT `two_factor_codes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_investments`
--
ALTER TABLE `user_investments`
  ADD CONSTRAINT `user_investments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_investments_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `investment_products` (`id`),
  ADD CONSTRAINT `user_investments_ibfk_3` FOREIGN KEY (`account_used_id`) REFERENCES `accounts` (`id`);

--
-- Constraints for table `user_notes`
--
ALTER TABLE `user_notes`
  ADD CONSTRAINT `user_notes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_notes_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
