-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 22, 2026 at 09:30 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ims_backend`
--
CREATE DATABASE IF NOT EXISTS `ims_backend` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `ims_backend`;

-- --------------------------------------------------------

--
-- Table structure for table `batch__stocks`
--

CREATE TABLE `batch__stocks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `supplier_invoice_id` bigint(20) UNSIGNED NOT NULL,
  `no_cases` int(11) NOT NULL,
  `pack_size` int(11) NOT NULL,
  `extra_units` int(11) NOT NULL DEFAULT 0,
  `remain_qty` int(11) NOT NULL,
  `returned_qty` int(11) NOT NULL DEFAULT 0,
  `free_qty` int(11) NOT NULL DEFAULT 0,
  `retail_price` decimal(10,2) NOT NULL,
  `netprice` decimal(10,2) NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `batch__stocks`
--

INSERT INTO `batch__stocks` (`id`, `product_id`, `supplier_invoice_id`, `no_cases`, `pack_size`, `extra_units`, `remain_qty`, `returned_qty`, `free_qty`, `retail_price`, `netprice`, `expiry_date`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 0, 0, 0, 2, 2, 100, 120.00, 120.00, NULL, '2026-02-18 13:56:47', '2026-02-18 14:05:08'),
(2, 1, 2, 2, 10, 0, 25, 0, 5, 140.00, 140.00, NULL, '2026-02-18 14:14:55', '2026-02-18 14:14:55');

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
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `nic` varchar(255) NOT NULL,
  `phoneno` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `name`, `nic`, `phoneno`, `created_at`, `updated_at`) VALUES
(1, 'sdsdsd', 'sdsd', 'dsds', '2026-02-18 13:56:06', '2026-02-18 13:56:06');

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
-- Table structure for table `loadings`
--

CREATE TABLE `loadings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `load_number` varchar(255) NOT NULL,
  `truck_id` bigint(20) UNSIGNED NOT NULL,
  `route_id` bigint(20) UNSIGNED NOT NULL,
  `prepared_date` date DEFAULT NULL,
  `loading_date` date DEFAULT NULL,
  `status` enum('pending','delivered','not_delivered') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `driver_id` bigint(20) UNSIGNED DEFAULT NULL,
  `helper_id` bigint(20) UNSIGNED DEFAULT NULL,
  `cash_collector_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sales_rep_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `loadings`
--

INSERT INTO `loadings` (`id`, `load_number`, `truck_id`, `route_id`, `prepared_date`, `loading_date`, `status`, `created_at`, `updated_at`, `driver_id`, `helper_id`, `cash_collector_id`, `sales_rep_id`) VALUES
(1, 'l1', 1, 1, '2026-02-18', '2026-02-18', 'delivered', '2026-02-18 13:57:22', '2026-02-18 13:57:51', 1, 1, 1, 1),
(2, 'loading', 1, 1, '2026-02-18', '2026-02-18', 'delivered', '2026-02-18 14:04:42', '2026-02-18 14:04:46', 1, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `loading_returns`
--

CREATE TABLE `loading_returns` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `loading_id` bigint(20) UNSIGNED NOT NULL,
  `batch_id` bigint(20) UNSIGNED NOT NULL,
  `qty` int(11) NOT NULL,
  `return_date` date NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `loading_returns`
--

INSERT INTO `loading_returns` (`id`, `loading_id`, `batch_id`, `qty`, `return_date`, `reason`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 10, '2026-02-18', 'Returned from loading', '2026-02-18 13:57:58', '2026-02-18 13:57:58'),
(2, 2, 1, 2, '2026-02-18', 'Return processed via Returns Page', '2026-02-18 14:05:08', '2026-02-18 14:05:08');

-- --------------------------------------------------------

--
-- Table structure for table `load_list_items`
--

CREATE TABLE `load_list_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `loading_id` bigint(20) UNSIGNED NOT NULL,
  `batch_id` bigint(20) UNSIGNED NOT NULL,
  `qty` int(11) NOT NULL,
  `free_qty` int(11) DEFAULT NULL,
  `wh_price` double DEFAULT NULL,
  `net_price` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `load_list_items`
--

INSERT INTO `load_list_items` (`id`, `created_at`, `updated_at`, `loading_id`, `batch_id`, `qty`, `free_qty`, `wh_price`, `net_price`) VALUES
(1, '2026-02-18 13:57:22', '2026-02-18 13:57:22', 1, 1, 100, 0, 0, 120),
(2, '2026-02-18 14:04:42', '2026-02-18 14:04:42', 2, 1, 10, 0, 0, 120);

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
(4, '2026_01_30_161809_create_suppliers_table', 1),
(5, '2026_01_30_162015_create_personal_access_tokens_table', 1),
(6, '2026_01_30_163538_create_products_table', 1),
(7, '2026_01_30_164500_create_supplier_invoices_table', 1),
(8, '2026_01_30_165732_create_batch__stocks_table', 1),
(9, '2026_01_30_171409_create_routes_table', 1),
(10, '2026_01_30_172235_create_trucks_table', 1),
(11, '2026_01_30_172957_create_employees_table', 1),
(12, '2026_01_30_174514_create_shops_table', 1),
(13, '2026_02_04_150335_create_sales_reps_table', 1),
(14, '2026_02_04_155716_create_loadings_table', 1),
(15, '2026_02_04_155757_create_load_list_items_table', 1),
(16, '2026_02_15_113239_add_extra_units_to_batch_stocks_table', 1),
(17, '2026_02_16_180000_add_barcode_to_products_table', 1),
(18, '2026_02_16_181000_remove_discount_from_supplier_invoices_table', 1),
(19, '2026_02_16_182000_add_free_qty_to_batch_stocks_table', 1),
(20, '2026_02_17_130517_add_employees_to_loadings_table', 1),
(21, '2026_02_17_183147_add_sales_rep_id_to_loadings_table', 1),
(22, '2026_02_17_185720_remove_role_from_employees_table', 1),
(23, '2026_02_17_200927_add_initial_free_qty_to_batch_stocks_table', 1),
(24, '2026_02_18_101935_rename_qty_to_remain_qty_in_batch_stocks_table', 1),
(25, '2026_02_18_181743_add_returned_qty_to_batch_stocks_table', 1),
(26, '2026_02_18_181745_create_loading_returns_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 1, 'auth_token', 'd557c6de15b443a302b7c8d13d185e695d2157dbc2769f48f0a47539492a464c', '[\"*\"]', '2026-02-18 14:17:38', NULL, '2026-02-18 13:55:21', '2026-02-18 14:17:38'),
(2, 'App\\Models\\User', 1, 'auth_token', 'd54a47ece7e5106a77791766645a01075194d9bf145d9c5c5df1518993fd0b26', '[\"*\"]', '2026-02-18 14:23:09', NULL, '2026-02-18 14:18:40', '2026-02-18 14:23:09');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `material_code` varchar(255) NOT NULL,
  `barcode` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `material_code`, `barcode`, `name`, `supplier_id`, `created_at`, `updated_at`) VALUES
(1, '4598', '1234', 'pen', 1, '2026-02-18 13:55:44', '2026-02-18 13:55:44');

-- --------------------------------------------------------

--
-- Table structure for table `routes`
--

CREATE TABLE `routes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `route_code` varchar(255) NOT NULL,
  `route_description` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `routes`
--

INSERT INTO `routes` (`id`, `route_code`, `route_description`, `created_at`, `updated_at`) VALUES
(1, 'err', 'css', '2026-02-18 13:55:56', '2026-02-18 13:55:56');

-- --------------------------------------------------------

--
-- Table structure for table `sales_reps`
--

CREATE TABLE `sales_reps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rep_id` varchar(255) NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact` varchar(255) DEFAULT NULL,
  `join_date` date DEFAULT NULL,
  `route_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales_reps`
--

INSERT INTO `sales_reps` (`id`, `rep_id`, `supplier_id`, `name`, `contact`, `join_date`, `route_id`, `created_at`, `updated_at`) VALUES
(1, 'rep', 1, 'dsd', 'dsd', '2026-02-18', 1, '2026-02-18 13:56:14', '2026-02-18 13:56:14');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shops`
--

CREATE TABLE `shops` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `shop_code` varchar(255) NOT NULL,
  `shop_name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phoneno` varchar(255) DEFAULT NULL,
  `route_code` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `contactno` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `contactno`, `address`, `created_at`, `updated_at`) VALUES
