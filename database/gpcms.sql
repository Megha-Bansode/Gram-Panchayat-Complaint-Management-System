-- ============================================================================
-- Gram Panchayat Complaint Management System (GPCMS) - Canonical Database Schema
-- Database Name: gpcms_db
-- Strict Compliance with GPCMS Professional Engineering Handbook Contract
-- Location: database/gpcms.sql
-- ============================================================================

CREATE DATABASE IF NOT EXISTS `gpcms_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `gpcms_db`;

-- Drop existing tables to ensure clean schema creation
DROP TABLE IF EXISTS `feedback`;
DROP TABLE IF EXISTS `settings`;
DROP TABLE IF EXISTS `notifications`;
DROP TABLE IF EXISTS `complaint_history`;
DROP TABLE IF EXISTS `complaint_photos`;
DROP TABLE IF EXISTS `complaints`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `roles`;

-- ----------------------------------------------------------------------------
-- Table 1: roles
-- Defines user authorization levels (admin, officer, citizen)
-- ----------------------------------------------------------------------------
CREATE TABLE `roles` (
  `role_id` INT AUTO_INCREMENT PRIMARY KEY,
  `role_name` VARCHAR(50) NOT NULL UNIQUE,
  `description` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table 2: users
-- All system accounts (Admins, Field Officers, Citizens)
-- ----------------------------------------------------------------------------
CREATE TABLE `users` (
  `user_id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `phone` VARCHAR(15) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role_id` INT NOT NULL,
  `ward_no` VARCHAR(50) DEFAULT NULL,
  `designation` VARCHAR(100) DEFAULT NULL,
  `profile_image` VARCHAR(255) DEFAULT 'default_avatar.png',
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table 3: categories
-- Categories of Panchayat public issues
-- ----------------------------------------------------------------------------
CREATE TABLE `categories` (
  `category_id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_name` VARCHAR(100) NOT NULL UNIQUE,
  `description` TEXT DEFAULT NULL,
  `icon_class` VARCHAR(50) DEFAULT 'bi-exclamation-triangle',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table 4: complaints
-- Core complaints registered by citizens and handled by Panchayat/Officers
-- Canonical Status Values: 'pending', 'assigned', 'in_progress', 'resolved'
-- ----------------------------------------------------------------------------
CREATE TABLE `complaints` (
  `complaint_id` INT AUTO_INCREMENT PRIMARY KEY,
  `complaint_code` VARCHAR(20) NOT NULL UNIQUE,
  `citizen_id` INT NOT NULL,
  `category_id` INT NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `ward_no` VARCHAR(50) NOT NULL,
  `landmark` VARCHAR(255) DEFAULT NULL,
  `location_address` TEXT DEFAULT NULL,
  `priority` ENUM('low', 'medium', 'high', 'urgent') NOT NULL DEFAULT 'medium',
  `status` ENUM('pending', 'assigned', 'in_progress', 'resolved') NOT NULL DEFAULT 'pending',
  `assigned_officer_id` INT DEFAULT NULL,
  `assigned_at` DATETIME DEFAULT NULL,
  `resolved_at` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`citizen_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE RESTRICT,
  FOREIGN KEY (`assigned_officer_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table 5: complaint_photos
-- Uploaded evidence photos (initial submission, field inspection, resolution)
-- ----------------------------------------------------------------------------
CREATE TABLE `complaint_photos` (
  `photo_id` INT AUTO_INCREMENT PRIMARY KEY,
  `complaint_id` INT NOT NULL,
  `photo_path` VARCHAR(255) NOT NULL,
  `uploaded_by` INT NOT NULL,
  `photo_type` ENUM('initial', 'inspection', 'resolution') NOT NULL DEFAULT 'inspection',
  `uploaded_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`complaint_id`) ON DELETE CASCADE,
  FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table 6: complaint_history
