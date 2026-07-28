<?php
// config/db_connect.php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

// Expose the global $conn variable expected by all modules (mysqli connection)
$conn = get_db_connection();

// Also provide PDO wrapper for modules expecting PDO
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=gpcms_db;charset=utf8mb4",
        'root',
        '',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    $pdo = null;
    error_log('PDO connection failed: ' . $e->getMessage());
}