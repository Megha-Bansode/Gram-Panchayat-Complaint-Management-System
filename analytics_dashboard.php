<?php
/**
 * analytics_dashboard.php (Root entry point)
 * Redirects to admin/analytics_dashboard.php propagating parameters.
 */
$query = isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] !== '' ? '?' . $_SERVER['QUERY_STRING'] : '';
header("Location: admin/analytics_dashboard.php" . $query);
exit;
