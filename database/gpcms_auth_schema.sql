CREATE DATABASE IF NOT EXISTS `gpcms` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `gpcms`;

-- 1. Roles Table
CREATE TABLE IF NOT EXISTS `roles` (
  `role_id` INT AUTO_INCREMENT PRIMARY KEY,
  `role_name` VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed default roles matching the SRS specifications

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(1, 'Super Admin'),
(2, 'Gram Panchayat Admin'),
(3, 'Field Officer'),
(4, 'Citizen')
-- ON DUPLICATE KEY UPDATE `role_name` = VALUES(`role_name`);
ON DUPLICATE KEY UPDATE role_name = role_name;

-- 2. Users Table
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(100) NOT NULL,
  `login_id` VARCHAR(100) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `role_id` INT NOT NULL,
  `mobile_number` VARCHAR(15) DEFAULT NULL,
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`role_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Categories Table
CREATE TABLE IF NOT EXISTS `categories` (
  `category_id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_name` VARCHAR(100) NOT NULL,
  `category_description` TEXT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed default civic complaint categories
INSERT INTO `categories` (`category_id`, `category_name`, `category_description`) VALUES
(1, 'Water Supply', 'Issues related to pipeline leaks, water contamination, or shortage'),
(2, 'Roads & Drainage', 'Potholes, broken drainage systems, or sewage blockage'),
(3, 'Street Lighting', 'Non-functional streetlights or damaged electrical poles'),
(4, 'Sanitation & Garbage', 'Uncollected garbage, public toilet maintenance, and cleanliness')
ON DUPLICATE KEY UPDATE `category_name` = VALUES(`category_name`);

-- 4. Complaints Table
CREATE TABLE IF NOT EXISTS `complaints` (
  `complaint_id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `category_id` INT NOT NULL,
  `complaint_title` VARCHAR(150) NOT NULL,
  `complaint_description` TEXT NOT NULL,
  `village_ward` VARCHAR(100) NOT NULL,
  `status` ENUM('pending', 'assigned', 'in_progress', 'resolved') DEFAULT 'pending',
  `assigned_to` INT DEFAULT NULL,
  `submitted_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`category_id`),
  FOREIGN KEY (`assigned_to`) REFERENCES `users`(`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Complaint Photos Table
CREATE TABLE IF NOT EXISTS `complaint_photos` (
  `photo_id` INT AUTO_INCREMENT PRIMARY KEY,
  `complaint_id` INT NOT NULL,
  `photo_type` ENUM('initial', 'before', 'after') NOT NULL,
  `photo_path` VARCHAR(255) NOT NULL,
  `uploaded_by` INT NOT NULL,
  `uploaded_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`complaint_id`) REFERENCES `complaints`(`complaint_id`) ON DELETE CASCADE,
  FOREIGN KEY (`uploaded_by`) REFERENCES `users`(`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Complaint History Table (Audit Trail for Tracking)
CREATE TABLE IF NOT EXISTS `complaint_history` (
  `history_id` INT AUTO_INCREMENT PRIMARY KEY,
  `complaint_id` INT NOT NULL,
  `status` ENUM('pending', 'assigned', 'in_progress', 'resolved') NOT NULL,
  `note` TEXT DEFAULT NULL,
  `updated_by` INT NOT NULL,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`complaint_id`) REFERENCES `complaints`(`complaint_id`) ON DELETE CASCADE,
  FOREIGN KEY (`updated_by`) REFERENCES `users`(`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Feedback Table
CREATE TABLE IF NOT EXISTS `feedback` (
  `feedback_id` INT AUTO_INCREMENT PRIMARY KEY,
  `complaint_id` INT NOT NULL UNIQUE,
  `user_id` INT NOT NULL,
  `rating` INT CHECK (`rating` BETWEEN 1 AND 5),
  `feedback_text` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`complaint_id`) REFERENCES `complaints`(`complaint_id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Notifications Table
CREATE TABLE IF NOT EXISTS `notifications` (
  `notification_id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `complaint_id` INT NOT NULL,
  `message` TEXT NOT NULL,
  `is_read` TINYINT(1) DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`complaint_id`) REFERENCES `complaints`(`complaint_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. Settings Table
CREATE TABLE IF NOT EXISTS `settings` (
  `setting_id` INT AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(50) NOT NULL UNIQUE,
  `setting_value` TEXT NOT NULL,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- CREATE DATABASE IF NOT EXISTS gpcms;
-- USE gpcms;

-- CREATE TABLE IF NOT EXISTS roles (
--   role_id INT AUTO_INCREMENT PRIMARY KEY,
--   role_name VARCHAR(50) NOT NULL UNIQUE,
--   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- CREATE TABLE IF NOT EXISTS users (
--   user_id INT AUTO_INCREMENT PRIMARY KEY,
--   full_name VARCHAR(100) NOT NULL,
--   login_id VARCHAR(50) NOT NULL UNIQUE,
--   password_hash VARCHAR(255) NOT NULL,
--   role_id INT NOT NULL,
--   mobile_number VARCHAR(15) NOT NULL,
--   status VARCHAR(20) NOT NULL DEFAULT 'active',
--   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--   CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(role_id)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- INSERT INTO roles (role_name) VALUES
--   ('Citizen'),
--   ('Gram Sevak'),
--   ('Field Officer'),
--   ('Super Admin')
-- ON DUPLICATE KEY UPDATE role_name = VALUES(role_name);

-- INSERT INTO users (full_name, login_id, password_hash, role_id, mobile_number, status)
-- SELECT 'Citizen User', 'citizen', '$2y$10$BBufOcaelyG8M4WL5wDvsO3i8kujh6qYjbH07ysAUHGcmL/LYkgZO', role_id, '9999999999', 'active'
-- FROM roles WHERE role_name = 'Citizen'
-- ON DUPLICATE KEY UPDATE full_name = VALUES(full_name);

-- INSERT INTO users (full_name, login_id, password_hash, role_id, mobile_number, status)
-- SELECT 'Gram Sevak User', 'gramsevak', '$2y$10$4sUl14R27ngS6ks1076B0ecwdEU3fRYoOWqudUgnsfQNkJyrP7NLm', role_id, '8888888888', 'active'
-- FROM roles WHERE role_name = 'Gram Sevak'
-- ON DUPLICATE KEY UPDATE full_name = VALUES(full_name);

-- INSERT INTO users (full_name, login_id, password_hash, role_id, mobile_number, status)
-- SELECT 'Field Officer User', 'fieldofficer', '$2y$10$fA/3ur5d81c9itWyZu7fc.YZs7nmDLNtDeG4l/YExgQ66.TU6vZcK', role_id, '7777777777', 'active'
-- FROM roles WHERE role_name = 'Field Officer'
-- ON DUPLICATE KEY UPDATE full_name = VALUES(full_name);

-- INSERT INTO users (full_name, login_id, password_hash, role_id, mobile_number, status)
-- SELECT 'Super Admin User', 'superadmin', '$2y$10$OBWzmUt6HMWKSr9qO1nCbea/FEXQ2M7bvx8agnTtz9xcB5n23ICxW', role_id, '6666666666', 'active'
-- FROM roles WHERE role_name = 'Super Admin'
-- ON DUPLICATE KEY UPDATE full_name = VALUES(full_name);
