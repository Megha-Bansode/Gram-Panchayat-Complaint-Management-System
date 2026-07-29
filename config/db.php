<?php

declare(strict_types=1);

function get_db_connection(): mysqli
{
    static $connection = null;

    if ($connection instanceof mysqli) {
        return $connection;
    }

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    $connection = new mysqli('localhost', 'root', '', 'gpcms', 3306);

    if ($connection->connect_error) {
        error_log('Database connection failed: ' . $connection->connect_error);
        header('HTTP/1.1 503 Service Unavailable');
        exit('The authentication service is temporarily unavailable. Please try again later.');
    }

    $connection->set_charset('utf8mb4');

    return $connection;
}
