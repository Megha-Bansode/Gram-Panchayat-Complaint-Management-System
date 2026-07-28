<?php
/**
 * config/db_connect.php
 * GPCMS — Database Connection
 * Modify host/user/pass/db as per your XAMPP setup.
 */
declare(strict_types=1);

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'gpcms');

function get_db_connection(): mysqli
{
    static $conn = null;
    if ($conn !== null) return $conn;

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        // Log error — do not expose credentials in output
        error_log('GPCMS DB Error: ' . $conn->connect_errno . ' — ' . $conn->connect_error);
        die('<div style="font-family:Poppins,sans-serif;padding:40px;background:#F7F3E8;color:#8A724C;border-left:4px solid #8A724C;margin:40px;border-radius:8px;">
             <strong>⚠ Database Connection Failed</strong><br>
             Please verify that XAMPP MySQL is running and the <code>gpcms</code> database exists.
             </div>');
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}
