-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 22, 2025 at 06:52 PM
-- Server version: 10.11.11-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `main`
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
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `printer_inventory_id` int(11) DEFAULT NULL,
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
(1, 330489980, 'metamorf_dev', 1, '27', NULL, '123123123', 'HP CF217A', '2025-07-05', NULL, '2025-07-04 22:21:43', ''),
(2, 330489980, 'metamorf_dev', 1, '27', NULL, '123123123', 'Епсон', '2025-07-05', NULL, '2025-07-04 22:26:26', ''),
(3, 330489980, 'metamorf_dev', 1, '27', NULL, '123123123', 'HP CF217A', '2025-07-06', NULL, '2025-07-06 19:36:52', ''),
(4, 1748926034, NULL, 1, '27', NULL, 'epson', '31222', '2025-08-13', NULL, '2025-08-13 08:54:05', ''),
(5, 542503468, 'ksenia_kalyan', 3, '204', NULL, 'Epson WorkForce Pro, WF-M5690', 'T8651', '2025-08-20', NULL, '2025-08-20 05:38:44', ''),
(6, 1951296190, NULL, 1, '37', NULL, 'Epson,WF-M5690', 'инвентарный номер 101467090', '2025-08-20', NULL, '2025-08-20 06:44:42', ''),
(7, 330489980, 'metamorf_dev', 1, '27', NULL, 'test', 'testtest', '2025-08-27', NULL, '2025-08-27 21:27:10', '2025-08-27 21:27:10');

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
  `transfer_number` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `from_branch_id` bigint(20) UNSIGNED NOT NULL,
  `to_branch_id` bigint(20) UNSIGNED NOT NULL,
  `from_room` varchar(255) DEFAULT NULL,
  `to_room` varchar(255) DEFAULT NULL,
  `transfer_date` date NOT NULL,
  `status` enum('planned','in_transit','completed','cancelled') NOT NULL DEFAULT 'planned',
  `reason` text NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(10, '2025_01_20_000001_add_warehouse_keeper_role', 7);

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
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_requests`
--

INSERT INTO `purchase_requests` (`id`, `request_number`, `user_id`, `status`, `description`, `total_amount`, `requested_date`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'ZAY-2025-000001', 1, 'submitted', NULL, 2655.00, '2025-09-24', NULL, '2025-09-22 08:35:48', '2025-09-22 08:57:26'),
(2, 'ZAY-2025-000002', 3, 'draft', NULL, 4779.50, '2025-10-01', NULL, '2025-09-22 09:32:36', '2025-09-22 09:32:36');

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
  `specifications` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_request_items`
--

INSERT INTO `purchase_request_items` (`id`, `purchase_request_id`, `warehouse_item_id`, `item_name`, `item_code`, `quantity`, `unit`, `estimated_price`, `specifications`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 'Папір офісний А4', 'PAPER-A4-80', 10, 'пачка', 125.50, NULL, '2025-09-22 08:35:48', '2025-09-22 08:35:48'),
(2, 1, NULL, 'Миша комп\'ютерна', 'MOUSE-OPTICAL-USB', 5, 'шт', 280.00, NULL, '2025-09-22 08:35:48', '2025-09-22 08:35:48'),
(3, 2, NULL, 'Флешка USB 32GB', 'USB-32GB-KINGSTON', 3, 'шт', 450.00, NULL, '2025-09-22 09:32:36', '2025-09-22 09:32:36'),
(4, 2, NULL, 'Ручки кулькові сині', 'PEN-BLUE-1MM', 1, 'шт', 12.00, NULL, '2025-09-22 09:32:36', '2025-09-22 09:32:36'),
(5, 2, NULL, 'Папір офісний А4', 'PAPER-A4-80', 25, 'пачка', 125.50, NULL, '2025-09-22 09:32:36', '2025-09-22 09:32:36'),
(6, 2, NULL, 'Миша комп\'ютерна', 'MOUSE-OPTICAL-USB', 1, 'шт', 280.00, NULL, '2025-09-22 09:32:36', '2025-09-22 09:32:36');

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
-- Table structure for table `repair_requests`
--

