<?php
/**
 * FILE: config/db_connect.php
 * PURPOSE: Central database connection for GPCMS.
 *          Exposes $conn (mysqli) to all including pages.
 *          Included as '../config/db_connect.php' from citizen/ pages.
 * CONTRACT: Citizen_Database_Contract.txt
 */

define('DB_HOST',    'localhost');
define('DB_USER',    'root');
define('DB_PASS',    '');          // Update for production
define('DB_NAME',    'gpcms');
define('DB_CHARSET', 'utf8mb4');

// ── Create mysqli connection ──────────────────────────────
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// ── Fail safely – never expose DB credentials in output ──
if ($conn->connect_error) {
    error_log('[GPCMS] DB connection failed: ' . $conn->connect_error);
    http_response_code(503);
    die('<h3 style="font-family:sans-serif;color:#8A724C;text-align:center;margin-top:5rem;">
         Service temporarily unavailable.<br>Please try again later.</h3>');
}

// ── Enforce charset ───────────────────────────────────────
if (!$conn->set_charset(DB_CHARSET)) {
    error_log('[GPCMS] Charset error: ' . $conn->error);
}
