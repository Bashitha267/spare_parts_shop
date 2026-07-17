-- ============================================================
-- GRM (Goods Receipt Management) - Database Migration Script
-- Database: spare_pos
-- Run this ONCE in phpMyAdmin or MySQL console
-- Safe to run on existing data - only ADDS new columns/tables
-- ============================================================

-- ============================================================
-- STEP 1: Create `suppliers` table
-- ============================================================

CREATE TABLE IF NOT EXISTS `suppliers` (
  `id`         INT(11)      NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(150) NOT NULL,
  `contact`    VARCHAR(30)  DEFAULT NULL,
  `address`    TEXT         DEFAULT NULL,
  `email`      VARCHAR(100) DEFAULT NULL,
  `created_at` DATETIME     DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- STEP 2: Upgrade `invoices` table
--         Adds: supplier_id, discount, final_amount, notes
--         Existing rows are unaffected (all default to NULL/0)
-- ============================================================

ALTER TABLE `invoices`
  ADD COLUMN `supplier_id`  INT(11)       DEFAULT NULL  AFTER `supplier_name`,
  ADD COLUMN `discount`     DECIMAL(15,2) DEFAULT 0.00  AFTER `total_amount`,
  ADD COLUMN `final_amount` DECIMAL(15,2) DEFAULT 0.00  AFTER `discount`,
  ADD COLUMN `notes`        TEXT          DEFAULT NULL  AFTER `final_amount`;

-- Add foreign key linking invoices → suppliers
-- (nullable so old "Direct Entry" invoices are not broken)
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_supplier`
  FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`)
  ON DELETE SET NULL ON UPDATE CASCADE;

-- ============================================================
-- STEP 3: Create `grm_items` table
--         Purchase line items per GRM invoice
-- ============================================================

CREATE TABLE IF NOT EXISTS `grm_items` (
  `id`                INT(11)       NOT NULL AUTO_INCREMENT,
  `invoice_id`        INT(11)       NOT NULL,
  `product_id`        INT(11)       NOT NULL,
  `batch_id`          INT(11)       DEFAULT NULL,
  `buying_price`      DECIMAL(15,2) NOT NULL,
  `selling_price`     DECIMAL(15,2) NOT NULL,
  `est_selling_price` DECIMAL(15,2) DEFAULT 0.00,
  `qty`               DECIMAL(10,2) NOT NULL,
  `expire_date`       DATE          DEFAULT NULL,
  `line_total`        DECIMAL(15,2) NOT NULL,
  `created_at`        DATETIME      DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`invoice_id`),
  KEY `product_id` (`product_id`),
  KEY `batch_id`   (`batch_id`),
  CONSTRAINT `grm_items_ibfk_invoice`  FOREIGN KEY (`invoice_id`) REFERENCES `invoices`  (`id`) ON DELETE CASCADE,
  CONSTRAINT `grm_items_ibfk_product`  FOREIGN KEY (`product_id`) REFERENCES `products`  (`id`),
  CONSTRAINT `grm_items_ibfk_batch`    FOREIGN KEY (`batch_id`)   REFERENCES `batches`   (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- STEP 4: Insert Super Admin user credentials
--         Username: superadmin
--         Password: super1234 (Hashed)
-- ============================================================

INSERT INTO `users` (`full_name`, `username`, `password`, `role`) 
VALUES (
  'Super Admin', 
  'superadmin', 
  '$2y$10$Xo.cE3uGqY1/R58tKkLgEu.1yZ08C1lE4K2l79WwQp.zW754/j8H.', 
  'superadmin'
) 
ON DUPLICATE KEY UPDATE 
  `password` = '$2y$10$Xo.cE3uGqY1/R58tKkLgEu.1yZ08C1lE4K2l79WwQp.zW754/j8H.';

-- ============================================================
-- DONE
-- Summary of changes:
--   + NEW TABLE:  suppliers
--   + NEW TABLE:  grm_items
--   + NEW COLS:   invoices (supplier_id, discount, final_amount, notes)
--   + CREATED:    superadmin user (Username: superadmin / Password: super1234)
-- ============================================================
