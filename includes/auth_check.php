<?php
/**
 * Gram Panchayat Complaint Management System (GPCMS)
 * Authentication & Role Checking Middleware
 * 
 * Canonical Session Keys:
 * - $_SESSION['user_id']
 * - $_SESSION['full_name']
 * - $_SESSION['role_id']
 * - $_SESSION['role_name']
 * - $_SESSION['is_logged_in']
 * - $_SESSION['complaint_id']
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Development / Demo Fallback Session Initialization (if direct access without prior auth)
if (!isset($_SESSION['is_logged_in'])) {
    $_SESSION['user_id'] = 101;
    $_SESSION['full_name'] = 'Rajesh Patil (Gram Sevak)';
    $_SESSION['role_id'] = 2;
    $_SESSION['role_name'] = 'Gram Sevak';
    $_SESSION['is_logged_in'] = true;
}

/**
 * Verify user authentication and authorize specific roles
 * 
 * @param array|int|string $allowed_roles Role IDs or Role Names allowed to access
 */
function check_role($allowed_roles = [2, 'Gram Sevak']) {
    if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
        header("Location: ../login.php");
        exit();
    }

    if (!is_array($allowed_roles)) {
        $allowed_roles = [$allowed_roles];
    }

    $user_role_id = $_SESSION['role_id'] ?? null;
    $user_role_name = $_SESSION['role_name'] ?? null;

    $has_access = in_array($user_role_id, $allowed_roles, true) || in_array($user_role_name, $allowed_roles, true);

    if (!$has_access) {
        http_response_code(403);
        echo "<h1>403 Forbidden</h1><p>Access Denied: You do not have permission to view this page.</p>";
        exit();
    }
}
