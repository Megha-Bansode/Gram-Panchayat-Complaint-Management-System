<?php
// config/db_connect.php
declare(strict_types=1);

require_once __DIR__ . '/db.php'; // or place get_db_connection() inside here

// Expose the global $conn variable expected by all modules
$conn = get_db_connection();