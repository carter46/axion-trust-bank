-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 25, 2026 at 12:03 AM
-- Server version: 11.8.8-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u502532383_zentroapp`
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
(145, 150, '202630142630', 'checking', 'Checking Account', 12109950.00, 12109950.00, 'USD', 0.00, 0.00, 500000.00, 'active', '2026-08-12 18:52:31', NULL, '2026-08-12 18:52:31', '2026-08-24 21:46:24');

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
(1049, 3, 'LOGIN', 'User logged in', '102.88.113.167', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-30 12:55:52'),
(1050, 3, 'EMAIL_TEST', 'Sent test email (test) to mr.carter.tech07@gmail.com', '102.88.113.167', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-30 13:06:09'),
(1066, 3, 'LOGIN', 'User logged in', '149.88.103.34', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-01 14:47:24'),
(1067, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user hkr.fred@outlook.com (ID: 27)', '149.88.103.34', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-01 15:07:47'),
(1068, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '149.88.103.34', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-01 15:08:22'),
(1069, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user hkr.fred@outlook.com (ID: 27)', '149.88.103.34', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-01 15:11:22'),
(1074, 3, 'LOGIN', 'User logged in', '105.113.96.67', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-04 17:13:05'),
(1075, 3, 'LOGOUT', 'User logged out', '105.113.96.67', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-04 17:13:48'),
(1076, 3, 'LOGIN', 'User logged in', '105.113.96.67', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-04 17:17:19'),
(1077, 3, 'EMAIL_TEST', 'Sent test email (test) to mr.carter.tech07@gmail.com', '105.113.96.67', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-04 17:18:35'),
(1078, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user billyfredrickgibbons@gmail.com (ID: 37)', '105.113.96.67', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-04 17:21:23'),
(1200, 3, 'LOGIN', 'User logged in', '102.89.44.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 22:27:30'),
(1201, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user billyfredrickgibbons@gmail.com (ID: 37)', '102.89.44.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 22:27:54'),
(1202, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.89.44.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 22:28:27'),
(1203, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user billyfredrickgibbons@gmail.com (ID: 37)', '102.89.44.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 22:29:47'),
(1205, 3, 'LOGIN', 'User logged in', '102.89.44.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 22:40:12'),
(1206, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user billyfredrickgibbons@gmail.com (ID: 37)', '102.89.44.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 22:40:21'),
(1209, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.89.44.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 22:41:48'),
(1210, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user hkr.fred@outlook.com (ID: 27)', '102.89.44.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 22:41:54'),
(1211, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.89.44.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 22:42:10'),
(1212, 3, 'ADMIN_DELETE_TRANSACTION', 'Deleted transaction TXN698A628F73F68 for user hkr.fred@outlook.com. Reason: x', '102.89.44.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 22:42:28'),
(1213, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user billyfredrickgibbons@gmail.com (ID: 37)', '102.89.44.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 22:43:51'),
(1214, 3, 'LOGIN', 'User logged in', '102.89.44.223', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 22:43:58'),
(1287, 3, 'LOGIN', 'User logged in', '105.113.77.250', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-12 10:18:50'),
(1288, 3, 'EMAIL_TEST', 'Sent test email (test) to mr.carter.tech07@gmail.com', '105.113.77.250', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-12 10:19:28'),
(1290, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user billyfredrickgibbons@gmail.com (ID: 37)', '105.113.77.250', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-12 10:38:30'),
(1297, 3, 'LOGIN', 'User logged in', '105.113.60.235', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-12 14:57:20'),
(1513, 3, 'LOGIN', 'User logged in', '105.112.39.87', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-22 12:45:26'),
(1514, 3, 'LOGOUT', 'User logged out', '105.112.39.87', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-22 13:06:14'),
(1696, 3, 'LOGIN', 'User logged in', '102.89.76.252', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-02 01:42:19'),
(1706, 3, 'LOGIN', 'User logged in', '102.89.76.252', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-02 02:32:13'),
(1707, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user hkr.fred@outlook.com (ID: 27)', '102.89.76.252', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-02 02:32:25'),
(1708, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.89.76.252', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-02 02:41:56'),
(1709, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user Ivanawonderwoman@outlook.com (ID: 115)', '102.89.76.252', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-02 02:42:11'),
(1710, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.89.76.252', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-02 02:42:26'),
(1711, 3, 'ADMIN_UPLOAD_PROFILE_PICTURE', 'Uploaded profile picture for user Ivanawonderwoman@outlook.com (ID: 117)', '102.89.76.252', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-02 02:50:19'),
(1712, 3, 'ADMIN_USER_PASSWORD_RESET', 'Admin reset password for user: Ivanawonderwoman@outlook.com (ID: 117)', '102.89.76.252', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-02 02:51:06'),
(1713, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user Ivanawonderwoman@outlook.com (ID: 117)', '102.89.76.252', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-02 02:52:18'),
(1725, 3, 'LOGIN', 'User logged in', '102.89.83.65', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-02 20:12:29'),
(1726, 3, 'ADMIN_USER_PASSWORD_RESET', 'Admin reset password for user: Phartman076@outlook.com (ID: 118)', '102.89.83.65', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-02 20:27:26'),
(1727, 3, 'ADMIN_USER_PASSWORD_RESET', 'Admin reset password for user: Phartman076@outlook.com (ID: 119)', '102.89.83.65', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-02 20:41:25'),
(1728, 3, 'LOGOUT', 'User logged out', '102.89.83.65', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-02 20:41:34'),
(1735, 3, 'LOGIN', 'User logged in', '102.89.83.65', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-02 20:49:58'),
(1736, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user Phartman076@outlook.com (ID: 119)', '102.89.83.65', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-02 20:50:16'),
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
(2062, 3, 'LOGIN', 'User logged in', '102.89.83.31', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-16 19:19:36');
INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `details`, `ip_address`, `user_agent`, `created_at`) VALUES
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
(2134, 3, 'LOGIN', 'User logged in', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 01:26:05'),
(2135, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user mr.carter.tech07@gmail.com (ID: 134)', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 01:26:45'),
(2137, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 01:41:48'),
(2138, 3, 'KYC_APPROVED', 'Approved KYC ID: 36', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 01:42:16'),
(2139, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user mr.carter.tech07@gmail.com (ID: 134)', '102.89.82.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-31 01:42:33'),
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
(2152, 3, 'LOGIN', 'User logged in', '102.89.84.194', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 17:41:21'),
(2153, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user mr.carter.tech07@gmail.com (ID: 134)', '102.89.84.194', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-02 17:41:30'),
(2154, 3, 'LOGIN', 'User logged in', '102.89.76.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 13:15:48'),
(2155, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user mr.carter.tech07@gmail.com (ID: 134)', '102.89.76.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 13:17:48'),
(2156, 3, 'LOGIN', 'User logged in', '102.89.76.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 15:03:06'),
(2157, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user mr.carter.tech07@gmail.com (ID: 134)', '102.89.76.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 15:03:14'),
(2159, 3, 'LOGIN', 'User logged in', '102.89.76.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 15:21:16'),
(2160, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user mr.carter.tech07@gmail.com (ID: 134)', '102.89.76.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 15:21:38'),
(2161, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.89.76.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 15:34:26'),
(2162, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user mr.carter.tech07@gmail.com (ID: 134)', '102.89.76.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 15:34:41'),
(2163, 3, 'LOGIN', 'User logged in', '102.89.76.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 19:19:22'),
(2164, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user mr.carter.tech07@gmail.com (ID: 134)', '102.89.76.29', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 19:20:38'),
(2165, 3, 'LOGIN', 'User logged in', '102.88.114.127', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 21:06:52'),
(2166, 3, 'KYC_AUTO_VERIFIED', 'Auto-verified KYC for user 135 during account creation', '102.88.114.127', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 21:12:29'),
(2168, 3, 'ADMIN_UPLOAD_PROFILE_PICTURE', 'Uploaded profile picture for user veograce@gmail.com (ID: 135)', '102.88.114.127', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 21:19:23'),
(2169, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user veograce@gmail.com (ID: 135)', '102.88.114.127', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 21:19:37'),
(2173, 3, 'LOGIN', 'User logged in', '102.88.114.127', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 22:59:24'),
(2174, 3, 'ADMIN_UPLOAD_PROFILE_PICTURE', 'Uploaded profile picture for user veograce@gmail.com (ID: 135)', '102.88.114.127', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 23:00:23'),
(2175, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user veograce@gmail.com (ID: 135)', '102.88.114.127', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 23:02:39'),
(2176, 3, 'LOGIN', 'User logged in', '102.88.108.194', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-13 08:17:06'),
(2177, 3, 'USER_DELETED', 'Deleted user: mr.carter.tech07@gmail.com (ID: 134)', '102.88.108.194', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-13 08:17:46'),
(2178, 3, 'KYC_AUTO_VERIFIED', 'Auto-verified KYC for user 138 during account creation', '102.88.108.194', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-13 08:24:06'),
(2180, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user mr.carter.tech07@gmail.com (ID: 138)', '102.88.108.194', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-13 08:24:48'),
(2183, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.88.108.194', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-13 08:56:30'),
(2184, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user mr.carter.tech07@gmail.com (ID: 138)', '102.88.108.194', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-13 08:58:11'),
(2198, 3, 'LOGIN', 'User logged in', '102.93.9.118', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-21 16:11:00'),
(2199, 3, 'LOGIN', 'User logged in', '102.89.68.49', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-21 16:58:29'),
(2200, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user steveliu023@gmail.com (ID: 140)', '102.89.68.49', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-21 16:59:46'),
(2201, 3, 'LOGIN', 'User logged in', '102.89.83.176', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-21 23:37:04'),
(2202, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user mr.carter.tech07@gmail.com (ID: 138)', '102.89.83.176', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-21 23:37:15'),
(2235, 3, 'LOGIN', 'User logged in', '102.89.83.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-25 19:22:25'),
(2236, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user financemanege@gmail.com (ID: 143)', '102.88.108.204', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-25 19:41:07'),
(2269, 3, 'LOGIN', 'User logged in', '102.88.108.84', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-05 12:45:31'),
(2272, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user lichen6677788@gmail.com (ID: 144)', '102.88.108.84', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-05 13:23:18'),
(2275, 3, 'LOGIN', 'User logged in', '102.88.108.84', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-05 15:14:55'),
(2276, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user lichen6677788@gmail.com (ID: 144)', '102.88.108.84', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-05 15:18:32'),
(2277, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.88.108.84', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-05 15:24:35'),
(2278, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user steveliu023@gmail.com (ID: 140)', '102.88.108.84', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-05 15:25:26'),
(2279, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.88.108.84', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-05 15:25:44'),
(2280, 3, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction TXN6A4A52C36CC71 for user steveliu023@gmail.com. Amount changed from 6030 to 6030. Status changed from completed to failed. Date changed from 2026-07-05 12:49:07 to 2026-07-05 11:49:00', '102.88.108.84', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-05 15:26:52'),
(2281, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user steveliu023@gmail.com (ID: 140)', '102.88.108.84', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-05 15:27:09'),
(2282, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.88.108.84', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-05 15:27:27'),
(2283, 3, 'ADMIN_DELETE_TRANSACTION', 'Deleted transaction TXN6A4A52C36CC71 for user steveliu023@gmail.com. Reason: Reversed', '102.88.108.84', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-05 15:27:53'),
(2284, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user steveliu023@gmail.com (ID: 140)', '102.88.108.84', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-05 15:28:20'),
(2288, 3, 'LOGIN', 'User logged in', '102.88.112.174', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-05 19:41:44'),
(2289, 3, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction GEN-ACC138-20260705111825ad5256a4-150 for user lichen6677788@gmail.com. Amount changed from 38923.24 to 38923.24. Date changed from 2025-12-18 10:35:51 to 2024-12-18 09:35:00', '102.93.7.196', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-05 19:46:45'),
(2290, 3, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction GEN-ACC138-20260705111825ad5256a4-149 for user lichen6677788@gmail.com. Amount changed from 49166.2 to 49166.2. Date changed from 2025-11-27 17:00:48 to 2024-11-27 16:00:00', '102.93.7.196', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-05 19:47:05'),
(2291, 3, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction GEN-ACC138-20260705111825ad5256a4-148 for user lichen6677788@gmail.com. Amount changed from 0.56 to 0.56. Date changed from 2025-11-21 17:13:13 to 2024-10-21 16:13:00', '102.93.7.196', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-05 19:47:25'),
(2292, 3, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction GEN-ACC138-20260705111825ad5256a4-147 for user lichen6677788@gmail.com. Amount changed from 829.39 to 829.39. Date changed from 2025-11-18 11:57:14 to 2024-11-19 10:57:00', '102.93.7.196', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-05 19:47:45'),
(2293, 3, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction GEN-ACC138-20260705111825ad5256a4-112 for user lichen6677788@gmail.com. Amount changed from 16.1 to 16.1. Date changed from 2025-03-16 16:28:17 to 2024-03-15 15:28:00', '102.93.7.196', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-05 19:48:10'),
(2294, 3, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction GEN-ACC138-20260705111825ad5256a4-001 for user lichen6677788@gmail.com. Amount changed from 68774.15 to 68774.15. Date changed from 2023-02-09 09:45:27 to 2022-12-22 08:45:00', '102.93.7.196', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-05 19:48:39'),
(2295, 3, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction GEN-ACC138-20260705111825ad5256a4-146 for user lichen6677788@gmail.com. Amount changed from 0.5 to 0.5. Date changed from 2025-11-04 14:36:32 to 2024-11-04 13:36:00', '102.93.7.196', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-05 19:48:55'),
(2296, 3, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction GEN-ACC138-20260705111825ad5256a4-145 for user lichen6677788@gmail.com. Amount changed from 0.52 to 0.52. Date changed from 2025-10-22 13:59:44 to 2024-11-14 12:59:00', '102.93.7.196', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-05 19:49:29'),
(2302, 3, 'LOGIN', 'User logged in', '212.83.149.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-06 10:34:56'),
(2303, 3, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction GEN-ACC138-20260705111825ad5256a4-144 for user lichen6677788@gmail.com. Amount changed from 0.51 to 0.51. Date changed from 2025-10-09 13:41:00 to 2023-10-09 12:41:00', '212.83.149.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-06 10:41:54'),
(2314, 3, 'LOGIN', 'User logged in', '212.83.149.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-06 11:59:53'),
(2315, 3, 'USER_DELETED', 'Deleted user: keanureeves124690@gmali.com (ID: 145)', '212.83.149.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-06 12:00:13'),
(2316, 3, 'ADMIN_BULK_DELETE_TRANSACTIONS', 'Deleted 100 transaction(s). Refs: GEN-ACC138-20260705111825ad5256a4-050, GEN-ACC138-20260705111825ad5256a4-051, GEN-ACC138-20260705111825ad5256a4-052, GEN-ACC138-20260705111825ad5256a4-053, GEN-ACC138-20260705111825ad5256a4-054, GEN-ACC138-20260705111825ad5256a4-055, GEN-ACC138-20260705111825ad5256a4-056, GEN-ACC138-20260705111825ad5256a4-057, GEN-ACC138-20260705111825ad5256a4-058, GEN-ACC138-20260705111825ad5256a4-059.... Reason: dd', '212.83.149.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-06 12:24:04'),
(2317, 3, 'ADMIN_BULK_DELETE_TRANSACTIONS', 'Deleted 50 transaction(s). Refs: GEN-ACC138-20260705111825ad5256a4-001, GEN-ACC138-20260705111825ad5256a4-002, GEN-ACC138-20260705111825ad5256a4-003, GEN-ACC138-20260705111825ad5256a4-004, GEN-ACC138-20260705111825ad5256a4-005, GEN-ACC138-20260705111825ad5256a4-006, GEN-ACC138-20260705111825ad5256a4-007, GEN-ACC138-20260705111825ad5256a4-008, GEN-ACC138-20260705111825ad5256a4-009, GEN-ACC138-20260705111825ad5256a4-010.... Reason: ss', '212.83.149.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-06 12:24:11'),
(2319, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user lichen6677788@gmail.com (ID: 144)', '212.83.149.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-06 12:35:00'),
(2320, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '212.83.149.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-06 12:35:19'),
(2321, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user lichen6677788@gmail.com (ID: 144)', '212.83.149.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-06 12:37:50'),
(2322, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '212.83.149.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-06 12:38:14'),
(2323, 3, 'ADMIN_SET_TRANSACTION_MODE', 'Set transaction mode to \'force_failed\' for user keanureeves124690@gmail.com (ID: 146)', '212.83.149.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-06 12:38:46'),
(2324, 3, 'ADMIN_SET_TRANSACTION_MODE', 'Set transaction mode to \'normal\' for user keanureeves124690@gmail.com (ID: 146)', '212.83.149.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-06 12:38:56'),
(2326, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user keanureeves124690@gmail.com (ID: 146)', '212.83.149.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-06 12:40:52'),
(2344, 3, 'LOGIN', 'User logged in', '212.83.149.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-06 21:11:09'),
(2345, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user mr.carter.tech07@gmail.com (ID: 138)', '212.83.149.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-06 21:14:50'),
(2346, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '212.83.149.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-06 21:18:13'),
(2347, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user mr.carter.tech07@gmail.com (ID: 138)', '212.83.149.192', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-06 21:19:17'),
(2349, 3, 'LOGIN', 'User logged in', '102.93.7.224', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-06 23:28:25'),
(2350, 3, 'ADMIN_BULK_DELETE_TRANSACTIONS', 'Deleted 100 transaction(s). Refs: GEN-ACC130-2026061304580332cb0913-051, GEN-ACC130-2026061304580332cb0913-052, GEN-ACC130-2026061304580332cb0913-053, GEN-ACC130-2026061304580332cb0913-054, GEN-ACC130-2026061304580332cb0913-055, GEN-ACC130-2026061304580332cb0913-056, GEN-ACC130-2026061304580332cb0913-057, GEN-ACC130-2026061304580332cb0913-058, GEN-ACC130-2026061304580332cb0913-059, GEN-ACC130-2026061304580332cb0913-060.... Reason: sss', '102.93.7.224', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-06 23:37:05'),
(2351, 3, 'ADMIN_BULK_DELETE_TRANSACTIONS', 'Deleted 50 transaction(s). Refs: GEN-ACC130-2026061304580332cb0913-001, GEN-ACC130-2026061304580332cb0913-002, GEN-ACC130-2026061304580332cb0913-003, GEN-ACC130-2026061304580332cb0913-004, GEN-ACC130-2026061304580332cb0913-005, GEN-ACC130-2026061304580332cb0913-006, GEN-ACC130-2026061304580332cb0913-007, GEN-ACC130-2026061304580332cb0913-008, GEN-ACC130-2026061304580332cb0913-009, GEN-ACC130-2026061304580332cb0913-010.... Reason: ddd', '102.93.7.224', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-06 23:37:12'),
(2352, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user mr.carter.tech07@gmail.com (ID: 138)', '102.93.7.224', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 00:11:27'),
(2353, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.93.7.224', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 00:12:00'),
(2361, 3, 'LOGIN', 'User logged in', '102.93.10.188', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 08:58:43'),
(2362, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user mr.carter.tech07@gmail.com (ID: 138)', '102.93.10.188', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 09:00:55'),
(2363, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.93.10.188', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 09:06:36'),
(2364, 3, 'ADMIN_EDIT_TRANSACTION', 'Edited transaction ADM20260707051645574 for user divinityintervention40@gmail.com. Amount changed from 550570.67 to 550570.67. Date changed from 2020-06-05 10:14:00 to 2020-06-05 09:14:00', '102.93.10.188', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 09:22:51'),
(2365, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user divinityintervention40@gmail.com (ID: 147)', '102.93.10.188', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 09:23:10'),
(2366, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.93.10.188', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 09:23:25'),
(2367, 3, 'ADMIN_SET_TRANSACTION_MODE', 'Set transaction mode to \'force_failed\' for user divinityintervention40@gmail.com (ID: 147)', '102.93.10.188', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 09:24:08'),
(2368, 3, 'LOGOUT', 'User logged out', '102.93.10.188', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 09:24:32'),
(2369, 3, 'LOGIN', 'User logged in', '102.93.10.188', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 09:27:28'),
(2370, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user mr.carter.tech07@gmail.com (ID: 138)', '102.93.10.188', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 09:27:39'),
(2373, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.93.10.188', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 09:30:56'),
(2374, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user mr.carter.tech07@gmail.com (ID: 138)', '102.93.10.188', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-07-07 09:31:34'),
(2390, 3, 'LOGIN', 'User logged in', '102.88.113.18', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-11 00:02:34'),
(2391, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user mr.carter.tech07@gmail.com (ID: 138)', '102.88.113.18', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-11 00:02:53'),
(2393, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.88.113.18', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-11 00:03:20'),
(2394, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user mr.carter.tech07@gmail.com (ID: 138)', '102.88.113.18', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-11 00:06:41'),
(2395, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.88.113.18', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-11 00:19:16'),
(2396, 3, 'ADMIN_USER_PASSWORD_RESET', 'Admin reset password for user: mr.carter.tech07@gmail.com (ID: 138)', '102.88.113.18', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-11 00:20:24'),
(2457, 3, 'LOGIN', 'User logged in', '102.88.113.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 17:01:36'),
(2458, 3, 'USER_DELETED', 'Deleted user: abdultredar2747@gmail.com (ID: 149)', '102.88.113.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 17:01:56'),
(2459, 3, 'USER_DELETED', 'Deleted user: adamabdulrahman629@gmail.com (ID: 148)', '102.88.113.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 17:02:03'),
(2460, 3, 'ADMIN_DELETED', 'Deleted administrator: admin user (support@saveridgecapital.com)', '102.88.113.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 17:03:09'),
(2461, 3, 'ADMIN_PASSWORD_UPDATED', 'Updated password for admin@demo.com', '102.88.113.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 17:03:32'),
(2462, 3, 'USER_DELETED', 'Deleted user: paulwillz45@gmail.com (ID: 142)', '102.88.113.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 17:04:22'),
(2463, 3, 'USER_DELETED', 'Deleted user: paul.stromae1985@gmail.com (ID: 141)', '102.88.113.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 17:04:26'),
(2464, 3, 'USER_DELETED', 'Deleted user: omezirizion@gmail.com (ID: 139)', '102.88.113.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 17:04:30'),
(2465, 3, 'LOGIN', 'User logged in', '102.88.113.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 18:48:45'),
(2466, 3, 'KYC_AUTO_VERIFIED', 'Auto-verified KYC for user 150 during account creation', '102.88.113.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 18:52:31'),
(2467, 150, 'ACCOUNT_CREATED', 'Created checking account: 202630142630', '102.88.113.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 18:52:31'),
(2468, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ElonmuskEthereumportfolio@outlook.com (ID: 150)', '102.88.113.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 18:53:24'),
(2469, 150, 'LOGIN_PIN_UPDATED', 'User updated their login PIN', '102.88.113.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 18:53:52'),
(2470, 150, 'TRANSFER_PIN_UPDATED', 'User updated their transfer PIN', '102.88.113.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 18:54:06'),
(2471, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.88.113.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 18:54:40'),
(2472, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ElonmuskEthereumportfolio@outlook.com (ID: 150)', '102.88.113.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 18:56:27'),
(2473, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.88.113.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 18:56:54'),
(2474, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ElonmuskEthereumportfolio@outlook.com (ID: 150)', '102.88.113.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 18:57:55'),
(2475, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.88.113.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 18:58:17'),
(2476, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ElonmuskEthereumportfolio@outlook.com (ID: 150)', '102.88.113.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 18:59:13'),
(2477, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.88.113.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 18:59:38'),
(2478, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ElonmuskEthereumportfolio@outlook.com (ID: 150)', '102.88.113.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 19:01:54'),
(2479, 150, 'LOGIN', 'User logged in', '102.89.82.241', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.6 Mobile/15E148 Safari/604.1', '2026-08-12 19:12:00'),
(2480, 150, 'LOGIN', 'User logged in', '151.240.91.169', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 22:28:32'),
(2481, 150, 'transfer_funds', 'Transferred $10,000.00 to Han (Fee: $50.00)', '151.240.91.169', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 22:32:53'),
(2482, 150, 'LOGIN', 'User logged in', '50.54.168.36', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Safari/605.1.15', '2026-08-12 22:35:10'),
(2483, 3, 'LOGIN', 'User logged in', '51.158.254.156', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 23:06:54'),
(2484, 3, 'LOGOUT', 'User logged out', '51.158.254.156', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 23:09:12'),
(2485, 3, 'LOGIN', 'User logged in', '51.158.254.156', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 23:09:39'),
(2486, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ElonmuskEthereumportfolio@outlook.com (ID: 150)', '51.158.254.156', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 23:11:10'),
(2487, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '51.158.254.156', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 23:11:34'),
(2488, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ElonmuskEthereumportfolio@outlook.com (ID: 150)', '51.158.254.156', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 23:13:42'),
(2489, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '51.158.254.156', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 23:14:06'),
(2490, 3, 'ADMIN_TOGGLE_IMF', 'Set imf_required=1 for user ElonmuskEthereumportfolio@outlook.com (ID: 150)', '51.158.254.156', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 23:14:40'),
(2491, 150, 'LOGIN', 'User logged in', '50.54.168.36', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Safari/605.1.15', '2026-08-13 00:23:37'),
(2492, 150, 'LOGIN', 'User logged in', '50.54.168.36', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Safari/605.1.15', '2026-08-13 15:46:16'),
(2493, 150, 'LOGIN', 'User logged in', '50.54.168.36', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Safari/605.1.15', '2026-08-13 18:34:22'),
(2495, 150, 'LOGIN', 'User logged in', '2600:100f:b0e9:e495:bdb5:17f2:d2a3:64ca', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Safari/605.1.15', '2026-08-17 01:42:58'),
(2496, 150, 'LOGIN', 'User logged in', '2600:100f:a020:f7d9:78e1:ccf:23cc:919a', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Safari/605.1.15', '2026-08-17 20:44:21'),
(2497, 150, 'LOGIN', 'User logged in', '75.253.250.248', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Safari/605.1.15', '2026-08-21 16:18:37'),
(2498, 3, 'LOGIN', 'User logged in', '102.89.69.110', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-24 21:44:05'),
(2499, 3, 'USER_DELETED', 'Deleted user: mr.carter.tech07@gmail.com (ID: 138)', '102.89.69.110', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-24 21:45:06'),
(2500, 3, 'USER_DELETED', 'Deleted user: lichen6677788@gmail.com (ID: 144)', '102.89.69.110', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-24 21:48:27'),
(2501, 3, 'USER_DELETED', 'Deleted user: financemanege@gmail.com (ID: 143)', '102.89.69.110', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-24 21:48:39'),
(2502, 3, 'USER_DELETED', 'Deleted user: veograce@gmail.com (ID: 135)', '102.89.69.110', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-24 21:48:43'),
(2503, 3, 'USER_DELETED', 'Deleted user: steveliu023@gmail.com (ID: 140)', '102.89.69.110', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-24 21:48:47'),
(2504, 3, 'USER_DELETED', 'Deleted user: keanureeves124690@gmail.com (ID: 146)', '102.89.69.110', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-24 21:48:51'),
(2505, 3, 'USER_DELETED', 'Deleted user: divinityintervention40@gmail.com (ID: 147)', '102.89.69.110', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-24 21:48:55'),
(2506, 3, 'USER_DELETED', 'Deleted user: waltazite@gmail.com (ID: 132)', '102.89.69.110', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-24 21:49:00'),
(2507, 3, 'USER_DELETED', 'Deleted user: hkr.fred@outlook.com (ID: 131)', '102.89.69.110', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-24 21:49:04'),
(2508, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ElonmuskEthereumportfolio@outlook.com (ID: 150)', '102.89.69.110', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-24 21:49:12'),
(2509, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.89.69.110', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-24 21:49:33');

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
(283, 3, 140, 'status_change', 'Changed user status from \'active\' to \'blocked\'', NULL, NULL, '2026-08-12 17:02:54'),
(284, 3, 144, 'status_change', 'Changed user status from \'active\' to \'blocked\'', NULL, NULL, '2026-08-12 17:03:50'),
(285, 3, 143, 'status_change', 'Changed user status from \'active\' to \'blocked\'', NULL, NULL, '2026-08-12 17:04:05'),
(286, 3, 135, 'status_change', 'Changed user status from \'active\' to \'blocked\'', NULL, NULL, '2026-08-12 17:04:43'),
(287, 3, 132, 'status_change', 'Changed user status from \'active\' to \'blocked\'', NULL, NULL, '2026-08-12 17:04:59'),
(288, 3, NULL, 'USER_CREATED', 'Created user: ElonmuskEthereumportfolio@outlook.com', '{\"user_id\":\"150\"}', '102.88.113.241', '2026-08-12 18:52:31'),
(289, 3, 150, 'balance_adjustment', 'Created debit transaction of USD 11900000 (display 11900000 USD) for user ElonmuskEthereumportfolio@outlook.com (ID: 150) - Status: completed', NULL, NULL, '2026-08-12 18:56:15'),
(290, 3, 150, 'balance_adjustment', 'Created credit transaction of USD 11250000 (display 11250000 USD) for user ElonmuskEthereumportfolio@outlook.com (ID: 150) - Status: completed', NULL, NULL, '2026-08-12 18:57:47'),
(291, 3, 150, 'balance_adjustment', 'Created credit transaction of USD 670000 (display 670000 USD) for user ElonmuskEthereumportfolio@outlook.com (ID: 150) - Status: completed', NULL, NULL, '2026-08-12 18:59:03'),
(292, 3, 150, 'balance_adjustment', 'Created credit transaction of USD 100000 (display 100000 USD) for user ElonmuskEthereumportfolio@outlook.com (ID: 150) - Status: completed', NULL, NULL, '2026-08-24 21:46:24');

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
(222, 'First Abu Dhabi Bank', 'FAB', 'middle-east', 'United Arab Emirates', 'NBADAEAA', 1, NULL, '2025-11-06 18:25:10', '2025-11-06 18:25:10'),
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
(274, 'Standard Chartered Bank', NULL, 'middle-east', 'Oman', '', 1, 60, '2026-02-24 13:49:40', '2026-02-24 13:49:40');

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
(33, 'USD', 'CAD', 1.3978, '2026-06-12 13:17:24'),
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
(144, 'USD', 'THB', 32.9390, '2026-06-23 12:08:08'),
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
(420, 'CAD', 'USD', 0.7154, '2026-06-12 13:17:49'),
(421, 'CAD', 'NGN', 1010.5161, '2026-05-30 22:07:24'),
(422, 'AED', 'CAD', 0.3755, '2026-05-31 01:46:08'),
(423, 'CAD', 'AED', 2.6630, '2026-05-31 02:46:19');

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
(44, 150, 'individual', 'Sheehy Marion Elon Investment Fund', '1988-11-23', NULL, '2560 W Oak Ridge Rd, Orlando, FL 32809, United States', 'Orlando', 'florida', 'United States', '110224', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'verified', 3, '2026-08-12 18:52:31', NULL, 'Account verified by admin during user creation - no documents required', '2026-08-12 18:52:31', '2026-08-12 18:52:31');

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
(264, 'Mr.carter.tech07@gmail.com', '102.89.68.42', '2026-06-25 18:43:13'),
(265, 'paul.stromae1985@gmail.com', '105.117.23.45', '2026-06-25 23:06:35'),
(266, 'financemanege@gmail.com', '2607:fb91:8b24:4be0:ad4:1e51:4e0b:2294', '2026-06-26 00:02:33'),
(267, 'financemanege@gmail.com', '2607:fb91:8b24:4be0:ad4:1e51:4e0b:2294', '2026-06-26 00:04:04'),
(268, 'financemanege@gmail.com', '2607:fb91:8b24:4be0:ad4:1e51:4e0b:2294', '2026-06-26 00:05:06'),
(269, 'lichen6677788@gmail.com', '105.118.3.150', '2026-07-06 09:37:15'),
(270, 'keanureeves124690@gmail.com', '102.88.113.35', '2026-07-06 12:55:46'),
(271, 'keanureeves124690@gmail.com', '102.88.113.35', '2026-07-06 12:56:05'),
(272, 'therenwicks1@hotmail.com', '82.132.212.78', '2026-07-06 19:40:16'),
(273, 'therenwicks3@hotmail.com', '82.132.212.78', '2026-07-06 19:42:42'),
(274, 'Keanureeves124690@gmail.com', '2001:8004:c82:9b65:be94:2887:1ddb:6419', '2026-07-07 06:31:18'),
(275, 'KeanuReeves@gmail.com', '82.132.212.158', '2026-07-07 07:40:06'),
(276, 'KeanuReeves@gmail.com', '82.132.212.158', '2026-07-07 07:42:17'),
(277, 'KeanuReeves@gmail.com', '82.132.212.158', '2026-07-07 07:44:21'),
(278, 'Keanureaves@gmail.com', '82.132.212.158', '2026-07-07 07:49:30'),
(279, 'keanureeves12469@gmail.com', '82.132.212.158', '2026-07-07 08:00:08'),
(280, 'keanureeves12469@gmail.com', '82.132.212.158', '2026-07-07 08:00:08'),
(281, 'keanureeves12469@gmail.com', '82.132.212.158', '2026-07-07 08:01:55'),
(282, 'divinityintervention40@gmail.com', '2600:1012:b358:448f:c139:b0f6:584d:cf25', '2026-07-07 09:47:04'),
(283, 'keanureeves124690@gmail.com', '82.132.212.111', '2026-07-07 16:33:27'),
(284, 'keanureeves124690@gmail.com', '82.132.212.111', '2026-07-07 16:45:08'),
(285, 'keanureeves124690@gmail.com', '82.132.212.111', '2026-07-07 17:25:45'),
(286, 'keanureeves124690@gmail.com', '82.132.212.111', '2026-07-07 17:46:28'),
(287, 'keanureeves124690@gmail.com', '82.132.212.111', '2026-07-07 17:49:17'),
(288, 'lichen6677788@gmail.com', '105.118.5.5', '2026-07-11 11:08:08'),
(289, 'Mr.carter.tech07@gmail.com', '102.88.55.71', '2026-07-12 12:38:36'),
(290, 'Mr.carter.tech07@gmail.com', '102.88.55.71', '2026-07-12 12:39:34'),
(291, 'Mr.carter.tech07@gmail.com', '102.88.55.71', '2026-07-12 13:15:02'),
(292, 'mr.carter.tech07@gmail.com', '102.93.10.22', '2026-07-12 15:15:16'),
(294, 'mr.carter.tech07@gmail.com', '102.93.10.22', '2026-07-12 18:08:04'),
(295, 'mr.carter.tech07@gmail.com', '102.93.10.22', '2026-07-12 18:08:08'),
(296, 'Mr.carter.tech07@gmail.com', '197.211.53.92', '2026-07-14 15:04:47'),
(297, 'Mr.carter.tech07@gmail.com', '197.211.53.92', '2026-07-14 15:05:38'),
(298, 'Mrcartertech07@gmail.com', '197.211.53.92', '2026-07-14 15:06:31'),
(299, 'Mr.carter.tech07@gmail.com', '197.211.53.92', '2026-07-14 15:38:23'),
(300, 'Mr.carter.tech07@gmail.com', '105.127.14.245', '2026-07-15 14:18:46'),
(301, 'adamabdulrahman629@gmail.com', '102.91.92.33', '2026-07-15 19:32:58'),
(302, 'keanureeves123690@gmail.com', '102.93.9.94', '2026-07-15 20:23:47'),
(303, 'adamabdulrahman629@gmail.com', '102.92.24.50', '2026-07-16 13:51:03'),
(304, 'lichen6677788@gmail.comi', '105.118.5.114', '2026-07-17 16:30:02'),
(305, 'financemanege@gmail.com', '2601:541:300:8ab0:5807:96b5:ea29:6926', '2026-07-18 22:02:30'),
(306, 'financemanege@gmail.com', '2601:541:300:8ab0:5807:96b5:ea29:6926', '2026-07-18 22:05:13'),
(307, 'keanureeve061o@gmail.com', '102.88.113.255', '2026-07-27 21:20:10'),
(308, 'lichen6677788@gmail.com', '197.211.63.175', '2026-08-08 02:09:18'),
(309, 'lichen6677788@gmail.com', '197.211.63.175', '2026-08-08 02:10:44'),
(310, 'lichen6677788@gmail.com', '197.211.63.175', '2026-08-08 02:12:50'),
(311, 'lichen6677788@gmail.com', '105.112.105.150', '2026-08-09 15:53:56'),
(312, 'lichen6677788@gmail.com', '105.112.105.150', '2026-08-09 15:54:37'),
(313, 'ElonmuskEthereumportfolio@outlook.com', '50.54.168.36', '2026-08-12 22:31:05'),
(314, 'hkr.fred@outlook.com', '172.59.184.55', '2026-08-16 23:40:27'),
(315, 'hkr.fred@outlook.com', '172.59.184.55', '2026-08-16 23:41:46');

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
(162, 150, 'KYC Verification Approved', 'Your KYC verification has been approved. You now have full access to all banking services.', 'success', 0, '/profile/kyc', NULL, '2026-08-12 18:52:31');

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
(1, '2026.03.19', 'safe_schema_upgrade', '2026_03_19_safe_schema_upgrade.sql', '2026-04-13 18:19:21', NULL, 'success', NULL),
(2, '2026.06.04', 'safe_schema_upgrade', '2026_03_19_safe_schema_upgrade.sql', '2026-06-12 14:58:32', NULL, 'success', NULL),
(3, '2026.06.05', 'safe_schema_upgrade', '2026_03_19_safe_schema_upgrade.sql', '2026-06-12 19:18:59', NULL, 'success', NULL),
(4, '2026.07.07', 'safe_schema_upgrade', '2026_03_19_safe_schema_upgrade.sql', '2026-07-06 23:58:12', NULL, 'success', NULL);

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
(1, 'site_name', 'Zentropay Global', 'string', 'Website name displayed throughout the site', 3, '2025-10-08 22:44:52', '2026-08-24 21:48:17'),
(2, 'site_url', 'https://app.zentropay-global.pro', 'string', 'Website URL', 3, '2025-10-08 22:44:52', '2026-08-24 21:48:17'),
(3, 'site_email', 'app@zentropay-global.pro', 'string', 'Primary contact email', 3, '2025-10-08 22:44:52', '2026-08-24 21:48:17'),
(4, 'default_currency', 'USD', 'string', 'Default system currency', 3, '2025-10-08 22:44:52', '2026-08-24 21:48:17'),
(5, 'min_transfer_amount', '50', 'number', 'Minimum transfer amount', 3, '2025-10-08 22:44:52', '2026-08-24 21:48:17'),
(6, 'max_transfer_amount', '100000000000', 'number', 'Maximum transfer amount per transaction', 3, '2025-10-08 22:44:52', '2026-08-24 21:48:17'),
(7, 'transfer_fee_domestic', '0', 'number', 'Domestic transfer fee (deprecated)', 3, '2025-10-08 22:44:52', '2026-08-24 21:48:17'),
(8, 'transfer_fee_international', '0.5', 'number', 'International transfer fee (deprecated)', 3, '2025-10-08 22:44:52', '2026-08-24 21:48:17'),
(9, 'interest_rate_savings', '2.5', 'number', 'Savings account interest rate percentage', 3, '2025-10-08 22:44:52', '2026-08-24 21:48:17'),
(10, 'maintenance_mode', '0', 'boolean', 'Enable maintenance mode', 3, '2025-10-08 22:44:52', '2026-08-24 21:48:17'),
(11, 'require_kyc', '1', 'boolean', 'Require KYC verification', 3, '2025-10-08 22:44:52', '2026-08-24 21:48:17'),
(12, 'two_factor_required', '1', 'boolean', 'Force 2FA for all users', 3, '2025-10-08 22:44:52', '2026-08-24 21:48:17'),
(13, 'allow_new_registrations', '1', 'boolean', 'Enable/disable new user registrations', 3, '2025-10-08 22:44:52', '2026-08-24 21:48:17'),
(14, 'loan_service_enabled', '1', 'boolean', 'Enable/disable loan applications', 3, '2025-10-08 22:44:52', '2026-08-24 21:48:17'),
(15, 'card_service_enabled', '1', 'boolean', 'Enable/disable card requests', 3, '2025-10-08 22:44:52', '2026-08-24 21:48:17'),
(16, 'maintenance_message', 'System maintenance in progress', 'string', 'Maintenance mode message', 3, '2025-10-08 22:44:52', '2026-08-24 21:48:17'),
(17, 'max_daily_transfer_amount', '50000', 'number', 'Maximum daily transfer amount per user', 3, '2025-10-08 22:44:52', '2025-11-09 19:09:06'),
(18, 'max_transaction_amount', '10000000', 'number', 'Maximum single transaction amount', 3, '2025-10-08 22:44:52', '2026-08-24 21:48:17'),
(19, 'kyc_required_for_transfer', '1', 'boolean', 'Require KYC verification for transfers', 3, '2025-10-08 22:44:52', '2026-08-24 21:48:17'),
(20, 'auto_flag_large_transactions', '0', 'boolean', 'Auto-flag transactions over threshold', 3, '2025-10-08 22:44:52', '2026-08-24 21:48:17'),
(21, 'large_transaction_threshold', '10000', 'number', 'Amount threshold for flagging', 3, '2025-10-08 22:44:52', '2026-08-24 21:48:17'),
(25, 'bank_operating_country', 'United States', 'string', 'Country where the bank operates', 3, '2025-10-14 00:00:00', '2026-08-24 21:48:17'),
(26, 'bank_operating_region', 'north-america', 'string', 'Region where the bank operates', 3, '2025-10-14 00:00:00', '2025-10-18 03:39:05'),
(27, 'site_logo_url', 'https://app.zentropay-global.pro/uploads/branding/site-logo.png?v=1783416669', 'string', 'URL to site logo image', 3, '2025-10-14 00:00:00', '2026-08-24 21:48:17'),
(28, 'site_tagline', 'Your Trusted Banking Partner', 'string', 'Site tagline/slogan', 3, '2025-10-14 00:00:00', '2026-08-24 21:48:17'),
(29, 'site_description', 'Secure online banking with 24/7 access to your accounts', 'string', 'Site description for SEO', 3, '2025-10-14 00:00:00', '2026-08-24 21:48:17'),
(30, 'support_phone', '+44882769***', 'string', 'Customer support phone number', 3, '2025-10-14 00:00:00', '2026-08-24 21:48:17'),
(31, 'support_hours', 'Monday - Friday, 8:00 AM - 6:00 PM EST', 'string', 'Customer support hours', 3, '2025-10-14 00:00:00', '2026-08-24 21:48:17'),
(32, 'bank_address', '2015 Northwest Hwy, Garland, TX 75041, London, United Kingdom ', 'string', 'Physical bank address', 3, '2025-10-14 00:00:00', '2026-08-24 21:48:17'),
(34, 'interest_rate_checking', '0', 'number', 'Checking account interest rate percentage', 3, '2025-10-14 00:00:00', '2026-08-24 21:48:17'),
(35, 'overdraft_fee', '35', 'number', 'Overdraft fee amount', 3, '2025-10-14 00:00:00', '2026-08-24 21:48:17'),
(36, 'monthly_maintenance_fee', '0', 'number', 'Monthly account maintenance fee', 3, '2025-10-14 00:00:00', '2026-08-24 21:48:17'),
(37, 'require_transfer_pin', '1', 'boolean', 'Require Transfer PIN for transactions', 3, '2025-10-14 00:00:00', '2026-08-24 21:48:17'),
(38, 'max_login_attempts', '10', 'number', 'Maximum failed login attempts before lockout', 3, '2025-10-14 00:00:00', '2026-08-24 21:48:17'),
(39, 'login_lockout_duration', '5', 'number', 'Login lockout duration in minutes', 3, '2025-01-15 10:00:00', '2026-08-24 21:48:17'),
(40, 'session_timeout', '30', 'number', 'Session timeout in minutes', 3, '2025-10-14 00:00:00', '2026-08-24 21:48:17'),
(41, 'email_on_transfer', '1', 'boolean', 'Send email notification on transfers', 3, '2025-10-14 00:00:00', '2026-08-24 21:48:17'),
(42, 'email_on_login', '1', 'boolean', 'Send email notification on login', 3, '2025-10-14 00:00:00', '2026-08-24 21:48:17'),
(43, 'site_favicon_url', 'https://app.zentropay-global.pro/uploads/branding/favicon.png?v=1783416682', 'string', 'URL to site favicon', 3, '2025-10-14 00:00:00', '2026-08-24 21:48:17'),
(44, 'transfer_internal_fee', '0', 'number', 'Internal transfer fee percentage', 3, '2025-10-14 00:00:00', '2025-10-18 03:39:05'),
(45, 'transfer_domestic_fee', '0.5', 'number', 'Domestic transfer fee percentage', 3, '2025-10-14 00:00:00', '2025-10-18 03:39:05'),
(46, 'transfer_international_fee', '2.5', 'number', 'International transfer fee percentage', 3, '2025-10-14 00:00:00', '2025-10-18 03:39:05'),
(47, 'sms_on_transfer', '1', 'boolean', 'Send SMS notification on transfers', 3, '2025-10-14 00:00:00', '2026-08-24 21:48:17'),
(48, 'daily_limit_checking', '100000000000', 'number', 'Daily transaction limit for Checking accounts', 3, '2025-11-03 02:27:47', '2026-08-24 21:48:17'),
(49, 'daily_limit_savings', '10000000000', 'number', 'Daily transaction limit for Savings accounts', 3, '2025-11-03 02:27:47', '2026-08-24 21:48:17'),
(50, 'daily_limit_business', '10000000000', 'number', 'Daily transaction limit for Business accounts', 3, '2025-11-03 02:27:47', '2026-08-24 21:48:17'),
(51, 'monthly_limit_checking', '100000000000', 'number', 'Monthly transaction limit for Checking accounts', 3, '2025-11-03 02:27:47', '2026-08-24 21:48:17'),
(52, 'monthly_limit_savings', '100000000000', 'number', 'Monthly transaction limit for Savings accounts', 3, '2025-11-03 02:27:47', '2026-08-24 21:48:17'),
(53, 'monthly_limit_business', '100000000000', 'number', 'Monthly transaction limit for Business accounts', 3, '2025-11-03 02:27:47', '2026-08-24 21:48:17'),
(142, 'enable_currency_conversion', '1', 'boolean', 'Enable currency conversion. When enabled, users can view balances and amounts in their preferred currency. When disabled, all amounts are displayed in the site default currency.', 3, '2025-11-04 17:17:14', '2026-08-24 21:48:17'),
(414, 'disable_2fa_entirely', '0', 'boolean', 'Disable 2FA entirely for all users. When enabled, users cannot enable 2FA and existing 2FA will be disabled. This overrides the \"Force 2FA\" setting.', 3, '2026-02-10 02:55:45', '2026-08-24 21:48:17'),
(1415, 'force_security_setup', '1', 'boolean', 'When enabled, users must complete Login PIN and Transfer PIN (+ 2FA if required) before accessing the dashboard', 3, '2026-05-31 01:13:56', '2026-08-24 21:48:17'),
(1416, 'kyc_use_custom_fields', '0', 'boolean', 'Use custom admin-defined KYC fields instead of country profile defaults', 3, '2026-05-31 01:13:56', '2026-08-24 21:48:17'),
(1417, 'kyc_custom_fields', '[]', 'json', 'JSON array of custom KYC field definitions when kyc_use_custom_fields is enabled', 3, '2026-05-31 01:13:56', '2026-08-24 21:48:17'),
(1517, 'ledger_aligned_to_site_default', '1', 'boolean', 'Ledger balances converted to site default_currency using exchange_rates', 3, '2026-06-12 19:18:37', '2026-08-24 21:48:17');

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
  `expense_category` enum('shopping','food','transport','bills','entertainment','healthcare','travel','education','salary','investment','rent','insurance','gift','personal','other','bonus','refund','utility') DEFAULT NULL,
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
(1884, 'ADM20260812145615455', 150, 145, 'debit', 'withdrawal', '', 11900000.00, 'USD', 12000000.00, 100000.00, 'Domestic Transfer to paul pascal at Bank Of America', '4418293723', 'paul pascal', 'Bank Of America', 'completed', NULL, 0.00, NULL, '{\"admin_id\":3,\"reason\":\"Administrative adjustment\",\"method\":\"domestic\",\"method_fields\":{\"recipient_bank\":\"Bank Of America\",\"recipient_account\":\"4418293723\",\"recipient_name\":\"paul pascal\"},\"admin_action\":true,\"display_amount\":11900000,\"display_currency\":\"USD\",\"ledger_amount\":11900000,\"ledger_currency\":\"USD\"}', '102.88.113.241', '2026-08-10 19:55:00', '2026-08-10 19:55:00'),
(1885, 'ADM20260812145747262', 150, 145, 'credit', 'deposit', '', 11250000.00, 'USD', 100000.00, 11350000.00, 'Transfer from Titan Core Assets Group LLC at wells Fargo', '868746356795', 'Titan Core Assets Group LLC', 'wells Fargo', 'completed', NULL, 0.00, NULL, '{\"admin_id\":3,\"reason\":\"Administrative adjustment\",\"method\":\"domestic\",\"method_fields\":{\"recipient_bank\":\"wells Fargo\",\"recipient_account\":\"868746356795\",\"recipient_name\":\"Titan Core Assets Group LLC\"},\"admin_action\":true,\"display_amount\":11250000,\"display_currency\":\"USD\",\"ledger_amount\":11250000,\"ledger_currency\":\"USD\"}', '102.88.113.241', '2026-08-02 10:00:00', '2026-08-02 10:00:00'),
(1886, 'ADM20260812145903390', 150, 145, 'credit', 'deposit', '', 670000.00, 'USD', 11350000.00, 12020000.00, 'Transfer from Apex Growth Ventures Ltd at JPMorgan Chase Bank', 'US-CH-77451092', 'Apex Growth Ventures Ltd', 'JPMorgan Chase Bank', 'completed', NULL, 0.00, NULL, '{\"admin_id\":3,\"reason\":\"Administrative adjustment\",\"method\":\"domestic\",\"method_fields\":{\"recipient_bank\":\"JPMorgan Chase Bank\",\"recipient_account\":\"US-CH-77451092\",\"recipient_name\":\"Apex Growth Ventures Ltd\"},\"admin_action\":true,\"display_amount\":670000,\"display_currency\":\"USD\",\"ledger_amount\":670000,\"ledger_currency\":\"USD\"}', '102.88.113.241', '2026-08-04 19:58:00', '2026-08-04 19:58:00'),
(1887, 'TXN6A7CF49560F29', 150, 145, 'debit', 'transfer', 'other', 10050.00, 'USD', 12020000.00, 12009950.00, 'Domestic Transfer to Han at Wells Fargo Bank', '776463991020', 'Han', 'Wells Fargo Bank', 'completed', 'wire', 50.00, NULL, '{\"transfer_scope\":\"domestic\",\"transfer_method\":\"wire\",\"transfer_method_label\":\"Wire Transfer\",\"country_code\":\"US\",\"bank_name\":\"Wells Fargo Bank\",\"account_number\":\"776463991020\",\"routing_number\":\"290556371\",\"swift\":\"WFBIUS6SXXX\",\"transaction_override\":\"normal\",\"failed_reason\":null,\"entry_amount\":10000,\"entry_currency\":\"USD\",\"entry_fee\":50,\"entry_total\":10050}', '151.240.91.169', '2026-08-12 22:32:53', '2026-08-12 22:32:53'),
(1888, 'ADM20260824174624353', 150, 145, 'credit', 'deposit', '', 100000.00, 'USD', 12009950.00, 12109950.00, 'Transfer from Elexir Shell BP at Wellsfargo', '3566*******24424', 'Elexir Shell BP', 'Wellsfargo', 'completed', NULL, 0.00, NULL, '{\"admin_id\":3,\"reason\":\"Administrative adjustment\",\"method\":\"domestic\",\"method_fields\":{\"recipient_bank\":\"Wellsfargo\",\"recipient_account\":\"3566*******24424\",\"recipient_name\":\"Elexir Shell BP\"},\"admin_action\":true,\"display_amount\":100000,\"display_currency\":\"USD\",\"ledger_amount\":100000,\"ledger_currency\":\"USD\"}', '102.89.69.110', '2026-08-24 22:45:00', '2026-08-24 22:45:00');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_generation_batches`
--

