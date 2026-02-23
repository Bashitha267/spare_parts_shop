-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 23, 2026 at 08:23 PM
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
(22, 28, 37, 5500.00, 7500.00, 6500.00, 1.00, 1.00, NULL, '2026-02-22 13:37:42', 0),
(23, 29, 38, 1000.00, 1235.00, 1200.00, 1.00, 1.00, NULL, '2026-02-22 13:59:10', 1),
(24, 30, 39, 1200.00, 1450.00, 1350.00, 10.00, 10.00, NULL, '2026-02-23 23:46:37', 1),
(25, 31, 40, 1500.00, 1650.00, 1600.00, 1.00, 1.00, NULL, '2026-02-24 00:00:33', 1);

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
(38, 'DIRECT-20260222-092910-320', 2, 'Direct Entry', '2026-02-22', 1000.00, 'completed', '2026-02-22 13:59:10'),
(39, 'DIRECT-20260223-191637-307', 1, 'Direct Entry', '2026-02-23', 12000.00, 'completed', '2026-02-23 23:46:37'),
(40, 'DIRECT-20260223-193033-303', 1, 'Direct Entry', '2026-02-23', 1500.00, 'completed', '2026-02-24 00:00:33');

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
(29, '48930335805', 'test can', 'oil', 'can', '', 'Toyota', '2026-02-22 13:59:10', 1),
(30, '1245666', 'fggaggg', 'spare_part', 'none', '', '', '2026-02-23 23:46:37', 1),
(31, '45566', 'brake pad', 'spare_part', 'none', '', '', '2026-02-24 00:00:33', 1);

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
(26, 1, 'New Batch Added', 'Batch added for test cashier: Qty 10, Total Value Rs. 15,000.00', '2026-02-22 13:59:40'),
(27, 2, 'Deactivate Batch', 'Batch #20 for test cashier set to Inactive', '2026-02-22 14:00:49'),
(28, 2, 'Activate Batch', 'Batch #20 for test cashier set to Active', '2026-02-22 14:00:50'),
(29, 2, 'Deactivate Batch', 'Batch #20 for test cashier set to Inactive', '2026-02-22 14:00:52'),
(30, 2, 'Activate Batch', 'Batch #20 for test cashier set to Active', '2026-02-22 14:00:53'),
(31, 1, 'Deactivate Batch', 'Batch #22 for brake  set to Inactive', '2026-02-23 23:45:58'),
(32, 1, 'New Item Added', 'Added new item: fggaggg (1245666) with initial stock 10 at 1200 each.', '2026-02-23 23:46:37'),
(33, 1, 'Activate Batch', 'Batch #22 for brake  set to Active', '2026-02-23 23:48:51'),
(34, 1, 'Deactivate Batch', 'Batch #22 for brake  set to Inactive', '2026-02-23 23:48:54'),
(35, 1, 'New Item Added', 'Added new item: brake pad (45566) with initial stock 1 at 1500 each.', '2026-02-24 00:00:33');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

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
