<?php
/**
 * Gram Panchayat Complaint Management System (GPCMS)
 * Database Connection Configuration
 * 
 * Handbook Contract Database Tables:
 * - users (user_id, full_name, mobile_number, email, password, role_id, village_ward, created_at)
 * - roles (role_id, role_name)
 * - categories (category_id, category_name, description, status)
 * - complaints (complaint_id, category_id, assigned_to, status, complaint_title, complaint_description, complaint_image, complainant_name, mobile_number, village_ward, created_at, updated_at)
 * - complaint_photos (photo_id, complaint_id, before_photo, after_photo, uploaded_at)
 * - complaint_history (history_id, complaint_id, status, remarks, updated_by, updated_at)
 * - notifications (notification_id, user_id, message, is_read, created_at)
 * - feedback (feedback_id, complaint_id, user_id, rating, comments, created_at)
 * - settings (setting_id, application_name, gram_panchayat_name, contact_email, contact_phone)
 * 
 * Canonical Statuses: 'pending', 'assigned', 'in_progress', 'resolved'
 */

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'gpcms_db';

try {
    // Attempt PDO connection
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    $conn = $pdo; // Alias for compatibility
} catch (PDOException $e) {
    // Try creating database if it doesn't exist locally
    try {
        $temp_pdo = new PDO("mysql:host=$db_host;charset=utf8mb4", $db_user, $db_pass);
        $temp_pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $conn = $pdo;
        
        // Execute canonical table initialization
        init_gpcms_schema($pdo);
    } catch (PDOException $ex) {
        // Fallback null connection indicator for graceful fallback in frontend templates
        $pdo = null;
        $conn = null;
        $db_error = $ex->getMessage();
    }
}

/**
 * Initialize Canonical GPCMS Schema if tables don't exist
 */
function init_gpcms_schema($pdo_conn) {
    if (!$pdo_conn) return;

    $queries = [
        "CREATE TABLE IF NOT EXISTS roles (
            role_id INT AUTO_INCREMENT PRIMARY KEY,
            role_name VARCHAR(50) NOT NULL UNIQUE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "CREATE TABLE IF NOT EXISTS users (
            user_id INT AUTO_INCREMENT PRIMARY KEY,
            full_name VARCHAR(100) NOT NULL,
            mobile_number VARCHAR(15) NOT NULL,
            email VARCHAR(100) DEFAULT NULL,
            password VARCHAR(255) DEFAULT NULL,
            role_id INT NOT NULL,
            village_ward VARCHAR(100) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "CREATE TABLE IF NOT EXISTS categories (
            category_id INT AUTO_INCREMENT PRIMARY KEY,
            category_name VARCHAR(100) NOT NULL,
            description TEXT,
            status VARCHAR(20) DEFAULT 'Active'
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "CREATE TABLE IF NOT EXISTS complaints (
            complaint_id VARCHAR(50) PRIMARY KEY,
            category_id INT NOT NULL,
            assigned_to INT DEFAULT NULL,
            status ENUM('pending', 'assigned', 'in_progress', 'resolved') NOT NULL DEFAULT 'pending',
            complaint_title VARCHAR(255) NOT NULL,
            complaint_description TEXT NOT NULL,
            complaint_image VARCHAR(255) DEFAULT NULL,
            complainant_name VARCHAR(100) NOT NULL,
            mobile_number VARCHAR(15) NOT NULL,
            village_ward VARCHAR(100) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES categories(category_id),
            FOREIGN KEY (assigned_to) REFERENCES users(user_id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "CREATE TABLE IF NOT EXISTS complaint_photos (
            photo_id INT AUTO_INCREMENT PRIMARY KEY,
            complaint_id VARCHAR(50) NOT NULL,
            before_photo VARCHAR(255) DEFAULT NULL,
            after_photo VARCHAR(255) DEFAULT NULL,
            uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (complaint_id) REFERENCES complaints(complaint_id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "CREATE TABLE IF NOT EXISTS complaint_history (
            history_id INT AUTO_INCREMENT PRIMARY KEY,
            complaint_id VARCHAR(50) NOT NULL,
            status ENUM('pending', 'assigned', 'in_progress', 'resolved') NOT NULL,
            remarks TEXT,
            updated_by INT DEFAULT NULL,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (complaint_id) REFERENCES complaints(complaint_id) ON DELETE CASCADE,
            FOREIGN KEY (updated_by) REFERENCES users(user_id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "CREATE TABLE IF NOT EXISTS notifications (
            notification_id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            message TEXT NOT NULL,
            is_read TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "CREATE TABLE IF NOT EXISTS feedback (
            feedback_id INT AUTO_INCREMENT PRIMARY KEY,
            complaint_id VARCHAR(50) NOT NULL,
            user_id INT NOT NULL,
            rating INT NOT NULL,
            comments TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (complaint_id) REFERENCES complaints(complaint_id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "CREATE TABLE IF NOT EXISTS settings (
            setting_id INT AUTO_INCREMENT PRIMARY KEY,
            application_name VARCHAR(100) DEFAULT 'Gram Panchayat Complaint Management System',
            gram_panchayat_name VARCHAR(100) DEFAULT 'Shivaji Nagar Gram Panchayat',
            contact_email VARCHAR(100) DEFAULT 'admin@grampanchayat.gov.in',
            contact_phone VARCHAR(15) DEFAULT '020-27654321'
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
    ];

    foreach ($queries as $q) {
        $pdo_conn->exec($q);
    }
}