CREATE TABLE `repair_requests` (
  `id` int(11) NOT NULL,
  `user_telegram_id` bigint(20) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `branch_id` int(11) NOT NULL,
  `room_number` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `status` enum('нова','в_роботі','виконана') DEFAULT 'нова',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `repair_requests`
--

INSERT INTO `repair_requests` (`id`, `user_telegram_id`, `username`, `branch_id`, `room_number`, `description`, `phone`, `status`, `created_at`, `updated_at`) VALUES
(10, 542503468, 'ksenia_kalyan', 3, '205', 'Ошибка принтера, код ошибки 0xEA', '0963754537', 'в_роботі', '2025-08-22 08:26:47', '2025-08-28 07:15:59'),
(11, 391828916, 'Atlantis_Star', 1, '26', 'Принтер не делает распечатку с компьютера и не сканирует документы(', '0979915532', 'виконана', '2025-08-26 06:06:38', '2025-08-28 07:15:50'),
(13, 923722881, 'Elena65005', 2, '6', 'новый картридж, лимит чипа', '+380974566093', 'нова', '2025-08-28 11:20:34', '2025-08-29 08:40:39');

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
  `id` int(11) NOT NULL,
  `admin_telegram_id` bigint(20) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `room_number` varchar(50) NOT NULL,
  `template_id` int(11) DEFAULT NULL,
  `equipment_type` varchar(100) NOT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `serial_number` varchar(255) DEFAULT NULL,
  `inventory_number` varchar(255) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `room_inventory`
--

INSERT INTO `room_inventory` (`id`, `admin_telegram_id`, `branch_id`, `room_number`, `template_id`, `equipment_type`, `brand`, `model`, `serial_number`, `inventory_number`, `notes`, `created_at`) VALUES
(2, 330489980, 1, '27', NULL, 'Мышь', 'Logitech', '123', '335210299', '2113212', NULL, '2025-08-05 21:04:48'),
(4, 330489980, 6, '1', NULL, 'Компьютер', 'HP', 'HP11', '123123', '321321', NULL, '2025-08-15 12:28:02');

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
('veggX4I8GppcZNV0qoC9oMPsm0q1ZtDSrALi23gA', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoidXV3U0xIM3d5b1ExNlpJNmpQMmRPYnluWnlEM2NXVlRTRlp5ek1vSyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9wcm9maWxlIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mzt9', 1758550322),
('WSSORzm6RbjYnhhgMyi2oqGoUbxSIpzVeBrBLy6H', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNDBHd2VqMVlCQlJFOHVjZ0FtUVR6RWN6MWRMTW95cWRMZmxnWWkzVyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9pbnZlbnRvcnkiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1758542762);

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
(1, 'Serhii', 'admin@localhost', '2025-08-01 22:58:20', '$2y$12$6y2sbFZ5oWmlo34k83dLGuXDaGXcvCoOc1AIRfhPxX/IJnBIZ.hnS', 330489980, 1, 'UcNeIPurbHh5kD65w6JLeMCxR64AGHzYM0yKA2Rr9q8OHr3YpvFQptEu96fE', '2025-08-01 22:58:20', '2025-09-22 07:56:35', 'admin'),
(2, 'Директор поликлиники', 'director@localhost', '2025-08-01 22:58:20', '$2y$12$7djSlQnjuc5QHsjWSbh6le6AX1cj/clh6Pqro9NdK.qmfj4JLn8Ea', NULL, 1, 'AdmUVpnchjgIGAdgG09Jwiw8dLd7FJVuqUKxVP9MIAWz2uBasIaciRTs69dR', '2025-08-01 22:58:20', '2025-08-01 22:58:20', 'admin'),
(3, 'Нач. Складу', 'warehouse@localhost', '2025-09-22 08:17:54', '$2y$12$OtGMBKrXfNF70iq8qt9FV.Ehh6nPXkqHrNwbrSGQWKLPzcQMAnsqy', NULL, 1, NULL, '2025-09-22 08:17:54', '2025-09-22 11:12:01', 'warehouse_keeper');

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

--
-- Dumping data for table `warehouse_inventories`
--

INSERT INTO `warehouse_inventories` (`id`, `inventory_number`, `user_id`, `inventory_date`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'INV-2025-000001', 3, '2025-09-22', 'completed', NULL, '2025-09-22 11:09:50', '2025-09-22 11:09:50');

-- --------------------------------------------------------

--
-- Table structure for table `warehouse_inventory_items`
--

CREATE TABLE `warehouse_inventory_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inventory_id` bigint(20) UNSIGNED NOT NULL,
  `warehouse_item_id` bigint(20) UNSIGNED NOT NULL,
  `system_quantity` int(11) NOT NULL,
  `actual_quantity` int(11) NOT NULL,
  `difference` int(11) NOT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `warehouse_inventory_items`
--

INSERT INTO `warehouse_inventory_items` (`id`, `inventory_id`, `warehouse_item_id`, `system_quantity`, `actual_quantity`, `difference`, `note`, `created_at`, `updated_at`) VALUES
(1, 1, 5, 12, 5, -7, NULL, '2025-09-22 11:09:50', '2025-09-22 11:09:50');

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

--
-- Dumping data for table `warehouse_items`
--

INSERT INTO `warehouse_items` (`id`, `name`, `code`, `description`, `unit`, `quantity`, `min_quantity`, `price`, `category`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Папір офісний А4', 'PAPER-A4-80', 'Папір офісний білий А4, щільність 80 г/м²', 'пачка', 25, 10, 125.50, 'Канцелярські товари', 1, '2025-09-22 08:17:54', '2025-09-22 09:22:16'),
(2, 'Ручки кулькові сині', 'PEN-BLUE-1MM', 'Ручка кулькова синя, товщина стержня 1мм', 'шт', 150, 50, 12.00, 'Канцелярські товари', 1, '2025-09-22 08:17:54', '2025-09-22 08:17:54'),
(3, 'Картридж HP LaserJet', 'CART-HP-85A', 'Оригінальний картридж HP CE285A для LaserJet', 'шт', 5, 3, 2850.00, 'Картриджі', 1, '2025-09-22 08:17:54', '2025-09-22 08:17:54'),
(4, 'Флешка USB 32GB', 'USB-32GB-KINGSTON', 'USB флеш-накопичувач Kingston 32GB USB 3.0', 'шт', 12, 5, 450.00, 'Носії інформації', 1, '2025-09-22 08:17:54', '2025-09-22 08:17:54'),
(5, 'Батарейки AA', 'BATTERY-AA-DURACELL', 'Батарейки пальчикові AA Duracell', 'упак', 5, 5, 89.50, 'Елементи живлення', 1, '2025-09-22 08:17:54', '2025-09-22 11:11:25'),
(6, 'Скотч канцелярський', 'TAPE-CLEAR-19MM', 'Скотч прозорий канцелярський 19мм х 33м', 'шт', 15, 10, 25.00, 'Канцелярські товари', 1, '2025-09-22 08:17:54', '2025-09-22 09:24:01'),
(7, 'Диск CD-R', 'CD-R-VERBATIM-700MB', 'CD-R диск Verbatim 700MB 80min 52x', 'шт', 35, 20, 18.00, 'Носії інформації', 1, '2025-09-22 08:17:54', '2025-09-22 08:17:54'),
(8, 'Миша комп\'ютерна', 'MOUSE-OPTICAL-USB', 'Миша комп\'ютерна оптична USB', 'шт', 7, 5, 280.00, 'Комп\'ютерна техніка', 1, '2025-09-22 08:17:54', '2025-09-22 08:17:54');

-- --------------------------------------------------------

--
-- Table structure for table `warehouse_movements`
--

CREATE TABLE `warehouse_movements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `warehouse_item_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('receipt','issue','writeoff','inventory') NOT NULL,
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

INSERT INTO `warehouse_movements` (`id`, `user_id`, `warehouse_item_id`, `type`, `quantity`, `balance_after`, `note`, `document_number`, `issued_to_user_id`, `operation_date`, `created_at`, `updated_at`) VALUES
(1, 1, 5, 'issue', -1, 7, 'Со склада (Видано: Фелонюк)', NULL, NULL, '2025-09-22', '2025-09-22 08:29:33', '2025-09-22 08:29:33'),
(2, 1, 5, 'receipt', 5, 12, NULL, '123221', NULL, '2025-09-22', '2025-09-22 08:30:08', '2025-09-22 08:30:08'),
(3, 3, 1, 'issue', -5, 20, ' (Видано: Фелонюк)', NULL, NULL, '2025-09-22', '2025-09-22 09:20:21', '2025-09-22 09:20:21'),
(4, 3, 1, 'receipt', 5, 25, NULL, 'ЕП152', NULL, '2025-09-22', '2025-09-22 09:22:16', '2025-09-22 09:22:16'),
(5, 3, 6, 'receipt', 15, 15, 'ЕП155 поставщик филя', 'ЕП155', NULL, '2025-09-22', '2025-09-22 09:24:01', '2025-09-22 09:24:01'),
(6, 3, 5, 'inventory', -7, 5, 'Швидка інвентаризація #INV-2025-000001', NULL, NULL, '2025-09-22', '2025-09-22 11:09:50', '2025-09-22 11:09:50');

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
-- Indexes for table `api_tokens`
--
ALTER TABLE `api_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventory_transfers_user_id_foreign` (`user_id`);

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
-- Indexes for table `repair_requests`
--
ALTER TABLE `repair_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `branch_id` (`branch_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_user` (`user_telegram_id`),
  ADD KEY `idx_created` (`created_at`);

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
  ADD KEY `idx_serial_number` (`serial_number`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `warehouse_inventory_items_inventory_id_foreign` (`inventory_id`),
  ADD KEY `warehouse_inventory_items_warehouse_item_id_foreign` (`warehouse_item_id`);

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
  ADD KEY `warehouse_movements_warehouse_item_id_foreign` (`warehouse_item_id`),
  ADD KEY `warehouse_movements_issued_to_user_id_foreign` (`issued_to_user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `api_tokens`
--
ALTER TABLE `api_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `cartridge_replacements`
--
ALTER TABLE `cartridge_replacements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_requests`
--
ALTER TABLE `purchase_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `purchase_request_items`
--
ALTER TABLE `purchase_request_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `repair_masters`
--
ALTER TABLE `repair_masters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `repair_requests`
--
ALTER TABLE `repair_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `repair_trackings`
--
ALTER TABLE `repair_trackings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `room_inventory`
--
ALTER TABLE `room_inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `warehouse_inventories`
--
ALTER TABLE `warehouse_inventories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `warehouse_inventory_items`
--
ALTER TABLE `warehouse_inventory_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `warehouse_items`
--
ALTER TABLE `warehouse_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `warehouse_movements`
--
ALTER TABLE `warehouse_movements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cartridge_replacements`
--
ALTER TABLE `cartridge_replacements`
  ADD CONSTRAINT `cartridge_replacements_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  ADD CONSTRAINT `cartridge_replacements_ibfk_2` FOREIGN KEY (`printer_inventory_id`) REFERENCES `room_inventory` (`id`);

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
-- Constraints for table `inventory_transfers`
--
ALTER TABLE `inventory_transfers`
  ADD CONSTRAINT `inventory_transfers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `purchase_request_items_purchase_request_id_foreign` FOREIGN KEY (`purchase_request_id`) REFERENCES `purchase_requests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_request_items_warehouse_item_id_foreign` FOREIGN KEY (`warehouse_item_id`) REFERENCES `warehouse_items` (`id`);

--
-- Constraints for table `repair_requests`
--
ALTER TABLE `repair_requests`
  ADD CONSTRAINT `repair_requests_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`);

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
-- Constraints for table `warehouse_inventory_items`
--
ALTER TABLE `warehouse_inventory_items`
  ADD CONSTRAINT `warehouse_inventory_items_inventory_id_foreign` FOREIGN KEY (`inventory_id`) REFERENCES `warehouse_inventories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `warehouse_inventory_items_warehouse_item_id_foreign` FOREIGN KEY (`warehouse_item_id`) REFERENCES `warehouse_items` (`id`);

--
-- Constraints for table `warehouse_movements`
--
ALTER TABLE `warehouse_movements`
  ADD CONSTRAINT `warehouse_movements_issued_to_user_id_foreign` FOREIGN KEY (`issued_to_user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `warehouse_movements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `warehouse_movements_warehouse_item_id_foreign` FOREIGN KEY (`warehouse_item_id`) REFERENCES `warehouse_items` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
