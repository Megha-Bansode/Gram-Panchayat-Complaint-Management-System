<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';

function auth_start_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function auth_sanitize_input(mixed $value): string
{
    return strip_tags(trim((string) $value));
}

function auth_escape_output(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function auth_redirect(string $path, ?string $message = null, string $type = 'error'): void
{
    $separator = strpos($path, '?') === false ? '?' : '&';
    $key = $type === 'success' ? 'success' : 'error';

    if ($message !== null) {
        $path .= $separator . $key . '=' . rawurlencode($message);
    }

    header('Location: ' . $path);
    exit;
}

function auth_require_guest(): void
{
    auth_start_session();

    if (!empty($_SESSION['is_logged_in'])) {
        auth_redirect(auth_get_post_login_path((string) ($_SESSION['role_name'] ?? '')));
    }
}

function auth_require_auth(): array
{
    auth_start_session();

    if (empty($_SESSION['is_logged_in']) || empty($_SESSION['user_id'])) {
        auth_redirect('../includes/login.php', 'Please sign in to continue.');
    }

    // Generate CSRF token if not present (Handbook §10)
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return [
        'user_id' => (int) $_SESSION['user_id'],
        'full_name' => (string) $_SESSION['full_name'],
        'role_id' => (int) $_SESSION['role_id'],
        'role_name' => (string) $_SESSION['role_name'],
    ];
}

function auth_get_post_login_path(string $roleName): string
{
    $role = strtolower(trim($roleName));

    // Map known role name patterns to dashboard paths (relative to the includes/ folder)
    // Supports both pattern matching (field-officer module) and exact database role names (gram-officer module)
    
    // Admin roles
    if (str_contains($role, 'super') || str_contains($role, 'admin') || $role === 'admin' || $role === 'gram panchayat admin') {
        return '../admin/admin_dashboard.php';
    }

    // Gram sevak / panchayat roles
    if (str_contains($role, 'gram') || str_contains($role, 'panchayat') || $role === 'gram sevak') {
        return '../gramsevak/gramsevak_dashboard.php';
    }

    // Field officer roles
    if (str_contains($role, 'field') || str_contains($role, 'officer') || $role === 'officer' || $role === 'field officer') {
        return '../officer/field_dashboard.php';
    }

    // Citizen roles
    if (str_contains($role, 'citizen') || $role === 'citizen') {
        return '../citizen/citizen_dashboard.php';
    }

    // Fallback to login page
    return 'login.php';
}

function auth_set_user_session(array $user): void
{
    auth_start_session();

    $_SESSION['user_id'] = (int) $user['user_id'];
    $_SESSION['full_name'] = (string) $user['full_name'];
    $_SESSION['role_id'] = (int) $user['role_id'];
    $_SESSION['role_name'] = (string) $user['role_name'];
    $_SESSION['is_logged_in'] = true;

    session_regenerate_id(true);
}

function auth_logout(): void
{
    auth_start_session();

    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
    session_regenerate_id(true);
}

function auth_get_role_id_by_name(string $roleName): ?int
{
    $conn = get_db_connection();
    $stmt = $conn->prepare('SELECT role_id FROM roles WHERE LOWER(role_name) = LOWER(?) LIMIT 1');
    $stmt->bind_param('s', $roleName);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    return $row !== null ? (int) $row['role_id'] : null;
}

/**
 * Get user by login identifier (supports both email and login_id for compatibility)
 * @param string $loginId - Can be email (field-officer module) or login_id (gram-officer module)
 * @return array|null
 */
function auth_get_user_by_login_id(string $loginId): ?array
{
    $conn = get_db_connection();
    
    // Try login_id first (gram-officer module), then fall back to email (field-officer module)
    $stmt = $conn->prepare(
        'SELECT u.user_id, u.full_name, u.email, u.login_id, u.password, u.password_hash, u.role_id, u.phone, u.mobile_number, u.status, r.role_name
         FROM users u
         INNER JOIN roles r ON r.role_id = u.role_id
         WHERE u.login_id = ? OR u.email = ? LIMIT 1'
    );
    $stmt->bind_param('ss', $loginId, $loginId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    return $user ?: null;
}

/**
 * Check if current user has one of the allowed roles
 * @param array|int|string $allowedRoles - Array of allowed role_ids or role_names, or single value
 * @return bool
 */
function check_role(array|int|string $allowedRoles): bool
{
    auth_start_session();
    
    if (empty($_SESSION['is_logged_in']) || empty($_SESSION['role_id'])) {
        return false;
    }
    
    $userRoleId = (int) $_SESSION['role_id'];
    $userRoleName = strtolower((string) $_SESSION['role_name']);
    
    // Normalize allowed roles to array
    $allowed = is_array($allowedRoles) ? $allowedRoles : [$allowedRoles];
    
    foreach ($allowed as $role) {
        if (is_int($role)) {
            // Check by role_id
            if ($userRoleId === $role) {
                return true;
            }
        } else {
            // Check by role_name (case-insensitive)
            if (strtolower((string) $role) === $userRoleName) {
                return true;
            }
        }
    }
    
    return false;
}

/**
 * Require specific role, redirect if not authorized
 * @param array|int|string $allowedRoles
 * @param string $redirectPath
 * @param string $message
 */
function require_role(array|int|string $allowedRoles, string $redirectPath = '../includes/official_login.php', string $message = 'Unauthorized access.'): void
{
    if (!check_role($allowedRoles)) {
        auth_redirect($redirectPath, $message);
    }
}