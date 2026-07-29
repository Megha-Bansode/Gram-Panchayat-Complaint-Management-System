<?php
/**
 * admin/admin_dashboard.php
 * GPCMS — Admin Dashboard Route wrapper
 * Admin-only access controlled. Follows handbook.
 */
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth_check.php';

// Access Control check
if (!isset($_SESSION['is_logged_in']) || !in_array($_SESSION['role_name'], ['Administrator', 'Super Admin', 'Gram Panchayat Admin'], true)) {
    header("Location: ../index.php");
    exit;
}

require_once __DIR__ . '/analytics_dashboard.php';