(1, 'test', '22', 'ww', '2026-02-18 13:55:33', '2026-02-18 13:55:33');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_invoices`
--

CREATE TABLE `supplier_invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `invoice_number` varchar(255) NOT NULL,
  `invoice_date` date NOT NULL,
  `total_bill_amount` decimal(17,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `supplier_invoices`
--

INSERT INTO `supplier_invoices` (`id`, `supplier_id`, `invoice_number`, `invoice_date`, `total_bill_amount`, `created_at`, `updated_at`) VALUES
(1, 1, '123', '2026-02-18', 1200.00, '2026-02-18 13:56:47', '2026-02-18 13:56:47'),
(2, 1, 'dsdsd', '2026-02-18', 1234.00, '2026-02-18 14:14:55', '2026-02-18 14:14:55');

-- --------------------------------------------------------

--
-- Table structure for table `trucks`
--

CREATE TABLE `trucks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `licence_plate_no` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `trucks`
--

INSERT INTO `trucks` (`id`, `licence_plate_no`, `description`, `created_at`, `updated_at`) VALUES
(1, 'sdd2333', 'dsd', '2026-02-18 13:56:02', '2026-02-18 13:56:02');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `role` enum('admin','user','staff','rep') NOT NULL DEFAULT 'user',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `email_verified_at`, `password`, `phone`, `address`, `profile_picture`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin', 'admin@ims.com', NULL, '$2y$12$l2QZ1LOxxKTrveN5v65p/.GY4YTRmTGbY4Ax6jLyEAblQWByoQi/q', '0771234567', NULL, NULL, 'user', NULL, '2026-02-18 13:54:21', '2026-02-18 13:54:21');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `batch__stocks`
--
ALTER TABLE `batch__stocks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `batch__stocks_product_id_foreign` (`product_id`),
  ADD KEY `batch__stocks_supplier_invoice_id_foreign` (`supplier_invoice_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employees_nic_unique` (`nic`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

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
-- Indexes for table `loadings`
--
ALTER TABLE `loadings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `loadings_truck_id_foreign` (`truck_id`),
  ADD KEY `loadings_route_id_foreign` (`route_id`),
  ADD KEY `loadings_driver_id_foreign` (`driver_id`),
  ADD KEY `loadings_helper_id_foreign` (`helper_id`),
  ADD KEY `loadings_cash_collector_id_foreign` (`cash_collector_id`),
  ADD KEY `loadings_sales_rep_id_foreign` (`sales_rep_id`);

--
-- Indexes for table `loading_returns`
--
ALTER TABLE `loading_returns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `loading_returns_loading_id_foreign` (`loading_id`),
  ADD KEY `loading_returns_batch_id_foreign` (`batch_id`);

--
-- Indexes for table `load_list_items`
--
ALTER TABLE `load_list_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `load_list_items_loading_id_foreign` (`loading_id`),
  ADD KEY `load_list_items_batch_id_foreign` (`batch_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_material_code_unique` (`material_code`),
  ADD UNIQUE KEY `products_barcode_unique` (`barcode`),
  ADD KEY `products_supplier_id_foreign` (`supplier_id`);

--
-- Indexes for table `routes`
--
ALTER TABLE `routes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `routes_route_code_unique` (`route_code`);

--
-- Indexes for table `sales_reps`
--
ALTER TABLE `sales_reps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sales_reps_supplier_id_foreign` (`supplier_id`),
  ADD KEY `sales_reps_route_id_foreign` (`route_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `shops`
--
ALTER TABLE `shops`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `shops_shop_code_unique` (`shop_code`),
  ADD KEY `shops_route_code_foreign` (`route_code`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `supplier_invoices`
--
ALTER TABLE `supplier_invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `supplier_invoices_invoice_number_unique` (`invoice_number`),
  ADD KEY `supplier_invoices_supplier_id_foreign` (`supplier_id`);

--
-- Indexes for table `trucks`
--
ALTER TABLE `trucks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `trucks_licence_plate_no_unique` (`licence_plate_no`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_phone_unique` (`phone`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `batch__stocks`
--
ALTER TABLE `batch__stocks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loadings`
--
ALTER TABLE `loadings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `loading_returns`
--
ALTER TABLE `loading_returns`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `load_list_items`
--
ALTER TABLE `load_list_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `routes`
--
ALTER TABLE `routes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sales_reps`
--
ALTER TABLE `sales_reps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `shops`
--
ALTER TABLE `shops`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `supplier_invoices`
--
ALTER TABLE `supplier_invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `trucks`
--
ALTER TABLE `trucks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `batch__stocks`
--
ALTER TABLE `batch__stocks`
  ADD CONSTRAINT `batch__stocks_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `batch__stocks_supplier_invoice_id_foreign` FOREIGN KEY (`supplier_invoice_id`) REFERENCES `supplier_invoices` (`id`);

--
-- Constraints for table `loadings`
--
ALTER TABLE `loadings`
  ADD CONSTRAINT `loadings_cash_collector_id_foreign` FOREIGN KEY (`cash_collector_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `loadings_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `loadings_helper_id_foreign` FOREIGN KEY (`helper_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `loadings_route_id_foreign` FOREIGN KEY (`route_id`) REFERENCES `routes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `loadings_sales_rep_id_foreign` FOREIGN KEY (`sales_rep_id`) REFERENCES `sales_reps` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `loadings_truck_id_foreign` FOREIGN KEY (`truck_id`) REFERENCES `trucks` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `loading_returns`
--
ALTER TABLE `loading_returns`
  ADD CONSTRAINT `loading_returns_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `batch__stocks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `loading_returns_loading_id_foreign` FOREIGN KEY (`loading_id`) REFERENCES `loadings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `load_list_items`
--
ALTER TABLE `load_list_items`
  ADD CONSTRAINT `load_list_items_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `batch__stocks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `load_list_items_loading_id_foreign` FOREIGN KEY (`loading_id`) REFERENCES `loadings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sales_reps`
--
ALTER TABLE `sales_reps`
  ADD CONSTRAINT `sales_reps_route_id_foreign` FOREIGN KEY (`route_id`) REFERENCES `routes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sales_reps_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shops`
--
ALTER TABLE `shops`
  ADD CONSTRAINT `shops_route_code_foreign` FOREIGN KEY (`route_code`) REFERENCES `routes` (`route_code`) ON DELETE CASCADE;

--
-- Constraints for table `supplier_invoices`
--
ALTER TABLE `supplier_invoices`
  ADD CONSTRAINT `supplier_invoices_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;
--
-- Database: `lms`
--
CREATE DATABASE IF NOT EXISTS `lms` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `lms`;

-- --------------------------------------------------------

--
-- Table structure for table `al_exam_submissions`
--

CREATE TABLE `al_exam_submissions` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL COMMENT 'FK to users.user_id',
  `subject_1` varchar(100) NOT NULL,
  `result_1` varchar(5) DEFAULT NULL,
  `subject_2` varchar(100) NOT NULL,
  `result_2` varchar(5) DEFAULT NULL,
  `subject_3` varchar(100) NOT NULL,
  `result_3` varchar(5) DEFAULT NULL,
  `index_number` varchar(50) DEFAULT NULL,
  `district` varchar(50) NOT NULL,
  `stream` varchar(50) DEFAULT NULL COMMENT 'Arts, Commerce, Science, Tech',
  `photo_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `agreed_to_publish` tinyint(1) DEFAULT 0,
  `results_submitted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores A/L exam details submitted by students';

--
-- Dumping data for table `al_exam_submissions`
--

INSERT INTO `al_exam_submissions` (`id`, `student_id`, `subject_1`, `result_1`, `subject_2`, `result_2`, `subject_3`, `result_3`, `index_number`, `district`, `stream`, `photo_path`, `created_at`, `updated_at`, `agreed_to_publish`, `results_submitted_at`) VALUES
(1, 'stu_1001', 'Economics', NULL, 'Business Studies', NULL, 'Accounting', NULL, '', 'Gampaha', NULL, NULL, '2026-02-15 07:39:34', '2026-02-15 07:39:34', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `physical_class_id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `attended_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL COMMENT 'Primary Key',
  `recording_id` int(11) NOT NULL COMMENT 'Foreign Key: Links to recordings.id - Chat context (video being watched)',
  `sender_id` varchar(20) NOT NULL COMMENT 'Foreign Key: Links to users.user_id - Message sender (student or teacher)',
  `sender_role` enum('student','teacher') NOT NULL COMMENT 'Role of the sender (for quick access)',
  `message` text NOT NULL COMMENT 'Message content',
  `video_timestamp` int(11) DEFAULT NULL COMMENT 'Seconds into the video when message was sent',
  `status` enum('sent','delivered','read') NOT NULL DEFAULT 'sent' COMMENT 'Message status',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Message creation timestamp',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Message update timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Chat messages between students and teachers for recordings';

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `teacher_id` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `cover_image` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `teacher_id`, `title`, `description`, `price`, `cover_image`, `status`, `created_at`) VALUES
(3, 'tea_1001', 'Japan Language', 'Beginner to Pro Japan Language Tutorials', 45000.00, 'uploads/courses/course_696a01491081b.jpg', 1, '2026-01-16 14:43:45');

-- --------------------------------------------------------

--
-- Table structure for table `course_chats`
--

CREATE TABLE `course_chats` (
  `id` int(11) NOT NULL,
  `course_recording_id` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_chats`
--

INSERT INTO `course_chats` (`id`, `course_recording_id`, `user_id`, `message`, `created_at`) VALUES
(4, 4, 'stu_1002', 'hello madam', '2026-01-16 14:47:01');

-- --------------------------------------------------------

--
-- Table structure for table `course_enrollments`
--

CREATE TABLE `course_enrollments` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `enrolled_at` datetime DEFAULT current_timestamp(),
  `status` enum('active','inactive') DEFAULT 'active',
  `payment_status` enum('paid','pending','free') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_enrollments`
--

INSERT INTO `course_enrollments` (`id`, `course_id`, `student_id`, `enrolled_at`, `status`, `payment_status`) VALUES
(6, 3, 'stu_1002', '2026-01-16 14:44:52', 'active', 'paid'),
(7, 3, 'stu_1001', '2026-01-31 21:09:12', 'active', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `course_payments`
--

CREATE TABLE `course_payments` (
  `id` int(11) NOT NULL,
  `course_enrollment_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_status` enum('pending','paid','failed','refunded') DEFAULT 'pending',
  `receipt_path` varchar(255) DEFAULT NULL,
  `receipt_type` varchar(20) DEFAULT NULL,
  `card_number` varchar(20) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `verified_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_payments`
--

INSERT INTO `course_payments` (`id`, `course_enrollment_id`, `amount`, `payment_method`, `payment_status`, `receipt_path`, `receipt_type`, `card_number`, `notes`, `created_at`, `verified_at`) VALUES
(4, 6, 45000.00, 'bank_transfer', 'paid', 'uploads/payments/course_pay_stu_1002_1768554902.pdf', 'pdf', NULL, NULL, '2026-01-16 09:15:02', '2026-01-16 09:16:41');

-- --------------------------------------------------------

--
-- Table structure for table `course_recordings`
--

CREATE TABLE `course_recordings` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `video_path` varchar(255) NOT NULL,
  `thumbnail_url` varchar(500) DEFAULT NULL,
  `is_free` tinyint(1) DEFAULT 0,
  `views` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_recordings`
--

INSERT INTO `course_recordings` (`id`, `course_id`, `title`, `description`, `video_path`, `thumbnail_url`, `is_free`, `views`, `created_at`) VALUES
(4, 3, 'Video 1', 'Introduction to Japan Language', 'https://youtu.be/G_oC7anVuA8?si=GgYhUbRvQ-426hFS', 'https://img.youtube.com/vi/G_oC7anVuA8/mqdefault.jpg', 0, 4, '2026-01-16 14:44:36'),
(5, 3, 'Video 2', 'Japan Language 2', 'https://youtu.be/D7IMsESKTIA?si=cOYhnwEsWdwpZUzO', 'https://img.youtube.com/vi/D7IMsESKTIA/mqdefault.jpg', 0, 0, '2026-01-16 14:47:47');

-- --------------------------------------------------------

--
-- Table structure for table `course_uploads`
--

CREATE TABLE `course_uploads` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `enrollment_fees`
--

CREATE TABLE `enrollment_fees` (
  `id` int(11) NOT NULL COMMENT 'Primary Key',
  `teacher_assignment_id` int(11) NOT NULL COMMENT 'Foreign Key: Links to teacher_assignments.id',
  `enrollment_fee` decimal(10,2) DEFAULT 0.00 COMMENT 'One-time enrollment fee',
  `monthly_fee` decimal(10,2) DEFAULT 0.00 COMMENT 'Monthly subscription fee',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Record creation timestamp',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Record update timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores enrollment and monthly fee settings per teacher assignment';

--
-- Dumping data for table `enrollment_fees`
--

INSERT INTO `enrollment_fees` (`id`, `teacher_assignment_id`, `enrollment_fee`, `monthly_fee`, `created_at`, `updated_at`) VALUES
(9, 16, 1500.00, 2500.00, '2026-01-18 16:43:16', '2026-01-18 16:43:16');

-- --------------------------------------------------------

--
-- Table structure for table `enrollment_payments`
--

CREATE TABLE `enrollment_payments` (
  `id` int(11) NOT NULL COMMENT 'Primary Key',
  `student_enrollment_id` int(11) NOT NULL COMMENT 'Foreign Key: Links to student_enrollment.id',
  `amount` decimal(10,2) NOT NULL COMMENT 'Payment amount',
  `payment_method` enum('card','bank_transfer','cash','mobile_payment') NOT NULL COMMENT 'Payment method',
  `payment_status` enum('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending' COMMENT 'Payment status',
  `payment_date` date DEFAULT NULL COMMENT 'Date when payment was made',
  `card_number` varchar(50) DEFAULT NULL COMMENT 'Last 4 digits of card (for card payments)',
  `receipt_path` varchar(255) DEFAULT NULL COMMENT 'Path to uploaded receipt (for bank transfers)',
  `receipt_type` enum('image','pdf') DEFAULT NULL COMMENT 'Type of receipt file',
  `verified_by` varchar(20) DEFAULT NULL COMMENT 'Admin user_id who verified the payment',
  `verified_at` timestamp NULL DEFAULT NULL COMMENT 'When payment was verified',
  `notes` text DEFAULT NULL COMMENT 'Additional notes',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Record creation timestamp',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Record update timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores enrollment payment transactions';

--
-- Dumping data for table `enrollment_payments`
--

INSERT INTO `enrollment_payments` (`id`, `student_enrollment_id`, `amount`, `payment_method`, `payment_status`, `payment_date`, `card_number`, `receipt_path`, `receipt_type`, `verified_by`, `verified_at`, `notes`, `created_at`, `updated_at`) VALUES
(10, 21, 1500.00, 'bank_transfer', 'paid', NULL, NULL, 'uploads/payments/payment_stu_1001_1770974303.pdf', 'pdf', NULL, NULL, NULL, '2026-02-13 09:18:23', '2026-02-13 09:19:43');

--
-- Triggers `enrollment_payments`
--
DELIMITER $$
CREATE TRIGGER `after_enrollment_payment_update` AFTER UPDATE ON `enrollment_payments` FOR EACH ROW BEGIN
    DECLARE v_teacher_id VARCHAR(20);
    DECLARE v_student_id VARCHAR(20);
    DECLARE v_teacher_points DECIMAL(10,2);
    DECLARE v_institute_points DECIMAL(10,2);
    
    
    IF NEW.payment_status = 'paid' AND OLD.payment_status != 'paid' THEN
        
        
        SELECT ta.teacher_id, se.student_id 
        INTO v_teacher_id, v_student_id
        FROM student_enrollment se
        JOIN teacher_assignments ta ON se.stream_subject_id = ta.stream_subject_id 
            AND se.academic_year = ta.academic_year
        WHERE se.id = NEW.student_enrollment_id
        LIMIT 1;
        
        IF v_teacher_id IS NOT NULL THEN
            
            SET v_teacher_points = NEW.amount * 0.75;
            SET v_institute_points = NEW.amount * 0.25;
            
            
            INSERT IGNORE INTO teacher_wallet (teacher_id, total_points, total_earned)
            VALUES (v_teacher_id, 0.00, 0.00);
            
            
            UPDATE teacher_wallet
            SET total_points = total_points + v_teacher_points,
                total_earned = total_earned + v_teacher_points
            WHERE teacher_id = v_teacher_id;
            
            
            UPDATE institute_wallet
            SET total_points = total_points + v_institute_points,
                total_earned = total_earned + v_institute_points
            WHERE id = 1;
            
            
            INSERT INTO payment_transactions (
                payment_type, payment_id, teacher_id, student_id,
                total_amount, teacher_points, institute_points, transaction_status
            ) VALUES (
                'enrollment', NEW.id, v_teacher_id, v_student_id,
                NEW.amount, v_teacher_points, v_institute_points, 'completed'
            );
        END IF;
        
    
    ELSEIF NEW.payment_status = 'refunded' AND OLD.payment_status = 'paid' THEN
        
        
        SELECT teacher_id, student_id, teacher_points, institute_points
        INTO v_teacher_id, v_student_id, v_teacher_points, v_institute_points
        FROM payment_transactions
        WHERE payment_type = 'enrollment' AND payment_id = NEW.id AND transaction_status = 'completed'
        LIMIT 1;
        
        IF v_teacher_id IS NOT NULL THEN
            
            UPDATE teacher_wallet
            SET total_points = total_points - v_teacher_points
            WHERE teacher_id = v_teacher_id;
            
            
            UPDATE institute_wallet
            SET total_points = total_points - v_institute_points
            WHERE id = 1;
            
            
            UPDATE payment_transactions
            SET transaction_status = 'reversed'
            WHERE payment_type = 'enrollment' AND payment_id = NEW.id;
        END IF;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `id` int(11) NOT NULL COMMENT 'Primary Key',
  `teacher_id` varchar(20) NOT NULL COMMENT 'FK to users.user_id',
  `subject_id` int(11) NOT NULL COMMENT 'FK to subjects.id',
  `title` varchar(255) NOT NULL COMMENT 'Exam title',
  `duration_minutes` int(11) NOT NULL DEFAULT 60 COMMENT 'Duration in minutes',
  `deadline` datetime NOT NULL COMMENT 'Exam deadline',
  `is_published` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=draft, 1=published',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active' COMMENT 'Exam status',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Record creation timestamp',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Record update timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores exam information';

-- --------------------------------------------------------

--
-- Table structure for table `exam_attempts`
--

CREATE TABLE `exam_attempts` (
  `id` int(11) NOT NULL COMMENT 'Primary Key',
  `exam_id` int(11) NOT NULL COMMENT 'FK to exams.id',
  `student_id` varchar(20) NOT NULL COMMENT 'FK to users.user_id',
  `start_time` datetime NOT NULL COMMENT 'When student started the exam',
  `end_time` datetime DEFAULT NULL COMMENT 'When student submitted or time expired',
  `score` decimal(5,2) DEFAULT NULL COMMENT 'Score percentage',
  `correct_count` int(11) DEFAULT 0 COMMENT 'Number of correct answers',
  `total_questions` int(11) DEFAULT 0 COMMENT 'Total questions in exam',
  `status` enum('in_progress','completed','expired') NOT NULL DEFAULT 'in_progress' COMMENT 'Attempt status',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Record creation timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores student exam attempts';

-- --------------------------------------------------------

--
-- Table structure for table `exam_questions`
--

CREATE TABLE `exam_questions` (
  `id` int(11) NOT NULL COMMENT 'Primary Key',
  `exam_id` int(11) NOT NULL COMMENT 'FK to exams.id',
  `question_text` text NOT NULL COMMENT 'Question text content',
  `question_image` varchar(255) DEFAULT NULL COMMENT 'Optional question image path',
  `question_type` enum('single','multiple') NOT NULL DEFAULT 'single' COMMENT 'single=one correct answer, multiple=multiple correct answers',
  `order_index` int(11) NOT NULL DEFAULT 0 COMMENT 'Question display order',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Record creation timestamp',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Record update timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores exam questions';

-- --------------------------------------------------------

--
-- Table structure for table `institute_wallet`
--

CREATE TABLE `institute_wallet` (
  `id` int(11) NOT NULL,
  `total_points` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_earned` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `institute_wallet`
--

INSERT INTO `institute_wallet` (`id`, `total_points`, `total_earned`, `created_at`, `updated_at`) VALUES
(1, 1625.00, 1625.00, '2026-02-06 09:22:50', '2026-02-13 10:01:27'),
(2, 0.00, 0.00, '2026-02-06 09:33:14', '2026-02-06 09:33:14'),
(3, 0.00, 0.00, '2026-02-06 09:33:21', '2026-02-06 09:33:21');

-- --------------------------------------------------------

--
-- Table structure for table `instructor_requests`
--

CREATE TABLE `instructor_requests` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `request_note` text DEFAULT NULL,
  `status` enum('pending','accepted','completed','cancelled') NOT NULL DEFAULT 'pending',
  `accepted_by` varchar(20) DEFAULT NULL,
  `accepted_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `instructor_subjects`
--

CREATE TABLE `instructor_subjects` (
  `id` int(11) NOT NULL,
  `instructor_id` varchar(20) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `live_class_participants`
--

CREATE TABLE `live_class_participants` (
  `id` int(11) NOT NULL,
  `recording_id` int(11) NOT NULL COMMENT 'Foreign Key: Links to recordings.id (live class)',
  `student_id` varchar(20) NOT NULL COMMENT 'Foreign Key: Links to users.user_id (student)',
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'When student joined the live class',
  `left_at` timestamp NULL DEFAULT NULL COMMENT 'When student left the live class (NULL = still online)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `live_class_participants`
--

INSERT INTO `live_class_participants` (`id`, `recording_id`, `student_id`, `joined_at`, `left_at`) VALUES
(5, 24, 'stu_1001', '2026-02-13 09:25:30', '2026-02-13 09:26:04');

-- --------------------------------------------------------

--
-- Table structure for table `monthly_payments`
--

CREATE TABLE `monthly_payments` (
  `id` int(11) NOT NULL COMMENT 'Primary Key',
  `student_enrollment_id` int(11) NOT NULL COMMENT 'Foreign Key: Links to student_enrollment.id',
  `month` int(2) NOT NULL COMMENT 'Month (1-12)',
  `year` int(4) NOT NULL COMMENT 'Year (e.g., 2025)',
  `amount` decimal(10,2) NOT NULL COMMENT 'Payment amount',
  `payment_method` enum('card','bank_transfer','cash','mobile_payment') NOT NULL COMMENT 'Payment method',
  `payment_status` enum('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending' COMMENT 'Payment status',
  `payment_date` date DEFAULT NULL COMMENT 'Date when payment was made',
  `card_number` varchar(50) DEFAULT NULL COMMENT 'Last 4 digits of card (for card payments)',
  `receipt_path` varchar(255) DEFAULT NULL COMMENT 'Path to uploaded receipt (for bank transfers)',
  `receipt_type` enum('image','pdf') DEFAULT NULL COMMENT 'Type of receipt file',
  `verified_by` varchar(20) DEFAULT NULL COMMENT 'Admin user_id who verified the payment',
  `verified_at` timestamp NULL DEFAULT NULL COMMENT 'When payment was verified',
  `notes` text DEFAULT NULL COMMENT 'Additional notes',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Record creation timestamp',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Record update timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores monthly payment transactions';

--
-- Dumping data for table `monthly_payments`
--

INSERT INTO `monthly_payments` (`id`, `student_enrollment_id`, `month`, `year`, `amount`, `payment_method`, `payment_status`, `payment_date`, `card_number`, `receipt_path`, `receipt_type`, `verified_by`, `verified_at`, `notes`, `created_at`, `updated_at`) VALUES
(9, 21, 2, 2026, 2500.00, 'bank_transfer', 'paid', NULL, NULL, 'uploads/payments/payment_stu_1001_1770370260.png', 'image', NULL, NULL, NULL, '2026-02-06 09:31:00', '2026-02-06 09:33:27'),
(11, 21, 1, 2026, 2500.00, 'bank_transfer', 'paid', NULL, NULL, 'uploads/payments/payment_stu_1001_1770975831.pdf', 'pdf', NULL, NULL, NULL, '2026-02-13 09:43:51', '2026-02-13 10:01:27');

--
-- Triggers `monthly_payments`
--
DELIMITER $$
CREATE TRIGGER `after_monthly_payment_update` AFTER UPDATE ON `monthly_payments` FOR EACH ROW BEGIN
    DECLARE v_teacher_id VARCHAR(20);
    DECLARE v_student_id VARCHAR(20);
    DECLARE v_teacher_points DECIMAL(10,2);
    DECLARE v_institute_points DECIMAL(10,2);
    
    
    IF NEW.payment_status = 'paid' AND OLD.payment_status != 'paid' THEN
        
        
        SELECT ta.teacher_id, se.student_id 
        INTO v_teacher_id, v_student_id
        FROM student_enrollment se
        JOIN teacher_assignments ta ON se.stream_subject_id = ta.stream_subject_id 
            AND se.academic_year = ta.academic_year
        WHERE se.id = NEW.student_enrollment_id
        LIMIT 1;
        
        IF v_teacher_id IS NOT NULL THEN
            
            SET v_teacher_points = NEW.amount * 0.75;
            SET v_institute_points = NEW.amount * 0.25;
            
            
            INSERT IGNORE INTO teacher_wallet (teacher_id, total_points, total_earned)
            VALUES (v_teacher_id, 0.00, 0.00);
            
            
            UPDATE teacher_wallet
            SET total_points = total_points + v_teacher_points,
                total_earned = total_earned + v_teacher_points
            WHERE teacher_id = v_teacher_id;
            
            
            UPDATE institute_wallet
            SET total_points = total_points + v_institute_points,
                total_earned = total_earned + v_institute_points
            WHERE id = 1;
            
            
            INSERT INTO payment_transactions (
                payment_type, payment_id, teacher_id, student_id,
                total_amount, teacher_points, institute_points, transaction_status
            ) VALUES (
                'monthly', NEW.id, v_teacher_id, v_student_id,
                NEW.amount, v_teacher_points, v_institute_points, 'completed'
            );
        END IF;
        
    
    ELSEIF NEW.payment_status = 'refunded' AND OLD.payment_status = 'paid' THEN
        
        
        SELECT teacher_id, student_id, teacher_points, institute_points
        INTO v_teacher_id, v_student_id, v_teacher_points, v_institute_points
        FROM payment_transactions
        WHERE payment_type = 'monthly' AND payment_id = NEW.id AND transaction_status = 'completed'
        LIMIT 1;
        
        IF v_teacher_id IS NOT NULL THEN
            
            UPDATE teacher_wallet
            SET total_points = total_points - v_teacher_points
            WHERE teacher_id = v_teacher_id;
            
            
            UPDATE institute_wallet
            SET total_points = total_points - v_institute_points
            WHERE id = 1;
            
            
            UPDATE payment_transactions
            SET transaction_status = 'reversed'
            WHERE payment_type = 'monthly' AND payment_id = NEW.id;
        END IF;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `payment_transactions`
--

CREATE TABLE `payment_transactions` (
  `id` int(11) NOT NULL,
  `payment_type` enum('enrollment','monthly') NOT NULL,
  `payment_id` int(11) NOT NULL,
  `teacher_id` varchar(20) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `teacher_points` decimal(10,2) NOT NULL,
  `institute_points` decimal(10,2) NOT NULL,
  `commission_rate_teacher` decimal(5,2) DEFAULT 75.00,
  `commission_rate_institute` decimal(5,2) DEFAULT 25.00,
  `transaction_status` enum('pending','completed','reversed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_transactions`
--

INSERT INTO `payment_transactions` (`id`, `payment_type`, `payment_id`, `teacher_id`, `student_id`, `total_amount`, `teacher_points`, `institute_points`, `commission_rate_teacher`, `commission_rate_institute`, `transaction_status`, `created_at`, `updated_at`) VALUES
(1, 'monthly', 9, 'tea_1001', 'stu_1001', 2500.00, 1875.00, 625.00, 75.00, 25.00, 'completed', '2026-02-06 09:33:27', '2026-02-06 09:33:27'),
(2, 'enrollment', 10, 'tea_1001', 'stu_1001', 1500.00, 1125.00, 375.00, 75.00, 25.00, 'completed', '2026-02-13 09:19:43', '2026-02-13 09:19:43'),
(3, 'monthly', 11, 'tea_1001', 'stu_1001', 2500.00, 1875.00, 625.00, 75.00, 25.00, 'completed', '2026-02-13 10:01:27', '2026-02-13 10:01:27');

-- --------------------------------------------------------

--
-- Table structure for table `physical_classes`
--

CREATE TABLE `physical_classes` (
  `id` int(11) NOT NULL,
  `teacher_assignment_id` int(11) NOT NULL,
  `teacher_id` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `class_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `status` enum('scheduled','ongoing','ended','cancelled') DEFAULT 'scheduled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `physical_classes`
--

INSERT INTO `physical_classes` (`id`, `teacher_assignment_id`, `teacher_id`, `title`, `description`, `class_date`, `start_time`, `location`, `status`, `created_at`) VALUES
(1, 16, 'tea_1001', 'test', 'dsdsdsd', '2026-02-27', '18:46:00', '1111', 'ended', '2026-02-06 08:12:21');

-- --------------------------------------------------------

--
-- Table structure for table `question_answers`
--

CREATE TABLE `question_answers` (
  `id` int(11) NOT NULL COMMENT 'Primary Key',
  `question_id` int(11) NOT NULL COMMENT 'FK to exam_questions.id',
  `answer_text` text NOT NULL COMMENT 'Answer text content',
  `answer_image` varchar(255) DEFAULT NULL COMMENT 'Optional answer image path',
  `is_correct` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=wrong, 1=correct',
  `order_index` int(11) NOT NULL DEFAULT 0 COMMENT 'Answer display order',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Record creation timestamp',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Record update timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores question answer options';

-- --------------------------------------------------------

--
-- Table structure for table `question_images`
--

CREATE TABLE `question_images` (
  `id` int(11) NOT NULL COMMENT 'Primary Key',
  `question_id` int(11) NOT NULL COMMENT 'FK to exam_questions.id',
  `image_path` varchar(255) NOT NULL COMMENT 'Image file path',
  `order_index` int(11) NOT NULL DEFAULT 0 COMMENT 'Image display order',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Record creation timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores multiple images per question';

-- --------------------------------------------------------

--
-- Table structure for table `recordings`
--

CREATE TABLE `recordings` (
  `id` int(11) NOT NULL COMMENT 'Primary Key',
  `teacher_assignment_id` int(11) NOT NULL COMMENT 'Foreign Key: Links to teacher_assignments.id',
  `is_live` tinyint(1) DEFAULT 0 COMMENT '0 = Uploaded Video, 1 = Live Stream',
  `title` varchar(255) NOT NULL COMMENT 'Video title',
  `description` text DEFAULT NULL COMMENT 'Video description',
  `youtube_video_id` varchar(20) NOT NULL COMMENT 'YouTube video ID extracted from URL',
  `youtube_url` varchar(500) DEFAULT NULL COMMENT 'Original YouTube URL',
  `duration` varchar(20) DEFAULT NULL COMMENT 'Video duration (e.g., "10:30")',
  `thumbnail_url` varchar(500) DEFAULT NULL COMMENT 'YouTube thumbnail URL',
  `view_count` int(11) DEFAULT 0 COMMENT 'View count',
  `free_video` tinyint(1) DEFAULT 0 COMMENT 'Whether this video is free to watch (1 = free, 0 = requires payment)',
  `watch_limit` int(11) NOT NULL DEFAULT 3 COMMENT 'Maximum number of times a student can watch this video (0 = unlimited)',
  `status` enum('active','inactive','pending','scheduled','ongoing','ended','cancelled') NOT NULL DEFAULT 'active',
  `scheduled_start_time` datetime DEFAULT NULL,
  `actual_start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Record creation timestamp',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Record update timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores YouTube video recordings linked to teacher assignments';

--
-- Dumping data for table `recordings`
--

INSERT INTO `recordings` (`id`, `teacher_assignment_id`, `is_live`, `title`, `description`, `youtube_video_id`, `youtube_url`, `duration`, `thumbnail_url`, `view_count`, `free_video`, `watch_limit`, `status`, `scheduled_start_time`, `actual_start_time`, `end_time`, `created_at`, `updated_at`) VALUES
(22, 16, 0, 'Title 1', 'this is test video', 'W-3xZiZj_sw', 'https://youtu.be/W-3xZiZj_sw?si=8M14hk07nA3TACEw', NULL, 'https://img.youtube.com/vi/W-3xZiZj_sw/maxresdefault.jpg', 0, 0, 3, 'active', NULL, NULL, NULL, '2026-01-23 18:30:00', '2026-01-24 09:50:27'),
(23, 16, 1, 'yt', '', 'EPvMbu_0GAA', 'https://www.youtube.com/live/EPvMbu_0GAA?si=FnBWr836v17dTfL6', NULL, 'https://img.youtube.com/vi/EPvMbu_0GAA/maxresdefault.jpg', 0, 0, 3, 'inactive', '2026-01-30 13:20:00', NULL, NULL, '2026-01-30 07:51:00', '2026-01-30 08:23:03'),
(24, 16, 1, 'Testing', 'dsdasdasdasdasd', '4cx-uFxs7uk', 'https://youtu.be/4cx-uFxs7uk?si=fjekr1P2xbZHd5U7', NULL, 'https://img.youtube.com/vi/4cx-uFxs7uk/maxresdefault.jpg', 0, 0, 3, 'ended', '2026-02-14 17:41:00', '2026-02-13 14:54:49', '2026-02-13 14:56:54', '2026-02-13 09:09:47', '2026-02-13 09:26:54');

-- --------------------------------------------------------

--
-- Table structure for table `recording_files`
--

CREATE TABLE `recording_files` (
  `id` int(11) NOT NULL,
  `recording_id` int(11) NOT NULL COMMENT 'Foreign Key: Links to recordings.id',
  `uploaded_by` varchar(20) NOT NULL COMMENT 'Foreign Key: Links to users.user_id (who uploaded the file)',
  `file_name` varchar(255) NOT NULL COMMENT 'Original file name',
  `file_path` varchar(500) NOT NULL COMMENT 'Path to stored file',
  `file_size` bigint(20) NOT NULL COMMENT 'File size in bytes',
  `file_type` varchar(100) DEFAULT NULL COMMENT 'MIME type of the file',
  `file_extension` varchar(10) DEFAULT NULL COMMENT 'File extension',
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'When file was uploaded',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Status: 1=active, 0=deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores file uploads for recordings';

--
-- Dumping data for table `recording_files`
--

INSERT INTO `recording_files` (`id`, `recording_id`, `uploaded_by`, `file_name`, `file_path`, `file_size`, `file_type`, `file_extension`, `upload_date`, `status`) VALUES
(7, 22, 'tea_1001', 'Merry Christmas Instagram Post.jpg', 'uploads/recordings/22/tea_1001_1769248245_697495f56b317.jpg', 408717, 'image/jpeg', 'jpg', '2026-01-24 09:50:45', 1),
(8, 22, 'tea_1001', 'purple.jpg', 'uploads/recordings/22/tea_1001_1769502512_69787730c0ee7.jpg', 55533, 'image/jpeg', 'jpg', '2026-01-27 08:28:32', 1),
(9, 22, 'stu_1001', 'green 2.jpg', 'uploads/recordings/22/stu_1001_1769502592_6978778068726.jpg', 51122, 'image/jpeg', 'jpg', '2026-01-27 08:29:52', 1),
(10, 22, 'stu_1003', 'green.png', 'uploads/recordings/22/stu_1003_1769502777_69787839314f5.png', 209264, 'image/png', 'png', '2026-01-27 08:32:57', 1);

-- --------------------------------------------------------

--
-- Table structure for table `streams`
--

CREATE TABLE `streams` (
  `id` int(11) NOT NULL COMMENT 'Primary Key',
  `name` varchar(100) NOT NULL COMMENT 'Stream name (e.g., "Grade 6", "Grade 7", "A/L Science")',
  `description` text DEFAULT NULL COMMENT 'Optional description of the stream',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Status: 1=active, 0=inactive',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Record creation timestamp',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Record update timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores the grades or categories';

--
-- Dumping data for table `streams`
--

INSERT INTO `streams` (`id`, `name`, `description`, `status`, `created_at`, `updated_at`) VALUES
(18, '2027 A/L COMMERCE', NULL, 1, '2026-01-18 16:43:16', '2026-02-13 10:11:26'),
(19, '2028 A/L', NULL, 1, '2026-02-13 10:08:35', '2026-02-13 10:08:35');

-- --------------------------------------------------------

--
-- Table structure for table `stream_subjects`
--

CREATE TABLE `stream_subjects` (
  `id` int(11) NOT NULL COMMENT 'Primary Key',
  `stream_id` int(11) NOT NULL COMMENT 'Foreign Key: Links to streams.id',
  `subject_id` int(11) NOT NULL COMMENT 'Foreign Key: Links to subjects.id',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Status: 1=active, 0=inactive',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Record creation timestamp',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Record update timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Defines which subjects exist in which grades (The Offering)';

--
-- Dumping data for table `stream_subjects`
--

INSERT INTO `stream_subjects` (`id`, `stream_id`, `subject_id`, `status`, `created_at`, `updated_at`) VALUES
(46, 18, 18, 1, '2026-01-18 16:43:16', '2026-01-18 16:43:16'),
(47, 19, 22, 1, '2026-02-13 10:08:43', '2026-02-13 10:08:43');

-- --------------------------------------------------------

--
-- Table structure for table `student_answers`
--

CREATE TABLE `student_answers` (
  `id` int(11) NOT NULL COMMENT 'Primary Key',
  `attempt_id` int(11) NOT NULL COMMENT 'FK to exam_attempts.id',
  `question_id` int(11) NOT NULL COMMENT 'FK to exam_questions.id',
  `answer_id` int(11) NOT NULL COMMENT 'FK to question_answers.id',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Record creation timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores student selected answers';

-- --------------------------------------------------------

--
-- Table structure for table `student_enrollment`
--

CREATE TABLE `student_enrollment` (
  `id` int(11) NOT NULL COMMENT 'Primary Key',
  `student_id` varchar(20) NOT NULL COMMENT 'Foreign Key: Links to users.user_id (where role = student)',
  `stream_subject_id` int(11) NOT NULL COMMENT 'Foreign Key: Links to stream_subjects.id',
  `academic_year` int(4) NOT NULL COMMENT 'Academic year (e.g., 2025, 2026)',
  `batch_name` varchar(50) DEFAULT NULL COMMENT 'Optional batch identifier',
  `enrolled_date` date NOT NULL COMMENT 'Date when student enrolled',
  `status` enum('active','inactive','completed','dropped') NOT NULL DEFAULT 'active' COMMENT 'Enrollment status',
  `payment_status` enum('pending','paid','partial','refunded') NOT NULL DEFAULT 'pending' COMMENT 'Payment status',
  `payment_method` varchar(50) DEFAULT NULL COMMENT 'Payment method used (e.g., bank_transfer, card, cash, mobile_payment)',
  `payment_date` date DEFAULT NULL COMMENT 'Date of payment',
  `payment_amount` decimal(10,2) DEFAULT NULL COMMENT 'Amount paid',
  `notes` text DEFAULT NULL COMMENT 'Optional notes about the enrollment',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Record creation timestamp',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Record update timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Links students to specific stream-subject enrollments';

--
-- Dumping data for table `student_enrollment`
--

INSERT INTO `student_enrollment` (`id`, `student_id`, `stream_subject_id`, `academic_year`, `batch_name`, `enrolled_date`, `status`, `payment_status`, `payment_method`, `payment_date`, `payment_amount`, `notes`, `created_at`, `updated_at`) VALUES
(21, 'stu_1001', 46, 2026, NULL, '2026-01-22', 'active', 'pending', NULL, NULL, NULL, NULL, '2026-01-22 08:21:47', '2026-01-22 08:21:47'),
(22, 'stu_1003', 46, 2026, NULL, '2026-01-27', 'active', 'pending', NULL, NULL, NULL, NULL, '2026-01-27 08:31:36', '2026-01-27 08:31:36');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL COMMENT 'Primary Key',
  `name` varchar(100) NOT NULL COMMENT 'Subject name (e.g., "Science", "Mathematics", "English")',
  `code` varchar(20) DEFAULT NULL COMMENT 'Optional subject code (e.g., "SCI", "MATH")',
  `description` text DEFAULT NULL COMMENT 'Optional description of the subject',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Status: 1=active, 0=inactive',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Record creation timestamp',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Record update timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores the subject names';

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `name`, `code`, `description`, `status`, `created_at`, `updated_at`) VALUES
(16, 'Physics', '', NULL, 1, '2026-01-15 16:49:03', '2026-01-15 16:49:03'),
(17, 'Information Technology Revision', '', NULL, 1, '2026-01-16 07:46:09', '2026-01-16 07:46:09'),
(18, 'Economics', '', NULL, 1, '2026-01-16 07:47:36', '2026-01-16 07:47:36'),
(19, 'Mathematics', '', NULL, 1, '2026-01-16 08:10:14', '2026-01-16 08:10:14'),
(20, 'Business Studies', '', NULL, 1, '2026-01-16 12:12:48', '2026-01-16 12:12:48'),
(21, 'Economics English Medium', '', NULL, 1, '2026-01-17 21:50:09', '2026-01-17 21:50:09'),
(22, 'IT', '', NULL, 1, '2026-02-13 10:08:43', '2026-02-13 10:08:43');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('text','image','number','boolean','json') DEFAULT 'text',
  `description` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `description`, `updated_at`, `updated_by`) VALUES
(1, 'dashboard_background', 'uploads/backgrounds/dashboard_bg_1767088657.webp', 'image', 'Background image for student dashboard', '2025-12-30 09:57:37', 'adm_0001'),
(3, 'recordings_background', 'uploads/backgrounds/recordings_bg_1767089340.jpeg', 'image', 'Background image for recordings page', '2025-12-30 10:09:00', 'adm_0001'),
(4, 'online_courses_background', 'uploads/backgrounds/online_courses_bg_1768555238.jpg', 'image', 'Background image for online courses page', '2026-01-16 09:20:38', 'adm_1000'),
(5, 'live_classes_background', 'uploads/backgrounds/live_classes_bg_1768555898.jpeg', 'image', 'Background image for live classes page', '2026-01-16 09:31:38', 'adm_1000');

-- --------------------------------------------------------

--
-- Table structure for table `teacher_assignments`
--

CREATE TABLE `teacher_assignments` (
  `id` int(11) NOT NULL COMMENT 'Primary Key',
  `teacher_id` varchar(20) NOT NULL COMMENT 'Foreign Key: Links to users.id (where role = teacher)',
  `stream_subject_id` int(11) NOT NULL COMMENT 'Foreign Key: Links to stream_subjects.id',
  `academic_year` int(4) NOT NULL COMMENT 'Academic year (e.g., 2025, 2026) - allows same teacher to teach same subject for different batches',
  `batch_name` varchar(50) DEFAULT NULL COMMENT 'Optional batch identifier (e.g., "Batch A", "Morning Batch", "2025-2026")',
  `status` enum('active','inactive','pending') NOT NULL DEFAULT 'active' COMMENT 'Assignment status',
  `assigned_date` date DEFAULT NULL COMMENT 'Date when teacher was assigned',
  `start_date` date DEFAULT NULL COMMENT 'Start date of the assignment',
  `end_date` date DEFAULT NULL COMMENT 'End date of the assignment',
  `notes` text DEFAULT NULL COMMENT 'Optional notes about the assignment',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Record creation timestamp',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Record update timestamp',
  `cover_image` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Links teachers to specific stream-subject offerings with academic year support';

--
-- Dumping data for table `teacher_assignments`
--

INSERT INTO `teacher_assignments` (`id`, `teacher_id`, `stream_subject_id`, `academic_year`, `batch_name`, `status`, `assigned_date`, `start_date`, `end_date`, `notes`, `created_at`, `updated_at`, `cover_image`) VALUES
(16, 'tea_1001', 46, 2026, NULL, 'active', '2026-01-18', NULL, NULL, NULL, '2026-01-18 16:43:16', '2026-01-18 16:43:16', 'uploads/subject_covers/696d0da4a8135.jpg'),
(17, 'tea_1006', 47, 2026, NULL, 'active', '2026-02-13', NULL, NULL, NULL, '2026-02-13 10:08:45', '2026-02-13 10:08:45', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `teacher_education`
--

CREATE TABLE `teacher_education` (
  `id` int(11) NOT NULL COMMENT 'Primary Key',
  `teacher_id` varchar(20) NOT NULL COMMENT 'Foreign Key: Links to users.user_id (where role = teacher)',
  `qualification` varchar(200) NOT NULL COMMENT 'Qualification name (e.g., "B.Sc. in Mathematics", "M.Ed.", "Ph.D. in Physics")',
  `institution` varchar(200) DEFAULT NULL COMMENT 'Institution name where qualification was obtained',
  `year_obtained` int(4) DEFAULT NULL COMMENT 'Year when qualification was obtained',
  `field_of_study` varchar(200) DEFAULT NULL COMMENT 'Field of study or specialization',
  `grade_or_class` varchar(50) DEFAULT NULL COMMENT 'Grade/Class obtained (e.g., "First Class", "Distinction", "A+")',
  `certificate_path` varchar(255) DEFAULT NULL COMMENT 'Path to certificate document if uploaded',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Record creation timestamp',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Record update timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores education details for teachers';

--
-- Dumping data for table `teacher_education`
--

INSERT INTO `teacher_education` (`id`, `teacher_id`, `qualification`, `institution`, `year_obtained`, `field_of_study`, `grade_or_class`, `certificate_path`, `created_at`, `updated_at`) VALUES
(4, 'tea_1000', 'BSc (Computer Science)', 'University of Colombo, Sri Lanka', 2022, '', '', NULL, '2026-01-16 07:46:18', '2026-01-16 07:46:18'),
(5, 'tea_1001', 'BSc (Mathematics)', 'University of Peradeniya, Sri Lanka', 2021, '', '', NULL, '2026-01-16 07:47:47', '2026-01-16 07:47:47'),
(7, 'tea_1003', 'BSc (Physics)', 'University of Kelaniya, Sri Lanka', 2024, '', '', NULL, '2026-01-16 08:08:23', '2026-01-16 08:08:23'),
(8, 'tea_1004', 'BSc (Engineering – Electrical & Electronic)', 'University of Moratuwa, Sri Lanka', 2021, '', '', NULL, '2026-01-16 08:14:33', '2026-01-16 08:14:33'),
(9, 'tea_1006', 'Bsc in Maths', 'University of Jaffna', 2024, '', 'A', NULL, '2026-02-13 10:08:45', '2026-02-13 10:08:45');

-- --------------------------------------------------------

--
-- Table structure for table `teacher_wallet`
--

CREATE TABLE `teacher_wallet` (
  `id` int(11) NOT NULL,
  `teacher_id` varchar(20) NOT NULL,
  `total_points` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_earned` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_withdrawn` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher_wallet`
--

INSERT INTO `teacher_wallet` (`id`, `teacher_id`, `total_points`, `total_earned`, `total_withdrawn`, `created_at`, `updated_at`) VALUES
(1, 'tea_1001', 4875.00, 4875.00, 0.00, '2026-02-06 09:33:27', '2026-02-13 10:01:27');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` varchar(20) NOT NULL COMMENT 'Unique user ID (e.g., stu_0001)',
  `email` varchar(100) NOT NULL COMMENT 'Email address',
  `password` varchar(255) NOT NULL COMMENT 'Hashed password',
  `role` enum('student','teacher','instructor','admin') NOT NULL DEFAULT 'student' COMMENT 'User role: student, teacher, instructor, admin',
  `first_name` varchar(100) DEFAULT NULL COMMENT 'First name',
  `second_name` varchar(100) DEFAULT NULL COMMENT 'Last name',
  `dob` date DEFAULT NULL COMMENT 'Date of birth',
  `school_name` varchar(200) DEFAULT NULL COMMENT 'School name',
  `exam_year` int(4) DEFAULT NULL COMMENT 'Exam year (e.g., 2024)',
  `closest_town` varchar(100) DEFAULT NULL COMMENT 'Closest town',
  `district` varchar(100) DEFAULT NULL COMMENT 'District',
  `address` text DEFAULT NULL COMMENT 'Full address',
  `nic_no` varchar(20) DEFAULT NULL COMMENT 'NIC number',
  `mobile_number` varchar(20) DEFAULT NULL COMMENT 'Mobile phone number',
  `whatsapp_number` varchar(20) DEFAULT NULL COMMENT 'WhatsApp number',
  `gender` enum('male','female') DEFAULT NULL COMMENT 'Gender',
  `profile_picture` varchar(255) DEFAULT NULL COMMENT 'Path to profile picture',
  `registering_date` date NOT NULL COMMENT 'Date of registration',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Account status: 1=active, 0=inactive',
  `approved` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Approval status: 1=approved, 0=not approved',
  `verification_method` varchar(20) DEFAULT 'none' COMMENT 'Verification method: nic, mobile, none',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Record creation timestamp',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Record update timestamp',
  `session_token` varchar(64) DEFAULT NULL COMMENT 'Session token for single login',
  `session_created_at` datetime DEFAULT NULL COMMENT 'Session creation timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Users table for LMS system';

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password`, `role`, `first_name`, `second_name`, `dob`, `school_name`, `exam_year`, `closest_town`, `district`, `address`, `nic_no`, `mobile_number`, `whatsapp_number`, `gender`, `profile_picture`, `registering_date`, `status`, `approved`, `verification_method`, `created_at`, `updated_at`, `session_token`, `session_created_at`) VALUES
('adm_1000', 'admin@example.com', '$2y$10$RTf99yYEtlRzGRLjlZQJaO8yRJPyCwiF3SFFEIMErqz7zkzCzE0fS', 'admin', 'System', 'Admin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0768368202', '0768368202', NULL, NULL, '2026-01-15', 1, 1, 'none', '2026-01-15 15:27:22', '2026-02-16 04:17:39', '70f9f2bf7177b04295df21d9bf2f756db9b7d93b389ce33db25af376714b7412', '2026-02-16 09:47:39'),
('ins_1000', 'dsdsd@sdasdasddsd', '$2y$10$k6IO6wZ86PpfU2WqrqwkpeXvYUcy9tQxYu7aeQ9oOdltpBtE/yC.y', 'instructor', 'ins', 'test', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '123', '123467544', NULL, NULL, '2026-02-16', 1, 1, 'none', '2026-02-16 04:25:27', '2026-02-16 04:25:27', NULL, NULL),
('stu_1001', 'dulani.wije@mail.lk', '$2y$10$A16a.cUAReAi82RmyLNdM.L5YX3QN411qJk16GGBkwzZkgFCWIBtm', 'student', 'Dulani', 'Wijesinghe', '2004-10-20', 'Ave Maria Convent', NULL, 'Negombo', 'Gampaha', 'No 45, Temple Road, Kandy', '200479405682', '22', '0766302421', 'female', 'uploads/profiles/stu_1001_1768553098.jpg', '2026-01-16', 1, 1, 'nic', '2026-01-16 08:44:58', '2026-02-16 05:31:51', NULL, NULL),
('stu_1002', 'sandu.fer99@webmail.com', '$2y$10$i/x6BIsIBHqEW4zwcq8Fq.2Wdb0k84uyYcOXO95oMFcQ7EOtxqmCm', 'student', 'Sanduni', 'Fernando', '2006-12-31', 'Rathnavali Balika Vidyalaya', NULL, 'Mirigama', 'Gampaha', '88/1, Galle Road, Hikkaduwa, Galle', '200686608910', '44', '44', 'female', 'uploads/profiles/stu_1002_1768553347.jpg', '2026-01-16', 1, 1, 'nic', '2026-01-16 08:49:07', '2026-01-27 08:32:44', NULL, NULL),
('stu_1003', 'Arul@gmail.com', '$2y$10$H2xdPCdGnfTsT4Aqv7xB5etwqcRYpPADc3s2YT2f4mPpg/qAYpHhi', 'student', 'Arul', 'Subramaniam', '2003-03-05', 'Sahira College Colombo', NULL, 'Anuradapura', 'Anuradhapura', 'No 15,Station Road,Anuradapura', '200306502233', '55', '55', 'male', 'uploads/profiles/stu_1003_1768553530.jpg', '2026-01-16', 1, 1, 'nic', '2026-01-16 08:52:10', '2026-01-27 08:33:02', NULL, NULL),
('tea_1000', 'nimal.perera@slacademy.lk', '$2y$10$IHIXsJTqIpedpTHJvdBF4O.DamEKGIy1OxQYuHOrx8LHjVRZGffAy', 'teacher', 'Nimal', 'Perera', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '12345', '12345', NULL, 'uploads/profiles/tea_1000_1768549578.jpg', '2026-01-16', 1, 1, 'none', '2026-01-16 07:46:18', '2026-01-16 08:58:08', NULL, NULL),
('tea_1001', 'sanduni.jayasinghe@slacademy.lk', '$2y$10$MMKpGjR6ROIGt4PIBqPAReTIlfvn4g1LAsIim33RlEG3nbJVCId42', 'teacher', 'Sanduni', 'Jayasinghe', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1234567', '0763255307', NULL, 'uploads/profiles/tea_1001_1768549667.jpg', '2026-01-16', 1, 1, 'none', '2026-01-16 07:47:47', '2026-02-16 04:19:33', NULL, NULL),
('tea_1003', 'ishara.wickramasinghe@slacademy.lk', '$2y$10$k7e5gNEHmmwTyQJ7nNlpReZSSRd52UhOeAsyccwZ0QYcbKnCJQLh6', 'teacher', 'Ishara', 'Fernando', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '123456789', '123456789', NULL, 'uploads/profiles/tea_1003_1768550903.jpg', '2026-01-16', 1, 1, 'none', '2026-01-16 08:08:23', '2026-01-16 08:08:23', NULL, NULL),
('tea_1004', 'kasun.amarasinghe@slacademy.lk', '$2y$10$g9h.bGilsuyxYwz.2uJN2erErM9.eUJRc4Idenz/bkXodi8g46Ih6', 'teacher', 'Kasun', 'Amarasinghe', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1234567890', '1234567890', NULL, 'uploads/profiles/tea_1004_1768551273.jpg', '2026-01-16', 1, 1, 'none', '2026-01-16 08:14:33', '2026-01-16 08:14:33', NULL, NULL),
('tea_1005', 'tharindu.fernando@slacademy.lk', '$2y$10$NTdNtFc5mCUms09m2m/HEuhUpiuVD4QkV5RkPdqb/KpcWvPY0JF4y', 'teacher', 'Tharindu', 'Fernando', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1212', '1212', NULL, 'uploads/profiles/tea_1005_1768551769.jpg', '2026-01-16', 1, 1, 'none', '2026-01-16 08:22:49', '2026-01-16 08:22:49', NULL, NULL),
('tea_1006', 'anjula@gmail.com', '$2y$10$Ye3cXUQRUK5oyKwG3mca3.0It0txWtKdjiq6EjCQm9ajDMbXM6EJy', 'teacher', 'anjula', 'nadeeshan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0789851619', '0789851619', NULL, 'uploads/profiles/tea_1006_1770977325.jpg', '2026-02-13', 1, 1, 'none', '2026-02-13 10:08:45', '2026-02-13 10:08:45', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `video_watch_log`
--

CREATE TABLE `video_watch_log` (
  `id` int(11) NOT NULL COMMENT 'Primary Key',
  `recording_id` int(11) NOT NULL COMMENT 'Foreign Key: Links to recordings.id',
  `student_id` varchar(20) NOT NULL COMMENT 'Foreign Key: Links to users.user_id (where role = student)',
  `watched_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Timestamp when video was watched',
  `watch_duration` int(11) DEFAULT NULL COMMENT 'Duration watched in seconds (optional, for future use)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tracks video watch history for students';

--
-- Dumping data for table `video_watch_log`
--

INSERT INTO `video_watch_log` (`id`, `recording_id`, `student_id`, `watched_at`, `watch_duration`) VALUES
(8, 22, 'stu_1001', '2026-01-27 08:29:38', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `zoom_chat_messages`
--

CREATE TABLE `zoom_chat_messages` (
  `id` int(11) NOT NULL,
  `zoom_class_id` int(11) NOT NULL,
  `sender_id` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `zoom_classes`
--

CREATE TABLE `zoom_classes` (
  `id` int(11) NOT NULL,
  `teacher_assignment_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `zoom_meeting_link` varchar(500) NOT NULL,
  `zoom_meeting_id` varchar(255) DEFAULT NULL,
  `zoom_passcode` varchar(100) DEFAULT NULL,
  `scheduled_start_time` datetime NOT NULL,
  `actual_start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `status` enum('scheduled','ongoing','ended','cancelled') DEFAULT 'scheduled',
  `free_class` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `zoom_classes`
--

INSERT INTO `zoom_classes` (`id`, `teacher_assignment_id`, `title`, `description`, `zoom_meeting_link`, `zoom_meeting_id`, `zoom_passcode`, `scheduled_start_time`, `actual_start_time`, `end_time`, `status`, `free_class`, `created_at`, `updated_at`) VALUES
(1, 16, 'Zoom Class 1', 'dsdsdsdsd', 'https://us05web.zoom.us/j/88041610928?pwd=QTbATsDefivm1pEEvbFQGN5OzYi2yq.1', '', '', '2026-01-30 13:44:00', '2026-01-30 12:47:33', '2026-01-30 13:05:35', 'ended', 0, '2026-01-30 07:15:06', '2026-01-30 07:35:35'),
(2, 16, 'dsdsd', 'dsadsadasd', 'https://us05web.zoom.us/j/83980760943?pwd=h57qINgFBiOaSknvpA0A4PE3Mm7fIm.1', '', '', '2026-01-30 13:13:00', '2026-01-30 13:13:58', '2026-01-30 13:53:11', 'ended', 0, '2026-01-30 07:43:54', '2026-01-30 08:23:11');

-- --------------------------------------------------------

--
-- Table structure for table `zoom_class_files`
--

CREATE TABLE `zoom_class_files` (
  `id` int(11) NOT NULL,
  `zoom_class_id` int(11) NOT NULL,
  `uploader_id` varchar(50) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` bigint(20) NOT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `zoom_class_files`
--

INSERT INTO `zoom_class_files` (`id`, `zoom_class_id`, `uploader_id`, `file_name`, `file_path`, `file_size`, `file_type`, `uploaded_at`) VALUES
(1, 2, 'stu_1001', '4bc76022537f80afd4f3de5b4f7e232a.jpg', '697c68bed26ca_1769760958.jpg', 147381, 'image/jpeg', '2026-01-30 08:15:58');

-- --------------------------------------------------------

--
-- Table structure for table `zoom_participants`
--

CREATE TABLE `zoom_participants` (
  `id` int(11) NOT NULL,
  `zoom_class_id` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `join_time` datetime NOT NULL,
  `leave_time` datetime DEFAULT NULL,
  `duration_minutes` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `zoom_participants`
--

INSERT INTO `zoom_participants` (`id`, `zoom_class_id`, `user_id`, `join_time`, `leave_time`, `duration_minutes`, `created_at`) VALUES
(1, 1, 'tea_1001', '2026-01-30 12:47:33', '2026-01-30 12:56:41', 9, '2026-01-30 07:17:33'),
(2, 1, 'tea_1001', '2026-01-30 12:56:41', '2026-01-30 12:59:09', 2, '2026-01-30 07:26:41'),
(3, 1, 'tea_1001', '2026-01-30 12:59:09', '2026-01-30 13:01:03', 1, '2026-01-30 07:29:09'),
(4, 1, 'tea_1001', '2026-01-30 13:01:03', '2026-01-30 13:01:12', 0, '2026-01-30 07:31:03'),
(5, 1, 'tea_1001', '2026-01-30 13:01:12', '2026-01-30 13:01:39', 0, '2026-01-30 07:31:12'),
(6, 1, 'tea_1001', '2026-01-30 13:01:42', '2026-01-30 13:02:17', 0, '2026-01-30 07:31:42'),
(7, 1, 'tea_1001', '2026-01-30 13:02:17', '2026-01-30 13:02:43', 0, '2026-01-30 07:32:17'),
(8, 1, 'tea_1001', '2026-01-30 13:02:43', '2026-01-30 13:03:00', 0, '2026-01-30 07:32:43'),
(9, 1, 'tea_1001', '2026-01-30 13:03:03', '2026-01-30 13:03:17', 0, '2026-01-30 07:33:03'),
(10, 1, 'tea_1001', '2026-01-30 13:03:17', '2026-01-30 13:03:24', 0, '2026-01-30 07:33:17'),
(11, 1, 'tea_1001', '2026-01-30 13:03:24', '2026-01-30 13:05:35', 2, '2026-01-30 07:33:24'),
(12, 2, 'tea_1001', '2026-01-30 13:13:58', '2026-01-30 13:20:05', 6, '2026-01-30 07:43:58'),
(13, 2, 'tea_1001', '2026-01-30 13:44:26', '2026-01-30 13:47:15', 2, '2026-01-30 08:14:26'),
(14, 2, 'stu_1001', '2026-01-30 13:45:27', '2026-01-30 13:53:11', 7, '2026-01-30 08:15:27'),
(15, 2, 'tea_1001', '2026-01-30 13:47:15', '2026-01-30 13:47:51', 0, '2026-01-30 08:17:15'),
(16, 2, 'tea_1001', '2026-01-30 13:47:52', '2026-01-30 13:48:03', 0, '2026-01-30 08:17:52'),
(17, 2, 'tea_1001', '2026-01-30 13:48:03', '2026-01-30 13:48:38', 0, '2026-01-30 08:18:03'),
(18, 2, 'tea_1001', '2026-01-30 13:48:38', '2026-01-30 13:49:16', 0, '2026-01-30 08:18:38'),
(19, 2, 'tea_1001', '2026-01-30 13:49:16', '2026-01-30 13:49:35', 0, '2026-01-30 08:19:16'),
(20, 2, 'tea_1001', '2026-01-30 13:49:35', '2026-01-30 13:51:50', 2, '2026-01-30 08:19:35');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `al_exam_submissions`
--
ALTER TABLE `al_exam_submissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_student_submission` (`student_id`),
  ADD KEY `idx_district` (`district`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `physical_class_id` (`physical_class_id`,`student_id`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_recording_id` (`recording_id`),
  ADD KEY `idx_sender_id` (`sender_id`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `course_chats`
--
ALTER TABLE `course_chats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_recording_id` (`course_recording_id`);

--
-- Indexes for table `course_enrollments`
--
ALTER TABLE `course_enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_enrollment` (`course_id`,`student_id`);

--
-- Indexes for table `course_payments`
--
ALTER TABLE `course_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cp_enrollment` (`course_enrollment_id`),
  ADD KEY `idx_cp_status` (`payment_status`);

--
-- Indexes for table `course_recordings`
--
ALTER TABLE `course_recordings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `course_uploads`
--
ALTER TABLE `course_uploads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `enrollment_fees`
--
ALTER TABLE `enrollment_fees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_teacher_assignment_fee` (`teacher_assignment_id`),
  ADD KEY `idx_teacher_assignment_id` (`teacher_assignment_id`);

--
-- Indexes for table `enrollment_payments`
--
ALTER TABLE `enrollment_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_enrollment_payment_verified_by` (`verified_by`),
  ADD KEY `idx_student_enrollment_id` (`student_enrollment_id`),
  ADD KEY `idx_payment_status` (`payment_status`),
  ADD KEY `idx_payment_date` (`payment_date`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_teacher_id` (`teacher_id`),
  ADD KEY `idx_subject_id` (`subject_id`),
  ADD KEY `idx_is_published` (`is_published`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `exam_attempts`
--
ALTER TABLE `exam_attempts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_student_exam` (`exam_id`,`student_id`),
  ADD KEY `idx_exam_id` (`exam_id`),
  ADD KEY `idx_student_id` (`student_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `exam_questions`
--
ALTER TABLE `exam_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_exam_id` (`exam_id`),
  ADD KEY `idx_order_index` (`order_index`);

--
-- Indexes for table `institute_wallet`
--
ALTER TABLE `institute_wallet`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `instructor_requests`
--
ALTER TABLE `instructor_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `instructor_subjects`
--
ALTER TABLE `instructor_subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_assignment` (`instructor_id`,`subject_id`);

--
-- Indexes for table `live_class_participants`
--
ALTER TABLE `live_class_participants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_participant` (`recording_id`,`student_id`),
  ADD KEY `idx_recording_status` (`recording_id`,`left_at`),
  ADD KEY `idx_student` (`student_id`);

--
-- Indexes for table `monthly_payments`
--
ALTER TABLE `monthly_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_enrollment_month_year` (`student_enrollment_id`,`month`,`year`),
  ADD KEY `fk_monthly_payment_verified_by` (`verified_by`),
  ADD KEY `idx_student_enrollment_id` (`student_enrollment_id`),
  ADD KEY `idx_payment_status` (`payment_status`),
  ADD KEY `idx_month_year` (`month`,`year`),
  ADD KEY `idx_payment_date` (`payment_date`);

--
-- Indexes for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_payment_tracking` (`payment_type`,`payment_id`),
  ADD KEY `idx_teacher_transactions` (`teacher_id`,`created_at`),
  ADD KEY `idx_transaction_status` (`transaction_status`);

--
-- Indexes for table `physical_classes`
--
ALTER TABLE `physical_classes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `question_answers`
--
ALTER TABLE `question_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_question_id` (`question_id`),
  ADD KEY `idx_is_correct` (`is_correct`),
  ADD KEY `idx_order_index` (`order_index`);

--
-- Indexes for table `question_images`
--
ALTER TABLE `question_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_question_id` (`question_id`),
  ADD KEY `idx_order_index` (`order_index`);

--
-- Indexes for table `recordings`
--
ALTER TABLE `recordings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_teacher_assignment_id` (`teacher_assignment_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_youtube_video_id` (`youtube_video_id`),
  ADD KEY `idx_is_live` (`is_live`),
  ADD KEY `idx_scheduled` (`scheduled_start_time`);

--
-- Indexes for table `recording_files`
--
ALTER TABLE `recording_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_recording_id` (`recording_id`),
  ADD KEY `idx_uploaded_by` (`uploaded_by`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `streams`
--
ALTER TABLE `streams`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `idx_name` (`name`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `stream_subjects`
--
ALTER TABLE `stream_subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_stream_subject` (`stream_id`,`subject_id`),
  ADD KEY `idx_stream_id` (`stream_id`),
  ADD KEY `idx_subject_id` (`subject_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `student_answers`
--
ALTER TABLE `student_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_student_answers_answer` (`answer_id`),
  ADD KEY `idx_attempt_id` (`attempt_id`),
  ADD KEY `idx_question_id` (`question_id`);

--
-- Indexes for table `student_enrollment`
--
ALTER TABLE `student_enrollment`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_student_stream_subject_year` (`student_id`,`stream_subject_id`,`academic_year`),
  ADD KEY `idx_student_id` (`student_id`),
  ADD KEY `idx_stream_subject_id` (`stream_subject_id`),
  ADD KEY `idx_academic_year` (`academic_year`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_payment_status` (`payment_status`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `idx_name` (`name`),
  ADD KEY `idx_code` (`code`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD UNIQUE KEY `unique_setting_key` (`setting_key`);

--
-- Indexes for table `teacher_assignments`
--
ALTER TABLE `teacher_assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_teacher_stream_subject_year` (`teacher_id`,`stream_subject_id`,`academic_year`),
  ADD KEY `idx_teacher_id` (`teacher_id`),
  ADD KEY `idx_stream_subject_id` (`stream_subject_id`),
  ADD KEY `idx_academic_year` (`academic_year`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_teacher_year` (`teacher_id`,`academic_year`);

--
-- Indexes for table `teacher_education`
--
ALTER TABLE `teacher_education`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_teacher_id` (`teacher_id`);

--
-- Indexes for table `teacher_wallet`
--
ALTER TABLE `teacher_wallet`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `teacher_id` (`teacher_id`),
  ADD KEY `idx_teacher_points` (`teacher_id`,`total_points`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `mobile_number` (`mobile_number`),
  ADD UNIQUE KEY `whatsapp_number` (`whatsapp_number`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_approved` (`approved`),
  ADD KEY `idx_nic_no` (`nic_no`),
  ADD KEY `idx_mobile_number` (`mobile_number`),
  ADD KEY `idx_session_token` (`session_token`);

--
-- Indexes for table `video_watch_log`
--
ALTER TABLE `video_watch_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_recording_id` (`recording_id`),
  ADD KEY `idx_student_id` (`student_id`),
  ADD KEY `idx_recording_student` (`recording_id`,`student_id`),
  ADD KEY `idx_watched_at` (`watched_at`);

--
-- Indexes for table `zoom_chat_messages`
--
ALTER TABLE `zoom_chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_zoom_class` (`zoom_class_id`),
  ADD KEY `idx_sender` (`sender_id`),
  ADD KEY `idx_sent_at` (`sent_at`);

--
-- Indexes for table `zoom_classes`
--
ALTER TABLE `zoom_classes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_teacher_assignment` (`teacher_assignment_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_scheduled_time` (`scheduled_start_time`);

--
-- Indexes for table `zoom_class_files`
--
ALTER TABLE `zoom_class_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_zoom_class` (`zoom_class_id`),
  ADD KEY `idx_uploader` (`uploader_id`);

--
-- Indexes for table `zoom_participants`
--
ALTER TABLE `zoom_participants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_zoom_class` (`zoom_class_id`),
  ADD KEY `idx_user` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `al_exam_submissions`
--
ALTER TABLE `al_exam_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key', AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `course_chats`
--
ALTER TABLE `course_chats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `course_enrollments`
--
ALTER TABLE `course_enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `course_payments`
--
ALTER TABLE `course_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `course_recordings`
--
ALTER TABLE `course_recordings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `course_uploads`
--
ALTER TABLE `course_uploads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `enrollment_fees`
--
ALTER TABLE `enrollment_fees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key', AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `enrollment_payments`
--
ALTER TABLE `enrollment_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key', AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key', AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `exam_attempts`
--
ALTER TABLE `exam_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key', AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `exam_questions`
--
ALTER TABLE `exam_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key', AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `institute_wallet`
--
ALTER TABLE `institute_wallet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `instructor_requests`
--
ALTER TABLE `instructor_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `instructor_subjects`
--
ALTER TABLE `instructor_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `live_class_participants`
--
ALTER TABLE `live_class_participants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `monthly_payments`
--
ALTER TABLE `monthly_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key', AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `physical_classes`
--
ALTER TABLE `physical_classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `question_answers`
--
ALTER TABLE `question_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key', AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `question_images`
--
ALTER TABLE `question_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key', AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `recordings`
--
ALTER TABLE `recordings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key', AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `recording_files`
--
ALTER TABLE `recording_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `streams`
--
ALTER TABLE `streams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key', AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `stream_subjects`
--
ALTER TABLE `stream_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key', AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `student_answers`
--
ALTER TABLE `student_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key', AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `student_enrollment`
--
ALTER TABLE `student_enrollment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key', AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key', AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `teacher_assignments`
--
ALTER TABLE `teacher_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key', AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `teacher_education`
--
ALTER TABLE `teacher_education`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key', AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `teacher_wallet`
--
ALTER TABLE `teacher_wallet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `video_watch_log`
--
ALTER TABLE `video_watch_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key', AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `zoom_chat_messages`
--
ALTER TABLE `zoom_chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `zoom_classes`
--
ALTER TABLE `zoom_classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `zoom_class_files`
--
ALTER TABLE `zoom_class_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `zoom_participants`
--
ALTER TABLE `zoom_participants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `al_exam_submissions`
--
ALTER TABLE `al_exam_submissions`
  ADD CONSTRAINT `fk_al_submissions_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `fk_chat_messages_recording` FOREIGN KEY (`recording_id`) REFERENCES `recordings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_chat_messages_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `course_chats`
--
ALTER TABLE `course_chats`
  ADD CONSTRAINT `course_chats_ibfk_1` FOREIGN KEY (`course_recording_id`) REFERENCES `course_recordings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_enrollments`
--
ALTER TABLE `course_enrollments`
  ADD CONSTRAINT `course_enrollments_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_payments`
--
ALTER TABLE `course_payments`
  ADD CONSTRAINT `course_payments_ibfk_1` FOREIGN KEY (`course_enrollment_id`) REFERENCES `course_enrollments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_recordings`
--
ALTER TABLE `course_recordings`
  ADD CONSTRAINT `course_recordings_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_uploads`
--
ALTER TABLE `course_uploads`
  ADD CONSTRAINT `course_uploads_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `enrollment_fees`
--
ALTER TABLE `enrollment_fees`
  ADD CONSTRAINT `fk_enrollment_fee_assignment` FOREIGN KEY (`teacher_assignment_id`) REFERENCES `teacher_assignments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `enrollment_payments`
--
ALTER TABLE `enrollment_payments`
  ADD CONSTRAINT `fk_enrollment_payment_enrollment` FOREIGN KEY (`student_enrollment_id`) REFERENCES `student_enrollment` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_enrollment_payment_verified_by` FOREIGN KEY (`verified_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `exams`
--
ALTER TABLE `exams`
  ADD CONSTRAINT `fk_exams_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_exams_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `exam_attempts`
--
ALTER TABLE `exam_attempts`
  ADD CONSTRAINT `fk_attempts_exam` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_attempts_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `exam_questions`
--
ALTER TABLE `exam_questions`
  ADD CONSTRAINT `fk_questions_exam` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `live_class_participants`
--
ALTER TABLE `live_class_participants`
  ADD CONSTRAINT `fk_participants_recording` FOREIGN KEY (`recording_id`) REFERENCES `recordings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_participants_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `monthly_payments`
--
ALTER TABLE `monthly_payments`
  ADD CONSTRAINT `fk_monthly_payment_enrollment` FOREIGN KEY (`student_enrollment_id`) REFERENCES `student_enrollment` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_monthly_payment_verified_by` FOREIGN KEY (`verified_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `question_answers`
--
ALTER TABLE `question_answers`
  ADD CONSTRAINT `fk_answers_question` FOREIGN KEY (`question_id`) REFERENCES `exam_questions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `question_images`
--
ALTER TABLE `question_images`
  ADD CONSTRAINT `fk_images_question` FOREIGN KEY (`question_id`) REFERENCES `exam_questions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `recordings`
--
ALTER TABLE `recordings`
  ADD CONSTRAINT `fk_recordings_teacher_assignment` FOREIGN KEY (`teacher_assignment_id`) REFERENCES `teacher_assignments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `recording_files`
--
ALTER TABLE `recording_files`
  ADD CONSTRAINT `fk_recording_files_recording` FOREIGN KEY (`recording_id`) REFERENCES `recordings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_recording_files_user` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `stream_subjects`
--
ALTER TABLE `stream_subjects`
  ADD CONSTRAINT `fk_stream_subjects_stream` FOREIGN KEY (`stream_id`) REFERENCES `streams` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_stream_subjects_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `student_answers`
--
ALTER TABLE `student_answers`
  ADD CONSTRAINT `fk_student_answers_answer` FOREIGN KEY (`answer_id`) REFERENCES `question_answers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_student_answers_attempt` FOREIGN KEY (`attempt_id`) REFERENCES `exam_attempts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_student_answers_question` FOREIGN KEY (`question_id`) REFERENCES `exam_questions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `student_enrollment`
--
ALTER TABLE `student_enrollment`
  ADD CONSTRAINT `fk_student_enrollment_stream_subject` FOREIGN KEY (`stream_subject_id`) REFERENCES `stream_subjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_student_enrollment_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `teacher_assignments`
--
ALTER TABLE `teacher_assignments`
  ADD CONSTRAINT `fk_teacher_assignments_stream_subject` FOREIGN KEY (`stream_subject_id`) REFERENCES `stream_subjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_teacher_assignments_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `teacher_education`
--
ALTER TABLE `teacher_education`
  ADD CONSTRAINT `fk_teacher_education_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `video_watch_log`
--
ALTER TABLE `video_watch_log`
  ADD CONSTRAINT `fk_video_watch_log_recording` FOREIGN KEY (`recording_id`) REFERENCES `recordings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_video_watch_log_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `zoom_chat_messages`
--
ALTER TABLE `zoom_chat_messages`
  ADD CONSTRAINT `zoom_chat_messages_ibfk_1` FOREIGN KEY (`zoom_class_id`) REFERENCES `zoom_classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `zoom_chat_messages_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `zoom_classes`
--
ALTER TABLE `zoom_classes`
  ADD CONSTRAINT `zoom_classes_ibfk_1` FOREIGN KEY (`teacher_assignment_id`) REFERENCES `teacher_assignments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `zoom_class_files`
--
ALTER TABLE `zoom_class_files`
  ADD CONSTRAINT `zoom_class_files_ibfk_1` FOREIGN KEY (`zoom_class_id`) REFERENCES `zoom_classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `zoom_class_files_ibfk_2` FOREIGN KEY (`uploader_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `zoom_participants`
--
ALTER TABLE `zoom_participants`
  ADD CONSTRAINT `zoom_participants_ibfk_1` FOREIGN KEY (`zoom_class_id`) REFERENCES `zoom_classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `zoom_participants_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
--
-- Database: `phpmyadmin`
--
CREATE DATABASE IF NOT EXISTS `phpmyadmin` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `phpmyadmin`;

-- --------------------------------------------------------

--
-- Table structure for table `pma__bookmark`
--

CREATE TABLE `pma__bookmark` (
  `id` int(10) UNSIGNED NOT NULL,
  `dbase` varchar(255) NOT NULL DEFAULT '',
  `user` varchar(255) NOT NULL DEFAULT '',
  `label` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `query` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Bookmarks';

-- --------------------------------------------------------

--
-- Table structure for table `pma__central_columns`
--

CREATE TABLE `pma__central_columns` (
  `db_name` varchar(64) NOT NULL,
  `col_name` varchar(64) NOT NULL,
  `col_type` varchar(64) NOT NULL,
  `col_length` text DEFAULT NULL,
  `col_collation` varchar(64) NOT NULL,
  `col_isNull` tinyint(1) NOT NULL,
  `col_extra` varchar(255) DEFAULT '',
  `col_default` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Central list of columns';

-- --------------------------------------------------------

--
-- Table structure for table `pma__column_info`
--

CREATE TABLE `pma__column_info` (
  `id` int(5) UNSIGNED NOT NULL,
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `column_name` varchar(64) NOT NULL DEFAULT '',
  `comment` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `mimetype` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `transformation` varchar(255) NOT NULL DEFAULT '',
  `transformation_options` varchar(255) NOT NULL DEFAULT '',
  `input_transformation` varchar(255) NOT NULL DEFAULT '',
  `input_transformation_options` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Column information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__designer_settings`
--

CREATE TABLE `pma__designer_settings` (
  `username` varchar(64) NOT NULL,
  `settings_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Settings related to Designer';

-- --------------------------------------------------------

--
-- Table structure for table `pma__export_templates`
--

CREATE TABLE `pma__export_templates` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL,
  `export_type` varchar(10) NOT NULL,
  `template_name` varchar(64) NOT NULL,
  `template_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved export templates';

-- --------------------------------------------------------

--
-- Table structure for table `pma__favorite`
--

CREATE TABLE `pma__favorite` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Favorite tables';

-- --------------------------------------------------------

--
-- Table structure for table `pma__history`
--

CREATE TABLE `pma__history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db` varchar(64) NOT NULL DEFAULT '',
  `table` varchar(64) NOT NULL DEFAULT '',
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp(),
  `sqlquery` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='SQL history for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__navigationhiding`
--

CREATE TABLE `pma__navigationhiding` (
  `username` varchar(64) NOT NULL,
  `item_name` varchar(64) NOT NULL,
  `item_type` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Hidden items of navigation tree';

-- --------------------------------------------------------

--
-- Table structure for table `pma__pdf_pages`
--

CREATE TABLE `pma__pdf_pages` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `page_nr` int(10) UNSIGNED NOT NULL,
  `page_descr` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='PDF relation pages for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__recent`
--

CREATE TABLE `pma__recent` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Recently accessed tables';

-- --------------------------------------------------------

--
-- Table structure for table `pma__relation`
--

CREATE TABLE `pma__relation` (
  `master_db` varchar(64) NOT NULL DEFAULT '',
  `master_table` varchar(64) NOT NULL DEFAULT '',
  `master_field` varchar(64) NOT NULL DEFAULT '',
  `foreign_db` varchar(64) NOT NULL DEFAULT '',
  `foreign_table` varchar(64) NOT NULL DEFAULT '',
  `foreign_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Relation table';

-- --------------------------------------------------------

--
-- Table structure for table `pma__savedsearches`
--

CREATE TABLE `pma__savedsearches` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `search_name` varchar(64) NOT NULL DEFAULT '',
  `search_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved searches';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_coords`
--

CREATE TABLE `pma__table_coords` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `pdf_page_number` int(11) NOT NULL DEFAULT 0,
  `x` float UNSIGNED NOT NULL DEFAULT 0,
  `y` float UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table coordinates for phpMyAdmin PDF output';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_info`
--

CREATE TABLE `pma__table_info` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `display_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_uiprefs`
--

CREATE TABLE `pma__table_uiprefs` (
  `username` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `prefs` text NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Tables'' UI preferences';

-- --------------------------------------------------------

--
-- Table structure for table `pma__tracking`
--

CREATE TABLE `pma__tracking` (
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `version` int(10) UNSIGNED NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `schema_snapshot` text NOT NULL,
  `schema_sql` text DEFAULT NULL,
  `data_sql` longtext DEFAULT NULL,
  `tracking` set('UPDATE','REPLACE','INSERT','DELETE','TRUNCATE','CREATE DATABASE','ALTER DATABASE','DROP DATABASE','CREATE TABLE','ALTER TABLE','RENAME TABLE','DROP TABLE','CREATE INDEX','DROP INDEX','CREATE VIEW','ALTER VIEW','DROP VIEW') DEFAULT NULL,
  `tracking_active` int(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Database changes tracking for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__userconfig`
--

CREATE TABLE `pma__userconfig` (
  `username` varchar(64) NOT NULL,
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `config_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User preferences storage for phpMyAdmin';

--
-- Dumping data for table `pma__userconfig`
--

INSERT INTO `pma__userconfig` (`username`, `timevalue`, `config_data`) VALUES
('root', '2019-10-21 13:37:09', '{\"Console\\/Mode\":\"collapse\"}');

-- --------------------------------------------------------

--
-- Table structure for table `pma__usergroups`
--

CREATE TABLE `pma__usergroups` (
  `usergroup` varchar(64) NOT NULL,
  `tab` varchar(64) NOT NULL,
  `allowed` enum('Y','N') NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User groups with configured menu items';

-- --------------------------------------------------------

--
-- Table structure for table `pma__users`
--

CREATE TABLE `pma__users` (
  `username` varchar(64) NOT NULL,
  `usergroup` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Users and their assignments to user groups';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pma__central_columns`
--
ALTER TABLE `pma__central_columns`
  ADD PRIMARY KEY (`db_name`,`col_name`);

--
-- Indexes for table `pma__column_info`
--
ALTER TABLE `pma__column_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `db_name` (`db_name`,`table_name`,`column_name`);

--
-- Indexes for table `pma__designer_settings`
--
ALTER TABLE `pma__designer_settings`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_user_type_template` (`username`,`export_type`,`template_name`);

--
-- Indexes for table `pma__favorite`
--
ALTER TABLE `pma__favorite`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__history`
--
ALTER TABLE `pma__history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`,`db`,`table`,`timevalue`);

--
-- Indexes for table `pma__navigationhiding`
--
ALTER TABLE `pma__navigationhiding`
  ADD PRIMARY KEY (`username`,`item_name`,`item_type`,`db_name`,`table_name`);

--
-- Indexes for table `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  ADD PRIMARY KEY (`page_nr`),
  ADD KEY `db_name` (`db_name`);

--
-- Indexes for table `pma__recent`
--
ALTER TABLE `pma__recent`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__relation`
--
ALTER TABLE `pma__relation`
  ADD PRIMARY KEY (`master_db`,`master_table`,`master_field`),
  ADD KEY `foreign_field` (`foreign_db`,`foreign_table`);

--
-- Indexes for table `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_savedsearches_username_dbname` (`username`,`db_name`,`search_name`);

--
-- Indexes for table `pma__table_coords`
--
ALTER TABLE `pma__table_coords`
  ADD PRIMARY KEY (`db_name`,`table_name`,`pdf_page_number`);

--
-- Indexes for table `pma__table_info`
--
ALTER TABLE `pma__table_info`
  ADD PRIMARY KEY (`db_name`,`table_name`);

--
-- Indexes for table `pma__table_uiprefs`
--
ALTER TABLE `pma__table_uiprefs`
  ADD PRIMARY KEY (`username`,`db_name`,`table_name`);

--
-- Indexes for table `pma__tracking`
--
ALTER TABLE `pma__tracking`
  ADD PRIMARY KEY (`db_name`,`table_name`,`version`);

--
-- Indexes for table `pma__userconfig`
--
ALTER TABLE `pma__userconfig`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__usergroups`
--
ALTER TABLE `pma__usergroups`
  ADD PRIMARY KEY (`usergroup`,`tab`,`allowed`);

--
-- Indexes for table `pma__users`
--
ALTER TABLE `pma__users`
  ADD PRIMARY KEY (`username`,`usergroup`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__column_info`
--
ALTER TABLE `pma__column_info`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__history`
--
ALTER TABLE `pma__history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  MODIFY `page_nr` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- Database: `psychology_life_centre`
--
CREATE DATABASE IF NOT EXISTS `psychology_life_centre` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `psychology_life_centre`;

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password_hash`, `email`, `full_name`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(2, 'admin', '$2y$10$fV2xS.I1iay6Q2m8kPUMnuBsk/9I0u5jH2f0f4vKk9WvO7I9Lp9P6', 'admin@psychologylife.com', 'System Administrator', 1, NULL, '2026-01-30 09:01:49', '2026-01-30 09:01:49');

-- --------------------------------------------------------

--
-- Table structure for table `hero_slides`
--

CREATE TABLE `hero_slides` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `subtitle` text DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hero_slides`
--

INSERT INTO `hero_slides` (`id`, `title`, `subtitle`, `image_path`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(4, 'Welcome to Psychology Life Centre', 'Professional Mental Health & Wellness Support', 'uploads/slides/1769763902_ca54c49109c820408e26e114bf707071_kqkxuk.jpg', 0, 1, '2026-01-30 09:05:02', '2026-01-30 09:05:02'),
(5, 'Your Mental Health Matters', 'Compassionate Care for Your Psychological Wellbeing', 'uploads/slides/1769763917_ee1ad32056083609a55bc7008709b58d_oiuxyo.jpg', 0, 1, '2026-01-30 09:05:17', '2026-01-30 09:05:17'),
(6, 'Expert Psychological Services', 'Individual, Family & Couples Counseling', 'uploads/slides/1769763932_download_tivc2d.jpg', 0, 1, '2026-01-30 09:05:32', '2026-01-30 09:05:32');

-- --------------------------------------------------------

--
-- Table structure for table `internship_applications`
--

CREATE TABLE `internship_applications` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `qualification` varchar(255) NOT NULL,
  `current_status` varchar(255) DEFAULT NULL,
  `duration_preference` enum('1_month','3_months','6_months','flexible') DEFAULT 'flexible',
  `mode_preference` enum('onsite','online','hybrid') DEFAULT 'onsite',
  `message` text DEFAULT NULL,
  `status` enum('pending','reviewed','contacted','accepted','rejected') DEFAULT 'pending',
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reviewed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `project_date` date NOT NULL,
  `main_image` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `title`, `description`, `project_date`, `main_image`, `is_active`, `created_at`, `updated_at`) VALUES
(3, 'Project 1', 'A focused initiative designed to deliver impactful results, combining expertise, innovation, and practical solutions to achieve clear objectives.', '2026-01-13', 'uploads/projects/1769802574_ca54c49109c820408e26e114bf707071_kqkxuk.jpg', 1, '2026-01-30 19:49:34', '2026-01-30 19:49:34');

-- --------------------------------------------------------

--
-- Table structure for table `project_images`
--

CREATE TABLE `project_images` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_images`
--

INSERT INTO `project_images` (`id`, `project_id`, `image_path`, `caption`, `display_order`, `created_at`) VALUES
(1, 3, 'uploads/projects/1769802574_0_Humanism_developed_as_a_response_to_the_patient_relationship_idea_of_therapy__inh4op.jpg', '', 0, '2026-01-30 19:49:34'),
(2, 3, 'uploads/projects/1769802574_1_Benefits_of_Family_Counseling_tpsrjf.jpg', '', 1, '2026-01-30 19:49:34'),
(3, 3, 'uploads/projects/1769802574_2_download_tivc2d.jpg', '', 2, '2026-01-30 19:49:34');

-- --------------------------------------------------------

--
-- Table structure for table `reels`
--

CREATE TABLE `reels` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `thumbnail_url` varchar(255) DEFAULT NULL,
  `video_url` text NOT NULL,
  `share_link` text NOT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reels`
--

INSERT INTO `reels` (`id`, `title`, `description`, `thumbnail_url`, `video_url`, `share_link`, `display_order`, `is_active`, `created_at`) VALUES
(7, 'Facebook Reel', 'Watch on Facebook', 'uploads/reels/1769798022_Screenshot 2026-01-31 000307.png', 'https://www.facebook.com/share/v/1QPu5LKWbP/?mibextid=wwXIfr', 'https://www.facebook.com/share/v/1QPu5LKWbP/?mibextid=wwXIfr', 1, 1, '2026-01-30 18:33:42'),
(8, 'Facebook Reel', 'Watch on Facebook', 'uploads/reels/1769798053_Screenshot 2026-01-31 000357.png', 'https://www.facebook.com/share/v/1HEpXGnAqJ/?mibextid=wwXIfr', 'https://www.facebook.com/share/v/1HEpXGnAqJ/?mibextid=wwXIfr', 0, 1, '2026-01-30 18:34:13');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `category` enum('core_clinical','expanded_specialty','holistic_wellness','online_counselling') DEFAULT 'core_clinical',
  `image_url` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `title`, `description`, `category`, `image_url`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Individual Therapy & Counseling', 'One-on-one psychological support for personal challenges, emotional concerns, and mental health issues.', 'core_clinical', 'uploads/services/1769764352_Humanism_developed_as_a_response_to_the_patient_relationship_idea_of_therapy__inh4op.jpg', 1, 1, '2026-01-30 09:12:32', '2026-01-30 09:12:32'),
(2, 'Couples / Marriage Counseling', 'Sessions to improve communication, resolve conflicts, and strengthen intimate relationships.\', \'core_clinical', 'core_clinical', 'uploads/services/1769764398_Marriage_Counseling_Questions___Katy_Counseling_kl2jtl.jpg', 2, 1, '2026-01-30 09:13:18', '2026-01-30 09:13:18'),
(3, 'Family Therapy', 'Aimed at improving family relationships, communication, and dynamics within the family system.', 'core_clinical', 'uploads/services/1769764428_Benefits_of_Family_Counseling_tpsrjf.jpg', 3, 1, '2026-01-30 09:13:48', '2026-01-30 09:13:48');

-- --------------------------------------------------------

--
-- Table structure for table `social_works`
--

CREATE TABLE `social_works` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `work_date` date DEFAULT NULL,
  `main_image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `social_work_images`
--

CREATE TABLE `social_work_images` (
  `id` int(11) NOT NULL,
  `social_work_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `designation` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `profile_photo` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `is_head` tinyint(1) DEFAULT 0,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `name`, `designation`, `description`, `profile_photo`, `email`, `is_head`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Dr. N Kumaranayake', 'Director', 'Dr. N. Kumaranayaka is the founder and manager of the Psychological Life Center, providing expert guidance in mental health, counseling, and personal well-being.', 'uploads/staff/1770000005_DSC00722.JPG', '', 1, 1, 1, '2026-01-28 13:17:43', '2026-02-02 02:40:05'),
(2, 'Dr Kamala Perera', 'Clinical Psychologist', 'Dr. S. Anjali Perera specializes in cognitive-behavioral therapy and mindfulness practices. She helps clients overcome anxiety, depression, and stress while promoting emotional well-being', 'uploads/staff/1769800096_fotos-H9lg5Noj660-unsplash.jpg', '', 0, 0, 1, '2026-01-30 19:08:16', '2026-01-30 19:08:16');

-- --------------------------------------------------------

--
-- Table structure for table `youtube_videos`
--

CREATE TABLE `youtube_videos` (
  `id` int(11) NOT NULL,
  `video_url` text NOT NULL,
  `video_id` varchar(50) DEFAULT NULL,
  `thumbnail_url` text DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `youtube_videos`
--

INSERT INTO `youtube_videos` (`id`, `video_url`, `video_id`, `thumbnail_url`, `title`, `is_active`, `display_order`, `created_at`) VALUES
(1, 'https://youtu.be/_7xbvxNoPlc?si=MNxGMMk9tenr3FEM', '_7xbvxNoPlc', 'https://img.youtube.com/vi/_7xbvxNoPlc/maxresdefault.jpg', 'මානසික සුවය ලැබෙන හැටි', 1, 1, '2026-02-02 02:29:37'),
(2, 'https://youtu.be/DJbv8n2Q3g8?si=SQ8Vp2pLqe9lxMPf', 'DJbv8n2Q3g8', 'https://img.youtube.com/vi/DJbv8n2Q3g8/maxresdefault.jpg', 'මනස හදන ආහාර වේලක්', 1, 2, '2026-02-02 02:30:28'),
(3, 'https://youtu.be/c3v6gL9PsnM?si=96vCxC1K0dgrYUDR', 'c3v6gL9PsnM', 'https://img.youtube.com/vi/c3v6gL9PsnM/maxresdefault.jpg', 'සුදු වීම කෙළවර වෙන්නේ පිිළිකාවකින්', 1, 3, '2026-02-02 02:31:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `hero_slides`
--
ALTER TABLE `hero_slides`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `internship_applications`
--
ALTER TABLE `internship_applications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project_images`
--
ALTER TABLE `project_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `reels`
--
ALTER TABLE `reels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `social_works`
--
ALTER TABLE `social_works`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `social_work_images`
--
ALTER TABLE `social_work_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `social_work_id` (`social_work_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `youtube_videos`
--
ALTER TABLE `youtube_videos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `hero_slides`
--
ALTER TABLE `hero_slides`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `internship_applications`
--
ALTER TABLE `internship_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `project_images`
--
ALTER TABLE `project_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `reels`
--
ALTER TABLE `reels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `social_works`
--
ALTER TABLE `social_works`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `social_work_images`
--
ALTER TABLE `social_work_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `youtube_videos`
--
ALTER TABLE `youtube_videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `project_images`
--
ALTER TABLE `project_images`
  ADD CONSTRAINT `project_images_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `social_work_images`
--
ALTER TABLE `social_work_images`
  ADD CONSTRAINT `social_work_images_ibfk_1` FOREIGN KEY (`social_work_id`) REFERENCES `social_works` (`id`) ON DELETE CASCADE;
--
-- Database: `spare_pos`
--
CREATE DATABASE IF NOT EXISTS `spare_pos` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `spare_pos`;

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action_type` enum('edit','delete') DEFAULT NULL,
  `table_name` varchar(50) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `old_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_data`)),
  `new_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_data`)),
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `batches`
--

CREATE TABLE `batches` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `buying_price` decimal(15,2) NOT NULL,
  `selling_price` decimal(15,2) NOT NULL,
  `estimated_selling_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `original_qty` decimal(10,2) NOT NULL,
  `current_qty` decimal(10,2) NOT NULL,
  `expire_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `is_active` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `batches`
--

INSERT INTO `batches` (`id`, `product_id`, `invoice_id`, `buying_price`, `selling_price`, `estimated_selling_price`, `original_qty`, `current_qty`, `expire_date`, `created_at`, `is_active`) VALUES
(20, 27, 35, 1500.00, 1800.00, 1750.00, 41.00, 29.00, NULL, '2026-02-22 13:14:16', 1),
(21, 28, 36, 4500.00, 6500.00, 6000.00, 30.00, 10.00, NULL, '2026-02-22 13:15:09', 1),
(22, 28, 37, 5500.00, 7500.00, 6500.00, 1.00, 1.00, NULL, '2026-02-22 13:37:42', 1),
(23, 29, 38, 1000.00, 1235.00, 1200.00, 1.00, 1.00, NULL, '2026-02-22 13:59:10', 1);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `contact`, `address`, `created_at`) VALUES
(4, 'nimesh', '22222', '', '2026-02-22 13:18:19');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `invoice_no` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL,
  `supplier_name` varchar(100) DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `total_amount` decimal(15,2) DEFAULT 0.00,
  `status` enum('draft','completed') DEFAULT 'draft',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `invoice_no`, `user_id`, `supplier_name`, `invoice_date`, `total_amount`, `status`, `created_at`) VALUES
(35, 'DIRECT-20260222-084416-652', 2, 'Direct Entry', '2026-02-22', 61500.00, 'completed', '2026-02-22 13:14:16'),
(36, 'DIRECT-20260222-084509-836', 1, 'Direct Entry', '2026-02-22', 135000.00, 'completed', '2026-02-22 13:15:09'),
(37, 'DIRECT-20260222-090742-955', 1, 'Direct Entry', '2026-02-22', 5500.00, 'completed', '2026-02-22 13:37:42'),
(38, 'DIRECT-20260222-092910-320', 2, 'Direct Entry', '2026-02-22', 1000.00, 'completed', '2026-02-22 13:59:10');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `barcode` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('oil','spare_part') NOT NULL,
  `oil_type` enum('can','loose','none') DEFAULT 'none',
  `brand` varchar(100) DEFAULT NULL,
  `vehicle_compatibility` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `barcode`, `name`, `type`, `oil_type`, `brand`, `vehicle_compatibility`, `created_at`, `is_active`) VALUES
(27, '084416542', 'test cashier', 'oil', 'loose', '', 'Toyota', '2026-02-22 13:14:16', 1),
(28, '084509644', 'brake ', 'spare_part', 'none', '', 'Honda', '2026-02-22 13:15:09', 1),
(29, '48930335805', 'test can', 'oil', 'can', '', 'Toyota', '2026-02-22 13:59:10', 1);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `discount` decimal(15,2) DEFAULT 0.00,
  `final_amount` decimal(15,2) NOT NULL,
  `payment_method` enum('cash','card','cheque','credit') NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `payment_status` enum('pending','approved','rejected') DEFAULT 'approved'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `customer_id`, `user_id`, `total_amount`, `discount`, `final_amount`, `payment_method`, `created_at`, `payment_status`) VALUES
(19, 4, 2, 55000.00, 5500.00, 49500.00, 'cash', '2026-02-22 13:22:25', 'approved'),
(20, 4, 2, 19250.00, 1100.00, 18150.00, 'card', '2026-02-22 13:29:13', 'approved'),
(21, 4, 2, 1750.00, 350.00, 1400.00, 'cheque', '2026-02-22 13:30:07', 'approved'),
(22, 4, 2, 60000.00, 0.00, 60000.00, 'credit', '2026-02-22 13:38:14', 'approved'),
(23, 4, 2, 1750.00, 0.00, 1750.00, 'cheque', '2026-02-22 13:42:04', 'approved'),
(24, 4, 2, 12250.00, 0.00, 12250.00, 'credit', '2026-02-22 13:44:21', 'rejected'),
(25, 4, 2, 1750.00, 100.00, 1650.00, 'credit', '2026-02-22 13:56:43', 'rejected');

-- --------------------------------------------------------

--
-- Table structure for table `sale_items`
--

CREATE TABLE `sale_items` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `qty` decimal(10,2) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `discount` decimal(15,2) DEFAULT 0.00,
  `total_price` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sale_items`
--

INSERT INTO `sale_items` (`id`, `sale_id`, `product_id`, `batch_id`, `qty`, `unit_price`, `discount`, `total_price`) VALUES
(21, 19, 28, 21, 10.00, 5500.00, 0.00, 55000.00),
(22, 20, 27, 20, 11.00, 1750.00, 1100.00, 18150.00),
(23, 21, 27, 20, 1.00, 1750.00, 0.00, 1750.00),
(24, 22, 28, 21, 10.00, 6000.00, 0.00, 60000.00),
(25, 23, 27, 20, 1.00, 1750.00, 0.00, 1750.00),
(26, 24, 27, 20, 7.00, 1750.00, 0.00, 12250.00),
(27, 25, 27, 20, 1.00, 1750.00, 100.00, 1650.00);

-- --------------------------------------------------------

--
-- Table structure for table `system_logs`
--

CREATE TABLE `system_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_logs`
--

INSERT INTO `system_logs` (`id`, `user_id`, `action`, `details`, `created_at`) VALUES
(3, 2, 'New Item Added', 'Added new item: test cashier (084416543) with initial stock 1 at 1500 each.', '2026-02-22 13:14:16'),
(4, 1, 'New Item Added', 'Added new item: brake  (084509645) with initial stock 10 at 4500 each.', '2026-02-22 13:15:09'),
(5, 1, 'Update Registry', 'Updated Product: test cashier\nQty: 1.00 -> 10', '2026-02-22 13:16:04'),
(6, 2, 'New Batch Added', 'Batch added for test cashier: Qty 20, Total Value Rs. 30,000.00', '2026-02-22 13:17:13'),
(7, 2, 'New Sale', 'Recorded TRX-19. Total: Rs. 49,500.00', '2026-02-22 13:22:25'),
(8, 1, 'Update Registry', 'test cashier | Est: ~~1,700~~ 1,750', '2026-02-22 13:24:05'),
(9, 1, 'New Batch Added', 'Batch added for brake : Qty 10, Total Value Rs. 45,000.00', '2026-02-22 13:27:17'),
(10, 1, 'Update Registry', 'brake  | Estimated Price: ~~5,500~~ 6,000', '2026-02-22 13:27:41'),
(11, 2, 'New Sale', 'Recorded TRX-20. Total: Rs. 18,150.00', '2026-02-22 13:29:13'),
(12, 2, 'New Sale', 'Recorded TRX-21. Total: Rs. 1,400.00', '2026-02-22 13:30:07'),
(13, 1, 'Update Registry', 'test cashier | Barcode: ~~084416543~~ 084416542', '2026-02-22 13:37:08'),
(14, 1, 'Update Registry', 'brake  | Barcode: ~~084509645~~ 084509644', '2026-02-22 13:37:19'),
(15, 1, 'New Batch Added', 'Batch added for brake : Qty 10, Total Value Rs. 45,000.00', '2026-02-22 13:37:24'),
(16, 1, 'New Batch Added', 'Batch added for brake : Qty 1, Total Value Rs. 5,500.00', '2026-02-22 13:37:42'),
(17, 2, 'New Sale', 'Recorded TRX-22. Total: Rs. 60,000.00', '2026-02-22 13:38:14'),
(18, 2, 'New Sale', 'Recorded TRX-23. Total: Rs. 1,750.00', '2026-02-22 13:42:04'),
(19, 1, 'Payment Update', 'TRX-23 | Method: cheque | Amount: Rs. 1,750.00 | Status: ~~pending~~ Approved', '2026-02-22 13:42:40'),
(20, 2, 'New Sale', 'Recorded TRX-24. Total: Rs. 12,250.00', '2026-02-22 13:44:21'),
(21, 1, 'Payment Update', 'TRX-24 | Method: credit | Amount: Rs. 12,250.00 | Status: ~~pending~~ Rejected', '2026-02-22 13:45:13'),
(22, 2, 'New Sale', 'Recorded TRX-25. Total: Rs. 1,650.00', '2026-02-22 13:56:43'),
(23, 1, 'Payment Update', 'TRX-25 | Method: credit | Amount: Rs. 1,650.00 | Status: ~~pending~~ Rejected', '2026-02-22 13:57:07'),
(24, 2, 'New Batch Added', 'Batch added for test cashier: Qty 10, Total Value Rs. 15,000.00', '2026-02-22 13:58:44'),
(25, 2, 'New Item Added', 'Added new item: test can (48930335805) with initial stock 1 at 1000 each.', '2026-02-22 13:59:10'),
(26, 1, 'New Batch Added', 'Batch added for test cashier: Qty 10, Total Value Rs. 15,000.00', '2026-02-22 13:59:40');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `emp_id` varchar(10) DEFAULT NULL,
  `full_name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','cashier') NOT NULL DEFAULT 'cashier',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `emp_id`, `full_name`, `username`, `password`, `role`, `created_at`) VALUES
(1, NULL, 'System Administrator', 'admin', '$2y$10$8W3V.qNf7H7v6J5vS1.0v.4H4z8q.K.l.v.v.v.v.v.v.v.v.v', 'admin', '2026-02-17 10:10:22'),
(2, NULL, 'John Doe', 'cashier', '$2y$10$R.v/X9u9V.T.p.S.C.B.G.e.N.u.v.z.w.x.y.z.1.2.3.4.5.6.7', 'cashier', '2026-02-17 10:10:22'),
(3, NULL, 'casier', 'cashier12', '$2y$10$lLvgYwyq.6Q857XmLT2slORvzWiGTpNXp9hOtdqw/DIcRakJ7KeoW', 'cashier', '2026-02-21 13:02:12'),
(4, NULL, 'manager', 'admin12', '$2y$10$46Y9u2WW/0f69.ghEhaR0ei8PoQsb5eVZS4D.ScFr/nZ1lh8Ww57q', 'admin', '2026-02-21 13:11:23');

--
-- Triggers `users`
--
DELIMITER $$
CREATE TRIGGER `before_insert_users` BEFORE INSERT ON `users` FOR EACH ROW BEGIN
    DECLARE next_id INT;
    SET next_id = (SELECT AUTO_INCREMENT FROM information_schema.TABLES 
                   WHERE TABLE_SCHEMA = 'oil_pos_db' AND TABLE_NAME = 'users');
    SET NEW.emp_id = CONCAT('EMP-', 1000 + next_id);
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `batches`
--
ALTER TABLE `batches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `contact` (`contact`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_no` (`invoice_no`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `barcode` (`barcode`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `batch_id` (`batch_id`);

--
-- Indexes for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `emp_id` (`emp_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `batches`
--
ALTER TABLE `batches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `sale_items`
--
ALTER TABLE `sale_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `batches`
--
ALTER TABLE `batches`
  ADD CONSTRAINT `batches_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `batches_ibfk_2` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`);

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `sales_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD CONSTRAINT `sale_items_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`),
  ADD CONSTRAINT `sale_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `sale_items_ibfk_3` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`);

--
-- Constraints for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD CONSTRAINT `system_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
--
-- Database: `test`
--
CREATE DATABASE IF NOT EXISTS `test` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `test`;
--
-- Database: `testingn8n`
--
CREATE DATABASE IF NOT EXISTS `testingn8n` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `testingn8n`;
--
-- Database: `todo`
--
CREATE DATABASE IF NOT EXISTS `todo` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `todo`;

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
(3, '0001_01_01_000002_create_jobs_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('1QHNRFglzTbrhyb7wrji5UvuWH0WvYzti3FXqslh', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicE1hV0xYSTlrZjc5SHM0SWMxeHlOTE1naUhFSTN3NlZlTk9xSzk3ViI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1768850626),
('k8u7tylP7fZ4LZpigE0TcrX76S71OMTgVPFFdggu', 5, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSEpXYnQxU3dpdnUxUWg0blZIR3NvUFZscEkzbVlUcm4xamdoMTBxZyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6NTt9', 1768850197),
('O8ckJPMvMe3qpI6LzWTZtsAv76j66I7jAOunBh3W', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiaWZhSzVXNWZHeGZjNlZhd3ZMdkhkeXdGQ1lCRmZQSU9oQmxXREFFVCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1768832727);

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
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'nimesh', 'asasas@sff', NULL, '$2y$12$I7XG5a/DxOJkW1sLVKuMEOlsOPEZbp9pwi9hTdn.6qvrPLVefrea.', NULL, '2026-01-19 08:55:27', '2026-01-19 08:55:27'),
(2, 'nimesh', 'kamala@gmail.com', NULL, '$2y$12$LLDLs5AQ4SCvfEnN2SnPVOiQqXXOSTucicPO/gcZ4XL9NszKnb/e.', NULL, '2026-01-19 13:32:21', '2026-01-19 13:32:21'),
(3, 'nimesh', 'kamala12@gmail.com', NULL, '$2y$12$1WpvaXxCZRhSlE2bIVSHiOS8zVgrYWLq/Gamj1mX.OsDm39IV1KDq', NULL, '2026-01-19 13:33:01', '2026-01-19 13:33:01'),
(4, 'ref', 'asasas1111@sff', NULL, '$2y$12$xMC1hLcO0EA6VpAXwNrcHOOEQibnPpC4NeZcoVNOQ.p.CzgmTdwJS', NULL, '2026-01-19 13:44:23', '2026-01-19 13:44:23'),
(5, 'Electronics', 'kamal3@gmail.com', NULL, '$2y$12$4u/TMycosw64NfT9Pz7IBumOEgMMjK3EzN43l14vSVBFHoEQ6VpgW', NULL, '2026-01-19 13:46:37', '2026-01-19 13:46:37');

--
-- Indexes for dumped tables
--

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
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

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
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

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
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- Database: `warehouse`
--
CREATE DATABASE IF NOT EXISTS `warehouse` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `warehouse`;

-- --------------------------------------------------------

--
-- Table structure for table `batch_stocks`
--

CREATE TABLE `batch_stocks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `batch_id` varchar(255) NOT NULL,
  `supplier_invoice_id` varchar(255) NOT NULL,
  `product_id` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `expiry_date` date NOT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `free_products_quantity` int(11) NOT NULL
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
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `nic` varchar(255) NOT NULL,
  `role` enum('cash collecter','helper','driver') NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
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
(4, '2026_01_24_123718_create_suppliers_table', 1),
(5, '2026_01_24_182329_create_employees_table', 1),
(6, '2026_01_24_183649_create_products_table', 1),
(7, '2026_01_24_184353_create_trucks_table', 1),
(8, '2026_01_24_184354_create_supplier_invoices_table', 1),
(9, '2026_01_24_185147_create_supplier_payments_table', 1),
(10, '2026_01_24_185544_create_routes_table', 1),
(11, '2026_01_24_190359_create_shops_table', 1),
(12, '2026_01_24_191807_create_sales_reps_table', 1),
(13, '2026_01_24_191858_create_batch_stocks_table', 1),
(14, '2026_01_25_182728_create_personal_access_tokens_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` varchar(255) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_created_at` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `routes`
--

CREATE TABLE `routes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `route_id` varchar(255) NOT NULL,
  `route_name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_reps`
--

CREATE TABLE `sales_reps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rep_id` varchar(255) NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `rep_name` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shops`
--

CREATE TABLE `shops` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `shop_id` varchar(255) NOT NULL,
  `shop_name` varchar(255) NOT NULL,
  `route_id` bigint(20) UNSIGNED DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `current_balance` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `supplier_name` varchar(255) NOT NULL,
  `supplier_id` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `supplier_invoices`
--

CREATE TABLE `supplier_invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `supplier_invoice_id` varchar(255) NOT NULL,
  `supplier_id` varchar(255) NOT NULL,
  `total_bill_amount` decimal(15,2) NOT NULL,
  `invoice_date` date DEFAULT NULL,
  `company_discount` decimal(15,2) NOT NULL,
  `net_payable_amount` decimal(15,2) NOT NULL,
  `payment_status` enum('paid','unpaid','partial') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `supplier_payments`
--

CREATE TABLE `supplier_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `supplier_invoice_id` varchar(255) NOT NULL,
  `supplier_id` varchar(255) NOT NULL,
  `amount_paid` decimal(12,2) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method` enum('cash','bank','cheque') NOT NULL,
  `reference` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trucks`
--

CREATE TABLE `trucks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `truck_id` varchar(255) NOT NULL,
  `license_number` varchar(255) NOT NULL,
  `model` varchar(255) NOT NULL,
  `capacity` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact_number` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `contact_number`, `email_verified_at`, `username`, `password`, `remember_token`, `created_at`, `updated_at`, `role`) VALUES
(1, 'John Doe', NULL, NULL, 'johndoe', '$2y$12$z4SFu/FZmWYYWWkgFbLNFuZSHIk2JiWGM75nK7sS/zhiMkjhGXWGW', NULL, '2026-01-29 13:20:49', '2026-01-29 13:20:49', 'user'),
(2, 'sunil kumar', NULL, NULL, 'asasas@sff', '$2y$12$f5MnjyJ4wXo/Hbj5dN1jSOV5bHpjaoPF.g.cmHgvjOR9G43YmfqmW', NULL, '2026-01-29 13:21:06', '2026-01-29 13:21:06', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `batch_stocks`
--
ALTER TABLE `batch_stocks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `batch_stocks_batch_id_unique` (`batch_id`),
  ADD KEY `batch_stocks_product_id_foreign` (`product_id`),
  ADD KEY `batch_stocks_supplier_invoice_id_foreign` (`supplier_invoice_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employees_employee_id_unique` (`employee_id`),
  ADD UNIQUE KEY `employees_nic_unique` (`nic`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

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
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_product_id_unique` (`product_id`);

--
-- Indexes for table `routes`
--
ALTER TABLE `routes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `routes_route_id_unique` (`route_id`);

--
-- Indexes for table `sales_reps`
--
ALTER TABLE `sales_reps`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sales_reps_rep_id_unique` (`rep_id`),
  ADD KEY `sales_reps_supplier_id_foreign` (`supplier_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `shops`
--
ALTER TABLE `shops`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `shops_shop_id_unique` (`shop_id`),
  ADD KEY `shops_route_id_foreign` (`route_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `suppliers_supplier_id_unique` (`supplier_id`);

--
-- Indexes for table `supplier_invoices`
--
ALTER TABLE `supplier_invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `supplier_invoices_supplier_invoice_id_unique` (`supplier_invoice_id`),
  ADD KEY `supplier_invoices_supplier_id_foreign` (`supplier_id`);

--
-- Indexes for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `supplier_payments_supplier_invoice_id_unique` (`supplier_invoice_id`);

--
-- Indexes for table `trucks`
--
ALTER TABLE `trucks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `trucks_truck_id_unique` (`truck_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `batch_stocks`
--
ALTER TABLE `batch_stocks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `routes`
--
ALTER TABLE `routes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales_reps`
--
ALTER TABLE `sales_reps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shops`
--
ALTER TABLE `shops`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supplier_invoices`
--
ALTER TABLE `supplier_invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trucks`
--
ALTER TABLE `trucks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `batch_stocks`
--
ALTER TABLE `batch_stocks`
  ADD CONSTRAINT `batch_stocks_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `batch_stocks_supplier_invoice_id_foreign` FOREIGN KEY (`supplier_invoice_id`) REFERENCES `supplier_invoices` (`supplier_invoice_id`) ON DELETE CASCADE;

--
-- Constraints for table `sales_reps`
--
ALTER TABLE `sales_reps`
  ADD CONSTRAINT `sales_reps_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shops`
--
ALTER TABLE `shops`
  ADD CONSTRAINT `shops_route_id_foreign` FOREIGN KEY (`route_id`) REFERENCES `routes` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `supplier_invoices`
--
ALTER TABLE `supplier_invoices`
  ADD CONSTRAINT `supplier_invoices_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`) ON DELETE CASCADE;

--
-- Constraints for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  ADD CONSTRAINT `supplier_payments_supplier_invoice_id_foreign` FOREIGN KEY (`supplier_invoice_id`) REFERENCES `supplier_invoices` (`supplier_invoice_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