-- Full audit trail of complaint status changes, inspection notes, & action logs
-- ----------------------------------------------------------------------------
CREATE TABLE `complaint_history` (
  `history_id` INT AUTO_INCREMENT PRIMARY KEY,
  `complaint_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `status_from` VARCHAR(50) DEFAULT NULL,
  `status_to` VARCHAR(50) NOT NULL,
  `remarks` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`complaint_id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table 7: notifications
-- User notifications for citizens, officers, and admins
-- ----------------------------------------------------------------------------
CREATE TABLE `notifications` (
  `notification_id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table 8: feedback
-- Citizen rating and feedback post resolution
-- ----------------------------------------------------------------------------
CREATE TABLE `feedback` (
  `feedback_id` INT AUTO_INCREMENT PRIMARY KEY,
  `complaint_id` INT NOT NULL UNIQUE,
  `citizen_id` INT NOT NULL,
  `rating` TINYINT NOT NULL CHECK (`rating` BETWEEN 1 AND 5),
  `comments` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`complaint_id`) ON DELETE CASCADE,
  FOREIGN KEY (`citizen_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table 9: settings
-- System-wide config settings (Gram Panchayat Name, Helpline Numbers, etc.)
-- ----------------------------------------------------------------------------
CREATE TABLE `settings` (
  `setting_id` INT AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` TEXT DEFAULT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- SEED DATA SETUP
-- ============================================================================

-- 1. Seed Roles
INSERT INTO `roles` (`role_id`, `role_name`, `description`) VALUES
(1, 'admin', 'Gram Sevak / System Administrator'),
(2, 'officer', 'Field Officer / Junior Engineer / Inspector'),
(3, 'citizen', 'Gram Panchayat Resident / Citizen');

-- 2. Seed Users
-- Password for demo accounts: 'password123'
INSERT INTO `users` (`user_id`, `full_name`, `email`, `phone`, `password`, `role_id`, `ward_no`, `designation`) VALUES
(1, 'Gram Panchayat Admin', 'admin@gpcms.gov.in', '9876543210', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe11x34jYVz8d7q.xXJ5sZ9', 1, 'Main Office', 'Gram Sevak'),
(2, 'Smit Ahirrao', 'smit.officer@gpcms.gov.in', '9876543211', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe11x34jYVz8d7q.xXJ5sZ9', 2, 'Ward 04', 'Field Officer / Junior Engineer'),
(3, 'Mukund Thorat', 'mukund.officer@gpcms.gov.in', '9876543212', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe11x34jYVz8d7q.xXJ5sZ9', 2, 'Ward 02', 'Sanitation Inspector'),
(4, 'Ramesh Patil', 'ramesh.citizen@gmail.com', '9876543213', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe11x34jYVz8d7q.xXJ5sZ9', 3, 'Ward 04', NULL),
(5, 'Suresh Sharma', 'suresh.citizen@gmail.com', '9876543214', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe11x34jYVz8d7q.xXJ5sZ9', 3, 'Ward 03', NULL);

-- 3. Seed Categories
INSERT INTO `categories` (`category_id`, `category_name`, `description`, `icon_class`) VALUES
(1, 'Water Supply', 'Issues related to water pipelines, leakage, supply timing, or contamination', 'bi-droplet-fill'),
(2, 'Roads & Infrastructure', 'Potholes, damaged street roads, broken footpaths, or construction obstruction', 'bi-cone-striped'),
(3, 'Drainage & Sanitation', 'Blocked sewage drains, overflow, open drain hazard, or stagnant wastewater', 'bi-water'),
(4, 'Street Lighting', 'Broken or malfunctioning LED street lights, faulty wiring, or dark zones', 'bi-lightbulb-fill'),
(5, 'Garbage & Waste Disposal', 'Uncollected garbage heaps, missing dustbins, or illegal dumping', 'bi-trash-fill'),
(6, 'Public Health & Hygiene', 'Mosquito breeding, fogging request, stray animals, or public toilet maintenance', 'bi-heart-pulse-fill');

-- 4. Seed Complaints
INSERT INTO `complaints` (`complaint_id`, `complaint_code`, `citizen_id`, `category_id`, `title`, `description`, `ward_no`, `landmark`, `location_address`, `priority`, `status`, `assigned_officer_id`, `assigned_at`, `created_at`) VALUES
(1, 'CMP-0012', 4, 1, 'Major Water Pipeline Burst Near Primary School', 'Main water pipeline cracked near Ward 4 primary school causing severe water logging on main street.', 'Ward 04', 'Near Primary School Gate', 'Station Road, Ward 04', 'high', 'in_progress', 2, NOW() - INTERVAL 2 DAY, NOW() - INTERVAL 3 DAY),
(2, 'CMP-0024', 5, 2, 'Deep Pothole Hazard on Market Main Road', 'Dangerous large pothole formed near market square causing traffic congestion and risks to two-wheelers.', 'Ward 03', 'Market Square Circle', 'Market Road, Ward 03', 'urgent', 'in_progress', 2, NOW() - INTERVAL 1 DAY, NOW() - INTERVAL 2 DAY),
(3, 'CMP-0035', 4, 4, 'Non-functional Street Lights in Residential Area', 'Three street light poles (P-14 to P-16) have been dark for 4 days creating safety concerns at night.', 'Ward 04', 'Opposite Community Hall', 'Shivaji Nagar, Ward 04', 'medium', 'assigned', 2, NOW() - INTERVAL 5 HOUR, NOW() - INTERVAL 1 DAY),
(4, 'CMP-0041', 5, 3, 'Blocked Drainage & Overflow Near Temple', 'Drain line choked with garbage causing overflow near temple entrance.', 'Ward 02', 'Near Hanuman Temple', 'Temple Lane, Ward 02', 'medium', 'pending', NULL, NULL, NOW() - INTERVAL 2 HOUR);

-- 5. Seed Complaint History
INSERT INTO `complaint_history` (`history_id`, `complaint_id`, `user_id`, `status_from`, `status_to`, `remarks`, `created_at`) VALUES
(1, 1, 1, 'pending', 'assigned', 'Assigned complaint to Officer Smit Ahirrao for immediate site inspection.', NOW() - INTERVAL 2 DAY),
(2, 1, 2, 'assigned', 'in_progress', 'Inspected pipeline leakage at Ward 04. Excavation completed, heavy clamp replacement work underway.', NOW() - INTERVAL 1 DAY),
(3, 2, 2, 'assigned', 'in_progress', 'Initial site inspection completed. Marked area with safety cones and arranged gravel filling team.', NOW() - INTERVAL 12 HOUR);

-- 6. Seed Settings
INSERT INTO `settings` (`setting_id`, `setting_key`, `setting_value`) VALUES
(1, 'panchayat_name', 'Gram Panchayat Office'),
(2, 'helpline_number', '+91 1800-123-4567'),
(3, 'admin_email', 'admin@gpcms.gov.in');
