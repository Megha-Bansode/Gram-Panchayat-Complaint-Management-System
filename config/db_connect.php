<?php
// config/db_connect.php
declare(strict_types=1);

if (!function_exists('get_db_connection')) {
    function get_db_connection(): mysqli
    {
        static $conn = null;
        if ($conn === null) {
            $host = '127.0.0.1';
            $user = 'root';
            $pass = '';
            $dbname = 'gpcms';
            $port = 3306;

            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            $conn = new mysqli($host, $user, $pass, $dbname, $port);
            $conn->set_charset('utf8mb4');
        }

        return $conn;
    }
}

// Expose global $conn variable expected by all modules
$conn = get_db_connection();