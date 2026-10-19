-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 23, 2026 at 10:46 AM
-- Server version: 11.4.13-MariaDB-cll-lve
-- PHP Version: 8.4.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `felon874_main`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `telegram_id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_active` tinyint(4) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `telegram_id`, `name`, `is_active`, `created_at`) VALUES
(1, 330489980, 'Serhii F', 1, '2025-07-03 12:04:35');

-- --------------------------------------------------------

--
-- Table structure for table `ambulatorii`
--

CREATE TABLE `ambulatorii` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `api_tokens`
--

CREATE TABLE `api_tokens` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `is_active` tinyint(4) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `api_tokens`
--

INSERT INTO `api_tokens` (`id`, `name`, `token`, `permissions`, `is_active`, `created_at`) VALUES
(1, 'Telegram Bot', 'd95ba3f7fee2cddbd6af02a39ddc0b25c1876ab37b0b4000c2aceab8cae7d74b', '[\"user\",\"admin\"]', 1, '2025-07-03 11:59:02');

-- --------------------------------------------------------

--
-- Table structure for table `app_settings`
--

CREATE TABLE `app_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `app_settings`
--

INSERT INTO `app_settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'print_deputy_director', 'Олександр ХРОМЧЕНКО', '2026-05-25 13:13:18', '2026-05-25 13:13:18'),
(2, 'print_director', 'Ганна ПАВЛЕГА', '2026-05-25 13:13:18', '2026-06-05 04:50:26');

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_active` tinyint(4) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `name`, `is_active`, `created_at`) VALUES
(1, 'Івана та Юрія Лип, 1', 1, '2025-07-03 11:59:02'),
(2, 'Болгарська, 38', 1, '2025-07-03 11:59:02'),
(3, 'Фесенка Юхима, 11', 1, '2025-07-03 11:59:02'),
(4, 'Бугаївська, 46', 1, '2025-07-03 11:59:02'),
(5, 'Академіка Гаркавого, 2', 1, '2025-07-03 11:59:02'),
(6, 'Склад', 1, '2025-07-03 11:59:02');

-- --------------------------------------------------------

--
-- Table structure for table `cabinets`
--

CREATE TABLE `cabinets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` int(10) UNSIGNED NOT NULL,
  `number` varchar(255) NOT NULL,
  `ambulatoriya_id` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cabinet_shifts`
--

CREATE TABLE `cabinet_shifts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cabinet_id` bigint(20) UNSIGNED NOT NULL,
  `shift` tinyint(3) UNSIGNED NOT NULL,
  `doctor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nurse_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel_cache_telegram_message_1269824605_1195', 's:32:\"888de875e939a8c20cab34093e06aad0\";', 1789988565),
('laravel_cache_telegram_message_1269824605_759', 's:32:\"f0f3ea2e6fa8b190137f2d115df10d1c\";', 1789984063),
('laravel_cache_telegram_message_1269824605_994', 's:32:\"888de875e939a8c20cab34093e06aad0\";', 1789984020),
('laravel_cache_telegram_message_1748926034_1185', 's:32:\"888de875e939a8c20cab34093e06aad0\";', 1789987328),
('laravel_cache_telegram_message_1748926034_1189', 's:32:\"45b8997b96784848ff9d9c215e327a2f\";', 1789987358),
('laravel_cache_telegram_message_495335120_1208', 's:32:\"888de875e939a8c20cab34093e06aad0\";', 1790078498),
('laravel_cache_telegram_message_495335120_1210', 's:32:\"6dfae0d0b6fa5b54bfab33e4fb4594a2\";', 1790078515),
('laravel_cache_telegram_user_state_6942033051', 'a:3:{s:5:\"state\";s:27:\"onboarding_awaiting_contact\";s:9:\"temp_data\";a:0:{}s:10:\"updated_at\";O:25:\"Illuminate\\Support\\Carbon\":4:{s:4:\"date\";s:26:\"2026-09-22 22:25:43.417650\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:3:\"UTC\";s:18:\"dumpDateProperties\";a:2:{s:4:\"date\";s:26:\"2026-09-22 22:25:43.417650\";s:8:\"timezone\";s:3:\"UTC\";}}}', 1790202343);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cartridge_replacements`
--

CREATE TABLE `cartridge_replacements` (
  `id` int(11) NOT NULL,
  `user_telegram_id` bigint(20) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `branch_id` int(11) NOT NULL,
  `room_number` varchar(50) NOT NULL,
  `printer_inventory_id` bigint(20) UNSIGNED DEFAULT NULL,
  `printer_info` varchar(500) NOT NULL,
  `cartridge_type` varchar(255) NOT NULL,
  `replacement_date` date NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cartridge_replacements`
--

INSERT INTO `cartridge_replacements` (`id`, `user_telegram_id`, `username`, `branch_id`, `room_number`, `printer_inventory_id`, `printer_info`, `cartridge_type`, `replacement_date`, `notes`, `created_at`, `updated_at`) VALUES
(1, 330489980, 'metamorf_dev', 1, '27', NULL, '123123123', 'HP CF217A', '2025-07-05', NULL, '2025-07-04 19:21:43', ''),
(2, 330489980, 'metamorf_dev', 1, '27', NULL, '123123123', 'Епсон', '2025-07-05', NULL, '2025-07-04 19:26:26', ''),
(3, 330489980, 'metamorf_dev', 1, '27', NULL, '123123123', 'HP CF217A', '2025-07-06', NULL, '2025-07-06 16:36:52', ''),
(4, 1748926034, NULL, 1, '27', NULL, 'epson', '31222', '2025-08-13', NULL, '2025-08-13 05:54:05', ''),
(5, 542503468, 'ksenia_kalyan', 3, '204', NULL, 'Epson WorkForce Pro, WF-M5690', 'T8651', '2025-08-20', NULL, '2025-08-20 02:38:44', ''),
(6, 1951296190, NULL, 1, '37', NULL, 'Epson,WF-M5690', 'инвентарный номер 101467090', '2025-08-20', NULL, '2025-08-20 03:44:42', ''),
(7, 330489980, 'metamorf_dev', 1, '27', NULL, 'test', 'testtest', '2025-08-27', NULL, '2025-08-27 18:27:10', '2025-08-27 21:27:10'),
(8, 542503468, 'ksenia_kalyan', 3, '210', NULL, 'Epson WorkForce Pro, WF-M5690', 'Epson WF-M5690', '2025-12-24', NULL, '2025-12-24 05:47:36', '2025-12-24 07:47:36'),
(9, 1742048506, NULL, 1, '16', NULL, 'Epson', '5061', '2025-12-25', NULL, '2025-12-25 12:17:48', '2025-12-25 14:17:48'),
(10, -960640883, NULL, 1, '35', NULL, 'EPSON', 'IC-T 8651XXL', '2025-12-30', NULL, '2025-12-30 04:51:15', '2025-12-30 06:51:15'),
(11, 534279612, NULL, 1, '45/3', NULL, 'Епсон', '5619', '2026-01-01', NULL, '2026-01-01 05:23:25', '2026-01-01 07:23:25'),
(12, 391828916, 'Atlantis_Star', 1, '26', NULL, '101467108', 'WF-M5690', '2026-01-09', NULL, '2026-01-09 06:12:02', '2026-01-09 08:12:02'),
(13, 464357703, 'daria_hirzheu', 5, '201', NULL, '11200174', 'Я не знаю', '2026-01-12', NULL, '2026-01-12 09:02:35', '2026-01-12 11:02:35'),
(14, 1913686001, NULL, 1, '7a', NULL, 'Epson', '8651', '2026-02-10', NULL, '2026-02-10 10:27:06', '2026-02-10 12:27:06'),
(15, 723971996, NULL, 3, '205', NULL, 'M5690', 'T 8651', '2026-02-24', NULL, '2026-02-24 09:51:24', '2026-02-24 11:51:24'),
(16, 923722881, 'Elena65005', 2, '306', NULL, 'Workforce Pro WF-M5690', 'HP', '2026-03-03', NULL, '2026-03-03 06:07:42', '2026-03-03 08:07:42'),
(17, 634490388, 'MedikalNata', 5, '216', NULL, 'Epson Workforce Pro WF-M5690.    Інв. номер: 101480247', 'IC-T8651XXL. T8651', '2026-03-17', NULL, '2026-03-17 07:03:44', '2026-03-17 09:03:44'),
(18, 495335120, NULL, 4, '103', NULL, 'Jet Pro', 'LaserJet', '2026-03-25', NULL, '2026-03-25 09:08:31', '2026-03-25 11:08:31'),
(19, 330489980, 'metamorf_dev', 4, '102', NULL, 'epson', '8651', '2026-04-01', NULL, '2026-04-01 08:00:10', '2026-04-01 11:00:10'),
(20, 534279612, NULL, 1, '43/3', NULL, '101467092', 'M-5690', '2026-06-03', NULL, '2026-06-03 03:01:26', '2026-06-03 06:01:26'),
(21, 2032031370, NULL, 1, '7', NULL, 'Epson', '5630', '2026-06-05', NULL, '2026-06-05 10:14:06', '2026-06-05 13:14:06'),
(22, 464357703, 'daria_hirzheu', 5, '133', NULL, 'Epson 101467097', 'IC-T8651XXL', '2026-08-24', NULL, '2026-08-24 04:26:00', '2026-08-24 07:26:00'),
(23, 906856609, 'MilaMoon_G', 1, '9', NULL, 'EPSON\nWF-M5690', 'T8651', '2026-09-14', NULL, '2026-09-14 06:57:06', '2026-09-14 09:57:06'),
(24, 1269824605, NULL, 1, '7a', NULL, 'Epson', '5690', '2026-09-21', NULL, '2026-09-21 07:03:02', '2026-09-21 10:03:02'),
(25, 495335120, NULL, 4, '103', NULL, 'Hp', 'Hp', '2026-09-22', NULL, '2026-09-22 08:02:06', '2026-09-22 11:02:06');

-- --------------------------------------------------------

--
-- Table structure for table `contractors`
--

CREATE TABLE `contractors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `type` enum('repair','supply','service') NOT NULL,
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contractor_operations`
--

CREATE TABLE `contractor_operations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `contractor_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `inventory_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` enum('send_for_repair','receive_from_repair','purchase','service') NOT NULL,
  `contract_number` varchar(255) DEFAULT NULL,
  `operation_date` date NOT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `description` text NOT NULL,
  `status` enum('in_progress','completed','cancelled') NOT NULL DEFAULT 'in_progress',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_audits`
--

CREATE TABLE `inventory_audits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `audit_number` varchar(255) NOT NULL,
  `audit_date` date NOT NULL,
  `status` enum('planned','in_progress','completed') NOT NULL DEFAULT 'planned',
  `total_items` int(11) NOT NULL DEFAULT 0,
  `checked_items` int(11) NOT NULL DEFAULT 0,
  `missing_items` int(11) NOT NULL DEFAULT 0,
  `extra_items` int(11) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_audit_items`
--

CREATE TABLE `inventory_audit_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inventory_id` bigint(20) UNSIGNED DEFAULT NULL,
  `inventory_number` varchar(255) NOT NULL,
  `equipment_type` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `status` enum('found','missing','extra','damaged') NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_logs`
--

CREATE TABLE `inventory_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `inventory_id` bigint(20) UNSIGNED NOT NULL,
  `action` varchar(255) NOT NULL,
  `old_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_data`)),
  `new_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_data`)),
  `from_location` varchar(255) DEFAULT NULL,
  `to_location` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_templates`
--

CREATE TABLE `inventory_templates` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `equipment_type` varchar(100) NOT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `requires_serial` tinyint(4) DEFAULT 0,
  `requires_inventory` tinyint(4) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory_templates`
--

INSERT INTO `inventory_templates` (`id`, `name`, `equipment_type`, `brand`, `model`, `requires_serial`, `requires_inventory`, `created_at`) VALUES
(1, 'Комп\'ютер стандартний', 'Комп\'ютер', '', '', 1, 1, '2025-07-03 11:59:02'),
(2, 'Монітор стандартний', 'Монітор', '', '', 1, 1, '2025-07-03 11:59:02'),
(3, 'Принтер HP LaserJet', 'Принтер', 'HP', 'LaserJet', 1, 1, '2025-07-03 11:59:02'),
(4, 'Клавіатура', 'Клавіатура', '', '', 0, 1, '2025-07-03 11:59:02'),
(5, 'Миша', 'Миша', '', '', 0, 1, '2025-07-03 11:59:02');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_transfers`
--

CREATE TABLE `inventory_transfers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inventory_id` bigint(20) UNSIGNED NOT NULL,
  `from_branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `from_room_number` varchar(50) DEFAULT NULL,
  `to_branch_id` bigint(20) UNSIGNED NOT NULL,
  `to_room_number` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `transfer_date` date NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory_transfers`
--

INSERT INTO `inventory_transfers` (`id`, `inventory_id`, `from_branch_id`, `from_room_number`, `to_branch_id`, `to_room_number`, `quantity`, `user_id`, `transfer_date`, `notes`, `created_at`, `updated_at`) VALUES
(5, 1740, 5, 'Склад', 1, 'Склад', 1, 1, '2025-12-16', NULL, '2025-12-16 10:20:29', '2025-12-16 10:20:29'),
(6, 1740, 1, 'Склад', 6, 'Підвал', 1, 1, '2025-12-16', NULL, '2025-12-16 11:51:25', '2025-12-16 11:51:25'),
(7, 194, 5, '327', 5, '308', 1, 1, '2025-12-17', NULL, '2025-12-17 08:46:14', '2025-12-17 08:46:14'),
(8, 196, 5, '327', 5, '308', 1, 1, '2025-12-17', NULL, '2025-12-17 08:46:24', '2025-12-17 08:46:24'),
(9, 197, 5, '327', 5, '308', 1, 1, '2025-12-17', NULL, '2025-12-17 08:46:32', '2025-12-17 08:46:32'),
(10, 833, 1, '35', 6, 'Підвал', 1, 1, '2026-01-16', NULL, '2026-01-16 06:53:33', '2026-01-16 06:53:33'),
(11, 834, 1, '35', 1, 'Підвал', 1, 1, '2026-01-16', NULL, '2026-01-16 06:54:15', '2026-01-16 06:54:15'),
(12, 835, 1, '35', 1, 'Підвал', 1, 1, '2026-01-16', NULL, '2026-01-16 06:54:23', '2026-01-16 06:54:23'),
(13, 836, 1, '35', 1, 'Підвал', 1, 1, '2026-01-16', NULL, '2026-01-16 06:54:30', '2026-01-16 06:54:30'),
(14, 837, 1, '35', 1, 'Підвал', 1, 1, '2026-01-16', NULL, '2026-01-16 06:54:38', '2026-01-16 06:54:38'),
(15, 838, 1, '35', 1, 'Підвал', 1, 1, '2026-01-16', NULL, '2026-01-16 06:54:46', '2026-01-16 06:54:46'),
(16, 839, 1, '35', 1, 'Підвал', 1, 1, '2026-01-16', NULL, '2026-01-16 06:54:54', '2026-01-16 06:54:54'),
(17, 840, 1, '35', 1, 'Підвал', 1, 1, '2026-01-16', NULL, '2026-01-16 06:55:03', '2026-01-16 06:55:03'),
(18, 841, 1, '35', 1, 'Підвал', 1, 1, '2026-01-16', NULL, '2026-01-16 06:55:13', '2026-01-16 06:55:13'),
(19, 842, 1, '35', 1, 'Підвал', 1, 1, '2026-01-16', NULL, '2026-01-16 06:55:33', '2026-01-16 06:55:33'),
(20, 843, 1, '35', 1, 'Підвал', 1, 1, '2026-01-16', NULL, '2026-01-16 06:55:43', '2026-01-16 06:55:43'),
(21, 844, 1, '35', 1, 'Підвал', 1, 1, '2026-01-16', NULL, '2026-01-16 06:55:51', '2026-01-16 06:55:51'),
(22, 1735, 1, '23', 1, '35', 1, 1, '2026-01-16', NULL, '2026-01-16 06:56:45', '2026-01-16 06:56:45'),
(23, 672, 1, '23', 1, '35', 1, 1, '2026-01-16', NULL, '2026-01-16 06:56:51', '2026-01-16 06:56:51'),
(24, 674, 1, '23', 1, '35', 1, 1, '2026-01-16', NULL, '2026-01-16 06:56:57', '2026-01-16 06:56:57'),
(25, 675, 1, '23', 1, '35', 1, 1, '2026-01-16', NULL, '2026-01-16 06:57:04', '2026-01-16 06:57:04'),
(26, 676, 1, '23', 1, '35', 1, 1, '2026-01-16', NULL, '2026-01-16 06:57:13', '2026-01-16 06:57:13'),
(27, 677, 1, '23', 1, '35', 1, 1, '2026-01-16', NULL, '2026-01-16 06:57:22', '2026-01-16 06:57:22'),
(28, 678, 1, '23', 1, '35', 1, 1, '2026-01-16', NULL, '2026-01-16 06:57:28', '2026-01-16 06:57:28'),
(29, 679, 1, '23', 1, '35', 1, 1, '2026-01-16', NULL, '2026-01-16 06:57:34', '2026-01-16 06:57:34'),
(30, 680, 1, '23', 1, '35', 1, 1, '2026-01-16', NULL, '2026-01-16 07:01:17', '2026-01-16 07:01:17'),
(31, 681, 1, '23', 1, '35', 1, 1, '2026-01-16', NULL, '2026-01-16 07:01:25', '2026-01-16 07:01:25'),
(32, 881, 1, '40', 6, 'Підвал', 1, 1, '2026-01-22', 'Вийшов з ладу процесор\\МП', '2026-01-22 09:18:34', '2026-01-22 09:18:34'),
(33, 54, 5, 'Склад', 1, '46', 1, 1, '2026-01-15', NULL, '2026-03-06 06:24:48', '2026-03-06 06:24:48'),
(34, 330, 6, 'Підвал', 1, '13', 1, 1, '2026-03-24', 'Для нових гінекологів', '2026-03-24 09:48:40', '2026-03-24 09:48:40'),
(35, 1658, 6, 'Підвал', 1, '13', 1, 1, '2026-03-24', 'Для нових гінекологів', '2026-03-24 09:49:15', '2026-03-24 09:49:15'),
(36, 1685, 6, 'Підвал', 1, '13', 1, 1, '2026-03-24', 'Для нових гінекологів', '2026-03-24 09:49:33', '2026-03-24 09:49:33'),
(37, 1687, 6, 'Підвал', 1, '23', 1, 1, '2026-03-24', 'Скринінг 40+', '2026-03-24 09:50:05', '2026-03-24 09:50:05'),
(38, 518, 1, '22', 1, '45/4', 1, 1, '2026-03-31', NULL, '2026-03-31 05:42:24', '2026-03-31 05:42:24'),
(39, 54, 1, '46', 6, 'Підвал', 1, 1, '2026-03-31', NULL, '2026-03-31 06:22:34', '2026-03-31 06:22:34'),
(40, 1851, 6, 'Підвал', 5, '133', 1, 1, '2026-06-04', 'Відкриття кабінету скринінгу', '2026-06-04 04:16:00', '2026-06-04 04:16:00'),
(41, 1311, 1, '50', 2, 'к307', 1, 1, '2026-07-02', 'скринінг', '2026-07-02 04:28:07', '2026-07-02 04:28:07'),
(42, 1350, 1, '58', 3, '106', 1, 1, '2026-07-02', 'скринінг', '2026-07-02 04:28:43', '2026-07-02 04:28:43'),
(43, 841, 1, 'Підвал', 6, '2 поверх', 1, 1, '2026-08-20', 'Под списание', '2026-08-20 06:47:19', '2026-08-20 06:47:19');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medical_staff`
--

CREATE TABLE `medical_staff` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `position` varchar(255) NOT NULL,
  `employment_status` varchar(255) DEFAULT NULL,
  `position_status` varchar(255) DEFAULT NULL,
  `ambulatoriya_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_07_09_081439_create_permission_tables', 1),
(5, '2025_01_01_000010_create_user_states_table', 2),
(6, '2025_01_01_000011_create_api_tokens_table', 3),
(7, '2025_01_01_000012_update_tables_to_match_existing', 4),
(8, '2025_01_01_120000_create_repair_trackings_table', 5),
(9, '2025_01_01_200000_update_roles_system', 6),
(10, '2025_01_20_000001_add_warehouse_keeper_role', 7),
(11, '2025_01_25_000001_add_warehouse_fields_to_room_inventory', 8),
(14, '2025_01_26_000001_fix_warehouse_inventory_items_table', 9),
(15, '2025_10_10_000001_add_balance_code_and_transfer_support', 10),
(16, '2025_01_27_000001_fix_warehouse_movements_inventory_id', 11),
(17, '2025_12_16_121905_add_transfer_type_to_warehouse_movements_table', 12),
(22, '2025_12_18_075443_refactor_repair_tracking_system', 13),
(23, '2025_12_18_100637_make_cost_nullable_on_repair_order_items', 13),
(24, '2025_12_31_104949_add_full_name_to_room_inventory', 13),
(25, '2026_01_27_065223_create_work_logs_table', 13),
(26, '2026_02_18_075857_add_archived_at_to_purchase_requests_table', 14),
(27, '2026_03_13_092300_add_category_to_purchase_request_items_table', 15),
(28, '2026_03_13_094704_add_cleaning_supplies_category', 15),
(29, '0001_01_01_000003_create_legacy_base_tables', 16),
(30, '2026_05_25_122710_create_app_settings_table', 16),
(31, '2026_05_25_142953_add_return_fields_to_repair_order_items_table', 16),
(32, '2026_05_26_092137_create_writeoff_requests_table', 17),
(33, '2026_05_26_092138_create_writeoff_request_items_table', 17),
(34, '2026_08_16_213059_add_print_period_to_purchase_requests_table', 18),
(35, '2026_08_16_213156_add_is_priority_to_room_inventory_table', 18),
(36, '2026_08_16_214706_add_old_inventory_id_to_repair_requests_table', 18),
(37, '2026_08_26_105354_create_telegram_profiles_table', 19),
(38, '2026_08_26_135812_add_phone_to_telegram_profiles_table', 19),
(39, '2026_09_20_214259_create_ambulatorii_table', 20),
(40, '2026_09_20_214259_create_medical_staff_table', 20),
(41, '2026_09_20_214300_create_cabinets_table', 20),
(42, '2026_09_20_214301_create_cabinet_shifts_table', 20);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_requests`
--

CREATE TABLE `purchase_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `request_number` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('draft','submitted','approved','rejected','completed') NOT NULL DEFAULT 'draft',
  `description` text DEFAULT NULL,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `requested_date` date NOT NULL,
  `print_month` varchar(255) DEFAULT NULL,
  `print_year` smallint(5) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `archived_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_requests`
--

INSERT INTO `purchase_requests` (`id`, `request_number`, `user_id`, `status`, `description`, `total_amount`, `requested_date`, `print_month`, `print_year`, `notes`, `archived_at`, `created_at`, `updated_at`) VALUES
(1, 'ZAY-2025-000001', 1, 'submitted', NULL, 2655.00, '2025-09-24', NULL, NULL, NULL, '2026-02-18 08:21:48', '2025-09-22 08:35:48', '2026-02-18 08:21:48'),
(2, 'ZAY-2025-000002', 3, 'draft', NULL, 4779.50, '2025-10-01', NULL, NULL, NULL, '2026-02-18 08:21:35', '2025-09-22 09:32:36', '2026-02-18 08:21:35'),
(3, 'ZAY-2025-000003', 3, 'submitted', NULL, 2295.00, '2025-10-01', NULL, NULL, NULL, '2026-02-18 08:21:21', '2025-09-23 03:45:25', '2026-02-18 08:21:21'),
(4, 'ZAY-2025-000004', 1, 'draft', 'Руслан', 5250.00, '2025-12-17', NULL, NULL, NULL, '2026-02-18 08:21:10', '2025-12-16 12:26:11', '2026-02-18 08:21:10'),
(5, 'ZAY-2025-000005', 1, 'draft', NULL, 2625.00, '2025-12-18', NULL, NULL, NULL, '2026-02-18 08:20:59', '2025-12-17 06:40:59', '2026-02-18 08:20:59'),
(6, 'ZAY-2025-000006', 3, 'draft', NULL, 0.00, '2025-12-24', NULL, NULL, NULL, '2026-02-18 08:20:44', '2025-12-17 09:03:19', '2026-02-18 08:20:44'),
(7, 'ZAY-2025-000007', 3, 'draft', NULL, 0.00, '2025-12-24', NULL, NULL, NULL, '2026-02-18 08:20:33', '2025-12-17 09:03:34', '2026-02-18 08:20:33'),
(8, 'ZAY-2025-000008', 3, 'draft', NULL, 0.00, '2025-12-31', NULL, NULL, NULL, '2026-02-18 08:20:16', '2025-12-26 12:08:13', '2026-02-18 08:20:16'),
(9, 'ZAY-2025-000009', 3, 'draft', NULL, 0.00, '2025-12-30', NULL, NULL, NULL, '2026-02-18 08:19:41', '2025-12-26 12:20:03', '2026-02-18 08:19:41'),
(10, 'ZAY-2025-000010', 3, 'draft', NULL, 0.00, '2025-12-30', NULL, NULL, NULL, '2026-02-18 08:19:58', '2025-12-29 09:46:47', '2026-02-18 08:19:58'),
(11, 'ZAY-2025-000011', 1, 'draft', NULL, 0.00, '2026-01-01', NULL, NULL, NULL, '2026-02-18 08:18:59', '2025-12-31 05:46:43', '2026-02-18 08:18:59'),
(12, 'ZAY-2025-000012', 1, 'draft', NULL, 0.00, '2026-01-01', NULL, NULL, NULL, '2026-02-18 08:19:14', '2025-12-31 10:12:19', '2026-02-18 08:19:14'),
(13, 'ZAY-2026-000013', 1, 'draft', NULL, 0.00, '2026-01-08', NULL, NULL, NULL, '2026-02-18 08:18:41', '2026-01-07 09:53:25', '2026-02-18 08:18:41'),
(14, 'ZAY-2026-000014', 3, 'draft', NULL, 0.00, '2026-01-20', NULL, NULL, NULL, '2026-02-18 08:18:20', '2026-01-07 10:52:44', '2026-02-18 08:18:20'),
(15, 'ZAY-2026-000015', 1, 'draft', NULL, 13275.00, '2026-01-12', NULL, NULL, NULL, NULL, '2026-01-09 05:56:08', '2026-01-09 05:56:08'),
(16, 'ZAY-2026-000016', 1, 'draft', NULL, 15939.00, '2026-01-12', NULL, NULL, NULL, '2026-02-18 08:44:24', '2026-01-09 05:56:23', '2026-02-18 08:44:24'),
(17, 'ZAY-2026-000017', 3, 'completed', 'Хімія ТЕРМІНОВА', 0.00, '2026-03-20', NULL, NULL, NULL, NULL, '2026-01-09 07:51:52', '2026-03-13 08:43:53'),
(18, 'ZAY-2026-000018', 3, 'draft', 'Канцелярія ТЕРМІНОВА', 0.00, '2026-01-12', NULL, NULL, NULL, '2026-02-18 08:16:35', '2026-01-09 08:27:18', '2026-02-18 08:16:35'),
(19, 'ZAY-2026-000019', 3, 'draft', NULL, 0.00, '2026-01-19', NULL, NULL, NULL, NULL, '2026-01-14 08:35:41', '2026-01-14 08:35:41'),
(20, 'ZAY-2026-000020', 3, 'completed', NULL, 0.00, '2026-03-23', NULL, NULL, NULL, NULL, '2026-01-14 08:39:03', '2026-03-13 07:19:43'),
(21, 'ZAY-2026-000021', 3, 'draft', NULL, 0.00, '2026-01-19', NULL, NULL, NULL, '2026-02-18 08:42:56', '2026-01-14 08:46:43', '2026-02-18 08:42:56'),
(22, 'ZAY-2026-000022', 1, 'draft', NULL, 0.00, '2026-01-19', NULL, NULL, NULL, '2026-02-18 08:15:44', '2026-01-16 11:34:10', '2026-02-18 08:15:44'),
(23, 'ZAY-2026-000023', 1, 'draft', NULL, 0.00, '2026-02-12', NULL, NULL, NULL, '2026-02-18 08:13:43', '2026-02-09 08:21:00', '2026-02-18 08:13:43'),
(24, 'ZAY-2026-000024', 3, 'completed', NULL, 0.00, '2026-02-16', NULL, NULL, NULL, NULL, '2026-02-09 10:44:28', '2026-04-03 03:58:41'),
(25, 'ZAY-2026-000025', 3, 'draft', NULL, 0.00, '2026-02-23', NULL, NULL, NULL, NULL, '2026-02-09 10:55:20', '2026-02-18 08:32:50'),
(26, 'ZAY-2026-000026', 3, 'completed', NULL, 0.00, '2026-02-23', NULL, NULL, NULL, NULL, '2026-02-16 11:52:29', '2026-03-16 11:50:37'),
(27, 'ZAY-2026-000027', 1, 'draft', NULL, 0.00, '2026-02-23', NULL, NULL, NULL, NULL, '2026-02-18 08:49:30', '2026-02-18 08:49:30'),
(28, 'ZAY-2026-000028', 3, 'draft', NULL, 0.00, '2026-03-05', NULL, NULL, NULL, NULL, '2026-02-18 09:05:59', '2026-03-05 12:20:01'),
(29, 'ZAY-2026-000029', 3, 'completed', NULL, 0.00, '2026-04-16', NULL, NULL, NULL, NULL, '2026-02-19 08:45:18', '2026-04-16 09:55:58'),
(30, 'ZAY-2026-000030', 3, 'completed', NULL, 0.00, '2026-02-23', NULL, NULL, NULL, NULL, '2026-02-19 12:07:47', '2026-04-28 04:24:48'),
(31, 'ZAY-2026-000031', 3, 'draft', NULL, 0.00, '2026-03-05', NULL, NULL, NULL, NULL, '2026-02-20 10:55:30', '2026-03-05 11:54:59'),
(32, 'ZAY-2026-000032', 3, 'completed', NULL, 0.00, '2026-04-27', NULL, NULL, NULL, NULL, '2026-02-20 12:03:30', '2026-04-21 11:13:37'),
(33, 'ZAY-2026-000033', 1, 'completed', NULL, 0.00, '2026-03-05', NULL, NULL, NULL, NULL, '2026-02-25 09:34:14', '2026-04-01 05:28:34'),
(34, 'ZAY-2026-000034', 1, 'completed', NULL, 0.00, '2026-04-09', NULL, NULL, NULL, NULL, '2026-02-26 11:04:25', '2026-04-09 05:26:00'),
(35, 'ZAY-2026-000035', 1, 'draft', NULL, 0.00, '2026-03-06', NULL, NULL, NULL, NULL, '2026-02-26 11:32:28', '2026-03-06 05:33:39'),
(36, 'ZAY-2026-000036', 1, 'draft', NULL, 0.00, '2026-02-26', NULL, NULL, NULL, '2026-03-23 06:36:33', '2026-02-26 12:06:08', '2026-03-23 06:36:33'),
(37, 'ZAY-2026-000037', 1, 'completed', NULL, 59500.00, '2026-03-02', NULL, NULL, NULL, NULL, '2026-02-27 08:15:17', '2026-06-23 08:23:27'),
(38, 'ZAY-2026-000038', 3, 'draft', NULL, 0.00, '2026-03-30', NULL, NULL, NULL, NULL, '2026-03-25 11:19:39', '2026-03-30 04:12:40'),
(39, 'ZAY-2026-000039', 1, 'completed', 'Акумулятори + комплектуючі', 8180.00, '2026-06-01', NULL, NULL, NULL, NULL, '2026-04-08 06:14:53', '2026-06-30 09:21:28'),
(40, 'ZAY-2026-000040', 3, 'draft', NULL, 0.00, '2026-05-11', NULL, NULL, NULL, NULL, '2026-04-30 10:06:00', '2026-05-11 09:53:49'),
(41, 'ZAY-2026-000041', 1, 'completed', 'Ноути Таісія', 190044.00, '2026-06-01', NULL, NULL, NULL, NULL, '2026-05-08 06:23:35', '2026-06-30 09:21:15'),
(42, 'ZAY-2026-000042', 1, 'completed', 'Картриджі та краски', 32400.00, '2026-06-01', NULL, NULL, NULL, NULL, '2026-05-08 07:06:59', '2026-06-30 09:21:04'),
(43, 'ZAY-2026-000043', 3, 'draft', NULL, 38620.80, '2026-05-12', NULL, NULL, NULL, NULL, '2026-05-12 05:26:04', '2026-05-12 05:26:04'),
(44, 'ZAY-2026-000044', 3, 'draft', NULL, 50520.00, '2026-06-01', NULL, NULL, NULL, NULL, '2026-05-13 05:53:50', '2026-05-13 05:53:50'),
(45, 'ZAY-2026-000045', 3, 'draft', NULL, 0.00, '2026-05-21', NULL, NULL, NULL, NULL, '2026-05-21 04:34:57', '2026-05-21 04:34:57'),
(46, 'ZAY-2026-000046', 1, 'completed', NULL, 37260.00, '2026-06-15', NULL, NULL, NULL, NULL, '2026-06-05 04:49:43', '2026-06-23 08:19:41'),
(47, 'ZAY-2026-000047', 3, 'completed', NULL, 63332.00, '2026-06-24', NULL, NULL, NULL, NULL, '2026-06-23 08:28:08', '2026-06-23 08:28:13'),
(48, 'ZAY-2026-000048', 1, 'draft', NULL, 20700.00, '2026-07-01', NULL, NULL, NULL, '2026-08-17 05:09:41', '2026-06-30 09:56:49', '2026-08-17 05:09:41'),
(49, 'ZAY-2026-000049', 1, 'submitted', 'Принтера', 36800.00, '2026-09-01', 'вересня', 2026, NULL, NULL, '2026-08-17 05:09:24', '2026-08-17 05:09:47'),
(50, 'ZAY-2026-000050', 3, 'draft', '1', 0.00, '2026-08-28', NULL, 2026, NULL, '2026-08-27 05:36:05', '2026-08-27 05:35:14', '2026-08-27 05:36:05'),
(51, 'ZAY-2026-000051', 3, 'draft', NULL, 0.00, '2026-09-21', NULL, 2026, NULL, NULL, '2026-09-01 04:47:10', '2026-09-14 09:42:29'),
(52, 'ZAY-2026-000052', 1, 'draft', NULL, 51750.00, '2026-09-01', NULL, 2026, NULL, NULL, '2026-09-01 06:27:55', '2026-09-01 06:48:02'),
(53, 'ZAY-2026-000053', 3, 'submitted', NULL, 0.00, '2026-09-17', 'вересня', 2026, NULL, NULL, '2026-09-10 09:47:05', '2026-09-16 06:55:18'),
(54, 'ZAY-2026-000054', 3, 'draft', NULL, 86200.00, '2026-09-16', NULL, 2026, NULL, NULL, '2026-09-15 07:58:44', '2026-09-16 05:33:35'),
(55, 'ZAY-2026-000055', 3, 'draft', NULL, 9000.00, '2026-09-16', NULL, 2026, NULL, NULL, '2026-09-15 08:14:44', '2026-09-16 05:32:18'),
(56, 'ZAY-2026-000056', 3, 'draft', NULL, 57406.00, '2026-10-01', NULL, 2026, NULL, NULL, '2026-09-15 08:27:07', '2026-09-15 09:20:44'),
(57, 'ZAY-2026-000057', 3, 'draft', NULL, 3430.00, '2026-09-23', 'вересня', 2026, NULL, NULL, '2026-09-17 05:25:05', '2026-09-17 05:25:05'),
(58, 'ZAY-2026-000058', 3, 'draft', NULL, 0.00, '2026-09-23', NULL, 2026, NULL, NULL, '2026-09-18 07:20:12', '2026-09-21 03:52:53');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_request_items`
--

CREATE TABLE `purchase_request_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_request_id` bigint(20) UNSIGNED NOT NULL,
  `warehouse_item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_code` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit` varchar(255) NOT NULL DEFAULT 'шт',
  `estimated_price` decimal(10,2) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `specifications` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_request_items`
--

INSERT INTO `purchase_request_items` (`id`, `purchase_request_id`, `warehouse_item_id`, `item_name`, `item_code`, `quantity`, `unit`, `estimated_price`, `category`, `specifications`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 'Папір офісний А4', 'PAPER-A4-80', 10, 'пачка', 125.50, NULL, NULL, '2025-09-22 08:35:48', '2025-09-22 08:35:48'),
(2, 1, NULL, 'Миша комп\'ютерна', 'MOUSE-OPTICAL-USB', 5, 'шт', 280.00, NULL, NULL, '2025-09-22 08:35:48', '2025-09-22 08:35:48'),
(3, 2, NULL, 'Флешка USB 32GB', 'USB-32GB-KINGSTON', 3, 'шт', 450.00, NULL, NULL, '2025-09-22 09:32:36', '2025-09-22 09:32:36'),
(4, 2, NULL, 'Ручки кулькові сині', 'PEN-BLUE-1MM', 1, 'шт', 12.00, NULL, NULL, '2025-09-22 09:32:36', '2025-09-22 09:32:36'),
(5, 2, NULL, 'Папір офісний А4', 'PAPER-A4-80', 25, 'пачка', 125.50, NULL, NULL, '2025-09-22 09:32:36', '2025-09-22 09:32:36'),
(6, 2, NULL, 'Миша комп\'ютерна', 'MOUSE-OPTICAL-USB', 1, 'шт', 280.00, NULL, NULL, '2025-09-22 09:32:36', '2025-09-22 09:32:36'),
(7, 3, NULL, 'Миша комп\'ютерна', 'MOUSE-OPTICAL-USB', 5, 'шт', 280.00, NULL, NULL, '2025-09-23 03:45:25', '2025-09-23 03:45:25'),
(8, 3, NULL, 'Батарейки AA', 'BATTERY-AA-DURACELL', 10, 'упак', 89.50, NULL, NULL, '2025-09-23 03:45:25', '2025-09-23 03:45:25'),
(9, 4, NULL, 'Бумага А4', NULL, 25, 'шт', 150.00, NULL, NULL, '2025-12-16 12:26:11', '2025-12-16 12:26:11'),
(10, 4, NULL, 'Бумага туалетна', NULL, 150, 'шт', 10.00, NULL, NULL, '2025-12-16 12:26:11', '2025-12-16 12:26:11'),
(11, 5, NULL, 'Бумага А4', '123123', 10, 'шт', 150.00, NULL, NULL, '2025-12-17 06:40:59', '2025-12-17 06:40:59'),
(12, 5, NULL, 'Бумага туалетна', '321', 150, 'шт', 7.50, NULL, NULL, '2025-12-17 06:40:59', '2025-12-17 06:40:59'),
(13, 6, NULL, 'HDMI Кабель 1.5м', 'INV-6-Підвал-009', 20, 'шт', NULL, NULL, NULL, '2025-12-17 09:03:19', '2025-12-17 09:03:19'),
(14, 7, NULL, 'HDMI Кабель 1.5м', 'INV-6-Підвал-009', 2, 'шт', NULL, NULL, NULL, '2025-12-17 09:03:34', '2025-12-17 09:03:34'),
(15, 8, NULL, 'Проф. засіб для миття та очищення ванних кімнат', '30553', 5, 'каністра', NULL, NULL, NULL, '2025-12-26 12:08:13', '2025-12-26 12:08:13'),
(16, 9, NULL, 'Проф. засіб для миття та очищення ванних кімнат', '30553', 5, 'каністра', NULL, NULL, NULL, '2025-12-26 12:20:03', '2025-12-26 12:20:03'),
(17, 10, NULL, 'Проф. засіб для миття та очищення ванних кімнат', '30553', 1, 'каністра', NULL, NULL, NULL, '2025-12-29 09:46:47', '2025-12-29 09:46:47'),
(18, 11, NULL, 'трубоочистник проф.засіб для очищ. труб та каналізаціі 5000 мл.', '30556', 1, 'шт', NULL, NULL, NULL, '2025-12-31 05:46:43', '2025-12-31 05:46:43'),
(19, 11, NULL, 'Проф. засіб для миття та очищення ванних кімнат', '30553', 1, 'каністра', NULL, NULL, NULL, '2025-12-31 05:46:43', '2025-12-31 05:46:43'),
(20, 11, NULL, 'Картридж відпрпацьованих чорнил (зливний)', '69912', 2, 'шт', NULL, NULL, NULL, '2025-12-31 05:46:43', '2025-12-31 05:46:43'),
(21, 11, NULL, 'Мило рідке для шкіри рук і тіла. Бланідас Софт 5000мл.', '30535', 1, 'каністра', NULL, NULL, NULL, '2025-12-31 05:46:43', '2025-12-31 05:46:43'),
(22, 12, NULL, 'Проф. засіб для миття та очищення ванних кімнат \"Білизна Кераміка\" 5000мл', '30553', 1, 'каністра', NULL, NULL, NULL, '2025-12-31 10:12:19', '2025-12-31 10:12:19'),
(23, 13, NULL, 'Проф. засіб для миття та очищення ванних кімнат \"Білизна Кераміка\" 5000мл', '30553', 3, 'каністра', NULL, NULL, NULL, '2026-01-07 09:53:25', '2026-01-07 09:53:25'),
(24, 14, NULL, 'Базік прання. засіб для прання виробів з бавов. льону сінтет. матеріалів 5000мл', '305537', 1, 'каністра', NULL, NULL, NULL, '2026-01-07 10:52:44', '2026-01-07 10:52:44'),
(25, 14, NULL, 'Професійний засіб для очищення різноманітних поверхонь універсального використання \"Білизна антиБак\"', '888288', 1, 'каністра', NULL, NULL, NULL, '2026-01-07 10:52:44', '2026-01-07 10:52:44'),
(26, 14, NULL, 'Засіб \"Білизна плямовивідник універсальний\" 500мл', '999199', 1, 'пляшки', NULL, NULL, NULL, '2026-01-07 10:52:44', '2026-01-07 10:52:44'),
(27, 15, NULL, 'Накопичувач SSD 2.5\" 240GB Kingston', 'U0245933', 5, 'шт', 2655.00, NULL, NULL, '2026-01-09 05:56:08', '2026-01-09 05:56:08'),
(28, 16, NULL, 'Пристрій безперебійного живлення Powercom SPR-1500 LCD Powercom (SPR.1500.LCD)', 'U0415638', 1, 'шт', 15939.00, NULL, NULL, '2026-01-09 05:56:23', '2026-01-09 05:56:23'),
(29, 17, 1786, 'Проф. засіб для очищення санітарно-технічного обладнання з бак.еф. Білизна Сантехника 5000мл', '30534', 6, 'каністра', NULL, 'миючі засоби', NULL, '2026-01-09 07:51:52', '2026-03-13 08:43:53'),
(30, 17, 1784, 'Проф. засіб для миття та очищення ванних кімнат \"Білизна Кераміка\" 5000мл', '30553', 8, 'каністра', NULL, 'миючі засоби', NULL, '2026-01-09 07:51:52', '2026-03-13 08:43:53'),
(31, 17, 1787, 'Мило рідке для шкіри рук і тіла. Бланідас Софт 5000мл.', '30535', 8, 'каністра', NULL, 'миючі засоби', NULL, '2026-01-09 07:51:52', '2026-03-13 08:43:53'),
(32, 17, 1789, 'трубоочистник проф.засіб для очищ. труб та каналізаціі 5000 мл.', '30556', 6, 'каністра', NULL, 'миючі засоби', NULL, '2026-01-09 07:51:52', '2026-03-13 08:43:53'),
(33, 17, 1790, 'Базік прання. засіб для прання виробів з бавов. льону сінтет. матеріалів 5000мл', '305537', 6, 'каністра', NULL, 'миючі засоби', NULL, '2026-01-09 07:51:52', '2026-03-13 08:43:53'),
(34, 17, 1795, 'Професійний концентрований засіб для миття всіх видів поверхонь \"Білизна з ароматом грейпфрут\" 5000мл', '45441', 6, 'каністра', NULL, 'миючі засоби', NULL, '2026-01-09 07:51:52', '2026-03-13 08:43:53'),
(35, 17, 1793, 'Засіб для очищення поверхонь і нейтралізації запахів \"Білизна медкомфорт\" 750мл (з квітовим ароматом)', '221145', 30, 'пляшки', NULL, 'миючі засоби', NULL, '2026-01-09 07:51:52', '2026-03-13 08:43:53'),
(36, 17, 1799, 'Професійний засіб для очищення різноманітних поверхонь універсального використання \"Білизна антиБак\"', '888288', 2, 'каністра', NULL, 'миючі засоби', NULL, '2026-01-09 07:51:52', '2026-03-13 08:43:53'),
(37, 17, 1800, 'Засіб для ручного миття посуду \"Білихна Посуд\" 5000мл', '888388', 2, 'каністра', NULL, 'миючі засоби', NULL, '2026-01-09 07:51:52', '2026-03-13 08:43:53'),
(38, 17, 1791, 'засіб для прання білизни. Білизна проф-еліт Універсальний 5000мл', '30538', 4, 'каністра', NULL, 'миючі засоби', NULL, '2026-01-09 07:51:52', '2026-03-13 08:43:53'),
(39, 17, 1848, 'Білизна проф еліт економ, 5кг', NULL, 2, 'шт', NULL, 'миючі засоби', NULL, '2026-01-09 07:51:52', '2026-03-13 08:43:53'),
(40, 18, NULL, 'Файл прозорий \"Глянець\" 100шт', '35536', 50, 'уп', NULL, NULL, NULL, '2026-01-09 08:27:18', '2026-01-09 08:27:18'),
(41, 18, NULL, 'Папка-швидкозшивач картонна', '354777', 50, 'шт', NULL, NULL, NULL, '2026-01-09 08:27:18', '2026-01-09 08:27:18'),
(42, 18, NULL, 'Бумага офісна А4 500арк', '3053833', 200, 'шт', NULL, NULL, NULL, '2026-01-09 08:27:18', '2026-01-09 08:27:18'),
(43, 19, NULL, 'Целюлозні паперові рушники Lizoform мед 3-складання №200', '200', 560, 'уп', NULL, NULL, NULL, '2026-01-14 08:35:41', '2026-01-14 08:35:41'),
(44, 19, NULL, 'Папір туалетний в рулонах', '201', 576, 'шт', NULL, NULL, NULL, '2026-01-14 08:35:41', '2026-01-14 08:35:41'),
(45, 19, NULL, 'Туалетний папір Lizoform med-3складання №200', '202', 320, 'шт', NULL, NULL, NULL, '2026-01-14 08:35:41', '2026-01-14 08:35:41'),
(52, 21, NULL, 'Моп (запасний) до системи Vermop жовтий, 40см', '205', 15, 'шт', NULL, NULL, NULL, '2026-01-14 08:46:43', '2026-01-14 08:46:43'),
(53, 21, NULL, 'Моп (запасний) до системи Vermop зелений, 40см', '204', 15, 'шт', NULL, NULL, NULL, '2026-01-14 08:46:43', '2026-01-14 08:46:43'),
(54, 21, NULL, 'Моп (запасний) до системи Vermop синій, 40см', '203', 15, 'шт', NULL, NULL, NULL, '2026-01-14 08:46:43', '2026-01-14 08:46:43'),
(55, 21, NULL, 'Моп (запасний) до системи Vermop червоний, 40см', '206', 15, 'шт', NULL, NULL, NULL, '2026-01-14 08:46:43', '2026-01-14 08:46:43'),
(56, 21, NULL, 'Серветка Progressiv, блакитна', '209', 20, 'шт', NULL, NULL, NULL, '2026-01-14 08:46:43', '2026-01-14 08:46:43'),
(57, 21, NULL, 'Серветка Progressiv, жовта', '210', 20, 'шт', NULL, NULL, NULL, '2026-01-14 08:46:43', '2026-01-14 08:46:43'),
(58, 21, NULL, 'Серветка Progressiv, зелена', '211', 20, 'шт', NULL, NULL, NULL, '2026-01-14 08:46:43', '2026-01-14 08:46:43'),
(59, 21, NULL, 'Серветка Progressiv, червона', '212', 20, 'шт', NULL, NULL, NULL, '2026-01-14 08:46:43', '2026-01-14 08:46:43'),
(60, 21, NULL, 'Пакети для сміття 120л', '230', 50, 'шт', NULL, NULL, NULL, '2026-01-14 08:46:43', '2026-01-14 08:46:43'),
(61, 21, NULL, 'Пакети для сміття 60л', '231', 50, 'шт', NULL, NULL, NULL, '2026-01-14 08:46:43', '2026-01-14 08:46:43'),
(62, 21, NULL, 'Пакети для сміття 35л', '232', 200, 'шт', NULL, NULL, NULL, '2026-01-14 08:46:43', '2026-01-14 08:46:43'),
(63, 22, NULL, 'Папка сегригатор 50мм', '2070', 10, 'шт', NULL, NULL, NULL, '2026-01-16 11:34:10', '2026-01-16 11:34:10'),
(64, 22, NULL, 'Папка сегригатор 70мм', '20707', 25, 'шт', NULL, NULL, NULL, '2026-01-16 11:34:10', '2026-01-16 11:34:10'),
(65, 22, NULL, 'Папка на гумці', '3089', 10, 'шт', NULL, NULL, NULL, '2026-01-16 11:34:10', '2026-01-16 11:34:10'),
(66, 22, NULL, 'Швидкозшивач пластиковий А4 Economix E315-10-10 перфор-сирень', '3088', 200, 'шт', NULL, NULL, NULL, '2026-01-16 11:34:10', '2026-01-16 11:34:10'),
(67, 22, NULL, 'Клей-олівець', '3090', 30, 'шт', NULL, NULL, NULL, '2026-01-16 11:34:10', '2026-01-16 11:34:10'),
(68, 23, NULL, 'Білизна Посуд', '888388', 1, 'каністра', NULL, NULL, NULL, '2026-02-09 08:21:00', '2026-02-09 08:21:00'),
(69, 24, 1811, 'Моп (запасний) до системи Vermop жовтий, 40см', '205', 15, 'шт', NULL, NULL, NULL, '2026-02-09 10:44:28', '2026-04-03 03:58:41'),
(70, 24, 1810, 'Моп (запасний) до системи Vermop зелений, 40см', '204', 15, 'шт', NULL, NULL, NULL, '2026-02-09 10:44:28', '2026-04-03 03:58:41'),
(71, 24, 1809, 'Моп (запасний) до системи Vermop синій, 40см', '203', 15, 'шт', NULL, NULL, NULL, '2026-02-09 10:44:28', '2026-04-03 03:58:41'),
(72, 24, 1812, 'Моп (запасний) до системи Vermop червоний, 40см', '206', 15, 'шт', NULL, NULL, NULL, '2026-02-09 10:44:28', '2026-04-03 03:58:41'),
(73, 24, 1814, 'Серветка Progressiv, блакитна', '209', 20, 'шт', NULL, NULL, NULL, '2026-02-09 10:44:28', '2026-04-03 03:58:41'),
(74, 24, 1816, 'Серветка Progressiv, жовта', '210', 20, 'шт', NULL, NULL, NULL, '2026-02-09 10:44:28', '2026-04-03 03:58:41'),
(75, 24, 1817, 'Серветка Progressiv, зелена', '211', 20, 'шт', NULL, NULL, NULL, '2026-02-09 10:44:28', '2026-04-03 03:58:41'),
(76, 24, 1818, 'Серветка Progressiv, червона', '212', 20, 'шт', NULL, NULL, NULL, '2026-02-09 10:44:28', '2026-04-03 03:58:41'),
(86, 26, 1835, 'Клей-олівець', '3090', 30, 'шт', NULL, NULL, NULL, '2026-02-16 11:52:29', '2026-03-16 11:50:37'),
(87, 26, 1803, 'Бумага офісна А4 500арк', '3053833', 200, 'пач', NULL, NULL, NULL, '2026-02-16 11:52:29', '2026-03-16 11:50:37'),
(88, 26, 1834, 'Папка на гумці', '3089', 10, 'шт', NULL, NULL, NULL, '2026-02-16 11:52:29', '2026-03-16 11:50:37'),
(89, 26, 1831, 'Папка сегригатор 50мм', '2070', 10, 'шт', NULL, NULL, NULL, '2026-02-16 11:52:29', '2026-03-16 11:50:37'),
(90, 26, 1832, 'Папка сегригатор 70мм', '20707', 25, 'шт', NULL, NULL, NULL, '2026-02-16 11:52:29', '2026-03-16 11:50:37'),
(91, 26, 1804, 'Файл прозорий \"Глянець\" 100шт', '35536', 50, 'уп', NULL, NULL, NULL, '2026-02-16 11:52:29', '2026-03-16 11:50:37'),
(92, 26, 1805, 'Папка-швидкозшивач картонна', '354777', 50, 'шт', NULL, NULL, NULL, '2026-02-16 11:52:29', '2026-03-16 11:50:37'),
(93, 26, 1833, 'Швидкозшивач пластиковий А4', '3088', 200, 'шт', NULL, NULL, NULL, '2026-02-16 11:52:29', '2026-03-16 11:50:37'),
(94, 25, NULL, 'Валик для  емульсійних фарб 10см', '102-10', 5, 'шт', NULL, NULL, NULL, '2026-02-18 08:32:50', '2026-02-18 08:32:50'),
(95, 25, NULL, 'Валик для  емульсійних фарб 15см', '102-11', 5, 'шт', NULL, NULL, NULL, '2026-02-18 08:32:50', '2026-02-18 08:32:50'),
(96, 25, NULL, 'Валик для  емульсійних фарб 18см', '102-12', 5, 'шт', NULL, NULL, NULL, '2026-02-18 08:32:50', '2026-02-18 08:32:50'),
(97, 25, NULL, 'Пензлики 50мм', '218', 10, 'шт', NULL, NULL, NULL, '2026-02-18 08:32:50', '2026-02-18 08:32:50'),
(98, 25, NULL, 'Пензлики 60мм', '3053600', 10, 'шт', NULL, NULL, NULL, '2026-02-18 08:32:50', '2026-02-18 08:32:50'),
(99, 25, NULL, 'Лоток малярний', '12311-00', 3, 'шт', NULL, NULL, NULL, '2026-02-18 08:32:50', '2026-02-18 08:32:50'),
(100, 25, NULL, 'Рукавички долоні', '216', 40, 'пар', NULL, NULL, NULL, '2026-02-18 08:32:50', '2026-02-18 08:32:50'),
(103, 27, NULL, 'Віник Молдова', '214', 30, 'шт', NULL, NULL, NULL, '2026-02-18 08:50:43', '2026-02-18 08:50:43'),
(104, 27, NULL, 'Мітла березова', '207', 30, 'шт', NULL, NULL, NULL, '2026-02-18 08:50:43', '2026-02-18 08:50:43'),
(169, 30, 2020, 'Монтажна піна', NULL, 5, 'шт', NULL, NULL, NULL, '2026-02-20 05:47:25', '2026-04-28 04:24:48'),
(170, 30, 2021, 'Лак яхтовий   2,5кг', NULL, 2, 'шт', NULL, NULL, NULL, '2026-02-20 05:47:25', '2026-04-28 04:24:48'),
(171, 30, 2022, 'Фарба алкідна по бетону біла  2,5кг', NULL, 2, 'шт', NULL, NULL, NULL, '2026-02-20 05:47:25', '2026-04-28 04:24:48'),
(172, 30, 2023, 'Фарба-емаль пф-115 коричнева  2,5кг', NULL, 6, 'шт', NULL, NULL, NULL, '2026-02-20 05:47:25', '2026-04-28 04:24:48'),
(173, 30, 2024, 'Фарба-емаль пф-115 синя  2,5кг', NULL, 2, 'шт', NULL, NULL, NULL, '2026-02-20 05:47:25', '2026-04-28 04:24:48'),
(174, 30, 2025, 'Фарба-емаль пф-115 червона 2,5кг', NULL, 2, 'шт', NULL, NULL, NULL, '2026-02-20 05:47:25', '2026-04-28 04:24:48'),
(175, 30, 2026, 'Фарба-емаль пф-115 жовта 2,5кг', NULL, 2, 'шт', NULL, NULL, NULL, '2026-02-20 05:47:25', '2026-04-28 04:24:48'),
(176, 30, 1971, 'Підвісна стеля Армстронг  60*60', NULL, 100, 'шт', NULL, NULL, NULL, '2026-02-20 05:47:25', '2026-04-28 04:24:48'),
(177, 30, 2027, 'Тактильна стрічка 5см,', NULL, 30, 'м,', NULL, NULL, NULL, '2026-02-20 05:47:25', '2026-04-28 04:24:48'),
(178, 30, 2028, 'Тактильна стрічка 10см,', NULL, 20, 'м,', NULL, NULL, NULL, '2026-02-20 05:47:25', '2026-04-28 04:24:48'),
(257, 36, NULL, 'Журнал', NULL, 1, 'шт', NULL, NULL, NULL, '2026-02-26 12:06:08', '2026-02-26 12:06:08'),
(258, 36, NULL, 'Папки під анкету', NULL, 1, 'шт', NULL, NULL, NULL, '2026-02-26 12:06:08', '2026-02-26 12:06:08'),
(259, 36, NULL, 'Ручки', NULL, 1, 'шт', NULL, NULL, NULL, '2026-02-26 12:06:08', '2026-02-26 12:06:08'),
(260, 36, NULL, 'Карандаши', NULL, 1, 'шт', NULL, NULL, NULL, '2026-02-26 12:06:08', '2026-02-26 12:06:08'),
(261, 36, NULL, 'Дублікат ключів', NULL, 1, 'шт', NULL, NULL, NULL, '2026-02-26 12:06:08', '2026-02-26 12:06:08'),
(262, 36, NULL, 'Кулер', NULL, 1, 'шт', NULL, NULL, NULL, '2026-02-26 12:06:08', '2026-02-26 12:06:08'),
(263, 36, NULL, 'Відро для смиття', NULL, 1, 'шт', NULL, NULL, NULL, '2026-02-26 12:06:08', '2026-02-26 12:06:08'),
(264, 36, NULL, 'Степлер', NULL, 1, 'шт', NULL, NULL, NULL, '2026-02-26 12:06:08', '2026-02-26 12:06:08'),
(265, 36, NULL, 'Скоби', NULL, 1, 'шт', NULL, NULL, NULL, '2026-02-26 12:06:08', '2026-02-26 12:06:08'),
(266, 36, NULL, 'Лінійка', NULL, 1, 'шт', NULL, NULL, NULL, '2026-02-26 12:06:08', '2026-02-26 12:06:08'),
(267, 37, 2057, 'Пристрій безперебійного живлення PowerWalker VI 3000 RLE', NULL, 2, 'шт', 23000.00, NULL, NULL, '2026-02-27 08:15:17', '2026-06-23 08:23:27'),
(268, 37, 2058, 'Пристрій безперебійного живлення Logicpower LP-UL 1550VA, 900W', NULL, 3, 'шт', 4500.00, NULL, NULL, '2026-02-27 08:15:17', '2026-06-23 08:23:27'),
(269, 34, 1875, 'Корпус замка код:165*35', NULL, 10, 'шт', NULL, 'господарчі товари', NULL, '2026-03-03 11:11:56', '2026-04-09 05:26:00'),
(270, 34, 1876, 'Корпус замка код:35*85', NULL, 15, 'шт', NULL, 'господарчі товари', NULL, '2026-03-03 11:11:56', '2026-04-09 05:26:00'),
(335, 33, 1852, 'Поплавок з бічною подачею води1/2', NULL, 8, 'шт', NULL, NULL, NULL, '2026-03-04 12:15:08', '2026-04-01 05:28:34'),
(336, 33, 1853, 'Кріплення бачка набір', NULL, 2, 'шт', NULL, NULL, NULL, '2026-03-04 12:15:08', '2026-04-01 05:28:34'),
(337, 33, 1854, 'Силікон для сантехніки білий', NULL, 5, 'шт', NULL, NULL, NULL, '2026-03-04 12:15:08', '2026-04-01 05:28:34'),
(338, 33, 1855, 'Гофротруба для унітаза  коротка 250мм', NULL, 2, 'шт', NULL, NULL, NULL, '2026-03-04 12:15:08', '2026-04-01 05:28:34'),
(339, 33, 1856, 'Клоччя  (пакля)', NULL, 2, 'шт', NULL, NULL, NULL, '2026-03-04 12:15:08', '2026-04-01 05:28:34'),
(340, 33, 1857, 'Шланг арм,1/2 мама+1/2 мама 60см,', NULL, 10, 'шт', NULL, NULL, NULL, '2026-03-04 12:15:08', '2026-04-01 05:28:34'),
(341, 33, 1858, 'Кран маєвського 1/2', NULL, 30, 'шт', NULL, NULL, NULL, '2026-03-04 12:15:08', '2026-04-01 05:28:34'),
(342, 33, 1859, 'Кран кутовий1/2 папа+папа', NULL, 3, 'шт', NULL, NULL, NULL, '2026-03-04 12:15:08', '2026-04-01 05:28:34'),
(343, 33, 1860, 'Заглушка внутрішня1/2', NULL, 4, 'шт', NULL, NULL, NULL, '2026-03-04 12:15:08', '2026-04-01 05:28:34'),
(344, 33, 1861, 'Заглушка зовнішня1/2', NULL, 4, 'шт', NULL, NULL, NULL, '2026-03-04 12:15:08', '2026-04-01 05:28:34'),
(345, 33, 1862, 'Заглушка внутрішня3/4', NULL, 2, 'шт', NULL, NULL, NULL, '2026-03-04 12:15:08', '2026-04-01 05:28:34'),
(346, 33, 1863, 'Заглушка зовнішня 3/4', NULL, 2, 'шт', NULL, NULL, NULL, '2026-03-04 12:15:08', '2026-04-01 05:28:34'),
(347, 33, 1864, 'Фум-стрічка', NULL, 6, 'шт', NULL, NULL, NULL, '2026-03-04 12:15:08', '2026-04-01 05:28:34'),
(348, 33, 1865, 'Кран кульковий1/2мама+мама', NULL, 10, 'шт', NULL, NULL, NULL, '2026-03-04 12:15:08', '2026-04-01 05:28:34'),
(349, 33, 1866, 'Кран кульковий1/2папа+мама', NULL, 5, 'шт', NULL, NULL, NULL, '2026-03-04 12:15:08', '2026-04-01 05:28:34'),
(350, 33, 1867, 'Кран кульковий3/4мама+мама', NULL, 5, 'шт', NULL, NULL, NULL, '2026-03-04 12:15:08', '2026-04-01 05:28:34'),
(351, 33, 1868, 'Кран кульковий3/4папа+мама', NULL, 5, 'шт', NULL, NULL, NULL, '2026-03-04 12:15:08', '2026-04-01 05:28:34'),
(352, 33, 1869, 'Труба паячна гор,воду 20  3м,', NULL, 3, 'шт', NULL, NULL, NULL, '2026-03-04 12:15:08', '2026-04-01 05:28:34'),
(353, 33, 1870, 'Соєдініт,муфта  20', NULL, 8, 'шт', NULL, NULL, NULL, '2026-03-04 12:15:08', '2026-04-01 05:28:34'),
(354, 33, 1871, 'Кутник під пайку 20    90градус', NULL, 8, 'шт', NULL, NULL, NULL, '2026-03-04 12:15:08', '2026-04-01 05:28:34'),
(355, 33, 1872, 'Кутник під пайку20      45градус', NULL, 8, 'шт', NULL, NULL, NULL, '2026-03-04 12:15:08', '2026-04-01 05:28:34'),
(356, 33, 1873, 'МРТ папа+пайка20тр,', NULL, 5, 'шт', NULL, NULL, NULL, '2026-03-04 12:15:08', '2026-04-01 05:28:34'),
(357, 33, 1874, 'МРТ мама+пайка 20тр,', NULL, 5, 'шт', NULL, NULL, NULL, '2026-03-04 12:15:08', '2026-04-01 05:28:34'),
(358, 32, 1900, 'Набір свердел по металу 1-10мм', NULL, 2, 'шт', NULL, 'буд матеріали', NULL, '2026-03-05 11:44:09', '2026-04-21 11:13:37'),
(359, 32, 1901, 'Свердло по бетону 4х75 мм', NULL, 4, 'шт', NULL, 'буд матеріали', NULL, '2026-03-05 11:44:09', '2026-04-21 11:13:37'),
(360, 32, 1902, 'Свердло по бетону 6х100мм', NULL, 6, 'шт', NULL, 'буд матеріали', NULL, '2026-03-05 11:44:09', '2026-04-21 11:13:37'),
(362, 32, 1903, 'Свердло для перфорат, 6х110мм   дл-11см', NULL, 2, 'шт', NULL, 'буд матеріали', NULL, '2026-03-05 11:44:09', '2026-04-21 11:13:37'),
(365, 32, 1904, 'Набір викруток', NULL, 2, 'шт', NULL, 'буд матеріали', NULL, '2026-03-05 11:44:09', '2026-04-21 11:13:37'),
(366, 32, 1905, 'Плоскогубці 200мм Універсальні', NULL, 2, 'шт', NULL, 'буд матеріали', NULL, '2026-03-05 11:44:09', '2026-04-21 11:13:37'),
(367, 32, 1906, 'Кусачки-бокорізи 180мм', NULL, 2, 'шт', NULL, 'буд матеріали', NULL, '2026-03-05 11:44:09', '2026-04-21 11:13:37'),
(368, 32, 1907, 'Довгогубці 160мм', NULL, 2, 'шт', NULL, 'буд матеріали', NULL, '2026-03-05 11:44:09', '2026-04-21 11:13:37'),
(369, 32, 1908, 'Рулетка вимірювальна 5м', NULL, 3, 'шт', NULL, 'буд матеріали', NULL, '2026-03-05 11:44:09', '2026-04-21 11:13:37'),
(370, 32, 1909, 'Рулетка вимірювальна 10м', NULL, 2, 'шт', NULL, 'буд матеріали', NULL, '2026-03-05 11:44:09', '2026-04-21 11:13:37'),
(371, 32, 1910, 'Захистни окуляри', NULL, 2, 'шт', NULL, 'буд матеріали', NULL, '2026-03-05 11:44:09', '2026-04-21 11:13:37'),
(372, 32, 1911, 'Набір шестигранників 1,5-10мм', NULL, 2, 'шт', NULL, 'буд матеріали', NULL, '2026-03-05 11:44:09', '2026-04-21 11:13:37'),
(373, 32, 1912, 'Невеликий набір головок із тріскачкою 6-13', NULL, 1, 'шт', NULL, 'буд матеріали', NULL, '2026-03-05 11:44:09', '2026-04-21 11:13:37'),
(387, 31, NULL, 'Лопата штикова з держаком', NULL, 4, 'шт', NULL, NULL, NULL, '2026-03-05 12:03:10', '2026-03-05 12:03:10'),
(388, 31, NULL, 'Шурупокрут акумуляторний з двома акумуляторами 20В', NULL, 1, 'шт', NULL, NULL, NULL, '2026-03-05 12:03:11', '2026-03-05 12:03:11'),
(389, 31, NULL, 'Сокира з держаком', NULL, 2, 'шт', NULL, NULL, NULL, '2026-03-05 12:03:11', '2026-03-05 12:03:11'),
(390, 31, NULL, 'Молоток-кірка муляра', NULL, 2, 'шт', NULL, NULL, NULL, '2026-03-05 12:03:11', '2026-03-05 12:03:11'),
(391, 31, NULL, 'Відрізний диск по металу 125 мм', NULL, 10, 'шт', NULL, NULL, NULL, '2026-03-05 12:03:11', '2026-03-05 12:03:11'),
(392, 31, NULL, 'Шліфувальний диск по металу 125 мм', NULL, 5, 'шт', NULL, NULL, NULL, '2026-03-05 12:03:11', '2026-03-05 12:03:11'),
(393, 31, NULL, 'Терка шліфувальна із затискачем', NULL, 2, 'шт', NULL, NULL, NULL, '2026-03-05 12:03:11', '2026-03-05 12:03:11'),
(394, 31, NULL, 'Сітки шліфувальні 100 -10 шт', NULL, 3, 'шт', NULL, NULL, NULL, '2026-03-05 12:03:11', '2026-03-05 12:03:11'),
(395, 31, NULL, 'Сітки шліфувальні 120-10 шт', NULL, 3, 'шт', NULL, NULL, NULL, '2026-03-05 12:03:11', '2026-03-05 12:03:11'),
(396, 31, NULL, 'Малярний шпатель 125мм', NULL, 2, 'шт', NULL, NULL, NULL, '2026-03-05 12:03:11', '2026-03-05 12:03:11'),
(397, 31, NULL, 'Малярний шпатель 80мм', NULL, 2, 'шт', NULL, NULL, NULL, '2026-03-05 12:03:11', '2026-03-05 12:03:11'),
(398, 31, NULL, 'Малярний шпатель 60мм', NULL, 2, 'шт', NULL, NULL, NULL, '2026-03-05 12:03:11', '2026-03-05 12:03:11'),
(399, 31, NULL, 'Малярний шпатель 40', NULL, 2, 'шт', NULL, NULL, NULL, '2026-03-05 12:03:11', '2026-03-05 12:03:11'),
(400, 28, NULL, 'Секатор садовий 210мм', NULL, 2, 'шт', NULL, NULL, NULL, '2026-03-05 12:20:02', '2026-03-05 12:20:02'),
(401, 28, NULL, 'Сапа з держаком', NULL, 4, 'шт', NULL, NULL, NULL, '2026-03-05 12:20:02', '2026-03-05 12:20:02'),
(402, 28, NULL, 'Граблі кручені 6-8 зубців з держаком', NULL, 4, 'шт', NULL, NULL, NULL, '2026-03-05 12:20:02', '2026-03-05 12:20:02'),
(403, 28, NULL, 'Граблі для листя (віялові) з держаком', NULL, 4, 'шт', NULL, NULL, NULL, '2026-03-05 12:20:02', '2026-03-05 12:20:02'),
(404, 35, NULL, 'Відро для смиття 8л, металеве', NULL, 20, 'шт', NULL, NULL, NULL, '2026-03-06 05:33:39', '2026-03-06 05:33:39'),
(405, 35, NULL, 'Руковички побутові латекс(XL)2шт,', NULL, 30, 'шт', NULL, NULL, NULL, '2026-03-06 05:33:39', '2026-03-06 05:33:39'),
(444, 29, 1881, 'Кабель канал 1,5*2,5*200', NULL, 20, 'шт', NULL, 'електрика', NULL, '2026-03-06 05:50:57', '2026-04-16 09:55:58'),
(445, 29, 1882, 'Автомат-25 1Р', NULL, 5, 'шт', NULL, 'електрика', NULL, '2026-03-06 05:50:57', '2026-04-16 09:55:58'),
(446, 29, 1883, 'Автомат-16 1Р', NULL, 15, 'шт', NULL, 'електрика', NULL, '2026-03-06 05:50:57', '2026-04-16 09:55:58'),
(447, 29, 1884, 'Корпус зовнішний під 1-2 автомат з прозорою кришкою', NULL, 15, 'шт', NULL, 'електрика', NULL, '2026-03-06 05:50:57', '2026-04-16 09:55:58'),
(449, 29, 1885, 'Одномісна розетка зовнішнього монтажу', NULL, 10, 'шт', NULL, 'електрика', NULL, '2026-03-06 05:50:57', '2026-04-16 09:55:58'),
(450, 29, 1886, 'Пятимісна розетка зовнішнього монтажу', NULL, 15, 'шт', NULL, 'електрика', NULL, '2026-03-06 05:50:57', '2026-04-16 09:55:58'),
(451, 29, 1887, 'Вимикач накладний подвійний', NULL, 15, 'шт', NULL, 'електрика', NULL, '2026-03-06 05:50:57', '2026-04-16 09:55:58'),
(452, 29, 1888, 'Ізоляційна стрічка 3м', NULL, 10, 'шт', NULL, 'електрика', NULL, '2026-03-06 05:50:57', '2026-04-16 09:55:58'),
(453, 29, 1889, 'Клемні колодки 4мм', NULL, 3, 'шт', NULL, 'електрика', NULL, '2026-03-06 05:50:57', '2026-04-16 09:55:58'),
(454, 29, 1890, 'ЛЕД світильник 60х60 (під армстронг) 36w-44w', NULL, 60, 'ящ', NULL, 'електрика', NULL, '2026-03-06 05:50:57', '2026-04-16 09:55:58'),
(455, 29, 1891, 'ЛЕД лампочки Е27', NULL, 10, 'шт', NULL, 'електрика', NULL, '2026-03-06 05:50:57', '2026-04-16 09:55:58'),
(456, 29, 1892, 'Коробка установча під бетон 65*45', NULL, 20, 'шт', NULL, 'електрика', NULL, '2026-03-06 05:50:57', '2026-04-16 09:55:58'),
(457, 29, 1893, 'Хомут нейлоновий 20см (100шт)', NULL, 2, 'уп', NULL, 'електрика', NULL, '2026-03-06 05:50:57', '2026-04-16 09:55:58'),
(458, 29, 1894, 'Індикаторна викрутка', NULL, 5, 'шт', NULL, 'електрика', NULL, '2026-03-06 05:50:57', '2026-04-16 09:55:58'),
(459, 29, 1895, 'Подовжувач 4гн 2м', NULL, 5, 'шт', NULL, 'електрика', NULL, '2026-03-06 05:50:57', '2026-04-16 09:55:58'),
(460, 29, 1896, 'Подовжувач 5гн 3м', NULL, 5, 'шт', NULL, 'електрика', NULL, '2026-03-06 05:50:57', '2026-04-16 09:55:58'),
(461, 29, 1897, 'Кабель 3х2.5 мідний, багатожильний', NULL, 30, 'м', NULL, 'електрика', NULL, '2026-03-06 05:50:57', '2026-04-16 09:55:58'),
(462, 29, 1898, 'Кабель 3х1.5 мідний, багатожильний', NULL, 30, 'м', NULL, 'електрика', NULL, '2026-03-06 05:50:57', '2026-04-16 09:55:58'),
(463, 20, 1844, 'Рукавички латекс Obery XL', '213', 17, 'шт', NULL, NULL, NULL, '2026-03-13 06:50:36', '2026-03-13 07:19:43'),
(464, 20, 1823, 'Стакани одноразові 180мл прозорі 100шт', '217', 30, 'уп', NULL, NULL, NULL, '2026-03-13 06:50:36', '2026-03-13 07:19:43'),
(465, 20, 1845, 'Совок зі щіткою з довгою ручкою 90см', '215', 17, 'шт', NULL, NULL, NULL, '2026-03-13 06:50:36', '2026-03-13 07:19:43'),
(466, 38, NULL, 'Тримач туалетного рулонного паперу', NULL, 5, 'шт', NULL, NULL, NULL, '2026-03-25 11:19:39', '2026-03-30 04:12:40'),
(467, 38, NULL, 'Тримач листового туалетного паперу', NULL, 5, 'шт', NULL, NULL, NULL, '2026-03-25 11:19:39', '2026-03-30 04:12:40'),
(468, 38, NULL, 'Йоржик підлоговий ZERIX CLEVER MEO2 нерж.', NULL, 5, 'шт', NULL, NULL, NULL, '2026-03-25 11:19:39', '2026-03-30 04:12:40'),
(469, 39, 2072, 'Батарея до ДБЖ Long 12В 7Ач', NULL, 10, 'шт', 653.00, 'електрика', NULL, '2026-04-08 06:14:53', '2026-06-30 09:21:28'),
(470, 39, 2073, 'Конектор Ritar RJ45 cat.5e UTP 8P8C PREMIUM (100шт в уп)', NULL, 1, 'уп', NULL, 'орг техніка', NULL, '2026-04-08 06:14:53', '2026-06-30 09:21:28'),
(471, 39, 2074, 'Тестер кабельний Noyafa RJ-45, RJ-11', NULL, 1, 'шт', NULL, 'електрика', NULL, '2026-04-08 06:14:53', '2026-06-30 09:21:28'),
(472, 39, 2075, 'Маршрутизатор Netis WF2419E', NULL, 10, 'шт', NULL, 'орг техніка', NULL, '2026-04-08 06:14:53', '2026-06-30 09:21:28'),
(473, 39, 2076, 'Монітор ViewSonic VA220-H', NULL, 5, 'шт', NULL, 'орг техніка', NULL, '2026-04-08 06:14:53', '2026-06-30 09:21:28'),
(474, 39, 2077, 'ОЗУ DDR3 8GB 1600 MHz Prologix (PRO8GB1600D3)', NULL, 5, 'шт', NULL, 'орг техніка', NULL, '2026-04-08 06:14:53', '2026-06-30 09:21:28'),
(475, 39, 2078, 'ОЗУ DDR4 8GB 2400 MHz INTELIGENTES (IU4BHC1/8)', NULL, 5, 'шт', NULL, 'орг техніка', NULL, '2026-04-08 06:14:53', '2026-06-30 09:21:28'),
(476, 39, 2079, 'Клавіатура Logitech K120 Ukr (920-002643)', NULL, 10, 'шт', NULL, 'орг техніка', NULL, '2026-04-08 06:14:53', '2026-06-30 09:21:28'),
(477, 39, 2080, 'Мишка Logitech M100 USB Black (910-006652)', NULL, 10, 'шт', NULL, 'орг техніка', NULL, '2026-04-08 06:14:53', '2026-06-30 09:21:28'),
(478, 39, 2081, 'Мережевий подовжувач Europower EPG518, 5 роз.', NULL, 5, 'шт', NULL, 'електрика', NULL, '2026-04-08 06:14:53', '2026-06-30 09:21:28'),
(479, 39, 2082, 'Мережевий подовжувач Defender S418 1.8 m 4 роз switch white (99237)', NULL, 5, 'шт', NULL, 'електрика', NULL, '2026-04-08 06:14:53', '2026-06-30 09:21:28'),
(480, 39, NULL, 'Рукавиці діелектричні клас 0 1000В безшовні (пара)', NULL, 1, 'пара', NULL, 'електрика', NULL, '2026-04-09 03:17:29', '2026-05-28 07:55:26'),
(481, 39, 2083, 'Чіп WWM для НПК EPSON WorkForce Pro WF-M5690', NULL, 10, 'шт', 165.00, 'орг техніка', NULL, '2026-04-15 03:13:17', '2026-06-30 09:21:28'),
(483, 32, 1913, 'Набір викруток діелектричних', NULL, 1, 'шт', NULL, 'буд матеріали', NULL, '2026-04-21 11:09:59', '2026-04-21 11:13:37'),
(484, 40, NULL, 'Професійний засіб для чищення санітарно-технічного обладнання з антибактеріальним ефектом \"Білизна сантехніка\" 5000мл', '30534', 10, 'каністра', NULL, 'миючі засоби', NULL, '2026-04-30 10:06:00', '2026-05-11 10:56:40'),
(485, 40, NULL, 'Проф. засіб для миття та очищення ванних кімнат \"Білизна Кераміка\" 5000мл', '30553', 24, 'каністра', NULL, 'миючі засоби', NULL, '2026-04-30 10:06:00', '2026-05-11 10:56:40'),
(486, 40, NULL, 'Мило рідке для шкіри рук і тіла. \"Бланідас Софт\" 5000мл.', '30535', 14, 'каністра', NULL, 'миючі засоби', NULL, '2026-04-30 10:06:00', '2026-05-11 10:56:40'),
(487, 40, NULL, 'Професійний засіб для очищення труб та каналізації \"Білизна трубоочисник\" 5000мл', '115', 8, 'шт', NULL, 'миючі засоби', NULL, '2026-04-30 10:06:00', '2026-05-11 10:56:40'),
(488, 40, NULL, 'Засіб для прання виробів з бавовни, льону та синтетичних матеріалів \"Білизна базік прання\" 5000мл', '305537', 10, 'каністра', NULL, 'миючі засоби', NULL, '2026-04-30 10:06:00', '2026-05-11 10:56:40'),
(489, 40, NULL, 'Професійний концентрований засіб для миття всіх видів поверхонь \"Білизна поверхня з ароматом Грейпфрут\" 5000мл', '45441', 24, 'каністра', NULL, 'миючі засоби', NULL, '2026-04-30 10:06:00', '2026-05-11 10:56:40'),
(490, 40, NULL, 'Засіб для очищення поверхонь та нейтралізації неприємних засобів \"Білизна медкомфорт\" із квітковим ароматом 750мл', '221145', 60, 'пляшки', NULL, 'миючі засоби', NULL, '2026-04-30 10:06:00', '2026-05-11 10:56:40'),
(491, 40, NULL, 'Професійний засіб для очищення різноманітних поверхонь універсального використання \"Білизна анти бак\" 5000мл', '888288', 8, 'каністра', NULL, 'миючі засоби', NULL, '2026-04-30 10:06:00', '2026-05-11 10:56:40'),
(492, 40, NULL, 'Засіб для прання білизни \"Білизна проф-еліт Універсальний\" 5000мл', '30538', 14, 'каністра', NULL, 'миючі засоби', NULL, '2026-04-30 10:06:00', '2026-05-11 10:56:40'),
(493, 40, NULL, 'Професіний засіб для дезинфекції та очищення поверхонь \"Білизна саніхлор\" 5000мл', '333213', 6, 'каністри', NULL, 'миючі засоби', NULL, '2026-04-30 10:06:00', '2026-05-11 10:56:40'),
(494, 40, NULL, 'Засіб миючий порошкоподібний універсальний \"Білизна проф еліт економ\" 5кг,', NULL, 8, 'шт', NULL, NULL, NULL, '2026-04-30 10:06:00', '2026-05-11 10:56:40'),
(497, 41, NULL, 'Ноутбук HP 250R G9 (B3AA7AT)', NULL, 6, 'шт', 25999.00, 'орг техніка', NULL, '2026-05-08 06:23:35', '2026-05-11 09:55:36'),
(498, 41, 2067, 'Мережевий фільтр живлення Patron EXT-PN-SP-1032, 1.8m Black (EXT-PN-SP-1032)', NULL, 5, 'шт', 160.00, 'електрика', NULL, '2026-05-08 06:23:35', '2026-06-30 09:21:15'),
(499, 41, 2068, 'Стабілізатор Europower EPX-1004', NULL, 5, 'шт', 1000.00, 'орг техніка', NULL, '2026-05-08 06:23:35', '2026-06-30 09:21:15'),
(500, 41, 2069, 'Принтер етикеток X-PRINTER XP-420B usb, Ethernet (XP-420B-0082)', NULL, 5, 'шт', 4250.00, 'орг техніка', NULL, '2026-05-08 06:23:35', '2026-06-30 09:21:15'),
(501, 41, 2070, 'Сканер штрих-коду Xkancode B1 USB, Black (B1)', NULL, 5, 'шт', 1000.00, 'орг техніка', NULL, '2026-05-08 06:23:35', '2026-06-30 09:21:15'),
(502, 41, 2071, 'Мишка Logitech B100 Black (910-003357)', NULL, 5, 'шт', 400.00, 'орг техніка', NULL, '2026-05-08 06:23:35', '2026-06-30 09:21:15'),
(503, 42, 2062, 'Контейнер з чорнилом Epson 664 комплект B/C/M/Y (по70мл) L100/L200 (C13T66464A)', NULL, 4, 'шт', 1300.00, 'орг техніка', NULL, '2026-05-08 07:06:59', '2026-06-30 09:21:04'),
(504, 42, 2063, 'Картридж HP LJ 44A, для M15/M28 Black 1К (CF244A)', NULL, 2, 'шт', 3300.00, 'орг техніка', NULL, '2026-05-08 07:06:59', '2026-06-30 09:21:04'),
(505, 42, 2064, 'Драм картридж HP Imaging Drum 19A (CF219A)', NULL, 2, 'шт', 4500.00, 'орг техніка', NULL, '2026-05-08 07:06:59', '2026-06-30 09:21:04'),
(506, 42, 2065, 'Картридж HP LJ 17A, Pro M130 Black (CF217A)', NULL, 2, 'шт', 5000.00, 'орг техніка', NULL, '2026-05-08 07:06:59', '2026-06-30 09:21:04'),
(507, 42, 2066, 'Картридж WWM для Samsung SL-M2020/2070/2070FW аналог MLT-D111S Black (LC58N)', NULL, 2, 'шт', 800.00, 'орг техніка', NULL, '2026-05-08 07:06:59', '2026-06-30 09:21:04'),
(508, 40, NULL, 'Засіб для видалення плям на кольорових та білих речах,БІЛИЗНА плямовивідник універсальний', NULL, 12, 'пляш,', NULL, NULL, NULL, '2026-05-11 09:53:49', '2026-05-11 10:56:40'),
(509, 43, NULL, 'Целюлозні паперові рушники3-складанняBlanidas med', NULL, 448, 'шт', 72.00, 'господарчі товари', NULL, '2026-05-12 05:26:04', '2026-05-12 05:26:04'),
(510, 43, NULL, 'Папір туалетний Лєста', NULL, 624, 'шт', 10.20, 'господарчі товари', NULL, '2026-05-12 05:26:04', '2026-05-12 05:26:04'),
(511, 44, NULL, 'Бумага офісна А4 500арк', '3053833', 250, 'шт', 173.00, 'канцелярські товари', NULL, '2026-05-13 05:53:50', '2026-05-13 05:53:50'),
(512, 44, NULL, 'Папка-швидкозшивач картонна', '354777', 70, 'шт', 6.00, 'канцелярські товари', NULL, '2026-05-13 05:53:50', '2026-05-13 05:53:50'),
(513, 44, NULL, 'Файл прозорий \"Глянець\" 100шт', '35536', 50, 'уп', 97.00, 'канцелярські товари', NULL, '2026-05-13 05:53:50', '2026-05-13 05:53:50'),
(514, 44, NULL, 'Книга обліку А4 96л. ламінат обкладинка, клітка Axent синя', NULL, 20, 'шт', 100.00, NULL, NULL, '2026-05-13 05:53:50', '2026-05-13 05:53:50'),
(515, 45, NULL, 'Внутрішній кут для плінтуса', NULL, 30, 'шт', NULL, NULL, NULL, '2026-05-21 04:34:57', '2026-05-21 04:34:57'),
(516, 45, NULL, 'Зовнішній кут для плінтуса', NULL, 25, 'шт', NULL, NULL, NULL, '2026-05-21 04:34:57', '2026-05-21 04:34:57'),
(517, 45, NULL, 'З\'єднувач для плінтуса', NULL, 30, 'шт', NULL, NULL, NULL, '2026-05-21 04:34:57', '2026-05-21 04:34:57'),
(518, 45, NULL, 'Заглушка для плінтуса ліва', NULL, 22, 'шт', NULL, NULL, NULL, '2026-05-21 04:34:57', '2026-05-21 04:34:57'),
(519, 45, NULL, 'Заглушка для плінтуса права', NULL, 22, 'шт', NULL, NULL, NULL, '2026-05-21 04:34:57', '2026-05-21 04:34:57'),
(520, 45, NULL, 'Плінтус', NULL, 70, 'м', NULL, NULL, NULL, '2026-05-21 04:34:57', '2026-05-21 04:34:57'),
(521, 45, NULL, 'Ударні дюбелі 6*60', NULL, 5, 'уп', NULL, NULL, NULL, '2026-05-21 04:34:57', '2026-05-21 04:34:57'),
(522, 46, 2055, 'Принтер чеків SPRT POS 58 IV with auto-cut USB (SP-POS58IVU with auto-cut)', NULL, 4, 'шт', 3115.00, 'орг техніка', NULL, '2026-06-05 04:49:43', '2026-06-23 08:19:41'),
(523, 46, 2056, 'Мобільний телефон Xiaomi Redmi 15C 4/128GB Midnight Black (1163425)', NULL, 4, 'шт', 6200.00, 'орг техніка', NULL, '2026-06-05 04:49:43', '2026-06-23 08:19:41'),
(524, 47, 2059, 'Монітор 2E F2725B', NULL, 5, 'шт', 4200.00, NULL, NULL, '2026-06-23 08:28:08', '2026-06-23 08:28:13'),
(525, 47, 2060, 'Комп\'ютер Vinga Advanced A1691', NULL, 3, 'шт', 13494.00, NULL, NULL, '2026-06-23 08:28:08', '2026-06-23 08:28:13'),
(526, 47, 2061, 'Комутатор мережевий Mercusys MS105G', NULL, 5, 'шт', 370.00, NULL, NULL, '2026-06-23 08:28:08', '2026-06-23 08:28:13'),
(527, 48, NULL, 'кадридж Epson T8651', NULL, 18, 'шт', 1150.00, 'орг техніка', NULL, '2026-06-30 09:56:49', '2026-07-01 06:43:48'),
(528, 49, NULL, 'Багатофункціональний пристрій Epson EcoTank L3250 WiFi (C11CJ67412/ C11CJ67418/ C11CJ67405)', NULL, 1, 'шт', 11600.00, 'орг техніка', NULL, '2026-08-17 05:09:24', '2026-08-17 05:09:24'),
(529, 49, NULL, 'Багатофункціональний пристрій Epson M2110 (C11CJ19401)', NULL, 2, 'шт', 12600.00, 'орг техніка', NULL, '2026-08-17 05:09:24', '2026-08-17 05:09:24'),
(530, 50, NULL, 'Viko Vera білий накладна розетка 2-а з.з', '191000', 1, 'шт', NULL, NULL, NULL, '2026-08-27 05:35:14', '2026-08-27 05:35:14'),
(531, 51, NULL, 'Целюлозні паперові рушники', NULL, 1000, 'шт', NULL, 'господарчі товари', NULL, '2026-09-01 04:47:10', '2026-09-14 09:44:13'),
(532, 51, NULL, 'Мішки для сміття 120л,', NULL, 60, 'шт', NULL, 'господарчі товари', NULL, '2026-09-01 04:47:10', '2026-09-14 09:44:13'),
(533, 51, NULL, 'Мішки для сміття 35л,', NULL, 85, 'шт', NULL, 'господарчі товари', NULL, '2026-09-01 04:47:10', '2026-09-14 09:44:13'),
(534, 52, NULL, 'кадридж Epson T8651', NULL, 45, 'шт', 1150.00, NULL, NULL, '2026-09-01 06:27:55', '2026-09-01 06:48:02'),
(535, 51, NULL, 'Мішки для сміття 60л,', NULL, 30, 'шт', NULL, 'господарчі товари', NULL, '2026-09-07 06:45:15', '2026-09-14 09:44:13'),
(536, 51, NULL, 'Туалетний папір Лєста рул,', NULL, 1200, 'шт', NULL, 'господарчі товари', NULL, '2026-09-07 06:45:15', '2026-09-14 09:44:13'),
(537, 51, NULL, 'Прання порошок економ 5кг,', NULL, 8, 'шт', NULL, 'господарчі товари', NULL, '2026-09-07 06:45:15', '2026-09-14 09:44:13'),
(538, 51, NULL, 'Білизна Сантехника 5л,', NULL, 10, 'шт', NULL, 'господарчі товари', NULL, '2026-09-07 06:45:15', '2026-09-14 09:44:13'),
(539, 51, NULL, 'Білизна Трубоочистник 5л,', NULL, 10, 'шт', NULL, 'господарчі товари', NULL, '2026-09-07 06:45:15', '2026-09-14 09:44:13'),
(540, 51, NULL, 'Білизна Анти Жир 5л,', NULL, 10, 'шт', NULL, 'господарчі товари', NULL, '2026-09-07 06:45:15', '2026-09-14 09:44:13'),
(541, 51, NULL, 'Білизна Сані Хлор 5л', NULL, 6, 'шт', NULL, 'господарчі товари', NULL, '2026-09-07 06:45:15', '2026-09-14 09:44:13'),
(542, 51, NULL, 'Білизна Анти Бак 5л,', NULL, 10, 'шт', NULL, 'господарчі товари', NULL, '2026-09-07 06:45:15', '2026-09-14 09:44:13'),
(543, 51, NULL, 'Білизна Стекло 5л,', NULL, 6, 'шт', NULL, 'господарчі товари', NULL, '2026-09-07 06:45:15', '2026-09-14 09:44:13'),
(544, 51, NULL, 'Мило рідке \"Бланідас\" 5л', NULL, 14, 'шт', NULL, 'господарчі товари', NULL, '2026-09-07 06:45:15', '2026-09-14 09:44:13'),
(545, 51, NULL, 'Білизна Кераміка 5л,', NULL, 20, 'шт', NULL, 'господарчі товари', NULL, '2026-09-07 07:11:38', '2026-09-14 09:44:13'),
(546, 51, NULL, 'Білизна Грейфрукт 5л,', NULL, 18, 'шт', NULL, 'господарчі товари', NULL, '2026-09-07 07:11:38', '2026-09-14 09:44:13'),
(547, 51, NULL, 'Білизна Посуд 5л,', NULL, 10, 'шт', NULL, 'господарчі товари', NULL, '2026-09-07 07:11:38', '2026-09-14 09:44:13'),
(548, 51, NULL, 'Білизна Прання Універсальне', NULL, 12, 'шт', NULL, 'господарчі товари', NULL, '2026-09-07 07:11:38', '2026-09-14 09:44:13'),
(549, 51, NULL, 'Білизна Базік Прання', NULL, 10, 'шт', NULL, 'господарчі товари', NULL, '2026-09-07 07:11:38', '2026-09-14 09:44:13'),
(551, 53, NULL, 'Папка сегригатор 50мм', NULL, 15, 'шт', NULL, NULL, NULL, '2026-09-10 09:47:05', '2026-09-16 06:55:18'),
(552, 53, NULL, 'Папка сегригатор 70мм', NULL, 15, 'шт', NULL, NULL, NULL, '2026-09-10 09:47:05', '2026-09-16 06:55:18'),
(553, 53, NULL, 'Папка швидкозшивач А-4 з прозорим верхом 10шт, в уп,', NULL, 200, 'шт', NULL, NULL, NULL, '2026-09-10 09:47:05', '2026-09-16 06:55:18'),
(554, 53, NULL, 'Папір А-4', NULL, 200, 'шт', NULL, NULL, NULL, '2026-09-10 09:47:05', '2026-09-16 06:55:18'),
(555, 53, NULL, 'Скріпки', NULL, 60, 'шт', NULL, NULL, NULL, '2026-09-10 09:47:05', '2026-09-16 06:55:18'),
(556, 54, NULL, 'Целюлозні паперові рушники', NULL, 1000, 'шт', 73.00, NULL, NULL, '2026-09-15 07:58:44', '2026-09-16 05:33:35'),
(557, 54, NULL, 'Папір туалетний Лєста', NULL, 1200, 'шт', 11.00, NULL, NULL, '2026-09-15 07:58:44', '2026-09-16 05:33:35'),
(558, 55, NULL, 'Пакети для сміття 35л,/15шт', NULL, 120, 'шт', 20.00, NULL, NULL, '2026-09-15 08:14:44', '2026-09-16 05:32:18'),
(559, 55, NULL, 'Пакети для сміття 60л/10шт', NULL, 50, 'шт', 20.00, NULL, NULL, '2026-09-15 08:14:44', '2026-09-16 05:32:18'),
(560, 55, NULL, 'Пакети для сміття 120л/10', NULL, 100, 'шт', 56.00, NULL, NULL, '2026-09-15 08:14:44', '2026-09-16 05:32:18'),
(561, 56, NULL, 'Засіб миючий порошкоподібний універсальний \"Білизна проф еліт економ\" 5кг', 'WH-20260313104353', 8, 'каністра', 440.00, 'миючі засоби', NULL, '2026-09-15 08:27:07', '2026-09-15 09:26:09'),
(562, 56, NULL, 'Професійний засіб для чищення санітарно-технічного обладнання з антибактеріальним ефектом \"Білизна сантехніка\" 5000мл', '30534', 10, 'каністра', 651.00, 'миючі засоби', NULL, '2026-09-15 08:27:07', '2026-09-15 09:26:09'),
(563, 56, NULL, 'Професійний засіб для очищення труб та каналізації \"Білизна трубоочисник\" 5000мл', '115', 10, 'каністра', 390.00, 'миючі засоби', NULL, '2026-09-15 08:27:07', '2026-09-15 09:26:09'),
(564, 56, NULL, 'Професійний засіб для видалення застарілого жиру та нагару на поверхнях \"Білизна анти жир\" 5000мл', '65445', 10, 'каністра', 340.00, 'миючі засоби', NULL, '2026-09-15 08:27:07', '2026-09-15 09:26:09'),
(565, 56, NULL, 'Професіний засіб для дезинфекції та очищення поверхонь \"Білизна саніхлор\" 5000мл', '333213', 6, 'каністри', 333.00, 'миючі засоби', NULL, '2026-09-15 09:20:44', '2026-09-15 09:26:09'),
(566, 56, NULL, 'Професійний засіб для очищення різноманітних поверхонь універсального використання \"Білизна анти бак\" 5000мл', '888288', 10, 'каністра', 561.00, 'миючі засоби', NULL, '2026-09-15 09:20:44', '2026-09-15 09:26:09'),
(567, 56, NULL, 'Професійний засіб для миття дзеркальних та скляних поверхонь \"Білизна Скло\" 5000мл,', NULL, 6, 'шт', 225.00, NULL, NULL, '2026-09-15 09:20:44', '2026-09-15 09:26:09'),
(568, 56, NULL, 'Мило рідке для шкіри рук і тіла. \"Бланідас Софт\" 5000мл.', '30535', 14, 'каністра', 320.00, 'миючі засоби', NULL, '2026-09-15 09:20:44', '2026-09-15 09:26:09'),
(569, 56, NULL, 'Проф. засіб для миття та очищення ванних кімнат \"Білизна Кераміка\" 5000мл', '30553', 20, 'каністра', 329.00, 'миючі засоби', NULL, '2026-09-15 09:20:44', '2026-09-15 09:26:09'),
(570, 56, NULL, 'Професійний концентрований засіб для миття всіх видів поверхонь \"Білизна поверхня з ароматом грейфрукт\" 5000мл,', NULL, 18, 'шт', 336.00, NULL, NULL, '2026-09-15 09:20:44', '2026-09-15 09:26:09'),
(571, 56, NULL, 'Професійний концентрований засіб для ручного миття посуду \"Білизна посуд\" 5000мл', '888388', 10, 'каністра', 393.00, 'миючі засоби', NULL, '2026-09-15 09:20:44', '2026-09-15 09:26:09'),
(572, 56, NULL, 'Засіб для прання білизни \"Білизна проф-еліт Універсальний\" 5000мл', '30538', 12, 'каністра', 528.00, 'миючі засоби', NULL, '2026-09-15 09:20:44', '2026-09-15 09:26:09'),
(573, 56, NULL, 'Засіб для прання виробів з бавовни, льону та синтетичних матеріалів \"Білизна базік прання\" 5000мл', '305537', 6, 'каністра', 624.00, 'миючі засоби', NULL, '2026-09-15 09:20:44', '2026-09-15 09:26:09'),
(574, 53, NULL, 'Папка-швидкозшивач картонна', NULL, 30, 'шт', NULL, NULL, NULL, '2026-09-16 06:55:18', '2026-09-16 06:55:18'),
(575, 57, NULL, 'Сіль технічна 25кг,', NULL, 7, 'мішків', 490.00, NULL, NULL, '2026-09-17 05:25:05', '2026-09-17 05:25:05'),
(576, 58, NULL, 'Штукатурка стартова гіпсова IZO 30кг, I', NULL, 5, 'шт', NULL, NULL, NULL, '2026-09-18 07:20:12', '2026-09-21 05:01:27'),
(577, 58, NULL, 'Шпаклівка фінішна гіпсова SATEN 25кг', NULL, 3, 'шт', NULL, NULL, NULL, '2026-09-18 07:20:12', '2026-09-21 05:01:27'),
(578, 58, NULL, 'Грунт глибокого проникнення Cerezit CT-17 10л,', NULL, 2, 'шт', NULL, NULL, NULL, '2026-09-18 07:20:12', '2026-09-21 05:01:27'),
(579, 58, NULL, 'Фарба акрилатна для стель і стін LUX PRO біла матова 10л,', NULL, 2, 'шт', NULL, NULL, NULL, '2026-09-18 07:20:12', '2026-09-21 05:01:27'),
(580, 58, NULL, 'Профіль основний підвісної стелі 3,60м', NULL, 10, 'шт', NULL, NULL, NULL, '2026-09-18 07:20:12', '2026-09-21 05:01:27'),
(581, 58, NULL, 'Профіль поперечний білий 0,6м', NULL, 50, 'шт', NULL, NULL, NULL, '2026-09-18 07:20:12', '2026-09-21 05:01:27'),
(582, 58, NULL, 'Плита Amstrong 600*600', NULL, 54, 'шт', NULL, NULL, NULL, '2026-09-18 07:20:12', '2026-09-21 05:01:27'),
(583, 58, NULL, 'Плінтус пласт, 2,5м', NULL, 16, 'м', NULL, NULL, NULL, '2026-09-18 07:20:12', '2026-09-21 05:01:27'),
(584, 58, NULL, 'Лінолеум 3*5,6', NULL, 17, 'м', NULL, NULL, NULL, '2026-09-18 07:20:12', '2026-09-21 05:01:27'),
(585, 58, NULL, 'Наждачка сітка 80', NULL, 30, 'шт', NULL, NULL, NULL, '2026-09-21 05:01:27', '2026-09-21 05:01:27'),
(586, 58, NULL, 'Наждачка сітка 120', NULL, 20, 'шт', NULL, NULL, NULL, '2026-09-21 05:01:27', '2026-09-21 05:01:27');

-- --------------------------------------------------------

--
-- Table structure for table `repair_masters`
--

CREATE TABLE `repair_masters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `repair_masters`
--

INSERT INTO `repair_masters` (`id`, `name`, `phone`, `email`, `notes`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Пишалко', NULL, NULL, NULL, 1, '2025-08-29 09:51:15', '2025-08-29 09:51:33');

-- --------------------------------------------------------

--
-- Table structure for table `repair_orders`
--

CREATE TABLE `repair_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('draft','pending_approval','approved','rejected','sent','in_repair','completed','cancelled') NOT NULL DEFAULT 'draft',
  `repair_master_id` bigint(20) UNSIGNED DEFAULT NULL,
  `invoice_number` varchar(255) DEFAULT NULL,
  `sent_date` date DEFAULT NULL,
  `returned_date` date DEFAULT NULL,
  `total_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `repair_orders`
--

INSERT INTO `repair_orders` (`id`, `order_number`, `user_id`, `status`, `repair_master_id`, `invoice_number`, `sent_date`, `returned_date`, `total_cost`, `description`, `notes`, `approved_by`, `approved_at`, `rejection_reason`, `created_at`, `updated_at`) VALUES
(1, 'REPAIR-2026-000001', 1, 'completed', 1, NULL, NULL, '2026-05-26', 0.00, 'ремонт принтеров Epson', NULL, 1, '2026-05-19 05:17:33', NULL, '2026-05-07 07:17:58', '2026-05-26 03:33:23'),
(2, 'REPAIR-2026-000002', 1, 'draft', 1, NULL, NULL, NULL, 12670.00, 'Виконана заявка від 03.2026', NULL, NULL, NULL, NULL, '2026-05-19 05:21:02', '2026-05-19 05:22:53'),
(3, 'REPAIR-2026-000003', 1, 'draft', 1, NULL, NULL, NULL, 0.00, 'Початок ремонту', NULL, NULL, NULL, NULL, '2026-08-27 09:04:05', '2026-08-27 09:04:05');

-- --------------------------------------------------------

--
-- Table structure for table `repair_order_items`
--

CREATE TABLE `repair_order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `repair_order_id` bigint(20) UNSIGNED NOT NULL,
  `equipment_id` bigint(20) UNSIGNED NOT NULL,
  `repair_description` text NOT NULL,
  `repair_notes` text DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `returned_at` date DEFAULT NULL,
  `return_document_number` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `repair_order_items`
--

INSERT INTO `repair_order_items` (`id`, `repair_order_id`, `equipment_id`, `repair_description`, `repair_notes`, `cost`, `returned_at`, `return_document_number`, `created_at`, `updated_at`) VALUES
(5, 1, 1311, 'Розтянувся ремінь друк. голівки', NULL, NULL, '2026-05-26', NULL, '2026-05-19 05:17:28', '2026-05-26 03:33:23'),
(4, 1, 1350, 'Безкінечна помилка заминання', NULL, NULL, '2026-05-26', NULL, '2026-05-19 05:17:28', '2026-05-26 03:33:23'),
(6, 1, 449, 'Глубока очистка', NULL, NULL, '2026-05-26', NULL, '2026-05-19 05:17:28', '2026-05-26 03:33:23'),
(7, 1, 165, 'Глубока очистка', NULL, NULL, '2026-05-26', NULL, '2026-05-19 05:17:28', '2026-05-26 03:33:23'),
(24, 2, 2053, '-', NULL, 2120.00, NULL, NULL, '2026-05-19 05:22:53', '2026-05-19 05:22:53'),
(23, 2, 227, 'Ремінь', NULL, 2110.00, NULL, NULL, '2026-05-19 05:22:53', '2026-05-19 05:22:53'),
(22, 2, 1024, 'Ремінь', NULL, 2110.00, NULL, NULL, '2026-05-19 05:22:53', '2026-05-19 05:22:53'),
(21, 2, 1554, 'Ремінь', NULL, 2110.00, NULL, NULL, '2026-05-19 05:22:53', '2026-05-19 05:22:53'),
(20, 2, 1592, '-', NULL, 2110.00, NULL, NULL, '2026-05-19 05:22:53', '2026-05-19 05:22:53'),
(19, 2, 783, '-', NULL, 2110.00, NULL, NULL, '2026-05-19 05:22:53', '2026-05-19 05:22:53'),
(25, 3, 1253, 'Ремонт 037982', NULL, NULL, NULL, NULL, '2026-08-27 09:04:05', '2026-08-27 09:04:05'),
(26, 3, 658, 'Ремонт 028385', NULL, NULL, NULL, NULL, '2026-08-27 09:04:05', '2026-08-27 09:04:05');

-- --------------------------------------------------------

--
-- Table structure for table `repair_requests`
--

CREATE TABLE `repair_requests` (
  `id` int(11) NOT NULL,
  `user_telegram_id` bigint(20) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `branch_id` int(11) NOT NULL,
  `room_number` varchar(50) NOT NULL,
  `old_inventory_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` text NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `status` enum('нова','в_роботі','виконана') DEFAULT 'нова',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `repair_requests`
--

INSERT INTO `repair_requests` (`id`, `user_telegram_id`, `username`, `branch_id`, `room_number`, `old_inventory_id`, `description`, `phone`, `status`, `created_at`, `updated_at`) VALUES
(10, 542503468, 'ksenia_kalyan', 3, '205', NULL, 'Ошибка принтера, код ошибки 0xEA', '0963754537', 'виконана', '2025-08-22 08:26:47', '2026-05-27 06:48:36'),
(11, 391828916, 'Atlantis_Star', 1, '26', NULL, 'Принтер не делает распечатку с компьютера и не сканирует документы(', '0979915532', 'виконана', '2025-08-26 06:06:38', '2025-08-28 07:15:50'),
(13, 923722881, 'Elena65005', 2, '6', NULL, 'новый картридж, лимит чипа', '+380974566093', 'виконана', '2025-08-28 11:20:34', '2026-01-12 12:16:21'),
(16, 483145271, NULL, 1, '38', NULL, 'Не друкує принтер', NULL, 'виконана', '2026-01-12 11:00:22', '2026-01-12 12:16:17'),
(17, 834179772, NULL, 1, '45/4', NULL, 'Принтер не працює', NULL, 'виконана', '2026-01-15 06:21:18', '2026-01-27 05:39:56'),
(18, 834179772, NULL, 1, '45/4', NULL, 'Не працює клавіатура, мишка, монітор', NULL, 'виконана', '2026-01-23 11:20:25', '2026-01-27 05:39:46'),
(19, 634490388, 'MedikalNata', 5, '141', NULL, 'Інтернету немає в зпт. Або прокинути із сусіднього кабінету або знайти кінці в серверній аби задіяти лан розетки в самому кабінеті', NULL, 'виконана', '2026-01-26 08:34:04', '2026-01-30 11:04:22'),
(20, 1123920933, NULL, 5, '236', NULL, 'Очень медленный компьютер. Диагностика показала проблемы с хдд, прошу зафиксировать заявку на модернизацию', NULL, 'виконана', '2026-01-28 12:02:43', '2026-01-30 11:04:32'),
(21, 483145271, NULL, 1, '38', NULL, 'Немає інтернету в одному комп\'ютері', '0964016356', 'виконана', '2026-02-03 07:45:06', '2026-02-04 09:49:15'),
(22, 723971996, NULL, 3, '205', NULL, 'Не работает принтер:\nОшибка 0xEA', '0963754537', 'виконана', '2026-02-03 07:46:20', '2026-05-27 06:48:38'),
(23, 464357703, 'daria_hirzheu', 5, '201', NULL, 'Поломка принтеру, щось дуже рипить всередині і видає помилку 0хЕА', '0673253316', 'в_роботі', '2026-02-04 09:38:43', '2026-02-04 09:49:10'),
(24, 923722881, 'Elena65005', 2, '306', NULL, 'Не вмикається комп\'ютер, на чорному екрані текст', '0930887750', 'виконана', '2026-02-11 10:55:28', '2026-02-24 17:13:44'),
(25, 634490388, 'MedikalNata', 5, '216', NULL, 'Комп не бачить прінтер', '+380672918071', 'виконана', '2026-02-12 07:07:36', '2026-02-12 14:26:10'),
(26, -1093346723, NULL, 2, '302', NULL, 'Оперативка полетела', '0631859161', 'виконана', '2026-02-16 11:35:53', '2026-02-18 05:52:09'),
(27, -660894383, NULL, 2, '305', NULL, 'Не працює клавітаутура.\nВ реестатурі', '80487407369', 'виконана', '2026-02-18 14:01:35', '2026-02-24 17:14:06'),
(28, 923722881, 'Elena65005', 2, '306', NULL, 'Не заходить в Хелсі', '0930887750', 'виконана', '2026-02-23 07:39:16', '2026-02-24 17:14:10'),
(29, 1075065270, NULL, 3, '203', NULL, 'Помилкв приньерв ЕхА09', NULL, 'виконана', '2026-02-24 09:23:39', '2026-05-12 06:45:30'),
(30, 542503468, 'ksenia_kalyan', 5, '231', NULL, 'принтер  не видет бумаги', '0963636217', 'виконана', '2026-02-26 09:04:44', '2026-04-01 07:20:03'),
(31, 634490388, 'MedikalNata', 5, '216', NULL, 'Темний екран', '0672918071', 'виконана', '2026-02-27 05:28:27', '2026-04-01 07:21:01'),
(32, 923722881, 'Elena65005', 2, '306', NULL, 'Не працює принтер', '0930887750', 'виконана', '2026-03-03 06:19:27', '2026-04-01 07:21:04'),
(33, 483145271, NULL, 1, '38', NULL, 'Принтер не робить сканування', '0964016356', 'виконана', '2026-03-06 08:10:19', '2026-04-01 07:20:55'),
(34, 1363484073, 'mednatash', 1, '18', NULL, 'Нет печати', NULL, 'виконана', '2026-03-09 06:28:15', '2026-04-01 07:20:49'),
(35, 1645961754, 'Dr_Nigar_P', 2, '302', NULL, 'Замена блока', '0972233141', 'виконана', '2026-03-09 07:22:32', '2026-04-01 07:20:42'),
(36, 464357703, 'daria_hirzheu', 5, '122', NULL, 'Постійно тормозить інтернет, тормозить ноутбук у черговому кабінеті, не бачить часто флешку з ключом', '0487407143', 'виконана', '2026-03-18 06:35:06', '2026-05-12 06:45:27'),
(37, 723971996, NULL, 3, '205', NULL, 'Монитор не работает.', '0963754537', 'виконана', '2026-03-25 08:34:52', '2026-04-01 07:20:16'),
(38, 723971996, NULL, 3, '205', NULL, 'Принтер печатает бесцветно.\nКартридж новий', '0963754537', 'виконана', '2026-03-30 06:37:05', '2026-05-18 07:08:20'),
(39, 634490388, 'MedikalNata', 5, '216', NULL, 'Заміна картриджа. Підключення кардіографа до ноутбука.', '0672918071', 'виконана', '2026-04-02 05:02:16', '2026-05-12 06:45:39'),
(40, -676842999, NULL, 4, '101', NULL, 'Шумить кулер блока живлення.', NULL, 'в_роботі', '2026-04-02 08:44:56', '2026-04-08 07:19:21'),
(41, 923722881, 'Elena65005', 2, '306', NULL, 'Не заходить в інтернет, немає з\'єднання', '0930887750', 'виконана', '2026-04-06 03:40:13', '2026-04-08 07:18:58'),
(42, 1592779032, NULL, 5, '234', NULL, 'Ndjdjueueueu', NULL, 'виконана', '2026-04-08 07:12:23', '2026-04-08 07:18:48'),
(43, 944606682, NULL, 5, '130', NULL, 'Криво печатает', NULL, 'виконана', '2026-05-01 08:12:16', '2026-05-12 06:45:22'),
(44, 834179772, NULL, 1, '45/4', NULL, 'Встановити ключ', NULL, 'виконана', '2026-05-05 10:54:37', '2026-05-12 06:45:19'),
(45, 1123920933, NULL, 5, '236', NULL, 'Треба глубока очистка принтера. Звичайна очистка дюз не допомагає (більше 15 разів чистили)', NULL, 'в_роботі', '2026-05-11 11:24:36', '2026-05-12 06:45:16'),
(46, 1441177784, NULL, 5, '124', NULL, 'Заміна хдд', NULL, 'виконана', '2026-05-12 07:23:27', '2026-05-18 07:08:07'),
(47, 860962904, NULL, 5, '202', NULL, 'В режимі двобічного друку принтер \"зажовує\" папір, при односторонньому друку такого немає.', '0930859570', 'виконана', '2026-05-15 04:13:40', '2026-05-19 05:30:51'),
(48, 1269824605, NULL, 1, '7a', NULL, 'Бледно печатает', NULL, 'виконана', '2026-05-19 09:47:43', '2026-05-19 10:10:18'),
(49, 1269824605, NULL, 1, '7а', NULL, 'Очень бледно печатает', NULL, 'виконана', '2026-05-20 08:21:11', '2026-05-25 09:00:41'),
(50, 483145271, NULL, 1, '38', NULL, 'Не працює комп\'ютер', '0964016356', 'виконана', '2026-05-29 02:29:25', '2026-05-29 10:31:06'),
(51, 821484901, NULL, 5, 'Реестратура', NULL, 'Компьютер плохо работает', NULL, 'в_роботі', '2026-05-29 05:44:40', '2026-05-29 10:31:12'),
(52, 1269824605, NULL, 1, '7а', NULL, 'Не друкує формат а5', NULL, 'виконана', '2026-06-02 10:23:37', '2026-06-02 10:30:40'),
(53, 330489980, 'metamorf_dev', 2, '303', NULL, 'Витікає картридж відпрацьованих чорнил', NULL, 'виконана', '2026-06-04 05:53:55', '2026-08-26 16:00:20'),
(54, 1269824605, NULL, 1, '7а', NULL, 'Не завантажується принтер', NULL, 'виконана', '2026-06-05 08:37:46', '2026-08-26 16:00:26'),
(55, 965198208, NULL, 1, '40', NULL, 'Вимкнувся пк', NULL, 'виконана', '2026-06-17 06:57:26', '2026-08-26 16:00:30'),
(56, 923722881, 'Elena65005', 2, '306', NULL, 'Принтер не сканує', '0930887750', 'виконана', '2026-06-19 06:05:34', '2026-08-26 16:00:32'),
(57, 923722881, 'Elena65005', 2, '302', NULL, 'Принтер не друкує', '0973754038', 'виконана', '2026-06-19 06:08:50', '2026-08-26 16:00:34'),
(58, -660894383, NULL, 2, '306', NULL, 'Не працює \n\nПечать в 306.\nКаб\nLibreOffice', '30930887750', 'виконана', '2026-07-01 03:23:38', '2026-08-26 16:00:37'),
(59, 634490388, 'MedikalNata', 5, '216', NULL, 'Немає зв\'язку кардіографа з ноутбуком', '0672918071', 'в_роботі', '2026-07-01 11:41:29', '2026-08-26 16:00:39'),
(60, -660894383, NULL, 2, '306', NULL, '306 кабінет\n\nПрацює сканер, неможливо \n\nСканувати.\nНе працює', '30930887750', 'виконана', '2026-07-01 14:03:03', '2026-08-26 16:00:43'),
(61, 483145271, NULL, 1, '28', NULL, 'Не можу зайти у профіль Helsi. Видає помилку.', '0964016356', 'виконана', '2026-07-03 00:58:46', '2026-08-26 16:00:47'),
(62, 464357703, 'daria_hirzheu', 5, '201', NULL, 'Підключити екг апарат до ноутбука, можна через anidesk, бо з екг і ноутом звязок є і підключення є, а сам апарат не передає екг', '0673253316', 'виконана', '2026-08-17 05:52:46', '2026-09-06 08:55:01'),
(63, 965198208, NULL, 1, '40', NULL, 'Не працює принтер', '0992763860', 'виконана', '2026-08-18 11:41:35', '2026-08-26 16:00:53'),
(64, 1269824605, NULL, 1, '7а', NULL, '7а каб з пʼтниці не працює Інтернет на компʼютері мед.сестри', NULL, 'виконана', '2026-08-24 02:30:31', '2026-08-26 16:00:55'),
(65, 7929040209, NULL, 2, '306', NULL, 'Не працюе сканер', '30930887750', 'виконана', '2026-08-24 15:03:17', '2026-09-06 08:54:58'),
(66, 464357703, 'daria_hirzheu', 5, '201', NULL, 'Зламався принтер', '0673253316', 'виконана', '2026-08-26 07:17:45', '2026-09-06 08:54:40'),
(67, 330489980, 'metamorf_dev', 1, '27', NULL, 'пробс', NULL, 'виконана', '2026-08-26 16:03:20', '2026-08-26 16:03:38'),
(68, 8490356310, 'sergio_devs', 1, '27', NULL, 'Hhggtyvcfffv hhhb', NULL, 'виконана', '2026-08-26 17:31:10', '2026-08-26 17:31:42'),
(69, 7929040209, NULL, 2, '306', NULL, 'Не включается компютер', '380930887750', 'нова', '2026-09-03 04:19:12', '2026-09-03 04:19:12'),
(70, 8958347958, NULL, 2, 'Talk in English', NULL, '⏭️ Пропустити', NULL, 'виконана', '2026-09-06 06:44:54', '2026-09-06 08:54:48'),
(71, 923722881, 'Elena65005', 2, '306', NULL, 'Не працює мишка', '+380974566093', 'нова', '2026-09-07 07:52:35', '2026-09-07 07:52:35'),
(72, 464357703, 'daria_hirzheu', 5, '202', NULL, 'Налаштувати екг апарат з ноутбуком', '0673253316', 'нова', '2026-09-17 07:07:12', '2026-09-17 07:07:12'),
(73, 1748926034, NULL, 1, '5y', NULL, 'Styugrtjhrt', NULL, 'нова', '2026-09-21 06:42:46', '2026-09-21 06:42:46');

-- --------------------------------------------------------

--
-- Table structure for table `repair_trackings`
--

CREATE TABLE `repair_trackings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `equipment_id` bigint(20) UNSIGNED NOT NULL,
  `repair_master_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sent_date` date NOT NULL,
  `returned_date` date DEFAULT NULL,
  `invoice_number` varchar(255) DEFAULT NULL,
  `our_description` text NOT NULL,
  `repair_description` text DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `status` enum('sent','in_repair','completed','cancelled') NOT NULL DEFAULT 'sent',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `repair_trackings`
--

INSERT INTO `repair_trackings` (`id`, `equipment_id`, `repair_master_id`, `sent_date`, `returned_date`, `invoice_number`, `our_description`, `repair_description`, `cost`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1628, 1, '2025-12-15', NULL, 'ЕПІ156', 'зламана', NULL, 150.00, 'sent', NULL, '2025-12-17 06:44:21', '2025-12-17 06:44:21');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `room_inventory`
--

CREATE TABLE `room_inventory` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `admin_telegram_id` bigint(20) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `room_number` varchar(50) NOT NULL,
  `template_id` int(11) DEFAULT NULL,
  `equipment_type` varchar(100) NOT NULL,
  `full_name` text DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `is_priority` tinyint(1) NOT NULL DEFAULT 0,
  `balance_code` varchar(100) DEFAULT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `serial_number` varchar(255) DEFAULT NULL,
  `inventory_number` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `unit` varchar(20) NOT NULL DEFAULT 'шт',
  `price` decimal(10,2) DEFAULT NULL,
  `min_quantity` int(11) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `room_inventory`
--

INSERT INTO `room_inventory` (`id`, `admin_telegram_id`, `branch_id`, `room_number`, `template_id`, `equipment_type`, `full_name`, `category`, `is_priority`, `balance_code`, `brand`, `model`, `serial_number`, `inventory_number`, `quantity`, `unit`, `price`, `min_quantity`, `notes`, `created_at`) VALUES
(2, 330489980, 5, '119', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', 'UV4Y039053', '11200174', 1, 'шт', NULL, 0, 'Монітор + ПК + миша + клавіатура - комплект', '2025-11-10 07:05:00'),
(3, 330489980, 5, '119', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, 'gamemax', NULL, '107926', '11200173', 1, 'шт', NULL, 0, 'Монітор + ПК + миша + клавіатура - комплект', '2025-11-10 07:05:00'),
(4, 330489980, 5, '119', NULL, 'Клавіатура', NULL, NULL, 0, NULL, 'Genius', 'GK', 'XP18S9101028', '11200173', 1, 'шт', NULL, 0, 'Монітор + ПК + миша + клавіатура - комплект', '2025-11-10 07:05:00'),
(5, 330489980, 5, '119', NULL, 'Миша', NULL, NULL, 0, NULL, 'a4Tech', NULL, 'EU2009014922', '-', 1, 'шт', NULL, 0, 'Монітор + ПК + миша + клавіатура - комплект', '2025-11-10 07:05:00'),
(6, 330489980, 5, '119', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, '11200173', '11200173', 1, 'шт', NULL, 0, 'Монітор + ПК + миша + клавіатура - комплект', '2025-11-10 07:05:00'),
(7, 330489980, 5, '119', NULL, 'Колонки', NULL, NULL, 0, NULL, '2E', NULL, 'PCS234BK', '11200346', 1, 'шт', NULL, 0, 'Монітор + ПК + миша + клавіатура - комплект', '2025-11-10 07:05:00'),
(8, 330489980, 5, '119', NULL, 'Камера', NULL, NULL, 0, NULL, 'Webcam', NULL, NULL, '11200345', 1, 'шт', NULL, 0, 'Монітор + ПК + миша + клавіатура - комплект', '2025-11-10 07:05:00'),
(9, 330489980, 5, '120', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', 'UV4Y038516', '11200174-1', 1, 'шт', NULL, 0, 'ПК - комплектом', '2025-11-10 07:12:09'),
(10, 330489980, 5, '120', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, 'gamemax', NULL, '107921', '11200170', 1, 'шт', NULL, 0, 'ПК - комплектом', '2025-11-10 07:12:09'),
(11, 330489980, 5, '120', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, '74601991824', '11200170', 1, 'шт', NULL, 0, 'ПК - комплектом', '2025-11-10 07:12:09'),
(12, 330489980, 5, '120', NULL, 'Клавіатура', NULL, NULL, 0, NULL, 'Genius', NULL, 'XP18S9101368', '11200170', 1, 'шт', NULL, 0, 'ПК - комплектом', '2025-11-10 07:12:09'),
(13, 330489980, 5, '120', NULL, 'Миша', NULL, NULL, 0, NULL, 'Logitech', 'B100', '2139HS039UJ8', '11200115', 1, 'шт', NULL, 0, 'ПК - комплектом', '2025-11-10 07:12:09'),
(14, 330489980, 5, '120', NULL, 'Колонки', NULL, NULL, 0, NULL, NULL, NULL, '2E-PSC245BK', '11200346-1', 1, 'шт', NULL, 0, 'ПК - комплектом', '2025-11-10 07:12:09'),
(15, 330489980, 5, '120', NULL, 'Камера', NULL, NULL, 0, NULL, NULL, NULL, '-', '11200345-1', 1, 'шт', NULL, 0, 'ПК - комплектом', '2025-11-10 07:12:09'),
(16, 330489980, 5, '121', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', 'UV4Y028551', '11200174-2', 1, 'шт', NULL, 0, 'комплект пк', '2025-11-10 07:20:35'),
(17, 330489980, 5, '121', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, 'LogicPower', NULL, NULL, '11200172-121', 1, 'шт', NULL, 0, 'комплект пк', '2025-11-10 07:20:35'),
(18, 330489980, 5, '121', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, '20900115385', '11200172-1211', 1, 'шт', NULL, 0, 'комплект пк', '2025-11-10 07:20:35'),
(19, 330489980, 5, '121', NULL, 'Камера', NULL, NULL, 0, NULL, 'Webcam', NULL, NULL, '11200345-2', 1, 'шт', NULL, 0, 'комплект пк', '2025-11-10 07:20:35'),
(20, 330489980, 5, '121', NULL, 'Колонки', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200346-2', 1, 'шт', NULL, 0, 'комплект пк', '2025-11-10 07:20:35'),
(21, 330489980, 5, '121', NULL, 'Клавіатура', NULL, NULL, 0, NULL, 'Genius', NULL, NULL, '1137731-3', 1, 'шт', NULL, 0, 'комплект пк', '2025-11-10 07:20:35'),
(22, 330489980, 5, '121', NULL, 'Миша', NULL, NULL, 0, NULL, 'Gemix', 'KBM-180', NULL, '1137731-4', 1, 'шт', NULL, 0, 'комплект пк', '2025-11-10 07:20:35'),
(23, 330489980, 5, '122', NULL, 'Ноутбук', NULL, NULL, 0, NULL, 'Lenovo', NULL, 'PP2XTYE9', '1046056', 1, 'шт', NULL, 0, NULL, '2025-11-10 07:23:43'),
(24, 330489980, 5, '122', NULL, 'Роутер', NULL, NULL, 0, NULL, 'Netis', NULL, NULL, 'INV-5-122-002', 1, 'шт', NULL, 0, NULL, '2025-11-10 07:23:43'),
(25, 330489980, 5, '122', NULL, 'Миша', NULL, NULL, 0, NULL, 'Gemix', NULL, NULL, 'INV-5-122-003', 1, 'шт', NULL, 0, NULL, '2025-11-10 07:23:43'),
(26, 330489980, 5, '120', NULL, 'Роутер', NULL, NULL, 0, NULL, 'Netis', NULL, NULL, 'INV-5-120-001', 1, 'шт', NULL, 0, NULL, '2025-11-10 07:24:06'),
(27, 330489980, 5, '119', NULL, 'Роутер', NULL, NULL, 0, NULL, 'Netis', NULL, NULL, 'INV-5-119-001', 1, 'шт', NULL, 0, NULL, '2025-11-10 07:24:27'),
(28, 330489980, 5, '124', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'L1250', 'X8LQ005509', '11200374', 1, 'шт', NULL, 0, NULL, '2025-11-10 07:33:08'),
(29, 330489980, 5, '124', NULL, 'Ноутбук', NULL, NULL, 0, NULL, 'Lenovo', NULL, 'PF2XBXBC', '1046054', 1, 'шт', NULL, 0, NULL, '2025-11-10 07:33:08'),
(30, 330489980, 5, '124', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', 'V226HQL', '74601117924', '11200173-3', 1, 'шт', NULL, 0, NULL, '2025-11-10 07:33:08'),
(31, 330489980, 5, '124', NULL, 'Миша', NULL, NULL, 0, NULL, 'Genius', NULL, 'XP18S9101038', '11200173-4', 1, 'шт', NULL, 0, NULL, '2025-11-10 07:33:08'),
(32, 330489980, 5, '124', NULL, 'Клавіатура', NULL, NULL, 0, NULL, 'Genius', NULL, 'XP18S9101026', '11200173-5', 1, 'шт', NULL, 0, NULL, '2025-11-10 07:33:08'),
(33, 330489980, 5, '124', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, 'LogicPower', NULL, NULL, '11200173-6', 1, 'шт', NULL, 0, NULL, '2025-11-10 07:33:08'),
(34, 330489980, 5, '124', NULL, 'Роутер', NULL, NULL, 0, NULL, 'Netis', NULL, NULL, 'INV-007', 1, 'шт', NULL, 0, NULL, '2025-11-10 07:33:08'),
(35, 330489980, 5, 'Реєстратура', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, 'LogicPower', NULL, NULL, '11200170-2', 1, 'шт', NULL, 0, NULL, '2025-11-10 07:42:52'),
(36, 330489980, 5, 'Реєстратура', NULL, 'ДБЖ', NULL, NULL, 0, NULL, 'ECM', NULL, '40225291710', '11200223', 1, 'шт', NULL, 0, NULL, '2025-11-10 07:42:52'),
(37, 330489980, 5, 'Реєстратура', NULL, 'Телефон', NULL, NULL, 0, NULL, 'Fanvil', NULL, NULL, '1124572', 1, 'шт', NULL, 0, NULL, '2025-11-10 07:42:52'),
(38, 330489980, 5, 'Реєстратура', NULL, 'Телефон', NULL, NULL, 0, NULL, 'Fanvil', NULL, NULL, '11200255', 1, 'шт', NULL, 0, NULL, '2025-11-10 07:42:52'),
(39, 330489980, 5, 'Реєстратура', NULL, 'Миша', NULL, NULL, 0, NULL, 'Jeqant', NULL, NULL, '11100260', 1, 'шт', NULL, 0, NULL, '2025-11-10 07:42:52'),
(40, 330489980, 5, 'Реєстратура', NULL, 'Клавіатура', NULL, NULL, 0, NULL, 'Sven', NULL, NULL, '11200170-3', 1, 'шт', NULL, 0, NULL, '2025-11-10 07:42:52'),
(41, 330489980, 5, 'Реєстратура', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '11200170-4', 1, 'шт', NULL, 0, NULL, '2025-11-10 07:42:52'),
(42, 330489980, 5, 'Серверна', NULL, 'Тірас 16-П', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200220', 1, 'шт', NULL, 0, NULL, '2025-11-10 07:57:03'),
(43, 330489980, 5, 'Серверна', NULL, 'Тірас', NULL, NULL, 0, NULL, 'БЖ 1230', NULL, NULL, '11200274', 1, 'шт', NULL, 0, NULL, '2025-11-10 07:57:03'),
(44, 330489980, 5, 'Серверна', NULL, 'Тірас', NULL, NULL, 0, NULL, 'БУ', NULL, NULL, '11200263', 1, 'шт', NULL, 0, NULL, '2025-11-10 07:57:03'),
(45, 330489980, 5, 'Серверна', NULL, 'Лунь 11', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200266', 1, 'шт', NULL, 0, NULL, '2025-11-10 07:57:03'),
(46, 330489980, 5, 'Серверна', NULL, 'Vellez', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1046028/11200217/11200176/11200224/11200/218/11200221', 1, 'шт', NULL, 0, NULL, '2025-11-10 07:57:03'),
(47, 330489980, 5, 'Серверна', NULL, 'Vellez RMS', NULL, NULL, 0, NULL, NULL, NULL, '001649', '11100219', 1, 'шт', NULL, 0, NULL, '2025-11-10 07:57:03'),
(48, 330489980, 5, 'Серверна', NULL, 'Сервер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1046018/1046019/1046024/1046023/11200269/11200216/11200223/11200222/11200177/11200273/11200212/11200264/11200213', 1, 'шт', NULL, 0, NULL, '2025-11-10 07:57:03'),
(49, 330489980, 5, 'Серверна', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '101480165', 1, 'шт', NULL, 0, NULL, '2025-11-10 07:57:03'),
(50, 330489980, 5, 'Серверна', NULL, 'Миша', NULL, NULL, 0, NULL, 'Dell', NULL, NULL, 'INV-5-Серверна-009', 1, 'шт', NULL, 0, NULL, '2025-11-10 07:57:03'),
(51, 330489980, 5, 'Серверна', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, 'Gree', NULL, '87448', '1046042', 1, 'шт', NULL, 0, NULL, '2025-11-10 07:58:51'),
(52, 330489980, 5, 'Реєстратура', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, 'Nordis', NULL, NULL, '11200230', 1, 'шт', NULL, 0, NULL, '2025-11-10 08:00:08'),
(53, 330489980, 5, 'Реєстратура', NULL, 'Телевізор', NULL, NULL, 0, NULL, 'LG', NULL, NULL, '1048010', 1, 'шт', NULL, 0, NULL, '2025-11-10 08:01:25'),
(54, 330489980, 6, 'Підвал', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Samsung', 'M2070', 'CNB2KCHGBH', 'INV-5-Склад-001', 1, 'шт', NULL, 0, NULL, '2025-11-10 08:37:50'),
(55, 330489980, 5, 'Склад', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'OLD', NULL, '11200174-3', 1, 'шт', NULL, 0, NULL, '2025-11-10 08:37:50'),
(56, 330489980, 5, 'Склад', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', 'UV4Y028503', '11200174-4', 1, 'шт', NULL, 0, NULL, '2025-11-10 08:37:50'),
(57, 330489980, 5, 'Склад', NULL, 'Проектор', NULL, NULL, 0, NULL, 'Optoma', NULL, NULL, '1048009', 1, 'шт', NULL, 0, NULL, '2025-11-10 08:37:50'),
(58, 330489980, 5, 'Склад', NULL, 'Проектор', NULL, NULL, 0, NULL, 'Optoma', NULL, NULL, '1048013', 1, 'шт', NULL, 0, NULL, '2025-11-10 08:37:50'),
(59, 330489980, 5, 'Склад', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Samsung', 'SCX-4226', '9C66BFEZ402130L', '11200200', 1, 'шт', NULL, 0, NULL, '2025-11-10 08:37:50'),
(60, 330489980, 5, 'Склад', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Samsung', 'ML-2851ND', '4F66BAXZ300011L', '11200198', 1, 'шт', NULL, 0, NULL, '2025-11-10 08:37:50'),
(61, 330489980, 5, 'Склад', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Canon', NULL, 'NBAA243704', '11200202', 1, 'шт', NULL, 0, NULL, '2025-11-10 08:37:50'),
(62, 330489980, 5, 'Склад', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Canon', NULL, 'NBAA154926', '11200201', 1, 'шт', NULL, 0, NULL, '2025-11-10 08:37:50'),
(63, 330489980, 5, 'Склад', NULL, 'Принтер', NULL, NULL, 0, NULL, 'HP', NULL, 'VNC7403369', '11200197', 1, 'шт', NULL, 0, NULL, '2025-11-10 08:37:50'),
(64, 330489980, 5, 'Склад', NULL, 'Принтер', NULL, NULL, 0, NULL, 'HP', NULL, 'VNCRK49668', '11200299', 1, 'шт', NULL, 0, NULL, '2025-11-10 08:37:50'),
(65, 330489980, 5, 'Склад', NULL, 'Ноутбук', NULL, NULL, 0, NULL, 'HP', NULL, NULL, '11200262', 1, 'шт', NULL, 0, NULL, '2025-11-10 08:37:50'),
(66, 330489980, 5, 'Склад', NULL, 'Ноутбук', NULL, NULL, 0, NULL, 'HP', NULL, NULL, '11200262-2', 1, 'шт', NULL, 0, NULL, '2025-11-10 08:37:50'),
(67, 330489980, 5, 'Склад', NULL, 'Ноутбук', NULL, NULL, 0, NULL, 'HP', NULL, NULL, '11200209', 1, 'шт', NULL, 0, NULL, '2025-11-10 08:37:50'),
(68, 330489980, 5, 'Склад', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, 'LogicPower', NULL, NULL, '11200244', 1, 'шт', NULL, 0, NULL, '2025-11-10 08:37:50'),
(69, 330489980, 5, 'Склад', NULL, 'Клавіатура', NULL, NULL, 0, NULL, 'A4Tech', NULL, NULL, '11200259', 1, 'шт', NULL, 0, NULL, '2025-11-10 08:37:50'),
(70, 330489980, 5, 'Склад', NULL, 'Колонки', NULL, NULL, 0, NULL, 'Multimedia', NULL, NULL, '11200346-3', 1, 'шт', NULL, 0, NULL, '2025-11-10 08:37:50'),
(71, 330489980, 5, 'Склад', NULL, 'Колонки', NULL, NULL, 0, NULL, 'Multimedia', NULL, NULL, '11200346-4', 1, 'шт', NULL, 0, NULL, '2025-11-10 08:37:50'),
(72, 330489980, 5, 'Склад', NULL, 'Колонки', NULL, NULL, 0, NULL, 'Multimedia', NULL, NULL, '11200346-5', 1, 'шт', NULL, 0, NULL, '2025-11-10 08:37:50'),
(73, 330489980, 5, 'Склад', NULL, 'Камера', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200345-3', 1, 'шт', NULL, 0, NULL, '2025-11-10 08:37:50'),
(74, 330489980, 5, 'Склад', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, 'К-120', NULL, 'INV-5-Склад-024', 1, 'шт', NULL, 0, NULL, '2025-11-10 08:37:50'),
(75, 330489980, 5, 'Склад', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, 'К-120', NULL, 'INV-5-Склад-025', 1, 'шт', NULL, 0, NULL, '2025-11-10 08:37:50'),
(76, 330489980, 5, 'Склад', NULL, 'Клавіатура', NULL, NULL, 0, NULL, 'Genius', NULL, NULL, 'INV-5-Склад-026', 1, 'шт', NULL, 0, NULL, '2025-11-10 08:37:50'),
(77, 330489980, 5, 'Склад', NULL, 'Клавіатура', NULL, NULL, 0, NULL, 'Genius', NULL, NULL, 'INV-5-Склад-027', 1, 'шт', NULL, 0, NULL, '2025-11-10 08:37:50'),
(78, 330489980, 5, 'Склад', NULL, 'Клавіатура', NULL, NULL, 0, NULL, 'Genius', NULL, NULL, 'INV-5-Склад-028', 1, 'шт', NULL, 0, NULL, '2025-11-10 08:37:50'),
(79, 330489980, 5, 'Склад', NULL, 'Клавіатура', NULL, NULL, 0, NULL, 'Gemix', NULL, NULL, 'INV-5-Склад-029', 1, 'шт', NULL, 0, NULL, '2025-11-10 08:37:50'),
(80, 330489980, 5, 'Склад', NULL, 'Клавіатура', NULL, NULL, 0, NULL, 'A4Tech', NULL, NULL, 'INV-5-Склад-030', 1, 'шт', NULL, 0, NULL, '2025-11-10 08:37:50'),
(81, 330489980, 5, 'Склад', NULL, 'Монітор', NULL, NULL, 0, NULL, 'LG', NULL, '606NTHM7P313', '10148092', 1, 'шт', NULL, 0, NULL, '2025-11-10 08:37:50'),
(82, 330489980, 5, 'Склад', NULL, 'Монітор', NULL, NULL, 0, NULL, 'LG', NULL, '410NDX007106', 'INV-5-Склад-032', 1, 'шт', NULL, 0, NULL, '2025-11-10 08:37:50'),
(83, 330489980, 5, 'Склад', NULL, 'Телефон', NULL, NULL, 0, NULL, 'Fanvil', NULL, NULL, '11200255-2', 1, 'шт', NULL, 0, NULL, '2025-11-10 08:37:50'),
(84, 330489980, 5, 'Склад', NULL, 'Телефон', NULL, NULL, 0, NULL, 'Fanvil', NULL, NULL, '11200255-3', 1, 'шт', NULL, 0, NULL, '2025-11-10 08:37:50'),
(85, 330489980, 5, '141', NULL, 'Монітор', NULL, NULL, 0, NULL, 'LG', NULL, '606NTYT7P314', '10480159', 1, 'шт', NULL, 0, NULL, '2025-11-10 08:45:45'),
(86, 330489980, 5, '141', NULL, 'Клавіатура', NULL, NULL, 0, NULL, 'Sven', NULL, 'SV1601MT23684', 'INV-5-141-002', 1, 'шт', NULL, 0, NULL, '2025-11-10 08:45:45'),
(87, 330489980, 5, '141', NULL, 'Ноутбук', NULL, NULL, 0, NULL, 'Lenovo', NULL, 'PF2Z25RY', '1046052', 1, 'шт', NULL, 0, NULL, '2025-11-10 08:45:45'),
(88, 330489980, 5, '141', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Canon', 'MF232W', 'WRD64395', '11200202-2', 1, 'шт', NULL, 0, NULL, '2025-11-10 08:45:45'),
(89, 330489980, 5, '141', NULL, 'Миша', NULL, NULL, 0, NULL, 'Logitech', NULL, NULL, 'INV-5-141-005', 1, 'шт', NULL, 0, NULL, '2025-11-10 08:45:45'),
(90, 330489980, 5, '209', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, 'OSAKA', NULL, NULL, '1063261', 1, 'шт', NULL, 0, NULL, '2025-11-10 09:42:32'),
(91, 330489980, 5, '202', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', 'UV4Y038761', '11200174-5', 1, 'шт', NULL, 0, NULL, '2025-11-10 09:47:49'),
(92, 330489980, 5, '202', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, 'gamemax', NULL, '107945', '11200173-8', 1, 'шт', NULL, 0, NULL, '2025-11-10 09:47:49'),
(93, 330489980, 5, '202', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, '74601137724', '11200173-7', 1, 'шт', NULL, 0, NULL, '2025-11-10 09:47:49'),
(94, 330489980, 5, '202', NULL, 'Клавіатура', NULL, NULL, 0, NULL, 'Genius', NULL, NULL, '11200173-9', 1, 'шт', NULL, 0, NULL, '2025-11-10 09:47:49'),
(95, 330489980, 5, '202', NULL, 'Роутер', NULL, NULL, 0, NULL, 'netis', NULL, NULL, 'INV-5-202-005', 1, 'шт', NULL, 0, NULL, '2025-11-10 09:47:49'),
(96, 330489980, 5, '201', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', 'UV4Y038709', '11200174-10', 1, 'шт', NULL, 0, NULL, '2025-11-10 09:56:04'),
(97, 330489980, 5, '201', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, 'GreenVision', NULL, NULL, '11200173-201', 1, 'шт', NULL, 0, NULL, '2025-11-10 09:56:04'),
(98, 330489980, 5, '201', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '11200173-2', 1, 'шт', NULL, 0, NULL, '2025-11-10 09:56:04'),
(99, 330489980, 5, '201', NULL, 'Миша', NULL, NULL, 0, NULL, 'Logitech', NULL, NULL, '11200173-3201', 1, 'шт', NULL, 0, NULL, '2025-11-10 09:56:04'),
(100, 330489980, 5, '201', NULL, 'Клавіатура', NULL, NULL, 0, NULL, 'Genius', NULL, NULL, '11200388', 1, 'шт', NULL, 0, NULL, '2025-11-10 09:56:04'),
(101, 330489980, 5, '201', NULL, 'Камера', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200345-4', 1, 'шт', NULL, 0, NULL, '2025-11-10 09:56:04'),
(102, 330489980, 5, '201', NULL, 'Роутер', NULL, NULL, 0, NULL, 'netis', NULL, NULL, 'INV-201-2', 1, 'шт', NULL, 0, NULL, '2025-11-10 09:56:04'),
(103, 330489980, 5, '203', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', 'UV4Y038509', '11200174-11', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:01:14'),
(104, 330489980, 5, '203', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, 'gamemax', NULL, '107938', '11200173-10', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:01:14'),
(105, 330489980, 5, '203', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '11200173-11', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:01:14'),
(106, 330489980, 5, '203', NULL, 'Клавіатура', NULL, NULL, 0, NULL, 'Genius', NULL, NULL, '11200173-12', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:01:14'),
(107, 330489980, 5, '203', NULL, 'Миша', NULL, NULL, 0, NULL, 'Logitech', NULL, NULL, '11200173-13', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:01:14'),
(108, 330489980, 5, '203', NULL, 'Роутер', NULL, NULL, 0, NULL, 'Netis', NULL, NULL, 'INV-5-203-007', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:01:14'),
(109, 330489980, 5, '204', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', 'UV4Y028238', '101467100', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:10:34'),
(110, 330489980, 5, '204', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, 'gamemax', NULL, NULL, '101467051', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:10:34'),
(111, 330489980, 5, '204', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Philips', NULL, '130342-12', '11200300-204', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:10:34'),
(112, 330489980, 5, '204', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Philips', NULL, '130342-13', '11137731-204', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:10:34'),
(113, 330489980, 5, '204', NULL, 'Клавіатура', NULL, NULL, 0, NULL, 'Sven', NULL, NULL, '11137765', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:10:34'),
(114, 330489980, 5, '204', NULL, 'Клавіатура', NULL, NULL, 0, NULL, 'Genius', NULL, NULL, '101467072', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:10:34'),
(115, 330489980, 5, '204', NULL, 'Колонки', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200346-11', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:10:34'),
(116, 330489980, 5, '204', NULL, 'Камера', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200345-7', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:10:34'),
(117, 330489980, 5, '204', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467036', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:10:34'),
(118, 330489980, 5, '204', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11137731', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:10:34'),
(119, 330489980, 5, '205', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', 'UV4Y028250', '11200174-12', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:14:10'),
(120, 330489980, 5, '205', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, '107949', '11200173-14', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:14:10'),
(121, 330489980, 5, '205', NULL, 'Монітор', NULL, NULL, 0, NULL, NULL, NULL, '74601006624', '11200173-15', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:14:10'),
(122, 330489980, 5, '205', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200173-16', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:14:10'),
(123, 330489980, 5, '205', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200173-17', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:14:10'),
(124, 330489980, 5, '206', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, 'gamemax', NULL, '107941', '11700173', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:20:52'),
(125, 330489980, 5, '206', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Philips', NULL, NULL, '11200300', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:20:52'),
(126, 330489980, 5, '206', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', 'UV4Y038507', '11200174-14', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:20:52'),
(127, 330489980, 5, '206', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11700173-2', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:20:52'),
(128, 330489980, 5, '206', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11700173-3', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:20:52'),
(129, 330489980, 5, '206', NULL, 'Камера', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200345-8', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:20:52'),
(130, 330489980, 5, '206', NULL, 'Колонки', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200346-6', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:20:52'),
(131, 330489980, 5, '206', NULL, 'Роутер', NULL, NULL, 0, NULL, 'Netis', NULL, NULL, 'INV-5-206-008', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:20:52'),
(132, 330489980, 5, '216', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', 'UV4Y028245', '11200174-15', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:28:25'),
(133, 330489980, 5, '216', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200244-216', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:28:25'),
(134, 330489980, 5, '216', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200173-21', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:28:25'),
(135, 330489980, 5, '216', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Asus', NULL, 'M9LMTF216042', '11200173-18', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:28:25'),
(136, 330489980, 5, '216', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Asus', NULL, 'M9LMTF216026', '11200171', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:28:25'),
(137, 330489980, 5, '216', NULL, 'Клавіатура', NULL, NULL, 0, NULL, 'Sven', NULL, NULL, '11200173-19', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:28:25'),
(138, 330489980, 5, '216', NULL, 'Клавіатура', NULL, NULL, 0, NULL, 'Sven', NULL, NULL, '11200171-2', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:28:25'),
(139, 330489980, 5, '216', NULL, 'Миша', NULL, NULL, 0, NULL, 'Logitech', NULL, NULL, '11200173-20', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:28:25'),
(140, 330489980, 5, '216', NULL, 'Миша', NULL, NULL, 0, NULL, 'Logi', NULL, NULL, '11200171-3', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:28:25'),
(141, 330489980, 5, '216', NULL, 'Роутер', NULL, NULL, 0, NULL, 'Netis', NULL, NULL, 'INV-5-216-010', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:28:25'),
(142, 330489980, 5, '216', NULL, 'Колонки', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200346-12', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:28:25'),
(143, 330489980, 5, '216', NULL, 'Камера', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200345-12', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:28:25'),
(144, 330489980, 5, '223', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', 'UV4Y028383', '11200174-20', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:34:52'),
(145, 330489980, 5, '223', NULL, 'Ноутбук', NULL, NULL, 0, NULL, 'HP', NULL, 'SND119NLLB', '11200209-3', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:34:52'),
(146, 330489980, 5, '223', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, 'Gamemax', NULL, '107944', '11200387\\11200389\\11200385\\11200383\\11200384', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:34:52'),
(147, 330489980, 5, '223', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, '74601136624', '11200258', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:34:52'),
(148, 330489980, 5, '223', NULL, 'Клавіатура', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '11200172-4', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:34:52'),
(149, 330489980, 5, '223', NULL, 'Миша', NULL, NULL, 0, NULL, 'Genius', NULL, NULL, '11200388-3', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:34:52'),
(150, 330489980, 5, '223', NULL, 'Роутер', NULL, NULL, 0, NULL, 'Netis', NULL, NULL, 'INV-5-223-007', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:34:52'),
(151, 330489980, 5, '234', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, '107973', '11200244-2', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:39:08'),
(152, 330489980, 5, '234', NULL, 'Монітор', NULL, NULL, 0, NULL, NULL, NULL, '74601101224', '11200258-4', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:39:08'),
(153, 330489980, 5, '234', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200388-5', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:39:08'),
(154, 330489980, 5, '234', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200388-4', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:39:08'),
(155, 330489980, 5, '234', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200230-2', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:39:08'),
(156, 330489980, 5, '234', NULL, 'Роутер', NULL, NULL, 0, NULL, 'Netis', NULL, NULL, 'INV-5-234-006', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:39:08'),
(157, 330489980, 5, '228', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', 'UV4Y038152', '11200174-22', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:45:30'),
(158, 330489980, 5, '228', NULL, 'Роутер', NULL, NULL, 0, NULL, 'Netis', NULL, NULL, 'INV-5-228-002', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:45:30'),
(159, 330489980, 5, '228\\233', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, 'LogicPower', NULL, NULL, '11200244-3', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:45:30'),
(160, 330489980, 5, '228', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, '031008459314', '11200258-3', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:45:30'),
(161, 330489980, 5, '228', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200388-6', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:45:30'),
(162, 330489980, 5, '228', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200388-6', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:45:30'),
(163, 330489980, 5, '228', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, 'OSAKA', NULL, NULL, '1063262', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:45:30'),
(164, 330489980, 5, '228', NULL, 'Телефон', NULL, NULL, 0, NULL, 'Fivel', NULL, NULL, '1134572', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:45:30'),
(165, 330489980, 5, '236', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', 'UV4Y0037880', '11200174-23', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:51:10'),
(166, 330489980, 5, '236', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200173-23', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:51:11'),
(167, 330489980, 5, '236', NULL, 'Клавіатура', NULL, NULL, 0, NULL, 'Genius', NULL, NULL, '11200173-23', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:51:11'),
(168, 330489980, 5, '236', NULL, 'Миша', NULL, NULL, 0, NULL, 'Genius', NULL, NULL, '11200173-23', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:51:11'),
(169, 330489980, 5, '236', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '11200173-23', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:51:11'),
(170, 330489980, 5, '236', NULL, 'Колонки', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200346-23', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:51:11'),
(171, 330489980, 5, '236', NULL, 'Камера', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200345-23', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:51:11'),
(172, 330489980, 5, '237', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', 'UV4Y037982', '11200174-24', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:57:23'),
(173, 330489980, 5, '237', NULL, 'Монітор', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200258-5', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:57:23'),
(174, 330489980, 5, '237', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200244-5', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:57:23'),
(175, 330489980, 5, '237', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200388-7', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:57:23'),
(176, 330489980, 5, '237', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200388-7', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:57:23'),
(177, 330489980, 5, '237', NULL, 'Камера', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200345-24', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:57:23'),
(178, 330489980, 5, '237', NULL, 'Колонки', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200346-25', 1, 'шт', NULL, 0, NULL, '2025-11-10 10:57:23'),
(179, 330489980, 5, '235', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', 'UV4Y028489', '11200174-25', 1, 'шт', NULL, 0, NULL, '2025-11-10 11:08:14'),
(180, 330489980, 5, '235', NULL, 'Роутер', NULL, NULL, 0, NULL, 'Netis', NULL, NULL, 'INV-5-235-002', 1, 'шт', NULL, 0, NULL, '2025-11-10 11:08:14'),
(181, 330489980, 5, '235', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, '74601139624', '11200173-26', 1, 'шт', NULL, 0, NULL, '2025-11-10 11:08:14'),
(182, 330489980, 5, '235', NULL, 'Клавіатура', NULL, NULL, 0, NULL, 'Genius', NULL, NULL, '11200173-24', 1, 'шт', NULL, 0, NULL, '2025-11-10 11:08:14'),
(183, 330489980, 5, '235', NULL, 'Миша', NULL, NULL, 0, NULL, 'А4Tech', NULL, NULL, '11200173-25', 1, 'шт', NULL, 0, NULL, '2025-11-10 11:08:14'),
(184, 330489980, 5, '235', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200173-22', 1, 'шт', NULL, 0, NULL, '2025-11-10 11:08:14'),
(185, 330489980, 5, '235', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, 'Smartair', NULL, NULL, '11200373', 1, 'шт', NULL, 0, NULL, '2025-11-10 11:08:14'),
(186, 330489980, 5, '231', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', 'UV4Y038721', '11200174-26', 1, 'шт', NULL, 0, NULL, '2025-11-10 11:17:16'),
(187, 330489980, 5, '231', NULL, 'Ноутбук', NULL, NULL, 0, NULL, 'Acer', NULL, '84802658634', '11200209-4', 1, 'шт', NULL, 0, NULL, '2025-11-10 11:17:16'),
(188, 330489980, 5, '231', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, '107922', '11200173-27', 1, 'шт', NULL, 0, NULL, '2025-11-10 11:17:16'),
(189, 330489980, 5, '231', NULL, 'Монітор', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200173-29', 1, 'шт', NULL, 0, NULL, '2025-11-10 11:17:16'),
(190, 330489980, 5, '231', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200173-28', 1, 'шт', NULL, 0, NULL, '2025-11-10 11:17:16'),
(191, 330489980, 5, '231', NULL, 'Миша', NULL, NULL, 0, NULL, 'Genius', NULL, NULL, '11200173-30', 1, 'шт', NULL, 0, NULL, '2025-11-10 11:17:16'),
(192, 330489980, 5, '231', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, 'OSAKA', NULL, NULL, 'INV-5-231-007', 1, 'шт', NULL, 0, NULL, '2025-11-10 11:17:16'),
(193, 330489980, 5, '231', NULL, 'Роутер', NULL, NULL, 0, NULL, 'Netis', NULL, NULL, 'INV-5-231-008', 1, 'шт', NULL, 0, NULL, '2025-11-10 11:17:16'),
(194, 330489980, 5, '308', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200173-31', 1, 'шт', NULL, 0, NULL, '2025-11-10 11:22:17'),
(195, 330489980, 5, '327', NULL, 'Роутер', NULL, NULL, 0, NULL, 'Netis', NULL, NULL, 'INV-5-327-002', 1, 'шт', NULL, 0, NULL, '2025-11-10 11:22:17'),
(196, 330489980, 5, '308', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '11200386', 1, 'шт', NULL, 0, NULL, '2025-11-10 11:22:17'),
(197, 330489980, 5, '308', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200173-32', 1, 'шт', NULL, 0, NULL, '2025-11-10 11:22:17'),
(198, 330489980, 5, '327', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, 'Saturn', NULL, NULL, '11200245', 1, 'шт', NULL, 0, NULL, '2025-11-10 11:22:17'),
(199, 330489980, 5, '329', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', 'UV4Y038161', '101467077\\101467084', 1, 'шт', NULL, 0, NULL, '2025-11-10 11:33:44'),
(200, 330489980, 5, '329', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Philips', NULL, NULL, '11200300-2', 1, 'шт', NULL, 0, NULL, '2025-11-10 11:33:44'),
(201, 330489980, 5, '329', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '101467051-2', 1, 'шт', NULL, 0, NULL, '2025-11-10 11:33:44'),
(202, 330489980, 5, '329', NULL, 'Клавіатура', NULL, NULL, 0, NULL, 'Logitech', NULL, NULL, '11200387', 1, 'шт', NULL, 0, NULL, '2025-11-10 11:33:44'),
(203, 330489980, 5, '329', NULL, 'Клавіатура', NULL, NULL, 0, NULL, 'Genius', NULL, NULL, '101467063', 1, 'шт', NULL, 0, NULL, '2025-11-10 11:33:44'),
(204, 330489980, 5, '329', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, 'LogicPower', NULL, NULL, '11200387/11200389', 1, 'шт', NULL, 0, NULL, '2025-11-10 11:33:44'),
(205, 330489980, 5, '329', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, 'LogicPower', NULL, NULL, '11200173-35', 1, 'шт', NULL, 0, NULL, '2025-11-10 11:33:44'),
(206, 330489980, 5, '329', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1120025', 1, 'шт', NULL, 0, NULL, '2025-11-10 11:33:44'),
(207, 330489980, 5, '329', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101437017', 1, 'шт', NULL, 0, NULL, '2025-11-10 11:33:44'),
(208, 330489980, 5, '329', NULL, 'Телефон', NULL, NULL, 0, NULL, 'Fanvil', NULL, NULL, '11200255-4', 1, 'шт', NULL, 0, NULL, '2025-11-10 11:33:44'),
(209, 330489980, 5, '329', NULL, 'Роутер', NULL, NULL, 0, NULL, 'Netis', NULL, NULL, 'INV-5-329-012', 1, 'шт', NULL, 0, NULL, '2025-11-10 11:33:44'),
(210, 330489980, 5, '327', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', 'UV4Y028316', '11200174-35', 1, 'шт', NULL, 0, NULL, '2025-11-10 11:39:20'),
(211, 330489980, 5, '327', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, '107984', '11200387-5', 1, 'шт', NULL, 0, NULL, '2025-11-10 11:39:20'),
(212, 330489980, 5, '327', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '11200388-8', 1, 'шт', NULL, 0, NULL, '2025-11-10 11:39:20'),
(213, 330489980, 5, '327', NULL, 'Клавіатура', NULL, NULL, 0, NULL, 'a4Tech', NULL, NULL, '11200387-6', 1, 'шт', NULL, 0, NULL, '2025-11-10 11:39:20'),
(214, 330489980, 5, '327', NULL, 'Миша', NULL, NULL, 0, NULL, 'Genius', NULL, NULL, '11200260', 1, 'шт', NULL, 0, NULL, '2025-11-10 11:39:20'),
(215, 330489980, 5, '327', NULL, 'Камера', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200345-9', 1, 'шт', NULL, 0, NULL, '2025-11-10 11:39:20'),
(216, 330489980, 5, '327', NULL, 'Колонки', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200346-9', 1, 'шт', NULL, 0, NULL, '2025-11-10 11:39:20'),
(217, 330489980, 5, 'Підвал', NULL, 'Насос', NULL, NULL, 0, NULL, 'Optima 15-80', NULL, NULL, '11200204', 1, 'шт', NULL, 0, NULL, '2025-11-10 11:48:35'),
(218, 330489980, 5, 'Підвал', NULL, 'Мережевий насос', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1063224', 1, 'шт', NULL, 0, NULL, '2025-11-10 11:52:36'),
(219, 330489980, 5, 'Підвал', NULL, 'Насос (переоцінка 2018)', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200169', 1, 'шт', NULL, 0, NULL, '2025-11-10 11:55:06'),
(220, 330489980, 3, 'Реєстратура', NULL, 'Елекронно-інформаційне табло', NULL, NULL, 0, NULL, 'LG', NULL, NULL, '1048011', 1, 'шт', NULL, 0, NULL, '2025-11-13 06:53:23'),
(221, 330489980, 3, 'Реєстратура', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, 'Qube', NULL, NULL, '11200173-33', 1, 'шт', NULL, 0, NULL, '2025-11-13 06:53:23'),
(222, 330489980, 3, 'Реєстратура', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Philips', NULL, NULL, '11200386-10', 1, 'шт', NULL, 0, NULL, '2025-11-13 06:53:23'),
(223, 330489980, 3, 'Реєстратура', NULL, 'Миша', NULL, NULL, 0, NULL, 'Titanum', NULL, NULL, 'INV-3-Реєстратура-004', 1, 'шт', NULL, 0, NULL, '2025-11-13 06:53:23'),
(224, 330489980, 3, 'Реєстратура', NULL, 'Клавіатура', NULL, NULL, 0, NULL, 'Titanum', NULL, NULL, '1124604', 1, 'шт', NULL, 0, NULL, '2025-11-13 06:53:23'),
(225, 330489980, 3, 'Реєстратура', NULL, 'Телефон', NULL, NULL, 0, NULL, 'Fanvil', NULL, NULL, '11200255-5', 1, 'шт', NULL, 0, NULL, '2025-11-13 06:53:23'),
(226, 330489980, 3, '203', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200173-34', 1, 'шт', NULL, 0, NULL, '2025-11-13 06:58:41'),
(227, 330489980, 3, '203', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', NULL, NULL, '101467099', 1, 'шт', NULL, 0, NULL, '2025-11-13 06:58:41'),
(228, 330489980, 3, '203', NULL, 'Клавіатура', NULL, NULL, 0, NULL, 'Genius', NULL, NULL, '11200173-36', 1, 'шт', NULL, 0, NULL, '2025-11-13 06:58:41'),
(229, 330489980, 3, '203', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '101480189', 1, 'шт', NULL, 0, NULL, '2025-11-13 06:58:41'),
(230, 330489980, 3, '204', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200173-37', 1, 'шт', NULL, 0, NULL, '2025-11-13 07:03:04'),
(231, 330489980, 3, '204', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '1124605', 1, 'шт', NULL, 0, NULL, '2025-11-13 07:03:04'),
(232, 330489980, 3, '204', NULL, 'Клавіатура', NULL, NULL, 0, NULL, 'Sven', NULL, NULL, '11200173-38', 1, 'шт', NULL, 0, NULL, '2025-11-13 07:03:04'),
(233, 330489980, 3, '204', NULL, 'Миша', NULL, NULL, 0, NULL, 'Genius', NULL, NULL, '11200173-39', 1, 'шт', NULL, 0, NULL, '2025-11-13 07:03:04'),
(234, 330489980, 3, '204', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Canon', NULL, NULL, '11200201-3?', 1, 'шт', NULL, 0, NULL, '2025-11-13 07:03:04'),
(235, 330489980, 3, '205', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Philips', NULL, NULL, '11200389', 1, 'шт', NULL, 0, NULL, '2025-11-13 07:09:10'),
(236, 330489980, 3, '205', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '104480175', 1, 'шт', NULL, 0, NULL, '2025-11-13 07:09:10'),
(237, 330489980, 3, '205', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, 'Gamemax', NULL, NULL, '11200173-40', 1, 'шт', NULL, 0, NULL, '2025-11-13 07:09:10'),
(238, 330489980, 3, '205', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, 'Gamemax', NULL, NULL, '11200173-41', 1, 'шт', NULL, 0, NULL, '2025-11-13 07:09:10'),
(239, 330489980, 3, '205', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', NULL, '11200174-205', 1, 'шт', NULL, 0, NULL, '2025-11-13 07:09:10'),
(240, 330489980, 3, '205', NULL, 'Миша', NULL, NULL, 0, NULL, 'Logitech', NULL, NULL, '11200153', 1, 'шт', NULL, 0, NULL, '2025-11-13 07:09:10'),
(241, 330489980, 3, '205', NULL, 'Миша', NULL, NULL, 0, NULL, 'Logitech', NULL, NULL, 'B/N', 1, 'шт', NULL, 0, NULL, '2025-11-13 07:09:10'),
(242, 330489980, 3, '205', NULL, 'Клавіатура', NULL, NULL, 0, NULL, 'Genius', NULL, NULL, 'B/N', 1, 'шт', NULL, 0, NULL, '2025-11-13 07:09:10'),
(243, 330489980, 3, '205', NULL, 'Клавіатура', NULL, NULL, 0, NULL, 'Logitech', NULL, NULL, '11200188', 1, 'шт', NULL, 0, NULL, '2025-11-13 07:09:10'),
(244, 330489980, 3, '205', NULL, 'Колонки', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200346-20', 1, 'шт', NULL, 0, NULL, '2025-11-13 07:09:10'),
(245, 330489980, 3, '205', NULL, 'Камера', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200345-21', 1, 'шт', NULL, 0, NULL, '2025-11-13 07:09:10'),
(246, 330489980, 3, 'Серверна', NULL, 'Сервер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1046021/1046020/1046025/11200225', 1, 'шт', NULL, 0, NULL, '2025-11-13 07:11:10'),
(247, 330489980, 3, 'Серверна', NULL, 'Роутер', NULL, NULL, 0, NULL, 'Netis', NULL, NULL, 'INV-3-Серверна-004', 1, 'шт', NULL, 0, NULL, '2025-11-13 07:11:10'),
(248, 330489980, 3, '210', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', NULL, NULL, '11200174-33', 1, 'шт', NULL, 0, NULL, '2025-11-13 07:16:41'),
(249, 330489980, 3, '210', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, 'LG', NULL, NULL, '11200171-5', 1, 'шт', NULL, 0, NULL, '2025-11-13 07:16:41'),
(250, 330489980, 3, '210', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200171-6', 1, 'шт', NULL, 0, NULL, '2025-11-13 07:16:41'),
(251, 330489980, 3, '210', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-3-210-004', 1, 'шт', NULL, 0, NULL, '2025-11-13 07:16:41'),
(252, 330489980, 3, '210', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-3-210-005', 1, 'шт', NULL, 0, NULL, '2025-11-13 07:16:41'),
(253, 330489980, 3, '210', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '10465061', 1, 'шт', NULL, 0, NULL, '2025-11-13 07:16:41'),
(254, 330489980, 3, '210', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '101480186', 1, 'шт', NULL, 0, NULL, '2025-11-13 07:16:41'),
(255, 330489980, 3, '210', NULL, 'Миша', NULL, NULL, 0, NULL, 'Logitech', NULL, NULL, 'INV-3-210-008', 1, 'шт', NULL, 0, NULL, '2025-11-13 07:16:41'),
(256, 330489980, 3, '210', NULL, 'Колонки', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200346-16', 1, 'шт', NULL, 0, NULL, '2025-11-13 07:16:41'),
(257, 330489980, 3, '210', NULL, 'Камера', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200345-6', 1, 'шт', NULL, 0, NULL, '2025-11-13 07:16:41'),
(258, 330489980, 3, '210', NULL, 'Роутер', NULL, NULL, 0, NULL, 'Netis', NULL, NULL, 'INV-3-210-014', 1, 'шт', NULL, 0, NULL, '2025-11-13 07:16:41'),
(259, 330489980, 4, 'Реєстратура', NULL, 'Сервер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1046022/1046025/11200177/11200264/11200383/11200199/11200216', 1, 'шт', NULL, 0, NULL, '2025-11-13 07:55:09'),
(260, 330489980, 4, 'Реєстратура', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, 'Qube', NULL, NULL, '11200301', 1, 'шт', NULL, 0, NULL, '2025-11-13 07:55:09'),
(261, 330489980, 4, 'Реєстратура', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Philips', NULL, NULL, '11200300-10', 1, 'шт', NULL, 0, NULL, '2025-11-13 07:55:09'),
(262, 330489980, 4, 'Реєстратура', NULL, 'Клавіатура', NULL, NULL, 0, NULL, 'RealEl', NULL, NULL, '11200301-2', 1, 'шт', NULL, 0, NULL, '2025-11-13 07:55:09'),
(263, 330489980, 4, 'Реєстратура', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200301-3', 1, 'шт', NULL, 0, NULL, '2025-11-13 07:55:09'),
(264, 330489980, 4, 'Реєстратура', NULL, 'Телефон', NULL, NULL, 0, NULL, 'Fanvil', NULL, NULL, '11200257-8', 1, 'шт', NULL, 0, NULL, '2025-11-13 07:55:09'),
(265, 330489980, 4, '104', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, 'Qube', NULL, NULL, '11200301-5', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:00:56'),
(266, 330489980, 4, '104', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Philips', NULL, NULL, '11200300-3', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:00:56'),
(267, 330489980, 4, '104', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200301-6', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:00:56'),
(268, 330489980, 4, '104', NULL, 'Принтер', NULL, NULL, 0, NULL, 'HP', NULL, 'G3Q57A', '11200299-30', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:00:56'),
(269, 330489980, 4, '104', NULL, 'Колонки', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200346-30', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:00:56'),
(270, 330489980, 4, '104', NULL, 'Камера', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200345-30', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:00:56'),
(271, 330489980, 4, '104', NULL, 'Ноутбук', NULL, NULL, 0, NULL, 'Lenovo', NULL, NULL, '11200209', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:00:56'),
(272, 330489980, 4, '103', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200301-7', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:06:50'),
(273, 330489980, 4, '103', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Philips', NULL, NULL, '11200385', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:06:50'),
(274, 330489980, 4, '103', NULL, 'Принтер', NULL, NULL, 0, NULL, 'HP', NULL, 'G3Q57A', '11200299-7', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:06:50'),
(275, 330489980, 4, '102', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', 'UV4Y037904', '11200174-45', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:13:29'),
(276, 330489980, 4, '102', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, 'gamemax', NULL, '107940', '11200301-9', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:13:29'),
(277, 330489980, 4, '102', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, '47601090024', '11200300-9', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:13:29'),
(278, 330489980, 4, '102', NULL, 'Клавіатура', NULL, NULL, 0, NULL, 'Sven', NULL, NULL, '11200301-10', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:13:29'),
(279, 330489980, 4, '102', NULL, 'Миша', NULL, NULL, 0, NULL, 'Logitech', NULL, NULL, '11200301-11', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:13:29'),
(280, 330489980, 4, '101', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', 'UV4Y028501', '11200174-46', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:20:02'),
(281, 330489980, 4, '101', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-4-101-002', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:20:02'),
(282, 330489980, 4, '101', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, '74601083024', '11200258-8', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:20:02'),
(283, 330489980, 4, '101', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, '107950', '11200301-12', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:20:02'),
(284, 330489980, 4, '101', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Asus', NULL, 'А9ДЬЕА216208', '11200173-47', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:20:02'),
(285, 330489980, 4, '101', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200301-47', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:20:02'),
(286, 330489980, 4, '101', NULL, 'Колонки', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200346-33', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:20:02'),
(287, 330489980, 4, '101', NULL, 'Камера', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200345-33', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:20:02'),
(288, 330489980, 2, 'Реєстратура', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '11200173-43', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:42:31'),
(289, 330489980, 2, 'Реєстратура', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1124604-13', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:42:31'),
(290, 330489980, 2, 'Реєстратура', NULL, 'Миша', NULL, NULL, 0, NULL, 'Logitech', NULL, NULL, '11200115-3', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:42:31'),
(291, 330489980, 2, 'Реєстратура', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200173-44', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:42:31'),
(292, 330489980, 2, 'Реєстратура', NULL, 'Телефон', NULL, NULL, 0, NULL, 'Fanvil', NULL, NULL, 'INV-2-Реєстратура-005', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:42:31'),
(293, 330489980, 2, '304A', NULL, 'Ноутбук', NULL, NULL, 0, NULL, 'HP', NULL, NULL, '11200262-8', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:48:58'),
(294, 330489980, 2, '304A', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200170-5', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:48:58'),
(295, 330489980, 2, '304A', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Asus', NULL, NULL, '11200170-6', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:48:58'),
(296, 330489980, 2, '304A', NULL, 'Клавіатура', NULL, NULL, 0, NULL, 'Genius', NULL, NULL, '11200170-7', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:48:58'),
(297, 330489980, 2, '304A', NULL, 'Миша', NULL, NULL, 0, NULL, 'Genius', NULL, NULL, '11200170-8', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:48:58'),
(298, 330489980, 2, '304A', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', NULL, '11200174-48', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:48:58'),
(299, 330489980, 2, '304A', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, 'Nordis', NULL, NULL, '11200373-304', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:48:58'),
(300, 330489980, 2, '303', NULL, 'Ноутбук', NULL, NULL, 0, NULL, 'Lenovo', NULL, NULL, '1046055', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:52:02'),
(301, 330489980, 2, '303', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', 'UV4Y028279', '11200174-50', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:52:02'),
(302, 330489980, 2, '303', NULL, 'Роутер', NULL, NULL, 0, NULL, 'Netis', NULL, NULL, 'INV-2-303-009', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:52:02'),
(303, 330489980, 2, '302', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', 'UV4Y038711', '11200174-51', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:59:27'),
(304, 330489980, 2, '302', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, 'Qube', NULL, NULL, '11200171-7', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:59:27'),
(305, 330489980, 2, '302', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Philips', NULL, NULL, '11200300-11', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:59:27'),
(306, 330489980, 2, '302', NULL, 'Клавіатура', NULL, NULL, 0, NULL, 'Genius', NULL, NULL, '11200171-8', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:59:27'),
(307, 330489980, 2, '302', NULL, 'Миша', NULL, NULL, 0, NULL, 'Logitech', NULL, NULL, '11200171-9', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:59:27'),
(308, 330489980, 2, '302', NULL, 'Миша', NULL, NULL, 0, NULL, 'Realel', NULL, NULL, '11200170-11', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:59:27'),
(309, 330489980, 2, '302', NULL, 'Камера', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200345-50', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:59:27'),
(310, 330489980, 2, '302', NULL, 'Колонки', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200346-50', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:59:27'),
(311, 330489980, 2, '302', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200170-9', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:59:27'),
(312, 330489980, 2, '302', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200171-10', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:59:27'),
(313, 330489980, 2, '302', NULL, 'Монітор', NULL, NULL, 0, NULL, 'LG', NULL, NULL, '11200173-48', 1, 'шт', NULL, 0, NULL, '2025-11-13 08:59:27'),
(314, 330489980, 2, '301', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, 'smartair', NULL, NULL, 'n/b', 1, 'шт', NULL, 0, NULL, '2025-11-13 09:03:10'),
(315, 330489980, 2, '306', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', 'UV4Y038723', '11200174-54', 1, 'шт', NULL, 0, NULL, '2025-11-13 09:09:14'),
(316, 330489980, 2, '306', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200173-54', 1, 'шт', NULL, 0, NULL, '2025-11-13 09:09:14'),
(317, 330489980, 2, '306', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '11200173-53', 1, 'шт', NULL, 0, NULL, '2025-11-13 09:09:14'),
(318, 330489980, 2, '306', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10465062', 1, 'шт', NULL, 0, NULL, '2025-11-13 09:09:14'),
(319, 330489980, 2, '306', NULL, 'Колонки', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200346-36', 1, 'шт', NULL, 0, NULL, '2025-11-13 09:09:14'),
(320, 330489980, 2, '306', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200244-15', 1, 'шт', NULL, 0, NULL, '2025-11-13 09:09:14'),
(321, 330489980, 2, '306', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '11200244-16', 1, 'шт', NULL, 0, NULL, '2025-11-13 09:09:14'),
(322, 330489980, 2, '306', NULL, 'Клавіатура', NULL, NULL, 0, NULL, 'Genius', NULL, NULL, '11200244-17', 1, 'шт', NULL, 0, NULL, '2025-11-13 09:09:14'),
(323, 330489980, 6, 'Підвал', NULL, 'Принтер', NULL, 'орг техніка', 0, NULL, 'HP', 'M130a', 'VNF3Y92082', '11200299-I', 1, 'шт', NULL, 0, NULL, '2025-11-19 11:30:07'),
(324, 330489980, 6, 'Підвал', NULL, 'Ноутбук', NULL, 'орг техніка', 0, NULL, 'HP', NULL, 'CND930010R', '11200156', 1, 'шт', NULL, 0, NULL, '2025-11-19 11:30:07'),
(325, 330489980, 6, 'Підвал', NULL, 'Ноутбук', NULL, 'орг техніка', 0, NULL, 'Acer', NULL, 'NXMLFEU0194321240C3400', '101467032', 1, 'шт', NULL, 0, NULL, '2025-11-19 11:30:07'),
(326, 330489980, 6, 'Підвал', NULL, 'Ноутбук', NULL, 'орг техніка', 0, NULL, 'Lenovo', NULL, 'MP116PGF', '101487045', 1, 'шт', NULL, 0, NULL, '2025-11-19 11:30:07'),
(327, 330489980, 6, 'Підвал', NULL, 'Монітор', NULL, 'орг техніка', 0, NULL, 'Samsung', NULL, NULL, '101467051-6', 1, 'шт', NULL, 0, NULL, '2025-11-19 11:30:07');
INSERT INTO `room_inventory` (`id`, `admin_telegram_id`, `branch_id`, `room_number`, `template_id`, `equipment_type`, `full_name`, `category`, `is_priority`, `balance_code`, `brand`, `model`, `serial_number`, `inventory_number`, `quantity`, `unit`, `price`, `min_quantity`, `notes`, `created_at`) VALUES
(328, 330489980, 6, 'Підвал', NULL, 'Монітор', NULL, 'орг техніка', 0, NULL, 'Asus', NULL, 'F9LMTF214674', '101467040', 1, 'шт', NULL, 0, NULL, '2025-11-19 11:30:07'),
(329, 330489980, 6, 'Підвал', NULL, 'Монітор', NULL, 'орг техніка', 0, NULL, 'Acer', NULL, NULL, '101467059', 1, 'шт', NULL, 0, NULL, '2025-11-19 11:30:07'),
(330, 330489980, 1, '13', NULL, 'Монітор', NULL, 'орг техніка', 0, NULL, 'Acer', NULL, NULL, '101467052', 1, 'шт', NULL, 0, NULL, '2025-11-19 11:30:07'),
(331, 330489980, 6, 'Підвал', NULL, 'Ноутбук', NULL, 'орг техніка', 0, NULL, 'HP', NULL, NULL, '11200156-2', 1, 'шт', NULL, 0, NULL, '2025-11-19 11:30:07'),
(332, 330489980, 6, 'Підвал', NULL, 'Принтер', NULL, 'орг техніка', 0, NULL, 'Samsung', NULL, 'CNNB2KCX682', '11137468', 1, 'шт', NULL, 0, NULL, '2025-11-19 11:30:07'),
(333, 330489980, 1, '7a', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467067', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:25:24'),
(334, 330489980, 1, '7a', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', NULL, 'UV4Y039287', '101467083', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:25:24'),
(335, 330489980, 1, '7a', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '10400059', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:25:24'),
(336, 330489980, 1, '7a', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467060', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:25:24'),
(337, 330489980, 1, '7a', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467063-5', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:25:24'),
(338, 330489980, 1, '7a', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Philips', NULL, NULL, '101467067', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:25:24'),
(339, 330489980, 1, '7a', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10400059', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:25:24'),
(340, 330489980, 1, '7a', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467051-5', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:25:24'),
(341, 330489980, 1, '7a', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11137705', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:25:24'),
(342, 330489980, 1, '7a', NULL, 'Колонки', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200346-45', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:25:24'),
(343, 330489980, 1, '7a', NULL, 'Камера', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200345-45', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:25:24'),
(344, 330489980, 1, '7a', NULL, 'Роутер', NULL, NULL, 0, NULL, 'Netis', NULL, NULL, 'inv-7a', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:25:24'),
(345, 330489980, 1, '7a', NULL, 'Шкаф документації 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:36:51'),
(346, 330489980, 1, '7a', NULL, 'Шкаф документації 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277-2', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:36:51'),
(347, 330489980, 1, '7a', NULL, 'Шафа для одягу подвійна', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10400025', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:36:51'),
(348, 330489980, 1, '7a', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11137484', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:36:51'),
(349, 330489980, 1, '7a', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11137484-2', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:36:51'),
(350, 330489980, 1, '7a', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:36:51'),
(351, 330489980, 1, '7a', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1136209', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:36:51'),
(352, 330489980, 1, '7a', NULL, 'Стилаж книжний відкритий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200235', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:36:51'),
(353, 330489980, 1, '7a', NULL, 'Стол', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136194', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:36:51'),
(354, 330489980, 1, '7a', NULL, 'Стол', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136194', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:36:51'),
(355, 330489980, 1, '7a', NULL, 'Кушетка', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10477147', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:36:51'),
(356, 330489980, 1, '7a', NULL, 'Тумба 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200049', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:36:51'),
(357, 330489980, 1, '7', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11137731-5', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:56:01'),
(358, 330489980, 1, '7', NULL, 'Монітор', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10400054', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:56:01'),
(359, 330489980, 1, '7', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467053', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:56:01'),
(360, 330489980, 1, '7', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11137705-5', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:56:01'),
(361, 330489980, 1, '7', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467061-7', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:56:01'),
(362, 330489980, 1, '7', NULL, 'Монітор', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467053', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:56:01'),
(363, 330489980, 1, '7', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1137731-5', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:56:01'),
(364, 330489980, 1, '7', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200378', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:56:01'),
(365, 330489980, 1, '7', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', NULL, 'UV4Y038155', '101467087', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:56:01'),
(366, 330489980, 1, '7', NULL, 'Роутер', NULL, NULL, 0, NULL, 'Netis', NULL, NULL, 'INV-1-7-010', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:56:01'),
(367, 330489980, 1, '7', NULL, 'Камера', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200345-46', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:56:01'),
(368, 330489980, 1, '7', NULL, 'Колонки', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200346-46', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:56:01'),
(369, 330489980, 1, '7', NULL, 'Тумба двері', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1124569', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:56:01'),
(370, 330489980, 1, '7', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211-7', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:56:01'),
(371, 330489980, 1, '7', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211-7', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:56:01'),
(372, 330489980, 1, '7', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211-7', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:56:01'),
(373, 330489980, 1, '7', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '112000113', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:56:01'),
(374, 330489980, 1, '7', NULL, 'Стіл з тумбою 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136194-5', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:56:01'),
(375, 330489980, 1, '7', NULL, 'Стіл з тумбою 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136194-6', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:56:01'),
(376, 330489980, 1, '7', NULL, 'Шкаф документації 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200242', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:56:01'),
(377, 330489980, 1, '7', NULL, 'Шкаф документації 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200244-6', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:56:01'),
(378, 330489980, 1, '7', NULL, 'Шафа для одягу подвійна', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200043', 1, 'шт', NULL, 0, NULL, '2025-11-21 07:56:01'),
(379, 330489980, 1, '13', NULL, 'Принтер', NULL, NULL, 0, NULL, 'HP', NULL, 'VNFYY82508', '10400049', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:17:15'),
(380, 330489980, 1, '13', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11137731-7', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:17:15'),
(381, 330489980, 1, '13', NULL, 'Монітор', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11137731-8', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:17:15'),
(382, 330489980, 1, '13', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11137731-9', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:17:15'),
(383, 330489980, 1, '13', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200155', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:17:15'),
(384, 330489980, 1, '13', NULL, 'Роутер', NULL, NULL, 0, NULL, 'Tp-link', NULL, NULL, 'INV-1-13-006', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:17:15'),
(385, 330489980, 1, '13', NULL, 'Стіл з тумбою 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211-13', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:17:15'),
(386, 330489980, 1, '13', NULL, 'Стіл з тумбою 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211-13', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:17:15'),
(387, 330489980, 1, '13', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211-13', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:17:15'),
(388, 330489980, 1, '13', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211-13', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:17:15'),
(389, 330489980, 1, '13', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211-13', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:17:15'),
(390, 330489980, 1, '13', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211-13', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:17:15'),
(391, 330489980, 1, '13', NULL, 'Тумба 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136244-13', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:17:15'),
(392, 330489980, 1, '13', NULL, 'Шафа для одягу 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200413', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:17:15'),
(393, 330489980, 1, '13', NULL, 'Шкаф документації 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277-13', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:17:15'),
(394, 330489980, 1, '13', NULL, 'Вентилятор', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-13-016', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:17:15'),
(395, 330489980, 1, '13', NULL, 'Подовжувач 1.8м', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-13-017', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:17:15'),
(396, 330489980, 1, '13', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10146755', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:17:15'),
(397, 330489980, 1, '18', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', NULL, 'UV4Y0038728', '101467098', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:45:05'),
(398, 330489980, 1, '18', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467074-18', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:45:05'),
(399, 330489980, 1, '18', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467073', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:45:05'),
(400, 330489980, 1, '18', NULL, 'Монітор', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200114', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:45:05'),
(401, 330489980, 1, '18', NULL, 'Монітор', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200158', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:45:05'),
(402, 330489980, 1, '18', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467074', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:45:05'),
(403, 330489980, 1, '18', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-15-007', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:45:05'),
(404, 330489980, 1, '18', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467074', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:45:05'),
(405, 330489980, 1, '18', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467074', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:45:05'),
(406, 330489980, 1, '18', NULL, 'Подовжувач 1.8м', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-15-010', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:45:05'),
(407, 330489980, 1, '18', NULL, 'Подовжувач 1.8м', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-15-011', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:45:05'),
(408, 330489980, 1, '18', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200154-5', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:45:05'),
(409, 330489980, 1, '18', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, 'Electrolux', NULL, NULL, 'INV-1-15-013', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:45:05'),
(410, 330489980, 1, '18', NULL, 'Стіл комп\'ютерний', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136194-18', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:45:05'),
(411, 330489980, 1, '18', NULL, 'Стіл комп\'ютерний', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136194-18', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:45:05'),
(412, 330489980, 1, '18', NULL, 'Тумба дверці', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-15-016', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:45:05'),
(413, 330489980, 1, '18', NULL, 'Тумба дверці', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-15-018', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:45:05'),
(414, 330489980, 1, '18', NULL, 'Тумба 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200049-18', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:45:05'),
(415, 330489980, 1, '18', NULL, 'Тумба 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200049-18', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:45:05'),
(416, 330489980, 1, '18', NULL, 'Сейф 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136165', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:45:05'),
(417, 330489980, 1, '18', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138050', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:45:05'),
(418, 330489980, 1, '18', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211-18', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:45:05'),
(419, 330489980, 1, '18', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211-18', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:45:05'),
(420, 330489980, 1, '18', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211-18', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:45:05'),
(421, 330489980, 1, '18', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211-18', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:45:05'),
(422, 330489980, 1, '18', NULL, 'Шкаф для докуменації 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277-18', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:45:05'),
(423, 330489980, 1, '18', NULL, 'Шкаф для докуменації 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277-18', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:45:05'),
(424, 330489980, 1, '18', NULL, 'Шафа для одягу 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136289-18', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:45:05'),
(425, 330489980, 1, '20', NULL, 'Стіл 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '020000012', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:55:25'),
(426, 330489980, 1, '20', NULL, 'Тумба 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138103', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:55:25'),
(427, 330489980, 1, '20', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211-20', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:55:25'),
(428, 330489980, 1, '20', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211-20', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:55:25'),
(429, 330489980, 1, '20', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211-20', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:55:25'),
(430, 330489980, 1, '20', NULL, 'Стіл', NULL, NULL, 0, NULL, NULL, NULL, NULL, '020000012', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:55:25'),
(431, 330489980, 1, '20', NULL, 'Тумба 2ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-20-011', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:55:25'),
(432, 330489980, 1, '20', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-20', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:55:25'),
(433, 330489980, 1, '20', NULL, 'Шафа для одягу 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136289', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:55:25'),
(434, 330489980, 1, '20', NULL, 'Шафа для одягу  2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200242-20', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:55:25'),
(435, 330489980, 1, '20', NULL, 'Вішак напольний', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138018', 1, 'шт', NULL, 0, NULL, '2025-11-21 08:55:25'),
(436, 330489980, 1, '24', NULL, 'Сул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211-24', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:06:13'),
(437, 330489980, 1, '24', NULL, 'Сул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211-24', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:06:13'),
(438, 330489980, 1, '24', NULL, 'Сул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211-24', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:06:13'),
(439, 330489980, 1, '24', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138107', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:06:13'),
(440, 330489980, 1, '24', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10400078-24', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:06:13'),
(441, 330489980, 1, '24', NULL, 'Стол', NULL, NULL, 0, NULL, NULL, NULL, NULL, '112000351', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:06:13'),
(442, 330489980, 1, '24', NULL, 'Стол кутовий з тумбою 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200348', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:06:13'),
(443, 330489980, 1, '24', NULL, 'Тумба 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200347', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:06:13'),
(444, 330489980, 1, '24', NULL, 'Шафа для докуменетів', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200354', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:06:13'),
(445, 330489980, 1, '24', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11137494', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:06:13'),
(446, 330489980, 1, '24', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-24-011', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:06:13'),
(447, 330489980, 1, '24', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-24-012', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:06:13'),
(448, 330489980, 1, '24', NULL, 'Колонки', NULL, NULL, 0, NULL, 'Real', NULL, NULL, 'INV-1-24-013', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:06:13'),
(449, 330489980, 1, '24', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', NULL, NULL, '101467110', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:06:13'),
(450, 330489980, 1, '24', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Philips', NULL, NULL, '11137494', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:06:13'),
(451, 330489980, 1, '24', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-24-016', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:06:13'),
(452, 330489980, 1, '18', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-18-001', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:06:31'),
(453, 330489980, 1, '18', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-18-002', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:06:31'),
(454, 330489980, 1, 'Коридор', NULL, 'Шафа для одягу 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136289-0', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:09:28'),
(455, 330489980, 1, '25', NULL, 'Шафа для одягу 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136289-25', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:17:26'),
(456, 330489980, 1, '25', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277-25', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:17:26'),
(457, 330489980, 1, '25', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1136211-25', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:17:26'),
(458, 330489980, 1, '25', NULL, 'Стіл комп\'ютерний з тумбою 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136194-25', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:17:26'),
(459, 330489980, 1, '25', NULL, 'Стіл комп\'ютерний', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136194-25', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:17:26'),
(460, 330489980, 1, '25', NULL, 'Вішак напольний', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-25-006', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:17:26'),
(461, 330489980, 1, '26', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', 'UV4Y040592', '101467108', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:30:22'),
(462, 330489980, 1, '26', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11137494-26', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:30:22'),
(463, 330489980, 1, '26', NULL, 'Монітор', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11137461', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:30:22'),
(464, 330489980, 1, '26', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-26-004', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:30:22'),
(465, 330489980, 1, '26', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200295', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:30:22'),
(466, 330489980, 1, '26', NULL, 'Колонки', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200346-26', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:30:22'),
(467, 330489980, 1, '26', NULL, 'Камера', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200345-26', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:30:22'),
(468, 330489980, 1, '26', NULL, 'Роутер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200159-26', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:30:22'),
(469, 330489980, 1, '26', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10400052', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:30:22'),
(470, 330489980, 1, '26', NULL, 'Монітор', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200114-26', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:30:22'),
(471, 330489980, 1, '26', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-26-011', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:30:22'),
(472, 330489980, 1, '26', NULL, 'Подовжувач 1.8м', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-26-012', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:30:22'),
(473, 330489980, 1, '26', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211-26', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:30:22'),
(474, 330489980, 1, '26', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211-26', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:30:22'),
(475, 330489980, 1, '26', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211-26', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:30:22'),
(476, 330489980, 1, '26', NULL, 'Стіл', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1136193', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:30:22'),
(477, 330489980, 1, '26', NULL, 'Стіл', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200335', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:30:22'),
(478, 330489980, 1, '26', NULL, 'Тумба дверці', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136537', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:30:22'),
(479, 330489980, 1, '26', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '112000113-26', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:30:22'),
(480, 330489980, 1, '26', NULL, 'Шафа для одягу 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136772', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:30:22'),
(481, 330489980, 1, '26', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277-26', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:30:22'),
(482, 330489980, 1, '26', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200230-26', 1, 'шт', NULL, 0, NULL, '2025-11-21 09:30:22'),
(483, 330489980, 1, '5', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211-15', 1, 'шт', NULL, 0, NULL, '2025-11-24 06:45:28'),
(484, 330489980, 1, '5', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211-15', 1, 'шт', NULL, 0, NULL, '2025-11-24 06:45:28'),
(485, 330489980, 1, '5', NULL, 'Стіл комп\'ютерний', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200423', 1, 'шт', NULL, 0, NULL, '2025-11-24 06:45:28'),
(486, 330489980, 1, '5', NULL, 'Свіч', NULL, NULL, 0, NULL, 'Tp-link', NULL, NULL, 'INV-1-5-004', 1, 'шт', NULL, 0, NULL, '2025-11-24 06:45:28'),
(487, 330489980, 1, '5', NULL, 'Подовжувач 1.8', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-5-005', 1, 'шт', NULL, 0, NULL, '2025-11-24 06:45:28'),
(488, 330489980, 1, '15', NULL, 'Подовжувач', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-15-001', 1, 'шт', NULL, 0, NULL, '2025-11-24 06:59:11'),
(489, 330489980, 1, '16', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138041\\11136277', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:03:28'),
(490, 330489980, 1, '16', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200051\\11136277', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:03:28'),
(491, 330489980, 1, '16', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200051\\11136277', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:03:28'),
(492, 330489980, 1, '16', NULL, 'Шафа для одягу 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136289-16', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:13:57'),
(493, 330489980, 1, '16', NULL, 'Шафа з полицями 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200413-16', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:13:57'),
(494, 330489980, 1, '16', NULL, 'Стіл комп\'ютерний 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136194-16', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:13:57'),
(495, 330489980, 1, '16', NULL, 'Стіл комп\'ютерний 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136194-16', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:13:57'),
(496, 330489980, 1, '16', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211-16', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:13:57'),
(497, 330489980, 1, '16', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211-16', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:13:57'),
(498, 330489980, 1, '16', NULL, 'Тумба комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200051', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:13:57'),
(499, 330489980, 1, '16', NULL, 'Вішак напольний', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138018-16', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:13:57'),
(500, 330489980, 1, '16', NULL, 'Тумба 1ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-16-009', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:13:57'),
(501, 330489980, 1, '16', NULL, 'Тумба 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200049-16', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:13:57'),
(502, 330489980, 1, '16', NULL, 'Тумба 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136247-16', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:13:57'),
(503, 330489980, 1, '16', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, 'Midea', NULL, NULL, '10149754', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:13:57'),
(504, 330489980, 1, '16', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', NULL, 'UV4Y038158', '101467078', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:13:57'),
(505, 330489980, 1, '16', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467053-16', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:13:57'),
(506, 330489980, 1, '16', NULL, 'Монітор', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200158-16', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:13:57'),
(507, 330489980, 1, '16', NULL, 'Колонки', NULL, NULL, 0, NULL, NULL, NULL, NULL, '112000346-16', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:13:57'),
(508, 330489980, 1, '16', NULL, 'Камера', NULL, NULL, 0, NULL, NULL, NULL, NULL, '112000345-16', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:13:57'),
(509, 330489980, 1, '16', NULL, 'Роутер', NULL, NULL, 0, NULL, 'Netis', NULL, NULL, 'INV-1-16-018', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:13:57'),
(510, 330489980, 1, '16', NULL, 'Подовжувач 3м', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-16-019', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:13:57'),
(511, 330489980, 1, '16', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11137765-16', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:13:57'),
(512, 330489980, 1, '16', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467048-16', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:13:57'),
(513, 330489980, 1, '16', NULL, 'Монітор', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200114-16', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:13:58'),
(514, 330489980, 1, '16', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200115-16', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:13:58'),
(515, 330489980, 1, '16', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '112000113-16', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:13:58'),
(516, 330489980, 1, '16', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138050-16', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:13:58'),
(517, 330489980, 1, '22', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467031', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:27:40'),
(518, 330489980, 1, '45/4', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', NULL, 'UV4Y038514', '11200174-IV', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:27:40'),
(519, 330489980, 1, '22', NULL, 'Монітор', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467031-22', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:27:40'),
(520, 330489980, 1, '22', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-22-004', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:27:40'),
(521, 330489980, 1, '22', NULL, 'Колонки', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200346-22', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:27:40'),
(522, 330489980, 1, '22', NULL, 'Камера', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200345-22', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:27:40'),
(523, 330489980, 1, '22', NULL, 'Монітор', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467046', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:27:40'),
(524, 330489980, 1, '22', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-22-008', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:27:40'),
(525, 330489980, 1, '22', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-22-009', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:27:40'),
(526, 330489980, 1, '22', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200156-22', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:27:40'),
(527, 330489980, 1, '22', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-22-011', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:27:40'),
(528, 330489980, 1, '22', NULL, 'Роутер', NULL, NULL, 0, NULL, 'Netis', NULL, NULL, 'INV-1-22-012', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:27:40'),
(529, 330489980, 1, '22', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, 'Smartair', NULL, NULL, '11200364', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:27:40'),
(530, 330489980, 1, '22', NULL, 'Подовжувач', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-22-014', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:27:40'),
(531, 330489980, 1, '22', NULL, 'Шафа для одягу  1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136289-22', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:27:40'),
(532, 330489980, 1, '22', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1136752-22', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:27:40'),
(533, 330489980, 1, '22', NULL, 'Пінал для документів 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277-22', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:27:40'),
(534, 330489980, 1, '22', NULL, 'Стіл комп\'ютерний коричневий', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-22-018', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:27:40'),
(535, 330489980, 1, '22', NULL, 'Стіл комп\'ютерний коричневий', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-22-019', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:27:40'),
(536, 330489980, 1, '22', NULL, 'Тумба 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200103-22', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:27:40'),
(537, 330489980, 1, '22', NULL, 'Тумба 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200103-22', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:27:40'),
(538, 330489980, 1, '22', NULL, 'Тумба 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138029-22', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:27:40'),
(539, 330489980, 1, '22', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-22-023', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:27:40'),
(540, 330489980, 1, '22', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-22-024', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:27:40'),
(541, 330489980, 1, '22', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-22-025', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:27:40'),
(542, 330489980, 1, '22', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-22-026', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:27:40'),
(543, 330489980, 1, '22', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '112000113-22', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:27:40'),
(544, 330489980, 1, '22', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '112000113-22', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:27:40'),
(545, 330489980, 1, '22', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211-22', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:27:40'),
(546, 330489980, 1, '27', NULL, 'Монітор', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10400053', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:48:32'),
(547, 330489980, 1, '27', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10400053', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:48:32'),
(548, 330489980, 1, '27', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467045\\1040053', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:48:32'),
(549, 330489980, 1, '27', NULL, 'Подовжувач 3м', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-27-004', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:48:32'),
(550, 330489980, 1, '27', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200230-27', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:48:32'),
(551, 330489980, 1, '27', NULL, 'Камера', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200345-27', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:48:32'),
(552, 330489980, 1, '27', NULL, 'Стіл письмовий 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136186', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:48:32'),
(553, 330489980, 1, '27', NULL, 'Шафа для одягу 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136772-27', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:48:32'),
(554, 330489980, 1, '27', NULL, 'Шава для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277-27', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:48:32'),
(555, 330489980, 1, '27', NULL, 'Тумба 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1113624', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:48:32'),
(556, 330489980, 1, '27', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211-27', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:48:32'),
(557, 330489980, 1, '27', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211-27', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:48:32'),
(558, 330489980, 1, '27', NULL, 'Роутер', NULL, NULL, 0, NULL, 'Netis', NULL, NULL, 'INV-1-27-014', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:48:32'),
(559, 330489980, 1, '29', NULL, 'Монітор', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467048-29', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:58:22'),
(560, 330489980, 1, '29', NULL, 'Принтер', NULL, NULL, 0, NULL, 'HP', NULL, NULL, '10400050-29', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:58:22'),
(561, 330489980, 1, '29', NULL, 'Роутер', NULL, NULL, 0, NULL, 'TP-link', NULL, NULL, 'INV-1-29-003', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:58:22'),
(562, 330489980, 1, '29', NULL, 'PosSector', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200302', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:58:22'),
(563, 330489980, 1, '29', NULL, 'PosTerminal', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-29-005', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:58:22'),
(564, 330489980, 1, '29', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200173-29-2', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:58:22'),
(565, 330489980, 1, '29', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467048-29', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:58:22'),
(566, 330489980, 1, '29', NULL, 'ДБЖ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200112-29', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:58:22'),
(567, 330489980, 1, '29', NULL, 'Подовжувач 1.8м', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-29-010', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:58:22'),
(568, 330489980, 1, '29', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, 'Smartair', NULL, NULL, 'INV-1-29-011', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:58:22'),
(569, 330489980, 1, '29', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11137484-29', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:58:22'),
(570, 330489980, 1, '29', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211-29', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:58:22'),
(571, 330489980, 1, '29', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '112000113-29', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:58:22'),
(572, 330489980, 1, '29', NULL, 'Сейф 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136166', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:58:22'),
(573, 330489980, 1, '29', NULL, 'Пінал для документів', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1136277', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:58:22'),
(574, 330489980, 1, '29', NULL, 'Шафа для одягу 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136289-29', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:58:22'),
(575, 330489980, 1, '29', NULL, 'Стіл письмовий 3-1ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136194-29', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:58:22'),
(576, 330489980, 1, '29', NULL, 'Стіл комп\'ютерний 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136194-29', 1, 'шт', NULL, 0, NULL, '2025-11-24 07:58:22'),
(577, 330489980, 1, '28', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', NULL, 'UV4Y039050', '10400061', 1, 'шт', NULL, 0, NULL, '2025-11-24 08:05:37'),
(578, 330489980, 1, '28', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10400060', 1, 'шт', NULL, 0, NULL, '2025-11-24 08:05:37'),
(579, 330489980, 1, '28', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-28-003', 1, 'шт', NULL, 0, NULL, '2025-11-24 08:05:37'),
(580, 330489980, 1, '28', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-28-004', 1, 'шт', NULL, 0, NULL, '2025-11-24 08:05:37'),
(581, 330489980, 1, '28', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, 'Nordis', NULL, NULL, '11200230-28', 1, 'шт', NULL, 0, NULL, '2025-11-24 08:05:37'),
(582, 330489980, 1, '28', NULL, 'Стіл', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136158', 1, 'шт', NULL, 0, NULL, '2025-11-24 08:05:37'),
(583, 330489980, 1, '28', NULL, 'Стіл', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136194-28', 1, 'шт', NULL, 0, NULL, '2025-11-24 08:05:37'),
(584, 330489980, 1, '28', NULL, 'Монітор', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467042', 1, 'шт', NULL, 0, NULL, '2025-11-24 08:05:37'),
(585, 330489980, 1, '28', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-28-009', 1, 'шт', NULL, 0, NULL, '2025-11-24 08:05:37'),
(586, 330489980, 1, '28', NULL, 'Подовжувач 1.8м', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-28-010', 1, 'шт', NULL, 0, NULL, '2025-11-24 08:05:37'),
(587, 330489980, 1, '28', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211-28', 1, 'шт', NULL, 0, NULL, '2025-11-24 08:05:37'),
(588, 330489980, 1, '28', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211-28', 1, 'шт', NULL, 0, NULL, '2025-11-24 08:05:37'),
(589, 330489980, 1, '28', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211-28', 1, 'шт', NULL, 0, NULL, '2025-11-24 08:05:37'),
(590, 330489980, 1, '28', NULL, 'Вішак напольний', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138018-28', 1, 'шт', NULL, 0, NULL, '2025-11-24 08:05:37'),
(591, 330489980, 1, '28', NULL, 'Шафа для документів 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277-28', 1, 'шт', NULL, 0, NULL, '2025-11-24 08:05:37'),
(592, 330489980, 1, '28', NULL, 'Холодильник', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138020', 1, 'шт', NULL, 0, NULL, '2025-11-24 08:05:37'),
(593, 330489980, 1, '33\\2', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467034', 1, 'шт', NULL, 0, NULL, '2025-11-24 11:29:25'),
(594, 330489980, 1, '33\\2', NULL, 'Монітор', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467034', 1, 'шт', NULL, 0, NULL, '2025-11-24 11:29:25'),
(595, 330489980, 1, '33\\2', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200115-33', 1, 'шт', NULL, 0, NULL, '2025-11-24 11:29:25'),
(596, 330489980, 1, '33\\2', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467034', 1, 'шт', NULL, 0, NULL, '2025-11-24 11:29:25'),
(597, 330489980, 1, '33\\2', NULL, 'Стіл комп\'ютерний 1дв з підставкою', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-33\\2-005', 1, 'шт', NULL, 0, NULL, '2025-11-24 11:29:25'),
(598, 330489980, 1, '33\\2', NULL, 'Тумба 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136244', 1, 'шт', NULL, 0, NULL, '2025-11-24 11:29:25'),
(599, 330489980, 1, '33\\2', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211-33', 1, 'шт', NULL, 0, NULL, '2025-11-24 11:29:25'),
(600, 330489980, 1, '33\\2', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138107-33', 1, 'шт', NULL, 0, NULL, '2025-11-24 11:29:25'),
(601, 330489980, 1, '33\\2', NULL, 'Шафа для одягу 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136289-33', 1, 'шт', NULL, 0, NULL, '2025-11-24 11:29:25'),
(602, 330489980, 1, '33\\2', NULL, 'Пінал для документів', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-33\\2-010', 1, 'шт', NULL, 0, NULL, '2025-11-24 11:29:25'),
(603, 330489980, 1, '33\\1', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, 'Nordis', NULL, NULL, '11200230-33', 1, 'шт', NULL, 0, NULL, '2025-11-24 11:36:18'),
(604, 330489980, 1, '33\\1', NULL, 'Стіл 2ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136186-33', 1, 'шт', NULL, 0, NULL, '2025-11-24 11:36:18'),
(605, 330489980, 1, '33\\1', NULL, 'Стіл', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11320031-33', 1, 'шт', NULL, 0, NULL, '2025-11-24 11:36:18'),
(606, 330489980, 1, '33\\1', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211-33\\1', 1, 'шт', NULL, 0, NULL, '2025-11-24 11:36:18'),
(607, 330489980, 1, '33\\1', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211-33\\1', 1, 'шт', NULL, 0, NULL, '2025-11-24 11:36:18'),
(608, 330489980, 1, '33\\1', NULL, 'Стул дерев\'яний', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136218-33\\1', 1, 'шт', NULL, 0, NULL, '2025-11-24 11:36:18'),
(609, 330489980, 1, '33\\1', NULL, 'Стул дерев\'яний', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1136516', 1, 'шт', NULL, 0, NULL, '2025-11-24 11:36:18'),
(610, 330489980, 1, '33\\1', NULL, 'Стул дерев\'яний', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136218-33\\1', 1, 'шт', NULL, 0, NULL, '2025-11-24 11:36:18'),
(611, 330489980, 1, '33\\3', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', 'UV4Y038136', '101467102', 1, 'шт', NULL, 0, NULL, '2025-11-24 11:44:57'),
(612, 330489980, 1, '33\\3', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10400054-33\\3', 1, 'шт', NULL, 0, NULL, '2025-11-24 11:44:57'),
(613, 330489980, 1, '33\\3', NULL, 'Монітор', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-33\\3-003', 1, 'шт', NULL, 0, NULL, '2025-11-24 11:44:57'),
(614, 330489980, 1, '33\\3', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467051-33\\3', 1, 'шт', NULL, 0, NULL, '2025-11-24 11:44:57'),
(615, 330489980, 1, '33\\3', NULL, 'ДБЖ', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-33\\3-006', 1, 'шт', NULL, 0, NULL, '2025-11-24 11:44:57'),
(616, 330489980, 1, '33\\3', NULL, 'Свіч', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-33\\3-007', 1, 'шт', NULL, 0, NULL, '2025-11-24 11:44:57'),
(617, 330489980, 1, '33\\3', NULL, 'Стіл комп\'ютерний', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136557', 1, 'шт', NULL, 0, NULL, '2025-11-24 11:44:57'),
(618, 330489980, 1, '33\\3', NULL, 'Тумба 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136244-33\\3', 1, 'шт', NULL, 0, NULL, '2025-11-24 11:44:57'),
(619, 330489980, 1, '33\\3', NULL, 'Тумба 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136428-33\\3', 1, 'шт', NULL, 0, NULL, '2025-11-24 11:44:57'),
(620, 330489980, 1, '33\\3', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11137484-33\\3', 1, 'шт', NULL, 0, NULL, '2025-11-24 11:44:57'),
(621, 330489980, 1, '33\\3', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138017', 1, 'шт', NULL, 0, NULL, '2025-11-24 11:44:57'),
(622, 330489980, 1, '33\\3', NULL, 'Пенал', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136393', 1, 'шт', NULL, 0, NULL, '2025-11-24 11:44:57'),
(623, 330489980, 1, '33\\3', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277-33\\3', 1, 'шт', NULL, 0, NULL, '2025-11-24 11:44:57'),
(624, 330489980, 1, '33\\3', NULL, 'Шафа для одягу  1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136393-33\\3', 1, 'шт', NULL, 0, NULL, '2025-11-24 11:44:57'),
(625, 330489980, 1, '33\\4', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11137494-33\\4', 1, 'шт', NULL, 0, NULL, '2025-11-24 12:03:03'),
(626, 330489980, 1, '33\\4', NULL, 'Монітор', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200257', 1, 'шт', NULL, 0, NULL, '2025-11-24 12:03:03'),
(627, 330489980, 1, '33\\4', NULL, 'Клавіатура', NULL, NULL, 0, NULL, 'Genius', NULL, NULL, 'INV-1-33\\4-003', 1, 'шт', NULL, 0, NULL, '2025-11-24 12:03:03'),
(628, 330489980, 1, '33\\4', NULL, 'Миша', NULL, NULL, 0, NULL, 'Logitech', NULL, NULL, 'INV-1-33\\4-004', 1, 'шт', NULL, 0, NULL, '2025-11-24 12:03:03'),
(629, 330489980, 1, '33\\4', NULL, 'Стіл комп\'ютерний', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11176194', 1, 'шт', NULL, 0, NULL, '2025-11-24 12:03:03'),
(630, 330489980, 1, '33\\4', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-33\\4-006', 1, 'шт', NULL, 0, NULL, '2025-11-24 12:03:03'),
(631, 330489980, 1, '33\\4', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200346-33\\4', 1, 'шт', NULL, 0, NULL, '2025-11-24 12:03:03'),
(632, 330489980, 1, '33\\4', NULL, 'Шафа для одягу 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136289-33\\4', 1, 'шт', NULL, 0, NULL, '2025-11-24 12:03:03'),
(633, 330489980, 1, '33\\4', NULL, 'Шафа для одягу 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136289-33\\4', 1, 'шт', NULL, 0, NULL, '2025-11-24 12:03:03'),
(634, 330489980, 1, '33\\4', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277-33\\4', 1, 'шт', NULL, 0, NULL, '2025-11-24 12:03:03'),
(635, 330489980, 1, '33\\4', NULL, 'Шафа з полицями для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277-33\\4', 1, 'шт', NULL, 0, NULL, '2025-11-24 12:03:03'),
(636, 330489980, 1, '33\\4', NULL, 'Стіл комп\'ютерний з тумбою 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1136186', 1, 'шт', NULL, 0, NULL, '2025-11-24 12:03:03'),
(637, 330489980, 1, '33\\4', NULL, 'Стіл комп\'ютерний 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136194-33\\4', 1, 'шт', NULL, 0, NULL, '2025-11-24 12:03:03'),
(638, 330489980, 1, '33\\4', NULL, 'Тумба 2ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136244-33\\4', 1, 'шт', NULL, 0, NULL, '2025-11-24 12:03:03'),
(639, 330489980, 1, '33\\4', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '112000113-33\\4', 1, 'шт', NULL, 0, NULL, '2025-11-24 12:03:03'),
(640, 330489980, 1, '33\\4', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '112000113-33\\4', 1, 'шт', NULL, 0, NULL, '2025-11-24 12:03:03'),
(641, 330489980, 1, '33\\4', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200173-33\\4', 1, 'шт', NULL, 0, NULL, '2025-11-24 12:03:03'),
(642, 330489980, 1, '33\\4', NULL, 'Монітор', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200300-33\\4', 1, 'шт', NULL, 0, NULL, '2025-11-24 12:03:03'),
(643, 330489980, 1, '33\\4', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-33\\4-021', 1, 'шт', NULL, 0, NULL, '2025-11-24 12:03:03'),
(644, 330489980, 1, '33\\4', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-33\\4-022', 1, 'шт', NULL, 0, NULL, '2025-11-24 12:03:03'),
(645, 330489980, 1, '33\\4', NULL, 'Подовжувач 1.8м', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-33\\4-023', 1, 'шт', NULL, 0, NULL, '2025-11-24 12:03:03'),
(646, 330489980, 1, '33\\4', NULL, 'Подовжувач 1.8м', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-33\\4-024', 1, 'шт', NULL, 0, NULL, '2025-11-24 12:03:03'),
(647, 330489980, 1, '33\\4', NULL, 'Подовжувач білий 1.8м', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-33\\4-025', 1, 'шт', NULL, 0, NULL, '2025-11-24 12:03:03'),
(648, 330489980, 1, '33\\4', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467041-33\\4', 1, 'шт', NULL, 0, NULL, '2025-11-24 12:03:03'),
(649, 330489980, 1, '33\\4', NULL, 'Монітор', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200386-33\\4', 1, 'шт', NULL, 0, NULL, '2025-11-24 12:03:03'),
(650, 330489980, 1, '33\\4', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-33\\4-028', 1, 'шт', NULL, 0, NULL, '2025-11-24 12:03:03'),
(651, 330489980, 1, '33\\4', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-33\\4-029', 1, 'шт', NULL, 0, NULL, '2025-11-24 12:03:03'),
(652, 330489980, 1, '33\\4', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277-33\\4', 1, 'шт', NULL, 0, NULL, '2025-11-24 12:03:03'),
(653, 330489980, 1, '33\\4', NULL, 'Тумба 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-33\\4-031', 1, 'шт', NULL, 0, NULL, '2025-11-24 12:03:03'),
(654, 330489980, 1, '33\\4', NULL, 'Роутер', NULL, NULL, 0, NULL, 'Netis', NULL, NULL, 'INV-1-33\\4-032', 1, 'шт', NULL, 0, NULL, '2025-11-24 12:03:03'),
(655, 330489980, 1, '33\\4', NULL, 'Свіч', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-33\\4-033', 1, 'шт', NULL, 0, NULL, '2025-11-24 12:03:03'),
(656, 330489980, 1, '33\\4', NULL, 'Радіатор обігрівач', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200109', 1, 'шт', NULL, 0, NULL, '2025-11-24 12:03:03'),
(657, 330489980, 1, '33\\4', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211-33\\4', 1, 'шт', NULL, 0, NULL, '2025-11-24 12:03:03'),
(658, 330489980, 1, '15', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', 'UV4Y028385', '101467081', 1, 'шт', NULL, 0, NULL, '2025-11-25 05:46:05'),
(659, 330489980, 1, '15', NULL, 'Роутер', NULL, NULL, 0, NULL, 'Netis', NULL, NULL, 'INV-1-15-002', 1, 'шт', NULL, 0, NULL, '2025-11-25 05:46:05'),
(660, 330489980, 1, '15', NULL, 'Тумба 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1124589', 1, 'шт', NULL, 0, NULL, '2025-11-25 05:46:05'),
(661, 330489980, 1, '15', NULL, 'Стіл 2ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136193', 1, 'шт', NULL, 0, NULL, '2025-11-25 05:46:05'),
(662, 330489980, 1, '15', NULL, 'Шафа для одягу', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200241', 1, 'шт', NULL, 0, NULL, '2025-11-25 05:46:05');
INSERT INTO `room_inventory` (`id`, `admin_telegram_id`, `branch_id`, `room_number`, `template_id`, `equipment_type`, `full_name`, `category`, `is_priority`, `balance_code`, `brand`, `model`, `serial_number`, `inventory_number`, `quantity`, `unit`, `price`, `min_quantity`, `notes`, `created_at`) VALUES
(663, 330489980, 1, '15', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211-1-15', 1, 'шт', NULL, 0, NULL, '2025-11-25 05:46:05'),
(664, 330489980, 1, '15', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11137484-15', 1, 'шт', NULL, 0, NULL, '2025-11-25 05:46:05'),
(665, 330489980, 1, '15', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211-1-15', 1, 'шт', NULL, 0, NULL, '2025-11-25 05:46:05'),
(666, 330489980, 1, '15', NULL, 'ДБЖ', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-15-009', 1, 'шт', NULL, 0, NULL, '2025-11-25 05:46:05'),
(667, 330489980, 1, '15', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467047-15', 1, 'шт', NULL, 0, NULL, '2025-11-25 05:46:05'),
(668, 330489980, 1, '15', NULL, 'Монітор', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467047-15', 1, 'шт', NULL, 0, NULL, '2025-11-25 05:46:05'),
(669, 330489980, 1, '15', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467047-15', 1, 'шт', NULL, 0, NULL, '2025-11-25 05:46:05'),
(670, 330489980, 1, '15', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467047-15', 1, 'шт', NULL, 0, NULL, '2025-11-25 05:46:05'),
(671, 330489980, 1, '15', NULL, 'Подовжувач 1.8м', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-15-014', 1, 'шт', NULL, 0, NULL, '2025-11-25 05:46:05'),
(672, 330489980, 1, '35', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', 'UV4Y039095', '101467088', 1, 'шт', NULL, 0, NULL, '2025-11-25 09:55:52'),
(673, 330489980, 1, '23', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200113', 1, 'шт', NULL, 0, NULL, '2025-11-25 09:55:52'),
(674, 330489980, 1, '35', NULL, 'Подовжувач мережевий 1.8м', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'ИНВ', 1, 'шт', NULL, 0, NULL, '2025-11-25 09:55:52'),
(675, 330489980, 1, '35', NULL, 'Подовжувач мережевий 1.8м', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'ИНВ', 1, 'шт', NULL, 0, NULL, '2025-11-25 09:55:52'),
(676, 330489980, 1, '35', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10400051', 1, 'шт', NULL, 0, NULL, '2025-11-25 09:55:52'),
(677, 330489980, 1, '35', NULL, 'Монітор', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200114', 1, 'шт', NULL, 0, NULL, '2025-11-25 09:55:52'),
(678, 330489980, 1, '35', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11137705', 1, 'шт', NULL, 0, NULL, '2025-11-25 09:55:52'),
(679, 330489980, 1, '35', NULL, 'Колонки', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200346', 1, 'шт', NULL, 0, NULL, '2025-11-25 09:55:52'),
(680, 330489980, 1, '35', NULL, 'Камера', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200345', 1, 'шт', NULL, 0, NULL, '2025-11-25 09:55:52'),
(681, 330489980, 1, '35', NULL, 'Роутер', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV', 1, 'шт', NULL, 0, NULL, '2025-11-25 09:55:52'),
(682, 330489980, 1, '23', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10400080', 1, 'шт', NULL, 0, NULL, '2025-11-25 09:55:52'),
(683, 330489980, 1, '23', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV', 1, 'шт', NULL, 0, NULL, '2025-11-25 09:55:52'),
(684, 330489980, 1, '23', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV', 1, 'шт', NULL, 0, NULL, '2025-11-25 09:55:52'),
(685, 330489980, 1, '23', NULL, 'Стіл комп\'ютерний 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136557', 1, 'шт', NULL, 0, NULL, '2025-11-25 09:55:52'),
(686, 330489980, 1, '23', NULL, 'Стіл комп\'ютерний 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200051', 1, 'шт', NULL, 0, NULL, '2025-11-25 09:55:52'),
(687, 330489980, 1, '23', NULL, 'Шафа для одягу 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136289', 1, 'шт', NULL, 0, NULL, '2025-11-25 09:55:52'),
(688, 330489980, 1, '23', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277', 1, 'шт', NULL, 0, NULL, '2025-11-25 09:55:52'),
(689, 330489980, 1, '23', NULL, 'Пенал малий 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'ИНВ', 1, 'шт', NULL, 0, NULL, '2025-11-25 09:55:52'),
(690, 330489980, 1, '23', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-25 09:55:52'),
(691, 330489980, 1, '23', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-25 09:55:52'),
(692, 330489980, 1, '23', NULL, 'Стільниця кутова відкрита', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136394', 1, 'шт', NULL, 0, NULL, '2025-11-25 09:55:53'),
(693, 330489980, 1, '23', NULL, 'Тумба 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-23-001', 1, 'шт', NULL, 0, NULL, '2025-11-25 09:56:55'),
(694, 330489980, 1, 'Реєстратура', NULL, 'Стіл комп\'ютерний 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136194', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:14:48'),
(695, 330489980, 1, 'Реєстратура', NULL, 'Стіл 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1136186', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:14:48'),
(696, 330489980, 1, 'Реєстратура', NULL, 'Тумба 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200104', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:14:48'),
(697, 330489980, 1, 'Реєстратура', NULL, 'Стіл 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136337', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:14:48'),
(698, 330489980, 1, 'Реєстратура', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:14:48'),
(699, 330489980, 1, 'Реєстратура', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:14:48'),
(700, 330489980, 1, 'Реєстратура', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '112000113', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:14:48'),
(701, 330489980, 1, 'Реєстратура', NULL, 'Сейф 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101637032', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:14:48'),
(702, 330489980, 1, 'Реєстратура', NULL, 'Тумба 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136247', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:14:48'),
(703, 330489980, 1, 'Реєстратура', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101487020', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:14:48'),
(704, 330489980, 1, 'Реєстратура', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '11137731', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:14:48'),
(705, 330489980, 1, 'Реєстратура', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11137705', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:14:48'),
(706, 330489980, 1, 'Реєстратура', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11137705', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:14:48'),
(707, 330489980, 1, 'Реєстратура', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10400047', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:14:48'),
(708, 330489980, 1, 'Реєстратура', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '11200114', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:14:48'),
(709, 330489980, 1, 'Реєстратура', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11137731', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:14:48'),
(710, 330489980, 1, 'Реєстратура', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11137731', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:14:48'),
(711, 330489980, 1, 'Реєстратура', NULL, 'Телефон', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11137295', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:14:48'),
(712, 330489980, 1, 'Реєстратура', NULL, 'Телефон', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11137295', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:14:48'),
(713, 330489980, 1, 'Реєстратура', NULL, 'Система сповіщення', NULL, NULL, 0, NULL, 'Vellez', NULL, NULL, 'INV-1-Реєстратура-027', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:14:48'),
(714, 330489980, 1, 'Реєстратура', NULL, 'Протипожежна система', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-Реєстратура-027', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:14:48'),
(715, 330489980, 1, 'Реєстратура', NULL, 'Система безпеки', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-Реєстратура-028', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:14:48'),
(716, 330489980, 1, 'Реєстратура', NULL, 'Лампа настільна червона', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138015', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:14:48'),
(717, 330489980, 1, 'Реєстратура', NULL, 'Лампа настільна червона', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138015', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:14:48'),
(718, 330489980, 1, 'Реєстратура', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467072', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:35:17'),
(719, 330489980, 1, 'Реєстратура', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '101467072', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:35:17'),
(720, 330489980, 1, 'Реєстратура', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101487010', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:35:17'),
(721, 330489980, 1, 'Реєстратура', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-Реєстратура-004', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:35:17'),
(722, 330489980, 1, 'Реєстратура', NULL, 'Подовжувач 1.8м', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-Реєстратура-005', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:35:17'),
(723, 330489980, 1, 'Реєстратура', NULL, 'Телефон', NULL, NULL, 0, NULL, 'Fanvil', NULL, NULL, '11137295', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:35:17'),
(724, 330489980, 1, 'Реєстратура', NULL, 'Телефон', NULL, NULL, 0, NULL, 'Fanvil', NULL, NULL, '11137295', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:35:17'),
(725, 330489980, 1, 'Реєстратура', NULL, 'Подовжувач 3м', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-Реєстратура-009', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:35:17'),
(726, 330489980, 1, 'Реєстратура', NULL, 'Стіл комп\'ютерний', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136557', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:35:17'),
(727, 330489980, 1, 'Реєстратура', NULL, 'Стіл 1ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138186', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:35:17'),
(728, 330489980, 1, 'Реєстратура', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:35:17'),
(729, 330489980, 1, 'Реєстратура', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:35:17'),
(730, 330489980, 1, 'Реєстратура', NULL, 'Шафа для документів з полицями', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:35:17'),
(731, 330489980, 1, 'Реєстратура', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, 'Electrolux', NULL, NULL, '11200154', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:35:17'),
(732, 330489980, 1, 'Реєстратура', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '112000113', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:35:17'),
(733, 330489980, 1, 'Реєстратура', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '112000113', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:35:17'),
(734, 330489980, 1, 'Реєстратура', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1136211', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:35:17'),
(735, 330489980, 1, 'Реєстратура', NULL, 'Табуретка', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1136223', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:35:17'),
(736, 330489980, 1, 'Реєстратура', NULL, 'Табуретка', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1136223', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:35:17'),
(737, 330489980, 1, 'Реєстратура', NULL, 'Табуретка', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1136223', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:35:17'),
(738, 330489980, 1, 'Реєстратура', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1136211', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:35:17'),
(739, 330489980, 1, 'Реєстратура', NULL, 'Вішак напольний чорний', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138078', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:35:17'),
(740, 330489980, 1, 'Реєстратура', NULL, 'Вентилятор', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136024', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:35:17'),
(741, 330489980, 1, 'Реєстратура', NULL, 'Холодильник', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138020', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:35:17'),
(742, 330489980, 1, 'Реєстратура', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11137731', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:35:17'),
(743, 330489980, 1, 'Реєстратура', NULL, 'Холодильник', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11137331', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:35:17'),
(744, 330489980, 1, 'Реєстратура', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200157', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:35:17'),
(745, 330489980, 1, 'Реєстратура', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '11137731', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:35:17'),
(746, 330489980, 1, 'Реєстратура', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-Реєстратура-031', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:35:17'),
(747, 330489980, 1, 'Реєстратура', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467069', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:35:17'),
(748, 330489980, 1, 'Реєстратура', NULL, 'Роутер', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-Реєстратура-033', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:35:17'),
(749, 330489980, 1, 'Реєстратура', NULL, 'Подовжувач 3м', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-Реєстратура-034', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:35:17'),
(750, 330489980, 1, 'Реєстратура', NULL, 'Стіл рецепсія', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200099', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:35:17'),
(751, 330489980, 1, 'Реєстратура', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:35:17'),
(752, 330489980, 1, 'Реєстратура', NULL, 'Шафа для одягу 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200341', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:35:17'),
(753, 330489980, 1, 'Реєстратура', NULL, 'Шафа для одягу 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1136277', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:35:17'),
(754, 330489980, 1, 'Реєстратура', NULL, 'Стелаж металевий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136060', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:38:38'),
(755, 330489980, 1, 'Реєстратура', NULL, 'Стелаж металевий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136060', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:38:38'),
(756, 330489980, 1, 'Реєстратура', NULL, 'Стелаж металевий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136060', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:38:38'),
(757, 330489980, 1, 'Реєстратура', NULL, 'Стелаж металевий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136060', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:38:38'),
(758, 330489980, 1, 'Реєстратура', NULL, 'Стелаж металевий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136060', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:38:38'),
(759, 330489980, 1, 'Реєстратура', NULL, 'Стелаж металевий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136060', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:38:38'),
(760, 330489980, 1, 'Реєстратура', NULL, 'Стелаж металевий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136060', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:38:38'),
(761, 330489980, 1, 'Реєстратура', NULL, 'Стелаж металевий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136060', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:38:38'),
(762, 330489980, 1, 'Реєстратура', NULL, 'Стелаж металевий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136060', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:38:38'),
(763, 330489980, 1, 'Реєстратура', NULL, 'Стелаж металевий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136060', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:38:38'),
(764, 330489980, 1, 'Реєстратура', NULL, 'Стелаж металевий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136060', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:38:38'),
(765, 330489980, 1, 'Реєстратура', NULL, 'Стелаж металевий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136060', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:38:38'),
(766, 330489980, 1, 'Реєстратура', NULL, 'Стелаж металевий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136060', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:38:38'),
(767, 330489980, 1, 'Реєстратура', NULL, 'Стелаж металевий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136060', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:38:38'),
(768, 330489980, 1, 'Реєстратура', NULL, 'Стелаж металевий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136060', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:38:38'),
(769, 330489980, 1, 'Реєстратура', NULL, 'Стелаж металевий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136060', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:38:38'),
(770, 330489980, 1, 'Реєстратура', NULL, 'Стелаж металевий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136060', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:38:38'),
(771, 330489980, 1, 'Реєстратура', NULL, 'Стелаж металевий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136060', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:38:38'),
(772, 330489980, 1, 'Реєстратура', NULL, 'Стелаж металевий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136060', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:38:38'),
(773, 330489980, 1, 'Реєстратура', NULL, 'Стелаж металевий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136060', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:38:38'),
(774, 330489980, 1, 'Реєстратура', NULL, 'Стелаж металевий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136060', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:38:38'),
(775, 330489980, 1, 'Реєстратура', NULL, 'Стелаж металевий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136060', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:38:38'),
(776, 330489980, 1, 'Реєстратура', NULL, 'Стелаж металевий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136060', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:38:38'),
(777, 330489980, 1, 'Реєстратура', NULL, 'Стелаж металевий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136060', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:38:38'),
(778, 330489980, 1, 'Реєстратура', NULL, 'Стелаж металевий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136060', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:38:38'),
(779, 330489980, 1, 'Реєстратура', NULL, 'Стелаж металевий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136060', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:38:38'),
(780, 330489980, 1, 'Реєстратура', NULL, 'Стелаж металевий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136060', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:38:38'),
(781, 330489980, 1, 'Реєстратура', NULL, 'Стелаж металевий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136060', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:38:38'),
(782, 330489980, 1, 'Реєстратура', NULL, 'Вентилятор настольний', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1136023', 1, 'шт', NULL, 0, NULL, '2025-11-25 10:38:38'),
(783, 330489980, 1, '36', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', NULL, 'UV4Y028548', '101467105', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:05:14'),
(784, 330489980, 1, '36', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467056', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:05:14'),
(785, 330489980, 1, '36', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '101467056', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:05:14'),
(786, 330489980, 1, '36', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11137494', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:05:14'),
(787, 330489980, 1, '36', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '101467047', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:05:14'),
(788, 330489980, 1, '36', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467047', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:05:14'),
(789, 330489980, 1, '36', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200155', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:05:14'),
(790, 330489980, 1, '36', NULL, 'Камера', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200345', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:05:14'),
(791, 330489980, 1, '36', NULL, 'Колонки', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200346', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:05:14'),
(792, 330489980, 1, '36', NULL, 'Тумба 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-36-013', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:05:14'),
(793, 330489980, 1, '36', NULL, 'Тумба 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-36-014', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:05:14'),
(794, 330489980, 1, '36', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-36-015', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:05:14'),
(795, 330489980, 1, '36', NULL, 'Стіл', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200051', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:05:14'),
(796, 330489980, 1, '36', NULL, 'Стіл', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200051', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:05:14'),
(797, 330489980, 1, '36', NULL, 'Тумба 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-36-018', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:05:14'),
(798, 330489980, 1, '36', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:05:14'),
(799, 330489980, 1, '36', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:05:14'),
(800, 330489980, 1, '36', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:05:14'),
(801, 330489980, 1, '36', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:05:14'),
(802, 330489980, 1, '36', NULL, 'Подовжувач білий 1.8м', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-36-024', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:05:14'),
(803, 330489980, 1, '36', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200230', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:05:14'),
(804, 330489980, 1, '36', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136752', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:05:14'),
(805, 330489980, 1, '36', NULL, 'Пенал з полицями 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:05:14'),
(806, 330489980, 1, '36', NULL, 'Пенал з полицями 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:05:14'),
(807, 330489980, 1, '36', NULL, 'Шафа для одягу 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136289', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:05:14'),
(808, 330489980, 1, '36', NULL, 'Вішак чорний', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138018', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:05:14'),
(809, 330489980, 1, '19', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', 'UV4Y038175', '101467085', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:15:42'),
(810, 330489980, 1, '19', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467055', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:15:42'),
(811, 330489980, 1, '19', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '101467055', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:15:42'),
(812, 330489980, 1, '19', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467055', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:15:42'),
(813, 330489980, 1, '19', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467055', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:15:42'),
(814, 330489980, 1, '19', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467035', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:15:42'),
(815, 330489980, 1, '19', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '101467038', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:15:42'),
(816, 330489980, 1, '19', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200296', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:15:42'),
(817, 330489980, 1, '19', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467047', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:15:42'),
(818, 330489980, 1, '19', NULL, 'Колонки', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200346', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:15:42'),
(819, 330489980, 1, '19', NULL, 'Камера', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200345', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:15:42'),
(820, 330489980, 1, '19', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '112000113', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:15:42'),
(821, 330489980, 1, '19', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '112000113', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:15:42'),
(822, 330489980, 1, '19', NULL, 'Стіл 2ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136244', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:15:42'),
(823, 330489980, 1, '19', NULL, 'Стіл комп\'ютерний 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136194', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:15:42'),
(824, 330489980, 1, '19', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200051', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:15:42'),
(825, 330489980, 1, '19', NULL, 'Шафа для одягу 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-19-017', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:15:42'),
(826, 330489980, 1, '19', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:15:42'),
(827, 330489980, 1, '19', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:15:42'),
(828, 330489980, 1, '19', NULL, 'Тумба 1ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-19-020', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:15:42'),
(829, 330489980, 1, '19', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:15:42'),
(830, 330489980, 1, '19', NULL, 'Сейф 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101630402', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:15:42'),
(831, 330489980, 1, '19', NULL, 'Холодильник', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101497042', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:15:42'),
(832, 330489980, 1, '19', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200154', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:15:42'),
(833, 330489980, 6, 'Підвал', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', 'UV4Y038195', '101467086', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:53:04'),
(834, 330489980, 1, 'Підвал', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467059', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:53:04'),
(835, 330489980, 1, 'Підвал', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '11200114', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:53:04'),
(836, 330489980, 1, 'Підвал', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467059', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:53:04'),
(837, 330489980, 1, 'Підвал', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467059', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:53:04'),
(838, 330489980, 1, 'Підвал', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467057', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:53:04'),
(839, 330489980, 1, 'Підвал', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '101467057', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:53:04'),
(840, 330489980, 1, 'Підвал', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467057', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:53:04'),
(841, 330489980, 6, '2 поверх', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467057', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:53:04'),
(842, 330489980, 1, 'Підвал', NULL, 'Роутер', NULL, NULL, 0, NULL, 'Netis', NULL, NULL, 'INV-1-35-010', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:53:04'),
(843, 330489980, 1, 'Підвал', NULL, 'Камера', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200345', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:53:04'),
(844, 330489980, 1, 'Підвал', NULL, 'Колонки', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200346', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:53:04'),
(845, 330489980, 1, '35', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, 'Nordis', NULL, NULL, '11200230', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:53:04'),
(846, 330489980, 1, '35', NULL, 'Тумба 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136247', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:53:04'),
(847, 330489980, 1, '35', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-35-015', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:53:04'),
(848, 330489980, 1, '35', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-35-016', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:53:04'),
(849, 330489980, 1, '35', NULL, 'Тумба 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-35-017', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:53:04'),
(850, 330489980, 1, '35', NULL, 'Тумба 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-35-018', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:53:04'),
(851, 330489980, 1, '35', NULL, 'Стіл', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-35-019', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:53:04'),
(852, 330489980, 1, '35', NULL, 'Стіл комп\'ютерний', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136194', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:53:04'),
(853, 330489980, 1, '35', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:53:04'),
(854, 330489980, 1, '35', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:53:04'),
(855, 330489980, 1, '35', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '112000113', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:53:04'),
(856, 330489980, 1, '35', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '111000113', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:53:04'),
(857, 330489980, 1, '35', NULL, 'Шафа для одягу', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-35-025', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:53:04'),
(858, 330489980, 1, '35', NULL, 'Шафа для документів 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:53:04'),
(859, 330489980, 1, '35', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136752', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:53:04'),
(860, 330489980, 1, '35', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136752', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:53:04'),
(861, 330489980, 1, '35', NULL, 'Шафа для одягу 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136289', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:53:04'),
(862, 330489980, 1, '35', NULL, 'Шафа для одягу 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136289', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:53:04'),
(863, 330489980, 1, '35', NULL, 'Вішар чорний напольний', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11137718', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:53:04'),
(864, 330489980, 1, '35', NULL, 'Подовжувач білий 1.8м', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-35-032', 1, 'шт', NULL, 0, NULL, '2025-11-25 11:53:04'),
(865, 330489980, 1, '40\\1', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467060', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:19:10'),
(866, 330489980, 1, '40\\1', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '11137494', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:19:11'),
(867, 330489980, 1, '40\\1', NULL, 'Подовжувач 1.8м', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-40\\1-005', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:19:11'),
(868, 330489980, 1, '40\\1', NULL, 'Стіл комп\'ютерний 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136194', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:19:11'),
(869, 330489980, 1, '40\\1', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-40\\1-007', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:19:11'),
(870, 330489980, 1, '40\\1', NULL, 'Шафа з полицями відкрита для документів', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:19:11'),
(871, 330489980, 1, '40\\1', NULL, 'Шафа для одягу 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10400026', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:19:11'),
(872, 330489980, 1, '40\\1', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:19:11'),
(873, 330489980, 1, '40\\1', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:19:11'),
(874, 330489980, 1, '40\\1', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138050', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:19:11'),
(875, 330489980, 1, '40\\1', NULL, 'Диван без підлокітників', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-40\\1-013', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:19:11'),
(876, 330489980, 1, '40', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467069', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:30:00'),
(877, 330489980, 1, '40', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '101467069', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:30:00'),
(878, 330489980, 1, '40', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467069', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:30:00'),
(879, 330489980, 1, '40', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467069', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:30:00'),
(880, 330489980, 1, '40', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', NULL, 'UV4Y039045', '11200174\\КОЗА', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:30:00'),
(881, 330489980, 6, 'Підвал', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200387', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:30:00'),
(882, 330489980, 1, '40', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '101467060', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:30:00'),
(883, 330489980, 1, '40', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-40-010', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:30:00'),
(884, 330489980, 1, '40', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10149753', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:30:00'),
(885, 330489980, 1, '40', NULL, 'Роутер', NULL, NULL, 0, NULL, 'TP-Link', NULL, NULL, 'INV-1-40-012', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:30:00'),
(886, 330489980, 1, '40', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '112000113', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:30:00'),
(887, 330489980, 1, '40', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '112000113', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:30:00'),
(888, 330489980, 1, '40', NULL, 'Колонки', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200346', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:30:00'),
(889, 330489980, 1, '40', NULL, 'Тумба 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136244', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:30:00'),
(890, 330489980, 1, '40', NULL, 'Тумба 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200045', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:30:00'),
(891, 330489980, 1, '40', NULL, 'Стіл комп\'ютерний 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200051', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:30:00'),
(892, 330489980, 1, '40', NULL, 'Стіл комп\'ютерний 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200051', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:30:00'),
(893, 330489980, 1, '40', NULL, 'Шафа для документів відкритий 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136752', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:30:00'),
(894, 330489980, 1, '40', NULL, 'Сейф 2дв', NULL, NULL, 0, NULL, 'AIKO', NULL, NULL, '11136161', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:30:00'),
(895, 330489980, 1, '40', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:30:00'),
(896, 330489980, 1, '39', NULL, 'Ламінатор', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200358', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:47:25'),
(897, 330489980, 1, '39', NULL, 'Стелаж меблевий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200359', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:47:25'),
(898, 330489980, 1, '39', NULL, 'Шафа металева 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10400038', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:47:25'),
(899, 330489980, 1, '39', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:47:25'),
(900, 330489980, 1, '39', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:47:25'),
(901, 330489980, 1, '39', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:47:25'),
(902, 330489980, 1, '39', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:47:25'),
(903, 330489980, 1, '39', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200113', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:47:25'),
(904, 330489980, 1, '39', NULL, 'Вішак чорний напольний', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138018', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:47:25'),
(905, 330489980, 1, '39', NULL, 'Сейф 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136161', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:47:25'),
(906, 330489980, 1, '39', NULL, 'Стіл комп\'ютерний 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136194', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:47:25'),
(907, 330489980, 1, '39', NULL, 'Стіл комп\'ютерний 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136193', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:47:25'),
(908, 330489980, 1, '39', NULL, 'Шафа для одягу 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136271', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:47:25'),
(909, 330489980, 1, '39', NULL, 'Шафа для одягу 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136271', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:47:25'),
(910, 330489980, 1, '39', NULL, 'Стіл кутовий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1136157', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:47:25'),
(911, 330489980, 1, '39', NULL, 'Драбина 4сх', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200367', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:47:25'),
(912, 330489980, 1, '39', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, 'Nordis', NULL, NULL, '11200230', 1, 'шт', NULL, 0, NULL, '2025-11-26 10:47:25'),
(913, 330489980, 1, '37', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467040', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:07:20'),
(914, 330489980, 1, '37', NULL, 'Монітор', NULL, NULL, 0, NULL, 'LG', NULL, NULL, '101467040', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:07:20'),
(915, 330489980, 1, '37', NULL, 'Клавіатура', NULL, NULL, 0, NULL, 'Logitech', NULL, NULL, 'INV-1-37-003', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:07:20'),
(916, 330489980, 1, '37', NULL, 'Миша', NULL, NULL, 0, NULL, 'Logitech', NULL, NULL, 'INV-1-37-004', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:07:20'),
(917, 330489980, 1, '37', NULL, 'Стіл комп\'ютерний 2ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1136193', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:07:20'),
(918, 330489980, 1, '37', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200170', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:07:20'),
(919, 330489980, 1, '37', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '101467058', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:07:20'),
(920, 330489980, 1, '37', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467039', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:07:20'),
(921, 330489980, 1, '37', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467039', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:07:20'),
(922, 330489980, 1, '37', NULL, 'Камера', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200345', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:07:20'),
(923, 330489980, 1, '37', NULL, 'Колонки', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200346', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:07:20'),
(924, 330489980, 1, '37', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', NULL, 'UV4Y038322', '101467090', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:07:20'),
(925, 330489980, 1, '37', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, 'GROL', NULL, NULL, '101497044', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:07:20'),
(926, 330489980, 1, '37', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138050', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:07:20'),
(927, 330489980, 1, '37', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:07:20'),
(928, 330489980, 1, '37', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:07:20'),
(929, 330489980, 1, '37', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:07:20'),
(930, 330489980, 1, '37', NULL, 'Стіл комп\'ютерний 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-37-018', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:07:20'),
(931, 330489980, 1, '37', NULL, 'Тумба 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-37-019', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:07:20'),
(932, 330489980, 1, '37', NULL, 'Тумба 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-37-020', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:07:20'),
(933, 330489980, 1, '37', NULL, 'Тумба 4ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-37-021', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:07:20'),
(934, 330489980, 1, '37', NULL, 'Шафа для документів відкрита 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-37-022', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:07:20'),
(935, 330489980, 1, '37', NULL, 'Шафа для документів відкрита 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-37-023', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:07:20'),
(936, 330489980, 1, '37', NULL, 'Шафа для одягу 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136772', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:07:20'),
(937, 330489980, 1, '37', NULL, 'Шафа для одягу 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136772', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:07:20'),
(938, 330489980, 1, '37', NULL, 'Подовжувач білий 1.8м', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-37-001', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:08:42'),
(939, 330489980, 1, '37', NULL, 'Світч', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-37-002', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:08:42'),
(940, 330489980, 1, '38', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', 'UV4Y043790', '101467103', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:18:19'),
(941, 330489980, 1, '38', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10400055', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:18:19'),
(942, 330489980, 1, '38', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '11200114', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:18:19'),
(943, 330489980, 1, '38', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200115', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:18:19'),
(944, 330489980, 1, '38', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1137450', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:18:19'),
(945, 330489980, 1, '38', NULL, 'Камера', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200345', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:18:19'),
(946, 330489980, 1, '38', NULL, 'Колонки', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200346', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:18:19'),
(947, 330489980, 1, '38', NULL, 'Тумба 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138029', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:18:19'),
(948, 330489980, 1, '38', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '112000113', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:18:19'),
(949, 330489980, 1, '38', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101497045', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:18:19'),
(950, 330489980, 1, '38', NULL, 'Сейф 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136165', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:18:19'),
(951, 330489980, 1, '38', NULL, 'Стіл комп\'ютерний 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136194', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:18:19'),
(952, 330489980, 1, '38', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11137494', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:18:19'),
(953, 330489980, 1, '38', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '11137494', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:18:19'),
(954, 330489980, 1, '38', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200115', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:18:19'),
(955, 330489980, 1, '38', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200115', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:18:19'),
(956, 330489980, 1, '38', NULL, 'Стіл 2ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136194', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:18:19'),
(957, 330489980, 1, '38', NULL, 'Тумба 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138029', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:18:19'),
(958, 330489980, 1, '38', NULL, 'Подовжувач білий 3м', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-38-019', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:18:19'),
(959, 330489980, 1, '38', NULL, 'Вішак чорний напольний', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138018', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:18:19'),
(960, 330489980, 1, '38', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:18:19'),
(961, 330489980, 1, '38', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:18:19'),
(962, 330489980, 1, '38', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:18:19'),
(963, 330489980, 1, '38', NULL, 'Шафа для одягу 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136772', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:18:19'),
(964, 330489980, 1, '38', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136877', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:18:19'),
(965, 330489980, 1, '38', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136877', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:18:19'),
(966, 330489980, 1, '38', NULL, 'Шафа для документів 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-38-027', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:18:19'),
(967, 330489980, 1, '38', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-38-001', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:18:34'),
(968, 330489980, 1, '41', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101497043', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:31:35'),
(969, 330489980, 1, '41', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10149752', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:31:35'),
(970, 330489980, 1, '41', NULL, 'Диван без підлокітників', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-41-003', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:31:35'),
(971, 330489980, 1, '41', NULL, 'Диван без підлокітників', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-41-004', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:31:35'),
(972, 330489980, 1, '41', NULL, 'Холодильник', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138020', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:31:35'),
(973, 330489980, 1, '41', NULL, 'Стіл комп\'ютерний 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136194', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:31:35'),
(974, 330489980, 1, '41', NULL, 'Стіл комп\'ютерний 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136194', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:31:35'),
(975, 330489980, 1, '41', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:31:35'),
(976, 330489980, 1, '41', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:31:35'),
(977, 330489980, 1, '41', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:31:35'),
(978, 330489980, 1, '41', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:31:35'),
(979, 330489980, 1, '41', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:31:35'),
(980, 330489980, 1, '41', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:31:35'),
(981, 330489980, 1, '41', NULL, 'Шафа для одягу 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200352', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:31:35'),
(982, 330489980, 1, '41', NULL, 'Шафа для одягу 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200352', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:31:35'),
(983, 330489980, 1, '41', NULL, 'Шафа для одягу 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200352', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:31:35'),
(984, 330489980, 1, '41', NULL, 'Шафа для одягу 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200352', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:31:35'),
(985, 330489980, 1, '41', NULL, 'Комод відкритий 3секції', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136248', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:31:35'),
(986, 330489980, 1, '41', NULL, 'Роутер', NULL, NULL, 0, NULL, 'Netis', NULL, NULL, 'INV-1-41-019', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:31:35'),
(987, 330489980, 1, '41', NULL, 'Комод закритий', NULL, NULL, 0, NULL, 'Netis', NULL, NULL, '11200353', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:31:35'),
(988, 330489980, 1, '41', NULL, 'Тумба 2дв', NULL, NULL, 0, NULL, 'Netis', NULL, NULL, '11200350', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:31:35'),
(989, 330489980, 1, '41', NULL, 'Стіл комп\'ютерний', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136194', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:31:35'),
(990, 330489980, 1, '41', NULL, 'Шафа для одягу 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138033', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:31:35'),
(991, 330489980, 1, '41', NULL, 'Шафа для одягу 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136289', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:31:35'),
(992, 330489980, 1, '41', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-41-025', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:31:35'),
(993, 330489980, 1, '41', NULL, 'Шафа для документів 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:37:38'),
(994, 330489980, 1, '41', NULL, 'Шафа для одягу 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136089', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:37:38');
INSERT INTO `room_inventory` (`id`, `admin_telegram_id`, `branch_id`, `room_number`, `template_id`, `equipment_type`, `full_name`, `category`, `is_priority`, `balance_code`, `brand`, `model`, `serial_number`, `inventory_number`, `quantity`, `unit`, `price`, `min_quantity`, `notes`, `created_at`) VALUES
(995, 330489980, 1, '41', NULL, 'Шафа для документів  1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:37:38'),
(996, 330489980, 1, '41', NULL, 'Тумба 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:37:38'),
(997, 330489980, 1, '41', NULL, 'Тумба 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:37:38'),
(998, 330489980, 1, '41', NULL, 'Тумба 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:37:38'),
(999, 330489980, 1, '41', NULL, 'Тумба 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:37:38'),
(1000, 330489980, 1, '41', NULL, 'Тумба 1дв скло', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-41-008', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:37:38'),
(1001, 330489980, 1, '41', NULL, 'Тумба 1дв скло', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-41-009', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:37:38'),
(1002, 330489980, 1, '41', NULL, 'Шафа для одягу чорний', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-41-010', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:37:38'),
(1003, 330489980, 1, '45', NULL, 'Шафа металева 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10400037', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:45:42'),
(1004, 330489980, 1, '45', NULL, 'Стелаж металевий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1136060', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:45:42'),
(1005, 330489980, 1, '45', NULL, 'Стелаж металевий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1136060', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:45:42'),
(1006, 330489980, 1, '45', NULL, 'Диван без підлокітників', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-45-004', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:45:42'),
(1007, 330489980, 1, '45', NULL, 'Стіл комп\'ютерний', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-45-005', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:45:42'),
(1008, 330489980, 1, '45', NULL, 'Тумба', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-45-006', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:45:42'),
(1009, 330489980, 1, '45', NULL, 'Тумба', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-45-007', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:45:42'),
(1010, 330489980, 1, '45', NULL, 'Стіл', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-45-008', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:45:42'),
(1011, 330489980, 1, '45', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1120051', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:45:42'),
(1012, 330489980, 1, '45', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-45-010', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:45:42'),
(1013, 330489980, 1, '45', NULL, 'Шафа', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-45-011', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:45:42'),
(1014, 330489980, 1, '45', NULL, 'Шафа', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-45-012', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:45:42'),
(1015, 330489980, 1, '45', NULL, 'Шафа', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-45-013', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:45:42'),
(1016, 330489980, 1, '45', NULL, 'Шафа', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-45-014', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:45:42'),
(1017, 330489980, 1, '45', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136218', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:45:42'),
(1018, 330489980, 1, '45', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:45:42'),
(1019, 330489980, 1, '45', NULL, 'Шафа для одягу 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136134', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:45:42'),
(1020, 330489980, 1, '45\\4', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467068', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:52:46'),
(1021, 330489980, 1, '45\\4', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '101467068', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:52:46'),
(1022, 330489980, 1, '45\\4', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467068', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:52:46'),
(1023, 330489980, 1, '45\\4', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467051', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:52:46'),
(1024, 330489980, 1, '45\\4', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', NULL, 'UV4Y038584', '101467076', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:52:46'),
(1025, 330489980, 1, '45\\4', NULL, 'Стіл комп\'ютерний 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-45\\4-006', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:52:46'),
(1026, 330489980, 1, '45\\4', NULL, 'Стіл комп\'ютерний 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-45\\4-007', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:52:46'),
(1027, 330489980, 1, '45\\4', NULL, 'Камера', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200345', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:52:46'),
(1028, 330489980, 1, '45\\4', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11137494', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:52:46'),
(1029, 330489980, 1, '45\\4', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '11200114', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:52:46'),
(1030, 330489980, 1, '45\\4', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '112000115', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:52:46'),
(1031, 330489980, 1, '45\\4', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-45\\4-013', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:52:46'),
(1032, 330489980, 1, '45\\4', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10149746', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:52:46'),
(1033, 330489980, 1, '45\\4', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:52:46'),
(1034, 330489980, 1, '45\\4', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:52:46'),
(1035, 330489980, 1, '45\\4', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:52:46'),
(1036, 330489980, 1, '45\\4', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:52:46'),
(1037, 330489980, 1, '45\\4', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '112000113', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:52:46'),
(1038, 330489980, 1, '45\\4', NULL, 'Шафа для документів відкрита 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136752', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:52:46'),
(1039, 330489980, 1, '45\\4', NULL, 'Шафадля одягу 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136772', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:52:46'),
(1040, 330489980, 1, '45\\4', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-45\\4-022', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:52:46'),
(1041, 330489980, 1, '45\\4', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-45\\4-023', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:52:46'),
(1042, 330489980, 1, '45\\4', NULL, 'Роутер', NULL, NULL, 0, NULL, 'TP-Link', NULL, NULL, 'INV-1-45\\4-024', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:52:46'),
(1043, 330489980, 1, '45\\4', NULL, 'Подовжувач', NULL, NULL, 0, NULL, 'TP-Link', NULL, NULL, 'INV-1-45\\4-025', 1, 'шт', NULL, 0, NULL, '2025-11-26 11:52:46'),
(1044, 330489980, 1, '45\\3', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', NULL, 'UV4Y039061', '101467092', 1, 'шт', NULL, 0, NULL, '2025-11-27 05:59:15'),
(1045, 330489980, 1, '45\\3', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200157', 1, 'шт', NULL, 0, NULL, '2025-11-27 05:59:15'),
(1046, 330489980, 1, '45\\3', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '1137731', 1, 'шт', NULL, 0, NULL, '2025-11-27 05:59:15'),
(1047, 330489980, 1, '45\\3', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200298', 1, 'шт', NULL, 0, NULL, '2025-11-27 05:59:15'),
(1048, 330489980, 1, '45\\3', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-45\\3-006', 1, 'шт', NULL, 0, NULL, '2025-11-27 05:59:15'),
(1049, 330489980, 1, '45\\3', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467058', 1, 'шт', NULL, 0, NULL, '2025-11-27 05:59:15'),
(1050, 330489980, 1, '45\\3', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '101467058', 1, 'шт', NULL, 0, NULL, '2025-11-27 05:59:15'),
(1051, 330489980, 1, '45\\3', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-45\\3-009', 1, 'шт', NULL, 0, NULL, '2025-11-27 05:59:15'),
(1052, 330489980, 1, '45\\3', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-45\\3-010', 1, 'шт', NULL, 0, NULL, '2025-11-27 05:59:15'),
(1053, 330489980, 1, '45\\3', NULL, 'Камера', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200345', 1, 'шт', NULL, 0, NULL, '2025-11-27 05:59:15'),
(1054, 330489980, 1, '45\\3', NULL, 'Колонки', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200346', 1, 'шт', NULL, 0, NULL, '2025-11-27 05:59:15'),
(1055, 330489980, 1, '45\\3', NULL, 'Подовжувач чорний', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-45\\3-013', 1, 'шт', NULL, 0, NULL, '2025-11-27 05:59:15'),
(1056, 330489980, 1, '45\\3', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101497036', 1, 'шт', NULL, 0, NULL, '2025-11-27 05:59:15'),
(1057, 330489980, 1, '45\\3', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200113', 1, 'шт', NULL, 0, NULL, '2025-11-27 05:59:15'),
(1058, 330489980, 1, '45\\3', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200113', 1, 'шт', NULL, 0, NULL, '2025-11-27 05:59:15'),
(1059, 330489980, 1, '45\\3', NULL, 'Тумба 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1113837', 1, 'шт', NULL, 0, NULL, '2025-11-27 05:59:15'),
(1060, 330489980, 1, '45\\3', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1120051', 1, 'шт', NULL, 0, NULL, '2025-11-27 05:59:15'),
(1061, 330489980, 1, '45\\3', NULL, 'Стіл комп\'ютерний 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-45\\3-019', 1, 'шт', NULL, 0, NULL, '2025-11-27 05:59:15'),
(1062, 330489980, 1, '45\\3', NULL, 'Стіл комп\'ютерний 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-45\\3-020', 1, 'шт', NULL, 0, NULL, '2025-11-27 05:59:15'),
(1063, 330489980, 1, '45\\3', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277', 1, 'шт', NULL, 0, NULL, '2025-11-27 05:59:15'),
(1064, 330489980, 1, '45\\3', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200051', 1, 'шт', NULL, 0, NULL, '2025-11-27 05:59:15'),
(1065, 330489980, 1, '45\\3', NULL, 'Шафа для одягу 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10400025', 1, 'шт', NULL, 0, NULL, '2025-11-27 05:59:15'),
(1066, 330489980, 1, '45\\3', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-45\\3-024', 1, 'шт', NULL, 0, NULL, '2025-11-27 05:59:15'),
(1067, 330489980, 1, '45\\3', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-45\\3-025', 1, 'шт', NULL, 0, NULL, '2025-11-27 05:59:15'),
(1068, 330489980, 1, '45\\3', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-45\\3-026', 1, 'шт', NULL, 0, NULL, '2025-11-27 05:59:15'),
(1069, 330489980, 1, '45\\3', NULL, 'Роутер', NULL, NULL, 0, NULL, 'TP_Link', NULL, NULL, 'INV-1-45\\3-027', 1, 'шт', NULL, 0, NULL, '2025-11-27 05:59:15'),
(1070, 330489980, 1, 'Реєстратура 2 поверх', NULL, 'Стіл комп\'ютерний 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136194', 1, 'шт', NULL, 0, NULL, '2025-11-27 06:06:37'),
(1071, 330489980, 1, 'Реєстратура 2 поверх', NULL, 'Стелаж деревяний (для карточек 15шт)', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-Реєстратура 2 поверх-002', 1, 'шт', NULL, 0, NULL, '2025-11-27 06:06:37'),
(1072, 330489980, 1, 'Реєстратура 2 поверх', NULL, 'Шафа металева -', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-Реєстратура 2 поверх-003', 1, 'шт', NULL, 0, NULL, '2025-11-27 06:06:37'),
(1073, 330489980, 1, '45\\1', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10400077', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:36:18'),
(1074, 330489980, 1, '45\\1', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200230', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:36:18'),
(1075, 330489980, 1, '45\\1', NULL, 'Стіл комп\'ютерний 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136194', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:36:18'),
(1076, 330489980, 1, '45\\1', NULL, 'Стіл комп\'ютерний 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '111361910', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:36:18'),
(1077, 330489980, 1, '45\\1', NULL, 'Стіл 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-45\\1-005', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:36:18'),
(1078, 330489980, 1, '45\\1', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:36:18'),
(1079, 330489980, 1, '45\\1', NULL, 'Тумба 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136244', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:36:18'),
(1080, 330489980, 1, '45\\1', NULL, 'Тумба 1ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-45\\1-008', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:36:18'),
(1081, 330489980, 1, '45\\1', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1136209', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:36:18'),
(1082, 330489980, 1, '45\\1', NULL, 'Подовжувач чорний 1.8м', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-45\\1-010', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:36:18'),
(1083, 330489980, 1, '45\\1', NULL, 'Холодильник', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101497041', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:36:18'),
(1084, 330489980, 1, '45\\1', NULL, 'Стіл комп\'ютерний', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136194', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:45:22'),
(1085, 330489980, 1, '45\\1', NULL, 'Стіл комп\'ютерний', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-45\\1-003', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:45:22'),
(1086, 330489980, 1, '45\\1', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:45:22'),
(1087, 330489980, 1, '45\\1', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136079', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:45:22'),
(1088, 330489980, 1, '45\\1', NULL, 'Стіл 4ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136181', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:45:22'),
(1089, 330489980, 1, '45\\1', NULL, 'Стіл 2ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-45\\1-007', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:45:22'),
(1090, 330489980, 1, '45\\1', NULL, 'Подовжувач білий', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-45\\1-008', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:45:22'),
(1091, 330489980, 1, '45\\1', NULL, 'ДБЖ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200112', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:45:22'),
(1092, 330489980, 1, '45\\1', NULL, 'Шафа для одягу 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136289', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:45:22'),
(1093, 330489980, 1, '45\\1', NULL, 'Пенал відкритий 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:45:22'),
(1094, 330489980, 1, '45\\1', NULL, 'Шафа для одягу 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:45:22'),
(1095, 330489980, 1, '45\\1', NULL, 'Шафа для документів 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:45:22'),
(1096, 330489980, 1, '45\\1', NULL, 'Шафа для одягу 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136289', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:45:22'),
(1097, 330489980, 1, '45\\1', NULL, 'Шафа для одягу 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136289', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:45:22'),
(1098, 330489980, 1, '45\\1', NULL, 'Подовжувач чорний 1.8м', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-45\\1-016', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:45:22'),
(1099, 330489980, 1, 'Кридор 2п', NULL, 'Кушетка зі спинкою', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136740', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:49:28'),
(1100, 330489980, 1, 'Кридор 2п', NULL, 'Кушетка зі спинкою', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136740', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:49:28'),
(1101, 330489980, 1, 'Кридор 2п', NULL, 'Кушетка зі спинкою', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136740', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:49:28'),
(1102, 330489980, 1, 'Кридор 2п', NULL, 'Кушетка зі спинкою', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136740', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:49:28'),
(1103, 330489980, 1, 'Кридор 2п', NULL, 'Кушетка зі спинкою', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136740', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:49:28'),
(1104, 330489980, 1, 'Кридор 2п', NULL, 'Диван без підлокітників', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-Кридор 2п-006', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:49:28'),
(1105, 330489980, 1, 'Кридор 2п', NULL, 'Диван без підлокітників', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-Кридор 2п-007', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:49:28'),
(1106, 330489980, 1, 'Кридор 2п', NULL, 'Диван без підлокітників', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-Кридор 2п-008', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:49:28'),
(1107, 330489980, 1, 'Кридор 2п', NULL, 'Диван без підлокітників', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-Кридор 2п-009', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:49:28'),
(1108, 330489980, 1, 'Кридор 2п', NULL, 'Диван без підлокітників', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-Кридор 2п-010', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:49:28'),
(1109, 330489980, 1, 'Кридор 2п', NULL, 'Диван без підлокітників', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-Кридор 2п-011', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:49:28'),
(1110, 330489980, 1, 'Кридор 2п', NULL, 'Диванчик кутовий', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-Кридор 2п-001', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:50:52'),
(1111, 330489980, 1, 'Кридор 3п', NULL, 'Диванчик кутовий', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-Кридор 3п-001', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:53:25'),
(1112, 330489980, 1, 'Кридор 3п', NULL, 'Диван без підлокітників', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-Кридор 3п-002', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:53:25'),
(1113, 330489980, 1, 'Кридор 3п', NULL, 'Диван без підлокітників', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-Кридор 3п-003', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:53:25'),
(1114, 330489980, 1, 'Кридор 3п', NULL, 'Диван без підлокітників', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-Кридор 3п-004', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:53:25'),
(1115, 330489980, 1, 'Кридор 3п', NULL, 'Диван без підлокітників', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-Кридор 3п-005', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:53:25'),
(1116, 330489980, 1, 'Кридор 3п', NULL, 'Диван без підлокітників', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-Кридор 3п-006', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:53:25'),
(1117, 330489980, 1, 'Кридор 3п', NULL, 'Диван без підлокітників', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-Кридор 3п-007', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:53:25'),
(1118, 330489980, 1, 'Кридор 3п', NULL, 'Кушетка зі спинкою', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136740', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:53:25'),
(1119, 330489980, 1, 'Кридор 3п', NULL, 'Телевізор', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10400007', 1, 'шт', NULL, 0, NULL, '2025-11-27 07:53:25'),
(1120, 330489980, 1, '52', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200157', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:08:19'),
(1121, 330489980, 1, '52', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Asus', NULL, NULL, '101467045', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:08:19'),
(1122, 330489980, 1, '52', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-52-003', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:08:19'),
(1123, 330489980, 1, '52', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467067', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:08:19'),
(1124, 330489980, 1, '52', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200142', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:08:19'),
(1125, 330489980, 1, '52', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Benq', NULL, NULL, '11200158', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:08:19'),
(1126, 330489980, 1, '52', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467066', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:08:19'),
(1127, 330489980, 1, '52', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200152', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:08:19'),
(1128, 330489980, 1, '52', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Samsung', NULL, 'CNB3L1JF92', '11137468', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:08:19'),
(1129, 330489980, 1, '52', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101497035', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:08:19'),
(1130, 330489980, 1, '52', NULL, 'Холодильник', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138045', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:08:19'),
(1131, 330489980, 1, '52', NULL, 'Подовжувач білий', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-52-013', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:08:19'),
(1132, 330489980, 1, '52', NULL, 'Стіл', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136194', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:08:19'),
(1133, 330489980, 1, '52', NULL, 'Стіл', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136194', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:08:19'),
(1134, 330489980, 1, '52', NULL, 'Тумба 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-52-016', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:08:19'),
(1135, 330489980, 1, '52', NULL, 'Подовжувач чорний', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-52-017', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:08:19'),
(1136, 330489980, 1, '52', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:08:19'),
(1137, 330489980, 1, '52', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:08:19'),
(1138, 330489980, 1, '52', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:08:19'),
(1139, 330489980, 1, '52', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:08:19'),
(1140, 330489980, 1, '52', NULL, 'Тумба 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-52-022', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:08:19'),
(1141, 330489980, 1, '52', NULL, 'Тумба відкрита 2 полиці', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138032', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:08:19'),
(1142, 330489980, 1, '52', NULL, 'Стіл комп\'ютерний кутовий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200432', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:08:19'),
(1143, 330489980, 1, '52', NULL, 'ДБЖ', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-52-025', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:08:19'),
(1144, 330489980, 1, '52', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-52-026', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:08:19'),
(1145, 330489980, 1, '52', NULL, 'Пенал кутовий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138036', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:08:19'),
(1146, 330489980, 1, '52', NULL, 'Пенал відкритий 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138035', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:08:19'),
(1147, 330489980, 1, '52', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:08:19'),
(1148, 330489980, 1, '52', NULL, 'Шафа для одягу 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136772', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:08:19'),
(1149, 330489980, 1, '52', NULL, 'Шафа для одягу 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136772', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:08:19'),
(1150, 330489980, 1, '52', NULL, 'Шафа для одягу 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136772', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:08:19'),
(1151, 330489980, 1, '52', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467065', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1152, 330489980, 1, '52', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '101467065', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1153, 330489980, 1, '52', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467065', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1154, 330489980, 1, '52', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467065', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1155, 330489980, 1, '52', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '112000113', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1156, 330489980, 1, '52', NULL, 'Стіл комп\'ютерний', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-52-006', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1157, 330489980, 1, '52', NULL, 'Тумба 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-52-007', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1158, 330489980, 1, '52', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-52-008', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1159, 330489980, 1, '52', NULL, 'ДБЖ', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-52-009', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1160, 330489980, 1, '52', NULL, 'Шафа для одягу 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136772', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1161, 330489980, 1, '52', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1162, 330489980, 1, '52', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1163, 330489980, 1, '52', NULL, 'Шафа для одягу 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136772', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1164, 330489980, 1, '52', NULL, 'Стіл', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136194', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1165, 330489980, 1, '52', NULL, 'ДБЖ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11100112', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1166, 330489980, 1, '52', NULL, 'Тумба 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138029', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1167, 330489980, 1, '52', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-52-017', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1168, 330489980, 1, '52', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200142', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1169, 330489980, 1, '52', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '10400042', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1170, 330489980, 1, '52', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467067', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1171, 330489980, 1, '52', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-52-021', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1172, 330489980, 1, '52', NULL, 'Подовжувач білий', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-52-022', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1173, 330489980, 1, '52', NULL, 'Роутер', NULL, NULL, 0, NULL, 'Xizomi', NULL, NULL, 'INV-1-52-023', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1174, 330489980, 1, '52', NULL, 'Телефон', NULL, NULL, 0, NULL, 'Fanvil', NULL, NULL, '11139275', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1175, 330489980, 1, '52', NULL, 'Лампа настільна', NULL, NULL, 0, NULL, 'Fanvil', NULL, NULL, '11138015-1', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1176, 330489980, 1, '52', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, 'Fanvil', NULL, NULL, '10149749', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1177, 330489980, 1, '52', NULL, 'Крісло', NULL, NULL, 0, NULL, 'Fanvil', NULL, NULL, '11138050', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1178, 330489980, 1, '52', NULL, 'Крісло', NULL, NULL, 0, NULL, 'Fanvil', NULL, NULL, '11138050', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1179, 330489980, 1, '52', NULL, 'Стіл комп\'ютерний', NULL, NULL, 0, NULL, 'Fanvil', NULL, NULL, '11136194', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1180, 330489980, 1, '52', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, 'Fanvil', NULL, NULL, 'INV-1-52-030', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1181, 330489980, 1, '52', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467054', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1182, 330489980, 1, '52', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '101467054', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1183, 330489980, 1, '52', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467064', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1184, 330489980, 1, '52', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-52-034', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1185, 330489980, 1, '52', NULL, 'Тумба відкрита з полицями', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200049', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1186, 330489980, 1, '52', NULL, 'Подовжувач чорний 1.8м', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-52-036', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1187, 330489980, 1, '52', NULL, 'ДБЖ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200112', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1188, 330489980, 1, '52', NULL, 'Тумба 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-52-038', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1189, 330489980, 1, '52', NULL, 'Лампа настільна', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138016', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1190, 330489980, 1, '52', NULL, 'Шафа для одягу 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136772', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1191, 330489980, 1, '52', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1192, 330489980, 1, '52', NULL, 'Полиця кутова', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138043', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1193, 330489980, 1, '52', NULL, 'Полиця кутова', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138043', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:24:09'),
(1194, 330489980, 1, '52', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200157', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:29:18'),
(1195, 330489980, 1, '52', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '11200158', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:29:18'),
(1196, 330489980, 1, '52', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200157', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:29:18'),
(1197, 330489980, 1, '52', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200157', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:29:18'),
(1198, 330489980, 1, '52', NULL, 'Телефон', NULL, NULL, 0, NULL, 'Fanvil', NULL, NULL, '11137295', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:29:18'),
(1199, 330489980, 1, '52', NULL, 'Пенал кутовий з полицями', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-52-006', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:29:18'),
(1200, 330489980, 1, '52', NULL, 'Пенал кутовий з полицями', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-52-007', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:29:18'),
(1201, 330489980, 1, '52', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:29:18'),
(1202, 330489980, 1, '52', NULL, 'Стіл комп\'ютерний кутовий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200052', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:29:18'),
(1203, 330489980, 1, '52', NULL, 'ДБЖ', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-52-010', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:29:18'),
(1204, 330489980, 1, '52', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-52-011', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:29:18'),
(1205, 330489980, 1, '52', NULL, 'Стіл приставний 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-52-012', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:29:18'),
(1206, 330489980, 1, '52', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138017', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:29:18'),
(1207, 330489980, 1, '52', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138050', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:29:18'),
(1208, 330489980, 1, '52', NULL, 'Сейф 1дв', NULL, NULL, 0, NULL, 'Ferocon', 'BL-65E', NULL, '11138044', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:30:45'),
(1209, 330489980, 1, '52', NULL, 'Лампа настільна', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138015', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:30:45'),
(1210, 330489980, 1, '49', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467070', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1211, 330489980, 1, '49', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '101487017', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1212, 330489980, 1, '49', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200113', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1213, 330489980, 1, '49', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'ИНВ', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1214, 330489980, 1, '49', NULL, 'Сейф 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136161', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1215, 330489980, 1, '49', NULL, 'Пенал відкритий 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1216, 330489980, 1, '49', NULL, 'Вішак чорний', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138018', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1217, 330489980, 1, '49', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '112000113', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1218, 330489980, 1, '49', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101497036', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1219, 330489980, 1, '49', NULL, 'Стіл комп\'ютерний', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-49-010', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1220, 330489980, 1, '49', NULL, 'Стіл 2ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136190', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1221, 330489980, 1, '49', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', NULL, 'UV4Y028549', '101467095', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1222, 330489980, 1, '49', NULL, 'Тумба 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-49-013', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1223, 330489980, 1, '49', NULL, 'Роутер', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-49-014', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1224, 330489980, 1, '49', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-49-015', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1225, 330489980, 1, '49', NULL, 'Подовжувач чорний 1.8м', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-49-016', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1226, 330489980, 1, '49', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-49-017', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1227, 330489980, 1, '49', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-49-018', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1228, 330489980, 1, '49', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '112000113', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1229, 330489980, 1, '49', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1230, 330489980, 1, '49', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-49-021', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1231, 330489980, 1, '49', NULL, 'Шафа для одягу 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136272', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1232, 330489980, 1, '49', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', NULL, 'UV4Y037946', '101467080', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1233, 330489980, 1, '49', NULL, 'Телефон', NULL, NULL, 0, NULL, 'Fanvil', NULL, NULL, '11137295', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1234, 330489980, 1, '49', NULL, 'Подовжувач білий', NULL, NULL, 0, NULL, 'Fanvil', NULL, NULL, 'INV-1-49-025', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1235, 330489980, 1, '49', NULL, 'Подовжувач чорний', NULL, NULL, 0, NULL, 'Fanvil', NULL, NULL, 'INV-1-49-026', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1236, 330489980, 1, '49', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467050', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1237, 330489980, 1, '49', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '101467050', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1238, 330489980, 1, '49', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467050', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1239, 330489980, 1, '49', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-49-030', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1240, 330489980, 1, '49', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-49-031', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1241, 330489980, 1, '49', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-49-032', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1242, 330489980, 1, '49', NULL, 'Тумба 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-49-033', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1243, 330489980, 1, '49', NULL, 'Стіл з приставкою', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-49-034', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1244, 330489980, 1, '49', NULL, 'Стіл відкритий 2п', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136190', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1245, 330489980, 1, '49', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1246, 330489980, 1, '49', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '112000113', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1247, 330489980, 1, '49', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200050', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1248, 330489980, 1, '49', NULL, 'Диван без підлокітників', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-49-039', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1249, 330489980, 1, '49', NULL, 'Шафа для одягу 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11135764', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1250, 330489980, 1, '49', NULL, 'Шафа для докуменетів відкрита 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136752', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1251, 330489980, 1, '49', NULL, 'Шафа для докуменетів відкрита 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136752', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1252, 330489980, 1, '49', NULL, 'Полиці кутові відкриті', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-49-043', 1, 'шт', NULL, 0, NULL, '2025-11-27 08:47:13'),
(1253, 330489980, 1, '51', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', 'UV4Y042905', '11200174', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:08:44'),
(1254, 330489980, 1, '51', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, 'Midea', NULL, NULL, '10149750', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:08:44'),
(1255, 330489980, 1, '51', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200142', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:08:44'),
(1256, 330489980, 1, '51', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '11200158', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:08:44'),
(1257, 330489980, 1, '51', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200157', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:08:44'),
(1258, 330489980, 1, '51', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200157', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:08:44'),
(1259, 330489980, 1, '51', NULL, 'ДБЖ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200112', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:08:44'),
(1260, 330489980, 1, '51', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-51-008', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:08:44'),
(1261, 330489980, 1, '51', NULL, 'Подовжувач білий', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-51-009', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:08:44'),
(1262, 330489980, 1, '51', NULL, 'Стіл комп\'ютерний 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-51-010', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:08:44'),
(1263, 330489980, 1, '51', NULL, 'Пенал відкритий 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1138105', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:08:44'),
(1264, 330489980, 1, '51', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200051', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:08:44'),
(1265, 330489980, 1, '51', NULL, 'Шафа для одягу 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10400023', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:08:44'),
(1266, 330489980, 1, '51', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200051', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:08:44'),
(1267, 330489980, 1, '51', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-51-015', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:08:44'),
(1268, 330489980, 1, '51', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-51-016', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:08:44'),
(1269, 330489980, 1, '51', NULL, 'Стіл комп\'ютерний 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-51-017', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:08:44'),
(1270, 330489980, 1, '51', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200157', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:08:44'),
(1271, 330489980, 1, '51', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '11200114', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:08:44'),
(1272, 330489980, 1, '51', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200296', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:08:44'),
(1273, 330489980, 1, '51', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200155', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:08:44'),
(1274, 330489980, 1, '51', NULL, 'ДБЖ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200112', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:08:44'),
(1275, 330489980, 1, '51', NULL, 'Подовжувач білий', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-51-023', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:08:44'),
(1276, 330489980, 1, '51', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:08:44'),
(1277, 330489980, 1, '51', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:08:44'),
(1278, 330489980, 1, '51', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138050', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:08:44'),
(1279, 330489980, 1, '51', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '112000113', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:08:44'),
(1280, 330489980, 1, '51', NULL, 'Бойлер ~50л', NULL, NULL, 0, NULL, 'Hi-Therm', NULL, NULL, '11200123', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:08:44'),
(1281, 330489980, 1, '47', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10400056', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:18:51'),
(1282, 330489980, 1, '47', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '11200114', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:18:51'),
(1283, 330489980, 1, '47', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-47-003', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:18:51'),
(1284, 330489980, 1, '47', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', NULL, 'UV4Y038156', '101467079', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:18:51'),
(1285, 330489980, 1, '47', NULL, 'Тумба 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-47-006', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:18:51'),
(1286, 330489980, 1, '47', NULL, 'Стіл комп\'ютерний 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200051', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:18:51'),
(1287, 330489980, 1, '47', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '112000113', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:18:51'),
(1288, 330489980, 1, '47', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-47-009', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:18:51'),
(1289, 330489980, 1, '47', NULL, 'Камера', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200345', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:18:51'),
(1290, 330489980, 1, '47', NULL, 'Колонки', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200346', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:18:51'),
(1291, 330489980, 1, '47', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467049', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:18:51'),
(1292, 330489980, 1, '47', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '101467049', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:18:51'),
(1293, 330489980, 1, '47', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467042', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:18:51'),
(1294, 330489980, 1, '47', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467040', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:18:51'),
(1295, 330489980, 1, '47', NULL, 'Роутер', NULL, NULL, 0, NULL, 'Netis', NULL, NULL, 'INV-1-47-017', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:18:51'),
(1296, 330489980, 1, '47', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11100040', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:18:51'),
(1297, 330489980, 1, '47', NULL, 'Тумба 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-47-019', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:18:51'),
(1298, 330489980, 1, '47', NULL, 'Стіл комп\'ютерний 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200051', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:18:51'),
(1299, 330489980, 1, '47', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138050', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:18:51'),
(1300, 330489980, 1, '47', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10149748', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:18:51'),
(1301, 330489980, 1, '47', NULL, 'Бойлер', NULL, NULL, 0, NULL, 'Hi-Therm', NULL, NULL, 'INV-1-47-023', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:18:51'),
(1302, 330489980, 1, '47', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, 'Hi-Therm', NULL, NULL, '11200051', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:18:51'),
(1303, 330489980, 1, '47', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, 'Hi-Therm', NULL, NULL, '11200051', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:18:51'),
(1304, 330489980, 1, '47', NULL, 'Шафа для одягу 2дв', NULL, NULL, 0, NULL, 'Hi-Therm', NULL, NULL, '10400022', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:18:51'),
(1305, 330489980, 1, '47', NULL, 'Вішак', NULL, NULL, 0, NULL, 'Hi-Therm', NULL, NULL, '11138018', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:18:51'),
(1306, 330489980, 1, '47', NULL, 'Стул', NULL, NULL, 0, NULL, 'Hi-Therm', NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:18:51'),
(1307, 330489980, 1, '47', NULL, 'Стул', NULL, NULL, 0, NULL, 'Hi-Therm', NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:18:51'),
(1308, 330489980, 1, '50', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11137494', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:29:28'),
(1309, 330489980, 1, '50', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '11200158', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:29:28'),
(1310, 330489980, 1, '50', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-46-003', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:29:28'),
(1311, 330489980, 2, 'к307', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', NULL, 'UV4Y028532', '101467109', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:29:28'),
(1312, 330489980, 1, '50', NULL, 'Телефон', NULL, NULL, 0, NULL, 'Fanvil', NULL, NULL, 'INV-1-50-006', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:29:28'),
(1313, 330489980, 1, '50', NULL, 'Лампа настільна біла', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1413815', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:29:28'),
(1314, 330489980, 1, '50', NULL, 'Камера', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200345', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:29:28'),
(1315, 330489980, 1, '50', NULL, 'Колонки', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200346', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:29:28'),
(1316, 330489980, 1, '50', NULL, 'ДБЖ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200112', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:29:28'),
(1317, 330489980, 1, '50', NULL, 'Подовжувач чорний 1.8м', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-50-011', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:29:28'),
(1318, 330489980, 1, '50', NULL, 'Світч', NULL, NULL, 0, NULL, 'TP-Link', NULL, NULL, 'INV-1-50-012', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:29:28'),
(1319, 330489980, 1, '50', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-50-013', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:29:28'),
(1320, 330489980, 1, '50', NULL, 'Стіл комп\'ютерний кутовий 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200102', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:29:28'),
(1321, 330489980, 1, '50', NULL, 'Тумба 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-50-015', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:29:28'),
(1322, 330489980, 1, '50', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200051', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:29:28');
INSERT INTO `room_inventory` (`id`, `admin_telegram_id`, `branch_id`, `room_number`, `template_id`, `equipment_type`, `full_name`, `category`, `is_priority`, `balance_code`, `brand`, `model`, `serial_number`, `inventory_number`, `quantity`, `unit`, `price`, `min_quantity`, `notes`, `created_at`) VALUES
(1323, 330489980, 1, '50', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200100', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:29:28'),
(1324, 330489980, 1, '50', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200100', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:29:28'),
(1325, 330489980, 1, '50', NULL, 'Шафа для одягу 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136772\\11200101', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:29:28'),
(1326, 330489980, 1, '50', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:29:28'),
(1327, 330489980, 1, '50', NULL, 'Диван без підлокітників', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-50-021', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:29:28'),
(1328, 330489980, 1, '50', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1120013', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:29:28'),
(1329, 330489980, 1, 'Серверна 2п', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200157', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:34:44'),
(1330, 330489980, 1, 'Серверна 2п', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '11137731', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:34:44'),
(1331, 330489980, 1, 'Серверна 2п', NULL, 'Клавіатура', NULL, NULL, 0, NULL, 'A4Tech', NULL, NULL, 'INV-1-Серверна 2п-003', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:34:44'),
(1332, 330489980, 1, 'Серверна 2п', NULL, 'ДБЖ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200112', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:34:44'),
(1333, 330489980, 1, 'Серверна 2п', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, 'Mitsubishi', NULL, NULL, 'B\\N', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:34:44'),
(1334, 330489980, 1, 'Серверна 2п', NULL, 'Стіл комп\'ютерний 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136194', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:34:44'),
(1335, 330489980, 1, 'Серверна 2п', NULL, 'Сервер', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-Серверна 2п-007', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:34:44'),
(1336, 330489980, 1, 'Серверна 2п', NULL, 'Сервер', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-Серверна 2п-008', 1, 'шт', NULL, 0, NULL, '2025-11-27 09:34:44'),
(1337, 330489980, 1, 'Підсобне приміщення', NULL, 'Шафа металева з 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10400033', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:26:45'),
(1338, 330489980, 1, 'Підсобне приміщення', NULL, 'Шафа металева з 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10400033', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:26:45'),
(1339, 330489980, 1, 'Підсобне приміщення', NULL, 'Холодильник', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138020', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:26:45'),
(1340, 330489980, 1, 'Підсобне приміщення', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '111136211', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:26:45'),
(1341, 330489980, 1, 'Підсобне приміщення', NULL, 'шафа для одягу 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136289', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:26:45'),
(1342, 330489980, 1, 'Підсобне приміщення', NULL, 'Кушетка', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1136009', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:26:45'),
(1343, 330489980, 1, '48', NULL, 'Драбина 3сх', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-48-001', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:29:42'),
(1344, 330489980, 1, '48', NULL, 'Пенал для документів відкритий', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-48-002', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:29:42'),
(1345, 330489980, 1, '48', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, 'OSAKA', NULL, NULL, '11137397', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:31:01'),
(1346, 330489980, 1, '58', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467071', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:39:42'),
(1347, 330489980, 1, '58', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '101467071', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:39:42'),
(1348, 330489980, 1, '58', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-58-003', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:39:42'),
(1349, 330489980, 1, '58', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200155', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:39:42'),
(1350, 330489980, 3, '106', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', 'UV4Y038134', '101467096', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:39:42'),
(1351, 330489980, 1, '58', NULL, 'ДБЖ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200112', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:39:42'),
(1352, 330489980, 1, '58', NULL, 'Подовжувач білий', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-58-007', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:39:42'),
(1353, 330489980, 1, '58', NULL, 'Подовжувач чорний', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-58-008', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:39:42'),
(1354, 330489980, 1, '58', NULL, 'Стіл комп\'ютерний кутовий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138080', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:39:42'),
(1355, 330489980, 1, '58', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '112000113', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:39:42'),
(1356, 330489980, 1, '58', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200230', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:39:42'),
(1357, 330489980, 1, '58', NULL, 'Тумба 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-58-012', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:39:42'),
(1358, 330489980, 1, '58', NULL, 'Кмодо відкритий 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-58-013', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:39:42'),
(1359, 330489980, 1, '58', NULL, 'Пенал відкритий', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-58-014', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:39:42'),
(1360, 330489980, 1, '58', NULL, 'Пенал відкритий', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-58-015', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:39:42'),
(1361, 330489980, 1, '58', NULL, 'Шафа для документів відкрита 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:39:42'),
(1362, 330489980, 1, '58', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136752', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:39:42'),
(1363, 330489980, 1, '58', NULL, 'Шафа для одягу', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138035', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:39:42'),
(1364, 330489980, 1, '58', NULL, 'Диван без підлокітників', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-58-019', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:39:42'),
(1365, 330489980, 1, '58', NULL, 'Диван без підлокітників', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-58-020', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:39:42'),
(1366, 330489980, 1, '58', NULL, 'Сейф подвійний', NULL, NULL, 0, NULL, 'AIKO', NULL, NULL, 'INV-1-58-021', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:39:42'),
(1367, 330489980, 1, '58', NULL, 'Журнальний столик', NULL, NULL, 0, NULL, 'AIKO', NULL, NULL, 'INV-1-58-022', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:39:42'),
(1368, 330489980, 1, '58', NULL, 'Холодильник', NULL, NULL, 0, NULL, 'AIKO', NULL, NULL, 'INV-1-58-023', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:39:42'),
(1369, 330489980, 1, '58', NULL, 'Подовжувач білий', NULL, NULL, 0, NULL, 'AIKO', NULL, NULL, 'INV-1-58-024', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:39:42'),
(1370, 330489980, 1, '58', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '112000113', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:40:49'),
(1371, 330489980, 1, '58', NULL, 'Стіл заокруглений', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-58-002', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:40:49'),
(1372, 330489980, 1, '55', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10400057', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:48:10'),
(1373, 330489980, 1, '55', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '11200114', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:48:10'),
(1374, 330489980, 1, '55', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200296', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:48:10'),
(1375, 330489980, 1, '55', NULL, 'Телефон', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11137296', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:48:10'),
(1376, 330489980, 1, '55', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', 'UV4Y038160', '11200174 ІІ', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:48:10'),
(1377, 330489980, 1, '55', NULL, 'ДБЖ', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-55-007', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:48:10'),
(1378, 330489980, 1, '55', NULL, 'Стіл кутовий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200102', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:48:10'),
(1379, 330489980, 1, '55', NULL, 'Тумба 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-55-009', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:48:10'),
(1380, 330489980, 1, '55', NULL, 'Тумба 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-55-010', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:48:10'),
(1381, 330489980, 1, '55', NULL, 'Тумба 2ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-55-011', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:48:10'),
(1382, 330489980, 1, '55', NULL, 'Роутер', NULL, NULL, 0, NULL, 'Netis', NULL, NULL, 'INV-1-55-012', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:48:10'),
(1383, 330489980, 1, '55', NULL, 'Холодильник', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101497037', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:48:10'),
(1384, 330489980, 1, '55', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200230-55', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:48:10'),
(1385, 330489980, 1, '55', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-55-015', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:48:10'),
(1386, 330489980, 1, '55', NULL, 'Тумба 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-55-016', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:48:10'),
(1387, 330489980, 1, '55', NULL, 'Шафа для документів відкрита 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:48:10'),
(1388, 330489980, 1, '55', NULL, 'Шафа для документів відкрита 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:48:10'),
(1389, 330489980, 1, '55', NULL, 'Шафа для документів 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136754', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:48:10'),
(1390, 330489980, 1, '55', NULL, 'Шафа для одягу 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136289', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:48:10'),
(1391, 330489980, 1, '55', NULL, 'Пенал відкритий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200051', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:48:10'),
(1392, 330489980, 1, '55', NULL, 'Крісло заокруглене', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-55-022', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:48:10'),
(1393, 330489980, 1, '55', NULL, 'Ноутбук', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10400276', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:55:51'),
(1394, 330489980, 1, '55', NULL, 'Стіл 2 тумби', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200428', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:55:51'),
(1395, 330489980, 1, '55', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1124563', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:55:51'),
(1396, 330489980, 1, '55', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1124563', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:55:51'),
(1397, 330489980, 1, '55', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1124563', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:55:51'),
(1398, 330489980, 1, '55', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1124563', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:55:51'),
(1399, 330489980, 1, '55', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1124563', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:55:51'),
(1400, 330489980, 1, '55', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1124563', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:55:51'),
(1401, 330489980, 1, '55', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:55:51'),
(1402, 330489980, 1, '55', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:55:51'),
(1403, 330489980, 1, '55', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:55:51'),
(1404, 330489980, 1, '55', NULL, 'Комод', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200320', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:55:51'),
(1405, 330489980, 1, '55', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, 'Midea', NULL, NULL, '10149747', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:55:51'),
(1406, 330489980, 1, '55', NULL, 'Стінка для документів та одягу', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200323', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:55:51'),
(1407, 330489980, 1, '55', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-55-015', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:55:51'),
(1408, 330489980, 1, '55', NULL, 'Тумба 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-55-016', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:55:51'),
(1409, 330489980, 1, '55', NULL, 'Шафа для одягу 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10400024', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:55:51'),
(1410, 330489980, 1, '55', NULL, 'Шафа для документів відкрита 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-55-018', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:55:51'),
(1411, 330489980, 1, '55', NULL, 'Журнальний столик', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-55-019', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:55:51'),
(1412, 330489980, 1, '55', NULL, 'Сейф 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136165', 1, 'шт', NULL, 0, NULL, '2025-11-27 11:55:51'),
(1413, 330489980, 1, '56', NULL, 'Ноутбук', NULL, NULL, 0, NULL, 'Asus', NULL, 'X4T3', '10400275-23', 1, 'шт', NULL, 0, NULL, '2025-11-27 12:05:45'),
(1414, 330489980, 1, '56', NULL, 'Роутер', NULL, NULL, 0, NULL, 'Netis', NULL, NULL, 'INV-1-56-002', 1, 'шт', NULL, 0, NULL, '2025-11-27 12:05:45'),
(1415, 330489980, 1, '56', NULL, 'Стіл', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1124557', 1, 'шт', NULL, 0, NULL, '2025-11-27 12:05:45'),
(1416, 330489980, 1, '56', NULL, 'Сейф 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138044', 1, 'шт', NULL, 0, NULL, '2025-11-27 12:05:45'),
(1417, 330489980, 1, '56', NULL, 'Комод', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-56-005', 1, 'шт', NULL, 0, NULL, '2025-11-27 12:05:45'),
(1418, 330489980, 1, '56', NULL, 'Лампа настільна', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1124513', 1, 'шт', NULL, 0, NULL, '2025-11-27 12:05:45'),
(1419, 330489980, 1, '56', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200230', 1, 'шт', NULL, 0, NULL, '2025-11-27 12:05:45'),
(1420, 330489980, 1, '56', NULL, 'Холодильник', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138046', 1, 'шт', NULL, 0, NULL, '2025-11-27 12:05:45'),
(1421, 330489980, 1, '56', NULL, 'Телефон', NULL, NULL, 0, NULL, 'Fanvil', NULL, NULL, '11137295', 1, 'шт', NULL, 0, NULL, '2025-11-27 12:05:45'),
(1422, 330489980, 1, '56', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-27 12:05:45'),
(1423, 330489980, 1, '56', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-27 12:05:45'),
(1424, 330489980, 1, '56', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-27 12:05:45'),
(1425, 330489980, 1, '56', NULL, 'Крісло заокруглене', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200315', 1, 'шт', NULL, 0, NULL, '2025-11-27 12:05:45'),
(1426, 330489980, 1, '56', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200411', 1, 'шт', NULL, 0, NULL, '2025-11-27 12:05:45'),
(1427, 330489980, 1, '56', NULL, 'Пенал відкритий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277', 1, 'шт', NULL, 0, NULL, '2025-11-27 12:05:45'),
(1428, 330489980, 1, '56', NULL, 'Шафа для одягу', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136772', 1, 'шт', NULL, 0, NULL, '2025-11-27 12:05:45'),
(1429, 330489980, 1, '56', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136752', 1, 'шт', NULL, 0, NULL, '2025-11-27 12:05:45'),
(1430, 330489980, 1, '56', NULL, 'Шафа для документів відкрита 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200051', 1, 'шт', NULL, 0, NULL, '2025-11-27 12:05:45'),
(1431, 330489980, 1, '56', NULL, 'Шафа для документів відкрита 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200051', 1, 'шт', NULL, 0, NULL, '2025-11-27 12:05:45'),
(1432, 330489980, 1, '56', NULL, 'Тумба 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-56-020', 1, 'шт', NULL, 0, NULL, '2025-11-27 12:05:45'),
(1433, 330489980, 1, '56', NULL, 'Подовжувач білий', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-56-021', 1, 'шт', NULL, 0, NULL, '2025-11-27 12:05:45'),
(1434, 330489980, 1, '59', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200157', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:10:34'),
(1435, 330489980, 1, '59', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '101487022', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:10:34'),
(1436, 330489980, 1, '59', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-59-003', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:10:34'),
(1437, 330489980, 1, '59', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11137705', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:10:34'),
(1438, 330489980, 1, '59', NULL, 'Принтер', NULL, NULL, 0, NULL, 'HP', NULL, NULL, '10400048', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:10:34'),
(1439, 330489980, 1, '59', NULL, 'Стіл 2ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136244', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:10:34'),
(1440, 330489980, 1, '59', NULL, 'Стіл', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-59-007', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:10:34'),
(1441, 330489980, 1, '59', NULL, 'Комод 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-59-008', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:10:34'),
(1442, 330489980, 1, '59', NULL, 'Тумба 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-59-009', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:10:34'),
(1443, 330489980, 1, '59', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, 'Nordis', NULL, NULL, '11200364-59', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:10:34'),
(1444, 330489980, 1, '59', NULL, 'Диван шкіряний без підлокітників', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200409', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:10:34'),
(1445, 330489980, 1, '59', NULL, 'Шафа для одягу 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136289', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:10:34'),
(1446, 330489980, 1, '59', NULL, 'Сейф подвійний', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136161', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:10:34'),
(1447, 330489980, 1, '59', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138016', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:10:34'),
(1448, 330489980, 1, '59', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:10:34'),
(1449, 330489980, 1, '59', NULL, 'Подовжувач чорний 1.8м', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-59-017', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:10:34'),
(1450, 330489980, 1, '57', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10400045', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:17:32'),
(1451, 330489980, 1, '57', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '10400045', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:17:32'),
(1452, 330489980, 1, '57', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', NULL, 'UV4Y039240', '101467106', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:17:32'),
(1453, 330489980, 1, '57', NULL, 'Стіл 2ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136569', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:17:32'),
(1454, 330489980, 1, '57', NULL, 'Стіл комп\'ютерний', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138101', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:17:32'),
(1455, 330489980, 1, '57', NULL, 'Холодильник', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138020', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:17:32'),
(1456, 330489980, 1, '57', NULL, 'Шафа для докуменетів відкритий 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136752', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:17:32'),
(1457, 330489980, 1, '57', NULL, 'Шафа для одягу 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136772', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:17:32'),
(1458, 330489980, 1, '57', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:17:32'),
(1459, 330489980, 1, '57', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:17:32'),
(1460, 330489980, 1, '57', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:17:32'),
(1461, 330489980, 1, '57', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200230', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:17:32'),
(1462, 330489980, 1, '57', NULL, 'Крісло шкіряне', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200439', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:17:32'),
(1463, 330489980, 1, '57', NULL, 'Крісло офісне шкіряне', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-57-016', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:17:32'),
(1464, 330489980, 1, 'Конференс-зал', NULL, 'Телевізор', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10400008', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1465, 330489980, 1, 'Конференс-зал', NULL, 'Холодильник', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138046', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1466, 330489980, 1, 'Конференс-зал', NULL, 'Стіл 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136193', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1467, 330489980, 1, 'Конференс-зал', NULL, 'Стіл 2ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1136183', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1468, 330489980, 1, 'Конференс-зал', NULL, 'Шафа металева 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-Конференс-зал-005', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1469, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11137484', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1470, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11137484', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1471, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11137484', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1472, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11137484', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1473, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'ИНВ', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1474, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'ИНВ', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1475, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'ИНВ', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1476, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'ИНВ', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1477, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'ИНВ', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1478, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1136209', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1479, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1480, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1481, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1482, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1483, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1484, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1485, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1486, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1487, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1488, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1489, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1490, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1491, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1492, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1493, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1494, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1495, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1496, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1497, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1498, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1499, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1500, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1136209', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1501, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1502, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1503, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1504, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1505, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1506, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1507, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1508, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1509, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1510, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1511, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1512, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1513, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1514, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1515, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1516, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1517, 330489980, 1, 'Конференс-зал', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1518, 330489980, 1, 'Конференс-зал', NULL, 'Кушетка', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1137131', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1519, 330489980, 1, 'Конференс-зал', NULL, 'Кушетка', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-Конференс-зал-057', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1520, 330489980, 1, 'Конференс-зал', NULL, 'Кушетка', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-Конференс-зал-058', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1521, 330489980, 1, 'Конференс-зал', NULL, 'Кушетка', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-Конференс-зал-059', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1522, 330489980, 1, 'Конференс-зал', NULL, 'Кушетка', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-Конференс-зал-060', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1523, 330489980, 1, 'Конференс-зал', NULL, 'Кушетка', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-Конференс-зал-061', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1524, 330489980, 1, 'Конференс-зал', NULL, 'Лавка зі спинкою без підлокітників', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-Конференс-зал-062', 1, 'шт', NULL, 0, NULL, '2025-11-28 06:53:35'),
(1525, 330489980, 1, 'РЕМОНТ', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138050', 1, 'шт', NULL, 0, NULL, '2025-11-28 07:06:11'),
(1526, 330489980, 1, 'РЕМОНТ', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-11-28 07:06:11'),
(1527, 330489980, 1, 'РЕМОНТ', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-РЕМОНТ-003', 1, 'шт', NULL, 0, NULL, '2025-11-28 07:06:11'),
(1528, 330489980, 1, 'РЕМОНТ', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '112000113', 1, 'шт', NULL, 0, NULL, '2025-11-28 07:06:11'),
(1529, 330489980, 1, 'РЕМОНТ', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '112000113', 1, 'шт', NULL, 0, NULL, '2025-11-28 07:06:11'),
(1530, 330489980, 1, 'РЕМОНТ', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '112000113', 1, 'шт', NULL, 0, NULL, '2025-11-28 07:06:11'),
(1531, 330489980, 1, 'РЕМОНТ', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-РЕМОНТ-007', 1, 'шт', NULL, 0, NULL, '2025-11-28 07:06:11'),
(1532, 330489980, 1, 'РЕМОНТ', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136079', 1, 'шт', NULL, 0, NULL, '2025-11-28 07:06:11'),
(1533, 330489980, 1, 'РЕМОНТ', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-РЕМОНТ-009', 1, 'шт', NULL, 0, NULL, '2025-11-28 07:06:11'),
(1534, 330489980, 1, 'РЕМОНТ', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-РЕМОНТ-010', 1, 'шт', NULL, 0, NULL, '2025-11-28 07:06:11'),
(1535, 330489980, 1, 'РЕМОНТ', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-РЕМОНТ-011', 1, 'шт', NULL, 0, NULL, '2025-11-28 07:06:11'),
(1536, 330489980, 1, 'РЕМОНТ', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-РЕМОНТ-012', 1, 'шт', NULL, 0, NULL, '2025-11-28 07:06:11'),
(1537, 330489980, 1, 'РЕМОНТ', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-РЕМОНТ-013', 1, 'шт', NULL, 0, NULL, '2025-11-28 07:06:11'),
(1538, 330489980, 1, 'РЕМОНТ', NULL, 'Шафа металева 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10400034', 1, 'шт', NULL, 0, NULL, '2025-11-28 07:06:11'),
(1539, 330489980, 1, 'РЕМОНТ', NULL, 'Шафа металева 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-РЕМОНТ-015', 1, 'шт', NULL, 0, NULL, '2025-11-28 07:06:11'),
(1540, 330489980, 1, 'РЕМОНТ', NULL, 'Шафа металева 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-РЕМОНТ-016', 1, 'шт', NULL, 0, NULL, '2025-11-28 07:06:11'),
(1541, 330489980, 1, 'РЕМОНТ', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '112000113', 1, 'шт', NULL, 0, NULL, '2025-11-28 07:06:11'),
(1542, 330489980, 1, 'РЕМОНТ', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-РЕМОНТ-019', 1, 'шт', NULL, 0, NULL, '2025-11-28 07:06:11'),
(1543, 330489980, 1, 'РЕМОНТ', NULL, 'Холодильник', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101497040', 1, 'шт', NULL, 0, NULL, '2025-11-28 07:06:11'),
(1544, 330489980, 1, 'РЕМОНТ', NULL, 'Шафа металева 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10400035', 1, 'шт', NULL, 0, NULL, '2025-11-28 07:06:11'),
(1545, 330489980, 1, 'РЕМОНТ', NULL, 'Шафа металева 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10400039', 1, 'шт', NULL, 0, NULL, '2025-11-28 07:06:11'),
(1546, 330489980, 6, 'Підвал', NULL, 'ДБЖ', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-001', 1, 'шт', NULL, 0, NULL, '2025-11-28 09:53:46'),
(1547, 330489980, 6, 'Підвал', NULL, 'Подовжувач білий', NULL, 'електрика', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-002', 0, 'шт', NULL, 0, NULL, '2025-11-28 09:53:46'),
(1548, 330489980, 6, 'Підвал', NULL, 'Монітор ?', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, '101487023', 1, 'шт', NULL, 0, NULL, '2025-11-28 09:53:46'),
(1549, 330489980, 6, 'Підвал', NULL, 'Монітор ?', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, '101487041', 1, 'шт', NULL, 0, NULL, '2025-11-28 09:53:46'),
(1550, 330489980, 6, 'Підвал', NULL, 'Монітор ?', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, '101487021', 1, 'шт', NULL, 0, NULL, '2025-11-28 09:53:46'),
(1551, 330489980, 6, 'Підвал', NULL, 'Монітор ?', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, '101467037', 1, 'шт', NULL, 0, NULL, '2025-11-28 09:53:46'),
(1552, 330489980, 6, 'Підвал', NULL, 'Монітор ?', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, '101467023', 1, 'шт', NULL, 0, NULL, '2025-11-28 09:53:46'),
(1553, 330489980, 6, 'Підвал', NULL, 'Монітор ?', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, '11137161', 1, 'шт', NULL, 0, NULL, '2025-11-28 09:53:46'),
(1554, 330489980, 6, 'Підвал', NULL, 'Принтер', NULL, 'орг техніка', 0, NULL, 'Epson', NULL, 'UV4Y039244', '101467101', 1, 'шт', NULL, 0, NULL, '2025-11-28 09:53:46'),
(1555, 330489980, 6, 'Підвал', NULL, 'Лампа настільна сіра', NULL, 'електрика', 0, NULL, NULL, NULL, NULL, '11138013', 1, 'шт', NULL, 0, NULL, '2025-11-28 09:53:46'),
(1556, 330489980, 6, 'Підвал', NULL, 'Телефон', NULL, 'орг техніка', 0, NULL, 'Fanvil', NULL, NULL, '11137295', 1, 'шт', NULL, 0, NULL, '2025-11-28 09:53:46'),
(1557, 330489980, 6, 'Підвал', NULL, 'Комп\'ютер', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, '11200173\\101467043', 1, 'шт', NULL, 0, NULL, '2025-11-28 09:53:46'),
(1558, 330489980, 6, 'Підвал', NULL, 'Комп\'ютер', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, '11200157', 1, 'шт', NULL, 0, NULL, '2025-11-28 09:53:46'),
(1559, 330489980, 6, 'Підвал', NULL, 'Комп\'ютер', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, '101467039', 1, 'шт', NULL, 0, NULL, '2025-11-28 09:53:46'),
(1560, 330489980, 6, 'Підвал', NULL, 'Комп\'ютер', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, '101467045', 1, 'шт', NULL, 0, NULL, '2025-11-28 09:53:46'),
(1561, 330489980, 6, 'Підвал', NULL, 'Комп\'ютер', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, '101467046', 1, 'шт', NULL, 0, NULL, '2025-11-28 09:53:46'),
(1562, 330489980, 6, 'Підвал', NULL, 'Комп\'ютер', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, '101467066', 1, 'шт', NULL, 0, NULL, '2025-11-28 09:53:46'),
(1563, 330489980, 6, 'Підвал', NULL, 'Комп\'ютер', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, '101467046', 1, 'шт', NULL, 0, NULL, '2025-11-28 09:53:46'),
(1564, 330489980, 6, 'Підвал', NULL, 'Комп\'ютер', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-020', 1, 'шт', NULL, 0, NULL, '2025-11-28 09:53:46'),
(1565, 330489980, 1, '45\\2', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', NULL, 'UV4Y038159', '101467093', 1, 'шт', NULL, 0, NULL, '2025-12-01 07:01:52'),
(1566, 330489980, 1, '45\\2', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467052', 1, 'шт', NULL, 0, NULL, '2025-12-01 07:01:52'),
(1567, 330489980, 1, '45\\2', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '10400058\\10400044', 1, 'шт', NULL, 0, NULL, '2025-12-01 07:01:52'),
(1568, 330489980, 1, '45\\2', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200115', 1, 'шт', NULL, 0, NULL, '2025-12-01 07:01:52'),
(1569, 330489980, 1, '45\\2', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200115', 1, 'шт', NULL, 0, NULL, '2025-12-01 07:01:52'),
(1570, 330489980, 1, '45\\2', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10400058', 1, 'шт', NULL, 0, NULL, '2025-12-01 07:01:52'),
(1571, 330489980, 1, '45\\2', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '111467041', 1, 'шт', NULL, 0, NULL, '2025-12-01 07:01:52'),
(1572, 330489980, 1, '45\\2', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467052', 1, 'шт', NULL, 0, NULL, '2025-12-01 07:01:52'),
(1573, 330489980, 1, '45\\2', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467072', 1, 'шт', NULL, 0, NULL, '2025-12-01 07:01:52'),
(1574, 330489980, 1, '45\\2', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-45\\2-010', 1, 'шт', NULL, 0, NULL, '2025-12-01 07:01:52'),
(1575, 330489980, 1, '45\\2', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-45\\2-011', 1, 'шт', NULL, 0, NULL, '2025-12-01 07:01:52'),
(1576, 330489980, 1, '45\\2', NULL, 'Роутер', NULL, NULL, 0, NULL, 'Netis', NULL, NULL, 'INV-1-45\\2-012', 1, 'шт', NULL, 0, NULL, '2025-12-01 07:01:52'),
(1577, 330489980, 1, '45\\2', NULL, 'Стіл 2ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136186', 1, 'шт', NULL, 0, NULL, '2025-12-01 07:01:52'),
(1578, 330489980, 1, '45\\2', NULL, 'Стіл 2ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136193', 1, 'шт', NULL, 0, NULL, '2025-12-01 07:01:52'),
(1579, 330489980, 1, '45\\2', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, 'Haler', NULL, NULL, '11200364-45\\2', 1, 'шт', NULL, 0, NULL, '2025-12-01 07:01:52'),
(1580, 330489980, 1, '45\\2', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11137484', 1, 'шт', NULL, 0, NULL, '2025-12-01 07:01:52'),
(1581, 330489980, 1, '45\\2', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11137484', 1, 'шт', NULL, 0, NULL, '2025-12-01 07:01:52'),
(1582, 330489980, 1, '45\\2', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-12-01 07:01:52'),
(1583, 330489980, 1, '45\\2', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-12-01 07:01:52'),
(1584, 330489980, 1, '45\\2', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277', 1, 'шт', NULL, 0, NULL, '2025-12-01 07:01:52'),
(1585, 330489980, 1, '45\\2', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277', 1, 'шт', NULL, 0, NULL, '2025-12-01 07:01:52'),
(1586, 330489980, 1, '45\\2', NULL, 'Шафа для одягу 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136289', 1, 'шт', NULL, 0, NULL, '2025-12-01 07:01:52'),
(1587, 330489980, 1, '45\\2', NULL, 'Шафа для одягу 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136772', 1, 'шт', NULL, 0, NULL, '2025-12-01 07:01:52'),
(1588, 330489980, 1, '45\\2', NULL, 'Подовжувач чорний', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-45\\2-024', 1, 'шт', NULL, 0, NULL, '2025-12-01 07:01:52'),
(1589, 330489980, 1, '45\\2', NULL, 'Тумба 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1124589', 1, 'шт', NULL, 0, NULL, '2025-12-01 07:01:52'),
(1590, 330489980, 1, '52', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', NULL, '101467094', 1, 'шт', NULL, 0, NULL, '2025-12-01 09:13:50'),
(1591, 330489980, 1, '52', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', NULL, '101467075', 1, 'шт', NULL, 0, NULL, '2025-12-01 09:13:50'),
(1592, 330489980, 1, '46', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', NULL, '101467107', 1, 'шт', NULL, 0, NULL, '2025-12-01 09:18:38'),
(1593, 330489980, 1, '46', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10400046', 1, 'шт', NULL, 0, NULL, '2025-12-03 10:30:12'),
(1594, 330489980, 1, '46', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '10400043', 1, 'шт', NULL, 0, NULL, '2025-12-03 10:30:12'),
(1595, 330489980, 1, '46', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200115', 1, 'шт', NULL, 0, NULL, '2025-12-03 10:30:12'),
(1596, 330489980, 1, '46', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV', 1, 'шт', NULL, 0, NULL, '2025-12-03 10:30:12'),
(1597, 330489980, 1, '46', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10149746', 1, 'шт', NULL, 0, NULL, '2025-12-03 10:30:12'),
(1598, 330489980, 1, '46', NULL, 'ДБЖ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200112', 1, 'шт', NULL, 0, NULL, '2025-12-03 10:30:12'),
(1599, 330489980, 1, '46', NULL, 'Стіл комп\'ютерний', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1124557', 1, 'шт', NULL, 0, NULL, '2025-12-03 10:30:12'),
(1600, 330489980, 1, '46', NULL, 'Стіл комп\'ютерний 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136194', 1, 'шт', NULL, 0, NULL, '2025-12-03 10:30:12'),
(1601, 330489980, 1, '46', NULL, 'Приставка до столу', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136198', 1, 'шт', NULL, 0, NULL, '2025-12-03 10:30:12'),
(1602, 330489980, 1, '46', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '112000113', 1, 'шт', NULL, 0, NULL, '2025-12-03 10:30:12'),
(1603, 330489980, 1, '46', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-46-013', 1, 'шт', NULL, 0, NULL, '2025-12-03 10:30:12'),
(1604, 330489980, 1, '46', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-12-03 10:30:12'),
(1605, 330489980, 1, '46', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-12-03 10:30:12'),
(1606, 330489980, 1, '46', NULL, 'Підставка комп\'ютерна', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-46-016', 1, 'шт', NULL, 0, NULL, '2025-12-03 10:30:12'),
(1607, 330489980, 1, '46', NULL, 'Роутер', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-46-017', 1, 'шт', NULL, 0, NULL, '2025-12-03 10:30:12'),
(1608, 330489980, 1, '46', NULL, 'Тумба 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11178039', 1, 'шт', NULL, 0, NULL, '2025-12-03 10:30:12'),
(1609, 330489980, 1, '46', NULL, 'Сейф 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10400003', 1, 'шт', NULL, 0, NULL, '2025-12-03 10:30:12'),
(1610, 330489980, 1, '46', NULL, 'Сейф 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10400005', 1, 'шт', NULL, 0, NULL, '2025-12-03 10:30:12'),
(1611, 330489980, 1, '46', NULL, 'Шафа для одягу 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138033', 1, 'шт', NULL, 0, NULL, '2025-12-03 10:30:12'),
(1612, 330489980, 1, '46', NULL, 'Пенал відкритий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138036', 1, 'шт', NULL, 0, NULL, '2025-12-03 10:30:12'),
(1613, 330489980, 1, '46', NULL, 'Телефон', NULL, NULL, 0, NULL, 'Fanvil', NULL, NULL, 'INV-1-46-023', 1, 'шт', NULL, 0, NULL, '2025-12-03 10:30:12'),
(1614, 330489980, 1, '46', NULL, 'Подовжувач чорний', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-46-024', 1, 'шт', NULL, 0, NULL, '2025-12-03 10:30:12'),
(1615, 330489980, 1, '46', NULL, 'Лампа настільна', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138013', 1, 'шт', NULL, 0, NULL, '2025-12-03 10:30:12'),
(1616, 330489980, 1, '46', NULL, 'Шафа для документів відкрита 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138034', 1, 'шт', NULL, 0, NULL, '2025-12-03 10:30:12'),
(1617, 330489980, 1, '46', NULL, 'Тумба 1да', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-46-027', 1, 'шт', NULL, 0, NULL, '2025-12-03 10:30:12'),
(1618, 330489980, 1, '46', NULL, 'Диван шкіряний без підколітників', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-46-028', 1, 'шт', NULL, 0, NULL, '2025-12-03 10:30:12'),
(1619, 330489980, 1, '46', NULL, 'Ноутбук', NULL, NULL, 0, NULL, 'Asus', NULL, NULL, '10400277', 1, 'шт', NULL, 0, NULL, '2025-12-03 11:40:37'),
(1620, 330489980, 1, '46', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200300', 1, 'шт', NULL, 0, NULL, '2025-12-03 11:40:37'),
(1621, 330489980, 1, '50', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10149751', 1, 'шт', NULL, 0, NULL, '2025-12-05 08:37:12'),
(1622, 330489980, 1, '45\\5', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467061', 1, 'шт', NULL, 0, NULL, '2025-12-05 09:30:54'),
(1623, 330489980, 1, '45\\5', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '101467061', 1, 'шт', NULL, 0, NULL, '2025-12-05 09:30:54'),
(1624, 330489980, 1, '45\\5', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467061', 1, 'шт', NULL, 0, NULL, '2025-12-05 09:30:54'),
(1625, 330489980, 1, '45\\5', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467061', 1, 'шт', NULL, 0, NULL, '2025-12-05 09:30:54'),
(1626, 330489980, 1, '45\\5', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467062', 1, 'шт', NULL, 0, NULL, '2025-12-05 09:30:54'),
(1627, 330489980, 1, '45\\5', NULL, 'Монітор', NULL, NULL, 0, NULL, 'Acer', NULL, NULL, '101467062', 1, 'шт', NULL, 0, NULL, '2025-12-05 09:30:54'),
(1628, 330489980, 1, '45\\5', NULL, 'Клавіатура', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467062', 1, 'шт', NULL, 0, NULL, '2025-12-05 09:30:54'),
(1629, 330489980, 1, '45\\5', NULL, 'Миша', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101467062', 1, 'шт', NULL, 0, NULL, '2025-12-05 09:30:54'),
(1630, 330489980, 1, '45\\5', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '10400079', 1, 'шт', NULL, 0, NULL, '2025-12-05 09:30:54'),
(1631, 330489980, 1, '45\\5', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', 'UV4Y039113', '101467089', 1, 'шт', NULL, 0, NULL, '2025-12-05 09:30:54'),
(1632, 330489980, 1, '45\\5', NULL, 'Подовжувач чорний 1.8м', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-45\\5-011', 1, 'шт', NULL, 0, NULL, '2025-12-05 09:30:54'),
(1633, 330489980, 1, '45\\5', NULL, 'Тумба 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136244', 1, 'шт', NULL, 0, NULL, '2025-12-05 09:30:54'),
(1634, 330489980, 1, '45\\5', NULL, 'Стіл комп\'ютерний 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136194', 1, 'шт', NULL, 0, NULL, '2025-12-05 09:30:54'),
(1635, 330489980, 1, '45\\5', NULL, 'Стіл комп\'ютерний 3ящ', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-45\\5-015', 1, 'шт', NULL, 0, NULL, '2025-12-05 09:30:54'),
(1636, 330489980, 1, '45\\5', NULL, 'Крісло', NULL, NULL, 0, NULL, NULL, NULL, NULL, '112000113', 1, 'шт', NULL, 0, NULL, '2025-12-05 09:30:54'),
(1637, 330489980, 1, '45\\5', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-12-05 09:30:54'),
(1638, 330489980, 1, '45\\5', NULL, 'Стул', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136211', 1, 'шт', NULL, 0, NULL, '2025-12-05 09:30:54'),
(1639, 330489980, 1, '45\\5', NULL, 'Пенал відкритий', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11138036', 1, 'шт', NULL, 0, NULL, '2025-12-05 09:30:54'),
(1640, 330489980, 1, '45\\5', NULL, 'Колонки', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200346', 1, 'шт', NULL, 0, NULL, '2025-12-05 09:30:54'),
(1641, 330489980, 1, '45\\5', NULL, 'Камера', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200345', 1, 'шт', NULL, 0, NULL, '2025-12-05 09:30:54'),
(1642, 330489980, 1, '45\\5', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11136277', 1, 'шт', NULL, 0, NULL, '2025-12-05 09:30:54'),
(1643, 330489980, 1, '45\\5', NULL, 'Шафа для документів 2дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-45\\5-023', 1, 'шт', NULL, 0, NULL, '2025-12-05 09:30:54'),
(1644, 330489980, 1, '45\\5', NULL, 'Шафа для одягу 1дв', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1136289', 1, 'шт', NULL, 0, NULL, '2025-12-05 09:30:54'),
(1645, 330489980, 1, '45\\5', NULL, 'Подовжувач білий 1.8м', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'INV-1-45\\5-025', 1, 'шт', NULL, 0, NULL, '2025-12-05 09:30:54'),
(1646, 330489980, 6, 'Загальний', NULL, 'HDMI Кабель 1.5м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-0011', 0, 'шт', NULL, 10, NULL, '2025-12-08 06:37:35'),
(1647, 330489980, 6, 'Підвал', NULL, 'HDMI Кабель 1.5м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-002', 0, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1648, 330489980, 6, 'Підвал', NULL, 'HDMI Кабель 1.5м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-003', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1649, 330489980, 6, 'Підвал', NULL, 'HDMI Кабель 1.5м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-004', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1650, 330489980, 6, 'Підвал', NULL, 'HDMI Кабель 1.5м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-005', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1651, 330489980, 6, 'Підвал', NULL, 'HDMI Кабель 1.5м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-006', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35');
INSERT INTO `room_inventory` (`id`, `admin_telegram_id`, `branch_id`, `room_number`, `template_id`, `equipment_type`, `full_name`, `category`, `is_priority`, `balance_code`, `brand`, `model`, `serial_number`, `inventory_number`, `quantity`, `unit`, `price`, `min_quantity`, `notes`, `created_at`) VALUES
(1652, 330489980, 6, 'Підвал', NULL, 'HDMI Кабель 1.5м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-007', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1653, 330489980, 6, 'Підвал', NULL, 'HDMI Кабель 1.5м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-008', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1654, 330489980, 6, 'Підвал', NULL, 'HDMI Кабель 1.5м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-009', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1655, 330489980, 6, 'орг техніка', NULL, 'Модуль памяті ОЗУ 4GB', NULL, 'орг техніка', 0, NULL, 'Exceleram', NULL, NULL, 'INV-6-Підвал-010', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1656, 330489980, 6, 'орг техніка', NULL, 'Модуль памяті ОЗУ 4GB', NULL, 'орг техніка', 0, NULL, 'Exceleram', NULL, NULL, 'INV-6-Підвал-011', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1657, 330489980, 6, 'Підвал', NULL, 'Миша', NULL, 'орг техніка', 0, NULL, 'Logitech', 'B100', NULL, 'INV-6-Підвал-012', 0, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1658, 330489980, 1, '13', NULL, 'Миша', NULL, 'орг техніка', 0, NULL, 'Logitech', 'B100', NULL, 'INV-6-Підвал-013', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1659, 330489980, 6, 'Підвал', NULL, 'Батарейки комп\'ютерні х5', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-014', 0, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1660, 330489980, 6, 'Підвал', NULL, 'Батарейки комп\'ютерні х5', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-015', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1661, 330489980, 6, 'Підвал', NULL, 'Батарейки комп\'ютерні х5', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-016', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1662, 330489980, 6, 'Підвал', NULL, 'Батарейки комп\'ютерні х5', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-017', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1663, 330489980, 6, 'Підвал', NULL, 'Батарейки комп\'ютерні х5', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-018', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1664, 330489980, 6, 'Підвал', NULL, 'Батарейки комп\'ютерні х5', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-019', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1665, 330489980, 6, 'Підвал', NULL, 'Батарейки комп\'ютерні х5', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-020', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1666, 330489980, 6, 'Підвал', NULL, 'Батарейки комп\'ютерні х5', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-021', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1667, 330489980, 6, 'Підвал', NULL, 'Батарейки комп\'ютерні х5', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-022', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1668, 330489980, 6, 'Підвал', NULL, 'Батарейки комп\'ютерні х5', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-023', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1669, 330489980, 6, 'Підвал', NULL, 'Батарейки комп\'ютерні х5', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-024', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1670, 330489980, 6, 'Підвал', NULL, 'Батарейки комп\'ютерні х5', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-025', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1671, 330489980, 6, 'Підвал', NULL, 'Батарейки комп\'ютерні х5', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-026', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1672, 330489980, 6, 'Підвал', NULL, 'Батарейки комп\'ютерні х5', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-027', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1673, 330489980, 6, 'Підвал', NULL, 'Батарейки комп\'ютерні х5', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-028', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1674, 330489980, 6, 'Підвал', NULL, 'Батарейки комп\'ютерні х5', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-029', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1675, 330489980, 6, 'Підвал', NULL, 'Батарейки комп\'ютерні х5', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-030', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1676, 330489980, 6, 'Підвал', NULL, 'Батарейки комп\'ютерні х5', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-031', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1677, 330489980, 6, 'Підвал', NULL, 'Батарейки комп\'ютерні х5', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-032', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1678, 330489980, 6, 'Підвал', NULL, 'Батарейки комп\'ютерні х5', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-033', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1679, 330489980, 6, 'Підвал', NULL, 'Батарейки комп\'ютерні х5', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-034', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1680, 330489980, 6, 'Підвал', NULL, 'Батарейки комп\'ютерні х5', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-035', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1681, 330489980, 6, 'Підвал', NULL, 'Батарейки комп\'ютерні х5', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-036', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1682, 330489980, 6, 'Підвал', NULL, 'Батарейки комп\'ютерні х5', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-037', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1683, 330489980, 6, 'Підвал', NULL, 'Батарейки комп\'ютерні х5', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-038', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1684, 330489980, 6, 'Підвал', NULL, 'Батарейки комп\'ютерні х5', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-039', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1685, 330489980, 1, '13', NULL, 'Клавіатура', NULL, 'орг техніка', 0, NULL, 'Logitech', 'K120', NULL, 'INV-6-Підвал-040', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1686, 330489980, 6, 'Підвал', NULL, 'Клавіатура', NULL, 'орг техніка', 0, NULL, 'Logitech', 'K120', NULL, 'INV-6-Підвал-041', 0, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1687, 330489980, 1, '23', NULL, 'Миша', NULL, 'орг техніка', 0, NULL, 'Logitech', 'B120', NULL, 'INV-6-Підвал-042', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1688, 330489980, 6, 'Підвал', NULL, 'Кабель для принтера', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-043', 0, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1689, 330489980, 6, 'Підвал', NULL, 'Кабель для принтера', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-044', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1690, 330489980, 6, 'Підвал', NULL, 'Кабель для принтера', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-045', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1691, 330489980, 6, 'Підвал', NULL, 'Блок живлення', NULL, 'орг техніка', 0, NULL, 'Vinga 500w', NULL, NULL, 'INV-6-Підвал-046', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1692, 330489980, 6, 'Підвал', NULL, 'Блок живлення', NULL, 'орг техніка', 0, NULL, 'Vinga 500w', NULL, NULL, 'INV-6-Підвал-047', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1693, 330489980, 6, 'Підвал', NULL, 'Блок живлення', NULL, 'орг техніка', 0, NULL, 'Vinga 500w', NULL, NULL, 'INV-6-Підвал-048', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1694, 330489980, 6, 'Підвал', NULL, 'Блок живлення', NULL, 'орг техніка', 0, NULL, 'Vinga 500w', NULL, NULL, 'INV-6-Підвал-049', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1695, 330489980, 6, 'Підвал', NULL, 'Комутатор', NULL, 'орг техніка', 0, NULL, 'Tp-link', NULL, NULL, 'INV-6-Підвал-050', 0, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1696, 330489980, 6, 'Підвал', NULL, 'VGA Кабель 1.8м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-051', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1697, 330489980, 6, 'Підвал', NULL, 'VGA Кабель 1.8м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-052', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1698, 330489980, 6, 'Підвал', NULL, 'VGA Кабель 1.8м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-053', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1699, 330489980, 6, 'Підвал', NULL, 'VGA Кабель 1.8м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-054', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1700, 330489980, 6, 'Підвал', NULL, 'VGA Кабель 1.8м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-055', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1701, 330489980, 6, 'Підвал', NULL, 'VGA Кабель 1.8м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-056', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1702, 330489980, 6, 'Підвал', NULL, 'VGA Кабель 1.8м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-057', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1703, 330489980, 6, 'Підвал', NULL, 'VGA Кабель 1.8м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-058', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1704, 330489980, 6, 'Підвал', NULL, 'VGA Кабель 1.8м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-059', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1705, 330489980, 6, 'Підвал', NULL, 'VGA Кабель 1.8м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-060', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1706, 330489980, 6, 'Підвал', NULL, 'SATA кабель 0.45м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-061', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1707, 330489980, 6, 'Підвал', NULL, 'SATA кабель 0.45м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-062', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1708, 330489980, 6, 'Підвал', NULL, 'SATA кабель 0.45м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-063', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1709, 330489980, 6, 'Підвал', NULL, 'SATA кабель 0.45м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-064', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1710, 330489980, 6, 'Підвал', NULL, 'SATA кабель 0.45м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-065', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1711, 330489980, 6, 'Підвал', NULL, 'SATA кабель 0.45м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-066', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1712, 330489980, 6, 'Підвал', NULL, 'SATA кабель 0.45м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-067', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1713, 330489980, 6, 'Підвал', NULL, 'SATA кабель 0.45м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-068', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1714, 330489980, 6, 'Підвал', NULL, 'SATA кабель 0.45м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-069', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1715, 330489980, 6, 'Підвал', NULL, 'SATA кабель 0.45м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-070', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1716, 330489980, 6, 'Підвал', NULL, 'Патч-корд кабель 5м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-071', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1717, 330489980, 6, 'Підвал', NULL, 'Патч-корд кабель 5м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-072', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1718, 330489980, 6, 'Підвал', NULL, 'Патч-корд кабель 5м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-073', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1719, 330489980, 6, 'Підвал', NULL, 'Патч-корд кабель 5м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-074', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1720, 330489980, 6, 'Підвал', NULL, 'Патч-корд кабель 5м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-075', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1721, 330489980, 6, 'Підвал', NULL, 'Патч-корд кабель 5м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-076', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1722, 330489980, 6, 'Підвал', NULL, 'Патч-корд кабель 5м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-077', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1723, 330489980, 6, 'Підвал', NULL, 'Патч-корд кабель 5м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-078', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1724, 330489980, 6, 'Підвал', NULL, 'Патч-корд кабель 5м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-079', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1725, 330489980, 6, 'Підвал', NULL, 'Патч-корд кабель 3м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-080', 0, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1726, 330489980, 6, 'Підвал', NULL, 'Патч-корд кабель 3м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-081', 0, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1727, 330489980, 6, 'Підвал', NULL, 'Патч-корд кабель 3м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-082', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1728, 330489980, 6, 'Підвал', NULL, 'Патч-корд кабель 3м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-083', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1729, 330489980, 6, 'Підвал', NULL, 'Патч-корд кабель 3м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-084', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1730, 330489980, 6, 'Підвал', NULL, 'Патч-корд кабель 3м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-085', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1731, 330489980, 6, 'Підвал', NULL, 'Патч-корд кабель 3м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-086', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1732, 330489980, 6, 'Підвал', NULL, 'Патч-корд кабель 3м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-087', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1733, 330489980, 6, 'Підвал', NULL, 'Патч-корд кабель 3м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-088', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1734, 330489980, 6, 'Підвал', NULL, 'Патч-корд кабель 3м', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'INV-6-Підвал-089', 1, 'шт', NULL, 0, NULL, '2025-12-08 06:37:35'),
(1735, 330489980, 1, '35', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101487025', 1, 'шт', NULL, 0, NULL, '2025-12-08 08:13:37'),
(1736, 330489980, 1, '50', NULL, 'Ноутбук', NULL, NULL, 0, NULL, 'Lenovo', NULL, NULL, '1046057', 1, 'шт', NULL, 0, NULL, '2025-12-08 08:27:10'),
(1737, 330489980, 1, '52', NULL, 'Ноутбук', NULL, NULL, 0, NULL, 'HP', 'H-255 G8', NULL, '11200209', 1, 'шт', NULL, 0, NULL, '2025-12-08 08:31:39'),
(1738, 330489980, 4, '104', NULL, 'Ноутбук', NULL, NULL, 0, NULL, 'Lenovo', NULL, NULL, '1046053', 1, 'шт', NULL, 0, NULL, '2025-12-08 11:47:10'),
(1739, 330489980, 1, '33\\4', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', NULL, '11200174', 1, 'шт', NULL, 0, NULL, '2025-12-11 10:13:43'),
(1740, 330489980, 6, 'Підвал', NULL, 'Принтер', NULL, 'орг техніка', 0, NULL, 'HP', NULL, NULL, '11200299', 1, 'шт', NULL, 0, NULL, '2025-12-11 11:31:26'),
(1741, 330489980, 5, '123', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, 'OSAKA', NULL, NULL, '11200246', 1, 'шт', NULL, 0, NULL, '2025-12-11 11:52:55'),
(1742, 330489980, 5, '317a', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, 'Samurai', NULL, NULL, '11200247', 1, 'шт', NULL, 0, NULL, '2025-12-11 11:54:00'),
(1743, 330489980, 5, '310', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, 'Saturn', NULL, NULL, '11200245', 1, 'шт', NULL, 0, NULL, '2025-12-11 11:54:28'),
(1744, 330489980, 5, '312', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, 'Saturn', NULL, NULL, '11200245', 1, 'шт', NULL, 0, NULL, '2025-12-11 11:54:52'),
(1745, 330489980, 5, '335', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, 'Saturn', NULL, NULL, '11200245', 1, 'шт', NULL, 0, NULL, '2025-12-11 11:55:15'),
(1746, 330489980, 5, '330', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, 'Saturn', NULL, NULL, '11200245', 1, 'шт', NULL, 0, NULL, '2025-12-11 11:56:01'),
(1747, 330489980, 5, '301', NULL, 'Кондиціонер', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200365', 1, 'шт', NULL, 0, NULL, '2025-12-11 11:57:44'),
(1748, 330489980, 5, 'Склад', NULL, 'Телефон', NULL, NULL, 0, NULL, 'Fanvil', NULL, NULL, '11200255', 1, 'шт', NULL, 0, NULL, '2025-12-12 06:48:24'),
(1749, 330489980, 5, 'Склад', NULL, 'Телефон', NULL, NULL, 0, NULL, 'Fanvil', NULL, NULL, '11200255', 1, 'шт', NULL, 0, NULL, '2025-12-12 06:48:24'),
(1750, 330489980, 5, 'Реєстратура', NULL, 'Телефон', NULL, NULL, 0, NULL, 'Fanvil', NULL, NULL, '11200255', 1, 'шт', NULL, 0, NULL, '2025-12-12 06:48:58'),
(1751, 330489980, 5, '228\\233', NULL, 'Телефон', NULL, NULL, 0, NULL, 'Fanvil', NULL, NULL, '11200255', 1, 'шт', NULL, 0, NULL, '2025-12-12 06:49:32'),
(1752, 330489980, 5, '231', NULL, 'Телефон', NULL, NULL, 0, NULL, 'Fanvil', NULL, NULL, '11200255', 1, 'шт', NULL, 0, NULL, '2025-12-12 06:50:01'),
(1753, 330489980, 2, 'Реєстратура', NULL, 'Телефон', NULL, NULL, 0, NULL, 'Fanvil', NULL, NULL, '11137295', 1, 'шт', NULL, 0, NULL, '2025-12-12 06:53:10'),
(1754, 330489980, 5, 'Коридори + Вулиця', NULL, 'Камера відео-спостереження', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200210', 1, 'шт', NULL, 0, NULL, '2025-12-12 07:20:00'),
(1755, 330489980, 5, 'Коридори + Вулиця', NULL, 'Камера відео-спостереження', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200210', 1, 'шт', NULL, 0, NULL, '2025-12-12 07:20:00'),
(1756, 330489980, 5, 'Коридори + Вулиця', NULL, 'Камера відео-спостереження', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200210', 1, 'шт', NULL, 0, NULL, '2025-12-12 07:20:00'),
(1757, 330489980, 5, 'Коридори + Вулиця', NULL, 'Камера відео-спостереження', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200210', 1, 'шт', NULL, 0, NULL, '2025-12-12 07:20:00'),
(1758, 330489980, 5, 'Коридори + Вулиця', NULL, 'Камера відео-спостереження', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200210', 1, 'шт', NULL, 0, NULL, '2025-12-12 07:20:00'),
(1759, 330489980, 5, 'Коридори + Вулиця', NULL, 'Камера відео-спостереження', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200210', 1, 'шт', NULL, 0, NULL, '2025-12-12 07:20:00'),
(1760, 330489980, 5, 'Коридори + Вулиця', NULL, 'Камера відео-спостереження', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200210', 1, 'шт', NULL, 0, NULL, '2025-12-12 07:20:00'),
(1761, 330489980, 5, 'Коридори + Вулиця', NULL, 'Камера відео-спостереження', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200210', 1, 'шт', NULL, 0, NULL, '2025-12-12 07:20:00'),
(1762, 330489980, 5, 'Коридори + Вулиця', NULL, 'Камера відео-спостереження', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200211', 1, 'шт', NULL, 0, NULL, '2025-12-12 07:20:00'),
(1763, 330489980, 5, 'Коридори + Вулиця', NULL, 'Камера відео-спостереження', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200211', 1, 'шт', NULL, 0, NULL, '2025-12-12 07:20:00'),
(1764, 330489980, 5, 'Коридори + Вулиця', NULL, 'Камера відео-спостереження', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200211', 1, 'шт', NULL, 0, NULL, '2025-12-12 07:20:00'),
(1765, 330489980, 5, 'Коридори + Вулиця', NULL, 'Камера відео-спостереження', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200214', 1, 'шт', NULL, 0, NULL, '2025-12-12 07:20:00'),
(1766, 330489980, 5, 'Коридори + Вулиця', NULL, 'Камера відео-спостереження', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200214', 1, 'шт', NULL, 0, NULL, '2025-12-12 07:20:00'),
(1767, 330489980, 3, 'Коридор', NULL, 'Камера відео-спостереження', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200271', 1, 'шт', NULL, 0, NULL, '2025-12-12 07:20:19'),
(1768, 330489980, 4, 'Коридор', NULL, 'Камера відео-спостереження', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200271', 1, 'шт', NULL, 0, NULL, '2025-12-12 07:21:02'),
(1769, 330489980, 4, 'Коридор', NULL, 'Камера відео-спостереження', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200271', 1, 'шт', NULL, 0, NULL, '2025-12-12 07:21:02'),
(1770, 330489980, 4, 'Коридор', NULL, 'Камера відео-спостереження', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200271', 1, 'шт', NULL, 0, NULL, '2025-12-12 07:21:02'),
(1771, 330489980, 4, 'Коридор', NULL, 'Камера відео-спостереження', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200270', 1, 'шт', NULL, 0, NULL, '2025-12-12 07:21:02'),
(1772, 330489980, 4, 'Коридор', NULL, 'Камера відео-спостереження', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200270', 1, 'шт', NULL, 0, NULL, '2025-12-12 07:21:02'),
(1773, 330489980, 4, 'Коридор', NULL, 'Камера відео-спостереження', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200270', 1, 'шт', NULL, 0, NULL, '2025-12-12 07:21:02'),
(1774, 330489980, 5, 'Серверна', NULL, 'Елктронний оповіщувач', NULL, NULL, 0, NULL, 'Vellez', NULL, NULL, '11200224', 1, 'шт', NULL, 0, NULL, '2025-12-12 07:23:24'),
(1775, 330489980, 5, 'Серверна', NULL, 'Відео-регістратор', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200268', 1, 'шт', NULL, 0, NULL, '2025-12-12 07:24:16'),
(1776, 330489980, 3, 'Серверна', NULL, 'Пожежна сигналізація', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200263', 1, 'шт', NULL, 0, NULL, '2025-12-12 07:26:31'),
(1777, 330489980, 3, 'Серверна', NULL, 'Газо-аналізатор', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200218', 1, 'шт', NULL, 0, NULL, '2025-12-12 07:29:55'),
(1778, 330489980, 3, 'Підвал', NULL, 'Насос (переоцінка 2018)', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200169', 1, 'шт', NULL, 0, NULL, '2025-12-12 07:37:19'),
(1779, 330489980, 5, 'Підвал', NULL, 'Насосний вузол', NULL, NULL, 0, NULL, NULL, NULL, NULL, '11200204', 1, 'шт', NULL, 0, NULL, '2025-12-12 07:38:55'),
(1780, 330489980, 5, 'Підвал', NULL, 'Тепловий лічильник', NULL, NULL, 0, NULL, NULL, NULL, NULL, '1046049', 1, 'шт', NULL, 0, NULL, '2025-12-12 07:40:13'),
(1781, 330489980, 4, 'Реєстратура', NULL, 'Елекронно-інформаційне табло', NULL, NULL, 0, NULL, 'LG', NULL, NULL, '1048012', 1, 'шт', NULL, 0, NULL, '2025-12-12 07:42:16'),
(1783, 330489980, 1, 'Реєстратура', NULL, 'Ноутбук', NULL, NULL, 0, NULL, NULL, NULL, NULL, '101487019', 1, 'шт', NULL, 0, NULL, '2025-12-17 08:33:55'),
(1784, 0, 6, 'миючі засоби', NULL, 'Білизна Кераміка', 'Проф. засіб для миття та очищення ванних кімнат \"Білизна Кераміка\" 5000мл', 'миючі засоби', 0, NULL, NULL, NULL, NULL, '30553', 13, 'каністра', NULL, 1, NULL, '2025-12-26 12:02:53'),
(1785, 0, 6, 'миючі засоби', NULL, 'Білизна Кераміка', 'Проф. засіб для миття та очищення ванних кімнат \"Білизна Кераміка\" 5000мл', 'миючі засоби', 0, NULL, NULL, NULL, NULL, '30533', 0, 'каністра', NULL, 1, NULL, '2025-12-29 09:30:41'),
(1786, 0, 6, 'миючі засоби', NULL, 'Білизна Сантехника', 'Професійний засіб для чищення санітарно-технічного обладнання з антибактеріальним ефектом \"Білизна сантехніка\" 5000мл', 'миючі засоби', 0, NULL, NULL, NULL, NULL, '30534', 5, 'каністра', NULL, 1, NULL, '2025-12-29 09:38:35'),
(1787, 0, 6, 'миючі засоби', NULL, 'Мило рідке Бланідас Софт 5000мл.', 'Мило рідке для шкіри рук і тіла. \"Бланідас Софт\" 5000мл.', 'миючі засоби', 0, NULL, NULL, NULL, NULL, '30535', 8, 'каністра', NULL, 1, NULL, '2025-12-29 09:57:47'),
(1788, 0, 6, 'орг техніка', NULL, 'Картридж відпрпацьованих чорнил (зливний)', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, '69912', 6, 'шт', NULL, 3, NULL, '2025-12-30 06:52:44'),
(1790, 0, 6, 'миючі засоби', NULL, 'Базік прання', 'Засіб для прання виробів з бавовни, льону та синтетичних матеріалів \"Білизна базік прання\" 5000мл', 'миючі засоби', 0, NULL, NULL, NULL, NULL, '305537', 6, 'каністра', NULL, 1, NULL, '2025-12-31 05:58:57'),
(1791, 0, 6, 'миючі засоби', NULL, 'Білизна проф-еліт Універсальний 5000мл', 'Засіб для прання білизни \"Білизна проф-еліт Універсальний\" 5000мл', 'миючі засоби', 0, NULL, NULL, NULL, NULL, '30538', 8, 'каністра', NULL, 1, NULL, '2025-12-31 06:39:48'),
(1793, 330489980, 6, 'миючі засоби', NULL, 'Білизна медкомфорт', 'Засіб для очищення поверхонь та нейтралізації неприємних засобів \"Білизна медкомфорт\" із квітковим ароматом 750мл', 'миючі засоби', 0, NULL, NULL, NULL, NULL, '221145', 37, 'пляшки', NULL, 4, NULL, '2026-01-07 10:22:45'),
(1794, 330489980, 6, 'миючі засоби', NULL, 'Білизна саніхлор', 'Професіний засіб для дезинфекції та очищення поверхонь \"Білизна саніхлор\" 5000мл', 'миючі засоби', 0, NULL, NULL, NULL, NULL, '333213', 3, 'каністри', NULL, 1, NULL, '2026-01-07 10:25:05'),
(1795, 330489980, 6, 'миючі засоби', NULL, 'Білизна грейпфрут', 'Професійний концентрований засіб для миття всіх видів поверхонь \"Білизна поверхня з ароматом Грейпфрут\" 5000мл', 'миючі засоби', 0, NULL, NULL, NULL, NULL, '45441', 11, 'каністра', NULL, 1, NULL, '2026-01-07 10:27:17'),
(1796, 330489980, 6, 'миючі засоби', NULL, 'Білизна анти-жир', NULL, 'миючі засоби', 0, NULL, NULL, NULL, NULL, '65445', 0, 'каністра', NULL, 1, NULL, '2026-01-07 10:28:55'),
(1797, 330489980, 6, 'миючі засоби', NULL, 'Білизна плямовивідник універсальний', 'Засіб для видалення плям на кольорових та білих речах \"Білизна плямовивідник універсальний\" 500мл', 'миючі засоби', 0, NULL, NULL, NULL, NULL, '999199', 12, 'пляшки', NULL, 1, NULL, '2026-01-07 10:36:23'),
(1798, 330489980, 6, 'миючі засоби', NULL, 'Білизна скло', NULL, 'миючі засоби', 0, NULL, NULL, NULL, NULL, '888188', 2, 'каністра', NULL, 1, NULL, '2026-01-07 10:37:38'),
(1799, 330489980, 6, 'миючі засоби', NULL, 'Білизна антиБак', 'Професійний засіб для очищення різноманітних поверхонь універсального використання \"Білизна анти бак\" 5000мл', 'миючі засоби', 0, NULL, NULL, NULL, NULL, '888288', 4, 'каністра', NULL, 1, NULL, '2026-01-07 10:38:58'),
(1800, 330489980, 6, 'миючі засоби', NULL, 'Білизна Посуд', 'Професійний концентрований засіб для ручного миття посуду \"Білизна посуд\" 5000мл', 'миючі засоби', 0, NULL, NULL, NULL, NULL, '888388', 0, 'каністра', NULL, 1, NULL, '2026-01-07 10:40:49'),
(1801, 330489980, 6, 'орг техніка', NULL, 'Накопичувач SSD 2.5\" 240GB Kingston', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'U0245933', 0, 'шт', 2655.00, 1, NULL, '2026-01-09 05:51:44'),
(1802, 330489980, 6, 'орг техніка', NULL, 'Серверний УПС PowerWalker VI 3000 RLE', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'U041563811', 0, 'шт', 22308.00, 1, NULL, '2026-01-09 05:55:37'),
(1803, 0, 6, 'канцелярські товари', NULL, 'Бумага офісна А4 500арк', NULL, 'канцелярські товари', 1, NULL, NULL, NULL, NULL, '3053833', 120, 'шт', NULL, 10, NULL, '2026-01-09 08:14:43'),
(1804, 0, 6, 'канцелярські товари', NULL, 'Файл прозорий \"Глянець\" 100шт', NULL, 'канцелярські товари', 0, NULL, NULL, NULL, NULL, '35536', 28, 'уп', NULL, 5, NULL, '2026-01-09 08:17:51'),
(1805, 0, 6, 'канцелярські товари', NULL, 'Папка-швидкозшивач картонна', NULL, 'канцелярські товари', 0, NULL, NULL, NULL, NULL, '354777', 43, 'шт', NULL, 10, NULL, '2026-01-09 08:20:09'),
(1806, 330489980, 6, 'господарчі товари', NULL, 'Целюлозні паперові рушники', NULL, 'господарчі товари', 1, NULL, NULL, NULL, NULL, '200', 0, 'шт', NULL, 32, NULL, '2026-01-14 07:58:26'),
(1807, 330489980, 6, 'господарчі товари', NULL, 'Папір туалетний в рулонах', 'Папір туалетний Лєста', 'господарчі товари', 1, NULL, NULL, NULL, NULL, '201', 0, 'шт', NULL, 50, NULL, '2026-01-14 08:01:53'),
(1808, 330489980, 6, 'господарчі товари', NULL, 'Туалетний папір Blanidas med-3складання №200', NULL, 'господарчі товари', 1, NULL, NULL, NULL, NULL, '202', 98, 'шт', NULL, 30, NULL, '2026-01-14 08:03:03'),
(1809, 330489980, 6, 'господарчі товари', NULL, 'Моп (запасний) до системи Vermop синій, 40см', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '203', 15, 'шт', NULL, 5, NULL, '2026-01-14 08:08:32'),
(1810, 330489980, 6, 'господарчі товари', NULL, 'Моп (запасний) до системи Vermop зелений, 40см', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '204', 15, 'шт', NULL, 5, NULL, '2026-01-14 08:08:44'),
(1811, 330489980, 6, 'господарчі товари', NULL, 'Моп (запасний) до системи Vermop жовтий, 40см', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '205', 15, 'шт', NULL, 5, NULL, '2026-01-14 08:09:00'),
(1812, 330489980, 6, 'господарчі товари', NULL, 'Моп (запасний) до системи Vermop червоний, 40см', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '206', 15, 'шт', NULL, 5, NULL, '2026-01-14 08:09:25'),
(1813, 330489980, 6, 'господарчі товари', NULL, 'Віник Драпак ручний (Дереза) з держаком', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '20700', 11, 'шт', NULL, 2, NULL, '2026-01-14 08:12:54'),
(1814, 330489980, 6, 'господарчі товари', NULL, 'Серветка Progressiv, блакитна', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '208', 10, 'шт', NULL, 10, NULL, '2026-01-14 08:17:57'),
(1815, 330489980, 6, 'господарчі товари', NULL, 'Серветка Progressiv, блакитна', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '209', 0, 'шт', NULL, 10, NULL, '2026-01-14 08:20:18'),
(1816, 330489980, 6, 'господарчі товари', NULL, 'Серветка Progressiv, жовта', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '210', 10, 'шт', NULL, 10, NULL, '2026-01-14 08:20:32'),
(1817, 330489980, 6, 'господарчі товари', NULL, 'Серветка Progressiv, зелена', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '211', 10, 'шт', NULL, 10, NULL, '2026-01-14 08:20:44'),
(1818, 330489980, 6, 'господарчі товари', NULL, 'Серветка Progressiv, червона', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '212', 10, 'шт', NULL, 10, NULL, '2026-01-14 08:24:36'),
(1819, 330489980, 6, 'господарчі товари', NULL, 'Рукавички Latexgloves XL', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '213', 0, 'шт', NULL, 10, NULL, '2026-01-14 08:25:12'),
(1820, 330489980, 6, 'господарчі товари', NULL, 'Віник №75 різний колір нитки 3-ох шов,', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '2140', 10, 'шт', NULL, 2, NULL, '2026-01-14 08:25:37'),
(1821, 330489980, 6, 'господарчі товари', NULL, 'Совок зі щіткою з ручкою 90см', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '215', 3, 'шт', NULL, 2, NULL, '2026-01-14 08:26:17'),
(1822, 330489980, 6, 'господарчі товари', NULL, 'Рукавички Зірка10/20', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '21600', 30, 'шт', NULL, 2, NULL, '2026-01-14 08:27:40'),
(1823, 330489980, 6, 'господарчі товари', NULL, 'Стакани одноразові 180мл прозорі 100шт', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '217', 54, 'шт', NULL, 5, NULL, '2026-01-14 08:28:00'),
(1824, 330489980, 6, 'буд матеріали', NULL, 'Пензлик Английський 2(50)', NULL, 'буд матеріали', 0, NULL, NULL, NULL, NULL, '21800', 4, 'шт', NULL, 0, NULL, '2026-01-14 08:29:45'),
(1825, 330489980, 6, 'буд матеріали', NULL, 'Пензлики 80мм', NULL, 'буд матеріали', 0, NULL, NULL, NULL, NULL, '219', 0, 'шт', NULL, 5, NULL, '2026-01-14 08:29:54'),
(1826, 330489980, 6, 'електрика', NULL, 'Кабельканал 2.5х1.5х200см', NULL, 'електрика', 0, NULL, NULL, NULL, NULL, '220', 0, 'шт', NULL, 2, NULL, '2026-01-14 08:31:21'),
(1827, 330489980, 6, 'електрика', NULL, 'Автомат 16А', NULL, 'електрика', 0, NULL, NULL, NULL, NULL, '221', 12, 'шт', NULL, 2, NULL, '2026-01-14 08:31:58'),
(1828, 0, 6, 'господарчі товари', NULL, 'Пакети для сміття 120л', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '230', 0, 'шт', NULL, 10, NULL, '2026-01-14 08:43:11'),
(1829, 0, 6, 'господарчі товари', NULL, 'Пакети для сміття 60л', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '231', 1, 'шт', NULL, 10, NULL, '2026-01-14 08:43:23'),
(1830, 0, 6, 'господарчі товари', NULL, 'Пакети для сміття 35л', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '232', 0, 'шт', NULL, 10, NULL, '2026-01-14 08:43:33'),
(1831, 330489980, 6, 'канцелярські товари', NULL, 'Папка сегригатор 50мм', NULL, 'канцелярські товари', 0, NULL, NULL, NULL, NULL, '2070', 0, 'шт', NULL, 5, NULL, '2026-01-16 11:27:30'),
(1832, 330489980, 6, 'канцелярські товари', NULL, 'Папка сегригатор 70мм', NULL, 'канцелярські товари', 0, NULL, NULL, NULL, NULL, '20707', 0, 'шт', NULL, 10, NULL, '2026-01-16 11:27:38'),
(1833, 330489980, 6, 'канцелярські товари', NULL, 'Папка швидкозшивач А4 з прозорим верхом 10шт, в уп', 'Папка швидкозшивач пластиковий А4 з прозорим верхом і перфорацією,різного коліру, 10шт, в уп', 'канцелярські товари', 0, NULL, NULL, NULL, NULL, '3088', 40, 'шт', NULL, 5, NULL, '2026-01-16 11:29:32'),
(1834, 330489980, 6, 'канцелярські товари', NULL, 'Папка на гумці', NULL, 'канцелярські товари', 0, NULL, NULL, NULL, NULL, '3089', 5, 'шт', NULL, 3, NULL, '2026-01-16 11:30:18'),
(1835, 330489980, 6, 'канцелярські товари', NULL, 'Клей-олівець', NULL, 'канцелярські товари', 0, NULL, NULL, NULL, NULL, '3090', 0, 'шт', NULL, 5, NULL, '2026-01-16 11:31:10'),
(1836, 330489980, 1, '40', NULL, 'Комп\'ютер', NULL, NULL, 0, NULL, 'Qube', NULL, '099317', '11200389', 1, 'шт', NULL, 0, NULL, '2026-01-22 09:19:29'),
(1837, 0, 6, 'буд матеріали', NULL, 'Валик ф 6 25/100', NULL, 'буд матеріали', 0, NULL, NULL, NULL, NULL, '27500', 0, 'шт', NULL, 0, NULL, '2026-02-09 10:47:22'),
(1838, 0, 6, 'буд матеріали', NULL, 'Валик ф 6 25/150мм', NULL, 'буд матеріали', 0, NULL, NULL, NULL, NULL, '102-110', 5, 'шт', NULL, 1, NULL, '2026-02-09 10:47:35'),
(1839, 0, 6, 'буд матеріали', NULL, 'Валик ф 8 40/180', NULL, 'буд матеріали', 0, NULL, NULL, NULL, NULL, '108042', 5, 'шт', NULL, 1, NULL, '2026-02-09 10:47:45'),
(1840, 0, 6, 'буд матеріали', NULL, 'Пензлик Английський 2,5 (63)', NULL, 'буд матеріали', 0, NULL, NULL, NULL, NULL, '30536000', 8, 'шт', NULL, 1, NULL, '2026-02-09 10:49:35'),
(1841, 0, 6, 'буд матеріали', NULL, 'Ванночка велика', NULL, 'буд матеріали', 0, NULL, NULL, NULL, NULL, '12311-000', 3, 'шт', NULL, 1, NULL, '2026-02-09 10:51:02'),
(1842, 0, 6, 'буд матеріали', NULL, 'Пензлик радіаторний', NULL, 'буд матеріали', 0, NULL, NULL, NULL, NULL, '30538-1222', 0, 'шт', NULL, 1, NULL, '2026-02-09 10:52:05'),
(1843, 330489980, 6, 'орг техніка', NULL, 'Картридж EPSON T8651', NULL, 'орг техніка', 1, NULL, NULL, NULL, NULL, 't8653xxxl', 28, 'шт', NULL, 2, NULL, '2026-03-10 12:06:42'),
(1844, 330489980, 6, 'господарчі товари', NULL, 'Рукавички латекс Obery XL', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '20260313091943', 0, 'шт', 0.00, 0, NULL, '2026-03-13 07:19:43'),
(1845, 330489980, 6, 'Загальний', NULL, 'Совок зі щіткою з довгою ручкою 90см', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'WH-20260313091943', 17, 'шт', 0.00, 0, NULL, '2026-03-13 07:19:43'),
(1848, 0, 6, 'миючі засоби', NULL, 'Білизна проф еліт економ, 5кг', 'Засіб миючий порошкоподібний універсальний \"Білизна проф еліт економ\" 5кг', 'миючі засоби', 0, NULL, NULL, NULL, NULL, 'WH-20260313104353', 2, 'шт', 0.00, 0, NULL, '2026-03-13 08:43:53'),
(1849, 330489980, 6, 'орг техніка', NULL, 'ДБЖ LogicPower 900W', 'ДБЖ LogicPower LP-UL 1550VA, 900W', 'орг техніка', 0, NULL, NULL, NULL, NULL, '30553712322', 1, 'шт', 5062.02, 1, NULL, '2026-03-20 07:36:08'),
(1850, 330489980, 1, '22', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', 'UV4Y028540', '101467082', 1, 'шт', NULL, 0, NULL, '2026-03-31 05:43:49'),
(1851, 330489980, 5, '133', NULL, 'Принтер', NULL, NULL, 0, NULL, 'Epson', 'WF-M5690', 'UV4Y038163', '101467097', 1, 'шт', NULL, 0, NULL, '2026-03-31 07:59:56'),
(1852, 330489980, 6, 'сантехніка', NULL, 'Поплавок з бічною подачею води1/2', NULL, 'сантехніка', 0, NULL, NULL, NULL, NULL, 'WH-202612322', 8, 'шт', 0.00, 0, NULL, '2026-04-01 05:28:34'),
(1853, 330489980, 6, 'сантехніка', NULL, 'Кріплення бачка набір', NULL, 'сантехніка', 0, NULL, NULL, NULL, NULL, 'WH-2026040423', 1, 'шт', 0.00, 0, NULL, '2026-04-01 05:28:34'),
(1855, 330489980, 6, 'сантехніка', NULL, 'Гофротруба для унітаза  армовона L-320-540', NULL, 'сантехніка', 0, NULL, NULL, NULL, NULL, 'WH-2026040108283400', 1, 'шт', 0.00, 2, NULL, '2026-04-01 05:28:34'),
(1856, 330489980, 6, 'сантехніка', NULL, 'Пакля UNIPAK 100 гр,', NULL, 'сантехніка', 0, NULL, NULL, NULL, NULL, 'WH-202604010828', 1, 'уп,', 0.00, 1, NULL, '2026-04-01 05:28:34'),
(1857, 330489980, 6, 'сантехніка', NULL, 'Шланг арм,1/2 мама+1/2 мама 60см,', NULL, 'сантехніка', 0, NULL, NULL, NULL, NULL, 'WH-2026044', 10, 'шт', 0.00, 0, NULL, '2026-04-01 05:28:34'),
(1858, 330489980, 6, 'Загальний', NULL, 'Кран маєвського 1/2', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'WH-20260401082834', 30, 'шт', 0.00, 0, NULL, '2026-04-01 05:28:34'),
(1859, 330489980, 6, 'сантехніка', NULL, 'Кран вентельний (кутовий) 1/2 папа+папа', NULL, 'сантехніка', 0, NULL, NULL, NULL, NULL, 'WH-2026040434', 2, 'шт', 0.00, 0, NULL, '2026-04-01 05:28:34'),
(1860, 330489980, 6, 'сантехніка', NULL, 'Заглушка внутрішня1/2', NULL, 'сантехніка', 0, NULL, NULL, NULL, NULL, 'WH-20260555', 4, 'шт', 0.00, 0, NULL, '2026-04-01 05:28:34'),
(1861, 330489980, 6, 'сантехніка', NULL, 'Заглушка зовнішня1/2', NULL, 'сантехніка', 0, NULL, NULL, NULL, NULL, 'WH-202604010888', 4, 'шт', 0.00, 0, NULL, '2026-04-01 05:28:34'),
(1862, 330489980, 6, 'сантехніка', NULL, 'Заглушка внутрішня3/4', NULL, 'сантехніка', 0, NULL, NULL, NULL, NULL, 'WH-20260401123', 2, 'шт', 0.00, 0, NULL, '2026-04-01 05:28:34'),
(1863, 330489980, 6, 'сантехніка', NULL, 'Заглушка зовнішня 3/4', NULL, 'сантехніка', 0, NULL, NULL, NULL, NULL, 'WH-2026040108283', 2, 'шт', 0.00, 0, NULL, '2026-04-01 05:28:34'),
(1865, 330489980, 6, 'сантехніка', NULL, 'Кран кульковий1/2мама+мама', NULL, 'сантехніка', 0, NULL, NULL, NULL, NULL, 'WH-202604010827', 10, 'шт', 0.00, 0, NULL, '2026-04-01 05:28:34'),
(1866, 330489980, 6, 'сантехніка', NULL, 'Кран кульковий1/2папа+мама', NULL, 'сантехніка', 0, NULL, NULL, NULL, NULL, 'WH-20260401082888', 4, 'шт', 0.00, 0, NULL, '2026-04-01 05:28:34'),
(1867, 330489980, 6, 'сантехніка', NULL, 'Кран кульковий3/4мама+мама', NULL, 'сантехніка', 0, NULL, NULL, NULL, NULL, 'WH-2026040108283411', 5, 'шт', 0.00, 0, NULL, '2026-04-01 05:28:34'),
(1868, 330489980, 6, 'сантехніка', NULL, 'Кран кульковий3/4папа+мама', NULL, 'сантехніка', 0, NULL, NULL, NULL, NULL, 'WH-202604010828345', 5, 'шт', 0.00, 0, NULL, '2026-04-01 05:28:34'),
(1869, 330489980, 6, 'сантехніка', NULL, 'Труба паячна гор,воду 20  3м,', NULL, 'сантехніка', 0, NULL, NULL, NULL, NULL, 'WH-2026040108', 3, 'шт', 0.00, 0, NULL, '2026-04-01 05:28:34'),
(1870, 330489980, 6, 'Загальний', NULL, 'Соєдініт,муфта  20', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'WH-20260401082834', 8, 'шт', 0.00, 0, NULL, '2026-04-01 05:28:34'),
(1871, 330489980, 6, 'сантехніка', NULL, 'Кутник під пайку 20    90градус', NULL, 'сантехніка', 0, NULL, NULL, NULL, NULL, 'WH-20260401322', 7, 'шт', 0.00, 0, NULL, '2026-04-01 05:28:34'),
(1872, 330489980, 6, 'сантехніка', NULL, 'Кутник під пайку20      45градус', NULL, 'сантехніка', 0, NULL, NULL, NULL, NULL, 'WH-20260401432', 8, 'шт', 0.00, 0, NULL, '2026-04-01 05:28:34'),
(1875, 330489980, 6, 'Загальний', NULL, 'Корпус замка код:165*35', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, 'WH-20260409082600', 8, 'шт', 0.00, 0, NULL, '2026-04-09 05:26:00'),
(1876, 330489980, 6, 'Загальний', NULL, 'Корпус замка код:35*85', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, 'WH-20260409082600', 13, 'шт', 0.00, 0, NULL, '2026-04-09 05:26:00'),
(1877, 330489980, 6, 'миючі засоби', NULL, 'Білизна трубоочисник 5000мл,', 'Професійний засіб для очищення труб та каналізації \"Білизна трубоочисник\" 5000мл', 'миючі засоби', 0, NULL, NULL, NULL, NULL, '115', 5, 'шт', NULL, 1, NULL, '2026-04-14 07:48:25'),
(1878, 330489980, 6, 'господарчі товари', NULL, 'Барель TrionZinc/Kedr-80мм,Ключ,', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '6514564185', 6, 'шт', NULL, 2, NULL, '2026-04-15 03:51:02'),
(1879, 330489980, 6, 'канцелярські товари', NULL, 'Діркопробивач BUROMAX', NULL, 'канцелярські товари', 0, NULL, NULL, NULL, NULL, '33333', 2, 'шт', NULL, 0, NULL, '2026-04-15 03:56:13'),
(1880, 330489980, 6, 'сантехніка', NULL, 'Змішувач Wezer умивальник одноважний', NULL, 'сантехніка', 0, NULL, NULL, NULL, NULL, '33433', 10, 'шт', NULL, 0, NULL, '2026-04-15 04:01:49'),
(1881, 330489980, 6, 'електрика', NULL, 'Кабель канал NEOMAKSULT.25*16mm 2м', NULL, 'електрика', 0, NULL, NULL, NULL, NULL, 'WH-2026041612555000', 6, 'шт', 0.00, 0, NULL, '2026-04-16 09:55:58'),
(1882, 330489980, 6, 'Загальний', NULL, 'Автомат-25 1Р', NULL, 'електрика', 0, NULL, NULL, NULL, NULL, 'WH-20260416125558', 5, 'шт', 0.00, 0, NULL, '2026-04-16 09:55:58'),
(1883, 330489980, 6, 'Загальний', NULL, 'Автомат-16 1Р', NULL, 'електрика', 0, NULL, NULL, NULL, NULL, 'WH-20260416125558', 5, 'шт', 0.00, 0, NULL, '2026-04-16 09:55:58'),
(1884, 330489980, 6, 'електрика', NULL, 'Корпус зовнішний під 1-2 автомат з кришкою', NULL, 'електрика', 0, NULL, NULL, NULL, NULL, 'WH-202604161255580011', 9, 'шт', 0.00, 0, NULL, '2026-04-16 09:55:58'),
(1885, 330489980, 6, 'Загальний', NULL, 'Одномісна розетка зовнішнього монтажу', NULL, 'електрика', 0, NULL, NULL, NULL, NULL, 'WH-20260416125558', 0, 'шт', 0.00, 0, NULL, '2026-04-16 09:55:58'),
(1886, 330489980, 6, 'Загальний', NULL, 'Пятимісна розетка зовнішнього монтажу', NULL, 'електрика', 0, NULL, NULL, NULL, NULL, 'WH-20260416125558', 0, 'шт', 0.00, 0, NULL, '2026-04-16 09:55:58'),
(1887, 330489980, 6, 'Загальний', NULL, 'Вимикач накладний подвійний', NULL, 'електрика', 0, NULL, NULL, NULL, NULL, 'WH-20260416125558', 8, 'шт', 0.00, 0, NULL, '2026-04-16 09:55:58'),
(1888, 330489980, 6, 'Загальний', NULL, 'Ізоляційна стрічка 3м', NULL, 'електрика', 0, NULL, NULL, NULL, NULL, 'WH-20260416125558', 8, 'шт', 0.00, 0, NULL, '2026-04-16 09:55:58'),
(1889, 330489980, 6, 'Загальний', NULL, 'Клемні колодки 4мм', NULL, 'електрика', 0, NULL, NULL, NULL, NULL, 'WH-20260416125558', 2, 'шт', 0.00, 0, NULL, '2026-04-16 09:55:58'),
(1890, 330489980, 6, 'електрика', NULL, 'ЛЕД світильник 60х60 ARM-3-600-50-6 50W.', NULL, 'електрика', 0, NULL, NULL, NULL, NULL, 'WH-202604161255588888', 50, 'шт,', 0.00, 0, NULL, '2026-04-16 09:55:58'),
(1891, 330489980, 6, 'електрика', NULL, 'Cвітлодіодна лампаBiomВТ-510А60 10W Е27', NULL, 'електрика', 0, NULL, NULL, NULL, NULL, 'WH-20260416125558000', 4, 'шт', 0.00, 0, NULL, '2026-04-16 09:55:58'),
(1892, 330489980, 6, 'Загальний', NULL, 'Коробка установча під бетон 65*45', NULL, 'електрика', 0, NULL, NULL, NULL, NULL, 'WH-20260416125558', 6, 'шт', 0.00, 0, NULL, '2026-04-16 09:55:58'),
(1893, 330489980, 6, 'Загальний', NULL, 'Хомут нейлоновий 20см (100шт)', NULL, 'електрика', 0, NULL, NULL, NULL, NULL, 'WH-20260416125558', 2, 'уп', 0.00, 0, NULL, '2026-04-16 09:55:58'),
(1894, 330489980, 6, 'електрика', NULL, 'Індикаторна викрутка DCY-1788', NULL, 'електрика', 0, NULL, NULL, NULL, NULL, 'WH-2026041612555800', 4, 'шт', 0.00, 0, NULL, '2026-04-16 09:55:58'),
(1895, 330489980, 6, 'Загальний', NULL, 'Подовжувач 4гн 2м', NULL, 'електрика', 0, NULL, NULL, NULL, NULL, 'WH-20260416125558', 3, 'шт', 0.00, 0, NULL, '2026-04-16 09:55:58'),
(1896, 330489980, 6, 'електрика', NULL, 'Подовжувач 5гн 2м', NULL, 'електрика', 0, NULL, NULL, NULL, NULL, 'WH-20260416125558888', 5, 'шт', 0.00, 0, NULL, '2026-04-16 09:55:58'),
(1897, 330489980, 6, 'електрика', NULL, 'Кабель ВВГ нг 3х2.5  (5кл) КУ  СІРИЙ', NULL, 'електрика', 0, NULL, NULL, NULL, NULL, 'WH-20260416125550', 30, 'м', 0.00, 0, NULL, '2026-04-16 09:55:58'),
(1898, 330489980, 6, 'електрика', NULL, 'Кабель ВВГ hr-LS-П 3х1.5 (5кл) КУ СІРИЙ', NULL, 'електрика', 0, NULL, NULL, NULL, NULL, 'WH-202604161255', 30, 'м', 0.00, 0, NULL, '2026-04-16 09:55:58'),
(1899, 330489980, 6, 'орг техніка', NULL, 'SSD 2.5 apacer 128gb', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'Ssd6627', 3, 'шт', NULL, 1, NULL, '2026-04-17 09:34:12'),
(1900, 330489980, 6, 'інструмент', NULL, 'Набір свердел по металу 1-10мм', NULL, 'інструмент', 0, NULL, NULL, NULL, NULL, 'WH-202604277', 2, 'шт', 0.00, 0, NULL, '2026-04-21 11:13:37'),
(1901, 330489980, 6, 'інструмент', NULL, 'Свердло по бетону 4х75 мм', NULL, 'інструмент', 0, NULL, NULL, NULL, NULL, 'WH-2026042114133700', 4, 'шт', 0.00, 0, NULL, '2026-04-21 11:13:37'),
(1902, 330489980, 6, 'інструмент', NULL, 'Свердло по бетону 6х100мм', NULL, 'інструмент', 0, NULL, NULL, NULL, NULL, 'WH-20260421141337007', 6, 'шт', 0.00, 0, NULL, '2026-04-21 11:13:37'),
(1903, 330489980, 6, 'інструмент', NULL, 'Свердло для перфорат, 6х110мм   дл-11см', NULL, 'інструмент', 0, NULL, NULL, NULL, NULL, 'WH-202604211413300', 2, 'шт', 0.00, 0, NULL, '2026-04-21 11:13:37'),
(1904, 330489980, 6, 'інструмент', NULL, 'Набір викруток', NULL, 'інструмент', 0, NULL, NULL, NULL, NULL, 'WH-2026042114', 2, 'шт', 0.00, 0, NULL, '2026-04-21 11:13:37'),
(1905, 330489980, 6, 'інструмент', NULL, 'Плоскогубці 200мм', NULL, 'інструмент', 0, NULL, NULL, NULL, NULL, 'WH-202604211410', 1, 'шт', 0.00, 0, NULL, '2026-04-21 11:13:37'),
(1906, 330489980, 6, 'інструмент', NULL, 'Кусачки-бокорізи 180мм', NULL, 'інструмент', 0, NULL, NULL, NULL, NULL, 'WH-202604211413', 1, 'шт', 0.00, 0, NULL, '2026-04-21 11:13:37'),
(1907, 330489980, 6, 'інструмент', NULL, 'Довгогубці 160мм', NULL, 'інструмент', 0, NULL, NULL, NULL, NULL, 'WH-2026042114133', 1, 'шт', 0.00, 0, NULL, '2026-04-21 11:13:37'),
(1908, 330489980, 6, 'інструмент', NULL, 'Рулетка вимірювальна 5м', NULL, 'інструмент', 0, NULL, NULL, NULL, NULL, 'WH-202604211417', 2, 'шт', 0.00, 0, NULL, '2026-04-21 11:13:37'),
(1909, 330489980, 6, 'інструмент', NULL, 'Рулетка вимірювальна 10м', NULL, 'інструмент', 0, NULL, NULL, NULL, NULL, '9082', 2, 'шт', 0.00, 1, NULL, '2026-04-21 11:13:37'),
(1910, 330489980, 6, 'інструмент', NULL, 'Захистни окуляри', NULL, 'інструмент', 0, NULL, NULL, NULL, NULL, 'WH-20260421141', 2, 'шт', 0.00, 0, NULL, '2026-04-21 11:13:37'),
(1911, 330489980, 6, 'інструмент', NULL, 'Набір шестигранників 1,5-10мм', NULL, 'інструмент', 0, NULL, NULL, NULL, NULL, 'WH-20260421141388', 2, 'шт', 0.00, 0, NULL, '2026-04-21 11:13:37'),
(1912, 330489980, 6, 'інструмент', NULL, 'Невеликий набір головок із тріскачкою 6-13', NULL, 'інструмент', 0, NULL, NULL, NULL, NULL, 'WH-20260421141366', 1, 'шт', 0.00, 0, NULL, '2026-04-21 11:13:37'),
(1913, 330489980, 6, 'інструмент', NULL, 'Набір викруток діелектричних', NULL, 'інструмент', 0, NULL, NULL, NULL, NULL, 'WH-202601', 0, 'шт', 0.00, 0, NULL, '2026-04-21 11:13:37'),
(1914, 330489980, 6, 'канцелярські товари', NULL, 'Штемпельна фарба KORES синя', NULL, 'канцелярські товари', 0, NULL, NULL, NULL, NULL, '323234234', 1, 'шт', NULL, 1, NULL, '2026-04-22 10:03:16'),
(1915, 330489980, 6, 'Підвал', NULL, 'Електролобзик ЗЕНІТ', NULL, 'інструмент', 1, NULL, NULL, NULL, NULL, 'INV-6-Підвал-001', 1, 'шт', NULL, 0, NULL, '2026-04-22 10:06:24'),
(1916, 330489980, 6, 'господарчі товари', NULL, 'Бідон 120л', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '2155', 8, 'шт', NULL, 2, NULL, '2026-04-22 10:07:10'),
(1917, 330489980, 6, 'орг техніка', NULL, 'Вебкамера прищіпка з мікрофоном HD1920x1080px', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, '34we11', 6, 'шт', NULL, 1, NULL, '2026-04-22 10:08:42'),
(1918, 330489980, 6, 'орг техніка', NULL, 'Акустична система', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, '323847283о', 3, 'шт', NULL, 2, NULL, '2026-04-22 10:09:16'),
(1919, 330489980, 6, 'інструмент', NULL, 'Викрутка з насадками', NULL, 'інструмент', 0, NULL, NULL, NULL, NULL, '432333', 1, 'шт', NULL, 0, NULL, '2026-04-22 10:10:27'),
(1920, 330489980, 6, 'господарчі товари', NULL, 'Граблі', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '32111', 2, 'шт', NULL, 0, NULL, '2026-04-22 10:10:46'),
(1921, 330489980, 6, 'господарчі товари', NULL, 'Драбина металева 4 сходинкова', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, 'ц43221', 1, 'шт', NULL, 0, NULL, '2026-04-22 10:11:13'),
(1922, 330489980, 6, 'різне', NULL, 'Желет утепленний \"Графіт\" XXL', NULL, 'різне', 0, NULL, NULL, NULL, NULL, 'цв2322', 5, 'шт', NULL, 1, NULL, '2026-04-22 10:11:54'),
(1923, 330489980, 6, 'різне', NULL, 'Желет утепленний \"Графіт\" XXХL', NULL, 'різне', 0, NULL, NULL, NULL, NULL, '324аа', 5, 'шт', NULL, 1, NULL, '2026-04-22 10:12:43'),
(1924, 330489980, 6, 'господарчі товари', NULL, 'Замок з секретом', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '11200339\\11200340', 1, 'шт', NULL, 0, NULL, '2026-04-22 10:25:39'),
(1925, 330489980, 6, 'інструмент', NULL, 'Ключ газовий', NULL, 'інструмент', 0, NULL, NULL, NULL, NULL, '11136550', 2, 'шт', NULL, 0, NULL, '2026-04-22 10:29:57'),
(1926, 330489980, 6, 'різне', NULL, 'Кулер для води VIO x-903', NULL, 'різне', 0, NULL, NULL, NULL, NULL, '11200291\\11200301', 2, 'шт', NULL, 0, NULL, '2026-04-22 10:32:17'),
(1927, 330489980, 6, 'інструмент', NULL, 'Кутова шліфувальна машина 125мм', NULL, 'інструмент', 1, NULL, NULL, NULL, NULL, '11138009', 1, 'шт', NULL, 0, NULL, '2026-04-22 10:32:54'),
(1928, 330489980, 6, 'різне', NULL, 'Ліхтар Rugged Flashlight 5V 9900mAh чорний', NULL, 'різне', 0, NULL, NULL, NULL, NULL, '11200292', 1, 'шт', NULL, 0, NULL, '2026-04-22 10:34:57'),
(1929, 330489980, 6, 'різне', NULL, 'Ліхтар Yajia YJ-2827', NULL, 'різне', 0, NULL, NULL, NULL, NULL, '11200308', 5, 'шт', NULL, 0, NULL, '2026-04-22 10:35:32'),
(1930, 330489980, 6, 'різне', NULL, 'Ламінатор', NULL, 'різне', 0, NULL, NULL, NULL, NULL, '112003581', 1, 'шт', NULL, 0, NULL, '2026-04-22 10:36:07'),
(1931, 330489980, 6, 'господарчі товари', NULL, 'Лідоруб', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '11136558', 2, 'шт', NULL, 0, NULL, '2026-04-22 10:36:35'),
(1932, 330489980, 6, 'господарчі товари', NULL, 'Лом пожарний', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '11136457', 2, 'шт', NULL, 0, NULL, '2026-04-22 10:36:58'),
(1933, 330489980, 6, 'інструмент', NULL, 'Лом цвягодер', NULL, 'інструмент', 0, NULL, NULL, NULL, NULL, '11200059', 1, 'шт', NULL, 0, NULL, '2026-04-22 10:37:22'),
(1934, 330489980, 6, 'інструмент', NULL, 'Міксер будівельний', NULL, 'інструмент', 0, NULL, NULL, NULL, NULL, '112000610', 1, 'шт', NULL, 0, NULL, '2026-04-22 10:37:44'),
(1935, 330489980, 6, 'різне', NULL, 'Манометр ДМ05-МП-ЗУ 100-1.6 МПА', NULL, 'різне', 0, NULL, NULL, NULL, NULL, '11200370', 4, 'шт', NULL, 0, NULL, '2026-04-22 10:39:03');
INSERT INTO `room_inventory` (`id`, `admin_telegram_id`, `branch_id`, `room_number`, `template_id`, `equipment_type`, `full_name`, `category`, `is_priority`, `balance_code`, `brand`, `model`, `serial_number`, `inventory_number`, `quantity`, `unit`, `price`, `min_quantity`, `notes`, `created_at`) VALUES
(1936, 330489980, 6, 'різне', NULL, 'Манометр ДМ05080-0.6 МПА 0-120С', NULL, 'різне', 0, NULL, NULL, NULL, NULL, '11200371', 1, 'шт', NULL, 0, NULL, '2026-04-22 10:41:08'),
(1937, 330489980, 6, 'електрика', NULL, 'Набор електрика', NULL, 'електрика', 0, NULL, NULL, NULL, NULL, '11200060', 1, 'шт', NULL, 0, NULL, '2026-04-23 03:20:33'),
(1938, 330489980, 6, 'інструмент', NULL, 'Набор ключів рожкових', NULL, 'інструмент', 0, NULL, NULL, NULL, NULL, '11138011', 1, 'шт', NULL, 0, NULL, '2026-04-23 03:23:00'),
(1939, 330489980, 6, 'інструмент', NULL, 'Набір торцевих голівок', NULL, 'інструмент', 0, NULL, NULL, NULL, NULL, '11138012', 1, 'шт', NULL, 0, NULL, '2026-04-23 03:24:52'),
(1940, 330489980, 6, 'різне', NULL, 'Намет кемпінговий уні сірий', NULL, 'різне', 0, NULL, NULL, NULL, NULL, '11200342', 1, 'шт', NULL, 0, NULL, '2026-04-23 03:27:07'),
(1941, 330489980, 6, 'електрика', NULL, 'Настільна лампа', NULL, 'електрика', 0, NULL, NULL, NULL, NULL, '11200402', 1, 'шт', NULL, 0, NULL, '2026-04-23 03:29:44'),
(1942, 330489980, 6, 'інструмент', NULL, 'Ножовка по дереву', NULL, 'інструмент', 0, NULL, NULL, NULL, NULL, '11138024', 0, 'шт', NULL, 0, NULL, '2026-04-23 03:35:32'),
(1943, 330489980, 6, 'інструмент', NULL, 'Ножовка по металу', NULL, 'інструмент', 0, NULL, NULL, NULL, NULL, '11138025', 1, 'шт', NULL, 0, NULL, '2026-04-23 03:36:46'),
(1944, 330489980, 6, 'різне', NULL, 'Обігрівач масляний', NULL, 'різне', 0, NULL, NULL, NULL, NULL, '112001090000', 2, 'шт', NULL, 1, NULL, '2026-04-23 03:39:16'),
(1945, 330489980, 6, 'інструмент', NULL, 'Паяльник для пайки пласт,пріборов', NULL, 'інструмент', 1, NULL, NULL, NULL, NULL, '11200140', 1, 'шт', NULL, 0, NULL, '2026-04-23 03:43:09'),
(1946, 330489980, 6, 'інструмент', NULL, 'Перфоратор', NULL, 'інструмент', 1, NULL, NULL, NULL, NULL, 'б/н', 1, 'шт', NULL, 0, NULL, '2026-04-23 03:44:33'),
(1947, 330489980, 6, 'інструмент', NULL, 'Плиткоріз', NULL, 'інструмент', 0, NULL, NULL, NULL, NULL, '11136680', 1, 'шт', NULL, 0, NULL, '2026-04-23 03:48:17'),
(1948, 330489980, 6, 'інструмент', NULL, 'Плоскогубці 200мм', NULL, 'інструмент', 0, NULL, NULL, NULL, NULL, '11136358', 0, 'шт', NULL, 0, NULL, '2026-04-23 03:50:05'),
(1949, 330489980, 6, 'різне', NULL, 'Портативний комплект рукомойник', NULL, 'різне', 0, NULL, NULL, NULL, NULL, '11200407', 1, 'шт', NULL, 0, NULL, '2026-04-23 03:52:43'),
(1950, 330489980, 6, 'різне', NULL, 'Прапор Одеси 1.15х0.85', 'Прапор Одеси 1.15х0.85 габардин двосторонній друк', 'різне', 0, NULL, NULL, NULL, NULL, '11200391', 1, 'шт', NULL, 0, NULL, '2026-04-23 04:12:32'),
(1951, 330489980, 6, 'різне', NULL, 'Прапор України1.15х0.85', NULL, 'різне', 0, NULL, NULL, NULL, NULL, '11200388-1', 1, 'шт', NULL, 0, NULL, '2026-04-23 04:13:10'),
(1952, 330489980, 6, 'різне', NULL, 'Прапор України1.15х0.85', NULL, 'різне', 0, NULL, NULL, NULL, NULL, '11200390', 1, 'шт', NULL, 0, NULL, '2026-04-23 04:14:00'),
(1953, 330489980, 6, 'різне', NULL, 'Радіоприймач Golon RX-9933 UAR', NULL, 'різне', 0, NULL, NULL, NULL, NULL, '11200307', 1, 'шт', NULL, 0, NULL, '2026-04-23 04:14:55'),
(1954, 330489980, 6, 'різне', NULL, 'Світильник настільний білий', NULL, 'різне', 0, NULL, NULL, NULL, NULL, '11138015-333', 3, 'шт', NULL, 0, NULL, '2026-04-23 04:16:06'),
(1955, 330489980, 6, 'різне', NULL, 'Світильник настільний червоний', NULL, 'різне', 0, NULL, NULL, NULL, NULL, '11138016-333', 2, 'шт', NULL, 0, NULL, '2026-04-23 04:16:36'),
(1956, 330489980, 6, 'різне', NULL, 'Скриня для ключей К300-93', NULL, 'різне', 0, NULL, NULL, NULL, NULL, 'БН', 1, 'шт', NULL, 0, NULL, '2026-04-23 04:18:02'),
(1957, 330489980, 6, 'інструмент', NULL, 'Сокира', NULL, 'інструмент', 0, NULL, NULL, NULL, NULL, '11138022', 1, 'шт', NULL, 0, NULL, '2026-04-23 04:18:36'),
(1958, 330489980, 6, 'інструмент', NULL, 'Сокира пожежна з діелектричною ручкою', NULL, 'інструмент', 0, NULL, NULL, NULL, NULL, '11200306', 2, 'шт', NULL, 0, NULL, '2026-04-23 04:18:57'),
(1959, 330489980, 6, 'господарчі товари', NULL, 'Стремянка ДНІПРО-М 8сх', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '11200062', 1, 'шт', NULL, 0, NULL, '2026-04-23 04:19:41'),
(1960, 330489980, 6, 'господарчі товари', NULL, 'Тачка садова 72л клевер', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '11200274-2', 1, 'шт', NULL, 0, NULL, '2026-04-23 04:20:08'),
(1961, 330489980, 6, 'інструмент', NULL, 'Точило електричне', NULL, 'інструмент', 1, NULL, NULL, NULL, NULL, '11138010', 1, 'шт', NULL, 0, NULL, '2026-04-23 04:20:23'),
(1962, 330489980, 6, 'інструмент', NULL, 'Турбинка електрична', NULL, 'інструмент', 1, NULL, NULL, NULL, NULL, '11137487', 1, 'шт', NULL, 0, NULL, '2026-04-23 04:20:47'),
(1963, 330489980, 6, 'різне', NULL, 'Флаг города', NULL, 'різне', 0, NULL, NULL, NULL, NULL, '11136257', 1, 'шт', NULL, 0, NULL, '2026-04-23 04:21:24'),
(1964, 330489980, 6, 'різне', NULL, 'Фанарь акумуляторний', NULL, 'різне', 0, NULL, NULL, NULL, NULL, '11138026', 2, 'шт', NULL, 0, NULL, '2026-04-23 04:21:56'),
(1965, 330489980, 6, 'господарчі товари', NULL, 'Шарнірна драбина-стремянка VIRASTAR ACROBAT', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '11200317', 1, 'шт', NULL, 0, NULL, '2026-04-23 04:22:38'),
(1966, 330489980, 6, 'господарчі товари', NULL, 'Шланг поливальний 50м', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '11200337', 1, 'шт', NULL, 0, NULL, '2026-04-23 04:27:47'),
(1967, 330489980, 6, 'інструмент', NULL, 'Шуруповерт акумулят', NULL, 'інструмент', 1, NULL, NULL, NULL, NULL, '11200500', 0, 'шт', NULL, 0, NULL, '2026-04-23 04:29:50'),
(1968, 330489980, 6, 'інструмент', NULL, 'Електролобзік', NULL, 'інструмент', 1, NULL, NULL, NULL, NULL, '11137535', 1, 'шт', NULL, 0, NULL, '2026-04-23 04:31:25'),
(1969, 330489980, 6, 'різне', NULL, 'Прапор УкраЇни1,15*0,85 габардін', NULL, 'різне', 0, NULL, NULL, NULL, NULL, '11200390000', 1, 'шт', NULL, 0, NULL, '2026-04-23 04:34:50'),
(1970, 330489980, 6, 'електрика', NULL, 'Автомат 1Р 10А', NULL, 'електрика', 0, NULL, NULL, NULL, NULL, '5115151', 6, 'шт', NULL, 0, NULL, '2026-04-23 04:54:07'),
(1971, 330489980, 6, 'буд матеріали', NULL, 'Амстронг', NULL, 'буд матеріали', 0, NULL, NULL, NULL, NULL, '4444', 55, 'шт', NULL, 0, NULL, '2026-04-23 05:10:45'),
(1972, 330489980, 6, 'сантехніка', NULL, 'Силікон білий', NULL, 'сантехніка', 0, NULL, NULL, NULL, NULL, '403', 4, 'шт', NULL, 0, NULL, '2026-04-23 05:33:58'),
(1973, 330489980, 6, 'сантехніка', NULL, 'Фум стрічка', NULL, 'сантехніка', 0, NULL, NULL, NULL, NULL, '24', 7, 'шт', NULL, 0, NULL, '2026-04-23 05:36:49'),
(1974, 330489980, 6, 'сантехніка', NULL, 'Змішувач для умив,Domino', NULL, 'сантехніка', 0, NULL, NULL, NULL, NULL, '852', 3, 'шт', NULL, 0, NULL, '2026-04-23 05:50:43'),
(1975, 330489980, 6, 'сантехніка', NULL, 'Коліно45*20', NULL, 'сантехніка', 0, NULL, NULL, NULL, NULL, '206000', 8, 'шт', NULL, 0, NULL, '2026-04-23 06:10:59'),
(1976, 330489980, 6, 'сантехніка', NULL, 'Коліно 90*20', NULL, 'сантехніка', 0, NULL, NULL, NULL, NULL, '15300', 8, 'шт', NULL, 0, NULL, '2026-04-23 06:13:12'),
(1977, 330489980, 6, 'електрика', NULL, 'Коробка розпод,герм 100*100*70мм', NULL, 'електрика', 0, NULL, NULL, NULL, NULL, '81940', 10, 'шт', NULL, 0, NULL, '2026-04-23 06:21:28'),
(1978, 330489980, 6, 'сантехніка', NULL, 'Кран повітровідвідний маєвського 1/2', NULL, 'сантехніка', 0, NULL, NULL, NULL, NULL, '57500', 30, 'шт', NULL, 0, NULL, '2026-04-23 06:28:04'),
(1979, 330489980, 6, 'сантехніка', NULL, 'Креплення бачка унітазу 100мм', NULL, 'сантехніка', 0, NULL, NULL, NULL, NULL, '99000', 1, 'шт', NULL, 0, NULL, '2026-04-23 06:31:45'),
(1980, 330489980, 6, 'господарчі товари', NULL, 'Линолеум  Ambient 3м', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '54821', 39, 'м', NULL, 0, NULL, '2026-04-23 06:35:49'),
(1981, 330489980, 6, 'сантехніка', NULL, 'Муфта с МРВ 20*1/2', NULL, 'сантехніка', 0, NULL, NULL, NULL, NULL, '12200', 4, 'шт', NULL, 0, NULL, '2026-04-23 06:39:02'),
(1982, 330489980, 6, 'сантехніка', NULL, 'Муфта с МРН 20*1/2', NULL, 'сантехніка', 0, NULL, NULL, NULL, NULL, '14520', 5, 'шт', NULL, 0, NULL, '2026-04-23 06:43:20'),
(1983, 330489980, 6, 'сантехніка', NULL, 'Муфта 20', NULL, 'сантехніка', 0, NULL, NULL, NULL, NULL, '13200', 8, 'шт', NULL, 0, NULL, '2026-04-23 06:45:08'),
(1984, 330489980, 6, 'сантехніка', NULL, 'Пєдестал Коlo', NULL, 'сантехніка', 0, NULL, NULL, NULL, NULL, '18600', 3, 'шт', NULL, 0, NULL, '2026-04-23 06:48:26'),
(1985, 330489980, 6, 'сантехніка', NULL, 'Боковий подвод 1/2', NULL, 'сантехніка', 0, NULL, NULL, NULL, NULL, '16640', 8, 'шт', NULL, 0, NULL, '2026-04-23 06:57:29'),
(1986, 330489980, 6, 'сантехніка', NULL, 'Раковина Kolo Status 50см,', NULL, 'сантехніка', 0, NULL, NULL, NULL, NULL, '1860044', 3, 'шт', NULL, 0, NULL, '2026-04-23 07:07:05'),
(1988, 330489980, 6, 'господарчі товари', NULL, 'Тактільна плитка бет, 300*300*40 жовта направляюча', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '27900', 30, 'шт', NULL, 0, NULL, '2026-04-23 07:16:28'),
(1989, 330489980, 6, 'господарчі товари', NULL, 'Тактильна плитка300*300*40 попереджувальна', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '279000000', 20, 'шт', NULL, 0, NULL, '2026-04-23 07:19:00'),
(1990, 330489980, 6, 'сантехніка', NULL, 'Труба 20*3,4PN20', NULL, 'сантехніка', 0, NULL, NULL, NULL, NULL, '81800', 9, 'шт', NULL, 0, NULL, '2026-04-23 07:26:01'),
(1991, 330489980, 6, 'електрика', NULL, 'Труба ПВХ гнуча гофрована с протяж, сіра', NULL, 'електрика', 0, NULL, NULL, NULL, NULL, '93000', 830, 'шт', NULL, 0, NULL, '2026-04-23 07:33:02'),
(1992, 330489980, 6, 'сантехніка', NULL, 'Шланг водяний1/2*1/2 м*м 40 см', NULL, 'сантехніка', 0, NULL, NULL, NULL, NULL, '37680', 3, 'шт', NULL, 0, NULL, '2026-04-23 07:40:18'),
(1993, 330489980, 6, 'сантехніка', NULL, 'Шланг вода 0.6м в.в', NULL, 'сантехніка', 0, NULL, NULL, NULL, NULL, '26290', 10, 'шт', NULL, 0, NULL, '2026-04-23 08:21:41'),
(1994, 330489980, 6, 'електрика', NULL, 'Schneider Asfora білий вимикач 1-й', NULL, 'електрика', 0, NULL, NULL, NULL, NULL, '95400', 5, 'шт', NULL, 0, NULL, '2026-04-23 08:27:07'),
(1995, 330489980, 6, 'електрика', NULL, 'Schneider Asfora білий Розетка', NULL, 'електрика', 0, NULL, NULL, NULL, NULL, '112000', 0, 'шт', NULL, 0, NULL, '2026-04-23 08:27:44'),
(1996, 330489980, 6, 'електрика', NULL, 'Viko Vera білий накладна розетка', NULL, 'електрика', 0, NULL, NULL, NULL, NULL, '135000', 0, 'шт', NULL, 0, NULL, '2026-04-23 08:28:16'),
(1997, 330489980, 6, 'електрика', NULL, 'Viko Vera білий накладна розетка 2-а з.з', NULL, 'електрика', 0, NULL, NULL, NULL, NULL, '191000', 0, 'шт', NULL, 0, NULL, '2026-04-23 08:28:39'),
(1998, 330489980, 6, 'електрика', NULL, 'Вилка ERKA з\\з біла', NULL, 'електрика', 0, NULL, NULL, NULL, NULL, '64050', 3, 'шт', NULL, 0, NULL, '2026-04-23 08:29:06'),
(1999, 330489980, 6, 'буд матеріали', NULL, 'Електроди \"МОНОЛІТ РЦ\" тубус 2.5кг', NULL, 'буд матеріали', 0, NULL, NULL, NULL, NULL, '473000', 1, 'тубус', NULL, 0, NULL, '2026-04-23 08:30:02'),
(2000, 330489980, 6, 'господарчі товари', NULL, 'Замок врізний', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '680', 2, 'шт', NULL, 0, NULL, '2026-04-23 08:30:58'),
(2001, 330489980, 6, 'сантехніка', NULL, 'Змивальний механізм NIKOPLAST однокл', NULL, 'сантехніка', 0, NULL, NULL, NULL, NULL, '504000', 1, 'шт', NULL, 0, NULL, '2026-04-23 08:31:40'),
(2002, 330489980, 6, 'сантехніка', NULL, 'Кран кульковий Reftec red 1/2 черв, металик', NULL, 'сантехніка', 0, NULL, NULL, NULL, NULL, '15700', 4, 'шт', NULL, 0, NULL, '2026-04-23 09:17:43'),
(2003, 330489980, 6, 'сантехніка', NULL, 'Кран мьяч DN15 1/2 на червона ручка', NULL, 'сантехніка', 0, NULL, NULL, NULL, NULL, '189000', 2, 'шт', NULL, 0, NULL, '2026-04-23 09:20:51'),
(2004, 330489980, 6, 'сантехніка', NULL, 'Поплавець до унітазу 1/2 NIKOPLAST', NULL, 'сантехніка', 0, NULL, NULL, NULL, NULL, '259200', 1, 'шт', NULL, 0, NULL, '2026-04-23 09:49:58'),
(2005, 330489980, 6, 'сантехніка', NULL, 'Поплавець-арматура SOLOPLAST', NULL, 'сантехніка', 0, NULL, NULL, NULL, NULL, '213600', 0, 'шт', NULL, 0, NULL, '2026-04-23 09:53:26'),
(2006, 330489980, 6, 'господарчі товари', NULL, 'Ручка алюмінієва L-1400', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '36000', 4, 'шт', NULL, 0, NULL, '2026-04-23 09:56:41'),
(2007, 330489980, 6, 'господарчі товари', NULL, 'Утримувач SprintPlus.40cm.Vermop', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '12420', 4, 'шт', NULL, 0, NULL, '2026-04-23 10:23:08'),
(2008, 330489980, 6, 'сантехніка', NULL, 'Сіфон для умивальніка Krona', NULL, 'сантехніка', 0, NULL, NULL, NULL, NULL, '165000', 13, 'шт', NULL, 0, NULL, '2026-04-23 11:01:42'),
(2009, 330489980, 6, 'господарчі товари', NULL, 'Циліндр замка АСК-80', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '299000', 0, 'шт', NULL, 0, NULL, '2026-04-23 11:07:50'),
(2010, 330489980, 6, 'буд матеріали', NULL, 'Шуруп чорн, 3,5*35', NULL, 'буд матеріали', 0, NULL, NULL, NULL, NULL, '0450', 30, 'шт', NULL, 0, NULL, '2026-04-24 03:26:20'),
(2011, 330489980, 6, 'буд матеріали', NULL, 'Шуруп чорн,4,2*75 дерев,', NULL, 'буд матеріали', 0, NULL, NULL, NULL, NULL, '0,900', 30, 'шт', NULL, 0, NULL, '2026-04-24 03:28:08'),
(2012, 330489980, 6, 'буд матеріали', NULL, 'Шуруп чорн, 4,8*100', NULL, 'буд матеріали', 0, NULL, NULL, NULL, NULL, '1550', 35, 'шт', NULL, 0, NULL, '2026-04-24 03:29:32'),
(2013, 330489980, 6, 'різне', NULL, 'Боти діалектрични', NULL, 'різне', 0, NULL, NULL, NULL, NULL, '1182', 1, 'шт', NULL, 0, NULL, '2026-04-24 10:06:15'),
(2014, 0, 6, 'електрика', NULL, 'світильник настільний білий', NULL, 'електрика', 0, NULL, NULL, NULL, NULL, '111380150', 3, 'шт', NULL, 0, NULL, '2026-04-27 04:22:11'),
(2015, 0, 6, 'господарчі товари', NULL, 'Диспенсер листового туалетн, паперу біл,', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '33430', 4, 'шт', NULL, 0, NULL, '2026-04-27 05:57:05'),
(2016, 0, 6, 'господарчі товари', NULL, 'Щітка для унітазу з кріпл, до стіни', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '39800', 4, 'шт', NULL, 0, NULL, '2026-04-27 06:13:17'),
(2017, 0, 6, 'господарчі товари', NULL, 'Руковички захистні латексні, нестерильні', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '28000', 14, 'шт', NULL, 0, NULL, '2026-04-27 06:15:54'),
(2018, 0, 6, 'господарчі товари', NULL, '8LT Корзина з педаллю нерж,сталь повіл,зак,', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '84900', 3, 'шт', NULL, 0, NULL, '2026-04-27 06:19:38'),
(2019, 0, 6, 'електрика', NULL, 'Продовжувач 4гн/3м', NULL, 'електрика', 0, NULL, NULL, NULL, NULL, '50900', 4, 'шт', NULL, 0, NULL, '2026-04-27 10:15:21'),
(2020, 0, 6, 'буд матеріали', NULL, 'Монтажна піна', NULL, 'буд матеріали', 0, NULL, NULL, NULL, NULL, 'WH-202604234', 4, 'шт', 0.00, 0, NULL, '2026-04-28 04:24:48'),
(2021, 0, 6, 'буд матеріали', NULL, 'Лак яхтовий   2,5кг', NULL, 'буд матеріали', 0, NULL, NULL, NULL, NULL, 'WH-20260422', 2, 'шт', 0.00, 0, NULL, '2026-04-28 04:24:48'),
(2022, 0, 6, 'буд матеріали', NULL, 'Фарба алкідна по бетону біла  2,5кг', NULL, 'буд матеріали', 0, NULL, NULL, NULL, NULL, 'WH-202604280', 1, 'шт', 0.00, 0, NULL, '2026-04-28 04:24:48'),
(2023, 0, 6, 'буд матеріали', NULL, 'Фарба-емаль пф-115 коричнева  2,5кг', NULL, 'буд матеріали', 0, NULL, NULL, NULL, NULL, 'WH-202604282', 6, 'шт', 0.00, 0, NULL, '2026-04-28 04:24:48'),
(2024, 0, 6, 'буд матеріали', NULL, 'Фарба-емаль пф-115 синя  2,5кг', NULL, 'буд матеріали', 0, NULL, NULL, NULL, NULL, 'WH-2026042', 2, 'шт', 0.00, 0, NULL, '2026-04-28 04:24:48'),
(2025, 0, 6, 'буд матеріали', NULL, 'Фарба-емаль пф-115 червона 2,5кг', NULL, 'буд матеріали', 0, NULL, NULL, NULL, NULL, 'WH-20260', 2, 'шт', 0.00, 0, NULL, '2026-04-28 04:24:48'),
(2026, 0, 6, 'буд матеріали', NULL, 'Фарба-емаль пф-115 жовта 2,5кг', NULL, 'буд матеріали', 0, NULL, NULL, NULL, NULL, 'WH-2026042807', 1, 'шт', 0.00, 0, NULL, '2026-04-28 04:24:48'),
(2027, 0, 6, 'господарчі товари', NULL, 'Тактильна стрічка 5см,', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, 'WH-202604280724', 28, 'м,', 0.00, 0, NULL, '2026-04-28 04:24:48'),
(2028, 0, 6, 'господарчі товари', NULL, 'Тактильна стрічка 10см,', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, 'WH-20260428', 10, 'м,', 0.00, 0, NULL, '2026-04-28 04:24:48'),
(2029, 0, 6, 'господарчі товари', NULL, 'Диспенсер рушників, білий', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '48960', 4, 'шт', NULL, 0, NULL, '2026-04-28 07:06:48'),
(2030, 0, 6, 'буд матеріали', NULL, 'Валик ф 6 25/100', NULL, 'буд матеріали', 0, NULL, NULL, NULL, NULL, '2750009', 5, 'шт', NULL, 0, NULL, '2026-04-28 10:11:34'),
(2031, 0, 6, 'інструмент', NULL, 'Диск ЗАК 125мм *1,2*22,23мм', NULL, 'інструмент', 0, NULL, NULL, NULL, NULL, '2700', 8, 'шт', NULL, 0, NULL, '2026-04-29 05:17:49'),
(2032, 0, 6, 'інструмент', NULL, 'ДискЗАК зачистний 125мм *6,0*22,23мм', NULL, 'інструмент', 0, NULL, NULL, NULL, NULL, '5340', 5, 'шт', NULL, 0, NULL, '2026-04-29 06:01:24'),
(2033, 0, 6, 'інструмент', NULL, 'Кирка 510г (ручка фіберглас)', NULL, 'інструмент', 0, NULL, NULL, NULL, NULL, '27200', 1, 'шт', NULL, 0, NULL, '2026-04-29 06:55:44'),
(2034, 0, 6, 'буд матеріали', NULL, 'Наждачка(сітка) 100', NULL, 'буд матеріали', 0, NULL, NULL, NULL, NULL, '30000', 20, 'шт', NULL, 0, NULL, '2026-04-29 06:57:57'),
(2035, 0, 6, 'буд матеріали', NULL, 'Наждачка (сітка) 120', NULL, 'буд матеріали', 0, NULL, NULL, NULL, NULL, '30000120', 20, 'шт', NULL, 0, NULL, '2026-04-29 07:00:03'),
(2036, 0, 6, 'господарчі товари', NULL, 'Сапка 200мм', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '99001', 2, 'шт', NULL, 0, NULL, '2026-04-29 07:01:44'),
(2037, 0, 6, 'господарчі товари', NULL, 'Сікатор садовий 200мм HAISSER', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '41500', 1, 'шт', NULL, 0, NULL, '2026-04-29 07:05:01'),
(2038, 0, 6, 'інструмент', NULL, 'Терка для наждачки цільноліта', NULL, 'інструмент', 0, NULL, NULL, NULL, NULL, '58100', 2, 'шт', NULL, 0, NULL, '2026-04-29 07:14:24'),
(2039, 0, 6, 'інструмент', NULL, 'Сокира сталь 1000г', NULL, 'інструмент', 0, NULL, NULL, NULL, NULL, '41100', 2, 'шт', NULL, 0, NULL, '2026-04-29 07:15:55'),
(2040, 0, 6, 'інструмент', NULL, 'Шпатель чорн,40', NULL, 'інструмент', 0, NULL, NULL, NULL, NULL, '1778', 2, 'шт', NULL, 0, NULL, '2026-04-29 07:18:11'),
(2041, 0, 6, 'інструмент', NULL, 'Шпатель 60', NULL, 'інструмент', 0, NULL, NULL, NULL, NULL, '2058', 2, 'шт', NULL, 0, NULL, '2026-04-29 07:57:36'),
(2042, 0, 6, 'інструмент', NULL, 'Шпатель 80', NULL, 'інструмент', 0, NULL, NULL, NULL, NULL, '2464', 1, 'шт', NULL, 0, NULL, '2026-04-29 07:58:41'),
(2043, 0, 6, 'інструмент', NULL, 'Шпатель 100', NULL, 'інструмент', 0, NULL, NULL, NULL, NULL, '2996', 1, 'шт', NULL, 0, NULL, '2026-04-29 07:59:41'),
(2044, 0, 6, 'інструмент', NULL, 'Шуруповерт акум,Metabo 1400 об/хв,', NULL, 'інструмент', 1, NULL, NULL, NULL, NULL, '462800', 1, 'шт', NULL, 0, NULL, '2026-04-29 08:05:06'),
(2045, 0, 6, 'інструмент', NULL, 'Шуруповерт акум,Metabo 1400 об/хв,', NULL, 'інструмент', 1, NULL, NULL, NULL, NULL, '4628000', 1, 'шт', NULL, 0, NULL, '2026-04-29 08:05:31'),
(2046, 0, 6, 'господарчі товари', NULL, 'Граблі мет,штирьові,сірі,10зуб,з Черенком', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '26000', 2, 'шт', NULL, 0, NULL, '2026-04-29 09:54:39'),
(2047, 0, 6, 'господарчі товари', NULL, 'Граблі мет,віялові з ручкою,15прут,росувні,', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '26800', 2, 'шт', NULL, 0, NULL, '2026-04-29 09:59:07'),
(2048, 0, 6, 'господарчі товари', NULL, 'Лопата штик,рельсова сталь з Черенком', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '25000', 2, 'шт', NULL, 0, NULL, '2026-04-29 10:04:25'),
(2049, 0, 6, 'електрика', NULL, 'Кабель ВВГ П нг 3х2.5 чорний', NULL, 'електрика', 0, NULL, NULL, NULL, NULL, '36310', 180, 'м', NULL, 0, NULL, '2026-05-08 09:30:44'),
(2050, 0, 6, 'електрика', NULL, 'Кабель ВВГ П нг 2*1,5 чорний', NULL, 'електрика', 0, NULL, NULL, NULL, NULL, '15820', 40, 'м', NULL, 0, NULL, '2026-05-08 09:34:15'),
(2051, 0, 6, 'господарчі товари', NULL, 'Відро поліетиленове 10л,', NULL, 'господарчі товари', 0, NULL, NULL, NULL, NULL, '3308', 31, 'шт', NULL, 0, NULL, '2026-05-18 06:57:28'),
(2052, 0, 6, 'канцелярські товари', NULL, 'Книга А4 96стр,', NULL, 'канцелярські товари', 0, NULL, NULL, NULL, NULL, '330800', 14, 'шт', NULL, 1, NULL, '2026-05-18 07:29:00'),
(2053, 330489980, 6, 'Підвал', NULL, 'Принтер', NULL, 'орг техніка', 0, NULL, 'Epson', 'WF-M5690', 'UV4Y038683', '101480200', 1, 'шт', NULL, 0, NULL, '2026-05-19 05:20:15'),
(2054, 0, 6, 'інструмент', NULL, 'Викрутка індікаторна', NULL, 'інструмент', 0, NULL, NULL, NULL, NULL, '25600', 4, 'шт', NULL, 0, NULL, '2026-06-01 08:17:03'),
(2055, 0, 6, 'Загальний', NULL, 'Принтер чеків SPRT POS 58 IV with auto-cut USB (SP-POS58IVU with auto-cut)', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'WH-20260623111941', 2, 'шт', 3115.00, 0, NULL, '2026-06-23 08:19:41'),
(2056, 0, 6, 'Загальний', NULL, 'Мобільний телефон Xiaomi Redmi 15C 4/128GB Midnight Black (1163425)', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'WH-20260623111941', 2, 'шт', 6200.00, 0, NULL, '2026-06-23 08:19:41'),
(2057, 0, 6, 'Загальний', NULL, 'Пристрій безперебійного живлення PowerWalker VI 3000 RLE', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'WH-20260623112327', 2, 'шт', 23000.00, 0, NULL, '2026-06-23 08:23:27'),
(2058, 0, 6, 'Загальний', NULL, 'Пристрій безперебійного живлення Logicpower LP-UL 1550VA, 900W', NULL, NULL, 0, NULL, NULL, NULL, NULL, 'WH-20260623112327', 3, 'шт', 4500.00, 0, NULL, '2026-06-23 08:23:27'),
(2059, 0, 6, 'орг техніка', NULL, 'Монітор 2E F2725B', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'WH-2026062311', 0, 'шт', 4200.00, 0, NULL, '2026-06-23 08:28:13'),
(2060, 0, 6, 'орг техніка', NULL, 'Комп\'ютер Vinga Advanced A1691', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'WH-2023333', 2, 'шт', 13494.00, 0, NULL, '2026-06-23 08:28:13'),
(2061, 0, 6, 'орг техніка', NULL, 'Комутатор мережевий Mercusys MS105G', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'WH-202604122', 4, 'шт', 370.00, 0, NULL, '2026-06-23 08:28:13'),
(2062, 330489980, 6, 'Загальний', NULL, 'Контейнер з чорнилом Epson 664 комплект B/C/M/Y (по70мл) L100/L200 (C13T66464A)', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'WH-20260630122104', 2, 'шт', 1300.00, 0, NULL, '2026-06-30 09:21:04'),
(2063, 330489980, 6, 'Загальний', NULL, 'Картридж HP LJ 44A, для M15/M28 Black 1К (CF244A)', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'WH-20260630122104', 1, 'шт', 3300.00, 0, NULL, '2026-06-30 09:21:04'),
(2064, 330489980, 6, 'Загальний', NULL, 'Драм картридж HP Imaging Drum 19A (CF219A)', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'WH-20260630122104', 2, 'шт', 4500.00, 0, NULL, '2026-06-30 09:21:04'),
(2065, 330489980, 6, 'Загальний', NULL, 'Картридж HP LJ 17A, Pro M130 Black (CF217A)', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'WH-20260630122104', 1, 'шт', 5000.00, 0, NULL, '2026-06-30 09:21:04'),
(2066, 330489980, 6, 'Загальний', NULL, 'Картридж WWM для Samsung SL-M2020/2070/2070FW аналог MLT-D111S Black (LC58N)', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'WH-20260630122104', 2, 'шт', 800.00, 0, NULL, '2026-06-30 09:21:04'),
(2067, 330489980, 6, 'Загальний', NULL, 'Мережевий фільтр живлення Patron EXT-PN-SP-1032, 1.8m Black (EXT-PN-SP-1032)', NULL, 'електрика', 0, NULL, NULL, NULL, NULL, 'WH-20260630122115', 5, 'шт', 160.00, 0, NULL, '2026-06-30 09:21:15'),
(2068, 330489980, 6, 'Загальний', NULL, 'Стабілізатор Europower EPX-1004', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'WH-20260630122115', 4, 'шт', 1000.00, 0, NULL, '2026-06-30 09:21:15'),
(2069, 330489980, 6, 'Загальний', NULL, 'Принтер етикеток X-PRINTER XP-420B usb, Ethernet (XP-420B-0082)', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'WH-20260630122115', 5, 'шт', 4250.00, 0, NULL, '2026-06-30 09:21:15'),
(2070, 330489980, 6, 'Загальний', NULL, 'Сканер штрих-коду Xkancode B1 USB, Black (B1)', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'WH-20260630122115', 5, 'шт', 1000.00, 0, NULL, '2026-06-30 09:21:15'),
(2071, 330489980, 6, 'Загальний', NULL, 'Мишка Logitech B100 Black (910-003357)', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'WH-20260630122115', 5, 'шт', 400.00, 0, NULL, '2026-06-30 09:21:15'),
(2072, 330489980, 6, 'Загальний', NULL, 'Батарея до ДБЖ Long 12В 7Ач', NULL, 'електрика', 0, NULL, NULL, NULL, NULL, 'WH-20260630122128', 8, 'шт', 653.00, 0, NULL, '2026-06-30 09:21:28'),
(2073, 330489980, 6, 'Загальний', NULL, 'Конектор Ritar RJ45 cat.5e UTP 8P8C PREMIUM (100шт в уп)', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'WH-20260630122128', 1, 'уп', 0.00, 0, NULL, '2026-06-30 09:21:28'),
(2074, 330489980, 6, 'Загальний', NULL, 'Тестер кабельний Noyafa RJ-45, RJ-11', NULL, 'електрика', 0, NULL, NULL, NULL, NULL, 'WH-20260630122128', 1, 'шт', 0.00, 0, NULL, '2026-06-30 09:21:28'),
(2075, 330489980, 6, 'Загальний', NULL, 'Маршрутизатор Netis WF2419E', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'WH-20260630122128', 7, 'шт', 0.00, 0, NULL, '2026-06-30 09:21:28'),
(2076, 330489980, 6, 'Загальний', NULL, 'Монітор ViewSonic VA220-H', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'WH-20260630122128', 2, 'шт', 0.00, 0, NULL, '2026-06-30 09:21:28'),
(2077, 330489980, 6, 'Загальний', NULL, 'ОЗУ DDR3 8GB 1600 MHz Prologix (PRO8GB1600D3)', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'WH-20260630122128', 5, 'шт', 0.00, 0, NULL, '2026-06-30 09:21:28'),
(2078, 330489980, 6, 'Загальний', NULL, 'ОЗУ DDR4 8GB 2400 MHz INTELIGENTES (IU4BHC1/8)', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'WH-20260630122128', 5, 'шт', 0.00, 0, NULL, '2026-06-30 09:21:28'),
(2079, 330489980, 6, 'Загальний', NULL, 'Клавіатура Logitech K120 Ukr (920-002643)', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'WH-20260630122128', 8, 'шт', 0.00, 0, NULL, '2026-06-30 09:21:28'),
(2080, 330489980, 6, 'Загальний', NULL, 'Мишка Logitech M100 USB Black (910-006652)', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'WH-20260630122128', 5, 'шт', 0.00, 0, NULL, '2026-06-30 09:21:28'),
(2081, 330489980, 6, 'Загальний', NULL, 'Мережевий подовжувач Europower EPG518, 5 роз.', NULL, 'електрика', 0, NULL, NULL, NULL, NULL, 'WH-20260630122128', 5, 'шт', 0.00, 0, NULL, '2026-06-30 09:21:28'),
(2082, 330489980, 6, 'Загальний', NULL, 'Мережевий подовжувач Defender S418 1.8 m 4 роз switch white (99237)', NULL, 'електрика', 0, NULL, NULL, NULL, NULL, 'WH-20260630122128', 4, 'шт', 0.00, 0, NULL, '2026-06-30 09:21:28'),
(2083, 330489980, 6, 'Загальний', NULL, 'Чіп WWM для НПК EPSON WorkForce Pro WF-M5690', NULL, 'орг техніка', 0, NULL, NULL, NULL, NULL, 'WH-20260630122128', 8, 'шт', 165.00, 0, NULL, '2026-06-30 09:21:28'),
(2086, 330489980, 6, 'інструмент', NULL, 'свердло 08*160', NULL, 'інструмент', 0, NULL, NULL, NULL, NULL, '452000', 1, 'шт', NULL, 0, NULL, '2026-08-06 06:33:22');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('4GEgNLyc3lIzAYYIVJeQpdbge6VhLzj20Bcuq6Gk', NULL, '162.62.231.139', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTlp6UEY2NnJIYUpOTUR0dExaTDhEM1BmNnY3S2o0SVdUZENhYkdXZCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly93d3cuY3Btc2QxNi5vbmxpbmUvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1790089219),
('92M0UTD4ihKddI6HbLWlTx9IP1ehb0XvF2bbQb3v', NULL, '54.221.155.113', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7_8 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidFpDQ2ZiOTNqMThBcTZEM1BrMWhsUVNHaVhaMFJ0Y012QmhkYVJCdCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjI6Imh0dHBzOi8vY3Btc2QxNi5vbmxpbmUiO3M6NToicm91dGUiO047fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1790102635),
('9o6ln06fSd2hyAwirc3R2vGgoLcKlijaAHsMVJqu', NULL, '195.138.83.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoib2xsaDdyUDhEWkY4cHk3Z3NGRWlZdVlDZ0dxMVhQeW13R0ZsRjQ2NCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8vY3Btc2QxNi5vbmxpbmUvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1790139973),
('A4Wrm8DD18lEY35hwOdb0YAU8v52vbezc3M3VnRT', NULL, '158.173.67.106', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiV20xRTk1NjR6MURlVXJuRGViTlRGZVcyb1huQ1RYZkZUaUV5YThPVyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9jcG1zZDE2Lm9ubGluZSI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1790084408),
('aFdAYeKJixTPLNRGHbqeKxY2WBmF2nb3p0QJc2BJ', NULL, '43.166.237.57', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSTV0MVVrQkJ0TDFJY01zNTg2NjVVdGtUTFB3djV6R0RJaTByMGdTdSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly93d3cuY3Btc2QxNi5vbmxpbmUvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1790133845),
('BX0t3R5XTbohsH7mV3P8LnQnXi9ZHZ62xWAcW9eF', NULL, '195.96.139.30', 'Mozilla/5.0 (compatible; InternetMeasurement/1.0; +https://internet-measurement.com/)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUXJFSTlCS0doRnhaWGVPMEZpTkFJQzJIMmRlZ2R5NjFoMzJ1cGtkZiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjY6Imh0dHBzOi8vY3Btc2QxNi5vbmxpbmU6NDQ0IjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1790091310),
('byN4tWGH0WO74IEpPqrdFAyxnglqOqvo4VhXKseD', 1, '195.138.83.96', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSXRyVWhEc08xN2p6SVI4amFZSEMwUVBYVW5wcmRySVNZaU9JMTJlOSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHBzOi8vY3Btc2QxNi5vbmxpbmUvcHVyY2hhc2UtcmVxdWVzdHMiO3M6NToicm91dGUiO3M6MjM6InB1cmNoYXNlLXJlcXVlc3RzLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1790149538),
('cjfX1rhmSoDT3pPtJaNoR4sl7WzPgL2TDTnFlmfr', NULL, '43.164.197.117', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTzRiMlR1NG9ZZ1NXcHFiRkNWOWdoajFxOUhYZk1aTTNtcFM2R215SCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9jcG1zZDE2Lm9ubGluZS9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1790071077),
('cR3BjEjm9BRsbEaLIc5hlPGtmvu2CtjBLwZuHCLV', 3, '195.138.83.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiT1NscHc0WXZjR3NzbGoySkV5TDQ2elpMWFJUSmo0YTRrcGluNWdhVCI7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MztzOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozNzoiaHR0cHM6Ly9jcG1zZDE2Lm9ubGluZS93YXJlaG91c2UvMTgyMCI7czo1OiJyb3V0ZSI7czoxNDoid2FyZWhvdXNlLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1790147513),
('CzKINH8wxodfJwmTZWjzFhsUShgRFZe08tL2XQDt', NULL, '194.132.62.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36 Viewer/99.9.8853.8', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWEM3OGF3ejlpeENBemJmMEVmaE9IU0dSdHM3bE1oRnBFWmQwUWQ1YiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9jcG1zZDE2Lm9ubGluZS9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1790110076),
('DU1RFjogcAk4bcjE2nKsKIfr5gKzM3iBPHfAzyGS', NULL, '54.221.155.113', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7_8 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoienlFQzhFckl5cGR5Nm5OVGtLdWZKMHFncm5IUWhmaU5nOThsRUY2SiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8vY3Btc2QxNi5vbmxpbmUvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1790102694),
('eqqhmhyviqaxLxGlnRv5gDMFqkPDcBmeCqqqKZKR', NULL, '54.242.112.133', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7_8 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiODd5REtVMW9pQW80b3dCTkdoR05mSmczUnAxVWh6c0VraU03SzNOMyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8vY3Btc2QxNi5vbmxpbmUvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1790101867),
('eTBszNJNtjKu1SwPTTr3UbO9bTZLI5PaAp6jwxfb', 1, '195.138.83.96', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiSFRXNTVWM2dUUnhud2U0TzZrUGlHcmxXRzFaa2ZEbndmTUhYN3djQSI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjQyOiJodHRwczovL2NwbXNkMTYub25saW5lL3dhcmVob3VzZS1tb3ZlbWVudHMiO3M6NToicm91dGUiO3M6MTk6IndhcmVob3VzZS5tb3ZlbWVudHMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1790082862),
('gnaUj7IfalOxKEhDUck5EyMWeMMs4fYcNPA3kPZ3', NULL, '104.28.131.167', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_6_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Mobile/15E148 Safari/604.1', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRzVseERJVDRVZGVoYjlla3N4ZTZNZ042Nkw1MWNNbWpNRU56anNjRyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9jcG1zZDE2Lm9ubGluZS93YXJlaG91c2UiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9jcG1zZDE2Lm9ubGluZS93YXJlaG91c2UiO3M6NToicm91dGUiO3M6MTU6IndhcmVob3VzZS5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1790073209),
('GpOnrre3nIZszWnqfgLfAUKBP6HxLgKDLGi6cfyT', NULL, '104.28.131.168', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_6_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Mobile/15E148 Safari/604.1', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiWmUxYkVCZW1vNDRQWUJxZ21yelJhZkpnT0hVOVZsQnZRRlVjdGk2NSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMjoiaHR0cHM6Ly9jcG1zZDE2Lm9ubGluZS93YXJlaG91c2UiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czoyODoiaHR0cHM6Ly9jcG1zZDE2Lm9ubGluZS9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1790074442),
('hXBiKifjHgYL8Jrf43fipqvqR5lgO0UY7eaGLGTz', NULL, '43.130.91.95', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQ3RKQWl2OTJRMDhsbmJ0MTAyQktWQTlTek1HS3ZpUHNZQkplTnpLYyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9jcG1zZDE2Lm9ubGluZS9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1790086308),
('IYBq7VBzurtm9GoJ9ZKq7nB9qrgsjRTBPBev7N5m', NULL, '5.183.255.30', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36/Nutch-1.23-SNAPSHOT', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQWw5WWdWYXljZXZKSzFoT2RYMWs4M1U1Q3dVRkFtTzFNMlRJY1psWSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9jcG1zZDE2Lm9ubGluZSI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1790119674),
('jNkQzJCPbuhflFznvUzjqCAdG3PAgbpZge0ijwPp', NULL, '32.194.74.48', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNE1uZXo4aDhtVFlZcHI3ckJqNENpZnN3cVFBVTNTUHUwejM4TTFrUiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9jcG1zZDE2Lm9ubGluZSI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1790080863),
('k0mnrvLs7ZVwtMwQmP8jTmsqn1F6UW6SiVh7FT8z', NULL, '54.162.26.144', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7_8 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicERPRWs4N1JTMkdYOERRWlJCVXBHSkUyZUlibnB5SkxIWDRLeVBrUCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjI6Imh0dHBzOi8vY3Btc2QxNi5vbmxpbmUiO3M6NToicm91dGUiO047fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1790103311),
('kzxVSPQEkE5ghl5kSCy5MdUXnq75SM1YfmPJkSQu', NULL, '43.159.145.149', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSW15OGhRRUtoVldiVERvcll6RFlQVVloVXp4TDNHb3ZDckxFaEJZSSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly93d3cuY3Btc2QxNi5vbmxpbmUvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1790074452),
('m8OdWMKg3tjnQs5WxsbublP8QiCWvW2hpGSa2BAh', NULL, '195.96.139.30', 'Mozilla/5.0 (compatible; InternetMeasurement/1.0; +https://internet-measurement.com/)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiR1lONnpOeFRpMXBIckx5aEZodHlLbktRdGgyMDFyY09LODRMNzJiOSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzI6Imh0dHBzOi8vY3Btc2QxNi5vbmxpbmU6NDQ0L2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1790091310),
('nhqgL0fOL37nIecwVe6gonhgV1vcfkqLE2X04pFs', NULL, '74.7.227.54', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; GPTBot/1.4; +https://openai.com/gptbot)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiT3pBRWV5b1loRFowODBrT1FBV2FWbFdYc0RoOWpBTkFidTRkVkpRQyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHBzOi8vY3Btc2QxNi5vbmxpbmUvZm9yZ290LXBhc3N3b3JkIjtzOjU6InJvdXRlIjtzOjE2OiJwYXNzd29yZC5yZXF1ZXN0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1790074851),
('npZfNSJl5Kbaq3gwsQi1CGz0VDF8lOg2Vyvee2UN', NULL, '180.153.236.188', 'User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0; 360Spider', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTXZuQnFxQkJuSzY5RW1iM0lGdm5XM2wyT0VSYTM2ME5jQXRmUUtqayI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly93d3cuY3Btc2QxNi5vbmxpbmUvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1790101848),
('pHv7FQhCkJT9CS1xMWQvlM8QjhMdQDV3jLh3jtCS', NULL, '193.183.109.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36 Viewer/99.9.8853.8', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiczBZSlJjbVpkYnVUMTZ2NzNBTElMaFR5aDU1R0p3VGtXZVFHRXBWMSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9jcG1zZDE2Lm9ubGluZS9mb3Jnb3QtcGFzc3dvcmQiO3M6NToicm91dGUiO3M6MTY6InBhc3N3b3JkLnJlcXVlc3QiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1790110076),
('PiBwkdKZhKOKCfg2eYsIk7c26Vy9dvKZI1ILWlL8', NULL, '49.51.36.179', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRUV1eUpVNWFOSEhMQmIySm1nT0pqNXFNM0hNQm5RbW01ZnVseFRKYSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9jcG1zZDE2Lm9ubGluZS9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1790146966),
('pLg122AxQqcrJxiDvnTNjr5R8UJQvglAaj4mtk8G', 1, '104.28.131.168', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_6_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Mobile/15E148 Safari/604.1', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoic01GYzlaajVTR0NBQWdDOUdldkFVOE5ZNTU4elJuM29IcTAyazFyRiI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjExMjoiaHR0cHM6Ly9jcG1zZDE2Lm9ubGluZS93YXJlaG91c2U/Y2F0ZWdvcnk9JUQwJUJFJUQxJTgwJUQwJUIzJTIwJUQxJTgyJUQwJUI1JUQxJTg1JUQwJUJEJUQxJTk2JUQwJUJBJUQwJUIwJnBhZ2U9MSI7czo1OiJyb3V0ZSI7czoxNToid2FyZWhvdXNlLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1790074448),
('PpwtkPEjCpYLqMftQfzTR905cPO8IOWX5B7a3pyo', NULL, '32.194.74.48', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVEc4YklDcklQOVEzdTNrYTVqSGlhY05saWxNeEoyQXBzc2U5eklpQiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9jcG1zZDE2Lm9ubGluZS9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1790080863),
('qPn8ZJ7FiwkvKMgb0YPszqWooQ8i5sPxIkm793kv', NULL, '54.221.155.113', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7_8 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZk1VMERtQzVRd0RXclMwU25SV1AzMm5MVGQ1VzdMYWFDdDdWd0NqeiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8vY3Btc2QxNi5vbmxpbmUvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1790102636),
('s5pAyalgDrpZ3PeBClL7sODo4BOHEAieGtR0lb03', NULL, '54.242.112.133', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7_8 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYzVGUTNPbG4yanFHTVhFdmFtRkk2cnlwWkNFSFZpQ2FyM3E4RzYzcSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8vY3Btc2QxNi5vbmxpbmUvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1790101917),
('St81Uv13CVA6s1gMAMIFiJSdoFjMPf9P4DpfrKdi', NULL, '123.207.65.62', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibzQ5RkV0VHR6cElUcWxnb0N2dHRTMnVkVldBOHRPUW1wNE0xQXppcyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9jcG1zZDE2Lm9ubGluZS9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1790111246),
('T4tGkfZoiTXadTrVB1yvYp4yFvRnPtZLHqfwywoo', NULL, '158.173.67.106', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiM29ITkFOM2huS21LdWVIWVZaQ01JRENKUElid0lmbWVielhlNXFmeiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9jcG1zZDE2Lm9ubGluZS9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1790084409),
('Ts6nXcVkc2hjvNMAr1nsAXZSDelLnlekHKEtlzmy', NULL, '8.138.201.190', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZ0RteWJEbUxwYU5kMFZ3R1hTVXVPUkJ5UzlaYTJIZWNqNWZPSzlPZCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly93d3cuY3Btc2QxNi5vbmxpbmUvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1790133713),
('TzJMrTmmuBkwHWYHPbTmw7kb2hf0tCyF9mtzzH9i', 1, '185.228.104.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiQUlNUUFkTHk1cUF1SnFaamlvUUZpNDZZbnh5UjBYcUY0Sm9zRXhGWSI7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9jcG1zZDE2Lm9ubGluZS9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1790115254),
('Uanxe28mcLH7nDebsN1qSbAL4YWiufWe2jfmIhxH', NULL, '170.168.30.156', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36/Nutch-1.23-SNAPSHOT', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWFc3ME1zT3o0WG02ME1vSUFmMHpPaGpaTmxkZm5IcEVVOUNjRFFBNiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9jcG1zZDE2Lm9ubGluZS9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1790119674),
('uLnX5mcVIVO2nPaRT21QeWjZIi8avlVrDyjmPUd7', 1, '185.228.104.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiOENJREJVdDVBWTRzUUs1bU1xZHBUOHZONWpleExMbkdiTVRTeDkyeSI7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMToiaHR0cHM6Ly9jcG1zZDE2Lm9ubGluZS9icmFuY2hlcyI7czo1OiJyb3V0ZSI7czoxNDoiYnJhbmNoZXMuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1790094850),
('VnOfdbmz5roHC8fERi3i1gQyigOdxL569lt783cf', NULL, '54.162.26.144', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7_8 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMkhkNkQ3WHU2Q2Qyd0RWamxDT0ZMV20yN1JxenJ3RHNkb0VxMzJSdyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8vY3Btc2QxNi5vbmxpbmUvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1790103362),
('VqN4gJeJKYGp2bnn66x9zdW7arWM59hNgotzEL6u', NULL, '193.182.19.204', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36 Viewer/99.9.8853.8', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicHd3ODczOHNlS1JRd3dZZ0g2SXJIYTFZaTl0NUd3Z25kdUx2YVZJViI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9jcG1zZDE2Lm9ubGluZSI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1790110076),
('vxXT9BAoubTn9gDosKXF9NViK06TOPV89l5q6U9J', NULL, '54.242.112.133', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7_8 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicUdESWFTazJ2aUszc2hPNk5RcFdNR0dOaHdPaG9pUVhHTUp3STJraSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjI6Imh0dHBzOi8vY3Btc2QxNi5vbmxpbmUiO3M6NToicm91dGUiO047fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1790101867),
('yAystqQhe0XBSyA0TC24eMFuj1YCDjaz0kjx7UY0', NULL, '43.164.190.28', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVmRZU01NemlBSTZEbEpNUDZ4OEVWQzlwdjg3R211V2FSU3JRNW4ycCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9jcG1zZDE2Lm9ubGluZS9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1790109610),
('yyPu3L4mYnzTjiabYpEO1h5UW6VIk9QE9UDyydFx', NULL, '54.162.26.144', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7_8 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQVRoczJ1NjdhcGlTVnBOUGs4ZFl6RFViMnR3VkRZNHZ6Rlh0NVpxeiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8vY3Btc2QxNi5vbmxpbmUvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1790103312),
('Ze8zu5KjxcWxLHMAttXIQNyLkHFqeHD1SsafPrJ5', NULL, '49.234.192.248', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibkY4RDAwQ3RVU0hHMTNOaXMyVkNqSUNwakltOTROcERrOXNwd2FrQyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly93d3cuY3Btc2QxNi5vbmxpbmUvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1790088954),
('zvNVygWYyWssN5r5q6EIvCkgpsQEPjzIf3kDaSFw', NULL, '207.46.13.153', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/116.0.1938.76 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZTV6NXpEVmppY1pZcXRmRjZYbE84bmZvaWJJenBPRlM4T1diNHdmaSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8vY3Btc2QxNi5vbmxpbmUvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1790105539),
('ZXHmICeW8dbDYjJGrvgjtuQodCPOt3IyYW1jXeDO', NULL, '49.51.141.76', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicUZXeXdtMTVzYmJ4amJKSDBxRXZUSFdadTYxYmc0TUdOdzcwQ3hNOSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9jcG1zZDE2Lm9ubGluZS9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1790130268);

-- --------------------------------------------------------

--
-- Table structure for table `telegram_profiles`
--

CREATE TABLE `telegram_profiles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `telegram_user_id` bigint(20) UNSIGNED NOT NULL,
  `telegram_chat_id` bigint(20) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `language_code` varchar(10) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `is_bot` tinyint(1) NOT NULL DEFAULT 0,
  `default_branch_id` int(10) UNSIGNED DEFAULT NULL,
  `default_room_number` varchar(50) DEFAULT NULL,
  `is_profile_complete` tinyint(1) NOT NULL DEFAULT 0,
  `notifications_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `contact_consent` tinyint(1) NOT NULL DEFAULT 0,
  `is_blocked` tinyint(1) NOT NULL DEFAULT 0,
  `last_interaction_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `telegram_profiles`
--

INSERT INTO `telegram_profiles` (`id`, `telegram_user_id`, `telegram_chat_id`, `username`, `first_name`, `last_name`, `language_code`, `phone`, `is_bot`, `default_branch_id`, `default_room_number`, `is_profile_complete`, `notifications_enabled`, `contact_consent`, `is_blocked`, `last_interaction_at`, `created_at`, `updated_at`) VALUES
(1, 330489980, 330489980, 'metamorf_dev', 'My Names', NULL, 'ru', NULL, 0, 1, '27', 1, 1, 0, 0, '2026-08-27 07:26:18', '2026-08-26 16:01:39', '2026-08-27 07:26:18'),
(2, 8490356310, 8490356310, 'sergio_devs', 'Serhii', NULL, 'ru', '380733417595', 0, 1, '27', 1, 1, 1, 0, '2026-08-26 17:32:05', '2026-08-26 17:30:28', '2026-08-26 17:32:05'),
(3, 8608703259, 8608703259, NULL, 'Shayan', 'Mustafa', 'en', '923356251910', 0, NULL, NULL, 0, 1, 1, 0, '2026-08-29 18:54:18', '2026-08-29 18:53:51', '2026-08-29 18:54:18'),
(4, 8855225256, 8855225256, 'ahmed10601', 'Ahmed', NULL, 'en', NULL, 0, NULL, NULL, 0, 1, 0, 0, '2026-08-30 14:09:24', '2026-08-30 14:09:24', '2026-08-30 14:09:24'),
(5, 7929040209, 7929040209, NULL, 'Tanusha', 'Tanusha', 'ru', NULL, 0, 2, '306', 1, 1, 0, 0, '2026-09-03 04:19:12', '2026-09-03 04:18:40', '2026-09-03 04:19:12'),
(6, 1149121695, 1149121695, 'moegypt2023', 'EGY', 'CPM', 'en', NULL, 0, NULL, NULL, 0, 1, 0, 0, '2026-09-05 19:15:48', '2026-09-05 19:15:22', '2026-09-05 19:15:48'),
(7, 8958347958, 8958347958, NULL, 'Zx', 'Syed', 'en', NULL, 0, 2, 'Talk in English', 1, 1, 0, 0, '2026-09-06 08:42:04', '2026-09-06 06:43:08', '2026-09-06 08:42:04'),
(8, 923722881, 923722881, 'Elena65005', 'Елена', NULL, 'ru', NULL, 0, 2, '306', 1, 1, 0, 0, '2026-09-07 07:52:35', '2026-09-07 07:51:11', '2026-09-07 07:52:35'),
(9, 906856609, 906856609, 'MilaMoon_G', 'Lyudmila ♉️', NULL, 'uk', '380988104036', 0, 1, '9', 1, 1, 1, 0, '2026-09-14 16:04:40', '2026-09-14 06:52:23', '2026-09-14 16:04:40'),
(10, 464357703, 464357703, 'daria_hirzheu', 'Дар\'я', 'Гіржеу', 'ru', NULL, 0, 5, '202', 1, 1, 0, 0, '2026-09-17 07:07:12', '2026-09-17 07:06:40', '2026-09-17 07:07:12'),
(11, 7646151667, 7646151667, NULL, 'Melo', NULL, 'tr', NULL, 0, NULL, NULL, 0, 1, 0, 0, '2026-09-20 15:14:54', '2026-09-20 15:03:48', '2026-09-20 15:14:54'),
(12, 1269824605, 1269824605, NULL, 'Наталья', 'Кацубо', 'ru', NULL, 0, 1, '7a', 1, 1, 0, 0, '2026-09-21 07:03:02', '2026-09-21 05:46:57', '2026-09-21 07:03:02'),
(13, 1748926034, 1748926034, NULL, 'Александр', NULL, 'ru', '380963681039', 0, 1, '5y', 1, 1, 1, 0, '2026-09-21 06:42:45', '2026-09-21 06:41:01', '2026-09-21 06:42:46'),
(14, 495335120, 495335120, NULL, 'Елена', NULL, 'ru', '380959397328', 0, 4, '103', 1, 1, 1, 0, '2026-09-22 08:02:06', '2026-09-22 08:01:19', '2026-09-22 08:02:06'),
(15, 6942033051, 6942033051, 'AS_DENON170', 'AS_DEMON170', NULL, 'en', NULL, 0, NULL, NULL, 0, 1, 0, 0, '2026-09-22 19:25:43', '2026-09-22 19:25:43', '2026-09-22 19:25:43');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `telegram_id` bigint(20) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role` enum('admin','warehouse_manager','warehouse_keeper','director') DEFAULT 'warehouse_keeper'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `telegram_id`, `is_active`, `remember_token`, `created_at`, `updated_at`, `role`) VALUES
(1, 'Serhii', 'admin@localhost', '2025-08-01 22:58:20', '$2y$12$6y2sbFZ5oWmlo34k83dLGuXDaGXcvCoOc1AIRfhPxX/IJnBIZ.hnS', 330489980, 1, 'AnERqpQc7YU8mb6cMDY04M9b5uLUS1G0nsikviFDmoZMTKRyTq374YvOdjid', '2025-08-01 22:58:20', '2025-09-22 07:56:35', 'admin'),
(2, 'Директор поликлиники', 'director@localhost', '2025-08-01 22:58:20', '$2y$12$7djSlQnjuc5QHsjWSbh6le6AX1cj/clh6Pqro9NdK.qmfj4JLn8Ea', NULL, 1, 'WQybCE5DjBGRV6MFjU7sF2yti3ylqgjr6VTQoQScLZAYbRypnvGm4mNrSdK1', '2025-08-01 22:58:20', '2025-08-01 22:58:20', 'director'),
(3, 'Нач. Складу', 'warehouse@localhost', '2025-09-22 08:17:54', '$2y$12$OtGMBKrXfNF70iq8qt9FV.Ehh6nPXkqHrNwbrSGQWKLPzcQMAnsqy', NULL, 1, '7ycT2WhWPwqbNMYT10JP73cbtku6XB0Y3ZSoFLZUVkDdsPAerolWdgFDhqZe', '2025-09-22 08:17:54', '2025-09-22 11:12:01', 'warehouse_keeper'),
(6, 'asdasd', 'escanadem@gmail.com', NULL, '$2y$12$iyDJ/zVkBF9a4wSsESJDfum0HPMALg3kpAj.e5lgiVp9RPLqluUou', NULL, 1, NULL, '2026-06-16 06:59:28', '2026-06-16 06:59:28', 'warehouse_keeper');

-- --------------------------------------------------------

--
-- Table structure for table `user_states`
--

CREATE TABLE `user_states` (
  `telegram_id` bigint(20) NOT NULL,
  `current_state` varchar(100) DEFAULT NULL,
  `temp_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `warehouse_inventories`
--

CREATE TABLE `warehouse_inventories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inventory_number` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `inventory_date` date NOT NULL,
  `status` enum('in_progress','completed') NOT NULL DEFAULT 'in_progress',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `warehouse_inventory_items`
--

CREATE TABLE `warehouse_inventory_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `system_quantity` int(11) NOT NULL,
  `actual_quantity` int(11) NOT NULL,
  `difference` int(11) NOT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `warehouse_items`
--

CREATE TABLE `warehouse_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `unit` varchar(255) NOT NULL DEFAULT 'шт',
  `quantity` int(11) NOT NULL DEFAULT 0,
  `min_quantity` int(11) NOT NULL DEFAULT 0,
  `price` decimal(10,2) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `warehouse_movements`
--

CREATE TABLE `warehouse_movements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `inventory_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('receipt','issue','writeoff','inventory','transfer') NOT NULL,
  `quantity` int(11) NOT NULL,
  `balance_after` int(11) NOT NULL,
  `note` text DEFAULT NULL,
  `document_number` varchar(255) DEFAULT NULL,
  `issued_to_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `operation_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `warehouse_movements`
--

INSERT INTO `warehouse_movements` (`id`, `user_id`, `inventory_id`, `type`, `quantity`, `balance_after`, `note`, `document_number`, `issued_to_user_id`, `operation_date`, `created_at`, `updated_at`) VALUES
(9, 1, 1740, 'transfer', 1, 1, 'Переміщення: Склад → Склад', NULL, NULL, '2025-12-16', '2025-12-16 10:20:29', '2025-12-16 10:20:29'),
(10, 1, 1740, 'transfer', 1, 1, 'Переміщення: Склад → Підвал', NULL, NULL, '2025-12-16', '2025-12-16 11:51:25', '2025-12-16 11:51:25'),
(11, 1, 1646, 'issue', -1, 0, ' (Видано: Фелонюк)', NULL, NULL, '2025-12-16', '2025-12-16 12:28:43', '2025-12-16 12:28:43'),
(12, 1, 194, 'transfer', 1, 1, 'Переміщення: 327 → 308', NULL, NULL, '2025-12-17', '2025-12-17 08:46:14', '2025-12-17 08:46:14'),
(13, 1, 196, 'transfer', 1, 1, 'Переміщення: 327 → 308', NULL, NULL, '2025-12-17', '2025-12-17 08:46:24', '2025-12-17 08:46:24'),
(14, 1, 197, 'transfer', 1, 1, 'Переміщення: 327 → 308', NULL, NULL, '2025-12-17', '2025-12-17 08:46:32', '2025-12-17 08:46:32'),
(15, 3, 1647, 'issue', 1, 0, ' | Кому: Фелонюк', NULL, NULL, '2025-12-17', '2025-12-17 08:52:24', '2025-12-17 08:52:24'),
(16, 3, 1646, 'receipt', 1, 1, NULL, 'Фелонюк', NULL, '2025-12-17', '2025-12-17 08:53:47', '2025-12-17 08:53:47'),
(17, 1, 1646, 'issue', 1, 0, ' | Кому: Фелонюк', NULL, NULL, '2025-12-22', '2025-12-22 08:05:19', '2025-12-22 08:05:19'),
(18, 3, 1784, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2025-12-26', '2025-12-26 12:02:53', '2025-12-26 12:02:53'),
(19, 3, 1785, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2025-12-29', '2025-12-29 09:30:41', '2025-12-29 09:30:41'),
(20, 3, 1786, 'receipt', 2, 2, 'Початковий залишок', NULL, NULL, '2025-12-29', '2025-12-29 09:38:35', '2025-12-29 09:38:35'),
(21, 3, 1784, 'issue', 1, 0, ' | Кому: Списання', NULL, NULL, '2025-12-29', '2025-12-29 09:41:22', '2025-12-29 09:41:22'),
(22, 3, 1788, 'receipt', 2, 2, 'Початковий залишок', NULL, NULL, '2025-12-30', '2025-12-30 06:52:44', '2025-12-30 06:52:44'),
(23, 3, 1788, 'issue', 1, 1, ' | Кому: 45/3', NULL, NULL, '2025-12-30', '2025-12-30 06:53:43', '2025-12-30 06:53:43'),
(24, 3, 1790, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2025-12-31', '2025-12-31 05:58:57', '2025-12-31 05:58:57'),
(25, 3, 1791, 'receipt', 3, 3, 'Початковий залишок', NULL, NULL, '2025-12-31', '2025-12-31 06:39:48', '2025-12-31 06:39:48'),
(26, 3, 1792, 'receipt', 2, 2, 'Початковий залишок', NULL, NULL, '2025-12-31', '2025-12-31 06:57:18', '2025-12-31 06:57:18'),
(27, 3, 1725, 'issue', 1, 0, ' | Кому: к7 Пилипенко', NULL, NULL, '2026-01-06', '2026-01-06 10:58:45', '2026-01-06 10:58:45'),
(28, 1, 1795, 'receipt', 2, 2, 'Початковий залишок', NULL, NULL, '2026-01-07', '2026-01-07 10:27:17', '2026-01-07 10:27:17'),
(29, 1, 1798, 'receipt', 4, 4, 'Початковий залишок', NULL, NULL, '2026-01-07', '2026-01-07 10:37:38', '2026-01-07 10:37:38'),
(30, 1, 1799, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-01-07', '2026-01-07 10:38:58', '2026-01-07 10:38:58'),
(31, 1, 1800, 'receipt', 3, 3, 'Початковий залишок', NULL, NULL, '2026-01-07', '2026-01-07 10:40:49', '2026-01-07 10:40:49'),
(32, 3, 1788, 'issue', 1, 0, ' | Кому: Болгарська каб.302', NULL, NULL, '2026-01-09', '2026-01-09 08:12:06', '2026-01-09 08:12:06'),
(33, 1, 833, 'transfer', 1, 1, 'Переміщення: 35 → Підвал', NULL, NULL, '2026-01-16', '2026-01-16 06:53:33', '2026-01-16 06:53:33'),
(34, 1, 834, 'transfer', 1, 1, 'Переміщення: 35 → Підвал', NULL, NULL, '2026-01-16', '2026-01-16 06:54:15', '2026-01-16 06:54:15'),
(35, 1, 835, 'transfer', 1, 1, 'Переміщення: 35 → Підвал', NULL, NULL, '2026-01-16', '2026-01-16 06:54:23', '2026-01-16 06:54:23'),
(36, 1, 836, 'transfer', 1, 1, 'Переміщення: 35 → Підвал', NULL, NULL, '2026-01-16', '2026-01-16 06:54:30', '2026-01-16 06:54:30'),
(37, 1, 837, 'transfer', 1, 1, 'Переміщення: 35 → Підвал', NULL, NULL, '2026-01-16', '2026-01-16 06:54:38', '2026-01-16 06:54:38'),
(38, 1, 838, 'transfer', 1, 1, 'Переміщення: 35 → Підвал', NULL, NULL, '2026-01-16', '2026-01-16 06:54:46', '2026-01-16 06:54:46'),
(39, 1, 839, 'transfer', 1, 1, 'Переміщення: 35 → Підвал', NULL, NULL, '2026-01-16', '2026-01-16 06:54:54', '2026-01-16 06:54:54'),
(40, 1, 840, 'transfer', 1, 1, 'Переміщення: 35 → Підвал', NULL, NULL, '2026-01-16', '2026-01-16 06:55:03', '2026-01-16 06:55:03'),
(41, 1, 841, 'transfer', 1, 1, 'Переміщення: 35 → Підвал', NULL, NULL, '2026-01-16', '2026-01-16 06:55:13', '2026-01-16 06:55:13'),
(42, 1, 842, 'transfer', 1, 1, 'Переміщення: 35 → Підвал', NULL, NULL, '2026-01-16', '2026-01-16 06:55:33', '2026-01-16 06:55:33'),
(43, 1, 843, 'transfer', 1, 1, 'Переміщення: 35 → Підвал', NULL, NULL, '2026-01-16', '2026-01-16 06:55:43', '2026-01-16 06:55:43'),
(44, 1, 844, 'transfer', 1, 1, 'Переміщення: 35 → Підвал', NULL, NULL, '2026-01-16', '2026-01-16 06:55:51', '2026-01-16 06:55:51'),
(45, 1, 1735, 'transfer', 1, 1, 'Переміщення: 23 → 35', NULL, NULL, '2026-01-16', '2026-01-16 06:56:45', '2026-01-16 06:56:45'),
(46, 1, 672, 'transfer', 1, 1, 'Переміщення: 23 → 35', NULL, NULL, '2026-01-16', '2026-01-16 06:56:51', '2026-01-16 06:56:51'),
(47, 1, 674, 'transfer', 1, 1, 'Переміщення: 23 → 35', NULL, NULL, '2026-01-16', '2026-01-16 06:56:57', '2026-01-16 06:56:57'),
(48, 1, 675, 'transfer', 1, 1, 'Переміщення: 23 → 35', NULL, NULL, '2026-01-16', '2026-01-16 06:57:04', '2026-01-16 06:57:04'),
(49, 1, 676, 'transfer', 1, 1, 'Переміщення: 23 → 35', NULL, NULL, '2026-01-16', '2026-01-16 06:57:13', '2026-01-16 06:57:13'),
(50, 1, 677, 'transfer', 1, 1, 'Переміщення: 23 → 35', NULL, NULL, '2026-01-16', '2026-01-16 06:57:22', '2026-01-16 06:57:22'),
(51, 1, 678, 'transfer', 1, 1, 'Переміщення: 23 → 35', NULL, NULL, '2026-01-16', '2026-01-16 06:57:28', '2026-01-16 06:57:28'),
(52, 1, 679, 'transfer', 1, 1, 'Переміщення: 23 → 35', NULL, NULL, '2026-01-16', '2026-01-16 06:57:34', '2026-01-16 06:57:34'),
(53, 1, 680, 'transfer', 1, 1, 'Переміщення: 23 → 35', NULL, NULL, '2026-01-16', '2026-01-16 07:01:17', '2026-01-16 07:01:17'),
(54, 1, 681, 'transfer', 1, 1, 'Переміщення: 23 → 35', NULL, NULL, '2026-01-16', '2026-01-16 07:01:25', '2026-01-16 07:01:25'),
(55, 1, 1834, 'receipt', 3, 3, 'Початковий залишок', NULL, NULL, '2026-01-16', '2026-01-16 11:30:18', '2026-01-16 11:30:18'),
(56, 1, 1835, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-01-16', '2026-01-16 11:31:10', '2026-01-16 11:31:10'),
(57, 1, 881, 'transfer', 1, 1, 'Переміщення: 40 → Підвал (Вийшов з ладу процесор\\МП)', NULL, NULL, '2026-01-22', '2026-01-22 09:18:34', '2026-01-22 09:18:34'),
(58, 3, 1837, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-02-09', '2026-02-09 10:47:22', '2026-02-09 10:47:22'),
(59, 3, 1838, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-02-09', '2026-02-09 10:47:35', '2026-02-09 10:47:35'),
(60, 3, 1839, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-02-09', '2026-02-09 10:47:45', '2026-02-09 10:47:45'),
(61, 3, 1840, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-02-09', '2026-02-09 10:49:35', '2026-02-09 10:49:35'),
(62, 1, 1659, 'issue', 1, 0, ' | Кому: Фелонюк', NULL, NULL, '2026-02-18', '2026-02-18 08:37:16', '2026-02-18 08:37:16'),
(63, 1, 54, 'transfer', 1, 1, 'Переміщення: Склад → 46', NULL, NULL, '2026-01-15', '2026-03-06 06:24:48', '2026-03-06 06:24:48'),
(64, 1, 1657, 'issue', 1, 0, ' | Кому: Фелонюк', NULL, NULL, '2026-03-09', '2026-03-09 06:25:57', '2026-03-09 06:25:57'),
(65, 1, 1788, 'receipt', 10, 10, 'пишалко', '945', NULL, '2026-03-10', '2026-03-10 12:05:29', '2026-03-10 12:05:29'),
(66, 1, 1843, 'receipt', 15, 15, '3 штуки давали авансом\r\n12 приїхали разом \r\nПИШАЛКО', '945', NULL, '2026-03-10', '2026-03-10 12:07:36', '2026-03-10 12:07:36'),
(67, 1, 1843, 'issue', 1, 14, ' | Кому: К23', NULL, NULL, '2026-03-10', '2026-03-10 12:07:55', '2026-03-10 12:07:55'),
(68, 1, 1843, 'issue', 1, 13, ' | Кому: к28', NULL, NULL, '2026-03-11', '2026-03-11 09:01:04', '2026-03-11 09:01:04'),
(69, 1, 1843, 'issue', 1, 12, ' | Кому: Гирченко к16', NULL, NULL, '2026-03-12', '2026-03-12 07:28:36', '2026-03-12 07:28:36'),
(70, 1, 1843, 'issue', 1, 11, ' | Кому: Болгарская 38, К302 Пашаева + Чумаченко', NULL, NULL, '2026-03-12', '2026-03-12 10:09:37', '2026-03-12 10:09:37'),
(71, 1, 1844, 'receipt', 17, 17, 'Оприходовано з заявки ZAY-2026-000020', 'ZAY-2026-000020', NULL, '2026-03-13', '2026-03-13 07:19:43', '2026-03-13 07:19:43'),
(72, 1, 1823, 'receipt', 30, 30, 'Оприходовано з заявки ZAY-2026-000020', 'ZAY-2026-000020', NULL, '2026-03-13', '2026-03-13 07:19:43', '2026-03-13 07:19:43'),
(73, 1, 1845, 'receipt', 17, 17, 'Оприходовано з заявки ZAY-2026-000020', 'ZAY-2026-000020', NULL, '2026-03-13', '2026-03-13 07:19:43', '2026-03-13 07:19:43'),
(74, 3, 1786, 'receipt', 6, 8, 'Оприходовано з заявки ZAY-2026-000017', 'ZAY-2026-000017', NULL, '2026-03-13', '2026-03-13 08:43:53', '2026-03-13 08:43:53'),
(75, 3, 1784, 'receipt', 8, 8, 'Оприходовано з заявки ZAY-2026-000017', 'ZAY-2026-000017', NULL, '2026-03-13', '2026-03-13 08:43:53', '2026-03-13 08:43:53'),
(76, 3, 1787, 'receipt', 8, 8, 'Оприходовано з заявки ZAY-2026-000017', 'ZAY-2026-000017', NULL, '2026-03-13', '2026-03-13 08:43:53', '2026-03-13 08:43:53'),
(77, 3, 1789, 'receipt', 6, 6, 'Оприходовано з заявки ZAY-2026-000017', 'ZAY-2026-000017', NULL, '2026-03-13', '2026-03-13 08:43:53', '2026-03-13 08:43:53'),
(78, 3, 1790, 'receipt', 6, 7, 'Оприходовано з заявки ZAY-2026-000017', 'ZAY-2026-000017', NULL, '2026-03-13', '2026-03-13 08:43:53', '2026-03-13 08:43:53'),
(79, 3, 1795, 'receipt', 6, 8, 'Оприходовано з заявки ZAY-2026-000017', 'ZAY-2026-000017', NULL, '2026-03-13', '2026-03-13 08:43:53', '2026-03-13 08:43:53'),
(80, 3, 1793, 'receipt', 30, 30, 'Оприходовано з заявки ZAY-2026-000017', 'ZAY-2026-000017', NULL, '2026-03-13', '2026-03-13 08:43:53', '2026-03-13 08:43:53'),
(81, 3, 1799, 'receipt', 2, 3, 'Оприходовано з заявки ZAY-2026-000017', 'ZAY-2026-000017', NULL, '2026-03-13', '2026-03-13 08:43:53', '2026-03-13 08:43:53'),
(82, 3, 1800, 'receipt', 2, 5, 'Оприходовано з заявки ZAY-2026-000017', 'ZAY-2026-000017', NULL, '2026-03-13', '2026-03-13 08:43:53', '2026-03-13 08:43:53'),
(83, 3, 1791, 'receipt', 4, 7, 'Оприходовано з заявки ZAY-2026-000017', 'ZAY-2026-000017', NULL, '2026-03-13', '2026-03-13 08:43:53', '2026-03-13 08:43:53'),
(84, 3, 1848, 'receipt', 2, 2, 'Оприходовано з заявки ZAY-2026-000017', 'ZAY-2026-000017', NULL, '2026-03-13', '2026-03-13 08:43:53', '2026-03-13 08:43:53'),
(85, 3, 1799, 'issue', -1, 2, 'Попередні видачі (Видано: Гриненко)', NULL, NULL, '2026-03-13', '2026-03-13 08:51:00', '2026-03-13 08:51:00'),
(86, 3, 1799, 'issue', -1, 1, 'Липи 1. (Видано: Санітарки)', NULL, NULL, '2026-03-13', '2026-03-13 08:51:33', '2026-03-13 08:51:33'),
(87, 3, 1843, 'issue', 1, 10, ' | Кому: K26', NULL, NULL, '2026-03-16', '2026-03-16 11:15:28', '2026-03-16 11:15:28'),
(88, 3, 1843, 'receipt', 30, 40, 'ФОП некрутенко\r\n30шт - 35400,00 грн', 'Накладна №240', NULL, '2026-03-16', '2026-03-16 11:16:19', '2026-03-16 11:16:19'),
(89, 3, 1835, 'receipt', 30, 31, 'Оприходовано з заявки ZAY-2026-000026', 'ZAY-2026-000026', NULL, '2026-03-16', '2026-03-16 11:50:37', '2026-03-16 11:50:37'),
(90, 3, 1803, 'receipt', 200, 200, 'Оприходовано з заявки ZAY-2026-000026', 'ZAY-2026-000026', NULL, '2026-03-16', '2026-03-16 11:50:37', '2026-03-16 11:50:37'),
(91, 3, 1834, 'receipt', 10, 13, 'Оприходовано з заявки ZAY-2026-000026', 'ZAY-2026-000026', NULL, '2026-03-16', '2026-03-16 11:50:37', '2026-03-16 11:50:37'),
(92, 3, 1831, 'receipt', 10, 10, 'Оприходовано з заявки ZAY-2026-000026', 'ZAY-2026-000026', NULL, '2026-03-16', '2026-03-16 11:50:37', '2026-03-16 11:50:37'),
(93, 3, 1832, 'receipt', 25, 25, 'Оприходовано з заявки ZAY-2026-000026', 'ZAY-2026-000026', NULL, '2026-03-16', '2026-03-16 11:50:37', '2026-03-16 11:50:37'),
(94, 3, 1804, 'receipt', 50, 50, 'Оприходовано з заявки ZAY-2026-000026', 'ZAY-2026-000026', NULL, '2026-03-16', '2026-03-16 11:50:37', '2026-03-16 11:50:37'),
(95, 3, 1805, 'receipt', 50, 50, 'Оприходовано з заявки ZAY-2026-000026', 'ZAY-2026-000026', NULL, '2026-03-16', '2026-03-16 11:50:37', '2026-03-16 11:50:37'),
(96, 3, 1833, 'receipt', 200, 200, 'Оприходовано з заявки ZAY-2026-000026', 'ZAY-2026-000026', NULL, '2026-03-16', '2026-03-16 11:50:37', '2026-03-16 11:50:37'),
(97, 1, 1843, 'issue', -2, 38, ' (Видано: Фесенко к210 беззабарна)', NULL, NULL, '2026-03-17', '2026-03-17 06:05:47', '2026-03-17 06:05:47'),
(98, 1, 1843, 'issue', -1, 37, ' (Видано: Гаркавого 2, к231 Лазуренко)', NULL, NULL, '2026-03-17', '2026-03-17 06:07:41', '2026-03-17 06:07:41'),
(99, 1, 1843, 'issue', -1, 36, ' (Видано: К7а арнаут+жарук)', NULL, NULL, '2026-03-17', '2026-03-17 06:12:56', '2026-03-17 06:12:56'),
(100, 1, 1843, 'issue', 1, 35, ' | Кому: Золота Таісія к58', NULL, NULL, '2026-03-19', '2026-03-19 08:53:25', '2026-03-19 08:53:25'),
(101, 1, 1843, 'issue', 1, 34, ' | Кому: Болгарська 38, Князєва к303', NULL, NULL, '2026-03-19', '2026-03-19 08:54:41', '2026-03-19 08:54:41'),
(102, 1, 1849, 'receipt', 3, 3, 'Початковий залишок', NULL, NULL, '2026-03-20', '2026-03-20 07:36:08', '2026-03-20 07:36:08'),
(103, 1, 1843, 'issue', 1, 33, ' | Кому: каб,№36', NULL, NULL, '2026-03-20', '2026-03-20 11:17:40', '2026-03-20 11:17:40'),
(104, 1, 1790, 'issue', -2, 5, ' (Видано: амб,6-10)', NULL, NULL, '2026-03-20', '2026-03-20 11:38:11', '2026-03-20 11:38:11'),
(105, 1, 1790, 'issue', -2, 3, ' (Видано: амб1-5)', NULL, NULL, '2026-03-20', '2026-03-20 11:39:17', '2026-03-20 11:39:17'),
(106, 1, 1793, 'issue', 4, 26, ' | Кому: амб,1-5', NULL, NULL, '2026-03-20', '2026-03-20 11:49:18', '2026-03-20 11:49:18'),
(107, 1, 1793, 'issue', 8, 18, ' | Кому: амб,6-10', NULL, NULL, '2026-03-20', '2026-03-20 11:50:51', '2026-03-20 11:50:51'),
(108, 1, 1843, 'issue', 2, 31, ' | Кому: К52 глав.Бух. Комарова', NULL, NULL, '2026-03-24', '2026-03-24 09:28:04', '2026-03-24 09:28:04'),
(109, 1, 1843, 'issue', 1, 30, ' | Кому: К46 Кадры', NULL, NULL, '2026-03-24', '2026-03-24 09:28:55', '2026-03-24 09:28:55'),
(110, 1, 330, 'transfer', 1, 1, 'Переміщення: Підвал → 13 (Для нових гінекологів)', NULL, NULL, '2026-03-24', '2026-03-24 09:48:40', '2026-03-24 09:48:40'),
(111, 1, 1658, 'transfer', 1, 1, 'Переміщення: Підвал → 13 (Для нових гінекологів)', NULL, NULL, '2026-03-24', '2026-03-24 09:49:15', '2026-03-24 09:49:15'),
(112, 1, 1685, 'transfer', 1, 1, 'Переміщення: Підвал → 13 (Для нових гінекологів)', NULL, NULL, '2026-03-24', '2026-03-24 09:49:33', '2026-03-24 09:49:33'),
(113, 1, 1687, 'transfer', 1, 1, 'Переміщення: Підвал → 23 (Скринінг 40+)', NULL, NULL, '2026-03-24', '2026-03-24 09:50:05', '2026-03-24 09:50:05'),
(114, 1, 1726, 'issue', 1, 0, ' | Кому: к13', NULL, NULL, '2026-03-24', '2026-03-24 11:13:59', '2026-03-24 11:13:59'),
(115, 3, 1843, 'issue', 1, 29, ' | Кому: К51', NULL, NULL, '2026-03-25', '2026-03-25 10:13:16', '2026-03-25 10:13:16'),
(116, 3, 1843, 'issue', 1, 28, ' | Кому: Бугаївська к101', NULL, NULL, '2026-03-25', '2026-03-25 10:15:04', '2026-03-25 10:15:04'),
(117, 1, 1843, 'issue', 1, 27, ' | Кому: к45\\5', NULL, NULL, '2026-03-27', '2026-03-27 12:35:03', '2026-03-27 12:35:03'),
(118, 1, 518, 'transfer', 1, 1, 'Переміщення: 22 → 45/4', NULL, NULL, '2026-03-31', '2026-03-31 05:42:24', '2026-03-31 05:42:24'),
(119, 1, 1843, 'issue', 1, 26, ' | Кому: К49', NULL, NULL, '2026-03-31', '2026-03-31 06:14:05', '2026-03-31 06:14:05'),
(120, 1, 1843, 'issue', 1, 25, ' | Кому: к45/4', NULL, NULL, '2026-03-31', '2026-03-31 06:14:20', '2026-03-31 06:14:20'),
(121, 1, 54, 'transfer', 1, 1, 'Переміщення: 46 → Підвал', NULL, NULL, '2026-03-31', '2026-03-31 06:22:34', '2026-03-31 06:22:34'),
(122, 1, 1852, 'receipt', 8, 8, 'Оприходовано з заявки ZAY-2026-000033', 'ZAY-2026-000033', NULL, '2026-04-01', '2026-04-01 05:28:34', '2026-04-01 05:28:34'),
(123, 1, 1853, 'receipt', 2, 2, 'Оприходовано з заявки ZAY-2026-000033', 'ZAY-2026-000033', NULL, '2026-04-01', '2026-04-01 05:28:34', '2026-04-01 05:28:34'),
(124, 1, 1854, 'receipt', 5, 5, 'Оприходовано з заявки ZAY-2026-000033', 'ZAY-2026-000033', NULL, '2026-04-01', '2026-04-01 05:28:34', '2026-04-01 05:28:34'),
(125, 1, 1855, 'receipt', 2, 2, 'Оприходовано з заявки ZAY-2026-000033', 'ZAY-2026-000033', NULL, '2026-04-01', '2026-04-01 05:28:34', '2026-04-01 05:28:34'),
(126, 1, 1856, 'receipt', 2, 2, 'Оприходовано з заявки ZAY-2026-000033', 'ZAY-2026-000033', NULL, '2026-04-01', '2026-04-01 05:28:34', '2026-04-01 05:28:34'),
(127, 1, 1857, 'receipt', 10, 10, 'Оприходовано з заявки ZAY-2026-000033', 'ZAY-2026-000033', NULL, '2026-04-01', '2026-04-01 05:28:34', '2026-04-01 05:28:34'),
(128, 1, 1858, 'receipt', 30, 30, 'Оприходовано з заявки ZAY-2026-000033', 'ZAY-2026-000033', NULL, '2026-04-01', '2026-04-01 05:28:34', '2026-04-01 05:28:34'),
(129, 1, 1859, 'receipt', 3, 3, 'Оприходовано з заявки ZAY-2026-000033', 'ZAY-2026-000033', NULL, '2026-04-01', '2026-04-01 05:28:34', '2026-04-01 05:28:34'),
(130, 1, 1860, 'receipt', 4, 4, 'Оприходовано з заявки ZAY-2026-000033', 'ZAY-2026-000033', NULL, '2026-04-01', '2026-04-01 05:28:34', '2026-04-01 05:28:34'),
(131, 1, 1861, 'receipt', 4, 4, 'Оприходовано з заявки ZAY-2026-000033', 'ZAY-2026-000033', NULL, '2026-04-01', '2026-04-01 05:28:34', '2026-04-01 05:28:34'),
(132, 1, 1862, 'receipt', 2, 2, 'Оприходовано з заявки ZAY-2026-000033', 'ZAY-2026-000033', NULL, '2026-04-01', '2026-04-01 05:28:34', '2026-04-01 05:28:34'),
(133, 1, 1863, 'receipt', 2, 2, 'Оприходовано з заявки ZAY-2026-000033', 'ZAY-2026-000033', NULL, '2026-04-01', '2026-04-01 05:28:34', '2026-04-01 05:28:34'),
(134, 1, 1864, 'receipt', 6, 6, 'Оприходовано з заявки ZAY-2026-000033', 'ZAY-2026-000033', NULL, '2026-04-01', '2026-04-01 05:28:34', '2026-04-01 05:28:34'),
(135, 1, 1865, 'receipt', 10, 10, 'Оприходовано з заявки ZAY-2026-000033', 'ZAY-2026-000033', NULL, '2026-04-01', '2026-04-01 05:28:34', '2026-04-01 05:28:34'),
(136, 1, 1866, 'receipt', 5, 5, 'Оприходовано з заявки ZAY-2026-000033', 'ZAY-2026-000033', NULL, '2026-04-01', '2026-04-01 05:28:34', '2026-04-01 05:28:34'),
(137, 1, 1867, 'receipt', 5, 5, 'Оприходовано з заявки ZAY-2026-000033', 'ZAY-2026-000033', NULL, '2026-04-01', '2026-04-01 05:28:34', '2026-04-01 05:28:34'),
(138, 1, 1868, 'receipt', 5, 5, 'Оприходовано з заявки ZAY-2026-000033', 'ZAY-2026-000033', NULL, '2026-04-01', '2026-04-01 05:28:34', '2026-04-01 05:28:34'),
(139, 1, 1869, 'receipt', 3, 3, 'Оприходовано з заявки ZAY-2026-000033', 'ZAY-2026-000033', NULL, '2026-04-01', '2026-04-01 05:28:34', '2026-04-01 05:28:34'),
(140, 1, 1870, 'receipt', 8, 8, 'Оприходовано з заявки ZAY-2026-000033', 'ZAY-2026-000033', NULL, '2026-04-01', '2026-04-01 05:28:34', '2026-04-01 05:28:34'),
(141, 1, 1871, 'receipt', 8, 8, 'Оприходовано з заявки ZAY-2026-000033', 'ZAY-2026-000033', NULL, '2026-04-01', '2026-04-01 05:28:34', '2026-04-01 05:28:34'),
(142, 1, 1872, 'receipt', 8, 8, 'Оприходовано з заявки ZAY-2026-000033', 'ZAY-2026-000033', NULL, '2026-04-01', '2026-04-01 05:28:34', '2026-04-01 05:28:34'),
(143, 1, 1873, 'receipt', 5, 5, 'Оприходовано з заявки ZAY-2026-000033', 'ZAY-2026-000033', NULL, '2026-04-01', '2026-04-01 05:28:34', '2026-04-01 05:28:34'),
(144, 1, 1874, 'receipt', 5, 5, 'Оприходовано з заявки ZAY-2026-000033', 'ZAY-2026-000033', NULL, '2026-04-01', '2026-04-01 05:28:34', '2026-04-01 05:28:34'),
(145, 1, 1843, 'issue', 1, 24, ' | Кому: к45\\2', NULL, NULL, '2026-04-01', '2026-04-01 06:26:17', '2026-04-01 06:26:17'),
(146, 1, 1843, 'issue', 1, 23, ' | Кому: Гирджеу житомирская', NULL, NULL, '2026-04-01', '2026-04-01 06:26:40', '2026-04-01 06:26:40'),
(147, 1, 1843, 'issue', 1, 22, ' | Кому: к203 Савов житомирская', NULL, NULL, '2026-04-01', '2026-04-01 06:27:00', '2026-04-01 06:27:00'),
(148, 1, 1786, 'issue', 1, 7, ' | Кому: амб,6-10', NULL, NULL, '2026-04-02', '2026-04-02 06:53:04', '2026-04-02 06:53:04'),
(149, 1, 1786, 'issue', 1, 6, ' | Кому: амб,1-5', NULL, NULL, '2026-04-02', '2026-04-02 08:13:32', '2026-04-02 08:13:32'),
(150, 1, 1786, 'issue', 2, 4, ' | Кому: амб,6-10', NULL, NULL, '2026-04-02', '2026-04-02 08:14:15', '2026-04-02 08:14:15'),
(151, 1, 1786, 'issue', 1, 3, ' | Кому: амб,1-5', NULL, NULL, '2026-04-02', '2026-04-02 08:14:43', '2026-04-02 08:14:43'),
(152, 1, 1784, 'issue', 1, 7, ' | Кому: амб,1-5', NULL, NULL, '2026-04-02', '2026-04-02 08:58:54', '2026-04-02 08:58:54'),
(153, 1, 1784, 'issue', 2, 5, ' | Кому: амб,6-10', NULL, NULL, '2026-04-02', '2026-04-02 08:59:37', '2026-04-02 08:59:37'),
(154, 1, 1784, 'issue', 2, 3, ' | Кому: амб,1-5', NULL, NULL, '2026-04-02', '2026-04-02 09:00:15', '2026-04-02 09:00:15'),
(155, 1, 1843, 'issue', 1, 21, ' | Кому: К102 бугаевская', NULL, NULL, '2026-04-02', '2026-04-02 09:02:46', '2026-04-02 09:02:46'),
(156, 1, 1811, 'receipt', 15, 15, 'Оприходовано з заявки ZAY-2026-000024', 'ZAY-2026-000024', NULL, '2026-04-03', '2026-04-03 03:58:41', '2026-04-03 03:58:41'),
(157, 1, 1810, 'receipt', 15, 15, 'Оприходовано з заявки ZAY-2026-000024', 'ZAY-2026-000024', NULL, '2026-04-03', '2026-04-03 03:58:41', '2026-04-03 03:58:41'),
(158, 1, 1809, 'receipt', 15, 15, 'Оприходовано з заявки ZAY-2026-000024', 'ZAY-2026-000024', NULL, '2026-04-03', '2026-04-03 03:58:41', '2026-04-03 03:58:41'),
(159, 1, 1812, 'receipt', 15, 15, 'Оприходовано з заявки ZAY-2026-000024', 'ZAY-2026-000024', NULL, '2026-04-03', '2026-04-03 03:58:41', '2026-04-03 03:58:41'),
(160, 1, 1814, 'receipt', 20, 20, 'Оприходовано з заявки ZAY-2026-000024', 'ZAY-2026-000024', NULL, '2026-04-03', '2026-04-03 03:58:41', '2026-04-03 03:58:41'),
(161, 1, 1816, 'receipt', 20, 20, 'Оприходовано з заявки ZAY-2026-000024', 'ZAY-2026-000024', NULL, '2026-04-03', '2026-04-03 03:58:41', '2026-04-03 03:58:41'),
(162, 1, 1817, 'receipt', 20, 20, 'Оприходовано з заявки ZAY-2026-000024', 'ZAY-2026-000024', NULL, '2026-04-03', '2026-04-03 03:58:41', '2026-04-03 03:58:41'),
(163, 1, 1818, 'receipt', 20, 20, 'Оприходовано з заявки ZAY-2026-000024', 'ZAY-2026-000024', NULL, '2026-04-03', '2026-04-03 03:58:41', '2026-04-03 03:58:41'),
(164, 1, 1843, 'issue', 1, 20, ' | Кому: к306 болгарская Борисюк', NULL, NULL, '2026-04-06', '2026-04-06 08:22:26', '2026-04-06 08:22:26'),
(165, 1, 1849, 'issue', 1, 2, ' | Кому: к52 Комарова', NULL, NULL, '2026-04-09', '2026-04-09 04:13:01', '2026-04-09 04:13:01'),
(166, 1, 1849, 'issue', 1, 1, ' | Кому: к39 Явтуховська', NULL, NULL, '2026-04-09', '2026-04-09 04:14:05', '2026-04-09 04:14:05'),
(167, 1, 1823, 'issue', 5, 25, ' | Кому: амб,6-10', NULL, NULL, '2026-04-09', '2026-04-09 05:10:29', '2026-04-09 05:10:29'),
(168, 1, 1823, 'issue', 4, 21, ' | Кому: амб,1-5', NULL, NULL, '2026-04-09', '2026-04-09 05:11:39', '2026-04-09 05:11:39'),
(169, 1, 1875, 'receipt', 10, 10, 'Оприходовано з заявки ZAY-2026-000034', 'ZAY-2026-000034', NULL, '2026-04-09', '2026-04-09 05:26:00', '2026-04-09 05:26:00'),
(170, 1, 1876, 'receipt', 15, 15, 'Оприходовано з заявки ZAY-2026-000034', 'ZAY-2026-000034', NULL, '2026-04-09', '2026-04-09 05:26:00', '2026-04-09 05:26:00'),
(171, 1, 1876, 'issue', 1, 14, ' | Кому: амб,6-10', NULL, NULL, '2026-04-09', '2026-04-09 05:26:45', '2026-04-09 05:26:45'),
(172, 1, 1795, 'issue', -2, 6, ' (Видано: амб,1-5)', NULL, NULL, '2026-04-09', '2026-04-09 09:59:24', '2026-04-09 09:59:24'),
(173, 1, 1795, 'issue', -2, 4, ' (Видано: амб,6-10)', NULL, NULL, '2026-04-09', '2026-04-09 09:59:49', '2026-04-09 09:59:49'),
(174, 1, 1795, 'issue', -2, 2, ' (Видано: амб,1-5)', NULL, NULL, '2026-04-09', '2026-04-09 10:00:10', '2026-04-09 10:00:10'),
(175, 1, 1803, 'issue', 142, 58, '17,03 амб,№6-10 50шт\r\n1,04  амб,№1-5   39шт,\r\n17,03-13,04  админ,  53шт, | Кому: --', NULL, NULL, '2026-04-13', '2026-04-13 05:05:16', '2026-04-13 05:05:16'),
(176, 1, 1793, 'issue', 2, 16, ' | Кому: амб,1-5', NULL, NULL, '2026-04-14', '2026-04-14 05:43:01', '2026-04-14 05:43:01'),
(177, 1, 1843, 'issue', 1, 19, ' | Кому: К216 житомирська', NULL, NULL, '2026-04-14', '2026-04-14 05:43:45', '2026-04-14 05:43:45'),
(178, 1, 1799, 'issue', 1, 0, ' | Кому: амб,1-5', NULL, NULL, '2026-04-14', '2026-04-14 05:44:33', '2026-04-14 05:44:33'),
(179, 1, 1790, 'issue', 1, 2, ' | Кому: амб,1-5', NULL, NULL, '2026-04-14', '2026-04-14 05:45:08', '2026-04-14 05:45:08'),
(180, 1, 1784, 'issue', 1, 2, ' | Кому: амб,1-5', NULL, NULL, '2026-04-14', '2026-04-14 05:46:32', '2026-04-14 05:46:32'),
(181, 1, 1791, 'issue', 2, 5, ' | Кому: амб,1-5', NULL, NULL, '2026-04-14', '2026-04-14 05:59:55', '2026-04-14 05:59:55'),
(182, 1, 1791, 'issue', 2, 3, ' | Кому: амб,6-10', NULL, NULL, '2026-04-14', '2026-04-14 06:00:18', '2026-04-14 06:00:18'),
(183, 1, 1848, 'issue', 1, 1, ' | Кому: амб,1-5', NULL, NULL, '2026-04-14', '2026-04-14 06:03:36', '2026-04-14 06:03:36'),
(184, 1, 1848, 'issue', 1, 0, ' | Кому: амб,6-10', NULL, NULL, '2026-04-14', '2026-04-14 06:04:05', '2026-04-14 06:04:05'),
(185, 1, 1877, 'receipt', 6, 6, 'Початковий залишок', NULL, NULL, '2026-04-14', '2026-04-14 07:48:25', '2026-04-14 07:48:25'),
(186, 1, 1877, 'issue', 2, 4, ' | Кому: амб,6-10', NULL, NULL, '2026-04-14', '2026-04-14 08:01:23', '2026-04-14 08:01:23'),
(187, 1, 1877, 'issue', 1, 3, ' | Кому: амб,1-5', NULL, NULL, '2026-04-14', '2026-04-14 08:01:50', '2026-04-14 08:01:50'),
(188, 1, 1798, 'issue', 1, 3, ' | Кому: амб,1-5', NULL, NULL, '2026-04-14', '2026-04-14 08:03:45', '2026-04-14 08:03:45'),
(189, 1, 1787, 'issue', 2, 6, ' | Кому: амб,6-10', NULL, NULL, '2026-04-14', '2026-04-14 08:50:53', '2026-04-14 08:50:53'),
(190, 1, 1787, 'issue', 2, 4, ' | Кому: амб,1-5', NULL, NULL, '2026-04-14', '2026-04-14 08:51:13', '2026-04-14 08:51:13'),
(191, 1, 1835, 'issue', -1, 30, ' (Видано: Фелонюк)', NULL, NULL, '2026-04-14', '2026-04-14 09:07:07', '2026-04-14 09:07:07'),
(192, 1, 1800, 'issue', -1, 4, ' (Видано: амб,1-5)', NULL, NULL, '2026-04-14', '2026-04-14 09:17:51', '2026-04-14 09:17:51'),
(193, 1, 1800, 'issue', -1, 3, ' (Видано: амб,1-5)', NULL, NULL, '2026-04-14', '2026-04-14 09:18:26', '2026-04-14 09:18:26'),
(194, 1, 1800, 'issue', -1, 2, ' (Видано: амб,6-10)', NULL, NULL, '2026-04-14', '2026-04-14 09:18:54', '2026-04-14 09:18:54'),
(195, 1, 1800, 'issue', -1, 1, ' (Видано: амб,1-5)', NULL, NULL, '2026-04-14', '2026-04-14 09:19:24', '2026-04-14 09:19:24'),
(196, 1, 1821, 'receipt', 17, 17, NULL, '433', NULL, '2026-04-15', '2026-04-15 03:41:50', '2026-04-15 03:41:50'),
(197, 1, 1821, 'issue', 6, 11, ' | Кому: амб,1-5', NULL, NULL, '2026-04-15', '2026-04-15 03:43:01', '2026-04-15 03:43:01'),
(198, 1, 1821, 'issue', 7, 4, ' | Кому: амб,6-10', NULL, NULL, '2026-04-15', '2026-04-15 03:43:48', '2026-04-15 03:43:48'),
(199, 1, 1878, 'receipt', 14, 14, 'Початковий залишок', NULL, NULL, '2026-04-15', '2026-04-15 03:51:02', '2026-04-15 03:51:02'),
(200, 1, 1878, 'issue', 4, 10, ' | Кому: амб,6-10', NULL, NULL, '2026-04-15', '2026-04-15 03:52:01', '2026-04-15 03:52:01'),
(201, 1, 1879, 'receipt', 2, 2, 'Початковий залишок', NULL, NULL, '2026-04-15', '2026-04-15 03:56:13', '2026-04-15 03:56:13'),
(202, 1, 1880, 'receipt', 14, 14, 'Початковий залишок', NULL, NULL, '2026-04-15', '2026-04-15 04:01:49', '2026-04-15 04:01:49'),
(203, 1, 1880, 'issue', 4, 10, ' | Кому: амб,6-10', NULL, NULL, '2026-04-15', '2026-04-15 04:02:42', '2026-04-15 04:02:42'),
(204, 1, 1835, 'issue', 15, 15, ' | Кому: амб,1-5', NULL, NULL, '2026-04-15', '2026-04-15 04:03:46', '2026-04-15 04:03:46'),
(205, 1, 1828, 'receipt', 50, 50, NULL, '433', NULL, '2026-04-15', '2026-04-15 04:07:33', '2026-04-15 04:07:33'),
(206, 1, 1828, 'issue', 10, 40, ' | Кому: амб,6-10', NULL, NULL, '2026-04-15', '2026-04-15 04:08:18', '2026-04-15 04:08:18'),
(207, 1, 1828, 'issue', 10, 30, ' | Кому: амб,1-5', NULL, NULL, '2026-04-15', '2026-04-15 04:09:08', '2026-04-15 04:09:08'),
(208, 1, 1829, 'receipt', 30, 30, NULL, '5554', NULL, '2026-04-15', '2026-04-15 04:10:48', '2026-04-15 04:10:48'),
(209, 1, 1829, 'issue', 10, 20, ' | Кому: амб,6-10', NULL, NULL, '2026-04-15', '2026-04-15 04:11:27', '2026-04-15 04:11:27'),
(210, 1, 1829, 'issue', 5, 15, ' | Кому: амб,1-5', NULL, NULL, '2026-04-15', '2026-04-15 04:13:36', '2026-04-15 04:13:36'),
(211, 1, 1830, 'receipt', 70, 70, '50шт', '111111', NULL, '2026-04-15', '2026-04-15 04:16:24', '2026-04-15 04:16:24'),
(212, 1, 1830, 'issue', 11, 59, ' | Кому: амб,1-5', NULL, NULL, '2026-04-15', '2026-04-15 04:19:57', '2026-04-15 04:19:57'),
(213, 1, 1807, 'receipt', 576, 576, NULL, '433', NULL, '2026-04-15', '2026-04-15 04:28:46', '2026-04-15 04:28:46'),
(214, 1, 1807, 'issue', 192, 384, ' | Кому: амб,6-10', NULL, NULL, '2026-04-15', '2026-04-15 04:29:26', '2026-04-15 04:29:26'),
(215, 1, 1807, 'issue', 176, 208, ' | Кому: амб,1-5', NULL, NULL, '2026-04-15', '2026-04-15 04:31:02', '2026-04-15 04:31:02'),
(216, 1, 1834, 'issue', 8, 5, ' | Кому: амб,1-5', NULL, NULL, '2026-04-15', '2026-04-15 04:41:12', '2026-04-15 04:41:12'),
(217, 1, 1831, 'issue', -2, 8, ' (Видано: амб,1-5)', NULL, NULL, '2026-04-15', '2026-04-15 04:43:48', '2026-04-15 04:43:48'),
(218, 1, 1832, 'issue', 5, 20, ' | Кому: амб,1-5', NULL, NULL, '2026-04-15', '2026-04-15 04:45:41', '2026-04-15 04:45:41'),
(219, 1, 1843, 'issue', 1, 18, ' | Кому: К19 булатова', NULL, NULL, '2026-04-15', '2026-04-15 09:25:58', '2026-04-15 09:25:58'),
(220, 1, 1843, 'issue', 1, 17, ' | Кому: К55 секретарь', NULL, NULL, '2026-04-15', '2026-04-15 09:26:27', '2026-04-15 09:26:27'),
(221, 1, 1881, 'receipt', 20, 20, 'Оприходовано з заявки ZAY-2026-000029', 'ZAY-2026-000029', NULL, '2026-04-16', '2026-04-16 09:55:58', '2026-04-16 09:55:58'),
(222, 1, 1882, 'receipt', 5, 5, 'Оприходовано з заявки ZAY-2026-000029', 'ZAY-2026-000029', NULL, '2026-04-16', '2026-04-16 09:55:58', '2026-04-16 09:55:58'),
(223, 1, 1883, 'receipt', 15, 15, 'Оприходовано з заявки ZAY-2026-000029', 'ZAY-2026-000029', NULL, '2026-04-16', '2026-04-16 09:55:58', '2026-04-16 09:55:58'),
(224, 1, 1884, 'receipt', 15, 15, 'Оприходовано з заявки ZAY-2026-000029', 'ZAY-2026-000029', NULL, '2026-04-16', '2026-04-16 09:55:58', '2026-04-16 09:55:58'),
(225, 1, 1885, 'receipt', 10, 10, 'Оприходовано з заявки ZAY-2026-000029', 'ZAY-2026-000029', NULL, '2026-04-16', '2026-04-16 09:55:58', '2026-04-16 09:55:58'),
(226, 1, 1886, 'receipt', 15, 15, 'Оприходовано з заявки ZAY-2026-000029', 'ZAY-2026-000029', NULL, '2026-04-16', '2026-04-16 09:55:58', '2026-04-16 09:55:58'),
(227, 1, 1887, 'receipt', 15, 15, 'Оприходовано з заявки ZAY-2026-000029', 'ZAY-2026-000029', NULL, '2026-04-16', '2026-04-16 09:55:58', '2026-04-16 09:55:58'),
(228, 1, 1888, 'receipt', 10, 10, 'Оприходовано з заявки ZAY-2026-000029', 'ZAY-2026-000029', NULL, '2026-04-16', '2026-04-16 09:55:58', '2026-04-16 09:55:58'),
(229, 1, 1889, 'receipt', 3, 3, 'Оприходовано з заявки ZAY-2026-000029', 'ZAY-2026-000029', NULL, '2026-04-16', '2026-04-16 09:55:58', '2026-04-16 09:55:58'),
(230, 1, 1890, 'receipt', 60, 60, 'Оприходовано з заявки ZAY-2026-000029', 'ZAY-2026-000029', NULL, '2026-04-16', '2026-04-16 09:55:58', '2026-04-16 09:55:58'),
(231, 1, 1891, 'receipt', 10, 10, 'Оприходовано з заявки ZAY-2026-000029', 'ZAY-2026-000029', NULL, '2026-04-16', '2026-04-16 09:55:58', '2026-04-16 09:55:58'),
(232, 1, 1892, 'receipt', 20, 20, 'Оприходовано з заявки ZAY-2026-000029', 'ZAY-2026-000029', NULL, '2026-04-16', '2026-04-16 09:55:58', '2026-04-16 09:55:58'),
(233, 1, 1893, 'receipt', 2, 2, 'Оприходовано з заявки ZAY-2026-000029', 'ZAY-2026-000029', NULL, '2026-04-16', '2026-04-16 09:55:58', '2026-04-16 09:55:58'),
(234, 1, 1894, 'receipt', 5, 5, 'Оприходовано з заявки ZAY-2026-000029', 'ZAY-2026-000029', NULL, '2026-04-16', '2026-04-16 09:55:58', '2026-04-16 09:55:58'),
(235, 1, 1895, 'receipt', 5, 5, 'Оприходовано з заявки ZAY-2026-000029', 'ZAY-2026-000029', NULL, '2026-04-16', '2026-04-16 09:55:58', '2026-04-16 09:55:58'),
(236, 1, 1896, 'receipt', 5, 5, 'Оприходовано з заявки ZAY-2026-000029', 'ZAY-2026-000029', NULL, '2026-04-16', '2026-04-16 09:55:58', '2026-04-16 09:55:58'),
(237, 1, 1897, 'receipt', 30, 30, 'Оприходовано з заявки ZAY-2026-000029', 'ZAY-2026-000029', NULL, '2026-04-16', '2026-04-16 09:55:58', '2026-04-16 09:55:58'),
(238, 1, 1898, 'receipt', 30, 30, 'Оприходовано з заявки ZAY-2026-000029', 'ZAY-2026-000029', NULL, '2026-04-16', '2026-04-16 09:55:58', '2026-04-16 09:55:58'),
(239, 1, 1899, 'receipt', 4, 4, NULL, 'Заказ пышалко 03.26', NULL, '2026-04-17', '2026-04-17 09:34:42', '2026-04-17 09:34:42'),
(240, 1, 1899, 'issue', -1, 3, 'Модернизация пк доктора (Видано: К302 болгарська)', NULL, NULL, '2026-04-17', '2026-04-17 09:35:49', '2026-04-17 09:35:49'),
(241, 1, 1844, 'issue', 7, 10, ' | Кому: амб,6-10', NULL, NULL, '2026-04-20', '2026-04-20 08:13:37', '2026-04-20 08:13:37'),
(242, 1, 1823, 'issue', 2, 19, ' | Кому: амб,1-5', NULL, NULL, '2026-04-20', '2026-04-20 08:15:33', '2026-04-20 08:15:33'),
(243, 1, 1844, 'issue', 6, 4, ' | Кому: амб,1-5', NULL, NULL, '2026-04-20', '2026-04-20 08:16:44', '2026-04-20 08:16:44'),
(244, 1, 1843, 'issue', 1, 16, ' | Кому: К203 Фесенко', NULL, NULL, '2026-04-21', '2026-04-21 04:32:57', '2026-04-21 04:32:57'),
(245, 1, 1813, 'receipt', 30, 30, NULL, '08/042', NULL, '2026-04-21', '2026-04-21 06:33:16', '2026-04-21 06:33:16'),
(246, 1, 1813, 'issue', 10, 20, ' | Кому: амб,6-10', NULL, NULL, '2026-04-21', '2026-04-21 06:34:53', '2026-04-21 06:34:53'),
(247, 1, 1820, 'receipt', 30, 30, '15.04.26', '08/042', NULL, '2026-04-21', '2026-04-21 06:36:52', '2026-04-21 06:36:52'),
(248, 1, 1820, 'issue', 10, 20, ' | Кому: амб,6-10', NULL, NULL, '2026-04-21', '2026-04-21 06:37:23', '2026-04-21 06:37:23'),
(249, 1, 1820, 'issue', 1, 19, ' | Кому: 1 пол,', NULL, NULL, '2026-04-21', '2026-04-21 07:26:46', '2026-04-21 07:26:46'),
(250, 1, 1900, 'receipt', 2, 2, 'Оприходовано з заявки ZAY-2026-000032', 'ZAY-2026-000032', NULL, '2026-04-21', '2026-04-21 11:13:37', '2026-04-21 11:13:37'),
(251, 1, 1901, 'receipt', 4, 4, 'Оприходовано з заявки ZAY-2026-000032', 'ZAY-2026-000032', NULL, '2026-04-21', '2026-04-21 11:13:37', '2026-04-21 11:13:37'),
(252, 1, 1902, 'receipt', 6, 6, 'Оприходовано з заявки ZAY-2026-000032', 'ZAY-2026-000032', NULL, '2026-04-21', '2026-04-21 11:13:37', '2026-04-21 11:13:37'),
(253, 1, 1903, 'receipt', 2, 2, 'Оприходовано з заявки ZAY-2026-000032', 'ZAY-2026-000032', NULL, '2026-04-21', '2026-04-21 11:13:37', '2026-04-21 11:13:37'),
(254, 1, 1904, 'receipt', 2, 2, 'Оприходовано з заявки ZAY-2026-000032', 'ZAY-2026-000032', NULL, '2026-04-21', '2026-04-21 11:13:37', '2026-04-21 11:13:37'),
(255, 1, 1905, 'receipt', 2, 2, 'Оприходовано з заявки ZAY-2026-000032', 'ZAY-2026-000032', NULL, '2026-04-21', '2026-04-21 11:13:37', '2026-04-21 11:13:37'),
(256, 1, 1906, 'receipt', 2, 2, 'Оприходовано з заявки ZAY-2026-000032', 'ZAY-2026-000032', NULL, '2026-04-21', '2026-04-21 11:13:37', '2026-04-21 11:13:37'),
(257, 1, 1907, 'receipt', 2, 2, 'Оприходовано з заявки ZAY-2026-000032', 'ZAY-2026-000032', NULL, '2026-04-21', '2026-04-21 11:13:37', '2026-04-21 11:13:37'),
(258, 1, 1908, 'receipt', 3, 3, 'Оприходовано з заявки ZAY-2026-000032', 'ZAY-2026-000032', NULL, '2026-04-21', '2026-04-21 11:13:37', '2026-04-21 11:13:37'),
(259, 1, 1909, 'receipt', 2, 2, 'Оприходовано з заявки ZAY-2026-000032', 'ZAY-2026-000032', NULL, '2026-04-21', '2026-04-21 11:13:37', '2026-04-21 11:13:37'),
(260, 1, 1910, 'receipt', 2, 2, 'Оприходовано з заявки ZAY-2026-000032', 'ZAY-2026-000032', NULL, '2026-04-21', '2026-04-21 11:13:37', '2026-04-21 11:13:37'),
(261, 1, 1911, 'receipt', 2, 2, 'Оприходовано з заявки ZAY-2026-000032', 'ZAY-2026-000032', NULL, '2026-04-21', '2026-04-21 11:13:37', '2026-04-21 11:13:37'),
(262, 1, 1912, 'receipt', 1, 1, 'Оприходовано з заявки ZAY-2026-000032', 'ZAY-2026-000032', NULL, '2026-04-21', '2026-04-21 11:13:37', '2026-04-21 11:13:37'),
(263, 1, 1913, 'receipt', 1, 1, 'Оприходовано з заявки ZAY-2026-000032', 'ZAY-2026-000032', NULL, '2026-04-21', '2026-04-21 11:13:37', '2026-04-21 11:13:37'),
(264, 1, 1914, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-04-22', '2026-04-22 10:03:16', '2026-04-22 10:03:16'),
(265, 1, 1916, 'receipt', 8, 8, 'Початковий залишок', NULL, NULL, '2026-04-22', '2026-04-22 10:07:10', '2026-04-22 10:07:10'),
(266, 1, 1917, 'receipt', 6, 6, 'Початковий залишок', NULL, NULL, '2026-04-22', '2026-04-22 10:08:42', '2026-04-22 10:08:42'),
(267, 1, 1918, 'receipt', 4, 4, 'Початковий залишок', NULL, NULL, '2026-04-22', '2026-04-22 10:09:16', '2026-04-22 10:09:16'),
(268, 1, 1919, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-04-22', '2026-04-22 10:10:27', '2026-04-22 10:10:27'),
(269, 1, 1920, 'receipt', 2, 2, 'Початковий залишок', NULL, NULL, '2026-04-22', '2026-04-22 10:10:46', '2026-04-22 10:10:46'),
(270, 1, 1921, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-04-22', '2026-04-22 10:11:13', '2026-04-22 10:11:13'),
(271, 1, 1922, 'receipt', 5, 5, 'Початковий залишок', NULL, NULL, '2026-04-22', '2026-04-22 10:11:54', '2026-04-22 10:11:54'),
(272, 1, 1923, 'receipt', 5, 5, 'Початковий залишок', NULL, NULL, '2026-04-22', '2026-04-22 10:12:43', '2026-04-22 10:12:43'),
(273, 1, 1924, 'receipt', 2, 2, 'Початковий залишок', NULL, NULL, '2026-04-22', '2026-04-22 10:25:39', '2026-04-22 10:25:39'),
(274, 1, 1925, 'receipt', 2, 2, 'Початковий залишок', NULL, NULL, '2026-04-22', '2026-04-22 10:29:57', '2026-04-22 10:29:57'),
(275, 1, 1926, 'receipt', 2, 2, 'Початковий залишок', NULL, NULL, '2026-04-22', '2026-04-22 10:32:17', '2026-04-22 10:32:17'),
(276, 1, 1927, 'receipt', 1, 1, NULL, 'Поточний залишок', NULL, '2026-04-22', '2026-04-22 10:33:23', '2026-04-22 10:33:23'),
(277, 1, 1928, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-04-22', '2026-04-22 10:34:57', '2026-04-22 10:34:57'),
(278, 1, 1929, 'receipt', 5, 5, 'Початковий залишок', NULL, NULL, '2026-04-22', '2026-04-22 10:35:32', '2026-04-22 10:35:32'),
(279, 1, 1930, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-04-22', '2026-04-22 10:36:07', '2026-04-22 10:36:07'),
(280, 1, 1931, 'receipt', 2, 2, 'Початковий залишок', NULL, NULL, '2026-04-22', '2026-04-22 10:36:35', '2026-04-22 10:36:35'),
(281, 1, 1932, 'receipt', 2, 2, 'Початковий залишок', NULL, NULL, '2026-04-22', '2026-04-22 10:36:58', '2026-04-22 10:36:58'),
(282, 1, 1933, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-04-22', '2026-04-22 10:37:22', '2026-04-22 10:37:22'),
(283, 1, 1934, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-04-22', '2026-04-22 10:37:44', '2026-04-22 10:37:44'),
(284, 1, 1935, 'receipt', 7, 7, 'Початковий залишок', NULL, NULL, '2026-04-22', '2026-04-22 10:39:03', '2026-04-22 10:39:03'),
(285, 1, 1935, 'issue', -3, 4, ' | Кому: Пущено в роботу Житомирська, Липи', NULL, NULL, '2026-04-22', '2026-04-22 10:40:09', '2026-04-22 10:40:09'),
(286, 1, 1936, 'receipt', 5, 5, 'Початковий залишок', NULL, NULL, '2026-04-22', '2026-04-22 10:41:08', '2026-04-22 10:41:08'),
(287, 1, 1936, 'issue', -4, 1, ' | Кому: Пущено в роботу Житомирська, Липи', NULL, NULL, '2026-04-22', '2026-04-22 10:41:38', '2026-04-22 10:41:38'),
(288, 1, 1937, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 03:20:33', '2026-04-23 03:20:33'),
(289, 1, 1938, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 03:23:00', '2026-04-23 03:23:00'),
(290, 1, 1939, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 03:24:52', '2026-04-23 03:24:52'),
(291, 1, 1940, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 03:27:07', '2026-04-23 03:27:07'),
(292, 1, 1941, 'receipt', 1, 1, NULL, NULL, NULL, '2026-04-23', '2026-04-23 03:33:18', '2026-04-23 03:33:18'),
(293, 1, 1942, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 03:35:32', '2026-04-23 03:35:32'),
(294, 1, 1943, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 03:36:46', '2026-04-23 03:36:46'),
(295, 1, 1944, 'receipt', 2, 2, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 03:39:16', '2026-04-23 03:39:16'),
(296, 1, 1945, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 03:43:09', '2026-04-23 03:43:09'),
(297, 1, 1946, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 03:44:33', '2026-04-23 03:44:33'),
(298, 1, 1947, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 03:48:17', '2026-04-23 03:48:17'),
(299, 1, 1949, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 03:52:43', '2026-04-23 03:52:43'),
(300, 1, 1886, 'issue', -1, 14, ' | Кому: каб,№52', NULL, NULL, '2026-04-23', '2026-04-23 04:03:36', '2026-04-23 04:03:36'),
(301, 1, 1950, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 04:12:32', '2026-04-23 04:12:32'),
(302, 1, 1951, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 04:13:10', '2026-04-23 04:13:10'),
(303, 1, 1952, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 04:14:00', '2026-04-23 04:14:00'),
(304, 1, 1953, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 04:14:55', '2026-04-23 04:14:55'),
(305, 1, 1954, 'receipt', 3, 3, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 04:16:06', '2026-04-23 04:16:06'),
(306, 1, 1955, 'receipt', 2, 2, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 04:16:36', '2026-04-23 04:16:36'),
(307, 1, 1956, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 04:18:02', '2026-04-23 04:18:02'),
(308, 1, 1957, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 04:18:36', '2026-04-23 04:18:36'),
(309, 1, 1958, 'receipt', 2, 2, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 04:18:57', '2026-04-23 04:18:57'),
(310, 1, 1959, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 04:19:41', '2026-04-23 04:19:41'),
(311, 1, 1960, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 04:20:08', '2026-04-23 04:20:08'),
(312, 1, 1961, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 04:20:23', '2026-04-23 04:20:23'),
(313, 1, 1962, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 04:20:47', '2026-04-23 04:20:47'),
(314, 1, 1963, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 04:21:24', '2026-04-23 04:21:24'),
(315, 1, 1964, 'receipt', 2, 2, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 04:21:56', '2026-04-23 04:21:56'),
(316, 1, 1965, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 04:22:38', '2026-04-23 04:22:38'),
(317, 1, 1966, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 04:27:47', '2026-04-23 04:27:47'),
(318, 1, 1967, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 04:29:50', '2026-04-23 04:29:50'),
(319, 1, 1968, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 04:31:25', '2026-04-23 04:31:25'),
(320, 1, 1969, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 04:34:50', '2026-04-23 04:34:50'),
(321, 1, 1883, 'receipt', 3, 18, 'залишок', NULL, NULL, '2026-04-23', '2026-04-23 04:38:33', '2026-04-23 04:38:33'),
(322, 1, 1970, 'receipt', 3, 3, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 04:54:07', '2026-04-23 04:54:07'),
(323, 1, 1971, 'receipt', 4, 4, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 05:10:45', '2026-04-23 05:10:45'),
(324, 1, 1856, 'issue', -1, 1, ' | Кому: Сантехник  Житомірська', NULL, NULL, '2026-04-23', '2026-04-23 05:29:52', '2026-04-23 05:29:52'),
(325, 1, 1972, 'receipt', 5, 5, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 05:33:58', '2026-04-23 05:33:58'),
(326, 1, 1972, 'issue', -1, 4, ' | Кому: Пущено в роботу Житомирська', NULL, NULL, '2026-04-23', '2026-04-23 05:34:49', '2026-04-23 05:34:49'),
(327, 1, 1973, 'receipt', 10, 10, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 05:36:49', '2026-04-23 05:36:49'),
(328, 1, 1973, 'issue', -2, 8, ' | Кому: Пущено в роботу Житомирська', NULL, NULL, '2026-04-23', '2026-04-23 05:37:28', '2026-04-23 05:37:28'),
(329, 1, 1974, 'receipt', 3, 3, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 05:50:43', '2026-04-23 05:50:43'),
(330, 1, 1975, 'receipt', 8, 8, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 06:10:59', '2026-04-23 06:10:59'),
(331, 1, 1976, 'receipt', 8, 8, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 06:13:12', '2026-04-23 06:13:12'),
(332, 1, 1977, 'receipt', 15, 15, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 06:21:28', '2026-04-23 06:21:28'),
(333, 1, 1978, 'receipt', 30, 30, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 06:28:04', '2026-04-23 06:28:04'),
(334, 1, 1979, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 06:31:45', '2026-04-23 06:31:45'),
(335, 1, 1980, 'receipt', 39, 39, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 06:35:49', '2026-04-23 06:35:49'),
(336, 1, 1981, 'receipt', 4, 4, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 06:39:02', '2026-04-23 06:39:02'),
(337, 1, 1981, 'receipt', 1, 5, NULL, '221', NULL, '2026-04-23', '2026-04-23 06:40:26', '2026-04-23 06:40:26'),
(338, 1, 1982, 'receipt', 5, 5, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 06:43:20', '2026-04-23 06:43:20'),
(339, 1, 1983, 'receipt', 8, 8, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 06:45:08', '2026-04-23 06:45:08'),
(340, 1, 1984, 'receipt', 3, 3, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 06:48:26', '2026-04-23 06:48:26'),
(341, 1, 1985, 'receipt', 8, 8, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 06:57:29', '2026-04-23 06:57:29'),
(342, 1, 1986, 'receipt', 3, 3, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 07:07:05', '2026-04-23 07:07:05'),
(343, 1, 1987, 'receipt', 3, 3, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 07:12:15', '2026-04-23 07:12:15'),
(344, 1, 1988, 'receipt', 30, 30, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 07:16:28', '2026-04-23 07:16:28'),
(345, 1, 1989, 'receipt', 20, 20, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 07:19:00', '2026-04-23 07:19:00'),
(346, 1, 1990, 'receipt', 9, 9, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 07:26:01', '2026-04-23 07:26:01'),
(347, 1, 1991, 'receipt', 930, 930, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 07:33:02', '2026-04-23 07:33:02'),
(348, 1, 1992, 'receipt', 4, 4, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 07:40:18', '2026-04-23 07:40:18'),
(349, 1, 1993, 'receipt', 10, 10, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 08:21:41', '2026-04-23 08:21:41'),
(350, 1, 1994, 'receipt', 5, 5, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 08:27:07', '2026-04-23 08:27:07'),
(351, 1, 1995, 'receipt', 10, 10, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 08:27:44', '2026-04-23 08:27:44'),
(352, 1, 1996, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 08:28:16', '2026-04-23 08:28:16'),
(353, 1, 1997, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 08:28:39', '2026-04-23 08:28:39'),
(354, 1, 1998, 'receipt', 4, 4, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 08:29:06', '2026-04-23 08:29:06'),
(355, 1, 1999, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 08:30:02', '2026-04-23 08:30:02'),
(356, 1, 2000, 'receipt', 2, 2, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 08:30:58', '2026-04-23 08:30:58'),
(357, 1, 2001, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 08:31:40', '2026-04-23 08:31:40'),
(358, 1, 2002, 'receipt', 4, 4, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 09:17:43', '2026-04-23 09:17:43'),
(359, 1, 2003, 'receipt', 2, 2, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 09:20:51', '2026-04-23 09:20:51'),
(360, 1, 2004, 'receipt', 3, 3, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 09:49:58', '2026-04-23 09:49:58'),
(361, 1, 2005, 'receipt', 2, 2, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 09:53:26', '2026-04-23 09:53:26'),
(362, 1, 2006, 'receipt', 4, 4, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 09:56:41', '2026-04-23 09:56:41'),
(363, 1, 2007, 'receipt', 4, 4, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 10:23:08', '2026-04-23 10:23:08'),
(364, 1, 2008, 'receipt', 13, 13, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 11:01:42', '2026-04-23 11:01:42'),
(365, 1, 2009, 'receipt', 5, 5, 'Початковий залишок', NULL, NULL, '2026-04-23', '2026-04-23 11:07:50', '2026-04-23 11:07:50'),
(366, 1, 2010, 'receipt', 30, 30, 'Початковий залишок', NULL, NULL, '2026-04-24', '2026-04-24 03:26:20', '2026-04-24 03:26:20'),
(367, 1, 2011, 'receipt', 30, 30, 'Початковий залишок', NULL, NULL, '2026-04-24', '2026-04-24 03:28:08', '2026-04-24 03:28:08'),
(368, 1, 2012, 'receipt', 35, 35, 'Початковий залишок', NULL, NULL, '2026-04-24', '2026-04-24 03:29:32', '2026-04-24 03:29:32'),
(369, 1, 1793, 'issue', -1, 15, ' | Кому: амб,1-5', NULL, NULL, '2026-04-24', '2026-04-24 03:48:12', '2026-04-24 03:48:12'),
(370, 1, 1795, 'issue', -1, 1, ' | Кому: амб,1-5', NULL, NULL, '2026-04-24', '2026-04-24 03:53:23', '2026-04-24 03:53:23'),
(371, 1, 1803, 'issue', -1, 57, ' | Кому: каб№55', NULL, NULL, '2026-04-24', '2026-04-24 04:16:44', '2026-04-24 04:16:44'),
(372, 1, 1833, 'issue', -80, 120, ' (Видано: амб,1-5)', NULL, NULL, '2026-04-24', '2026-04-24 04:40:40', '2026-04-24 04:40:40'),
(373, 1, 1805, 'issue', -30, 20, ' (Видано: каб,№52)', NULL, NULL, '2026-04-24', '2026-04-24 04:54:05', '2026-04-24 04:54:05'),
(374, 1, 1832, 'issue', -4, 16, ' (Видано: амб,1-5)', NULL, NULL, '2026-04-24', '2026-04-24 04:59:28', '2026-04-24 04:59:28'),
(375, 1, 1832, 'issue', -4, 12, ' (Видано: амб,6-10)', NULL, NULL, '2026-04-24', '2026-04-24 04:59:44', '2026-04-24 04:59:44'),
(376, 1, 1804, 'issue', -28, 22, ' | Кому: амб,1-5', NULL, NULL, '2026-04-24', '2026-04-24 05:07:31', '2026-04-24 05:07:31'),
(377, 1, 1804, 'issue', -1, 21, ' | Кому: амб,6-10', NULL, NULL, '2026-04-24', '2026-04-24 05:08:02', '2026-04-24 05:08:02'),
(378, 1, 1804, 'issue', -2, 19, ' | Кому: амб,6-10', NULL, NULL, '2026-04-24', '2026-04-24 05:11:35', '2026-04-24 05:11:35'),
(379, 1, 1786, 'issue', -1, 2, ' | Кому: амб,6-10', NULL, NULL, '2026-04-24', '2026-04-24 05:14:14', '2026-04-24 05:14:14'),
(380, 1, 1784, 'issue', -1, 1, ' | Кому: амб,6-10', NULL, NULL, '2026-04-24', '2026-04-24 05:15:57', '2026-04-24 05:15:57');
INSERT INTO `warehouse_movements` (`id`, `user_id`, `inventory_id`, `type`, `quantity`, `balance_after`, `note`, `document_number`, `issued_to_user_id`, `operation_date`, `created_at`, `updated_at`) VALUES
(381, 1, 1823, 'issue', -1, 18, ' | Кому: амб,1-5', NULL, NULL, '2026-04-24', '2026-04-24 05:40:35', '2026-04-24 05:40:35'),
(382, 1, 1788, 'issue', -1, 9, ' | Кому: К306 Борисюк, болгарская 38', NULL, NULL, '2026-04-24', '2026-04-24 05:48:54', '2026-04-24 05:48:54'),
(383, 1, 1843, 'issue', -1, 15, ' | Кому: К40 Гроздева липи', NULL, NULL, '2026-04-24', '2026-04-24 05:49:21', '2026-04-24 05:49:21'),
(384, 1, 1806, 'receipt', 560, 560, NULL, '2728', NULL, '2026-04-24', '2026-04-24 06:05:19', '2026-04-24 06:05:19'),
(385, 1, 1806, 'issue', -144, 416, ' (Видано: амб,6-10)', NULL, NULL, '2026-04-24', '2026-04-24 06:06:06', '2026-04-24 06:06:06'),
(386, 1, 1806, 'issue', -189, 227, ' (Видано: амб,1-5)', NULL, NULL, '2026-04-24', '2026-04-24 06:09:27', '2026-04-24 06:09:27'),
(387, 1, 1808, 'receipt', 320, 320, NULL, '2827', NULL, '2026-04-24', '2026-04-24 06:17:10', '2026-04-24 06:17:10'),
(388, 1, 1808, 'issue', -96, 224, ' | Кому: амб,6-10', NULL, NULL, '2026-04-24', '2026-04-24 06:22:28', '2026-04-24 06:22:28'),
(389, 1, 1808, 'issue', -10, 214, ' (Видано: амб,1-5)', NULL, NULL, '2026-04-24', '2026-04-24 07:32:49', '2026-04-24 07:32:49'),
(390, 1, 1883, 'receipt', 5, 23, NULL, '3442', NULL, '2026-04-24', '2026-04-24 09:54:10', '2026-04-24 09:54:10'),
(391, 1, 1970, 'receipt', 10, 13, NULL, '16800', NULL, '2026-04-24', '2026-04-24 09:58:23', '2026-04-24 09:58:23'),
(392, 1, 2013, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-04-24', '2026-04-24 10:06:15', '2026-04-24 10:06:15'),
(393, 3, 1843, 'issue', -2, 13, ' | Кому: каб,№52', NULL, NULL, '2026-04-27', '2026-04-27 03:38:57', '2026-04-27 03:38:57'),
(394, 3, 2004, 'issue', -1, 2, ' | Кому: амб,6-10', NULL, NULL, '2026-04-27', '2026-04-27 03:58:27', '2026-04-27 03:58:27'),
(395, 3, 2014, 'receipt', 3, 3, 'Початковий залишок', NULL, NULL, '2026-04-27', '2026-04-27 04:22:11', '2026-04-27 04:22:11'),
(396, 3, 1793, 'issue', -1, 14, ' | Кому: амб,1-5 2Єт,', NULL, NULL, '2026-04-27', '2026-04-27 04:55:23', '2026-04-27 04:55:23'),
(397, 3, 1798, 'issue', -1, 2, ' | Кому: амб,1-5', NULL, NULL, '2026-04-27', '2026-04-27 05:00:16', '2026-04-27 05:00:16'),
(398, 3, 1830, 'issue', -2, 57, ' | Кому: амб,1-5', NULL, NULL, '2026-04-27', '2026-04-27 05:37:31', '2026-04-27 05:37:31'),
(399, 3, 1830, 'issue', -10, 47, ' | Кому: амб,6-10', NULL, NULL, '2026-04-27', '2026-04-27 05:39:24', '2026-04-27 05:39:24'),
(400, 3, 2015, 'receipt', 5, 5, 'Початковий залишок', NULL, NULL, '2026-04-27', '2026-04-27 05:57:05', '2026-04-27 05:57:05'),
(401, 3, 2016, 'receipt', 5, 5, 'Початковий залишок', NULL, NULL, '2026-04-27', '2026-04-27 06:13:17', '2026-04-27 06:13:17'),
(402, 3, 2017, 'receipt', 30, 30, 'Початковий залишок', NULL, NULL, '2026-04-27', '2026-04-27 06:15:54', '2026-04-27 06:15:54'),
(403, 3, 2018, 'receipt', 10, 10, 'Початковий залишок', NULL, NULL, '2026-04-27', '2026-04-27 06:19:38', '2026-04-27 06:19:38'),
(404, 3, 2017, 'issue', -10, 20, ' | Кому: амб,6-10', NULL, NULL, '2026-04-27', '2026-04-27 06:25:06', '2026-04-27 06:25:06'),
(405, 3, 1891, 'issue', -4, 6, ' (Видано: амб,6-10)', NULL, NULL, '2026-04-27', '2026-04-27 09:54:19', '2026-04-27 09:54:19'),
(406, 3, 1890, 'issue', -2, 58, ' | Кому: каб,№52', NULL, NULL, '2026-04-27', '2026-04-27 09:56:20', '2026-04-27 09:56:20'),
(407, 3, 2019, 'receipt', 5, 5, 'Початковий залишок', NULL, NULL, '2026-04-27', '2026-04-27 10:15:21', '2026-04-27 10:15:21'),
(408, 3, 2020, 'receipt', 5, 5, 'Оприходовано з заявки ZAY-2026-000030', 'ZAY-2026-000030', NULL, '2026-04-28', '2026-04-28 04:24:48', '2026-04-28 04:24:48'),
(409, 3, 2021, 'receipt', 2, 2, 'Оприходовано з заявки ZAY-2026-000030', 'ZAY-2026-000030', NULL, '2026-04-28', '2026-04-28 04:24:48', '2026-04-28 04:24:48'),
(410, 3, 2022, 'receipt', 2, 2, 'Оприходовано з заявки ZAY-2026-000030', 'ZAY-2026-000030', NULL, '2026-04-28', '2026-04-28 04:24:48', '2026-04-28 04:24:48'),
(411, 3, 2023, 'receipt', 6, 6, 'Оприходовано з заявки ZAY-2026-000030', 'ZAY-2026-000030', NULL, '2026-04-28', '2026-04-28 04:24:48', '2026-04-28 04:24:48'),
(412, 3, 2024, 'receipt', 2, 2, 'Оприходовано з заявки ZAY-2026-000030', 'ZAY-2026-000030', NULL, '2026-04-28', '2026-04-28 04:24:48', '2026-04-28 04:24:48'),
(413, 3, 2025, 'receipt', 2, 2, 'Оприходовано з заявки ZAY-2026-000030', 'ZAY-2026-000030', NULL, '2026-04-28', '2026-04-28 04:24:48', '2026-04-28 04:24:48'),
(414, 3, 2026, 'receipt', 2, 2, 'Оприходовано з заявки ZAY-2026-000030', 'ZAY-2026-000030', NULL, '2026-04-28', '2026-04-28 04:24:48', '2026-04-28 04:24:48'),
(415, 3, 1971, 'receipt', 100, 104, 'Оприходовано з заявки ZAY-2026-000030', 'ZAY-2026-000030', NULL, '2026-04-28', '2026-04-28 04:24:48', '2026-04-28 04:24:48'),
(416, 3, 2027, 'receipt', 30, 30, 'Оприходовано з заявки ZAY-2026-000030', 'ZAY-2026-000030', NULL, '2026-04-28', '2026-04-28 04:24:48', '2026-04-28 04:24:48'),
(417, 3, 2028, 'receipt', 20, 20, 'Оприходовано з заявки ZAY-2026-000030', 'ZAY-2026-000030', NULL, '2026-04-28', '2026-04-28 04:24:48', '2026-04-28 04:24:48'),
(418, 3, 2020, 'issue', -1, 4, ' | Кому: Садовнік', NULL, NULL, '2026-04-28', '2026-04-28 05:24:36', '2026-04-28 05:24:36'),
(419, 3, 1695, 'issue', -1, 0, ' (Видано: к №52)', NULL, NULL, '2026-04-28', '2026-04-28 06:25:40', '2026-04-28 06:25:40'),
(420, 3, 2029, 'receipt', 5, 5, 'Початковий залишок', NULL, NULL, '2026-04-28', '2026-04-28 07:06:48', '2026-04-28 07:06:48'),
(421, 3, 2030, 'receipt', 5, 5, 'Початковий залишок', NULL, NULL, '2026-04-28', '2026-04-28 10:11:34', '2026-04-28 10:11:34'),
(422, 3, 1837, 'issue', -1, 0, ' (Видано: амб,1-5)', NULL, NULL, '2026-04-28', '2026-04-28 10:13:23', '2026-04-28 10:13:23'),
(423, 3, 1838, 'issue', -1, 0, ' | Кому: амб,1-5', NULL, NULL, '2026-04-28', '2026-04-28 10:19:47', '2026-04-28 10:19:47'),
(424, 3, 1838, 'receipt', 5, 5, NULL, '08042', NULL, '2026-04-28', '2026-04-28 10:20:31', '2026-04-28 10:20:31'),
(425, 3, 1839, 'issue', -1, 0, ' (Видано: амб,1-5)', NULL, NULL, '2026-04-28', '2026-04-28 10:23:45', '2026-04-28 10:23:45'),
(426, 3, 1839, 'receipt', 5, 5, NULL, '08/042', NULL, '2026-04-28', '2026-04-28 10:24:09', '2026-04-28 10:24:09'),
(427, 3, 1841, 'receipt', 3, 3, NULL, '08/042', NULL, '2026-04-28', '2026-04-28 10:28:37', '2026-04-28 10:28:37'),
(428, 3, 1824, 'receipt', 10, 10, NULL, '08/042', NULL, '2026-04-28', '2026-04-28 10:33:53', '2026-04-28 10:33:53'),
(429, 3, 1840, 'issue', -1, 0, ' (Видано: амб,1-5)', NULL, NULL, '2026-04-28', '2026-04-28 10:37:23', '2026-04-28 10:37:23'),
(430, 3, 1840, 'receipt', 10, 10, NULL, '08/042', NULL, '2026-04-28', '2026-04-28 10:37:46', '2026-04-28 10:37:46'),
(431, 3, 1888, 'issue', -2, 8, ' (Видано: амб,6-10 сантехнік)', NULL, NULL, '2026-04-29', '2026-04-29 04:22:32', '2026-04-29 04:22:32'),
(432, 3, 1886, 'issue', -2, 12, ' (Видано: каб,№52)', NULL, NULL, '2026-04-29', '2026-04-29 04:28:38', '2026-04-29 04:28:38'),
(433, 3, 1996, 'issue', -1, 0, ' | Кому: каб,№52', NULL, NULL, '2026-04-29', '2026-04-29 04:30:54', '2026-04-29 04:30:54'),
(434, 3, 1998, 'issue', -1, 3, ' | Кому: каб,№52', NULL, NULL, '2026-04-29', '2026-04-29 04:32:10', '2026-04-29 04:32:10'),
(435, 3, 2031, 'receipt', 10, 10, 'Початковий залишок', NULL, NULL, '2026-04-29', '2026-04-29 05:17:49', '2026-04-29 05:17:49'),
(436, 1, 1788, 'issue', -1, 8, ' | Кому: К49 Сікорська', NULL, NULL, '2026-04-29', '2026-04-29 06:00:09', '2026-04-29 06:00:09'),
(437, 3, 2032, 'receipt', 5, 5, 'Початковий залишок', NULL, NULL, '2026-04-29', '2026-04-29 06:01:24', '2026-04-29 06:01:24'),
(438, 3, 1822, 'receipt', 40, 40, NULL, '31100', NULL, '2026-04-29', '2026-04-29 06:41:10', '2026-04-29 06:41:10'),
(439, 3, 2033, 'receipt', 2, 2, 'Початковий залишок', NULL, NULL, '2026-04-29', '2026-04-29 06:55:44', '2026-04-29 06:55:44'),
(440, 3, 2034, 'receipt', 30, 30, 'Початковий залишок', NULL, NULL, '2026-04-29', '2026-04-29 06:57:57', '2026-04-29 06:57:57'),
(441, 3, 2035, 'receipt', 30, 30, 'Початковий залишок', NULL, NULL, '2026-04-29', '2026-04-29 07:00:03', '2026-04-29 07:00:03'),
(442, 3, 2036, 'receipt', 4, 4, 'Початковий залишок', NULL, NULL, '2026-04-29', '2026-04-29 07:01:44', '2026-04-29 07:01:44'),
(443, 3, 2037, 'receipt', 2, 2, 'Початковий залишок', NULL, NULL, '2026-04-29', '2026-04-29 07:05:01', '2026-04-29 07:05:01'),
(444, 3, 2038, 'receipt', 2, 2, 'Початковий залишок', NULL, NULL, '2026-04-29', '2026-04-29 07:14:24', '2026-04-29 07:14:24'),
(445, 3, 2039, 'receipt', 2, 2, 'Початковий залишок', NULL, NULL, '2026-04-29', '2026-04-29 07:15:55', '2026-04-29 07:15:55'),
(446, 3, 2040, 'receipt', 2, 2, 'Початковий залишок', NULL, NULL, '2026-04-29', '2026-04-29 07:18:11', '2026-04-29 07:18:11'),
(447, 3, 2041, 'receipt', 2, 2, 'Початковий залишок', NULL, NULL, '2026-04-29', '2026-04-29 07:57:36', '2026-04-29 07:57:36'),
(448, 3, 2042, 'receipt', 2, 2, 'Початковий залишок', NULL, NULL, '2026-04-29', '2026-04-29 07:58:41', '2026-04-29 07:58:41'),
(449, 3, 2043, 'receipt', 2, 2, 'Початковий залишок', NULL, NULL, '2026-04-29', '2026-04-29 07:59:41', '2026-04-29 07:59:41'),
(450, 3, 2044, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-04-29', '2026-04-29 08:05:06', '2026-04-29 08:05:06'),
(451, 3, 2045, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-04-29', '2026-04-29 08:05:31', '2026-04-29 08:05:31'),
(452, 3, 2046, 'receipt', 4, 4, 'Початковий залишок', NULL, NULL, '2026-04-29', '2026-04-29 09:54:39', '2026-04-29 09:54:39'),
(453, 3, 2047, 'receipt', 4, 4, 'Початковий залишок', NULL, NULL, '2026-04-29', '2026-04-29 09:59:07', '2026-04-29 09:59:07'),
(454, 3, 2048, 'receipt', 4, 4, 'Початковий залишок', NULL, NULL, '2026-04-29', '2026-04-29 10:04:25', '2026-04-29 10:04:25'),
(455, 3, 2048, 'issue', -2, 2, ' | Кому: амб,6-10', NULL, NULL, '2026-04-29', '2026-04-29 10:06:14', '2026-04-29 10:06:14'),
(456, 3, 2047, 'issue', -2, 2, ' | Кому: амб,6-10', NULL, NULL, '2026-04-29', '2026-04-29 10:06:41', '2026-04-29 10:06:41'),
(457, 3, 2046, 'issue', -2, 2, ' | Кому: амб,6-10', NULL, NULL, '2026-04-29', '2026-04-29 10:07:49', '2026-04-29 10:07:49'),
(458, 3, 2036, 'issue', -2, 2, ' | Кому: амб,6-10', NULL, NULL, '2026-04-29', '2026-04-29 10:23:20', '2026-04-29 10:23:20'),
(459, 3, 2037, 'issue', -1, 1, ' (Видано: амб,6-10)', NULL, NULL, '2026-04-29', '2026-04-29 10:25:53', '2026-04-29 10:25:53'),
(460, 3, 1821, 'issue', -1, 3, ' (Видано: Пол,№1)', NULL, NULL, '2026-04-29', '2026-04-29 10:28:12', '2026-04-29 10:28:12'),
(461, 3, 1806, 'issue', -23, 204, ' (Видано: амб,1-5)', NULL, NULL, '2026-04-29', '2026-04-29 10:31:07', '2026-04-29 10:31:07'),
(462, 3, 1807, 'issue', -56, 152, ' (Видано: амб,1-5)', NULL, NULL, '2026-04-29', '2026-04-29 10:35:27', '2026-04-29 10:35:27'),
(463, 3, 1813, 'issue', -2, 18, ' | Кому: амб,1-5 Лобода,Машошина', NULL, NULL, '2026-04-30', '2026-04-30 05:40:30', '2026-04-30 05:40:30'),
(464, 3, 1907, 'issue', -1, 1, ' | Кому: Алексанров', NULL, NULL, '2026-05-01', '2026-05-01 08:19:18', '2026-05-01 08:19:18'),
(465, 3, 2033, 'issue', -1, 1, ' | Кому: Садовнік', NULL, NULL, '2026-05-01', '2026-05-01 08:20:13', '2026-05-01 08:20:13'),
(466, 3, 1906, 'issue', -1, 1, ' | Кому: Александров', NULL, NULL, '2026-05-01', '2026-05-01 08:20:56', '2026-05-01 08:20:56'),
(467, 3, 1913, 'issue', -1, 0, ' | Кому: Александров', NULL, NULL, '2026-05-01', '2026-05-01 08:21:48', '2026-05-01 08:21:48'),
(468, 3, 1942, 'issue', -1, 0, ' (Видано: дворнік Житомірська)', NULL, NULL, '2026-05-01', '2026-05-01 08:26:41', '2026-05-01 08:26:41'),
(469, 3, 1905, 'issue', -1, 1, ' | Кому: Александров', NULL, NULL, '2026-05-01', '2026-05-01 08:27:53', '2026-05-01 08:27:53'),
(470, 3, 1967, 'issue', -1, 0, ' | Кому: Александров', NULL, NULL, '2026-05-01', '2026-05-01 08:29:18', '2026-05-01 08:29:18'),
(471, 1, 1843, 'issue', -1, 12, ' (Видано: К38)', NULL, NULL, '2026-05-01', '2026-05-01 10:54:59', '2026-05-01 10:54:59'),
(472, 3, 1883, 'issue', -3, 20, ' (Видано: амб,6-10 лобараторія 3пов,)', NULL, NULL, '2026-05-04', '2026-05-04 04:27:36', '2026-05-04 04:27:36'),
(473, 3, 1881, 'issue', -10, 10, ' | Кому: амб,6-10 лобараторія 3пов,', NULL, NULL, '2026-05-04', '2026-05-04 04:29:32', '2026-05-04 04:29:32'),
(474, 3, 1889, 'issue', -1, 2, ' | Кому: амб,6-10 лобараторія 3пов,', NULL, NULL, '2026-05-04', '2026-05-04 04:30:56', '2026-05-04 04:30:56'),
(475, 3, 1886, 'issue', -10, 2, ' | Кому: амб,6-10 лобараторія 3пов,', NULL, NULL, '2026-05-04', '2026-05-04 04:32:59', '2026-05-04 04:32:59'),
(476, 3, 1803, 'issue', -1, 56, ' | Кому: Козак', NULL, NULL, '2026-05-05', '2026-05-05 10:00:39', '2026-05-05 10:00:39'),
(477, 3, 1977, 'issue', -5, 10, ' | Кому: амб,6-10 Лабораторія 3поверх', NULL, NULL, '2026-05-05', '2026-05-05 10:05:06', '2026-05-05 10:05:06'),
(478, 3, 1991, 'issue', -100, 830, ' | Кому: амб,6-10 лобараторія 3пов,', NULL, NULL, '2026-05-05', '2026-05-05 10:06:18', '2026-05-05 10:06:18'),
(479, 3, 2018, 'issue', -4, 6, ' (Видано: амб,6-10 лобараторія 3пов,)', NULL, NULL, '2026-05-06', '2026-05-06 05:35:10', '2026-05-06 05:35:10'),
(480, 3, 2015, 'issue', -1, 4, ' | Кому: амб,6-10 інвал,туал', NULL, NULL, '2026-05-06', '2026-05-06 05:39:15', '2026-05-06 05:39:15'),
(481, 1, 1843, 'issue', -1, 11, ' | Кому: К24 ЗПТ Инна', NULL, NULL, '2026-05-06', '2026-05-06 09:20:21', '2026-05-06 09:20:21'),
(482, 3, 1791, 'issue', -1, 2, ' | Кому: амб,1-5', NULL, NULL, '2026-05-08', '2026-05-08 03:55:26', '2026-05-08 03:55:26'),
(483, 3, 2049, 'receipt', 280, 280, 'Початковий залишок', NULL, NULL, '2026-05-08', '2026-05-08 09:30:44', '2026-05-08 09:30:44'),
(484, 3, 2050, 'receipt', 50, 50, 'Початковий залишок', NULL, NULL, '2026-05-08', '2026-05-08 09:34:15', '2026-05-08 09:34:15'),
(485, 3, 2049, 'issue', -100, 180, ' | Кому: амб,6-10 лобараторія 3пов,', NULL, NULL, '2026-05-08', '2026-05-08 09:35:16', '2026-05-08 09:35:16'),
(486, 3, 1950, 'receipt', 1, 2, 'габардин двосторонній', '11200389', NULL, '2026-05-11', '2026-05-11 06:41:12', '2026-05-11 06:41:12'),
(487, 3, 1950, 'issue', -1, 1, ' (Видано: амб,6-10)', NULL, NULL, '2026-05-11', '2026-05-11 06:42:59', '2026-05-11 06:42:59'),
(488, 3, 1876, 'issue', -1, 13, ' (Видано: амб,6-10 2пов,прийомна)', NULL, NULL, '2026-05-12', '2026-05-12 03:34:58', '2026-05-12 03:34:58'),
(489, 3, 1823, 'issue', -1, 17, ' | Кому: амб,1-5 1поверх', NULL, NULL, '2026-05-12', '2026-05-12 04:28:41', '2026-05-12 04:28:41'),
(490, 3, 1844, 'issue', -4, 0, ' | Кому: амб,1-5', NULL, NULL, '2026-05-12', '2026-05-12 04:29:48', '2026-05-12 04:29:48'),
(491, 3, 1807, 'issue', -56, 96, ' (Видано: амб,1-5)', NULL, NULL, '2026-05-12', '2026-05-12 04:36:23', '2026-05-12 04:36:23'),
(492, 3, 1806, 'issue', -46, 158, ' (Видано: амб,1-5)', NULL, NULL, '2026-05-12', '2026-05-12 04:48:34', '2026-05-12 04:48:34'),
(493, 3, 1808, 'issue', -9, 205, ' (Видано: амб,1-5)', NULL, NULL, '2026-05-12', '2026-05-12 04:51:40', '2026-05-12 04:51:40'),
(494, 3, 1795, 'issue', -1, 0, ' | Кому: амб,1-5', NULL, NULL, '2026-05-12', '2026-05-12 04:53:49', '2026-05-12 04:53:49'),
(495, 3, 1784, 'issue', -1, 0, ' | Кому: амб,1-5', NULL, NULL, '2026-05-12', '2026-05-12 04:55:28', '2026-05-12 04:55:28'),
(496, 3, 1793, 'issue', -1, 13, ' | Кому: амб,1-5', NULL, NULL, '2026-05-12', '2026-05-12 04:56:18', '2026-05-12 04:56:18'),
(497, 3, 1786, 'issue', -1, 1, ' | Кому: амб,1-5', NULL, NULL, '2026-05-12', '2026-05-12 04:57:47', '2026-05-12 04:57:47'),
(498, 3, 1877, 'issue', -1, 2, ' | Кому: амб,1-5', NULL, NULL, '2026-05-12', '2026-05-12 04:59:01', '2026-05-12 04:59:01'),
(499, 3, 1787, 'issue', -1, 3, ' | Кому: амб,1-5', NULL, NULL, '2026-05-12', '2026-05-12 04:59:47', '2026-05-12 04:59:47'),
(500, 1, 1843, 'issue', -1, 10, ' (Видано: к236 Житомирська, Тополова)', NULL, NULL, '2026-05-13', '2026-05-13 03:41:04', '2026-05-13 03:41:04'),
(501, 3, 1878, 'issue', -1, 9, ' | Кому: каб,№48', NULL, NULL, '2026-05-18', '2026-05-18 05:47:37', '2026-05-18 05:47:37'),
(502, 3, 1820, 'issue', -2, 17, ' | Кому: амб,1-5', NULL, NULL, '2026-05-18', '2026-05-18 05:52:38', '2026-05-18 05:52:38'),
(503, 3, 1823, 'issue', -1, 16, ' (Видано: амб,1-5 2поверх)', NULL, NULL, '2026-05-18', '2026-05-18 05:55:41', '2026-05-18 05:55:41'),
(504, 3, 1806, 'issue', -24, 134, ' (Видано: амб,1-5)', NULL, NULL, '2026-05-18', '2026-05-18 06:17:44', '2026-05-18 06:17:44'),
(505, 3, 1807, 'issue', -32, 64, ' (Видано: амб,1-5)', NULL, NULL, '2026-05-18', '2026-05-18 06:26:31', '2026-05-18 06:26:31'),
(506, 3, 2051, 'receipt', 36, 36, 'Початковий залишок', NULL, NULL, '2026-05-18', '2026-05-18 06:57:28', '2026-05-18 06:57:28'),
(507, 3, 2051, 'issue', -5, 31, ' | Кому: амб,6-10 лабораторія 3пов,', NULL, NULL, '2026-05-18', '2026-05-18 07:03:14', '2026-05-18 07:03:14'),
(508, 3, 2052, 'receipt', 15, 15, 'Початковий залишок', NULL, NULL, '2026-05-18', '2026-05-18 07:29:00', '2026-05-18 07:29:00'),
(509, 3, 2052, 'issue', -10, 5, ' | Кому: амб,1-5', NULL, NULL, '2026-05-18', '2026-05-18 07:29:50', '2026-05-18 07:29:50'),
(510, 3, 2052, 'issue', -4, 1, ' | Кому: амб,6-10 лабораторія 3пов,', NULL, NULL, '2026-05-18', '2026-05-18 07:30:45', '2026-05-18 07:30:45'),
(511, 1, 1918, 'issue', -1, 3, ' | Кому: Секретар к55', NULL, NULL, '2026-05-19', '2026-05-19 05:36:28', '2026-05-19 05:36:28'),
(512, 1, 1843, 'issue', -1, 9, ' | Кому: к302 Бугаївська (Ярослав)', NULL, NULL, '2026-05-19', '2026-05-19 05:52:09', '2026-05-19 05:52:09'),
(513, 3, 1892, 'issue', -10, 10, ' | Кому: амб,6-10 лобараторія 3пов,', NULL, NULL, '2026-05-20', '2026-05-20 02:42:56', '2026-05-20 02:42:56'),
(514, 3, 1803, 'issue', -1, 55, ' | Кому: каб,№46', NULL, NULL, '2026-05-20', '2026-05-20 02:51:49', '2026-05-20 02:51:49'),
(515, 3, 1803, 'issue', -1, 54, ' | Кому: каб,№46', NULL, NULL, '2026-05-20', '2026-05-20 02:51:49', '2026-05-20 02:51:49'),
(516, 3, 1803, 'issue', -1, 53, ' (Видано: каб,№49)', NULL, NULL, '2026-05-20', '2026-05-20 02:52:55', '2026-05-20 02:52:55'),
(517, 3, 1871, 'issue', -1, 7, ' | Кому: амб,6-10', NULL, NULL, '2026-05-20', '2026-05-20 02:54:03', '2026-05-20 02:54:03'),
(518, 3, 1981, 'issue', -1, 4, ' | Кому: амб,6-10', NULL, NULL, '2026-05-20', '2026-05-20 02:54:51', '2026-05-20 02:54:51'),
(519, 3, 1866, 'issue', -1, 4, ' | Кому: амб,6-10', NULL, NULL, '2026-05-20', '2026-05-20 02:55:56', '2026-05-20 02:55:56'),
(520, 3, 1995, 'issue', -10, 0, ' | Кому: амб,6-10 лобараторія 3пов,', NULL, NULL, '2026-05-20', '2026-05-20 03:40:17', '2026-05-20 03:40:17'),
(521, 3, 1881, 'issue', -1, 9, ' | Кому: амб,6-10 лобараторія 3пов,', NULL, NULL, '2026-05-20', '2026-05-20 03:41:41', '2026-05-20 03:41:41'),
(522, 3, 1970, 'issue', -2, 11, ' | Кому: амб,6-10 лобараторія 3пов,', NULL, NULL, '2026-05-21', '2026-05-21 04:07:47', '2026-05-21 04:07:47'),
(523, 3, 1884, 'issue', -2, 13, ' | Кому: амб,6-10 лобараторія 3пов,', NULL, NULL, '2026-05-21', '2026-05-21 04:08:29', '2026-05-21 04:08:29'),
(524, 3, 1840, 'issue', -1, 9, ' | Кому: амб,6-10', NULL, NULL, '2026-05-21', '2026-05-21 05:00:57', '2026-05-21 05:00:57'),
(525, 3, 1824, 'issue', -1, 9, ' | Кому: амб,6-10', NULL, NULL, '2026-05-21', '2026-05-21 05:01:27', '2026-05-21 05:01:27'),
(526, 3, 1803, 'issue', -1, 52, ' | Кому: каб№15', NULL, NULL, '2026-05-25', '2026-05-25 04:23:31', '2026-05-25 04:23:31'),
(527, 3, 1803, 'issue', -5, 47, ' | Кому: каб,№52', NULL, NULL, '2026-05-26', '2026-05-26 03:25:55', '2026-05-26 03:25:55'),
(528, 3, 1824, 'issue', -1, 8, ' | Кому: амб,6-10', NULL, NULL, '2026-05-26', '2026-05-26 03:29:52', '2026-05-26 03:29:52'),
(529, 3, 1997, 'issue', -1, 0, ' (Видано: амб,6-10)', NULL, NULL, '2026-05-26', '2026-05-26 04:28:18', '2026-05-26 04:28:18'),
(530, 3, 1885, 'issue', -2, 8, ' | Кому: амб,6-10 дежурн, каб,', NULL, NULL, '2026-05-26', '2026-05-26 04:32:36', '2026-05-26 04:32:36'),
(531, 3, 1885, 'issue', -3, 5, ' | Кому: амб,6-10 каб№216', NULL, NULL, '2026-05-26', '2026-05-26 04:33:49', '2026-05-26 04:33:49'),
(532, 3, 1885, 'issue', -2, 3, ' | Кому: амб,6-10 лобараторія 3пов,', NULL, NULL, '2026-05-26', '2026-05-26 04:34:17', '2026-05-26 04:34:17'),
(533, 3, 1881, 'issue', -1, 8, ' | Кому: амб,6-10 лобараторія 3пов,', NULL, NULL, '2026-05-27', '2026-05-27 03:23:54', '2026-05-27 03:23:54'),
(534, 3, 1787, 'issue', -1, 2, ' (Видано: амб,1-5)', NULL, NULL, '2026-05-27', '2026-05-27 03:32:12', '2026-05-27 03:32:12'),
(535, 3, 1803, 'issue', -5, 42, ' | Кому: амб,6-10 Грінєнко', NULL, NULL, '2026-05-27', '2026-05-27 04:24:47', '2026-05-27 04:24:47'),
(536, 3, 1924, 'issue', -1, 1, ' | Кому: каб№55 сейф маленький', NULL, NULL, '2026-05-27', '2026-05-27 04:33:18', '2026-05-27 04:33:18'),
(537, 3, 1793, 'issue', -1, 12, ' | Кому: амб,1-5', NULL, NULL, '2026-05-27', '2026-05-27 04:36:14', '2026-05-27 04:36:14'),
(538, 1, 1843, 'issue', -1, 8, ' | Кому: Черговий каб.122 Житомирська, 2', NULL, NULL, '2026-05-27', '2026-05-27 06:46:11', '2026-05-27 06:46:11'),
(539, 3, 1803, 'issue', -2, 40, ' | Кому: каб№50', NULL, NULL, '2026-05-29', '2026-05-29 09:51:05', '2026-05-29 09:51:05'),
(540, 3, 2054, 'receipt', 5, 5, 'Початковий залишок', NULL, NULL, '2026-06-01', '2026-06-01 08:17:03', '2026-06-01 08:17:03'),
(541, 3, 1887, 'issue', -3, 12, ' | Кому: амб,6-10', NULL, NULL, '2026-06-01', '2026-06-01 08:19:52', '2026-06-01 08:19:52'),
(542, 3, 1855, 'issue', -1, 1, ' | Кому: амб,6-10', NULL, NULL, '2026-06-01', '2026-06-01 08:23:14', '2026-06-01 08:23:14'),
(543, 3, 1971, 'issue', -4, 100, ' | Кому: амб,1-5', NULL, NULL, '2026-06-01', '2026-06-01 10:17:33', '2026-06-01 10:17:33'),
(544, 3, 1885, 'issue', -2, 1, ' (Видано: амб,6-10)', NULL, NULL, '2026-06-01', '2026-06-01 10:22:51', '2026-06-01 10:22:51'),
(545, 3, 1822, 'issue', -5, 35, ' (Видано: амб,1-5)', NULL, NULL, '2026-06-01', '2026-06-01 10:28:39', '2026-06-01 10:28:39'),
(546, 3, 1800, 'issue', -1, 0, ' | Кому: амб,1-5', NULL, NULL, '2026-06-02', '2026-06-02 10:32:34', '2026-06-02 10:32:34'),
(547, 3, 1791, 'issue', -1, 1, ' | Кому: амб,1-5', NULL, NULL, '2026-06-02', '2026-06-02 10:39:19', '2026-06-02 10:39:19'),
(548, 3, 1828, 'issue', -14, 16, ' | Кому: амб,1-5', NULL, NULL, '2026-06-02', '2026-06-02 10:47:37', '2026-06-02 10:47:37'),
(549, 3, 1830, 'issue', -13, 34, ' | Кому: амб,1-5', NULL, NULL, '2026-06-02', '2026-06-02 10:48:58', '2026-06-02 10:48:58'),
(550, 3, 1829, 'issue', -3, 12, ' | Кому: амб,1-5', NULL, NULL, '2026-06-02', '2026-06-02 10:52:01', '2026-06-02 10:52:01'),
(551, 3, 1807, 'issue', -16, 48, ' | Кому: амб,6-10', NULL, NULL, '2026-06-03', '2026-06-03 04:18:11', '2026-06-03 04:18:11'),
(552, 3, 1807, 'issue', -48, 0, ' | Кому: амб,1-5', NULL, NULL, '2026-06-03', '2026-06-03 04:18:54', '2026-06-03 04:18:54'),
(553, 3, 2052, 'issue', -1, 0, ' (Видано: амб,1-5)', NULL, NULL, '2026-06-03', '2026-06-03 05:00:35', '2026-06-03 05:00:35'),
(554, 3, 1831, 'issue', -1, 7, ' | Кому: амб,1-5', NULL, NULL, '2026-06-03', '2026-06-03 05:11:59', '2026-06-03 05:11:59'),
(555, 3, 1832, 'issue', -2, 10, ' (Видано: амб,6-10)', NULL, NULL, '2026-06-03', '2026-06-03 05:18:53', '2026-06-03 05:18:53'),
(556, 3, 1833, 'issue', -20, 100, ' (Видано: амб,1-5)', NULL, NULL, '2026-06-03', '2026-06-03 05:34:00', '2026-06-03 05:34:00'),
(557, 1, 1851, 'transfer', 1, 1, 'Переміщення: Підвал → 133 (Відкриття кабінету скринінгу)', NULL, NULL, '2026-06-04', '2026-06-04 04:16:00', '2026-06-04 04:16:00'),
(558, 1, 1843, 'issue', -1, 7, ' | Кому: К133 житомирская (скрининг)', NULL, NULL, '2026-06-04', '2026-06-04 04:51:13', '2026-06-04 04:51:13'),
(559, 3, 2017, 'issue', -5, 15, ' (Видано: амб,1-5)', NULL, NULL, '2026-06-04', '2026-06-04 06:03:20', '2026-06-04 06:03:20'),
(560, 3, 1823, 'issue', -6, 10, ' (Видано: амб,1-5)', NULL, NULL, '2026-06-04', '2026-06-04 06:08:55', '2026-06-04 06:08:55'),
(561, 3, 1808, 'issue', -29, 176, ' (Видано: амб,6-10)', NULL, NULL, '2026-06-04', '2026-06-04 06:58:02', '2026-06-04 06:58:02'),
(562, 3, 1806, 'issue', -36, 98, ' (Видано: амб,1-5)', NULL, NULL, '2026-06-04', '2026-06-04 07:13:54', '2026-06-04 07:13:54'),
(563, 3, 1806, 'issue', -16, 82, ' (Видано: амб,6-10)', NULL, NULL, '2026-06-04', '2026-06-04 07:14:24', '2026-06-04 07:14:24'),
(564, 3, 1806, 'issue', -16, 66, ' (Видано: амб,6-10)', NULL, NULL, '2026-06-04', '2026-06-04 07:14:43', '2026-06-04 07:14:43'),
(565, 1, 1843, 'issue', -1, 6, ' | Кому: К9 скринінг', NULL, NULL, '2026-06-05', '2026-06-05 04:14:50', '2026-06-05 04:14:50'),
(566, 3, 1883, 'issue', -6, 14, ' (Видано: амб,6-10 лобараторія 3пов,)', NULL, NULL, '2026-06-05', '2026-06-05 08:38:00', '2026-06-05 08:38:00'),
(567, 3, 1883, 'issue', -9, 5, ' (Видано: амб,6-10)', NULL, NULL, '2026-06-05', '2026-06-05 08:48:26', '2026-06-05 08:48:26'),
(568, 3, 1970, 'issue', -1, 10, ' | Кому: амб,1-5', NULL, NULL, '2026-06-05', '2026-06-05 08:50:40', '2026-06-05 08:50:40'),
(569, 3, 1804, 'issue', -1, 18, ' (Видано: каб,№46)', NULL, NULL, '2026-06-05', '2026-06-05 08:52:59', '2026-06-05 08:52:59'),
(570, 3, 2016, 'issue', -1, 4, ' | Кому: амб,1-5 дальн,інвалід, туалет', NULL, NULL, '2026-06-05', '2026-06-05 08:57:23', '2026-06-05 08:57:23'),
(571, 1, 1843, 'issue', -1, 5, ' | Кому: К28 гайдара', NULL, NULL, '2026-06-17', '2026-06-17 09:28:21', '2026-06-17 09:28:21'),
(572, 1, 1843, 'issue', -1, 4, ' | Кому: К52 Гайдара бухгалтерия', NULL, NULL, '2026-06-17', '2026-06-17 09:30:01', '2026-06-17 09:30:01'),
(573, 3, 2055, 'receipt', 4, 4, 'Оприходовано з заявки ZAY-2026-000046', 'ZAY-2026-000046', NULL, '2026-06-23', '2026-06-23 08:19:41', '2026-06-23 08:19:41'),
(574, 3, 2056, 'receipt', 4, 4, 'Оприходовано з заявки ZAY-2026-000046', 'ZAY-2026-000046', NULL, '2026-06-23', '2026-06-23 08:19:41', '2026-06-23 08:19:41'),
(575, 3, 2057, 'receipt', 2, 2, 'Оприходовано з заявки ZAY-2026-000037', 'ZAY-2026-000037', NULL, '2026-06-23', '2026-06-23 08:23:27', '2026-06-23 08:23:27'),
(576, 3, 2058, 'receipt', 3, 3, 'Оприходовано з заявки ZAY-2026-000037', 'ZAY-2026-000037', NULL, '2026-06-23', '2026-06-23 08:23:27', '2026-06-23 08:23:27'),
(577, 3, 2059, 'receipt', 5, 5, 'Оприходовано з заявки ZAY-2026-000047', 'ZAY-2026-000047', NULL, '2026-06-23', '2026-06-23 08:28:13', '2026-06-23 08:28:13'),
(578, 3, 2060, 'receipt', 3, 3, 'Оприходовано з заявки ZAY-2026-000047', 'ZAY-2026-000047', NULL, '2026-06-23', '2026-06-23 08:28:13', '2026-06-23 08:28:13'),
(579, 3, 2061, 'receipt', 5, 5, 'Оприходовано з заявки ZAY-2026-000047', 'ZAY-2026-000047', NULL, '2026-06-23', '2026-06-23 08:28:13', '2026-06-23 08:28:13'),
(580, 3, 2056, 'issue', -1, 3, ' | Кому: Скринінг Житомирська к133', NULL, NULL, '2026-06-23', '2026-06-23 08:29:13', '2026-06-23 08:29:13'),
(581, 3, 2055, 'issue', -1, 3, ' | Кому: Скринінг Житомирська к133', NULL, NULL, '2026-06-23', '2026-06-23 08:29:40', '2026-06-23 08:29:40'),
(582, 3, 2059, 'issue', -1, 4, ' | Кому: к 57 Секретар', NULL, NULL, '2026-06-23', '2026-06-23 08:31:27', '2026-06-23 08:31:27'),
(583, 3, 2059, 'issue', -1, 3, ' | Кому: к52 Комарова', NULL, NULL, '2026-06-23', '2026-06-23 08:31:46', '2026-06-23 08:31:46'),
(584, 3, 1688, 'issue', -1, 0, ' | Кому: к45\\1 скринінг', NULL, NULL, '2026-06-23', '2026-06-23 10:09:57', '2026-06-23 10:09:57'),
(585, 3, 1786, 'receipt', 10, 11, NULL, '539', NULL, '2026-06-24', '2026-06-24 08:36:05', '2026-06-24 08:36:05'),
(586, 3, 1784, 'receipt', 24, 24, NULL, '539', NULL, '2026-06-24', '2026-06-24 08:52:30', '2026-06-24 08:52:30'),
(587, 3, 1787, 'receipt', 14, 16, NULL, '539', NULL, '2026-06-24', '2026-06-24 08:54:27', '2026-06-24 08:54:27'),
(588, 3, 1877, 'receipt', 8, 10, NULL, '539', NULL, '2026-06-24', '2026-06-24 08:55:44', '2026-06-24 08:55:44'),
(589, 3, 1790, 'receipt', 10, 12, NULL, '539', NULL, '2026-06-24', '2026-06-24 08:56:57', '2026-06-24 08:56:57'),
(590, 3, 1795, 'receipt', 24, 24, NULL, '539', NULL, '2026-06-24', '2026-06-24 08:57:34', '2026-06-24 08:57:34'),
(591, 3, 1793, 'receipt', 60, 72, NULL, '539', NULL, '2026-06-24', '2026-06-24 08:58:17', '2026-06-24 08:58:17'),
(592, 3, 1799, 'receipt', 8, 8, NULL, '539', NULL, '2026-06-24', '2026-06-24 08:59:10', '2026-06-24 08:59:10'),
(593, 3, 1791, 'receipt', 14, 15, NULL, '539', NULL, '2026-06-24', '2026-06-24 08:59:52', '2026-06-24 08:59:52'),
(594, 3, 1794, 'receipt', 6, 6, NULL, '539', NULL, '2026-06-24', '2026-06-24 09:00:36', '2026-06-24 09:00:36'),
(595, 3, 1848, 'receipt', 8, 8, NULL, '539', NULL, '2026-06-24', '2026-06-24 09:01:49', '2026-06-24 09:01:49'),
(596, 3, 1797, 'receipt', 24, 24, NULL, '539', NULL, '2026-06-24', '2026-06-24 09:02:23', '2026-06-24 09:02:23'),
(597, 3, 1786, 'issue', -2, 9, ' | Кому: амб,6-10', NULL, NULL, '2026-06-24', '2026-06-24 09:19:36', '2026-06-24 09:19:36'),
(598, 3, 1784, 'issue', -2, 22, ' | Кому: амб,6-10', NULL, NULL, '2026-06-24', '2026-06-24 10:03:16', '2026-06-24 10:03:16'),
(599, 3, 1787, 'issue', -2, 14, ' | Кому: амб,6-10', NULL, NULL, '2026-06-24', '2026-06-24 10:04:29', '2026-06-24 10:04:29'),
(600, 3, 1877, 'issue', -2, 8, ' | Кому: амб,6-10', NULL, NULL, '2026-06-24', '2026-06-24 10:08:51', '2026-06-24 10:08:51'),
(601, 3, 1790, 'issue', -2, 10, ' | Кому: амб,6-10', NULL, NULL, '2026-06-24', '2026-06-24 10:12:45', '2026-06-24 10:12:45'),
(602, 3, 1795, 'issue', -1, 23, ' | Кому: амб,1-5', NULL, NULL, '2026-06-24', '2026-06-24 10:17:58', '2026-06-24 10:17:58'),
(603, 3, 1795, 'issue', -2, 21, ' | Кому: амб,6-10', NULL, NULL, '2026-06-24', '2026-06-24 10:18:26', '2026-06-24 10:18:26'),
(604, 3, 1793, 'issue', -2, 70, ' (Видано: амб,1-5)', NULL, NULL, '2026-06-24', '2026-06-24 10:30:06', '2026-06-24 10:30:06'),
(605, 3, 1793, 'issue', -12, 58, ' (Видано: амб,6-10)', NULL, NULL, '2026-06-24', '2026-06-24 10:30:28', '2026-06-24 10:30:28'),
(606, 3, 1799, 'issue', -2, 6, ' (Видано: амб,6-10)', NULL, NULL, '2026-06-24', '2026-06-24 10:34:09', '2026-06-24 10:34:09'),
(607, 3, 1791, 'issue', -2, 13, ' (Видано: амб,6-10)', NULL, NULL, '2026-06-24', '2026-06-24 10:47:49', '2026-06-24 10:47:49'),
(608, 3, 1794, 'issue', -2, 4, ' | Кому: амб,6-10', NULL, NULL, '2026-06-24', '2026-06-24 10:56:12', '2026-06-24 10:56:12'),
(609, 3, 1848, 'issue', -1, 7, ' | Кому: амб,6-10', NULL, NULL, '2026-06-24', '2026-06-24 11:03:12', '2026-06-24 11:03:12'),
(610, 3, 1797, 'issue', -6, 18, ' (Видано: амб,6-10)', NULL, NULL, '2026-06-24', '2026-06-24 11:06:31', '2026-06-24 11:06:31'),
(611, 3, 1831, 'issue', -4, 3, ' | Кому: амб,6-10 скрінінг', NULL, NULL, '2026-06-24', '2026-06-24 11:17:35', '2026-06-24 11:17:35'),
(612, 3, 1832, 'issue', -4, 6, ' | Кому: амб,6-10 скрінінг', NULL, NULL, '2026-06-24', '2026-06-24 11:18:26', '2026-06-24 11:18:26'),
(613, 3, 1804, 'issue', -2, 16, ' (Видано: каб,№9)', NULL, NULL, '2026-06-24', '2026-06-24 11:21:58', '2026-06-24 11:21:58'),
(614, 3, 1804, 'issue', -1, 15, ' (Видано: ден,стационар)', NULL, NULL, '2026-06-24', '2026-06-24 11:22:51', '2026-06-24 11:22:51'),
(615, 3, 1804, 'issue', -4, 11, ' (Видано: амб,6-10 скрінінг)', NULL, NULL, '2026-06-24', '2026-06-24 11:23:30', '2026-06-24 11:23:30'),
(616, 3, 1803, 'issue', -1, 39, ' (Видано: каб,№36 Бошко)', NULL, NULL, '2026-06-24', '2026-06-24 11:26:13', '2026-06-24 11:26:13'),
(617, 3, 1803, 'issue', -1, 38, ' (Видано: каб,№33 Пужанська)', NULL, NULL, '2026-06-24', '2026-06-24 11:27:06', '2026-06-24 11:27:06'),
(618, 3, 1803, 'issue', -3, 35, ' (Видано: каб,№9 Полетаєва)', NULL, NULL, '2026-06-24', '2026-06-24 11:28:33', '2026-06-24 11:28:33'),
(619, 3, 1803, 'issue', -2, 33, ' (Видано: каб,№55 Конюшенко)', NULL, NULL, '2026-06-24', '2026-06-24 11:29:28', '2026-06-24 11:29:28'),
(620, 3, 1803, 'issue', -1, 32, ' (Видано: каб,№57 Прядко)', NULL, NULL, '2026-06-24', '2026-06-24 11:30:14', '2026-06-24 11:30:14'),
(621, 3, 1803, 'issue', -1, 31, ' (Видано: каб,№45/2 Ненашев)', NULL, NULL, '2026-06-24', '2026-06-24 11:31:13', '2026-06-24 11:31:13'),
(622, 3, 1803, 'issue', -1, 30, ' (Видано: каб,№27 Фелонюк)', NULL, NULL, '2026-06-24', '2026-06-24 11:32:06', '2026-06-24 11:32:06'),
(623, 3, 1803, 'issue', -1, 29, ' (Видано: каб,№23 скрінінг)', NULL, NULL, '2026-06-24', '2026-06-24 11:33:17', '2026-06-24 11:33:17'),
(624, 3, 1813, 'issue', -1, 17, ' | Кому: 1 пол,', NULL, NULL, '2026-06-25', '2026-06-25 04:29:12', '2026-06-25 04:29:12'),
(625, 3, 1875, 'issue', -1, 9, ' | Кому: каб,№33', NULL, NULL, '2026-06-25', '2026-06-25 04:30:55', '2026-06-25 04:30:55'),
(626, 3, 1878, 'issue', -1, 8, ' | Кому: каб,№33', NULL, NULL, '2026-06-25', '2026-06-25 04:33:27', '2026-06-25 04:33:27'),
(627, 3, 2018, 'issue', -1, 5, ' | Кому: туалет 1 поверх', NULL, NULL, '2026-06-25', '2026-06-25 04:41:17', '2026-06-25 04:41:17'),
(628, 3, 1547, 'issue', -1, 0, ' | Кому: амб,6-10 скрінінг', NULL, NULL, '2026-06-25', '2026-06-25 05:15:40', '2026-06-25 05:15:40'),
(629, 3, 1806, 'issue', -16, 50, 'видано на 8,06 (Видано: амб,1-5)', NULL, NULL, '2026-06-25', '2026-06-25 05:43:32', '2026-06-25 05:43:32'),
(630, 3, 1806, 'issue', -44, 6, 'видано на 16,06 на 23,06 (Видано: амб,1-5)', NULL, NULL, '2026-06-25', '2026-06-25 05:49:49', '2026-06-25 05:49:49'),
(631, 3, 1806, 'receipt', 448, 454, NULL, 'ЕР-540', NULL, '2026-06-25', '2026-06-25 05:52:51', '2026-06-25 05:52:51'),
(632, 3, 1806, 'issue', -16, 438, 'видано на 1,06 (Видано: амб,1-5)', NULL, NULL, '2026-06-25', '2026-06-25 05:55:11', '2026-06-25 05:55:11'),
(633, 3, 1806, 'issue', -80, 358, ' (Видано: амб,6-10)', NULL, NULL, '2026-06-25', '2026-06-25 06:08:30', '2026-06-25 06:08:30'),
(634, 3, 1807, 'receipt', 624, 624, 'Лєста', 'ЕР-540', NULL, '2026-06-25', '2026-06-25 06:18:50', '2026-06-25 06:18:50'),
(635, 3, 1807, 'issue', -48, 576, ' | Кому: амб,6-10', NULL, NULL, '2026-06-25', '2026-06-25 06:20:09', '2026-06-25 06:20:09'),
(636, 3, 1807, 'issue', -48, 528, 'видано на 16,-23,06 (Видано: амб,1-5)', NULL, NULL, '2026-06-25', '2026-06-25 06:23:25', '2026-06-25 06:23:25'),
(637, 3, 1807, 'issue', -96, 432, ' (Видано: амб,6-10)', NULL, NULL, '2026-06-25', '2026-06-25 06:29:53', '2026-06-25 06:29:53'),
(638, 3, 1878, 'issue', -1, 7, ' | Кому: амб,6-10 Бугаївка', NULL, NULL, '2026-06-25', '2026-06-25 07:15:31', '2026-06-25 07:15:31'),
(639, 3, 1804, 'issue', -1, 10, ' (Видано: каб,№55 Конюшенко)', NULL, NULL, '2026-06-25', '2026-06-25 07:17:16', '2026-06-25 07:17:16'),
(640, 3, 1804, 'issue', -1, 9, ' (Видано: каб,№57 Прядко)', NULL, NULL, '2026-06-25', '2026-06-25 07:17:35', '2026-06-25 07:17:35'),
(641, 3, 1804, 'issue', -1, 8, ' (Видано: каб,№46 Коваль)', NULL, NULL, '2026-06-25', '2026-06-25 07:18:08', '2026-06-25 07:18:08'),
(642, 3, 1804, 'issue', -1, 7, ' (Видано: амб,6-10 Фісенко)', NULL, NULL, '2026-06-25', '2026-06-25 07:19:01', '2026-06-25 07:19:01'),
(643, 3, 1803, 'issue', -1, 28, ' (Видано: каб,№58 Золота)', NULL, NULL, '2026-06-25', '2026-06-25 07:22:22', '2026-06-25 07:22:22'),
(644, 3, 1803, 'issue', -1, 27, ' (Видано: каб,№46 Коваль)', NULL, NULL, '2026-06-25', '2026-06-25 07:22:55', '2026-06-25 07:22:55'),
(645, 3, 1803, 'issue', -5, 22, ' (Видано: амб,6-10)', NULL, NULL, '2026-06-25', '2026-06-25 07:23:24', '2026-06-25 07:23:24'),
(646, 3, 1804, 'issue', -2, 5, ' (Видано: каб,№52)', NULL, NULL, '2026-06-25', '2026-06-25 07:55:49', '2026-06-25 07:55:49'),
(647, 3, 1835, 'issue', -5, 10, ' (Видано: каб,№52)', NULL, NULL, '2026-06-25', '2026-06-25 07:57:05', '2026-06-25 07:57:05'),
(648, 3, 1835, 'issue', -1, 9, ' (Видано: каб,№35 Скрібантовіч)', NULL, NULL, '2026-06-25', '2026-06-25 08:05:23', '2026-06-25 08:05:23'),
(649, 3, 1835, 'issue', -2, 7, ' (Видано: каб,№33 Пужанська)', NULL, NULL, '2026-06-25', '2026-06-25 08:06:04', '2026-06-25 08:06:04'),
(650, 3, 1878, 'issue', -1, 6, ' (Видано: амб,6-10 Болгарська каб Скрінінга)', NULL, NULL, '2026-06-30', '2026-06-30 06:16:22', '2026-06-30 06:16:22'),
(651, 3, 1822, 'issue', -2, 33, ' | Кому: амб,1-5', NULL, NULL, '2026-06-30', '2026-06-30 06:18:42', '2026-06-30 06:18:42'),
(652, 3, 1814, 'issue', -10, 10, ' | Кому: амб,1-5', NULL, NULL, '2026-06-30', '2026-06-30 06:26:13', '2026-06-30 06:26:13'),
(653, 3, 1816, 'issue', -10, 10, ' | Кому: амб,1-5', NULL, NULL, '2026-06-30', '2026-06-30 06:27:16', '2026-06-30 06:27:16'),
(654, 3, 1817, 'issue', -10, 10, ' | Кому: амб,1-5', NULL, NULL, '2026-06-30', '2026-06-30 06:27:48', '2026-06-30 06:27:48'),
(655, 3, 1818, 'issue', -10, 10, '', NULL, NULL, '2026-06-30', '2026-06-30 06:28:12', '2026-06-30 06:28:12'),
(656, 3, 1823, 'issue', -3, 7, ' (Видано: амб,1-5)', NULL, NULL, '2026-06-30', '2026-06-30 06:31:43', '2026-06-30 06:31:43'),
(657, 3, 1807, 'issue', -24, 408, ' | Кому: амб,1-5', NULL, NULL, '2026-06-30', '2026-06-30 06:35:37', '2026-06-30 06:35:37'),
(658, 3, 1806, 'issue', -26, 332, ' (Видано: амб,1-5)', NULL, NULL, '2026-06-30', '2026-06-30 06:39:48', '2026-06-30 06:39:48'),
(659, 3, 1784, 'issue', -2, 20, ' | Кому: амб,6-10', NULL, NULL, '2026-06-30', '2026-06-30 06:46:09', '2026-06-30 06:46:09'),
(660, 3, 1785, 'issue', -1, 0, ' (Видано: амб,1-5)', NULL, NULL, '2026-06-30', '2026-06-30 06:51:01', '2026-06-30 06:51:01'),
(661, 3, 1787, 'issue', -2, 12, ' | Кому: амб,6-10', NULL, NULL, '2026-06-30', '2026-06-30 07:06:16', '2026-06-30 07:06:16'),
(662, 3, 1793, 'issue', -1, 57, ' | Кому: амб,1-5', NULL, NULL, '2026-06-30', '2026-06-30 07:15:01', '2026-06-30 07:15:01'),
(663, 3, 1848, 'issue', -1, 6, ' (Видано: амб,1-5)', NULL, NULL, '2026-06-30', '2026-06-30 07:23:30', '2026-06-30 07:23:30'),
(664, 3, 1797, 'issue', -4, 14, ' (Видано: амб,1-5)', NULL, NULL, '2026-06-30', '2026-06-30 07:27:16', '2026-06-30 07:27:16'),
(665, 1, 1843, 'issue', -1, 3, ' | Кому: К307 скринінг', NULL, NULL, '2026-06-30', '2026-06-30 07:58:06', '2026-06-30 07:58:06'),
(666, 1, 1843, 'issue', -1, 2, ' | Кому: К35', NULL, NULL, '2026-06-30', '2026-06-30 07:58:39', '2026-06-30 07:58:39'),
(667, 3, 2028, 'issue', -10, 10, ' (Видано: амб,1-5 сходинки головного входу)', NULL, NULL, '2026-06-30', '2026-06-30 08:51:22', '2026-06-30 08:51:22'),
(668, 3, 2027, 'issue', -2, 28, ' | Кому: амб,1-5', NULL, NULL, '2026-06-30', '2026-06-30 08:52:56', '2026-06-30 08:52:56'),
(669, 3, 1808, 'issue', -17, 159, ' | Кому: амб,1-5', NULL, NULL, '2026-06-30', '2026-06-30 08:54:26', '2026-06-30 08:54:26'),
(670, 3, 1808, 'issue', -17, 142, ' | Кому: амб,1-5', NULL, NULL, '2026-06-30', '2026-06-30 08:54:27', '2026-06-30 08:54:27'),
(671, 1, 2062, 'receipt', 4, 4, 'Оприходовано з заявки ZAY-2026-000042', 'ZAY-2026-000042', NULL, '2026-06-30', '2026-06-30 09:21:04', '2026-06-30 09:21:04'),
(672, 1, 2063, 'receipt', 2, 2, 'Оприходовано з заявки ZAY-2026-000042', 'ZAY-2026-000042', NULL, '2026-06-30', '2026-06-30 09:21:04', '2026-06-30 09:21:04'),
(673, 1, 2064, 'receipt', 2, 2, 'Оприходовано з заявки ZAY-2026-000042', 'ZAY-2026-000042', NULL, '2026-06-30', '2026-06-30 09:21:04', '2026-06-30 09:21:04'),
(674, 1, 2065, 'receipt', 2, 2, 'Оприходовано з заявки ZAY-2026-000042', 'ZAY-2026-000042', NULL, '2026-06-30', '2026-06-30 09:21:04', '2026-06-30 09:21:04'),
(675, 1, 2066, 'receipt', 2, 2, 'Оприходовано з заявки ZAY-2026-000042', 'ZAY-2026-000042', NULL, '2026-06-30', '2026-06-30 09:21:04', '2026-06-30 09:21:04'),
(676, 1, 2067, 'receipt', 5, 5, 'Оприходовано з заявки ZAY-2026-000041', 'ZAY-2026-000041', NULL, '2026-06-30', '2026-06-30 09:21:15', '2026-06-30 09:21:15'),
(677, 1, 2068, 'receipt', 5, 5, 'Оприходовано з заявки ZAY-2026-000041', 'ZAY-2026-000041', NULL, '2026-06-30', '2026-06-30 09:21:15', '2026-06-30 09:21:15'),
(678, 1, 2069, 'receipt', 5, 5, 'Оприходовано з заявки ZAY-2026-000041', 'ZAY-2026-000041', NULL, '2026-06-30', '2026-06-30 09:21:15', '2026-06-30 09:21:15'),
(679, 1, 2070, 'receipt', 5, 5, 'Оприходовано з заявки ZAY-2026-000041', 'ZAY-2026-000041', NULL, '2026-06-30', '2026-06-30 09:21:15', '2026-06-30 09:21:15'),
(680, 1, 2071, 'receipt', 5, 5, 'Оприходовано з заявки ZAY-2026-000041', 'ZAY-2026-000041', NULL, '2026-06-30', '2026-06-30 09:21:15', '2026-06-30 09:21:15'),
(681, 1, 2072, 'receipt', 10, 10, 'Оприходовано з заявки ZAY-2026-000039', 'ZAY-2026-000039', NULL, '2026-06-30', '2026-06-30 09:21:28', '2026-06-30 09:21:28'),
(682, 1, 2073, 'receipt', 1, 1, 'Оприходовано з заявки ZAY-2026-000039', 'ZAY-2026-000039', NULL, '2026-06-30', '2026-06-30 09:21:28', '2026-06-30 09:21:28'),
(683, 1, 2074, 'receipt', 1, 1, 'Оприходовано з заявки ZAY-2026-000039', 'ZAY-2026-000039', NULL, '2026-06-30', '2026-06-30 09:21:28', '2026-06-30 09:21:28'),
(684, 1, 2075, 'receipt', 10, 10, 'Оприходовано з заявки ZAY-2026-000039', 'ZAY-2026-000039', NULL, '2026-06-30', '2026-06-30 09:21:28', '2026-06-30 09:21:28'),
(685, 1, 2076, 'receipt', 5, 5, 'Оприходовано з заявки ZAY-2026-000039', 'ZAY-2026-000039', NULL, '2026-06-30', '2026-06-30 09:21:28', '2026-06-30 09:21:28'),
(686, 1, 2077, 'receipt', 5, 5, 'Оприходовано з заявки ZAY-2026-000039', 'ZAY-2026-000039', NULL, '2026-06-30', '2026-06-30 09:21:28', '2026-06-30 09:21:28'),
(687, 1, 2078, 'receipt', 5, 5, 'Оприходовано з заявки ZAY-2026-000039', 'ZAY-2026-000039', NULL, '2026-06-30', '2026-06-30 09:21:28', '2026-06-30 09:21:28'),
(688, 1, 2079, 'receipt', 10, 10, 'Оприходовано з заявки ZAY-2026-000039', 'ZAY-2026-000039', NULL, '2026-06-30', '2026-06-30 09:21:28', '2026-06-30 09:21:28'),
(689, 1, 2080, 'receipt', 10, 10, 'Оприходовано з заявки ZAY-2026-000039', 'ZAY-2026-000039', NULL, '2026-06-30', '2026-06-30 09:21:28', '2026-06-30 09:21:28'),
(690, 1, 2081, 'receipt', 5, 5, 'Оприходовано з заявки ZAY-2026-000039', 'ZAY-2026-000039', NULL, '2026-06-30', '2026-06-30 09:21:28', '2026-06-30 09:21:28'),
(691, 1, 2082, 'receipt', 5, 5, 'Оприходовано з заявки ZAY-2026-000039', 'ZAY-2026-000039', NULL, '2026-06-30', '2026-06-30 09:21:28', '2026-06-30 09:21:28'),
(692, 1, 2083, 'receipt', 10, 10, 'Оприходовано з заявки ZAY-2026-000039', 'ZAY-2026-000039', NULL, '2026-06-30', '2026-06-30 09:21:28', '2026-06-30 09:21:28'),
(693, 1, 1686, 'issue', -1, 0, ' | Кому: К307 Скринінг Болгарська 38', NULL, NULL, '2026-06-30', '2026-06-30 09:22:07', '2026-06-30 09:22:07'),
(694, 1, 2060, 'issue', -1, 2, ' | Кому: к52 Бухгалтерія', NULL, NULL, '2026-06-30', '2026-06-30 09:22:32', '2026-06-30 09:22:32'),
(695, 1, 2061, 'issue', -1, 4, ' | Кому: к 36', NULL, NULL, '2026-06-30', '2026-06-30 09:22:49', '2026-06-30 09:22:49'),
(696, 1, 2059, 'issue', -3, 0, ' (Видано: к52 Комарова)', NULL, NULL, '2026-06-30', '2026-06-30 09:25:29', '2026-06-30 09:25:29'),
(697, 3, 1887, 'issue', -2, 10, ' | Кому: амб,6-10', NULL, NULL, '2026-07-01', '2026-07-01 05:46:30', '2026-07-01 05:46:30'),
(698, 1, 2062, 'issue', -1, 3, ' | Кому: 56 хромченко', NULL, NULL, '2026-07-01', '2026-07-01 06:06:16', '2026-07-01 06:06:16'),
(699, 1, 2062, 'issue', -1, 2, ' | Кому: К39 Татьяна Ивановна', NULL, NULL, '2026-07-01', '2026-07-01 06:07:03', '2026-07-01 06:07:03'),
(700, 1, 2080, 'issue', -1, 9, ' | Кому: К52 Аня', NULL, NULL, '2026-07-01', '2026-07-01 06:30:44', '2026-07-01 06:30:44'),
(701, 1, 2080, 'issue', -1, 8, ' | Кому: К307 скрининг болгарская', NULL, NULL, '2026-07-01', '2026-07-01 06:31:20', '2026-07-01 06:31:20'),
(702, 3, 2082, 'issue', -1, 4, ' | Кому: амб,6-10 фісенко 206', NULL, NULL, '2026-07-01', '2026-07-01 07:01:37', '2026-07-01 07:01:37'),
(703, 3, 2079, 'issue', -1, 9, ' | Кому: амб,6-10 фісенко 206', NULL, NULL, '2026-07-01', '2026-07-01 07:02:17', '2026-07-01 07:02:17'),
(704, 3, 2080, 'issue', -1, 7, ' | Кому: амб,6-10 фісенко 206', NULL, NULL, '2026-07-01', '2026-07-01 07:03:09', '2026-07-01 07:03:09'),
(705, 3, 2055, 'issue', -1, 2, ' | Кому: амб,6-10 фісенко 206', NULL, NULL, '2026-07-01', '2026-07-01 07:04:32', '2026-07-01 07:04:32'),
(706, 3, 2075, 'issue', -1, 9, ' | Кому: амб,6-10 фісенко 206', NULL, NULL, '2026-07-01', '2026-07-01 07:05:54', '2026-07-01 07:05:54'),
(707, 3, 1843, 'issue', -1, 1, ' | Кому: амб,6-10 фісенко 206', NULL, NULL, '2026-07-01', '2026-07-01 07:10:37', '2026-07-01 07:10:37'),
(708, 3, 2076, 'issue', -1, 4, ' | Кому: к40 Гроздєва', NULL, NULL, '2026-07-02', '2026-07-02 04:26:17', '2026-07-02 04:26:17'),
(709, 1, 1311, 'transfer', 1, 1, 'Переміщення: 50 → к307 (скринінг)', NULL, NULL, '2026-07-02', '2026-07-02 04:28:07', '2026-07-02 04:28:07'),
(710, 1, 1350, 'transfer', 1, 1, 'Переміщення: 58 → 106 (скринінг)', NULL, NULL, '2026-07-02', '2026-07-02 04:28:43', '2026-07-02 04:28:43'),
(711, 3, 1803, 'issue', -2, 20, ' (Видано: каб,№52 Комарова)', NULL, NULL, '2026-07-07', '2026-07-07 04:12:51', '2026-07-07 04:12:51'),
(712, 3, 1803, 'issue', -1, 19, ' (Видано: каб,№7 Юрченко)', NULL, NULL, '2026-07-07', '2026-07-07 04:13:58', '2026-07-07 04:13:58'),
(713, 3, 1803, 'issue', -1, 18, ' (Видано: каб,№7а Жарук)', NULL, NULL, '2026-07-07', '2026-07-07 04:14:49', '2026-07-07 04:14:49'),
(714, 3, 1803, 'issue', -2, 16, ' (Видано: амб,6-10)', NULL, NULL, '2026-07-07', '2026-07-07 04:15:58', '2026-07-07 04:15:58'),
(715, 3, 1803, 'issue', -1, 15, ' (Видано: каб,№40 Гроздева)', NULL, NULL, '2026-07-07', '2026-07-07 04:16:53', '2026-07-07 04:16:53'),
(716, 3, 2009, 'issue', -3, 2, ' | Кому: амб,6-10', NULL, NULL, '2026-07-07', '2026-07-07 04:54:33', '2026-07-07 04:54:33'),
(717, 3, 2009, 'issue', -1, 1, ' | Кому: каб№7а', NULL, NULL, '2026-07-07', '2026-07-07 04:55:42', '2026-07-07 04:55:42'),
(718, 3, 1820, 'issue', -1, 16, ' | Кому: амб,1-5 м,м,с,Лобода', NULL, NULL, '2026-07-07', '2026-07-07 05:00:05', '2026-07-07 05:00:05'),
(719, 3, 2018, 'issue', -2, 3, ' | Кому: амб,6-10 скрінінг, узі', NULL, NULL, '2026-07-07', '2026-07-07 05:04:12', '2026-07-07 05:04:12'),
(720, 3, 1970, 'issue', -2, 8, ' | Кому: амб,6-10 електрик', NULL, NULL, '2026-07-07', '2026-07-07 05:23:15', '2026-07-07 05:23:15'),
(721, 3, 1891, 'issue', -2, 4, ' | Кому: амб,6-10', NULL, NULL, '2026-07-07', '2026-07-07 05:25:27', '2026-07-07 05:25:27'),
(722, 3, 1970, 'issue', -2, 6, ' | Кому: амб,6-10 лобараторія 3пов,', NULL, NULL, '2026-07-07', '2026-07-07 05:45:02', '2026-07-07 05:45:02'),
(723, 3, 1881, 'issue', -2, 6, ' (Видано: амб,6-10 лобараторія 3пов,)', NULL, NULL, '2026-07-07', '2026-07-07 05:47:12', '2026-07-07 05:47:12'),
(724, 3, 1895, 'issue', -1, 4, ' (Видано: амб,1-5)', NULL, NULL, '2026-07-07', '2026-07-07 05:58:42', '2026-07-07 05:58:42'),
(725, 3, 1892, 'issue', -1, 9, ' | Кому: амб,6-10', NULL, NULL, '2026-07-07', '2026-07-07 05:59:58', '2026-07-07 05:59:58'),
(726, 3, 1887, 'issue', -2, 8, ' | Кому: амб,6-10 лобараторія 3пов,', NULL, NULL, '2026-07-07', '2026-07-07 06:03:50', '2026-07-07 06:03:50'),
(727, 3, 1894, 'issue', -1, 4, ' (Видано: амб,6-10 електрик)', NULL, NULL, '2026-07-07', '2026-07-07 06:08:03', '2026-07-07 06:08:03'),
(728, 3, 1884, 'issue', -2, 11, ' (Видано: амб,6-10 лобараторія 3пов,)', NULL, NULL, '2026-07-07', '2026-07-07 06:15:22', '2026-07-07 06:15:22'),
(729, 3, 1884, 'issue', -2, 9, ' (Видано: амб,6-10 для кондіционерів)', NULL, NULL, '2026-07-07', '2026-07-07 06:16:35', '2026-07-07 06:16:35'),
(730, 3, 1853, 'issue', -1, 1, ' | Кому: амб,6-10', NULL, NULL, '2026-07-07', '2026-07-07 06:18:29', '2026-07-07 06:18:29'),
(731, 3, 2005, 'issue', -2, 0, ' | Кому: амб,6-10', NULL, NULL, '2026-07-07', '2026-07-07 06:29:09', '2026-07-07 06:29:09'),
(732, 3, 1992, 'issue', -1, 3, ' (Видано: амб,1-5 туалет 2ет)', NULL, NULL, '2026-07-07', '2026-07-07 06:56:37', '2026-07-07 06:56:37'),
(733, 3, 2079, 'issue', -1, 8, ' | Кому: амб,1-5 регістратура', NULL, NULL, '2026-07-07', '2026-07-07 07:22:31', '2026-07-07 07:22:31'),
(734, 3, 1803, 'issue', -1, 14, ' (Видано: каб,№15 Тіщенко)', NULL, NULL, '2026-07-07', '2026-07-07 07:48:43', '2026-07-07 07:48:43'),
(735, 3, 1822, 'issue', -2, 31, ' | Кому: амб,1-5 Садовнік ,Александров', NULL, NULL, '2026-07-07', '2026-07-07 07:51:08', '2026-07-07 07:51:08'),
(736, 3, 1803, 'issue', -1, 13, ' | Кому: каб,№51 Рєутова', NULL, NULL, '2026-07-07', '2026-07-07 09:47:19', '2026-07-07 09:47:19'),
(737, 3, 1795, 'issue', -1, 20, ' | Кому: амб,1-5', NULL, NULL, '2026-07-07', '2026-07-07 09:58:24', '2026-07-07 09:58:24'),
(738, 3, 1794, 'issue', -1, 3, ' | Кому: амб,1-5', NULL, NULL, '2026-07-07', '2026-07-07 10:05:29', '2026-07-07 10:05:29'),
(739, 3, 1786, 'issue', -1, 8, ' | Кому: амб,1-5', NULL, NULL, '2026-07-07', '2026-07-07 10:06:15', '2026-07-07 10:06:15'),
(740, 3, 1803, 'issue', -1, 12, ' (Видано: каб,№49 Петрова)', NULL, NULL, '2026-07-08', '2026-07-08 06:41:21', '2026-07-08 06:41:21'),
(741, 3, 1803, 'issue', -1, 11, ' (Видано: каб,№50 Лічікакі)', NULL, NULL, '2026-07-08', '2026-07-08 06:42:06', '2026-07-08 06:42:06'),
(742, 3, 1803, 'issue', -1, 10, ' (Видано: каб,№59 Маценко)', NULL, NULL, '2026-07-08', '2026-07-08 06:42:49', '2026-07-08 06:42:49'),
(743, 3, 1835, 'issue', -1, 6, ' | Кому: каб№59 Маценко', NULL, NULL, '2026-07-08', '2026-07-08 06:53:57', '2026-07-08 06:53:57'),
(744, 3, 1831, 'issue', -1, 2, ' | Кому: каб,№49 Петрова', NULL, NULL, '2026-07-08', '2026-07-08 06:54:43', '2026-07-08 06:54:43'),
(745, 3, 1832, 'issue', -1, 5, ' | Кому: каб,№49 Петрова', NULL, NULL, '2026-07-08', '2026-07-08 06:55:29', '2026-07-08 06:55:29'),
(746, 3, 1833, 'issue', -10, 90, ' | Кому: каб,№59 Маценко', NULL, NULL, '2026-07-08', '2026-07-08 06:56:30', '2026-07-08 06:56:30'),
(747, 3, 1833, 'issue', -10, 80, ' | Кому: каб,№49 Петрова', NULL, NULL, '2026-07-08', '2026-07-08 06:57:07', '2026-07-08 06:57:07'),
(748, 1, 2063, 'issue', -1, 1, ' | Кому: К29 касса', NULL, NULL, '2026-07-10', '2026-07-10 05:20:38', '2026-07-10 05:20:38'),
(749, 1, 1803, 'issue', -1, 9, ' (Видано: каб,№33 Пужанська)', NULL, NULL, '2026-07-13', '2026-07-13 10:23:52', '2026-07-13 10:23:52'),
(750, 1, 1803, 'issue', -1, 8, ' (Видано: каб,№9 Полетаєва)', NULL, NULL, '2026-07-13', '2026-07-13 10:24:20', '2026-07-13 10:24:20'),
(751, 1, 1804, 'issue', -1, 4, ' | Кому: каб,№9 Полетаєва', NULL, NULL, '2026-07-13', '2026-07-13 10:25:32', '2026-07-13 10:25:32'),
(752, 1, 1835, 'issue', -1, 5, ' (Видано: каб,№7а Жарук)', NULL, NULL, '2026-07-14', '2026-07-14 04:26:37', '2026-07-14 04:26:37'),
(753, 1, 1805, 'issue', -5, 15, ' (Видано: каб,№33 Пужанська)', NULL, NULL, '2026-07-14', '2026-07-14 04:39:43', '2026-07-14 04:39:43'),
(754, 1, 1805, 'issue', -2, 13, ' (Видано: каб,№56 Хромченко)', NULL, NULL, '2026-07-14', '2026-07-14 04:40:32', '2026-07-14 04:40:32'),
(755, 1, 1804, 'issue', -1, 3, ' (Видано: каб,№7а Жарук)', NULL, NULL, '2026-07-14', '2026-07-14 04:50:14', '2026-07-14 04:50:14'),
(756, 1, 1804, 'issue', -1, 2, ' (Видано: каб,№59 Маценко)', NULL, NULL, '2026-07-14', '2026-07-14 04:50:43', '2026-07-14 04:50:43'),
(757, 1, 1843, 'issue', -1, 0, ' | Кому: к 16 Гірченко', NULL, NULL, '2026-07-14', '2026-07-14 05:44:26', '2026-07-14 05:44:26'),
(758, 1, 1803, 'issue', -1, 7, ' (Видано: каб,№55 Конюшенко)', NULL, NULL, '2026-07-15', '2026-07-15 05:11:34', '2026-07-15 05:11:34'),
(759, 1, 1803, 'issue', -1, 6, ' (Видано: каб,№57 Прядко)', NULL, NULL, '2026-07-15', '2026-07-15 05:12:01', '2026-07-15 05:12:01');
INSERT INTO `warehouse_movements` (`id`, `user_id`, `inventory_id`, `type`, `quantity`, `balance_after`, `note`, `document_number`, `issued_to_user_id`, `operation_date`, `created_at`, `updated_at`) VALUES
(760, 1, 2026, 'issue', -1, 1, ' | Кому: амб,1-5 Александров поручні ГоловВихід', NULL, NULL, '2026-07-15', '2026-07-15 05:25:44', '2026-07-15 05:25:44'),
(761, 1, 1803, 'issue', -1, 5, ' | Кому: каб,№46 Коваль', NULL, NULL, '2026-07-16', '2026-07-16 03:21:14', '2026-07-16 03:21:14'),
(762, 1, 2065, 'issue', -1, 1, ' | Кому: К13 гинеколог', NULL, NULL, '2026-07-16', '2026-07-16 10:41:25', '2026-07-16 10:41:25'),
(763, 1, 2075, 'issue', -1, 8, ' | Кому: К36', NULL, NULL, '2026-07-16', '2026-07-16 10:41:51', '2026-07-16 10:41:51'),
(764, 1, 2075, 'issue', -1, 7, ' | Кому: К307 скрининг болгарская', NULL, NULL, '2026-07-16', '2026-07-16 10:42:27', '2026-07-16 10:42:27'),
(765, 1, 2055, 'issue', -1, 1, ' | Кому: К307 скрининг болгарская', NULL, NULL, '2026-07-16', '2026-07-16 10:43:32', '2026-07-16 10:43:32'),
(766, 1, 2055, 'issue', -1, 0, ' | Кому: К107 скрининг бугаевская', NULL, NULL, '2026-07-16', '2026-07-16 10:43:53', '2026-07-16 10:43:53'),
(767, 1, 1823, 'issue', -1, 6, ' | Кому: амб,1-5', NULL, NULL, '2026-07-20', '2026-07-20 07:57:55', '2026-07-20 07:57:55'),
(768, 1, 2017, 'issue', -1, 14, ' | Кому: амб,1-5 Лобода.', NULL, NULL, '2026-07-20', '2026-07-20 08:10:15', '2026-07-20 08:10:15'),
(769, 1, 1803, 'issue', -1, 4, ' | Кому: каб,№9 Полетаєва', NULL, NULL, '2026-07-20', '2026-07-20 08:11:47', '2026-07-20 08:11:47'),
(770, 1, 1835, 'issue', -1, 4, ' | Кому: каб,№39', NULL, NULL, '2026-07-20', '2026-07-20 08:12:26', '2026-07-20 08:12:26'),
(771, 1, 1804, 'issue', -1, 1, ' | Кому: каб,№9 Полетаєва', NULL, NULL, '2026-07-20', '2026-07-20 08:13:09', '2026-07-20 08:13:09'),
(772, 1, 1795, 'issue', -1, 19, ' | Кому: амб,1-5', NULL, NULL, '2026-07-20', '2026-07-20 08:14:37', '2026-07-20 08:14:37'),
(773, 1, 1807, 'issue', -8, 400, ' (Видано: 1 пол,)', NULL, NULL, '2026-07-20', '2026-07-20 08:19:53', '2026-07-20 08:19:53'),
(774, 1, 1807, 'issue', -8, 392, ' (Видано: 1 пол,)', NULL, NULL, '2026-07-20', '2026-07-20 08:19:54', '2026-07-20 08:19:54'),
(775, 1, 1807, 'issue', -58, 334, ' (Видано: амб,1-5)', NULL, NULL, '2026-07-20', '2026-07-20 08:20:51', '2026-07-20 08:20:51'),
(776, 1, 1806, 'issue', -69, 263, ' (Видано: амб,1-5)', NULL, NULL, '2026-07-20', '2026-07-20 08:27:49', '2026-07-20 08:27:49'),
(777, 1, 1806, 'issue', -32, 231, ' (Видано: амб,6-10)', NULL, NULL, '2026-07-20', '2026-07-20 08:28:28', '2026-07-20 08:28:28'),
(778, 1, 1787, 'issue', -1, 11, ' | Кому: амб,1-5', NULL, NULL, '2026-07-22', '2026-07-22 04:57:28', '2026-07-22 04:57:28'),
(779, 1, 2054, 'issue', -1, 4, ' (Видано: амб,6-10\'єлектрик)', NULL, NULL, '2026-07-22', '2026-07-22 08:26:57', '2026-07-22 08:26:57'),
(780, 1, 1803, 'issue', -2, 2, ' | Кому: каб,№52 Комарова', NULL, NULL, '2026-07-23', '2026-07-23 06:29:07', '2026-07-23 06:29:07'),
(781, 1, 1833, 'issue', -10, 70, ' | Кому: каб,№52 Комарова', NULL, NULL, '2026-07-23', '2026-07-23 06:30:00', '2026-07-23 06:30:00'),
(782, 1, 1833, 'issue', -10, 60, ' | Кому: каб,№58 Золота', NULL, NULL, '2026-07-23', '2026-07-23 06:30:41', '2026-07-23 06:30:41'),
(783, 1, 1875, 'issue', -1, 8, ' (Видано: амб,6-10)', NULL, NULL, '2026-07-23', '2026-07-23 09:09:54', '2026-07-23 09:09:54'),
(784, 1, 1823, 'issue', -2, 4, ' | Кому: амб,1-5', NULL, NULL, '2026-07-23', '2026-07-23 09:16:28', '2026-07-23 09:16:28'),
(785, 1, 1832, 'issue', -5, 0, ' | Кому: амб,1-5 скрининг Золота', NULL, NULL, '2026-07-28', '2026-07-28 04:20:06', '2026-07-28 04:20:06'),
(786, 1, 1833, 'issue', -20, 40, ' | Кому: амб,1-5 скрининг Золота', NULL, NULL, '2026-07-28', '2026-07-28 04:21:00', '2026-07-28 04:21:00'),
(787, 1, 1804, 'issue', -1, 0, ' | Кому: амб,1-5 скрининг Золота', NULL, NULL, '2026-07-28', '2026-07-28 04:21:44', '2026-07-28 04:21:44'),
(788, 1, 1803, 'issue', -1, 1, ' | Кому: амб,1-5 скрининг каб,№9', NULL, NULL, '2026-07-29', '2026-07-29 03:43:10', '2026-07-29 03:43:10'),
(789, 1, 1803, 'issue', -1, 0, ' | Кому: каб,№29 касса', NULL, NULL, '2026-07-29', '2026-07-29 03:44:12', '2026-07-29 03:44:12'),
(790, 1, 1835, 'issue', -2, 2, ' | Кому: амб,1-5', NULL, NULL, '2026-07-29', '2026-07-29 03:44:52', '2026-07-29 03:44:52'),
(791, 1, 1803, 'receipt', 215, 215, NULL, '178814', NULL, '2026-07-31', '2026-07-31 07:57:37', '2026-07-31 07:57:37'),
(792, 1, 1803, 'issue', -40, 175, ' | Кому: амб,6-10 Гріненко', NULL, NULL, '2026-07-31', '2026-07-31 07:58:48', '2026-07-31 07:58:48'),
(793, 1, 1803, 'issue', -5, 170, ' (Видано: каб,№58 Золота)', NULL, NULL, '2026-07-31', '2026-07-31 07:59:25', '2026-07-31 07:59:25'),
(794, 1, 1803, 'issue', -5, 165, ' (Видано: каб,№55 Конюшенко)', NULL, NULL, '2026-07-31', '2026-07-31 07:59:46', '2026-07-31 07:59:46'),
(795, 1, 1804, 'receipt', 50, 50, NULL, '178812', NULL, '2026-07-31', '2026-07-31 08:46:34', '2026-07-31 08:46:34'),
(796, 1, 1805, 'receipt', 70, 83, NULL, '178812', NULL, '2026-07-31', '2026-07-31 08:47:58', '2026-07-31 08:47:58'),
(797, 1, 2052, 'receipt', 20, 20, NULL, '178813', NULL, '2026-07-31', '2026-07-31 08:48:59', '2026-07-31 08:48:59'),
(798, 1, 1823, 'receipt', 50, 54, NULL, '2082', NULL, '2026-07-31', '2026-07-31 08:53:24', '2026-07-31 08:53:24'),
(799, 1, 2035, 'issue', -5, 25, ' | Кому: амб,6-10', NULL, NULL, '2026-08-06', '2026-08-06 03:53:46', '2026-08-06 03:53:46'),
(800, 1, 2034, 'issue', -5, 25, ' | Кому: амб,6-10', NULL, NULL, '2026-08-06', '2026-08-06 03:54:12', '2026-08-06 03:54:12'),
(801, 1, 1885, 'issue', -1, 0, ' | Кому: амб,1-5', NULL, NULL, '2026-08-06', '2026-08-06 04:09:31', '2026-08-06 04:09:31'),
(802, 1, 1840, 'issue', -1, 8, ' | Кому: амб,1-5', NULL, NULL, '2026-08-06', '2026-08-06 04:48:33', '2026-08-06 04:48:33'),
(803, 1, 1822, 'issue', -1, 30, ' | Кому: амб,1-5 Садовнік', NULL, NULL, '2026-08-06', '2026-08-06 04:51:08', '2026-08-06 04:51:08'),
(804, 1, 1890, 'issue', -8, 50, ' (Видано: амб,6-10)', NULL, NULL, '2026-08-06', '2026-08-06 05:19:02', '2026-08-06 05:19:02'),
(805, 1, 2084, 'receipt', 6, 6, 'Початковий залишок', NULL, NULL, '2026-08-06', '2026-08-06 05:49:06', '2026-08-06 05:49:06'),
(806, 1, 2085, 'receipt', 6, 6, 'Початковий залишок', NULL, NULL, '2026-08-06', '2026-08-06 05:49:30', '2026-08-06 05:49:30'),
(807, 1, 2086, 'receipt', 1, 1, 'Початковий залишок', NULL, NULL, '2026-08-06', '2026-08-06 06:33:22', '2026-08-06 06:33:22'),
(808, 1, 2009, 'issue', -1, 0, ' (Видано: амб,1-5)', NULL, NULL, '2026-08-06', '2026-08-06 07:04:27', '2026-08-06 07:04:27'),
(809, 1, 1795, 'issue', -1, 18, ' | Кому: амб,1-5', NULL, NULL, '2026-08-07', '2026-08-07 03:54:30', '2026-08-07 03:54:30'),
(810, 1, 1790, 'issue', -1, 9, ' | Кому: амб,1-5', NULL, NULL, '2026-08-07', '2026-08-07 04:08:11', '2026-08-07 04:08:11'),
(811, 1, 1784, 'issue', -1, 19, ' | Кому: амб,1-5', NULL, NULL, '2026-08-07', '2026-08-07 04:12:25', '2026-08-07 04:12:25'),
(812, 1, 1791, 'issue', -1, 12, ' | Кому: амб,1-5', NULL, NULL, '2026-08-07', '2026-08-07 04:27:08', '2026-08-07 04:27:08'),
(813, 1, 1848, 'issue', -1, 5, ' | Кому: амб,1-5', NULL, NULL, '2026-08-07', '2026-08-07 04:44:02', '2026-08-07 04:44:02'),
(814, 1, 1877, 'issue', -1, 7, ' | Кому: амб,1-5', NULL, NULL, '2026-08-07', '2026-08-07 04:59:00', '2026-08-07 04:59:00'),
(815, 1, 2029, 'issue', -1, 4, ' | Кому: амб,6-10', NULL, NULL, '2026-08-07', '2026-08-07 05:01:12', '2026-08-07 05:01:12'),
(816, 1, 1835, 'issue', -1, 1, ' | Кому: каб,№57 Прядко', NULL, NULL, '2026-08-07', '2026-08-07 05:10:07', '2026-08-07 05:10:07'),
(817, 1, 1835, 'issue', -1, 0, ' | Кому: каб,№55 Конюшенко', NULL, NULL, '2026-08-07', '2026-08-07 05:10:56', '2026-08-07 05:10:56'),
(818, 1, 1828, 'issue', -13, 3, ' | Кому: амб,1-5', NULL, NULL, '2026-08-07', '2026-08-07 05:17:52', '2026-08-07 05:17:52'),
(819, 1, 1807, 'issue', -63, 271, ' | Кому: амб,1-5', NULL, NULL, '2026-08-07', '2026-08-07 05:39:11', '2026-08-07 05:39:11'),
(820, 1, 1807, 'issue', -28, 243, ' (Видано: амб,1-5)', NULL, NULL, '2026-08-10', '2026-08-10 07:19:48', '2026-08-10 07:19:48'),
(821, 1, 1806, 'issue', -71, 160, ' (Видано: амб,1-5)', NULL, NULL, '2026-08-10', '2026-08-10 08:31:20', '2026-08-10 08:31:20'),
(822, 1, 1827, 'receipt', 12, 12, NULL, '9970', NULL, '2026-08-10', '2026-08-10 08:54:43', '2026-08-10 08:54:43'),
(823, 1, 1813, 'issue', -1, 16, ' | Кому: амб,1-5', NULL, NULL, '2026-08-10', '2026-08-10 09:17:48', '2026-08-10 09:17:48'),
(824, 1, 1804, 'issue', -2, 48, ' (Видано: каб,№9 Алексейчук)', NULL, NULL, '2026-08-11', '2026-08-11 04:52:16', '2026-08-11 04:52:16'),
(825, 1, 1804, 'issue', -1, 47, ' (Видано: каб,№33 Савельєва)', NULL, NULL, '2026-08-11', '2026-08-11 04:53:15', '2026-08-11 04:53:15'),
(826, 1, 1804, 'issue', -1, 46, ' (Видано: каб,№41 Бойченко)', NULL, NULL, '2026-08-11', '2026-08-11 04:54:03', '2026-08-11 04:54:03'),
(827, 1, 1804, 'issue', -1, 45, ' (Видано: каб,№7а Жарук)', NULL, NULL, '2026-08-11', '2026-08-11 04:54:40', '2026-08-11 04:54:40'),
(828, 1, 1788, 'issue', -1, 7, ' | Кому: Скрининг 40+, 9к липи', NULL, NULL, '2026-08-11', '2026-08-11 05:02:41', '2026-08-11 05:02:41'),
(829, 1, 2068, 'issue', -1, 4, ' (Видано: Лаборатория Житомирская)', NULL, NULL, '2026-08-11', '2026-08-11 05:05:29', '2026-08-11 05:05:29'),
(830, 1, 1843, 'receipt', 16, 16, 'Авансом было 5 и потом приехало 11', 'Накладная от июля', NULL, '2026-08-11', '2026-08-11 05:09:28', '2026-08-11 05:09:28'),
(831, 1, 1843, 'issue', -1, 15, ' (Видано: К9 липи)', NULL, NULL, '2026-08-11', '2026-08-11 05:09:49', '2026-08-11 05:09:49'),
(832, 1, 1843, 'issue', -1, 14, ' (Видано: К28 липи)', NULL, NULL, '2026-08-11', '2026-08-11 05:10:02', '2026-08-11 05:10:02'),
(833, 1, 1843, 'issue', -1, 13, ' (Видано: Болгарская скрининг)', NULL, NULL, '2026-08-11', '2026-08-11 05:11:00', '2026-08-11 05:11:00'),
(834, 1, 1843, 'issue', -1, 12, ' (Видано: К52 бухгалтерия)', NULL, NULL, '2026-08-11', '2026-08-11 05:11:11', '2026-08-11 05:11:11'),
(835, 1, 1843, 'issue', -1, 11, ' (Видано: Житомирская скрининг)', NULL, NULL, '2026-08-11', '2026-08-11 05:11:29', '2026-08-11 05:11:29'),
(836, 1, 1843, 'issue', -1, 10, ' (Видано: К50 Ольга викторовна)', NULL, NULL, '2026-08-11', '2026-08-11 05:13:54', '2026-08-11 05:13:54'),
(837, 1, 1843, 'receipt', 2, 12, NULL, 'Остаток у иваныча', NULL, '2026-08-11', '2026-08-11 05:14:15', '2026-08-11 05:14:15'),
(838, 1, 1843, 'issue', -1, 11, ' (Видано: Секретар Житомирська)', NULL, NULL, '2026-08-13', '2026-08-13 06:28:40', '2026-08-13 06:28:40'),
(839, 1, 1788, 'issue', -1, 6, ' (Видано: К16 Гирченко)', NULL, NULL, '2026-08-13', '2026-08-13 06:30:01', '2026-08-13 06:30:01'),
(840, 3, 1803, 'issue', -3, 162, ' (Видано: каб,№33 Савельєва)', NULL, NULL, '2026-08-13', '2026-08-13 07:06:12', '2026-08-13 07:06:12'),
(841, 3, 1803, 'issue', -1, 161, ' (Видано: каб,№9 Алексейчук)', NULL, NULL, '2026-08-13', '2026-08-13 07:06:52', '2026-08-13 07:06:52'),
(842, 3, 1803, 'issue', -1, 160, ' (Видано: каб,№33 Козак)', NULL, NULL, '2026-08-13', '2026-08-13 07:08:22', '2026-08-13 07:08:22'),
(843, 3, 1803, 'issue', -1, 159, ' (Видано: каб,№57 Прядко)', NULL, NULL, '2026-08-13', '2026-08-13 07:09:03', '2026-08-13 07:09:03'),
(844, 3, 1803, 'issue', -5, 154, ' (Видано: каб,№39 Явтуховская)', NULL, NULL, '2026-08-13', '2026-08-13 07:10:08', '2026-08-13 07:10:08'),
(845, 3, 1803, 'issue', -1, 153, ' (Видано: каб,№49 Чорна)', NULL, NULL, '2026-08-13', '2026-08-13 07:10:50', '2026-08-13 07:10:50'),
(846, 3, 1803, 'issue', -2, 151, ' (Видано: каб,№46 Коваль)', NULL, NULL, '2026-08-13', '2026-08-13 07:11:17', '2026-08-13 07:11:17'),
(847, 3, 1803, 'issue', -2, 149, ' (Видано: каб,№15 Тіщенко)', NULL, NULL, '2026-08-13', '2026-08-13 07:11:47', '2026-08-13 07:11:47'),
(848, 3, 1803, 'issue', -10, 139, ' (Видано: каб,№52 Комарова)', NULL, NULL, '2026-08-13', '2026-08-13 07:12:29', '2026-08-13 07:12:29'),
(849, 3, 1803, 'issue', -1, 138, ' (Видано: каб,№9 Алексейчук)', NULL, NULL, '2026-08-13', '2026-08-13 07:21:22', '2026-08-13 07:21:22'),
(850, 1, 2056, 'issue', -1, 2, ' (Видано: Скрининг Фесенко)', NULL, NULL, '2026-08-14', '2026-08-14 03:13:49', '2026-08-14 03:13:49'),
(851, 1, 2056, 'issue', -1, 1, ' (Видано: Скрининг бугаевская)', NULL, NULL, '2026-08-14', '2026-08-14 03:14:02', '2026-08-14 03:14:02'),
(852, 1, 2056, 'issue', -1, 0, ' (Видано: Скрининг болгарская)', NULL, NULL, '2026-08-14', '2026-08-14 03:14:26', '2026-08-14 03:14:26'),
(853, 1, 2055, 'receipt', 2, 2, 'Докупили для выездного скрининга', 'Заказ июль', NULL, '2026-08-14', '2026-08-14 03:15:48', '2026-08-14 03:15:48'),
(854, 1, 2056, 'receipt', 2, 2, 'Купили для выездного скрининга', 'Новый приход', NULL, '2026-08-14', '2026-08-14 03:22:43', '2026-08-14 03:22:43'),
(855, 1, 2083, 'issue', -1, 9, ' | Кому: к16 Гірченко', NULL, NULL, '2026-08-17', '2026-08-17 04:24:30', '2026-08-17 04:24:30'),
(856, 1, 2083, 'issue', -1, 8, ' | Кому: к306 Болгарська', NULL, NULL, '2026-08-17', '2026-08-17 04:24:49', '2026-08-17 04:24:49'),
(857, 1, 2072, 'issue', -1, 9, ' | Кому: Лабораторія Житомирська', NULL, NULL, '2026-08-17', '2026-08-17 04:26:18', '2026-08-17 04:26:18'),
(858, 3, 1895, 'issue', -1, 3, ' | Кому: амб,6-10 лобараторія 3пов,', NULL, NULL, '2026-08-17', '2026-08-17 06:34:29', '2026-08-17 06:34:29'),
(859, 1, 1843, 'issue', -1, 10, ' | Кому: Кадри Липи', NULL, NULL, '2026-08-17', '2026-08-17 10:19:33', '2026-08-17 10:19:33'),
(860, 1, 1843, 'issue', -1, 9, ' | Кому: Юрист Липи', NULL, NULL, '2026-08-17', '2026-08-17 10:25:00', '2026-08-17 10:25:00'),
(861, 1, 841, 'transfer', 1, 1, 'Переміщення: Підвал → 2 поверх (Под списание)', NULL, NULL, '2026-08-20', '2026-08-20 06:47:19', '2026-08-20 06:47:19'),
(862, 1, 2080, 'issue', -1, 6, ' | Кому: К9 скрининг', NULL, NULL, '2026-08-20', '2026-08-20 06:47:59', '2026-08-20 06:47:59'),
(863, 1, 2076, 'issue', -1, 3, ' (Видано: Реестратура Фесенко)', NULL, NULL, '2026-08-21', '2026-08-21 04:43:48', '2026-08-21 04:43:48'),
(864, 1, 2076, 'issue', -1, 2, ' (Видано: Реестратура болгарская)', NULL, NULL, '2026-08-21', '2026-08-21 04:44:03', '2026-08-21 04:44:03'),
(865, 1, 1843, 'issue', -1, 8, ' | Кому: Секретар Липи', NULL, NULL, '2026-08-21', '2026-08-21 09:11:24', '2026-08-21 09:11:24'),
(866, 3, 2031, 'issue', -1, 9, ' | Кому: амб,1-5', NULL, NULL, '2026-08-26', '2026-08-26 03:55:59', '2026-08-26 03:55:59'),
(867, 3, 2031, 'issue', -1, 8, ' | Кому: амб,1-5', NULL, NULL, '2026-08-26', '2026-08-26 03:57:08', '2026-08-26 03:57:08'),
(868, 3, 1804, 'issue', -1, 44, ' (Видано: каб,№55 Конюшенко)', NULL, NULL, '2026-08-26', '2026-08-26 07:05:13', '2026-08-26 07:05:13'),
(869, 3, 1804, 'issue', -1, 43, ' (Видано: каб,№50 Лічікакі)', NULL, NULL, '2026-08-26', '2026-08-26 07:05:59', '2026-08-26 07:05:59'),
(870, 3, 1804, 'issue', -1, 42, ' (Видано: каб,№52)', NULL, NULL, '2026-08-26', '2026-08-26 07:06:16', '2026-08-26 07:06:16'),
(871, 3, 1804, 'issue', -1, 41, ' (Видано: каб,№29 касса)', NULL, NULL, '2026-08-26', '2026-08-26 07:07:22', '2026-08-26 07:07:22'),
(872, 3, 1804, 'issue', -2, 39, ' (Видано: амб,6-10 Мірчєва)', NULL, NULL, '2026-08-26', '2026-08-26 07:08:25', '2026-08-26 07:08:25'),
(873, 3, 1804, 'issue', -1, 38, ' (Видано: каб,№56 Хромченко)', NULL, NULL, '2026-08-26', '2026-08-26 07:08:59', '2026-08-26 07:08:59'),
(874, 3, 1804, 'issue', -1, 37, ' (Видано: каб,№16 Калугіна)', NULL, NULL, '2026-08-26', '2026-08-26 07:10:43', '2026-08-26 07:10:43'),
(875, 3, 1804, 'issue', -1, 36, ' (Видано: каб,№45/2 Ненашев)', NULL, NULL, '2026-08-26', '2026-08-26 07:11:29', '2026-08-26 07:11:29'),
(876, 3, 1804, 'issue', -5, 31, ' (Видано: амб,6-10 сест,Госп, Гріненко)', NULL, NULL, '2026-08-26', '2026-08-26 07:12:50', '2026-08-26 07:12:50'),
(877, 3, 1803, 'issue', -1, 137, ' (Видано: каб,№35 Скрібантовіч)', NULL, NULL, '2026-08-26', '2026-08-26 07:18:52', '2026-08-26 07:18:52'),
(878, 3, 1803, 'issue', -1, 136, ' (Видано: каб,№7а Арнаут)', NULL, NULL, '2026-08-26', '2026-08-26 07:19:42', '2026-08-26 07:19:42'),
(879, 3, 1803, 'issue', -1, 135, ' (Видано: каб,№56 Хромченко)', NULL, NULL, '2026-08-26', '2026-08-26 07:20:40', '2026-08-26 07:20:40'),
(880, 3, 1803, 'issue', -1, 134, ' (Видано: каб,№57 Прядко)', NULL, NULL, '2026-08-26', '2026-08-26 07:21:04', '2026-08-26 07:21:04'),
(881, 3, 1803, 'issue', -1, 133, ' (Видано: каб,№16 Калугіна)', NULL, NULL, '2026-08-26', '2026-08-26 07:21:54', '2026-08-26 07:21:54'),
(882, 3, 1803, 'issue', -1, 132, ' (Видано: каб,№45/2 Ненашев)', NULL, NULL, '2026-08-26', '2026-08-26 07:22:15', '2026-08-26 07:22:15'),
(883, 3, 1803, 'issue', -1, 131, ' (Видано: каб,№40 Гроздева)', NULL, NULL, '2026-08-26', '2026-08-26 07:22:48', '2026-08-26 07:22:48'),
(884, 1, 1843, 'issue', -1, 7, ' (Видано: К15 (сосудистый на приеме))', NULL, NULL, '2026-08-27', '2026-08-27 04:09:02', '2026-08-27 04:09:02'),
(885, 1, 1843, 'issue', -1, 6, ' (Видано: К133 Житомирська)', NULL, NULL, '2026-08-27', '2026-08-27 07:18:06', '2026-08-27 07:18:06'),
(886, 3, 2050, 'issue', -10, 40, ' | Кому: каб,№2-5', NULL, NULL, '2026-08-28', '2026-08-28 04:07:37', '2026-08-28 04:07:37'),
(887, 3, 1892, 'issue', -3, 6, ' | Кому: каб,№2-5', NULL, NULL, '2026-08-28', '2026-08-28 04:09:36', '2026-08-28 04:09:36'),
(888, 3, 1886, 'issue', -2, 0, ' | Кому: каб,№2-5', NULL, NULL, '2026-08-28', '2026-08-28 04:12:10', '2026-08-28 04:12:10'),
(889, 3, 2004, 'issue', -1, 1, ' (Видано: Туалет Болгарська 38)', NULL, NULL, '2026-08-28', '2026-08-28 04:16:34', '2026-08-28 04:16:34'),
(890, 3, 1973, 'issue', -1, 7, ' | Кому: амб,6-10 сантехнік', NULL, NULL, '2026-08-28', '2026-08-28 04:17:59', '2026-08-28 04:17:59'),
(891, 3, 1859, 'issue', -1, 2, ' | Кому: амб,6-10 лобараторія 3пов,', NULL, NULL, '2026-08-28', '2026-08-28 04:20:18', '2026-08-28 04:20:18'),
(892, 1, 2072, 'issue', -1, 8, ' | Кому: Кадри липи', NULL, NULL, '2026-08-28', '2026-08-28 08:39:49', '2026-08-28 08:39:49'),
(893, 3, 1828, 'issue', -3, 0, ' (Видано: амб,1-5)', NULL, NULL, '2026-08-31', '2026-08-31 05:37:53', '2026-08-31 05:37:53'),
(894, 3, 1830, 'issue', -30, 4, 'с 5,06 по 31,08 (Видано: амб,1-5)', NULL, NULL, '2026-08-31', '2026-08-31 05:41:26', '2026-08-31 05:41:26'),
(895, 3, 1807, 'issue', -48, 195, ' (Видано: амб,6-10)', NULL, NULL, '2026-08-31', '2026-08-31 05:45:29', '2026-08-31 05:45:29'),
(896, 3, 1807, 'issue', -67, 128, '17,08 по 31,08 (Видано: амб,1-5)', NULL, NULL, '2026-08-31', '2026-08-31 05:46:24', '2026-08-31 05:46:24'),
(897, 3, 1806, 'issue', -32, 128, '24,08 (Видано: амб,6-10)', NULL, NULL, '2026-08-31', '2026-08-31 05:52:12', '2026-08-31 05:52:12'),
(898, 3, 1806, 'issue', -65, 63, '17,08 по 31,08 (Видано: амб,1-5)', NULL, NULL, '2026-08-31', '2026-08-31 05:53:23', '2026-08-31 05:53:23'),
(899, 3, 1803, 'issue', -1, 130, ' | Кому: каб,№9 Алексейчук', NULL, NULL, '2026-08-31', '2026-08-31 06:05:58', '2026-08-31 06:05:58'),
(900, 3, 1803, 'issue', -1, 129, ' | Кому: каб,№24 Григорашенко', NULL, NULL, '2026-08-31', '2026-08-31 06:07:00', '2026-08-31 06:07:00'),
(901, 3, 1790, 'issue', -1, 8, ' (Видано: амб,1-5)', NULL, NULL, '2026-08-31', '2026-08-31 06:18:06', '2026-08-31 06:18:06'),
(902, 3, 1799, 'issue', -2, 4, '6,07 по 31,08 (Видано: амб,1-5)', NULL, NULL, '2026-08-31', '2026-08-31 06:33:25', '2026-08-31 06:33:25'),
(903, 3, 1795, 'issue', -2, 16, ' | Кому: амб,1-5', NULL, NULL, '2026-08-31', '2026-08-31 06:38:57', '2026-08-31 06:38:57'),
(904, 3, 1806, 'issue', -4, 59, ' | Кому: амб,1-5', NULL, NULL, '2026-09-01', '2026-09-01 04:07:25', '2026-09-01 04:07:25'),
(905, 3, 1803, 'issue', -1, 128, ' | Кому: каб,№46 Коваль', NULL, NULL, '2026-09-01', '2026-09-01 08:08:52', '2026-09-01 08:08:52'),
(906, 3, 1831, 'issue', -2, 0, ' | Кому: каб,№46 Коваль', NULL, NULL, '2026-09-01', '2026-09-01 08:09:43', '2026-09-01 08:09:43'),
(907, 3, 1804, 'issue', -1, 30, ' | Кому: каб,№9 Грігорашенко', NULL, NULL, '2026-09-01', '2026-09-01 08:11:44', '2026-09-01 08:11:44'),
(908, 3, 1803, 'issue', -2, 126, ' (Видано: каб,№56 Хромченко)', NULL, NULL, '2026-09-02', '2026-09-02 08:12:16', '2026-09-02 08:12:16'),
(909, 3, 1793, 'issue', -10, 47, ' (Видано: амб,6-10 Гріненко)', NULL, NULL, '2026-09-03', '2026-09-03 04:12:58', '2026-09-03 04:12:58'),
(910, 3, 1793, 'issue', -9, 38, ' (Видано: амб,1-5)', NULL, NULL, '2026-09-03', '2026-09-03 04:15:12', '2026-09-03 04:15:12'),
(911, 3, 1791, 'issue', -2, 10, ' (Видано: амб,1-5)', NULL, NULL, '2026-09-03', '2026-09-03 04:23:37', '2026-09-03 04:23:37'),
(912, 3, 1791, 'issue', -2, 8, ' (Видано: амб,6-10 Грінєнко)', NULL, NULL, '2026-09-03', '2026-09-03 04:24:17', '2026-09-03 04:24:17'),
(913, 3, 1848, 'issue', -1, 4, ' (Видано: амб,1-5)', NULL, NULL, '2026-09-03', '2026-09-03 04:29:02', '2026-09-03 04:29:02'),
(914, 3, 1848, 'issue', -2, 2, ' (Видано: амб,6-10 Грінєнко)', NULL, NULL, '2026-09-03', '2026-09-03 04:29:22', '2026-09-03 04:29:22'),
(915, 3, 1797, 'issue', -2, 12, ' (Видано: амб,1-5)', NULL, NULL, '2026-09-03', '2026-09-03 04:53:52', '2026-09-03 04:53:52'),
(916, 3, 1786, 'issue', -1, 7, ' (Видано: амб,1-5)', NULL, NULL, '2026-09-03', '2026-09-03 04:59:43', '2026-09-03 04:59:43'),
(917, 3, 1786, 'issue', -2, 5, ' (Видано: амб,6-10 Грінєнко)', NULL, NULL, '2026-09-03', '2026-09-03 05:00:11', '2026-09-03 05:00:11'),
(918, 3, 1784, 'issue', -2, 17, ' | Кому: амб,1-5', NULL, NULL, '2026-09-03', '2026-09-03 05:06:26', '2026-09-03 05:06:26'),
(919, 3, 1784, 'issue', -4, 13, ' | Кому: амб,6-10 Грінєнко', NULL, NULL, '2026-09-03', '2026-09-03 05:07:24', '2026-09-03 05:07:24'),
(920, 3, 1795, 'issue', -4, 12, ' (Видано: амб,6-10 Грінєнко)', NULL, NULL, '2026-09-03', '2026-09-03 05:14:01', '2026-09-03 05:14:01'),
(921, 3, 1787, 'issue', -1, 10, ' (Видано: амб,1-5)', NULL, NULL, '2026-09-03', '2026-09-03 05:16:26', '2026-09-03 05:16:26'),
(922, 3, 1787, 'issue', -2, 8, ' (Видано: амб,6-10 Грінєнко)', NULL, NULL, '2026-09-03', '2026-09-03 05:16:49', '2026-09-03 05:16:49'),
(923, 3, 1877, 'issue', -2, 5, ' (Видано: амб,6-10 Грінєнко)', NULL, NULL, '2026-09-03', '2026-09-03 05:18:33', '2026-09-03 05:18:33'),
(924, 3, 1790, 'issue', -2, 6, ' (Видано: амб,6-10 Грінєнко)', NULL, NULL, '2026-09-03', '2026-09-03 05:39:57', '2026-09-03 05:39:57'),
(925, 3, 1829, 'issue', -6, 6, ' | Кому: амб,6-10 Грінєнко', NULL, NULL, '2026-09-03', '2026-09-03 06:04:20', '2026-09-03 06:04:20'),
(926, 3, 1829, 'issue', -1, 5, ' (Видано: амб,1-5)', NULL, NULL, '2026-09-03', '2026-09-03 06:06:02', '2026-09-03 06:06:02'),
(927, 3, 1830, 'issue', -4, 0, ' (Видано: амб,1-5)', NULL, NULL, '2026-09-03', '2026-09-03 06:12:08', '2026-09-03 06:12:08'),
(928, 3, 1806, 'issue', -32, 27, ' (Видано: амб,6-10 Грінєнко)', NULL, NULL, '2026-09-03', '2026-09-03 06:20:45', '2026-09-03 06:20:45'),
(929, 3, 1807, 'issue', -8, 120, ' (Видано: амб,1-5)', NULL, NULL, '2026-09-03', '2026-09-03 06:25:07', '2026-09-03 06:25:07'),
(930, 3, 1807, 'issue', -48, 72, ' (Видано: амб,6-10 Грінєнко)', NULL, NULL, '2026-09-03', '2026-09-03 06:25:22', '2026-09-03 06:25:22'),
(931, 1, 1843, 'issue', -1, 5, ' | Кому: Болгарская Мукоида', NULL, NULL, '2026-09-07', '2026-09-07 03:08:57', '2026-09-07 03:08:57'),
(932, 1, 1843, 'issue', -1, 4, ' | Кому: Мыльникова к36', NULL, NULL, '2026-09-07', '2026-09-07 03:09:30', '2026-09-07 03:09:30'),
(933, 1, 1843, 'issue', -1, 3, ' | Кому: К58 Золота', NULL, NULL, '2026-09-07', '2026-09-07 03:10:20', '2026-09-07 03:10:20'),
(934, 1, 1843, 'issue', -1, 2, ' | Кому: Бухгалтерия к52', NULL, NULL, '2026-09-07', '2026-09-07 03:12:21', '2026-09-07 03:12:21'),
(935, 1, 1843, 'receipt', 10, 12, NULL, 'Аванс', NULL, '2026-09-07', '2026-09-07 03:12:52', '2026-09-07 03:12:52'),
(936, 3, 1793, 'issue', -1, 37, ' | Кому: амб,1-5', NULL, NULL, '2026-09-07', '2026-09-07 07:27:06', '2026-09-07 07:27:06'),
(937, 3, 1807, 'issue', -26, 46, ' (Видано: амб,1-5)', NULL, NULL, '2026-09-07', '2026-09-07 07:45:58', '2026-09-07 07:45:58'),
(938, 3, 1806, 'issue', -24, 3, ' (Видано: амб,1-5)', NULL, NULL, '2026-09-07', '2026-09-07 07:49:48', '2026-09-07 07:49:48'),
(939, 1, 1843, 'issue', -1, 11, ' | Кому: К16 Гирченко', NULL, NULL, '2026-09-07', '2026-09-07 09:11:58', '2026-09-07 09:11:58'),
(940, 3, 2019, 'issue', -1, 4, ' | Кому: каб,№20', NULL, NULL, '2026-09-08', '2026-09-08 06:02:43', '2026-09-08 06:02:43'),
(941, 3, 1804, 'issue', -1, 29, ' | Кому: каб,№41 Бойченко', NULL, NULL, '2026-09-08', '2026-09-08 06:05:32', '2026-09-08 06:05:32'),
(942, 3, 1803, 'issue', -1, 125, ' | Кому: каб,№7 Юрченко', NULL, NULL, '2026-09-09', '2026-09-09 04:54:12', '2026-09-09 04:54:12'),
(943, 3, 1803, 'issue', -1, 124, ' | Кому: каб,№7 Піліпенко', NULL, NULL, '2026-09-09', '2026-09-09 04:55:00', '2026-09-09 04:55:00'),
(944, 3, 2022, 'issue', -1, 1, ' (Видано: амб,1-5 фасад центр,вхід)', NULL, NULL, '2026-09-10', '2026-09-10 05:00:08', '2026-09-10 05:00:08'),
(945, 3, 2035, 'issue', -5, 20, ' (Видано: амб,1-5 каб№2-5)', NULL, NULL, '2026-09-10', '2026-09-10 05:02:18', '2026-09-10 05:02:18'),
(946, 3, 1971, 'issue', -45, 55, ' | Кому: амб,1-5 каб№2-5', NULL, NULL, '2026-09-11', '2026-09-11 04:56:11', '2026-09-11 04:56:11'),
(947, 3, 1824, 'issue', -2, 6, ' | Кому: амб,1-5 каб№2-5', NULL, NULL, '2026-09-11', '2026-09-11 05:41:25', '2026-09-11 05:41:25'),
(948, 3, 1908, 'issue', -1, 2, ' (Видано: амб,1-5)', NULL, NULL, '2026-09-11', '2026-09-11 05:43:54', '2026-09-11 05:43:54'),
(949, 3, 2043, 'issue', -1, 1, ' | Кому: амб,1-5 каб№2-5', NULL, NULL, '2026-09-11', '2026-09-11 05:45:27', '2026-09-11 05:45:27'),
(950, 3, 2042, 'issue', -1, 1, ' (Видано: амб,1-5 каб№2-5)', NULL, NULL, '2026-09-11', '2026-09-11 05:46:13', '2026-09-11 05:46:13'),
(951, 3, 1824, 'issue', -1, 5, ' (Видано: амб,1-5)', NULL, NULL, '2026-09-11', '2026-09-11 06:53:57', '2026-09-11 06:53:57'),
(952, 3, 1824, 'issue', -1, 4, ' (Видано: амб,1-5)', NULL, NULL, '2026-09-11', '2026-09-11 06:54:00', '2026-09-11 06:54:00'),
(953, 3, 1806, 'issue', -3, 0, ' | Кому: амб,1-5', NULL, NULL, '2026-09-15', '2026-09-15 03:45:23', '2026-09-15 03:45:23'),
(954, 3, 1807, 'issue', -20, 26, ' (Видано: амб,1-5)', NULL, NULL, '2026-09-15', '2026-09-15 03:50:32', '2026-09-15 03:50:32'),
(955, 3, 1829, 'issue', -4, 1, ' | Кому: амб,1-5', NULL, NULL, '2026-09-15', '2026-09-15 03:52:06', '2026-09-15 03:52:06'),
(956, 3, 1808, 'issue', -17, 125, ' (Видано: амб,1-5)', NULL, NULL, '2026-09-15', '2026-09-15 04:06:04', '2026-09-15 04:06:04'),
(957, 1, 1843, 'issue', -1, 10, ' (Видано: Кульпина 45/2)', NULL, NULL, '2026-09-15', '2026-09-15 04:06:37', '2026-09-15 04:06:37'),
(958, 1, 1843, 'issue', -1, 9, ' (Видано: К9 скрининг)', NULL, NULL, '2026-09-15', '2026-09-15 04:07:06', '2026-09-15 04:07:06'),
(959, 3, 1803, 'issue', -1, 123, ' (Видано: каб,№24 Маценко)', NULL, NULL, '2026-09-15', '2026-09-15 05:33:32', '2026-09-15 05:33:32'),
(960, 3, 1803, 'issue', -1, 122, ' (Видано: каб,№49 Весєлова)', NULL, NULL, '2026-09-15', '2026-09-15 05:34:55', '2026-09-15 05:34:55'),
(961, 3, 1803, 'issue', -1, 121, ' (Видано: Регестратура Потапова)', NULL, NULL, '2026-09-15', '2026-09-15 05:36:25', '2026-09-15 05:36:25'),
(962, 3, 1803, 'issue', -1, 120, ' (Видано: каб,№57 Прядко)', NULL, NULL, '2026-09-15', '2026-09-15 05:36:53', '2026-09-15 05:36:53'),
(963, 3, 1795, 'issue', -1, 11, ' (Видано: амб,1-5)', NULL, NULL, '2026-09-15', '2026-09-15 06:45:34', '2026-09-15 06:45:34'),
(964, 1, 1843, 'issue', -1, 8, ' (Видано: К33 Савельева)', NULL, NULL, '2026-09-16', '2026-09-16 06:05:47', '2026-09-16 06:05:47'),
(965, 1, 1843, 'receipt', 35, 43, NULL, 'Накладная 14.09', NULL, '2026-09-16', '2026-09-16 06:09:47', '2026-09-16 06:09:47'),
(966, 1, 1843, 'issue', -16, 27, 'Выдача на все вторичные филиалы \r\nФесенко + житомирская + бугаевка + болгарская (Видано: Елена сестра хозяйка)', NULL, NULL, '2026-09-16', '2026-09-16 06:14:27', '2026-09-16 06:14:27'),
(967, 3, 1805, 'issue', -30, 53, ' (Видано: каб,№52 Комарова)', NULL, NULL, '2026-09-16', '2026-09-16 10:03:07', '2026-09-16 10:03:07'),
(968, 3, 1805, 'issue', -10, 43, ' (Видано: каб,№29 Мартинюк)', NULL, NULL, '2026-09-16', '2026-09-16 10:04:00', '2026-09-16 10:04:00'),
(969, 3, 2052, 'issue', -5, 15, ' | Кому: каб,№58 Золота', NULL, NULL, '2026-09-18', '2026-09-18 07:21:31', '2026-09-18 07:21:31'),
(970, 3, 1804, 'issue', -1, 28, ' | Кому: каб,№58 Золота', NULL, NULL, '2026-09-18', '2026-09-18 07:22:05', '2026-09-18 07:22:05'),
(971, 3, 2052, 'issue', -1, 14, ' (Видано: каб,№50 Лічікакі)', NULL, NULL, '2026-09-18', '2026-09-18 07:24:31', '2026-09-18 07:24:31'),
(972, 3, 1808, 'issue', -27, 98, ' (Видано: амб,1-5)', NULL, NULL, '2026-09-21', '2026-09-21 04:46:50', '2026-09-21 04:46:50'),
(973, 3, 1807, 'issue', -26, 0, ' (Видано: амб,1-5)', NULL, NULL, '2026-09-21', '2026-09-21 04:50:57', '2026-09-21 04:50:57'),
(974, 3, 1813, 'issue', -1, 15, ' (Видано: пОЛИК,№1 Пацановская)', NULL, NULL, '2026-09-21', '2026-09-21 04:54:30', '2026-09-21 04:54:30'),
(975, 3, 1813, 'issue', -2, 13, ' (Видано: амб,1-5)', NULL, NULL, '2026-09-21', '2026-09-21 04:54:58', '2026-09-21 04:54:58'),
(976, 3, 2034, 'issue', -5, 20, ' (Видано: амб,1-5)', NULL, NULL, '2026-09-21', '2026-09-21 04:58:37', '2026-09-21 04:58:37'),
(977, 1, 2080, 'issue', -1, 5, ' (Видано: Борисюк)', NULL, NULL, '2026-09-21', '2026-09-21 05:57:32', '2026-09-21 05:57:32'),
(978, 1, 1843, 'receipt', 1, 28, NULL, 'От иваныча чёрный кулек', NULL, '2026-09-21', '2026-09-21 06:31:24', '2026-09-21 06:31:24'),
(979, 3, 1813, 'issue', -2, 11, ' (Видано: амб,1-5 Лобода)', NULL, NULL, '2026-09-23', '2026-09-23 04:10:50', '2026-09-23 04:10:50'),
(980, 3, 1820, 'issue', -6, 10, ' (Видано: амб,1-5)', NULL, NULL, '2026-09-23', '2026-09-23 04:11:53', '2026-09-23 04:11:53');

-- --------------------------------------------------------

--
-- Table structure for table `work_logs`
--

CREATE TABLE `work_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `work_type` enum('inventory_transfer','cartridge_replacement','repair_sent','repair_returned','manual') NOT NULL,
  `description` text NOT NULL,
  `branch_id` int(10) UNSIGNED DEFAULT NULL,
  `room_number` varchar(50) DEFAULT NULL,
  `performed_at` date NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `loggable_type` varchar(100) DEFAULT NULL,
  `loggable_id` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `work_logs`
--

INSERT INTO `work_logs` (`id`, `work_type`, `description`, `branch_id`, `room_number`, `performed_at`, `user_id`, `loggable_type`, `loggable_id`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'manual', 'Налаштування підключення вай-фаю до макбуку лікаря. налаштування роутера, налаштування принтера', 5, '121', '2026-01-27', 1, NULL, NULL, NULL, '2026-01-27 08:16:42', '2026-01-27 08:16:42'),
(2, 'manual', 'Підключення ЛАН розетки у сервері.\r\nпроведено комплекс робіт с знаходження кінця у сервері та підключення його до інтернету. Налаштування вай-фай роутера, перевідка працездатності', 5, '141', '2026-01-27', 1, NULL, NULL, NULL, '2026-01-27 08:18:54', '2026-01-27 08:18:54'),
(3, 'manual', 'Вирішення проблем із браузером, заповнення форми на перевірку в хелсі', 5, '236', '2026-01-28', 1, NULL, NULL, NULL, '2026-01-29 09:35:38', '2026-01-29 09:35:38'),
(4, 'manual', 'Налаштування ноутбуку, вайфай, налаштування принтеру', 1, '52', '2026-01-30', 1, NULL, NULL, NULL, '2026-01-30 10:59:32', '2026-01-30 10:59:32'),
(5, 'manual', 'Допомога із заповненням форми мсек', 1, '45/4', '2026-01-30', 1, NULL, NULL, NULL, '2026-01-30 11:00:09', '2026-01-30 11:00:09'),
(6, 'manual', 'Робота з заявками на закупівлю', 1, 'Загально', '2026-01-30', 1, NULL, NULL, NULL, '2026-01-30 11:01:25', '2026-01-30 11:01:25'),
(7, 'repair_sent', 'За наличку', 5, '201', '2026-02-05', 1, NULL, NULL, NULL, '2026-02-05 07:35:11', '2026-02-05 07:35:11'),
(8, 'inventory_transfer', 'Установка 1 ПК в лабораторию (монитор, пк, клава, мыша)', 5, '319', '2026-02-05', 1, NULL, NULL, NULL, '2026-02-05 07:36:09', '2026-02-05 07:36:09'),
(9, 'manual', 'Регулярные решения с ПО. Инна, ОА. Принтер', 1, '52', '2026-02-04', 1, NULL, NULL, NULL, '2026-02-05 07:37:11', '2026-02-05 07:37:11'),
(10, 'manual', 'работа с ЦМС', 1, '27', '2026-02-02', 1, NULL, NULL, NULL, '2026-02-05 07:40:54', '2026-02-05 07:40:54'),
(11, 'cartridge_replacement', 'Заміна картриджа 8651 на Epson', 1, '7a', '2026-02-10', 1, 'App\\Models\\CartridgeReplacement', 14, 'Запит створено через Telegram', '2026-02-10 10:27:06', '2026-02-10 10:27:06'),
(12, 'cartridge_replacement', 'Заміна картриджа T 8651 на M5690', 3, '205', '2026-02-24', 1, 'App\\Models\\CartridgeReplacement', 15, 'Запит створено через Telegram', '2026-02-24 09:51:24', '2026-02-24 09:51:24'),
(13, 'cartridge_replacement', 'Заміна картриджа HP на Workforce Pro WF-M5690', 2, '306', '2026-03-03', 1, 'App\\Models\\CartridgeReplacement', 16, 'Запит створено через Telegram', '2026-03-03 06:07:42', '2026-03-03 06:07:42'),
(14, 'inventory_transfer', 'Переміщення Принтер: Склад → 46', 1, '46', '2026-01-15', 1, 'App\\Models\\InventoryTransfer', 33, NULL, '2026-03-06 06:24:48', '2026-03-06 06:24:48'),
(15, 'cartridge_replacement', 'Заміна картриджа IC-T8651XXL. T8651 на Epson Workforce Pro WF-M5690.    Інв. номер: 101480247', 5, '216', '2026-03-17', 1, 'App\\Models\\CartridgeReplacement', 17, 'Запит створено через Telegram', '2026-03-17 07:03:44', '2026-03-17 07:03:44'),
(16, 'inventory_transfer', 'Переміщення Монітор: Підвал → 13', 1, '13', '2026-03-24', 1, 'App\\Models\\InventoryTransfer', 34, 'Для нових гінекологів', '2026-03-24 09:48:40', '2026-03-24 09:48:40'),
(17, 'inventory_transfer', 'Переміщення Миша: Підвал → 13', 1, '13', '2026-03-24', 1, 'App\\Models\\InventoryTransfer', 35, 'Для нових гінекологів', '2026-03-24 09:49:15', '2026-03-24 09:49:15'),
(18, 'inventory_transfer', 'Переміщення Клавіатура: Підвал → 13', 1, '13', '2026-03-24', 1, 'App\\Models\\InventoryTransfer', 36, 'Для нових гінекологів', '2026-03-24 09:49:33', '2026-03-24 09:49:33'),
(19, 'inventory_transfer', 'Переміщення Миша: Підвал → 23', 1, '23', '2026-03-24', 1, 'App\\Models\\InventoryTransfer', 37, 'Скринінг 40+', '2026-03-24 09:50:05', '2026-03-24 09:50:05'),
(20, 'cartridge_replacement', 'Заміна картриджа LaserJet на Jet Pro', 4, '103', '2026-03-25', 1, 'App\\Models\\CartridgeReplacement', 18, 'Запит створено через Telegram', '2026-03-25 09:08:31', '2026-03-25 09:08:31'),
(21, 'inventory_transfer', 'Переміщення Принтер: 22 → 45/4', 1, '45/4', '2026-03-31', 1, 'App\\Models\\InventoryTransfer', 38, NULL, '2026-03-31 05:42:24', '2026-03-31 05:42:24'),
(22, 'inventory_transfer', 'Переміщення Принтер: 46 → Підвал', 6, 'Підвал', '2026-03-31', 1, 'App\\Models\\InventoryTransfer', 39, NULL, '2026-03-31 06:22:34', '2026-03-31 06:22:34'),
(23, 'cartridge_replacement', 'Заміна картриджа 8651 на epson', 4, '102', '2026-04-01', 1, 'App\\Models\\CartridgeReplacement', 19, 'Запит створено через Telegram', '2026-04-01 08:00:10', '2026-04-01 08:00:10'),
(24, 'repair_returned', 'Полный спект обслуживания ПК. чистка, замена термопасты, замена блока питания, тестирование сборки', 2, '304', '2026-05-12', 1, NULL, NULL, NULL, '2026-05-12 06:44:37', '2026-05-12 06:44:37'),
(25, 'manual', 'Регистрация сертификатов, помощь с аккаунтом хелси пациента', 1, '7', '2026-05-14', 1, NULL, NULL, NULL, '2026-05-14 08:37:34', '2026-05-14 08:37:34'),
(26, 'manual', 'Обслуживание принтера', 1, '52', '2026-05-14', 1, NULL, NULL, NULL, '2026-05-14 08:37:54', '2026-05-14 08:37:54'),
(27, 'manual', 'Помощь с браузером. Не открывалось хелси, очистка кеша пк', 1, '16', '2026-05-14', 1, NULL, NULL, NULL, '2026-05-14 08:38:33', '2026-05-14 08:38:33'),
(28, 'manual', 'Диагностика ПК', 5, '124 (Гарбузенко)', '2026-05-12', 1, NULL, NULL, 'ССД вышел из строя. Очень низкое качество диска', '2026-05-14 08:39:33', '2026-05-14 08:39:33'),
(29, 'manual', 'Подготовка нового ССД. Установил виндовс, накатил пакет драйверов.', 5, '124 (Гарбузенко)', '2026-05-13', 1, NULL, NULL, NULL, '2026-05-14 08:40:24', '2026-05-14 08:40:24'),
(30, 'manual', 'Переподключение принтера,', 1, '13', '2026-05-14', 1, NULL, NULL, NULL, '2026-05-14 08:40:43', '2026-05-14 08:40:43'),
(31, 'manual', 'Замена хдд на ссд', 5, '234', '2026-05-19', 1, NULL, NULL, NULL, '2026-05-19 03:50:06', '2026-05-19 03:50:06'),
(34, 'cartridge_replacement', 'Обслуговування принтера', 2, '302', '2026-05-19', 1, NULL, NULL, NULL, '2026-05-20 03:36:01', '2026-05-20 03:36:01'),
(33, 'manual', 'Налащтування сканеру, перевірка принтера', 1, '47', '2026-05-19', 1, NULL, NULL, NULL, '2026-05-19 05:48:19', '2026-05-19 05:48:19'),
(35, 'manual', 'Налаштування принтеру', 1, '36', '2026-05-19', 1, NULL, NULL, NULL, '2026-05-20 03:41:21', '2026-05-20 03:41:21'),
(36, 'manual', 'Встановлення ключа на пк', 1, '45/3', '2026-05-19', 1, NULL, NULL, NULL, '2026-05-20 03:41:49', '2026-05-20 03:41:49'),
(37, 'manual', 'Вирішення проблеми з касою', 1, '29', '2026-05-22', 1, NULL, NULL, NULL, '2026-05-22 03:34:44', '2026-05-22 03:34:44'),
(38, 'inventory_transfer', 'Принтен 11200174 IV - в черговий кабінет + картридж', 5, '122', '2026-05-27', 1, NULL, NULL, NULL, '2026-05-27 06:45:43', '2026-05-27 06:45:43'),
(39, 'manual', 'Картридж відпрацьованих чорнил - помилка', 1, '7a', '2026-05-26', 1, NULL, NULL, NULL, '2026-05-27 06:46:57', '2026-05-27 06:46:57'),
(40, 'manual', 'Принтер - помилка друку', 1, '36', '2026-05-27', 1, NULL, NULL, NULL, '2026-05-27 06:47:27', '2026-05-27 06:47:27'),
(41, 'manual', 'Очищення кешу', 1, '38', '2026-05-29', 1, NULL, NULL, NULL, '2026-05-29 10:31:52', '2026-05-29 10:31:52'),
(42, 'manual', 'Спроба налаштування аналізатора', 5, '327', '2026-05-29', 1, NULL, NULL, NULL, '2026-05-29 10:32:31', '2026-05-29 10:32:31'),
(43, 'manual', 'Вирішення проблем запуску пк', 1, '7', '2026-05-29', 1, NULL, NULL, NULL, '2026-05-29 10:33:19', '2026-05-29 10:33:19'),
(44, 'cartridge_replacement', 'Заміна картриджа M-5690 на 101467092', 1, '43/3', '2026-06-03', 1, 'App\\Models\\CartridgeReplacement', 20, 'Запит створено через Telegram', '2026-06-03 03:01:26', '2026-06-03 03:01:26'),
(45, 'inventory_transfer', 'Переміщення Принтер: Підвал → 133', 5, '133', '2026-06-04', 1, 'App\\Models\\InventoryTransfer', 40, 'Відкриття кабінету скринінгу', '2026-06-04 04:16:00', '2026-06-04 04:16:00'),
(46, 'cartridge_replacement', 'Заміна картриджа 5630 на Epson', 1, '7', '2026-06-05', 1, 'App\\Models\\CartridgeReplacement', 21, 'Запит створено через Telegram', '2026-06-05 10:14:06', '2026-06-05 10:14:06'),
(47, 'inventory_transfer', 'Переміщення Принтер: 50 → к307', 2, 'к307', '2026-07-02', 1, 'App\\Models\\InventoryTransfer', 41, 'скринінг', '2026-07-02 04:28:07', '2026-07-02 04:28:07'),
(48, 'inventory_transfer', 'Переміщення Принтер: 58 → 106', 3, '106', '2026-07-02', 1, 'App\\Models\\InventoryTransfer', 42, 'скринінг', '2026-07-02 04:28:43', '2026-07-02 04:28:43'),
(49, 'inventory_transfer', 'Переміщення Миша: Підвал → 2 поверх', 6, '2 поверх', '2026-08-20', 1, 'App\\Models\\InventoryTransfer', 43, 'Под списание', '2026-08-20 06:47:19', '2026-08-20 06:47:19'),
(50, 'cartridge_replacement', 'Заміна картриджа IC-T8651XXL на Epson 101467097', 5, '133', '2026-08-24', 1, 'App\\Models\\CartridgeReplacement', 22, 'Запит створено через Telegram', '2026-08-24 04:26:00', '2026-08-24 04:26:00'),
(51, 'cartridge_replacement', 'Заміна картриджа T8651 на EPSON\nWF-M5690', 1, '9', '2026-09-14', 1, 'App\\Models\\CartridgeReplacement', 23, 'Запит створено через Telegram', '2026-09-14 06:57:06', '2026-09-14 06:57:06'),
(52, 'cartridge_replacement', 'Заміна картриджа 5690 на Epson', 1, '7a', '2026-09-21', 1, 'App\\Models\\CartridgeReplacement', 24, 'Запит створено через Telegram', '2026-09-21 07:03:02', '2026-09-21 07:03:02'),
(53, 'cartridge_replacement', 'Заміна картриджа Hp на Hp', 4, '103', '2026-09-22', 1, 'App\\Models\\CartridgeReplacement', 25, 'Запит створено через Telegram', '2026-09-22 08:02:06', '2026-09-22 08:02:06');

-- --------------------------------------------------------

--
-- Table structure for table `writeoff_requests`
--

CREATE TABLE `writeoff_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `request_number` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('draft','submitted','approved','completed') NOT NULL DEFAULT 'draft',
  `description` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `writeoff_date` date NOT NULL,
  `archived_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `writeoff_requests`
--

INSERT INTO `writeoff_requests` (`id`, `request_number`, `user_id`, `status`, `description`, `notes`, `writeoff_date`, `archived_at`, `created_at`, `updated_at`) VALUES
(1, 'SPY-2026-000001', 1, 'draft', NULL, NULL, '2026-06-02', NULL, '2026-06-02 03:37:16', '2026-06-02 03:37:16'),
(2, 'SPY-2026-000002', 3, 'draft', NULL, NULL, '2026-06-02', NULL, '2026-06-02 10:10:37', '2026-06-02 10:10:37'),
(3, 'SPY-2026-000003', 1, 'draft', NULL, NULL, '2026-07-27', NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(4, 'SPY-2026-000004', 3, 'submitted', NULL, NULL, '2026-08-31', NULL, '2026-08-12 03:56:18', '2026-08-12 03:58:26'),
(5, 'SPY-2026-000005', 3, 'draft', NULL, NULL, '2026-08-12', NULL, '2026-08-12 07:54:01', '2026-08-12 07:54:01'),
(6, 'SPY-2026-000006', 3, 'draft', NULL, NULL, '2026-08-28', NULL, '2026-08-28 04:38:18', '2026-08-28 04:38:18');

-- --------------------------------------------------------

--
-- Table structure for table `writeoff_request_items`
--

CREATE TABLE `writeoff_request_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `writeoff_request_id` bigint(20) UNSIGNED NOT NULL,
  `inventory_id` bigint(20) UNSIGNED DEFAULT NULL,
  `item_name` varchar(255) NOT NULL,
  `unit` varchar(255) NOT NULL DEFAULT 'шт',
  `quantity` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `writeoff_request_items`
--

INSERT INTO `writeoff_request_items` (`id`, `writeoff_request_id`, `inventory_id`, `item_name`, `unit`, `quantity`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 1822, 'Рукавички Зірка10/20', 'шт', 5, NULL, '2026-06-02 03:37:16', '2026-06-02 03:37:16'),
(2, 1, 1885, 'Одномісна розетка зовнішнього монтажу', 'шт', 2, NULL, '2026-06-02 03:37:16', '2026-06-02 03:37:16'),
(3, 1, 1971, 'Амстронг', 'шт', 4, NULL, '2026-06-02 03:37:16', '2026-06-02 03:37:16'),
(4, 1, 1855, 'Гофротруба для унітаза  армовона L-320-540', 'шт', 1, NULL, '2026-06-02 03:37:16', '2026-06-02 03:37:16'),
(5, 1, 1887, 'Вимикач накладний подвійний', 'шт', 3, NULL, '2026-06-02 03:37:16', '2026-06-02 03:37:16'),
(6, 2, 1822, 'Рукавички Зірка10/20', 'шт', 5, NULL, '2026-06-02 10:10:37', '2026-06-02 10:10:37'),
(7, 2, 1971, 'Амстронг', 'шт', 4, NULL, '2026-06-02 10:10:37', '2026-06-02 10:10:37'),
(8, 2, 1881, 'Кабель канал NEOMAKSULT.25*16mm 2м', 'шт', 12, NULL, '2026-06-02 10:10:37', '2026-06-02 10:10:37'),
(9, 2, 1883, 'Автомат-16 1Р', 'шт', 3, NULL, '2026-06-02 10:10:37', '2026-06-02 10:10:37'),
(10, 3, 1887, 'Вимикач накладний подвійний', 'шт', 5, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(11, 3, 1808, 'Туалетний папір Lizoform med-3складання №200', 'шт', 178, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(12, 3, 2027, 'Тактильна стрічка 5см,', 'м,', 2, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(13, 3, 2028, 'Тактильна стрічка 10см,', 'м,', 10, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(14, 3, 1797, 'Засіб для видалення плям на кольорових та білих речах', 'пляшки', 10, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(15, 3, 1848, 'Засіб миючий порошкоподібний універсальний', 'шт', 4, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(16, 3, 1793, 'Засіб для очищення поверхонь та нейтралізації неприємних засобів', 'пляшки', 33, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(17, 3, 1787, 'Мило рідке для шкіри рук і тіла.', 'каністра', 10, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(18, 3, 1785, 'Проф. засіб для миття та очищення ванних кімнат', 'каністра', 1, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(19, 3, 1784, 'Проф. засіб для миття та очищення ванних кімнат', 'каністра', 12, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(20, 3, 1806, 'Целюлозні паперові рушники', 'шт', 676, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(21, 3, 1807, 'Папір туалетний Лєста', 'шт', 792, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(22, 3, 1823, 'Стакани одноразові 180мл прозорі 100шт', 'шт', 23, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(23, 3, 1818, 'Серветка Progressiv, червона', 'шт', 10, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(24, 3, 1817, 'Серветка Progressiv, зелена', 'шт', 10, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(25, 3, 1816, 'Серветка Progressiv, жовта', 'шт', 10, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(26, 3, 1814, 'Серветка Progressiv, блакитна', 'шт', 10, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(27, 3, 1822, 'Рукавички Зірка10/20', 'шт', 7, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(28, 3, 1878, 'Барель TrionZinc/Kedr-80мм,Ключ,', 'шт', 8, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(29, 3, 1835, 'Клей-олівець', 'шт', 24, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(30, 3, 1804, 'Файл прозорий', 'уп', 45, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(31, 3, 1803, 'Бумага офісна А4 500арк', 'шт', 178, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(32, 3, 1875, 'Корпус замка код:165*35', 'шт', 1, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(33, 3, 1813, 'Віник Драпак ручний (Дереза) з держаком', 'шт', 13, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(34, 3, 1832, 'Папка сегригатор 70мм', 'шт', 19, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(35, 3, 1831, 'Папка сегригатор 50мм', 'шт', 7, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(36, 3, 1794, 'Професіний засіб для дезинфекції та очищення поверхонь', 'каністри', 2, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(37, 3, 1791, 'Засіб для прання білизни', 'каністра', 8, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(38, 3, 1799, 'Професійний засіб для очищення різноманітних поверхонь універсального використання', 'каністра', 3, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(39, 3, 1795, 'Професійний концентрований засіб для миття всіх видів поверхонь', 'каністра', 11, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(40, 3, 1790, 'Засіб для прання виробів з бавовни, льону та синтетичних матеріалів', 'каністра', 7, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(41, 3, 1877, 'Професійний засіб для очищення труб та каналізації', 'шт', 6, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(42, 3, 1786, 'Професійний засіб для чищення санітарно-технічного обладнання з антибактеріальним ефектом', 'каністра', 9, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(43, 3, 2016, 'Щітка для унітазу з кріпл, до стіни', 'шт', 1, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(44, 3, 2017, 'Руковички захистні латексні, нестерильні', 'шт', 15, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(45, 3, 1833, 'Папка швидкозшивач пластиковий А4 з прозорим верхом і перфорацією,різного коліру, 10шт, в уп', 'шт', 100, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(46, 3, 2052, 'Книга А4 96стр,', 'шт', 15, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(47, 3, 1829, 'Пакети для сміття 60л', 'шт', 18, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(48, 3, 1830, 'Пакети для сміття 35л', 'шт', 36, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(49, 3, 1828, 'Пакети для сміття 120л', 'шт', 34, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(50, 3, 1800, 'Професійний концентрований засіб для ручного миття посуду', 'каністра', 5, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(51, 3, 1855, 'Гофротруба для унітаза  армовона L-320-540', 'шт', 1, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(52, 3, 1824, 'Пензлик Английський 2(50)', 'шт', 2, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(53, 3, 1840, 'Пензлик Английський 2,5 (63)', 'шт', 2, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(54, 3, 1884, 'Корпус зовнішний під 1-2 автомат з кришкою', 'шт', 2, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(55, 3, 1995, 'Schneider Asfora білий Розетка', 'шт', 10, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(56, 3, 1866, 'Кран кульковий1/2папа+мама', 'шт', 1, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(57, 3, 1981, 'Муфта с МРВ 20*1/2', 'шт', 1, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(58, 3, 1871, 'Кутник під пайку 20    90градус', 'шт', 1, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(59, 3, 1892, 'Коробка установча під бетон 65*45', 'шт', 10, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(60, 3, 2051, 'Відро поліетиленове 10л,', 'шт', 5, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(61, 3, 1820, 'Віник №75 різний колір нитки 3-ох шов,', 'шт', 13, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(62, 3, 1844, 'Рукавички латекс Obery XL', 'шт', 17, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(63, 3, 1876, 'Корпус замка код:35*85', 'шт', 2, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(64, 3, 1991, 'Труба ПВХ гнуча гофрована с протяж, сіра', 'шт', 100, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(65, 3, 1977, 'Коробка розпод,герм 100*100*70мм', 'шт', 5, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(66, 3, 1886, 'Пятимісна розетка зовнішнього монтажу', 'шт', 13, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(67, 3, 1996, 'Viko Vera білий накладна розетка', 'шт', 1, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(68, 3, 1888, 'Ізоляційна стрічка 3м', 'шт', 2, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(69, 3, 1798, 'Білизна скло', 'каністра', 2, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(70, 3, 1805, 'Папка-швидкозшивач картонна', 'шт', 30, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(71, 3, 1856, 'Пакля UNIPAK 100 гр,', 'уп,', 1, NULL, '2026-07-14 04:11:09', '2026-07-14 04:11:09'),
(72, 4, 2005, 'Поплавець-арматура SOLOPLAST', 'шт', 2, NULL, '2026-08-12 03:56:18', '2026-08-12 03:56:18'),
(73, 4, 2082, 'Мережевий подовжувач Defender S418 1.8 m 4 роз switch white (99237)', 'шт', 1, NULL, '2026-08-12 03:56:18', '2026-08-12 03:56:18'),
(74, 4, 2062, 'Контейнер з чорнилом Epson 664 комплект B/C/M/Y (по70мл) L100/L200 (C13T66464A)', 'шт', 2, NULL, '2026-08-12 03:56:18', '2026-08-12 03:56:18'),
(75, 5, 1804, 'Файл А4 100шт, глянсовий', 'уп', 14, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(76, 5, 1813, 'Віник ручний з держаком', 'шт', 2, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(77, 5, 1806, 'Целюлозні паперові рушники 3-складання', 'шт', 354, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(78, 5, 1807, 'Папір туалетний Лєста', 'шт', 381, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(79, 5, 1828, 'Мішки для сміття 120л', 'шт', 22, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(80, 5, 1835, 'Клеючий олівець', 'шт', 15, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(81, 5, 1877, 'Білизна трубоочисник 5000мл,', 'шт', 3, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(82, 5, 1848, 'Білизна проф,еліт економ,5кг', 'шт', 3, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(83, 5, 1791, 'Білизна проф еліт універсальний, 5000мл', 'каністра', 3, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(84, 5, 1784, 'Білизна кераміка', 'каністра', 6, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(85, 5, 1790, 'Білизна базік прання', 'каністра', 3, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(122, 5, NULL, 'Білизна плямовивідник', 'шт', 10, 'Видано в роботу', '2026-08-13 05:15:16', '2026-08-28 05:04:52'),
(86, 5, 1795, 'Білизна поверхня з ароматом грейфрукт 5000мл', 'каністра', 6, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(87, 5, 2009, 'Циліндр замка АСК-80', 'шт', 5, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(88, 5, 1822, 'Рукавички Зірка10/20', 'шт', 5, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(89, 5, 1885, 'Розетка 1-а зовнішня з з/к', 'шт', 1, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(90, 5, 1803, 'Папір  А4 80г/м2 , 500арк', 'шт', 40, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(91, 5, 1833, 'Папка швидкозшивач  А4 з прозорим верхом і перфорацією,різного коліру, 10шт, в уп', 'шт', 40, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(92, 5, 1832, 'Папка швидкосшивач 7см,', 'шт', 10, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(93, 5, 1823, 'Стакани інпак пластиковий 180мл 100шт', 'шт', 10, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(94, 5, 1875, 'Корпус замка код:165*35', 'шт', 8, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(95, 5, 2054, 'Викрутка індікаторна', 'шт', 1, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(96, 5, 1787, 'Мило рідке для шкіри рук і тіла.', 'каністра', 5, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(97, 5, 2017, 'Руковички захистні латексні, нестерильні', 'шт', 15, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(98, 5, 2026, 'Емаль пф-115 жовта 2,8кг', 'шт', 1, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(99, 5, 1831, 'Папка сегригатор 50мм', 'шт', 4, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(140, 5, 2065, 'Картридж HP LJ 17A, Pro M130 Black (CF217A)', 'шт', 1, 'Видано в роботу', '2026-08-17 04:30:46', '2026-08-28 05:04:52'),
(139, 5, NULL, 'Мережевий подовжувач Defender S418 1.8 m 4 роз', 'шт', 1, 'Видано в роботу, Фесенко к206', '2026-08-14 07:04:39', '2026-08-28 05:04:52'),
(102, 5, 1992, 'Шланг водяний1/2*1/2 м*м 40 см', 'шт', 2, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(103, 5, 2005, 'Поплавець-арматура SOLOPLAST', 'шт', 2, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(104, 5, 1853, 'Кріплення бочка до унітазу 100мм', 'шт', 1, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(105, 5, 1884, 'Коробка під 1-2 автомат з кришкою', 'шт', 4, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(106, 5, 1894, 'Індикаторна викрутка DCY-1788', 'шт', 1, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(107, 5, 1887, 'Вимикач 2-й зовнішний', 'шт', 4, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(108, 5, 1892, 'Коробка установча під бетон', 'шт', 1, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(109, 5, 1895, 'Подовжувач 4гн 2м', 'шт', 1, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(110, 5, 1881, 'Кабель канал NEOMAХ ULT.25*16mm', 'шт', 10, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(111, 5, 1970, 'Автомат 1Р 10А', 'шт', 4, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(112, 5, 1820, 'Віник сорго', 'шт', 1, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(113, 5, NULL, 'Розетка 5-азовнішня з з/к', 'шт', 1, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(114, 5, NULL, 'Тактильна гумова стрічка СТ50 жовта', 'шт', 2, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(115, 5, NULL, 'Тактильна гумова стрічка СТ100 жовта', 'шт', 10, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(116, 5, NULL, 'Труба ПВХ гнучка гофрована', 'шт', 130, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(117, 5, NULL, 'Корпус замка Редкайн 92/35', 'шт', 2, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(118, 5, NULL, 'Кронштейн', 'шт', 2, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(119, 5, NULL, 'Поплавок арматура нап, з бок,підвед, \"Ліфт\"SOLOPLAST', 'шт', 2, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(120, 5, NULL, 'Білизна медкомфорт 750мл', 'шт', 12, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(121, 5, NULL, 'Білизна медкомфорт,1000мл,', 'шт', 3, 'Видано в роботу', '2026-08-12 07:54:01', '2026-08-28 05:04:52'),
(123, 5, NULL, 'Білизна анти бак', 'шт', 3, 'Видано в роботу', '2026-08-13 05:15:16', '2026-08-28 05:04:52'),
(124, 5, NULL, 'Білизна сантехніка', 'шт', 3, 'Видано в роботу', '2026-08-13 06:08:07', '2026-08-28 05:04:52'),
(125, 5, NULL, 'Білизна саніхлор', 'шт', 3, 'Видано в роботу', '2026-08-13 06:08:07', '2026-08-28 05:04:52'),
(126, 5, NULL, 'Мішки д/сміття 35л/50шт', 'шт', 21, 'Видано в роботу', '2026-08-13 06:08:07', '2026-08-28 05:04:52'),
(127, 5, NULL, 'Папка швидкосшивач А4 з прозорим верхом і пперфорацією', 'шт', 20, 'Видано в роботу', '2026-08-13 06:08:07', '2026-08-28 05:04:52'),
(128, 5, NULL, 'Руковички побудові універсаль, латексні 2шт, вуп,', 'шт', 3, 'Видано в роботу', '2026-08-13 06:08:07', '2026-08-28 05:04:52'),
(129, 5, NULL, 'Серветки Рrogressiv блакитна', 'шт', 10, 'Видано в роботу', '2026-08-13 06:08:07', '2026-08-28 05:04:52'),
(130, 5, NULL, 'Серветки Progresiv жовта', 'шт', 10, 'Видано в роботу', '2026-08-13 06:08:07', '2026-08-28 05:04:52'),
(131, 5, NULL, 'Серветки Progresiv зелена', 'шт', 10, 'Видано в роботу', '2026-08-13 06:08:07', '2026-08-28 05:04:52'),
(132, 5, NULL, 'Серветки Progresiv червона', 'шт', 10, 'Видано в роботу', '2026-08-13 06:08:07', '2026-08-28 05:04:52'),
(133, 5, NULL, 'Степлер', 'шт', 5, 'Видано в роботу', '2026-08-13 06:08:07', '2026-08-28 05:04:52'),
(134, 5, NULL, 'Туалетний папір Blanidas Med 3-скл,№200', 'шт', 34, 'Видано в роботу', '2026-08-13 06:08:07', '2026-08-28 05:04:52'),
(135, 5, NULL, 'АвтоматHager C 1П 10А', 'шт', 4, 'Видано в роботу', '2026-08-13 06:08:07', '2026-08-28 05:04:52'),
(136, 5, NULL, 'Замок круглий JanesСР Хром', 'шт', 1, 'Видано в роботу', '2026-08-13 06:08:07', '2026-08-28 05:04:52'),
(137, 5, NULL, 'Книга А4,96стр', 'шт', 4, 'Видано в роботу', '2026-08-13 06:08:07', '2026-08-28 05:04:52'),
(138, 5, NULL, 'Мило туалетне,брусок 100г', 'шт', 30, 'Видано в роботу', '2026-08-13 06:08:07', '2026-08-28 05:04:52'),
(154, 6, NULL, 'Конектор RJ45 cat5e UTP 8P8C 100шт', 'уп', 1, 'Видано в роботу', '2026-08-28 05:00:58', '2026-08-28 05:00:58'),
(156, 6, NULL, 'Модуль пам\'яті для комп\'ютера DDR3 8GB 1600 MHz', 'шт', 2, 'Видано в роботу', '2026-08-28 05:00:58', '2026-08-28 05:00:58'),
(155, 6, NULL, 'Мережевий фільтр живлення Patron 1.8м 3 роз чорний', 'шт', 3, 'Видано в роботу', '2026-08-28 05:00:58', '2026-08-28 05:00:58'),
(153, 6, NULL, 'Акамуляторна батарея CSB UPS123607F2 12B', 'шт', 5, 'Видано в роботу', '2026-08-28 04:38:18', '2026-08-28 05:00:58'),
(157, 6, NULL, 'Модуль пам\'яті для комп\'ютера DDR3 8GB 2666 MHz', 'шт', 2, 'Видано в роботу', '2026-08-28 05:00:58', '2026-08-28 05:00:58'),
(158, 6, NULL, 'Батарейка', 'шт', 15, 'Видано в роботу', '2026-08-28 05:00:58', '2026-08-28 05:00:58'),
(159, 6, NULL, 'Драм картридж HP Imaging Drum 19A', 'шт', 1, 'Видано в роботу', '2026-08-28 05:00:58', '2026-08-28 05:00:58'),
(160, 6, NULL, 'Кабель HDMI 1.5 м', 'шт', 3, 'Видано в роботу', '2026-08-28 05:00:58', '2026-08-28 05:00:58'),
(161, 6, NULL, 'Кабель SATA 0.45 м', 'шт', 4, 'Видано в роботу', '2026-08-28 05:00:58', '2026-08-28 05:00:58'),
(162, 6, NULL, 'Кабель USB 1.8 м', 'шт', 3, 'Видано в роботу', '2026-08-28 05:00:58', '2026-08-28 05:00:58'),
(163, 6, NULL, 'Кабель VGA 1.8 м', 'шт', 2, 'Видано в роботу', '2026-08-28 05:00:58', '2026-08-28 05:00:58'),
(164, 6, NULL, 'Картридж HP LJ 44A', 'шт', 1, 'Видано в роботу', '2026-08-28 05:00:58', '2026-08-28 05:00:58'),
(165, 6, NULL, 'Картридж HP LJ 17A Pro', 'шт', 1, 'Видано в роботу', '2026-08-28 05:00:58', '2026-08-28 05:00:58'),
(166, 6, NULL, 'Картридж Vinga Samsung MLT-D111S', 'шт', 1, 'Видано в роботу', '2026-08-28 05:00:58', '2026-08-28 05:00:58'),
(167, 6, NULL, 'Картридж сумісний Epson T8651 10K арк., ліцензія', 'шт', 6, 'Видано в роботу', '2026-08-28 05:00:58', '2026-08-28 05:00:58'),
(168, 6, NULL, 'Комутатор мережевий Mercus MS105G', 'шт', 4, 'Видано в роботу', '2026-08-28 05:00:58', '2026-08-28 05:00:58'),
(169, 6, NULL, 'Контейнер з чорнилом Epson L100/L200 комплект', 'шт', 2, 'Видано в роботу', '2026-08-28 05:00:58', '2026-08-28 05:00:58'),
(170, 6, NULL, 'Мережевий подовжувач Europower EPG518 5роз.', 'шт', 2, 'Видано в роботу', '2026-08-28 05:00:58', '2026-08-28 05:00:58'),
(171, 6, NULL, 'Мережевий подовжувач DEFENDER S418 1.8м 4роз.', 'шт', 2, 'Видано в роботу', '2026-08-28 05:00:58', '2026-08-28 05:00:58'),
(172, 6, 1788, 'Картридж відпрпацьованих чорнил (зливний)', 'шт', 6, 'Видано в роботу', '2026-08-28 05:00:58', '2026-08-28 05:00:58'),
(173, 6, NULL, 'Набір інструментів для мережі 11', 'шт', 1, 'Видано в роботу', '2026-08-28 05:00:58', '2026-08-28 05:00:58'),
(174, 6, NULL, 'Патч-корд Vanga 3m', 'шт', 7, 'Видано в роботу', '2026-08-28 05:00:58', '2026-08-28 05:00:58'),
(175, 6, NULL, 'Патч-корд Vanga 5m', 'шт', 2, 'Видано в роботу', '2026-08-28 05:00:58', '2026-08-28 05:00:58'),
(176, 6, NULL, 'Єлектро двигатель', 'шт', 1, 'Н\\Б відновленню не підлягає', '2026-08-28 05:00:58', '2026-08-28 05:00:58'),
(177, 6, NULL, 'Тестер кабельний NCT-3', 'шт', 1, 'Видано в роботу', '2026-08-28 05:00:58', '2026-08-28 05:00:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `telegram_id` (`telegram_id`);

--
-- Indexes for table `ambulatorii`
--
ALTER TABLE `ambulatorii`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ambulatorii_name_unique` (`name`),
  ADD KEY `ambulatorii_branch_id_index` (`branch_id`);

--
-- Indexes for table `api_tokens`
--
ALTER TABLE `api_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`);

--
-- Indexes for table `app_settings`
--
ALTER TABLE `app_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `app_settings_key_unique` (`key`) USING HASH;

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cabinets`
--
ALTER TABLE `cabinets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cabinets_branch_id_number_unique` (`branch_id`,`number`),
  ADD KEY `cabinets_branch_id_index` (`branch_id`),
  ADD KEY `cabinets_ambulatoriya_id_foreign` (`ambulatoriya_id`);

--
-- Indexes for table `cabinet_shifts`
--
ALTER TABLE `cabinet_shifts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cabinet_shifts_cabinet_id_shift_unique` (`cabinet_id`,`shift`),
  ADD KEY `cabinet_shifts_doctor_id_foreign` (`doctor_id`),
  ADD KEY `cabinet_shifts_nurse_id_foreign` (`nurse_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cartridge_replacements`
--
ALTER TABLE `cartridge_replacements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `printer_inventory_id` (`printer_inventory_id`),
  ADD KEY `idx_replacement_date` (`replacement_date`),
  ADD KEY `idx_branch_room` (`branch_id`,`room_number`);

--
-- Indexes for table `contractors`
--
ALTER TABLE `contractors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contractor_operations`
--
ALTER TABLE `contractor_operations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contractor_operations_contractor_id_foreign` (`contractor_id`),
  ADD KEY `contractor_operations_user_id_foreign` (`user_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `inventory_audits`
--
ALTER TABLE `inventory_audits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventory_audits_user_id_foreign` (`user_id`);

--
-- Indexes for table `inventory_audit_items`
--
ALTER TABLE `inventory_audit_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventory_logs_user_id_foreign` (`user_id`);

--
-- Indexes for table `inventory_templates`
--
ALTER TABLE `inventory_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_transfers`
--
ALTER TABLE `inventory_transfers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `medical_staff`
--
ALTER TABLE `medical_staff`
  ADD PRIMARY KEY (`id`),
  ADD KEY `medical_staff_ambulatoriya_id_foreign` (`ambulatoriya_id`),
  ADD KEY `medical_staff_type_index` (`type`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `purchase_requests`
--
ALTER TABLE `purchase_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchase_requests_request_number_unique` (`request_number`),
  ADD KEY `purchase_requests_user_id_foreign` (`user_id`);

--
-- Indexes for table `purchase_request_items`
--
ALTER TABLE `purchase_request_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_request_items_purchase_request_id_foreign` (`purchase_request_id`),
  ADD KEY `purchase_request_items_warehouse_item_id_foreign` (`warehouse_item_id`);

--
-- Indexes for table `repair_masters`
--
ALTER TABLE `repair_masters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `repair_orders`
--
ALTER TABLE `repair_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `repair_orders_order_number_unique` (`order_number`),
  ADD KEY `repair_orders_approved_by_foreign` (`approved_by`),
  ADD KEY `repair_orders_status_created_at_index` (`status`,`created_at`),
  ADD KEY `repair_orders_user_id_index` (`user_id`),
  ADD KEY `repair_orders_repair_master_id_index` (`repair_master_id`);

--
-- Indexes for table `repair_order_items`
--
ALTER TABLE `repair_order_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `repair_order_items_repair_order_id_equipment_id_unique` (`repair_order_id`,`equipment_id`),
  ADD KEY `repair_order_items_repair_order_id_index` (`repair_order_id`),
  ADD KEY `repair_order_items_equipment_id_index` (`equipment_id`);

--
-- Indexes for table `repair_requests`
--
ALTER TABLE `repair_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `branch_id` (`branch_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_user` (`user_telegram_id`),
  ADD KEY `idx_created` (`created_at`),
  ADD KEY `repair_requests_old_inventory_id_foreign` (`old_inventory_id`);

--
-- Indexes for table `repair_trackings`
--
ALTER TABLE `repair_trackings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `room_inventory`
--
ALTER TABLE `room_inventory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `template_id` (`template_id`),
  ADD KEY `idx_branch_room` (`branch_id`,`room_number`),
  ADD KEY `idx_inventory_number` (`inventory_number`),
  ADD KEY `idx_serial_number` (`serial_number`),
  ADD KEY `room_inventory_category_index` (`category`),
  ADD KEY `room_inventory_quantity_index` (`quantity`),
  ADD KEY `room_inventory_balance_code_index` (`balance_code`),
  ADD KEY `room_inventory_equipment_type_index` (`equipment_type`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `telegram_profiles`
--
ALTER TABLE `telegram_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `telegram_profiles_telegram_user_id_unique` (`telegram_user_id`),
  ADD UNIQUE KEY `telegram_profiles_telegram_chat_id_unique` (`telegram_chat_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_telegram_id_unique` (`telegram_id`);

--
-- Indexes for table `user_states`
--
ALTER TABLE `user_states`
  ADD PRIMARY KEY (`telegram_id`);

--
-- Indexes for table `warehouse_inventories`
--
ALTER TABLE `warehouse_inventories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `warehouse_inventories_inventory_number_unique` (`inventory_number`),
  ADD KEY `warehouse_inventories_user_id_foreign` (`user_id`);

--
-- Indexes for table `warehouse_inventory_items`
--
ALTER TABLE `warehouse_inventory_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `warehouse_items`
--
ALTER TABLE `warehouse_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `warehouse_items_code_unique` (`code`);

--
-- Indexes for table `warehouse_movements`
--
ALTER TABLE `warehouse_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `warehouse_movements_user_id_foreign` (`user_id`),
  ADD KEY `warehouse_movements_issued_to_user_id_foreign` (`issued_to_user_id`),
  ADD KEY `warehouse_movements_inventory_id_foreign` (`inventory_id`);

--
-- Indexes for table `work_logs`
--
ALTER TABLE `work_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `work_logs_work_type_index` (`work_type`),
  ADD KEY `work_logs_branch_id_index` (`branch_id`),
  ADD KEY `work_logs_performed_at_index` (`performed_at`),
  ADD KEY `work_logs_user_id_index` (`user_id`),
  ADD KEY `work_logs_loggable_type_loggable_id_index` (`loggable_type`,`loggable_id`),
  ADD KEY `work_logs_created_at_index` (`created_at`);

--
-- Indexes for table `writeoff_requests`
--
ALTER TABLE `writeoff_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `writeoff_requests_request_number_unique` (`request_number`) USING HASH,
  ADD KEY `writeoff_requests_user_id_foreign` (`user_id`);

--
-- Indexes for table `writeoff_request_items`
--
ALTER TABLE `writeoff_request_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `writeoff_request_items_writeoff_request_id_foreign` (`writeoff_request_id`),
  ADD KEY `writeoff_request_items_inventory_id_foreign` (`inventory_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ambulatorii`
--
ALTER TABLE `ambulatorii`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `api_tokens`
--
ALTER TABLE `api_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `app_settings`
--
ALTER TABLE `app_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `cabinets`
--
ALTER TABLE `cabinets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cabinet_shifts`
--
ALTER TABLE `cabinet_shifts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cartridge_replacements`
--
ALTER TABLE `cartridge_replacements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `contractors`
--
ALTER TABLE `contractors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contractor_operations`
--
ALTER TABLE `contractor_operations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_audits`
--
ALTER TABLE `inventory_audits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_audit_items`
--
ALTER TABLE `inventory_audit_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_templates`
--
ALTER TABLE `inventory_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `inventory_transfers`
--
ALTER TABLE `inventory_transfers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medical_staff`
--
ALTER TABLE `medical_staff`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_requests`
--
ALTER TABLE `purchase_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `purchase_request_items`
--
ALTER TABLE `purchase_request_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=587;

--
-- AUTO_INCREMENT for table `repair_masters`
--
ALTER TABLE `repair_masters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `repair_orders`
--
ALTER TABLE `repair_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `repair_order_items`
--
ALTER TABLE `repair_order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `repair_requests`
--
ALTER TABLE `repair_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `repair_trackings`
--
ALTER TABLE `repair_trackings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `room_inventory`
--
ALTER TABLE `room_inventory`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2087;

--
-- AUTO_INCREMENT for table `telegram_profiles`
--
ALTER TABLE `telegram_profiles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `warehouse_inventories`
--
ALTER TABLE `warehouse_inventories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `warehouse_inventory_items`
--
ALTER TABLE `warehouse_inventory_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `warehouse_items`
--
ALTER TABLE `warehouse_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `warehouse_movements`
--
ALTER TABLE `warehouse_movements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=981;

--
-- AUTO_INCREMENT for table `work_logs`
--
ALTER TABLE `work_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `writeoff_requests`
--
ALTER TABLE `writeoff_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `writeoff_request_items`
--
ALTER TABLE `writeoff_request_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=178;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cabinets`
--
ALTER TABLE `cabinets`
  ADD CONSTRAINT `cabinets_ambulatoriya_id_foreign` FOREIGN KEY (`ambulatoriya_id`) REFERENCES `ambulatorii` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `cabinet_shifts`
--
ALTER TABLE `cabinet_shifts`
  ADD CONSTRAINT `cabinet_shifts_cabinet_id_foreign` FOREIGN KEY (`cabinet_id`) REFERENCES `cabinets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cabinet_shifts_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `medical_staff` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `cabinet_shifts_nurse_id_foreign` FOREIGN KEY (`nurse_id`) REFERENCES `medical_staff` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `cartridge_replacements`
--
ALTER TABLE `cartridge_replacements`
  ADD CONSTRAINT `cartridge_replacements_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  ADD CONSTRAINT `cartridge_replacements_printer_inventory_id_foreign` FOREIGN KEY (`printer_inventory_id`) REFERENCES `room_inventory` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `contractor_operations`
--
ALTER TABLE `contractor_operations`
  ADD CONSTRAINT `contractor_operations_contractor_id_foreign` FOREIGN KEY (`contractor_id`) REFERENCES `contractors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contractor_operations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory_audits`
--
ALTER TABLE `inventory_audits`
  ADD CONSTRAINT `inventory_audits_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  ADD CONSTRAINT `inventory_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `medical_staff`
--
ALTER TABLE `medical_staff`
  ADD CONSTRAINT `medical_staff_ambulatoriya_id_foreign` FOREIGN KEY (`ambulatoriya_id`) REFERENCES `ambulatorii` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_requests`
--
ALTER TABLE `purchase_requests`
  ADD CONSTRAINT `purchase_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `purchase_request_items`
--
ALTER TABLE `purchase_request_items`
  ADD CONSTRAINT `purchase_request_items_purchase_request_id_foreign` FOREIGN KEY (`purchase_request_id`) REFERENCES `purchase_requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `repair_requests`
--
ALTER TABLE `repair_requests`
  ADD CONSTRAINT `repair_requests_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  ADD CONSTRAINT `repair_requests_old_inventory_id_foreign` FOREIGN KEY (`old_inventory_id`) REFERENCES `room_inventory` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `room_inventory`
--
ALTER TABLE `room_inventory`
  ADD CONSTRAINT `room_inventory_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  ADD CONSTRAINT `room_inventory_ibfk_2` FOREIGN KEY (`template_id`) REFERENCES `inventory_templates` (`id`);

--
-- Constraints for table `warehouse_inventories`
--
ALTER TABLE `warehouse_inventories`
  ADD CONSTRAINT `warehouse_inventories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `warehouse_movements`
--
ALTER TABLE `warehouse_movements`
  ADD CONSTRAINT `warehouse_movements_issued_to_user_id_foreign` FOREIGN KEY (`issued_to_user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `warehouse_movements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
