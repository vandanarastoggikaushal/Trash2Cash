-- Trash2Cash Database Schema
-- Run this SQL in phpMyAdmin or via command line to create all tables

-- Users table
CREATE TABLE IF NOT EXISTS `users` (
  `id` VARCHAR(16) NOT NULL PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `first_name` VARCHAR(100) DEFAULT NULL,
  `last_name` VARCHAR(100) DEFAULT NULL,
  `address` VARCHAR(255) DEFAULT NULL,
  `phone` VARCHAR(30) DEFAULT NULL,
  `marketing_opt_in` TINYINT(1) DEFAULT 0,
  `payout_method` VARCHAR(20) DEFAULT 'bank',
  `payout_bank_name` VARCHAR(255) DEFAULT NULL,
  `payout_bank_account` VARCHAR(50) DEFAULT NULL,
  `payout_child_name` VARCHAR(255) DEFAULT NULL,
  `payout_child_bank_account` VARCHAR(50) DEFAULT NULL,
  `payout_kiwisaver_provider` VARCHAR(255) DEFAULT NULL,
  `payout_kiwisaver_member_id` VARCHAR(50) DEFAULT NULL,
  `email` VARCHAR(255) DEFAULT NULL,
  `address_updated_at` DATETIME NULL,
  `payout_updated_at` DATETIME NULL,
  `role` VARCHAR(20) DEFAULT 'user',
  `created_at` DATETIME NOT NULL,
  `last_login` DATETIME DEFAULT NULL,
  INDEX `idx_username` (`username`),
  INDEX `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Leads table (pickup requests)
CREATE TABLE IF NOT EXISTS `leads` (
  `id` VARCHAR(16) NOT NULL PRIMARY KEY,
  `person_name` VARCHAR(255) NOT NULL,
  `person_email` VARCHAR(255) NOT NULL,
  `person_phone` VARCHAR(50) NOT NULL,
  `person_marketing_optin` TINYINT(1) DEFAULT 0,
  `address_street` VARCHAR(255) NOT NULL,
  `address_suburb` VARCHAR(100) NOT NULL,
  `address_city` VARCHAR(100) NOT NULL,
  `address_postcode` VARCHAR(10) NOT NULL,
  `address_access_notes` TEXT DEFAULT NULL,
  `pickup_type` VARCHAR(20) NOT NULL,
  `pickup_cans_estimate` INT DEFAULT NULL,
  `pickup_preferred_date` DATE DEFAULT NULL,
  `pickup_preferred_window` VARCHAR(20) DEFAULT NULL,
  `payout_method` VARCHAR(20) NOT NULL,
  `payout_bank_name` VARCHAR(255) DEFAULT NULL,
  `payout_bank_account` VARCHAR(50) DEFAULT NULL,
  `payout_child_name` VARCHAR(255) DEFAULT NULL,
  `payout_child_bank_account` VARCHAR(50) DEFAULT NULL,
  `payout_kiwisaver_provider` VARCHAR(255) DEFAULT NULL,
  `payout_kiwisaver_member_id` VARCHAR(50) DEFAULT NULL,
  `items_are_clean` TINYINT(1) DEFAULT 0,
  `accepted_terms` TINYINT(1) DEFAULT 0,
  `appliances_json` TEXT DEFAULT NULL COMMENT 'JSON array of appliances',
  `created_at` DATETIME NOT NULL,
  `status` VARCHAR(20) DEFAULT 'pending',
  INDEX `idx_email` (`person_email`),
  INDEX `idx_status` (`status`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Messages table (contact form submissions)
CREATE TABLE IF NOT EXISTS `messages` (
  `id` VARCHAR(16) NOT NULL PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `created_at` DATETIME NOT NULL,
  `read` TINYINT(1) DEFAULT 0,
  INDEX `idx_email` (`email`),
  INDEX `idx_created_at` (`created_at`),
  INDEX `idx_read` (`read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sessions table (optional - for better session management)
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` VARCHAR(128) NOT NULL PRIMARY KEY,
  `user_id` VARCHAR(16) DEFAULT NULL,
  `data` TEXT,
  `last_activity` INT UNSIGNED NOT NULL,
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_last_activity` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User Payments table (tracks payouts to registered users)
CREATE TABLE IF NOT EXISTS `user_payments` (
  `id` VARCHAR(16) NOT NULL PRIMARY KEY,
  `user_id` VARCHAR(16) NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `currency` CHAR(3) NOT NULL DEFAULT 'NZD',
  `reference` VARCHAR(100) DEFAULT NULL,
  `notes` TEXT DEFAULT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'completed',
  `payment_date` DATE NOT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_payment_date` (`payment_date`),
  INDEX `idx_status` (`status`),
  CONSTRAINT `fk_user_payments_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

