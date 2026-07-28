<?php
/**
 * Gram Panchayat Complaint Management System (GPCMS)
 * Shared Session Authentication & Role Guard Middleware
 * handbook reference: includes/auth_check.php
 *
 * Canonical Session Keys:
 * - $_SESSION['user_id']
 * - $_SESSION['full_name']
 * - $_SESSION['role_id']
 * - $_SESSION['role_name'] ('admin', 'officer', 'citizen')
 * - $_SESSION['is_logged_in']
 * - $_SESSION['complaint_id']
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if the user is authenticated
 */
function requireLogin() {
    if (empty($_SESSION['is_logged_in']) || empty($_SESSION['user_id'])) {
        header("Location: ../login.php?error=unauthorized");
        exit();
    }
}

/**
 * Require specific role access
 * @param array|string $allowed_roles e.g. ['officer', 'admin'] or 'officer'
 */
function requireRole($allowed_roles) {
    requireLogin();
    
    if (is_string($allowed_roles)) {
        $allowed_roles = [$allowed_roles];
    }
    
    $user_role = $_SESSION['role_name'] ?? '';
    
    if (!in_array($user_role, $allowed_roles, true)) {
        header("Location: ../unauthorized.php");
        exit();
    }
}
?>
