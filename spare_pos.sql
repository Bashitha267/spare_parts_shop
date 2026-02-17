-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 17, 2026 at 08:42 AM
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
(1, NULL, 'delete', 'sales', 3, 'check cancelled and returns', '{\"id\":\"3\",\"customer_id\":\"1\",\"user_id\":\"2\",\"total_amount\":\"1450.00\",\"discount\":\"0.00\",\"final_amount\":\"1450.00\",\"payment_method\":\"cheque\",\"created_at\":\"2026-02-17 12:08:43\",\"payment_status\":\"approved\"}', NULL, '2026-02-17 13:01:40');

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
  `original_qty` decimal(10,2) NOT NULL,
  `current_qty` decimal(10,2) NOT NULL,
  `expire_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `batches`
--

INSERT INTO `batches` (`id`, `product_id`, `invoice_id`, `buying_price`, `selling_price`, `original_qty`, `current_qty`, `expire_date`, `created_at`) VALUES
(2, 1, 4, 1250.00, 1450.00, 5.00, 1.00, NULL, '2026-02-17 11:03:15');

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
(1, 'nimesh bashitha', '45557855', '', '2026-02-17 11:40:13');

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
(6, 'inv-10002', 1, 'newsup', '2026-02-17', 0.00, 'draft', '2026-02-17 11:04:28');

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
(1, 'BC-53364501', 'Mobil 1 ', 'oil', 'loose', 'Castol', 'Bike,Scooter', '2026-02-17 10:37:23', 1);

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
(4, 1, 2, 1450.00, 0.00, 1450.00, 'cash', '2026-02-17 12:15:40', 'approved');

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
(4, 4, 1, 2, 1.00, 1450.00, 0.00, 1450.00);

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
(2, NULL, 'John Doe', 'cashier', '$2y$10$R.v/X9u9V.T.p.S.C.B.G.e.N.u.v.z.w.x.y.z.1.2.3.4.5.6.7', 'cashier', '2026-02-17 10:10:22');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `batches`
--
ALTER TABLE `batches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sale_items`
--
ALTER TABLE `sale_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