CREATE TABLE `transaction_generation_batches` (
  `id` int(11) NOT NULL,
  `batch_id` varchar(64) NOT NULL,
  `idempotency_key` varchar(128) NOT NULL,
  `params_hash` char(64) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `template_id` int(11) NOT NULL,
  `engine_params` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`engine_params`)),
  `plan_summary` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`plan_summary`)),
  `density` enum('light','normal','heavy') NOT NULL DEFAULT 'normal',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `previous_balance` decimal(15,2) NOT NULL,
  `history_impact` decimal(15,2) NOT NULL,
  `target_final_balance` decimal(15,2) NOT NULL,
  `opening_balance` decimal(15,2) NOT NULL,
  `transaction_count` int(11) NOT NULL DEFAULT 0,
  `replaced_previous` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('completed','undone') NOT NULL DEFAULT 'completed',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transaction_generation_batches`
--

INSERT INTO `transaction_generation_batches` (`id`, `batch_id`, `idempotency_key`, `params_hash`, `admin_id`, `user_id`, `account_id`, `template_id`, `engine_params`, `plan_summary`, `density`, `start_date`, `end_date`, `previous_balance`, `history_impact`, `target_final_balance`, `opening_balance`, `transaction_count`, `replaced_previous`, `status`, `created_at`, `updated_at`) VALUES
(1, '202606121902219b24e64d', 'gen-129-1781305317790-7ifc201x', '67dc402ba3b78565bd73a3ed7c480c8f3f7ac6926d53282d2f07d7856701bae1', 3, 135, 129, 1, NULL, NULL, 'heavy', '2018-07-25', '2026-06-11', 590000.03, 270234.00, 860234.03, 319766.03, 150, 0, 'completed', '2026-06-12 23:02:21', '2026-06-12 23:02:21'),
(2, '2026061304580332cb0913', 'gen-130-1781341063110-3iupnm3r', 'ed3adb59293ef420a6492975bcccfe693f1dd57f433e0b70db1a455836669dd6', 3, 138, 130, 1, NULL, NULL, 'heavy', '2018-02-07', '2026-06-11', 0.00, 900000.00, 900000.00, -900000.00, 150, 0, 'undone', '2026-06-13 08:58:03', '2026-07-06 23:37:12'),
(3, '202606211259343d2cdf94', 'gen-132-1782061166576-wm9usapy', '2448fc7aa0220ffce1095d17c7d3bdf1858660270efda9e04aec748d73ec2368', 3, 140, 132, 1, NULL, NULL, 'heavy', '2012-10-05', '2020-08-23', 0.00, 19453552.00, 19453552.00, -19453552.00, 150, 0, 'completed', '2026-06-21 16:59:34', '2026-06-21 16:59:34'),
(4, '202606251540593756006c', 'gen-137-1782416445495-f457oakz', 'debda4a1bbe2f9eaf9934e87ad2a4505dad1a0c0f6ab8942141b067b69c7865a', 3, 143, 137, 1, NULL, NULL, 'normal', '2014-06-13', '2026-06-24', 0.00, 1200000.00, 1200000.00, -1200000.00, 70, 0, 'completed', '2026-06-25 19:40:59', '2026-06-25 19:40:59'),
(5, '2026070509230432766202', 'gen-138-1783234187655-0nvsd8op', '0e9dabedc9cc2a5898fb4a1772d513a3e3d90c38930e94739e9a263774ea0c38', 3, 144, 138, 1, NULL, NULL, 'normal', '2026-04-06', '2026-07-04', 0.00, 13000000.00, 13000000.00, -13000000.00, 70, 0, 'undone', '2026-07-05 13:23:04', '2026-07-05 15:18:25'),
(6, '20260705111825ad5256a4', 'gen-138-1783241066018-c2izzz43', '07edf3cd608865feb8882c302c6688145b43ce4e6c2eaa6683d4c8a64fc6dc23', 3, 144, 138, 1, NULL, NULL, 'heavy', '2023-02-09', '2025-12-24', 13000000.00, 200000.00, 13200000.00, 12800000.00, 150, 1, 'undone', '2026-07-05 15:18:25', '2026-07-06 12:37:17'),
(7, '202607060837172bac4f20', 'gen-138-1783341410221-rf77nq4m', 'a4c00de3d0d58c0847e4cd143ca3c09cf0a8ee1e17db283fdfdcabc26f38d1ea', 3, 144, 138, 1, NULL, NULL, 'heavy', '2016-02-16', '2023-11-21', 0.00, 13200000.00, 13000000.00, -13400000.00, 150, 0, 'completed', '2026-07-06 12:37:17', '2026-07-06 12:37:17'),
(8, '20260706084043d356f971', 'gen-140-1783341634722-k64rw053', 'b46eaa4a5f90042a448e551c7c3468444beeb671d3e88152c43d584583f5ebd8', 3, 146, 140, 1, NULL, NULL, 'heavy', '2019-02-14', '2023-11-24', 0.00, 50000000.00, 50000000.00, -50000000.00, 150, 0, 'completed', '2026-07-06 12:40:43', '2026-07-06 12:40:43'),
(9, '202607062011052041e61f', 'gen-130-1783383045815-yqc6rahg', '9a74cda412a2b6c052182716062c0a877d3a4136677a2537b7dfe569234ff2df', 3, 138, 130, 0, '{\"account_style\":\"investor\",\"financial_behaviour\":\"intl_traveller\",\"volume\":\"high\",\"persona_id\":\"investor_uk\"}', '{\"domestic_transfers\":45,\"international_transfers\":66,\"incoming_credits\":40,\"card_payments\":21,\"bills\":10,\"salary_credits\":0,\"atm_withdrawals\":0,\"other\":0,\"total\":182,\"account_style\":\"investor\",\"financial_behaviour\":\"intl_traveller\",\"volume\":\"high\",\"persona_label\":\"Investor \\u2014 travel & dividends\",\"operating_country\":\"United States\"}', 'heavy', '2021-06-11', '2026-07-05', 0.00, 945000.00, 945000.00, -945000.00, 182, 0, 'completed', '2026-07-07 00:11:05', '2026-07-07 00:11:05');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_templates`
--

