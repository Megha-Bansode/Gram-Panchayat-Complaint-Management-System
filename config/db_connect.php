<?php
/**
 * Gram Panchayat Complaint Management System (GPCMS)
 * Central Shared Database Connection File
 * handbook reference: config/db_connect.php
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'gpcms_db');
define('DB_PORT', '3306');

// Global PDO Connection Function
function getDBConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Fallback error logging / message display
            error_log("GPCMS DB Connection Error: " . $e->getMessage());
            die("Database Connection Error: Could not connect to Gram Panchayat database. Please check XAMPP MySQL server status.");
        }
    }
    
    return $pdo;
}

// Instantiate global $conn PDO variable for direct script usage
$conn = getDBConnection();
?>
