-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 22, 2026 at 07:06 AM
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
-- Database: `spare_pos`
--

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

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action_type`, `table_name`, `record_id`, `reason`, `old_data`, `new_data`, `created_at`) VALUES
(1, NULL, 'delete', 'sales', 3, 'check cancelled and returns', '{\"id\":\"3\",\"customer_id\":\"1\",\"user_id\":\"2\",\"total_amount\":\"1450.00\",\"discount\":\"0.00\",\"final_amount\":\"1450.00\",\"payment_method\":\"cheque\",\"created_at\":\"2026-02-17 12:08:43\",\"payment_status\":\"approved\"}', NULL, '2026-02-17 13:01:40'),
(2, NULL, 'edit', 'sales', 13, 'this is edited for testign', '{\"id\":\"13\",\"customer_id\":\"1\",\"user_id\":\"2\",\"total_amount\":\"17000.00\",\"discount\":\"1800.00\",\"final_amount\":\"15200.00\",\"payment_method\":\"cash\",\"created_at\":\"2026-02-20 14:02:33\",\"payment_status\":\"approved\"}', '{\"final_amount\":\"14200.00\",\"payment_method\":\"card\",\"payment_status\":\"approved\"}', '2026-02-21 13:14:11'),
(3, NULL, 'edit', 'sales', 13, 'this is edited for testign', '{\"id\":\"13\",\"customer_id\":\"1\",\"user_id\":\"2\",\"total_amount\":\"17000.00\",\"discount\":\"1800.00\",\"final_amount\":\"14200.00\",\"payment_method\":\"card\",\"created_at\":\"2026-02-20 14:02:33\",\"payment_status\":\"approved\"}', '{\"final_amount\":\"14200.00\",\"payment_method\":\"card\",\"payment_status\":\"approved\"}', '2026-02-21 13:14:14'),
(4, NULL, 'edit', 'sales', 13, 'this is edited for testign', '{\"id\":\"13\",\"customer_id\":\"1\",\"user_id\":\"2\",\"total_amount\":\"17000.00\",\"discount\":\"1800.00\",\"final_amount\":\"14200.00\",\"payment_method\":\"card\",\"created_at\":\"2026-02-20 14:02:33\",\"payment_status\":\"approved\"}', '{\"final_amount\":\"14200.00\",\"payment_method\":\"card\",\"payment_status\":\"approved\"}', '2026-02-21 13:14:14'),
(5, NULL, 'edit', 'sales', 13, 'this is edited for testign', '{\"id\":\"13\",\"customer_id\":\"1\",\"user_id\":\"2\",\"total_amount\":\"17000.00\",\"discount\":\"1800.00\",\"final_amount\":\"14200.00\",\"payment_method\":\"card\",\"created_at\":\"2026-02-20 14:02:33\",\"payment_status\":\"approved\"}', '{\"final_amount\":\"14200.00\",\"payment_method\":\"card\",\"payment_status\":\"approved\"}', '2026-02-21 13:14:15'),
(6, NULL, 'edit', 'sales', 13, 'this is added', '{\"id\":\"13\",\"customer_id\":\"1\",\"user_id\":\"2\",\"total_amount\":\"17000.00\",\"discount\":\"1800.00\",\"final_amount\":\"14200.00\",\"payment_method\":\"card\",\"created_at\":\"2026-02-20 14:02:33\",\"payment_status\":\"approved\"}', '{\"final_amount\":\"14200.00\",\"payment_method\":\"card\",\"payment_status\":\"approved\"}', '2026-02-21 13:14:25'),
(7, NULL, 'edit', 'sales', 13, 'this is added', '{\"id\":\"13\",\"customer_id\":\"1\",\"user_id\":\"2\",\"total_amount\":\"17000.00\",\"discount\":\"1800.00\",\"final_amount\":\"14200.00\",\"payment_method\":\"card\",\"created_at\":\"2026-02-20 14:02:33\",\"payment_status\":\"approved\"}', '{\"final_amount\":\"1200.00\",\"payment_method\":\"card\",\"payment_status\":\"pending\"}', '2026-02-21 13:14:33'),
(8, NULL, 'edit', 'sales', 13, 'this is added', '{\"id\":\"13\",\"customer_id\":\"1\",\"user_id\":\"2\",\"total_amount\":\"17000.00\",\"discount\":\"1800.00\",\"final_amount\":\"1200.00\",\"payment_method\":\"card\",\"created_at\":\"2026-02-20 14:02:33\",\"payment_status\":\"pending\"}', '{\"final_amount\":\"14200.00\",\"payment_method\":\"card\",\"payment_status\":\"pending\"}', '2026-02-21 13:14:36'),
(9, NULL, 'edit', 'sales', 13, 'this is added', '{\"id\":\"13\",\"customer_id\":\"1\",\"user_id\":\"2\",\"total_amount\":\"17000.00\",\"discount\":\"1800.00\",\"final_amount\":\"14200.00\",\"payment_method\":\"card\",\"created_at\":\"2026-02-20 14:02:33\",\"payment_status\":\"pending\"}', '{\"final_amount\":\"14200.00\",\"payment_method\":\"card\",\"payment_status\":\"pending\"}', '2026-02-21 13:14:37'),
(10, NULL, 'edit', 'sales', 13, 'this is added', '{\"id\":\"13\",\"customer_id\":\"1\",\"user_id\":\"2\",\"total_amount\":\"17000.00\",\"discount\":\"1800.00\",\"final_amount\":\"14200.00\",\"payment_method\":\"card\",\"created_at\":\"2026-02-20 14:02:33\",\"payment_status\":\"pending\"}', '{\"final_amount\":\"14200.00\",\"payment_method\":\"card\",\"payment_status\":\"pending\"}', '2026-02-21 13:14:37'),
(11, NULL, 'edit', 'sales', 13, 'this is added', '{\"id\":\"13\",\"customer_id\":\"1\",\"user_id\":\"2\",\"total_amount\":\"17000.00\",\"discount\":\"1800.00\",\"final_amount\":\"14200.00\",\"payment_method\":\"card\",\"created_at\":\"2026-02-20 14:02:33\",\"payment_status\":\"pending\"}', '{\"final_amount\":\"14200.00\",\"payment_method\":\"card\",\"payment_status\":\"pending\"}', '2026-02-21 13:14:37'),
(12, NULL, 'edit', 'sales', 13, 'this is added', '{\"id\":\"13\",\"customer_id\":\"1\",\"user_id\":\"2\",\"total_amount\":\"17000.00\",\"discount\":\"1800.00\",\"final_amount\":\"14200.00\",\"payment_method\":\"card\",\"created_at\":\"2026-02-20 14:02:33\",\"payment_status\":\"pending\"}', '{\"final_amount\":\"14200.00\",\"payment_method\":\"card\",\"payment_status\":\"pending\"}', '2026-02-21 13:14:44'),
(13, NULL, 'edit', 'sales', 13, 'this is added', '{\"id\":\"13\",\"customer_id\":\"1\",\"user_id\":\"2\",\"total_amount\":\"17000.00\",\"discount\":\"1800.00\",\"final_amount\":\"14200.00\",\"payment_method\":\"card\",\"created_at\":\"2026-02-20 14:02:33\",\"payment_status\":\"pending\"}', '{\"final_amount\":\"14200.00\",\"payment_method\":\"card\",\"payment_status\":\"pending\"}', '2026-02-21 13:14:44'),
(14, NULL, 'edit', 'sales', 13, 'this is added', '{\"id\":\"13\",\"customer_id\":\"1\",\"user_id\":\"2\",\"total_amount\":\"17000.00\",\"discount\":\"1800.00\",\"final_amount\":\"14200.00\",\"payment_method\":\"card\",\"created_at\":\"2026-02-20 14:02:33\",\"payment_status\":\"pending\"}', '{\"final_amount\":\"14200.00\",\"payment_method\":\"card\",\"payment_status\":\"pending\"}', '2026-02-21 13:14:44'),
(15, 1, 'edit', 'sales', 13, 'testing', '{\"id\":\"13\",\"customer_id\":\"1\",\"user_id\":\"2\",\"total_amount\":\"17000.00\",\"discount\":\"1800.00\",\"final_amount\":\"14200.00\",\"payment_method\":\"card\",\"created_at\":\"2026-02-20 14:02:33\",\"payment_status\":\"pending\"}', '{\"final_amount\":\"14200.00\",\"payment_method\":\"card\",\"payment_status\":\"approved\"}', '2026-02-21 13:17:36');

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
(2, 1, 4, 1250.00, 1450.00, 0.00, 5.00, 0.00, NULL, '2026-02-17 11:03:15', 1),
(3, 8, 8, 4500.00, 5000.00, 0.00, 14.00, 0.00, NULL, '2026-02-17 13:34:59', 1),
(5, 11, 13, 1250.00, 1500.00, 0.00, 10.00, 6.00, NULL, '2026-02-19 14:56:16', 1),
(6, 11, 14, 1450.00, 1550.00, 0.00, 10.00, 10.00, NULL, '2026-02-19 14:56:43', 1),
(7, 14, 15, 1500.00, 1900.00, 0.00, 20.00, 19.00, NULL, '2026-02-19 16:27:46', 1),
(8, 11, 19, 1450.00, 1850.00, 0.00, 1.00, 1.00, NULL, '2026-02-19 22:02:05', 1),
(10, 11, 20, 1234.00, 2345.00, 0.00, 1.00, 1.00, NULL, '2026-02-19 22:15:00', 1),
(11, 11, 26, 1234.00, 2345.00, 1600.00, 1.00, 1.00, NULL, '2026-02-20 13:31:00', 1),
(12, 11, 27, 1234.00, 2345.00, 1600.00, 10.00, 10.00, NULL, '2026-02-20 13:31:09', 1),
(15, 24, 30, 1500.00, 1800.00, 1700.00, 25.00, 10.00, NULL, '2026-02-20 13:58:08', 1),
(16, 25, 31, 1700.00, 2000.00, 1900.00, 1.00, 1.00, NULL, '2026-02-20 14:13:22', 1),
(17, 26, 32, 5000.00, 7000.00, 6500.00, 1.00, 1.00, NULL, '2026-02-20 14:13:48', 1),
(18, 11, 33, 1450.00, 1550.00, 1500.00, 30.00, 20.00, NULL, '2026-02-22 11:14:52', 1),
(19, 14, 34, 1500.00, 1900.00, 1800.00, 20.00, 20.00, NULL, '2026-02-22 11:27:02', 1);

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
(1, 'nimesh bashitha', '45557855', '', '2026-02-17 11:40:13'),
(2, 'kamal perea', 'dsdsd', 'dasads', '2026-02-20 00:09:49'),
(3, 'cutomer test', '12333', '', '2026-02-21 13:42:10');

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
(4, 'inv-100', 1, 'test12', '2026-02-17', 6250.00, 'completed', '2026-02-17 11:02:41'),
(6, 'inv-10002', 1, 'newsup', '2026-02-17', 0.00, 'draft', '2026-02-17 11:04:28'),
(7, 'inv-100123', 2, 'test12d', '2026-02-17', 0.00, 'draft', '2026-02-17 13:21:49'),
(8, 'inv-1001234', 2, '', '2026-02-17', 63000.00, 'completed', '2026-02-17 13:27:49'),
(12, 'STOCK-20260219-101116', 1, 'Local Supply', '2026-02-19', 0.00, 'draft', '2026-02-19 14:41:16'),
(13, 'STOCK-20260219-102527', 2, 'Local Supply', '2026-02-19', 12500.00, 'completed', '2026-02-19 14:55:27'),
(14, 'STOCK-20260219-102628', 2, 'Local Supply', '2026-02-19', 14500.00, 'completed', '2026-02-19 14:56:28'),
(15, 'STOCK-20260219-105803', 1, 'Local Supply', '2026-02-19', 30000.00, 'completed', '2026-02-19 15:28:03'),
(16, 'STOCK-20260219-115622', 2, 'Local Supply', '2026-02-19', 0.00, 'draft', '2026-02-19 16:26:22'),
(17, 'STOCK-20260219-115849', 1, 'Local Supply', '2026-02-19', 0.00, 'draft', '2026-02-19 16:28:49'),
(18, 'STOCK-20260219-130454', 2, 'Local Supply', '2026-02-19', 0.00, 'draft', '2026-02-19 17:34:54'),
(19, 'STOCK-20260219-173038', 2, 'Local Supply', '2026-02-19', 1450.00, 'completed', '2026-02-19 22:00:38'),
(20, 'STOCK-20260219-173245', 2, 'Local Supply', '2026-02-19', 24144.00, 'completed', '2026-02-19 22:02:45'),
(25, 'STOCK-20260220-083129', 1, 'Local Supply', '2026-02-20', 0.00, 'draft', '2026-02-20 13:01:29'),
(26, 'DIRECT-20260220-090100-593', 1, 'Direct Entry', '2026-02-20', 1234.00, 'completed', '2026-02-20 13:31:00'),
(27, 'DIRECT-20260220-090109-905', 1, 'Direct Entry', '2026-02-20', 12340.00, 'draft', '2026-02-20 13:31:09'),
(28, 'DIRECT-20260220-092256-118', 1, 'Direct Entry', '2026-02-20', 2900.00, 'completed', '2026-02-20 13:52:56'),
(29, 'DIRECT-20260220-092304-524', 1, 'Direct Entry', '2026-02-20', 14500.00, 'completed', '2026-02-20 13:53:04'),
(30, 'DIRECT-20260220-092808-356', 1, 'Direct Entry', '2026-02-20', 37500.00, 'completed', '2026-02-20 13:58:08'),
(31, 'DIRECT-20260220-094322-879', 2, 'Direct Entry', '2026-02-20', 1700.00, 'completed', '2026-02-20 14:13:22'),
(32, 'DIRECT-20260220-094348-220', 2, 'Direct Entry', '2026-02-20', 5000.00, 'completed', '2026-02-20 14:13:48'),
(33, 'DIRECT-20260222-064452-311', 1, 'Direct Entry', '2026-02-22', 43500.00, 'completed', '2026-02-22 11:14:52'),
(34, 'DIRECT-20260222-065702-516', 1, 'Direct Entry', '2026-02-22', 30000.00, 'completed', '2026-02-22 11:27:02');

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
(1, 'BC-53364501', 'Mobil 1 ', 'oil', 'loose', 'Castol', 'Bike,Scooter', '2026-02-17 10:37:23', 1),
(2, '100000000001', 'Mobil 1 5W-30 Full Synthetic 4L', 'oil', 'can', 'Mobil', 'Toyota, Honda, Nissan Petrol Vehicles', '2026-02-17 13:27:17', 1),
(3, '100000000002', 'Castrol GTX 20W-50 4L', 'oil', 'can', 'Castrol', 'Toyota, Mitsubishi, Older Petrol Engines', '2026-02-17 13:27:17', 1),
(4, '100000000003', 'Shell Helix HX7 10W-40 4L', 'oil', 'can', 'Shell', 'Suzuki, Nissan, Mazda', '2026-02-17 13:27:17', 1),
(5, '100000000004', 'Total Quartz 7000 10W-40 4L', 'oil', 'can', 'Total', 'Toyota, Hyundai, Kia', '2026-02-17 13:27:17', 1),
(6, '100000000005', 'Liqui Moly 5W-30 4L', 'oil', 'can', 'Liqui Moly', 'BMW, Mercedes Benz', '2026-02-17 13:27:17', 1),
(8, '100000000007', 'Mobil Super 15W-40 1L', 'oil', 'can', 'Mobil', 'Diesel Vehicles, Toyota Hilux', '2026-02-17 13:27:17', 1),
(9, '100000000008', 'Shell Rimula R4 15W-40 5L', 'oil', 'can', 'Shell', 'Diesel Trucks & Buses', '2026-02-17 13:27:17', 1),
(10, '100000000009', 'Castrol CRB 20W-50 1L', 'oil', 'can', 'Castrol', 'Diesel & Petrol Vehicles', '2026-02-17 13:27:17', 1),
(11, '100000000010', 'Loose Engine Oil 20W-50', 'oil', 'loose', 'Generic', 'Three Wheelers, Motorcycles', '2026-02-17 13:27:17', 1),
(12, '100000000011', 'Toyota Corolla Brake Pads Front', 'spare_part', 'none', 'Toyota Genuine', 'Toyota Corolla 121/141', '2026-02-17 13:27:17', 1),
(13, '100000000012', 'Nissan FB15 Air Filter', 'spare_part', 'none', 'Nissan', 'Nissan Sunny FB15', '2026-02-17 13:27:17', 1),
(14, '100000000013', 'Honda Fit Oil Filter', 'spare_part', 'none', 'Honda', 'Honda Fit GP1/GP5', '2026-02-17 13:27:17', 1),
(15, '100000000014', 'Toyota Hilux Fuel Filter', 'spare_part', 'none', 'Toyota', 'Toyota Hilux Diesel', '2026-02-17 13:27:17', 1),
(16, '100000000015', 'Suzuki Alto Spark Plugs (Set)', 'spare_part', 'none', 'NGK', 'Suzuki Alto 800/Works', '2026-02-17 13:27:17', 1),
(17, '100000000016', 'Mitsubishi Lancer Timing Belt', 'spare_part', 'none', 'Mitsubishi', 'Mitsubishi Lancer CS3', '2026-02-17 13:27:17', 1),
(18, '100000000017', 'Perodua Axia Battery 45Ah', 'spare_part', 'none', 'Amaron', 'Perodua Axia', '2026-02-17 13:27:17', 1),
(19, '100000000018', 'Toyota Axio Wiper Blades Set', 'spare_part', 'none', 'Bosch', 'Toyota Axio 141', '2026-02-17 13:27:17', 1),
(20, '100000000019', 'Bajaj Three Wheeler Clutch Plate', 'spare_part', 'none', 'Bajaj', 'Bajaj RE 4 Stroke', '2026-02-17 13:27:17', 1),
(21, '100000000020', 'TVS Apache Brake Shoe Set', 'spare_part', 'none', 'TVS', 'TVS Apache RTR', '2026-02-17 13:27:17', 1),
(22, '171869068922', 'Engine V567', 'spare_part', 'none', '', '', '2026-02-18 17:14:40', 1),
(24, '1235', 'Test Oil', 'oil', 'can', '', 'Universal', '2026-02-20 13:58:08', 1),
(25, '1234566', 'test oil cashier', 'oil', 'loose', '', 'Universal', '2026-02-20 14:13:22', 1),
(26, '124577', 'cashier spare part test', 'spare_part', 'none', '', 'Universal', '2026-02-20 14:13:48', 1);

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
(1, 1, 2, 1450.00, 0.00, 1450.00, 'cash', '2026-02-17 11:47:43', 'approved'),
(2, 1, 2, 2900.00, 0.00, 2900.00, 'cash', '2026-02-17 11:48:40', 'approved'),
(4, 1, 2, 1450.00, 0.00, 1450.00, 'cash', '2026-02-17 12:15:40', 'approved'),
(5, 1, 2, 25000.00, 0.00, 25000.00, 'cash', '2026-02-17 13:43:12', 'approved'),
(6, 1, 2, 15000.00, 0.00, 15000.00, 'credit', '2026-02-17 13:55:47', 'approved'),
(7, 1, 2, 1400.00, 100.00, 1300.00, 'cash', '2026-02-19 16:18:49', 'approved'),
(8, 1, 2, 1400.00, 100.00, 1300.00, 'cash', '2026-02-19 16:25:52', 'approved'),
(9, 1, 2, 30000.00, 10000.00, 20000.00, 'cheque', '2026-02-19 16:28:37', 'approved'),
(10, 1, 2, 1500.00, 0.00, 0.00, 'cash', '2026-02-19 23:25:37', 'approved'),
(11, 1, 2, 1900.00, 190.00, 1710.00, 'cash', '2026-02-19 23:57:59', 'approved'),
(12, 1, 2, 2950.00, 242.50, 2707.50, 'credit', '2026-02-20 00:11:47', 'rejected'),
(13, 1, 2, 17000.00, 1800.00, 14200.00, 'card', '2026-02-20 14:02:33', 'approved'),
(14, 3, 2, 1700.00, 85.00, 1615.00, 'cash', '2026-02-21 13:42:46', 'approved'),
(15, 1, 2, 1700.00, 340.00, 1360.00, 'cash', '2026-02-21 13:43:52', 'approved'),
(16, 1, 2, 1700.00, 170.00, 1530.00, 'cash', '2026-02-21 13:46:26', 'approved'),
(17, 1, 2, 3400.00, 680.00, 2720.00, 'cash', '2026-02-21 13:47:05', 'approved');

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
(1, 1, 1, 2, 1.00, 1450.00, 0.00, 1450.00),
(2, 2, 1, 2, 2.00, 1450.00, 0.00, 2900.00),
(4, 4, 1, 2, 1.00, 1450.00, 0.00, 1450.00),
(5, 5, 8, 3, 5.00, 5000.00, 0.00, 25000.00),
(6, 6, 8, 3, 3.00, 5000.00, 0.00, 15000.00),
(7, 7, 11, 5, 1.00, 1400.00, 100.00, 1300.00),
(8, 8, 11, 5, 1.00, 1400.00, 100.00, 1300.00),
(9, 9, 8, 3, 6.00, 5000.00, 10000.00, 20000.00),
(10, 10, 11, 5, 1.00, 1500.00, 0.00, 1500.00),
(11, 11, 14, 7, 1.00, 1900.00, 0.00, 1900.00),
(12, 12, 11, 5, 1.00, 1500.00, 0.00, 1500.00),
(13, 12, 1, 2, 1.00, 1450.00, 100.00, 1350.00),
(14, 13, 24, 15, 10.00, 1700.00, 1000.00, 16000.00),
(15, 14, 24, 15, 1.00, 1700.00, 0.00, 1700.00),
(16, 15, 24, 15, 1.00, 1700.00, 0.00, 1700.00),
(17, 16, 24, 15, 1.00, 1700.00, 0.00, 1700.00),
(18, 17, 24, 15, 2.00, 1700.00, 0.00, 3400.00);

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
(1, 1, 'Register Admin', 'Added new admin: manager (admin12)', '2026-02-21 13:11:23'),
(2, 1, 'Edit Sale', 'Modified TRX-13. Reason: testing', '2026-02-21 13:17:36');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `sale_items`
--
ALTER TABLE `sale_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
