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
        auth_redirect(auth_get_dashboard_path((string) ($_SESSION['role_name'] ?? '')));
    }
}

function auth_require_auth(): array
{
    auth_start_session();

    if (empty($_SESSION['is_logged_in']) || empty($_SESSION['user_id'])) {
        auth_redirect('login.php', 'Please sign in to continue.');
    }

    return [
        'user_id' => (int) $_SESSION['user_id'],
        'full_name' => (string) $_SESSION['full_name'],
        'role_id' => (int) $_SESSION['role_id'],
        'role_name' => (string) $_SESSION['role_name'],
    ];
}

function auth_get_dashboard_path(string $roleName): string
{
    $role = strtolower(trim($roleName));

    if ($role === 'citizen') {
        return 'citizen_dashboard.php';
    }

    if ($role === 'gram sevak') {
        return 'admin_dashboard.php';
    }

    if ($role === 'field officer') {
        return 'field_dashboard.php';
    }

    if ($role === 'super admin') {
        return 'super_admin_dashboard.php';
    }

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

function auth_get_user_by_login_id(string $loginId): ?array
{
    $conn = get_db_connection();
    $stmt = $conn->prepare(
        'SELECT u.user_id, u.full_name, u.login_id, u.password_hash, u.role_id, u.mobile_number, u.status, r.role_name
         FROM users u
         INNER JOIN roles r ON r.role_id = u.role_id
         WHERE u.login_id = ? LIMIT 1'
    );
    $stmt->bind_param('s', $loginId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    return $user ?: null;
}
