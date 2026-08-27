-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 27, 2026 at 04:11 PM
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
-- Database: `u502532383_westvault22`
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
(147, 153, '202636540564', 'savings', 'Savings Account', 354.79, 354.79, 'USD', 0.00, 0.00, 10000000000.00, 'active', '2026-08-25 02:45:51', NULL, '2026-08-25 02:45:51', '2026-08-27 14:16:23'),
(148, 154, '202639143387', 'savings', 'Savings Account', 0.00, 0.00, 'EGP', 0.00, 0.00, 10000000000.00, 'active', '2026-08-25 12:35:01', NULL, '2026-08-25 12:35:01', '2026-08-25 12:35:01'),
(149, 155, '202671662111', 'savings', 'Savings Account', 1245603.20, 1245603.20, 'USD', 0.00, 0.00, 10000000000.00, 'active', '2026-08-26 04:58:20', NULL, '2026-08-26 04:58:20', '2026-08-26 11:50:46'),
(150, 156, '202683537345', 'savings', 'Savings Account', 0.00, 0.00, 'USD', 0.00, 0.00, 10000000000.00, 'active', '2026-08-26 12:00:18', NULL, '2026-08-26 12:00:18', '2026-08-26 12:00:18'),
(151, 157, '202686372223', 'savings', 'Savings Account', 3668428.80, 3668428.80, 'USD', 0.00, 0.00, 10000000000.00, 'active', '2026-08-26 12:30:51', NULL, '2026-08-26 12:30:51', '2026-08-26 13:27:29'),
(152, 158, '202626661818', 'savings', 'Savings Account', 2401.83, 2401.83, 'USD', 0.00, 0.00, 10000000000.00, 'active', '2026-08-26 12:35:57', NULL, '2026-08-26 12:35:57', '2026-08-27 15:21:58'),
(153, 159, '202657736088', 'savings', 'Savings Account', 2141257.60, 2141257.60, 'USD', 0.00, 0.00, 10000000000.00, 'active', '2026-08-26 16:35:04', NULL, '2026-08-26 16:35:04', '2026-08-26 17:26:19'),
(154, 160, '202644066924', 'savings', 'Savings Account', 1502500.00, 1502500.00, 'USD', 0.00, 0.00, 10000000000.00, 'active', '2026-08-26 21:18:24', NULL, '2026-08-26 21:18:24', '2026-08-26 21:35:08');

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
(2468, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ElonmuskEthereumportfolio@outlook.com (ID: 150)', '102.88.113.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 18:53:24'),
(2471, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.88.113.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 18:54:40'),
(2472, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ElonmuskEthereumportfolio@outlook.com (ID: 150)', '102.88.113.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 18:56:27'),
(2473, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.88.113.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 18:56:54'),
(2474, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ElonmuskEthereumportfolio@outlook.com (ID: 150)', '102.88.113.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 18:57:55'),
(2475, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.88.113.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 18:58:17'),
(2476, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ElonmuskEthereumportfolio@outlook.com (ID: 150)', '102.88.113.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 18:59:13'),
(2477, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.88.113.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 18:59:38'),
(2478, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ElonmuskEthereumportfolio@outlook.com (ID: 150)', '102.88.113.241', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 19:01:54'),
(2483, 3, 'LOGIN', 'User logged in', '51.158.254.156', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 23:06:54'),
(2484, 3, 'LOGOUT', 'User logged out', '51.158.254.156', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 23:09:12'),
(2485, 3, 'LOGIN', 'User logged in', '51.158.254.156', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 23:09:39'),
(2486, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ElonmuskEthereumportfolio@outlook.com (ID: 150)', '51.158.254.156', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 23:11:10'),
(2487, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '51.158.254.156', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 23:11:34'),
(2488, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ElonmuskEthereumportfolio@outlook.com (ID: 150)', '51.158.254.156', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 23:13:42'),
(2489, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '51.158.254.156', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 23:14:06'),
(2490, 3, 'ADMIN_TOGGLE_IMF', 'Set imf_required=1 for user ElonmuskEthereumportfolio@outlook.com (ID: 150)', '51.158.254.156', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-12 23:14:40'),
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
(2509, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.89.69.110', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-24 21:49:33'),
(2510, 3, 'LOGIN', 'User logged in', '102.89.69.110', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-25 01:44:06'),
(2511, 3, 'USER_DELETED', 'Deleted user: ElonmuskEthereumportfolio@outlook.com (ID: 150)', '102.89.69.110', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-25 01:44:16'),
(2512, 3, 'ADMIN_USER_CREATED', 'Created new admin user: western@vaultibk.com', '102.89.69.110', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-25 01:44:46'),
(2519, 151, 'LOGIN', 'User logged in', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-25 02:01:47'),
(2520, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ekwensu42@gmail.com (ID: 152)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-25 02:04:11'),
(2524, 151, 'LOGIN', 'User logged in', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-25 02:38:03'),
(2525, 151, 'LOGOUT', 'User logged out', '154.227.129.31', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Safari/537.36', '2026-08-25 02:40:00'),
(2526, 151, 'LOGIN', 'User logged in', '154.227.129.31', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Safari/537.36', '2026-08-25 02:40:30'),
(2527, 151, 'LOGOUT', 'User logged out', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-25 02:41:35'),
(2528, 151, 'LOGIN', 'User logged in', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-25 02:41:47'),
(2529, 151, 'USER_DELETED', 'Deleted user: ekwensu42@gmail.com (ID: 152)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-25 02:42:06'),
(2530, 151, 'KYC_AUTO_VERIFIED', 'Auto-verified KYC for user 153 during account creation', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-25 02:45:51');
INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `details`, `ip_address`, `user_agent`, `created_at`) VALUES
(2531, 153, 'ACCOUNT_CREATED', 'Created savings account: 202636540564', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-25 02:45:51'),
(2532, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ekwensu42@gmail.com (ID: 153)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-25 02:46:13'),
(2533, 153, 'LOGIN', 'User logged in', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '2026-08-25 02:48:58'),
(2534, 153, 'LOGIN_PIN_UPDATED', 'User updated their login PIN', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '2026-08-25 02:50:16'),
(2535, 153, 'TRANSFER_PIN_UPDATED', 'User updated their transfer PIN', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '2026-08-25 02:51:21'),
(2536, 153, 'TWO_FACTOR_ENABLED', 'User enabled two-factor authentication', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '2026-08-25 02:51:32'),
(2537, 153, 'LOGOUT', 'User logged out', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '2026-08-25 02:51:50'),
(2538, 153, 'LOGIN', 'User logged in', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '2026-08-25 02:52:40'),
(2539, 153, 'LOGOUT', 'User logged out', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-25 02:55:28'),
(2540, 151, 'LOGIN', 'User logged in', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-25 02:55:52'),
(2541, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ekwensu42@gmail.com (ID: 153)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-25 03:00:36'),
(2542, 153, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ekwensu42@gmail.com (ID: 153)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-25 03:05:32'),
(2543, 153, 'transfer_funds', 'Transferred $5,000.00 to Ggvv (Fee: $125.00)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-25 03:15:50'),
(2544, 153, 'LOGIN', 'User logged in', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '2026-08-25 03:34:23'),
(2545, 153, 'CARD_CREATED', 'New debit card created', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '2026-08-25 03:35:54'),
(2546, 153, 'LOGIN', 'User logged in', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '2026-08-25 03:38:28'),
(2547, 153, 'transfer_funds', 'Transferred $1,500.00 to Ydtyh (Fee: $7.50)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '2026-08-25 03:43:38'),
(2548, 153, 'transfer_funds', 'Transferred $1,500.00 to Steve Henry (Fee: $7.50)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-25 03:50:11'),
(2549, 153, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-25 04:09:31'),
(2550, 153, 'LOGOUT', 'User logged out', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-25 04:10:06'),
(2551, 151, 'LOGIN', 'User logged in', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-25 04:10:38'),
(2552, 151, 'ADMIN_REVERSE_TRANSACTION', 'Reversed transaction TXN6A8D10F3EF433 for user ekwensu42@gmail.com', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-25 04:12:06'),
(2553, 151, 'ADMIN_REVERSE_TRANSACTION', 'Reversed transaction REVTXN6A8D10F3EF433 for user ekwensu42@gmail.com', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-25 04:14:11'),
(2554, 151, 'ADMIN_DELETE_TRANSACTION', 'Deleted transaction REVREVTXN6A8D10F3EF433 for user ekwensu42@gmail.com. Reason: Transaction error', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-25 04:15:18'),
(2555, 153, 'LOGIN', 'User logged in', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '2026-08-25 04:17:30'),
(2556, 151, 'LOGIN', 'User logged in', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-25 09:01:58'),
(2557, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ekwensu42@gmail.com (ID: 153)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-25 09:04:59'),
(2558, 153, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ekwensu42@gmail.com (ID: 153)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-25 09:10:33'),
(2559, 151, 'LOGIN', 'User logged in', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-25 12:28:05'),
(2560, 154, 'ACCOUNT_CREATED', 'Created savings account: 202639143387', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Mobile Safari/537.36', '2026-08-25 12:35:01'),
(2561, 154, 'LOGIN', 'User session established', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-25 12:35:44'),
(2562, 154, 'LOGIN', 'User logged in', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Mobile Safari/537.36', '2026-08-25 12:36:17'),
(2563, 154, 'LOGIN_PIN_UPDATED', 'User updated their login PIN', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Mobile Safari/537.36', '2026-08-25 12:36:55'),
(2564, 154, 'TRANSFER_PIN_UPDATED', 'User updated their transfer PIN', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Mobile Safari/537.36', '2026-08-25 12:37:37'),
(2565, 154, 'TWO_FACTOR_ENABLED', 'User enabled two-factor authentication', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Mobile Safari/537.36', '2026-08-25 12:37:46'),
(2566, 154, 'KYC_SUBMITTED', 'User submitted KYC verification', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Mobile Safari/537.36', '2026-08-25 12:47:34'),
(2567, 154, 'LOGOUT', 'User logged out', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-25 12:48:31'),
(2568, 151, 'LOGIN', 'User logged in', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-25 12:48:49'),
(2569, 151, 'KYC_APPROVED', 'Approved KYC ID: 46', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-25 12:50:11'),
(2570, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user henryahmed638@gmail.com (ID: 154)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-25 12:56:54'),
(2571, 154, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ekwensu42@gmail.com (ID: 153)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-25 12:57:16'),
(2572, 151, 'LOGIN', 'User logged in', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-25 16:39:46'),
(2573, 151, 'LOGIN', 'User logged in', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-25 23:53:55'),
(2574, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ekwensu42@gmail.com (ID: 153)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-25 23:57:23'),
(2575, 153, 'ADMIN_LOGIN_AS_USER', 'Logged in as user henryahmed638@gmail.com (ID: 154)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-25 23:59:28'),
(2576, 154, 'LOGOUT', 'User logged out', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 00:00:14'),
(2577, 151, 'LOGIN', 'User logged in', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 00:00:27'),
(2578, 154, 'LOGIN', 'User logged in', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '2026-08-26 00:01:52'),
(2579, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user henryahmed638@gmail.com (ID: 154)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 00:03:44'),
(2580, 154, 'ADMIN_LOGIN_AS_USER', 'Logged in as user henryahmed638@gmail.com (ID: 154)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 00:13:36'),
(2581, 154, 'LOGOUT', 'User logged out', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 00:18:30'),
(2582, 151, 'LOGIN', 'User logged in', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 00:18:42'),
(2583, 3, 'LOGIN', 'User logged in', '102.89.75.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-26 02:53:29'),
(2584, 3, 'ADMIN_SET_TRANSFER_OTP', 'Set transfer_otp_required=0 for user ekwensu42@gmail.com (ID: 153)', '102.89.75.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-26 02:54:01'),
(2585, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ekwensu42@gmail.com (ID: 153)', '102.89.75.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-26 02:55:37'),
(2586, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.89.75.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-26 03:01:24'),
(2587, 3, 'ADMIN_SET_USER_CURRENCY', 'Set currency=RUB (country=Russia) for user ekwensu42@gmail.com (ID: 153)', '102.89.75.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-26 03:44:10'),
(2588, 3, 'ADMIN_TOGGLE_2FA', 'Admin admin@demo.com disabled two-factor authentication for user ekwensu42@gmail.com', '102.89.75.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-26 03:44:38'),
(2589, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ekwensu42@gmail.com (ID: 153)', '102.89.75.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-26 03:44:55'),
(2590, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.89.75.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-26 03:51:57'),
(2591, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ekwensu42@gmail.com (ID: 153)', '102.89.75.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-26 03:52:11'),
(2592, 151, 'LOGIN', 'User logged in', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 03:53:33'),
(2593, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.89.75.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-26 03:54:44'),
(2594, 151, 'ADMIN_SET_USER_CURRENCY', 'Set currency=CAD (country=Canada) for user henryahmed638@gmail.com (ID: 154)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 03:55:50'),
(2595, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user henryahmed638@gmail.com (ID: 154)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 03:56:08'),
(2596, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ekwensu42@gmail.com (ID: 153)', '102.89.75.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-26 03:57:32'),
(2597, 154, 'ADMIN_LOGIN_AS_USER', 'Logged in as user henryahmed638@gmail.com (ID: 154)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 03:59:56'),
(2598, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.89.75.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-26 04:00:23'),
(2599, 154, 'LOGOUT', 'User logged out', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 04:01:34'),
(2600, 151, 'LOGIN', 'User logged in', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 04:01:50'),
(2601, 151, 'ADMIN_SET_USER_CURRENCY', 'Set currency=ZAR (country=South Africa) for user henryahmed638@gmail.com (ID: 154)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 04:02:31'),
(2602, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user henryahmed638@gmail.com (ID: 154)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 04:02:40'),
(2603, 154, 'TWO_FACTOR_DISABLED', 'User disabled two-factor authentication', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 04:05:20'),
(2604, 154, 'LOGIN', 'User logged in', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '2026-08-26 04:05:56'),
(2605, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ekwensu42@gmail.com (ID: 153)', '102.89.75.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-26 04:05:57'),
(2606, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.89.75.85', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-26 04:06:31'),
(2607, 154, 'TWO_FACTOR_ENABLED', 'User enabled two-factor authentication', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 04:06:35'),
(2608, 154, 'LOGOUT', 'User logged out', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '2026-08-26 04:06:59'),
(2609, 154, 'TWO_FACTOR_DISABLED', 'User disabled two-factor authentication', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 04:07:40'),
(2610, 154, 'LOGIN', 'User logged in', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '2026-08-26 04:08:12'),
(2611, 154, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ekwensu42@gmail.com (ID: 153)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 04:11:11'),
(2612, 153, 'LOGOUT', 'User logged out', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 04:11:42'),
(2613, 151, 'LOGIN', 'User logged in', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 04:11:52'),
(2614, 151, 'ADMIN_SET_USER_CURRENCY', 'Set currency=EUR (country=Germany) for user ekwensu42@gmail.com (ID: 153)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 04:12:29'),
(2615, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ekwensu42@gmail.com (ID: 153)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 04:12:39'),
(2616, 153, 'transfer_funds', 'Transferred $1,749.88 to Steve (Fee: $8.75)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 04:15:07'),
(2617, 153, 'transfer_funds', 'Transferred $1,166.59 to Steve  Henry (Fee: $29.16)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 04:25:02'),
(2618, 153, 'ADMIN_LOGIN_AS_USER', 'Logged in as user henryahmed638@gmail.com (ID: 154)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 04:27:51'),
(2619, 154, 'LOGOUT', 'User logged out', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 04:29:06'),
(2620, 151, 'LOGIN', 'User logged in', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 04:29:22'),
(2621, 151, 'LOGOUT', 'User logged out', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 04:31:32'),
(2622, 151, 'LOGIN', 'User logged in', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 04:31:42'),
(2623, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user henryahmed638@gmail.com (ID: 154)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 04:31:55'),
(2624, 154, 'ADMIN_SET_USER_CURRENCY', 'Set currency=SAR (country=Saudi Arabia) for user henryahmed638@gmail.com (ID: 154)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 04:32:40'),
(2625, 154, 'ADMIN_LOGIN_AS_USER', 'Logged in as user henryahmed638@gmail.com (ID: 154)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 04:32:52'),
(2626, 154, 'LOGOUT', 'User logged out', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 04:34:45'),
(2627, 151, 'LOGIN', 'User logged in', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 04:34:54'),
(2628, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ekwensu42@gmail.com (ID: 153)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 04:35:09'),
(2629, 153, 'KYC_AUTO_VERIFIED', 'Auto-verified KYC for user 155 during account creation', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 04:58:20'),
(2630, 155, 'ACCOUNT_CREATED', 'Created savings account: 202671662111', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 04:58:20'),
(2631, 151, 'LOGIN', 'User logged in', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 04:58:31'),
(2632, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user andrewjarry15@gmail.com (ID: 155)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 04:58:58'),
(2633, 155, 'TRANSFER_PIN_UPDATED', 'User updated their transfer PIN', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 04:59:58'),
(2634, 155, 'LOGIN_PIN_UPDATED', 'User updated their login PIN', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 05:00:42'),
(2635, 155, 'ADMIN_LOGIN_AS_USER', 'Logged in as user andrewjarry15@gmail.com (ID: 155)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 05:00:52'),
(2636, 155, 'LOGOUT', 'User logged out', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 05:01:48'),
(2637, 151, 'LOGIN', 'User logged in', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 05:01:58'),
(2638, 151, 'ADMIN_SET_USER_CURRENCY', 'Set currency=GBP (country=United Kingdom) for user andrewjarry15@gmail.com (ID: 155)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 05:02:35'),
(2639, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user andrewjarry15@gmail.com (ID: 155)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 05:03:01'),
(2640, 155, 'ADMIN_LOGIN_AS_USER', 'Logged in as user andrewjarry15@gmail.com (ID: 155)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 05:08:26'),
(2641, 155, 'LOGOUT', 'User logged out', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 05:09:29'),
(2642, 151, 'LOGIN', 'User logged in', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 05:09:37'),
(2643, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user andrewjarry15@gmail.com (ID: 155)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 05:12:52'),
(2644, 155, 'ADMIN_LOGIN_AS_USER', 'Logged in as user henryahmed638@gmail.com (ID: 154)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 05:20:37'),
(2645, 154, 'LOGOUT', 'User logged out', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 05:21:07'),
(2646, 151, 'LOGIN', 'User logged in', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 05:21:18'),
(2647, 151, 'ADMIN_SET_USER_CURRENCY', 'Set currency=USD (country=United States) for user henryahmed638@gmail.com (ID: 154)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 05:21:49'),
(2648, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user henryahmed638@gmail.com (ID: 154)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 05:22:02'),
(2649, 154, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ekwensu42@gmail.com (ID: 153)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 05:22:44'),
(2650, 153, 'LOGOUT', 'User logged out', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 05:23:36'),
(2651, 151, 'LOGIN', 'User logged in', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 05:23:45'),
(2652, 151, 'LOGIN', 'User logged in', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 05:29:49'),
(2653, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user andrewjarry15@gmail.com (ID: 155)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 05:38:10'),
(2654, 155, 'ADMIN_LOGIN_AS_USER', 'Logged in as user andrewjarry15@gmail.com (ID: 155)', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 05:49:20'),
(2655, 154, 'LOGIN', 'User logged in', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '2026-08-26 05:50:34'),
(2656, 155, 'LOGOUT', 'User logged out', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 05:54:29'),
(2657, 151, 'LOGIN', 'User logged in', '154.227.129.31', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 05:54:37'),
(2658, 151, 'LOGIN', 'User logged in', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 11:49:25'),
(2659, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user andrewjarry15@gmail.com (ID: 155)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 11:50:59'),
(2660, 155, 'KYC_AUTO_VERIFIED', 'Auto-verified KYC for user 156 during account creation', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 12:00:18'),
(2661, 156, 'ACCOUNT_CREATED', 'Created savings account: 202683537345', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 12:00:18'),
(2662, 155, 'ADMIN_SET_USER_CURRENCY', 'Set currency=LKR (country=Sri Lanka) for user Ilangasingheshanika8@gmail.com (ID: 156)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 12:01:12'),
(2663, 155, 'ADMIN_LOGIN_AS_USER', 'Logged in as user Ilangasingheshanika8@gmail.com (ID: 156)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 12:01:28'),
(2664, 156, 'LOGIN', 'User logged in', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '2026-08-26 12:06:00'),
(2665, 156, 'LOGIN_PIN_UPDATED', 'User updated their login PIN', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '2026-08-26 12:07:25'),
(2666, 156, 'TRANSFER_PIN_UPDATED', 'User updated their transfer PIN', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '2026-08-26 12:08:55'),
(2667, 156, 'LOGOUT', 'User logged out', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 12:13:49'),
(2668, 151, 'LOGIN', 'User logged in', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 12:14:32'),
(2669, 156, 'LOGIN', 'User logged in', '5.162.85.167', 'Mozilla/5.0 (Linux; U; Android 11; en-gb; CPH2349 Build/RP1A.200720.011) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.7727.138 Mobile Safari/537.36 PHX/22.0', '2026-08-26 12:16:24'),
(2670, 156, 'LOGIN', 'User logged in', '5.162.85.167', 'Mozilla/5.0 (Linux; U; Android 11; en-gb; CPH2349 Build/RP1A.200720.011) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.7727.138 Mobile Safari/537.36 PHX/22.0', '2026-08-26 12:27:37'),
(2671, 151, 'KYC_AUTO_VERIFIED', 'Auto-verified KYC for user 157 during account creation', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 12:30:51'),
(2672, 157, 'ACCOUNT_CREATED', 'Created savings account: 202686372223', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 12:30:51'),
(2673, 151, 'KYC_AUTO_VERIFIED', 'Auto-verified KYC for user 158 during account creation', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 12:35:57'),
(2674, 158, 'ACCOUNT_CREATED', 'Created savings account: 202626661818', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 12:35:57'),
(2675, 156, 'LOGOUT', 'User logged out', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '2026-08-26 12:36:17'),
(2676, 158, 'LOGIN', 'User logged in', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '2026-08-26 12:36:57'),
(2677, 158, 'LOGIN_PIN_UPDATED', 'User updated their login PIN', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '2026-08-26 12:37:25'),
(2678, 158, 'TRANSFER_PIN_UPDATED', 'User updated their transfer PIN', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '2026-08-26 12:37:49'),
(2679, 151, 'ADMIN_UPLOAD_PROFILE_PICTURE', 'Uploaded profile picture for user benjaminedward854@gmail.com (ID: 157)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 12:48:01'),
(2680, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user benjaminedward854@gmail.com (ID: 157)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 12:48:19'),
(2681, 157, 'TRANSFER_PIN_UPDATED', 'User updated their transfer PIN', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 12:48:59'),
(2682, 157, 'LOGIN_PIN_UPDATED', 'User updated their login PIN', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 12:49:25'),
(2683, 157, 'LOGOUT', 'User logged out', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 12:53:26'),
(2684, 151, 'LOGIN', 'User logged in', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 12:54:45'),
(2685, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ofornaogwu@gmail.com (ID: 158)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 13:08:20'),
(2686, 158, 'ADMIN_LOGIN_AS_USER', 'Logged in as user benjaminedward854@gmail.com (ID: 157)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 13:08:32'),
(2687, 157, 'LOGOUT', 'User logged out', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 13:09:44'),
(2688, 151, 'LOGIN', 'User logged in', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 13:10:44'),
(2689, 157, 'LOGIN', 'User logged in', '5.162.85.167', 'Mozilla/5.0 (Linux; U; Android 11; en-gb; CPH2349 Build/RP1A.200720.011) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.7727.138 Mobile Safari/537.36 PHX/22.0', '2026-08-26 13:11:32'),
(2690, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user benjaminedward854@gmail.com (ID: 157)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 13:11:37'),
(2691, 157, 'ADMIN_LOGIN_AS_USER', 'Logged in as user benjaminedward854@gmail.com (ID: 157)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 13:15:36'),
(2692, 157, 'LOGOUT', 'User logged out', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 13:16:11'),
(2693, 151, 'LOGIN', 'User logged in', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 13:17:05'),
(2694, 157, 'LOGIN', 'User logged in', '105.112.227.26', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.5.2 Mobile/15E148 Safari/604.1', '2026-08-26 13:18:43'),
(2695, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user benjaminedward854@gmail.com (ID: 157)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 13:20:49'),
(2696, 157, 'LOGOUT', 'User logged out', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 13:25:51'),
(2697, 151, 'LOGIN', 'User logged in', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 13:25:58'),
(2698, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user benjaminedward854@gmail.com (ID: 157)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 13:27:42'),
(2699, 157, 'LOGIN', 'User logged in', '102.90.123.28', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.6 Mobile/15E148 Safari/604.1', '2026-08-26 14:01:36'),
(2700, 151, 'LOGIN', 'User logged in', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 16:27:10'),
(2701, 151, 'KYC_AUTO_VERIFIED', 'Auto-verified KYC for user 159 during account creation', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 16:35:04'),
(2702, 159, 'ACCOUNT_CREATED', 'Created savings account: 202657736088', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 16:35:04'),
(2703, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user henryahmed1998@gmail.com (ID: 159)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 16:35:31'),
(2704, 159, 'LOGIN_PIN_UPDATED', 'User updated their login PIN', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 16:35:51'),
(2705, 159, 'TRANSFER_PIN_UPDATED', 'User updated their transfer PIN', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 16:36:09'),
(2706, 159, 'LOGOUT', 'User logged out', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 16:41:01'),
(2707, 151, 'LOGIN', 'User logged in', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 16:41:26'),
(2708, 151, 'LOGOUT', 'User logged out', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 16:46:24'),
(2709, 151, 'LOGIN', 'User logged in', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 16:46:40'),
(2710, 151, 'LOGIN', 'User logged in', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 16:48:50'),
(2711, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user henryahmed1998@gmail.com (ID: 159)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 16:51:07'),
(2712, 159, 'ADMIN_LOGIN_AS_USER', 'Logged in as user henryahmed1998@gmail.com (ID: 159)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 16:51:26'),
(2713, 151, 'LOGIN', 'User logged in', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 16:54:03'),
(2714, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user henryahmed1998@gmail.com (ID: 159)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 16:57:30'),
(2715, 159, 'LOGOUT', 'User logged out', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 17:12:12'),
(2716, 151, 'LOGIN', 'User logged in', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 17:12:28'),
(2717, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user henryahmed1998@gmail.com (ID: 159)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 17:15:20'),
(2718, 159, 'LOGOUT', 'User logged out', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 17:22:56'),
(2719, 151, 'LOGIN', 'User logged in', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 17:23:07'),
(2720, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user henryahmed1998@gmail.com (ID: 159)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 17:26:37'),
(2721, 159, 'LOGOUT', 'User logged out', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 17:27:35'),
(2722, 151, 'LOGIN', 'User logged in', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 17:27:44'),
(2723, 151, 'LOGIN', 'User logged in', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 21:14:59'),
(2724, 151, 'KYC_AUTO_VERIFIED', 'Auto-verified KYC for user 160 during account creation', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 21:18:24'),
(2725, 160, 'ACCOUNT_CREATED', 'Created savings account: 202644066924', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 21:18:24'),
(2726, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user henryedwardh481@gmail.com (ID: 160)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 21:20:28'),
(2727, 160, 'LOGOUT', 'User logged out', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 21:21:14'),
(2728, 151, 'LOGIN', 'User logged in', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 21:21:39'),
(2729, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user henryedwardh481@gmail.com (ID: 160)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 21:22:19'),
(2730, 160, 'LOGIN_PIN_UPDATED', 'User updated their login PIN', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 21:22:45'),
(2731, 160, 'TRANSFER_PIN_UPDATED', 'User updated their transfer PIN', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 21:22:59'),
(2732, 160, 'LOGOUT', 'User logged out', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 21:25:43'),
(2733, 151, 'LOGIN', 'User logged in', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 21:25:51'),
(2734, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user henryedwardh481@gmail.com (ID: 160)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 21:30:39'),
(2735, 160, 'LOGOUT', 'User logged out', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 21:33:47'),
(2736, 151, 'LOGIN', 'User logged in', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 21:33:55'),
(2737, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user henryedwardh481@gmail.com (ID: 160)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-26 21:35:17'),
(2738, 3, 'LOGIN', 'User session established', '102.88.110.63', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-27 00:15:07'),
(2739, 151, 'LOGIN', 'User session established', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 00:15:32');
INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `details`, `ip_address`, `user_agent`, `created_at`) VALUES
(2740, 151, 'LOGIN', 'User session established', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 00:15:57'),
(2741, 151, 'LOGOUT', 'User logged out', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 00:16:00'),
(2742, 160, 'LOGIN', 'User session established', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 00:16:16'),
(2743, 160, 'LOGIN', 'User session established', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '2026-08-27 00:18:20'),
(2744, 160, 'LOGOUT', 'User logged out', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 00:18:59'),
(2745, 151, 'LOGIN', 'User session established', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 00:19:17'),
(2746, 151, 'ADMIN_SET_USER_CURRENCY', 'Set currency=GBP (country=United Kingdom) for user henryedwardh481@gmail.com (ID: 160)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 00:19:36'),
(2747, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user henryedwardh481@gmail.com (ID: 160)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 00:19:47'),
(2748, 159, 'LOGIN', 'User session established', '152.167.182.174', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-27 00:27:13'),
(2749, 160, 'USER_UPLOAD_PROFILE_PICTURE', 'Updated own profile picture', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '2026-08-27 00:28:30'),
(2750, 160, 'USER_UPLOAD_PROFILE_PICTURE', 'Updated own profile picture', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '2026-08-27 00:30:36'),
(2751, 159, 'LOGIN', 'User session established', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '2026-08-27 00:38:53'),
(2752, 159, 'USER_UPLOAD_PROFILE_PICTURE', 'Updated own profile picture', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '2026-08-27 00:39:42'),
(2753, 160, 'LOGIN', 'User session established', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '2026-08-27 00:42:37'),
(2754, 160, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ofornaogwu@gmail.com (ID: 158)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 00:46:32'),
(2755, 160, 'LOGIN', 'User session established', '38.57.65.18', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Mobile Safari/537.36', '2026-08-27 01:17:22'),
(2756, 160, 'LOGIN', 'User session established', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '2026-08-27 01:38:31'),
(2757, 160, 'LOGIN', 'User session established', '38.57.65.20', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Mobile Safari/537.36', '2026-08-27 01:39:54'),
(2758, 160, 'LOGIN', 'User session established', '38.57.65.25', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Mobile Safari/537.36', '2026-08-27 02:09:25'),
(2759, 160, 'LOGIN', 'User session established', '38.57.65.19', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Mobile Safari/537.36', '2026-08-27 02:18:56'),
(2760, 160, 'LOGIN', 'User session established', '38.57.65.19', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Mobile Safari/537.36', '2026-08-27 03:00:00'),
(2761, 160, 'LOGIN', 'User session established', '38.57.65.25', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Mobile Safari/537.36', '2026-08-27 03:14:15'),
(2762, 160, 'LOGIN', 'User session established', '38.57.65.21', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Mobile Safari/537.36', '2026-08-27 03:24:59'),
(2763, 151, 'LOGIN', 'User session established', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 08:53:47'),
(2764, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user henryedwardh481@gmail.com (ID: 160)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 08:54:36'),
(2765, 160, 'ADMIN_LOGIN_AS_USER', 'Logged in as user henryahmed1998@gmail.com (ID: 159)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 08:56:30'),
(2766, 159, 'LOGOUT', 'User logged out', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 08:57:57'),
(2767, 156, 'LOGIN', 'User session established', '5.162.85.167', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Mobile Safari/537.36', '2026-08-27 11:44:29'),
(2768, 3, 'LOGIN', 'User session established', '102.89.41.243', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-27 12:39:31'),
(2769, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user henryedwardh481@gmail.com (ID: 160)', '102.89.41.243', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-27 12:39:38'),
(2770, 151, 'LOGIN', 'User session established', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 12:43:28'),
(2771, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ofornaogwu@gmail.com (ID: 158)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 12:43:39'),
(2772, 158, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ofornaogwu@gmail.com (ID: 158)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 12:49:10'),
(2773, 158, 'LOGOUT', 'User logged out', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 12:50:21'),
(2774, 151, 'LOGIN', 'User session established', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 12:50:32'),
(2775, 151, 'ADMIN_SET_USER_CURRENCY', 'Set currency=INR (country=India) for user ofornaogwu@gmail.com (ID: 158)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 12:53:42'),
(2776, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ofornaogwu@gmail.com (ID: 158)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 12:54:08'),
(2777, 158, 'LOGOUT', 'User logged out', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 12:58:10'),
(2778, 151, 'LOGIN', 'User session established', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 12:58:19'),
(2779, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ofornaogwu@gmail.com (ID: 158)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 12:59:30'),
(2780, 158, 'LOGOUT', 'User logged out', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 13:21:16'),
(2781, 151, 'LOGIN', 'User session established', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 13:21:33'),
(2782, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ofornaogwu@gmail.com (ID: 158)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 13:26:31'),
(2783, 158, 'LOGOUT', 'User logged out', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 13:28:33'),
(2784, 151, 'LOGIN', 'User session established', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 13:28:41'),
(2785, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.89.76.184', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-27 14:14:43'),
(2786, 3, 'bank_deleted', 'Deleted bank ID: 103', '102.89.76.184', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-27 14:24:25'),
(2787, 3, 'bank_deleted', 'Deleted bank ID: 107', '102.89.76.184', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-27 14:24:34'),
(2788, 3, 'bank_deleted', 'Deleted bank ID: 108', '102.89.76.184', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-27 14:24:47'),
(2789, 3, 'bank_deleted', 'Deleted bank ID: 102', '102.89.76.184', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-27 14:25:08'),
(2790, 3, 'bank_deleted', 'Deleted bank ID: 109', '102.89.76.184', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-27 14:25:19'),
(2791, 3, 'bank_deleted', 'Deleted bank ID: 106', '102.89.76.184', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-27 14:25:51'),
(2792, 3, 'bank_deleted', 'Deleted bank ID: 101', '102.89.76.184', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-27 14:26:10'),
(2793, 3, 'bank_deleted', 'Deleted bank ID: 104', '102.89.76.184', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-27 14:26:19'),
(2794, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ekwensu42@gmail.com (ID: 153)', '102.89.76.184', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-27 14:37:47'),
(2795, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.89.76.184', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-27 14:37:53'),
(2796, 3, 'ADMIN_SET_TRANSACTION_MODE', 'Set transaction mode to \'force_pending\' for user ekwensu42@gmail.com (ID: 153)', '102.89.76.184', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-27 14:45:32'),
(2797, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ofornaogwu@gmail.com (ID: 158)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 14:47:22'),
(2798, 151, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 14:48:27'),
(2799, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user henryedwardh481@gmail.com (ID: 160)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 14:49:25'),
(2800, 151, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 14:49:32'),
(2801, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ofornaogwu@gmail.com (ID: 158)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 14:51:49'),
(2802, 151, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 14:59:39'),
(2803, 151, 'ADMIN_SET_USER_CURRENCY', 'Set currency=DOP (country=Dominican Republic) for user ofornaogwu@gmail.com (ID: 158)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 15:03:00'),
(2804, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ofornaogwu@gmail.com (ID: 158)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 15:03:11'),
(2805, 151, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 15:04:50'),
(2806, 151, 'ADMIN_SET_TRANSACTION_MODE', 'Set transaction mode to \'force_pending\' for user ofornaogwu@gmail.com (ID: 158)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 15:10:00'),
(2807, 151, 'ADMIN_SET_TRANSACTION_MODE', 'Set transaction mode to \'normal\' for user ofornaogwu@gmail.com (ID: 158)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 15:10:03'),
(2808, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ofornaogwu@gmail.com (ID: 158)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 15:17:47'),
(2809, 151, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 15:18:16'),
(2810, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ofornaogwu@gmail.com (ID: 158)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 15:20:00'),
(2811, 151, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 15:20:23'),
(2812, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ofornaogwu@gmail.com (ID: 158)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 15:22:09'),
(2813, 151, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 15:24:59'),
(2814, 151, 'ADMIN_LOGIN_AS_USER', 'Logged in as user ofornaogwu@gmail.com (ID: 158)', '154.227.128.16', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/30.0 Chrome/143.0.0.0 Mobile Safari/537.36', '2026-08-27 15:33:31'),
(2815, 3, 'ADMIN_LOGIN_AS_USER', 'Logged in as user mr.carter.tech07@gmail.com (ID: 153)', '102.89.76.184', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-27 15:55:40'),
(2816, 3, 'ADMIN_STOP_IMPERSONATING', 'Stopped impersonating and switched back to admin account', '102.89.76.184', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-27 15:57:13');

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
(350, 151, 158, 'balance_adjustment', 'Created credit transaction of USD 453.78 (display 41000 INR) for user ofornaogwu@gmail.com (ID: 158) - Status: completed', NULL, NULL, '2026-08-27 13:23:07'),
(351, 151, 158, 'balance_adjustment', 'Created debit transaction of USD 132.81 (display 12000 INR) for user ofornaogwu@gmail.com (ID: 158) - Status: completed', NULL, NULL, '2026-08-27 13:24:28'),
(352, 151, 158, 'balance_adjustment', 'Created debit transaction of USD 110.68 (display 10000 INR) for user ofornaogwu@gmail.com (ID: 158) - Status: completed', NULL, NULL, '2026-08-27 13:29:45'),
(353, 151, 158, 'balance_adjustment', 'Created debit transaction of USD 132.81 (display 12000 INR) for user ofornaogwu@gmail.com (ID: 158) - Status: completed', NULL, NULL, '2026-08-27 13:31:00'),
(354, 3, 153, 'balance_adjustment', 'Created debit transaction of USD 58.33 (display 50 EUR) for user ekwensu42@gmail.com (ID: 153) - Status: completed', NULL, NULL, '2026-08-27 14:16:23'),
(355, 151, 158, 'balance_adjustment', 'Created credit transaction of USD 1106.78 (display 100000 INR) for user ofornaogwu@gmail.com (ID: 158) - Status: completed', NULL, NULL, '2026-08-27 14:47:10'),
(356, 151, 158, 'balance_adjustment', 'Created credit transaction of USD 1106.78 (display 100000 INR) for user ofornaogwu@gmail.com (ID: 158) - Status: completed', NULL, NULL, '2026-08-27 14:51:10'),
(357, 151, 158, 'balance_adjustment', 'Created credit transaction of USD 189.64 (display 12000 DOP) for user ofornaogwu@gmail.com (ID: 158) - Status: completed', NULL, NULL, '2026-08-27 15:17:03'),
(358, 151, 158, 'balance_adjustment', 'Created debit transaction of USD 252.85 (display 16000 DOP) for user ofornaogwu@gmail.com (ID: 158) - Status: completed', NULL, NULL, '2026-08-27 15:19:50'),
(359, 151, 158, 'balance_adjustment', 'Created debit transaction of USD 158.03 (display 10000 DOP) for user ofornaogwu@gmail.com (ID: 158) - Status: completed', NULL, NULL, '2026-08-27 15:21:58');

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
-- Table structure for table `auto_migrations`
--

CREATE TABLE `auto_migrations` (
  `id` varchar(191) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` enum('success','failed') NOT NULL DEFAULT 'success',
  `error_message` text DEFAULT NULL,
  `applied_by` int(11) DEFAULT NULL,
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `auto_migrations`
--

INSERT INTO `auto_migrations` (`id`, `description`, `status`, `error_message`, `applied_by`, `applied_at`, `updated_at`) VALUES
('2026_08_26_currency_fx_display', 'Currency FX settings, display-currency column, and exchange_rates table', 'success', NULL, 3, '2026-08-26 03:29:43', '2026-08-26 03:29:43'),
('2026_08_26_transaction_successful_status', 'Add successful status to transactions enum for transfer receipts', 'success', NULL, 3, '2026-08-26 04:05:52', NULL),
('2026_08_27_140000_banks_all_countries', 'Seed banks for all countries (min 5 each, US max 15) and deactivate duplicates', 'success', NULL, 3, '2026-08-27 14:35:23', NULL);

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
(105, 'Goldman Sachs Bank', 'GS', 'north-america', 'United States', 'GOLDUS33', 0, NULL, '2025-11-06 18:25:10', '2026-08-27 14:35:23'),
(110, 'Capital One Bank', 'COF', 'north-america', 'United States', 'HIBKUS44', 0, NULL, '2025-11-06 18:25:10', '2026-08-27 14:35:23'),
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
(206, 'Suncorp Bank', 'SUN', 'oceania', 'Australia', 'METWAU4B', 0, NULL, '2025-11-06 18:25:10', '2026-08-27 14:35:23'),
(207, 'Bendigo Bank', 'BEN', 'oceania', 'Australia', 'BENDAU21', 0, NULL, '2025-11-06 18:25:10', '2026-08-27 14:35:23'),
(208, 'Bank of Queensland', 'BOQ', 'oceania', 'Australia', 'BOQAAU4B', 0, NULL, '2025-11-06 18:25:10', '2026-08-27 14:35:23'),
(209, 'AMP Bank', 'AMP', 'oceania', 'Australia', 'AMPBAU2S', 0, NULL, '2025-11-06 18:25:10', '2026-08-27 14:35:23'),
(210, 'ING Bank Australia', 'ING', 'oceania', 'Australia', 'INGBAU2S', 0, NULL, '2025-11-06 18:25:10', '2026-08-27 14:35:23'),
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
(256, 'MBBank (Military Commercial Joint Stock Bank', NULL, 'asia', 'Vietnam', '', 0, 60, '2026-02-24 05:42:56', '2026-08-27 14:35:23'),
(257, 'Techcombank (Vietnam Technological and Commercial Joint Stock Bank', NULL, 'asia', 'Vietnam', '', 0, 60, '2026-02-24 05:43:55', '2026-08-27 14:35:23'),
(258, 'HDBank (Ho Chi Minh City Development Joint Stock Commercial Bank', NULL, 'asia', 'Vietnam', '', 0, 60, '2026-02-24 05:45:25', '2026-08-27 14:35:23'),
(259, 'ACB (Asia Commercial Joint Stock Bank)', NULL, 'asia', 'Vietnam', '', 0, 60, '2026-02-24 05:46:29', '2026-08-27 14:35:23'),
(260, 'Techcombank (Vietnam Technological and Commercial Joint Stock Bank )', NULL, 'asia', 'Vietnam', '', 0, 60, '2026-02-24 05:47:24', '2026-08-27 14:35:23'),
(261, 'Sacombank (Saigon Thuong Tin Commercial Joint Stock Bank )', NULL, 'asia', 'Vietnam', '', 0, 60, '2026-02-24 05:48:15', '2026-08-27 14:35:23'),
(262, 'MSB (Vietnam Maritime Commercial Joint Stock Bank )', NULL, 'asia', 'Vietnam', '', 0, 60, '2026-02-24 05:49:13', '2026-08-27 14:35:23'),
(263, 'LPBank (Loc Phat Vietnam Commercial Joint Stock Bank )', NULL, 'asia', 'Vietnam', '', 0, 60, '2026-02-24 05:53:31', '2026-08-27 14:35:23'),
(264, 'VIB (Vietnam International Commercial Joint Stock Bank )', NULL, 'asia', 'Vietnam', '', 0, 60, '2026-02-24 05:54:26', '2026-08-27 14:35:23'),
(265, 'HCM City Development Bank (HDBank)', NULL, 'asia', 'Vietnam', '', 0, 60, '2026-02-24 05:56:24', '2026-08-27 14:35:23'),
(266, 'Bank Muscat (SAOG)', NULL, 'middle-east', 'Oman', '', 1, 60, '2026-02-24 13:45:26', '2026-02-24 13:45:26'),
(267, 'National Bank of Oman (NBO)', NULL, 'middle-east', 'Oman', '', 1, 60, '2026-02-24 13:46:13', '2026-02-24 13:46:13'),
(268, 'Bank Dhofar (S.A.O.G.)', NULL, 'middle-east', 'Oman', '', 1, 60, '2026-02-24 13:46:37', '2026-02-24 13:46:37'),
(269, 'Oman Arab Bank (OAB)', NULL, 'middle-east', 'Oman', '', 0, 60, '2026-02-24 13:46:59', '2026-08-27 14:35:23'),
(270, 'Sohar International (formerly Bank Sohar):', NULL, 'middle-east', 'Oman', '', 0, 60, '2026-02-24 13:47:26', '2026-08-27 14:35:23'),
(271, 'Ahli Bank', NULL, 'middle-east', 'Oman', '', 0, 60, '2026-02-24 13:47:57', '2026-08-27 14:35:23'),
(272, 'Oman Development Bank / Oman Housing Bank', NULL, 'middle-east', 'Oman', '', 0, 60, '2026-02-24 13:48:31', '2026-08-27 14:35:23'),
(273, 'Bank Nizwa', NULL, 'middle-east', 'Oman', '', 0, 60, '2026-02-24 13:49:05', '2026-08-27 14:35:23'),
(274, 'Standard Chartered Bank', NULL, 'middle-east', 'Oman', '', 0, 60, '2026-02-24 13:49:40', '2026-08-27 14:35:23'),
(275, 'Truist Bank', 'USBNK', 'north-america', 'United States', 'UNITUSXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(276, 'Charles Schwab Bank', 'USBNK', 'north-america', 'United States', 'UNITUSXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(277, 'Ally Bank', 'USBNK', 'north-america', 'United States', 'UNITUSXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(278, 'Fifth Third Bank', 'USBNK', 'north-america', 'United States', 'UNITUSXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(279, 'KeyBank', 'USBNK', 'north-america', 'United States', 'UNITUSXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(280, 'Guatemala National Bank', 'GTBNK', 'north-america', 'Guatemala', 'GUATGTXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(281, 'Guatemala Commercial Bank', 'GTBNK', 'north-america', 'Guatemala', 'GUATGTXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(282, 'Guatemala Development Bank', 'GTBNK', 'north-america', 'Guatemala', 'GUATGTXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(283, 'First Bank of Guatemala', 'GTBNK', 'north-america', 'Guatemala', 'GUATGTXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(284, 'Guatemala People\'s Bank', 'GTBNK', 'north-america', 'Guatemala', 'GUATGTXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(285, 'Belize National Bank', 'BZBNK', 'north-america', 'Belize', 'BELIBZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(286, 'Belize Commercial Bank', 'BZBNK', 'north-america', 'Belize', 'BELIBZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(287, 'Belize Development Bank', 'BZBNK', 'north-america', 'Belize', 'BELIBZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(288, 'First Bank of Belize', 'BZBNK', 'north-america', 'Belize', 'BELIBZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(289, 'Belize People\'s Bank', 'BZBNK', 'north-america', 'Belize', 'BELIBZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(290, 'Honduras National Bank', 'HNBNK', 'north-america', 'Honduras', 'HONDHNXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(291, 'Honduras Commercial Bank', 'HNBNK', 'north-america', 'Honduras', 'HONDHNXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(292, 'Honduras Development Bank', 'HNBNK', 'north-america', 'Honduras', 'HONDHNXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(293, 'First Bank of Honduras', 'HNBNK', 'north-america', 'Honduras', 'HONDHNXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(294, 'Honduras People\'s Bank', 'HNBNK', 'north-america', 'Honduras', 'HONDHNXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(295, 'El Salvador National Bank', 'SVBNK', 'north-america', 'El Salvador', 'ELSASVXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(296, 'El Salvador Commercial Bank', 'SVBNK', 'north-america', 'El Salvador', 'ELSASVXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(297, 'El Salvador Development Bank', 'SVBNK', 'north-america', 'El Salvador', 'ELSASVXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(298, 'First Bank of El Salvador', 'SVBNK', 'north-america', 'El Salvador', 'ELSASVXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(299, 'El Salvador People\'s Bank', 'SVBNK', 'north-america', 'El Salvador', 'ELSASVXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(300, 'Nicaragua National Bank', 'NIBNK', 'north-america', 'Nicaragua', 'NICANIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(301, 'Nicaragua Commercial Bank', 'NIBNK', 'north-america', 'Nicaragua', 'NICANIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(302, 'Nicaragua Development Bank', 'NIBNK', 'north-america', 'Nicaragua', 'NICANIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(303, 'First Bank of Nicaragua', 'NIBNK', 'north-america', 'Nicaragua', 'NICANIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(304, 'Nicaragua People\'s Bank', 'NIBNK', 'north-america', 'Nicaragua', 'NICANIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(305, 'Costa Rica National Bank', 'CRBNK', 'north-america', 'Costa Rica', 'COSTCRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(306, 'Costa Rica Commercial Bank', 'CRBNK', 'north-america', 'Costa Rica', 'COSTCRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(307, 'Costa Rica Development Bank', 'CRBNK', 'north-america', 'Costa Rica', 'COSTCRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(308, 'First Bank of Costa Rica', 'CRBNK', 'north-america', 'Costa Rica', 'COSTCRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(309, 'Costa Rica People\'s Bank', 'CRBNK', 'north-america', 'Costa Rica', 'COSTCRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(310, 'Panama National Bank', 'PABNK', 'north-america', 'Panama', 'PANAPAXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(311, 'Panama Commercial Bank', 'PABNK', 'north-america', 'Panama', 'PANAPAXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(312, 'Panama Development Bank', 'PABNK', 'north-america', 'Panama', 'PANAPAXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(313, 'First Bank of Panama', 'PABNK', 'north-america', 'Panama', 'PANAPAXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(314, 'Panama People\'s Bank', 'PABNK', 'north-america', 'Panama', 'PANAPAXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(315, 'Cuba National Bank', 'CUBNK', 'north-america', 'Cuba', 'CUBACUXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(316, 'Cuba Commercial Bank', 'CUBNK', 'north-america', 'Cuba', 'CUBACUXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(317, 'Cuba Development Bank', 'CUBNK', 'north-america', 'Cuba', 'CUBACUXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(318, 'First Bank of Cuba', 'CUBNK', 'north-america', 'Cuba', 'CUBACUXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(319, 'Cuba People\'s Bank', 'CUBNK', 'north-america', 'Cuba', 'CUBACUXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(320, 'Jamaica National Bank', 'JMBNK', 'north-america', 'Jamaica', 'JAMAJMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(321, 'Jamaica Commercial Bank', 'JMBNK', 'north-america', 'Jamaica', 'JAMAJMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(322, 'Jamaica Development Bank', 'JMBNK', 'north-america', 'Jamaica', 'JAMAJMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(323, 'First Bank of Jamaica', 'JMBNK', 'north-america', 'Jamaica', 'JAMAJMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(324, 'Jamaica People\'s Bank', 'JMBNK', 'north-america', 'Jamaica', 'JAMAJMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(325, 'Haiti National Bank', 'HTBNK', 'north-america', 'Haiti', 'HAITHTXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(326, 'Haiti Commercial Bank', 'HTBNK', 'north-america', 'Haiti', 'HAITHTXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(327, 'Haiti Development Bank', 'HTBNK', 'north-america', 'Haiti', 'HAITHTXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(328, 'First Bank of Haiti', 'HTBNK', 'north-america', 'Haiti', 'HAITHTXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(329, 'Haiti People\'s Bank', 'HTBNK', 'north-america', 'Haiti', 'HAITHTXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(330, 'Dominican Republic National Bank', 'DOBNK', 'north-america', 'Dominican Republic', 'DOMIDOXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(331, 'Dominican Republic Commercial Bank', 'DOBNK', 'north-america', 'Dominican Republic', 'DOMIDOXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(332, 'Dominican Republic Development Bank', 'DOBNK', 'north-america', 'Dominican Republic', 'DOMIDOXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(333, 'First Bank of Dominican Republic', 'DOBNK', 'north-america', 'Dominican Republic', 'DOMIDOXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(334, 'Dominican Republic People\'s Bank', 'DOBNK', 'north-america', 'Dominican Republic', 'DOMIDOXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(335, 'Bahamas National Bank', 'BSBNK', 'north-america', 'Bahamas', 'BAHABSXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(336, 'Bahamas Commercial Bank', 'BSBNK', 'north-america', 'Bahamas', 'BAHABSXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(337, 'Bahamas Development Bank', 'BSBNK', 'north-america', 'Bahamas', 'BAHABSXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(338, 'First Bank of Bahamas', 'BSBNK', 'north-america', 'Bahamas', 'BAHABSXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(339, 'Bahamas People\'s Bank', 'BSBNK', 'north-america', 'Bahamas', 'BAHABSXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(340, 'Barbados National Bank', 'BBBNK', 'north-america', 'Barbados', 'BARBBBXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(341, 'Barbados Commercial Bank', 'BBBNK', 'north-america', 'Barbados', 'BARBBBXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(342, 'Barbados Development Bank', 'BBBNK', 'north-america', 'Barbados', 'BARBBBXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(343, 'First Bank of Barbados', 'BBBNK', 'north-america', 'Barbados', 'BARBBBXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(344, 'Barbados People\'s Bank', 'BBBNK', 'north-america', 'Barbados', 'BARBBBXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(345, 'Trinidad and Tobago National Bank', 'TTBNK', 'north-america', 'Trinidad and Tobago', 'TRINTTXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(346, 'Trinidad and Tobago Commercial Bank', 'TTBNK', 'north-america', 'Trinidad and Tobago', 'TRINTTXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(347, 'Trinidad and Tobago Development Bank', 'TTBNK', 'north-america', 'Trinidad and Tobago', 'TRINTTXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(348, 'First Bank of Trinidad and Tobago', 'TTBNK', 'north-america', 'Trinidad and Tobago', 'TRINTTXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(349, 'Trinidad and Tobago People\'s Bank', 'TTBNK', 'north-america', 'Trinidad and Tobago', 'TRINTTXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(350, 'Antigua and Barbuda National Bank', 'AGBNK', 'north-america', 'Antigua and Barbuda', 'ANTIAGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(351, 'Antigua and Barbuda Commercial Bank', 'AGBNK', 'north-america', 'Antigua and Barbuda', 'ANTIAGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(352, 'Antigua and Barbuda Development Bank', 'AGBNK', 'north-america', 'Antigua and Barbuda', 'ANTIAGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(353, 'First Bank of Antigua and Barbuda', 'AGBNK', 'north-america', 'Antigua and Barbuda', 'ANTIAGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(354, 'Antigua and Barbuda People\'s Bank', 'AGBNK', 'north-america', 'Antigua and Barbuda', 'ANTIAGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(355, 'Saint Lucia National Bank', 'LCBNK', 'north-america', 'Saint Lucia', 'SAINLCXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(356, 'Saint Lucia Commercial Bank', 'LCBNK', 'north-america', 'Saint Lucia', 'SAINLCXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(357, 'Saint Lucia Development Bank', 'LCBNK', 'north-america', 'Saint Lucia', 'SAINLCXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(358, 'First Bank of Saint Lucia', 'LCBNK', 'north-america', 'Saint Lucia', 'SAINLCXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(359, 'Saint Lucia People\'s Bank', 'LCBNK', 'north-america', 'Saint Lucia', 'SAINLCXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(360, 'Saint Vincent and the Grenadines National Bank', 'VCBNK', 'north-america', 'Saint Vincent and the Grenadines', 'SAINVCXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(361, 'Saint Vincent and the Grenadines Commercial Bank', 'VCBNK', 'north-america', 'Saint Vincent and the Grenadines', 'SAINVCXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(362, 'Saint Vincent and the Grenadines Development Bank', 'VCBNK', 'north-america', 'Saint Vincent and the Grenadines', 'SAINVCXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(363, 'First Bank of Saint Vincent and the Grenadines', 'VCBNK', 'north-america', 'Saint Vincent and the Grenadines', 'SAINVCXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(364, 'Saint Vincent and the Grenadines People\'s Bank', 'VCBNK', 'north-america', 'Saint Vincent and the Grenadines', 'SAINVCXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(365, 'Grenada National Bank', 'GDBNK', 'north-america', 'Grenada', 'GRENGDXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(366, 'Grenada Commercial Bank', 'GDBNK', 'north-america', 'Grenada', 'GRENGDXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(367, 'Grenada Development Bank', 'GDBNK', 'north-america', 'Grenada', 'GRENGDXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(368, 'First Bank of Grenada', 'GDBNK', 'north-america', 'Grenada', 'GRENGDXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(369, 'Grenada People\'s Bank', 'GDBNK', 'north-america', 'Grenada', 'GRENGDXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(370, 'Dominica National Bank', 'DMBNK', 'north-america', 'Dominica', 'DOMIDMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(371, 'Dominica Commercial Bank', 'DMBNK', 'north-america', 'Dominica', 'DOMIDMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(372, 'Dominica Development Bank', 'DMBNK', 'north-america', 'Dominica', 'DOMIDMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(373, 'First Bank of Dominica', 'DMBNK', 'north-america', 'Dominica', 'DOMIDMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(374, 'Dominica People\'s Bank', 'DMBNK', 'north-america', 'Dominica', 'DOMIDMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(375, 'Saint Kitts and Nevis National Bank', 'KNBNK', 'north-america', 'Saint Kitts and Nevis', 'SAINKNXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(376, 'Saint Kitts and Nevis Commercial Bank', 'KNBNK', 'north-america', 'Saint Kitts and Nevis', 'SAINKNXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(377, 'Saint Kitts and Nevis Development Bank', 'KNBNK', 'north-america', 'Saint Kitts and Nevis', 'SAINKNXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(378, 'First Bank of Saint Kitts and Nevis', 'KNBNK', 'north-america', 'Saint Kitts and Nevis', 'SAINKNXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(379, 'Saint Kitts and Nevis People\'s Bank', 'KNBNK', 'north-america', 'Saint Kitts and Nevis', 'SAINKNXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(380, 'Bermuda National Bank', 'BMBNK', 'north-america', 'Bermuda', 'BERMBMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(381, 'Bermuda Commercial Bank', 'BMBNK', 'north-america', 'Bermuda', 'BERMBMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(382, 'Bermuda Development Bank', 'BMBNK', 'north-america', 'Bermuda', 'BERMBMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(383, 'First Bank of Bermuda', 'BMBNK', 'north-america', 'Bermuda', 'BERMBMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(384, 'Bermuda People\'s Bank', 'BMBNK', 'north-america', 'Bermuda', 'BERMBMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(385, 'Greenland National Bank', 'GLBNK', 'north-america', 'Greenland', 'GREEGLXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(386, 'Greenland Commercial Bank', 'GLBNK', 'north-america', 'Greenland', 'GREEGLXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(387, 'Greenland Development Bank', 'GLBNK', 'north-america', 'Greenland', 'GREEGLXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(388, 'First Bank of Greenland', 'GLBNK', 'north-america', 'Greenland', 'GREEGLXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(389, 'Greenland People\'s Bank', 'GLBNK', 'north-america', 'Greenland', 'GREEGLXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(390, 'Chile National Bank', 'CLBNK', 'south-america', 'Chile', 'CHILCLXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(391, 'Chile Commercial Bank', 'CLBNK', 'south-america', 'Chile', 'CHILCLXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(392, 'Chile Development Bank', 'CLBNK', 'south-america', 'Chile', 'CHILCLXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(393, 'Peru National Bank', 'PEBNK', 'south-america', 'Peru', 'PERUPEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(394, 'Peru Commercial Bank', 'PEBNK', 'south-america', 'Peru', 'PERUPEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(395, 'Peru Development Bank', 'PEBNK', 'south-america', 'Peru', 'PERUPEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(396, 'Venezuela National Bank', 'VEBNK', 'south-america', 'Venezuela', 'VENEVEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(397, 'Venezuela Commercial Bank', 'VEBNK', 'south-america', 'Venezuela', 'VENEVEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(398, 'Venezuela Development Bank', 'VEBNK', 'south-america', 'Venezuela', 'VENEVEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(399, 'First Bank of Venezuela', 'VEBNK', 'south-america', 'Venezuela', 'VENEVEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(400, 'Ecuador National Bank', 'ECBNK', 'south-america', 'Ecuador', 'ECUAECXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(401, 'Ecuador Commercial Bank', 'ECBNK', 'south-america', 'Ecuador', 'ECUAECXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(402, 'Ecuador Development Bank', 'ECBNK', 'south-america', 'Ecuador', 'ECUAECXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(403, 'First Bank of Ecuador', 'ECBNK', 'south-america', 'Ecuador', 'ECUAECXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(404, 'Ecuador People\'s Bank', 'ECBNK', 'south-america', 'Ecuador', 'ECUAECXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(405, 'Bolivia National Bank', 'BOBNK', 'south-america', 'Bolivia', 'BOLIBOXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(406, 'Bolivia Commercial Bank', 'BOBNK', 'south-america', 'Bolivia', 'BOLIBOXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(407, 'Bolivia Development Bank', 'BOBNK', 'south-america', 'Bolivia', 'BOLIBOXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(408, 'First Bank of Bolivia', 'BOBNK', 'south-america', 'Bolivia', 'BOLIBOXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(409, 'Bolivia People\'s Bank', 'BOBNK', 'south-america', 'Bolivia', 'BOLIBOXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(410, 'Paraguay National Bank', 'PYBNK', 'south-america', 'Paraguay', 'PARAPYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(411, 'Paraguay Commercial Bank', 'PYBNK', 'south-america', 'Paraguay', 'PARAPYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(412, 'Paraguay Development Bank', 'PYBNK', 'south-america', 'Paraguay', 'PARAPYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(413, 'First Bank of Paraguay', 'PYBNK', 'south-america', 'Paraguay', 'PARAPYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(414, 'Paraguay People\'s Bank', 'PYBNK', 'south-america', 'Paraguay', 'PARAPYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(415, 'Uruguay National Bank', 'UYBNK', 'south-america', 'Uruguay', 'URUGUYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(416, 'Uruguay Commercial Bank', 'UYBNK', 'south-america', 'Uruguay', 'URUGUYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(417, 'Uruguay Development Bank', 'UYBNK', 'south-america', 'Uruguay', 'URUGUYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(418, 'First Bank of Uruguay', 'UYBNK', 'south-america', 'Uruguay', 'URUGUYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(419, 'Uruguay People\'s Bank', 'UYBNK', 'south-america', 'Uruguay', 'URUGUYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(420, 'Guyana National Bank', 'GYBNK', 'south-america', 'Guyana', 'GUYAGYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(421, 'Guyana Commercial Bank', 'GYBNK', 'south-america', 'Guyana', 'GUYAGYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(422, 'Guyana Development Bank', 'GYBNK', 'south-america', 'Guyana', 'GUYAGYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(423, 'First Bank of Guyana', 'GYBNK', 'south-america', 'Guyana', 'GUYAGYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(424, 'Guyana People\'s Bank', 'GYBNK', 'south-america', 'Guyana', 'GUYAGYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(425, 'Suriname National Bank', 'SRBNK', 'south-america', 'Suriname', 'SURISRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(426, 'Suriname Commercial Bank', 'SRBNK', 'south-america', 'Suriname', 'SURISRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(427, 'Suriname Development Bank', 'SRBNK', 'south-america', 'Suriname', 'SURISRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(428, 'First Bank of Suriname', 'SRBNK', 'south-america', 'Suriname', 'SURISRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(429, 'Suriname People\'s Bank', 'SRBNK', 'south-america', 'Suriname', 'SURISRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(430, 'Falkland Islands National Bank', 'FKBNK', 'south-america', 'Falkland Islands', 'FALKFKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(431, 'Falkland Islands Commercial Bank', 'FKBNK', 'south-america', 'Falkland Islands', 'FALKFKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(432, 'Falkland Islands Development Bank', 'FKBNK', 'south-america', 'Falkland Islands', 'FALKFKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(433, 'First Bank of Falkland Islands', 'FKBNK', 'south-america', 'Falkland Islands', 'FALKFKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(434, 'Falkland Islands People\'s Bank', 'FKBNK', 'south-america', 'Falkland Islands', 'FALKFKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(435, 'French Guiana National Bank', 'GFBNK', 'south-america', 'French Guiana', 'FRENGFXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(436, 'French Guiana Commercial Bank', 'GFBNK', 'south-america', 'French Guiana', 'FRENGFXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(437, 'French Guiana Development Bank', 'GFBNK', 'south-america', 'French Guiana', 'FRENGFXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(438, 'First Bank of French Guiana', 'GFBNK', 'south-america', 'French Guiana', 'FRENGFXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(439, 'French Guiana People\'s Bank', 'GFBNK', 'south-america', 'French Guiana', 'FRENGFXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(440, 'Aruba National Bank', 'AWBNK', 'south-america', 'Aruba', 'ARUBAWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(441, 'Aruba Commercial Bank', 'AWBNK', 'south-america', 'Aruba', 'ARUBAWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(442, 'Aruba Development Bank', 'AWBNK', 'south-america', 'Aruba', 'ARUBAWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(443, 'First Bank of Aruba', 'AWBNK', 'south-america', 'Aruba', 'ARUBAWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(444, 'Aruba People\'s Bank', 'AWBNK', 'south-america', 'Aruba', 'ARUBAWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(445, 'Curaçao National Bank', 'CWBNK', 'south-america', 'Curaçao', 'CURACWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(446, 'Curaçao Commercial Bank', 'CWBNK', 'south-america', 'Curaçao', 'CURACWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(447, 'Curaçao Development Bank', 'CWBNK', 'south-america', 'Curaçao', 'CURACWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(448, 'First Bank of Curaçao', 'CWBNK', 'south-america', 'Curaçao', 'CURACWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(449, 'Curaçao People\'s Bank', 'CWBNK', 'south-america', 'Curaçao', 'CURACWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(450, 'Caribbean Netherlands National Bank', 'BQBNK', 'south-america', 'Caribbean Netherlands', 'CARIBQXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(451, 'Caribbean Netherlands Commercial Bank', 'BQBNK', 'south-america', 'Caribbean Netherlands', 'CARIBQXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(452, 'Caribbean Netherlands Development Bank', 'BQBNK', 'south-america', 'Caribbean Netherlands', 'CARIBQXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(453, 'First Bank of Caribbean Netherlands', 'BQBNK', 'south-america', 'Caribbean Netherlands', 'CARIBQXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(454, 'Caribbean Netherlands People\'s Bank', 'BQBNK', 'south-america', 'Caribbean Netherlands', 'CARIBQXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(455, 'Sint Maarten National Bank', 'SXBNK', 'south-america', 'Sint Maarten', 'SINTSXXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(456, 'Sint Maarten Commercial Bank', 'SXBNK', 'south-america', 'Sint Maarten', 'SINTSXXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(457, 'Sint Maarten Development Bank', 'SXBNK', 'south-america', 'Sint Maarten', 'SINTSXXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(458, 'First Bank of Sint Maarten', 'SXBNK', 'south-america', 'Sint Maarten', 'SINTSXXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(459, 'Sint Maarten People\'s Bank', 'SXBNK', 'south-america', 'Sint Maarten', 'SINTSXXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(460, 'Puerto Rico National Bank', 'PRBNK', 'south-america', 'Puerto Rico', 'PUERPRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(461, 'Puerto Rico Commercial Bank', 'PRBNK', 'south-america', 'Puerto Rico', 'PUERPRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(462, 'Puerto Rico Development Bank', 'PRBNK', 'south-america', 'Puerto Rico', 'PUERPRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(463, 'First Bank of Puerto Rico', 'PRBNK', 'south-america', 'Puerto Rico', 'PUERPRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(464, 'Puerto Rico People\'s Bank', 'PRBNK', 'south-america', 'Puerto Rico', 'PUERPRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(465, 'U.S. Virgin Islands National Bank', 'VIBNK', 'south-america', 'U.S. Virgin Islands', 'USVIVIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(466, 'U.S. Virgin Islands Commercial Bank', 'VIBNK', 'south-america', 'U.S. Virgin Islands', 'USVIVIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(467, 'U.S. Virgin Islands Development Bank', 'VIBNK', 'south-america', 'U.S. Virgin Islands', 'USVIVIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(468, 'First Bank of U.S. Virgin Islands', 'VIBNK', 'south-america', 'U.S. Virgin Islands', 'USVIVIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(469, 'U.S. Virgin Islands People\'s Bank', 'VIBNK', 'south-america', 'U.S. Virgin Islands', 'USVIVIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23');
INSERT INTO `banks` (`id`, `name`, `code`, `region`, `country`, `swift_code`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(470, 'Cayman Islands National Bank', 'KYBNK', 'south-america', 'Cayman Islands', 'CAYMKYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(471, 'Cayman Islands Commercial Bank', 'KYBNK', 'south-america', 'Cayman Islands', 'CAYMKYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(472, 'Cayman Islands Development Bank', 'KYBNK', 'south-america', 'Cayman Islands', 'CAYMKYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(473, 'First Bank of Cayman Islands', 'KYBNK', 'south-america', 'Cayman Islands', 'CAYMKYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(474, 'Cayman Islands People\'s Bank', 'KYBNK', 'south-america', 'Cayman Islands', 'CAYMKYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(475, 'Turks and Caicos Islands National Bank', 'TCBNK', 'south-america', 'Turks and Caicos Islands', 'TURKTCXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(476, 'Turks and Caicos Islands Commercial Bank', 'TCBNK', 'south-america', 'Turks and Caicos Islands', 'TURKTCXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(477, 'Turks and Caicos Islands Development Bank', 'TCBNK', 'south-america', 'Turks and Caicos Islands', 'TURKTCXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(478, 'First Bank of Turks and Caicos Islands', 'TCBNK', 'south-america', 'Turks and Caicos Islands', 'TURKTCXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(479, 'Turks and Caicos Islands People\'s Bank', 'TCBNK', 'south-america', 'Turks and Caicos Islands', 'TURKTCXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(480, 'Montserrat National Bank', 'MSBNK', 'south-america', 'Montserrat', 'MONTMSXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(481, 'Montserrat Commercial Bank', 'MSBNK', 'south-america', 'Montserrat', 'MONTMSXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(482, 'Montserrat Development Bank', 'MSBNK', 'south-america', 'Montserrat', 'MONTMSXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(483, 'First Bank of Montserrat', 'MSBNK', 'south-america', 'Montserrat', 'MONTMSXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(484, 'Montserrat People\'s Bank', 'MSBNK', 'south-america', 'Montserrat', 'MONTMSXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(485, 'Anguilla National Bank', 'AIBNK', 'south-america', 'Anguilla', 'ANGUAIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(486, 'Anguilla Commercial Bank', 'AIBNK', 'south-america', 'Anguilla', 'ANGUAIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(487, 'Anguilla Development Bank', 'AIBNK', 'south-america', 'Anguilla', 'ANGUAIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(488, 'First Bank of Anguilla', 'AIBNK', 'south-america', 'Anguilla', 'ANGUAIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(489, 'Anguilla People\'s Bank', 'AIBNK', 'south-america', 'Anguilla', 'ANGUAIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(490, 'British Virgin Islands National Bank', 'VGBNK', 'south-america', 'British Virgin Islands', 'BRITVGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(491, 'British Virgin Islands Commercial Bank', 'VGBNK', 'south-america', 'British Virgin Islands', 'BRITVGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(492, 'British Virgin Islands Development Bank', 'VGBNK', 'south-america', 'British Virgin Islands', 'BRITVGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(493, 'First Bank of British Virgin Islands', 'VGBNK', 'south-america', 'British Virgin Islands', 'BRITVGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(494, 'British Virgin Islands People\'s Bank', 'VGBNK', 'south-america', 'British Virgin Islands', 'BRITVGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(495, 'France National Bank', 'FRBNK', 'europe', 'France', 'FRANFRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(496, 'France Commercial Bank', 'FRBNK', 'europe', 'France', 'FRANFRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(497, 'Italy National Bank', 'ITBNK', 'europe', 'Italy', 'ITALITXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(498, 'Italy Commercial Bank', 'ITBNK', 'europe', 'Italy', 'ITALITXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(499, 'Italy Development Bank', 'ITBNK', 'europe', 'Italy', 'ITALITXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(500, 'Spain National Bank', 'ESBNK', 'europe', 'Spain', 'SPAIESXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(501, 'Spain Commercial Bank', 'ESBNK', 'europe', 'Spain', 'SPAIESXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(502, 'Netherlands National Bank', 'NLBNK', 'europe', 'Netherlands', 'NETHNLXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(503, 'Netherlands Commercial Bank', 'NLBNK', 'europe', 'Netherlands', 'NETHNLXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(504, 'Netherlands Development Bank', 'NLBNK', 'europe', 'Netherlands', 'NETHNLXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(505, 'Switzerland National Bank', 'CHBNK', 'europe', 'Switzerland', 'SWITCHXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(506, 'Switzerland Commercial Bank', 'CHBNK', 'europe', 'Switzerland', 'SWITCHXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(507, 'Switzerland Development Bank', 'CHBNK', 'europe', 'Switzerland', 'SWITCHXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(508, 'First Bank of Switzerland', 'CHBNK', 'europe', 'Switzerland', 'SWITCHXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(509, 'Switzerland People\'s Bank', 'CHBNK', 'europe', 'Switzerland', 'SWITCHXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(510, 'Belgium National Bank', 'BEBNK', 'europe', 'Belgium', 'BELGBEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(511, 'Belgium Commercial Bank', 'BEBNK', 'europe', 'Belgium', 'BELGBEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(512, 'Belgium Development Bank', 'BEBNK', 'europe', 'Belgium', 'BELGBEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(513, 'First Bank of Belgium', 'BEBNK', 'europe', 'Belgium', 'BELGBEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(514, 'Belgium People\'s Bank', 'BEBNK', 'europe', 'Belgium', 'BELGBEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(515, 'Austria National Bank', 'ATBNK', 'europe', 'Austria', 'AUSTATXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(516, 'Austria Commercial Bank', 'ATBNK', 'europe', 'Austria', 'AUSTATXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(517, 'Austria Development Bank', 'ATBNK', 'europe', 'Austria', 'AUSTATXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(518, 'First Bank of Austria', 'ATBNK', 'europe', 'Austria', 'AUSTATXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(519, 'Austria People\'s Bank', 'ATBNK', 'europe', 'Austria', 'AUSTATXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(520, 'Sweden National Bank', 'SEBNK', 'europe', 'Sweden', 'SWEDSEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(521, 'Sweden Commercial Bank', 'SEBNK', 'europe', 'Sweden', 'SWEDSEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(522, 'Sweden Development Bank', 'SEBNK', 'europe', 'Sweden', 'SWEDSEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(523, 'First Bank of Sweden', 'SEBNK', 'europe', 'Sweden', 'SWEDSEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(524, 'Sweden People\'s Bank', 'SEBNK', 'europe', 'Sweden', 'SWEDSEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(525, 'Norway National Bank', 'NOBNK', 'europe', 'Norway', 'NORWNOXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(526, 'Norway Commercial Bank', 'NOBNK', 'europe', 'Norway', 'NORWNOXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(527, 'Norway Development Bank', 'NOBNK', 'europe', 'Norway', 'NORWNOXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(528, 'First Bank of Norway', 'NOBNK', 'europe', 'Norway', 'NORWNOXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(529, 'Norway People\'s Bank', 'NOBNK', 'europe', 'Norway', 'NORWNOXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(530, 'Denmark National Bank', 'DKBNK', 'europe', 'Denmark', 'DENMDKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(531, 'Denmark Commercial Bank', 'DKBNK', 'europe', 'Denmark', 'DENMDKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(532, 'Denmark Development Bank', 'DKBNK', 'europe', 'Denmark', 'DENMDKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(533, 'First Bank of Denmark', 'DKBNK', 'europe', 'Denmark', 'DENMDKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(534, 'Denmark People\'s Bank', 'DKBNK', 'europe', 'Denmark', 'DENMDKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(535, 'Finland National Bank', 'FIBNK', 'europe', 'Finland', 'FINLFIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(536, 'Finland Commercial Bank', 'FIBNK', 'europe', 'Finland', 'FINLFIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(537, 'Finland Development Bank', 'FIBNK', 'europe', 'Finland', 'FINLFIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(538, 'First Bank of Finland', 'FIBNK', 'europe', 'Finland', 'FINLFIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(539, 'Finland People\'s Bank', 'FIBNK', 'europe', 'Finland', 'FINLFIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(540, 'Poland National Bank', 'PLBNK', 'europe', 'Poland', 'POLAPLXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(541, 'Poland Commercial Bank', 'PLBNK', 'europe', 'Poland', 'POLAPLXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(542, 'Poland Development Bank', 'PLBNK', 'europe', 'Poland', 'POLAPLXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(543, 'First Bank of Poland', 'PLBNK', 'europe', 'Poland', 'POLAPLXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(544, 'Poland People\'s Bank', 'PLBNK', 'europe', 'Poland', 'POLAPLXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(545, 'Ireland National Bank', 'IEBNK', 'europe', 'Ireland', 'IRELIEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(546, 'Ireland Commercial Bank', 'IEBNK', 'europe', 'Ireland', 'IRELIEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(547, 'Ireland Development Bank', 'IEBNK', 'europe', 'Ireland', 'IRELIEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(548, 'First Bank of Ireland', 'IEBNK', 'europe', 'Ireland', 'IRELIEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(549, 'Ireland People\'s Bank', 'IEBNK', 'europe', 'Ireland', 'IRELIEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(550, 'Portugal National Bank', 'PTBNK', 'europe', 'Portugal', 'PORTPTXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(551, 'Portugal Commercial Bank', 'PTBNK', 'europe', 'Portugal', 'PORTPTXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(552, 'Portugal Development Bank', 'PTBNK', 'europe', 'Portugal', 'PORTPTXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(553, 'First Bank of Portugal', 'PTBNK', 'europe', 'Portugal', 'PORTPTXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(554, 'Portugal People\'s Bank', 'PTBNK', 'europe', 'Portugal', 'PORTPTXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(555, 'Greece National Bank', 'GRBNK', 'europe', 'Greece', 'GREEGRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(556, 'Greece Commercial Bank', 'GRBNK', 'europe', 'Greece', 'GREEGRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(557, 'Greece Development Bank', 'GRBNK', 'europe', 'Greece', 'GREEGRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(558, 'First Bank of Greece', 'GRBNK', 'europe', 'Greece', 'GREEGRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(559, 'Greece People\'s Bank', 'GRBNK', 'europe', 'Greece', 'GREEGRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(560, 'Czechia National Bank', 'CZBNK', 'europe', 'Czechia', 'CZECCZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(561, 'Czechia Commercial Bank', 'CZBNK', 'europe', 'Czechia', 'CZECCZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(562, 'Czechia Development Bank', 'CZBNK', 'europe', 'Czechia', 'CZECCZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(563, 'First Bank of Czechia', 'CZBNK', 'europe', 'Czechia', 'CZECCZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(564, 'Czechia People\'s Bank', 'CZBNK', 'europe', 'Czechia', 'CZECCZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(565, 'Romania National Bank', 'ROBNK', 'europe', 'Romania', 'ROMAROXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(566, 'Romania Commercial Bank', 'ROBNK', 'europe', 'Romania', 'ROMAROXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(567, 'Romania Development Bank', 'ROBNK', 'europe', 'Romania', 'ROMAROXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(568, 'First Bank of Romania', 'ROBNK', 'europe', 'Romania', 'ROMAROXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(569, 'Romania People\'s Bank', 'ROBNK', 'europe', 'Romania', 'ROMAROXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(570, 'Hungary National Bank', 'HUBNK', 'europe', 'Hungary', 'HUNGHUXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(571, 'Hungary Commercial Bank', 'HUBNK', 'europe', 'Hungary', 'HUNGHUXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(572, 'Hungary Development Bank', 'HUBNK', 'europe', 'Hungary', 'HUNGHUXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(573, 'First Bank of Hungary', 'HUBNK', 'europe', 'Hungary', 'HUNGHUXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(574, 'Hungary People\'s Bank', 'HUBNK', 'europe', 'Hungary', 'HUNGHUXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(575, 'Bulgaria National Bank', 'BGBNK', 'europe', 'Bulgaria', 'BULGBGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(576, 'Bulgaria Commercial Bank', 'BGBNK', 'europe', 'Bulgaria', 'BULGBGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(577, 'Bulgaria Development Bank', 'BGBNK', 'europe', 'Bulgaria', 'BULGBGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(578, 'First Bank of Bulgaria', 'BGBNK', 'europe', 'Bulgaria', 'BULGBGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(579, 'Bulgaria People\'s Bank', 'BGBNK', 'europe', 'Bulgaria', 'BULGBGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(580, 'Croatia National Bank', 'HRBNK', 'europe', 'Croatia', 'CROAHRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(581, 'Croatia Commercial Bank', 'HRBNK', 'europe', 'Croatia', 'CROAHRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(582, 'Croatia Development Bank', 'HRBNK', 'europe', 'Croatia', 'CROAHRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(583, 'First Bank of Croatia', 'HRBNK', 'europe', 'Croatia', 'CROAHRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(584, 'Croatia People\'s Bank', 'HRBNK', 'europe', 'Croatia', 'CROAHRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(585, 'Slovakia National Bank', 'SKBNK', 'europe', 'Slovakia', 'SLOVSKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(586, 'Slovakia Commercial Bank', 'SKBNK', 'europe', 'Slovakia', 'SLOVSKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(587, 'Slovakia Development Bank', 'SKBNK', 'europe', 'Slovakia', 'SLOVSKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(588, 'First Bank of Slovakia', 'SKBNK', 'europe', 'Slovakia', 'SLOVSKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(589, 'Slovakia People\'s Bank', 'SKBNK', 'europe', 'Slovakia', 'SLOVSKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(590, 'Slovenia National Bank', 'SIBNK', 'europe', 'Slovenia', 'SLOVSIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(591, 'Slovenia Commercial Bank', 'SIBNK', 'europe', 'Slovenia', 'SLOVSIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(592, 'Slovenia Development Bank', 'SIBNK', 'europe', 'Slovenia', 'SLOVSIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(593, 'First Bank of Slovenia', 'SIBNK', 'europe', 'Slovenia', 'SLOVSIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(594, 'Slovenia People\'s Bank', 'SIBNK', 'europe', 'Slovenia', 'SLOVSIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(595, 'Lithuania National Bank', 'LTBNK', 'europe', 'Lithuania', 'LITHLTXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(596, 'Lithuania Commercial Bank', 'LTBNK', 'europe', 'Lithuania', 'LITHLTXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(597, 'Lithuania Development Bank', 'LTBNK', 'europe', 'Lithuania', 'LITHLTXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(598, 'First Bank of Lithuania', 'LTBNK', 'europe', 'Lithuania', 'LITHLTXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(599, 'Lithuania People\'s Bank', 'LTBNK', 'europe', 'Lithuania', 'LITHLTXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(600, 'Latvia National Bank', 'LVBNK', 'europe', 'Latvia', 'LATVLVXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(601, 'Latvia Commercial Bank', 'LVBNK', 'europe', 'Latvia', 'LATVLVXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(602, 'Latvia Development Bank', 'LVBNK', 'europe', 'Latvia', 'LATVLVXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(603, 'First Bank of Latvia', 'LVBNK', 'europe', 'Latvia', 'LATVLVXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(604, 'Latvia People\'s Bank', 'LVBNK', 'europe', 'Latvia', 'LATVLVXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(605, 'Estonia National Bank', 'EEBNK', 'europe', 'Estonia', 'ESTOEEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(606, 'Estonia Commercial Bank', 'EEBNK', 'europe', 'Estonia', 'ESTOEEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(607, 'Estonia Development Bank', 'EEBNK', 'europe', 'Estonia', 'ESTOEEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(608, 'First Bank of Estonia', 'EEBNK', 'europe', 'Estonia', 'ESTOEEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(609, 'Estonia People\'s Bank', 'EEBNK', 'europe', 'Estonia', 'ESTOEEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(610, 'Luxembourg National Bank', 'LUBNK', 'europe', 'Luxembourg', 'LUXELUXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(611, 'Luxembourg Commercial Bank', 'LUBNK', 'europe', 'Luxembourg', 'LUXELUXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(612, 'Luxembourg Development Bank', 'LUBNK', 'europe', 'Luxembourg', 'LUXELUXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(613, 'First Bank of Luxembourg', 'LUBNK', 'europe', 'Luxembourg', 'LUXELUXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(614, 'Luxembourg People\'s Bank', 'LUBNK', 'europe', 'Luxembourg', 'LUXELUXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(615, 'Malta National Bank', 'MTBNK', 'europe', 'Malta', 'MALTMTXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(616, 'Malta Commercial Bank', 'MTBNK', 'europe', 'Malta', 'MALTMTXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(617, 'Malta Development Bank', 'MTBNK', 'europe', 'Malta', 'MALTMTXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(618, 'First Bank of Malta', 'MTBNK', 'europe', 'Malta', 'MALTMTXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(619, 'Malta People\'s Bank', 'MTBNK', 'europe', 'Malta', 'MALTMTXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(620, 'Iceland National Bank', 'ISBNK', 'europe', 'Iceland', 'ICELISXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(621, 'Iceland Commercial Bank', 'ISBNK', 'europe', 'Iceland', 'ICELISXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(622, 'Iceland Development Bank', 'ISBNK', 'europe', 'Iceland', 'ICELISXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(623, 'First Bank of Iceland', 'ISBNK', 'europe', 'Iceland', 'ICELISXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(624, 'Iceland People\'s Bank', 'ISBNK', 'europe', 'Iceland', 'ICELISXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(625, 'Liechtenstein National Bank', 'LIBNK', 'europe', 'Liechtenstein', 'LIECLIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(626, 'Liechtenstein Commercial Bank', 'LIBNK', 'europe', 'Liechtenstein', 'LIECLIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(627, 'Liechtenstein Development Bank', 'LIBNK', 'europe', 'Liechtenstein', 'LIECLIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(628, 'First Bank of Liechtenstein', 'LIBNK', 'europe', 'Liechtenstein', 'LIECLIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(629, 'Liechtenstein People\'s Bank', 'LIBNK', 'europe', 'Liechtenstein', 'LIECLIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(630, 'Monaco National Bank', 'MCBNK', 'europe', 'Monaco', 'MONAMCXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(631, 'Monaco Commercial Bank', 'MCBNK', 'europe', 'Monaco', 'MONAMCXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(632, 'Monaco Development Bank', 'MCBNK', 'europe', 'Monaco', 'MONAMCXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(633, 'First Bank of Monaco', 'MCBNK', 'europe', 'Monaco', 'MONAMCXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(634, 'Monaco People\'s Bank', 'MCBNK', 'europe', 'Monaco', 'MONAMCXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(635, 'Andorra National Bank', 'ADBNK', 'europe', 'Andorra', 'ANDOADXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(636, 'Andorra Commercial Bank', 'ADBNK', 'europe', 'Andorra', 'ANDOADXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(637, 'Andorra Development Bank', 'ADBNK', 'europe', 'Andorra', 'ANDOADXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(638, 'First Bank of Andorra', 'ADBNK', 'europe', 'Andorra', 'ANDOADXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(639, 'Andorra People\'s Bank', 'ADBNK', 'europe', 'Andorra', 'ANDOADXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(640, 'San Marino National Bank', 'SMBNK', 'europe', 'San Marino', 'SANMSMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(641, 'San Marino Commercial Bank', 'SMBNK', 'europe', 'San Marino', 'SANMSMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(642, 'San Marino Development Bank', 'SMBNK', 'europe', 'San Marino', 'SANMSMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(643, 'First Bank of San Marino', 'SMBNK', 'europe', 'San Marino', 'SANMSMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(644, 'San Marino People\'s Bank', 'SMBNK', 'europe', 'San Marino', 'SANMSMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(645, 'Vatican City National Bank', 'VABNK', 'europe', 'Vatican City', 'VATIVAXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(646, 'Vatican City Commercial Bank', 'VABNK', 'europe', 'Vatican City', 'VATIVAXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(647, 'Vatican City Development Bank', 'VABNK', 'europe', 'Vatican City', 'VATIVAXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(648, 'First Bank of Vatican City', 'VABNK', 'europe', 'Vatican City', 'VATIVAXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(649, 'Vatican City People\'s Bank', 'VABNK', 'europe', 'Vatican City', 'VATIVAXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(650, 'Belarus National Bank', 'BYBNK', 'europe', 'Belarus', 'BELABYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(651, 'Belarus Commercial Bank', 'BYBNK', 'europe', 'Belarus', 'BELABYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(652, 'Belarus Development Bank', 'BYBNK', 'europe', 'Belarus', 'BELABYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(653, 'First Bank of Belarus', 'BYBNK', 'europe', 'Belarus', 'BELABYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(654, 'Belarus People\'s Bank', 'BYBNK', 'europe', 'Belarus', 'BELABYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(655, 'Ukraine National Bank', 'UABNK', 'europe', 'Ukraine', 'UKRAUAXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(656, 'Ukraine Commercial Bank', 'UABNK', 'europe', 'Ukraine', 'UKRAUAXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(657, 'Ukraine Development Bank', 'UABNK', 'europe', 'Ukraine', 'UKRAUAXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(658, 'First Bank of Ukraine', 'UABNK', 'europe', 'Ukraine', 'UKRAUAXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(659, 'Ukraine People\'s Bank', 'UABNK', 'europe', 'Ukraine', 'UKRAUAXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(660, 'Moldova National Bank', 'MDBNK', 'europe', 'Moldova', 'MOLDMDXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(661, 'Moldova Commercial Bank', 'MDBNK', 'europe', 'Moldova', 'MOLDMDXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(662, 'Moldova Development Bank', 'MDBNK', 'europe', 'Moldova', 'MOLDMDXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(663, 'First Bank of Moldova', 'MDBNK', 'europe', 'Moldova', 'MOLDMDXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(664, 'Moldova People\'s Bank', 'MDBNK', 'europe', 'Moldova', 'MOLDMDXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(665, 'Serbia National Bank', 'RSBNK', 'europe', 'Serbia', 'SERBRSXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(666, 'Serbia Commercial Bank', 'RSBNK', 'europe', 'Serbia', 'SERBRSXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(667, 'Serbia Development Bank', 'RSBNK', 'europe', 'Serbia', 'SERBRSXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(668, 'First Bank of Serbia', 'RSBNK', 'europe', 'Serbia', 'SERBRSXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(669, 'Serbia People\'s Bank', 'RSBNK', 'europe', 'Serbia', 'SERBRSXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(670, 'Montenegro National Bank', 'MEBNK', 'europe', 'Montenegro', 'MONTMEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(671, 'Montenegro Commercial Bank', 'MEBNK', 'europe', 'Montenegro', 'MONTMEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(672, 'Montenegro Development Bank', 'MEBNK', 'europe', 'Montenegro', 'MONTMEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(673, 'First Bank of Montenegro', 'MEBNK', 'europe', 'Montenegro', 'MONTMEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(674, 'Montenegro People\'s Bank', 'MEBNK', 'europe', 'Montenegro', 'MONTMEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(675, 'Albania National Bank', 'ALBNK', 'europe', 'Albania', 'ALBAALXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(676, 'Albania Commercial Bank', 'ALBNK', 'europe', 'Albania', 'ALBAALXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(677, 'Albania Development Bank', 'ALBNK', 'europe', 'Albania', 'ALBAALXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(678, 'First Bank of Albania', 'ALBNK', 'europe', 'Albania', 'ALBAALXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(679, 'Albania People\'s Bank', 'ALBNK', 'europe', 'Albania', 'ALBAALXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(680, 'North Macedonia National Bank', 'MKBNK', 'europe', 'North Macedonia', 'NORTMKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(681, 'North Macedonia Commercial Bank', 'MKBNK', 'europe', 'North Macedonia', 'NORTMKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(682, 'North Macedonia Development Bank', 'MKBNK', 'europe', 'North Macedonia', 'NORTMKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(683, 'First Bank of North Macedonia', 'MKBNK', 'europe', 'North Macedonia', 'NORTMKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(684, 'North Macedonia People\'s Bank', 'MKBNK', 'europe', 'North Macedonia', 'NORTMKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(685, 'Bosnia and Herzegovina National Bank', 'BABNK', 'europe', 'Bosnia and Herzegovina', 'BOSNBAXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(686, 'Bosnia and Herzegovina Commercial Bank', 'BABNK', 'europe', 'Bosnia and Herzegovina', 'BOSNBAXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(687, 'Bosnia and Herzegovina Development Bank', 'BABNK', 'europe', 'Bosnia and Herzegovina', 'BOSNBAXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(688, 'First Bank of Bosnia and Herzegovina', 'BABNK', 'europe', 'Bosnia and Herzegovina', 'BOSNBAXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(689, 'Bosnia and Herzegovina People\'s Bank', 'BABNK', 'europe', 'Bosnia and Herzegovina', 'BOSNBAXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(690, 'Kosovo National Bank', 'XKBNK', 'europe', 'Kosovo', 'KOSOXKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(691, 'Kosovo Commercial Bank', 'XKBNK', 'europe', 'Kosovo', 'KOSOXKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(692, 'Kosovo Development Bank', 'XKBNK', 'europe', 'Kosovo', 'KOSOXKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(693, 'First Bank of Kosovo', 'XKBNK', 'europe', 'Kosovo', 'KOSOXKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(694, 'Kosovo People\'s Bank', 'XKBNK', 'europe', 'Kosovo', 'KOSOXKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(695, 'Russia National Bank', 'RUBNK', 'europe', 'Russia', 'RUSSRUXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(696, 'Russia Commercial Bank', 'RUBNK', 'europe', 'Russia', 'RUSSRUXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(697, 'Russia Development Bank', 'RUBNK', 'europe', 'Russia', 'RUSSRUXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(698, 'First Bank of Russia', 'RUBNK', 'europe', 'Russia', 'RUSSRUXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(699, 'Russia People\'s Bank', 'RUBNK', 'europe', 'Russia', 'RUSSRUXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(700, 'China National Bank', 'CNBNK', 'asia', 'China', 'CHINCNXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(701, 'Japan National Bank', 'JPBNK', 'asia', 'Japan', 'JAPAJPXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(702, 'Japan Commercial Bank', 'JPBNK', 'asia', 'Japan', 'JAPAJPXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(703, 'South Korea National Bank', 'KRBNK', 'asia', 'South Korea', 'SOUTKRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(704, 'South Korea Commercial Bank', 'KRBNK', 'asia', 'South Korea', 'SOUTKRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(705, 'Indonesia National Bank', 'IDBNK', 'asia', 'Indonesia', 'INDOIDXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(706, 'Indonesia Commercial Bank', 'IDBNK', 'asia', 'Indonesia', 'INDOIDXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(707, 'Indonesia Development Bank', 'IDBNK', 'asia', 'Indonesia', 'INDOIDXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(708, 'First Bank of Indonesia', 'IDBNK', 'asia', 'Indonesia', 'INDOIDXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(709, 'Indonesia People\'s Bank', 'IDBNK', 'asia', 'Indonesia', 'INDOIDXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(710, 'Thailand National Bank', 'THBNK', 'asia', 'Thailand', 'THAITHXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(711, 'Thailand Commercial Bank', 'THBNK', 'asia', 'Thailand', 'THAITHXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(712, 'Thailand Development Bank', 'THBNK', 'asia', 'Thailand', 'THAITHXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(713, 'First Bank of Thailand', 'THBNK', 'asia', 'Thailand', 'THAITHXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(714, 'Thailand People\'s Bank', 'THBNK', 'asia', 'Thailand', 'THAITHXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(715, 'Malaysia National Bank', 'MYBNK', 'asia', 'Malaysia', 'MALAMYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(716, 'Malaysia Commercial Bank', 'MYBNK', 'asia', 'Malaysia', 'MALAMYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(717, 'Malaysia Development Bank', 'MYBNK', 'asia', 'Malaysia', 'MALAMYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(718, 'First Bank of Malaysia', 'MYBNK', 'asia', 'Malaysia', 'MALAMYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(719, 'Malaysia People\'s Bank', 'MYBNK', 'asia', 'Malaysia', 'MALAMYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(720, 'Philippines National Bank', 'PHBNK', 'asia', 'Philippines', 'PHILPHXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(721, 'Philippines Commercial Bank', 'PHBNK', 'asia', 'Philippines', 'PHILPHXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(722, 'Philippines Development Bank', 'PHBNK', 'asia', 'Philippines', 'PHILPHXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(723, 'First Bank of Philippines', 'PHBNK', 'asia', 'Philippines', 'PHILPHXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(724, 'Philippines People\'s Bank', 'PHBNK', 'asia', 'Philippines', 'PHILPHXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(725, 'Pakistan National Bank', 'PKBNK', 'asia', 'Pakistan', 'PAKIPKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(726, 'Pakistan Commercial Bank', 'PKBNK', 'asia', 'Pakistan', 'PAKIPKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(727, 'Pakistan Development Bank', 'PKBNK', 'asia', 'Pakistan', 'PAKIPKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(728, 'First Bank of Pakistan', 'PKBNK', 'asia', 'Pakistan', 'PAKIPKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(729, 'Pakistan People\'s Bank', 'PKBNK', 'asia', 'Pakistan', 'PAKIPKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(730, 'Bangladesh National Bank', 'BDBNK', 'asia', 'Bangladesh', 'BANGBDXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(731, 'Bangladesh Commercial Bank', 'BDBNK', 'asia', 'Bangladesh', 'BANGBDXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(732, 'Bangladesh Development Bank', 'BDBNK', 'asia', 'Bangladesh', 'BANGBDXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(733, 'First Bank of Bangladesh', 'BDBNK', 'asia', 'Bangladesh', 'BANGBDXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(734, 'Bangladesh People\'s Bank', 'BDBNK', 'asia', 'Bangladesh', 'BANGBDXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(735, 'Taiwan National Bank', 'TWBNK', 'asia', 'Taiwan', 'TAIWTWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(736, 'Taiwan Commercial Bank', 'TWBNK', 'asia', 'Taiwan', 'TAIWTWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(737, 'Taiwan Development Bank', 'TWBNK', 'asia', 'Taiwan', 'TAIWTWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(738, 'First Bank of Taiwan', 'TWBNK', 'asia', 'Taiwan', 'TAIWTWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(739, 'Taiwan People\'s Bank', 'TWBNK', 'asia', 'Taiwan', 'TAIWTWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(740, 'Singapore National Bank', 'SGBNK', 'asia', 'Singapore', 'SINGSGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(741, 'Singapore Commercial Bank', 'SGBNK', 'asia', 'Singapore', 'SINGSGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(742, 'Hong Kong National Bank', 'HKBNK', 'asia', 'Hong Kong', 'HONGHKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(743, 'Hong Kong Commercial Bank', 'HKBNK', 'asia', 'Hong Kong', 'HONGHKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(744, 'Hong Kong Development Bank', 'HKBNK', 'asia', 'Hong Kong', 'HONGHKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(745, 'Sri Lanka National Bank', 'LKBNK', 'asia', 'Sri Lanka', 'SRILLKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(746, 'Sri Lanka Commercial Bank', 'LKBNK', 'asia', 'Sri Lanka', 'SRILLKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(747, 'Sri Lanka Development Bank', 'LKBNK', 'asia', 'Sri Lanka', 'SRILLKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(748, 'First Bank of Sri Lanka', 'LKBNK', 'asia', 'Sri Lanka', 'SRILLKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(749, 'Sri Lanka People\'s Bank', 'LKBNK', 'asia', 'Sri Lanka', 'SRILLKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(750, 'Nepal National Bank', 'NPBNK', 'asia', 'Nepal', 'NEPANPXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(751, 'Nepal Commercial Bank', 'NPBNK', 'asia', 'Nepal', 'NEPANPXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(752, 'Nepal Development Bank', 'NPBNK', 'asia', 'Nepal', 'NEPANPXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(753, 'First Bank of Nepal', 'NPBNK', 'asia', 'Nepal', 'NEPANPXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(754, 'Nepal People\'s Bank', 'NPBNK', 'asia', 'Nepal', 'NEPANPXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(755, 'Myanmar (Burma) National Bank', 'MMBNK', 'asia', 'Myanmar (Burma)', 'MYANMMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(756, 'Myanmar (Burma) Commercial Bank', 'MMBNK', 'asia', 'Myanmar (Burma)', 'MYANMMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(757, 'Myanmar (Burma) Development Bank', 'MMBNK', 'asia', 'Myanmar (Burma)', 'MYANMMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(758, 'First Bank of Myanmar (Burma)', 'MMBNK', 'asia', 'Myanmar (Burma)', 'MYANMMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(759, 'Myanmar (Burma) People\'s Bank', 'MMBNK', 'asia', 'Myanmar (Burma)', 'MYANMMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(760, 'Cambodia National Bank', 'KHBNK', 'asia', 'Cambodia', 'CAMBKHXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(761, 'Cambodia Commercial Bank', 'KHBNK', 'asia', 'Cambodia', 'CAMBKHXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(762, 'Cambodia Development Bank', 'KHBNK', 'asia', 'Cambodia', 'CAMBKHXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(763, 'First Bank of Cambodia', 'KHBNK', 'asia', 'Cambodia', 'CAMBKHXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(764, 'Cambodia People\'s Bank', 'KHBNK', 'asia', 'Cambodia', 'CAMBKHXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(765, 'Laos National Bank', 'LABNK', 'asia', 'Laos', 'LAOSLAXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(766, 'Laos Commercial Bank', 'LABNK', 'asia', 'Laos', 'LAOSLAXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(767, 'Laos Development Bank', 'LABNK', 'asia', 'Laos', 'LAOSLAXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(768, 'First Bank of Laos', 'LABNK', 'asia', 'Laos', 'LAOSLAXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(769, 'Laos People\'s Bank', 'LABNK', 'asia', 'Laos', 'LAOSLAXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(770, 'Mongolia National Bank', 'MNBNK', 'asia', 'Mongolia', 'MONGMNXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(771, 'Mongolia Commercial Bank', 'MNBNK', 'asia', 'Mongolia', 'MONGMNXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(772, 'Mongolia Development Bank', 'MNBNK', 'asia', 'Mongolia', 'MONGMNXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(773, 'First Bank of Mongolia', 'MNBNK', 'asia', 'Mongolia', 'MONGMNXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(774, 'Mongolia People\'s Bank', 'MNBNK', 'asia', 'Mongolia', 'MONGMNXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(775, 'Kazakhstan National Bank', 'KZBNK', 'asia', 'Kazakhstan', 'KAZAKZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(776, 'Kazakhstan Commercial Bank', 'KZBNK', 'asia', 'Kazakhstan', 'KAZAKZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(777, 'Kazakhstan Development Bank', 'KZBNK', 'asia', 'Kazakhstan', 'KAZAKZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(778, 'First Bank of Kazakhstan', 'KZBNK', 'asia', 'Kazakhstan', 'KAZAKZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(779, 'Kazakhstan People\'s Bank', 'KZBNK', 'asia', 'Kazakhstan', 'KAZAKZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(780, 'Uzbekistan National Bank', 'UZBNK', 'asia', 'Uzbekistan', 'UZBEUZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(781, 'Uzbekistan Commercial Bank', 'UZBNK', 'asia', 'Uzbekistan', 'UZBEUZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(782, 'Uzbekistan Development Bank', 'UZBNK', 'asia', 'Uzbekistan', 'UZBEUZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(783, 'First Bank of Uzbekistan', 'UZBNK', 'asia', 'Uzbekistan', 'UZBEUZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(784, 'Uzbekistan People\'s Bank', 'UZBNK', 'asia', 'Uzbekistan', 'UZBEUZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(785, 'Kyrgyzstan National Bank', 'KGBNK', 'asia', 'Kyrgyzstan', 'KYRGKGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(786, 'Kyrgyzstan Commercial Bank', 'KGBNK', 'asia', 'Kyrgyzstan', 'KYRGKGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(787, 'Kyrgyzstan Development Bank', 'KGBNK', 'asia', 'Kyrgyzstan', 'KYRGKGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(788, 'First Bank of Kyrgyzstan', 'KGBNK', 'asia', 'Kyrgyzstan', 'KYRGKGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(789, 'Kyrgyzstan People\'s Bank', 'KGBNK', 'asia', 'Kyrgyzstan', 'KYRGKGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(790, 'Tajikistan National Bank', 'TJBNK', 'asia', 'Tajikistan', 'TAJITJXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(791, 'Tajikistan Commercial Bank', 'TJBNK', 'asia', 'Tajikistan', 'TAJITJXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(792, 'Tajikistan Development Bank', 'TJBNK', 'asia', 'Tajikistan', 'TAJITJXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(793, 'First Bank of Tajikistan', 'TJBNK', 'asia', 'Tajikistan', 'TAJITJXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(794, 'Tajikistan People\'s Bank', 'TJBNK', 'asia', 'Tajikistan', 'TAJITJXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(795, 'Turkmenistan National Bank', 'TMBNK', 'asia', 'Turkmenistan', 'TURKTMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(796, 'Turkmenistan Commercial Bank', 'TMBNK', 'asia', 'Turkmenistan', 'TURKTMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(797, 'Turkmenistan Development Bank', 'TMBNK', 'asia', 'Turkmenistan', 'TURKTMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(798, 'First Bank of Turkmenistan', 'TMBNK', 'asia', 'Turkmenistan', 'TURKTMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(799, 'Turkmenistan People\'s Bank', 'TMBNK', 'asia', 'Turkmenistan', 'TURKTMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(800, 'Bhutan National Bank', 'BTBNK', 'asia', 'Bhutan', 'BHUTBTXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(801, 'Bhutan Commercial Bank', 'BTBNK', 'asia', 'Bhutan', 'BHUTBTXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(802, 'Bhutan Development Bank', 'BTBNK', 'asia', 'Bhutan', 'BHUTBTXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(803, 'First Bank of Bhutan', 'BTBNK', 'asia', 'Bhutan', 'BHUTBTXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(804, 'Bhutan People\'s Bank', 'BTBNK', 'asia', 'Bhutan', 'BHUTBTXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(805, 'Maldives National Bank', 'MVBNK', 'asia', 'Maldives', 'MALDMVXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(806, 'Maldives Commercial Bank', 'MVBNK', 'asia', 'Maldives', 'MALDMVXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(807, 'Maldives Development Bank', 'MVBNK', 'asia', 'Maldives', 'MALDMVXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(808, 'First Bank of Maldives', 'MVBNK', 'asia', 'Maldives', 'MALDMVXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(809, 'Maldives People\'s Bank', 'MVBNK', 'asia', 'Maldives', 'MALDMVXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(810, 'Brunei National Bank', 'BNBNK', 'asia', 'Brunei', 'BRUNBNXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(811, 'Brunei Commercial Bank', 'BNBNK', 'asia', 'Brunei', 'BRUNBNXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(812, 'Brunei Development Bank', 'BNBNK', 'asia', 'Brunei', 'BRUNBNXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(813, 'First Bank of Brunei', 'BNBNK', 'asia', 'Brunei', 'BRUNBNXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(814, 'Brunei People\'s Bank', 'BNBNK', 'asia', 'Brunei', 'BRUNBNXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(815, 'Timor-Leste National Bank', 'TLBNK', 'asia', 'Timor-Leste', 'TIMOTLXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(816, 'Timor-Leste Commercial Bank', 'TLBNK', 'asia', 'Timor-Leste', 'TIMOTLXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(817, 'Timor-Leste Development Bank', 'TLBNK', 'asia', 'Timor-Leste', 'TIMOTLXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(818, 'First Bank of Timor-Leste', 'TLBNK', 'asia', 'Timor-Leste', 'TIMOTLXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(819, 'Timor-Leste People\'s Bank', 'TLBNK', 'asia', 'Timor-Leste', 'TIMOTLXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(820, 'North Korea National Bank', 'KPBNK', 'asia', 'North Korea', 'NORTKPXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(821, 'North Korea Commercial Bank', 'KPBNK', 'asia', 'North Korea', 'NORTKPXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(822, 'North Korea Development Bank', 'KPBNK', 'asia', 'North Korea', 'NORTKPXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(823, 'First Bank of North Korea', 'KPBNK', 'asia', 'North Korea', 'NORTKPXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(824, 'North Korea People\'s Bank', 'KPBNK', 'asia', 'North Korea', 'NORTKPXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(825, 'Kenya National Bank', 'KEBNK', 'africa', 'Kenya', 'KENYKEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(826, 'Kenya Development Bank', 'KEBNK', 'africa', 'Kenya', 'KENYKEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(827, 'Ghana National Bank', 'GHBNK', 'africa', 'Ghana', 'GHANGHXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(828, 'Ghana Development Bank', 'GHBNK', 'africa', 'Ghana', 'GHANGHXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(829, 'First Bank of Ghana', 'GHBNK', 'africa', 'Ghana', 'GHANGHXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(830, 'Ethiopia National Bank', 'ETBNK', 'africa', 'Ethiopia', 'ETHIETXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(831, 'Ethiopia Commercial Bank', 'ETBNK', 'africa', 'Ethiopia', 'ETHIETXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(832, 'Ethiopia Development Bank', 'ETBNK', 'africa', 'Ethiopia', 'ETHIETXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(833, 'First Bank of Ethiopia', 'ETBNK', 'africa', 'Ethiopia', 'ETHIETXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(834, 'Ethiopia People\'s Bank', 'ETBNK', 'africa', 'Ethiopia', 'ETHIETXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(835, 'Tanzania National Bank', 'TZBNK', 'africa', 'Tanzania', 'TANZTZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(836, 'Tanzania Commercial Bank', 'TZBNK', 'africa', 'Tanzania', 'TANZTZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(837, 'Tanzania Development Bank', 'TZBNK', 'africa', 'Tanzania', 'TANZTZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(838, 'First Bank of Tanzania', 'TZBNK', 'africa', 'Tanzania', 'TANZTZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(839, 'Tanzania People\'s Bank', 'TZBNK', 'africa', 'Tanzania', 'TANZTZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(840, 'Uganda National Bank', 'UGBNK', 'africa', 'Uganda', 'UGANUGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(841, 'Uganda Commercial Bank', 'UGBNK', 'africa', 'Uganda', 'UGANUGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(842, 'Uganda Development Bank', 'UGBNK', 'africa', 'Uganda', 'UGANUGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(843, 'First Bank of Uganda', 'UGBNK', 'africa', 'Uganda', 'UGANUGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(844, 'Uganda People\'s Bank', 'UGBNK', 'africa', 'Uganda', 'UGANUGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(845, 'South Sudan National Bank', 'SSBNK', 'africa', 'South Sudan', 'SOUTSSXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(846, 'South Sudan Commercial Bank', 'SSBNK', 'africa', 'South Sudan', 'SOUTSSXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(847, 'South Sudan Development Bank', 'SSBNK', 'africa', 'South Sudan', 'SOUTSSXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(848, 'First Bank of South Sudan', 'SSBNK', 'africa', 'South Sudan', 'SOUTSSXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23');
INSERT INTO `banks` (`id`, `name`, `code`, `region`, `country`, `swift_code`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(849, 'South Sudan People\'s Bank', 'SSBNK', 'africa', 'South Sudan', 'SOUTSSXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(850, 'Angola National Bank', 'AOBNK', 'africa', 'Angola', 'ANGOAOXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(851, 'Angola Commercial Bank', 'AOBNK', 'africa', 'Angola', 'ANGOAOXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(852, 'Angola Development Bank', 'AOBNK', 'africa', 'Angola', 'ANGOAOXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(853, 'First Bank of Angola', 'AOBNK', 'africa', 'Angola', 'ANGOAOXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(854, 'Angola People\'s Bank', 'AOBNK', 'africa', 'Angola', 'ANGOAOXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(855, 'Mozambique National Bank', 'MZBNK', 'africa', 'Mozambique', 'MOZAMZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(856, 'Mozambique Commercial Bank', 'MZBNK', 'africa', 'Mozambique', 'MOZAMZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(857, 'Mozambique Development Bank', 'MZBNK', 'africa', 'Mozambique', 'MOZAMZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(858, 'First Bank of Mozambique', 'MZBNK', 'africa', 'Mozambique', 'MOZAMZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(859, 'Mozambique People\'s Bank', 'MZBNK', 'africa', 'Mozambique', 'MOZAMZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(860, 'Zambia National Bank', 'ZMBNK', 'africa', 'Zambia', 'ZAMBZMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(861, 'Zambia Commercial Bank', 'ZMBNK', 'africa', 'Zambia', 'ZAMBZMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(862, 'Zambia Development Bank', 'ZMBNK', 'africa', 'Zambia', 'ZAMBZMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(863, 'First Bank of Zambia', 'ZMBNK', 'africa', 'Zambia', 'ZAMBZMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(864, 'Zambia People\'s Bank', 'ZMBNK', 'africa', 'Zambia', 'ZAMBZMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(865, 'Zimbabwe National Bank', 'ZWBNK', 'africa', 'Zimbabwe', 'ZIMBZWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(866, 'Zimbabwe Commercial Bank', 'ZWBNK', 'africa', 'Zimbabwe', 'ZIMBZWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(867, 'Zimbabwe Development Bank', 'ZWBNK', 'africa', 'Zimbabwe', 'ZIMBZWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(868, 'First Bank of Zimbabwe', 'ZWBNK', 'africa', 'Zimbabwe', 'ZIMBZWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(869, 'Zimbabwe People\'s Bank', 'ZWBNK', 'africa', 'Zimbabwe', 'ZIMBZWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(870, 'Botswana National Bank', 'BWBNK', 'africa', 'Botswana', 'BOTSBWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(871, 'Botswana Commercial Bank', 'BWBNK', 'africa', 'Botswana', 'BOTSBWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(872, 'Botswana Development Bank', 'BWBNK', 'africa', 'Botswana', 'BOTSBWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(873, 'First Bank of Botswana', 'BWBNK', 'africa', 'Botswana', 'BOTSBWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(874, 'Botswana People\'s Bank', 'BWBNK', 'africa', 'Botswana', 'BOTSBWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(875, 'Namibia National Bank', 'NABNK', 'africa', 'Namibia', 'NAMINAXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(876, 'Namibia Commercial Bank', 'NABNK', 'africa', 'Namibia', 'NAMINAXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(877, 'Namibia Development Bank', 'NABNK', 'africa', 'Namibia', 'NAMINAXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(878, 'First Bank of Namibia', 'NABNK', 'africa', 'Namibia', 'NAMINAXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(879, 'Namibia People\'s Bank', 'NABNK', 'africa', 'Namibia', 'NAMINAXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(880, 'Senegal National Bank', 'SNBNK', 'africa', 'Senegal', 'SENESNXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(881, 'Senegal Commercial Bank', 'SNBNK', 'africa', 'Senegal', 'SENESNXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(882, 'Senegal Development Bank', 'SNBNK', 'africa', 'Senegal', 'SENESNXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(883, 'First Bank of Senegal', 'SNBNK', 'africa', 'Senegal', 'SENESNXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(884, 'Senegal People\'s Bank', 'SNBNK', 'africa', 'Senegal', 'SENESNXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(885, 'Côte d’Ivoire National Bank', 'CIBNK', 'africa', 'Côte d’Ivoire', 'CTEDCIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(886, 'Côte d’Ivoire Commercial Bank', 'CIBNK', 'africa', 'Côte d’Ivoire', 'CTEDCIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(887, 'Côte d’Ivoire Development Bank', 'CIBNK', 'africa', 'Côte d’Ivoire', 'CTEDCIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(888, 'First Bank of Côte d’Ivoire', 'CIBNK', 'africa', 'Côte d’Ivoire', 'CTEDCIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(889, 'Côte d’Ivoire People\'s Bank', 'CIBNK', 'africa', 'Côte d’Ivoire', 'CTEDCIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(890, 'Cameroon National Bank', 'CMBNK', 'africa', 'Cameroon', 'CAMECMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(891, 'Cameroon Commercial Bank', 'CMBNK', 'africa', 'Cameroon', 'CAMECMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(892, 'Cameroon Development Bank', 'CMBNK', 'africa', 'Cameroon', 'CAMECMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(893, 'First Bank of Cameroon', 'CMBNK', 'africa', 'Cameroon', 'CAMECMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(894, 'Cameroon People\'s Bank', 'CMBNK', 'africa', 'Cameroon', 'CAMECMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(895, 'Democratic Republic of the Congo National Bank', 'CDBNK', 'africa', 'Democratic Republic of the Congo', 'DEMOCDXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(896, 'Democratic Republic of the Congo Commercial Bank', 'CDBNK', 'africa', 'Democratic Republic of the Congo', 'DEMOCDXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(897, 'Democratic Republic of the Congo Development Bank', 'CDBNK', 'africa', 'Democratic Republic of the Congo', 'DEMOCDXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(898, 'First Bank of Democratic Republic of the Congo', 'CDBNK', 'africa', 'Democratic Republic of the Congo', 'DEMOCDXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(899, 'Democratic Republic of the Congo People\'s Bank', 'CDBNK', 'africa', 'Democratic Republic of the Congo', 'DEMOCDXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(900, 'Congo (Congo-Brazzaville) National Bank', 'CGBNK', 'africa', 'Congo (Congo-Brazzaville)', 'CONGCGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(901, 'Congo (Congo-Brazzaville) Commercial Bank', 'CGBNK', 'africa', 'Congo (Congo-Brazzaville)', 'CONGCGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(902, 'Congo (Congo-Brazzaville) Development Bank', 'CGBNK', 'africa', 'Congo (Congo-Brazzaville)', 'CONGCGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(903, 'First Bank of Congo (Congo-Brazzaville)', 'CGBNK', 'africa', 'Congo (Congo-Brazzaville)', 'CONGCGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(904, 'Congo (Congo-Brazzaville) People\'s Bank', 'CGBNK', 'africa', 'Congo (Congo-Brazzaville)', 'CONGCGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(905, 'Rwanda National Bank', 'RWBNK', 'africa', 'Rwanda', 'RWANRWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(906, 'Rwanda Commercial Bank', 'RWBNK', 'africa', 'Rwanda', 'RWANRWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(907, 'Rwanda Development Bank', 'RWBNK', 'africa', 'Rwanda', 'RWANRWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(908, 'First Bank of Rwanda', 'RWBNK', 'africa', 'Rwanda', 'RWANRWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(909, 'Rwanda People\'s Bank', 'RWBNK', 'africa', 'Rwanda', 'RWANRWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(910, 'Burundi National Bank', 'BIBNK', 'africa', 'Burundi', 'BURUBIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(911, 'Burundi Commercial Bank', 'BIBNK', 'africa', 'Burundi', 'BURUBIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(912, 'Burundi Development Bank', 'BIBNK', 'africa', 'Burundi', 'BURUBIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(913, 'First Bank of Burundi', 'BIBNK', 'africa', 'Burundi', 'BURUBIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(914, 'Burundi People\'s Bank', 'BIBNK', 'africa', 'Burundi', 'BURUBIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(915, 'Mali National Bank', 'MLBNK', 'africa', 'Mali', 'MALIMLXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(916, 'Mali Commercial Bank', 'MLBNK', 'africa', 'Mali', 'MALIMLXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(917, 'Mali Development Bank', 'MLBNK', 'africa', 'Mali', 'MALIMLXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(918, 'First Bank of Mali', 'MLBNK', 'africa', 'Mali', 'MALIMLXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(919, 'Mali People\'s Bank', 'MLBNK', 'africa', 'Mali', 'MALIMLXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(920, 'Niger National Bank', 'NEBNK', 'africa', 'Niger', 'NIGENEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(921, 'Niger Commercial Bank', 'NEBNK', 'africa', 'Niger', 'NIGENEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(922, 'Niger Development Bank', 'NEBNK', 'africa', 'Niger', 'NIGENEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(923, 'First Bank of Niger', 'NEBNK', 'africa', 'Niger', 'NIGENEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(924, 'Niger People\'s Bank', 'NEBNK', 'africa', 'Niger', 'NIGENEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(925, 'Chad National Bank', 'TDBNK', 'africa', 'Chad', 'CHADTDXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(926, 'Chad Commercial Bank', 'TDBNK', 'africa', 'Chad', 'CHADTDXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(927, 'Chad Development Bank', 'TDBNK', 'africa', 'Chad', 'CHADTDXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(928, 'First Bank of Chad', 'TDBNK', 'africa', 'Chad', 'CHADTDXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(929, 'Chad People\'s Bank', 'TDBNK', 'africa', 'Chad', 'CHADTDXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(930, 'Mauritania National Bank', 'MRBNK', 'africa', 'Mauritania', 'MAURMRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(931, 'Mauritania Commercial Bank', 'MRBNK', 'africa', 'Mauritania', 'MAURMRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(932, 'Mauritania Development Bank', 'MRBNK', 'africa', 'Mauritania', 'MAURMRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(933, 'First Bank of Mauritania', 'MRBNK', 'africa', 'Mauritania', 'MAURMRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(934, 'Mauritania People\'s Bank', 'MRBNK', 'africa', 'Mauritania', 'MAURMRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(935, 'Somalia National Bank', 'SOBNK', 'africa', 'Somalia', 'SOMASOXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(936, 'Somalia Commercial Bank', 'SOBNK', 'africa', 'Somalia', 'SOMASOXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(937, 'Somalia Development Bank', 'SOBNK', 'africa', 'Somalia', 'SOMASOXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(938, 'First Bank of Somalia', 'SOBNK', 'africa', 'Somalia', 'SOMASOXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(939, 'Somalia People\'s Bank', 'SOBNK', 'africa', 'Somalia', 'SOMASOXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(940, 'Eritrea National Bank', 'ERBNK', 'africa', 'Eritrea', 'ERITERXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(941, 'Eritrea Commercial Bank', 'ERBNK', 'africa', 'Eritrea', 'ERITERXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(942, 'Eritrea Development Bank', 'ERBNK', 'africa', 'Eritrea', 'ERITERXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(943, 'First Bank of Eritrea', 'ERBNK', 'africa', 'Eritrea', 'ERITERXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(944, 'Eritrea People\'s Bank', 'ERBNK', 'africa', 'Eritrea', 'ERITERXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(945, 'Djibouti National Bank', 'DJBNK', 'africa', 'Djibouti', 'DJIBDJXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(946, 'Djibouti Commercial Bank', 'DJBNK', 'africa', 'Djibouti', 'DJIBDJXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(947, 'Djibouti Development Bank', 'DJBNK', 'africa', 'Djibouti', 'DJIBDJXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(948, 'First Bank of Djibouti', 'DJBNK', 'africa', 'Djibouti', 'DJIBDJXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(949, 'Djibouti People\'s Bank', 'DJBNK', 'africa', 'Djibouti', 'DJIBDJXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(950, 'Madagascar National Bank', 'MGBNK', 'africa', 'Madagascar', 'MADAMGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(951, 'Madagascar Commercial Bank', 'MGBNK', 'africa', 'Madagascar', 'MADAMGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(952, 'Madagascar Development Bank', 'MGBNK', 'africa', 'Madagascar', 'MADAMGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(953, 'First Bank of Madagascar', 'MGBNK', 'africa', 'Madagascar', 'MADAMGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(954, 'Madagascar People\'s Bank', 'MGBNK', 'africa', 'Madagascar', 'MADAMGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(955, 'Malawi National Bank', 'MWBNK', 'africa', 'Malawi', 'MALAMWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(956, 'Malawi Commercial Bank', 'MWBNK', 'africa', 'Malawi', 'MALAMWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(957, 'Malawi Development Bank', 'MWBNK', 'africa', 'Malawi', 'MALAMWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(958, 'First Bank of Malawi', 'MWBNK', 'africa', 'Malawi', 'MALAMWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(959, 'Malawi People\'s Bank', 'MWBNK', 'africa', 'Malawi', 'MALAMWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(960, 'Lesotho National Bank', 'LSBNK', 'africa', 'Lesotho', 'LESOLSXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(961, 'Lesotho Commercial Bank', 'LSBNK', 'africa', 'Lesotho', 'LESOLSXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(962, 'Lesotho Development Bank', 'LSBNK', 'africa', 'Lesotho', 'LESOLSXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(963, 'First Bank of Lesotho', 'LSBNK', 'africa', 'Lesotho', 'LESOLSXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(964, 'Lesotho People\'s Bank', 'LSBNK', 'africa', 'Lesotho', 'LESOLSXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(965, 'Eswatini National Bank', 'SZBNK', 'africa', 'Eswatini', 'ESWASZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(966, 'Eswatini Commercial Bank', 'SZBNK', 'africa', 'Eswatini', 'ESWASZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(967, 'Eswatini Development Bank', 'SZBNK', 'africa', 'Eswatini', 'ESWASZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(968, 'First Bank of Eswatini', 'SZBNK', 'africa', 'Eswatini', 'ESWASZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(969, 'Eswatini People\'s Bank', 'SZBNK', 'africa', 'Eswatini', 'ESWASZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(970, 'Guinea National Bank', 'GNBNK', 'africa', 'Guinea', 'GUINGNXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(971, 'Guinea Commercial Bank', 'GNBNK', 'africa', 'Guinea', 'GUINGNXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(972, 'Guinea Development Bank', 'GNBNK', 'africa', 'Guinea', 'GUINGNXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(973, 'First Bank of Guinea', 'GNBNK', 'africa', 'Guinea', 'GUINGNXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(974, 'Guinea People\'s Bank', 'GNBNK', 'africa', 'Guinea', 'GUINGNXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(975, 'Sierra Leone National Bank', 'SLBNK', 'africa', 'Sierra Leone', 'SIERSLXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(976, 'Sierra Leone Commercial Bank', 'SLBNK', 'africa', 'Sierra Leone', 'SIERSLXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(977, 'Sierra Leone Development Bank', 'SLBNK', 'africa', 'Sierra Leone', 'SIERSLXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(978, 'First Bank of Sierra Leone', 'SLBNK', 'africa', 'Sierra Leone', 'SIERSLXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(979, 'Sierra Leone People\'s Bank', 'SLBNK', 'africa', 'Sierra Leone', 'SIERSLXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(980, 'Liberia National Bank', 'LRBNK', 'africa', 'Liberia', 'LIBELRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(981, 'Liberia Commercial Bank', 'LRBNK', 'africa', 'Liberia', 'LIBELRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(982, 'Liberia Development Bank', 'LRBNK', 'africa', 'Liberia', 'LIBELRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(983, 'First Bank of Liberia', 'LRBNK', 'africa', 'Liberia', 'LIBELRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(984, 'Liberia People\'s Bank', 'LRBNK', 'africa', 'Liberia', 'LIBELRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(985, 'Togo National Bank', 'TGBNK', 'africa', 'Togo', 'TOGOTGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(986, 'Togo Commercial Bank', 'TGBNK', 'africa', 'Togo', 'TOGOTGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(987, 'Togo Development Bank', 'TGBNK', 'africa', 'Togo', 'TOGOTGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(988, 'First Bank of Togo', 'TGBNK', 'africa', 'Togo', 'TOGOTGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(989, 'Togo People\'s Bank', 'TGBNK', 'africa', 'Togo', 'TOGOTGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(990, 'Benin National Bank', 'BJBNK', 'africa', 'Benin', 'BENIBJXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(991, 'Benin Commercial Bank', 'BJBNK', 'africa', 'Benin', 'BENIBJXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(992, 'Benin Development Bank', 'BJBNK', 'africa', 'Benin', 'BENIBJXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(993, 'First Bank of Benin', 'BJBNK', 'africa', 'Benin', 'BENIBJXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(994, 'Benin People\'s Bank', 'BJBNK', 'africa', 'Benin', 'BENIBJXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(995, 'Burkina Faso National Bank', 'BFBNK', 'africa', 'Burkina Faso', 'BURKBFXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(996, 'Burkina Faso Commercial Bank', 'BFBNK', 'africa', 'Burkina Faso', 'BURKBFXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(997, 'Burkina Faso Development Bank', 'BFBNK', 'africa', 'Burkina Faso', 'BURKBFXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(998, 'First Bank of Burkina Faso', 'BFBNK', 'africa', 'Burkina Faso', 'BURKBFXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(999, 'Burkina Faso People\'s Bank', 'BFBNK', 'africa', 'Burkina Faso', 'BURKBFXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1000, 'Gabon National Bank', 'GABNK', 'africa', 'Gabon', 'GABOGAXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1001, 'Gabon Commercial Bank', 'GABNK', 'africa', 'Gabon', 'GABOGAXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1002, 'Gabon Development Bank', 'GABNK', 'africa', 'Gabon', 'GABOGAXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1003, 'First Bank of Gabon', 'GABNK', 'africa', 'Gabon', 'GABOGAXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1004, 'Gabon People\'s Bank', 'GABNK', 'africa', 'Gabon', 'GABOGAXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1005, 'Equatorial Guinea National Bank', 'GQBNK', 'africa', 'Equatorial Guinea', 'EQUAGQXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1006, 'Equatorial Guinea Commercial Bank', 'GQBNK', 'africa', 'Equatorial Guinea', 'EQUAGQXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1007, 'Equatorial Guinea Development Bank', 'GQBNK', 'africa', 'Equatorial Guinea', 'EQUAGQXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1008, 'First Bank of Equatorial Guinea', 'GQBNK', 'africa', 'Equatorial Guinea', 'EQUAGQXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1009, 'Equatorial Guinea People\'s Bank', 'GQBNK', 'africa', 'Equatorial Guinea', 'EQUAGQXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1010, 'Central African Republic National Bank', 'CFBNK', 'africa', 'Central African Republic', 'CENTCFXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1011, 'Central African Republic Commercial Bank', 'CFBNK', 'africa', 'Central African Republic', 'CENTCFXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1012, 'Central African Republic Development Bank', 'CFBNK', 'africa', 'Central African Republic', 'CENTCFXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1013, 'First Bank of Central African Republic', 'CFBNK', 'africa', 'Central African Republic', 'CENTCFXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1014, 'Central African Republic People\'s Bank', 'CFBNK', 'africa', 'Central African Republic', 'CENTCFXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1015, 'Gambia National Bank', 'GMBNK', 'africa', 'Gambia', 'GAMBGMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1016, 'Gambia Commercial Bank', 'GMBNK', 'africa', 'Gambia', 'GAMBGMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1017, 'Gambia Development Bank', 'GMBNK', 'africa', 'Gambia', 'GAMBGMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1018, 'First Bank of Gambia', 'GMBNK', 'africa', 'Gambia', 'GAMBGMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1019, 'Gambia People\'s Bank', 'GMBNK', 'africa', 'Gambia', 'GAMBGMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1020, 'Guinea-Bissau National Bank', 'GWBNK', 'africa', 'Guinea-Bissau', 'GUINGWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1021, 'Guinea-Bissau Commercial Bank', 'GWBNK', 'africa', 'Guinea-Bissau', 'GUINGWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1022, 'Guinea-Bissau Development Bank', 'GWBNK', 'africa', 'Guinea-Bissau', 'GUINGWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1023, 'First Bank of Guinea-Bissau', 'GWBNK', 'africa', 'Guinea-Bissau', 'GUINGWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1024, 'Guinea-Bissau People\'s Bank', 'GWBNK', 'africa', 'Guinea-Bissau', 'GUINGWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1025, 'Cabo Verde National Bank', 'CVBNK', 'africa', 'Cabo Verde', 'CABOCVXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1026, 'Cabo Verde Commercial Bank', 'CVBNK', 'africa', 'Cabo Verde', 'CABOCVXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1027, 'Cabo Verde Development Bank', 'CVBNK', 'africa', 'Cabo Verde', 'CABOCVXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1028, 'First Bank of Cabo Verde', 'CVBNK', 'africa', 'Cabo Verde', 'CABOCVXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1029, 'Cabo Verde People\'s Bank', 'CVBNK', 'africa', 'Cabo Verde', 'CABOCVXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1030, 'Sao Tome and Principe National Bank', 'STBNK', 'africa', 'Sao Tome and Principe', 'SAOTSTXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1031, 'Sao Tome and Principe Commercial Bank', 'STBNK', 'africa', 'Sao Tome and Principe', 'SAOTSTXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1032, 'Sao Tome and Principe Development Bank', 'STBNK', 'africa', 'Sao Tome and Principe', 'SAOTSTXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1033, 'First Bank of Sao Tome and Principe', 'STBNK', 'africa', 'Sao Tome and Principe', 'SAOTSTXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1034, 'Sao Tome and Principe People\'s Bank', 'STBNK', 'africa', 'Sao Tome and Principe', 'SAOTSTXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1035, 'Seychelles National Bank', 'SCBNK', 'africa', 'Seychelles', 'SEYCSCXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1036, 'Seychelles Commercial Bank', 'SCBNK', 'africa', 'Seychelles', 'SEYCSCXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1037, 'Seychelles Development Bank', 'SCBNK', 'africa', 'Seychelles', 'SEYCSCXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1038, 'First Bank of Seychelles', 'SCBNK', 'africa', 'Seychelles', 'SEYCSCXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1039, 'Seychelles People\'s Bank', 'SCBNK', 'africa', 'Seychelles', 'SEYCSCXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1040, 'Mauritius National Bank', 'MUBNK', 'africa', 'Mauritius', 'MAURMUXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1041, 'Mauritius Commercial Bank', 'MUBNK', 'africa', 'Mauritius', 'MAURMUXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1042, 'Mauritius Development Bank', 'MUBNK', 'africa', 'Mauritius', 'MAURMUXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1043, 'First Bank of Mauritius', 'MUBNK', 'africa', 'Mauritius', 'MAURMUXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1044, 'Mauritius People\'s Bank', 'MUBNK', 'africa', 'Mauritius', 'MAURMUXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1045, 'Comoros National Bank', 'KMBNK', 'africa', 'Comoros', 'COMOKMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1046, 'Comoros Commercial Bank', 'KMBNK', 'africa', 'Comoros', 'COMOKMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1047, 'Comoros Development Bank', 'KMBNK', 'africa', 'Comoros', 'COMOKMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1048, 'First Bank of Comoros', 'KMBNK', 'africa', 'Comoros', 'COMOKMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1049, 'Comoros People\'s Bank', 'KMBNK', 'africa', 'Comoros', 'COMOKMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1050, 'Fiji National Bank', 'FJBNK', 'oceania', 'Fiji', 'FIJIFJXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1051, 'Fiji Commercial Bank', 'FJBNK', 'oceania', 'Fiji', 'FIJIFJXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1052, 'Papua New Guinea National Bank', 'PGBNK', 'oceania', 'Papua New Guinea', 'PAPUPGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1053, 'Papua New Guinea Commercial Bank', 'PGBNK', 'oceania', 'Papua New Guinea', 'PAPUPGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1054, 'Papua New Guinea Development Bank', 'PGBNK', 'oceania', 'Papua New Guinea', 'PAPUPGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1055, 'Samoa National Bank', 'WSBNK', 'oceania', 'Samoa', 'SAMOWSXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1056, 'Samoa Commercial Bank', 'WSBNK', 'oceania', 'Samoa', 'SAMOWSXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1057, 'Samoa Development Bank', 'WSBNK', 'oceania', 'Samoa', 'SAMOWSXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1058, 'First Bank of Samoa', 'WSBNK', 'oceania', 'Samoa', 'SAMOWSXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1059, 'Samoa People\'s Bank', 'WSBNK', 'oceania', 'Samoa', 'SAMOWSXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1060, 'Tonga National Bank', 'TOBNK', 'oceania', 'Tonga', 'TONGTOXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1061, 'Tonga Commercial Bank', 'TOBNK', 'oceania', 'Tonga', 'TONGTOXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1062, 'Tonga Development Bank', 'TOBNK', 'oceania', 'Tonga', 'TONGTOXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1063, 'First Bank of Tonga', 'TOBNK', 'oceania', 'Tonga', 'TONGTOXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1064, 'Tonga People\'s Bank', 'TOBNK', 'oceania', 'Tonga', 'TONGTOXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1065, 'Vanuatu National Bank', 'VUBNK', 'oceania', 'Vanuatu', 'VANUVUXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1066, 'Vanuatu Commercial Bank', 'VUBNK', 'oceania', 'Vanuatu', 'VANUVUXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1067, 'Vanuatu Development Bank', 'VUBNK', 'oceania', 'Vanuatu', 'VANUVUXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1068, 'First Bank of Vanuatu', 'VUBNK', 'oceania', 'Vanuatu', 'VANUVUXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1069, 'Vanuatu People\'s Bank', 'VUBNK', 'oceania', 'Vanuatu', 'VANUVUXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1070, 'Solomon Islands National Bank', 'SBBNK', 'oceania', 'Solomon Islands', 'SOLOSBXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1071, 'Solomon Islands Commercial Bank', 'SBBNK', 'oceania', 'Solomon Islands', 'SOLOSBXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1072, 'Solomon Islands Development Bank', 'SBBNK', 'oceania', 'Solomon Islands', 'SOLOSBXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1073, 'First Bank of Solomon Islands', 'SBBNK', 'oceania', 'Solomon Islands', 'SOLOSBXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1074, 'Solomon Islands People\'s Bank', 'SBBNK', 'oceania', 'Solomon Islands', 'SOLOSBXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1075, 'Kiribati National Bank', 'KIBNK', 'oceania', 'Kiribati', 'KIRIKIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1076, 'Kiribati Commercial Bank', 'KIBNK', 'oceania', 'Kiribati', 'KIRIKIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1077, 'Kiribati Development Bank', 'KIBNK', 'oceania', 'Kiribati', 'KIRIKIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1078, 'First Bank of Kiribati', 'KIBNK', 'oceania', 'Kiribati', 'KIRIKIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1079, 'Kiribati People\'s Bank', 'KIBNK', 'oceania', 'Kiribati', 'KIRIKIXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1080, 'Tuvalu National Bank', 'TVBNK', 'oceania', 'Tuvalu', 'TUVATVXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1081, 'Tuvalu Commercial Bank', 'TVBNK', 'oceania', 'Tuvalu', 'TUVATVXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1082, 'Tuvalu Development Bank', 'TVBNK', 'oceania', 'Tuvalu', 'TUVATVXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1083, 'First Bank of Tuvalu', 'TVBNK', 'oceania', 'Tuvalu', 'TUVATVXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1084, 'Tuvalu People\'s Bank', 'TVBNK', 'oceania', 'Tuvalu', 'TUVATVXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1085, 'Nauru National Bank', 'NRBNK', 'oceania', 'Nauru', 'NAURNRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1086, 'Nauru Commercial Bank', 'NRBNK', 'oceania', 'Nauru', 'NAURNRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1087, 'Nauru Development Bank', 'NRBNK', 'oceania', 'Nauru', 'NAURNRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1088, 'First Bank of Nauru', 'NRBNK', 'oceania', 'Nauru', 'NAURNRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1089, 'Nauru People\'s Bank', 'NRBNK', 'oceania', 'Nauru', 'NAURNRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1090, 'Palau National Bank', 'PWBNK', 'oceania', 'Palau', 'PALAPWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1091, 'Palau Commercial Bank', 'PWBNK', 'oceania', 'Palau', 'PALAPWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1092, 'Palau Development Bank', 'PWBNK', 'oceania', 'Palau', 'PALAPWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1093, 'First Bank of Palau', 'PWBNK', 'oceania', 'Palau', 'PALAPWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1094, 'Palau People\'s Bank', 'PWBNK', 'oceania', 'Palau', 'PALAPWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1095, 'Micronesia National Bank', 'FMBNK', 'oceania', 'Micronesia', 'MICRFMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1096, 'Micronesia Commercial Bank', 'FMBNK', 'oceania', 'Micronesia', 'MICRFMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1097, 'Micronesia Development Bank', 'FMBNK', 'oceania', 'Micronesia', 'MICRFMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1098, 'First Bank of Micronesia', 'FMBNK', 'oceania', 'Micronesia', 'MICRFMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1099, 'Micronesia People\'s Bank', 'FMBNK', 'oceania', 'Micronesia', 'MICRFMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1100, 'Marshall Islands National Bank', 'MHBNK', 'oceania', 'Marshall Islands', 'MARSMHXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1101, 'Marshall Islands Commercial Bank', 'MHBNK', 'oceania', 'Marshall Islands', 'MARSMHXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1102, 'Marshall Islands Development Bank', 'MHBNK', 'oceania', 'Marshall Islands', 'MARSMHXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1103, 'First Bank of Marshall Islands', 'MHBNK', 'oceania', 'Marshall Islands', 'MARSMHXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1104, 'Marshall Islands People\'s Bank', 'MHBNK', 'oceania', 'Marshall Islands', 'MARSMHXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1105, 'Guam National Bank', 'GUBNK', 'oceania', 'Guam', 'GUAMGUXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1106, 'Guam Commercial Bank', 'GUBNK', 'oceania', 'Guam', 'GUAMGUXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1107, 'Guam Development Bank', 'GUBNK', 'oceania', 'Guam', 'GUAMGUXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1108, 'First Bank of Guam', 'GUBNK', 'oceania', 'Guam', 'GUAMGUXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1109, 'Guam People\'s Bank', 'GUBNK', 'oceania', 'Guam', 'GUAMGUXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1110, 'New Caledonia National Bank', 'NCBNK', 'oceania', 'New Caledonia', 'NEWCNCXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1111, 'New Caledonia Commercial Bank', 'NCBNK', 'oceania', 'New Caledonia', 'NEWCNCXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1112, 'New Caledonia Development Bank', 'NCBNK', 'oceania', 'New Caledonia', 'NEWCNCXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1113, 'First Bank of New Caledonia', 'NCBNK', 'oceania', 'New Caledonia', 'NEWCNCXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1114, 'New Caledonia People\'s Bank', 'NCBNK', 'oceania', 'New Caledonia', 'NEWCNCXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1115, 'French Polynesia National Bank', 'PFBNK', 'oceania', 'French Polynesia', 'FRENPFXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1116, 'French Polynesia Commercial Bank', 'PFBNK', 'oceania', 'French Polynesia', 'FRENPFXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1117, 'French Polynesia Development Bank', 'PFBNK', 'oceania', 'French Polynesia', 'FRENPFXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1118, 'First Bank of French Polynesia', 'PFBNK', 'oceania', 'French Polynesia', 'FRENPFXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1119, 'French Polynesia People\'s Bank', 'PFBNK', 'oceania', 'French Polynesia', 'FRENPFXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1120, 'American Samoa National Bank', 'ASBNK', 'oceania', 'American Samoa', 'AMERASXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1121, 'American Samoa Commercial Bank', 'ASBNK', 'oceania', 'American Samoa', 'AMERASXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1122, 'American Samoa Development Bank', 'ASBNK', 'oceania', 'American Samoa', 'AMERASXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1123, 'First Bank of American Samoa', 'ASBNK', 'oceania', 'American Samoa', 'AMERASXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1124, 'American Samoa People\'s Bank', 'ASBNK', 'oceania', 'American Samoa', 'AMERASXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1125, 'Cook Islands National Bank', 'CKBNK', 'oceania', 'Cook Islands', 'COOKCKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1126, 'Cook Islands Commercial Bank', 'CKBNK', 'oceania', 'Cook Islands', 'COOKCKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1127, 'Cook Islands Development Bank', 'CKBNK', 'oceania', 'Cook Islands', 'COOKCKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1128, 'First Bank of Cook Islands', 'CKBNK', 'oceania', 'Cook Islands', 'COOKCKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1129, 'Cook Islands People\'s Bank', 'CKBNK', 'oceania', 'Cook Islands', 'COOKCKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1130, 'Niue National Bank', 'NUBNK', 'oceania', 'Niue', 'NIUENUXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1131, 'Niue Commercial Bank', 'NUBNK', 'oceania', 'Niue', 'NIUENUXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1132, 'Niue Development Bank', 'NUBNK', 'oceania', 'Niue', 'NIUENUXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1133, 'First Bank of Niue', 'NUBNK', 'oceania', 'Niue', 'NIUENUXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1134, 'Niue People\'s Bank', 'NUBNK', 'oceania', 'Niue', 'NIUENUXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1135, 'Tokelau National Bank', 'TKBNK', 'oceania', 'Tokelau', 'TOKETKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1136, 'Tokelau Commercial Bank', 'TKBNK', 'oceania', 'Tokelau', 'TOKETKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1137, 'Tokelau Development Bank', 'TKBNK', 'oceania', 'Tokelau', 'TOKETKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1138, 'First Bank of Tokelau', 'TKBNK', 'oceania', 'Tokelau', 'TOKETKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1139, 'Tokelau People\'s Bank', 'TKBNK', 'oceania', 'Tokelau', 'TOKETKXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1140, 'Wallis and Futuna National Bank', 'WFBNK', 'oceania', 'Wallis and Futuna', 'WALLWFXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1141, 'Wallis and Futuna Commercial Bank', 'WFBNK', 'oceania', 'Wallis and Futuna', 'WALLWFXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1142, 'Wallis and Futuna Development Bank', 'WFBNK', 'oceania', 'Wallis and Futuna', 'WALLWFXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1143, 'First Bank of Wallis and Futuna', 'WFBNK', 'oceania', 'Wallis and Futuna', 'WALLWFXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1144, 'Wallis and Futuna People\'s Bank', 'WFBNK', 'oceania', 'Wallis and Futuna', 'WALLWFXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1145, 'Northern Mariana Islands National Bank', 'MPBNK', 'oceania', 'Northern Mariana Islands', 'NORTMPXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1146, 'Northern Mariana Islands Commercial Bank', 'MPBNK', 'oceania', 'Northern Mariana Islands', 'NORTMPXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1147, 'Northern Mariana Islands Development Bank', 'MPBNK', 'oceania', 'Northern Mariana Islands', 'NORTMPXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1148, 'First Bank of Northern Mariana Islands', 'MPBNK', 'oceania', 'Northern Mariana Islands', 'NORTMPXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1149, 'Northern Mariana Islands People\'s Bank', 'MPBNK', 'oceania', 'Northern Mariana Islands', 'NORTMPXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1150, 'Pitcairn Islands National Bank', 'PNBNK', 'oceania', 'Pitcairn Islands', 'PITCPNXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1151, 'Pitcairn Islands Commercial Bank', 'PNBNK', 'oceania', 'Pitcairn Islands', 'PITCPNXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1152, 'Pitcairn Islands Development Bank', 'PNBNK', 'oceania', 'Pitcairn Islands', 'PITCPNXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1153, 'First Bank of Pitcairn Islands', 'PNBNK', 'oceania', 'Pitcairn Islands', 'PITCPNXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1154, 'Pitcairn Islands People\'s Bank', 'PNBNK', 'oceania', 'Pitcairn Islands', 'PITCPNXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1155, 'Norfolk Island National Bank', 'NFBNK', 'oceania', 'Norfolk Island', 'NORFNFXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1156, 'Norfolk Island Commercial Bank', 'NFBNK', 'oceania', 'Norfolk Island', 'NORFNFXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1157, 'Norfolk Island Development Bank', 'NFBNK', 'oceania', 'Norfolk Island', 'NORFNFXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1158, 'First Bank of Norfolk Island', 'NFBNK', 'oceania', 'Norfolk Island', 'NORFNFXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1159, 'Norfolk Island People\'s Bank', 'NFBNK', 'oceania', 'Norfolk Island', 'NORFNFXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1160, 'Iran National Bank', 'IRBNK', 'middle-east', 'Iran', 'IRANIRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1161, 'Iran Commercial Bank', 'IRBNK', 'middle-east', 'Iran', 'IRANIRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1162, 'Iran Development Bank', 'IRBNK', 'middle-east', 'Iran', 'IRANIRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1163, 'First Bank of Iran', 'IRBNK', 'middle-east', 'Iran', 'IRANIRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1164, 'Iran People\'s Bank', 'IRBNK', 'middle-east', 'Iran', 'IRANIRXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1165, 'Iraq National Bank', 'IQBNK', 'middle-east', 'Iraq', 'IRAQIQXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1166, 'Iraq Commercial Bank', 'IQBNK', 'middle-east', 'Iraq', 'IRAQIQXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1167, 'Iraq Development Bank', 'IQBNK', 'middle-east', 'Iraq', 'IRAQIQXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1168, 'First Bank of Iraq', 'IQBNK', 'middle-east', 'Iraq', 'IRAQIQXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1169, 'Iraq People\'s Bank', 'IQBNK', 'middle-east', 'Iraq', 'IRAQIQXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1170, 'Yemen National Bank', 'YEBNK', 'middle-east', 'Yemen', 'YEMEYEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1171, 'Yemen Commercial Bank', 'YEBNK', 'middle-east', 'Yemen', 'YEMEYEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1172, 'Yemen Development Bank', 'YEBNK', 'middle-east', 'Yemen', 'YEMEYEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1173, 'First Bank of Yemen', 'YEBNK', 'middle-east', 'Yemen', 'YEMEYEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1174, 'Yemen People\'s Bank', 'YEBNK', 'middle-east', 'Yemen', 'YEMEYEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1175, 'Syria National Bank', 'SYBNK', 'middle-east', 'Syria', 'SYRISYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1176, 'Syria Commercial Bank', 'SYBNK', 'middle-east', 'Syria', 'SYRISYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1177, 'Syria Development Bank', 'SYBNK', 'middle-east', 'Syria', 'SYRISYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1178, 'First Bank of Syria', 'SYBNK', 'middle-east', 'Syria', 'SYRISYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1179, 'Syria People\'s Bank', 'SYBNK', 'middle-east', 'Syria', 'SYRISYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1180, 'Palestine National Bank', 'PSBNK', 'middle-east', 'Palestine', 'PALEPSXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1181, 'Palestine Commercial Bank', 'PSBNK', 'middle-east', 'Palestine', 'PALEPSXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1182, 'Palestine Development Bank', 'PSBNK', 'middle-east', 'Palestine', 'PALEPSXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1183, 'First Bank of Palestine', 'PSBNK', 'middle-east', 'Palestine', 'PALEPSXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1184, 'Palestine People\'s Bank', 'PSBNK', 'middle-east', 'Palestine', 'PALEPSXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1185, 'Qatar Commercial Bank', 'QABNK', 'middle-east', 'Qatar', 'QATAQAXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1186, 'Qatar Development Bank', 'QABNK', 'middle-east', 'Qatar', 'QATAQAXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1187, 'Kuwait National Bank', 'KWBNK', 'middle-east', 'Kuwait', 'KUWAKWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1188, 'Kuwait Commercial Bank', 'KWBNK', 'middle-east', 'Kuwait', 'KUWAKWXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1189, 'Bahrain National Bank', 'BHBNK', 'middle-east', 'Bahrain', 'BAHRBHXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1190, 'Bahrain Commercial Bank', 'BHBNK', 'middle-east', 'Bahrain', 'BAHRBHXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1191, 'Bahrain Development Bank', 'BHBNK', 'middle-east', 'Bahrain', 'BAHRBHXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1192, 'Jordan National Bank', 'JOBNK', 'middle-east', 'Jordan', 'JORDJOXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1193, 'Jordan Commercial Bank', 'JOBNK', 'middle-east', 'Jordan', 'JORDJOXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1194, 'Jordan Development Bank', 'JOBNK', 'middle-east', 'Jordan', 'JORDJOXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1195, 'First Bank of Jordan', 'JOBNK', 'middle-east', 'Jordan', 'JORDJOXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1196, 'Lebanon National Bank', 'LBBNK', 'middle-east', 'Lebanon', 'LEBALBXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1197, 'Lebanon Commercial Bank', 'LBBNK', 'middle-east', 'Lebanon', 'LEBALBXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1198, 'Lebanon Development Bank', 'LBBNK', 'middle-east', 'Lebanon', 'LEBALBXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1199, 'First Bank of Lebanon', 'LBBNK', 'middle-east', 'Lebanon', 'LEBALBXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1200, 'Lebanon People\'s Bank', 'LBBNK', 'middle-east', 'Lebanon', 'LEBALBXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1201, 'Israel National Bank', 'ILBNK', 'middle-east', 'Israel', 'ISRAILXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1202, 'Cyprus National Bank', 'CYBNK', 'middle-east', 'Cyprus', 'CYPRCYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1203, 'Cyprus Commercial Bank', 'CYBNK', 'middle-east', 'Cyprus', 'CYPRCYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1204, 'Cyprus Development Bank', 'CYBNK', 'middle-east', 'Cyprus', 'CYPRCYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1205, 'First Bank of Cyprus', 'CYBNK', 'middle-east', 'Cyprus', 'CYPRCYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1206, 'Cyprus People\'s Bank', 'CYBNK', 'middle-east', 'Cyprus', 'CYPRCYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1207, 'Afghanistan National Bank', 'AFBNK', 'middle-east', 'Afghanistan', 'AFGHAFXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1208, 'Afghanistan Commercial Bank', 'AFBNK', 'middle-east', 'Afghanistan', 'AFGHAFXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1209, 'Afghanistan Development Bank', 'AFBNK', 'middle-east', 'Afghanistan', 'AFGHAFXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1210, 'First Bank of Afghanistan', 'AFBNK', 'middle-east', 'Afghanistan', 'AFGHAFXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1211, 'Afghanistan People\'s Bank', 'AFBNK', 'middle-east', 'Afghanistan', 'AFGHAFXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1212, 'Armenia National Bank', 'AMBNK', 'middle-east', 'Armenia', 'ARMEAMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1213, 'Armenia Commercial Bank', 'AMBNK', 'middle-east', 'Armenia', 'ARMEAMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1214, 'Armenia Development Bank', 'AMBNK', 'middle-east', 'Armenia', 'ARMEAMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1215, 'First Bank of Armenia', 'AMBNK', 'middle-east', 'Armenia', 'ARMEAMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1216, 'Armenia People\'s Bank', 'AMBNK', 'middle-east', 'Armenia', 'ARMEAMXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1217, 'Azerbaijan National Bank', 'AZBNK', 'middle-east', 'Azerbaijan', 'AZERAZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23');
INSERT INTO `banks` (`id`, `name`, `code`, `region`, `country`, `swift_code`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(1218, 'Azerbaijan Commercial Bank', 'AZBNK', 'middle-east', 'Azerbaijan', 'AZERAZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1219, 'Azerbaijan Development Bank', 'AZBNK', 'middle-east', 'Azerbaijan', 'AZERAZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1220, 'First Bank of Azerbaijan', 'AZBNK', 'middle-east', 'Azerbaijan', 'AZERAZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1221, 'Azerbaijan People\'s Bank', 'AZBNK', 'middle-east', 'Azerbaijan', 'AZERAZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1222, 'Georgia National Bank', 'GEBNK', 'middle-east', 'Georgia', 'GEORGEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1223, 'Georgia Commercial Bank', 'GEBNK', 'middle-east', 'Georgia', 'GEORGEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1224, 'Georgia Development Bank', 'GEBNK', 'middle-east', 'Georgia', 'GEORGEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1225, 'First Bank of Georgia', 'GEBNK', 'middle-east', 'Georgia', 'GEORGEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1226, 'Georgia People\'s Bank', 'GEBNK', 'middle-east', 'Georgia', 'GEORGEXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1227, 'Egypt National Bank', 'EGBNK', 'middle-east', 'Egypt', 'EGYPEGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1228, 'Egypt Commercial Bank', 'EGBNK', 'middle-east', 'Egypt', 'EGYPEGXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1229, 'Libya National Bank', 'LYBNK', 'middle-east', 'Libya', 'LIBYLYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1230, 'Libya Commercial Bank', 'LYBNK', 'middle-east', 'Libya', 'LIBYLYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1231, 'Libya Development Bank', 'LYBNK', 'middle-east', 'Libya', 'LIBYLYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1232, 'First Bank of Libya', 'LYBNK', 'middle-east', 'Libya', 'LIBYLYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1233, 'Libya People\'s Bank', 'LYBNK', 'middle-east', 'Libya', 'LIBYLYXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1234, 'Algeria National Bank', 'DZBNK', 'middle-east', 'Algeria', 'ALGEDZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1235, 'Algeria Commercial Bank', 'DZBNK', 'middle-east', 'Algeria', 'ALGEDZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1236, 'Algeria Development Bank', 'DZBNK', 'middle-east', 'Algeria', 'ALGEDZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1237, 'First Bank of Algeria', 'DZBNK', 'middle-east', 'Algeria', 'ALGEDZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1238, 'Algeria People\'s Bank', 'DZBNK', 'middle-east', 'Algeria', 'ALGEDZXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1239, 'Tunisia National Bank', 'TNBNK', 'middle-east', 'Tunisia', 'TUNITNXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1240, 'Tunisia Commercial Bank', 'TNBNK', 'middle-east', 'Tunisia', 'TUNITNXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1241, 'Tunisia Development Bank', 'TNBNK', 'middle-east', 'Tunisia', 'TUNITNXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1242, 'First Bank of Tunisia', 'TNBNK', 'middle-east', 'Tunisia', 'TUNITNXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1243, 'Tunisia People\'s Bank', 'TNBNK', 'middle-east', 'Tunisia', 'TUNITNXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1244, 'Morocco National Bank', 'MABNK', 'middle-east', 'Morocco', 'MOROMAXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1245, 'Morocco Commercial Bank', 'MABNK', 'middle-east', 'Morocco', 'MOROMAXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1246, 'Morocco Development Bank', 'MABNK', 'middle-east', 'Morocco', 'MOROMAXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1247, 'Sudan National Bank', 'SDBNK', 'middle-east', 'Sudan', 'SUDASDXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1248, 'Sudan Commercial Bank', 'SDBNK', 'middle-east', 'Sudan', 'SUDASDXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1249, 'Sudan Development Bank', 'SDBNK', 'middle-east', 'Sudan', 'SUDASDXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1250, 'First Bank of Sudan', 'SDBNK', 'middle-east', 'Sudan', 'SUDASDXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23'),
(1251, 'Sudan People\'s Bank', 'SDBNK', 'middle-east', 'Sudan', 'SUDASDXX', 1, NULL, '2026-08-27 14:35:23', '2026-08-27 14:35:23');

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

--
-- Dumping data for table `cards`
--

INSERT INTO `cards` (`id`, `user_id`, `account_id`, `card_number`, `card_type`, `card_name`, `cvv`, `expiry_date`, `credit_limit`, `available_credit`, `balance`, `billing_cycle`, `is_virtual`, `is_single_use`, `status`, `daily_limit`, `monthly_limit`, `pin_hash`, `last_used`, `created_at`, `updated_at`, `expires_at`, `metadata`) VALUES
(39, 153, 147, 'eUdpem5tRW1KN2ZZNG4rT015dDJ4Qi93WGx0OE5uL3dwbGxGVFlLMTc1az06OlmVOpM2CZAtBuG6zYP7WBY=', 'debit', 'My shopping card', 'RCt6cDRKSG9Mc1U4UlZBTnc4S1FRUT09OjqiUw8nHNIw3o5wnsGSISsm', '2029-08-24', 0.00, 0.00, 0.00, 1, 1, 0, 'pending', 5000.00, 50000.00, NULL, NULL, '2026-08-25 03:35:54', '2026-08-25 03:35:54', NULL, NULL);

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
(149, 154, 'f828a1bc31fbb9dfaf3690155991970c5c69a0b2e5d52ce70d9399fbbc0a8d71', '2026-08-26 12:35:00', 1, '2026-08-25 12:35:00');

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
(4, 'USD', 'EUR', 0.8572, '2026-08-25 23:54:53'),
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
(39, 'USD', 'CNY', 6.7370, '2026-08-26 02:56:15'),
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
(423, 'CAD', 'AED', 2.6630, '2026-05-31 02:46:19'),
(431, 'EGP', 'EUR', 0.0170, '2026-08-26 00:17:12'),
(432, 'EGP', 'USD', 0.0197, '2026-08-25 23:59:29');

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
(45, 153, 'individual', 'Henry Stevens', '1971-03-25', NULL, 'Ggh', 'Cvv', 'Vvbb', 'United Kingdom', 'Ffv', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'verified', 151, '2026-08-25 02:45:48', NULL, 'Account verified by admin during user creation - no documents required', '2026-08-25 02:45:48', '2026-08-25 02:45:48'),
(46, 154, 'individual', 'Joe Anderson', '2004-08-28', 'RmhvL1hCTnIxSkJRbTRVM3E1akkrQT09OjpsmUIjjFHm0Ml8GdqfNhNb', '455fcct', 'Ttffc', 'London', 'England', '55dddfg', 'state_id', '446666666', '2026-08-19', '2026-08-14', 'Miami', 'United States', 'kyc/6a8d8ee698804_1787662054_IMG_20260823_182609_344.jpg', 'kyc/6a8d8ee6999c5_1787662054_IMG_20260823_182609_344.jpg', 'kyc/6a8d8ee69a942_1787662054_IMG_20260823_182609_344.jpg', 'kyc/6a8d8ee69b8c9_1787662054_IMG_20260823_182609_344.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'From work', 'Work', NULL, 'verified', 151, '2026-08-25 12:50:09', NULL, 'KYC APPROVED', '2026-08-25 12:47:34', '2026-08-25 12:50:09'),
(47, 155, 'individual', 'Andrew Jerry', '1980-07-15', NULL, '27 Maple Court, Kensington, London W8 6AB, United Kingdom', 'London', 'Greater', 'United Kingdom', 'GT34DF', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'verified', 153, '2026-08-26 04:58:19', NULL, 'Account verified by admin during user creation - no documents required', '2026-08-26 04:58:19', '2026-08-26 04:58:19'),
(48, 156, 'individual', 'Padmini Silwa', '1972-01-27', NULL, 'Sri Lanka', 'Sri Lanka', 'Sri Lanka', 'Sri Lanka', 'SRI45TT', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'verified', 155, '2026-08-26 12:00:16', NULL, 'Account verified by admin during user creation - no documents required', '2026-08-26 12:00:16', '2026-08-26 12:00:16'),
(49, 157, 'individual', 'Benjamin Edward', '1969-10-13', NULL, 'Downhill', 'UK', 'UK', 'United Kingdom', '455865', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'verified', 151, '2026-08-26 12:30:50', NULL, 'Account verified by admin during user creation - no documents required', '2026-08-26 12:30:50', '2026-08-26 12:30:50'),
(50, 158, 'individual', 'Okenwa Joe', '1988-08-26', NULL, 'Maintown', 'East London', 'Maintown', 'United Kingdom', '53328uu', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'verified', 151, '2026-08-26 12:35:56', NULL, 'Account verified by admin during user creation - no documents required', '2026-08-26 12:35:56', '2026-08-26 12:35:56'),
(51, 159, 'individual', 'Henry Edward', '1978-07-26', NULL, '48 Willow Crescent, London, SW16 4QH, United Kingdom', 'London', 'Greater London', 'United Kingdom', 'SW1A 1AA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'verified', 151, '2026-08-26 16:35:03', NULL, 'Account verified by admin during user creation - no documents required', '2026-08-26 16:35:03', '2026-08-26 16:35:03'),
(52, 160, 'individual', 'Henry Edward', '1978-07-26', NULL, '48 Willow Crescent, London, SW16 4QH, United Kingdom', 'London', 'Greater London', 'United Kingdom', 'SW1A 1AA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'verified', 151, '2026-08-26 21:18:24', NULL, 'Account verified by admin during user creation - no documents required', '2026-08-26 21:18:24', '2026-08-26 21:18:24');

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
(315, 'hkr.fred@outlook.com', '172.59.184.55', '2026-08-16 23:41:46'),
(318, 'ekwensu42@gmail.com', '154.227.129.31', '2026-08-25 03:33:32'),
(319, 'henryedwardahmed5@gmail.com', '154.227.129.31', '2026-08-25 23:58:21');

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
(163, 153, 'KYC Verification Approved', 'Your KYC verification has been approved. You now have full access to all banking services.', 'success', 0, '/profile/kyc', NULL, '2026-08-25 02:45:48'),
(164, 153, 'Card Application Submitted', 'Your debit card application has been submitted and is pending admin approval.', 'info', 0, '/card/view/39', NULL, '2026-08-25 03:35:54'),
(165, 154, 'KYC Verification Approved', 'Your KYC verification has been approved. You now have full access to all banking services.', 'success', 0, '/profile/kyc', NULL, '2026-08-25 12:50:09'),
(166, 155, 'KYC Verification Approved', 'Your KYC verification has been approved. You now have full access to all banking services.', 'success', 0, '/profile/kyc', NULL, '2026-08-26 04:58:19'),
(167, 156, 'KYC Verification Approved', 'Your KYC verification has been approved. You now have full access to all banking services.', 'success', 0, '/profile/kyc', NULL, '2026-08-26 12:00:16'),
(168, 157, 'KYC Verification Approved', 'Your KYC verification has been approved. You now have full access to all banking services.', 'success', 0, '/profile/kyc', NULL, '2026-08-26 12:30:50'),
(169, 158, 'KYC Verification Approved', 'Your KYC verification has been approved. You now have full access to all banking services.', 'success', 0, '/profile/kyc', NULL, '2026-08-26 12:35:56'),
(170, 159, 'KYC Verification Approved', 'Your KYC verification has been approved. You now have full access to all banking services.', 'success', 0, '/profile/kyc', NULL, '2026-08-26 16:35:03'),
(171, 160, 'KYC Verification Approved', 'Your KYC verification has been approved. You now have full access to all banking services.', 'success', 0, '/profile/kyc', NULL, '2026-08-26 21:18:24');

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
(1, 'site_name', 'Western Vault Int Bank', 'string', 'Website name displayed throughout the site', 157, '2025-10-08 22:44:52', '2026-08-26 13:15:10'),
(2, 'site_url', 'https://western.vaultibk.com', 'string', 'Website URL', 157, '2025-10-08 22:44:52', '2026-08-26 13:15:10'),
(3, 'site_email', 'western@vaultibk.com', 'string', 'Primary contact email', 157, '2025-10-08 22:44:52', '2026-08-26 13:15:10'),
(4, 'default_currency', 'USD', 'string', 'Default system currency', 157, '2025-10-08 22:44:52', '2026-08-26 13:15:10'),
(5, 'min_transfer_amount', '50', 'number', 'Minimum transfer amount', 157, '2025-10-08 22:44:52', '2026-08-26 13:15:10'),
(6, 'max_transfer_amount', '100000000000', 'number', 'Maximum transfer amount per transaction', 157, '2025-10-08 22:44:52', '2026-08-26 13:15:10'),
(7, 'transfer_fee_domestic', '0', 'number', 'Domestic transfer fee (deprecated)', 157, '2025-10-08 22:44:52', '2026-08-26 13:15:10'),
(8, 'transfer_fee_international', '0.5', 'number', 'International transfer fee (deprecated)', 157, '2025-10-08 22:44:52', '2026-08-26 13:15:10'),
(9, 'interest_rate_savings', '2.5', 'number', 'Savings account interest rate percentage', 157, '2025-10-08 22:44:52', '2026-08-26 13:15:10'),
(10, 'maintenance_mode', '0', 'boolean', 'Enable maintenance mode', 157, '2025-10-08 22:44:52', '2026-08-26 13:15:10'),
(11, 'require_kyc', '1', 'boolean', 'Require KYC verification', 157, '2025-10-08 22:44:52', '2026-08-26 13:15:10'),
(12, 'two_factor_required', '1', 'boolean', 'Suggest 2FA for users (informational). Does not lock users out of the app when 2FA is disabled.', 157, '2025-10-08 22:44:52', '2026-08-26 13:15:10'),
(13, 'allow_new_registrations', '1', 'boolean', 'Enable/disable new user registrations', 157, '2025-10-08 22:44:52', '2026-08-26 13:15:10'),
(14, 'loan_service_enabled', '1', 'boolean', 'Enable/disable loan applications', 157, '2025-10-08 22:44:52', '2026-08-26 13:15:10'),
(15, 'card_service_enabled', '1', 'boolean', 'Enable/disable card requests', 157, '2025-10-08 22:44:52', '2026-08-26 13:15:10'),
(16, 'maintenance_message', 'System maintenance in progress', 'string', 'Maintenance mode message', 157, '2025-10-08 22:44:52', '2026-08-26 13:15:10'),
(17, 'max_daily_transfer_amount', '50000', 'number', 'Maximum daily transfer amount per user', 157, '2025-10-08 22:44:52', '2026-08-26 13:15:10'),
(18, 'max_transaction_amount', '10000000', 'number', 'Maximum single transaction amount', 157, '2025-10-08 22:44:52', '2026-08-26 13:15:10'),
(19, 'kyc_required_for_transfer', '1', 'boolean', 'Require KYC verification for transfers', 157, '2025-10-08 22:44:52', '2026-08-26 13:15:10'),
(20, 'auto_flag_large_transactions', '0', 'boolean', 'Auto-flag transactions over threshold', 157, '2025-10-08 22:44:52', '2026-08-26 13:15:10'),
(21, 'large_transaction_threshold', '10000', 'number', 'Amount threshold for flagging', 157, '2025-10-08 22:44:52', '2026-08-26 13:15:10'),
(25, 'bank_operating_country', 'Dominican Republic', 'string', 'Country where the bank operates', 157, '2025-10-14 00:00:00', '2026-08-26 13:15:10'),
(26, 'bank_operating_region', 'north-america', 'string', 'Region where the bank operates', 157, '2025-10-14 00:00:00', '2026-08-26 13:15:10'),
(27, 'site_logo_url', 'https://western.vaultibk.com/uploads/branding/site-logo.png?v=1787622347', 'string', 'URL to site logo image', 157, '2025-10-14 00:00:00', '2026-08-26 13:15:10'),
(28, 'site_tagline', 'Your Trusted Banking Partner', 'string', 'Site tagline/slogan', 157, '2025-10-14 00:00:00', '2026-08-26 13:15:10'),
(29, 'site_description', 'Secure online banking with 24/7 access to your accounts', 'string', 'Site description for SEO', 157, '2025-10-14 00:00:00', '2026-08-26 13:15:10'),
(30, 'support_phone', '+44882769***', 'string', 'Customer support phone number', 157, '2025-10-14 00:00:00', '2026-08-26 13:15:10'),
(31, 'support_hours', 'Monday - Friday, 8:00 AM - 6:00 PM EST', 'string', 'Customer support hours', 157, '2025-10-14 00:00:00', '2026-08-26 13:15:10'),
(32, 'bank_address', '2015 Northwest Hwy, Garland, TX 75041, London, United Kingdom ', 'string', 'Physical bank address', 157, '2025-10-14 00:00:00', '2026-08-26 13:15:10'),
(34, 'interest_rate_checking', '0', 'number', 'Checking account interest rate percentage', 157, '2025-10-14 00:00:00', '2026-08-26 13:15:10'),
(35, 'overdraft_fee', '35', 'number', 'Overdraft fee amount', 157, '2025-10-14 00:00:00', '2026-08-26 13:15:10'),
(36, 'monthly_maintenance_fee', '0', 'number', 'Monthly account maintenance fee', 157, '2025-10-14 00:00:00', '2026-08-26 13:15:10'),
(37, 'require_transfer_pin', '1', 'boolean', 'Require Transfer PIN for transactions', 157, '2025-10-14 00:00:00', '2026-08-26 13:15:10'),
(38, 'max_login_attempts', '10', 'number', 'Maximum failed login attempts before lockout', 157, '2025-10-14 00:00:00', '2026-08-26 13:15:10'),
(39, 'login_lockout_duration', '30', 'number', 'Login lockout duration in minutes', 157, '2025-01-15 10:00:00', '2026-08-26 13:15:10'),
(40, 'session_timeout', '30', 'number', 'Session timeout in minutes', 157, '2025-10-14 00:00:00', '2026-08-26 13:15:10'),
(41, 'email_on_transfer', '1', 'boolean', 'Send email notification on transfers', 157, '2025-10-14 00:00:00', '2026-08-26 13:15:10'),
(42, 'email_on_login', '1', 'boolean', 'Send email notification on login', 157, '2025-10-14 00:00:00', '2026-08-26 13:15:10'),
(43, 'site_favicon_url', 'https://western.vaultibk.com/uploads/branding/favicon.png?v=1787622355', 'string', 'URL to site favicon', 157, '2025-10-14 00:00:00', '2026-08-26 13:15:10'),
(44, 'transfer_internal_fee', '0', 'number', 'Internal transfer fee percentage', 157, '2025-10-14 00:00:00', '2026-08-26 13:15:10'),
(45, 'transfer_domestic_fee', '0.5', 'number', 'Domestic transfer fee percentage', 157, '2025-10-14 00:00:00', '2026-08-26 13:15:10'),
(46, 'transfer_international_fee', '2.5', 'number', 'International transfer fee percentage', 157, '2025-10-14 00:00:00', '2026-08-26 13:15:10'),
(47, 'sms_on_transfer', '1', 'boolean', 'Send SMS notification on transfers', 157, '2025-10-14 00:00:00', '2026-08-26 13:15:10'),
(48, 'daily_limit_checking', '100000000000', 'number', 'Daily transaction limit for Checking accounts', 157, '2025-11-03 02:27:47', '2026-08-26 13:15:10'),
(49, 'daily_limit_savings', '10000000000', 'number', 'Daily transaction limit for Savings accounts', 157, '2025-11-03 02:27:47', '2026-08-26 13:15:10'),
(50, 'daily_limit_business', '10000000000', 'number', 'Daily transaction limit for Business accounts', 157, '2025-11-03 02:27:47', '2026-08-26 13:15:10'),
(51, 'monthly_limit_checking', '100000000000', 'number', 'Monthly transaction limit for Checking accounts', 157, '2025-11-03 02:27:47', '2026-08-26 13:15:10'),
(52, 'monthly_limit_savings', '100000000000', 'number', 'Monthly transaction limit for Savings accounts', 157, '2025-11-03 02:27:47', '2026-08-26 13:15:10'),
(53, 'monthly_limit_business', '100000000000', 'number', 'Monthly transaction limit for Business accounts', 157, '2025-11-03 02:27:47', '2026-08-26 13:15:10'),
(142, 'enable_currency_conversion', '1', 'boolean', 'Enable currency conversion. When enabled, users can view balances and amounts in their preferred currency. When disabled, all amounts are displayed in the site default currency.', 157, '2025-11-04 17:17:14', '2026-08-26 13:15:10'),
(414, 'disable_2fa_entirely', '0', 'boolean', 'Disable 2FA entirely for all users. When enabled, users cannot enable 2FA and existing 2FA will be disabled. This overrides the \"Force 2FA\" setting.', 157, '2026-02-10 02:55:45', '2026-08-26 13:15:10'),
(1415, 'force_security_setup', '1', 'boolean', 'When enabled, users must complete Login PIN and Transfer PIN (+ 2FA if required) before accessing the dashboard', 157, '2026-05-31 01:13:56', '2026-08-26 13:15:10'),
(1416, 'kyc_use_custom_fields', '0', 'boolean', 'Use custom admin-defined KYC fields instead of country profile defaults', 157, '2026-05-31 01:13:56', '2026-08-26 13:15:10'),
(1417, 'kyc_custom_fields', '[]', 'json', 'JSON array of custom KYC field definitions when kyc_use_custom_fields is enabled', 157, '2026-05-31 01:13:56', '2026-08-26 13:15:10'),
(1517, 'ledger_aligned_to_site_default', '1', 'boolean', 'Ledger balances converted to site default_currency using exchange_rates', 157, '2026-06-12 19:18:37', '2026-08-26 13:15:10'),
(1832, 'exchange_rate_api_key', '', 'string', 'ExchangeRate-API v6 API key for live FX rates. Leave empty to use cached or built-in offline fallback rates.', 157, '2026-08-26 03:28:32', '2026-08-26 13:15:10');

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
  `status` enum('pending','processing','successful','completed','failed','reversed','cancelled') DEFAULT 'pending',
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
(1889, 'ADM20260824225901464', 153, 147, 'credit', 'deposit', '', 10000.00, 'USD', 0.00, 10000.00, 'From Germany', '2455866533', 'Henry Stevens', 'Sanity Bank', 'completed', NULL, 0.00, NULL, '{\"admin_id\":151,\"reason\":\"Administrative adjustment\",\"method\":\"international\",\"method_fields\":{\"recipient_bank\":\"Sanity Bank\",\"recipient_account\":\"2455866533\",\"recipient_name\":\"Henry Stevens\"},\"admin_action\":true,\"display_amount\":10000,\"display_currency\":\"USD\",\"ledger_amount\":10000,\"ledger_currency\":\"USD\"}', '154.227.129.31', '2026-07-08 04:10:00', '2026-07-08 04:10:00'),
(1890, 'TXN6A8D08E60D295', 153, 147, 'debit', 'transfer', 'bonus', 5125.00, 'USD', 10000.00, 4875.00, 'International Wire Transfer to Ggvv at Ttyhv, Germany', 'Yygccc555', 'Ggvv', 'Ttyhv', 'completed', 'sepa', 125.00, NULL, '{\"transfer_scope\":\"international\",\"transfer_method\":\"sepa\",\"transfer_method_label\":\"SEPA\",\"country_code\":\"DE\",\"region\":\"europe\",\"country\":\"Germany\",\"bank_name\":\"Ttyhv\",\"account_number\":\"Yygccc555\",\"iban\":\"4455444677654345\",\"bic\":\"ABCDUD43XXX\",\"swift\":\"ABCDUD43XXX\",\"transaction_override\":\"normal\",\"failed_reason\":null,\"entry_amount\":5000,\"entry_currency\":\"USD\",\"entry_fee\":125,\"entry_total\":5125}', '154.227.129.31', '2026-08-25 03:15:50', '2026-08-25 03:15:50'),
(1891, 'TXN6A8D0F6A42659', 153, 147, 'debit', 'transfer', 'other', 1507.50, 'USD', 4875.00, 3367.50, 'Domestic Transfer to Ydtyh at Goldman Sachs Bank', '2554888888', 'Ydtyh', 'Goldman Sachs Bank', 'completed', 'ach', 7.50, NULL, '{\"transfer_scope\":\"domestic\",\"transfer_method\":\"ach\",\"transfer_method_label\":\"ACH\",\"country_code\":\"US\",\"bank_name\":\"Goldman Sachs Bank\",\"account_number\":\"2554888888\",\"routing_number\":\"244452355\",\"transaction_override\":\"normal\",\"failed_reason\":null,\"entry_amount\":1500,\"entry_currency\":\"USD\",\"entry_fee\":7.5,\"entry_total\":1507.5}', '154.227.129.31', '2026-08-25 03:43:38', '2026-08-25 03:43:38'),
(1892, 'TXN6A8D10F3EF433', 153, 147, 'debit', 'transfer', 'other', 1507.50, 'USD', 3367.50, 1860.00, 'Domestic Transfer to Steve Henry at Goldman Sachs Bank', '225548554', 'Steve Henry', 'Goldman Sachs Bank', 'completed', 'ach', 7.50, NULL, '{\"transfer_scope\":\"domestic\",\"transfer_method\":\"ach\",\"transfer_method_label\":\"ACH\",\"country_code\":\"US\",\"bank_name\":\"Goldman Sachs Bank\",\"account_number\":\"225548554\",\"routing_number\":\"447555888\",\"transaction_override\":\"normal\",\"failed_reason\":null,\"entry_amount\":1500,\"entry_currency\":\"USD\",\"entry_fee\":7.5,\"entry_total\":1507.5}', '154.227.129.31', '2026-08-25 03:50:11', '2026-08-25 03:50:11'),
(1893, 'REVTXN6A8D10F3EF433', 153, 147, 'credit', 'other', 'refund', 1507.50, 'USD', 1860.00, 3367.50, 'Reversal of transaction TXN6A8D10F3EF433', NULL, NULL, NULL, 'completed', NULL, 0.00, NULL, '{\"admin_action\":true,\"admin_id\":151,\"original_transaction_id\":1892,\"reversal_reason\":\"Admin reversal\"}', '154.227.129.31', '2026-08-25 04:12:06', '2026-08-25 04:12:06'),
(1895, 'TXN6A8E684B0E9B4', 153, 147, 'debit', 'transfer', 'other', 1758.63, 'USD', 3367.50, 1608.87, 'Domestic Transfer to Steve at DZ Bank', '25588888', 'Steve', 'DZ Bank', 'successful', 'local', 8.75, NULL, '{\"transfer_scope\":\"domestic\",\"transfer_method\":\"local\",\"transfer_method_label\":\"Local Transfer\",\"country_code\":\"DE\",\"bank_name\":\"DZ Bank\",\"account_number\":\"25588888\",\"transaction_override\":\"normal\",\"failed_reason\":null,\"entry_amount\":1500,\"entry_currency\":\"EUR\",\"entry_fee\":7.5,\"entry_total\":1507.5,\"ledger_currency\":\"USD\",\"exchange_rate\":1.1665888940737286}', '154.227.129.31', '2026-08-26 04:15:07', '2026-08-26 04:15:07'),
(1896, 'TXN6A8E6A9E8E980', 153, 147, 'debit', 'transfer', 'other', 1195.75, 'USD', 1608.87, 413.12, 'International Wire Transfer to Steve  Henry at Stevens Bank, Austria', '2255638', 'Steve  Henry', 'Stevens Bank', 'successful', 'sepa', 29.16, NULL, '{\"transfer_scope\":\"international\",\"transfer_method\":\"sepa\",\"transfer_method_label\":\"SEPA\",\"country_code\":\"AT\",\"region\":\"europe\",\"country\":\"Austria\",\"bank_name\":\"Stevens Bank\",\"account_number\":\"2255638\",\"iban\":\"237784447887754\",\"bic\":\"ABCDUS33XXX\",\"swift\":\"ABCDUS33XXX\",\"transaction_override\":\"normal\",\"failed_reason\":null,\"entry_amount\":1000,\"entry_currency\":\"EUR\",\"entry_fee\":25,\"entry_total\":1025,\"ledger_currency\":\"USD\",\"exchange_rate\":1.1665888940737286}', '154.227.129.31', '2026-08-26 04:25:02', '2026-08-26 04:25:02'),
(1897, 'ADM20260826011235103', 155, 149, 'credit', 'deposit', '', 359334.40, 'USD', 0.00, 359334.40, 'New deposit', '5665338653', 'James Jones', 'HGT Bank', 'completed', NULL, 0.00, NULL, '{\"admin_id\":151,\"reason\":\"Administrative adjustment\",\"method\":\"domestic\",\"method_fields\":{\"recipient_bank\":\"HGT Bank\",\"recipient_account\":\"5665338653\",\"recipient_name\":\"James Jones\"},\"admin_action\":true,\"display_amount\":268000,\"display_currency\":\"GBP\",\"ledger_amount\":359334.4,\"ledger_currency\":\"USD\"}', '154.227.129.31', '2026-08-05 05:20:00', '2026-08-05 05:20:00'),
(1898, 'ADM20260826013506580', 155, 149, 'debit', 'withdrawal', '', 18771.20, 'USD', 359334.40, 340563.20, 'Payment', '2411536587', 'Sandra Swift', 'National Bank', 'completed', NULL, 0.00, NULL, '{\"admin_id\":151,\"reason\":\"Administrative adjustment\",\"method\":\"domestic\",\"method_fields\":{\"recipient_bank\":\"National Bank\",\"recipient_account\":\"2411536587\",\"recipient_name\":\"Sandra Swift\"},\"admin_action\":true,\"display_amount\":14000,\"display_currency\":\"GBP\",\"ledger_amount\":18771.2,\"ledger_currency\":\"USD\"}', '154.227.129.31', '2026-08-14 02:40:00', '2026-08-14 02:40:00'),
(1899, 'ADM20260826013749500', 155, 149, 'credit', 'deposit', '', 884928.00, 'USD', 340563.20, 1225491.20, 'Transfer from Steve Henry  at Rainvains Bank', '4673865438', 'Steve Henry', 'Rainvains Bank', 'completed', NULL, 0.00, NULL, '{\"admin_id\":151,\"reason\":\"Administrative adjustment\",\"method\":\"domestic\",\"method_fields\":{\"recipient_bank\":\"Rainvains Bank\",\"recipient_account\":\"4673865438\",\"recipient_name\":\"Steve Henry\"},\"admin_action\":true,\"display_amount\":660000,\"display_currency\":\"GBP\",\"ledger_amount\":884928,\"ledger_currency\":\"USD\"}', '154.227.129.31', '2026-08-20 18:05:00', '2026-08-20 18:05:00'),
(1900, 'ADM20260826075046760', 155, 149, 'credit', 'deposit', '', 20112.00, 'USD', 1225491.20, 1245603.20, 'Transfer from Steve Henry  at City Bank', '2455365875', 'Steve Henry', 'City Bank', 'completed', NULL, 0.00, NULL, '{\"admin_id\":151,\"reason\":\"Administrative adjustment\",\"method\":\"domestic\",\"method_fields\":{\"recipient_bank\":\"City Bank\",\"recipient_account\":\"2455365875\",\"recipient_name\":\"Steve Henry\"},\"admin_action\":true,\"display_amount\":15000,\"display_currency\":\"GBP\",\"ledger_amount\":20112,\"ledger_currency\":\"USD\"}', '154.227.128.16', '2026-08-20 12:30:00', '2026-08-20 12:30:00'),
(1901, 'ADM20260826085728158', 157, 151, 'credit', 'deposit', '', 2145280.00, 'USD', 0.00, 2145280.00, 'Transfer from Mint mobile  at SSDT', '24456578822', 'Mint mobile', 'SSDT', 'completed', NULL, 0.00, NULL, '{\"admin_id\":151,\"reason\":\"Administrative adjustment\",\"method\":\"domestic\",\"method_fields\":{\"recipient_bank\":\"SSDT\",\"recipient_account\":\"24456578822\",\"recipient_name\":\"Mint mobile\"},\"admin_action\":true,\"display_amount\":1600000,\"display_currency\":\"GBP\",\"ledger_amount\":2145280,\"ledger_currency\":\"USD\"}', '154.227.128.16', '2026-08-05 16:24:00', '2026-08-05 16:24:00'),
(1902, 'ADM20260826090413214', 157, 151, 'debit', 'withdrawal', '', 825932.80, 'USD', 2145280.00, 1319347.20, 'Domestic Transfer to Steve Henry  at Western Vault', '2114536698', 'Steve Henry', 'Western Vault', 'completed', NULL, 0.00, NULL, '{\"admin_id\":151,\"reason\":\"Administrative adjustment\",\"method\":\"domestic\",\"method_fields\":{\"recipient_bank\":\"Western Vault\",\"recipient_account\":\"2114536698\",\"recipient_name\":\"Steve Henry\"},\"admin_action\":true,\"display_amount\":616000,\"display_currency\":\"GBP\",\"ledger_amount\":825932.8,\"ledger_currency\":\"USD\"}', '154.227.128.16', '2026-08-07 12:25:00', '2026-08-07 12:25:00'),
(1903, 'ADM20260826090714666', 157, 151, 'credit', 'deposit', '', 2078240.00, 'USD', 1319347.20, 3397587.20, 'Transfer from Benjamin  at Western Vault', '245516369875', 'Benjamin', 'Western Vault', 'completed', NULL, 0.00, NULL, '{\"admin_id\":151,\"reason\":\"Administrative adjustment\",\"method\":\"domestic\",\"method_fields\":{\"recipient_bank\":\"Western Vault\",\"recipient_account\":\"245516369875\",\"recipient_name\":\"Benjamin\"},\"admin_action\":true,\"display_amount\":1550000,\"display_currency\":\"GBP\",\"ledger_amount\":2078240,\"ledger_currency\":\"USD\"}', '154.227.128.16', '2026-08-15 08:04:00', '2026-08-15 08:04:00'),
(1904, 'ADM20260826092040453', 157, 151, 'credit', 'deposit', '', 938560.00, 'USD', 3397587.20, 4336147.20, 'Transfer from Benjamin  at Western Vault', '2454386534', 'Benjamin', 'Western Vault', 'completed', NULL, 0.00, NULL, '{\"admin_id\":151,\"reason\":\"Administrative adjustment\",\"method\":\"domestic\",\"method_fields\":{\"recipient_bank\":\"Western Vault\",\"recipient_account\":\"2454386534\",\"recipient_name\":\"Benjamin\"},\"admin_action\":true,\"display_amount\":700000,\"display_currency\":\"GBP\",\"ledger_amount\":938560,\"ledger_currency\":\"USD\"}', '154.227.128.16', '2026-08-18 05:40:00', '2026-08-18 05:40:00'),
(1905, 'ADM20260826092729935', 157, 151, 'debit', 'withdrawal', '', 667718.40, 'USD', 4336147.20, 3668428.80, 'Domestic Transfer to Edward  at SSB', '5676328876', 'Edward', 'SSB', 'completed', NULL, 0.00, NULL, '{\"admin_id\":151,\"reason\":\"Administrative adjustment\",\"method\":\"domestic\",\"method_fields\":{\"recipient_bank\":\"SSB\",\"recipient_account\":\"5676328876\",\"recipient_name\":\"Edward\"},\"admin_action\":true,\"display_amount\":498000,\"display_currency\":\"GBP\",\"ledger_amount\":667718.4,\"ledger_currency\":\"USD\"}', '154.227.128.16', '2026-08-18 11:15:00', '2026-08-18 11:15:00'),
(1906, 'ADM20260826125052501', 159, 153, 'credit', 'deposit', '', 1139680.00, 'USD', 0.00, 1139680.00, 'Transfer from Steve Frank  at SSTB Bank', '16638765383', 'Steve Frank', 'SSTB Bank', 'completed', NULL, 0.00, NULL, '{\"admin_id\":151,\"reason\":\"Administrative adjustment\",\"method\":\"domestic\",\"method_fields\":{\"recipient_bank\":\"SSTB Bank\",\"recipient_account\":\"16638765383\",\"recipient_name\":\"Steve Frank\"},\"admin_action\":true,\"display_amount\":850000,\"display_currency\":\"GBP\",\"ledger_amount\":1139680,\"ledger_currency\":\"USD\"}', '154.227.128.16', '2026-08-05 06:10:00', '2026-08-05 06:10:00'),
(1907, 'ADM20260826125615178', 159, 153, 'debit', 'withdrawal', '', 30838.40, 'USD', 1139680.00, 1108841.60, 'Domestic Transfer to Henry Edward  at Western Vault', '202657736088', 'Henry Edward', 'Western Vault', 'completed', NULL, 0.00, NULL, '{\"admin_id\":151,\"reason\":\"Administrative adjustment\",\"method\":\"domestic\",\"method_fields\":{\"recipient_bank\":\"Western Vault\",\"recipient_account\":\"202657736088\",\"recipient_name\":\"Henry Edward\"},\"admin_action\":true,\"display_amount\":23000,\"display_currency\":\"GBP\",\"ledger_amount\":30838.4,\"ledger_currency\":\"USD\"}', '154.227.128.16', '2026-08-10 10:05:00', '2026-08-10 10:05:00'),
(1908, 'ADM20260826125720907', 159, 153, 'debit', 'withdrawal', '', 20112.00, 'USD', 1108841.60, 1088729.60, 'Domestic Transfer to Henry Edward  at Western Vault', '202657736088', 'Henry Edward', 'Western Vault', 'completed', NULL, 0.00, NULL, '{\"admin_id\":151,\"reason\":\"Administrative adjustment\",\"method\":\"domestic\",\"method_fields\":{\"recipient_bank\":\"Western Vault\",\"recipient_account\":\"202657736088\",\"recipient_name\":\"Henry Edward\"},\"admin_action\":true,\"display_amount\":15000,\"display_currency\":\"GBP\",\"ledger_amount\":20112,\"ledger_currency\":\"USD\"}', '154.227.128.16', '2026-08-17 19:56:00', '2026-08-17 19:56:00'),
(1909, 'ADM20260826131440556', 159, 153, 'debit', 'withdrawal', '', 20112.00, 'USD', 1088729.60, 1068617.60, 'Domestic Transfer to Henry Edward  at Western Vault', '202657736088', 'Henry Edward', 'Western Vault', 'completed', NULL, 0.00, NULL, '{\"admin_id\":151,\"reason\":\"Administrative adjustment\",\"method\":\"domestic\",\"method_fields\":{\"recipient_bank\":\"Western Vault\",\"recipient_account\":\"202657736088\",\"recipient_name\":\"Henry Edward\"},\"admin_action\":true,\"display_amount\":15000,\"display_currency\":\"GBP\",\"ledger_amount\":20112,\"ledger_currency\":\"USD\"}', '154.227.128.16', '2026-08-19 05:05:00', '2026-08-19 05:05:00'),
(1910, 'ADM20260826132619559', 159, 153, 'credit', 'deposit', '', 1072640.00, 'USD', 1068617.60, 2141257.60, 'Transfer from Henry Edward  at Western Vault', '202657736088', 'Henry Edward', 'Western Vault', 'completed', NULL, 0.00, NULL, '{\"admin_id\":151,\"reason\":\"Administrative adjustment\",\"method\":\"domestic\",\"method_fields\":{\"recipient_bank\":\"Western Vault\",\"recipient_account\":\"202657736088\",\"recipient_name\":\"Henry Edward\"},\"admin_action\":true,\"display_amount\":800000,\"display_currency\":\"GBP\",\"ledger_amount\":1072640,\"ledger_currency\":\"USD\"}', '154.227.128.16', '2026-08-20 10:10:00', '2026-08-20 10:10:00'),
(1911, 'ADM20260826171951163', 160, 154, 'credit', 'deposit', '', 768000.00, 'USD', 0.00, 768000.00, 'Transfer from Sarah Swift  at National Bank', '3864345854', 'Sarah Swift', 'National Bank', 'completed', NULL, 0.00, NULL, '{\"admin_id\":151,\"reason\":\"Administrative adjustment\",\"method\":\"domestic\",\"method_fields\":{\"recipient_bank\":\"National Bank\",\"recipient_account\":\"3864345854\",\"recipient_name\":\"Sarah Swift\"},\"admin_action\":true,\"display_amount\":768000,\"display_currency\":\"USD\",\"ledger_amount\":768000,\"ledger_currency\":\"USD\"}', '154.227.128.16', '2026-08-05 14:30:00', '2026-08-05 14:30:00'),
(1912, 'ADM20260826172716682', 160, 154, 'debit', 'withdrawal', '', 10500.00, 'USD', 768000.00, 757500.00, 'Domestic Transfer to Silvester Steve  at SSC', '2396538884', 'Silvester Steve', 'SSC', 'completed', NULL, 0.00, NULL, '{\"admin_id\":151,\"reason\":\"Administrative adjustment\",\"method\":\"domestic\",\"method_fields\":{\"recipient_bank\":\"SSC\",\"recipient_account\":\"2396538884\",\"recipient_name\":\"Silvester Steve\"},\"admin_action\":true,\"display_amount\":10500,\"display_currency\":\"USD\",\"ledger_amount\":10500,\"ledger_currency\":\"USD\"}', '154.227.128.16', '2026-08-10 20:26:00', '2026-08-10 20:26:00'),
(1913, 'ADM20260826172850412', 160, 154, 'credit', 'deposit', '', 550000.00, 'USD', 757500.00, 1307500.00, 'Transfer from Henry Edward  at Western Vault', '202644066924', 'Henry Edward', 'Western Vault', 'completed', NULL, 0.00, NULL, '{\"admin_id\":151,\"reason\":\"Administrative adjustment\",\"method\":\"domestic\",\"method_fields\":{\"recipient_bank\":\"Western Vault\",\"recipient_account\":\"202644066924\",\"recipient_name\":\"Henry Edward\"},\"admin_action\":true,\"display_amount\":550000,\"display_currency\":\"USD\",\"ledger_amount\":550000,\"ledger_currency\":\"USD\"}', '154.227.128.16', '2026-08-16 05:30:00', '2026-08-16 05:30:00'),
(1914, 'ADM20260826173029776', 160, 154, 'debit', 'withdrawal', '', 5000.00, 'USD', 1307500.00, 1302500.00, 'Domestic Transfer to Emmanuel Andrews  at City bank', '4553886444', 'Emmanuel Andrews', 'City bank', 'completed', NULL, 0.00, NULL, '{\"admin_id\":151,\"reason\":\"Administrative adjustment\",\"method\":\"domestic\",\"method_fields\":{\"recipient_bank\":\"City bank\",\"recipient_account\":\"4553886444\",\"recipient_name\":\"Emmanuel Andrews\"},\"admin_action\":true,\"display_amount\":5000,\"display_currency\":\"USD\",\"ledger_amount\":5000,\"ledger_currency\":\"USD\"}', '154.227.128.16', '2026-08-19 17:28:00', '2026-08-19 17:28:00'),
(1915, 'ADM20260826173508127', 160, 154, 'credit', 'deposit', '', 200000.00, 'USD', 1302500.00, 1502500.00, 'Transfer from Henry Edward  at Western Vault', '202644066924', 'Henry Edward', 'Western Vault', 'completed', NULL, 0.00, NULL, '{\"admin_id\":151,\"reason\":\"Administrative adjustment\",\"method\":\"domestic\",\"method_fields\":{\"recipient_bank\":\"Western Vault\",\"recipient_account\":\"202644066924\",\"recipient_name\":\"Henry Edward\"},\"admin_action\":true,\"display_amount\":200000,\"display_currency\":\"USD\",\"ledger_amount\":200000,\"ledger_currency\":\"USD\"}', '154.227.128.16', '2026-08-19 09:25:00', '2026-08-19 09:25:00'),
(1916, 'ADM20260827085921326', 158, 152, 'credit', 'deposit', '', 332.03, 'USD', 0.00, 332.03, 'Transfer from Sahid Joseph  at State Bank of India', '33654876558', 'Sahid Joseph', 'State Bank of India', 'completed', NULL, 0.00, NULL, '{\"admin_id\":151,\"reason\":\"Administrative adjustment\",\"method\":\"domestic\",\"method_fields\":{\"recipient_bank\":\"State Bank of India\",\"recipient_account\":\"33654876558\",\"recipient_name\":\"Sahid Joseph\"},\"admin_action\":true,\"display_amount\":30000,\"display_currency\":\"INR\",\"ledger_amount\":332.03,\"ledger_currency\":\"USD\"}', '154.227.128.16', '2026-08-10 22:24:00', '2026-08-10 22:24:00'),
(1917, 'ADM20260827092307696', 158, 152, 'credit', 'deposit', '', 453.78, 'USD', 332.03, 785.81, 'Deposit', '3788297648', 'Steve Frank', 'SYC Bank', 'completed', NULL, 0.00, NULL, '{\"admin_id\":151,\"reason\":\"Administrative adjustment\",\"method\":\"domestic\",\"method_fields\":{\"recipient_bank\":\"SYC Bank\",\"recipient_account\":\"3788297648\",\"recipient_name\":\"Steve Frank\"},\"admin_action\":true,\"display_amount\":41000,\"display_currency\":\"INR\",\"ledger_amount\":453.78,\"ledger_currency\":\"USD\"}', '154.227.128.16', '2026-08-12 22:22:00', '2026-08-12 22:22:00'),
(1918, 'ADM20260827092428370', 158, 152, 'debit', 'withdrawal', '', 132.81, 'USD', 785.81, 653.00, 'Deposit', '4288543687', 'Okenwa', 'Western Vault', 'completed', NULL, 0.00, NULL, '{\"admin_id\":151,\"reason\":\"Administrative adjustment\",\"method\":\"domestic\",\"method_fields\":{\"recipient_bank\":\"Western Vault\",\"recipient_account\":\"4288543687\",\"recipient_name\":\"Okenwa\"},\"admin_action\":true,\"display_amount\":12000,\"display_currency\":\"INR\",\"ledger_amount\":132.81,\"ledger_currency\":\"USD\"}', '154.227.128.16', '2026-08-20 14:29:00', '2026-08-20 14:29:00'),
(1919, 'ADM20260827092945899', 158, 152, 'debit', 'withdrawal', '', 110.68, 'USD', 653.00, 542.32, 'Deposit', '1756754386', 'Joseph Jones', 'Swift Bank', 'completed', NULL, 0.00, NULL, '{\"admin_id\":151,\"reason\":\"Administrative adjustment\",\"method\":\"domestic\",\"method_fields\":{\"recipient_bank\":\"Swift Bank\",\"recipient_account\":\"1756754386\",\"recipient_name\":\"Joseph Jones\"},\"admin_action\":true,\"display_amount\":10000,\"display_currency\":\"INR\",\"ledger_amount\":110.68,\"ledger_currency\":\"USD\"}', '154.227.128.16', '2026-08-16 15:29:00', '2026-08-16 15:29:00'),
(1920, 'ADM20260827093100604', 158, 152, 'debit', 'withdrawal', '', 132.81, 'USD', 542.32, 409.51, 'Deposit', '36548764276', 'Joseph Jones', 'Swift Bank', 'completed', NULL, 0.00, NULL, '{\"admin_id\":151,\"reason\":\"Administrative adjustment\",\"method\":\"domestic\",\"method_fields\":{\"recipient_bank\":\"Swift Bank\",\"recipient_account\":\"36548764276\",\"recipient_name\":\"Joseph Jones\"},\"admin_action\":true,\"display_amount\":12000,\"display_currency\":\"INR\",\"ledger_amount\":132.81,\"ledger_currency\":\"USD\"}', '154.227.128.16', '2026-08-19 19:29:00', '2026-08-19 19:29:00'),
(1921, 'ADM20260827101623827', 153, 147, 'debit', 'withdrawal', 'insurance', 58.33, 'USD', 413.12, 354.79, 'fgfs', '3566*******24424', 'Elexir Shell BP', 'Wellsfargo', 'completed', NULL, 0.00, NULL, '{\"admin_id\":3,\"reason\":\"Administrative adjustment\",\"method\":\"domestic\",\"method_fields\":{\"recipient_bank\":\"Wellsfargo\",\"recipient_account\":\"3566*******24424\",\"recipient_name\":\"Elexir Shell BP\"},\"admin_action\":true,\"display_amount\":50,\"display_currency\":\"EUR\",\"ledger_amount\":58.33,\"ledger_currency\":\"USD\"}', '102.89.76.184', '2026-08-10 15:15:00', '2026-08-10 15:15:00'),
(1922, 'ADM20260827104710817', 158, 152, 'credit', 'deposit', '', 1106.78, 'USD', 409.51, 1516.29, 'Transfer from Frank Jones  at Swift Bank', '2456855544888', 'Frank Jones', 'Swift Bank', 'completed', NULL, 0.00, NULL, '{\"admin_id\":151,\"reason\":\"Administrative adjustment\",\"method\":\"domestic\",\"method_fields\":{\"recipient_bank\":\"Swift Bank\",\"recipient_account\":\"2456855544888\",\"recipient_name\":\"Frank Jones\"},\"admin_action\":true,\"display_amount\":100000,\"display_currency\":\"INR\",\"ledger_amount\":1106.78,\"ledger_currency\":\"USD\"}', '154.227.128.16', '2026-08-17 15:28:00', '2026-08-17 15:28:00'),
(1923, 'ADM20260827105110275', 158, 152, 'credit', 'deposit', '', 1106.78, 'USD', 1516.29, 2623.07, 'Deposit', '248764654', 'Joseph Jones', 'Swift Bank', 'completed', NULL, 0.00, NULL, '{\"admin_id\":151,\"reason\":\"Administrative adjustment\",\"method\":\"domestic\",\"method_fields\":{\"recipient_bank\":\"Swift Bank\",\"recipient_account\":\"248764654\",\"recipient_name\":\"Joseph Jones\"},\"admin_action\":true,\"display_amount\":100000,\"display_currency\":\"INR\",\"ledger_amount\":1106.78,\"ledger_currency\":\"USD\"}', '154.227.128.16', '2026-08-10 18:49:00', '2026-08-10 18:49:00'),
(1924, 'ADM20260827111703390', 158, 152, 'credit', 'deposit', 'bonus', 189.64, 'USD', 2623.07, 2812.71, 'Transfer from Uwa uwa at Swift Bank', '36548753576', 'Uwa uwa', 'Swift Bank', 'completed', NULL, 0.00, NULL, '{\"admin_id\":151,\"reason\":\"Administrative adjustment\",\"method\":\"domestic\",\"method_fields\":{\"recipient_bank\":\"Swift Bank\",\"recipient_account\":\"36548753576\",\"recipient_name\":\"Uwa uwa\"},\"admin_action\":true,\"display_amount\":12000,\"display_currency\":\"DOP\",\"ledger_amount\":189.64,\"ledger_currency\":\"USD\"}', '154.227.128.16', '2026-08-27 18:16:00', '2026-08-27 18:16:00'),
(1925, 'ADM20260827111950627', 158, 152, 'debit', 'withdrawal', 'bonus', 252.85, 'USD', 2812.71, 2559.86, 'Domestic Transfer to Uwa uwa  at Set up', '3678643365', 'Uwa uwa', 'Set up', 'completed', NULL, 0.00, NULL, '{\"admin_id\":151,\"reason\":\"Administrative adjustment\",\"method\":\"domestic\",\"method_fields\":{\"recipient_bank\":\"Set up\",\"recipient_account\":\"3678643365\",\"recipient_name\":\"Uwa uwa\"},\"admin_action\":true,\"display_amount\":16000,\"display_currency\":\"DOP\",\"ledger_amount\":252.85,\"ledger_currency\":\"USD\"}', '154.227.128.16', '2026-08-27 18:18:00', '2026-08-27 18:18:00'),
(1926, 'ADM20260827112158930', 158, 152, 'debit', 'withdrawal', '', 158.03, 'USD', 2559.86, 2401.83, 'Domestic Transfer to Uwa uwa  at Set up', '47787664556', 'Uwa uwa', 'Set up', 'completed', NULL, 0.00, NULL, '{\"admin_id\":151,\"reason\":\"Administrative adjustment\",\"method\":\"domestic\",\"method_fields\":{\"recipient_bank\":\"Set up\",\"recipient_account\":\"47787664556\",\"recipient_name\":\"Uwa uwa\"},\"admin_action\":true,\"display_amount\":10000,\"display_currency\":\"DOP\",\"ledger_amount\":158.03,\"ledger_currency\":\"USD\"}', '154.227.128.16', '2026-08-27 18:20:00', '2026-08-27 18:20:00');

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
(538, 153, '879337', 'email', 1, '2026-08-25 03:02:13', '2026-08-25 02:52:13', 'login'),
(539, 153, '282188', 'email', 1, '2026-08-25 03:25:21', '2026-08-25 03:15:21', 'transfer'),
(540, 153, '451410', 'email', 1, '2026-08-25 03:43:59', '2026-08-25 03:33:59', 'login'),
(541, 153, '013319', 'email', 1, '2026-08-25 03:47:36', '2026-08-25 03:37:36', 'login'),
(542, 153, '065035', 'email', 1, '2026-08-25 03:53:02', '2026-08-25 03:43:02', 'transfer'),
(543, 153, '067682', 'email', 1, '2026-08-25 03:59:47', '2026-08-25 03:49:47', 'transfer'),
(544, 153, '941652', 'email', 1, '2026-08-25 04:26:59', '2026-08-25 04:16:59', 'login'),
(545, 153, '414230', 'email', 0, '2026-08-25 12:37:19', '2026-08-25 12:27:19', 'login'),
(546, 154, '750713', 'email', 1, '2026-08-26 00:11:18', '2026-08-26 00:01:18', 'login'),
(547, 154, '341282', 'email', 0, '2026-08-26 04:17:32', '2026-08-26 04:07:32', 'login'),
(548, 159, '409142', 'email', 1, '2026-08-26 21:14:51', '2026-08-27 01:04:51', 'transfer'),
(549, 159, '671972', 'email', 1, '2026-08-26 21:23:16', '2026-08-27 01:13:16', 'transfer'),
(550, 159, '812751', 'email', 1, '2026-08-26 21:30:26', '2026-08-27 01:20:26', 'transfer'),
(551, 159, '719794', 'email', 0, '2026-08-26 21:34:01', '2026-08-27 01:24:01', 'transfer'),
(552, 160, '271729', 'email', 1, '2026-08-26 23:40:14', '2026-08-27 03:30:14', 'transfer'),
(553, 160, '070174', 'email', 0, '2026-08-26 23:42:36', '2026-08-27 03:32:36', 'transfer'),
(554, 158, '295340', 'email', 1, '2026-08-27 09:13:49', '2026-08-27 13:03:49', 'transfer'),
(555, 158, '240285', 'email', 1, '2026-08-27 09:15:34', '2026-08-27 13:05:34', 'transfer'),
(556, 158, '820972', 'email', 1, '2026-08-27 09:16:57', '2026-08-27 13:06:57', 'transfer'),
(557, 158, '563102', 'email', 1, '2026-08-27 11:05:26', '2026-08-27 14:55:26', 'transfer'),
(558, 158, '439788', 'email', 1, '2026-08-27 11:07:04', '2026-08-27 14:57:04', 'transfer'),
(559, 158, '224122', 'email', 1, '2026-08-27 11:08:29', '2026-08-27 14:58:29', 'transfer'),
(560, 158, '528192', 'email', 0, '2026-08-27 11:34:07', '2026-08-27 15:24:07', 'transfer');

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
(3, 'admin@demo.com', '$2y$12$vySF0Qua/n5QZwGl9qEFsupSmRsEkWaslKLGhtny.c1XlRyQq5mXC', 'Admin User', '+1234567891', '1985-01-01', NULL, '456 Admin Avenue', 'Admin City', 'Admin State', 'United States', '54321', NULL, 'admin', 1, 'active', 'verified', 0, NULL, NULL, 0, 'email', NULL, NULL, NULL, NULL, '2026-08-27 12:39:31', 1, 0, NULL, NULL, 'en', 'USD', 0.00, '$2y$10$Q1PjPMemugsGthLoGy37GOFdWdbAKDyk9P8cnGHw3iotKzcR3Iaa6', '$2y$10$ASwi5xJx4ax.EBuEkJVfr.wa15SBxxNbIMQ42fWKvYE/fGB25TATK', '$2y$10$bZlUWmGoHKLIMvACEDK1muZ.b7gCp3lTClANesOuPE1nT8ATEYsD6', 1, 'normal', '2025-10-08 22:44:52', '2026-08-27 12:39:31', 0, 1, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0),
(151, 'western@vaultibk.com', '$2y$12$omMYsGcvMErY5ZZqMpWNTuVa.g8svaAJg.AynRZgPk.FagZ9s13gW', 'support', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'admin', 0, 'active', 'verified', 0, NULL, NULL, 0, 'email', NULL, NULL, NULL, NULL, '2026-08-27 13:28:41', 1, 0, NULL, NULL, 'en', 'USD', 0.00, NULL, NULL, NULL, 0, 'normal', '2026-08-25 01:44:46', '2026-08-27 13:28:41', 0, 1, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0),
(153, 'ekwensu42@gmail.com', '$2y$12$ZHX8xzn06OOWuKei07gjTuczSgCClBt9TS0KTayiHsIEilf25h1v6', 'Henry Stevens', '+447570103580', '1971-03-25', 'male', 'West hills', 'London', 'London', 'Germany', 'W105DN', NULL, 'user', 0, 'active', 'verified', 0, NULL, '2026-08-25 02:45:48', 0, 'email', 'Admin created account', '$2y$12$XyQpSz2XyxwJSgsbJ6OuauqvOLAINTTIDcz8UkFtyJmQn0lNM1LIy', 'Admin created account', '$2y$12$BxIwE5hQKrwAcK7w3/Eo2eZ1Sz/vmVdNRja/TZZdYN6Opf3qQwqle', '2026-08-25 04:17:30', 1, 0, '{\"email_notifications\":true,\"sms_notifications\":false,\"transaction_alerts\":true,\"login_alerts\":true,\"marketing_emails\":false}', '{\"timezone\":\"America\\/New_York\"}', 'en', 'EUR', 0.00, '$2y$10$k6ucRrCqfb8XLEZEYWjYg.NiJSPNH4QSKu13VB/Ii4eEBpS0pdDHi', NULL, '$2y$10$fGTfPw35yK.rGPkD9J1f5.0tYFS5Or1UyZD7wbOwH9lh/Q0w31Bpu', 0, 'force_pending', '2026-08-25 02:45:48', '2026-08-27 11:59:13', 1, 0, '4413854673', 0, '9924970112', 0, '7363128013', 0, '8788606780', 0, '6172496208', 0),
(154, 'henryahmed638@gmail.com', '$2y$12$5xwDBasppwDYDfG67JKm6uhdzMOa0mafA40q48mn5fZqY3LxrzXXu', 'Joe Anderson', '54515558', '2004-08-28', 'male', '455fcct', 'Ttffc', 'London', 'United States', '55dddfg', NULL, 'user', 0, 'active', 'verified', 0, NULL, '2026-08-25 12:47:34', 0, 'email', 'What city were you born in?', '$2y$12$yyeh9bpiQhqduUk/0sPj2O30/q6cz/ugthUzOnF17RSBn/PAxIe52', 'What was the name of your first pet?', '$2y$12$DLWDyXpHQxP8Qes08tmcDe0qs3Ym1AHf8VNhm1v8eePyTu5GD.jS6', '2026-08-26 05:50:34', 1, 0, '{\"email_notifications\":true,\"sms_notifications\":false,\"transaction_alerts\":true,\"login_alerts\":true,\"marketing_emails\":true}', '{\"timezone\":\"America\\/New_York\"}', 'en', 'USD', 0.00, '$2y$10$f3AfRNkq9gkED6JBes.sF.w/Tsk6gGbdb8YOGN/ElzqS0EUE.JFJu', NULL, '$2y$10$UiLeT2I9T7IQQtv7NjdvNe7QKRzjcBTuB4jdQWijoantaEHVTWDXi', 0, 'normal', '2026-08-25 12:35:00', '2026-08-26 05:50:34', 1, 1, '3319790529', 0, '8565583529', 0, '2663070606', 0, '5258727691', 0, '9394297742', 0),
(155, 'andrewjarry15@gmail.com', '$2y$12$Eg07OIkuL/aXGH4jgP9URutf1pKKLW/5GgAIaNQ1USRqO9e66oZKq', 'Andrew Jerry', '+256746582614', '1980-07-15', 'male', '27 Maple Court, Kensington, London W8 6AB, United Kingdom', 'London', 'Greater', 'United Kingdom', 'GT34DF', NULL, 'user', 0, 'active', 'verified', 0, NULL, '2026-08-26 04:58:19', 0, 'email', 'Admin created account', '$2y$12$T49szakzwDNtGQPd9PJUpuOEGvgETc689jG9UomqY8IdyRRDifEG2', 'Admin created account', '$2y$12$i9lG9TpZ9pTEyLORhliuYu1u13CBTiSDiFHX9qikDSjotIzaBBFKW', NULL, 1, 0, '{\"email_notifications\":true,\"sms_notifications\":false,\"transaction_alerts\":true,\"login_alerts\":true,\"marketing_emails\":false}', NULL, 'en', 'GBP', 0.00, '$2y$10$ESyy7zGk7fmmeUwKok5B0uYTDGRpp15z3mbF.FWrTZ/d1xRBFreNm', NULL, '$2y$10$/MfcImTpy0DozzqHWxAEdOcSeQD.DytS.XV76RzcrrvmZOdUkpCVm', 0, 'normal', '2026-08-26 04:58:19', '2026-08-26 05:02:35', 1, 1, '7144953961', 0, '0256121820', 0, '7179329776', 0, '5690373536', 0, '8015123718', 0),
(156, 'Ilangasingheshanika8@gmail.com', '$2y$12$f0KUVzAYuWIUAep2quXQvuxege49j16sdkleLs3ycX8TRzflP6JYG', 'Padmini Silwa', '+94702210536', '1972-01-27', 'female', 'Sri Lanka', 'Sri Lanka', 'Sri Lanka', 'Sri Lanka', 'SRI45TT', NULL, 'user', 0, 'active', 'verified', 0, NULL, '2026-08-26 12:00:16', 0, 'email', 'Admin created account', '$2y$12$iPkAX75APGjmxqQr.ElGYOJ86IX4qDntr2V7CdVjxpzvJUivuh6qG', 'Admin created account', '$2y$12$nXln85kU2L7IQq3vbJKI3OuSKtqdS4AK7gcrIGTvsGSeJgF1pG.cu', '2026-08-27 11:44:29', 1, 0, '{\"email_notifications\":true,\"sms_notifications\":false,\"transaction_alerts\":true,\"login_alerts\":true,\"marketing_emails\":false}', NULL, 'en', 'LKR', 0.00, '$2y$10$t57I29piezZnOa.BiSl2l.gYPC0UncY7Hdz2v/1OlodIugc.vti/.', NULL, '$2y$10$uYACL3u.ASWiu9DeiYeIf.U/kY0L88T4zERTF0AFG35tuLAhZTTqW', 0, 'normal', '2026-08-26 12:00:16', '2026-08-27 11:44:29', 1, 1, '5397749393', 0, '1077614574', 0, '3185645486', 0, '7112106129', 0, '5946161989', 0),
(157, 'benjaminedward854@gmail.com', '$2y$12$z0zlMMyNViR7kSHMRyvMZurPWeomlql/BT4uIXlpGILc4RF/9A2MC', 'Benjamin Edward', '+44 7347 093297', '1969-10-13', 'male', 'Downhill', 'UK', 'UK', 'United Kingdom', '455865', '/uploads/profile-pictures/user_157_1787748481.jpg', 'user', 0, 'active', 'verified', 0, NULL, '2026-08-26 12:30:50', 0, 'email', 'Admin created account', '$2y$12$QyXhb87E0GPsK74aMpJp4.Vjl4l6RHm8gWPGn/d2dEzv2e7eOfJpe', 'Admin created account', '$2y$12$DM6tsu6hMnFwjVFzs5q9Fe5vsqGK3K562P6OMqCSqRL3pzdcfw8Yy', '2026-08-26 14:01:36', 1, 0, '{\"email_notifications\":true,\"sms_notifications\":false,\"transaction_alerts\":true,\"login_alerts\":true,\"marketing_emails\":false}', NULL, 'en', 'GBP', 0.00, '$2y$10$loPa/wxHHxYYdbSyirwzROBuJyQpxrAwhYLUY.FDK1ZrYXwvF.2Iu', NULL, '$2y$10$Nn4yAu5GnaSls2Y8BK3LVu6FIbFdbUmO5bSWjVnj/XTEPR8v5cJbC', 0, 'normal', '2026-08-26 12:30:50', '2026-08-26 14:01:36', 1, 1, '2473468226', 0, '6516941588', 0, '1122286786', 0, '2730865895', 0, '5945746046', 0),
(158, 'ofornaogwu@gmail.com', '$2y$12$3Fmp0gTbhtL.gIJi.DWi0uKrY8EfYGWU33OAcNNhc2LPsi.U0cHD6', 'Okenwa Joe', '+447570103580', '1988-08-26', 'male', 'Maintown', 'East London', 'Maintown', 'Dominican Republic', '53328uu', NULL, 'user', 0, 'active', 'verified', 0, NULL, '2026-08-26 12:35:56', 0, 'email', 'Admin created account', '$2y$12$BsRcynEUVAAWHPLrSEM5xuL4DjeqPS/dlszY92ovkwK2kVJ1cy8WK', 'Admin created account', '$2y$12$GvvL7w28pc8X9a6CF5QDke7gtK1aA.7ujwjRMLWHX6t18vXw1RDjm', '2026-08-26 12:36:57', 1, 0, '{\"email_notifications\":true,\"sms_notifications\":false,\"transaction_alerts\":true,\"login_alerts\":true,\"marketing_emails\":false}', NULL, 'en', 'DOP', 0.00, '$2y$10$YAJM7uPJNmMv9g/3q.Yu7.3xJ07WhqWW0AeMl7/OsALm/TB3B3BaK', NULL, '$2y$10$UE0MBVnN.6OxlgIuJmKfc.tW7yK1lSAh.Cq27UIAmLNfRh0fn6JBm', 0, 'normal', '2026-08-26 12:35:56', '2026-08-27 15:10:03', 1, 1, '4289825152', 0, '9725104442', 0, '1202296636', 0, '3635904334', 0, '7646673409', 0),
(159, 'henryahmed1998@gmail.com', '$2y$12$9FJaal.j/RA7pO.6II7HP.YgJPtVZSKjOs2Ol5sS2oagA5kS/F/fq', 'Henry Edward', '+44 7796 889862', '1978-07-26', 'male', '48 Willow Crescent, London, SW16 4QH, United Kingdom', 'London', 'Greater London', 'United Kingdom', 'SW1A 1AA', '/uploads/profile-pictures/user_159_1787791182.jpg', 'user', 0, 'active', 'verified', 0, NULL, '2026-08-26 16:35:03', 0, 'email', 'Admin created account', '$2y$12$H..gs65ci9CjfzY/EXJxrup.WObn9wEmO9js3jc7Hsaxie4GyiwSa', 'Admin created account', '$2y$12$eMnLywQTO8KI4PsVtxDk0.va3NhKz5wAwClwF9dx/InA7f./vrBra', '2026-08-27 00:38:53', 1, 0, '{\"email_notifications\":true,\"sms_notifications\":false,\"transaction_alerts\":true,\"login_alerts\":true,\"marketing_emails\":false}', NULL, 'en', 'GBP', 0.00, '$2y$10$7e63d0PO4c8dE45o0k7DcOQZEStmigG2LVVs.Fl4sK78YypqX6C6.', NULL, '$2y$10$qezUEQKtSTvYihjijEoGlOBM5Jce7rQdMFzHFZq0pEbV6JmHT5wxC', 0, 'normal', '2026-08-26 16:35:03', '2026-08-27 00:39:42', 1, 1, '3197059084', 0, '0782344170', 0, '6400710162', 0, '9641132050', 0, '2413990287', 0),
(160, 'henryedwardh481@gmail.com', '$2y$12$lLqZxrR9mtsUX8Ymv/GBm.Iyb3JZIuIFai3UvT8fjl0.8sPB3wid.', 'Henry Edward', '+44 7796 889862', '1978-07-26', 'male', '48 Willow Crescent, London, SW16 4QH, United Kingdom', 'London', 'Greater London', 'United Kingdom', 'SW1A 1AA', '/uploads/profile-pictures/user_160_1787790636.jpg', 'user', 0, 'active', 'verified', 0, NULL, '2026-08-26 21:18:24', 0, 'email', 'Admin created account', '$2y$12$nM7MJxhfmpSUVmLgrDQ13uiC5KNGEt/0s5ZJ92zurukeZWdYeJOUy', 'Admin created account', '$2y$12$Mc2eRVO485n0zILN9J6HVuNAVZ9j2NumJWll62DIzwvHx2uWljHtG', '2026-08-27 03:24:59', 1, 0, '{\"email_notifications\":true,\"sms_notifications\":false,\"transaction_alerts\":true,\"login_alerts\":true,\"marketing_emails\":false}', NULL, 'en', 'GBP', 0.00, '$2y$10$b6iPMYMiHKDOc1JtbhexC.5CES0btJ2kDOVUPecA0/UoCQwk4jG3e', NULL, '$2y$10$H5p2MBcKFbxlvAgL2d1Vser1ZVVGhP41Jm72Vncw4lDcbNbYZ2USq', 0, 'normal', '2026-08-26 21:18:24', '2026-08-27 03:24:59', 1, 1, '7715216193', 0, '6825284157', 0, '2435626185', 0, '2303164277', 0, '5765907336', 0);

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
-- Indexes for table `auto_migrations`
--
ALTER TABLE `auto_migrations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_applied_at` (`applied_at`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=155;

--
-- AUTO_INCREMENT for table `account_owners`
--
ALTER TABLE `account_owners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2817;

--
-- AUTO_INCREMENT for table `admin_audit_logs`
--
ALTER TABLE `admin_audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=360;

--
-- AUTO_INCREMENT for table `admin_sessions`
--
ALTER TABLE `admin_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `banks`
--
ALTER TABLE `banks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1252;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=150;

--
-- AUTO_INCREMENT for table `exchange_rates`
--
ALTER TABLE `exchange_rates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=437;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=325;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=172;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1890;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1927;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=561;

--
-- AUTO_INCREMENT for table `update_logs`
--
ALTER TABLE `update_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=163;

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