CREATE TABLE `transaction_templates` (
  `id` int(11) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `account_type` varchar(50) DEFAULT 'checking',
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transaction_templates`
--

INSERT INTO `transaction_templates` (`id`, `slug`, `name`, `account_type`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'default_checking', 'Default Checking History', 'checking', 'Realistic mixed credit/debit history derived from Andy seed reference pack.', 1, '2026-06-12 22:59:08', '2026-06-12 22:59:08');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_template_items`
--

CREATE TABLE `transaction_template_items` (
  `id` int(11) NOT NULL,
  `template_id` int(11) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `transaction_type` enum('debit','credit') NOT NULL,
  `category` enum('transfer','payment','deposit','withdrawal','fee','interest','loan','card','other') NOT NULL,
  `expense_category` enum('shopping','food','transport','bills','entertainment','healthcare','travel','education','salary','investment','rent','insurance','gift','personal','other','bonus','refund','utility') DEFAULT NULL,
  `base_amount` decimal(15,2) NOT NULL,
  `description` text DEFAULT NULL,
  `recipient_account` varchar(255) DEFAULT NULL,
  `recipient_name` varchar(255) DEFAULT NULL,
  `recipient_bank` varchar(255) DEFAULT NULL,
  `status` enum('pending','processing','completed','failed','reversed') NOT NULL DEFAULT 'completed',
  `fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `weight` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transaction_template_items`
--

INSERT INTO `transaction_template_items` (`id`, `template_id`, `sort_order`, `transaction_type`, `category`, `expense_category`, `base_amount`, `description`, `recipient_account`, `recipient_name`, `recipient_bank`, `status`, `fee`, `weight`) VALUES
(1, 1, 1, 'credit', 'deposit', 'salary', 2350000.00, 'Transfer from Salary Payment – ACADEMI PMC at wells Fargo', '44182937', 'Salary Payment – ACADEMI PMC', 'wells Fargo', 'completed', 0.00, 1),
(2, 1, 2, 'debit', 'withdrawal', NULL, 7000000.00, 'Domestic Transfer to pascal paul at citi bank', '22353563', 'pascal paul', 'citi bank', 'completed', 0.00, 1),
(3, 1, 3, 'debit', 'withdrawal', NULL, 185000.00, 'International Transfer to James Thornton at HSBC UK', 'GB72HBUK40127612345678', 'James Thornton', 'HSBC UK', 'completed', 0.00, 1),
(4, 1, 4, 'debit', 'withdrawal', NULL, 27500.00, 'Domestic Transfer to Michael Rodriguez at Chase Bank', '463817492', 'Michael Rodriguez', 'Chase Bank', 'failed', 0.00, 1),
(5, 1, 5, 'debit', 'withdrawal', NULL, 27500.00, 'Domestic Transfer to Michael Rodriguez at Chase Bank', '463817492', 'Michael Rodriguez', 'Chase Bank', 'completed', 0.00, 1),
(6, 1, 6, 'credit', 'deposit', NULL, 9842.00, 'IRS Tax Refund Adjustment', '009283514', 'Internal Revenue Service', 'U.S. Treasury Department', 'completed', 0.00, 1),
(7, 1, 7, 'debit', 'withdrawal', NULL, 62900.00, 'International Transfer to Cobus Van Der West at Standard Bank South Africa', '128476395', 'Cobus Van Der West', 'Standard Bank South Africa', 'completed', 0.00, 1),
(8, 1, 8, 'debit', 'withdrawal', NULL, 3120.00, 'Domestic Transfer to Amazon Web Services at JPMorgan Payments', '875341209', 'Amazon Web Services', 'JPMorgan Payments', 'completed', 0.00, 1),
(9, 1, 9, 'credit', 'deposit', NULL, 2350000.00, 'Transfer from ACADEMI PMC at wells Fargo', '4418293723', 'ACADEMI PMC', 'wells Fargo', 'completed', 0.00, 1),
(10, 1, 10, 'credit', 'deposit', NULL, 2350000.00, 'Transfer from ACADEMI PMC at wells Fargo', '4418293723', 'ACADEMI PMC', 'wells Fargo', 'completed', 0.00, 1),
(11, 1, 11, 'debit', 'transfer', NULL, 8492.25, 'Domestic Transfer to Matts Anderson at Wells Fargo Bank', '6272883838', 'Matts Anderson', 'Wells Fargo Bank', 'completed', 42.25, 1),
(12, 1, 12, 'debit', 'withdrawal', NULL, 35700.00, 'Domestic Transfer to James Thornton at HSBC UK', '3647687970809', 'James Thornton', 'HSBC UK', 'completed', 0.00, 1),
(13, 1, 13, 'debit', 'transfer', NULL, 4600.00, 'Domestic Transfer to Leave@academi at JPMorgan Chase Bank', '26273741639', 'Leave@academi', 'JPMorgan Chase Bank', 'failed', 22.96, 1),
(14, 1, 14, 'credit', 'deposit', 'insurance', 7097129.00, 'Transfer from Titan Core Assets Group LLC at wells Fargo', '4418293723', 'Titan Core Assets Group LLC', 'wells Fargo', 'completed', 0.00, 1),
(15, 1, 15, 'debit', 'withdrawal', NULL, 9100.00, 'Domestic Transfer to Academi@Admin at JPMorgan Chase Bank', '868746356795', 'Academi@Admin', 'JPMorgan Chase Bank', 'failed', 0.00, 1),
(16, 1, 16, 'debit', 'withdrawal', NULL, 2300.00, 'Card payment to Academi@Clinic', '868746356795', 'Academi@Clinic', 'JPMorgan Chase Bank', 'completed', 0.00, 1),
(17, 1, 17, 'debit', 'withdrawal', NULL, 49500.00, 'Domestic Transfer to Wright Caleb at wells Fargo', 'US-CH-77451092', 'Wright Caleb', 'wells Fargo', 'completed', 0.00, 1),
(18, 1, 18, 'credit', 'transfer', 'other', 27150.00, 'BKK Gesund – health allowance Q3 2023', NULL, 'BKK Gesund', 'DZ Bank Ndl. Frankfurt', 'completed', 0.00, 1),
(19, 1, 19, 'debit', 'payment', 'bills', 17.30, 'Telekom Deutschland – Oct 2023 invoice', NULL, 'Telekom Deutschland GmbH', NULL, 'completed', 0.00, 1),
(20, 1, 20, 'debit', 'payment', 'shopping', 550.00, 'Nike.com e-gift card order', NULL, 'Nike E-Commerce', NULL, 'completed', 0.00, 1),
(21, 1, 21, 'debit', 'payment', 'shopping', 182.00, 'Shopify store – online purchase', NULL, 'Shopify Payments', NULL, 'completed', 0.00, 1),
(22, 1, 22, 'debit', 'payment', 'bills', 17.67, 'Vodafone GmbH – mobile & landline Nov', NULL, 'Vodafone GmbH', NULL, 'completed', 0.00, 1),
(23, 1, 23, 'credit', 'transfer', 'other', 55955.00, 'Verpflegungspauschale Nov 2023', NULL, 'Muster GmbH HR', 'Landesbank Hessen-Thüringen', 'completed', 0.00, 1),
(24, 1, 24, 'debit', 'payment', 'bills', 17.45, 'O2 Rechnung – December 2023', NULL, 'O2 Germany', NULL, 'completed', 0.00, 1),
(25, 1, 25, 'debit', 'payment', 'shopping', 3280.00, 'Wilma wunder – Wiesbaden store', NULL, 'Wilma wunder Einzelhandel', NULL, 'completed', 0.00, 1),
(26, 1, 26, 'debit', 'payment', 'other', 2800.00, 'Heiliggeist Apotheke – prescription & OTC', NULL, 'Heiliggeist Apotheke', NULL, 'completed', 0.00, 1),
(27, 1, 27, 'debit', 'payment', 'bills', 17.67, '1&1 Versatel – Jan 2024 broadband', NULL, '1&1 Versatel GmbH', NULL, 'completed', 0.00, 1),
(28, 1, 28, 'credit', 'transfer', 'salary', 1450000.00, 'Gehalt Nov 2023 – Muster GmbH', NULL, 'Muster GmbH Payroll', 'Commerzbank AG', 'completed', 0.00, 1),
(29, 1, 29, 'debit', 'payment', 'bills', 17.85, 'Congstar – Feb 2024 mobile', NULL, 'Congstar GmbH', NULL, 'completed', 0.00, 1),
(30, 1, 30, 'debit', 'payment', 'bills', 17.20, 'E.ON Strom – March 2024', NULL, 'E.ON Energie Deutschland', NULL, 'completed', 0.00, 1),
(31, 1, 31, 'debit', 'payment', 'shopping', 1625.00, 'Fitshop Wiesbaden – sports gear', NULL, 'Fitshop Wiesbaden', NULL, 'completed', 0.00, 1),
(32, 1, 32, 'debit', 'payment', 'bills', 17.67, 'Stadtwerke Wiesbaden – April utilities', NULL, 'Stadtwerke Wiesbaden', NULL, 'completed', 0.00, 1),
(33, 1, 33, 'debit', 'payment', 'bills', 17.60, 'Vodafone – May 2024 mobile', NULL, 'Vodafone GmbH', NULL, 'completed', 0.00, 1),
(34, 1, 34, 'debit', 'payment', 'bills', 17.70, 'O2 Rechnung – June 2024', NULL, 'O2 Germany', NULL, 'completed', 0.00, 1),
(35, 1, 35, 'debit', 'transfer', 'other', 5000.00, 'Wire to Paul Hartman – Ref WH-60924', NULL, 'Paul Hartman', 'Deutsche Bank AG', 'completed', 0.00, 1),
(36, 1, 36, 'debit', 'payment', 'bills', 17.67, '1&1 – July 2024 broadband', NULL, '1&1 Versatel GmbH', NULL, 'completed', 0.00, 1),
(37, 1, 37, 'debit', 'payment', 'bills', 17.47, 'Congstar – Aug 2024', NULL, 'Congstar GmbH', NULL, 'completed', 0.00, 1),
(38, 1, 38, 'debit', 'payment', 'shopping', 1320.00, 'Amazon.de – treadmill order', NULL, 'Amazon EU S.à r.l.', NULL, 'completed', 0.00, 1),
(39, 1, 39, 'credit', 'transfer', 'other', 32250.00, 'DAK Zuschuss – health allowance Aug 2024', NULL, 'DAK-Gesundheit', 'Sparkasse KölnBonn', 'completed', 0.00, 1),
(40, 1, 40, 'debit', 'payment', 'bills', 17.85, 'E.ON Strom – September 2024', NULL, 'E.ON Energie Deutschland', NULL, 'completed', 0.00, 1),
(41, 1, 41, 'debit', 'payment', 'bills', 17.65, 'Telekom Deutschland – Oct 2024', NULL, 'Telekom Deutschland GmbH', NULL, 'completed', 0.00, 1),
(42, 1, 42, 'debit', 'payment', 'bills', 17.25, 'Vodafone – Nov 2024', NULL, 'Vodafone GmbH', NULL, 'completed', 0.00, 1),
(43, 1, 43, 'credit', 'transfer', 'other', 59700.00, 'Verpflegungspauschale Nov 2024 – Muster GmbH', NULL, 'Muster GmbH HR', 'ING-DiBa AG', 'completed', 0.00, 1),
(44, 1, 44, 'debit', 'payment', 'bills', 17.46, 'O2 Rechnung – Dec 2024', NULL, 'O2 Germany', NULL, 'completed', 0.00, 1),
(45, 1, 45, 'debit', 'payment', 'shopping', 18270.00, 'Amazon.de – year-end order', NULL, 'Amazon EU S.à r.l.', NULL, 'completed', 0.00, 1),
(46, 1, 46, 'debit', 'payment', 'bills', 17.38, '1&1 – Jan 2025', NULL, '1&1 Versatel GmbH', NULL, 'completed', 0.00, 1),
(47, 1, 47, 'credit', 'transfer', 'salary', 1330000.00, 'Gehalt Dez 2024 – Muster GmbH', NULL, 'Muster GmbH Payroll', 'Commerzbank AG', 'completed', 0.00, 1),
(48, 1, 48, 'debit', 'payment', 'bills', 17.67, 'Congstar – Feb 2025', NULL, 'Congstar GmbH', NULL, 'completed', 0.00, 1),
(49, 1, 49, 'debit', 'payment', 'bills', 17.86, 'E.ON Strom – March 2025', NULL, 'E.ON Energie Deutschland', NULL, 'completed', 0.00, 1),
(50, 1, 50, 'debit', 'payment', 'gift', 7130.00, 'Galeria Kaufhof – gift & collection', NULL, 'Galeria Kaufhof', NULL, 'completed', 0.00, 1),
(51, 1, 51, 'debit', 'payment', 'bills', 17.34, 'Stadtwerke – April 2025', NULL, 'Stadtwerke Wiesbaden', NULL, 'completed', 0.00, 1),
(52, 1, 52, 'debit', 'payment', 'bills', 17.75, 'Vodafone – May 2025', NULL, 'Vodafone GmbH', NULL, 'completed', 0.00, 1),
(53, 1, 53, 'debit', 'payment', 'bills', 17.55, 'O2 Rechnung – June 2025', NULL, 'O2 Germany', NULL, 'completed', 0.00, 1),
(54, 1, 54, 'debit', 'transfer', 'other', 17000.00, 'Wire to Kendra Nielsen – Ref WN-62725', NULL, 'Kendra Nielsen', 'Erste Bank Wien', 'completed', 0.00, 1),
(55, 1, 55, 'debit', 'payment', 'bills', 17.82, 'Telekom Deutschland – July 2025', NULL, 'Telekom Deutschland GmbH', NULL, 'completed', 0.00, 1),
(56, 1, 56, 'debit', 'payment', 'bills', 17.22, '1&1 – August 2025', NULL, '1&1 Versatel GmbH', NULL, 'completed', 0.00, 1),
(57, 1, 57, 'credit', 'transfer', 'other', 37925.00, 'AOK Zuschuss – health Aug 2025', NULL, 'AOK Rheinland/Hamburg', 'Postbank Ndl. Bonn', 'completed', 0.00, 1),
(58, 1, 58, 'debit', 'payment', 'bills', 17.27, 'Congstar – Sept 2025', NULL, 'Congstar GmbH', NULL, 'completed', 0.00, 1),
(59, 1, 59, 'debit', 'payment', 'shopping', 5500.00, 'Parfümerie Hussong oHG – Wiesbaden', NULL, 'Parfümerie Hussong oHG', NULL, 'completed', 0.00, 1),
(60, 1, 60, 'debit', 'payment', 'bills', 17.66, 'E.ON Strom – Oct 2025', NULL, 'E.ON Energie Deutschland', NULL, 'completed', 0.00, 1),
(61, 1, 61, 'debit', 'payment', 'bills', 17.52, 'Vodafone – Nov 2025', NULL, 'Vodafone GmbH', NULL, 'completed', 0.00, 1),
(62, 1, 62, 'debit', 'payment', 'shopping', 6750.00, 'E-Bike Center Mainz – electric bike', NULL, 'E-Bike Center Mainz', NULL, 'completed', 0.00, 1),
(63, 1, 63, 'debit', 'payment', 'bills', 17.79, 'O2 Rechnung – Dec 2025', NULL, 'O2 Germany', NULL, 'completed', 0.00, 1),
(64, 1, 64, 'debit', 'payment', 'shopping', 6400.00, 'SportScheck – gym equipment', NULL, 'SportScheck GmbH', NULL, 'completed', 0.00, 1),
(65, 1, 65, 'debit', 'payment', 'bills', 17.85, 'Telekom Deutschland – Jan 2026', NULL, 'Telekom Deutschland GmbH', NULL, 'completed', 0.00, 1),
(66, 1, 66, 'credit', 'transfer', 'salary', 1680000.00, 'Gehalt Jan 2026 – Muster GmbH', NULL, 'Muster GmbH Payroll', 'Targobank AG', 'completed', 0.00, 1),
(67, 1, 67, 'debit', 'payment', 'shopping', 28340.00, 'Ford Händler Mainz – accessories', NULL, 'Ford Autohaus Mainz', NULL, 'completed', 0.00, 1),
(68, 1, 68, 'debit', 'payment', 'shopping', 3920.00, 'Shopify store – kiddies order (declined)', NULL, 'Shopify Payments', NULL, 'failed', 0.00, 1),
(69, 1, 69, 'debit', 'payment', 'bills', 17.67, 'Congstar – Feb 2026 (declined)', NULL, 'Congstar GmbH', NULL, 'failed', 0.00, 1),
(70, 1, 70, 'debit', 'transfer', 'other', 25000.00, 'Wire to Paul Hartman – Ref WH-22726 (declined)', NULL, 'Paul Hartman', 'UBS Switzerland', 'failed', 0.00, 1);

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
(527, 150, '736640', 'email', 1, '2026-08-12 19:21:33', '2026-08-12 19:11:33', 'login'),
(528, 150, '885751', 'email', 1, '2026-08-12 22:38:02', '2026-08-12 22:28:02', 'login'),
(529, 150, '967999', 'email', 1, '2026-08-12 22:42:31', '2026-08-12 22:32:31', 'transfer'),
(530, 150, '365155', 'email', 1, '2026-08-12 22:43:50', '2026-08-12 22:33:50', 'login'),
(531, 150, '869935', 'email', 1, '2026-08-13 00:31:23', '2026-08-13 00:21:23', 'login'),
(532, 150, '365434', 'email', 1, '2026-08-13 15:55:36', '2026-08-13 15:45:36', 'login'),
(533, 150, '271249', 'email', 1, '2026-08-13 18:43:54', '2026-08-13 18:33:54', 'login'),
(535, 150, '828760', 'email', 1, '2026-08-17 01:52:31', '2026-08-17 01:42:31', 'login'),
(536, 150, '458754', 'email', 1, '2026-08-17 20:53:58', '2026-08-17 20:43:58', 'login'),
(537, 150, '774291', 'email', 1, '2026-08-21 16:26:13', '2026-08-21 16:16:13', 'login');

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
(3, 'admin@demo.com', '$2y$12$vySF0Qua/n5QZwGl9qEFsupSmRsEkWaslKLGhtny.c1XlRyQq5mXC', 'Admin User', '+1234567891', '1985-01-01', NULL, '456 Admin Avenue', 'Admin City', 'Admin State', 'United States', '54321', NULL, 'admin', 1, 'active', 'verified', 0, NULL, NULL, 0, 'email', NULL, NULL, NULL, NULL, '2026-08-24 21:44:05', 1, 0, NULL, NULL, 'en', 'USD', 0.00, '$2y$10$Q1PjPMemugsGthLoGy37GOFdWdbAKDyk9P8cnGHw3iotKzcR3Iaa6', '$2y$10$ASwi5xJx4ax.EBuEkJVfr.wa15SBxxNbIMQ42fWKvYE/fGB25TATK', '$2y$10$bZlUWmGoHKLIMvACEDK1muZ.b7gCp3lTClANesOuPE1nT8ATEYsD6', 1, 'normal', '2025-10-08 22:44:52', '2026-08-24 21:44:05', 0, 1, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0),
(150, 'ElonmuskEthereumportfolio@outlook.com', '$2y$12$XmZZ1pz7YXJtR9kl5zbrtea7P96ZHI8BWmRCXNKl3h6TH0j92rA6W', 'Sheehy Marion Elon Investment Fund', '+1 (803) 932-8491', '1988-11-23', 'other', '2560 W Oak Ridge Rd, Orlando, FL 32809, United States', 'Orlando', 'florida', 'United States', '110224', NULL, 'user', 0, 'active', 'verified', 0, NULL, '2026-08-12 18:52:31', 1, 'email', 'Admin created account', '$2y$12$PaX.FiYgmQ5SXqIQ0.ngG.haQcFuN66DbqO/uHam8rTNnqEG1yjcq', 'Admin created account', '$2y$12$/M0Oxzdi7vFYe8dLnMuhQuqGAvRHp46dwwSoIxtE1vdZyCfoOvoHS', '2026-08-21 16:18:37', 1, 0, '{\"email_notifications\":true,\"sms_notifications\":false,\"transaction_alerts\":true,\"login_alerts\":true,\"marketing_emails\":false}', NULL, 'en', 'USD', 0.00, '$2y$10$PA3EsG5PDFBxI4TMdna33e4w1/xPDANmh3iMXPruxeoRYB2MDccbq', NULL, '$2y$10$rCYtqTZR4SvrQgDk2/WgluLvOm22Eq17a.m.7UfSUZdFTSyXfqS4W', 0, 'normal', '2026-08-12 18:52:31', '2026-08-21 16:18:37', 1, 1, '1757640497', 1, '7844386020', 0, '2663940619', 0, '2936961112', 0, '8644502890', 0);

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
-- Indexes for table `transaction_generation_batches`
--
ALTER TABLE `transaction_generation_batches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `batch_id` (`batch_id`),
  ADD UNIQUE KEY `idempotency_key` (`idempotency_key`),
  ADD KEY `idx_params_hash` (`params_hash`),
  ADD KEY `idx_account_status` (`account_id`,`status`),
  ADD KEY `idx_user_account` (`user_id`,`account_id`);

--
-- Indexes for table `transaction_templates`
--
ALTER TABLE `transaction_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_account_type` (`account_type`);

--
-- Indexes for table `transaction_template_items`
--
ALTER TABLE `transaction_template_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_template_sort` (`template_id`,`sort_order`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;

--
-- AUTO_INCREMENT for table `account_owners`
--
ALTER TABLE `account_owners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2510;

--
-- AUTO_INCREMENT for table `admin_audit_logs`
--
ALTER TABLE `admin_audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=293;

--
-- AUTO_INCREMENT for table `admin_sessions`
--
ALTER TABLE `admin_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `banks`
--
ALTER TABLE `banks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=275;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=148;

--
-- AUTO_INCREMENT for table `exchange_rates`
--
ALTER TABLE `exchange_rates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=429;

--
-- AUTO_INCREMENT for table `investment_funding`
--
ALTER TABLE `investment_funding`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `loan_payments`
--
ALTER TABLE `loan_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=145;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=316;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=163;

--
-- AUTO_INCREMENT for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `schema_migrations`
--
ALTER TABLE `schema_migrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1677;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1889;

--
-- AUTO_INCREMENT for table `transaction_generation_batches`
--
ALTER TABLE `transaction_generation_batches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `transaction_templates`
--
ALTER TABLE `transaction_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `transaction_template_items`
--
ALTER TABLE `transaction_template_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `two_factor_codes`
--
ALTER TABLE `two_factor_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=538;

--
-- AUTO_INCREMENT for table `update_logs`
--
ALTER TABLE `update_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=151;

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
-- Constraints for table `transaction_template_items`
--
ALTER TABLE `transaction_template_items`
  ADD CONSTRAINT `fk_template_items_template` FOREIGN KEY (`template_id`) REFERENCES `transaction_templates` (`id`) ON DELETE CASCADE;

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
