<?php
/**
 * FILE: dashboard.php (Root Entry Point Forwarder)
 * PURPOSE: Forwards root requests for dashboard.php to citizen/citizen_dashboard.php
 */
header("Location: citizen/citizen_dashboard.php" . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : ''));
exit();
