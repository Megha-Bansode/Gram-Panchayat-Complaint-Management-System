<?php
/**
 * config/db_connect.php
 * GPCMS — Database Connection
 * Modify host/user/pass/db as per your XAMPP setup.
 */
declare(strict_types=1);

if (!defined('DB_HOST')) {
    define('DB_HOST', '127.0.0.1');
}
if (!defined('DB_USER')) {
    define('DB_USER', 'root');
}
if (!defined('DB_PASS')) {
    define('DB_PASS', '');
}
if (!defined('DB_NAME')) {
    define('DB_NAME', 'gpcms');
}
if (!defined('DB_PORT')) {
    define('DB_PORT', 3306);
}

if (!function_exists('get_db_connection')) {
    function get_db_connection(): mysqli
    {
        static $conn = null;
        if ($conn !== null) {
            return $conn;
        }

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        try {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
            $conn->set_charset('utf8mb4');
        } catch (Throwable $e) {
            // Log error — do not expose credentials in output
            error_log('GPCMS DB Error: ' . $e->getMessage());
            die('<div style="font-family:Poppins,sans-serif;padding:40px;background:#F7F3E8;color:#8A724C;border-left:4px solid #8A724C;margin:40px;border-radius:8px;">
                 <strong>⚠ Database Connection Failed</strong><br>
                 Please verify that XAMPP MySQL is running and the <code>gpcms</code> database exists.
                 </div>');
        }

        return $conn;
    }
}

// Expose global $conn variable expected by all modules
$conn = get_db_connection();

if (!isset($pdo)) {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";port=" . DB_PORT . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        error_log("PDO Connection failed: " . $e->getMessage());
    }
}

