<?php
/**
 * FILE: includes/auth_check.php
 * PURPOSE: Centralised authentication guard for the Citizen Module.
 *          Include at the top of every protected citizen page.
 *          Starts the session and redirects unauthenticated/
 *          non-Citizen users to login.php.
 * CONTRACT: Citizen_Database_Contract.txt (line 19)
 * USAGE:    require_once '../includes/auth_check.php';
 *           (included from citizen/ directory)
 */

// ── Start session if not already active ──────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── Redirect path (relative from citizen/ pages) ─────────
$_auth_login_url = '../includes/login.php';

// ── Check 1: Must be logged in ────────────────────────────
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header('Location: ' . $_auth_login_url);
    exit();
}

// ── Check 2: Must be a Citizen ───────────────────────────
if (!isset($_SESSION['role_name']) || $_SESSION['role_name'] !== 'Citizen') {
    // Non-citizens (Admin/Officer) are redirected back to login
    header('Location: ' . $_auth_login_url);
    exit();
}

// ── Check 3: Session user_id must be a valid integer ─────
if (empty($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
    session_destroy();
    header('Location: ' . $_auth_login_url);
    exit();
}

// Clean up internal variable
unset($_auth_login_url);

// ── CSRF Token (generated once per session) ───────────────
// Handbook §10 — CSRF protection required on all POST forms.
// Read with $_SESSION['csrf_token']; verify with hash_equals().
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
