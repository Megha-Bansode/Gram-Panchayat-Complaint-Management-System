<?php
/**
 * FILE: includes/db.php
 * PURPOSE: Database connection for GPCMS.
 *          Exposes $conn (mysqli) to all including pages.
 * HANDBOOK: GPCMS Engineering Handbook V3.1
 */

define('DB_HOST',    'localhost');
define('DB_USER',    'root');
define('DB_PASS',    '');          // Change for production
define('DB_NAME',    'gpcms');
define('DB_CHARSET', 'utf8mb4');

// ── Create mysqli connection ──────────────────────────────
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// ── Check connection ──────────────────────────────────────
if ($conn->connect_error) {
    // In production: log the error, never expose it to the browser
    error_log("DB connection failed: " . $conn->connect_error);
    http_response_code(503);
    die("Service temporarily unavailable. Please try again later.");
}

// ── Set charset ───────────────────────────────────────────
if (!$conn->set_charset(DB_CHARSET)) {
    error_log("Error setting charset: " . $conn->error);
}
